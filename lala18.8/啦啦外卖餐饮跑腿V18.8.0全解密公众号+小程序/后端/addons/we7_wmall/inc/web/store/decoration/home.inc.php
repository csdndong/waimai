<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $_W["page"]["title"] = "门店首页设置";
    $store_id = $sid;
    mload()->model("page");
    $homepage = store_page_get($sid, 0, false);
    if ($_W["ispost"]) {
        $insert = array("uniacid" => $_W["uniacid"], "sid" => $store_id, "type" => "home", "data" => base64_encode(json_encode($_GPC["data"])), "addtime" => TIMESTAMP);
        if (!empty($homepage)) {
            pdo_update("tiny_wmall_store_page", $insert, array("uniacid" => $_W["uniacid"], "sid" => $store_id, "type" => "home"));
        } else {
            pdo_insert("tiny_wmall_store_page", $insert);
        }
        imessage(error(0, "门店首页设置成功"), iurl("store/decoration/home"), "ajax");
    }
    $plugins = get_available_plugin();
    if (empty($homepage["data"])) {
        $params = array("page" => array("title" => "门店首页", "type" => "home", "background" => "#F3F3F3", "navigationbackground" => "#000000", "navigationtextcolor" => "#ffffff"), "items" => array("M1528801076072" => array("style" => array("paddingtop" => "0", "paddingleft" => "0", "dotbackground" => "#ff2d4b", "background" => "#fafafa"), "params" => array("picturedata" => 0, "has_gohome" => check_plugin_exist("gohome") ? 1 : 0), "data" => array("C1528801076072" => array("imgurl" => "../addons/we7_wmall/plugin/wxapp/static/img/default/picture-1.jpg", "linkurl" => ""), "C1528801076073" => array("imgurl" => "../addons/we7_wmall/plugin/wxapp/static/img/default/picture-2.jpg", "linkurl" => "")), "max" => "1", "id" => "picture"), "M1528801043513" => array("params" => array("rownum" => 4, "pagenum" => 8, "navsdata" => 0, "navsnum" => 4, "showtype" => 0, "showdot" => 0, "has_diypage" => check_plugin_perm("diypage") ? 1 : 0), "style" => array("margintop" => "0", "style" => "2", "navstyle" => "radius", "dotbackground" => "#ff2d4b"), "data" => array("C1528801043513" => array("imgurl" => "../addons/we7_wmall/plugin/wxapp/static/img/default/navs-1.png", "linkurl" => "pages/store/goods?sid=" . $store_id, "text" => "点外卖", "color" => "#333333", "decoration" => "优质美食，急速配送", "dec_color" => "#a0a0a0"), "C1528801043514" => array("imgurl" => "../addons/we7_wmall/plugin/wxapp/static/img/default/navs-2.png", "linkurl" => "wx:scanCode", "text" => "扫码点餐", "color" => "#333333", "decoration" => "扫一扫轻松下单", "dec_color" => "#a0a0a0"), "C1528801043515" => array("imgurl" => "../addons/we7_wmall/plugin/wxapp/static/img/default/navs-3.png", "linkurl" => "pages/store/paybill?sid=" . $store_id, "text" => "当面付", "color" => "#333333", "decoration" => "当面收钱", "dec_color" => "#a0a0a0"), "C1528801043516" => array("imgurl" => "../addons/we7_wmall/plugin/wxapp/static/img/default/navs-4.png", "linkurl" => "tangshi/pages/reserve/index?sid=" . $store_id, "text" => "预定", "color" => "#333333", "decoration" => "点餐预定", "dec_color" => "#a0a0a0"), "C1528801043517" => array("imgurl" => "../addons/we7_wmall/plugin/wxapp/static/img/default/navs-1.png", "linkurl" => "tangshi/pages/assign/assign?sid=" . $store_id, "text" => "排号", "color" => "#333333", "decoration" => "店内排号", "dec_color" => "#a0a0a0")), "max" => "1", "id" => "operation"), "M1528801540887" => array("params" => array("titletext" => "商家详情", "icon" => "icon-shop"), "style" => array("margintop" => "0", "titlecolor" => "#333333", "iconcolor" => "#ff2d4b", "statuscolor" => "#ffffff", "statusbackground" => "#FC6167", "namecolor" => "#333333"), "max" => "1", "id" => "info"), "M1528801580463" => array("style" => array("margintop" => "10", "background" => "#ffffff", "activecolor" => "#ff2d4b"), "data" => array("C1528801580463" => array("text" => "代金券", "color" => "#333333", "binddata" => "coupon", "selected" => "0"), "C1528801580464" => array("text" => "本店在售", "color" => "#333333", "binddata" => "onsale", "selected" => "1"), "C1528801580465" => array("text" => "评价", "color" => "#333333", "binddata" => "evaluate", "selected" => "0")), "max" => "1", "id" => "tags"), "M1528801621127" => array("params" => array("getbtntext" => "立即领取", "usebtntext" => "去使用"), "style" => array("pricecolor" => "#F9001A", "conditioncolor" => "#AEAEAE", "scenecolor" => "#DAA8A3", "limitcolor" => "#AEAEAE", "rightbackground" => "#FFD300", "leftbackground" => "#ffffff", "circlecolor" => "#E3BC03", "circletextcolor" => "#ffffff", "btnbordercolor" => "#E3BC03", "getbtncolor" => "#FFD300", "getbtnbackground" => "#ffffff", "usebtncolor" => "#232326", "usebtnbackground" => "#ffec00"), "max" => "1", "id" => "coupon"), "M1528801622558" => array("params" => array("titletext" => "本店在售", "icon" => "icon-goods_light", "goodsdata" => "0", "goodsnum" => "4", "buybtntext" => "购买"), "style" => array("margintop" => "10", "background" => "#ffffff", "titlecolor" => "#333333", "iconcolor" => "#ff2d4b", "goodstitlecolor" => "#333333", "pricecolor" => "#EB3C1E", "oldpricecolor" => "#8E8E8E", "discountcolor" => "#EB3C1E", "buybtncolor" => "#ffffff", "buybtnbackground" => "#fb4e44", "sailedcolor" => "#8E8E8E", "lookallcolor" => "#999999"), "data" => array("C1528801622558" => array("sid" => "0", "goods_id" => "0", "thumb" => "../addons/we7_wmall/plugin/wxapp/static/img/default/goods-1.jpg", "price" => "20.00", "old_price" => "10.00", "title" => "这里是商品标题", "discount" => "5"), "C1528801622559" => array("sid" => "0", "goods_id" => "0", "thumb" => "../addons/we7_wmall/plugin/wxapp/static/img/default/goods-2.jpg", "price" => "20.00", "old_price" => "10.00", "title" => "这里是商品标题", "discount" => "5"), "C1528801622560" => array("sid" => "0", "goods_id" => "0", "thumb" => "../addons/we7_wmall/plugin/wxapp/static/img/default/goods-3.jpg", "price" => "20.00", "old_price" => "10.00", "title" => "这里是商品标题", "discount" => "5"), "C1528801622561" => array("sid" => "0", "goods_id" => "0", "thumb" => "../addons/we7_wmall/plugin/wxapp/static/img/default/goods-4.jpg", "price" => "20.00", "old_price" => "10.00", "title" => "这里是商品标题", "discount" => "5")), "max" => "1", "id" => "onsale"), "M1528801624190" => array("params" => array("titletext" => "评论", "icon" => "icon-comment"), "style" => array("margintop" => "10", "background" => "#fff", "titlecolor" => "#333333", "iconcolor" => "#ff2d4b", "telcolor" => "#2f2f2f", "timecolor" => "#898989", "contentcolor" => "#333", "goodstitlecolor" => "#576B95", "replaycolor" => "#898989", "replaybackground" => "#F4F4F4", "lookallcolor" => "#999"), "max" => "1", "id" => "evaluate")));
        $homepage["data"] = $params;
    }
    include itemplate("store/decoration/home");
} else {
    if ($ta == "cover") {
        mload()->model("qrcode");
        $_W["page"]["title"] = "小程序入口";
        $urls = array("wxapp" => "pages/store/home?sid=" . $sid);
        $path = "we7_wmall/wxappqrcode/store/" . $_W["uniacid"] . "/" . $sid . "_store_home.png";
        if (ifile_exists(tomedia($path))) {
            $legel = 1;
        } else {
            $legel = 0;
        }
        if ($_W["ispost"]) {
            $params = array("url" => "pages/store/home", "scene" => "store:" . $sid, "name" => $path);
            $res = qrcode_wxapp_build($params);
            if (is_error($res)) {
                imessage($res, iurl("store/decoration/home/cover"), "ajax");
            }
            imessage(error(0, "生成二维码成功"), iurl("store/decoration/home/cover"), "ajax");
        }
        include itemplate("store/decoration/cover");
    }
}

?>