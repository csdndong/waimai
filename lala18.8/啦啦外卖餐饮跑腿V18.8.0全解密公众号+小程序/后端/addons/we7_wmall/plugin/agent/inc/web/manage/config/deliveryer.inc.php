<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "cash";
if ($op == "cash") {
    $_W["page"]["title"] = "提成及提现";
    if ($_W["ispost"]) {
        $sync = intval($_GPC["sync"]);
        $data = array("is_errander" => intval($_GPC["is_errander"]), "is_takeout" => intval($_GPC["is_takeout"]), "collect_max_takeout" => intval($_GPC["collect_max_takeout"]), "collect_max_errander" => intval($_GPC["collect_max_errander"]), "perm_cancel" => array("status_takeout" => intval($_GPC["perm_cancel"]["status_takeout"]), "status_errander" => intval($_GPC["perm_cancel"]["status_errander"])), "perm_transfer" => array("status_takeout" => intval($_GPC["perm_transfer"]["status_takeout"]), "max_takeout" => intval($_GPC["perm_transfer"]["max_takeout"]), "status_errander" => intval($_GPC["perm_transfer"]["status_errander"]), "max_errander" => intval($_GPC["perm_transfer"]["max_errander"])));
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
        $delivery_fee = array("takeout" => array("deliveryer_fee_type" => $deliveryer_takeout_fee_type, "deliveryer_fee" => $deliveryer_takeout_fee), "errander" => array("deliveryer_fee_type" => $deliveryer_errander_fee_type, "deliveryer_fee" => $deliveryer_errander_fee));
        $data["fee_delivery"] = array("takeout" => array("deliveryer_fee_type" => $deliveryer_takeout_fee_type, "deliveryer_fee" => $deliveryer_takeout_fee), "errander" => array("deliveryer_fee_type" => $deliveryer_errander_fee_type, "deliveryer_fee" => $deliveryer_errander_fee));
        $data["sync"] = intval($_GPC["sync"]);
        set_agent_system_config("delivery.cash", $data);
        unset($data["sync"]);
        $sync = intval($_GPC["sync"]);
        if (0 < $sync) {
            $data["perm_cancel"] = iserializer($data["perm_cancel"]);
            $data["perm_transfer"] = iserializer($data["perm_transfer"]);
            $data["fee_delivery"] = iserializer($data["fee_delivery"]);
            if ($sync == 1) {
                pdo_update("tiny_wmall_deliveryer", $data, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"]));
            } else {
                if ($sync == 2) {
                    $deliveryer_ids = $_GPC["deliveryer_ids"];
                    foreach ($deliveryer_ids as $deliveryer_id) {
                        pdo_update("tiny_wmall_deliveryer", $data, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => intval($deliveryer_id)));
                    }
                }
            }
        }
        imessage(error(0, "配送员设置成功"), referer(), "ajax");
    }
    $deliveryer = $_config["delivery"]["cash"];
    mload()->model("deliveryer");
    $deliveryers = deliveryer_all();
    include itemplate("config/deliveryer-cash");
}

?>