var e = {
    _handleZanSwitchChange: function(e) {
        var n = e.currentTarget.dataset, a = !n.checked, h = n.loading, t = n.disabled, c = n.componentId;
        h || t || (this.handleZanSwitchChange ? this.handleZanSwitchChange({
            checked: a,
            componentId: c
        }) : console.warn("页面缺少 handleZanSwitchChange 回调函数"));
    }
};

module.exports = e;