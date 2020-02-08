var n = getApp();

Page({
    data: {},
    onLoad: function(e) {
        var o = this;
        n.util.request({
            url: "manage/finance/index",
            success: function(e) {
                var t = e.data.message;
                if (t.errno) return n.util.toast(t.message), !1;
                o.setData(t.message);
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