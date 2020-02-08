App({
    onLaunch: function() {
        console.log("=================onLaunch==================");
    },
    onShow: function() {
        console.log("=================onShow==================");
    },
    onHide: function() {},
    util: require("static/js/utils/util.js"),
    WxParse: require("./library/wxParse/wxParse.js"),
    ext: {
        siteInfo: {
            uniacid: "165",
            acid: "165",
            siteroot: "https://www.wazyb.com/app/wxapp.php",
            sitebase: "https://www.wazyb.com/app",
            module: "we7_wmall"
        }
    },
    navigator: {
        list: [ {
            link: "pages/order/index",
            icon: "icon-order"
        }, {
            link: "pages/order/tangshi/index",
            icon: "icon-order"
        }, {
            link: "pages/shop/home",
            icon: "icon-mine"
        }, {
            link: "pages/shop/setting",
            icon: "icon-mine"
        } ],
        position: {
            bottom: "80px"
        }
    }
});