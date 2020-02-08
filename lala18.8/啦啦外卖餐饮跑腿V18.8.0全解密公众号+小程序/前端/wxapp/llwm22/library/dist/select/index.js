function e(e) {
    var t = a(e), l = e.detail.value;
    n.call(this, t, l);
}

function n(e, n) {
    var a = {
        componentId: e,
        value: n
    };
    this.handleZanSelectChange ? this.handleZanSelectChange(a) : console.warn("页面缺少 handleZanSelectChange 回调函数");
}

var a = require("../common/helper").extractComponentId;

module.exports = {
    _handleZanSelectChange: function(n) {
        e.call(this, n);
    }
};