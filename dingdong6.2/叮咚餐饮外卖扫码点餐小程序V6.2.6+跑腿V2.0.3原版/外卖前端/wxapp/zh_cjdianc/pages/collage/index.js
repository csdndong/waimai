var app = getApp(), siteinfo = require("../../../siteinfo.js");

Page({
    data: {
        share: !0,
        friendsImage: !0,
        clock: !0
    },
    maketel: function(t) {
        var e = this.data.StoreInfo.store.tel;
        wx.makePhoneCall({
            phoneNumber: e
        });
    },
    location: function() {
        var t = this.data.StoreInfo.store.coordinates.split(","), e = this.data.StoreInfo.store;
        console.log(t), wx.openLocation({
            latitude: parseFloat(t[0]),
            longitude: parseFloat(t[1]),
            address: e.address,
            name: e.name
        });
    },
    back: function() {
        wx.navigateBack({});
    },
    onLoad: function(t) {
        var e = this;
        console.log(t), app.setNavigationBarColor(this), wx.setNavigationBarTitle({
            title: t.title
        }), e.setData({
            store_id: t.store_id,
            store_logo: t.store_logo
        });
        var o = decodeURIComponent(t.scene);
        null == t.scene ? e.setData({
            id: t.id
        }) : e.setData({
            id: o
        }), e.reload(), wx.getSystemInfo({
            success: function(t) {
                e.setData({
                    width: t.windowWidth,
                    height: t.windowHeight,
                    v_wid: t.windowWidth - 40
                });
            }
        });
    },
    refresh: function(t) {
        var d = this, c = d.data, e = siteinfo.siteroot.slice(0, siteinfo.siteroot.length - 14);
        console.log(e), app.util.request({
            url: "entry/wxapp/GoodsInfo",
            cachetime: "0",
            data: {
                goods_id: d.data.id
            },
            success: function(t) {
                for (var e in console.log("商品详情", t), app.util.request({
                    url: "entry/wxapp/StoreInfo",
                    cachetime: "0",
                    data: {
                        store_id: t.data.goods.store_id
                    },
                    success: function(t) {
                        console.log("商家详情", t), d.setData({
                            StoreInfo: t.data
                        });
                    }
                }), t.data.group) t.data.group[e].num = Number(t.data.group[e].kt_num) - Number(t.data.group[e].yg_num);
                console.log(t.data.group);
                var o = t.data.goods;
                d.countdown(o.end_time), o.img = o.img.split(","), o.end_times = o.end_time, o.xf_times = o.xf_time, 
                o.start_time = d.ormatDate(o.start_time), o.end_time = d.ormatDate(o.end_time), 
                o.xf_time = d.ormatDate(o.xf_time);
                for (var a = c.width - 110, i = o.name, n = [], s = 0, l = i.length; s < l; s += a / 14) n.push(i.slice(s, s + a / 14));
                for (var r in console.log(o.store_logo), wx.downloadFile({
                    url: o.store_logo,
                    success: function(t) {
                        console.log(t.tempFilePath), d.setData({
                            logo: t.tempFilePath
                        }), d.img0();
                    }
                }), 0 != n.length && (console.log("正在下载图片"), wx.downloadFile({
                    url: d.data.url + o.img[0],
                    success: function(t) {
                        console.log(t.tempFilePath), d.setData({
                            logo1: t.tempFilePath,
                            title: i,
                            row: n
                        });
                    }
                })), t.data.group) null != t.data.group[r].name && 4 <= t.data.group[r].name.length && (t.data.group[r].name = t.data.group[r].name.slice(0, 4) + "..."), 
                t.data.group[r].user_id == wx.getStorageSync("users").id && (console.log("已经有开的团了"), 
                d.setData({
                    already_group: !0,
                    already: t.data.group[r]
                }));
                d.setData({
                    goods_info: o,
                    group: t.data.group
                });
            }
        });
    },
    img0: function(t) {
        var o = this, a = siteinfo.siteroot.slice(0, siteinfo.siteroot.length - 14);
        app.util.request({
            url: "entry/wxapp/GoodsCode",
            cachetime: "0",
            data: {
                goods_id: o.data.id
            },
            success: function(t) {
                var e = t.data;
                o.setData({
                    goods_code: e
                }), console.log("下载的网址链接", a + e), wx.downloadFile({
                    url: a + e + "",
                    success: function(t) {
                        console.log(t.tempFilePath), o.setData({
                            shop_logo: t.tempFilePath
                        }), o.ctx();
                    }
                });
            }
        });
    },
    reload: function(t) {
        var e = this;
        wx.showLoading({
            title: "加载中",
            mask: !0
        });
        e.data.id;
        app.util.request({
            url: "entry/wxapp/GroupType",
            cachetime: "0",
            success: function(t) {
                console.log("分类列表", t), e.setData({
                    nav_array: t.data
                });
            }
        }), app.util.request({
            url: "entry/wxapp/Url",
            cachetime: "0",
            success: function(t) {
                wx.setStorageSync("imglink", t.data), e.setData({
                    url: t.data
                });
            }
        });
    },
    ctx: function(t) {
        var e = this, o = e.data, a = (o.width, o.height, wx.createCanvasContext("ctx"));
        a.drawImage(o.shop_logo, 0, 0, 150, 150), a.save(), a.beginPath(), a.arc(75, 75, 35, 0, 2 * Math.PI), 
        a.clip(), a.drawImage(o.logo, 35, 35, 75, 75), a.restore(), a.draw(), setTimeout(function(t) {
            wx.canvasToTempFilePath({
                x: 0,
                y: 0,
                width: 150,
                height: 150,
                canvasId: "ctx",
                success: function(t) {
                    console.log(t.tempFilePath), wx.hideLoading(), e.setData({
                        logos: t.tempFilePath
                    });
                }
            });
        }, 500);
    },
    canvas: function(t) {
        wx.showLoading({
            title: "正在生成海报"
        });
        var e = this, o = e.data, a = o.width, i = o.height, n = a - 20, s = a;
        console.log(n), console.log(s);
        var l = o.row;
        console.log("这是生成的标题数组"), console.log(l);
        wx.getStorageSync("users").name;
        console.log(o);
        var r = wx.createCanvasContext("firstCanvas");
        r.rect(0, 0, a, i), r.setFillStyle("#fff"), r.fill(), r.fillStyle = "red", r.setFontSize(16), 
        r.fillText(wx.getStorageSync("users").name, 10, 30), r.fillStyle = "#222", r.setFontSize(16), 
        r.fillText("邀请你一起来拼团", 78, 30), r.drawImage(o.logo1, 10, 50, n, s), r.drawImage(o.logos, a - 100, n + 100, 80, 80), 
        r.fillStyle = "#333", r.setFontSize(12);
        for (var d = 0; d < l.length; d++) console.log(l[d]), d <= 1 && r.fillText(l[d], 10, s + 80 + 20 * d);
        r.fillStyle = "red", r.setFontSize(16), r.fillText("￥", 10, s + 140), r.fillStyle = "red", 
        r.setFontSize(22), r.fillText(e.data.goods_info.pt_price, 30, s + 140), r.fillStyle = "#ccc", 
        r.setFontSize(14), r.fillText("长按识别小程序码访问", 10, s + 160), r.draw(), setTimeout(function() {
            e.genImage(), e.close();
        }, 1e3);
    },
    genImage: function(t) {
        var e = this, o = this.data.width, a = this.data.canvas_height;
        wx.canvasToTempFilePath({
            x: 0,
            y: 0,
            width: o,
            height: a,
            canvasId: "firstCanvas",
            success: function(t) {
                console.log(t.tempFilePath), wx.hideLoading(), e.setData({
                    genImage: t.tempFilePath,
                    friendsImage: !1
                });
            }
        });
    },
    close: function(t) {
        this.setData({
            friendsImage: !0,
            share: !0
        });
    },
    toTemp: function(t) {
        var e = this;
        wx.saveImageToPhotosAlbum({
            filePath: e.data.genImage,
            success: function(t) {
                console.log(t), wx.showToast({
                    title: "保存成功"
                }), e.setData({
                    friendsImage: !0,
                    share: !0
                });
            },
            fail: function(t) {},
            complete: function(t) {}
        });
    },
    share: function(t) {
        var e = this.data.share;
        e = 1 != e, this.setData({
            share: e
        });
    },
    collageInfo: function(t) {
        var e = this.data;
        console.log(e.goods_info), console.log(t);
        var o = t.currentTarget.dataset, a = e.goods_info;
        wx.navigateTo({
            url: "collageInfo?id=" + o.id + "&user_id=" + o.userid + "&goods_id=" + a.id
        });
    },
    alone_pay: function(t) {
        var e = this.data;
        wx.navigateTo({
            url: "qgform?store_id=" + e.goods_info.store_id + "&goods_id=" + e.goods_info.id + "&type=1&group_id=&end_time=" + e.goods_info.end_times + "&xf_time=" + e.goods_info.xf_times
        });
    },
    group_pay: function(t) {
        var e = this.data;
        1 == e.already_group ? wx.showModal({
            title: "温馨提示",
            content: "您已经开过团了，是否跳转至我发起的拼团",
            success: function(t) {
                t.confirm && wx.navigateTo({
                    url: "collageInfo?id=" + e.already.id + "&user_id=" + wx.getStorageSync("users").id + "&goods_id=" + e.goods_info.id
                });
            }
        }) : wx.navigateTo({
            url: "qgform?store_id=" + e.goods_info.store_id + "&goods_id=" + e.goods_info.id + "&type=2&kt_num=" + e.goods_info.people + "&group_id=&end_time=" + e.goods_info.end_times + "&xf_time=" + e.goods_info.xf_times
        });
    },
    onReady: function() {},
    onShow: function() {
        var e = this;
        app.getUserInfo(function(t) {
            "" == t.img || "" == t.name ? wx.navigateTo({
                url: "../smdc/getdl"
            }) : (e.setData({
                userInfo: t
            }), e.refresh());
        });
    },
    onHide: function() {},
    onUnload: function() {
        this.setData({
            clock: !1
        });
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    countdown: function(t) {
        var e = this, o = (t || []) - Math.round(new Date().getTime() / 1e3) || [];
        o <= 0 ? (app.util.request({
            url: "entry/wxapp/UpdateGroup",
            data: {
                store_id: e.data.store_id
            },
            success: function(t) {
                console.log(t);
            }
        }), e.setData({
            clock: !1
        })) : 0 < o && 0 != e.data.clock && (e.dateformat(o), setTimeout(function() {
            o -= 1e3, e.countdown(t);
        }, 1e3));
    },
    dateformat: function(t) {
        var e = Math.floor(t), o = Math.floor(e / 3600 / 24), a = Math.floor(e / 3600 % 24), i = Math.floor(e / 60 % 60), n = Math.floor(e % 60);
        o < 10 && (o = "0" + o), a < 10 && (a = "0" + a), n < 10 && (n = "0" + n), i < 10 && (i = "0" + i), 
        this.setData({
            day: o,
            hour: a,
            min: i,
            sec: n
        });
    },
    ormatDate: function(t) {
        var e = new Date(1e3 * t);
        return e.getFullYear() + "-" + o(e.getMonth() + 1, 2) + "-" + o(e.getDate(), 2) + " " + o(e.getHours(), 2) + ":" + o(e.getMinutes(), 2) + ":" + o(e.getSeconds(), 2);
        function o(t, e) {
            for (var o = "" + t, a = o.length, i = "", n = e; n-- > a; ) i += "0";
            return i + o;
        }
    },
    onShareAppMessage: function(t) {
        return this.setData({
            share: !0
        }), {
            title: wx.getStorageSync("users").name + "邀请您一起来拼团",
            path: "/zh_cjdianc/pages/collage/index?store_id=" + this.data.store_id + "&id=" + this.data.id,
            success: function(t) {
                console.log(t);
            },
            complete: function(t) {
                console.log("执行");
            }
        };
    }
});