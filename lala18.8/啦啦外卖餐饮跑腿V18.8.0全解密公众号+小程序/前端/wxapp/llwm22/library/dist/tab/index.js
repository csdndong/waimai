var e = require("../common/helper").extractComponentId, a = {
    _handleZanTabChange: function(a) {
        var n = {
            componentId: e(a),
            selectedId: a.currentTarget.dataset.itemId
        };
        this.handleZanTabChange ? this.handleZanTabChange(n) : console.warn("页面缺少 handleZanTabChange 回调函数");
    }
};

module.exports = a;