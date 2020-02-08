var e = getApp();

Page({
    data: {
        codeModalHide: !0,
        status: 1,
        psize: 15,
        showloading: !1,
        order: {
            status: 1,
            page: 1,
            list: [],
            empty: 0,
            loaded: 0
        }
    },
    onLoad: function(e) {
        this.onReachBottom();
    },
    onJsEvent: function(t) {
        e.util.jsEvent(t);
    },
    onReachBottom: function(t) {
        var a = this;
        a.setData({
            showloading: !0
        }), e.util.request({
            url: "delivery/order/errander",
            data: {
                psize: a.data.psize,
                page: a.data.order.page,
                status: a.data.status
            },
            success: function(t) {
                var o = t.data.message;
                o.errno && e.util.toast(o.message, "", 1e3);
                var s = a.data.order.list.concat(o.message.orders);
                a.data.order.list = s, s.length || (a.data.order.empty = 1), o.message.orders.length < a.data.psize && (a.data.order.loaded = 1), 
                a.data.order.page++, a.setData({
                    showloading: !1,
                    can_collect_order: o.message.can_collect_order,
                    activityItem: a.data.order,
                    deliveryer: o.message.deliveryer,
                    order: a.data.order,
                    verification_code: o.message.verification_code
                });
            }
        });
    },
    onChangeOrderStatus: function(t) {
        var a = this, o = t.currentTarget.dataset;
        wx.showModal({
            title: "",
            content: o.confirm,
            success: function(t) {
                if (t.confirm) {
                    if ("delivery_success" == o.type && a.data.verification_code) return a.setData({
                        codeModalHide: !1,
                        orderId: o.id
                    }), !1;
                    e.util.request({
                        url: "delivery/order/errander/status",
                        data: o,
                        success: function(t) {
                            var o = t.data.message;
                            e.util.toast(o.message, "", 1e3), o.errno || a.onPullDownRefresh();
                        }
                    });
                } else t.cancel;
            }
        });
    },
    onChange: function(e) {
        var t = this, a = e.currentTarget.dataset.index;
        if (a == t.data.status) return !1;
        t.data.order = {
            status: a,
            page: 1,
            list: [],
            empty: 0,
            loaded: 0
        }, t.setData({
            status: a
        }), t.onReachBottom();
    },
    onDetail: function(t) {
        var a = t.currentTarget.dataset, o = a.id;
        if (1 == a.status) return e.util.toast("抢单后才能查看订单详情", "", 1e3), !1;
        wx.navigateTo({
            url: "./detail?id=" + o
        });
    },
    onCodeConfirm: function() {
        var t = this;
        return t.setData({
            codeModalHide: !0
        }), t.data.code ? 4 != t.data.code.length ? (e.util.toast("输入收货码有误", "", 1e3), !1) : void e.util.request({
            url: "delivery/order/errander/status",
            data: {
                type: "delivery_success",
                id: t.data.orderId,
                code: t.data.code
            },
            success: function(a) {
                var o = a.data.message;
                e.util.toast(o.message, "", 1e3), o.errno || t.onPullDownRefresh();
            }
        }) : (e.util.toast("请输入收货码", "", 1e3), !1);
    },
    onCodecancel: function() {
        this.setData({
            codeModalHide: !0
        });
    },
    onInput: function(e) {
        this.data.code = e.detail.value;
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        var e = this;
        e.data.order = {
            status: e.data.status,
            page: 1,
            list: [],
            empty: 0,
            loaded: 0
        }, e.onReachBottom(), wx.stopPullDownRefresh();
    },
    onShareAppMessage: function() {}
});