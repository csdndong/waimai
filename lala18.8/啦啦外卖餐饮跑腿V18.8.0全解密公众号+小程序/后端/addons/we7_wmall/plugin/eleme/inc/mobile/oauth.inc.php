<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$sid = intval($_GPC["state"]);
if (empty($sid)) {
    imessage("店铺id不能为空", "", "info");
}
pload()->classs("eleme");
$app = new Eleme($sid);
$redirect_uri = imurl("eleme/oauth", array(), true);
$url = $app->getOauthCodeUrl($redirect_uri, $sid);
if (empty($_GPC["code"])) {
    header("Location: " . $url);
    exit;
}
$url = $app->getAccessTokenByCode($_GPC["code"], $redirect_uri);
if (is_error($url)) {
    imessage("进行饿了么授权失败:" . $url["message"], "", "info");
}
imessage("授权成功", referer(), "info");
exit;

?>