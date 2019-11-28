<?php

/**
 * This file contains all the enqueues for qterest.
 */

namespace QTEREST\Enqueues;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $qterest_settings;

/**
 * Load contact scripts
 */
if ( $qterest_settings['contact'] ) {
	function load_contact_scripts( $hook ) {
		wp_enqueue_script( 'qterest-form', QTEREST_PLUGIN_DIR . '/assets/js/form.js', array( 'jquery', 'wp-api' ), '1.1.0', true );
	}
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\load_contact_scripts' );
}

if ( $qterest_settings['mailchimp'] ) {
	function load_mailchimp_scripts( $hook ) {
		wp_enqueue_script( 'qterest-mailchimp', QTEREST_PLUGIN_DIR . '/assets/js/mailchimp.js', array( 'jquery', 'wp-api' ), '1.1.0', true );
	}
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\load_mailchimp_scripts' );
}

if( $qterest_settings['mailchimp'] || $qterest_settings['contact'] ) {
    function load_shared_scripts( $hook ) {
        wp_enqueue_style( 'qterest-form', QTEREST_PLUGIN_DIR . '/assets/css/form.css', false, '1.1.0' );
    }
    add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\load_shared_scripts' );
}

function load_global_scripts( $hook ) {
	wp_enqueue_style( 'qterest-general', QTEREST_PLUGIN_DIR . '/assets/css/general.css', false, '1.0.0' );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\load_global_scripts' );
