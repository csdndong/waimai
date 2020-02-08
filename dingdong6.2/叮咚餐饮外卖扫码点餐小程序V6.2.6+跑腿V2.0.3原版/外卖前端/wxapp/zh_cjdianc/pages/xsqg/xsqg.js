/*   time:2019-07-18 01:03:04*/
var app = getApp();
Page({
    data: {
        navbar: [{
            name: "全部",
            id: ""
        }],
        selectedindex: 0,
        mask1Hidden: !0,
        img: "http://img1.imgtn.bdimg.com/it/u=4078366710,4168441355&fm=200&gp=0.jpg",
        status: 1,
        pagenum: 1,
        order_list: [],
        storelist: [],
        mygd: !1,
        jzgd: !0
    },
    onOverallTag: function(t) {
        console.log(t), this.setData({
            mask1Hidden: !1
        })
    },
    mask1Cancel: function() {
        this.setData({
            mask1Hidden: !0
        })
    },
    selectednavbar: function(t) {
        console.log(t), this.setData({
            pagenum: 1,
            order_list: [],
            storelist: [],
            mygd: !1,
            jzgd: !0,
            selectedindex: t.currentTarget.dataset.index,
            toView: "a" + (t.currentTarget.dataset.index - 1),
            status: Number(t.currentTarget.dataset.index) + 1
        }), this.reLoad()
    },
    reLoad: function() {
        var t, n = this,
            a = this.data.status || 1,
            e = this.data.store_id || "",
            s = null == this.data.store_id ? 1 : "",
            o = this.data.pagenum;
        t = 1 == a ? "" : n.data.navbar[a - 1].id, console.log(a, t, e, o), app.util.request({
            url: "entry/wxapp/QgGoods",
            cachetime: "0",
            data: {
                type_id: t,
                store_id: e,
                page: o,
                pagesize: 10,
                type: s
            },
            success: function(t) {
                console.log("分页返回的列表数据", t.data);
                for (var a = 0; a < t.data.length; a++) t.data[a].discount = (Number(t.data[a].money) / Number(t.data[a].price) * 10).toFixed(1), t.data[a].yqnum = ((Number(t.data[a].number) - Number(t.data[a].surplus)) / Number(t.data[a].number) * 100).toFixed(1);
                t.data.length < 10 ? n.setData({
                    mygd: !0,
                    jzgd: !0
                }) : n.setData({
                    jzgd: !0,
                    pagenum: n.data.pagenum + 1
                });
                var e = n.data.storelist;
                e = function(t) {
                    for (var a = [], e = 0; e < t.length; e++) - 1 == a.indexOf(t[e]) && a.push(t[e]);
                    return a
                }(e = e.concat(t.data)), n.setData({
                    order_list: e,
                    storelist: e
                }), console.log(e)
            }
        })
    },
    onLoad: function(t) {
        app.setNavigationBarColor(this);
        var e = this,
            a = t.storeid;
        console.log(a), e.setData({
            store_id: a
        }), app.util.request({
            url: "entry/wxapp/QgType",
            cachetime: "0",
            success: function(t) {
                var a = e.data.navbar.concat(t.data);
                console.log(t, a), e.setData({
                    navbar: a
                })
            }
        }), this.reLoad()
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        console.log("上拉加载", this.data.pagenum);
        !this.data.mygd && this.data.jzgd && (this.setData({
            jzgd: !1
        }), this.reLoad())
    }
});