var t = getApp();

Page({
    data: {
        getCode: !0,
        code: {
            text: "获取验证码",
            downcount: 60
        },
        readed: !1,
        idCardOne: [],
        idCardTwo: []
    },
    onLoad: function(e) {
        var a = this;
        t.util.request({
            url: "delivery/auth/register",
            success: function(e) {
                var r = e.data.message;
                if (r.errno) return t.util.toast(r.message), !1;
                console.log(r.message), a.setData(r.message);
            }
        });
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    },
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
        var r = {
            mobile: e.data.mobile
        };
        t.util.request({
            url: "system/common/code",
            data: r,
            success: function(r) {
                var s = r.data.message;
                if (s.errno) return t.util.toast(s.message), !1;
                a.text = a.downcount + "秒后重新获取", e.setData({
                    code: a,
                    getCode: !1
                });
                var i = setInterval(function() {
                    a.downcount--, a.downcount <= 0 ? (clearInterval(i), a.text = "获取验证码", a.downcount = 60, 
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
    onChangeReaded: function() {
        var t = this;
        t.setData({
            readed: !t.data.readed
        });
    },
    onSubmit: function(e) {
        var a = this, r = e.detail.value;
        if (!r.mobile) return t.util.toast("手机号不能为空"), !1;
        if (!t.util.isMobile(r.mobile)) return t.util.toast("手机号格式错误"), !1;
        if (1 == a.data.config_deliveryer.settle.mobile_verify_status && !r.code) return t.util.toast("请输入短信验证码"), 
        !1;
        if (!r.password) return t.util.toast("密码不能为空"), !1;
        var s = r.password.length;
        if (s < 8 || s > 20) return t.util.toast("请输入8-20位密码"), !1;
        if (!/[0-9]+[a-zA-Z]+[0-9a-zA-Z]*|[a-zA-Z]+[0-9]+[0-9a-zA-Z]*/.test(r.password)) return t.util.toast("密码必须由数字和字母组合"), 
        !1;
        if (!r.repassword) return t.util.toast("请重复输入密码"), !1;
        if (r.password != r.repassword) return t.util.toast("两次密码输入不一致"), !1;
        if (!r.title) return t.util.toast("请输入真实姓名"), !1;
        if (1 == a.data.config_deliveryer.settle.idCard && 1 == !a.data.idCardOne.length) return t.util.toast("手持身份证照片不能为空"), 
        !1;
        if (1 == a.data.config_deliveryer.settle.idCard && 1 == !a.data.idCardTwo.length) return t.util.toast("身份证正面照片不能为空"), 
        !1;
        if (!a.data.readed) return t.util.toast("请确认已阅读入驻申请协议"), !1;
        var i = {
            mobile: r.mobile,
            code: r.code,
            password: r.password,
            repassword: r.repassword,
            title: r.title,
            idCardOne: a.data.idCardOne[0].attachment,
            idCardTwo: a.data.idCardTwo[0].attachment
        };
        t.util.request({
            url: "delivery/auth/register",
            data: i,
            method: "POST",
            success: function(e) {
                0 == e.data.message.errno ? t.util.toast(e.data.message.message, "login", 1e3) : t.util.toast(e.data.message.message);
            }
        });
    }
});