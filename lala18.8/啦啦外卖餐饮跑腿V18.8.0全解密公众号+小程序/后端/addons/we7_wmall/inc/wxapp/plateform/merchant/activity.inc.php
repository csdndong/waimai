<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and a.agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " AND b.title like :keyword";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $status = intval($_GPC["status"]);
    if (-1 < $status) {
        $condition .= " AND a.status = :status";
        $params[":status"] = $status;
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $activitis = pdo_fetchall("select a.*,b.title as store_title from" . tablename("tiny_wmall_store_activity") . "as a left join" . tablename("tiny_wmall_store") . "as b on a.sid = b.id" . $condition . " ORDER BY b.id desc LIMIT " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($activitis)) {
        $all_activity = store_all_activity();
        foreach ($activitis as &$val) {
            $val["type_cn"] = $all_activity[$val["type"]]["title"];
            $val["starttime_cn"] = date("Y-m-d", $val["starttime"]);
            $val["endtime_cn"] = date("Y-m-d", $val["endtime"]);
        }
    }
    $result = array("records" => $activitis);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "del") {
        $id = intval($_GPC["id"]);
        $data = pdo_fetchall("select type from" . tablename("tiny_wmall_store_activity") . "where uniacid = :uniacid and id in (" . $id . ")", array(":uniacid" => $_W["uniacid"]), "id");
        pdo_delete("tiny_wmall_store_activity", array("uniacid" => $_W["uniacid"], "id" => $id));
        if ($data[$id]["type"] == "couponGrant" || $data[$id]["type"] == "couponCollect") {
            pdo_update("tiny_wmall_activity_coupon", array("status" => 0), array("uniacid" => $_W["uniacid"], "type" => $data[$id]["type"]));
        }
        mload()->model("activity");
        activity_cron();
        imessage(error(0, ""), "", "ajax");
    } else {
        if ($ta == "post") {
            $id = intval($_GPC["id"]);
            $records = pdo_get("tiny_wmall_store_activity", array("uniacid" => $_W["uniacid"], "id" => $id));
            if (!empty($records)) {
                $records["starttime_cn"] = date("Y-m-d H:i", $records["starttime"]);
                $records["endtime_cn"] = date("Y-m-d H:i", $records["endtime"]);
                $records["data"] = iunserializer($records["data"]);
                $records["data"] = array_values($records["data"]);
            }
            $result = array("records" => $records);
            imessage(error(0, $result), "", "ajax");
        }
    }
}

?>