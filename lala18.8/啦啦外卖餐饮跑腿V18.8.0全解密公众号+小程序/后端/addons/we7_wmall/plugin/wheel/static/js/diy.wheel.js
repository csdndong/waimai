define(['jquery.ui', 'clockpicker', 'datetimepicker'], function(ui) {
	var diyWheel = {itemid: ''};
	diyWheel.init = function (params) {
		window.tmodtpl = params.tmodtpl;
		diyWheel.attachurl = params.attachurl;
		diyWheel.wheel = params.wheel;
		diyWheel.id = params.id;
		if(!diyWheel.wheel) {
			diyWheel.wheel = {
				name: '',
				params: {
					name: '活动名称',
					desc: '活动描述',
					status: '1',
					starttime: '',
					endtime: '',
					memberlimit: '0',
					membertype: '0',
					backstatus: '',
					consume: '',
					takepartlimit: '1',
					takepartback: '',
					backlimit: '',
					takeparttotal: '1',
					per_day: '1',
					noawardtips: ''
				},
				data: {
					one: {
						text: '一等奖',
						type: 'one',
						color: '#f87a7c',
						award: 'credit1',
						credittype: '0',
						creditmax: '10',
						creditmin: '1',
						creditnum: '10',
						awardtotal: '1',
						giftcontent: '',
						get_chance: '',
						can_get_total: '',
						can_get_day: '',
						redpackets: {},
						order_discount_type: 'free',
						order_free_max:'',
						imgurl: '../addons/we7_wmall/plugin/wheel/static/img/reward.png'
					},
					two: {
						text: '二等奖',
						type: 'two',
						color: '#f87a7c',
						award: 'credit1',
						credittype: '0',
						creditmax: '10',
						creditmin: '1',
						creditnum: '10',
						awardtotal: '5',
						giftcontent: '',
						get_chance: '',
						can_get_total: '',
						can_get_day: '',
						redpackets: {},
						order_discount_type: 'free',
						order_free_max:'',
						imgurl: '../addons/we7_wmall/plugin/wheel/static/img/reward.png'
					},
					three: {
						text: '三等奖',
						type: 'three',
						color: '#f87a7c',
						award: 'credit1',
						credittype: '0',
						creditmax: '10',
						creditmin: '1',
						creditnum: '10',
						awardtotal: '10',
						giftcontent: '',
						get_chance: '',
						can_get_total: '',
						can_get_day: '',
						redpackets: {},
						order_discount_type: 'free',
						order_free_max:'',
						imgurl: '../addons/we7_wmall/plugin/wheel/static/img/reward.png'
					}
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
				return diyWheel.attachurl + src;
			}
		});

		diyWheel.tplWheel();
		diyWheel.tplEditor();
		diyWheel.initGotop();
		diyWheel.save();
	};

	diyWheel.tplWheel = function () {
		var html = tmodtpl("tpl-show-wheel", diyWheel.wheel);
		$("#app-preview").html(html);
	};

	diyWheel.tplEditor = function () {
		var html = tmodtpl("tpl-edit-wheel", diyWheel.wheel);
		$("#app-editor .inner").html(html);
		$('.clockpicker :text').clockpicker({autoclose: true});
		$('.datetimepicker :text').datetimepicker({
			lang : "zh",
			step : "10",
			timepicker : "true",
			closeOnDateSelect : true,
			format : "Y-m-d H:i",
			value : $(this).value
		});

		$("#app-editor #addItem").unbind('click').click(function () {
			var itemid = diyWheel.getId('M', 0);
			var max = $(this).closest('.form-items').data('max');
			var num = diyWheel.length(diyWheel.wheel.data);
			if (num >= max) {
				Notify.info("最大添加 " + max + " 个！");
				return;
			}
			diyWheel.wheel.data[itemid] = {
				text: '',
				color: '',
				backgroundcolor: '',
				award: '',
				credittype: '0',
				creditmax: '10',
				creditmin: '1',
				creditnum: '10',
				awardtotal: '5',
				giftcontent: '',
				order_free_max:'',
				order_discount_type: 'free',
				redpackets: {},
				imgurl:"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFoAAABmBAMAAAC5PpvnAAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAAwUExURUxpcfzgT/xjVPxgVPy3UvxgVP2adPxgVPzgT/xgVPxgVPzgT//55/67rP/Rwv/fzilpAjkAAAAKdFJOUwC4HLr+3jpmbpGZ6r2aAAABgElEQVRYw+2YvUrEQBDHF4N7Z3lgIVyTxspGsPI6wQeQAxFTpbayEu6qWF9zIApeJUo4Mt1ZCXmFgC+gvoH3BBYm2Xzsza7JDjZy7r/b//wyzH4kE5YxSXx0fyWPWecWTu+YXh0PAM5dZMCZFt4YwGqwMGBfR1+IGFyWxm5hPLkqzIsYzMvUXulokpeZAHxhOICflzSrgkNhDCojVOcIKLhVG/VUqtWSgnmdY2zIcqTgHD0NJ5jelKN+vZ65HhrpdJ6ePH5spKPVQlpyg+vAD3Xz616q7WNZaHiUAjv5pvF+bKZFhk9iU70w1o3NFbA9Av1MKCQrpU+gFyymyNK/pmm7Q9t52qminVja20BIHmQvZrdnpoD9I/GbA6SP5Se2Dn2l45R6S14VL1K7QgNdtJOxIS36oGdIi1YHhjSgRtlCu5a2tKXXgOZLVV9JonGzLyFPTPXH6HdV6Sw1rm/PiaUtvQY0o9G0/6qZIR2hy41meojuSRrp0JWvd1rocJqS36tj93C8qjCpAAAAAElFTkSuQmCC",
			};
			diyWheel.tplWheel();
			diyWheel.tplEditor();
		});


		$("#app-editor #addChildItem").unbind('click').click(function () {
			var childItemId = diyWheel.getId('R', 0);
			var itemid = $(this).closest('.item').data('id');
			var max = $(this).closest('.form-child-items').data('max');
			var num = diyWheel.length(diyWheel.wheel.data[itemid].redpackets);
			if (num >= max) {
				Notify.info("最大添加 " + max + " 个！");
				return;
			}
			if(num == 0) {
				diyWheel.wheel.data[itemid].redpackets = {};
			}
			diyWheel.wheel.data[itemid].redpackets[childItemId] = {
				scene: 'waimai',
				name: '',
				nums: '',
				discount: '',
				condition: '',
				grant_days_effect: '',
				use_days_limit: '',
				times: {},
				categorys: {}
			};
			console.log(diyWheel.wheel.data[itemid].redpackets[childItemId]);
			diyWheel.tplWheel();
			diyWheel.tplEditor();
		});

		$("#app-editor .del-childItem").unbind('click').click(function() {
			var min = $(this).closest('.form-child-items').data('min');
			var itemid = $(this).closest('.item').data('id');
			var childItemId = $(this).closest('.child-item').data('childId');
			if(min) {
				var length = diyWheel.length(diyWheel.wheel.data[itemid].redpackets);
				if(length <= min) {
					Notify.info("至少保留 " + min + " 个！");
					return;
				}
			}
			Notify.confirm("确定删除吗", function() {
				delete diyWheel.wheel.data[itemid].redpackets[childItemId];
				diyWheel.tplWheel();
				diyWheel.tplEditor()
			});
		});

		$("#app-editor .del-item").unbind('click').click(function() {
			var min = $(this).closest('.form-items').data('min');
			var itemid = $(this).closest('.item').data('id');
			if(min) {
				var length = diyWheel.length(diyWheel.wheel.data);
				if(length <= min) {
					Notify.info("至少保留 " + min + " 个！");
					return;
				}
			}
			Notify.confirm("确定删除吗", function() {
				delete diyWheel.wheel.data[itemid];
				diyWheel.tplWheel();
				diyWheel.tplEditor()
			});
		});

		$("#app-editor .hour-add").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var childItemId = $(this).closest('.child-item').data('childId');
			var times = diyWheel.wheel.data[itemid].redpackets[childItemId]['times'];
			var length = diyWheel.length(times);
			if (length >= 3) {
				Notify.info("最大添加3个！");
				return;
			}
			var timeid = diyWheel.getId('T', 0);
			times[timeid] = {
				start_hour:  '20:00',
				end_hour: '23:00'
			};
			diyWheel.tplEditor();
		});

		$("#app-editor .category-add").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var childItemId = $(this).closest('.child-item').data('childId');
			var categorys = diyWheel.wheel.data[itemid].redpackets[childItemId]['categorys'];
			var categoryid = diyWheel.getId('C', 0);
			categorys[categoryid] = {
				id: 0,
				title: '选择分类',
				src: ''
			};
			diyWheel.tplEditor();
		});

		$("#app-editor .child-item-del").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var childItemId = $(this).closest('.child-item').data('childId');
			var typeid = $(this).data('id');
			var type = $(this).data('type');
			var type_obj = diyWheel.wheel.data[itemid].redpackets[childItemId][type];
			Notify.confirm("确定删除吗", function() {
				delete type_obj[typeid];
				diyWheel.tplEditor();
			});
		});

		$("#app-editor").find(".diy-bind").bind('input propertychange change', function() {
			var _this = $(this);
			var bind = _this.data("bind");
			var bindchild = _this.data('bind-child');
			var bindparent = _this.data('bind-parent');
			var tplEditor = _this.data('bind-init');
			var bindcategory = _this.data('bind-category');
			var bindtype = _this.data('bind-type');
			var bindone = _this.data('bind-one');
			var bindtwo = _this.data('bind-two');
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
							if(bindone) {
								if(bindtwo) {
									diyWheel.wheel[bindparent][bindchild][bind][bindcategory][bindtype][bindone][bindtwo] = value;
								} else {
									diyWheel.wheel[bindparent][bindchild][bind][bindcategory][bindtype][bindone] = value;
								}
							} else {
								diyWheel.wheel[bindparent][bindchild][bind][bindcategory][bindtype] = value;
							}
						} else {
							diyWheel.wheel[bindparent][bindchild][bind][bindcategory] = value;
						}
					} else {
						diyWheel.wheel[bindparent][bindchild][bind] = value;
					}
				} else {
					diyWheel.wheel[bindchild][bind] = value;
				}
			} else {
				diyWheel.wheel[bind] = value;
			}

			diyWheel.tplWheel();
			if(tplEditor) {
				diyWheel.tplEditor();
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

	diyWheel.tplSortable = function () {
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
				diyWheel.sortItems();
			}
		});
	};

	diyWheel.sortItems = function () {
		var newItems = {};
		$("#app-editor .inner .item").each(function () {
			var thisid = $(this).data('id');
			newItems[thisid] = diyWheel.wheel.data[thisid]
		});
		diyWheel.wheel.data = newItems;
		diyWheel.tplWheel();
	};

	diyWheel.initGotop = function () {
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

	diyWheel.length = function(json) {
		if(typeof(json) === 'undefined') {
			return 0;
		}
		var len = 0;
		for(var i in json) {
			len++;
		}
		return len;
	};

	diyWheel.getId = function(S, N) {
		var date = +new Date();
		var id = S + (date + N);
		return id;
	};

	diyWheel.save = function () {
		$(".btn-save").unbind('click').click(function() {
			var status = $(this).data('status');
			if (status) {
				Notify.info("正在保存，请稍候。。。");
				return;
			}
			if(!diyWheel.wheel.data) {
				Notify.info("数据为空！");
				return;
			}
			$(".btn-save").data('status', 1).text("保存中...");
			var posturl = "./index.php?c=site&a=entry&ctrl=wheel&ac=activity&op=post&do=web&m=we7_wmall";
			$.post(posturl, {wheel: diyWheel.wheel, id:diyWheel.id}, function(result) {
				$(".btn-save").text("保存中").data("status", 0);
				if(result.message.errno != 0) {
					Notify.error(result.message.message);
					return;
				}
				Notify.success("保存成功！", result.message.url);
			}, 'json');
		});
	};
	return diyWheel;
});