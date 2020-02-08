/*   time:2019-07-18 01:03:18*/
var dsq, app = getApp(),
    siteinfo = require("../../../../siteinfo.js");
Page({
    data: {
        selectedindex: 0,
        topnav: [{
            img: "../../../img/icon/djd.png",
            img1: "../../../img/icon/wdjd.png",
            name: "待接单"
        }, {
            img: "../../../img/icon/dps.png",
            img1: "../../../img/icon/wdps.png",
            name: "待送达"
        }, {
            img: "../../../img/icon/dzt.png",
            img1: "../../../img/icon/wdzt.png",
            name: "待自提"
        }, {
            img: "../../../img/icon/ywc.png",
            img1: "../../../img/icon/wywc.png",
            name: "已完成"
        }, {
            img: "../../../img/icon/sh.png",
            img1: "../../../img/icon/wsh.png",
            name: "售后/退款"
        }],
        open: !1,
        pagenum: 1,
        order_list: [],
        storelist: [],
        mygd: !1,
        jzgd: !0,
        hide: 1
    },
    hide: function(t) {
        this.setData({
            hide: 1
        })
    },
    psxq: function(t) {
        var e = this,
            a = t.currentTarget.dataset.id,
            o = t.currentTarget.dataset.sjid,
            n = t.currentTarget.dataset.psmode;
        console.log(a, o, n), wx.showLoading({
            title: "加载中",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/GetStorePsInfo",
            cachetime: "0",
            data: {
                store_id: o,
                order_id: a
            },
            success: function(t) {
                console.log(t.data), "达达配送" != n || null != t.data.result ? e.setData({
                    psxx: t.data,
                    psmode: n,
                    hide: 2
                }) : wx.showModal({
                    title: "提示",
                    content: "配送信息异常" + t.data
                })
            }
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
            o = t.currentTarget.dataset.address;
        console.log(e, a), wx.openLocation({
            latitude: parseFloat(e),
            longitude: parseFloat(a),
            address: o,
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
        for (var o = 0, n = a.length; o < n; ++o) a[o].open = o == e && !a[o].open;
        this.setData({
            order_list: a
        })
    },
    reLoad: function() {
        var t, a = this,
            e = this.data.status || 1,
            o = wx.getStorageSync("sjdsjid"),
            n = this.data.pagenum,
            s = "";
        1 == e && (t = "2"), 2 == e && (t = "3", s = "2"), 3 == e && (t = "3", s = "1"), 4 == e && (t = "4,5"), 5 == e && (t = "6,7,8,9,10"), console.log(e, t, s, o, n), app.util.request({
            url: "entry/wxapp/StoreWmOrder",
            cachetime: "0",
            data: {
                state: t,
                zt: s,
                store_id: o,
                page: n,
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
            a = wx.getStorageSync("sjdsjid"),
            o = siteinfo.siteroot.replace("app/index.php", "");
        console.log(a, o), this.reLoad(), app.setNavigationBarColor(this), app.sjdpageOnLoad(this), app.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(t) {
                console.log(t.data), wx.setStorageSync("system", t.data), wx.setNavigationBarTitle({
                    title: t.data.wm_name || "外卖"
                }), e.setData({
                    xtxx: t.data
                })
            }
        }), dsq = setInterval(function() {
            wx.getStorageSync("yybb") ? app.util.request({
                url: "entry/wxapp/NewOrder",
                cachetime: "0",
                data: {
                    store_id: a
                },
                success: function(t) {
                    console.log(t), 1 == t.data && wx.playBackgroundAudio({
                        dataUrl: o + "addons/zh_cjdianc/template/images/wm.wav",
                        title: "语音播报"
                    }), 2 == t.data && wx.playBackgroundAudio({
                        dataUrl: o + "addons/zh_cjdianc/template/images/dn.wav",
                        title: "语音播报"
                    })
                }
            }) : clearInterval(dsq)
        }, 1e4)
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
    djjd: function(t) {
        var e = this,
            a = t.currentTarget.dataset.id;
        console.log(a), wx.showModal({
            title: "提示",
            content: "是否确认接单？",
            cancelText: "否",
            confirmText: "是",
            success: function(t) {
                if (t.cancel) return !0;
                t.confirm && (wx.showLoading({
                    title: "操作中",
                    mask: !0
                }), app.util.request({
                    url: "entry/wxapp/JdOrder",
                    cachetime: "0",
                    data: {
                        order_id: a
                    },
                    success: function(t) {
                        console.log(t.data), "1" == t.data ? (wx.showToast({
                            title: "接单成功",
                            icon: "success",
                            duration: 1e3
                        }), setTimeout(function() {
                            e.doreload(2)
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
    jjjd: function(t) {
        var e = this,
            a = t.currentTarget.dataset.id;
        console.log(a), wx.showModal({
            title: "提示",
            content: "是否拒绝接单？",
            cancelText: "否",
            confirmText: "是",
            success: function(t) {
                if (t.cancel) return !0;
                t.confirm && (wx.showLoading({
                    title: "操作中",
                    mask: !0
                }), app.util.request({
                    url: "entry/wxapp/JjOrder",
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
                            e.doreload(5)
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
                    url: "entry/wxapp/OkOrder",
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
    jjtk: function(t) {
        var e = this,
            a = t.currentTarget.dataset.id;
        console.log(a), wx.showModal({
            title: "提示",
            content: "是否拒绝退款？",
            cancelText: "否",
            confirmText: "是",
            success: function(t) {
                if (t.cancel) return !0;
                t.confirm && (wx.showLoading({
                    title: "操作中",
                    mask: !0
                }), app.util.request({
                    url: "entry/wxapp/JjTk",
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
                            e.doreload(5)
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
    tgtk: function(t) {
        var e = this,
            a = t.currentTarget.dataset.id;
        console.log(a), wx.showModal({
            title: "提示",
            content: "是否通过退款？",
            cancelText: "否",
            confirmText: "是",
            success: function(t) {
                if (t.cancel) return !0;
                t.confirm && (wx.showLoading({
                    title: "操作中",
                    mask: !0
                }), app.util.request({
                    url: "entry/wxapp/TkTg",
                    cachetime: "0",
                    data: {
                        order_id: a
                    },
                    success: function(t) {
                        console.log(t.data), "1" == t.data ? (wx.showLoading({
                            title: "操作中",
                            mask: !0
                        }), setTimeout(function() {
                            e.doreload(5)
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
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {
        clearInterval(dsq)
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        console.log("上拉加载", this.data.pagenum);
        !this.data.mygd && this.data.jzgd && (this.setData({
            jzgd: !1
        }), this.reLoad())
    }
});