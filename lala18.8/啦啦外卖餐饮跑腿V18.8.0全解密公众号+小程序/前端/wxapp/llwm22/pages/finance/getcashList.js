var t = getApp();

Page({
    data: {
        status: 0,
        psize: 10,
        showLoading: !1,
        records: {
            status_0: {
                loaded: 0,
                empty: 0,
                page: 1,
                status: 0,
                list: []
            },
            status_1: {
                loaded: 0,
                empty: 0,
                page: 1,
                status: 1,
                list: []
            },
            status_2: {
                loaded: 0,
                empty: 0,
                page: 1,
                status: 2,
                list: []
            },
            status_3: {
                loaded: 0,
                empty: 0,
                page: 1,
                status: 3,
                list: []
            }
        }
    },
    onLoad: function(t) {
        this.onGetRecords();
    },
    onJsEvent: function(s) {
        t.util.jsEvent(s);
    },
    onChange: function(t) {
        var s = this, a = t.currentTarget.dataset.index;
        s.setData({
            status: a
        }), s.onGetRecords();
    },
    onGetRecords: function() {
        var s = this, a = "status_" + s.data.status;
        if (1 == s.data.records[a].loaded) return !1;
        s.setData({
            showLoading: !0
        }), t.util.request({
            url: "delivery/finance/getcash/list",
            data: {
                psize: s.data.psize,
                page: s.data.records[a].page,
                status: s.data.records[a].status
            },
            success: function(e) {
                var o = e.data.message;
                o.errno && t.util.toast(o.message);
                var d = s.data.records[a].list.concat(o.message.records);
                s.data.records[a].list = d, d.length || (s.data.records[a].empty = 1), o.message.records.length < s.data.psize && (s.data.records[a].loaded = 1), 
                s.data.records[a].page++, s.setData({
                    records: s.data.records,
                    showLoading: !1
                });
            }
        });
    },
    onReachBottom: function() {
        this.onGetRecords();
    },
    onPullDownRefresh: function() {
        var t = this, s = t.data.status, a = "status_" + s;
        t.data.records[a] = {
            loaded: 0,
            empty: 0,
            page: 1,
            status: s,
            list: []
        }, t.onGetRecords(), wx.stopPullDownRefresh();
    }
});