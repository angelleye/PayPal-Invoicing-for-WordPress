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
    public $paypal_invoice_post_status_list;
    public $get_access_token_url;

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
        $this->tax_rate = isset($this->apifw_setting['tax_rate']) ? $this->apifw_setting['tax_rate'] : '';
        $this->tax_name = isset($this->apifw_setting['tax_name']) ? $this->apifw_setting['tax_name'] : '';
        $this->item_quantity = isset($this->apifw_setting['item_quantity']) ? $this->apifw_setting['item_quantity'] : '1';
        $this->get_access_token_url = '';
        // Secure Invoice notes.
        add_filter('comments_clauses', array(__CLASS__, 'exclude_invoice_comments'), 10, 1);
        add_filter('comment_feed_where', array(__CLASS__, 'exclude_invoice_comments_from_feed_where'));
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_register_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/angelleye-paypal-invoicing-admin.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . 'ui', plugin_dir_url(__FILE__) . 'css/angelleye-paypal-invoicing-admin-ui.css', array(), $this->version, 'all');
        wp_register_style($this->plugin_name . 'bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), null, 'all');
        wp_register_style('jquery-ui-style', plugin_dir_url(__FILE__) . 'css/jquery-ui/jquery-ui.min.css', array(), $this->version);
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts($hook_suffix) {
        global $post;
        wp_register_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/angelleye-paypal-invoicing-admin.js', array('jquery', 'jquery-ui-datepicker'), $this->version, false);
        wp_enqueue_script($this->plugin_name . 'ui', plugin_dir_url(__FILE__) . 'js/angelleye-paypal-invoicing-admin-ui.js', array('jquery'), $this->version, false);
        wp_register_script($this->plugin_name . 'bootstrap', plugin_dir_url(__FILE__) . 'js/bootstrap.bundle.min.js', null, null, false);
        $cpt = 'shop_order';
        if (in_array($hook_suffix, array('post.php'))) {
            $screen = get_current_screen();
            if (is_object($screen) && $cpt == $screen->post_type) {
                $paypal_invoice_wp_post_id = get_post_meta($post->ID, '_paypal_invoice_wp_post_id', true);
                if (!empty($paypal_invoice_wp_post_id)) {
                    $status = get_post_meta($paypal_invoice_wp_post_id, 'status', true);
                    if (!empty($status)) {
                        if ($status == 'DRAFT') {
                            wp_enqueue_script($this->plugin_name . 'bootstrap');
                            wp_enqueue_script($this->plugin_name);
                            $translation_array = array(
                                'move_trace_confirm_string' => __('Would you like to delete the invoice at PayPal?', 'angelleye-paypal-invoicing'),
                                'invoice_post_id' => $paypal_invoice_wp_post_id,
                                'order_id' => $post->ID
                            );
                            wp_localize_script($this->plugin_name, 'angelleye_paypal_invoicing_js', $translation_array);
                        }
                    }
                }
            }
        }
        if ('plugins.php' === $hook_suffix) {
            wp_enqueue_style('deactivation-modal-paypal-invoicing', plugin_dir_url(__FILE__) . 'css/deactivation-modal.css', null, $this->version);
            wp_enqueue_script('deactivation-modal-paypal-invoicing', plugin_dir_url(__FILE__) . 'js/deactivation-form-modal.js', null, $this->version, true);
            wp_localize_script('deactivation-modal-paypal-invoicing', 'angelleye_ajax_data', array('nonce' => wp_create_nonce('angelleye-ajax')));
        }
    }

    public function angelleye_paypal_invoicing_sub_menu_manage_invoices() {
        global $wpdb;
        if (post_type_exists('paypal_invoices')) {
            return;
        }
        do_action('paypal_invoices_for_wordpress_register_post_type');
        register_post_type('paypal_invoices', apply_filters('paypal_invoices_for_wordpress_register_post_type_paypal_invoices', array(
            'labels' => array(
                'name' => __('Manage Invoices', 'angelleye-paypal-invoicing'),
                'singular_name' => __('PayPal Invoice', 'angelleye-paypal-invoicing'),
                'all_items' => __('Manage Invoices', 'angelleye-paypal-invoicing'),
                'menu_name' => _x('PayPal Invoicing', 'Admin menu name', 'angelleye-paypal-invoicing'),
                'add_new' => __('Create Invoice', 'angelleye-paypal-invoicing'),
                'add_new_item' => __('Add New Invoice', 'angelleye-paypal-invoicing'),
                'edit' => __('Edit', 'angelleye-paypal-invoicing'),
                'edit_item' => __('Invoice Details', 'angelleye-paypal-invoicing'),
                'new_item' => __('New Invoice', 'angelleye-paypal-invoicing'),
                'view' => __('View PayPal Invoice', 'angelleye-paypal-invoicing'),
                'view_item' => __('View PayPal Invoice', 'angelleye-paypal-invoicing'),
                'search_items' => __('Search PayPal Invoices', 'angelleye-paypal-invoicing'),
                'not_found' => __('No PayPal Invoice found', 'angelleye-paypal-invoicing'),
                'not_found_in_trash' => __('No PayPal Invoice found in trash', 'angelleye-paypal-invoicing'),
                'parent' => __('Parent PayPal Invoice', 'angelleye-paypal-invoicing')
            ),
            'description' => __('This is where you can add new PayPal Invoice to your store.', 'angelleye-paypal-invoicing'),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'hierarchical' => false, // Hierarchical causes memory issues - WP loads all records!
            'query_var' => false,
            'menu_icon' => ANGELLEYE_PAYPAL_INVOICING_PLUGIN_URL . 'admin/images/angelleye-paypal-invoicing-icom.png',
            'supports' => array('', ''),
            'has_archive' => false,
            'show_in_nav_menus' => true
                        )
                )
        );

        $user_id = get_current_user_id();
        update_user_meta($user_id, 'screen_layout_paypal_invoices', 1);
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
        //add_menu_page('PayPal Invoicing', 'PayPal Invoicing', 'manage_options', 'apifw_manage_invoces', null, ANGELLEYE_PAYPAL_INVOICING_PLUGIN_URL . 'admin/images/angelleye-paypal-invoicing-icom.png', '54.6');
        // add_submenu_page('apifw_manage_invoces', 'Manage Invoces', 'Manage Invoces', 'manage_options', 'apifw_manage_invoces', array($this, 'angelleye_paypal_invoicing_manage_invoicing_content'));
        //add_submenu_page('apifw_manage_invoces', 'Create Invoice', 'Create Invoice', 'manage_options', 'post-new.php?post_type=paypal_invoices', array($this, 'angelleye_paypal_invoicing_create_invoice_content'));
        //add_submenu_page('apifw_manage_invoces', 'Manage Items', 'Manage Items', 'manage_options', 'apifw_manage_items', array($this, 'angelleye_paypal_invoicing_manage_items_content'));
        add_submenu_page('edit.php?post_type=paypal_invoices', 'Settings', 'Settings', 'manage_options', 'apifw_settings', array($this, 'angelleye_paypal_invoicing_settings_content'));
        //add_submenu_page('apifw_manage_invoces', 'Address Book', 'Address Book', 'manage_options', 'apifw_address_book', array($this, 'angelleye_paypal_invoicing_address_book_content'));
        //add_submenu_page('apifw_manage_invoces', 'Business Information Settings', 'Business Information', 'manage_options', 'apifw_business_information', array($this, 'angelleye_paypal_invoicing_business_information_content'));
        //add_submenu_page('apifw_manage_invoces', 'Tax Settings', 'Tax Information', 'manage_options', 'apifw_tax_settings', array($this, 'angelleye_paypal_invoicing_tax_information_content'));
        //add_submenu_page('apifw_manage_invoces', 'Manage Your Templates', 'Templates', 'manage_options', 'apifw_templates', array($this, 'angelleye_paypal_invoicing_templates_content'));
    }

    public function angelleye_paypal_invoicing_add_bootstrap() {
        wp_enqueue_script($this->plugin_name . 'bootstrap');
        wp_enqueue_script($this->plugin_name);
        $translation_array = array(
            'tax_name' => $this->tax_name,
            'tax_rate' => $this->tax_rate,
            'is_ssl' => is_ssl() ? 'yes' : 'no',
            'choose_image' => __('Choose Image', 'angelleye-paypal-invoicing'),
            'item_qty' => $this->item_quantity,
            'dateFormat' => angelleye_date_format_php_to_js(get_option('date_format'))
        );
        wp_localize_script($this->plugin_name, 'angelleye_paypal_invoicing_js', $translation_array);
        wp_enqueue_style($this->plugin_name . 'bootstrap');
        wp_enqueue_style($this->plugin_name);
    }

    public function angelleye_paypal_invoicing_create_invoice_content() {
        global $post;
        wp_enqueue_style('jquery-ui-style');
        $this->angelleye_paypal_invoicing_add_bootstrap();
        $this->angelleye_paypal_invoicing_load_rest_api();
        if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
            if (empty($_GET['action'])) {
                $this->response = $this->request->angelleye_paypal_invoicing_get_next_invoice_number();
                include_once ANGELLEYE_PAYPAL_INVOICING_PLUGIN_DIR . '/admin/views/html-admin-page-create-invoice.php';
            } elseif (!empty($_GET['action']) && $_GET['action'] == 'edit') {
                $invoice_id = get_post_meta($post->ID, 'id', true);
                if (!empty($invoice_id)) {
                    $invoice = $this->request->angelleye_paypal_invoicing_get_invoice_details($invoice_id);
                    $this->request->angelleye_paypal_invoicing_update_paypal_invoice_data($invoice, $post->ID);
                    include_once ANGELLEYE_PAYPAL_INVOICING_PLUGIN_DIR . '/admin/views/html-admin-page-view-invoice.php';
                } else {
                    include_once ANGELLEYE_PAYPAL_INVOICING_PLUGIN_DIR . '/admin/views/html-admin-page-create-invoice.php';
                }
            }
        } else {
            $this->angelleye_paypal_invoicing_print_error();
        }
    }

    public function angelleye_paypal_invoicing_manage_items_content() {
        $this->angelleye_paypal_invoicing_add_bootstrap();
        if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
            
        } else {
            $this->angelleye_paypal_invoicing_print_error();
        }
    }

    public function angelleye_paypal_invoicing_settings_content() {
        $this->angelleye_paypal_invoicing_delete_log_file();
        $this->angelleye_paypal_invoicing_save_setting();
        $this->angelleye_paypal_invoicing_add_bootstrap();
        include_once ANGELLEYE_PAYPAL_INVOICING_PLUGIN_DIR . '/admin/views/html-admin-page-invoice-setting.php';
    }

    public function angelleye_paypal_invoicing_address_book_content() {
        $this->angelleye_paypal_invoicing_add_bootstrap();
        if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
            
        } else {
            $this->angelleye_paypal_invoicing_print_error();
        }
    }

    public function angelleye_paypal_invoicing_business_information_content() {
        $this->angelleye_paypal_invoicing_add_bootstrap();
        if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
            
        } else {
            $this->angelleye_paypal_invoicing_print_error();
        }
    }

    public function angelleye_paypal_invoicing_tax_information_content() {
        $this->angelleye_paypal_invoicing_add_bootstrap();
        if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
            
        } else {
            $this->angelleye_paypal_invoicing_print_error();
        }
    }

    public function angelleye_paypal_invoicing_templates_content() {
        $this->angelleye_paypal_invoicing_add_bootstrap();
        $this->angelleye_paypal_invoicing_load_rest_api();
        if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
            $this->response = $this->request->angelleye_paypal_invoicing_get_all_templates();
            include_once ANGELLEYE_PAYPAL_INVOICING_PLUGIN_DIR . '/admin/views/html-admin-page-template_list.php';
        } else {
            $this->angelleye_paypal_invoicing_print_error();
        }
    }

    public function angelleye_paypal_invoicing_print_error() {
        ?>
        <br>
        <div class="alert alert-danger alert-dismissible fade show mtonerem" role="alert">
            <?php echo wp_kses_post(sprintf(__('PayPal API credentials is not set up, <a href="%s" class="alert-link">Click here to set up</a>.', 'angelleye-paypal-invoicing'), admin_url('admin.php?page=apifw_settings'))) . PHP_EOL; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
    }

    public function angelleye_paypal_invoicing_save_setting() {
        $api_setting_field = array();
        if (!empty($_POST['apifw_setting_submit']) && 'save' == $_POST['apifw_setting_submit']) {
            $this->angelleye_paypal_invoicing_load_rest_api();
            if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
                try {
                    $this->request->angelleye_paypal_invoicing_delete_web_hook_request();
                } catch (Exception $ex) {
                    
                }
            }
            delete_transient('apifw_sandbox_access_token');
            delete_transient('apifw_live_access_token');
            delete_option('webhook_id');
            $setting_field_keys = array('sandbox_client_id', 'sandbox_secret', 'client_id', 'secret', 'enable_paypal_sandbox', 'paypal_email', 'first_name', 'last_name', 'compnay_name', 'phone_number', 'address_line_1', 'address_line_2', 'city', 'post_code', 'state', 'country', 'shipping_rate', 'shipping_amount', 'tax_rate', 'tax_name', 'note_to_recipient', 'terms_and_condition', 'debug_log', 'apifw_company_logo', 'sandbox_paypal_email', 'item_quantity', 'enable_sync_paypal_invoice_history', 'sync_paypal_invoice_history_interval');
            foreach ($setting_field_keys as $key => $value) {
                if (!empty($_POST[$value])) {
                    $api_setting_field[$value] = pifw_clean($_POST[$value]);
                }
            }
            update_option('apifw_setting', $api_setting_field);
            AngellEYE_PayPal_Invoicing_Activator::activate($web_services = false);
            echo "<br><div class='alert alert-success alert-dismissible' role='alert'>" . __('Your settings have been saved.', 'angelleye-paypal-invoicing') . "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
            </button></div>";
        }
    }

    public function angelleye_paypal_invoicing_load_rest_api() {
        include_once(ANGELLEYE_PAYPAL_INVOICING_PLUGIN_DIR . '/admin/class-angelleye-paypal-invoicing-request.php');
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
                error_log(print_r($ex->getMessage(), true));
            }
        }
    }

    public static function get_log_files() {
        $files = @scandir(ANGELLEYE_PAYPAL_INVOICING_LOG_DIR);
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
            @unlink(trailingslashit(ANGELLEYE_PAYPAL_INVOICING_LOG_DIR) . $log_file);
        }
    }

    public function angelleye_paypal_invoicing_register_post_status() {
        global $wpdb;
        if (isset($_GET['angelleye_display_agree_disgree_opt_in_logging_paypal_invoicing']) && $_GET['angelleye_display_agree_disgree_opt_in_logging_paypal_invoicing'] == 'yes') {
            update_option('angelleye_send_opt_in_logging_details', 'yes');
            $log_url = $_SERVER['HTTP_HOST'];
            $log_plugin_id = 1;
            $log_activation_status = 1;
            wp_remote_request('http://www.angelleye.com/web-services/wordpress/update-plugin-status.php?url=' . $log_url . '&plugin_id=' . $log_plugin_id . '&activation_status=' . $log_activation_status);
            $set_ignore_tag_url = remove_query_arg('angelleye_display_agree_disgree_opt_in_logging_paypal_invoicing');
            wp_redirect($set_ignore_tag_url);
        } elseif (isset($_GET['angelleye_display_agree_disgree_opt_in_logging_paypal_invoicing']) && $_GET['angelleye_display_agree_disgree_opt_in_logging_paypal_invoicing'] == 'no') {
            update_option('angelleye_send_opt_in_logging_details', 'no');
            $set_ignore_tag_url = remove_query_arg('angelleye_display_agree_disgree_opt_in_logging_paypal_invoicing');
            wp_redirect($set_ignore_tag_url);
        }
        $this->paypal_invoice_post_status_list = $this->angelleye_paypal_invoicing_get_paypal_invoice_status();
        if (isset($this->paypal_invoice_post_status_list) && !empty($this->paypal_invoice_post_status_list)) {
            foreach ($this->paypal_invoice_post_status_list as $paypal_invoice_post_status) {
                $paypal_invoice_post_status_display_name = ucfirst(str_replace('_', ' ', $paypal_invoice_post_status));
                register_post_status($paypal_invoice_post_status, array(
                    'label' => _x($paypal_invoice_post_status_display_name, 'PayPal Invoice status', 'angelleye-paypal-invoicing'),
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
        add_meta_box('angelleye_paypal_invoicing_meta_box', __('Add New Invoice', 'angelleye-paypal-invoicing'), array($this, 'angelleye_paypal_invoicing_add_meta_box_add_new_invoice'), 'paypal_invoices', 'normal');
    }

    public function angelleye_paypal_invoicing_add_meta_box_add_new_invoice() {
        $this->angelleye_paypal_invoicing_create_invoice_content();
    }

    public function angelleye_paypal_invoicing_add_paypal_invoices_columns($columns) {
        unset($columns['date']);
        $columns['title'] = __('Invoice #', 'angelleye-paypal-invoicing');
        $columns['invoice_date'] = _x('Date', 'angelleye-paypal-invoicing');
        $columns['recipient'] = _x('Recipient', 'angelleye-paypal-invoicing');
        $columns['status'] = __('Status', 'angelleye-paypal-invoicing');
        $columns['amount'] = __('Amount', 'angelleye-paypal-invoicing');
        return $columns;
    }

    public function angelleye_paypal_invoicing_render_paypal_invoices_columns($column) {
        global $post;
        switch ($column) {
            case 'invoice_date' :
                $invoice = get_post_meta($post->ID, 'wp_invoice_date', true);
                echo date_i18n(get_option('date_format'), strtotime($invoice));
                break;
            case 'recipient' :
                echo esc_attr(get_post_meta($post->ID, 'email', true));
                break;
            case 'status' :
                $status = get_post_meta($post->ID, 'status', true);
                if (!empty($status)) {
                    $invoice_status_array = pifw_get_invoice_status_name_and_class($status);
                    echo isset($invoice_status_array['label']) ? $invoice_status_array['label'] : '';
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
            'title' => 'ID',
            'invoice_date' => 'wp_invoice_date'
        );
        return wp_parse_args($custom, $columns);
    }

    public function angelleye_paypal_invoicing_paypal_invoices_column_orderby($query) {
        global $wpdb;
        if (is_admin() && isset($_GET['post_type']) && $_GET['post_type'] == 'paypal_invoices' && isset($_GET['orderby']) && $_GET['orderby'] != 'None') {
            $orderby = $query->get('orderby');
            if ('total_amount_value' == $orderby) {
                $query->query_vars['orderby'] = 'meta_value_num';
            } elseif ('invoice_date' == $orderby) {
                $query->query_vars['orderby'] = 'meta_value_num date';
            } else {
                $query->query_vars['orderby'] = 'meta_value';
            }
            $query->query_vars['meta_key'] = pifw_clean($_GET['orderby']);
        } else {
            if (is_admin() && isset($_GET['post_type']) && $_GET['post_type'] == 'paypal_invoices') {
                $query->query_vars['orderby'] = 'ID';
                $query->query_vars['order'] = 'DESC';
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
        $this->angelleye_paypal_invoicing_load_rest_api();
        if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
            $invoice_id = $this->request->angelleye_paypal_invoicing_create_invoice($post_ID, $post, $update);
            if (!empty($invoice_id) && !is_array($invoice_id)) {
                $invoice = $this->request->angelleye_paypal_invoicing_get_invoice_details($invoice_id);
                $this->request->angelleye_paypal_invoicing_update_paypal_invoice_data($invoice, $post_ID);
                wp_redirect(admin_url('edit.php?post_type=paypal_invoices&message=1028'));
                exit();
            } else {
                if (!empty($invoice_id['message'])) {
                    set_transient('angelleye_paypal_invoicing_error', $invoice_id['message']);
                }
                wp_delete_post($post_ID, true);
                wp_redirect(admin_url('edit.php?post_type=paypal_invoices&message=1029'));
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
            echo "<div class='notice notice-success is-dismissible'><p>Your reminder is sent.</p></div>";
        }
        if (!empty($_GET['message']) && $_GET['message'] == '1026') {
            echo "<div class='notice notice-success is-dismissible'><p>Your invoice is canceled.</p></div>";
        }
        if (!empty($_GET['message']) && $_GET['message'] == '1027') {
            echo "<div class='notice notice-success is-dismissible'><p>Your invoice is deleted.</p></div>";
        }
        if (!empty($_GET['message']) && $_GET['message'] == '1028') {
            echo "<div class='notice notice-success is-dismissible'><p>Your invoice is created.</p></div>";
        }
        if (!empty($_GET['message']) && $_GET['message'] == '1029') {
            $angelleye_paypal_invoicing_error = get_transient('angelleye_paypal_invoicing_error');
            if ($angelleye_paypal_invoicing_error == false) {
                echo "<div class='notice notice-success is-dismissible'><p>" . __('Invoice not created', 'angelleye-paypal-invoicing') . "</p></div>";
            } else {
                delete_transient('angelleye_paypal_invoicing_error');
                echo "<div class='notice notice-error is-dismissible'><p>" . $angelleye_paypal_invoicing_error . "</p></div>";
            }
        }
        if (!empty($_GET['message']) && $_GET['message'] == '1030') {
            echo "<div class='notice notice-success is-dismissible'><p>Payment for invoice is recorded.</p></div>";
        }
        if (!empty($_GET['message']) && $_GET['message'] == '1031') {
            echo "<div class='notice notice-error is-dismissible'><p>" . __('Payment for invoice is not recorded', 'angelleye-paypal-invoicing') . "</p></div>";
        }
        if (!empty($_GET['message']) && $_GET['message'] == '1032') {
            echo "<div class='notice notice-success is-dismissible'><p>Refund for invoice is recorded.</p></div>";
        }
        if (!empty($_GET['message']) && $_GET['message'] == '1033') {
            echo "<div class='notice notice-error is-dismissible'><p>" . __('Refund for invoice is not recorded', 'angelleye-paypal-invoicing') . "</p></div>";
        }
        $opt_in_log = get_option('angelleye_display_agree_disgree_opt_in_logging_paypal_invoicing', 'yes');
        $angelleye_send_opt_in_logging_details = get_option('angelleye_send_opt_in_logging_details', '');
        if ($opt_in_log == 'yes' && empty($angelleye_send_opt_in_logging_details)) {
            echo '<div class="notice notice-success angelleye-notice" style="display:none;" id="angelleye_send_opt_in_logging_details">'
            . '<div class="angelleye-notice-logo-original"><span></span></div>'
            . '<div class="angelleye-notice-message">'
            . '<h3>PayPal Invoicing for WordPress</h3>'
            . '<div class="angelleye-notice-message-inner">' . sprintf(__('We work directly with PayPal to improve your experience as a seller as well as your buyer\'s experience. May we log some basic details about your site (eg. URL) for future improvement purposes? It would be a big help, thanks!.', 'angelleye-paypal-invoicing'))
            . '</div></div>'
            . '<div class="angelleye-notice-cta">'
            . '<a href="' . add_query_arg('angelleye_display_agree_disgree_opt_in_logging_paypal_invoicing', 'yes') . '" class="button button-primary">' . __('Sure, I\'ll help!', 'angelleye-paypal-invoicing') . '</a>&nbsp;&nbsp;'
            . '<a href="' . add_query_arg('angelleye_display_agree_disgree_opt_in_logging_paypal_invoicing', 'no') . '" class="button">' . __('No thanks.', 'angelleye-paypal-invoicing') . '</a>'
            . '</div>'
            . '</div>';
        }
        if (isset($_GET['angelleye_display_agree_disgree_opt_in_logging_paypal_invoicing']) && $_GET['angelleye_display_agree_disgree_opt_in_logging_paypal_invoicing'] == 'yes') {
            update_option('angelleye_send_opt_in_logging_details', 'yes');
            $log_url = $_SERVER['HTTP_HOST'];
            $log_plugin_id = 1;
            $log_activation_status = 1;
            wp_remote_request('http://www.angelleye.com/web-services/wordpress/update-plugin-status.php?url=' . $log_url . '&plugin_id=' . $log_plugin_id . '&activation_status=' . $log_activation_status);
            $set_ignore_tag_url = remove_query_arg('angelleye_display_agree_disgree_opt_in_logging_paypal_invoicing');
            wp_redirect($set_ignore_tag_url);
        } elseif (isset($_GET['angelleye_display_agree_disgree_opt_in_logging_paypal_invoicing']) && $_GET['angelleye_display_agree_disgree_opt_in_logging_paypal_invoicing'] == 'no') {
            update_option('angelleye_send_opt_in_logging_details', 'no');
            $set_ignore_tag_url = remove_query_arg('angelleye_display_agree_disgree_opt_in_logging_paypal_invoicing');
            wp_redirect($set_ignore_tag_url);
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
                $actions['paypal_invoice_remind'] = '<a href="' . add_query_arg(array('post_id' => $post->ID, 'invoice_action' => 'paypal_invoice_remind')) . '">' . __('Send Invoice Reminder', 'angelleye-paypal-invoicing') . '</a>';
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
        $actions['remind'] = __('Send Invoice Reminder', 'angelleye-paypal-invoicing');
        $actions['cancel'] = __('Cancel Invoice', 'angelleye-paypal-invoicing');
        $actions['delete'] = __('Delete Invoice', 'angelleye-paypal-invoicing');
        return $actions;
    }

    public function angelleye_paypal_invoicing_handle_bulk_action($redirect_to, $action_name, $post_ids) {
        try {
            $this->angelleye_paypal_invoicing_load_rest_api();
            if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
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
            error_log(print_r($ex->getMessage(), true));
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
            'post_type' => 'paypal_invoices',
        );
        remove_filter('comments_clauses', array('AngellEYE_PayPal_Invoicing_Admin', 'exclude_invoice_comments'), 10, 1);
        $comments = get_comments($args);
        add_filter('comments_clauses', array('AngellEYE_PayPal_Invoicing_Admin', 'exclude_invoice_comments'), 10, 1);
        foreach ($comments as $comment) {
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
                $this->angelleye_paypal_invoicing_load_rest_api();
                if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
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
            error_log(print_r($ex->getMessage(), true));
        }
    }

    public function angelleye_paypal_invoicing_handle_webhook_request() {
        if (isset($_GET['action']) && $_GET['action'] == 'webhook_handler') {
            $this->angelleye_paypal_invoicing_load_rest_api();
            if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
                $log = new AngellEYE_PayPal_Invoicing_Logger();
                $posted_raw = $this->angelleye_paypal_invoicing_get_raw_data();
                if (empty($posted_raw)) {
                    return false;
                }
                webhook_log($posted_raw);
                $headers = $this->getallheaders_value();
                $headers = array_change_key_case($headers, CASE_UPPER);
                $post_id = $this->request->angelleye_paypal_invoicing_validate_webhook_event($headers, $posted_raw);
                $posted = json_decode($posted_raw, true);
                if ($post_id != false && !empty($posted['summary'])) {
                    if ($posted['event_type'] == 'INVOICING.INVOICE.CANCELLED') {
                        $this->add_invoice_note($post_id, 'Webhook: ' . $posted['summary'], $is_customer_note = 1);
                    } elseif ($posted['event_type'] == 'INVOICING.INVOICE.CREATED') {
                        $invoice = $posted['resource']['invoice'];
                        $amount = $invoice['total_amount'];
                        $this->add_invoice_note($post_id, sprintf(__(' You created a %s invoice.', 'angelleye-paypal-invoicing'), pifw_get_currency_symbol($amount['currency']) . $amount['value'] . ' ' . $amount['currency']), $is_customer_note = 1);
                    } elseif ($posted['event_type'] == 'INVOICING.INVOICE.PAID') {
                        $invoice = $posted['resource']['invoice'];
                        $billing_info = isset($invoice['billing_info']) ? $invoice['billing_info'] : array();
                        $amount = $invoice['total_amount'];
                        $email = isset($billing_info[0]['email']) ? $billing_info[0]['email'] : 'Customer';
                        do_action('angelleye_paypal_invoice_response_data', $invoice, array(), '10', ($this->request->testmode == true) ? true : false, false, 'paypal_invoice');
                        if (isset($invoice['payments'][0]['transaction_id']) && !empty($invoice['payments'][0]['transaction_id'])) {
                            if ($this->request->testmode == true) {
                                $transaction_details_url = "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_history-details-from-hub&id=" . $invoice['payments'][0]['transaction_id'];
                            } else {
                                $transaction_details_url = "https://www.paypal.com/cgi-bin/webscr?cmd=_history-details-from-hub&id=" . $invoice['payments'][0]['transaction_id'];
                            }
                            $this->add_invoice_note($post_id, sprintf(__(' %s made a %s payment. <a href="%s">View details</a>', 'angelleye-paypal-invoicing'), $email, pifw_get_currency_symbol($amount['currency']) . $amount['value'] . ' ' . $amount['currency'], $transaction_details_url), $is_customer_note = 1);
                        } else {
                            $this->add_invoice_note($post_id, 'Webhook: ' . $posted['summary'], $is_customer_note = 1);
                        }
                    } elseif ($posted['event_type'] == 'INVOICING.INVOICE.REFUNDED') {
                        $this->add_invoice_note($post_id, 'Webhook: ' . $posted['summary'], $is_customer_note = 1);
                    } else {
                        $this->add_invoice_note($post_id, 'Webhook: ' . $posted['summary'], $is_customer_note = 1);
                    }
                }
            }
            @ob_clean();
            header('HTTP/1.1 200 OK');
            exit();
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
        $paypal_invoice_id = $old_wc ? get_post_meta($order_id, '_paypal_invoice_id', true) : $order->get_meta('_paypal_invoice_id', true);
        if (!is_array($actions)) {
            $actions = array();
        }
        $this->angelleye_paypal_invoicing_load_rest_api();
        if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
            if (empty($paypal_invoice_id)) {
                $actions['angelleye_paypal_invoicing_wc_save_paypal_invoice'] = esc_html__('Save PayPal Invoice Draft', 'angelleye-paypal-invoicing');
                $actions['angelleye_paypal_invoicing_wc_send_paypal_invoice'] = esc_html__('Send PayPal Invoice', 'angelleye-paypal-invoicing');
            } else {
                $paypal_invoice_wp_post_id = get_post_meta($order_id, '_paypal_invoice_wp_post_id', true);
                $status = get_post_meta($paypal_invoice_wp_post_id, 'status', true);
                if (!empty($status)) {
                    if ($status == 'PARTIALLY_PAID' || $status == 'SCHEDULED' || $status == 'SENT') {
                        $actions['angelleye_paypal_invoicing_wc_remind_paypal_invoice'] = esc_html__('Send PayPal Invoice Reminder', 'angelleye-paypal-invoicing');
                    }
                    if ($status == 'SENT') {
                        $actions['angelleye_paypal_invoicing_wc_cancel_paypal_invoice'] = esc_html__('Cancel PayPal Invoice', 'angelleye-paypal-invoicing');
                    }
                    if ($status == 'DRAFT') {
                        $actions['angelleye_paypal_invoicing_wc_send_paypal_invoice'] = esc_html__('Send PayPal Invoice', 'angelleye-paypal-invoicing');
                        $actions['angelleye_paypal_invoicing_wc_delete_paypal_invoice'] = esc_html__('Delete PayPal Invoice', 'angelleye-paypal-invoicing');
                    }
                }
            }
        }
        return $actions;
    }

    public function angelleye_paypal_invoicing_wc_save_paypal_invoice($order) {
        $this->angelleye_paypal_invoicing_load_rest_api();
        if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
            if (!is_object($order)) {
                $order = wc_get_order($order);
            }
            $order_id = version_compare(WC_VERSION, '3.0', '<') ? $order->id : $order->get_id();
            $invoice_id = $this->request->angelleye_paypal_invoicing_create_invoice_for_wc_order($order, false);
            if (is_array($invoice_id)) {
                $order->add_order_note($invoice_id['message']);
                return false;
            } else {
                if (!empty($invoice_id) && $invoice_id != false) {
                    update_post_meta($order_id, '_payment_method', 'pifw_paypal_invoice');
                    $order->add_order_note(__("Your invoice is created.", 'angelleye-paypal-invoicing'));
                    update_post_meta($order_id, '_paypal_invoice_id', $invoice_id);
                    $invoice = $this->request->angelleye_paypal_invoicing_get_invoice_details($invoice_id);
                    $paypal_invoice_wp_post_id = $this->request->angelleye_paypal_invoicing_insert_paypal_invoice_data($invoice);
                    update_post_meta($order_id, '_paypal_invoice_wp_post_id', $paypal_invoice_wp_post_id);
                    update_post_meta($paypal_invoice_wp_post_id, '_order_id', $order_id);
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

    public function angelleye_paypal_invoicing_wc_send_paypal_invoice($order) {
        $this->angelleye_paypal_invoicing_load_rest_api();
        if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
            if (!is_object($order)) {
                $order = wc_get_order($order);
            }
            $order_id = version_compare(WC_VERSION, '3.0', '<') ? $order->id : $order->get_id();
            $paypal_invoice_wp_post_id = get_post_meta($order_id, '_paypal_invoice_wp_post_id', true);
            if (!empty($paypal_invoice_wp_post_id)) {
                $invoice_id = get_post_meta($paypal_invoice_wp_post_id, 'id', true);
            } else {
                $invoice_id = '';
            }
            if (!empty($invoice_id)) {
                $this->request->angelleye_paypal_invoicing_send_invoice_from_draft($invoice_id, $paypal_invoice_wp_post_id);
                $order->add_order_note(__("We've sent your invoice.", 'angelleye-paypal-invoicing'));
            } else {
                $invoice_id = $this->request->angelleye_paypal_invoicing_create_invoice_for_wc_order($order, true);
                if (is_array($invoice_id)) {
                    $order->add_order_note($invoice_id['message']);
                    return false;
                } else {
                    if (!empty($invoice_id) && $invoice_id != false) {
                        update_post_meta($order_id, '_payment_method', 'pifw_paypal_invoice');
                        $order->add_order_note(__("We've sent your invoice.", 'angelleye-paypal-invoicing'));
                        update_post_meta($order_id, '_paypal_invoice_id', $invoice_id);
                        $invoice = $this->request->angelleye_paypal_invoicing_get_invoice_details($invoice_id);
                        $paypal_invoice_wp_post_id = $this->request->angelleye_paypal_invoicing_insert_paypal_invoice_data($invoice);
                        update_post_meta($order_id, '_paypal_invoice_wp_post_id', $paypal_invoice_wp_post_id);
                        update_post_meta($paypal_invoice_wp_post_id, '_order_id', $order_id);
                        if ($order->get_total() > 0) {
                            $order->update_status('on-hold', _x('Awaiting payment', 'PayPal Invoice', 'angelleye-paypal-invoicing'));
                        } else {
                            $order->payment_complete();
                        }
                        wc_reduce_stock_levels($order_id);
                    }
                }
            }
        }
        return true;
    }

    public function angelleye_paypal_invoicing_wc_remind_paypal_invoice($order) {
        $this->angelleye_paypal_invoicing_load_rest_api();
        if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
            if (!is_object($order)) {
                $order = wc_get_order($order);
            }
            $order_id = version_compare(WC_VERSION, '3.0', '<') ? $order->id : $order->get_id();
            $paypal_invoice_wp_post_id = get_post_meta($order_id, '_paypal_invoice_wp_post_id', true);
            if (!empty($paypal_invoice_wp_post_id)) {
                $invoice_id = get_post_meta($paypal_invoice_wp_post_id, 'id', true);
                if (!empty($invoice_id)) {
                    $this->request->angelleye_paypal_invoicing_send_invoice_remind($invoice_id);
                    $order->add_order_note(__('Your reminder is sent.', 'angelleye-paypal-invoicing'));
                }
            }
        }
        return true;
    }

    public function angelleye_paypal_invoicing_wc_cancel_paypal_invoice($order) {
        $this->angelleye_paypal_invoicing_load_rest_api();
        if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
            if (!is_object($order)) {
                $order = wc_get_order($order);
            }
            $order_id = version_compare(WC_VERSION, '3.0', '<') ? $order->id : $order->get_id();
            $paypal_invoice_wp_post_id = get_post_meta($order_id, '_paypal_invoice_wp_post_id', true);
            if (!empty($paypal_invoice_wp_post_id)) {
                $invoice_id = get_post_meta($paypal_invoice_wp_post_id, 'id', true);
                if (!empty($invoice_id)) {
                    $this->request->angelleye_paypal_invoicing_cancel_invoice($invoice_id);
                    $invoice = $this->request->angelleye_paypal_invoicing_get_invoice_details($invoice_id);
                    $this->request->angelleye_paypal_invoicing_update_paypal_invoice_data($invoice, $paypal_invoice_wp_post_id);
                    $order->add_order_note(__('You canceled this invoice.', 'angelleye-paypal-invoicing'));
                    $order->update_status('cancelled');
                }
            }
        }
        return true;
    }

    public function angelleye_paypal_invoicing_wc_delete_paypal_invoice($order) {
        $this->angelleye_paypal_invoicing_load_rest_api();
        if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
            if (!is_object($order)) {
                $order = wc_get_order($order);
            }
            $order_id = version_compare(WC_VERSION, '3.0', '<') ? $order->id : $order->get_id();
            $paypal_invoice_wp_post_id = get_post_meta($order_id, '_paypal_invoice_wp_post_id', true);
            if (!empty($paypal_invoice_wp_post_id)) {
                $invoice_id = get_post_meta($paypal_invoice_wp_post_id, 'id', true);
                if (!empty($invoice_id)) {
                    $this->request->angelleye_paypal_invoicing_delete_invoice($invoice_id);
                    wp_delete_post($paypal_invoice_wp_post_id, true);
                    delete_post_meta($order_id, '_transaction_id');
                    delete_post_meta($order_id, '_payment_method');
                    delete_post_meta($order_id, '_paypal_invoice_id');
                    delete_post_meta($order_id, '_paypal_invoice_wp_post_id');
                    $order->add_order_note(__('Your invoice is deleted.', 'angelleye-paypal-invoicing'));
                }
            }
        }
        return true;
    }

    public function angelleye_paypal_invoicing_wc_display_paypal_invoice_status($order) {
        $this->angelleye_paypal_invoicing_load_rest_api();
        if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
            if (!is_object($order)) {
                $order = wc_get_order($order);
            }
            $order_id = version_compare(WC_VERSION, '3.0', '<') ? $order->id : $order->get_id();
            $paypal_invoice_wp_post_id = get_post_meta($order_id, '_paypal_invoice_wp_post_id', true);
            $invoice_status = get_post_meta($paypal_invoice_wp_post_id, 'status', true);
            if (!empty($invoice_status)) {
                echo "<p class='form-field form-field-wide wc-order-status'><strong>PayPal Invoice Status: </strong><b>" . ucfirst(strtolower($invoice_status)) . "</b></p>";
                if( !empty($paypal_invoice_wp_post_id) ) {
                    $url = '<a target="_blank" href="' . esc_url( add_query_arg( array( 'post' => $paypal_invoice_wp_post_id, 'action' => 'edit' ), admin_url( 'post.php' ) ) ) . '">' . esc_html( 'View PayPal Invoice' ) . '</a>';
                    echo '<p class="form-field form-field-wide wc-order-status"><strong>View PayPal Invoice: </strong>'.$url.'</p>';
                }
            }
        }
    }

    public function angelleye_paypal_invoicing_wc_delete_paypal_invoice_ajax() {
        $invoice_post_id = pifw_clean($_POST['invoice_post_id']);
        $order_id = pifw_clean($_POST['order_id']);
        $this->angelleye_paypal_invoicing_wc_delete_paypal_invoice($order_id);
    }

    public function angelleye_paypal_invoicing_add_custom_query_var($public_query_vars) {
        $public_query_vars[] = 'invoices_search';
        return $public_query_vars;
    }

    public function angelleye_paypal_invoicing_search_label($query) {
        global $pagenow, $typenow;
        if ('edit.php' !== $pagenow || 'paypal_invoices' !== $typenow || !get_query_var('invoices_search') || !isset($_GET['s'])) {
            return $query;
        }
        return pifw_clean(wp_unslash(urldecode($_GET['s'])));
    }

    public function angelleye_paypal_invoicing_search_custom_fields($wp) {
        global $pagenow;
        if ('edit.php' !== $pagenow || empty($wp->query_vars['s']) || 'paypal_invoices' !== $wp->query_vars['post_type'] || !isset($_GET['s'])) {
            return;
        }
        $post_ids = $this->angelleye_paypal_invoicing_serch_invoice(pifw_clean(wp_unslash(urldecode($_GET['s']))));
        if (!empty($post_ids)) {
            unset($wp->query_vars['s']);
            $wp->query_vars['invoices_search'] = true;
            $wp->query_vars['post__in'] = array_merge($post_ids, array(0));
        }
    }

    public function angelleye_paypal_invoicing_serch_invoice($s) {
        global $wpdb, $wp;
        $search_fields = array(
            'id',
            'wp_invoice_date',
            'currency',
            'email',
            'number',
            'invoice_date',
            'status',
            'total_amount_value',
            'currency'
        );
        if (empty($_GET['post_status']) || 'all' == $_GET['post_status']) {
            $post_id = $wpdb->get_col(
                    $wpdb->prepare("
                SELECT
                DISTINCT pt.ID
                FROM {$wpdb->posts} pt
                INNER JOIN {$wpdb->postmeta} pmt ON pt.ID = pmt.post_id
		WHERE
                pt.post_type = 'paypal_invoices' AND
                pmt.meta_value LIKE %s AND pmt.meta_key IN ('" . implode("','", array_map('esc_sql', $search_fields)) . "')", '%' . $wpdb->esc_like(pifw_clean($s)) . '%'
            ));
        } else {
            $post_id = $wpdb->get_col(
                    $wpdb->prepare("
                SELECT
                DISTINCT pt.ID
                FROM {$wpdb->posts} pt
                INNER JOIN {$wpdb->postmeta} pmt ON pt.ID = pmt.post_id
		WHERE
                pt.post_type = 'paypal_invoices' AND
                pt.post_status = '%s' AND
                pmt.meta_value LIKE %s AND pmt.meta_key IN ('" . implode("','", array_map('esc_sql', $search_fields)) . "')", pifw_clean($_GET['post_status']), '%' . $wpdb->esc_like(pifw_clean($s)) . '%'
            ));
        }
        return $post_id;
    }

    public function angelleye_paypal_invoice_update_user_info($access_token) {
        $this->angelleye_paypal_invoicing_load_rest_api();
        $result_data = $this->request->angelleye_get_user_info_using_access_token($access_token);
        if (isset($result_data['result']) && $result_data['result'] == 'success') {
            $user_data = json_decode($result_data['user_data'], true);
            $apifw_setting = get_option('apifw_setting', false);
            if ($apifw_setting == false) {
                $apifw_setting = array();
            }
            if (!empty($user_data['email'])) {
                if ($_GET['action'] == 'lipp_paypal_sandbox_connect') {
                    $apifw_setting['sandbox_paypal_email'] = $user_data['email'];
                } else {
                    $apifw_setting['paypal_email'] = $user_data['email'];
                }
            }
            if (!empty($user_data['name'])) {
                $full_name = explode(" ", $user_data['name']);
                $apifw_setting['first_name'] = isset($full_name[0]) ? $full_name[0] : '';
                $apifw_setting['last_name'] = isset($full_name[1]) ? $full_name[1] : '';
            }
            if (!empty($user_data['phone_number'])) {
                $apifw_setting['phone_number'] = $user_data['phone_number'];
            }
            if (!empty($user_data['address'])) {
                $apifw_setting['address_line_1'] = isset($user_data['address']['street_address']) ? $user_data['address']['street_address'] : '';
                $apifw_setting['city'] = isset($user_data['address']['locality']) ? $user_data['address']['locality'] : '';
                $apifw_setting['state'] = isset($user_data['address']['region']) ? $user_data['address']['region'] : '';
                $apifw_setting['post_code'] = isset($user_data['address']['postal_code']) ? $user_data['address']['postal_code'] : '';
                $apifw_setting['country'] = isset($user_data['address']['country']) ? $user_data['address']['country'] : '';
            }
            update_option('apifw_setting', $apifw_setting);
        }
    }

    public function angelleye_update_order_status($post_id, $invoice, $request_array) {
        $this->angelleye_paypal_invoicing_load_rest_api();
        $order_id = get_post_meta($post_id, '_order_id', true);
        if (!empty($order_id)) {
            try {
                $order = wc_get_order($order_id);
                if($order) {
                    if ($invoice['status'] == 'PAID' || 'MARKED_AS_PAID' == $invoice['status']) {
                        if (isset($invoice['payments'][0]['transaction_id']) && !empty($invoice['payments'][0]['transaction_id'])) {
                            if (!$order->has_status(array('processing', 'completed'))) {
                                $order->payment_complete($invoice['payments'][0]['transaction_id']);
                            }
                        } else {
                            if (!$order->has_status(array('processing', 'completed'))) {
                                $order->payment_complete();
                            }
                        }
                        wc_reduce_stock_levels($order_id);
                        $billing_info = isset($invoice['billing_info']) ? $invoice['billing_info'] : array();
                        $amount = isset($invoice['total_amount']) ? $invoice['total_amount'] : '';
                        $email = isset($billing_info[0]['email']) ? $billing_info[0]['email'] : 'Customer';
                        if (isset($invoice['payments'][0]['transaction_id']) && !empty($invoice['payments'][0]['transaction_id'])) {
                            if ($this->request->testmode == true) {
                                $transaction_details_url = "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_history-details-from-hub&id=" . $invoice['payments'][0]['transaction_id'];
                            } else {
                                $transaction_details_url = "https://www.paypal.com/cgi-bin/webscr?cmd=_history-details-from-hub&id=" . $invoice['payments'][0]['transaction_id'];
                            }
                            if( !empty($amount)) {
                                $order->add_order_note(sprintf(__(' %s made a %s payment. <a href="%s">View details</a>', 'angelleye-paypal-invoicing'), $email, pifw_get_currency_symbol($amount['currency']) . $amount['value'] . ' ' . $amount['currency'], $transaction_details_url));
                            }
                        }
                        $order->add_order_note('PayPal Invoice Paid');
                    } else if ($invoice['status'] == 'CANCELLED') {
                        $order->update_status('cancelled');
                        $order->add_order_note('PayPal Invoice Cancelled');
                    } else if ('MARKED_AS_REFUNDED' == $invoice['status'] || 'REFUNDED' == $invoice['status']) {
                        $order->update_status('refunded');
                        $order->add_order_note('PayPal Invoice Refunded');
                    } else if ('PARTIALLY_PAID' == $invoice['status']) {
                        $order->update_status('wc-partial-payment');
                        $order->add_order_note('PayPal Invoice Partially Paid');
                    } 
                    if( !empty($request_array['summary'])) {
                        $order->add_order_note($request_array['summary']);
                    }
                }
            } catch (Exception $ex) {
                error_log(print_r($ex->getMessage(), true));
            }
        }
    }

    public function angelleye_log_errors() {
        $GLOBALS['wpdb']->query('COMMIT;');
    }

    public function angelleye_paypal_invoicing_add_deactivation_form() {
        $current_screen = get_current_screen();
        if ('plugins' !== $current_screen->id && 'plugins-network' !== $current_screen->id) {
            return;
        }
        include_once ( ANGELLEYE_PAYPAL_INVOICING_PLUGIN_DIR . '/admin/views/deactivation-form.php');
    }

    public function angelleye_handle_plugin_deactivation_request() {
        $log_url = pifw_clean($_SERVER['HTTP_HOST']);
        $log_plugin_id = 10;
        $web_services_url = 'http://www.angelleye.com/web-services/wordpress/update-plugin-status.php';
        $request_url = add_query_arg(array(
            'url' => $log_url,
            'plugin_id' => $log_plugin_id,
            'activation_status' => 0,
            'reason' => pifw_clean($_POST['reason']),
            'reason_details' => pifw_clean($_POST['reason_details']),
                ), $web_services_url);
        $response = wp_remote_request($request_url);
        update_option('angelleye_paypal_invoicing_submited_feedback', 'yes');
        if (is_wp_error($response)) {
            wp_send_json(wp_remote_retrieve_body($response));
        } else {
            wp_send_json(wp_remote_retrieve_body($response));
        }
    }

    public function angelleye_paypal_invoicing_add_web_hooks() {
        $webhook_id = get_option('webhook_id', false);
        if ($webhook_id == false) {
            AngellEYE_PayPal_Invoicing_Activator::activate($web_services = false);
        }
    }

    public function angelleye_paypal_invoicing_display_push_notification() {
        global $current_user;
        $user_id = $current_user->ID;
        if (false === ( $response = get_transient('angelleye_paypal_invoicing_push_notification_result') )) {
            $response = $this->angelleye_get_push_notifications();
            if (is_object($response)) {
                set_transient('angelleye_paypal_invoicing_push_notification_result', $response, 12 * HOUR_IN_SECONDS);
            }
        }
        if (is_object($response)) {
            foreach ($response->data as $key => $response_data) {
                if (!get_user_meta($user_id, $response_data->id)) {
                    $this->angelleye_display_push_notification($response_data);
                }
            }
        }
    }

    public function angelleye_get_push_notifications() {
        $args = array(
            'plugin_name' => 'angelleye-paypal-invoicing',
        );
        $api_url = PAYPAL_FOR_WOOCOMMERCE_PUSH_NOTIFICATION_WEB_URL . '?Wordpress_Plugin_Notification_Sender';
        $api_url .= '&action=angelleye_get_plugin_notification';
        $request = wp_remote_post($api_url, array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array('user-agent' => 'AngellEYE'),
            'body' => $args,
            'cookies' => array(),
            'sslverify' => false
        ));
        if (is_wp_error($request) or wp_remote_retrieve_response_code($request) != 200) {
            return false;
        }
        if ($request != '') {
            $response = json_decode(wp_remote_retrieve_body($request));
        } else {
            $response = false;
        }
        return $response;
    }

    public function angelleye_display_push_notification($response_data) {
        echo '<div class="notice notice-success angelleye-notice" style="display:none;" id="' . $response_data->id . '">'
        . '<div class="angelleye-notice-logo-push"><span> <img src="' . $response_data->ans_company_logo . '"> </span></div>'
        . '<div class="angelleye-notice-message">'
        . '<h3>' . $response_data->ans_message_title . '</h3>'
        . '<div class="angelleye-notice-message-inner">'
        . '<p>' . $response_data->ans_message_description . '</p>'
        . '<div class="angelleye-notice-action"><a target="_blank" href="' . $response_data->ans_button_url . '" class="button button-primary">' . $response_data->ans_button_label . '</a></div>'
        . '</div>'
        . '</div>'
        . '<div class="angelleye-notice-cta">'
        . '<button class="angelleye-notice-dismiss angelleye-dismiss-welcome" data-msg="' . $response_data->id . '">Dismiss</button>'
        . '</div>'
        . '</div>';
    }

    public function angelleye_dismiss_notice() {
        global $current_user;
        $user_id = $current_user->ID;
        if (!empty($_POST['action']) && $_POST['action'] == 'angelleye_dismiss_notice') {
            add_user_meta($user_id, wc_clean($_POST['data']), 'true', true);
            wp_send_json_success();
        }
    }

    public function angelleye_paypal_invoicing_record_payment() {
        global $post;
        $this->angelleye_paypal_invoicing_load_rest_api();
        if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
            $record_payment_data = array();
            $record_payment_data['method'] = $_POST['payment_method'];
            $record_payment_data['payment_date'] = pifw_get_paypal_invoice_date_format($_POST['payment_date']); //date('Y-m-d\TH:i:s\Z',$_POST['payment_date']);
            $record_payment_data['amount'] = array('currency_code' => 'USD', 'value' => $_POST['payment_amount']);
            $record_payment_data['note'] = $_POST['payment_note'];
            $invoice_id = $_POST['invoice_id'];
            $return = $this->request->angelleye_paypal_invoice_record_payment($invoice_id, $record_payment_data);
            if ($return == true) {
                wp_send_json_success(admin_url('edit.php?post_type=paypal_invoices&message=1030'));
            } else {
                wp_send_json_error(admin_url('edit.php?post_type=paypal_invoices&message=1031'));
            }
        }
    }

    public function angelleye_paypal_invoicing_record_refund() {
        $this->angelleye_paypal_invoicing_load_rest_api();
        if ($this->request->angelleye_paypal_invoicing_is_api_set() == true) {
            $record_refund_data = array();
            $record_refund_data['method'] = $_POST['refund_method'];
            $record_refund_data['payment_date'] = pifw_get_paypal_invoice_date_format($_POST['refund_date']); //date('Y-m-d\TH:i:s\Z',$_POST['refund_date']);
            $record_refund_data['amount'] = array('currency_code' => 'USD', 'value' => $_POST['refund_amount']);
            $record_refund_data['note'] = $_POST['refund_note'];
            $invoice_id = $_POST['invoice_id'];
            $return = $this->request->angelleye_paypal_invoice_record_refund($invoice_id, $record_refund_data);
            if ($return == true) {
                wp_send_json_success(admin_url('edit.php?post_type=paypal_invoices&message=1032'));
            } else {
                wp_send_json_error(admin_url('edit.php?post_type=paypal_invoices&message=1033'));
            }
        }
    }

    public function getallheaders_value() {
        if (!function_exists('getallheaders')) {
            return $this->getallheaders_custome();
        } else {
            return getallheaders();
        }
    }

    public function getallheaders_custome() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    public function angelleye_paypal_invoicing_update_token() {
        if (isset($_GET['refresh_token']) && !empty($_GET['refresh_token']) && isset($_GET['action']) && ($_GET['action'] == 'lipp_paypal_sandbox_connect' || $_GET['action'] == 'lipp_paypal_live_connect')) {
            $apifw_setting = get_option('apifw_setting', false);
            if ($apifw_setting == false) {
                $apifw_setting = array();
            }
            if ($_GET['action'] == 'lipp_paypal_sandbox_connect') {
                $this->get_access_token_url = add_query_arg(array('rest_action' => 'get_access_token', 'mode' => 'SANDBOX'), PAYPAL_INVOICE_PLUGIN_SANDBOX_API_URL);
                update_option('apifw_sandbox_refresh_token', $_GET['refresh_token']);
                $apifw_setting['enable_paypal_sandbox'] = 'on';
            } elseif ($_GET['action'] == 'lipp_paypal_live_connect') {
                $this->get_access_token_url = add_query_arg(array('rest_action' => 'get_access_token', 'mode' => 'LIVE'), PAYPAL_INVOICE_PLUGIN_LIVE_API_URL);
                update_option('apifw_live_refresh_token', $_GET['refresh_token']);
                $apifw_setting['enable_paypal_sandbox'] = '';
            }
            update_option('apifw_setting', $apifw_setting);
            $response = wp_remote_post($this->get_access_token_url, array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => array('refresh_token' => $_GET['refresh_token']),
                'cookies' => array()
                    )
            );
            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                error_log(print_r($error_message, true));
                echo "Something went wrong: $error_message";
                exit();
            } else {
                $json_data_string = wp_remote_retrieve_body($response);
                $data = json_decode($json_data_string, true);
                if (isset($data['result']) && $data['result'] == 'success' && !empty($data['access_token'])) {
                    if ($_GET['action'] == 'lipp_paypal_sandbox_connect') {
                        set_transient('apifw_sandbox_access_token', $data['access_token'], 28200);
                    } else {
                        set_transient('apifw_live_access_token', $data['access_token'], 28200);
                    }
                    delete_option('webhook_id');
                    $this->angelleye_paypal_invoice_update_user_info($data['access_token']);
                    wp_redirect(admin_url('admin.php?page=apifw_settings'));
                    exit();
                } else {
                    error_log(print_r($data, true));
                }
            }
        }
        if (!empty($_GET['action']) && $_GET['action'] == 'disconnect_paypal') {
            $this->angelleye_paypal_invoicing_load_rest_api();
            if (!empty($_GET['mode']) && $_GET['mode'] == 'SANDBOX') {
                try {
                    if (is_local_server() === false) {
                        $this->request->angelleye_paypal_invoicing_delete_web_hook_request();
                    }
                } catch (Exception $ex) {
                    delete_option('apifw_sandbox_refresh_token');
                    delete_transient('apifw_sandbox_access_token');
                    delete_option('webhook_id');
                    wp_redirect(admin_url('admin.php?page=apifw_settings'));
                    exit();
                }
                delete_option('apifw_sandbox_refresh_token');
                delete_transient('apifw_sandbox_access_token');
                delete_option('webhook_id');
                wp_redirect(admin_url('admin.php?page=apifw_settings'));
                exit();
            } else if (!empty($_GET['mode']) && $_GET['mode'] == 'LIVE') {
                try {
                    $this->request->angelleye_paypal_invoicing_delete_web_hook_request();
                } catch (Exception $ex) {
                    delete_option('apifw_live_refresh_token');
                    delete_transient('apifw_live_access_token');
                    delete_option('webhook_id');
                    wp_redirect(admin_url('admin.php?page=apifw_settings'));
                    exit();
                }
            }
            delete_option('apifw_live_refresh_token');
            delete_transient('apifw_live_access_token');
            delete_option('webhook_id');
            wp_redirect(admin_url('admin.php?page=apifw_settings'));
            exit();
        }
    }

    public static function exclude_invoice_comments_from_feed_where($where) {
        return $where . ( $where ? ' AND ' : '' ) . " comment_type != 'invoice_note' ";
    }

    public static function exclude_invoice_comments($clauses) {
        $clauses['where'] .= ( $clauses['where'] ? ' AND ' : '' ) . " comment_type != 'invoice_note' ";
        return $clauses;
    }

    public function own_angelleye_marketing_sendy_subscription() {
        global $wp;
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $current_url = $_SERVER['HTTP_REFERER'];
        } else {
            $current_url = home_url(add_query_arg(array(), $wp->request));
        }
        $url = 'https://sendy.angelleye.com/subscribe';
        $response = wp_remote_post($url, array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => array('list' => 'pjFolYKqSdLe57i4uuUz0g',
                'boolean' => 'true',
                'email' => $_POST['email'],
                'gdpr' => 'true',
                'silent' => 'true',
                'api_key' => 'qFcoVlU2uG3AMYabNTrC',
                'referrer' => $current_url
            ),
            'cookies' => array()
                )
        );
        if (is_wp_error($response)) {
            wp_send_json(wp_remote_retrieve_body($response));
        } else {
            $body = wp_remote_retrieve_body($response);
            $apiResponse = strval($body);
            switch ($apiResponse) {
                case 'true':
                case '1':
                    $this->prepareResponse("true", 'Thank you for subscribing!');
                case 'Already subscribed.':
                    $this->prepareResponse("true", 'Already subscribed!');
                default:
                    $this->prepareResponse("false", $apiResponse);
            }
        }
    }
    
    public function prepareResponse($status = false, $msg = 'Something went wrong!') {
        $return = array(
            'result' => $status,
            'message' => $msg
        );
        wp_send_json($return);
    }

}
