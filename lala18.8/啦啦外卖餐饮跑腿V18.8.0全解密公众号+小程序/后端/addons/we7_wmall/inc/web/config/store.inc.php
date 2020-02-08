<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "settle";
if ($op == "settle") {
    $_W["page"]["title"] = "商户入驻";
    if ($_W["ispost"]) {
        $settle = array("status" => intval($_GPC["status"]), "audit_status" => intval($_GPC["audit_status"]), "mobile_verify_status" => intval($_GPC["mobile_verify_status"]), "qualification_verify_status" => intval($_GPC["qualification_verify_status"]), "store_label_new" => intval($_GPC["store_label_new"]));
        set_config_text("商户入驻协议", "agreement_settle", htmlspecialchars_decode($_GPC["agreement_settle"]));
        set_system_config("store.settle", $settle);
        imessage(error(0, "商户入驻设置成功"), referer(), "ajax");
    }
    $settle = $_config["store"]["settle"];
    $settle["agreement_settle"] = get_config_text("agreement_settle");
    include itemplate("config/settle");
} else {
    if ($op == "serve_fee") {
        $_W["page"]["title"] = "服务费率";
        $serve_fee = $_config["store"]["serve_fee"];
        if ($_W["ispost"]) {
            $form_type = trim($_GPC["form_type"]);
            if ($form_type == "serve_fee_setting") {
                $takeout_GPC = $_GPC["fee_takeout"];
                $serve_fee["fee_takeout"]["type"] = intval($takeout_GPC["type"]) ? intval($takeout_GPC["type"]) : 1;
                if ($serve_fee["fee_takeout"]["type"] == 2) {
                    $serve_fee["fee_takeout"]["fee"] = floatval($takeout_GPC["fee"]);
                } else {
                    $serve_fee["fee_takeout"]["fee_rate"] = floatval($takeout_GPC["fee_rate"]);
                    $serve_fee["fee_takeout"]["fee_min"] = floatval($takeout_GPC["fee_min"]);
                    $items_yes = array_filter($takeout_GPC["items_yes"], trim);
                    if (empty($items_yes)) {
                    imessage(error(-1, "至少选择一项抽佣项目"), "", "ajax");
                    }
                    $serve_fee["fee_takeout"]["items_yes"] = $items_yes;
                    $items_no = array_filter($takeout_GPC["items_no"], trim);
                    $serve_fee["fee_takeout"]["items_no"] = $items_no;
                }
                $selfDelivery_GPC = $_GPC["fee_selfDelivery"];
                $serve_fee["fee_selfDelivery"]["type"] = intval($selfDelivery_GPC["type"]) ? intval($selfDelivery_GPC["type"]) : 1;
                if ($serve_fee["fee_selfDelivery"]["type"] == 2) {
                    $serve_fee["fee_selfDelivery"]["fee"] = floatval($selfDelivery_GPC["fee"]);
                } else {
                    $serve_fee["fee_selfDelivery"]["fee_rate"] = floatval($selfDelivery_GPC["fee_rate"]);
                    $serve_fee["fee_selfDelivery"]["fee_min"] = floatval($selfDelivery_GPC["fee_min"]);
                    $items_yes = array_filter($selfDelivery_GPC["items_yes"], trim);
                    if (empty($items_yes)) {
                    imessage(error(-1, "至少选择一项抽佣项目"), "", "ajax");
                    }
                    $serve_fee["fee_selfDelivery"]["items_yes"] = $items_yes;
                    $items_no = array_filter($selfDelivery_GPC["items_no"], trim);
                    $serve_fee["fee_selfDelivery"]["items_no"] = $items_no;
                }
                $instore_GPC = $_GPC["fee_instore"];
                $serve_fee["fee_instore"]["type"] = intval($instore_GPC["type"]) ? intval($instore_GPC["type"]) : 1;
                if ($serve_fee["fee_instore"]["type"] == 2) {
                    $serve_fee["fee_instore"]["fee"] = floatval($instore_GPC["fee"]);
                } else {
                    $serve_fee["fee_instore"]["fee_rate"] = floatval($instore_GPC["fee_rate"]);
                    $serve_fee["fee_instore"]["fee_min"] = floatval($instore_GPC["fee_min"]);
                    $items_yes = array_filter($instore_GPC["items_yes"], trim);
                    if (empty($items_yes)) {
                    imessage(error(-1, "至少选择一项抽佣项目"), "", "ajax");
                    }
                    $serve_fee["fee_instore"]["items_yes"] = $items_yes;
                    $items_no = array_filter($instore_GPC["items_no"], trim);
                    $serve_fee["fee_instore"]["items_no"] = $items_no;
                }
                $paybill_GPC = $_GPC["fee_paybill"];
                $serve_fee["fee_paybill"]["type"] = intval($paybill_GPC["type"]) ? intval($paybill_GPC["type"]) : 1;
                if ($serve_fee["fee_paybill"]["type"] == 2) {
                    $serve_fee["fee_paybill"]["fee"] = floatval($paybill_GPC["fee"]);
                } else {
                    $serve_fee["fee_paybill"]["fee_rate"] = floatval($paybill_GPC["fee_rate"]);
                    $serve_fee["fee_paybill"]["fee_min"] = floatval($paybill_GPC["fee_min"]);
                }
            } else {
                if ($form_type == "getcash_fee_setting") {
                    $getcashperiod = intval($_GPC["get_cash_period"]) ? intval($_GPC["get_cash_period"]) : 0;
                    $serve_fee["get_cash_fee_limit"] = intval($_GPC["get_cash_fee_limit"]);
                    $serve_fee["get_cash_fee_rate"] = trim($_GPC["get_cash_fee_rate"]);
                    $serve_fee["get_cash_fee_min"] = intval($_GPC["get_cash_fee_min"]);
                    $serve_fee["get_cash_fee_max"] = intval($_GPC["get_cash_fee_max"]);
                    $serve_fee["fee_period"] = intval($_GPC["fee_period"]);
                    $serve_fee["get_cash_period"] = $getcashperiod;
                }
            }
            unset($serve_fee["sync"]);
            set_system_config("store.serve_fee", $serve_fee);
            $data = array("fee_takeout" => iserializer($serve_fee["fee_takeout"]), "fee_selfDelivery" => iserializer($serve_fee["fee_selfDelivery"]), "fee_instore" => iserializer($serve_fee["fee_instore"]), "fee_paybill" => iserializer($serve_fee["fee_paybill"]), "fee_limit" => $serve_fee["get_cash_fee_limit"], "fee_rate" => $serve_fee["get_cash_fee_rate"], "fee_min" => $serve_fee["get_cash_fee_min"], "fee_max" => $serve_fee["get_cash_fee_max"], "fee_period" => $serve_fee["fee_period"]);
            if ($form_type == "serve_fee_setting") {
                $update = array("fee_takeout" => $data["fee_takeout"], "fee_selfDelivery" => $data["fee_selfDelivery"], "fee_instore" => $data["fee_instore"], "fee_paybill" => $data["fee_paybill"]);
            } else {
                if ($form_type == "getcash_fee_setting") {
                    $update = array("fee_limit" => $data["fee_limit"], "fee_rate" => $data["fee_rate"], "fee_min" => $data["fee_min"], "fee_max" => $data["fee_max"], "fee_period" => $data["fee_period"]);
                }
            }
            $sync = intval($_GPC["sync"]);
            if ($sync == 1) {
                pdo_update("tiny_wmall_store_account", $update, array("uniacid" => $_W["uniacid"]));
            } else {
                if ($sync == 2) {
                    $store_ids = $_GPC["store_ids"];
                    foreach ($store_ids as $storeid) {
                        pdo_update("tiny_wmall_store_account", $update, array("uniacid" => $_W["uniacid"], "sid" => intval($storeid)));
                    }
                }
            }
            imessage(error(0, "商户服务费率设置成功"), referer(), "ajax");
        }
        $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title"));
        include itemplate("config/serve_fee");
    } else {
        if ($op == "delivery") {
            $_W["page"]["title"] = "配送模式";
            $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title"));
            if ($_W["ispost"]) {
                if ($_GPC["form_type"] == "another") {
                    if (empty($_GPC["times"]["start"])) {
                        imessage(error(-1, "请先生成配送时间段"), "", "ajax");
                    }
                    $delivery = array("delivery_mode" => intval($_GPC["delivery_mode"]), "delivery_fee_mode" => intval($_GPC["delivery_fee_mode"]), "delivery_fee" => floatval($_GPC["delivery_fee"]), "pre_delivery_time_minute" => intval($_GPC["pre_delivery_time_minute"]), "send_price" => trim($_GPC["send_price_1"]), "delivery_free_price" => trim($_GPC["delivery_free_price_1"]), "delivery_extra" => array("store_bear_deliveryprice" => floatval($_GPC["store_bear_deliveryprice"]), "delivery_free_bear" => trim($_GPC["delivery_free_bear"]), "plateform_bear_deliveryprice" => floatval($_GPC["plateform_bear_deliveryprice"])));
                    if ($delivery["delivery_fee_mode"] == 2) {
                        $delivery["send_price"] = trim($_GPC["send_price_2"]);
                        $delivery["delivery_free_price"] = trim($_GPC["delivery_free_price_2"]);
                        $delivery["delivery_fee"] = array("start_fee" => floatval($_GPC["start_fee"]), "start_km" => floatval($_GPC["start_km"]), "pre_km_fee" => floatval($_GPC["pre_km_fee"]), "calculate_distance_type" => intval($_GPC["calculate_distance_type"]), "distance_type" => intval($_GPC["distance_type"]), "max_fee" => floatval($_GPC["max_fee"]), "over_km" => floatval($_GPC["over_km"]), "over_pre_km_fee" => floatval($_GPC["over_pre_km_fee"]));
                        if (!empty($_GPC["over_km"]) && $_GPC["over_km"] <= $_GPC["start_km"]) {
                            imessage(error(-1, "设置超出公里加收配送费，距离应大于起步价包含公里数"), "", "ajax");
                        }
                    }
                    set_system_config("store.delivery", $delivery);
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
                    set_config_text("配送时段", "takeout_delivery_time", iserializer($times));
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
                    set_config_text("配送区域", "store_delivery_areas", $delivery_areas);
                    $update = array("delivery_mode" => $delivery["delivery_mode"], "delivery_fee_mode" => $delivery["delivery_fee_mode"], "delivery_price" => $delivery["delivery_fee"], "delivery_free_price" => $delivery["delivery_free_price"], "send_price" => $delivery["send_price"], "delivery_times" => iserializer($times), "delivery_areas" => iserializer($delivery_areas), "delivery_extra" => iserializer($delivery["delivery_extra"]));
                    if ($delivery["delivery_fee_mode"] == 2) {
                        $update["delivery_price"] = iserializer($delivery["delivery_fee"]);
                        $update["not_in_serve_radius"] = 1;
                        $update["auto_get_address"] = 1;
                    }
                    $delivery_sync = intval($_GPC["delivery_sync"]);
                    if ($delivery_sync == 1) {
                        pdo_update("tiny_wmall_store", $update, array("uniacid" => $_W["uniacid"]));
                        foreach ($stores as $store) {
                            store_delivery_times($store["id"], true);
                        }
                    } else {
                        if ($delivery_sync == 2) {
                            $store_ids = $_GPC["store_ids"];
                            foreach ($store_ids as $storeid) {
                                pdo_update("tiny_wmall_store", $update, array("uniacid" => $_W["uniacid"], "id" => intval($storeid)));
                                store_delivery_times($storeid, true);
                            }
                        }
                    }
                    imessage(error(0, "配送模式设置成功"), referer(), "ajax");
                } else {
                    if ($_GPC["form_type"] == "reserve") {
                        $reserve_data = array("rest_can_order" => intval($_GPC["rest_can_order"]), "reserve" => array("reserve_time_limit" => intval($_GPC["reserve"]["reserve_time_limit"]), "notice_clerk_before_delivery" => intval($_GPC["reserve"]["notice_clerk_before_delivery"])));
                        set_system_config("store.reserve", $reserve_data);
                        $update_reserve = array("rest_can_order" => $reserve_data["rest_can_order"]);
                        $reserve_sync = intval($_GPC["reserve_sync"]);
                        if ($reserve_sync == 1) {
                            pdo_update("tiny_wmall_store", $update_reserve, array("uniacid" => $_W["uniacid"]));
                            foreach ($stores as $store) {
                                store_set_data($store["id"], "reserve", $reserve_data["reserve"]);
                            }
                        } else {
                            if ($reserve_sync == 2) {
                                $store_ids = $_GPC["store_ids"];
                                foreach ($store_ids as $storeid) {
                                    pdo_update("tiny_wmall_store", $update_reserve, array("uniacid" => $_W["uniacid"], "id" => $storeid));
                                    store_set_data($storeid, "reserve", $reserve_data["reserve"]);
                                }
                            }
                        }
                        imessage(error(0, "预订单设置成功"), referer(), "ajax");
                    } else {
                        if ($_GPC["form_type"] == "pindan") {
                            $pindan_data = array("pindan_status" => intval($_GPC["pindan_status"]));
                            set_system_config("store.pindan", $pindan_data);
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
                            $delivery_free_price = floatval($_GPC["delivery_free_price_1"]);
                            $update = array("delivery_free_price" => $delivery_free_price);
                            $extra_sync = intval($_GPC["extra_sync"]);
                            if ($extra_sync == "1") {
                                foreach ($stores as $val) {
                                    pdo_update("tiny_wmall_store", $update, array("uniacid" => $_W["uniacid"]));
                                }
                            } else {
                                if ($extra_sync == 2) {
                                    $store_ids = $_GPC["store_ids"];
                                    foreach ($store_ids as $storeid) {
                                        pdo_update("tiny_wmall_store", $update, array("uniacid" => $_W["uniacid"], "id" => intval($storeid)));
                                    }
                                }
                            }
                            imessage(error(0, "配送费设置成功"), referer(), "ajax");
                        }
                    }
                }
            }
            $delivery = $_config["store"]["delivery"];
            $reserve = $_config["store"]["reserve"];
            $pindan = $_config["store"]["pindan"];
            $item = array("isChange" => 1, "delivery_areas" => get_config_text("store_delivery_areas"), "location_y" => $_W["we7_wmall"]["config"]["takeout"]["range"]["map"]["location_y"], "location_x" => $_W["we7_wmall"]["config"]["takeout"]["range"]["map"]["location_x"]);
            $delivery_times = get_config_text("takeout_delivery_time");
            include itemplate("config/delivery");
            return 1;
        } else {
            if ($op == "extra") {
                $_W["page"]["title"] = "其他";
                $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title"), "id");
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
                        set_system_config("store.extra", $extra);
                        store_stat_init("delivery_time", 0);
                    } else {
                        if ($form_type == "takeOrder_setting") {
                            $extra["auto_handel_order"] = intval($_GPC["auto_handel_order"]);
                            $extra["auto_notice_deliveryer"] = intval($_GPC["auto_notice_deliveryer"]);
                            $update = array("auto_notice_deliveryer" => $extra["auto_notice_deliveryer"], "auto_handel_order" => $extra["auto_handel_order"]);
                            set_system_config("store.extra", $extra);
                        } else {
                            if ($form_type == "remind_setting") {
                                $extra["remind_time_start"] = intval($_GPC["remind_time_start"]);
                                $extra["remind_time_limit"] = intval($_GPC["remind_time_limit"]);
                                $update = array("remind_time_start" => $extra["remind_time_start"], "remind_time_limit" => $extra["remind_time_limit"]);
                                set_system_config("store.extra", $extra);
                            } else {
                                if ($form_type == "deliveryprice_setting") {
                                    $extra["delivery_extra"] = iserializer(array("store_bear_deliveryprice" => floatval($_GPC["store_bear_deliveryprice"]), "delivery_free_bear" => trim($_GPC["delivery_free_bear"]), "plateform_bear_deliveryprice" => floatval($_GPC["plateform_bear_deliveryprice"])));
                                    $update = array("delivery_extra" => $extra["delivery_extra"]);
                                    set_system_config("store.extra", $extra);
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
                                        set_system_config("store.extra", $extra);
                                    } else {
                                        if ($form_type == "goods_setting") {
                                            $goods_rule_price = array("audit_status" => intval($_GPC["audit_status"]), "increase_range" => intval($_GPC["increase_range"]), "time_interval" => intval($_GPC["time_interval"]));
                                            $extra["goods_rule_price"] = $goods_rule_price;
                                            set_system_config("store.extra", $extra);
                                        } else {
                                            $template = intval($_GPC["template"]);
                                            $template_page = array("wxapp" => intval($_GPC["template_page"]["wxapp"]), "vue" => intval($_GPC["template_page"]["vue"]));
                                            $extra_sync = intval($_GPC["extra_sync"]);
                                            if ($extra_sync == "1") {
                                                foreach ($stores as $val) {
                                                    store_set_data($val["id"], "wxapp.template", $template);
                                                    store_set_data($val["id"], "wxapp.template_page", $template_page);
                                                }
                                            } else {
                                                if ($extra_sync == 2) {
                                                    $store_ids = $_GPC["store_ids"];
                                                    foreach ($store_ids as $storeid) {
                                                        store_set_data($storeid, "wxapp.template", $template);
                                                        store_set_data($storeid, "wxapp.template_page", $template_page);
                                                    }
                                                }
                                            }
                                            imessage(error(0, "模板配置成功"), referer(), "ajax");
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $extra_sync = intval($_GPC["extra_sync"]);
                    if ($extra_sync == 1) {
                        if (!empty($update)) {
                            pdo_update("tiny_wmall_store", $update, array("uniacid" => $_W["uniacid"]));
                        }
                        $store_ids = array_keys($stores);
                    } else {
                        if ($extra_sync == 2) {
                            $store_ids = $_GPC["store_ids"];
                            if (!empty($update)) {
                                foreach ($store_ids as $storeid) {
                                    pdo_update("tiny_wmall_store", $update, array("uniacid" => $_W["uniacid"], "id" => $storeid));
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
                return 1;
            } else {
                if ($op == "activity") {
                    $_W["page"]["title"] = "商户活动";
                    $activitys = store_all_activity();
                    if (isset($activitys["svipRedpacket"])) {
                        unset($activitys["svipRedpacket"]);
                    }
                    if (isset($activitys["zhunshibao"])) {
                        unset($activitys["zhunshibao"]);
                    }
                    if ($_W["ispost"]) {
                        $data = array();
                        foreach ($_GPC["activity"]["status"] as $type => $val) {
                            $data[$type] = array("status" => intval($val), "cancel_status" => intval($_GPC["activity"]["cancel_status"][$type]));
                        }
                        set_system_config("store.activity.perm", $data);
                        imessage(error(0, "设置商户活动成功"), referer(), "ajax");
                    }
                    $config_activity = get_system_config("store.activity.perm");
                    include itemplate("config/storeActivity");
                    return 1;
                } else {
                    if ($op == "recommend") {
                        $_W["page"]["title"] = "为您优选";
                        if ($_W["ispost"]) {
                            $recommend = array("image" => trim($_GPC["selective_image"]));
                            set_system_config("selective", $recommend);
                            imessage(error(0, "设置为您优选图片成功"), referer(), "ajax");
                        }
                        $recommend = get_system_config("selective");
                        include itemplate("config/recommend");
                    }
                }
            }
        }
    }
}

?>