var t = getApp();

Page({
    data: {
        cid: 0
    },
    onLoad: function(n) {
        var a = this;
        a.data.options = n, t.util.request({
            url: "manage/order/table/index",
            data: {
                cid: n.cid || 0
            },
            success: function(n) {
                var o = n.data.message;
                if (o.errno) return t.util.toast(o.message), !1;
                a.setData(o.message), a.data.options.cid && a.setData({
                    cid: a.data.options.cid
                });
            }
        });
    },
    onChangeStatus: function(t) {
        var n = t.currentTarget.dataset.cid;
        this.setData({
            cid: n
        }), this.onLoad({
            cid: n
        });
    },
    onJsEvent: function(n) {
        t.util.jsEvent(n);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    },
    onShareAppMessage: function() {}
});