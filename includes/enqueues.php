<?php

/**
 * This file contains all the enqueues for qterest.
 */

use function QTEREST\Helpers\is_recaptcha_enabled;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load contact scripts
 */

function qterest_enqueues() {
	wp_enqueue_script( 'qterest-form', QTEREST_PLUGIN_DIR . '/assets/js/form.js', array( 'jquery', 'wp-api' ), '1.2.0', true );
	wp_enqueue_script( 'qterest-mailchimp', QTEREST_PLUGIN_DIR . '/assets/js/mailchimp.js', array( 'jquery', 'wp-api' ), '1.1.0', true );

	if ( is_recaptcha_enabled() ) {
		wp_enqueue_script( 'qterest-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), null, false );
	}

	wp_enqueue_style( 'qterest-form', QTEREST_PLUGIN_DIR . '/assets/css/form.css', false, '1.1.0' );
	wp_enqueue_style( 'qterest-general', QTEREST_PLUGIN_DIR . '/assets/css/general.css', false, '1.0.0' );
}
	add_action( 'wp_enqueue_scripts', 'qterest_enqueues' );

