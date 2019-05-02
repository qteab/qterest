<?php

/**
 * This file contains customization on edit page for the contact_requests post type 
 */

namespace QTEREST\CPTS\EditPage;

if (!defined('ABSPATH')) {
    exit;
}

function contact_add_custom_box()
{

    add_meta_box(
        'request-content',           // Unique ID
        'Request Content',  // Box title
        __NAMESPACE__ . '\\contact_custom_box_html',  // Content callback, must be of type callable
        'contact_requests',        // Post type
        'normal'
    );

    add_meta_box(
        'request-information',           // Unique ID
        'Request Information',  // Box title
        __NAMESPACE__ . '\\contact_custom_side_box_html',  // Content callback, must be of type callable
        'contact_requests',        // Post type
        'side'
    );

}
add_action('add_meta_boxes', __NAMESPACE__ . '\\contact_add_custom_box');

function contact_custom_box_html($post)
{
    ?>
    <table>
        <tbody>
            <?php $request_content = get_post_meta($post->ID, 'request_content', true);

                $request_content = \unserialize($request_content);

                foreach($request_content as $key => $value){

                    $is_link = substr($value, 0, 4) == "http";

                    echo "<tr><th>" . \ucfirst(str_replace("_", ' ', $key)) . "</th><td>" . ($is_link ? "<a href=\"$value\">$value</a>" : $value ) . "</td></tr>";

                }
            
            ?>
        </tbody>
    </table>
    <style>
        #request-content td {
            padding-left: 1rem;
        }
        #request-content th {
            text-align: left;
        }
    </style>
    <?php
}

function contact_custom_side_box_html($post)
{
    ?>
    <table>
        <tbody>
            <tr>
                <th>Date</th>
                <td><?php echo get_the_date("Y-m-d H:m:s", $post->ID); ?></td>
            </tr>
            <tr>
                <th>IP</th>
                <td><?php echo get_post_meta($post->ID, 'request_ip_address', true); ?></td>
            </tr>
        </tbody>
    </table>
    <style>
        #request-information td {
            padding-left: 1rem;
        }
        #request-information th {
            text-align: left;
        }
    </style>
    <?php
}

add_action( 'do_meta_boxes' , __NAMESPACE__ . '\\contact_remove_default_meta_boxes'  );
 
/**
 * Remove default meta boxes 
 */
function contact_remove_default_meta_boxes() {
    remove_meta_box( 'postcustom' , 'contact_requests' , 'normal' ); 
    remove_meta_box( 'submitdiv' , 'contact_requests' , 'side' );
    remove_meta_box( 'sharing_meta' , 'contact_requests' , 'side' );

}
?>