define(['jquery.ui', 'clockpicker'], function(ui, $) {
	var superRedpacket = {};
	superRedpacket.init = function (params) {
		window.tmodtpl = params.tmodtpl;
		superRedpacket.attachurl = params.attachurl;
		superRedpacket.id = params.id;
		superRedpacket.data = params.data;
		if(!superRedpacket.data) {
			superRedpacket.data = {
				name: '套餐红包Plus',
				placeholder: '提示信息',
				rules: '',
				redpackets: {
					P0123456789101: {
						name: '套餐一',
						old_price: 50,
						price: 25,
						data: {
							M0123456789101: {
								scene: 'waimai',
								order_type_limit: 0,
								name: '套餐红包',
								discount: 5,
								condition: 20,
								grant_days_effect: 0,
								use_days_limit: 7,
								times: {},
								categorys: {}
							},
							M0123456789102: {
								scene: 'waimai',
								order_type_limit: 0,
								name: '套餐红包',
								nums: 100,
								discount: 5,
								condition: 40,
								grant_days_effect: 1,
								use_days_limit: 10,
								times: {},
								categorys: {}
							},
							M0123456789103: {
								scene: 'waimai',
								order_type_limit: 0,
								name: '套餐红包',
								nums: 100,
								discount: 6,
								condition: 50,
								grant_days_effect: 1,
								use_days_limit: 10,
								times: {},
								categorys: {}
							}
						}
					}
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
				return superRedpacket.attachurl + src;
			}
		});
		superRedpacket.tplSuperRedpacket();
		superRedpacket.tplEditor();
		superRedpacket.initGotop();
		superRedpacket.save();
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

	superRedpacket.tplSuperRedpacket = function() {
		var html = tmodtpl("tpl-show-superRedpacket", superRedpacket.data);
		$("#app-preview").html(html);
	};

	superRedpacket.tplEditor = function() {
		var html = tmodtpl("tpl-edit-superRedpacket", superRedpacket.data);
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
				var thisitem = superRedpacket.data;
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

		$("#app-editor #addMeal").unbind('click').click(function() {
			var itemid = superRedpacket.getId('P', 0);
			var max = $(this).closest('.form-items').data('max');
			var num = superRedpacket.length(superRedpacket.data.redpackets);
			if (num >= max) {
				Notify.info("最大添加 " + max + " 个！");
				return;
			}
			superRedpacket.data.redpackets[itemid] = {
				name: '套餐一',
				old_price: 50,
				price: 25,
				data: {
					M0123456789105: {
						scene: 'waimai',
						order_type_limit: 0,
						name: '通用红包',
						discount: 5,
						condition: 20,
						grant_days_effect: 0,
						use_days_limit: 7,
						times: {},
						categorys: {}
					},
					M0123456789106: {
						scene: 'waimai',
						order_type_limit: 0,
						name: '下午茶频道红包',
						nums: 100,
						discount: 5,
						condition: 40,
						grant_days_effect: 1,
						use_days_limit: 10,
						times: {
							T0123456789101: {
								start_hour: '13:00',
								end_hour: '17:30'
							}
						},
						categorys: {}
					},
					M0123456789107: {
						scene: 'waimai',
						order_type_limit: 0,
						name: '夜宵频道红包',
						nums: 100,
						discount: 6,
						condition: 50,
						grant_days_effect: 1,
						use_days_limit: 10,
						times: {
							T0123456789101: {
								start_hour: '00:00',
								end_hour: '05:59'
							},
							T0123456789102: {
								start_hour: '20:00',
								end_hour: '23:59'
							}
						},
						categorys: {}
					}
				}
			};
			superRedpacket.tplSuperRedpacket();
			superRedpacket.tplEditor();
		});

		$("#app-editor .del-meal").unbind('click').click(function() {
			var min = $(this).closest('.form-items').data('min');
			var itemid = $(this).closest('.item').data('id');
			if(min) {
				var length = superRedpacket.length(superRedpacket.data.redpackets);
				if(length <= min) {
					Notify.info("至少保留 " + min + " 个！");
					return;
				}
			}
			Notify.confirm("确定删除吗", function() {
				delete superRedpacket.data.redpackets[itemid];
				superRedpacket.tplEditor();
				superRedpacket.tplSuperRedpacket();
			});
		});

		$("#app-editor #addItem").unbind('click').click(function() {
			var itemid = superRedpacket.getId('M', 0);
			var max = $(this).closest('.form-items').data('max');
			var parentid = $(this).closest('.form-items').data('parentid');
			var num = superRedpacket.length(superRedpacket.data.redpackets[parentid].data);
			if (num >= max) {
				Notify.info("最大添加 " + max + " 个！");
				return;
			}
			superRedpacket.data.redpackets[parentid].data[itemid] = {
				scene: 'waimai',
				order_type_limit: 0,
				name: '通用红包',
				discount: 5,
				condition: 10,
				grant_days_effect: 1,
				use_days_limit: 10,
				times: {},
				categorys: {}
			};
			superRedpacket.tplSuperRedpacket();
			superRedpacket.tplEditor();
		});

		$("#app-editor .del-item").unbind('click').click(function() {
			var min = $(this).closest('.form-items').data('min');
			var itemid = $(this).closest('.item').data('id');
			var parentid = $(this).closest('.item').data('parentid');
			if(min) {
				var length = superRedpacket.length(superRedpacket.data.redpackets[parentid].data);
				if(length <= min) {
					Notify.info("至少保留 " + min + " 个！");
					return;
				}
			}
			Notify.confirm("确定删除吗", function() {
				delete superRedpacket.data.redpackets[parentid].data[itemid];
				superRedpacket.tplEditor();
			});
		});

		$("#app-editor .hour-add").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var parentid = $(this).closest('.item').data('parentid');
			if(!superRedpacket.data.redpackets[parentid].data[itemid]['times']) {
				superRedpacket.data.redpackets[parentid].data[itemid]['times'] = {};
			}
			var times = superRedpacket.data.redpackets[parentid].data[itemid]['times'];
			var length = superRedpacket.length(times);
			if (length >= 3) {
				Notify.info("最大添加3个！");
				return;
			}
			var timeid = superRedpacket.getId('T', 0);
		 	times[timeid] = {
				start_hour:  '08:00',
				end_hour: '22:00'
			};
			superRedpacket.tplEditor();
		});

		$("#app-editor .hour-del").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var parentid = $(this).closest('.item').data('parentid');
			var timeid = $(this).data('id');
			var times = superRedpacket.data.redpackets[parentid].data[itemid]['times'];
			Notify.confirm("确定删除吗", function() {
				delete times[timeid];
				superRedpacket.tplEditor();
			});
		});

		$("#app-editor .category-add").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var parentid = $(this).closest('.item').data('parentid');
			if(!superRedpacket.data.redpackets[parentid].data[itemid]['categorys']) {
				superRedpacket.data.redpackets[parentid].data[itemid]['categorys'] = {};
			}
			var categorys = superRedpacket.data.redpackets[parentid].data[itemid]['categorys'];
			var categoryid = superRedpacket.getId('C', 0);
			categorys[categoryid] = {
				id: 0,
				title: '选择分类',
				src: ''
			};
			if(superRedpacket.data.redpackets[parentid].data[itemid].scene == 'paotui') {
				categorys[categoryid].title = '选择场景';
			}
			superRedpacket.tplEditor();
		});

		$("#app-editor .category-del").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var parentid = $(this).closest('.item').data('parentid');
			var categorys = superRedpacket.data.redpackets[parentid].data[itemid]['categorys'];
			var categoryid = $(this).data('id');
			Notify.confirm("确定删除吗", function() {
				delete categorys[categoryid];
				superRedpacket.tplEditor();
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
									superRedpacket.data[bindchild][bindparent][bind][bindcategory][bindtype][bindone][bindtwo] = value;
								} else {
									superRedpacket.data[bindchild][bindparent][bind][bindcategory][bindtype][bindone] = value;
								}
							} else {
								superRedpacket.data[bindchild][bindparent][bind][bindcategory][bindtype] = value;
							}
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
				var posturl = "./index.php?c=site&a=entry&ctrl=mealRedpacket&ac=plus&op=post&do=web&m=we7_wmall";
				$.post(posturl, {id: superRedpacket.id, data: superRedpacket.data}, function(result) {
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