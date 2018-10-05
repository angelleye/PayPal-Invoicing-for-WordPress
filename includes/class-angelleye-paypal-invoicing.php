<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    AngellEYE_PayPal_Invoicing
 * @subpackage AngellEYE_PayPal_Invoicing/includes
 * @author     Angell EYE <service@angelleye.com>
 */
class AngellEYE_PayPal_Invoicing {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      AngellEYE_PayPal_Invoicing_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (defined('PLUGIN_NAME_VERSION')) {
            $this->version = PLUGIN_NAME_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'angelleye-paypal-invoicing';
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        include_once( PAYPAL_INVOICE_PLUGIN_DIR . '/angelleye-paypal-invoicing-function.php' );
        add_filter('cron_schedules', array($this, 'angelleye_paypal_invoicing_new_interval_cron_time'));
        add_action('angelleye_paypal_invoicing_sync_event', array($this, 'angelleye_paypal_invoicing_sync_with_paypal'));
        $prefix = is_network_admin() ? 'network_admin_' : '';
        add_filter("{$prefix}plugin_action_links_" . PAYPAL_INVOICE_PLUGIN_BASENAME, array($this, 'angelleye_paypal_invoicing_plugin_action_links'), 10, 4);
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - AngellEYE_PayPal_Invoicing_Loader. Orchestrates the hooks of the plugin.
     * - AngellEYE_PayPal_Invoicing_i18n. Defines internationalization functionality.
     * - AngellEYE_PayPal_Invoicing_Admin. Defines all hooks for the admin area.
     * - AngellEYE_PayPal_Invoicing_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-angelleye-paypal-invoicing-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-angelleye-paypal-invoicing-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-angelleye-paypal-invoicing-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-angelleye-paypal-invoicing-public.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-angelleye-paypal-invoicing-logger.php';

        $this->loader = new AngellEYE_PayPal_Invoicing_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the AngellEYE_PayPal_Invoicing_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new AngellEYE_PayPal_Invoicing_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new AngellEYE_PayPal_Invoicing_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('init', $plugin_admin, 'angelleye_paypal_invoicing_sub_menu_manage_invoices');
        $this->loader->add_action('init', $plugin_admin, 'angelleye_paypal_invoicing_register_post_status', 10);
        $this->loader->add_action('admin_menu', $plugin_admin, 'angelleye_paypal_invoicing_top_menu');
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'angelleye_paypal_invoicing_remove_meta', 10, 2);
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'angelleye_paypal_invoicing_add_meta_box', 99, 2);
        $this->loader->add_action('manage_edit-paypal_invoices_columns', $plugin_admin, 'angelleye_paypal_invoicing_add_paypal_invoices_columns', 10, 2);
        $this->loader->add_action('manage_paypal_invoices_posts_custom_column', $plugin_admin, 'angelleye_paypal_invoicing_render_paypal_invoices_columns', 2);
        $this->loader->add_filter('manage_edit-paypal_invoices_sortable_columns', $plugin_admin, 'angelleye_paypal_invoicing_paypal_invoices_sortable_columns');
        $this->loader->add_action('pre_get_posts', $plugin_admin, 'angelleye_paypal_invoicing_paypal_invoices_column_orderby');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'angelleye_paypal_invoicing_disable_auto_save');
        $this->loader->add_action('save_post_paypal_invoices', $plugin_admin, 'angelleye_paypal_invoicing_create_invoice_hook', 10, 3);
        $this->loader->add_action('admin_notices', $plugin_admin, 'angelleye_paypal_invoicing_display_admin_notice');
        $this->loader->add_filter('post_row_actions', $plugin_admin, 'angelleye_paypal_invoicing_remove_actions_row', 10, 2);
        $this->loader->add_filter('bulk_actions-edit-paypal_invoices', $plugin_admin, 'angelleye_paypal_invoicing_bulk_actions', 10, 2);
        $this->loader->add_filter('handle_bulk_actions-edit-paypal_invoices', $plugin_admin, 'angelleye_paypal_invoicing_handle_bulk_action', 10, 3);
        $this->loader->add_filter('admin_init', $plugin_admin, 'angelleye_paypal_invoicing_handle_post_row_action', 10);
        $this->loader->add_action('parse_request', $plugin_admin, 'angelleye_paypal_invoicing_handle_webhook_request', 0);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new AngellEYE_PayPal_Invoicing_Public($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_filter('woocommerce_payment_gateways', $plugin_public, 'pifw_woocommerce_payment_gateways', 99, 1);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    AngellEYE_PayPal_Invoicing_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    public function angelleye_paypal_invoicing_new_interval_cron_time($interval) {
        $interval['every_ten_minutes'] = array('interval' => 600, 'display' => 'Every 10 Minutes');
        return $interval;
    }

    public function angelleye_paypal_invoicing_add_wp_schedule_event() {
        if (!wp_next_scheduled('angelleye_paypal_invoicing_sync_with_paypal')) {
            wp_schedule_event(time(), 'every_ten_minutes', 'angelleye_paypal_invoicing_sync_event');
        }
    }

    public function angelleye_paypal_invoicing_sync_with_paypal() {
        include_once(PAYPAL_INVOICE_PLUGIN_DIR . '/admin/class-angelleye-paypal-invoicing-request.php');
        $request = new AngellEYE_PayPal_Invoicing_Request(null, null);
        $request->angelleye_paypal_invoicing_sync_invoicing_with_wp();
    }

    public function angelleye_paypal_invoicing_plugin_action_links($actions, $plugin_file, $plugin_data, $context) {
        $custom_actions = array(
            'configure' => sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=apifw_settings'), __('Configure', 'angelleye-paypal-invoicing')),
            'docs' => sprintf('<a href="%s" target="_blank">%s</a>', 'https://www.angelleye.com/category/docs/angelleye-paypal-invoicing-documentation/?utm_source=angelleye-paypal-invoicing&utm_medium=docs_link&utm_campaign=plugin', __('Docs', 'angelleye-paypal-invoicing')),
            'support' => sprintf('<a href="%s" target="_blank">%s</a>', 'https://www.angelleye.com/support/?utm_source=angelleye-paypal-invoicing&utm_medium=support_link&utm_campaign=plugin', __('Support', 'angelleye-paypal-invoicing')),
            'review' => sprintf('<a href="%s" target="_blank">%s</a>', 'https://www.angelleye.com/product/angelleye-paypal-invoicing?utm_source=angelleye-paypal-invoicing&utm_medium=review_link&utm_campaign=plugin', __('Write a Review', 'angelleye-paypal-invoicing')),
        );
        return array_merge($custom_actions, $actions);
    }

}
