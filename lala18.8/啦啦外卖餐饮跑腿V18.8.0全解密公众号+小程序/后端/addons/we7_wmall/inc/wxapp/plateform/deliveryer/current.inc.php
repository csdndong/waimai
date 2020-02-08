<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("deliveryer");
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " WHERE a.uniacid = :uniacid";
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
    $trade_type = intval($_GPC["trade_type"]);
    if ("0" < $trade_type) {
        $condition .= " AND a.trade_type = :trade_type";
        $params[":trade_type"] = $trade_type;
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
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 20;
    $records = pdo_fetchall("select a.*,b.title,b.avatar,b.nickname from " . tablename("tiny_wmall_deliveryer_current_log") . " as a left join " . tablename("tiny_wmall_deliveryer") . " as b on a.deliveryer_id = b.id " . $condition . " order by a.id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($records)) {
        foreach ($records as &$val) {
            $val["addtime_cn"] = date("Y-m-d H:i", $val["addtime"]);
            $val["avatar"] = tomedia($val["avatar"]);
        }
    }
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
}

?>