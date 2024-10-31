<?php
/*
 * Plugin Name: OlyCash for WooCommerce
 * Plugin URI: https://olycash.com/plugin
 * Description: Make payments with any method such as Card, PayPal, Mobile Money, Cash Codes, Crypto and more. Withdraw your sales via SMS or OlyCash mobile app. For more information visit <a href="https://olycash.com">www.olycash.com</a>.
 * Author: OlyCash
 * Author URI: https://olycash.com
 * Version: 2.0.2
 * */

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
    
define( 'OLY_GATEWAY_VERSION', '2.0.2' );
define( 'OLY_FILE_URL', __FILE__ );
define( 'OLY_BASENAME', plugin_basename(__FILE__) );
define( 'OLY_DIR_URL', plugin_dir_path( __FILE__ ) );
define( 'OLY_BASE_URL', plugins_url('',__FILE__ ));
define( 'OLY_ICON_URL', plugins_url('assets/woocommerce_checkout_icon_set.svg',__FILE__ ));
define( 'OLYPAGES_BASE_URL', 'https://api.olypages.com/v1/' );
define( 'OLYCASH_BASE_URL', 'https://api.olycash.com/v2/' );




/**
 * Add the Settings link to the plugin
 */
function plugin_action_links( $links ) 
{

$settings_url = esc_url( get_admin_url( null, 'admin.php?page=wc-settings&tab=checkout&section=wc_olycash_gateway' ) );
array_unshift( $links, "<a title='OlyCash Settings Page' href='$settings_url'>Settings</a>" );

return $links;

}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'plugin_action_links' );





if ( in_array( 'woocommerce/woocommerce.php', 
apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    /**
     * The plugin generator class used to setup admin settings
     * and user interface settings
     */
    require OLY_DIR_URL . 'includes/class.generator.php';

    /**
     * Generate the plugin
     */
    function generate_plugin(){
        $plugin = new Wc_Generate_Oly();
    }
    generate_plugin();
    
}



















