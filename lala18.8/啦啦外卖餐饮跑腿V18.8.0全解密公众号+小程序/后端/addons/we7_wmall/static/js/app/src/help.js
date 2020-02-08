define([], function() {
	var help = {};
	help.init = function() {
		$(document).on('click', '.item-help-title', function(){
			var $this = $(this);
			if($this.next().hasClass('hide')) {
				$('.item-help-content').addClass('hide');
				$('.item-help-title').find('.fa').removeClass('fa-arrow-up').addClass("fa-arrow-down");
				$this.next().removeClass('hide');
				$this.find('.fa').removeClass('fa-arrow-down').addClass("fa-arrow-up");
			} else {
				$this.next().addClass('hide');
				$this.find('.fa').removeClass('fa-arrow-up').addClass("fa-arrow-down");
			}
		});
	};
	return help;
});