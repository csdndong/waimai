var formatTime = function(t) {
    var e = t.getFullYear(), a = t.getMonth() + 1, r = t.getDate(), n = t.getHours(), o = t.getMinutes(), i = t.getSeconds();
    return [ e, a, r ].map(formatNumber).join("/") + " " + [ n, o, i ].map(formatNumber).join(":");
}, formatNumber = function(t) {
    return (t = t.toString())[1] ? t : "0" + t;
};

function validTime(t, e) {
    var a = t.split("-"), r = e.split("-"), n = new Date(parseInt(a[0]), parseInt(a[1]) - 1, parseInt(a[2]), 0, 0, 0), o = new Date(parseInt(r[0]), parseInt(r[1]) - 1, parseInt(r[2]), 0, 0, 0);
    return !(n.getTime() >= o.getTime()) || (console.log("结束日期不能小于开始日期", this), !1);
}

function validTime1(t, e) {
    var a = t.split("-"), r = e.split("-"), n = new Date(parseInt(a[0]), parseInt(a[1]) - 1, parseInt(a[2]), 0, 0, 0), o = new Date(parseInt(r[0]), parseInt(r[1]) - 1, parseInt(r[2]), 0, 0, 0);
    return !(n.getTime() > o.getTime()) || (console.log("结束日期不能小于开始日期", this), !1);
}

function getRandomNum() {
    for (var t = "" + Math.round(1e6 * Math.random()); t.length < 6; ) t = "0" + t;
    return console.info("randomNum is ========", t), t;
}

function in_array(t, e) {
    for (var a = 0; a < e.length; a++) {
        if (e[a] == t) return 1;
    }
    return 2;
}

function getDistance(t, e, a, r) {
    e = e || 0, a = a || 0, r = r || 0;
    var n = (t = t || 0) * Math.PI / 180, o = a * Math.PI / 180, i = n - o, s = e * Math.PI / 180 - r * Math.PI / 180;
    return 12756274 * Math.asin(Math.sqrt(Math.pow(Math.sin(i / 2), 2) + Math.cos(n) * Math.cos(o) * Math.pow(Math.sin(s / 2), 2)));
}

function getNowFormatDate() {
    var t = new Date(), e = t.getMonth() + 1, a = t.getDate();
    return 1 <= e && e <= 9 && (e = "0" + e), 0 <= a && a <= 9 && (a = "0" + a), t.getFullYear() + "/" + e + "/" + a + " " + t.getHours() + ":" + t.getMinutes() + ":" + t.getSeconds();
}

function xctsfm(t, e) {
    var a = new Date(t.replace(/-/g, "/")), r = new Date(e), n = parseInt((r.getTime() - a.getTime()) / 1e3), o = Math.floor(n / 86400 / 365);
    n %= 31536e3;
    var i = Math.floor(n / 86400 / 30);
    n %= 2592e3;
    var s = Math.floor(n / 86400);
    n %= 86400;
    var m = Math.floor(n / 3600);
    n %= 3600;
    var u = Math.floor(n / 60), g = n %= 60;
    console.log(o, i, s, m, u, g);
    var l = {};
    return l.day = s, l.hour = m, l.minute = u, l;
}

function ormatDate(t) {
    var e = new Date(1e3 * t);
    return e.getFullYear() + "-" + a(e.getMonth() + 1, 2) + "-" + a(e.getDate(), 2) + " " + a(e.getHours(), 2) + ":" + a(e.getMinutes(), 2) + ":" + a(e.getSeconds(), 2);
    function a(t, e) {
        for (var a = "" + t, r = a.length, n = "", o = e; o-- > r; ) n += "0";
        return n + a;
    }
}

module.exports = {
    formatTime: formatTime,
    getRandomNum: getRandomNum,
    in_array: in_array,
    getDistance: getDistance,
    validTime: validTime,
    validTime1: validTime1,
    getNowFormatDate: getNowFormatDate,
    xctsfm: xctsfm,
    ormatDate: ormatDate
};