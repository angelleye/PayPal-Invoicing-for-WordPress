<?php

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Address;
use PayPal\Api\BillingInfo;
use PayPal\Api\Cost;
use PayPal\Api\Currency;
use PayPal\Api\InvoiceAddress;
use PayPal\Api\Invoice;
use PayPal\Api\AngellEYE_Invoice;
use PayPal\Api\InvoiceItem;
use PayPal\Api\MerchantInfo;
use PayPal\Api\PaymentTerm;
use PayPal\Api\Phone;
use PayPal\Api\ShippingInfo;
use PayPal\Api\Templates;
use PayPal\Api\Participant;
use PayPal\Api\ShippingCost;
use PayPal\Api\Notification;
use PayPal\Api\CancelNotification;
use PayPal\Api\VerifyWebhookSignature;
use PayPal\Api\WebhookEvent;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Api\OpenIdTokeninfo;
use PayPal\Api\OpenIdUserinfo;
use PayPal\Api\Payment;
use PayPal\Api\PaymentDetail;
use PayPal\Api\RefundDetail;

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
class AngellEYE_PayPal_Invoicing_Request {

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
    public $invoice_request;
    public $tpv_tracking;

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
        $this->testmode = ( isset($this->apifw_setting['enable_paypal_sandbox']) && $this->apifw_setting['enable_paypal_sandbox'] == 'on' ) ? true : false;
        if ($this->testmode == true) {
            $this->apifw_refresh_token = get_option('apifw_sandbox_refresh_token', false);
            $this->apifw_access_token = get_transient('apifw_sandbox_access_token', false);
            $this->rest_client_id = ( isset($this->apifw_setting['sandbox_client_id']) && !empty($this->apifw_setting['sandbox_client_id']) ) ? $this->apifw_setting['sandbox_client_id'] : '';
            $this->rest_secret_id = ( isset($this->apifw_setting['sandbox_secret']) && !empty($this->apifw_setting['sandbox_secret']) ) ? $this->apifw_setting['sandbox_secret'] : '';
            $this->rest_paypal_email = isset($this->apifw_setting['sandbox_paypal_email']) ? $this->apifw_setting['sandbox_paypal_email'] : '';
            $this->get_access_token_url = add_query_arg(array('rest_action' => 'get_access_token', 'mode' => 'SANDBOX'), PAYPAL_INVOICE_PLUGIN_SANDBOX_API_URL);
        } else {
            $this->apifw_refresh_token = get_option('apifw_live_refresh_token', false);
            $this->apifw_access_token = get_transient('apifw_live_access_token', false);
            $this->rest_client_id = ( isset($this->apifw_setting['client_id']) && !empty($this->apifw_setting['client_id']) ) ? $this->apifw_setting['client_id'] : '';
            $this->rest_secret_id = ( isset($this->apifw_setting['secret']) && !empty($this->apifw_setting['secret']) ) ? $this->apifw_setting['secret'] : '';
            $this->rest_paypal_email = isset($this->apifw_setting['paypal_email']) ? $this->apifw_setting['paypal_email'] : '';
            $this->get_access_token_url = add_query_arg(array('rest_action' => 'get_access_token', 'mode' => 'LIVE'), PAYPAL_INVOICE_PLUGIN_LIVE_API_URL);
        }
        $this->first_name = isset($this->apifw_setting['first_name']) ? $this->apifw_setting['first_name'] : '';
        $this->last_name = isset($this->apifw_setting['last_name']) ? $this->apifw_setting['last_name'] : '';
        $this->compnay_name = isset($this->apifw_setting['compnay_name']) ? $this->apifw_setting['compnay_name'] : '';
        $this->phone_number = isset($this->apifw_setting['phone_number']) ? $this->apifw_setting['phone_number'] : '';
        $this->address_line_1 = isset($this->apifw_setting['address_line_1']) ? $this->apifw_setting['address_line_1'] : '';
        $this->address_line_2 = isset($this->apifw_setting['address_line_2']) ? $this->apifw_setting['address_line_2'] : '';
        $this->city = isset($this->apifw_setting['city']) ? $this->apifw_setting['city'] : '';
        $this->post_code = isset($this->apifw_setting['post_code']) ? $this->apifw_setting['post_code'] : '';
        $this->state = isset($this->apifw_setting['state']) ? $this->apifw_setting['state'] : '';
        $this->country = isset($this->apifw_setting['country']) ? $this->apifw_setting['country'] : '';
        $this->shipping_rate = isset($this->apifw_setting['shipping_rate']) ? $this->apifw_setting['shipping_rate'] : '';
        $this->shipping_amount = isset($this->apifw_setting['shipping_amount']) ? $this->apifw_setting['shipping_amount'] : '';
        $this->tax_rate = isset($this->apifw_setting['tax_rate']) ? $this->apifw_setting['tax_rate'] : '';
        $this->tax_name = isset($this->apifw_setting['tax_name']) ? $this->apifw_setting['tax_name'] : '';
        $this->note_to_recipient = isset($this->apifw_setting['note_to_recipient']) ? $this->apifw_setting['note_to_recipient'] : '';
        $this->terms_and_condition = isset($this->apifw_setting['terms_and_condition']) ? $this->apifw_setting['terms_and_condition'] : '';
        $this->debug_log_value = isset($this->apifw_setting['debug_log']) ? $this->apifw_setting['debug_log'] : '';
        $this->debug_log = ($this->debug_log_value == 'on' ) ? true : false;
        $this->apifw_company_logo = isset($this->apifw_setting['apifw_company_logo']) ? $this->apifw_setting['apifw_company_logo'] : '';
        $this->mode = ($this->testmode == true) ? 'SANDBOX' : 'LIVE';
        include_once( ANGELLEYE_PAYPAL_INVOICING_PLUGIN_DIR . '/paypal-rest/autoload.php' );
        if (!class_exists('AngellEYE_PayPal_Invoicing_Logger')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-angelleye-paypal-invoicing-logger.php';
        }
        $this->log = new AngellEYE_PayPal_Invoicing_Logger();
        try {
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-angelleye-paypal-invoicing-manage.php';
        } catch (Exception $ex) {
            $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
        }
    }

    public function angelleye_paypal_invoicing_getAuth() {
        if ($this->apifw_refresh_token) {
            if (false === $this->apifw_access_token) {
                $response = wp_remote_post($this->get_access_token_url, array(
                    'method' => 'POST',
                    'timeout' => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => array(),
                    'body' => array('refresh_token' => $this->apifw_refresh_token),
                    'cookies' => array()
                        )
                );
                if (is_wp_error($response)) {
                    $error_message = $response->get_error_message();
                    error_log(print_r($error_message, true));
                } else {
                    $json_data_string = wp_remote_retrieve_body($response);
                    $data = json_decode($json_data_string, true);
                    if (isset($data['result']) && $data['result'] == 'success' && !empty($data['access_token'])) {
                        if ($this->mode == 'LIVE') {
                            set_transient('apifw_live_access_token', $data['access_token'], 28200);
                        } else {
                            set_transient('apifw_sandbox_access_token', $data['access_token'], 28200);
                        }
                        $this->apifw_access_token = $data['access_token'];
                    } else {
                        error_log(print_r($data, true));
                    }
                }
            }
            $auth = new ApiContext("Bearer " . $this->apifw_access_token);
            $auth->setConfig(array('mode' => $this->mode, 'http.headers.Authorization' => "Bearer " . $this->apifw_access_token, 'log.LogEnabled' => $this->debug_log, 'log.LogLevel' => 'DEBUG', 'log.FileName' => ANGELLEYE_PAYPAL_INVOICING_LOG_DIR . 'paypal_invoice.log', 'cache.enabled' => true, 'cache.FileName' => ANGELLEYE_PAYPAL_INVOICING_LOG_DIR . 'paypal_invoice_cache.log', "prefer" => "return=representation"));

            return $auth;
        } else {
            $auth = new ApiContext(new OAuthTokenCredential($this->rest_client_id, $this->rest_secret_id));
            $auth->setConfig(array('mode' => $this->mode, 'log.LogEnabled' => $this->debug_log, 'log.LogLevel' => 'DEBUG', 'log.FileName' => ANGELLEYE_PAYPAL_INVOICING_LOG_DIR . 'paypal_invoice.log', 'cache.enabled' => true, 'cache.FileName' => ANGELLEYE_PAYPAL_INVOICING_LOG_DIR . 'paypal_invoice_cache.log', "prefer" => "return=representation"));
            return $auth;
        }
    }

    public function angelleye_paypal_invoicing_get_all_invoice() {
        try {
            $invoices = AngellEYE_Invoice::getAll(array('page' => 120, 'page_size' => 20, 'total_count_required' => "true"), $this->angelleye_paypal_invoicing_getAuth());
            return json_decode($invoices, true);
        } catch (Exception $ex) {
            $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
            $error = $this->angelleye_paypal_invoicing_get_readable_message($ex->getData());
            if (empty($error)) {
                $error = $ex->getMessage();
            }
            set_transient('angelleye_paypal_invoicing_error', $error);
            wp_redirect(admin_url('edit.php?post_type=paypal_invoices&message=1029'));
            exit();
        }
    }

    public function angelleye_paypal_invoicing_get_next_invoice_number() {
        try {
            $invoices = AngellEYE_Invoice::generateNumber($this->angelleye_paypal_invoicing_getAuth());
            return json_decode($invoices, true);
        } catch (Exception $ex) {
            $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
            $error = $this->angelleye_paypal_invoicing_get_readable_message($ex->getData());
            if (empty($error)) {
                $error = $ex->getMessage();
            }
            set_transient('angelleye_paypal_invoicing_error', $error);
            wp_redirect(admin_url('edit.php?post_type=paypal_invoices&message=1029'));
            exit();
        }
    }

    public function angelleye_paypal_invoicing_sync_invoicing_with_wp() {

        global $wpdb;
        try {
            if ($this->angelleye_paypal_invoicing_is_api_set() == true) {
                remove_action('do_pings', 'do_all_pings', 10, 1);
                define('WP_IMPORTING', true);
                ini_set("memory_limit", -1);
                set_time_limit(0);
                ignore_user_abort(true);
                wp_defer_term_counting(true);
                wp_defer_comment_counting(true);
                $wpdb->query('SET autocommit = 0;');
                $angelleye_paypal_invoice_last_page_synce_number = get_option('angelleye_paypal_invoice_last_page_synce_number', false);
                if ($angelleye_paypal_invoice_last_page_synce_number == false) {
                    $page = 1;
                } else {
                    $page = 1;
                }
                $bool = true;
                while ($bool) {
                    try {
                        $invoices_data = AngellEYE_Invoice::getAll(array('page' => $page, 'page_size' => 100, 'total_count_required' => "true", 'fields' => 'payment_detail, payment_id, method, type'), $this->angelleye_paypal_invoicing_getAuth());
                    } catch (Exception $ex) {
                        $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
                        $bool = false;
                        break;
                    }
                    $invoices_array_data = json_decode($invoices_data, true);
                    if (!empty($invoices_array_data)) {
                        if (isset($invoices_array_data['items']) && !empty($invoices_array_data['items']) > 0) {
                            krsort($invoices_array_data['items'], SORT_NUMERIC);
                            foreach ($invoices_array_data['items'] as $key => $invoice) {
                                $this->angelleye_paypal_invoicing_insert_paypal_invoice_data($invoice);
                            }
                        } else {
                            $bool = false;
                            break;
                        }
                    } else {
                        $bool = false;
                        break;
                    }
                    update_option('angelleye_paypal_invoice_last_page_synce_number', $page);
                    $page = $page + 1;
                }
                delete_option('angelleye_paypal_invoice_last_page_synce_number');
                $wpdb->query('COMMIT;');
                wp_defer_term_counting(false);
                wp_defer_comment_counting(false);
            }
        } catch (Exception $ex) {
            $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
            $wpdb->query('COMMIT;');
            wp_defer_term_counting(false);
            wp_defer_comment_counting(false);
        }
    }

    public function angelleye_paypal_invoicing_insert_paypal_invoice_data($invoice) {
        $wp_post_id = '';
        $amount = $invoice['amount'];
        $paypal_invoice_data_array = array(
            'id' => $invoice['id'],
            'status' => isset($invoice['status']) ? $invoice['status'] : '',
            'invoice_date' => isset($invoice['invoice_date']) ? $invoice['invoice_date'] : '',
            'number' => isset($invoice['detail']['invoice_number']) ? $invoice['detail']['invoice_number'] : '',
            'email' => isset($invoice['primary_recipients'][0]['billing_info']['email_address']) ? $invoice['primary_recipients'][0]['billing_info']['email_address'] : '',
            'currency' => isset($amount['currency_code']) ? $amount['currency_code'] : '',
            'total_amount_value' => isset($amount['value']) ? $amount['value'] : '',
            'wp_invoice_date' => date("Y-m-d H:i:s", strtotime($invoice['detail']['invoice_date']))
        );
        $insert_invoice_array = array(
            'ID' => '',
            'post_type' => 'paypal_invoices',
            'post_status' => $paypal_invoice_data_array['status'],
            'post_title' => $paypal_invoice_data_array['number'],
            'post_author' => 0,
            'post_date' => date("Y-m-d H:i:s", strtotime($invoice['detail']['invoice_date'])),
            'post_name' => sanitize_title($invoice['id'])
        );
        $existing_post_id = $this->angelleye_paypal_invoicing_exist_post_by_name(sanitize_title($paypal_invoice_data_array['number']));
        if ($existing_post_id == false) {
            $post_id = wp_insert_post($insert_invoice_array);
            foreach ($paypal_invoice_data_array as $key => $value) {
                add_post_meta($post_id, $key, pifw_clean($value));
            }
            add_post_meta($post_id, 'all_invoice_data', pifw_clean($invoice));
            $wp_post_id = $post_id;
        } else {
            $insert_invoice_array['ID'] = $existing_post_id;
            wp_update_post($insert_invoice_array);
            foreach ($paypal_invoice_data_array as $key => $value) {
                update_post_meta($existing_post_id, $key, pifw_clean($value));
            }
            update_post_meta($existing_post_id, 'all_invoice_data', pifw_clean($invoice));
            if( function_exists('WC') ) {
                $order_id = get_post_meta($existing_post_id, '_order_id', true);
                if (!empty($order_id)) {
                    $order = wc_get_order($order_id);
                    if ($order) {
                        do_action('angelleye_update_order_status', $existing_post_id, $invoice, array());
                    }
                }
            }
            $wp_post_id = $existing_post_id;
        }
        if (isset($invoice['status']) && !empty($invoice['id']) && in_array($invoice['status'], array('PARTIALLY_PAID', 'PAID', 'MARKED_AS_PAID'))) {
            $tpv_tracking = get_post_meta($wp_post_id, '_tpv_tracking', true);
            if ($this->angelleye_paypal_invoicing_is_tpv_send($invoice, $tpv_tracking) === false) {
                try {
                    $invoice_data = AngellEYE_Invoice::get($invoice['id'], $this->angelleye_paypal_invoicing_getAuth());
                    if (!empty($invoice_data)) {
                        $invoice = json_decode($invoice_data, true);
                        update_post_meta($wp_post_id, 'all_invoice_data', pifw_clean($invoice));
                        $this->angelleye_paypa_invoice_save_track_invoice($wp_post_id, $invoice, $tpv_tracking);
                    }
                } catch (Exception $ex) {
                    return $wp_post_id;
                }
            }
        }
        return $wp_post_id;
    }
    
    public function angelleye_paypa_invoice_save_track_invoice($wp_post_id, $invoice, $tpv_tracking) {
        $paid_amount = 0;
        $tpv_tracked_transaction = array();
        if(isset($invoice['payments']['transactions']) && !empty($invoice['payments']['transactions'])) {
            foreach ($invoice['payments']['transactions'] as $key => $transaction) {
                if(isset($transaction['payment_id']) && !empty($transaction['payment_id'])) {
                    if(!empty($tpv_tracking) && isset($tpv_tracking['payment_id']) && !empty($tpv_tracking['payment_id']) && in_array($transaction['payment_id'], $tpv_tracking['payment_id'])) {
                        $tpv_tracked_transaction['amount'][$key] = isset($transaction['amount']['value']) ? $transaction['amount']['value'] : 0;
                        $paid_amount + $paid_amount = isset($transaction['amount']['value']) ? $transaction['amount']['value'] : 0;
                        $tpv_tracked_transaction['payment_id'][$key] = isset($transaction['payment_id']) ? $transaction['payment_id'] : '';
                    } else {
                        do_action('angelleye_paypal_invoice_response_data', $transaction, 10, $this->testmode);
                        $tpv_tracked_transaction['amount'][$key] = isset($transaction['amount']['value']) ? $transaction['amount']['value'] : 0;
                        $paid_amount + $paid_amount = isset($transaction['amount']['value']) ? $transaction['amount']['value'] : 0;
                        $tpv_tracked_transaction['payment_id'][$key] = isset($transaction['payment_id']) ? $transaction['payment_id'] : '';
                    }
                }
            }
            
        }
        if( !empty($tpv_tracked_transaction) && $paid_amount > 0) {
            $tpv_tracked_transaction['total_amout'] = $paid_amount;
            update_post_meta($wp_post_id, '_tpv_tracking', $tpv_tracked_transaction);
        }
    }

    public function angelleye_paypal_invoicing_is_tpv_send($invoice, $tpv_tracking) {
        if (empty($tpv_tracking)) {
            return false;
        }
        if (!isset($tpv_tracking['total_amout'])) {
            return false;
        }
        if (!isset($invoice['amount']['value'])) {
            return false;
        }
        if(!isset($invoice['payments']['paid_amount']['value'])) {
            return false;
        }
        if ($tpv_tracking['total_amout'] < $invoice['payments']['paid_amount']['value']) {
            return false;
        }
        return true;
    }

    public function angelleye_paypal_invoicing_exist_post_by_name($paypal_invoice_txn_id) {

        global $wpdb;

        $post_data = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = %s ", $paypal_invoice_txn_id, 'paypal_invoices'));

        if (empty($post_data)) {

            return false;
        } else {

            return $post_data[0];
        }
    }

    public function angelleye_paypal_invoicing_get_all_templates() {
        try {
            $templates = Templates::getAll(array('page' => 0, 'page_size' => 20, 'fields' => "all"), $this->angelleye_paypal_invoicing_getAuth());
            return json_decode($templates, true);
        } catch (Exception $ex) {
            $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
            set_transient('angelleye_paypal_invoicing_error', $this->angelleye_paypal_invoicing_get_readable_message($ex->getData()));
            wp_redirect(admin_url('edit.php?post_type=paypal_invoices&message=1029'));
            exit();
        }
    }

    public function angelleye_paypal_invoicing_create_invoice_for_wc_order($order, $is_send = false) {
        include_once(ANGELLEYE_PAYPAL_INVOICING_PLUGIN_DIR . '/includes/class-angelleye-paypal-invoicing-calculations.php');
        $this->calculation = new AngellEYE_PayPal_Invoicing_Calculation();
        $order_id = version_compare(WC_VERSION, '3.0', '<') ? $order->id : $order->get_id();
        $this->order_param = $this->calculation->order_calculation($order_id);
        $billing_company = version_compare(WC_VERSION, '3.0', '<') ? $order->billing_company : $order->get_billing_company();
        $billing_first_name = version_compare(WC_VERSION, '3.0', '<') ? $order->billing_first_name : $order->get_billing_first_name();
        $billing_last_name = version_compare(WC_VERSION, '3.0', '<') ? $order->billing_last_name : $order->get_billing_last_name();
        $billing_address_1 = version_compare(WC_VERSION, '3.0', '<') ? $order->billing_address_1 : $order->get_billing_address_1();
        $billing_address_2 = version_compare(WC_VERSION, '3.0', '<') ? $order->billing_address_2 : $order->get_billing_address_2();
        $billing_city = version_compare(WC_VERSION, '3.0', '<') ? $order->billing_city : $order->get_billing_city();
        $billing_postcode = version_compare(WC_VERSION, '3.0', '<') ? $order->billing_postcode : $order->get_billing_postcode();
        $billing_country = version_compare(WC_VERSION, '3.0', '<') ? $order->billing_country : $order->get_billing_country();
        $billing_state = version_compare(WC_VERSION, '3.0', '<') ? $order->billing_state : $order->get_billing_state();
        $billing_email = version_compare(WC_VERSION, '3.0', '<') ? $order->billing_email : $order->get_billing_email();
        $billing_phone = version_compare(WC_VERSION, '3.0', '<') ? $order->billing_phone : $order->get_billing_phone();
        $shipping_first_name = version_compare(WC_VERSION, '3.0', '<') ? $order->shipping_first_name : $order->get_shipping_first_name();
        $shipping_last_name = version_compare(WC_VERSION, '3.0', '<') ? $order->shipping_last_name : $order->get_shipping_last_name();
        $shipping_address_1 = version_compare(WC_VERSION, '3.0', '<') ? $order->shipping_address_1 : $order->get_shipping_address_1();
        $shipping_address_2 = version_compare(WC_VERSION, '3.0', '<') ? $order->shipping_address_2 : $order->get_shipping_address_2();
        $shipping_city = version_compare(WC_VERSION, '3.0', '<') ? $order->shipping_city : $order->get_shipping_city();
        $shipping_postcode = version_compare(WC_VERSION, '3.0', '<') ? $order->shipping_postcode : $order->get_shipping_postcode();
        $shipping_country = version_compare(WC_VERSION, '3.0', '<') ? $order->shipping_country : $order->get_shipping_country();
        $shipping_state = version_compare(WC_VERSION, '3.0', '<') ? $order->shipping_state : $order->get_shipping_state();
        if (empty($shipping_address_1) || empty($shipping_country)) {
            $shipping_first_name = $billing_first_name;
            $shipping_last_name = $billing_last_name;
            $shipping_address_1 = $billing_address_1;
            $shipping_address_2 = $billing_address_2;
            $shipping_city = $billing_city;
            $shipping_postcode = $billing_postcode;
            $shipping_country = $billing_country;
            $shipping_state = $billing_state;
        }
        $currency = version_compare(WC_VERSION, '3.0', '<') ? $order->get_order_currency() : $order->get_currency();
        $inovoice_param = array(
            'detail' =>
            array(
                'currency_code' => $currency
            ),
            'invoicer' =>
            array(
                'name' =>
                array(
                    'given_name' => $this->first_name,
                    'surname' => $this->last_name,
                ),
                'address' =>
                array(
                    'address_line_1' => $this->address_line_1,
                    'address_line_2' => $this->address_line_2,
                    'admin_area_2' => $this->city,
                    'admin_area_1' => $this->state,
                    'postal_code' => $this->post_code,
                    'country_code' => $this->country,
                ),
                'email_address' => $this->rest_paypal_email
            ),
            'primary_recipients' =>
            array(
                0 =>
                array(
                    'billing_info' =>
                    array(
                        'email_address' => $billing_email,
                        'additional_info_value' => 'add-info',
                        'name' => array('given_name' => $billing_first_name, 'surname' => $billing_last_name),
                        'address' => array('address_line_1' => $billing_address_1, 'address_line_2' => $billing_address_2, 'admin_area_2' => $billing_city, 'admin_area_1' => $billing_state, 'postal_code' => $billing_postcode, 'country_code' => $billing_country)
                    ),
                    'shipping_info' =>
                    array(
                        'name' => array('given_name' => $shipping_first_name, 'surname' => $shipping_last_name),
                        'address' => array('address_line_1' => $shipping_address_1, 'address_line_2' => $shipping_address_2, 'admin_area_2' => $shipping_city, 'admin_area_1' => $shipping_state, 'postal_code' => $shipping_postcode, 'country_code' => $shipping_country)
                    ),
                ),
            ),
            'configuration' =>
            array(
                'allow_tip' => false,
                'tax_calculated_after_discount' => false,
                'tax_inclusive' => false
            ),
            'amount' =>
            array(
                'breakdown' => array()
            )
        );

        if (!empty($this->apifw_company_logo)) {
            $inovoice_param['invoicer']['logo_url'] = $this->apifw_company_logo;
        }

        if (!empty($this->phone_number)) {
            $inovoice_param['invoicer']['phones'] = array(
                0 =>
                array(
                    'country_code' => $this->angelleye_paypal_invoice_get_phone_country_code($this->country),
                    'national_number' => preg_replace("/[^0-9]/", "", $this->phone_number),
                    'phone_type' => 'MOBILE',
                ),
            );
        }

        if (!empty($this->order_param['order_items'])) {
            foreach ($this->order_param['order_items'] as $key => $order_items) {
                $inovoice_param['items'][$key] = array(
                    'name' => $order_items['name'],
                    'quantity' => $order_items['quantity'],
                    'unit_amount' =>
                    array(
                        'currency_code' => $currency,
                        'value' => $order_items['unitPrice'],
                    ),
                );
            }
        }
        $body_request = $this->angelleye_remove_empty_key($inovoice_param);
        $payLoad = json_encode($body_request);
        $AngellEYE_Invoice = new AngellEYE_Invoice();
        try {
            $invoice_id = $AngellEYE_Invoice->create($this->angelleye_paypal_invoicing_getAuth(), '', $payLoad);
            $invoice_ob = AngellEYE_Invoice::get($invoice_id, $this->angelleye_paypal_invoicing_getAuth());
            if ($is_send == true) {
                $invoice_ob->send($this->angelleye_paypal_invoicing_getAuth());
            }
            return $invoice_id;
        } catch (PayPalConnectionException $ex) {
            $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
            return false;
        } catch (Exception $ex) {
            $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
            return false;
        }
    }

    public function angelleye_paypal_invoice_get_phone_country_code($country_code) {
        $countryArray = array(
            'AD' => array('name' => 'ANDORRA', 'code' => '376'),
            'AE' => array('name' => 'UNITED ARAB EMIRATES', 'code' => '971'),
            'AF' => array('name' => 'AFGHANISTAN', 'code' => '93'),
            'AG' => array('name' => 'ANTIGUA AND BARBUDA', 'code' => '1268'),
            'AI' => array('name' => 'ANGUILLA', 'code' => '1264'),
            'AL' => array('name' => 'ALBANIA', 'code' => '355'),
            'AM' => array('name' => 'ARMENIA', 'code' => '374'),
            'AN' => array('name' => 'NETHERLANDS ANTILLES', 'code' => '599'),
            'AO' => array('name' => 'ANGOLA', 'code' => '244'),
            'AQ' => array('name' => 'ANTARCTICA', 'code' => '672'),
            'AR' => array('name' => 'ARGENTINA', 'code' => '54'),
            'AS' => array('name' => 'AMERICAN SAMOA', 'code' => '1684'),
            'AT' => array('name' => 'AUSTRIA', 'code' => '43'),
            'AU' => array('name' => 'AUSTRALIA', 'code' => '61'),
            'AW' => array('name' => 'ARUBA', 'code' => '297'),
            'AZ' => array('name' => 'AZERBAIJAN', 'code' => '994'),
            'BA' => array('name' => 'BOSNIA AND HERZEGOVINA', 'code' => '387'),
            'BB' => array('name' => 'BARBADOS', 'code' => '1246'),
            'BD' => array('name' => 'BANGLADESH', 'code' => '880'),
            'BE' => array('name' => 'BELGIUM', 'code' => '32'),
            'BF' => array('name' => 'BURKINA FASO', 'code' => '226'),
            'BG' => array('name' => 'BULGARIA', 'code' => '359'),
            'BH' => array('name' => 'BAHRAIN', 'code' => '973'),
            'BI' => array('name' => 'BURUNDI', 'code' => '257'),
            'BJ' => array('name' => 'BENIN', 'code' => '229'),
            'BL' => array('name' => 'SAINT BARTHELEMY', 'code' => '590'),
            'BM' => array('name' => 'BERMUDA', 'code' => '1441'),
            'BN' => array('name' => 'BRUNEI DARUSSALAM', 'code' => '673'),
            'BO' => array('name' => 'BOLIVIA', 'code' => '591'),
            'BR' => array('name' => 'BRAZIL', 'code' => '55'),
            'BS' => array('name' => 'BAHAMAS', 'code' => '1242'),
            'BT' => array('name' => 'BHUTAN', 'code' => '975'),
            'BW' => array('name' => 'BOTSWANA', 'code' => '267'),
            'BY' => array('name' => 'BELARUS', 'code' => '375'),
            'BZ' => array('name' => 'BELIZE', 'code' => '501'),
            'CA' => array('name' => 'CANADA', 'code' => '1'),
            'CC' => array('name' => 'COCOS (KEELING) ISLANDS', 'code' => '61'),
            'CD' => array('name' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'code' => '243'),
            'CF' => array('name' => 'CENTRAL AFRICAN REPUBLIC', 'code' => '236'),
            'CG' => array('name' => 'CONGO', 'code' => '242'),
            'CH' => array('name' => 'SWITZERLAND', 'code' => '41'),
            'CI' => array('name' => 'COTE D IVOIRE', 'code' => '225'),
            'CK' => array('name' => 'COOK ISLANDS', 'code' => '682'),
            'CL' => array('name' => 'CHILE', 'code' => '56'),
            'CM' => array('name' => 'CAMEROON', 'code' => '237'),
            'CN' => array('name' => 'CHINA', 'code' => '86'),
            'CO' => array('name' => 'COLOMBIA', 'code' => '57'),
            'CR' => array('name' => 'COSTA RICA', 'code' => '506'),
            'CU' => array('name' => 'CUBA', 'code' => '53'),
            'CV' => array('name' => 'CAPE VERDE', 'code' => '238'),
            'CX' => array('name' => 'CHRISTMAS ISLAND', 'code' => '61'),
            'CY' => array('name' => 'CYPRUS', 'code' => '357'),
            'CZ' => array('name' => 'CZECH REPUBLIC', 'code' => '420'),
            'DE' => array('name' => 'GERMANY', 'code' => '49'),
            'DJ' => array('name' => 'DJIBOUTI', 'code' => '253'),
            'DK' => array('name' => 'DENMARK', 'code' => '45'),
            'DM' => array('name' => 'DOMINICA', 'code' => '1767'),
            'DO' => array('name' => 'DOMINICAN REPUBLIC', 'code' => '1809'),
            'DZ' => array('name' => 'ALGERIA', 'code' => '213'),
            'EC' => array('name' => 'ECUADOR', 'code' => '593'),
            'EE' => array('name' => 'ESTONIA', 'code' => '372'),
            'EG' => array('name' => 'EGYPT', 'code' => '20'),
            'ER' => array('name' => 'ERITREA', 'code' => '291'),
            'ES' => array('name' => 'SPAIN', 'code' => '34'),
            'ET' => array('name' => 'ETHIOPIA', 'code' => '251'),
            'FI' => array('name' => 'FINLAND', 'code' => '358'),
            'FJ' => array('name' => 'FIJI', 'code' => '679'),
            'FK' => array('name' => 'FALKLAND ISLANDS (MALVINAS)', 'code' => '500'),
            'FM' => array('name' => 'MICRONESIA, FEDERATED STATES OF', 'code' => '691'),
            'FO' => array('name' => 'FAROE ISLANDS', 'code' => '298'),
            'FR' => array('name' => 'FRANCE', 'code' => '33'),
            'GA' => array('name' => 'GABON', 'code' => '241'),
            'GB' => array('name' => 'UNITED KINGDOM', 'code' => '44'),
            'GD' => array('name' => 'GRENADA', 'code' => '1473'),
            'GE' => array('name' => 'GEORGIA', 'code' => '995'),
            'GH' => array('name' => 'GHANA', 'code' => '233'),
            'GI' => array('name' => 'GIBRALTAR', 'code' => '350'),
            'GL' => array('name' => 'GREENLAND', 'code' => '299'),
            'GM' => array('name' => 'GAMBIA', 'code' => '220'),
            'GN' => array('name' => 'GUINEA', 'code' => '224'),
            'GQ' => array('name' => 'EQUATORIAL GUINEA', 'code' => '240'),
            'GR' => array('name' => 'GREECE', 'code' => '30'),
            'GT' => array('name' => 'GUATEMALA', 'code' => '502'),
            'GU' => array('name' => 'GUAM', 'code' => '1671'),
            'GW' => array('name' => 'GUINEA-BISSAU', 'code' => '245'),
            'GY' => array('name' => 'GUYANA', 'code' => '592'),
            'HK' => array('name' => 'HONG KONG', 'code' => '852'),
            'HN' => array('name' => 'HONDURAS', 'code' => '504'),
            'HR' => array('name' => 'CROATIA', 'code' => '385'),
            'HT' => array('name' => 'HAITI', 'code' => '509'),
            'HU' => array('name' => 'HUNGARY', 'code' => '36'),
            'ID' => array('name' => 'INDONESIA', 'code' => '62'),
            'IE' => array('name' => 'IRELAND', 'code' => '353'),
            'IL' => array('name' => 'ISRAEL', 'code' => '972'),
            'IM' => array('name' => 'ISLE OF MAN', 'code' => '44'),
            'IN' => array('name' => 'INDIA', 'code' => '91'),
            'IQ' => array('name' => 'IRAQ', 'code' => '964'),
            'IR' => array('name' => 'IRAN, ISLAMIC REPUBLIC OF', 'code' => '98'),
            'IS' => array('name' => 'ICELAND', 'code' => '354'),
            'IT' => array('name' => 'ITALY', 'code' => '39'),
            'JM' => array('name' => 'JAMAICA', 'code' => '1876'),
            'JO' => array('name' => 'JORDAN', 'code' => '962'),
            'JP' => array('name' => 'JAPAN', 'code' => '81'),
            'KE' => array('name' => 'KENYA', 'code' => '254'),
            'KG' => array('name' => 'KYRGYZSTAN', 'code' => '996'),
            'KH' => array('name' => 'CAMBODIA', 'code' => '855'),
            'KI' => array('name' => 'KIRIBATI', 'code' => '686'),
            'KM' => array('name' => 'COMOROS', 'code' => '269'),
            'KN' => array('name' => 'SAINT KITTS AND NEVIS', 'code' => '1869'),
            'KP' => array('name' => 'KOREA DEMOCRATIC PEOPLES REPUBLIC OF', 'code' => '850'),
            'KR' => array('name' => 'KOREA REPUBLIC OF', 'code' => '82'),
            'KW' => array('name' => 'KUWAIT', 'code' => '965'),
            'KY' => array('name' => 'CAYMAN ISLANDS', 'code' => '1345'),
            'KZ' => array('name' => 'KAZAKSTAN', 'code' => '7'),
            'LA' => array('name' => 'LAO PEOPLES DEMOCRATIC REPUBLIC', 'code' => '856'),
            'LB' => array('name' => 'LEBANON', 'code' => '961'),
            'LC' => array('name' => 'SAINT LUCIA', 'code' => '1758'),
            'LI' => array('name' => 'LIECHTENSTEIN', 'code' => '423'),
            'LK' => array('name' => 'SRI LANKA', 'code' => '94'),
            'LR' => array('name' => 'LIBERIA', 'code' => '231'),
            'LS' => array('name' => 'LESOTHO', 'code' => '266'),
            'LT' => array('name' => 'LITHUANIA', 'code' => '370'),
            'LU' => array('name' => 'LUXEMBOURG', 'code' => '352'),
            'LV' => array('name' => 'LATVIA', 'code' => '371'),
            'LY' => array('name' => 'LIBYAN ARAB JAMAHIRIYA', 'code' => '218'),
            'MA' => array('name' => 'MOROCCO', 'code' => '212'),
            'MC' => array('name' => 'MONACO', 'code' => '377'),
            'MD' => array('name' => 'MOLDOVA, REPUBLIC OF', 'code' => '373'),
            'ME' => array('name' => 'MONTENEGRO', 'code' => '382'),
            'MF' => array('name' => 'SAINT MARTIN', 'code' => '1599'),
            'MG' => array('name' => 'MADAGASCAR', 'code' => '261'),
            'MH' => array('name' => 'MARSHALL ISLANDS', 'code' => '692'),
            'MK' => array('name' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'code' => '389'),
            'ML' => array('name' => 'MALI', 'code' => '223'),
            'MM' => array('name' => 'MYANMAR', 'code' => '95'),
            'MN' => array('name' => 'MONGOLIA', 'code' => '976'),
            'MO' => array('name' => 'MACAU', 'code' => '853'),
            'MP' => array('name' => 'NORTHERN MARIANA ISLANDS', 'code' => '1670'),
            'MR' => array('name' => 'MAURITANIA', 'code' => '222'),
            'MS' => array('name' => 'MONTSERRAT', 'code' => '1664'),
            'MT' => array('name' => 'MALTA', 'code' => '356'),
            'MU' => array('name' => 'MAURITIUS', 'code' => '230'),
            'MV' => array('name' => 'MALDIVES', 'code' => '960'),
            'MW' => array('name' => 'MALAWI', 'code' => '265'),
            'MX' => array('name' => 'MEXICO', 'code' => '52'),
            'MY' => array('name' => 'MALAYSIA', 'code' => '60'),
            'MZ' => array('name' => 'MOZAMBIQUE', 'code' => '258'),
            'NA' => array('name' => 'NAMIBIA', 'code' => '264'),
            'NC' => array('name' => 'NEW CALEDONIA', 'code' => '687'),
            'NE' => array('name' => 'NIGER', 'code' => '227'),
            'NG' => array('name' => 'NIGERIA', 'code' => '234'),
            'NI' => array('name' => 'NICARAGUA', 'code' => '505'),
            'NL' => array('name' => 'NETHERLANDS', 'code' => '31'),
            'NO' => array('name' => 'NORWAY', 'code' => '47'),
            'NP' => array('name' => 'NEPAL', 'code' => '977'),
            'NR' => array('name' => 'NAURU', 'code' => '674'),
            'NU' => array('name' => 'NIUE', 'code' => '683'),
            'NZ' => array('name' => 'NEW ZEALAND', 'code' => '64'),
            'OM' => array('name' => 'OMAN', 'code' => '968'),
            'PA' => array('name' => 'PANAMA', 'code' => '507'),
            'PE' => array('name' => 'PERU', 'code' => '51'),
            'PF' => array('name' => 'FRENCH POLYNESIA', 'code' => '689'),
            'PG' => array('name' => 'PAPUA NEW GUINEA', 'code' => '675'),
            'PH' => array('name' => 'PHILIPPINES', 'code' => '63'),
            'PK' => array('name' => 'PAKISTAN', 'code' => '92'),
            'PL' => array('name' => 'POLAND', 'code' => '48'),
            'PM' => array('name' => 'SAINT PIERRE AND MIQUELON', 'code' => '508'),
            'PN' => array('name' => 'PITCAIRN', 'code' => '870'),
            'PR' => array('name' => 'PUERTO RICO', 'code' => '1'),
            'PT' => array('name' => 'PORTUGAL', 'code' => '351'),
            'PW' => array('name' => 'PALAU', 'code' => '680'),
            'PY' => array('name' => 'PARAGUAY', 'code' => '595'),
            'QA' => array('name' => 'QATAR', 'code' => '974'),
            'RO' => array('name' => 'ROMANIA', 'code' => '40'),
            'RS' => array('name' => 'SERBIA', 'code' => '381'),
            'RU' => array('name' => 'RUSSIAN FEDERATION', 'code' => '7'),
            'RW' => array('name' => 'RWANDA', 'code' => '250'),
            'SA' => array('name' => 'SAUDI ARABIA', 'code' => '966'),
            'SB' => array('name' => 'SOLOMON ISLANDS', 'code' => '677'),
            'SC' => array('name' => 'SEYCHELLES', 'code' => '248'),
            'SD' => array('name' => 'SUDAN', 'code' => '249'),
            'SE' => array('name' => 'SWEDEN', 'code' => '46'),
            'SG' => array('name' => 'SINGAPORE', 'code' => '65'),
            'SH' => array('name' => 'SAINT HELENA', 'code' => '290'),
            'SI' => array('name' => 'SLOVENIA', 'code' => '386'),
            'SK' => array('name' => 'SLOVAKIA', 'code' => '421'),
            'SL' => array('name' => 'SIERRA LEONE', 'code' => '232'),
            'SM' => array('name' => 'SAN MARINO', 'code' => '378'),
            'SN' => array('name' => 'SENEGAL', 'code' => '221'),
            'SO' => array('name' => 'SOMALIA', 'code' => '252'),
            'SR' => array('name' => 'SURINAME', 'code' => '597'),
            'ST' => array('name' => 'SAO TOME AND PRINCIPE', 'code' => '239'),
            'SV' => array('name' => 'EL SALVADOR', 'code' => '503'),
            'SY' => array('name' => 'SYRIAN ARAB REPUBLIC', 'code' => '963'),
            'SZ' => array('name' => 'SWAZILAND', 'code' => '268'),
            'TC' => array('name' => 'TURKS AND CAICOS ISLANDS', 'code' => '1649'),
            'TD' => array('name' => 'CHAD', 'code' => '235'),
            'TG' => array('name' => 'TOGO', 'code' => '228'),
            'TH' => array('name' => 'THAILAND', 'code' => '66'),
            'TJ' => array('name' => 'TAJIKISTAN', 'code' => '992'),
            'TK' => array('name' => 'TOKELAU', 'code' => '690'),
            'TL' => array('name' => 'TIMOR-LESTE', 'code' => '670'),
            'TM' => array('name' => 'TURKMENISTAN', 'code' => '993'),
            'TN' => array('name' => 'TUNISIA', 'code' => '216'),
            'TO' => array('name' => 'TONGA', 'code' => '676'),
            'TR' => array('name' => 'TURKEY', 'code' => '90'),
            'TT' => array('name' => 'TRINIDAD AND TOBAGO', 'code' => '1868'),
            'TV' => array('name' => 'TUVALU', 'code' => '688'),
            'TW' => array('name' => 'TAIWAN, PROVINCE OF CHINA', 'code' => '886'),
            'TZ' => array('name' => 'TANZANIA, UNITED REPUBLIC OF', 'code' => '255'),
            'UA' => array('name' => 'UKRAINE', 'code' => '380'),
            'UG' => array('name' => 'UGANDA', 'code' => '256'),
            'US' => array('name' => 'UNITED STATES', 'code' => '001'),
            'UY' => array('name' => 'URUGUAY', 'code' => '598'),
            'UZ' => array('name' => 'UZBEKISTAN', 'code' => '998'),
            'VA' => array('name' => 'HOLY SEE (VATICAN CITY STATE)', 'code' => '39'),
            'VC' => array('name' => 'SAINT VINCENT AND THE GRENADINES', 'code' => '1784'),
            'VE' => array('name' => 'VENEZUELA', 'code' => '58'),
            'VG' => array('name' => 'VIRGIN ISLANDS, BRITISH', 'code' => '1284'),
            'VI' => array('name' => 'VIRGIN ISLANDS, U.S.', 'code' => '1340'),
            'VN' => array('name' => 'VIET NAM', 'code' => '84'),
            'VU' => array('name' => 'VANUATU', 'code' => '678'),
            'WF' => array('name' => 'WALLIS AND FUTUNA', 'code' => '681'),
            'WS' => array('name' => 'SAMOA', 'code' => '685'),
            'XK' => array('name' => 'KOSOVO', 'code' => '381'),
            'YE' => array('name' => 'YEMEN', 'code' => '967'),
            'YT' => array('name' => 'MAYOTTE', 'code' => '262'),
            'ZA' => array('name' => 'SOUTH AFRICA', 'code' => '27'),
            'ZM' => array('name' => 'ZAMBIA', 'code' => '260'),
            'ZW' => array('name' => 'ZIMBABWE', 'code' => '263')
        );
        if (!empty($country_code)) {
            if (isset($countryArray[$country_code])) {
                return $countryArray[$country_code]['code'];
            }
        }
        return '1';
    }

    public function angelleye_paypal_invoicing_create_invoice($post_ID, $post, $update) {
        try {
            $post_data = pifw_clean($_REQUEST);
            $invoice_date = (isset($post_data['invoice_date'])) ? $post_data['invoice_date'] : date(get_option('date_format'));
            //$invoice_date_obj = DateTime::createFromFormat('d/m/Y', $invoice_date);
            //$invoice_date = $invoice_date_obj->format('Y-m-d e');
            $invoice_date = pifw_get_paypal_invoice_date_format($invoice_date);
            $term_type = isset($post_data['invoiceTerms']) ? $post_data['invoiceTerms'] : '';
            if ($term_type == 'DUE_ON_DATE_SPECIFIED') {
                $due_date = isset($post_data['DUE_ON_DATE_SPECIFIED']) ? pifw_get_paypal_invoice_date_format($post_data['DUE_ON_DATE_SPECIFIED']) : '';
            } else {
                $due_date = '';
            }
            $reference = isset($post_data['reference']) ? $post_data['reference'] : '';
            $number = isset($post_data['invoice_number']) ? $post_data['invoice_number'] : '';
            $notes = isset($post_data['notes']) ? $post_data['notes'] : '';
            $terms = isset($post_data['terms']) ? $post_data['terms'] : '';
            $merchant_memo = isset($post_data['memodesc']) ? $post_data['memodesc'] : '';
            $bill_to = isset($post_data['bill_to']) ? $post_data['bill_to'] : '';
            $cc_to = isset($post_data['cc_to']) ? $post_data['cc_to'] : '';
            $shippingAmount = isset($post_data['shippingAmount']) ? $post_data['shippingAmount'] : 0.00;
            $invoiceDiscType = isset($post_data['invoiceDiscType']) ? $post_data['invoiceDiscType'] : 'percentage';
            $invDiscount = isset($post_data['invDiscount']) ? $post_data['invDiscount'] : 0;
            $allowPartialPayments = isset($post_data['allowPartialPayments']) ? $post_data['allowPartialPayments'] : 'no';
            $allow_tips = isset($post_data['allowTips']) ? $post_data['allowTips'] : 'no';
            $minimumDueAmount = isset($post_data['minimumDueAmount']) ? $post_data['minimumDueAmount'] : 0.00;

            $inovoice_param = array(
                'detail' =>
                array(
                    'invoice_number' => $number,
                    'reference' => $reference,
                    'invoice_date' => $invoice_date,
                    'currency_code' => 'USD',
                    'note' => $notes,
                    'term' => $terms,
                    'memo' => $merchant_memo,
                ),
                'invoicer' =>
                array(
                    'name' =>
                    array(
                        'given_name' => $this->first_name,
                        'surname' => $this->last_name,
                    ),
                    'address' =>
                    array(
                        'address_line_1' => $this->address_line_1,
                        'address_line_2' => $this->address_line_2,
                        'admin_area_2' => $this->city,
                        'admin_area_1' => $this->state,
                        'postal_code' => $this->post_code,
                        'country_code' => $this->country,
                    ),
                    'email_address' => $this->rest_paypal_email
                ),
                'primary_recipients' =>
                array(
                    0 =>
                    array(
                        'billing_info' =>
                        array(
                            'email_address' => $bill_to,
                            'additional_info_value' => 'add-info'
                        )
                    )
                ),
                'configuration' =>
                array(
                    'allow_tip' => false,
                    'tax_calculated_after_discount' => false,
                    'tax_inclusive' => false
                ),
                'amount' =>
                array(
                    'breakdown' => array()
                ),
            );

            if (!empty($this->phone_number)) {
                $inovoice_param['invoicer']['phones'] = array(
                    0 =>
                    array(
                        'country_code' => $this->angelleye_paypal_invoice_get_phone_country_code($this->country),
                        'national_number' => preg_replace("/[^0-9]/", "", $this->phone_number),
                        'phone_type' => 'MOBILE',
                    ),
                );
            }

            if (!empty($this->apifw_company_logo)) {
                $inovoice_param['invoicer']['logo_url'] = $this->apifw_company_logo;
            }

            if ($allowPartialPayments == 'on') {
                $inovoice_param['detail']['configuration']['partial_payment'] = array(
                    'allow_partial_payment' => $allowPartialPayments == 'on' ? true : false,
                    'minimum_amount_due' =>
                    array(
                        'currency_code' => 'USD',
                        'value' => $allowPartialPayments == 'on' ? $minimumDueAmount : '',
                    ),
                );
            }
            if (!empty($due_date)) {
                $inovoice_param['detail']['payment_term'] = array(
                    'term_type' => $term_type,
                    'due_date' => $due_date,
                );
            }
            if (!empty($post_data['item_name'])) {
                foreach ($post_data['item_name'] as $key => $order_items) {
                    $inovoice_param['items'][$key] = array(
                        'name' => $order_items,
                        'quantity' => $post_data['item_qty'][$key],
                        'unit_amount' =>
                        array(
                            'currency_code' => 'USD',
                            'value' => $post_data['item_amt'][$key],
                        ),
                    );
                    if (!empty($post_data['item_txt_rate'][$key])) {
                        $inovoice_param['items'][$key]['tax'] = array(
                            'name' => $post_data['item_txt_name'][$key],
                            'percent' => $post_data['item_txt_rate'][$key],
                        );
                    }
                    if (!empty($post_data['item_description'][$key])) {
                        $inovoice_param['items'][$key]['description'] = $post_data['item_description'][$key];
                    }
                }
            }

            if (!empty($shippingAmount) && $shippingAmount > 0) {
                $inovoice_param['amount']['breakdown']['shipping'] = array(
                    'amount' =>
                    array(
                        'currency_code' => 'USD',
                        'value' => $shippingAmount,
                    ),
                );
            }
            if (!empty($invDiscount) && $invDiscount > 0) {
                if ($invoiceDiscType == 'percentage') {
                    $inovoice_param['amount']['breakdown']['discount']['invoice_discount'] = array(
                        'percent' => $invDiscount
                    );
                } else {
                    $inovoice_param['amount']['breakdown']['discount']['invoice_discount'] = array(
                        'amount' =>
                        array(
                            'currency_code' => 'USD',
                            'value' => $invDiscount,
                        ),
                    );
                }
            }
            $body_request = $this->angelleye_remove_empty_key($inovoice_param);
            $payLoad = json_encode($body_request);
            $AngellEYE_Invoice = new AngellEYE_Invoice();
            $invoice_id = $AngellEYE_Invoice->create($this->angelleye_paypal_invoicing_getAuth(), '', $payLoad);
            $invoice_ob = AngellEYE_Invoice::get($invoice_id, $this->angelleye_paypal_invoicing_getAuth());
            if (!empty($_REQUEST['send_invoice'])) {
                $invoice_ob->send($this->angelleye_paypal_invoicing_getAuth());
                update_post_meta($post_ID, 'is_paypal_invoice_sent', 'yes');
                return $invoice_ob->getId();
            }
            update_post_meta($post_ID, 'is_paypal_invoice_sent', 'yes');
            return $invoice_id;
        } catch (PayPalConnectionException $ex) {
            $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
            $error = $this->angelleye_paypal_invoicing_get_readable_message($ex->getData());
            if (empty($error)) {
                $error = $ex->getMessage();
            }
            set_transient('angelleye_paypal_invoicing_error', $error);
            wp_redirect(admin_url('edit.php?post_type=paypal_invoices&message=1029'));
            exit();
        }
    }

    public function angelleye_remove_empty_key($data) {
        $original = $data;
        $data = array_filter($data);
        $data = array_map(function ($e) {
            return is_array($e) ? $this->angelleye_remove_empty_key($e) : $e;
        }, $data);
        return $original === $data ? $data : $this->angelleye_remove_empty_key($data);
    }

    public function angelleye_paypal_invoicing_print_api_error($error_msg) {
        ?>
        <br>
        <div class="alert alert-danger alert-dismissible fade show mtonerem" role="alert">
            <?php echo wp_kses_post($error_msg) . PHP_EOL; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
    }

    public function angelleye_paypal_invoicing_get_invoice_details($invoiceId) {
        try {
            $invoice = AngellEYE_Invoice::get($invoiceId, $this->angelleye_paypal_invoicing_getAuth());
            $invoices_array_data = json_decode($invoice, true);
            return $invoices_array_data;
        } catch (PayPalConnectionException $ex) {
            $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
            $error = $this->angelleye_paypal_invoicing_get_readable_message($ex->getData());
            if (empty($error)) {
                $error = $ex->getMessage();
            }
            set_transient('angelleye_paypal_invoicing_error', $error);
            wp_redirect(admin_url('edit.php?post_type=paypal_invoices&message=1029'));
            exit();
        }
    }

    public function angelleye_paypal_invoicing_update_paypal_invoice_data($invoice, $post_id) {
        $amount = $invoice['amount'];
        $paypal_invoice_data_array = array(
            'id' => $invoice['id'],
            'status' => isset($invoice['status']) ? $invoice['status'] : '',
            'invoice_date' => isset($invoice['invoice_date']) ? $invoice['invoice_date'] : '',
            'number' => isset($invoice['detail']['invoice_number']) ? $invoice['detail']['invoice_number'] : '',
            'email' => isset($invoice['primary_recipients'][0]['billing_info']['email_address']) ? $invoice['primary_recipients'][0]['billing_info']['email_address'] : '',
            'currency' => isset($amount['currency_code']) ? $amount['currency_code'] : '',
            'total_amount_value' => isset($amount['value']) ? $amount['value'] : '',
            'wp_invoice_date' => date("Y-m-d H:i:s", strtotime($invoice['detail']['invoice_date']))
        );
        $insert_invoice_array = array(
            'ID' => $post_id,
            'post_type' => 'paypal_invoices',
            'post_status' => $paypal_invoice_data_array['status'],
            'post_title' => $paypal_invoice_data_array['number'],
            'post_author' => 0,
            'post_date' => date("Y-m-d H:i:s", strtotime($invoice['detail']['invoice_date'])),
            'post_name' => sanitize_title($invoice['id'])
        );
        wp_update_post($insert_invoice_array);
        foreach ($paypal_invoice_data_array as $key => $value) {
            update_post_meta($post_id, $key, pifw_clean($value));
        }
        update_post_meta($post_id, 'all_invoice_data', pifw_clean($invoice));
        if( function_exists('WC') ) {
            $order_id = get_post_meta($post_id, '_order_id', true);
            if (!empty($order_id)) {
                $order = wc_get_order($order_id);
                if ($order) {
                    do_action('angelleye_update_order_status', $post_id, $invoice, array());
                }
            }
        }
    }

    public function angelleye_paypal_invoicing_send_invoice_remind($invoiceId) {
        try {
            $invoice_ob = AngellEYE_Invoice::get($invoiceId, $this->angelleye_paypal_invoicing_getAuth());
            $notify = new Notification();
            $notify
                    ->setSubject(apply_filters('angelleye_paypal_invoice_remind_subject', "Past due"))
                    ->setNote(apply_filters('angelleye_paypal_invoice_remind_note', "Please pay soon"))
                    ->setSendToMerchant(true);
            $invoice_ob->remind($notify, $this->angelleye_paypal_invoicing_getAuth());
        } catch (PayPalConnectionException $ex) {
            $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
            $error = $this->angelleye_paypal_invoicing_get_readable_message($ex->getData());
            if (empty($error)) {
                $error = $ex->getMessage();
            }
            set_transient('angelleye_paypal_invoicing_error', $error);
            wp_redirect(admin_url('edit.php?post_type=paypal_invoices&message=1029'));
            exit();
        }
    }

    public function angelleye_paypal_invoicing_send_invoice_from_draft($invoiceId, $post_id) {
        try {
            $invoice_ob = AngellEYE_Invoice::get($invoiceId, $this->angelleye_paypal_invoicing_getAuth());
            $invoice_ob->send($this->angelleye_paypal_invoicing_getAuth());
            $invoice = $this->angelleye_paypal_invoicing_get_invoice_details($invoiceId);
            $this->angelleye_paypal_invoicing_update_paypal_invoice_data($invoice, $post_id);
        } catch (PayPalConnectionException $ex) {
            $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
            $error = $this->angelleye_paypal_invoicing_get_readable_message($ex->getData());
            if (empty($error)) {
                $error = $ex->getMessage();
            }
            set_transient('angelleye_paypal_invoicing_error', $error);
            wp_redirect(admin_url('edit.php?post_type=paypal_invoices&message=1029'));
            exit();
        }
    }

    public function angelleye_paypal_invoicing_cancel_invoice($invoiceId) {
        try {
            $invoice_ob = AngellEYE_Invoice::get($invoiceId, $this->angelleye_paypal_invoicing_getAuth());
            $notify = new CancelNotification();
            $notify
                    ->setSubject(apply_filters('angelleye_paypal_invoice_cancel_subject', "Past due"))
                    ->setNote(apply_filters('angelleye_paypal_invoice_cancel_note', "Please pay soon"))
                    ->setSendToMerchant(apply_filters('angelleye_paypal_invoice_send_to_merchant', true))
                    ->setSendToPayer(apply_filters('angelleye_paypal_invoice_send_to_payer', true));
            $invoice_ob->cancel($notify, $this->angelleye_paypal_invoicing_getAuth());
        } catch (PayPalConnectionException $ex) {
            $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
            $error = $this->angelleye_paypal_invoicing_get_readable_message($ex->getData());
            if (empty($error)) {
                $error = $ex->getMessage();
            }
            set_transient('angelleye_paypal_invoicing_error', $error);
            wp_redirect(admin_url('edit.php?post_type=paypal_invoices&message=1029'));
            exit();
        }
    }

    public function angelleye_paypal_invoicing_delete_invoice($invoiceId) {
        try {
            $invoice_ob = AngellEYE_Invoice::get($invoiceId, $this->angelleye_paypal_invoicing_getAuth());
            $invoice_ob->delete($this->angelleye_paypal_invoicing_getAuth());
        } catch (PayPalConnectionException $ex) {
            $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
            $error = $this->angelleye_paypal_invoicing_get_readable_message($ex->getData());
            if (empty($error)) {
                $error = $ex->getMessage();
            }
            set_transient('angelleye_paypal_invoicing_error', $error);
            wp_redirect(admin_url('edit.php?post_type=paypal_invoices&message=1029'));
            exit();
        }
    }

    public function angelleye_paypal_invoicing_get_readable_message($json_error) {
        $message = '';
        $error_object = json_decode($json_error);
        if (isset($error_object->name)) {
            switch ($error_object->name) {
                case 'VALIDATION_ERROR':
                    foreach ($error_object->details as $e) {
                        $message .= "\t" . $e->field . "\n\t" . $e->issue . "\n\n";
                    }
                    break;
                case 'INVALID_REQUEST':
                    foreach ($error_object->details as $e) {
                        $message .= "\t" . $e->field . "\n\t" . $e->description . "\n\n";
                    }
                    break;
                case 'BUSINESS_ERROR':
                    $message .= $error_object->message;
                    break;
            }
        }
        if (!empty($message)) {
            return $message;
        } else if (!empty($error_object->message)) {
            $message = $error_object->message;
        } else if (!empty($error_object->error_description)) {
            $message = $error_object->error_description;
        } else {
            $message = $json_error;
        }
        return $message;
    }

    public function angelleye_get_user_info_using_access_token($access_token) {
        try {
            $params = array('access_token' => $access_token);
            $userInfo = OpenIdUserinfo::getUserinfo($params, $this->angelleye_paypal_invoicing_getAuth());
            $result_data = array('result' => 'success', 'user_data' => $userInfo);
        } catch (Exception $ex) {
            $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
            $result_data = array('result' => 'error', 'error_msg' => $ex->getMessage());
        }
        return $result_data;
    }

    public function angelleye_paypal_invoicing_is_api_set() {
        if ((!empty($this->rest_client_id) && !empty($this->rest_secret_id) && !empty($this->rest_paypal_email)) || $this->apifw_refresh_token) {
            return true;
        } else {
            return false;
        }
    }

    public function angelleye_paypal_invoice_get_payment($transaction_id) {
        try {
            $payment = Payment::get($transaction_id, $this->angelleye_paypal_invoicing_getAuth());
            return $payment;
        } catch (Exception $ex) {
            $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
            $error = $this->angelleye_paypal_invoicing_get_readable_message($ex->getData());
            if (empty($error)) {
                $error = $ex->getMessage();
            }
            set_transient('angelleye_paypal_invoicing_error', $error);
        }
    }

    public function angelleye_paypal_invoice_record_payment($invoiceId, $paymentDetail) {
        try {
            $body_request = $this->angelleye_remove_empty_key($paymentDetail);
            $payLoad = json_encode($body_request);
            $invoice_ob = AngellEYE_Invoice::get($invoiceId, $this->angelleye_paypal_invoicing_getAuth());
            $record = new PaymentDetail($payLoad);
            $return = $invoice_ob->recordPayment($record, $this->angelleye_paypal_invoicing_getAuth());
            return $return;
        } catch (Exception $ex) {
            $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
            $error = $this->angelleye_paypal_invoicing_get_readable_message($ex->getData());
            if (empty($error)) {
                $error = $ex->getMessage();
            }
            set_transient('angelleye_paypal_invoicing_error', $error);
            return false;
        }
    }

    public function angelleye_paypal_invoice_record_refund($invoiceId, $paymentDetail) {
        try {
            $body_request = $this->angelleye_remove_empty_key($paymentDetail);
            $payLoad = json_encode($body_request);
            $invoice_ob = AngellEYE_Invoice::get($invoiceId, $this->angelleye_paypal_invoicing_getAuth());
            $record = new RefundDetail($payLoad);
            $return = $invoice_ob->recordRefund($record, $this->angelleye_paypal_invoicing_getAuth());
            return $return;
        } catch (Exception $ex) {
            $this->log->add('paypal_invoice_log', print_r($ex->getMessage(), true));
            $error = $this->angelleye_paypal_invoicing_get_readable_message($ex->getData());
            if (empty($error)) {
                $error = $ex->getMessage();
            }
            set_transient('angelleye_paypal_invoicing_error', $error);
            return false;
        }
    }

}
