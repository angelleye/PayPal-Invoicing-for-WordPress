<?php
$all_invoice_data = get_post_meta($post->ID, 'all_invoice_data', true);
$status = get_post_meta($post->ID, 'status', true);
$apifw_company_logo = ( isset($invoice['invoicer']['logo_url']) && !empty($invoice['invoicer']['logo_url']) ) ? $invoice['invoicer']['logo_url'] : '';
?>
<div class="container" id="invoice_view_table">
    <div class="card">
        <span class="folded-corner"></span>
        <div class="card-body">
            <br>
            <div class="row">
                <div class="col-sm-8">
                    <?php if (!empty($apifw_company_logo)) { ?>
                        <img src="<?php echo $apifw_company_logo; ?>" class="rounded img-fluid float-left angelleye-invoice-company-logo">
                    <?php } ?>
                    <br>
                    <div class="mt30-invoice clearboth">
                        <?php echo isset($invoice['invoicer']['name']['given_name']) ? $invoice['invoicer']['name']['given_name'] : ''; ?>
                        <?php echo isset($invoice['invoicer']['name']['surname']) ? $invoice['invoicer']['name']['surname'] : ''; ?>
                    </div>
                    <?php echo isset($invoice['invoicer']['address']['address_line_1']) ? '<div>' . $invoice['invoicer']['address']['address_line_1'] . '</div>' : ''; ?>
                    <?php echo isset($invoice['invoicer']['address']['address_line_2']) ? '<div>' . $invoice['invoicer']['address']['address_line_2'] . '</div>' : ''; ?>
                    <div>
                        <?php echo isset($invoice['invoicer']['address']['admin_area_2']) ? $invoice['invoicer']['address']['admin_area_2'] : ''; ?>
                        <?php echo isset($invoice['invoicer']['address']['admin_area_1']) ? $invoice['invoicer']['address']['admin_area_1'] : ''; ?>
                        <?php echo isset($invoice['invoicer']['address']['postal_code']) ? $invoice['invoicer']['address']['postal_code'] : ''; ?>
                    </div>
                    <div>
                        <?php echo isset($invoice['invoicer']['address']['country_code']) ? $invoice['invoicer']['address']['country_code'] : ''; ?>
                    </div>
                    <?php echo isset($invoice['invoicer']['email_address']) ? '<div>' . $invoice['invoicer']['email_address'] . '</div>' : ''; ?>
                    <?php
                    if (isset($invoice['invoicer']['phones'][0]['country_code']) && !empty($invoice['invoicer']['phones'][0]['country_code'])) {
                        echo '<div>+' . $invoice['invoicer']['phones'][0]['country_code'] . '  ' . $invoice['invoicer']['phones'][0]['national_number'] . '</div>';
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
                                <?php echo $invoice_status_array['label']; ?>
                            </span>
                            <br>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <span class="col-sm-6"></span>
                        <?php if (isset($invoice['status']) && $invoice['status'] != 'PAID') : ?>
                            <div class="btn-group invoice-action-group">
                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php echo __('Action', 'angelleye-paypal-invoicing'); ?>
                                </button>
                                <div class="dropdown-menu">
                                    <?php
                                    if (isset($invoice['detail']['metadata']['recipient_view_url']) && !empty($invoice['detail']['metadata']['recipient_view_url'])) {
                                        echo '<a class="dropdown-item" target="_blank" href="' . $invoice['detail']['metadata']['recipient_view_url'] . '">' . __('View PayPal Invoice', 'angelleye-paypal-invoicing') . '</a>';
                                    }
                                    if ($status == 'DRAFT') {
                                        echo '<a class="dropdown-item" href="' . add_query_arg(array('post_id' => $post->ID, 'invoice_action' => 'paypal_invoice_send')) . '">' . __('Send Invoice', 'angelleye-paypal-invoicing') . '</a>';
                                        echo '<a class="dropdown-item" href="' . add_query_arg(array('post_id' => $post->ID, 'invoice_action' => 'paypal_invoice_delete')) . '">' . __('Delete Invoice', 'angelleye-paypal-invoicing') . '</a>';
                                    }
                                    if ($status == 'PARTIALLY_PAID' || $status == 'SCHEDULED' || $status == 'SENT') {
                                        echo '<a class="dropdown-item" href="' . add_query_arg(array('post_id' => $post->ID, 'invoice_action' => 'paypal_invoice_remind')) . '">' . __('Send Invoice Reminder', 'angelleye-paypal-invoicing') . '</a>';
                                    }
                                    if ($status == 'PARTIALLY_PAID' || $status == 'SCHEDULED' || $status == 'SENT' || $status == 'UNPAID') {
                                        echo '<a class="dropdown-item" data-toggle="modal" data-target="#Payment_RecordModal" href="">' . __('Record a payment', 'angelleye-paypal-invoicing') . '</a>';
                                    }
                                    if ($status == 'SENT' || $status == 'UNPAID') {
                                        echo '<a class="dropdown-item" href="' . add_query_arg(array('post_id' => $post->ID, 'invoice_action' => 'paypal_invoice_cancel')) . '">' . __('Cancel Invoice', 'angelleye-paypal-invoicing') . '</a>';
                                    }
                                    if ($status == 'PARTIALLY_PAID' || $status == 'PAID' || $status == 'PARTIALLY_REFUNDED') { 
                                        echo '<a class="dropdown-item" data-toggle="modal" data-target="#Refund_RecordModal" href="">' . __('Record a Refund', 'angelleye-paypal-invoicing') . '</a>';
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($invoice['detail']['invoice_number'])) : ?>
                        <div class="row">
                            <span class="col-sm-6 text-right"><?php echo __('Invoice #:', 'angelleye-paypal-invoicing'); ?></span>
                            <span><?php echo $invoice['detail']['invoice_number']; ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['detail']['invoice_date'])) : ?>
                        <div class="row">
                            <span class="col-sm-6 text-right"><?php echo __('Invoice date:', 'angelleye-paypal-invoicing'); ?></span>
                            <span><?php echo date_i18n(get_option('date_format'), strtotime($invoice['detail']['invoice_date'])); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['detail']['reference'])) : ?>
                        <div class="row">
                            <span class="col-sm-6 text-right"><?php echo __('Reference:', 'angelleye-paypal-invoicing'); ?></span>
                            <span><?php echo $invoice['detail']['reference']; ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['detail']['payment_term']['due_date'])) : ?>
                        <div class="row">
                            <span class="col-sm-6 text-right"><?php echo __('Due date:', 'angelleye-paypal-invoicing'); ?></span>
                            <span><?php echo date_i18n(get_option('date_format'), strtotime($invoice['detail']['payment_term']['due_date'])); ?></span>
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
                    if (!empty($invoice['primary_recipients'][0]['billing_info']['address'])) {
                        echo '<h4 class="mb-3">Bill To</h4>';
                    }
                    ?>
                    <div>
                        <?php echo isset($invoice['primary_recipients'][0]['billing_info']['name']['given_name']) ? $invoice['primary_recipients'][0]['billing_info']['name']['given_name'] : ''; ?>
                        <?php echo isset($invoice['primary_recipients'][0]['billing_info']['name']['surname']) ? $invoice['primary_recipients'][0]['billing_info']['name']['surname'] : ''; ?>
                    </div>
                    <?php echo isset($invoice['primary_recipients'][0]['billing_info']['address']['address_line_1']) ? '<div>' . $invoice['primary_recipients'][0]['billing_info']['address']['address_line_1'] . '</div>' : ''; ?>
                    <?php echo isset($invoice['primary_recipients'][0]['billing_info']['address']['address_line_2']) ? '<div>' . $invoice['primary_recipients'][0]['billing_info']['address']['address_line_2'] . '</div>' : ''; ?>
                    <div>
                        <?php echo isset($invoice['primary_recipients'][0]['billing_info']['address']['admin_area_2']) ? $invoice['primary_recipients'][0]['billing_info']['address']['admin_area_2'] : ''; ?>
                        <?php echo isset($invoice['primary_recipients'][0]['billing_info']['address']['admin_area_1']) ? $invoice['primary_recipients'][0]['billing_info']['address']['admin_area_1'] : ''; ?>
                        <?php echo isset($invoice['primary_recipients'][0]['billing_info']['address']['postal_code']) ? $invoice['primary_recipients'][0]['billing_info']['address']['postal_code'] : ''; ?>
                    </div>
                    <div>
                        <?php echo isset($invoice['primary_recipients'][0]['billing_info']['address']['country_code']) ? $invoice['primary_recipients'][0]['billing_info']['address']['country_code'] : ''; ?>
                    </div>
                    <?php echo isset($invoice['primary_recipients'][0]['billing_info']['email_address']) ? '<div>' . $invoice['primary_recipients'][0]['billing_info']['email_address'] . '</div>' : ''; ?>
                    <?php
                    if (!empty($invoice['primary_recipients'][0]['billing_info']['phones']['country_code'])) {
                        echo '<div>' . $invoice['primary_recipients'][0]['billing_info']['phones']['country_code'] . '  ' . $invoice['primary_recipients'][0]['billing_info']['phones']['national_number'] . '</div>';
                    }
                    ?>
                </div>
                <div class="col-sm-6">
                    <?php
                    if (!empty($invoice['primary_recipients'][0]['shipping_info']['name']['given_name'])) {
                        echo '<h4 class="mb-3">Ship To</h4>';
                    }
                    ?>
                    <div>
                        <?php echo isset($invoice['primary_recipients'][0]['shipping_info']['name']['given_name']) ? $invoice['primary_recipients'][0]['shipping_info']['name']['given_name'] : ''; ?>
                        <?php echo isset($invoice['primary_recipients'][0]['shipping_info']['name']['surname']) ? $invoice['primary_recipients'][0]['shipping_info']['name']['surname'] : ''; ?>
                    </div>
                    <?php echo isset($invoice['primary_recipients'][0]['shipping_info']['address']['address_line_1']) ? '<div>' . $invoice['primary_recipients'][0]['shipping_info']['address']['address_line_1'] . '</div>' : ''; ?>
                    <?php echo isset($invoice['primary_recipients'][0]['shipping_info']['address']['address_line_2']) ? '<div>' . $invoice['primary_recipients'][0]['shipping_info']['address']['address_line_2'] . '</div>' : ''; ?>
                    <div>
                        <?php echo isset($invoice['primary_recipients'][0]['shipping_info']['address']['admin_area_2']) ? $invoice['primary_recipients'][0]['shipping_info']['address']['admin_area_2'] : ''; ?>
                        <?php echo isset($invoice['primary_recipients'][0]['shipping_info']['address']['admin_area_1']) ? $invoice['primary_recipients'][0]['shipping_info']['address']['admin_area_1'] : ''; ?>
                        <?php echo isset($invoice['primary_recipients'][0]['shipping_info']['address']['postal_code']) ? $invoice['primary_recipients'][0]['shipping_info']['address']['postal_code'] : ''; ?>
                    </div>
                    <div>
                        <?php echo isset($invoice['primary_recipients'][0]['shipping_info']['address']['country_code']) ? $invoice['primary_recipients'][0]['shipping_info']['address']['country_code'] : ''; ?>
                    </div>
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
                                echo '<td class="itemprice text-right">' . pifw_get_currency_symbol($invoice_item['unit_amount']['currency_code']) . $invoice_item['unit_amount']['value'] . '</td>';
                                echo '<td class="itemamount text-right">' . pifw_get_currency_symbol($invoice_item['unit_amount']['currency_code']) . number_format($invoice_item['quantity'] * $invoice_item['unit_amount']['value'], 2) . '</td>';
                                echo '</tr>';
                                if (!empty($invoice_item['tax'])) {
                                    $invoice_total_array['tax'][$key] = $invoice_item['tax'];
                                }
                            }
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
                                <?php if (!empty($invoice['amount']['breakdown']['item_total'])) : ?>
                                    <tr>
                                        <td class="left">
                                            <?php echo __('Subtotal', 'angelleye-paypal-invoicing'); ?>
                                        </td>
                                        <td class="right"><?php echo pifw_get_currency_symbol($invoice['amount']['breakdown']['item_total']['currency_code']) . number_format($invoice['amount']['breakdown']['item_total']['value'], 2); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (!empty($invoice['amount']['breakdown']['shipping']['amount'])) : ?>
                                    <tr>
                                        <td class="left">
                                            <?php echo __('Shipping', 'angelleye-paypal-invoicing'); ?>
                                        </td>
                                        <td class="right"><?php echo pifw_get_currency_symbol($invoice['amount']['breakdown']['shipping']['amount']['currency_code']) . number_format($invoice['amount']['breakdown']['shipping']['amount']['value'], 2); ?></td>
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
                                                    echo '<td class="right">' . pifw_get_currency_symbol($tax_data['amount']['currency_code']) . number_format($tax_data['amount']['value'], 2) . '</td>';
                                                    echo '</tr>';
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                <?php endif; ?>
                                <?php if (!empty($invoice['amount']['breakdown']['discount']['invoice_discount']['amount'])) : ?>
                                    <tr>
                                        <td class="left">
                                            <?php echo __('Discount', 'angelleye-paypal-invoicing'); ?>
                                        </td>
                                        <?php echo '<td class="right">-' . pifw_get_currency_symbol($invoice['amount']['breakdown']['discount']['invoice_discount']['amount']['currency_code']) . number_format(abs($invoice['amount']['breakdown']['discount']['invoice_discount']['amount']['value']), 2) . '</td>'; ?>
                                    </tr>
                                <?php endif; ?>
                                <?php if (!empty($invoice['due_amount']['value'])) { ?>
                                    <tr>
                                        <td class="left">
                                            <?php echo __('Total', 'angelleye-paypal-invoicing'); ?>
                                        </td>
                                        <?php echo '<td class="right">' . pifw_get_currency_symbol($invoice['amount']['currency_code']) . number_format($invoice['amount']['value'], 2) . ' ' . $invoice['amount']['currency_code'] . '</td>'; ?>
                                    </tr>
                                     <?php if (!empty($invoice['payments']['paid_amount'])) { ?>
                                     <tr>
                                        <td class="left">
                                            <?php echo __('Amount paid', 'angelleye-paypal-invoicing'); ?>
                                        </td>
                                        <?php echo '<td class="right">-' . pifw_get_currency_symbol($invoice['payments']['paid_amount']['currency_code']) . number_format(abs($invoice['payments']['paid_amount']['value']), 2) . '</td>'; ?>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="left total">
                                             <strong><?php echo __('Amount due', 'angelleye-paypal-invoicing'); ?> </strong>
                                        </td>
                                        <?php echo '<td class="right total"><strong>' . pifw_get_currency_symbol($invoice['due_amount']['currency_code']) . number_format($invoice['due_amount']['value'], 2) . ' ' . $invoice['due_amount']['currency_code'] . '</strong></td>'; ?>
                                    </tr>
                                   
                                <?php  } elseif (!empty($invoice['amount']['value'])) { ?>
                                    <tr>
                                        <td class="left total">
                                            <strong><?php echo __('Total', 'angelleye-paypal-invoicing'); ?></strong>
                                        </td>
                                        <?php echo '<td class="right total"><strong>' . pifw_get_currency_symbol($invoice['amount']['currency_code']) . number_format($invoice['amount']['value'], 2) . ' ' . $invoice['amount']['currency_code'] . '</strong></td>'; ?>
                                    </tr>
                                <?php } ?>
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
<div class="modal fade" id="Payment_RecordModal" tabindex="-1" role="dialog" aria-labelledby="Payment_RecordModalLabel" aria-hidden="true">
    <div class="modal-dialog invoice-table" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="Payment_RecordModalLabel"><?php echo __('Record a payment', 'angelleye-paypal-invoicing'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <input type="hidden" name="angelleye_paypal_invoice_id" id="angelleye_paypal_invoice_id" value="<?php echo $invoice['id']; ?>">
                    <?php if (!empty($invoice['detail']['invoice_number'])) : ?>
                        <div class="row">
                            <span class="col-4"><strong><?php echo __('Invoice number:', 'angelleye-paypal-invoicing'); ?></strong></span>
                            <span class="col-4"><?php echo $invoice['detail']['invoice_number']; ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['due_amount']['value'])) : ?>
                        <div class="row">
                            <span class="col-4"><strong><?php echo __('Amount due:', 'angelleye-paypal-invoicing'); ?></strong></span>
                            <span class="col-4"><?php echo pifw_get_currency_symbol($invoice['due_amount']['currency_code']) . $invoice['due_amount']['value']; ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="row mt30-invoice">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="angelleye_record_payment_amount"><strong><?php echo __('Payment amount:', 'angelleye-paypal-invoicing'); ?></strong></label>
                                <input type="number" step="0.01" class="form-control" id="angelleye_record_payment_amount" value="<?php echo $invoice['due_amount']['value']; ?>" max="<?php echo $invoice['due_amount']['value']; ?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="record_payment_invoice_date" ><strong><?php echo __('Payment date', 'angelleye-paypal-invoicing'); ?></strong></label>
                                <input type="text" class="form-control" value="<?php echo date(get_option('date_format')); ?>" id="record_payment_invoice_date" placeholder="" name="record_payment_invoice_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="angelleye_paypal_invoice_payment_method" ><strong><?php echo __('Payment method', 'angelleye-paypal-invoicing'); ?></strong></label>
                                <select class="form-control" id="angelleye_paypal_invoice_payment_method">
                                    <option value="BANK_TRANSFER"><?php echo __('Bank transfer', 'angelleye-paypal-invoicing'); ?></option>
                                    <option value="CASH"><?php echo __('Cash', 'angelleye-paypal-invoicing'); ?></option>
                                    <option value="CHECK"><?php echo __('Check', 'angelleye-paypal-invoicing'); ?></option>
                                    <option value="CREDIT_CARD"><?php echo __('Credit card', 'angelleye-paypal-invoicing'); ?></option>
                                    <option value="DEBIT_CARD"><?php echo __('Debit card', 'angelleye-paypal-invoicing'); ?></option>
                                    <option value="PAYPAL"><?php echo __('PayPal', 'angelleye-paypal-invoicing'); ?></option>
                                    <option value="WIRE_TRANSFER"><?php echo __('Wire transfer', 'angelleye-paypal-invoicing'); ?></option>
                                    <option value="OTHER"><?php echo __('Other', 'angelleye-paypal-invoicing'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <textarea placeholder="<?php echo __('Add a note for your records', 'angelleye-paypal-invoicing'); ?>" rows="5" class="form-control" name="notes" id="angelleye_paypal_invoice_payment_note"></textarea>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="angelleye_record_payment"><?php echo __('Record payment', 'angelleye-paypal-invoicing'); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="Refund_RecordModal" tabindex="-1" role="dialog" aria-labelledby="Refund_RecordModalLabel" aria-hidden="true">
    <div class="modal-dialog invoice-table" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="Refund_RecordModalLabel"><?php echo __('Record a Refund', 'angelleye-paypal-invoicing'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <?php 
                    $max_refund_amout = 0;
                    if (!empty($invoice['payments']['paid_amount']['value'])) {
                        $max_refund_amout = $invoice['payments']['paid_amount']['value'];
                    }
                    if (!empty($invoice['refunds']['refund_amount']['value'])) {
                        $max_refund_amout = $max_refund_amout - $invoice['refunds']['refund_amount']['value'];
                    }
                    ?>
                    <input type="hidden" name="angelleye_paypal_invoice_id" id="angelleye_paypal_invoice_id" value="<?php echo $invoice['id']; ?>">
                    <?php if (!empty($invoice['detail']['invoice_number'])) : ?>
                        <div class="row">
                            <span class="col-6"><strong><?php echo __('Invoice number:', 'angelleye-paypal-invoicing'); ?></strong></span>
                            <span class="col-6"><?php echo $invoice['detail']['invoice_number']; ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['amount']['value'])) : ?>
                        <div class="row">
                            <span class="col-6"><strong><?php echo __('Invoice total:', 'angelleye-paypal-invoicing'); ?></strong></span>
                            <span class="col-6"><?php echo pifw_get_currency_symbol($invoice['amount']['currency_code']) . $invoice['amount']['value']; ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['due_amount']['value'])) : ?>
                        <div class="row">
                            <span class="col-6"><strong><?php echo __('Amount due:', 'angelleye-paypal-invoicing'); ?></strong></span>
                            <span class="col-6"><?php echo pifw_get_currency_symbol($invoice['due_amount']['currency_code']) . $invoice['due_amount']['value']; ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['payments']['paid_amount']['value'])) : ?>
                        <div class="row">
                            <span class="col-6"><strong><?php echo __('Recorded payments:', 'angelleye-paypal-invoicing'); ?></strong></span>
                            <span class="col-6"><?php echo pifw_get_currency_symbol($invoice['payments']['paid_amount']['currency_code']) . $invoice['payments']['paid_amount']['value']; ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['refunds']['refund_amount']['value'])) : ?>
                        <div class="row">
                            <span class="col-6"><strong><?php echo __('Recorded refunds:', 'angelleye-paypal-invoicing'); ?></strong></span>
                            <span class="col-6"><?php echo pifw_get_currency_symbol($invoice['refunds']['refund_amount']['currency_code']) . $invoice['refunds']['refund_amount']['value']; ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="row mt30-invoice">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="angelleye_record_refund_amount"><strong><?php echo __('Refund amount', 'angelleye-paypal-invoicing'); ?></strong></label>
                                <input type="number" step="0.01" class="form-control" value="<?php echo $max_refund_amout; ?>" id="angelleye_record_refund_amount" max="<?php echo $max_refund_amout; ?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="record_refund_invoice_date"><strong><?php echo __('Refund date', 'angelleye-paypal-invoicing'); ?></strong></label>
                                <input type="text" class="form-control" value="<?php echo date(get_option('date_format')); ?>" id="record_refund_invoice_date" placeholder="" name="record_refund_invoice_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="angelleye_paypal_invoice_payment_method" ><strong><?php echo __('Payment method', 'angelleye-paypal-invoicing'); ?></strong></label>
                                <select class="form-control" id="angelleye_paypal_invoice_payment_method">
                                    <option value="BANK_TRANSFER"><?php echo __('Bank transfer', 'angelleye-paypal-invoicing'); ?></option>
                                    <option value="CASH"><?php echo __('Cash', 'angelleye-paypal-invoicing'); ?></option>
                                    <option value="CHECK"><?php echo __('Check', 'angelleye-paypal-invoicing'); ?></option>
                                    <option value="CREDIT_CARD"><?php echo __('Credit card', 'angelleye-paypal-invoicing'); ?></option>
                                    <option value="DEBIT_CARD"><?php echo __('Debit card', 'angelleye-paypal-invoicing'); ?></option>
                                    <option value="PAYPAL"><?php echo __('PayPal', 'angelleye-paypal-invoicing'); ?></option>
                                    <option value="WIRE_TRANSFER"><?php echo __('Wire transfer', 'angelleye-paypal-invoicing'); ?></option>
                                    <option value="OTHER"><?php echo __('Other', 'angelleye-paypal-invoicing'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <textarea placeholder="<?php echo __('Add a note for your records', 'angelleye-paypal-invoicing'); ?>" rows="5" class="form-control" name="notes" id="angelleye_paypal_invoice_payment_note"></textarea>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="angelleye_record_refund"><?php echo __('Record Refund', 'angelleye-paypal-invoicing'); ?></button>
            </div>
        </div>
    </div>
</div>