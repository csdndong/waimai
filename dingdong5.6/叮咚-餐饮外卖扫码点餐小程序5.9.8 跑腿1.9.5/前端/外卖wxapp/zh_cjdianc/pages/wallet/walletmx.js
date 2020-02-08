var app = getApp();

Page({
    data: {},
    onLoad: function(n) {
        wx.hideShareMenu({}), app.setNavigationBarColor(this);
        var t = this, o = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/Qbmx",
            cachetime: "0",
            data: {
                user_id: o
            },
            success: function(n) {
                console.log(n), t.setData({
                    score: n.data
                });
            }
        });
    },
    tzjfsc: function() {
        wx.redirectTo({
            url: "../integral/integral"
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