<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$config_mall = $_W["we7_wmall"]["config"]["mall"];
$config_mall["logo"] = tomedia($config_mall["logo"]);
$config_deliveryer = $_W["we7_wmall"]["config"]["delivery"];
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    if ($_W["ispost"]) {
        $mobile = trim($_GPC["mobile"]) ? trim($_GPC["mobile"]) : imessage(error(-1, "请输入手机号"), "", "ajax");
    $password = trim($_GPC["password"]) ? trim($_GPC["password"]) : imessage(error(-1, "请输入密码"), "", "ajax");
    $repassword = trim($_GPC["repassword"]) ? trim($_GPC["repassword"]) : imessage(error(-1, "请输入确认密码"), "", "ajax");
        $length = strlen($password);
        if ($length < 8 || 20 < $length) {
        imessage(error(-1, "请输入8-20密码"), "", "ajax");
        }
        if (!preg_match(IREGULAR_PASSWORD, $password)) {
            imessage(error(-1, "密码必须由数字和字母组合"), "", "ajax");
        }
        if ($password != $repassword) {
        imessage(error(-1, "两次输入的密码不一致"), "", "ajax");
        }
        $title = trim($_GPC["title"]) ? trim($_GPC["title"]) : imessage(error(-1, "真实姓名不能为空"), "", "ajax");
        if ($config_deliveryer["settle"]["idCard"] == 1) {
            $idCard = array("idCardOne" => trim($_GPC["idCardOne"]), "idCardTwo" => trim($_GPC["idCardTwo"]));
            if (empty($idCard["idCardOne"])) {
            imessage(error(-1, "手持身份证照片不能为空"), "", "ajax");
            }
            if (empty($idCard["idCardTwo"])) {
            imessage(error(-1, "身份证正面照片不能为空"), "", "ajax");
            }
        }
        $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "mobile" => $mobile));
        if (!empty($deliveryer)) {
            if ($deliveryer["status"] != 1) {
                imessage(error(-1, "此手机号注册的配送员账号已被删除,如需继续使用请联系管理员"), "", "ajax");
            }
            imessage(error(-1, "此手机号已注册, 请直接登录"), "", "ajax");
        }
        $deliveryer = array("uniacid" => $_W["uniacid"], "agentid" => intval($_GPC["agentid"]), "mobile" => $mobile, "title" => $title, "salt" => random(6), "token" => random(32), "addtime" => TIMESTAMP, "auth_info" => iserializer($idCard), "is_errander" => $config_deliveryer["cash"]["is_errander"], "is_takeout" => $config_deliveryer["cash"]["is_takeout"], "collect_max_takeout" => $config_deliveryer["cash"]["collect_max_takeout"], "collect_max_errander" => $config_deliveryer["cash"]["collect_max_errander"], "perm_cancel" => iserializer($config_deliveryer["cash"]["perm_cancel"]), "perm_transfer" => iserializer($config_deliveryer["cash"]["perm_transfer"]), "fee_getcash" => iserializer($config_deliveryer["cash"]["fee_getcash"]), "fee_delivery" => iserializer($config_deliveryer["cash"]["fee_delivery"]));
        $deliveryer["password"] = md5(md5($deliveryer["salt"] . $password) . $deliveryer["salt"]);
        pdo_insert("tiny_wmall_deliveryer", $deliveryer);
        $deliveryer_id = pdo_insertid();
        sys_notice_deliveryer_settle($deliveryer_id);
        imessage(error(0, "注册成功"), "", "ajax");
    }
    $result = array("config_mall" => $config_mall, "config_deliveryer" => $config_deliveryer, "captcha" => iaurl("system/common/captcha", array("state" => "we7sid-" . $_W["session_id"], "t" => TIMESTAMP), true), "isagent" => $_W["is_agent"]);
    if ($_W["is_agent"]) {
        mload()->model("agent");
        $agents = get_agents(1);
        foreach ($agents as $val) {
            $result["agents"][] = array("text" => $val["area"], "id" => $val["id"]);
        }
    }
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "captcha") {
        $result = array("captcha" => iaurl("system/common/captcha", array("state" => "we7sid-" . $_W["session_id"], "t" => TIMESTAMP), true));
        imessage(error(0, $result), "", "ajax");
    }
}

?>