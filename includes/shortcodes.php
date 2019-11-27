<?php

/**
 * This file contains all the shortcodes for qterest.
 */

namespace QTEREST\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function handle_qterest_form_shortcode( $atts, $content ) {
	global $qterest_settings;

	if ( ! $qterest_settings['contact'] ) {
		if ( current_user_can( 'manage_options' ) ) {
			return '<p class="qterest-error">' . __( 'Contact is disabled on this site!', 'qterest' ) . '</p>';
		}
		return;
	}

	$field_defaults = array(
		'type'        => 'text',
		'required'    => false,
		'name'        => '',
		'placeholder' => '',
		'value'       => '',
		'class'       => '',
		'id'          => '',
		'toggles_on'  => '',
		'toggles'     => false,
	);

	$misc_defaults = array(
		'type' => 'title',
	);

	$lines = explode( "\n", str_replace( '<br />', '', $content ) );

	$fields = array();

	foreach ( $lines as $line ) {
		$raw_args = explode( '|', $line );

		$parsed_args = array();

		if ( ! empty( trim( $line ) ) ) {
			foreach ( $raw_args as $arg ) {
				$parsed_arg = explode( '=', $arg );

				if ( \sizeof( $parsed_arg ) > 1 ) {
					if ( trim( $parsed_arg[0] ) == 'options' ) {
						$parsed_options = array();
						$raw_options    = explode( ';', $parsed_arg[1] );

						foreach ( $raw_options as $raw_option ) {
							if ( ! empty( $raw_option ) ) {
								$parsed_option = explode( ':', $raw_option );

								$parsed_options[] = array(
									'value' => trim( $parsed_option[0] ),
									'name'  => trim( $parsed_option[1] ),
								);
							}
						}

						$parsed_args[ trim( $parsed_arg[0] ) ] = $parsed_options;
					} else {
						$parsed_args[ trim( $parsed_arg[0] ) ] = trim( $parsed_arg[1] );
					}
				}
			}

			switch ( $parsed_args['type'] ) {
				case 'title':
				case 'paragraph':
				case 'link':
					if ( isset( $parsed_args['text'] ) && ! empty( $parsed_args['text'] ) ) {
						$fields[] = wp_parse_args( $parsed_args, $misc_defaults );
					}

					break;
				default:
					if ( isset( $parsed_args['name'] ) && ! empty( $parsed_args['name'] ) ) {
						$fields[] = wp_parse_args( $parsed_args, $field_defaults );
					}

					break;

			}
		}
	}

	$form_args = array(
		'fields' => $fields,
	);

	if ( is_array( $atts ) ) {
		foreach ( $atts as $key => $val ) {
			$form_args[ $key ] = $val;
		}
	}

	return \qterest_render_form( $form_args, false );
}
add_shortcode( 'qterest-form', __NAMESPACE__ . '\\handle_qterest_form_shortcode' );

function handle_qterest_mailchimp_form_shortcode( $atts, $content ) {
	$input_label  = $atts['input_label'] ?? __( 'Email', 'qterest' );
	$submit_label = $atts['submit_label'] ?? __( 'Subscribe', 'qterest' );

	return \qterest_render_mailchimp_form( $input_label, $submit_label, false );
}

add_shortcode( 'qterest-mailchimp-form', __NAMESPACE__ . '\\handle_qterest_mailchimp_form_shortcode' );
