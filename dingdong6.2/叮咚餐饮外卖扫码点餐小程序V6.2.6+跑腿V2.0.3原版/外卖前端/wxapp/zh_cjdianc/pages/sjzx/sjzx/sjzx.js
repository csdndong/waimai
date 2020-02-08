/*   time:2019-07-18 01:03:18*/
var app = getApp();
Page({
    data: {
        yykgtext: "语音播报已关闭"
    },
    cartaddformSubmit: function(t) {
        console.log("formid", t.detail.formId);
        var a = this,
            e = this.data.storeinfo.store.admin_id;
        app.util.request({
            url: "entry/wxapp/AddFormId",
            cachetime: "0",
            data: {
                user_id: e,
                form_id: t.detail.formId
            },
            success: function(t) {
                console.log(t.data), a.reLoad()
            }
        })
    },
    tcdl: function() {
        wx.removeStorageSync("sjdsjid"), wx.reLaunch({
            url: "/zh_cjdianc/pages/my/index"
        })
    },
    jdswitchChange: function(a) {
        var e = this,
            t = wx.getStorageSync("sjdsjid");
        if (console.log(t), console.log("jdswitchChange 发生 change 事件，携带值为", a.detail.value), a.detail.value) var o = 1;
        else o = 2;
        console.log(o), wx.showLoading({
            title: "提交中",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/UpStore",
            cachetime: "0",
            data: {
                store_id: t,
                is_jd: o
            },
            success: function(t) {
                console.log(t.data), "1" == t.data ? (wx.showToast({
                    title: "设置成功",
                    icon: "success",
                    duration: 1e3
                }), a.detail.value ? e.setData({
                    jdkgtext: "自动接单已开启"
                }) : e.setData({
                    jdkgtext: "自动接单已关闭"
                })) : "2" == t.data ? wx.showToast({
                    title: "请修改后提交",
                    icon: "loading",
                    duration: 1e3
                }) : wx.showToast({
                    title: "请重试",
                    icon: "loading",
                    duration: 1e3
                })
            }
        })
    },
    yyswitchChange: function(t) {
        console.log("switch2 发生 change 事件，携带值为", t.detail.value), t.detail.value ? (wx.setStorageSync("yybb", !0), this.setData({
            yykg: !0,
            yykgtext: "语音播报已开启"
        })) : (wx.removeStorageSync("yybb"), this.setData({
            yykg: !1,
            yykgtext: "语音播报已关闭"
        }))
    },
    onLoad: function(t) {
        var a = this,
            e = wx.getStorageSync("sjdsjid"),
            o = wx.getStorageSync("users").id;
        console.log(e, o), app.setNavigationBarColor(this), app.sjdpageOnLoad(this), app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: e
            },
            success: function(t) {
                console.log("商家详情", t), a.setData({
                    storeinfo: t.data,
                    user_id: o
                }), a.reLoad(), "1" == t.data.storeset.is_jd && a.setData({
                    jdkg: !0,
                    jdkgtext: "自动接单已开启"
                }), "2" == t.data.storeset.is_jd && a.setData({
                    jdkg: !1,
                    jdkgtext: "自动接单已关闭"
                })
            }
        }), wx.getStorageSync("yybb") && a.setData({
            yykg: !0,
            yykgtext: "语音播报已开启"
        })
    },
    reLoad: function() {
        var a = this,
            t = this.data.storeinfo.store.admin_id;
        app.util.request({
            url: "entry/wxapp/MyFormId",
            cachetime: "0",
            data: {
                admin_id: t
            },
            success: function(t) {
                console.log(t.data), a.setData({
                    sycs: t.data
                })
            }
        })
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {}
});