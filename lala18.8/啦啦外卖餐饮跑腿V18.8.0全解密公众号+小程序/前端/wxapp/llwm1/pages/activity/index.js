var t = getApp();

Page({
    data: {},
    onLoad: function() {
        var e = this;
        t.util.request({
            url: "manage/activity/index",
            success: function(a) {
                var s = a.data.message;
                if (s.errno) return t.util.toast(s.message), !1;
                e.setData(s.message);
            }
        });
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    }
});