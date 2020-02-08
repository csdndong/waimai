var app = new getApp(), uniacid = app.siteInfo.uniacid;

Page({
    data: {
        checkAll: !1,
        select: [],
        deskData: [],
        orderData: [],
        orderDetail: [],
        desk_id: "",
        order_id: ""
    },
    onLoad: function(e) {
        var i = this;
        if (e.desk_id) {
            var s = e.desk_id;
            app.util.request({
                url: "entry/wxapp/order",
                data: {
                    control: "desk",
                    op: "deskOrderDetail",
                    uniacid: uniacid,
                    desk_id: s
                },
                success: function(e) {
                    console.log(e);
                    var t = e.data, a = t.orderDetail, d = t.deskData, r = t.orderData;
                    i.setData({
                        orderDetail: a,
                        deskData: d,
                        orderData: r,
                        desk_id: s
                    });
                }
            });
        } else if (e.order_id) {
            var o = e.order_id;
            app.util.request({
                url: "entry/wxapp/order",
                data: {
                    control: "desk",
                    op: "deskOrderDetail",
                    uniacid: uniacid,
                    order_id: o
                },
                success: function(e) {
                    var t = e.data, a = t.orderDetail, d = t.deskData, r = t.orderData;
                    i.setData({
                        orderDetail: a,
                        deskData: d,
                        orderData: r,
                        order_id: o,
                        desk_id: d.id
                    });
                }
            });
        }
    },
    displayAll: function(e) {
        var t = e.currentTarget.dataset.id, a = this.data.orderDetail;
        for (var d in a) if (a[d].uid == t) {
            var r = a[d].show;
            a[d].show = !r;
        }
        this.setData({
            orderDetail: a
        });
    },
    selectItem: function(e) {
        var a = e.currentTarget.dataset.uid, d = e.currentTarget.dataset.id, t = !1, r = [], i = [], s = this.data.orderDetail;
        for (var o in s) s[o].items.map(function(e) {
            if (s[o].uid == a && e.id == d) {
                var t = e.selected;
                e.selected = !t;
            }
            e.selected && i.push(e), 0 === e.state && r.push(e);
        });
        r.every(function(e) {
            return !0 === e.selected;
        }) && (t = !0), this.setData({
            orderData: this.data.orderData,
            checkAll: t,
            select: i,
            orderDetail: s
        });
    },
    selectAll: function(e) {
        var t = [], a = this, d = a.data.orderDetail;
        for (var r in d) d[r].items.map(function(e) {
            0 == e.status && (e.selected = !a.data.checkAll, e.selected && t.push(e));
        });
        this.setData({
            orderData: this.data.orderData,
            checkAll: !this.data.checkAll,
            select: t,
            orderDetail: d
        });
    },
    returnFoods: function() {
        var a = this, d = this.data.select, r = a.data.orderData.id;
        d.length <= 0 ? wx.showToast({
            title: "请选择要操作的菜品",
            icon: "none"
        }) : wx.showModal({
            title: "提示",
            content: "确认要退掉所选菜品吗？",
            success: function(e) {
                if (e.confirm) {
                    var t = JSON.stringify(d);
                    app.util.request({
                        url: "entry/wxapp/order",
                        method: "POST",
                        data: {
                            control: "desk",
                            op: "deskOpeartionGoods",
                            uniacid: uniacid,
                            select: t,
                            type: -1,
                            order_id: r
                        },
                        success: function(e) {
                            1 != e.data.code ? wx.showToast({
                                title: "操作失败"
                            }) : wx.showModal({
                                title: "提示",
                                content: "操作成功",
                                showCancel: !1,
                                success: function(e) {
                                    wx.redirectTo({
                                        url: "../select_items/index?desk_id=" + a.data.deskData.id
                                    });
                                }
                            });
                        }
                    });
                }
            }
        });
    },
    takeFood: function() {
        var e = this.data.select, t = this;
        if (e.length <= 0) wx.showToast({
            title: "请选择要操作的菜品",
            icon: "none"
        }); else {
            var a = JSON.stringify(e);
            app.util.request({
                url: "entry/wxapp/order",
                method: "POST",
                data: {
                    control: "desk",
                    op: "deskOpeartionGoods",
                    uniacid: uniacid,
                    select: a,
                    type: 1
                },
                success: function(e) {
                    1 != e.data.code ? wx.showToast({
                        title: "操作失败"
                    }) : wx.showModal({
                        title: "提示",
                        content: "上菜操作成功",
                        showCancel: !1,
                        success: function() {
                            wx.redirectTo({
                                url: "../select_items/index?desk_id=" + t.data.deskData.id
                            });
                        }
                    });
                }
            });
        }
    }
});