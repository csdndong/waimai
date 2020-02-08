<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $type = trim($_GPC["type"]) ? trim($_GPC["type"]) : "exchangeRedpacket";
    $condition = " where a.uniacid = :uniacid and a.type = :type and a.is_pay = 1";
    $params = array(":uniacid" => $_W["uniacid"], ":type" => $type);
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and b.nickname like '%" . $keyword . "%'";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $records = pdo_fetchall("select a.*,b.avatar,b.nickname from " . tablename("tiny_wmall_superredpacket_meal_order") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid " . $condition . " order by a.id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pay_type = order_pay_types();
    if (!empty($records)) {
        foreach ($records as &$record) {
            $record["data"] = iunserializer($record["data"]);
            $record["pay_type_cn"] = $pay_type[$record["pay_type"]]["text"];
            $record["addtime_cn"] = date("Y-m-d H:i", $record["addtime"]);
        }
    }
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
}

?>