var a = getApp(), util = require("../../utils/util.js");

Page({
    data: {
        carte: [ {
            img1: "../../img/personal/kefu.png",
            name: "客服与投诉",
            img2: "",
            margin: "margin_top",
            border: "border_bottom",
            bindtap: "customer"
        }, {
            img1: "../../img/personal/bangzhu.png",
            name: "帮助中心",
            img2: "",
            border: "border_bottom",
            bindtap: "help"
        } ],
        top: "-420"
    },
    wdsc: function() {
        wx.navigateTo({
            url: "../extra/wdsc"
        });
    },
    wddd: function() {
        wx.navigateTo({
            url: "../wddd/order"
        });
    },
    wddz: function() {
        wx.navigateTo({
            url: "../wddz/xzdz"
        });
    },
    wdyy: function() {
        wx.navigateTo({
            url: "../reserve/order"
        });
    },
    wdqg: function() {
        wx.navigateTo({
            url: "../xsqg/order"
        });
    },
    wdpt: function() {
        wx.navigateTo({
            url: "../collage/order"
        });
    },
    wdyhq: function() {
        wx.navigateTo({
            url: "myyhq"
        });
    },
    help: function() {
        wx.navigateTo({
            url: "bzzx"
        });
    },
    seller: function() {
        var t = wx.getStorageSync("users").id;
        a.util.request({
            url: "entry/wxapp/CheckRz",
            cachetime: "0",
            data: {
                user_id: t
            },
            success: function(t) {
                console.log(t.data), 0 != t.data ? 1 == t.data.state ? wx.showModal({
                    title: "",
                    content: "系统正在审核中"
                }) : 2 == t.data.state ? (wx.setStorageSync("sjdsjid", t.data.id), wx.navigateTo({
                    url: "../sjzx/wmdd/wmdd"
                })) : 3 == t.data.state ? wx.showModal({
                    title: "",
                    content: "您的入驻申请已被拒绝，点击确定进行编辑",
                    success: function(t) {
                        t.confirm && wx.navigateTo({
                            url: "../ruzhu/index?state=3"
                        });
                    }
                }) : wx.showModal({
                    title: "",
                    content: "您的入驻已经到期,请联系平台管理员续费"
                }) : wx.navigateTo({
                    url: "../sjzx/login"
                });
            }
        });
    },
    fx: function() {
        wx.navigateTo({
            url: "../distribution/index"
        });
    },
    jfsc: function() {
        wx.navigateTo({
            url: "../integral/integral"
        });
    },
    czzx: function() {
        wx.navigateTo({
            url: "../wallet/walletadd"
        });
    },
    tzhy: function() {
        wx.navigateTo({
            url: "../hyk/hyk"
        });
    },
    bindGetUserInfo: function(t) {
        console.log(t), "getUserInfo:ok" == t.detail.errMsg && (this.setData({
            hydl: !1
        }), this.changeData());
    },
    changeData: function() {
        var n = this;
        wx.getSetting({
            success: function(t) {
                console.log(t), t.authSetting["scope.userInfo"] ? wx.getUserInfo({
                    success: function(t) {
                        console.log(t), a.util.request({
                            url: "entry/wxapp/login",
                            cachetime: "0",
                            data: {
                                openid: getApp().getOpenId,
                                img: t.userInfo.avatarUrl,
                                name: t.userInfo.nickName
                            },
                            header: {
                                "content-type": "application/json"
                            },
                            dataType: "json",
                            success: function(t) {
                                console.log("用户信息", t);
                            }
                        });
                        var e = t.userInfo;
                        console.log(e), n.setData({
                            avatarUrl: e.avatarUrl,
                            nickName: e.nickName
                        });
                    }
                }) : (console.log("未授权过"), n.setData({
                    hydl: !0
                }));
            }
        });
    },
    jumps: function(t) {
        var e = t.currentTarget.dataset.id, a = t.currentTarget.dataset.name, n = t.currentTarget.dataset.appid, o = t.currentTarget.dataset.src, s = t.currentTarget.dataset.wb_src, i = t.currentTarget.dataset.type;
        console.log(e, a, n, o, s, i), 1 == i ? (console.log(o), wx.navigateTo({
            url: o
        })) : 2 == i ? (wx.setStorageSync("vr", s), wx.navigateTo({
            url: "../car/car"
        })) : 3 == i && wx.navigateToMiniProgram({
            appId: n
        });
    },
    onLoad: function(t) {
        a.setNavigationBarColor(this), a.pageOnLoad(this), this.changeData();
        var o = this, e = wx.getStorageSync("users").id;
        console.log(e), a.util.request({
            url: "entry/wxapp/MyCoupons",
            cachetime: "0",
            data: {
                user_id: e
            },
            success: function(t) {
                console.log(t.data), o.setData({
                    yhnum: t.data.length
                });
            }
        }), a.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(t) {
                console.log(t), o.setData({
                    system: t.data
                });
            }
        }), a.util.request({
            url: "entry/wxapp/CheckRetail",
            cachetime: "0",
            success: function(t) {
                console.log(t), o.setData({
                    fxset: t.data
                });
            }
        }), a.util.request({
            url: "entry/wxapp/Signset",
            cachetime: "0",
            success: function(t) {
                console.log("签到设置", t), o.setData({
                    qdset: t.data
                });
            }
        }), a.util.request({
            url: "entry/wxapp/ad",
            cachetime: "0",
            success: function(t) {
                console.log(t);
                for (var e = [], a = 0; a < t.data.length; a++) "7" == t.data[a].type && e.push(t.data[a]);
                console.log(e), o.setData({
                    lblist: e
                });
            }
        }), a.util.request({
            url: "entry/wxapp/Llz",
            cachetime: "0",
            data: {
                type: "3,4"
            },
            success: function(t) {
                console.log(t);
                for (var e = [], a = [], n = 0; n < t.data.length; n++) 3 == t.data[n].type && e.push(t.data[n]), 
                4 == t.data[n].type && a.push(t.data[n]);
                o.setData({
                    dbllz: e,
                    zbllz: a
                });
            }
        }), wx.getSystemInfo({
            success: function(t) {
                console.log(t.model), console.log(t.pixelRatio), console.log(t.windowWidth), console.log(t.windowHeight), 
                console.log(t.language), console.log(t.version), console.log(t.platform), "android" != t.platform && o.setData({
                    top: "-330"
                });
            }
        });
    },
    feedback: function(t) {
        wx.navigateTo({
            url: "feedback"
        });
    },
    wallet: function(t) {
        wx.navigateTo({
            url: "../wallet/wallet"
        });
    },
    set_up: function(t) {
        wx.navigateTo({
            url: "set_up"
        });
    },
    receive: function(t) {
        wx.navigateTo({
            url: "receive"
        });
    },
    integral: function(t) {
        wx.navigateTo({
            url: "../integral/myintegral"
        });
    },
    sign_in: function(t) {
        wx.navigateTo({
            url: "rankings"
        });
    },
    sjrz: function(t) {
        var e = wx.getStorageSync("users").id;
        a.util.request({
            url: "entry/wxapp/CheckRz",
            cachetime: "0",
            data: {
                user_id: e
            },
            success: function(t) {
                console.log(t.data), 0 != t.data ? 1 == t.data.state ? wx.showModal({
                    title: "",
                    content: "系统正在审核中"
                }) : 2 == t.data.state ? wx.showModal({
                    title: "",
                    content: "您已经入驻过了"
                }) : 3 == t.data.state ? wx.showModal({
                    title: "",
                    content: "您的入驻申请已被拒绝，点击确定进行编辑",
                    success: function(t) {
                        t.confirm && wx.navigateTo({
                            url: "../ruzhu/index?state=3"
                        });
                    }
                }) : wx.showModal({
                    title: "",
                    content: "您的入驻已经到期,请联系平台管理员续费"
                }) : wx.navigateTo({
                    url: "../ruzhu/index"
                });
            }
        });
    },
    onReady: function() {},
    onShow: function() {
        var e = this, t = wx.getStorageSync("users").id, n = util.formatTime(new Date()).substring(0, 10).replace(/\//g, "-");
        console.log(n.toString()), a.util.request({
            url: "entry/wxapp/UserInfo",
            cachetime: "0",
            data: {
                user_id: t
            },
            success: function(t) {
                console.log(t), "" != t.data.dq_time && t.data.dq_time >= n.toString() && (t.data.ishy = 1), 
                e.setData({
                    userInfo: t.data
                });
            }
        });
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});