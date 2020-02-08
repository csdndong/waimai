var o = function() {};

module.exports = {
    showZanDialog: function() {
        var o = this, t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {}, e = t.buttons, n = void 0 === e ? [] : e, i = t.title, c = void 0 === i ? "" : i, a = t.content, r = void 0 === a ? " " : a, s = t.buttonsShowVertical, l = void 0 !== s && s, v = t.showConfirm, h = void 0 === v || v, d = t.confirmText, u = void 0 === d ? "确定" : d, f = t.confirmColor, m = void 0 === f ? "#3CC51F" : f, C = t.showCancel, w = void 0 !== C && C, p = t.cancelText, g = void 0 === p ? "取消" : p, x = t.cancelColor, y = void 0 === x ? "#333" : x, D = !1;
        if (0 === n.length) {
            if (h && n.push({
                type: "confirm",
                text: u,
                color: m
            }), w) {
                var T = {
                    type: "cancel",
                    text: g,
                    color: y
                };
                l ? n.push(T) : n.unshift(T);
            }
        } else D = !0;
        return new Promise(function(t, e) {
            o.setData({
                zanDialog: {
                    show: !0,
                    showCustomBtns: D,
                    buttons: n,
                    title: c,
                    content: r,
                    buttonsShowVertical: l,
                    showConfirm: h,
                    confirmText: u,
                    confirmColor: m,
                    showCancel: w,
                    cancelText: g,
                    cancelColor: y,
                    resolve: t,
                    reject: e
                }
            });
        });
    },
    _handleZanDialogButtonClick: function(t) {
        var e = t.currentTarget, n = (void 0 === e ? {} : e).dataset, i = void 0 === n ? {} : n, c = this.data.zanDialog || {}, a = c.resolve, r = void 0 === a ? o : a, s = c.reject, l = void 0 === s ? o : s;
        this.setData({
            zanDialog: {
                show: !1
            }
        }), c.showCustomBtns ? r({
            type: i.type
        }) : "confirm" === i.type ? r({
            type: "confirm"
        }) : l({
            type: "cancel"
        });
    }
};