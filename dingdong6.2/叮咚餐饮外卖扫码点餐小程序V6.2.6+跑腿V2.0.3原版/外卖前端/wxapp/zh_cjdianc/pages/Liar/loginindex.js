/*   time:2019-07-18 01:07:48*/
var dsq, dsq1, a = getApp();
Page({
    data: {
        second: 3
    },
    onLoad: function(e) {
        var o = this;
        console.log(e);
        var t = decodeURIComponent(e.scene);
        if (console.log("scene", t), "undefined" != t) var n = t;
        if (null != e.userid) {
            console.log("转发获取到的userid:", e.userid);
            n = e.userid
        }
        console.log("fxzuid", n), this.setData({
            fxzuid: n
        }), a.getUserInfo(function(e) {
            console.log(e), null != n ? a.util.request({
                url: "entry/wxapp/Binding",
                cachetime: "0",
                data: {
                    fx_user: e.id,
                    user_id: n
                },
                success: function(e) {
                    console.log(e)
                }
            }) : a.util.request({
                url: "entry/wxapp/Binding",
                cachetime: "0",
                data: {
                    fx_user: e.id,
                    user_id: 0
                },
                success: function(e) {
                    console.log(e)
                }
            })
        }), a.setNavigationBarColor(this), a.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(e) {
                var t = Number(e.data.countdown);
                dsq = setInterval(function() {
                    t--, o.setData({
                        second: t
                    })
                }, 1e3), dsq1 = setTimeout(function() {
                    clearInterval(dsq), o.tggg()
                }, 1e3 * t);
                var n = e.data;
                o.setData({
                    xtxx: n,
                    second: e.data.countdown
                }), getApp().xtxx1 = n, wx.setNavigationBarTitle({
                    title: e.data.url_name
                }), a.util.request({
                    url: "entry/wxapp/ad",
                    cachetime: "0",
                    data: {
                        type: "2"
                    },
                    success: function(e) {
                        0 == e.data.length && 0 < t && (clearInterval(dsq), clearTimeout(dsq1), setTimeout(function() {
                            "1" == n.model && wx.reLaunch({
                                url: "/zh_cjdianc/pages/index/index"
                            }), "2" == n.model && (getApp().sjid = n.default_store, wx.reLaunch({
                                url: "/zh_cjdianc/pages/seller/index"
                            })), "3" == n.model && wx.reLaunch({
                                url: "/zh_cjdianc/pages/Liar/Liar"
                            }), "4" == n.model && (getApp().sjid = n.default_store, wx.reLaunch({
                                url: "/zh_cjdianc/pages/seller/indextakeout"
                            }))
                        }, 0)), o.setData({
                            kpggimg: e.data
                        })
                    }
                })
            }
        })
    },
    tggg: function() {
        console.log("tggg"), clearInterval(dsq), clearTimeout(dsq1);
        var e = this.data.xtxx;
        "1" == e.model && wx.reLaunch({
            url: "/zh_cjdianc/pages/index/index"
        }), "2" == e.model && (getApp().sjid = e.default_store, wx.reLaunch({
            url: "/zh_cjdianc/pages/seller/index"
        })), "3" == e.model && wx.reLaunch({
            url: "/zh_cjdianc/pages/Liar/Liar"
        }), "4" == e.model && (getApp().sjid = e.default_store, wx.reLaunch({
            url: "/zh_cjdianc/pages/seller/indextakeout"
        }))
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});