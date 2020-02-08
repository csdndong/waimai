var e = getApp();

Page({
    data: {},
    onLoad: function(t) {
        wx.removeStorageSync("timer");
        var s = wx.getStorageSync("deliveryerInfo");
        if (s && s.token) e.util.jump2url("/pages/order/list"); else {
            var a = this;
            e.util.request({
                url: "delivery/auth/login",
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
            url: "delivery/auth/login",
            method: "POST",
            data: {
                mobile: s.mobile,
                password: s.password
            },
            success: function(t) {
                var s = t.data.message;
                if (s.errno) return e.util.toast(s.message), !1;
                wx.setStorageSync("deliveryerInfo", {
                    token: s.message.deliveryer.token
                }), e.util.toast("登录成功", "/pages/order/list", 1e3);
            }
        }) : e.util.toast("请输入密码", "", 1e3) : e.util.toast("请输入手机号", "", 1e3);
    },
    onJsEvent: function(t) {
        e.util.jsEvent(t);
    }
});