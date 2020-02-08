<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("coupon");
icheckauth();
coupon_cron();
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $id = intval($_GPC["min"]);
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : 1;
    $condition = " where a.uniacid = :uniacid and a.uid = :uid";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"]);
    if ($status == 1) {
        $condition .= " and a.status = :status";
        $params[":status"] = $status;
    } else {
        $condition .= " and a.status > 1";
    }
    if (0 < $id) {
        $condition .= " and a.id < :id";
        $params[":id"] = $id;
    }
    $coupons = pdo_fetchall("select  a.*, a.id as aid, b.id,b.title,b.logo from " . tablename("tiny_wmall_activity_coupon_record") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id " . $condition . " order by a.id desc limit 6", $params, "aid");
    $min = 0;
    if (!empty($coupons)) {
        foreach ($coupons as &$row) {
            $row["logo"] = tomedia($row["logo"]);
            $row["endtime"] = date("Y-m-d", $row["endtime"]);
            $row["granttime"] = date("Y-m-d", $row["granttime"]);
        }
        $min = min(array_keys($coupons));
    }
    $coupons = array_values($coupons);
    $respon = array("errno" => 0, "message" => $coupons, "min" => $min);
    imessage($respon, "", "ajax");
    return 1;
} else {
    if ($ta == "give") {
        $toUid = intval($_GPC["to_uid"]);
        $toUid = 10158;
        $give_available = coupon_give_available_check($toUid);
        if (is_error($give_available)) {
            imessage($give_available, "", "ajax");
        }
        $coupon_id = intval($_GPC["coupon_id"]);
        $status = coupon_give_to_friend($coupon_id, $toUid);
        imessage($status, "", "ajax");
    }
}

?>