<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="container-fluid pifw_section" id="angelleye-paypal-invoicing">
    <div class="row">
        <div class="col-12 mt30-invoice col-sm-8"></div>
        <div class="col-12 mt30-invoice col-sm-4">
            <div class="form-group row">
                <label for="invoice_number" class="col-sm-5 col-form-label pifw_lable_left col-12"><?php echo __('Invoice number', ''); ?> </label>
                <div class="col-sm-6 col-11">
                    <input type="text" class="form-control" id="invoice_number" placeholder="" name="invoice_number">
                </div>
                <div class="input-group-append">
                    <span class="dashicons dashicons-info" data-toggle="tooltip" data-placement="top" title="<?php echo __("Invoices are numbered automatically beginning with invoice number 0001. You can customize the invoice number any way you'd like, and the next number will increment by 1.", ''); ?>"></span>
                </div>
            </div>
            <div class="form-group row">
                <label for="invoice_date" class="col-sm-5 col-form-label pifw_lable_left"><?php echo __('Invoice date', ''); ?></label>
                <div class="col-sm-6 col-11">
                    <input type="text" class="form-control" id="invoice_date" placeholder="" name="invoice_date">
                </div>
                <div class="input-group-append">
                    <span class="dashicons dashicons-info" data-toggle="tooltip" data-placement="top" title="<?php echo __("You can select any invoice date. This invoice will be sent today or on a future date you choose.", ''); ?>"></span>
                </div>
            </div>
            <div class="form-group row">
                <label for="Reference" class="col-sm-5 col-form-label pifw_lable_left">Reference</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" id="Reference" placeholder="<?php echo __('Such as PO#', ''); ?>" name="reference">
                </div>
            </div>
            <div class="form-group row" >
                <label for="invoiceTerms" class="col-sm-5 col-form-label pifw_lable_left"><?php echo __('Due date', ''); ?></label>
                <div class="col-sm-7">
                    <select id="invoiceTerms" class="form-control" name="invoiceTerms">
                        <option value="noduedate"><?php echo __('No due date', ''); ?></option>
                        <option value="receipt" selected=""><?php echo __('Due on receipt', ''); ?></option>
                        <option name="dueDateTermsSpecified" id="dueDateTermsSpecified" value="specified"><?php echo __('Due on date specified', ''); ?></option>
                        <option value="net10"><?php echo __('Due in 10 days', ''); ?></option>
                        <option value="net15"><?php echo __('Due in 15 days', ''); ?></option>
                        <option value="net30"><?php echo __('Due in 30 days', ''); ?></option>
                        <option value="net45"><?php echo __('Due in 45 days', ''); ?></option>
                        <option value="net60"><?php echo __('Due in 60 days', ''); ?></option>
                        <option value="net90"><?php echo __('Due in 90 days', ''); ?></option>
                    </select>
                </div>
            </div>
            <div class="form-group row" id="dueDate_box">
                <label for="dueDate" class="col-sm-5 col-form-label pifw_lable_left"></label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" id="dueDate" placeholder="<?php echo __('dd/mm/yyyy', ''); ?>" name="dueDate">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6 mt30-invoice">
            <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label pifw_lable_left"><b>Bill to:</b></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputPassword" placeholder="">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6 mt30-invoice">
            <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label pifw_lable_left"><b>Cc:</b></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputPassword" placeholder="">
                </div>
            </div>
        </div>
    </div>
    <div class="row mt30-invoice">
        <div class="table-responsive">
            <table class="table invoice-table">
                <thead>
                    <tr>
                        <th scope="col" class="pifw-item-name"><?php echo __('Description', ''); ?></th>
                        <th scope="col" class="pifw-item-qty"><?php __('Quantity', ''); ?></th>
                        <th scope="col" class="pifw-item-price"><?php __('Price', ''); ?></th>
                        <th colspan="2" scope="col" class="pifw-item-tax" style="text-align: center;"><?php echo __('Tax', ''); ?></th>
                        <th  scope="col" class="pifw-item-amount"><?php echo __('Amount', ''); ?></th>
                        <th scope="col" class="pifw-item-action"></th>
                    </tr>
                </thead>
                <tbody class="first_tbody">
                    <tr class="invoice-item-data">
                        <td><input type="text" placeholder="<?php echo __('Item name'); ?>"></td>
                        <td><input type="number" placeholder="<?php echo __('0'); ?>"></td>
                        <td><input type="number" placeholder="<?php echo __('0.00'); ?>"></td>
                        <td><input type="text" placeholder="<?php echo __('Name'); ?>"></td>
                        <td><input type="number" placeholder="<?php echo __('Amount'); ?>"></td>
                        <td rowspan="2" class="amount">0.00</td>
                        <td></td>
                    </tr>
                    <tr class="invoice-detailed">
                        <td colspan="5"><input type="text" aria-label="" placeholder="<?php echo __('Enter detailed description (optional)', ''); ?>"></td>
                        <td><div class="deleteItem" style="width:23px;">&nbsp;</div></td>
                    </tr>
                    <tr class="invoice-end-row"><td colspan="5"></td></tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7">
                            <div class="add_new_item_box">
                                <a href="#" id="add_new_item" tabindex="-1" pa-marked="1"><span></span><?php echo __('Add another line item', ''); ?></a>
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
                <label class="custom-control-label" for="allowPartialPayments"><?php echo __('Allow partial payment', ''); ?> <span class="dashicons dashicons-info" data-toggle="tooltip" data-placement="top" title="<?php echo __('Your customer will be allowed to enter any payment amount above the minimum until the invoice is paid in full.', ''); ?>"></span></label>
            </div>
            <div class="allow_partial_payment_content_box">
                <div class="col-sm-12">
                    <div class="form-group">
                        <span><?php echo __('Minimum amount due (optional)', ''); ?></span>
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
                <label class="custom-control-label" for="allowTips"><?php echo __('Allow customer to add a tip.', ''); ?></label>
            </div>
        </div>
        <div class="col-sm-6 invoice-subtotal">
            <div class="table-responsive">
                <div class="total-section">
                    <table class="table sub-total-table table-bordered">
                        <tbody>
                            <tr>
                                <th colspan="3"><?php echo __('Subtotal', ''); ?></th>
                                <td class="grey-bg itemSubTotal">$0.00</td>
                            </tr>
                            <tr>
                                <th><?php echo __('Discount', ''); ?></th>
                                <td>
                                    <input name="invDiscount" id="invDiscount" class="text short-field" value="0.00" type="text">
                                </td>
                                <td>
                                    <select name="invoiceDiscType" id="invoiceDiscType" class="select">
                                        <option value="percentage">%</option>
                                        <option value="dollar">$</option>
                                    </select>
                                </td>
                                <td class="grey-bg invoiceDiscountAmount">$0.00</td>
                            </tr>
                            <tr>
                                <th><?php echo __('Shipping', ''); ?></th>
                                <td colspan="2">
                                    <input name="shippingAmount" id="shippingAmount" class="text short-field" value="0.00" type="text">
                                </td>
                                <td class="grey-bg shippingAmountTd">$15.00</td>
                            </tr>
                            <tr class="dynamic_tax" id="tax_tr_0"><td colspan="3"><b>Tax (2.5%) </b>GST</td><td>$<span class="tax_to_add">0.00</span></td></tr>
                            <tr class="grey-bg">
                                <th colspan="3"><?php echo __('Total', ''); ?></th>
                                <td class="finalTotal">$15.00 USD</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt30-invoice">
        <div class="col-sm-6">
            <div class="form-group"><label for="terms"><?php echo __('Note to recipient', ''); ?></label><textarea placeholder="<?php echo __('Such as “Thank you for your business”', ''); ?>" rows="5" class="form-control" name="notes" id="notes"></textarea><p class="help-block text-right" id="notesChars">3837</p></div>
        </div>
        <div class="col-sm-6">
            <div class="form-group"><label for="notes"><?php echo __('Terms and conditions', ''); ?></label><textarea placeholder="<?php echo __('Include your return or cancelation policy', ''); ?>" rows="5" class="form-control" name="terms" id="terms"></textarea><p class="help-block text-right" id="termsChars">3991</p></div>
        </div>
    </div>
    <div class="col-xs-6">
        <div id="memo">
            <div class="memoHead" style="">
                <span class="addIcon" tabindex="0" id="memoAddButton"></span>
                <span><?php echo __('Add memo to self', ''); ?></span>
            </div>
            <div class="memoDetail" style="display: none;">
                <label for="memo"><?php echo __('Memo', ''); ?></label>
                <textarea style="color: #333" name="memodesc" id="memodesc" class="form-control" rows="5" placeholder="<?php echo __("Add memo to self (your recipient won't see this)", ''); ?>"></textarea>
                <div class="memoAction">
                    <p class="memo-p"><a id="memoHideLink" class="cnlAction pull-left" href="#" pa-marked="1"><?php echo __('Hide', ''); ?></a></p>
                    <p class="memo-p disabled pull-right" id="chars">500</p>
                </div>
            </div>
        </div>
    </div>
</div>