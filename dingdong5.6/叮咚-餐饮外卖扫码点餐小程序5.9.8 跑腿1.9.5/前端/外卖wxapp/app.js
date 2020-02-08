var _App;

function _defineProperty(o, a, e) {
    return a in o ? Object.defineProperty(o, a, {
        value: e,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : o[a] = e, o;
}

App((_defineProperty(_App = {
    onLaunch: function() {},
    onShow: function() {
        console.log(getCurrentPages());
    },
    onHide: function() {
        console.log(getCurrentPages());
    },
    onError: function(o) {
        console.log(o);
    },
    util: require("we7/resource/js/util.js"),
    getimgUrl: function(a) {
        var o = this.globalData.imgurl;
        console.log(o, a), a.setData({
            url: o
        });
        var e = this;
        o || e.util.request({
            url: "entry/wxapp/Url",
            success: function(o) {
                console.log(o), e.globalData.imgurl = o.data, e.getimgUrl(a);
            }
        });
    },
    setNavigationBarColor: function(a) {
        var o = this.globalData.color, e = this.globalData.imgurl;
        console.log(o, e, a), o && wx.setNavigationBarColor({
            frontColor: "#ffffff",
            backgroundColor: o
        }), a.setData({
            color: o,
            url: e
        });
        var t = this;
        o || t.util.request({
            url: "entry/wxapp/system",
            success: function(o) {
                console.log(o), getApp().xtxx = o.data, t.globalData.imgurl = o.data.attachurl, 
                t.globalData.color = o.data.color, t.setNavigationBarColor(a);
            }
        });
    },
    pageOnLoad: function(n) {
        var t = this;
        function l(o) {
            console.log(o);
            var a = !1, e = n.route || n.__route__ || null;
            for (var t in o.navs) o.navs[t].url === "/" + e ? a = o.navs[t].active = !0 : o.navs[t].active = !1;
            a && n.setData({
                _navbar: o
            });
        }
        console.log("----setPageNavbar----"), console.log(n);
        var i = {
            background_image: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEX///+nxBvIAAAACklEQVQI12NgAAAAAgAB4iG8MwAAAABJRU5ErkJggg==",
            border_color: "rgba(0,0,0,.1)"
        }, o = t.globalData.navbar;
        console.log(o), o && l(o), o || t.util.request({
            url: "entry/wxapp/nav",
            success: function(o) {
                var a = getApp().xtxx1;
                if (console.log(o, a), 0 == o.data.length) {
                    if ("1" == a.model) var e = [ {
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
                    if ("2" == a.model) e = [ {
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
                    if ("4" == a.model) e = [ {
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
                    i.navs = e, l(i), t.globalData.navbar = i;
                } else i.navs = o.data, l(i), t.globalData.navbar = i;
            }
        });
    },
    title: function(o) {
        if ("" == o) return !0;
        wx.showModal({
            title: "",
            content: o
        });
    },
    getUserInfo: function(a) {
        var e = this, o = this.globalData.userInfo;
        console.log(o), o ? "function" == typeof a && a(o) : wx.login({
            success: function(o) {
                wx.showLoading({
                    title: "正在登录",
                    mask: !0
                }), console.log(o.code), e.util.request({
                    url: "entry/wxapp/Openid",
                    cachetime: "0",
                    data: {
                        code: o.code
                    },
                    header: {
                        "content-type": "application/json"
                    },
                    dataType: "json",
                    success: function(o) {
                        console.log("openid信息", o.data), getApp().getOpenId = o.data.openid, getApp().getSK = o.data.session_key, 
                        e.util.request({
                            url: "entry/wxapp/login",
                            cachetime: "0",
                            data: {
                                openid: o.data.openid
                            },
                            header: {
                                "content-type": "application/json"
                            },
                            dataType: "json",
                            success: function(o) {
                                console.log("用户信息", o), getApp().getuniacid = o.data.uniacid, wx.setStorageSync("users", o.data), 
                                e.globalData.userInfo = o.data, "function" == typeof a && a(e.globalData.userInfo);
                            }
                        });
                    },
                    fail: function(o) {},
                    complete: function(o) {}
                });
            }
        });
    },
    sjdpageOnLoad: function(n) {
        var a = this;
        function e(o) {
            console.log(o);
            var a = !1, e = n.route || n.__route__ || null;
            for (var t in o.navs) o.navs[t].url === "/" + e ? a = o.navs[t].active = !0 : o.navs[t].active = !1;
            a && n.setData({
                _navbar: o
            });
        }
        console.log("----setPageNavbar----"), console.log(n);
        var t = {
            background_image: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEX///+nxBvIAAAACklEQVQI12NgAAAAAgAB4iG8MwAAAABJRU5ErkJggg==",
            border_color: "rgba(0,0,0,.1)"
        }, o = a.globalData.sjdnavbar;
        console.log(o), o && e(o), o || a.util.request({
            url: "entry/wxapp/nav",
            success: function(o) {
                console.log(o);
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
                } ], e(t), a.globalData.sjdnavbar = t;
            }
        });
    },
    convertHtmlToText: function(o) {
        var a = "" + o;
        return a = (a = a.replace(/<p.*?>/gi, "\r\n")).replace(/<\/p>/gi, "\r\n", "  *  ");
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