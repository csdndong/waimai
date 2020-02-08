App({
    util: require("static/js/utils/util.js"),
    onLaunch: function() {},
    onShow: function() {},
    onHide: function() {},
    WxParse: require("./library/wxParse/wxParse.js"),
    ext: {
        siteInfo: {
            uniacid: "165",
            acid: "165",
            siteroot: "https://www.wazyb.com/app/wxapp.php",
            sitebase: "https://www.wazyb.com/app",
            module: "we7_wmall"
        },
        diy: {
            home: 0
        }
    }
});