<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "basic";
if ($op == "basic") {
    $_W["page"]["title"] = "基础设置";
    $wxapp = get_plugin_config("wxapp.basic");
    if ($_W["ispost"]) {
        $data = array("status" => intval($_GPC["status"]), "audit_status" => intval($_GPC["audit_status"]), "default_sid" => intval($_GPC["default_sid"]), "key" => trim($_GPC["key"]), "secret" => trim($_GPC["secret"]), "wxapp_consumer_notice_channel" => trim($_GPC["wxapp_consumer_notice_channel"]), "store_url" => trim($_GPC["store_url"]), "tpl_consumer_url" => trim($_GPC["tpl_consumer_url"]));
        if (!empty($wxapp["release_version"])) {
            $data["release_version"] = $wxapp["release_version"];
        }
        set_plugin_config("wxapp.basic", $data);
        imessage(error(0, "基础设置成功"), "refresh", "ajax");
    }
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]));
    include itemplate("config/basic");
}
if ($op == "payment") {
    $_W["page"]["title"] = "支付方式";
    if ($_W["ispost"]) {
        load()->func("file");
        $config_old_payment = get_plugin_config("wxapp.payment");
        $config_payment = array("wechat" => array("type" => trim($_GPC["wechat"]["type"]) ? trim($_GPC["wechat"]["type"]) : "default", "default" => array("appid" => trim($_GPC["wxapp"]["wechat"]["appid"]), "appsecret" => trim($_GPC["wxapp"]["wechat"]["appsecret"]), "mchid" => trim($_GPC["wxapp"]["wechat"]["mchid"]), "apikey" => trim($_GPC["wxapp"]["wechat"]["apikey"]), "apiclient_cert" => $config_old_payment["wechat"]["default"]["apiclient_cert"], "apiclient_key" => $config_old_payment["wechat"]["default"]["apiclient_key"], "rootca" => $config_old_payment["wechat"]["default"]["rootca"])), "wxapp" => array());
        if (!empty($_GPC["wxapp_type"])) {
            foreach ($_GPC["wxapp_type"] as $key => $row) {
                if ($row == 1) {
                    $config_payment["wxapp"][] = $key;
                }
            }
        }
        $keys = array("apiclient_cert", "apiclient_key", "rootca");
        foreach ($keys as $key) {
            if (!empty($_GPC["wxapp"]["wechat"][$key])) {
                $text = trim($_GPC["wxapp"]["wechat"][$key]);
                @unlink(MODULE_ROOT . "/cert/" . $config_payment["wechat"]["default"][$key] . "/" . $key . ".pem");
                @rmdir(MODULE_ROOT . "/cert/" . $config_payment["wechat"]["default"][$key]);
                $name = random(10);
                $status = ifile_put_contents("cert/" . $name . "/" . $key . ".pem", $text);
                $config_payment["wechat"]["default"][$key] = $name;
            }
        }
        set_plugin_config("wxapp.payment", $config_payment);
        imessage(error(0, "支付方式设置成功"), referer(), "ajax");
    }
    $payment = get_plugin_config("wxapp.payment");
    include itemplate("config/payment");
}
if ($op == "wxtemplate") {
    $_W["page"]["title"] = "微信模板消息";
    if ($_W["ispost"]) {
        $wx_template = $_GPC["wechat"];
        set_plugin_config("wxapp.wxtemplate", $wx_template);
        imessage(error(0, "微信模板消息设置成功"), "refresh", "ajax");
    }
    $wechat = get_plugin_config("wxapp.wxtemplate");
    include itemplate("config/wxtemplate");
}
if ($op == "del_cert") {
    load()->func("file");
    $config_payment = get_plugin_config("wxapp.payment");
    $keys = array("apiclient_cert", "apiclient_key", "rootca");
    foreach ($keys as $key) {
        @unlink(MODULE_ROOT . "/cert/" . $config_payment["wechat"]["default"][$key] . "/" . $key . ".pem");
        @rmdir(MODULE_ROOT . "/cert/" . $config_payment["wechat"]["default"][$key]);
        $config_payment["wechat"]["default"][$key] = "";
    }
    set_plugin_config("wxapp.payment", $config_payment);
    imessage(error(0, "证书已删除，请上传新证书！"), referer(), "ajax");
}

?>
