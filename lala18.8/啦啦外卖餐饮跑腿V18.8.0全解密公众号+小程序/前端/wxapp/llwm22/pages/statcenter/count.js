var t = getApp();

Page({
    data: {
        type: "today"
    },
    onLoad: function(e) {
        var a = this;
        t.util.request({
            url: "delivery/statcenter/stat/index",
            data: {
                type: a.data.type
            },
            success: function(e) {
                var n = e.data.message;
                if (n.errno) return t.util.toast(n.message), !1;
                a.setData(n.message);
            }
        });
    },
    onChange: function(t) {
        var e = this, a = t.currentTarget.dataset.type;
        e.setData({
            type: a
        }), e.onLoad();
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});