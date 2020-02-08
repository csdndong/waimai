/*   time:2019-07-18 01:07:50*/
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
        })
    },
    xszz: function() {
        var t = this.data.userinfo;
        console.log(t), "" == t.img || "" == t.name ? wx.navigateTo({
            url: "../smdc/getdl"
        }) : this.setData({
            showModal: !0
        })
    },
    yczz: function() {
        this.setData({
            showModal: !1
        })
    },
    money: function(t) {
        var e;
        console.log(t.detail.value), e = "" != t.detail.value ? t.detail.value : 0, this.setData({
            money: parseFloat(e).toFixed(2)
        })
    },
    formSubmit: function(e) {
        var a = this;
        form_id = e.detail.formId, a.setData({
            form_id: form_id
        });
        var o = this.data.userinfo.openid,
            t = this.data.userinfo.id,
            s = this.data.money,
            i = this.data.storeinfo.store.id;
        if (console.log(o, t, s, i), 0 == s) return wx.showModal({
            title: "提示",
            content: "付款金额不能等于0"
        }), !1;
        if (console.log("form发生了submit事件，携带数据为：", e.detail.value.radiogroup), "yezf" == e.detail.value.radiogroup) {
            var n = Number(this.data.wallet);
            s = Number(this.data.money);
            if (console.log(n, s), n < s) return void wx.showToast({
                title: "余额不足支付",
                icon: "loading"
            })
        }
        var l = 0;
        if ("jfzf" == e.detail.value.radiogroup) {
            var r = Number(this.data.total_score) / Number(this.data.jf_proportion);
            if (l = (s = Number(this.data.money)) * Number(this.data.jf_proportion), console.log(r, s, l), r < s) return void wx.showToast({
                title: "积分不足支付",
                icon: "loading"
            })
        }
        if ("yezf" == e.detail.value.radiogroup) var d = 2;
        if ("wxzf" == e.detail.value.radiogroup) d = 1;
        if ("jfzf" == e.detail.value.radiogroup) d = 3;
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
                money: s,
                store_id: i,
                user_id: t,
                pay_type: d
            },
            success: function(t) {
                a.setData({
                    zfz: !1,
                    showModal: !1
                }), console.log(t), "下单失败" != t.data && ("yezf" == e.detail.value.radiogroup ? (console.log("余额支付流程"), wx.redirectTo({
                    url: "/zh_cjdianc/pages/seller/fukuan?storeid=" + i
                }), wx.showModal({
                    title: "提示",
                    content: "支付成功"
                })) : "jfzf" == e.detail.value.radiogroup ? console.log("积分支付流程") : (console.log("微信支付流程"), app.util.request({
                    url: "entry/wxapp/pay",
                    cachetime: "0",
                    data: {
                        openid: o,
                        money: s,
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
                                console.log(t.data), console.log(t), console.log(form_id)
                            },
                            complete: function(t) {
                                console.log(t), "requestPayment:fail cancel" == t.errMsg && wx.showToast({
                                    title: "取消支付",
                                    icon: "loading",
                                    duration: 1e3
                                }), "requestPayment:ok" == t.errMsg && (wx.redirectTo({
                                    url: "/zh_cjdianc/pages/seller/fukuan?storeid=" + i
                                }), wx.showModal({
                                    title: "提示",
                                    content: "支付成功"
                                }))
                            }
                        })
                    }
                })))
            }
        }))
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this);
        var a = this;
        console.log(t);
        var e = decodeURIComponent(t.scene);
        if ("undefined" != e) {
            console.log("扫码进入");
            var o = e
        } else o = t.storeid;
        console.log("scene", e, o), app.getUserInfo(function(t) {
            console.log(t), a.onShow1(), a.setData({
                userinfo: t
            })
        }), app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: o
            },
            success: function(t) {
                console.log(t);
                var e = t.data;
                a.setData({
                    storeinfo: t.data
                }), "1" == getApp().xtxx.is_yuepay && "1" == e.storeset.is_yuepay && a.setData({
                    kqyue: !0
                })
            }
        }), app.util.request({
            url: "entry/wxapp/Url",
            cachetime: "0",
            success: function(t) {
                a.setData({
                    url: t.data
                })
            }
        }), app.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(t) {
                console.log(t), a.setData({
                    ptxx: t.data,
                    jf_proportion: t.data.jf_proportion
                }), "1" == t.data.is_yue ? a.setData({
                    ptkqyue: !0
                }) : a.setData({
                    ptkqyue: !1
                }), "1" == t.data.is_jfpay ? a.setData({
                    ptkqjf: !0
                }) : a.setData({
                    ptkqjf: !1
                })
            }
        })
    },
    onReady: function() {},
    onShow1: function() {
        var e = this,
            t = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/UserInfo",
            cachetime: "0",
            data: {
                user_id: t
            },
            success: function(t) {
                console.log(t), e.setData({
                    wallet: t.data.wallet,
                    total_score: t.data.total_score
                })
            }
        })
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh()
    },
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});