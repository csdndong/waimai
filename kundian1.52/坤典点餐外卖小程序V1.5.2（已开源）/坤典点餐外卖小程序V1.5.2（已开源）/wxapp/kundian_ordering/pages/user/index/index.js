var app = new getApp(), uniacid = app.siteInfo.uniacid;

Page({
    data: {
        userInfo: [],
        is_hexiao: 1,
        aboutData: [],
        nickName: "",
        avatarUrl: "",
        setData: [],
        back_img: "",
        center_img: ""
    },
    onLoad: function(a) {
        var n = wx.getStorageSync("kundian_ordering_uid");
        this.getCenterData();
        var e = wx.getStorageSync("kundian_ordering_userInfo");
        this.setData({
            nickName: e.nickname,
            avatarUrl: e.avatar
        }), n || wx.navigateTo({
            url: "../../login/index"
        });
    },
    getCenterData: function() {
        var s = this, a = wx.getStorageSync("kundian_ordering_uid");
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "index",
                op: "checkHexiao",
                uid: a,
                uniacid: uniacid
            },
            success: function(a) {
                var n = a.data, e = n.is_hexiao, t = n.aboutData, i = n.setData, o = n.back_img, r = n.center_img;
                s.setData({
                    is_hexiao: e,
                    aboutData: t,
                    setData: i,
                    back_img: o,
                    center_img: r
                });
            }
        });
    },
    intoSet: function(a) {
        wx.openSetting({
            success: function(a) {
                a.authSetting = {
                    "scope.userInfo": !0,
                    "scope.userLocation": !0
                };
            }
        });
    },
    intoAddress: function(a) {
        wx.navigateTo({
            url: "/kundian_ordering/pages/address/index"
        });
    },
    intoShop: function(a) {
        wx.navigateTo({
            url: "../order/index"
        });
    },
    intoDesk: function(a) {
        wx.navigateTo({
            url: "../../desk/tables/index"
        });
    },
    updateUserInfo: function(a) {
        var o = getApp(), r = this, s = o.siteInfo.uniacid;
        o.util.getUserInfo(function(a) {
            o.globalData.userInfo = a.memberInfo, o.globalData.uid = a.memberInfo.uid, o.globalData.sessionid = a.sessionid, 
            wx.setStorageSync("kundian_ordering_uid", a.memberInfo.uid), wx.setStorageSync("kundian_ordering_userInfo", a.memberInfo), 
            wx.setStorageSync("kundian_ordering_sessionid", a.sessionid), wx.setStorageSync("kundian_ordering_wxInfo", a.wxInfo);
            var n = a.memberInfo, e = a.wxInfo.avatarUrl, t = a.wxInfo.nickName, i = {
                avatarUrl: n.avatar,
                uid: n.uid,
                nickname: n.nickname,
                uniacid: s,
                op: "login",
                wxNickName: t,
                wxAvatar: e
            };
            o.util.request({
                url: "entry/wxapp/index",
                data: i,
                success: function(a) {
                    0 == a.data.code ? (wx.showToast({
                        title: "登陆成功"
                    }), r.setData({
                        nickName: n.nickname,
                        avatarUrl: n.avatar
                    })) : wx.showToast({
                        title: "登录失败"
                    });
                }
            });
        });
    },
    onShow: function(a) {
        var n = wx.getStorageSync("kundian_ordering_wxInfo");
        this.setData({
            nickName: n.nickName,
            avatarUrl: n.avatarUrl
        }), this.getCenterData();
    },
    onShareAppMessage: function() {}
});