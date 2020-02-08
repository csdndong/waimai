var e = getApp();

Page({
    data: {
        type: "recommendHome",
        day: {}
    },
    onLoad: function() {
        var a = this;
        e.util.request({
            url: "manage/advertise/index/recommend",
            success: function(t) {
                var d = t.data.message;
                if (d.errno) return e.util.toast(d.message), !1;
                if (a.setData(d.message), a.data.advertise.recommendHome.leave > 0) for (var r in a.data.advertise.recommendHome.prices) {
                    a.data.day.recommendHome = r;
                    break;
                } else if (a.data.advertise.recommendOther.leave > 0) {
                    a.data.type = "recommendOther";
                    for (var r in a.data.advertise.recommendOther.prices) {
                        a.data.day.recommendOther = r;
                        break;
                    }
                }
                a.setData({
                    day: a.data.day,
                    type: a.data.type
                });
            }
        });
    },
    onSelectPosition: function(e) {
        var a = this, t = e.currentTarget.dataset.type, d = e.currentTarget.dataset.index;
        "day" == t ? a.data.advertise[a.data.type].leave > 0 && (a.data.day[a.data.type] = d, 
        a.setData({
            day: a.data.day
        })) : a.setData({
            type: t
        });
    },
    onSubmit: function() {
        var a = this;
        if (!a.data.type) return e.util.toast("请选择购买位置", "", 1e3), !1;
        if (!a.data.day[a.data.type]) return e.util.toast("请选择购买天数", "", 1e3), !1;
        var t = {
            type: a.data.type,
            day: a.data.day[a.data.type],
            pay_type: "credit"
        };
        e.util.request({
            url: "manage/advertise/index/stick",
            data: t,
            method: "POST",
            success: function(a) {
                var t = a.data.message;
                if (t.errno) return e.util.toast(t.message), !1;
                wx.showToast({
                    title: "下单成功"
                }), e.util.pay({
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