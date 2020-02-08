var app = getApp(), siteinfo = require("../../../siteinfo.js");

Page({
    data: {
        getmsg: "获取验证码",
        color: "#459cf9",
        succ: !1,
        close: !1,
        codes: "",
        name: 0,
        imgUrls: [ "http://img02.tooopen.com/images/20150928/tooopen_sy_143912755726.jpg", "http://img06.tooopen.com/images/20160818/tooopen_sy_175866434296.jpg", "http://img06.tooopen.com/images/20160818/tooopen_sy_175833047715.jpg" ],
        indicatorDots: !1,
        autoplay: !1,
        interval: 5e3,
        duration: 1e3
    },
    onLoad: function(e) {
        var o = this;
        wx.hideShareMenu(), app.getUserInfo(function(e) {
            o.setData({
                userInfo: e
            });
        }), app.getSystem(function(e) {
            console.log(e), o.setData({
                getSystem: e,
                color: e.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: e.color
            });
        }), app.util.request({
            url: "entry/wxapp/Attachurl",
            success: function(e) {
                console.log(e), o.setData({
                    url: e.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/ad",
            success: function(e) {
                console.log(e), o.setData({
                    list: e.data
                });
            }
        });
    },
    xieyi: function(e) {
        wx.navigateTo({
            url: "rz"
        });
    },
    choose: function(e) {
        var t = this, a = siteinfo.siteroot, n = siteinfo.uniacid, s = e.currentTarget.dataset.type;
        console.log(s);
        wx.chooseImage({
            count: 1,
            sizeType: [ "original", "compressed" ],
            sourceType: [ "album", "camera" ],
            success: function(e) {
                console.log(e);
                var o = e.tempFilePaths[0];
                wx.uploadFile({
                    url: a + "?i=" + n + "&c=entry&a=wxapp&do=upload&m=zh_cjpt",
                    filePath: o,
                    name: "upfile",
                    formData: {},
                    success: function(e) {
                        console.log("这是上传成功"), console.log(e), console.log(o), 1 == s ? t.setData({
                            upload_one: o,
                            logo: e.data
                        }) : 2 == s ? t.setData({
                            upload_two: o,
                            img_0: e.data
                        }) : 3 == s && t.setData({
                            upload_three: o,
                            img_1: e.data
                        });
                    },
                    fail: function(e) {
                        console.log("这是上传失败"), console.log(e);
                    }
                });
            }
        });
    },
    user_tel: function(e) {
        var o = this;
        11 == e.detail.value.length && (1 == app.isTelCode(e.detail.value) ? o.setData({
            phone: e.detail.value
        }) : (o.setData({
            title: "请检查您输入的手机号是否有误",
            close: !0
        }), setTimeout(function() {
            o.setData({
                close: !1
            });
        }, 3e3)));
    },
    sendmessg: function(e) {
        var o = this, t = o.data.phone;
        if ("" == t || null == t) wx.showToast({
            title: "请输入手机号",
            icon: "",
            image: "",
            duration: 2e3,
            mask: !0,
            success: function(e) {},
            fail: function(e) {},
            complete: function(e) {}
        }); else {
            for (var a = "", n = 0; n < 6; n++) a += Math.floor(10 * Math.random());
            console.log(a), app.util.request({
                url: "entry/wxapp/Sms2",
                cachetime: "0",
                data: {
                    code: a,
                    tel: t,
                    type: 1
                },
                success: function(e) {
                    console.log(e);
                }
            }), o.setData({
                codes: a
            });
            var s = 59, i = setInterval(function() {
                o.setData({
                    getmsg: s + "s后重新发送",
                    send: !0
                }), --s <= 0 && (clearInterval(i), o.setData({
                    getmsg: "获取验证码",
                    send: !1,
                    num: 0
                }));
            }, 1e3);
        }
    },
    P_message: function(e) {
        var o = this;
        o.setData({
            title: e,
            close: !0
        }), setTimeout(function() {
            o.setData({
                close: !1
            });
        }, 3e3);
    },
    selse_succ: function(e) {
        var o = this;
        0 == o.data.succ ? o.setData({
            succ: !0
        }) : o.setData({
            succ: !1
        });
    },
    getPhoneNumber: function(e) {
        var o = this, t = o.data;
        console.log(app.getSK), console.log(e), app.util.request({
            url: "entry/wxapp/jiemi",
            cachetime: "0",
            data: {
                sessionKey: t.userInfo.session_key,
                iv: e.detail.iv,
                data: e.detail.encryptedData
            },
            success: function(e) {
                console.log("这是解密手机号"), console.log(e), o.setData({
                    phone: e.data.phoneNumber
                });
            }
        });
    },
    formSubmit: function(e) {
        var o = this.data, t = o.getSystem, a = e.detail.value, n = a.name;
        if (1 == t.is_dxyz) var s = a.tel; else s = o.phone;
        var i = a.email;
        console.log(i);
        var l = a.password, c = a.confirm_pw, u = a.code, r = o.logo, p = o.img_0, d = o.img_1, f = o.succ, g = o.userInfo.openid, m = o.codes, h = "";
        "" == n ? h = "请输入您的真实名字" : "" == s && 1 == t.is_dxyz ? h = "请输入您的真实手机号" : null == s && 2 == t.is_dxyz ? h = "请输入您的真实手机号" : "" == i ? h = "请输入您的邮箱账号" : /^\w+@[a-zA-Z0-9]{2,10}(?:\.[a-z]{2,4}){1,3}$/.test(i) ? "" == u && 1 == t.is_dxyz ? h = "请输入验证码" : "" == l ? h = "请输入登录密码" : "" == c ? h = "请再次输入登录密码" : u != m && 1 == t.is_dxyz ? h = "验证码输入错误" : l != c ? h = "登录密码输入不一致" : 0 == f && (h = "请阅读并同意入驻申请协议") : h = "请输入正确的邮箱", 
        "" != h ? this.P_message(h) : app.util.request({
            url: "entry/wxapp/SaveRider",
            data: {
                openid: g,
                name: n,
                tel: s,
                logo: r,
                pwd: l,
                zm_img: p,
                fm_img: d,
                email: i
            },
            success: function(e) {
                console.log(e), "" == e.data ? app.succ_t("注册成功", !1) : wx.showModal({
                    title: "",
                    content: e.data
                });
            },
            fail: function(e) {
                console.log(e);
            }
        });
    },
    names: function(e) {
        var o = this;
        setInterval(function() {
            var e = o.data.name;
            o.setData({
                name: e + 1
            }), o.one();
        });
    },
    one: function(e) {
        var o = this.data.name;
        console.log(o), app.util.request({
            url: "entry/wxapp/SaveRider",
            data: {
                openid: "123",
                name: o,
                tel: o + "123",
                logo: "这是头像",
                pwd: "这是密码",
                zm_img: "这是身份证正面",
                fm_img: "这是身份证反面"
            },
            success: function(e) {
                console.log(e);
            },
            fail: function(e) {
                console.log(e);
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});