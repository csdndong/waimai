Page({
    data: {},
    onLoad: function(n) {
        wx.hideShareMenu(), this.setData({
            info: wx.getStorageSync("info")
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