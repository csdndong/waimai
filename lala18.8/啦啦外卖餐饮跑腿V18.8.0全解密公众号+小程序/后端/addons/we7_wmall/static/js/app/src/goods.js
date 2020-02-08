define(['tiny'], function(tiny) {
	var goods = {
		selected: 0,
		data: {},
		cart: {
			list: ''
		}
	};
	goods.init = function (params) {
		window.tmodtpl = params.tmodtpl;
		goods.attachurl = params.attachurl;
		goods.data = params.categorys;
		goods.cart = params.cart;
		goods.store = params.store;
		tmodtpl.helper("tomedia", function(src) {
			if (typeof src != 'string') {
				return '';
			}
			if(src.indexOf('http://') == 0 || src.indexOf('https://') == 0 || src.indexOf('../addons') == 0) {
				return src;
			}
			if(src.indexOf('images/') == 0) {
				return goods.attachurl + src;
			}
		});
		goods.initCateMenu();
		goods.initClick();
		goods.initTitle();
		goods.initGoods();
//		goods.initCart();
	};
	goods.initCateMenu = function() {
		var html = tmodtpl("tpl-parent-category", goods);
		$("#cateMenu").html(html);
	};
	goods.initClick = function() {
		$(document).off('click', "#cateMenu li");
		$(document).on('click', '#cateMenu li', function() {
			var index = $(this).data('index');
			if(index == goods.selected) {
				return;
			}
			goods.selected = parseInt(index);
			goods.initCateMenu();
			goods.initTitle();
			goods.initGoods();
		});
		$(document).on('click', '.goods-popup', function(){
			var id = $(this).data('id');
			$.showIndicator();
			$.post(tiny.getUrl('wmall/store/goods1/detail'), {id: id, sid: goods.store.id}, function(result) {
				if(result.message.errno != 0) {
					$.toast(result.message.message);
				} else {
					goods.detail = result.message.message;
					var html = tmodtpl("goods-detail", goods);
					$.popup(html);
					$(".swiper-container").swiper({autoplay: 1000});
				}
				$.hideIndicator();
				return false;
			}, 'json');
		});
		$(document).off('click', ".icon-plus");
		$(document).on('click', ".icon-plus", function() {
			var goodsid = $(this).data('goods-id');
			var optionid = $(this).data('option-id');
			goods.updateCart(goodsid, optionid, 2);
		});
	};
	goods.initTitle = function () {
		var index = goods.selected;
		var item = goods.data[index];
		if (item) {
			$("#title").text(item.title || "未命名");
			if (item.min_fee > 0) {
				$("#min_fee").text('最低消费'+item.min_fee+'元').show();
			} else {
				$("#min_fee").hide()
			}
		}
	};
	goods.initGoods = function () {
		var index = goods.selected;
		var item = goods.data[index];
		if (!item) {
			goods.initCateMenu();
			goods.initTitle();
			goods.initGoods();
			return;
		}
		if (item.data && item.data.length > 0) {
			goods.showGoods();
		} else {
			goods.getGoods();
		}
	};
	goods.showGoods = function () {
		var index = goods.selected;
		if (!goods.data[index]) {
			return;
		}
		$('.children-category-wrapper ul').empty();
		if (!goods.data[index].goods) {
			return;
		}
		$(".goods-list-empty").hide();
		var html = tmodtpl("tpl-category-goods", goods);
		$('.children-category-wrapper ul').html(html);
	};
	goods.getGoods = function() {
		var index = goods.selected;
		var item = goods.data[index];
		if(item.empty) {
			$('.goods-list-empty').show();
			$('.children-category-wrapper ul').empty();
			return;
		}
		$.showIndicator();
		var params = {cateid: item.id, sid: goods.store.id};
		$.post(tiny.getUrl('wmall/store/goods1/goods'), params, function(result) {
			if(!result.message.errno) {
				if (result.message.message.total <= 0) {
					goods.data[index].empty = 1;
					$(".goods-list-empty").show();
				} else {
					goods.data[index].goods = result.message.message.goods;
					goods.data.cart = result.message.message.cart;
				}
				$.hideIndicator();
				goods.showGoods();
			}
		}, 'json');
	}
/*	goods.initCart = function() {
		var cart = goods.cart;
		if(!$.isArray(cart.list)) {
			cart.list = [];
		}
		if (cart.list.length < 1) {
			$('#cartEmpty').removeClass('hide');
			$('#cartNotEmpty').addClass('hide');
			$('#cartNum').addClass('hide');
			$("#totalPrice").html("0");
		} else {
			$('#cartEmpty').addClass('hide');
			$('#cartNotEmpty').removeClass('hide');
			$('#cartNum').removeClass('hide').html(goods.cart.total);
			$("#totalPrice").html(goods.cart.totalprice);
			var html = tmodtpl("goods-cart", goods);
			$('.popup-shop-cart .shop-cart-list').html(html);
		}
	};
	goods.getCart = function() {
		$.post(tiny.getUrl('wmall/store/goods1/getCart'), {}, function(result) {
			if (result.messgae.errno == 0) {
				$.toast(result.messgae.message);
				return;
			}
			goods.cart = result.messgae.message;
			goods.initCart();
		}, 'json');
	}*/

	goods.updateCart = function(goodsid, optionid, num) {
		if(num < 0) {
			num = 0;
		}
		var params = {
			sid: goods.store.id,
			goodsid: goodsid,
			optionid: optionid,
			num: num
		}
		$.post(tiny.getUrl('wmall/store/goods1/updateCart'), params, function(result) {

		}, 'json');
	};
	return goods;
});