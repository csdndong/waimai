<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("table");
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
$_W["page"]["title"] = "店内桌台";
if ($ta == "index") {
    $sid = intval($sid);
    $table_category = table_category_fetchall($sid);
    $filter = array("cid" => intval($_GPC["cid"]), "sid" => $sid);
    $tables = table_fetchall($filter);
    $status = table_status();
    foreach ($tables as &$val) {
        $val["status"] = $status[$val["status"]]["text"];
    }
} else {
    if ($ta == "status") {
        $sid = intval($sid);
        if ($_W["ispost"]) {
            $id = intval($_GPC["id"]);
            $table = pdo_get("tiny_wmall_tables", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id), array("status"));
            if ($table["status"] == 1) {
                $update = array("status" => 2);
            } else {
                $update = array("status" => 1, "order_id" => 0);
            }
            if (!empty($update)) {
                pdo_update("tiny_wmall_tables", $update, array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
                imessage(error(0, "设置桌台状态成功"), referer(), "ajax");
            }
            imessage(error(-1, "桌台信息有误"), referer(), "ajax");
        }
    }
}
include itemplate("order/table");

?>