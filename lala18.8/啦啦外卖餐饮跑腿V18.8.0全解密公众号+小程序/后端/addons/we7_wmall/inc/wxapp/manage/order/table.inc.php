<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("table");
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $sid = intval($sid);
    $table_category = table_category_fetchall($sid);
    $filter = array("cid" => intval($_GPC["cid"]), "sid" => $sid);
    $tables = table_fetchall($filter);
    $status = table_status();
    foreach ($tables as &$val) {
        $val["status_cn"] = $status[$val["status"]]["text"];
    }
    $result = array("tables" => $tables, "table_category" => $table_category);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "status") {
        $sid = intval($sid);
        $id = intval($_GPC["id"]);
        $status = intval($_GPC["status"]);
        if ($status == 1) {
            $update = array("status" => 2);
        } else {
            $update = array("status" => 1, "order_id" => 0);
        }
        if (!empty($update)) {
            pdo_update("tiny_wmall_tables", $update, array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
            imessage(error(0, "设置桌台状态成功"), "", "ajax");
        }
        imessage(error(-1, "桌台信息有误"), "", "ajax");
    }
}

?>