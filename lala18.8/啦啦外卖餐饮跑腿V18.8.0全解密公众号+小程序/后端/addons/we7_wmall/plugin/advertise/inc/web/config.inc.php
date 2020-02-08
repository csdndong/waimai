<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
$config_advertise = get_plugin_config("advertise.basic");
if ($op == "index") {
    $_W["page"]["title"] = "基础设置";
    if ($_W["ispost"]) {
        $hours = intval($_GPC["notify_before_hours"]);
        if ($hours < 0 || 24 < $hours) {
            imessage(error(-1, "请设置为0-24之间的整数"), "", "ajax");
        }
        $data = array("status" => intval($_GPC["status"]) ? intval($_GPC["status"]) : 0, "notify_before_hours" => $hours);
        set_plugin_config("advertise.basic", $data);
        imessage(error(0, "设置推广成功"), "refresh", "ajax");
    }
    include itemplate("config");
}
if ($op == "payment") {
    $_W["page"]["title"] = "支付设置";
    if ($_W["ispost"]) {
        $payment = array("alipay" => array("status" => intval($_GPC["alipay"]["status"]), "account" => trim($_GPC["alipay"]["account"]), "partner" => trim($_GPC["alipay"]["partner"]), "secret" => trim($_GPC["alipay"]["secret"])), "wechat" => array(), "credit" => array());
        set_plugin_config("advertise.payment", $payment);
        imessage(error(0, "设置推广成功"), "refresh", "ajax");
    }
    $config_payment = get_plugin_config("advertise.payment");
    include itemplate("payment");
}

?>