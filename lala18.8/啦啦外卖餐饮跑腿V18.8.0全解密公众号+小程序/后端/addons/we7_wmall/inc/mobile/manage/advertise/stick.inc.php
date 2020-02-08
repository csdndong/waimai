<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("plugin");
pload()->model("advertise");
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
$advertise_stick = get_advertise_info("stick");
if ($advertise_stick["status"] != 1) {
    imessage(error(-1, "该广告暂未开售"), "", "ajax");
}
$amount = $store["account"]["amount"];
$_W["page"]["title"] = "商家置顶推广";
if ($ta == "index") {
    $displayorder_fees = $advertise_stick["prices"];
    $sailed = $advertise_stick["sailed"];
    foreach ($displayorder_fees as $key => &$val) {
        if (in_array($key, $sailed)) {
            $val["sailed"] = 1;
        } else {
            $val["sailed"] = 0;
        }
    }
    if ($_W["isajax"]) {
        $displayorder = intval($_GPC["displayorder"]);
        if (!$displayorder) {
            imessage(error(-1, "请选择置顶位置"), "", "ajax");
        }
        if (empty($advertise_stick["leave"])) {
            imessage(error(-1, "商家置顶广告位已售空，请选择其他广告位"), "", "ajax");
        }
        $day = intval($_GPC["day"]);
        if (!$day) {
            imessage(error(-1, "请选择购买天数"), "", "ajax");
        }
        $pay_type = $_GPC["pay_type"];
        if (!$pay_type) {
            imessage(error(-1, "请选择支付方式"), "", "ajax");
        }
        $finalfee = $advertise_stick["prices"][$displayorder]["fees"][$day]["fee"];
        if ($pay_type == "credit" && $amount < $finalfee) {
            imessage(error(-1, "余额不足，请选择其他支付方式"), "", "ajax");
        }
        $stickData = array("uniacid" => $_W["uniacid"], "sid" => $sid, "type" => "stick", "displayorder" => $displayorder, "title" => "置顶No." . $displayorder . "," . $day . "天", "status" => 0, "addtime" => TIMESTAMP, "starttime" => TIMESTAMP, "endtime" => TIMESTAMP, "is_pay" => 0, "order_sn" => date("YmdHis", time()) . random(6, true), "final_fee" => $finalfee, "pay_type" => $pay_type, "days" => $day, "data" => iserializer(array("displayorder" => $store["displayorder"])));
        pdo_insert("tiny_wmall_advertise_trade", $stickData);
        $id = pdo_insertid();
        imessage(error(0, array("id" => $id, "sid" => $sid)), "", "ajax");
    }
    include itemplate("advertise/stick");
}

?>