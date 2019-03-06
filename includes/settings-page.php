<?php
/**
 * This file contains all the functions to setup the settings page
 */
namespace QTEREST\SettingsPage;

use DrewM\MailChimp\MailChimp;
use function QTEREST\Helpers\mailchimp_api_key_is_valid;

if (!defined('ABSPATH')) {
    exit;
}

global $qterest_settings;

/**
 * Register settings page if MailChimp is activated for this site 
 */
if ($qterest_settings['mailchimp']) {

    function settings_init()
    {

        // register a new setting for "wporg" page
        register_setting('qterest', 'qterest_options');

        // register a new section in the "wporg" page
        add_settings_section(
            'qterest_mailchimp_section',
            __('MailChimp', 'qterest'),
            __NAMESPACE__ . '\\qterest_mailchimp_section_cb',
            'qterest'
        );

        /**
         * Register MailChimp API key field
         */
        add_settings_field(
            'qterest_field_mailchimp_api_key',
            __('MailChimp API Key', 'qterest'),
            __NAMESPACE__ . '\\qterest_field_mailchimp_api_key_cb',
            'qterest',
            'qterest_mailchimp_section',
            [
                'label_for' => 'qterest_field_mailchimp_api_key',
                'class' => 'qterest_row',
                'qterest_custom_data' => 'custom',
            ]
        );

        if (mailchimp_api_key_is_valid()) {

            /**
             * If api key is valid register MailChimp list field
             */
            add_settings_field(
                'qterest_field_mail_list',
                __('MailChimp Lista', 'qterest'),
                __NAMESPACE__ . '\\qterest_field_mailchimp_mail_list_cb',
                'qterest',
                'qterest_mailchimp_section',
                [
                    'label_for' => 'qterest_field_mailchimp_mail_list',
                    'class' => 'qterest_row',
                    'qterest_custom_data' => 'custom',
                ]
            );
        }
    }

    /**
     * register our wporg_settings_init to the admin_init action hook
     */
    add_action('admin_init', __NAMESPACE__ . '\\settings_init');

    /**
     * Callback for title to the mailchimp section
     */
    function qterest_mailchimp_section_cb($args)
    {
        ?>
        <p id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e('Settings for MailChimp', 'qterest');?></p>
        <?php
    }

    /**
     * Callback for MailChimp API key field
     */
    function qterest_field_mailchimp_api_key_cb($args)
    {

        $options = get_option('qterest_options');

        ?>
            <input type="text" value="<?php echo isset($options[$args['label_for']]) ? $options[$args['label_for']] : null; ?>" id="<?php echo esc_attr($args['label_for']); ?>" data-custom="<?php echo esc_attr($args['qterest_custom_data']); ?>" name="qterest_options[<?php echo esc_attr($args['label_for']); ?>]" size="50">
            <p class="description"><?php esc_html_e('Enter your Mailchimp API key ', 'qterest');?></p>
        <?php
}

    /**
     * Callback for MailChimp mail list field
     */
    function qterest_field_mailchimp_mail_list_cb($args)
    {

        $options = get_option('qterest_options');

        if (mailchimp_api_key_is_valid()):
        ?>
            <select id="<?php echo esc_attr($args['label_for']); ?>" data-custom="<?php echo esc_attr($args['qterest_custom_data']); ?>" name="qterest_options[<?php echo esc_attr($args['label_for']); ?>]">
                <?php

                    $MailChimp = new MailChimp($options['qterest_field_mailchimp_api_key']);

                    $response = $MailChimp->get("lists");

                    foreach ($response['lists'] as $list) { ?>
                                    <option value="<?php echo $list['id'] ?>" <?php echo isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], $list['id'], false)) : (''); ?>>
                                        <?php esc_html_e($list['name'], 'do_sub');?>
                                    </option>
                    <?php } ?>
            </select>
            <p class="description">
                <?php esc_html_e('Choose which list you want to add new subscribers to', 'do_sub');?>
            </p>
        <?php else: ?>
            <p><b>
                <?php esc_html_e('Please enter a valid api key', 'do_sub');?>
            </b></p>
        <?php endif;
    }

    /**
     * Callback to register settings page
     */
    function qterest_options_page()
    {

        add_menu_page(
            'Settings',
            'QTEREST Settings',
            'manage_options',
            'qterest_settings',
            __NAMESPACE__ . '\\qterest_options_page_html'
        );
    }

    /**
     * Register settings page
     */
    add_action('admin_menu', __NAMESPACE__ . '\\qterest_options_page');

    /**
     * page callback
     */
    function qterest_options_page_html()
    {

        /**
         * Check user capabilities
         */
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['settings-updated'])) {

            add_settings_error('qterest_messages', 'qterest_message', __('Settings Saved', 'qterest'), 'updated');
        }

        settings_errors('qterest_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                    settings_fields('qterest');

                    do_settings_sections('qterest');

                    submit_button('Save Settings');
                ?>
            </form>
        </div>
    <?php
    }
}