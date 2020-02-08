define(['jquery.ui', 'clockpicker'], function(ui, $) {
	var superRedpacket = {};
	superRedpacket.init = function (params) {
		window.tmodtpl = params.tmodtpl;
		superRedpacket.attachurl = params.attachurl;
		superRedpacket.data = params.data;
		if(!superRedpacket.data) {
			superRedpacket.data = {
				name: '商家代金券',
				params: {
					image: '../addons/we7_wmall/plugin/superRedpacket/static/img/header.png',
				},
				style: {
					backgroundColor: '#b80404',
					storeTitleColor: '#333333',
					usefulDayColor: '#8F8F8F',
					moneyColor: '#ff2d4b',
					useLimitColor: '#8F8F8F',
					toUseColor: '#333333',
					toUseBackgroundColor: '#ffd161',
					buttonColor: '#333333',
					buttonBackgroundColor: '#ffd161'
				},
			}
		}

		tmodtpl.helper("tomedia", function(src){
			if (typeof src != 'string') {
				return '';
			}
			if(src.indexOf('http://') == 0 || src.indexOf('https://') == 0 || src.indexOf('../addons') == 0) {
				return src;
			}
			if(src.indexOf('images/') == 0) {
				return superRedpacket.attachurl + src;
			}
		});
		superRedpacket.tplSuperRedpacket();
		superRedpacket.tplEditor();
		superRedpacket.initGotop();
		superRedpacket.save();
	};

	superRedpacket.tplSuperRedpacket = function() {
		var html = tmodtpl("tpl-show-superRedpacket", superRedpacket.data);
		$("#app-preview").html(html);
	};

	superRedpacket.tplEditor = function() {
		var html = tmodtpl("tpl-edit-superRedpacket", superRedpacket.data);
		$("#app-editor .inner").html(html);

		$("#app-editor").find(".diy-bind").bind('input propertychange change', function() {
			var _this = $(this);
			var bind = _this.data("bind");
			var bindchild = _this.data('bind-child');
			var bindparent = _this.data('bind-parent');
			var bindcategory = _this.data('bind-category');
			var bindtype = _this.data('bind-type');
			var tplEditor = _this.data('bind-init');
			var value = '';
			var tag = this.tagName;
			if (tag == 'INPUT') {
				var placeholder = _this.data('placeholder');
				value = _this.val();
				value = value == '' ? placeholder : value;
			} else if (tag == 'SELECT') {
				value = _this.find('option:selected').val();
			} else if (tag == 'TEXTAREA') {
				value = _this.val();
			}
			value = $.trim(value);
			if(bindchild) {
				if(bindparent) {
					if(bindcategory) {
						if(bindtype) {
							superRedpacket.data[bindchild][bindparent][bind][bindcategory][bindtype] = value;
						} else {
							superRedpacket.data[bindchild][bindparent][bind][bindcategory] = value;
						}
					} else {
						superRedpacket.data[bindchild][bindparent][bind] = value;
					}
				} else {
					superRedpacket.data[bindchild][bind] = value;
				}
			} else {
				superRedpacket.data[bind] = value;
			}
			superRedpacket.tplSuperRedpacket();
			if(tplEditor) {
				superRedpacket.tplEditor();
			}
		});
	};

	superRedpacket.length = function(json) {
		if(typeof(json) === 'undefined') {
			return 0;
		}
		var len = 0;
		for(var i in json) {
			len++;
		}
		return len;
	};

	superRedpacket.getId = function(S, N) {
		var date = +new Date();
		var id = S + (date + N);
		return id;
	};

	superRedpacket.initGotop = function() {
		$(window).bind('scroll resize', function() {
			var scrolltop = $(window).scrollTop();
			if (scrolltop > 100) {
				$("#gotop").show()
			} else {
				$("#gotop").hide()
			}
			$("#gotop").unbind('click').click(function() {
				$('body').animate({scrollTop: "0px"}, 1000)
			})
		})
	};

	superRedpacket.save = function() {
		$(".btn-save").unbind('click').click(function() {
			var $this = $(this);
			Notify.confirm('确定保存吗?', function() {
				var status = $this.data('status');
				if(status) {
					Notify.info("正在保存，请稍候。。。");
					return;
				}
				$(".btn-save").data('status', 1).text("保存中...");
				var posturl = "./index.php?c=site&a=entry&ctrl=superRedpacket&ac=coupon&op=post&do=web&m=we7_wmall";
				$.post(posturl, {data: superRedpacket.data}, function(result) {
					$(".btn-save").text("已保存").data("status", 0);
					if(result.message.errno != 0) {
						Notify.error(result.message.message);
						return;
					}
					Notify.success("保存成功！", result.message.url);
				}, 'json');
			});
			return false;
		});
	};
	return superRedpacket;
});