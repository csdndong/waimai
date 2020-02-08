var app = getApp();

Page({
    data: {
        accountIndex: 0,
        fwxy: !0,
        xymc: "申请分销商协议",
        xynr: ""
    },
    lookck: function() {
        this.setData({
            fwxy: !1
        });
    },
    queren: function() {
        this.setData({
            fwxy: !0
        });
    },
    bindAccountChange: function(t) {
        console.log("picker account 发生选择改变，携带值为", t.detail.value), this.setData({
            accountIndex: t.detail.value
        });
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this);
        var e = this, a = wx.getStorageSync("users").id;
        app.util.request({
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
                console.log(t.data), e.setData({
                    img: t.data.img2,
                    xynr: t.data.fx_details,
                    fxset: t.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/MySx",
            cachetime: "0",
            data: {
                user_id: a
            },
            success: function(t) {
                console.log(t.data), t.data ? e.setData({
                    yqr: t.data.name
                }) : e.setData({
                    yqr: "总店"
                });
            }
        }), app.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(t) {
                console.log(t), e.setData({
                    pt_name: t.data.url_name
                });
            }
        });
    },
    tzweb: function(t) {
        console.log(t.currentTarget.dataset.index, this.data.lblist);
        var e = this.data.lblist[t.currentTarget.dataset.index], a = t.currentTarget.dataset.sjtype;
        console.log(e), "1" == e.state && wx.redirectTo({
            url: e.src
        }), "2" == e.state && wx.navigateTo({
            url: "../car/car?vr=" + e.id + "&sjtype=" + a,
            success: function(t) {},
            fail: function(t) {},
            complete: function(t) {}
        }), "3" == e.state && wx.navigateToMiniProgram({
            appId: e.appid,
            success: function(t) {
                console.log(t);
            }
        });
    },
    formSubmit: function(t) {
        console.log("form发生了submit事件，携带数据为：", t.detail);
        var e = t.detail.value.name, a = t.detail.value.tel, n = t.detail.value.checkbox.length, o = wx.getStorageSync("users").id, s = getApp().getOpenId, i = t.detail.formId;
        console.log(o, s, i, e, a);
        var c = "", l = !0;
        "" == e ? c = "请填写姓名！" : "" == a ? c = "请填写联系电话！" : 11 != a.length ? c = "手机号错误！" : 0 == n ? c = "阅读并同意《申请分销商协议》" : (l = !1, 
        wx.showLoading({
            title: "加载中...",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/SaveRetail",
            cachetime: "0",
            data: {
                user_id: o,
                user_name: e,
                user_tel: a
            },
            success: function(t) {
                if (console.log(t), "1" == t.data) {
                    wx.showToast({
                        title: "提交成功"
                    });
                    var e = getCurrentPages();
                    if (console.log(e), 1 < e.length) e[e.length - 2].reLoad();
                    setTimeout(function() {
                        wx.navigateBack({});
                    }, 1e3);
                } else wx.showToast({
                    title: "请重试！",
                    icon: "loading"
                }), wx.hideLoading();
            }
        })), 1 == l && wx.showModal({
            title: "提示",
            content: c
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});