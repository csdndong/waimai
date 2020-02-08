var app = new getApp();

Page({
    data: {
        aboutData: []
    },
    onLoad: function(a) {
        var t = this, e = app.siteInfo.uniacid;
        app.globalData.uid ? app.util.request({
            url: "entry/wxapp/transfer",
            data: {
                op: "getIndex",
                uniacid: e
            },
            success: function(a) {
                console.log(a), t.setData({
                    aboutData: a.data.aboutData
                });
            }
        }) : wx.redirectTo({
            url: "../../login/index"
        });
    },
    formSubmit: function(a) {
        console.log("sss");
        var e = app.globalData.uid, n = app.siteInfo.uniacid, t = a.detail.value.price;
        "" == t ? wx.showToast({
            title: "请输入金额"
        }) : app.util.request({
            url: "entry/wxapp/transfer",
            data: {
                op: "doTransfer",
                uid: e,
                uniacid: n,
                price: t
            },
            success: function(a) {
                if (console.log(a), 1 == a.data.code) {
                    var t = a.data.order_id;
                    app.util.request({
                        url: "entry/wxapp/pay",
                        data: {
                            orderid: t,
                            uniacid: n,
                            uid: e
                        },
                        cachetime: "0",
                        success: function(a) {
                            console.log(a), a.data && a.data.data && !a.data.errno ? wx.requestPayment({
                                timeStamp: a.data.data.timeStamp,
                                nonceStr: a.data.data.nonceStr,
                                package: a.data.data.package,
                                signType: "MD5",
                                paySign: a.data.data.paySign,
                                success: function(a) {
                                    console.log(a), "requestPayment:ok" == a.errMsg ? app.util.request({
                                        url: "entry/wxapp/transfer",
                                        data: {
                                            op: "notify",
                                            order_id: t,
                                            uniacid: n
                                        },
                                        success: function(a) {
                                            console.log(a), 1 == a.data.code ? wx.showToast({
                                                title: "支付成功",
                                                success: function(a) {
                                                    wx.switchTab({
                                                        url: "../../index/index/index"
                                                    });
                                                },
                                                fail: function(a) {},
                                                complete: function(a) {}
                                            }) : wx.showToast({
                                                title: "支付失败"
                                            });
                                        }
                                    }) : wx.showToast({
                                        title: "支付失败"
                                    });
                                },
                                fail: function(a) {
                                    console.log("error"), wx.switchTab({
                                        url: "../../index/index/index"
                                    });
                                }
                            }) : wx.switchTab({
                                url: "../../index/index/index"
                            });
                        },
                        fail: function(a) {
                            wx.showModal({
                                title: "系统提示",
                                content: a.data.message ? a.data.message : "错误",
                                showCancel: !1,
                                success: function(a) {
                                    a.confirm && console.log("2");
                                }
                            });
                        }
                    });
                } else wx.showToast({
                    title: "您已取消支付"
                });
            }
        });
    },
    onShareAppMessage: function() {}
});