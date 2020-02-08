var app = getApp();

Page({
    data: {},
    onLoad: function(t) {
        app.setNavigationBarColor(this), this.reLoad();
    },
    reLoad: function() {
        var n = this;
        app.util.request({
            url: "entry/wxapp/Url",
            cachetime: "0",
            success: function(a) {
                console.log(a.data), app.util.request({
                    url: "entry/wxapp/StoreInfo",
                    cachetime: "0",
                    data: {
                        store_id: getApp().sjid
                    },
                    success: function(t) {
                        for (var e = 0; e < t.data.store.environment.length; e++) t.data.store.environment[e] = a.data + t.data.store.environment[e];
                        for (var o = 0; o < t.data.store.yyzz.length; o++) t.data.store.yyzz[o] = a.data + t.data.store.yyzz[o];
                        console.log(t), n.setData({
                            store: t.data.store
                        });
                    }
                });
            }
        });
    },
    previewImage: function(t) {
        wx.previewImage({
            current: t.currentTarget.id,
            urls: t.currentTarget.dataset.urls
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        this.reLoad(), setTimeout(function() {
            wx.stopPullDownRefresh();
        }, 1500);
    },
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});