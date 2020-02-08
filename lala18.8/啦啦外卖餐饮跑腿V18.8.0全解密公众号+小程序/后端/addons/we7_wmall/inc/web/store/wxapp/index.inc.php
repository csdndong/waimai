<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "template";
if ($ta == "index") {
    $_W["page"]["title"] = "页面配置";
    $extpages = array("pages_store_goods" => array("title" => "商品列表页配色", "key" => "pages_store_goods"));
    if ($_W["ispost"]) {
        store_set_data($sid, "wxapp.extPages", $_GPC["pages"]);
        imessage(error(0, "页面配置成功"), "refresh", "ajax");
    }
    $config_extpage = store_get_data($sid, "wxapp.extPages");
}
if ($ta == "cover") {
    mload()->model("qrcode");
    $_W["page"]["title"] = "小程序入口";
    $urls = array("wxapp" => "pages/store/goods?sid=" . $sid);
    $path = "we7_wmall/wxappqrcode/store/" . $_W["uniacid"] . "/" . $sid . "_store_goods.png";
    if (ifile_exists($path)) {
        $legel = 1;
    } else {
        $legel = 0;
    }
    if ($_W["ispost"]) {
        $params = array("url" => "pages/store/goods", "scene" => "store:" . $sid, "name" => $path);
        $res = qrcode_wxapp_build($params);
        if (is_error($res)) {
            imessage($res, iurl("store/wxapp/index/cover"), "ajax");
        }
        imessage(error(0, "生成二维码成功"), iurl("store/wxapp/index/cover"), "ajax");
    }
}
include itemplate("store/wxapp/index");

?>