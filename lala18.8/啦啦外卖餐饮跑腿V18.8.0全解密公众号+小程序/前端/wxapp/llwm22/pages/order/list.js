var t = getApp();

Page({
    data: {
        status: 3,
        psize: 15,
        showloading: !1,
        order: {
            status_3: {
                status: 3,
                page: 1,
                list: [],
                empty: 0,
                loaded: 0
            },
            status_4: {
                status: 7,
                page: 1,
                list: [],
                empty: 0,
                loaded: 0
            },
            status_7: {
                status: 7,
                page: 1,
                list: [],
                empty: 0,
                loaded: 0
            }
        }
    },
    onLoad: function(t) {
        console.log("onLoadonLoadonLoadonLoadonLoadonLoadonLoadonLoadonLoad");
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    },
    onReachBottom: function(a) {
        var e = this, o = "status_" + e.data.status;
        e.setData({
            showloading: !0
        }), t.util.request({
            url: "delivery/order/takeout",
            data: {
                psize: e.data.psize,
                page: e.data.order[o].page,
                status: e.data.status
            },
            success: function(a) {
                var s = a.data.message;
                s.errno && t.util.toast(s.message, "", 1e3);
                var d = e.data.order[o].list.concat(s.message.orders);
                if (e.data.order[o].list = d, d.length || (e.data.order[o].empty = 1), s.message.orders.length < e.data.psize && (e.data.order[o].loaded = 1), 
                e.data.order[o].page++, e.setData({
                    showloading: !1,
                    can_collect_order: s.message.can_collect_order,
                    activityItem: e.data.order[o],
                    deliveryer: s.message.deliveryer,
                    order: e.data.order
                }), e.data.deliveryer.openid_wxapp_deliveryer) {
                    var r = t.util.getStorageSync("deliveryerInfo");
                    r.openid_wxapp_deliveryer || (r.openid_wxapp_deliveryer = e.data.deliveryer.openid_wxapp_deliveryer, 
                    t.util.setStorageSync("deliveryerInfo", r));
                }
            }
        });
    },
    onChangeOrderStatus: function(a) {
        var e = this, o = a.currentTarget.dataset;
        wx.showModal({
            title: "系统提示",
            content: o.confirm,
            success: function(a) {
                a.confirm ? t.util.request({
                    url: "delivery/order/takeout/status",
                    data: o,
                    success: function(a) {
                        var o = a.data.message;
                        t.util.toast(o.message, "", 1e3), o.errno || e.onPullDownRefresh();
                    }
                }) : a.cancel;
            }
        });
    },
    onChange: function(t) {
        var a = this, e = t.currentTarget.dataset.index, o = "status_" + e;
        a.data.order[o] = {
            status: e,
            page: 1,
            list: [],
            empty: 0,
            loaded: 0
        }, a.setData({
            status: e
        }), a.onReachBottom();
    },
    onDetail: function(a) {
        var e = a.currentTarget.dataset, o = e.id;
        if (3 == e.status) return t.util.toast("抢单后才能查看订单详情", "", 1e3), !1;
        wx.navigateTo({
            url: "./detail?id=" + o
        });
    },
    onReady: function() {},
    onShow: function() {
        var a = this, e = "status_" + a.data.status;
        a.data.order[e] = {
            status: a.data.status,
            page: 1,
            list: [],
            empty: 0,
            loaded: 0
        }, t.util.uploadOpenid(), a.onReachBottom(), t.util.followLocation(!1, !0);
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        var t = this, a = "status_" + t.data.status;
        t.data.order[a] = {
            status: t.data.status,
            page: 1,
            list: [],
            empty: 0,
            loaded: 0
        }, t.onReachBottom(), wx.stopPullDownRefresh();
    },
    onShareAppMessage: function() {}
});