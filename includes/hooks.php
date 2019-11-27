<?php

/**
 * This file contains hooks for qterest
 */

namespace QTEREST\Hooks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This function loads the textdomain
 */
function plugin_load_plugin_textdomain() {
	load_plugin_textdomain( 'qterest', false, 'qterest/languages/' );
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\plugin_load_plugin_textdomain' );
