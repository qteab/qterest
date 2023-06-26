<?php
/*
 * Plugin Name: QTE Rest
 * Description: QTE Rest adds new endpoints for the WordPress API
 * Version: 1.6.4
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

$restController = new \QTEREST\Controllers\RestController();
$restController->hook_rest_server();

$gatsbySource = new \QTEREST\Utils\GatsbySource();

new \QTEREST\Admin\ExportPage();

\QTEREST\Uploads\FileHandler::registerHooks();

// Setting up update
$qterestUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/qteab/qterest/',
	__FILE__,
	'qterest'
);
$qterestUpdateChecker->getVcsApi()->enableReleaseAssets();
