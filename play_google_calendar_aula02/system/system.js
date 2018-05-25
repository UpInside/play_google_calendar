$(function () {

    $('html').on('submit', 'form', function () {

        var form = $(this);
        var action = form.attr('action');

        form.ajaxSubmit({
            url: 'controller.php',
            data: {action: action},
            dataType: 'json',
            success: function (response) {

                if (response.msg) {
                    var component = $('form > p.result');

                    if (component.length) {
                        component.remove();
                        $('form').prepend('<p class="result ' + response.msg[0] + '">' + response.msg[1] + '</p>');
                    } else {
                        $('form').prepend('<p class="result ' + response.msg[0] + '">' + response.msg[1] + '</p>');
                    }

                }
            }
        });

        return false;

    });

    $('.j_delete').click(function(){

        $.post('controller.php', {action: 'delete_appointment', value: $(this).data('id')}, function (response) {
            if (response.redirect) {
                window.location.reload();
            }
        }, 'json');

    });

});