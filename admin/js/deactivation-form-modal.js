var $ = jQuery;
$(document).ready(function () {
    var $deactivationModal = $(".deactivation-Modal-paypal-invocing");
    if ($deactivationModal) {
        new ModalWprinvocing($deactivationModal);
    }
    $("#mixpanel-send-deactivation-paypal-invocing").click(function (e) {
        e.preventDefault();
        
        var data = {
            action: 'angelleye_send_deactivation_invocing',
            reason_details: $("#reason-other-details").val(),
            reason: $("input[name='reason']:checked"). val()
            
        };
        $.post(ajaxurl, data, function () {
        })
                .done(function (response) {
                    window.location.replace($('#mixpanel-send-deactivation-paypal-invocing').attr("href"));
                })
                .fail(function (response) {
                    window.location.replace($('#mixpanel-send-deactivation-paypal-invocing').attr("href"));
                });
    });
});
function ModalWprinvocing(aElem) {
    var refThis = this;
    this.elem = aElem;
    this.overlay = $('.deactivation-Modal-paypal-invocing-overlay');
    this.radio = $('input[name=reason]', aElem);
    this.closer = $('.deactivation-Modal-paypal-invocing-close, .deactivation-Modal-paypal-invocing-cancel', aElem);
    this.return = $('.deactivation-Modal-paypal-invocing-return', aElem);
    this.opener = $('.plugins [data-slug="paypal-invoicing-for-wordpress"] .deactivate');
    this.question = $('.deactivation-Modal-paypal-invocing-question', aElem);
    this.button = $('.button-primary', aElem);
    this.title = $('.deactivation-Modal-paypal-invocing-header h2', aElem);
    this.textFields = $('input[type=text], textarea', aElem);
    this.hiddenReason = $('#deactivation-reason', aElem);
    this.hiddenDetails = $('#deactivation-details', aElem);
    this.titleText = this.title.text();
    this.opener.click(function () {
        refThis.open();
        return false;
    });
    this.closer.click(function () {
        refThis.close();
        return false;
    });
    aElem.bind('keyup', function (event) {
        if (event.keyCode == 27) {
            refThis.close();
            return false;
        }
    });
    this.return.click(function () {
        refThis.returnToQuestion();
        return false;
    });
    this.radio.change(function () {
        refThis.change($(this));
    });
    this.textFields.keyup(function () {
        refThis.hiddenDetails.val($(this).val());
        if (refThis.hiddenDetails.val() != '') {
            refThis.button.removeClass('deactivation-isDisabled');
            refThis.button.removeAttr("disabled");
        } else {
            refThis.button.addClass('deactivation-isDisabled');
            refThis.button.attr("disabled", true);
        }
    });
}
ModalWprinvocing.prototype.change = function (aElem) {
    var id = aElem.attr('id');
    var refThis = this;
    this.hiddenReason.val(aElem.val());
    this.hiddenDetails.val('');
    this.textFields.val('');
    $('.deactivation-Modal-paypal-invocing-fieldHidden').removeClass('deactivation-isOpen');
    $('.deactivation-Modal-paypal-invocing-hidden').removeClass('deactivation-isOpen');
    this.button.removeClass('deactivation-isDisabled');
    this.button.removeAttr("disabled");
    switch (id) {
        case 'reason-temporary':
            break;
        case 'reason-broke':
            break;
        case 'reason-complicated':
            break;
            break;
        case 'reason-other':
            var field = aElem.siblings('.deactivation-Modal-paypal-invocing-fieldHidden');
            field.addClass('deactivation-isOpen');
            field.find('input, textarea').focus();
            refThis.button.addClass('deactivation-isDisabled');
            refThis.button.attr("disabled", true);
            break;
    }
};
ModalWprinvocing.prototype.returnToQuestion = function () {
    $('.deactivation-Modal-paypal-invocing-fieldHidden').removeClass('deactivation-isOpen');
    $('.deactivation-Modal-paypal-invocing-hidden').removeClass('deactivation-isOpen');
    this.question.addClass('deactivation-isOpen');
    this.return.removeClass('deactivation-isOpen');
    this.title.text(this.titleText);
    this.hiddenReason.val('');
    this.hiddenDetails.val('');
    this.radio.attr('checked', false);
    this.button.addClass('deactivation-isDisabled');
    this.button.attr("disabled", true);
};
ModalWprinvocing.prototype.open = function () {
    this.elem.css('display', 'block');
    this.overlay.css('display', 'block');
    localStorage.setItem('deactivation-hash', '');
};
ModalWprinvocing.prototype.close = function () {
    this.returnToQuestion();
    this.elem.css('display', 'none');
    this.overlay.css('display', 'none');
};