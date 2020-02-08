var app = getApp();

Page({
    data: {
        color: "#459cf9"
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this);
        var a = this, n = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/MyCommission",
            cachetime: "0",
            data: {
                user_id: n
            },
            success: function(t) {
                console.log(t.data), a.setData({
                    yjdata: t.data
                });
            }
        });
    },
    detaulted: function(t) {
        wx.navigateTo({
            url: "detaulted"
        });
    },
    detault: function(t) {
        wx.navigateTo({
            url: "detault"
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});