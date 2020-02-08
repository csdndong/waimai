var e = getApp();

Page({
    data: {
        codeModalHide: !0
    },
    onLoad: function(t) {
        var a = this;
        a.data.options = t;
        var o = t.id;
        e.util.request({
            url: "delivery/order/errander/detail",
            data: {
                id: o
            },
            showLoading: !1,
            success: function(t) {
                var o = t.data.message;
                if (o.errno) return e.util.toast(o.message, "", 1e3), !1;
                a.setData(o.message);
            }
        });
    },
    onChangeOrderStatus: function(t) {
        var a = this, o = t.currentTarget.dataset, n = o.type;
        if ("delivery_transfer" == n || "direct_transfer" == n || "cancel" == n) return wx.navigateTo({
            url: "./reason?type=" + n + "&id=" + o.id + "&status=" + o.status
        }), !1;
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
    onJsEvent: function(t) {
        e.util.jsEvent(t);
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
        var e = this, t = {
            id: e.data.options.id
        };
        e.onLoad(t), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});