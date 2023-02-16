<?php


namespace QTEREST\Utils;

class GatsbySource {

    public function __construct() {

        add_filter( 'gatsby_pre_log_action_monitor_action', function( $null, $log_data ) {

            $meta_data = get_post_meta($log_data['node_id']);

            // If any of the meta data is a qte-rest file, return false
            if (isset($meta_data['_wp_attached_file']) && is_array($meta_data['_wp_attached_file'])) {
                foreach ($meta_data['_wp_attached_file'] as $attached_file_path) {
                    if (strpos($attached_file_path, 'qte-rest') !== false)
                        return false;
                }
            }

            return $null;
        
        }, 10, 2 );
    }
}
