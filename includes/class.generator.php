<?php

/**
 * Create OlyCash Account and generate widget
 * 
 * @author OlyCash
 * @copyright OlyCash Inc. 
 * 
 */

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Wc_Generate_Oly {

    # Define the OlyCash Widget default plugin settings
    public function __construct() {
        $this->account_email = get_bloginfo('admin_email');
        $this->account_name = get_bloginfo('name');

        add_filter( 'woocommerce_payment_gateways', array( $this, 'payment_gateways' ) );
        add_action( 'plugins_loaded', array( $this, 'init_plugin' ));

        register_activation_hook(OLY_FILE_URL, array( $this, 'olycash_plugin_activate' ));
        add_action('admin_init', array( $this, 'olycash_plugin_redirect' ));
    }





    /**
     * Add OlyCash to one of the woocommerce 
     * payment gateways
     */
    public function payment_gateways($methods)
    {
        $methods[] = 'WC_Gateway_OlyCash';
        return $methods;
    }






    /**
     * Initialise gateway and all its dependecies
     */
    public function init_plugin() 
    {
        if (class_exists('WC_Payment_Gateway')){
            require OLY_DIR_URL . 'includes/class.gateway.php';
        }
    }








    /**
     * Enable plugin internationilazation
     * 
     */

    public function load_plugin_textdomain(){
        load_plugin_textdomain('wc-olycash-gateway');
    }







    public function olycash_plugin_activate() {
        add_option('olycash_plugin_do_activation_redirect', true);
    }
    





    public function olycash_plugin_redirect() {
        if (get_option('olycash_plugin_do_activation_redirect', false)) {
            delete_option('olycash_plugin_do_activation_redirect');

            // Delete all existing details
            delete_option('woocommerce_wc_olycash_gateway_settings');
            delete_option('woocommerce_ofwc_olycash_settings');
            delete_option('olycash_plugin_key');
            delete_option('business_name');
            delete_option('email_address');
            delete_option('third_party_fee');
            delete_option('olycash_plugin_id');
            delete_option('oly_plugin_id');
            delete_option('oly_plugin_key');
            delete_option('olycash_pre_process_actions');
            delete_option('olycash_post_process_actions');
            delete_option('olycash_post_response_actions');
            delete_option('olycash_third_party_fee_paid_by');
            delete_option('widget_pixel_width');
            wp_redirect("admin.php?page=wc-settings&tab=checkout&section=wc_olycash_gateway");
            exit;
        }
    }






















}


