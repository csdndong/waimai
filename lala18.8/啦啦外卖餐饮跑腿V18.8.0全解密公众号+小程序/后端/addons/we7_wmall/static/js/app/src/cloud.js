define(['tiny'], function(tiny) {
	var cloudApp = {
		relation: {}
	};
	cloudApp.init = function(params) {
		cloudApp.relation = params.relation;
		cloudApp.checkconnect();
		cloudApp.initAjpush();
		cloudApp.checkSmartUpdate();
	};

	cloudApp.checkSmartUpdate = function() {
		api.addEventListener({
			name:'smartupdatefinish'
		}, function(ret, err){
			api.rebootApp();
		});
	};

	cloudApp.checkconnect = function() {
		api.addEventListener({
			name:'offline'
		}, function(ret, err){
			api.openFrame({
				name: 'frm_connect',
				url: 'widget://html/connect.html',
				bounces: true,
				rect: {
					x: 0,
					y: 0,
					w: 'auto',
					h: 'auto'
				}
			});
			return false;
		});

		if(api.connectType == 'none') {
			api.openFrame({
				name: 'frm_connect',
				url: 'widget://html/connect.html',
				bounces: true,
				rect: {
					x: 0,
					y: 0,
					w: 'auto',
					h: 'auto'
				}
			});
			return false;
		}

		api.addEventListener({
			name:'online'
		}, function(ret, err){
			api.closeFrame({
				name: 'frm_connect'
			});
			var url_cn = api.igetPrefs('url_cn');
			if(url_cn) {
				window.location.href = url_cn;
			}
		});
	};

	cloudApp.netAudioPlay = function(resource, times) {
		var netAudio = api.require('netAudio');
		if(times > 0) {
			netAudio.play({
				path: resource
			}, function(ret) {
				if(ret.complete) {
					times--;
					cloudApp.netAudioPlay(resource, times);
				}
			});
		}
		return true;
	};

	cloudApp.speechRecognizerRead = function(resource, times) {
		var speechRecognizer = api.require('speechRecognizer');
		if(times > 0) {
			speechRecognizer.read({
				readStr: resource,
				speed: 60,
				volume: 100,
				voice: 'xiaoyan',
				rate: 16000
			}, function(ret, err) {
				if(ret.status) {
					if(ret.speakProgress == 100) {
						times--;
						cloudApp.speechRecognizerRead(resource, times);
					}
				}
			});
		}
		return true;
	};

	cloudApp.play = function(notify_type, resource, text) {
		var prefs = api.igetPrefs('phonic');
		if(prefs.vibrate) {
			api.notification({
				vibrate: [3000, 3000]
			});
		}
		if(prefs.voice) {
			var times = prefs[notify_type]['times'];
			if(times == 'loop') {
				times = 100;
			} else {
				times = 3;
			}
			var voice_type = prefs['voice_type'];
			if(voice_type == 1) {
				cloudApp.speechRecognizerRead(text, times);
			} else {
				cloudApp.netAudioPlay(resource, times);
			}
		}
		return true;
	}

	cloudApp.initAjpush = function(){
		var ajpush = api.require('ajpush');
		ajpush.setBadge({
			badge:0
		});
		ajpush.init(function(ret) {
			if(ret && ret.status){}
		});
		//同步推送标签和别名
		var push_code = api.igetPrefs('push_code');
		if(cloudApp.relation && cloudApp.relation.code && (!push_code || (push_code && push_code != cloudApp.relation.code))) {
			var bindParams = {};
			if(cloudApp.relation.alias) {
				bindParams['alias'] = cloudApp.relation.alias;
			}
			if(cloudApp.relation.tags) {
				bindParams['tags'] = cloudApp.relation.tags;
			}
			ajpush.bindAliasAndTags(bindParams, function(ret, err) {
				var statusCode = ret.statusCode;
				if(statusCode == 0) {
					api.isetPrefs('push_code', cloudApp.relation.code);
				} else {
					alert("同步推送信息出错：" + statusCode);
				}
			});
		}

		ajpush.setListener(function(ret) {
			var extra = ret.extra;
			try {
				extra = JSON.parse(ret.extra);
			} catch(e) {}
			if(api.systemType == 'ios') {
				var ajpush = api.require('ajpush');
				ajpush.setBadge({
					badge:0
				});
			}
			cloudApp.play(extra.notify_type, extra.resource, extra.voice_text);
		});

		api.addEventListener({
			name: 'appintent'
		}, function(ret, err) {
			if(ret && ret.appParam.ajpush) {
				var ajpush = ret.appParam.ajpush;
				var extra = JSON.parse(ajpush.extra);
				location.href = extra.url;
			}
		});

		api.addEventListener({
			name: 'noticeclicked'
		}, function(ret, err) {
			if(ret && ret.value) {
				var ajpush = api.require('ajpush');
				ajpush.setBadge({
					badge:0
				});
				var ajpush = ret.value;
				var extra = ajpush.extra;
				location.href = extra.url;
			}
		});
	};
	window.cloudApp = cloudApp;
	return cloudApp;
});


