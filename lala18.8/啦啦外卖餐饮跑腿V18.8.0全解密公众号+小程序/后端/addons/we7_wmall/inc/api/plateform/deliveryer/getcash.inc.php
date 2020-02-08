<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("deliveryer");
mload()->model("deliveryer.extra");
$op = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "配送员提现记录";
    $condition = " WHERE uniacid = :uniacid";
    $params[":uniacid"] = $_W["uniacid"];
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    if (0 < $deliveryer_id) {
        $condition .= " AND deliveryer_id = :deliveryer_id";
        $params[":deliveryer_id"] = $deliveryer_id;
    }
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $records = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_deliveryer_getcash_log") . $condition . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    foreach ($records as &$record) {
        $record["account"] = iunserializer($record["account"]);
        $record["addtime_cn"] = date("Y-m-d H:i", $record["addtime"]);
    }
    $result = array("records" => $records);
    message(ierror(0, "", $result), "", "ajax");
}
if ($op == "transfers") {
    $id = intval($_GPC["id"]);
    $transfers = deliveryer_getcash_update($id, "transfers");
    if (is_error($transfers)) {
        message(ierror(-1, (string) $transfers["message"]), "", "ajax");
    }
    message(ierror(0, (string) $transfers["message"]), "", "ajax");
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $status = intval($_GPC["status"]);
    $result = deliveryer_getcash_update($id, "status", array("status" => $status));
    message(ierror(0, (string) $result["message"]), "", "ajax");
}
if ($op == "cancel") {
    $id = intval($_GPC["id"]);
    $remark = trim($_GPC["remark"]);
    if (empty($remark)) {
        message(ierror(-1, "撤销原因不能为空"), "", "ajax");
    }
    $result = deliveryer_getcash_update($id, "cancel", array("remark" => $remark));
    if (is_error($result)) {
        message(ierror(-1, (string) $result["message"]), "", "ajax");
    }
    message(ierror(0, (string) $result["message"]), "", "ajax");
}

?>