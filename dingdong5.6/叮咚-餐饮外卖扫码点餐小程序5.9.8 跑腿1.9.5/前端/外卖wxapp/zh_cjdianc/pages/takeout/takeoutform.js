var qqmapsdk, app = getApp(), util = require("../../utils/util.js"), QQMapWX = require("../../utils/qqmap-wx-jssdk.js");

Page({
    data: {
        share_modal_active: !1,
        activeradio: "",
        hbshare_modal_active: !1,
        hbactiveradio: "",
        sjshare_modal_active: !1,
        sjindex: 0,
        radioItems: [],
        timearr: [],
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
        wmtimearray: [ "尽快送达" ],
        cjindex: 0,
        cjarray: [ "1份", "2份", "3份", "4份", "5份", "6份", "7份", "8份", "9份", "10份", "10份以上" ],
        mdoaltoggle: !0,
        total: 0,
        showModal: !1,
        zffs: 1,
        zfz: !1,
        zfwz: "微信支付",
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
    sjshowcart: function() {
        this.setData({
            sjshare_modal_active: !this.data.sjshare_modal_active
        });
    },
    sjclosecart: function() {
        this.setData({
            sjshare_modal_active: !1
        });
    },
    sjradioChange: function(t) {
        console.log("radio发生change事件，携带value值为：", t.detail.value);
        for (var e = this.data.radioItems, a = 0, s = e.length; a < s; ++a) e[a].checked = e[a].id == t.detail.value, 
        e[a].id == t.detail.value && this.setData({
            xztime: this.data.timearr[this.data.sjindex].time + " " + e[a].time
        });
        this.setData({
            radioItems: e,
            sjshare_modal_active: !1
        });
    },
    selectedime: function(t) {
        console.log(t);
        this.setData({
            sjindex: t.currentTarget.dataset.index,
            radioItems: this.data.timearr[t.currentTarget.dataset.index].ej
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
        var e = this.data.psfbf;
        console.log(e), 1 == t.currentTarget.dataset.index ? this.setData({
            psf: 0
        }) : this.setData({
            psf: e
        }), this.gettotalprice();
    },
    checkboxChange: function(t) {
        this.setData({
            checked: !this.data.checked
        });
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
    distance: function(t, e, a) {
        var s;
        qqmapsdk.calculateDistance({
            mode: "driving",
            from: {
                latitude: t.lat,
                longitude: t.lng
            },
            to: [ {
                latitude: e.lat,
                longitude: e.lng
            } ],
            success: function(t) {
                console.log(t), 0 == t.status && (s = Math.round(t.result.elements[0].distance), 
                a(s));
            },
            fail: function(t) {
                console.log(t), 373 == t.status && (s = 15e3, a(s));
            },
            complete: function(t) {
                console.log(t);
            }
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
        });
        var e = this.data.address, a = this.data.selectedindex, s = this.data.storeset;
        if (console.log(e, a, s), 0 == a && null == e && "1" == s.is_ps) return wx.showModal({
            title: "提示",
            content: "请选择收货地址"
        }), !1;
        if (0 != a || this.data.dzisnormall || "1" != s.is_ps) {
            if (0 == a && this.data.dzisnormall && "1" == s.is_ps) this.setData({
                showModal: !0
            }); else if (1 == a || 0 == a && "2" == s.is_ps) {
                var o = this.data.name, i = this.data.mobile, n = this.data.checked;
                if (console.log(o, i), "" == o || "" == i || null == o || null == i) return wx.showModal({
                    title: "提示",
                    content: "到店自提必须填写收货人和联系电话！"
                }), !1;
                if (!n) return wx.showModal({
                    title: "提示",
                    content: "请阅读并同意《到店自取服务协议》"
                }), !1;
                this.setData({
                    showModal: !0
                });
            }
        } else wx.showModal({
            title: "提示",
            content: "当前选择的收货地址超出商家配送距离,请选择其他地址",
            showCancel: !1,
            success: function(t) {
                wx.navigateTo({
                    url: "../wddz/xzdz"
                });
            }
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
        app.setNavigationBarColor(this), console.log(t);
        var e = util.formatTime(new Date()), a = util.formatTime(new Date()).substring(0, 10).replace(/\//g, "-"), s = util.formatTime(new Date()).substring(11, 16);
        console.log(e, a.toString(), s.toString());
        var o = new Date(), i = o.getTime(), n = 2 * (24 - new Date(i).getHours());
        console.log(n, new Date(i), new Date(i).getHours(), new Date(i).getMinutes());
        for (var d = [ "尽快送达" ], r = 1; r < n; r++) {
            i = o.getTime() + 18e5 * r;
            var l = new Date(i).getMinutes();
            l < 10 && (l = "0" + l);
            var c = new Date(i).getHours() + ":" + l;
            d.push(c);
        }
        console.log(d), this.setData({
            datestart: a,
            timestart: s,
            date: a,
            time: s,
            wmtimearray: d
        });
        var u = this, h = t.storeid, m = wx.getStorageSync("users").id;
        wx.removeStorageSync("note"), app.util.request({
            url: "entry/wxapp/GetStoreService",
            cachetime: "0",
            data: {
                store_id: h
            },
            success: function(t) {
                console.log(t), t.data && 0 < t.data.length && (t.data[0].ej[0].checked = !0, u.setData({
                    timearr: t.data,
                    radioItems: t.data[0].ej,
                    xztime: t.data[0].time + " " + t.data[0].ej[0].time
                }));
            }
        }), app.util.request({
            url: "entry/wxapp/UserInfo",
            cachetime: "0",
            data: {
                user_id: m
            },
            success: function(t) {
                var e = util.formatTime(new Date()).substring(0, 10).replace(/\//g, "-");
                console.log(t, e.toString()), "" != t.data.dq_time && t.data.dq_time >= e.toString() && (t.data.ishy = 1), 
                u.setData({
                    userInfo: t.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/MyCoupons",
            cachetime: "0",
            data: {
                store_id: h,
                user_id: m
            },
            success: function(t) {
                console.log(t.data);
                for (var e = [], a = [], s = 0; s < t.data.length; s++) "2" != t.data[s].coupon_type && "1" == t.data[s].type && e.push(t.data[s]), 
                "2" != t.data[s].coupon_type && "2" == t.data[s].type && a.push(t.data[s]);
                console.log(e, a), u.setData({
                    Coupons: e,
                    hbarr: a
                });
            }
        }), app.util.request({
            url: "entry/wxapp/CouponSet",
            cachetime: "0",
            success: function(t) {
                console.log(t.data), u.setData({
                    CouponSet: t.data
                });
            }
        }), qqmapsdk = new QQMapWX({
            key: getApp().xtxx.map_key
        }), u.setData({
            xtxx: getApp().xtxx
        }), app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: h,
                type: 2
            },
            success: function(t) {
                console.log(t.data), t.data.storeset.wmps_name = "" != t.data.storeset.wmps_name ? t.data.storeset.wmps_name : "外卖配送";
                var e = t.data, a = t.data.store.coordinates.split(","), s = {
                    lng: Number(a[1]),
                    lat: Number(a[0])
                };
                console.log(s), "1" == e.storeset.is_ps && "1" == e.storeset.is_zt && u.setData({
                    navbar: [ e.storeset.wmps_name, "到店自取" ]
                }), "2" == e.storeset.is_zt && u.setData({
                    navbar: [ e.storeset.wmps_name ]
                }), "2" == e.storeset.is_ps && u.setData({
                    navbar: [ "到店自取" ]
                }), "1" != e.storeset.is_hdfk && "3" != e.storeset.is_hdfk || u.setData({
                    hdfk: !0
                }), "1" == getApp().xtxx.is_yuepay && "1" == e.storeset.is_yuepay && u.setData({
                    kqyue: !0
                }), u.setData({
                    psfarr: t.data.psf,
                    reduction: t.data.reduction,
                    store: t.data.store,
                    storeset: t.data.storeset,
                    sjstart: s,
                    xynr: t.data.storeset.ztxy
                }), app.util.request({
                    url: "entry/wxapp/MyCar",
                    cachetime: "0",
                    data: {
                        store_id: h,
                        user_id: m
                    },
                    success: function(t) {
                        console.log(t), app.util.request({
                            url: "entry/wxapp/IsNew",
                            data: {
                                store_id: h,
                                user_id: m
                            },
                            cachetime: "0",
                            success: function(t) {
                                console.log(t.data), "1" == e.storeset.xyh_open && 1 == t.data ? u.setData({
                                    xyhmoney: e.storeset.xyh_money,
                                    isnewuser: "1"
                                }) : u.setData({
                                    xyhmoney: 0,
                                    isnewuser: "2"
                                }), u.countMj(), u.countpsf();
                            }
                        }), u.setData({
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
        var t = this.data.gwcprice, e = this.data.gwcinfo.box_money, a = this.data.psf, s = this.data.mjmoney, o = this.data.xyhmoney, i = this.data.yhqkdje, n = this.data.hbkdje;
        var d = (Number(s) + Number(o) + 0 + Number(i) + Number(n)).toFixed(2), r = (Number(t) + Number(a) - d).toFixed(2);
        r < 0 && (r = 0), console.log("gwcprice", t, "bzf", e, "psf", a, "mjmoney", s, "xyhmoney", o, "totalyh", d, "totalPrice", r, "yhqkdje", i, "hbkdje", n, "zkmoney", 0), 
        this.setData({
            totalyh: d,
            totalPrice: r,
            zkmoney: 0,
            isloading: !1
        });
    },
    jsmj: function(t, e) {
        for (var a, s = 0; s < e.length; s++) if (Number(t) >= Number(e[s].full)) {
            a = s;
            break;
        }
        return a;
    },
    countMj: function() {
        var t = this.data.gwcprice, e = this.data.reduction.reverse(), a = this.jsmj(t, e), s = this.data.isnewuser;
        console.log(t, e, a, s);
        var o = 0;
        0 < e.length && null != a && "2" == s && (o = e[a].reduction), this.setData({
            reduction: e,
            mjindex: a,
            mjmoney: o
        });
    },
    countpsf: function() {
        var s = this, t = wx.getStorageSync("users").id, a = s.data.sjstart, o = 1e3 * Number(this.data.storeset.ps_jl), i = this.data.psfarr;
        console.log(i), app.util.request({
            url: "entry/wxapp/MyDefaultAddress",
            cachetime: "0",
            data: {
                user_id: t
            },
            success: function(t) {
                if (console.log(t.data), t.data && "1" == s.data.storeset.is_ps) {
                    var e = {
                        lng: t.data.lng,
                        lat: t.data.lat
                    };
                    console.log(a, e, o), s.setData({
                        address: t.data,
                        mobile: t.data.tel,
                        name: t.data.user_name
                    }), s.distance(a, e, function(t) {
                        o < t ? (s.setData({
                            dzisnormall: !1
                        }), wx.showModal({
                            title: "提示",
                            content: "当前选择的收货地址超出商家配送距离,请选择其他地址",
                            success: function(t) {
                                t.confirm ? (console.log("用户点击确定"), wx.navigateTo({
                                    url: "../wddz/xzdz"
                                })) : t.cancel && console.log("用户点击取消");
                            }
                        })) : s.setData({
                            dzisnormall: !0
                        });
                        var e = (t / 1e3).toFixed(2);
                        console.log(t, o, e);
                        for (var a = i.length - 1; 0 <= a; a--) if (console.log(a), Number(e) >= Number(i[a].end) || Number(e) >= Number(i[a].start) && Number(e) < Number(i[a].end)) {
                            console.log(a, i[a].money), s.setData({
                                psf: i[a].money,
                                psfbf: i[a].money
                            }), s.gettotalprice();
                            break;
                        }
                    });
                } else t.data || "1" != s.data.storeset.is_ps ? s.setData({
                    psf: 0,
                    psfbf: 0
                }) : s.setData({
                    psf: i[0].money,
                    psfbf: i[0].money
                }), s.gettotalprice();
            }
        });
    },
    formSubmit: function(a) {
        var s = [];
        this.data.cart_list.map(function(t) {
            if (0 < t.num) {
                var e = {};
                e.name = "1" == t.is_qg ? t.qg_name : t.name, e.img = "1" == t.is_qg ? t.qg_logo : t.logo, 
                e.num = t.num, e.money = t.money, e.dishes_id = t.good_id, e.spec = t.spec, e.is_qg = t.is_qg, 
                s.push(e);
            }
        }), console.log(s);
        var o = this, i = this.data.storeset, n = getApp().getOpenId;
        console.log("form发生了submit事件，携带数据为：", a.detail.value.radiogroup, this.data.activeradio, this.data.hbactiveradio);
        var t, d = a.detail.formId, e = this.data.form_id2, r = wx.getStorageSync("users").id, l = this.data.store.id, c = this.data.totalPrice, u = this.data.totalyh, h = this.data.gwcinfo.box_money, m = this.data.psf, g = this.data.mjmoney, f = this.data.xyhmoney, p = this.data.note, y = this.data.cjarray[this.data.cjindex], w = this.data.yhqkdje, _ = this.data.activeradio, v = this.data.hbactiveradio, x = this.data.hbkdje, b = this.data.zkmoney;
        if ("hdfk" == a.detail.value.radiogroup && "3" == i.is_hdfk && Number(m) <= 0) wx.showModal({
            title: "提示",
            content: "货到付款，配送费不能为0，请选择其他付款方式"
        }); else {
            var D = parseInt(this.data.selectedindex) + 1;
            if ("2" == o.data.storeset.is_ps && (D = 2), t = 2 == D ? 0 < this.data.timearr.length ? this.data.xztime : this.data.date + " " + this.data.time : 0 < this.data.timearr.length ? this.data.xztime : this.data.wmtimearray[this.data.wmindex], 
            null != this.data.address) var k = this.data.address.area.replace(/,/g, "") + this.data.address.address, j = this.data.address.user_name, z = this.data.address.tel, q = this.data.address.sex, T = this.data.address.area, S = this.data.address.lat, M = this.data.address.lng; else k = "", 
            j = "", z = "", q = "0", T = "", S = "", M = "";
            if (2 == D && (j = o.data.name, z = this.data.mobile, "" == j || "" == z)) return wx.showModal({
                title: "提示",
                content: "到店自提必须填写收货人和联系电话！"
            }), !1;
            if (console.log(n, d, e, r, l, "实付", c, "总优惠", u, "包装费", h, "运费", m, "满减金额", g, "新用户money", f, "优惠券", w, "红包", x, "折扣", b, "订单类型", D, T, S, M, "收货人", j, "收获电话", z, "收货地址", k, "留言", p, "sz", s, "配送时间", t, "餐具数量", y, q), 
            "yezf" == a.detail.value.radiogroup) {
                var N = Number(this.data.userInfo.wallet), C = Number(c);
                if (console.log(N, C), N < C) return void wx.showToast({
                    title: "余额不足支付",
                    icon: "loading"
                });
            }
            if ("yezf" == a.detail.value.radiogroup) var I = 2;
            if ("wxzf" == a.detail.value.radiogroup) I = 1;
            if ("jfzf" == a.detail.value.radiogroup) I = 3;
            if ("hdfk" == a.detail.value.radiogroup) I = 4;
            console.log("支付方式", I), "" == d ? wx.showToast({
                title: "没有获取到formid",
                icon: "loading",
                duration: 1e3
            }) : (this.setData({
                zfz: !0
            }), app.util.request({
                url: "entry/wxapp/AddOrder",
                cachetime: "0",
                method: "POST",
                data: {
                    user_id: r,
                    store_id: l,
                    money: c,
                    discount: u,
                    box_money: h,
                    ps_money: m,
                    mj_money: g,
                    xyh_money: f,
                    tel: z,
                    name: j,
                    address: k,
                    note: p,
                    type: 1,
                    area: T,
                    lat: S,
                    lng: M,
                    form_id: d,
                    form_id2: e,
                    delivery_time: t,
                    order_type: D,
                    pay_type: I,
                    sz: JSON.stringify(s),
                    tableware: y,
                    sex: q,
                    yhq_money: w,
                    yhq_money2: x,
                    coupon_id: _,
                    coupon_id2: v,
                    zk_money: b
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
                        wx.reLaunch({
                            url: "../wddd/order?status=2"
                        });
                    }, 1e3)) : wx.showToast({
                        title: "支付失败",
                        icon: "loading"
                    })), "hdfk" == a.detail.value.radiogroup && "1" == i.is_hdfk && (console.log("货到付款流程"), 
                    "下单失败" != e ? (o.setData({
                        mdoaltoggle: !1
                    }), setTimeout(function() {
                        wx.reLaunch({
                            url: "../wddd/order?status=2"
                        });
                    }, 1e3)) : wx.showToast({
                        title: "支付失败",
                        icon: "loading"
                    })), "hdfk" == a.detail.value.radiogroup && "3" == i.is_hdfk && (console.log("货到付款3流程"), 
                    "下单失败" != e ? app.util.request({
                        url: "entry/wxapp/pay",
                        cachetime: "0",
                        data: {
                            openid: n,
                            money: m,
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
                                    console.log(t.data), console.log(t), console.log(d);
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
                                    }, 1e3)), "requestPayment:ok" == t.errMsg && (o.setData({
                                        mdoaltoggle: !1
                                    }), setTimeout(function() {
                                        wx.reLaunch({
                                            url: "../wddd/order?status=2"
                                        });
                                    }, 1e3));
                                }
                            });
                        }
                    }) : wx.showToast({
                        title: "支付失败",
                        icon: "loading"
                    })), "wxzf" == a.detail.value.radiogroup && (console.log("微信支付流程"), 0 == c ? (wx.showModal({
                        title: "提示",
                        content: "0元买单请选择其他方式支付"
                    }), o.setData({
                        zfz: !1
                    })) : "下单失败" != e && app.util.request({
                        url: "entry/wxapp/pay",
                        cachetime: "0",
                        data: {
                            openid: n,
                            money: c,
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
                                    console.log(t.data), console.log(t), console.log(d);
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
                                    }, 1e3)), "requestPayment:ok" == t.errMsg && (o.setData({
                                        mdoaltoggle: !1
                                    }), setTimeout(function() {
                                        wx.reLaunch({
                                            url: "../wddd/order?status=2"
                                        });
                                    }, 1e3));
                                }
                            });
                        }
                    }));
                }
            }));
        }
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