var qqmapsdk, app = getApp(), QQMapWX = require("../../utils/qqmap-wx-jssdk.js");

Page({
    data: {
        address_list: null
    },
    onLoad: function(e) {
        app.setNavigationBarColor(this);
        var t = this;
        app.util.request({
            url: "entry/wxapp/System",
            cachetime: "0",
            success: function(e) {
                console.log(e), t.setData({
                    System: e.data
                }), qqmapsdk = new QQMapWX({
                    key: e.data.map_key
                });
            }
        });
    },
    onReady: function() {},
    onShow: function() {
        wx.showNavigationBarLoading();
        var a = this, e = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/MyAddress",
            cachetime: "0",
            data: {
                user_id: e
            },
            success: function(e) {
                console.log(e);
                for (var t = 0; t < e.data.length; t++) e.data[t].address = e.data[t].area.join("") + e.data[t].address;
                a.setData({
                    address_list: e.data
                });
            }
        });
    },
    bianji: function(e) {
        var t = e.currentTarget.dataset.bjid;
        console.log(t), wx.navigateTo({
            url: "bjdz?bjid=" + t,
            success: function(e) {},
            fail: function(e) {},
            complete: function(e) {}
        });
    },
    shanchu: function(e) {
        console.log(e.currentTarget.dataset.scid);
        var t = e.currentTarget.dataset.scid, a = this;
        wx.showModal({
            title: "提示",
            content: "确定要删除该地址吗？",
            confirmText: "确定",
            cancelText: "取消",
            success: function(e) {
                console.log(e), e.confirm ? (app.util.request({
                    url: "entry/wxapp/DelAdd",
                    cachetime: "0",
                    data: {
                        id: t
                    },
                    header: {
                        "content-type": "application/json"
                    },
                    success: function(e) {
                        console.log(e), "1" == e.data && (a.onShow(), wx.showToast({
                            title: "删除成功",
                            icon: "success"
                        }));
                    }
                }), console.log("用户点击确定")) : console.log("用户点击取消");
            }
        });
    },
    radioChange: function(e) {
        wx.getStorageSync("mydata").id;
        var a = this;
        console.log("radio发生change事件，携带value值为：", e.currentTarget.dataset.id);
        var t = e.currentTarget.dataset.id;
        app.util.request({
            url: "entry/wxapp/AddDefault",
            cachetime: "0",
            data: {
                id: t
            },
            success: function(e) {
                if (console.log(e), "1" == e.data) {
                    a.onShow(), wx.showToast({
                        title: "修改成功",
                        icon: "success",
                        duration: 1e3
                    });
                    var t = getCurrentPages();
                    if (console.log(t), 1 < t.length && "zh_cjdianc/pages/takeout/takeoutform" == t[t.length - 2].route) t[t.length - 2].countpsf();
                    setTimeout(function() {
                        wx.navigateBack({
                            delta: 1
                        });
                    }, 1e3);
                }
                if ("2" == e.data) {
                    t = getCurrentPages();
                    if (console.log(t), 1 < t.length && "zh_cjdianc/pages/takeout/takeoutform" == t[t.length - 2].route) t[t.length - 2].countpsf();
                    setTimeout(function() {
                        wx.navigateBack({
                            delta: 1
                        });
                    }, 1e3);
                }
            },
            fail: function(e) {},
            complete: function(e) {}
        });
    },
    getWechatAddress: function(e) {
        var n = wx.getStorageSync("users").id, s = this;
        wx.chooseAddress({
            success: function(o) {
                console.log(o), "chooseAddress:ok" == o.errMsg && (wx.showLoading(), qqmapsdk.geocoder({
                    address: o.provinceName + o.cityName + o.countyName + o.detailInfo,
                    success: function(e) {
                        if (console.log(e), "0" == e.status) {
                            var t = e.result.location.lat, a = e.result.location.lng;
                            app.util.request({
                                url: "entry/wxapp/AddAddress",
                                cachetime: "0",
                                data: {
                                    address: o.detailInfo,
                                    area: o.provinceName + "," + o.cityName + "," + o.countyName,
                                    user_name: o.userName,
                                    user_id: n,
                                    tel: o.telNumber,
                                    lat: t,
                                    lng: a
                                },
                                success: function(e) {
                                    if (console.log(e.data), "1" == e.data) {
                                        wx.showToast({
                                            title: "保存成功",
                                            duration: 1e3
                                        });
                                        var t = getCurrentPages();
                                        if (console.log(t), 1 < t.length && "zh_cjdianc/pages/takeout/takeoutform" == t[t.length - 2].route) t[t.length - 2].countpsf();
                                        setTimeout(function() {
                                            wx.navigateBack({
                                                delta: 1
                                            });
                                        }, 1e3), s.onShow();
                                    }
                                }
                            });
                        }
                    },
                    fail: function(e) {
                        console.log(e);
                    },
                    complete: function(e) {
                        console.log(e);
                    }
                }));
            },
            fail: function() {
                wx.getSetting({
                    success: function(e) {
                        console.log(e), e.authSetting["scope.address"] ? console.log("取消") : wx.showModal({
                            title: "提示",
                            content: "您拒绝了获取收货地址授权，部分功能无法使用,点击确定重新获取授权。",
                            showCancel: !1,
                            success: function(e) {
                                e.confirm && wx.openSetting({
                                    success: function(e) {
                                        e.authSetting["scope.address"] && s.getWechatAddress();
                                    },
                                    fail: function(e) {}
                                });
                            }
                        });
                    }
                });
            }
        });
    }
});