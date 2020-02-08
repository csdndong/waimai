var n = getApp();

Page({
    data: {},
    onLoad: function() {
        var o = this;
        n.util.request({
            url: "manage/shop/index/index",
            success: function(t) {
                var e = t.data.message;
                if (e.errno) return n.util.toast(e.message), !1;
                o.setData(e.message);
            }
        });
    },
    onJsEvent: function(o) {
        n.util.jsEvent(o);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});