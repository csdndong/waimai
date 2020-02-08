var app = getApp(), util = require("../../utils/util.js");

Page({
    data: {
        navbar: [],
        selectedindex: 0
    },
    selectednavbar: function(e) {
        console.log(e), this.setData({
            selectedindex: e.currentTarget.dataset.index,
            yxtime: this.data.navbar[e.currentTarget.dataset.index].time
        });
    },
    selectedTime: function(e) {
        console.log(e), this.setData({
            selectedtime: e.currentTarget.dataset.index
        });
    },
    bindDateChange: function(e) {
        console.log("picker发送选择改变，携带值为", e.detail.value), this.setData({
            yxtime: e.detail.value
        });
    },
    onLoad: function(e) {
        function t(e) {
            var t = new Date();
            t.setDate(t.getDate() + e);
            var a = t.getFullYear(), n = t.getMonth() + 1;
            n < 10 && (n = "0" + n);
            var i = t.getDate();
            i < 10 && (i = "0" + i);
            t.getHours(), t.getMinutes(), t.getSeconds();
            return a + "-" + n + "-" + i;
        }
        wx.setNavigationBarTitle({
            title: "选择时间"
        }), app.setNavigationBarColor(this);
        var a = this, n = util.formatTime(new Date()).substring(0, 10).replace(/\//g, "-");
        console.log(e, t(0), t(1), t(2)), app.util.request({
            url: "entry/wxapp/GetStoreTime",
            cachetime: "0",
            data: {
                store_id: e.storeid
            },
            success: function(e) {
                console.log(e), a.setData({
                    tiemarr: e.data,
                    yxtime: t(0),
                    startdate: n
                });
            }
        }), app.util.request({
            url: "entry/wxapp/GetYdSet",
            cachetime: "0",
            data: {
                store_id: e.storeid
            },
            success: function(e) {
                console.log(e), "1" == e.data.is_ydtime ? a.setData({
                    navbar: [ {
                        name: "今天",
                        time: t(0)
                    } ]
                }) : a.setData({
                    navbar: [ {
                        name: "今天",
                        time: t(0)
                    }, {
                        name: "明天",
                        time: t(1)
                    }, {
                        name: "后天",
                        time: t(2)
                    }, {
                        name: "其他时间"
                    } ]
                });
            }
        });
    },
    formSubmit: function(e) {
        var t = getCurrentPages(), a = this, n = wx.getStorageSync("users").id;
        if (app.util.request({
            url: "entry/wxapp/AddFormId",
            cachetime: "0",
            data: {
                user_id: n,
                form_id: e.detail.formId
            },
            success: function(e) {
                console.log(e.data);
            }
        }), null != this.data.selectedtime) {
            if (1 < t.length) t[t.length - 2].setData({
                date: a.data.yxtime,
                time: a.data.tiemarr[a.data.selectedtime].time
            });
            setTimeout(function() {
                wx.navigateBack({});
            }, 500);
        } else wx.showModal({
            title: "提示",
            content: "请选择时间"
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});