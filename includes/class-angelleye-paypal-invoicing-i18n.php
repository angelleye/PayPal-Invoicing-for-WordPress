<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    AngellEYE_PayPal_Invoicing
 * @subpackage AngellEYE_PayPal_Invoicing/includes
 * @author     Angell EYE <service@angelleye.com>
 */
class AngellEYE_PayPal_Invoicing_i18n {

    /**
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {

        load_plugin_textdomain(
                'angelleye-paypal-invoicing', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }

}
