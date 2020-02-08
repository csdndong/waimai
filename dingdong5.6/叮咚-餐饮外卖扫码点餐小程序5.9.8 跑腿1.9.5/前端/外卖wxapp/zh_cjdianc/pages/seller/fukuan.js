var form_id, app = getApp();

Page({
    data: {
        money: 0,
        qzf: !0,
        showModal: !1,
        zffs: 1,
        zfz: !1,
        zfwz: "微信支付",
        btntype: "btn_ok1"
    },
    radioChange: function(t) {
        console.log("radio发生change事件，携带value值为：", t.detail.value), "wxzf" == t.detail.value && this.setData({
            zffs: 1,
            zfwz: "微信支付",
            btntype: "btn_ok1"
        }), "yezf" == t.detail.value && this.setData({
            zffs: 2,
            zfwz: "余额支付",
            btntype: "btn_ok2"
        }), "jfzf" == t.detail.value && this.setData({
            zffs: 3,
            zfwz: "积分支付",
            btntype: "btn_ok3"
        });
    },
    xszz: function() {
        var t = this.data.userinfo;
        console.log(t), "" == t.img || "" == t.name ? wx.navigateTo({
            url: "../smdc/getdl"
        }) : this.setData({
            showModal: !0
        });
    },
    yczz: function() {
        this.setData({
            showModal: !1
        });
    },
    money: function(t) {
        var o;
        console.log(t.detail.value), o = "" != t.detail.value ? t.detail.value : 0, this.setData({
            money: parseFloat(o).toFixed(2)
        });
    },
    formSubmit: function(o) {
        var e = this;
        form_id = o.detail.formId, e.setData({
            form_id: form_id
        });
        var a = this.data.userinfo.openid, t = this.data.userinfo.id, n = this.data.money, s = this.data.storeinfo.store.id;
        if (console.log(a, t, n, s), 0 == n) return wx.showModal({
            title: "提示",
            content: "付款金额不能等于0"
        }), !1;
        if (console.log("form发生了submit事件，携带数据为：", o.detail.value.radiogroup), "yezf" == o.detail.value.radiogroup) {
            var i = Number(this.data.wallet);
            n = Number(this.data.money);
            if (console.log(i, n), i < n) return void wx.showToast({
                title: "余额不足支付",
                icon: "loading"
            });
        }
        var l = 0;
        if ("jfzf" == o.detail.value.radiogroup) {
            var r = Number(this.data.total_score) / Number(this.data.jf_proportion);
            if (l = (n = Number(this.data.money)) * Number(this.data.jf_proportion), console.log(r, n, l), 
            r < n) return void wx.showToast({
                title: "积分不足支付",
                icon: "loading"
            });
        }
        if ("yezf" == o.detail.value.radiogroup) var d = 2;
        if ("wxzf" == o.detail.value.radiogroup) d = 1;
        if ("jfzf" == o.detail.value.radiogroup) d = 3;
        console.log("pay_type", d), "" == form_id ? wx.showToast({
            title: "没有获取到formid",
            icon: "loading",
            duration: 1e3
        }) : (this.setData({
            zfz: !0
        }), app.util.request({
            url: "entry/wxapp/DmOrder",
            cachetime: "0",
            data: {
                money: n,
                store_id: s,
                user_id: t,
                pay_type: d
            },
            success: function(t) {
                e.setData({
                    zfz: !1,
                    showModal: !1
                }), console.log(t), "下单失败" != t.data && ("yezf" == o.detail.value.radiogroup ? (console.log("余额支付流程"), 
                e.onShow1(), wx.showModal({
                    title: "提示",
                    content: "支付成功"
                })) : "jfzf" == o.detail.value.radiogroup ? console.log("积分支付流程") : (console.log("微信支付流程"), 
                app.util.request({
                    url: "entry/wxapp/pay",
                    cachetime: "0",
                    data: {
                        openid: a,
                        money: n,
                        order_id: t.data
                    },
                    success: function(t) {
                        console.log(t), wx.requestPayment({
                            timeStamp: t.data.timeStamp,
                            nonceStr: t.data.nonceStr,
                            package: t.data.package,
                            signType: t.data.signType,
                            paySign: t.data.paySign,
                            success: function(t) {
                                console.log(t.data), console.log(t), console.log(form_id);
                            },
                            complete: function(t) {
                                console.log(t), "requestPayment:fail cancel" == t.errMsg && wx.showToast({
                                    title: "取消支付",
                                    icon: "loading",
                                    duration: 1e3
                                }), "requestPayment:ok" == t.errMsg && (e.onShow1(), wx.showModal({
                                    title: "提示",
                                    content: "支付成功"
                                }));
                            }
                        });
                    }
                })));
            }
        }));
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this), this.setData({
            money: parseFloat(0).toFixed(2)
        });
        var e = this;
        console.log(t);
        var o = decodeURIComponent(t.scene);
        if ("undefined" != o) {
            console.log("扫码进入");
            var a = o;
        } else a = t.storeid;
        console.log("scene", o, a), app.getUserInfo(function(t) {
            console.log(t), e.onShow1(), e.setData({
                userinfo: t
            });
        }), app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: a
            },
            success: function(t) {
                console.log(t);
                var o = t.data;
                e.setData({
                    storeinfo: t.data
                }), "1" == getApp().xtxx.is_yuepay && "1" == o.storeset.is_yuepay && e.setData({
                    kqyue: !0
                });
            }
        }), app.util.request({
            url: "entry/wxapp/Url",
            cachetime: "0",
            success: function(t) {
                e.setData({
                    url: t.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(t) {
                console.log(t), e.setData({
                    ptxx: t.data,
                    jf_proportion: t.data.jf_proportion
                }), "1" == t.data.is_yue ? e.setData({
                    ptkqyue: !0
                }) : e.setData({
                    ptkqyue: !1
                }), "1" == t.data.is_jfpay ? e.setData({
                    ptkqjf: !0
                }) : e.setData({
                    ptkqjf: !1
                });
            }
        });
    },
    onReady: function() {},
    onShow1: function() {
        var o = this, t = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/UserInfo",
            cachetime: "0",
            data: {
                user_id: t
            },
            success: function(t) {
                console.log(t), o.setData({
                    wallet: t.data.wallet,
                    total_score: t.data.total_score
                });
            }
        });
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});