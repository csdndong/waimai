<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]);
if ($op == "link") {
    $data = array();
    $getType = trim($_GPC["type"]);
    if (empty($getType)) {
        $getType = "all";
    }
    $getType = explode(",", $getType);
    $data["takeout"]["sys"] = array("title" => "小程序链接", "items" => array(array("title" => "平台首页", "url" => "pages/home/index"), array("title" => "搜索商家", "url" => "pages/home/search"), array("title" => "会员中心", "url" => "pages/member/mine"), array("title" => "我的订单", "url" => "pages/order/index"), array("title" => "我的代金券", "url" => "pages/member/coupon/index"), array("title" => "我的收货地址", "url" => "pages/member/address"), array("title" => "我的收藏", "url" => "pages/member/favorite"), array("title" => "配送会员卡", "url" => "package/pages/deliveryCard/index"), array("title" => "领券中心", "url" => "pages/channel/coupon"), array("title" => "余额充值", "url" => "pages/member/recharge"), array("title" => "天天特价", "url" => "pages/channel/bargain")));
    if (check_plugin_perm("errander")) {
        $data["errander"] = array(array("title" => "平台链接", "items" => array(array("title" => "跑腿首页", "url" => "pages/errander/index"), array("title" => "跑腿订单", "url" => "pages/errander/order"))));
        $data["errander"]["business"] = array("title" => "业务链接", "items" => array());
        $categorys = pdo_getall("tiny_wmall_errander_category", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "status" => 1), array("id", "title"));
        if (!empty($categorys)) {
            foreach ($categorys as $category) {
                $data["errander"]["business"]["items"][] = array("title" => $category["title"], "url" => "pages/errander/category?id=" . $category["id"]);
            }
        }
        $data["errander"]["scene"] = array("title" => "跑腿场景（新版）", "items" => array());
        $scenes = pdo_getall("tiny_wmall_errander_page", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "type" => "scene"), array("id", "name"));
        if (!empty($scenes)) {
            foreach ($scenes as $scene) {
                $data["errander"]["scene"]["items"][] = array("title" => $scene["name"], "url" => "pages/paotui/diy?id=" . $scene["id"]);
            }
        }
    }
    if (check_plugin_perm("spread")) {
        $data["spread"] = array(array("title" => "啦啦推广", "items" => array(array("title" => "推广中心", "url" => "pages/spread/index"))));
    }
    if (check_plugin_perm("ordergrant")) {
        $data["ordergrant"] = array(array("title" => "下单有礼", "items" => array(array("title" => "下单有礼", "url" => "package/pages/ordergrant/index"))));
    }
    if (check_plugin_perm("diypage")) {
        $diypages = pdo_getall("tiny_wmall_wxapp_page", array("uniacid" => $_W["uniacid"]), array("id", "name"));
        if (!empty($diypages)) {
            $data["diyPages"] = $diypages;
        }
    }
    include itemplate("public/wxappLink");
    return 1;
} else {
    if ($op == "icon") {
        include itemplate("public/wxappIcon");
    }
}

?>