Page({
    data: {
        color: "#459cf9"
    },
    onLoad: function(n) {
        wx.hideShareMenu();
    },
    detaulted: function(n) {
        wx.navigateTo({
            url: "detaulted"
        });
    },
    detault: function(n) {
        wx.navigateTo({
            url: "detault"
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