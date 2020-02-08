var app = getApp(), util = require("../../utils/util.js");

Page({
    data: {
        fwxy: !0,
        radioItems: [],
        showModal: !1,
        zffs: 1,
        zfz: !1,
        zfwz: "微信支付",
        btntype: "btn_ok1",
        isbd: !1,
        bdsjhtext: "验证微信手机号"
    },
    getPhoneNumber: function(t) {
        var e = this;
        console.log(t), console.log(t.detail.iv), console.log(t.detail.encryptedData), "getPhoneNumber:fail user deny" == t.detail.errMsg && wx.showModal({
            title: "提示",
            showCancel: !1,
            content: "您未授权获取您的手机号",
            success: function(t) {}
        }), "getPhoneNumber:ok" == t.detail.errMsg && app.util.request({
            url: "entry/wxapp/Jiemi",
            cachetime: "0",
            data: {
                sessionKey: getApp().getSK,
                data: t.detail.encryptedData,
                iv: t.detail.iv
            },
            success: function(t) {
                console.log("解密后的数据", t), null != t.data.phoneNumber && e.setData({
                    sjh: t.data.phoneNumber,
                    isbd: !0,
                    bdsjhtext: "验证成功"
                });
            }
        });
    },
    yczz: function() {
        this.setData({
            showModal: !1
        });
    },
    radioChange1: function(t) {
        console.log("radio1发生change事件，携带value值为：", t.detail.value), "wxzf" == t.detail.value && this.setData({
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
        }), "hdfk" == t.detail.value && this.setData({
            zffs: 4,
            zfwz: "货到付款",
            btntype: "btn_ok4"
        });
    },
    tjddformSubmit: function(t) {
        console.log(t.detail, "formid", t.detail.formId, this.data.radioItems);
        wx.getStorageSync("users").id;
        0 != this.data.radioItems.length ? "" != t.detail.value.lxr && "" != t.detail.value.tel && "11" == t.detail.value.tel.length ? this.setData({
            showModal: !0,
            lxr: t.detail.value.lxr,
            tel: t.detail.value.tel
        }) : wx.showModal({
            title: "提示",
            content: "请完善会员信息或手机号不正确"
        }) : wx.showModal({
            title: "提示",
            content: "对不起！暂无添加优惠套餐，无法购买"
        });
    },
    radioChange: function(t) {
        console.log("radio发生change事件，携带value值为：", t.detail.value);
        for (var e = this.data.radioItems, a = 0, o = e.length; a < o; ++a) e[a].checked = e[a].id == t.detail.value, 
        e[a].checked && this.setData({
            zfmoney: e[a].money,
            month: e[a].days
        });
        this.setData({
            radioItems: e
        });
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this);
        var e = util.formatTime(new Date()), a = util.formatTime(new Date()).substring(0, 10).replace(/\//g, "-"), o = util.formatTime(new Date()).substring(11, 16);
        console.log(e, a.toString(), o.toString());
        var i = this, n = wx.getStorageSync("users").id;
        this.setData({
            xtxx: getApp().xtxx
        }), "1" == getApp().xtxx.is_yuepay && this.setData({
            kqyue: !0
        }), app.util.request({
            url: "entry/wxapp/GetHyqx",
            cachetime: "0",
            success: function(t) {
                console.log(t.data), 0 < t.data.length && (t.data[0].checked = !0, i.setData({
                    radioItems: t.data,
                    zfmoney: t.data[0].money,
                    month: t.data[0].days
                }));
            }
        }), app.util.request({
            url: "entry/wxapp/UserInfo",
            cachetime: "0",
            data: {
                user_id: n
            },
            success: function(t) {
                console.log(t), "" == t.data.dq_time || t.data.dq_time < a.toString() ? t.data.ishy = 2 : i.setData({
                    kttext: "立即续费"
                }), i.setData({
                    userInfo: t.data,
                    lxr: t.data.user_name,
                    tel: t.data.user_tel
                });
            }
        });
    },
    formSubmit: function(a) {
        var o = this, i = a.detail.formId, n = this.data.userInfo.openid, t = this.data.userInfo.id, l = Number(this.data.zfmoney), e = this.data.month, s = this.data.lxr, d = this.data.tel;
        if (console.log(n, t, l, i, e, s, d), "yezf" == a.detail.value.radiogroup) {
            var r = Number(this.data.userInfo.wallet), u = l;
            if (console.log(r, u), r < u) return void wx.showToast({
                title: "余额不足支付",
                icon: "loading"
            });
        }
        if ("yezf" == a.detail.value.radiogroup) var c = 2;
        if ("wxzf" == a.detail.value.radiogroup) c = 1;
        if ("jfzf" == a.detail.value.radiogroup) c = 3;
        if ("hdfk" == a.detail.value.radiogroup) c = 4;
        console.log("支付方式", c), "" == i ? wx.showToast({
            title: "没有获取到formid",
            icon: "loading",
            duration: 1e3
        }) : (this.setData({
            zfz: !0
        }), app.util.request({
            url: "entry/wxapp/AddHyOrder",
            cachetime: "0",
            data: {
                user_id: t,
                money: l,
                month: e,
                pay_type: c,
                user_name: s,
                user_tel: d
            },
            success: function(t) {
                console.log(t);
                var e = t.data;
                o.setData({
                    zfz: !1,
                    showModal: !1
                }), "yezf" == a.detail.value.radiogroup && (console.log("余额流程"), "下单失败" != e ? (wx.showModal({
                    title: "提示",
                    content: "购买成功"
                }), app.globalData.userInfo = null, setTimeout(function() {
                    wx.navigateBack({});
                }, 1e3)) : wx.showToast({
                    title: "支付失败",
                    icon: "loading"
                })), "hdfk" == a.detail.value.radiogroup && (console.log("货到付款流程"), "下单失败" != e ? (o.setData({
                    mdoaltoggle: !1
                }), setTimeout(function() {
                    wx.reLaunch({
                        url: "../wddd/order"
                    });
                }, 1e3)) : wx.showToast({
                    title: "支付失败",
                    icon: "loading"
                })), "wxzf" == a.detail.value.radiogroup && (console.log("微信支付流程"), 0 == l ? (wx.showModal({
                    title: "提示",
                    content: "0元买单请选择其他方式支付"
                }), o.setData({
                    zfz: !1
                })) : "下单失败" != e && app.util.request({
                    url: "entry/wxapp/pay",
                    cachetime: "0",
                    data: {
                        openid: n,
                        money: l,
                        order_id: e,
                        type: 3
                    },
                    success: function(t) {
                        console.log(t), wx.requestPayment({
                            timeStamp: t.data.timeStamp,
                            nonceStr: t.data.nonceStr,
                            package: t.data.package,
                            signType: t.data.signType,
                            paySign: t.data.paySign,
                            success: function(t) {
                                console.log(t.data), console.log(t), console.log(i);
                            },
                            complete: function(t) {
                                console.log(t), "requestPayment:fail cancel" == t.errMsg && wx.showToast({
                                    title: "取消支付",
                                    icon: "loading",
                                    duration: 1e3
                                }), "requestPayment:ok" == t.errMsg && (wx.showModal({
                                    title: "提示",
                                    content: "购买成功"
                                }), app.globalData.userInfo = null, setTimeout(function() {
                                    wx.navigateBack({});
                                }, 1e3));
                            }
                        });
                    }
                }));
            }
        }));
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});