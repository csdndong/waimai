var t = getApp();

Page({
    data: {
        notice: {
            page: 1,
            psize: 10,
            loaded: !1,
            empty: !1,
            data: []
        }
    },
    onLoad: function(t) {
        this.onPullDownRefresh();
    },
    onPullDownRefresh: function() {
        var t = this;
        t.setData({
            notice: {
                page: 1,
                psize: 10,
                loaded: !1,
                empty: !1,
                data: []
            }
        }), t.onReachBottom(), wx.stopPullDownRefresh();
    },
    onReachBottom: function(e) {
        var a = this;
        if (a.data.notice.loaded) return !1;
        t.util.request({
            url: "manage/news/notice/list",
            data: {
                page: a.data.notice.page,
                psize: a.data.notice.psize
            },
            success: function(e) {
                var n = e.data.message;
                if (n.errno) return t.util.toast(n.message), !1;
                n = n.message;
                var o = a.data.notice.data.concat(n.notice);
                a.data.notice.data = o, a.data.notice.page++, o.length || (a.data.notice.empty = !0), 
                n.notice.length < a.data.notice.psize && (a.data.notice.loaded = !0), a.setData({
                    notice: a.data.notice
                });
            }
        });
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    }
});