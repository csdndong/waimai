var app = new getApp();

Page({
    data: {},
    onLoad: function(n) {},
    updateUserInfo: function(n) {
        var t = getApp(), r = t.siteInfo.uniacid;
        t.util.getUserInfo(function(n) {
            console.log(n), t.globalData.userInfo = n.memberInfo, t.globalData.uid = n.memberInfo.uid, 
            t.globalData.sessionid = n.sessionid, wx.setStorageSync("kundian_ordering_uid", n.memberInfo.uid), 
            wx.setStorageSync("kundian_ordering_userInfo", n.memberInfo), wx.setStorageSync("kundian_ordering_sessionid", n.sessionid), 
            wx.setStorageSync("kundian_ordering_wxInfo", n.wxInfo);
            var e = n.memberInfo, a = n.wxInfo.avatarUrl, o = n.wxInfo.nickName, i = {
                control: "index",
                avatarUrl: e.avatar,
                uid: e.uid,
                nickname: e.nickname,
                uniacid: r,
                op: "login",
                wxNickName: o,
                wxAvatar: a
            };
            t.util.request({
                url: "entry/wxapp/order",
                data: i,
                success: function(n) {
                    0 == n.data.code ? wx.showToast({
                        title: "登陆成功",
                        success: function() {
                            wx.navigateBack({
                                delta: 1
                            });
                        }
                    }) : wx.showToast({
                        title: "登录失败"
                    });
                }
            });
        });
    }
});