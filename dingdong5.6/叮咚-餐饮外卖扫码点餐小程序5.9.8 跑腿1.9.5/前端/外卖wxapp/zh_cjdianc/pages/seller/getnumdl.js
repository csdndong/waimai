var app = getApp(), util = require("../../utils/util.js");

Page({
    data: {},
    onLoad: function(t) {
        console.log(t), wx.setNavigationBarTitle({
            title: "排队详情"
        });
        var e = this;
        e.setData({
            id: t.id
        }), app.setNavigationBarColor(this), app.getimgUrl(this), app.getUserInfo(function(t) {
            console.log(t);
        }), app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: t.storeid
            },
            success: function(t) {
                console.log(t.data), e.setData({
                    storeinfo: t.data.store
                });
            }
        }), this.reLoad();
    },
    reLoad: function() {
        var e = this;
        app.util.request({
            url: "entry/wxapp/NumberDetails",
            cachetime: "0",
            data: {
                num_id: e.data.id
            },
            success: function(t) {
                console.log(t.data), e.setData({
                    NumberDetail: t.data
                });
            }
        });
    },
    seller_info: function(t) {
        var e = this.data.storeinfo.coordinates.split(","), o = this.data.storeinfo;
        console.log(e), wx.openLocation({
            latitude: parseFloat(e[0]),
            longitude: parseFloat(e[1]),
            address: o.address,
            name: o.name
        });
    },
    maketel: function() {
        wx.makePhoneCall({
            phoneNumber: this.data.storeinfo.tel
        });
    },
    refresh: function() {
        wx.startPullDownRefresh({});
    },
    formSubmit: function(t) {
        var e = this, o = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/AddFormId",
            cachetime: "0",
            data: {
                user_id: o,
                form_id: t.detail.formId
            },
            success: function(t) {
                console.log(t.data, o);
            }
        }), wx.showModal({
            title: "提示",
            content: "确定取消排号吗？",
            success: function(t) {
                if (t.cancel) return !0;
                t.confirm && (wx.showLoading({
                    title: "操作中"
                }), app.util.request({
                    url: "entry/wxapp/DelNumber",
                    cachetime: "0",
                    data: {
                        num_id: e.data.id
                    },
                    success: function(t) {
                        console.log(t.data), t.data ? (wx.showToast({
                            title: "取消成功",
                            icon: "success",
                            mask: !0,
                            duration: 1e3
                        }), setTimeout(function() {
                            wx.redirectTo({
                                url: "getnum?storeid=" + e.data.storeinfo.id
                            });
                        }, 1e3)) : wx.showToast({
                            title: "请重试",
                            icon: "loading",
                            duration: 1e3
                        });
                    }
                }));
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        this.reLoad(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {}
});