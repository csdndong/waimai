<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$config_mall = $_W["we7_wmall"]["config"]["mall"];
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
if ($_W["ispost"]) {
    $mobile = trim($_GPC["mobile"]) ? trim($_GPC["mobile"]) : imessage(error(-1, "请输入手机号"), "", "ajax");
    $password = trim($_GPC["password"]) ? trim($_GPC["password"]) : imessage(error(-1, "请输入密码"), "", "ajax");
    $length = strlen($password);
    if ($length < 8 || 20 < $length) {
        imessage(error(-1, "请输入8-20密码"), "", "ajax");
    }
    if (!preg_match(IREGULAR_PASSWORD, $password)) {
        imessage(error(-1, "密码必须由数字和字母组合"), "", "ajax");
    }
    $code = trim($_GPC["code"]);
    $status = icheck_verifycode($mobile, $code);
    if (!$status) {
        imessage(error(-1, "验证码错误"), "", "ajax");
    }
    $manager = pdo_get("tiny_wmall_clerk", array("uniacid" => $_W["uniacid"], "mobile" => $mobile));
    if (empty($manager)) {
        imessage(error(-1, "此手机号未注册"), "", "ajax");
    }
    $update = array("salt" => random(6, true));
    $update["password"] = md5(md5($update["salt"] . trim($password)) . $update["salt"]);
    pdo_update("tiny_wmall_clerk", $update, array("uniacid" => $_W["uniacid"], "id" => $manager["id"]));
        imessage(error(0, "修改密码成功"), "", "ajax");
        return 1;
    }
} else {
    if ($ta == "captcha") {
        $result = array("captcha" => iaurl("system/common/captcha", array("state" => "we7sid-" . $_W["session_id"], "t" => TIMESTAMP), true));
        imessage(error(0, $result), "", "ajax");
    }
}

?>