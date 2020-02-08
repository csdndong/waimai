<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "settle";
if ($op == "settle") {
    $_W["page"]["title"] = "配送员申请";
    if ($_W["ispost"]) {
        $settle = array("mobile_verify_status" => intval($_GPC["mobile_verify_status"]), "idCard" => intval($_GPC["idCard"]));
        set_config_text("配送员入驻协议", "agreement_delivery", htmlspecialchars_decode($_GPC["agreement_delivery"]));
        set_system_config("delivery.settle", $settle);
        imessage(error(0, "配送员申请设置成功"), referer(), "ajax");
    }
    $settle = $_config["delivery"]["settle"];
    $settle["agreement_delivery"] = get_config_text("agreement_delivery");
    include itemplate("config/deliveryer-settle");
} else {
if ($op == "cash") {
    $_W["page"]["title"] = "提成及提现";
        $deliveryerCash = $_config["delivery"]["cash"];
        if ($_W["ispost"]) {
            $form_type = trim($_GPC["form_type"]);
            if ($form_type == "delivery_setting") {
                $deliveryerCash["is_errander"] = intval($_GPC["is_errander"]);
                $deliveryerCash["is_takeout"] = intval($_GPC["is_takeout"]);
                $deliveryerCash["collect_max_takeout"] = intval($_GPC["collect_max_takeout"]);
                $deliveryerCash["collect_max_errander"] = intval($_GPC["collect_max_errander"]);
                $deliveryerCash["perm_cancel"] = array("status_takeout" => intval($_GPC["perm_cancel"]["status_takeout"]), "status_errander" => intval($_GPC["perm_cancel"]["status_errander"]));
                $deliveryerCash["perm_transfer"] = array("status_takeout" => intval($_GPC["perm_transfer"]["status_takeout"]), "max_takeout" => intval($_GPC["perm_transfer"]["max_takeout"]), "status_errander" => intval($_GPC["perm_transfer"]["status_errander"]), "max_errander" => intval($_GPC["perm_transfer"]["max_errander"]));
                $deliveryer_takeout_fee_type = intval($_GPC["deliveryer_takeout_fee_type"]);
                $deliveryer_takeout_fee = 0;
                if ($deliveryer_takeout_fee_type == 1) {
                    $deliveryer_takeout_fee = floatval($_GPC["deliveryer_takeout_fee_1"]);
                } else {
                    if ($deliveryer_takeout_fee_type == 2) {
                        $deliveryer_takeout_fee = floatval($_GPC["deliveryer_takeout_fee_2"]);
                    } else {
                        if ($deliveryer_takeout_fee_type == 3) {
                            $deliveryer_takeout_fee = array("start_fee" => floatval($_GPC["deliveryer_takeout_fee_3"]["start_fee"]), "start_km" => floatval($_GPC["deliveryer_takeout_fee_3"]["start_km"]), "pre_km" => floatval($_GPC["deliveryer_takeout_fee_3"]["pre_km"]), "max_fee" => floatval($_GPC["deliveryer_takeout_fee_3"]["max_fee"]));
                        } else {
                            if ($deliveryer_takeout_fee_type == 4) {
                                $deliveryer_takeout_fee = floatval($_GPC["deliveryer_takeout_fee_4"]);
                            }
                        }
                    }
                }
                $deliveryer_errander_fee_type = intval($_GPC["deliveryer_errander_fee_type"]);
                $deliveryer_errander_fee = 0;
                if ($deliveryer_errander_fee_type == 1) {
                    $deliveryer_errander_fee = floatval($_GPC["deliveryer_errander_fee_1"]);
                } else {
                    if ($deliveryer_errander_fee_type == 2) {
                        $deliveryer_errander_fee = floatval($_GPC["deliveryer_errander_fee_2"]);
                    } else {
                        if ($deliveryer_errander_fee_type == 3) {
                            $deliveryer_errander_fee = array("start_fee" => floatval($_GPC["deliveryer_errander_fee_3"]["start_fee"]), "start_km" => floatval($_GPC["deliveryer_errander_fee_3"]["start_km"]), "pre_km" => floatval($_GPC["deliveryer_errander_fee_3"]["pre_km"]), "max_fee" => floatval($_GPC["deliveryer_errander_fee_3"]["max_fee"]));
                        }
                    }
                }
                $deliveryerCash["fee_delivery"] = array("takeout" => array("deliveryer_fee_type" => $deliveryer_takeout_fee_type, "deliveryer_fee" => $deliveryer_takeout_fee), "errander" => array("deliveryer_fee_type" => $deliveryer_errander_fee_type, "deliveryer_fee" => $deliveryer_errander_fee));
            } else {
                if ($form_type == "getcash_setting") {
                    $deliveryerCash["fee_getcash"] = array("get_cash_fee_limit" => floatval($_GPC["fee_getcash"]["get_cash_fee_limit"]), "get_cash_fee_rate" => floatval($_GPC["fee_getcash"]["get_cash_fee_rate"]), "get_cash_fee_min" => floatval($_GPC["fee_getcash"]["get_cash_fee_min"]), "get_cash_fee_max" => floatval($_GPC["fee_getcash"]["get_cash_fee_max"]), "get_cash_period" => intval($_GPC["fee_getcash"]["get_cash_period"]));
                }
            }
            unset($deliveryerCash["sync"]);
            unset($deliveryerCash["get_cash_fee_limit"]);
            unset($deliveryerCash["get_cash_fee_rate"]);
            unset($deliveryerCash["get_cash_fee_min"]);
            unset($deliveryerCash["get_cash_fee_max"]);
            unset($deliveryerCash["get_cash_period"]);
            set_system_config(base64_decode("ZGVsaXZlcnkuY2FzaA=="), $deliveryerCash);
            $deliveryerCash["perm_cancel"] = iserializer($deliveryerCash["perm_cancel"]);
            $deliveryerCash["perm_transfer"] = iserializer($deliveryerCash["perm_transfer"]);
            $deliveryerCash["fee_delivery"] = iserializer($deliveryerCash["fee_delivery"]);
            $deliveryerCash["fee_getcash"] = iserializer($deliveryerCash["fee_getcash"]);
            $update = $deliveryerCash;
            if ($form_type == "delivery_setting") {
                unset($update["fee_getcash"]);
            } else {
                if ($form_type == "getcash_setting") {
                    $update = array("fee_getcash" => $update["fee_getcash"]);
                }
            }
            $sync = intval($_GPC["sync"]);
            if ($sync == 1) {
                pdo_update("tiny_wmall_deliveryer", $update, array("uniacid" => $_W["uniacid"]));
            } else {
                if ($sync == 2) {
                    $deliveryer_ids = $_GPC["deliveryer_ids"];
                    foreach ($deliveryer_ids as $deliveryer_id) {
                        pdo_update("tiny_wmall_deliveryer", $update, array("uniacid" => $_W["uniacid"], "id" => intval($deliveryer_id)));
                    }
                }
            }
            imessage(error(0, "配送员设置成功"), referer(), "ajax");
        }
        mload()->model("deliveryer");
        $deliveryers = deliveryer_all();
        include itemplate("config/deliveryer-cash");
    } else {
        if ($op == "extra") {
            $_W["page"]["title"] = "其他设置";
            if ($_W["ispost"]) {
                $data = array("takeout_rank_status" => intval($_GPC["takeout_rank_status"]), "errander_rank_status" => intval($_GPC["errander_rank_status"]));
                set_system_config("delivery.extra", $data);
                imessage(error(0, "配送员的其他设置设置成功"), referer(), "ajax");
            }
            $extra = $_config["delivery"]["extra"];
            include itemplate("config/deliveryer-extra");
        }
    }
}

?>