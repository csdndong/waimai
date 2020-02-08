<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $condition = " where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and order_type <= 2 and stat_day = :stat_day";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":stat_day" => date("Ymd"));
    $stat["total_order"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . $condition, $params));
    $stat["total_fee"] = floatval(pdo_fetchcolumn("select round(sum(final_fee), 2) from " . tablename("tiny_wmall_order") . $condition, $params));
    $stat["final_fee"] = floatval(pdo_fetchcolumn("select round(sum(store_final_fee), 2) from " . tablename("tiny_wmall_order") . $condition, $params));
    $notice = store_notice_stat($_W["manager"]["id"]);
    $ads = pdo_fetchall("select * from " . tablename("tiny_wmall_slide") . " where uniacid = :uniacid and type = 3 and status = 1 order by displayorder desc", array(":uniacid" => $_W["uniacid"]));
    $poster = get_plugin_config("poster.store");
    $advertise = get_plugin_config("advertise.basic");
    $store = store_fetch($sid);
    $store["advertise_status"] = $advertise["status"];
    $store["poster_status"] = $poster["status"];
    $order_notice_num = get_available_wxapp_formid($_W["openid_wxapp"]);
    $result = array("store" => $store, "manager" => $_W["manager"], "stat" => $stat, "notice" => $notice, "ads" => $ads, "order_notice_num" => $order_notice_num);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "newindex") {
        $condition = " where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and stat_day = :stat_day";
        $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":stat_day" => date("Ymd"));
        $stat["total_order"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . $condition, $params));
        $stat["final_fee"] = floatval(pdo_fetchcolumn("select round(sum(store_final_fee), 2) from " . tablename("tiny_wmall_order") . $condition, $params));
        $store = store_fetch($sid);
        $ads = pdo_fetchall("select * from " . tablename("tiny_wmall_slide") . " where uniacid = :uniacid and type = 3 and status = 1 order by displayorder desc", array(":uniacid" => $_W["uniacid"]));
        $news = pdo_fetchall("select a.*, b.title as btitle from " . tablename("tiny_wmall_news") . " as a left join" . tablename("tiny_wmall_news_category") . " as b on a.cateid = b.id where a.uniacid = :uniacid order by id desc limit 5 ", array(":uniacid" => $_W["uniacid"]));
        if (!empty($news)) {
            foreach ($news as &$val) {
                $val["thumb"] = tomedia($val["thumb"]);
            }
        }
        $result = array("store" => $store, "stat" => $stat, "news" => $news, "ads" => $ads);
        imessage(error(0, $result), "", "ajax");
        return 1;
    } else {
        if ($ta == "info") {
            $type = trim($_GPC["type"]);
            if (empty($type)) {
                $filter = array("title", "address", "cid", "logo", "business_hours", "telephone", "notice", "content", "thumbs", "haodian_cid", "haodian_child_id", "haodian_data");
            } else {
                if ($type == "haodian_category") {
                    $filter = array("haodian_cid", "haodian_child_id");
                } else {
                    if ($type == "haodian_tags") {
                        $filter = array("haodian_data");
                    } else {
                        $filter = array($type);
                    }
                }
            }
            $store = store_fetch($sid, $filter);
            $store["category_cn"] = tocategory($store["cid"]);
            $result = array("store" => $store);
            if ($type == "haodian_category") {
                $haodian_category = pdo_fetchall("select id, title, parentid from " . tablename("tiny_wmall_haodian_category") . " where uniacid = :uniacid and agentid = :agentid and status = 1", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]), "id");
                if (!empty($haodian_category)) {
                    foreach ($haodian_category as &$val) {
                        if (!empty($val["parentid"])) {
                            $haodian_category[$val["parentid"]]["child"][] = $val;
                            unset($haodian_category[$val["id"]]);
                        }
                    }
                }
                $result["haodian_category"] = array_values($haodian_category);
            }
            imessage(error(0, $result), "", "ajax");
            return 1;
        } else {
            if ($ta == "setting") {
                $type = trim($_GPC["type"]);
                $value = json_decode(htmlspecialchars_decode($_GPC["value"]), true);
                if (!empty($type)) {
                    if (in_array($type, array("title", "address", "telephone")) && empty($value)) {
                    imessage(error(-1, "信息不能为空"), "", "ajax");
                    }
                    if (in_array($type, array("business_hours", "thumbs", "qualification"))) {
                        $value = iserializer($value);
                    }
                    if ($type == "haodian_category") {
                        $updata = array_map("intval", $value);
                    } else {
                        if ($type == "haodian_tags") {
                            mload()->model("plugin");
                            pload()->model("haodian");
                            $value = array_map("trim", $value);
                            haodian_set_data($sid, "tags", $value);
                        imessage(error(0, "好店标签设置成功"), "", "ajax");
                        } else {
                            $updata = array($type => $value);
                        }
                    }
                    pdo_update("tiny_wmall_store", $updata, array("uniacid" => $_W["uniacid"], "id" => $sid));
                imessage(error(0, "门店信息设置成功"), "", "ajax");
                } else {
                imessage(error(-1, "参数错误"), "", "ajax");
                }
            } else {
                if ($ta == "logo") {
                    $logo = trim($_GPC["logo"]);
                    if (empty($logo)) {
                    imessage(error(-1, "请上传门店Logo"), "", "ajax");
                    }
                    pdo_update("tiny_wmall_store", array("logo" => $logo), array("uniacid" => $_W["uniacid"], "id" => $sid));
                imessage(error(0, "门店Logo设置成功"), "", "ajax");
                } else {
                    if ($ta == "status") {
                        $type = trim($_GPC["type"]);
                        $value = intval($_GPC["value"]);
                        if (in_array($type, array("is_in_business", "auto_handel_order", "auto_notice_deliveryer"))) {
                            $update = array($type => $value);
                            pdo_update("tiny_wmall_store", $update, array("uniacid" => $_W["uniacid"], "id" => $sid));
                            store_business_hours_init($sid);
                            if (in_array($type, array("is_in_business"))) {
                                mlog(2012, $sid);
                            }
                        } else {
                            if (in_array($type, array("accept_wechat_notice", "accept_voice_notice"))) {
                                $result = clerk_set_extra($type, $value, $_W["manager"]["id"]);
                                if (is_error($result)) {
                                    imessage($result, "", "ajax");
                                }
                            }
                        }
                    imessage(error(0, "设置成功"), "", "ajax");
                    } else {
                        if ($ta == "account") {
                            if ($_W["ispost"]) {
                                $store = json_decode(htmlspecialchars_decode($_GPC["store"]), true);
                                $update = array("delivery_type" => intval($store["delivery_type"]), "delivery_within_days" => intval($store["delivery_within_days"]), "delivery_reserve_days" => intval($store["delivery_reserve_days"]), "pack_price" => floatval($store["pack_price"]));
                                $delivery_mode = intval($store["delivery_mode"]);
                                if ($delivery_mode == 1) {
                                    $update["delivery_fee_mode"] = intval($store["delivery_fee_mode"]);
                                    $update["delivery_free_price"] = floatval($store["delivery_free_price"]);
                                    $update["delivery_time"] = intval($store["delivery_time"]);
                                    $update["auto_get_address"] = intval($store["auto_get_address"]);
                                    $update["not_in_serve_radius"] = intval($store["not_in_serve_radius"]);
                                    $update["delivery_area"] = trim($store["delivery_area"]);
                                    $update["serve_radius"] = floatval($store["serve_radius"]);
                                    $update["send_price"] = floatval($store["send_price"]);
                                    if ($update["delivery_fee_mode"] == 1) {
                                        $update["delivery_price"] = floatval($store["delivery_price"]);
                                    } else {
                                        if ($update["delivery_fee_mode"] == 2) {
                                            $delivery_price = array("start_fee" => floatval($store["delivery_price_extra"]["start_fee"]), "start_km" => floatval($store["delivery_price_extra"]["start_km"]), "pre_km_fee" => floatval($store["delivery_price_extra"]["pre_km_fee"]), "over_km" => floatval($store["delivery_price_extra"]["over_km"]), "over_pre_km_fee" => floatval($store["delivery_price_extra"]["over_pre_km_fee"]), "max_fee" => floatval($store["delivery_price_extra"]["max_fee"]), "calculate_distance_type" => intval($store["delivery_price_extra"]["calculate_distance_type"]), "distance_type" => intval($store["delivery_price_extra"]["distance_type"]));
                                            $update["delivery_price"] = iserializer($delivery_price);
                                        } else {
                                            if ($update["delivery_fee_mode"] == 3) {
                                                $update["auto_get_address"] = 1;
                                            }
                                        }
                                    }
                                    if (empty($update["not_in_serve_radius"])) {
                                        $update["auto_get_address"] = 1;
                                        if (empty($update["serve_radius"])) {
                                        imessage(error(-1, "您设置了超出配送费范围不允许下单, 此项设置需要设置门店的的配送半径"), "", "ajax");
                                        }
                                    }
                                }
                                pdo_update("tiny_wmall_store", $update, array("uniacid" => $_W["uniacid"], "id" => $sid));
                            imessage(error(0, "账户信息设置成功"), "", "ajax");
                            }
                            $store = store_fetch($sid);
                            if (empty($store)) {
                            imessage(error(-1, "门店不存在", 0, "ajax"));
                            }
                            $result = array("store" => $store);
                            imessage(error(0, $result), "", "ajax");
                        } else {
                            if ($ta == "pill") {
                                if ($_W["ispost"]) {
                                    $store = json_decode(htmlspecialchars_decode($_GPC["store"]), true);
                                    $update = array("auto_handel_order" => intval($store["auto_handel_order"]), "auto_print_order" => intval($store["auto_handel_order"]) == 2 ? 0 : intval($store["auto_print_order"]), "auto_notice_deliveryer" => intval($store["auto_notice_deliveryer"]), "invoice_status" => intval($store["invoice_status"]), "token_status" => intval($store["token_status"]), "payment" => iserializer(array_map("trim", $store["payment"])));
                                    pdo_update("tiny_wmall_store", $update, array("uniacid" => $_W["uniacid"], "id" => $sid));
                                imessage(error(0, "支付方式设置成功"), "", "ajax");
                                }
                                $store = store_fetch($sid, array("auto_handel_order", "auto_print_order", "auto_notice_deliveryer", "invoice_status", "token_status", "payment"));
                                $store["auto_print_order"] = strval($store["auto_print_order"]);
                                if (empty($store)) {
                                imessage(error(-1, "门店不存在", 0, "ajax"));
                                }
                                $payments = get_available_payment("", 0, "all");
                                if (empty($payments)) {
                                imessage("公众号没有设置支付方式,请先设置支付方式", referer(), "info");
                                }
                                $result = array("store" => $store, "payments" => $payments);
                                imessage(error(0, $result), "", "ajax");
                            } else {
                                if ($ta == "slog") {
                                    $type = trim($_GPC["type"]);
                                    $title = trim($_GPC["title"]);
                                    $message = trim($_GPC["message"]);
                                    if (!empty($type) && !empty($message)) {
                                        slog($type, (string) $title . ":" . $_W["manager"]["title"], "", $message);
                                    }
                                    imessage(error(0, ""), "", "ajax");
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

?>
