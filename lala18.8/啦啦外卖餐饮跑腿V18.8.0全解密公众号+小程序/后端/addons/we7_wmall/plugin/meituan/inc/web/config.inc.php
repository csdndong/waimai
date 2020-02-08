<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "基础设置";
    if ($_W["ispost"]) {
        $data = array("status" => intval($_GPC["status"]), "developerId" => trim($_GPC["developerId"]), "signKey" => trim($_GPC["signKey"]));
        set_plugin_config("meituan", $data);
        imessage(error(0, "设置美团平台对接成功"), "refresh", "ajax");
    }
    $meituan = get_plugin_config("meituan");
    $urls = array("order" => array("title" => "推送订单URL", "url" => imurl("meituan/api/order", array(), true)), "orderCancel" => array("title" => "美团用户或客服取消URL", "url" => imurl("meituan/api/orderCancel", array(), true)), "orderRefund" => array("title" => "美团用户或客服退款流程URL", "url" => imurl("meituan/api/orderRefund", array(), true)), "orderConfirm" => array("title" => "订单已确认的回调URL", "url" => imurl("meituan/api/orderConfirm", array(), true)), "orderEnd" => array("title" => "已完成订单推送回调URL", "url" => imurl("meituan/api/orderEnd", array(), true)), "orderShippingStatus" => array("title" => "订单配送状态的回调URL", "url" => imurl("meituan/api/orderShippingStatus", array(), true)), "storemap" => array("title" => "门店映射回调地址", "url" => imurl("meituan/api/storemap", array(), true)), "releasebinding" => array("title" => "门店映射解绑回调地址", "url" => imurl("meituan/api/releasebinding", array(), true)), "yinsihaojiangji" => array("title" => "隐私号降级URL", "url" => imurl("meituan/api/yinsihaojiangji", array(), true)));
}
include itemplate("config");

?>