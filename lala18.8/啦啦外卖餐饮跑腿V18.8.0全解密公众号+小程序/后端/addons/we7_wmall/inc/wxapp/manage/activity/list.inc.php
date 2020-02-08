<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("activity");
mload()->model("coupon");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
$sid = intval($_GPC["__mg_sid"]);
if ($ta == "index") {
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : 1;
    $activity = activity_getall($sid, $status);
    $all_activity = store_all_activity();
    foreach ($activity as $key => &$val) {
        $val["until"] = round(($val["endtime"] - time()) / 86400);
        if ($key == "couponGrant" || $key == "couponCollect") {
            $val["coupon_detail"] = coupon_fetch(0, $val["id"]);
        }
        $val["type_cn"] = $all_activity[$key]["title"];
        $val["starttime_cn"] = date("Y-m-d", $val["starttime"]);
        $val["endtime_cn"] = date("Y-m-d", $val["endtime"]);
        $val["addtime_cn"] = date("Y-m-d", $val["addtime"]);
        if ($key == "bargain") {
            unset($activity["bargain"]);
        }
    }
    $result = array("activity" => $activity);
    imessage(error(0, $result), "", "ajax");
}
if ($ta == "del") {
    $type = $_GPC["type"];
    $status = activity_del($sid, $type);
    if (is_error($status)) {
        imessage($status, "", "ajax");
    }
    imessage(error(0, "撤销活动成功"), "", "ajax");
}

?>