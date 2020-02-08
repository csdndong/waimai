<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "yinsihao";
if ($op == "yinsihao") {
    $order_id = intval($_GPC["order_id"]);
    $ordersn = trim($_GPC["ordersn"]);
    $type = trim($_GPC["type"]);
    $orderType = trim($_GPC["orderType"]) ? trim($_GPC["orderType"]) : "waimai";
    $memberType = trim($_GPC["memberType"]) ? trim($_GPC["memberType"]) : "accept";
    $config_basic = $_W["_plugin"]["config"]["basic"];
    if ($type == "store" && empty($config_basic["member_call_store_status"])) {
        imessage(error(-1000, ""), "", "ajax");
    }
    if ($type == "deliveryer" && empty($config_basic["member_call_deliveryer_status"])) {
        imessage(error(-1000, ""), "", "ajax");
    }
    $data = yinsihao_bind($order_id, $type, $ordersn, $orderType, $memberType);
    $type_cn = array("store" => "商家", "deliveryer" => "配送员", "member" => "顾客");
    if (is_error($data)) {
        slog("yinsihao", "隐私号绑定错误", array("order_id" => $order_id), "生成" . $type_cn[$type] . "隐私号错误: " . $data["message"]);
        imessage($data, "", "ajax");
    }
    $result = array("data" => $data);
    imessage(error(0, $result), "", "ajax");
}

?>
