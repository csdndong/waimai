var app = getApp();

Page({
    data: {
        page: 1,
        statistics: [ {
            time: "2018-06-30",
            note: "后台充值",
            money: 50
        }, {
            time: "2018-06-30",
            note: "后台充值",
            money: 50
        }, {
            time: "2018-06-30",
            note: "提现",
            money: 50
        } ]
    },
    onLoad: function(t) {
        wx.hideShareMenu(), wx.setNavigationBarColor({
            frontColor: "#ffffff",
            backgroundColor: wx.getStorageSync("platform").color
        }), this.refresh();
    },
    refresh: function(t) {
        var o = this, e = o.data.statistics, n = o.data.page, a = wx.getStorageSync("userInfo").id;
        app.util.request({
            url: "entry/wxapp/Yjlist",
            data: {
                user_id: a,
                page: n
            },
            success: function(t) {
                if (console.log(t), 0 < t.data.length) {
                    for (var a in e = e.concat(t.data), t.data) t.data[a].time = app.ormatDate(t.data[a].time);
                    o.setData({
                        statistics: e,
                        page: n + 1
                    });
                }
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        this.refresh();
    }
});