var app = getApp();

Page({
    data: {},
    onLoad: function(n) {
        app.setNavigationBarColor(this);
        var o = this;
        app.util.request({
            url: "entry/wxapp/CheckRetail",
            cachetime: "0",
            success: function(n) {
                console.log(n), o.setData({
                    fxset: n.data,
                    url: wx.getStorageSync("imglink")
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