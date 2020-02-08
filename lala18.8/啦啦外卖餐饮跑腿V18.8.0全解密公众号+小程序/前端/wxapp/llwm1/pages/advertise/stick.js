var a = getApp();

Page({
    data: {
        displayorder: 0,
        day: 0
    },
    onLoad: function(e) {
        var t = this;
        a.util.request({
            url: "manage/advertise/index/stick",
            data: {
                type: e.type || "stick"
            },
            success: function(d) {
                var i = d.data.message;
                if (i.errno) return a.util.toast(i.message), !1;
                if (t.setData(i.message), "stick" == e.type) {
                    for (var r in t.data.advertise.prices) if (0 == t.data.advertise.prices[r].sailed) {
                        for (var s in t.data.advertise.prices[r].fees) {
                            t.data.day = s;
                            break;
                        }
                        t.data.displayorder = t.data.advertise.prices[r].displayorder;
                        break;
                    }
                } else if (t.data.advertise.leave > 0) for (var s in t.data.advertise.prices) {
                    t.data.day = s;
                    break;
                }
                t.setData({
                    day: t.data.day,
                    displayorder: t.data.displayorder
                }), wx.setNavigationBarTitle({
                    title: t.data.page_title
                });
            }
        });
    },
    onSelectPosition: function(a) {
        var e = this, t = a.currentTarget.dataset.type, d = a.currentTarget.dataset.index;
        if ("stick" == e.data.type) if ("day" == t) {
            var i = e.data.advertise.prices[e.data.displayorder];
            if (1 == i.sailed) return !1;
            e.setData({
                day: i.fees[d].day
            });
        } else e.setData({
            displayorder: d
        }); else e.data.advertise.leave > 0 && e.setData({
            day: d
        });
    },
    onSubmit: function() {
        var e = this;
        if ("stick" == e.data.type && !e.data.displayorder) return a.util.toast("请选择置顶位置", "", 1e3), 
        !1;
        if (!e.data.day) return a.util.toast("请选择购买天数", "", 1e3), !1;
        var t = {
            type: e.data.type,
            displayorder: e.data.displayorder,
            day: e.data.day,
            pay_type: "credit"
        };
        a.util.request({
            url: "manage/advertise/index/stick",
            data: t,
            method: "POST",
            success: function(e) {
                var t = e.data.message;
                if (t.errno) return a.util.toast(t.message), !1;
                wx.showToast({
                    title: "下单成功"
                }), a.util.pay({
                    pay_type: "credit",
                    order_type: "advertise",
                    order_id: t.message.id,
                    sid: t.message.sid
                });
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});