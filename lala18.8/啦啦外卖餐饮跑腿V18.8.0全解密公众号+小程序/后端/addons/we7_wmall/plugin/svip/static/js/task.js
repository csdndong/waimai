define(['jquery.ui', 'datetimepicker', 'clockpicker'], function(ui) {
	var task = {};
	task.init = function (params) {
		window.tmodtpl = params.tmodtpl;
		task.attachurl = params.attachurl;
		task.id = params.id;
		task.data = params.data;
		if(task.id == 0 || !task.id) {
			task.data = {
				activity_types: params.data.activity_types,
				title: '',
				thumb: '',
				activity_type: 'oneOrderFee',
				displayorder: '',
				condition: '',
				activity_starttime: task.dateToStr(),
				activity_endtime: task.dateToStr('', 5),
				task_endtime_type: '',
				task_endtime: '',
				task_takepart_type: '',
				takepart_repeat_time: '',
				award: {
					credit1: '',
					credit2: '',
					svip_credit1: '',
					redpackets: {}
				}
			};
		}
		tmodtpl.helper("tomedia", function(src){
			if (typeof src != 'string') {
				return '';
			}
			if(src.indexOf('http://') == 0 || src.indexOf('https://') == 0 || src.indexOf('../addons') == 0) {
				return src;
			}
			if(src.indexOf('images/') == 0) {
				return task.attachurl + src;
			}
		});
		task.tplEditor();
		task.save();
	};

	task.tplEditor = function() {
		var html = tmodtpl("tpl-basic", task.data);
		$("#basic").html(html);
		$('.clockpicker :text').clockpicker({autoclose: true});
		$('.datetimepicker :text').datetimepicker({
			lang : "zh",
			step : "10",
			timepicker : "true",
			closeOnDateSelect : true,
			format : "Y-m-d H:i",
			value : $(this).value
		});

		$("#basic #addItem").unbind('click').click(function() {
			var itemid = task.getId('M', 0);
			var max = $(this).closest('.form-items').data('max');
			var num = task.length(task.data.award.redpackets);
			if (num >= max) {
				Notify.info("最大添加 " + max + " 个！");
				return;
			}
			task.data.award.redpackets[itemid] = {
				scene: 'waimai',
				name: '通用红包',
				discount: 5,
				condition: 10,
				grant_days_effect: 1,
				use_days_limit: 10,
				times: {},
				categorys: {}
			};
			task.tplEditor();
		});

		$("#basic .del-item").unbind('click').click(function() {
			var min = $(this).closest('.form-items').data('min');
			var itemid = $(this).closest('.item').data('id');
			if(min) {
				var length = task.length(task.data.award.redpackets);
				if(length <= min) {
					Notify.info("至少保留 " + min + " 个！");
					return;
				}
			}
			Notify.confirm("确定删除吗", function() {
				delete task.data.award.redpackets[itemid];
				task.tplEditor();
			});
		});

		$("#basic .hour-add").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var times = task.data.award.redpackets[itemid]['times'];
			var length = task.length(times);
			if (length >= 3) {
				Notify.info("最大添加3个！");
				return;
			}
			var timeid = task.getId('T', 0);
			times[timeid] = {
				start_hour:  '20:00',
				end_hour: '23:00'
			};
			task.tplEditor();
		});

		$("#basic .hour-del").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var timeid = $(this).data('id');
			var times = task.data.award.redpackets[itemid]['times'];
			Notify.confirm("确定删除吗", function() {
				delete times[timeid];
				task.tplEditor();
			});
		});

		$("#basic .category-add").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var categorys = task.data.award.redpackets[itemid]['categorys'];
			var categoryid = task.getId('C', 0);
			categorys[categoryid] = {
				id: 0,
				title: '选择分类',
				src: ''
			};
			task.tplEditor();
		});

		$("#basic .category-del").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var categorys = task.data.award.redpackets[itemid]['categorys'];
			var categoryid = $(this).data('id');
			Notify.confirm("确定删除吗", function() {
				delete categorys[categoryid];
				task.tplEditor();
			});
		});

		$("#basic").find(".diy-bind").bind('input propertychange change', function() {
			var _this = $(this);
			var bind = _this.data("bind");
			var bindchild = _this.data('bind-child');
			var bindparent = _this.data('bind-parent');
			var bindcategory = _this.data('bind-category');
			var bindtype = _this.data('bind-type');
			var bindone = _this.data('bind-one');
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
							if(bindone) {
								task.data[bindchild][bindparent][bind][bindcategory][bindtype][bindone] = value;
							} else {
								task.data[bindchild][bindparent][bind][bindcategory][bindtype] = value;
							}
						} else {
							task.data[bindchild][bindparent][bind][bindcategory] = value;
						}
					} else {
						task.data[bindchild][bindparent][bind] = value;
					}
				} else {
					task.data[bindchild][bind] = value;
				}
			} else {
				task.data[bind] = value;
			}
			if(tplEditor) {
				task.tplEditor();
			}
		});
	};

	task.length = function(json) {
		if(typeof(json) === 'undefined') {
			return 0;
		}
		var len = 0;
		for(var i in json) {
			len++;
		}
		return len;
	};

	task.getId = function(S, N) {
		var date = +new Date();
		var id = S + (date + N);
		return id;
	};

	task.dateToStr = function(formatStr, date){
		formatStr = arguments[0] || "yyyy-MM-dd HH:mm:ss";
		if(date && typeof date == 'number') {
			var myDate=new Date();
			myDate.setDate(myDate.getDate()+date);
			date = myDate;
		} else {
			date = arguments[1] || new Date();
		}
		var str = formatStr;
		var Week = ['日','一','二','三','四','五','六'];
		str=str.replace(/yyyy|YYYY/,date.getFullYear());
		str=str.replace(/yy|YY/,(date.getYear() % 100)>9?(date.getYear() % 100).toString():'0' + (date.getYear() % 100));
		str=str.replace(/MM/,date.getMonth()>9?(date.getMonth() + 1):'0' + (date.getMonth() + 1));
		str=str.replace(/M/g,date.getMonth());
		str=str.replace(/w|W/g,Week[date.getDay()]);

		str=str.replace(/dd|DD/,date.getDate()>9?date.getDate().toString():'0' + date.getDate());
		str=str.replace(/d|D/g,date.getDate());

		str=str.replace(/hh|HH/,date.getHours()>9?date.getHours().toString():'0' + date.getHours());
		str=str.replace(/h|H/g,date.getHours());
		str=str.replace(/mm/,date.getMinutes()>9?date.getMinutes().toString():'0' + date.getMinutes());
		str=str.replace(/m/g,date.getMinutes());

		str=str.replace(/ss|SS/,date.getSeconds()>9?date.getSeconds().toString():'0' + date.getSeconds());
		str=str.replace(/s|S/g,date.getSeconds());

		return str;
	},

	task.save = function() {
		$(".btn-save").unbind('click').click(function() {
			var $this = $(this);
			Notify.confirm('确定保存任务吗?', function() {
				var status = $this.data('status');
				if(status) {
					Notify.info("正在保存，请稍候。。。");
					return;
				}
				$(".btn-save").data('status', 1).text("保存中...");
				var posturl = "./index.php?c=site&a=entry&ctrl=svip&ac=task&op=post&do=web&m=we7_wmall";
				delete task.data.activity_types;
				$.post(posturl, {id: task.id, data: task.data}, function(result) {
					$(".btn-save").text("保存任务").data("status", 0);
					if(result.message.errno != 0) {
						Notify.error(result.message.message);
						return;
					}
					Notify.success("保存成功", result.message.url);
				}, 'json');
			});
			return false;
		});
	};
	return task;
});