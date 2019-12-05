<?php

/**
 * This file contains cpts for qterest
 */

namespace QTEREST\CPTS;

use QTEREST\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( Settings::isEnabled( Settings::Contact ) ) {

	// Register Custom Post Type
	function contact_post_type() {
		$labels = array(
			'name'                  => _x( 'Contact Requests', 'Post Type General Name', 'qterest' ),
			'singular_name'         => _x( 'Contact Request', 'Post Type Singular Name', 'qterest' ),
			'menu_name'             => __( 'Contact Requests', 'qterest' ),
			'name_admin_bar'        => __( 'Contact Request', 'qterest' ),
			'archives'              => __( 'Request Archives', 'qterest' ),
			'attributes'            => __( 'Request Attributes', 'qterest' ),
			'parent_item_colon'     => __( 'Parent Request:', 'qterest' ),
			'all_items'             => __( 'All Request', 'qterest' ),
			'add_new_item'          => __( 'Add New Request', 'qterest' ),
			'add_new'               => __( 'Add New', 'qterest' ),
			'new_item'              => __( 'New Request', 'qterest' ),
			'edit_item'             => __( 'Edit Request', 'qterest' ),
			'update_item'           => __( 'Update Request', 'qterest' ),
			'view_item'             => __( 'View Request', 'qterest' ),
			'view_items'            => __( 'View Request', 'qterest' ),
			'search_items'          => __( 'Search Requests', 'qterest' ),
			'not_found'             => __( 'Not found', 'qterest' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'qterest' ),
			'featured_image'        => __( 'Featured Image', 'qterest' ),
			'set_featured_image'    => __( 'Set featured image', 'qterest' ),
			'remove_featured_image' => __( 'Remove featured image', 'qterest' ),
			'use_featured_image'    => __( 'Use as featured image', 'qterest' ),
			'insert_into_item'      => __( 'Insert into request', 'qterest' ),
			'uploaded_to_this_item' => __( 'Uploaded to this request', 'qterest' ),
			'items_list'            => __( 'Requests list', 'qterest' ),
			'items_list_navigation' => __( 'Requests list navigation', 'qterest' ),
			'filter_items_list'     => __( 'Filter requests list', 'qterest' ),
		);
		$args   = array(
			'label'               => __( 'Contact Request', 'qterest' ),
			'description'         => __( 'Contact requests', 'qterest' ),
			'labels'              => $labels,
			'supports'            => false,
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-email-alt',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'capability_type'     => 'page',
			'capabilities'        => array(
				'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
			),
			'map_meta_cap'        => true,
			'show_in_rest'        => false,
		);
		register_post_type( 'contact_requests', $args );
	}
	add_action( 'init', __NAMESPACE__ . '\\contact_post_type', 0 );
}
