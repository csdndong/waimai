var app = getApp();

Component({
    properties: {},
    data: {},
    methods: {
        getPhoneNumber: function(e) {
            var t = this;
            console.log(e, e.detail.iv, e.detail.encryptedData), "getPhoneNumber:fail user deny" == e.detail.errMsg ? wx.showModal({
                title: "提示",
                showCancel: !1,
                content: "您未授权获取您的手机号",
                success: function(e) {}
            }) : app.util.request({
                url: "entry/wxapp/Jiemi",
                data: {
                    sessionKey: getApp().getSK,
                    data: e.detail.encryptedData,
                    iv: e.detail.iv
                },
                success: function(e) {
                    console.log("解密后的数据", e), null != e.data.phoneNumber && (t.setData({
                        isbd: !0
                    }), wx.showLoading({
                        title: "加载中"
                    }), app.util.request({
                        url: "entry/wxapp/SaveTel",
                        data: {
                            tel: e.data.phoneNumber,
                            user_id: wx.getStorageSync("users").id
                        },
                        success: function(e) {
                            app.globalData.userInfo = null, wx.showToast({
                                title: "手机号绑定成功",
                                icon: "none"
                            }), setTimeout(function() {
                                app.getUserInfo(function(e) {});
                            }, 1e3);
                        }
                    }));
                }
            });
        }
    }
});