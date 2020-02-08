var app = getApp(), util = require("../../utils/util.js");

Page({
    data: {},
    sxsj: function() {
        this.reLoad();
    },
    reLoad: function() {
        var i = this, t = this.data.storeid, e = this.data.zuid, o = this.data.drid;
        console.log(t, e, o), app.util.request({
            url: "entry/wxapp/DrShopList",
            cachetime: "0",
            data: {
                store_id: t,
                user_id: e,
                dr_id: o
            },
            success: function(t) {
                console.log(t.data), i.setData({
                    drlsit: t.data
                });
            }
        });
    },
    onLoad: function(i) {
        var e = this;
        app.setNavigationBarColor(this), app.getUserInfo(function(t) {
            console.log(t), t.id == i.uid && wx.redirectTo({
                url: "drdc?storeid=" + i.storeid + "&tableid=" + i.tableid
            }), e.setData({
                userinfo: t
            });
        }), console.log(i), this.setData({
            drid: i.drid,
            storeid: i.storeid,
            zuid: i.uid,
            tableid: i.tableid
        }), app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: i.storeid,
                type: 1
            },
            success: function(t) {
                console.log(t.data), e.setData({
                    store: t.data.store
                });
            }
        }), app.util.request({
            url: "entry/wxapp/DrShopList",
            cachetime: "0",
            data: {
                store_id: i.storeid,
                user_id: i.uid,
                dr_id: i.drid
            },
            success: function(t) {
                console.log(t.data), e.setData({
                    drlsit: t.data,
                    drid: i.drid
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