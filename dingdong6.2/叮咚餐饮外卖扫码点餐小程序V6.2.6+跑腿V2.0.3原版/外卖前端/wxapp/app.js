var _App;

function _defineProperty(a, o, e) {
    return o in a ? Object.defineProperty(a, o, {
        value: e,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : a[o] = e, a;
}

App((_defineProperty(_App = {
    onLaunch: function() {
        var o = this;
        wx.getSystemInfo({
            success: function(a) {
                console.log(a.model), -1 != a.model.search("iPhone X") ? o.globalData.isIpx = !0 : o.globalData.isIpx = !1;
            }
        });
    },
    onShow: function() {
        console.log(getCurrentPages());
    },
    onHide: function() {
        console.log(getCurrentPages());
    },
    onError: function(a) {
        console.log(a);
    },
    util: require("we7/resource/js/util.js"),
    getimgUrl: function(o) {
        var a = this.globalData.imgurl;
        console.log(a, o), o.setData({
            url: a
        });
        var e = this;
        a || e.util.request({
            url: "entry/wxapp/Url",
            success: function(a) {
                console.log(a), e.globalData.imgurl = a.data, e.getimgUrl(o);
            }
        });
    },
    setNavigationBarColor: function(o) {
        var a = this.globalData.color, e = this.globalData.imgurl;
        console.log(a, e, o), a && wx.setNavigationBarColor({
            frontColor: "#ffffff",
            backgroundColor: a
        }), o.setData({
            color: a,
            url: e,
            isIpx: this.globalData.isIpx
        });
        var t = this;
        a || t.util.request({
            url: "entry/wxapp/system",
            success: function(a) {
                console.log(a), getApp().xtxx = a.data, t.globalData.imgurl = a.data.attachurl, 
                t.globalData.color = a.data.color || "#34aaff", t.setNavigationBarColor(o);
            }
        });
    },
    pageOnLoad: function(l) {
        var t = this;
        function n(a) {
            console.log(a);
            var o = !1, e = l.route || l.__route__ || null;
            for (var t in a.navs) a.navs[t].url === "/" + e ? o = a.navs[t].active = !0 : a.navs[t].active = !1;
            o && l.setData({
                _navbar: a
            });
        }
        console.log("----setPageNavbar----"), console.log(l);
        var i = {
            background_image: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEX///+nxBvIAAAACklEQVQI12NgAAAAAgAB4iG8MwAAAABJRU5ErkJggg==",
            border_color: "rgba(0,0,0,.1)"
        }, a = t.globalData.navbar;
        console.log(a), a && n(a), a || t.util.request({
            url: "entry/wxapp/nav",
            success: function(a) {
                var o = getApp().xtxx1;
                if (console.log(a, o), 0 == a.data.length) {
                    if ("1" == o.model) var e = [ {
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
                    } ];
                    if ("2" == o.model) e = [ {
                        logo: "/zh_cjdianc/img/tabindexf.png",
                        logo2: "/zh_cjdianc/img/tabindex.png",
                        title: "首页",
                        title_color: "#34aaff",
                        title_color2: "#888",
                        url: "/zh_cjdianc/pages/seller/index"
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
                    } ];
                    if ("4" == o.model) e = [ {
                        logo: "/zh_cjdianc/img/tabindexf.png",
                        logo2: "/zh_cjdianc/img/tabindex.png",
                        title: "首页",
                        title_color: "#34aaff",
                        title_color2: "#888",
                        url: "/zh_cjdianc/pages/seller/indextakeout"
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
                    } ];
                    i.navs = e, n(i), t.globalData.navbar = i;
                } else i.navs = a.data, n(i), t.globalData.navbar = i;
            }
        });
    },
    title: function(a) {
        if ("" == a) return !0;
        wx.showModal({
            title: "",
            content: a
        });
    },
    getUserInfo: function(o) {
        var e = this, a = this.globalData.userInfo;
        console.log(a), a ? "function" == typeof o && o(a) : wx.login({
            success: function(a) {
                wx.showLoading({
                    title: "正在登录",
                    mask: !0
                }), console.log(a.code), e.util.request({
                    url: "entry/wxapp/Openid",
                    cachetime: "0",
                    data: {
                        code: a.code
                    },
                    header: {
                        "content-type": "application/json"
                    },
                    dataType: "json",
                    success: function(a) {
                        wx.showLoading({
                            title: "正在登录",
                            mask: !0
                        }), console.log("openid信息", a.data), getApp().getOpenId = a.data.openid, getApp().getSK = a.data.session_key, 
                        e.util.request({
                            url: "entry/wxapp/login",
                            cachetime: "0",
                            data: {
                                openid: a.data.openid
                            },
                            header: {
                                "content-type": "application/json"
                            },
                            dataType: "json",
                            success: function(a) {
                                getApp().getuniacid = a.data.uniacid, wx.setStorageSync("users", a.data), e.globalData.userInfo = a.data, 
                                "function" == typeof o && o(e.globalData.userInfo);
                            }
                        });
                    },
                    fail: function(a) {},
                    complete: function(a) {}
                });
            }
        });
    },
    sjdpageOnLoad: function(l) {
        var o = this;
        function e(a) {
            console.log(a);
            var o = !1, e = l.route || l.__route__ || null;
            for (var t in a.navs) a.navs[t].url === "/" + e ? o = a.navs[t].active = !0 : a.navs[t].active = !1;
            o && l.setData({
                _navbar: a
            });
        }
        console.log("----setPageNavbar----"), console.log(l);
        var t = {
            background_image: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEX///+nxBvIAAAACklEQVQI12NgAAAAAgAB4iG8MwAAAABJRU5ErkJggg==",
            border_color: "rgba(0,0,0,.1)"
        }, a = o.globalData.sjdnavbar;
        console.log(a), a && e(a), a || o.util.request({
            url: "entry/wxapp/nav",
            success: function(a) {
                console.log(a);
                t.navs = [ {
                    logo: "/zh_cjdianc/img/tabindexf.png",
                    logo2: "/zh_cjdianc/img/tabindex.png",
                    title: "外卖订单",
                    title_color: "#34aaff",
                    title_color2: "#888",
                    url: "/zh_cjdianc/pages/sjzx/wmdd/wmdd"
                }, {
                    logo: "/zh_cjdianc/img/tabdnf.png",
                    logo2: "/zh_cjdianc/img/tabdn.png",
                    title: "店内订单",
                    title_color: "#34aaff",
                    title_color2: "#888",
                    url: "/zh_cjdianc/pages/sjzx/dndd/dndd"
                }, {
                    logo: "/zh_cjdianc/img/tabglf.png",
                    logo2: "/zh_cjdianc/img/tabgl.png",
                    title: "商品管理",
                    title_color: "#34aaff",
                    title_color2: "#888",
                    url: "/zh_cjdianc/pages/sjzx/spgl/cplb"
                }, {
                    logo: "/zh_cjdianc/img/tabddf.png",
                    logo2: "/zh_cjdianc/img/tabdd.png",
                    title: "数据统计",
                    title_color: "#34aaff",
                    title_color2: "#888",
                    url: "/zh_cjdianc/pages/sjzx/sjtj/sjtj"
                }, {
                    logo: "/zh_cjdianc/img/tabmyf.png",
                    logo2: "/zh_cjdianc/img/tabmy.png",
                    title: "商家中心",
                    title_color: "#34aaff",
                    title_color2: "#888",
                    url: "/zh_cjdianc/pages/sjzx/sjzx/sjzx"
                } ], e(t), o.globalData.sjdnavbar = t;
            }
        });
    },
    convertHtmlToText: function(a) {
        var o = "" + a;
        return o = (o = o.replace(/<p.*?>/gi, "\r\n")).replace(/<\/p>/gi, "\r\n", "  *  ");
    }
}, "util", require("we7/resource/js/util.js")), _defineProperty(_App, "tabBar", {
    color: "#123",
    selectedColor: "#1ba9ba",
    borderStyle: "#1ba9ba",
    backgroundColor: "#fff",
    list: [ {
        pagePath: "/we7/pages/index/index",
        iconPath: "/we7/resource/icon/home.png",
        selectedIconPath: "/we7/resource/icon/homeselect.png",
        text: "首页"
    }, {
        pagePath: "/we7/pages/user/index/index",
        iconPath: "/we7/resource/icon/user.png",
        selectedIconPath: "/we7/resource/icon/userselect.png",
        text: "微擎我的"
    } ]
}), _defineProperty(_App, "globalData", {
    userInfo: null
}), _defineProperty(_App, "siteInfo", require("siteinfo.js")), _App));