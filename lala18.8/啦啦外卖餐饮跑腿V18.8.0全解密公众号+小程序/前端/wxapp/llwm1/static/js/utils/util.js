function getUrlParam(e, t) {
    var a = new RegExp("(^|&)" + t + "=([^&]*)(&|$)"), r = e.split("?")[1].match(a);
    return null != r ? unescape(r[2]) : null;
}

var _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(e) {
    return typeof e;
} : function(e) {
    return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e;
}, util = {};

util.imessage = function(e, t, a) {
    var r = {};
    if (t) {
        var n = t.substring(0, 9), i = "", s = "";
        "navigate:" == n ? (s = "navigate", i = t.substring(9)) : "redirect:" == n ? (s = "redirect", 
        i = t.substring(9)) : "switchTab:" == t.substring(0, 10) ? (s = "switchTab", i = t.substring(10)) : (s = "redirect", 
        i = t);
    }
    r = "object" == (void 0 === e ? "undefined" : _typeof(e)) ? {
        show: 1,
        type: a || "info",
        title: e.title,
        message: e.message,
        btn_text: e.btn_text,
        open_type: s,
        url: i
    } : {
        show: 1,
        type: a || "info",
        title: e,
        open_type: s,
        url: i
    }, util.getCurPage().setData({
        wuiMessage: r
    }), wx.setNavigationBarTitle({
        title: "系统提示"
    }), wx.setNavigationBarColor({
        frontColor: "#000000",
        backgroundColor: "#ffffff",
        animation: {
            duration: 400,
            timingFunc: "easeIn"
        }
    });
}, util.jump2url = function(e, t) {
    var t = t || "redirectTo";
    wx[t]({
        url: e,
        fail: function(t) {
            "navigateTo:fail can not navigate to a tabbar page" != t.errMsg && "navigateTo:fail:can not navigate to a tab bar page" != t.errMsg && "navigateTo:fail can not navigateTo a tabbar page" != t.errMsg && "redirectTo:fail can not redirect to a tabbar page" != t.errMsg && "redirectTo:fail:can not redirect to a tab bar page" != t.errMsg && "redirectTo:fail can not redirectTo a tabbar page" != t.errMsg || wx.switchTab({
                url: e
            });
        }
    });
}, util.toast = function(e, t, a) {
    var r = getCurrentPages(), n = r[r.length - 1], i = n.data.wuiToast || {};
    clearTimeout(i.timer), n.setData({
        wuiToast: {
            show: !0,
            title: e,
            url: t
        }
    });
    var s = setTimeout(function() {
        clearTimeout(i.timer), n.setData({
            "wuiToast.show": !1
        });
        var e = n.data.wuiToast.url;
        if (e) if ("back" == e) wx.navigateBack(); else {
            if ("refresh" != e) {
                var t = e.substring(0, 9), a = "";
                return "navigate:" == t ? (a = "navigateTo", e = e.substring(9)) : "redirect:" == t ? (a = "redirectTo", 
                e = e.substring(9)) : "switchTab:" == e.substring(0, 10) ? (a = "switchTab", e = e.substring(10)) : (e = e, 
                a = "navigateTo"), wx[a]({
                    url: e,
                    fail: function(t) {
                        "navigateTo:fail can not navigate to a tabbar page" != t.errMsg && "navigateTo:fail:can not navigate to a tab bar page" != t.errMsg && "navigateTo:fail can not navigateTo a tabbar page" != t.errMsg && "redirectTo:fail can not redirect to a tabbar page" != t.errMsg && "redirectTo:fail:can not redirect to a tab bar page" != t.errMsg && "redirectTo:fail can not redirectTo a tabbar page" != t.errMsg || wx.switchTab({
                            url: e
                        });
                    }
                }), !1;
            }
            n.onLoad();
        }
    }, a || 3e3);
    n.setData({
        "wuiToast.timer": s
    });
}, util.getCurPage = function() {
    var e = getCurrentPages();
    return e[e.length - 1];
}, util.setNavigator = function(e, t) {
    var a = util.getCurPage();
    t = Object.assign({
        bottom: "150px",
        right: "10px"
    }, t), a.setData({
        wuiNavigator: {
            show: !1,
            tabBar: e,
            position: t
        }
    });
}, util.loading = function() {
    util.getCurPage().setData({
        wuiLoading: {
            show: !0
        }
    });
}, util.loaded = function() {
    util.getCurPage().setData({
        wuiLoading: {
            show: !1
        }
    });
}, util.jsInfinite = function(e) {
    var t = util.getCurPage(), a = e.currentTarget.dataset, r = a.min, n = a.href, i = a.name;
    if (!id || !n || 1 == t.data.loading) return !1;
    t.data.loading = 1, t.setData({
        showloading: !0
    }), util.request({
        url: n,
        data: {
            min: r
        },
        success: function(e) {
            var t = e.data.message;
            {
                if (!t.errno) {
                    var a = that.data.redPackets.concat(t.message);
                    if (!a.length) return that.setData({
                        showNodata: !0,
                        showloading: !1
                    }), !1;
                    var r = {
                        min: t.min,
                        showloading: !1
                    };
                    return r[i] = a, e.data.message.min || (r.min = -1), that.setData(r), !0;
                }
                util.toast(t.message);
            }
        }
    });
}, util.gohome = function() {
    return wx.switchTab({
        url: "/pages/home/index",
        fail: function(e) {
            "switchTab:fail can not switch to no-tabBar page" != e.errMsg && "switchTab:fail:can not switch to non-TabBar page" != e.errMsg || wx.redirectTo({
                url: "/pages/diy/index"
            });
        }
    }), !0;
}, util.jsEvent = function(e) {
    var t = e.currentTarget.dataset.eventType || "jsPost";
    if ("jsPost" == t) util.jsPost(e); else if ("jsPay" == t) util.jsPay(e); else if ("jsUrl" == t) util.jsUrl(e); else if ("jsPhone" == t) util.jsPhone(e); else if ("jsToggle" == t) util.jsToggle(e); else if ("jsLocation" == t) util.jsLocation(e); else if ("jsCopy" == t) util.jsCopy(e); else if ("webview" == t) util.webview(e); else if ("jsInfinite" == t) util.jsInfinite(e); else if ("jsToggleNavigator" == t) {
        var a = util.getCurPage();
        a.setData({
            "wuiNavigator.show": !a.data.wuiNavigator.show
        });
    } else "jsSaveImg" == t ? util.jsSaveImg(e) : "jsUploadImg" == t ? util.jsUploadImg(e) : "jsDelImg" == t && util.jsDelImg(e);
}, util.jsUrl = function(e) {
    var t = e.currentTarget.dataset.url;
    return t ? 1 == (t = t.split(":")).length ? (t = "/" + t[0], void wx.navigateTo({
        url: t,
        fail: function(e) {
            console.log(e), "navigateTo:fail can not navigate to a tabbar page" != e.errMsg && "navigateTo:fail:can not navigate to a tab bar page" != e.errMsg && "navigateTo:fail can not navigateTo a tabbar page" != e.errMsg || wx.switchTab({
                url: t
            });
        }
    })) : void ("webview" == t[0] ? util.webview(e) : "tel" == t[0] ? util.jsPhone(e) : "miniProgram" == t[0] ? util.jsMiniProgram(e) : "wx" == t[0] && "scanCode" == t[1] && wx.scanCode()) : (util.toast("请先设置跳转链接"), 
    !1);
}, util.jsMiniProgram = function(e) {
    var t = e.currentTarget.dataset;
    if (t.url && -1 != t.url.indexOf(":")) for (var a = t.url.split(":")[1].split(","), r = {}, n = 0; n < a.length; n++) {
        var i = a[n].split("_");
        r[i[0]] = i[1];
    } else {
        r = {
            appId: t.appid || t.appId
        };
        t.path && (r.path = path);
    }
    wx.navigateToMiniProgram(r);
}, util.tolink = function(e) {
    if (-1 != e.indexOf("http://") || -1 != e.indexOf("https://")) return e;
    var t = util.getExtConfigSync();
    return 0 == e.indexOf("./") ? t.siteInfo.sitebase + "/" + e.replace("./", "") : "";
}, util.webview = function(e) {
    var t = e.currentTarget.dataset;
    if (!t.url) return !1;
    var a = t.url.split(":");
    "webview" == a[0] && (t.url = a[1] + ":" + a[2]);
    var r = util.tolink(t.url), n = t.src || "../public/webview";
    r = (r = (r = r.replace("?", "_a_")).replace(/=/g, "_b_")).replace(/&/g, "_c_"), 
    wx.navigateTo({
        url: n + "?url=" + r
    });
}, util.jsDelImg = function(e) {
    var t = e.currentTarget.dataset, a = t.key, r = t.index, n = util.getCurPage(), i = n.data[a];
    i.splice(r, 1);
    var s = {};
    s[a] = i, n.setData(s);
}, util.jsSaveImg = function(e) {
    var t = e.currentTarget.dataset.url;
    wx.showLoading({
        title: "正在下载中"
    }), wx.downloadFile({
        url: t,
        success: function(e) {
            200 === e.statusCode && wx.saveImageToPhotosAlbum({
                filePath: e.tempFilePath,
                success: function() {
                    wx.hideLoading(), app.util.toast("文件保存成功");
                }
            });
        }
    });
}, util.jsUploadImg = function(e) {
    var t = e.currentTarget.dataset, a = t.key, r = t.count || 9, n = util.getCurPage();
    util.image({
        count: r,
        success: function(e) {
            n.data[a].push(e);
            var t = {};
            t[a] = n.data[a], n.setData(t);
        }
    });
}, util.jsPhone = function(e) {
    var t = e.currentTarget.dataset, a = t.phonenumber || t.url, r = a.split(":");
    "tel" == r[0] && (a = r[1]), wx.makePhoneCall({
        phoneNumber: a
    });
}, util.jsLocation = function(e) {
    var t = e.currentTarget.dataset, a = parseFloat(t.lat), r = parseFloat(t.lng);
    if (!a || !r) return !1;
    var n = {
        latitude: a,
        longitude: r
    };
    t.scale && (n.scale = t.scale), t.name && (n.name = t.name), t.address && (n.address = t.address), 
    wx.openLocation(n);
}, util.jsCopy = function(e) {
    var t = e.currentTarget.dataset;
    wx.setClipboardData({
        data: t.text || "",
        success: function(e) {
            wx.getClipboardData({
                success: function(e) {
                    util.toast("复制成功");
                }
            });
        }
    });
}, util.jsToggle = function(e) {
    var t = e.currentTarget.dataset, a = util.getCurPage(), r = t.modal, n = t.modal.split("."), i = {};
    if (1 == n.length) i[r] = !a.data[r], a.setData(i); else {
        var s = !a.data[n[0]][n[1]];
        i[r] = s, a.setData(i);
    }
    return !0;
}, util.jsPay = function(e) {
    var t = e.currentTarget.dataset, a = t.successUrl, r = t.orderId, n = t.orderType;
    return wx.navigateTo({
        url: "../public/pay?order_id=" + r + "&order_type=" + n + "&success_url=" + a
    }), !0;
}, util.jsPost = function(e) {
    var t = e.currentTarget.dataset, a = t.confirm, r = t.href || t.url, n = t.successUrl, i = util.getCurPage(), s = function() {
        i.data.jspost && 1 == i.data.jspost || (i.data.jspost = 1, util.showLoading(), n || (n = "refresh"), 
        util.request({
            url: util.url(r),
            data: {},
            success: function(e) {
                i.data.jspost = 0, wx.hideLoading();
                var t = e.data.message, a = t.errno, r = t.message;
                a ? util.toast(r) : (n || (n = "refresh"), util.toast(r, n));
            }
        }));
    };
    a ? wx.showModal({
        title: "",
        content: a,
        success: function(e) {
            e.confirm ? s() : e.cancel;
        }
    }) : s();
}, util.setData = function(e, t) {
    var a = e.split("."), r = util.getCurPage();
    if (1 == a.length) r.data[e] = t; else if (2 == a.length) (n = r.data[a[0]]) || (n = {}), 
    n[a[1]] = t, r.data[a[0]] = n; else if (3 == a.length) (n = r.data[a[0]]) || (n = {}), 
    n[a[1]] || (n[a[1]] = {}), n[a[1]][a[2]] = t, r.data[a[0]] = n; else if (4 == a.length) {
        var n = r.data[a[0]];
        n || (n = {}), n[a[1]] || (n[a[1]] = {}), n[a[1]][a[2]] || (n[a[1]][a[2]] = {}), 
        n[a[1]][a[2]][a[3]] = t, r.data[a[0]] = n;
    }
    return !0;
}, util.setStorageSync = function(e, t, a) {
    var r = e.split(".");
    if (a > 0) {
        var n = new Date();
        t.expire = parseInt(n.getTime() / 1e3) + a;
    }
    if (1 == r.length) wx.setStorageSync(e, t); else if (2 == r.length) (i = wx.getStorageSync(r[0])) || (i = {}), 
    i[r[1]] = t, wx.setStorageSync(r[0], i); else if (3 == r.length) {
        var i = wx.getStorageSync(r[0]);
        i || (i = {}), i[r[1]] || (i[r[1]] = {}), i[r[1]][r[2]] = t, wx.setStorageSync(r[0], i);
    }
    return !0;
}, util.getStorageSync = function(e) {
    var t = e.split("."), a = "";
    if (1 == t.length) a = wx.getStorageSync(e); else if (2 == t.length) (r = wx.getStorageSync(t[0])) && r[t[1]] && (a = r[t[1]]); else if (3 == t.length) {
        var r = wx.getStorageSync(t[0]);
        r && r[t[1]] && (a = r[t[1]][t[2]]);
    }
    if (a && a.expire) {
        var n = new Date();
        a.expire < n.getTime() / 1e3 && (a = {});
    }
    return a;
}, util.getExtConfigSync = function(e) {
    var t = wx.getExtConfigSync();
    if (t && t.siteInfo || (t = getApp().ext), !e) return t;
    var a = e.split(".");
    return 1 == a.length ? t[a[0]] : 2 == a.length ? t[a[0]] ? t[a[0]][a[1]] : "" : 3 == a.length ? t[a[0]] && t[a[0]][a[1]] ? t[a[0]][a[1]][a[2]] : "" : data;
}, util.removeStorageSync = function(e) {
    var t = e.split(".");
    if (1 == t.length) wx.removeStorageSync(e); else if (2 == t.length) (a = wx.getStorageSync(t[0])) && (delete a[t[1]], 
    wx.setStorageSync(t[0], a)); else if (3 == t.length) {
        var a = wx.getStorageSync(t[0]);
        a && a[t[1]] && (delete a[t[1]][t[2]], wx.setStorageSync(t[0], a));
    }
    return !0;
}, util.merge = function(e, t) {
    var a = require("underscore.js");
    return e || (e = {}), t || (t = {}), a.extend(e, t);
}, util.pay = function(e) {
    if (e.pay_type || (e.pay_type = "wechat"), !e.order_type) return !1;
    if (!e.order_id) return !1;
    var t = {
        pay_type: e.pay_type,
        order_type: e.order_type,
        id: e.order_id
    }, a = util.getCurPage();
    util.request({
        url: "manage/pay/pay",
        data: t,
        success: function(t) {
            if ((t = t.data.message).errno) return -1e3 == t.errno && a.setData({
                submitDisabled: 0
            }), util.toast(t.message), !1;
            t = t.message;
            var r = {
                advertise: {
                    url_detail: "./list"
                }
            }[e.order_type];
            if ("wechat" == e.pay_type) return wx.requestPayment({
                timeStamp: t.timeStamp,
                nonceStr: t.nonceStr,
                package: t.package,
                signType: "MD5",
                paySign: t.paySign,
                success: function(t) {
                    return "function" == typeof e.success ? (e.success(t), !1) : r ? (util.toast("支付成功", r.url_detail, 3e3), 
                    !1) : void 0;
                },
                fail: function(t) {
                    if ("function" == typeof e.fail) return e.fail(t), !1;
                    a.setData({
                        submitDisabled: 0
                    });
                }
            }), !0;
            if ("credit" == e.pay_type) {
                if ("function" == typeof e.success) return e.success(res), !1;
            } else if (("delivery" == e.pay_type || "finishMeal" == e.pay_type) && "function" == typeof e.success) return e.success(res), 
            !1;
            return !!r && (util.toast("支付成功", r.url_detail, 3e3), !0);
        }
    });
}, util.url = function(e, t) {
    var a = util.getExtConfigSync(), r = a.siteInfo.siteroot + "?i=" + a.siteInfo.uniacid;
    if (-1 == e.indexOf("/")) return r + "&" + e;
    r = a.siteInfo.siteroot + "?i=" + a.siteInfo.uniacid + "&v=" + a.siteInfo.version + "&m=we7_wmall&c=entry&do=mobile&";
    var n = e.split("?");
    if ((e = n[0].split("/"))[0] && (r += "ctrl=" + e[0] + "&"), e[1] && (r += "ac=" + e[1] + "&"), 
    e[2] && (r += "op=" + e[2] + "&"), e[3] && (r += "ta=" + e[3] + "&"), n[1] && (r += n[1] + "&"), 
    (t = "object" === (void 0 === t ? "undefined" : _typeof(t)) ? t : {}) && "object" === (void 0 === t ? "undefined" : _typeof(t))) for (var i in t) i && t.hasOwnProperty(params) && t[i] && (r += i + "=" + t[i] + "&");
    return r += "&from=wxapp";
}, util.getUrlQuery = function(e) {
    var t = [];
    if (-1 != e.indexOf("?")) for (var a = e.split("?")[1].split("&"), r = 0; r < a.length; r++) a[r].split("=")[0] && unescape(a[r].split("=")[1]) && (t[r] = {
        name: a[r].split("=")[0],
        value: unescape(a[r].split("=")[1])
    });
    return t;
}, util.request = function(e) {
    (e = e || {}).showLoading = void 0 === e.showLoading || e.showLoading, e.data || (e.data = {});
    var t = e.url;
    -1 == t.indexOf("http://") && -1 == t.indexOf("https://") && (t = util.url(t));
    var a = wx.getStorageSync("clerkInfo");
    if (!getUrlParam(t, "token") && a && a.token) t = t + "&token=" + a.token; else if (1 == e.data.forceOauth) return void util.getUserInfo();
    var r = wx.getStorageSync("__sid");
    if (!r && 1 != e.data.nosid) return wx.removeStorageSync("__sid"), void wx.redirectTo({
        url: "/pages/shop/select"
    });
    t = t + "&sid=" + r, wx.showNavigationBarLoading(), e.showLoading && util.showLoading(), 
    console.log(t), wx.request({
        url: t,
        data: e.data ? e.data : {},
        method: e.method ? e.method : "GET",
        header: {
            "content-type": "application/x-www-form-urlencoded"
        },
        success: function(t) {
            if (wx.hideNavigationBarLoading(), wx.hideLoading(), t.data.message || console.log(t.data.message), 
            t.data.message.errno) {
                if ("41009" == t.data.message.errno) return wx.setStorageSync("clerkInfo", ""), 
                wx.setStorageSync("__sid", 0), void util.getUserInfo();
                if ("41002" == t.data.message.errno) return wx.removeStorageSync("__sid"), void wx.redirectTo({
                    url: "/pages/shop/select"
                });
            }
            e.success && "function" == typeof e.success && e.success(t);
        },
        fail: function(t) {
            if (t && "request:ok" != t.errMsg) return wx.hideLoading(), "request:fail url not in domain list" == t.errMsg ? void util.toast("您没有设置小程序服务器域名!注意：每次小程序发布后，都需要重新设置服务器域名，程序每个月可修改5次服务器域名。设置步骤：进入微信公众号平台-登陆小程序账号密码进入管理中心-设置-开发设置-服务器域名(服务器域名为模块授权域名)", "", 1e5) : void util.toast(t.errMsg);
            wx.hideNavigationBarLoading(), wx.hideLoading(), e.fail && "function" == typeof e.fail && e.fail(t);
        },
        complete: function(t) {
            if (console.log(t), t && "request:ok" != t.errMsg) return wx.hideLoading(), "request:fail url not in domain list" == t.errMsg ? void util.toast("您没有设置小程序服务器域名!注意：每次小程序发布后，都需要重新设置服务器域名，程序每个月可修改5次服务器域名。设置步骤：进入微信公众号平台-登陆小程序账号密码进入管理中心-设置-开发设置-服务器域名(服务器域名为模块授权域名)", "", 1e5) : void util.toast(t.errMsg);
            wx.hideNavigationBarLoading(), wx.hideLoading(), e.complete && "function" == typeof e.complete && e.complete(t);
        }
    });
}, util.orderNotice = function(e) {
    var t = wx.getStorageSync("clerkInfo"), a = wx.getStorageSync("__sid"), r = wx.getBackgroundAudioManager();
    r.title = "您有新的订单";
    t && t.token && a && setInterval(function() {
        util.request({
            url: "manage/common/orderNotice",
            success: function(e) {
                e.data.message.errno ? r.stop() : r.src = "http://ws.stream.qqmusic.qq.com/M500001VfvsJ21xFqb.mp3?guid=ffffffff82def4af4b12b3cd9337d5e7&uin=346897220&vkey=6292F51E1E384E061FF02C31F716658E5C81F5594D561F2E88B854E81CAAB7806D5E4F103E55D33C16F3FAC506D1AB172DE8600B37E43FAD&fromtag=46";
            }
        });
    }, 3e4);
}, util.image = function(e) {
    var t = e.count ? e.count : 9, a = getCurrentPages();
    a[a.length - 1];
    wx.chooseImage({
        count: t,
        sizeType: [ "original", "compressed" ],
        sourceType: [ "album", "camera" ],
        success: function(t) {
            var a = t.tempFilePaths;
            if (a.length && a.length > 0) for (var r = 0; r < a.length; r++) wx.uploadFile({
                url: util.url("c=utility&a=file&do=upload&type=image&thumb=0"),
                filePath: a[r],
                name: "file",
                success: function(t) {
                    t.data = JSON.parse(t.data), "function" == typeof e.success && e.success(t.data);
                }
            });
        }
    });
}, util.getUserInfo = function(e) {
    return wx.redirectTo({
        url: "/pages/auth/login"
    }), !0;
}, util.getLocation = function(e) {
    wx.getLocation({
        type: "gcj02",
        success: function(t) {
            util.request({
                url: "system/common/map/regeo",
                data: {
                    latitude: t.latitude,
                    longitude: t.longitude,
                    convert: 0
                },
                success: function(t) {
                    "function" == typeof e && e(t);
                },
                fail: function(e) {}
            });
        },
        fail: function(e) {
            e && "getLocation:fail auth deny" == e.errMsg && wx.showModal({
                title: "授权提示",
                content: "若需使用平台，平台需要获取您的位置信息",
                confirmText: "授权",
                showCancel: !1,
                success: function(e) {
                    e.confirm ? wx.openSetting({
                        success: function() {}
                    }) : e.cancel;
                }
            });
        }
    });
}, util.getLocationPois = function(e) {
    util.request({
        url: "system/common/map/regeo",
        data: {
            latitude: e.latitude,
            longitude: e.longitude
        },
        showLoading: !1 !== e.showLoading || e.showLoading,
        success: function(t) {
            var a = t.data.message.message;
            "function" == typeof e.success && e.success(a);
        },
        fail: function(e) {}
    });
}, util.getLocationAround = function(e) {
    util.request({
        url: "system/common/map/place_around",
        data: {
            latitude: e.latitude,
            longitude: e.longitude,
            keywords: e.keywords,
            radius: e.radius
        },
        success: function(t) {
            var a = t.data.message.message;
            "function" == typeof e.success && e.success(a);
        },
        fail: function(e) {}
    });
}, util.navigateBack = function(e) {
    var t = e.delta ? e.delta : 1;
    if (e.data) {
        var a = getCurrentPages(), r = a[a.length - (t + 1)];
        r.pageForResult ? r.pageForResult(e.data) : r.setData(e.data);
    }
    wx.navigateBack({
        delta: t,
        success: function(t) {
            "function" == typeof e.success && e.success(t);
        },
        fail: function(t) {
            "function" == typeof e.fail && e.function(t);
        },
        complete: function() {
            "function" == typeof e.complete && e.complete();
        }
    });
}, util.footer = function(e) {
    var t = getApp().tabBar;
    for (var a in t.list) t.list[a].pageUrl = t.list[a].pagePath.replace(/(\?|#)[^"]*/g, "");
    t.thisurl = e.__route__, e.setData({
        tabBar: t
    });
}, util.message = function(e, t, a) {
    if (!e) return !0;
    if ("object" == (void 0 === e ? "undefined" : _typeof(e)) && (t = e.redirect, a = e.type, 
    e = e.title), t) {
        var r = t.substring(0, 9), n = "", i = "";
        "navigate:" == r ? (i = "navigateTo", n = t.substring(9)) : "redirect:" == r ? (i = "redirectTo", 
        n = t.substring(9)) : (n = t, i = "redirectTo");
    }
    a || (a = "success"), "success" == a ? wx.showToast({
        title: e,
        icon: "success",
        duration: 2e3,
        mask: !!n,
        complete: function() {
            n && setTimeout(function() {
                wx[i]({
                    url: n
                });
            }, 1800);
        }
    }) : "error" == a && wx.showModal({
        title: "系统信息",
        content: e,
        showCancel: !1,
        complete: function() {
            n && wx[i]({
                url: n
            });
        }
    });
}, util.user = util.getUserInfo, util.showLoading = function() {
    wx.getStorageSync("isShowLoading") && (wx.hideLoading(), wx.setStorageSync("isShowLoading", !1)), 
    wx.showLoading({
        title: "加载中",
        complete: function() {
            wx.setStorageSync("isShowLoading", !0);
        },
        fail: function() {
            wx.setStorageSync("isShowLoading", !1);
        }
    });
}, util.showImage = function(e) {
    var t = e ? e.currentTarget.dataset.preview : "", a = e ? e.currentTarget.dataset.current : "";
    if (!t) return !1;
    var r = [];
    Array.isArray(t) ? r = t : r.push(t), wx.previewImage({
        urls: r,
        current: a
    });
}, util.parseContent = function(e) {
    if (!e) return e;
    var t = [ "\ud83c[\udf00-\udfff]", "\ud83d[\udc00-\ude4f]", "\ud83d[\ude80-\udeff]" ], a = e.match(new RegExp(t.join("|"), "g"));
    if (a) for (var r in a) e = e.replace(a[r], "[U+" + a[r].codePointAt(0).toString(16).toUpperCase() + "]");
    return e;
}, util.date = function() {
    this.isLeapYear = function(e) {
        return 0 == e.getYear() % 4 && (e.getYear() % 100 != 0 || e.getYear() % 400 == 0);
    }, this.dateToStr = function(e, t) {
        e = arguments[0] || "yyyy-MM-dd HH:mm:ss", t = arguments[1] || new Date();
        var a = e, r = [ "日", "一", "二", "三", "四", "五", "六" ];
        return a = a.replace(/yyyy|YYYY/, t.getFullYear()), a = a.replace(/yy|YY/, t.getYear() % 100 > 9 ? (t.getYear() % 100).toString() : "0" + t.getYear() % 100), 
        a = a.replace(/MM/, t.getMonth() > 9 ? t.getMonth() + 1 : "0" + (t.getMonth() + 1)), 
        a = a.replace(/M/g, t.getMonth()), a = a.replace(/w|W/g, r[t.getDay()]), a = a.replace(/dd|DD/, t.getDate() > 9 ? t.getDate().toString() : "0" + t.getDate()), 
        a = a.replace(/d|D/g, t.getDate()), a = a.replace(/hh|HH/, t.getHours() > 9 ? t.getHours().toString() : "0" + t.getHours()), 
        a = a.replace(/h|H/g, t.getHours()), a = a.replace(/mm/, t.getMinutes() > 9 ? t.getMinutes().toString() : "0" + t.getMinutes()), 
        a = a.replace(/m/g, t.getMinutes()), a = a.replace(/ss|SS/, t.getSeconds() > 9 ? t.getSeconds().toString() : "0" + t.getSeconds()), 
        a = a.replace(/s|S/g, t.getSeconds());
    }, this.dateAdd = function(e, t, a) {
        switch (a = arguments[2] || new Date(), e) {
          case "s":
            return new Date(a.getTime() + 1e3 * t);

          case "n":
            return new Date(a.getTime() + 6e4 * t);

          case "h":
            return new Date(a.getTime() + 36e5 * t);

          case "d":
            return new Date(a.getTime() + 864e5 * t);

          case "w":
            return new Date(a.getTime() + 6048e5 * t);

          case "m":
            return new Date(a.getFullYear(), a.getMonth() + t, a.getDate(), a.getHours(), a.getMinutes(), a.getSeconds());

          case "y":
            return new Date(a.getFullYear() + t, a.getMonth(), a.getDate(), a.getHours(), a.getMinutes(), a.getSeconds());
        }
    }, this.dateDiff = function(e, t, a) {
        switch (e) {
          case "s":
            return parseInt((a - t) / 1e3);

          case "n":
            return parseInt((a - t) / 6e4);

          case "h":
            return parseInt((a - t) / 36e5);

          case "d":
            return parseInt((a - t) / 864e5);

          case "w":
            return parseInt((a - t) / 6048e5);

          case "m":
            return a.getMonth() + 1 + 12 * (a.getFullYear() - t.getFullYear()) - (t.getMonth() + 1);

          case "y":
            return a.getFullYear() - t.getFullYear();
        }
    }, this.strToDate = function(dateStr) {
        var data = dateStr, reCat = /(\d{1,4})/gm, t = data.match(reCat);
        return t[1] = t[1] - 1, eval("var d = new Date(" + t.join(",") + ");"), d;
    }, this.strFormatToDate = function(e, t) {
        var a = 0, r = -1, n = t.length;
        (r = e.indexOf("yyyy")) > -1 && r < n && (a = t.substr(r, 4));
        var i = 0;
        (r = e.indexOf("MM")) > -1 && r < n && (i = parseInt(t.substr(r, 2)) - 1);
        var s = 0;
        (r = e.indexOf("dd")) > -1 && r < n && (s = parseInt(t.substr(r, 2)));
        var o = 0;
        ((r = e.indexOf("HH")) > -1 || (r = e.indexOf("hh")) > 1) && r < n && (o = parseInt(t.substr(r, 2)));
        var u = 0;
        (r = e.indexOf("mm")) > -1 && r < n && (u = t.substr(r, 2));
        var c = 0;
        return (r = e.indexOf("ss")) > -1 && r < n && (c = t.substr(r, 2)), new Date(a, i, s, o, u, c);
    }, this.dateToLong = function(e) {
        return e.getTime();
    }, this.longToDate = function(e) {
        return new Date(e);
    }, this.isDate = function(e, t) {
        null == t && (t = "yyyyMMdd");
        var a = t.indexOf("yyyy");
        if (-1 == a) return !1;
        var r = e.substring(a, a + 4), n = t.indexOf("MM");
        if (-1 == n) return !1;
        var i = e.substring(n, n + 2), s = t.indexOf("dd");
        if (-1 == s) return !1;
        var o = e.substring(s, s + 2);
        return !(!isNumber(r) || r > "2100" || r < "1900") && (!(!isNumber(i) || i > "12" || i < "01") && !(o > getMaxDay(r, i) || o < "01"));
    }, this.getMaxDay = function(e, t) {
        return 4 == t || 6 == t || 9 == t || 11 == t ? "30" : 2 == t ? e % 4 == 0 && e % 100 != 0 || e % 400 == 0 ? "29" : "28" : "31";
    }, this.isNumber = function(e) {
        return /^\d+$/g.test(e);
    }, this.toArray = function(e) {
        e = arguments[0] || new Date();
        var t = Array();
        return t[0] = e.getFullYear(), t[1] = e.getMonth(), t[2] = e.getDate(), t[3] = e.getHours(), 
        t[4] = e.getMinutes(), t[5] = e.getSeconds(), t;
    }, this.datePart = function(e, t) {
        t = arguments[1] || new Date();
        var a = "", r = [ "日", "一", "二", "三", "四", "五", "六" ];
        switch (e) {
          case "y":
            a = t.getFullYear();
            break;

          case "M":
            a = t.getMonth() + 1;
            break;

          case "d":
            a = t.getDate();
            break;

          case "w":
            a = r[t.getDay()];
            break;

          case "ww":
            a = t.WeekNumOfYear();
            break;

          case "h":
            a = t.getHours();
            break;

          case "m":
            a = t.getMinutes();
            break;

          case "s":
            a = t.getSeconds();
        }
        return a;
    }, this.maxDayOfDate = function(e) {
        (e = arguments[0] || new Date()).setDate(1), e.setMonth(e.getMonth() + 1);
        var t = e.getTime() - 864e5;
        return new Date(t).getDate();
    };
}, util.parseScene = function(e) {
    e = (e = decodeURIComponent(e)).split("/");
    for (var t = {}, a = 0; a < e.length; a++) {
        e[a] = e[a].split(":");
        var r = e[a][0], n = e[a][1];
        t[r] = n;
    }
    return t;
}, module.exports = util;