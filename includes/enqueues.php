<?php

/**
 * This file contains all the enqueues for qterest.
 */

namespace QTEREST\Enqueues;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Never worry about cache again!
 */
function load_scripts($hook) {
     
    wp_enqueue_script( 'qterest-form', QTEREST_PLUGIN_DIR . '/assets/js/form.js', array('jquery', 'wp-api'),"1.0.0", true );
    wp_enqueue_style( 'qterest-form', QTEREST_PLUGIN_DIR . '/assets/css/form.css', false, '1.0.0' );
 
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\load_scripts');