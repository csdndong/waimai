var app = getApp();

Page({
    data: {},
    onLoad: function(o) {
        var n = this;
        wx.hideShareMenu(), app.getSystem(function(o) {
            console.log(o), n.setData({
                getSystem: o,
                color: o.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: o.color
            });
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