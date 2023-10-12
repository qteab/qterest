<?php


namespace QTEREST\Uploads;

class FileHandler {

	const UPLOADS_DIR = 'qte-rest';

	protected $postId;

	public function __construct( int $postId ) {
		$this->postId = $postId;

		// Make sure the required functions is loaded
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		static::setupUploadsDir();

		add_filter( 'upload_dir', array( $this, 'changeUploadsDir' ) );
	}

	public static function registerHooks() {
		add_action( 'wp_ajax_download_qte_rest_file', array( static::class, 'downloadFile' ) );

		add_filter( 'ajax_query_attachments_args', array( static::class, 'appendAjaxQueryAttachmentArgs' ) );
	}

	public static function make( int $postId ): self {
		return new static( $postId );
	}

	public function handleAllFiles(): array {
		$attachmentIds = array();

		foreach ( $_FILES as $fileId => $file ) {
			if ( $file['size'] ) {
				$attachmentIds[ $fileId ] = $this->handleFile( $fileId );
			}
		}

		return $attachmentIds;
	}

	public function handleFile( string $fileId ) {
		return media_handle_upload(
			$fileId,
			$this->postId,
			array(
				'meta_query' => array(
					'qte_rest_file' => true,
				),
			)
		);
	}

	public function handleFileTemp(string $fileId) {
		$file = wp_handle_upload($_FILES[$fileId], array('test_form' => FALSE));
		return $file['file'];
	}

	public function handleAllFilesTemp(): array {
		$attachmentUrls = array();

		foreach ($_FILES as $fileId => $file) {
			if ($file['size']) {
				$attachmentUrls[$fileId] = $this->handleFileTemp($fileId);
			}
		}

		return $attachmentUrls;
	}

	public static function appendAjaxQueryAttachmentArgs( $query ) {
		$query['meta_query'] = array(
			array(
				'key'     => 'qte_rest_file',
				'compare' => 'NOT EXISTS',
			),
		);

		return $query;
	}

	public static function changeUploadsDir( $upload ) {
		$upload['subdir'] = '/' . static::UPLOADS_DIR . $upload['subdir'];
		$upload['path']   = $upload['basedir'] . $upload['subdir'];
		$upload['url']    = $upload['baseurl'] . $upload['subdir'];

		return $upload;
	}

	public static function setupUploadsDir() {
		$wp_upload_dir    = wp_upload_dir();
		$protected_folder = trailingslashit( $wp_upload_dir['basedir'] ) . static::UPLOADS_DIR;

		// Do not allow direct access to files in protected folder
		// Add rules to .htacess
		$rules  = "Order Deny,Allow\n";
		$rules .= 'Deny from all';

		if ( ! @file_get_contents( trailingslashit( $protected_folder ) . '.htaccess' ) ) {
			// Protected directory doesn't exist - create it.
			wp_mkdir_p( $protected_folder );
		}

		@file_put_contents( trailingslashit( $protected_folder ) . '.htaccess', $rules );
	}

	public static function getDownloadUrl( $attachmentId ): string {
		return admin_url( 'admin-ajax.php?action=download_qte_rest_file&file_id=' . $attachmentId );
	}

	public static function downloadFile() {
		if ( ! isset( $_GET['file_id'] ) ) {
			return;
		}

		$file = get_attached_file( $_GET['file_id'] );

		if ( ! $file ) {
			return;
		}

		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename=' . basename( $file ) );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . filesize( $file ) );

		ob_clean();
		flush();
		readfile( $file );
		exit();
	}
}
