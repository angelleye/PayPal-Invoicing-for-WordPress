<?php

class AngellEYE_PayPal_Invoicing_Payment_Logger {

    protected static $_instance = null;
    public $allow_method = array();
    public $api_url;
    public $api_key;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        $this->api_url = 'https://gtctgyk7fh.execute-api.us-east-2.amazonaws.com/default/PayPalPaymentsTracker';
        $this->api_key = 'srGiuJFpDO4W7YCDXF56g2c9nT1JhlURVGqYD7oa';
        $this->allow_method = array('PayPal Invoice');
        add_action('angelleye_paypal_invoice_response_data', array($this, 'own_angelleye_paypal_invoice_response_data'), 10, 6);
    }

    public function own_angelleye_paypal_invoice_response_data($result_data, $request_data, $product_id = 1, $sandbox = false, $is_nvp = true, $payment_method = 'paypal_invoice') {
        $request_param = array();
        if (isset($result_data) && is_array($result_data) && !empty($result_data['CURL_ERROR'])) {
            return $result_data;
        } else {
            $invoice = $result_data;
            $invoice['METHOD'] = 'PayPal Invoice';
            $request_param['site_url'] = get_bloginfo('url');
            $request_param['type'] = $invoice['METHOD'];
            $request_param['mode'] = ($sandbox) ? 'sandbox' : 'live';
            $request_param['product_id'] = $product_id;
            if (isset($invoice['payments']['transactions'][0]['payment_id'])) {
                $request_param['status'] = 'Success';
                $request_param['transaction_id'] = isset($invoice['payments']['transactions'][0]['payment_id']) ? $invoice['payments']['transactions'][0]['payment_id'] : '';
                $request_param['merchant_id'] = '';
                $request_param['correlation_id'] = '';
                $request_param['amount'] = isset($invoice['payments']['transactions'][0]['amount']['value']) ? $invoice['payments']['transactions'][0]['amount']['value'] : '0.00';
                $this->angelleye_tpv_request($request_param);
            }
        }
    }

    public function angelleye_tpv_request($request_param) {
        try {
            $payment_type = $request_param['type'];
            $amount = $request_param['amount'];
            $status = $request_param['status'];
            $site_url = $request_param['site_url'];
            $payment_mode = $request_param['mode'];
            $merchant_id = $request_param['merchant_id'];
            $correlation_id = $request_param['correlation_id'];
            $transaction_id = $request_param['transaction_id'];
            $product_id = $request_param['product_id'];
            $params = [
                "product_id" => $product_id,
                "type" => $payment_type,
                "amount" => $amount,
                "status" => $status,
                "site_url" => $site_url,
                "mode" => $payment_mode,
                "merchant_id" => $merchant_id,
                "correlation_id" => $correlation_id,
                "transaction_id" => $transaction_id
            ];
            $params = apply_filters('angelleye_log_params', $params);
            $post_args = array(
                'headers' => array(
                    'Content-Type' => 'application/json; charset=utf-8',
                    'x-api-key' => $this->api_key
                ),
                'body' => json_encode($params),
                'method' => 'POST',
                'data_format' => 'body',
            );
            $response = wp_remote_post($this->api_url, $post_args);
            if (is_wp_error($response)) {
            } else {
            }
        } catch (Exception $ex) {
            
        }
    }

}
