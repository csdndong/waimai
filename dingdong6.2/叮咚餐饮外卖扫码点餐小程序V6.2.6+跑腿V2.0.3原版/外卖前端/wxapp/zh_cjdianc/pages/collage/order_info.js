var app = getApp();

Page({
    data: {},
    onLoad: function(o) {
        var a = this, n = wx.getStorageSync("order_info");
        app.setNavigationBarColor(this), (a = this).setData({
            url: wx.getStorageSync("url")
        }), console.log(n), a.setData({
            info: n
        }), app.util.request({
            url: "entry/wxapp/OrderCode",
            cachetime: "0",
            data: {
                order_id: n.id
            },
            success: function(o) {
                a.setData({
                    hx_code: o.data
                });
            }
        });
    },
    my_group: function(o) {
        var a = this.data.info;
        wx.navigateTo({
            url: "collageInfo?id=" + a.group_id + "&user_id=" + a.user_id + "&goods_id=" + a.goods_id
        });
    },
    buy: function(o) {
        var a = this.data.info;
        wx.navigateTo({
            url: "/zh_cjdianc/pages/collage/index?id=" + a.goods_id + "&store_id=" + a.store_id
        });
    },
    more: function(o) {
        wx.navigateTo({
            url: "/zh_cjdianc/pages/collage/group"
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