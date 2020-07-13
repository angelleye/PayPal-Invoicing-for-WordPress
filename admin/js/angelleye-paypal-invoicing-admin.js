(function ($) {
    'use strict';
    $(function () {
        var el_notice = jQuery(".angelleye-notice");
        el_notice.fadeIn(750);
        jQuery(".angelleye-notice-dismiss").click(function (e) {
            e.preventDefault();
            jQuery(this).parent().parent(".angelleye-notice").fadeOut(600, function () {
                jQuery(this).parent().parent(".angelleye-notice").remove();
            });
            notify_wordpress(jQuery(this).data("msg"));
        });
         var opt_in_logging = jQuery("#angelleye_send_opt_in_logging_details");
        jQuery('#angelleye_send_opt_in_logging_details').each(function (i) {
            jQuery('[id="' + this.id + '"]').slice(1).remove();
        });
        opt_in_logging.fadeIn(750);
        function notify_wordpress(message) {
            var param = {
                action: 'angelleye_dismiss_notice',
                data: message
            };
            jQuery.post(ajaxurl, param);
        }
        jQuery('#apifw_enable_paypal_sandbox').change(function () {
            var sandbox = jQuery('#apifw_sandbox_client_id, #apifw_sandbox_secret').closest('.row'),
                    production = jQuery('#apifw_client_id, #apifw_secret').closest('.row');
            if (jQuery(this).is(':checked')) {
                jQuery('.angelleye_paypal_invoicing_sandbox_connect_box').show();
                jQuery('.angelleye_paypal_invoicing_live_connect_box').hide();
                jQuery('#apifw_sandbox_paypal_email').closest('.row').show();
                jQuery('#apifw_paypal_email').closest('.row').hide();
                production.hide();
                if (jQuery('#apifw_sandbox_client_id').val().length === 0 && jQuery('#apifw_sandbox_secret').val().length === 0) {
                    sandbox.hide();
                } else {
                    sandbox.show();
                }
            } else {
                jQuery('.angelleye_paypal_invoicing_live_connect_box').show();
                jQuery('.angelleye_paypal_invoicing_sandbox_connect_box').hide();
                jQuery('#apifw_sandbox_paypal_email').closest('.row').hide();
                jQuery('#apifw_paypal_email').closest('.row').show();
                sandbox.hide();
                if (jQuery('#apifw_client_id').val().length === 0 && jQuery('#apifw_secret').val().length === 0) {
                    production.hide();
                } else {
                    production.show();
                }

            }
        }).change();
        jQuery('#apifw_enable_sync_paypal_invoice_history').change(function () {
            if (jQuery(this).is(':checked')) {
                jQuery('#sync_paypal_invoice_history_interval').parent().parent().show();
            } else {
                jQuery('#sync_paypal_invoice_history_interval').parent().parent().hide();
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
            $first_row.find('.amount').html('');
            $first_row.find("[name='item_txt_name[]']").val(angelleye_paypal_invoicing_js.tax_name);
            $first_row.find("[name='item_txt_rate[]']").val(angelleye_paypal_invoicing_js.tax_rate);
            $first_row.find("[name='item_qty[]']").val(angelleye_paypal_invoicing_js.item_qty);
            $table.append($first_row);
        });
        jQuery(document).on('click', '.deleteItem', function (event) {
            event.preventDefault();
            jQuery(this).closest('tbody').remove();
            count_sub_total();
        });
        jQuery('#dueDate').datepicker({
            dateFormat: angelleye_paypal_invoicing_js.dateFormat
        });
        jQuery("#dueDate").datepicker("option", "minDate", new Date());
        jQuery('#invoice_date').datepicker({
            dateFormat: angelleye_paypal_invoicing_js.dateFormat,
            onSelect: function (dateText, inst) {
                jQuery("#dueDate").datepicker("option", "minDate",
                        jQuery("#invoice_date").datepicker("getDate"));
            }
        });
        jQuery('#record_payment_invoice_date').datepicker({
            dateFormat: angelleye_paypal_invoicing_js.dateFormat
        });
        jQuery('#record_refund_invoice_date').datepicker({
            dateFormat: angelleye_paypal_invoicing_js.dateFormat
        });
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
        jQuery(document).on('input paste keyup', '#angelleye-paypal-invoicing input, #angelleye-paypal-invoicing select', function (event) {
            count_sub_total();
        });
        function count_sub_total() {
            var total = 0;
            var i = 0;
            var tax_array = [];
            var new_tax_array = [];
            jQuery('#tax_tr_0').html('');
            jQuery('input[name="item_name[]"]').each(function () {
                jQuery('#tax_tr_' + (i + 1)).html('');
                var qty = parseInt(jQuery(this).parent().next().children('input').val());
                if (isNaN(qty)) {
                    tax = 0;
                }
                var price = parseFloat(jQuery(this).parent().next().next().children('input').val()).toFixed(2);
                if (isNaN(price)) {
                    tax = 0.00;
                }
                var tax_name = jQuery(this).parent().next().next().next().children('input').val();
                if (tax_name != '') {
                    jQuery(this).parent().next().next().next().next().children().children('input').attr("required", true);
                } else {
                    jQuery(this).parent().next().next().next().next().children().children('input').attr("required", false);
                    jQuery(this).parent().next().next().next().next().children().children('input').trigger("change");
                }
                var tax = parseFloat(jQuery(this).parent().next().next().next().next().children().children('input').val());
                if (isNaN(tax)) {
                    tax = 0.00;
                }
                var amount = (qty * price);
                var temp_amount = ((amount * tax) / 100);
                if (isNaN(amount)) {
                    amount = 0.00;
                }
                if (tax_name != '' && tax != '') {
                    var tax_array_new = {
                        tax_digit: tax,
                        tax_name: tax_name,
                        tax_amount: parseFloat(temp_amount)
                    };
                    tax_array.push(tax_array_new);
                }
                jQuery(this).parent().next().next().next().next().next().html('$' + amount.toFixed(2));
                total = total + amount;
                i++;
            });
            jQuery.each(tax_array, function (index, tax_data) {
                if (index === 0) {
                    new_tax_array[tax_data.tax_name] = [];
                    new_tax_array[index] = [];
                    var tax_name = tax_data.tax_name;
                    var tax_digit = tax_data.tax_digit;
                    new_tax_array[index] = {tax_name, tax_digit};
                    new_tax_array[tax_data.tax_name][tax_data.tax_digit] = {tax_digit: tax_data.tax_digit,
                        tax_name: tax_data.tax_name,
                        tax_amount: parseFloat(tax_data.tax_amount).toFixed(2)
                    };
                } else {
                    if (typeof new_tax_array[tax_data.tax_name] === 'undefined') {
                        new_tax_array[tax_data.tax_name] = [];
                        new_tax_array[index] = [];
                        var tax_name = tax_data.tax_name;
                        var tax_digit = tax_data.tax_digit;
                        new_tax_array[index] = {tax_name, tax_digit};
                    }
                    if (typeof new_tax_array[tax_data.tax_name][tax_data.tax_digit] !== 'undefined') {
                        var old_tax_amount = new_tax_array[tax_data.tax_name][tax_data.tax_digit].tax_amount;
                        var new_tax_amount = parseFloat(old_tax_amount) + parseFloat(tax_data.tax_amount);
                        new_tax_array[tax_data.tax_name][tax_data.tax_digit] = {tax_digit: tax_data.tax_digit,
                            tax_name: tax_data.tax_name,
                            tax_amount: parseFloat(new_tax_amount).toFixed(2)
                        };
                    } else {
                        var tax_name = tax_data.tax_name;
                        var tax_digit = tax_data.tax_digit;
                        new_tax_array[index] = {tax_name, tax_digit};
                        new_tax_array[tax_data.tax_name][tax_data.tax_digit] = {tax_digit: tax_data.tax_digit,
                            tax_name: tax_data.tax_name,
                            tax_amount: parseFloat(tax_data.tax_amount).toFixed(2)
                        };
                    }
                }
            });
            var i_tax_index = 0;
            var new_unique_tax_array = [];
            jQuery.each(new_tax_array, function (index, new_tax_array_first_loop) {
                if (typeof new_tax_array_first_loop !== 'undefined') {
                    new_unique_tax_array = new_tax_array[new_tax_array_first_loop.tax_name][new_tax_array_first_loop.tax_digit];
                    if (jQuery('#tax_tr_' + i_tax_index).length) {
                        jQuery('#tax_tr_' + i_tax_index).html('<td colspan="3"><b>Tax (' + new_unique_tax_array.tax_digit + '%) </b>' + new_unique_tax_array.tax_name + '</td><td>$<span class="tax_to_add">' + new_unique_tax_array.tax_amount + '</span></td>');
                    } else {
                        var next_id = 'tax_tr_' + i_tax_index;
                        jQuery('#tax_tr_' + (i_tax_index - 1)).after('<tr class="dynamic_tax" id="' + next_id + '"></tr>');
                        jQuery('#tax_tr_' + i_tax_index).html('<td colspan="3"><b>Tax (' + new_unique_tax_array.tax_digit + '%) </b>' + new_unique_tax_array.tax_name + '</td><td>$<span class="tax_to_add">' + new_unique_tax_array.tax_amount + '</span></td>');
                    }
                    i_tax_index++;
                }
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
                        'order_id': angelleye_paypal_invoicing_js.order_id,
                        'move_to_trace_url': jQuery(this).attr("href")
                    };
                    jQuery.post(ajaxurl, data, function (response) {
                        
                    });
                }
            });
            jQuery('#angelleye_record_payment').click(function (event) {
                var data = {
                    'action': 'angelleye_paypal_invoicing_record_payment',
                    'invoice_id': jQuery('#angelleye_paypal_invoice_id').val(),
                    'payment_amount' : jQuery('#angelleye_record_payment_amount').val(),
                    'payment_date' : jQuery('#record_payment_invoice_date').val(),
                    'payment_method' : jQuery('#angelleye_paypal_invoice_payment_method').val(),
                    'payment_note' : jQuery('#angelleye_paypal_invoice_payment_note').val(),
                };
                jQuery.post(ajaxurl, data, function (response) {
                    console.log(response);
                    window.location.href = response.data;
                });

            });
            jQuery('#angelleye_record_refund').click(function (event) {
                console.log('refund');
                var data = {
                    'action': 'angelleye_paypal_invoicing_record_refund',
                    'invoice_id': jQuery('#angelleye_paypal_invoice_id').val(),
                    'refund_amount' : jQuery('#angelleye_record_refund_amount').val(),
                    'refund_date' : jQuery('#record_refund_invoice_date').val(),
                    'refund_method' : jQuery('#angelleye_paypal_invoice_payment_method').val(),
                    'refund_note' : jQuery('#angelleye_paypal_invoice_payment_note').val(),
                };
                jQuery.post(ajaxurl, data, function (response) {
                    console.log(response);
                    window.location.href = response.data;
                });

            });
            var sandbox = jQuery('#apifw_sandbox_client_id, #apifw_sandbox_secret').closest('.row'),
                    production = jQuery('#apifw_client_id, #apifw_secret').closest('.row');
            jQuery('.angelleye-invoice-toggle-settings').click(function (evt) {
                evt.preventDefault();
                if (jQuery('#apifw_enable_paypal_sandbox').is(':checked')) {
                    sandbox.toggle();
                    production.hide();
                } else {
                    sandbox.hide();
                    production.toggle();
                }
            });
            if (angelleye_paypal_invoicing_js.is_ssl == "yes") {
                jQuery("#apifw_company_logo").after('<a href="#" id="checkout_logo" class="button_upload button">Upload</a>');
            }
            var custom_uploader;
            $('.button_upload').click(function (e) {
                var BTthis = jQuery(this);
                e.preventDefault();
                custom_uploader = wp.media.frames.file_frame = wp.media({
                    title: angelleye_paypal_invoicing_js.choose_image,
                    button: {
                        text: angelleye_paypal_invoicing_js.choose_image
                    },
                    multiple: false
                });
                custom_uploader.on('select', function () {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    var pre_input = BTthis.prev();
                    var url = attachment.url;
                    if (BTthis.attr('id') != 'upload') {
                        if (attachment.url.indexOf('http:') > -1) {
                            url = url.replace('http', 'https');
                        }
                    }
                    pre_input.val(url);
                });
                //Open the uploader dialog
                custom_uploader.open();
            });

        });
        $(".icon_header_ppiw_one").toggle(
                function () {				
                        $('.icon_header_ppiw_one').html('').html('<span class="dashicons dashicons-minus" style="float: right;margin-top: 13px;"></span>');
                        $('.card-body_ppiw_one').show('slow');
                        
                },
                function () {
                        $('.icon_header_ppiw_one').html('').html('<span class="dashicons dashicons-plus" style="float: right;margin-top: 13px;"></span>');
                        $('.card-body_ppiw_one').hide('slow');
                }
        );

        $(".icon_header_ppiw_two").toggle(
                function () {				
                        $('.icon_header_ppiw_two').html('').html('<span class="dashicons dashicons-minus" style="float: right;margin-top: 13px;"></span>');
                        $('.card-body_ppiw_two').show('slow');
                        
                },
                function () {
                        $('.icon_header_ppiw_two').html('').html('<span class="dashicons dashicons-plus" style="float: right;margin-top: 13px;"></span>');
                        $('.card-body_ppiw_two').hide('slow');
                }
        );
    });
})(jQuery);