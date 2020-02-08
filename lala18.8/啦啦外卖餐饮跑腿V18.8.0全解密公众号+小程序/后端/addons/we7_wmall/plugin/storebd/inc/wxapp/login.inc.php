<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
if ($_W["ispost"]) {
    $mobile = trim($_GPC["mobile"]) ? trim($_GPC["mobile"]) : imessage(error(-1, "请输入手机号"), "", "ajax");
    $storebd_user = storebd_user_fetch($mobile, "mobile");
    if (empty($storebd_user)) {
        imessage(error(-1, "店铺推广员不存在"), "", "ajax");
    }
    $password = md5(md5($storebd_user["salt"] . trim($_GPC["password"])) . $storebd_user["salt"]);
    if ($password != $storebd_user["password"]) {
        imessage(error(-1, "用户名或密码错误"), "", "ajax");
    }
    $result = array("storebd_user" => $storebd_user);
    imessage(error(0, $result), "", "ajax");
}
$config_mall = $_W["we7_wmall"]["config"]["mall"];
$result = array("config" => array("logo" => tomedia($config_mall["logo"]), "title" => $config_mall["title"], "setting_meta_title" => $_config_plugin["basic"]["setting_meta_title"]));
imessage(error(0, $result), "", "ajax");

?>