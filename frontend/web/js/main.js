$('document').ready(function () {
    $(document).on('click', '.modalClick', function () {
        $('#model').modal('show')
                .find('#modelContent')
                .load($(this).attr('value'));
    });
    $(document).on('click', '.close', function () {
        $('#model').modal().find('#modelContent').html('<img src="/deepdive/frontend/web/images/ajax-loader.gif">');
    });
});