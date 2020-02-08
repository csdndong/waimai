var t = getApp();

Page({
    data: {},
    onLoad: function(e) {
        var n = this, a = e.id;
        n.data.options = e, t.util.request({
            url: "delivery/order/takeout/detail",
            data: {
                id: a
            },
            showLoading: !1,
            success: function(e) {
                var a = e.data.message;
                if (a.errno) return t.util.toast(a.message, "", 1e3), !1;
                n.setData(a.message);
            }
        });
    },
    onChangeOrderStatus: function(e) {
        var n = this, a = e.currentTarget.dataset, o = a.type;
        if ("delivery_transfer" == o || "direct_transfer" == o || "delivery_cancel" == o) return wx.navigateTo({
            url: "./reason?type=" + o + "&id=" + a.id + "&status=" + a.status
        }), !1;
        wx.showModal({
            title: "系统提示",
            content: a.confirm,
            success: function(e) {
                e.confirm ? t.util.request({
                    url: "delivery/order/takeout/status",
                    data: a,
                    success: function(e) {
                        var a = e.data.message;
                        t.util.toast(a.message, "", 1e3), a.errno || n.onPullDownRefresh();
                    }
                }) : e.cancel;
            }
        });
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        var t = this, e = {
            id: t.data.options.id
        };
        t.onLoad(e), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});