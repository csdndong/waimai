var version = +new Date();
var iconfig = {
	path: '../addons/we7_wmall/static/js/',
	alias: {
		'map': '//webapi.amap.com/maps?v=1.4.1&key=550a3bf0cb6d96c3b43d330fb7d86950',
		'jquery': 'components/jquery/jquery-1.11.1.min',
		'jquery.form': 'components/jquery/jquery.form',
		'jquery.extend': 'components/jquery/jquery.extend',
		'jquery.qrcode': 'components/jquery/jquery.qrcode.min',
		'jquery.validate': 'components/jquery/jquery.validate.min',
		'jquery.nestable': 'components/jquery/nestable/jquery.nestable',
		'jquery.contextMenu': 'components/jquery/contextMenu/jquery.contextMenu',
		'jquery.smint': 'components/jquery/jquery.smint',
		'jquery.animateNumber': 'components/jquery/jquery.animateNumber.min',
		'bootstrap': 'components/bootstrap/bootstrap.min',
		'bootstrap.suggest': 'components/bootstrap/bootstrap-suggest.min',
		'bootbox': 'components/bootbox/bootbox.min',
		'select2': 'components/select2/select2.min',
		'jquery.confirm': 'components/jquery/confirm/jquery-confirm',
		'switchery': 'components/switchery/switchery',
		'echarts': 'components/echarts/echarts.min',
		'chart': 'components/chart.min',
		'toast': 'components/jquery/toastr.min',
		'jquery.circliful': 'components/jquery/jquery.circliful.min',
		'laytpl': 'components/jquery/laytpl',
		'tmodtpl': 'components/jquery/tmod',
		'jquery.slimscroll': 'components/jquery/jquery.slimscroll.min',
		'tiny': 'web/tiny',
		'filestyle': 'components/bootstrap/bootstrap-filestyle.min',
		'tagsinput': 'components/tagsinput/bootstrap-tagsinput.min',
		'clipboard': 'components/clipboard.min',
	},
	map: {
		'js': '.js?v=' + version,
		'css': '.css?v=' + version
	},
	cssArr: {
		'jquery.confirm': 'components/jquery/confirm/jquery-confirm',
		'sweet': 'components/sweetalert/sweetalert',
		'select2': 'components/select2/select2,components/select2/select2-bootstrap',
		'jquery.nestable': 'components/jquery/nestable/nestable',
		'jquery.contextMenu': 'components/jquery/contextMenu/jquery.contextMenu',
		'switchery': 'components/switchery/switchery',
		'clockpicker': 'components/clockpicker/clockpicker.min',
		'tagsinput': 'components/tagsinput/bootstrap-tagsinput'
	},
	preload: ['jquery']
}

var irequire = function(arr, callback) {
	var newarr = [];
	$.each(arr, function() {
		var path = iconfig.path;
		var js = this;
		if(iconfig.cssArr[js]) {
			var css = iconfig.cssArr[js].split(',');
			$.each(css, function() {
				newarr.push("css!" + path + this + iconfig.map['css']);
			});
		}
		var jsitem = this;
		if(iconfig.alias[js]) {
			jsitem = iconfig.alias[js];
		}
		if(js == 'map') {
			path = '';
		}
		newarr.push(path + jsitem + iconfig.map['js']);
	});
	require(newarr, callback);
}
