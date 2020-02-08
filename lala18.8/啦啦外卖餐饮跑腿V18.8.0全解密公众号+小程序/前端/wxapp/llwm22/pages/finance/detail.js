var n = getApp();

Page({
    data: {},
    onLoad: function(e) {
        var t = this, o = e.id;
        n.util.request({
            url: "delivery/finance/current/detail",
            data: {
                id: o
            },
            success: function(e) {
                var o = e.data.message;
                if (o.errno) return n.util.toast(o.message), !1;
                t.setData(o.message);
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});