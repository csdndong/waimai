var o = getApp(), n = o.requirejs("core");

o.requirejs("icons"), o.requirejs("jquery");

Page({
    data: {
        show: !0
    },
    onLoad: function(o) {
        var e = this;
        n.get("package.get_list", {
            goodsid: o.id
        }, function(o) {
            console.log(o.list), e.setData({
                list: o.list
            });
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});