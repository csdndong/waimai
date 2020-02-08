<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("activity");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
$sid = intval($_GPC["__mg_sid"]);
if ($ta == "index") {
    $_W["page"]["title"] = "新建自提满减活动";
    if ($_W["isajax"]) {
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
        $data = array();
        $title = array();
        if (!empty($_GPC["options"])) {
            foreach ($_GPC["options"] as $val) {
                $condition = trim($val["condition"]);
                $back = trim($val["back"]);
                if ($condition && $back) {
                    $data[$condition] = array("condition" => $condition, "back" => $back, "plateform_charge" => 0, "store_charge" => $back);
                    $title[] = "自提满" . $condition . "元减" . $back;
                }
            }
        }
        if (empty($data)) {
            imessage(error(-1, "自提满减活动不能为空"), "", "ajax");
        }
        $title = implode(",", $title);
        $activity = array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => $title, "starttime" => $starttime, "endtime" => $endtime, "type" => "selfPickup", "status" => 1, "data" => iserializer($data));
        $status = activity_set($sid, $activity);
        if (is_error($status)) {
            imessage($status, "", "ajax");
        }
        imessage(error(0, "设置自提满减优惠成功"), "refresh", "ajax");
    }
    $activity = activity_get($sid, "selfPickup");
    if (!empty($activity)) {
        foreach ($activity["data"] as &$row) {
            if (!is_array($row)) {
                continue;
            }
            $data[] = $row;
        }
        $activity["data"] = $data;
    }
}
if ($ta == "del") {
    activity_del($sid, "selfPickup");
    imessage(error(0, "撤销活动成功"), referer(), "ajax");
}
include itemplate("activity/selfPickup");

?>