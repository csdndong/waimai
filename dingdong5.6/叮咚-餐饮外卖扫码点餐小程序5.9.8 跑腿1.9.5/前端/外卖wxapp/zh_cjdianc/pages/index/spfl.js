var qqmapsdk, a = getApp(), QQMapWX = require("../../utils/qqmap-wx-jssdk.js");

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
    onTapTag: function(t) {
        var e = this.data.params;
        "1" == t.currentTarget.dataset.index && (e.by = "juli asc"), "2" == t.currentTarget.dataset.index && (e.by = "sales desc"), 
        console.log(e, t.currentTarget.dataset.index), this.setData({
            sortSelected: this.data.sortList[0].sort,
            selected: t.currentTarget.dataset.index,
            params: e,
            pagenum: 1,
            storelist: [],
            bfstorelist: [],
            mygd: !1,
            jzgd: !0
        }), this.getstorelist();
    },
    sortSelected: function(t) {
        var e = this.data.params;
        "0" == t.currentTarget.dataset.sortindex && (e.by = "number asc"), "1" == t.currentTarget.dataset.sortindex && (e.by = "score desc"), 
        "2" == t.currentTarget.dataset.sortindex && (e.by = "start_at asc"), "3" == t.currentTarget.dataset.sortindex && (e.by = "ps_money asc"), 
        console.log(e, t.currentTarget.dataset.index, t.currentTarget.dataset.sortindex + 1), 
        this.setData({
            selected: t.currentTarget.dataset.index,
            sortSelected: this.data.sortList[t.currentTarget.dataset.sortindex].sort,
            params: e,
            pagenum: 1,
            storelist: [],
            bfstorelist: [],
            mygd: !1,
            jzgd: !0
        }), this.getstorelist();
    },
    finish: function() {
        var t = this.data.params, e = this.data.characteristicSelected, a = this.data.characteristicList, s = this.data.discountSelected;
        this.setData({
            issx: !0
        });
        for (var n = 0; n < e.length; n++) e[n] && ("0配送费" == a[n].text && (t.nopsf = 1), 
        "0元起送" == a[n].text && (t.nostart = 1));
        t.yhhd = null != s ? this.data.discountList[s].zdname : "", this.setData({
            params: t,
            pagenum: 1,
            storelist: [],
            bfstorelist: [],
            mygd: !1,
            jzgd: !0
        }), this.getstorelist(), console.log(t, this.data.issx, e, a, s);
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
        var e = this.data.characteristicSelected;
        e[t.currentTarget.dataset.index] = !e[t.currentTarget.dataset.index], this.setData({
            characteristicSelected: e,
            selectedNumb: this.data.selectedNumb + (e[t.currentTarget.dataset.index] ? 1 : -1)
        }), console.log(e, t.currentTarget.dataset.index, t.currentTarget.dataset.name);
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
        var n = this, i = this.data.params;
        wx.getLocation({
            type: "wgs84",
            success: function(t) {
                var e = t.latitude, a = t.longitude, s = e + "," + a;
                console.log(s), qqmapsdk.reverseGeocoder({
                    location: {
                        latitude: e,
                        longitude: a
                    },
                    coord_type: 1,
                    success: function(t) {
                        var e = t.result.ad_info.location;
                        i.lat = e.lat, i.lng = e.lng, console.log(t), console.log(t.result.formatted_addresses.recommend), 
                        console.log("坐标转地址后的经纬度：", t.result.ad_info.location), n.setData({
                            weizhi: t.result.formatted_addresses.recommend,
                            startjwd: e,
                            params: i
                        }), n.getstorelist();
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
                                        t.authSetting["scope.userLocation"], n.dwreLoad();
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
                var e = n.data.bfstorelist;
                e = function(t) {
                    for (var e = [], a = 0; a < t.length; a++) -1 == e.indexOf(t[a]) && e.push(t[a]);
                    return e;
                }(e = e.concat(t.data));
                for (var a = 0; a < t.data.length; a++) {
                    "0.0" == t.data[a].sales && (t.data[a].sales = "5.0");
                    var s = parseFloat(t.data[a].juli);
                    console.log(s), console.log(), t.data[a].aa = s < 1e3 ? s + "m" : (s / 1e3).toFixed(2) + "km", 
                    t.data[a].aa1 = s, n.setData({
                        storelist: e,
                        bfstorelist: e
                    });
                }
                console.log(e);
            }
        });
    },
    onLoad: function(t) {
        a.setNavigationBarColor(this), console.log(t), wx.setNavigationBarTitle({
            title: t.typename
        });
        var e = this, s = this.data.params;
        s.type_id = t.type_id, e.setData({
            params: s
        }), a.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(t) {
                console.log(t), qqmapsdk = new QQMapWX({
                    key: t.data.map_key
                }), e.setData({
                    mdxx: t.data
                }), e.dwreLoad(), wx.setStorageSync("bqxx", t.data);
            }
        });
    },
    tzsjxq: function(t) {
        console.log(t.currentTarget.dataset.sjid, this.data.mdxx), "1" == this.data.mdxx.is_tzms ? (getApp().sjid = t.currentTarget.dataset.sjid, 
        wx.navigateTo({
            url: "/zh_cjdianc/pages/seller/index"
        })) : wx.navigateTo({
            url: "/zh_cjdianc/pages/takeout/takeoutindex?storeid=" + t.currentTarget.dataset.sjid
        });
    },
    onPageScroll: function(t) {},
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
        }), console.log("下拉刷新", this.data.pagenum), this.data.jzgd && (this.setData({
            jzgd: !1
        }), this.dwreLoad()), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        console.log("上拉加载", this.data.pagenum);
        this.data.mygd || !this.data.jzgd || this.data.isjzz || (this.setData({
            jzgd: !1
        }), this.getstorelist());
    }
});