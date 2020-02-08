var app = getApp();

Page({
    data: {
        list: []
    },
    kindToggle: function(t) {
        var e = t.currentTarget.id, a = this.data.list;
        console.log(e);
        for (var n = 0, o = a.length; n < o; ++n) a[n].open = n == e && !a[n].open;
        this.setData({
            list: a
        });
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this);
        var e = this;
        console.log(this), app.util.request({
            url: "entry/wxapp/GetHelp",
            cachetime: "0",
            success: function(t) {
                console.log(t.data), e.setData({
                    list: t.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(t) {
                console.log(t.data), e.setData({
                    tel: t.data.tel
                });
            }
        });
    },
    tel: function() {
        wx.makePhoneCall({
            phoneNumber: this.data.tel
        });
    },
    tzxq: function(t) {
        console.log(t.currentTarget.dataset.answer), wx.setStorageSync("answer", t.currentTarget.dataset.answer), 
        wx.navigateTo({
            url: "kfzx?title=" + t.currentTarget.dataset.title
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