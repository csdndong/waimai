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
        isPackage: !1
    },
    onLoad: function(e) {
        var c = this, a = wx.getStorageSync("kundian_ordering_uid"), d = wx.getStorageSync("selectList");
        c.setData({
            deskConfig: app.globalData.deskConfig - 0
        });
        var u = e.totalPrice;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "buy",
                op: "getNewOrder",
                uniacid: uniacid,
                uid: a,
                totalPrice: u
            },
            success: function(e) {
                var a = e.data, t = a.send_time, i = a.aboutData, s = a.deliveryData, n = "立即配送(预计" + t + "送达）", r = i.in_time.split("-"), o = [ "在线支付" ];
                1 == s.value && o.push("货到付款"), c.setData({
                    cartData: d,
                    aboutData: i,
                    totalPrice: parseFloat(u) + parseFloat(i.send_price),
                    time: t,
                    time_str: n,
                    limit_time: r,
                    array: o
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
    isPackage: function(e) {
        this.setData({
            isPackage: e.detail.value
        });
    },
    selectAddress: function(e) {
        var a = this;
        wx.chooseAddress({
            success: function(e) {
                a.setData({
                    address: e.provinceName + e.cityName + e.countyName + e.detailInfo,
                    userName: e.userName,
                    phone: e.telNumber,
                    is_select_address: 2
                });
            },
            fail: function(e) {
                wx.showModal({
                    title: "提示",
                    content: "请到设置中开启通讯地址！",
                    success: function(e) {
                        e.confirm && wx.openSetting({
                            success: function(e) {}
                        });
                    }
                });
            }
        });
    },
    formSubmit: function(e) {
        var t = this, a = wx.getStorageSync("kundian_ordering_uid"), i = t.data, s = (i.is_select_address, 
        i.time), n = i.index, r = i.totalPrice, o = i.cartData, c = e.detail.value, d = (c.card_number, 
        c.card_pwd, c.remark), u = this.data.isPackage ? 1 : 0, l = JSON.stringify(o);
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "buy",
                op: "subOrder",
                uniacid: uniacid,
                uid: a,
                remark: d,
                index: n,
                time: s,
                cartData: l,
                hasfastfood: 1,
                isPackage: u,
                totalPrice: r,
                is_fast_food: 1
            },
            method: "POST",
            success: function(e) {
                var a = e.data.order_id;
                if (console.log(e), t.setData({
                    order_id: a
                }), 1 == n) app.util.request({
                    url: "entry/wxapp/order",
                    data: {
                        control: "buy",
                        op: "notify",
                        order_id: a,
                        uniacid: uniacid
                    },
                    success: function(e) {
                        wx.showToast({
                            title: "下单成功",
                            success: function(e) {
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
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});