define(['tiny'], function(tiny) {
	var auth = {};
	auth.initLogin = function(forward) {
		$('.button-login').click(function(){
			var $this = $(this);
			if($(this).hasClass('disabled')) {
				return false;
			}
			var mobile = $.trim($('input[name="mobile"]').val());
			if(!mobile) {
				$.toast('请输入手机号');
				return false;
			}
			var reg = /^[01][3456789][0-9]{9}/;
			if(!reg.test(mobile)) {
				$.toast('手机号格式错误');
				return false;
			}
			var password = $.trim($('input[name="password"]').val());
			if(!password) {
				$.toast('请输入密码');
				return false;
			}
			$this.addClass("disabled");
			$.post(tiny.getUrl('wmall/auth/login'), {mobile: mobile, password: password, forward: forward}, function(data){
				var result = $.parseJSON(data);
				if(!result.message.errno) {
					$.toast('登录成功', result.message.message);
				} else {
					$.toast(result.message.message);
					$this.removeClass("disabled");
				}
			});
			return false;
		});
	};

	auth.sendCode = function(product) {
		$('.button-code').click(function(){
			var $this = $(this);
			if($(this).hasClass('disabled')) {
				return false;
			}
			var mobile = $.trim($('input[name="mobile"]').val());
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
			$.post(tiny.getUrl('system/common/code'), {mobile: mobile, product: product, captcha: captcha}, function(data){
				if(data != 'success') {
					$.toast(data);
				} else {
					$this.addClass("disabled");
					var downcount = 60;
					$this.html(downcount + "秒后重新获取");
					var timer = setInterval(function(){
						downcount--;
						if(downcount <= 0){
							clearInterval(timer);
							$this.html("获取验证码");
							$this.removeClass("disabled");
							downcount = 60;
						} else {
							$this.html(downcount + "秒后重新获取");
						}
					}, 1000);
					$.toast('验证码发送成功, 请注意查收');
				}
			});
			return false;
		});
	};

	auth.initRegister = function() {
		auth.sendCode('注册用户');
		$('.button-register').click(function(){
			var $this = $(this);
			if($(this).hasClass('disabled')) {
				return false;
			}
			var mobile = $.trim($('input[name="mobile"]').val());
			if(!mobile) {
				$.toast('请输入手机号');
				return false;
			}
			var reg = /^[01][3456789][0-9]{9}/;
			if(!reg.test(mobile)) {
				$.toast('手机号格式错误');
				return false;
			}
			var code = $.trim($('input[name="code"]').val());
			if(!code) {
				$.toast('请输入短信验证码');
				return false;
			}
			var password = $.trim($('input[name="password"]').val());
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
			var repassword = $.trim($('input[name="repassword"]').val());
			if(!repassword) {
				$.toast('请重复输入密码');
				return false;
			}
			if(password != repassword) {
				$.toast('两次密码输入不一致');
				return false;
			}
			$this.addClass("disabled");
			$.post(tiny.getUrl('wmall/auth/register'), {mobile: mobile, password: password, code: code}, function(data){
				var result = $.parseJSON(data);
				if(!result.message.errno) {
					$.toast('注册成功', result.message.message);
				} else {
					$.toast(result.message.message);
					$this.removeClass("disabled");
				}
			});
			return false;
		});
	}

	auth.initForget = function() {
		auth.sendCode('找回密码');
		$('.button-forget').click(function(){
			var $this = $(this);
			if($(this).hasClass('disabled')) {
				return false;
			}
			var mobile = $.trim($('input[name="mobile"]').val());
			if(!mobile) {
				$.toast('请输入手机号');
				return false;
			}
			var reg = /^[01][3456789][0-9]{9}/;
			if(!reg.test(mobile)) {
				$.toast('手机号格式错误');
				return false;
			}
			var code = $.trim($('input[name="code"]').val());
			if(!code) {
				$.toast('请输入短信验证码');
				return false;
			}
			var password = $.trim($('input[name="password"]').val());
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
			var repassword = $.trim($('input[name="repassword"]').val());
			if(!repassword) {
				$.toast('请重复输入密码');
				return false;
			}
			if(password != repassword) {
				$.toast('两次密码输入不一致');
				return false;
			}
			$this.addClass("disabled");
			$.post(tiny.getUrl('wmall/auth/forget'), {mobile: mobile, password: password, code: code}, function(data){
				var result = $.parseJSON(data);
				if(!result.message.errno) {
					$.toast('设置新密码成功', result.message.message);
				} else {
					$.toast(result.message.message);
					$this.removeClass("disabled");
				}
			});
			return false;
		});
	}
	return auth;
});