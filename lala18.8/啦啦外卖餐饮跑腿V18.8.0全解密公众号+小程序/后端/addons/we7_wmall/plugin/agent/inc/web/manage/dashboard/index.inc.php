<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "运营概括";
    $stat = array();
    $condition = " where uniacid = :uniacid and agentid = :agentid and is_pay = 1 and stat_day = :stat_day and order_type <= 2";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":stat_day" => date("Ymd"));
    $stat["total_wait_handel"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . (string) $condition . " and status = 1", $params));
    $stat["total_wait_delivery"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . (string) $condition . " and status = 3", $params));
    $stat["total_wait_refund"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . (string) $condition . " and refund_status = 1", $params));
    $stat["total_wait_reply"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . (string) $condition . " and is_remind = 1", $params));
    $storeCondition = " where uniacid = :uniacid and agentid = :agentid and is_waimai = 1";
    $storeParams = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $store["total_stores"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_store") . (string) $storeCondition . " and (status = 1 or status = 0)", $storeParams));
    $store["total_work_stores"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_store") . (string) $storeCondition . " and (status = 1 or status = 0) and is_rest = 0 ", $storeParams));
    $store["total_rest_stores"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_store") . (string) $storeCondition . " and (status = 1 or status = 0) and is_rest = 1 ", $storeParams));
    $store["total_storage_stores"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_store") . (string) $storeCondition . " and status = 4", $storeParams));
    $deliveryerCondition = " where uniacid = :uniacid and agentid = :agentid";
    $deliveryerParams = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $deliveryer["total_deliveryer"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_deliveryer") . $deliveryerCondition, $deliveryerParams));
    $deliveryer["total_work_deliveryer"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_deliveryer") . (string) $deliveryerCondition . " and status = 1 and work_status = 1", $deliveryerParams));
    $deliveryer["total_rest_deliveryer"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_deliveryer") . (string) $deliveryerCondition . " and status = 1 and work_status = 0", $deliveryerParams));
    $deliveryer["total_storage_deliveryer"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_deliveryer") . (string) $deliveryerCondition . " and status = 2", $deliveryerParams));
}
include itemplate("dashboard/index");

?>