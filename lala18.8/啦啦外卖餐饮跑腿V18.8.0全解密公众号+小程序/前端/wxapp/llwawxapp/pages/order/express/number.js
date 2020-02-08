var e = getApp(), s = e.requirejs("core"), t = e.requirejs("foxui");

e.requirejs("jquery");

Page({
    data: {
        express: "",
        expresscom: "",
        express_number: ""
    },
    onLoad: function(s) {
        this.setData({
            options: s
        }), e.url(s), this.get_list();
    },
    get_list: function() {
        var e = this;
        s.get("order/express_number", e.data.options, function(t) {
            console.log(t), 0 == t.error ? (t.show = !0, e.setData(t)) : s.toast(t.message, "loading");
        });
    },
    inputPrickChange: function(e) {
        var s = this, t = s.data.express_list, r = e.detail.value, a = t[r].name, i = t[r].express;
        s.setData({
            expresscom: a,
            express: i,
            index: r
        });
    },
    inputChange: function(e) {
        var s = e.detail.value;
        this.setData({
            express_number: s
        });
    },
    back: function() {
        wx.navigateBack();
    },
    submit: function(e) {
        var r = this, a = e.currentTarget.dataset.refund, i = r.data.express_number, a = r.data.options.refundid, n = r.data.options.id;
        if ("" != i) {
            var o = r.data.express, u = r.data.expresscom;
            s.get("order/express_number", {
                submit: 1,
                refundid: a,
                orderid: n,
                express_number: i,
                express: o,
                expresscom: u
            }, function(e) {
                0 == e.error && wx.navigateTo({
                    url: "/pages/order/detail/index?id=" + n
                }), console.log(e);
            });
        } else t.toast(r, "请填写快递单号");
    }
});