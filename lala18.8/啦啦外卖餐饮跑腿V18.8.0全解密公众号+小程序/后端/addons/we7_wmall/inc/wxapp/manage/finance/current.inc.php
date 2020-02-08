<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $sid = intval($_GPC["__mg_sid"]);
    $condition = " where uniacid = :uniacid and sid = :sid";
    $params[":uniacid"] = $_W["uniacid"];
    $params[":sid"] = $sid;
    $trade_type = intval($_GPC["trade_type"]);
    if (0 < $trade_type) {
        $condition .= " and trade_type = :trade_type";
        $params[":trade_type"] = $trade_type;
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $records = pdo_fetchall("select * from " . tablename("tiny_wmall_store_current_log") . $condition . " order by id desc limit " . ($page - 1) * $psize . ", " . $psize, $params);
    if (!empty($records)) {
        $trade_types = order_trade_type();
       foreach ($records as &$row) {
            $row["trade_type_cn"] = $trade_types[$row["trade_type"]]["text"];
            $row["addtime_cn"] = date("Y-m-d H:i", $row["addtime"]);
        }
    }
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
}
if ($ta == "detail") {
    $id = intval($_GPC["id"]);
    $current = pdo_get("tiny_wmall_store_current_log", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($current)) {
        imessage(error(-1, "交易记录不存在"), "", "ajax");
    }
    $trade_types = order_trade_type();
    $current["trade_type_cn"] = $trade_types[$current["trade_type"]]["text"];
    $current["addtime_cn"] = date("Y-m-d H:i", $current["addtime"]);
    $result = array("current" => $current);
    if ($current["trade_type"] == 2) {
        $getcash_log = pdo_get("tiny_wmall_store_getcash_log", array("uniacid" => $_W["uniacid"], "id" => $current["extra"]));
        if (!empty($getcash_log)) {
            $getcash_log["account"] = iunserializer($getcash_log["account"]);
            $getcash_log["addtime_cn"] = date("Y-m-d H:i", $getcash_log["addtime"]);
            $result["getcash_log"] = $getcash_log;
        }
    }
    imessage(error(0, $result), "", "ajax");
}

?>