<?php

function pifw_get_currency_symbol($currency = '') {

    $symbols = apply_filters(
            'woocommerce_currency_symbols', array(
        'AED' => '&#x62f;.&#x625;',
        'AFN' => '&#x60b;',
        'ALL' => 'L',
        'AMD' => 'AMD',
        'ANG' => '&fnof;',
        'AOA' => 'Kz',
        'ARS' => '&#36;',
        'AUD' => '&#36;',
        'AWG' => 'Afl.',
        'AZN' => 'AZN',
        'BAM' => 'KM',
        'BBD' => '&#36;',
        'BDT' => '&#2547;&nbsp;',
        'BGN' => '&#1083;&#1074;.',
        'BHD' => '.&#x62f;.&#x628;',
        'BIF' => 'Fr',
        'BMD' => '&#36;',
        'BND' => '&#36;',
        'BOB' => 'Bs.',
        'BRL' => '&#82;&#36;',
        'BSD' => '&#36;',
        'BTC' => '&#3647;',
        'BTN' => 'Nu.',
        'BWP' => 'P',
        'BYR' => 'Br',
        'BYN' => 'Br',
        'BZD' => '&#36;',
        'CAD' => '&#36;',
        'CDF' => 'Fr',
        'CHF' => '&#67;&#72;&#70;',
        'CLP' => '&#36;',
        'CNY' => '&yen;',
        'COP' => '&#36;',
        'CRC' => '&#x20a1;',
        'CUC' => '&#36;',
        'CUP' => '&#36;',
        'CVE' => '&#36;',
        'CZK' => '&#75;&#269;',
        'DJF' => 'Fr',
        'DKK' => 'DKK',
        'DOP' => 'RD&#36;',
        'DZD' => '&#x62f;.&#x62c;',
        'EGP' => 'EGP',
        'ERN' => 'Nfk',
        'ETB' => 'Br',
        'EUR' => '&euro;',
        'FJD' => '&#36;',
        'FKP' => '&pound;',
        'GBP' => '&pound;',
        'GEL' => '&#x20be;',
        'GGP' => '&pound;',
        'GHS' => '&#x20b5;',
        'GIP' => '&pound;',
        'GMD' => 'D',
        'GNF' => 'Fr',
        'GTQ' => 'Q',
        'GYD' => '&#36;',
        'HKD' => '&#36;',
        'HNL' => 'L',
        'HRK' => 'Kn',
        'HTG' => 'G',
        'HUF' => '&#70;&#116;',
        'IDR' => 'Rp',
        'ILS' => '&#8362;',
        'IMP' => '&pound;',
        'INR' => '&#8377;',
        'IQD' => '&#x639;.&#x62f;',
        'IRR' => '&#xfdfc;',
        'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
        'ISK' => 'kr.',
        'JEP' => '&pound;',
        'JMD' => '&#36;',
        'JOD' => '&#x62f;.&#x627;',
        'JPY' => '&yen;',
        'KES' => 'KSh',
        'KGS' => '&#x441;&#x43e;&#x43c;',
        'KHR' => '&#x17db;',
        'KMF' => 'Fr',
        'KPW' => '&#x20a9;',
        'KRW' => '&#8361;',
        'KWD' => '&#x62f;.&#x643;',
        'KYD' => '&#36;',
        'KZT' => 'KZT',
        'LAK' => '&#8365;',
        'LBP' => '&#x644;.&#x644;',
        'LKR' => '&#xdbb;&#xdd4;',
        'LRD' => '&#36;',
        'LSL' => 'L',
        'LYD' => '&#x644;.&#x62f;',
        'MAD' => '&#x62f;.&#x645;.',
        'MDL' => 'MDL',
        'MGA' => 'Ar',
        'MKD' => '&#x434;&#x435;&#x43d;',
        'MMK' => 'Ks',
        'MNT' => '&#x20ae;',
        'MOP' => 'P',
        'MRO' => 'UM',
        'MUR' => '&#x20a8;',
        'MVR' => '.&#x783;',
        'MWK' => 'MK',
        'MXN' => '&#36;',
        'MYR' => '&#82;&#77;',
        'MZN' => 'MT',
        'NAD' => '&#36;',
        'NGN' => '&#8358;',
        'NIO' => 'C&#36;',
        'NOK' => '&#107;&#114;',
        'NPR' => '&#8360;',
        'NZD' => '&#36;',
        'OMR' => '&#x631;.&#x639;.',
        'PAB' => 'B/.',
        'PEN' => 'S/.',
        'PGK' => 'K',
        'PHP' => '&#8369;',
        'PKR' => '&#8360;',
        'PLN' => '&#122;&#322;',
        'PRB' => '&#x440;.',
        'PYG' => '&#8370;',
        'QAR' => '&#x631;.&#x642;',
        'RMB' => '&yen;',
        'RON' => 'lei',
        'RSD' => '&#x434;&#x438;&#x43d;.',
        'RUB' => '&#8381;',
        'RWF' => 'Fr',
        'SAR' => '&#x631;.&#x633;',
        'SBD' => '&#36;',
        'SCR' => '&#x20a8;',
        'SDG' => '&#x62c;.&#x633;.',
        'SEK' => '&#107;&#114;',
        'SGD' => '&#36;',
        'SHP' => '&pound;',
        'SLL' => 'Le',
        'SOS' => 'Sh',
        'SRD' => '&#36;',
        'SSP' => '&pound;',
        'STD' => 'Db',
        'SYP' => '&#x644;.&#x633;',
        'SZL' => 'L',
        'THB' => '&#3647;',
        'TJS' => '&#x405;&#x41c;',
        'TMT' => 'm',
        'TND' => '&#x62f;.&#x62a;',
        'TOP' => 'T&#36;',
        'TRY' => '&#8378;',
        'TTD' => '&#36;',
        'TWD' => '&#78;&#84;&#36;',
        'TZS' => 'Sh',
        'UAH' => '&#8372;',
        'UGX' => 'UGX',
        'USD' => '&#36;',
        'UYU' => '&#36;',
        'UZS' => 'UZS',
        'VEF' => 'Bs F',
        'VND' => '&#8363;',
        'VUV' => 'Vt',
        'WST' => 'T',
        'XAF' => 'CFA',
        'XCD' => '&#36;',
        'XOF' => 'CFA',
        'XPF' => 'Fr',
        'YER' => '&#xfdfc;',
        'ZAR' => '&#82;',
        'ZMW' => 'ZK',
            )
    );
    return $currency_symbol = isset($symbols[$currency]) ? $symbols[$currency] : '';
}

function pifw_get_invoice_status_name_and_class($status) {
    $invoice_status = array(
        "UNPAID" => array('label' => 'Unpaid', 'class' => 'isDraft', 'action' => array('send' => 'Send')),
        "SENT" => array('label' => 'Unpaid (Sent)', 'class' => 'isDraft', 'action' => array('remind' => 'Remind')),
        'SCHEDULED' => array('label' => 'Scheduled', 'class' => 'isDraft'),
        "DRAFT" => array('label' => 'Draft', 'class' => 'isDraft', 'action' => array('send' => 'Send')),
        "PAID" => array('label' => 'Paid', 'class' => 'isPaid'),
        "MARKED_AS_PAID" => array('label' => 'Mark as paid', 'class' => 'isPaid'),
        "CANCELLED" => array('label' => 'Cancelled', 'class' => 'isCancelled'),
        "REFUNDED" => array('label' => 'Refunded', 'class' => 'isDraft'),
        "PARTIALLY_REFUNDED" => array('label' => 'Partially refunded', 'class' => 'isDraft'),
        "MARKED_AS_REFUNDED" => array('label' => 'Mark as refunded', 'class' => 'isDraft'),
        "PAYMENT_PENDING" => array('label' => 'Payment pending', 'class' => 'isDraft'),
        "PARTIALLY_PAID" => array('label' => 'Partially paid', 'class' => 'isPartiallyPaid', 'action' => array('remind' => 'Remind')),
    );
    if (!empty($invoice_status[$status])) {
        return $invoice_status[$status];
    }
}

function pifw_clean($var) {
    if (is_array($var)) {
        return array_map('pifw_clean', $var);
    } else {
        return is_scalar($var) ? sanitize_text_field($var) : $var;
    }
}

function pifw_get_paypal_invoice_date_format($date, $output_date_format = 'Y-m-d') {
    $input_date_format = get_option('date_format');
    $string = preg_replace('/[(]+[^*]+/', '', $date);
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
        //$tz = new DateTimeZone($tzstring);
        date_default_timezone_set($tzstring);
    } else {
        //$tz = new DateTimeZone('UTC');
        date_default_timezone_set('UTC');
    }
    $dt = DateTime::createFromFormat($input_date_format, $string);
    return $dt->format($output_date_format);
}

function angelleye_date_format_php_to_js($sFormat) {
    $chars = array(
        // Day
        'd' => 'dd',
        'j' => 'd',
        'l' => 'DD',
        'D' => 'D',
        // Month
        'm' => 'mm',
        'n' => 'm',
        'F' => 'MM',
        'M' => 'M',
        // Year
        'Y' => 'yy',
        'y' => 'y',
    );
    return strtr((string) $sFormat, $chars);
}

function is_local_server() {
    // we are from cli
    if (!isset($_SERVER['HTTP_HOST'])) {
        return;
    }

    if ($_SERVER['HTTP_HOST'] === 'localhost' || substr($_SERVER['REMOTE_ADDR'], 0, 3) === '10.' || substr($_SERVER['REMOTE_ADDR'], 0, 7) === '192.168') {

        return true;
    }

    $live_sites = [
        'HTTP_CLIENT_IP',
        'HTTP_X_REAL_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
    ];

    foreach ($live_sites as $ip) {
        if (!empty($_SERVER[$ip])) {
            return false;
        }
    }

    if (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) {
        return true;
    }

    $fragments = explode('.', site_url());

    if (in_array(end($fragments), array('dev', 'local', 'localhost', 'test'))) {
        return true;
    }

    return false;
}

function webhook_log($message) {
    if (function_exists('wc_get_logger')) {
        $log = wc_get_logger();
        $log->log('info', $message, array('source' => 'webhook'));
    }
}
