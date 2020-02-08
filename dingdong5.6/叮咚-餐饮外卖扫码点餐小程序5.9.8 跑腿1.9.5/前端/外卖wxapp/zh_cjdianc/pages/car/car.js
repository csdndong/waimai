var app = getApp(), screenWidth = 0, screenHeight = 0, screenWidth1 = 0, screenHeight1 = 0, screenWidth2 = 0, screenHeight2 = 0;

Page({
    data: {},
    onLoad: function(e) {
        console.log(e);
        console.log(wx.getStorageSync("vr")), this.setData({
            vr: wx.getStorageSync("vr")
        });
    },
    canvas: function(e) {
        var n = this;
        console.log(e), wx.canvasToTempFilePath({
            x: 0,
            y: 0,
            width: 400,
            height: 200,
            destWidth: 400,
            destHeight: 600,
            canvasId: "firstCanvas",
            success: function(e) {
                console.log(e), wx.saveImageToPhotosAlbum({
                    filePath: e.tempFilePath,
                    success: function(e) {
                        console.log(e), wx.showToast({
                            title: "保存成功",
                            icon: "success",
                            duration: 2e3
                        });
                    }
                }), n.setData({
                    tempFilePath: e.tempFilePath
                });
            },
            fail: function(e) {
                console.log(e);
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});