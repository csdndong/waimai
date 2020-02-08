var app = getApp(), util = require("../../utils/util.js");

Page({
    data: {
        isloading: !0,
        store_id: "1",
        navbar: [ "店内", "评价", "详情" ],
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
        jzgd: !0
    },
    back: function() {
        wx.navigateBack({
            delta: 1
        });
    },
    submit: function() {
        var t = this.data.userinfo, a = this.data.store_id, e = this.data.zuid, o = this.data.dr_id;
        console.log(t, a, e, o, this.data.tableid), "" == t.img || "" == t.name ? wx.navigateTo({
            url: "getdl"
        }) : t.id == e ? wx.reLaunch({
            url: "drdc?storeid=" + a + "&tableid=" + this.data.tableid
        }) : wx.reLaunch({
            url: "/zh_cjdianc/pages/smdc/sharedrdc?storeid=" + a + "&uid=" + e + "&drid=" + o
        });
    },
    lookck: function() {
        this.setData({
            fwxy: !1
        });
    },
    queren: function() {
        this.setData({
            fwxy: !0
        });
    },
    spxqck: function(t) {
        var a = t.currentTarget.dataset.itemIndex, e = t.currentTarget.dataset.parentindex, o = this.data.dishes, s = this.data.cart_list.res, i = t.currentTarget.dataset.goodid, d = this.data.dishes[e].good[a], r = wx.getStorageSync("users").id, n = this.data.store_id;
        d.goodindex = a, d.catalogSelect = e, console.log(o, s, a, e, i, d, r, n), this.setData({
            spxqinfo: d,
            spxqtoggle: !1
        });
    },
    ckcd: function() {
        this.setData({
            yysjtoggle: !0
        });
    },
    gdsh: function() {
        wx.navigateBack({});
    },
    gbspxq: function() {
        this.setData({
            spxqtoggle: !0
        });
    },
    ggcartdec: function() {
        var a = this;
        wx.showModal({
            title: "提示",
            content: "多规格商品请在购物车中删除对应的规格商品！",
            success: function(t) {
                a.setData({
                    share_modal_active: !0
                });
            }
        });
    },
    gwcdec: function(t) {
        var o = this, s = this.data.dishes, i = t.currentTarget.dataset.goodid, a = Number(t.currentTarget.dataset.num) - 1, e = t.currentTarget.dataset.id;
        console.log(s, i, a, e), wx.showLoading({
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
                    for (var a = 0; a < s.length; a++) for (var e = 0; e < s[a].good.length; e++) s[a].good[e].id == i && s[a].good[e].quantity--;
                    o.setData({
                        dishes: s
                    }), o.gwcreload();
                }
                "超出库存!" == t.data && wx.showModal({
                    title: "提示",
                    content: "超出库存!"
                });
            }
        });
    },
    gwcadd: function(t) {
        var o = this, s = this.data.dishes, i = t.currentTarget.dataset.goodid, a = Number(t.currentTarget.dataset.num) + 1, e = t.currentTarget.dataset.id;
        console.log(s, i, a, e), wx.showLoading({
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
                    }), o.gwcreload();
                }
                "超出库存!" == t.data && wx.showModal({
                    title: "提示",
                    content: "超出库存!请选择其他商品"
                });
            }
        });
    },
    cartdec: function(t) {
        var a = t.currentTarget.dataset.itemIndex, e = t.currentTarget.dataset.parentindex, o = this.data.dishes, s = this.data.cart_list.res, i = t.currentTarget.dataset.goodid, d = this, r = this.data.dishes[e].good[a], n = wx.getStorageSync("users").id, c = this.data.store_id;
        console.log(o, s, a, e, i, r, n, c);
        for (var l = 0; l < s.length; l++) if (s[l].good_id == i) {
            var g = Number(s[l].num) - 1, u = s[l].id;
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
                        for (var a = 0; a < o.length; a++) for (var e = 0; e < o[a].good.length; e++) o[a].good[e].id == i && o[a].good[e].quantity--;
                        d.setData({
                            dishes: o
                        }), d.gwcreload();
                    }
                    "超出库存!" == t.data && wx.showModal({
                        title: "提示",
                        content: "超出库存!"
                    });
                }
            });
        }
    },
    cartadd: function(t) {
        var a = t.currentTarget.dataset.itemIndex, e = t.currentTarget.dataset.parentindex, o = this.data.dishes, s = t.currentTarget.dataset.goodid, i = this, d = this.data.dishes[e].good[a], r = wx.getStorageSync("users").id, n = this.data.store_id;
        console.log(a, e, s, d, r, n), wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/AddCar",
            cachetime: "0",
            data: {
                money: d.dn_money,
                good_id: s,
                store_id: n,
                user_id: i.data.isfqr ? r : i.data.zuid,
                son_id: i.data.isfqr ? "" : r,
                dr_id: i.data.isfqr ? "" : i.data.dr_id,
                num: 1,
                spec: "",
                combination_id: "",
                box_money: d.box_money,
                type: 2
            },
            success: function(t) {
                if (console.log(t), "1" == t.data) {
                    for (var a = 0; a < o.length; a++) for (var e = 0; e < o[a].good.length; e++) o[a].good[e].id == s && o[a].good[e].quantity++;
                    i.setData({
                        dishes: o
                    }), console.log(o), i.gwcreload();
                }
                "超出库存!" == t.data && wx.showModal({
                    title: "提示",
                    content: "库存不足!请重新选择"
                });
            }
        });
    },
    spggck: function(t) {
        var n = t.currentTarget.dataset.itemIndex, c = t.currentTarget.dataset.parentindex, l = t.currentTarget.dataset.goodid, g = this;
        console.log(n, c, l), app.util.request({
            url: "entry/wxapp/GoodInfo",
            cachetime: "0",
            data: {
                good_id: l
            },
            success: function(t) {
                console.log(t.data);
                var a = t.data.spec, e = t.data.name;
                for (var o in a) for (var s in a[o].spec_val) a[o].spec_val[s].checked = 0 == s;
                g.setData({
                    gg: a,
                    spname: e
                }), console.log(a);
                var i = [], d = !0;
                for (var o in a) {
                    var r = !1;
                    for (var s in a[o].spec_val) if (a[o].spec_val[s].checked) {
                        i.push(a[o].spec_val[s].spec_val_name), r = !0;
                        break;
                    }
                    if (!r) {
                        d = !1;
                        break;
                    }
                }
                console.log(l, i, i.toString()), d && (wx.showLoading({
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
                            itemIndex: n,
                            parentindex: c
                        });
                    }
                }));
            }
        });
    },
    attrClick: function(t) {
        var a = this, e = this.data.gginfo.good_id, o = t.target.dataset.groupId, s = t.target.dataset.id, i = a.data.gg;
        for (var d in console.log(o, s, i), i) if (i[d].spec_id == o) for (var r in i[d].spec_val) i[d].spec_val[r].spec_val_id == s ? i[d].spec_val[r].checked = !0 : i[d].spec_val[r].checked = !1;
        a.setData({
            gg: i
        });
        var n = [], c = !0;
        for (var d in i) {
            var l = !1;
            for (var r in i[d].spec_val) if (i[d].spec_val[r].checked) {
                n.push(i[d].spec_val[r].spec_val_name), l = !0;
                break;
            }
            if (!l) {
                c = !1;
                break;
            }
        }
        console.log(e, n, n.toString()), c && (wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/GgZh",
            cachetime: "0",
            data: {
                combination: n.toString(),
                good_id: e
            },
            success: function(t) {
                console.log(t), a.setData({
                    gginfo: t.data
                });
            }
        }));
    },
    ggaddcart: function() {
        var t = this.data.itemIndex, a = this.data.parentindex, o = this.data.dishes, s = this, i = this.data.gginfo, e = wx.getStorageSync("users").id, d = this.data.gg, r = this.data.store_id, n = [], c = !0;
        for (var l in d) {
            var g = !1;
            for (var u in d[l].spec_val) if (d[l].spec_val[u].checked) {
                n.push(d[l].spec_name + ":" + d[l].spec_val[u].spec_val_name), g = !0;
                break;
            }
            if (!g) {
                c = !1;
                break;
            }
        }
        console.log("加入购物车", t, a, o, i, e, r, d, n, n.toString()), c && (wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/AddCar",
            cachetime: "0",
            data: {
                money: i.dn_money,
                good_id: i.good_id,
                store_id: r,
                user_id: s.data.isfqr ? e : s.data.zuid,
                son_id: s.data.isfqr ? "" : e,
                dr_id: s.data.isfqr ? "" : s.data.dr_id,
                num: 1,
                spec: n.toString(),
                combination_id: i.id,
                box_money: i.box_money,
                type: 2
            },
            success: function(t) {
                if (console.log(t), "1" == t.data) {
                    for (var a = 0; a < o.length; a++) for (var e = 0; e < o[a].good.length; e++) o[a].good[e].id == i.good_id && o[a].good[e].quantity++;
                    s.setData({
                        dishes: o
                    }), s.gwcreload(), s.setData({
                        spggtoggle: !0
                    });
                }
                "超出库存!" == t.data && wx.showModal({
                    title: "提示",
                    content: "暂无库存!请选择其他规格或商品"
                });
            }
        }));
    },
    gwcreload: function() {
        var a = this.data.dishes, e = this, t = wx.getStorageSync("users").id, o = this.data.store_id;
        console.log(a, t, o), app.util.request({
            url: "entry/wxapp/MyCar",
            cachetime: "0",
            data: {
                store_id: o,
                user_id: e.data.isfqr ? t : e.data.zuid,
                son_id: e.data.isfqr ? "" : t,
                dr_id: e.data.isfqr ? "" : e.data.dr_id,
                type: 2
            },
            success: function(t) {
                console.log(t), console.log(a), e.setData({
                    cart_list: t.data
                }), e.subText();
            }
        });
    },
    gbspgg: function() {
        this.setData({
            spggtoggle: !0
        });
    },
    gbyysj: function() {
        this.setData({
            yysjtoggle: !0
        });
    },
    selectednavbar: function(t) {
        console.log(t), this.setData({
            selectedindex: t.currentTarget.dataset.index
        });
    },
    selectMenu: function(e) {
        var s = this.data.dishes, i = this, o = wx.getStorageSync("users").id, d = i.data.store_id, r = e.currentTarget.dataset.itemIndex;
        if (console.log(s, o, d, r), this.setData({
            catalogSelect: e.currentTarget.dataset.itemIndex
        }), 0 == s[e.currentTarget.dataset.itemIndex].good.length) {
            var t = s[e.currentTarget.dataset.itemIndex].id;
            console.log("还没加载过数据", t), i.setData({
                cpjzz: !0
            }), app.util.request({
                url: "entry/wxapp/Dishes",
                cachetime: "0",
                data: {
                    type_id: t,
                    type: 1
                },
                success: function(t) {
                    console.log(t.data);
                    for (var a = 0; a < t.data.length; a++) t.data[a].quantity = Number(t.data[a].quantity);
                    s[e.currentTarget.dataset.itemIndex].good = t.data, i.setData({
                        cpjzz: !1
                    }), app.util.request({
                        url: "entry/wxapp/MyCar",
                        cachetime: "0",
                        data: {
                            store_id: d,
                            user_id: i.data.isfqr ? o : i.data.zuid,
                            son_id: i.data.isfqr ? "" : o,
                            dr_id: i.data.isfqr ? "" : i.data.dr_id,
                            type: 2
                        },
                        success: function(t) {
                            console.log(t);
                            for (var a = t.data.res, e = 0; e < a.length; e++) for (var o = 0; o < s[r].good.length; o++) a[e].good_id == s[r].good[o].id && (s[r].good[o].quantity = s[r].good[o].quantity + Number(a[e].num));
                            console.log(s), i.setData({
                                dishes: s
                            });
                        }
                    });
                }
            });
        } else console.log("已有缓存数据");
    },
    swiperChange: function(t) {
        console.log(t), this.setData({
            selectedindex: t.detail.current
        });
    },
    showcart: function() {
        this.setData({
            share_modal_active: !this.data.share_modal_active
        });
    },
    closecart: function() {
        this.setData({
            share_modal_active: !1
        });
    },
    clear: function() {
        var o = this, s = this.data.dishes, a = wx.getStorageSync("users").id, e = o.data.store_id;
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
                        store_id: e,
                        user_id: o.data.isfqr ? a : o.data.zuid,
                        son_id: o.data.isfqr ? "" : a,
                        dr_id: o.data.isfqr ? "" : o.data.dr_id,
                        type: 2
                    },
                    success: function(t) {
                        if (console.log(t.data), "1" == t.data) {
                            for (var a = 0; a < s.length; a++) for (var e = 0; e < s[a].good.length; e++) s[a].good[e].quantity = 0;
                            o.setData({
                                dishes: s,
                                share_modal_active: !1
                            }), o.gwcreload();
                        }
                    }
                })) : t.cancel && console.log("用户点击取消");
            }
        });
    },
    subText: function() {
        console.log(this.data);
        var t, a = parseFloat(this.data.cart_list.money), e = parseFloat(this.data.start_at);
        console.log(a, e), a <= 0 ? t = "还没有选购商品" : (console.log(a), t = "去结算"), this.setData({
            subtext: t
        });
    },
    onLoad: function(a) {
        console.log("options", a), app.setNavigationBarColor(this), this.setData({
            store_id: a.storeid,
            zuid: a.zuid,
            dr_id: a.dr_id,
            tableid: a.tableid
        }), "1" == a.isyy ? this.setData({
            isyy: !0
        }) : this.setData({
            isyy: !1
        });
        var c = this, t = c.data.store_id, l = util.formatTime(new Date()).slice(11, 16);
        app.util.request({
            url: "entry/wxapp/Url",
            cachetime: "0",
            success: function(n) {
                console.log(n.data), getApp().imgurl = n.data, c.setData({
                    url: n.data
                }), app.util.request({
                    url: "entry/wxapp/StoreInfo",
                    cachetime: "0",
                    data: {
                        store_id: t,
                        type: 1
                    },
                    success: function(t) {
                        console.log(t.data);
                        var a = t.data.store.time, e = t.data.store.time2, o = t.data.store.time3, s = t.data.store.time4, i = t.data.store.is_rest;
                        console.log("当前的系统时间为" + l), console.log("商家的营业时间从" + a + "至" + e, o + "至" + s), 
                        1 == i ? (c.setData({
                            yysjtoggle: !1
                        }), console.log("商家正在休息" + i)) : console.log("商家正在营业" + i), a < s ? a < l && l < e || o < l && l < s ? (console.log("商家正常营业"), 
                        c.setData({
                            time: 1
                        })) : l < a || e < l && l < o ? (console.log("商家还没开店呐，稍等一会儿可以吗？"), c.setData({
                            time: 2,
                            yysjtoggle: !1
                        })) : s < l && (console.log("商家以及关店啦，明天再来吧"), c.setData({
                            time: 3,
                            yysjtoggle: !1
                        })) : s < a && (a < l && l < e || o < l && s < l || l < o && l < s ? (console.log("商家正常营业"), 
                        c.setData({
                            time: 1
                        })) : l < a || e < l && l < o ? (console.log("商家还没开店呐，稍等一会儿可以吗？"), c.setData({
                            time: 2,
                            yysjtoggle: !1
                        })) : s < l && (console.log("商家以及关店啦，明天再来吧"), c.setData({
                            time: 3,
                            yysjtoggle: !1
                        })));
                        for (var d = 0; d < t.data.store.environment.length; d++) t.data.store.environment[d] = n.data + t.data.store.environment[d];
                        for (var r = 0; r < t.data.store.yyzz.length; r++) t.data.store.yyzz[r] = n.data + t.data.store.yyzz[r];
                        "" != t.data.storeset.dn_name && (wx.setNavigationBarTitle({
                            title: t.data.storeset.dn_name
                        }), c.setData({
                            navbar: [ t.data.storeset.dn_name, "评价", "详情" ]
                        })), c.setData({
                            psf: t.data.psf,
                            reduction: t.data.reduction,
                            store: t.data.store,
                            storeset: t.data.storeset,
                            start_at: 0
                        });
                    }
                });
            }
        }), app.getUserInfo(function(t) {
            console.log(t), t.id == a.zuid ? c.setData({
                isfqr: !0
            }) : c.setData({
                isfqr: !1
            }), c.setData({
                userinfo: t
            });
            var s = wx.getStorageSync("users").id, d = c.data.store_id;
            console.log("uid", s), app.util.request({
                url: "entry/wxapp/Hot",
                cachetime: "0",
                data: {
                    store_id: d,
                    type: 1
                },
                success: function(t) {
                    console.log(t.data);
                    for (var a = 0; a < t.data.length; a++) t.data[a].quantity = Number(t.data[a].quantity);
                    if (0 < t.data.length) {
                        var e = new Array(), o = new Object();
                        o.good = t.data, o.type_name = "热销", o.id = "0", e.push(o), app.util.request({
                            url: "entry/wxapp/DishesType",
                            cachetime: "0",
                            data: {
                                store_id: d,
                                type: 1
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
                                        store_id: d,
                                        user_id: c.data.isfqr ? s : c.data.zuid,
                                        son_id: c.data.isfqr ? "" : s,
                                        dr_id: c.data.isfqr ? "" : c.data.dr_id,
                                        type: 2
                                    },
                                    success: function(t) {
                                        console.log(t);
                                        for (var a = t.data.res, e = 0; e < a.length; e++) for (var o = 0; o < i.length; o++) for (var s = 0; s < i[o].good.length; s++) a[e].good_id == i[o].good[s].id && (i[o].good[s].quantity = i[o].good[s].quantity + Number(a[e].num));
                                        console.log(i), c.setData({
                                            cart_list: t.data,
                                            dishes: i,
                                            isloading: !1
                                        }), c.subText();
                                    }
                                });
                            }
                        });
                    } else app.util.request({
                        url: "entry/wxapp/DishesType",
                        cachetime: "0",
                        data: {
                            store_id: d,
                            type: 1
                        },
                        success: function(t) {
                            console.log(t.data);
                            var i = t.data;
                            app.util.request({
                                url: "entry/wxapp/Dishes",
                                cachetime: "0",
                                data: {
                                    type_id: i[0].id,
                                    type: 1
                                },
                                success: function(t) {
                                    console.log(t.data);
                                    for (var a = 0; a < t.data.length; a++) t.data[a].quantity = Number(t.data[a].quantity);
                                    i[0].good = t.data, c.setData({
                                        cpjzz: !1
                                    }), app.util.request({
                                        url: "entry/wxapp/MyCar",
                                        cachetime: "0",
                                        data: {
                                            store_id: d,
                                            user_id: c.data.isfqr ? s : c.data.zuid,
                                            son_id: c.data.isfqr ? "" : s,
                                            dr_id: c.data.isfqr ? "" : c.data.dr_id,
                                            type: 2
                                        },
                                        success: function(t) {
                                            console.log(t);
                                            for (var a = t.data.res, e = 0; e < a.length; e++) for (var o = 0; o < i.length; o++) for (var s = 0; s < i[o].good.length; s++) a[e].good_id == i[o].good[s].id && (i[o].good[s].quantity = i[o].good[s].quantity + Number(a[e].num));
                                            console.log(i), c.setData({
                                                cart_list: t.data,
                                                dishes: i,
                                                isloading: !1
                                            }), c.subText();
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            });
        }), wx.getSystemInfo({
            success: function(t) {
                console.log(t), c.setData({
                    height: t.windowHeight - 145
                });
            }
        });
    },
    maketel: function(t) {
        var a = this.data.store.tel;
        wx.makePhoneCall({
            phoneNumber: a
        });
    },
    location: function() {
        var t = this.data.store.coordinates.split(","), a = this.data.store;
        console.log(t), wx.openLocation({
            latitude: parseFloat(t[0]),
            longitude: parseFloat(t[1]),
            address: a.address,
            name: a.name
        });
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
        }), this.getstorelist());
    }
});