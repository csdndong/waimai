var app = getApp();

Page({
    data: {
        color: "#459cf9",
        fwxy: !0
    },
    previewimg: function() {
        wx.previewImage({
            urls: [ this.data.code ]
        });
    },
    mdmfx: function() {
        this.setData({
            fwxy: !1
        });
    },
    yczz: function() {
        this.setData({
            fwxy: !0
        });
    },
    bctp: function() {
        console.log(this.data.code);
        var a = this;
        wx.downloadFile({
            url: a.data.code,
            success: function(t) {
                console.log(t), wx.showLoading({
                    title: "正在保存图片",
                    mask: !0
                }), wx.saveImageToPhotosAlbum({
                    filePath: t.tempFilePath,
                    success: function() {
                        a.setData({
                            fwxy: !0
                        }), wx.showModal({
                            title: "提示",
                            content: "商家海报保存成功",
                            showCancel: !1
                        });
                    }
                });
            },
            complete: function(t) {
                console.log(t), wx.hideLoading();
            }
        });
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this);
        var a = this, e = wx.getStorageSync("users").id;
        this.setData({
            userinfo: wx.getStorageSync("users")
        }), app.util.request({
            url: "entry/wxapp/MySx",
            cachetime: "0",
            data: {
                user_id: e
            },
            success: function(t) {
                console.log(t.data), t.data ? a.setData({
                    yqr: t.data.name,
                    sxdata: t.data
                }) : a.setData({
                    yqr: "总店",
                    sxdata: t.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/MyCode",
            cachetime: "0",
            data: {
                user_id: e
            },
            success: function(t) {
                console.log(t.data), a.setData({
                    code: t.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/CheckRetail",
            cachetime: "0",
            success: function(t) {
                console.log(t), a.setData({
                    fxset: t.data
                });
            }
        });
    },
    distribution: function(t) {
        wx.navigateTo({
            url: "distribution"
        });
    },
    downline: function(t) {
        wx.navigateTo({
            url: "downline"
        });
    },
    ranking: function(t) {
        wx.navigateTo({
            url: "ranking"
        });
    },
    invation: function(t) {
        wx.navigateTo({
            url: "index"
        });
    },
    tixian: function(t) {
        wx.navigateTo({
            url: "tixian"
        });
    },
    onReady: function() {},
    onShow: function() {
        var a = this, t = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/MyCommission",
            cachetime: "0",
            data: {
                user_id: t
            },
            success: function(t) {
                console.log(t.data), a.setData({
                    yjdata: t.data
                });
            }
        });
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});