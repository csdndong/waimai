<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $plateformer = $_W["plateformer"];
    $result = array("plateformer" => $plateformer, "user" => array("token" => (string) $plateformer["usertype"] . ":" . $plateformer["token"], "perms" => $plateformer["perms"], "role" => $plateformer["role"]));
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "password") {
        $plateformer = $_W["plateformer"];
        $password = trim($_GPC["password"]);
        if (empty($password)) {
            imessage(error(-1, "当前密码不能为空"), "", "ajax");
        }
        $newpassword = trim($_GPC["newpassword"]);
        if (empty($newpassword)) {
            imessage(error(-1, "新密码密码不能为空"), "", "ajax");
        }
        $repasswrod = trim($_GPC["repassword"]);
        if (empty($repasswrod)) {
            imessage(error(-1, "请确认新密码"), "", "ajax");
        }
        if ($newpassword != $repasswrod) {
            imessage(error(-1, "两次密码输入不一致"), "", "ajax");
        }
        if ($plateformer["usertype"] == "plateform") {
            load()->model("user");
            $password = user_hash($password, $plateformer["salt"]);
            if ($password != $plateformer["password"]) {
                imessage(error(-1, "当前密码错误"), "", "ajax");
            }
            $update = array("password" => user_hash($newpassword, $plateformer["salt"]));
            pdo_update("users", $update, array("username" => $plateformer["username"]));
        } else {
            $password = md5(md5($plateformer["salt"] . $password) . $plateformer["salt"]);
            if ($password != $plateformer["password"]) {
                imessage(error(-1, "当前密码错误"), "", "ajax");
            }
            $update = array("password" => md5(md5($plateformer["salt"] . $newpassword) . $plateformer["salt"]));
            pdo_update("tiny_wmall_agent", $update, array("uniacid" => $_W["uniacid"], "mobile" => $plateformer["username"]));
        }
        imessage(error(0, "修改成功"), "", "ajax");
    }
}

?>