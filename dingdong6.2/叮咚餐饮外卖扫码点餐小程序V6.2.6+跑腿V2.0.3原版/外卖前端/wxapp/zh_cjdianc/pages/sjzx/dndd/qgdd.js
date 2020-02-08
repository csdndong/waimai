/*   time:2019-07-18 01:03:19*/
var _Page;

function _defineProperty(t, e, a) {
    return e in t ? Object.defineProperty(t, e, {
        value: a,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : t[e] = a, t
}
var app = getApp(),
    util = require("../../../utils/util.js");
Page((_defineProperty(_Page = {
    data: {
        selectedindex: 0,
        topnav: [{
            img: "../../../img/icon/dzt.png",
            img1: "../../../img/icon/wdzt.png",
            name: "全部"
        }, {
            img: "../../../img/icon/djd.png",
            img1: "../../../img/icon/wdjd.png",
            name: "待支付"
        }, {
            img: "../../../img/icon/ywc.png",
            img1: "../../../img/icon/wywc.png",
            name: "已完成"
        }, {
            img: "../../../img/icon/sh.png",
            img1: "../../../img/icon/wsh.png",
            name: "已关闭"
        }],
        open: !1,
        pagenum: 1,
        order_list: [],
        storelist: [],
        mygd: !1,
        jzgd: !0,
        selecttype: !1,
        typename: "选择类型",
        selectdate: !1,
        datetype: ["全部", "待核销", "已核销"],
        start: "",
        timestart: "",
        timeend: "",
        start_time: "",
        end_time: ""
    },
    hidemask: function() {
        this.setData({
            selecttype: !1,
            selectdate: !1
        })
    },
    choseinfo: function() {
        this.setData({
            selectinfo: !this.data.selectinfo,
            selecttype: !1
        })
    },
    qginfoinput: function(t) {
        console.log(t.detail.value), this.setData({
            searchinfo: t.detail.value
        })
    },
    search: function() {
        var t = this.data.searchinfo;
        console.log(t), null != t && "" != t ? (this.setData({
            typename: this.data.datetype[0],
            state: "",
            pagenum: 1,
            order_list: [],
            storelist: [],
            mygd: !1,
            jzgd: !0,
            selectinfo: !1
        }), this.reLoad()) : wx.showModal({
            title: "提示",
            content: "请输入查找内容"
        })
    },
    chosetype: function() {
        this.setData({
            selecttype: !this.data.selecttype,
            selectdate: !1,
            selectinfo: !1
        })
    },
    xztype: function(t) {
        var e, a = t.currentTarget.dataset.index;
        console.log(a), 0 == a && (e = ""), 1 == a && (e = "2"), 2 == a && (e = "3"), this.setData({
            typename: this.data.datetype[a],
            selecttype: !1,
            searchinfo: "",
            state: e,
            start_time: "",
            end_time: "",
            pagenum: 1,
            order_list: [],
            storelist: [],
            mygd: !1,
            jzgd: !0,
            selectedindex: 0,
            status: 1
        }), this.reLoad()
    },
    bindTimeChange: function(t) {
        console.log("picker 发生选择改变，携带值为", t.detail.value), this.setData({
            timestart: t.detail.value
        })
    },
    bindTimeChange1: function(t) {
        console.log("picker  发生选择改变，携带值为", t.detail.value), this.setData({
            timeend: t.detail.value
        })
    },
    find: function() {
        var t = this.data.timestart,
            e = this.data.timeend;
        console.log(util.validTime(t, e)), util.validTime(t, e) ? (this.setData({
            typename: this.data.datetype[0],
            time: "",
            pagenum: 1,
            order_list: [],
            storelist: [],
            mygd: !1,
            jzgd: !0,
            selectedindex: 0,
            status: 1,
            start_time: t,
            end_time: e,
            selectdate: !1
        }), this.reLoad()) : wx.showModal({
            title: "提示",
            content: "请选择正确的日期范围"
        })
    },
    repeat: function() {
        var t = this.data.start;
        console.log(t), this.setData({
            typename: this.data.datetype[0],
            time: "",
            pagenum: 1,
            order_list: [],
            storelist: [],
            mygd: !1,
            jzgd: !0,
            selectedindex: 0,
            status: 1,
            timestart: t,
            timeend: t,
            start_time: "",
            end_time: "",
            selectdate: !1
        }), this.reLoad()
    },
    chosedate: function() {
        this.setData({
            selectdate: !this.data.selectdate,
            selecttype: !1
        })
    },
    maketel: function(t) {
        var e = t.currentTarget.dataset.tel;
        wx.makePhoneCall({
            phoneNumber: e
        })
    },
    location: function(t) {
        var e = t.currentTarget.dataset.lat,
            a = t.currentTarget.dataset.lng,
            s = t.currentTarget.dataset.address;
        console.log(e, a), wx.openLocation({
            latitude: parseFloat(e),
            longitude: parseFloat(a),
            address: s,
            name: "位置"
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
            status: Number(t.currentTarget.dataset.index) + 1
        }), this.reLoad()
    },
    doreload: function(t) {
        console.log(t), this.setData({
            pagenum: 1,
            order_list: [],
            storelist: [],
            mygd: !1,
            jzgd: !0,
            selectedindex: t - 1,
            status: t
        }), this.reLoad()
    },
    kindToggle: function(t) {
        var e = t.currentTarget.id,
            a = this.data.order_list;
        console.log(e);
        for (var s = 0, i = a.length; s < i; ++s) a[s].open = s == e && !a[s].open;
        this.setData({
            order_list: a
        })
    },
    reLoad: function() {
        var s = this,
            t = this.data.status || 1,
            e = this.data.state || "",
            a = this.data.searchinfo || "",
            i = wx.getStorageSync("sjdsjid"),
            n = this.data.pagenum;
        console.log(t, e, i, n, a), app.util.request({
            url: "entry/wxapp/StoreQgOrder",
            cachetime: "0",
            data: {
                state: e,
                keywords: a,
                store_id: i,
                page: n,
                pagesize: 10
            },
            success: function(t) {
                console.log("分页返回的列表数据", t.data);
                for (var e = 0; e < t.data.length; e++) t.data[e].dq_time = util.ormatDate(t.data[e].dq_time);
                t.data.length < 10 ? s.setData({
                    mygd: !0,
                    jzgd: !0
                }) : s.setData({
                    jzgd: !0,
                    pagenum: s.data.pagenum + 1
                });
                var a = s.data.storelist;
                a = function(t) {
                    for (var e = [], a = 0; a < t.length; a++) - 1 == e.indexOf(t[a]) && e.push(t[a]);
                    return e
                }(a = a.concat(t.data)), s.setData({
                    order_list: a,
                    storelist: a
                }), console.log(a)
            }
        })
    },
    onLoad: function(t) {
        var e = this,
            a = wx.getStorageSync("sjdsjid");
        console.log(a, t);
        var s = util.formatTime(new Date).substring(0, 10).replace(/\//g, "-");
        console.log(s.toString()), this.setData({
            start: s,
            timestart: s,
            timeend: s
        }), wx.setNavigationBarTitle({
            title: "抢购订单"
        }), this.reLoad(), app.setNavigationBarColor(this), app.sjdpageOnLoad(this), app.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(t) {
                console.log(t.data), wx.setStorageSync("system", t.data), e.setData({
                    xtxx: t.data
                })
            }
        })
    }
}, "maketel", function(t) {
    var e = t.currentTarget.dataset.tel;
    wx.makePhoneCall({
        phoneNumber: e
    })
}), _defineProperty(_Page, "smhx", function(t) {
    var a = wx.getStorageSync("sjdsjid");
    wx.scanCode({
        success: function(t) {
            console.log(t);
            var e = "/" + t.path;
            wx.navigateTo({
                url: e + "&storeid=" + a
            })
        },
        fail: function(t) {
            console.log("扫码fail")
        }
    })
}), _defineProperty(_Page, "onPullDownRefresh", function() {}), _defineProperty(_Page, "onReachBottom", function() {
    console.log("上拉加载", this.data.pagenum);
    !this.data.mygd && this.data.jzgd && (this.setData({
        jzgd: !1
    }), this.reLoad())
}), _Page));