<?php //echo print_r($invoice, true);         ?>
<div class="container" id="invoice_view_table">
    <div class="card">
        <span class="folded-corner"></span>
        <div class="card-body">
            <br>
            <div class="row">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6" style="text-align: right;"><div class="pageCurl"><?php echo __('INVOICE', ''); ?></div></div>
            </div>
            <br>
            <div class="row">
                <div class="col-sm-8">
                </div>
                <div class="col-sm-4 invoice-view-info">
                    <?php
                    $invoice_status_array = pifw_get_invoice_status_name_and_class($invoice['status']);
                    if (!empty($invoice_status_array)) :
                        ?>
                        <div class="row" style="margin-bottom: 20px;">
                            <span class="col-sm-6 text-right"></span>
                            <span class="invoiceStatus <?php echo $invoice_status_array['class']; ?>"><?php echo $invoice_status_array['lable']; ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['number'])) : ?>
                        <div class="row">
                            <span class="col-sm-6 text-right"><?php echo __('Invoice #:', ''); ?></span>
                            <span><?php echo $invoice['number']; ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['invoice_date'])) : ?>
                        <div class="row">
                            <span class="col-sm-6 text-right"><?php echo __('Invoice date:', ''); ?></span>
                            <span><?php echo date_i18n(get_option('date_format'), strtotime($invoice['invoice_date'])); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['reference'])) : ?>
                        <div class="row">
                            <span class="col-sm-6 text-right"><?php echo __('Reference:', ''); ?></span>
                            <span><?php echo $invoice['reference']; ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['payment_term']['due_date'])) : ?>
                        <div class="row">
                            <span class="col-sm-6 text-right"><?php echo __('Due date:', ''); ?></span>
                            <span><?php echo date_i18n(get_option('date_format'), strtotime($invoice['payment_term']['due_date'])); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <br>
            <div class="sectionBottom"></div>
            <br>
            <div class="row mb-4">
                <div class="col-sm-4">
                    <h4 class="mb-3"><?php echo __('Merchant Info:', ''); ?></h4>
                    <div>
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
                <div class="col-sm-4">
                    <?php
                    if (!empty($invoice['billing_info'][0]['email'])) {
                        echo '<h4 class="mb-3">Bill To:</h4>';
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
                    if (!empty($invoice['billing_info'][0]['phone'])) {
                        echo '<div>+' . $invoice['billing_info'][0]['phone']['country_code'] . '  ' . $invoice['billing_info'][0]['phone']['national_number'] . '</div>';
                    }
                    ?>
                </div>
                <div class="col-sm-4">
                    <?php
                    if (!empty($invoice['shipping_info']['first_name'])) {
                        echo '<h4 class="mb-3">Ship To:</h4>';
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
                            <th class="itemdescription"><?php echo __('Description', ''); ?></th>
                            <th class="itemquantity text-right"><?php echo __('Quantity', ''); ?></th>
                            <th class="itemprice text-right"><?php echo __('Price', ''); ?></th>
                            <th class="itemamount text-right"><?php echo __('Amount', ''); ?></th>
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
                                <tr>
                                    <td class="left">
                                        <?php echo __('Subtotal', ''); ?>
                                    </td>
                                    <td class="right"><?php echo pifw_get_currency_symbol($invoice_total_array['sub_total']['currency']) . number_format($invoice_total_array['sub_total']['value'], 2); ?></td>
                                </tr>
                                <?php if (!empty($invoice_total_array['shipping_cost'])) : ?>
                                    <tr>
                                        <td class="left">
                                            <?php echo __('Shipping', ''); ?>
                                        </td>
                                        <td class="right"><?php echo pifw_get_currency_symbol($invoice_total_array['shipping_cost']['amount']['currency']) . number_format($invoice_total_array['shipping_cost']['amount']['value'], 2); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (!empty($invoice_total_array['tax'])) : ?>
                                    <?php
                                    foreach ($invoice_total_array['tax'] as $tax_index => $tax_data) {
                                        echo '<tr>';
                                        echo '<td class="left">';
                                        echo $tax_data['name'] . ' (' . $tax_data['percent'] . '%)';
                                        echo '</td>';
                                        echo '<td class="right">' . pifw_get_currency_symbol($tax_data['amount']['currency']) . number_format($tax_data['amount']['value'], 2) . '</td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                <?php endif; ?>
                                <?php if (!empty($invoice_total_array['discount'])) : ?>
                                    <tr>
                                        <td class="left">
                                            <?php echo __('Discount', ''); ?>
                                        </td>
                                        <?php echo '<td class="right">-' . pifw_get_currency_symbol($invoice_total_array['discount']['amount']['currency']) . number_format($invoice_total_array['discount']['amount']['value'], 2) . '</td>'; ?>
                                    </tr>
                                <?php endif; ?>
                                <?php if (!empty($invoice['total_amount'])) : ?>
                                    <tr>
                                        <td class="left total">
                                            <strong><?php echo __('Total', ''); ?></strong>
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
                            <h4 class="headline"><?php echo __('Notes', ''); ?></h4>
                            <p class="notes"><?php echo $invoice['note']; ?></p>
                        </div>
                    </div><!-- close note col-xs -->
                <?php endif; ?>
                <?php if (!empty($invoice['terms'])) : ?>
                    <div class="col-xs-6 col-sm-6">
                        <div>
                            <h4 class="headline"><?php echo __('Terms and Conditions', ''); ?></h4>
                            <p class="terms"><?php echo $invoice['terms']; ?></p>
                        </div>
                    </div> <!-- close terms col-xs -->
                <?php endif; ?>
            </div>
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