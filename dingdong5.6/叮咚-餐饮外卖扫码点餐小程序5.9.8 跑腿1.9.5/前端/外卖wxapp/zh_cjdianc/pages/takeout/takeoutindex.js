var app = getApp(), util = require("../../utils/util.js");

Page({
    data: {
        isloading: !0,
        store_id: "1",
        navbar: [ "外卖", "评价", "详情" ],
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
    scsj: function(t) {
        var a = this, e = "2" == this.data.issc ? "1" : "2", o = wx.getStorageSync("users").id, s = a.data.store_id;
        console.log(e, o, s), app.util.request({
            url: "entry/wxapp/SaveCollection",
            cachetime: "0",
            data: {
                store_id: s,
                user_id: o,
                type: e
            },
            success: function(t) {
                console.log(t), a.setData({
                    issc: e
                });
            }
        });
    },
    previewzzImage: function(t) {
        console.log(t);
        var a = t.currentTarget.dataset.urls, e = t.currentTarget.id;
        wx.previewImage({
            current: e,
            urls: a
        });
    },
    previewhjImage: function(t) {
        var a = t.currentTarget.dataset.urls, e = t.currentTarget.id;
        wx.previewImage({
            current: e,
            urls: a
        });
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
                console.log(t.data);
            }
        });
    },
    commentPicView: function(t) {
        console.log(t);
        var a = this.data.storelist, e = [], o = t.currentTarget.dataset.index, s = t.currentTarget.dataset.picindex, i = t.currentTarget.dataset.id;
        if (console.log(o, s, i), i == a[o].id) {
            var r = a[o].img;
            for (var n in r) e.push(this.data.url + r[n]);
            wx.previewImage({
                current: this.data.url + r[s],
                urls: e
            });
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
        }), this.getstorelist();
    },
    pjselectednavbar: function(t) {
        console.log(t);
        var a = this.data.params;
        0 == t.currentTarget.dataset.index && (a.type = "全部"), 1 == t.currentTarget.dataset.index && (a.type = "1"), 
        2 == t.currentTarget.dataset.index && (a.type = "2"), this.setData({
            pagenum: 1,
            storelist: [],
            bfstorelist: [],
            mygd: !1,
            jzgd: !0,
            pjselectedindex: t.currentTarget.dataset.index,
            params: a
        }), this.getstorelist();
    },
    getstorelist: function() {
        var o = this, s = o.data.pagenum;
        o.data.params.page = s, o.data.params.pagesize = 10, console.log(s, o.data.params), 
        o.setData({
            isjzz: !0
        }), app.util.request({
            url: "entry/wxapp/AssessList",
            cachetime: "0",
            data: o.data.params,
            success: function(t) {
                console.log("分页返回的商家列表数据", t.data);
                var a = [ {
                    name: "全部",
                    num: t.data.all
                }, {
                    name: "满意",
                    num: t.data.ok
                }, {
                    name: "不满意",
                    num: t.data.no
                } ], e = o.data.bfstorelist;
                e = function(t) {
                    for (var a = [], e = 0; e < t.length; e++) -1 == a.indexOf(t[e]) && a.push(t[e]);
                    return a;
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
                }), console.log(e);
            }
        });
    },
    Coupons: function() {
        var o = this, t = wx.getStorageSync("users").id, a = o.data.store_id;
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
                o.setData({
                    Coupons: a
                });
            }
        });
    },
    ljlq: function(t) {
        console.log(t.currentTarget.dataset.qid);
        var a = this, e = wx.getStorageSync("users").id;
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
                    a.Coupons();
                }, 1e3));
            }
        });
    },
    submit: function() {
        var t = this.data.userinfo;
        console.log(t), "" == t.img || "" == t.name ? wx.navigateTo({
            url: "../smdc/getdl"
        }) : wx.navigateTo({
            url: "takeoutform?storeid=" + this.data.store_id
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
        var a = t.currentTarget.dataset.itemIndex, e = t.currentTarget.dataset.parentindex, o = this.data.dishes, s = this.data.cart_list.res, i = t.currentTarget.dataset.goodid, r = this.data.dishes[e].good[a], n = wx.getStorageSync("users").id, d = this.data.store_id;
        r.goodindex = a, r.catalogSelect = e, console.log(o, s, a, e, i, r, n, d), this.setData({
            spxqinfo: r,
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
        var o = this, s = this.data.dishes, i = t.currentTarget.dataset.goodid, a = t.currentTarget.dataset.id;
        console.log(s, i, d, a);
        for (var e = 0; e < s.length; e++) for (var r = 0; r < s[e].good.length; r++) if (s[e].good[r].id == i) {
            console.log(s[e].good[r]);
            var n = 1;
            Number(s[e].good[r].start_num) == Number(t.currentTarget.dataset.num) && (n = Number(s[e].good[r].start_num));
            var d = Number(t.currentTarget.dataset.num) - n;
            console.log(d, n), wx.showLoading({
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
                        for (var a = 0; a < s.length; a++) for (var e = 0; e < s[a].good.length; e++) s[a].good[e].id == i && (s[a].good[e].quantity = s[a].good[e].quantity - n);
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
        }
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
                } else "超出库存!" == t.data ? wx.showModal({
                    title: "提示",
                    content: "库存不足!请重新选择"
                }) : "超出购买限制!" == t.data && wx.showModal({
                    title: "提示",
                    content: "超出购买限制!"
                });
            }
        });
    },
    cartdec: function(t) {
        var a = t.currentTarget.dataset.itemIndex, e = t.currentTarget.dataset.parentindex, o = this.data.dishes, s = this.data.cart_list.res, i = t.currentTarget.dataset.goodid, r = this, n = this.data.dishes[e].good[a], d = wx.getStorageSync("users").id, l = this.data.store_id;
        console.log(o, s, a, e, i, n, d, l);
        for (var c = 0; c < s.length; c++) if (s[c].good_id == i) {
            var g = 1;
            Number(n.start_num) == Number(s[c].num) && (g = Number(n.start_num));
            var u = Number(s[c].num) - g, h = s[c].id;
            console.log(s[c], u, h), wx.showLoading({
                title: "正在加载",
                mask: !0
            }), app.util.request({
                url: "entry/wxapp/UpdCar",
                cachetime: "0",
                data: {
                    num: u,
                    id: h
                },
                success: function(t) {
                    if (console.log(t), "1" == t.data) {
                        for (var a = 0; a < o.length; a++) for (var e = 0; e < o[a].good.length; e++) o[a].good[e].id == i && (o[a].good[e].quantity = o[a].good[e].quantity - g);
                        r.setData({
                            dishes: o
                        }), r.gwcreload();
                    }
                    "超出库存!" == t.data && wx.showModal({
                        title: "提示",
                        content: "超出库存!"
                    });
                }
            });
        }
    },
    isInArray: function(t, a) {
        for (var e = 0; e < t.length; e++) if (a === t[e].good_id) return !0;
        return !1;
    },
    cartadd: function(t) {
        var a = t.currentTarget.dataset.itemIndex, e = t.currentTarget.dataset.parentindex, o = this.data.dishes, s = this.data.cart_list.res, i = t.currentTarget.dataset.goodid, r = this, n = this.data.dishes[e].good[a], d = wx.getStorageSync("users").id, l = this.data.store_id, c = this.data.iszk && "0.00" != n.vip_money ? 1 : 0;
        console.log(a, e, i, s, n, d, l, this.data.iszk, c), console.log(r.isInArray(s, i));
        var g = 1;
        "0" == n.start_num || r.isInArray(s, i) || (g = Number(n.start_num)), console.log(g), 
        wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/AddCar",
            cachetime: "0",
            data: {
                money: c ? n.vip_money : n.money,
                good_id: i,
                store_id: l,
                user_id: d,
                num: g,
                spec: "",
                combination_id: "",
                box_money: n.box_money
            },
            success: function(t) {
                if (console.log(t), "1" == t.data) {
                    for (var a = 0; a < o.length; a++) for (var e = 0; e < o[a].good.length; e++) o[a].good[e].id == i && (o[a].good[e].quantity = g + o[a].good[e].quantity);
                    r.setData({
                        dishes: o
                    }), console.log(o), r.gwcreload();
                } else "超出库存!" == t.data ? wx.showModal({
                    title: "提示",
                    content: "库存不足!请重新选择"
                }) : "超出购买限制!" == t.data && wx.showModal({
                    title: "提示",
                    content: "超出购买限制!"
                });
            }
        });
    },
    spggck: function(t) {
        var d = t.currentTarget.dataset.itemIndex, l = t.currentTarget.dataset.parentindex, c = t.currentTarget.dataset.goodid, g = this;
        console.log(d, l, c), app.util.request({
            url: "entry/wxapp/GoodInfo",
            cachetime: "0",
            data: {
                good_id: c
            },
            success: function(t) {
                console.log(t.data);
                var a = t.data.spec, e = t.data.name;
                for (var o in a) for (var s in a[o].spec_val) a[o].spec_val[s].checked = 0 == s;
                g.setData({
                    gg: a,
                    spname: e
                }), console.log(a);
                var i = [], r = !0;
                for (var o in a) {
                    var n = !1;
                    for (var s in a[o].spec_val) if (a[o].spec_val[s].checked) {
                        i.push(a[o].spec_val[s].spec_val_name), n = !0;
                        break;
                    }
                    if (!n) {
                        r = !1;
                        break;
                    }
                }
                console.log(c, i, i.toString()), r && (wx.showLoading({
                    title: "正在加载",
                    mask: !0
                }), app.util.request({
                    url: "entry/wxapp/GgZh",
                    cachetime: "0",
                    data: {
                        combination: i.toString(),
                        good_id: c
                    },
                    success: function(t) {
                        console.log(t), g.setData({
                            spggtoggle: !1,
                            gginfo: t.data,
                            itemIndex: d,
                            parentindex: l
                        });
                    }
                }));
            }
        });
    },
    attrClick: function(t) {
        var a = this, e = this.data.gginfo.good_id, o = t.target.dataset.groupId, s = t.target.dataset.id, i = a.data.gg;
        for (var r in console.log(o, s, i), i) if (i[r].spec_id == o) for (var n in i[r].spec_val) i[r].spec_val[n].spec_val_id == s ? i[r].spec_val[n].checked = !0 : i[r].spec_val[n].checked = !1;
        a.setData({
            gg: i
        });
        var d = [], l = !0;
        for (var r in i) {
            var c = !1;
            for (var n in i[r].spec_val) if (i[r].spec_val[n].checked) {
                d.push(i[r].spec_val[n].spec_val_name), c = !0;
                break;
            }
            if (!c) {
                l = !1;
                break;
            }
        }
        console.log(e, d, d.toString()), l && (wx.showLoading({
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
                });
            }
        }));
    },
    ggaddcart: function() {
        var t = this.data.itemIndex, a = this.data.parentindex, o = this.data.dishes, e = this.data.dishes[a].good[t], s = this.data.cart_list.res, i = this, r = this.data.gginfo, n = wx.getStorageSync("users").id, d = this.data.gg, l = this.data.store_id, c = [], g = !0;
        for (var u in d) {
            var h = !1;
            for (var p in d[u].spec_val) if (d[u].spec_val[p].checked) {
                c.push(d[u].spec_name + ":" + d[u].spec_val[p].spec_val_name), h = !0;
                break;
            }
            if (!h) {
                g = !1;
                break;
            }
        }
        console.log("加入购物车", t, a, o, s, e, r, n, l, d, c, c.toString()), g && (wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/AddCar",
            cachetime: "0",
            data: {
                money: r.wm_money,
                good_id: r.good_id,
                store_id: l,
                user_id: n,
                num: 1,
                spec: c.toString(),
                combination_id: r.id,
                box_money: r.box_money
            },
            success: function(t) {
                if (console.log(t), "1" == t.data) {
                    for (var a = 0; a < o.length; a++) for (var e = 0; e < o[a].good.length; e++) o[a].good[e].id == r.good_id && o[a].good[e].quantity++;
                    i.setData({
                        dishes: o
                    }), i.gwcreload(), i.setData({
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
                user_id: t
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
    scrolltolower: function() {
        var s = this.data.dishes, i = this, e = wx.getStorageSync("users").id, o = i.data.store_id, r = this.data.loadindex;
        if (console.log(s, e, o, r), i.setData({
            loadMore: !1
        }), r < s.length && 0 == s[r].good.length) {
            var t = s[r].id;
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
                    s[r].good = t.data, app.util.request({
                        url: "entry/wxapp/MyCar",
                        cachetime: "0",
                        data: {
                            store_id: o,
                            user_id: e
                        },
                        success: function(t) {
                            console.log(t);
                            for (var a = t.data.res, e = 0; e < a.length; e++) for (var o = 0; o < s[r].good.length; o++) a[e].good_id == s[r].good[o].id && (s[r].good[o].quantity = s[r].good[o].quantity + Number(a[e].num));
                            console.log(s), i.setData({
                                dishes: s,
                                loadindex: r + 1,
                                loadMore: !0
                            }), r == s.length - 1 && (console.log("alldie"), i.setData({
                                cpjzz: !1
                            }));
                        }
                    });
                }
            });
        } else console.log("alldie"), i.setData({
            cpjzz: !1
        });
    },
    scroll: function(t) {
        console.log(t);
        this.data.dishes;
        var a = this.data.dataheith, e = this.data.catalogSelect;
        console.log(t.detail.scrollTop, a, e);
        for (var o = t.detail.scrollTop, s = 0; s < a.length; s++) if (o <= a[s]) {
            console.log(s), console.log(o, e), this.setData({
                catalogSelect: s,
                toType: "type" + (s - 2)
            });
            break;
        }
    },
    selectMenu: function(t) {
        var a = this.data.dishes, e = this, o = wx.getStorageSync("users").id, s = e.data.store_id, i = t.currentTarget.dataset.itemIndex;
        console.log(a, o, s, i), this.setData({
            catalogSelect: t.currentTarget.dataset.itemIndex,
            toView: "order" + i.toString(),
            toType: "type" + (i - 2),
            scroll: ""
        }), setTimeout(function() {
            e.setData({
                scroll: "scroll"
            });
        }, 500);
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
                        user_id: a,
                        store_id: e
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
        if (console.log(a, e), a <= 0) t = "￥" + this.data.start_at + "元起送", null == this.data.start_at && (t = "请选择商品"); else if (a < e) {
            var o = e - a;
            console.log(o), t = "还差" + o.toFixed(2) + "元起送";
        } else console.log(a), t = "去结算";
        this.setData({
            subtext: t
        });
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
        var c = this, a = c.data.store_id, d = util.formatTime(new Date()).slice(11, 16);
        app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: a,
                type: 2
            },
            success: function(t) {
                console.log(t.data);
                var a = t.data.store.time, e = t.data.store.time2, o = t.data.store.time3, s = t.data.store.time4, i = t.data.store.is_rest;
                console.log("当前的系统时间为" + d), console.log("商家的营业时间从" + a + "至" + e, o + "至" + s), 
                1 == i ? (c.setData({
                    yysjtoggle: !1
                }), console.log("商家正在休息" + i)) : console.log("商家正在营业" + i), a < s ? a < d && d < e || o < d && d < s || o < d && s < o ? (console.log("商家正常营业"), 
                c.setData({
                    time: 1
                })) : d < a || e < d && d < o ? (console.log("商家还没开店呐，稍等一会儿可以吗？"), c.setData({
                    time: 2,
                    yysjtoggle: !1
                })) : s < d && (console.log("商家以及关店啦，明天再来吧"), c.setData({
                    time: 3,
                    yysjtoggle: !1
                })) : s < a && (a < d && d < e || o < d && s < d || d < o && d < s ? (console.log("商家正常营业"), 
                c.setData({
                    time: 1
                })) : d < a || e < d && d < o ? (console.log("商家还没开店呐，稍等一会儿可以吗？"), c.setData({
                    time: 2,
                    yysjtoggle: !1
                })) : s < d && (console.log("商家以及关店啦，明天再来吧"), c.setData({
                    time: 3,
                    yysjtoggle: !1
                })));
                for (var r = 0; r < t.data.store.environment.length; r++) t.data.store.environment[r] = c.data.url + t.data.store.environment[r];
                for (var n = 0; n < t.data.store.yyzz.length; n++) t.data.store.yyzz[n] = c.data.url + t.data.store.yyzz[n];
                "" != t.data.storeset.wm_name && c.setData({
                    navbar: [ t.data.storeset.wm_name, "评价", "详情" ]
                }), c.setData({
                    psf: t.data.psf,
                    reduction: t.data.reduction,
                    store: t.data.store,
                    storeset: t.data.storeset,
                    start_at: t.data.store.start_at,
                    xtxx: getApp().xtxx
                });
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
                    console.log(t.data);
                }
            });
            var a = util.formatTime(new Date()).substring(0, 10).replace(/\//g, "-");
            console.log(t, a), c.setData({
                userinfo: t
            }), "" != t.dq_time && t.dq_time >= a.toString() && c.setData({
                iszk: !0
            });
            var d = wx.getStorageSync("users").id, l = c.data.store_id;
            console.log("uid", d), c.Coupons(), app.util.request({
                url: "entry/wxapp/SaveCollection",
                cachetime: "0",
                data: {
                    store_id: l,
                    user_id: d
                },
                success: function(t) {
                    console.log(t), c.setData({
                        issc: t.data
                    });
                }
            }), app.util.request({
                url: "entry/wxapp/Hot",
                cachetime: "0",
                data: {
                    store_id: l,
                    type: 2
                },
                success: function(t) {
                    if (console.log(t.data), 0 < t.data.length) {
                        var n = new Array(), a = new Object();
                        a.good = t.data, a.type_name = "热销", a.id = "0", n.push(a), app.util.request({
                            url: "entry/wxapp/DishesList",
                            cachetime: "0",
                            data: {
                                store_id: l,
                                type: 2
                            },
                            success: function(t) {
                                console.log(t.data);
                                for (var i = n.concat(t.data), a = 0; a < i.length; a++) for (var e = 0; e < i[a].good.length; e++) i[a].good[e].quantity = Number(i[a].good[e].quantity);
                                console.log(i);
                                for (var o = 0, s = [], r = 0; r < i.length; r++) o += 105 * i[r].good.length, s.push(o);
                                console.log(i), c.setData({
                                    cpjzz: !1,
                                    dataheith: s
                                }), app.util.request({
                                    url: "entry/wxapp/MyCar",
                                    cachetime: "0",
                                    data: {
                                        store_id: l,
                                        user_id: d
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
                        url: "entry/wxapp/DishesList",
                        cachetime: "0",
                        data: {
                            store_id: l,
                            type: 2
                        },
                        success: function(t) {
                            console.log(t.data);
                            for (var i = t.data, a = 0; a < i.length; a++) for (var e = 0; e < i[a].good.length; e++) i[a].good[e].quantity = Number(i[a].good[e].quantity);
                            console.log(i);
                            for (var o = 0, s = [], r = 0; r < i.length; r++) o += 105 * i[r].good.length, s.push(o);
                            console.log(i), c.setData({
                                cpjzz: !1,
                                dataheith: s
                            }), app.util.request({
                                url: "entry/wxapp/MyCar",
                                cachetime: "0",
                                data: {
                                    store_id: l,
                                    user_id: d
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
    },
    onShareAppMessage: function() {
        return {
            title: this.data.store.name,
            path: "/zh_cjdianc/pages/takeout/takeoutindex?storeid=" + this.data.store_id,
            success: function(t) {},
            fail: function(t) {}
        };
    }
});