var s = getApp();

Page({
    data: {},
    onLoad: function(s) {},
    onSubmit1: function(a) {
        var t = a.detail.value;
        if (!t.password) return s.util.toast("原密码不能为空"), !1;
        if (!t.newpassword) return s.util.toast("密码不能为空"), !1;
        var e = t.newpassword.length;
        if (e < 8 || e > 20) return s.util.toast("请输入8-20位密码"), !1;
        if (!/[0-9]+[a-zA-Z]+[0-9a-zA-Z]*|[a-zA-Z]+[0-9]+[0-9a-zA-Z]*/.test(t.newpassword)) return s.util.toast("密码必须由数字和字母组合"), 
        !1;
        if (!t.repassword) return s.util.toast("请重复输入密码"), !1;
        if (t.newpassword != t.repassword) return s.util.toast("两次密码输入不一致"), !1;
        var o = {
            password: t.password,
            newpassword: t.newpassword,
            repassword: t.repassword,
            formid: a.detail.formId
        };
        s.util.request({
            url: "manage/more/profile/password",
            data: o,
            method: "POST",
            success: function(a) {
                0 == a.data.message.errno ? s.util.toast(a.data.message.message, "../shop/setting", 1e3) : s.util.toast(a.data.message.message);
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {},
    oldPassword: function(s) {
        this.setData({
            password: s.detail.value
        });
    },
    newPassword: function(s) {
        this.setData({
            newPassword: s.detail.value
        });
    },
    checkPassword: function(s) {
        this.setData({
            rePassword: s.detail.value
        });
    },
    onSubmit: function() {
        var a = this, t = {
            password: a.data.password,
            newpassword: a.data.newPassword,
            repassword: a.data.rePassword
        };
        s.util.request({
            url: "manage/more/profile/password",
            data: t,
            method: "POST",
            success: function(s) {
                0 == s.data.message.errno && wx.redirectTo({
                    url: "profile"
                });
            }
        });
    }
});