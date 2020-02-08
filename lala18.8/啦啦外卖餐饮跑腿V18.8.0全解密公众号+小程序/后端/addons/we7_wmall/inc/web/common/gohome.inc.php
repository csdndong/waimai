<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]);
if ($op == "goods") {
    if (isset($_GPC["key"])) {
        $key = trim($_GPC["key"]);
        $type = trim($_GPC["type"]);
        $sid = intval($_GPC["sid"]);
        $filter = array("keyword" => $key, "status" => 1, "sid" => $sid);
        $_GPC["psize"] = 50;
        mload()->model("plugin");
        pload()->model($type);
        if ($type == "kanjia") {
            $goods = kanjia_get_activitylist($filter);
        } else {
            if ($type == "pintuan") {
                $goods = pintuan_get_activitylist($filter);
            } else {
                if ($type == "seckill") {
                    $goods = seckill_allgoods($filter);
                }
            }
        }
        message(array("errno" => 0, "message" => $goods, "data" => $goods), "", "ajax");
    }
    include itemplate("public/gohomeGoods");
}

?>