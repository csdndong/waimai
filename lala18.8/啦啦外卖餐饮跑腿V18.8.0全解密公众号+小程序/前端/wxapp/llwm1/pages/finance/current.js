var a = getApp();

Page({
    data: {
        trade_type: 0,
        showLoading: !1,
        records: {
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
        var e = this, t = a.currentTarget.dataset.type;
        if (t == e.data.trade_type) return !1;
        e.setData({
            trade_type: t,
            records: {
                page: 1,
                psize: 15,
                empty: !1,
                loaded: !1,
                data: []
            }
        }), e.onReachBottom();
    },
    onPullDownRefresh: function() {
        var a = this;
        a.setData({
            trade_type: 0,
            records: {
                page: 1,
                psize: 15,
                empty: !1,
                loaded: !1,
                data: []
            }
        }), a.onLoad(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        var e = this;
        if (e.data.records.loaded) return !1;
        e.setData({
            showLoading: !0
        }), a.util.request({
            url: "manage/finance/current",
            data: {
                trade_type: e.data.trade_type,
                page: e.data.records.page,
                psize: e.data.records.psize
            },
            success: function(t) {
                var r = t.data.message;
                if (r.errno) a.util.toast(r.message); else {
                    r = r.message;
                    var d = e.data.records.data.concat(r.records);
                    e.data.records.data = d, e.data.records.page++, d.length || (e.data.records.empty = !0), 
                    r.records.length < e.data.records.psize && (e.data.records.loaded = !0), e.setData({
                        records: e.data.records,
                        showLoading: !1
                    });
                }
            }
        });
    },
    onJsEvent: function(e) {
        a.util.jsEvent(e);
    }
});