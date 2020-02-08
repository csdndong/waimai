<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " WHERE a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " AND b.title like :keyword";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $status = intval($_GPC["status"]);
    if (0 < $status) {
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
    $records = pdo_fetchall("SELECT a.*,b.title as store_title,b.logo FROM " . tablename("tiny_wmall_store_getcash_log") . "as a left join" . tablename("tiny_wmall_store") . "as b on a.sid = b.id " . $condition . " order by a.id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($records)) {
        foreach ($records as &$val) {
            $val["account"] = iunserializer($val["account"]);
            $val["addtime_cn"] = date("Y-m-d H:i", $val["addtime"]);
            $val["endtime_cn"] = date("Y-m-d H:i", $val["endtime"]);
        }
    }
    $filter = array("extra" => array("time" => "days"), "status" => array("title" => "提现状态", "name" => "status", "options" => array(array("title" => "不限", "value" => "-1"), array("title" => "申请中", "value" => "2"), array("title" => "成功", "value" => "1"), array("title" => "撤销", "value" => "3"))));
    $filter = get_filter_params($filter);
    $result = array("records" => $records, "filter" => $filter);
    imessage(error(0, $result), "", "ajax");
}

?>