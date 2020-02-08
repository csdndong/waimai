var app = getApp();

Page({
    data: {
        page: 1,
        group_list: []
    },
    onLoad: function(t) {
        if (app.setNavigationBarColor(this), wx.setNavigationBarTitle({
            title: t.title
        }), console.log(t), null == t.id) var o = ""; else o = t.id;
        if (null == t.store_id) var a = ""; else a = t.store_id;
        if (null == t.store_logo) var e = ""; else e = t.store_logo;
        if (null == t.display) var i = ""; else i = t.display;
        this.setData({
            id: o,
            store_id: a,
            store_logo: e,
            display: i
        }), wx.showLoading({
            title: "正在加载"
        }), this.reload();
    },
    reload: function(t) {
        var o = this, a = o.data.page, e = o.data.group_list;
        app.util.request({
            url: "entry/wxapp/GroupGoods",
            cachetime: "0",
            data: {
                store_id: o.data.store_id,
                type_id: o.data.id,
                page: a,
                display: o.data.display
            },
            success: function(t) {
                console.log("商品列表", t), console.log(e), 0 < t.data.length && (wx.hideLoading(), 
                e = e.concat(t.data), o.setData({
                    group_list: e,
                    page: a + 1
                }));
            }
        });
    },
    index: function(t) {
        wx.navigateTo({
            url: "index?id=" + t.currentTarget.dataset.id + "&store_id=" + t.currentTarget.dataset.storeid + "&store_logo=" + this.data.store_logo
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        this.reload();
    },
    onShareAppMessage: function() {}
});