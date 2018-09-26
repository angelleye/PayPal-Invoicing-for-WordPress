<?php
echo print_r($invoice, true);
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <?php echo __('Invoice', ''); ?>
            <strong><?php echo date_i18n(get_option('date_format'), strtotime($invoice['invoice_date'])); ?></strong>
            <span class = "float-right"> <strong>Status:</strong> <?php echo $invoice['status']; ?></span>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-sm-6">
                    <?php if(!empty($invoice['billing_info'][0]['email'])) {
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
                        <?php echo isset($invoice['billing_info'][0]['address']['city']) ?  $invoice['billing_info'][0]['address']['city'] : ''; ?>
                        <?php echo isset($invoice['billing_info'][0]['address']['state']) ?  $invoice['billing_info'][0]['address']['state'] : ''; ?>
                        <?php echo isset($invoice['billing_info'][0]['address']['postal_code']) ?  $invoice['billing_info'][0]['address']['postal_code'] : ''; ?>
                    </div>
                    <div>
                        <?php echo isset($invoice['billing_info'][0]['address']['country_code']) ?  $invoice['billing_info'][0]['address']['country_code'] : ''; ?>
                    </div>
                    <?php echo isset($invoice['billing_info'][0]['email']) ?  '<div>' . $invoice['billing_info'][0]['email'] .'</div>' : ''; ?>
                    <?php if( !empty($invoice['billing_info'][0]['phone'])) {
                        echo '<div>+' . $invoice['billing_info'][0]['phone']['country_code'] .'  '. $invoice['billing_info'][0]['phone']['national_number'] . '</div>';
                    }
                    ?>
                </div>
                <div class="col-sm-6">
                    <?php if(!empty($invoice['shipping_info']['first_name'])) {
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
                        <?php echo isset($invoice['shipping_info']['address']['city']) ?  $invoice['shipping_info']['address']['city'] : ''; ?>
                        <?php echo isset($invoice['shipping_info']['address']['state']) ?  $invoice['shipping_info']['address']['state'] : ''; ?>
                        <?php echo isset($invoice['shipping_info']['address']['postal_code']) ?  $invoice['shipping_info']['address']['postal_code'] : ''; ?>
                    </div>
                    <div>
                        <?php echo isset($invoice['shipping_info']['address']['country_code']) ?  $invoice['shipping_info']['address']['country_code'] : ''; ?>
                    </div>
                    <?php echo isset($invoice['shipping_info']['email']) ?  '<div>' . $invoice['shipping_info']['email'] .'</div>' : ''; ?>
                    <?php if( !empty($invoice['shipping_info']['phone'])) {
                        echo '<div>+' . $invoice['shipping_info']['phone']['country_code'] .'  '. $invoice['shipping_info']['phone']['national_number'] . '</div>';
                    }
                    ?>
                </div>
            </div>
            <div class="table-responsive-sm">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="right">Quantity</th>
                            <th class="center">Price</th>
                            <th class="center">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php 
                            if( !empty($invoice['items'])) {
                                foreach ($invoice['items'] as $key => $invoice_item) {
                                    echo '<tr>';
                                    echo '<td>' . $invoice_item['name'] . $invoice_item['description'] . '</td>';
                                    
                                    echo '<td>' . $invoice_item['quantity'] . '</td>';
                                    echo '<td>' . pifw_get_currency_symbol($invoice_item['unit_price']['currency']) . $invoice_item['unit_price']['value'] . '</td>';
                                    //echo '<td>' . $invoice_item['tax']['name']. ' ('.$invoice_item['tax']['percent'] . '%) '. '</td>';
                                    echo '<td>' . pifw_get_currency_symbol($invoice_item['unit_price']['currency']) . number_format($invoice_item['quantity'] * $invoice_item['unit_price']['value'], 2) . '</td>';
                                    echo '</tr>';
                                }
                            }
                            ?>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-lg-4 col-sm-5">
                </div>
                <div class="col-lg-4 col-sm-5 ml-auto">
                    <table class="table table-clear">
                        <tbody>
                            <tr>
                                <td class="left">
                                    <strong>Subtotal</strong>
                                </td>
                                <td class="right">$8.497,00</td>
                            </tr>
                            <tr>
                                <td class="left">
                                    <strong>Discount (20%)</strong>
                                </td>
                                <td class="right">$1,699,40</td>
                            </tr>
                            <tr>
                                <td class="left">
                                    <strong>VAT (10%)</strong>
                                </td>
                                <td class="right">$679,76</td>
                            </tr>
                            <tr>
                                <td class="left">
                                    <strong>Total</strong>
                                </td>
                                <td class="right">
                                    <strong>$7.477,36</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


