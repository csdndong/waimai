<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "门店回收站";
    $condition = " uniacid = :uniacid and agentid = :agentid and status = 4";
    $params[":uniacid"] = $_W["uniacid"];
    $params[":agentid"] = $_W["agentid"];
    $cid = intval($_GPC["cid"]);
    if (0 < $cid) {
        $condition .= " AND cid LIKE :cid";
        $params[":cid"] = "%|" . $cid . "|%";
    }
    $label = intval($_GPC["label"]);
    if (0 < $label) {
        $condition .= " AND label = :label";
        $params[":label"] = $label;
    }
    if (!empty($_GPC["keyword"])) {
        $condition .= " AND title LIKE '%" . $_GPC["keyword"] . "%'";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_store") . " WHERE " . $condition, $params);
    $lists = pdo_fetchall("SELECT id,logo,title,address,telephone,agentid,label,deltime FROM " . tablename("tiny_wmall_store") . " WHERE " . $condition . " ORDER BY displayorder DESC,id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
    $store_label = category_store_label();
    $categorys = store_fetchall_category();
    $store_status = store_status();
}
if ($op == "del") {
    $id = intval($_GPC["id"]);
    pdo_delete("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
    $tables = array("tiny_wmall_activity_bargain", "tiny_wmall_activity_bargain_goods", "tiny_wmall_activity_coupon", "tiny_wmall_activity_coupon_grant_log", "tiny_wmall_activity_coupon_record", "tiny_wmall_assign_board", "tiny_wmall_assign_queue", "tiny_wmall_clerk", "tiny_wmall_goods", "tiny_wmall_goods_category", "tiny_wmall_goods_options", "tiny_wmall_order_cart", "tiny_wmall_order_stat", "tiny_wmall_printer", "tiny_wmall_printer_label", "tiny_wmall_reply", "tiny_wmall_report", "tiny_wmall_reserve", "tiny_wmall_sms_send_log", "tiny_wmall_store_account", "tiny_wmall_store_activity", "tiny_wmall_store_clerk", "tiny_wmall_store_current_log", "tiny_wmall_store_deliveryer", "tiny_wmall_store_favorite", "tiny_wmall_store_getcash_log", "tiny_wmall_store_members", "tiny_wmall_tables", "tiny_wmall_tables_category", "tiny_wmall_tables_scan");
    foreach ($tables as $table) {
        if (pdo_tableexists($table) && pdo_fieldexists($table, "sid")) {
            pdo_delete($table, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $id));
        }
    }
    mlog(2001, $id);
    imessage(error(0, "删除门店成功"), "", "ajax");
}
if ($op == "restore") {
    $id = intval($_GPC["id"]);
    pdo_update("tiny_wmall_store", array("status" => 1), array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
    imessage(error(0, "恢复门店成功"), "", "ajax");
}
include itemplate("merchant/storage");

?>