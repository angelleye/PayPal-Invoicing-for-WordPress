<?php
$all_invoice_data = get_post_meta($post->ID, 'all_invoice_data', true);
$status = get_post_meta($post->ID, 'status', true);
$apifw_company_logo = ( isset($invoice['logo_url']) && !empty($invoice['logo_url']) ) ? $invoice['logo_url'] : '';
?>
<div class="container" id="invoice_view_table">
    <div class="card">
        <span class="folded-corner"></span>
        <div class="card-body">
            <br>
            <div class="row">
                <div class="col-sm-8">
                    <?php if (!empty($apifw_company_logo)) { ?>
                        <img src="<?php echo $apifw_company_logo; ?>" class="rounded img-fluid float-left">
                    <?php } ?>
                    <br>
                    <div class="mt30-invoice clearboth">
                        <?php echo isset($invoice['merchant_info']['address']['first_name']) ? $invoice['merchant_info']['address']['first_name'] : ''; ?>
                        <?php echo isset($invoice['merchant_info']['address']['last_name']) ? $invoice['merchant_info']['address']['last_name'] : ''; ?>
                    </div>
                    <?php echo isset($invoice['merchant_info']['address']['line1']) ? '<div>' . $invoice['merchant_info']['address']['line1'] . '</div>' : ''; ?>
                    <?php echo isset($invoice['merchant_info']['address']['line2']) ? '<div>' . $invoice['merchant_info']['address']['line2'] . '</div>' : ''; ?>
                    <div>
                        <?php echo isset($invoice['merchant_info']['address']['city']) ? $invoice['merchant_info']['address']['city'] : ''; ?>
                        <?php echo isset($invoice['merchant_info']['address']['state']) ? $invoice['merchant_info']['address']['state'] : ''; ?>
                        <?php echo isset($invoice['merchant_info']['address']['postal_code']) ? $invoice['merchant_info']['address']['postal_code'] : ''; ?>
                    </div>
                    <div>
                        <?php echo isset($invoice['merchant_info']['address']['country_code']) ? $invoice['merchant_info']['address']['country_code'] : ''; ?>
                    </div>
                    <?php echo isset($invoice['merchant_info']['email']) ? '<div>' . $invoice['merchant_info']['email'] . '</div>' : ''; ?>
                    <?php
                    if (!empty($invoice['merchant_info']['phone'])) {
                        echo '<div>+' . $invoice['merchant_info']['phone']['country_code'] . '  ' . $invoice['merchant_info']['phone']['national_number'] . '</div>';
                    }
                    ?>
                </div>
                <div class="col-sm-4" style="text-align: right;">
                    <div class="pageCurl" ><?php echo __('INVOICE', 'angelleye-paypal-invoicing'); ?></div>
                    <?php
                    $invoice_status_array = pifw_get_invoice_status_name_and_class($invoice['status']);
                    if (!empty($invoice_status_array)) :
                        ?>
                        <div class="row">
                            <span class="col-sm-6"></span>
                            <span style="text-align: right;" class="invoiceStatus <?php echo isset($invoice_status_array['class']) ? $invoice_status_array['class'] : 'isDraft'; ?>">
                                <?php echo $invoice_status_array['lable']; ?>
                            </span>
                            <br>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <span class="col-sm-6"></span>
                        <div class="btn-group invoice-action-group">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo __('Action', 'angelleye-paypal-invoicing'); ?>
                        </button>
                        <div class="dropdown-menu">
                            <?php
                            if ($payer_view_url = $this->angelleye_paypal_invoicing_get_payer_view($all_invoice_data)) {
                                echo '<a class="dropdown-item" target="_blank" href="' . $payer_view_url . '">' . __('View PayPal Invoice', 'angelleye-paypal-invoicing') . '</a>';
                            }
                            if ($status == 'DRAFT') {
                                echo '<a class="dropdown-item" href="' . add_query_arg(array('post_id' => $post->ID, 'invoice_action' => 'paypal_invoice_send')) . '">' . __('Send Invoice', 'angelleye-paypal-invoicing') . '</a>';
                                echo '<a class="dropdown-item" href="' . add_query_arg(array('post_id' => $post->ID, 'invoice_action' => 'paypal_invoice_delete')) . '">' . __('Delete Invoice', 'angelleye-paypal-invoicing') . '</a>';
                            }
                            if ($status == 'PARTIALLY_PAID' || $status == 'SCHEDULED' || $status == 'SENT') {
                                echo '<a class="dropdown-item" href="' . add_query_arg(array('post_id' => $post->ID, 'invoice_action' => 'paypal_invoice_remind')) . '">' . __('Send Invoice Reminder', 'angelleye-paypal-invoicing') . '</a>';
                            }
                            if ($status == 'SENT') {
                                echo '<a class="dropdown-item" href="' . add_query_arg(array('post_id' => $post->ID, 'invoice_action' => 'paypal_invoice_cancel')) . '">' . __('Cancel Invoice', 'angelleye-paypal-invoicing') . '</a>';
                            }
                            ?>
                        </div>
                    </div>
                    </div>
                    
                    <?php if (!empty($invoice['number'])) : ?>
                        <div class="row">
                            <span class="col-sm-6 text-right"><?php echo __('Invoice #:', 'angelleye-paypal-invoicing'); ?></span>
                            <span><?php echo $invoice['number']; ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['invoice_date'])) : ?>
                        <div class="row">
                            <span class="col-sm-6 text-right"><?php echo __('Invoice date:', 'angelleye-paypal-invoicing'); ?></span>
                            <span><?php echo date_i18n(get_option('date_format'), strtotime($invoice['invoice_date'])); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['reference'])) : ?>
                        <div class="row">
                            <span class="col-sm-6 text-right"><?php echo __('Reference:', 'angelleye-paypal-invoicing'); ?></span>
                            <span><?php echo $invoice['reference']; ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['payment_term']['due_date'])) : ?>
                        <div class="row">
                            <span class="col-sm-6 text-right"><?php echo __('Due date:', 'angelleye-paypal-invoicing'); ?></span>
                            <span><?php echo date_i18n(get_option('date_format'), strtotime($invoice['payment_term']['due_date'])); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <br>
            <div class="sectionBottom"></div>
            <br>
            <div class="row mb-4">
                <div class="col-sm-6">
                    <?php
                    if (!empty($invoice['billing_info'][0]['email'])) {
                        echo '<h4 class="mb-3">Bill To</h4>';
                    }
                    ?>
                    <?php echo isset($invoice['billing_info'][0]['business_name']) ? '<div>' . $invoice['billing_info'][0]['business_name'] . '</div>' : ''; ?>
                    <div>
                        <?php echo isset($invoice['billing_info'][0]['first_name']) ? $invoice['billing_info'][0]['first_name'] : ''; ?>
                        <?php echo isset($invoice['billing_info'][0]['last_name']) ? $invoice['billing_info'][0]['last_name'] : ''; ?>
                    </div>
                    <?php echo isset($invoice['billing_info'][0]['address']['line1']) ? '<div>' . $invoice['billing_info'][0]['address']['line1'] . '</div>' : ''; ?>
                    <?php echo isset($invoice['billing_info'][0]['address']['line2']) ? '<div>' . $invoice['billing_info'][0]['address']['line2'] . '</div>' : ''; ?>
                    <div>
                        <?php echo isset($invoice['billing_info'][0]['address']['city']) ? $invoice['billing_info'][0]['address']['city'] : ''; ?>
                        <?php echo isset($invoice['billing_info'][0]['address']['state']) ? $invoice['billing_info'][0]['address']['state'] : ''; ?>
                        <?php echo isset($invoice['billing_info'][0]['address']['postal_code']) ? $invoice['billing_info'][0]['address']['postal_code'] : ''; ?>
                    </div>
                    <div>
                        <?php echo isset($invoice['billing_info'][0]['address']['country_code']) ? $invoice['billing_info'][0]['address']['country_code'] : ''; ?>
                    </div>
                    <?php echo isset($invoice['billing_info'][0]['email']) ? '<div>' . $invoice['billing_info'][0]['email'] . '</div>' : ''; ?>
                    <?php
                    if (!empty($invoice['billing_info'][0]['address']['phone']['country_code'])) {
                        echo '<div>' . $invoice['billing_info'][0]['address']['phone']['country_code'] . '  ' . $invoice['billing_info'][0]['address']['phone']['national_number'] . '</div>';
                    }
                    ?>
                </div>
                <div class="col-sm-6">
                    <?php
                    if (!empty($invoice['shipping_info']['first_name'])) {
                        echo '<h4 class="mb-3">Ship To</h4>';
                    }
                    ?>
                    <?php echo isset($invoice['shipping_info']['business_name']) ? '<div>' . $invoice['shipping_info']['business_name'] . '</div>' : ''; ?>
                    <div>
                        <?php echo isset($invoice['shipping_info']['first_name']) ? $invoice['shipping_info']['first_name'] : ''; ?>
                        <?php echo isset($invoice['shipping_info']['last_name']) ? $invoice['shipping_info']['last_name'] : ''; ?>
                    </div>
                    <?php echo isset($invoice['shipping_info']['address']['line1']) ? '<div>' . $invoice['shipping_info']['address']['line1'] . '</div>' : ''; ?>
                    <?php echo isset($invoice['shipping_info']['address']['line2']) ? '<div>' . $invoice['shipping_info']['address']['line2'] . '</div>' : ''; ?>
                    <div>
                        <?php echo isset($invoice['shipping_info']['address']['city']) ? $invoice['shipping_info']['address']['city'] : ''; ?>
                        <?php echo isset($invoice['shipping_info']['address']['state']) ? $invoice['shipping_info']['address']['state'] : ''; ?>
                        <?php echo isset($invoice['shipping_info']['address']['postal_code']) ? $invoice['shipping_info']['address']['postal_code'] : ''; ?>
                    </div>
                    <div>
                        <?php echo isset($invoice['shipping_info']['address']['country_code']) ? $invoice['shipping_info']['address']['country_code'] : ''; ?>
                    </div>
                    <?php echo isset($invoice['shipping_info']['email']) ? '<div>' . $invoice['shipping_info']['email'] . '</div>' : ''; ?>
                    <?php
                    if (!empty($invoice['shipping_info']['phone'])) {
                        echo '<div>+' . $invoice['shipping_info']['phone']['country_code'] . '  ' . $invoice['shipping_info']['phone']['national_number'] . '</div>';
                    }
                    ?>
                </div>
            </div>
            <div class="table-responsive-sm">
                <table class="table" id="paypal_invoice_view_table_format">
                    <thead>
                        <tr>
                            <th class="itemdescription"><?php echo __('Description', 'angelleye-paypal-invoicing'); ?></th>
                            <th class="itemquantity text-right"><?php echo __('Quantity', 'angelleye-paypal-invoicing'); ?></th>
                            <th class="itemprice text-right"><?php echo __('Price', 'angelleye-paypal-invoicing'); ?></th>
                            <th class="itemamount text-right"><?php echo __('Amount', 'angelleye-paypal-invoicing'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $invoice_total_array = array();
                        $sub_total = 0;
                        if (!empty($invoice['items'])) {
                            foreach ($invoice['items'] as $key => $invoice_item) {
                                $description_html = '';
                                if (!empty($invoice_item['description'])) {
                                    $description = $invoice_item['description'];
                                } else {
                                    $description = '';
                                }
                                echo '<tr>';
                                echo '<td class="itemdescription">';
                                $description_html .= '<div class="wrap">' . $invoice_item['name'];
                                $description_html .= !empty($description) ? '<br>' . $description : '';
                                echo $description_html;
                                echo '</div></td>';
                                echo '<td class="itemquantity text-right">' . $invoice_item['quantity'] . '</td>';
                                echo '<td class="itemprice text-right">' . pifw_get_currency_symbol($invoice_item['unit_price']['currency']) . $invoice_item['unit_price']['value'] . '</td>';
                                echo '<td class="itemamount text-right">' . pifw_get_currency_symbol($invoice_item['unit_price']['currency']) . number_format($invoice_item['quantity'] * $invoice_item['unit_price']['value'], 2) . '</td>';
                                echo '</tr>';
                                $sub_total = $sub_total + ($invoice_item['quantity'] * $invoice_item['unit_price']['value']);
                                if (!empty($invoice_item['tax'])) {
                                    $invoice_total_array['tax'][$key] = $invoice_item['tax'];
                                }
                            }
                            if (!empty($invoice['discount'])) {
                                $invoice_total_array['discount'] = $invoice['discount'];
                            }
                            if (!empty($invoice['shipping_cost'])) {
                                $invoice_total_array['shipping_cost'] = $invoice['shipping_cost'];
                            }

                            $invoice_total_array['sub_total'] = array('currency' => $invoice_item['unit_price']['currency'], 'value' => $sub_total);
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-lg-4 col-sm-5">
                </div>
                <div class="col-lg-4 col-sm-5 ml-auto paypal_invoice_view_table_format_total">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <?php if (!empty($invoice_total_array['sub_total'])) : ?>
                                    <tr>
                                        <td class="left">
                                            <?php echo __('Subtotal', 'angelleye-paypal-invoicing'); ?>
                                        </td>
                                        <td class="right"><?php echo pifw_get_currency_symbol($invoice_total_array['sub_total']['currency']) . number_format($invoice_total_array['sub_total']['value'], 2); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (!empty($invoice_total_array['shipping_cost'])) : ?>
                                    <tr>
                                        <td class="left">
                                            <?php echo __('Shipping', 'angelleye-paypal-invoicing'); ?>
                                        </td>
                                        <td class="right"><?php echo pifw_get_currency_symbol($invoice_total_array['shipping_cost']['amount']['currency']) . number_format($invoice_total_array['shipping_cost']['amount']['value'], 2); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (!empty($invoice_total_array['tax'])) : ?>
                                    <?php
                                    $new_tax_data = array();
                                    foreach ($invoice_total_array['tax'] as $key_index => $value_data) {
                                        if (!isset($new_tax_data[$value_data['name']][$value_data['percent']])) {
                                            $new_tax_data[$value_data['name']][$value_data['percent']] = $value_data;
                                        } else {
                                            $new_amount_value = $new_tax_data[$value_data['name']][$value_data['percent']]['amount']['value'] + $value_data['amount']['value'];
                                            $value_data['amount']['value'] = $new_amount_value;
                                            $new_tax_data[$value_data['name']][$value_data['percent']] = $value_data;
                                        }
                                    }
                                    if (!empty($new_tax_data)) {
                                        foreach ($new_tax_data as $tax_index => $tax_data_value) {
                                            if (!empty($tax_data_value)) {
                                                foreach ($tax_data_value as $key => $tax_data) {
                                                    echo '<tr>';
                                                    echo '<td class="left">';
                                                    echo $tax_data['name'] . ' (' . $tax_data['percent'] . '%)';
                                                    echo '</td>';
                                                    echo '<td class="right">' . pifw_get_currency_symbol($tax_data['amount']['currency']) . number_format($tax_data['amount']['value'], 2) . '</td>';
                                                    echo '</tr>';
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                <?php endif; ?>
                                <?php if (!empty($invoice_total_array['discount'])) : ?>
                                    <tr>
                                        <td class="left">
                                            <?php echo __('Discount', 'angelleye-paypal-invoicing'); ?>
                                        </td>
                                        <?php echo '<td class="right">-' . pifw_get_currency_symbol($invoice_total_array['discount']['amount']['currency']) . number_format($invoice_total_array['discount']['amount']['value'], 2) . '</td>'; ?>
                                    </tr>
                                <?php endif; ?>
                                <?php if (!empty($invoice['total_amount'])) : ?>
                                    <tr>
                                        <td class="left total">
                                            <strong><?php echo __('Total', 'angelleye-paypal-invoicing'); ?></strong>
                                        </td>
                                        <?php echo '<td class="right total"><strong>' . pifw_get_currency_symbol($invoice['total_amount']['currency']) . number_format($invoice['total_amount']['value'], 2) . ' ' . $invoice['total_amount']['currency'] . '</strong></td>'; ?>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <br>
            <div class="sectionBottom"></div>
            <br>
            <div class="row">
                <?php if (!empty($invoice['note'])) : ?>
                    <div class="col-xs-6 col-sm-6">
                        <div>
                            <h4 class="headline"><?php echo __('Notes', 'angelleye-paypal-invoicing'); ?></h4>
                            <p class="notes"><?php echo $invoice['note']; ?></p>
                        </div>
                    </div><!-- close note col-xs -->
                <?php endif; ?>
                <?php if (!empty($invoice['terms'])) : ?>
                    <div class="col-xs-6 col-sm-6">
                        <div>
                            <h4 class="headline"><?php echo __('Terms and Conditions', 'angelleye-paypal-invoicing'); ?></h4>
                            <p class="terms"><?php echo $invoice['terms']; ?></p>
                        </div>
                    </div> <!-- close terms col-xs -->
                <?php endif; ?>
            </div>
            <?php if (!empty($invoice['merchant_memo'])) : ?>
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <div>
                            <h4 class="headline"><?php echo __('Memo', 'angelleye-paypal-invoicing'); ?></h4>
                            <p class="notes"><?php echo $invoice['merchant_memo']; ?></p>
                        </div>
                    </div><!-- close note col-xs -->
                </div>
            <?php endif; ?>
            <?php
            $invoice_history = $this->get_invoice_notes($post->ID);
            if (!empty($invoice_history)) :
                ?>
                <br><br><br><br>
                <div class="row">
                    <div class="col-sm-12">
                        <h4 class="headline">History</h4>
                        <div class="table-responsive-sm">
                            <div class="table-responsive">
                                <table class="table">
                                    <?php
                                    foreach ($invoice_history as $key => $history) {
                                        echo '<tr>';
                                        echo '<td>' . date_i18n(get_option('date_format'), strtotime($history->comment_date)) . '</td>';
                                        echo '<td>' . $history->comment_content . '</td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>