<?php
if (!defined('ABSPATH')) {
    exit;
}

class USFW_Hooks {

    public function __construct() {
        // WooCommerce hooks
        add_action('woocommerce_checkout_order_processed', array($this, 'send_order_notification'), 10, 1);
        add_action('woocommerce_payment_complete', array($this, 'send_payment_notification'), 10, 1);
        add_action('woocommerce_order_status_changed', array($this, 'send_order_status_change_notification'), 10, 3);
        add_action('woocommerce_order_refunded', array($this, 'send_refund_notification'), 10, 2);
        
        // User account hooks
        add_action('user_register', array($this, 'send_account_creation_notification'), 10, 1);
        add_action('profile_update', array($this, 'send_password_change_notification'), 10, 2);
        add_action('password_reset', array($this, 'send_password_reset_notification'), 10, 2);

        // User login 
        add_action('wp_login', array($this, 'send_login_notification'), 10, 2);
    }

    // 1. Send SMS when an order is placed
    public function send_order_notification($order_id) {
        $order = wc_get_order($order_id);
        $customer_phone = $order->get_billing_phone();
        $admin_phone = get_option('usfw_admin_phone');

        // Message to customer
        $message_to_customer = "Dear ". $order->get_billing_first_name() ." thank you for your order #" . $order->get_order_number() . " at ". site_url()." . We will notify you once it is processed.";
        USFW_SMS::send_sms($customer_phone, $message_to_customer);

        // Message to admin
        $message_to_admin = "A new order #" . $order->get_order_number() . " has been placed at your site ". site_url() ." by ".$order->get_billing_first_name();
        USFW_SMS::send_sms($admin_phone, $message_to_admin);
    }

    // 2. Send SMS when payment is completed
    public function send_payment_notification($order_id) {
        $order = wc_get_order($order_id);
        $customer_phone = $order->get_billing_phone();
        $admin_phone = get_option('usfw_admin_phone');

        // Message to customer
        $message_to_customer = "Dear ". $order->get_billing_first_name() ." your payment for order #" . $order->get_order_number() . " at ". site_url()." has been received, and the order is being processed";
        USFW_SMS::send_sms($customer_phone, $message_to_customer);

        // Message to admin
        $message_to_admin = "Payment received for order #" . $order->get_order_number() . " on your site ". site_url();
        USFW_SMS::send_sms($admin_phone, $message_to_admin);
    }

    // 3. Send SMS when order status changes
    public function send_order_status_change_notification($order_id, $old_status, $new_status) {
        $order = wc_get_order($order_id);
        $customer_phone = $order->get_billing_phone();
    
        // Message to customer
        $message_to_customer = "Dear ". $order->get_billing_first_name() ." your order #" . $order->get_order_number() . " status has changed from " . ucfirst($old_status) . " to " . ucfirst($new_status) . ".";
        USFW_SMS::send_sms($customer_phone, $message_to_customer);

    }

    // 4. Send SMS when a refund is processed
    public function send_refund_notification($order_id) {
        $order = wc_get_order($order_id);
        $customer_phone = $order->get_billing_phone();

        // Message to customer
        $message_to_customer = "Dear ". $order->get_billing_first_name() ." a refund has been processed for your order #" . $order->get_order_number() . " at ". site_url()." .";
        USFW_SMS::send_sms($customer_phone, $message_to_customer);

    }

    // 5. Send SMS when a user creates an account
    public function send_account_creation_notification($user_id) {
        $user = get_userdata($user_id);
        $customer_phone = get_user_meta($user_id, 'billing_phone', true);
        $admin_phone = get_option('usfw_admin_phone');

        if ($customer_phone) {
            // Message to customer
            $message_to_customer = "Welcome " . $user->display_name . "! Your account has been successfully created at ". site_url();
            USFW_SMS::send_sms($customer_phone, $message_to_customer);
        }

        // Message to admin
        $message_to_admin = "A new account has been created by " . $user->display_name . " at ". site_url()." .";
        USFW_SMS::send_sms($admin_phone, $message_to_admin);
    }

    // 6. Send SMS when a password is changed by user or admin
    public function send_password_change_notification($user_id) {
        
        $customer_phone = get_user_meta($user_id, 'billing_phone', true);

        // Message to customer (if phone exists)
        if ($customer_phone) {
            $message_to_customer = "Your account password has been successfully updated at ". site_url();
            USFW_SMS::send_sms($customer_phone, $message_to_customer);
        }

    }

    // 7. Send SMS when a password is reset via "Forgot Password"
    public function send_password_reset_notification($user) {
        $customer_phone = get_user_meta($user->ID, 'billing_phone', true);

        // Message to customer (if phone exists)
        if ($customer_phone) {
            $message_to_customer = "Your password has been reset successfully at ". site_url();
            USFW_SMS::send_sms($customer_phone, $message_to_customer);
        }
    }

    // 8. Send SMS on user login (user or admin)
    public function send_login_notification($user) {
        $customer_phone = get_user_meta($user->ID, 'billing_phone', true);

        // Message to customer (if phone exists)
        if ($customer_phone) {
            $message_to_customer = "You have successfully logged in to your account at ". site_url();
            USFW_SMS::send_sms($customer_phone, $message_to_customer);
        }
    }
}
