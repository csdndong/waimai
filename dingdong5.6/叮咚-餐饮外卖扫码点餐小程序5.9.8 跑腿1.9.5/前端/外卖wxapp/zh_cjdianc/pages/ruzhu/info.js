var app = getApp(), siteinfo = require("../../../siteinfo.js");

Page({
    data: {
        imgUrls: [ "http://img02.tooopen.com/images/20150928/tooopen_sy_143912755726.jpg", "http://img06.tooopen.com/images/20160818/tooopen_sy_175866434296.jpg", "http://img06.tooopen.com/images/20160818/tooopen_sy_175833047715.jpg" ],
        indicatorDots: !1,
        autoplay: !1,
        interval: 5e3,
        duration: 1e3,
        index: 0,
        getmsg: "获取验证码",
        ac_index: 0,
        succ: !0
    },
    onLoad: function(a) {
        app.setNavigationBarColor(this);
        var s = this, e = wx.getStorageSync("users").id;
        s.setData({
            state: a.state
        }), console.log(a), app.util.request({
            url: "entry/wxapp/CheckRz",
            cachetime: "0",
            data: {
                user_id: e
            },
            success: function(a) {
                if (console.log(a.data), 0 == a.data) {
                    var e = wx.getStorageSync("imglink");
                    s.setData({
                        id: "",
                        url: e
                    }), s.rz_time();
                } else s.setData({
                    name: a.data.name,
                    details: app.convertHtmlToText(a.data.details),
                    link_name: a.data.link_name,
                    link_tel: a.data.link_tel,
                    address: a.data.address,
                    latitude: a.data.coordinates,
                    phone: a.data.link_tel,
                    upload_one: a.data.logo,
                    upload_two: a.data.zm_img,
                    upload_three: a.data.fm_img,
                    upload_four: a.data.yyzz,
                    bdupload_one: a.data.logo,
                    bdupload_two: a.data.zm_img,
                    bdupload_three: a.data.fm_img,
                    bdupload_four: a.data.yyzz,
                    id: a.data.id,
                    day: a.data.rz_time,
                    url: ""
                }), s.rz_time();
            }
        }), s.setData({
            form_id: a.form_id
        }), app.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(a) {
                s.setData({
                    system: a.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/CheckSms",
            cachetime: "0",
            success: function(a) {
                s.setData({
                    CheckSms: a.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/ad",
            cachetime: "0",
            success: function(a) {
                console.log(a);
                var e = a.data, t = [];
                for (var o in e) "5" == e[o].type && t.push(e[o]);
                s.setData({
                    ad: t
                });
            }
        });
    },
    rz_time: function(a) {
        var o = this.data, s = this;
        app.util.request({
            url: "entry/wxapp/GetRzqx",
            cachetime: "0",
            success: function(a) {
                console.log(a);
                var e = a.data;
                if (0 < e.length) {
                    if (s.setData({
                        array: !0,
                        arr: e
                    }), null != o.day) for (var t in e) e[t].days == o.day && s.setData({
                        ac_index: t
                    });
                } else s.setData({
                    array: !1
                });
            }
        });
    },
    selse_succ: function(a) {
        1 == this.data.succ ? this.setData({
            succ: !1
        }) : this.setData({
            succ: !0
        });
    },
    code: function(a) {
        console.log(a), this.setData({
            phone: a.detail.value
        });
    },
    sendmessg: function(a) {
        var e = this;
        console.log(e.data);
        var t = e.data.phone;
        if ("" == t || null == t) wx.showModal({
            title: "",
            content: "请输入手机号"
        }); else {
            for (var o = "", s = 0; s < 6; s++) o += Math.floor(10 * Math.random());
            console.log(o), app.util.request({
                url: "entry/wxapp/sms2",
                cachetime: "0",
                data: {
                    code: o,
                    tel: t
                },
                success: function(a) {
                    console.log(a);
                }
            }), e.setData({
                num: o
            });
            var n = 59, i = setInterval(function() {
                e.setData({
                    getmsg: n + "s后重新发送",
                    send: !0
                }), --n <= 0 && (clearInterval(i), e.setData({
                    getmsg: "获取验证码",
                    send: !1
                }));
            }, 1e3);
        }
    },
    bindPickerChange: function(a) {
        console.log("picker发送选择改变，携带值为", a.detail.value), this.setData({
            index: a.detail.value
        });
    },
    choose: function(a) {
        var t = this, o = a.currentTarget.dataset.type;
        wx.getStorageSync("imglink");
        wx.chooseImage({
            count: 1,
            sizeType: [ "original", "compressed" ],
            sourceType: [ "album", "camera" ],
            success: function(a) {
                console.log(a);
                var e = a.tempFilePaths[0];
                wx.uploadFile({
                    url: siteinfo.siteroot + "?i=" + siteinfo.uniacid + "&c=entry&a=wxapp&do=upload&m=zh_cjdianc",
                    filePath: e,
                    name: "upfile",
                    formData: {},
                    success: function(a) {
                        console.log("这是上传成功"), console.log(a), 1 == o ? t.setData({
                            bdupload_one: e,
                            upload_one: a.data
                        }) : 2 == o ? t.setData({
                            bdupload_two: e,
                            upload_two: a.data
                        }) : 3 == o ? t.setData({
                            bdupload_three: e,
                            upload_three: a.data
                        }) : 4 == o && t.setData({
                            bdupload_four: e,
                            upload_four: a.data
                        });
                    },
                    fail: function(a) {
                        console.log("这是上传失败"), console.log(a);
                    }
                });
            }
        });
    },
    sele_arr: function(a) {
        this.data.arr;
        var e = a.currentTarget.dataset.index;
        3 == this.data.state || 4 == this.data.state ? wx.showModal({
            title: "",
            content: "入驻期限不可以修改"
        }) : this.setData({
            ac_index: e
        });
    },
    choose_address: function(a) {
        var t = this;
        wx.chooseLocation({
            success: function(a) {
                console.log(a);
                var e = a.latitude + "," + a.longitude;
                console.log(e), t.setData({
                    address: a.address,
                    latitude: e
                });
            }
        });
    },
    xieyi: function(a) {
        wx.navigateTo({
            url: "xieyi"
        });
    },
    formSubmit: function(a) {
        var t = this.data, e = a.detail.value, o = e.name_title, s = e.name_prompt, n = e.name_wor, i = e.name_tel, l = e.code, c = t.num, d = t.arr, u = t.succ, r = t.ac_index, p = t.array, m = (t.system, 
        t.id);
        if ("" == m) {
            if (console.log("这是新建"), 1 == p) var g = d[r].days, f = d[r].money;
        } else {
            console.log("这是修改");
            g = t.day;
        }
        var _ = t.address, h = t.upload_one, y = t.upload_two, w = t.upload_three, x = t.upload_four, v = t.latitude, D = t.form_id, z = t.CheckSms, S = a.detail.formId;
        console.log(D), console.log(S);
        var k = wx.getStorageSync("users").id;
        app.util.request({
            url: "entry/wxapp/AddFormId",
            cachetime: "0",
            data: {
                user_id: k,
                form_id: a.detail.formId
            },
            success: function(a) {
                console.log(a.data);
            }
        });
        var T = wx.getStorageSync("users").openid, q = "";
        "" == o ? q = "请输入商户名称" : null == _ ? q = "请选择商户地址" : "" == s ? q = "请输入商户简介" : "" == n ? q = "请输入联系人姓名" : "" == i ? q = "请输入联系人电话" : null == h ? q = "请上传商户logo" : null == y ? q = "请上传身份证正面照片" : null == w ? q = "请上传身份证反面照片" : null == x ? q = "请上传营业执照" : c != l && 1 == z ? q = "验证码输入错误" : 0 == u ? q = "请先阅读并同意入驻申请协议" : 0 == p && (q = "请选择入驻期限"), 
        1 == app.title(q) && app.util.request({
            url: "entry/wxapp/SaveRzsq",
            cachetime: "0",
            data: {
                id: m,
                name: o,
                user_id: k,
                address: _,
                details: s,
                rz_time: g,
                yyzz: x,
                fm_img: w,
                zm_img: y,
                logo: h,
                link_name: n,
                link_tel: i,
                coordinates: v,
                money: f
            },
            success: function(a) {
                console.log(a);
                var e = a.data;
                "" == m ? 0 < Number(f) ? app.util.request({
                    url: "entry/wxapp/RzPay",
                    cachetime: "0",
                    data: {
                        openid: T,
                        money: f,
                        rz_id: e
                    },
                    success: function(a) {
                        wx.requestPayment({
                            timeStamp: a.data.timeStamp,
                            nonceStr: a.data.nonceStr,
                            package: a.data.package,
                            signType: a.data.signType,
                            paySign: a.data.paySign,
                            success: function(a) {
                                console.log("支付成功"), console.log(a), wx.showToast({
                                    title: "申请已提交"
                                }), app.util.request({
                                    url: "entry/wxapp/RzMessage",
                                    cachetime: "0",
                                    data: {
                                        form_id: D,
                                        openid: T,
                                        sh_id: e
                                    },
                                    success: function(a) {
                                        console.log("发送模板消息"), console.log(a);
                                    }
                                }), setTimeout(function() {
                                    wx.navigateBack({
                                        delta: 2
                                    });
                                }, 1500);
                            },
                            fail: function(a) {
                                console.log("支付失败"), wx.showToast({
                                    title: "支付失败"
                                }), setTimeout(function() {
                                    wx.navigateBack({
                                        delta: 2
                                    });
                                }, 1500);
                            }
                        });
                    }
                }) : (app.util.request({
                    url: "entry/wxapp/RzMessage",
                    cachetime: "0",
                    data: {
                        form_id: D,
                        openid: T,
                        sh_id: e
                    },
                    success: function(a) {
                        console.log("发送模板消息"), console.log(a);
                    }
                }), wx.showToast({
                    title: "申请已提交"
                }), setTimeout(function() {
                    wx.navigateBack({
                        delta: 2
                    });
                }, 1500)) : (wx.showToast({
                    title: "申请已提交"
                }), setTimeout(function() {
                    wx.navigateBack({
                        delta: 2
                    });
                }, 1500), app.util.request({
                    url: "entry/wxapp/RzMessage",
                    cachetime: "0",
                    data: {
                        form_id: D,
                        openid: T,
                        sh_id: t.id
                    },
                    success: function(a) {
                        console.log("发送模板消息"), console.log(a);
                    }
                }));
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