define(['jquery.ui'], function (ui) {
	var diyDanmu = {
		default: {
			params: {
				'status': '0',
				'styleType': '1',
				'dataType': '1',
				'starttime': '5',
				'endtime': '60'
			},
			css: {
				'background': '#ff2d4b',
				'color': '#ffffff',
				'opacity': '0.7'
			},
			data: {
				M0123456789101: {
					'avatar': '../addons/we7_wmall/plugin/diypage/static/img/1.png',
					'nickname': '粉丝昵称',
					'time': '5'
				}
			}
		}
	};

	diyDanmu.init = function (params) {
		window.tmodtpl = params.tmodtpl;
		diyDanmu.attachurl = params.attachurl;
		diyDanmu.danmu = params.danmu;

		if(!diyDanmu.danmu) {
			diyDanmu.danmu = diyDanmu.default;
		}
		tmodtpl.helper("tomedia", function(src) {
			if(src.indexOf('images/') == 0) {
				return diyDanmu.attachurl + src;
			}
			if(typeof src != 'string') {
				return '';
			}
			if(src.indexOf('http://') == 0 || src.indexOf('https://') == 0 || src.indexOf('../addons/ewei_shopv2/') == 0) {
				return src;
			} else if (src.indexOf('images/') == 0 || src.indexOf('audios/') == 0) {
				return diy.attachurl + src;
			}
		});

		diyDanmu.tplDanmu();
		diyDanmu.tplEditor();
		diyDanmu.initGotop();
		diyDanmu.save();
	};

	diyDanmu.tplDanmu = function () {
		var html = tmodtpl("tpl-show-danmu", diyDanmu.danmu);
		$("#app-preview").html(html).show();
	};

	diyDanmu.initSortable = function () {
		$(".app-editor .form-items .inner").sortable({
			opacity: 0.8,
			placeholder: "highlight",
			items: '.item',
			revert: 100,
			scroll: false,
			cancel: '.goods-selector,input,select,.btn,.btn-del,.three',
			start: function (event, ui) {
				var height = ui.item.height();
				$(".highlight").css({"height": height + 22 + "px"});
				$(".highlight").html('<div><i class="fa fa-plus"></i> 放置此处</div>');
				$(".highlight div").css({"line-height": height + 16 + "px", "font-size" : "16px", "color" : "#999", "text-align" : "center", "border" : "2px dashed #eee"})
			},
			update: function (event, ui) {
				var  childType = ui.item.closest(".form-items").data('type');
				diyDanmu.sortChildItems(childType)
			}
		})
	};
	diyDanmu.sortChildItems = function () {
		var newArr = [];
		$("#form-items .item").each(function (i) {
			var index = $(this).data('index');
			var item = diyDanmu.danmu.data[index];
			if(item){
				newArr[i] = item;
			}
		});
		diyDanmu.danmu.data = newArr;
		diyDanmu.tplDanmu();
	};

	diyDanmu.tplEditor = function () {
		var html = tmodtpl("tpl-editor-danmu", diyDanmu.danmu);
		$("#app-editor .inner").html(html);
		$("#app-editor .slider").each(function () {
			var decimal = $(this).data('decimal');
			var multiply = $(this).data('multiply');
			var defaultValue = $(this).data("value");
			if (decimal) {
				defaultValue = defaultValue * decimal
			}
			$(this).slider({
				slide: function (event, ui) {
						var sliderValue = ui.value;
						if (decimal) {
							sliderValue = sliderValue / decimal
						}
						$(this).siblings(".input").val(sliderValue).trigger("propertychange");
						$(this).siblings(".count").find("span").text(sliderValue)
				}, value: defaultValue, min: $(this).data("min"), max: $(this).data("max")
			})
		});
		$("#app-editor").find(".diy-bind").bind('input propertychange change', function () {
			var _this = $(this);
			var bind = _this.data("bind");
			var bindchild = _this.data('bind-child');
			var bindparent = _this.data('bind-parent');
			var bindthree = _this.data('bind-three');
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
			if (bindchild) {
				if (bindparent || bindparent==0) {
					if (bindthree) {
						diyDanmu.danmu[bindchild][bindparent].child[bindthree][bind] = value;
					} else {
						diyDanmu.danmu[bindchild][bindparent][bind] = value;
					}
				} else {
					diyDanmu.danmu[bindchild][bind] = value;
				}
			} else {
				diyDanmu.danmu[bind] = value;
			}
			diyDanmu.tplDanmu();
			if (tplEditor) {
				diyDanmu.tplEditor();
			}
		});

		$("#app-editor #addItem").unbind('click').click(function () {
			var itemid = diyDanmu.getId('M', 0);
			var max = $(this).closest('.form-items').data('max');
			var num = diyDanmu.length(diyDanmu.danmu.data);
			if (num >= max) {
				Notify.info("最大添加 " + max + " 个！");
				return;
			}
			diyDanmu.danmu.data[itemid] = {
				'avatar': '../addons/we7_wmall/plugin/diypage/static/img/1.png',
				'nickname': '粉丝昵称',
				'time': '5'
			};
			diyDanmu.tplDanmu();
			diyDanmu.tplEditor();
		});

		$("#app-editor .form-items .item .btn-del").unbind('click').click(function () {
			var itemid = $(this).closest(".item").data('id');
			var min = $(this).closest(".form-items").data("min");
			if (min) {
				if(!diyDanmu.danmu.data){
					diyDanmu.danmu.data = [];
				}
				var length = diyDanmu.danmu.data.length;
				if (length <= min) {
					Notify.msgbox.err("至少保留 " + min + " 个");
					return
				}
			}
			Notify.confirm("确定删除吗", function() {
				delete diyDanmu.danmu.data[itemid];
				diyDanmu.tplDanmu();
				diyDanmu.tplEditor();
			});
		});

		$("#diy-editor").show();
		diyDanmu.initSortable();
	};

	diyDanmu.initGotop = function () {
		$(window).bind('scroll resize', function () {
			var scrolltop = $(window).scrollTop();
			if (scrolltop > 100) {
				$("#gotop").show()
			} else {
				$("#gotop").hide()
			}
			$("#gotop").unbind('click').click(function () {
				$('body').animate({scrollTop: "0px"}, 1000)
			})
		})
	};

	diyDanmu.length = function(json) {
		if(typeof(json) === 'undefined') {
			return 0;
		}
		var len = 0;
		for(var i in json) {
			len++;
		}
		return len;
	};

	diyDanmu.getId = function(S, N) {
		var date = +new Date();
		var id = S + (date + N);
		return id;
	};

	diyDanmu.save = function () {
		$(".btn-save").unbind('click').click(function() {
			var status = $(this).data('status');
			if(status) {
				Notify.info("正在保存，请稍候。。。");
				return;
			}
			if(!diyDanmu.danmu) {
				Notify.info("订单数据不能为空！");
				return;
			}
			$(".btn-save").data('status', 1).text("保存中...");
			var posturl = "./index.php?c=site&a=entry&ctrl=diypage&ac=danmu&do=web&m=we7_wmall";
			$.post(posturl, {danmu: diyDanmu.danmu}, function(result) {
				$(".btn-save").text("保存菜单").data("status", 0);
				if(result.message.errno != 0) {
					Notify.error(result.message.result);
					return;
				}
				Notify.success("保存成功！", result.message.url);
			}, 'json');
		});
	};
	return diyDanmu
});