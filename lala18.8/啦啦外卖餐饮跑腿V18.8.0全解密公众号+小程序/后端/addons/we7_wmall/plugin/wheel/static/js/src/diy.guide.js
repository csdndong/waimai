define(['jquery.ui'], function(ui) {
	var diyGuide = {itemid: ''};
	diyGuide.init = function (params) {
		window.tmodtpl = params.tmodtpl;
		diyGuide.attachurl = params.attachurl;
		diyGuide.guide = params.guide;
		if(!diyGuide.guide) {
			diyGuide.guide = {
				name: '启动图',
				params: {
					'status': 1,
					'show_setting': 'everytime', //everytime每次 interval间隔
					'interval_time': 60
				},
				css: {
					'backgroundColor': '#000000',
				},
				data: {
					M0123456789101: {
						pagePath: "pages/home/index",
						imgUrl: "https://1.xinzuowl.com/attachment/images/1/2017/03/qra8AS8rF5m8MUom19Bo521maA8oF8.jpg",
					},
					M0123456789102: {
						pagePath: "pages/home/index",
						imgUrl: "https://1.xinzuowl.com/attachment/images/1/2016/11/yammcm8C22RvxXR2E2RrRX262rHkZP.jpg",
					},
					M0123456789103: {
						pagePath: "pages/home/index",
						imgUrl: "https://1.xinzuowl.com/attachment/images/1/2016/11/z6KuY8xzb8NnKb0B1cW6wK46W9Dlnu.jpg",
					},
				}
			}
		}

		tmodtpl.helper("tomedia", function(src) {
			if (typeof src != 'string') {
				return '';
			}
			if(src.indexOf('http://') == 0 || src.indexOf('https://') == 0 || src.indexOf('../addons') == 0) {
				return src;
			}
			if(src.indexOf('images/') == 0) {
				return diyGuide.attachurl + src;
			}
		});

		diyGuide.tplGuide();
		diyGuide.tplEditor();
		diyGuide.initGotop();
		diyGuide.save();
	};

	diyGuide.tplGuide = function () {
		var html = tmodtpl("tpl-show-guide", diyGuide.guide);
		$("#app-preview").html(html);
	};

	diyGuide.tplEditor = function () {
		var html = tmodtpl("tpl-edit-guide", diyGuide.guide);
		$("#app-editor .inner").html(html);

		$("#app-editor #addItem").unbind('click').click(function () {
			var itemid = diyGuide.getId('M', 0);
			var max = $(this).closest('.form-items').data('max');
			var num = diyGuide.length(diyGuide.guide.data);
			if (num >= max) {
				Notify.info("最大添加 " + max + " 个！");
				return;
			}
			diyGuide.guide.data[itemid] = {
				pagePath: "",
				imgUrl: "",
			};
			diyGuide.tplGuide();
			diyGuide.tplEditor();
		});

		$("#app-editor .del-item").unbind('click').click(function() {
			var min = $(this).closest('.form-items').data('min');
			var itemid = $(this).closest('.item').data('id');
			if(min) {
				var length = diyGuide.length(diyGuide.guide.data);
				if(length <= min) {
					Notify.info("至少保留 " + min + " 个！");
					return;
				}
			}
			Notify.confirm("确定删除吗", function() {
				delete diyGuide.guide.data[itemid];
				diyGuide.tplGuide();
				diyGuide.tplEditor()
			});
		});

		diyGuide.tplSortable();

		$("#app-editor").find(".diy-bind").bind('input propertychange change', function() {
			var _this = $(this);
			var bind = _this.data("bind");
			var bindchild = _this.data('bind-child');
			var bindparent = _this.data('bind-parent');
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
					diyGuide.guide[bindchild][bindparent][bind] = value;
				} else {
					diyGuide.guide[bindchild][bind] = value;
				}
			} else {
				diyGuide.guide[bind] = value;
			}
			diyGuide.tplGuide();
			if(tplEditor) {
				diyGuide.tplEditor();
			}
		});

		var sliderlength = $("#app-editor .slider").length;
		if(sliderlength > 0) {
			$("#app-editor .slider").each(function(){
				var decimal = $(this).data('decimal');
				var multiply = $(this).data('multiply');
				var defaultValue = $(this).data("value");
				if(decimal) {
					defaultValue = defaultValue * decimal;
				}
				$(this).slider({
					slide: function(event, ui){
						var sliderValue = ui.value;
						if(decimal) {
							sliderValue = sliderValue / decimal;
						}
						$(this).siblings(".input").val(sliderValue).trigger("propertychange");
						$(this).siblings(".count").find("span").text(sliderValue);
					},
					value: defaultValue,
					min: $(this).data("min"),
					max: $(this).data("max")
				});
			});
		}
	};

	diyGuide.tplSortable = function () {
		$("#app-editor .inner").sortable({
			opacity: 0.8,
			placeholder: "highlight",
			items: '.item',
			revert: 100,
			scroll: false,
			cancel: '.goods-selector,input,.btn',
			axis: 'y',
			start: function(event, ui) {
				var height = ui.item.height();
				$(".highlight").css({"height": height + 22 + "px", "margin-bottom" : "10px"});
				$(".highlight").html('<div><i class="icon icon-plus"></i> 放置此处</div>');
				$(".highlight div").css({"line-height": height + 16 + "px", "font-size" : "16px", "color" : "#999", "text-align" : "center", "border" : "2px dashed #eee"})
			},
			update: function(event, ui) {
				diyGuide.sortItems();
			}
		});
	};

	diyGuide.sortItems = function () {
		var newItems = {};
		$("#app-editor .inner .item").each(function () {
			var thisid = $(this).data('id');
			newItems[thisid] = diyGuide.guide.data[thisid]
		});
		diyGuide.guide.data = newItems;
		diyGuide.tplGuide();
	};

	diyGuide.initGotop = function () {
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

	diyGuide.length = function(json) {
		if(typeof(json) === 'undefined') {
			return 0;
		}
		var len = 0;
		for(var i in json) {
			len++;
		}
		return len;
	};

	diyGuide.getId = function(S, N) {
		var date = +new Date();
		var id = S + (date + N);
		return id;
	};

	diyGuide.save = function () {
		$(".btn-save").unbind('click').click(function() {
			var status = $(this).data('status');
			if (status) {
				Notify.info("正在保存，请稍候。。。");
				return;
			}
			if(!diyGuide.guide.data) {
				Notify.info("启动图为空！");
				return;
			}
			$(".btn-save").data('status', 1).text("保存中...");
			var posturl = "./index.php?c=site&a=entry&ctrl=wxapp&ac=guide&op=index&do=web&m=we7_wmall";
			$.post(posturl, {guide: diyGuide.guide}, function(result) {
				$(".btn-save").text("保存启动图").data("status", 0);
				if(result.message.errno != 0) {
					Notify.error(result.message.message);
					return;
				}
				Notify.success("保存成功！", result.message.url);
			}, 'json');
		});
	};
	return diyGuide;
});