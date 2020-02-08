/*   time:2019-07-18 01:07:49*/
var app = getApp(),
    util = require("../../utils/util.js");
Page({
    data: {
        isloading: !0,
        store_id: "1",
        navbar: ["店内", "评价", "详情"],
        selectedindex: 0,
        catalogSelect: 0,
        share_modal_active: !1,
        color: "",
        fwxy: !0,
        cpjzz: !0,
        spggtoggle: !0,
        yysjtoggle: !0,
        spxqtoggle: !0,
        gg: [],
        storeyyzz: [],
        pjselectedindex: 0,
        isytpj: !1,
        pagenum: 1,
        storelist: [],
        bfstorelist: [],
        mygd: !1,
        jzgd: !0,
        scroll: "scroll",
        iszk: !1
    },
    drdc: function() {
        var e = this,
            t = this.data.userinfo;
        console.log(t), "" == t.img || "" == t.name ? wx.navigateTo({
            url: "getdl"
        }) : wx.showModal({
            title: "提示",
            content: "确定多人选购拼单吗？",
            success: function(t) {
                t.confirm ? (console.log("用户点击确定"), wx.redirectTo({
                    url: "drdc?storeid=" + e.data.store_id + "&tableid=" + e.data.tableid + "&type_name=" + e.data.tableinfo.type_name + "&table_name=" + e.data.tableinfo.table_name
                })) : t.cancel && console.log("用户点击取消")
            }
        })
    },
    commentPicView: function(t) {
        console.log(t);
        var e = this.data.storelist,
            a = [],
            o = t.currentTarget.dataset.index,
            s = t.currentTarget.dataset.picindex,
            n = t.currentTarget.dataset.id;
        if (console.log(o, s, n), n == e[o].id) {
            var i = e[o].img;
            for (var d in i) a.push(this.data.url + i[d]);
            wx.previewImage({
                current: this.data.url + i[s],
                urls: a
            })
        }
    },
    ytpj: function() {
        var t = this.data.params;
        this.data.isytpj ? t.img = "" : t.img = "1", this.setData({
            pagenum: 1,
            storelist: [],
            bfstorelist: [],
            mygd: !1,
            jzgd: !0,
            isytpj: !this.data.isytpj,
            params: t
        }), this.getstorelist()
    },
    pjselectednavbar: function(t) {
        console.log(t);
        var e = this.data.params;
        0 == t.currentTarget.dataset.index && (e.type = "全部"), 1 == t.currentTarget.dataset.index && (e.type = "1"), 2 == t.currentTarget.dataset.index && (e.type = "2"), this.setData({
            pagenum: 1,
            storelist: [],
            bfstorelist: [],
            mygd: !1,
            jzgd: !0,
            pjselectedindex: t.currentTarget.dataset.index,
            params: e
        }), this.getstorelist()
    },
    getstorelist: function() {
        var o = this,
            s = o.data.pagenum;
        o.data.params.page = s, o.data.params.pagesize = 10, console.log(s, o.data.params), o.setData({
            isjzz: !0
        }), app.util.request({
            url: "entry/wxapp/AssessList",
            cachetime: "0",
            data: o.data.params,
            success: function(t) {
                console.log("分页返回的商家列表数据", t.data);
                var e = [{
                    name: "全部",
                    num: t.data.all
                }, {
                    name: "满意",
                    num: t.data.ok
                }, {
                    name: "不满意",
                    num: t.data.no
                }],
                    a = o.data.bfstorelist;
                a = function(t) {
                    for (var e = [], a = 0; a < t.length; a++) - 1 == e.indexOf(t[a]) && e.push(t[a]);
                    return e
                }(a = a.concat(t.data.assess)), o.setData({
                    storelist: a,
                    bfstorelist: a,
                    pjnavbar: e
                }), t.data.assess.length < 10 ? o.setData({
                    mygd: !0,
                    jzgd: !0,
                    isjzz: !1
                }) : o.setData({
                    jzgd: !0,
                    pagenum: s + 1,
                    isjzz: !1
                }), console.log(a)
            }
        })
    },
    Coupons: function() {
        var o = this,
            t = wx.getStorageSync("users").id,
            e = o.data.store_id;
        app.util.request({
            url: "entry/wxapp/Coupons",
            cachetime: "0",
            data: {
                store_id: e,
                user_id: t
            },
            success: function(t) {
                console.log(t.data);
                for (var e = [], a = 0; a < t.data.length; a++) "1" != t.data[a].type && e.push(t.data[a]);
                o.setData({
                    Coupons: e
                })
            }
        })
    },
    ljlq: function(t) {
        console.log(t.currentTarget.dataset.qid);
        var e = this,
            a = wx.getStorageSync("users").id;
        wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/LqCoupons",
            cachetime: "0",
            data: {
                user_id: a,
                coupon_id: t.currentTarget.dataset.qid
            },
            success: function(t) {
                console.log(t), "1" == t.data && (wx.showLoading({
                    title: "领取成功",
                    mask: !0
                }), setTimeout(function() {
                    e.Coupons()
                }, 1e3))
            }
        })
    },
    submit: function() {
        var t = this.data.userinfo;
        console.log(t), "" == t.img || "" == t.name ? wx.navigateTo({
            url: "getdl"
        }) : wx.navigateTo({
            url: "smdcform?storeid=" + this.data.store_id + "&tableid=" + this.data.tableid
        })
    },
    lookck: function() {
        this.setData({
            fwxy: !1
        })
    },
    queren: function() {
        this.setData({
            fwxy: !0
        })
    },
    spxqck: function(t) {
        var e = t.currentTarget.dataset.itemIndex,
            a = t.currentTarget.dataset.parentindex,
            o = this.data.dishes,
            s = this.data.cart_list.res,
            n = t.currentTarget.dataset.goodid,
            i = this.data.dishes[a].good[e],
            d = wx.getStorageSync("users").id,
            r = this.data.store_id;
        i.goodindex = e, i.catalogSelect = a, console.log(o, s, e, a, n, i, d, r), this.setData({
            spxqinfo: i,
            spxqtoggle: !1
        })
    },
    ckcd: function() {
        this.setData({
            yysjtoggle: !0
        })
    },
    gdsh: function() {
        wx.navigateBack({})
    },
    gbspxq: function() {
        this.setData({
            spxqtoggle: !0
        })
    },
    ggcartdec: function() {
        var e = this;
        wx.showModal({
            title: "提示",
            content: "多规格商品请在购物车中删除对应的规格商品！",
            success: function(t) {
                e.setData({
                    share_modal_active: !0
                })
            }
        })
    },
    gwcdec: function(t) {
        var o = this,
            s = this.data.dishes,
            n = t.currentTarget.dataset.goodid,
            e = Number(t.currentTarget.dataset.num) - 1,
            a = t.currentTarget.dataset.id;
        console.log(s, n, e, a), wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/UpdCar",
            cachetime: "0",
            data: {
                num: e,
                id: a
            },
            success: function(t) {
                if (console.log(t), "1" == t.data) {
                    for (var e = 0; e < s.length; e++) for (var a = 0; a < s[e].good.length; a++) s[e].good[a].id == n && s[e].good[a].quantity--;
                    o.setData({
                        dishes: s
                    }), o.gwcreload()
                }
                "超出库存!" == t.data && wx.showModal({
                    title: "提示",
                    content: "超出库存!"
                })
            }
        })
    },
    gwcadd: function(t) {
        var o = this,
            s = this.data.dishes,
            n = t.currentTarget.dataset.goodid,
            e = Number(t.currentTarget.dataset.num) + 1,
            a = t.currentTarget.dataset.id;
        console.log(s, n, e, a), wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/UpdCar",
            cachetime: "0",
            data: {
                num: e,
                id: a
            },
            success: function(t) {
                if (console.log(t), "1" == t.data) {
                    for (var e = 0; e < s.length; e++) for (var a = 0; a < s[e].good.length; a++) s[e].good[a].id == n && s[e].good[a].quantity++;
                    o.setData({
                        dishes: s
                    }), o.gwcreload()
                }
                "超出库存!" == t.data && wx.showModal({
                    title: "提示",
                    content: "超出库存!请选择其他商品"
                })
            }
        })
    },
    cartdec: function(t) {
        var e = t.currentTarget.dataset.itemIndex,
            a = t.currentTarget.dataset.parentindex,
            o = this.data.dishes,
            s = this.data.cart_list.res,
            n = t.currentTarget.dataset.goodid,
            i = this,
            d = this.data.dishes[a].good[e],
            r = wx.getStorageSync("users").id,
            c = this.data.store_id;
        console.log(o, s, e, a, n, d, r, c);
        for (var l = 0; l < s.length; l++) if (s[l].good_id == n) {
            var g = Number(s[l].num) - 1,
                u = s[l].id;
            console.log(s[l], g, u), wx.showLoading({
                title: "正在加载",
                mask: !0
            }), app.util.request({
                url: "entry/wxapp/UpdCar",
                cachetime: "0",
                data: {
                    num: g,
                    id: u
                },
                success: function(t) {
                    if (console.log(t), "1" == t.data) {
                        for (var e = 0; e < o.length; e++) for (var a = 0; a < o[e].good.length; a++) o[e].good[a].id == n && o[e].good[a].quantity--;
                        i.setData({
                            dishes: o
                        }), i.gwcreload()
                    }
                    "超出库存!" == t.data && wx.showModal({
                        title: "提示",
                        content: "超出库存!"
                    })
                }
            })
        }
    },
    cartadd: function(t) {
        var e = t.currentTarget.dataset.itemIndex,
            a = t.currentTarget.dataset.parentindex,
            o = this.data.dishes,
            s = t.currentTarget.dataset.goodid,
            n = this,
            i = this.data.dishes[a].good[e],
            d = wx.getStorageSync("users").id,
            r = this.data.store_id,
            c = "1" == getApp().xtxx.hygn && this.data.iszk && "0.00" != i.dn_hymoney ? 1 : 0;
        console.log(e, a, s, i, d, r), wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/AddCar",
            cachetime: "0",
            data: {
                money: c ? i.dn_hymoney : i.dn_money,
                good_id: s,
                store_id: r,
                user_id: d,
                num: 1,
                spec: "",
                combination_id: "",
                box_money: i.box_money,
                type: 2
            },
            success: function(t) {
                if (console.log(t), "1" == t.data) {
                    for (var e = 0; e < o.length; e++) for (var a = 0; a < o[e].good.length; a++) o[e].good[a].id == s && o[e].good[a].quantity++;
                    n.setData({
                        dishes: o
                    }), console.log(o), n.gwcreload()
                }
                "超出库存!" == t.data && wx.showModal({
                    title: "提示",
                    content: "库存不足!请重新选择"
                })
            }
        })
    },
    spggck: function(t) {
        var r = t.currentTarget.dataset.itemIndex,
            c = t.currentTarget.dataset.parentindex,
            l = t.currentTarget.dataset.goodid,
            g = this;
        console.log(r, c, l), app.util.request({
            url: "entry/wxapp/GoodInfo",
            cachetime: "0",
            data: {
                good_id: l
            },
            success: function(t) {
                console.log(t.data);
                var e = t.data.spec,
                    a = t.data.name;
                for (var o in e) for (var s in e[o].spec_val) e[o].spec_val[s].checked = 0 == s;
                g.setData({
                    gg: e,
                    spname: a
                }), console.log(e);
                var n = [],
                    i = !0;
                for (var o in e) {
                    var d = !1;
                    for (var s in e[o].spec_val) if (e[o].spec_val[s].checked) {
                        n.push(e[o].spec_val[s].spec_val_name), d = !0;
                        break
                    }
                    if (!d) {
                        i = !1;
                        break
                    }
                }
                console.log(l, n, n.toString()), i && (wx.showLoading({
                    title: "正在加载",
                    mask: !0
                }), app.util.request({
                    url: "entry/wxapp/GgZh",
                    cachetime: "0",
                    data: {
                        combination: n.toString(),
                        good_id: l
                    },
                    success: function(t) {
                        console.log(t), g.setData({
                            spggtoggle: !1,
                            gginfo: t.data,
                            itemIndex: r,
                            parentindex: c
                        })
                    }
                }))
            }
        })
    },
    attrClick: function(t) {
        var e = this,
            a = this.data.gginfo.good_id,
            o = t.target.dataset.groupId,
            s = t.target.dataset.id,
            n = e.data.gg;
        for (var i in console.log(o, s, n), n) if (n[i].spec_id == o) for (var d in n[i].spec_val) n[i].spec_val[d].spec_val_id == s ? n[i].spec_val[d].checked = !0 : n[i].spec_val[d].checked = !1;
        e.setData({
            gg: n
        });
        var r = [],
            c = !0;
        for (var i in n) {
            var l = !1;
            for (var d in n[i].spec_val) if (n[i].spec_val[d].checked) {
                r.push(n[i].spec_val[d].spec_val_name), l = !0;
                break
            }
            if (!l) {
                c = !1;
                break
            }
        }
        console.log(a, r, r.toString()), c && (wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/GgZh",
            cachetime: "0",
            data: {
                combination: r.toString(),
                good_id: a
            },
            success: function(t) {
                console.log(t), e.setData({
                    gginfo: t.data
                })
            }
        }))
    },
    ggaddcart: function() {
        var t = this.data.itemIndex,
            e = this.data.parentindex,
            o = this.data.dishes,
            s = this,
            n = this.data.gginfo,
            a = wx.getStorageSync("users").id,
            i = this.data.gg,
            d = this.data.store_id,
            r = [],
            c = !0;
        for (var l in i) {
            var g = !1;
            for (var u in i[l].spec_val) if (i[l].spec_val[u].checked) {
                r.push(i[l].spec_name + ":" + i[l].spec_val[u].spec_val_name), g = !0;
                break
            }
            if (!g) {
                c = !1;
                break
            }
        }
        console.log("加入购物车", t, e, o, n, a, d, i, r, r.toString()), c && (wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/AddCar",
            cachetime: "0",
            data: {
                money: n.dn_money,
                good_id: n.good_id,
                store_id: d,
                user_id: a,
                num: 1,
                spec: r.toString(),
                combination_id: n.id,
                box_money: n.box_money,
                type: 2
            },
            success: function(t) {
                if (console.log(t), "1" == t.data) {
                    for (var e = 0; e < o.length; e++) for (var a = 0; a < o[e].good.length; a++) o[e].good[a].id == n.good_id && o[e].good[a].quantity++;
                    s.setData({
                        dishes: o
                    }), s.gwcreload(), s.setData({
                        spggtoggle: !0
                    })
                }
                "超出库存!" == t.data && wx.showModal({
                    title: "提示",
                    content: "暂无库存!请选择其他规格或商品"
                })
            }
        }))
    },
    gwcreload: function() {
        var e = this.data.dishes,
            a = this,
            t = wx.getStorageSync("users").id,
            o = this.data.store_id;
        console.log(e, t, o), app.util.request({
            url: "entry/wxapp/MyCar",
            cachetime: "0",
            data: {
                store_id: o,
                user_id: t,
                type: 2
            },
            success: function(t) {
                console.log(t), console.log(e), a.setData({
                    cart_list: t.data
                }), a.subText()
            }
        })
    },
    gbspgg: function() {
        this.setData({
            spggtoggle: !0
        })
    },
    gbyysj: function() {
        this.setData({
            yysjtoggle: !0
        })
    },
    selectednavbar: function(t) {
        console.log(t), this.setData({
            selectedindex: t.currentTarget.dataset.index
        })
    },
    scroll: function(t) {
        console.log(t);
        this.data.dishes;
        var e = this.data.dataheith,
            a = this.data.catalogSelect;
        console.log(t.detail.scrollTop, e, a);
        for (var o = t.detail.scrollTop, s = 0; s < e.length; s++) if (o <= e[s]) {
            console.log(s), console.log(o, a), this.setData({
                catalogSelect: s,
                toType: "type" + (s - 2)
            });
            break
        }
    },
    selectMenu: function(t) {
        var e = this.data.dishes,
            a = this,
            o = wx.getStorageSync("users").id,
            s = a.data.store_id,
            n = t.currentTarget.dataset.itemIndex;
        console.log(e, o, s, n), this.setData({
            catalogSelect: t.currentTarget.dataset.itemIndex,
            toView: "order" + n.toString(),
            toType: "type" + (n - 2),
            scroll: ""
        }), setTimeout(function() {
            a.setData({
                scroll: "scroll"
            })
        }, 500)
    },
    swiperChange: function(t) {
        console.log(t), this.setData({
            selectedindex: t.detail.current
        })
    },
    showcart: function() {
        this.setData({
            share_modal_active: !this.data.share_modal_active
        })
    },
    closecart: function() {
        this.setData({
            share_modal_active: !1
        })
    },
    clear: function() {
        var o = this,
            s = this.data.dishes,
            e = wx.getStorageSync("users").id,
            a = o.data.store_id;
        console.log(s, e, a), wx.showModal({
            title: "提示",
            content: "确定清空此商家的购物车吗？",
            success: function(t) {
                t.confirm ? (console.log("用户点击确定"), wx.showLoading({
                    title: "正在加载",
                    mask: !0
                }), app.util.request({
                    url: "entry/wxapp/DelCar",
                    cachetime: "0",
                    data: {
                        user_id: e,
                        store_id: a,
                        type: 2
                    },
                    success: function(t) {
                        if (console.log(t.data), "1" == t.data) {
                            for (var e = 0; e < s.length; e++) for (var a = 0; a < s[e].good.length; a++) s[e].good[a].quantity = 0;
                            o.setData({
                                dishes: s,
                                share_modal_active: !1
                            }), o.gwcreload()
                        }
                    }
                })) : t.cancel && console.log("用户点击取消")
            }
        })
    },
    subText: function() {
        console.log(this.data);
        var t, e = parseFloat(this.data.cart_list.money),
            a = parseFloat(this.data.start_at);
        console.log(e, a), e <= 0 ? t = "还没有选购商品" : (console.log(e), t = "去结算"), this.setData({
            subtext: t
        })
    },
    hjfwy: function() {
        var e = this.data.store_id,
            a = this.data.tableid;
        console.log(e, a), wx.showModal({
            title: "提示",
            content: "确定呼叫服务员吗？",
            success: function(t) {
                t.confirm ? (console.log("用户点击确定"), app.util.request({
                    url: "entry/wxapp/VoiceCall",
                    cachetime: "0",
                    data: {
                        store_id: e,
                        id: a
                    },
                    success: function(t) {
                        console.log(t.data), wx.showModal({
                            title: "提示",
                            content: "成功呼叫服务员,系统存在网络延迟,请您不要重复点击呼叫"
                        })
                    }
                })) : t.cancel && console.log("用户点击取消")
            }
        })
    },
    wddd: function() {
        wx.navigateTo({
            url: "../wddd/order"
        })
    },
    addsp: function() {
        var e = this.data.oid,
            a = [],
            t = this.data.cart_list.res,
            o = this.data.cart_list.money;
        t.map(function(t) {
            if (0 < t.num) {
                var e = {};
                e.name = t.name, e.img = t.logo, e.num = t.num, e.money = t.money, e.dishes_id = t.good_id, e.spec = t.spec, a.push(e)
            }
        }), console.log(e, o, t, a), wx.showModal({
            title: "提示",
            content: "确定添加商品吗？",
            success: function(t) {
                t.confirm ? (console.log("用户点击确定"), wx.showLoading({
                    title: "加载中",
                    mask: !0
                }), app.util.request({
                    url: "entry/wxapp/Addgoods",
                    cachetime: "0",
                    data: {
                        sz: a,
                        money: o,
                        order_id: e
                    },
                    success: function(t) {
                        console.log(t.data), "1" == t.data && (wx.showLoading({
                            title: "添加成功",
                            mask: !0
                        }), setTimeout(function() {
                            wx.reLaunch({
                                url: "../wddd/order"
                            })
                        }, 1e3))
                    }
                })) : t.cancel && console.log("用户点击取消")
            }
        })
    },
    onLoad: function(t) {
        console.log("options", t, t.store_id);
        var e = decodeURIComponent(t.scene).split(",");
        console.log(decodeURIComponent(t.scene), e), app.setNavigationBarColor(this), "undefined" != decodeURIComponent(t.scene) && this.setData({
            store_id: e[1],
            tableid: e[0]
        }), null != t.store_id && this.setData({
            store_id: t.store_id,
            tableid: t.tableid,
            oid: t.oid
        });
        var l = this,
            a = l.data.store_id,
            g = l.data.tableid;
        this.setData({
            params: {
                store_id: a,
                type: "全部",
                img: ""
            }
        }), this.getstorelist();
        var u = util.formatTime(new Date).slice(11, 16);
        app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: a,
                type: 1
            },
            success: function(t) {
                console.log(t.data);
                var e = t.data;
                app.util.request({
                    url: "entry/wxapp/Zhuohao",
                    cachetime: "0",
                    data: {
                        id: g
                    },
                    success: function(t) {
                        console.log(t), l.setData({
                            tableinfo: t.data
                        }), "1" == e.storeset.is_czztpd ? "0" == t.data.status ? l.setData({
                            iskt: !1
                        }) : l.setData({
                            iskt: !0
                        }) : l.setData({
                            iskt: !1
                        })
                    }
                });
                var a = t.data.store.time,
                    o = t.data.store.time2,
                    s = t.data.store.time3,
                    n = t.data.store.time4,
                    i = t.data.store.is_rest;
                console.log("当前的系统时间为" + u), console.log("商家的营业时间从" + a + "至" + o, s + "至" + n), 1 == i ? (l.setData({
                    yysjtoggle: !1
                }), console.log("商家正在休息" + i)) : console.log("商家正在营业" + i), a < n ? a < u && u < o || s < u && u < n || s < u && n < s ? (console.log("商家正常营业"), l.setData({
                    time: 1
                })) : u < a || o < u && u < s ? (console.log("商家还没开店呐，稍等一会儿可以吗？"), l.setData({
                    time: 2,
                    yysjtoggle: !1
                })) : n < u && (console.log("商家以及关店啦，明天再来吧"), l.setData({
                    time: 3,
                    yysjtoggle: !1
                })) : n < a && (a < u && u < o || s < u && n < u || u < s && u < n ? (console.log("商家正常营业"), l.setData({
                    time: 1
                })) : u < a || o < u && u < s ? (console.log("商家还没开店呐，稍等一会儿可以吗？"), l.setData({
                    time: 2,
                    yysjtoggle: !1
                })) : n < u && (console.log("商家以及关店啦，明天再来吧"), l.setData({
                    time: 3,
                    yysjtoggle: !1
                })));
                for (var d = 0; d < t.data.store.environment.length; d++) t.data.store.environment[d] = l.data.url + t.data.store.environment[d];
                for (var r = 0; r < t.data.store.yyzz.length; r++) t.data.store.yyzz[r] = l.data.url + t.data.store.yyzz[r];
                "" != t.data.storeset.dn_name && (wx.setNavigationBarTitle({
                    title: t.data.storeset.dn_name
                }), l.setData({
                    navbar: [t.data.storeset.dn_name, "评价", "详情"]
                }));
                var c = t.data.store.tel;
                l.setData({
                    psf: t.data.psf,
                    reduction: t.data.reduction,
                    store: t.data.store,
                    storeset: t.data.storeset,
                    start_at: 0,
                    xtxx: getApp().xtxx,
                    paytel: "1" == getApp().xtxx.is_pay && 0 < Number(getApp().xtxx.pay_money) ? c.substring(0, 3) + "****" + c.substring(c.length - 4) : c
                })
            }
        }), app.getUserInfo(function(t) {
            app.util.request({
                url: "entry/wxapp/DelCar",
                cachetime: "0",
                data: {
                    user_id: t.id,
                    store_id: l.data.store_id,
                    type: 2
                },
                success: function(t) {
                    console.log(t.data)
                }
            });
            var e = util.formatTime(new Date).substring(0, 10).replace(/\//g, "-");
            console.log(t, e), l.setData({
                userinfo: t
            }), "" != t.dq_time && t.dq_time >= e.toString() && l.setData({
                iszk: !0
            });
            var r = wx.getStorageSync("users").id,
                c = l.data.store_id;
            console.log("uid", r), l.Coupons(), app.util.request({
                url: "entry/wxapp/Hot",
                cachetime: "0",
                data: {
                    store_id: c,
                    type: 1
                },
                success: function(t) {
                    console.log(t.data);
                    for (var e = 0; e < t.data.length; e++) t.data[e].quantity = Number(t.data[e].quantity);
                    if (0 < t.data.length) {
                        var d = new Array,
                            a = new Object;
                        a.good = t.data, a.type_name = "热销", a.id = "0", d.push(a), app.util.request({
                            url: "entry/wxapp/DishesList",
                            cachetime: "0",
                            data: {
                                store_id: c,
                                type: 1
                            },
                            success: function(t) {
                                console.log(t.data);
                                for (var n = d.concat(t.data), e = 0; e < n.length; e++) for (var a = 0; a < n[e].good.length; a++) n[e].good[a].quantity = Number(n[e].good[a].quantity);
                                console.log(n);
                                for (var o = 0, s = [], i = 0; i < n.length; i++) o += 105 * n[i].good.length, s.push(o);
                                console.log(n), l.setData({
                                    cpjzz: !1,
                                    dataheith: s
                                }), app.util.request({
                                    url: "entry/wxapp/MyCar",
                                    cachetime: "0",
                                    data: {
                                        store_id: c,
                                        user_id: r,
                                        type: 2
                                    },
                                    success: function(t) {
                                        console.log(t);
                                        for (var e = t.data.res, a = 0; a < e.length; a++) for (var o = 0; o < n.length; o++) for (var s = 0; s < n[o].good.length; s++) e[a].good_id == n[o].good[s].id && (n[o].good[s].quantity = n[o].good[s].quantity + Number(e[a].num));
                                        console.log(n), l.setData({
                                            cart_list: t.data,
                                            dishes: n,
                                            isloading: !1
                                        }), l.subText()
                                    }
                                })
                            }
                        })
                    } else app.util.request({
                        url: "entry/wxapp/DishesList",
                        cachetime: "0",
                        data: {
                            store_id: c,
                            type: 1
                        },
                        success: function(t) {
                            console.log(t.data);
                            for (var n = t.data, e = 0; e < n.length; e++) for (var a = 0; a < n[e].good.length; a++) n[e].good[a].quantity = Number(n[e].good[a].quantity);
                            console.log(n);
                            for (var o = 0, s = [], i = 0; i < n.length; i++) o += 105 * n[i].good.length, s.push(o);
                            console.log(n), l.setData({
                                cpjzz: !1,
                                dataheith: s
                            }), app.util.request({
                                url: "entry/wxapp/MyCar",
                                cachetime: "0",
                                data: {
                                    store_id: c,
                                    user_id: r,
                                    type: 2
                                },
                                success: function(t) {
                                    console.log(t);
                                    for (var e = t.data.res, a = 0; a < e.length; a++) for (var o = 0; o < n.length; o++) for (var s = 0; s < n[o].good.length; s++) e[a].good_id == n[o].good[s].id && (n[o].good[s].quantity = n[o].good[s].quantity + Number(e[a].num));
                                    console.log(n), l.setData({
                                        cart_list: t.data,
                                        dishes: n,
                                        isloading: !1
                                    }), l.subText()
                                }
                            })
                        }
                    })
                }
            })
        }), wx.getSystemInfo({
            success: function(t) {
                console.log(t), l.setData({
                    height: t.windowHeight - 145
                })
            }
        })
    },
    maketel: function(t) {
        var e = this,
            a = this.data.store.tel,
            o = this.data.paytel;
        console.log(o), "-1" != o.indexOf("****") ? wx.showModal({
            title: "提示",
            content: "查看电话需付费" + getApp().xtxx.pay_money + "元",
            success: function(t) {
                t.confirm && (console.log("用户点击确定"), app.util.request({
                    url: "entry/wxapp/telPay",
                    cachetime: "0",
                    data: {
                        openid: e.data.userinfo.openid,
                        pay_money: getApp().xtxx.pay_money
                    },
                    success: function(t) {
                        wx.requestPayment({
                            timeStamp: t.data.timeStamp,
                            nonceStr: t.data.nonceStr,
                            package: t.data.package,
                            signType: t.data.signType,
                            paySign: t.data.paySign,
                            success: function(t) {
                                console.log(t)
                            },
                            complete: function(t) {
                                console.log(t), "requestPayment:fail cancel" == t.errMsg && wx.showToast({
                                    title: "取消支付"
                                }), "requestPayment:ok" == t.errMsg && (wx.showToast({
                                    title: "支付成功",
                                    duration: 1e3
                                }), e.setData({
                                    paytel: a
                                }))
                            }
                        })
                    }
                }))
            }
        }) : (wx.makePhoneCall({
            phoneNumber: a
        }), e.setData({
            paytel: a
        }))
    },
    location: function() {
        var t = this.data.store.coordinates.split(","),
            e = this.data.store;
        console.log(t), wx.openLocation({
            latitude: parseFloat(t[0]),
            longitude: parseFloat(t[1]),
            address: e.address,
            name: e.name
        })
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    pjmore: function() {
        console.log("上拉加载", this.data.pagenum);
        this.data.mygd || !this.data.jzgd || this.data.isjzz || (this.setData({
            jzgd: !1
        }), this.getstorelist())
    }
});