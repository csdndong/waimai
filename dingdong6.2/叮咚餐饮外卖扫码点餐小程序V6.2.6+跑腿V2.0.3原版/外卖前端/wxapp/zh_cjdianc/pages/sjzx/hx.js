/*   time:2019-07-18 01:03:18*/
var app = getApp();
Page({
    data: {},
    onLoad: function(t) {
        app.setNavigationBarColor(this);
        var o = this;
        console.log(t);
        var e = decodeURIComponent(t.scene);
        console.log(e, e.split(","));
        var i = e.split(",")[1],
            n = e.split(",")[0],
            a = t.storeid;
        this.setData({
            moid: i,
            msjid: n,
            storeid: a
        }), wx.showLoading({
            title: "加载中"
        }), app.getUserInfo(function(t) {
            console.log(t), o.setData({
                smuid: t.id
            })
        }), app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: n
            },
            success: function(t) {
                console.log("小店详情", t), o.setData({
                    admin_id: t.data.store.admin_id
                })
            }
        })
    },
    hx: function() {
        var o = getCurrentPages();
        console.log(o);
        var t = this.data.storeid,
            e = this.data.admin_id,
            i = this.data.smuid,
            n = this.data.moid,
            a = this.data.msjid;
        console.log("扫码人的storeid", t, "smuid", i, "admin_id", e, "订单id", n, "msjid", a), t == a || e == i ? app.util.request({
            url: "entry/wxapp/OkOrder",
            cachetime: "0",
            data: {
                order_id: n
            },
            success: function(t) {
                console.log(t), "1" == t.data ? (wx.showToast({
                    title: "核销成功",
                    icon: "success",
                    duration: 1e3
                }), setTimeout(function() {
                    1 < o.length ? setTimeout(function() {
                        wx.navigateBack({})
                    }, 1e3) : wx.reLaunch({
                        url: "/zh_cjdianc/pages/Liar/loginindex"
                    })
                }, 1e3)) : wx.showToast({
                    title: "请重试",
                    icon: "loading",
                    duration: 1e3
                })
            }
        }) : (wx.showModal({
            title: "提示",
            content: "您暂无核销权限"
        }), setTimeout(function() {
            1 < o.length ? setTimeout(function() {
                wx.navigateBack({})
            }, 1e3) : wx.reLaunch({
                url: "/zh_cjdianc/pages/Liar/loginindex"
            })
        }, 1e3))
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});