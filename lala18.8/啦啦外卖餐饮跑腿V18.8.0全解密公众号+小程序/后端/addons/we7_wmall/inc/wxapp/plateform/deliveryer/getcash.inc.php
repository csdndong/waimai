<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("deliveryer");
mload()->model("deliveryer.extra");
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " AND b.title like :keyword";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    if (0 < $deliveryer_id) {
        $condition .= " AND a.deliveryer_id = :deliveryer_id";
        $params[":deliveryer_id"] = $deliveryer_id;
    }
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and a.agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $status = intval($_GPC["status"]);
    if (!empty($status)) {
        $condition .= " AND a.status = :status";
        $params[":status"] = $status;
    }
    $days = isset($_GPC["days"]) ? intval($_GPC["days"]) : -2;
    $todaytime = strtotime(date("Y-m-d"));
    $starttime = $todaytime;
    $endtime = $starttime + 86399;
    if (-2 < $days) {
        if ($days == -1) {
            $starttime = strtotime($_GPC["addtime"]["start"]);
            $endtime = strtotime($_GPC["addtime"]["end"]);
            $condition .= " AND a.addtime > :start AND a.addtime < :end";
            $params[":start"] = $starttime;
            $params[":end"] = $endtime;
        } else {
            $starttime = strtotime("-" . $days . " days", $todaytime);
            $condition .= " and a.addtime >= :start";
            $params[":start"] = $starttime;
        }
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $records = pdo_fetchall("select a.*,b.title,b.avatar,b.nickname from " . tablename("tiny_wmall_deliveryer_getcash_log") . " as a left join " . tablename("tiny_wmall_deliveryer") . " as b on a.deliveryer_id = b.id " . $condition . " order by a.id desc limit " . ($page - 1) * $psize . ", " . $psize, $params);
    if (!empty($records)) {
        $toaccount_status_arr = getcash_toaccount_status("", "all", true);
        foreach ($records as &$value) {
            $value["addtime_cn"] = date("Y-m-d H:i", $value["addtime"]);
            $value["endtime_cn"] = date("Y-m-d H:i", $value["endtime"]);
            $value["avatar"] = tomedia($value["avatar"]);
            $value["toaccount_status_cn"] = $toaccount_status_arr[$value["toaccount_status"]]["text"];
            $value["toaccount_status_css"] = $toaccount_status_arr[$value["toaccount_status"]]["css"];
        }
    }
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "status") {
        if (0 < $_W["agentid"]) {
            imessage(error(-1, "没有操作权限"), "", "ajax");
        }
        $id = intval($_GPC["id"]);
        $type = trim($_GPC["type"]);
        $extra = array();
        if ($type == "status") {
            $extra["status"] = intval($_GPC["status"]);
        } else {
            if ($type == "cancel") {
                $extra["remark"] = trim($_GPC["remark"]);
            }
        }
        $result = deliveryer_getcash_update($id, $type, $extra);
        imessage($result, "", "ajax");
    } else {
        if ($ta == "query") {
            $id = intval($_GPC["id"]);
            $result = deliveryer_getcash_update($id, "query");
            imessage($result, "", "ajax");
        }
    }
}

?>