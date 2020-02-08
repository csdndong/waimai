var app = getApp(), util = require("../../utils/util.js");

Page({
    data: {
        index: 1,
        navbar: [],
        nav: [ {
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
        } ],
        selectedindex: 0,
        isytpj: !1,
        pagenum: 1,
        storelist: [],
        bfstorelist: [],
        mygd: !1,
        jzgd: !0,
        arr: [ {
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
        } ],
        bjyylb: "laba",
        opendh: !1,
        mdoaltoggle: !0
    },
    closehbtoggle: function() {
        this.setData({
            hbtoggle: !1
        });
    },
    previewImage: function(t) {
        var a = this.data.store_info.qrcode;
        console.log(a), wx.previewImage({
            current: a,
            urls: [ a ]
        });
    },
    sjmp: function() {
        this.setData({
            mdoaltoggle: !1,
            opendh: !1
        });
    },
    mdoalclose: function() {
        this.setData({
            mdoaltoggle: !0
        });
    },
    opennav: function() {
        this.setData({
            opendh: !this.data.opendh
        });
    },
    commentPicView: function(t) {
        console.log(t);
        var a = this.data.storelist, e = [], s = t.currentTarget.dataset.index, o = t.currentTarget.dataset.picindex, i = t.currentTarget.dataset.id;
        if (console.log(s, o, i), i == a[s].id) {
            var n = a[s].img;
            for (var r in n) e.push(this.data.url + n[r]);
            wx.previewImage({
                current: this.data.url + n[o],
                urls: e
            });
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
        }), this.getstorelist();
    },
    selectednavbar: function(t) {
        console.log(t);
        var a = this.data.params;
        0 == t.currentTarget.dataset.index && (a.type = "全部"), 1 == t.currentTarget.dataset.index && (a.type = "1"), 
        2 == t.currentTarget.dataset.index && (a.type = "2"), this.setData({
            pagenum: 1,
            storelist: [],
            bfstorelist: [],
            mygd: !1,
            jzgd: !0,
            selectedindex: t.currentTarget.dataset.index,
            params: a
        }), this.getstorelist();
    },
    pdqh: function() {
        wx.navigateTo({
            url: "getnum?storeid=" + this.data.store_info.id
        });
    },
    sy: function() {
        wx.navigateTo({
            url: "fukuan?storeid=" + this.data.store_info.id
        });
    },
    qg: function() {
        wx.navigateTo({
            url: "../xsqg/xsqg?storeid=" + this.data.store_info.id
        });
    },
    pt: function(t) {
        wx.navigateTo({
            url: "../collage/list?store_id=" + this.data.store_info.id + "&store_logo=" + this.data.store_info.logo
        });
    },
    smdc: function() {
        wx.scanCode({
            success: function(t) {
                console.log(t);
                var a = "/" + t.path;
                wx.navigateTo({
                    url: a
                });
            },
            fail: function(t) {
                console.log("扫码fail");
            }
        });
    },
    takeout: function() {
        wx.navigateTo({
            url: "/zh_cjdianc/pages/takeout/takeoutindex?storeid=" + this.data.store_info.id
        });
    },
    plan: function() {
        wx.navigateTo({
            url: "/zh_cjdianc/pages/reserve/reserve?storeid=" + this.data.store_info.id
        });
    },
    qsy: function(t) {
        console.log(t.currentTarget.dataset.type), "2" != t.currentTarget.dataset.type && wx.navigateTo({
            url: "/zh_cjdianc/pages/takeout/takeoutindex?storeid=" + this.data.store_info.id
        });
    },
    ljlq: function(t) {
        console.log(t.currentTarget.dataset.qid);
        var a = this, e = wx.getStorageSync("users").id;
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
                    a.Coupons();
                }, 1e3));
            }
        });
    },
    getstorelist: function() {
        var s = this, o = s.data.pagenum;
        s.data.params.page = o, s.data.params.pagesize = 10, console.log(o, s.data.params), 
        s.setData({
            isjzz: !0
        }), app.util.request({
            url: "entry/wxapp/AssessList",
            cachetime: "0",
            data: s.data.params,
            success: function(t) {
                console.log("分页返回的商家列表数据", t.data);
                var a = [ {
                    name: "全部",
                    num: t.data.all
                }, {
                    name: "满意",
                    num: t.data.ok
                }, {
                    name: "不满意",
                    num: t.data.no
                } ], e = s.data.bfstorelist;
                e = function(t) {
                    for (var a = [], e = 0; e < t.length; e++) -1 == a.indexOf(t[e]) && a.push(t[e]);
                    return a;
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
                }), console.log(e);
            }
        });
    },
    onLoad: function(t) {
        var e = this;
        app.setNavigationBarColor(this);
        var a = decodeURIComponent(t.scene);
        console.log("scene", a), "undefined" != a && (getApp().sjid = a), null != t.sjid && (console.log("转发获取到的sjid:", t.sjid), 
        getApp().sjid = t.sjid), console.log(t, getApp().sjid), this.setData({
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
                });
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
                });
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
                });
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
                    tjcarr: t.data
                });
            }
        });
    },
    Coupons: function() {
        var o = this, t = wx.getStorageSync("users").id;
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
                }), console.log(a);
            }
        });
    },
    jumps: function(t) {
        var a = t.currentTarget.dataset.id, e = t.currentTarget.dataset.name, s = t.currentTarget.dataset.appid, o = t.currentTarget.dataset.src, i = t.currentTarget.dataset.wb_src, n = t.currentTarget.dataset.type;
        console.log(a, e, s, o, i, n), 1 == n ? (console.log(o), wx.navigateTo({
            url: o
        })) : 2 == n ? (wx.setStorageSync("vr", i), wx.navigateTo({
            url: "../car/car"
        })) : 3 == n && wx.navigateToMiniProgram({
            appId: s
        });
    },
    refresh: function(t) {
        var d = this, l = util.formatTime(new Date()).slice(11, 16);
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
                        t.status, t.dataUrl, t.currentPosition, t.duration, t.downloadPercent;
                    },
                    fail: function(t) {
                        console.log(t);
                    },
                    complete: function(t) {
                        console.log(t);
                    }
                })), wx.setNavigationBarTitle({
                    title: t.data.store.name
                }), d.setData({
                    store_info: t.data.store,
                    storeset: t.data.storeset
                });
                var a = t.data.storeset, e = d.data.nav;
                "1" == a.is_dn && (e[1].active = !0, "" != a.dn_img && (e[1].img = a.dn_img), "" != a.dn_name && (e[1].name = a.dn_name), 
                "" != a.dnsm && (e[1].smwz = a.dnsm)), "1" == a.is_wm && (e[0].active = !0, "" != a.wm_img && (e[0].img = a.wm_img), 
                "" != a.wm_name && (e[0].name = a.wm_name), "" != a.wmsm && (e[0].smwz = a.wmsm)), 
                "1" == a.is_yy && (e[2].active = !0, "" != a.yy_img && (e[2].img = a.yy_img), "" != a.yy_name && (e[2].name = a.yy_name), 
                "" != a.sysm && (e[2].smwz = a.yysm)), "1" == a.is_sy && (e[3].active = !0, "" != a.sy_img && (e[3].img = a.sy_img), 
                "" != a.sy_name && (e[3].name = a.sy_name), "" != a.sysm && (e[3].smwz = a.sysm)), 
                "1" == a.is_qg && "1" == getApp().xtxx.qggn && (e[4].active = !0, "" != a.qg_img && (e[4].img = a.qg_img), 
                "" != a.qg_name && (e[4].name = a.qg_name), "" != a.qgsm && (e[4].smwz = a.qgsm)), 
                "1" == a.is_pt && "1" == getApp().xtxx.ptgn && (e[5].active = !0, "" != a.pt_img && (e[5].img = a.pt_img), 
                "" != a.pt_name && (e[5].name = a.pt_name), "" != a.ptsm && (e[5].smwz = a.ptsm)), 
                "1" == a.is_pd && (e[6].active = !0, "" != a.pd_img && (e[6].img = a.pd_img), "" != a.pd_name && (e[6].name = a.pd_name), 
                "" != a.pdsm && (e[6].smwz = a.pdsm)), console.log(e), d.setData({
                    nav: e
                });
                var s = t.data.store.time, o = t.data.store.time2, i = t.data.store.time3, n = t.data.store.time4, r = t.data.store.is_rest;
                console.log("当前的系统时间为" + l), console.log("商家的营业时间从" + s + "至" + o, i + "至" + n), 
                1 != r ? (console.log("商家正在营业" + r), s < n ? s < l && l < o || i < l && l < n || i < l && n < i ? (console.log("商家正常营业"), 
                d.setData({
                    time: 1
                })) : l < s || o < l && l < i ? (console.log("商家还没开店呐，稍等一会儿可以吗？"), d.setData({
                    time: 2
                })) : n < l && (console.log("商家以及关店啦，明天再来吧"), d.setData({
                    time: 3
                })) : n < s && (s < l && l < o || i < l && n < l || l < i && l < n ? (console.log("商家正常营业"), 
                d.setData({
                    time: 1
                })) : l < s || o < l && l < i ? (console.log("商家还没开店呐，稍等一会儿可以吗？"), d.setData({
                    time: 2
                })) : n < l && (console.log("商家以及关店啦，明天再来吧"), d.setData({
                    time: 3
                })))) : d.setData({
                    time: 2
                });
            }
        });
    },
    seller_coupon: function() {
        this.setData({
            index: 0
        });
    },
    seller_dishes: function() {
        this.setData({
            index: 1
        });
    },
    seller_evalate: function() {
        this.setData({
            index: 2
        });
    },
    seller_info: function(t) {
        var a = this.data.store_info.coordinates.split(","), e = this.data.store_info;
        console.log(a), wx.openLocation({
            latitude: parseFloat(a[0]),
            longitude: parseFloat(a[1]),
            address: e.address,
            name: e.name
        });
    },
    maketel: function() {
        wx.makePhoneCall({
            phoneNumber: this.data.store_info.tel
        });
    },
    onReady: function() {},
    onShow: function() {
        var a = this;
        app.getUserInfo(function(t) {
            console.log(t), a.Coupons();
        });
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
        }));
    },
    onUnload: function() {
        wx.stopBackgroundAudio();
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        console.log("上拉加载", this.data.pagenum);
        this.data.mygd || !this.data.jzgd || this.data.isjzz || (this.setData({
            jzgd: !1
        }), this.getstorelist());
    },
    onShareAppMessage: function() {
        var t = this.data.xtxx;
        return console.log(t), {
            title: this.data.store_info.name,
            path: "/zh_cjdianc/pages/seller/index?sjid=" + this.data.store_info.id,
            success: function(t) {},
            fail: function(t) {}
        };
    }
});