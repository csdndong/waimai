<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$sid = intval($_GPC["sid"]);
store_business_hours_init($sid);
$store = store_fetch($sid);
if (empty($store)) {
    imessage(error(-1, "门店不存在或已经删除"), "", "ajax");
}
define("ORDER_TYPE", "takeout");
mload()->model("goods");
mload()->model("activity");
$price = store_order_condition($store);
$store["send_price"] = $price["send_price"];
$store["goods_style"] = 2;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $result = array("store" => $store, "cart" => cart_data_init($sid), "config_mall" => $_config_mall);
    $categorys = array_values(store_fetchall_goods_category($sid, 1, false, "all", "available"));
    $result["category"] = $categorys;
    $cid = trim($_GPC["cid"]) ? trim($_GPC["cid"]) : $categorys[0]["id"];
    $child_id = 0;
    if (!empty($categorys)) {
        foreach ($categorys as $index => $cate) {
            if ($cate["id"] == $cid) {
                $cindex = $index;
                if (!empty($cate["child"]) && 0 < count($cate["child"])) {
                    $child_id = $cate["child"][0]["id"];
                }
                break;
            }
        }
    }
    $result["goods"] = goods_filter($sid, array("cid" => $cid, "child_id" => $child_id));
    $result["cid"] = $cid;
    $result["child_id"] = $child_id;
    $result["cindex"] = $cindex;
    imessage(error(0, $result), "", "ajax");
}

?>
