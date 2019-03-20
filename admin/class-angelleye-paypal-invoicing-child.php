<?php

namespace PayPal\Api;

use PayPal\Common\PayPalResourceModel;
use PayPal\Rest\ApiContext;
use PayPal\Transport\PayPalRestCall;
use PayPal\Validation\ArgumentValidator;
use PayPal\Validation\UrlValidator;

class AngellEYE_PayPal_Invoice_Child extends Invoice {
    
    public static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function getAll($params = array(), $apiContext = null, $restCall = null) {
        ArgumentValidator::validate($params, 'params');

        $allowedParams = array(
            'page' => 1,
            'page_size' => 1,
            'total_count_required' => 1
        );

        $payLoad = "";
        $json = self::executeCall(
                        "/v2/invoicing/invoices/?" . http_build_query(array_intersect_key($params, $allowedParams)), "GET", $payLoad, null, $apiContext, $restCall
        );
        $ret = new InvoiceSearchResponse();
        $ret->fromJson($json);
        return $ret;
    }

}
