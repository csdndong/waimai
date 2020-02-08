var app = getApp(), util = require("../../utils/util.js");

Page({
    data: {
        isloading: !0
    },
    qxpd: function() {
        var a = this.data.drid, t = this.data.drlsit.drorder;
        console.log(t), wx.showModal({
            title: "提示",
            content: "是否关闭此拼单？",
            success: function(t) {
                t.confirm ? (console.log("用户点击确定"), wx.showLoading({
                    title: "加载中"
                }), app.util.request({
                    url: "entry/wxapp/SdDrShop",
                    cachetime: "0",
                    data: {
                        id: a
                    },
                    success: function(t) {
                        console.log(t.data), "1" == t.data && (wx.showToast({
                            title: "取消成功"
                        }), wx.navigateBack({}));
                    }
                })) : t.cancel && console.log("用户点击取消");
            }
        });
    },
    tjddformSubmit: function() {
        var a = this, e = this.data.drid, t = this.data.drlsit.drorder;
        if (console.log(t), this.data.drlsit.money <= 0) return wx.showModal({
            title: "提示",
            content: "金额过低无法提交订单"
        }), !1;
        2 == t.state && wx.navigateTo({
            url: "smdcform?storeid=" + a.data.storeid + "&tableid=" + a.data.tableid + "&drid=" + e
        }), 1 == t.state && wx.showModal({
            title: "提示",
            content: "提交订单后将锁定订单",
            success: function(t) {
                t.confirm ? (console.log("用户点击确定"), wx.showLoading({
                    title: "加载中"
                }), app.util.request({
                    url: "entry/wxapp/SdDrShop",
                    cachetime: "0",
                    data: {
                        id: e
                    },
                    success: function(t) {
                        console.log(t.data), "1" == t.data && (wx.showToast({
                            title: "锁定成功"
                        }), a.reLoad(), setTimeout(function() {
                            wx.navigateTo({
                                url: "smdcform?storeid=" + a.data.storeid + "&tableid=" + a.data.tableid + "&drid=" + e
                            });
                        }, 1e3));
                    }
                })) : t.cancel && console.log("用户点击取消");
            }
        });
    },
    jsdd: function() {
        var a = this, t = this.data.drid;
        this.data.drlsit.drorder;
        wx.showLoading({
            title: "加载中"
        }), app.util.request({
            url: "entry/wxapp/JsDrShop",
            cachetime: "0",
            data: {
                id: t
            },
            success: function(t) {
                console.log(t.data), "1" == t.data && (wx.showToast({
                    title: "解锁成功"
                }), a.reLoad());
            }
        });
    },
    sc: function(t) {
        var a = this, e = this.data.storeid, o = t.currentTarget.dataset.sonid, s = this.data.zuid, i = this.data.drid;
        console.log(e, s, o, i), wx.showModal({
            title: "提示",
            content: "确定删除他的商品吗？",
            success: function(t) {
                t.confirm ? (console.log("用户点击确定"), app.util.request({
                    url: "entry/wxapp/DelCar",
                    cachetime: "0",
                    data: {
                        store_id: e,
                        user_id: s,
                        son_id: o,
                        dr_id: i,
                        type: 2
                    },
                    success: function(t) {
                        console.log(t.data), "1" == t.data && (wx.showToast({
                            title: "删除成功"
                        }), a.reLoad());
                    }
                })) : t.cancel && console.log("用户点击取消");
            }
        });
    },
    sxsj: function() {
        this.reLoad();
    },
    reLoad: function() {
        var a = this, t = this.data.storeid, e = this.data.zuid, o = this.data.drid;
        console.log(t, e, o), app.util.request({
            url: "entry/wxapp/DrShopList",
            cachetime: "0",
            data: {
                store_id: t,
                user_id: e,
                dr_id: o
            },
            success: function(t) {
                console.log(t.data), a.setData({
                    drlsit: t.data
                });
            }
        });
    },
    onLoad: function(e) {
        wx.hideShareMenu({});
        var o = this, s = wx.getStorageSync("users").id;
        console.log(s, e), app.setNavigationBarColor(this), this.setData({
            storeid: e.storeid,
            tableid: e.tableid,
            zuid: s
        }), app.util.request({
            url: "entry/wxapp/Zhuohao",
            cachetime: "0",
            data: {
                id: e.tableid
            },
            success: function(t) {
                console.log(t), o.setData({
                    type_name: t.data.type_name,
                    table_name: t.data.table_name
                });
            }
        }), app.util.request({
            url: "entry/wxapp/IsDr",
            cachetime: "0",
            data: {
                store_id: e.storeid,
                user_id: s
            },
            success: function(t) {
                console.log(t.data);
                var a = t.data;
                "请重新开启拼单" != t.data ? (o.setData({
                    isloading: !1
                }), app.util.request({
                    url: "entry/wxapp/DrShopList",
                    cachetime: "0",
                    data: {
                        store_id: e.storeid,
                        user_id: s,
                        dr_id: a
                    },
                    success: function(t) {
                        console.log(t.data), o.setData({
                            drlsit: t.data,
                            drid: a
                        });
                    }
                })) : app.util.request({
                    url: "entry/wxapp/DrShop",
                    cachetime: "0",
                    data: {
                        store_id: e.storeid,
                        user_id: s
                    },
                    success: function(t) {
                        console.log(t.data);
                        var a = t.data;
                        "请稍后重试" != t.data && (o.setData({
                            isloading: !1
                        }), app.util.request({
                            url: "entry/wxapp/DrShopList",
                            cachetime: "0",
                            data: {
                                store_id: e.storeid,
                                user_id: s,
                                dr_id: a
                            },
                            success: function(t) {
                                console.log(t.data), o.setData({
                                    drlsit: t.data,
                                    drid: a
                                });
                            }
                        }));
                    }
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
    onShareAppMessage: function(t) {
        var a = this.data.storeid, e = wx.getStorageSync("users").id, o = this.data.tableid, s = this.data.drid;
        return console.log(a, e, o, s), "button" === t.from && console.log(t.target), {
            title: wx.getStorageSync("users").name + "邀请你来拼单",
            path: "/zh_cjdianc/pages/smdc/sharedrdc?storeid=" + a + "&tableid=" + o + "&uid=" + e + "&drid=" + s
        };
    }
});