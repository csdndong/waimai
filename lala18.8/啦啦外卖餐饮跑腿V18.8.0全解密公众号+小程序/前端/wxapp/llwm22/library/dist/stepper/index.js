function e(e, t) {
    var a = e.currentTarget.dataset, l = a.componentId, r = a.disabled, i = +a.stepper;
    if (r) return null;
    n.call(this, l, i + t);
}

function n(e, n) {
    var t = {
        componentId: e,
        stepper: n = +n
    };
    this.handleZanStepperChange ? this.handleZanStepperChange(t) : console.warn("页面缺少 handleZanStepperChange 回调函数");
}

var t = {
    _handleZanStepperMinus: function(n) {
        e.call(this, n, -1);
    },
    _handleZanStepperPlus: function(n) {
        e.call(this, n, 1);
    },
    _handleZanStepperBlur: function(e) {
        var t = this, a = e.currentTarget.dataset, l = a.componentId, r = +a.max, i = +a.min, p = e.detail.value;
        return p ? ((p = +p) > r ? p = r : p < i && (p = i), n.call(this, l, p), "" + p) : (setTimeout(function() {
            n.call(t, l, i);
        }, 16), n.call(this, l, p), "" + p);
    }
};

module.exports = t;