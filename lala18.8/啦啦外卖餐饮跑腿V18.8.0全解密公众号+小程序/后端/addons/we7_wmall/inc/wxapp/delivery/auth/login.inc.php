<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
if ($_W["ispost"]) {
    $mobile = trim($_GPC["mobile"]) ? trim($_GPC["mobile"]) : imessage(error(-1, "请输入手机号"), "", "ajax");
    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "mobile" => $mobile));
    if (empty($deliveryer)) {
        imessage(error(-1, "用户不存在"), "", "ajax");
    }
    if ($deliveryer["status"] != 1) {
        imessage(error(-1, "此手机号注册的配送员账号已被删除,如需继续使用请联系管理员"), "", "ajax");
    }
    $password = md5(md5($deliveryer["salt"] . trim($_GPC["password"])) . $deliveryer["salt"]);
    if ($password != $deliveryer["password"]) {
        imessage(error(-1, "用户名或密码错误"), "", "ajax");
    }
    if (empty($deliveryer["token"])) {
        $deliveryer["token"] = random(32);
        pdo_update("tiny_wmall_deliveryer", array("token" => $deliveryer["token"]), array("id" => $deliveryer["id"]));
    }
    $result = array("deliveryer" => $deliveryer);
    imessage(error(0, $result), "", "ajax");
}
$config_mall = $_W["we7_wmall"]["config"]["mall"];
$result = array("config" => array("logo" => tomedia($config_mall["logo"]), "title" => $config_mall["title"]));
imessage(error(0, $result), "", "ajax");

?>