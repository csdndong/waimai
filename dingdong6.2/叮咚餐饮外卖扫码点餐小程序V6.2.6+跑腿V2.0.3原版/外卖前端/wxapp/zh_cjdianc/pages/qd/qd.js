/*   time:2019-07-18 01:03:16*/
var app = getApp(),
    util = require("../../utils/util.js"),
    date = new Date,
    year = date.getFullYear(),
    month = date.getMonth() + 1,
    dayInMonth = date.getDate(),
    dayInWeek = date.getDay(),
    selected = [year, month, dayInMonth],
    week = [{
        value: "日",
        class: "weekend"
    }, {
        value: "一",
        class: ""
    }, {
        value: "二",
        class: ""
    }, {
        value: "三",
        class: ""
    }, {
        value: "四",
        class: ""
    }, {
        value: "五",
        class: ""
    }, {
        value: "六",
        class: "weekend"
    }],
    isLeapYear = function(t) {
        return t % 400 == 0 || t % 4 == 0 && t % 100 != 0
    }, isToday = function(t, e, a) {
        return t == year && e == month && a == dayInMonth
    }, isWeekend = function(t, e) {
        return (t + e) % 7 == 0 || (t + e - 1) % 7 == 0
    }, calEmptyGrid = function(t, e) {
        return new Date(t + "/" + e + "/02 00:00:00").getUTCDay()
    }, calDaysInMonth = function(t, e) {
        var a = isLeapYear(t);
        return 2 == month && a ? 29 : 2 != month || a ? [4, 6, 9, 11].includes(e) ? 30 : 31 : 28
    }, calWeekDay = function(t, e, a) {
        return new Date(t + "/" + e + "/" + a + " 00:00:00").getUTCDay()
    }, getThisMonthDays = function(t, e) {
        return new Date(t, e, 0).getDate()
    }, calDays = function(t, e) {
        for (var a = getThisMonthDays(t, e), s = calEmptyGrid(t, e), n = [], o = 1; o <= a; o++) {
            var l = isToday(t, e, o),
                i = selected[0] == t && selected[1] == e && selected[2] == o,
                r = l ? "today" : "",
                d = i ? "selected" : "",
                c = {
                    value: o,
                    date: [t, e, o],
                    class: "date-bg " + (isWeekend(s, o) ? "weekend" : "") + " " + r + " " + d + " " + (l && i ? "today-selected" : "")
                };
            n.push(c)
        }
        return n.slice(0, calDaysInMonth(t, e))
    };
Page({
    data: {
        currYear: year,
        currMonth: month,
        week: week,
        emptyGrids: calEmptyGrid(year, month),
        days: calDays(year, month),
        selected: selected,
        disabled: !1,
        logintext: "点击签到",
        lxts: 0,
        isbq: !1,
        bqtext: "点击补签"
    },
    onLoad: function() {
        function t() {
            var t = new Date,
                e = t.getMonth() + 1,
                a = t.getDate();
            return [t.getFullYear(), e, a]
        }
        app.setNavigationBarColor(this), console.log(this.data.days, this.data.selected), console.log(t()), this.setData({
            nowtime: t()
        });
        var e = this;
        app.util.request({
            url: "entry/wxapp/Signset",
            cachetime: "0",
            success: function(t) {
                console.log("签到设置", t), e.setData({
                    qdset: t.data
                })
            }
        }), app.util.request({
            url: "entry/wxapp/ContinuousList",
            cachetime: "0",
            success: function(t) {
                console.log("查看连签奖励", t), e.setData({
                    jl: t.data
                })
            }
        }), this.reLoad(), this.lqts()
    },
    in_array: function(t, e) {
        for (var a = 0; a < e.length; a++) {
            if (e[a].toString() == t) return !0
        }
        return !1
    },
    lqts: function() {
        this.setData({
            isbq: !1
        });
        var e = this,
            t = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/Continuous",
            cachetime: "0",
            data: {
                user_id: t
            },
            success: function(t) {
                console.log("查看连续签到天数", t), e.setData({
                    lxts: t.data
                })
            }
        }), app.util.request({
            url: "entry/wxapp/Isbq",
            cachetime: "0",
            data: {
                user_id: t
            },
            success: function(t) {
                console.log("isbq", t), e.setData({
                    havebq: t.data
                })
            }
        }), app.util.request({
            url: "entry/wxapp/userinfo",
            cachetime: "0",
            data: {
                user_id: t
            },
            success: function(t) {
                console.log("个人信息", t), e.setData({
                    grjf: t.data.total_score,
                    userinfo: t.data
                })
            }
        })
    },
    reLoad: function() {
        wx.showLoading({
            title: "加载中",
            mask: !0
        });
        var c = this,
            t = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/MySign",
            cachetime: "0",
            data: {
                user_id: t
            },
            success: function(t) {
                console.log("我的签到", t), c.setData({
                    wdqd: t.data
                });
                for (var e = [], d = c.data.days, a = 0; a < t.data.length; a++) e.push(t.data[a].time);
                console.log(e, d), c.in_array(c.data.nowtime.toString(), e) ? (console.log("今日已签到"), c.setData({
                    disabled: !0,
                    logintext: "今日已签到"
                })) : (console.log("今日未签到"), c.setData({
                    disabled: !1,
                    logintext: "点击签到"
                }));
                for (var s = 0; s < d.length; s++) c.in_array(d[s].date.toString(), e) && (d[s].isqd = 1);
                app.util.request({
                    url: "entry/wxapp/Special",
                    cachetime: "0",
                    success: function(t) {
                        console.log("Special", t);
                        for (var e = t.data, a = 0; a < e.length; a++) {
                            e[a].day = e[a].day.split("-");
                            var s = new Date(e[a].day[0], e[a].day[1] - 1, e[a].day[2]),
                                n = s.getFullYear(),
                                o = s.getMonth() + 1,
                                l = s.getDate();
                            e[a].day = n + "," + o + "," + l
                        }
                        console.log(e), c.setData({
                            special: e
                        });
                        for (var i = 0; i < d.length; i++) for (var r = 0; r < e.length; r++) d[i].date.toString() == e[r].day && (d[i].tsrq = e[r]);
                        c.setData({
                            days: d
                        })
                    }
                })
            }
        })
    },
    qd: function() {
        var e = this,
            t = this.data.userinfo;
        if (console.log(t), "" != t.img && "" != t.name) {
            var a = wx.getStorageSync("users").id,
                s = this.data.wdqd;
            console.log(e.data.nowtime, e.data.special, e.data.qdset, s);
            for (var n = e.data.qdset[0].integral, o = 0; o < e.data.special.length; o++) e.data.nowtime.toString() == e.data.special[o].day && (n = e.data.special[o].integral);
            if (0 == s.length) var l = e.data.qdset[0].one;
            else l = 0;
            console.log(n, l), wx.showLoading({
                title: "加载中",
                mask: !0
            }), app.util.request({
                url: "entry/wxapp/Sign",
                cachetime: "0",
                data: {
                    user_id: a,
                    time: e.data.nowtime.toString(),
                    integral: n,
                    one: l
                },
                success: function(t) {
                    wx.showLoading({
                        title: "签到成功",
                        mask: !0
                    }), console.log(t), e.reLoad(), e.lqts()
                }
            })
        } else wx.navigateTo({
            url: "../smdc/getdl"
        })
    },
    bq: function() {
        var e = this,
            t = this.data.userinfo;
        if (console.log(t), "" != t.img && "" != t.name) {
            var a = wx.getStorageSync("users").id,
                s = this.data.wdqd,
                n = Number(this.data.grjf);
            console.log(e.data.bqtime, e.data.special, e.data.qdset, s, n);
            for (var o = e.data.qdset[0].integral, l = 0; l < e.data.special.length; l++) e.data.bqtime.toString() == e.data.special[l].day && (o = e.data.special[l].integral);
            if (0 == s.length) var i = e.data.qdset[0].one;
            else i = 0;
            console.log(o, i), wx.showModal({
                title: "温馨提示",
                content: "补签将会扣除您" + e.data.qdset[0].bq_integral + "积分哦",
                success: function(t) {
                    t.confirm ? (console.log("用户点击确定"), Number(e.data.qdset[0].bq_integral) > n ? wx.showModal({
                        title: "提示",
                        content: "您的积分为" + n + ",不足补签扣除"
                    }) : (wx.showLoading({
                        title: "加载中",
                        mask: !0
                    }), app.util.request({
                        url: "entry/wxapp/Sign2",
                        cachetime: "0",
                        data: {
                            user_id: a,
                            time: e.data.bqtime.toString(),
                            integral: o,
                            one: i
                        },
                        success: function(t) {
                            console.log(t), e.reLoad(), e.lqts()
                        }
                    }))) : t.cancel && console.log("用户点击取消")
                }
            })
        } else wx.navigateTo({
            url: "../smdc/getdl"
        })
    },
    changeMonth: function(t) {
        var e = t.target.id,
            a = this.data.currYear,
            s = this.data.currMonth;
        s = "prev" == e ? s - 1 : s + 1, "prev" == e && s < 1 && (a -= 1, s = 12), "next" == e && 12 < s && (a += 1, s = 1), this.setData({
            currYear: a,
            currMonth: s,
            emptyGrids: calEmptyGrid(a, s),
            days: calDays(a, s)
        }), this.reLoad()
    },
    selectDate: function(t) {
        var e = this,
            a = this.data.havebq,
            s = e.data.nowtime,
            n = t.currentTarget.dataset.selected,
            o = t.currentTarget.dataset.tsrq;
        console.log(s, n, o), e.setData({
            bqtime: n
        });
        var l = new Date(s[0], s[1], s[2]),
            i = new Date(n[0], n[1], n[2]),
            r = l.getTime(),
            d = i.getTime();
        console.log(r, d, a), d < r ? (console.log("以前"), 2 == a ? e.setData({
            bqdisabled: !1,
            bqtext: "点击补签"
        }) : e.setData({
            bqdisabled: !0,
            bqtext: "今日已补签一次"
        }), e.setData({
            isbq: !0
        }), null != o.tsrq && wx.showModal({
            title: o.tsrq.day + "是" + o.tsrq.title,
            content: "本日签到特殊奖励" + o.tsrq.integral + "积分"
        })) : (r == d ? (null != o.tsrq && wx.showModal({
            title: o.tsrq.day + "是" + o.tsrq.title,
            content: "本日签到特殊奖励" + o.tsrq.integral + "积分"
        }), console.log("今日")) : (null != o.tsrq && wx.showModal({
            title: o.tsrq.day + "是" + o.tsrq.title,
            content: "本日签到特殊奖励" + o.tsrq.integral + "积分"
        }), console.log("以后")), e.setData({
            isbq: !1
        }));
        var c = o.value;
        this.setData({
            xz: c
        })
    },
    onPullDownRefresh: function() {
        this.reLoad(), this.lqts(), setTimeout(function() {
            wx.stopPullDownRefresh()
        }, 1e3)
    }
});