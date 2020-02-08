var t = getApp();

Page({
    data: {
        goodsAll: {},
        goodsItem: {},
        category_goodsNum: {}
    },
    onLoad: function(t) {
        var a = this;
        a.data.options = t;
        var e = t.cid || 0;
        a.onGetGoods(e);
    },
    onToggleCategory: function(t) {
        var a = this, e = t.currentTarget.dataset.id;
        a.data.selectCategory = a.data.categorys[e], a.data.selectCategory.goods_num = a.data.category_goodsNum[e], 
        a.setData({
            selectCategory: a.data.selectCategory,
            selectCategoryId: e
        }), a.onGetGoods(e);
    },
    onGetGoods: function(a) {
        var e = this;
        if (e.data.goodsAll[a]) return e.setData({
            goodsItem: e.data.goodsAll[a]
        }), !1;
        t.util.request({
            url: "manage/goods/index/list",
            data: {
                cid: a
            },
            success: function(a) {
                var o = a.data.message;
                if (o.errno) return t.util.toast(o.message), !1;
                e.data.goodsItem = o.message.goods, e.data.selectCategoryId = o.message.cid, e.data.goodsAll[e.data.selectCategoryId] = e.data.goodsItem, 
                e.data.selectCategory = o.message.categorys[e.data.selectCategoryId], e.data.selectCategory.goods_num = o.message.goods_num, 
                e.data.category_goodsNum[e.data.selectCategoryId] = o.message.goods_num, e.setData({
                    categorys: o.message.categorys,
                    goodsItem: o.message.goods,
                    selectCategoryId: o.message.cid,
                    selectCategory: e.data.selectCategory
                });
            }
        });
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    },
    onChangeStatus: function(a) {
        var e = this, o = a.currentTarget.dataset, s = o.id, d = o.type, n = o.value, g = o.cid, r = o.index, c = "", l = {}, i = "";
        "status" == d ? (c = "manage/goods/index/status", i = 1 == n ? "确定下架此商品吗?" : "确定上架此商品吗?", 
        l = {
            id: s,
            value: 1 == n ? 0 : 1
        }) : "total" == d && (c = "manage/goods/index/turncate", i = "确定设置已售罄吗?", l = {
            id: s
        }), wx.showModal({
            title: "",
            content: i,
            success: function(a) {
                a.confirm ? t.util.request({
                    url: c,
                    data: l,
                    showloading: !1,
                    success: function(a) {
                        var o = a.data.message;
                        if (t.util.toast(o.message), o.errno) return !1;
                        "status" == d ? (e.data.goodsAll[g][r].status = 1 == n ? 0 : 1, e.setData({
                            goodsItem: e.data.goodsAll[g]
                        })) : "total" == d && (e.data.goodsAll[g][r].total = 0, e.setData({
                            goodsItem: e.data.goodsAll[g]
                        }));
                    }
                }) : a.cancel;
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        var t = this.data.selectCategoryId;
        delete this.data.goodsAll[t], this.onGetGoods(t), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});