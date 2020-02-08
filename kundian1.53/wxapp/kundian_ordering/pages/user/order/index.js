var app = new getApp(), uniacid = app.siteInfo.uniacid;

Page({
    data: {
        is_active: 1,
        orderData: [],
        page: 1,
        aboutData: []
    },
    onLoad: function(a) {
        var t = this;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "manager",
                op: "getAllOrder",
                uniacid: uniacid,
                is_active: 1
            },
            success: function(a) {
                console.log(a), t.setData({
                    orderData: a.data.orderData
                });
            }
        });
    },
    getOrderList: function(a) {
        var t = this, e = app.siteInfo.uniacid, r = t.data.is_active;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "manager",
                op: "getAllOrder",
                uniacid: e,
                is_active: r
            },
            success: function(a) {
                console.log(a), t.setData({
                    orderData: a.data.orderData
                });
            }
        });
    },
    waiOrder: function(a) {
        var t = this, e = app.siteInfo.uniacid;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "manager",
                op: "getAllOrder",
                uniacid: e,
                is_active: 1
            },
            success: function(a) {
                console.log(a), t.setData({
                    orderData: a.data.orderData,
                    is_active: 1,
                    page: 1
                });
            }
        });
    },
    kuaiOrder: function(a) {
        var t = this, e = app.siteInfo.uniacid;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "manager",
                op: "getAllOrder",
                uniacid: e,
                is_active: 4
            },
            success: function(a) {
                t.setData({
                    orderData: a.data.orderData,
                    is_active: 4,
                    page: 1
                });
            }
        });
    },
    dianOrder: function(a) {
        var i = this;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "desk",
                op: "getDeskShopOrder",
                uniacid: uniacid,
                is_active: 2
            },
            success: function(a) {
                console.log(a);
                var t = a.data, e = t.orderData, r = t.aboutData;
                i.setData({
                    orderData: e,
                    is_active: 2,
                    aboutData: r,
                    page: 1
                });
            }
        });
    },
    makeOrder: function(a) {
        var t = this, e = app.siteInfo.uniacid;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "manager",
                op: "getAllOrder",
                uniacid: e,
                is_active: 3
            },
            success: function(a) {
                console.log(a), t.setData({
                    orderData: a.data.orderData,
                    is_active: 3,
                    page: 1
                });
            }
        });
    },
    onReachBottom: function() {
        var r = this, a = r.data, t = a.is_active, i = a.page, n = a.orderData;
        2 == t ? app.util.request({
            url: "entry/wxapp/desk",
            data: {
                op: "getDeskShopOrder",
                uniacid: uniacid,
                is_active: 2,
                page: i
            },
            success: function(a) {
                console.log(a);
                for (var t = a.data.orderData, e = 0; e < t.length; e++) n.push(t[e]);
                r.setData({
                    orderData: n,
                    is_active: 2,
                    aboutData: a.data.aboutData,
                    page: parseInt(i) + 1
                });
            }
        }) : app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "manager",
                op: "getMoreData",
                uniacid: uniacid,
                is_active: t,
                page: i
            },
            success: function(a) {
                for (var t = a.data.orderData, e = 0; e < t.length; e++) n.push(t[e]);
                r.setData({
                    orderData: n,
                    page: parseInt(i) + 1
                });
            }
        });
    },
    cancelOrder: function(a) {
        var t = this, e = a.currentTarget.dataset.orderid;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "manager",
                op: "cancelOrder",
                orderid: e,
                uniacid: uniacid
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
    beginSend: function(a) {
        var t = this, e = a.currentTarget.dataset.orderid;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "manager",
                op: "beginSend",
                orderid: e,
                uniacid: uniacid
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
    completeSend: function(a) {
        var t = this, e = a.currentTarget.dataset.orderid;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "manager",
                op: "completeSend",
                orderid: e,
                uniacid: uniacid
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
    useMake: function(a) {
        var t = this, e = a.currentTarget.dataset.orderid;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "manager",
                op: "useMake",
                orderid: e,
                uniacid: uniacid
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
    cancelMake: function(a) {
        var t = this, e = a.currentTarget.dataset.orderid;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "manager",
                op: "cancelMake",
                orderid: e,
                uniacid: uniacid
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
    intoHexiao: function(a) {
        wx.redirectTo({
            url: "../gift_ovucher/index"
        });
    },
    intoDeskOrderDetail: function(a) {
        var t = a.currentTarget.dataset.orderid;
        wx.navigateTo({
            url: "../../desk/select_items/index?order_id=" + t
        });
    }
});