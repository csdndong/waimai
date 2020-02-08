var t = getApp();

Page({
    data: {
        checked: 0
    },
    onLoad: function(e) {
        var a = this;
        a.data.options = e, t.util.request({
            url: "delivery/order/takeout/op",
            data: {
                type: e.type,
                id: e.id
            },
            success: function(e) {
                var s = e.data.message;
                if (s.errno) return t.util.toast(s.message, "", 1e3), !1;
                if (a.setData({
                    reasons: s.message,
                    type: a.data.options.type
                }), "delivery_transfer" == a.data.type) var r = "转单理由"; else "delivery_cancel" == a.data.type ? r = "取消订单理由" : "direct_transfer" == a.data.type && (r = "选择配送员");
                wx.setNavigationBarTitle({
                    title: r
                });
            }
        });
    },
    onChoose: function(t) {
        var e = t.detail.value;
        this.setData({
            checked: e
        });
    },
    onNote: function(t) {
        this.setData({
            note: t.detail.value
        });
    },
    onSubmit: function() {
        var e = this, a = e.data.checked, s = e.data.type;
        if ("direct_transfer" == s) {
            var r = e.data.reasons[a].id;
            if (!r) return t.util.toast("请选择要转单到的配送员", "", 1e3), !1;
        } else {
            if (-1 == a) i = e.data.note; else {
                if (!e.data.reasons) return t.util.toast("请输入原因", "", 1e3), !1;
                var i = e.data.reasons[a];
            }
            if (!i) return t.util.toast("请先输入原因", "", 1e3), !1;
        }
        var o = {
            id: e.data.options.id,
            type: s,
            deliveryer_id: r,
            reason: i
        };
        t.util.request({
            url: "delivery/order/takeout/status",
            data: o,
            success: function(e) {
                var a = e.data.message;
                if (a.errno) return t.util.toast(a.message, "", 1e3), !1;
                t.util.toast(a.message, "./list", 1e3);
            }
        });
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});