<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "order";
if ($ta == "order") {
    $condition = " where a.uniacid = :uniacid and a.is_pay = 1";
    $params = array(":uniacid" => $_W["uniacid"]);
    $status = intval($_GPC["status"]);
    if ($status == 0) {
        $condition .= " and a.endtime <= :endtime";
        $params[":endtime"] = TIMESTAMP;
    } else {
        if ($status == 1) {
            $condition .= " and a.endtime > :endtime";
            $params[":endtime"] = TIMESTAMP;
        }
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (a.uid = :uid or b.realname like :keyword)";
        $params[":uid"] = $keyword;
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $records = pdo_fetchall("SELECT a.*,b.realname,b.avatar,c.title as card_name FROM " . tablename("tiny_wmall_delivery_cards_order") . "as a left join" . tablename("tiny_wmall_members") . "as b on a.uid = b.uid left join " . tablename("tiny_wmall_delivery_cards") . "as c on a.card_id = c.id " . $condition . " order by a.id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($records)) {
        $pay_types = order_pay_types();
        foreach ($records as &$val) {
            $val["pay_type_cn"] = $pay_types[$val["pay_type"]]["text"];
            $val["paytime_cn"] = date("Y-m-d", $val["paytime"]);
            $val["starttime_cn"] = date("Y-m-d", $val["starttime"]);
            $val["endtime_cn"] = date("Y-m-d", $val["endtime"]);
            if ($val["endtime"] <= time()) {
                $val["card_status"] = "已到期";
            } else {
                $val["card_status"] = "生效中";
            }
            $val["avatar"] = tomedia($val["avatar"]);
        }
    }
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
}

?>