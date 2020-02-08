var a = getApp();

Page({
    data: {
        checked: 0,
        data: {},
        reply: ""
    },
    onLoad: function(e) {
        var t = this;
        t.data.options = e, a.util.request({
            url: "manage/order/takeout/op",
            data: {
                type: e.type,
                id: e.id
            },
            success: function(e) {
                var r = e.data.message;
                if (r.errno) return a.util.toast(r.message, "", 1e3), !1;
                if (t.setData({
                    data: r.message.data,
                    type: t.data.options.type
                }), "cancel" == t.data.type) var d = "选择理由"; else "direct_deliveryer" == t.data.type ? d = "选择配送员" : "reply" == t.data.type && (d = "催单回复");
                wx.setNavigationBarTitle({
                    title: d
                });
            }
        });
    },
    onChoose: function(a) {
        var e = this, t = a.detail.value;
        e.setData({
            checked: t
        }), "reply" == e.data.type && (e.data.reply = e.data.reply + e.data.data[t], e.setData({
            reply: e.data.reply
        }));
    },
    onInput: function(a) {
        var e = this;
        e.data.reply = e.data.reply + a.detail.value, e.setData({
            reply: e.data.reply
        });
    },
    onSubmit: function() {
        var e = this, t = e.data.checked, r = e.data.type;
        if ("direct_deliveryer" == r) {
            var d = e.data.data[t].id;
            if (!d) return a.util.toast("请选择要转单到的配送员", "", 1e3), !1;
            var i = {
                id: e.data.options.id,
                deliveryer_id: d
            }, o = "manage/order/takeout/deliveryer";
        } else if ("cancel" == r) {
            var s = e.data.checked;
            if (!s) return a.util.toast("请先选择原因", "", 1e3), !1;
            var i = {
                id: e.data.options.id,
                reason: s
            }, o = "manage/order/takeout/cancel";
        } else if ("reply" == r) {
            var n = e.data.options.id;
            if (!e.data.reply) return a.util.toast("请输入回复内容", "", 1e3), !1;
            var i = {
                id: n,
                reply: e.data.reply
            }, o = "manage/order/takeout/reply";
        }
        a.util.request({
            url: o,
            data: i,
            method: "POST",
            success: function(e) {
                var t = e.data.message;
                if (t.errno) return a.util.toast(t.message, "", 1e3), !1;
                a.util.toast(t.message, "./index", 1e3);
            }
        });
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});