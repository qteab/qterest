<?php


namespace QTEREST\Uploads;


class FileHandler
{
    const UPLOADS_DIR = "qte-rest";

    protected $postId;

    public function __construct( int $postId ) {
        $this->postId = $postId;

        // Make sure the required functions is loaded
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

        static::setupUploadsDir();

        add_filter( 'upload_dir', array( $this, 'changeUploadsDir' ) );
    }

    public static function registerHooks() {
        add_filter( 'ajax_query_attachments_args', array( static::class, 'appendAjaxQueryAttachmentArgs' ) );
    }

    public static function make( int $postId ): self {
        return new static( $postId );
    }

    public function handleAllFiles(): array {
        $attachmentIds = [];
        foreach ( $_FILES as $fileId => $file ) {
            $attachmentIds[] = $this->handleFile( $fileId );
        }
    }

    public function handleFile( string $fileId ) {
        return media_handle_upload( $fileId, $this->postId, array(
            'meta_query' => array(
                'qte_rest_file' => true,
            )
        ) );
    }

    public static function appendAjaxQueryAttachmentArgs( $query ) {
        $query['meta_query'] = array(
            array(
                'key' => 'qte_rest_file',
                'compare' => 'NOT EXISTS',
            )
        );

        return $query;
    }

    public static function changeUploadsDir( $upload) {
        $upload['subdir'] = '/' . static::UPLOADS_DIR . $upload['subdir'];
        $upload['path'] = $upload['basedir'] . $upload['subdir'];
        $upload['url']  = $upload['baseurl'] . $upload['subdir'];

        return $upload;
    }

    public static function setupUploadsDir() {
        $wp_upload_dir = wp_upload_dir();
        $protected_folder = trailingslashit( $wp_upload_dir['basedir'] ) . static::UPLOADS_DIR;

        // Do not allow direct access to files in protected folder
        // Add rules to .htacess
        $rules = "Order Deny,Allow\n";
        $rules .= "Deny from all";

        if( ! @file_get_contents( trailingslashit( $protected_folder ) . '.htaccess' ) ) {
            //Protected directory doesn't exist - create it.
            wp_mkdir_p( $protected_folder );
        }

        @file_put_contents( trailingslashit( $protected_folder ) . '.htaccess', $rules );
    }

}
