<?php

/**
 * Initialise gateway settings 
 * and render widget in the pay page
 * 
 * @author OlyCash
 * @copyright OlyCash Inc. 
 * 
 */

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class WC_Gateway_OlyCash extends WC_Payment_Gateway {
    
    public function __construct() {

        # Create a plugin id to avoid naming conflicts
        $this->id = 'wc_olycash_gateway';

        # Show OlyCash logo on the checkout page
        $this->icon = OLY_ICON_URL;
    
        # Do not show any fields on the checkout page
        $this->has_fields = false; 

        $this->olycash_plugin_id = !empty(get_option( 'olycash_plugin_id' ))?get_option( 'olycash_plugin_id' ):'';
        $this->description = __('Make payments with any method such as Card, PayPal, Mobile Money, Cash Codes, Crypto and more. Withdraw your sales via SMS or OlyCash mobile app. For more information visit <a href="https://olycash.com">www.olycash.com</a> ', 'wc-olycash-gateway');
    
        # Show OlyCash Gateway details shown on Woocommerce > setting > payments
        $this->method_title = __('Pay With OlyCash', 'wc-olycash-gateway');
        $this->method_description = __('Make payments with any method such as Card, PayPal, Mobile Money, Cash Codes, Crypto and more. Withdraw your sales via SMS or OlyCash mobile app. For more information visit <a href="https://olycash.com">www.olycash.com</a> ', 'wc-olycash-gateway');
            
        // Method with all options fields
       
        if ( is_admin() ) {
            $this->init_form_fields();
            # Save plugin default settings in the database
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        }
        
        // Load the settings.
        $this->init_settings();

        # Save widget default settings
        $this->title = !empty(get_option( 'title' ))?get_option( 'title' ):__('Pay With OlyCash', 'wc-olycash-gateway');
        $this->enabled = $this->get_option( 'enabled' );
        $this->business_name = !empty(get_option( 'business_name' ))?get_option( 'business_name' ):get_bloginfo('name');
        $this->email_address = !empty(get_option( 'business_email' ))?get_option( 'business_email' ):get_bloginfo('admin_email');
        $this->third_party_fee = $this->get_option( 'third_party_fee' );
        $this->payment_frequency = $this->get_option( 'payment_frequency' );
        $this->widget_display = $this->get_option( 'widget_display' );
        $this->default_card_provider = $this->get_option( 'default_card_provider' );
        $this->widget_background_color = $this->get_option( 'widget_background_color' );
        $this->widget_pixel_width = $this->get_option( 'widget_pixel_width' );
        $this->post_response_type = $this->get_option( 'post_response_type' );
        $this->post_response_action = $this->get_option( 'post_response_action' );
        $this->plugin_key = !empty(get_option( 'olycash_plugin_key' ))?get_option( 'olycash_plugin_key' ):'';


        add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
        
        # Add widget css and js code to the checkout page
        add_action( 'wp_enqueue_scripts', array( $this, 'widget_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_settings_scripts' ) );
        add_action( 'woocommerce_api_' . $this->id , array( $this, 'update_order_details' ) );

    }






    # Render the admin settings form in the woocommerce dashboard
    public function init_form_fields()
    {
        require OLY_DIR_URL . 'trunk/admin/settings.php';
    }



    # Validate the admin settings on form submition
    public function process_admin_options() {

        // Make sure user supplies the first name
        if ( empty( $_POST['woocommerce_'.$this->id.'_business_name'] ) ) 
        {
            $settings = new WC_Admin_Settings();
            $settings->add_error('Please provide your official business name.');
            return;
        } 
        elseif (empty( $_POST['woocommerce_'.$this->id.'_business_email'] )) 
        {
            $settings = new WC_Admin_Settings();
            $settings->add_error('Please provide your registered business email contact.');
            return;
        }
        else {

            $business_name  = $_POST['woocommerce_'.$this->id.'_business_name'];
            $business_email = $_POST['woocommerce_'.$this->id.'_business_email'];
            
            # First generate the plugin key
            if(empty(get_option( 'olycash_plugin_id' )))
            {
                require OLY_DIR_URL . 'includes/class.processor.php';
                $widget = new WC_Oly_Processor;
                $response = $widget->process_plugin($business_email, $business_name);

                if($response == 'error') {
                    $settings = new WC_Admin_Settings();
                    $settings->add_error('There was a problem creating your OlyCash account.');
                    return;
                }
            }

            # Enable plugin for checkout display
            $_POST['woocommerce_'.$this->id.'_enabled'] = 1;

            # Save posted data
            parent::process_admin_options();
        }
    }






    # On place order redirect to pay for order page
    public function process_payment($order_id) {
        $order = new WC_Order($order_id);
        return array(
            'result' => 'success',
            'redirect' => $order->get_checkout_payment_url(true)
        );
    }




    # Show payment widget on the pay for order page
    public function receipt_page($order_id) {
        $order = new WC_Order($order_id);

        # Widget script
        $html   =  '<script>(function(d, s, id) {var js, ojs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return;js = d.createElement(s); js.id = id;js.src = "https://share.olycash.com/en-us/sdk.js";ojs.parentNode.insertBefore(js, ojs);}(document, "script", "olycash-js-sdk"));</script>';
        
        # Widget div
        $html   .=  '<div class="olycash-pay olycash--window '.(!empty($this->widget_background_color) && $this->widget_background_color == 'dark'?'fade-into-window':'').'"
                        data_plugin-key="'.$this->plugin_key.'" 
                        data-ignorefrequency="'.$this->payment_frequency.'"
                        data-cardprocessor="'.$this->default_card_provider.'"
                        data-id="'.$this->olycash_plugin_id.'" >
                        <input type="hidden" id="olycash__category" name="olycash__category" value="156"/>
                        <input type="hidden" id="olycash__total" name="olycash__total" value="'.$order->get_total().'"/>
                        <input type="hidden" id="olycash__currency" name="olycash__currency" value="'.$order->get_currency().'"/>
                        <input type="hidden" id="olycash__third_party_fee_paid_by" name="olycash__third_party_fee_paid_by" value="'.$this->third_party_fee.'">
                        <input type="hidden" id="olycash__post_process" name="olycash__post_process" value="olycashPostProcess"/>
                        <input type="hidden" id="olycash__post_response" name="olycash__post_response" value="olycashPostResponse"/>
                        <input type="hidden" id="olycash__pre_process" name="olycash__pre_process" value="olycashPreProcess">';
        # Close widget
        $html   .=  '</div></div>';

        # Add script for custome js
        if($this->post_response_type && $this->post_response_type == 'js'){
            $html   .=  '<script>function executeCustomCode(){'.$this->post_response_action.'}</script>';
        }

        echo $html;
    }





    # Add css and javascript scripts to the pay for order page
    public function widget_scripts(){

        # Append the shadowbox css file in the checkout page
        wp_enqueue_style( 'checkout', plugins_url( '/trunk/css/widget.css', OLY_FILE_URL ),false,'1.1','all');

        if ( get_query_var( 'order-pay' ) ) {
            $order_key = urldecode( $_REQUEST['key'] );
            $order_id  = absint( get_query_var( 'order-pay' ) );
            $order = new WC_Order($order_id);
            $status = $order->get_status();
            wp_enqueue_style( 'widget-ui', plugins_url( '/trunk/css/widget.css', OLY_FILE_URL ),false,'1.1','all');
            wp_enqueue_script( 'widget-ui', plugins_url( '/trunk/js/widget.js', OLY_FILE_URL ), array ( 'jquery' ), true);
            wp_localize_script( 'widget-ui', 'widget', array(
                'order_id' => $order_id,
                'order_key' => $order_key,
                'url' => $this->get_return_url( $order ),
                'cburl' => get_bloginfo('url')."/wc-api/".$this->id,
                'baseURL' => get_bloginfo('url'),
                'order_status' => $status,
                'response_type' => $this->post_response_type,
                'response_action' => $this->post_response_action
            ) );
        }
    }




    # Add javascript file to the OlyCash admin dashboard
    public function admin_settings_scripts(){
        if($_GET['page'] == 'wc-settings' && (isset($_GET['section']) && $_GET['section'] == $this->id)){
            wp_enqueue_script( 'admin-ui',  plugins_url( '/trunk/js/admin.js', OLY_FILE_URL ), array ( 'jquery' ), true);
            wp_localize_script( 'admin-ui', 'admin', array(
                'admin_email' => get_bloginfo('admin_email'),
                'plugin_id'=>$this->id
            ) );
        }
    }





    # Update order details using a callback function
    public function update_order_details() {
        header( 'HTTP/1.1 200 OK' );
        $order_id = isset($_REQUEST['order_id']) ? $_REQUEST['order_id'] : null;
        $order_key = isset($_REQUEST['order_key']) ? $_REQUEST['order_key'] : null;
        $order_status = isset($_REQUEST['status']) ? $_REQUEST['status'] : null;
        $note = isset($_REQUEST['note']) ? $_REQUEST['note'] : null;
        $order = wc_get_order( $order_id );

        if($order_status == 'completed') {
            $order->payment_complete();
            wc_reduce_stock_levels( $order_id );
        }
        $order->update_status( $order_status );
        $order->add_order_note( $note, 1 );
        
        echo $order;
        die();
    }


}


