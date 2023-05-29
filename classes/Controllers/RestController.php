<?php

/**
 * This file contains the extended rest controller
 */

namespace QTEREST\Controllers;

use GuzzleHttp\Client;
use QTEREST\Uploads\FileHandler;
use QTEREST\Utils\Options;
use QTEREST\Utils\Recaptcha;
use QTEREST\Utils\SanitizeParams;
use function QTEREST\Helpers\is_recaptcha_enabled;
use function QTEREST\Helpers\validate_email;
use function QTEREST\Helpers\get_client_ip;
use function QTEREST\Helpers\mailchimp_api_key_is_valid;
use function QTEREST\Helpers\maybe_get_notification_email;
use function QTEREST\Helpers\get_translated_string;

use DrewM\MailChimp\MailChimp;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RestController extends \WP_REST_Controller {


	// The namespace and version for the REST SERVER
	public $qterest_namespace = 'qte/v';
	public $qterest_version   = '1';

	public function register_routes() {

		$namespace = $this->qterest_namespace . $this->qterest_version;

		register_rest_route(
			$namespace,
			'/contact',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'handle_contact' ),
					'permission_callback' => '__return_true',
				),
			)
		);

		register_rest_route(
			$namespace,
			'/mailchimp/add-subscriber',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'handle_mailchimp_add_subscriber' ),
					'permission_callback' => '__return_true',
				),
			)
		);
	}

	// Register our REST Server
	public function hook_rest_server() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function handle_contact( \WP_REST_Request $request ) {

		$messages = array(
			'name_empty'        => get_translated_string( 'Name cannot be empty!' ),
			'email_empty'       => get_translated_string( 'Email cannot be empty!' ),
			'email_invalid'     => get_translated_string( 'Email is not valid!' ),
			'invalid_recaptcha' => get_translated_string( 'Invalid reCAPTCHA!' ),
			'failed'            => get_translated_string( 'Something went wrong. Please try again later!' ),
			'success'           => get_translated_string( 'Thank you! We will contact you as fast as we can!' ),
			'mail_subject'      => get_translated_string( 'New contact request!' ),
			'mail_body'         => get_translated_string( '<p>New contact request is available. Click the link below to access it</p><br>{LINK}' ),
			'mail_to'           => maybe_get_notification_email(),
		);

		$params = $request->get_content_type()['subtype'] === 'json' ? $request->get_json_params() : $request->get_body_params(); // Get contact request params
		$params = SanitizeParams::sanitizeParams( $params );

		/**
		 * Applys a filter to change the messages from for example a theme
		 */
		$messages = apply_filters( 'qterest_contact_messages', $messages, $params );

		/**
		 * Get options for qterest
		 */
		$options = get_option( 'qterest_options' );

		/**
		 * Maybe validate reCaptcha
		 */
		if ( is_recaptcha_enabled() && isset( $params['g-recaptcha-response'] ) ) {
			if ( ! Recaptcha::make( $options[ Options::RECAPTCHA_SECRET_KEY ], new Client() )->validateResponse( $params['g-recaptcha-response'] ) ) {
				return array(
					'success'   => false,
					'error_msg' => $messages['invalid_recaptcha'],
				);
			}
		}

		/**
		 * Checks that email isn't empty
		 */
		if ( empty( $params['email'] ) ) {
			return array(
				'success'   => false,
				'error_msg' => $messages['email_empty'],
			);
		}

		/**
		 * Checks if email is valid
		 */
		if ( ! validate_email( $params['email'] ) ) {
			return array(
				'success'   => false,
				'error_msg' => $messages['email_invalid'],
			);
		}

		$post_id = wp_insert_post(
			array(
				'post_title'          => $params['email'] . ' - ' . date( 'Y-m-d H:m:s' ),
				'post_type'           => 'contact_requests',
				'post_status'         => 'publish',
				'exclude_from_searcg' => true,
				'show_in_rest'        => false,
				'meta_input'          => array(
					'request_content' => serialize( $params ),
				),
			)
		);

		/**
		 * Checks if request got inserted
		 */
		if ( is_wp_error( $post_id ) ) {
			return array(
				'success'   => false,
				'error_msg' => $messages['failed'],
			);
		}

		if ( $_FILES ) {
			$attachment_ids = FileHandler::make( $post_id )->handleAllFiles();

			$errors = array_filter(
				$attachment_ids,
				function ( $attachment_id ) {
					return is_wp_error( $attachment_id );
				}
			);

			if ( $errors ) {
				return array(
					'success'    => false,
					'error_msg'  => $messages['failed'],
					'error_data' => array_map(
						function( $error ) {
							/** @var \WP_Error $error */
							return $error->get_error_messages();
						},
						$errors
					),
				);
			}
		}

		/**
		 * This hook can be used to change the post that was just inserted
		 */
		do_action( 'qterest_after_post_insertion', $post_id, $params );

		/**
		 * Gets and inserts the clients ip address
		 */
		update_post_meta( $post_id, 'request_ip_address', get_client_ip() );

		$link = site_url( "wp-admin/post.php?post=$post_id&action=edit" );

		$to          = apply_filters( 'qterest_contact_mail_to', $messages['mail_to'], $params, $post_id );
		$subject     = apply_filters( 'qterest_contact_mail_subject', $messages['mail_subject'], $params, $post_id );
		$body        = apply_filters( 'qterest_contact_mail_body', $messages['mail_body'], $params );
		$body        = \preg_replace( '#{LINK}#', "<a href=\"$link\">$link</a>", $body );
		$headers     = apply_filters( 'qterest_contact_mail_headers', array( 'Content-Type: text/html; charset=UTF-8' ), $params, $post_id );
		$attachments = apply_filters( 'qterest_contact_mail_attachments', array(), $attachment_ids ?? array(), $post_id );

		/**
		 * This hook can be used to manipulate the mail
		 */
		do_action( 'qterest_contact_before_send_mail', $to, $subject, $body, $headers, $attachments );

		if ( $to != null ) {
			wp_mail( $to, $subject, $body, $headers, $attachments );
		}

		// Remove attachments after mail
		if (apply_filters('qterest_contact_remove_attachments_after_request', false) == true) {
			foreach ($attachment_ids as $attachment_id) {
				wp_delete_attachment($attachment_id, true);
			}
		}

		return array(
			'success'     => true,
			'success_msg' => $messages['success'],
		);
	}

	public function handle_mailchimp_add_subscriber( \WP_REST_Request $request ) {

		$messages = array(
			'invalid_api_key'   => get_translated_string( 'Invalid MailChimp API key!' ),
			'email_empty'       => get_translated_string( 'Email cannot be empty!' ),
			'email_invalid'     => get_translated_string( 'Email is not valid!' ),
			'invalid_recaptcha' => get_translated_string( 'Invalid reCAPTCHA!' ),
			'failed'            => get_translated_string( 'Something went wrong. Please try again later!' ),
			'success'           => get_translated_string( 'Thank you for subscribing to our newsletter!' ),
		);

		/**
		 * Applys a filter to change the messages from for example a theme
		 */
		$messages = apply_filters( 'qterest_mailchimp_messages', $messages );

		$params = $request->get_content_type()['subtype'] === 'json' ? $request->get_json_params() : $request->get_body_params(); // Get mailchimp request params

		/**
		 * Get options for qterest
		 */
		$options = get_option( 'qterest_options' );

		if ( is_recaptcha_enabled() && isset( $params['g-recaptcha-response'] ) ) {
			if ( ! Recaptcha::make( $options[ Options::RECAPTCHA_SECRET_KEY ], new Client() )->validateResponse( $params['g-recaptcha-response'] ) ) {
				return array(
					'success'   => false,
					'error_msg' => $messages['invalid_recaptcha'],
				);
			}
		}

		/**
		 *  Check if email is not empty
		 */
		if ( ! isset( $params['email'] ) && empty( $params['email'] ) ) {
			return array(
				'success'   => false,
				'error_msg' => $messages['email_empty'],
			);
		}

		/**
		 * Check if email is valid
		 */
		if ( ! validate_email( $params['email'] ) ) {
			return array(
				'success'   => false,
				'error_msg' => $messages['email_invalid'],
			);
		}

		/**
		 * Check if MailChimp API key is valid
		 */
		if ( ! mailchimp_api_key_is_valid() ) {
			return array(
				'success'   => false,
				'error_msg' => $messages['invalid_api_key'],
			);
		}

		/**
		 * Try catch in case it throws exceptions
		 */
		try {
			$MailChimp = new MailChimp( $options['qterest_field_mailchimp_api_key'] );
		} catch ( \Exception $e ) {
			return array(
				'success'   => false,
				'error_msg' => $e->getMessage(),
			);
		}

		/**
		 * Add subscriber to MailChimp list
		 */
		$repsonse = $MailChimp->post(
			"/lists/$options[qterest_field_mailchimp_mail_list]/members",
			array(
				'email_address' => $params['email'],
				'status'        => 'subscribed',
			)
		);

		/**
		 * Check if already subscribed and if subscribed make sure that the status is subscribed
		 */
		if ( $member_exists = $repsonse['title'] == 'Member Exists' ) {
			$repsonse = $MailChimp->put(
				"/lists/$options[qterest_field_mailchimp_mail_list]/members/" . $MailChimp->subscriberHash( $params['email'] ),
				array(
					'status' => 'subscribed',
				)
			);
		}

		/**
		 * Check if user added or updated
		 */
		if ( ! isset( $repsonse['id'] ) ) {
			return array(
				'success'   => false,
				'error_msg' => $messages['failed'],
			);
		}

		/**
		 * Hook to do stuff when a new subscriber is added.
		 */
		do_action( 'qterest_mailchimp_subscriber_added', $params['email'], $member_exists );

		return array(
			'success'     => true,
			'success_msg' => $messages['success'],
		);
	}
}
