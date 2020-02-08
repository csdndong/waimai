var t = getApp();

Page({
    data: {},
    onLoad: function(e) {
        var n = this;
        t.util.request({
            url: "delivery/member/mine/index",
            success: function(e) {
                var o = e.data.message;
                if (o.errno) return t.util.toast(o.message), !1;
                n.setData(o.message);
            }
        });
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    },
    onLogout: function() {
        wx.showModal({
            title: "",
            content: "确定退出吗？",
            success: function(e) {
                e.confirm && (t.util.removeStorageSync("deliveryerInfo"), t.util.followLocation(!0), 
                t.util.toast("退出登录成功", "/pages/auth/login", 1e3));
            }
        });
    }
});