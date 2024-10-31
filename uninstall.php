<?php


/**
 * Fired when the plugin is uninstalled.
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}


# Delete all existing plugin options and settings
delete_option('widget_pixel_width');
delete_option('olycash_plugin_key');
delete_option('business_name');
delete_option('business_email');
delete_option('third_party_fee');
delete_option('olycash_plugin_id');
delete_option('woocommerce_wc_olycash_gateway_settings');
delete_option('woocommerce_ofwc_olycash_settings');
delete_option('oly_plugin_id');
delete_option('oly_plugin_key');
delete_option('olycash_pre_process_actions');
delete_option('olycash_post_process_actions');
delete_option('olycash_post_response_actions');
delete_option('olycash_third_party_fee_paid_by');