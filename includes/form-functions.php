<?php

/**
 * This file contains all the form functions for qterest.
 */

namespace QTEREST\Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This function makes a label from the given parameters
 *
 * @param string  $for The id for the element which the label belongs to
 * @param string  $text The content of the label
 * @param boolean $echo Determines whether or not to echo the label. Default is false
 *
 * @return mixed
 */
function render_label( $for, $text, $echo = false ) {
	$label = "<label for=\"$for\">$text</label>";

	if ( $echo ) {
		echo $label;
	} else {
		return $label;
	}
}

/**
 * This function make a field form the given parameters
 *
 * @param array   $args => [
 *                'name' => (string) The name for the form field
 *                'placeholder => (string) The placeholder for the field
 *                'type' => (string) The field type
 *                'value' => (string) The field value
 *                'class' => (string) The field class
 *                'label' => (string) If filled a label will be added
 *                'required' => (string) Is the field required?
 *                'options' => [ Options for select
 *                    'name' => (string) Name for option
 *                    'value' => (string) Value for option
 *                ],
 *            ]
 * @param boolean $echo Determines whether or not to echo the label. Default is false
 *
 * @return mixed
 */
function render_field( $args, $echo = false ) {
	$field = '';

	$required = isset( $args['required'] ) && $args['required'] ? 'required' : '';

	if ( $required ) {
		$args['class'] = isset( $args['class'] ) ? $args['class'] .= ' required' : 'required';
	};

	$class = isset( $args['class'] ) ? "class=\"$args[class]\"" : null;

	$id = isset( $args['id'] ) && ! empty( $args['id'] ) ? $args['id'] : 'field_' . $args['name'];

	$for = isset( $args['label_for'] ) && ! $args['label_for'] ? null : $id;

	$toggles = isset( $args['toggles'] ) && $args['toggles'] && $args['type'] == 'checkbox' ? 'qterest-toggles' : null;

	$rows = isset( $args['rows'] ) && $args['rows'] ? $args['rows'] : 4;

	$value = isset( $args['value'] ) ? $args['value'] : '';

	$label = isset( $args['label'] ) ? $args['label'] : '';

	$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';

	$pattern = isset( $args['pattern'] ) ? "pattern=\"$args[pattern]\"" : null;

	$pattern_format = isset( $args['pattern_format'] ) ? "title=\"$args[pattern_format]\"" : null;

	switch ( $args['type'] ) {
		case 'select':
			if ( $label ) {
				$field .= render_label( $for, $label );
			}

			$field .= "<select id=\"$id\" $class name=\"$args[name]\" $required ><option>$args[placeholder]</option>";

			if ( isset( $args['options'] ) && $args['options'] ) {
				foreach ( $args['options'] as $option ) {
					$field .= "<option value=\"$option[value]\">$option[name]</option>";
				}
			}

			$field .= '</select>';

			break;

		case 'textarea':
			if ( $label ) {
				$field .= render_label( $id, $label );
			}

			$field .= "<textarea id=\"$id\" $class name=\"$args[name]\" rows=\"$rows\" placeholder=\"$placeholder\" $required >$value</textarea>";

			break;

		case 'tel':
			if ( $label ) {
				$field .= render_label( $for, $label );
			}

			$field .= "<input id=\"$id\" $class type=\"$args[type]\" name=\"$args[name]\" placeholder=\"$placeholder\" value=\"$value\" $pattern $pattern_format $required/>";

			break;

		case 'text':
		case 'email':
		case 'hidden':
		case 'file':
			if ( $label ) {
				$field .= render_label( $for, $label );
			}

			$field .= "<input id=\"$id\" $class type=\"$args[type]\" name=\"$args[name]\" placeholder=\"$placeholder\" value=\"$value\" $required/>";

			break;

		case 'checkbox':
		case 'radio':
			$field .= "<label for=\"$for\" class=\"qterest-$args[type] $args[class]\"><input id=\"$id\" class=\"$toggles $required\" type=\"$args[type]\" name=\"$args[name]\" value=\"$value\"$required />$args[label]</label>";

	}
	/**
	 * This filter can be used to overwrite the default HTML for a specific field type.
	 */
	$field = apply_filters( "qterest_contact_field_{$args['type']}_html", $field, $args );

	if ( $echo ) {
		echo $field;
	} else {
		return $field;
	}
}

/**
 * This function renders a misc form the given parameters
 */
function render_misc( $args, $echo = false ) {
	$misc = '';

	switch ( $args['type'] ) {
		case 'title':
			$misc .= "<h$args[size]>$args[text]</$args[size]>";
			break;

		case 'paragraph':
			$misc .= "<p>$args[text]</p>";
			break;

		case 'link':
			$misc .= "<a href=\"$args[href]\">$args[text]</a>";
			break;
	}

	if ( $echo ) {
		echo $misc;
		return;
	}

	return $misc;
}