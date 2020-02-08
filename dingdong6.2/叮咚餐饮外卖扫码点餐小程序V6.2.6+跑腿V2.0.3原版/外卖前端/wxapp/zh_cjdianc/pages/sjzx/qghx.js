/*   time:2019-07-18 01:03:18*/
var app = getApp();
Page({
    data: {},
    onLoad: function(t) {
        app.setNavigationBarColor(this), wx.setNavigationBarTitle({
            title: "核销抢购订单"
        });
        var o = this;
        console.log(t);
        var e = decodeURIComponent(t.scene);
        console.log(e);
        var n = e,
            i = t.storeid;
        this.setData({
            moid: n,
            storeid: i
        }), wx.showLoading({
            title: "加载中"
        }), app.getUserInfo(function(t) {
            console.log(t), o.setData({
                smuid: t.id
            })
        })
    },
    hx: function() {
        var t = this.data.moid,
            o = this.data.storeid,
            e = this.data.smuid;
        console.log("扫码人的storeid", o, "smuid", e, "订单id", t), app.util.request({
            url: "entry/wxapp/QgHx",
            cachetime: "0",
            data: {
                order_id: t,
                store_id: o,
                user_id: e
            },
            success: function(t) {
                console.log(t), "核销成功" == t.data ? (wx.showToast({
                    title: "核销成功",
                    icon: "success",
                    duration: 1e3
                }), setTimeout(function() {
                    1 < pages.length ? setTimeout(function() {
                        wx.navigateBack({})
                    }, 1e3) : wx.reLaunch({
                        url: "/zh_cjdianc/pages/Liar/loginindex"
                    })
                }, 1e3)) : (wx.showModal({
                    title: "提示",
                    content: t.data
                }), setTimeout(function() {
                    wx.navigateBack({})
                }, 1e3))
            }
        })
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});