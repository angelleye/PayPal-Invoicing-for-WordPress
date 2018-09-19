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
                <label for="invoice_number" class="col-sm-5 col-form-label pifw_lable_left col-12"><?php echo __('Invoice number', ''); ?></label>
                <div class="col-sm-7 col-12">
                    <input type="text" class="form-control" id="invoice_number" placeholder="" name="invoice_number">
                </div>
            </div>
            <div class="form-group row">
                <label for="invoice_date" class="col-sm-5 col-form-label pifw_lable_left"><?php echo __('Invoice date', ''); ?></label>
                <div class="col-sm-7">
                    <input type="text" class="form-control hasDatepicker" id="invoice_date" placeholder="" name="invoice_date">
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
            <div class="form-group row" style="display: none;">
                 <label for="dueDate" class="col-sm-5 col-form-label pifw_lable_left"></label>
                <div class="col-sm-7">
                    <input type="text" class="form-control hasDatepicker" id="dueDate" placeholder="<?php echo __('dd/mm/yyyy', ''); ?>" name="dueDate">
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
                        <th scope="col" class="pifw-item-name">Description</th>
                        <th scope="col" class="pifw-item-qty">Quantity</th>
                        <th scope="col" class="pifw-item-price">Price</th>
                        <th colspan="2" scope="col" class="pifw-item-tax" style="text-align: center;">Tax</th>
                        <th  scope="col" class="pifw-item-amount">Amount</th>
                        <th scope="col" class="pifw-item-action"></th>
                    </tr>
                </thead>
                <tbody class="first_tbody">
                    <tr class="invoice-item-data">
                        <td><input type="text" placeholder="<?php echo __('Item name'); ?>"></td>
                        <td><input type="number" placeholder="<?php echo __('0'); ?>"></td>
                        <td><input type="number" placeholder="<?php echo __('0.00'); ?>"></td>
                        <td><input type="text" placeholder="<?php echo __('Name'); ?>"></div>
                        <td><input type="number" placeholder="<?php echo __('Amount'); ?>"></td>
                        <td rowspan="2" class="amount">0.00</td>
                        <td></td>
                    </tr>
                    <tr class="invoice-detailed">
                        <td colspan="5"><input type="text" aria-label="" placeholder="Enter detailed description (optional)"></td>
                        <td><div class="deleteItem" style="width:23px;">&nbsp;</div></td>
                    </tr>
                    <tr class="invoice-end-row"><td colspan="5"></td></tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7">
                            <div class="add_new_item_box">
                                <a href="#" id="add_new_item" tabindex="-1" pa-marked="1"><span></span> Add another line item</a>
                            </div>

                        </td>
                    </tr>

                </tfoot>

            </table>
        </div>
    </div>
    <div class="row invoice-total">
        <div class="col-sm-6">
        </div>
        <div class="col-sm-6 invoice-subtotal">
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        <tr>
                            <th colspan="3">Subtotal</th>
                            <td class="itemSubTotal">$&nbsp;0.00&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>