var qqmapsdk, app = getApp(), util = require("../../utils/util.js"), QQMapWX = require("../../utils/qqmap-wx-jssdk.js");

Page({
    data: {
        color: "#34aaff"
    },
    countdown: function(o) {
        var a = this, t = (o || []) - Math.round(new Date().getTime() / 1e3) || [];
        a.setData({
            clock: a.dateformat(t)
        }), t <= 0 ? a.setData({
            clock: !1
        }) : 0 < t && setTimeout(function() {
            t -= 1e3, a.countdown(o);
        }, 1e3);
    },
    dateformat: function(o) {
        var a = Math.floor(o), t = Math.floor(a / 3600 / 24), e = Math.floor(a / 3600 % 24), n = Math.floor(a / 60 % 60), r = Math.floor(a % 60);
        return t < 10 && (t = "0" + t), e < 10 && (e = "0" + e), r < 10 && (r = "0" + r), 
        n < 10 && (n = "0" + n), {
            day: t,
            hr: e,
            min: n,
            sec: r
        };
    },
    onLoad: function(o) {
        app.setNavigationBarColor(this), console.log(o);
        var n = this;
        app.util.request({
            url: "entry/wxapp/OrderInfo",
            cachetime: "0",
            data: {
                order_id: o.oid
            },
            success: function(o) {
                if (console.log(o), "" != o.data.order.pt_info) {
                    var a = JSON.parse(o.data.order.pt_info);
                    o.data.order.pt_info = a;
                }
                if ("" != o.data.order.dd_info) {
                    var t = JSON.parse(o.data.order.dd_info);
                    o.data.order.dd_info = t;
                }
                if ("" != o.data.order.kfw_info) {
                    var e = JSON.parse(o.data.order.kfw_info);
                    o.data.order.kfw_info = e;
                }
                console.log(o.data, a, t, e), n.setData({
                    odinfo: o.data
                });
            }
        });
    },
    lxqs: function(o) {
        var a = o.currentTarget.dataset.tel;
        wx.makePhoneCall({
            phoneNumber: a
        });
    },
    maketel: function(o) {
        var a = o.currentTarget.dataset.tel;
        wx.makePhoneCall({
            phoneNumber: a
        });
    },
    copyText: function(o) {
        var a = o.currentTarget.dataset.text;
        wx.setClipboardData({
            data: a,
            success: function() {
                wx.showToast({
                    title: "已复制"
                });
            }
        });
    },
    location: function() {
        var o = this.data.odinfo.store.coordinates.split(","), a = this.data.odinfo.store;
        console.log(o), wx.openLocation({
            latitude: parseFloat(o[0]),
            longitude: parseFloat(o[1]),
            address: a.address,
            name: a.name
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