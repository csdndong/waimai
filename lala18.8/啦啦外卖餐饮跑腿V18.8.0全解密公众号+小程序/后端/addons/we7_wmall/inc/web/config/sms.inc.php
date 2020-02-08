<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "set";
if ($op == "set") {
    $_W["page"]["title"] = "短信平台";
    if ($_W["ispost"]) {
        $sms = array("status" => intval($_GPC["status"]), "version" => intval($_GPC["version"]), "key" => trim($_GPC["key"]), "secret" => trim($_GPC["secret"]), "sign" => trim($_GPC["sign"]));
        set_system_config("sms.set", $sms);
        imessage(error(0, "短信平台设置成功"), referer(), "ajax");
    }
    $sms = $_config["sms"]["set"];
} else {
    if ($op == "template") {
        $_W["page"]["title"] = "短信平台";
        if ($_W["ispost"]) {
            $template = array("verify_code_tpl" => trim($_GPC["verify_code_tpl"]));
            set_system_config("sms.template", $template);
            imessage(error(0, "短信模板设置成功"), referer(), "ajax");
        }
        $sms = $_config["sms"]["template"];
    } else {
        if ($op == "verify") {
            $_W["page"]["title"] = "短信验证";
            if ($_W["ispost"]) {
                $data = array("clerk_register" => intval($_GPC["clerk_register"]), "consumer_register" => intval($_GPC["consumer_register"]));
                set_system_config("sms.verify", $data);
                imessage(error(0, "短信模板设置成功"), referer(), "ajax");
            }
            $verify = $_config["sms"]["verify"];
        }
    }
}
include itemplate("config/sms");

?>