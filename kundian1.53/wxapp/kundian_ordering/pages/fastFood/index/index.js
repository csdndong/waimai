var _Page;

function _defineProperty(t, e, a) {
    return e in t ? Object.defineProperty(t, e, {
        value: a,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : t[e] = a, t;
}

var app = getApp(), util = require("../../../utils/util.js"), DIFF_HEIGHT1 = 30, DIFF_HEIGHT2 = 26, setTime = 2e3;

Page((_defineProperty(_Page = {
    data: {
        closeModels: !0,
        notReduce: !1,
        specInfo: {},
        currentspecId: 0,
        currentspecPrice: 0,
        specState: !1,
        hidden: !1,
        state: !1,
        height: 0,
        heights: 0,
        top: 200,
        lessScrean: !1,
        currentClass: 0,
        currentIndex: 0,
        style: "",
        listHeight: [],
        scrollTop: 0,
        selectList: [],
        totalPrice: 0,
        totalNum: 0,
        shopImg: [],
        goToPay: 1,
        loading: !0
    },
    onLoad: function(t) {
        var n = this, e = wx.getStorageSync("kundian_ordering_uid"), a = app.siteInfo.uniacid;
        if (0 != e) {
            app.util.request({
                url: "entry/wxapp/order",
                data: {
                    control: "take",
                    op: "getGoodsData",
                    uniacid: a
                },
                success: function(t) {
                    var e = t.data, a = e.goodsType, s = e.aboutData, i = e.shopImg;
                    n.setData({
                        typeData: a,
                        aboutData: s,
                        currentIndex: a[0].id,
                        shopImg: i,
                        loading: !1
                    });
                }
            }), app.globalData.windowHeight < 600 && (n.data.lessScrean = !0);
            n.setData({
                lessScrean: n.data.lessScrean,
                bottom: 100
            });
        } else wx.redirectTo({
            url: "../../login/index"
        });
    },
    scroll: function(t) {
        var s = this, e = s.data.lessScrean, i = t.detail.scrollTop, n = 0, c = void 0;
        e ? s.data.typeData.map(function(t, e) {
            var a = 26 + 85 * s.length(t.id);
            n <= i && (c = t.id), n += a;
        }) : s.data.typeData.map(function(t, e) {
            var a = 30 + 100 * s.length(t.id);
            n <= i && (c = t.id), n += a;
        }), s.data.currentIndex != c && s.setData({
            currentIndex: c
        });
    },
    length: function(t) {
        for (var e = this.data.typeData, a = 0; a < e.length; a++) if (e[a].id == t) return e[a].items.length;
    },
    changeIndex: function(t) {
        var e = t.currentTarget.dataset.index;
        2 == e ? wx.switchTab({
            url: "../../order/index/index"
        }) : this.setData({
            currentClass: e
        });
    },
    change: function(t) {
        var e = t.detail.current, a = this.data.currentClass;
        2 == e ? (this.setData({
            currentClass: a
        }), wx.navigateTo({
            url: "../../order/index/index"
        })) : this.setData({
            currentClass: e
        });
    },
    changeType: function(t) {
        var e = t.currentTarget.dataset.index;
        this.setData({
            view: e
        });
    },
    preventTouchMove: function() {},
    addCommon: function(s, i, t, e) {
        var a = s.data.typeData;
        a.map(function(t) {
            t.items.map(function(e) {
                if (e.id === i) if (e.selectNum++, e.count--, 0 == s.data.selectList.length) s.data.selectList.push(e); else {
                    var a = !1;
                    s.data.selectList.map(function(t) {
                        t.id === i && (t.selectNum = e.selectNum, a = !0);
                    }), a || s.data.selectList.push(e);
                }
            });
        }), s.setData({
            typeData: a,
            selectList: s.data.selectList
        }), s.setHeight(), s.sumPrice(s.data.selectList);
    },
    addItem: util.throttle(function(t) {
        var e = t.currentTarget.dataset.id, a = t.changedTouches[0].pageX, s = t.changedTouches[0].pageY;
        this.addCommon(this, e, a, s), util.cartAnimation(a, s, this);
    }, 0),
    addItems: util.throttle(function(t) {
        var e = t.currentTarget.dataset.id, a = t.currentTarget.dataset.proid;
        this.addCommon(this, e, a);
    }, 0),
    reduceItem: util.throttle(function(t) {
        var a = this, s = t.currentTarget.dataset.id;
        a.data.typeData.map(function(t) {
            t.items.map(function(t) {
                t.id === s && 1 <= t.selectNum && (t.selectNum--, t.count++, a.data.selectList.map(function(t, e) {
                    t.id === s && (t.selectNum--, 0 == t.selectNum && a.data.selectList.splice(e, 1));
                }));
            });
        }), a.setData({
            typeData: a.data.typeData,
            selectList: a.data.selectList
        }), a.sumPrice(a.data.selectList), a.setHeight();
    }, 0),
    reduceSpecNum: util.throttle(function(t) {
        var a = this, s = t.currentTarget.dataset.id, i = t.currentTarget.dataset.proid;
        a.data.typeData.map(function(t) {
            t.items.map(function(t) {
                t.id == s && 1 <= t.selectNum && (t.selectNum--, a.data.selectList.map(function(t, e) {
                    t.id === s && (t.selectNum--, t.spec.map(function(t) {
                        t.id === i && t.selectNum--;
                    }), 0 == t.selectNum && a.data.selectList.splice(e, 1));
                }));
            });
        }), a.setData({
            typeData: a.data.typeData,
            selectList: a.data.selectList
        }), a.sumPrice(a.data.selectList), a.setHeight();
    }),
    clearAll: function() {
        this.setData({
            selectList: [],
            state: !1
        });
    },
    sumPrice: function(t) {
        var e = 0, a = 0, s = 0;
        t.map(function(t) {
            s += t.selectNum, t.spec ? t.spec.map(function(t) {
                a = t.price * t.selectNum + a, e = Number(e) + Number(t.price * t.selectNum);
            }) : (a += t.price * t.selectNum, e = t.discount ? Number(e) + Number(t.discount * t.price * t.selectNum) : Number(e) + Number(t.price * t.selectNum));
        });
        var i = this.data.aboutData, n = 1, c = e.toFixed(2);
        n = parseFloat(i.begin_price) > parseFloat(c) ? 1 : 2, this.setData({
            totalPrice: c,
            discount: (a - e).toFixed(1),
            totalNum: s,
            goToPay: n
        });
    },
    setHeight: function() {
        var t = 0, e = 0;
        this.data.selectList.map(function(t) {
            t.spec ? t.spec.map(function(t) {
                0 < t.selectNum && (e += 1);
            }) : e += 1;
        }), t = e <= 6 ? 100 * e : 600, this.setData({
            height: t,
            heights: t + 60
        }), 0 == this.data.selectList.length && this.setData({
            state: !1
        });
    },
    hideModel: function() {
        this.setData({
            state: !1
        });
    },
    showModel: function() {
        0 < this.data.selectList.length && this.setData({
            state: !0
        });
    },
    randomHexColor: function() {
        for (var t = Math.floor(16777216 * Math.random()).toString(16); t.length < 6; ) t = "0" + t;
        return "#" + t;
    },
    saleDetail: function() {
        this.setData({
            currentClass: 2
        });
    },
    specState: function(t) {
        var e = t.currentTarget.dataset.id, a = app.siteInfo.uniacid, i = this;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "take",
                op: "getProductSpec",
                uniacid: a,
                goods_id: e
            },
            success: function(t) {
                var e = t.data, a = e.skuData, s = e.specItem;
                i.setData({
                    skuData: a,
                    specItem: s,
                    specState: !0
                });
            }
        });
    },
    closeSpec: function() {
        this.setData({
            specState: !1
        });
    },
    changeSpec: function(t) {
        var e = this, a = t.currentTarget.dataset, s = a.itemid, i = a.valid, n = e.data, c = n.specItem, r = n.skuData, o = [];
        c.map(function(e) {
            e.specVal.map(function(t) {
                e.id == s && (t.select = !1), t.id == i && (t.select = !0), t.select && o.push(t.id);
            });
        });
        var u = o.sort(function(t, e) {
            return t - e;
        }).join(",");
        r.map(function(t) {
            t.sku_name == u && e.setData({
                selectSpec: t
            });
        }), e.setData({
            specItem: c
        });
    },
    confrim: function(t) {
        var e = this, a = t.currentTarget.dataset, s = (a.id, a.proid, e.data), i = s.typeData, n = s.selectSpec, c = (s.selectList, 
        new Array());
        i.map(function(t) {
            t.items(function(t) {
                t.id == n[0].goods_id && (t.selectSpec = n, c.push(t));
            });
        }), e.setData({
            selectList: e.data.selectList,
            specState: !1
        }), e.sumPrice(e.data.selectList), e.setHeight();
    },
    closeModels: function() {
        this.setData({
            closeModels: !0
        });
    }
}, "saleDetail", function() {
    this.setData({
        closeModels: !1
    });
}), _defineProperty(_Page, "toPay", function(t) {
    var e = this.data, a = e.selectList, s = e.totalPrice;
    wx.setStorageSync("selectList", a), s - 0 ? wx.navigateTo({
        url: "../sub_order/index?totalPrice=" + s,
        success: function(t) {},
        fail: function(t) {},
        complete: function(t) {}
    }) : wx.showToast({
        title: "请先选择商品",
        icon: "none"
    });
}), _defineProperty(_Page, "previewImg", function(t) {
    var e = t.currentTarget.dataset.index, a = this.data.shopImg;
    wx.previewImage({
        current: a[e],
        urls: a
    });
}), _defineProperty(_Page, "clearCart", function(t) {
    var e = this.data.typeData;
    e.map(function(t) {
        t.items.map(function(t) {
            t.selectNum = 0;
        });
    }), this.setData({
        selectList: [],
        goToPay: 1,
        typeData: e
    });
}), _defineProperty(_Page, "onShow", function(t) {}), _Page));