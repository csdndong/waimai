<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " WHERE uniacid = :uniacid and agentid = :agentid";
    $params[":uniacid"] = $_W["uniacid"];
    $params[":agentid"] = $_W["agentid"];
    $work_status = intval($_GPC["work_status"]);
    if (0 < $work_status) {
        $condition .= " and work_status = :work_status";
        $params[":work_status"] = $work_status;
    }
    $is_takeout = isset($_GPC["is_takeout"]) ? intval($_GPC["is_takeout"]) : -1;
    if (-1 < $is_takeout) {
        $condition .= " and is_takeout = :is_takeout";
        $params[":is_takeout"] = $is_takeout;
    }
    $is_errander = isset($_GPC["is_errander"]) ? intval($_GPC["is_errander"]) : -1;
    if (-1 < $is_errander) {
        $condition .= " and is_errander = :is_errander";
        $params[":is_errander"] = $is_errander;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (title like '%" . $keyword . "%' or nickname like '%" . $keyword . "%' or mobile like '%" . $keyword . "%')";
    }
    $data = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_deliveryer") . $condition . " ORDER BY id DESC", $params);
    foreach ($data as &$row) {
        $row = array_elements(array("id", "title", "avatar", "mobile", "realname", "work_status"), $row);
        $row["work_status_cn"] = "休息中";
        if ($row["work_status"] == 1) {
            $row["work_status_cn"] = "工作中";
        }
        $row["perm_cn"] = "暂无配送权限";
        if ($row["is_takeout"]) {
            $row["perm_cn"] = "平台外卖单";
        }
        if ($row["is_errander"]) {
            $row["perm_cn"] = "平台跑腿单";
        }
    }
    message(ierror(0, "", $data), "", "ajax");
}

?>