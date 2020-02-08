var s = getApp();

Page({
    data: {},
    onLoad: function(s) {},
    onSubmit: function(e) {
        var a = e.detail.value;
        if (a.oldPassword) if (a.newPassword) if (a.checkPassword) {
            var t = {
                password: a.oldPassword,
                newpassword: a.newPassword,
                repassword: a.checkPassword
            };
            s.util.request({
                url: "delivery/member/mine/password",
                data: t,
                success: function(e) {
                    0 == e.data.message.errno ? s.util.toast(e.data.message.message, "setting", 1e3) : s.util.toast(e.data.message.message, "", 1e3);
                }
            });
        } else s.util.toast("请确认密码", "", 1e3); else s.util.toast("新密码不能为空", "", 1e3); else s.util.toast("密码不能为空", "", 1e3);
    }
});