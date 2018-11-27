<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * @since      1.0.0
 * @package    AngellEYE_PayPal_Invoicing_WC_Payment
 * @subpackage AngellEYE_PayPal_Invoicing_WC_Payment/includes
 * @author     Angell EYE <service@angelleye.com>
 */
class AngellEYE_PayPal_Invoicing_WC_Payment extends WC_Payment_Gateway {

    /**
     * Constructor for the gateway.
     */
    public function __construct() {
        $this->id = 'pifw_paypal_invoice';
        $this->icon = apply_filters('woocommerce_pifw_paypal_invoice_icon', '');
        $this->has_fields = false;
        $this->method_title = _x('PayPal Invoice', 'PayPal Invoice', 'angelleye-paypal-invoicing');
        $this->method_description = __('PayPal Invoice', 'angelleye-paypal-invoicing');
        $this->apifw_setting = get_option('apifw_setting');
        $apifw_setting = $this->apifw_setting;
        $this->enable_paypal_sandbox = isset($apifw_setting['enable_paypal_sandbox']) ? $apifw_setting['enable_paypal_sandbox'] : '';
        $this->sandbox_secret = isset($apifw_setting['sandbox_secret']) ? $apifw_setting['sandbox_secret'] : '';
        $this->sandbox_client_id = isset($apifw_setting['sandbox_client_id']) ? $apifw_setting['sandbox_client_id'] : '';
        $this->client_id = isset($apifw_setting['client_id']) ? $apifw_setting['client_id'] : '';
        $this->secret = isset($apifw_setting['secret']) ? $apifw_setting['secret'] : '';
        $this->paypal_email = isset($apifw_setting['paypal_email']) ? $apifw_setting['paypal_email'] : '';
        $this->sandbox_paypal_email = isset($apifw_setting['sandbox_paypal_email']) ? $apifw_setting['sandbox_paypal_email'] : '';
        $this->note_to_recipient = isset($apifw_setting['note_to_recipient']) ? $apifw_setting['note_to_recipient'] : '';
        $this->terms_and_condition = isset($apifw_setting['terms_and_condition']) ? $apifw_setting['terms_and_condition'] : '';
        $this->init_form_fields();
        $this->init_settings();
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->instructions = $this->get_option('instructions');
        $this->is_enabled = 'yes' === $this->get_option('enabled', 'no');
        $this->testmode = 'yes' === ($this->enable_paypal_sandbox == 'on' ? 'yes' : 'no');
        $this->mode = ($this->testmode == true) ? 'SANDBOX' : 'LIVE';
        if ($this->testmode == true) {
            $this->rest_client_id = $this->sandbox_client_id;
            $this->rest_secret_id = $this->sandbox_secret;
            $this->rest_paypal_email = $this->sandbox_paypal_email;
        } else {
            $this->rest_client_id = $this->client_id;
            $this->rest_secret_id = $this->secret;
            $this->rest_paypal_email = $this->paypal_email;
        }
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_thankyou_pifw_paypal_invoice', array($this, 'thankyou_page'));
        add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);
    }

    /**
     * Initialise Gateway Settings Form Fields.
     */
    public function init_form_fields() {

        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'angelleye-paypal-invoicing'),
                'type' => 'checkbox',
                'label' => __('Enable PayPal Invoice', 'angelleye-paypal-invoicing'),
                'default' => 'no',
            ),
            'title' => array(
                'title' => __('Title', 'angelleye-paypal-invoicing'),
                'type' => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'angelleye-paypal-invoicing'),
                'default' => _x('PayPal Invoice', 'PayPal Invoice', 'angelleye-paypal-invoicing'),
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __('Description', 'angelleye-paypal-invoicing'),
                'type' => 'textarea',
                'description' => __('Payment method description that the customer will see on your checkout.', 'angelleye-paypal-invoicing'),
                'default' => __('Please send a check to Store Name, Store Street, Store Town, Store State / County, Store Postcode.', 'angelleye-paypal-invoicing'),
                'desc_tip' => true,
            ),
            'instructions' => array(
                'title' => __('Instructions', 'angelleye-paypal-invoicing'),
                'type' => 'textarea',
                'description' => __('Instructions that will be added to the thank you page and emails.', 'angelleye-paypal-invoicing'),
                'default' => '',
                'desc_tip' => true,
            ),
        );
    }

    public function admin_options() {
        ?>
        <h3><?php _e('PayPal Invoice', 'paypal-for-woocommerce'); ?></h3>
        <p><?php _e($this->method_description, 'paypal-for-woocommerce'); ?></p>
        <table class="form-table">
            <?php
            $this->generate_settings_html();
            ?>
        </table> 
        <?php
    }

    public function is_available() {
        if ($this->is_enabled == true) {
            $apifw_sandbox_refresh_token = get_option('apifw_sandbox_refresh_token', false);
            if ((!empty($this->rest_client_id) && !empty($this->rest_secret_id) && !empty($this->rest_paypal_email)) || (!empty($apifw_sandbox_refresh_token) && $apifw_sandbox_refresh_token != false)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Output for the order received page.
     */
    public function thankyou_page() {
        if ($this->instructions) {
            echo wp_kses_post(wpautop(wptexturize($this->instructions)));
        }
    }

    /**
     * Add content to the WC emails.
     *
     * @access public
     * @param WC_Order $order Order object.
     * @param bool     $sent_to_admin Sent to admin.
     * @param bool     $plain_text Email format: plain text or HTML.
     */
    public function email_instructions($order, $sent_to_admin, $plain_text = false) {
        if ($this->instructions && !$sent_to_admin && 'pifw_paypal_invoice' === $order->get_payment_method() && $order->has_status('on-hold')) {
            echo wp_kses_post(wpautop(wptexturize($this->instructions)) . PHP_EOL);
        }
    }

    /**
     * Process the payment and return the result.
     *
     * @param int $order_id Order ID.
     * @return array
     */
    public function process_payment($order_id) {
        $order = wc_get_order($order_id);
        include_once(ANGELLEYE_PAYPAL_INVOICING_PLUGIN_DIR . '/admin/class-angelleye-paypal-invoicing-request.php');
        $this->request = new AngellEYE_PayPal_Invoicing_Request(null, null);
        $invoice_id = $this->request->angelleye_paypal_invoicing_create_invoice_for_wc_order($order, true);
        if (!empty($invoice_id) && $invoice_id != false) {
            $invoice = $this->request->angelleye_paypal_invoicing_get_invoice_details($invoice_id);
            $post_id = $this->request->angelleye_paypal_invoicing_insert_paypal_invoice_data($invoice);
            update_post_meta($order_id, '_paypal_invoice_id', $invoice_id);
            update_post_meta($order_id, '_paypal_invoice_wp_post_id', $post_id);
            update_post_meta($post_id, '_order_id', $order_id);
            $order->add_order_note(__("We've sent your invoice.", 'angelleye-paypal-invoicing'));
            if ($order->get_total() > 0) {
                $order->update_status('on-hold', _x('Awaiting payment', 'PayPal Invoice', 'angelleye-paypal-invoicing'));
            } else {
                $order->payment_complete();
            }
            wc_reduce_stock_levels($order_id);
            WC()->cart->empty_cart();
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($order),
            );
        } else {
            return array(
                'result' => 'false',
                'redirect' => $this->get_return_url($order),
            );
        }
    }

}
