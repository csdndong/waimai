Page({
    data: {
        ac_index: 0
    },
    onLoad: function(n) {
        wx.hideShareMenu();
    },
    one: function(n) {
        this.setData({
            ac_index: 0
        });
    },
    two: function(n) {
        this.setData({
            ac_index: 1
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