<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " WHERE a.uniacid = :uniacid and is_pay = 1";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and a.agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $records = pdo_fetchall("select a.*,b.nickname,b.mobile,b.avatar,c.title as store_title from " . tablename("tiny_wmall_paybill_order") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid left join " . tablename("tiny_wmall_store") . " as c on a.sid = c.id " . $condition . " order by a.id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($records)) {
        foreach ($records as &$val) {
            $val["pay_type_cn"] = to_paytype($val["pay_type"]);
            $val["avatar"] = tomedia($val["avatar"]);
            $val["addtime_cn"] = date("Y-m-d H:i", $val["addtime"]);
        }
    }
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
}

?>