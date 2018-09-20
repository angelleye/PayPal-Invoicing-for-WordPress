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
        jQuery('#invoiceTerms').change(function () {
            if (jQuery(this).val() === 'specified') {
                jQuery('#dueDate_box').show();
            } else {
                jQuery('#dueDate_box').hide();
            }
        }).change();
        jQuery('#allowPartialPayments').change(function () {
            if (jQuery(this).is(':checked')) {
                jQuery('.allow_partial_payment_content_box').show();
            } else {
                jQuery('.allow_partial_payment_content_box').hide();
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
        jQuery('#invoice_date, #dueDate').datepicker();
        jQuery('[data-toggle="tooltip"]').tooltip();
        jQuery(".memoHead").click(function () {
            jQuery(".memoDetail").show();
            jQuery(".memoHead").hide();
            
        });
        jQuery("#memoHideLink").click(function (event) {
            event.preventDefault();
            jQuery(".memoDetail").hide();
            jQuery(".memoHead").show();
        });

    });

})(jQuery);


