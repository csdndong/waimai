/*   time:2019-07-18 01:03:18*/
var app = getApp(),
    util = require("../../../utils/util.js");
Page({
    data: {
        activeid: "",
        disabled: !1,
        disabled1: !1,
        isxz: !1
    },
    qxbj: function() {
        this.setData({
            activeid: ""
        })
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this), this.reLoad()
    },
    xzfl: function() {
        this.setData({
            isxz: !0,
            activeid: ""
        })
    },
    qx: function() {
        this.setData({
            isxz: !1
        })
    },
    bianji: function(t) {
        var e = t.currentTarget.dataset.id;
        console.log(e), this.setData({
            activeid: e
        })
    },
    sc: function(t) {
        var e = this,
            a = t.currentTarget.dataset.id;
        console.log(a), wx.showModal({
            title: "提示",
            content: "确认删除此分类吗？",
            success: function(t) {
                t.confirm ? (console.log("用户点击确定"), app.util.request({
                    url: "entry/wxapp/DelGoodsType",
                    cachetime: "0",
                    data: {
                        id: a
                    },
                    success: function(t) {
                        console.log(t), 1 == t.data && (wx.showToast({
                            title: "操作成功",
                            duration: 1e3
                        }), setTimeout(function() {
                            e.reLoad()
                        }, 1e3))
                    }
                })) : t.cancel && console.log("用户点击取消")
            }
        })
    },
    reLoad: function() {
        var e = this,
            t = wx.getStorageSync("sjdsjid");
        console.log(t), app.util.request({
            url: "entry/wxapp/GoodsType",
            cachetime: "0",
            data: {
                store_id: t
            },
            success: function(t) {
                console.log(t.data), e.setData({
                    flarr: t.data
                })
            }
        })
    },
    formSubmit1: function(t) {
        console.log("form1发生了submit事件，携带数据为：", t.detail.value);
        var e = this;
        if (t.detail.value.sfxs) var a = 1;
        else a = 2;
        var o = wx.getStorageSync("sjdsjid"),
            i = t.detail.value.flmc,
            s = t.detail.value.pxxh;
        console.log("", o, i, s, a);
        var n = "",
            l = !0;
        "" == i ? n = "请填写商品名称！" : "" == s ? n = "请填写排序序号！" : (e.setData({
            disabled1: !0
        }), l = !1, app.util.request({
            url: "entry/wxapp/UpdGoodsType",
            cachetime: "0",
            data: {
                order_by: s,
                type_name: i,
                is_open: a,
                store_id: o,
                id: ""
            },
            success: function(t) {
                console.log(t), 1 == t.data ? (wx.showToast({
                    title: "操作成功"
                }), setTimeout(function() {
                    e.reLoad(), e.setData({
                        isxz: !1,
                        disabled1: !1
                    })
                }, 1e3)) : (e.setData({
                    disabled1: !1
                }), wx.showToast({
                    title: "请修改后提交！",
                    icon: "loading"
                }))
            }
        })), 1 == l && wx.showModal({
            title: "提示",
            content: n
        })
    },
    formSubmit: function(t) {
        console.log("form发生了submit事件，携带数据为：", t.detail.value);
        var e = this,
            a = this.data.activeid;
        if (t.detail.value.sfxs) var o = 1;
        else o = 2;
        var i = wx.getStorageSync("sjdsjid"),
            s = t.detail.value.flmc,
            n = t.detail.value.pxxh;
        console.log(a, i, s, n, o);
        var l = "",
            d = !0;
        "" == s ? l = "请填写商品名称！" : "" == n ? l = "请填写排序序号！" : (e.setData({
            disabled: !0
        }), d = !1, app.util.request({
            url: "entry/wxapp/UpdGoodsType",
            cachetime: "0",
            data: {
                order_by: n,
                type_name: s,
                is_open: o,
                store_id: i,
                id: a
            },
            success: function(t) {
                console.log(t), 1 == t.data ? (wx.showToast({
                    title: "操作成功"
                }), setTimeout(function() {
                    e.reLoad(), e.setData({
                        activeid: "",
                        disabled: !1
                    })
                }, 1e3)) : (e.setData({
                    disabled: !1
                }), wx.showToast({
                    title: "请修改后提交！",
                    icon: "loading"
                }))
            }
        })), 1 == d && wx.showModal({
            title: "提示",
            content: l
        })
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});