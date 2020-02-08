/*   time:2019-07-18 01:03:25*/
var qqmapsdk, _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(t) {
        return typeof t
    } : function(t) {
        return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : typeof t
    }, app = getApp(),
    QQMapWX = require("../../utils/qqmap-wx-jssdk.js");
Page({
    data: {
        navbar: [{
            name: "全部",
            id: ""
        }],
        selectedindex: 0,
        params: {
            nopsf: 2,
            nostart: 2,
            yhhd: ""
        },
        status: 1,
        pagenum: 1,
        order_list: [],
        storelist: [],
        mygd: !1,
        jzgd: !0
    },
    dwreLoad: function() {
        var s = this,
            o = this.data.params;
        wx.getLocation({
            type: "wgs84",
            success: function(t) {
                var a = t.latitude,
                    e = t.longitude;
                o.lat = a, o.lng = e, s.setData({
                    params: o
                }), s.reLoad()
            }
        })
    },
    onOverallTag: function(t) {
        console.log(t), this.setData({
            mask1Hidden: !1
        })
    },
    mask1Cancel: function() {
        this.setData({
            mask1Hidden: !0
        })
    },
    selectednavbar: function(t) {
        console.log(t), this.setData({
            pagenum: 1,
            order_list: [],
            storelist: [],
            mygd: !1,
            jzgd: !0,
            selectedindex: t.currentTarget.dataset.index,
            toView: "a" + (t.currentTarget.dataset.index - 1),
            status: Number(t.currentTarget.dataset.index) + 1
        }), this.reLoad()
    },
    reLoad: function() {
        var t, o = this,
            a = this.data.status || 1,
            e = this.data.store_id || "";
        this.data.store_id;
        o.data.params.page = o.data.pagenum, o.data.params.pagesize = 5, t = 1 == a ? "" : o.data.navbar[a - 1].id, console.log(a, t, e, o.data.params), app.util.request({
            url: "entry/wxapp/SelectStoreList",
            cachetime: "0",
            data: o.data.params,
            success: function(t) {
                console.log("分页返回的列表数据", t.data);
                for (var a = 0; a < t.data.length; a++) {
                    "0.0" == t.data[a].sales && (t.data[a].sales = "5.0");
                    var e = parseFloat(t.data[a].juli);
                    console.log(e), console.log(), t.data[a].aa = e < 1e3 ? e + "m" : (e / 1e3).toFixed(2) + "km", t.data[a].aa1 = e
                }
                t.data.length < 5 ? o.setData({
                    mygd: !0,
                    jzgd: !0
                }) : o.setData({
                    jzgd: !0,
                    pagenum: o.data.pagenum + 1
                });
                var s = o.data.storelist;
                s = function(t) {
                    for (var a = [], e = 0; e < t.length; e++) - 1 == a.indexOf(t[e]) && a.push(t[e]);
                    return a
                }(s = s.concat(t.data)), o.setData({
                    order_list: s,
                    storelist: s
                }), console.log(s)
            }
        })
    },
    tzsjxq: function(t) {
        console.log(t.currentTarget.dataset, getApp().xtxx), 1 == t.currentTarget.dataset.type ? (getApp().sjid = t.currentTarget.dataset.sjid, wx.navigateTo({
            url: "/zh_cjdianc/pages/seller/index"
        })) : "1" == getApp().xtxx.is_tzms ? (getApp().sjid = t.currentTarget.dataset.sjid, wx.navigateTo({
            url: "/zh_cjdianc/pages/seller/index"
        })) : wx.navigateTo({
            url: "/zh_cjdianc/pages/takeout/takeoutindex?storeid=" + t.currentTarget.dataset.sjid
        })
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this), app.pageOnLoad(this);
        var s = this,
            a = t.storeid;
        console.log(t, a, "undefined", getApp().xtxx), wx.setNavigationBarTitle({
            title: t.title || "精选好店"
        }), s.setData({
            store_id: a
        }), qqmapsdk = new QQMapWX({
            key: getApp().xtxx.map_key
        }), s.dwreLoad(), app.util.request({
            url: "entry/wxapp/ad",
            cachetime: "0",
            success: function(t) {
                console.log(t);
                for (var a = [], e = 0; e < t.data.length; e++) "10" == t.data[e].type && a.push(t.data[e]);
                console.log(a, [], []), s.setData({
                    toplb: a
                })
            }
        })
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        console.log("上拉加载", this.data.pagenum);
        !this.data.mygd && this.data.jzgd && (this.setData({
            jzgd: !1
        }), this.reLoad())
    }
});