var app = new getApp(), uniacid = app.siteInfo.uniacid;

Page({
    data: {
        orderInfo: {
            Number: 38,
            orderState: "订单已下厨",
            tableNum: "08",
            peopleNum: 6,
            orderTime: "17:45",
            WaiterNum: 1,
            WaiterAvator: "../../../images/banner/avator.png",
            consumptionAmount: "395.00",
            discountAmount: "10.00",
            serviceAmount: "10.00",
            total: "395.00",
            additional: [ {
                name: "餐巾纸",
                amount: 1,
                price: "2.00"
            } ],
            selectItem: []
        },
        projectNum: 0,
        isShow: !1,
        height: 570,
        desk_id: "",
        order_id: "",
        orderData: [],
        deskData: [],
        setData: []
    },
    onLoad: function(a) {
        var t = 0;
        if (this.data.orderInfo.selectItem.map(function(a) {
            t += a.selects.length;
        }), a.desk_id) {
            var e = a.desk_id;
            this.setData({
                projectNum: t + this.data.orderInfo.additional.length,
                desk_id: e
            });
            var r = this;
            app.util.request({
                url: "entry/wxapp/order",
                data: {
                    control: "desk",
                    op: "deskOrderDetail",
                    uniacid: uniacid,
                    desk_id: e
                },
                success: function(a) {
                    if (console.log(a), a.data.orderData) {
                        var t = a.data, e = t.orderDetail, o = t.orderData, i = t.deskData, d = t.setData;
                        r.setData({
                            selectItem: e,
                            orderData: o,
                            deskData: i,
                            setData: d
                        });
                    }
                }
            });
        } else {
            var o = a.order_id;
            this.setData({
                projectNum: t + this.data.orderInfo.additional.length,
                order_id: o
            });
            var s = this;
            app.util.request({
                url: "entry/wxapp/order",
                data: {
                    control: "desk",
                    op: "deskOrderDetail",
                    uniacid: uniacid,
                    order_id: o
                },
                success: function(a) {
                    if (console.log(a), a.data.orderData) {
                        var t = a.data, e = t.orderDetail, o = t.orderData, i = t.deskData, d = t.setData;
                        s.setData({
                            selectItem: e,
                            orderData: o,
                            deskData: i,
                            setData: d
                        });
                    }
                }
            });
        }
    },
    checkAll: function() {
        var a = this.data.isShow, t = 550;
        a || (t = 970), this.setData({
            isShow: !a,
            height: t
        });
    },
    goOnOrder: function(a) {
        wx.redirectTo({
            url: "../diancan/index?desk_id=" + this.data.deskData.id
        });
    },
    goPay: function(a) {
        if (1 == this.data.setData.is_open_desk_pay) {
            var t = app.siteInfo.uniacid, e = a.currentTarget.dataset.orderid;
            app.util.request({
                url: "entry/wxapp/deskPay",
                data: {
                    orderid: e,
                    uniacid: t
                },
                cachetime: "0",
                success: function(a) {
                    console.log(a), a.data && a.data.data && !a.data.errno && wx.requestPayment({
                        timeStamp: a.data.data.timeStamp,
                        nonceStr: a.data.data.nonceStr,
                        package: a.data.data.package,
                        signType: "MD5",
                        paySign: a.data.data.paySign,
                        success: function(a) {
                            console.log(a), "requestPayment:ok" == a.errMsg ? app.util.request({
                                url: "entry/wxapp/order",
                                data: {
                                    control: "desk",
                                    op: "notify",
                                    order_id: e,
                                    uniacid: t
                                },
                                success: function(a) {
                                    console.log(a), wx.showToast({
                                        title: "支付成功",
                                        success: function(a) {},
                                        fail: function(a) {},
                                        complete: function(a) {}
                                    }), wx.switchTab({
                                        url: "../../order/index/index?is_active=2"
                                    });
                                }
                            }) : wx.showToast({
                                title: "支付失败"
                            });
                        },
                        fail: function(a) {
                            console.log("error");
                        }
                    });
                },
                fail: function(a) {
                    wx.showModal({
                        title: "系统提示",
                        content: a.data.message ? a.data.message : "错误",
                        showCancel: !1,
                        success: function(a) {
                            a.confirm && console.log("2");
                        }
                    });
                }
            });
        } else wx.showModal({
            title: "提示",
            content: "请到柜台进行结账！"
        });
    }
});