var t = getApp(), a = require("../../static/js/utils/dateTimePicker.js");

Page({
    data: {
        options: {},
        startYear: 2018,
        endYear: 2030
    },
    onLoad: function(e) {
        var i = this;
        t.util.request({
            url: "manage/activity/index/activity_other",
            data: {
                type: e.type
            },
            success: function(e) {
                var n = e.data.message;
                if (n.errno) return t.util.toast(n.message), !1;
                var s = a.dateTimePicker(i.data.startYear, i.data.endYear), r = s.dateTimeArray, o = s.dateTime;
                r.pop(), o.pop(), i.data.starttime = r[0][o[0]] + "-" + r[1][o[1]] + "-" + r[2][o[2]] + " " + r[3][o[3]] + ":" + r[4][o[4]], 
                i.setData({
                    starttime: i.data.starttime,
                    endtime: i.data.starttime,
                    dateTime: o,
                    dateTimeArray: r,
                    type: n.message.type,
                    page_title: n.message.page_title,
                    discount_title: n.message.discount_title,
                    discount_cn: n.message.discount_cn
                }), wx.setNavigationBarTitle({
                    title: i.data.page_title
                });
            }
        });
    },
    changeDateTime: function(t) {
        this.setData({
            dateTime: t.detail.value
        });
    },
    changeDateTimeColumn: function(t) {
        var e = this, i = e.data.dateTime, n = e.data.dateTimeArray;
        i[t.detail.column] = t.detail.value, n[2] = a.getMonthDay(n[0][i[0]], n[1][i[1]]);
        var s = t.currentTarget.dataset.type, r = n[0][i[0]] + "-" + n[1][i[1]] + "-" + n[2][i[2]] + " " + n[3][i[3]] + ":" + n[4][i[4]];
        "starttime" == s ? e.setData({
            starttime: r
        }) : "endtime" == s && e.setData({
            endtime: r
        });
    },
    onInput: function(t) {
        var a = this, e = t.currentTarget.dataset.index, i = t.currentTarget.dataset.type, n = t.detail.value;
        a.data.options[e] || (a.data.options[e] = {}), a.data.options[e][i] = n;
    },
    onSubmit: function(a) {
        var e = this;
        if (!e.data.type) return t.util.toast("请选择活动类型", "", 1e3), !1;
        var i = {
            options: e.data.options,
            starttime: e.data.starttime,
            endtime: e.data.endtime
        };
        t.util.request({
            url: "manage/activity/index/activity_other",
            data: {
                type: e.data.type,
                params: JSON.stringify(i)
            },
            method: "POST",
            success: function(a) {
                var e = a.data.message;
                if (t.util.toast(e.message), e.errno) return !1;
                wx.navigateTo({
                    url: "./index"
                });
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});