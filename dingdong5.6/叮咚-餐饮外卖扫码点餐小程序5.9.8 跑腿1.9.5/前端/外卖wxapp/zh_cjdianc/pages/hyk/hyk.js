var app = getApp(), util = require("../../utils/util.js");

Page({
    data: {
        Coupons: [ {
            reduce: "5",
            state: "1"
        }, {
            reduce: "8.8",
            state: "2"
        }, {
            reduce: "5",
            state: "1"
        }, {
            reduce: "8.8",
            state: "2"
        }, {
            reduce: "5",
            state: "1"
        } ],
        fwxy: !0,
        xymc: "会员特权说明",
        xynr: ""
    },
    lookck: function() {
        wx.navigateTo({
            url: "../car/xydtl?title=会员特权说明"
        });
    },
    jumps: function(t) {
        var e = t.currentTarget.dataset.id, a = t.currentTarget.dataset.name, r = t.currentTarget.dataset.appid, n = t.currentTarget.dataset.src, o = t.currentTarget.dataset.wb_src, s = t.currentTarget.dataset.type;
        console.log(e, a, r, n, o, s), 1 == s ? (console.log(n), wx.navigateTo({
            url: n
        })) : 2 == s ? (wx.setStorageSync("vr", o), wx.navigateTo({
            url: "../car/car"
        })) : 3 == s && wx.navigateToMiniProgram({
            appId: r
        });
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this), app.pageOnLoad(this);
        var r = this;
        app.util.request({
            url: "entry/wxapp/ad",
            cachetime: "30",
            success: function(t) {
                console.log(t);
                for (var e = [], a = 0; a < t.data.length; a++) "8" == t.data[a].type && e.push(t.data[a]);
                console.log(e), r.setData({
                    lblist: e,
                    xtxx: getApp().xtxx,
                    xynr: getApp().xtxx.hy_details.replace(/\<img/gi, '<img style="width:100%;height:auto" ')
                });
            }
        }), app.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(t) {
                console.log(t), r.setData({
                    system: t.data
                });
            }
        });
    },
    onReady: function() {},
    onShow: function() {
        var e = this, t = wx.getStorageSync("users").id, a = util.formatTime(new Date()).substring(0, 10).replace(/\//g, "-");
        console.log(a.toString()), app.util.request({
            url: "entry/wxapp/UserInfo",
            cachetime: "0",
            data: {
                user_id: t
            },
            success: function(t) {
                console.log(t), ("" == t.data.dq_time || t.data.dq_time < a.toString()) && (t.data.ishy = 2), 
                e.setData({
                    userInfo: t.data,
                    lxr: t.data.user_name,
                    tel: t.data.user_tel
                });
            }
        });
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});