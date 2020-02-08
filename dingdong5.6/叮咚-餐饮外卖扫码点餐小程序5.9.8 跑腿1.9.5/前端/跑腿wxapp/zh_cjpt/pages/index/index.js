var app = getApp();

Page({
    data: {
        nav: [ {
            name: "新任务"
        }, {
            name: "待取送"
        }, {
            name: "配送中"
        }, {
            name: "配送完成"
        } ],
        ac_index: 0,
        page: 1,
        list: [],
        state: 1,
        inter: !1
    },
    onLoad: function(t) {
        var a = this;
        wx.hideShareMenu();
        var e = [ 5809, 4198, 2282, 1835, 1206, 1112.92, 815, 779, 680, 680, 463, 424, 91, 0, 0, 0, 0 ];
        for (var o in e) e[o] = 6e3 - e[o], e[o];
        console.log(e);
        var n = function(t, a) {
            for (var e = 0, o = t.length - 1; e <= o; ) {
                var n = parseInt((o + e) / 2);
                if (a == t[n]) return n;
                if (a > t[n]) e = n + 1; else {
                    if (!(a < t[n])) return -1;
                    o = n - 1;
                }
            }
        }([ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 23, 44, 86 ], 10);
        console.log(n), null != t.ac_index && (console.log("从别的页面跳转过来的"), a.setData({
            ac_index: t.ac_index,
            state: t.state
        }));
        var s = app.bottom_menu("/zh_cjpt/pages/index/index");
        a.setData({
            menu: s
        }), app.getSystem(function(t) {
            console.log(t), a.setData({
                getSystem: t,
                distaceShop: Number(t.distance),
                color: t.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: t.color
            }), app.g_t(function(t) {
                console.log(t), a.setData({
                    location: t,
                    lat: t.split(",")[0],
                    lng: t.split(",")[1]
                }), a.nav_data(), a.timing();
            });
        });
    },
    timing: function(t) {},
    locations: function(t) {},
    order_list: function(t) {
        var e = this, a = e.data, o = a.state, n = a.page, s = a.qs_id, r = a.list, i = (a.distaceShop, 
        e.data.location.split(","));
        console.log("当前的page为" + n + " 当前的状态值为" + o + " 骑手的id为" + s), console.log(e.data.lat), 
        console.log(e.data.lng), app.util.request({
            url: "entry/wxapp/JdList",
            data: {
                state: o,
                page: n,
                qs_id: s,
                lat: e.data.lat,
                lng: e.data.lng
            },
            success: function(t) {
                if (console.log(t), 0 < t.data.length) {
                    for (var a in t.data) t.data[a].distance = app.location(Number(i[0]), Number(t.data[a].sender_lat), Number(i[1]), Number(t.data[a].sender_lng)), 
                    t.data[a].distance1 = app.location(Number(t.data[a].sender_lat), Number(t.data[a].receiver_lat), Number(t.data[a].sender_lng), Number(t.data[a].receiver_lng)), 
                    t.data[a].wc_time = app.ormatDate(t.data[a].wc_time), t.data[a].goods_info = t.data[a].goods_info.split(","), 
                    t.data[a].time = app.ormatDate(t.data[a].time), r = r.concat(t.data[a]);
                    console.log(r), 0 == r.length || e.setData({
                        list: r,
                        page: n + 1
                    });
                }
            }
        });
    },
    nav_data: function(t) {
        var a = this, e = a.data;
        console.log(e.list);
        var o = e.nav, n = e.color, s = e.ac_index;
        for (var r in console.log(s), o) r == s ? (o[r].color = n, o[r].border = n) : (o[r].border = "#f9f9f9", 
        o[r].color = "#333");
        console.log(o);
        var i = wx.getStorageSync("qs").status;
        console.log(i);
        var l = wx.getStorageSync("qs").id;
        0 == s && 1 == i ? (a.setData({
            state: 1,
            page: 1,
            list: [],
            qs_id: "",
            lat: this.data.location.split(",")[0],
            lng: this.data.location.split(",")[1]
        }), a.order_list()) : 1 == s && 1 == i ? (a.setData({
            state: 2,
            page: 1,
            list: [],
            qs_id: l,
            lat: "",
            lng: ""
        }), a.order_list()) : 2 == s && 1 == i ? (a.setData({
            state: 3,
            page: 1,
            list: [],
            qs_id: l,
            lat: "",
            lng: ""
        }), a.order_list()) : 3 == s && 1 == i && (a.setData({
            state: 4,
            page: 1,
            list: [],
            qs_id: l,
            lat: "",
            lng: ""
        }), a.order_list()), a.setData({
            nav: o
        });
    },
    nav: function(t) {
        this.setData({
            ac_index: t.currentTarget.dataset.index
        }), this.nav_data();
    },
    route: function(t) {
        console.log(t);
        var a = t.currentTarget.dataset.lat, e = t.currentTarget.dataset.lng, o = t.currentTarget.dataset.name, n = t.currentTarget.dataset.address;
        wx.navigateTo({
            url: "info?lat=" + a + "&lng=" + e + "&name=" + o + "&address=" + n
        });
    },
    order_info: function(t) {
        wx.navigateTo({
            url: "order_info?id=" + t.currentTarget.dataset.id + "&index=" + t.currentTarget.dataset.index
        });
    },
    robbing: function(t) {
        var a = this, e = (a.data, t.currentTarget.dataset.id), o = wx.getStorageSync("qs").id;
        console.log(o), wx.showModal({
            title: "温馨提示",
            content: "是否接单",
            success: function(t) {
                t.confirm && app.util.request({
                    url: "entry/wxapp/Robbing",
                    data: {
                        qs_id: o,
                        id: e
                    },
                    success: function(t) {
                        console.log(t), 1 == t.data ? app.succ_t("抢单成功", !0) : (app.succ_t("抢单失败", !0), 
                        wx.showModal({
                            title: "",
                            content: "抢单失败"
                        })), a.setData({
                            state: 1,
                            page: 1,
                            list: [],
                            qs_id: ""
                        }), a.order_list();
                    }
                });
            }
        });
    },
    sender_tel: function(t) {
        wx.makePhoneCall({
            phoneNumber: t.currentTarget.dataset.tel
        });
    },
    Slip: function(t) {
        var a = this, e = (a.data, t.currentTarget.dataset.id), o = wx.getStorageSync("qs").id;
        console.log(o), wx.showModal({
            title: "温馨提示",
            content: "请确认是否需要转单",
            success: function(t) {
                t.confirm && app.util.request({
                    url: "entry/wxapp/Transfer",
                    data: {
                        id: e
                    },
                    success: function(t) {
                        console.log(t), 1 == t.data ? (app.succ_t("转单成功", !0), a.setData({
                            state: 2,
                            page: 1,
                            list: [],
                            qs_id: o
                        }), a.order_list()) : app.succ_t("系统出错", !0);
                    }
                });
            }
        });
    },
    g_shop: function(t) {
        var a = this, e = (a.data, t.currentTarget.dataset.id), o = wx.getStorageSync("qs").id;
        console.log(o), wx.showModal({
            title: "温馨提示",
            content: "请确认是否已经到店",
            success: function(t) {
                t.confirm && app.util.request({
                    url: "entry/wxapp/Arrival",
                    data: {
                        order_id: e
                    },
                    success: function(t) {
                        console.log(t), 1 == t.data ? (app.succ_t("确认到店", !0), a.setData({
                            state: 2,
                            page: 1,
                            list: [],
                            qs_id: o
                        }), a.order_list()) : "到店失败" == t.data ? (app.succ_t("订单出错", !0), a.setData({
                            state: 2,
                            page: 1,
                            list: [],
                            qs_id: o
                        }), a.order_list()) : app.succ_t("系统出错", !0);
                    }
                });
            }
        });
    },
    service: function(t) {
        var a = this, e = (a.data, t.currentTarget.dataset.id), o = wx.getStorageSync("qs").id;
        console.log(o), wx.showModal({
            title: "温馨提示",
            content: "请确认是否已经送达",
            success: function(t) {
                t.confirm && app.util.request({
                    url: "entry/wxapp/Complete",
                    data: {
                        order_id: e
                    },
                    success: function(t) {
                        console.log(t), 1 == t.data ? (app.succ_t("确认送达", !0), a.setData({
                            state: 3,
                            page: 1,
                            list: [],
                            qs_id: o
                        }), a.order_list()) : app.succ_t("系统出错", !0);
                    }
                });
            }
        });
    },
    route_page: function(t) {
        wx.reLaunch({
            url: t.currentTarget.dataset.url
        });
    },
    onReady: function() {},
    onShow: function() {
        app.globalData.refresh = !0, this.setData({
            inter: !1
        });
    },
    onHide: function() {
        console.log("页面隐藏"), this.setData({
            inter: !0
        });
    },
    onUnload: function() {
        console.log("页面卸载"), this.setData({
            inter: !0
        });
    },
    onPullDownRefresh: function() {
        var a = this;
        app.getSystem(function(t) {
            console.log(t), a.setData({
                getSystem: t,
                distaceShop: Number(t.distance),
                color: t.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: t.color
            }), app.g_t(function(t) {
                console.log(t), a.setData({
                    location: t
                }), a.setData({
                    list: [],
                    page: 1,
                    state: 1,
                    ac_index: 0,
                    qs_id: ""
                }), a.nav_data();
            });
        }), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        this.order_list();
    },
    onShareAppMessage: function() {}
});