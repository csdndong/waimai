App({
    onLaunch: function(t) {
        var n = wx.getStorageSync("logs") || [];
        n.unshift(Date.now()), wx.setStorageSync("logs", n);
    },
    getUrl: function(n) {
        var t = this.globalData.url;
        n.setData({
            url: t
        });
        var e = this;
        t || e.util.request({
            url: "entry/wxapp/Attachurl",
            success: function(t) {
                e.globalData.url = t.data, e.getUrl(n);
            }
        });
    },
    ifArrVal: function(e) {
        function t(t, n) {
            return e.apply(this, arguments);
        }
        return t.toString = function() {
            return e.toString();
        }, t;
    }(function(t, n) {
        for (var e = 0; e < t.length; e++) {
            if (t[e] instanceof Array) return ifArrVal(t[e].url, n);
            if (t[e].url == n) return t[e].active = !0, t;
        }
        return !1;
    }),
    repeat: function(t) {
        var e = {};
        return t.reduce(function(t, n) {
            return !e[n.url] && (e[n.url] = t.push(n)), t;
        }, []);
    },
    bottom_menu: function(t) {
        var n = this;
        console.log(n);
        var e = [ {
            img: "../img/qiang.png",
            sele_img: "../img/z_qiang.png",
            name: "任务大厅",
            color: "#999",
            active: !1,
            url: "/zh_cjpt/pages/index/index"
        }, {
            img: "../img/index.png",
            sele_img: "../img/z_index.png",
            name: "我的",
            color: "#999",
            active: !1,
            sele_color: "#89f7fe",
            url: "/zh_cjpt/pages/logs/logs"
        } ];
        console.log(this.route);
        var o = t, r = n.ifArrVal(e, o);
        return 0 != r && n.repeat(r);
    },
    g_t: function(e) {
        wx.getLocation({
            type: "wgs84",
            success: function(t) {
                console.log(t);
                var n = t.latitude + "," + t.longitude;
                location = n, e(n), console.log(n), wx.setStorageSync("loacation", n);
            },
            fail: function(t) {
                console.log(t), wx.hideLoading(), location = 1, wx.showModal({
                    title: "授权提示",
                    content: "您取消了位置授权，小程序将无法正常使用，如需再次授权，请在我的-授权管理中进行授权，再次进入小程序即可",
                    showCancel: !0,
                    cancelText: "取消",
                    cancelColor: "#333",
                    confirmText: "确定",
                    confirmColor: "#333",
                    success: function(t) {},
                    fail: function(t) {},
                    complete: function(t) {}
                });
            }
        });
    },
    onShow: function(t) {
        console.log("这是显示");
    },
    onHide: function(t) {
        console.log("这是小程序从前台进入后台"), this.globalData.sign_out = !0;
    },
    today_time: function(t) {
        var n = new Date(), e = n.getMonth() + 1, o = n.getDate();
        return 1 <= e && e <= 9 && (e = "0" + e), 0 <= o && o <= 9 && (o = "0" + o), n.getFullYear() + "-" + e + "-" + o;
    },
    today_month: function(t) {
        var n = new Date(), e = n.getMonth() + 1, o = n.getDate();
        return 1 <= e && e <= 9 && (e = "0" + e), 0 <= o && o <= 9 && (o = "0" + o), n.getFullYear() + "-" + e;
    },
    ormatDate: function(t) {
        var n = new Date(1e3 * t);
        return n.getFullYear() + "-" + e(n.getMonth() + 1, 2) + "-" + e(n.getDate(), 2) + " " + e(n.getHours(), 2) + ":" + e(n.getMinutes(), 2) + ":" + e(n.getSeconds(), 2);
        function e(t, n) {
            for (var e = "" + t, o = e.length, r = "", a = n; a-- > o; ) r += "0";
            return r + e;
        }
    },
    location: function(t, n, e, o) {
        var r = t * Math.PI / 180, a = n * Math.PI / 180, i = r - a, c = e * Math.PI / 180 - o * Math.PI / 180, u = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(i / 2), 2) + Math.cos(r) * Math.cos(a) * Math.pow(Math.sin(c / 2), 2)));
        return u *= 6378.137, u = (u = Math.round(u)).toFixed(2);
    },
    util: require("we7/resource/js/util.js"),
    siteInfo: require("siteinfo.js"),
    getUserInfo: function(n) {
        var e = this;
        wx.login({
            success: function(t) {
                console.log(t), e.util.request({
                    url: "entry/wxapp/Openid",
                    cachetime: "0",
                    data: {
                        code: t.code
                    },
                    success: function(t) {
                        console.log(t), n(t.data);
                    }
                });
            }
        });
    },
    getSystem: function(n) {
        this.util.request({
            url: "entry/wxapp/GetSystem",
            cachetime: "0",
            success: function(t) {
                console.log(t), n(t.data);
            }
        });
    },
    succ_t: function(t, n) {
        wx.showToast({
            title: t
        }), 0 == n && setTimeout(function() {
            wx.navigateBack({
                delta: 1
            });
        }, 1500);
    },
    succ_m: function(t, n) {
        wx.showModal({
            title: "温馨提示",
            content: t,
            success: function(t) {
                return !!t.confirm;
            }
        });
    },
    isTelCode: function(t) {
        var n = /^1[3|4|5|7|8|9][0-9]\d{4,8}$/;
        return n.test(t);
    },
    list: function(t) {},
    globalData: {
        userInfo: null,
        mid: 0,
        refresh: !1
    }
});