var a = getApp();

Page({
    data: {
        status: 0,
        orders: {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        },
        showPopup: !1
    },
    onLoad: function(a) {
        this.onPullDownRefresh();
    },
    onChangeStatus: function(a) {
        var t = this, e = a.currentTarget.dataset.status;
        if (e == t.data.status) return !1;
        t.setData({
            status: e,
            orders: {
                page: 1,
                psize: 15,
                empty: !1,
                loaded: !1,
                data: []
            }
        }), t.onReachBottom();
    },
    onShowPopup: function(a) {
        var t = this, e = a.currentTarget.dataset.id;
        e && t.setData({
            orderId: e
        }), t.setData({
            showPopup: !t.data.showPopup
        });
    },
    onReceive: function(t) {
        var e = this, s = t.detail.value.code;
        if (!s) return a.util.toast("请输入兑换码"), !1;
        a.util.request({
            url: "manage/seckill/order/status",
            data: {
                id: e.data.orderId,
                code: s,
                type: "status",
                formid: t.detail.formId
            },
            success: function(t) {
                var s = t.data.message;
                a.util.toast(s.message), s.errno || (e.setData({
                    showPopup: !1
                }), e.onLoad());
            }
        });
    },
    onShowGoods: function(a) {
        var t = a.currentTarget.dataset.index;
        this.data.orders.data[t].showGoods = !this.data.orders.data[t].showGoods, this.setData({
            "orders.data": this.data.orders.data
        });
    },
    onPullDownRefresh: function() {
        var a = this;
        a.setData({
            status: 0,
            orders: {
                page: 1,
                psize: 15,
                empty: !1,
                loaded: !1,
                data: []
            }
        }), a.onReachBottom(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        var t = this;
        if (t.data.orders.loaded) return !1;
        a.util.request({
            url: "manage/seckill/order",
            data: {
                status: t.data.status,
                page: t.data.orders.page,
                psize: t.data.orders.psize
            },
            success: function(e) {
                var s = e.data.message;
                if (s.errno) a.util.toast(s.message); else {
                    s = s.message;
                    var o = t.data.orders.data.concat(s.orders);
                    t.data.orders.data = o, t.data.orders.page++, o.length || (t.data.orders.empty = !0), 
                    s.orders.length < t.data.orders.psize && (t.data.orders.loaded = !0), t.setData({
                        orders: t.data.orders
                    });
                }
            }
        });
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    }
});