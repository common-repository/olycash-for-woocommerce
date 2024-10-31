<?php

/**
 * Class for doing the following;
 * Create an OlyCash Account
 * Generate the widget
 * Check payment status
 */

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly



class WC_Oly_Processor {



    /**
     * 
     * Function called to generate the plugin
     */
    public function process_plugin($email, $name)
    {
        $oly_account = $this->check_email($email);
        if($oly_account['message'] == 'exists') {

            $plugin_id = $this->generate_plugin($oly_account['account_id'], $name);
            return $plugin_id;

        }
        else if($oly_account['message'] == 'available') {
            $oly_account = $this->create_account($email, $name, $this->get_user_ip());
            if($oly_account['account_id'])
            {
                $plugin_id = $this->generate_plugin($oly_account['account_id'], $name);
                return $plugin_id;
            } 
            else return 'error'; 
        }
        else return 'error';
    }




    /**
     * 
     * Function to check if account already exits
     * 
     */
    public function check_email($email)
    {
        return $this->make_api_request(OLYCASH_BASE_URL, 'account/check_email', [
            'email_address'=>$email
        ], 'POST');
    }




    /**
     * 
     * Function to create an OlyCash Account
     */
    public function create_account($contact, $name, $ip_address){

        # Create New OlyCash Account for User
        $response = $this->make_api_request(OLYPAGES_BASE_URL, 'account/signup', [
            'name' => $name,
            'email_address' => $contact,
            'ip_address' => $ip_address
        ], 'POST');
        
        return $response;
    }








    /**
     * 
     * Function to create OlyCash Widget
     * 
     */
    public function generate_plugin($oly_account_id, $oly_admin_name){
        $response = $this->make_api_request(OLYCASH_BASE_URL, 'plugins/generate', [
            "account_id"    => $oly_account_id,
            "name"          => $oly_admin_name." OlyCash Plugin",
            "type"          => "general",
            "width"         => "max",
            "height"        => "max",
            "return_type"   => "plugin"
        ], 'POST');

        if(!empty($response) || $response['plugin_id']){
            $olycash_plugin_id = $oly_account_id.'_'.$response['plugin_id'];
            # Save plugin id in the database
            update_option('olycash_plugin_id', $olycash_plugin_id );
            # Get the plugin key
            $key = $this->generate_plugin_key($oly_account_id, $response['plugin_id']);
            return $olycash_plugin_id;
        }
        else return 'error';
    }

	


    /**
     * 
     * Function to get and save the plugin widget
     */
    private function generate_plugin_key($account_id, $plugin_id){
        $params     = 'account_id='.$account_id.'&plugin_id='.$plugin_id;
        $response   = wp_remote_get( OLYCASH_BASE_URL.'plugins/details/?'.$params);
        $key        = json_decode(wp_remote_retrieve_body($response), true);

        # Save plugin key 
        if(!empty($key['plugin_key'])){
            update_option('olycash_plugin_key', $key['plugin_key']);
            return $key['plugin_key'];
        } 
        else return 'error';
    }







    /**
     * 
     * Function used to make OlyCash API requests
     */
    private function make_api_request($base_url, $end_point, $parameters, $method)
    {
        $response = wp_remote_retrieve_body(
            wp_remote_post( $base_url.$end_point, [
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'body'    => json_encode($parameters),
            'method' => $method,
            'timeout' => 120000,
            'data_format' => 'body'
        ]));
        return json_decode($response, true);  
    }




    /**
     * Get current user ip
     */
    public function get_user_ip() {
        try {
            return WC_Geolocation::get_ip_address();
        } catch (Exception $ex) {

        }
    }

    
}

