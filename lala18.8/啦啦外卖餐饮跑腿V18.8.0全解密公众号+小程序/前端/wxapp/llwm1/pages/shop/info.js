var e = getApp();

Page({
    data: {},
    onLoad: function() {
        var t = this;
        e.util.request({
            url: "manage/shop/index/info",
            success: function(s) {
                var o = s.data.message;
                if (o.errno) return e.util.toast(o.message), !1;
                t.setData(o.message);
            }
        });
    },
    onShowTip: function() {
        wx.showModal({
            content: "您无法修改此项，如需更换门店分类，请联系平台管理员",
            showCancel: !1
        });
    },
    onUploadLogo: function() {
        e.util.image({
            count: 1,
            success: function(t) {
                var s = t.filename;
                e.util.request({
                    url: "manage/shop/index/logo",
                    methods: "POST",
                    data: {
                        logo: s
                    },
                    success: function(t) {
                        var s = t.data.message;
                        if (s.errno) return e.util.toast(s.message), !1;
                        e.util.toast(s.message, "refresh");
                    }
                });
            }
        });
    },
    onJsEvent: function(t) {
        e.util.jsEvent(t);
    },
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    }
});