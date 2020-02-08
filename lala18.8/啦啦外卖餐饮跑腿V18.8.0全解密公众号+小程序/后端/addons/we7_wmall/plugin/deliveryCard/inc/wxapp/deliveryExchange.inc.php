<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
icheckauth();
if ($_config_plugin["card_apply_status"] != 1) {
    imessage(error(-1, "配送会员卡功能未开启"), "", "ajax");
}
if ($op == "index") {
    $member = $_W["member"];
    $result = array("member" => $member);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($op == "exchange") {
        $code = trim($_GPC["code"]);
        $uid = $_W["member"]["uid"];
        if (empty($code) || empty($uid)) {
            imessage(error(-1, "参数错误"), "", "ajax");
        }
        $delivery_code = pdo_get("tiny_wmall_delivery_cards_code", array("uniacid" => $_W["uniacid"], "code" => $code));
        if (empty($delivery_code)) {
            imessage(error(-1, "兑换码不存在"), "", "ajax");
        }
        if ($delivery_code["status"] == 2) {
            imessage(error(-1, "兑换码已被使了"), "", "ajax");
        }
        if ($delivery_code["status"] == 3 || $delivery_code["endtime"] < TIMESTAMP) {
            imessage(error(-1, "兑换码已过期"), "", "ajax");
        }
        $update = array("uid" => $uid, "status" => 2, "exchangetime" => TIMESTAMP);
        $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $uid), array("setmeal_id", "setmeal_day_free_limit", "setmeal_deliveryfee_free_limit", "setmeal_starttime", "setmeal_endtime"));
        if (empty($member)) {
            imessage(error(-1, "会员不存在或已删了"), "", "ajax");
        }
        $delivery_card = pdo_get("tiny_wmall_delivery_cards", array("uniacid" => $_W["uniacid"], "id" => $delivery_code["deliverycard_id"]));
        if (empty($delivery_card)) {
            imessage(error(-1, "套餐不存在或已被删除"), "", "ajax");
        }
        $update_member = array("setmeal_id" => $delivery_code["deliverycard_id"], "setmeal_day_free_limit" => $delivery_card["day_free_limit"], "setmeal_deliveryfee_free_limit" => $delivery_card["delivery_fee_free_limit"], "setmeal_starttime" => TIMESTAMP, "setmeal_endtime" => TIMESTAMP + $delivery_code["days"] * 86400);
        if (TIMESTAMP <= $member["setmeal_endtime"]) {
            if ($member["setmeal_id"] == $delivery_code["deliverycard_id"]) {
                $update_member["setmeal_starttime"] = $member["setmeal_starttime"];
                $update_member["setmeal_endtime"] = $member["setmeal_endtime"] + $delivery_code["days"] * 86400;
            } else {
                imessage(error(-1, "兑换套餐与当前套餐不匹配"), "", "ajax");
            }
        }
        pdo_update("tiny_wmall_delivery_cards_code", $update, array("uniacid" => $_W["uniacid"], "code" => $code));
        pdo_update("tiny_wmall_members", $update_member, array("uniacid" => $_W["uniacid"], "uid" => $uid));
        imessage(error(0, "兑换成功"), "", "ajax");
    }
}

?>
