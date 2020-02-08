/*   time:2019-07-18 01:03:19*/
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
        wx.setNavigationBarTitle({
            title: "排队分类"
        }), app.setNavigationBarColor(this), this.reLoad()
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
                    url: "entry/wxapp/DelNumberType",
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
            url: "entry/wxapp/NumberTypeList",
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
        var i = wx.getStorageSync("sjdsjid"),
            o = t.detail.value.flmc,
            s = t.detail.value.pxxh;
        console.log("", i, o, s, a);
        var n = "",
            l = !0;
        "" == o ? n = "请填写商品名称！" : "" == s ? n = "请填写排序序号！" : (e.setData({
            disabled1: !0
        }), l = !1, app.util.request({
            url: "entry/wxapp/EditNumberType",
            cachetime: "0",
            data: {
                sort: s,
                typename: o,
                store_id: i,
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
        if (t.detail.value.sfxs) var i = 1;
        else i = 2;
        var o = wx.getStorageSync("sjdsjid"),
            s = t.detail.value.flmc,
            n = t.detail.value.pxxh;
        console.log(a, o, s, n, i);
        var l = "",
            c = !0;
        "" == s ? l = "请填写商品名称！" : "" == n ? l = "请填写排序序号！" : (e.setData({
            disabled: !0
        }), c = !1, app.util.request({
            url: "entry/wxapp/EditNumberType",
            cachetime: "0",
            data: {
                sort: n,
                typename: s,
                store_id: o,
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
        })), 1 == c && wx.showModal({
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