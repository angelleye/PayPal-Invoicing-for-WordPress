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
    public static function activate() {
        self::create_files();
        self::angelleye_paypal_invoicing_synce_paypal_invoiceing_data_to_wp();
        if (wp_next_scheduled('angelleye_paypal_invoicing_sync_with_paypal')) {
            $timestamp = wp_next_scheduled('angelleye_paypal_invoicing_sync_with_paypal');
            wp_unschedule_event($timestamp, 'angelleye_paypal_invoicing_sync_with_paypal');
        }
        wp_clear_scheduled_hook('angelleye_paypal_invoicing_sync_event');
        if (!wp_next_scheduled('angelleye_paypal_invoicing_sync_with_paypal')) {
            wp_schedule_event(time(), 'every_ten_minutes', 'angelleye_paypal_invoicing_sync_event');
        }
    }

    private static function create_files() {
        $files = array(
            array(
                'base' => PAYPAL_INVOICE_LOG_DIR,
                'file' => '.htaccess',
                'content' => 'deny from all',
            ),
            array(
                'base' => PAYPAL_INVOICE_LOG_DIR,
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

    private static function angelleye_paypal_invoicing_synce_paypal_invoiceing_data_to_wp() {
        try {
            include_once(PAYPAL_INVOICE_PLUGIN_DIR . '/admin/class-angelleye-paypal-invoicing-request.php');
            $request = new AngellEYE_PayPal_Invoicing_Request(null, null);
            $request->angelleye_paypal_invoicing_sync_invoicing_with_wp();
        } catch (Exception $ex) {
            
        }
    }

}
