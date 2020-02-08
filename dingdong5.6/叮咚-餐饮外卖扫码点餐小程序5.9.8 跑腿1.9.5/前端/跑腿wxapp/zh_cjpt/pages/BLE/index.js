Page({
    data: {},
    onLoad: function(n) {},
    choose: function(n) {
        wx.chooseImage({
            count: 1,
            sizeType: [ "original", "compressed" ],
            sourceType: [ "camera" ],
            success: function(n) {
                console.log(n);
                n.tempFilePaths;
            }
        });
    },
    emoji: function(n) {
        var o = n.detail.value;
        "" != o && this.setData({
            text: this.decodeUnicode(this.encodeUnicode(o))
        });
    },
    encodeUnicode: function(n) {
        for (var o = [], e = 0; e < n.length; e++) o[e] = ("00" + n.charCodeAt(e).toString(16)).slice(-4);
        return "\\u" + o.join("\\u");
    },
    decodeUnicode: function(n) {
        return n = n.replace(/\\/g, "%"), unescape(n);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});