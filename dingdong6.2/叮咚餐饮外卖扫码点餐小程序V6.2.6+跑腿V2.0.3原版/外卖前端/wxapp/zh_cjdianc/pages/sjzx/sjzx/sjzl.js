/*   time:2019-07-18 01:03:18*/
var imgArray, lbimgArray, app = getApp(),
    util = require("../../../utils/util.js"),
    siteinfo = require("../../../../siteinfo.js"),
    imgArray1 = [],
    lbimgArray1 = [],
    imglogo = "";
Page({
    data: {
        mdid: "",
        issq: !1,
        isbj: !0,
        isyz: !0,
        isbd: !1,
        url1: "",
        url2: "",
        VerifyCode: "验证",
        bdsjhtext: "验证微信手机号",
        lxr: "",
        sjh: "",
        yzm: "",
        jwd: "",
        mdmc: "",
        mdgg: "",
        zsnum: 0,
        hy: [],
        checkbox: [],
        hyIndex: 0,
        timestart: "06:00",
        timeend: "22:00",
        weizhi: "",
        checkboxItems: [{
            name: "WIFI",
            value: "WIFI"
        }, {
            name: "停车位",
            value: "停车位"
        }, {
            name: "支付宝支付",
            value: "支付宝支付"
        }, {
            name: "微信支付",
            value: "微信支付"
        }],
        logo: "../../img/logo.png",
        images: [],
        images1: [],
        lbimages: [],
        lbimages1: [],
        uploadedImages: [],
        fwxy: !0
    },
    lookck: function() {
        this.setData({
            fwxy: !1
        })
    },
    queren: function() {
        this.setData({
            fwxy: !0
        })
    },
    hqsjh: function(e) {
        console.log(e.detail.value), this.setData({
            sjh: e.detail.value
        }), "" != e.detail.value ? this.setData({
            isyz: !1
        }) : this.setData({
            isyz: !0
        })
    },
    setVerify: function() {
        var e = util.getRandomNum();
        this.setData({
            yzm: e
        });
        var a = this.data.sjh;
        console.log(a), console.log(e);
        var t = 60,
            o = this;
        if (!/^0?(13[0-9]|15[012356789]|17[013678]|18[0-9]|14[57])[0-9]{8}$/.test(a) || 11 != a.length) return wx.showToast({
            title: "手机号错误",
            duration: 1e3
        }), !1;
        var i = setInterval(function() {
            0 < --t ? o.setData({
                VerifyCode: t + " 秒",
                isyz: !0
            }) : (o.setData({
                VerifyCode: "验证",
                isyz: !1
            }), clearInterval(i))
        }, 1e3);
        app.util.request({
            url: "entry/wxapp/sms2",
            cachetime: "0",
            headers: {
                "Content-Type": "application/json"
            },
            data: {
                tel: a,
                code: e
            },
            success: function(e) {
                console.log("111111111"), console.log(e), "操作成功" == e.data.reason && wx.showToast({
                    title: "发送成功",
                    icon: "success",
                    duration: 1e3
                })
            },
            fail: function(e) {
                console.log("error res="), console.log(e.data)
            }
        })
    },
    dw: function() {
        var a = this;
        wx.chooseLocation({
            success: function(e) {
                console.log(e), a.setData({
                    weizhi: e.address,
                    jwd: e.latitude + "," + e.longitude
                })
            }
        })
    },
    bindTimeChange: function(e) {
        console.log(e.detail.value), this.setData({
            timestart: e.detail.value
        })
    },
    bindTimeChange1: function(e) {
        console.log(e.detail.value), this.setData({
            timeend: e.detail.value
        })
    },
    bindTypeChange: function(e) {
        console.log("picker type 发生选择改变，携带值为", e.detail.value, this.data.hy[e.detail.value].id), this.setData({
            hyIndex: e.detail.value,
            hyid: this.data.hy[e.detail.value].id
        })
    },
    checkboxChange: function(e) {
        console.log("checkbox发生change事件，携带value值为：", e.detail.value), this.setData({
            checkbox: e.detail.value
        }), console.log(util.in_array("WIFI", e.detail.value));
        for (var a = this.data.checkboxItems, t = e.detail.value, o = 0, i = a.length; o < i; ++o) {
            a[o].checked = !1;
            for (var l = 0, s = t.length; l < s; ++l) if (a[o].value == t[l]) {
                a[o].checked = !0;
                break
            }
        }
        this.setData({
            checkboxItems: a
        })
    },
    chooseLogo: function() {
        var t = this;
        wx.chooseImage({
            count: 1,
            sizeType: ["compressed"],
            sourceType: ["album", "camera"],
            success: function(e) {
                wx.showToast({
                    icon: "loading",
                    title: "正在上传"
                });
                var a = e.tempFilePaths;
                console.log(a), wx.uploadFile({
                    url: siteinfo.siteroot + "?i=" + siteinfo.uniacid + "&c=entry&a=wxapp&do=upload&m=zh_cjdianc",
                    filePath: e.tempFilePaths[0],
                    name: "upfile",
                    success: function(e) {
                        console.log(e), imglogo = e.data, "" != e.data ? t.setData({
                            logo: a[0]
                        }) : wx.showModal({
                            title: "提示",
                            content: "上传失败",
                            showCancel: !1
                        })
                    },
                    fail: function(e) {
                        console.log(e), wx.showModal({
                            title: "提示",
                            content: "上传失败",
                            showCancel: !1
                        })
                    },
                    complete: function() {
                        wx.hideToast()
                    }
                })
            }
        })
    },
    chooseImage1: function() {
        var o = this,
            i = this.data.images1;
        imgArray1 = [], wx.chooseImage({
            count: 9 - i.length - o.data.images.length,
            sizeType: ["compressed"],
            sourceType: ["album", "camera"],
            success: function(e) {
                var a = e.tempFilePaths;
                console.log(a);
                var t = e.tempFilePaths;
                i = i.concat(t), console.log(i), o.setData({
                    images1: i
                })
            }
        })
    },
    lbchooseImage1: function() {
        var o = this,
            i = this.data.lbimages1;
        lbimgArray1 = [], wx.chooseImage({
            count: 3 - i.length - o.data.lbimages.length,
            sizeType: ["compressed"],
            sourceType: ["album", "camera"],
            success: function(e) {
                wx.showToast({
                    icon: "loading",
                    title: "正在上传"
                });
                var a = e.tempFilePaths;
                console.log(a);
                var t = e.tempFilePaths;
                i = i.concat(t), console.log(i), o.lbuploadimg1({
                    url: getApp().imglink + "app/index.php?i=" + getApp().getuniacid + "&c=entry&a=wxapp&do=upload&m=zh_zbkq",
                    path: i
                })
            }
        })
    },
    previewImage: function() {
        wx.previewImage({
            urls: this.data.images
        })
    },
    uploadimg1: function(e) {
        var a = this,
            t = e.i ? e.i : 0,
            o = e.success ? e.success : 0,
            i = e.fail ? e.fail : 0;
        wx.uploadFile({
            url: e.url,
            filePath: e.path[t],
            name: "upfile",
            formData: null,
            success: function(e) {
                "" != e.data ? (console.log(e), o++, imgArray1.push(e.data), console.log(t), console.log("编辑信息时候提交的图片数组", imgArray1)) : wx.showToast({
                    icon: "loading",
                    title: "请重试"
                })
            },
            fail: function(e) {
                i++, console.log("fail:" + t + "fail:" + i)
            },
            complete: function() {
                console.log(t), ++t == e.path.length ? (a.setData({
                    images1: e.path
                }), wx.hideToast(), console.log("执行完毕"), console.log("成功：" + o + " 失败：" + i)) : (console.log(t), e.i = t, e.success = o, e.fail = i, a.uploadimg1(e))
            }
        })
    },
    lbuploadimg1: function(e) {
        var a = this,
            t = e.i ? e.i : 0,
            o = e.success ? e.success : 0,
            i = e.fail ? e.fail : 0;
        wx.uploadFile({
            url: e.url,
            filePath: e.path[t],
            name: "upfile",
            formData: null,
            success: function(e) {
                "" != e.data ? (console.log(e), o++, lbimgArray1.push(e.data), console.log(t), console.log("编辑信息时候提交的轮播图片数组", lbimgArray1)) : wx.showToast({
                    icon: "loading",
                    title: "请重试"
                })
            },
            fail: function(e) {
                i++, console.log("fail:" + t + "fail:" + i)
            },
            complete: function() {
                console.log(t), ++t == e.path.length ? (a.setData({
                    lbimages1: e.path
                }), wx.hideToast(), console.log("执行完毕"), console.log("成功：" + o + " 失败：" + i)) : (console.log(t), e.i = t, e.success = o, e.fail = i, a.lbuploadimg1(e))
            }
        })
    },
    delete: function(e) {
        var a = e.currentTarget.dataset.index,
            t = this.data.images;
        t.splice(a, 1), imgArray.splice(a, 1), console.log("删除images里的图片后剩余的图片", t, imgArray), this.setData({
            images: t
        })
    },
    delete1: function(e) {
        var a = e.currentTarget.dataset.index,
            t = this.data.images1;
        t.splice(a, 1), console.log("删除images1里的图片后剩余的图片", t), this.setData({
            images1: t
        })
    },
    lbdelete: function(e) {
        var a = e.currentTarget.dataset.index,
            t = this.data.lbimages;
        t.splice(a, 1), lbimgArray.splice(a, 1), console.log("删除lbimages里的图片后剩余的图片", lbimgArray), this.setData({
            lbimages: t
        })
    },
    lbdelete1: function(e) {
        var a = e.currentTarget.dataset.index,
            t = this.data.lbimages1;
        t.splice(a, 1), lbimgArray1.splice(a, 1), console.log("删除lbimages1里的图片后剩余的图片", lbimgArray1), this.setData({
            lbimages1: t
        })
    },
    formSubmit: function(e) {
        var a = this,
            t = a.data.images1;
        console.log("imgArray", imgArray, "imgArray1", imgArray1, "images", a.data.images, "images1", a.data.images1, "lbimgArray", lbimgArray, "lbimgArray1", lbimgArray1, "lbimages", a.data.lbimages, "lbimages1", a.data.lbimages1);
        var o = wx.getStorageSync("sjdsjid");
        console.log(o), console.log("form发生了submit事件，携带数据为：", e.detail.value), console.log(imglogo, imgArray, imgArray1);
        var i = this.data.yzm;
        console.log("随机生成的验证码", i);
        var l = e.detail.value.sjmc,
            s = e.detail.value.sjdh,
            n = e.detail.value.mdwz,
            c = this.data.jwd,
            r = e.detail.value.rjj,
            g = e.detail.value.qsj,
            u = e.detail.value.xyh,
            d = e.detail.value.xyhje,
            m = e.detail.value.mdgg;
        console.log(l, s, n, c, r, g, u, d, m);
        var h = "",
            f = !0;
        if ("" == imglogo) h = "请上传商家Logo！";
        else if ("" == l) h = "请填写商家名称！";
        else if ("" == s) h = "请填写商家手机号！";
        else if (11 != s.length) h = "手机号错误！";
        else if ("" == n) h = "请填写门店位置";
        else if ("" == c) h = "请点击定位按钮进行定位";
        else if ("" == r) h = "请填写人均价";
        else if ("" == g) h = "请填写起送价";
        else if ("" == d) h = "请填写新用户优惠金额";
        else if ("" == m) h = "请填写门店公告";
        else {
            var p = function() {
                var e = imgArray.concat(imgArray1);
                console.log("请求接口", e, e.toString()), app.util.request({
                    url: "entry/wxapp/UpdStoreInfo",
                    cachetime: "0",
                    data: {
                        id: o,
                        logo: imglogo,
                        name: l,
                        tel: s,
                        address: n,
                        coordinates: c,
                        capita: r,
                        start_at: g,
                        announcement: m,
                        xyh_money: d,
                        xyh_open: u ? 1 : 2,
                        environment: e.toString()
                    },
                    success: function(e) {
                        "1" == e.data ? (wx.showModal({
                            title: "提示",
                            content: "提交成功"
                        }), setTimeout(function() {
                            wx.navigateBack({})
                        }, 1e3)) : "2" == e.data ? wx.showModal({
                            title: "提示",
                            content: "请修改后提交"
                        }) : wx.showToast({
                            title: "网络错误"
                        }), console.log("Assess", e.data)
                    }
                })
            };
            f = !1, wx.showLoading({
                title: "正在提交",
                mask: !0
            }), 0 == t.length ? p() : function e(a) {
                var t = a.i ? a.i : 0,
                    o = a.success ? a.success : 0,
                    i = a.fail ? a.fail : 0;
                wx.uploadFile({
                    url: a.url,
                    filePath: a.path[t],
                    name: "upfile",
                    formData: null,
                    success: function(e) {
                        "" != e.data ? (console.log(e), o++, imgArray1.push(e.data), console.log(t), console.log("图片数组", imgArray1)) : wx.showToast({
                            icon: "loading",
                            title: "请重试"
                        })
                    },
                    fail: function(e) {
                        i++, console.log("fail:" + t + "fail:" + i)
                    },
                    complete: function() {
                        console.log(t), ++t == a.path.length ? (wx.hideToast(), console.log("执行完毕"), p(), console.log("成功：" + o + " 失败：" + i)) : (console.log(t), a.i = t, a.success = o, a.fail = i, e(a))
                    }
                })
            }({
                url: siteinfo.siteroot + "?i=" + siteinfo.uniacid + "&c=entry&a=wxapp&do=upload&m=zh_cjdianc",
                path: t
            })
        }
        1 == f && wx.showModal({
            title: "提示",
            content: h
        })
    },
    cxkt: function() {
        this.setData({
            issq: !0
        })
    },
    gongg: function(e) {
        console.log(e.detail.value);
        var a = parseInt(e.detail.value.length);
        this.setData({
            zsnum: a
        })
    },
    getPhoneNumber: function(e) {
        var a = this;
        console.log(e), console.log(e.detail.iv), console.log(e.detail.encryptedData), "getPhoneNumber:fail user deny" == e.detail.errMsg ? wx.showModal({
            title: "提示",
            showCancel: !1,
            content: "您未授权获取您的手机号",
            success: function(e) {}
        }) : app.util.request({
            url: "entry/wxapp/Jiemi",
            cachetime: "0",
            data: {
                sessionKey: getApp().getSK,
                data: e.detail.encryptedData,
                iv: e.detail.iv
            },
            success: function(e) {
                console.log("解密后的数据", e), null != e.data.phoneNumber && a.setData({
                    sjh: e.data.phoneNumber,
                    isbd: !0,
                    bdsjhtext: "验证成功"
                })
            }
        })
    },
    onLoad: function(e) {
        imgArray = [], imgArray1 = [], lbimgArray = [], lbimgArray1 = [];
        var a = wx.getStorageSync("users").id,
            t = wx.getStorageSync("sjdsjid"),
            o = this;
        console.log(getApp().getuniacid, a, t), app.setNavigationBarColor(this), app.util.request({
            url: "entry/wxapp/Url",
            cachetime: "0",
            success: function(e) {
                console.log(e), o.setData({
                    url: e.data
                })
            }
        }), app.util.request({
            url: "entry/wxapp/StoreInfo",
            cachetime: "0",
            data: {
                store_id: t
            },
            success: function(e) {
                console.log(e);
                var a = e.data.store;
                imglogo = a.logo, imgArray = a.environment, lbimgArray = a.yyzz, o.setData({
                    logo: a.logo,
                    sjmc: a.name,
                    sjdh: a.tel,
                    weizhi: a.address,
                    jwd: a.coordinates,
                    rjj: a.capita,
                    qsj: a.start_at,
                    xyhje: e.data.storeset.xyh_money,
                    mdgg: a.announcement,
                    zsnum: parseInt(a.announcement.length),
                    images: a.environment,
                    lbimages: a.yyzz,
                    xyh_open: 1 == e.data.storeset.xyh_open
                }), console.log("imgArray", imgArray, "imgArray1", imgArray1, "images", o.data.images, "images1", o.data.images1, "lbimgArray", lbimgArray, "lbimgArray1", lbimgArray1, "lbimages", o.data.lbimages, "lbimages1", o.data.lbimages1)
            }
        })
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});