function r(r) {
    if (Array.isArray(r)) {
        for (var n = 0, e = Array(r.length); n < r.length; n++) e[n] = r[n];
        return e;
    }
    return Array.from(r);
}

var n = [ "onLoad", "onReady", "onShow", "onHide", "onUnload", "onPullDownRefresh", "onReachBottom", "onShareAppMessage", "onPageScroll" ], e = function(r) {
    return "__$" + r;
};

module.exports = {
    extractComponentId: function() {
        return ((arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {}).currentTarget || {}).dataset.componentId;
    },
    extend: Object.assign,
    extendCreator: function() {
        var o = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {}, t = o.life, a = void 0 === t ? n : t, i = o.exclude, f = void 0 === i ? [] : i, u = f.concat(n.map(e));
        if (!Array.isArray(a) || !Array.isArray(f)) throw new Error("Invalid Extend Config");
        var c = a.filter(function(r) {
            return n.indexOf(r) >= 0;
        });
        return function(n) {
            for (var o = arguments.length, t = Array(o > 1 ? o - 1 : 0), a = 1; a < o; a++) t[a - 1] = arguments[a];
            return t.forEach(function(o) {
                o && Object.keys(o).forEach(function(t) {
                    var a = o[t];
                    if (!(u.indexOf(t) >= 0)) if (c.indexOf(t) >= 0 && "function" == typeof a) {
                        var i = e(t);
                        if (n[i] || (n[i] = [], n[t] && n[i].push(n[t]), n[t] = function() {
                            for (var r = this, e = arguments.length, o = Array(e), t = 0; t < e; t++) o[t] = arguments[t];
                            n[i].forEach(function(n) {
                                return n.apply(r, o);
                            });
                        }), o[i]) {
                            var f;
                            (f = n[i]).push.apply(f, r(o[i]));
                        } else n[i].push(a);
                    } else n[t] = a;
                });
            }), n;
        };
    }
};