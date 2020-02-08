var app = getApp();

Page({
    data: {
        page: 1,
        list: []
    },
    onLoad: function(o) {
        var t = this;
        wx.hideShareMenu(), app.getSystem(function(o) {
            console.log(o), t.setData({
                getSystem: o,
                color: o.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: o.color
            });
        }), t.list();
    },
    list: function(o) {
        var a = this;
        app.util.request({
            url: "entry/wxapp/GetHelp",
            success: function(o) {
                console.log(o);
                var t = o.data;
                for (var n in t) t[n].class = "none";
                a.setData({
                    help: t
                });
            }
        });
    },
    show: function(o) {
        var t = this.data.help, n = o.currentTarget.dataset.index;
        for (var a in t) a == n ? t[n].class = "show" : t[a].class = "none";
        console.log(t), this.setData({
            help: t
        });
    },
    help_info: function(o) {
        console.log(o);
        var t = o.currentTarget.dataset.info;
        wx.setStorageSync("info", t), wx.navigateTo({
            url: "help"
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        this.list();
    }
});