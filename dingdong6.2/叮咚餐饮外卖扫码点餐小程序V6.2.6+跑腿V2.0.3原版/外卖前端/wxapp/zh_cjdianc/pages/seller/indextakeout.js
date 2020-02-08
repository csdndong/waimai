/*   time:2019-07-18 01:07:50*/
var qqmapsdk, app = getApp(),
    util = require("../../utils/util.js"),
    QQMapWX = require("../../utils/qqmap-wx-jssdk.js");
Page({
    data: {
        isloading: !0,
        store_id: "1",
        navbar: ["外卖", "评价", "详情"],
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
        loadMore: !0,
        loadindex: 1,
        scroll: "scroll",
        iszk: !1
    },
    closehbtoggle: function() {
        this.setData({
            hbtoggle: !1
        })
    },
    tozd: function(t) {
        this.setData({
            iszd: !1
        })
    },
    previewzzImage: function(t) {
        var a = t.currentTarget.dataset.urls,
            e = t.currentTarget.id;
        wx.previewImage({
            current: e,
            urls: a
        })
    },
    previewhjImage: function(t) {
        var a = t.currentTarget.dataset.urls,
            e = t.currentTarget.id;
        wx.previewImage({
            current: e,
            urls: a
        })
    },
    cartaddformSubmit: function(t) {
        console.log("formid", t.detail.formId);
        var a = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/AddFormId",
            cachetime: "0",
            data: {
                user_id: a,
                form_id: t.detail.formId
            },
            success: function(t) {
                console.log(t.data)
            }
        })
    },
    commentPicView: function(t) {
        console.log(t);
        var a = this.data.storelist,
            e = [],
            o = t.currentTarget.dataset.index,
            s = t.currentTarget.dataset.picindex,
            i = t.currentTarget.dataset.id;
        if (console.log(o, s, i), i == a[o].id) {
            var n = a[o].img;
            for (var r in n) e.push(this.data.url + n[r]);
            wx.previewImage({
                current: this.data.url + n[s],
                urls: e
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
        var a = this.data.params;
        0 == t.currentTarget.dataset.index && (a.type = "全部"), 1 == t.currentTarget.dataset.index && (a.type = "1"), 2 == t.currentTarget.dataset.index && (a.type = "2"), this.setData({
            pagenum: 1,
            storelist: [],
            bfstorelist: [],
            mygd: !1,
            jzgd: !0,
            pjselectedindex: t.currentTarget.dataset.index,
            params: a
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
                var a = [{
                    name: "全部",
                    num: t.data.all
                }, {
                    name: "满意",
                    num: t.data.ok
                }, {
                    name: "不满意",
                    num: t.data.no
                }],
                    e = o.data.bfstorelist;
                e = function(t) {
                    for (var a = [], e = 0; e < t.length; e++) - 1 == a.indexOf(t[e]) && a.push(t[e]);
                    return a
                }(e = e.concat(t.data.assess)), o.setData({
                    storelist: e,
                    bfstorelist: e,
                    pjnavbar: a
                }), t.data.assess.length < 10 ? o.setData({
                    mygd: !0,
                    jzgd: !0,
                    isjzz: !1
                }) : o.setData({
                    jzgd: !0,
                    pagenum: s + 1,
                    isjzz: !1
                }), console.log(e)
            }
        })
    },
    Coupons: function() {
        var s = this,
            t = wx.getStorageSync("users").id,
            a = s.data.store_id;
        app.util.request({
            url: "entry/wxapp/Coupons",
            cachetime: "0",
            data: {
                store_id: a,
                user_id: t
            },
            success: function(t) {
                console.log(t.data);
                for (var a = [], e = [], o = 0; o < t.data.length; o++) "2" != t.data[o].type && "0" != t.data[o].stock && a.push(t.data[o]), "2" == t.data[o].state && "2" != t.data[o].type && "0" != t.data[o].stock && e.push(t.data[o]);
                s.setData({
                    Coupons: a,
                    wlqyhq: e,
                    hbtoggle: 0 < e.length
                }), console.log(e)
            }
        })
    },
    ljlq: function(t) {
        console.log(t.currentTarget.dataset.qid);
        var a = this,
            e = wx.getStorageSync("users").id;
        wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/LqCoupons",
            cachetime: "0",
            data: {
                user_id: e,
                coupon_id: t.currentTarget.dataset.qid
            },
            success: function(t) {
                console.log(t), "1" == t.data && (wx.showLoading({
                    title: "领取成功",
                    mask: !0
                }), setTimeout(function() {
                    a.Coupons()
                }, 1e3))
            }
        })
    },
    submit: function() {
        var t = this.data.userinfo;
        console.log(t), "" == t.img || "" == t.name ? wx.navigateTo({
            url: "../smdc/getdl"
        }) : wx.navigateTo({
            url: "../takeout/takeoutform?storeid=" + this.data.store_id
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
        var a = t.currentTarget.dataset.itemIndex,
            e = t.currentTarget.dataset.parentindex,
            o = (this.data.dishes, this.data.cart_list.res, t.currentTarget.dataset.goodid, this.data.dishes[e].good[a]);
        wx.getStorageSync("users").id, this.data.store_id;
        o.goodindex = a, o.catalogSelect = e, this.setData({
            spxqinfo: o,
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
        var a = this;
        wx.showModal({
            title: "提示",
            content: "多规格商品请在购物车中删除对应的规格商品！",
            success: function(t) {
                a.setData({
                    share_modal_active: !0
                })
            }
        })
    },
    gwcdec: function(t) {
        for (var o = this, s = this.data.dishes, i = t.currentTarget.dataset.goodid, a = t.currentTarget.dataset.id, e = 0; e < s.length; e++) for (var n = 0; n < s[e].good.length; n++) if (s[e].good[n].id == i) {
            console.log(s[e].good[n]);
            var r = 1;
            Number(s[e].good[n].start_num) == Number(t.currentTarget.dataset.num) && (r = Number(s[e].good[n].start_num));
            var d = Number(t.currentTarget.dataset.num) - r;
            console.log(d, r), wx.showLoading({
                title: "正在加载",
                mask: !0
            }), app.util.request({
                url: "entry/wxapp/UpdCar",
                cachetime: "0",
                data: {
                    num: d,
                    id: a
                },
                success: function(t) {
                    if (console.log(t), "1" == t.data) {
                        for (var a = 0; a < s.length; a++) for (var e = 0; e < s[a].good.length; e++) s[a].good[e].id == i && (s[a].good[e].quantity = s[a].good[e].quantity - r);
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
        }
    },
    gwcadd: function(t) {
        var o = this,
            s = this.data.dishes,
            i = t.currentTarget.dataset.goodid,
            a = Number(t.currentTarget.dataset.num) + 1,
            e = t.currentTarget.dataset.id;
        wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/UpdCar",
            cachetime: "0",
            data: {
                num: a,
                id: e
            },
            success: function(t) {
                if (console.log(t), "1" == t.data) {
                    for (var a = 0; a < s.length; a++) for (var e = 0; e < s[a].good.length; e++) s[a].good[e].id == i && s[a].good[e].quantity++;
                    o.setData({
                        dishes: s
                    }), o.gwcreload()
                } else "超出库存!" == t.data ? wx.showModal({
                    title: "提示",
                    content: "库存不足!请重新选择"
                }) : "超出购买限制!" == t.data && wx.showModal({
                    title: "提示",
                    content: "超出购买限制!"
                })
            }
        })
    },
    cartdec: function(t) {
        for (var a = t.currentTarget.dataset.itemIndex, e = t.currentTarget.dataset.parentindex, o = this.data.dishes, s = this.data.cart_list.res, i = t.currentTarget.dataset.goodid, n = this, r = this.data.dishes[e].good[a], d = (wx.getStorageSync("users").id, this.data.store_id, 0); d < s.length; d++) if (s[d].good_id == i) {
            var c = 1;
            Number(r.start_num) == Number(s[d].num) && (c = Number(r.start_num));
            var l = Number(s[d].num) - c,
                g = s[d].id;
            console.log(s[d], l, g), wx.showLoading({
                title: "正在加载",
                mask: !0
            }), app.util.request({
                url: "entry/wxapp/UpdCar",
                cachetime: "0",
                data: {
                    num: l,
                    id: g
                },
                success: function(t) {
                    if (console.log(t), "1" == t.data) {
                        for (var a = 0; a < o.length; a++) for (var e = 0; e < o[a].good.length; e++) o[a].good[e].id == i && (o[a].good[e].quantity = o[a].good[e].quantity - c);
                        n.setData({
                            dishes: o
                        }), n.gwcreload()
                    }
                    "超出库存!" == t.data && wx.showModal({
                        title: "提示",
                        content: "超出库存!"
                    })
                }
            })
        }
    },
    isInArray: function(t, a) {
        for (var e = 0; e < t.length; e++) if (a === t[e].good_id) return !0;
        return !1
    },
    cartadd: function(t) {
        var a = t.currentTarget.dataset.itemIndex,
            e = t.currentTarget.dataset.parentindex,
            o = this.data.dishes,
            s = this.data.cart_list.res,
            i = t.currentTarget.dataset.goodid,
            n = this,
            r = this.data.dishes[e].good[a],
            d = wx.getStorageSync("users").id,
            c = this.data.store_id,
            l = "1" == getApp().xtxx.hygn && this.data.iszk && "0.00" != r.vip_money ? 1 : 0,
            g = 1;
        "0" == r.start_num || n.isInArray(s, i) || (g = Number(r.start_num)), console.log(g), wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/AddCar",
            cachetime: "0",
            data: {
                money: l ? r.vip_money : r.money,
                good_id: i,
                store_id: c,
                user_id: d,
                num: g,
                spec: "",
                combination_id: "",
                box_money: r.box_money
            },
            success: function(t) {
                if (console.log(t), "1" == t.data) {
                    for (var a = 0; a < o.length; a++) for (var e = 0; e < o[a].good.length; e++) o[a].good[e].id == i && (o[a].good[e].quantity = g + o[a].good[e].quantity);
                    n.setData({
                        dishes: o
                    }), n.gwcreload()
                } else "超出库存!" == t.data ? wx.showModal({
                    title: "提示",
                    content: "库存不足!请重新选择"
                }) : "超出购买限制!" == t.data && wx.showModal({
                    title: "提示",
                    content: "超出购买限制!"
                })
            }
        })
    },
    spggck: function(t) {
        var d = t.currentTarget.dataset.itemIndex,
            c = t.currentTarget.dataset.parentindex,
            l = t.currentTarget.dataset.goodid,
            g = this;
        console.log(d, c, l), app.util.request({
            url: "entry/wxapp/GoodInfo",
            cachetime: "0",
            data: {
                good_id: l
            },
            success: function(t) {
                console.log(t.data);
                var a = t.data.spec,
                    e = t.data.name;
                for (var o in a) for (var s in a[o].spec_val) a[o].spec_val[s].checked = 0 == s;
                g.setData({
                    gg: a,
                    spname: e
                }), console.log(a);
                var i = [],
                    n = !0;
                for (var o in a) {
                    var r = !1;
                    for (var s in a[o].spec_val) if (a[o].spec_val[s].checked) {
                        i.push(a[o].spec_val[s].spec_val_name), r = !0;
                        break
                    }
                    if (!r) {
                        n = !1;
                        break
                    }
                }
                console.log(l, i, i.toString()), n && (wx.showLoading({
                    title: "正在加载",
                    mask: !0
                }), app.util.request({
                    url: "entry/wxapp/GgZh",
                    cachetime: "0",
                    data: {
                        combination: i.toString(),
                        good_id: l
                    },
                    success: function(t) {
                        console.log(t), g.setData({
                            spggtoggle: !1,
                            gginfo: t.data,
                            itemIndex: d,
                            parentindex: c
                        })
                    }
                }))
            }
        })
    },
    attrClick: function(t) {
        var a = this,
            e = this.data.gginfo.good_id,
            o = t.target.dataset.groupId,
            s = t.target.dataset.id,
            i = a.data.gg;
        for (var n in console.log(o, s, i), i) if (i[n].spec_id == o) for (var r in i[n].spec_val) i[n].spec_val[r].spec_val_id == s ? i[n].spec_val[r].checked = !0 : i[n].spec_val[r].checked = !1;
        a.setData({
            gg: i
        });
        var d = [],
            c = !0;
        for (var n in i) {
            var l = !1;
            for (var r in i[n].spec_val) if (i[n].spec_val[r].checked) {
                d.push(i[n].spec_val[r].spec_val_name), l = !0;
                break
            }
            if (!l) {
                c = !1;
                break
            }
        }
        console.log(e, d, d.toString()), c && (wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/GgZh",
            cachetime: "0",
            data: {
                combination: d.toString(),
                good_id: e
            },
            success: function(t) {
                console.log(t), a.setData({
                    gginfo: t.data
                })
            }
        }))
    },
    ggaddcart: function() {
        this.data.itemIndex, this.data.parentindex;
        var o = this.data.dishes,
            s = this,
            i = this.data.gginfo,
            t = wx.getStorageSync("users").id,
            a = this.data.gg,
            e = this.data.store_id,
            n = [],
            r = !0;
        for (var d in a) {
            var c = !1;
            for (var l in a[d].spec_val) if (a[d].spec_val[l].checked) {
                n.push(a[d].spec_name + ":" + a[d].spec_val[l].spec_val_name), c = !0;
                break
            }
            if (!c) {
                r = !1;
                break
            }
        }
        r && (wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/AddCar",
            cachetime: "0",
            data: {
                money: i.wm_money,
                good_id: i.good_id,
                store_id: e,
                user_id: t,
                num: 1,
                spec: n.toString(),
                combination_id: i.id,
                box_money: i.box_money
            },
            success: function(t) {
                if (console.log(t), "1" == t.data) {
                    for (var a = 0; a < o.length; a++) for (var e = 0; e < o[a].good.length; e++) o[a].good[e].id == i.good_id && o[a].good[e].quantity++;
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
        this.data.dishes;
        var a = this,
            t = wx.getStorageSync("users").id,
            e = this.data.store_id;
        app.util.request({
            url: "entry/wxapp/MyCar",
            cachetime: "0",
            data: {
                store_id: e,
                user_id: t
            },
            success: function(t) {
                console.log(t), a.setData({
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
    scrolltolower: function() {
        var s = this.data.dishes,
            i = this,
            e = wx.getStorageSync("users").id,
            o = i.data.store_id,
            n = this.data.loadindex;
        if (console.log(s, e, o, n), i.setData({
            loadMore: !1
        }), n < s.length && 0 == s[n].good.length) {
            var t = s[n].id;
            console.log("还没加载过数据", t), i.setData({
                cpjzz: !0
            }), app.util.request({
                url: "entry/wxapp/Dishes",
                cachetime: "0",
                data: {
                    type_id: t,
                    type: 2
                },
                success: function(t) {
                    console.log(t.data);
                    for (var a = 0; a < t.data.length; a++) t.data[a].quantity = Number(t.data[a].quantity);
                    s[n].good = t.data, app.util.request({
                        url: "entry/wxapp/MyCar",
                        cachetime: "0",
                        data: {
                            store_id: o,
                            user_id: e
                        },
                        success: function(t) {
                            console.log(t);
                            for (var a = t.data.res, e = 0; e < a.length; e++) for (var o = 0; o < s[n].good.length; o++) a[e].good_id == s[n].good[o].id && (s[n].good[o].quantity = s[n].good[o].quantity + Number(a[e].num));
                            i.setData({
                                dishes: s,
                                loadindex: n + 1,
                                loadMore: !0
                            }), n == s.length - 1 && (console.log("alldie"), i.setData({
                                cpjzz: !1
                            }))
                        }
                    })
                }
            })
        } else console.log("alldie"), i.setData({
            cpjzz: !1
        })
    },
    scroll: function(t) {
        60 < t.detail.scrollTop && !this.data.iszd && "2" == this.data.storeset.top_style && (wx.pageScrollTo({
            scrollTop: this.data.navzdoffsetTop
        }), this.setData({
            iszd: !0
        }));
        this.data.dishes;
        for (var a = this.data.dataheith, e = (this.data.catalogSelect, t.detail.scrollTop), o = 0; o < a.length; o++) if (e <= a[o]) {
            this.setData({
                catalogSelect: o,
                toType: "type" + (o - 2)
            });
            break
        }
    },
    selectMenu: function(t) {
        this.data.dishes;
        var a = this,
            e = (wx.getStorageSync("users").id, a.data.store_id, t.currentTarget.dataset.itemIndex);
        this.setData({
            catalogSelect: t.currentTarget.dataset.itemIndex,
            toView: "order" + e.toString(),
            toType: "type" + (e - 2),
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
            a = wx.getStorageSync("users").id,
            e = o.data.store_id;
        console.log(s, a, e), wx.showModal({
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
                        user_id: a,
                        store_id: e
                    },
                    success: function(t) {
                        if (console.log(t.data), "1" == t.data) {
                            for (var a = 0; a < s.length; a++) for (var e = 0; e < s[a].good.length; e++) s[a].good[e].quantity = 0;
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
        var t, a = parseFloat(this.data.cart_list.money),
            e = parseFloat(this.data.start_at);
        if (console.log(a, e), a <= 0) t = "￥" + this.data.start_at + "元起送", null == this.data.start_at && (t = "请选择商品");
        else if (a < e) {
            var o = e - a;
            console.log(o), t = "还差" + o.toFixed(2) + "元起送"
        } else console.log(a), t = "去结算";
        this.setData({
            subtext: t
        })
    },
    onLoad: function(t) {
        qqmapsdk = new QQMapWX({
            key: getApp().xtxx.map_key
        }), console.log("options", t), app.setNavigationBarColor(this), app.pageOnLoad(this), this.setData({
            store_id: getApp().sjid
        }), this.setData({
            params: {
                store_id: getApp().sjid,
                type: "全部",
                img: ""
            }
        }), this.getstorelist();
        var l = this,
            c = l.data.store_id,
            g = util.formatTime(new Date).slice(11, 16);
        app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: c,
                type: 2
            },
            success: function(t) {
                console.log(t.data), wx.setNavigationBarTitle({
                    title: t.data.store.name
                }), "2" == t.data.storeset.top_style && (wx.getLocation({
                    type: "wgs84",
                    success: function(t) {
                        var a = t.latitude,
                            e = t.longitude,
                            o = a + "," + e;
                        console.log(o), qqmapsdk.reverseGeocoder({
                            location: {
                                latitude: a,
                                longitude: e
                            },
                            coord_type: 1,
                            success: function(t) {
                                t.result.ad_info.location;
                                console.log(t), console.log(t.result.formatted_addresses.recommend), console.log("坐标转地址后的经纬度：", t.result.ad_info.location), l.setData({
                                    weizhi: t.result.formatted_addresses.recommend
                                })
                            },
                            fail: function(t) {
                                console.log(t)
                            },
                            complete: function(t) {
                                console.log(t)
                            }
                        })
                    },
                    fail: function() {
                        wx.getSetting({
                            success: function(t) {
                                console.log(t), 0 == t.authSetting["scope.userLocation"] && wx.showModal({
                                    title: "提示",
                                    content: "您点击了拒绝授权,无法正常使用功能，点击确定重新获取授权。",
                                    showCancel: !1,
                                    success: function(t) {
                                        t.confirm && (console.log("用户点击确定"), wx.openSetting({
                                            success: function(t) {
                                                t.authSetting["scope.userLocation"], l.onLoad()
                                            }
                                        }))
                                    }
                                })
                            }
                        })
                    },
                    complete: function(t) {}
                }), app.util.request({
                    url: "entry/wxapp/StoreAd",
                    cachetime: "0",
                    data: {
                        store_id: c
                    },
                    success: function(t) {
                        console.log(t.data), l.setData({
                            slider: t.data
                        })
                    }
                }));
                var a = t.data.store.time,
                    e = t.data.store.time2,
                    o = t.data.store.time3,
                    s = t.data.store.time4,
                    i = t.data.store.is_rest;
                console.log("当前的系统时间为" + g), console.log("商家的营业时间从" + a + "至" + e, o + "至" + s), 1 == i ? (l.setData({
                    yysjtoggle: !1
                }), console.log("商家正在休息" + i)) : console.log("商家正在营业" + i), a < s ? a < g && g < e || o < g && g < s || o < g && s < o ? (console.log("商家正常营业"), l.setData({
                    time: 1
                })) : g < a || e < g && g < o ? (console.log("商家还没开店呐，稍等一会儿可以吗？"), l.setData({
                    time: 2,
                    yysjtoggle: !1
                })) : s < g && (console.log("商家以及关店啦，明天再来吧"), l.setData({
                    time: 3,
                    yysjtoggle: !1
                })) : s < a && (a < g && g < e || o < g && s < g || g < o && g < s ? (console.log("商家正常营业"), l.setData({
                    time: 1
                })) : g < a || e < g && g < o ? (console.log("商家还没开店呐，稍等一会儿可以吗？"), l.setData({
                    time: 2,
                    yysjtoggle: !1
                })) : s < g && (console.log("商家以及关店啦，明天再来吧"), l.setData({
                    time: 3,
                    yysjtoggle: !1
                })));
                for (var n = 0; n < t.data.store.environment.length; n++) t.data.store.environment[n] = l.data.url + t.data.store.environment[n];
                for (var r = 0; r < t.data.store.yyzz.length; r++) t.data.store.yyzz[r] = l.data.url + t.data.store.yyzz[r];
                "" != t.data.storeset.wm_name && l.setData({
                    navbar: [t.data.storeset.wm_name, "评价", "详情"]
                });
                var d = t.data.store.tel;
                l.setData({
                    psf: t.data.psf,
                    reduction: t.data.reduction,
                    store: t.data.store,
                    storeset: t.data.storeset,
                    start_at: t.data.store.start_at,
                    xtxx: getApp().xtxx,
                    paytel: "1" == getApp().xtxx.is_pay && 0 < Number(getApp().xtxx.pay_money) ? d.substring(0, 3) + "****" + d.substring(d.length - 4) : d
                })
            }
        }), app.getUserInfo(function(t) {
            app.util.request({
                url: "entry/wxapp/DelCar",
                cachetime: "0",
                data: {
                    user_id: t.id,
                    store_id: l.data.store_id
                },
                success: function(t) {
                    console.log(t.data)
                }
            });
            var a = util.formatTime(new Date).substring(0, 10).replace(/\//g, "-");
            console.log(t, a), l.setData({
                userinfo: t
            }), "" != t.dq_time && t.dq_time >= a.toString() && l.setData({
                iszk: !0
            });
            var d = wx.getStorageSync("users").id,
                c = l.data.store_id;
            console.log("uid", d), app.util.request({
                url: "entry/wxapp/Hot",
                cachetime: "0",
                data: {
                    store_id: c,
                    type: 2
                },
                success: function(t) {
                    if (0 < t.data.length) {
                        var r = new Array,
                            a = new Object;
                        a.good = t.data, a.type_name = "热销", a.id = "0", r.push(a), app.util.request({
                            url: "entry/wxapp/DishesList",
                            cachetime: "0",
                            data: {
                                store_id: c,
                                type: 2
                            },
                            success: function(t) {
                                for (var i = r.concat(t.data), a = 0; a < i.length; a++) for (var e = 0; e < i[a].good.length; e++) i[a].good[e].quantity = Number(i[a].good[e].quantity);
                                for (var o = 0, s = [], n = 0; n < i.length; n++) o += 105 * i[n].good.length, s.push(o);
                                l.setData({
                                    cpjzz: !1,
                                    dataheith: s
                                }), app.util.request({
                                    url: "entry/wxapp/MyCar",
                                    cachetime: "0",
                                    data: {
                                        store_id: c,
                                        user_id: d
                                    },
                                    success: function(t) {
                                        for (var a = t.data.res, e = 0; e < a.length; e++) for (var o = 0; o < i.length; o++) for (var s = 0; s < i[o].good.length; s++) a[e].good_id == i[o].good[s].id && (i[o].good[s].quantity = i[o].good[s].quantity + Number(a[e].num));
                                        l.setData({
                                            cart_list: t.data,
                                            dishes: i,
                                            isloading: !1
                                        }), l.subText(), l.Coupons()
                                    }
                                })
                            }
                        })
                    } else app.util.request({
                        url: "entry/wxapp/DishesList",
                        cachetime: "0",
                        data: {
                            store_id: c,
                            type: 2
                        },
                        success: function(t) {
                            for (var i = t.data, a = 0; a < i.length; a++) for (var e = 0; e < i[a].good.length; e++) i[a].good[e].quantity = Number(i[a].good[e].quantity);
                            for (var o = 0, s = [], n = 0; n < i.length; n++) o += 105 * i[n].good.length, s.push(o);
                            l.setData({
                                cpjzz: !1,
                                dataheith: s
                            }), app.util.request({
                                url: "entry/wxapp/MyCar",
                                cachetime: "0",
                                data: {
                                    store_id: c,
                                    user_id: d
                                },
                                success: function(t) {
                                    for (var a = t.data.res, e = 0; e < a.length; e++) for (var o = 0; o < i.length; o++) for (var s = 0; s < i[o].good.length; s++) a[e].good_id == i[o].good[s].id && (i[o].good[s].quantity = i[o].good[s].quantity + Number(a[e].num));
                                    l.setData({
                                        cart_list: t.data,
                                        dishes: i,
                                        isloading: !1
                                    }), l.subText(), l.Coupons()
                                }
                            })
                        }
                    })
                }
            })
        }), wx.getSystemInfo({
            success: function(t) {
                console.log(t), l.setData({
                    navzdoffsetTop: t.windowHeight / 4
                })
            }
        })
    },
    maketel: function(t) {
        var a = this,
            e = this.data.store.tel,
            o = this.data.paytel;
        console.log(o), "-1" != o.indexOf("****") ? wx.showModal({
            title: "提示",
            content: "查看电话需付费" + getApp().xtxx.pay_money + "元",
            success: function(t) {
                t.confirm && (console.log("用户点击确定"), app.util.request({
                    url: "entry/wxapp/telPay",
                    cachetime: "0",
                    data: {
                        openid: a.data.userinfo.openid,
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
                                }), a.setData({
                                    paytel: e
                                }))
                            }
                        })
                    }
                }))
            }
        }) : (wx.makePhoneCall({
            phoneNumber: e
        }), a.setData({
            paytel: e
        }))
    },
    location: function() {
        var t = this.data.store.coordinates.split(","),
            a = this.data.store;
        console.log(t), wx.openLocation({
            latitude: parseFloat(t[0]),
            longitude: parseFloat(t[1]),
            address: a.address,
            name: a.name
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
    },
    onShareAppMessage: function() {
        return {
            title: this.data.store.name,
            path: "/zh_cjdianc/pages/Liar/loginindex",
            success: function(t) {},
            fail: function(t) {}
        }
    }
});