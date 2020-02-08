var app = getApp();

Page({
    data: {
        select_code: !0
    },
    onLoad: function(o) {
        var t = this;
        wx.hideShareMenu(), app.getSystem(function(o) {
            console.log(o), t.setData({
                getSystem: o,
                color: o.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: o.color
            });
        });
    },
    se_code: function(o) {
        1 == this.data.select_code ? this.setData({
            select_code: !1
        }) : this.setData({
            select_code: !0
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