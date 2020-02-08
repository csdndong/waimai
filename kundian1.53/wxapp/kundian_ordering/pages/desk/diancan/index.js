var app = getApp(), util = require("../../../utils/util.js"), DIFF_HEIGHT1 = 30, DIFF_HEIGHT2 = 26, setTime = 2e3, uniacid = app.siteInfo.uniacid;

Page({
    data: {
        notReduce: !1,
        specInfo: {},
        currentspecId: 0,
        currentspecPrice: 0,
        specState: !1,
        hidden: !1,
        height: 0,
        heights: 0,
        state: !1,
        lessScrean: !1,
        currentClass: 0,
        currentIndex: 0,
        scrollTop: 0,
        selectList: [],
        time: 30,
        distence: 1.1,
        distributedFee: 5,
        lists: [],
        totalPrice: 0,
        totalNum: 0,
        aboutData: [],
        desk_id: "",
        setData: []
    },
    onLoad: function(t) {
        var e = wx.getStorageSync("kundian_ordering_uid");
        if (console.log(t), e) {
            var c = this;
            app.globalData.windowHeight < 600 && (c.data.lessScrean = !0), app.util.request({
                url: "entry/wxapp/order",
                data: {
                    control: "desk",
                    op: "getGoodsData",
                    uniacid: uniacid,
                    desk_id: t.desk_id
                },
                success: function(t) {
                    if (2 == t.data.code && wx.showModal({
                        title: "提示",
                        content: "餐桌未用餐！",
                        success: function(t) {
                            wx.switchTab({
                                url: "../../index/index/index"
                            });
                        }
                    }), t.data.typeData) {
                        var e = t.data, a = e.typeData, s = e.aboutData, i = e.setData;
                        c.setData({
                            lists: a,
                            aboutData: s,
                            setData: i,
                            currentIndex: a[0].id
                        });
                    }
                }
            });
            var a = 100;
            c.data.disCountBanner && (a = 140);
            c.setData({
                lessScrean: c.data.lessScrean,
                desk_id: t.desk_id,
                bottom: a,
                color: []
            });
        } else wx.navigateTo({
            url: "../../login/index"
        });
    },
    changeIndex: function(t) {
        var e = t.currentTarget.dataset.index;
        2 != e ? this.setData({
            currentClass: e
        }) : wx.switchTab({
            url: "../../order/index/index?"
        });
    },
    change: function(t) {
        var e = t.detail.current, a = this.data.currentClass;
        if (2 == e) return this.setData({
            currentClass: a
        }), void wx.navigateTo({
            url: "../../order/index/index"
        });
        this.setData({
            currentClass: e
        });
    },
    changeType: function(t) {
        var e = t.currentTarget.dataset.index;
        this.setData({
            view: e
        });
    },
    reduceItem: util.throttle(function(t) {
        var a = this, s = t.currentTarget.dataset.id;
        a.data.lists.map(function(t) {
            t.items.map(function(t) {
                t.id === s && (t.spec && 1 < t.spec.length ? (a.setData({
                    notReduce: !0
                }), setTimeout(function() {
                    a.setData({
                        notReduce: !1
                    });
                }, setTime)) : t.spec && 1 == t.spec.length ? (t.selectNum--, t.spec[0].selectNum--, 
                a.data.selectList.map(function(t, e) {
                    t.id === s && (t.selectNum--, t.spec[0].selectNum--, 0 == t.selectNum && a.data.selectList.splice(e, 1));
                })) : (t.selectNum--, t.count++, a.data.selectList.map(function(t, e) {
                    t.id === s && (t.selectNum--, 0 == t.selectNum && a.data.selectList.splice(e, 1));
                })));
            });
        }), a.setData({
            lists: a.data.lists,
            selectList: a.data.selectList
        }), a.sumPrice(a.data.selectList), a.setHeight();
    }),
    reduceSpecNum: util.throttle(function(t) {
        var a = this, s = t.currentTarget.dataset.id, i = t.currentTarget.dataset.proid;
        a.data.lists.map(function(e) {
            e.items.map(function(t) {
                t.id == s && (t.selectNum--, t.spec.map(function(t) {
                    t.id === i && (t.selectNum--, e.count++);
                }), a.data.selectList.map(function(t, e) {
                    t.id === s && (t.selectNum--, t.spec.map(function(t) {
                        t.id === i && t.selectNum--;
                    }), 0 == t.selectNum && a.data.selectList.splice(e, 1));
                }));
            });
        }), a.setData({
            lists: a.data.lists,
            selectList: a.data.selectList
        }), a.sumPrice(a.data.selectList), a.setHeight();
    }),
    addCommon: function(s, i, c) {
        s.data.lists.map(function(t) {
            t.items.map(function(e) {
                if (e.id === i) if (0 < e.count) if (e.selectNum++, e.count--, e.spec && e.spec.map(function(t) {
                    t.id === c && t.selectNum++;
                }), 0 == s.data.selectList.length) s.data.selectList.push(e); else {
                    var a = !1;
                    s.data.selectList.map(function(t) {
                        t.id === i && (t.selectNum = e.selectNum, a = !0, t.spec && t.spec.map(function(t) {
                            t.id == c && t.selectNum++;
                        }));
                    }), a || s.data.selectList.push(e);
                } else wx.showToast({
                    title: "库存不足"
                });
            });
        }), s.setData({
            lists: s.data.lists,
            selectList: s.data.selectList
        }), s.setHeight(), s.sumPrice(s.data.selectList);
    },
    addItem: util.throttle(function(t) {
        var e = t.currentTarget.dataset.id, a = t.changedTouches[0].pageX, s = t.changedTouches[0].pageY;
        this.addCommon(this, e), util.cartAnimation(a, s, this);
    }),
    addItems: util.throttle(function(t) {
        var e = t.currentTarget.dataset.id, a = t.currentTarget.dataset.proid;
        this.addCommon(this, e, a);
    }),
    sumPrice: function(t) {
        var e = 0, a = 0, s = 0;
        t.map(function(t) {
            s += t.selectNum, t.spec ? t.spec.map(function(t) {
                a = t.price * t.selectNum + a, e = Number(e) + Number(t.price * t.selectNum);
            }) : (a += t.price * t.selectNum, e = t.discount ? Number(e) + Number(t.discount * t.price * t.selectNum) : Number(e) + Number(t.price * t.selectNum));
        }), this.setData({
            totalPrice: e.toFixed(2),
            discount: (a - e).toFixed(1),
            totalNum: s
        });
    },
    clearAll: function() {
        this.data.lists.map(function(t) {
            t.items.map(function(t) {
                t.selectNum = 0, t.spec && t.spec.map(function(t) {
                    t.selectNum = 0;
                });
            });
        }), this.setData({
            lists: this.data.lists,
            selectList: [],
            state: !1
        });
    },
    sumHeight: function(t) {
        var e = this, a = [], s = 0;
        a.push(s), t.map(function(t) {
            s = e.data.lessScrean ? s + 85 * t.items.length + 26 : s + 100 * t.items.length + 30, 
            a.push(s);
        }), this.setData({
            listHeight: a
        }), console.log(a);
    },
    scroll: function(t) {
        var s = this, e = s.data.lessScrean, i = t.detail.scrollTop, c = 0, n = void 0;
        e ? s.data.lists.map(function(t, e) {
            var a = 26 + 85 * s.length(t.id);
            c <= i && (n = t.id), c += a;
        }) : s.data.lists.map(function(t, e) {
            var a = 30 + 100 * s.length(t.id);
            c <= i && (n = t.id), c += a;
        }), s.data.currentIndex != n && s.setData({
            currentIndex: n
        });
    },
    length: function(t) {
        for (var e = this.data.lists, a = 0; a < e.length; a++) if (e[a].id == t) return e[a].items.length;
    },
    preventTouchMove: function() {},
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
    randomHexColor: function() {
        for (var t = Math.floor(16777216 * Math.random()).toString(16); t.length < 6; ) t = "0" + t;
        return "#" + t;
    },
    specState: function(t) {
        var e = t.currentTarget.dataset.id, a = void 0;
        this.data.lists.map(function(t) {
            t.items.map(function(t) {
                t.id === e && (a = t);
            });
        }), this.setData({
            specInfo: a,
            currentspecId: a.spec[0].id,
            currentspecPrice: a.spec[0].price,
            specState: !0
        });
    },
    closeSpec: function() {
        this.setData({
            specState: !1
        });
    },
    changeSpec: function(t) {
        var e = this, a = t.currentTarget.dataset.id;
        this.data.specInfo.spec.map(function(t) {
            t.id == a && e.setData({
                currentspecId: t.id,
                currentspecPrice: t.price
            });
        });
    },
    confrim: function(t) {
        var s = this, i = t.currentTarget.dataset.id, c = t.currentTarget.dataset.proid;
        s.data.lists.map(function(t) {
            t.items.map(function(a) {
                a.id == i && (a.selectNum++, a.spec.map(function(t) {
                    if (t.id === c) if (t.selectNum++, 0 == s.data.selectList.length) s.data.selectList.push(a); else {
                        var e = !1;
                        s.data.selectList.map(function(t) {
                            t.id === i && (t.selectNum = a.selectNum, t.spec.map(function(t) {
                                t.id == c && (t.selectNum++, e = !0);
                            }));
                        }), e || s.data.selectList.push(a);
                    }
                }));
            });
        }), s.setData({
            lists: s.data.lists,
            selectList: s.data.selectList,
            specState: !1
        }), s.sumPrice(s.data.selectList), s.setHeight();
    },
    confirmOrder: function(t) {
        if (this.data.totalPrice) {
            var e = this.data, a = e.selectList, s = e.totalPrice, i = e.totalNum, c = e.desk_id;
            wx.setStorageSync("selectList", a), wx.redirectTo({
                url: "../sure_order/index?totalPrice=" + s + "&totalNum=" + i + "&desk_id=" + c
            });
        } else wx.showToast({
            title: "您未选菜品",
            icon: "none"
        });
    },
    previewImg: function(t) {
        var e = t.currentTarget.dataset.index, a = this.data.setData;
        wx.previewImage({
            current: a.value[e],
            urls: a.value
        });
    },
    onShow: function(t) {
        var a = this, e = a.data.desk_id;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "desk",
                op: "getGoodsData",
                uniacid: uniacid,
                desk_id: e
            },
            success: function(t) {
                if (2 == t.data.code && wx.showModal({
                    title: "提示",
                    content: "餐桌未用餐！",
                    success: function(t) {
                        wx.switchTab({
                            url: "../../index/index/index"
                        });
                    }
                }), t.data.typeData) {
                    var e = t.data.typeData;
                    a.setData({
                        lists: e,
                        aboutData: t.data.aboutData,
                        setData: t.data.setData,
                        currentIndex: e[0].id
                    });
                }
            }
        });
        var s = 100;
        a.data.disCountBanner && (s = 140);
        a.setData({
            lessScrean: a.data.lessScrean,
            bottom: s,
            color: []
        }), wx.removeStorageSync("selectList"), this.setData({
            selectList: []
        });
    }
});