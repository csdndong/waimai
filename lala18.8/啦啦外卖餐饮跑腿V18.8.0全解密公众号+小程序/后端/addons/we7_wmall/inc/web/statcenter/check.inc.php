<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "商户账户核对";
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title"), "id");
    $condition = " WHERE uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $sid = intval($_GPC["sid"]);
    $balance = intval($_GPC["balance"]);
    if (0 < $sid && !$balance) {
        $condition .= " and sid = :sid";
        $params[":sid"] = $sid;
    }
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $accounts = pdo_fetchall("SELECT sid,amount FROM " . tablename("tiny_wmall_store_account") . $condition, $params, "sid");
    if (!empty($_GPC["stat_day"])) {
        $starttime = strtotime($_GPC["stat_day"]["start"]);
        $endtime = strtotime($_GPC["stat_day"]["end"]);
        $endtime = $endtime + 86399;
        $condition .= " and addtime >= :starttime and addtime <= :endtime";
        $params[":starttime"] = $starttime;
        $params[":endtime"] = $endtime;
    } else {
        $starttime = $endtime = TIMESTAMP;
    }
    $records_takeout = pdo_fetchall("SELECT sid,\r\n\t\tround(sum(store_final_fee), 2) as takeout_final_fee\r\n\tFROM " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1 group by sid", $params, "sid");
    $records_paybill = pdo_fetchall("SELECT sid,\r\n\t\tround(sum(store_final_fee), 2) as paybill_final_fee\r\n\tFROM " . tablename("tiny_wmall_paybill_order") . $condition . " and status = 1 and is_pay = 1 group by sid", $params, "sid");
    $records_getcash = pdo_fetchall("SELECT sid,\r\n\t\tround(sum(get_fee), 2) as getcash_final_fee\r\n\tFROM " . tablename("tiny_wmall_store_getcash_log") . $condition . " group by sid", $params, "sid");
    $records_change = pdo_fetchall("SELECT sid,\r\n\t\tround(sum(fee), 2) as change_final_fee\r\n\tFROM " . tablename("tiny_wmall_store_current_log") . $condition . " and trade_type = 3 group by sid", $params, "sid");
    if (0 < $sid) {
        $data = array($sid => array("id" => $stores[$sid]["id"], "title" => $stores[$sid]["title"], "takeout_final_fee" => $records_takeout[$sid]["takeout_final_fee"], "paybill_final_fee" => $records_paybill[$sid]["paybill_final_fee"], "getcash_final_fee" => $records_getcash[$sid]["getcash_final_fee"], "change_final_fee" => $records_change[$sid]["change_final_fee"], "amount" => $accounts[$sid]["amount"]));
        $data[$sid]["balance"] = $data[$sid]["takeout_final_fee"] + $data[$sid]["paybill_final_fee"] + $data[$sid]["change_final_fee"] - $data[$sid]["getcash_final_fee"] - $data[$sid]["amount"];
    } else {
        foreach ($stores as &$store) {
            $store["takeout_final_fee"] = $records_takeout[$store["id"]]["takeout_final_fee"];
            $store["paybill_final_fee"] = $records_paybill[$store["id"]]["paybill_final_fee"];
            $store["getcash_final_fee"] = $records_getcash[$store["id"]]["getcash_final_fee"];
            $store["change_final_fee"] = $records_change[$store["id"]]["change_final_fee"];
            $store["amount"] = $accounts[$store["id"]]["amount"];
            $store["balance"] = $store["takeout_final_fee"] + $store["paybill_final_fee"] + $store["change_final_fee"] - $store["getcash_final_fee"] - $store["amount"];
        }
        $data = $stores;
    }
    if ($_W["ispost"] && $balance == 1) {
        mload()->model("store");
        $fee = round($data[$sid]["balance"], 2);
        $status = store_update_account($sid, $fee, 5, "", "平台多退少补，平衡商家账户");
        if (is_error($status)) {
            imessage(error(-1, $status["message"]), "", "ajax");
        }
        imessage(error(0, "平衡账户成功"), "", "ajax");
    }
}
include itemplate("statcenter/check");

?>