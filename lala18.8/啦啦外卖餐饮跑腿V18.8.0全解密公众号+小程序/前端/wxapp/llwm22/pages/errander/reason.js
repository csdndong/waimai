var e = getApp();

Page({
    data: {
        checked: 0
    },
    onLoad: function(t) {
        var a = this;
        a.data.options = t, e.util.request({
            url: "delivery/order/errander/op",
            data: {
                type: t.type,
                id: t.id
            },
            success: function(t) {
                var r = t.data.message;
                if (r.errno) return e.util.toast(r.message, "", 1e3), !1;
                if (a.setData({
                    reasons: r.message,
                    type: a.data.options.type
                }), "delivery_transfer" == a.data.type) var s = "转单理由"; else "cancel" == a.data.type ? s = "取消订单理由" : "direct_transfer" == a.data.type && (s = "选择配送员");
                wx.setNavigationBarTitle({
                    title: s
                });
            }
        });
    },
    onChoose: function(e) {
        var t = e.detail.value;
        this.setData({
            checked: t
        });
    },
    onNote: function(e) {
        this.setData({
            note: e.detail.value
        });
    },
    onSubmit: function() {
        var t = this, a = t.data.checked, r = t.data.type;
        if ("direct_transfer" == r) {
            var s = t.data.reasons[a].id;
            if (!s) return e.util.toast("请选择要转单到的配送员", "", 1e3), !1;
        } else {
            if (-1 == a) i = t.data.note; else {
                if (!t.data.reasons) return e.util.toast("请输入原因", "", 1e3), !1;
                var i = t.data.reasons[a];
            }
            if (!i) return e.util.toast("请先输入原因", "", 1e3), !1;
        }
        var n = {
            id: t.data.options.id,
            type: r,
            deliveryer_id: s,
            reason: i
        };
        e.util.request({
            url: "delivery/order/errander/status",
            data: n,
            success: function(t) {
                var a = t.data.message;
                if (a.errno) return e.util.toast(a.message, "", 1e3), !1;
                e.util.toast(a.message, "./list", 1e3);
            }
        });
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});