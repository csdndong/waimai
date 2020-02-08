/*   time:2019-07-18 01:03:19*/
var app = getApp(),
    util = require("../../../utils/util.js");
Page({
    data: {
        selectedindex: 0,
        topnav: [{
            img: "../../../img/icon/dzt.png",
            img1: "../../../img/icon/wdzt.png",
            name: "全部"
        }, {
            img: "../../../img/icon/djd.png",
            img1: "../../../img/icon/wdjd.png",
            name: "待支付"
        }, {
            img: "../../../img/icon/ywc.png",
            img1: "../../../img/icon/wywc.png",
            name: "已完成"
        }, {
            img: "../../../img/icon/sh.png",
            img1: "../../../img/icon/wsh.png",
            name: "已关闭"
        }],
        open: !1,
        pagenum: 1,
        order_list: [],
        storelist: [],
        mygd: !1,
        jzgd: !0,
        selecttype: !1,
        typename: "选择类型",
        selectdate: !1,
        datetype: ["全部", "今天", "昨天", "近七天", "本月"],
        start: "",
        timestart: "",
        timeend: "",
        start_time: "",
        end_time: ""
    },
    reLoad: function() {
        var a = this,
            t = wx.getStorageSync("sjdsjid"),
            e = this.data.pagenum,
            i = this.data.typename;
        console.log(t, e, i), app.util.request({
            url: "entry/wxapp/lqNumberList",
            cachetime: "0",
            data: {
                typename: i,
                store_id: t,
                page: e,
                pagesize: 10
            },
            success: function(t) {
                console.log("分页返回的列表数据", t.data), t.data.length < 10 ? a.setData({
                    mygd: !0,
                    jzgd: !0
                }) : a.setData({
                    jzgd: !0,
                    pagenum: a.data.pagenum + 1
                });
                var e = a.data.storelist;
                e = function(t) {
                    for (var e = [], a = 0; a < t.length; a++) - 1 == e.indexOf(t[a]) && e.push(t[a]);
                    return e
                }(e = e.concat(t.data)), a.setData({
                    order_list: e,
                    storelist: e
                }), console.log(e)
            }
        })
    },
    onLoad: function(t) {
        var e = wx.getStorageSync("sjdsjid");
        console.log(e, t);
        var a = util.formatTime(new Date).substring(0, 10).replace(/\//g, "-");
        console.log(a.toString()), this.setData({
            typename: t.typename,
            start: a,
            timestart: a,
            timeend: a
        }), wx.setNavigationBarTitle({
            title: t.typename + "领取记录"
        }), this.reLoad(), app.setNavigationBarColor(this)
    },
    sc: function(t) {
        var e = this,
            a = t.currentTarget.dataset.id;
        console.log(a), wx.showModal({
            title: "提示",
            content: "确认删除此记录吗？",
            success: function(t) {
                t.confirm ? (console.log("用户点击确定"), app.util.request({
                    url: "entry/wxapp/DelNumberCode",
                    cachetime: "0",
                    data: {
                        id: a
                    },
                    success: function(t) {
                        console.log(t), 1 == t.data && (wx.showToast({
                            title: "操作成功",
                            duration: 1e3
                        }), setTimeout(function() {
                            e.setData({
                                pagenum: 1,
                                order_list: [],
                                storelist: [],
                                mygd: !1,
                                jzgd: !0
                            }), e.reLoad()
                        }, 1e3))
                    }
                })) : t.cancel && console.log("用户点击取消")
            }
        })
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        console.log("上拉加载", this.data.pagenum);
        !this.data.mygd && this.data.jzgd && (this.setData({
            jzgd: !1
        }), this.reLoad())
    }
});