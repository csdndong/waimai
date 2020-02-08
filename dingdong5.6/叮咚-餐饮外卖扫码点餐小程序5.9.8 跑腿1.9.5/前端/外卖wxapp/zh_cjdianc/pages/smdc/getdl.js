var a = getApp();

Page({
    data: {},
    bindGetUserInfo: function(n) {
        console.log(n);
        var o = getCurrentPages();
        console.log(o), "getUserInfo:ok" == n.detail.errMsg && (wx.showLoading({
            title: "登录中...",
            mask: !0
        }), wx.getUserInfo({
            success: function(n) {
                console.log(n), a.util.request({
                    url: "entry/wxapp/login",
                    cachetime: "0",
                    data: {
                        openid: getApp().getOpenId,
                        img: n.userInfo.avatarUrl,
                        name: n.userInfo.nickName
                    },
                    header: {
                        "content-type": "application/json"
                    },
                    dataType: "json",
                    success: function(n) {
                        (console.log("用户信息", n), a.globalData.userInfo = n.data, 1 < o.length) && o[o.length - 2].setData({
                            userinfo: n.data
                        });
                        setTimeout(function() {
                            wx.navigateBack({});
                        }, 1e3);
                    }
                });
            }
        }));
    },
    onLoad: function(n) {
        a.setNavigationBarColor(this);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});