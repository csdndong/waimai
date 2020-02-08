var app = getApp(), util = require("../../utils/util.js");

Page({
    data: {
        pagenum: 1,
        order_list: [],
        storelist: [],
        mygd: !1,
        jzgd: !0,
        tabs: [ "已完成", "未完成", "无效" ],
        activeIndex: 0
    },
    tabClick: function(t) {
        this.setData({
            activeIndex: t.currentTarget.id,
            pagenum: 1,
            order_list: [],
            storelist: [],
            mygd: !1,
            jzgd: !0
        }), this.reLoad();
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this), this.reLoad();
    },
    reLoad: function() {
        var t, o = this, a = this.data.activeIndex, e = wx.getStorageSync("users").id, i = this.data.pagenum;
        0 == a && (t = "2"), 1 == a && (t = "1"), 2 == a && (t = "3"), console.log(a, t, e, i), 
        app.util.request({
            url: "entry/wxapp/CommissionList",
            cachetime: "0",
            data: {
                type: t,
                user_id: e,
                page: i,
                pagesize: 10
            },
            success: function(t) {
                console.log("分页返回的列表数据", t.data);
                for (var a = 0; a < t.data.length; a++) t.data[a].time = util.ormatDate(t.data[a].time);
                t.data.length < 10 ? o.setData({
                    mygd: !0,
                    jzgd: !0
                }) : o.setData({
                    jzgd: !0,
                    pagenum: o.data.pagenum + 1
                });
                var e = o.data.storelist;
                e = function(t) {
                    for (var a = [], e = 0; e < t.length; e++) -1 == a.indexOf(t[e]) && a.push(t[e]);
                    return a;
                }(e = e.concat(t.data)), o.setData({
                    order_list: e,
                    storelist: e
                }), console.log(e);
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        console.log("上拉加载", this.data.pagenum);
        !this.data.mygd && this.data.jzgd && (this.setData({
            jzgd: !1
        }), this.reLoad());
    }
});