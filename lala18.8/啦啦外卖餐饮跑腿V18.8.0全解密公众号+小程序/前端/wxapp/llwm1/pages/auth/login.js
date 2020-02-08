var e = getApp();

Page({
    data: {},
    onLoad: function(t) {
        wx.removeStorageSync("timer");
        var s = e.util.getStorageSync("clerkInfo");
        if (s && s.token) e.util.jump2url("/pages/order/index"); else {
            var a = this;
            e.util.request({
                url: "manage/auth/login",
                data: {
                    nosid: 1
                },
                success: function(t) {
                    var s = t.data.message;
                    if (s.errno) return e.util.toast(s.message), !1;
                    a.setData(s.message);
                }
            });
        }
    },
    onSubmit: function(t) {
        var s = t.detail.value;
        s.mobile ? s.password ? e.util.request({
            url: "manage/auth/login",
            method: "POST",
            data: {
                mobile: s.mobile,
                password: s.password,
                nosid: 1,
                formid: t.detail.formId
            },
            success: function(t) {
                var s = t.data.message;
                if (s.errno) return e.util.toast(s.message), !1;
                e.util.setStorageSync("clerkInfo", {
                    token: s.message.clerk.token
                }), 1 == s.message.sids.length ? (e.util.setStorageSync("__sid", s.message.sids[0]), 
                e.util.toast("登录成功", "/pages/order/index", 1e3)) : s.message.sids.length > 1 && e.util.toast("登录成功", "/pages/shop/select", 1e3);
            }
        }) : e.util.toast("请输入密码", "", 1e3) : e.util.toast("请输入手机号", "", 1e3);
    },
    onJsEvent: function(t) {
        e.util.jsEvent(t);
    }
});