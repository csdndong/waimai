var app = new getApp(), uniacid = app.siteInfo.uniacid;

Page({
    data: {
        slideData: [],
        aboutData: [],
        setData: [],
        deskConfig: [],
        navData: []
    },
    onLoad: function(a) {
        var d = this;
        app.globalData.uid || (app.globalData.uid = wx.getStorageSync("kundian_ordering_uid"), 
        app.globalData.userInfo = wx.getStorageSync("userInfo"));
        var t = wx.getStorageSync("kundian_ordering_uid");
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "index",
                op: "index",
                uid: t,
                uniacid: uniacid
            },
            success: function(a) {
                app.globalData.deskConfig = a.data.deskConfig, wx.setStorage({
                    key: "configMode",
                    data: 0
                });
                var t = a.data, e = t.slideData, n = t.aboutData, o = t.deskConfig, i = t.navData;
                d.setData({
                    slideData: e,
                    aboutData: n,
                    deskConfig: o,
                    navData: i
                });
            }
        });
    },
    openAddress: function(a) {
        var t = this.data.aboutData;
        wx.openLocation({
            latitude: parseFloat(t.longitude),
            longitude: parseFloat(t.latitude),
            scale: 28,
            name: t.merchant_name,
            address: t.address
        });
    },
    doCall: function(a) {
        var t = this.data.aboutData;
        wx.makePhoneCall({
            phoneNumber: t.phone
        });
    },
    intoNav: function(a) {
        var t = a.currentTarget.dataset.path;
        1 == t ? wx.scanCode({
            success: function(a) {
                var t = a.path;
                wx.navigateTo({
                    url: "../../../../" + t
                });
            }
        }) : wx.navigateTo({
            url: "../../../../" + t
        });
    },
    onShareAppMessage: function() {}
});