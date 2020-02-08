<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "商户活动";
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "status" => 1, "is_waimai" => 1), array("id", "title", "logo"), "id");
    $all_activity = store_all_activity();
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $sid = trim($_GPC["sid"]);
    if (!empty($sid)) {
        $condition .= " and a.sid = :sid";
        $params[":sid"] = $sid;
    }
    $type = trim($_GPC["type"]);
    if (!empty($type)) {
        $condition .= " and a.type = :type";
        $params[":type"] = $type;
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 30;
    $activitis = pdo_fetchall("select a.*,b.title as store_title from" . tablename("tiny_wmall_store_activity") . "as a left join" . tablename("tiny_wmall_store") . "as b on a.sid = b.id" . $condition . " ORDER BY b.id desc LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM" . tablename("tiny_wmall_store_activity") . "as a" . $condition, $params);
    $pager = pagination($total, $pindex, $psize);
}
if ($op == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    $ids_str = implode(",", $ids);
    $data = pdo_fetchall("select type from" . tablename("tiny_wmall_store_activity") . "where uniacid = :uniacid and id in (" . $ids_str . ")", array(":uniacid" => $_W["uniacid"]), "id");
    foreach ($ids as $id) {
        pdo_delete("tiny_wmall_store_activity", array("uniacid" => $_W["uniacid"], "id" => $id));
        if ($data[$id]["type"] == "couponGrant" || $data[$id]["type"] == "couponCollect") {
            pdo_update("tiny_wmall_activity_coupon", array("status" => 0), array("uniacid" => $_W["uniacid"], "type" => $data[$id]["type"]));
        }
    }
    mload()->model("activity");
    activity_cron();
    imessage(error(0, "活动删除成功"), "", "ajax");
}
if ($op == "add") {
    $_W["page"]["title"] = "活动创建";
    $all_activity = array("mallNewMember" => array("title" => "平台新用户", "key" => "mallNewMember", "label" => "label-danger"), "newMember" => array("title" => "门店新用户", "key" => "newMember", "label" => "label-danger"), "discount" => array("title" => "满减优惠", "key" => "discount", "label" => "label-danger"), "cashGrant" => array("title" => "下单返现", "key" => "cashGrant", "label" => "label-success"), "grant" => array("title" => "下单满赠", "key" => "grant", "label" => "label-success"), "selfDelivery" => array("title" => "自提优惠", "key" => "selfDelivery", "label" => "label-warning"), "deliveryFeeDiscount" => array("title" => "满减配送费", "key" => "deliveryFeeDiscount", "label" => "label-warning"), "selfPickup" => array("title" => "自提满减优惠", "key" => "selfPickup", "label" => "label-success"));

	//商户状态 代理判断
 
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "status" => 1, "is_waimai" => 1), array("id", "title"));
    if ($_W["ispost"]) {
        $type = trim($_GPC["type"]);
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
        if (empty($_GPC["back_" . $type])) {
            imessage(error(-1, "活动优惠不能为空"), "", "ajax");
        }
        $title = array();
        if ($type == "discount" || $type == "cashGrant" || $type == "selfDelivery" || $type == "deliveryFeeDiscount" || $type == "selfPickup") {
            $data = array();
            $condition_type = "condition_" . $type;
            $back_type = "back_" . $type;
            $agent_charge_type = "agent_charge_" . $type;
            $plateform_charge_type = "plateform_charge_" . $type;
            if (!empty($_GPC[$condition_type])) {
                foreach ($_GPC[$condition_type] as $key => $value) {
                    $condition = intval($value);
                    $back = trim($_GPC[$back_type][$key]);
                    if ($condition && $back) {
                        $data[$condition] = array("condition" => $condition, "back" => $back, "plateform_charge" => 0, "store_charge" => $back);
                        if (!empty($_W["ismanager"])) {
                            $data[$condition]["agent_charge"] = trim($_GPC[$agent_charge_type][$key]);
                            $data[$condition]["plateform_charge"] = trim($_GPC[$plateform_charge_type][$key]);
                            if ($back < $data[$condition]["agent_charge"]) {
                                $data[$condition]["agent_charge"] = $back;
                                $data[$condition]["plateform_charge"] = 0;
                                $data[$condition]["store_charge"] = 0;
                            } else {
                                if ($back < $data[$condition]["plateform_charge"]) {
                                    $data[$condition]["plateform_charge"] = $back;
                                    $data[$condition]["agent_charge"] = 0;
                                    $data[$condition]["store_charge"] = 0;
                                } else {
                                    if ($back < $data[$condition]["plateform_charge"] + $data[$condition]["agent_charge"]) {
                                        $data[$condition]["plateform_charge"] = $back - $data[$condition]["agent_charge"];
                                        $data[$condition]["store_charge"] = 0;
                                    } else {
                                        $data[$condition]["store_charge"] = round($back - $data[$condition]["agent_charge"] - $data[$condition]["plateform_charge"], 2);
                                    }
                                }
                            }
                            if ($data[$condition]["store_charge"] < 0) {
                                $data[$condition]["store_charge"] = 0;
                            }
                        } else {
                            if (!empty($_W["isagenter"])) {
                                $data[$condition]["agent_charge"] = trim($_GPC["agent_charge"][$key]);
                                if ($back < $data[$condition]["agent_charge"]) {
                                    $data[$condition]["agent_charge"] = $back;
                                    $data[$condition]["plateform_charge"] = 0;
                                    $data[$condition]["store_charge"] = 0;
                                } else {
                                    $data[$condition]["store_charge"] = round($back - $data[$condition]["agent_charge"], 2);
                                }
                                if ($data[$condition]["store_charge"] < 0) {
                                    $data[$condition]["store_charge"] = 0;
                                }
                            }
                        }
                        if ($type == "discount") {
                            $title[] = "满" . $condition . "元减" . $back . "元";
                        } else {
                            if ($type == "deliveryFeeDiscount") {
                                $title[] = "满" . $condition . "元配送费减" . $back . "元";
                            } else {
                                if ($type == "cashGrant") {
                                    $title[] = "满" . $condition . "元返" . $back . "元";
                                } else {
                                    if ($type == "selfPickup") {
                                        $title[] = "自提满" . $condition . "元减" . $back . "元";
                                    } else {
                                        $title[] = "满" . $condition . "元打" . $back . "元";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            if ($type == "newMember" || $type == "mallNewMember") {
                $back_type = "back_" . $type;
                $agent_charge_type = "agent_charge_" . $type;
                $plateform_charge_type = "plateform_charge_" . $type;
                $back = intval($_GPC[$back_type]);
                $data = array("back" => $back, "plateform_charge" => 0, "store_charge" => $back);
                if (!empty($_W["ismanager"])) {
                    $data["agent_charge"] = trim($_GPC[$agent_charge_type]);
                    $data["plateform_charge"] = trim($_GPC[$plateform_charge_type]);
                    if ($back < $data["agent_charge"]) {
                        $data["agent_charge"] = $back;
                        $data["plateform_charge"] = 0;
                        $data["store_charge"] = 0;
                    } else {
                        if ($back < $data["plateform_charge"]) {
                            $data["plateform_charge"] = $back;
                            $data["agent_charge"] = 0;
                            $data["store_charge"] = 0;
                        } else {
                            if ($back < $data["plateform_charge"] + $data["agent_charge"]) {
                                $data["plateform_charge"] = $back - $data["agent_charge"];
                                $data["store_charge"] = 0;
                            } else {
                                $data["store_charge"] = round($back - $data["agent_charge"] - $data["plateform_charge"], 2);
                            }
                        }
                    }
                    if ($data["store_charge"] < 0) {
                        $data["store_charge"] = 0;
                    }
                } else {
                    if (!empty($_W["isagenter"])) {
                        $data["agent_charge"] = trim($_GPC[$agent_charge_type]);
                        if ($back < $data["agent_charge"]) {
                            $data["agent_charge"] = $back;
                            $data["plateform_charge"] = 0;
                            $data["store_charge"] = 0;
                        } else {
                            $data["store_charge"] = round($back - $data["agent_charge"], 2);
                        }
                        if ($data["store_charge"] < 0) {
                            $data["store_charge"] = 0;
                        }
                    }
                }
                if ($type == "mallNewMember") {
                     $title[] = "平台新用户立减" . $back . "元";
                } else {
                    $title[] = "门店新用户立减" . $back . "元";
                }
            } else {
                if ($type == "grant") {
                    $data = array();
                    if (!empty($_GPC["condition_grant"])) {
                        foreach ($_GPC["condition_grant"] as $key => $value) {
                            $condition = intval($value);
                            $back = trim($_GPC["back_grant"][$key]);
                            if ($condition && $back) {
                                $data[$condition] = array("condition" => $condition, "back" => $back);
                                $title[] = "满" . $condition . "元赠" . $back;

                            }
                        }
                    }
                }
            }
        }
        if (empty($data)) {
            imessage(error(-1, "活动内容不能为空"), "", "ajax");
        }
        $title = implode(",", $title);
        $activity = array("uniacid" => $_W["uniacid"], "title" => $title, "addtime" => TIMESTAMP, "starttime" => $starttime, "endtime" => $endtime, "type" => $type, "status" => 1, "data" => iserializer($data));
        $sync = intval($_GPC["sync"]);
        if ($sync == 1) {
            pdo_delete("tiny_wmall_store_activity", array("uniacid" => $_W["uniacid"], "type" => $type));
            foreach ($stores as $val) {
                $activity["sid"] = $val["id"];
                pdo_insert("tiny_wmall_store_activity", $activity);
            }
        } else {
            if ($sync == 2) {
                $store_ids = $_GPC["store_ids"];
                foreach ($store_ids as $storeid) {
                    pdo_delete("tiny_wmall_store_activity", array("uniacid" => $_W["uniacid"], "type" => $type, "sid" => $storeid));
                    $activity["sid"] = $storeid;
                    pdo_insert("tiny_wmall_store_activity", $activity);
                }
            }
        }
        mload()->model("activity");
        activity_cron();
        imessage(error(0, "创建活动成功"), iurl("merchant/activity/index"), "ajax");
    }
    $length = array(0, 1, 2, 3);
}
if ($op == "displayorder") {
    $_W["page"]["title"] = "活动展示排序";
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "status" => 1, "is_waimai" => 1), array("id", "title"));
    $all_activity = store_all_activity();
    if ($_W["ispost"]) {
        $displayorder = $_GPC["displayorder"];
        $sync = intval($_GPC["sync"]);
        if ($sync == 1) {
            foreach ($displayorder as $key => $val) {
                $val = intval($val);
                if (0 < $val) {
                    pdo_update("tiny_wmall_store_activity", array("displayorder" => $val), array("uniacid" => $_W["uniacid"], "type" => $key));
                }
            }
        } else {
            if ($sync == 2) {
                $store_ids = $_GPC["store_ids"];
                $store_ids_str = implode(",", $store_ids);
                foreach ($displayorder as $key => $val) {
                    $val = intval($val);
                    if (0 < $val) {
                        pdo_query("update " . tablename("tiny_wmall_store_activity") . " set displayorder = " . $val . " where uniacid = :uniacid and type = :type and sid in (" . $store_ids_str . ")", array(":uniacid" => $_W["uniacid"], ":type" => $key));
                    }
                }
            }
        }
        set_system_config("store.activity.displayorder", $displayorder);
        imessage(error(0, "设置排序成功"), referer(), "ajax");
    }
    $displayorder = get_system_config("store.activity.displayorder");
}
include itemplate("merchant/activity");

?>
