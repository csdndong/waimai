<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth(false);
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $sid = intval($_GPC["sid"]);
    mload()->model("page");
    $homepage = store_page_get($sid);
    if (empty($homepage)) {
        imessage(error(-1, "你需要登陆默认店铺后台-装修-门店首页 设置自定义首页后才能显示出来"), "", "ajax");
    }
    $store = store_fetch($sid);
    $_W["_share"] = array("title" => $store["title"], "desc" => $store["content"], "imgUrl" => tomedia($store["logo"]), "link" => ivurl("/pages/store/home", array("sid" => $sid), true));
    imessage(error(0, array("homepage" => $homepage["data"], "store_id" => $sid, "config_mall" => $_config_mall, "store" => $store)), "", "ajax");
}

?>