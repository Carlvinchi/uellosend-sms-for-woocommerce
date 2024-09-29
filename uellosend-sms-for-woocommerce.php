<?php
/*
 * Plugin Name:       UelloSend SMS for WooCommerce
 * Description:       Sends WooCommerce order and account related SMS notifications to customers and admin using UelloSend SMS Gateway, only Ghanaian numbers.
 * Version:           1.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            UviTech
 * Author URI:        https://www.uvitechgh.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       uellosend-sms-for-woocommerce
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Define constants for paths
define('UELLOSEND_SMS_PATH', plugin_dir_path(__FILE__));
define('UELLOSEND_SMS_URL', plugin_dir_url(__FILE__));

// Include the necessary files
require_once UELLOSEND_SMS_PATH . 'includes/class-uellosend-settings.php';
require_once UELLOSEND_SMS_PATH . 'includes/class-uellosend-hooks.php';
require_once UELLOSEND_SMS_PATH . 'includes/class-uellosend-sms.php';

// Initialize the settings page
if (is_admin()) {
    new UelloSend_Settings();
}

// Initialize WooCommerce hooks
new UelloSend_Hooks();

// Add default API URL on plugin activation
function uellosend_activate() {
    if (!get_option('wc_sms_api_url')) {
        update_option('wc_sms_api_url', 'https://uellosend.com/quicksend/');
    }
}
register_activation_hook(__FILE__, 'uellosend_activate');

// Add settings link on the plugins page
function uellosend_plugin_action_links($links) {
    $settings_link = '<a href="options-general.php?page=uellosend-sms-for-woocommerce">' . __('Settings', 'uellosend-sms-for-woocommerce') . '</a>';
    array_unshift($links, $settings_link); // Add to the beginning of the array
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'uellosend_plugin_action_links');
