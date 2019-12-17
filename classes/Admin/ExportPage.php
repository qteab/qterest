<?php


namespace QTEREST\Admin;

use QTEREST\Export\Exporter;

class ExportPage {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'registerPage' ) );
	}

	public function registerPage() {

		$hookname = add_submenu_page(
			'edit.php?post_type=contact_requests',
			__( 'Export contact requests', 'qterest' ),
			__( 'Export', 'qterest' ),
			'manage_options',
			'qterest_cr_export',
			array( $this, 'renderPage' )
		);

		add_action( 'load-' . $hookname, array( $this, 'pageLoad' ) );

	}

	public function renderPage() {
		require_once __DIR__ . '/../../assets/pages/export-page.php';
	}

	public function pageLoad() {
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$this->handlePost();
		}
	}

	private function handlePost() {
		$exporter = new Exporter();
		$exporter->export();
	}
}
