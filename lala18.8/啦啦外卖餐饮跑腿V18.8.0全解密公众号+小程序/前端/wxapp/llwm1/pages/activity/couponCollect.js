var t = getApp(), a = require("../../static/js/utils/dateTimePicker.js");

Page({
    data: {
        startYear: 2018,
        endYear: 2030,
        coupon: {},
        coupons: [],
        type_limit: 1
    },
    onLoad: function(e) {
        var o = this;
        t.util.request({
            url: "manage/activity/index/activity_coupon",
            data: {
                type: e.type || "couponCollect"
            },
            success: function(e) {
                var n = e.data.message;
                if (n.errno) return t.util.toast(n.message), !1;
                var i = a.dateTimePicker(o.data.startYear, o.data.endYear), u = i.dateTimeArray, d = i.dateTime;
                u.pop(), d.pop(), o.data.starttime = u[0][d[0]] + "-" + u[1][d[1]] + "-" + u[2][d[2]] + " " + u[3][d[3]] + ":" + u[4][d[4]], 
                o.setData({
                    starttime: o.data.starttime,
                    endtime: o.data.starttime,
                    dateTime: d,
                    dateTimeArray: u,
                    type: n.message.type,
                    page_title: n.message.page_title
                }), wx.setNavigationBarTitle({
                    title: o.data.page_title
                });
            }
        });
    },
    changeDateTime: function(t) {
        this.setData({
            dateTime: t.detail.value
        });
    },
    changeDateTimeColumn: function(t) {
        var e = this, o = e.data.dateTime, n = e.data.dateTimeArray;
        o[t.detail.column] = t.detail.value, n[2] = a.getMonthDay(n[0][o[0]], n[1][o[1]]);
        var i = t.currentTarget.dataset.type, u = n[0][o[0]] + "-" + n[1][o[1]] + "-" + n[2][o[2]] + " " + n[3][o[3]] + ":" + n[4][o[4]];
        "starttime" == i ? e.setData({
            starttime: u
        }) : "endtime" == i && e.setData({
            endtime: u
        });
    },
    onInput: function(t) {
        var a = this, e = t.currentTarget.dataset.type, o = t.detail.value;
        a.data.coupon[e] = o;
    },
    onSelectGroup: function(t) {
        var a = this, e = t.detail.value;
        a.setData({
            type_limit: e
        });
    },
    onAddCoupon: function(a) {
        var e = this;
        return "couponGrant" == e.data.type && e.data.coupons.length >= 1 ? (t.util.toast("最多添加1张代金券", "", 1e3), 
        !1) : e.data.coupons.length >= 3 ? (t.util.toast("最多添加3张代金券", "", 1e3), !1) : "cancel" == a.currentTarget.dataset.type ? (e.setData({
            addCoupon: !1
        }), !1) : void e.setData({
            addCoupon: !0
        });
    },
    onSaveCoupon: function() {
        var a = this;
        return !a.data.coupon.discount || a.data.coupon.discount < 0 ? (t.util.toast("请填写优惠金额", "", 1e3), 
        !1) : !a.data.coupon.condition || a.data.coupon.condition < 0 || a.data.coupon.condition < a.data.coupon.discount ? (t.util.toast("请填写使用条件", "", 1e3), 
        !1) : !a.data.coupon.use_days_limit || a.data.coupon.use_days_limit < 0 ? (t.util.toast("请填写有效期", "", 1e3), 
        !1) : (a.data.coupons.push(a.data.coupon), void a.setData({
            coupons: a.data.coupons,
            addCoupon: !1
        }));
    },
    onDelCoupon: function(t) {
        var a = this, e = t.currentTarget.dataset.index;
        a.data.coupons.splice(e, 1), a.setData({
            coupons: a.data.coupons
        });
    },
    onEditCoupon: function(t) {
        var a = this, e = t.currentTarget.dataset.index;
        a.data.coupon = a.data.coupons[e], a.setData({
            coupon: a.data.coupon,
            addCoupon: !0
        });
    },
    onSubmit: function(a) {
        var e = this, o = a.detail.value;
        return o.title ? o.amount ? "couponCollect" != e.data.type || o.type_limit ? e.data.coupons.length ? (o.coupons = e.data.coupons, 
        o.starttime = e.data.starttime, o.endtime = e.data.endtime, void t.util.request({
            url: "manage/activity/index/activity_coupon",
            data: {
                type: e.data.type,
                params: JSON.stringify(o),
                formid: a.detail.formId
            },
            method: "POST",
            success: function(a) {
                var e = a.data.message;
                if (t.util.toast(e.message), e.errno) return !1;
                wx.navigateTo({
                    url: "./index"
                });
            }
        })) : (t.util.toast("请添加优惠券", "", 1e3), !1) : (t.util.toast("请选择面向人群", "", 1e3), 
        !1) : (t.util.toast("券包总数不能为空", "", 1e3), !1) : (t.util.toast("活动名称不能为空", "", 1e3), 
        !1);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});