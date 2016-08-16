$(function(){
	$('.modalClick').click(function(){
		$('#model').modal('show')
			.find('#modelContent')
			.load($(this).attr('value'));
	});
    $('.close').click(function () {
        $('#model').modal().find('#modelContent').html('<img src="/deepdive/frontend/web/images/ajax-loader.gif">');
    });
});