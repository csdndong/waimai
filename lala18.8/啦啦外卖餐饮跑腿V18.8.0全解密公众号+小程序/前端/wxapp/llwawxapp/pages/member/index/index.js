var e = getApp(), a = e.requirejs("core"), t = e.requirejs("wxParse/wxParse"), i = e.requirejs("biz/diypage"), o = e.requirejs("jquery");

Page({
    data: {
        route: "member",
        icons: e.requirejs("icons"),
        member: {},
        diypages: {},
        audios: {},
        audiosObj: {},
        modelShow: !1,
        autoplay: !0,
        interval: 5e3,
        duration: 500,
        swiperheight: 0,
        iscycelbuy: !1,
        bargain: !1
    },
    onLoad: function(a) {
        e.checkAuth(), this.setData({
            options: a
        });
    },
    getInfo: function() {
        var e = this;
        a.get("member", {}, function(a) {
            1 == a.isblack && wx.showModal({
                title: "无法访问",
                content: "您在商城的黑名单中，无权访问！",
                success: function(a) {
                    a.confirm && e.close(), a.cancel && e.close();
                }
            }), 0 != a.error ? wx.redirectTo({
                url: "/pages/message/auth/index"
            }) : e.setData({
                member: a,
                show: !0,
                customer: a.customer,
                customercolor: a.customercolor,
                phone: a.phone,
                phonecolor: a.phonecolor,
                phonenumber: a.phonenumber,
                iscycelbuy: a.iscycelbuy,
                bargain: a.bargain
            }), t.wxParse("wxParseData", "html", a.copyright, e, "5");
        });
    },
    onShow: function() {
        e.checkAuth();
        var a = this;
        this.getInfo(), wx.getSystemInfo({
            success: function(e) {
                var t = e.windowWidth / 1.7;
                a.setData({
                    windowWidth: e.windowWidth,
                    windowHeight: e.windowHeight,
                    swiperheight: t
                });
            }
        }), a.setData({
            imgUrl: e.globalData.approot
        }), i.get(this, "member", function(e) {});
    },
    onShareAppMessage: function() {
        return a.onShareAppMessage();
    },
    cancelclick: function() {
        wx.switchTab({
            url: "/pages/index/index"
        });
    },
    confirmclick: function() {
        wx.openSetting({
            success: function(e) {}
        });
    },
    phone: function() {
        var e = this.data.phonenumber + "";
        wx.makePhoneCall({
            phoneNumber: e
        });
    },
    play: function(e) {
        var a = e.target.dataset.id, t = this.data.audiosObj[a] || !1;
        if (!t) {
            t = wx.createInnerAudioContext("audio_" + a);
            var i = this.data.audiosObj;
            i[a] = t, this.setData({
                audiosObj: i
            });
        }
        var o = this;
        t.onPlay(function() {
            var e = setInterval(function() {
                var i = t.currentTime / t.duration * 100 + "%", r = Math.floor(Math.ceil(t.currentTime) / 60), n = (Math.ceil(t.currentTime) % 60 / 100).toFixed(2).slice(-2), s = Math.ceil(t.currentTime);
                r < 10 && (r = "0" + r);
                var u = r + ":" + n, c = o.data.audios;
                c[a].audiowidth = i, c[a].Time = e, c[a].audiotime = u, c[a].seconds = s, o.setData({
                    audios: c
                });
            }, 1e3);
        });
        var r = e.currentTarget.dataset.audio, n = e.currentTarget.dataset.time, s = e.currentTarget.dataset.pausestop, u = e.currentTarget.dataset.loopplay;
        0 == u && t.onEnded(function(e) {
            c[a].status = !1, o.setData({
                audios: c
            });
        });
        var c = o.data.audios;
        c[a] || (c[a] = {}), t.paused && 0 == n ? (t.src = r, t.play(), 1 == u && (t.loop = !0), 
        c[a].status = !0, o.pauseOther(a)) : t.paused && n > 0 ? (t.play(), 0 == s ? t.seek(n) : t.seek(0), 
        c[a].status = !0, o.pauseOther(a)) : (t.pause(), c[a].status = !1), o.setData({
            audios: c
        });
    },
    pauseOther: function(e) {
        var a = this;
        o.each(this.data.audiosObj, function(t, i) {
            if (t != e) {
                i.pause();
                var o = a.data.audios;
                o[t] && (o[t].status = !1, a.setData({
                    audios: o
                }));
            }
        });
    },
    onHide: function() {
        this.pauseOther();
    },
    onUnload: function() {
        this.pauseOther();
    },
    navigate: function(e) {
        var a = e.currentTarget.dataset.url, t = e.currentTarget.dataset.phone, i = e.currentTarget.dataset.appid, o = e.currentTarget.dataset.appurl;
        a && wx.navigateTo({
            url: a,
            fail: function() {
                wx.switchTab({
                    url: a
                });
            }
        }), t && wx.makePhoneCall({
            phoneNumber: t
        }), i && wx.navigateToMiniProgram({
            appId: i,
            path: o
        });
    },
    close: function() {
        e.globalDataClose.flag = !0, wx.reLaunch({
            url: "/pages/index/index"
        });
    }
});