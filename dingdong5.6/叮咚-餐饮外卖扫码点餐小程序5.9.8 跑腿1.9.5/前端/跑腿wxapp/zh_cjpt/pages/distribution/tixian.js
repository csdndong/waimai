var app = getApp();

Page({
    data: {
        hidden3: !1,
        hidden4: !0,
        button: !0,
        cash_zhi2: !1,
        cash_zhi: !1
    },
    onLoad: function(t) {
        var e = this;
        wx.hideShareMenu(), e.setData({
            color: wx.getStorageSync("platform").color
        }), wx.setNavigationBarColor({
            frontColor: "#ffffff",
            backgroundColor: wx.getStorageSync("platform").color
        }), wx.hideShareMenu(), app.util.request({
            url: "entry/wxapp/getSystem",
            cachetime: "0",
            success: function(t) {
                console.log("这是系统设置"), console.log(t), e.setData({
                    system: t.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/TxMoney",
            cachetime: "0",
            data: {
                seller_id: t.seller_id
            },
            success: function(t) {
                console.log("这是可提现金额"), console.log(t), e.setData({
                    price: t.data
                });
            }
        }), e.setData({
            seller_id: t.seller_id
        });
    },
    bindblur: function(t) {
        var e = this.data.system.tx_sxf, a = t.detail.value, o = a * (e / 100), n = a - o;
        n = n.toFixed(2), o = o.toFixed(2), this.setData({
            tx_cost: a,
            sxf: o,
            sj_cost: n
        });
    },
    formSubmit: function(t) {
        var e = this;
        console.log(t), console.log(e.data);
        var a = e.data.seller_id, o = Number(e.data.system.zd_money), n = e.data.sj_cost, s = (e.data.sxf, 
        Number(e.data.price)), l = t.detail.value.name, i = e.data.tx_cost, c = t.detail.value.account_number, r = t.detail.value.account_number_two, u = (e.data.user_id, 
        "");
        "" == i || i <= 0 ? u = "请输入提现金额" : s < i ? u = "不能超过可提现金额" : i < o ? u = "没有到提现门槛" : "" == l ? u = "请输入姓名" : "" == c ? u = "请输入微信账号" : "" == r ? u = "请再次输入微信账号" : c != r && (u = "账号输入有误，请重述"), 
        "" != u ? wx.showModal({
            title: "温馨提示",
            content: u
        }) : app.util.request({
            url: "entry/wxapp/SaveTxApply",
            data: {
                name: l,
                seller_id: a,
                tx_cost: i,
                sj_cost: n,
                username: c
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