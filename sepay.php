<?php

/**
 * Ensures that the module init file can't be accessed directly, only within the application.
 */
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: SePay Payment Gateway
Description: SePay Payment Gateway for Perfex CRM
Version: 1.0.0
Requires at least: 2.3.5
*/

define('SEPAY_MODULE_NAME', 'sepay');

hooks()->add_action('admin_init', 'sepay_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(SEPAY_MODULE_NAME, 'sepay_module_activation_hook');

function sepay_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(SEPAY_MODULE_NAME, [SEPAY_MODULE_NAME]);

/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
function sepay_module_init_menu_items()
{
    /**
     * If the logged in user is administrator, add menu item in setup
     */
    if (is_admin()) {
        $CI = &get_instance();
    }
}

/**
 * Register payment gateway
 */
register_payment_gateway('Sepay_gateway', 'sepay');

hooks()->add_action('app_init', 'sepay_update_default_description');

function sepay_update_default_description()
{
    $current_desc = get_option('paymentmethod_sepay_description');
    $instruction = _l('sepay_payment_instructions');
    $new_desc = 'Thanh toán qua SePay. <span style="color:red">' . $instruction . '</span>';

    // Check if empty or matches old default or simple "Thanh toán qua SePay"
    if (empty($current_desc) || trim($current_desc) == 'Payment for Invoice {invoice_number}' || trim($current_desc) == 'Thanh toán qua SePay') {
        update_option('paymentmethod_sepay_description', $new_desc);
    }
}


