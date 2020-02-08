var app = new getApp(), uniacid = app.siteInfo.uniacid;

Page({
    data: {
        selectList: [],
        totalPrice: 0,
        totalNum: 0,
        desk_id: "",
        deskData: [],
        orderData: []
    },
    onLoad: function(t) {
        var e = wx.getStorageSync("selectList"), d = t.desk_id, s = this;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "desk",
                op: "getDeskData",
                uniacid: uniacid,
                desk_id: d
            },
            success: function(t) {
                var e = t.data, a = e.deskData, i = e.orderData;
                s.setData({
                    deskData: a,
                    desk_id: d,
                    orderData: i
                });
            }
        }), s.setData({
            selectList: e,
            totalPrice: t.totalPrice,
            totalNum: t.totalNum,
            desk_id: d
        });
    },
    confirmOrder: function(t) {
        var e = JSON.stringify(this.data.selectList), a = this.data.desk_id, i = wx.getStorageSync("kundian_ordering_uid");
        app.util.request({
            url: "entry/wxapp/order",
            method: "POST",
            data: {
                control: "desk",
                op: "confirmOrder",
                select: e,
                uniacid: uniacid,
                desk_id: a,
                uid: i
            },
            success: function(t) {
                if (1 == t.data.code) return wx.removeStorageSync("selectList"), void wx.showModal({
                    title: "提示",
                    content: "下单成功",
                    success: function(t) {
                        wx.redirectTo({
                            url: "../order_details/index?desk_id=" + a
                        });
                    }
                });
                wx.showToast({
                    title: "下单失败"
                });
            }
        });
    }
});