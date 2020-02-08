var app = new getApp(), uniacid = app.siteInfo.uniacid;

Page({
    data: {
        orderData: [],
        is_active: 1,
        page: 1,
        userData: [],
        aboutData: []
    },
    onLoad: function(a) {
        var t = wx.getStorageSync("kundian_ordering_uid");
        if (a.is_active) {
            console.log(a.is_active);
            var e = a.is_active;
            this.setData({
                is_active: e
            });
        }
        0 != t ? this.getOrderList() : wx.navigateTo({
            url: "../../login/index"
        });
    },
    getOrderList: function(a) {
        var t = this, e = wx.getStorageSync("kundian_ordering_uid"), r = t.data.is_active;
        e ? app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "order",
                op: "getAll",
                uniacid: uniacid,
                uid: e,
                is_active: r
            },
            success: function(a) {
                t.setData({
                    orderData: a.data.orderData
                });
            }
        }) : wx.navigateTo({
            url: "../../login/index"
        });
    },
    onShow: function(a) {
        2 == this.data.is_active ? this.dianOrder() : this.getOrderList();
    },
    getAll: function(a) {
        var t = this, e = wx.getStorageSync("kundian_ordering_uid");
        t.setData({
            is_active: 1,
            page: 1
        }), app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "order",
                op: "getAll",
                uniacid: uniacid,
                uid: e,
                is_active: 1
            },
            success: function(a) {
                t.setData({
                    orderData: a.data.orderData
                });
            }
        });
    },
    dianOrder: function(a) {
        var o = this, t = wx.getStorageSync("kundian_ordering_uid");
        o.setData({
            is_active: 2,
            page: 1
        }), app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "desk",
                op: "getDeskOrderList",
                uniacid: uniacid,
                uid: t,
                is_active: 2
            },
            success: function(a) {
                var t = a.data, e = t.orderData, r = t.aboutData, i = t.userData;
                o.setData({
                    orderData: e,
                    aboutData: r,
                    userData: i
                });
            }
        });
    },
    orderGoods: function(a) {
        var t = this, e = wx.getStorageSync("kundian_ordering_uid");
        t.setData({
            is_active: 3,
            page: 1
        }), app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "order",
                op: "getAll",
                uniacid: uniacid,
                uid: e,
                is_active: 3
            },
            success: function(a) {
                t.setData({
                    orderData: a.data.orderData
                });
            }
        });
    },
    getFastOrder: function(a) {
        var t = this, e = wx.getStorageSync("kundian_ordering_uid");
        t.setData({
            is_active: 4,
            page: 1
        }), app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "order",
                op: "getAll",
                uniacid: uniacid,
                uid: e,
                is_active: 4
            },
            success: function(a) {
                t.setData({
                    orderData: a.data.orderData
                });
            }
        });
    },
    confirmGoods: function(a) {
        var t = this, e = a.currentTarget.dataset.orderid;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "order",
                op: "confirmGoods",
                uniacid: uniacid,
                orderid: e
            },
            success: function(a) {
                console.log(a), 1 == a.data.code ? (wx.showToast({
                    title: "收货成功"
                }), t.getOrderList()) : wx.showToast({
                    title: "收货失败"
                });
            }
        });
    },
    deleteOrder: function(a) {
        var t = this, e = a.currentTarget.dataset.orderid;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "order",
                op: "deleteOrder",
                uniacid: uniacid,
                orderid: e
            },
            success: function(a) {
                console.log(a), 1 == a.data.code ? (wx.showToast({
                    title: "删除成功"
                }), t.getOrderList()) : 2 == a.data.code ? wx.showToast({
                    title: "订单未完成"
                }) : wx.showToast({
                    title: "删除失败"
                });
            }
        });
    },
    liPay: function(a) {
        var e = a.currentTarget.dataset.orderid;
        app.util.request({
            url: "entry/wxapp/pay",
            data: {
                orderid: e,
                uniacid: uniacid
            },
            cachetime: "0",
            success: function(t) {
                console.log(t), t.data && t.data.data && !t.data.errno ? wx.requestPayment({
                    timeStamp: t.data.data.timeStamp,
                    nonceStr: t.data.data.nonceStr,
                    package: t.data.data.package,
                    signType: "MD5",
                    paySign: t.data.data.paySign,
                    success: function(a) {
                        console.log(a), "requestPayment:ok" == a.errMsg ? (wx.getStorageSync("configMode") - 0 == 1 && app.util.request({
                            url: "entry/wxapp/desk",
                            data: {
                                op: "fastFoodPrint",
                                order_id: e,
                                uniacid: uniacid,
                                uid: app.globalData.uid
                            },
                            success: function(a) {
                                1 == a.data.code && wx.setStorage({
                                    key: "fastFoodDetail",
                                    data: a.data.order_data
                                });
                            }
                        }), app.util.request({
                            url: "entry/wxapp/order",
                            data: {
                                control: "buy",
                                op: "notify",
                                order_id: e,
                                uniacid: uniacid,
                                prepay_id: t.data.data.package
                            },
                            success: function(a) {
                                console.log(a), wx.showToast({
                                    title: "支付成功",
                                    success: function(a) {
                                        wx.navigateTo({
                                            url: "../index/index"
                                        });
                                    },
                                    fail: function(a) {},
                                    complete: function(a) {}
                                });
                            }
                        })) : wx.showToast({
                            title: "支付失败"
                        });
                    },
                    fail: function(a) {
                        console.log("error"), wx.navigateTo({
                            url: "../index/index"
                        });
                    }
                }) : wx.navigateTo({
                    url: "../index/index"
                });
            },
            fail: function(a) {
                wx.showModal({
                    title: "系统提示",
                    content: a.data.message ? a.data.message : "错误",
                    showCancel: !1,
                    success: function(a) {
                        a.confirm && console.log("2");
                    }
                });
            }
        });
    },
    cancelWeiOrder: function(a) {
        var t = this, e = a.currentTarget.dataset.orderid, r = wx.getStorageSync("kundian_ordering_uid");
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "order",
                op: "cancelWeiOrder",
                orderid: e,
                uniacid: uniacid,
                uid: r
            },
            success: function(a) {
                wx.showModal({
                    title: "提示",
                    content: a.data.msg,
                    showCancel: !1,
                    success: function() {
                        1 == a.data.code && t.getOrderList();
                    }
                });
            }
        });
    },
    cancelPayOrder: function(a) {
        var t = this, e = a.currentTarget.dataset.orderid, r = wx.getStorageSync("kundian_ordering_uid");
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "order",
                op: "cancelPayOrder",
                orderid: e,
                uniacid: uniacid,
                uid: r
            },
            success: function(a) {
                1 == a.data.code ? wx.showToast({
                    title: "取消申请已提交",
                    success: function(a) {
                        t.getOrderList();
                    }
                }) : wx.showToast({
                    title: "取消失败"
                });
            }
        });
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        var r = this, a = r.data, t = a.page, e = a.is_active, i = a.orderData, o = wx.getStorageSync("kundian_ordering_uid");
        r.setData({
            page: parseInt(t) + 1
        }), 2 != e && app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "order",
                op: "getMoreData",
                uniacid: uniacid,
                uid: o,
                is_active: e,
                page: t
            },
            success: function(a) {
                var t = a.data.orderData;
                if ("" != t) for (var e = 0; e < t.length; e++) i.push(t[e]);
                r.setData({
                    orderData: i
                });
            }
        });
    },
    intoDeskOrderDetail: function(a, t) {
        var e = a.currentTarget.dataset, r = e.orderid, i = (e.ordertype, "../../desk/order_details/index?order_id=" + r);
        0 != e.orderpay && (i = "../../desk/order_details/index?order_id=" + r), wx.navigateTo({
            url: i
        });
    }
});