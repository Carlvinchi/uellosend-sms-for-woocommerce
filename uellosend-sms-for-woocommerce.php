<?php
/*
 * Plugin Name:       UelloSend SMS for WooCommerce
 * Description:       Sends WooCommerce order and account related SMS notifications to customers and admin using UelloSend SMS Gateway, only Ghanaian numbers.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            UviTech
 * Author URI:        https://uvitechgh.com/about-us
 * Plugin URI:        https://github.com/Carlvinchi/uellosend-sms-for-woocommerce
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Requires Plugins:  woocommerce
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Define constants for paths
define('USFW_PATH', plugin_dir_path(__FILE__));
define('USFW_URL', plugin_dir_url(__FILE__));

// Include the necessary files
require_once USFW_PATH . 'includes/class-uellosend-settings.php';
require_once USFW_PATH . 'includes/class-uellosend-hooks.php';
require_once USFW_PATH . 'includes/class-uellosend-sms.php';

// Initialize the settings page
if (is_admin()) {
    new USFW_Settings();
}

// Initialize WooCommerce hooks
new USFW_Hooks();

// Add default API URL on plugin activation
function usfw_activate() {
    if (!get_option('usfw_api_url')) {
        update_option('usfw_api_url', 'https://uellosend.com/quicksend/');
    }
}

register_activation_hook(__FILE__, 'usfw_activate');

// Add settings link on the plugins page
function usfw_action_links($links) {
    $settings_link = '<a href="options-general.php?page=uellosend-sms-for-woocommerce">' . __('Settings', 'uellosend-sms-for-woocommerce') . '</a>';
    array_unshift($links, $settings_link); // Add to the beginning of the array
    return $links;
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'usfw_action_links');


// Add deactivation action
function usfw_deactivate() {

    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'usfw_deactivate');

