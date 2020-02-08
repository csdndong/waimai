var app = getApp();

Page({
    data: {
        slide: [ {
            logo: "http://opocfatra.bkt.clouddn.com/images/0/2017/10/tdJ70qw1fEfjfVJfFDD09570eqF28d.jpg"
        }, {
            logo: "http://opocfatra.bkt.clouddn.com/images/0/2017/10/k5JQwpBfpb0u8sNNy5l5bhlnrhl33W.jpg"
        }, {
            logo: "http://opocfatra.bkt.clouddn.com/images/0/2017/10/zUeEednDedmUkIUumN9XI6IXU91eko.jpg"
        } ],
        fenlei: [],
        commodity: []
    },
    jumps: function(t) {
        var e = t.currentTarget.dataset.id, a = t.currentTarget.dataset.name, o = t.currentTarget.dataset.appid, n = t.currentTarget.dataset.src, r = t.currentTarget.dataset.wb_src, c = t.currentTarget.dataset.type;
        console.log(e, a, o, n, r, c), 1 == c ? (console.log(n), wx.navigateTo({
            url: n
        })) : 2 == c ? (wx.setStorageSync("vr", r), wx.navigateTo({
            url: "../car/car"
        })) : 3 == c && wx.navigateToMiniProgram({
            appId: o
        });
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this), wx.hideShareMenu({});
        var o = this;
        this.reLoad(), app.util.request({
            url: "entry/wxapp/ad",
            cachetime: "0",
            success: function(t) {
                console.log(t);
                for (var e = [], a = 0; a < t.data.length; a++) "6" == t.data[a].type && e.push(t.data[a]);
                console.log(e), o.setData({
                    lblist: e
                });
            }
        }), app.util.request({
            url: "entry/wxapp/Jftype",
            cachetime: "0",
            success: function(t) {
                console.log(t), o.setData({
                    fenlei: t.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/JfGoods",
            cachetime: "0",
            success: function(t) {
                console.log(t), o.setData({
                    commodity: t.data
                });
            }
        });
    },
    reLoad: function() {
        var e = this, t = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/UserInfo",
            cachetime: "0",
            data: {
                user_id: t
            },
            success: function(t) {
                console.log(t), e.setData({
                    integral: t.data.total_score
                });
            }
        });
    },
    record: function(t) {
        wx.navigateTo({
            url: "record/record"
        });
    },
    interinfo: function(t) {
        console.log(t.currentTarget.id), wx.navigateTo({
            url: "integralinfo/integralinfo?id=" + t.currentTarget.id
        });
    },
    cxfl: function(t) {
        console.log(t.currentTarget.id);
        var e = this;
        app.util.request({
            url: "entry/wxapp/JftypeGoods",
            cachetime: "0",
            data: {
                type_id: t.currentTarget.id
            },
            success: function(t) {
                console.log(t), e.setData({
                    commodity: t.data
                });
            }
        });
    },
    onReady: function() {},
    onShow: function() {
        this.reLoad();
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});