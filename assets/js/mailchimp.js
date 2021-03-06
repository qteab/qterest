jQuery(document).ready(function ($) {

    $('form.qterest-mailchimp-signup').submit(function (e) {
        e.preventDefault();

        let form = this;
        let errorMessages = $(form).find('.qterest-error-messages');
        let successMessages = $(form).find('.qterest-success-messages');
        let fieldsContainer = $(form).find('.qterest-form-fields');
        var formData = $(form).serialize();

        $(form).addClass('loading');

        $(errorMessages).removeClass('show');
        $(successMessages).removeClass('show');

        $.ajax({
            url: wpApiSettings.root + 'qte/v1/mailchimp/add-subscriber',
            method: 'POST',
            data: formData
        })
        .done(function (data) {
            if (data.success) {
                $(successMessages).html(data.success_msg);
                $(successMessages).addClass('show');
                $(fieldsContainer).hide();

                $(form).trigger('qterestSubmitted', [data]);
            } else {
                $(errorMessages).html(data.error_msg);
                $(errorMessages).addClass('show');

                $(form).trigger('qterestError', [data]);
            }

            $(form).removeClass('loading');
        });
    });
});
