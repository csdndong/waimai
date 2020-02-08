var app = getApp();

Page({
    data: {
        czhd: [],
        activeIndex: 0,
        czmoney: 0
    },
    lookck: function() {
        wx.navigateTo({
            url: "../car/xydtl?title=充值服务协议"
        });
    },
    tabClick: function(e) {
        this.setData({
            activeIndex: e.currentTarget.id,
            czmoney: Number(this.data.czhd[e.currentTarget.id].full)
        });
    },
    tabClick1: function(e) {
        this.setData({
            activeIndex: -1,
            czmoney: 0
        });
    },
    bindinput: function(e) {
        var t;
        console.log(e.detail.value), t = "" != e.detail.value ? e.detail.value : 0, this.setData({
            czmoney: parseFloat(t)
        });
    },
    jsmj: function(e, t) {
        for (var a, o = 0; o < t.length; o++) if (Number(e) >= Number(t[o].full)) {
            a = o;
            break;
        }
        return a;
    },
    tjddformSubmit: function(e) {
        var t = e.detail.formId;
        console.log("form发生了submit事件，携带数据为：", e.detail, e.detail.formId);
        var a = this.data.userinfo.openid, o = this.data.czmoney, n = this.data.czhd, i = this.data.userinfo.id;
        if (console.log(n), Number(o) <= 0) wx.showModal({
            title: "提示",
            content: "充值金额不能小于0"
        }); else {
            if (0 == n.length) var c = o; else if (Number(o) >= Number(this.data.czhd[n.length - 1].full)) {
                var s = this.jsmj(o, n);
                console.log(s);
                c = Number(o) + Number(n[s].reduction);
            } else c = o;
            console.log(a, o, i, c, c - o), app.util.request({
                url: "entry/wxapp/AddFormId",
                cachetime: "0",
                data: {
                    user_id: i,
                    form_id: t
                },
                success: function(e) {
                    console.log(e.data);
                }
            }), wx.showLoading({
                title: "加载中",
                mask: !0
            }), app.util.request({
                url: "entry/wxapp/AddCzorder",
                cachetime: "0",
                data: {
                    user_id: i,
                    money: o,
                    money2: c - o
                },
                success: function(e) {
                    console.log(e);
                    var t = e.data;
                    app.util.request({
                        url: "entry/wxapp/pay",
                        cachetime: "0",
                        data: {
                            openid: a,
                            money: o,
                            order_id: t,
                            type: 2
                        },
                        success: function(e) {
                            console.log(e), wx.requestPayment({
                                timeStamp: e.data.timeStamp,
                                nonceStr: e.data.nonceStr,
                                package: e.data.package,
                                signType: e.data.signType,
                                paySign: e.data.paySign,
                                success: function(e) {
                                    console.log(e);
                                },
                                complete: function(e) {
                                    console.log(e), "requestPayment:fail cancel" == e.errMsg && wx.showToast({
                                        title: "取消支付"
                                    }), "requestPayment:ok" == e.errMsg && (wx.showModal({
                                        title: "提示",
                                        content: "支付成功",
                                        showCancel: !1
                                    }), setTimeout(function() {
                                        wx.navigateBack({});
                                    }, 1e3));
                                }
                            });
                        }
                    });
                }
            });
        }
    },
    onLoad: function(e) {
        app.setNavigationBarColor(this);
        var t = this, a = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/UserInfo",
            cachetime: "0",
            data: {
                user_id: a
            },
            success: function(e) {
                console.log(e), t.setData({
                    wallet: e.data.wallet,
                    userinfo: e.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/Czhd",
            cachetime: "0",
            success: function(e) {
                console.log(e), 0 < e.data.length && t.setData({
                    czhd: e.data,
                    czmoney: e.data[0].full
                });
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});