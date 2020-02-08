var t = getApp();

Page({
    data: {},
    onLoad: function(e) {
        wx.removeStorageSync("timer");
        var n = this;
        t.util.request({
            url: "delivery/member/mine/index",
            success: function(e) {
                var o = e.data.message;
                if (o.errno) return t.util.toast(o.message), !1;
                n.setData(o.message);
            }
        });
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    },
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    },
    onClearStorage: function(e) {
        wx.showModal({
            title: "",
            content: "确定清除缓存吗？",
            success: function(e) {
                e.confirm && (t.util.followLocation(!0, !0), t.util.toast("清除缓存成功"));
            }
        });
    }
});