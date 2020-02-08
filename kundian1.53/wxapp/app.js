App({
    onLaunch: function() {
        var e = this, n = this;
        wx.getStorageSync("kundian_ordering_uid") && (n.globalData.uid = wx.getStorageSync("kundian_ordering_uid"), 
        n.globalData.sessionid = wx.getStorageSync("kundian_ordering_sessionid"), n.globalData.userInfo = wx.getStorageSync("kundian_ordering_userInfo")), 
        wx.getSystemInfo({
            success: function(n) {
                e.globalData.windowHeight = n.windowHeight;
            }
        });
    },
    onShow: function() {},
    onHide: function() {},
    onError: function(n) {
        console.log(n);
    },
    util: require("we7/resource/js/util.js"),
    tabBar: {
        color: "#bfbfbf",
        selectedColor: "black",
        borderStyle: "white",
        backgroundColor: "#fff",
        list: [ {
            pagePath: "/kundian_ordering/pages/index/index/index",
            iconPath: "/kundian_ordering/img/tabbar/index.png",
            selectedIconPath: "/kundian_ordering/img/tabbar/selectIndex.png",
            text: "首页"
        }, {
            pagePath: "/kundian_ordering/pages/order/index/index",
            iconPath: "/kundian_ordering/img/tabbar/order.png",
            selectedIconPath: "/kundian_ordering/img/tabbar/selectOrder.png",
            text: "订单"
        }, {
            pagePath: "/kundian_ordering/pages/user/index/index",
            iconPath: "/kundian_ordering/img/tabbar/user.png",
            selectedIconPath: "/kundian_ordering/img/tabbar/selectUser.png",
            text: "我的"
        } ]
    },
    globalData: {
        userInfo: null,
        uid: "",
        windowHeight: 0
    },
    siteInfo: require("siteinfo.js")
});