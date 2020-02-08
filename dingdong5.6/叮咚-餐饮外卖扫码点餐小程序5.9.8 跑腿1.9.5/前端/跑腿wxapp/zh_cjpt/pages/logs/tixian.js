var app = getApp();

Page({
    data: {
        hidden3: !1,
        hidden4: !0,
        button: !0,
        cash_zhi2: !1,
        cash_zhi: !1,
        tx_cost: 0
    },
    onLoad: function(t) {
        var o = this;
        wx.hideShareMenu(), app.getSystem(function(t) {
            console.log(t), o.setData({
                getSystem: t,
                color: t.color
            }), wx.setNavigationBarColor({
                frontColor: "#ffffff",
                backgroundColor: t.color
            });
        }), o.setData({
            color: wx.getStorageSync("platform").color
        });
        var e = wx.getStorageSync("qs").id;
        app.util.request({
            url: "entry/wxapp/KtxMoney",
            cachetime: "0",
            data: {
                qs_id: e
            },
            success: function(t) {
                console.log("这是可提现金额"), console.log(t), o.setData({
                    price: t.data
                });
            }
        }), o.setData({
            seller_id: t.seller_id
        });
    },
    bindblur: function(t) {
        var o = this.data.getSystem.tx_sxf, e = t.detail.value, a = e * (o / 100), n = e - a;
        n = n.toFixed(2), a = a.toFixed(2), this.setData({
            tx_cost: e,
            sxf: a,
            sj_cost: n
        });
    },
    formSubmit: function(t) {
        var o = this;
        console.log(t), console.log(o.data);
        var e = wx.getStorageSync("qs").id, a = Number(o.data.getSystem.tx_zdmoney), n = o.data.sj_cost, s = (o.data.sxf, 
        Number(o.data.price)), c = t.detail.value.name, i = o.data.tx_cost, l = t.detail.value.account_number, r = t.detail.value.account_number_two, d = (o.data.user_id, 
        "");
        "" == i || i <= 0 ? d = "请输入提现金额" : s < i ? d = "不能超过可提现金额" : i < a ? d = "没有到提现门槛" : "" == c ? d = "请输入姓名" : "" == l ? d = "请输入微信账号" : "" == r ? d = "请再次输入微信账号" : l != r && (d = "账号输入有误，请重述"), 
        "" != d ? wx.showModal({
            title: "温馨提示",
            content: d
        }) : app.util.request({
            url: "entry/wxapp/Savetx",
            data: {
                name: c,
                qs_id: e,
                tx_cost: i,
                sj_cost: n,
                user_name: l
            },
            cachetime: "5000",
            method: "POST",
            header: {
                "content-type": "application/x-www-form-urlencoded"
            },
            success: function(t) {
                console.log(t), 1 == t.data && (wx.showToast({
                    title: "发起提现申请"
                }), setTimeout(function() {
                    wx.navigateBack({
                        delta: 1
                    });
                }, 1500));
            }
        });
    },
    inform: function(t) {
        wx.navigateTo({
            url: "inform?status=2"
        });
    },
    onReady: function() {},
    onShow: function() {
        console.log(this.data);
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});