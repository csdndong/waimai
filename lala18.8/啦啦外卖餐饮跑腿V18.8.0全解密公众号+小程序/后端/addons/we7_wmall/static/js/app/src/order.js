define(['tiny'], function(tiny) {
	var order = {};
	order.initDetail = function(params) {
		if(params.order.orderGrant_share > 0) {
			window.sharedata.success = function(){
				$.showIndicator();
				var url = tiny.getUrl('ordergrant/share/grant', {id: params.order.id}, true);
				$.post(url, function(dat){
					var result = $.parseJSON(dat);
					$.hideIndicator();
					$.toast(result.message.message, location.href);
				});
			}
		}

		if(params.order.delivery_handle_type != 'app') {
			return false;
		}
		var map = new AMap.Map('map', {
			resizeEnable: true,
			center: [116.397428, 39.90923],
			zoom: 13
		});
		var current_map = new AMap.Map('map-current', {
			resizeEnable: true,
			center: [116.397428, 39.90923],
			zoom: 15
		});

		if(params.order.location_y && params.order.location_x) {
			var marker = new AMap.Marker({
				position: [params.order.location_y, params.order.location_x],
				offset: new AMap.Pixel(-35, -35),
				content: '<div class="marker-mine-route"></div>'
			});
			marker.setMap(current_map);
		}

		if(params.store.location_y && params.store.location_x) {
			var marker = new AMap.Marker({
				position: [params.store.location_y, params.store.location_x],
				offset: new AMap.Pixel(-33, -70),
				content: '<div class="marker-start-head-route"><img src="' + params.store.logo + '" alt=""/></div>'
			});
			marker.setMap(current_map);
		}

		if(params.order.status == 5) {
			map.panTo([params.order.delivery_success_location_y, params.order.delivery_success_location_x]);
			current_map.panTo([params.order.delivery_success_location_y, params.order.delivery_success_location_x]);

			var marker = new AMap.Marker({
				position: [params.order.delivery_success_location_y, params.order.delivery_success_location_x],
				offset: new AMap.Pixel(-26, -80),
				content: '<div class="marker-deliveyer-route"><img src='+ params.deliveryer.avatar +' alt=""/></div>'
			});
			marker.setMap(map);

			var marker = new AMap.Marker({
				position: [params.order.delivery_success_location_y, params.order.delivery_success_location_x],
				offset: new AMap.Pixel(-26, -80),
				content: '<div class="marker-deliveyer-route"><img src='+ params.deliveryer.avatar +' alt=""/></div>'
			});
			marker.setMap(current_map);
		} else {
			map.panTo([params.deliveryer.location_y, params.deliveryer.location_x]);
			var marker = new AMap.Marker({
				position: [params.deliveryer.location_y, params.deliveryer.location_x],
				offset: new AMap.Pixel(-26, -80),
				content: '<div class="marker-deliveyer-route"><img src='+ params.deliveryer.avatar +' alt=""/></div>'
			});
			marker.setMap(map);
		}

		var set = '';
		map.on('click', function(){
			setTimeout(function(){
				current_map.setFitView();
			}, 500);
			position_sync();
			set = setInterval(position_sync, 60000);
			$.popup('.popup-order-map-info');
		});

		$('.btn-close-popup').click(function(){
			clearInterval(set);
			$.closeModal('.popup-order-map-info');
		});
		$('.btn-refresh').click(function(){
			position_sync();
		});
		$('.btn-info').click(function(){
			alert('配送员位置一分钟更新一次，如果配送员远离您，那可能是正在为更早下单的用户配送，请耐心等待~');
		});

		function position_sync() {
			$.showIndicator();
			if(params.order.status == 5) {
				$.hideIndicator();
				return;
			}
			var markers = [];
			$.post(tiny.getUrl('system/common/deliveryer/location'), {id: params.order.deliveryer_id}, function(data){
				$.hideIndicator();
				var result = $.parseJSON(data);
				if(result.message.errno != -1) {
					var deliveryer = result.message.message;
					var marker = new AMap.Marker({
						position: [deliveryer.location_y, deliveryer.location_x],
						offset: new AMap.Pixel(-26, -80),
						content: '<div class="marker-deliveyer-route"><img src="'+ deliveryer.avatar +'" alt=""/></div>'
					});
					var marker1 = new AMap.Marker({
						position: [deliveryer.location_y, deliveryer.location_x],
						offset: new AMap.Pixel(-26, -80),
						content: '<div class="marker-deliveyer-route"><img src="'+ deliveryer.avatar +'" alt=""/></div>'
					});
					map.panTo([deliveryer.location_y, deliveryer.location_x]);
					map.remove(markers);
					marker.setMap(map);

					current_map.panTo([deliveryer.location_y, deliveryer.location_x]);
					current_map.remove(markers);
					marker1.setMap(current_map);
					current_map.setFitView();
					markers.push(marker);
					markers.push(marker1);
				}
			});
		}
	};

	order.initComment = function(){
		$(document).on('click', '.star-outline label', function(){
			$(this).parent().find('.radio').removeClass('checked').prop('checked', false);
			$(this).prevAll().find('.radio').prop('checked', true);
			$(this).find('.radio').addClass('checked').prop('checked', true);
		});

		$(document).on('click', '.submit-com', function(){
			var $this = $(this);
			var order_id = $this.data('id');
			if($this.hasClass('disabled')) {
				return false;
			}
			$this.addClass('disabled');
			var params = {
				id: order_id,
				goods: {},
				thumbs: []
			};
			$('.star-outline').each(function(){
				var name = $(this).data('name');
				var value = $(this).find('.radio.checked').val();
				params[name] = value;
			});
			if(!params.delivery_service) {
				$this.removeClass('disabled')
				$.toast('请评价配送服务');
				return false;
			}
			if(!params.goods_quality) {
				$this.removeClass('disabled')
				$.toast('请评价商品质量');
				return false;
			}
			var note = $.trim($('.note').val());
			params.note = note;
			$('.goods-list').each(function(){
				var id = $(this).data('id');
				params.goods[id] = $(this).find('.radio:checked').val();
			});
			$('.tpl-image .image-item input[type!="file"]').each(function(){
				var value = $.trim($(this).val());
				if(value) {
					params.thumbs.push(value);
				}
			});
			$.post(tiny.getUrl('wmall/order/comment'), params, function(data){
				var result = $.parseJSON(data);
				if(result.message.errno != 0) {
					$this.removeClass('disabled')
					$.toast(result.message.message);
				} else {
					$.toast('评价成功', tiny.getUrl('wmall/order/index/detail', {id: order_id}));
				}
				return false;
			});
		});
	};
	return order;
});