<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("activity");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
$sid = intval($_GPC["__mg_sid"]);
if ($ta == "index") {
    $_W["page"]["title"] = "下单返券";
    if ($_W["isajax"]) {
        $activitytitle = trim($_GPC["title"]);
        if (empty($activitytitle)) {
            imessage(error(-1, "活动名称不能为空"), "", "ajax");
        }
        $starttime = trim($_GPC["starttime"]);
        if (empty($starttime)) {
            imessage(error(-1, "活动开始时间不能为空"), "", "ajax");
        }
        $endtime = trim($_GPC["endtime"]);
        if (empty($endtime)) {
            imessage(error(-1, "活动结束时间不能为空"), "", "ajax");
        }
        $starttime = strtotime($starttime);
        $endtime = strtotime($endtime);
        if ($endtime <= $starttime) {
            imessage(error(-1, "活动开始时间不能大于结束时间"), "", "ajax");
        }
        $condition = trim($_GPC["condition"]);
        if (empty($condition)) {
            imessage(error(-1, "返券条件不能为空"), "", "ajax");
        }
        $amount = trim($_GPC["amount"]);
        if (empty($amount)) {
            imessage(error(-1, "预计发放总数量不能为空"), "", "ajax");
        }
        if (!empty($_GPC["coupon"])) {
            foreach ($_GPC["coupon"] as $value) {
                $_GPC["coupon"] = $value;
                $discount = $value["discount"];
            }
            if (empty($discount)) {
                imessage(error(-1, "请添加优惠券"), "", "ajax");
            }
            $title = "购物满" . $condition . "元可返" . $discount . "元代金券";
        } else {
            imessage(error(-1, "请添加优惠券"), "", "ajax");
        }
        $activity = array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => $title, "starttime" => $starttime, "endtime" => $endtime, "type" => "couponGrant", "status" => 1, "data" => iserializer($_GPC["coupon"]));
        $status = activity_set($sid, $activity);
        if (is_error($status)) {
            imessage($status, "", "ajax");
        }
        $coupon = array("uniacid" => $_W["uniacid"], "sid" => $sid, "activity_id" => $status, "condition" => $condition, "amount" => $amount, "title" => $activitytitle, "starttime" => $starttime, "endtime" => $endtime, "type" => "couponGrant", "status" => 1, "coupons" => iserializer($_GPC["coupon"]));
        pdo_insert("tiny_wmall_activity_coupon", $coupon);
        imessage(error(0, "设置下单返券活动成功"), "refresh", "ajax");
    }
}
include itemplate("activity/couponGrant");

?>