var a = getApp();

Page({
    data: {
        status: 0,
        showLoading: !1,
        record: {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        }
    },
    onLoad: function(a) {
        this.onReachBottom();
    },
    onChangeType: function(a) {
        var t = this, e = a.currentTarget.dataset.type;
        if (e == t.data.status) return !1;
        t.setData({
            status: e,
            record: {
                page: 1,
                psize: 15,
                empty: !1,
                loaded: !1,
                data: []
            }
        }), t.onReachBottom();
    },
    onReachBottom: function() {
        var t = this;
        if (t.data.record.loaded) return !1;
        t.setData({
            showLoading: !0
        }), a.util.request({
            url: "manage/finance/getcashRecord",
            data: {
                status: t.data.status,
                page: t.data.record.page,
                psize: t.data.record.psize
            },
            success: function(e) {
                var r = e.data.message;
                if (r.errno) return a.util.toast(r.message), !1;
                r = r.message;
                var o = t.data.record.data.concat(r.record);
                t.data.record.data = o, t.data.record.page++, o.length || (t.data.record.empty = !0), 
                r.record.length < t.data.record.psize && (t.data.record.loaded = !0), t.setData({
                    record: t.data.record,
                    showLoading: !1
                });
            }
        });
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    },
    onShareAppMessage: function() {}
});