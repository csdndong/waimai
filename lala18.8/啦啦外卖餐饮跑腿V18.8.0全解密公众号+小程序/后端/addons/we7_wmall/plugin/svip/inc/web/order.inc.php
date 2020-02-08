<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "购买记录";
    if (!empty($_GPC["addtime"]["start"]) && !empty($_GPC["addtime"]["end"])) {
        $_GPC["starttime"] = strtotime($_GPC["addtime"]["start"]);
        $_GPC["endtime"] = strtotime($_GPC["addtime"]["end"]);
    }
    $meal_id = intval($_GPC["meal_id"]);
    $meals = svip_meal_getall();
    $filter = $_GPC;
    $filter["is_pay"] = 1;
    $data = svip_order_getall($filter);
    $orders = $data["orders"];
    $pager = $data["pager"];
}
include itemplate("order");

?>