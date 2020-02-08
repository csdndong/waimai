var t = getApp();

Page({
    data: {
        weeks: [ "周一", "周二", "周三", "周四", "周五", "周六", "周日" ],
        limit_week: {}
    },
    onLoad: function(e) {
        var a = this;
        a.data.options = e, t.util.request({
            url: "manage/goods/category/post",
            data: {
                id: e.id || 0
            },
            success: function(e) {
                var o = e.data.message;
                if (o.errno) return t.util.toast(o.message), !1;
                o.message.now_category.length || (a.data.now_category = {}), a.setData({
                    categorys: o.message.categorys,
                    now_category: o.message.now_category
                });
            }
        });
    },
    onSelectParentCategory: function(t) {
        var e = this, a = t.detail.value;
        e.data.parentCategory = e.data.categorys[a], e.setData({
            parentCategory: e.data.parentCategory,
            "now_category.parentid": e.data.parentCategory.id
        });
    },
    onSetShowtime: function(t) {
        var e = this, a = t.detail.value;
        e.setData({
            "now_category.is_showtime": a
        });
    },
    onSelectWeek: function(t) {
        var e = this, a = t.currentTarget.dataset.value;
        e.data.limit_week[a] ? delete e.data.limit_week[a] : e.data.limit_week[a] = a, e.setData({
            "now_category.limit_week": e.data.limit_week
        });
    },
    onSelectTime: function(t) {
        var e = this, a = t.detail.value, o = t.currentTarget.dataset.type;
        e.data.now_category[o] = a, "start_time" == o ? e.setData({
            "now_category.start_time": a
        }) : e.setData({
            "now_category.end_time": a
        });
    },
    onSubmit: function(e) {
        var a = this, o = e.detail.value;
        if (!o.title) return t.util.toast("请输入分类名称", "", 1e3), !1;
        if (1 == o.is_showtime) {
            if (!o.start_time) return t.util.toast("请选择限购开始时间", "", 1e3), !1;
            if (!o.end_time) return t.util.toast("请选择限购结束时间", "", 1e3), !1;
        }
        o.parentid = a.data.now_category.parentid, o.limit_week = a.data.now_category.limit_week, 
        t.util.request({
            url: "manage/goods/category/post",
            data: {
                id: a.data.options.id || 0,
                params: JSON.stringify(o),
                formid: e.detail.formId
            },
            method: "POST",
            success: function(e) {
                var a = e.data.message;
                if (t.util.toast(a.message), a.errno) return !1;
                wx.navigateTo({
                    url: "./index"
                });
            }
        });
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});