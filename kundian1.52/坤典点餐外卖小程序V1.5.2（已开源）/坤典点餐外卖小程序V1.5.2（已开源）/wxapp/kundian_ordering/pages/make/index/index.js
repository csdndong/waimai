var app = new getApp(), uniacid = app.siteInfo.uniacid;

Page({
    data: {
        cartid: "",
        order_type: "",
        date: "",
        time: "",
        array: [ "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20" ],
        index: "",
        cartData: [],
        total_price: 0,
        ordering_title: "提前预定到店开餐",
        date_time: []
    },
    onLoad: function(t) {
        var e = this;
        app.util.request({
            url: "entry/wxapp/index",
            data: {
                op: "getOrderingAbout",
                uniacid: uniacid
            },
            success: function(t) {
                t.data.ordering_title && e.setData({
                    ordering_title: t.data.ordering_title,
                    date_time: t.data.aboutData.date_time
                });
            }
        });
    },
    bindDateChange: function(t) {
        this.setData({
            date: t.detail.value
        });
    },
    bindTimeChange: function(t) {
        this.setData({
            time: t.detail.value
        });
    },
    bindPickerChange: function(t) {
        var e = t.detail.value, a = this.data.array;
        this.setData({
            index: a[e]
        });
    },
    formSubmit: function(t) {
        var e = this.data, a = (e.cardid, e.date), i = e.time, n = e.index, d = (e.order_type, 
        t.detail.value), o = d.name, r = d.phone, s = d.remark, u = wx.getStorageSync("kundian_ordering_uid");
        if ("" == o) wx.showToast({
            title: "请填写姓名"
        }); else if ("" == r) wx.showToast({
            title: "请填写联系电话"
        }); else {
            if (!/^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1})|(17[0-9]{1})|(19[0-9]{1})|(16[0-9]{1}))+\d{8})$/.test(r)) return wx.showToast({
                title: "手机号有误！",
                icon: "success"
            }), !1;
            "" == a ? wx.showToast({
                title: "请选择用餐日期"
            }) : "" == i ? wx.showToast({
                title: "请填写用餐时间"
            }) : "" == n ? wx.showToast({
                title: "请填写用餐人数"
            }) : app.util.request({
                url: "entry/wxapp/order",
                data: {
                    control: "index",
                    op: "orderMake",
                    uniacid: uniacid,
                    uid: u,
                    name: o,
                    phone: r,
                    date: a,
                    time: i,
                    person_count: n,
                    remark: s
                },
                success: function(t) {
                    console.log(t), 1 == t.data.code ? wx.showToast({
                        title: "订餐成功",
                        success: function(t) {
                            wx.redirectTo({
                                url: "../../order/index/index"
                            });
                        }
                    }) : wx.showToast({
                        title: "订餐失败"
                    });
                }
            });
        }
    }
});