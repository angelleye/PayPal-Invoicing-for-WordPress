<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    AngellEYE_PayPal_Invoicing
 * @subpackage AngellEYE_PayPal_Invoicing/admin
 * @author     Angell EYE <service@angelleye.com>
 */
class AngellEYE_PayPal_Invoicing_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;
    public $request;
    public $response;
    public $invoices;
    public $invoice;
    public $billing_info;
    public $paypal_invoice_post_status_list;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->apifw_setting = get_option('apifw_setting');
        $woocommerce_pifw_paypal_invoice_settings = get_option('woocommerce_pifw_paypal_invoice_settings');
        $this->enable_paypal_sandbox = isset($woocommerce_pifw_paypal_invoice_settings['enable_paypal_sandbox']) ? $woocommerce_pifw_paypal_invoice_settings['enable_paypal_sandbox'] : '';
        $this->sandbox_paypal_email = isset($woocommerce_pifw_paypal_invoice_settings['sandbox_paypal_email']) ? $woocommerce_pifw_paypal_invoice_settings['sandbox_paypal_email'] : '';
        $this->sandbox_secret = isset($woocommerce_pifw_paypal_invoice_settings['sandbox_secret']) ? $woocommerce_pifw_paypal_invoice_settings['sandbox_secret'] : '';
        $this->sandbox_client_id = isset($woocommerce_pifw_paypal_invoice_settings['sandbox_client_id']) ? $woocommerce_pifw_paypal_invoice_settings['sandbox_client_id'] : '';
        $this->client_id = isset($woocommerce_pifw_paypal_invoice_settings['client_id']) ? $woocommerce_pifw_paypal_invoice_settings['client_id'] : '';
        $this->secret = isset($woocommerce_pifw_paypal_invoice_settings['secret']) ? $woocommerce_pifw_paypal_invoice_settings['secret'] : '';
        $this->paypal_email = isset($woocommerce_pifw_paypal_invoice_settings['paypal_email']) ? $woocommerce_pifw_paypal_invoice_settings['paypal_email'] : '';
        $this->first_name = isset($woocommerce_pifw_paypal_invoice_settings['first_name']) ? $woocommerce_pifw_paypal_invoice_settings['first_name'] : '';
        $this->last_name = isset($woocommerce_pifw_paypal_invoice_settings['last_name']) ? $woocommerce_pifw_paypal_invoice_settings['last_name'] : '';
        $this->compnay_name = isset($woocommerce_pifw_paypal_invoice_settings['compnay_name']) ? $woocommerce_pifw_paypal_invoice_settings['compnay_name'] : '';
        $this->phone_number = isset($woocommerce_pifw_paypal_invoice_settings['phone_number']) ? $woocommerce_pifw_paypal_invoice_settings['phone_number'] : '';
        $this->address_line_1 = isset($woocommerce_pifw_paypal_invoice_settings['address_line_1']) ? $woocommerce_pifw_paypal_invoice_settings['address_line_1'] : '';
        $this->address_line_2 = isset($woocommerce_pifw_paypal_invoice_settings['address_line_2']) ? $woocommerce_pifw_paypal_invoice_settings['address_line_2'] : '';
        $this->city = isset($woocommerce_pifw_paypal_invoice_settings['city']) ? $woocommerce_pifw_paypal_invoice_settings['city'] : '';
        $this->post_code = isset($woocommerce_pifw_paypal_invoice_settings['post_code']) ? $woocommerce_pifw_paypal_invoice_settings['post_code'] : '';
        $this->state = isset($woocommerce_pifw_paypal_invoice_settings['state']) ? $woocommerce_pifw_paypal_invoice_settings['state'] : '';
        $this->country = isset($woocommerce_pifw_paypal_invoice_settings['country']) ? $woocommerce_pifw_paypal_invoice_settings['country'] : '';
        $this->shipping_rate = isset($woocommerce_pifw_paypal_invoice_settings['shipping_rate']) ? $woocommerce_pifw_paypal_invoice_settings['shipping_rate'] : '';
        $this->shipping_amount = isset($woocommerce_pifw_paypal_invoice_settings['shipping_amount']) ? $woocommerce_pifw_paypal_invoice_settings['shipping_amount'] : '';
        $this->tax_rate = isset($woocommerce_pifw_paypal_invoice_settings['tax_rate']) ? $woocommerce_pifw_paypal_invoice_settings['tax_rate'] : '';
        $this->tax_name = isset($woocommerce_pifw_paypal_invoice_settings['tax_name']) ? $woocommerce_pifw_paypal_invoice_settings['tax_name'] : '';
        $this->note_to_recipient = isset($woocommerce_pifw_paypal_invoice_settings['note_to_recipient']) ? $woocommerce_pifw_paypal_invoice_settings['note_to_recipient'] : '';
        $this->terms_and_condition = isset($woocommerce_pifw_paypal_invoice_settings['terms_and_condition']) ? $woocommerce_pifw_paypal_invoice_settings['terms_and_condition'] : '';
        $this->debug_log = isset($woocommerce_pifw_paypal_invoice_settings['debug_log']) ? $woocommerce_pifw_paypal_invoice_settings['debug_log'] : '';
        $this->testmode = ( isset($this->apifw_setting['enable_paypal_sandbox']) && $this->apifw_setting['enable_paypal_sandbox'] == 'on' ) ? true : false;
        if ($this->testmode == true) {
            $this->rest_client_id = ( isset($this->apifw_setting['sandbox_client_id']) && !empty($this->apifw_setting['sandbox_client_id']) ) ? $this->apifw_setting['sandbox_client_id'] : $this->sandbox_client_id;
            $this->rest_secret_id = ( isset($this->apifw_setting['sandbox_secret']) && !empty($this->apifw_setting['sandbox_secret']) ) ? $this->apifw_setting['sandbox_secret'] : $this->sandbox_secret;
            $this->rest_paypal_email = ( isset($this->apifw_setting['sandbox_paypal_email']) && !empty($this->apifw_setting['sandbox_paypal_email']) ) ? $this->apifw_setting['sandbox_paypal_email'] : $this->sandbox_paypal_email;
        } else {
            $this->rest_client_id = ( isset($this->apifw_setting['client_id']) && !empty($this->apifw_setting['client_id']) ) ? $this->apifw_setting['client_id'] : $this->client_id;
            $this->rest_secret_id = ( isset($this->apifw_setting['secret']) && !empty($this->apifw_setting['secret']) ) ? $this->apifw_setting['secret'] : $this->secret;
            $this->rest_paypal_email = ( isset($this->apifw_setting['paypal_email']) && !empty($this->apifw_setting['paypal_email']) ) ? $this->apifw_setting['paypal_email'] : $this->paypal_email;
        }
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_register_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/angelleye-paypal-invoicing-admin.css', array(), $this->version, 'all');
        wp_register_style($this->plugin_name . 'bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), null, 'all');
        wp_register_style('jquery-ui-style', plugin_dir_url(__FILE__) . 'css/jquery-ui/jquery-ui.min.css', array(), $this->version);
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_register_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/angelleye-paypal-invoicing-admin.js', array('jquery', 'jquery-ui-datepicker'), $this->version, false);
        wp_register_script($this->plugin_name . 'bootstrap', plugin_dir_url(__FILE__) . 'js/bootstrap.bundle.min.js', null, null, false);
    }

    public function angelleye_paypal_invoicing_sub_menu_manage_invoices() {
        global $wpdb;
        if (post_type_exists('paypal_invoices')) {
            return;
        }
        do_action('paypal_invoices_for_wordpress_register_post_type');
        register_post_type('paypal_invoices', apply_filters('paypal_invoices_for_wordpress_register_post_type_paypal_invoices', array(
            'labels' => array(
                'name' => __('Manage invoices', 'angelleye-paypal-invoicing'),
                'singular_name' => __('PayPal invoice', 'angelleye-paypal-invoicing'),
                'menu_name' => _x('Manage invoices', 'Manage invoices', 'angelleye-paypal-invoicing'),
                'add_new' => __('Add invoice', 'angelleye-paypal-invoicing'),
                'add_new_item' => __('Add New invoice', 'angelleye-paypal-invoicing'),
                'edit' => __('Edit', 'angelleye-paypal-invoicing'),
                'edit_item' => __('Invoice Details', 'angelleye-paypal-invoicing'),
                'new_item' => __('New invoice', 'angelleye-paypal-invoicing'),
                'view' => __('View PayPal invoice', 'angelleye-paypal-invoicing'),
                'view_item' => __('View PayPal invoice', 'angelleye-paypal-invoicing'),
                'search_items' => __('Search PayPal invoices', 'angelleye-paypal-invoicing'),
                'not_found' => __('No PayPal invoice found', 'angelleye-paypal-invoicing'),
                'not_found_in_trash' => __('No PayPal invoice found in trash', 'angelleye-paypal-invoicing'),
                'parent' => __('Parent PayPal invoice', 'angelleye-paypal-invoicing')
            ),
            'description' => __('This is where you can add new PayPal Invoice to your store.', 'angelleye-paypal-invoicing'),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => 'apifw_manage_invoces',
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'hierarchical' => false, // Hierarchical causes memory issues - WP loads all records!
            'query_var' => false,
            'menu_icon' => PAYPAL_INVOICE_PLUGIN_URL . 'admin/images/angelleye-paypal-invoicing-for-wordpress-icon.png',
            'supports' => array('', ''),
            'has_archive' => false,
            'show_in_nav_menus' => false
                        )
                )
        );
    }

    public function angelleye_paypal_invoicing_top_menu() {
        remove_meta_box('submitdiv', 'paypal_invoices', 'side');
        remove_meta_box('postexcerpt', 'paypal_invoices', 'normal');
        remove_meta_box('trackbacksdiv', 'paypal_invoices', 'normal');
        remove_meta_box('postcustom', 'paypal_invoices', 'normal');
        remove_meta_box('commentstatusdiv', 'paypal_invoices', 'normal');
        remove_meta_box('commentsdiv', 'paypal_invoices', 'normal');
        remove_meta_box('revisionsdiv', 'paypal_invoices', 'normal');
        remove_meta_box('authordiv', 'paypal_invoices', 'normal');
        remove_meta_box('sqpt-meta-tags', 'paypal_invoices', 'normal');
        add_menu_page('PayPal Invoicing', 'PayPal Invoicing', 'manage_options', 'apifw_manage_invoces', null, PAYPAL_INVOICE_PLUGIN_URL . 'admin/images/angelleye-paypal-invoicing-icom.png', '54.6');
        // add_submenu_page('apifw_manage_invoces', 'Manage Invoces', 'Manage Invoces', 'manage_options', 'apifw_manage_invoces', array($this, 'angelleye_paypal_invoicing_manage_invoicing_content'));
        // add_submenu_page('apifw_manage_invoces', 'Create Invoice', 'Create Invoice', 'manage_options', 'apifw_create_invoces', array($this, 'angelleye_paypal_invoicing_create_invoice_content'));
        //add_submenu_page('apifw_manage_invoces', 'Manage Items', 'Manage Items', 'manage_options', 'apifw_manage_items', array($this, 'angelleye_paypal_invoicing_manage_items_content'));
        add_submenu_page('apifw_manage_invoces', 'Settings', 'Settings', 'manage_options', 'apifw_settings', array($this, 'angelleye_paypal_invoicing_settings_content'));
        //add_submenu_page('apifw_manage_invoces', 'Address Book', 'Address Book', 'manage_options', 'apifw_address_book', array($this, 'angelleye_paypal_invoicing_address_book_content'));
        //add_submenu_page('apifw_manage_invoces', 'Business Information Settings', 'Business Information', 'manage_options', 'apifw_business_information', array($this, 'angelleye_paypal_invoicing_business_information_content'));
        //add_submenu_page('apifw_manage_invoces', 'Tax Settings', 'Tax Information', 'manage_options', 'apifw_tax_settings', array($this, 'angelleye_paypal_invoicing_tax_information_content'));
        //add_submenu_page('apifw_manage_invoces', 'Manage Your Templates', 'Templates', 'manage_options', 'apifw_templates', array($this, 'angelleye_paypal_invoicing_templates_content'));
    }

    public function angelleye_paypal_invoicing_add_bootstrap() {
        wp_enqueue_script($this->plugin_name . 'bootstrap');
        wp_enqueue_script($this->plugin_name);
        wp_enqueue_style($this->plugin_name . 'bootstrap');
        wp_enqueue_style($this->plugin_name);
    }

    public function angelleye_paypal_invoicing_manage_invoicing_content() {
        $this->angelleye_paypal_invoicing_add_bootstrap();
        if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
            $this->angelleye_paypal_invoicing_load_rest_api();
            $this->response = $this->request->angelleye_paypal_invoicing_get_all_invoice();
            include_once PAYPAL_INVOICE_PLUGIN_DIR . '/admin/views/html-admin-page-invoice-list.php';
        } else {
            $this->angelleye_paypal_invoicing_print_error();
        }
    }

    public function angelleye_paypal_invoicing_create_invoice_content() {
        global $post;
        wp_enqueue_style('jquery-ui-style');
        $this->angelleye_paypal_invoicing_add_bootstrap();
        if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
            $this->angelleye_paypal_invoicing_load_rest_api();
            $this->response = $this->request->angelleye_paypal_invoicing_get_next_invoice_number();
            if (empty($_GET['action'])) {
                include_once PAYPAL_INVOICE_PLUGIN_DIR . '/admin/views/html-admin-page-create-invoice.php';
            } elseif (!empty($_GET['action']) && $_GET['action'] == 'edit') {
                $invoice_id = get_post_meta($post->ID, 'id', true);
                if (!empty($invoice_id)) {
                    $invoice = $this->request->angelleye_paypal_invoicing_get_invoice_details($invoice_id);
                    $this->request->angelleye_paypal_invoicing_update_paypal_invoice_data($invoice, $post->ID);
                    include_once PAYPAL_INVOICE_PLUGIN_DIR . '/admin/views/html-admin-page-view-invoice.php';
                } else {
                    include_once PAYPAL_INVOICE_PLUGIN_DIR . '/admin/views/html-admin-page-create-invoice.php';
                }
            }
        } else {
            $this->angelleye_paypal_invoicing_print_error();
        }
    }

    public function angelleye_paypal_invoicing_manage_items_content() {
        $this->angelleye_paypal_invoicing_add_bootstrap();
        if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
            
        } else {
            $this->angelleye_paypal_invoicing_print_error();
        }
    }

    public function angelleye_paypal_invoicing_settings_content() {
        $this->angelleye_paypal_invoicing_delete_log_file();
        $this->angelleye_paypal_invoicing_save_setting();
        $this->angelleye_paypal_invoicing_add_bootstrap();
        include_once PAYPAL_INVOICE_PLUGIN_DIR . '/admin/views/html-admin-page-invoice-setting.php';
    }

    public function angelleye_paypal_invoicing_address_book_content() {
        $this->angelleye_paypal_invoicing_add_bootstrap();
        if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
            
        } else {
            $this->angelleye_paypal_invoicing_print_error();
        }
    }

    public function angelleye_paypal_invoicing_business_information_content() {
        $this->angelleye_paypal_invoicing_add_bootstrap();
        if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
            
        } else {
            $this->angelleye_paypal_invoicing_print_error();
        }
    }

    public function angelleye_paypal_invoicing_tax_information_content() {
        $this->angelleye_paypal_invoicing_add_bootstrap();
        if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
            
        } else {
            $this->angelleye_paypal_invoicing_print_error();
        }
    }

    public function angelleye_paypal_invoicing_templates_content() {
        $this->angelleye_paypal_invoicing_add_bootstrap();
        if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
            $this->angelleye_paypal_invoicing_load_rest_api();
            $this->response = $this->request->angelleye_paypal_invoicing_get_all_templates();
            include_once PAYPAL_INVOICE_PLUGIN_DIR . '/admin/views/html-admin-page-template_list.php';
        } else {
            $this->angelleye_paypal_invoicing_print_error();
        }
    }

    public function angelleye_paypal_invoicing_is_api_set() {
        if (!empty($this->rest_client_id) && !empty($this->rest_secret_id) && !empty($this->rest_paypal_email)) {
            return true;
        } else {
            return false;
        }
    }

    public function angelleye_paypal_invoicing_print_error() {
        ?>
        <br>
        <div class="alert alert-danger alert-dismissible fade show mtonerem" role="alert">
            <?php echo wp_kses_post(__('PayPal API credentials is not set up, <a href="?page=apifw_settings" class="alert-link">Click here to set up</a>.', 'angelleye-paypal-invoicing')) . PHP_EOL; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
    }

    public function angelleye_paypal_invoicing_save_setting() {
        $api_setting_field = array();
        if (!empty($_POST['apifw_setting_submit']) && 'save' == $_POST['apifw_setting_submit']) {
            $setting_field_keys = array('sandbox_client_id', 'sandbox_secret', 'client_id', 'secret', 'enable_paypal_sandbox', 'sandbox_paypal_email', 'paypal_email', 'first_name', 'last_name', 'compnay_name', 'phone_number', 'address_line_1', 'address_line_2', 'city', 'post_code', 'state', 'country', 'shipping_rate', 'shipping_amount', 'tax_rate', 'tax_name', 'note_to_recipient', 'terms_and_condition', 'debug_log');
            foreach ($setting_field_keys as $key => $value) {
                if (!empty($_POST[$value])) {
                    $api_setting_field[$value] = pifw_clean($_POST[$value]);
                }
            }
            update_option('apifw_setting', $api_setting_field);
            echo "<br><div class='alert alert-success alert-dismissible' role='alert'>" . __('Your settings have been saved.', 'angelleye-paypal-invoicing') . "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
            </button></div>";
        }
    }

    public function angelleye_paypal_invoicing_load_rest_api() {
        include_once(PAYPAL_INVOICE_PLUGIN_DIR . '/admin/class-angelleye-paypal-invoicing-request.php');
        $this->request = new AngellEYE_PayPal_Invoicing_Request(null, null);
    }

    public function angelleye_paypal_invoicing_date_parsing($date) {
        $string = preg_replace('/[(]+[^*]+/', '', $date);
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        if (!empty($date_format) && !empty($time_format)) {
            $format = $date_format . ' ' . $time_format;
        } else {
            $format = 'Y-m-d H:i:s';
        }
        $current_offset = get_option('gmt_offset');
        $tzstring = get_option('timezone_string');
        $check_zone_info = true;
        if (false !== strpos($tzstring, 'Etc/GMT')) {
            $tzstring = '';
        }
        if (empty($tzstring)) { // Create a UTC+- zone if no timezone string exists
            $check_zone_info = false;
            if (0 == $current_offset)
                $tzstring = 'UTC+0';
            elseif ($current_offset < 0)
                $tzstring = 'UTC' . $current_offset;
            else
                $tzstring = 'UTC+' . $current_offset;
        }
        $allowed_zones = timezone_identifiers_list();
        if (in_array($tzstring, $allowed_zones)) {
            $tz = new DateTimeZone($tzstring);
        } else {
            $tz = new DateTimeZone('UTC');
        }
        $dt = new DateTime($string);
        $dt->setTimezone($tz);
        return $dt->format($format);
    }

    public function angelleye_paypal_invoicing_delete_log_file() {
        if (!empty($_POST['apifw_delete_logs']) && 'Delete Logs' == $_POST['apifw_delete_logs']) {
            try {
                self::delete_logs_before_timestamp();
                echo "<br><div class='alert alert-success alert-dismissible' role='alert'>" . __('Successfully deleted log files.', 'angelleye-paypal-invoicing') . "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
            </button></div>";
            } catch (Exception $ex) {
                
            }
        }
    }

    public static function get_log_files() {
        $files = @scandir(PAYPAL_INVOICE_LOG_DIR);
        $result = array();
        if (!empty($files)) {
            foreach ($files as $key => $value) {
                if (!in_array($value, array('.', '..'), true)) {
                    if (!is_dir($value) && strstr($value, '.log')) {
                        $result[sanitize_title($value)] = $value;
                    }
                }
            }
        }
        return $result;
    }

    public static function delete_logs_before_timestamp() {
        $log_files = self::get_log_files();
        foreach ($log_files as $log_file) {
            @unlink(trailingslashit(PAYPAL_INVOICE_LOG_DIR) . $log_file);
        }
    }

    public function angelleye_paypal_invoicing_register_post_status() {
        global $wpdb;
        $this->paypal_invoice_post_status_list = $this->angelleye_paypal_invoicing_get_paypal_invoice_status();
        if (isset($this->paypal_invoice_post_status_list) && !empty($this->paypal_invoice_post_status_list)) {
            foreach ($this->paypal_invoice_post_status_list as $paypal_invoice_post_status) {
                $paypal_invoice_post_status_display_name = ucfirst(str_replace('_', ' ', $paypal_invoice_post_status));
                register_post_status($paypal_invoice_post_status, array(
                    'label' => _x($paypal_invoice_post_status_display_name, 'PayPal invoice status', 'angelleye-paypal-invoicing'),
                    'public' => ($paypal_invoice_post_status == 'trash') ? false : true,
                    'exclude_from_search' => false,
                    'show_in_admin_all_list' => ($paypal_invoice_post_status == 'trash') ? false : true,
                    'show_in_admin_status_list' => true,
                    'label_count' => _n_noop($paypal_invoice_post_status_display_name . ' <span class="count">(%s)</span>', $paypal_invoice_post_status_display_name . ' <span class="count">(%s)</span>', 'angelleye-paypal-invoicing')
                ));
            }
        }
    }

    public function angelleye_paypal_invoicing_get_paypal_invoice_status() {
        global $wpdb;
        return $wpdb->get_col($wpdb->prepare("SELECT DISTINCT post_status FROM {$wpdb->posts} WHERE post_type = %s AND post_status != %s  ORDER BY post_status", 'paypal_invoices', 'auto-draft'));
    }

    public function angelleye_paypal_invoicing_remove_meta($post_type, $post) {
        global $wp_meta_boxes;
        $screen = get_current_screen();
        if (!$screen = get_current_screen()) {
            return;
        }
        if (!empty($screen->post_type) && $screen->post_type == 'paypal_invoices' && !empty($screen->action) && $screen->action == 'add') {
            unset($wp_meta_boxes[$post_type]);
        }
    }

    public function angelleye_paypal_invoicing_add_meta_box() {
        add_meta_box('angelleye_paypal_invoicing_meta_box', __('Add New invoice', 'angelleye-paypal-invoicing'), array($this, 'angelleye_paypal_invoicing_add_meta_box_add_new_invoice'), 'paypal_invoices', 'normal');
    }

    public function angelleye_paypal_invoicing_add_meta_box_add_new_invoice() {
        $this->angelleye_paypal_invoicing_create_invoice_content();
    }

    public function angelleye_paypal_invoicing_add_paypal_invoices_columns($columns) {
        unset($columns['date']);
        $columns['title'] = __('Invoice ID', 'angelleye-paypal-invoicing');
        $columns['invoice_date'] = _x('Date', 'angelleye-paypal-invoicing');
        $columns['invoice'] = __('Invoice #', 'angelleye-paypal-invoicing');
        $columns['recipient'] = _x('Recipient', 'angelleye-paypal-invoicing');
        $columns['status'] = __('Status', 'angelleye-paypal-invoicing');
        $columns['amount'] = __('Amount', 'angelleye-paypal-invoicing');
        //$columns['action'] = __('Action', 'angelleye-paypal-invoicing');
        return $columns;
    }

    public function angelleye_paypal_invoicing_render_paypal_invoices_columns($column) {
        global $post;
        switch ($column) {
            case 'invoice_date' :
                $invoice = get_post_meta($post->ID, 'invoice_date', true);
                echo date_i18n(get_option('date_format'), strtotime($invoice));
                break;
            case 'invoice' :
                $invoice_number = esc_attr(get_post_meta($post->ID, 'number', true));
                $all_invoice_data = get_post_meta($post->ID, 'all_invoice_data', true);
                if ($payer_view_url = $this->angelleye_paypal_invoicing_get_payer_view($all_invoice_data)) {
                    echo sprintf('<a href="%1$s">%2$s</a>', $payer_view_url, $invoice_number);
                } else {
                    echo $invoice_number;
                }
                break;
            case 'recipient' :
                echo esc_attr(get_post_meta($post->ID, 'email', true));
                break;
            case 'status' :
                $status = get_post_meta($post->ID, 'status', true);
                if (!empty($status)) {
                    $invoice_status_array = pifw_get_invoice_status_name_and_class($status);
                    echo isset($invoice_status_array['lable']) ? $invoice_status_array['lable'] : '';
                }
                break;
            case 'action' :
                $status = get_post_meta($post->ID, 'status', true);
                if (!empty($status)) {
                    $invoice_status_array = pifw_get_invoice_status_name_and_class($status);
                    if (!empty($invoice_status_array['action'])) {
                        foreach ($invoice_status_array['action'] as $key => $value) {
                            ?><select name="pifw_action">
                                <option value="<?php echo $key; ?>"><?php echo esc_attr($value); ?></option>
                            </select> <?php
                        }
                    }
                }
                break;
            case 'amount' :
                $total_amount_value = get_post_meta($post->ID, 'total_amount_value', true);
                $currency = get_post_meta($post->ID, 'currency', true);
                echo pifw_get_currency_symbol($currency) . $total_amount_value . ' ' . $currency;
                break;
        }
    }

    public function angelleye_paypal_invoicing_paypal_invoices_sortable_columns($columns) {
        $custom = array(
            'invoice' => 'number',
            'recipient' => 'email',
            'status' => 'status',
            'amount' => 'total_amount_value',
            'title' => 'ID'
        );
        return wp_parse_args($custom, $columns);
    }

    public function angelleye_paypal_invoicing_paypal_invoices_column_orderby($query) {
        global $wpdb;
        if (is_admin() && isset($_GET['post_type']) && $_GET['post_type'] == 'paypal_invoices' && isset($_GET['orderby']) && $_GET['orderby'] != 'None') {
            $orderby = $query->get('orderby');
            if ('total_amount_value' == $orderby) {
                $query->query_vars['orderby'] = 'meta_value_num';
            } else {
                $query->query_vars['orderby'] = 'meta_value';
            }
            $query->query_vars['meta_key'] = pifw_clean($_GET['orderby']);
            if (isset($query->query_vars['s']) && empty($query->query_vars['s'])) {
                $query->is_search = false;
            }
        } else {
            if (is_admin() && isset($_GET['post_type']) && $_GET['post_type'] == 'paypal_invoices') {
                $query->query_vars['orderby'] = 'ID';
                $query->query_vars['order'] = 'asc';
            }
        }
    }

    public function angelleye_paypal_invoicing_disable_auto_save() {
        if ('paypal_invoices' == get_post_type()) {
            wp_dequeue_script('autosave');
        }
    }

    public function angelleye_paypal_invoicing_create_invoice_hook($post_ID, $post, $update) {
        if ($update == false) {
            return false;
        }
        if (isset($post->post_status) && $post->post_status == 'trash') {
            return false;
        }
        if (empty($_REQUEST['send_invoice']) && empty($_REQUEST['save_invoice'])) {
            return false;
        }
        $is_paypal_invoice_sent = get_post_meta($post_ID, 'is_paypal_invoice_sent', true);
        if (!empty($is_paypal_invoice_sent) && $is_paypal_invoice_sent == 'yes') {
            return false;
        }
        if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
            $this->angelleye_paypal_invoicing_load_rest_api();
            $invoice_id = $this->request->angelleye_paypal_invoicing_create_invoice($post_ID, $post, $update);
            if (!empty($invoice_id) && $invoice_id != false) {
                $invoice = $this->request->angelleye_paypal_invoicing_get_invoice_details($invoice_id);
                $this->request->angelleye_paypal_invoicing_update_paypal_invoice_data($invoice, $post_ID);
                wp_redirect(admin_url('edit.php?post_type=paypal_invoices&message=1024'));
                exit();
            }
        } else {
            $this->angelleye_paypal_invoicing_print_error();
        }
    }

    public function angelleye_paypal_invoicing_display_admin_notice() {
        if (!empty($_GET['message']) && $_GET['message'] == '1024') {
            echo "<div class='notice notice-success is-dismissible'><p>We've sent your invoice.</p></div>";
        }
        if (!empty($_GET['message']) && $_GET['message'] == '1025') {
            echo "<div class='notice notice-success is-dismissible'><p>Your reminder was sent.</p></div>";
        }
        if (!empty($_GET['message']) && $_GET['message'] == '1026') {
            echo "<div class='notice notice-success is-dismissible'><p>Your invoice is canceled.</p></div>";
        }
        if (!empty($_GET['message']) && $_GET['message'] == '1027') {
            echo "<div class='notice notice-success is-dismissible'><p>Your invoice is deleted.</p></div>";
        }
    }

    public function angelleye_paypal_invoicing_get_payer_view($invoice) {
        if (!empty($invoice['links'])) {
            foreach ($invoice['links'] as $key => $link_array) {
                if ($link_array['rel'] == 'payer-view') {
                    return $link_array['href'];
                }
            }
        }
        return false;
    }

    public function angelleye_paypal_invoicing_remove_actions_row($actions, $post) {
        if ($post->post_type == 'paypal_invoices') {
            $all_invoice_data = get_post_meta($post->ID, 'all_invoice_data', true);
            $status = get_post_meta($post->ID, 'status', true);
            unset($actions['inline hide-if-no-js']);
            unset($actions['trash']);
            $actions['view'] = str_replace('Edit', 'View', $actions['edit']);
            unset($actions['edit']);
            if ($payer_view_url = $this->angelleye_paypal_invoicing_get_payer_view($all_invoice_data)) {
                $actions['paypal_invoice_link'] = '<a target="_blank" href="' . $payer_view_url . '">' . __('View PayPal Invoice', 'angelleye-paypal-invoicing') . '</a>';
            }
            if ($status == 'DRAFT') {
                $actions['paypal_invoice_send'] = '<a href="' . add_query_arg(array('post_id' => $post->ID, 'invoice_action' => 'paypal_invoice_send')) . '">' . __('Send Invoice', 'angelleye-paypal-invoicing') . '</a>';
                $actions['paypal_invoice_delete'] = '<a href="' . add_query_arg(array('post_id' => $post->ID, 'invoice_action' => 'paypal_invoice_delete')) . '">' . __('Delete Invoice', 'angelleye-paypal-invoicing') . '</a>';
            }
            if ($status == 'PARTIALLY_PAID' || $status == 'SCHEDULED' || $status == 'SENT') {
                $actions['paypal_invoice_remind'] = '<a href="' . add_query_arg(array('post_id' => $post->ID, 'invoice_action' => 'paypal_invoice_remind')) . '">' . __('Remind Invoice', 'angelleye-paypal-invoicing') . '</a>';
            }
            if ($status == 'SENT') {
                $actions['paypal_invoice_remind'] = '<a href="' . add_query_arg(array('post_id' => $post->ID, 'invoice_action' => 'paypal_invoice_cancel')) . '">' . __('Cancel Invoice', 'angelleye-paypal-invoicing') . '</a>';
            }
        }
        return $actions;
    }

    public function angelleye_paypal_invoicing_bulk_actions($actions) {
        unset($actions);
        $actions['send'] = __('Send Invoice', 'angelleye-paypal-invoicing');
        $actions['remind'] = __('Remind Invoice', 'angelleye-paypal-invoicing');
        $actions['cancel'] = __('Cancel Invoice', 'angelleye-paypal-invoicing');
        $actions['delete'] = __('Delete Invoice', 'angelleye-paypal-invoicing');
        return $actions;
    }

    public function angelleye_paypal_invoicing_handle_bulk_action($redirect_to, $action_name, $post_ids) {
        try {
            if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
                $this->angelleye_paypal_invoicing_load_rest_api();
                if ('send' === $action_name) {
                    foreach ($post_ids as $post_id) {
                        $status = get_post_meta($post_id, 'status', true);
                        if ($status == 'DRAFT') {
                            $invoice_id = get_post_meta($post_id, 'id', true);
                            $this->request->angelleye_paypal_invoicing_send_invoice_from_draft($invoice_id, $post_id);
                            $email = get_post_meta($post_id, 'email', true);
                            $this->add_invoice_note($post_id, sprintf(__('You sent a invoice to %1$s', 'angelleye-paypal-invoicing'), $email), $is_customer_note = 1);
                            $invoice = $this->request->angelleye_paypal_invoicing_get_invoice_details($invoice_id);
                            $this->request->angelleye_paypal_invoicing_update_paypal_invoice_data($invoice, $post_id);
                        }
                    }
                    $redirect_to = add_query_arg('message', 1024, $redirect_to);
                    return $redirect_to;
                } elseif ('remind' === $action_name) {
                    foreach ($post_ids as $post_id) {
                        $status = get_post_meta($post_id, 'status', true);
                        if ($status == 'PARTIALLY_PAID' || $status == 'SCHEDULED' || $status == 'SENT') {
                            $invoice_id = get_post_meta($post_id, 'id', true);
                            $this->request->angelleye_paypal_invoicing_send_invoice_remind($invoice_id);
                            $email = get_post_meta($post_id, 'email', true);
                            $this->add_invoice_note($post_id, sprintf(__('You sent a payment reminder to %1$s', 'angelleye-paypal-invoicing'), $email), $is_customer_note = 1);
                            $invoice = $this->request->angelleye_paypal_invoicing_get_invoice_details($invoice_id);
                            $this->request->angelleye_paypal_invoicing_update_paypal_invoice_data($invoice, $post_id);
                        }
                    }
                    $redirect_to = add_query_arg('message', 1025, $redirect_to);
                    return $redirect_to;
                } elseif ('cancel' === $action_name) {
                    foreach ($post_ids as $post_id) {
                        $status = get_post_meta($post_id, 'status', true);
                        if ($status == 'SENT') {
                            $invoice_id = get_post_meta($post_id, 'id', true);
                            $this->request->angelleye_paypal_invoicing_cancel_invoice($invoice_id);
                            $this->add_invoice_note($post_id, sprintf(__('You canceled this invoice.', 'angelleye-paypal-invoicing')), $is_customer_note = 1);
                            $invoice = $this->request->angelleye_paypal_invoicing_get_invoice_details($invoice_id);
                            $this->request->angelleye_paypal_invoicing_update_paypal_invoice_data($invoice, $post_id);
                        }
                    }
                    $redirect_to = add_query_arg('message', 1026, $redirect_to);
                    return $redirect_to;
                } elseif ('delete' === $action_name) {
                    foreach ($post_ids as $post_id) {
                        $status = get_post_meta($post_id, 'status', true);
                        if ($status == 'DRAFT') {
                            $invoice_id = get_post_meta($post_id, 'id', true);
                            $this->request->angelleye_paypal_invoicing_delete_invoice($invoice_id);
                            wp_delete_post($post_id, true);
                        }
                    }
                    $redirect_to = add_query_arg('message', 1027, $redirect_to);
                    return $redirect_to;
                }
            } else {
                $this->angelleye_paypal_invoicing_print_error();
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function add_invoice_note($post_id, $note, $is_customer_note = 0, $added_by_user = false) {
        if (is_user_logged_in() && $added_by_user) {
            $user = get_user_by('id', get_current_user_id());
            $comment_author = $user->display_name;
            $comment_author_email = $user->user_email;
        } else {
            $comment_author = __('PayPal Invoice', 'angelleye-paypal-invoicing');
            $comment_author_email = strtolower(__('paypal_invoice', 'angelleye-paypal-invoicing')) . '@';
            $comment_author_email .= isset($_SERVER['HTTP_HOST']) ? str_replace('www.', '', sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST']))) : 'noreply.com'; // WPCS: input var ok.
            $comment_author_email = sanitize_email($comment_author_email);
        }
        $commentdata = apply_filters(
                'angelleye_paypal_invoicing_new_order_note_data', array(
            'comment_post_ID' => $post_id,
            'comment_author' => $comment_author,
            'comment_author_email' => $comment_author_email,
            'comment_author_url' => '',
            'comment_content' => $note,
            'comment_agent' => 'PayPal Invoice',
            'comment_type' => 'invoice_note',
            'post_type' => 'paypal_invoices',
            'comment_parent' => 0,
            'comment_approved' => 1,
                ), array(
            'is_customer_note' => $is_customer_note,
                )
        );
        $comment_id = wp_insert_comment($commentdata);
        if ($is_customer_note) {
            add_comment_meta($comment_id, 'is_customer_note', 1);
            do_action(
                    'angelleye_paypal_invoicing_new_customer_note', array(
                'order_id' => $post_id,
                'customer_note' => $commentdata['comment_content'],
                    )
            );
        }
        return $comment_id;
    }

    public function get_invoice_notes($post_id) {
        $notes = array();
        $args = array(
            'post_id' => $post_id,
            'comment_type' => 'invoice_note',
            'post_type' => 'paypal_invoices'
        );
        $comments = get_comments($args);
        foreach ($comments as $comment) {
            if (!get_comment_meta($comment->comment_ID, 'is_customer_note', true)) {
                continue;
            }
            $comment->comment_content = make_clickable($comment->comment_content);
            $notes[] = $comment;
        }
        return $notes;
    }

    public function angelleye_paypal_invoicing_handle_post_row_action() {
        try {
            if (isset($_REQUEST['invoice_action']) && !empty($_REQUEST['invoice_action']) && isset($_REQUEST['post_id']) && !empty($_REQUEST['post_id'])) {
                $action_name = pifw_clean($_REQUEST['invoice_action']);
                $post_id = pifw_clean($_REQUEST['post_id']);
                if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
                    $this->angelleye_paypal_invoicing_load_rest_api();
                    if ('paypal_invoice_send' === $action_name) {
                        $invoice_id = get_post_meta($post_id, 'id', true);
                        $this->request->angelleye_paypal_invoicing_send_invoice_from_draft($invoice_id, $post_id);
                        $email = get_post_meta($post_id, 'email', true);
                        $this->add_invoice_note($post_id, sprintf(__('You sent a invoice to %1$s', 'angelleye-paypal-invoicing'), $email), $is_customer_note = 1);
                        $invoice = $this->request->angelleye_paypal_invoicing_get_invoice_details($invoice_id);
                        $this->request->angelleye_paypal_invoicing_update_paypal_invoice_data($invoice, $post_id);
                        wp_redirect(admin_url('edit.php?post_type=paypal_invoices&message=1024'));
                        exit();
                    } elseif ('paypal_invoice_remind' === $action_name) {
                        $invoice_id = get_post_meta($post_id, 'id', true);
                        $this->request->angelleye_paypal_invoicing_send_invoice_remind($invoice_id);
                        $email = get_post_meta($post_id, 'email', true);
                        $this->add_invoice_note($post_id, sprintf(__('You sent a payment reminder to %1$s', 'angelleye-paypal-invoicing'), $email), $is_customer_note = 1);
                        $invoice = $this->request->angelleye_paypal_invoicing_get_invoice_details($invoice_id);
                        $this->request->angelleye_paypal_invoicing_update_paypal_invoice_data($invoice, $post_id);
                        wp_redirect(admin_url('edit.php?post_type=paypal_invoices&message=1025'));
                        exit();
                    } elseif ('paypal_invoice_cancel' === $action_name) {
                        $invoice_id = get_post_meta($post_id, 'id', true);
                        $this->request->angelleye_paypal_invoicing_cancel_invoice($invoice_id);
                        $this->add_invoice_note($post_id, sprintf(__('You canceled this invoice', 'angelleye-paypal-invoicing')), $is_customer_note = 1);
                        $invoice = $this->request->angelleye_paypal_invoicing_get_invoice_details($invoice_id);
                        $this->request->angelleye_paypal_invoicing_update_paypal_invoice_data($invoice, $post_id);
                        wp_redirect(admin_url('edit.php?post_type=paypal_invoices&message=1026'));
                        exit();
                    } elseif ('paypal_invoice_delete' === $action_name) {
                        $invoice_id = get_post_meta($post_id, 'id', true);
                        $this->request->angelleye_paypal_invoicing_delete_invoice($invoice_id);
                        wp_delete_post($post_id, true);
                        wp_redirect(admin_url('edit.php?post_type=paypal_invoices&message=1027'));
                        exit();
                    }
                } else {
                    $this->angelleye_paypal_invoicing_print_error();
                }
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function angelleye_paypal_invoicing_handle_webhook_request() {
        if (isset($_GET['action']) && $_GET['action'] == 'webhook_handler') {
            if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
                $this->angelleye_paypal_invoicing_load_rest_api();
                $log = new AngellEYE_PayPal_Invoicing_Logger();
                $posted_raw = $this->angelleye_paypal_invoicing_get_raw_data();
                $log->add('paypal_invoice_log', print_r($posted_raw, true));
                $headers = getallheaders();
                $headers = array_change_key_case($headers, CASE_UPPER);
                $post_id = $this->request->angelleye_paypal_invoicing_validate_webhook_event($headers, $posted_raw);
                $posted = json_decode($posted_raw, true);
                if ($post_id != false && !empty($posted['summary'])) {
                    $this->add_invoice_note($post_id, 'Webhook: ' . $posted['summary'], $is_customer_note = 1);
                }
                @ob_clean();
                header('HTTP/1.1 200 OK');
                exit();
            }
        }
    }

    public function angelleye_paypal_invoicing_get_raw_data() {
        if (function_exists('phpversion') && version_compare(phpversion(), '5.6', '>=')) {
            return file_get_contents('php://input');
        }
        global $HTTP_RAW_POST_DATA;
        if (!isset($HTTP_RAW_POST_DATA)) {
            $HTTP_RAW_POST_DATA = file_get_contents('php://input');
        }
        return $HTTP_RAW_POST_DATA;
    }

    public function angelleye_paypal_invoicing_add_order_action($actions) {
        if (!isset($_REQUEST['post'])) {
            return $actions;
        }
        $order = wc_get_order($_REQUEST['post']);
        $old_wc = version_compare(WC_VERSION, '3.0', '<');
        $order_id = $old_wc ? $order->id : $order->get_id();
        /* $payment_method = $old_wc ? $order->payment_method : $order->get_payment_method();
          $paypal_status = $old_wc ? get_post_meta($order_id, '_paypal_status', true) : $order->get_meta('_paypal_status', true);
          if ('ppec_paypal' !== $payment_method || 'pending' !== $paypal_status) {
          //return $actions;
          } */
        $paypal_invoice_id = $old_wc ? get_post_meta($order_id, '_paypal_invoice_id', true) : $order->get_meta('_paypal_invoice_id', true);

        if (!is_array($actions)) {
            $actions = array();
        }
        if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
            if (empty($paypal_invoice_id)) {
                $actions['angelleye_paypal_invoicing_wc_save_paypal_invoice'] = esc_html__('Save PayPal Invoice Draft', 'angelleye-paypal-invoicing');
                $actions['angelleye_paypal_invoicing_wc_send_paypal_invoice'] = esc_html__('Send PayPal Invoice', 'angelleye-paypal-invoicing');
            } else {
                $actions['angelleye_paypal_invoicing_wc_remind_paypal_invoice'] = esc_html__('Send PayPal Invoice Reminder', 'angelleye-paypal-invoicing');
                $actions['angelleye_paypal_invoicing_wc_cancel_paypal_invoice'] = esc_html__('Cancel PayPal Invoice', 'angelleye-paypal-invoicing');
                $actions['angelleye_paypal_invoicing_wc_delete_paypal_invoice'] = esc_html__('Delete PayPal Invoice', 'angelleye-paypal-invoicing');
            }
        }
        return $actions;
    }

    public function angelleye_paypal_invoicing_wc_save_paypal_invoice($order) {
        if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
            $this->angelleye_paypal_invoicing_load_rest_api();
            if (!is_object($order)) {
                $order = wc_get_order($order);
            }
            $order_id = version_compare(WC_VERSION, '3.0', '<') ? $order->id : $order->get_id();
        }

        return true;
    }

    public function angelleye_paypal_invoicing_wc_send_paypal_invoice($order) {
        if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
            $this->angelleye_paypal_invoicing_load_rest_api();
            if (!is_object($order)) {
                $order = wc_get_order($order);
            }
            $order_id = version_compare(WC_VERSION, '3.0', '<') ? $order->id : $order->get_id();
            $invoice_id = $this->request->angelleye_paypal_invoicing_create_invoice_for_wc_order($order);
            if (is_array($invoice_id)) {
                $order->add_order_note($invoice_id['message']);
                return false;
            } else {
                if (!empty($invoice_id) && $invoice_id != false) {
                    update_post_meta($order_id, '_transaction_id', pifw_clean($invoice_id));
                    update_post_meta($order_id, '_payment_method', 'pifw_paypal_invoice');
                    $order->add_order_note(__("We've sent your invoice.", 'angelleye-paypal-invoicing'));
                    update_post_meta($order_id, '_paypal_invoice_id', $invoice_id);
                    $invoice = $this->request->angelleye_paypal_invoicing_get_invoice_details($invoice_id);
                    $paypal_invoice_wp_post_id = $this->request->angelleye_paypal_invoicing_insert_paypal_invoice_data($invoice);
                    update_post_meta($order_id, '_paypal_invoice_wp_post_id', $paypal_invoice_wp_post_id);
                    if ($order->get_total() > 0) {
                        $order->update_status('on-hold', _x('Awaiting payment', 'PayPal Invoice', 'angelleye-paypal-invoicing'));
                    } else {
                        $order->payment_complete();
                    }
                    wc_reduce_stock_levels($order_id);
                }
            }
        }
        return true;
    }

    public function angelleye_paypal_invoicing_wc_remind_paypal_invoice($order) {
        if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
            $this->angelleye_paypal_invoicing_load_rest_api();
            if (!is_object($order)) {
                $order = wc_get_order($order);
            }
            $order_id = version_compare(WC_VERSION, '3.0', '<') ? $order->id : $order->get_id();
        }

        return true;
    }

    public function angelleye_paypal_invoicing_wc_cancel_paypal_invoice($order) {
        if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
            $this->angelleye_paypal_invoicing_load_rest_api();
            if (!is_object($order)) {
                $order = wc_get_order($order);
            }
            $order_id = version_compare(WC_VERSION, '3.0', '<') ? $order->id : $order->get_id();
        }

        return true;
    }

    public function angelleye_paypal_invoicing_wc_delete_paypal_invoice($order) {
        if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
            $this->angelleye_paypal_invoicing_load_rest_api();
            if (!is_object($order)) {
                $order = wc_get_order($order);
            }
            $order_id = version_compare(WC_VERSION, '3.0', '<') ? $order->id : $order->get_id();
        }

        return true;
    }
    
    public function angelleye_paypal_invoicing_wc_display_paypal_invoice_status($order) {
        if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
            if (!is_object($order)) {
                $order = wc_get_order($order);
            }
            $order_id = version_compare(WC_VERSION, '3.0', '<') ? $order->id : $order->get_id();
            $paypal_invoice_wp_post_id = get_post_meta($order_id, '_paypal_invoice_wp_post_id', true);
            $invoice_status = get_post_meta($paypal_invoice_wp_post_id, 'status', true);
            if( !empty($invoice_status) ) {
                echo "<p class='form-field form-field-wide wc-order-status'><strong>PayPal Invoice Status: </strong><label>" . ucfirst(strtolower($invoice_status)) . "</lable></p>";
            }
            
        }
    }

}
