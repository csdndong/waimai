var qqmapsdk, app = getApp(), util = require("../../utils/util.js"), QQMapWX = require("../../utils/qqmap-wx-jssdk.js");

Page({
    data: {
        share_modal_active: !1,
        activeradio: "",
        hbshare_modal_active: !1,
        hbactiveradio: "",
        isloading: !0,
        navbar: [ "外卖配送", "到店自取" ],
        fwxy: !0,
        xymc: "到店自取服务协议",
        xynr: "",
        selectedindex: 0,
        color: "#019fff",
        checked: !1,
        cart_list: [],
        wmindex: 0,
        wmtimearray: [ "尽快送达" ],
        cjindex: 0,
        cjarray: [ "1份", "2份", "3份", "4份", "5份", "6份", "7份", "8份", "9份", "10份", "10份以上" ],
        mdoaltoggle: !0,
        total: 0,
        showModal: !1,
        zfz: !1,
        zfwz: "",
        btntype: "btn_ok1",
        yhqkdje: 0,
        hbkdje: 0
    },
    showcart: function() {
        this.setData({
            share_modal_active: !this.data.share_modal_active
        });
    },
    closecart: function() {
        this.setData({
            share_modal_active: !1
        });
    },
    hbshowcart: function() {
        this.setData({
            hbshare_modal_active: !this.data.hbshare_modal_active
        });
    },
    hbclosecart: function() {
        this.setData({
            hbshare_modal_active: !1
        });
    },
    openxy: function() {
        this.setData({
            fwxy: !1
        });
    },
    queren: function() {
        this.setData({
            fwxy: !0
        });
    },
    bindPickerChange: function(t) {
        console.log("picker发送选择改变，携带值为", t.detail.value), this.setData({
            wmindex: t.detail.value
        });
    },
    bindcjPickerChange: function(t) {
        console.log("picker发送选择改变，携带值为", t.detail.value), this.setData({
            cjindex: t.detail.value
        });
    },
    selectednavbar: function(t) {
        console.log(t);
        this.setData({
            selectedindex: t.currentTarget.dataset.index
        });
        var a = this.data.psfbf;
        console.log(a), 1 == t.currentTarget.dataset.index ? this.setData({
            psf: 0
        }) : this.setData({
            psf: a
        }), this.gettotalprice();
    },
    checkboxChange: function(t) {
        this.setData({
            checked: !this.data.checked
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
        }), "chzf" == t.detail.value && this.setData({
            zffs: 4,
            zfwz: "餐后支付",
            btntype: "btn_ok4"
        });
    },
    KeyName: function(t) {
        this.setData({
            name: t.detail.value
        });
    },
    KeyMobile: function(t) {
        this.setData({
            mobile: t.detail.value
        });
    },
    tjddformSubmit: function(t) {
        console.log(t), this.setData({
            form_id2: t.detail.formId
        }), this.setData({
            showModal: !0
        });
    },
    yczz: function() {
        this.setData({
            showModal: !1
        });
    },
    mdoalclose: function() {
        this.setData({
            mdoaltoggle: !0
        });
    },
    bindDateChange: function(t) {
        console.log("date 发生 change 事件，携带值为", t.detail.value, this.data.datestart), this.setData({
            date: t.detail.value
        }), t.detail.value == this.data.datestart ? (console.log("日期没有修改"), this.setData({
            timestart: util.formatTime(new Date()).substring(11, 16)
        })) : (console.log("修改了日期"), this.setData({
            timestart: "00:01"
        }));
    },
    bindTimeChange: function(t) {
        console.log("time 发生 change 事件，携带值为", t.detail.value), this.setData({
            time: t.detail.value
        });
    },
    radioChange: function(t) {
        this.setData({
            radioChange: t.detail.value
        }), console.log("radio发生change事件，携带value值为：", t.detail.value);
    },
    hbradioChange: function(t) {
        this.setData({
            hbradioChange: t.detail.value
        }), console.log("radio发生change事件，携带value值为：", t.detail.value);
    },
    xzq: function(t) {
        if (console.log(t.currentTarget.dataset, this.data.gwcinfo.money, this.data.CouponSet.yhq_set), 
        Number(t.currentTarget.dataset.full) > this.data.gwcinfo.money) return wx.showModal({
            title: "提示",
            content: "您的消费金额不满足此优惠券条件"
        }), !1;
        "1" == this.data.CouponSet.yhq_set ? this.setData({
            share_modal_active: !1,
            activeradio: t.currentTarget.dataset.rdid,
            yhqkdje: t.currentTarget.dataset.kdje
        }) : this.setData({
            share_modal_active: !1,
            activeradio: t.currentTarget.dataset.rdid,
            yhqkdje: t.currentTarget.dataset.kdje,
            hbactiveradio: "",
            hbkdje: 0
        }), this.gettotalprice();
    },
    xzhb: function(t) {
        if (console.log(t.currentTarget.dataset, this.data.gwcinfo.money, this.data.CouponSet.yhq_set), 
        Number(t.currentTarget.dataset.full) > this.data.gwcinfo.money) return wx.showModal({
            title: "提示",
            content: "您的消费金额不满足此红包条件"
        }), !1;
        "1" == this.data.CouponSet.yhq_set ? this.setData({
            hbshare_modal_active: !1,
            hbactiveradio: t.currentTarget.dataset.rdid,
            hbkdje: t.currentTarget.dataset.kdje
        }) : (wx.showModal({
            title: "提示",
            content: "优惠券与红包不可同时享用"
        }), this.setData({
            hbshare_modal_active: !1,
            hbactiveradio: t.currentTarget.dataset.rdid,
            hbkdje: t.currentTarget.dataset.kdje,
            activeradio: "",
            yhqkdje: 0
        })), this.gettotalprice();
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this), console.log(t), this.setData({
            tableid: t.tableid,
            drid: t.drid
        });
        var i, s = this, n = t.storeid, d = wx.getStorageSync("users").id;
        null != t.drid && (i = "mycar2"), null == t.drid && (i = "mycar"), console.log(i), 
        wx.removeStorageSync("note"), app.util.request({
            url: "entry/wxapp/UserInfo",
            cachetime: "0",
            data: {
                user_id: d
            },
            success: function(t) {
                console.log(t), s.setData({
                    userInfo: t.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/Zhuohao",
            cachetime: "0",
            data: {
                id: t.tableid
            },
            success: function(t) {
                console.log(t), s.setData({
                    type_name: t.data.type_name,
                    table_name: t.data.table_name
                });
            }
        }), app.util.request({
            url: "entry/wxapp/MyCoupons",
            cachetime: "0",
            data: {
                store_id: n,
                user_id: d
            },
            success: function(t) {
                console.log(t.data);
                for (var a = [], e = [], o = 0; o < t.data.length; o++) "1" != t.data[o].coupon_type && "1" == t.data[o].type && a.push(t.data[o]), 
                "1" != t.data[o].coupon_type && "2" == t.data[o].type && e.push(t.data[o]);
                console.log(a, e), s.setData({
                    Coupons: a,
                    hbarr: e
                });
            }
        }), app.util.request({
            url: "entry/wxapp/CouponSet",
            cachetime: "0",
            success: function(t) {
                console.log(t.data), s.setData({
                    CouponSet: t.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: n,
                type: 1
            },
            success: function(t) {
                console.log(t.data);
                var a = t.data, e = t.data.store.coordinates.split(","), o = {
                    lng: Number(e[1]),
                    lat: Number(e[0])
                };
                console.log(o), "2" == a.storeset.is_zt && s.setData({
                    navbar: [ "外卖配送" ]
                }), "1" == a.storeset.is_chzf && s.setData({
                    chzf: !0
                }), "1" == a.storeset.is_wxzf && s.setData({
                    wxzf: !0
                }), "1" == getApp().xtxx.is_yuepay && "1" == a.storeset.is_yuepay && s.setData({
                    kqyue: !0
                }), s.setData({
                    psfarr: t.data.psf,
                    reduction: t.data.reduction,
                    store: t.data.store,
                    storeset: t.data.storeset,
                    sjstart: o,
                    xynr: t.data.storeset.ztxy
                }), app.util.request({
                    url: "entry/wxapp/" + i,
                    cachetime: "0",
                    data: {
                        store_id: n,
                        user_id: d,
                        type: 2
                    },
                    success: function(t) {
                        console.log(t), app.util.request({
                            url: "entry/wxapp/IsNew",
                            data: {
                                store_id: n,
                                user_id: d
                            },
                            cachetime: "0",
                            success: function(t) {
                                console.log(t.data), "1" == a.storeset.xyh_open && 1 == t.data ? s.setData({
                                    xyhmoney: a.storeset.xyh_money,
                                    isnewuser: "1"
                                }) : s.setData({
                                    xyhmoney: 0,
                                    isnewuser: "2"
                                }), s.countMj();
                            }
                        }), s.setData({
                            cart_list: t.data.res,
                            gwcinfo: t.data,
                            gwcprice: t.data.money
                        });
                    }
                });
            }
        });
    },
    gettotalprice: function() {
        var t = this.data.gwcprice, a = this.data.mjmoney, e = this.data.xyhmoney, o = this.data.yhqkdje, i = this.data.hbkdje, s = (Number(a) + Number(e) + Number(o) + Number(i)).toFixed(2), n = (Number(t) - s).toFixed(2);
        n < 0 && (n = 0), console.log("gwcprice", t, "mjmoney", a, "xyhmoney", e, "totalyh", s, "totalPrice", n, "yhqkdje", o, "hbkdje", i), 
        this.setData({
            totalyh: s,
            totalPrice: n,
            isloading: !1
        });
    },
    jsmj: function(t, a) {
        for (var e, o = 0; o < a.length; o++) if (Number(t) >= Number(a[o].full)) {
            e = o;
            break;
        }
        return e;
    },
    countMj: function() {
        var t = this.data.gwcprice, a = this.data.reduction.reverse(), e = this.jsmj(t, a), o = this.data.isnewuser;
        console.log(t, a, e, o);
        var i = 0;
        0 < a.length && null != e && "2" == o && (i = a[e].reduction), this.setData({
            reduction: a,
            mjindex: e,
            mjmoney: i
        }), this.gettotalprice();
    },
    formSubmit: function(e) {
        var t = this.data.zffs;
        if (console.log(e, t), null == t) return wx.showModal({
            title: "提示",
            content: "请选择支付方式"
        }), !1;
        var o = [];
        this.data.cart_list.map(function(t) {
            if (0 < t.num) {
                var a = {};
                a.name = t.name, a.img = t.logo, a.num = t.num, a.money = t.money, a.dishes_id = t.good_id, 
                a.spec = t.spec, o.push(a);
            }
        }), console.log(o);
        var i = this, s = getApp().getOpenId;
        console.log("form发生了submit事件，携带数据为：", e.detail.value.radiogroup, this.data.activeradio, this.data.hbactiveradio);
        var a = this.data.tableid, n = e.detail.formId, d = this.data.form_id2, r = wx.getStorageSync("users").id, l = this.data.store.id, c = this.data.totalPrice, u = this.data.totalyh, h = this.data.gwcinfo.box_money, g = this.data.mjmoney, p = this.data.xyhmoney, f = parseInt(this.data.selectedindex) + 1, m = this.data.note, y = this.data.cjarray[this.data.cjindex], w = this.data.yhqkdje, v = this.data.activeradio, x = this.data.hbactiveradio, _ = this.data.hbkdje;
        if (console.log("桌子id", a, s, n, d, r, l, "实付", c, "总优惠", u, "包装费", h, "满减金额", g, "新用户money", p, "优惠券", w, "红包", _, "订单类型", f, "留言", m, "sz", o, "餐具数量", y), 
        "yezf" == e.detail.value.radiogroup) {
            var b = Number(this.data.userInfo.wallet), D = Number(c);
            if (console.log(b, D), b < D) return void wx.showToast({
                title: "余额不足支付",
                icon: "loading"
            });
        }
        if ("yezf" == e.detail.value.radiogroup) var z = 2;
        if ("wxzf" == e.detail.value.radiogroup) z = 1;
        if ("jfzf" == e.detail.value.radiogroup) z = 3;
        if ("chzf" == e.detail.value.radiogroup) z = 5;
        console.log("支付方式", z), "" == n ? wx.showToast({
            title: "没有获取到formid",
            icon: "loading",
            duration: 1e3
        }) : (this.setData({
            zfz: !0
        }), app.util.request({
            url: "entry/wxapp/AddDnOrder",
            cachetime: "0",
            data: {
                table_id: a,
                user_id: r,
                store_id: l,
                money: c,
                discount: u,
                mj_money: g,
                xyh_money: p,
                note: m,
                type: 2,
                form_id: n,
                form_id2: d,
                pay_type: z,
                sz: o,
                tableware: y,
                yhq_money: w,
                yhq_money2: _,
                coupon_id: v,
                coupon_id2: x
            },
            success: function(t) {
                console.log(t);
                var a = t.data;
                i.setData({
                    zfz: !1,
                    showModal: !1
                }), "yezf" == e.detail.value.radiogroup && (console.log("余额支付流程"), "已开台" == a ? wx.showModal({
                    title: "提示",
                    content: "对不起，此桌已开台"
                }) : "下单失败" != a ? (i.setData({
                    mdoaltoggle: !1
                }), setTimeout(function() {
                    wx.reLaunch({
                        url: "../wddd/order?status=4"
                    });
                }, 1e3), null != i.data.drid && (console.log(i.data.drid), app.util.request({
                    url: "entry/wxapp/WcDrShop",
                    cachetime: "0",
                    data: {
                        id: i.data.drid
                    },
                    success: function(t) {
                        console.log(t);
                    }
                }))) : wx.showToast({
                    title: "支付失败",
                    icon: "loading"
                })), "chzf" == e.detail.value.radiogroup && (console.log("餐后支付流程"), "已开台" == a ? wx.showModal({
                    title: "提示",
                    content: "对不起，此桌已开台"
                }) : "下单失败" != a ? (i.setData({
                    mdoaltoggle: !1
                }), setTimeout(function() {
                    wx.reLaunch({
                        url: "../wddd/order"
                    });
                }, 1e3), null != i.data.drid && (console.log(i.data.drid), app.util.request({
                    url: "entry/wxapp/WcDrShop",
                    cachetime: "0",
                    data: {
                        id: i.data.drid
                    },
                    success: function(t) {
                        console.log(t);
                    }
                }))) : wx.showToast({
                    title: "支付失败",
                    icon: "loading"
                })), "wxzf" == e.detail.value.radiogroup && (console.log("微信支付流程"), 0 == c ? (wx.showModal({
                    title: "提示",
                    content: "0元买单请选择其他方式支付"
                }), i.setData({
                    zfz: !1
                })) : "已开台" == a ? wx.showModal({
                    title: "提示",
                    content: "对不起，此桌已开台"
                }) : "下单失败" != a && app.util.request({
                    url: "entry/wxapp/pay",
                    cachetime: "0",
                    data: {
                        openid: s,
                        money: c,
                        order_id: a
                    },
                    success: function(t) {
                        console.log(t), null != i.data.drid && (console.log(i.data.drid), app.util.request({
                            url: "entry/wxapp/WcDrShop",
                            cachetime: "0",
                            data: {
                                id: i.data.drid
                            },
                            success: function(t) {
                                console.log(t);
                            }
                        })), wx.requestPayment({
                            timeStamp: t.data.timeStamp,
                            nonceStr: t.data.nonceStr,
                            package: t.data.package,
                            signType: t.data.signType,
                            paySign: t.data.paySign,
                            success: function(t) {
                                console.log(t.data), console.log(t), console.log(n);
                            },
                            complete: function(t) {
                                console.log(t), "requestPayment:fail cancel" == t.errMsg && (wx.showToast({
                                    title: "取消支付",
                                    icon: "loading",
                                    duration: 1e3
                                }), setTimeout(function() {
                                    wx.reLaunch({
                                        url: "../wddd/order"
                                    });
                                }, 1e3)), "requestPayment:ok" == t.errMsg && (i.setData({
                                    mdoaltoggle: !1
                                }), setTimeout(function() {
                                    wx.reLaunch({
                                        url: "../wddd/order?status=4"
                                    });
                                }, 1e3));
                            }
                        });
                    }
                }));
            }
        }));
    },
    onReady: function() {},
    onShow: function() {
        var t = wx.getStorageSync("note");
        console.log(t), this.setData({
            note: t
        });
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});