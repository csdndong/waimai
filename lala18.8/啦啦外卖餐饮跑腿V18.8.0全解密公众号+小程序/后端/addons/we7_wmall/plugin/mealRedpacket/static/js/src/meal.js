define(['jquery.ui', 'clockpicker'], function(ui, $) {
	var mealRedpacket = {};
	mealRedpacket.init = function (params) {
		window.tmodtpl = params.tmodtpl;
		mealRedpacket.attachurl = params.attachurl;
		mealRedpacket.id = params.id;
		mealRedpacket.data = params.data;
		if(!mealRedpacket.data) {
			mealRedpacket.data = {
				name: '套餐红包',
				rules: '',
				params: {
					title: '抢价值30元红包套餐',
					placeholder: '内含5元红包 x 6个',
					backgroundImage: '../addons/we7_wmall/plugin/mealRedpacket/static/img/meal_title_bg.png',
					price: 15,
					btnText: '立即抢购',
					tips: {
						T0123456789101: {
							imgurl: '../addons/we7_wmall/plugin/mealRedpacket/static/img/meal_day.png',
							text: '有效期31天',
							color: '#999999'
						},
						T0123456789102: {
							imgurl: '../addons/we7_wmall/plugin/mealRedpacket/static/img/meal_circle.png',
							text: '每月限购1次',
							color: '#999999'
						},
						T0123456789103: {
							imgurl: '../addons/we7_wmall/plugin/mealRedpacket/static/img/meal_percent.png',
							text: '与优惠活动同享',
							color: '#999999'
						}
					},
					exchangeStatus: 1,
				},
				style: {
					rulesColor: '#ffffff',
					titleColor: '#ffffff',
					placeholderColor: '#ffffff',
					btnColor: '#ffdee3',
					btnBackground: '#b64d57',
				},
				redpackets: {
					M0123456789101: {
						scene: 'waimai',
						order_type_limit: 0,
						name: '通用红包',
						discount: 5,
						condition: 0,
						grant_days_effect: 0,
						use_days_limit: 31,
						times: {},
						categorys: {}
					},
					M0123456789102: {
						scene: 'waimai',
						order_type_limit: 0,
						name: '通用红包',
						discount: 5,
						condition: 0,
						grant_days_effect: 0,
						use_days_limit: 31,
						times: {},
						categorys: {}
					},
					M0123456789103: {
						scene: 'waimai',
						order_type_limit: 0,
						name: '通用红包',
						discount: 5,
						condition: 0,
						grant_days_effect: 0,
						use_days_limit: 31,
						times: {},
						categorys: {}
					},
					M0123456789104: {
						scene: 'waimai',
						order_type_limit: 0,
						name: '通用红包',
						discount: 5,
						condition: 0,
						grant_days_effect: 0,
						use_days_limit: 31,
						times: {},
						categorys: {}
					},
					M0123456789105: {
						scene: 'waimai',
						order_type_limit: 0,
						name: '通用红包',
						discount: 5,
						condition: 0,
						grant_days_effect: 0,
						use_days_limit: 31,
						times: {},
						categorys: {}
					},
					M0123456789106: {
						scene: 'waimai',
						order_type_limit: 0,
						name: '通用红包',
						discount: 5,
						condition: 0,
						grant_days_effect: 0,
						use_days_limit: 31,
						times: {},
						categorys: {}
					},
				},
				exchanges: {
					S0123456789101: {
						store_id: 0,
						logo: '../addons/we7_wmall/plugin/mealRedpacket/static/img/store-1.jpg',
						title: '门店名称',
						score: '5',
						activity: '满35减12;满60减20',
						discount: 10,
						condition: 0,
						grant_days_effect: 0,
						use_days_limit: 31
					},
					S0123456789102: {
						store_id: 0,
						logo: '../addons/we7_wmall/plugin/mealRedpacket/static/img/store-2.jpg',
						title: '门店名称',
						score: '5',
						activity: '满35减12;满60减20',
						discount: 10,
						condition: 0,
						grant_days_effect: 0,
						use_days_limit: 31
					},
					S0123456789103: {
						store_id: 0,
						logo: '../addons/we7_wmall/plugin/mealRedpacket/static/img/store-3.jpg',
						title: '门店名称',
						score: '5',
						activity: '满35减12;满60减20',
						discount: 10,
						condition: 0,
						grant_days_effect: 0,
						use_days_limit: 31
					},
				}
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
				return mealRedpacket.attachurl + src;
			}
		});
		mealRedpacket.tplmealRedpacket();
		mealRedpacket.tplEditor();
		mealRedpacket.initGotop();
		mealRedpacket.save();
	};
	jQuery.base64 = (function($) {
		var _PADCHAR = "=", _ALPHA = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/", _VERSION = "1.1";
		function _getbyte64(s, i) {
			var idx = _ALPHA.indexOf(s.charAt(i));
			if(idx === -1) {
				throw"Cannot decode base64";
			}
			return idx;
		}
		function _decode_chars(y, x) {
			while (y.length > 0) {
				var ch = y[0];
				if(ch < 0x80) {
					y.shift();
					x.push(String.fromCharCode(ch))
				} else if((ch & 0x80) == 0xc0) {
					if(y.length < 2)break;
					ch = y.shift();
					var ch1 = y.shift();
					x.push(String.fromCharCode(((ch & 0x1f) << 6) + (ch1 & 0x3f)))
				} else {
					if(y.length < 3)break;
					ch = y.shift();
					var ch1 = y.shift();
					var ch2 = y.shift();
					x.push(String.fromCharCode(((ch & 0x0f) << 12) + ((ch1 & 0x3f) << 6) + (ch2 & 0x3f)))
				}
			}
		}

		function _decode(s) {
			var pads = 0, i, b10, imax = s.length, x = [], y = [];
			s = String(s);
			if(imax === 0) {
				return s
			}
			if(imax % 4 !== 0) {
				throw"Cannot decode base64"
			}
			if(s.charAt(imax - 1) === _PADCHAR) {
				pads = 1;
				if(s.charAt(imax - 2) === _PADCHAR) {
					pads = 2
				}
				imax -= 4
			}
			for (i = 0; i < imax; i += 4) {
				var ch1 = _getbyte64(s, i);
				var ch2 = _getbyte64(s, i + 1);
				var ch3 = _getbyte64(s, i + 2);
				var ch4 = _getbyte64(s, i + 3);
				b10 = (_getbyte64(s, i) << 18) | (_getbyte64(s, i + 1) << 12) | (_getbyte64(s, i + 2) << 6) | _getbyte64(s, i + 3);
				y.push(b10 >> 16);
				y.push((b10 >> 8) & 0xff);
				y.push(b10 & 0xff);
				_decode_chars(y, x)
			}
			switch (pads) {
				case 1:
					b10 = (_getbyte64(s, i) << 18) | (_getbyte64(s, i + 1) << 12) | (_getbyte64(s, i + 2) << 6);
					y.push(b10 >> 16);
					y.push((b10 >> 8) & 0xff);
					break;
				case 2:
					b10 = (_getbyte64(s, i) << 18) | (_getbyte64(s, i + 1) << 12);
					y.push(b10 >> 16);
					break
			}
			_decode_chars(y, x);
			if(y.length > 0)throw"Cannot decode base64";
			return x.join("")
		}

		function _get_chars(ch, y) {
			if(ch < 0x80)y.push(ch); else if(ch < 0x800) {
				y.push(0xc0 + ((ch >> 6) & 0x1f));
				y.push(0x80 + (ch & 0x3f))
			} else {
				y.push(0xe0 + ((ch >> 12) & 0xf));
				y.push(0x80 + ((ch >> 6) & 0x3f));
				y.push(0x80 + (ch & 0x3f))
			}
		}

		function _encode(s) {
			if(arguments.length !== 1) {
				throw"SyntaxError: exactly one argument required"
			}
			s = String(s);
			if(s.length === 0) {
				return s
			}
			var i, b10, y = [], x = [], len = s.length;
			i = 0;
			while (i < len) {
				_get_chars(s.charCodeAt(i), y);
				while (y.length >= 3) {
					var ch1 = y.shift();
					var ch2 = y.shift();
					var ch3 = y.shift();
					b10 = (ch1 << 16) | (ch2 << 8) | ch3;
					x.push(_ALPHA.charAt(b10 >> 18));
					x.push(_ALPHA.charAt((b10 >> 12) & 0x3F));
					x.push(_ALPHA.charAt((b10 >> 6) & 0x3f));
					x.push(_ALPHA.charAt(b10 & 0x3f))
				}
				i++
			}
			switch (y.length) {
				case 1:
					var ch = y.shift();
					b10 = ch << 16;
					x.push(_ALPHA.charAt(b10 >> 18) + _ALPHA.charAt((b10 >> 12) & 0x3F) + _PADCHAR + _PADCHAR);
					break;
				case 2:
					var ch1 = y.shift();
					var ch2 = y.shift();
					b10 = (ch1 << 16) | (ch2 << 8);
					x.push(_ALPHA.charAt(b10 >> 18) + _ALPHA.charAt((b10 >> 12) & 0x3F) + _ALPHA.charAt((b10 >> 6) & 0x3f) + _PADCHAR);
					break
			}
			return x.join("")
		}

		return {decode: _decode, encode: _encode, VERSION: _VERSION}
	}(jQuery));

	mealRedpacket.tplmealRedpacket = function() {
		var html = tmodtpl("tpl-show-mealRedpacket", mealRedpacket.data);
		$("#app-preview").html(html);
	};

	mealRedpacket.tplEditor = function() {
		var html = tmodtpl("tpl-edit-mealRedpacket", mealRedpacket.data);
		$("#app-editor .inner").html(html);
		$('.clockpicker :text').clockpicker({autoclose: true});
		var richtext = $("#app-editor .form-richtext").length;
		if(richtext > 0) {
			var ueditoroption = {
				'autoClearinitialContent': false,
				'toolbars': [['fullscreen', 'source', 'preview', '|', 'bold', 'italic', 'underline', 'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft', 'justifycenter', 'justifyright', '|', 'insertorderedlist', 'insertunorderedlist', 'blockquote', 'emotion', 'removeformat', '|', 'rowspacingtop', 'rowspacingbottom', 'lineheight', 'indent', 'paragraph', 'fontsize', '|', 'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', '|', 'anchor', 'map', 'print', 'drafts', '|', 'link']],
				'elementPathEnabled': false,
				'initialFrameHeight': 300,
				'focus': false,
				'maximumWords': 9999999999999
			};
			var opts = {
				type: 'image',
				direct: false,
				multiple: true,
				tabs: {'upload': 'active', 'browser': '', 'crawler': ''},
				path: '',
				dest_dir: '',
				global: false,
				thumb: false,
				width: 0
			};
			UE.registerUI('myinsertimage', function(editor, uiName) {
				editor.registerCommand(uiName, {
					execCommand: function(){
						require(['fileUploader'], function(uploader) {
							uploader.show(function(imgs) {
								if(imgs.length == 0) {
									return
								} else if(imgs.length == 1) {
									editor.execCommand('insertimage', {
										'src': imgs[0]['url'],
										'_src': imgs[0]['url'],
										'width': '100%',
										'alt': imgs[0].filename
									})
								} else {
									var imglist = [];
									for (i in imgs) {
										imglist.push({
											'src': imgs[i]['url'],
											'_src': imgs[i]['url'],
											'width': '100%',
											'alt': imgs[i].filename
										})
									}
									editor.execCommand('insertimage', imglist)
								}
							}, opts);
						})
					}
				});
				var btn = new UE.ui.Button({
					name: '插入图片',
					title: '插入图片',
					cssRules: 'background-position: -726px -77px',
					onclick: function(){
						editor.execCommand(uiName);
					}
				});
				editor.addListener('selectionchange', function(){
					var state = editor.queryCommandState(uiName);
					if(state == -1) {
						btn.setDisabled(true);
						btn.setChecked(false);
					} else {
						btn.setDisabled(false);
						btn.setChecked(state);
					}
				});
				return btn;
			}, 48);
			UE.registerUI('myinsertvideo', function(editor, uiName) {
				editor.registerCommand(uiName, {
					execCommand: function(){
						require(['fileUploader'], function(uploader) {
							uploader.show(function(video) {
								if(!video) {
									return
								} else {
									var videoType = video.isRemote ? 'iframe' : 'video';
									editor.execCommand('insertvideo', {
										'url': video.url,
										'width': 300,
										'height': 200
									}, videoType);
								}
							}, {fileSizeLimit: 5120000, type: 'video', allowUploadVideo: true});
						});
					}
				});
				var btn = new UE.ui.Button({
					name: '插入视频',
					title: '插入视频',
					cssRules: 'background-position: -320px -20px',
					onclick: function(){
						editor.execCommand(uiName);
					}
				});
				editor.addListener('selectionchange', function(){
					var state = editor.queryCommandState(uiName);
					if(state == -1) {
						btn.setDisabled(true);
						btn.setChecked(false);
					} else {
						btn.setDisabled(false);
						btn.setChecked(state);
					}
				});
				return btn;
			}, 20);
			UE.registerUI('mylink', function(editor, uiName) {
				var btn = new UE.ui.Button({
					name: 'selectUrl',
					title: '系统链接',
					cssRules: 'background-position: -622px 80px;',
					onclick: function(){
						//todo
						$("#" + this.id).attr({"data-toggle": "selectUrl", "data-callback": "selectUrlCallback"});
					}
				});
				editor.addListener('selectionchange', function(){
					var state = editor.queryCommandState(uiName);
					if(state == -1) {
						btn.setDisabled(true);
						btn.setChecked(false);
					} else {
						btn.setDisabled(false);
						btn.setChecked(state);
					}
				});
				return btn;
			});
			if(typeof(UE) != 'undefined') {
				UE.delEditor('rich')
			}
			var ue = UE.getEditor('rich', ueditoroption);
			ue.ready(function(){
				var thisitem = mealRedpacket.data;
				var richContent = thisitem.rules;
				richContent = $.base64.decode(richContent);
				ue.setContent(richContent);
				ue.addListener('contentChange', function(){
					var newContent = ue.getContent();
					newContent = $.base64.encode(newContent);
					$("#richtext").html(newContent).trigger('change');
				})
			});
		}
		var storelength = $("#app-editor .js-selectStore").length;
		if(storelength > 0) {
			$(document).off("click", '.js-selectStore');
			$(document).on("click", '.js-selectStore', function() {
				mealRedpacket.exchangeId = $(this).closest('.item').data('id');
				irequire(["web/tiny"], function(tiny){
					tiny.selectStore(mealRedpacket.callbackStore, {mutil: 0});
				});
			});
		}

		$("#app-editor .add-item").unbind('click').click(function() {
			var itemid = mealRedpacket.getId('M', 0);
			var num = mealRedpacket.length(mealRedpacket.data.redpackets);
			var type = $(this).data('type');
			if(type == 'exchanges') {
				itemid = mealRedpacket.getId('S', 0);
				num = mealRedpacket.length(mealRedpacket.data.exchanges);
			}
			var max = $(this).closest('.form-items').data('max');
			if (num >= max) {
				Notify.info("最大添加 " + max + " 个！");
				return;
			}
			if(type == 'redpackets') {
				mealRedpacket.data.redpackets[itemid] = {
					scene: 'waimai',
					order_type_limit: 0,
					name: '通用红包',
					discount: 5,
					condition: 0,
					grant_days_effect: 0,
					use_days_limit: 31,
					times: {},
					categorys: {}
				}
			} else if(type == 'exchanges') {
				mealRedpacket.data.exchanges[itemid] = {
					store_id: 0,
					logo: '../addons/we7_wmall/plugin/mealRedpacket/static/img/store-3.jpg',
					title: '门店名称',
					score: '5',
					activity: '满35减12;满60减20',
					discount: 10,
					condition: 0,
					grant_days_effect: 0,
					use_days_limit: 31
				}
			}
			mealRedpacket.tplmealRedpacket();
			mealRedpacket.tplEditor();
		});

		$("#app-editor .del-item").unbind('click').click(function() {
			var type = $(this).data('type');
			var min = $(this).closest('.form-items').data('min');
			var itemid = $(this).closest('.item').data('id');
			var length = mealRedpacket.length(mealRedpacket.data.redpackets);
			if(type == 'exchanges') {
				length = mealRedpacket.length(mealRedpacket.data.exchanges);
			}
			if(min) {
				if(length <= min) {
					Notify.info("至少保留 " + min + " 个！");
					return;
				}
			}
			Notify.confirm("确定删除吗", function() {
				delete mealRedpacket.data[type][itemid];
				mealRedpacket.tplmealRedpacket();
				mealRedpacket.tplEditor();
			});
		});

		$("#app-editor .hour-add").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			if(!mealRedpacket.data.redpackets[itemid]['times']) {
				mealRedpacket.data.redpackets[itemid]['times'] = {};
			}
			var times = mealRedpacket.data.redpackets[itemid]['times'];
			var length = mealRedpacket.length(times);
			if (length >= 3) {
				Notify.info("最大添加3个！");
				return;
			}
			var timeid = mealRedpacket.getId('T', 0);
		 	times[timeid] = {
				start_hour:  '08:00',
				end_hour: '22:00'
			};
			mealRedpacket.tplEditor();
		});

		$("#app-editor .hour-del").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var timeid = $(this).data('id');
			var times = mealRedpacket.data.redpackets[itemid]['times'];
			Notify.confirm("确定删除吗", function() {
				delete times[timeid];
				mealRedpacket.tplEditor();
			});
		});

		$("#app-editor .category-add").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			if(!mealRedpacket.data.redpackets[itemid]['categorys']) {
				mealRedpacket.data.redpackets[itemid]['categorys'] = {};
			}
			var categorys = mealRedpacket.data.redpackets[itemid]['categorys'];
			var categoryid = mealRedpacket.getId('C', 0);
			categorys[categoryid] = {
				id: 0,
				title: '选择分类',
				src: ''
			};
			if(mealRedpacket.data.redpackets[itemid].scene == 'paotui') {
				categorys[categoryid].title = '选择场景';
			}
			mealRedpacket.tplEditor();
		});

		$("#app-editor .category-del").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var categorys = mealRedpacket.data.redpackets[itemid]['categorys'];
			var categoryid = $(this).data('id');
			Notify.confirm("确定删除吗", function() {
				delete categorys[categoryid];
				mealRedpacket.tplEditor();
			});
		});

		$("#app-editor").find(".diy-bind").bind('input propertychange change', function() {
			var _this = $(this);
			var bind = _this.data("bind");
			var bindchild = _this.data('bind-child');
			var bindparent = _this.data('bind-parent');
			var bindcategory = _this.data('bind-category');
			var bindtype = _this.data('bind-type');
			var bindone = _this.data('bind-one');
			var bindtwo = _this.data('bind-two');
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
								if(bindtwo) {
									mealRedpacket.data[bindchild][bindparent][bind][bindcategory][bindtype][bindone][bindtwo] = value;
								} else {
									mealRedpacket.data[bindchild][bindparent][bind][bindcategory][bindtype][bindone] = value;
								}
							} else {
								mealRedpacket.data[bindchild][bindparent][bind][bindcategory][bindtype] = value;
							}
						} else {
							mealRedpacket.data[bindchild][bindparent][bind][bindcategory] = value;
						}
					} else {
						mealRedpacket.data[bindchild][bindparent][bind] = value;
					}
				} else {
					mealRedpacket.data[bindchild][bind] = value;
				}
			} else {
				mealRedpacket.data[bind] = value;
			}
			mealRedpacket.tplmealRedpacket();
			if(tplEditor) {
				mealRedpacket.tplEditor();
			}
		});
	};

	mealRedpacket.callbackStore = function(data) {
		if(!data) {
			Notify.error("回调数据错误，请重试！");
			return;
		}
		var exchangeId = mealRedpacket.exchangeId;
		mealRedpacket.data.exchanges[exchangeId].store_id = data.id;
		mealRedpacket.data.exchanges[exchangeId].logo = data.logo;
		mealRedpacket.data.exchanges[exchangeId].title = data.title;
		mealRedpacket.data.exchanges[exchangeId].score = data.score;
		mealRedpacket.tplmealRedpacket();
		mealRedpacket.tplEditor();
		mealRedpacket.exchangeId = null;
	};

	mealRedpacket.length = function(json) {
		if(typeof(json) === 'undefined') {
			return 0;
		}
		var len = 0;
		for(var i in json) {
			len++;
		}
		return len;
	};

	mealRedpacket.getId = function(S, N) {
		var date = +new Date();
		var id = S + (date + N);
		return id;
	};

	mealRedpacket.initGotop = function() {
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

	mealRedpacket.save = function() {
		$(".btn-save").unbind('click').click(function() {
			var $this = $(this);
			Notify.confirm('确定保存吗?', function() {
				var status = $this.data('status');
				if(status) {
					Notify.info("正在保存，请稍候。。。");
					return;
				}
				$(".btn-save").data('status', 1).text("保存中...");
				var posturl = "./index.php?c=site&a=entry&ctrl=mealRedpacket&ac=meal&op=post&do=web&m=we7_wmall";
				$.post(posturl, {id: mealRedpacket.id, data: mealRedpacket.data}, function(result) {
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
	return mealRedpacket;
});