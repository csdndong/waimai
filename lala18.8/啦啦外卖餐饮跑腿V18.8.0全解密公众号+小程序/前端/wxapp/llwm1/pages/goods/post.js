var t = getApp();

Page({
    data: {
        goodsTemp: {}
    },
    onLoad: function(e) {
        var o = this;
        o.data.options = e, t.util.request({
            url: "manage/goods/index/post",
            data: {
                id: e.id || 0
            },
            success: function(e) {
                var a = e.data.message;
                if (a.errno) return t.util.toast(a.message), !1;
                o.data.goodsTemp = a.message.goods, o.setData({
                    goodsTemp: o.data.goodsTemp,
                    categorys: a.message.categorys,
                    type: a.message.type
                });
            }
        });
    },
    onSelectCategory: function(t) {
        var e = this, o = t.detail.value, a = e.data.categorys[o];
        e.setData({
            "goodsTemp.cid": a.parentid ? a.parentid : a.id,
            "goodsTemp.child_id": a.parentid ? a.id : 0,
            "goodsTemp.category_title": a.title
        });
    },
    onSelectType: function(t) {
        var e = this, o = t.detail.value, a = e.data.type[o];
        e.setData({
            "goodsTemp.type": a.id,
            "goodsTemp.type_title": a.title
        });
    },
    switch1Change: function(t) {
        var e = this, o = t.currentTarget.dataset.name, a = t.detail.value;
        e.data.goodsTemp[o] = a ? 1 : 0, e.setData({
            goodsTemp: e.data.goodsTemp
        });
    },
    onSelectGoodsImage: function() {
        var e = this;
        t.util.image({
            count: 1,
            success: function(t) {
                e.setData({
                    "goodsTemp.thumb_": t.url,
                    "goodsTemp.thumb": t.filename
                });
            }
        });
    },
    onSubmit: function(e) {
        var o = this, a = e.detail.value;
        return a.title ? a.price ? (a.cid = o.data.goodsTemp.cid, a.cid ? (a.type = o.data.goodsTemp.type, 
        a.type ? (a.thumb = o.data.goodsTemp.thumb, a.child_id = o.data.goodsTemp.child_id, 
        void t.util.request({
            url: "manage/goods/index/post",
            data: {
                id: o.options.id || 0,
                params: JSON.stringify(a),
                formid: e.detail.formId
            },
            method: "POST",
            success: function(e) {
                var o = e.data.message;
                if (o.errno) return t.util.toast(o.message), !1;
                t.util.toast(o.message, "./index", 1e3);
            }
        })) : (t.util.toast("请选择商品所属类型", "", 1e3), !1)) : (t.util.toast("请选择商品所属分类", "", 1e3), 
        !1)) : (t.util.toast("商品价格不能为空", "", 1e3), !1) : (t.util.toast("商品名称不能为空", "", 1e3), 
        !1);
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});