<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="container-fluid">
    
    <form>
        <div class="pifw_section">
            <div class="row ">
                <div class="pt30-invoice"></div>
                
                <div class="col-8 mt30-invoice"></div>
                <div class="col-4 mt30-invoice">
                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-5 col-form-label pifw_lable_left">Invoice number</label>
                        <div class="col-sm-7">
                            <input type="password" class="form-control" id="inputPassword" placeholder="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-5 col-form-label pifw_lable_left">Invoice date</label>
                        <div class="col-sm-7">
                            <input type="password" class="form-control" id="inputPassword" placeholder="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-5 col-form-label pifw_lable_left">Reference</label>
                        <div class="col-sm-7">
                            <input type="password" class="form-control" id="inputPassword" placeholder="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-5 col-form-label pifw_lable_left">Due date</label>
                        <div class="col-sm-7">
                            <input type="password" class="form-control" id="inputPassword" placeholder="">
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
                <table class="table invoice-table">

                    <thead>
                        <tr>
                            <th scope="col" class="pifw-item-name">Description</th>
                            <th scope="col" class="pifw-item-qty">Quantity</th>
                            <th scope="col" class="pifw-item-price">Price</th>
                            <th scope="col" class="pifw-item-tax">Tax</th>
                            <th scope="col" class="pifw-item-amount">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" placeholder="<?php echo __('Item name'); ?>"></td>
                            <td><input type="text" placeholder="<?php echo __('0'); ?>"></td>
                            <td><input type="text" placeholder="<?php echo __('0.00'); ?>"></td>
                            <td><input type="text" style="width: 50%;" placeholder="<?php echo __('Name'); ?>"><input style="width: 50%;" type="text" placeholder="<?php echo __('Amount'); ?>"></td>
                            
                            <td rowspan="2" class="pifw-border-bottom"><label>125.00</label></td>
                        </tr>
                        <tr class="pifw-border-bottom">
                            <td colspan="4" ><input type="text" aria-label="" placeholder="Enter detailed description (optional)"></td>
                            
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>




