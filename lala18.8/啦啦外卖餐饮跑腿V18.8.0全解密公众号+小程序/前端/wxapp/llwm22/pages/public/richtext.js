var e = getApp();

Page({
    data: {},
    onLoad: function(a) {
        var t = this, s = a.key;
        e.util.request({
            url: "delivery/common/agreement",
            data: {
                key: s
            },
            success: function(a) {
                var s = a.data.message.message.agreement;
                e.WxParse.wxParse("agreement", "html", s, t, 5), wx.setNavigationBarTitle({
                    title: a.data.message.message.title
                });
            }
        });
    }
});