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
            url: "delivery/statcenter/stat/rank",
            data: {
                type: a.data.type,
                sort_type: a.data.sort_type
            },
            success: function(e) {
                var s = e.data.message;
                if (s.errno) return t.util.toast(s.message), !1;
                a.setData(s.message);
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
        var e = this, a = t.currentTarget.dataset.sort_type, s = "";
        "total_success_order" == a ? s = "单" : "percent_normal" == a ? s = "%" : "avg_delivery_success_time" == a && (s = "分钟"), 
        e.setData({
            dataUnit: s,
            sort_type: a
        }), e.onLoad();
    }
});