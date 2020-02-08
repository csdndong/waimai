var app = new getApp(), uniacid = app.siteInfo.uniacid;

Page({
    data: {
        uid: "",
        region: [ "北京市", "北京市", "东城区" ],
        region_str: "",
        detail_add: "",
        showBox: !1,
        addList: [],
        editList: [],
        is_select: !1
    },
    onLoad: function(t) {
        var e = wx.getStorageSync("kundian_ordering_uid");
        this.setData({
            uid: e,
            is_select: t.is_select || !1
        }), this.getAddressList();
    },
    getAddressList: function(t) {
        wx.showLoading({
            title: "玩命加载中..."
        });
        var e = this, a = this.data.uid;
        app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "index",
                op: "addressList",
                uniacid: uniacid,
                uid: a
            },
            success: function(t) {
                e.setData({
                    addList: t.data.addList
                }), wx.hideLoading(), console.log(e.data.addList);
            }
        });
    },
    bindRegionChange: function(t) {
        var e = t.detail.value;
        this.setData({
            region_str: e[0] + " " + e[1] + " " + e[2]
        });
    },
    getLocation: function(t) {
        var e = this;
        wx.chooseLocation({
            success: function(t) {
                e.setData({
                    detail_add: t.address
                });
            }
        });
    },
    saveAddress: function(t) {
        wx.showLoading({
            title: "正在保存..."
        });
        var e = this, a = t.detail.value, d = a.phone, i = a.name, s = a.detail_add, n = t.detail.formId, o = this.data, c = o.region_str, r = o.uid, u = o.editList;
        "" != i ? "" != d ? "" != c && "" != s ? app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "index",
                op: "saveAddress",
                operation: "add",
                region: c,
                address: s,
                phone: d,
                name: i,
                uid: r,
                uniacid: uniacid,
                formId: n,
                id: u.id || ""
            },
            success: function(t) {
                wx.showModal({
                    title: "提示",
                    content: t.data.msg,
                    showCancel: !1,
                    success: function() {
                        0 == t.data.code && (e.setData({
                            showBox: !1
                        }), e.getAddressList());
                    }
                }), wx.hideLoading();
            }
        }) : wx.showToast({
            title: "请填写完整的地址",
            icon: "none"
        }) : wx.showToast({
            title: "请填写联系电话",
            icon: "none"
        }) : wx.showToast({
            title: "请填写收货姓名",
            icon: "none"
        });
    },
    changeDeafult: function(t) {
        var e = this, a = t.currentTarget.dataset, d = a.addid, i = a.isdefault, s = t.detail.value, n = this.data.uid;
        1 != i && app.util.request({
            url: "entry/wxapp/order",
            data: {
                control: "index",
                operation: "changeDefault",
                op: "saveAddress",
                id: d,
                is_default: s,
                uid: n,
                uniacid: uniacid
            },
            success: function(t) {
                console.log(t), wx.showToast({
                    title: t.data.msg,
                    icon: "none"
                }), e.getAddressList();
            }
        });
    },
    handAdd: function(t) {
        this.setData({
            showBox: !this.data.showBox,
            editList: [],
            detail_add: "",
            region_str: ""
        });
    },
    editAdd: function(t) {
        var e = this.data.addList, a = t.currentTarget.dataset.addid, d = [];
        e.map(function(t) {
            t.id == a && (d = t);
        }), this.setData({
            editList: d,
            detail_add: d.address,
            region_str: d.region,
            showBox: !0
        });
    },
    deleteAdd: function(t) {
        var e = this, a = t.currentTarget.dataset, d = a.addid, i = a.sub, s = this.data, n = s.uid, o = s.addList;
        wx.showModal({
            title: "提示",
            content: "确认要删除该地址吗？",
            success: function(t) {
                t.confirm && app.util.request({
                    url: "entry/wxapp/order",
                    data: {
                        control: "index",
                        operation: "deleteAdd",
                        op: "saveAddress",
                        id: d,
                        uid: n,
                        uniacid: uniacid
                    },
                    success: function(t) {
                        wx.showToast({
                            title: t.data.msg,
                            icon: "none",
                            duration: 2e3,
                            success: function() {
                                o.splice(i, 1), e.setData({
                                    addList: o
                                });
                            }
                        });
                    }
                });
            }
        });
    },
    wxAdd: function(t) {
        var e = this, a = e.data.uid;
        wx.chooseAddress({
            success: function(t) {
                console.log(t), app.util.request({
                    url: "entry/wxapp/order",
                    data: {
                        control: "index",
                        op: "saveAddress",
                        operation: "add",
                        region: t.provinceName + " " + t.cityName + " " + t.countyName,
                        address: t.detailInfo,
                        phone: t.telNumber,
                        name: t.userName,
                        uid: a,
                        uniacid: uniacid
                    },
                    success: function(t) {
                        wx.showModal({
                            title: "提示",
                            content: t.data.msg,
                            showCancel: !1,
                            success: function() {
                                0 == t.data.code && e.getAddressList();
                            }
                        });
                    }
                });
            },
            fail: function(t) {
                console.log(t);
            }
        });
    },
    selectAddress: function(t) {
        var e = t.currentTarget.dataset.sub, a = this.data, d = a.addList, i = a.uid, s = d[e];
        wx.setStorageSync("selectAdd_" + i, s), wx.navigateBack({
            delta: 1
        });
    }
});