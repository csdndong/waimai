define(["jquery", "bootstrap"], function($, bs) {
	function _bindCssEvent(events, callback) {
		var dom = this;
		function fireCallBack(e) {
			if (e.target !== this) {
				return;
			}
			callback.call(this, e);
			for (var i = 0; i < events.length; i++) {
				dom.off(events[i], fireCallBack);
			}
		}
		if (callback) {
			for (var i = 0; i < events.length; i++) {
				dom.on(events[i], fireCallBack);
			}
		}
	}
	$.fn.animationEnd = function(callback) {
		_bindCssEvent.call(this, [ "webkitAnimationEnd", "animationend" ], callback);
		return this;
	};
	$.fn.transitionEnd = function(callback) {
		_bindCssEvent.call(this, [ "webkitTransitionEnd", "transitionend" ], callback);
		return this;
	};
	$.fn.transition = function(duration) {
		if (typeof duration !== "string") {
			duration = duration + "ms";
		}
		for (var i = 0; i < this.length; i++) {
			var elStyle = this[i].style;
			elStyle.webkitTransitionDuration = elStyle.MozTransitionDuration = elStyle.transitionDuration = duration;
		}
		return this;
	};
	$.fn.transform = function(transform) {
		for (var i = 0; i < this.length; i++) {
			var elStyle = this[i].style;
			elStyle.webkitTransform = elStyle.MozTransform = elStyle.transform = transform;
		}
		return this;
	};
	$.fn.iappend = function(html, callback) {
		var len = $("body").html().length;
		this.append(html);
		var e = 1, interval = setInterval(function() {
			e++;
			var clear = function() {
				clearInterval(interval);
				callback && callback();
			};
			if (len != $("body").html().length || e > 1e3) {
				clear();
			}
		}, 1);
	};

	$('.btn').hover(function(){
		$(this).tooltip('show');
	},function(){
		$(this).tooltip('hide');
	});

	irequire(['jquery.slimscroll'], function(){
		$(".slimscroll").slimScroll({
			height: 'auto',
			size: '5px',
			railVisible: false
		});
	});

	$('[data-toggle="popover"]').hover(function(){
		$(this).popover('show');
	},function(){
		$(this).popover('hide');
	});

	window.redirect = function(url) {
		location.href = url;
	}

	if($(".select2").length > 0) {
		irequire(["select2"], function() {
			$(".select2").each(function() {
				$(this).select2({});
				if($(this).hasClass('js-select2')) {
					$(this).change(function(){
						$(this).parents('form').submit();
					});
				}
			});
		});
	}

	if ($(".js-clip").length > 0) {
		irequire(['clipboard'], function(Clipboard) {
			var clipboard = new Clipboard(".js-clip", {
				text: function(e) {
					return $(e).data("url") || $(e).data("href") || $(e).data("text");
				}
			});
			clipboard.on("success", function(e) {
				Notify.success("复制成功");
			});
		});
	}

	if($(".js-switch").length > 0) {
		irequire(['switchery'], function(){
			$(".js-switch").switchery();
		});
	}

	if($(".toggle-tabs").length > 0) {
		$(document).on("click", ".toggle-role", function() {
			var $this = $(this);
			var $tabs = $this.parents('.toggle-tabs');
			var $content = $($tabs.data('content'));
			if(!$content) return false;
			var target = $this.data('target');
			if(!target) return false;

			$content.find('.toggle-pane').removeClass('active').addClass('hide');
			$content.find('#' + target).removeClass('hide').addClass('active');
		});
	}

	if($(".js-qrcode").length > 0) {
		irequire(['jquery.qrcode'], function(){
			$(".js-qrcode").each(function() {
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
	}

	if($(".js-checkbox").length > 0) {
		require(["bootstrap.switch"], function() {
			$('.js-checkbox').bootstrapSwitch();
			$('.js-checkbox').on("switchChange.bootstrapSwitch", function(e, state){
				var href = $(this).data('href');
				if(!href) {
					return false;
				}
				var name = $(this).data('name') ? $(this).data('name') : 'status';
				var status = this.checked ? 1 : 0;
				var params = {};
				params[name] = status;
				$.post(href, params, function(data){
					var result = $.parseJSON(data);
					var errno = result.message.errno, message = result.message.message, url = result.message.url;
					if(errno != 0) {
						Notify.error(message);
						return false;
					}
					if(url) {
						Notify.success(message, url);
					}
				});
			});
		});
	}

	if($(".js-colorpicker").length > 0) {
		$(".colorpicker").each(function(){
			var elm = this;
			util.colorpicker(elm, function(color){
				$(elm).parent().prev().prev().val(color.toHexString());
				$(elm).parent().prev().css("background-color", color.toHexString());
			});
		});
		$(".colorclean").click(function(){
			$(this).parent().prev().prev().val("");
			$(this).parent().prev().css("background-color", "#FFF");
		});
	}

	if($(".js-daterange").length > 0) {
		$(document).on("click", ".js-daterange .js-btn-custom", function() {
			$(this).siblings().removeClass('btn-primary').addClass('btn-default');
			$(this).addClass('btn-primary');
			$(this).parent().next('.js-btn-daterange').removeClass('hide');
			$(this).parents('form').find(':hidden[name="days"]').val(-1);
		});

		require(['daterangepicker'], function() {
			$(".js-daterange").each(function() {
				var form = $(this).data('form');
				$(this).find('.daterange').on('apply.daterangepicker', function(ev, picker) {
					$(form).submit();
				});
			});
		});
	};

	$(document).on("click", ".js-refresh", function(e) {
		e && e.preventDefault();
		var url = $(e.target).data("href");
		url ? window.location = url : window.location.reload();
	});

	$(document).on("click", "js-back", function(e) {
		e && e.preventDefault();
		var url = $(e.target).data("href");
		url ? window.location = url : window.history.back();
	});

	$(document).on("click", '.js-modal', function(e) {
		e.preventDefault();
		var obj = $(this), confirm = obj.data("confirm");
		var handler = function() {
			$("#js-modal").remove(), e.preventDefault();
			var url = obj.data("href") || obj.attr("href"), data = obj.data("set"), modal;
			$.ajax(url, {
				type: "get",
				dataType: "html",
				cache: false,
				data: data
			}).done(function(result) {
				if (result.substr(0, 10) == '{"message"') {
					var json = eval("(" + result + ")");
					var errno = json.message.errno,
						message = json.message.message;
					if(errno) {
						Notify.error(message || Notify.lang.error);
						return;
					}
				}
				modal = $('<div class="modal fade" id="js-modal"></div>');
				$(document.body).append(modal), modal.modal("show");
				modal.iappend(result, function() {
					var form_validate = $("form.form-validate", modal);
					if(form_validate.length > 0) {
						$("button[type='submit']", modal).length && $("button[type='submit']", modal).attr("disabled", true);
						irequire(["web/form"], function(form) {
							$("button[type='submit']", modal).length && $("button[type='submit']", modal).removeAttr("disabled");
						});
					}
				});
			});
		};
		if (confirm) {
			Notify.confirm(confirm, handler);
		} else {
			handler();
		}
	});

	$(document).on("click", '.js-edit', function(e) {
		var obj = $(this), url = obj.data("href"), html = $.trim(obj.html()), required = obj.data("required") || true;
		var oldval = $.trim($(this).text());
		e.preventDefault();
		submit = function() {
			e.preventDefault();
			var val = $.trim(obj.val());
			if(required) {
				if(val == "") {
					Notify.error(Notify.lang.empty);
					return;
				}
			}
			if(val == html) {
				input.remove(), obj.html(val).show();
				return;
			}
			if(url) {
				var params = {};
				var name = obj.data('name');
				params[name] = val;
				$.post(url, params, function(ret) {
					var result = $.parseJSON(ret);
					var errno = result.message.errno,
						url = result.message.url,
						message = result.message.message;
					if(!errno) {
						obj.html(val).show();
					} else {
						Notify.error(message, url);
					}
				}).fail(function() {
					Notify.error(Notify.lang.exception);
				});
			} else {
				obj.html(val).show();
			}
			obj.trigger("valueChange", [val, oldval]);
		};
		obj.select().blur(function() {
			submit();
		}).keyup(function(e) {
			console.dir(e)
			if(e.keyCode == 13) {
				submit();
				return;
			}
		});
	});

	$(document).on("click", '.js-post', function(e) {
		e.preventDefault();
		var obj = $(this), confirm = obj.data("confirm"), url = obj.data("href") || obj.attr("href"), data = obj.data("set") || {}, html = obj.html();
		handler = function() {
			e.preventDefault();
			if(obj.attr("submitting") == "1") {
				return;
			}
			obj.html('<i class="fa fa-spinner fa-spin"></i>').attr("submitting", 1);
			$.post(url, {data: data}, function(ret) {
				var result = $.parseJSON(ret);
				var errno = result.message.errno,
					url = result.message.url ? result.message.url : location.href,
					message = result.message.message;
				if(!errno) {
					Notify.success(message || Notify.lang.success, url);
				} else {
					Notify.error(message || Notify.lang.erroror, url);
					obj.removeAttr("submitting").html(html);
				}
			}).fail(function() {
				obj.removeAttr("submitting").html(html);
				Notify.error(Notify.lang.exception);
			});
		};
		if (confirm) {
			Notify.confirm(confirm, handler);
		} else {
			handler();
		}
	});

	$(document).on("click", '.js-selectImg', function() {
		var input = $(this).data('input');
		var element = $(this).data('element');
		var full = $(this).data('full');
		util.image('', function(data) {
			var imgurl = data.attachment;
			if(full) {
				imgurl = data.url;
			}
			if(input) {
				$(input).val(imgurl).trigger('change');
			}
			if(element) {
				$(element).attr('src', data.url);
			}
		});
	});

	$(document).on("click", '.js-selectLink', function() {
		var input = $(this).data('input');
		irequire(["web/tiny"], function(tiny){
			tiny.selectLink(function(href) {
				if(input) {
					$(input).val(href).trigger('change');
				}
			});
		});
	});

	$(document).on("click", '.js-selectData', function() {
		var input = $(this).data('input');
		var val = $(this).data('val');
		if(input) {
			$(input).val(val).trigger('change');
		}
		return true;
	});

	$(document).on("click", '.js-selectWxappLink', function() {
		var input = $(this).data('input');
		var scene = $(this).data('scene') || 'page';
		var options = {
			scene : scene
		};
		irequire(["web/tiny"], function(tiny){
			tiny.selectWxappLink(function(href) {
				if(input) {
					$(input).val(href).trigger('change');
				}
			}, options);
		});
	});

	$(document).on("click", '.js-selectVueLink', function() {
		var input = $(this).data('input');
		var scene = $(this).data('scene') || 'vuepage';
		var options = {
			scene : scene
		};
		irequire(["web/tiny"], function(tiny){
			tiny.selectWxappLink(function(href) {
				if(input) {
					$(input).val(href).trigger('change');
				}
			}, options);
		});
	});

	$(document).on("click", '.js-selectWxappIcon', function() {
		var input = $(this).data('input');
		var element = $(this).data('element');
		if(!input && !element) {
			return;
		}
		irequire(["web/tiny"], function(tiny){
			tiny.selectWxappIcon(function(icon){
				if(input) {
					$(input).children().eq(0).val(icon.tabbar.normal).trigger("change");
					$(input).children().eq(1).val(icon.tabbar.active).trigger("change");
				}
				if(element) {
					$(element).children().eq(0).attr('src', icon.url.normal);
					$(element).children().eq(1).attr('src', icon.url.active);
				}
			});
		});
	});

	$(document).on("click", '.js-selectCategory', function() {
		var id = $(this).data('id-input');
		var title = $(this).data('title-input');
		var src = $(this).data('src-input');
		var element = $(this).data('element');
		if(!id || !title) {
			return;
		}
		irequire(["web/tiny"], function(tiny){
			tiny.selectCategory(function(data){
				if(id) {
					$(id).val(data.id).trigger('change');
				}
				if(title) {
					$(title).val(data.title).trigger('change');
				}
				if(src) {
					$(src).val(data.thumb_cn).trigger('change');
				}
				if(element) {
					$(element).find('img').attr('src', data.thumb_cn);
					$(element).find('.title').html(data.title);
				}
			}, {mutil: 0});
		});
	});

	$(document).on("click", '.js-selectIcon', function() {
		var input = $(this).data('input');
		var element = $(this).data('element');
		if(!input && !element) {
			return;
		}
		irequire(["web/tiny"], function(tiny){
			tiny.selectIcon(function(icon){
				if(input) {
					$(input).val(icon).trigger("change");
				}
				if(element) {
					$(element).removeAttr("class").addClass("icon " + icon);
				}
			});
		});
	});

	var form_validate_length = $('.form-validate').length,
		js_table_length = $('.js-table').length;
	if(!form_validate_length && !js_table_length) {
		$('#page-loading').remove();
	} else {
		if(form_validate_length > 0) {
			irequire(['web/form'], function(){});
		}
		if(js_table_length > 0) {
			irequire(['web/table'], function(){});
		}
	}
});