/*   time:2019-07-18 01:03:18*/
var app = getApp();
Page({
    data: {
        jrdd: "0",
        jrcj: "0"
    },
    onLoad: function(t) {
        var a = this,
            n = wx.getStorageSync("sjdsjid");
        console.log(n, wx.getStorageSync("system")), this.setData({
            wm_name: wx.getStorageSync("system").wm_name || "外卖",
            dc_name: wx.getStorageSync("system").dc_name || "店内"
        }), app.setNavigationBarColor(this), app.sjdpageOnLoad(this), app.util.request({
            url: "entry/wxapp/StoreStatistics",
            cachetime: "0",
            data: {
                store_id: n
            },
            success: function(t) {
                console.log(t.data), a.setData({
                    wmdd: t.data
                })
            }
        })
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});