var a = getApp();

Page({
    data: {
        pay_type: "all",
        orders: {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        }
    },
    onLoad: function(a) {
        this.onReachBottom();
    },
    onChangeType: function(a) {
        var e = this, t = a.currentTarget.dataset.type;
        if (t == e.data.pay_type) return !1;
        e.setData({
            pay_type: t,
            orders: {
                page: 1,
                psize: 15,
                empty: !1,
                loaded: !1,
                data: []
            }
        }), e.onReachBottom();
    },
    onPullDownRefresh: function() {
        var a = this;
        a.setData({
            pay_type: "all",
            orders: {
                page: 1,
                psize: 15,
                empty: !1,
                loaded: !1,
                data: []
            }
        }), a.onLoad(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        var e = this;
        if (e.data.orders.loaded) return !1;
        a.util.request({
            url: "manage/paybill/index",
            data: {
                pay_type: e.data.pay_type,
                page: e.data.orders.page,
                psize: e.data.orders.psize
            },
            success: function(t) {
                var r = t.data.message;
                if (r.errno) a.util.toast(r.message); else {
                    r = r.message;
                    var d = e.data.orders.data.concat(r.orders);
                    e.data.orders.data = d, e.data.orders.page++, d.length || (e.data.orders.empty = !0), 
                    r.orders.length < e.data.orders.psize && (e.data.orders.loaded = !0), e.setData({
                        orders: e.data.orders
                    });
                }
            }
        });
    },
    onJsEvent: function(e) {
        a.util.jsEvent(e);
    }
});