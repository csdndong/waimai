/*   time:2019-07-18 01:03:18*/
var dsq, app = getApp(),
    siteinfo = require("../../../../siteinfo.js");
Page({
    data: {
        navbar: [{
            name: "全部",
            id: ""
        }],
        selectedindex: 0,
        mask1Hidden: !0,
        img: "http://img1.imgtn.bdimg.com/it/u=4078366710,4168441355&fm=200&gp=0.jpg",
        status: 1,
        pagenum: 1,
        order_list: [],
        storelist: [],
        mygd: !1,
        jzgd: !0
    },
    onOverallTag: function(t) {
        console.log(t), this.setData({
            mask1Hidden: !1
        })
    },
    mask1Cancel: function() {
        this.setData({
            mask1Hidden: !0
        })
    },
    selectednavbar: function(t) {
        console.log(t), this.setData({
            pagenum: 1,
            order_list: [],
            storelist: [],
            mygd: !1,
            jzgd: !0,
            selectedindex: t.currentTarget.dataset.index,
            toView: "a" + (t.currentTarget.dataset.index - 1),
            status: Number(t.currentTarget.dataset.index) + 1
        }), this.reLoad()
    },
    reLoad: function() {
        var t, e = this,
            a = this.data.status || 1,
            n = wx.getStorageSync("sjdsjid"),
            s = this.data.pagenum;
        t = 1 == a ? "" : e.data.navbar[a - 1].id, console.log(a, t, n, s), app.util.request({
            url: "entry/wxapp/Table2",
            cachetime: "0",
            data: {
                type_id: t,
                store_id: n,
                page: s,
                pagesize: 20
            },
            success: function(t) {
                console.log("分页返回的列表数据", t.data), t.data.length < 20 ? e.setData({
                    mygd: !0,
                    jzgd: !0
                }) : e.setData({
                    jzgd: !0,
                    pagenum: e.data.pagenum + 1
                });
                var a = e.data.storelist;
                a = function(t) {
                    for (var a = [], e = 0; e < t.length; e++) - 1 == a.indexOf(t[e]) && a.push(t[e]);
                    return a
                }(a = a.concat(t.data)), e.setData({
                    order_list: a,
                    storelist: a
                }), console.log(a)
            }
        })
    },
    onLoad: function(t) {
        var e = this,
            a = wx.getStorageSync("sjdsjid"),
            n = siteinfo.siteroot.replace("app/index.php", "");
        console.log(a, wx.getStorageSync("system")), wx.setNavigationBarTitle({
            title: wx.getStorageSync("system").dc_name || "店内"
        }), app.setNavigationBarColor(this), app.sjdpageOnLoad(this), app.util.request({
            url: "entry/wxapp/TableType",
            cachetime: "0",
            data: {
                store_id: a
            },
            success: function(t) {
                var a = e.data.navbar.concat(t.data);
                console.log(t, a), e.setData({
                    navbar: a
                })
            }
        }), dsq = setInterval(function() {
            wx.getStorageSync("yybb") ? app.util.request({
                url: "entry/wxapp/NewOrder",
                cachetime: "0",
                data: {
                    store_id: a
                },
                success: function(t) {
                    console.log(t), 1 == t.data && wx.playBackgroundAudio({
                        dataUrl: n + "addons/zh_cjdianc/template/images/wm.wav",
                        title: "语音播报"
                    }), 2 == t.data && wx.playBackgroundAudio({
                        dataUrl: n + "addons/zh_cjdianc/template/images/dn.wav",
                        title: "语音播报"
                    })
                }
            }) : clearInterval(dsq)
        }, 1e4), this.reLoad()
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {
        clearInterval(dsq)
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        console.log("上拉加载", this.data.pagenum);
        !this.data.mygd && this.data.jzgd && (this.setData({
            jzgd: !1
        }), this.reLoad())
    }
});