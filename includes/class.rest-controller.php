<?php

/**
 * This file contains the extended rest controller
 */

namespace QTEREST\REST_Controller;

use QTEREST\Helpers;
use function QTEREST\Helpers\validate_email;
use function QTEREST\Helpers\get_client_ip;
use function QTEREST\Helpers\mailchimp_api_key_is_valid;

use DrewM\MailChimp\MailChimp;

if (!defined('ABSPATH')) {
    exit;
}

class REST_Controller extends \WP_REST_Controller
{
 
    //The namespace and version for the REST SERVER
    var $qterest_namespace = 'qte/v';
    var $qterest_version = '1';

    public function register_routes()
    {
        global $qterest_settings; 

        $namespace = $this->qterest_namespace . $this->qterest_version;

        if($qterest_settings['search']) {
            $base = 'search';
            register_rest_route($namespace, '/' . $base, array(
                array(
                    'methods' => 'GET',
                    'callback' => array($this, 'handle_search'),
                )
            ));
        }

        if($qterest_settings['contact']) {
            $base = 'contact';
            register_rest_route($namespace, '/' . $base, array(
                array(
                    'methods' => 'POST',
                    'callback' => array($this, 'handle_contact'),
                )
            ));
        }

        if($qterest_settings['mailchimp']) {
            $base = 'mailchimp/add-subscriber';
            register_rest_route($namespace, '/' . $base, array(
                array(
                    'methods' => 'POST',
                    'callback' => array($this, 'handle_mailchimp_add_subscriber'),
                )
            ));
        }
    }

    // Register our REST Server
    public function hook_rest_server()
    {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function handle_search(\WP_REST_Request $request)
    {
        global $qterest_settings; 

        if(!$qterest_settings['search']) {
            return array('success' => false, 'error_msg' => __("Search is not enabled for this site", 'qterest'));
        }

        $params = $request->get_params(); //Get search params


        $args = array(
            'post_type' => 'post',
            'posts_per_page' => -1,
            'order_by' => 'date',
            'order' => 'DESC',
        );

        /**
         * This hook can be used to change the $args array to change the query.
         * The $params variable can be used to access query variables.
         */
        $args = apply_filters('qterest_before_query', $args, $params);

        $the_query = new \WP_Query($args);

        ob_start();

        /**
         * This hook is used to add code before the post loop
         */
        do_action('qterest_before_search_content');

        while ($the_query->have_posts()) {
            $the_query->the_post();
            
            /**
             * This hook is used to add code for the single posts
             */
            do_action('qterest_search_content');

        }
        wp_reset_query();

        do_action('qterest_after_search_content');

        return array('success' => true, 'data' => ob_get_clean() , "total_items" => $the_query->found_posts);
    }

    public function handle_contact(\WP_REST_Request $request)
    {
        global $qterest_settings; 

        if(!$qterest_settings['contact']) {
            return array('success' => false, 'error_msg' => __("Contact is not enabled for this site", 'qterest'));
        }

        $messages = array(
            'name_empty' => __("Name cannot be empty!", 'qterest'),
            'email_empty' => __("Email cannot be empty!", 'qterest'),
            'email_invalid' => __("Email is not valid!", 'qterest'),
            'failed' => __("Something went wrong. Please try again later!", 'qterest'),
            'success' => __("Thank you! We will contact you as fast as we can!", 'qterest'),
            'mail_subject' => __("New contact request!", 'qterest'),
            'mail_body' => __("<p>New contact request is available. Click the link below to acces it</p><br>{LINK}", 'qterest'),
            'mail_to' => NULL
        );

        /**
         * Applys a filter to change the messages from for example a theme
         */
        $messages = apply_filters('qterest_contact_messages', $messages);


        $params = $request->get_params(); //Get contact request params

        /**
         * Checks the name isn't empty
         */
        if(empty($params['name'])) {

            return array('success' => false, 'error_msg' => $messages['name_empty']);

        }

        /**
         * Checks that email isn't empty
         */
        if(empty($params['email'])) {

            return array('success' => false, 'error_msg' => $messages['email_empty']);
            
        }

        /**
         * Checks if email is valid
         */
        if(!validate_email($params['email'])) {
            
            return array('success' => false, 'error_msg' => $messages['email_invalid']);

        }

        $post_id = wp_insert_post(array(
            'post_title' => $params['name'] . " - " . date("Y-m-d H:m:s"),
            'post_type' => 'contact_requests',
            'post_status' => 'publish',
            'meta_input' => array(
                'request_content' => serialize($params),
            )
        ));

        /**
         * Checks if request got inserted
         */
        if(is_wp_error($post_id)){

            return array('success' => false, 'error_msg' => $messages['failed']);

        }

        /**
         * This hook can be used to change the post tha was just inserted
         */
        do_action('qterest_after_post_insertion', $post_id, $params);

        /**
         * Gets and inserts the clients ip address
         */
        update_post_meta($post_id, 'request_ip_address', get_client_ip());


        $link = site_url("wp-admin/post.php?post=$post_id&action=edit");

        $to = $messages['mail_to'];
        $subject = $messages['mail_subject'];
        $body = $messages['mail_body'];
        $body = \preg_replace('#{LINK}#', "<a href=\"$link\">$link</a>", $body); 
        $headers = array('Content-Type: text/html; charset=UTF-8');

        /**
         * This hook can be used to manipulate the mail
         */
        do_action('qterest_contact_before_send_mail', $to, $subject, $body, $headers);
        

        if($messages['mail_to'] != NULL){
            wp_mail( $to, $subject, $body, $headers );
        }
        

        return array('success' => true, 'success_msg' => $messages['success']);
    }

    public function handle_mailchimp_add_subscriber(\WP_REST_Request $request) {
        global $qterest_settings; 

        if(!$qterest_settings['mailchimp']) {
            return array('success' => false, 'error_msg' => __("Mailchimp is not enabled for this site", 'qterest'));
        }

        $messages = array(
            'invalid_api_key' => __("Invalid MailChimp API key!", 'qterest'),
            'email_empty' => __("Email cannot be empty!", 'qterest'),
            'email_invalid' => __("Email is not valid!", 'qterest'),
            'failed' => __("Something went wrong. Please try again later!", 'qterest'),
            'success' => __("Thank you for subscribing to our newsletter!", 'qterest'),
        );


        /**
         * Applys a filter to change the messages from for example a theme
         */
        $messages = apply_filters('qterest_mailchimp_messages', $messages);

        $params = $request->get_params(); //Get mailchimp request params

        /**
         *  Check if email is not empty
         */
        if(!isset($params['email']) && empty($params['email'])){
            return array('success' => false, 'error_msg' => $messages['email_empty']);
        }

        /**
         * Check if email is valid
         */
        if(!validate_email($params['email'])){
            return array('success' => false, 'error_msg' => $messages['email_invalid']);
        }

        /**
         * Check if MailChimp API key is valid
         */
        if(!mailchimp_api_key_is_valid()){
            return array('success' => false, 'error_msg' => $messages['invalid_api_key']);
        }

        /**
         * Get options for qterest
         */
        $options = get_option('qterest_options');


        /**
         * Try catch in case it throws exceptions
         */
        try {
            $MailChimp = new MailChimp($options['qterest_field_mailchimp_api_key']);
        } catch (\Exception $e){
            return array('succes' => false, 'error_msg' => $e->getMessage());
        }

        /**
         * Add subscriber to MailChimp list
         */
        $repsonse = $MailChimp->post("/lists/$options[qterest_field_mailchimp_mail_list]/members", array(
            'email_address' => $params['email'],
            'status' => 'subscribed',
        ));

        /**
         * Check if already subscribed and if subscribed make sure that the status is subscribed
         */
        if($repsonse['title'] == "Member Exists"){
            $repsonse = $MailChimp->put("/lists/$options[qterest_field_mailchimp_mail_list]/members/" . $MailChimp->subscriberHash($params['email']), array(
                'status' => 'subscribed',
            ));
        }

        /**
         * Check if user added or updated
         */
        if(!isset($repsonse['id'])){
            return array('success' => false, 'error_msg' => $messages['failed']);
        }

        return array('success' => true, 'success_msg' => $messages['success'], 'debug' => $repsonse);
        
    }
}