var app = getApp();

Page({
    data: {},
    onLoad: function(e) {
        var t = this;
        app.getUrl(t), wx.hideShareMenu(), app.getSystem(function(e) {
            console.log(e), t.setData({
                getSystem: e,
                color: e.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: e.color
            });
        }), app.getUserInfo(function(e) {
            console.log(e), t.setData({
                userInfo: e
            }), console.log(wx.getStorageSync("user_info")), "" != wx.getStorageSync("user_info") ? (wx.showLoading({
                title: "正在检测登录状态"
            }), app.util.request({
                url: "entry/wxapp/login",
                cachetime: "0",
                data: {
                    tel: wx.getStorageSync("user_info").name,
                    pwd: wx.getStorageSync("user_info").pwd,
                    openid: e.openid
                },
                success: function(e) {
                    console.log(e), 0 == e.data ? (wx.showModal({
                        title: "",
                        content: "该账号密码已修改，请重新登录"
                    }), t.setData({
                        sign: !1
                    })) : "该账号不存在" == e.data ? t.setData({
                        sign: !1
                    }) : "账号异常,请联系管理员" == e.data ? (wx.showModal({
                        title: "",
                        content: "系统正在审核中，请稍后再试"
                    }), t.setData({
                        sign: !1
                    })) : "" != e.data.name && (console.log("可以进行正常登录"), wx.hideLoading(), wx.setStorageSync("qs", e.data), 
                    wx.reLaunch({
                        url: "../index/index"
                    }));
                }
            })) : (console.log("还没进行过登录"), t.setData({
                sign: !1
            }));
        });
    },
    zhuce: function(e) {
        wx.navigateTo({
            url: "index"
        });
    },
    formSubmit: function(e) {
        var o = this;
        console.log(e);
        e.detail.formId;
        var t = e.detail.value, a = t.name, n = t.tel;
        "" == a ? app.succ_t("账号不能为空", !0) : "" == n ? app.succ_t("密码不能为空", !0) : app.util.request({
            url: "entry/wxapp/login",
            cachetime: "0",
            data: {
                tel: a,
                pwd: n,
                openid: o.data.userInfo.openid
            },
            success: function(e) {
                if (console.log(e), 0 == e.data) app.succ_m("账号或者密码错误"); else if ("该账号不存在" == e.data) wx.showModal({
                    title: "",
                    content: "请先注册账号"
                }); else if ("账号异常,请联系管理员" == e.data) wx.showModal({
                    title: "",
                    content: "系统正在审核中，请稍后再试"
                }), o.setData({
                    sign: !1
                }); else if ("" != e.data.name) {
                    console.log("可以进行正常登录");
                    var t = {
                        name: a,
                        pwd: n
                    };
                    wx.setStorageSync("user_info", t), wx.setStorageSync("qs", e.data), wx.reLaunch({
                        url: "../index/index"
                    });
                }
            }
        });
    },
    wx_login: function(e) {
        app.util.request({
            url: "entry/wxapp/WxLogin",
            cachetime: "0",
            data: {
                openid: this.data.userInfo.openid
            },
            success: function(e) {
                console.log(e), 0 == e.data ? app.succ_m("暂无绑定微信账号") : 1 == e.data.state ? app.succ_m("当前账号正在审核中") : 2 == e.data.state ? (wx.setStorageSync("qs", e.data), 
                wx.reLaunch({
                    url: "../index/index"
                })) : app.succ_m("您的入驻申请已被拒绝");
            }
        });
    },
    uppaword: function(e) {
        wx.navigateTo({
            url: "uppaword"
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});