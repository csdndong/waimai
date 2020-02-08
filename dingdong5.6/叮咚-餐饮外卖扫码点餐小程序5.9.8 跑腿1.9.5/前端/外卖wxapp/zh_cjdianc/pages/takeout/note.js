var app = getApp();

Page({
    data: {
        bzarr: [ {
            name: "不要辣",
            checked: !1
        }, {
            name: "少点辣",
            checked: !1
        }, {
            name: "不要葱",
            checked: !1
        }, {
            name: "多点葱",
            checked: !1
        }, {
            name: "多点醋",
            checked: !1
        } ],
        selectedindex: 0,
        color: "#34aaff",
        bznr: ""
    },
    selected: function(e) {
        var n = e.currentTarget.dataset.index, t = this.data.bzarr;
        console.log(n), t[n].checked = !0, this.setData({
            bzarr: t
        });
    },
    bznr: function(e) {
        console.log(e.detail.value), this.setData({
            bznr: e.detail.value
        });
    },
    submitbz: function() {
        for (var e = this.data.bzarr, n = [], t = this.data.bznr, a = 0; a < e.length; a++) e[a].checked && n.push(e[a].name);
        if ("" == t && 0 == n.length) return wx.showModal({
            title: "提示",
            content: "请选择标签或者输入备注后提交！"
        }), !1;
        console.log(t, n.toString() + t), wx.setStorageSync("note", n.toString() + t), wx.navigateBack({});
    },
    onLoad: function(e) {
        app.setNavigationBarColor(this);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});