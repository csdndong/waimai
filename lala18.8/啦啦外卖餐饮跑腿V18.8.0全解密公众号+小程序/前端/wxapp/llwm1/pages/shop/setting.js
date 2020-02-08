var e = getApp();

Page({
    data: {},
    onLoad: function() {},
    onChangeSwitch: function(t) {
        var n = t.detail.value ? 1 : 0, a = t.currentTarget.dataset.type;
        e.util.request({
            url: "manage/shop/index/status",
            method: "POST",
            data: {
                type: a,
                value: n
            },
            success: function(t) {
                var n = t.data.message;
                if (e.util.toast(n.message), n.errno) return !1;
            }
        });
    },
    onLoginout: function() {
        wx.showModal({
            title: "",
            content: "确定退出当前登录吗？",
            success: function(t) {
                t.confirm ? e.util.request({
                    url: "manage/auth/loginout",
                    method: "POST",
                    success: function(t) {
                        var n = t.data.message;
                        if (n.errno) return e.util.toast(n.message), !1;
                        wx.removeStorageSync("clerkInfo"), wx.removeStorageSync("__sid"), e.util.toast(n.message, "/pages/auth/login", 1e3);
                    }
                }) : t.cancel;
            }
        });
    },
    onJsEvent: function(t) {
        e.util.jsEvent(t);
    },
    onShow: function() {
        var t = this;
        e.util.request({
            url: "manage/shop/index",
            success: function(n) {
                var a = n.data.message;
                if (a.errno) return e.util.toast(a.message), !1;
                t.setData(a.message);
            }
        });
    },
    onPullDownRefresh: function() {
        this.onShow(), wx.stopPullDownRefresh();
    }
});