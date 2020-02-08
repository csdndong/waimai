/*   time:2019-07-18 01:03:16*/
var app = getApp(),
    util = require("../../utils/util.js");
Page({
    data: {
        tabs: ["手速榜", "总榜"],
        activeIndex: 0,
        sliderOffset: 0,
        sliderLeft: 15,
        refresh_top: !1,
        refresh_top1: !1,
        rankpage: 1,
        zrankpage: 1,
        sranklist: [],
        szrank: []
    },
    tabClick: function(a) {
        console.log(a), this.setData({
            sliderOffset: a.currentTarget.offsetLeft,
            activeIndex: a.currentTarget.id
        })
    },
    onLoad: function(a) {
        app.setNavigationBarColor(this);
        var t = this,
            e = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/userinfo",
            cachetime: "0",
            data: {
                user_id: e
            },
            success: function(a) {
                console.log("个人信息", a), t.setData({
                    userinfo: a.data
                })
            }
        }), app.util.request({
            url: "entry/wxapp/Continuous",
            cachetime: "0",
            data: {
                user_id: e
            },
            success: function(a) {
                console.log("查看连续签到天数", a), t.setData({
                    lxts: a.data
                })
            }
        }), app.util.request({
            url: "entry/wxapp/MyJrRank",
            cachetime: "0",
            data: {
                user_id: e
            },
            success: function(a) {
                console.log("MyJrRank", a.data), a.data.time3 = util.ormatDate(a.data.time3).substring(11), t.setData({
                    MyRank: a.data
                })
            }
        }), this.rank(), this.zrank()
    },
    rank: function() {
        var e = this,
            s = (wx.getStorageSync("users").id, e.data.rankpage),
            a = e.data.zrankpage,
            n = e.data.sranklist,
            t = e.data.szrank;
        console.log(s, a, n, t), app.util.request({
            url: "entry/wxapp/JrRank",
            cachetime: "0",
            data: {
                page: s,
                pagesize: 20
            },
            success: function(a) {
                for (var t in console.log("JrRank", a.data), console.log(a), e.setData({
                    rankpage: s + 1
                }), a.data.length < 20 ? e.setData({
                    refresh_top: !0
                }) : e.setData({
                    refresh_top: !1
                }), a.data) a.data[t].time3 = util.ormatDate(a.data[t].time3).substring(11);
                n = n.concat(a.data), console.log(n), e.setData({
                    ranklist: n,
                    sranklist: n
                })
            }
        })
    },
    zrank: function() {
        var t = this,
            a = (wx.getStorageSync("users").id, t.data.rankpage),
            e = t.data.zrankpage,
            s = t.data.sranklist,
            n = t.data.szrank;
        console.log(a, e, s, n), app.util.request({
            url: "entry/wxapp/Rank",
            cachetime: "0",
            data: {
                page: e,
                pagesize: 20
            },
            success: function(a) {
                console.log("rank", a), console.log(a), t.setData({
                    zrankpage: e + 1
                }), a.data.length < 20 ? t.setData({
                    refresh_top1: !0
                }) : t.setData({
                    refresh_top1: !1
                }), n = n.concat(a.data), console.log(n), t.setData({
                    zrank: n,
                    szrank: n
                })
            }
        })
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        console.log("上拉加载", this.data.activeIndex, this.data.rankpage, this.data.zrankpage), 0 == this.data.refresh_top && 0 == this.data.activeIndex ? this.rank() : console.log("今日没有了"), 0 == this.data.refresh_top1 && 1 == this.data.activeIndex ? this.zrank() : console.log("总的没有了")
    }
});