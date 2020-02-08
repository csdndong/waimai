var t = getApp();

Page({
    data: {
        getCode: !0,
        code: {
            text: "获取验证码",
            downcount: 60
        }
    },
    onLoad: function(t) {},
    onMobile: function(t) {
        this.setData({
            mobile: t.detail.value
        });
    },
    getCode: function() {
        var e = this, a = e.data.code;
        if (!e.data.getCode) return !1;
        if (!e.data.mobile) return t.util.toast("手机号不能为空"), !1;
        if (!t.util.isMobile(e.data.mobile)) return t.util.toast("手机号格式错误"), !1;
        var o = {
            mobile: e.data.mobile
        };
        t.util.request({
            url: "system/common/code",
            data: o,
            success: function(o) {
                var s = o.data.message;
                if (s.errno) return t.util.toast(s.message), !1;
                a.text = a.downcount + "秒后重新获取", e.setData({
                    code: a,
                    getCode: !1
                });
                var r = setInterval(function() {
                    a.downcount--, a.downcount <= 0 ? (clearInterval(r), a.text = "获取验证码", a.downcount = 60, 
                    e.setData({
                        getCode: !0
                    })) : a.text = a.downcount + "秒后重新获取", e.setData({
                        code: a
                    });
                }, 1e3);
                t.util.toast("验证码发送成功, 请注意查收");
            }
        });
    },
    onSubmit: function(e) {
        var a = e.detail.value;
        if (!a.mobile) return t.util.toast("手机号不能为空"), !1;
        if (!t.util.isMobile(a.mobile)) return t.util.toast("手机号格式错误"), !1;
        if (!a.code) return t.util.toast("请输入短信验证码"), !1;
        if (!a.password) return t.util.toast("密码不能为空"), !1;
        var o = a.password.length;
        if (o < 8 || o > 20) return t.util.toast("请输入8-20位密码"), !1;
        if (!/[0-9]+[a-zA-Z]+[0-9a-zA-Z]*|[a-zA-Z]+[0-9]+[0-9a-zA-Z]*/.test(a.password)) return t.util.toast("密码必须由数字和字母组合"), 
        !1;
        if (!a.repassword) return t.util.toast("请重复输入密码"), !1;
        if (a.password != a.repassword) return t.util.toast("两次密码输入不一致"), !1;
        var s = {
            mobile: a.mobile,
            code: a.code,
            password: a.password,
            repassword: a.repassword
        };
        t.util.request({
            url: "delivery/auth/forget",
            data: s,
            success: function(e) {
                0 == e.data.message.errno ? t.util.toast(e.data.message.message, "login", 1e3) : t.util.toast(e.data.message.message);
            }
        });
    }
});