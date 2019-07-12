<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    AngellEYE_PayPal_Invoicing
 * @subpackage AngellEYE_PayPal_Invoicing/includes
 * @author     Angell EYE <service@angelleye.com>
 */
class AngellEYE_PayPal_Invoicing_Deactivator {

    /**
     * @since    1.0.0
     */
    public static function deactivate() {

        if (wp_next_scheduled('angelleye_paypal_invoicing_sync_with_paypal')) {
            $timestamp = wp_next_scheduled('angelleye_paypal_invoicing_sync_with_paypal');
            wp_unschedule_event($timestamp, 'angelleye_paypal_invoicing_sync_with_paypal');
        }
        wp_clear_scheduled_hook('angelleye_paypal_invoicing_sync_event');
        
        $opt_in_log = get_option('angelleye_send_opt_in_logging_details', 'no');
        $is_submited_feedback = get_option('angelleye_paypal_invoicing_submited_feedback', 'no');
        if($opt_in_log == 'yes') {
            if($is_submited_feedback == 'no') {
                $log_url = $_SERVER['HTTP_HOST'];
                $log_plugin_id = 10;
                $log_activation_status = 0;
                wp_remote_request('http://www.angelleye.com/web-services/wordpress/update-plugin-status.php?url='.$log_url.'&plugin_id='.$log_plugin_id.'&activation_status='.$log_activation_status);
            }
        } 
    }

}
