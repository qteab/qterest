<?php
/**
 * This file contains all the functions to setup the settings page
 */

namespace QTEREST\SettingsPage;

use QTEREST\Vendor\DrewM\MailChimp\MailChimp;
use QTEREST\Utils\Options;
use function QTEREST\Helpers\mailchimp_api_key_is_valid;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register settings page if MailChimp is activated for this site
 */
function settings_init() {
	/**
	   * Register a new setting for qterest
	   */
	register_setting( 'qterest', 'qterest_options' );
}

function settings_mailchimp() {
	/**
	 * Register a settings section for mailchimp
	 */
	add_settings_section(
		'qterest_mailchimp_section',
		__( 'MailChimp', 'qterest' ),
		__NAMESPACE__ . '\\qterest_mailchimp_section_cb',
		'qterest'
	);

	/**
	 * Register MailChimp API key field
	 */
	add_settings_field(
		'qterest_field_mailchimp_api_key',
		__( 'MailChimp API Key', 'qterest' ),
		__NAMESPACE__ . '\\qterest_field_mailchimp_api_key_cb',
		'qterest',
		'qterest_mailchimp_section',
		array(
			'label_for'           => 'qterest_field_mailchimp_api_key',
			'class'               => 'qterest_row',
			'qterest_custom_data' => 'custom',
		)
	);

	if ( mailchimp_api_key_is_valid() ) {

		/**
		 * If api key is valid register MailChimp list field
		 */
		add_settings_field(
			'qterest_field_mail_list',
			__( 'MailChimp Lista', 'qterest' ),
			__NAMESPACE__ . '\\qterest_field_mailchimp_mail_list_cb',
			'qterest',
			'qterest_mailchimp_section',
			array(
				'label_for'           => 'qterest_field_mailchimp_mail_list',
				'class'               => 'qterest_row',
				'qterest_custom_data' => 'custom',
			)
		);
	}
}

function settings_contact() {
	/**
	 * Register a settings section for contact
	 */
	add_settings_section(
		'qterest_contact_section',
		__( 'Contact forms', 'qterest' ),
		__NAMESPACE__ . '\\qterest_contact_section_cb',
		'qterest'
	);

	/**
	 * Register Contact Notification email field
	 */
	add_settings_field(
		'qterest_field_contact_notification_email',
		__( 'Notification email', 'qterest' ),
		__NAMESPACE__ . '\\qterest_field_contact_notification_email_cb',
		'qterest',
		'qterest_contact_section',
		array(
			'label_for' => 'qterest_field_contact_notification_email',
		)
	);
}

function settings_recaptcha() {
	add_settings_section(
		'qterest_recaptcha_section',
		__( 'reCaptcha', 'qterest' ),
		__NAMESPACE__ . '\\qterest_recaptcha_section_cb',
		'qterest'
	);

	add_settings_field(
		Options::RECAPTCHA_SITE_KEY,
		__( 'Site Key', 'qterest' ),
		__NAMESPACE__ . '\\qterest_field_recaptcha_site_key_cb',
		'qterest',
		'qterest_recaptcha_section',
		array(
			'label_for' => 'qterest_field_recaptcha_site_key',
		)
	);

	add_settings_field(
		Options::RECAPTCHA_SECRET_KEY,
		__( 'Secret Key', 'qterest' ),
		__NAMESPACE__ . '\\qterest_field_recaptcha_secret_key_cb',
		'qterest',
		'qterest_recaptcha_section',
		array(
			'label_for' => 'qterest_field_recaptcha_secret_key',
		)
	);
}

/**
 * Global settings init
 */
add_action( 'admin_init', __NAMESPACE__ . '\\settings_init' );

/**
 * Register mailchimp settings
 */
	add_action( 'admin_init', __NAMESPACE__ . '\\settings_mailchimp' );


/**
 * Register contact settings
 */

	add_action( 'admin_init', __NAMESPACE__ . '\\settings_contact' );

/**
 * Register reCaptcha settings
 */
add_action( 'admin_init', __NAMESPACE__ . '\\settings_recaptcha' );

/**
 * Callback for title to the mailchimp section
 */
function qterest_mailchimp_section_cb( $args ) {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Settings for MailChimp', 'qterest' ); ?></p>
	<?php
}

/**
 * Callback for title to the contact section
 */
function qterest_contact_section_cb( $args ) {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Settings for Contact forms', 'qterest' ); ?></p>
	<?php
}

/**
 * Callback for title to the reCaptcha section
 */
function qterest_recaptcha_section_cb( $args ) {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Settings for reCaptcha', 'qterest' ); ?></p>
	<?php
}


/**
 * Callback for MailChimp API key field
 */
function qterest_field_mailchimp_api_key_cb( $args ) {
	$options = get_option( 'qterest_options' );
	?>
	<input type="text" value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : null; ?>"
		   id="<?php echo esc_attr( $args['label_for'] ); ?>"
		   data-custom="<?php echo esc_attr( $args['qterest_custom_data'] ); ?>"
		   name="qterest_options[<?php echo esc_attr( $args['label_for'] ); ?>]" size="50">
	<p class="description"><?php esc_html_e( 'Enter your Mailchimp API key ', 'qterest' ); ?></p>
	<?php
}

/**
 * Callback for MailChimp mail list field
 */
function qterest_field_mailchimp_mail_list_cb( $args ) {
	$options = get_option( 'qterest_options' );

	if ( mailchimp_api_key_is_valid() ) :
		?>
		<select id="<?php echo esc_attr( $args['label_for'] ); ?>"
				data-custom="<?php echo esc_attr( $args['qterest_custom_data'] ); ?>"
				name="qterest_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
			<?php

			$MailChimp = new MailChimp( $options['qterest_field_mailchimp_api_key'] );

			$response = $MailChimp->get( 'lists' );

			foreach ( $response['lists'] as $list ) {
				?>
				<option value="<?php echo $list['id']; ?>" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], $list['id'], false ) ) : ( '' ); ?>>
					<?php esc_html_e( $list['name'], 'do_sub' ); ?>
				</option>
			<?php } ?>
		</select>
		<p class="description">
			<?php esc_html_e( 'Choose which list you want to add new subscribers to', 'do_sub' ); ?>
		</p>
	<?php else : ?>
		<p><b>
				<?php esc_html_e( 'Please enter a valid api key', 'do_sub' ); ?>
			</b></p>
		<?php
	endif;
}

/**
 * Callback for Contact notification email field
 */
function qterest_field_contact_notification_email_cb( $args ) {
	$options = get_option( 'qterest_options' );
	?>
	<input type="email" value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : null; ?>"
		   id="<?php echo esc_attr( $args['label_for'] ); ?>"
		   name="qterest_options[<?php echo esc_attr( $args['label_for'] ); ?>]" size="50">
	<p class="description"><?php esc_html_e( 'Enter a valid email that you want to recive notifications to when a new contact request is recived', 'qterest' ); ?></p>
	<?php
}

/**
 * Callback for reCaptcha Site key field
 */
function qterest_field_recaptcha_site_key_cb( $args ) {
	$options = get_option( 'qterest_options' );
	?>
	<input type="text" value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : null; ?>"
		   id="<?php echo esc_attr( $args['label_for'] ); ?>"
		   data-custom="<?php echo esc_attr( $args['qterest_custom_data'] ); ?>"
		   name="qterest_options[<?php echo esc_attr( $args['label_for'] ); ?>]" size="50">
	<p class="description"><?php esc_html_e( 'Enter your site key ', 'qterest' ); ?></p>
	<?php
}

/**
 * Callback for reCaptcha Secret key field
 */
function qterest_field_recaptcha_secret_key_cb( $args ) {
	$options = get_option( 'qterest_options' );
	?>
	<input type="text" value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : null; ?>"
		   id="<?php echo esc_attr( $args['label_for'] ); ?>"
		   data-custom="<?php echo esc_attr( $args['qterest_custom_data'] ); ?>"
		   name="qterest_options[<?php echo esc_attr( $args['label_for'] ); ?>]" size="50">
	<p class="description"><?php esc_html_e( 'Enter your secret key ', 'qterest' ); ?></p>
	<?php
}

/**
 * Callback to register settings page
 */
function qterest_options_page() {
	add_menu_page(
		'Settings',
		'QTE Rest',
		'manage_options',
		'qterest_settings',
		__NAMESPACE__ . '\\qterest_options_page_html'
	);
}

/**
 * Register settings page
 */
add_action( 'admin_menu', __NAMESPACE__ . '\\qterest_options_page' );

/**
 * Page callback
 */
function qterest_options_page_html() {
	/**
	 * Check user capabilities
	 */
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_GET['settings-updated'] ) ) {
		add_settings_error( 'qterest_messages', 'qterest_message', __( 'Settings Saved', 'qterest' ), 'updated' );
	}

	settings_errors( 'qterest_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'qterest' );

			do_settings_sections( 'qterest' );

			submit_button( __( 'Save Settings', 'qterest' ) );
			?>
		</form>
	</div>
	<?php
}
