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
    }

}
