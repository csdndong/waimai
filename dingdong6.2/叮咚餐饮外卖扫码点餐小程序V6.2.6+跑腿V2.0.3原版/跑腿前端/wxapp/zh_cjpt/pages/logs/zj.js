for (var app = getApp(), date = new Date(), years = [], months = [], i = 1990; i <= date.getFullYear(); i++) years.push(i);

for (var _i = 1; _i <= 12; _i++) months.push(_i);

Page({
    data: {
        page: 1,
        history: [],
        years: years,
        year: date.getFullYear(),
        months: months,
        month: "01",
        start: {
            year: date.getFullYear(),
            month: "01"
        },
        start_time: app.today_month(),
        end_time: app.today_month(),
        value: [ 9999, 0, 1 ],
        color_1: "#999",
        start_month: !0,
        end_month: !1,
        type: 0,
        sele_month: !0,
        tx_statu: !0,
        list: [],
        tx_list: []
    },
    change_tx1: function(t) {
        this.setData({
            tx_statu: !0,
            list: [],
            page: 1
        }), this.tx_list();
    },
    change_tx2: function(t) {
        this.setData({
            tx_statu: !1,
            list: [],
            page: 1
        }), this.order_info();
    },
    bindChange: function(t) {
        var a = t.detail.value, e = this.data.type, n = this.data.years[a[0]], o = this.data.months[a[1]];
        console.log(o), o < 10 && (o = "0" + o), console.log(a), 0 == e ? this.setData({
            start: {
                year: n,
                month: o
            },
            start_time: n + "-" + o
        }) : this.setData({
            end: {
                year: n,
                month: o
            },
            end_time: n + "-" + o
        });
    },
    determine: function(t) {
        this.data.end_time < this.data.start_time ? app.succ_m("请选择正确的时间") : (this.setData({
            sele_month: !0,
            list: [],
            page: 1
        }), this.order_info());
    },
    month_show: function(t) {
        this.setData({
            sele_month: !1
        });
    },
    cancel: function(t) {
        this.setData({
            sele_month: !0
        });
    },
    sele_month: function(t) {
        var a = this, e = t.currentTarget.dataset.type;
        a.data.start_month, a.data.end_month;
        console.log(e), 0 == e ? a.setData({
            start_month: !0,
            end_month: !1,
            type: 0
        }) : (a.setData({
            start_month: !1,
            end_month: !0,
            type: 1
        }), null != this.data.end ? this.setData({
            end: {
                year: this.data.end.year,
                month: this.data.end.month
            }
        }) : this.setData({
            end: {
                year: date.getFullYear(),
                month: 1
            }
        }));
    },
    onLoad: function(t) {
        var a = this;
        wx.hideShareMenu(), a.order_info(), app.getSystem(function(t) {
            console.log(t), a.setData({
                getSystem: t,
                color: t.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: t.color
            });
        });
    },
    money: function(t) {
        var a = this, e = wx.getStorageSync("qs").id;
        app.util.request({
            url: "entry/wxapp/KtxMoney",
            cachetime: "0",
            data: {
                qs_id: e
            },
            success: function(t) {
                console.log("这是可提现金额"), console.log(t), a.setData({
                    price: t.data
                });
            }
        });
    },
    order_info: function(t) {
        var e = this, a = (e.data, wx.getStorageSync("qs").id), n = e.data.page, o = e.data.list;
        app.util.request({
            url: "entry/wxapp/SearchList",
            data: {
                qs_id: a,
                start_time: e.data.start_time,
                end_time: e.data.end_time,
                page: n,
                pagesize: 10
            },
            success: function(t) {
                if (console.log(t), 0 < t.data.length) {
                    for (var a in t.data) t.data[a].jd_time = app.ormatDate(t.data[a].jd_time);
                    o = o.concat(t.data), e.setData({
                        list: o,
                        page: n + 1
                    });
                }
            }
        });
    },
    tx_list: function(t) {
        var e = this, a = (e.data, wx.getStorageSync("qs").id);
        app.util.request({
            url: "entry/wxapp/txlist",
            data: {
                qs_id: a
            },
            success: function(t) {
                for (var a in console.log("提现列表", t), t.data) t.data[a].time = app.ormatDate(t.data[a].time);
                e.setData({
                    tx_list: t.data
                });
            }
        });
    },
    History_list: function(t) {
        var e = this, a = wx.getStorageSync("qs").id, n = e.data.page, o = e.data.history;
        app.util.request({
            url: "entry/wxapp/History",
            cachetime: "0",
            data: {
                qs_id: a,
                page: n
            },
            success: function(t) {
                if (console.log("这是历史账单"), console.log(t), 0 < t.data.length) {
                    for (var a in o = o.concat(t.data), t.data) t.data[a].year = t.data[a].days.substr(0, 4), 
                    t.data[a].month = t.data[a].days.substr(5, 2);
                    e.setData({
                        history: o,
                        page: n + 1
                    });
                }
            }
        });
    },
    capital: function(t) {
        wx.navigateTo({
            url: "capital"
        });
    },
    reward: function(t) {
        wx.navigateTo({
            url: "reward"
        });
    },
    bill: function(t) {
        var a = t.currentTarget.dataset;
        wx.navigateTo({
            url: "bill?days=" + a.days + "&money=" + a.money + "&count=" + a.count
        });
    },
    tixian: function(t) {
        wx.navigateTo({
            url: "tixian"
        });
    },
    tx_info: function(t) {
        wx.navigateTo({
            url: "tx_info?id=" + t.currentTarget.dataset.id
        });
    },
    zd_info: function(t) {
        wx.navigateTo({
            url: "zd_info?id=" + t.currentTarget.dataset.id
        });
    },
    onReady: function() {},
    onShow: function() {
        this.money(), this.tx_list();
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        0 == this.data.tx_statu && this.order_info();
    }
});