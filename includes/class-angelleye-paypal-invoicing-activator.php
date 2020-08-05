<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    AngellEYE_PayPal_Invoicing
 * @subpackage AngellEYE_PayPal_Invoicing/includes
 * @author     Angell EYE <service@angelleye.com>
 */
class AngellEYE_PayPal_Invoicing_Activator {

    /**
     * @since    1.0.0
     */
    public static function activate($web_services) {
        $apifw_setting = get_option('apifw_setting');
        $sync_paypal_invoice_history_interval = isset($apifw_setting['sync_paypal_invoice_history_interval']) ? $apifw_setting['sync_paypal_invoice_history_interval'] : 'daily';
        self::create_files();
        if (wp_next_scheduled('angelleye_paypal_invoicing_sync_event')) {
            $timestamp = wp_next_scheduled('angelleye_paypal_invoicing_sync_event');
            wp_unschedule_event($timestamp, 'angelleye_paypal_invoicing_sync_event');
        }
        if (!wp_next_scheduled('angelleye_paypal_invoicing_sync_event')) {
            wp_schedule_event(time(), $sync_paypal_invoice_history_interval, 'angelleye_paypal_invoicing_sync_event');
        }
        if($web_services) { 
            delete_option('angelleye_paypal_invoicing_submited_feedback');
            $opt_in_log = get_option('angelleye_send_opt_in_logging_details', 'no');
            if($opt_in_log == 'yes') {
                $log_url = $_SERVER['HTTP_HOST'];
                $log_plugin_id = 10;
                $log_activation_status = 1;
                wp_remote_request('http://www.angelleye.com/web-services/wordpress/update-plugin-status.php?url='.$log_url.'&plugin_id='.$log_plugin_id.'&activation_status='.$log_activation_status);               
            } 
        }
    }

    private static function create_files() {
        $files = array(
            array(
                'base' => ANGELLEYE_PAYPAL_INVOICING_LOG_DIR,
                'file' => '.htaccess',
                'content' => 'deny from all',
            ),
            array(
                'base' => ANGELLEYE_PAYPAL_INVOICING_LOG_DIR,
                'file' => 'index.html',
                'content' => '',
            ),
        );
        foreach ($files as $file) {
            if (wp_mkdir_p($file['base']) && !file_exists(trailingslashit($file['base']) . $file['file'])) {
                $file_handle = @fopen(trailingslashit($file['base']) . $file['file'], 'w');
                if ($file_handle) {
                    fwrite($file_handle, $file['content']);
                    fclose($file_handle);
                }
            }
        }
    }
}
