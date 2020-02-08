var app = getApp();

Page({
    data: {},
    onLoad: function(t) {
        wx.hideShareMenu({}), app.setNavigationBarColor(this);
        var n = this, e = wx.getStorageSync("users").id;
        console.log(e), app.util.request({
            url: "entry/wxapp/Jfmx",
            cachetime: "0",
            data: {
                user_id: e
            },
            success: function(t) {
                console.log(t);
                var e = t.data;
                n.setData({
                    score: e
                });
            }
        }), app.util.request({
            url: "entry/wxapp/UserInfo",
            cachetime: "0",
            data: {
                user_id: e
            },
            success: function(t) {
                console.log(t), n.setData({
                    integral: t.data.total_score
                });
            }
        });
    },
    tzjfsc: function() {
        wx.redirectTo({
            url: "integral"
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