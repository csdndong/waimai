var t = getApp();

t.requirejs("core");

Page({
    data: {
        userInfo: {},
        replace1: "商品",
        replace2: "2",
        hasUserInfo: !1,
        canIUse: wx.canIUse("button.open-type.getUserInfo"),
        now: !0,
        angle: 45,
        halfAngle: 27.5,
        radian: 0,
        rotateAngle: 0,
        offOn: !0,
        index: 0,
        circle: 1800,
        huojiangbj: "0",
        time: "4",
        sudokuIndex: -1,
        count: 8,
        timer: 0,
        speed: 20,
        times: 0,
        cycle: 50,
        prizeIndex: -1,
        click: !1,
        sudokuShow: !1,
        prize_show: !1,
        prize_details: !1,
        prize_details2: !1,
        award_rule: !1,
        winner: [],
        cdkey: !1,
        my_prize: [],
        smscodetext: "发送验证码"
    },
    bindViewTap: function() {
        wx.navigateTo({
            url: "../logs/logs"
        });
    },
    onLoad: function() {
        var e = this, a = this;
        console.log(2), wx.request({
            url: "https://u.we7shop.com/api/activity/activity-text?activity_id=1",
            data: {},
            header: {
                "content-type": "application/x-www-form-urlencoded"
            },
            success: function(t) {
                console.log(t.data.status, t.data.result.cjiang), 1 == t.data.status && (a.setData({
                    replace1: t.data.result.cjiang,
                    replace2: "奖"
                }), wx.setNavigationBarTitle({
                    title: "人人商城三周年" + t.data.result.cjiang
                }));
            }
        }), 5 == new Date().getMonth() && (console.log(new Date().getMonth(), new Date().getDate(), new Date().getHours()), 
        5 <= new Date().getDate() && new Date().getDate() <= 7 && new Date().getHours() >= 10 ? this.setData({
            now: !0,
            activity_id: 2
        }) : (11 == new Date().getDate() || 13 == new Date().getDate() || 15 == new Date().getDate()) && new Date().getHours() >= 10 ? this.setData({
            now: !1,
            activity_id: 1
        }) : new Date().getDate() > 7 ? this.setData({
            now: !1,
            nobegun: !0,
            activity_id: 1
        }) : this.setData({
            now: !0,
            nobegun: !0,
            activity_id: 2
        }));
        a = this;
        t.globalData.userInfo ? this.setData({
            userInfo: t.globalData.userInfo,
            hasUserInfo: !0
        }) : this.data.canIUse ? t.userInfoReadyCallback = function(t) {
            e.setData({
                userInfo: t.userInfo,
                hasUserInfo: !0
            });
        } : wx.getUserInfo({
            success: function(a) {
                t.globalData.userInfo = a.userInfo, e.setData({
                    userInfo: a.userInfo,
                    hasUserInfo: !0
                });
            }
        }), this.getLotteryRecord(), this.getLotteryTickets(), a.getMyRecord();
    },
    getUserInfo: function(e) {
        t.globalData.userInfo = e.detail.userInfo, this.setData({
            userInfo: e.detail.userInfo,
            hasUserInfo: !0
        });
    },
    getLotteryRecord: function(t) {
        var e = this;
        wx.request({
            url: "https://u.we7shop.com/api/activity/lottery-record",
            data: {
                activity_id: e.data.activity_id
            },
            header: {
                "content-type": "application/x-www-form-urlencoded"
            },
            success: function(t) {
                1 == t.data.status ? e.setData({
                    winner: t.data.result
                }) : wx.showToast({
                    icon: "none",
                    title: t.data.result.message,
                    duration: 2e3
                });
            }
        });
    },
    getMyRecord: function(t) {
        var e = this;
        wx.getStorage({
            key: "session_id",
            success: function(t) {
                wx.request({
                    url: "https://u.we7shop.com/api/activity/my-lottery-record?activity_id=" + e.data.activity_id,
                    data: {
                        session_id: t.data
                    },
                    header: {
                        "content-type": "application/x-www-form-urlencoded"
                    },
                    method: "POST",
                    success: function(t) {
                        1 == t.data.status ? e.setData({
                            my_prize: t.data.result
                        }) : -10 == t.data.status ? e.setData({
                            login: !0,
                            mask: !0
                        }) : wx.showToast({
                            icon: "none",
                            title: t.data.result.message,
                            duration: 2e3
                        });
                    }
                });
            },
            fail: function(t) {
                e.setData({
                    login: !0,
                    mask: !0
                });
            }
        });
    },
    getLotteryTickets: function(t) {
        var e = this;
        wx.getStorage({
            key: "session_id",
            success: function(t) {
                wx.request({
                    url: "https://u.we7shop.com/api/activity/get-lottery-tickets?activity_id=" + e.data.activity_id,
                    data: {
                        session_id: t.data
                    },
                    header: {
                        "content-type": "application/x-www-form-urlencoded"
                    },
                    method: "POST",
                    success: function(t) {
                        1 == t.data.status ? e.setData({
                            tickets: t.data.result.tickets
                        }) : -10 == t.data.status ? e.setData({
                            login: !0,
                            mask: !0
                        }) : wx.showToast({
                            icon: "none",
                            title: t.data.result.message,
                            duration: 2e3
                        });
                    }
                });
            },
            fail: function(t) {
                e.setData({
                    login: !0,
                    mask: !0
                });
            }
        });
    },
    lottery: function(t) {
        var e = this;
        wx.getStorage({
            key: "session_id",
            success: function(a) {
                wx.request({
                    url: "https://u.we7shop.com/api/activity/lottery?activity_id=" + e.data.activity_id,
                    data: {
                        session_id: a.data
                    },
                    header: {
                        "content-type": "application/x-www-form-urlencoded"
                    },
                    method: "POST",
                    success: function(a) {
                        if (1 == a.data.status) {
                            if (e.setData({
                                lottery: a.data.result,
                                tickets: e.data.tickets - 1,
                                success: !0
                            }), e.getMyRecord(), "sudoku" == t) return !e.data.click && (e.setData({
                                speed: 100
                            }), e.roll(), e.setData({
                                click: !0
                            }), !1);
                            var s = e.data.offOn;
                            s && (e.setData({
                                time: "0",
                                rotateAngle: 0
                            }), s = !s, e.ratating());
                        } else -10 == a.data.status ? e.setData({
                            login: !0,
                            mask: !0,
                            success: !1
                        }) : (wx.showToast({
                            icon: "none",
                            title: a.data.result.message,
                            duration: 2e3
                        }), e.setData({
                            success: !1
                        }));
                    }
                });
            },
            fail: function(t) {
                e.setData({
                    login: !0,
                    mask: !0,
                    success: !1
                });
            }
        });
    },
    logintel: function(t) {
        this.setData({
            logintel: t.detail.value
        });
    },
    loginpass: function(t) {
        this.setData({
            loginpass: t.detail.value
        });
    },
    registertel: function(t) {
        this.setData({
            registertel: t.detail.value
        });
    },
    registerpass: function(t) {
        this.setData({
            registerpass: t.detail.value
        });
    },
    confirmPassword: function(t) {
        this.setData({
            confirmPassword: t.detail.value
        });
    },
    registersmscode: function(t) {
        this.setData({
            registersmscode: t.detail.value
        });
    },
    closelogin: function(t) {
        this.setData({
            register: !1,
            login: !1,
            mask: !1
        });
    },
    goregister: function(t) {
        this.setData({
            register: !0,
            login: !1
        });
    },
    gologin: function(t) {
        this.setData({
            login: !0,
            register: !1
        });
    },
    login: function(t) {
        var e = this, a = {};
        a.mobile = this.data.logintel, a.password = this.data.loginpass, wx.login({
            success: function(t) {
                a.code = t.code, wx.request({
                    url: "https://u.we7shop.com/api/user/login",
                    header: {
                        "content-type": "application/x-www-form-urlencoded"
                    },
                    data: a,
                    method: "POST",
                    success: function(t) {
                        1 == t.data.status ? (e.setData({
                            isnew: t.data.result.isnew,
                            uid: t.data.result.uid,
                            session_id: t.data.result.session_id,
                            login: !1,
                            mask: !1
                        }), wx.showToast({
                            title: "登录成功 ",
                            duration: 2e3
                        }), wx.setStorage({
                            key: "session_id",
                            data: t.data.result.session_id
                        }), e.getLotteryTickets(), e.getMyRecord()) : wx.showToast({
                            icon: "none",
                            title: t.data.result.message,
                            duration: 2e3
                        });
                    }
                });
            }
        });
    },
    sendsmscode: function() {
        var t = 60, e = this;
        if ("发送验证码" == e.data.smscodetext) {
            var a = {};
            a.mobile = this.data.registertel, a.type = "register", wx.request({
                url: "https://u.we7shop.com/api/user/getsmscode",
                data: a,
                method: "POST",
                header: {
                    "content-type": "application/x-www-form-urlencoded"
                },
                success: function(a) {
                    if (1 == a.data.status) {
                        wx.showToast({
                            title: "发送成功",
                            duration: 2e3
                        });
                        var s = setInterval(function() {
                            if (e.setData({
                                smscodetext: t + "s"
                            }), 0 == t) return clearInterval(s), void e.setData({
                                smscodetext: "发送验证码"
                            });
                            t--;
                        }, 1e3);
                    } else wx.showToast({
                        icon: "none",
                        title: a.data.result.message,
                        duration: 2e3
                    });
                }
            });
        }
    },
    register: function(t) {
        var e = {};
        e.mobile = this.data.registertel, e.password = this.data.registerpass, e.confirm_password = this.data.confirmPassword, 
        e.smscode = this.data.registersmscode, wx.login({
            success: function(t) {
                e.code = t.code, wx.request({
                    url: "https://u.we7shop.com/api/user/register",
                    data: e,
                    method: "POST",
                    header: {
                        "content-type": "application/x-www-form-urlencoded"
                    },
                    success: function(t) {
                        1 == t.data.status ? (wx.showToast({
                            title: "注册成功",
                            duration: 2e3
                        }), $this.setData({
                            login: !0
                        })) : wx.showToast({
                            icon: "none",
                            title: t.data.result.message,
                            duration: 2e3
                        });
                    }
                });
            }
        });
    },
    dial: function(t) {
        this.lottery("dial");
    },
    ratating: function(t) {
        var e = this, a = e.data.angle, s = e.data.rotateAngle, i = e.data.offOn, o = (e.data.halfAngle, 
        e.data.radian, e.data.index), n = e.data.circle, r = null, c = this.pri2();
        o = c, clearInterval(r), r = setInterval(function() {
            s = n - o * a, e.setData({
                time: "4",
                rotateAngle: s
            }), console.log(e.data.rotateAngle), clearInterval(r), setTimeout(function() {
                i = !i, e.setData({
                    sudokuShow: !0
                });
            }, 4e3);
        }, 30);
    },
    pri2: function() {
        var t = "";
        switch (this.data.lottery.prize_level) {
          case "三等奖":
            t = 0;
            break;

          case "四等奖":
            t = 1;
            break;

          case "二等奖":
            t = 2;
            break;

          case "五等奖":
            t = 3;
            break;

          case "一等奖":
            t = 4;
            break;

          case "五等奖":
            t = 5;
            break;

          case "七等奖":
            t = 6;
            break;

          case "六等奖":
            t = 7;
        }
        return t;
    },
    closehuojiang: function(t) {
        this.setData({
            huojiangbj: "0"
        });
    },
    sudoku: function(t) {
        this.lottery("sudoku");
    },
    rollInit: function() {
        var t = this.data.sudokuIndex;
        return (t += 1) > this.data.count - 1 && (t = 0), this.setData({
            sudokuIndex: t
        }), !1;
    },
    roll: function(t) {
        var e = this, a = this.data.times + 1, s = this.data.cycle, i = this.data.prizeIndex, o = this.data.sudokuIndex, n = this.data.timer, r = this.data.speed;
        this.data.count;
        if (e.setData({
            times: a
        }), a > s + 10 && i == o) clearTimeout(n), e.setData({
            prizeIndex: -1,
            times: 0,
            click: !1
        }), setTimeout(function() {
            e.setData({
                sudokuShow: !0
            });
        }, 1e3); else {
            if (e.rollInit(), a < s) e.setData({
                speed: r - 10
            }); else if (a == s) var c = e.pri(); else a > s + 10 && (0 == i && 7 == c || i == c + 1) ? e.setData({
                speed: r + 110
            }) : e.setData({
                speed: r + 20
            });
            r < 40 && e.setData({
                speed: 40
            });
            var d = setTimeout(e.roll, e.data.speed);
            e.setData({
                timer: d
            });
        }
        return !1;
    },
    pri: function() {
        var t = "";
        switch (this.data.lottery.prize_level) {
          case "八等奖":
            t = 0;
            break;

          case "二等奖":
            t = 1;
            break;

          case "五等奖":
            t = 2;
            break;

          case "三等奖":
            t = 3;
            break;

          case "一等奖":
            t = 4;
            break;

          case "四等奖":
            t = 5;
            break;

          case "七等奖":
            t = 6;
            break;

          case "六等奖":
            t = 7;
        }
        return this.setData({
            prizeIndex: t
        }), t;
    },
    sudokuClose: function() {
        this.setData({
            sudokuShow: !1,
            prize_show: !1,
            prize_details: !1,
            prize_details2: !1,
            award_rule: !1
        });
    },
    myAward: function() {
        var t = this;
        wx.getStorage({
            key: "session_id",
            success: function(e) {
                wx.request({
                    url: "https://u.we7shop.com/api/user/check-login",
                    data: {
                        session_id: e.data
                    },
                    method: "POST",
                    header: {
                        "content-type": "application/x-www-form-urlencoded"
                    },
                    success: function(e) {
                        console.log(e.data), 0 == e.data ? t.setData({
                            login: !0,
                            mask: !0
                        }) : t.setData({
                            prize_show: !0
                        });
                    }
                });
            }
        });
    },
    awardRule: function() {
        this.setData({
            award_rule: !0
        });
    },
    awardDetails: function() {
        this.setData({
            prize_details: !0
        });
    },
    awardDetails2: function() {
        this.setData({
            prize_details2: !0
        });
    },
    copyCdkey: function(t) {
        var e = t.target.dataset.key;
        wx.setClipboardData({
            data: e,
            success: function(t) {
                wx.showToast({
                    title: "复制成功",
                    icon: "succes",
                    duration: 1e3,
                    mask: !0
                });
            }
        });
    },
    change: function() {
        this.setData({
            now: !this.data.now
        });
    }
});