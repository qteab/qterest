<?php

/**
 * This file contains global functions for qterest;
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * This function renders a qterest form from an array.
 * 
 * @param array $args Arry containing the form structure
 * $args => [
 *      'wrapper_class' => (string) Overrides the default wrapper class
 *      'form_class' => (string) Overrides the default form class
 *      'form_row_class' => (string) Overrides the default form row class
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
 *              'options' => [ Options for select 
 *                      'name' => (string) Name for option
 *                      'value' => (string) Value for option
 *                  ],
 *          ],
 *      ]
 *  ]
 * @param bool $echo Tells whether or not to echo. True as default 
 */
function qterest_render_form(array $args, bool $echo = true){

    $defaults = array(
        'wrapper_class' => "qterest-form-container",
        'form_class' => "qterest-form",
        'form_row_class' => "qterest-form-row",
        'error_messages_class' => "qterest-error-messages",
        'success_messages_class' => "qterest-success-messages",
        'form_fields_class' => "qterest-form-fields",
        'submit_label' => __("Submit", 'qterest'),
        'submit_class' => 'button submit'
    );

    $args = wp_parse_args($args, $defaults);

    $form = "<div class=\"$args[wrapper_class]\">";

    $form .= "<form class=\"$args[form_class]\">";

    if(isset($args['form_title']) && $args['form_title']){
        
        $form .= "<h3>$args[form_title]</h3>";

    }

    $form .= "<div class=\"qterest-spinner-overlay\"><div class=\"qterest-spinner\"></div></div>";

    $form .= "<div class=\"$args[error_messages_class]\"></div>";

    $form .= "<div class=\"$args[success_messages_class]\"></div>";

    $form .= "<div class=\"$args[form_fields_class]\">";


    if(isset($args['fields']) && $args['fields']){

        foreach($args['fields'] as $field){ // Loop through all fields

            $form .= "<div class=\"$args[form_row_class]\">";

            $required = $field['required'] ? "required" : null;

            $class = isset($field['class']) ? "class=\"$field[class]\"" : null;

            switch($field['type']){
                case "select":

                    $form .= "<select $class name=\"$field[name]\" $required ><option>$field[placeholder]</option>";

                    if(isset($field['options']) && $field['options']) {

                        foreach($field['options'] as $option){
                            $form .= "<option value=\"$option[value]\">$option[name]</option>";
                        }

                    }

                    $form .= "</select>";

                    break;

                case "textarea":

                    $form .= "<textarea $class name=\"$field[name]\" placeholder=\"$field[placeholder]\" $required ></textarea>";

                    break;
                
                case "tel":
                case "text":
                case "email":
                case "hidden":
                    
                    $form .= "<input $class type=\"$field[type]\" name=\"$field[name]\" placeholder=\"$field[placeholder]\" value=\"$field[value]\" $required />";

                    break;

                case "checkbox":
                case "radio":

                    $form .= "<label class=\"qterest-$field[type] $field[class]\"><input type=\"$field[type]\" name=\"$field[name]\" value=\"$field[value]\"$required />$field[placeholder]</label>";

            }

            $form .= "</div>";

        }
    }

    $form .= "<div class=\"$args[form_row_class]\"><input class=\"$args[submit_class]\" type=\"submit\" value=\"$args[submit_label]\"></div>";

    $form .= "</div>";

    $form .= "</form>";

    $form .= "</div>";

    if(!$echo){
        return $form;
    }

    echo $form;

}