<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $condition = " WHERE uniacid = :uniacid AND sid = :sid and order_plateform = :order_plateform and status = 5 ";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":order_plateform" => "we7_wmall");
    $days = isset($_GPC["days"]) ? intval($_GPC["days"]) : 0;
    if ($days == -1) {
        $stat_day = json_decode(htmlspecialchars_decode($_GPC["stat_day"]), true);
        $starttime = str_replace("-", "", trim($stat_day["start"]));
        $endtime = str_replace("-", "", trim($stat_day["end"]));
        $condition .= " and stat_day >= :start_day and stat_day <= :end_day";
        $params[":start_day"] = $starttime;
        $params[":end_day"] = $endtime;
    } else {
        $todaytime = strtotime(date("Y-m-d"));
        $starttime = date("Ymd", strtotime("-" . $days . " days", $todaytime));
        $endtime = date("Ymd", $todaytime + 86399);
        $condition .= " and stat_day >= :stat_day";
        $params[":stat_day"] = $starttime;
    }
    $orderby = trim($_GPC["orderby"]) ? trim($_GPC["orderby"]) : "total_goods_price";
    $stat = array();
    $stat["total_goods_num"] = intval(pdo_fetchcolumn("select sum(goods_num) from " . tablename("tiny_wmall_order_stat") . $condition, $params));
    $stat["total_goods_price"] = floatval(pdo_fetchcolumn("select round(sum(goods_price), 2) from " . tablename("tiny_wmall_order_stat") . $condition, $params));
    $records = pdo_fetchall("select stat_day, goods_id, goods_title, sum(goods_price) as total_goods_price, sum(goods_num) as total_goods_num from " . tablename("tiny_wmall_order_stat") . $condition . " group by goods_id order by " . $orderby . " desc", $params);
    if (!empty($records)) {
        $oids = pdo_fetchall("select oid from " . tablename("tiny_wmall_order_stat") . $condition, $params, "oid");
        $oid_str = implode(",", array_keys($oids));
        foreach ($records as &$row) {
            $row["pre_goods_price"] = round($row["total_goods_price"] / $stat["total_goods_price"], 2) * 100 . "%";
            $row["pre_goods_num"] = round($row["total_goods_num"] / $stat["total_goods_num"], 2) * 100 . "%";
            $row["goods"] = pdo_get("tiny_wmall_goods", array("uniacid" => $_W["uniacid"], "id" => $row["goods_id"]), array("title", "number", "is_options", "unitname"));
            $row["goods_unitname"] = $row["goods"]["unitname"];
            $row["goods_title"] = $row["goods"]["title"];
            $row["goods_number"] = $row["goods"]["number"];
            $row["goods_is_options"] = $row["goods"]["is_options"];
            if ($row["goods_is_options"] == 1) {
                $row["options"] = pdo_fetchall("select sum(a.goods_num) as option_goods_num, a.option_id, b.name from " . tablename("tiny_wmall_order_stat") . " as a left join " . tablename("tiny_wmall_goods_options") . " as b on a.option_id = b.id where a.uniacid = :uniacid and a.goods_id = :goods_id and a.oid in(" . $oid_str . ") group by a.option_id", array(":uniacid" => $_W["uniacid"], ":goods_id" => $row["goods_id"]), "option_id");
            }
        }
    }
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
}

?>