var app = getApp(), util = require("../../utils/util.js");

Page({
    data: {},
    onLoad: function(e) {
        console.log(e), this.setData({
            store_id: e.storeid
        }), wx.setNavigationBarTitle({
            title: "排队取号"
        });
        var a = this;
        app.setNavigationBarColor(this), app.getimgUrl(this), app.getUserInfo(function(t) {
            console.log(t), app.util.request({
                url: "entry/wxapp/IsReceive",
                cachetime: "0",
                data: {
                    store_id: e.storeid,
                    user_id: t.id
                },
                success: function(t) {
                    console.log(t.data), t.data && "1" == t.data.state && wx.redirectTo({
                        url: "getnumdl?storeid=" + a.data.store_id + "&id=" + t.data.id
                    });
                }
            });
        }), app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: e.storeid
            },
            success: function(t) {
                console.log(t.data), a.setData({
                    storeinfo: t.data.store
                });
            }
        }), app.util.request({
            url: "entry/wxapp/GetTable",
            cachetime: "0",
            data: {
                store_id: e.storeid
            },
            success: function(t) {
                console.log(t.data), a.setData({
                    tableinfo: t.data
                });
            }
        });
    },
    select: function(t) {
        this.setData({
            activeIndex: t.currentTarget.dataset.index
        });
    },
    formSubmit: function(t) {
        var e = this, a = this.data.store_id, o = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/AddFormId",
            cachetime: "0",
            data: {
                user_id: o,
                form_id: t.detail.formId
            },
            success: function(t) {
                console.log(t.data, o, a);
            }
        }), null == this.data.activeIndex ? wx.showModal({
            title: "提示",
            content: "请选择桌位类型"
        }) : wx.showModal({
            title: "提示",
            content: "您选择的是" + e.data.tableinfo[e.data.activeIndex].typename,
            success: function(t) {
                if (t.cancel) return !0;
                t.confirm && (wx.showLoading({
                    title: "操作中"
                }), app.util.request({
                    url: "entry/wxapp/SaveNumber",
                    cachetime: "0",
                    data: {
                        store_id: a,
                        typename: e.data.tableinfo[e.data.activeIndex].typename,
                        user_id: o
                    },
                    success: function(t) {
                        console.log(t.data), t.data ? (wx.showToast({
                            title: "取号成功",
                            icon: "success",
                            mask: !0,
                            duration: 1e3
                        }), setTimeout(function() {
                            wx.redirectTo({
                                url: "getnumdl?storeid=" + e.data.storeinfo.id + "&id=" + t.data
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
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});