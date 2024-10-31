<?php

/**
 * Render the OlyCash Widget settings form
 * 
 * @author OlyCash
 * @copyright OlyCash Inc. 
 * 
 */

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


    $fields = array();

    $fields['enabled'] = array(
        'title'       => 'Enable OlyCash Gateway',
        'label'       => ' ',
        'type'        => 'checkbox',
        'description' => '',
        'default'     => 'no'
    );

    $fields['business_name'] =  array(
        'title'       => 'Business Name',
        'type'        => 'text',
        'description' =>  'This is your official business name',
        'default'     =>  get_bloginfo('name'),
        'placeholder' => 'This is your official business name',
        'required'    => true,
    );

    $fields['business_email'] = array(
        'title'       => 'Email Address',
        'type'        => 'email',
        'validate'    => 'validate-email',
        'description' => 'This is your registered business email contact.',
        'default'     => get_bloginfo('admin_email'),
        'placeholder' => 'This is your registered business email contact',
        'required'    => true,
    );


    // Apply readonly to name and email after widget setup

    if(!empty(get_option( 'olycash_plugin_id' ))){
        $fields['business_name']['custom_attributes'] = array(
            'readonly' => true,
        );
        $fields['business_email']['custom_attributes'] = array(
            'readonly' => true,
        );
    }

    $fields['third_party_fee'] = array(
        'title'       => 'Third Party Fee Paid By',
        'type'        => 'select',
        'default'     => 'payer',
        'options'     => array(
            'payer'   => 'Payer',
            'payee'   => 'Payee'
            ),
        'description' => 'Third party processors like PayPal charge a fee for accepting 
                            payment through OlyCash. Choose who covers this third party fee.',
    );


    $fields['payment_frequency'] = array(
        'title'       => 'Show Payment Frequency',
        'type'        => 'select',
        'default'     => 'show',
        'options'     => array(
            'N'   => 'Show',
            'Y'   => 'Hide'
            ),
        'description' => 'Payment frequency allows payer to pay once or more times such as monthly or annually.',
    );


    $fields['default_card_provider'] = array(
        'title'       => 'Default Card Provider',
        'type'        => 'select',
        'default'     => 'inline',
        'options'     => array(
            'paypal'   => 'PayPal',
            'stripe'   => 'Stripe'
            ),
        'description' => 'Select which card is prefered to handle card payments(PayPal or Stripe).',
    );


    $fields['widget_background_color'] = array(
        'title'       => 'Set Website Color Theme',
        'type'        => 'select',
        'default'     => 'dark',
        'options'     => array(
            'dark'   => 'Dark',
            'white'   => 'Light'
            ),
        'description' => 'Your color theme determines the widget presentation.',
    );



    $fields['widget_background_color'] = array(
        'title'       => 'Set Website Color Theme',
        'type'        => 'select',
        'default'     => 'dark',
        'options'     => array(
            'dark'   => 'Dark',
            'white'   => 'Light'
            ),
        'description' => 'Your color theme determines the widget presentation.',
    );


    $fields['widget_pixel_width'] = array(
        'title'       => 'Widget Maximum Width',
        'type'        => 'number',
        'description' => 'Leave empty if width is flexible. The maximum width is 250 pixels.',
    );


    $fields['post_response_type'] = array(
        'title'       => 'Js Function or Webhook',
        'type'        => 'select',
        'class'       => 'select-response',
        'default'     => 'JS',
        'options'     => array(
            'js'   => 'JS Function',
            'url'   => 'URL'
            ),
        'description' => 'How would you want to handle the payment response. Prepare a JS function or post to a webhook url',
    );


    $fields['post_response_action'] = array(
        'title'       => ' ',
        'type'        => 'textarea',
        'class'       => 'post-response-field',
        'description' => 'JS function or post to webhook url',
    );

    $fields['plugin_id'] = array(
        'type'        => 'hidden',
        'default'     => !empty(get_option( 'olycash_plugin_id' ))?get_option( 'olycash_plugin_id' ):'',
    );

$this->form_fields = $fields;
