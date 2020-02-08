/*   time:2019-07-18 01:03:05*/
var qqmapsdk, app = getApp(),
    util = require("../../utils/util.js"),
    QQMapWX = require("../../utils/qqmap-wx-jssdk.js");
Page({
    data: {
        share_modal_active: !1,
        activeradio: "",
        hbshare_modal_active: !1,
        hbactiveradio: "",
        isloading: !0,
        navbar: [],
        fwxy: !0,
        xymc: "到店自取服务协议",
        xynr: "",
        selectedindex: 0,
        color: "#019fff",
        checked: !0,
        cart_list: [],
        wmindex: 0,
        wmtimearray: ["尽快送达"],
        cjindex: 0,
        cjarray: ["1份", "2份", "3份", "4份", "5份", "6份", "7份", "8份", "9份", "10份", "10份以上"],
        mdoaltoggle: !0,
        total: 0,
        showModal: !1,
        zffs: 1,
        zfz: !1,
        zfwz: "微信支付",
        btntype: "btn_ok1",
        yhqkdje: 0,
        hbkdje: 0,
        note: ""
    },
    bindinput: function(t) {
        console.log(t.detail.value);
        this.setData({
            note: t.detail.value
        })
    },
    openxy: function() {
        this.setData({
            fwxy: !1
        })
    },
    queren: function() {
        this.setData({
            fwxy: !0
        })
    },
    checkboxChange: function(t) {
        this.setData({
            checked: !this.data.checked
        })
    },
    ckwz: function(t) {
        console.log(t.currentTarget.dataset.jwd);
        var e = t.currentTarget.dataset.jwd.split(",");
        console.log(e);
        wx.openLocation({
            latitude: Number(e[0]),
            longitude: Number(e[1]),
            name: this.data.store.name,
            address: this.data.store.address
        })
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
        })
    },
    KeyName: function(t) {
        this.setData({
            name: t.detail.value
        })
    },
    KeyMobile: function(t) {
        this.setData({
            mobile: t.detail.value
        })
    },
    tjddformSubmit: function(t) {
        console.log(t);
        var e = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/AddFormId",
            cachetime: "0",
            data: {
                user_id: e,
                form_id: t.detail.formId
            },
            success: function(t) {
                console.log(t.data)
            }
        });
        var a = this.data.name,
            o = this.data.mobile;
        if (console.log(a, o), "" == a || "" == o || null == a || null == o) return wx.showModal({
            title: "提示",
            content: "请填写联系人和联系电话！"
        }), !1;
        this.setData({
            showModal: !0
        })
    },
    yczz: function() {
        this.setData({
            showModal: !1
        })
    },
    mdoalclose: function() {
        this.setData({
            mdoaltoggle: !0
        })
    },
    bindDateChange: function(t) {
        console.log("date 发生 change 事件，携带值为", t.detail.value, this.data.datestart), this.setData({
            date: t.detail.value
        }), t.detail.value == this.data.datestart ? (console.log("日期没有修改"), this.setData({
            timestart: util.formatTime(new Date).substring(11, 16)
        })) : (console.log("修改了日期"), this.setData({
            timestart: "00:01"
        }))
    },
    bindTimeChange: function(t) {
        console.log("time 发生 change 事件，携带值为", t.detail.value), this.setData({
            time: t.detail.value
        })
    },
    radioChange: function(t) {
        this.setData({
            radioChange: t.detail.value
        }), console.log("radio发生change事件，携带value值为：", t.detail.value)
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this), console.log(t);
        var e = util.formatTime(new Date),
            a = util.formatTime(new Date).substring(0, 10).replace(/\//g, "-"),
            o = util.formatTime(new Date).substring(11, 16);
        console.log(e, a.toString(), o.toString());
        var s = new Date,
            i = s.getTime(),
            n = 2 * (24 - new Date(i).getHours());
        console.log(n, new Date(i), new Date(i).getHours(), new Date(i).getMinutes());
        for (var r = ["尽快送达"], d = 1; d < n; d++) {
            i = s.getTime() + 18e5 * d;
            var l = new Date(i).getMinutes();
            l < 10 && (l = "0" + l);
            var u = new Date(i).getHours() + ":" + l;
            r.push(u)
        }
        console.log(r), this.setData({
            datestart: a,
            timestart: o,
            date: a,
            time: o,
            wmtimearray: r
        });
        var c = this,
            g = t.storeid,
            f = t.goodid,
            m = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/UserInfo",
            cachetime: "0",
            data: {
                user_id: m
            },
            success: function(t) {
                var e = util.formatTime(new Date).substring(0, 10).replace(/\//g, "-");
                console.log(t, e.toString()), "" != t.data.dq_time && t.data.dq_time >= e.toString() && (t.data.ishy = 1), c.setData({
                    userInfo: t.data,
                    mobile: t.data.user_tel ? t.data.user_tel : "",
                    name: t.data.user_name ? t.data.user_name : ""
                })
            }
        }), app.util.request({
            url: "entry/wxapp/QgGoodInfo",
            cachetime: "0",
            data: {
                id: f
            },
            success: function(t) {
                console.log(t), t.data.yh = (Number(t.data.price) - Number(t.data.money)).toFixed(1), c.setData({
                    QgGoodInfo: t.data,
                    isloading: !1
                })
            }
        }), app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: g,
                type: 2
            },
            success: function(t) {
                console.log(t.data), t.data.storeset.wmps_name = "" != t.data.storeset.wmps_name ? t.data.storeset.wmps_name : "外卖配送";
                var e = t.data,
                    a = t.data.store.coordinates.split(","),
                    o = {
                        lng: Number(a[1]),
                        lat: Number(a[0])
                    };
                console.log(o), "1" == e.storeset.is_ps && "1" == e.storeset.is_zt && c.setData({
                    navbar: [e.storeset.wmps_name, "到店自取"]
                }), "2" == e.storeset.is_zt && c.setData({
                    navbar: [e.storeset.wmps_name]
                }), "2" == e.storeset.is_ps && c.setData({
                    navbar: ["到店自取"]
                }), "1" != e.storeset.is_hdfk && "3" != e.storeset.is_hdfk || c.setData({
                    hdfk: !0
                }), "1" == getApp().xtxx.is_yuepay && "1" == e.storeset.is_yuepay && c.setData({
                    kqyue: !0
                }), c.setData({
                    psfarr: t.data.psf,
                    reduction: t.data.reduction,
                    store: t.data.store,
                    storeset: t.data.storeset,
                    sjstart: o,
                    xynr: t.data.storeset.ztxy
                })
            }
        })
    },
    formSubmit: function(a) {
        var o = this,
            t = this.data.userInfo.id,
            s = this.data.userInfo.openid,
            e = this.data.name,
            i = this.data.mobile,
            n = this.data.store.id,
            r = this.data.QgGoodInfo.money,
            d = this.data.QgGoodInfo.id,
            l = this.data.QgGoodInfo.logo,
            u = this.data.QgGoodInfo.name,
            c = this.data.note;
        if (console.log(t, s, e, i, n, r, d, l, u, c), app.util.request({
            url: "entry/wxapp/AddFormId",
            cachetime: "0",
            data: {
                user_id: t,
                form_id: a.detail.formId
            },
            success: function(t) {
                console.log(t.data)
            }
        }), "yezf" == a.detail.value.radiogroup) {
            var g = Number(this.data.userInfo.wallet),
                f = Number(r);
            if (console.log(g, f), g < f) return void wx.showToast({
                title: "余额不足支付",
                icon: "loading"
            })
        }
        if ("yezf" == a.detail.value.radiogroup) var m = 2;
        if ("wxzf" == a.detail.value.radiogroup) m = 1;
        if ("jfzf" == a.detail.value.radiogroup) m = 3;
        if ("hdfk" == a.detail.value.radiogroup) m = 4;
        console.log("支付方式", m), this.setData({
            zfz: !0
        }), app.util.request({
            url: "entry/wxapp/QgOrder",
            cachetime: "0",
            data: {
                user_id: t,
                user_name: e,
                user_tel: i,
                store_id: n,
                money: r,
                good_id: d,
                pay_type: m,
                good_logo: l,
                good_name: u,
                note: c
            },
            success: function(t) {
                console.log(t);
                var e = t.data;
                o.setData({
                    zfz: !1,
                    showModal: !1
                }), "yezf" == a.detail.value.radiogroup && (console.log("余额流程"), "下单失败" != e ? (o.setData({
                    mdoaltoggle: !1
                }), setTimeout(function() {
                    wx.redirectTo({
                        url: "order"
                    })
                }, 1e3)) : wx.showToast({
                    title: "支付失败",
                    icon: "loading"
                })), "wxzf" == a.detail.value.radiogroup && (console.log("微信支付流程"), 0 == r ? (wx.showModal({
                    title: "提示",
                    content: "0元买单请选择其他方式支付"
                }), o.setData({
                    zfz: !1
                })) : "下单失败" != e && app.util.request({
                    url: "entry/wxapp/QgPay",
                    cachetime: "0",
                    data: {
                        openid: s,
                        money: r,
                        order_id: e
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
                                }), "requestPayment:ok" == t.errMsg && (o.setData({
                                    mdoaltoggle: !1
                                }), setTimeout(function() {
                                    wx.redirectTo({
                                        url: "order"
                                    })
                                }, 1e3))
                            }
                        })
                    }
                }))
            }
        })
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});