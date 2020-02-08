<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->classs("wxapp");
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "openid";
if ($ta == "openid") {
    $code = $_GPC["code"];
    if (empty($code)) {
        imessage(error(-1, "通信错误，请在微信中重新发起请求"), "", "ajax");
    }
    $token = trim($_GPC["token"]);
    if (empty($token)) {
        imessage(error(41009, "请重新登录"), "", "ajax");
    }
    $oauth = pdo_get("tiny_wmall_oauth_fans", array("openid" => $token));
    if (!empty($oauth["oauth_openid"])) {
        imessage(error(0, ""), "", "ajax");
    }
    $type = trim($_GPC["type"]);
    if ($type == "manager") {
        $account = get_plugin_config("wxapp.manager");
    } else {
        if ($type == "deliveryer") {
            $account = get_plugin_config("wxapp.deliveryer");
        }
    }
    $account_api = new Wxapp($account);
    $oauth = $account_api->getOauthInfo($code);
    if (!empty($oauth) && !is_error($oauth)) {
        $insert = array("appid" => $account["key"], "openid" => $token, "oauth_openid" => $oauth["openid"], "type" => "wxapp");
        pdo_insert("tiny_wmall_oauth_fans", $insert);
        imessage(error(0, ""), "", "ajax");
        return 1;
    }
    imessage(error(-1, $oauth["message"]), "", "ajax");
}

?>