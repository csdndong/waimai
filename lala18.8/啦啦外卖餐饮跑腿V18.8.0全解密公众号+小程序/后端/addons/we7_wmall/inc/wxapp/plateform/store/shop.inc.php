<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "setting";
if ($ta == "setting") {
    $id = intval($_GPC["id"]);
    $type = trim($_GPC["type"]);
    if ($_W["ispost"]) {
        $value = json_decode(htmlspecialchars_decode($_GPC["value"]), true);
        if (!empty($type)) {
            if (in_array($type, array("title", "address", "telephone")) && empty($value)) {
                imessage(error(-1, "信息不能为空"), "", "ajax");
            }
            $updata = array();
            if ($type == "cid") {
                $cids = array();
                if (!empty($value)) {
                    foreach ($value as $key => $cid) {
                        $cid = intval($cid);
                        if (!empty($key) && in_array($key, array("cate_parentid1", "cate_childid1", "cate_parentid2", "cate_childid2"))) {
                            $cids[] = $cid;
                            $updata[$key] = $cid;
                        }
                    }
                }
                $value = implode("|", $cids);
                if (!empty($_W["ismanager"]) || !empty($_W["isoperator"]) || !empty($_W["isagenter"])) {
                    $value = "|" . $value . "|";
                }
            } else {
                if ($type == "business_hours") {
                    $hour = array();
                    foreach ($value as $k => $v) {
                        if (empty($v["s"]) || empty($v["e"])) {
                            continue;
                        }
                        $starttime = date("H:i", strtotime(trim($v["s"])));
                        $endtime = date("H:i", strtotime(trim($v["e"])));
                        $hour[] = array("s" => $starttime, "e" => $endtime);
                    }
                    $value = iserializer($hour);
                } else {
                    if ($type == "thumbs" || $type == "qualification") {
                        $value = iserializer($value);
                    }
                }
            }
            $updata[$type] = $value;
            pdo_update("tiny_wmall_store", $updata, array("uniacid" => $_W["uniacid"], "id" => $id));
            imessage(error(0, "门店信息设置成功"), "", "ajax");
        } else {
            imessage(error(-1, "参数错误"), "", "ajax");
        }
    }
    $filter = empty($type) ? array("title", "address", "cid", "logo", "business_hours", "telephone", "notice", "content", "thumbs") : array($type);
    $store = store_fetch($id, $filter);
    if (empty($store)) {
        imessage(error(-1, "门店不存在", 0, "ajax"));
    }
    $result = array("store" => $store);
    if ($type == "cid") {
        $result["categorys"] = store_fetchall_category("parent_child", array("is_sys" => 1));
        $result["categorys"] = array_values($result["categorys"]);
    }
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "account") {
        $id = intval($_GPC["id"]);
        if ($_W["ispost"]) {
            $store = json_decode(htmlspecialchars_decode($_GPC["store"]), true);
            $update = array("delivery_type" => intval($store["delivery_type"]), "delivery_within_days" => intval($store["delivery_within_days"]), "delivery_reserve_days" => intval($store["delivery_reserve_days"]), "serve_radius" => floatval($store["serve_radius"]), "delivery_area" => trim($store["delivery_area"]), "not_in_serve_radius" => intval($store["not_in_serve_radius"]), "auto_get_address" => intval($store["auto_get_address"]), "delivery_time" => intval($store["delivery_time"]), "pack_price" => floatval($store["pack_price"]), "delivery_fee_mode" => intval($store["delivery_fee_mode"]), "send_price" => floatval($store["send_price"]), "delivery_free_price" => floatval($store["delivery_free_price"]));
            $delivery_extra = array("store_bear_deliveryprice" => floatval($store["delivery_extra"]["store_bear_deliveryprice"]), "plateform_bear_deliveryprice" => floatval($store["delivery_extra"]["plateform_bear_deliveryprice"]), "delivery_free_bear" => trim($store["delivery_extra"]["delivery_free_bear"]));
            $update["delivery_extra"] = iserializer($delivery_extra);
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
            pdo_update("tiny_wmall_store", $update, array("uniacid" => $_W["uniacid"], "id" => $id));
            imessage(error(0, "账户信息设置成功"), "", "ajax");
        }
        $store = store_fetch($id, array());
        if (empty($store)) {
            imessage(error(-1, "门店不存在", 0, "ajax"));
        }
        $result = array("store" => $store);
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($ta == "pill") {
            $id = intval($_GPC["id"]);
            if ($_W["ispost"]) {
                $store = json_decode(htmlspecialchars_decode($_GPC["store"]), true);
                $update = array("auto_handel_order" => intval($store["auto_handel_order"]), "auto_notice_deliveryer" => intval($store["auto_notice_deliveryer"]), "invoice_status" => intval($store["invoice_status"]), "token_status" => intval($store["token_status"]), "payment" => iserializer(array_map("trim", $store["payment"])));
                pdo_update("tiny_wmall_store"), $update, array("uniacid" => $_W["uniacid"], "id" => $id));
                imessage(error(0, "支付方式设置成功"), "", "ajax");
            }
            $store = store_fetch($id, array("auto_handel_order", "auto_notice_deliveryer", "invoice_status", "token_status", "payment"));
            if (empty($store)) {
                imessage(error(-1, "门店不存在", 0, "ajax"));
            }
            $pay = get_available_payment();
            if (empty($pay)) {
                imessage("公众号没有设置支付方式,请先设置支付方式", referer(), "info");
            }
            $result = array("store" => $store, "pay" => $pay);
            imessage(error(0, $result), "", "ajax");
        } else {
            if ($ta == "fee") {
                $id = intval($_GPC["id"]);
                $store = store_fetch($id, array("data"));
                if (empty($store)) {
                    imessage(error(-1, "门店不存在", 0, "ajax"));
                }
                $account = store_account($id);
                if (empty($account)) {
                    imessage(error(-1, "门店账户不存在", 0, "ajax"));
                }
                if ($_W["ispost"]) {
                    $account = json_decode(htmlspecialchars_decode($_GPC["account"]), true);
                    $fee_takeout = array();
                    $fee_takeout["type"] = in_array(intval($account["fee_takeout"]["type"]), array(1, 2)) ? intval($account["fee_takeout"]["type"]) : 1;
                    if ($fee_takeout["type"] == 1) {
                        $fee_takeout["fee_rate"] = floatval($account["fee_takeout"]["fee_rate"]);
                        $fee_takeout["fee_min"] = floatval($account["fee_takeout"]["fee_min"]);
                        $fee_takeout["items_yes"] = array_map("trim", $account["fee_takeout"]["items_yes"]);
                        if (empty($fee_takeout["items_yes"])) {
                            imessage(error(-1, "至少选择一项抽佣项目"), "", "ajax");
                        }
                        $fee_takeout["items_no"] = array_map("trim", $account["fee_takeout"]["items_no"]);
                    } else {
                        if ($fee_takeout["type"] == 2) {
                            $fee_takeout["fee"] = floatval($account["fee_takeout"]["fee"]);
                        }
                    }
                    $fee_selfDelivery = array();
                    $fee_selfDelivery["type"] = in_array(intval($account["fee_selfDelivery"]["type"]), array(1, 2)) ? intval($account["fee_selfDelivery"]["type"]) : 1;
                    if ($fee_selfDelivery["type"] == 1) {
                        $fee_selfDelivery["fee_rate"] = floatval($account["fee_selfDelivery"]["fee_rate"]);
                        $fee_selfDelivery["fee_min"] = floatval($account["fee_selfDelivery"]["fee_min"]);
                        $fee_selfDelivery["items_yes"] = array_map("trim", $account["fee_selfDelivery"]["items_yes"]);
                        if (empty($fee_selfDelivery["items_yes"])) {
                            imessage(error(-1, "至少选择一项抽佣项目"), "", "ajax");
                        }
                        $fee_selfDelivery["items_no"] = array_map("trim", $account["fee_selfDelivery"]["items_no"]);
                    } else {
                        if ($fee_selfDelivery["type"] == 2) {
                            $fee_selfDelivery["fee"] = floatval($account["fee_selfDelivery"]["fee"]);
                        }
                    }
                    $fee_instore = array();
                    $fee_instore["type"] = in_array(intval($account["fee_instore"]["type"]), array(1, 2)) ? intval($account["fee_instore"]["type"]) : 1;
                    if ($fee_instore["type"] == 1) {
                        $fee_instore["fee_rate"] = floatval($account["fee_instore"]["fee_rate"]);
                        $fee_instore["fee_min"] = floatval($account["fee_instore"]["fee_min"]);
                        $fee_instore["items_yes"] = array_map("trim", $account["fee_instore"]["items_yes"]);
                        if (empty($fee_instore["items_yes"])) {
                            imessage(error(-1, "至少选择一项抽佣项目"), "", "ajax");
                        }
                        $fee_instore["items_no"] = array_map("trim", $account["fee_instore"]["items_no"]);
                    } else {
                        if ($fee_instore["type"] == 2) {
                            $fee_instore["fee"] = floatval($account["fee_instore"]["fee"]);
                        }
                    }
                    $fee_paybill = array();
                    $fee_paybill["type"] = in_array(intval($account["fee_paybill"]["type"]), array(1, 2)) ? intval($account["fee_paybill"]["type"]) : 1;
                    if ($fee_paybill["type"] == 1) {
                        $fee_paybill["fee_rate"] = floatval($account["fee_paybill"]["fee_rate"]);
                        $fee_paybill["fee_min"] = floatval($account["fee_paybill"]["fee_min"]);
                    } else {
                        if ($fee_paybill["type"] == 2) {
                            $fee_paybill["fee"] = floatval($account["fee_paybill"]["fee"]);
                        }
                    }
                    $update = array("fee_takeout" => iserializer($fee_takeout), "fee_selfDelivery" => iserializer($fee_selfDelivery), "fee_instore" => iserializer($fee_instore), "fee_paybill" => iserializer($fee_paybill));
                    if (empty($_W["agentid"])) {
                        $update["fee_limit"] = intval($account["fee_limit"]);
                        $update["fee_rate"] = intval($account["fee_rate"]);
                        $update["fee_min"] = intval($account["fee_min"]);
                        $update["fee_max"] = intval($account["fee_max"]);
                        $update["fee_period"] = intval($account["fee_period"]);
                    }
                    pdo_update(base64_decode("dGlueV93bWFsbF9zdG9yZV9hY2NvdW50"), $update, array("uniacid" => $_W["uniacid"], "id" => $id));
                    $store = json_decode(htmlspecialchars_decode($_GPC["store"]), true);
                    $data = array("status" => intval($store["data"]["superCoupon"]["status"]), "max_limit" => intval($store["data"]["superCoupon"]["max_limit"]));
                    store_set_data($id, "superCoupon", $data);
                    imessage(error(0, "设置成功"), "", "ajax");
                }
                $result = array("account" => $account, "store" => $store);
                imessage(error(0, $result), "", "ajax");
            }
        }
    }
}

?>
