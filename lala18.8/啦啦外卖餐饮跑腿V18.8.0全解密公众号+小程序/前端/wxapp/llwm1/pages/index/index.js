var s = getApp();

Page({
    data: {
        motto: "Hello World",
        userInfo: {},
        hasUserInfo: !1,
        canIUse: wx.canIUse("button.open-type.getUserInfo")
    },
    bindViewTap: function() {
        wx.navigateTo({
            url: "../logs/logs"
        });
    },
    onLoad: function() {
        var e = this;
        s.globalData.userInfo ? this.setData({
            userInfo: s.globalData.userInfo,
            hasUserInfo: !0
        }) : this.data.canIUse ? s.userInfoReadyCallback = function(s) {
            e.setData({
                userInfo: s.userInfo,
                hasUserInfo: !0
            });
        } : wx.getUserInfo({
            success: function(o) {
                s.globalData.userInfo = o.userInfo, e.setData({
                    userInfo: o.userInfo,
                    hasUserInfo: !0
                });
            }
        });
    },
    getUserInfo: function(e) {
        console.log(e), s.globalData.userInfo = e.detail.userInfo, this.setData({
            userInfo: e.detail.userInfo,
            hasUserInfo: !0
        });
    }
});