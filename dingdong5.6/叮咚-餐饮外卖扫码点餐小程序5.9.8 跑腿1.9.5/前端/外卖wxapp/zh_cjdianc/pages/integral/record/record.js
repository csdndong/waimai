var app = getApp();

Page({
    data: {},
    onLoad: function(n) {
        wx.hideShareMenu({}), app.setNavigationBarColor(this);
        var o = this, t = wx.getStorageSync("users").id;
        wx.getStorageSync("url");
        app.util.request({
            url: "entry/wxapp/Dhmx",
            cachetime: "0",
            data: {
                user_id: t
            },
            success: function(n) {
                console.log(n), o.setData({
                    score: n.data,
                    url: ""
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