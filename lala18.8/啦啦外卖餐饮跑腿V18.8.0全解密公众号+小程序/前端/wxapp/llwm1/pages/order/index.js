var t = getApp();

Page(function(t, a, e) {
    return a in t ? Object.defineProperty(t, a, {
        value: e,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : t[a] = e, t;
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
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    },
    onLoad: function(t) {
        this.onReachBottom();
    },
    onChangeOrderStatus: function(a) {
        var e = this, s = a.currentTarget.dataset, o = s.type, r = s.id;
        s.status;
        if ("cancel" == o || "direct_deliveryer" == o) return wx.navigateTo({
            url: "./op?type=" + o + "&id=" + r
        }), !1;
        wx.showModal({
            title: "系统提示",
            content: s.confirm,
            success: function(a) {
                a.confirm ? t.util.request({
                    url: "manage/order/takeout/status",
                    data: s,
                    success: function(a) {
                        var s = a.data.message;
                        t.util.toast(s.message, "", 1e3), s.errno || e.onPullDownRefresh();
                    }
                }) : a.cancel;
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        this.data.refresh = 1, this.onReachBottom(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        var a = this;
        if (1 == a.data.refresh && (a.data.orders = {
            page: 1,
            psize: 10,
            loaded: !1,
            data: []
        }), a.data.orders.loaded) return !1;
        a.setData({
            showLoading: !0
        }), t.util.request({
            url: "manage/order/takeout/list",
            data: {
                status: a.data.status,
                page: a.data.orders.page,
                psize: a.data.orders.psize
            },
            success: function(e) {
                var s = e.data.message;
                if (s.errno) return t.util.toast(s.message), !1;
                var o = a.data.orders.data.concat(s.message.orders);
                a.data.orders.data = o, s.message.orders.length < a.data.orders.psize && (a.data.orders.loaded = !0), 
                a.data.refresh = 0, a.setData({
                    orders: a.data.orders,
                    showLoading: !1
                });
            }
        });
    },
    onChangeStatus: function(t) {
        var a = this, e = t.currentTarget.dataset.status;
        a.data.status != e && (a.data.refresh = 1), a.setData({
            status: e
        }), a.onReachBottom();
    },
    onShowGoods: function(t) {
        var a = t.currentTarget.dataset.index;
        this.data.orders.data[a].showGoods = !this.data.orders.data[a].showGoods, this.setData({
            "orders.data": this.data.orders.data
        });
    },
    onShareAppMessage: function() {}
}, "onPullDownRefresh", function() {
    var t = this;
    t.data.refresh = 1, t.onReachBottom(), wx.stopPullDownRefresh();
}));