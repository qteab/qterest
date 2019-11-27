<?php

/**
 * This file loads all the other files
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once QTEREST_PLUGIN_PATH . 'settings.php';

require_once QTEREST_PLUGIN_PATH . 'includes/hooks.php';
require_once QTEREST_PLUGIN_PATH . 'includes/enqueues.php';
require_once QTEREST_PLUGIN_PATH . 'includes/cpts.php';
require_once QTEREST_PLUGIN_PATH . 'includes/helpers.php';
require_once QTEREST_PLUGIN_PATH . 'includes/custom-edit-page.php';
require_once QTEREST_PLUGIN_PATH . 'includes/settings-page.php';
require_once QTEREST_PLUGIN_PATH . 'includes/form-functions.php';
require_once QTEREST_PLUGIN_PATH . 'includes/global-functions.php';

require_once QTEREST_PLUGIN_PATH . 'includes/shortcodes.php';

require_once QTEREST_PLUGIN_PATH . 'includes/class.rest-controller.php';
