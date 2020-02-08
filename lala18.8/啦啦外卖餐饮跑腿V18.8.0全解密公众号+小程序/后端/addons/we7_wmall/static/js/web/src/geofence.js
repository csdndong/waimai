define(["jquery"], function($) {
	var geofence = {
		polygons: {},
		colors: {
			1: {
				strokeColor: '#4589ef',
				fillColor: '#71a3ef',
			},
			2: {
				strokeColor: '#1ebd4f',
				fillColor: '#1ecb54',
			},
			3: {
				strokeColor: '#06954b',
				fillColor: '#41ad73',
			},
			4: {
				strokeColor: '#9a6a38',
				fillColor: '#b38f66',
			},
			5: {
				strokeColor: '#6b543c',
				fillColor: '#917e6a',
			},
			6: {
				strokeColor: '#4589ef',
				fillColor: '#71a3ef',
			},
			7: {
				strokeColor: '#1ebd4f',
				fillColor: '#1ecb54',
			},
			8: {
				strokeColor: '#06954b',
				fillColor: '#41ad73',
			},
			9: {
				strokeColor: '#9a6a38',
				fillColor: '#b38f66',
			},
			10: {
				strokeColor: '#6b543c',
				fillColor: '#917e6a',
			}
		}
	};
	geofence.init = function(params){
		var map = new AMap.Map('allmap', {
			resizeEnable: true,
			zoom: 14,
			doubleClickZoom: false,
			center: [params.store.location_y, params.store.location_x]
		});
		map.addControl(new AMap.ToolBar());
		window.map = map;
		window.tmodtpl = params.tmodtpl;
		geofence.isChange = params.isChange;
		geofence.store = params.store;
		geofence.areas = params.areas;
		if(!geofence.areas || $.isArray(geofence.areas)) {
			geofence.areas = {};
		}
		geofence.areasOriginal = params.areas;
		geofence.tplArea();
		geofence.tplEditor();
		geofence.initDom();
	};

	geofence.tplArea = function() {
		var html = tmodtpl("tpl-area", geofence);
		$(".geofence-container").html(html);
	};

	geofence.markerStore = function() {
		if(geofence.store.location_y && geofence.store.location_x) {
			var marker = new AMap.Marker({
				position: [geofence.store.location_y, geofence.store.location_x],
				offset: new AMap.Pixel(-10, -36),
				content: '<div class="marker-start-head-route"></div>'
			});
			marker.setMap(map);
		}
	};

	geofence.tplEditor = function() {
		map.clearMap();
		geofence.markerStore();
		$.each(geofence.areas, function(k, v){
			var color = geofence.colors[v.colorType];
			var polygon = new AMap.Polygon({
				path: v.path,//设置多边形边界路径
				strokeColor: color.strokeColor, //线颜色
				strokeOpacity: 0.9, //线透明度
				strokeWeight: 3,    //线宽
				fillColor: color.fillColor, //填充色
				fillOpacity: 0.8//填充透明度
			});
			geofence.polygons[k] = polygon;
			polygon.setMap(map);
		});
		$(':hidden[name="areas"]').val((encodeURI(JSON.stringify((geofence.areas)))));
	};

	geofence.initDom = function() {
		$(document).off('click', '#area-add');
		$(document).on('click', '#area-add', function() {
			if(geofence.isActive == 1) {
				return false;
			}
			var itemid = geofence.getId('M', 0);
			var num = geofence.length(geofence.areas);
			if(num >= 10) {
				Notify.info("最多可添加10个！");
				return;
			}
			var index = geofence.getColor();
			var color = geofence.colors[index];
			geofence.isActive = 1;
			geofence.areas[itemid] = {
				delivery_price: 0,
				delivery_free_price: 0,
				send_price: 0,
				strokeColor: color.strokeColor,
				fillColor: color.fillColor,
				isActive: 1,
				isAdd: 1,
				path: [],
				colorType: index
			};
			var mouseTool = new AMap.MouseTool(map);
			var polygon = mouseTool.polygon();
			AMap.event.addListener(mouseTool, 'draw', function callback(e) {
				mouseTool.close();
				var eObject = e.obj;
				var polygonEditor= new AMap.PolyEditor(map, eObject);
				polygonEditor.open();
				geofence.areas[itemid].path = eObject.getPath();
				geofence.tplArea();
				geofence.tplEditor();
			});
			geofence.tplArea();
			geofence.tplEditor();
		});

		$(document).off('click', '.area-item .editor-area-item');
		$(document).on('click', '.area-item .editor-area-item', function() {
			var id = $(this).data('id');
			if(!id || !geofence.polygons[id]) {
				return false;
			}
			geofence.isActive = 1;
			var area = geofence.areas[id];
			area['isActive'] = 1;
			area['isAdd'] = 0;

			var polygonEditor= new AMap.PolyEditor(map, geofence.polygons[id]);
			polygonEditor.open();
			geofence.tplArea();
		});

		$(document).off('click', '.area-item .btn-reset');
		$(document).on('click', '.area-item .btn-reset', function() {
			var id = $(this).data('id');
			Notify.confirm("退出编辑后，此次修改将不会生效，是否确定退出？", function(){
				geofence.isActive = 0;
				var area = geofence.areas[id];
				area.isActive = 0;
				if(area.isAdd == 1) {
					delete(geofence.areas[id]);
				} else {
					geofence.areas[id] = geofence.areasOriginal[id];
				}
				geofence.tplArea();
				geofence.tplEditor();
			});
		});

		$(document).off('click', '.area-item .btn-delete');
		$(document).on('click', '.area-item .btn-delete', function() {
			var id = $(this).data('id');
			Notify.confirm("确定删除此区域吗？", function(){
				geofence.isActive = 0;
				delete(geofence.areas[id]);
				geofence.tplArea();
				geofence.tplEditor();
			});
		});

		$(document).off('click', '.area-item .btn-save');
		$(document).on('click', '.area-item .btn-save', function() {
			var id = $(this).data('id');
			Notify.confirm("确定对该区域进行修改？", function(){
				var polygon = geofence.polygons[id];
				geofence.areas[id].path = polygon.getPath();
				geofence.areas[id].isActive = 0;
				geofence.isActive = 0;
				geofence.tplArea();
				geofence.tplEditor();
			});
		});

		$(document).on('input propertychange change', '#area-container .diy-bind', function() {
			var _this = $(this);
			var bind = _this.data("bind");
			var bindchild = _this.data('bind-child');
			var bindparent = _this.data('bind-parent');
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
					geofence.areas[bindchild][bindparent][bind] = value;
				} else {
					geofence.areas[bindchild][bind] = value;
				}
			} else {
				geofence.areas[bind] = value;
			}
		});
	};

	geofence.getColor = function() {
		var arr = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
		for(var i in geofence.areas) {
			var index = $.inArray(geofence.areas[i]['colorType'], arr);
			if(index != -1) {
				arr.splice(index, 1);
			}
		}
		return arr.shift();
	};

	geofence.length = function(json) {
		if(typeof(json) === 'undefined') {
			return 0;
		}
		var len = 0;
		for(var i in json) {
			len++;
		}
		return len;
	};

	geofence.getId = function(S, N) {
		var date = +new Date();
		var id = S + (date + N);
		return id;
	};
	return geofence;
});