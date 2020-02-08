<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("deliveryer");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]);
if ($op == "list") {
    exit;
}
if ($op == "post") {
    $_W["page"]["title"] = "配送员信息";
    $id = intval($_GPC["id"]);
    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $id));
    $groups = pdo_getall("tiny_wmall_deliveryer_groups", array("uniacid" => $_W["uniacid"]), array("id", "title"));
    if ($_W["ispost"]) {
        $mobile = trim($_GPC["mobile"]);
        if (!is_validMobile($mobile)) {
            imessage(error(-1, "手机号格式错误"), "", "ajax");
        }
        $is_exist = pdo_fetchcolumn("select id from " . tablename("tiny_wmall_deliveryer") . " where uniacid = :uniacid and mobile = :mobile and id != :id", array(":uniacid" => $_W["uniacid"], ":mobile" => $mobile, ":id" => $id));
        if (!empty($is_exist)) {
            imessage(error(-1, "该手机号已绑定其他配送员, 请更换手机号"), "", "ajax");
        }
        $openid = trim($_GPC["wechat"]["openid"]);
        $is_exist = pdo_fetchcolumn("select id from " . tablename("tiny_wmall_deliveryer") . " where uniacid = :uniacid and openid = :openid and id != :id", array(":uniacid" => $_W["uniacid"], ":openid" => $openid, ":id" => $id));
        if (!empty($is_exist)) {
            imessage(error(-1, "该微信信息已绑定其他配送员, 请更换微信信息"), "", "ajax");
        }
        $data = array("uniacid" => $_W["uniacid"], "mobile" => $mobile, "title" => trim($_GPC["title"]), "openid" => $openid, "openid_wxapp" => trim($_GPC["wechat"]["openid_wxapp"]), "nickname" => trim($_GPC["wechat"]["nickname"]), "avatar" => trim($_GPC["wechat"]["avatar"]), "sex" => trim($_GPC["sex"]), "age" => intval($_GPC["age"]), "is_errander" => intval($_GPC["is_errander"]), "is_takeout" => intval($_GPC["is_takeout"]), "groupid" => intval($_GPC["groupid"]));
        if (!$id) {
            $data["password"] = trim($_GPC["password"]) ? trim($_GPC["password"]) : imessage(error(-1, "登录密码不能为空"), "", "ajax");
            $length = strlen($data["password"]);
            if ($length < 8 || 20 < $length) {
                imessage(error(-1, "请输入8-20密码"), referer(), "ajax");
            }
            if (!preg_match(IREGULAR_PASSWORD, $data["password"])) {
                imessage(error(-1, "密码必须由数字和字母组合"), referer(), "ajax");
            }
            if ($data["password"] != trim($_GPC["repassword"])) {
                imessage(error(-1, "两次密码输入不一致"), referer(), "ajax");
            }
            $data["salt"] = random(6);
            $data["token"] = random(32);
            $data["password"] = md5(md5($data["salt"] . $data["password"]) . $data["salt"]);
            $data["addtime"] = TIMESTAMP;
            pdo_insert("tiny_wmall_deliveryer", $data);
            $id = pdo_insertid();
            deliveryer_all(true);
            imessage(error(0, "添加配送员成功"), iurl("deliveryer/account/post", array("id" => $id)), "ajax");
        } else {
            $password = trim($_GPC["password"]);
            if (!empty($password)) {
                $length = strlen($password);
                if ($length < 8 || 20 < $length) {
                    imessage(error(-1, "请输入8-20密码"), referer(), "ajax");
                }
                if (!preg_match(IREGULAR_PASSWORD, $password)) {
                    imessage(error(-1, "密码必须由数字和字母组合"), referer(), "ajax");
                }
                if ($password != trim($_GPC["repassword"])) {
                    imessage(error(-1, "两次密码输入不一致"), referer(), "ajax");
                }
                $data["salt"] = random(6);
                $data["password"] = md5(md5($data["salt"] . $password) . $data["salt"]);
            }
            pdo_update("tiny_wmall_deliveryer", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
            deliveryer_all(true);
            imessage(error(0, "编辑配送员成功"), iurl("deliveryer/account/post", array("id" => $id)), "ajax");
        }
    }
    include itemplate("deliveryer/account");
}
if ($op == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        pdo_delete("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $id));
        pdo_delete("tiny_wmall_store_deliveryer", array("uniacid" => $_W["uniacid"], "deliveryer_id" => $id));
        pdo_delete("tiny_wmall_deliveryer_current_log", array("uniacid" => $_W["uniacid"], "deliveryer_id" => $id));
        pdo_delete("tiny_wmall_deliveryer_getcash_log", array("uniacid" => $_W["uniacid"], "deliveryer_id" => $id));
    }
    deliveryer_all(true);
    imessage(error(0, "删除配送员成功"), "", "ajax");
    include itemplate("deliveryer/account");
}
if ($op == "lots") {
    if ($_W["is_agent"]) {
        $agents = get_agents();
    }
    if ($_W["ispost"] && $_GPC["set"] == 1) {
        $deliveryerIds = explode(",", $_GPC["deliveryerIds"]);
        if (empty($deliveryerIds)) {
            imessage(error(-1, "请选择需要操作的配送员"), "", "ajax");
        }
        $update = array();
        if ($_W["is_agent"]) {
            $agentid = intval($_GPC["agentid"]);
            if (0 < $agentid) {
                foreach ($deliveryerIds as $val) {
                    update_deliveryer_agent($val, $agentid);
                }
            }
        }
        if (!empty($update)) {
            foreach ($deliveryerIds as $row) {
                pdo_update("tiny_wmall_deliveryer", $update, array("id" => $row, "uniacid" => $_W["uniacid"]));
            }
        }
        imessage(error(0, "批量操作修改成功"), iurl("deliveryer/account"), "ajax");
    }
    $deliveryer_ids = $_GPC["id"];
    if (empty($deliveryer_ids)) {
        imessage(error(-1, "请选择需要操作的配送员"), "", "ajax");
    }
    $deliveryer_ids = implode(",", $deliveryer_ids);
    include itemplate("deliveryer/accountOp");
}

?>