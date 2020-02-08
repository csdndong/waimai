define(['tiny', 'laytpl', 'jquery.circliful'], function(tiny, laytpl) {
	var coupon = {};
	coupon.initChannel = function() {
		$('.myStat').circliful();
		var loading = false;
		$(document).on('infinite', '.infinite-scroll',function() {
			var $this = $(this);
			var id = $this.data('min');
			if(!id) return;
			if (loading) return;
			loading = true;

			$this.find('.infinite-scroll-preloader').removeClass('hide');
			$.post(tiny.getUrl('wmall/channel/coupon/list'), {min: id}, function(data){
				var result = $.parseJSON(data);
				$this.attr('data-min', result.message.min);
				if(!result.message.min) {
					$.detachInfiniteScroll($('.infinite-scroll'));
					$('.infinite-scroll-preloader').remove();
					return;
				}
				$this.find('.infinite-scroll-preloader').removeClass('hide');
				var gettpl = $('#tpl-coupon').html();
				loading = false;
				laytpl(gettpl).render(result.message.message, function(html){
					$this.find(".coupon-list").append(html);
					$('.myStat').not('.circliful').circliful();
				});
			});
		});

		$(document).ready(function() {
			$('.coupon-list').on('click', '.button-get-coupon', function(){
				var id = $(this).data('id');
				var sid = $(this).data('sid');
				var $this=$(this);
				$.post(tiny.getUrl('wmall/channel/coupon/get'), {id: id, sid: sid}, function(data){
					var result = $.parseJSON(data);
					if(result.message.errno != 0) {
						$.toast(result.message.message);
					} else {
						$.toast(result.message.message);
						$this.parent().parent().prev().removeClass('hide');
						$this.parent().parent().addClass('hide');
					}
					return false;
				})
			})
		});
	};
	return coupon;
});