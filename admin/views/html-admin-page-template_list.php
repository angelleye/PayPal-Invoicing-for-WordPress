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
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col"><?php echo __('Date', 'angelleye-paypal-invoicing'); ?></th>
                                <th scope="col"><?php echo __('Invoice #', 'angelleye-paypal-invoicing'); ?></th>
                                <th scope="col"><?php echo __('Recipient', 'angelleye-paypal-invoicing'); ?></th>
                                <th scope="col"><?php echo __('Status', 'angelleye-paypal-invoicing'); ?></th>
                                <th scope="col"><?php echo __('Action', 'angelleye-paypal-invoicing'); ?></th>
                                <th scope="col"><?php echo __('Amount', 'angelleye-paypal-invoicing'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($this->response)) {
                                echo print_r($this->response, true);
                            } else {
                                echo __('You haven’t created any invoices', 'angelleye-paypal-invoicing');
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th scope="col"><?php echo __('Date', 'angelleye-paypal-invoicing'); ?></th>
                                <th scope="col"><?php echo __('Invoice #', 'angelleye-paypal-invoicing'); ?></th>
                                <th scope="col"><?php echo __('Recipient', 'angelleye-paypal-invoicing'); ?></th>
                                <th scope="col"><?php echo __('Status', 'angelleye-paypal-invoicing'); ?></th>
                                <th scope="col"><?php echo __('Action', 'angelleye-paypal-invoicing'); ?></th>
                                <th scope="col"><?php echo __('Amount', 'angelleye-paypal-invoicing'); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
