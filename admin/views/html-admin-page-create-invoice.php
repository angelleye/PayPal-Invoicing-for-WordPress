<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$apifw_setting = get_option('apifw_setting');
$note_to_recipient = isset($apifw_setting['note_to_recipient']) ? $apifw_setting['note_to_recipient'] : '';
$terms_and_condition = isset($apifw_setting['terms_and_condition']) ? $apifw_setting['terms_and_condition'] : '';
$shipping_amount = isset($apifw_setting['shipping_amount']) ? $apifw_setting['shipping_amount'] : '';
$tax_rate = isset($apifw_setting['tax_rate']) ? $apifw_setting['tax_rate'] : $this->tax_rate;
$tax_name = isset($apifw_setting['tax_name']) ? $apifw_setting['tax_name'] : $this->tax_name;
$apifw_company_logo = isset($apifw_setting['apifw_company_logo']) ? $apifw_setting['apifw_company_logo'] : '';
//echo print_r(_get_cron_array(), true);
?>

<div class="container-fluid pifw_section" id="angelleye-paypal-invoicing">
    <div class="row">
        <div class="col-sm-12 col-12" style="float: right;">
            <div class="form-group row paypal-invoice-create-action-box">
                <button value="send_invoice" class="btn btn-primary" type="submit" id="send_invoice" name="send_invoice"><?php echo __('Send Invoice', 'angelleye-paypal-invoicing'); ?></button>
                &nbsp;<button value="save_invoice" class="btn btn-primary" type="submit" id="save_invoice" name="save_invoice"><?php echo __('Save as Draft', 'angelleye-paypal-invoicing'); ?></button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mt30-invoice col-sm-8">
            <?php if( !empty($apifw_company_logo)) { ?>
            <img src="<?php echo $apifw_company_logo; ?>" class="rounded float-left company_logo">
            <?php } ?>
        </div>
        <div class="col-12 mt30-invoice col-sm-4">
            <div class="form-group row">
                <label for="invoice_number" class="col-sm-5 col-form-label pifw_lable_left col-12"><?php echo __('Invoice number', 'angelleye-paypal-invoicing'); ?> </label>
                <div class="col-sm-6 col-11 ">
                    <input type="text" class="form-control" value="<?php echo isset($this->response['number']) ? esc_attr($this->response['number']) : '' ?>" id="invoice_number" placeholder="" name="invoice_number" required>
                </div>
                <div class="input-group-append">
                    <span class="dashicons dashicons-info" data-toggle="tooltip" data-placement="top" title="<?php echo __("Invoices are numbered automatically beginning with invoice number 0001. You can customize the invoice number any way you'd like, and the next number will increment by 1.", 'angelleye-paypal-invoicing'); ?>"></span>
                </div>
            </div>
            <div class="form-group row">
                <label for="invoice_date" class="col-sm-5 col-form-label pifw_lable_left"><?php echo __('Invoice date', 'angelleye-paypal-invoicing'); ?></label>
                <div class="col-sm-6 col-11">
                    <input type="text" class="form-control" value="<?php echo date("d/m/Y"); ?>" id="invoice_date" placeholder="" name="invoice_date" required>
                </div>
                <div class="input-group-append">
                    <span class="dashicons dashicons-info" data-toggle="tooltip" data-placement="top" title="<?php echo __("You can select any invoice date. This invoice will be sent today or on a future date you choose.", 'angelleye-paypal-invoicing'); ?>"></span>
                </div>
            </div>
            <div class="form-group row">
                <label for="reference" class="col-sm-5 col-form-label pifw_lable_left col-12"><?php echo __('Reference', 'angelleye-paypal-invoicing'); ?></label>
                <div class="col-sm-6 col-11">
                    <input type="text" class="form-control" id="reference" placeholder="<?php echo __('Such as PO#', 'angelleye-paypal-invoicing'); ?>" name="reference">
                </div>
            </div>
            <div class="form-group row" >
                <label for="invoiceTerms" class="col-sm-5 col-form-label pifw_lable_left"><?php echo __('Due date', 'angelleye-paypal-invoicing'); ?></label>
                <div class="col-sm-6 col-11">
                    <select id="invoiceTerms" class="form-control" name="invoiceTerms" required>
                        <option value="NO_DUE_DATE"><?php echo __('No due date', 'angelleye-paypal-invoicing'); ?></option>
                        <option value="DUE_ON_RECEIPT"><?php echo __('Due on receipt', 'angelleye-paypal-invoicing'); ?></option>
                        <option value="DUE_ON_DATE_SPECIFIED"><?php echo __('Due on date specified', 'angelleye-paypal-invoicing'); ?></option>
                        <option value="NET_10"><?php echo __('Due in 10 days', 'angelleye-paypal-invoicing'); ?></option>
                        <option value="NET_15"><?php echo __('Due in 15 days', 'angelleye-paypal-invoicing'); ?></option>
                        <option value="NET_30"><?php echo __('Due in 30 days', 'angelleye-paypal-invoicing'); ?></option>
                        <option value="NET_45"><?php echo __('Due in 45 days', 'angelleye-paypal-invoicing'); ?></option>
                        <option value="NET_60"><?php echo __('Due in 60 days', 'angelleye-paypal-invoicing'); ?></option>
                        <option value="NET_90"><?php echo __('Due in 90 days', 'angelleye-paypal-invoicing'); ?></option>
                    </select>
                </div>
            </div>
            <div class="form-group row" id="dueDate_box">
                <label for="dueDate" class="col-sm-5 col-form-label pifw_lable_left"></label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" id="dueDate" placeholder="<?php echo __('d/m/Y', 'angelleye-paypal-invoicing'); ?>" name="DUE_ON_DATE_SPECIFIED" value="<?php echo date("d/m/Y"); ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6 mt30-invoice">
            <div class="form-group row">
                <label for="bill_to" class="col-sm-2 col-form-label pifw_lable_left"><b><?php echo __('Bill to:', 'angelleye-paypal-invoicing'); ?></b></label>
                <div class="col-sm-10">
                    <input type="text" name="bill_to" class="form-control" id="bill_to" placeholder="" required>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6 mt30-invoice">
            <div class="form-group row">
                <label for="cc_to" class="col-sm-2 col-form-label pifw_lable_left"><b><?php echo __('Cc:', 'angelleye-paypal-invoicing'); ?></b></label>
                <div class="col-sm-10">
                    <input type="text" name="cc_to" class="form-control" id="cc_to" placeholder="">
                </div>
            </div>
        </div>
    </div>
    <div class="row mt30-invoice">
        <div class="table-responsive-sm">
            <table class="table invoice-table">
                <thead>
                    <tr>
                        <th scope="col" class="pifw-item-name"><?php echo __('Description', 'angelleye-paypal-invoicing'); ?></th>
                        <th scope="col" class="pifw-item-qty"><?php echo __('Quantity', 'angelleye-paypal-invoicing'); ?></th>
                        <th scope="col" class="pifw-item-price"><?php echo __('Price', 'angelleye-paypal-invoicing'); ?></th>
                        <th scope="col" class="pifw-item-tax"><?php echo __('Tax Name', 'angelleye-paypal-invoicing'); ?></th>
                        <th scope="col" class="pifw-item-tax-rate"><?php echo __('Tax Rate', 'angelleye-paypal-invoicing'); ?></th>
                        <th scope="col" class="pifw-item-amount"><?php echo __('Amount', 'angelleye-paypal-invoicing'); ?></th>
                        <th scope="col" class="pifw-item-action"></th>
                    </tr>
                </thead>
                <tbody class="first_tbody">
                    <tr class="invoice-item-data">
                        <td><input name="item_name[]" class="form-control" type="text" placeholder="<?php echo __('Item name', 'angelleye-paypal-invoicing'); ?>" required></td>
                        <td><input name="item_qty[]" class="form-control" type="text" placeholder="<?php echo __('0'); ?>" required></td>
                        <td><input name="item_amt[]"  class="form-control" type="text" placeholder="<?php echo __('0.00'); ?>" required></td>
                        <td><input name="item_txt_name[]" class="form-control" type="text" value="<?php echo $tax_name; ?>" placeholder="<?php echo __('Name', 'angelleye-paypal-invoicing'); ?>"></td>
                        <td>
                            <div class="input-group">
                                <input name="item_txt_rate[]" class="form-control" type="text" <?php if(!empty($tax_rate)) { echo "value=".$tax_rate.""; } ?>  placeholder="<?php echo __('%', 'angelleye-paypal-invoicing'); ?>" oninvalid="this.setCustomValidity('<?php echo __('Please enter a tax rate, or remove the tax name to continue.', 'angelleye-paypal-invoicing'); ?>' )" onvalid="this.setCustomValidity('')" oninput="setCustomValidity('')" onchange="setCustomValidity('')">
                                <div class="input-group-prepend">
                                <span class="input-group-text" id="validationTooltipUsernamePrepend">%</span>
                              </div>
                            </div>
                            
                        </td>
                        <td rowspan="2" class="amount">0.00</td>
                        <td class="no-border"></td>
                    </tr>
                    <tr class="invoice-detailed">
                        <td colspan="5"><input type="text" aria-label="" class="form-control" name="item_description[]" placeholder="<?php echo __('Enter detailed description (optional)', 'angelleye-paypal-invoicing'); ?>"></td>
                        <td><div class="deleteItem" style="width:23px;">&nbsp;</div></td>
                    </tr>
                    <tr class="invoice-end-row"><td colspan="5"></td></tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7">
                            <div class="add_new_item_box">
                                <a href="#" id="add_new_item" class="add_new_item"><span></span><?php echo __('Add another line item', 'angelleye-paypal-invoicing'); ?></a>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="row invoice-total">
        <div class="col-sm-6">
            <br>
            <div class="custom-control custom-checkbox form-group">
                <input type="checkbox" class="custom-control-input" id="allowPartialPayments" name="allowPartialPayments">
                <label class="custom-control-label" for="allowPartialPayments"><?php echo __('Allow partial payment', 'angelleye-paypal-invoicing'); ?> <span class="dashicons dashicons-info" data-toggle="tooltip" data-placement="top" title="<?php echo __('Your customer will be allowed to enter any payment amount above the minimum until the invoice is paid in full.', 'angelleye-paypal-invoicing'); ?>"></span></label>
            </div>
            <div class="allow_partial_payment_content_box">
                <div class="col-sm-12">
                    <div class="form-group">
                        <span><?php echo __('Minimum amount due (optional)', 'angelleye-paypal-invoicing'); ?></span>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-inline">
                        <input type="text" class="form-control" id="minimumDueAmount" placeholder="0" name="minimumDueAmount">
                        <label for="minimumDueAmount">&nbsp;&nbsp;USD</label>
                    </div>
                </div>
            </div>
            <div class="custom-control custom-checkbox form-group" style="margin-top: 15px;">
                <input type="checkbox" class="custom-control-input" id="allowTips" name="allowTips">
                <label class="custom-control-label" for="allowTips"><?php echo __('Allow customer to add a tip.', 'angelleye-paypal-invoicing'); ?></label>
            </div>
        </div>
        <div class="col-sm-6 invoice-subtotal">
            <div class="table-responsive">
                <div class="total-section">
                    <table class="table sub-total-table table-bordered">
                        <tbody>
                            <tr>
                                <th colspan="3"><?php echo __('Subtotal', 'angelleye-paypal-invoicing'); ?></th>
                                <td class="grey-bg itemSubTotal">$0.00</td>
                            </tr>
                            <tr>
                                <th><?php echo __('Discount', 'angelleye-paypal-invoicing'); ?></th>
                                <td>
                                        <input name="invDiscount" id="invDiscount" class="text form-control" placeholder="0.00" type="text">
                                </td>
                                <td>
                                    <select name="invoiceDiscType" id="invoiceDiscType" class="select form-control">
                                        <option value="percentage">%</option>
                                        <option value="dollar">$</option>
                                    </select>
                                </td>
                                <td class="grey-bg invoiceDiscountAmount">$0.00</td>
                            </tr>
                            <tr>
                                <th><?php echo __('Shipping', 'angelleye-paypal-invoicing'); ?></th>
                                <td colspan="2">
                                    
                                        <input name="shippingAmount" id="shippingAmount" class="text form-control" value="<?php echo esc_attr($shipping_amount); ?>" type="text">
                                    
                                </td>
                                <td class="grey-bg shippingAmountTd"><?php echo esc_attr($shipping_amount); ?></td>
                            </tr>
                            <tr class="dynamic_tax" id="tax_tr_0"><td colspan="3"></td><td>$<span class="tax_to_add">0.00</span></td></tr>
                            <tr class="grey-bg">
                                <th colspan="3"><?php echo __('Total', 'angelleye-paypal-invoicing'); ?></th>
                                <td class="finalTotal"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt30-invoice">
        <div class="col-sm-6">
            <div class="form-group"><label for="notes"><?php echo __('Note to recipient', 'angelleye-paypal-invoicing'); ?></label><textarea placeholder="<?php echo __('Such as &ldquo;Thank you for your business&rdquo;', 'angelleye-paypal-invoicing'); ?>" rows="5" class="form-control" name="notes" id="notes"><?php echo $note_to_recipient; ?></textarea><p class="help-block text-right" id="notesChars">3837</p></div>
        </div>
        <div class="col-sm-6">
            <div class="form-group"><label for="terms"><?php echo __('Terms and conditions', 'angelleye-paypal-invoicing'); ?></label><textarea placeholder="<?php echo __('Include your return or cancelation policy', 'angelleye-paypal-invoicing'); ?>" rows="5" class="form-control" name="terms" id="terms"><?php echo $terms_and_condition; ?></textarea><p class="help-block text-right" id="termsChars">3991</p></div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
        <div id="memo">
            <div class="memoHead" style="">
                <span class="addIcon" id="memoAddButton"></span>
                <span><?php echo __('Add memo to self', 'angelleye-paypal-invoicing'); ?></span>
            </div>
            <div class="memoDetail" style="display: none;">
                <label for="memo"><?php echo __('Memo', 'angelleye-paypal-invoicing'); ?></label>
                <textarea style="color: #333" name="memodesc" id="memodesc" class="form-control" rows="5" placeholder="<?php echo __("Add memo to self (your recipient won't see this)", 'angelleye-paypal-invoicing'); ?>"></textarea>
                <div class="memoAction">
                    <p class="memo-p"><a id="memoHideLink" class="cnlAction pull-left" href="#"><?php echo __('Hide', 'angelleye-paypal-invoicing'); ?></a></p>
                    <p class="memo-p disabled pull-right" id="chars">500</p>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-12" style="float: right;">
            <div class="form-group row paypal-invoice-create-action-box">
                <button value="send_invoice" class="btn btn-primary" type="submit" id="send_invoice" name="send_invoice"><?php echo __('Send Invoice', 'angelleye-paypal-invoicing'); ?></button>
                &nbsp;<button value="save_invoice" class="btn btn-primary" type="submit" id="save_invoice" name="save_invoice"><?php echo __('Save as Draft', 'angelleye-paypal-invoicing'); ?></button>
                </div>
        </div>
    </div>
</div>

