<?php

/**
 * This file contains global functions for qterest;
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use function QTEREST\Form\render_field;
use function QTEREST\Form\render_misc;

/**
 * This function renders a qterest form from an array.
 *
 * @param array $args Arry containing the form structure
 * $args => [
 *      'wrapper_class' => (string) Overrides the default wrapper class
 *      'form_class' => (string) Overrides the default form class
 *      'form_row_class' => (string) Overrides the default form row class
 *      'form_title' => (string) Adds a h3-tag with the given content
 *      'error_messages_class' => (string) Overrides the default error message class
 *      'success_message_class' => (string) Overrides the default success message class
 *      'form_fields_class' => (string) Overrides the default form fields class
 *      'submit_label' => (string) Overrides the default submit label
 *      'submit_class' => (string) Overrides the default submit class
 *      'fields' = [ // Defines the form fields
 *          $anyKey => [
 *              'name' => (string) The name for the form field
 *              'placeholder => (string) The placeholder for the field
 *              'type' => (string) The field type
 *              'value' => (string) The field value
 *              'class' => (string) The field class
 *              'label' => (string) If filled a label will be added
 *              'toggles' => (boolean) Should this field toggle other fields ONLY when type is 'checkbox'
 *              'toggles_on' => (string) The id of the field that this field toggles on
 *              'options' => [ Options for select
 *                      'name' => (string) Name for option
 *                      'value' => (string) Value for option
 *                  ],
 *          ],
 *      ]
 *  ]
 * @param bool  $echo Tells whether or not to echo. True as default
 */
function qterest_render_form( array $args, bool $echo = true ) {
	$defaults = array(
		'wrapper_class'          => 'qterest-form-container',
		'form_class'             => 'qterest-form',
		'form_row_class'         => 'qterest-form-row',
		'form_misc_class'        => 'qterest-form-misc',
		'error_messages_class'   => 'qterest-error-messages',
		'success_messages_class' => 'qterest-success-messages',
		'form_fields_class'      => 'qterest-form-fields',
		'submit_label'           => __( 'Submit', 'qterest' ),
		'submit_class'           => 'button submit',
	);

	$args = wp_parse_args( $args, $defaults );

	$form = "<div class=\"$args[wrapper_class]\">";

	$form .= "<form class=\"$args[form_class]\">";

	if ( isset( $args['form_title'] ) && $args['form_title'] ) {
		$form .= "<h3>$args[form_title]</h3>";
	}

	$form .= '<div class="qterest-spinner-overlay"><div class="qterest-spinner"></div></div>';

	$form .= "<div class=\"$args[error_messages_class]\"></div>";

	$form .= "<div class=\"$args[success_messages_class]\"></div>";

	$form .= "<div class=\"$args[form_fields_class]\">";

	if ( isset( $args['fields'] ) && $args['fields'] ) {
		foreach ( $args['fields'] as $field ) { // Loop through all fields

			switch ( $field['type'] ) {
				case 'paragraph':
				case 'link':
				case 'title':
					$toggles_on = isset( $field['toggles_on'] ) && ! empty( $field['toggles_on'] ) ? "data-qterest-toggles-on=\"field_$field[toggles_on]\"" : null;

					$form .= "<div class=\"$args[form_misc_class]\" $toggles_on>";

					$form .= render_misc( $field );

					$form .= '</div>';

					break;

				default:
					$toggles_on = isset( $field['toggles_on'] ) && ! empty( $field['toggles_on'] ) ? "data-qterest-toggles-on=\"field_$field[toggles_on]\"" : null;

					$form .= "<div class=\"$args[form_row_class]\" $toggles_on>";

					$form .= render_field( $field );

					$form .= '</div>';

					break;
			}
		}
	}

	$form .= "<div class=\"$args[form_row_class]\"><input class=\"$args[submit_class]\" type=\"submit\" value=\"$args[submit_label]\"></div>";

	$form .= '</div>';

	$form .= '</form>';

	$form .= '</div>';

	if ( ! $echo ) {
		return $form;
	}

	echo $form;
}

/**
 * This function makes a simple MailChimp sign up form.
 *
 * @param string $input_label
 * @param string $submit_label
 * @param bool   $echo
 */
function qterest_render_mailchimp_form( string $input_label, string $submit_label, bool $echo = true ) {
    $field = apply_filters(
        'qterest_mailchimp_field_arguments',
        array(
            'name'        => 'email',
            'type'        => 'email',
            'label'       => $input_label,
            'placeholder' => $input_label,
            'required'    => true,
        )
    );

	$form = '<div class="qterest-form-container">';

	$form .= '<form class="qterest-mailchimp-signup">';

	$form .= '<div class="qterest-spinner-overlay"><div class="qterest-spinner"></div></div>';

	$form .= '<div class="qterest-error-messages"></div>';

	$form .= '<div class="qterest-success-messages"></div>';

	$form .= '<div class="qterest-form-fields">';

	$form .= '<div class="qterest-form-row">';

	$form .= render_field( $field );

	$form .= '</div>';

	$form .= "<div class=\"qterest-form-row\"><input class=\"button submit\" type=\"submit\" value=\"$submit_label\"></div>";

	$form .= '</div>';

	$form .= '</form>';

	$form .= '</div>';

	if ( ! $echo ) {
		return $form;
	}

	echo $form;
}
