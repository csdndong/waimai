var n = getApp();

Page({
    data: {},
    onLoad: function(o) {
        var t = this, e = [];
        setInterval(function() {
            wx.getLocation({
                type: "gcj02",
                success: function(o) {
                    console.log(o), e.push(o), t.setData({
                        record: e
                    }), n.util.request({
                        url: "delivery/member/set/location",
                        data: {
                            location_x: o.latitude,
                            location_y: o.longitude
                        },
                        success: function() {}
                    });
                }
            });
        }, 3e3);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});