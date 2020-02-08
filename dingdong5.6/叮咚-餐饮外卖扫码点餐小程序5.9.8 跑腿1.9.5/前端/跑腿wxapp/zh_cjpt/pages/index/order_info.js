var amapFile = require("../../utils/amap-wx.js"), app = getApp();

Page({
    data: {
        status: [ "待抢单", "待取货", "待配送", "已完成" ],
        order_statu: [ {
            name: "待抢单"
        }, {
            name: "待取货"
        }, {
            name: "配送中"
        }, {
            name: "已完成"
        } ]
    },
    onLoad: function(t) {
        var a = this;
        wx.hideShareMenu(), a.setData({
            index: t.index,
            id: t.id
        });
        app.g_t(function(t) {
            a.setData({
                latitude: Number(t.split(",")[0]),
                longitude: Number(t.split(",")[1])
            });
        }), a.location(), app.getSystem(function(t) {
            console.log(t), a.setData({
                getSystem: t,
                color: t.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: t.color
            });
        });
    },
    location: function(t) {
        var n = this, a = n.data.id;
        app.g_t(function(o) {
            console.log(o);
            o = o.split(",");
            app.util.request({
                url: "entry/wxapp/OrderInfo",
                data: {
                    order_id: a
                },
                success: function(t) {
                    if (console.log(t), "" != t.data.goods_info) {
                        t.data.goods_info = t.data.goods_info.split("#");
                        var a = t.data.goods_info, e = [];
                        a.map(function(t) {
                            console.log(t);
                            var a = {};
                            a.name = t.match(/(\S*)数量:/)[1], a.num = t.match(/数量:(\S*)价格/)[1], a.price = t.match(/价格(\S*)/)[1], 
                            e.push(a);
                        }), console.log(e);
                    }
                    t.data.distance = app.location(Number(o[0]), Number(t.data.sender_lat), Number(o[1]), Number(t.data.sender_lng)), 
                    t.data.distance1 = app.location(Number(t.data.sender_lat), Number(t.data.receiver_lat), Number(t.data.sender_lng), Number(t.data.receiver_lng)), 
                    t.data.time = app.ormatDate(t.data.time), t.data.price = (Number(t.data.yh_money) + Number(t.data.goods_price)).toFixed(2), 
                    n.setData({
                        order_info: t.data,
                        goodNum: e
                    }), 1 == t.data.state ? n.setData({
                        width: "0"
                    }) : 2 == t.data.state ? n.setData({
                        width: "33%"
                    }) : 3 == t.data.state ? n.setData({
                        width: "66%"
                    }) : 4 == t.data.state && n.setData({
                        width: "100%"
                    }), n.order(), n.map();
                }
            });
        });
    },
    map: function(t) {
        var e = this, a = e.data.order_info, o = a.receiver_lat, n = a.receiver_lng, i = a.sender_lat, s = a.sender_lng;
        app.g_t(function(t) {
            var a = [ {
                iconPath: "../img/dao.png",
                id: 0,
                latitude: i,
                longitude: s,
                width: 25,
                height: 30
            }, {
                iconPath: "../img/qi.png",
                id: 0,
                latitude: t.split(",")[0],
                longitude: t.split(",")[1],
                width: 25,
                height: 30
            }, {
                iconPath: "../img/user.png",
                id: 0,
                latitude: o,
                longitude: n,
                width: 25,
                height: 30
            } ];
            e.setData({
                markers: a,
                distance: "",
                cost: "",
                polyline: [],
                lat: i,
                lng: s,
                location: t.split(",")
            }), e.route();
        });
    },
    route: function(t) {
        var s = this, a = s.data, e = a.getSystem.map_key;
        new amapFile.AMapWX({
            key: e
        }).getRidingRoute({
            origin: a.lng + "," + a.lat,
            destination: a.location[1] + "," + a.location[0],
            success: function(t) {
                console.log("第一次执行", t);
                var a = [];
                if (t.paths && t.paths[0] && t.paths[0].steps) for (var e = t.paths[0].steps, o = 0; o < e.length; o++) for (var n = e[o].polyline.split(";"), i = 0; i < n.length; i++) a.push({
                    longitude: parseFloat(n[i].split(",")[0]),
                    latitude: parseFloat(n[i].split(",")[1])
                });
                s.setData({
                    polyline: [ {
                        points: a,
                        color: "#0091ff",
                        width: 6
                    } ]
                }), s.route1(), t.paths[0] && t.paths[0].distance && s.setData({
                    distance: t.paths[0].distance + "米"
                }), t.taxi_cost && s.setData({
                    cost: "打车约" + parseInt(t.taxi_cost) + "元"
                });
            },
            fail: function(t) {}
        });
    },
    route1: function(t) {
        var l = this, a = l.data, e = a.getSystem.map_key, o = new amapFile.AMapWX({
            key: e
        });
        a.order_info.receiver_lat, a.order_info.receiver_lng;
        o.getRidingRoute({
            origin: a.order_info.receiver_lng + "," + a.order_info.receiver_lat,
            destination: a.lng + "," + a.lat,
            success: function(t) {
                console.log("第二次执行", t);
                var a = [];
                if (t.paths && t.paths[0] && t.paths[0].steps) for (var e = t.paths[0].steps, o = 0; o < e.length; o++) for (var n = e[o].polyline.split(";"), i = 0; i < n.length; i++) a.push({
                    longitude: parseFloat(n[i].split(",")[0]),
                    latitude: parseFloat(n[i].split(",")[1])
                });
                var s = {
                    points: a,
                    color: "#F66925",
                    width: 6
                };
                console.log("第一次的路线", l.data.polyline), console.log("第二次查找路线", s);
                var r = l.data.polyline;
                r = r.concat(s), console.log("查看路线"), console.log(r), l.setData({
                    polyline: r
                }), t.paths[0] && t.paths[0].distance && l.setData({
                    distance1: t.paths[0].distance + "米"
                }), t.taxi_cost && l.setData({
                    cost1: "打车约" + parseInt(t.taxi_cost) + "元"
                });
            },
            fail: function(t) {
                console.log("失败原因", t);
            },
            complete: function(t) {
                console.log("每一步都执行了", t);
            }
        });
    },
    order: function(t) {
        this.data.index;
        var a = this.data.order_info, e = this.data.order_statu;
        for (var o in e) 1 == a.state ? e[o].img = o <= 0 ? "../img/gou.png" : "../img/gou_1.png" : 2 == a.state ? e[o].img = o <= 1 ? "../img/gou.png" : "../img/gou_1.png" : 3 == a.state ? e[o].img = o <= 2 ? "../img/gou.png" : "../img/gou_1.png" : 4 == a.state && (e[o].img = o <= 3 ? "../img/gou.png" : "../img/gou_1.png");
        console.log(e), this.setData({
            order_statu: e
        });
    },
    phone: function(t) {
        wx.makePhoneCall({
            phoneNumber: t.currentTarget.dataset.tel
        });
    },
    robbing: function(t) {
        var a = this, e = (a.data, a.data.order_info.id), o = wx.getStorageSync("qs").id;
        console.log(o), wx.showModal({
            title: "温馨提示",
            content: "是否确认接单",
            success: function(t) {
                t.confirm && app.util.request({
                    url: "entry/wxapp/Robbing",
                    data: {
                        qs_id: o,
                        id: e
                    },
                    success: function(t) {
                        console.log(t), 1 == t.data ? (app.succ_t("抢单成功", !0), a.location(), wx.reLaunch({
                            url: "index?ac_index=1&state=2"
                        })) : (app.succ_t("抢单失败", !0), wx.showModal({
                            title: "",
                            content: "抢单失败"
                        }));
                    }
                });
            }
        });
    },
    g_shop: function(t) {
        var a = this, e = (a.data, a.data.order_info.id), o = wx.getStorageSync("qs").id;
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
                        console.log(t), 1 == t.data ? (app.succ_t("确认到店", !0), a.location(), wx.reLaunch({
                            url: "index?ac_index=2&state=3"
                        })) : app.succ_t("系统出错", !0);
                    }
                });
            }
        });
    },
    service: function(t) {
        var a = this, e = (a.data, a.data.order_info.id), o = wx.getStorageSync("qs").id;
        console.log(o), wx.showModal({
            title: "温馨提示",
            content: "请确认是否送达",
            success: function(t) {
                t.confirm && app.util.request({
                    url: "entry/wxapp/Complete",
                    data: {
                        order_id: e
                    },
                    success: function(t) {
                        console.log(t), 1 == t.data ? (app.succ_t("确认送达", !0), a.location()) : app.succ_t("系统出错", !0);
                    }
                });
            }
        });
    },
    routes: function(t) {
        console.log(t);
        var a = t.currentTarget.dataset.lat, e = t.currentTarget.dataset.lng, o = t.currentTarget.dataset.name, n = t.currentTarget.dataset.address;
        wx.openLocation({
            latitude: Number(a),
            longitude: Number(e),
            name: o,
            address: n
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