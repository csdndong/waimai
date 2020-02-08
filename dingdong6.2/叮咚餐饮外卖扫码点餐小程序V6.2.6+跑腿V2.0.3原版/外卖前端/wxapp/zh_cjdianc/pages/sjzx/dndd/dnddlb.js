/*   time:2019-07-18 01:03:19*/
var app = getApp(),
    util = require("../../../utils/util.js");
Page({
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
        datetype: ["全部", "今天", "昨天", "近七天", "本月"],
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
    chosetype: function() {
        this.setData({
            selecttype: !this.data.selecttype,
            selectdate: !1
        })
    },
    xztype: function(t) {
        var e, a = t.currentTarget.dataset.index;
        console.log(a), 0 == a && (e = ""), 1 == a && (e = "today"), 2 == a && (e = "yesterday"), 3 == a && (e = "week"), 4 == a && (e = "month"), this.setData({
            typename: this.data.datetype[a],
            selecttype: !1,
            time: e,
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
        var t, a = this,
            e = this.data.table_id,
            s = this.data.status || 1,
            i = this.data.time || "",
            n = wx.getStorageSync("sjdsjid"),
            o = this.data.pagenum,
            d = this.data.start_time,
            c = this.data.end_time;
        1 == s && (t = "1,2,3"), 2 == s && (t = "1"), 3 == s && (t = "2"), 4 == s && (t = "3"), console.log(s, t, i, d, c, n, o, e), app.util.request({
            url: "entry/wxapp/StoreDnOrder",
            cachetime: "0",
            data: {
                dn_state: t,
                time: i,
                start_time: d,
                end_time: c,
                table_id: e,
                store_id: n,
                page: o,
                pagesize: 10
            },
            success: function(t) {
                console.log("分页返回的列表数据", t.data), t.data.length < 10 ? a.setData({
                    mygd: !0,
                    jzgd: !0
                }) : a.setData({
                    jzgd: !0,
                    pagenum: a.data.pagenum + 1
                });
                var e = a.data.storelist;
                e = function(t) {
                    for (var e = [], a = 0; a < t.length; a++) - 1 == e.indexOf(t[a]) && e.push(t[a]);
                    return e
                }(e = e.concat(t.data)), a.setData({
                    order_list: e,
                    storelist: e
                }), console.log(e)
            }
        })
    },
    onLoad: function(t) {
        var e = this,
            a = wx.getStorageSync("sjdsjid");
        console.log(a, t);
        var s = util.formatTime(new Date).substring(0, 10).replace(/\//g, "-");
        console.log(s.toString()), this.setData({
            table_id: t.table_id,
            tablestatus: t.status,
            start: s,
            timestart: s,
            timeend: s
        }), wx.setNavigationBarTitle({
            title: t.name + "订单"
        }), this.reLoad(), app.setNavigationBarColor(this), app.sjdpageOnLoad(this), app.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(t) {
                console.log(t.data), wx.setStorageSync("system", t.data), e.setData({
                    xtxx: t.data
                })
            }
        })
    },
    smhx: function(t) {
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
    },
    qkt: function(t) {
        var e = this,
            a = this.data.table_id;
        console.log(a), wx.showModal({
            title: "提示",
            content: "确认进行此操作？",
            cancelText: "否",
            confirmText: "是",
            success: function(t) {
                if (t.cancel) return !0;
                t.confirm && (wx.showLoading({
                    title: "操作中",
                    mask: !0
                }), app.util.request({
                    url: "entry/wxapp/DnOpen",
                    cachetime: "0",
                    data: {
                        table_id: a,
                        status: 1 == e.data.tablestatus ? 0 : 1
                    },
                    success: function(t) {
                        if (console.log(t.data), "1" == t.data) {
                            wx.showToast({
                                title: "操作成功",
                                icon: "success",
                                duration: 1e3
                            });
                            var e = getCurrentPages();
                            if (console.log(e), 1 < e.length) {
                                var a = e[e.length - 2];
                                a.setData({
                                    pagenum: 1,
                                    order_list: [],
                                    storelist: []
                                }), a.reLoad()
                            }
                            setTimeout(function() {
                                wx.navigateBack({})
                            }, 1e3)
                        } else wx.showToast({
                            title: "请重试",
                            icon: "loading",
                            duration: 1e3
                        })
                    }
                }))
            }
        })
    },
    dyxp: function(t) {
        var e = t.currentTarget.dataset.id;
        console.log(e), wx.showModal({
            title: "提示",
            content: "是否确认打印此订单小票？",
            cancelText: "否",
            confirmText: "是",
            success: function(t) {
                if (t.cancel) return !0;
                t.confirm && (wx.showLoading({
                    title: "操作中",
                    mask: !0
                }), app.util.request({
                    url: "entry/wxapp/QtPrint",
                    cachetime: "0",
                    data: {
                        order_id: e,
                        type: 1
                    },
                    success: function(t) {
                        console.log(t.data), wx.showToast({
                            title: "操作成功",
                            icon: "success",
                            duration: 1e3
                        })
                    }
                }))
            }
        })
    },
    jjjd: function(t) {
        var e = this,
            a = t.currentTarget.dataset.id;
        console.log(a), wx.showModal({
            title: "提示",
            content: "是否关闭订单？",
            cancelText: "否",
            confirmText: "是",
            success: function(t) {
                if (t.cancel) return !0;
                t.confirm && (wx.showLoading({
                    title: "操作中",
                    mask: !0
                }), app.util.request({
                    url: "entry/wxapp/DnClose",
                    cachetime: "0",
                    data: {
                        order_id: a
                    },
                    success: function(t) {
                        console.log(t.data), "1" == t.data ? (wx.showToast({
                            title: "操作成功",
                            icon: "success",
                            duration: 1e3
                        }), setTimeout(function() {
                            e.doreload(4)
                        }, 1e3)) : wx.showToast({
                            title: "请重试",
                            icon: "loading",
                            duration: 1e3
                        })
                    }
                }))
            }
        })
    },
    wcps: function(t) {
        var e = this,
            a = t.currentTarget.dataset.id;
        console.log(a), wx.showModal({
            title: "提示",
            content: "确认完成此订单？",
            cancelText: "否",
            confirmText: "是",
            success: function(t) {
                if (t.cancel) return !0;
                t.confirm && (wx.showLoading({
                    title: "操作中",
                    mask: !0
                }), app.util.request({
                    url: "entry/wxapp/DnReceivables",
                    cachetime: "0",
                    data: {
                        order_id: a
                    },
                    success: function(t) {
                        console.log(t.data), "1" == t.data ? (wx.showToast({
                            title: "操作成功",
                            icon: "success",
                            duration: 1e3
                        }), setTimeout(function() {
                            e.doreload(3)
                        }, 1e3)) : wx.showToast({
                            title: "请重试",
                            icon: "loading",
                            duration: 1e3
                        })
                    }
                }))
            }
        })
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        console.log("上拉加载", this.data.pagenum);
        !this.data.mygd && this.data.jzgd && (this.setData({
            jzgd: !1
        }), this.reLoad())
    }
});