<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$status = check_mall_status($_W["agentid"]);
if (is_error($status)) {
    imessage(error(-3000, $status["message"]), "", "ajax");
}
if (!$_config_plugin["status"]) {
    imessage(error(-1, "平台暂未开启跑腿功能"), "", "ajax");
}
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $id = intval($_GPC["id"]);
    $diypage = get_errander_diypage($id);
    $diypage["basic"]["params"]["yinsihao"] = array("status" => 0, "agreement" => "");
    if (check_plugin_perm(base64_decode("eWluc2loYW8="))) {
        $yinsihao = get_plugin_config("yinsihao.basic");
        if (!empty($yinsihao) && $yinsihao["status"] == 1) {
            $diypage["basic"]["params"]["yinsihao"]["status"] = 1;
            $diypage["basic"]["params"]["yinsihao"]["agreement"] = get_config_text("yinsihao:agreement");
        }
    }
    $condition = array();
    $params = json_decode(htmlspecialchars_decode($_GPC["extra"]), true);
    if (!empty($params)) {
        $buyaddress_id = intval($params["buyaddress_id"]);
        if (0 < $buyaddress_id) {
            $buyaddress = member_errander_address_check($buyaddress_id);
            if (!is_error($buyaddress)) {
                $condition["buyaddress"] = $buyaddress;
            }
        } else {
            if (!empty($params["buyaddress"])) {
                $buyaddress = member_errander_address_check($params["buyaddress"]);
                if (!is_error($buyaddress)) {
                    $condition["buyaddress"] = $buyaddress;
                }
            }
        }
        $acceptaddress_id = intval($params["acceptaddress_id"]);
        if (0 < $acceptaddress_id) {
            $acceptaddress = member_errander_address_check($acceptaddress_id);
            if (!is_error($acceptaddress)) {
                $condition["acceptaddress"] = $acceptaddress;
            }
        }
        unset($params["buyaddress"]);
        $condition = array_merge($condition, $params);
    }
    $order = errander_order_calculate_delivery_fee($diypage, $condition, intval($_GPC["is_calculate"]));
    if (is_error($order)) {
        message($order, "", "ajax");
    }
    $filter = array("serve_radius" => $_config_plugin["serve_radius"], "location_x" => $_config_plugin["map"]["location_x"], "location_y" => $_config_plugin["map"]["location_y"]);
    $addresses = member_fetchall_address($filter);
    mload()->model("redPacket");
    $result = array("diy" => $diypage["diypage"], "basic" => $diypage["basic"], "addresses" => $addresses, "redPackets" => redPacket_available($order["delivery_fee"], array($id), array("scene" => "paotui")), "order" => $order, "buyaddress_id" => $buyaddress["id"], "buyaddress" => $buyaddress, "acceptaddress_id" => $acceptaddress["id"], "acceptaddress" => $acceptaddress);
    message(error(0, $result), "", "ajax");
} else {
    if ($op == "feeRule") {
        $id = $_GPC["id"];
        $result = array("feeRule" => get_errander_rule_fee($id));
        message(error(0, $result), "", "ajax");
    }
}

?>