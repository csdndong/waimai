var e = getApp();

Page({
    data: {},
    onLoad: function(n) {
        var t = this, a = {
            key: n.key
        };
        n.pageid && (a.pageid = n.pageid), n.helpid && (a.helpid = n.helpid), (n.key = "notice") && (a.noticeid = n.noticeid), 
        e.util.request({
            url: "wmall/common/agreement",
            data: a,
            success: function(n) {
                var a = n.data.message.message.agreement;
                e.WxParse.wxParse("agreement", "html", a, t, 5), wx.setNavigationBarTitle({
                    title: n.data.message.message.title
                });
            }
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