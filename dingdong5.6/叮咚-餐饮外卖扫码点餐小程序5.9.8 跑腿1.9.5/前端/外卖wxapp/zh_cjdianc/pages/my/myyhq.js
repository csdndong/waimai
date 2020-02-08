var app = getApp();

Page({
    data: {
        tabs: [ "平台红包", "商家红包" ],
        activeIndex: 0
    },
    tabClick: function(t) {
        this.setData({
            activeIndex: t.currentTarget.id
        });
    },
    qsy: function(t) {
        console.log(t.currentTarget.dataset.sjid), getApp().sjid = t.currentTarget.dataset.sjid, 
        wx.redirectTo({
            url: "/zh_cjdianc/pages/seller/index"
        });
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this), console.log(this);
        var o = this, a = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/MyCoupons",
            cachetime: "0",
            data: {
                user_id: a
            },
            success: function(t) {
                console.log(t.data);
                for (var a = [], e = [], n = 0; n < t.data.length; n++) "2" == t.data[n].type && a.push(t.data[n]), 
                "1" == t.data[n].type && e.push(t.data[n]);
                console.log(a, e), o.setData({
                    ptarr: a,
                    sjarr: e
                });
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});