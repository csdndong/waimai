<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "基础设置";
    if ($_W["ispost"]) {
        $type = trim($_GPC["type"]);
        if (!in_array($type, array("store", "plateform"))) {
            imessage(error(-1, "请选择对接模式"), "", "ajax");
        }
        $data = array("status" => intval($_GPC["status"]), "type" => $type, "appkey" => trim($_GPC["appkey"]), "appsecret" => trim($_GPC["appsecret"]), "accesstoken" => trim($_GPC["accesstoken"]), "merchantid" => intval($_GPC["merchantid"]), "cityCode" => intval($_GPC["cityCode"]));
        set_plugin_config("dianwoda", $data);
        imessage(error(0, "设置成功"), "refresh", "ajax");
    }
    $notify_url = WE7_WMALL_URL . "plugin/dianwoda/notify.php";
    $dianwoda = get_plugin_config("dianwoda");
    if (empty($dianwoda["type"])) {
        $dianwoda["type"] = "plateform";
    }
    $testUrl = imurl("dianwoda/api", array(), true);
}
include itemplate("config");

?>
