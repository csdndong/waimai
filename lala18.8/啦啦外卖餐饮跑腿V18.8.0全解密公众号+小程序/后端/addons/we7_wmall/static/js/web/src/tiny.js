define(['bootstrap'], function($) {
	var tiny = {};
	tiny.getUrl = function(routes, params) {
		var routes = routes.split('/');
		var pre = 'ctrl=' + routes[0] + '&ac=' + routes[1] + '&op=' + routes[2];
		if(routes[3]) {
			pre += '&ta=' + routes[3];
		}
		var url = "./index.php?c=site&a=entry&m=we7_wmall&do=web&" + pre;
		if(window.sysinfo.agent == 1) {
			url = "./wagent.php?c=site&a=entry&m=we7_wmall&do=web&" + pre + '&i=' + window.sysinfo.uniacid;
		} else if(window.sysinfo.merchant == 1) {
			url = "./wmerchant.php?c=site&a=entry&m=we7_wmall&do=web&" + pre + '&i=' + window.sysinfo.uniacid;
		}
		if (params) {
			if (typeof params == "object") {
				url += "&" + $.toQueryString(params);
			} else if (typeof params == "string") {
				url += "&" + params;
			}
		}
		return url;
	};

	tiny.countDown = function(time, day_elem, hour_elem, minute_elem, second_elem, split){
		if(!time) {
			return false;
		}
		var end_time = (typeof time == 'string' ? new Date(time).getTime() / 1000 : time),
			sys_second = parseInt(end_time-new Date().getTime()/1000);
		var timer = setInterval(function(){
			if (sys_second > 0) {
				sys_second -= 1;
				var day = Math.floor((sys_second / 3600) / 24);
				var hour = Math.floor((sys_second / 3600) % 24);
				var minute = Math.floor((sys_second / 60) % 60);
				var second = Math.floor(sys_second % 60);
				day = day < 10 ? "0" + day : day;
				hour = hour < 10 ? "0" + hour : hour;
				minute = minute < 10 ? "0" + minute : minute;
				second = second < 10 ? "0" + second : second;
				if(!split) {
					$(day_elem).text(day);
					$(hour_elem).text(hour);
					$(minute_elem).text(minute);//计算分
					$(second_elem).text(second);// 计算秒
				} else {
					day = String(day).split('');
					hour = String(hour).split('');
					minute = String(minute).split('');
					second = String(second).split('');
					$(day_elem + '_0').text(day[0]);
					$(day_elem + '_1').text(day[1]);
					$(hour_elem + '_0').text(hour[0]);
					$(hour_elem + '_1').text(hour[1]);
					$(minute_elem + '_0').text(minute[0]);
					$(minute_elem + '_1').text(minute[1]);
					$(second_elem + '_0').text(second[0]);
					$(second_elem + '_1').text(second[1]);
				}
			} else {
				clearInterval(timer);
			}
		}, 1000);
	};

	tiny.selectLink = function(callback) {
		$('#select-link-modal').remove();
		$.ajax(tiny.getUrl('common/link'), {
			type: "get",
			dataType: "html",
			cache: false
		}).done(function(html) {
			modal = $('<div class="modal fade" id="select-link-modal"></div>');
			$(document.body).append(modal), modal.modal("show");
			modal.iappend(html, function() {
				$(document).off("click", "#select-link-modal .btn-link").on("click", "#select-link-modal .btn-link", function() {
					var href = $.trim($(this).data("href"));
					if($.isFunction(callback)){
						callback(href);
						modal.modal('hide');
					}
				});
			});
		});
	};
	//{types: 'wmall,errander'}
	tiny.selectWxappLink = function(callback, options) {
		$('#select-wxapp-link-modal').remove();
		$.ajax(tiny.getUrl('common/wxapp/link'), {
			type: "get",
			dataType: "html",
			cache: false,
			data: options,
		}).done(function(html) {
			modal = $('<div class="modal fade" id="select-wxapp-link-modal"></div>');
			$(document.body).append(modal), modal.modal("show");
			modal.iappend(html, function() {
				$(document).off("click", "#select-wxapp-link-modal .btn-link").on("click", "#select-wxapp-link-modal .btn-link", function() {
					var href = $.trim($(this).data("href"));
					if($.isFunction(callback)){
						callback(href);
						modal.modal('hide');
					}
				});
				$(document).off("click", "#select-wxapp-link-modal .btn-webview").on("click", "#select-wxapp-link-modal .btn-webview", function() {
					var prefix = 'webview:https://';
					var webview = $.trim($(this).prev().val());
					if(!webview){
						return false;
					}
					var webview = prefix + webview;
					if($.isFunction(callback)){
						callback(webview);
						modal.modal('hide');
					}
				});
				$(document).off("click", "#select-wxapp-link-modal .btn-phone").on("click", "#select-wxapp-link-modal .btn-phone", function() {
					var prefix = 'tel:';
					var phone = $.trim($(this).prev().val());
					if(!phone){
						return false
					}
					var phone = prefix + phone;
					if($.isFunction(callback)){
						callback(phone);
						modal.modal('hide');
					}
				});
				$(document).off("click", "#select-wxapp-link-modal .btn-wxapp").on("click", "#select-wxapp-link-modal .btn-wxapp", function() {
					var prefix = 'miniProgram:';
					var appid = $.trim($(this).parent().parent().find("input[name='appid']").val());
					var url = $.trim($(this).parent().parent().find("input[name='url']").val());
					if(!appid){
						return false;
					}
					var wxapp = prefix + 'appId_' + appid;
					if(url) {
						url = 'path_' + url;
						wxapp = wxapp + ',' + url;
					}
					if($.isFunction(callback)){
						callback(wxapp);
						modal.modal('hide');
					}
				})
			});
		});
	};

	tiny.selectWxappIcon = function(callback) {
		$('#select-wxapp-icon-modal').remove();
		$.ajax(tiny.getUrl('common/wxapp/icon'), {
			type: "get",
			dataType: "html",
			cache: false
		}).done(function(html) {
			modal = $('<div class="modal fade" id="select-wxapp-icon-modal"></div>');
			$(document.body).append(modal), modal.modal("show");
			modal.iappend(html, function() {
				$(document).off("click", "#select-wxapp-icon-modal .tabbar-list .item").on("click", "#select-wxapp-icon-modal .tabbar-list .item", function() {
					var index = $.trim($(this).data("index"));
					var tabbar = {
						index: index,
						url: {
							normal: '../addons/we7_wmall/plugin/wxapp/static/img/tabbar/icon-' + index + '.png',
							active: '../addons/we7_wmall/plugin/wxapp/static/img/tabbar/icon-' + index + '-active.png'
						},
						tabbar: {
							normal: 'static/img/tabbar/icon-' + index + '.png',
							active: 'static/img/tabbar/icon-' + index + '-active.png'
						}
					};
					if($.isFunction(callback)){
						callback(tabbar);
						modal.modal('hide');
					}
				});
			});
		});
	};

	tiny.selectIcon = function(callback) {
		$('#select-icon-modal').remove();
		$.ajax(tiny.getUrl('common/icon'), {
			type: "get",
			dataType: "html",
			cache: false
		}).done(function(html) {
			modal = $('<div class="modal fade" id="select-icon-modal"></div>');
			$(document.body).append(modal), modal.modal("show");
			modal.iappend(html, function() {
				$(document).off("click", "#select-icon-modal a").on("click", "#select-icon-modal a", function() {
					var icon = $.trim($(this).data("icon"));
					if($.isFunction(callback)){
						callback(icon);
						modal.modal('hide');
					}
				});
			});
		});
	};

	tiny.selectCategory = function(callback, option) {
		$('#select-category-modal').remove();
		$.ajax(tiny.getUrl('common/store/category'), {
			type: "get",
			dataType: "html",
			cache: false
		}).done(function(html) {
			$Modal = $('<div class="modal fade" id="select-category-modal"></div>');
			$(document.body).append($Modal), $Modal.modal("show");
			$Modal.iappend(html, function(){
				if(option.mutil == 1) {
					$Modal.find('.modal-footer').removeClass('hide');
				} else {
					$Modal.find('.modal-footer').addClass('hide');
				}

				$Modal.find('#keyword').on('keydown', function(e){
					if(e.keyCode == 13) {
						$Modal.find('#search').trigger('click');
						e.preventDefault();
						return;
					}
				});
				$Modal.find('#search').on('click', function(){
					var key = $.trim($Modal.find('#keyword').val());
					$.post(tiny.getUrl('common/store/category'), {key: key}, function(data){
						var result = $.parseJSON(data);
						if(result.message.message && result.message.message.length > 0) {
							$Modal.find('.content').data('attachment', result.message.data);
							var gettpl = $('#select-category-data').html();
							irequire(['laytpl'], function(laytpl){
								laytpl(gettpl).render(result.message.message, function(html){
									$Modal.find('.content').html(html);
									$Modal.find('.content .btn-item').off();
									$Modal.find('.content .btn-item').on('click', function(){
										if(!option.mutil) {
											var id = $(this).data('id');
											var category = result.message.data[id];
											if($.isFunction(callback)){
												callback(category, option);
											}
											$Modal.modal('hide');
										} else {
											$(this).toggleClass('btn-primary');
											if($(this).hasClass('btn-primary')) {
												$(this).removeClass('btn-default');
											} else {
												$(this).removeClass('btn-primary').addClass('btn-default');
											}
											$Modal.find('.modal-footer .btn-submit').off();
											$Modal.find('.modal-footer .btn-submit').on('click', function(){
												var categorys = [];
												$Modal.find('.content .btn-primary').each(function(){
													categorys.push($Modal.find('.content').data('attachment')[$(this).data('id')]);
												});
												if($.isFunction(callback)){
													callback(categorys, option);
												}
												$Modal.modal('hide');
											});
										}
									});
								});
							});
						} else {
							$Modal.find('.content #info').html('没有符合条件的分类');
						}
					});
				});
			});
		});
	};

	tiny.selectfan = function(callback, option) {
		$('#select-fans-modal').remove();
		$(document.body).append($('#select-fans-containter').html());
		var $Modal = $('#select-fans-modal');
		irequire(['jquery.qrcode'], function(){
			$Modal.find('.js-qrcode').each(function() {
				var $this = $(this);
				var text = $(this).data("text") || $(this).data("href") || $(this).data("url");
				var width = $(this).data("width") || 150;
				$(this).show().html('').qrcode({
					render: 'canvas',
					width: width,
					height: width,
					text: text
				});
			});
		});
		$Modal.modal('show');
		$Modal.find('#keyword').on('keydown', function(e){
			if(e.keyCode == 13) {
				$Modal.find('#search').trigger('click');
				e.preventDefault();
				return;
			}
		});

		$Modal.find('#search').on('click', function(){
			var key = $.trim($Modal.find('#keyword').val());
			if(!key) {
				return false;
			}
			var params = {
				key: key,
				scene: option.scene
			};
			$.post(tiny.getUrl('common/fans/list'), params, function(data){
				var result = $.parseJSON(data);
				if(result.message.message && result.message.message.length > 0) {
					$Modal.find('.content').data('attachment', result.message.message);
					var gettpl = $('#select-fans-data').html();
					irequire(['laytpl'], function(laytpl){
						laytpl(gettpl).render(result.message.message, function(html){
							$Modal.find('.content').html(html);
							$Modal.find('.content .btn-primary').off();
							$Modal.find('.content .btn-primary').on('click', function(){
								var id = $(this).data('id');
								var fan = result.message.data[id];
								if($.isFunction(callback)){
									callback(fan);
								}
								$Modal.modal('hide');
							});
						});
					});
				} else {
					$html = '没有符合条件的粉丝<br>如果您正在设置提现账户,并且没有找到粉丝,如果您使用了小程序,请先进入外卖小程序首页,然后再搜索添加<br>如果未搜索到粉丝,你可以<a href="javascript:;" onclick="$(\'#follow-qrcode\').toggle()">"扫码绑定粉丝"</a>来进行粉丝绑定，绑定成功后，然后再搜索添加';
					$Modal.find('.content #info').html($html);
				}
			});
		});
	}

	tiny.selectStore = function(callback, option) {
		$('#select-store-modal').remove();
		$.ajax(tiny.getUrl('common/store/list'), {
			type: "get",
			dataType: "html",
			cache: false
		}).done(function(html) {
			$Modal = $('<div class="modal fade" id="select-store-modal"></div>');
			$(document.body).append($Modal), $Modal.modal("show");
			$Modal.iappend(html, function(){
				if(option.mutil == 1) {
					$Modal.find('.modal-footer').removeClass('hide');
				} else {
					$Modal.find('.modal-footer').addClass('hide');
				}
				$Modal.find('#keyword').on('keydown', function(e){
					if(e.keyCode == 13) {
						$Modal.find('#search').trigger('click');
						e.preventDefault();
						return;
					}
				});
				$Modal.find('#search').on('click', function(){
					var key = $.trim($Modal.find('#keyword').val());
					$.post(tiny.getUrl('common/store/list'), {key: key}, function(data){
						var result = $.parseJSON(data);
						if(result.message.message && result.message.message.length > 0) {
							$Modal.find('.content').data('attachment', result.message.data);
							var gettpl = $('#select-store-data').html();
							irequire(['laytpl'], function(laytpl){
								laytpl(gettpl).render(result.message.message, function(html){
									$Modal.find('.content').html(html);
									$Modal.find('.content .btn-item').off();
									$Modal.find('.content .btn-item').on('click', function(){
										if(!option.mutil) {
											var id = $(this).data('id');
											var store = result.message.data[id];
											callback = eval(callback);
											if($.isFunction(callback)){
												callback(store, option);
											}
											$Modal.modal('hide');
										} else {
											$(this).toggleClass('btn-primary');
											if($(this).hasClass('btn-primary')) {
												$(this).removeClass('btn-default');
											} else {
												$(this).removeClass('btn-primary').addClass('btn-default');
											}
											$Modal.find('.modal-footer .btn-submit').off();
											$Modal.find('.modal-footer .btn-submit').on('click', function(){
												var stores = [];
												$Modal.find('.content .btn-primary').each(function(){
													stores.push($Modal.find('.content').data('attachment')[$(this).data('id')]);
												});
												callback = eval(callback);
												if($.isFunction(callback)){
													callback(stores, option);
												}
												$Modal.modal('hide');
											});
										}
									});
								});
							});
						} else {
							$Modal.find('.content #info').html('没有符合条件的商户');
						}
					});
				});
			});
		});
	};

	tiny.selectDeliveryer = function(callback, option) {
		$('#select-deliveryer-modal').remove();
		$.ajax(tiny.getUrl('common/deliveryer/list'), {
			type: "get",
			dataType: "html",
			cache: false
		}).done(function(html) {
			$Modal = $('<div class="modal fade" id="select-deliveryer-modal"></div>');
			$(document.body).append($Modal), $Modal.modal("show");
			$Modal.iappend(html, function(){
				if(option.mutil == 1){
					$Modal.find('.modal-footer').removeClass('hide');
				} else {
					$Modal.find('.modal-footer').addClass('hide');
				}
				$Modal.find('#keyword').on('keydown', function(e){
					if(e.keyCode == 13) {
						$Modal.find('#search').trigger('click');
						e.preventDefault();
						return;
					}
				});
				$Modal.find('#search').on('click', function(){
					var params = {
						key: $.trim($Modal.find('#keyword').val()),
						type: option.type
					};

					$.post(tiny.getUrl('common/deliveryer/list'), params, function(data){
						var result = $.parseJSON(data);
						if(result.message.message && result.message.message.length > 0){
							$Modal.find('.content').data('attachment', result.message.data);
							var gettpl = $('#select-deliveryer-data').html();
							irequire(['laytpl'], function(laytpl){
								laytpl(gettpl).render(result.message.message, function(html){
									$Modal.find('.content').html(html);
									$Modal.find('.content .btn-item').off();
									$Modal.find('.content .btn-item').on('click', function(){
										if(!option.mutil) {
											var id = $(this).data('id');
											var deliveryer = result.message.data[id];
											callback = eval(callback);
											if($.isFunction(callback)){
												callback(deliveryer, option);
											}
											console.log(deliveryer);
											$Modal.modal('hide');
										} else {
											$(this).toggleClass('btn-primary');
											if($(this).hasClass('btn-primary')) {
												$(this).removeClass('btn-default');
											} else {
												$(this).removeClass('btn-primary').addClass('btn-default');
											}
											$Modal.find('.modal-footer .btn-submit').off();
											$Modal.find('.modal-footer .btn-submit').on('click', function(){
												var deliveryer = [];
												$Modal.find('.content .btn-primary').each(function(){
													deliveryer.push($Modal.find('.content').data('attachment')[$(this).data('id')]);
												});
												callback = eval(callback);
												if($.isFunction(callback)){
													callback(deliveryer, option);
												}
												$Modal.modal('hide');
											});
										}
									});
								});
							})
						}  else {
							$Modal.find('.content #info').html('没有符合条件的配送员');
						}
					});
				});
			});
		});
	};

	tiny.selectgoods = function(callback, option) {
		$('#select-goods-modal').remove();
		$(document.body).append($('#select-goods-containter').html());
		var $Modal = $('#select-goods-modal');
		if(option.mutil == 1) {
			$Modal.find('.modal-footer').removeClass('hide');
		} else {
			$Modal.find('.modal-footer').addClass('hide');
		}
		$Modal.modal('show');
		$Modal.find('#keyword').on('keydown', function(e){
			if(e.keyCode == 13) {
				$Modal.find('#search').trigger('click');
				e.preventDefault();
				return;
			}
		});

		$Modal.find('#search').on('click', function(){
			var key = $.trim($Modal.find('#keyword').val());
			if(!key) {
				return false;
			}
			option.key = key;
			$.post(tiny.getUrl('common/goods/list'), option, function(data){
				var result = $.parseJSON(data);
				if(result.message.message && result.message.message.length > 0) {
					$Modal.find('.content').data('attachment', result.message.data);
					var gettpl = $('#select-goods-data').html();
					irequire(['laytpl'], function(laytpl){
						laytpl(gettpl).render(result.message.message, function(html){
							$Modal.find('.content').html(html);
							$Modal.find('.content .btn-item').off();
							$Modal.find('.content .btn-item').on('click', function(){
								if(!option.mutil) {
									var id = $(this).data('id');
									var goods = result.message.data[id];
									callback = eval(callback);
									if($.isFunction(callback)){
										callback(goods, option);
									}
									$Modal.modal('hide');
								} else {
									$(this).toggleClass('btn-primary');
									if($(this).hasClass('btn-primary')) {
										$(this).removeClass('btn-default');
									} else {
										$(this).removeClass('btn-primary').addClass('btn-default');
									}
									$Modal.find('.modal-footer .btn-submit').off();
									$Modal.find('.modal-footer .btn-submit').on('click', function(){
										var goods = [];
										$Modal.find('.content .btn-primary').each(function(){
											goods.push($Modal.find('.content').data('attachment')[$(this).data('id')]);
										});
										callback = eval(callback);
										if($.isFunction(callback)){
											callback(goods, option);
										}
										$Modal.modal('hide');
									});
								}
							});
						});
					});
				} else {
					$Modal.find('.content #info').html('没有符合条件的商品');
				}
			});
		});
	};

	tiny.selectaccount = function(callback) {
		irequire(['laytpl'], function(laytpl){
			$('#select-account-modal').remove();
			$(document.body).append($('#select-account-containter').html());
			var $Modal = $('#select-account-modal');
			$Modal.modal('show');
			$Modal.find('#keyword').on('keydown', function(e){
				if(e.keyCode == 13) {
					$Modal.find('#search').trigger('click');
					e.preventDefault();
					return;
				}
			});

			$Modal.find('#search').on('click', function(){
				var key = $.trim($Modal.find('#keyword').val());
				if(!key) {
					return false;
				}
				$.post(tiny.getUrl('common/account/list'), {key: key}, function(data){
					var result = $.parseJSON(data);
					if(result.message.message && result.message.message.length > 0) {
						$Modal.find('.content').data('attachment', result.message.message);
						var gettpl = $('#select-account-data').html();
						laytpl(gettpl).render(result.message.message, function(html){
							$Modal.find('.content').html(html);
							$Modal.find('.content .btn-primary').off();
							$Modal.find('.content .btn-primary').on('click', function(){
								var uniacid = $(this).data('uniacid');
								var account = result.message.data[uniacid];
								if($.isFunction(callback)){
									callback(account);
								}
								$Modal.modal('hide');
							});
						});
					} else {
						$Modal.find('.content #info').html('没有符合条件的粉丝');
					}
				});
			});
		});
	};

	tiny.confirm = function(obj, option, callback_confirm, callback_cancel) {
		if(typeof option == 'string'){
			option = {tips : option};
		}
		option = $.extend({tips:'确认删除?', placement:'left'}, option);
		obj.popover({
			'html': true,
			'placement': option.placement,
			'trigger': 'manual',
			'title': '',
			'content': '<span> '+ option.tips +' </span> <a class="btn btn-primary confirm">确定</a> <a class="btn btn-default cancel">取消</a>'
		});
		obj.popover('show');
		var confirm = obj.next().find('a.confirm');
		var cancel = obj.next().find('a.cancel');
		cancel.off('click').on('click', function(){
			obj.popover('hide');
			obj.next().remove();
			if(typeof callback_cancel == 'function') {
				callback_cancel();
			}
		});
		confirm.off('click').on('click', function(){
			obj.popover('hide');
			obj.next().remove();
			if(typeof callback_confirm == 'function') {
				callback_confirm();
			}
		});
		return false;
	};

	tiny.map = function(val, callback){
		$.getScript('//webapi.amap.com/maps?v=1.4.1&key=550a3bf0cb6d96c3b43d330fb7d86950&plugin=AMap.Geocoder,AMap.Scale,AMap.OverView,AMap.ToolBar', function(){
			if(!val) {
				val = {};
			}
			if(!val.lng) {
				val.lng = 116.397428;
			}
			if(!val.lat) {
				val.lat = 39.90923;
			}
			var geo = new AMap.Geocoder();

			var modalobj = $('#map-dialog');
			if(modalobj.length == 0) {
				var content =
					'<div class="form-group">' +
						'<div class="input-group">' +
						'<input type="text" class="form-control" placeholder="请输入地址来直接查找相关位置">' +
						'<div class="input-group-btn">' +
						'<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>' +
						'</div>' +
						'</div>' +
						'</div>' +
						'<div id="map-container" style="height:400px;"></div>';
				var footer =
					'<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>' +
						'<button type="button" class="btn btn-primary">确认</button>';
				modalobj = util.dialog('请选择地点', content, footer, {containerName : 'map-dialog'});
				modalobj.find('.modal-dialog').css('width', '80%');
				modalobj.modal({'keyboard': false});

				map = tiny.map.instance = new AMap.Map('map-container');
				map.setZoomAndCenter(12, [val.lng, val.lat]);

				map.addControl(new AMap.Scale());
				map.addControl(new AMap.ToolBar());

				marker = tiny.map.marker = new AMap.Marker({
					position: [val.lng, val.lat],
					draggable: true
				});
				map.on('complete', function() {
					marker.setLabel({
						offset: new AMap.Pixel(-80, -25),
						content: "请您移动此标记，选择您的坐标！"
					});
					marker.setMap(map);

					AMap.event.addListener(marker, "dragend", function(e){
						var point = marker.getPosition();
						geo.getAddress([point.lng, point.lat], function(status, result) {
							if (status === 'complete' && result.info === 'OK') {
								modalobj.find('.input-group :text').val(result.regeocode.formattedAddress);
							}
						});
					});
				});
				function searchAddress(address) {
					geo.getLocation(address, function(status, result) {
						if (status === 'complete' && result.info === 'OK') {
							var geocode = result.geocodes[0];
							if(geocode.location) {
								map.panTo([geocode.location.lng, geocode.location.lat]);
								marker.setPosition([geocode.location.lng, geocode.location.lat]);
								marker.setAnimation('AMAP_ANIMATION_BOUNCE');
								setTimeout(function(){marker.setAnimation(null)}, 3600);
							}
						}
					});
				}
				modalobj.find('.input-group :text').keydown(function(e){
					if(e.keyCode == 13) {
						var kw = $(this).val();
						searchAddress(kw);
					}
				});
				modalobj.find('.input-group button').click(function(){
					var kw = $(this).parent().prev().val();
					searchAddress(kw);
				});
			}
			modalobj.off('shown.bs.modal');
			modalobj.on('shown.bs.modal', function(){
				marker.setPosition([val.lng, val.lat]);
				map.panTo([val.lng, val.lat]);
			});

			modalobj.find('button.btn-primary').off('click');
			modalobj.find('button.btn-primary').on('click', function(){
				if($.isFunction(callback)) {
					var point = marker.getPosition();
					geo.getAddress([point.lng, point.lat], function(status, result) {
						if (status === 'complete' && result.info === 'OK') {
							var val = {lng: point.lng, lat: point.lat, label: result.regeocode.formattedAddress};
							callback(val);
						}
					});
				}
				modalobj.modal('hide');
			});
			modalobj.modal('show');
		});
	};

	tiny.prompt = function(obj, option, callback_confirm, callback_cancel) {
		if(typeof option == 'string'){
			option = {tips : option};
		}
		option = $.extend({title: '', placement:'top'}, option);
		obj.popover({
			'html':true,
			'placement': option.placement,
			'trigger': 'manual',
			'title': option.title,
			'content':'<input type="text" class="form-control prompt-input-text" value=""> <a class="btn btn-primary confirm" style="margin-right:5px">确定</a> <a class="btn btn-default cancel" style="margin-right:5px">取消</a>'
		});
		obj.popover('show');
		var confirm = obj.next().find('a.confirm');
		var cancel = obj.next().find('a.cancel');
		var input = obj.next().find('.prompt-input-text');
		input.focus();
		$(input).keydown(function(event){
			if(event.keyCode == 13){
				$(confirm).trigger('click');
				return false;
			}
		});
		cancel.off('click').on('click', function(){
			var value = obj.next().find('.prompt-input-text').val();
			obj.popover('hide');
			obj.next().remove();
			if(typeof callback_cancel == 'function') {
				callback_cancel(value);
			}
		});
		confirm.off('click').on('click', function(){
			var value = obj.next().find('.prompt-input-text').val();
			obj.popover('hide');
			obj.next().remove();
			if(typeof callback_confirm == 'function') {
				callback_confirm(value);
			}
		});
		return false;
	};
	return tiny;
});







