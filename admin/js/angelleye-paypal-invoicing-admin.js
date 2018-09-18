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
    });
})(jQuery);
