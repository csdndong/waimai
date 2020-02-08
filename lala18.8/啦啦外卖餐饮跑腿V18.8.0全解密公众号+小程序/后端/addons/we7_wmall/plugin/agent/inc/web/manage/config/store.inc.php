<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "settle";
$_config = get_agent_system_config();
if ($op == "serve_fee") {
    $_W["page"]["title"] = "服务费率";
    $serve_fee = $_config["store"]["serve_fee"];
    if ($_W["ispost"]) {
        $fee_takeout = $serve_fee["fee_takeout"];
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
        $fee_selfDelivery = $serve_fee["fee_selfDelivery"];
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
        $fee_instore = $serve_fee["fee_instore"];
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
        $fee_paybill = $serve_fee["fee_paybill"];
        $paybill_GPC = $_GPC["fee_paybill"];
        $fee_paybill["type"] = intval($paybill_GPC["type"]) ? intval($paybill_GPC["type"]) : 1;
        if ($fee_paybill["type"] == 2) {
            $fee_paybill["fee"] = floatval($paybill_GPC["fee"]);
        } else {
            $fee_paybill["fee_rate"] = floatval($paybill_GPC["fee_rate"]);
            $fee_paybill["fee_min"] = floatval($paybill_GPC["fee_min"]);
        }
        $serve_fee = array("fee_takeout" => $fee_takeout, "fee_selfDelivery" => $fee_selfDelivery, "fee_instore" => $fee_instore, "fee_paybill" => $fee_paybill);
        set_agent_system_config("store.serve_fee", $serve_fee);
        $sync = intval($_GPC["sync"]);
        if ($sync == 1) {
            $update = array("fee_takeout" => iserializer($fee_takeout), "fee_selfDelivery" => iserializer($fee_selfDelivery), "fee_instore" => iserializer($fee_instore), "fee_paybill" => iserializer($fee_paybill));
            pdo_update("tiny_wmall_store_account", $update, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"]));
        }
        imessage(error(0, "商户服务费率设置成功"), referer(), "ajax");
    }
    $serve_fee = $_config["store"]["serve_fee"];
    include itemplate("config/serve_fee");
}
if ($op == "delivery") {
    $_W["page"]["title"] = "配送模式";
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"]), array("id", "title"));
    if ($_W["ispost"]) {
        if ($_GPC["form_type"] == "pindan") {
            $pindan_data = array("pindan_status" => intval($_GPC["pindan_status"]));
            set_agent_system_config("store.pindan", $pindan_data);
            $pindan_sync = intval($_GPC["pindan_sync"]);
            if ($pindan_sync == 1) {
                foreach ($stores as $store) {
                    store_set_data($store["id"], "pindan", $pindan_data);
                }
            } else {
                if ($pindan_sync == 2) {
                    $store_ids = $_GPC["store_ids"];
                    foreach ($store_ids as $storeid) {
                        store_set_data($storeid, "pindan", $pindan_data);
                    }
                }
            }
            imessage(error(0, "拼单设置成功"), referer(), "ajax");
        } else {
            if (empty($_GPC["times"]["start"])) {
                imessage(error(-1, "请先生成配送时间段"), "", "ajax");
            }
            $delivery = array("delivery_mode" => intval($_GPC["delivery_mode"]), "delivery_fee_mode" => intval($_GPC["delivery_fee_mode"]), "delivery_fee" => floatval($_GPC["delivery_fee"]), "pre_delivery_time_minute" => intval($_GPC["pre_delivery_time_minute"]), "send_price" => trim($_GPC["send_price_1"]), "delivery_free_price" => trim($_GPC["delivery_free_price_1"]), "delivery_extra" => array("store_bear_deliveryprice" => floatval($_GPC["store_bear_deliveryprice"]), "delivery_free_bear" => trim($_GPC["delivery_free_bear"]), "plateform_bear_deliveryprice" => floatval($_GPC["plateform_bear_deliveryprice"])));
            if ($delivery["delivery_fee_mode"] == 2) {
                $delivery["send_price"] = floatval($_GPC["send_price_2"]);
                $delivery["delivery_free_price"] = floatval($_GPC["delivery_free_price_2"]);
                $delivery["delivery_fee"] = array("start_fee" => floatval($_GPC["start_fee"]), "start_km" => floatval($_GPC["start_km"]), "pre_km_fee" => floatval($_GPC["pre_km_fee"]), "distance_type" => intval($_GPC["distance_type"]), "calculate_distance_type" => intval($_GPC["calculate_distance_type"]), "max_fee" => floatval($_GPC["max_fee"]), "over_km" => floatval($_GPC["over_km"]), "over_pre_km_fee" => floatval($_GPC["over_pre_km_fee"]));
                if (!empty($_GPC["over_km"]) && $_GPC["over_km"] <= $_GPC["start_km"]) {
                    imessage(error(-1, "设置超出公里加收配送费，距离应大于起步价包含公里数"), "", "ajax");
                }
            }
            set_agent_system_config("store.delivery", $delivery);
            $times = array();
            if (!empty($_GPC["times"]["start"])) {
                foreach ($_GPC["times"]["start"] as $key => $val) {
                    $start = trim($val);
                    $end = trim($_GPC["times"]["end"][$key]);
                    if (empty($start) || empty($end)) {
                        continue;
                    }
                    $times[] = array("start" => $start, "end" => $end, "status" => intval($_GPC["times"]["status"][$key]), "fee" => intval($_GPC["times"]["fee"][$key]));
                }
            }
            set_agent_config_text("代理配送时间段", "takeout_delivery_time", iserializer($times));
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
            $delivery_areas = $_GPC["areas"];
            set_agent_config_text("代理配送区域", "store_delivery_areas", $delivery_areas);
            $delivery_sync = intval($_GPC["delivery_sync"]);
            $update = array("delivery_mode" => $delivery["delivery_mode"], "delivery_fee_mode" => $delivery["delivery_fee_mode"], "delivery_price" => $delivery["delivery_fee"], "send_price" => $delivery["send_price"], "delivery_free_price" => $delivery["delivery_free_price"], "delivery_times" => iserializer($times), "delivery_areas" => iserializer($delivery_areas), "delivery_extra" => iserializer($delivery["delivery_extra"]));
            if ($delivery["delivery_fee_mode"] == 2) {
                $update["delivery_price"] = iserializer($delivery["delivery_fee"]);
                $update["not_in_serve_radius"] = 1;
                $update["auto_get_address"] = 1;
            }
            if ($delivery_sync == 1) {
                pdo_update("tiny_wmall_store", $update, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"]));
                foreach ($stores as $store) {
                    store_delivery_times($store["id"], true);
                }
            } else {
                if ($delivery_sync == 2) {
                    $store_ids = $_GPC["store_ids"];
                    foreach ($store_ids as $storeid) {
                        pdo_update("tiny_wmall_store", $update, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => intval($storeid)));
                        store_delivery_times($storeid, true);
                    }
                }
            }
            imessage(error(0, "配送模式设置成功"), referer(), "ajax");
        }
    }
    $delivery = $_config["store"]["delivery"];
    $pindan = $_config["store"]["pindan"];
    $item = array("isChange" => 1, "delivery_areas" => get_agent_config_text("store_delivery_areas"), "location_y" => $_W["we7_wmall"]["config"]["takeout"]["range"]["map"]["location_y"], "location_x" => $_W["we7_wmall"]["config"]["takeout"]["range"]["map"]["location_x"]);
    $delivery_times = get_agent_config_text("takeout_delivery_time");
    include itemplate("config/delivery");
}
if ($op == "extra") {
    $_W["page"]["title"] = "其他";
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"]), array("id", "title"), "id");
    if ($_W["ispost"]) {
        $extra = $_config["store"]["extra"];
        $form_type = trim($_GPC["form_type"]);
        if ($form_type == "another_setting") {
            $extra["payment"] = $_GPC["payment"];
            $extra["custom_goods_sailed_status"] = intval($_GPC["custom_goods_sailed_status"]);
            $extra["self_audit_comment"] = intval($_GPC["self_audit_comment"]);
            $extra["delivery_time_type"] = intval($_GPC["delivery_time_type"]);
            if (!$extra["self_audit_comment"]) {
                $extra["comment_status"] = 1;
            } else {
                $extra["comment_status"] = intval($_GPC["comment_status"]);
            }
            $update = array("payment" => iserializer($extra["payment"]), "self_audit_comment" => $extra["self_audit_comment"], "comment_status" => $extra["comment_status"]);
            set_agent_system_config("store.extra", $extra);
            store_stat_init("delivery_time", 0);
        } else {
            if ($form_type == "takeOrder_setting") {
                $extra["auto_handel_order"] = intval($_GPC["auto_handel_order"]);
                $extra["auto_notice_deliveryer"] = intval($_GPC["auto_notice_deliveryer"]);
                $update = array("auto_notice_deliveryer" => $extra["auto_notice_deliveryer"], "auto_handel_order" => $extra["auto_handel_order"]);
                set_agent_system_config("store.extra", $extra);
            } else {
                if ($form_type == "remind_setting") {
                    $extra["remind_time_start"] = intval($_GPC["remind_time_start"]);
                    $extra["remind_time_limit"] = intval($_GPC["remind_time_limit"]);
                    $update = array("remind_time_start" => intval($_GPC["remind_time_start"]), "remind_time_limit" => intval($_GPC["remind_time_limit"]));
                    set_agent_system_config("store.extra", $extra);
                } else {
                    if ($form_type == "deliveryprice_setting") {
                        $extra["delivery_extra"] = iserializer(array("store_bear_deliveryprice" => floatval($_GPC["store_bear_deliveryprice"]), "delivery_free_bear" => trim($_GPC["delivery_free_bear"]), "plateform_bear_deliveryprice" => floatval($_GPC["plateform_bear_deliveryprice"])));
                        $update = array("delivery_extra" => $extra["delivery_extra"]);
                        set_agent_system_config("store.extra", $extra);
                    } else {
                        if ($form_type == "extra_setting") {
                            $extra["extra_fee"] = array();
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
                                    $extra["extra_fee"][] = array("name" => $name, "fee" => $fee, "status" => $status);
                                }
                            }
                            set_agent_system_config("store.extra", $extra);
                        } else {
                            if ($form_type == "goods_setting") {
                                $goods_rule_price = array("audit_status" => intval($_GPC["audit_status"]), "increase_range" => intval($_GPC["increase_range"]), "time_interval" => intval($_GPC["time_interval"]));
                                $extra["goods_rule_price"] = $goods_rule_price;
                                set_agent_system_config("store.extra", $extra);
                            }
                        }
                    }
                }
            }
        }
        $extra_sync = intval($_GPC["extra_sync"]);
        if ($extra_sync == 1) {
            if (!empty($update)) {
                pdo_update("tiny_wmall_store", $update, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"]));
            }
            $store_ids = array_keys($stores);
        } else {
            if ($extra_sync == 2) {
                $store_ids = $_GPC["store_ids"];
                if (!empty($update)) {
                    foreach ($store_ids as $storeid) {
                        pdo_update("tiny_wmall_store", $update, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $storeid));
                    }
                }
            }
        }
        if ($form_type == "another_setting") {
            foreach ($store_ids as $storeid) {
                store_set_data($storeid, "custom_goods_sailed_status", $extra["custom_goods_sailed_status"]);
                store_set_data($storeid, "delivery_time_type", $extra["delivery_time_type"]);
            }
        } else {
            if ($form_type == "extra_setting") {
                foreach ($store_ids as $storeid) {
                    store_set_data($storeid, "extra_fee", $extra["extra_fee"]);
                }
            } else {
                if ($form_type == "goods_setting") {
                    foreach ($store_ids as $storeid) {
                        store_set_data($storeid, "goods.rule_price", $goods_rule_price);
                    }
                }
            }
        }
        imessage(error(0, "设置成功"), referer(), "ajax");
    }
    $payments = get_available_payment("", "", true);
    $extra = $_config["store"]["extra"];
    $extra["delivery_extra"] = iunserializer($extra["delivery_extra"]);
    include itemplate("config/storeExtra");
}

?>