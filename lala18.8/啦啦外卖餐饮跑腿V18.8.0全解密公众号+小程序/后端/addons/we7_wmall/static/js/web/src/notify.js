define(function(require, exports, module) {
	var Notify = {}
	Notify.lang = {
		success: "操作成功",
		error: "操作失败",
		exception: "网络异常",
		processing: "处理中...",
		empty: "该项不能为空"
	};

	Notify.confirm = function(msg, callback, cancel_callback, confirmButtonText, cancelButtonText) {
		irequire(["jquery.confirm"], function() {
			$.confirm({
				title: "提示",
				content: msg,
				confirmButtonClass: "btn-primary",
				cancelButtonClass: "btn-default",
				confirmButton: confirmButtonText || "确 定",
				cancelButton: cancelButtonText || "取 消",
				animation: "top",
				confirm: function() {
					if (callback && typeof callback == "function") {
						callback();
					}
				},
				cancel: function() {
					if (cancel_callback && typeof cancel_callback == "function") {
						cancel_callback();
					}
				}
			});
		});
	};

	Notify.alert = function(msg, callback) {
		irequire(["jquery.confirm"], function() {
			$.alert({
				title: "提示",
				content: msg,
				confirmButtonClass: "btn-primary",
				confirmButton: "确 定",
				animation: "top",
				confirm: function() {
					if (callback && typeof callback == "function") {
						callback();
					}
				}
			});
		});
	};

	if($("div.message-box", top.window.document).length == 0) {
		$("body", top.window.document).append('<div class="message-box"></div>');
	}

	var Tip = function(element, options) {
		this.$element = $(element);
		this.$note = $('<span class="msg"></span>');
		this.options = $.extend({}, {type: "success", delay: 3e3, message: ""}, options);
		this.$note.addClass(this.options.type ? "msg-" + this.options.type : "msg-success");
		if (this.options.message) {
			this.$note.html(this.options.message);
		}
		return this;
	};
	Tip.prototype.show = function() {
		this.$element.addClass("in"), this.$element.html(this.$note);
		var autoClose = this.options.autoClose || true;
		if (autoClose) {
			var self = this;
			setTimeout(function() {
				self.close();
			}, this.options.delay || 2e3);
		}
	};
	Tip.prototype.close = function() {
		var self = this;
		self.$element.removeClass("in").transitionEnd(function() {
			self.$element.empty();
			if (self.options.onClosed) {
				self.options.onClosed(self);
			}
		});
		if (self.options.onClose) {
			self.options.onClose(self);
		}
	}
	$.fn.Tip = function(options) {
		return new Tip(this, options);
	}

	window.msgbox = $("div.message-box", top.window.document);

	Notify.success = function(msg, url, onClose, onClosed) {
		Notify.message.success(msg, url, onClose, onClosed);
	};

	Notify.error = function(msg, url, onClose, onClosed) {
		Notify.message.error(msg, url, onClose, onClosed);
	};
	Notify.info = function(msg, url, onClose, onClosed) {
		Notify.message.info(msg, url, onClose, onClosed);
	};

	Notify.message = {
		show: function(options) {
			if(options.url) {
				options.url = options.url.replace(/&amp;/gi, "&");
				options.onClose = function() {
					redirect(options.url);
				};
			}
			if(options.message && options.message.length > 17) {
				Notify.alert(options.message, function() {
					if (options.url) {
						redirect(options.url);
					}
				});
				return;
			}
			notify = window.msgbox.Tip(options);
			notify.show();
		},
		success: function(msg, url, onClose, onClosed) {
			Notify.message.show({
				delay: 2e3,
				type: "success",
				message: msg,
				url: url,
				onClose: onClose,
				onClosed: onClosed
			});
		},
		error: function(msg, url, onClose, onClosed) {
			Notify.message.show({
				delay: 2e3,
				type: "error",
				message: msg,
				url: url,
				onClose: onClose,
				onClosed: onClosed
			});
		},
		info: function(msg, url, onClose, onClosed) {
			Notify.message.show({
				delay: 2e3,
				type: "info",
				message: msg,
				url: url,
				onClose: onClose,
				onClosed: onClosed
			});
		}
	};
	window.Notify = Notify;
});