<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "商户账户";
    $stores = pdo_fetchall("select id, title, logo from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid and status != 4", array(":uniacid" => $_W["uniacid"]), "id");
    if (!empty($stores)) {
        $stores_ids = implode(",", array_keys($stores));
        pdo_query("delete from " . tablename("tiny_wmall_store_account") . " where uniacid = :uniacid and sid not in (" . $stores_ids . ")", array(":uniacid" => $_W["uniacid"]));
    }
    $condition = " as a left join " . tablename("tiny_wmall_store_account") . " as b on a.id = b.sid where a.uniacid = :uniacid and a.status != 4";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and a.agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $sid = trim($_GPC["sid"]);
    if (!empty($sid)) {
        $condition .= " and b.sid = :sid";
        $params[":sid"] = $sid;
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_store") . $condition, $params);
    $accounts = pdo_fetchall("SELECT a.*, b.sid, b.amount, b.fee_limit, b.fee_max, b.fee_min, b.fee_rate, b.id as bid, b.deposit, b.fee_period FROM " . tablename("tiny_wmall_store") . $condition . " ORDER BY b.id ASC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($accounts)) {
        foreach ($accounts as &$row) {
            if ($row["delivery_fee_mode"] == 2) {
                $row["delivery_price"] = iunserializer($row["delivery_price"]);
            }
            if (empty($row["bid"])) {
                $data = array("uniacid" => $_W["uniacid"], "sid" => $row["id"], "amount" => 0);
                pdo_insert("tiny_wmall_store_account", $data);
            }
        }
    }
    $pager = pagination($total, $pindex, $psize);
    $delivery_modes = store_delivery_modes();
} else {
    if ($op == "turncate") {
        if (!$_W["isajax"]) {
            return false;
        }
        if (empty($_GPC["ids"])) {
            imessage(error(-1, "请选择要操作的账户"), "", "ajax");
        }
        $remark = trim($_GPC["remark"]);
        foreach ($_GPC["ids"] as $sid) {
            $sid = intval($sid);
            if (!$sid) {
                continue;
            }
            $account = pdo_get("tiny_wmall_store_account", array("uniacid" => $_W["uniacid"], "sid" => $sid));
            if (empty($account) || empty($account["amount"]) || $account["amount"] == 0) {
                continue;
            }
            store_update_account($sid, 0 - $account["amount"], 3, "", $remark);
        }
        imessage(error(0, ""), "", "ajax");
    } else {
        if ($op == "changes") {
            $id = intval($_GPC["id"]);
            $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $id), array("logo", "title", "telephone"));
            $account = pdo_get("tiny_wmall_store_account", array("uniacid" => $_W["uniacid"], "sid" => $id), array("amount"));
            if ($_W["ispost"]) {
                $change_type = intval($_GPC["change_type"]);
                $amount = floatval($_GPC["amount"]);
                $remark = trim($_GPC["remark"]);
                if ($change_type == 1) {
                    $fee = "+" . $amount;
                } else {
                    if ($change_type == 2) {
                        $fee = "-" . $amount;
                    } else {
                        $fee = $amount - $account["amount"];
                    }
                }
                store_update_account($id, $fee, 3, "", $remark);
                imessage(error(0, "更改账余额成功"), referer(), "ajax");
            }
            include itemplate("merchant/accountOp");
            exit;
        }
        if ($op == "set") {
            $_W["page"]["title"] = "账户设置";
            $sid = intval($_GPC["id"]);
            $store = store_fetch($sid);
            if (empty($store)) {
                imessage(error(-1, "门店不存在或已删除"), "", "ajax");
            }
            $item = $store;
            $item["isChange"] = 1;
            $account = store_account($sid);
            $gohome = $account["fee_gohome"];
            if (!empty($account["fee_goods"])) {
                foreach ($account["fee_goods"] as $key => $value) {
                    $account["fee_goods"][$key]["title"] = pdo_fetchcolumn("select title from " . tablename("tiny_wmall_goods") . "where uniacid = :uniacid and sid = :sid and id = :id", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":id" => $key));
                }
            }
            if ($_W["ispost"]) {
                $fee_takeout = $account["fee_takeout"];
                $takeout_GPC = $_GPC["fee_takeout"];
                $fee_takeout["type"] = intval($takeout_GPC["type"]) ? intval($takeout_GPC["type"]) : 1;
                if ($fee_takeout["type"] == 2) {
                    $fee_takeout["fee"] = floatval($takeout_GPC["fee"]);
                } else {
                    $fee_takeout["fee_rate"] = floatval($takeout_GPC["fee_rate"]);
                    $fee_takeout["fee_min"] = floatval($takeout_GPC["fee_min"]);
                    $items_yes = array_filter($takeout_GPC["items_yes"], trim);
                    if (empty($items_yes)) {
                        imessage(error(-1, "至少选择一项抽佣项目"), "", "ajax");
                    }
                    $fee_takeout["items_yes"] = $items_yes;
                    $items_no = array_filter($takeout_GPC["items_no"], trim);
                    $fee_takeout["items_no"] = $items_no;
                }
                $fee_selfDelivery = $account["fee_selfDelivery"];
                $selfDelivery_GPC = $_GPC["fee_selfDelivery"];
                $fee_selfDelivery["type"] = intval($selfDelivery_GPC["type"]) ? intval($selfDelivery_GPC["type"]) : 1;
                if ($fee_selfDelivery["type"] == 2) {
                    $fee_selfDelivery["fee"] = floatval($selfDelivery_GPC["fee"]);
                } else {
                    $fee_selfDelivery["fee_rate"] = floatval($selfDelivery_GPC["fee_rate"]);
                    $fee_selfDelivery["fee_min"] = floatval($selfDelivery_GPC["fee_min"]);
                    $items_yes = array_filter($selfDelivery_GPC["items_yes"], trim);
                    if (empty($items_yes)) {
                        imessage(error(-1, "至少选择一项抽佣项目"), "", "ajax");
                    }
                    $fee_selfDelivery["items_yes"] = $items_yes;
                    $items_no = array_filter($selfDelivery_GPC["items_no"], trim);
                    $fee_selfDelivery["items_no"] = $items_no;
                }
                $fee_instore = $account["fee_instore"];
                $instore_GPC = $_GPC["fee_instore"];
                $fee_instore["type"] = intval($instore_GPC["type"]) ? intval($instore_GPC["type"]) : 1;
                if ($fee_instore["type"] == 2) {
                    $fee_instore["fee"] = floatval($instore_GPC["fee"]);
                } else {
                    $fee_instore["fee_rate"] = floatval($instore_GPC["fee_rate"]);
                    $fee_instore["fee_min"] = floatval($instore_GPC["fee_min"]);
                    $items_yes = array_filter($instore_GPC["items_yes"], trim);
                    if (empty($items_yes)) {
                        imessage(error(-1, "至少选择一项抽佣项目"), "", "ajax");
                    }
                    $fee_instore["items_yes"] = $items_yes;
                    $items_no = array_filter($instore_GPC["items_no"], trim);
                    $fee_instore["items_no"] = $items_no;
                }
                $fee_paybill = $account["fee_paybill"];
                $paybill_GPC = $_GPC["fee_paybill"];
                $fee_paybill["type"] = intval($paybill_GPC["type"]) ? intval($paybill_GPC["type"]) : 1;
                if ($fee_paybill["type"] == 2) {
                    $fee_paybill["fee"] = floatval($paybill_GPC["fee"]);
                } else {
                    $fee_paybill["fee_rate"] = floatval($paybill_GPC["fee_rate"]);
                    $fee_paybill["fee_min"] = floatval($paybill_GPC["fee_min"]);
                }
                $fee_goods = $account["fee_goods"];
                $fee_goods_GPC = $_GPC["fee_goods"];
                foreach ($fee_goods as $key => $value) {
                    $fee_goods_GPC_keys = array_keys($fee_goods_GPC);
                    if (!in_array($key, $fee_goods_GPC_keys)) {
                        unset($fee_goods[$key]);
                    }
                }
                foreach ($fee_goods_GPC as $key => $value) {
                    $fee_goods[$key]["type"] = intval($fee_goods_GPC[$key]["type"]) ? intval($fee_goods_GPC[$key]["type"]) : 1;
                    if ($fee_goods_GPC[$key]["type"] == 2) {
                        $fee_goods[$key]["fee"] = floatval($fee_goods_GPC[$key]["fee"]);
                        unset($fee_goods[$key]["fee_rate"]);
                    } else {
                        $fee_goods[$key]["fee_rate"] = floatval($fee_goods_GPC[$key]["fee_rate"]);
                        unset($fee_goods[$key]["fee"]);
                    }
                }
                if (check_plugin_perm("gohome")) {
                    $kanjia_GPC = $_GPC["kanjia"];
                    $kanjia["type"] = intval($kanjia_GPC["type"]);
                    if ($kanjia["type"] == 2) {
                        $kanjia["fee"] = floatval($kanjia_GPC["fee"]);
                    } else {
                        $kanjia["fee_rate"] = floatval($kanjia_GPC["fee_rate"]);
                        $kanjia["fee_min"] = floatval($kanjia_GPC["fee_min"]);
                        $items_yes = array_filter($kanjia_GPC["items_yes"], trim);
                        if (empty($items_yes)) {
                            imessage(error(-1, "至少选择一项砍价抽成项目"), "", "ajax");
                        }
                        $kanjia["items_yes"] = $items_yes;
                        $items_no = array_filter($kanjia_GPC["items_no"], trim);
                        $kanjia["items_no"] = $items_no;
                    }
                    $pintuan_GPC = $_GPC["pintuan"];
                    $pintuan["type"] = intval($pintuan_GPC["type"]);
                    if ($pintuan["type"] == 2) {
                        $pintuan["fee"] = floatval($pintuan_GPC["fee"]);
                    } else {
                        $pintuan["fee_rate"] = floatval($pintuan_GPC["fee_rate"]);
                        $pintuan["fee_min"] = floatval($pintuan_GPC["fee_min"]);
                        $items_yes = array_filter($pintuan_GPC["items_yes"], trim);
                        if (empty($items_yes)) {
                            imessage(error(-1, "至少选择一项拼团抽成项目"), "", "ajax");
                        }
                        $pintuan["items_yes"] = $items_yes;
                        $items_no = array_filter($pintuan_GPC["items_no"], trim);
                        $pintuan["items_no"] = $items_no;
                    }
                    $seckill_GPC = $_GPC["seckill"];
                    $seckill["type"] = intval($seckill_GPC["type"]);
                    if ($seckill["type"] == 2) {
                        $seckill["fee"] = floatval($seckill_GPC["fee"]);
                    } else {
                        $seckill["fee_rate"] = floatval($seckill_GPC["fee_rate"]);
                        $seckill["fee_min"] = floatval($seckill_GPC["fee_min"]);
                        $items_yes = array_filter($seckill_GPC["items_yes"], trim);
                        if (empty($items_yes)) {
                            imessage(error(-1, "至少选择一项抢购抽成项目"), "", "ajax");
                        }
                        $seckill["items_yes"] = $items_yes;
                        $items_no = array_filter($seckill_GPC["items_no"], trim);
                        $seckill["items_no"] = $items_no;
                    }
                    $gohome = array("kanjia" => $kanjia, "pintuan" => $pintuan, "seckill" => $seckill);
                }
                if (check_plugin_perm("zhunshibao")) {
                    $zhunshibao_GPC = $_GPC["zhunshibao"];
                    $zhunshibao = array("status" => intval($zhunshibao_GPC["status"]), "price_type" => intval($zhunshibao_GPC["price_type"]), "fee_type" => intval($zhunshibao_GPC["fee_type"]));
                    if ($zhunshibao["price_type"] == 1) {
                        $zhunshibao["price"] = floatval($zhunshibao_GPC["price1"]);
                    } else {
                        if ($zhunshibao["price_type"] == 2) {
                            $zhunshibao["price"] = floatval($zhunshibao_GPC["price2"]);
                        }
                    }
                    if ($zhunshibao["fee_type"] == "1") {
                        $rule = $_GPC["rule"];
                    } else {
                        if ($zhunshibao["fee_type"] == "2") {
                            $rule = $_GPC["rule_type_2"];
                        }
                    }
                    if (!empty($rule)) {
                        foreach ($rule["time"] as $key => $val) {
                            if (empty($val)) {
                                continue;
                            }
                            $price = $rule["fee"][$key];
                            if (empty($price)) {
                                continue;
                            }
                            $zhunshibao["rule"][] = array("time" => intval($val), "fee" => floatval($price));
                        }
                    }
                    store_set_data($sid, "zhunshibao", $zhunshibao);
                    $activity = array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => "准时宝", "type" => "zhunshibao", "status" => $zhunshibao["status"]);
                    mload()->model("activity");
                    activity_set($sid, $activity);
                }
                $data = array("uniacid" => $_W["uniacid"], "sid" => $sid, "fee_takeout" => iserializer($fee_takeout), "fee_selfDelivery" => iserializer($fee_selfDelivery), "fee_instore" => iserializer($fee_instore), "fee_paybill" => iserializer($fee_paybill), "fee_goods" => iserializer($fee_goods), "fee_limit" => intval($_GPC["get_cash_fee_limit"]), "fee_rate" => trim($_GPC["get_cash_fee_rate"]) ? trim($_GPC["get_cash_fee_rate"]) : 0, "fee_min" => intval($_GPC["get_cash_fee_min"]), "fee_max" => intval($_GPC["get_cash_fee_max"]), "fee_period" => intval($_GPC["fee_period"]), "fee_gohome" => iserializer($gohome));
                if ($_GPC["fee_eleme"]["fee_type"] == 1) {
                    $data["fee_eleme"] = iserializer(array("fee_type" => 1, "fee_rate" => floatval($_GPC["fee_eleme"]["fee_rate"]), "fee_min" => floatval($_GPC["fee_eleme"]["fee_min"])));
                } else {
                    $data["fee_eleme"] = iserializer(array("fee_type" => 2, "fee" => floatval($_GPC["fee_eleme"]["fee"])));
                }
                if ($_GPC["fee_meituan"]["fee_type"] == 1) {
                    $data["fee_meituan"] = iserializer(array("fee_type" => 1, "fee_rate" => floatval($_GPC["fee_meituan"]["fee_rate"]), "fee_min" => floatval($_GPC["fee_meituan"]["fee_min"])));
                } else {
                    $data["fee_meituan"] = iserializer(array("fee_type" => 2, "fee" => floatval($_GPC["fee_meituan"]["fee"])));
                }
                if (empty($account)) {
                    $data["amount"] = 0;
                    pdo_insert("tiny_wmall_store_account", $data);
                } else {
                    pdo_update("tiny_wmall_store_account", $data, array("uniacid" => $_W["uniacid"], "sid" => $sid));
                }
                $update = array("delivery_mode" => intval($_GPC["delivery_mode"]), "delivery_fee_mode" => intval($_GPC["delivery_fee_mode"]), "auto_get_address" => intval($_GPC["auto_get_address"]), "send_price" => intval($_GPC["send_price_1"]), "delivery_free_price" => intval($_GPC["delivery_free_price_1"]), "delivery_time" => intval($_GPC["delivery_time"]), "serve_radius" => floatval($_GPC["serve_radius"]), "not_in_serve_radius" => intval($_GPC["not_in_serve_radius"]), "delivery_extra" => iserializer(array("store_bear_deliveryprice" => floatval($_GPC["store_bear_deliveryprice"]), "delivery_free_bear" => trim($_GPC["delivery_free_bear"]), "plateform_bear_deliveryprice" => floatval($_GPC["plateform_bear_deliveryprice"]))), "eleme_status" => intval($_GPC["eleme_status"]), "meituan_status" => intval($_GPC["meituan_status"]));
                if ($store["data"]["delivery_time_type"] == 0) {
                    unset($update["delivery_time"]);
                }
                if ($update["delivery_fee_mode"] == 1) {
                    $update["delivery_price"] = trim($_GPC["delivery_price"]);
                    if (!$update["not_in_serve_radius"]) {
                        $update["auto_get_address"] = 1;
                        if (empty($update["serve_radius"])) {
                            imessage(error(-1, "您设置了超出配送费范围不允许下单, 此项设置需要设置门店的的配送半径"), "", "ajax");
                        }
                    }
                } else {
                    if ($update["delivery_fee_mode"] == 2) {
                        $update["auto_get_address"] = 1;
                        $update["not_in_serve_radius"] = intval($_GPC["not_in_serve_radius"]);
                        $update["send_price"] = intval($_GPC["send_price_2"]);
                        $update["delivery_free_price"] = intval($_GPC["delivery_free_price_2"]);
                        if (!empty($_GPC["over_km"]) && $_GPC["over_km"] <= $_GPC["start_km"]) {
                            imessage(error(-1, "设置超出公里加收配送费，距离应大于起步价包含公里数"), "", "ajax");
                        }
                        $update["delivery_price"] = iserializer(array("start_fee" => floatval($_GPC["start_fee"]), "start_km" => floatval($_GPC["start_km"]), "pre_km_fee" => floatval($_GPC["pre_km_fee"]), "over_km" => floatval($_GPC["over_km"]), "over_pre_km_fee" => floatval($_GPC["over_pre_km_fee"]), "max_fee" => floatval($_GPC["max_fee"]), "calculate_distance_type" => intval($_GPC["calculate_distance_type"]), "distance_type" => intval($_GPC["distance_type"])));
                    } else {
                        if ($update["delivery_fee_mode"] == 3) {
                            $update["auto_get_address"] = 1;
                        }
                    }
                }
                $times = array();
                if (!empty($_GPC["times"]["start"])) {
                    foreach ($_GPC["times"]["start"] as $key => $val) {
                        $start = trim($val);
                        $end = trim($_GPC["times"]["end"][$key]);
                        if (empty($start) || empty($end)) {
                            continue;
                        }
                        $times[] = array("start" => $start, "end" => $end, "status" => intval($_GPC["times"]["status"][$key]), "fee" => trim($_GPC["times"]["fee"][$key]));
                    }
                    $update["delivery_times"] = iserializer($times);
                }
                $_GPC["areas"] = str_replace("&nbsp;", "#nbsp;", $_GPC["areas"]);
                $_GPC["areas"] = json_decode(str_replace("#nbsp;", "&nbsp;", html_entity_decode(urldecode($_GPC["areas"]))), true);
                foreach ($_GPC["areas"] as $key => &$val) {
                    if (empty($val["path"])) {
                        unset($_GPC["areas"][$key]);
                    }
                    $path = array();
                    foreach ($val["path"] as $row) {
                        $path[] = array($row["lng"], $row["lat"]);
                    }
                    $val["path"] = $path;
                    unset($val["isAdd"]);
                    unset($val["isActive"]);
                }
                $update["delivery_areas"] = iserializer($_GPC["areas"]);
                $update["self_audit_comment"] = intval($_GPC["self_audit_comment"]);
                if ($update["self_audit_comment"] == 0) {
                    $update["comment_status"] = 1;
                } else {
                    $update["comment_status"] = intval($_GPC["comment_status"]);
                }
                $eleme_delivery = array("delivery_mode" => intval($_GPC["eleme"]["delivery_mode"]), "delivery_fee_mode" => intval($_GPC["eleme"]["delivery_fee_mode"]), "delivery_price" => floatval($_GPC["eleme"]["delivery_price"]));
                if ($eleme_delivery["delivery_fee_mode"] == 2) {
                    $eleme_delivery["delivery_price"] = array("start_fee" => trim($_GPC["eleme"]["start_fee"]), "start_km" => trim($_GPC["eleme"]["start_km"]), "pre_km_fee" => trim($_GPC["eleme"]["pre_km_fee"]));
                }
                store_set_data($sid, "eleme.delivery", $eleme_delivery);
                $meituan_delivery = array("delivery_mode" => intval($_GPC["meituan"]["delivery_mode"]), "delivery_fee_mode" => intval($_GPC["meituan"]["delivery_fee_mode"]), "delivery_price" => floatval($_GPC["meituan"]["delivery_price"]));
                if ($meituan_delivery["delivery_fee_mode"] == 2) {
                    $meituan_delivery["delivery_price"] = array("start_fee" => trim($_GPC["meituan"]["start_fee"]), "start_km" => trim($_GPC["meituan"]["start_km"]), "pre_km_fee" => trim($_GPC["meituan"]["pre_km_fee"]));
                }
                store_set_data($sid, "meituan.delivery", $meituan_delivery);
                $extra_fee = array();
                if (!empty($_GPC["extra"])) {
                    foreach ($_GPC["extra"]["name"] as $key => $value) {
                        $name = trim($value);
                        if (empty($name)) {
                            continue;
                        }
                        $fee = $_GPC["extra"]["fee"][$key];
                        if (empty($fee)) {
                            continue;
                        }
                        $status = intval($_GPC["extra"]["status"][$key]);
                        $extra_fee[] = array("name" => $name, "fee" => $fee, "status" => $status);
                    }
                    store_set_data($sid, "extra_fee", $extra_fee);
                }
                $superCoupon = array("status" => intval($_GPC["superCoupon"]["status"]), "max_limit" => intval($_GPC["superCoupon"]["max_limit"]));
                $goods_rule_price = array("audit_status" => intval($_GPC["audit_status"]), "increase_range" => intval($_GPC["increase_range"]), "time_interval" => intval($_GPC["time_interval"]));
                store_set_data($sid, "superCoupon", $superCoupon);
                pdo_update("tiny_wmall_store", $update, array("uniacid" => $_W["uniacid"], "id" => $sid));
                store_delivery_times($sid, true);
                store_set_data($sid, "custom_goods_sailed_status", intval($_GPC["custom_goods_sailed_status"]));
                store_set_data($sid, "delivery_time_type", intval($_GPC["delivery_time_type"]));
                store_set_data($sid, "pindan", array("pindan_status" => intval($_GPC["pindan_status"])));
                store_set_data($sid, "goods.rule_price", $goods_rule_price);
                store_stat_init("delivery_time", $sid);
                imessage(error(0, "设置门店账户成功"), "refresh", "ajax");
            }
        } else {
            if ($op == "deposit") {
                if ($_W["ispost"]) {
                    $ids = explode(",", $_GPC["id"]);
                    if (empty($ids)) {
                        imessage(error(-1, "请选择需要修改的门店"), "", "ajax");
                    }
                    $deposit = intval($_GPC["deposit"]);
                    $remark = trim($_GPC["remark"]);
                    if ($deposit < 0) {
                        imessage(error(-1, "请输入保证金"), "", "ajax");
                    }
                    if (empty($remark)) {
                        imessage(error(-1, "请输入保证金设置备注"), "", "ajax");
                    }
                    foreach ($ids as $id) {
                        $store_account = pdo_get("tiny_wmall_store_account", array("uniacid" => $_W["uniacid"], "sid" => $id), array("amount", "deposit"));
                        if (!empty($store_account)) {
                            $difference = $deposit - $store_account["deposit"];
                            if ($difference == 0) {
                                continue;
                            }
                            if (0 < $difference) {
                                if ($store_account["amount"] < $difference) {
                                    continue;
                                }
                                $remark .= " 平台保证金增加，系统扣除账户余额";
                            } else {
                                $remark .= " 平台保证金减少，系统增加账户余额";
                            }
                            pdo_update("tiny_wmall_store_account", array("deposit" => $deposit), array("uniacid" => $_W["uniacid"], "sid" => $id));
                            store_update_account($id, 0 - $difference, 7, "", $remark);
                        }
                    }
                    imessage(error(0, "批量设置保证金成功"), iurl("merchant/account/index"), "ajax");
                }
                $ids = $_GPC["id"];
                $ids = implode(",", $ids);
                include itemplate("merchant/accountOp");
                exit;
            }
        }
    }
}
include itemplate("merchant/account");

?>