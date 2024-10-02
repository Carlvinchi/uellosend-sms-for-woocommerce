<?php
if (!defined('ABSPATH')) {
    exit;
}

class USFW_SMS {

    public static function send_sms($phone_number, $message) {
        $api_url = get_option('usfw_api_url');
        $api_key = get_option('usfw_api_key');
        $sender_id = get_option('usfw_sender_id');

        // Check if the necessary settings are provided
        if (empty($api_url) || empty($api_key) || empty($phone_number)) {
            error_log('SMS Error: Missing API URL, API Key, or phone number.');
            return;
        }
        if (strlen($phone_number) < 10) {
            error_log('SMS Error: Invalid phone number.');
            return;
        }

        $prefix1 = '+233'; 
        $prefix2 = '233';
        $prefix3 = '0';

        $pattern1 = '/^\+233\./';
        $pattern2 = '/^\233\./';

        //format the phone number
        if(strpos($phone_number, $prefix3) === 0){
            $phone_number = $phone_number;
        }
        if(strpos($phone_number, $prefix2) === 0){
            $phone_number = USFW_SMS::replaceCountryCode($phone_number,$pattern2);
        }
        if(strpos($phone_number, $prefix1) === 0){
            $phone_number = USFW_SMS::replaceCountryCode($phone_number,$pattern1);
        }
        
        // Prepare the request body
        $args = array(
            'body' => wp_json_encode(array(
                'sender_id' => $sender_id,
                'recipient' => $phone_number,
                'message' => $message,
                'api_key' => $api_key,
            )),
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'method' => 'POST',
        );

        // Send the request
        $response = wp_remote_post($api_url, $args);

        // Log errors if request fails
        if (is_wp_error($response)) {
            error_log('SMS sending failed for ' . $phone_number . ': ' . $response->get_error_message());
        } else {
            // Decode the response
            $response_body = wp_remote_retrieve_body($response);
            $response_code = wp_remote_retrieve_response_code($response);

            // Log the response status and body for debugging
            if ($response_code === 200) {
                error_log('SMS successfully sent to ' . $phone_number . '. Response: ' . $response_body);
            } else {
                error_log('SMS failed to send to ' . $phone_number . '. Response Code: ' . $response_code . '. Response: ' . $response_body);
            }
        }
    }


    // function to format the number
   public static function replaceCountryCode($phoneNumber, $pattern) {
        // Define the pattern to search for
        

        // Define the replacement string
        $replacement = '0';

        // Replace 0 with 233
        $newPhoneNumber = preg_replace($pattern, $replacement, $phoneNumber);

        // Replace any spaces
        $output = preg_replace('/\s+/', '', $newPhoneNumber);

        return $output;
    }
}
