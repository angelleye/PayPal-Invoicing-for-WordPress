(function ($) {
    'use strict';
    $(function () {
        jQuery('#apifw_enable_paypal_sandbox').change(function () {
            var sandbox = jQuery('#apifw_sandbox_client_id, #apifw_sandbox_secret, #apifw_sandbox_paypal_email').closest('.row'),
                    production = jQuery('#apifw_client_id, #apifw_secret, #apifw_paypal_email').closest('.row');
            if (jQuery(this).is(':checked')) {
                sandbox.show();
                production.hide();
            } else {
                sandbox.hide();
                production.show();
            }
        }).change();
        jQuery('#add_new_item').click(function (event) {
            event.preventDefault();
            var $table = $('.invoice-table'),
                    $first_row = $table.find('tbody:last').clone().find('input').val('').end();
            $first_row.removeClass('first_tbody');
            $table.append($first_row);
        });
        jQuery(document).on('click', '.deleteItem', function (event) {
            event.preventDefault();
            jQuery(this).closest('tbody').remove();
        });
        jQuery('#invoice_date').datepicker({
            format: 'mm/dd/yyyy',
            startDate: '-3d'
        });
    });
})(jQuery);
