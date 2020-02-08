Page({
    data: {
        color: "#459cf9"
    },
    onLoad: function(n) {
        wx.hideShareMenu();
    },
    distribution: function(n) {
        wx.navigateTo({
            url: "distribution"
        });
    },
    downline: function(n) {
        wx.navigateTo({
            url: "downline"
        });
    },
    ranking: function(n) {
        wx.navigateTo({
            url: "ranking"
        });
    },
    invation: function(n) {
        wx.navigateTo({
            url: "index"
        });
    },
    tixian: function(n) {
        wx.navigateTo({
            url: "tixian"
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