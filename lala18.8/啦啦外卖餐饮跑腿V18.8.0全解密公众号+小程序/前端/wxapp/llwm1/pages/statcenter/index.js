var n = getApp();

Page({
    data: {},
    onLoad: function() {
        var t = this;
        n.util.request({
            url: "manage/statcenter/index",
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