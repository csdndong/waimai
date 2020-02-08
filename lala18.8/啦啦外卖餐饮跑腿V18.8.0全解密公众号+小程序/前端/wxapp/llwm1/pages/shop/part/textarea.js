var t = getApp();

Page({
    data: {
        store: {},
        files: [],
        thumbs: [],
        tempThumbs: []
    },
    onLoad: function(e) {
        var a = this, s = e.type;
        if (!s) return !1;
        a.setData({
            type: s
        }), t.util.request({
            url: "manage/shop/index/info",
            data: {
                type: a.data.type
            },
            success: function(e) {
                var n = e.data.message;
                if (n.errno) return t.util.toast(n.message), !1;
                "thumbs" != s ? a.setData(n.message) : n.message.store.thumbs.length > 0 && a.setData({
                    thumbs: n.message.store.thumbs
                });
            }
        });
    },
    onGetTextarea: function(t) {
        this.setData({
            textarea: t.detail.value
        });
    },
    onTimeChange: function(t) {
        console.log(t);
        var e = this, a = t.currentTarget.dataset.index, s = t.currentTarget.dataset.type, n = t.detail.value;
        e.data.store.business_hours[a][s] = n, e.setData({
            store: e.data.store
        });
    },
    onRemoveTime: function(t) {
        var e = this, a = t.currentTarget.dataset.index;
        e.data.store.business_hours.splice(a, 1), e.setData({
            store: e.data.store
        });
    },
    onAddTime: function() {
        var e = this;
        if (e.data.store.business_hours.length >= 3) return t.util.toast("最多可添加3个时间段"), 
        !1;
        var a = {
            s: "00:00",
            e: "23:59"
        };
        e.data.store.business_hours.push(a), e.setData({
            store: e.data.store
        });
    },
    onSubmit: function(e) {
        var a = this;
        console.log("form发生了submit事件，携带数据为：", e.detail.value);
        var s = "", n = a.data.type;
        if ("title" == n || "address" == n || "telephone" == n) {
            if (!(s = e.detail.value.input)) return t.util.toast("信息不能为空"), !1;
        } else if ("notice" == n || "content" == n) s = a.data.textarea; else if ("business_hours" == n) s = a.data.store.business_hours; else if ("thumbs" == n) {
            var o = a.data.thumbs;
            if (o.length > 0) for (var r = 0; r < o.length; r++) {
                var i = {
                    image: o[r].filename || o[r].image,
                    url: ""
                };
                a.data.tempThumbs.push(i);
            }
            s = a.data.tempThumbs;
        }
        t.util.request({
            url: "manage/shop/index/setting",
            method: "POST",
            data: {
                type: a.data.type,
                value: JSON.stringify(s),
                formid: e.detail.formId
            },
            success: function(e) {
                var a = e.data.message;
                if (a.errno) return t.util.toast(a.message), !1;
                t.util.toast("设置成功", "../info", 1e3);
            }
        });
    },
    onPullDownRefresh: function() {
        var t = this;
        this.onLoad({
            type: t.data.type
        }), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {},
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    }
});