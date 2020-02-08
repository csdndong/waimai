var dsq, dsq1, a = getApp();

Page({
    data: {
        second: 3
    },
    onLoad: function(e) {
        var o = this;
        console.log(e);
        var n = decodeURIComponent(e.scene);
        if (console.log("scene", n), "undefined" != n) var t = n;
        if (null != e.userid) {
            console.log("转发获取到的userid:", e.userid);
            t = e.userid;
        }
        console.log("fxzuid", t), this.setData({
            fxzuid: t
        }), a.getUserInfo(function(e) {
            console.log(e), null != t ? a.util.request({
                url: "entry/wxapp/Binding",
                cachetime: "0",
                data: {
                    fx_user: e.id,
                    user_id: t
                },
                success: function(e) {
                    console.log(e);
                }
            }) : a.util.request({
                url: "entry/wxapp/Binding",
                cachetime: "0",
                data: {
                    fx_user: e.id,
                    user_id: 0
                },
                success: function(e) {
                    console.log(e);
                }
            });
        }), a.setNavigationBarColor(this), a.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(e) {
                console.log(e);
                var n = Number(e.data.countdown);
                dsq = setInterval(function() {
                    n--, o.setData({
                        second: n
                    });
                }, 1e3), dsq1 = setTimeout(function() {
                    clearInterval(dsq), o.tggg();
                }, 1e3 * n);
                var t = e.data;
                o.setData({
                    xtxx: t,
                    second: e.data.countdown
                }), getApp().xtxx1 = t, wx.setNavigationBarTitle({
                    title: e.data.url_name
                }), a.util.request({
                    url: "entry/wxapp/ad",
                    cachetime: "0",
                    data: {
                        type: "2"
                    },
                    success: function(e) {
                        console.log(e), 0 == e.data.length && 0 < n && (clearInterval(dsq), clearTimeout(dsq1), 
                        setTimeout(function() {
                            "1" == t.model && wx.reLaunch({
                                url: "/zh_cjdianc/pages/index/index"
                            }), "2" == t.model && (getApp().sjid = t.default_store, wx.reLaunch({
                                url: "/zh_cjdianc/pages/seller/index"
                            })), "3" == t.model && wx.reLaunch({
                                url: "/zh_cjdianc/pages/Liar/Liar"
                            }), "4" == t.model && (getApp().sjid = t.default_store, wx.reLaunch({
                                url: "/zh_cjdianc/pages/seller/indextakeout"
                            }));
                        }, 0)), o.setData({
                            kpggimg: e.data
                        });
                    }
                });
            }
        });
    },
    tggg: function() {
        console.log("tggg"), clearInterval(dsq), clearTimeout(dsq1);
        var e = this.data.xtxx;
        console.log(e), "1" == e.model && wx.reLaunch({
            url: "/zh_cjdianc/pages/index/index"
        }), "2" == e.model && (getApp().sjid = e.default_store, wx.reLaunch({
            url: "/zh_cjdianc/pages/seller/index"
        })), "3" == e.model && wx.reLaunch({
            url: "/zh_cjdianc/pages/Liar/Liar"
        }), "4" == e.model && (getApp().sjid = e.default_store, wx.reLaunch({
            url: "/zh_cjdianc/pages/seller/indextakeout"
        }));
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});