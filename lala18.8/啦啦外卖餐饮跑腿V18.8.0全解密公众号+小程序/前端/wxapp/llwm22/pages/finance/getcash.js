var e = getApp();

Page({
    data: {},
    onLoad: function(t) {
        var a = this;
        e.util.request({
            url: "delivery/finance/getcash/index",
            success: function(t) {
                var s = t.data.message;
                if (s.errno) return e.util.toast(s.message), !1;
                a.setData(s.message);
            }
        });
    },
    onGetCash: function(t) {
        var a = this, s = parseFloat(t.detail.value.get_fee), i = a.data.deliveryer;
        return !a.data.submit && (s <= 0 ? (e.util.toast("提现金额有误"), !1) : s > i.credit2 ? (e.util.toast("提现金额大于可用余额"), 
        !1) : s < i.fee_getcash.get_cash_fee_limit ? (e.util.toast("提现金额不能小于" + i.fee_getcash.get_cash_fee_limit + "元"), 
        !1) : (a.data.submit = !0, void e.util.request({
            url: "delivery/finance/getcash/submit",
            data: {
                formId: t.detail.formId,
                get_fee: s
            },
            success: function(t) {
                a.data.submit = !1;
                var s = t.data.message;
                if (s.errno ? s.message.message ? e.util.toast(s.message.message) : e.util.toast(s.message) : e.util.toast(s.message.message), 
                !s.errno || s.errno && s.message.id) {
                    var i = s.message.id;
                    wx.redirectTo({
                        url: "./getcashDetail?id=" + i
                    });
                }
            }
        })));
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});