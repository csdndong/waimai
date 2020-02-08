var qqmapsdk, _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(t) {
    return typeof t;
} : function(t) {
    return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : typeof t;
}, a = getApp(), util = require("../../utils/util.js"), QQMapWX = require("../../utils/qqmap-wx-jssdk.js");

Page({
    data: {
        characteristicList: [ {
            text: "0配送费"
        }, {
            text: "0元起送"
        } ],
        sortList: [ {
            sort: "综合排序",
            image: ""
        }, {
            sort: "销量最高",
            image: ""
        }, {
            sort: "起送价最低",
            image: ""
        }, {
            sort: "配送费最低",
            image: ""
        } ],
        discountList: [ {
            icon: "减",
            iconColor: "#FF635B",
            text: "满减优惠",
            zdname: " and d.money is not null"
        }, {
            icon: "新",
            iconColor: "#34aaff",
            text: "新用户立减",
            zdname: " and c.xyh_open=1"
        }, {
            icon: "提",
            iconColor: "#6FDF64",
            text: "到店自提",
            zdname: " and c.is_zt=1"
        } ],
        categoryList: {
            pageone: [ {
                name: "美食",
                src: "/pages/images/1.png"
            }, {
                name: "甜点饮品",
                src: "/pages/images/2.png"
            }, {
                name: "美团超市",
                src: "/pages/images/3.png"
            }, {
                name: "正餐精选",
                src: "/pages/images/4.png"
            }, {
                name: "生鲜果蔬",
                src: "/pages/images/5.png"
            }, {
                name: "全部商家",
                src: "/pages/images/6.png"
            }, {
                name: "免配送费",
                src: "/pages/images/7.png"
            }, {
                name: "新商家",
                src: "/pages/images/8.png"
            } ],
            pagetwo: [ {
                name: "美食",
                src: "/pages/images/1.png"
            }, {
                name: "甜点饮品",
                src: "/pages/images/2.png"
            }, {
                name: "美团超市",
                src: "/pages/images/3.png"
            }, {
                name: "正餐精选",
                src: "/pages/images/4.png"
            }, {
                name: "生鲜果蔬",
                src: "/pages/images/5.png"
            }, {
                name: "全部商家",
                src: "/pages/images/6.png"
            }, {
                name: "免配送费",
                src: "/pages/images/7.png"
            }, {
                name: "新商家",
                src: "/pages/images/8.png"
            } ]
        },
        params: {
            nopsf: 2,
            nostart: 2,
            yhhd: ""
        },
        issx: !1,
        selected: 0,
        mask1Hidden: !0,
        mask2Hidden: !0,
        animationData: "",
        location: "",
        characteristicSelected: [ !1, !1 ],
        discountSelected: null,
        selectedNumb: 0,
        sortSelected: "综合排序",
        pagenum: 1,
        storelist: [],
        bfstorelist: [],
        mygd: !1,
        jzgd: !0,
        isjzz: !0
    },
    refresh: function() {
        var a = this, t = wx.createAnimation({
            duration: 3e3,
            timingFunction: "linear"
        });
        t.opacity(.2).step({
            duration: 200
        }).opacity(.3).scale(1.1, 1.1).translate3d(0, 10, 0).step({
            duration: 200
        }).opacity(.4).scale(1.2, 1.2).translate3d(0, 30, 0).step({
            duration: 200
        }).opacity(.5).scale(1.3, 1.3).translate3d(0, 50, 0).step({
            duration: 200
        }).opacity(.6).scale(1.4, 1.4).translate3d(0, 70, 0).step({
            duration: 200
        }).opacity(.7).translate3d(0, 90, 0).step({
            duration: 200
        }).opacity(.8).translate3d(0, 110, 0).step({
            duration: 200
        }).opacity(.9).translate3d(0, 130, 0).step({
            duration: 200
        }).opacity(1).translate3d(0, 140, 0).step({
            duration: 200
        }), a.setData({
            animationData_4: t.export()
        });
        var e = wx.createAnimation({
            duration: 3e3,
            timingFunction: "linear"
        });
        e.opacity(.2).step({
            duration: 200
        }).opacity(.3).scale(1.1, 1.1).translate3d(-10, 10, 0).step({
            duration: 200
        }).opacity(.4).scale(1.2, 1.2).translate3d(-15, 30, 0).step({
            duration: 200
        }).opacity(.5).scale(1.3, 1.3).translate3d(-20, 50, 0).step({
            duration: 200
        }).opacity(.6).scale(1.4, 1.4).translate3d(-25, 70, 0).step({
            duration: 200
        }).opacity(.7).translate3d(-30, 90, 0).step({
            duration: 200
        }).opacity(.8).translate3d(-35, 110, 0).step({
            duration: 200
        }).opacity(.9).translate3d(-40, 130, 0).step({
            duration: 200
        }).opacity(1).translate3d(-30, 150, 0).step({
            duration: 200
        }), a.setData({
            animationData_5: e.export()
        });
        var s = wx.createAnimation({
            duration: 3e3,
            timingFunction: "linear"
        });
        s.translate3d(0, 0, 0).scale(0, 0).translate3d(0, 0, 0).step({
            duration: 200
        }).scale(.2, .2).translate3d(0, 20, 0).step({
            duration: 200
        }).scale(.4, .4).translate3d(0, 40, 0).step({
            duration: 200
        }).scale(.6, .6).translate3d(0, 60, 0).step({
            duration: 200
        }).scale(.8, .8).translate3d(0, 80, 0).step({
            duration: 200
        }).scale(1, 1).translate3d(0, 90, 0).step({
            duration: 200
        }), a.setData({
            animationData: s.export()
        });
        var n = wx.createAnimation({
            duration: 3e3,
            timingFunction: "linear"
        });
        n.scale(0, 0).step({
            duration: 100
        }).scale(.2, .2).step({
            duration: 100
        }).scale(.4, .4).step({
            duration: 100
        }).scale(.6, .6).step({
            duration: 200
        }).scale(.8, .8).step({
            duration: 200
        }).scale(1, 1).step({
            duration: 200
        }), a.setData({
            animationData_1: n.export()
        }), setTimeout(function() {
            console.log("开始执行");
            var t = wx.createAnimation({
                duration: 3e3,
                timingFunction: "linear"
            });
            t.opacity(.1).step({
                duration: 100
            }).opacity(.3).scale(1.1, 1.1).translate3d(10, 10, 0).step({
                duration: 100
            }).opacity(.4).scale(1.2, 1.2).translate3d(11, 15, 0).step({
                duration: 100
            }).opacity(.5).scale(1.3, 1.3).translate3d(12, 20, 0).step({
                duration: 100
            }).opacity(.6).scale(1.4, 1.4).translate3d(13, 25, 0).step({
                duration: 100
            }).opacity(.7).translate3d(14, 30, 0).step({
                duration: 100
            }).opacity(.8).translate3d(15, 35, 0).step({
                duration: 100
            }).opacity(.9).translate3d(16, 40, 0).step({
                duration: 100
            }).opacity(1).translate3d(17, 45, 0).step({
                duration: 100
            }), a.setData({
                animationData_2: t.export()
            });
        }, 700), setTimeout(function() {
            var t = wx.createAnimation({
                duration: 3e3,
                timingFunction: "linear"
            });
            t.opacity(.1).step({
                duration: 100
            }).opacity(.3).scale(1.1, 1.1).translate3d(-100, 10, 0).step({
                duration: 70
            }).opacity(.4).scale(1.2, 1.2).translate3d(-110, 15, 0).step({
                duration: 70
            }).opacity(.5).scale(1.3, 1.3).translate3d(-120, 20, 0).step({
                duration: 70
            }).opacity(.6).scale(1.4, 1.4).translate3d(-130, 25, 0).step({
                duration: 100
            }).opacity(.7).translate3d(-120, 30, 0).step({
                duration: 130
            }).opacity(.8).translate3d(-110, 35, 0).step({
                duration: 130
            }).opacity(.9).translate3d(-100, 40, 0).step({
                duration: 130
            }).opacity(1).translate3d(-90, 45, 0).step({
                duration: 130
            }), a.setData({
                animationData_6: t.export()
            });
        }, 700), setTimeout(function() {
            var t = wx.createAnimation({
                duration: 3e3,
                timingFunction: "linear"
            });
            t.opacity(.1).translate3d(0, 0, 0).step({
                duration: 100
            }).opacity(.3).scale(1.1, 1.1).translate3d(10, 10, 0).step({
                duration: 70
            }).opacity(.4).scale(1.2, 1.2).translate3d(20, 15, 0).step({
                duration: 70
            }).opacity(.5).scale(1.3, 1.3).translate3d(30, 20, 0).step({
                duration: 70
            }).opacity(.6).scale(1.4, 1.4).translate3d(40, 25, 0).step({
                duration: 100
            }).opacity(.7).translate3d(50, 30, 0).step({
                duration: 200
            }).opacity(.8).translate3d(60, 35, 0).step({
                duration: 200
            }).opacity(.9).translate3d(70, 40, 0).step({
                duration: 200
            }).opacity(1).translate3d(80, 45, 0).step({
                duration: 300
            }), a.setData({
                animationData_7: t.export()
            });
        }, 700), setTimeout(function() {
            var t = wx.createAnimation({
                duration: 3e3,
                timingFunction: "ease-in-out"
            });
            t.opacity(.1).translate3d(20, -50, 0).step({
                duration: 300
            }).opacity(1).translate3d(40, 240, 200).step({
                duration: 3e3
            }), a.setData({
                animationData_8: t.export()
            });
        }, 300), setTimeout(function() {
            setInterval(function() {
                var t = wx.createAnimation({
                    duration: 3e3,
                    timingFunction: "linear"
                });
                t.scale(1, 1).step({
                    duration: 300
                }).scale(1.1, 1.1).step({
                    duration: 300
                }), a.setData({
                    animationData_9: t.export()
                });
            }, 600);
        }, 1200);
    },
    onTapTag: function(t) {
        var a = this.data.params;
        "1" == t.currentTarget.dataset.index && (a.by = "juli asc"), "2" == t.currentTarget.dataset.index && (a.by = "sales desc"), 
        console.log(a, t.currentTarget.dataset.index), this.setData({
            sortSelected: this.data.sortList[0].sort,
            selected: t.currentTarget.dataset.index,
            params: a,
            pagenum: 1,
            storelist: [],
            bfstorelist: [],
            mygd: !1,
            jzgd: !0
        }), this.getstorelist();
    },
    sortSelected: function(t) {
        var a = this.data.params;
        "0" == t.currentTarget.dataset.sortindex && (a.by = "number asc"), "1" == t.currentTarget.dataset.sortindex && (a.by = "score desc"), 
        "2" == t.currentTarget.dataset.sortindex && (a.by = "start_at asc"), "3" == t.currentTarget.dataset.sortindex && (a.by = "ps_money asc"), 
        console.log(a, t.currentTarget.dataset.index, t.currentTarget.dataset.sortindex + 1), 
        this.setData({
            selected: t.currentTarget.dataset.index,
            sortSelected: this.data.sortList[t.currentTarget.dataset.sortindex].sort,
            params: a,
            pagenum: 1,
            storelist: [],
            bfstorelist: [],
            mygd: !1,
            jzgd: !0
        }), this.getstorelist();
    },
    finish: function() {
        var t = this.data.params, a = this.data.characteristicSelected, e = this.data.characteristicList, s = this.data.discountSelected;
        this.setData({
            issx: !0
        });
        for (var n = 0; n < a.length; n++) a[n] && ("0配送费" == e[n].text && (t.nopsf = 1), 
        "0元起送" == e[n].text && (t.nostart = 1));
        t.yhhd = null != s ? this.data.discountList[s].zdname : "", this.setData({
            params: t,
            pagenum: 1,
            storelist: [],
            bfstorelist: [],
            mygd: !1,
            jzgd: !0
        }), this.getstorelist(), console.log(t, this.data.issx, a, e, s);
    },
    clearSelectedNumb: function() {
        var t = this.data.params;
        t.nopsf = 2, t.nostart = 2, t.yhhd = "", this.setData({
            characteristicSelected: [ !1 ],
            discountSelected: null,
            selectedNumb: 0,
            issx: !1,
            params: t,
            pagenum: 1,
            storelist: [],
            bfstorelist: [],
            mygd: !1,
            jzgd: !0
        }), this.getstorelist();
    },
    characteristicSelected: function(t) {
        var a = this.data.characteristicSelected;
        a[t.currentTarget.dataset.index] = !a[t.currentTarget.dataset.index], this.setData({
            characteristicSelected: a,
            selectedNumb: this.data.selectedNumb + (a[t.currentTarget.dataset.index] ? 1 : -1)
        }), console.log(a, t.currentTarget.dataset.index, t.currentTarget.dataset.name);
    },
    discountSelected: function(t) {
        this.data.discountSelected != t.currentTarget.dataset.index ? this.setData({
            discountSelected: t.currentTarget.dataset.index,
            selectedNumb: this.data.selectedNumb + (null == this.data.discountSelected ? 1 : 0)
        }) : this.setData({
            discountSelected: null,
            selectedNumb: this.data.selectedNumb - 1
        });
    },
    mask1Cancel: function() {
        this.setData({
            mask1Hidden: !0
        });
    },
    mask2Cancel: function() {
        this.setData({
            mask2Hidden: !0
        });
    },
    onOverallTag: function(t) {
        console.log(t), this.setData({
            mask1Hidden: !1
        });
    },
    onFilter: function() {
        this.setData({
            mask2Hidden: !1
        });
    },
    hddb: function() {
        wx.pageScrollTo({
            scrollTop: 0,
            duration: 1e3
        });
    },
    dwreLoad: function() {
        var i = this, o = this.data.params;
        wx.getLocation({
            type: "wgs84",
            success: function(t) {
                var e = t.latitude, s = t.longitude, n = e + "," + s;
                console.log(n), qqmapsdk.reverseGeocoder({
                    location: {
                        latitude: e,
                        longitude: s
                    },
                    coord_type: 1,
                    success: function(t) {
                        var e = t.result.ad_info.location;
                        o.lat = e.lat, o.lng = e.lng, console.log(t), console.log(t.result.formatted_addresses.recommend), 
                        console.log("坐标转地址后的经纬度：", t.result.ad_info.location), i.setData({
                            weizhi: t.result.formatted_addresses.recommend,
                            startjwd: e,
                            params: o
                        }), i.getstorelist(), a.util.request({
                            url: "entry/wxapp/Brand",
                            cachetime: "0",
                            data: {
                                lat: e.lat,
                                lng: e.lng
                            },
                            success: function(t) {
                                console.log(t.data), i.setData({
                                    Brand: t.data
                                });
                            }
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
                                        t.authSetting["scope.userLocation"], i.dwreLoad();
                                    }
                                }));
                            }
                        });
                    }
                });
            },
            complete: function(t) {}
        });
    },
    getstorelist: function() {
        var n = this, i = n.data.pagenum;
        n.data.params.page = i, n.data.params.pagesize = 10, console.log(i, n.data.params), 
        n.setData({
            isjzz: !0
        }), a.util.request({
            url: "entry/wxapp/StoreList",
            cachetime: "0",
            data: n.data.params,
            success: function(t) {
                console.log("分页返回的商家列表数据", t.data), t.data.length < 10 ? n.setData({
                    mygd: !0,
                    jzgd: !0,
                    isjzz: !1
                }) : n.setData({
                    jzgd: !0,
                    pagenum: i + 1,
                    isjzz: !1
                });
                for (var a = 0; a < t.data.length; a++) {
                    "0.0" == t.data[a].sales && (t.data[a].sales = "5.0");
                    var e = parseFloat(t.data[a].juli);
                    console.log(e), t.data[a].aa = e < 1e3 ? e + "m" : (e / 1e3).toFixed(2) + "km", 
                    t.data[a].aa1 = e;
                }
                var s = n.data.bfstorelist;
                s = function(t) {
                    for (var a = [], e = 0; e < t.length; e++) -1 == a.indexOf(t[e]) && a.push(t[e]);
                    return a;
                }(s = s.concat(t.data)), n.setData({
                    isxlsxz: !1,
                    storelist: s,
                    bfstorelist: s
                }), console.log(s);
            }
        });
    },
    jumps: function(t) {
        var a = t.currentTarget.dataset.id, e = t.currentTarget.dataset.name, s = t.currentTarget.dataset.appid, n = t.currentTarget.dataset.src, i = t.currentTarget.dataset.wb_src, o = t.currentTarget.dataset.type;
        console.log(a, e, s, n, i, o), 1 == o ? (console.log(n), wx.navigateTo({
            url: n
        })) : 2 == o ? (wx.setStorageSync("vr", i), wx.navigateTo({
            url: "../car/car"
        })) : 3 == o && wx.navigateToMiniProgram({
            appId: s
        });
    },
    tzsjxq: function(t) {
        console.log(t.currentTarget.dataset, this.data.mdxx), 1 == t.currentTarget.dataset.type ? (getApp().sjid = t.currentTarget.dataset.sjid, 
        wx.navigateTo({
            url: "/zh_cjdianc/pages/seller/index"
        })) : "1" == this.data.mdxx.is_tzms ? (getApp().sjid = t.currentTarget.dataset.sjid, 
        wx.navigateTo({
            url: "/zh_cjdianc/pages/seller/index"
        })) : wx.navigateTo({
            url: "/zh_cjdianc/pages/takeout/takeoutindex?storeid=" + t.currentTarget.dataset.sjid
        });
    },
    qxd: function() {
        this.setData({
            istjhb: !1
        });
    },
    sssj: function() {
        wx.navigateTo({
            url: "sssj"
        });
    },
    onLoad: function(t) {
        var i = this;
        i.setData({
            isxlsxz: !0
        }), a.setNavigationBarColor(this), a.pageOnLoad(this), a.getUserInfo(function(s) {
            console.log(s), a.util.request({
                url: "entry/wxapp/CouponSet",
                cachetime: "0",
                success: function(t) {
                    i.setData({
                        CouponSet: t.data
                    });
                    var e = util.formatTime(new Date()).slice(11, 16);
                    console.log(t.data, e), e >= t.data.time && e < t.data.time2 ? (console.log("hbtime"), 
                    "1" == t.data.is_tjhb && a.util.request({
                        url: "entry/wxapp/TjCoupons",
                        cachetime: "0",
                        data: {
                            user_id: s.id
                        },
                        success: function(t) {
                            console.log(t.data, _typeof(t.data)), t.data, t.data, "object" == _typeof(t.data) && (console.log(_typeof(t.data)), 
                            i.setData({
                                istjhb: !0,
                                tjhbarr: t.data
                            }), i.refresh());
                        }
                    })) : console.log("nothbtime");
                }
            });
        }), a.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(t) {
                console.log(t);
                var n = t.data;
                qqmapsdk = new QQMapWX({
                    key: t.data.map_key
                }), wx.setNavigationBarTitle({
                    title: t.data.url_name
                }), i.setData({
                    mdxx: t.data
                }), a.util.request({
                    url: "entry/wxapp/TypeAd",
                    cachetime: "0",
                    success: function(t) {
                        console.log(t.data);
                        var a = [];
                        if ("1" == n.fl_more) for (var e = 0, s = t.data.length; e < s; e += 8) a.push(t.data.slice(e, e + 8));
                        if ("2" == n.fl_more) for (e = 0, s = t.data.length; e < s; e += 10) a.push(t.data.slice(e, e + 10));
                        console.log(a), i.setData({
                            navs: a
                        });
                    }
                }), i.dwreLoad(), wx.setStorageSync("bqxx", t.data);
            }
        }), a.util.request({
            url: "entry/wxapp/Llz",
            cachetime: "0",
            data: {
                type: "1,2"
            },
            success: function(t) {
                console.log(t);
                for (var a = [], e = [], s = 0; s < t.data.length; s++) 1 == t.data[s].type && a.push(t.data[s]), 
                2 == t.data[s].type && e.push(t.data[s]);
                i.setData({
                    dbllz: a,
                    zbllz: e
                });
            }
        }), a.util.request({
            url: "entry/wxapp/ZbOrder",
            cachetime: "0",
            success: function(t) {
                console.log(t.data), i.setData({
                    ZbOrder: t.data
                });
            }
        }), a.util.request({
            url: "entry/wxapp/QgGoods",
            cachetime: "0",
            data: {
                type_id: "",
                store_id: "",
                page: 1,
                pagesize: 10,
                type: 1
            },
            success: function(t) {
                console.log("分页返回的列表数据", t.data);
                for (var a = 0; a < t.data.length; a++) t.data[a].discount = (Number(t.data[a].money) / Number(t.data[a].price) * 10).toFixed(1), 
                t.data[a].yqnum = ((Number(t.data[a].number) - Number(t.data[a].surplus)) / Number(t.data[a].number) * 100).toFixed(1);
                i.setData({
                    qglist: t.data
                });
            }
        }), a.util.request({
            url: "entry/wxapp/ad",
            cachetime: "0",
            success: function(t) {
                console.log(t);
                for (var a = [], e = [], s = [], n = 0; n < t.data.length; n++) "1" == t.data[n].type && a.push(t.data[n]), 
                "3" == t.data[n].type && e.push(t.data[n]), "4" == t.data[n].type && s.push(t.data[n]);
                console.log(a, e, s), i.setData({
                    toplb: a,
                    zblb: e,
                    dblb: s
                });
            }
        });
    },
    onPageScroll: function(t) {
        0 < t.scrollTop ? this.setData({
            topmove: !0
        }) : this.setData({
            topmove: !1
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        this.setData({
            issx: !1,
            selected: 0,
            mask1Hidden: !0,
            mask2Hidden: !0,
            animationData: "",
            location: "",
            characteristicSelected: [ !1, !1 ],
            discountSelected: null,
            selectedNumb: 0,
            sortSelected: "综合排序",
            params: {
                nopsf: 2,
                nostart: 2,
                yhhd: ""
            },
            pagenum: 1,
            storelist: [],
            bfstorelist: [],
            mygd: !1,
            jzgd: !0
        }), console.log("下拉刷新", this.data.pagenum, this.data.isxlsxz), this.data.isxlsxz || (this.setData({
            jzgd: !1
        }), this.onLoad()), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        console.log("上拉加载", this.data.pagenum);
        this.data.mygd || !this.data.jzgd || this.data.isjzz || (this.setData({
            jzgd: !1
        }), this.getstorelist());
    },
    onShareAppMessage: function() {
        return {
            title: "" == this.data.mdxx.fx_title ? this.data.mdxx.url_name : this.data.mdxx.fx_title,
            path: "/zh_cjdianc/pages/Liar/loginindex",
            success: function(t) {},
            fail: function(t) {}
        };
    }
});