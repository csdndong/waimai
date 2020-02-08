var t = getApp();

Page({
    data: {
        status: 1
    },
    onLoad: function(a) {
        var s = this;
        a && a.status && (s.data.status = a.status), t.util.request({
            url: "manage/advertise/list/index",
            data: {
                status: s.data.status
            },
            success: function(a) {
                var n = a.data.message;
                if (n.errno) return t.util.toast(n.message), !1;
                s.setData(n.message);
            }
        });
    },
    onChangeStatus: function(t) {
        this.setData({
            status: t.target.dataset.status
        }), this.onLoad();
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});