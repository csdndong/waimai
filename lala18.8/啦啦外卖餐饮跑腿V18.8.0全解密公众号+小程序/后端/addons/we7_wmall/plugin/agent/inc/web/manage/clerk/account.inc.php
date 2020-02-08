<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "店员账户";
    $condition = " WHERE uniacid = :uniacid and agentid = :agentid";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (title like '%" . $keyword . "%' or nickname like '%" . $keyword . "%' or mobile like '%" . $keyword . "%')";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_clerk") . $condition, $params);
    $data = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_clerk") . $condition . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($data)) {
        $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title"), "id");
        foreach ($data as &$val) {
            $sids = pdo_getall("tiny_wmall_store_clerk", array("uniacid" => $_W["uniacid"], "clerk_id" => $val["id"]), array("sid"));
            $val["stores_title"] = "暂无门店";
            if (!empty($sids)) {
                foreach ($sids as $sid) {
                    $stores_title[] = $stores[$sid["sid"]]["title"];
                }
                if (!empty($val["stores_title"])) {
                    $val["stores_title"] = implode("，", $stores_title);
                }
                unset($stores_title);
            }
        }
    }
    $pager = pagination($total, $pindex, $psize);
}
if ($op == "post") {
    $_W["page"]["title"] = "添加店员";
    $id = intval($_GPC["id"]);
    if (0 < $id) {
        $clerk = pdo_get("tiny_wmall_clerk", array("uniacid" => $_W["uniacid"], "id" => $id));
    }
    if ($_W["ispost"]) {
        $mobile = trim($_GPC["mobile"]);
        if (!is_validMobile($mobile)) {
            imessage(error(-1, "手机号格式错误"), "", "ajax");
        }
        $is_exist = pdo_fetchcolumn("select id from " . tablename("tiny_wmall_clerk") . " where uniacid = :uniacid and mobile = :mobile and id != :id", array(":uniacid" => $_W["uniacid"], ":mobile" => $mobile, ":id" => $id));
        if (!empty($is_exist)) {
            imessage(error(-1, "该手机号已绑定其他店员, 请更换手机号"), "", "ajax");
        }
        $openid = trim($_GPC["wechat"]["openid"]);
        if (!empty($openid)) {
            $is_exist = pdo_fetchcolumn("select id from " . tablename("tiny_wmall_clerk") . " where uniacid = :uniacid and openid = :openid and id != :id", array(":uniacid" => $_W["uniacid"], ":openid" => $openid, ":id" => $id));
            if (!empty($is_exist)) {
                imessage(error(-1, "该微信信息已绑定其他店员, 请更换微信信息"), "", "ajax");
            }
        }
        $openid_wxapp = trim($_GPC["wechat"]["openid_wxapp"]);
        if (!empty($openid_wxapp)) {
            $is_exist = pdo_fetchcolumn("select id from " . tablename("tiny_wmall_clerk") . " where uniacid = :uniacid and openid_wxapp = :openid_wxapp and id != :id", array(":uniacid" => $_W["uniacid"], ":openid_wxapp" => $openid_wxapp, ":id" => $id));
            if (!empty($is_exist)) {
                imessage(error(-1, "该微信信息已绑定其他店员, 请更换微信信息"), "", "ajax");
            }
        }
        $data = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "mobile" => $mobile, "title" => trim($_GPC["title"]), "openid" => $openid, "openid_wxapp" => trim($_GPC["wechat"]["openid_wxapp"]), "nickname" => trim($_GPC["wechat"]["nickname"]), "avatar" => trim($_GPC["wechat"]["avatar"]));
        if (!$id) {
            $data["password"] = trim($_GPC["password"]) ? trim($_GPC["password"]) : imessage(error(-1, "登录密码不能为空"), "", "ajax");
            $length = strlen($data["password"]);
            if ($length < 8 || 20 < $length) {
                imessage(error(-1, "请输入8-20密码"), "", "ajax");
            }
            if (!preg_match(IREGULAR_PASSWORD, $data["password"])) {
                imessage(error(-1, "密码必须由数字和字母组合"), "", "ajax");
            }
            if ($data["password"] != trim($_GPC["repassword"])) {
                imessage(error(-1, "两次密码输入不一致"), "", "ajax");
            }
            $data["salt"] = random(6);
            $data["password"] = md5(md5($data["salt"] . $data["password"]) . $data["salt"]);
            $data["token"] = random(32);
            $data["addtime"] = TIMESTAMP;
            pdo_insert("tiny_wmall_clerk", $data);
            $id = pdo_insertid();
            mlog(3000, $id, "代理添加店员");
        } else {
            $password = trim($_GPC["password"]);
            if (!empty($password)) {
                $length = strlen($password);
                if ($length < 8 || 20 < $length) {
                    imessage(error(-1, "请输入8-20密码"), "", "ajax");
                }
                if (!preg_match(IREGULAR_PASSWORD, $password)) {
                    imessage(error(-1, "密码必须由数字和字母组合"), "", "ajax");
                }
                if ($password != trim($_GPC["repassword"])) {
                    imessage(error(-1, "两次密码输入不一致"), "", "ajax");
                }
                $data["salt"] = random(6);
                $data["password"] = md5(md5($data["salt"] . $password) . $data["salt"]);
            }
            pdo_update("tiny_wmall_clerk", $data, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
            mlog(3001, $id);
        }
        imessage(error(0, "编辑店员成功"), iurl("clerk/account/post", array("id" => $id)), "ajax");
    }
}
if ($op == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        pdo_delete("tiny_wmall_clerk", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
        pdo_delete("tiny_wmall_store_clerk", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "clerk_id" => $id));
        mlog(3002, $id, "代理删除店员");
    }
    imessage(error(0, "删除店员成功"), "", "ajax");
}
include itemplate("clerk/account");

?>