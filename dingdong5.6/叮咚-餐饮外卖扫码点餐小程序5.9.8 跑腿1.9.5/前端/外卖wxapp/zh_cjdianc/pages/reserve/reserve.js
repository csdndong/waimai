var form_id, app = getApp(), util = require("../../utils/util.js");

Page({
    data: {
        date: "",
        index: 0,
        jindex: 0,
        time: "12:00",
        array: [],
        jcrsarray: [ "1人", "2人", "3人", "4人", "5人", "6人", "7人", "8人", "9人", "10人", "10人以上" ],
        showModal: !1,
        zftype: !0,
        zffs: 1,
        zfz: !1,
        zfwz: "微信支付",
        btntype: "btn_ok1",
        mdoaltoggle: !0,
        radioItems: [ {
            name: "只订座",
            value: "0",
            checked: !0
        }, {
            name: "提前选商品",
            value: "1"
        } ],
        items: [ {
            name: "先生",
            value: 1,
            checked: !0
        }, {
            name: "女士",
            value: 2
        } ]
    },
    radioChange1: function(e) {
        console.log("radio111发生change事件，携带value值为：", e.detail.value);
        for (var t = this.data.radioItems, a = 0, o = t.length; a < o; ++a) t[a].checked = t[a].value == e.detail.value;
        this.setData({
            radioItems: t
        });
    },
    xszz: function() {
        this.setData({
            showModal: !0
        });
    },
    yczz: function() {
        this.setData({
            showModal: !1
        });
    },
    radioChange: function(e) {
        console.log("radio发生change事件，携带value值为：", e.detail.value), this.setData({
            zflx: e.detail.value
        }), "wxzf" == e.detail.value && this.setData({
            zffs: 1,
            zfwz: "微信支付",
            btntype: "btn_ok1"
        }), "yezf" == e.detail.value && this.setData({
            zffs: 2,
            zfwz: "余额支付",
            btntype: "btn_ok2"
        }), "jfzf" == e.detail.value && this.setData({
            zffs: 3,
            zfwz: "积分支付",
            btntype: "btn_ok3"
        });
    },
    bindPickerChange: function(e) {
        console.log("picker发送选择改变，携带值为", e.detail.value), this.setData({
            index: e.detail.value
        });
    },
    bindDateChange: function(e) {
        console.log("picker发送选择改变，携带值为", e.detail.value), this.setData({
            date: e.detail.value
        });
    },
    bindTimeChange: function(e) {
        console.log("picker发送选择改变，携带值为", e.detail.value), this.setData({
            time: e.detail.value
        });
    },
    bindjcrsChange: function(e) {
        console.log("picker发送选择改变，携带值为", e.detail.value), this.setData({
            jindex: e.detail.value
        });
    },
    formSubmit: function(e) {
        var t = this, a = wx.getStorageSync("users").id;
        form_id = e.detail.formId, t.setData({
            form_id: form_id
        }), app.util.request({
            url: "entry/wxapp/AddFormId",
            cachetime: "0",
            data: {
                user_id: a,
                form_id: e.detail.formId
            },
            success: function(e) {
                console.log(e.data);
            }
        }), console.log("form发生了submit事件，携带数据为：", e.detail.value);
        var o = this.data.array;
        if (console.log(o), 0 != o.length) {
            var i = getApp().getOpenId, s = this.data.store.name, l = this.data.store.id, n = this.data.StoreInfo.storeset.is_yydc, r = e.detail.value.datepicker, d = e.detail.value.timepicker, c = e.detail.value.lxr, u = e.detail.value.jcrs, p = e.detail.value.tel, f = e.detail.value.beizhu, g = e.detail.value.radioChange1;
            if (null != e.detail.value.zwpicker) var h = e.detail.value.zwpicker.name, m = e.detail.value.zwpicker.id, v = (e.detail.value.zwpicker.zd_cost, 
            parseFloat(e.detail.value.zwpicker.yd_cost)); else h = "", m = "", v = 0;
            if ("1" == n && "1" == g) var w = (v + Number(this.data.gwcprice)).toFixed(2); else w = v;
            if ("1" != g || 0 != this.data.cart_list.length) {
                console.log(w, i, s, a, l, r, d, h, m, c, u, p, v, f, g, n), t.setData({
                    totalprice: w,
                    ydcost: v,
                    forminfo: e.detail.value
                });
                var y = "", z = !0;
                "" == c ? y = "请填写您的姓名！" : "" == u ? y = "请选择您的就餐人数" : "" == p ? y = "请填写您的手机号！" : p.length < 7 ? y = "手机号格式不正确" : (z = !1, 
                t.setData({
                    showModal: !0
                })), 1 == z && wx.showModal({
                    title: "提示",
                    content: y
                });
            } else wx.showModal({
                title: "提示",
                content: "请前往选择商品"
            });
        } else wx.showModal({
            title: "提示",
            content: "商家暂未添加桌位类型，暂时不能预定"
        });
    },
    qdzf: function(e) {
        var a = this, t = this.data.forminfo, o = this.data.zffs, i = getApp().getOpenId, s = this.data.store.name, l = wx.getStorageSync("users").id;
        if ("2" == o) {
            var n = Number(this.data.userInfo.wallet), r = Number(this.data.totalprice);
            if (console.log(n, r), n < r) return void wx.showToast({
                title: "余额不足支付",
                icon: "loading"
            });
        }
        app.util.request({
            url: "entry/wxapp/AddFormId",
            cachetime: "0",
            data: {
                user_id: l,
                form_id: e.detail.formId
            },
            success: function(e) {
                console.log(e.data);
            }
        });
        var d = this.data.store.id, c = this.data.StoreInfo.storeset.is_yydc, u = this.data.date, p = this.data.time, f = t.lxr, g = t.jcrs, h = t.tel, m = t.beizhu, v = t.radioChange1, w = t.sexradiogroup;
        if (null != t.zwpicker) var y = t.zwpicker.name, z = t.zwpicker.id, _ = (t.zwpicker.zd_cost, 
        parseFloat(t.zwpicker.yd_cost)); else y = "", z = "", _ = 0;
        var x = [], D = this.data.cart_list, k = this.data.totalprice;
        "1" == v && D.map(function(e) {
            if (0 < e.num) {
                var t = {};
                t.name = e.name, t.img = e.logo, t.num = e.num, t.money = e.money, t.dishes_id = e.good_id, 
                t.spec = e.spec, x.push(t);
            }
        }), console.log(k, D, x), console.log(i, s, l, d, u, p, y, z, f, g, h, _, m, v, c, w), 
        console.log(t, form_id, o), this.setData({
            zfz: !0
        }), app.util.request({
            url: "entry/wxapp/AddYyOrder",
            cachetime: "0",
            data: {
                store_id: d,
                user_id: l,
                delivery_time: u + " " + p,
                pay_type: o,
                sex: w,
                sz: x,
                table_id: z,
                deposit: _,
                name: f,
                tel: h,
                tableware: g,
                note: m,
                money: k
            },
            success: function(e) {
                console.log(e);
                var t = e.data;
                "下单失败" != e.data ? (a.setData({
                    zfz: !1,
                    showModal: !1
                }), 2 == o && (console.log("余额支付流程"), a.setData({
                    mdoaltoggle: !1
                }), setTimeout(function() {
                    wx.redirectTo({
                        url: "reserveinfo?oid=" + t
                    });
                }, 1e3)), 1 == o && (console.log("微信支付流程"), 0 < Number(k) ? app.util.request({
                    url: "entry/wxapp/pay",
                    cachetime: "0",
                    data: {
                        openid: i,
                        money: k,
                        order_id: t
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
                                console.log(e), "requestPayment:fail cancel" == e.errMsg && (wx.showToast({
                                    title: "取消支付",
                                    icon: "loading"
                                }), a.setData({
                                    zfz: !1
                                })), "requestPayment:ok" == e.errMsg && (a.setData({
                                    mdoaltoggle: !1
                                }), setTimeout(function() {
                                    wx.redirectTo({
                                        url: "reserveinfo?oid=" + t
                                    });
                                }, 1e3));
                            }
                        });
                    }
                }) : (a.setData({
                    mdoaltoggle: !1
                }), setTimeout(function() {
                    wx.redirectTo({
                        url: "reserveinfo?oid=" + t
                    });
                }, 1e3)))) : wx.showToast({
                    title: "请重试",
                    icon: "loading",
                    duration: 1e3
                });
            }
        });
    },
    onLoad: function(e) {
        app.setNavigationBarColor(this);
        var a = this, t = e.storeid, o = util.formatTime(new Date()).substring(0, 10).replace(/\//g, "-"), i = wx.getStorageSync("users").id;
        console.log(t, o.toString(), i), this.setData({
            date: o,
            storeid: t,
            zuid: i
        }), app.util.request({
            url: "entry/wxapp/UserInfo",
            cachetime: "0",
            data: {
                user_id: i
            },
            success: function(e) {
                console.log(e), a.setData({
                    userInfo: e.data,
                    wallet: e.data.wallet,
                    total_score: e.data.total_score
                });
            }
        }), app.util.request({
            url: "entry/wxapp/TableType",
            cachetime: "0",
            data: {
                store_id: t
            },
            success: function(e) {
                console.log(e), a.setData({
                    array: e.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: t,
                type: 2
            },
            success: function(e) {
                console.log(e.data);
                var t = e.data;
                "1" == t.storeset.is_hdfk && a.setData({
                    hdfk: !0
                }), "1" == getApp().xtxx.is_yuepay && "1" == t.storeset.is_yuepay && a.setData({
                    kqyue: !0
                }), a.setData({
                    StoreInfo: t,
                    store: t.store,
                    time: t.store.time
                });
            }
        });
    },
    onReady: function() {},
    onShow: function() {
        var t = this, e = this.data.storeid, a = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/mycar",
            cachetime: "0",
            data: {
                store_id: e,
                user_id: a,
                type: 2
            },
            success: function(e) {
                console.log(e), t.setData({
                    cart_list: e.data.res,
                    gwcinfo: e.data,
                    gwcprice: e.data.money
                });
            }
        });
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {}
});