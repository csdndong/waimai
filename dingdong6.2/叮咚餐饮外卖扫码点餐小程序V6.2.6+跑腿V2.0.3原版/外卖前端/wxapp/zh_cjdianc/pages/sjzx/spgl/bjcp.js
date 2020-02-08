/*   time:2019-07-18 01:03:18*/
var app = getApp(),
    util = require("../../../utils/util.js"),
    siteinfo = require("../../../../siteinfo.js");
Page({
    data: {
        isbj: !1,
        url1: "",
        logo: "../../../img/splogo.png",
        spfl: [],
        spflIndex: 0,
        spxx: ["外卖菜品", "店内菜品", "店内+外卖"],
        checkboxItems: [{
            name: "热销",
            value: "0"
        }, {
            name: "推荐",
            value: "1"
        }, {
            name: "新品",
            value: "2"
        }, {
            name: "招牌",
            value: "3"
        }],
        spxxIndex: 0,
        sjxj: ["上架", "下架"],
        sjxjIndex: 0,
        disabled: !1,
        sppx: "",
        spmc: "",
        cpkc: "",
        yxsl: "",
        dnjg: "",
        wmjg: "",
        bzfy: "",
        cpid: ""
    },
    spflChange: function(e) {
        console.log("spflChange 发生选择改变，携带值为", e.detail.value, this.data.spfl[e.detail.value].id), this.setData({
            spflIndex: e.detail.value
        })
    },
    spxxChange: function(e) {
        console.log("spxxChange 发生选择改变，携带值为", e.detail.value, this.data.spxx[e.detail.value]), this.setData({
            spxxIndex: e.detail.value
        })
    },
    switchChange: function(e) {
        console.log("switchChange 发生选择改变，携带值为", e.detail.value), this.setData({
            sjxjIndex: e.detail.value ? 0 : 1
        })
    },
    checkboxChange: function(e) {
        console.log("checkbox发生change事件，携带value值为：", e.detail.value);
        for (var a = this.data.checkboxItems, t = e.detail.value, s = 0, o = a.length; s < o; ++s) {
            a[s].checked = !1;
            for (var i = 0, n = t.length; i < n; ++i) if (a[s].value == t[i]) {
                a[s].checked = !0;
                break
            }
        }
        this.setData({
            checkboxItems: a
        })
    },
    onLoad: function(e) {
        app.setNavigationBarColor(this), console.log(e, this.data.cpid), null != e.cpid && (this.setData({
            cpid: e.cpid,
            isbj: !0
        }), app.util.request({
            url: "entry/wxapp/StoreDishesInfo",
            cachetime: "0",
            data: {
                id: e.cpid
            },
            success: function(t) {
                console.log("菜品信息", t);
                var e = s.data.checkboxItems;
                "1" == t.data.is_hot && (e[0].checked = !0), "1" == t.data.is_tj && (e[1].checked = !0), "1" == t.data.is_new && (e[2].checked = !0), "1" == t.data.is_zp && (e[3].checked = !0);
                var a = wx.getStorageSync("sjdsjid");
                app.util.request({
                    url: "entry/wxapp/GoodsType",
                    cachetime: "0",
                    data: {
                        store_id: a
                    },
                    success: function(e) {
                        console.log(e);
                        e.data;
                        for (var a = 0; a < e.data.length; a++) e.data[a].id == t.data.type_id && s.setData({
                            spflIndex: a
                        })
                    }
                }), s.setData({
                    logo: t.data.logo,
                    splogo: t.data.logo,
                    sppx: t.data.num,
                    spmc: t.data.name,
                    cpkc: t.data.inventory,
                    yxsl: t.data.sales,
                    dnjg: t.data.dn_money,
                    wmjg: t.data.money,
                    yj: t.data.money2,
                    bzfy: t.data.box_money,
                    xgfs: t.data.restrict_num,
                    qsfs: t.data.start_num,
                    spjj: t.data.content,
                    sjxjIndex: Number(t.data.is_show) - 1,
                    spxxIndex: Number(t.data.type) - 1,
                    checkboxItems: e
                })
            }
        }));
        var a = wx.getStorageSync("sjdsjid");
        console.log(a);
        var s = this;
        app.util.request({
            url: "entry/wxapp/GoodsType",
            cachetime: "0",
            data: {
                store_id: a
            },
            success: function(e) {
                console.log(e), s.setData({
                    spfl: e.data
                })
            }
        })
    },
    choose: function(e) {
        var t = this;
        console.log(siteinfo), wx.chooseImage({
            count: 1,
            sizeType: ["compressed"],
            sourceType: ["album", "camera"],
            success: function(e) {
                console.log(e.tempFilePaths);
                var a = e.tempFilePaths;
                wx.showToast({
                    icon: "loading",
                    title: "正在上传"
                }), wx.uploadFile({
                    url: siteinfo.siteroot + "?i=" + siteinfo.uniacid + "&c=entry&a=wxapp&do=upload&m=zh_cjdianc",
                    filePath: e.tempFilePaths[0],
                    name: "upfile",
                    success: function(e) {
                        console.log(e), t.setData({
                            splogo: e.data
                        }), 200 == e.statusCode ? t.setData({
                            url1: "",
                            logo: a
                        }) : wx.showModal({
                            title: "提示",
                            content: "上传失败",
                            showCancel: !1
                        })
                    },
                    fail: function(e) {
                        console.log(e), wx.showModal({
                            title: "提示",
                            content: "上传失败",
                            showCancel: !1
                        })
                    },
                    complete: function() {
                        wx.hideToast()
                    }
                })
            }
        })
    },
    formSubmit: function(e) {
        console.log("form发生了submit事件，携带数据为：", e.detail.value);
        var a = this,
            t = this.data.cpid,
            s = this.data.checkboxItems,
            o = s[0].checked ? 1 : 2,
            i = s[1].checked ? 1 : 2,
            n = s[2].checked ? 1 : 2,
            l = s[3].checked ? 1 : 2,
            d = wx.getStorageSync("sjdsjid"),
            c = e.detail.value.sppx,
            u = e.detail.value.spmc,
            p = e.detail.value.cpkc,
            h = e.detail.value.yxsl,
            x = e.detail.value.dnjg,
            r = e.detail.value.yj,
            g = e.detail.value.wmjg,
            m = e.detail.value.bzfy,
            f = e.detail.value.xgfs,
            v = e.detail.value.qsfs,
            w = e.detail.value.spjj,
            j = this.data.splogo,
            y = this.data.spfl[this.data.spflIndex].id,
            b = Number(this.data.spxxIndex) + 1,
            _ = Number(this.data.sjxjIndex) + 1;
        console.log(t, d, c, u, p, h, x, r, g, m, f, v, w, j, y, b, _, o, i, n, l);
        var k = "",
            I = !0;
        null == j ? k = "请上传商品图片！" : "" == u ? k = "请填写商品名称！" : "" == p ? k = "请填写商品库存！" : "" == x ? k = "请填写商品店内价格！" : "" == g ? k = "请填写商品外卖价格！" : (a.setData({
            disabled: !0
        }), I = !1, app.util.request({
            url: "entry/wxapp/AddStoreDishes",
            cachetime: "0",
            data: {
                store_id: d,
                id: t,
                num: c,
                name: u,
                type_id: y,
                type: b,
                inventory: p,
                sales: h,
                money: g,
                money2: r,
                dn_money: x,
                box_money: m,
                restrict_num: f,
                start_num: v,
                content: w,
                logo: j,
                is_show: _,
                is_hot: o,
                is_tj: i,
                is_new: n,
                is_zp: l
            },
            success: function(e) {
                console.log(e), 1 == e.data ? (wx.showToast({
                    title: "提交成功"
                }), setTimeout(function() {
                    wx.navigateBack({})
                }, 1e3)) : (a.setData({
                    disabled: !1
                }), wx.showToast({
                    title: "请修改后提交！",
                    icon: "loading"
                }))
            }
        })), 1 == I && wx.showModal({
            title: "提示",
            content: k
        })
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});