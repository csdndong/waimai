var app = getApp();

Page({
    data: {
        bomb: !0,
        kpgg: !0,
        ssq: "",
        xxdz: "",
        djdh: !1,
        qddh: !1
    },
    onLoad: function(t) {
        console.log(t.id), app.setNavigationBarColor(this), wx.hideShareMenu({});
        var e = wx.getStorageSync("users").id, a = this;
        app.util.request({
            url: "entry/wxapp/JfGoodsInfo",
            cachetime: "0",
            data: {
                id: t.id
            },
            success: function(t) {
                console.log(t), a.setData({
                    spinfo: t.data[0]
                }), wx.setNavigationBarTitle({
                    title: t.data[0].name
                });
            }
        }), app.util.request({
            url: "entry/wxapp/UserInfo",
            cachetime: "0",
            data: {
                user_id: e
            },
            success: function(t) {
                console.log(t), a.setData({
                    integral: t.data.total_score
                });
            }
        }), app.util.request({
            url: "entry/wxapp/MyDefaultAddress",
            cachetime: "0",
            data: {
                user_id: e
            },
            success: function(t) {
                console.log(t.data), t.data && a.setData({
                    myaddress: t.data
                });
            }
        });
    },
    duihuan: function() {
        this.setData({
            bomb: !1
        });
    },
    cancel: function() {
        this.setData({
            bomb: !0
        });
    },
    caomfirm: function() {
        var t = wx.getStorageSync("users").id, e = this.data.myaddress, a = this, o = a.data.spinfo.id, s = a.data.spinfo.money, n = a.data.spinfo.hb_moeny, i = Number(a.data.integral), d = a.data.spinfo.name, l = a.data.spinfo.img;
        if (console.log(e, t, o, Number(s), n, i, d, l), "1" == a.data.spinfo.type) a.setData({
            bomb: !0
        }), Number(s) > i ? wx.showModal({
            title: "提示",
            content: "您的积分不足以兑换此物品"
        }) : (a.setData({
            djdh: !0
        }), app.util.request({
            url: "entry/wxapp/Exchange",
            cachetime: "0",
            data: {
                user_id: t,
                good_id: o,
                integral: s,
                hb_money: n,
                type: 1,
                good_name: d,
                good_img: l
            },
            success: function(t) {
                console.log(t), 1 == t.data ? (wx.showToast({
                    title: "兑换成功"
                }), setTimeout(function() {
                    wx.navigateBack({});
                }, 1e3)) : (wx.showToast({
                    title: "请重试！",
                    icon: "loading"
                }), a.setData({
                    djdh: !1
                }));
            }
        })); else if (a.setData({
            bomb: !0
        }), Number(s) > i) wx.showModal({
            title: "提示",
            content: "您的积分不足以兑换此物品"
        }); else if (null == e) wx.showModal({
            title: "提示",
            content: "请前往个人中心填写收货地址",
            success: function(t) {
                t.confirm ? (console.log("用户点击确定"), wx.reLaunch({
                    url: "../../my/index"
                })) : t.cancel && console.log("用户点击取消");
            }
        }); else {
            var c = e.user_name, u = e.tel, r = e.area + e.address;
            console.log(c, u, r), wx.showLoading({
                title: "提交中",
                mask: !0
            }), app.util.request({
                url: "entry/wxapp/Exchange",
                cachetime: "0",
                data: {
                    user_id: t,
                    good_id: o,
                    integral: s,
                    user_name: c,
                    user_tel: u,
                    address: r,
                    type: 2,
                    good_name: d,
                    good_img: l
                },
                success: function(t) {
                    console.log(t), 1 == t.data ? (wx.showToast({
                        title: "兑换成功"
                    }), setTimeout(function() {
                        wx.navigateBack({});
                    }, 1e3)) : (wx.showToast({
                        title: "请重试！",
                        icon: "loading"
                    }), a.setData({
                        qddh: !1
                    }));
                }
            });
        }
    },
    ycgg: function() {
        this.setData({
            kpgg: !0
        });
    },
    dingwei: function(t) {
        console.log(t);
        var a = this;
        wx.chooseLocation({
            success: function(t) {
                console.log(t);
                var e = t.address.indexOf("区");
                console.log(t.address.substring(0, e + 1)), a.setData({
                    location: t.latitude + "," + t.longitude,
                    ssq: t.address.substring(0, e + 1),
                    xxdz: t.address.substring(e + 1) + t.name
                });
            }
        });
    },
    formSubmit: function(t) {
        console.log("form发生了submit事件，携带数据为：", t.detail.value);
        var e = this, a = wx.getStorageSync("users").id, o = e.data.spinfo.id, s = e.data.spinfo.money, n = e.data.spinfo.name, i = e.data.spinfo.img, d = t.detail.value.lxr, l = t.detail.value.tel, c = (e.data.ssq, 
        e.data.ssq + t.detail.value.grxxdz);
        console.log(a, o, s, d, l, c, n, i);
        var u = "", r = !0;
        "" == d ? u = "请填写联系人！" : "" == l ? u = "请填写联系电话！" : /^0?(13[0-9]|15[012356789]|17[013678]|18[0-9]|14[57])[0-9]{8}$/.test(l) && 11 == l.length ? "" == c ? u = "请选择位置！" : (r = !1, 
        e.setData({
            qddh: !0
        }), app.util.request({
            url: "entry/wxapp/Exchange",
            cachetime: "0",
            data: {
                user_id: a,
                good_id: o,
                integral: s,
                user_name: d,
                user_tel: l,
                address: c,
                type: 2,
                good_name: n,
                good_img: i
            },
            success: function(t) {
                console.log(t), 1 == t.data ? (wx.showToast({
                    title: "兑换成功"
                }), setTimeout(function() {
                    wx.navigateBack({});
                }, 1e3)) : (wx.showToast({
                    title: "请重试！",
                    icon: "loading"
                }), e.setData({
                    qddh: !1
                }));
            }
        })) : u = "手机号错误", 1 == r && wx.showModal({
            title: "提示",
            content: u
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});