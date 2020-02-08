<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $_W["page"]["title"] = "超级优惠券发放记录";
    $condition = " where a.uniacid = :uniacid and a.sid = :sid";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
    $status = intval($_GPC["status"]);
    $now = TIMESTAMP;
    if ($status == 1) {
        $condition .= " and a.endtime > " . $now;
    } else {
        if ($status == 2) {
            $condition .= " and a.endtime <= " . $now;
        }
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_supercoupon_coupon") . " as a " . $condition, $params);
    $records = pdo_fetchall("select a.*, b.title as group_title  from " . tablename("tiny_wmall_supercoupon_coupon") . " as a left join " . tablename("tiny_wmall_supercoupon_member_group") . " as b on a.sid = b.sid and a.group_id = b.id " . $condition . " ORDER BY a.id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
    foreach ($records as &$record) {
        $record["data"] = iunserializer($record["data"]);
        $record["total_fee"] = $record["data"]["grant_object"]["total"] * $record["data"]["coupon"]["discount"];
        $use_num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_activity_coupon_record") . " where uniacid = :uniacid and sid = :sid and couponid = :couponid and type = :type and status = 2", array(":uniacid" => $_W["uniacid"], ":type" => "superCoupon", ":sid" => $record["sid"], ":couponid" => $record["id"]));
        $record["orders_num"] = $use_num;
    }
} else {
    if ($ta == "detail") {
        $id = intval($_GPC["id"]);
        $condition = " where a.uniacid = :uniacid and a.sid = :sid and a.id = :id";
        $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":id" => $id);
        $coupon = pdo_fetch("select a.*, b.title as group_title  from " . tablename("tiny_wmall_supercoupon_coupon") . " as a left join " . tablename("tiny_wmall_supercoupon_member_group") . " as b on a.sid = b.sid and a.group_id = b.id " . $condition, $params);
        $coupon["data"] = iunserializer($coupon["data"]);
        $coupon["status"] = TIMESTAMP < $coupon["endtime"] ? 1 : 2;
        $total = $coupon["data"]["grant_object"]["total"];
        $total_fee = $total * $coupon["data"]["coupon"]["discount"];
        $grant_num = $coupon["data"]["grant_object"]["grant_success"];
        $use_num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_activity_coupon_record") . " where uniacid = :uniacid and sid = :sid and couponid = :couponid and type = :type and status = 2", array(":uniacid" => $_W["uniacid"], ":type" => "superCoupon", ":sid" => $coupon["sid"], ":couponid" => $coupon["id"]));
        $grant_percent = round($grant_num / $total, 2) * 100;
        $use_percent = round($use_num / $total, 2) * 100;
    } else {
        if ($ta == "del") {
            $ids = $_GPC["id"];
            if (!is_array($ids)) {
                $ids = array($ids);
            }
            if ($_W["ispost"]) {
                foreach ($ids as $id) {
                    $id = intval($id);
                    pdo_delete("tiny_wmall_supercoupon_coupon", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
                }
                imessage(error(0, "删除成功"), "", "ajax");
            }
        }
    }
}
include itemplate("store/activity/superCoupon-record");

?>