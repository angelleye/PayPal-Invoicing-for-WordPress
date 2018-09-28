<?php

/**
 * @wordpress-plugin
 * Plugin Name:       PayPal Invoicing for WordPress
 * Plugin URI:        http://www.angelleye.com/product/angelleye-paypal-invoicing/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Angell EYE
 * Author URI:        http://www.angelleye.com/
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       angelleye-paypal-invoicing
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('PAYPAL_INVOICE_VERSION', '1.0.0');
if (!defined('PAYPAL_INVOICE_PLUGIN_URL')) {
    define('PAYPAL_INVOICE_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('PAYPAL_INVOICE_PLUGIN_DIR')) {
    define('PAYPAL_INVOICE_PLUGIN_DIR', dirname(__FILE__));
}


if (!defined('PAYPAL_INVOICE_LOG_DIR')) {
    $upload_dir = wp_upload_dir( null, false );
    define('PAYPAL_INVOICE_LOG_DIR', $upload_dir['basedir'] . '/angelleye-paypal-invoicing/');
}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-angelleye-paypal-invoicing-activator.php
 */
function activate_angelleye_paypal_invoicing() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-angelleye-paypal-invoicing-activator.php';
    AngellEYE_PayPal_Invoicing_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-angelleye-paypal-invoicing-deactivator.php
 */
function deactivate_angelleye_paypal_invoicing() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-angelleye-paypal-invoicing-deactivator.php';
    AngellEYE_PayPal_Invoicing_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_angelleye_paypal_invoicing');
register_deactivation_hook(__FILE__, 'deactivate_angelleye_paypal_invoicing');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-angelleye-paypal-invoicing.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_angelleye_paypal_invoicing() {

    $plugin = new AngellEYE_PayPal_Invoicing();
    $plugin->run();
}

run_angelleye_paypal_invoicing();
