var t = getApp();

Page({
    data: {
        type: "today",
        sort_type: "total_success_order",
        dataUnit: "单"
    },
    onLoad: function(e) {
        var a = this;
        t.util.request({
            url: "delivery/statcenter/stat/rank_errander",
            data: {
                type: a.data.type,
                sort_type: a.data.sort_type
            },
            success: function(e) {
                var r = e.data.message;
                if (r.errno) return t.util.toast(r.message), !1;
                a.setData(r.message);
            }
        });
    },
    onPullDownRefresh: function() {},
    onChange: function(t) {
        var e = this, a = t.currentTarget.dataset.type;
        e.setData({
            type: a
        }), e.onLoad();
    },
    onSortType: function(t) {
        var e = this, a = t.currentTarget.dataset.sort_type, r = "";
        "total_success_order" == a ? r = "单" : "percent_normal" == a ? r = "%" : "avg_delivery_success_time" == a && (r = "分钟"), 
        e.setData({
            dataUnit: r,
            sort_type: a
        }), e.onLoad();
    }
});