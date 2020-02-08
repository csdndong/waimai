var app = getApp();

Page({
    data: {
        hidden3: !1,
        hidden4: !0,
        button: !0,
        cash_zhi2: !1,
        cash_zhi: !1,
        tx_cost: 0,
        sj_cost: 0,
        fwxy: !0,
        xymc: "佣金提现协议",
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
    onLoad: function(t) {
        app.setNavigationBarColor(this);
        var a = this, e = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/MyCommission",
            cachetime: "0",
            data: {
                user_id: e
            },
            success: function(t) {
                console.log(t.data), a.setData({
                    yjdata: t.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/CheckRetail",
            cachetime: "0",
            success: function(t) {
                console.log("CheckRetail", t.data), a.setData({
                    fxset: t.data,
                    xynr: t.data.tx_details
                });
            }
        }), a.setData({
            seller_id: t.seller_id
        });
    },
    bindblur: function(t) {
        var a = Number(this.data.fxset.tx_rate), e = Number(t.detail.value), o = e * (a / 100), s = e - o;
        s = s.toFixed(2), o = o.toFixed(2), this.setData({
            tx_cost: e,
            sxf: o,
            sj_cost: s
        });
    },
    formSubmit: function(t) {
        var a = this;
        console.log(t), console.log(a.data);
        var e = wx.getStorageSync("users").id, o = Number(a.data.fxset.tx_money), s = a.data.sj_cost, n = (a.data.sxf, 
        Number(a.data.yjdata.ktxyj)), i = t.detail.value.name, c = a.data.tx_cost, u = t.detail.value.account_number, l = t.detail.value.account_number_two, d = t.detail.value.checkbox.length;
        console.log("zd_money", o, "sj_cost", s, "ktxyj", n, "tx_cost", c, i, u, l, "user_id", e, d);
        var r = "";
        "" == c || c <= 0 ? r = "请输入提现金额" : n < c ? r = "不能超过可提现金额" : c < o ? r = "没有到提现门槛" : "" == i ? r = "请输入姓名" : "" == u ? r = "请输入联系电话" : "" == l ? r = "请再次输入联系电话" : u != l ? r = "联系电话不一致，请重新输入" : 0 == d && (r = "阅读并同意《佣金提现协议》"), 
        "" != r ? wx.showModal({
            title: "温馨提示",
            content: r
        }) : (wx.showLoading({
            title: "加载中",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/SaveYjtx",
            data: {
                user_id: e,
                user_name: i,
                account: u,
                tx_cost: c,
                sj_cost: s
            },
            success: function(t) {
                console.log(t), 1 == t.data && (wx.showToast({
                    title: "发起提现申请"
                }), setTimeout(function() {
                    wx.navigateBack({
                        delta: 1
                    });
                }, 1e3));
            }
        }));
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