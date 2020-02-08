var qqmapsdk, app = getApp(), util = require("../../utils/util.js"), QQMapWX = require("../../utils/qqmap-wx-jssdk.js");

Page({
    data: {
        share_modal_active: !1,
        activeradio: "",
        hbshare_modal_active: !1,
        hbactiveradio: "",
        group_id: "",
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
        hbkdje: 0,
        note: ""
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
    checkboxChange: function(e) {
        this.setData({
            checked: !this.data.checked
        });
    },
    ckwz: function(e) {
        console.log(e.currentTarget.dataset.jwd);
        var t = e.currentTarget.dataset.jwd.split(",");
        console.log(t);
        wx.openLocation({
            latitude: Number(t[0]),
            longitude: Number(t[1]),
            name: this.data.store.name,
            address: this.data.store.address
        });
    },
    radioChange1: function(e) {
        console.log("radio1发生change事件，携带value值为：", e.detail.value), "wxzf" == e.detail.value && this.setData({
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
        }), "hdfk" == e.detail.value && this.setData({
            zffs: 4,
            zfwz: "货到付款",
            btntype: "btn_ok4"
        });
    },
    KeyName: function(e) {
        this.setData({
            name: e.detail.value
        });
    },
    KeyMobile: function(e) {
        this.setData({
            mobile: e.detail.value
        });
    },
    note: function(e) {
        this.setData({
            note: e.detail.value
        });
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this), console.log(wx.getStorageSync("users"));
        var o = this, e = t.store_id, a = t.goods_id, n = wx.getStorageSync("users").openid, i = wx.getStorageSync("users").id;
        console.log(t), o.setData({
            user_id: i,
            openid: n,
            options: t
        }), app.util.request({
            url: "entry/wxapp/UserInfo",
            cachetime: "0",
            data: {
                user_id: i
            },
            success: function(e) {
                var t = util.formatTime(new Date()).substring(0, 10).replace(/\//g, "-");
                console.log(e, t.toString()), "" != e.data.dq_time && e.data.dq_time >= t.toString() && (e.data.ishy = 1), 
                o.setData({
                    userInfo: e.data,
                    mobile: e.data.user_tel ? e.data.user_tel : "",
                    name: e.data.user_name ? e.data.user_name : ""
                });
            }
        }), app.util.request({
            url: "entry/wxapp/GroupType",
            cachetime: "0",
            success: function(e) {
                console.log("分类列表", e), o.setData({
                    nav_array: e.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/GoodsInfo",
            cachetime: "0",
            data: {
                goods_id: a
            },
            success: function(e) {
                console.log(e), 1 == t.type ? (e.data.goods.yh = (Number(e.data.goods.y_price) - Number(e.data.goods.dd_price)).toFixed(2), 
                e.data.goods.money = (1 * Number(e.data.goods.dd_price)).toFixed(2)) : (e.data.goods.yh = (Number(e.data.goods.y_price) - Number(e.data.goods.pt_price)).toFixed(2), 
                e.data.goods.money = (1 * Number(e.data.goods.pt_price)).toFixed(2)), o.setData({
                    QgGoodInfo: e.data.goods,
                    isloading: !1
                });
            }
        }), app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: e
            },
            success: function(e) {
                console.log(e.data), e.data.storeset.wmps_name = "" != e.data.storeset.wmps_name ? e.data.storeset.wmps_name : "外卖配送";
                e.data;
                var t = e.data.store.coordinates.split(","), a = {
                    lng: Number(t[1]),
                    lat: Number(t[0])
                };
                o.setData({
                    psfarr: e.data.psf,
                    reduction: e.data.reduction,
                    store: e.data.store,
                    storeset: e.data.storeset,
                    sjstart: a,
                    xynr: e.data.storeset.ztxy
                });
            }
        });
    },
    tjddformSubmit: function(e) {
        console.log(e);
        wx.getStorageSync("users").id;
        this.setData({
            showModal: !0
        });
    },
    alone_pay: function(e) {
        var t = this;
        t.setData({
            showModal: !1
        }), wx.showLoading({
            title: "正在支付",
            mark: !0
        });
        var a = t.data, o = a.QgGoodInfo, n = a.nav_array, i = o.store_id, s = a.user_id, d = o.id, r = o.logo, l = o.name;
        if ("微信支付" == a.zfwz) var c = 1; else if ("余额支付" == a.zfwz) c = 2;
        for (var u in n) if (n[u].id == o.type_id) var p = n[u].name;
        if (1 == a.options.type) var g = o.dd_price, f = 1, m = Number(f) * Number(g); else g = o.pt_price, 
        f = 1, m = Number(f) * Number(g);
        var w = a.name, _ = a.mobile, y = a.store.address, h = a.note;
        console.log(o), console.log("用户id", s), console.log("商家id", i), Number(a.userInfo.wallet) < m && 2 == c ? (wx.hideLoading(), 
        wx.showModal({
            title: "",
            content: "您的余额不足"
        })) : 1 == t.confirm_info() ? app.util.request({
            url: "entry/wxapp/SaveGroupOrder",
            data: {
                store_id: i,
                user_id: s,
                goods_id: d,
                logo: r,
                goods_name: l,
                goods_type: p,
                price: g,
                goods_num: f,
                money: m,
                receive_name: w,
                receive_tel: _,
                receive_address: y,
                note: h,
                type: a.options.type,
                pay_type: c,
                kt_num: a.options.kt_num,
                group_id: a.options.group_id,
                dq_time: a.options.end_time,
                xf_time: a.options.xf_time
            },
            success: function(e) {
                console.log(e), 1 == c ? t.pay(e.data, m) : t.ye_pay(e.data, m);
            }
        }) : wx.hideLoading();
    },
    confirm_info: function(e) {
        var t = this.data;
        if (console.log(t), null == t.mobile || "" == t.mobile) wx.showModal({
            title: "温馨提示",
            content: "请输入您的联系电话"
        }); else {
            if (null != t.name && "" != t.name) return !0;
            wx.showModal({
                title: "温馨提示",
                content: "请输入您的姓名"
            });
        }
    },
    pay: function(e, t) {
        console.log("调用微信支付");
        var a = this.data.openid;
        app.util.request({
            url: "entry/wxapp/GroupPay",
            data: {
                order_id: e,
                money: t,
                openid: a
            },
            success: function(e) {
                console.log(e), wx.requestPayment({
                    timeStamp: e.data.timeStamp,
                    nonceStr: e.data.nonceStr,
                    package: e.data.package,
                    signType: e.data.signType,
                    paySign: e.data.paySign,
                    success: function(e) {
                        console.log(e), wx.hideLoading(), wx.showToast({
                            title: "支付成功"
                        }), setTimeout(function() {
                            wx.redirectTo({
                                url: "/zh_cjdianc/pages/collage/order"
                            });
                        }, 1500);
                    },
                    fail: function(e) {
                        console.log(e), wx.showLoading({
                            title: "支付失败"
                        }), setTimeout(function() {
                            wx.hideLoading(), wx.navigateBack({
                                delta: 2
                            });
                        }, 1500);
                    }
                });
            }
        });
    },
    ye_pay: function(e, t) {
        console.log("调用余额支付");
        this.data.openid;
        app.util.request({
            url: "entry/wxapp/GroupYePay",
            data: {
                order_id: e
            },
            success: function(e) {
                console.log(e), 1 == e.data ? (wx.hideLoading(), wx.showToast({
                    title: "支付成功"
                }), setTimeout(function() {
                    wx.redirectTo({
                        url: "/zh_cjdianc/pages/collage/order"
                    });
                }, 1500)) : (wx.hideLoading(), wx.showToast({
                    title: "支付失败"
                }), setTimeout(function() {
                    wx.navigateBack({
                        delta: 2
                    });
                }, 1500));
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