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
                                <th scope="col"><?php echo __('Date', ''); ?></th>
                                <th scope="col"><?php echo __('Invoice #', ''); ?></th>
                                <th scope="col"><?php echo __('Recipient', ''); ?></th>
                                <th scope="col"><?php echo __('Status', ''); ?></th>
                                <th scope="col"><?php echo __('Action', ''); ?></th>
                                <th scope="col"><?php echo __('Amount', ''); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($this->response)) {
                                echo print_r($this->response, true);
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
                                <th scope="col"><?php echo __('Action', ''); ?></th>
                                <th scope="col"><?php echo __('Amount', ''); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
