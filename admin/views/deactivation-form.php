<?php
defined('ABSPATH') || die('Cheatin&#8217; uh?');
$deactivation_url = wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . rawurlencode(PAYPAL_INVOICE_PLUGIN_BASENAME), 'deactivate-plugin_' . PAYPAL_INVOICE_PLUGIN_BASENAME);
?>
<div class="deactivation-Modal-paypal-invocing">
    <div class="deactivation-Modal-paypal-invocing-header">
        <div>
            <button class="deactivation-Modal-paypal-invocing-return deactivation-icon-chevron-left"><?php _e('Return', 'angelleye-paypal-invoicing'); ?></button>
            <h2><?php _e('PayPal Invoicing for WordPress feedback', 'angelleye-paypal-invoicing'); ?></h2>
        </div>
        <button class="deactivation-Modal-paypal-invocing-close deactivation-icon-close"><?php _e('Close', 'angelleye-paypal-invoicing'); ?></button>
    </div>
    <div class="deactivation-Modal-paypal-invocing-content">
        <div class="deactivation-Modal-paypal-invocing-question deactivation-isOpen">
            <h3><?php _e('May we have a little info about why you are deactivating?', 'angelleye-paypal-invoicing'); ?></h3>
            <ul>
                <li>
                    <input type="radio" name="reason" id="reason-temporary" value="Temporary Deactivation">
                    <label for="reason-temporary"><?php _e('<strong>It is a temporary deactivation.</strong> I am just debugging an issue.', 'angelleye-paypal-invoicing'); ?></label>
                </li>
                <li>
                    <input type="radio" name="reason" id="reason-broke" value="Broken Layout">
                    <label for="reason-broke"><?php _e('The plugin <strong>broke my layout</strong> or some functionality.', 'angelleye-paypal-invoicing'); ?></label>
                </li>
                <li>
                    <input type="radio" name="reason" id="reason-complicated" value="Complicated">
                    <label for="reason-complicated"><?php _e('The plugin is <strong>too complicated to configure.</strong>', 'angelleye-paypal-invoicing'); ?></label>
                </li>
                <li>
                    <input type="radio" name="reason" id="reason-other" value="Other">
                    <label for="reason-other"><?php _e('Other', 'angelleye-paypal-invoicing'); ?></label>
                    <div class="deactivation-Modal-paypal-invocing-fieldHidden">
                        <textarea name="reason-other-details" id="reason-other-details" placeholder="<?php _e('Let us know why you are deactivating PayPal Invoicing for WordPress so we can improve the plugin', 'angelleye-paypal-invoicing'); ?>"></textarea>
                    </div>
                </li>
            </ul>
            <input id="deactivation-reason" type="hidden" value="">
            <input id="deactivation-details" type="hidden" value="">
        </div>
    </div>
    <div class="deactivation-Modal-paypal-invocing-footer">
        <div>
            <a href="<?php echo esc_attr($deactivation_url); ?>" class="button button-primary deactivation-isDisabled" disabled id="mixpanel-send-deactivation-paypal-invocing"><?php _e('Send & Deactivate', 'angelleye-paypal-invoicing'); ?></a>
            <button class="deactivation-Modal-paypal-invocing-cancel"><?php _e('Cancel', 'angelleye-paypal-invoicing'); ?></button>
        </div>
        <a href="<?php echo esc_attr($deactivation_url); ?>" class="button button-secondary"><?php _e('Skip & Deactivate', 'angelleye-paypal-invoicing'); ?></a>
    </div>
</div>
<div class="deactivation-Modal-paypal-invocing-overlay"></div>
