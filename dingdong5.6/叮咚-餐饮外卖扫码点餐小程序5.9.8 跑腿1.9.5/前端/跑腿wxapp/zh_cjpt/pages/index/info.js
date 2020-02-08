var amapFile = require("../../utils/amap-wx.js"), app = getApp();

Page({
    data: {},
    onLoad: function(a) {
        var i = this;
        wx.hideShareMenu(), console.log(a);
        var s = "30.527259,114.324417";
        app.g_t(function(t) {
            i.setData({
                latitude: Number(t.split(",")[0]),
                longitude: Number(t.split(",")[1])
            });
        }), app.getSystem(function(t) {
            i.setData({
                getSystem: t,
                color: t.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: t.color
            });
            var o = a.lat, e = a.lng;
            i.setData({
                name: a.name,
                address: a.address
            }), app.g_t(function(t) {
                var a = [ {
                    iconPath: "../img/dao.png",
                    id: 0,
                    latitude: o,
                    longitude: e,
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
                    latitude: s.split(",")[0],
                    longitude: s.split(",")[1],
                    width: 25,
                    height: 30
                } ];
                i.setData({
                    markers: a,
                    distance: "",
                    cost: "",
                    polyline: [],
                    lat: o,
                    lng: e,
                    location: t.split(",")
                }), i.route();
            });
        });
    },
    route: function(t) {
        var l = this, a = l.data, o = a.getSystem.map_key;
        new amapFile.AMapWX({
            key: o
        }).getRidingRoute({
            origin: a.lng + "," + a.lat,
            destination: a.location[1] + "," + a.location[0],
            success: function(t) {
                console.log("第一次执行", t);
                var a = [];
                if (t.paths && t.paths[0] && t.paths[0].steps) for (var o = t.paths[0].steps, e = 0; e < o.length; e++) for (var i = o[e].polyline.split(";"), s = 0; s < i.length; s++) a.push({
                    longitude: parseFloat(i[s].split(",")[0]),
                    latitude: parseFloat(i[s].split(",")[1])
                });
                l.setData({
                    polyline: [ {
                        points: a,
                        color: "#0091ff",
                        width: 6
                    } ]
                }), l.route1(), t.paths[0] && t.paths[0].distance && l.setData({
                    distance: t.paths[0].distance + "米"
                }), t.taxi_cost && l.setData({
                    cost: "打车约" + parseInt(t.taxi_cost) + "元"
                });
            },
            fail: function(t) {}
        });
    },
    route1: function(t) {
        var p = this;
        console.log("第二次执行查看当前的路线", p.data.polyline);
        var a = p.data, o = a.getSystem.map_key, e = new amapFile.AMapWX({
            key: o
        });
        console.log("查看当前骑手的经纬度", a.location), e.getRidingRoute({
            origin: "114.324417,30.527259",
            destination: a.lng + "," + a.lat,
            success: function(t) {
                console.log("第二次执行", t);
                var a = [];
                if (t.paths && t.paths[0] && t.paths[0].steps) for (var o = t.paths[0].steps, e = 0; e < o.length; e++) for (var i = o[e].polyline.split(";"), s = 0; s < i.length; s++) a.push({
                    longitude: parseFloat(i[s].split(",")[0]),
                    latitude: parseFloat(i[s].split(",")[1])
                });
                var l = {
                    points: a,
                    color: "#F66925",
                    width: 6
                };
                console.log("第一次的路线", p.data.polyline), console.log("第二次查找路线", l);
                var n = p.data.polyline;
                n = n.concat(l), console.log("查看路线"), console.log(n), p.setData({
                    polyline: n
                }), t.paths[0] && t.paths[0].distance && p.setData({
                    distance1: t.paths[0].distance + "米"
                }), t.taxi_cost && p.setData({
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
    goDetail: function(t) {
        wx.openLocation({
            latitude: Number(this.data.lat),
            longitude: Number(this.data.lng),
            name: this.data.name,
            address: this.data.address
        });
    }
});