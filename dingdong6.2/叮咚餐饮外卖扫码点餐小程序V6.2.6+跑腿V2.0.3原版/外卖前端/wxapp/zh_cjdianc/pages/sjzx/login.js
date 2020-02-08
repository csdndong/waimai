/*   time:2019-07-18 01:03:18*/
var app = getApp();
Page({
    data: {
        disabled: !0,
        zh: "",
        mm: "",
        logintext: "登录",
        werchat: !1
    },
    tel: function() {
        wx.makePhoneCall({
            phoneNumber: this.data.xtxx.tel
        })
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this);
        var o = this;
        console.log(this), app.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(t) {
                console.log(t.data), o.setData({
                    xtxx: t.data
                })
            }
        })
    },
    name: function(t) {
        console.log(t), this.setData({
            name: t.detail.value
        })
    },
    password: function(t) {
        console.log(t), this.setData({
            password: t.detail.value
        })
    },
    sign: function(t) {
        console.log(this.data), wx.showLoading({
            title: "正在提交",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/StoreLogin",
            cachetime: "0",
            data: {
                user: this.data.name,
                password: this.data.password
            },
            success: function(t) {
                console.log(t), null != t.data.storeid ? (wx.setStorageSync("sjdsjid", t.data.storeid), wx.redirectTo({
                    url: "wmdd/wmdd"
                })) : wx.showModal({
                    title: "提示",
                    content: t.data
                })
            }
        })
    },
    weixin: function(t) {
        var o = wx.getStorageSync("users").id;
        console.log(o), wx.showModal({
            title: "提示",
            content: "确定使用此微信号绑定的操作员身份登录吗？",
            success: function(t) {
                t.confirm ? (console.log("用户点击确定"), app.util.request({
                    url: "entry/wxapp/StoreWxLogin",
                    cachetime: "0",
                    data: {
                        user_id: o
                    },
                    success: function(t) {
                        console.log(t), null != t.data.id ? (wx.setStorageSync("sjdsjid", t.data.id), wx.redirectTo({
                            url: "wmdd/wmdd"
                        })) : wx.showModal({
                            title: "提示",
                            content: t.data
                        })
                    }
                })) : t.cancel && console.log("用户点击取消")
            }
        })
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});