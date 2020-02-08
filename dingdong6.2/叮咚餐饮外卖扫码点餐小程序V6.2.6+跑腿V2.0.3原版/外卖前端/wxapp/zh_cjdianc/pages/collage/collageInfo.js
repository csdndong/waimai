var app = getApp();

Page({
    data: {
        group: "拼团开始",
        num: 3,
        button_text: "我要参团",
        sec: ""
    },
    onLoad: function(o) {
        app.setNavigationBarColor(this), console.log("传递过来的参数", o), this.setData({
            id: o.id,
            options: o,
            goods_id: o.goods_id,
            num_peo: Number(o.group_num)
        });
    },
    reload: function(o) {
        var a = this;
        a.data;
        app.util.request({
            url: "entry/wxapp/GoodsInfo",
            cachetime: "0",
            data: {
                goods_id: a.data.goods_id
            },
            success: function(o) {
                for (var t in console.log("商品的详情", o), a.getCountDown(Number(o.data.goods.end_time)), 
                a.setData({
                    goods: o.data.goods
                }), o.data.group) 6 <= o.data.group[t].name.length && (o.data.group[t].name = o.data.group[t].name.slice(0, 6) + "..."), 
                a.data.goods_info.user_id == o.data.group[t].user_id && a.setData({
                    already_group: !0,
                    already: o.data.group[t]
                });
            }
        });
    },
    refresh: function(o) {
        var a = this, e = a.data.id;
        app.util.request({
            url: "entry/wxapp/GroupInfo",
            data: {
                group_id: e
            },
            success: function(o) {
                console.log("团的详情", o);
                var t = Number(o.data.kt_num) - Number(o.data.yg_num);
                a.setData({
                    goods_info: o.data,
                    sy_num: t
                }), a.reload();
            }
        }), app.util.request({
            url: "entry/wxapp/GetGroupUserInfo",
            data: {
                group_id: e
            },
            success: function(o) {
                for (var t in console.log("这是团id", e), console.log("这是参团的人数", o), console.log("这是用户的信息", a.data.userInfo), 
                o.data) {
                    if (console.log(a.data.userInfo.name), console.log(o.data[t].name), a.data.userInfo.name == o.data[t].name) {
                        a.setData({
                            button_text: "邀请好友参团",
                            button: "invite",
                            button_type: "share"
                        });
                        break;
                    }
                    a.setData({
                        button_text: "我要参团",
                        button: "join_group"
                    });
                }
                a.setData({
                    group_user: o.data
                });
            }
        });
    },
    getCountDown: function(s) {
        var i = this;
        "拼团开始" == i.data.group && setInterval(function() {
            var o = new Date(), t = new Date(1e3 * s).getTime() - o.getTime(), a = Math.floor(t / 1e3 / 60 / 60) + "", e = Math.floor(t / 1e3 / 60 % 60) + "", n = Math.floor(t / 1e3 % 60) + "";
            0 < t ? (a < 10 && (a = "0" + a), e < 10 && (e = "0" + e), n < 10 && (n = "0" + n), 
            a = a.split(""), e = e.split(""), n = n.split(""), i.setData({
                hour: a,
                min: e,
                sec: n
            })) : i.setData({
                group: "拼团已结束"
            });
        }, 1e3);
    },
    invite: function(o) {
        this.data;
    },
    join_group: function(o) {
        var t = this.data;
        wx.redirectTo({
            url: "qgform?store_id=" + t.goods.store_id + "&goods_id=" + t.goods_id + "&type=2&group_id=" + t.id + "&end_time=" + t.goods.end_time + "&xf_time=" + t.goods.xf_time
        });
    },
    onReady: function() {},
    onShow: function() {
        var t = this;
        app.getUserInfo(function(o) {
            t.setData({
                userInfo: o
            }), "" == o.img || "" == o.name ? wx.navigateTo({
                url: "../smdc/getdl"
            }) : t.refresh();
        });
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {
        var o = this.data.options;
        return {
            title: wx.getStorageSync("users").name + "邀请您一起来拼团",
            path: "/zh_cjdianc/pages/collage/collageInfo?id=" + o.id + "&user_id=" + o.user_id + "&goods_id=" + o.goods_id,
            success: function(o) {
                console.log(o);
            },
            complete: function(o) {
                console.log("执行");
            }
        };
    }
});