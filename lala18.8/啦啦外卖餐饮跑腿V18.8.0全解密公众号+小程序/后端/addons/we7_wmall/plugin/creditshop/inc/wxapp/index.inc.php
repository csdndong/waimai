<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $result = array("adv" => creditshop_adv_get(1), "goods" => creditshop_goodsall_get(), "category" => creditshop_category_get(1), "member" => $_W["member"]);
    imessage(error(0, $result), "", "ajax");
}
if ($op == "goods") {
    $goods = creditshop_goodsall_get();
    $result = array("goods" => $goods);
    imessage(error(0, $result), "", "ajax");
}
if ($op == "detail") {
    $id = $_GPC["id"];
    $good = creditshop_goods_get($id);
    $can = creditshop_can_exchange_goods($id);
    if ($can["errno"] == -2) {
        $good["can"] = 0;
    } else {
        $good["can"] = 1;
    }
    $goods = creditshop_goodsall_get(array("page" => 1, "psize" => 4, "type" => $good["type"]));
    $records = creditshop_record_get();
    $goods_keys = array();
    if (!empty($goods)) {
        foreach ($goods as $key => $value) {
            if ($value["id"] == $id) {
                unset($goods[$key]);
            }
        }
    }
    $goods = array_slice($goods, 0, 3, true);
    $member = $_W["member"];
    $member["credit1"] = intval($member["credit1"]);
    $result = array("good" => $good, "goods" => $goods, "member" => $member, "records" => $records);
    imessage(error(0, $result), "", "ajax");
}

?>