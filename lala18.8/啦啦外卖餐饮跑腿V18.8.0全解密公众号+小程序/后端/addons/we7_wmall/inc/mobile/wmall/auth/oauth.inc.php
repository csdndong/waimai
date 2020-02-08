<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "微信信息";
if (!is_weixin()) {
    imessage("请在微信中打开该链接", "", "info");
}
$fans = mc_oauth_userinfo();
if (is_error($fans) || empty($fans["openid"])) {
    imessage("获取微信信息失败", "", "info");
}
$params = json_decode(base64_decode($_GPC["params"]), true);
if (!empty($params)) {
    $_SESSION["params"] = $params;
} else {
    $params = $_SESSION["params"];
}
if (empty($params)) {
    imessage("参数不合法", "", "info");
}
$url = imurl("wmall/auth/oauth", array(), true);
$oauth = member_oauth_info($url, $params);
if (is_error($oauth)) {
    imessage("获取粉丝身份出错,请重新操作", "close", "error");
} else {
    unset($_SESSION["params"]);
    imessage("获取微信信息成功", "close", "success");
}

?>