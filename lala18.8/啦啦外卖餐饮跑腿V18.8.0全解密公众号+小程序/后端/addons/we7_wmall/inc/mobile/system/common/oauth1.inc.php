<?php
defined("IN_IA") or exit("Access Denied");
$fans = mc_oauth_userinfo();
if (is_error($fans) || empty($fans["openid"])) {
    imessage("获取微信信息失败", "", "info");
}
if (!empty($_W["member"])) {
    if (MODULE_FAMILY == "wxapp") {
        echo "绑定粉丝成功";
        exit;
    }
    imessage("绑定粉丝成功", "close", "success");
}
if (MODULE_FAMILY == "wxapp") {
    echo "绑定粉丝失败,请重新绑定";
    exit;
}
imessage("绑定粉丝失败,请重新绑定", "close", "info");

?>