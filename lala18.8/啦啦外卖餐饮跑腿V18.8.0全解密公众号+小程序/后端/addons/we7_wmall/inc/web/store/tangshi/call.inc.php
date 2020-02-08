<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("table");
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $_W["page"]["title"] = "呼叫记录";
    $condition = "where a.uniacid = :uniacid and a.sid = :sid";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : -1;
    if (0 <= $status) {
        $condition .= " and a.status = :status";
        $params[":status"] = $status;
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_table_call_record") . " as a " . $condition, $params);
    $data = pdo_fetchall("select a.*,b.title as table_title from " . tablename("tiny_wmall_table_call_record") . " as a left join " . tablename("tiny_wmall_tables") . " as b on a.table_id = b.id " . $condition . " order by a.id desc limit " . ($pindex - 1) * $psize . ", " . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
}
if ($ta == "status") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    if ($_W["ispost"]) {
        foreach ($ids as $id) {
            pdo_update("tiny_wmall_table_call_record", array("status" => intval($_GPC["status"])), array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
        }
        imessage(error(0, "设为已处理成功"), iurl("store/tangshi/call"), "ajax");
    }
}
if ($ta == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    if ($_W["ispost"]) {
        foreach ($ids as $id) {
            pdo_delete("tiny_wmall_table_call_record", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
        }
        imessage(error(0, "删除成功"), iurl("store/tangshi/call"), "ajax");
    }
}
include itemplate("store/tangshi/call");

?>