<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "店铺推广员";
    $condition = " WHERE a.uniacid = :uniacid";
    $params[":uniacid"] = $_W["uniacid"];
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and a.agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (b.uid like '%" . $keyword . "%' or b.nickname like '%" . $keyword . "%' or b.mobile like '%" . $keyword . "%')";
    }
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "status" => 1), array("id", "title"));
    $sid = intval($_GPC["sid"]);
    if (0 < $sid) {
        $storebd_store = pdo_get("tiny_wmall_storebd_store", array("uniacid" => $_W["uniacid"], "sid" => $sid), array("bd_id"));
        $condition .= " and a.id = :id";
        $params[":id"] = $storebd_store["bd_id"];
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_storebd_user") . " as a" . $condition, $params);
    $data = pdo_fetchall("SELECT a.*, b.nickname,b.avatar, b.realname as title, b.mobile FROM " . tablename("tiny_wmall_storebd_user") . " as a left join" . tablename("tiny_wmall_members") . " as b on a.uid = b.uid" . $condition . " ORDER BY a.id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($data)) {
        $stores = store_fetchall(array("id", "title"));
        foreach ($data as &$val) {
            $sids = pdo_getall("tiny_wmall_storebd_store", array("uniacid" => $_W["uniacid"], "bd_id" => $val["id"]), array("sid"));
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
} else {
    if ($op == "post") {
        $_W["page"]["title"] = "店铺推广员";
        $id = intval($_GPC["id"]);
        if (0 < $id) {
            $store_spread = pdo_get("tiny_wmall_storebd_user", array("uniacid" => $_W["uniacid"], "id" => $id));
        }
        if ($_W["ispost"]) {
            $mobile = trim($_GPC["mobile"]);
            if (!is_validMobile($mobile)) {
                imessage(error(-1, "手机号格式错误"), "", "ajax");
            }
            $is_exist = pdo_fetchcolumn("select id from " . tablename("tiny_wmall_storebd_user") . " where uniacid = :uniacid and mobile = :mobile and id != :id", array(":uniacid" => $_W["uniacid"], ":mobile" => $mobile, ":id" => $id));
            if (!empty($is_exist)) {
                imessage(error(-1, "该手机号已绑定其他店员, 请更换手机号"), "", "ajax");
            }
            $openid = trim($_GPC["wechat"]["openid"]);
            $is_exist = pdo_fetchcolumn("select id from " . tablename("tiny_wmall_storebd_user") . " where uniacid = :uniacid and openid = :openid and id != :id", array(":uniacid" => $_W["uniacid"], ":openid" => $openid, ":id" => $id));
            if (!empty($is_exist)) {
                imessage(error(-1, "该微信信息已绑定其他店员, 请更换微信信息"), "", "ajax");
            }
            $data = array("uniacid" => $_W["uniacid"], "mobile" => $mobile, "title" => trim($_GPC["title"]), "openid" => $openid, "openid_wxapp" => trim($_GPC["wechat"]["openid_wxapp"]), "nickname" => trim($_GPC["wechat"]["nickname"]), "avatar" => trim($_GPC["wechat"]["avatar"]));
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
                $data["token"] = random(32);
                $data["salt"] = random(6);
                $data["password"] = md5(md5($data["salt"] . $data["password"]) . $data["salt"]);
                $data["addtime"] = TIMESTAMP;
                pdo_insert("tiny_wmall_storebd_user", $data);
                $id = pdo_insertid();
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
                pdo_update("tiny_wmall_storebd_user", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
            }
            imessage(error(0, "编辑店铺推广员成功"), iurl("storebd/account/post", array("id" => $id)), "ajax");
        }
    } else {
        if ($op == "add") {
            if ($_W["isajax"]) {
                $mobile = trim($_GPC["mobile"]);
                if (empty($mobile)) {
                    imessage(error(-1, "手机号不能为空"), "", "ajax");
                }
                $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "mobile" => $mobile));
                if (empty($member)) {
                    imessage(error(-1, "未找到该手机号对应的会员"), "", "ajax");
                }
                $is_exist = pdo_get("tiny_wmall_storebd_user", array("uniacid" => $_W["uniacid"], "uid" => $member["uid"]));
                if (!empty($is_exist)) {
                    imessage(error(-1, "该手机号对用的会员已经是店铺推广员, 请勿重复添加"), "", "ajax");
                }
                $data = array("uniacid" => $_W["uniacid"], "uid" => $member["uid"], "addtime" => TIMESTAMP);
                pdo_insert("tiny_wmall_storebd_user", $data);
                imessage(error(0, "添加店铺推广员成功"), "", "ajax");
            }
        } else {
            if ($op == "del") {
                $ids = $_GPC["id"];
                if (!is_array($ids)) {
                    $ids = array($ids);
                }
                foreach ($ids as $id) {
                    pdo_delete("tiny_wmall_storebd_user", array("uniacid" => $_W["uniacid"], "id" => $id));
                }
                imessage(error(0, "删除店铺推广员成功"), "", "ajax");
            }
        }
    }
}
include itemplate("account");

?>