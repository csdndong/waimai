<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$_W["page"]["title"] = "佣金明细";
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $condition = " where uniacid = :uniacid and spreadid = :spreadid";
    $params = array(":uniacid" => $_W["uniacid"], ":spreadid" => $_W["member"]["uid"]);
    $trade_type = isset($_GPC["trade_type"]) ? intval($_GPC["trade_type"]) : 0;
    if (0 < $trade_type) {
        $condition .= " and trade_type = " . $trade_type;
    }
    $id = intval($_GPC["min"]);
    if (0 < $id) {
        $condition .= " and id < :id";
        $params[":id"] = trim($_GPC["min"]);
    }
    $current = pdo_fetchall("select * from" . tablename("tiny_wmall_spread_current_log") . $condition . " order by id desc limit 10", $params, "id");
    $min = 0;
    if (!empty($current)) {
        foreach ($current as &$v) {
            $v["addtime"] = date("Y-m-d H:i", $v["addtime"]);
        }
        $min = min(array_keys($current));
    }
    $current = array_values($current);
    $respon = array("min" => $min, "detail" => $detail, "current" => $current);
    imessage(error(0, $respon), "", "ajax");
}
if ($op == "detail") {
    $id = intval($_GPC["id"]);
    $detail = pdo_get("tiny_wmall_spread_current_log", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($detail)) {
        imessage(error(-1, "明细记录不存在"), "", "ajax");
    }
    if ($detail["trade_type"] == "1") {
        $detail["trade_type_cn"] = "订单入账";
    } else {
        if ($detail["trade_type"] == "2") {
            $detail["trade_type_cn"] = "申请提现";
        } else {
            $detail["trade_type_cn"] = "其他变动";
        }
    }
    $detail["addtime"] = date("Y-m-d H:i", $detail["addtime"]);
    $result = array("detail" => $detail);
    imessage(error(0, $result), "", "ajax");
}

?>