var app = getApp(), util = require("../../utils/util.js");

Page({
    data: {
        tabs: [ "待审核", "已通过", "已拒绝" ],
        activeIndex: 0
    },
    tabClick: function(t) {
        this.setData({
            activeIndex: t.currentTarget.id
        });
    },
    reLoad: function() {
        var i = this, t = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/YjtxList",
            cachetime: "0",
            data: {
                user_id: t
            },
            success: function(t) {
                console.log(t.data);
                for (var a = 0; a < t.data.length; a++) t.data[a].time = util.ormatDate(t.data[a].time), 
                t.data[a].sh_time = util.ormatDate(t.data[a].sh_time);
                var e = [], o = [], n = [];
                for (a = 0; a < t.data.length; a++) "1" == t.data[a].state && e.push(t.data[a]), 
                "2" == t.data[a].state && o.push(t.data[a]), "3" == t.data[a].state && n.push(t.data[a]);
                console.log(e, o, n), i.setData({
                    dsh: e,
                    ytg: o,
                    yjj: n
                });
            }
        });
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this), this.reLoad();
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        this.reLoad();
    },
    onReachBottom: function() {}
});