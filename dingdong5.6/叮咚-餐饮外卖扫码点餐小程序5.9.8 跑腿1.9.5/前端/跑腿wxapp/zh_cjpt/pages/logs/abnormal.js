var app = getApp();

Page({
    data: {
        page: 1,
        list: []
    },
    onLoad: function(a) {
        var t = this;
        app.getSystem(function(a) {
            console.log(a), t.setData({
                getSystem: a,
                distaceShop: Number(a.distance),
                color: a.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: a.color
            }), app.g_t(function(a) {
                console.log(a), t.setData({
                    location: a,
                    lat: a.split(",")[0],
                    lng: a.split(",")[1]
                }), t.refresh();
            });
        });
    },
    refresh: function(a) {
        var e = this, o = this.data.list, n = this.data.location;
        app.util.request({
            url: "entry/wxapp/Myabnormal",
            data: {
                qs_id: wx.getStorageSync("qs").id,
                page: this.data.page
            },
            success: function(a) {
                if (console.log(a), 0 < a.data.length) {
                    for (var t in a.data) a.data[t].distance = app.location(Number(n[0]), Number(a.data[t].sender_lat), Number(n[1]), Number(a.data[t].sender_lng)), 
                    a.data[t].distance1 = app.location(Number(a.data[t].sender_lat), Number(a.data[t].receiver_lat), Number(a.data[t].sender_lng), Number(a.data[t].receiver_lng)), 
                    a.data[t].wc_time = app.ormatDate(a.data[t].wc_time), a.data[t].goods_info = a.data[t].goods_info.split(","), 
                    a.data[t].time = app.ormatDate(a.data[t].time);
                    o = o.concat(a.data), e.setData({
                        list: o,
                        page: e.data.page + 1
                    });
                }
            }
        });
    },
    order_info: function(a) {
        wx.navigateTo({
            url: "../index/order_info?id=" + a.currentTarget.dataset.id + "&index=" + a.currentTarget.dataset.index
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        this.refresh();
    },
    onShareAppMessage: function() {}
});