define(['jquery'], function($) {
	var $table = $('.js-table'),
		$checkboxs = $('tbody tr td:first-child [type="checkbox"]', $table),
		$form = $table.closest('form'),
		$bottom_bar = $form.find('.js-bar'),
		$batch = $form.find('.js-batch');

	if($table.length > 0) {
		$(document).on("change", '.js-table thead th:first [type="checkbox"]', function(e) {
			e && e.preventDefault();
			var $table = $(this).closest("table"),
				checked = $(this).prop("checked");
			$('tbody tr td:first-child [type="checkbox"]', $table).prop("checked", checked);
			if(checked) {
				$bottom_bar.show();
			} else {
				$bottom_bar.hide();
			}
		});

		$(document).on("change", '.js-table tbody td:first-child [type="checkbox"]', function(e) {
			e && e.preventDefault();
			var $table = $(this).closest("table"),
				checked = $(this).prop("checked"),
				$checked_all = $('tbody tr td:first-child [type="checkbox"]:checked', $table);
			$('thead th:first [type="checkbox"]', $table).prop("checked", checked && $checked_all.length == $checkboxs.length);
			if($checked_all.length > 0) {
				$bottom_bar.show();
			} else {
				$bottom_bar.hide();
			}
		});

		var get_selecteds = function() {
			var $selected_checkboxs = $('tbody tr td:first-child [type="checkbox"]:checked', $table);
			selecteds = $selected_checkboxs.map(function() {
				return $(this).val();
			}).get();
			return selecteds;
		};

		$batch.on("click", function(e) {
			e.preventDefault();
			var $this = $(this),
				href = $this.attr("href") || $this.data("href") || $this.data("url"),
				html = $this.val() || $this.html(),
				type = $this.data("batch"),
				redirect = $this.data("redirect"),

				data = $this.data("set")
			button_type = $this.val() ? "input" : "button";
			$this.attr("disabled", "disabled");
			var chks = $('tbody tr td:first-child [type="checkbox"]:checked', $table);
			var selecteds = get_selecteds();
			if(selecteds.length <= 0) {
				Notify.info('请先选择要操作的数据');
				$this.removeAttr("disabled", "disabled");
				return false;
			}
			var submit = function() {
				button_type == "button" ? $this.html('<i class="fa fa-spinner fa-spin"></i> ' + Notify.lang.processing) : $this.val(Notify.lang.processing);
				if(type != 'modal') {
					$.post(href, {
						id: selecteds
					}).done(function(result) {
						var result = $.parseJSON(result);
						var errno = result.message.errno,
							url = result.message.url,
							message = result.message.message;
						if(!errno) {
							if($this.data("batch") == "remove") {
								var deferred = $.Deferred(),
									removeHandler = function(def) {
										var num = 0;
										return chks.parents("tr").fadeOut(function() {
											$(this).remove();
											num++;
											chks.length == num && def.resolve();
										}), def;
									};
								$.when(removeHandler(deferred)).done(function() {
									if(!$("table tbody tr", $table).length || redirect == 'refresh') {
										window.location.reload();
									}
								});
							} else {
								Notify.success(message || Notify.lang.success, url);
							}
							button_type == "button" ? $this.html(html) : $this.val(html);
						} else {
							button_type == "button" ? $this.html(html) : $this.val(html);
							Notify.error(message || Notify.lang.error);
						}
					}).fail(function() {
						button_type == "button" ? $this.html(html) : $this.val(html);
						Notify.error(Notify.lang.exception);
					});
				} else {
					$("#js-modal").remove();
					$.ajax(href, {
						type: "get",
						dataType: "html",
						cache: false,
						data: {id: selecteds}
					}).done(function(result) {
						if(result.substr(0, 10) == '{"message"') {
							var json = eval("(" + result + ")");
							var errno = json.message.errno, message = json.message.message;
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
				}
			};
			if($this.data("confirm")) {
				Notify.confirm($this.data("confirm"), submit, function() {
					$this.removeAttr("disabled", "disabled");
				});
			} else {
				submit();
			}
		});

		$(document).on("click", '.table-responsive .js-remove', function(e) {
			e.preventDefault();
			var obj = $(this),
				url = obj.attr("href") || obj.data("href") || obj.data("url"),
				confirm = obj.data("confirm");
			var submit = function() {
				obj.html('<i class="fa fa-spinner fa-spin"></i> ' + Notify.lang.processing);
				$.post(url).done(function(result) {
					var result = $.parseJSON(result);
					var errno = result.message.errno,
						url = result.message.url,
						message = result.message.message;
					if(!errno) {
						var tr = obj.parents("tr");
						if(tr.length) {
							tr.fadeOut(function() {
								tr.remove();
								if($table.find(".js-remove").length == 0) {
									window.location.reload();
								}
							});
						} else {
							Notify.success('操作成功', location.href);
						}
					} else {
						obj.button("reset");
						Notify.error(message || Notify.lang.error, url);
					}
				}).fail(function() {
					obj.button("reset");
					Notify.error(Notify.lang.exception);
				});
			};
			if (confirm) {
				Notify.confirm(confirm, submit, function() {
					obj.removeAttr("disabled", "disabled");
				});
			} else {
				submit();
			}
		});
		$('#page-loading').remove();
	}
});