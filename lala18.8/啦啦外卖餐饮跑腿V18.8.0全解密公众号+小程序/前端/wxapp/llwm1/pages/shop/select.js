var t = getApp();

Page({
    data: {},
    onLoad: function(e) {
        var n = this, s = e.from;
        t.util.request({
            url: "manage/home/index",
            data: {
                nosid: 1
            },
            success: function(e) {
                var a = e.data.message;
                if (a.errno) return t.util.toast(a.message), !1;
                var r = a.message.stores;
                s || 1 != r.length ? r.length > 1 && n.setData(a.message) : (t.util.setStorageSync("__sid", r[0].id), 
                t.util.jump2url("/pages/order/index"));
            }
        });
    },
    onSwitch: function(e) {
        var n = e.currentTarget.dataset.sid;
        t.util.setStorageSync("__sid", n), t.util.jump2url("/pages/order/index");
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    }
});