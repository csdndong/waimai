var app = getApp();

Page({
    data: {},
    onLoad: function(o) {
        var n = this;
        app.setNavigationBarColor(n);
        var t = decodeURIComponent(o.scene);
        app.getUserInfo(function(o) {
            console.log(o), n.setData({
                userInfo: o,
                order_id: t
            });
        });
    },
    add_market: function(o) {
        var n = this.data.userInfo.id, t = this.data.order_id;
        app.util.request({
            url: "entry/wxapp/GroupVerification",
            cachetime: "0",
            data: {
                order_id: t,
                user_id: n
            },
            success: function(o) {
                console.log(o), "核销成功" == o.data ? wx.showToast({
                    title: "核销成功"
                }) : wx.showToast({
                    title: "核销失败"
                }), setTimeout(function() {
                    wx.reLaunch({
                        url: "../Liar/loginindex"
                    });
                }, 1500);
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