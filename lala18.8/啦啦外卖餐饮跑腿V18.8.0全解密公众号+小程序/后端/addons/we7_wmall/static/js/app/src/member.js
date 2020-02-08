define(['tiny'], function(tiny) {
	var member = {};
	member.initFavorite = function() {
		$(document).on('click', '#btn-favorite', function(){
			var $this = $(this);
			var id = $(this).data('id');
			if(!id) return false;
			var type = 'star';
			if($(this).find('i').hasClass('icon-favorfill')) {
				type = 'cancel';
			}
			$.post(tiny.getUrl('wmall/member/op/favorite'), {id: id, type: type}, function(data){
				var result = $.parseJSON(data);
				if(result.message.errno != 0) {
					$.toast(result.message.message, result.message.url);
				} else {
					if(type == 'cancel') {
						$this.find('i').removeClass('icon-favorfill').addClass('icon-favor');
						$.toast('取消收藏成功');
					} else {
						$this.find('i').addClass('icon-favorfill').removeClass('icon-favor');
						$.toast('添加收藏成功');
					}
				}
				return false;
			});
		});
	};
	return member;
});