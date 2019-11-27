<?php
/*
 * Plugin Name: QTE Rest
 * Description: QTE Rest adds new endpoints for the Wordpress API
 * Version: 1.2.0
 * Author: QTE Development AB
 * Author URI: https://getqte.se/
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('QTEREST_PLUGIN_DIR', plugin_dir_url(__FILE__));
define('QTEREST_PLUGIN_PATH', plugin_dir_path(__FILE__));

require_once QTEREST_PLUGIN_PATH . 'vendor/autoload.php';
require_once QTEREST_PLUGIN_PATH . 'includes/load.php';

$qterest_controller = new QTEREST\REST_Controller\REST_Controller;
$qterest_controller->hook_rest_server();

// Setting up update
$qterestUpdateChecker = Puc_v4p8_Factory::buildUpdateChecker(
    'https://github.com/qteab/qterest/',
    __FILE__,
    'qterest'
);
