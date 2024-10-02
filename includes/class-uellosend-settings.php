<?php
if (!defined('ABSPATH')) {
    exit;
}

class USFW_Settings {

    public function __construct() {
        add_action('admin_menu', array($this, 'create_settings_page'));
    }

    public function create_settings_page() {
        add_options_page(
            'UelloSend SMS for WooCommerce',
            'UelloSend SMS Settings',
            'manage_options',
            'uellosend-sms-for-woocommerce',
            array($this, 'render_settings_page')
        );
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Save settings when the form is submitted
        if (isset($_POST['submit'])) {

            // Verify nonce to ensure the request is legitimate
            if (isset($_POST['usfw_settings_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['usfw_settings_nonce'])), 'usfw_save_settings')) {
                // Check if all required fields are set
                if (isset($_POST['sender_id']) && isset($_POST['api_url']) && isset($_POST['api_key']) && isset($_POST['admin_phone'])) {
                    // Sanitize and save the settings
                    update_option('usfw_sender_id', sanitize_text_field(wp_unslash($_POST['sender_id'])));
                    update_option('usfw_api_url', esc_url_raw(wp_unslash($_POST['api_url'])));
                    update_option('usfw_api_key', sanitize_text_field(wp_unslash($_POST['api_key'])));
                    update_option('usfw_admin_phone', sanitize_text_field(wp_unslash($_POST['admin_phone'])));
                    echo '<div class="updated"><p>Settings saved.</p></div>';
                }
            } else {
                // If nonce verification fails, show an error message
                echo '<div class="error"><p>Security check failed. Please try again.</p></div>';
            }
        }


        // Retrieve stored settings
        $sender_id = get_option('usfw_sender_id');
        $api_url = get_option('usfw_api_url');
        $api_key = get_option('usfw_api_key');
        $admin_phone = get_option('usfw_admin_phone'); 

        ?>
        <div class="wrap">
            <h1>UelloSend SMS for WooCommerce Settings</h1>

            <img src="<?php echo esc_url(USFW_URL . 'assets/images/uvitech.jpg'); ?>" alt="UelloSend Logo" style="width:200px; height:auto; margin-bottom:20px;"/>

            <div>
                <p>Sign up for account on UelloSend at <a href="https://uellosend.com/register.php">https://uellosend.com/register.php</a> </p>
                <p>Register your SenderID at <a href="https://uellosend.com/register-brand.php">https://uellosend.com/register-brand.php</a> </p>
                <p>Generate API Keys at <a href="https://uellosend.com/user-api.php">https://uellosend.com/user-api.php</a> </p>
                <p>Once you have approved SenderID and credit on your account, you will be able to send messages</p>
                <div>
                <p>Need Help?  <a href="https://wa.me/233543524033">WhatsApp Us</a> </p>
                </div>
            </div>

            <form method="post" action="">
            <?php wp_nonce_field('usfw_save_settings', 'usfw_settings_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">Sender ID</th>
                        <td><input type="text" name="sender_id" value="<?php echo esc_attr($sender_id); ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">API URL</th>
                        <td><input type="url" name="api_url" value="<?php echo esc_attr($api_url); ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">API Key</th>
                        <td><input type="text" name="api_key" value="<?php echo esc_attr($api_key); ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">Admin Phone Number</th>
                        <td><input type="text" name="admin_phone" placeholder="0543524032" value="<?php echo esc_attr($admin_phone); ?>" /></td>
                    </tr>
                </table>
                <?php submit_button('Save Settings'); ?>
            </form>
        </div>
        <?php
    }
}
