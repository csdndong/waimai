<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "入驻套餐";
    if ($_W["ispost"] && !empty($_GPC["meal"])) {
        foreach ($_GPC["meal"]["title"] as $key => $val) {
            if (empty($val)) {
                continue;
            }
            $time = $_GPC["meal"]["time"][$key];
            if (empty($time)) {
                continue;
            }
            $meal["meal"][] = array("title" => $val, "price" => floatval($_GPC["meal"]["price"][$key]), "time" => $time);
        }
        $meal["status"] = intval($_GPC["status"]);
        set_plugin_config("gohome.haodian.settle", $meal);
        imessage(error(0, "编辑入驻套餐成功"), "refresh", "ajax");
    }
    $meal = get_plugin_config("gohome.haodian.settle");
}
include itemplate("settle");

?>