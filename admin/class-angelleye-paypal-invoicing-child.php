<?php

class AngellEYE_PayPal_Invoicing_Request extends Invoice {

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
