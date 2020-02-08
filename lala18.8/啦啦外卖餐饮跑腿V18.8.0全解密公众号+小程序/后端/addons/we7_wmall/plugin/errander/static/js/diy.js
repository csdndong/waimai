define(['jquery.ui'], function(ui) {
	var diy = {
		sysinfo: null,
		id: 0,
		type: 'home',
		navs: {},
		initPart: [],
		data: {},
		selected: 'page',
		childid: null,
		keyworderr: false
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

	diy.init = function(params) {
		window.tmodtpl = params.tmodtpl;
		diy.attachurl = params.attachurl;
		diy.type = params.type;
		diy.data = params.data;
		diy.id = params.id;
		diy.mallset = params.mallset;
		diy.diymenu = params.diymenu;
		if(diy.data) {
			diy.page = diy.data.page;
			diy.items = diy.data.items;
		};
		diy.initTpl();
		diy.initPage();
		diy.initItems();
		diy.initParts();
		diy.initSortable();
		diy.initGotop();
		diy.initSave();
		$("#page").unbind('click').click(function(){
			if(diy.selected == 'page') {
				return;
			};
			diy.selected = 'page';
			diy.initPage();
		});
	};
	diy.initParts = function(){
		diy.getParts();
		var partGroup = {
			0: ['guide'],
		};
		var partPage = partGroup[0];
		$.each(partPage, function(index, val) {
			var params = diy.parts[val];
			if(params) {
				params.id = val;
				diy.initPart.push(params)
			}
		});
		var html = tmodtpl("tpl-parts", diy);
		$("#parts").html(html).show();
		$("#parts nav").unbind('click').click(function(){
			var id = $(this).data('id');
			if(id === 'page') {
				$("#page").trigger("click");
				return;
			}
			var inArray = $.inArray(id, partPage);
			if(inArray < 0) {
				Notify.error("此页面组建不存在！");
				return
			}
			var item = $.extend(true, {}, diy.parts[id]);
			delete item.name;
			if(!item) {
				Notify.error("未找到此元素！");
				return
			}
			var itemTplShow = $("#tpl-show-" + id).length;
			var itemTplEditor = $("#tpl-editor-" + id).length;
			if(itemTplShow == 0 || itemTplEditor == 0) {
				Notify.error("添加失败！模板错误，请刷新页面重试");
				return;
			}
			var itemid = diy.getId("M", 0);
			if(item.data) {
				var itemData = $.extend(true, {}, item.data);
				var newData = {};
				var index = 0;
				$.each(itemData, function(id, data) {
					var childid = diy.getId("C", index);
					newData[childid] = data;
					delete childid;
					index++;
				});
				item.data = newData
			}
			if(item.max && item.max > 0) {
				var itemNum = diy.getItemNum(id);
				if(itemNum > 0 && itemNum >= item.max) {
					Notify.error("此元素最多允许添加 " + item.max + " 个");
					return;
				}
			}
			var append = true;
			if(diy.selected && diy.selected != 'page') {
				var thisitem = diy.items[diy.selected];
				var noAppend = [];
				if(noAppend.length > 0 && $.inArray(thisitem.id, noAppend) != -1) {
					append = false;
				}
			}
			if(item.istop) {
				var newItems = {};
				newItems[itemid] = item;
				$.each(diy.items, function(id, eachitem) {
					newItems[id] = eachitem;
				});
				diy.items = newItems;
			} else if(diy.selected && diy.selected != 'page' && append) {
				var newItems = {};
				$.each(diy.items, function(id, eachitem) {
					newItems[id] = eachitem;
					if(id == diy.selected) {
						newItems[itemid] = item;
					}
				});
				diy.items = newItems;
			} else {
				diy.items[itemid] = item;
			}

			var normalItems = {};
			var bottomItems = [];
			var newBottomItems = {};
			$.each(diy.items, function(id, eachitem) {
				if(!eachitem.isbottom) {
					normalItems[id] = eachitem;
				} else {
					eachitem['key'] = id;
					bottomItems.push(eachitem);
				}
			});
			if(bottomItems.length > 0) {
				function compare(property){
					return function(a,b){
						var value1 = a[property];
						var value2 = b[property];
						return value1 - value2;
					}
				}
				bottomItems.sort(compare('priority'));
				for(var i = 0; i < bottomItems.length; i++) {
					var item = bottomItems[i];
					var key = item['key'];
					delete item['key'];
					newBottomItems[key] = item;
				}
			}
			diy.items = $.extend({}, normalItems, newBottomItems);
			diy.initItems();
			$(".drag[data-itemid='" + itemid + "']").trigger('mousedown').trigger('click');
			diy.selected = itemid;
		});
	};
	diy.getId = function(S, N) {
		var date = +new Date();
		var id = S + (date + N);
		return id;
	};
	diy.getParts = function(){
		diy.parts = {
			guide: {
				name: '跑腿引导页',
				style: {
					background: '#ffffff',
				},
				params: {
					placeholder: '输入想买的商品，如：麦当劳嘻哈小食盒',
					submiturl: ''
				},
				picture: {
					M0123456789101: {
						imgurl: 'https://p0.meituan.net/paotui/j8pawbmx2uik9.png',
						linkurl: '',
					},
					M0123456789102: {
						imgurl: 'https://p0.meituan.net/paotui/j8pawkcocr3sor.png',
						linkurl: '',
					},
					M0123456789103: {
						imgurl: 'https://p0.meituan.net/paotui/j8pawbmx2uik9.png',
						linkurl: '',
					}
				},
				rider: {
					text: {
						left: '附近有',
						num: '44',
						right:'位骑手'
					},
					color: '#ffffff',
					num_color: '#f6ce00',
				},
				lanmu: {
					M0123456789101: {
						linkurl: '',
						text: '小吃',
						color: '#83490d',
						background_color: '#fef7df',
						is_hot: '1'
					},
					M0123456789102: {
						linkurl: '',
						text: '咖啡',
						color: '#83490d',
						background_color: '#fef7df',
						is_hot: '0'
					},
					M0123456789103: {
						linkurl: '',
						text: '酒水',
						color: '#83490d',
						background_color: '#fef7df',
						is_hot: '0'
					},
					M0123456789104: {
						linkurl: '',
						text: '药品',
						color: '#83490d',
						background_color: '#fef7df',
						is_hot: '0'
					},
					M0123456789105: {
						linkurl: '',
						text: '栏目5',
						color: '#83490d',
						background_color: '#fef7df',
						is_hot: '0'
					},
					M0123456789106: {
						linkurl: '',
						text: '栏目6',
						color: '#83490d',
						background_color: '#fef7df',
						is_hot: '0'
					},
					M0123456789107: {
						linkurl: '',
						text: '栏目7',
						color: '#83490d',
						background_color: '#fef7df',
						is_hot: '0'
					},
					M0123456789108: {
						linkurl: '',
						text: '栏目8',
						color: '#83490d',
						background_color: '#fef7df',
						is_hot: '0'
					},
				},
				feature: {
					M0123456789101: {
						icon: 'icon-location',
						icon_color: '#FFD56F',
						linkurl: '',
						text: '同城配送',
						color: '#000',
					},
					M0123456789102: {
						icon: 'icon-time',
						icon_color: '#FFD56F',
						linkurl: '',
						text: '全天服务',
						color: '#000',
					},
					MF0123456789103: {
						icon: 'icon icon-goods',
						icon_color: '#FFD56F',
						linkurl: '',
						text: '万能代购',
						color: '#000',
					},
				},
				order: {
					text: '订单',
					color: '#FFD56F',
					icon: 'icon-order',
					border_color: '#FFD56F',
				},
				max: 1
			},
		};
	};
	diy.initItems = function(selected) {
		var preview = $("#app-preview");
		if(!diy.items) {
			diy.items = {};
			return;
		}
		preview.empty();
		$.each(diy.items, function(itemid, item) {
			if(typeof(item.id) !== 'undefined') {
				var newItem = $.extend(true, {}, item);
				newItem.itemid = itemid;
				var html = tmodtpl("tpl-show-" + item.id, newItem);
				preview.append(html);
			}
		});
		var btnhtml = $("#tpl-editor-del").html();
		$("#app-preview .drag").append(btnhtml);
		$("#app-preview .drag .btn-edit-del .btn-del").unbind('click').click(function(e) {
			e.stopPropagation();
			var drag = $(this).closest(".drag");
			var itemid = drag.data('itemid');
			var nodelete = $(this).closest(".drag").hasClass("nodelete");
			if(nodelete) {
				Notify.error("此组建禁止删除");
				return;
			}
			Notify.confirm("确定删除吗", function(){
				var nearid = diy.getNear(itemid);
				delete diy.items[itemid];
				diy.initItems();
				if(nearid) {
					$(document).find(".drag[data-itemid='" + nearid + "']").trigger('mousedown');
				} else {
					$("#page").trigger('click');
				}
			})
		});
		if(selected) {
			diy.selectedItem(selected);
		}
	};
	diy.selectedItem = function(itemid){
		if(!itemid) {
			return
		}
		diy.selected = itemid;
		if(itemid == 'page') {
			$("#page").trigger('click')
		} else {
			$(".drag[data-itemid='" + itemid + "']").addClass('selected')
		}
	};
	diy.initPage = function(initE) {
		if(typeof(initE) === 'undefined') {
			initE = true;
		}
		if(!diy.page) {
			diy.page = {
				// type: diy.type,
				title: '请输入页面标题',
				name: '未命名页面',
				desc: '',
				keyword: '',
				background: '#F3F3F3',
				diygotop: '0',
				navigationbackground: '#000000',
				navigationtextcolor: '#ffffff'
				//followbar: '0',
				//danmu: 0,


			};
		}
		$("#page").text(diy.page.title);
		$("#page").css({'background-color': diy.page.navigationbackground, 'color': diy.page.navigationtextcolor});
		$("#app-preview").css({'background-color': diy.page.background});
		$("#app-preview").find(".drag").removeClass("selected");
		if(initE) {
			diy.initEditor();
		}
	};
	diy.initSortable = function(){
		$("#app-preview").sortable({
			opacity: 0.8,
			placeholder: "highlight",
			items: '.drag:not(.fixed)',
			revert: 100,
			scroll: false,
			start: function(event, ui) {
				var height = ui.item.height();
				$(".highlight").css({"height": height + 22 + "px", "margin-bottom" : "10px"});
				$(".highlight").html('<div><i class="icon icon-plus"></i> 放置此处</div>');
				$(".highlight div").css({"line-height": height + 16 + "px", "font-size" : "16px", "color" : "#999", "text-align" : "center", "border" : "2px dashed #eee"})
			},
			stop: function(event, ui) {
				diy.initEditor();
			},
			update: function(event, ui) {
				diy.sortItems();
			}
		});
		$("#app-preview").disableSelection();
		$(document).on('mousedown', "#app-preview .drag", function(){
			if($(this).hasClass("selected")) {
				return;
			}
			$("#app-preview").find(".drag").removeClass("selected");
			$(this).addClass("selected");
			diy.selected = $(this).data('itemid');
			diy.initEditor();
		});
	};
	diy.sortItems = function(){
		var newItems = {};
		$("#app-preview .drag").each(function(){
			var thisid = $(this).data('itemid');
			newItems[thisid] = diy.items[thisid];
		});
		diy.items = newItems;
	};
	diy.initEditor = function(scroll) {
		if(typeof(scroll) === 'undefined') {
			scroll = true;
		}
		var itemid = diy.selected;
		var top = -50;
		if(diy.selected != 'page') {
			var stop = $(".selected").position().top;
			top = stop ? stop : 0;
		}
		if(scroll) {
			$("#app-editor").unbind('animate').animate({"margin-top": top + 100 + "px"});
			setTimeout(function(){
				$("body").unbind('animate').animate({scrollTop: top + 100 + "px"}, 1000)
			}, 1000);
		}
		if(diy.selected) {
			if(diy.selected == 'page') {
				var html = tmodtpl("tpl-editor-page", diy);
				$("#app-editor .inner").html(html);
			} else {
				var item = $.extend(true, {}, diy.items[diy.selected]);
				item.itemid = diy.selected;
				var html = tmodtpl("tpl-editor-" + item.id, item);
				$("#app-editor .inner").html(html);
			}
			$("#app-editor").attr("data-editid", diy.selected).show();
		}

		var goodslength = $("#app-editor .js-selectGoods").length;
		if(goodslength > 0) {
			$(document).off("click", '.js-selectGoods');
			$(document).on("click", '.js-selectGoods', function() {
				diy.childid = $(this).closest('.item').data('id');
				var callback = $(this).data('callback');
				irequire(["web/tiny"], function(tiny){
					tiny.selectgoods(callback, {mutil: 0});
				});
			});
		}

		var storelength = $("#app-editor .js-selectStore").length;
		if(storelength > 0) {
			$(document).off("click", '.js-selectStore');
			$(document).on("click", '.js-selectStore', function() {
				diy.childid = $(this).closest('.item').data('id');
				var callback = $(this).data('callback');
				irequire(["web/tiny"], function(tiny){
					tiny.selectStore(callback, {mutil: 0});
				});
			});
		}

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
		var childitems = $("#app-editor .form-items").length;
		if(childitems > 0) {
			diy.initSortableChild();
			$(".addChild").unbind('click').click(function(){
				var itemid = diy.selected;
				var type = diy.items[itemid].id;
				var data = $(this).data('goal') ? $(this).data('goal') : 'data';
				var temp = diy.parts[type][data];
				var max = $(this).closest(".form-items").data('max');
				if(max) {
					var length = diy.length(diy.items[itemid][data]);
					if(length >= max) {
						Notify.error("最大添加 " + max + " 个！");
						return;
					}
				}
				var newChild = {};
				var index = 0;
				$.each(temp, function(i, t) {
					if(index == 0) {
						newChild = t;
						index++;
					}
				});
				if(newChild) {
					var childName = diy.getId("M", 0);
					if(typeof(diy.items[itemid][data]) === 'undefined') {
						diy.items[itemid][data] = {};
					}
					newChild = $.extend(true, {}, newChild);
					diy.items[itemid][data][childName] = newChild;
					console.log(111);
				}
				diy.initItems(itemid);
				diy.initEditor(false);
			});
			$("#app-editor .form-items .item .btn-del").unbind('click').click(function(){
				var childid = $(this).closest(".item").data('id');
				var itemid = diy.selected;
				var min = $(this).closest(".form-items").data("min");
				var data = $(this).data('goal') ? $(this).data('goal') : 'data';
				if(min) {
					var length = diy.length(diy.items[itemid][data]);
					if(length <= min) {
						Notify.error("至少保留 " + min + " 个！");
						return;
					}
				}
				Notify.confirm("确定删除吗", function(){
					delete diy.items[itemid][data][childid];
					diy.initItems(itemid);
					diy.initEditor(false);
				})
			})
		}
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
				var thisitem = diy.items[itemid];
				var richContent = thisitem.params.content;
				richContent = $.base64.decode(richContent);
				ue.setContent(richContent);
				ue.addListener('contentChange', function(){
					var newContent = ue.getContent();
					newContent = $.base64.encode(newContent);
					$("#richtext").html(newContent).trigger('change');
				})
			});
		}
		$("#app-editor").find(".diy-bind").bind('input propertychange change', function(){
			var _this = $(this);
			var bind = _this.data("bind");
			var bindchild = _this.data('bind-child');
			var bindparent = _this.data('bind-parent');
			var initEditor = _this.data('bind-init');
			var value = '';
			var tag = this.tagName;
			if(!itemid) {
				diy.selectedItem('page');
			}
			if(tag == 'INPUT') {
				var type = _this.attr('type');
				if(type == 'checkbox') {
					value = [];
					_this.closest('.form-group').find('input[type=checkbox]').each(function(){
						var checked = this.checked;
						var valname = $(this).val();
						if(checked) {
							value.push(valname);
						}
					})
				} else {
					var placeholder = _this.data('placeholder');
					value = _this.val();
					value = value == '' ? placeholder : value;
				}
			} else if(tag == 'SELECT') {
				value = _this.find('option:selected').val();
			} else if(tag == 'TEXTAREA') {
				value = _this.val();
			}
			value = $.trim(value);
			if(itemid == 'page') {
				if(bindchild) {
					if(!diy.page[bindchild]) {
						diy.page[bindchild] = {};
					}
					diy.page[bindchild][bind] = value;
				} else {
					diy.page[bind] = value;
				}
				diy.initPage(false);
				if(bind == 'keyword') {
					$.post(biz.url('wxapp/page/keyword'), {id: diy.id, keyword: value}, function(r) {
						if(r.status == 0) {
							_this.closest('.form-group').addClass('has-error');
							diy.keyworderr = true;
						} else {
							_this.closest('.form-group').removeClass('has-error');
							diy.keyworderr = false;
						}
					}, 'json');
				}
			} else {
				if(bindchild) {
					if(bindparent) {
						diy.items[itemid][bindparent][bindchild][bind] = value;
					} else {
						diy.items[itemid][bindchild][bind] = value;
					}
				} else {
					diy.items[itemid][bind] = value;
				}
				diy.initItems(itemid);
			}
			if(initEditor) {
				diy.initEditor(false);
			}
		})
	};
	diy.initSortableChild = function(){
		$("#app-editor .inner").sortable({
			opacity: 0.8,
			placeholder: "highlight",
			items: '.item',
			revert: 100,
			scroll: false,
			cancel: '.goods-selector,input,select,.btn,btn-del',
			start: function(event, ui) {
				var height = ui.item.height();
				$(".highlight").css({"height": height + 22 + "px"});
				$(".highlight").html('<div><i class="fa fa-plus"></i> 放置此处</div>');
				$(".highlight div").css({"line-height": height + 16 + "px"});
			},
			update: function(event, ui) {
				diy.sortChildItems();
			}
		})
	};
	diy.initTpl = function(){
		tmodtpl.helper("tomedia", function(src) {
			if(src.indexOf('images/') == 0) {
				return diy.attachurl + src;
			}
			if(typeof src != 'string') {
				return '';
			}
			if(src.indexOf('http://') == 0 || src.indexOf('https://') == 0 || src.indexOf('../addons/we7_wmall/') == 0) {
				return src;
			} else if(src.indexOf('images/') == 0 || src.indexOf('audios/') == 0) {
				return diy.attachurl + src;
			}
		});
		tmodtpl.helper("decode", function(content) {
			return $.base64.decode(content);
		});
		tmodtpl.helper("count", function(data) {
			return diy.length(data);
		});
		tmodtpl.helper("toArray", function(data) {
			var oldArray = $.makeArray(data);
			var newArray = [];
			$.each(data, function(itemid, item) {
				newArray.push(item);
			});
			return newArray;
		});
		tmodtpl.helper("strexists", function(str, tag) {
			if(!str || !tag) {
				return false;
			}
			if(str.indexOf(tag) != -1){
				return true;
			}
			return false;
		});
		tmodtpl.helper("inArray", function(str, tag) {
			if(!str || !tag) {
				return false;
			}
			if(typeof(str) == 'string'){
				var arr = str.split(",");
				if($.inArray(tag, arr)>-1){
					return true;
				}
			}
			return false;
		});
		tmodtpl.helper("define", function(str) {
			var str;
		})
	};
	diy.initGotop = function(){
		$(window).bind('scroll resize', function(){
			var scrolltop = $(window).scrollTop();
			if(scrolltop > 300) {
				$("#gotop").show();
			} else {
				$("#gotop").hide();
			}
			$("#gotop").unbind('click').click(function(){
				$('body').animate({scrollTop: "0px"}, 1000);
			})
		});
	};
	diy.getNear = function(itemid) {
		var newarr = [];
		var index = 0;
		var prev = 0;
		var next = 0;
		$.each(diy.items, function(id, obj) {
			newarr[index] = id;
			if(id == itemid) {
				prev = index - 1;
				next = index + 1;
			}
			index++;
		});
		var pervid = newarr[prev];
		var nextid = newarr[next];
		if(nextid) {
			return nextid;
		}
		if(pervid) {
			return pervid;
		}
		return false
	};
	diy.getItemNum = function(id) {
		if(!id || !diy.items) {
			return -1;
		}
		var itemNum = 0;
		$.each(diy.items, function(itemid, eachitem) {
			if(eachitem.id == id) {
				itemNum++;
			}
		});
		return itemNum;
	};
	diy.sortChildItems = function(){
		var newChild = {};
		var itemid = diy.selected;
		$("#app-editor .form-items .item").each(function(){
			var thisid = $(this).data('id');
			newChild[thisid] = diy.items[itemid].data[thisid];
		});
		diy.items[itemid].data = newChild;
		diy.initItems(itemid);
	};
	diy.length = function(json) {
		if(typeof(json) === 'undefined') {
			return 0;
		}
		var jsonlen = 0;
		for (var item in json) {
			jsonlen++;
		}
		return jsonlen;
	};
	diy.callbackGoods = function(data) {
		if(!data) {
			Notify.error("回调数据错误，请重试！");
			return;
		}
		var itemid = diy.selected;
		var childid = diy.childid;
		diy.items[itemid].data[childid] = {
			sid: data.sid,
			goods_id: data.id,
			title: data.title,
			thumb: data.thumb,
			price: data.price,
			old_price: data.old_price,
			discount: data.discount,
			store_title: data.store_title,
			sailed: data.sailed,
			comment_good_percent: data.comment_good_percent,
		};
		diy.initItems(itemid);
		diy.initEditor(false);
		diy.childid = null;
	};
	diy.callbackStore = function(data) {
		if(!data) {
			Notify.error("回调数据错误，请重试！");
			return;
		}
		var itemid = diy.selected;
		var childid = diy.childid;
		diy.items[itemid].data[childid] = {
			store_id: data.id,
			logo: data.logo,
			title: data.title,
			score_cn: data.score_cn,
			sailed: data.sailed,
			send_price: data.send_price,
			delivery_price: data.delivery_price,
			delivery_title: data.delivery_title,
			delivery_time: data.delivery_time,
			activity: data.activity,
			hot_goods: data.hot_goods,
		};
		console.dir(diy.items[itemid].data[childid]);
		diy.initItems(itemid);
		diy.initEditor(false);
		diy.childid = null;
	};
	diy.initSave = function() {
		$(".btn-save").unbind('click').click(function() {
			var status = $(this).data('status');
			if (status) {
				Notify.error("正在保存，请稍候。。。");
				return;
			}

			diy.data = {};
			diy.data = {page: diy.page, items: diy.items};
			if(!diy.page.title) {
				Notify.error("页面标题是必填项");
				$("#page").trigger("click");
				return;
			}
			$(".btn-save").data('status', 1).text("保存中...");
			irequire(['tiny'], function(tiny){
				$.post(tiny.getUrl('errander/diypage/home'), {id: diy.id, type: diy.type, data: diy.data}, function(ret) {
					var ret = ret.message;
					if(ret.errno != 0) {
						Notify.error(ret.message);
						$(".btn-save[data-type='save']").text("保存页面").data("status", 0);
						return;
					}
					Notify.success("保存成功！", ret.url);
				}, 'json');
			});
		});
	};
	return diy;
});