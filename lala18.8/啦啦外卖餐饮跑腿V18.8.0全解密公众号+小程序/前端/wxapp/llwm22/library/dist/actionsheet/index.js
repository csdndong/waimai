function n(n) {
    var e = n.componentId;
    this.handleZanActionsheetCancel ? this.handleZanActionsheetCancel({
        componentId: e
    }) : console.warn("页面缺少 handleZanActionsheetCancel 回调函数");
}

var e = require("../common/helper").extractComponentId;

module.exports = {
    _handleZanActionsheetMaskClick: function(e) {
        var t = e.currentTarget, c = (void 0 === t ? {} : t).dataset || {}, a = c.componentId;
        c.closeOnClickOverlay && n.call(this, {
            componentId: a
        });
    },
    _handleZanActionsheetCancelBtnClick: function(t) {
        var c = e(t);
        n.call(this, {
            componentId: c
        });
    },
    _handleZanActionsheetBtnClick: function(n) {
        var e = n.currentTarget, t = (void 0 === e ? {} : e).dataset || {}, c = t.componentId, a = t.index;
        this.handleZanActionsheetClick ? this.handleZanActionsheetClick({
            componentId: c,
            index: a
        }) : console.warn("页面缺少 handleZanActionsheetClick 回调函数");
    }
};