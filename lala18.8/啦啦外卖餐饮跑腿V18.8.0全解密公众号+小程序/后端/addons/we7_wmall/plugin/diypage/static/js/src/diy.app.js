define(['tiny'], function(tiny) {
	var diy = {};
	diy.init = function() {
		$(document).off('click', '.toggle-activity');
		$(document).on('click', '.toggle-activity', function(){
			if($(this).hasClass('icon-fold')) {
				$('.single-line-row').addClass('hide');
				$(this).addClass('icon-unfold').removeClass('icon-fold');
			} else {
				$('.single-line-row').removeClass('hide');
				$(this).addClass('icon-fold').removeClass('icon-unfold');
			}
		});
	}
	return diy;
});