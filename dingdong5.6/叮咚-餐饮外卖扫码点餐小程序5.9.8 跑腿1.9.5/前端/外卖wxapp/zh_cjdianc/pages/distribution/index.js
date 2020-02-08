var app = getApp();

Page({
    data: {
        color: "#459cf9",
        select_code: !0
    },
    ljsq: function() {
        wx.navigateTo({
            url: "jrhhr"
        });
    },
    onLoad: function(t) {
        wx.hideShareMenu({}), app.setNavigationBarColor(this);
        var e = this, a = wx.getStorageSync("users");
        console.log(a), this.setData({
            userid: a.id,
            username: a.name,
            pt_name: getApp().xtxx.url_name
        }), app.util.request({
            url: "entry/wxapp/Url",
            cachetime: "0",
            success: function(t) {
                console.log(t), e.setData({
                    url: t.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/CheckRetail",
            cachetime: "0",
            success: function(t) {
                console.log(t), e.setData({
                    fxset: t.data
                });
            }
        }), this.reLoad();
    },
    reLoad: function() {
        var e = this, t = wx.getStorageSync("users").id;
        console.log(t), app.util.request({
            url: "entry/wxapp/MyDistribution",
            cachetime: "0",
            data: {
                user_id: t
            },
            success: function(t) {
                console.log(t.data), "2" == t.data.state ? (console.log("是分销商"), e.setData({
                    isfxs: 2
                })) : "1" == t.data.state ? (console.log("待审核"), e.setData({
                    isfxs: 1
                })) : (console.log("未申请过"), e.setData({
                    isfxs: 3
                }));
            }
        });
    },
    se_code: function(t) {
        wx.navigateTo({
            url: "core"
        });
    },
    onReady: function() {},
    onShow: function() {
        var e = this, t = wx.getStorageSync("users").id;
        console.log(t), app.util.request({
            url: "entry/wxapp/GetFxData",
            cachetime: "0",
            data: {
                user_id: t
            },
            success: function(t) {
                console.log(t.data), e.setData({
                    fxdata: t.data
                });
            }
        });
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function(t) {
        return console.log(this.data.pt_name, this.data.userid, this.data.username), console.log(t), 
        "menu" !== t.from && {
            title: this.data.username + "邀请你来看看" + this.data.pt_name,
            path: "/zh_cjdianc/pages/Liar/loginindex?userid=" + this.data.userid,
            success: function(t) {},
            fail: function(t) {}
        };
    }
});