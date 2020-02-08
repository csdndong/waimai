var t = getApp();

Page({
    data: {},
    onLoad: function(e) {
        var a = this;
        if (!e.id) return t.util.toast("参数错误"), !1;
        t.util.request({
            url: "manage/news/notice/detail",
            data: {
                id: e.id
            },
            success: function(e) {
                var s = e.data.message.message.notice.content;
                t.WxParse.wxParse("content", "html", s, a, 5), wx.setNavigationBarTitle({
                    title: e.data.message.message.notice.title
                });
            }
        });
    }
});