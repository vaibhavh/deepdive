$('document').ready(function () {
    $('.modalClick').click(function () {
        $('#model').modal('show')
                .find('#modelContent')
                .load($(this).attr('value'));
    });
});