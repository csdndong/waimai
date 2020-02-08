define(['tiny'], function(tiny) {
	if($(".api-checkbox").length > 0) {
		$(document).off('click', '.api-checkbox');
		$(document).on('click', '.api-checkbox', function(){
			var obj = $(this), confirm = obj.data("confirm"), value = obj.prop('checked') ? (obj.attr('value') || 1) : 0, name = obj.attr('name');
			handler = function() {
				$.showIndicator();
				api.isetPrefs(name, value);
				$.hideIndicator();
			};
			if(confirm) {
				$.confirm(confirm, handler);
			} else {
				handler();
			}
		});

		$.showIndicator();
		$(".api-checkbox").each(function() {
			var obj = $(this), name = obj.attr('name'), value = obj.attr('value') || 1;
			var prefs_value = api.igetPrefs(name);
			if(prefs_value == value) {
				obj.prop('checked', true);
			}
		});
		$.hideIndicator();
	}

	$(document).off('click', '.api-radio');
	$(document).on('click', '.api-radio', function(){
		var obj = $(this), confirm = obj.data("confirm"), value = obj.prop('checked') ? (obj.attr('value') || 1) : 0, name = obj.attr('name');
		handler = function() {
			$.showIndicator();
			api.isetPrefs(name, value);
			$.hideIndicator();
		};
		if(confirm) {
			$.confirm(confirm, handler);
		} else {
			handler();
		}
		phonic.init();
	});

	if($(".api-radio").length > 0) {
		$(".api-radio").each(function() {
			var obj = $(this), name = obj.attr('name'), value = obj.attr('value') || 1;
			var prefs_value = api.igetPrefs(name);
			if(prefs_value == value) {
				obj.prop('checked', true);
			}
		});
	}

	var phonic = {
		new: {
			times: {
				'three': '播放3遍',
				'loop': '循环播放'
			}
		},
		remind: {
			times: {
				'three': '播放3遍',
				'loop': '循环播放'
			}
		},
		refund: {
			times: {
				'three': '播放3遍',
				'loop': '循环播放'
			}
		},
		init: function() {
			var types = ['new', 'remind', 'refund'];
			var prefs = api.igetPrefs('phonic');
			$.each(types, function(k, v){
				var times = prefs[v]['times'];
				$('.phonic-times-' + v +' .item-after').html(phonic[v]['times'][times]);
				$('.phonic-times-' + v).data('value', times);
			});
		}
	};
	phonic.init();
});
