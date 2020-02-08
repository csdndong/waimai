var app = getApp();

Page({
    data: {},
    tel: function() {
        wx.makePhoneCall({
            phoneNumber: this.data.tel
        });
    },
    onLoad: function(n) {
        app.setNavigationBarColor(this), wx.setNavigationBarTitle({
            title: n.title
        });
        console.log(wx.getStorageSync("answer")), this.setData({
            answer: wx.getStorageSync("answer")
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});