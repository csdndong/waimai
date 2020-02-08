<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("activity");
global $_W;
global $_GPC;
$sid = intval($_GPC["__mg_sid"]);
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
$all_activity = store_all_activity();
if ($ta == "index") {
    $activity = activity_getall($sid, -1);
    $result = array("activity" => $activity, "perm" => $_W["we7_wmall"]["config"]["store"]["activity"]["perm"]);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "activity_other") {
        $type = trim($_GPC["type"]);
        if ($_W["we7_wmall"]["config"]["store"]["activity"]["perm"][$type]["status"] != 1) {
            imessage(error(-1000, "平台没有开启该活动，请联系平台管理员开通"), "", "ajax");
        }
        $discount_title = "减";
        $discount_cn = "元";
        $condition_title = "满";
        if ($type == "selfDelivery") {
            $discount_title = "打";
            $discount_cn = "折";
        } else {
            if ($type == "cashGrant") {
                $discount_title = "返";
            } else {
                if ($type == "grant") {
                    $discount_title = "赠送";
                    $discount_cn = "";
                } else {
                    if ($type == "deliveryFeeDiscount") {
                        $discount_cn = "元配送费";
                    } else {
                        if ($type == "selfPickup") {
                            $condition_title = "自提满";
                        }
                    }
                }
            }
        }
        $page_title = $all_activity[$type]["title"];
        if ($_W["ispost"]) {
            $params = json_decode(htmlspecialchars_decode($_GPC["params"]), true);
            $starttime = trim($params["starttime"]);
            if (empty($starttime)) {
                imessage(error(-1, "活动开始时间不能为空"), "", "ajax");
            }
            $endtime = trim($params["endtime"]);
            $starttime = strtotime($starttime);
            $endtime = strtotime($endtime);
            if ($endtime <= $starttime) {
                imessage(error(-1, "活动开始时间不能大于结束时间"), "", "ajax");
            }
            $data = array();
            $title = array();
            if (!empty($params["options"])) {
                if ($type == "newMember") {
                    $back = floatval($params["options"][0]["back"]);
                    if (empty($back)) {
                        imessage(error(-1, "活动金额不能为空"), "", "ajax");
                    }
                    $data = array("back" => $back, "plateform_charge" => 0, "store_charge" => $back);
                    $title[] = "本店新用户立减" . $back . "元";
                } else {
                    foreach ($params["options"] as $val) {
                        $condition = floatval($val["condition"]);
                        if ($type == "grant") {
                            $back = trim($val["back"]);
                        } else {
                            $back = floatval($val["back"]);
                        }
                        if ($condition && $back) {
                            $data[$condition] = array("condition" => $condition, "back" => $back, "plateform_charge" => 0, "store_charge" => $back);
                            $title[] = (string) $condition_title . $condition . "元" . $discount_title . $back . $discount_cn;
                        }
                    }
                }
            }
            if (empty($data)) {
                imessage(error(-1, "活动内容不能为空"), "", "ajax");
            }
            $title = implode(",", $title);
            $activity = array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => $title, "starttime" => $starttime, "endtime" => $endtime, "type" => $type, "status" => 1, "data" => iserializer($data));
            $status = activity_set($sid, $activity);
            if (is_error($status)) {
                imessage($status, "", "ajax");
            }
            imessage(error(0, "设置" . $page_title . "活动成功"), "", "ajax");
        }
        if (empty($page_title)) {
            imessage(error(-1, "活动类型有误"), "", "ajax");
        }
        $result = array("type" => $type, "page_title" => $page_title, "discount_title" => $discount_title, "discount_cn" => $discount_cn);
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($ta == "activity_coupon") {
            $type = trim($_GPC["type"]);
            if ($_W["we7_wmall"]["config"]["store"]["activity"]["perm"][$type]["status"] != 1) {
                imessage(error(-1000, "平台没有开启该活动，请联系平台管理员开通"), "", "ajax");
            }
            if ($_W["ispost"]) {
                $params = json_decode(htmlspecialchars_decode($_GPC["params"]), true);
                $activitytitle = trim($params["title"]);
                if (empty($activitytitle)) {
                    imessage(error(-1, "活动名称不能为空"), "", "ajax");
                }
                $starttime = trim($params["starttime"]);
                if (empty($starttime)) {
                    imessage(error(-1, "活动开始时间不能为空"), "", "ajax");
                }
                $endtime = trim($params["endtime"]);
                $starttime = strtotime($starttime);
                $endtime = strtotime($endtime);
                if ($endtime <= $starttime) {
                    imessage(error(-1, "活动开始时间不能大于结束时间"), "", "ajax");
                }
                $amount = intval($params["amount"]);
                if (empty($amount)) {
                    imessage(error(-1, "券包总数不能为空"), "", "ajax");
                }
                if ($type == "couponCollect") {
                    $type_limit = intval($params["type_limit"]);
                    if (empty($type_limit)) {
                        imessage(error(-1, "请选择面向人群"), "", "ajax");
                    }
                } else {
                    if ($type == "couponGrant") {
                        $condition = intval($params["condition"]);
                        if (empty($condition)) {
                            imessage(error(-1, "返券条件不能为空"), "", "ajax");
                        }
                    }
                }
                if (!empty($params["coupons"])) {
                    $discount = array();
                    foreach ($params["coupons"] as $coupon) {
                        if (0 < $coupon["discount"]) {
                            $discount[] = $coupon["discount"];
                        }
                    }
                    if (empty($discount)) {
                        imessage(error(-1, "请先添加有效优惠券"), "", "ajax");
                    }
                    if ($type == "couponCollect") {
                        $min = min($discount);
                        $max = max($discount);
                        if ($min == $max) {
                            $title = "进店可领" . $min . "元代金券";
                        } else {
                            $title = "进店可领" . $min . "~" . $max . "元代金券";
                        }
                    } else {
                        if ($type == "couponGrant") {
                            $title = "购物满" . $condition . "元可返" . $discount[0] . "元代金券";
                            $params["coupons"] = $params["coupons"][0];
                        }
                    }
                } else {
                    imessage(error(-1, "请先添加优惠券"), "", "ajax");
                }
                $activity = array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => $title, "starttime" => $starttime, "endtime" => $endtime, "type" => $type, "status" => 1, "data" => iserializer($params["coupons"]));
                $status = activity_set($sid, $activity);
                if (is_error($status)) {
                    imessage($status, "", "ajax");
                }
                $coupon = array("uniacid" => $_W["uniacid"], "sid" => $sid, "activity_id" => $status, "title" => $activitytitle, "starttime" => $starttime, "endtime" => $endtime, "type" => $type, "type_limit" => $type_limit, "condition" => $condition, "status" => 1, "amount" => $amount, "coupons" => iserializer($params["coupons"]));
                pdo_insert("tiny_wmall_activity_coupon", $coupon);
                imessage(error(0, "进店领券活动添加成功"), "", "ajax");
            }
            $page_title = $all_activity[$type]["title"];
            $result = array("type" => $type, "page_title" => $page_title);
            imessage(error(0, $result), "", "ajax");
            return 1;
        } else {
            if ($ta == "activity_svipRedpacket") {
                $type = trim($_GPC["type"]);
                if ($_W["ispost"]) {
                    $params = $_GPC["params"];
                    $starttime = trim($params["starttime"]);
                    if (empty($starttime)) {
                        imessage(error(-1, "活动开始时间不能为空"), "", "ajax");
                    }
                    $endtime = trim($params["endtime"]);
                    if (empty($endtime)) {
                        imessage(error(-1, "活动结束时间不能为空"), "", "ajax");
                    }
                    $starttime = strtotime($starttime);
                    $endtime = strtotime($endtime);
                    if ($endtime <= $starttime) {
                        imessage(error(-1, "活动开始时间不能大于结束时间"), "", "ajax");
                    }
                    $discount = floatval($params["discount"]);
                    $config_svip = get_plugin_config("svip");
                    $redpacket_min = floatval($config_svip["basic"]["store_redpacket_min"]);
                    if ($discount < $redpacket_min) {
                        imessage(error(-1, "红包金额不能小于" . $redpacket_min . "元"), "", "ajax");
                    }
                    $condition = 0;
                    $use_days_limit = intval($params["use_days_limit"]);
                    if ($use_days_limit < 0) {
                        imessage(error(-1, "红包有效期必须大于或等于零"), "", "ajax");
                    }
                    $amount = floatval($params["amount"]);
                    if ($amount <= 0) {
                        imessage(error(-1, "每日限领红包数量必须大于零"), "", "ajax");
                    }
                    $store = store_fetch($sid, array("id", "title", "agentid"));
                    $activity = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $sid, "title" => (string) $discount . "元会员红包", "starttime" => $starttime, "endtime" => $endtime, "type" => "svipRedpacket", "status" => 1, "data" => array("discount" => $discount, "condition" => $condition, "use_days_limit" => $use_days_limit, "amount" => $amount, "discount_bear" => array("plateform_charge" => 0, "agent_charge" => 0, "store_charge" => $discount)));
                    $activity["data"] = iserializer($activity["data"]);
                    $status = activity_set($sid, $activity);
                    if (is_error($status)) {
                        imessage($status, "", "ajax");
                    }
                    mload()->model("plugin");
                    pload()->model("svip");
                    $redpacket = array("uniacid" => $_W["uniacid"], "agentid" => $store["agentid"], "sid" => $sid, "title" => $store["title"], "discount" => $discount, "condition" => $condition, "use_days_limit" => $use_days_limit, "amount" => $amount, "starttime" => $starttime, "endtime" => $endtime, "data" => array("plateform_charge" => 0, "agent_charge" => 0, "store_charge" => $discount));
                    $status = svip_set_store_redpacket($sid, $redpacket);
                    if (is_error($status)) {
                        imessage($status, "", "ajax");
                    }
                    imessage(error(0, "设置超级会员门店红包成功"), "refresh", "ajax");
                }
                $page_title = $all_activity[$type]["title"];
                $result = array("type" => $type, "page_title" => $page_title);
                imessage(error(0, $result), "", "ajax");
            }
        }
    }
}

?>