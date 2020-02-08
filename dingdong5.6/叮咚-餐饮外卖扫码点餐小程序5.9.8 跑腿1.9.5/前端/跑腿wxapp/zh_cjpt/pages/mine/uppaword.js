var app = getApp();

Page({
    data: {
        getmsg: "发送验证码",
        code: "215421521521"
    },
    onLoad: function(e) {
        var t = this;
        wx.hideShareMenu(), app.getSystem(function(e) {
            console.log(e), t.setData({
                getSystem: e,
                color: e.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: e.color
            });
        });
    },
    user_tel: function(e) {
        var t = this;
        if (11 == e.detail.value.length) {
            var o = app.isTelCode(e.detail.value);
            console.log(o), 1 == o ? t.setData({
                phone: e.detail.value
            }) : t.setData({
                title: "请检查您输入的手机号是否有误"
            });
        } else t.setData({
            title: "请检查您输入的手机号是否有误",
            close: !0
        });
    },
    codes: function(e) {
        var t = this, o = t.data.code;
        console.log(o);
        var a = e.detail.value;
        console.log(a), "" != a && 6 == a.length && (a == o ? (console.log("验证码输入正确"), t.setData({
            close: !1
        })) : (console.log("验证码输入错误"), wx.showModal({
            title: "",
            content: "验证码输入错误"
        }), t.setData({
            close: !0
        })));
    },
    sendmessg: function(e) {
        var t = this, o = t.data.phone;
        if ("" == o || null == o) wx.showToast({
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
                    tel: o,
                    type: 2
                },
                success: function(e) {
                    console.log(e);
                }
            }), t.setData({
                code: a
            });
            var s = 59, c = setInterval(function() {
                t.setData({
                    getmsg: "重新发送(" + s + "s)",
                    send: !0
                }), --s <= 0 && (clearInterval(c), t.setData({
                    getmsg: "发送验证码",
                    send: !1,
                    num: 0
                }));
            }, 1e3);
        }
    },
    pwd: function(e) {
        "" != e.detail.value && (e.detail.value.length <= 8 ? this.setData({
            pwd: e.detail.value
        }) : wx.showModal({
            title: "",
            content: "密码最多设置8位"
        }));
    },
    formSubmit: function(e) {
        console.log(e);
        var t = this.data, o = e.detail.value, a = o.new_pwd, n = o.pwd, s = o.phone, c = o.code;
        this.setData({
            phone: s
        }), "" == s ? app.succ_m("请输入您的手机号") : "" == n ? app.succ_m("请输入您的新密码") : "" == a ? app.succ_m("请再次输入您的密码") : n != a ? app.succ_m("两次密码输入不一致") : "" == c ? app.succ_m("请输入验证码") : c != t.code ? app.succ_m("验证码错误") : app.util.request({
            url: "entry/wxapp/UpdPwd",
            cachetime: "0",
            data: {
                tel: s,
                pwd: n
            },
            success: function(e) {
                console.log(e), 1 == e.data ? app.succ_t("修改成功", !1) : app.succ_t("修改失败", !0);
            }
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