/*   time:2019-07-18 01:03:18*/
var app = getApp();
Page({
    data: {},
    bindTimeChange: function(t) {
        this.setData({
            time: t.detail.value
        })
    },
    bindTimeChange1: function(t) {
        this.setData({
            time1: t.detail.value
        })
    },
    bindTimeChange2: function(t) {
        this.setData({
            time2: t.detail.value
        })
    },
    bindTimeChange3: function(t) {
        this.setData({
            time3: t.detail.value
        })
    },
    onLoad: function(t) {
        var e = this,
            a = wx.getStorageSync("sjdsjid");
        console.log(t, a), this.setData({
            szname: t.szname
        }), app.setNavigationBarColor(this), app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: a
            },
            success: function(t) {
                console.log("商家详情", t), e.setData({
                    storeinfo: t.data,
                    time: t.data.store.time,
                    time1: t.data.store.time2,
                    time2: t.data.store.time3,
                    time3: t.data.store.time4
                }), "1" == t.data.storeset.print_type && e.setData({
                    dyfs: !0
                }), "2" == t.data.storeset.print_type && e.setData({
                    dyfs: !1
                }), "1" == t.data.storeset.print_mode && e.setData({
                    dysj: !0
                }), "2" == t.data.storeset.print_mode && e.setData({
                    dysj: !1
                }), "2" == t.data.store.is_rest && e.setData({
                    sfyy: !0
                }), "1" == t.data.store.is_rest && e.setData({
                    sfyy: !1
                })
            }
        })
    },
    formSubmit: function(t) {
        console.log("form发生了submit事件，携带数据为：", t.detail.value);
        var e = this.data.szname,
            a = wx.getStorageSync("sjdsjid");
        if (console.log(e, a), 1 == e) {
            var i = t.detail.value.radiodyfs,
                o = t.detail.value.radiodysj;
            console.log(i, o), wx.showLoading({
                title: "提交中",
                mask: !0
            }), app.util.request({
                url: "entry/wxapp/UpStore",
                cachetime: "0",
                data: {
                    store_id: a,
                    print_type: i,
                    print_mode: o
                },
                success: function(t) {
                    console.log(t.data), "1" == t.data ? (wx.showToast({
                        title: "设置成功",
                        icon: "success",
                        duration: 1e3
                    }), setTimeout(function() {
                        wx.navigateBack({})
                    }, 1e3)) : "2" == t.data ? wx.showToast({
                        title: "请修改后提交",
                        icon: "loading",
                        duration: 1e3
                    }) : wx.showToast({
                        title: "请重试",
                        icon: "loading",
                        duration: 1e3
                    })
                }
            })
        }
        if (2 == e) {
            var s = t.detail.value.radiosfyy,
                n = t.detail.value.time,
                d = t.detail.value.time1,
                l = t.detail.value.time2,
                r = t.detail.value.time3;
            if (console.log(s, n, d, l, r), "" == n || "" == d || "" == l || "" == r) return void wx.showModal({
                title: "提示",
                content: "时间不能为空"
            });
            if (d <= n || l <= d) return void wx.showModal({
                title: "提示",
                content: "请设置正确合理的时间"
            });
            wx.showLoading({
                title: "提交中",
                mask: !0
            }), app.util.request({
                url: "entry/wxapp/UpStore",
                cachetime: "0",
                data: {
                    store_id: a,
                    is_rest: s,
                    time: n,
                    time2: d,
                    time3: l,
                    time4: r
                },
                success: function(t) {
                    console.log(t.data), "1" == t.data ? (wx.showToast({
                        title: "设置成功",
                        icon: "success",
                        duration: 1e3
                    }), setTimeout(function() {
                        wx.navigateBack({})
                    }, 1e3)) : "2" == t.data ? wx.showToast({
                        title: "请修改后提交",
                        icon: "loading",
                        duration: 1e3
                    }) : wx.showToast({
                        title: "请重试",
                        icon: "loading",
                        duration: 1e3
                    })
                }
            })
        }
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});