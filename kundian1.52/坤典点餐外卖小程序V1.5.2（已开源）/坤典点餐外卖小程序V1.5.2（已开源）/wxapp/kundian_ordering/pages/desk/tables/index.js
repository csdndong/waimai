var app = new getApp();

Page({
    data: {
        tables: []
    },
    onLoad: function(t) {
        var a = this, e = app.siteInfo.uniacid;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "desk",
                op: "getShopDeskData",
                uniacid: e
            },
            success: function(t) {
                a.setData({
                    tables: t.data.deskData
                });
            }
        });
    },
    selectTable: function(t) {
        var a = t.currentTarget.dataset, e = a.id;
        1 != a.status ? wx.showToast({
            title: "未开桌"
        }) : wx.navigateTo({
            url: "../select_items/index?desk_id=" + e
        });
    }
});