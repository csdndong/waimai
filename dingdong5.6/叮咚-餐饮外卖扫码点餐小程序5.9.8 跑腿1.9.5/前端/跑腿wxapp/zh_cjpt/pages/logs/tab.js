var app = getApp();

Page({
    data: {
        ac_index: 0
    },
    onLoad: function(t) {
        var a = this;
        wx.hideShareMenu(), app.getSystem(function(t) {
            console.log(t), a.setData({
                getSystem: t,
                color: t.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: t.color
            });
        });
        var o = wx.getStorageSync("qs").id;
        app.util.request({
            url: "entry/wxapp/OrderStatistics",
            data: {
                qs_id: o
            },
            success: function(t) {
                console.log(t), a.setData({
                    Statistics: t.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/MoneyStatistics",
            data: {
                qs_id: o
            },
            success: function(t) {
                console.log(t), a.setData({
                    MoneyStatistics: t.data
                });
            }
        });
    },
    sele: function(t) {
        var a = this;
        0 == a.data.ac_index ? a.setData({
            ac_index: 1
        }) : a.setData({
            ac_index: 0
        });
    },
    day_order: function(t) {
        var a = t.currentTarget.dataset;
        wx.navigateTo({
            url: "day_order?type=" + a.type + "&num=" + a.num + "&price=" + a.price
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