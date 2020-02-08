Page({
    data: {
        color: "#459cf9",
        select_code: !0
    },
    onLoad: function(o) {
        wx.hideShareMenu();
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