var app = getApp();

Page({
    data: {
        color: "#459cf9",
        page: 1,
        nav: [],
        keywords: ""
    },
    onLoad: function(a) {
        var t = this;
        wx.hideShareMenu(), t.setData({
            money: a.money,
            count: a.count,
            days: a.days,
            options: a
        }), 0 == a.type ? t.order_info() : (1 == a.type && t.setData({
            time: app.today_month()
        }), t.order_list()), app.getSystem(function(a) {
            console.log(a), t.setData({
                getSystem: a,
                color: a.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: a.color
            });
        });
    },
    input: function(a) {
        var t = a.detail.value;
        console.log(a), console.log(t), this.setData({
            keywords: t
        });
    },
    search: function(a) {
        this.setData({
            page: 1,
            nav: []
        }), this.order_list();
    },
    order_info: function(a) {
        var o = this, t = (o.data.days, wx.getStorageSync("qs").id);
        app.util.request({
            url: "entry/wxapp/TodayList",
            data: {
                qs_id: t,
                days: app.today_time()
            },
            success: function(a) {
                for (var t in console.log(a), a.data) a.data[t].ps_money = Number(a.data[t].ps_money).toFixed(1), 
                a.data[t].jd_time = app.ormatDate(a.data[t].jd_time), 5 == a.data[t].state && (a.data[t].color = "#fe5656"), 
                2 == a.data[t].state && (a.data[t].color = "#ff9a49"), 3 == a.data[t].state && (a.data[t].color = "#97d5ff"), 
                4 == a.data[t].state && (a.data[t].color = "#dddddd");
                o.setData({
                    nav: a.data
                });
            }
        });
    },
    order_list: function(a) {
        var o = this, t = (o.data, wx.getStorageSync("qs").id), e = o.data.page, d = o.data.nav, n = o.data.keywords;
        app.util.request({
            url: "entry/wxapp/SearchList",
            data: {
                qs_id: t,
                start_time: o.data.time || "",
                end_time: o.data.time || "",
                page: e,
                pagesize: 10,
                keywords: n
            },
            success: function(a) {
                if (console.log(a), 0 < a.data.length) {
                    for (var t in a.data) a.data[t].ps_money = Number(a.data[t].ps_money).toFixed(1), 
                    a.data[t].jd_time = app.ormatDate(a.data[t].jd_time), 5 == a.data[t].state && (a.data[t].color = "#fe5656"), 
                    2 == a.data[t].state && (a.data[t].color = "#ff9a49"), 3 == a.data[t].state && (a.data[t].color = "#97d5ff"), 
                    4 == a.data[t].state && (a.data[t].color = "#dddddd");
                    d = d.concat(a.data), console.log(d), o.setData({
                        nav: d.sort(function(a, t) {
                            return t.time - Number(a.time);
                        }),
                        page: e + 1
                    });
                }
            }
        });
    },
    order_infos: function(a) {
        wx.navigateTo({
            url: "../index/order_info?id=" + a.currentTarget.dataset.id
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        this.order_list();
    },
    onShareAppMessage: function() {}
});