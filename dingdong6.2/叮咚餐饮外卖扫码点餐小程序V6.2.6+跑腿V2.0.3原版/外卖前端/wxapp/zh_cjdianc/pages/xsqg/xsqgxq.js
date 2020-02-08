/*   time:2019-07-18 01:03:04*/
var app = getApp(),
    util = require("../../utils/util.js");
Page({
    data: {},
    opennav: function() {
        this.setData({
            opendh: !this.data.opendh
        })
    },
    bought: function() {
        wx.navigateTo({
            url: "yqgyh?goodid=" + this.data.QgGoodInfo.id
        })
    },
    tzsj: function() {
        wx.redirectTo({
            url: "../seller/index?sjid=" + this.data.StoreInfo.store.id
        })
    },
    order: function() {
        wx.navigateTo({
            url: "order"
        })
    },
    addformSubmit: function(t) {
        console.log("formid", t.detail.formId);
        var a = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/AddFormId",
            cachetime: "0",
            data: {
                user_id: a,
                form_id: t.detail.formId
            },
            success: function(t) {
                console.log(t.data)
            }
        })
    },
    ljqg: function() {
        var o = this,
            e = this.data.userinfo,
            n = this.data.QgGoodInfo.store_id,
            i = this.data.QgGoodInfo.id;
        console.log(e), "" == e.img || "" == e.name ? wx.navigateTo({
            url: "../smdc/getdl"
        }) : ("1" == this.data.QgGoodInfo.type && wx.redirectTo({
            url: "qgform?storeid=" + n + "&goodid=" + i
        }), "2" == this.data.QgGoodInfo.type && (wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/DelCar",
            cachetime: "0",
            data: {
                user_id: e.id,
                store_id: n
            },
            success: function(t) {
                console.log(t.data);
                var a = o.data.QgGoodInfo;
                console.log(a), wx.showLoading({
                    title: "正在加载",
                    mask: !0
                }), app.util.request({
                    url: "entry/wxapp/QgAddCar",
                    cachetime: "0",
                    data: {
                        money: a.money,
                        good_id: i,
                        store_id: n,
                        user_id: e.id,
                        num: 1,
                        spec: "",
                        combination_id: "",
                        box_money: 0,
                        is_qg: 1,
                        qg_name: a.name,
                        qg_logo: a.logo
                    },
                    success: function(t) {
                        console.log(t), "1" == t.data ? wx.redirectTo({
                            url: "/zh_cjdianc/pages/takeout/takeoutindex?storeid=" + n + "&qgjl=1"
                        }) : "超出库存!" == t.data ? wx.showModal({
                            title: "提示",
                            content: "库存不足"
                        }) : "超出购买限制!" == t.data && wx.showModal({
                            title: "提示",
                            content: "超出购买限制!"
                        })
                    }
                })
            }
        })))
    },
    maketel: function(t) {
        var a = this.data.StoreInfo.store.tel;
        wx.makePhoneCall({
            phoneNumber: a
        })
    },
    location: function() {
        var t = this.data.StoreInfo.store.coordinates.split(","),
            a = this.data.StoreInfo.store;
        console.log(t), wx.openLocation({
            latitude: parseFloat(t[0]),
            longitude: parseFloat(t[1]),
            address: a.address,
            name: a.name
        })
    },
    onLoad: function(a) {
        app.setNavigationBarColor(this);
        var i = this;
        app.getUserInfo(function(t) {
            console.log(t), i.setData({
                userinfo: t
            }), app.util.request({
                url: "entry/wxapp/IsPay",
                cachetime: "0",
                data: {
                    user_id: t.id,
                    good_id: a.id
                },
                success: function(t) {
                    console.log(t), i.setData({
                        IsPay: t.data
                    })
                }
            })
        }), app.util.request({
            url: "entry/wxapp/Url",
            cachetime: "0",
            success: function(t) {
                console.log(t.data), i.setData({
                    url: t.data,
                    xtxx: getApp().xtxx
                })
            }
        }), app.util.request({
            url: "entry/wxapp/QgPeople",
            cachetime: "0",
            data: {
                good_id: a.id
            },
            success: function(t) {
                console.log(t.data), i.setData({
                    QgPeople: t.data
                })
            }
        }), app.util.request({
            url: "entry/wxapp/QgGoodInfo",
            cachetime: "0",
            data: {
                id: a.id
            },
            success: function(t) {
                console.log(t), wx.setNavigationBarTitle({
                    title: t.data.money + "元抢购" + t.data.name
                }), t.data.yqnum = ((Number(t.data.number) - Number(t.data.surplus)) / Number(t.data.number) * 100).toFixed(1), t.data.img = t.data.img.split(",");
                new Date(t.data.end_time).getTime();
                ! function t(a) {
                    var o = a || [];
                    var e = Math.round((new Date).getTime() / 1e3);
                    var n = o - e || [];
                    i.setData({
                        clock: function(t) {
                            var a = Math.floor(t),
                                o = Math.floor(a / 3600 / 24),
                                e = Math.floor(a / 3600 % 24),
                                n = Math.floor(a / 60 % 60),
                                i = Math.floor(a % 60);
                            o < 10 && (o = "0" + o);
                            e < 10 && (e = "0" + e);
                            i < 10 && (i = "0" + i);
                            n < 10 && (n = "0" + n);
                            return {
                                day: o,
                                hr: e,
                                min: n,
                                sec: i
                            }
                        }(n)
                    });
                    n <= 0 ? i.setData({
                        clock: !1
                    }) : 0 < n && setTimeout(function() {
                        n -= 1e3, t(a)
                    }, 1e3)
                }(t.data.end_time), t.data.start_time = util.ormatDate(t.data.start_time), t.data.end_time = util.ormatDate(t.data.end_time), i.setData({
                    QgGoodInfo: t.data
                }), app.util.request({
                    url: "entry/wxapp/StoreInfo",
                    cachetime: "0",
                    data: {
                        store_id: t.data.store_id
                    },
                    success: function(t) {
                        console.log("商家详情", t), i.setData({
                            StoreInfo: t.data
                        })
                    }
                })
            }
        })
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {
        var t = this.data.userinfo,
            a = this.data.QgGoodInfo,
            o = t.name + "邀请你" + a.money + "元抢购" + a.name;
        return console.log(t), {
            title: o,
            path: "/zh_cjdianc/pages/xsqg/xsqgxq?id=" + a.id,
            success: function(t) {},
            fail: function(t) {}
        }
    }
});