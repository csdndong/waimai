define(['tiny'], function(tiny) {
	var store = {};
	store.initIndex = function() {
		$(document).on('click', '#scanqrcode', function(){
			$.confirm("如果您已经到店,请点击'扫码下单'并扫描桌子上的二维码进行店内下单", function(){
				wx.ready(function(){
					wx.scanQRCode({
						needResult: 0, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
						scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
						success: function (res) {
							var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
						}
					});
				});
			});
		});
	};

	store.initReport = function(config) {
		$('#btnSubmit').click(function(){
			var $this = $(this);
			if($this.hasClass('disabled')) {
				return false;
			}
			var title = $(':radio[name="title"]:checked').val();
			if(!title) {
				$.toast('投诉类型不能为空');
				return false;
			}
			var note = $('textarea[name="note"]').val();
			if(!note) {
				$.toast('投诉内容不能为空');
				return false;
			}
			var mobile = $(':text[name="mobile"]').val();
			var reg = /^[01][3456789][0-9]{9}$/;
			if(!reg.test(mobile)) {
				$.toast("手机号格式错误");
				return false;
			}
			var params = {
				sid: config.sid,
				title: title,
				note: note,
				mobile: mobile,
				thumbs: []
			};
			$('.tpl-image .image-item input[type!="file"]').each(function(){
				var value = $.trim($(this).val());
				if(value) {
					params.thumbs.push(value);
				}
			});
			$this.addClass('disabled');
			$.post(tiny.getUrl('wmall/store/report/post'), params, function(data){
				var result = $.parseJSON(data);
				if(result.message.errno != 0) {
					$this.removeClass('disabled');
					$.toast(result.message.message);
					return false;
				}
				$.toast('投诉成功', tiny.getUrl('wmall/store/index', {sid: config.sid}));
			});
		});
	};

	store.initSettle = function(params) {
		if(params.mobile_verify_status == 1) {
			$('#btn-code').click(function(){
				if($(this).hasClass('disabled')) {
					return false;
				}
				var mobile = $.trim($(':text[name="mobile"]').val());
				if(!mobile) {
					$.toast('请输入手机号');
					return false;
				}
				var reg = /^[01][3456789][0-9]{9}/;
				if(!reg.test(mobile)) {
					$.toast('手机号格式错误');
					return false;
				}
				var captcha = $.trim($('input[name="captcha"]').val());
				if(!captcha) {
					$.toast('请输入图形验证码');
					return false;
				}
				var $this = $(this);
				$this.addClass("disabled");
				var downcount = 60;
				$this.html(downcount + "秒后重新获取");
				var timer = setInterval(function(){
					downcount--;
					if(downcount <= 0){
						clearInterval(timer);
						$this.html("重新获取验证码");
						$this.removeClass("disabled");
						downcount = 60;
					}else{
						$this.html(downcount + "秒后重新获取");
					}
				}, 1000);

				$.post(tiny.getUrl('system/common/code'), {mobile: mobile, product: '商户入驻', captcha: captcha}, function(data){
					if(data != 'success') {
						$.toast(data);
					} else {
						$.toast('验证码发送成功, 请注意查收');
					}
				});
				return false;
			});
		}

		$('#btn-account').click(function(){
			var $this = $(this);
			if($this.hasClass('disabled')) {
				return false;
			}
			var mobile = $.trim($('#form-account :text[name="mobile"]').val());
			var reg = /^[01][3456789][0-9]{9}$/;
			if(!reg.test(mobile)) {
				$.toast("手机号格式错误");
				return false;
			}
			var code = '';
			if(params.mobile_verify_status == 1) {
				code = $.trim($('#form-account :text[name="code"]').val());
				if(!code) {
					$.toast("验证码不能为空");
					return false;
				}
			}
			var password = $.trim($('#form-account :password[name="password"]').val());
			if(!password) {
				$.toast('请输入密码');
				return false;
			} else {
				var length = password.length;
				if(length < 8 || length > 20) {
					$.toast("请输入8-20位密码");
					return false;
				}
				var reg = /[0-9]+[a-zA-Z]+[0-9a-zA-Z]*|[a-zA-Z]+[0-9]+[0-9a-zA-Z]*/;
				if(!reg.test(password)) {
					$.toast("密码必须由数字和字母组合");
					return false;
				}
			}
			var repassword = $.trim($('#form-account :password[name="repassword"]').val());
			if(repassword != password) {
				$.toast('两次密码输入不一致');
				return false;
			}
			var title = $.trim($('#form-account :text[name="title"]').val());
			if(!title) {
				$.toast('姓名不能为空');
				return false;
			}
			var openid = $.trim($(':hidden[name="openid"]').val());
			if(!openid && 0) {
				$.toast("获取微信信息错误");
				return false;
			}
			var agentid = 0;
			if(params.isagent > 0) {
				agentid = $('#form-account :hidden[name="agentid"]').val();
				if(!agentid) {
					$.toast('请选择所属区域');
					return false;
				}
			}
			$this.addClass('disabled');
			var postData = {
				title: title,
				password: password,
				mobile: mobile,
				code: code,
				openid: openid,
				nickname: $.trim($(':hidden[name="nickname"]').val()),
				avatar: $.trim($(':hidden[name="avatar"]').val()),
				agentid: agentid
			};
			$.post(tiny.getUrl('wmall/store/settle/account'), postData, function(data){
				var result = $.parseJSON(data);
				if(result.message.errno != 0) {
					$.toast(result.message.message);
					$this.removeClass('disabled');
					return false;
				} else {
					$.toast('注册成功,跳转中...', tiny.getUrl('wmall/store/settle/store'));
					return false;
				}
			});
		});

		$('#btn-store').click(function(){
			var $this = $(this);
			if($(this).hasClass('disabled')) {
				return false;
			}
			var title = $.trim($('#form-store :text[name="title"]').val());
			if(!title) {
				$.toast('商户名称不能为空');
				return false;
			}
			var address = $.trim($('#form-store :text[name="address"]').val());
			if(!title) {
				$.toast('商户地址不能为空');
				return false;
			}
			var telephone = $.trim($('#form-store :text[name="telephone"]').val());
			if(!title) {
				$.toast('商户电话不能为空');
				return false;
			}
			var content = $.trim($('#form-store textarea[name="content"]').val());
			if(!title) {
				$.toast('商户简介不能为空');
				return false;
			}
			$this.addClass('disabled');
			var params = {
				title: title,
				address: address,
				telephone: telephone,
				content: content
			}
			$.post(tiny.getUrl('wmall/store/settle/store'), params, function(data){
				var result = $.parseJSON(data);
				if(result.message.errno != 0) {
					$this.removeClass('disabled');
					$.toast(result.message.message);
					return false;
				} else {
					$.toast('申请成功,跳转中...', tiny.getUrl('wmall/store/settle/store'));
					return false;
				}
			});
		});
	}
	return store;
});