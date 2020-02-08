var a = getApp();

Page({
    data: {},
    onLoad: function(t) {
        var e = this;
        if (!t.id) return a.util.toast("参数错误"), !1;
        a.util.request({
            url: "manage/finance/current/detail",
            data: {
                id: t.id
            },
            success: function(t) {
                var s = t.data.message;
                s.errno ? a.util.toast(s.message) : e.setData(s.message);
            }
        });
    }
});