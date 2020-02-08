var qqmapsdk, app = getApp(), QQMapWX = require("../../utils/qqmap-wx-jssdk.js"), util = require("../../utils/util.js"), pageNum = 1, searchTitle = "", msgListKey = "";

Page({
    data: {
        qqsj: !0,
        msgList: [],
        searchLogList: [],
        hidden: !0,
        scrollTop: 0,
        inputShowed: !1,
        inputVal: "",
        searchLogShowed: !0,
        scrollHeight: 0,
        pagenum: 1,
        storelist: [],
        bfstorelist: [],
        mygd: !1,
        jzgd: !0,
        isjzz: !0,
        params: {
            nopsf: 2,
            nostart: 2,
            yhhd: ""
        }
    },
    dwreLoad: function() {
        var o = this, i = this.data.params;
        wx.getLocation({
            type: "wgs84",
            success: function(t) {
                var a = t.latitude, e = t.longitude, s = a + "," + e;
                console.log(s), qqmapsdk.reverseGeocoder({
                    location: {
                        latitude: a,
                        longitude: e
                    },
                    coord_type: 1,
                    success: function(t) {
                        var a = t.result.ad_info.location;
                        i.lat = a.lat, i.lng = a.lng, console.log(t), console.log(t.result.formatted_addresses.recommend), 
                        console.log("坐标转地址后的经纬度：", t.result.ad_info.location), o.setData({
                            weizhi: t.result.formatted_addresses.recommend,
                            startjwd: a,
                            params: i
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
                                        t.authSetting["scope.userLocation"], o.dwreLoad();
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
    onLoad: function(t) {
        app.setNavigationBarColor(this);
        var a = this;
        a.setData({
            mdxx: wx.getStorageSync("bqxx")
        });
        var e = getApp().imgurl;
        wx.getSystemInfo({
            success: function(t) {
                a.setData({
                    windowHeight: t.windowHeight,
                    windowWidth: t.windowWidth,
                    searchLogList: wx.getStorageSync("searchLog") || [],
                    url: e
                });
            }
        }), qqmapsdk = new QQMapWX({
            key: wx.getStorageSync("bqxx").map_key
        }), console.log(wx.getStorageSync("bqxx")), a.dwreLoad(), wx.getSystemInfo({
            success: function(t) {
                a.setData({
                    scrollHeight: t.windowHeight
                });
            }
        });
    },
    sljz: function() {
        console.log("上拉加载", this.data.pagenum);
        this.data.mygd || !this.data.jzgd || this.data.isjzz ? wx.showToast({
            title: "没有更多了",
            icon: "loading",
            duration: 1e3
        }) : (this.setData({
            jzgd: !1
        }), this.getstorelist());
    },
    getstorelist: function() {
        var o = this, i = o.data.pagenum;
        o.data.params.page = i, o.data.params.pagesize = 20, o.data.params.keywords = searchTitle, 
        console.log(i, o.data.params), o.setData({
            isjzz: !0
        }), app.util.request({
            url: "entry/wxapp/StoreList",
            cachetime: "0",
            data: o.data.params,
            success: function(t) {
                console.log("分页返回的商家列表数据", t.data), t.data.length < 10 ? o.setData({
                    mygd: !0,
                    jzgd: !0,
                    isjzz: !1
                }) : o.setData({
                    jzgd: !0,
                    pagenum: i + 1,
                    isjzz: !1
                });
                var a = o.data.bfstorelist;
                a = function(t) {
                    for (var a = [], e = 0; e < t.length; e++) -1 == a.indexOf(t[e]) && a.push(t[e]);
                    return a;
                }(a = a.concat(t.data));
                for (var e = 0; e < t.data.length; e++) {
                    "0.0" == t.data[e].sales && (t.data[e].sales = "5.0");
                    var s = parseFloat(t.data[e].juli);
                    console.log(s), console.log(), t.data[e].aa = s < 1e3 ? s + "m" : (s / 1e3).toFixed(2) + "km", 
                    t.data[e].aa1 = s, o.setData({
                        storelist: a,
                        bfstorelist: a,
                        isxlsxz: !1
                    });
                }
                console.log(a), o.setData({
                    qqsj: !0
                });
            }
        });
    },
    tzsj: function(t) {
        console.log(t.currentTarget.dataset.src), wx.navigateTo({
            url: t.currentTarget.dataset.src
        });
    },
    onReady: function() {},
    onShow: function() {},
    scroll: function(t) {
        this.setData({
            scrollTop: t.detail.scrollTop
        });
    },
    showInput: function() {
        "" != wx.getStorageSync("searchLog") ? this.setData({
            inputShowed: !0,
            searchLogShowed: !0,
            searchLogList: wx.getStorageSync("searchLog")
        }) : this.setData({
            inputShowed: !0,
            searchLogShowed: !0
        });
    },
    searchData: function() {
        console.log(searchTitle);
        if ("" != searchTitle) {
            var t = this.data.searchLogList;
            -1 === t.indexOf(searchTitle) && (t.unshift(searchTitle), wx.setStorageSync("searchLog", t), 
            this.setData({
                searchLogList: wx.getStorageSync("searchLog")
            })), this.setData({
                qqsj: !1,
                msgList: [],
                scrollTop: 0,
                pagenum: 1,
                storelist: [],
                bfstorelist: [],
                mygd: !1,
                jzgd: !0,
                isjzz: !0
            }), this.getstorelist();
        } else wx.showToast({
            title: "搜索内容为空",
            icon: "loading",
            duration: 1e3
        });
    },
    clearInput: function() {
        this.setData({
            msgList: [],
            scrollTop: 0,
            inputVal: ""
        }), searchTitle = "";
    },
    inputTyping: function(t) {
        "" != wx.getStorageSync("searchLog") ? this.setData({
            inputVal: t.detail.value,
            searchLogList: wx.getStorageSync("searchLog")
        }) : this.setData({
            inputVal: t.detail.value,
            searchLogShowed: !0
        }), searchTitle = t.detail.value;
    },
    searchDataByLog: function(t) {
        searchTitle = t.target.dataset.log, console.log(t.target.dataset.log);
        this.setData({
            msgList: [],
            scrollTop: 0,
            inputShowed: !0,
            inputVal: searchTitle
        }), this.searchData();
    },
    clearSearchLog: function() {
        this.setData({
            hidden: !1
        }), wx.removeStorageSync("searchLog"), this.setData({
            scrollTop: 0,
            hidden: !0,
            searchLogList: []
        });
    },
    onHide: function() {},
    onUnload: function() {}
});