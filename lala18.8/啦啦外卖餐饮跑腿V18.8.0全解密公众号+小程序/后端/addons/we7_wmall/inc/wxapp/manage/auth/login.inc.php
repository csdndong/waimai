<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
if ($_W["ispost"]) {
    $mobile = trim($_GPC["mobile"]) ? trim($_GPC["mobile"]) : imessage(error(-1, "请输入手机号"), "", "ajax");
    $clerk = pdo_get("tiny_wmall_clerk", array("uniacid" => $_W["uniacid"], "mobile" => $mobile));
    if (empty($clerk)) {
        imessage(error(-1, "用户不存在"), "", "ajax");
    }
    $password = md5(md5($clerk["salt"] . trim($_GPC["password"])) . $clerk["salt"]);
    if ($password != $clerk["password"]) {
        imessage(error(-1, "用户名或密码错误"), "", "ajax");
    }
    if (empty($clerk["token"])) {
        $clerk["token"] = random(32);
        $token = $clerk["token"];
        pdo_update("tiny_wmall_clerk", array("token" => $token), array("uniacid" => $_W["uniacid"], "id" => $clerk["id"]));
    }
    $sids = pdo_getall("tiny_wmall_store_clerk", array("uniacid" => $_W["uniacid"], "clerk_id" => $clerk["id"]), array(), "sid");
    if (empty($sids)) {
        imessage(error(-1, "您没有绑定店铺，请先绑定店铺"), "", "ajax");
    }
    $result = array("clerk" => $clerk, "sids" => array_keys($sids));
    imessage(error(0, $result), "", "ajax");
}
$config_mall = $_W["we7_wmall"]["config"]["mall"];
$result = array("config" => array("logo" => tomedia($config_mall["logo"]), "title" => $config_mall["title"]));
imessage(error(0, $result), "", "ajax");

?>