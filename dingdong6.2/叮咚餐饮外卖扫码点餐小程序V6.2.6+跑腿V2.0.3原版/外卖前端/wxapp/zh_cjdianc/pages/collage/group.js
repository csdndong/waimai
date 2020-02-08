var app = getApp();

Page({
    data: {
        page: 1,
        group_list: []
    },
    onLoad: function(t) {
        var a = this;
        app.setNavigationBarColor(a), wx.setNavigationBarTitle({
            title: t.title
        }), a.setData({
            store_id: t.id,
            store_logo: t.store_logo
        }), a.reload();
    },
    refresh: function(t) {
        var n = this;
        app.util.request({
            url: "entry/wxapp/Ad",
            cachetime: "0",
            data: {
                type: 9
            },
            success: function(t) {
                console.log("轮播图列表", t), n.setData({
                    imgArray: t.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/GroupType",
            cachetime: "0",
            success: function(t) {
                if (console.log("分类列表", t), 5 < t.data.length) var a = 340; else a = 170;
                for (var e = [], o = 0, r = t.data.length; o < r; o += 10) e.push(t.data.slice(o, o + 10));
                n.setData({
                    nav_array: e,
                    height: a
                });
            }
        });
    },
    reload: function(t) {
        var a = this, e = a.data.page, o = a.data.group_list;
        app.util.request({
            url: "entry/wxapp/GroupGoods",
            cachetime: "0",
            data: {
                store_id: "",
                type_id: "",
                page: e,
                display: 1
            },
            success: function(t) {
                console.log("商品列表", t), 0 < t.data.length && (o = o.concat(t.data), a.setData({
                    group_list: o,
                    page: e + 1
                }));
            }
        });
    },
    jumps: function(t) {
        var a = t.currentTarget.dataset.id, e = t.currentTarget.dataset.name, o = t.currentTarget.dataset.appid, r = t.currentTarget.dataset.src, n = t.currentTarget.dataset.wb_src, i = t.currentTarget.dataset.type;
        console.log(a, e, o, r, n, i), 1 == i ? (console.log(r), wx.navigateTo({
            url: r
        })) : 2 == i ? (wx.setStorageSync("vr", n), wx.navigateTo({
            url: "../car/car"
        })) : 3 == i && wx.navigateToMiniProgram({
            appId: o
        });
    },
    nav_child: function(t) {
        wx.navigateTo({
            url: "list?id=" + t.currentTarget.dataset.id + "&store_id=&store_logo=" + this.data.store_logo + "&display=1"
        });
    },
    index: function(t) {
        wx.navigateTo({
            url: "index?id=" + t.currentTarget.dataset.id + "&store_id=" + t.currentTarget.dataset.storeid + "&store_logo=" + this.data.store_logo
        });
    },
    onReady: function() {},
    onShow: function() {
        this.refresh();
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        this.reload();
    },
    onShareAppMessage: function() {}
});