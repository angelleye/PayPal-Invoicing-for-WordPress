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
            if (jQuery(this).val() === 'DUE_ON_DATE_SPECIFIED') {
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
            count_sub_total();
        });
        jQuery('#invoice_date, #dueDate').datepicker({dateFormat: 'dd/mm/yy'});
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
        jQuery("#memoHideLink").click(function (event) {
            event.preventDefault();
            jQuery(".memoDetail").hide();
            jQuery(".memoHead").show();
        });
        jQuery('#apifw_shipping_amount').change(function () {
            if (jQuery(this).val() === 'DUE_ON_DATE_SPECIFIED') {
                jQuery('#dueDate_box').show();
            } else {
                jQuery('#dueDate_box').hide();
            }
        }).change();
        jQuery(document).on('change', '#apifw_shipping_amount', function (event) {
            var newVal = parseFloat(jQuery('#apifw_shipping_amount').val(), 10).toFixed(2);
            if (newVal != 'NaN') {
                jQuery('#apifw_shipping_amount').val(newVal);
            }
        });
        jQuery(document).on('change blur keyup', '#angelleye-paypal-invoicing input, #angelleye-paypal-invoicing select', function (event) {
            count_sub_total();
        });
        function count_sub_total() {
            var total = 0;
            var i = 0;
            jQuery('input[name="item_name[]"]').each(function () {
                jQuery('#tax_tr_' + (i + 1)).html('');
                var qty = parseInt(jQuery(this).parent().next().children('input[type="text"]').val());
                if (isNaN(qty)) {
                    tax = 0;
                }
                var price = parseFloat(jQuery(this).parent().next().next().children('input[type="text"]').val()).toFixed(2);
                if (isNaN(price)) {
                    tax = 0.00;
                }
                var tax_name = jQuery(this).parent().next().next().next().children('input[type="text"]').val();
                var tax = parseFloat(jQuery(this).parent().next().next().next().next().children('input[type="text"]').val());
                if (isNaN(tax)) {
                    tax = 0.00;
                }
                var amount = (qty * price);
                var temp_amount = ((amount * tax) / 100);
                if (isNaN(amount)) {
                    amount = 0.00;
                }

                if (jQuery('#tax_tr_' + i).length) {
                    if (tax > 0 && jQuery.isNumeric(temp_amount)) {
                        jQuery('#tax_tr_' + i).html('<td colspan="3"><b>Tax (' + tax + '%) </b>' + tax_name + '</td><td>$<span class="tax_to_add">' + parseFloat(temp_amount).toFixed(2) + '</span></td>');
                    } else {
                        jQuery('#tax_tr_' + i).html('');
                    }
                } else {
                    var next_id = 'tax_tr_' + i;
                    jQuery('#tax_tr_' + (i - 1)).after('<tr class="dynamic_tax" id="' + next_id + '"></tr>');
                    if (tax > 0 && jQuery.isNumeric(temp_amount)) {
                        jQuery('#tax_tr_' + i).html('<td colspan="3"><b>Tax (' + tax + '%) </b>' + tax_name + '</td><td>$<span class="tax_to_add">' + parseFloat(temp_amount).toFixed(2) + '</span></td>');
                    } else {
                        jQuery('#tax_tr_' + i).html('');
                    }
                }
                jQuery(this).parent().next().next().next().next().next().html('$' + amount.toFixed(2));
                console.log("qty: " + qty + "  price: " + price + " tax: " + tax + " tax_name: " + tax_name);
                total = total + amount;
                i++;
            });
            jQuery('.itemSubTotal').text('$' + total.toFixed(2));
            countFinalTotal(jQuery('input[name="invDiscount"]').val());
        }
        function countFinalTotal(val) {
            var total = Number(jQuery('.itemSubTotal').text().replace(/[^0-9\.-]+/g, ""));
            var discountBase = jQuery('select[name="invoiceDiscType"]').val();
            var discount = parseFloat(val);
            var discountAmt = 0;
            if (discountBase == 'percentage') {
                discountAmt = parseFloat((total * discount) / 100);
            } else if (discountBase == 'dollar') {
                discountAmt = parseFloat(val);
            }
            if (isNaN(discountAmt)) {
                discountAmt = 0.00;
            }
            jQuery('.invoiceDiscountAmount').text('$' + discountAmt.toFixed(2));

            var shippingAmount = parseFloat(jQuery('input[name="shippingAmount"]').val());
            if (isNaN(shippingAmount))
            {
                shippingAmount = 0.00;
            }
            jQuery('.shippingAmountTd').text('$' + shippingAmount.toFixed(2));
            var taxs = 0;
            jQuery('.tax_to_add').each(function () {
                taxs = taxs + parseFloat(jQuery(this).html());
            });

            var finalTotal = total - discountAmt + shippingAmount + taxs;

            jQuery('.finalTotal').text('$' + finalTotal.toFixed(2) + ' USD');
        }

        jQuery(document).ready(function ($) {
            jQuery('.order_actions .submitdelete').click(function (event) {
                if (!confirm(angelleye_paypal_invoicing_js.move_trace_confirm_string)) {
                    event.preventDefault();
                } else {
                    var data = {
                        'action': 'angelleye_paypal_invoicing_wc_delete_paypal_invoice_ajax',
                        'invoice_post_id': angelleye_paypal_invoicing_js.invoice_post_id,
                        'order_id' : angelleye_paypal_invoicing_js.order_id
                    };
                    jQuery.post(ajaxurl, data, function (response) {
                        alert('Got this from the server: ' + response);
                    });
                }
            });
        });
    });
})(jQuery);


