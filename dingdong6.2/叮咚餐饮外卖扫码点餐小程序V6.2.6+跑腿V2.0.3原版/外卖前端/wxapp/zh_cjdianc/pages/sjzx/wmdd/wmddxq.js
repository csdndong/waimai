/*   time:2019-07-18 01:03:18*/
var qqmapsdk, app = getApp(),
    util = require("../../../utils/util.js"),
    QQMapWX = require("../../../utils/qqmap-wx-jssdk.js");
Page({
    data: {
        color: "#34aaff"
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this), console.log(t);
        var a = this;
        app.util.request({
            url: "entry/wxapp/OrderInfo",
            cachetime: "0",
            data: {
                order_id: t.oid
            },
            success: function(t) {
                console.log(t.data), a.setData({
                    odinfo: t.data
                })
            }
        })
    },
    maketel: function(t) {
        var a = t.currentTarget.dataset.tel;
        wx.makePhoneCall({
            phoneNumber: a
        })
    },
    copyText: function(t) {
        var a = t.currentTarget.dataset.text;
        wx.setClipboardData({
            data: a,
            success: function() {
                wx.showToast({
                    title: "已复制"
                })
            }
        })
    },
    location: function(t) {
        var a = t.currentTarget.dataset.lat,
            e = t.currentTarget.dataset.lng,
            o = t.currentTarget.dataset.address;
        console.log(a, e), wx.openLocation({
            latitude: parseFloat(a),
            longitude: parseFloat(e),
            address: o,
            name: "位置"
        })
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});