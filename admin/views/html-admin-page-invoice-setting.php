<?php
/**
 * Admin View: Page - Addons
 *
 * @var string $view
 * @var object $addons
 */
if (!defined('ABSPATH')) {
    exit;
}
$apifw_setting = get_option('apifw_setting');
$enable_paypal_sandbox = isset($apifw_setting['enable_paypal_sandbox']) ? $apifw_setting['enable_paypal_sandbox'] : '';
$sandbox_client_id = isset($apifw_setting['sandbox_client_id']) ? $apifw_setting['sandbox_client_id'] : '';
$sandbox_secret = isset($apifw_setting['sandbox_secret']) ? $apifw_setting['sandbox_secret'] : '';
$client_id = isset($apifw_setting['client_id']) ? $apifw_setting['client_id'] : '';
$secret = isset($apifw_setting['secret']) ? $apifw_setting['secret'] : '';
$paypal_email = isset($apifw_setting['paypal_email']) ? $apifw_setting['paypal_email'] : '';
$sandbox_paypal_email = isset($apifw_setting['sandbox_paypal_email']) ? $apifw_setting['sandbox_paypal_email'] : '';

$first_name = isset($apifw_setting['first_name']) ? $apifw_setting['first_name'] : '';
$last_name = isset($apifw_setting['last_name']) ? $apifw_setting['last_name'] : '';
$compnay_name = isset($apifw_setting['compnay_name']) ? $apifw_setting['compnay_name'] : get_bloginfo('name');
$phone_number = isset($apifw_setting['phone_number']) ? $apifw_setting['phone_number'] : '';

$address_line_1 = isset($apifw_setting['address_line_1']) ? $apifw_setting['address_line_1'] : '';
$address_line_2 = isset($apifw_setting['address_line_2']) ? $apifw_setting['address_line_2'] : '';
$city = isset($apifw_setting['city']) ? $apifw_setting['city'] : '';
$post_code = isset($apifw_setting['post_code']) ? $apifw_setting['post_code'] : '';
$state = isset($apifw_setting['state']) ? $apifw_setting['state'] : '';
$country = isset($apifw_setting['country']) ? $apifw_setting['country'] : '';

$shipping_rate = isset($apifw_setting['shipping_rate']) ? $apifw_setting['shipping_rate'] : '';
$shipping_amount = isset($apifw_setting['shipping_amount']) ? $apifw_setting['shipping_amount'] : '';
$tax_rate = isset($apifw_setting['tax_rate']) ? $apifw_setting['tax_rate'] : '';
$tax_name = isset($apifw_setting['tax_name']) ? $apifw_setting['tax_name'] : '';
$note_to_recipient = isset($apifw_setting['note_to_recipient']) ? $apifw_setting['note_to_recipient'] : '';
$terms_and_condition = isset($apifw_setting['terms_and_condition']) ? $apifw_setting['terms_and_condition'] : '';
$debug_log = isset($apifw_setting['debug_log']) ? $apifw_setting['debug_log'] : '';
$paypal_sandbox_connect_url = add_query_arg(array('action' => 'lipp_paypal_sandbox_connect', 'mode' => 'SANDBOX'), admin_url('admin.php?page=apifw_settings'));
$paypal_connect_url = add_query_arg(array('action' => 'lipp_paypal_live_connect', 'mode' => 'LIVE'), admin_url('admin.php?page=apifw_settings'));
$paypal_sandbox_remote_connect_url = add_query_arg(array('rest_action' => 'connect', 'mode' => 'SANDBOX', 'return_url' => urlencode($paypal_sandbox_connect_url)), PAYPAL_INVOICE_PLUGIN_SANDBOX_API_URL);
$paypal_live_remote_connect_url = add_query_arg(array('rest_action' => 'connect', 'mode' => 'LIVE', 'return_url' => urlencode($paypal_connect_url)), PAYPAL_INVOICE_PLUGIN_LIVE_API_URL);
$apifw_sandbox_refresh_token = get_option('apifw_sandbox_refresh_token', false);
$apifw_live_refresh_token = get_option('apifw_live_refresh_token', false);
$delete_paypal_sandbox_refresh_token = add_query_arg(array('action' => 'disconnect_paypal', 'mode' => 'SANDBOX'), admin_url('admin.php?page=apifw_settings'));
$delete_paypal_live_refresh_token = add_query_arg(array('action' => 'disconnect_paypal', 'mode' => 'LIVE'), admin_url('admin.php?page=apifw_settings'));
$sandbox_email_read_only = '';
$live_email_read_only = '';
if( !empty($enable_paypal_sandbox) && $enable_paypal_sandbox == 'on') {
    $sandbox_email_read_only = !empty($apifw_sandbox_refresh_token) ? 'readonly' : '';
} elseif (empty ($enable_paypal_sandbox)) {
    $live_email_read_only = !empty($apifw_live_refresh_token) ? 'readonly' : '';
}

?>
<div class="wrap">
    <div class="container-fluid" id="angelleye-paypal-invoicing">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12">
                <form method="POST">
                    <h3><?php echo __('PayPal API Credentials', 'angelleye-paypal-invoicing'); ?></h3>
                    <div class="form-group row">
                        <div class="col-sm-4"><?php echo __('PayPal Sandbox', 'angelleye-paypal-invoicing'); ?> </div>
                        <div class="col-sm-8">
                            <label  for="apifw_enable_paypal_sandbox">
                                <input  type="checkbox" id="apifw_enable_paypal_sandbox" name="enable_paypal_sandbox" <?php checked($enable_paypal_sandbox, 'on', true); ?>>
                                <?php echo __('Enable PayPal Sandbox', 'angelleye-paypal-invoicing'); ?>
                            </label>
                        </div>
                    </div>
                    <!-- SandBox -->
                    <?php
                    if (empty($sandbox_client_id) || empty($sandbox_secret)) {
                        if ($apifw_sandbox_refresh_token == false) {
                            ?> 
                            <div class="form-group row angelleye_paypal_invoicing_sandbox_connect_box">
                                <div class="col-sm-12" >
                                    <a  href="<?php echo $paypal_sandbox_remote_connect_url; ?>">
                                        <img src="https://www.paypalobjects.com/webstatic/en_US/developer/docs/lipp/loginwithpaypalbutton.png" alt="Login with PayPal" style="cursor: pointer"/>
                                    </a> 
                                    <span class="paypal_invoice_setting_sepraer">OR</span> <a href="#" class="angelleye-invoice-toggle-settings">Add my own app credentials</a>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="form-group row angelleye_paypal_invoicing_sandbox_connect_box">
                                <div class="col-sm-10">
                                    <a class="btn btn-danger" href="<?php echo $delete_paypal_sandbox_refresh_token; ?>" role="button">Disconnect PayPal</a>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                    <div class="form-group row">
                        <label for="apifw_sandbox_client_id" class="col-sm-12 col-form-label"><?php echo __('Sandbox Client ID', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-12">
                            <input type="password" class="form-control" id="apifw_sandbox_client_id" placeholder="<?php echo __('Sandbox Client ID', 'angelleye-paypal-invoicing'); ?>" name="sandbox_client_id" value="<?php echo esc_attr($sandbox_client_id); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_sandbox_secret" class="col-sm-12 col-form-label"><?php echo __('Sandbox Secret', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-12">
                            <input type="password" class="form-control" id="apifw_sandbox_secret" placeholder="<?php echo __('Sandbox Secret', 'angelleye-paypal-invoicing'); ?>" name="sandbox_secret" value="<?php echo esc_attr($sandbox_secret); ?>">
                        </div>
                    </div>
                    <!-- Live -->
                    <?php
                    if (empty($client_id) || empty($secret)) {
                        if ($apifw_live_refresh_token == false) {
                            ?> 
                            <div class="form-group row angelleye_paypal_invoicing_live_connect_box">
                                <div class="col-sm-12" >
                                    <a  href="<?php echo $paypal_live_remote_connect_url; ?>">
                                        <img src="https://www.paypalobjects.com/webstatic/en_US/developer/docs/lipp/loginwithpaypalbutton.png" alt="Login with PayPal" style="cursor: pointer"/>
                                    </a>
                                    <span class="paypal_invoice_setting_sepraer">OR</span> <a href="#" class="angelleye-invoice-toggle-settings">Add my own app credentials</a>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="form-group row angelleye_paypal_invoicing_live_connect_box">
                                <div class="col-sm-9">
                                    <a class="btn btn-danger" href="<?php echo $delete_paypal_live_refresh_token; ?>" role="button">Disconnect PayPal</a>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                    <div class="form-group row">
                        <label for="apifw_client_id" class="col-sm-12 col-form-label"><?php echo __('Client ID', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-12">
                            <input type="password" class="form-control" id="apifw_client_id" placeholder="<?php echo __('Client ID', 'angelleye-paypal-invoicing'); ?>" name="client_id" value="<?php echo esc_attr($client_id); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_secret" class="col-sm-12 col-form-label"><?php echo __('Secret', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-12">
                            <input type="password" class="form-control" id="apifw_secret" placeholder="<?php echo __('Secret', 'angelleye-paypal-invoicing'); ?>" name="secret" value="<?php echo esc_attr($secret); ?>">
                        </div>
                    </div>
                    <h3><?php echo __('My Business Info', 'angelleye-paypal-invoicing'); ?></h3>
                    <div class="form-group row">
                        <label for="apifw_paypal_email" class="col-sm-3 col-form-label"><?php echo __('PayPal Email', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="apifw_paypal_email" placeholder="<?php echo __('PayPal Email', 'angelleye-paypal-invoicing'); ?>" name="paypal_email" value="<?php echo esc_attr($paypal_email); ?>" <?php echo $live_email_read_only; ?>>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_sandbox_paypal_email" class="col-sm-3 col-form-label"><?php echo __('PayPal Email', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="apifw_sandbox_paypal_email" placeholder="<?php echo __('PayPal Email', 'angelleye-paypal-invoicing'); ?>" name="sandbox_paypal_email" value="<?php echo esc_attr($sandbox_paypal_email); ?>" <?php echo $sandbox_email_read_only; ?>>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_first_name" class="col-sm-3 col-form-label"><?php echo __('First Name', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="apifw_first_name" placeholder="<?php echo __('First Name', 'angelleye-paypal-invoicing'); ?>" name="first_name" value="<?php echo esc_attr($first_name); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_last_name" class="col-sm-3 col-form-label"><?php echo __('Last Name', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="apifw_last_name" placeholder="<?php echo __('Last Name', 'angelleye-paypal-invoicing'); ?>" name="last_name" value="<?php echo esc_attr($last_name); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_compnay_name" class="col-sm-3 col-form-label"><?php echo __('Company Name', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="apifw_compnay_name" placeholder="<?php echo __('Company Name', 'angelleye-paypal-invoicing'); ?>" name="compnay_name" value="<?php echo esc_attr($compnay_name); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_phone_number" class="col-sm-3 col-form-label"><?php echo __('Phone Number', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="apifw_phone_number" placeholder="<?php echo __('Phone Number', 'angelleye-paypal-invoicing'); ?>" name="phone_number" value="<?php echo esc_attr($phone_number); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_address_line_1" class="col-sm-3 col-form-label"><?php echo __('Address line 1', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="apifw_address_line_1" placeholder="<?php echo __('House number and street name', 'angelleye-paypal-invoicing'); ?>" name="address_line_1" value="<?php echo esc_attr($address_line_1); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_address_line_2" class="col-sm-3 col-form-label"><?php echo __('Address line 2', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="apifw_address_line_2" placeholder="<?php echo __('Apartment, suite, unit etc.', 'angelleye-paypal-invoicing'); ?>" name="address_line_2" value="<?php echo esc_attr($address_line_2); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_city" class="col-sm-3 col-form-label"><?php echo __('City', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="apifw_city" placeholder="<?php echo __('City', 'angelleye-paypal-invoicing'); ?>" name="city" value="<?php echo esc_attr($city); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_post_code" class="col-sm-3 col-form-label"><?php echo __('Postcode / ZIP', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="apifw_post_code" placeholder="<?php echo __('', 'angelleye-paypal-invoicing'); ?>" name="post_code" value="<?php echo esc_attr($post_code); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_state" class="col-sm-3 col-form-label"><?php echo __('State / County', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="apifw_state" placeholder="<?php echo __('', 'angelleye-paypal-invoicing'); ?>" name="state" value="<?php echo esc_attr($state); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_country" class="col-sm-3 col-form-label"><?php echo __('Country', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" maxlength="2" class="form-control" id="apifw_country" placeholder="<?php echo __('', 'angelleye-paypal-invoicing'); ?>" name="country" value="<?php echo esc_attr($country); ?>">
                        </div>
                    </div>
                    <h3><?php echo __('Default Values', 'angelleye-paypal-invoicing'); ?></h3>
                    <div class="form-group row">
                        <label for="apifw_shipping_amount" class="col-sm-3 col-form-label"><?php echo __('Shipping Amount', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="apifw_shipping_amount" placeholder="<?php echo __('0.00', 'angelleye-paypal-invoicing'); ?>" name="shipping_amount" value="<?php echo esc_attr($shipping_amount); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_tax_name" class="col-sm-3 col-form-label"><?php echo __('Tax Name', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="apifw_tax_name" placeholder="" name="tax_name" value="<?php echo esc_attr($tax_name); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_tax_rate" class="col-sm-3 col-form-label"><?php echo __('Tax Rate %', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="apifw_tax_rate" placeholder="<?php echo __('%', 'angelleye-paypal-invoicing'); ?>" name="tax_rate" value="<?php echo esc_attr($tax_rate); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_note_to_recipient" class="col-sm-3 col-form-label"><?php echo __('Note to Recipient', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="apifw_note_to_recipient" placeholder="<?php echo __('Note to Recipient', 'angelleye-paypal-invoicing'); ?>" name="note_to_recipient" value="<?php echo esc_attr($note_to_recipient); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_terms_and_condition" class="col-sm-3 col-form-label"><?php echo __('Terms and Conditions', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="apifw_terms_and_condition" placeholder="<?php echo __('Terms and Conditions', 'angelleye-paypal-invoicing'); ?>" name="terms_and_condition" value="<?php echo $terms_and_condition; ?>">
                        </div>
                    </div>
                    <h3><?php echo __('Log Event', 'angelleye-paypal-invoicing'); ?></h3>
                    <div class="form-group row">
                        <div class="col-sm-3"><?php echo __('Debug Log', 'angelleye-paypal-invoicing'); ?> </div>
                        <div class="col-sm-9">
                            <label  for="apifw_debug_log">
                                <input  type="checkbox" id="apifw_debug_log" name="debug_log" <?php checked($debug_log, 'on', true); ?>>
                                <?php echo __('Enable logging', 'angelleye-paypal-invoicing'); ?>
                            </label>
                            <small id="passwordHelpBlock" class="form-text text-muted">
                                <?php echo __('Log PayPal events, inside', 'angelleye-paypal-invoicing'); ?> <code><?php echo ANGELLEYE_PAYPAL_INVOICING_LOG_DIR; ?> </code>
                            </small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apifw_delete_logs" class="col-sm-3 col-form-label"><?php echo __('Delete Logs', 'angelleye-paypal-invoicing'); ?></label>
                        <div class="col-sm-9">
                            <button name="apifw_delete_logs" type="submit" value="Delete Logs" class="btn btn-danger"><?php echo __('Delete Logs', 'angelleye-paypal-invoicing'); ?></button>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <button name="apifw_setting_submit" type="submit" value="save" class="btn btn-primary"><?php echo __('Save changes', 'angelleye-paypal-invoicing'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="card border-light">
                    <div class="card-header"><h4><?php echo __('Instructions', 'angelleye-paypal-invoicing'); ?></h4></div>
                    <div class="card-body">
                        <ol>
                            <li><?php echo __('Check the box to Enable PayPal Sandbox if you are going to configure the plugin with a test account.  Otherwise, leave it unchecked.', 'angelleye-paypal-invoicing'); ?></li>
                            <li><?php echo __('Click the Log In with PayPal button to quickly and easily connect your PayPal account to the Invoicing plugin.', 'angelleye-paypal-invoicing'); ?></li>
                            <ol style="list-style-type:lower-alpha">
                                <li><?php echo __('If you have your own app configured with PayPal and would prefer to use that, click the link to “add my own app credentials”.', 'angelleye-paypal-invoicing'); ?></li>
                            </ol>
                            <li><?php echo __('Enter details for Address and Default values to use on new invoices.', 'angelleye-paypal-invoicing'); ?></li>
                            <li><?php echo __('Save Changes.', 'angelleye-paypal-invoicing'); ?></li>
                            <li><?php echo __('Click the PayPal Invoicing item in the WordPress admin menu bar.', 'angelleye-paypal-invoicing'); ?></li>
                            <li><?php echo __('Click Add Invoice to create your first PayPal invoice!', 'angelleye-paypal-invoicing'); ?></li>
                        </ol>
                    </div>
                </div>
                <br>
                <div class="card border-light">
                    <div class="card-header"><h4><?php echo __('WooCommerce Compatibility', 'angelleye-paypal-invoicing'); ?></h4></div>
                    <div class="card-body">
                        <ol>
                            <li><?php echo __('Create an order in WooCommerce.', 'angelleye-paypal-invoicing'); ?></li>
                            <li><?php echo __('From the Order Actions menu, choose to:', 'angelleye-paypal-invoicing'); ?></li>
                            <ol style="list-style-type:lower-alpha">
                                <li><?php echo __('Save PayPal Invoice Draft', 'angelleye-paypal-invoicing'); ?></li>
                                <li><?php echo __('Send PayPal Invoice', 'angelleye-paypal-invoicing'); ?></li>
                            </ol>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
