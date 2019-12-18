jQuery(document).ready(function ($) {

    $('form.qterest-form').submit(function (e) {
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
                url: wpApiSettings.root + 'qte/v1/contact',
                method: 'POST',
                data: formData
            })
            .done(function (data) {
                if (data.success) {
                    $(successMessages).html(data.success_msg);
                    $(successMessages).addClass('show');
                    $(fieldsContainer).hide();
                } else {
                    $(errorMessages).html(data.error_msg);
                    $(errorMessages).addClass('show');
                }
                $(form).removeClass('loading');
                $(window).scrollTop($(form).offset().top - 30);
            })

    })

    $('.qterest-toggles').each(function () {
        var toggler = this;
        $('[data-qterest-toggles-on="' + $(this).attr('id') + '"]').each(function () {
            $(this).toggleClass('qterest-hide', !$(toggler).prop('checked'));

            $(this).find('input').each(function () {
                if ($(toggler).prop('checked')) {
                    if ($(this).hasClass('required')) {
                        $(this).prop('required', true);
                    }
                } else {
                    $(this).prop('required', false);
                }
            })

        });
    });

    $('.qterest-toggles').change(function () {
        var toggler = this;
        $('[data-qterest-toggles-on="' + $(this).attr('id') + '"]').each(function () {
            $(this).toggleClass('qterest-hide', !$(toggler).prop('checked'));

            $(this).find('input').each(function () {
                if ($(toggler).prop('checked')) {
                    if ($(this).hasClass('required')) {
                        $(this).prop('required', true);
                    }
                } else {
                    $(this).prop('required', false);
                }
            })

        });
    });

})
