<?php

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Address;
use PayPal\Api\BillingInfo;
use PayPal\Api\Cost;
use PayPal\Api\Currency;
use PayPal\Api\Invoice;
use PayPal\Api\InvoiceAddress;
use PayPal\Api\InvoiceItem;
use PayPal\Api\MerchantInfo;
use PayPal\Api\PaymentTerm;
use PayPal\Api\Phone;
use PayPal\Api\ShippingInfo;
use PayPal\Api\Templates;
use PayPal\Api\Participant;
use PayPal\Api\ShippingCost;

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

        $this->first_name = isset($apifw_setting['first_name']) ? $apifw_setting['first_name'] : $this->first_name;
        $this->last_name = isset($apifw_setting['last_name']) ? $apifw_setting['last_name'] : $this->last_name;
        $this->compnay_name = isset($apifw_setting['compnay_name']) ? $apifw_setting['compnay_name'] : $this->compnay_name;
        $this->phone_number = isset($apifw_setting['phone_number']) ? $apifw_setting['phone_number'] : $this->phone_number;

        $this->address_line_1 = isset($apifw_setting['address_line_1']) ? $apifw_setting['address_line_1'] : $this->address_line_1;
        $this->address_line_2 = isset($apifw_setting['address_line_2']) ? $apifw_setting['address_line_2'] : $this->address_line_2;
        $this->city = isset($apifw_setting['city']) ? $apifw_setting['city'] : $this->city;
        $this->post_code = isset($apifw_setting['post_code']) ? $apifw_setting['post_code'] : $this->post_code;
        $this->state = isset($apifw_setting['state']) ? $apifw_setting['state'] : $this->state;
        $this->country = isset($apifw_setting['country']) ? $apifw_setting['country'] : $this->country;

        $this->shipping_rate = isset($apifw_setting['shipping_rate']) ? $apifw_setting['shipping_rate'] : $this->shipping_rate;
        $this->shipping_amount = isset($apifw_setting['shipping_amount']) ? $apifw_setting['shipping_amount'] : $this->shipping_amount;
        $this->tax_rate = isset($apifw_setting['tax_rate']) ? $apifw_setting['tax_rate'] : $this->tax_rate;
        $this->tax_name = isset($apifw_setting['tax_name']) ? $apifw_setting['tax_name'] : $this->tax_name;
        $this->note_to_recipient = isset($apifw_setting['note_to_recipient']) ? $apifw_setting['note_to_recipient'] : $this->note_to_recipient;
        $this->terms_and_condition = isset($apifw_setting['terms_and_condition']) ? $apifw_setting['terms_and_condition'] : $this->terms_and_condition;
        $this->debug_log = isset($apifw_setting['debug_log']) ? $apifw_setting['debug_log'] : $this->debug_log;

        $this->mode = ($this->testmode == true) ? 'SANDBOX' : 'LIVE';
        include_once( PAYPAL_INVOICE_PLUGIN_DIR . '/paypal-rest/vendor/autoload.php' );
    }

    public function angelleye_paypal_invoicing_getAuth() {
        $auth = new ApiContext(new OAuthTokenCredential($this->rest_client_id, $this->rest_secret_id));
        $auth->setConfig(array('mode' => $this->mode, 'http.headers.PayPal-Partner-Attribution-Id' => 'AngellEYE_SP_WP_Invoice', 'log.LogEnabled' => true, 'log.LogLevel' => 'DEBUG', 'log.FileName' => PAYPAL_INVOICE_LOG_DIR . 'paypal_invoice.log', 'cache.enabled' => true, 'cache.FileName' => PAYPAL_INVOICE_LOG_DIR . 'paypal_invoice_cache.log'));
        return $auth;
    }

    public function angelleye_paypal_invoicing_get_all_invoice() {
        try {
            $invoices = Invoice::getAll(array('page' => 120, 'page_size' => 20, 'total_count_required' => "true"), $this->angelleye_paypal_invoicing_getAuth());
            return json_decode($invoices, true);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function angelleye_paypal_invoicing_get_next_invoice_number() {
        try {
            $invoices = Invoice::generateNumber($this->angelleye_paypal_invoicing_getAuth());
            return json_decode($invoices, true);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function angelleye_paypal_invoicing_sync_invoicing_with_wp() {
        $page = 0;
        $bool = true;
        while ($bool) {
            $invoices_data = Invoice::getAll(array('page' => $page, 'page_size' => 20, 'total_count_required' => "true"), $this->angelleye_paypal_invoicing_getAuth());
            $invoices_array_data = json_decode($invoices_data, true);
            if (!empty($invoices_array_data)) {
                if (isset($invoices_array_data['invoices']) && !empty($invoices_array_data['invoices']) > 0) {
                    foreach ($invoices_array_data['invoices'] as $key => $invoice) {
                        $this->angelleye_paypal_invoicing_insert_paypal_invoice_data($invoice);
                    }
                } else {
                    $bool = false;
                    break;
                }
            } else {
                
            }
            $page = $page + 20;
        }
    }

    public function angelleye_paypal_invoicing_insert_paypal_invoice_data($invoice) {
        $billing_info = $invoice['billing_info'];
        $amount = $invoice['total_amount'];
        $paypal_invoice_data_array = array(
            'id' => $invoice['id'],
            'status' => $invoice['status'],
            'invoice_date' => $invoice['invoice_date'],
            'number' => $invoice['number'],
            'email' => $billing_info[0]['email'],
            'currency' => $amount['currency'],
            'total_amount_value' => $amount['value'],
        );
        $insert_invoice_array = array(
            'ID' => '',
            'post_type' => 'paypal_invoices',
            'post_status' => $paypal_invoice_data_array['status'],
            'post_title' => $paypal_invoice_data_array['id'],
            'post_author' => 0
        );
        $existing_post_id = $this->angelleye_paypal_invoicing_exist_post_by_title($paypal_invoice_data_array['id']);
        if ($existing_post_id == false) {
            $post_id = wp_insert_post($insert_invoice_array);
            foreach ($paypal_invoice_data_array as $key => $value) {
                update_post_meta($post_id, $key, $value);
            }
            update_post_meta($post_id, 'all_invoice_data', $invoice);
        } else {
            $insert_invoice_array['ID'] = $existing_post_id;
            wp_update_post($insert_invoice_array);
            foreach ($paypal_invoice_data_array as $key => $value) {
                update_post_meta($existing_post_id, $key, $value);
            }
            update_post_meta($existing_post_id, 'all_invoice_data', $invoice);
        }
    }

    public function angelleye_paypal_invoicing_exist_post_by_title($paypal_invoice_txn_id) {

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
            return $ex->getMessage();
        }
    }

    public function angelleye_paypal_invoicing_create_invoice_for_wc_order($order) {
        include_once(PAYPAL_INVOICE_PLUGIN_DIR . '/includes/class-angelleye-paypal-invoicing-calculations.php');
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
        if (wc_shipping_enabled() == true) {
            $shipping_first_name = version_compare(WC_VERSION, '3.0', '<') ? $order->shipping_first_name : $order->get_shipping_first_name();
            $shipping_last_name = version_compare(WC_VERSION, '3.0', '<') ? $order->shipping_last_name : $order->get_shipping_last_name();
            $shipping_address_1 = version_compare(WC_VERSION, '3.0', '<') ? $order->shipping_address_1 : $order->get_shipping_address_1();
            $shipping_address_2 = version_compare(WC_VERSION, '3.0', '<') ? $order->shipping_address_2 : $order->get_shipping_address_2();
            $shipping_city = version_compare(WC_VERSION, '3.0', '<') ? $order->shipping_city : $order->get_shipping_city();
            $shipping_postcode = version_compare(WC_VERSION, '3.0', '<') ? $order->shipping_postcode : $order->get_shipping_postcode();
            $shipping_country = version_compare(WC_VERSION, '3.0', '<') ? $order->shipping_country : $order->get_shipping_country();
            $shipping_state = version_compare(WC_VERSION, '3.0', '<') ? $order->shipping_state : $order->get_shipping_state();
        } else {
            $shipping_first_name = $billing_first_name;
            $shipping_last_name = $billing_last_name;
            $shipping_address_1 = $billing_address_1;
            $shipping_address_2 = $billing_address_2;
            $shipping_city = $shipping_city;
            $shipping_postcode = $billing_postcode;
            $shipping_country = $billing_country;
            $shipping_state = $billing_state;
        }

        $currency = version_compare(WC_VERSION, '3.0', '<') ? $order->get_order_currency() : $order->get_currency();
        $invoice = new Invoice();
        $invoice
                ->setMerchantInfo(new MerchantInfo())
                ->setBillingInfo(array(new BillingInfo()))
                ->setNote("")
                ->setPaymentTerm(new PaymentTerm())
                ->setShippingInfo(new ShippingInfo());
        $invoice->getMerchantInfo()
                ->setEmail($this->rest_paypal_email)
                ->setAddress(new Address());
        $invoice->getMerchantInfo()->getAddress()
                ->setLine1(get_option('woocommerce_store_address'))
                ->setLine1(get_option('woocommerce_store_address_2'))
                ->setCity(get_option('woocommerce_store_city'))
                ->setState(get_option('store_state'))
                ->setPostalCode(get_option('woocommerce_store_postcode'))
                ->setCountryCode(WC()->countries->get_base_country());
        $billing = $invoice->getBillingInfo();
        $billing[0]
                ->setEmail($billing_email)
                ->setFirstName($billing_first_name)
                ->setLastName($billing_last_name);
        $billing[0]->setBusinessName($billing_company)
                ->setAddress(new InvoiceAddress());
        $billing[0]->getAddress()
                ->setLine1($billing_address_1)
                ->setLine2($billing_address_2)
                ->setCity($billing_city)
                ->setState($billing_state)
                ->setPostalCode($billing_postcode)
                ->setCountryCode($billing_country);
        $invoice->getShippingInfo()
                ->setFirstName($shipping_first_name)
                ->setLastName($shipping_last_name)
                ->setBusinessName($billing_company)
                ->setPhone(new Phone())
                ->setAddress(new InvoiceAddress());
        $invoice->getShippingInfo()->getPhone()
                ->setCountryCode($this->angelleye_paypal_invoice_get_phone_country_code($billing_country))
                ->setNationalNumber($billing_phone);
        $invoice->getShippingInfo()->getAddress()
                ->setLine1($shipping_address_1)
                ->setLine2($shipping_address_2)
                ->setCity($shipping_city)
                ->setState($shipping_state)
                ->setPostalCode($shipping_postcode)
                ->setCountryCode($shipping_country);
        $items = array();
        $i = 0;
        if (!empty($this->order_param['order_items'])) {
            foreach ($this->order_param['order_items'] as $key => $order_items) {
                $items[$key] = new InvoiceItem();
                $items[$key]
                        ->setName($order_items['name'])
                        ->setQuantity($order_items['quantity'])
                        ->setUnitPrice(new Currency());
                $items[$key]->getUnitPrice()
                        ->setCurrency($currency)
                        ->setValue($order_items['unitPrice']);
                $i = $i + 1;
            }
        }
        $invoice->setItems($items);
        $invoice->getPaymentTerm()
                ->setTermType("DUE_ON_RECEIPT");
        try {
            $invoice->create($this->angelleye_paypal_invoicing_getAuth());
            $invoice->send($this->angelleye_paypal_invoicing_getAuth());
            return $invoice->getId();
        } catch (Exception $ex) {
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
            'US' => array('name' => 'UNITED STATES', 'code' => '1'),
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
            $post_data = $_REQUEST;
            $invoice_date = (isset($post_data['invoice_date'])) ? $post_data['invoice_date'] : date("d/m/Y");
            $invoice_date_obj = DateTime::createFromFormat('d/m/Y', $invoice_date);
            $invoice_date = $invoice_date_obj->format('Y-m-d e');
            $term_type = isset($post_data['invoiceTerms']) ? $post_data['invoiceTerms'] : '';
            $due_date = isset($post_data['DUE_ON_DATE_SPECIFIED']) ? $post_data['DUE_ON_DATE_SPECIFIED'] : '';
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
            $invoice = new Invoice();
            $invoice->setReference($reference);
            $invoice->setNumber($number);
            $invoice->setInvoiceDate($invoice_date);
            $invoice->setTerms($terms);
            $invoice->setMerchantMemo($merchant_memo);
            $participant = new Participant();
            $participant->setEmail($cc_to);
            $invoice->addCcInfo($participant);
            $invoice
                    ->setMerchantInfo(new MerchantInfo())
                    ->setBillingInfo(array(new BillingInfo()))
                    ->setNote($notes)
                    ->setPaymentTerm(new PaymentTerm())
                    ->setShippingInfo(new ShippingInfo());
            $invoice->getMerchantInfo()
                    ->setEmail($this->rest_paypal_email)
                    ->setAddress(new Address());
            $invoice->getMerchantInfo()->getAddress()
                    ->setLine1($this->address_line_1)
                    ->setLine1($this->address_line_2)
                    ->setCity($this->city)
                    ->setState($this->state)
                    ->setPostalCode($this->post_code)
                    ->setCountryCode($this->country);
            $billing = $invoice->getBillingInfo();
            $billing[0]
                    ->setEmail($bill_to);
            $billing[0]->setAddress(new InvoiceAddress());
            $invoice->getShippingInfo()->setAddress(new InvoiceAddress());
            $items = array();
            if (!empty($post_data['item_name'])) {
                foreach ($post_data['item_name'] as $key => $order_items) {
                    $items[$key] = new InvoiceItem();
                    $items[$key]
                            ->setName($order_items)
                            ->setQuantity($post_data['item_qty'][$key])
                            ->setDescription($post_data['item_description'][$key])
                            ->setUnitPrice(new Currency());
                    $items[$key]->getUnitPrice()
                            ->setCurrency('USD')
                            ->setValue($post_data['item_amt'][$key]);

                    $tax = new \PayPal\Api\Tax();
                    $tax->setPercent($post_data['item_txt_rate'][$key])->setName($post_data['item_txt_name'][$key]);
                    $items[$key]->setTax($tax);
                }
            }

            if (!empty($shippingAmount) && $shippingAmount > 0) {
                $shipping_cost = new ShippingCost();
                $shipping_currency = new Currency();
                $shipping_currency->setCurrency('USD');
                $shipping_currency->setValue($shippingAmount);
                $shipping_cost->setAmount($shipping_currency);
                $invoice->setShippingCost($shipping_cost);
            }
            if ($allowPartialPayments == 'on') {
                $invoice->setAllowPartialPayment('true');
                $minimum_amount_due_shipping_currency = new Currency();
                $minimum_amount_due_shipping_currency->setCurrency('USD');
                $minimum_amount_due_shipping_currency->setValue($minimumDueAmount);
                $invoice->setMinimumAmountDue($minimum_amount_due_shipping_currency);
            }
            if (!empty($invDiscount) && $invDiscount > 0) {
                $cost = new Cost();
                if ($invoiceDiscType == 'percentage') {
                    $cost->setPercent($invDiscount);
                } else {
                    $discount_currency = new Currency();
                    $discount_currency->setCurrency('USD');
                    $discount_currency->setValue($invDiscount);
                    $cost->setAmount($discount_currency);
                }
                $invoice->setDiscount($cost);
            }
            if ($allow_tips == 'on') {
                $invoice->setAllowtip('true');
            }
            if (!empty($due_date)) {
                $invoice_due_date_obj = DateTime::createFromFormat('d/m/Y', $due_date);
                $invoice_due_date = $invoice_due_date_obj->format('Y-m-d e');
                $invoice->getPaymentTerm()->setDueDate($invoice_due_date);
            }
            $invoice->setItems($items);
            $invoice->getPaymentTerm()
                    ->setTermType($term_type);
            $invoice->create($this->angelleye_paypal_invoicing_getAuth());
            $invoice->send($this->angelleye_paypal_invoicing_getAuth());
            update_post_meta($post_ID, 'is_paypal_invoice_sent', 'yes');
            return $invoice->getId();
        } catch (Exception $ex) {
            echo $this->angelleye_paypal_invoicing_print_api_error($ex->getMessage());
            return false;
        }
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
        $invoice = Invoice::get($invoiceId, $this->angelleye_paypal_invoicing_getAuth());
        $invoices_array_data = json_decode($invoice, true);
        return $invoices_array_data;
    }

    public function angelleye_paypal_invoicing_update_paypal_invoice_data($invoice, $post_id) {
        $billing_info = $invoice['billing_info'];
        $amount = $invoice['total_amount'];
        $paypal_invoice_data_array = array(
            'id' => $invoice['id'],
            'status' => $invoice['status'],
            'invoice_date' => $invoice['invoice_date'],
            'number' => $invoice['number'],
            'email' => $billing_info[0]['email'],
            'currency' => $amount['currency'],
            'total_amount_value' => $amount['value'],
        );
        $insert_invoice_array = array(
            'ID' => $post_id,
            'post_type' => 'paypal_invoices',
            'post_status' => $paypal_invoice_data_array['status'],
            'post_title' => $paypal_invoice_data_array['id'],
            'post_author' => 0
        );
        $post_id = wp_insert_post($insert_invoice_array);
        foreach ($paypal_invoice_data_array as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }
        update_post_meta($post_id, 'all_invoice_data', $invoice);
    }

}
