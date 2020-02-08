var n = require("../common/helper").extractComponentId;

module.exports = {
    _handleZanFieldChange: function(e) {
        var a = n(e);
        if (e.componentId = a, this.handleZanFieldChange) return this.handleZanFieldChange(e);
        console.warn("页面缺少 handleZanFieldChange 回调函数");
    },
    _handleZanFieldFocus: function(e) {
        var a = n(e);
        if (e.componentId = a, this.handleZanFieldFocus) return this.handleZanFieldFocus(e);
    },
    _handleZanFieldBlur: function(e) {
        var a = n(e);
        if (e.componentId = a, this.handleZanFieldBlur) return this.handleZanFieldBlur(e);
    }
};