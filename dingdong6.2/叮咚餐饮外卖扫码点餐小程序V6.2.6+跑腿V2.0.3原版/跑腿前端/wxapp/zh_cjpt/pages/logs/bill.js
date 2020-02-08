var app = getApp();

Page({
    data: {
        nav: [ "", "", "", "", "", "", "", "", "", "" ]
    },
    onLoad: function(o) {
        var t = this;
        wx.hideShareMenu(), t.NoticeDetails(o.id), app.getSystem(function(o) {
            console.log(o), t.setData({
                getSystem: o,
                color: o.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: o.color
            });
        });
    },
    NoticeDetails: function(o) {
        var t = this;
        app.util.request({
            url: "entry/wxapp/NoticeDetails",
            data: {
                id: o
            },
            success: function(o) {
                console.log(o), t.setData({
                    NoticeDetails: o.data
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