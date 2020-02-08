var t = getApp();

Page({
    data: {
        showStatus: !1
    },
    onLoad: function(e) {
        var a = this;
        t.util.request({
            url: "manage/order/takeout/detail",
            data: {
                id: e.id || 433
            },
            success: function(e) {
                var s = e.data.message;
                s.errno ? t.util.toast(s.message) : a.setData(s.message);
            }
        });
    },
    chooseStatus: function() {
        this.setData({
            showStatus: !this.data.showStatus
        });
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    },
    onChangeOrderStatus: function(e) {
        var a = this, s = e.currentTarget.dataset, o = s.type, n = s.id;
        s.status;
        if ("cancel" == o || "direct_deliveryer" == o || "reply" == o) return wx.navigateTo({
            url: "./op?type=" + o + "&id=" + n
        }), !1;
        wx.showModal({
            title: "系统提示",
            content: s.confirm,
            success: function(e) {
                e.confirm ? t.util.request({
                    url: "manage/order/takeout/status",
                    data: s,
                    success: function(e) {
                        var s = e.data.message;
                        t.util.toast(s.message, "", 1e3), s.errno || a.onLoad({
                            id: n
                        });
                    }
                }) : e.cancel;
            }
        });
    },
    onPullDownRefresh: function() {
        this.onLoad({
            id: this.data.order.id
        }), wx.stopPullDownRefresh();
    }
});