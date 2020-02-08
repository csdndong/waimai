/*   time:2019-07-18 01:03:05*/
var app = getApp();
Page({
    data: {},
    onLoad: function(o) {
        wx.setNavigationBarTitle({
            title: "已抢购用户"
        }), console.log(o), app.setNavigationBarColor(this);
        var n = this;
        app.util.request({
            url: "entry/wxapp/QgPeople",
            cachetime: "0",
            data: {
                good_id: o.goodid
            },
            success: function(o) {
                console.log(o.data), n.setData({
                    QgPeople: o.data
                })
            }
        })
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});