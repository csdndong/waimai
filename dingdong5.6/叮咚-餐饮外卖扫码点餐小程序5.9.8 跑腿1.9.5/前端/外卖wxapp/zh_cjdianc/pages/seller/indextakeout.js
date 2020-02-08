var qqmapsdk, app = getApp(), util = require("../../utils/util.js"), QQMapWX = require("../../utils/qqmap-wx-jssdk.js");

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
    closehbtoggle: function() {
        this.setData({
            hbtoggle: !1
        });
    },
    tozd: function(t) {
        this.setData({
            iszd: !1
        });
    },
    previewzzImage: function(t) {
        var e = t.currentTarget.dataset.urls, a = t.currentTarget.id;
        wx.previewImage({
            current: a,
            urls: e
        });
    },
    previewhjImage: function(t) {
        var e = t.currentTarget.dataset.urls, a = t.currentTarget.id;
        wx.previewImage({
            current: a,
            urls: e
        });
    },
    cartaddformSubmit: function(t) {
        console.log("formid", t.detail.formId);
        var e = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/AddFormId",
            cachetime: "0",
            data: {
                user_id: e,
                form_id: t.detail.formId
            },
            success: function(t) {
                console.log(t.data);
            }
        });
    },
    commentPicView: function(t) {
        console.log(t);
        var e = this.data.storelist, a = [], o = t.currentTarget.dataset.index, s = t.currentTarget.dataset.picindex, n = t.currentTarget.dataset.id;
        if (console.log(o, s, n), n == e[o].id) {
            var i = e[o].img;
            for (var r in i) a.push(this.data.url + i[r]);
            wx.previewImage({
                current: this.data.url + i[s],
                urls: a
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
        var e = this.data.params;
        0 == t.currentTarget.dataset.index && (e.type = "全部"), 1 == t.currentTarget.dataset.index && (e.type = "1"), 
        2 == t.currentTarget.dataset.index && (e.type = "2"), this.setData({
            pagenum: 1,
            storelist: [],
            bfstorelist: [],
            mygd: !1,
            jzgd: !0,
            pjselectedindex: t.currentTarget.dataset.index,
            params: e
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
                var e = [ {
                    name: "全部",
                    num: t.data.all
                }, {
                    name: "满意",
                    num: t.data.ok
                }, {
                    name: "不满意",
                    num: t.data.no
                } ], a = o.data.bfstorelist;
                a = function(t) {
                    for (var e = [], a = 0; a < t.length; a++) -1 == e.indexOf(t[a]) && e.push(t[a]);
                    return e;
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
                }), console.log(a);
            }
        });
    },
    Coupons: function() {
        var s = this, t = wx.getStorageSync("users").id, e = s.data.store_id;
        app.util.request({
            url: "entry/wxapp/Coupons",
            cachetime: "0",
            data: {
                store_id: e,
                user_id: t
            },
            success: function(t) {
                console.log(t.data);
                for (var e = [], a = [], o = 0; o < t.data.length; o++) "2" != t.data[o].type && "0" != t.data[o].stock && e.push(t.data[o]), 
                "2" == t.data[o].state && "2" != t.data[o].type && "0" != t.data[o].stock && a.push(t.data[o]);
                s.setData({
                    Coupons: e,
                    wlqyhq: a,
                    hbtoggle: 0 < a.length
                }), console.log(a);
            }
        });
    },
    ljlq: function(t) {
        console.log(t.currentTarget.dataset.qid);
        var e = this, a = wx.getStorageSync("users").id;
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
                    e.Coupons();
                }, 1e3));
            }
        });
    },
    submit: function() {
        var t = this.data.userinfo;
        console.log(t), "" == t.img || "" == t.name ? wx.navigateTo({
            url: "../smdc/getdl"
        }) : wx.navigateTo({
            url: "../takeout/takeoutform?storeid=" + this.data.store_id
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
        var e = t.currentTarget.dataset.itemIndex, a = t.currentTarget.dataset.parentindex, o = this.data.dishes, s = this.data.cart_list.res, n = t.currentTarget.dataset.goodid, i = this.data.dishes[a].good[e], r = wx.getStorageSync("users").id, d = this.data.store_id;
        i.goodindex = e, i.catalogSelect = a, console.log(o, s, e, a, n, i, r, d), this.setData({
            spxqinfo: i,
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
        var e = this;
        wx.showModal({
            title: "提示",
            content: "多规格商品请在购物车中删除对应的规格商品！",
            success: function(t) {
                e.setData({
                    share_modal_active: !0
                });
            }
        });
    },
    gwcdec: function(t) {
        var o = this, s = this.data.dishes, n = t.currentTarget.dataset.goodid, e = t.currentTarget.dataset.id;
        console.log(s, n, d, e);
        for (var a = 0; a < s.length; a++) for (var i = 0; i < s[a].good.length; i++) if (s[a].good[i].id == n) {
            console.log(s[a].good[i]);
            var r = 1;
            Number(s[a].good[i].start_num) == Number(t.currentTarget.dataset.num) && (r = Number(s[a].good[i].start_num));
            var d = Number(t.currentTarget.dataset.num) - r;
            console.log(d, r), wx.showLoading({
                title: "正在加载",
                mask: !0
            }), app.util.request({
                url: "entry/wxapp/UpdCar",
                cachetime: "0",
                data: {
                    num: d,
                    id: e
                },
                success: function(t) {
                    if (console.log(t), "1" == t.data) {
                        for (var e = 0; e < s.length; e++) for (var a = 0; a < s[e].good.length; a++) s[e].good[a].id == n && (s[e].good[a].quantity = s[e].good[a].quantity - r);
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
        var o = this, s = this.data.dishes, n = t.currentTarget.dataset.goodid, e = Number(t.currentTarget.dataset.num) + 1, a = t.currentTarget.dataset.id;
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
        var e = t.currentTarget.dataset.itemIndex, a = t.currentTarget.dataset.parentindex, o = this.data.dishes, s = this.data.cart_list.res, n = t.currentTarget.dataset.goodid, i = this, r = this.data.dishes[a].good[e], d = wx.getStorageSync("users").id, l = this.data.store_id;
        console.log(o, s, e, a, n, r, d, l);
        for (var c = 0; c < s.length; c++) if (s[c].good_id == n) {
            var g = 1;
            Number(r.start_num) == Number(s[c].num) && (g = Number(r.start_num));
            var u = Number(s[c].num) - g, p = s[c].id;
            console.log(s[c], u, p), wx.showLoading({
                title: "正在加载",
                mask: !0
            }), app.util.request({
                url: "entry/wxapp/UpdCar",
                cachetime: "0",
                data: {
                    num: u,
                    id: p
                },
                success: function(t) {
                    if (console.log(t), "1" == t.data) {
                        for (var e = 0; e < o.length; e++) for (var a = 0; a < o[e].good.length; a++) o[e].good[a].id == n && (o[e].good[a].quantity = o[e].good[a].quantity - g);
                        i.setData({
                            dishes: o
                        }), i.gwcreload();
                    }
                    "超出库存!" == t.data && wx.showModal({
                        title: "提示",
                        content: "超出库存!"
                    });
                }
            });
        }
    },
    isInArray: function(t, e) {
        for (var a = 0; a < t.length; a++) if (e === t[a].good_id) return !0;
        return !1;
    },
    cartadd: function(t) {
        var e = t.currentTarget.dataset.itemIndex, a = t.currentTarget.dataset.parentindex, o = this.data.dishes, s = this.data.cart_list.res, n = t.currentTarget.dataset.goodid, i = this, r = this.data.dishes[a].good[e], d = wx.getStorageSync("users").id, l = this.data.store_id, c = this.data.iszk && "0.00" != r.vip_money ? 1 : 0;
        console.log(e, a, n, s, r, d, l), console.log(i.isInArray(s, n));
        var g = 1;
        "0" == r.start_num || i.isInArray(s, n) || (g = Number(r.start_num)), console.log(g), 
        wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/AddCar",
            cachetime: "0",
            data: {
                money: c ? r.vip_money : r.money,
                good_id: n,
                store_id: l,
                user_id: d,
                num: g,
                spec: "",
                combination_id: "",
                box_money: r.box_money
            },
            success: function(t) {
                if (console.log(t), "1" == t.data) {
                    for (var e = 0; e < o.length; e++) for (var a = 0; a < o[e].good.length; a++) o[e].good[a].id == n && (o[e].good[a].quantity = g + o[e].good[a].quantity);
                    i.setData({
                        dishes: o
                    }), console.log(o), i.gwcreload();
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
                var e = t.data.spec, a = t.data.name;
                for (var o in e) for (var s in e[o].spec_val) e[o].spec_val[s].checked = 0 == s;
                g.setData({
                    gg: e,
                    spname: a
                }), console.log(e);
                var n = [], i = !0;
                for (var o in e) {
                    var r = !1;
                    for (var s in e[o].spec_val) if (e[o].spec_val[s].checked) {
                        n.push(e[o].spec_val[s].spec_val_name), r = !0;
                        break;
                    }
                    if (!r) {
                        i = !1;
                        break;
                    }
                }
                console.log(c, n, n.toString()), i && (wx.showLoading({
                    title: "正在加载",
                    mask: !0
                }), app.util.request({
                    url: "entry/wxapp/GgZh",
                    cachetime: "0",
                    data: {
                        combination: n.toString(),
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
        var e = this, a = this.data.gginfo.good_id, o = t.target.dataset.groupId, s = t.target.dataset.id, n = e.data.gg;
        for (var i in console.log(o, s, n), n) if (n[i].spec_id == o) for (var r in n[i].spec_val) n[i].spec_val[r].spec_val_id == s ? n[i].spec_val[r].checked = !0 : n[i].spec_val[r].checked = !1;
        e.setData({
            gg: n
        });
        var d = [], l = !0;
        for (var i in n) {
            var c = !1;
            for (var r in n[i].spec_val) if (n[i].spec_val[r].checked) {
                d.push(n[i].spec_val[r].spec_val_name), c = !0;
                break;
            }
            if (!c) {
                l = !1;
                break;
            }
        }
        console.log(a, d, d.toString()), l && (wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/GgZh",
            cachetime: "0",
            data: {
                combination: d.toString(),
                good_id: a
            },
            success: function(t) {
                console.log(t), e.setData({
                    gginfo: t.data
                });
            }
        }));
    },
    ggaddcart: function() {
        var t = this.data.itemIndex, e = this.data.parentindex, o = this.data.dishes, s = this, n = this.data.gginfo, a = wx.getStorageSync("users").id, i = this.data.gg, r = this.data.store_id, d = [], l = !0;
        for (var c in i) {
            var g = !1;
            for (var u in i[c].spec_val) if (i[c].spec_val[u].checked) {
                d.push(i[c].spec_name + ":" + i[c].spec_val[u].spec_val_name), g = !0;
                break;
            }
            if (!g) {
                l = !1;
                break;
            }
        }
        console.log("加入购物车", t, e, o, n, a, r, i, d, d.toString()), l && (wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/AddCar",
            cachetime: "0",
            data: {
                money: n.wm_money,
                good_id: n.good_id,
                store_id: r,
                user_id: a,
                num: 1,
                spec: d.toString(),
                combination_id: n.id,
                box_money: n.box_money
            },
            success: function(t) {
                if (console.log(t), "1" == t.data) {
                    for (var e = 0; e < o.length; e++) for (var a = 0; a < o[e].good.length; a++) o[e].good[a].id == n.good_id && o[e].good[a].quantity++;
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
        var e = this.data.dishes, a = this, t = wx.getStorageSync("users").id, o = this.data.store_id;
        console.log(e, t, o), app.util.request({
            url: "entry/wxapp/MyCar",
            cachetime: "0",
            data: {
                store_id: o,
                user_id: t
            },
            success: function(t) {
                console.log(t), console.log(e), a.setData({
                    cart_list: t.data
                }), a.subText();
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
        var s = this.data.dishes, n = this, a = wx.getStorageSync("users").id, o = n.data.store_id, i = this.data.loadindex;
        if (console.log(s, a, o, i), n.setData({
            loadMore: !1
        }), i < s.length && 0 == s[i].good.length) {
            var t = s[i].id;
            console.log("还没加载过数据", t), n.setData({
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
                    for (var e = 0; e < t.data.length; e++) t.data[e].quantity = Number(t.data[e].quantity);
                    s[i].good = t.data, app.util.request({
                        url: "entry/wxapp/MyCar",
                        cachetime: "0",
                        data: {
                            store_id: o,
                            user_id: a
                        },
                        success: function(t) {
                            console.log(t);
                            for (var e = t.data.res, a = 0; a < e.length; a++) for (var o = 0; o < s[i].good.length; o++) e[a].good_id == s[i].good[o].id && (s[i].good[o].quantity = s[i].good[o].quantity + Number(e[a].num));
                            console.log(s), n.setData({
                                dishes: s,
                                loadindex: i + 1,
                                loadMore: !0
                            }), i == s.length - 1 && (console.log("alldie"), n.setData({
                                cpjzz: !1
                            }));
                        }
                    });
                }
            });
        } else console.log("alldie"), n.setData({
            cpjzz: !1
        });
    },
    scroll: function(t) {
        60 < t.detail.scrollTop && !this.data.iszd && "2" == this.data.storeset.top_style && (wx.pageScrollTo({
            scrollTop: this.data.navzdoffsetTop
        }), this.setData({
            iszd: !0
        })), console.log(t);
        this.data.dishes;
        var e = this.data.dataheith, a = this.data.catalogSelect;
        console.log(t.detail.scrollTop, e, a);
        for (var o = t.detail.scrollTop, s = 0; s < e.length; s++) if (o <= e[s]) {
            console.log(s), console.log(o, a), this.setData({
                catalogSelect: s,
                toType: "type" + (s - 2)
            });
            break;
        }
    },
    selectMenu: function(t) {
        var e = this.data.dishes, a = this, o = wx.getStorageSync("users").id, s = a.data.store_id, n = t.currentTarget.dataset.itemIndex;
        console.log(e, o, s, n), this.setData({
            catalogSelect: t.currentTarget.dataset.itemIndex,
            toView: "order" + n.toString(),
            toType: "type" + (n - 2),
            scroll: ""
        }), setTimeout(function() {
            a.setData({
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
        var o = this, s = this.data.dishes, e = wx.getStorageSync("users").id, a = o.data.store_id;
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
                        store_id: a
                    },
                    success: function(t) {
                        if (console.log(t.data), "1" == t.data) {
                            for (var e = 0; e < s.length; e++) for (var a = 0; a < s[e].good.length; a++) s[e].good[a].quantity = 0;
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
        var t, e = parseFloat(this.data.cart_list.money), a = parseFloat(this.data.start_at);
        if (console.log(e, a), e <= 0) t = "￥" + this.data.start_at + "元起送", null == this.data.start_at && (t = "请选择商品"); else if (e < a) {
            var o = a - e;
            console.log(o), t = "还差" + o.toFixed(2) + "元起送";
        } else console.log(e), t = "去结算";
        this.setData({
            subtext: t
        });
    },
    onLoad: function(t) {
        qqmapsdk = new QQMapWX({
            key: getApp().xtxx.map_key
        }), console.log("options", t), app.setNavigationBarColor(this), app.pageOnLoad(this), 
        this.setData({
            store_id: getApp().sjid
        }), this.setData({
            params: {
                store_id: getApp().sjid,
                type: "全部",
                img: ""
            }
        }), this.getstorelist();
        var c = this, d = c.data.store_id, l = util.formatTime(new Date()).slice(11, 16);
        app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: d,
                type: 2
            },
            success: function(t) {
                console.log(t.data), wx.setNavigationBarTitle({
                    title: t.data.store.name
                }), "2" == t.data.storeset.top_style && (wx.getLocation({
                    type: "wgs84",
                    success: function(t) {
                        var e = t.latitude, a = t.longitude, o = e + "," + a;
                        console.log(o), qqmapsdk.reverseGeocoder({
                            location: {
                                latitude: e,
                                longitude: a
                            },
                            coord_type: 1,
                            success: function(t) {
                                t.result.ad_info.location;
                                console.log(t), console.log(t.result.formatted_addresses.recommend), console.log("坐标转地址后的经纬度：", t.result.ad_info.location), 
                                c.setData({
                                    weizhi: t.result.formatted_addresses.recommend
                                });
                            },
                            fail: function(t) {
                                console.log(t);
                            },
                            complete: function(t) {
                                console.log(t);
                            }
                        });
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
                                                t.authSetting["scope.userLocation"], c.onLoad();
                                            }
                                        }));
                                    }
                                });
                            }
                        });
                    },
                    complete: function(t) {}
                }), app.util.request({
                    url: "entry/wxapp/StoreAd",
                    cachetime: "0",
                    data: {
                        store_id: d
                    },
                    success: function(t) {
                        console.log(t.data), c.setData({
                            slider: t.data
                        });
                    }
                }));
                var e = t.data.store.time, a = t.data.store.time2, o = t.data.store.time3, s = t.data.store.time4, n = t.data.store.is_rest;
                console.log("当前的系统时间为" + l), console.log("商家的营业时间从" + e + "至" + a, o + "至" + s), 
                1 == n ? (c.setData({
                    yysjtoggle: !1
                }), console.log("商家正在休息" + n)) : console.log("商家正在营业" + n), e < s ? e < l && l < a || o < l && l < s || o < l && s < o ? (console.log("商家正常营业"), 
                c.setData({
                    time: 1
                })) : l < e || a < l && l < o ? (console.log("商家还没开店呐，稍等一会儿可以吗？"), c.setData({
                    time: 2,
                    yysjtoggle: !1
                })) : s < l && (console.log("商家以及关店啦，明天再来吧"), c.setData({
                    time: 3,
                    yysjtoggle: !1
                })) : s < e && (e < l && l < a || o < l && s < l || l < o && l < s ? (console.log("商家正常营业"), 
                c.setData({
                    time: 1
                })) : l < e || a < l && l < o ? (console.log("商家还没开店呐，稍等一会儿可以吗？"), c.setData({
                    time: 2,
                    yysjtoggle: !1
                })) : s < l && (console.log("商家以及关店啦，明天再来吧"), c.setData({
                    time: 3,
                    yysjtoggle: !1
                })));
                for (var i = 0; i < t.data.store.environment.length; i++) t.data.store.environment[i] = c.data.url + t.data.store.environment[i];
                for (var r = 0; r < t.data.store.yyzz.length; r++) t.data.store.yyzz[r] = c.data.url + t.data.store.yyzz[r];
                "" != t.data.storeset.wm_name && c.setData({
                    navbar: [ t.data.storeset.wm_name, "评价", "详情" ]
                }), c.setData({
                    psf: t.data.psf,
                    reduction: t.data.reduction,
                    store: t.data.store,
                    storeset: t.data.storeset,
                    start_at: t.data.store.start_at
                });
            }
        }), app.getUserInfo(function(t) {
            app.util.request({
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
            var e = util.formatTime(new Date()).substring(0, 10).replace(/\//g, "-");
            console.log(t, e), c.setData({
                userinfo: t
            }), "" != t.dq_time && t.dq_time >= e.toString() && c.setData({
                iszk: !0
            });
            var d = wx.getStorageSync("users").id, l = c.data.store_id;
            console.log("uid", d), app.util.request({
                url: "entry/wxapp/Hot",
                cachetime: "0",
                data: {
                    store_id: l,
                    type: 2
                },
                success: function(t) {
                    if (console.log(t.data), 0 < t.data.length) {
                        var r = new Array(), e = new Object();
                        e.good = t.data, e.type_name = "热销", e.id = "0", r.push(e), app.util.request({
                            url: "entry/wxapp/DishesList",
                            cachetime: "0",
                            data: {
                                store_id: l,
                                type: 2
                            },
                            success: function(t) {
                                console.log(t.data);
                                for (var n = r.concat(t.data), e = 0; e < n.length; e++) for (var a = 0; a < n[e].good.length; a++) n[e].good[a].quantity = Number(n[e].good[a].quantity);
                                console.log(n);
                                for (var o = 0, s = [], i = 0; i < n.length; i++) o += 105 * n[i].good.length, s.push(o);
                                console.log(n), c.setData({
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
                                        for (var e = t.data.res, a = 0; a < e.length; a++) for (var o = 0; o < n.length; o++) for (var s = 0; s < n[o].good.length; s++) e[a].good_id == n[o].good[s].id && (n[o].good[s].quantity = n[o].good[s].quantity + Number(e[a].num));
                                        console.log(n), c.setData({
                                            cart_list: t.data,
                                            dishes: n,
                                            isloading: !1
                                        }), c.subText(), c.Coupons();
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
                            for (var n = t.data, e = 0; e < n.length; e++) for (var a = 0; a < n[e].good.length; a++) n[e].good[a].quantity = Number(n[e].good[a].quantity);
                            console.log(n);
                            for (var o = 0, s = [], i = 0; i < n.length; i++) o += 105 * n[i].good.length, s.push(o);
                            console.log(n), c.setData({
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
                                    for (var e = t.data.res, a = 0; a < e.length; a++) for (var o = 0; o < n.length; o++) for (var s = 0; s < n[o].good.length; s++) e[a].good_id == n[o].good[s].id && (n[o].good[s].quantity = n[o].good[s].quantity + Number(e[a].num));
                                    console.log(n), c.setData({
                                        cart_list: t.data,
                                        dishes: n,
                                        isloading: !1
                                    }), c.subText(), c.Coupons();
                                }
                            });
                        }
                    });
                }
            });
        }), wx.getSystemInfo({
            success: function(t) {
                console.log(t), c.setData({
                    navzdoffsetTop: t.windowHeight / 4
                });
            }
        });
    },
    maketel: function(t) {
        var e = this.data.store.tel;
        wx.makePhoneCall({
            phoneNumber: e
        });
    },
    location: function() {
        var t = this.data.store.coordinates.split(","), e = this.data.store;
        console.log(t), wx.openLocation({
            latitude: parseFloat(t[0]),
            longitude: parseFloat(t[1]),
            address: e.address,
            name: e.name
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
            path: "/zh_cjdianc/pages/Liar/loginindex",
            success: function(t) {},
            fail: function(t) {}
        };
    }
});