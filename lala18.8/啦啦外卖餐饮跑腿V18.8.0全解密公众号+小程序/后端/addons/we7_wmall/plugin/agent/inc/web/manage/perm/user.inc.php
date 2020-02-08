<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "role";
mload()->model(base64_decode("YWdlbnQ="));
$all_perms = get_agent_perms(false, "web");
if ($op == "list") {
    $_W["page"]["title"] = "操作员管理";
    $condition = " where a.uniacid = :uniacid and a.agentid = :agentid";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : -1;
    if (-1 < $status) {
        $condition .= " and a.status = :status";
        $params["status"] = $status;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and b.username like '%" . $keyword . "%'";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_perm_user") . " as a left join " . tablename("tiny_wmall_agent_users") . " as b on a.uid = b.id" . $condition, $params);
    $users = pdo_fetchall("select a.*, b.username from " . tablename("tiny_wmall_perm_user") . " as a left join " . tablename("tiny_wmall_agent_users") . " as b on a.uid = b.id" . $condition . " order by a.id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
    $roles = pdo_fetchall("select id, rolename from " . tablename("tiny_wmall_perm_role") . " where uniacid = :uniacid and agentid = :agentid", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]), "id");
}
if ($op == "post") {
    $_W["page"]["title"] = "编辑操作员";
    $id = intval($_GPC["id"]);
    if (!empty($id)) {
        $user = pdo_get("tiny_wmall_perm_user", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
        $user["username"] = pdo_fetchcolumn("select username from " . tablename("tiny_wmall_agent_users") . " where uniacid = :uniacid and agentid = :agentid and id = :id", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], ":id" => $user["uid"]));
        $user["perms"] = explode(",", $user["perms"]);
    }
    $roles = pdo_fetchall("select id, rolename, perms from " . tablename("tiny_wmall_perm_role") . " where uniacid = :uniacid and agentid = :agentid", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]), "id");
    if (!empty($roles)) {
        foreach ($roles as &$val) {
            $val["perms"] = explode(",", $val["perms"]);
        }
    }
    if ($_W["ispost"]) {
        $mobile = trim($_GPC["mobile"]);
        if (!is_validMobile($mobile)) {
            imessage(error(-1, "手机号格式错误"), referer(), "ajax");
        }
        if (empty($id)) {
            $is_exist = pdo_get("tiny_wmall_agent_users", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "mobile" => $mobile), array("id"));
            if (!empty($is_exist)) {
                imessage(error(-1, "手机号已被注册"), referer(), "ajax");
            }
        }
        $insert = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "roleid" => intval($_GPC["roleid"]), "status" => intval($_GPC["status"]), "realname" => trim($_GPC["realname"]), "mobile" => trim($_GPC["mobile"]), "perms" => implode(",", $_GPC["perms"]));
        if (0 < $insert["roleid"]) {
            $insert["perms"] = implode(",", array_diff($_GPC["perms"], $roles[$insert["roleid"]]["perms"]));
        }
        $member = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "mobile" => trim($_GPC["mobile"]), "status" => intval($_GPC["status"]));
        $member["username"] = trim($_GPC["username"]) ? trim($_GPC["username"]) : imessage(error(-1, "操作员用户名不能为空"), referer(), "ajax");
        if (empty($id)) {
            $member["password"] = trim($_GPC["password"]) ? trim($_GPC["password"]) : imessage(error(-1, "密码不能为空"), "", "ajax");
            $member["salt"] = random(6);
            $member["password"] = md5(md5($member["salt"] . $member["password"]) . $member["salt"]);
            $member["token"] = random(32);
            pdo_insert("tiny_wmall_agent_users", $member);
            $uid = pdo_insertid();
            $insert["uid"] = $uid;
            pdo_insert("tiny_wmall_perm_user", $insert);
        } else {
            $password = trim($_GPC["password"]);
            if (!empty($password)) {
                $member["salt"] = random(6);
                $member["password"] = md5(md5($member["salt"] . $password) . $member["salt"]);
                pdo_update("tiny_wmall_agent_users", $member, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $user["uid"]));
            }
            pdo_update("tiny_wmall_perm_user", $insert, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
        }
        imessage(error(0, "编辑操作员成功"), iurl("perm/user/list"), "ajax");
    }
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $status = intval($_GPC["status"]);
    pdo_update("tiny_wmall_perm_user", array("status" => $status), array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
    imessage(error(0, ""), "", "ajax");
}
if ($op == "del") {
    $uids = $_GPC["id"];
    if (!is_array($uids)) {
        $uids = array($uids);
    }
    foreach ($uids as $uid) {
        pdo_delete("tiny_wmall_perm_user", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "uid" => $uid));
        pdo_delete("tiny_wmall_agent_users", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $uid));
    }
    imessage(error(0, "删除操作员成功"), "", "ajax");
}
include itemplate("perm/user");

?>
