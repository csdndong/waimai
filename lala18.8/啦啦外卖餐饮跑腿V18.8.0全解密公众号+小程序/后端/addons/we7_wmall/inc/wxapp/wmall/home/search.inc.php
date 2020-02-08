<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("page");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
$config_mall = $_W["we7_wmall"]["config"]["mall"];
if ($ta == "index") {

    $orderbys = store_orderbys();
    $discounts = store_discounts();
    $delivery_type = intval($_GPC["delivery_type"]);
    if ($delivery_type == 2) {
        $carousel = array("title" => "到店自提");
    } else {
        $carousel = store_fetch_category();
    }
    if (check_plugin_perm("svip")) {
        $config_mall["is_has_svip"] = 1;
    }
    if (check_plugin_perm("zhunshibao")) {
        $config_mall["is_has_zhunshibao"] = 1;
    }
    $result = array("config" => $config_mall, "stores" => store_filter(), "orderbys" => $orderbys, "discounts" => $discounts, "carousel" => $carousel);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "store") {
        $result = store_filter();
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($ta == "allcategory") {
            if ($_W["is_agent"] && $_W["agentid"] == -1) {
                imessage(error(-2, "获取定位失败!您可以选择手动搜索地址"), "", "ajax");
            }
            $allcategory = store_fetchall_category("parent_child", array("is_sys" => 1, "store_num" => 1));
            $result = array("allcategory" => array_values($allcategory));
            imessage(error(0, $result), "", "ajax");
        }
    }
}

?>