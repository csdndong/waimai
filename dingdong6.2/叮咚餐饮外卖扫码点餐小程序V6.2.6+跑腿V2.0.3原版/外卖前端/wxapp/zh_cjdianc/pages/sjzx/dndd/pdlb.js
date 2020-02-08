/*   time:2019-07-18 01:03:19*/
var app = getApp(),
    siteinfo = require("../../../../siteinfo.js");
Page({
    data: {
        jrdd: "0",
        jrcj: "0"
    },
    formSubmit: function(t) {
        var o = wx.getStorageSync("users").id;
        console.log(o), wx.showLoading({
            title: "跳转中"
        }), app.util.request({
            url: "entry/wxapp/AddFormId",
            cachetime: "0",
            data: {
                user_id: o,
                form_id: t.detail.formId
            },
            success: function(t) {
                console.log(t.data, o), wx.navigateTo({
                    url: "pdfl"
                })
            }
        })
    },
    reLoad: function() {
        var o = this;
        app.util.request({
            url: "entry/wxapp/NumberList",
            cachetime: "0",
            data: {
                store_id: wx.getStorageSync("sjdsjid")
            },
            success: function(t) {
                console.log(t.data), o.setData({
                    NumberList: t.data
                })
            }
        })
    },
    refresh: function() {
        wx.showToast({
            title: "刷新数据",
            icon: "loading"
        }), this.reLoad()
    },
    ckyl: function(t) {
        var o = t.currentTarget.dataset.id;
        console.log(o), wx.navigateTo({
            url: "pdxq?typename=" + t.currentTarget.dataset.id
        })
    },
    call: function(t) {
        var o = t.currentTarget.dataset.id,
            e = siteinfo.siteroot.replace("app/index.php", "");
        console.log(o, e), wx.showModal({
            title: "提示",
            content: "确认叫号吗？",
            success: function(t) {
                t.confirm ? (console.log("用户点击确定"), app.util.request({
                    url: "entry/wxapp/QueryNumber",
                    cachetime: "0",
                    data: {
                        id: o
                    },
                    success: function(t) {
                        console.log(t, t.data), wx.playBackgroundAudio({
                            dataUrl: e + t.data,
                            title: "语音播报"
                        })
                    }
                })) : t.cancel && console.log("用户点击取消")
            }
        })
    },
    sitdown: function(t) {
        var o = this,
            e = t.currentTarget.dataset.id;
        console.log(e), wx.showModal({
            title: "提示",
            content: "确认入座此号吗？",
            success: function(t) {
                t.confirm ? (console.log("用户点击确定"), app.util.request({
                    url: "entry/wxapp/Pdrz",
                    cachetime: "0",
                    data: {
                        id: e
                    },
                    success: function(t) {
                        console.log(t), 1 == t.data && (wx.showToast({
                            title: "操作成功",
                            duration: 1e3
                        }), setTimeout(function() {
                            o.reLoad()
                        }, 1e3))
                    }
                })) : t.cancel && console.log("用户点击取消")
            }
        })
    },
    pass: function(t) {
        var o = this,
            e = t.currentTarget.dataset.id;
        console.log(e), wx.showModal({
            title: "提示",
            content: "确认跳过此号吗？",
            success: function(t) {
                t.confirm ? (console.log("用户点击确定"), app.util.request({
                    url: "entry/wxapp/Pdth",
                    cachetime: "0",
                    data: {
                        id: e
                    },
                    success: function(t) {
                        console.log(t), 1 == t.data && (wx.showToast({
                            title: "操作成功",
                            duration: 1e3
                        }), setTimeout(function() {
                            o.reLoad()
                        }, 1e3))
                    }
                })) : t.cancel && console.log("用户点击取消")
            }
        })
    },
    onLoad: function(t) {
        wx.setNavigationBarTitle({
            title: "排队取号"
        }), this.reLoad();
        var o = wx.getStorageSync("sjdsjid");
        console.log(o, wx.getStorageSync("system")), app.setNavigationBarColor(this), app.sjdpageOnLoad(this)
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});