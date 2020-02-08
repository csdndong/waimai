define(['laytpl'], function(laytpl) {
	var defaults = {baseUrl: '', siteUrl: '', attachUrl: '', staticUrl: '../addons/we7_wmall/static/', uniacid: ''};
	var tiny = {options: {}};

	tiny.init = function (options) {
		this.options = $.extend({}, defaults, options || {})
	};

	tiny.querystring = function(name){
		var result = location.search.match(new RegExp("[\?\&]" + name+ "=([^\&]+)","i"));
		if (result == null || result.length < 1){
			return "";
		}
		return result[1];
	}

	tiny.toQueryPair = function (key, value) {
		if (typeof value == 'undefined') {
			return key
		}
		return key + '=' + encodeURIComponent(value === null ? '' : String(value))
	};

	tiny.toQueryString = function (obj) {
		var ret = [];
		for (var key in obj) {
			key = encodeURIComponent(key);
			var values = obj[key];
			if (values && values.constructor == Array) {
				var queryValues = [];
				for (var i = 0, len = values.length, value; i < len; i++) {
					value = values[i];
					queryValues.push(this.toQueryPair(key, value))
				}
				ret = ret.concat(queryValues)
			} else {
				ret.push(this.toQueryPair(key, values))
			}
		}
		return ret.join('&')
	};

	tiny.getUrl = function(routes, params, full) {
		routes = routes.split('/');
		var pre = 'ctrl=' + routes[0] + '&ac=' + routes[1] + '&op=' + routes[2];
		if(routes[3]) {
			pre += '&ta=' + routes[3];
		}
		var url = this.options.baseUrl.replace('ctrl=ROUTES', pre);
		if(params) {
			if (typeof(params) == 'object') {
				url += "&" + this.toQueryString(params);
			} else if (typeof(params) == 'string') {
				url += "&" + params;
			}
		}
		return full ? this.options.siteUrl + 'app/' + url : url;
	};

	tiny.getLocation = function(callbackLocation, callbackAddress, callbackError) {
		if(tiny.isWeixin() && location.href.indexOf('https://') != -1) {
			wx.ready(function(){
				wx.getLocation({
					type: 'gcj02',
					success: function (res) {
						var location = {
							lat: res.latitude,
							lng: res.longitude,
							location_x: res.latitude,
							location_y: res.longitude
						};
						if($.isFunction(callbackLocation)) {
							callbackLocation(location);
						}
						if($.isFunction(callbackAddress)) {
							$.post(tiny.getUrl('system/common/map/regeo'), {latitude: res.latitude, longitude: res.longitude}, function(result){
								var result = $.parseJSON(result);
								result = result.message;
								if(!result.errno) {
									callbackAddress(result.message);
								}
							});
						}
					},
					fail: function(res) {
						//alert('获取位置失败:' + res.errMsg)
						var map, geolocation;
						map = new AMap.Map('allmap');
						map.plugin('AMap.Geolocation', function() {
							geolocation = new AMap.Geolocation({
								enableHighAccuracy: true //是否使用高精度定位，默认:true
							});
							geolocation.getCurrentPosition();
							AMap.event.addListener(geolocation, 'complete', function(res){
								var point = res.position;
								var location = {
									lat: point.lat,
									lng: point.lng
								};
								callbackLocation(location);
								var lnglatXY = [point.lng, point.lat]; //已知点坐标
								map.plugin('AMap.Geocoder', function() {
									var geocoder = new AMap.Geocoder();
									geocoder.getAddress(lnglatXY, function(status, result) {
										if (status === 'complete' && result.info === 'OK') {
											var obj = result.regeocode.addressComponent;
											var position = result.regeocode.formattedAddress;
											position = position.replace(obj.province, '');
											position = position.replace(obj.district, '');
											position = position.replace(obj.city, '');
											if($.isFunction(callbackAddress)) {
												callbackAddress({
													address: position,
													lat: point.lat,
													lng: point.lng,
													location_x: point.lat,
													location_y: point.lng
												});
											}
										}
									});
								});
							});
							AMap.event.addListener(geolocation, 'error', function(res){
								if($.isFunction(callbackError)) {
									callbackError();
								}
							});
						});
					}
				});
			});
		} else {
			var map, geolocation;
			map = new AMap.Map('allmap');
			map.plugin('AMap.Geolocation', function() {
				geolocation = new AMap.Geolocation({
					enableHighAccuracy: true //是否使用高精度定位，默认:true
				});
				geolocation.getCurrentPosition();
				AMap.event.addListener(geolocation, 'complete', function(res){
					var point = res.position;
					var location = {
						lat: point.lat,
						lng: point.lng
					};
					callbackLocation(location);
					var lnglatXY = [point.lng, point.lat]; //已知点坐标
					map.plugin('AMap.Geocoder', function() {
						var geocoder = new AMap.Geocoder();
						geocoder.getAddress(lnglatXY, function(status, result) {
							if (status === 'complete' && result.info === 'OK') {
								var obj = result.regeocode.addressComponent;
								var position = result.regeocode.formattedAddress;
								position = position.replace(obj.province, '');
								position = position.replace(obj.district, '');
								position = position.replace(obj.city, '');
								if($.isFunction(callbackAddress)) {
									callbackAddress({
										address: position,
										lat: point.lat,
										lng: point.lng,
										location_x: point.lat,
										location_y: point.lng
									});
								}
							}
						});
					});
				});
				AMap.event.addListener(geolocation, 'error', function(res){
					if($.isFunction(callbackError)) {
						callbackError();
					}
				});
			});
		}
	};

	tiny.image = function(obj, callback, options) {
		var defaultOptions = {
			fileNum: 1
		};
		var options = $.extend({}, defaultOptions, options);
		var $button = $(obj);
		if(tiny.isWeixin()) {
			wx.ready(function(){
				wx.chooseImage({
					count: options.fileNum,
					sizeType: ['compressed'],
					sourceType: ['album', 'camera'],
					success: function (res) {
						var localIds = res.localIds;
						if(localIds.length > 0) {
							for(var i = 0; i < localIds.length; i++) {
								$.showIndicator();
								wx.uploadImage({
									localId: localIds[i],
									isShowProgressTips: 0,
									success: function (res) {
										var serverId = res.serverId;
										var i = tiny.querystring('i');
										$.post(tiny.getUrl('system/common/file/image', {channel: 'weixin'}), {media_id: serverId}, function(data){
											$.hideIndicator();
											var result = $.parseJSON(data);
											if(result.message.errno == 0) {
												if($.isFunction(callback)) {
													callback($button, result.message);
												}
											} else {
												alert('上传文件失败, 具体原因:' + result.message.message);
											}
										});
									},
									fail: function() {}
								});
							}
						}
					}
				});
			});
		} else {
			var $file = $button.find('input[type="file"]');
			$file.off('change');
			$file.change(function(e){
				var fileElm = e.target;
				if(fileElm.files && fileElm.files.length > 0) {
					for(var i = 0; i < fileElm.files.length; i++) {
						$.showIndicator();
						var pars = new FormData();
						pars.append('file', fileElm.files[i]);
						$.ajax({
							url: './index.php?c=utility&a=file&do=upload&type=image&thumb=0&i=' + tiny.options.uniacid,
							type: "POST",
							processData: false,
							contentType: false,
							dataType: 'json',
							data: pars,
							success:function(result){
								$.hideIndicator();
								if(result.url) {
									if($.isFunction(callback)) {
										callback($button, result);
									}
								} else {
									if(result.message) {
										alert('上传文件失败, 具体原因:' + result.message);
									} else {
										alert('上传文件失败, 具体原因:' + result.error.message);
									}
								}
							},
							error:function(result){
								$.hideIndicator();
								alert('上传文件失败');
							}
						});
					}
				}
			});
		}
	};

	tiny.cookie = {
		'prefix' : we7_wmall.prefix,
		// 保存 Cookie
		'set' : function(name, value, seconds) {
			expires = new Date();
			expires.setTime(expires.getTime() + (1000 * seconds));
			document.cookie = this.name(name) + "=" + escape(value) + "; expires=" + expires.toGMTString() + "; path=/";
		},
		// 获取 Cookie
		'get' : function(name) {
			cookie_name = this.name(name) + "=";
			cookie_length = document.cookie.length;
			cookie_begin = 0;
			while (cookie_begin < cookie_length)
			{
				value_begin = cookie_begin + cookie_name.length;
				if (document.cookie.substring(cookie_begin, value_begin) == cookie_name)
				{
					var value_end = document.cookie.indexOf ( ";", value_begin);
					if (value_end == -1)
					{
						value_end = cookie_length;
					}
					return unescape(document.cookie.substring(value_begin, value_end));
				}
				cookie_begin = document.cookie.indexOf ( " ", cookie_begin) + 1;
				if (cookie_begin == 0)
				{
					break;
				}
			}
			return null;
		},
		// 清除 Cookie
		'del' : function(name) {
			var expireNow = new Date();
			document.cookie = this.name(name) + "=" + "; expires=Thu, 01-Jan-70 00:00:01 GMT" + "; path=/";
		},
		'name' : function(name) {
			return this.prefix + name;
		}
	};

	tiny.countDown = function(time, day_elem, hour_elem, minute_elem, second_elem, split){
		if($.isNumber(time)) {
			var end_time = time;
		} else {
			var end_time = new Date(time).getTime() / 1000;
		}
		var sys_second = parseInt(end_time-new Date().getTime()/1000);
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

	tiny.ish5app = function() {
		var userAgent = navigator.userAgent;
		if(userAgent.indexOf('CK 2.0') > -1){
			return true;
		}
		return false;
	};

	tiny.isWeixin = function() {
		var ua = navigator.userAgent.toLowerCase();
		var isWX = ua.match(/MicroMessenger/i) == "micromessenger";
		return isWX;
	}

	tiny.isQianfan = function() {
		var userAgent = navigator.userAgent;
		if(userAgent.indexOf('QianFan') > -1){
			return true;
		}
		return false;
	};

	tiny.isMajia = function() {
		var userAgent = navigator.userAgent;
		if(userAgent.indexOf('MAGAPPX') > -1){
			return true;
		}
		return false;
	};

	tiny.isCloud = function() {
		var userAgent = navigator.userAgent;
		if(userAgent.indexOf('APICloud') > -1){
			return true;
		}
		return false;
	};

	tiny.distance = function(origins, destination, type, callback) {
		var type = type ? type : 1;
		var callback = callback ? callback : '';
		var params = {
			key: '37bb6a3b1656ba7d7dc8946e7e26f39b',
			type: type,
			origins: origins[0] + ',' + origins[1],
			destination: destination[0] + ',' + destination[1],
			output: 'json'
		};
		var url = 'http://restapi.amap.com/v3/distance?' + tiny.toQueryString(params);
		$.getJSON(url, function(result){
			if(result.info != 'OK') {
				var data = {
					errno: result.infocode,
					message: result.info
				};
			} else {
				var data = {
					errno: 0,
					message: result.results[0],
					distance: (result.results[0]['distance'] / 1000).toFixed(3)
				};
			}
			if($.isFunction(callback)) {
				callback(data);
			}
		});
	};

	tiny.tomedia = function(src) {
		if(typeof src != 'string') {
			return '';
		}
		if(src.indexOf('images/') == 0 || src.indexOf('/images/') == 0) {
			return this.options.attachUrl + src;
		}
		if(src.indexOf('http://') == 0 || src.indexOf('https://') == 0 || src.indexOf('../addons/we7_wmall/') == 0) {
			return src;
		}
	};

	tiny.localStorage = {
		push: function(name, value) {
			var data = tiny.localStorage.get(name);
			if(!data || !$.isArray(data)) {
				data = [];
			}
			data.push(value);
			localStorage.setItem(name, JSON.stringify(data));
		},
		remove: function(name) {
			localStorage.removeItem(name);
			return true;
		},
		get: function(name) {
			var data = localStorage.getItem(name);
			data = JSON.parse(data);
			return data;
		},
		set: function(name, value) {
			var data = tiny.localStorage.get(name);
			if(!data) {
				data = value;
			}
			if(typeof data == 'object') {
				data = JSON.stringify(data);
			}
			localStorage.setItem(name, data);
		}
	};
	window.tiny = tiny;
	return tiny;
});

