var app = getApp();

Page({
    data: {},
    onLoad: function(n) {
        app.setNavigationBarColor(this);
        var t = this;
        app.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(n) {
                t.setData({
                    system: n.data
                });
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