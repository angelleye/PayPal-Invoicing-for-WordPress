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
?>
<div class="wrap">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="table-responsive">
                    <br><h3><?php echo __('Manage Invoices', ''); ?></h3><br>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col"><?php echo __('Date', ''); ?></th>
                                <th scope="col"><?php echo __('Invoice #', ''); ?></th>
                                <th scope="col"><?php echo __('Recipient', ''); ?></th>
                                <th scope="col"><?php echo __('Status', ''); ?></th>
                                <th scope="col"><?php echo __('Amount', ''); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($this->response)) {
                                if (isset($this->response['total_count']) && $this->response['total_count'] > 0) {
                                    foreach ($this->response['invoices'] as $key => $invoice) {
                                        $this->billing_info = $invoice['billing_info'];
                                        $amount = $invoice['total_amount'];
                                        $status = ($invoice['status'] == 'SENT') ? 'Unpaid (Sent)' : $invoice['status'];
                                        echo '<tr>';
                                        echo '<td>' . $invoice['invoice_date'] . '</td>';
                                        echo '<td>' . $invoice['number'] . '</td>';
                                        echo '<td>' . $this->billing_info[0]['email'] . '</td>';
                                        echo '<td>' . $status . '</td>';
                                        echo '<td>' . pifw_get_currency_symbol($amount['currency']) . $amount['value'] . ' ' . $amount['currency'] . '</td>';
                                        echo '</tr>';
                                    }
                                }
                            } else {
                                echo __('You haven’t created any invoices');
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th scope="col"><?php echo __('Date', ''); ?></th>
                                <th scope="col"><?php echo __('Invoice #', ''); ?></th>
                                <th scope="col"><?php echo __('Recipient', ''); ?></th>
                                <th scope="col"><?php echo __('Status', ''); ?></th>
                                <th scope="col"><?php echo __('Amount', ''); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
