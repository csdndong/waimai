var a = getApp();

Page(function(a, t, e) {
    return t in a ? Object.defineProperty(a, t, {
        value: e,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : a[t] = e, a;
}({
    data: {
        status: 1,
        refresh: 0,
        showLoading: !1,
        orders: {
            page: 1,
            psize: 10,
            loaded: !1,
            data: []
        },
        showGoods: !1
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    },
    onLoad: function(a) {
        this.onReachBottom();
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        this.data.refresh = 1, this.onReachBottom(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        var t = this;
        if (1 == t.data.refresh && (t.data.orders = {
            page: 1,
            psize: 10,
            loaded: !1,
            data: []
        }), t.data.orders.loaded) return !1;
        t.setData({
            showLoading: !0
        }), a.util.request({
            url: "manage/order/tangshi/list",
            data: {
                status: t.data.status,
                page: t.data.orders.page,
                psize: t.data.orders.psize
            },
            success: function(e) {
                var o = e.data.message;
                if (o.errno) return a.util.toast(o.message), !1;
                var s = t.data.orders.data.concat(o.message.orders);
                t.data.orders.data = s, o.message.orders.length < t.data.orders.psize && (t.data.orders.loaded = !0), 
                t.data.refresh = 0, t.setData({
                    orders: t.data.orders,
                    showLoading: !1
                });
            }
        });
    },
    onChangeStatus: function(a) {
        var t = this, e = a.currentTarget.dataset.status;
        t.data.status != e && (t.data.refresh = 1), t.setData({
            status: e
        }), t.onReachBottom();
    },
    onShowGoods: function(a) {
        var t = a.currentTarget.dataset.index;
        this.data.orders.data[t].showGoods = !this.data.orders.data[t].showGoods, this.setData({
            "orders.data": this.data.orders.data
        });
    },
    onShareAppMessage: function() {}
}, "onPullDownRefresh", function() {
    var a = this;
    a.data.refresh = 1, a.onReachBottom(), wx.stopPullDownRefresh();
}));