var app = getApp();

Page({
    data: {
        nav: [ "", "", "", "", "", "", "", "", "", "" ]
    },
    onLoad: function(o) {
        var t = this;
        wx.hideShareMenu(), t.location(o.id), app.getSystem(function(o) {
            console.log(o), t.setData({
                getSystem: o,
                color: o.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: o.color
            });
        });
    },
    location: function(o) {
        var t = this;
        app.util.request({
            url: "entry/wxapp/OrderInfo",
            data: {
                order_id: o
            },
            success: function(o) {
                console.log(o), o.data.time = app.ormatDate(o.data.time), o.data.price = (Number(o.data.yh_money) + Number(o.data.goods_price)).toFixed(2), 
                t.setData({
                    order_info: o.data
                });
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