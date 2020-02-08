var app = getApp(), formatTime = function(t) {
    var n = t.getFullYear(), e = t.getMonth() + 1, a = t.getDate(), i = t.getHours(), o = t.getMinutes(), r = t.getSeconds();
    return [ n, e, a ].map(formatNumber).join("/") + " " + [ i, o, r ].map(formatNumber).join(":");
}, formatNumber = function(t) {
    return (t = t.toString())[1] ? t : "0" + t;
}, cartAnimation = function(t, n, e) {
    var a = app.globalData.windowHeight - 50, i = flyX(35, t), o = flyY(a, n), r = scaleS(1.2, 1.2);
    e.setData({
        leftNum: t - 5,
        topNum: n - 5,
        showBall: !0
    }), setTimeoutES6(100).then(function() {
        return e.setData({
            animationX: i.export(),
            animationY: o.export(),
            scales: scaleS(1, 1).export()
        }), setTimeoutES6(400);
    }).then(function() {
        return e.setData({
            showBall: !1,
            animationX: flyX(0, 0, 0).export(),
            animationY: flyY(0, 0, 0).export(),
            scales: r.export()
        }), setTimeoutES6(200);
    }).then(function() {
        e.setData({
            scales: scaleS(1, 1).export()
        });
    });
}, flyY = function(t, n, e) {
    var a = wx.createAnimation({
        duration: e || 400,
        timingFunction: "ease-in"
    });
    return a.translateY(t - n).step(), a;
}, flyX = function(t, n, e) {
    var a = wx.createAnimation({
        duration: e || 400,
        timingFunction: "linear"
    });
    return a.translateX(t - n).step(), a;
}, scaleS = function(t) {
    var n = wx.createAnimation({
        duration: 200,
        timingFunction: "linear"
    });
    return n.scale(t, t).step(), n;
}, setTimeoutES6 = function(e) {
    return new Promise(function(t, n) {
        setTimeout(function() {
            t();
        }, e);
    });
}, throttle = function(n, e) {
    null != e && null != e || (e = 1500);
    var a = null;
    return function() {
        var t = +new Date();
        (e < t - a || !a) && (n.apply(this, arguments), a = t);
    };
};

module.exports = {
    formatTime: formatTime,
    cartAnimation: cartAnimation,
    throttle: throttle
};