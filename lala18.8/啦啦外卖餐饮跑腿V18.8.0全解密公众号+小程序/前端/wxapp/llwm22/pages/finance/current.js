var t = getApp();

Page({
    data: {
        status: 0,
        psize: 10,
        showLoading: !1,
        records: {
            tradeType_0: {
                loaded: 0,
                empty: 0,
                page: 1,
                trade_type: 0,
                list: []
            },
            tradeType_1: {
                loaded: 0,
                empty: 0,
                page: 1,
                trade_type: 1,
                list: []
            },
            tradeType_2: {
                loaded: 0,
                empty: 0,
                page: 1,
                trade_type: 2,
                list: []
            },
            tradeType_3: {
                loaded: 0,
                empty: 0,
                page: 1,
                trade_type: 3,
                list: []
            }
        }
    },
    onLoad: function(t) {
        this.onGetRecords();
    },
    bindDateChange: function(t) {
        var e = this, a = e.data.stat_month, s = t.detail.value;
        if (e.setData({
            stat_month: t.detail.value
        }), a != s && a || !a && s != e.data.stat.now) {
            var d = e.data.status, r = "tradeType_" + d;
            e.data.records[r] = {
                loaded: 0,
                empty: 0,
                page: 1,
                trade_type: d,
                list: []
            }, e.onGetRecords();
        }
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    },
    onChange: function(t) {
        var e = this, a = t.currentTarget.dataset.index;
        e.setData({
            status: a
        }), e.onGetRecords();
    },
    onGetRecords: function() {
        var e = this, a = "tradeType_" + e.data.status;
        if (1 == e.data.records[a].loaded) return !1;
        e.setData({
            showLoading: !0
        }), t.util.request({
            url: "delivery/finance/current",
            data: {
                psize: e.data.psize,
                page: e.data.records[a].page,
                trade_type: e.data.records[a].trade_type,
                stat_month: e.data.stat_month || 0
            },
            success: function(s) {
                var d = s.data.message;
                d.errno && t.util.toast(d.message);
                var r = e.data.records[a].list.concat(d.message.records);
                e.data.records[a].list = r, r.length || (e.data.records[a].empty = 1), d.message.records.length < e.data.psize && (e.data.records[a].loaded = 1), 
                e.data.records[a].page++, e.setData({
                    records: e.data.records,
                    stat: d.message.stat,
                    showLoading: !1
                });
            }
        });
    },
    onReachBottom: function() {
        this.onGetRecords();
    },
    onPullDownRefresh: function() {
        var t = this, e = t.data.status, a = "tradeType_" + e;
        t.data.records[a] = {
            loaded: 0,
            empty: 0,
            page: 1,
            trade_type: e,
            list: []
        }, t.onGetRecords(), wx.stopPullDownRefresh();
    }
});