var _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(e) {
    return typeof e;
} : function(e) {
    return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e;
};

function postData(e) {
    var t = e.url, a = e.param, o = e.back;
    wx.showLoading({
        title: "加载中",
        mask: !0
    }), console.log(t), console.log(a), wx.request({
        url: getUrl() + t,
        data: a,
        method: "POST",
        header: {
            "content-type": "application/x-www-form-urlencoded"
        },
        complete: function(e) {
            wx.hideLoading();
        },
        fail: function(e) {
            wx.showToast({
                title: "请求错误",
                icon: "error",
                mask: !0,
                duration: 2e3
            }), o(!1);
        },
        success: function(e) {
            if (console.log(e), wx.hideLoading(), 500 == e.data.status) return wx.showToast({
                title: "请求成功",
                icon: "success",
                mask: !0,
                duration: 2e3
            }), void o(e.data.data);
            0 < e.data.length && wx.showToast({
                title: e.data.data,
                icon: "error",
                mask: !0,
                duration: 2e3
            }), o(!1);
        }
    });
}

function getData(e) {
    var t = e.url, a = e.param, o = e.back;
    console.log(t + "++++" + a), wx.request({
        url: getUrl() + t,
        data: a,
        method: "GET",
        success: function(e) {
            console.log(e), wx.hideLoading(), 500 == e.data.status ? (wx.showToast({
                title: "请求成功",
                icon: "success",
                mask: !0
            }), o(e.data.data)) : wx.showToast({
                title: e.data.data,
                icon: "error",
                mask: !0
            }), o(!1);
        },
        fail: function(e) {
            o(!1);
        },
        complete: function(e) {
            wx.hideLoading();
        }
    });
}

function getUrl() {
    return "https://xcx.uzhizhu.com";
}

function getUserId(a) {
    wx.getStorage({
        key: getUserKey(),
        fail: function(e) {
            a.back(!1);
        },
        success: function(e) {
            var t = e.data;
            console.log(t), a.back(t.usrid);
        }
    });
}

function getUser(a) {
    wx.getStorage({
        key: getUserKey(),
        fail: function(e) {
            a.back(!1);
        },
        success: function(e) {
            var t = e.data;
            console.log(t), a.back(t);
        }
    });
}

function getUserKey() {
    return "userInfo";
}

function getOpenPwKey() {
    return "openpw";
}

function stringToDate(e) {
    var t = e.split("-");
    return new Date(t[0], t[1] - 1, t[2], 0, 0, 0);
}

function formatDate(e, t) {
    if ("string" != typeof e && "object" == (void 0 === e ? "undefined" : _typeof(e))) {
        var a = e.getFullYear(), o = e.getMonth() + 1, n = e.getDate(), r = e.getHours(), s = e.getMinutes(), c = e.getSeconds(), i = e.getDay(), g = e.getMilliseconds(), l = "";
        1 == i ? l = "星期一" : 2 == i ? l = "星期二" : 3 == i ? l = "星期三" : 4 == i ? l = "星期四" : 5 == i ? l = "星期五" : 6 == i ? l = "星期六" : 7 == i && (l = "星期日");
        var u = "0" + o, d = "0" + n, f = "0" + r, p = "0" + s, y = "0" + c;
        return t.replace(/yyyy/g, a).replace(/YYYY/g, a).replace(/yy/g, (a + "").substring(2, 4)).replace(/YY/g, (a + "").substring(2, 4)).replace(/MM/g, u.substring(u.length - 2)).replace(/dd/g, d.substring(d.length - 2)).replace(/HH/g, f.substring(f.length - 2)).replace(/hh/g, f.substring(f.length - 2)).replace(/mm/g, p.substring(p.length - 2)).replace(/sss/g, g).replace(/SSS/g, g).replace(/ss/g, y.substring(y.length - 2)).replace(/SS/g, y.substring(y.length - 2)).replace(/E/g, l);
    }
}

function calculateTime(e, t) {
    return (e.getTime() - t.getTime()) / 864e5;
}

module.exports = {
    getUserKey: getUserKey,
    getOpenPwKey: getOpenPwKey,
    getUrl: getUrl,
    postData: postData,
    getData: getData,
    getUserId: getUserId,
    getUser: getUser,
    formatDate: formatDate,
    stringToDate: stringToDate,
    calculateTime: calculateTime
};