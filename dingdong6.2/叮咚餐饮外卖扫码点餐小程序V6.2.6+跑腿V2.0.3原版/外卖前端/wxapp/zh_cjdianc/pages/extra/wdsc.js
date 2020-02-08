/*   time:2019-07-18 01:03:26*/
var app = getApp();
Page({
    data: {
        pagenum: 1,
        storelist: [],
        bfstorelist: [],
        mygd: !1,
        jzgd: !0,
        isjzz: !0,
        params: {
            user_id: wx.getStorageSync("users").id
        }
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this), wx.setNavigationBarTitle({
            title: "我的收藏"
        });
        var a = this;
        app.util.request({
            url: "entry/wxapp/system",
            cachetime: "0",
            success: function(t) {
                console.log(t), a.setData({
                    mdxx: t.data
                })
            }
        }), a.getstorelist()
    },
    tzsjxq: function(t) {
        console.log(t.currentTarget.dataset.sjid, this.data.mdxx), "1" == this.data.mdxx.is_tzms ? (getApp().sjid = t.currentTarget.dataset.sjid, wx.navigateTo({
            url: "/zh_cjdianc/pages/seller/index"
        })) : wx.navigateTo({
            url: "/zh_cjdianc/pages/takeout/takeoutindex?storeid=" + t.currentTarget.dataset.sjid
        })
    },
    getstorelist: function() {
        var o = this,
            i = o.data.pagenum;
        o.data.params.page = i, o.data.params.pagesize = 10, console.log(i, o.data.params), o.setData({
            isjzz: !0
        }), app.util.request({
            url: "entry/wxapp/MyStoreCollection",
            cachetime: "0",
            data: o.data.params,
            success: function(t) {
                console.log("分页返回的商家列表数据", t.data), t.data.length < 10 ? o.setData({
                    mygd: !0,
                    jzgd: !0,
                    isjzz: !1
                }) : o.setData({
                    jzgd: !0,
                    pagenum: i + 1,
                    isjzz: !1
                });
                var a = o.data.bfstorelist;
                a = function(t) {
                    for (var a = [], e = 0; e < t.length; e++) - 1 == a.indexOf(t[e]) && a.push(t[e]);
                    return a
                }(a = a.concat(t.data));
                for (var e = 0; e < t.data.length; e++) {
                    "0.0" == t.data[e].sales && (t.data[e].sales = "5.0");
                    var s = parseFloat(t.data[e].juli);
                    console.log(s), console.log(), t.data[e].aa = s < 1e3 ? s + "m" : (s / 1e3).toFixed(2) + "km", t.data[e].aa1 = s
                }
                o.setData({
                    storelist: a,
                    bfstorelist: a
                }), console.log(a)
            }
        })
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        console.log("上拉加载", this.data.pagenum);
        this.data.mygd || !this.data.jzgd || this.data.isjzz || (this.setData({
            jzgd: !1
        }), this.getstorelist())
    }
});