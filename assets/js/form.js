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
        .done(function(data){
            if(data.success){
                $(successMessages).html(data.success_msg);
                $(successMessages).addClass('show');
                $(fieldsContainer).hide();
            } else {
                $(errorMessages).html(data.error_msg);
                $(errorMessages).addClass('show');
            }
            $(form).removeClass('loading');
        })

    })

})