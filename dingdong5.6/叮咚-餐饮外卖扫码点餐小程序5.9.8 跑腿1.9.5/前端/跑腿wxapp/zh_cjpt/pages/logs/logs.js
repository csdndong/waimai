var util = require("../../utils/util.js"), app = getApp();

Page({
    data: {
        logs: [],
        imgUrls: [ "http://img02.tooopen.com/images/20150928/tooopen_sy_143912755726.jpg", "http://img06.tooopen.com/images/20160818/tooopen_sy_175866434296.jpg", "http://img06.tooopen.com/images/20160818/tooopen_sy_175833047715.jpg" ],
        indicatorDots: !1,
        autoplay: !1,
        interval: 5e3,
        duration: 1e3
    },
    onLoad: function() {
        var o = this;
        wx.hideShareMenu();
        var t = app.bottom_menu("/zh_cjpt/pages/logs/logs");
        app.util.request({
            url: "entry/wxapp/Url",
            success: function(t) {
                console.log(t), o.setData({
                    url: t.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/GetNotice",
            success: function(t) {
                console.log("公告列表为", t), o.setData({
                    GetNotice: t.data
                });
            }
        }), o.setData({
            menu: t,
            qs: wx.getStorageSync("qs")
        }), app.getSystem(function(t) {
            console.log(t), o.setData({
                getSystem: t,
                color: t.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: t.color
            });
        }), o.setData({
            logs: (wx.getStorageSync("logs") || []).map(function(t) {
                return util.formatTime(new Date(t));
            })
        });
    },
    route_page: function(t) {
        wx.reLaunch({
            url: t.currentTarget.dataset.url
        });
    },
    abnormal: function(t) {
        wx.navigateTo({
            url: "abnormal"
        });
    },
    tj: function(t) {
        wx.navigateTo({
            url: "tab"
        });
    },
    xx: function(t) {
        wx.navigateTo({
            url: "xx"
        });
    },
    zj: function(t) {
        wx.navigateTo({
            url: "zj"
        });
    },
    help: function(t) {
        wx.navigateTo({
            url: "capital"
        });
    },
    bill: function(t) {
        wx.navigateTo({
            url: "bill?id=" + t.currentTarget.dataset.id
        });
    },
    platform: function(t) {
        wx.navigateTo({
            url: "reward"
        });
    },
    custom: function(t) {
        wx.navigateTo({
            url: "xx"
        });
    },
    remove: function(t) {
        wx.showModal({
            title: "",
            content: "是否退出当前账号",
            success: function(t) {
                t.confirm && (wx.clearStorage(), wx.reLaunch({
                    url: "../mine/zhuce"
                }));
            }
        });
    },
    sz: function(t) {
        var o = wx.getStorageSync("qs").id, e = t.currentTarget.dataset.id;
        wx.getStorageSync("qs").status == e ? 1 == e ? app.succ_m("当前正处于上班状态") : app.succ_m("当前正处于休息状态") : wx.showModal({
            title: "温馨提示",
            content: "是否切换当前状态",
            success: function(t) {
                t.confirm && app.util.request({
                    url: "entry/wxapp/Work",
                    data: {
                        qs_id: o,
                        status: e
                    },
                    success: function(t) {
                        console.log(t), app.succ_t("设置成功", !0), setTimeout(function() {
                            wx.reLaunch({
                                url: "../mine/zhuce"
                            });
                        }, 1500);
                    }
                });
            }
        });
    }
});