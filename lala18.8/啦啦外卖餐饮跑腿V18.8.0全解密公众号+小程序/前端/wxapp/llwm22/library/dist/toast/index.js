function t(t) {
    var a = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : 0;
    return "object" === (void 0 === t ? "undefined" : o(t)) ? t : {
        title: t,
        timeout: a
    };
}

var o = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(t) {
    return typeof t;
} : function(t) {
    return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : typeof t;
}, a = Object.assign || function(t) {
    for (var o = 1; o < arguments.length; o++) {
        var a = arguments[o];
        for (var n in a) Object.prototype.hasOwnProperty.call(a, n) && (t[n] = a[n]);
    }
    return t;
};

module.exports = {
    showZanToast: function(o, a) {
        var n = this, e = t(o, a), i = (this.data.zanToast || {}).timer, r = void 0 === i ? 0 : i;
        clearTimeout(r);
        var s = {
            show: !0,
            icon: e.icon,
            image: e.image,
            title: e.title
        };
        if (this.setData({
            zanToast: s
        }), !(a < 0)) {
            var c = setTimeout(function() {
                n.clearZanToast();
            }, a || 3e3);
            this.setData({
                "zanToast.timer": c
            });
        }
    },
    clearZanToast: function() {
        var t = (this.data.zanToast || {}).timer, o = void 0 === t ? 0 : t;
        clearTimeout(o), this.setData({
            "zanToast.show": !1
        });
    },
    showZanLoading: function(o) {
        var n = t(o);
        this.showZanToast(a({}, n, {
            icon: "loading"
        }));
    }
};