/*   time:2019-07-18 01:07:50*/
var app = getApp(),
    util = require("../../utils/util.js");
Page({
    data: {
        index: 1,
        navbar: [],
        nav: [{
            bindtap: "takeout",
            img: "../../img/seller/two.png",
            name: "外卖",
            active: !1,
            smwz: "快速送达"
        }, {
            bindtap: "smdc",
            img: "../../img/seller/six.png",
            name: "扫码点餐",
            active: !1,
            smwz: "扫一扫轻松下单"
        }, {
            bindtap: "plan",
            img: "../../img/seller/one.png",
            name: "预约",
            active: !1,
            smwz: "提前预定"
        }, {
            bindtap: "sy",
            img: "../../img/seller/four.png",
            name: "收银",
            active: !1,
            smwz: "当面收款"
        }, {
            bindtap: "qg",
            img: "../../img/seller/yysj.png",
            name: "抢购",
            active: !1,
            smwz: "限时抢购"
        }, {
            bindtap: "pt",
            img: "../../img/seller/zdjd.png",
            name: "拼团",
            active: !1,
            smwz: "拼团活动"
        }, {
            bindtap: "pdqh",
            img: "../../img/seller/eight.png",
            name: "排队取号",
            active: !1
        }, {
            bindtap: "cj",
            img: "../../img/seller/three.png",
            name: "存酒",
            active: !1
        }, {
            bindtap: "hjfwy",
            img: "../../img/seller/five.png",
            name: "呼叫服务员",
            active: !1
        }, {
            bindtap: "yhq",
            img: "../../img/seller/seven.png",
            name: "优惠券",
            active: !1
        }],
        selectedindex: 0,
        isytpj: !1,
        pagenum: 1,
        storelist: [],
        bfstorelist: [],
        mygd: !1,
        jzgd: !0,
        arr: [{
            logo: "/zh_cjdianc/img/tabindexf.png",
            logo2: "/zh_cjdianc/img/tabindex.png",
            title: "首页",
            title_color: "#34aaff",
            title_color2: "#888",
            url: "/zh_cjdianc/pages/index/index"
        }, {
            logo: "/zh_cjdianc/img/tabddf.png",
            logo2: "/zh_cjdianc/img/tabdd.png",
            title: "订单",
            title_color: "#34aaff",
            title_color2: "#888",
            url: "/zh_cjdianc/pages/wddd/order"
        }, {
            logo: "/zh_cjdianc/img/tabmyf.png",
            logo2: "/zh_cjdianc/img/tabmy.png",
            title: "我的",
            title_color: "#34aaff",
            title_color2: "#888",
            url: "/zh_cjdianc/pages/my/index"
        }],
        bjyylb: "laba",
        opendh: !1,
        mdoaltoggle: !0
    },
    closehbtoggle: function() {
        this.setData({
            hbtoggle: !1
        })
    },
    previewImage: function(t) {
        var a = this.data.store_info.qrcode;
        console.log(a), wx.previewImage({
            current: a,
            urls: [a]
        })
    },
    sjmp: function() {
        this.setData({
            mdoaltoggle: !1,
            opendh: !1
        })
    },
    mdoalclose: function() {
        this.setData({
            mdoaltoggle: !0
        })
    },
    opennav: function() {
        this.setData({
            opendh: !this.data.opendh
        })
    },
    commentPicView: function(t) {
        console.log(t);
        var a = this.data.storelist,
            e = [],
            s = t.currentTarget.dataset.index,
            o = t.currentTarget.dataset.picindex,
            n = t.currentTarget.dataset.id;
        if (console.log(s, o, n), n == a[s].id) {
            var i = a[s].img;
            for (var r in i) e.push(this.data.url + i[r]);
            wx.previewImage({
                current: this.data.url + i[o],
                urls: e
            })
        }
    },
    ytpj: function() {
        var t = this.data.params;
        this.data.isytpj ? t.img = "" : t.img = "1", this.setData({
            pagenum: 1,
            storelist: [],
            bfstorelist: [],
            mygd: !1,
            jzgd: !0,
            isytpj: !this.data.isytpj,
            params: t
        }), this.getstorelist()
    },
    selectednavbar: function(t) {
        console.log(t);
        var a = this.data.params;
        0 == t.currentTarget.dataset.index && (a.type = "全部"), 1 == t.currentTarget.dataset.index && (a.type = "1"), 2 == t.currentTarget.dataset.index && (a.type = "2"), this.setData({
            pagenum: 1,
            storelist: [],
            bfstorelist: [],
            mygd: !1,
            jzgd: !0,
            selectedindex: t.currentTarget.dataset.index,
            params: a
        }), this.getstorelist()
    },
    pdqh: function() {
        wx.navigateTo({
            url: "getnum?storeid=" + this.data.store_info.id
        })
    },
    sy: function() {
        wx.navigateTo({
            url: "fukuan?storeid=" + this.data.store_info.id
        })
    },
    qg: function() {
        wx.navigateTo({
            url: "../xsqg/xsqg?storeid=" + this.data.store_info.id
        })
    },
    pt: function(t) {
        wx.navigateTo({
            url: "../collage/list?store_id=" + this.data.store_info.id + "&store_logo=" + this.data.store_info.logo
        })
    },
    smdc: function() {
        wx.scanCode({
            success: function(t) {
                console.log(t);
                var a = "/" + t.path;
                wx.navigateTo({
                    url: a
                })
            },
            fail: function(t) {
                console.log("扫码fail")
            }
        })
    },
    takeout: function() {
        wx.navigateTo({
            url: "/zh_cjdianc/pages/takeout/takeoutindex?storeid=" + this.data.store_info.id
        })
    },
    plan: function() {
        wx.navigateTo({
            url: "/zh_cjdianc/pages/reserve/reserve?storeid=" + this.data.store_info.id
        })
    },
    qsy: function(t) {
        console.log(t.currentTarget.dataset.type), "2" != t.currentTarget.dataset.type && wx.navigateTo({
            url: "/zh_cjdianc/pages/takeout/takeoutindex?storeid=" + this.data.store_info.id
        })
    },
    ljlq: function(t) {
        console.log(t.currentTarget.dataset.qid);
        var a = this,
            e = wx.getStorageSync("users").id;
        wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/LqCoupons",
            cachetime: "0",
            data: {
                user_id: e,
                coupon_id: t.currentTarget.dataset.qid
            },
            success: function(t) {
                console.log(t), "1" == t.data && (wx.showLoading({
                    title: "领取成功",
                    mask: !0
                }), setTimeout(function() {
                    a.Coupons()
                }, 1e3))
            }
        })
    },
    getstorelist: function() {
        var s = this,
            o = s.data.pagenum;
        s.data.params.page = o, s.data.params.pagesize = 10, console.log(o, s.data.params), s.setData({
            isjzz: !0
        }), app.util.request({
            url: "entry/wxapp/AssessList",
            cachetime: "0",
            data: s.data.params,
            success: function(t) {
                console.log("分页返回的商家列表数据", t.data);
                var a = [{
                    name: "全部",
                    num: t.data.all
                }, {
                    name: "满意",
                    num: t.data.ok
                }, {
                    name: "不满意",
                    num: t.data.no
                }],
                    e = s.data.bfstorelist;
                e = function(t) {
                    for (var a = [], e = 0; e < t.length; e++) - 1 == a.indexOf(t[e]) && a.push(t[e]);
                    return a
                }(e = e.concat(t.data.assess)), s.setData({
                    storelist: e,
                    bfstorelist: e,
                    navbar: a
                }), t.data.assess.length < 10 ? s.setData({
                    mygd: !0,
                    jzgd: !0,
                    isjzz: !1
                }) : s.setData({
                    jzgd: !0,
                    pagenum: o + 1,
                    isjzz: !1
                }), console.log(e)
            }
        })
    },
    onLoad: function(t) {
        var e = this;
        app.setNavigationBarColor(this);
        var a = decodeURIComponent(t.scene);
        console.log("scene", a), "undefined" != a && (getApp().sjid = a), null != t.sjid && (console.log("转发获取到的sjid:", t.sjid), getApp().sjid = t.sjid), console.log(t, getApp().sjid), this.setData({
            params: {
                store_id: getApp().sjid,
                type: "全部",
                img: ""
            }
        }), this.getstorelist(), e.refresh(getApp().sjid), app.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(t) {
                console.log(t);
                var a = t.data;
                getApp().xtxx1 = a, app.pageOnLoad(e), e.setData({
                    xtxx: a
                })
            }
        }), app.util.request({
            url: "entry/wxapp/Llz",
            cachetime: "0",
            data: {
                type: "5"
            },
            success: function(t) {
                console.log(t), e.setData({
                    dbllz: t.data
                })
            }
        }), app.util.request({
            url: "entry/wxapp/StoreAd",
            cachetime: "0",
            data: {
                store_id: getApp().sjid
            },
            success: function(t) {
                console.log(t.data), e.setData({
                    slider: t.data
                })
            }
        }), app.util.request({
            url: "entry/wxapp/TjGoods",
            cachetime: "0",
            data: {
                store_id: getApp().sjid
            },
            success: function(t) {
                console.log(t.data);
                for (var a = 0; a < t.data.length; a++) t.data[a].discount = (Number(t.data[a].money) / Number(t.data[a].money2) * 10).toFixed(1);
                e.setData({
                    index: t.data.length ? 1 : 0,
                    tjcarr: t.data
                })
            }
        })
    },
    Coupons: function() {
        var o = this,
            t = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/Coupons",
            cachetime: "0",
            data: {
                store_id: getApp().sjid,
                user_id: t
            },
            success: function(t) {
                console.log(t.data);
                for (var a = [], e = 0; e < t.data.length; e++) t.data[e].sysl = parseInt((Number(t.data[e].number) - Number(t.data[e].stock)) / Number(t.data[e].number) * 100);
                for (var s = 0; s < t.data.length; s++) "2" == t.data[s].state && t.data[s].sysl < 100 && a.push(t.data[s]);
                o.setData({
                    Coupons: t.data,
                    wlqyhq: a,
                    hbtoggle: 0 < a.length
                }), console.log(a)
            }
        })
    },
    jumps: function(t) {
        var a = t.currentTarget.dataset.id,
            e = t.currentTarget.dataset.name,
            s = t.currentTarget.dataset.appid,
            o = t.currentTarget.dataset.src,
            n = t.currentTarget.dataset.wb_src,
            i = t.currentTarget.dataset.type;
        console.log(a, e, s, o, n, i), 1 == i ? (console.log(o), wx.navigateTo({
            url: o
        })) : 2 == i ? (wx.setStorageSync("vr", n), wx.navigateTo({
            url: "../car/car"
        })) : 3 == i && wx.navigateToMiniProgram({
            appId: s
        })
    },
    refresh: function(t) {
        var l = this,
            c = util.formatTime(new Date).slice(11, 16);
        app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: t
            },
            success: function(t) {
                console.log("商家详情"), console.log(t), "" != t.data.store.store_mp3 && "1" == t.data.store.is_mp3 && (wx.playBackgroundAudio({
                    dataUrl: t.data.store.store_mp3
                }), wx.getBackgroundAudioPlayerState({
                    success: function(t) {
                        console.log(t);
                        t.status, t.dataUrl, t.currentPosition, t.duration, t.downloadPercent
                    },
                    fail: function(t) {
                        console.log(t)
                    },
                    complete: function(t) {
                        console.log(t)
                    }
                })), wx.setNavigationBarTitle({
                    title: t.data.store.name
                });
                var a = t.data.store.tel;
                l.setData({
                    store_info: t.data.store,
                    storeset: t.data.storeset,
                    paytel: "1" == getApp().xtxx.is_pay && 0 < Number(getApp().xtxx.pay_money) ? a.substring(0, 3) + "****" + a.substring(a.length - 4) : a
                });
                var e = t.data.storeset,
                    s = l.data.nav;
                "1" == e.is_dn && (s[1].active = !0, "" != e.dn_img && (s[1].img = e.dn_img), "" != e.dn_name && (s[1].name = e.dn_name), "" != e.dnsm && (s[1].smwz = e.dnsm)), "1" == e.is_wm && (s[0].active = !0, "" != e.wm_img && (s[0].img = e.wm_img), "" != e.wm_name && (s[0].name = e.wm_name), "" != e.wmsm && (s[0].smwz = e.wmsm)), "1" == e.is_yy && (s[2].active = !0, "" != e.yy_img && (s[2].img = e.yy_img), "" != e.yy_name && (s[2].name = e.yy_name), "" != e.sysm && (s[2].smwz = e.yysm)), "1" == e.is_sy && (s[3].active = !0, "" != e.sy_img && (s[3].img = e.sy_img), "" != e.sy_name && (s[3].name = e.sy_name), "" != e.sysm && (s[3].smwz = e.sysm)), "1" == e.is_qg && "1" == getApp().xtxx.qggn && (s[4].active = !0, "" != e.qg_img && (s[4].img = e.qg_img), "" != e.qg_name && (s[4].name = e.qg_name), "" != e.qgsm && (s[4].smwz = e.qgsm)), "1" == e.is_pt && "1" == getApp().xtxx.ptgn && (s[5].active = !0, "" != e.pt_img && (s[5].img = e.pt_img), "" != e.pt_name && (s[5].name = e.pt_name), "" != e.ptsm && (s[5].smwz = e.ptsm)), "1" == e.is_pd && (s[6].active = !0, "" != e.pd_img && (s[6].img = e.pd_img), "" != e.pd_name && (s[6].name = e.pd_name), "" != e.pdsm && (s[6].smwz = e.pdsm)), console.log(s), l.setData({
                    nav: s
                });
                var o = t.data.store.time,
                    n = t.data.store.time2,
                    i = t.data.store.time3,
                    r = t.data.store.time4,
                    d = t.data.store.is_rest;
                console.log("当前的系统时间为" + c), console.log("商家的营业时间从" + o + "至" + n, i + "至" + r), 1 != d ? (console.log("商家正在营业" + d), o < r ? o < c && c < n || i < c && c < r || i < c && r < i ? (console.log("商家正常营业"), l.setData({
                    time: 1
                })) : c < o || n < c && c < i ? (console.log("商家还没开店呐，稍等一会儿可以吗？"), l.setData({
                    time: 2
                })) : r < c && (console.log("商家以及关店啦，明天再来吧"), l.setData({
                    time: 3
                })) : r < o && (o < c && c < n || i < c && r < c || c < i && c < r ? (console.log("商家正常营业"), l.setData({
                    time: 1
                })) : c < o || n < c && c < i ? (console.log("商家还没开店呐，稍等一会儿可以吗？"), l.setData({
                    time: 2
                })) : r < c && (console.log("商家以及关店啦，明天再来吧"), l.setData({
                    time: 3
                })))) : l.setData({
                    time: 2
                })
            }
        })
    },
    seller_coupon: function() {
        this.setData({
            index: 0
        })
    },
    seller_dishes: function() {
        this.setData({
            index: 1
        })
    },
    seller_evalate: function() {
        this.setData({
            index: 2
        })
    },
    seller_info: function(t) {
        var a = this.data.store_info.coordinates.split(","),
            e = this.data.store_info;
        console.log(a), wx.openLocation({
            latitude: parseFloat(a[0]),
            longitude: parseFloat(a[1]),
            address: e.address,
            name: e.name
        })
    },
    maketel: function(t) {
        var a = this,
            e = this.data.store_info.tel,
            s = this.data.paytel;
        console.log(s), "-1" != s.indexOf("****") ? wx.showModal({
            title: "提示",
            content: "查看电话需付费" + getApp().xtxx.pay_money + "元",
            success: function(t) {
                t.confirm && (console.log("用户点击确定"), app.util.request({
                    url: "entry/wxapp/telPay",
                    cachetime: "0",
                    data: {
                        openid: a.data.userinfo.openid,
                        pay_money: getApp().xtxx.pay_money
                    },
                    success: function(t) {
                        wx.requestPayment({
                            timeStamp: t.data.timeStamp,
                            nonceStr: t.data.nonceStr,
                            package: t.data.package,
                            signType: t.data.signType,
                            paySign: t.data.paySign,
                            success: function(t) {
                                console.log(t)
                            },
                            complete: function(t) {
                                console.log(t), "requestPayment:fail cancel" == t.errMsg && wx.showToast({
                                    title: "取消支付"
                                }), "requestPayment:ok" == t.errMsg && (wx.showToast({
                                    title: "支付成功",
                                    duration: 1e3
                                }), a.setData({
                                    paytel: e
                                }))
                            }
                        })
                    }
                }))
            }
        }) : (wx.makePhoneCall({
            phoneNumber: e
        }), a.setData({
            paytel: e
        }))
    },
    onReady: function() {},
    onShow: function() {
        var a = this;
        app.getUserInfo(function(t) {
            console.log(t), a.Coupons(), a.setData({
                userinfo: t
            })
        })
    },
    onHide: function() {},
    gbbjyy: function() {
        var t = this.data.bjyylb;
        "laba" == t && (wx.stopBackgroundAudio(), this.setData({
            bjyylb: "laba1"
        }), wx.showToast({
            title: "音乐已关闭"
        })), "laba1" == t && (wx.playBackgroundAudio({
            dataUrl: this.data.store_info.store_mp3
        }), this.setData({
            bjyylb: "laba"
        }), wx.showToast({
            title: "音乐已开启"
        }))
    },
    onUnload: function() {
        wx.stopBackgroundAudio()
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        console.log("上拉加载", this.data.pagenum);
        this.data.mygd || !this.data.jzgd || this.data.isjzz || (this.setData({
            jzgd: !1
        }), this.getstorelist())
    },
    onShareAppMessage: function() {
        var t = this.data.xtxx;
        return console.log(t), {
            title: this.data.store_info.name,
            path: "/zh_cjdianc/pages/seller/index?sjid=" + this.data.store_info.id,
            success: function(t) {},
            fail: function(t) {}
        }
    }
});