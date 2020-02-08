var app = new getApp(), uniacid = app.siteInfo.uniacid;

Page({
    data: {
        array: [ "在线支付" ],
        index: 0,
        time: "立即配送",
        is_select_address: 1,
        address: "",
        userName: "",
        phone: "",
        cartData: [],
        aboutData: [],
        totalPrice: 0,
        time_str: "",
        limit_time: [],
        tag: 2,
        hasfastfood: !1,
        order_id: 0
    },
    onLoad: function(e) {
        var p = this, a = wx.getStorageSync("kundian_ordering_uid"), _ = wx.getStorageSync("selectList"), t = e.totalPrice;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "buy",
                op: "getNewOrder",
                uniacid: uniacid,
                uid: a,
                totalPrice: t
            },
            success: function(e) {
                var a = e.data, t = a.send_time, i = a.aboutData, r = a.deliveryData, d = a.totalPrice, s = a.address, n = "立即配送(预计" + t + "送达）", o = i.in_time.split("-"), u = new Array("在线支付");
                1 == r.value && u.push("货到付款");
                var c = 1, l = "";
                s && (c = 2, l = s.region + " " + s.address), p.setData({
                    cartData: _,
                    aboutData: i,
                    totalPrice: d,
                    time: t,
                    time_str: n,
                    limit_time: o,
                    array: u,
                    is_select_address: c,
                    address: l,
                    userName: s.name || "",
                    phone: s.phone || ""
                });
            }
        });
    },
    bindPickerChange: function(e) {
        this.setData({
            index: e.detail.value
        });
    },
    bindTimeChange: function(e) {
        this.setData({
            time_str: e.detail.value
        });
    },
    selectAddress: function(e) {
        wx.navigateTo({
            url: "/kundian_ordering/pages/address/index?is_select=true"
        });
    },
    onShow: function() {
        var e = wx.getStorageSync("kundian_ordering_uid"), a = wx.getStorageSync("selectAdd_" + e);
        a && (this.setData({
            userName: a.name,
            phone: a.phone,
            address: a.region + " " + a.address,
            is_select_address: 2
        }), wx.removeStorageSync("selectAdd_" + e));
    },
    formSubmit: function(e) {
        var t = this, a = t.data, i = a.order_id, r = a.address, d = a.userName, s = a.is_select_address, n = a.time, o = a.index, u = a.phone, c = a.cartData, l = a.totalPrice, p = app.globalData.uid;
        if (0 == i) {
            console.log(o);
            var _ = e.detail.value.remark, m = t.data.hasfastfood ? 0 : 1;
            if (1 == s) return wx.showToast({
                title: "请选择收货地址",
                icon: "none"
            }), !1;
            var x = JSON.stringify(c);
            app.util.request({
                url: "entry/wxapp/order",
                data: {
                    control: "buy",
                    op: "subOrder",
                    uniacid: uniacid,
                    uid: p,
                    address: r,
                    userName: d,
                    phone: u,
                    remark: _,
                    index: o,
                    time: n,
                    cartData: x,
                    totalPrice: l,
                    hasfastfood: m
                },
                method: "POST",
                success: function(e) {
                    var a = e.data.order_id;
                    if (t.setData({
                        order_id: a
                    }), 1 == o) app.util.request({
                        url: "entry/wxapp/order",
                        data: {
                            control: "buy",
                            op: "notify",
                            order_id: a,
                            uniacid: uniacid
                        },
                        success: function(e) {
                            wx.showModal({
                                title: "提示",
                                content: "下单成功",
                                showCancel: !1,
                                success: function() {
                                    wx.switchTab({
                                        url: "../../order/index/index?is_active=1"
                                    });
                                }
                            });
                        }
                    }); else if (0 == e.data.code) {
                        wx.removeStorageSync("selectList");
                        app.util.pay("entry/wxapp/pay", a, uniacid, "entry/wxapp/buy", "order/index/index");
                    }
                }
            });
        } else {
            app.util.pay("entry/wxapp/pay", i, uniacid, "entry/wxapp/buy", "order/index/index");
        }
    }
});