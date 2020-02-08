<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " WHERE a.uniacid = :uniacid and a.is_pay = 1";
    $params[":uniacid"] = $_W["uniacid"];
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and b.title like :keyword";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $status = intval($_GPC["status"]);
    if (-1 < $status) {
        $condition .= " and a.status = :status";
        $params[":status"] = $status;
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $records = pdo_fetchall("SELECT a.*,b.title as store_title,b.logo FROM " . tablename("tiny_wmall_advertise_trade") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id " . $condition . " order by a.id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($records)) {
        foreach ($records as &$val) {
            $val["logo"] = tomedia($val["logo"]);
            $val["addtime_cn"] = date("Y-m-d", $val["addtime"]);
            $val["endtime_cn"] = date("Y-m-d", $val["endtime"]);
        }
    }
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
}

?>