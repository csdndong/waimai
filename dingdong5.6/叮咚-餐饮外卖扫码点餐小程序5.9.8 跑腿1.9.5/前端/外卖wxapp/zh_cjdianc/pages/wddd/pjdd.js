var app = getApp(), count = 0, imgArray = [], siteinfo = require("../../../siteinfo.js");

Page({
    data: {
        stars: [ 0, 1, 2, 3, 4 ],
        normalSrc: "../../img/no-star.png",
        selectedSrc: "../../img/full-star.png",
        key: 0,
        count: 0,
        images: [],
        sctp: !1
    },
    sctp: function() {
        this.setData({
            sctp: !0
        });
    },
    selectLeft: function(t) {
        console.log("111111");
        var o = t.currentTarget.dataset.key;
        1 == this.data.key && 1 == t.currentTarget.dataset.key && (o = 0), count = o, this.setData({
            key: o,
            count: count
        });
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this);
        var o = this, e = wx.getStorageSync("users");
        console.log(t, e), o.setData({
            order_id: t.oid
        }), wx.showLoading({
            title: "正在加载",
            mask: !0
        }), app.util.request({
            url: "entry/wxapp/OrderInfo",
            data: {
                order_id: t.oid
            },
            success: function(t) {
                console.log(t), o.setData({
                    good: t.data.good,
                    orderinfo: t.data
                });
            }
        });
    },
    contentInput: function(t) {
        this.setData({
            pjnr: t.detail.value
        });
    },
    chooseImage: function(t) {
        var o = this, e = this.data.images, a = e.length;
        console.log(e), wx.chooseImage({
            count: 3 - a,
            success: function(t) {
                e = e.concat(t.tempFilePaths), o.setData({
                    images: e
                }), console.log(e);
            }
        });
    },
    deleteImage: function(t) {
        var o = t.currentTarget.dataset.index, e = this.data.images;
        console.log(o), e.splice(o, 1), this.setData({
            images: e
        }), console.log(e);
    },
    commentSubmit: function(t) {
        var o = wx.getStorageSync("users").id, e = this.data.orderinfo.store.id, a = this.data.orderinfo.order.id, n = this.data.pjnr, s = this.data.count, i = this.data.images;
        if (console.log(a, o, e, n, s, i), 0 == s) wx.showModal({
            title: "提示",
            content: "请选择评分"
        }); else if (null == n || "" == n) wx.showModal({
            title: "提示",
            content: "请输入评价内容"
        }); else {
            var c = function() {
                console.log("请求接口", imgArray, imgArray.toString()), app.util.request({
                    url: "entry/wxapp/Assess",
                    cachetime: "0",
                    data: {
                        store_id: e,
                        user_id: o,
                        order_id: a,
                        stars: s,
                        content: n,
                        img: imgArray.toString()
                    },
                    success: function(t) {
                        "1" == t.data && (wx.showModal({
                            title: "提示",
                            content: "提交成功"
                        }), setTimeout(function() {
                            wx.redirectTo({
                                url: "order?status=4"
                            });
                        }, 1e3)), console.log("Assess", t.data);
                    }
                });
            };
            wx.showLoading({
                title: "正在提交",
                mask: !0
            }), 0 == i.length ? c() : function t(o) {
                var e = o.i ? o.i : 0, a = o.success ? o.success : 0, n = o.fail ? o.fail : 0;
                wx.uploadFile({
                    url: o.url,
                    filePath: o.path[e],
                    name: "upfile",
                    formData: null,
                    success: function(t) {
                        "" != t.data ? (console.log(t), a++, imgArray.push(t.data), console.log(e), console.log("图片数组", imgArray)) : wx.showToast({
                            icon: "loading",
                            title: "请重试"
                        });
                    },
                    fail: function(t) {
                        n++, console.log("fail:" + e + "fail:" + n);
                    },
                    complete: function() {
                        console.log(e), ++e == o.path.length ? (wx.hideToast(), console.log("执行完毕"), c(), 
                        console.log("成功：" + a + " 失败：" + n)) : (console.log(e), o.i = e, o.success = a, 
                        o.fail = n, t(o));
                    }
                });
            }({
                url: siteinfo.siteroot + "?i=" + siteinfo.uniacid + "&c=entry&a=wxapp&do=upload&m=zh_cjdianc",
                path: i
            });
        }
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});