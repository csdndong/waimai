var a = getApp();

Page({
    data: {
        imgUrls: [ "http://img02.tooopen.com/images/20150928/tooopen_sy_143912755726.jpg", "http://img06.tooopen.com/images/20160818/tooopen_sy_175866434296.jpg", "http://img06.tooopen.com/images/20160818/tooopen_sy_175833047715.jpg" ]
    },
    onLoad: function(o) {
        var n = this;
        a.setNavigationBarColor(this), a.pageOnLoad(this), a.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(o) {
                console.log(o);
                var t = o.data;
                n.setData({
                    xtxx: t
                });
            }
        });
    },
    maketel: function(o) {
        var t = this.data.xtxx.tel;
        wx.makePhoneCall({
            phoneNumber: t
        });
    },
    location: function() {
        var o = this.data.xtxx.gs_zb.split(","), t = this.data.xtxx;
        console.log(o), wx.openLocation({
            latitude: parseFloat(o[0]),
            longitude: parseFloat(o[1]),
            address: t.gs_add,
            name: "位置"
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});