var app = getApp();

Page({
    data: {},
    onLoad: function(n) {
        this.info(n.id), wx.hideShareMenu();
    },
    info: function(n) {
        var t = this;
        app.util.request({
            url: "entry/wxapp/GetTxDetails",
            data: {
                tx_id: n
            },
            success: function(n) {
                console.log(n), n.data.time = app.ormatDate(n.data.time), t.setData({
                    info: n.data
                });
            }
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