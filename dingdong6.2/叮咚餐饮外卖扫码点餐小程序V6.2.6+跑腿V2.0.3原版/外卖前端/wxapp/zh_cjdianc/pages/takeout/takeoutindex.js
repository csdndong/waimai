/*   time:2019-07-18 01:07:49*/
var app = getApp(),
    util = require("../../utils/util.js");
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
    catchTouchMove: function(t) {
        return !1
    },
    scsj: function(t) {
        var a = this,
            e = "2" == this.data.issc ? "1" : "2",
            s = wx.getStorageSync("users").id,
            o = a.data.store_id;
        console.log(e, s, o), app.util.request({
            url: "entry/wxapp/SaveCollection",
            cachetime: "0",
            data: {
                store_id: o,
                user_id: s,
                type: e
            },
            success: function(t) {
                console.log(t), a.setData({
                    issc: e
                })
            }
        })
    },
    previewzzImage: function(t) {
        console.log(t);
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
            s = t.currentTarget.dataset.index,
            o = t.currentTarget.dataset.picindex,
            i = t.currentTarget.dataset.id;
        if (console.log(s, o, i), i == a[s].id) {
            var r = a[s].img;
            for (var n in r) e.push(this.data.url + r[n]);
            wx.previewImage({
                current: this.data.url + r[o],
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
        var s = this,
            o = s.data.pagenum;
        s.data.params.page = o, s.data.params.pagesize = 10, console.log(o, s.data.params), s.setData({
            isjzz: !0
        }), app.util.request({
            url: "entry/wxapp/AssessList",
            cachetime: "0",
            data: s.data.params,
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
                    e = s.data.bfstorelist;
                e = function(t) {
                    for (var a = [], e = 0; e < t.length; e++) - 1 == a.indexOf(t[e]) && a.push(t[e]);
                    return a
                }(e = e.concat(t.data.assess)), s.setData({
                    storelist: e,
                    bfstorelist: e,
                    pjnavbar: a
                }), t.data.assess.length < 10 ? s.setData({
                    mygd: !0,
                    jzgd: !0,
                    isjzz: !1
                }) : s.setData({
                    jzgd: !0,
                    pagenum: o + 1,
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
                for (var a = [], e = 0; e < t.data.length; e++) "2" != t.data[e].type && a.push(t.data[e]);
                s.setData({
                    Coupons: a
                })
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
            url: "takeoutform?storeid=" + this.data.store_id
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
            s = (this.data.dishes, this.data.cart_list.res, t.currentTarget.dataset.goodid, this.data.dishes[e].good[a]);
        wx.getStorageSync("users").id, this.data.store_id;
        s.goodindex = a, s.catalogSelect = e, this.setData({
            spxqinfo: s,
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
        for (var s = this, o = this.data.dishes, i = t.currentTarget.dataset.goodid, a = t.currentTarget.dataset.id, e = 0, r = o.length; e < r; e++) for (var n = 0, d = o[e].good.length; n < d; n++) if (o[e].good[n].id == i) {
            var c = 1;
            Number(o[e].good[n].start_num) == Number(t.currentTarget.dataset.num) && (c = Number(o[e].good[n].start_num));
            var l = Number(t.currentTarget.dataset.num) - c;
            console.log(l, c), wx.showLoading({
                title: "正在加载",
                mask: !0
            }), app.util.request({
                url: "entry/wxapp/UpdCar",
                cachetime: "0",
                data: {
                    num: l,
                    id: a
                },
                success: function(t) {
                    if (console.log(t), "1" == t.data) {
                        for (var a = 0; a < o.length; a++) for (var e = 0; e < o[a].good.length; e++) o[a].good[e].id == i && (o[a].good[e].quantity = o[a].good[e].quantity - c);
                        s.setData({
                            dishes: o
                        }), s.gwcreload()
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
        var i = this,
            r = this.data.dishes,
            n = t.currentTarget.dataset.goodid,
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
                    for (var a = 0, e = r.length; a < e; a++) for (var s = 0, o = r[a].good.length; s < o; s++) r[a].good[s].id == n && r[a].good[s].quantity++;
                    i.setData({
                        dishes: r
                    }), i.gwcreload()
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
        for (var a = t.currentTarget.dataset.itemIndex, e = t.currentTarget.dataset.parentindex, i = this.data.dishes, s = this.data.cart_list.res, r = t.currentTarget.dataset.goodid, n = this, o = this.data.dishes[e].good[a], d = (wx.getStorageSync("users").id, this.data.store_id, 0), c = s.length; d < c; d++) if (s[d].good_id == r) {
            var l = 1;
            Number(o.start_num) == Number(s[d].num) && (l = Number(o.start_num));
            var g = Number(s[d].num) - l,
                u = s[d].id;
            wx.showLoading({
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
                        for (var a = 0, e = i.length; a < e; a++) for (var s = 0, o = i[a].good.length; s < o; s++) i[a].good[s].id == r && (i[a].good[s].quantity = i[a].good[s].quantity - l);
                        n.setData({
                            dishes: i
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
            i = this.data.dishes,
            s = this.data.cart_list.res,
            r = t.currentTarget.dataset.goodid,
            n = this,
            o = this.data.dishes[e].good[a],
            d = wx.getStorageSync("users").id,
            c = this.data.store_id,
            l = "1" == getApp().xtxx.hygn && this.data.iszk && "0.00" != o.vip_money ? 1 : 0,
            g = 1;
        "0" == o.start_num || n.isInArray(s, r) || (g = Number(o.start_num)), console.log(g), wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/AddCar",
            cachetime: "0",
            data: {
                money: l ? o.vip_money : o.money,
                good_id: r,
                store_id: c,
                user_id: d,
                num: g,
                spec: "",
                combination_id: "",
                box_money: o.box_money
            },
            success: function(t) {
                if (console.log(t), "1" == t.data) {
                    for (var a = 0, e = i.length; a < e; a++) for (var s = 0, o = i[a].good.length; s < o; s++) i[a].good[s].id == r && (i[a].good[s].quantity = g + i[a].good[s].quantity);
                    n.setData({
                        dishes: i
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
                for (var s in a) for (var o in a[s].spec_val) a[s].spec_val[o].checked = 0 == o;
                g.setData({
                    gg: a,
                    spname: e
                }), console.log(a);
                var i = [],
                    r = !0;
                for (var s in a) {
                    var n = !1;
                    for (var o in a[s].spec_val) if (a[s].spec_val[o].checked) {
                        i.push(a[s].spec_val[o].spec_val_name), n = !0;
                        break
                    }
                    if (!n) {
                        r = !1;
                        break
                    }
                }
                console.log(l, i, i.toString()), r && (wx.showLoading({
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
            s = t.target.dataset.groupId,
            o = t.target.dataset.id,
            i = a.data.gg;
        for (var r in console.log(s, o, i), i) if (i[r].spec_id == s) for (var n in i[r].spec_val) i[r].spec_val[n].spec_val_id == o ? i[r].spec_val[n].checked = !0 : i[r].spec_val[n].checked = !1;
        a.setData({
            gg: i
        });
        var d = [],
            c = !0;
        for (var r in i) {
            var l = !1;
            for (var n in i[r].spec_val) if (i[r].spec_val[n].checked) {
                d.push(i[r].spec_val[n].spec_val_name), l = !0;
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
        var t = this.data.itemIndex,
            a = this.data.parentindex,
            i = this.data.dishes,
            r = (this.data.dishes[a].good[t], this.data.cart_list.res, this),
            n = this.data.gginfo,
            e = wx.getStorageSync("users").id,
            s = this.data.gg,
            o = this.data.store_id,
            d = [],
            c = !0;
        for (var l in s) {
            var g = !1;
            for (var u in s[l].spec_val) if (s[l].spec_val[u].checked) {
                d.push(s[l].spec_name + ":" + s[l].spec_val[u].spec_val_name), g = !0;
                break
            }
            if (!g) {
                c = !1;
                break
            }
        }
        c && (wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/AddCar",
            cachetime: "0",
            data: {
                money: n.wm_money,
                good_id: n.good_id,
                store_id: o,
                user_id: e,
                num: 1,
                spec: d.toString(),
                combination_id: n.id,
                box_money: n.box_money
            },
            success: function(t) {
                if (console.log(t), "1" == t.data) {
                    for (var a = 0, e = i.length; a < e; a++) for (var s = 0, o = i[a].good.length; s < o; s++) i[a].good[s].id == n.good_id && i[a].good[s].quantity++;
                    r.setData({
                        dishes: i
                    }), r.gwcreload(), r.setData({
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
                a.setData({
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
        var o = this.data.dishes,
            i = this,
            e = wx.getStorageSync("users").id,
            s = i.data.store_id,
            r = this.data.loadindex;
        if (console.log(o, e, s, r), i.setData({
            loadMore: !1
        }), r < o.length && 0 == o[r].good.length) {
            var t = o[r].id;
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
                    o[r].good = t.data, app.util.request({
                        url: "entry/wxapp/MyCar",
                        cachetime: "0",
                        data: {
                            store_id: s,
                            user_id: e
                        },
                        success: function(t) {
                            console.log(t);
                            for (var a = t.data.res, e = 0; e < a.length; e++) for (var s = 0; s < o[r].good.length; s++) a[e].good_id == o[r].good[s].id && (o[r].good[s].quantity = o[r].good[s].quantity + Number(a[e].num));
                            console.log(o), i.setData({
                                dishes: o,
                                loadindex: r + 1,
                                loadMore: !0
                            }), r == o.length - 1 && (console.log("alldie"), i.setData({
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
        this.data.dishes;
        for (var a = this.data.dataheith, e = (this.data.catalogSelect, t.detail.scrollTop), s = 0; s < a.length; s++) if (e <= a[s]) {
            this.setData({
                catalogSelect: s,
                toType: "type" + (s - 2)
            });
            break
        }
    },
    selectMenu: function(s) {
        var o = this.data.dishes,
            i = this,
            r = wx.getStorageSync("users").id,
            n = i.data.store_id,
            d = s.currentTarget.dataset.itemIndex;
        if (i.setData({
            catalogSelect: s.currentTarget.dataset.itemIndex,
            toType: "type" + (d - 2),
            scrolltop: 0
        }), 0 == o[s.currentTarget.dataset.itemIndex].good.length) {
            var t = o[s.currentTarget.dataset.itemIndex].id;
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
                    for (var a = 0, e = t.data.length; a < e; a++) t.data[a].quantity = Number(t.data[a].quantity);
                    o[s.currentTarget.dataset.itemIndex].good = t.data, i.setData({
                        cpjzz: !1
                    }), app.util.request({
                        url: "entry/wxapp/MyCar",
                        cachetime: "0",
                        data: {
                            store_id: n,
                            user_id: r
                        },
                        success: function(t) {
                            console.log(t);
                            for (var a = t.data.res, e = 0; e < a.length; e++) for (var s = 0; s < o[d].good.length; s++) a[e].good_id == o[d].good[s].id && (o[d].good[s].quantity = o[d].good[s].quantity + Number(a[e].num));
                            console.log(o), i.setData({
                                dishes: o
                            })
                        }
                    })
                }
            })
        } else console.log("已有缓存数据")
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
        var i = this,
            r = this.data.dishes,
            a = wx.getStorageSync("users").id,
            e = i.data.store_id;
        console.log(r, a, e), wx.showModal({
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
                            for (var a = 0, e = r.length; a < e; a++) for (var s = 0, o = r[a].good.length; s < o; s++) r[a].good[s].quantity = 0;
                            i.setData({
                                dishes: r,
                                share_modal_active: !1
                            }), i.gwcreload()
                        }
                    }
                })) : t.cancel && console.log("用户点击取消")
            }
        })
    },
    subText: function() {
        var t, a = parseFloat(this.data.cart_list.money),
            e = parseFloat(this.data.start_at);
        if (console.log(a, e), a <= 0) t = "￥" + this.data.start_at + "元起送", null == this.data.start_at && (t = "请选择商品");
        else if (a < e) {
            var s = e - a;
            console.log(s), t = "还差" + s.toFixed(2) + "元起送"
        } else console.log(a), t = "去结算";
        this.setData({
            subtext: t
        })
    },
    onLoad: function(e) {
        console.log("options", e), app.setNavigationBarColor(this);
        var t = decodeURIComponent(e.scene);
        console.log("scene", t), "undefined" != t ? this.setData({
            store_id: t,
            params: {
                store_id: t,
                type: "全部",
                img: ""
            }
        }) : this.setData({
            store_id: e.storeid,
            params: {
                store_id: e.storeid,
                type: "全部",
                img: ""
            }
        }), this.getstorelist();
        var c = this,
            a = c.data.store_id,
            l = util.formatTime(new Date).slice(11, 16);
        app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: a,
                type: 2
            },
            success: function(t) {
                console.log(t.data);
                var a = t.data.store.time,
                    e = t.data.store.time2,
                    s = t.data.store.time3,
                    o = t.data.store.time4,
                    i = t.data.store.is_rest;
                console.log("当前的系统时间为" + l), console.log("商家的营业时间从" + a + "至" + e, s + "至" + o), 1 == i ? (c.setData({
                    yysjtoggle: !1
                }), console.log("商家正在休息" + i)) : console.log("商家正在营业" + i), a < o ? a < l && l < e || s < l && l < o || s < l && o < s ? (console.log("商家正常营业"), c.setData({
                    time: 1
                })) : l < a || e < l && l < s ? (console.log("商家还没开店呐，稍等一会儿可以吗？"), c.setData({
                    time: 2,
                    yysjtoggle: !1
                })) : o < l && (console.log("商家以及关店啦，明天再来吧"), c.setData({
                    time: 3,
                    yysjtoggle: !1
                })) : o < a && (a < l && l < e || s < l && o < l || l < s && l < o ? (console.log("商家正常营业"), c.setData({
                    time: 1
                })) : l < a || e < l && l < s ? (console.log("商家还没开店呐，稍等一会儿可以吗？"), c.setData({
                    time: 2,
                    yysjtoggle: !1
                })) : o < l && (console.log("商家以及关店啦，明天再来吧"), c.setData({
                    time: 3,
                    yysjtoggle: !1
                })));
                for (var r = 0; r < t.data.store.environment.length; r++) t.data.store.environment[r] = c.data.url + t.data.store.environment[r];
                for (var n = 0; n < t.data.store.yyzz.length; n++) t.data.store.yyzz[n] = c.data.url + t.data.store.yyzz[n];
                "" != t.data.storeset.wm_name && c.setData({
                    navbar: [t.data.storeset.wm_name, "评价", "详情"]
                });
                var d = t.data.store.tel;
                c.setData({
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
            null == e.qgjl && app.util.request({
                url: "entry/wxapp/DelCar",
                cachetime: "0",
                data: {
                    user_id: t.id,
                    store_id: c.data.store_id
                },
                success: function(t) {
                    console.log(t.data)
                }
            });
            var a = util.formatTime(new Date).substring(0, 10).replace(/\//g, "-");
            console.log(t, a), c.setData({
                userinfo: t
            }), "" != t.dq_time && t.dq_time >= a.toString() && c.setData({
                iszk: !0
            });
            var o = wx.getStorageSync("users").id,
                n = c.data.store_id;
            console.log("uid", o), c.Coupons(), app.util.request({
                url: "entry/wxapp/SaveCollection",
                cachetime: "0",
                data: {
                    store_id: n,
                    user_id: o
                },
                success: function(t) {
                    console.log(t), c.setData({
                        issc: t.data
                    })
                }
            }), app.util.request({
                url: "entry/wxapp/Hot",
                cachetime: "0",
                data: {
                    store_id: n,
                    type: 2
                },
                success: function(t) {
                    if (0 < t.data.length) {
                        for (var a = 0; a < t.data.length; a++) t.data[a].quantity = Number(t.data[a].quantity);
                        var e = new Array,
                            s = new Object;
                        s.good = t.data, s.type_name = "热销", s.id = "0", e.push(s), app.util.request({
                            url: "entry/wxapp/DishesType",
                            cachetime: "0",
                            data: {
                                store_id: n,
                                type: 2
                            },
                            success: function(t) {
                                console.log(t.data);
                                var i = e.concat(t.data);
                                console.log(i), c.setData({
                                    cpjzz: !1
                                }), app.util.request({
                                    url: "entry/wxapp/MyCar",
                                    cachetime: "0",
                                    data: {
                                        store_id: n,
                                        user_id: o
                                    },
                                    success: function(t) {
                                        console.log(t);
                                        for (var a = t.data.res, e = 0; e < a.length; e++) for (var s = 0; s < i.length; s++) for (var o = 0; o < i[s].good.length; o++) a[e].good_id == i[s].good[o].id && (i[s].good[o].quantity = i[s].good[o].quantity + Number(a[e].num));
                                        console.log(i), c.setData({
                                            cart_list: t.data,
                                            dishes: i,
                                            isloading: !1
                                        }), c.subText()
                                    }
                                })
                            }
                        })
                    } else app.util.request({
                        url: "entry/wxapp/DishesType",
                        cachetime: "0",
                        data: {
                            store_id: n,
                            type: 2
                        },
                        success: function(t) {
                            var r = t.data;
                            app.util.request({
                                url: "entry/wxapp/Dishes",
                                cachetime: "0",
                                data: {
                                    type_id: r[0].id,
                                    type: 2
                                },
                                success: function(t) {
                                    console.log(t.data);
                                    for (var a = 0, e = t.data.length; a < e; a++) t.data[a].quantity = Number(t.data[a].quantity);
                                    r[0].good = t.data, c.setData({
                                        cpjzz: !1
                                    }), app.util.request({
                                        url: "entry/wxapp/MyCar",
                                        data: {
                                            store_id: n,
                                            user_id: o
                                        },
                                        success: function(t) {
                                            console.log(t);
                                            for (var a = t.data.res, e = 0, s = a.length; e < s; e++) for (var o = 0; o < r.length; o++) for (var i = 0; i < r[o].good.length; i++) a[e].good_id == r[o].good[i].id && (r[o].good[i].quantity = r[o].good[i].quantity + Number(a[e].num));
                                            console.log(r), c.setData({
                                                cart_list: t.data,
                                                dishes: r,
                                                isloading: !1
                                            }), c.subText()
                                        }
                                    })
                                }
                            })
                        }
                    })
                }
            })
        }), wx.getSystemInfo({
            success: function(t) {
                console.log(t), c.setData({
                    height: t.windowHeight - 145
                })
            }
        })
    },
    maketel: function(t) {
        var a = this,
            e = this.data.store.tel,
            s = this.data.paytel;
        console.log(s), "-1" != s.indexOf("****") ? wx.showModal({
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
            path: "/zh_cjdianc/pages/takeout/takeoutindex?storeid=" + this.data.store_id,
            success: function(t) {},
            fail: function(t) {}
        }
    }
});