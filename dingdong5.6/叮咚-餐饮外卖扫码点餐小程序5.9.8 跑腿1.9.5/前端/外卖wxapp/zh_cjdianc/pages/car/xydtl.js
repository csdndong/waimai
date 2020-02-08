var app = getApp();

Page({
    data: {},
    onLoad: function(t) {
        console.log(t), app.setNavigationBarColor(this), wx.setNavigationBarTitle({
            title: t.title
        }), "会员特权说明" == t.title && this.setData({
            nodes: getApp().xtxx.hy_details.replace(/\<img/gi, '<img style="width:100%;height:auto" ')
        }), "充值服务协议" == t.title && this.setData({
            nodes: getApp().xtxx.cz_notice.replace(/\<img/gi, '<img style="width:100%;height:auto" ')
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