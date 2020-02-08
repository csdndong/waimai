<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("agent");
global $_W;
global $_GPC;
$_W["page"]["title"] = "代理账号设置";
if ($_W["ispost"]) {
    $mobile = trim($_GPC["mobile"]);
    if (!is_validMobile($mobile)) {
        imessage(error(-1, "手机号格式错误"), referer(), "ajax");
    }
    if ($_W["role"] == "agenter") {
    $is_exist = pdo_fetch("select id from " . tablename("tiny_wmall_agent") . " where uniacid = :uniacid and id != :id and mobile = :mobile", array(":uniacid" => $_W["uniacid"], ":id" => $_W["agentid"], ":mobile" => $mobile));
    if (!empty($is_exist)) {
        imessage(error(-1, "该手机号已被其他代理注册"), referer(), "ajax");
    }
    $data = array("title" => trim($_GPC["title"]), "realname" => trim($_GPC["realname"]), "mobile" => $mobile);
    $password = trim($_GPC["password"]);
    if (!empty($password)) {
        $data["salt"] = random(6);
        $data["password"] = md5(md5($data["salt"] . $password) . $data["salt"]);
    }
    pdo_update("tiny_wmall_agent", $data, array("id" => $_W["agentid"], "uniacid" => $_W["uniacid"]));
        imessage(error(0, "代理账号设置成功"), referer(), "ajax");
    } else {
        if ($_W["role"] == "agent_operator") {
            $is_exist = pdo_fetch("select id from " . tablename("tiny_wmall_agent_users") . " where uniacid = :uniacid and id != :id and mobile = :mobile", array(":uniacid" => $_W["uniacid"], ":id" => $_W["we7_wmall"]["agent_user"]["id"], ":mobile" => $mobile));
            if (!empty($is_exist)) {
                imessage(error(-1, "该手机号已经被其他操作员注册"), referer(), "ajax");
            }
            $data = array("username" => trim($_GPC["title"]), "mobile" => $mobile);
            $password = trim($_GPC["password"]);
            if (!empty($password)) {
                $data["salt"] = random(6);
                $data["password"] = md5(md5($data["salt"] . $password) . $data["salt"]);
                pdo_update("tiny_wmall_agent_users", $data, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $_W["we7_wmall"]["agent_user"]["id"]));
            }
            $update = array("realname" => trim($_GPC["realname"]), "mobile" => $mobile);
            pdo_update(base64_decode("dGlueV93bWFsbF9wZXJtX3VzZXI="), $update, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "uid" => $_W["we7_wmall"]["agent_user"]["id"]));
            imessage(error(0, "代理下的操作员账号设置成功"), referer(), "ajax");
        }
    }
}
if ($_W["role"] == "agenter") {
    $agent = get_agent($_W["agentid"], array("title", "realname", "mobile", "password"));
} else {
    if ($_W["role"] == "agent_operator") {
        $user = pdo_get("tiny_wmall_perm_user", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "uid" => $_W["we7_wmall"]["agent_user"]["id"]), array("realname"));
        $agent = array("title" => $_W["we7_wmall"]["agent_user"]["username"], "realname" => $user["realname"], "mobile" => $_W["we7_wmall"]["agent_user"]["mobile"]);
    }
}
include itemplate("agent/setting");

?>