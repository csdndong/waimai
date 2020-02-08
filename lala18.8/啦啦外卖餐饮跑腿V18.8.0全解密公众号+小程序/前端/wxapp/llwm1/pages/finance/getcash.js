var e = getApp();

Page({
    data: {},
    onLoad: function(t) {
        var a = this;
        e.util.request({
            url: "manage/finance/getcash",
            success: function(t) {
                var s = t.data.message;
                s.errno ? e.util.toast(s.message) : a.setData(s.message);
            }
        });
    },
    onSubmit: function(t) {
        var a = this, s = parseFloat(t.detail.value.fee), i = a.data.account;
        if (isNaN(s)) return e.util.toast("提现金额有误"), !1;
        if (s > i.amount) return e.util.toast("提现金额不能大于账户可用余额"), !1;
        if (s < i.fee_limit) return e.util.toast("提现金额不能小于" + i.fee_limit + "元"), !1;
        var n = (s * i.fee_rate / 100).toFixed(2);
        n = Math.max(n, i.fee_min), i.fee_max > 0 && (n = Math.min(n, i.fee_max)), n = parseFloat(n);
        var o = (s - n).toFixed(2), r = "提现金额" + s + "元, 手续费" + n + "元,实际到账" + o + "元, 确定提现吗";
        wx.showModal({
            content: r,
            success: function(a) {
                a.confirm ? e.util.request({
                    url: "manage/finance/getcash/getcash",
                    methods: "POST",
                    data: {
                        fee: s,
                        formid: t.detail.formId
                    },
                    success: function(t) {
                        var a = t.data.message;
                        a.errno ? e.util.toast(a.message) : e.util.toast(a.message, "/pages/shop/setting", 3e3);
                    }
                }) : a.cancel;
            }
        });
    }
});