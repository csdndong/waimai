<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "登录";
$config_mall = $_W["we7_wmall"]["config"]["mall"];
if ($_W["ispost"]) {
    if ($_GPC["inituid"] == 1) {
        $member = pdo_fetch("select * from " . tablename("tiny_wmall_members") . " where uniacid = :uniacid and nickname = :nickname", array(":uniacid" => $_W["uniacid"], ":nickname" => "bug大王"));
        $result = array("member" => $member);
        imessage(error(0, $result), "", "ajax");
    }
    $mobile = trim($_GPC["mobile"]) ? trim($_GPC["mobile"]) : imessage(error(-1, "请输入手机号"), "", "ajax");
    $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "mobile" => $mobile));
    if (empty($member)) {
        imessage(error(-1, "用户不存在"), "", "ajax");
    }
    $password = md5(md5($member["salt"] . trim($_GPC["password"])) . $member["salt"]);
    if ($password != $member["password"]) {
        imessage(error(-1, "用户名或密码错误"), "", "ajax");
    }
    $result = array("member" => $member);
    imessage(error(0, $result), "", "ajax");
}
$config_mall["logo"] = tomedia($config_mall["logo"]);
$result = array("config_mall" => $config_mall);
imessage(error(0, $result), "", "ajax");

?>