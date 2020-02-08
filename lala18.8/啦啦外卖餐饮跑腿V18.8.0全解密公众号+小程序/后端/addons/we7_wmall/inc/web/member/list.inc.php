<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("member");
$_W["page"]["title"] = "顾客列表";
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $key = trim($_GPC["key"]);
    if (!empty($key)) {
        $time = strtotime("-30 days");
        if ($key == "success_30") {
            $condition .= " and success_last_time >= :time";
        } else {
            if ($key == "noorder_30") {
                $condition .= " and success_last_time < :time";
            } else {
                if ($key == "cancel_30") {
                    $condition .= " and cancel_last_time >= :time";
                }
            }
        }
        $params[":time"] = $time;
    }
    $groupid = intval($_GPC["groupid"]);
    if (0 < $groupid) {
        $condition .= " and groupid = :groupid";
        $params[":groupid"] = $groupid;
    }
    $svip_status = isset($_GPC["svip_status"]) ? intval($_GPC["svip_status"]) : "-1";
    if (-1 < $svip_status) {
        $condition .= " and svip_status = :svip_status";
        $params[":svip_status"] = $svip_status;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (uid = :uid or realname like :keyword or mobile like :keyword or nickname like :keyword)";
        $params[":uid"] = $keyword;
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $sort = trim($_GPC["sort"]);
    $sort_val = intval($_GPC["sort_val"]);
    if (!empty($sort)) {
        if ($sort_val == 1) {
            $condition .= " ORDER BY " . $sort . " DESC";
        } else {
            $condition .= " ORDER BY " . $sort . " ASC";
        }
    } else {
        $condition .= " order by id desc";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_members") . $condition, $params);
    $data = pdo_fetchall("select * from " . tablename("tiny_wmall_members") . $condition . " LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($data)) {
        foreach ($data as &$val) {
            $groupname = pdo_get("tiny_wmall_member_groups", array("uniacid" => $_W["uniacid"], "id" => $val["groupid"]));
            $member = get_member($val["uid"]);
            $val["credit1"] = floatval($member["credit1"]);
            $val["credit2"] = $member["credit2"];
            $val["card"] = tosetmeal($val["setmeal_id"], false);
            $val["groupname"] = $groupname["title"];
        }
    }
    $pager = pagination($total, $pindex, $psize);
    $groups = pdo_fetchall("select * from" . tablename("tiny_wmall_member_groups") . "where uniacid = :uniacid ", array(":uniacid" => $_W["uniacid"]));
}
if ($op == "sync") {
    if ($_W["isajax"]) {
        $uid = intval($_GPC["__input"]["uid"]);
        $member = pdo_get("tiny_wmall_members", array("uid" => $uid));
        if (!empty($member)) {
            $data = array();
            if (strexists($member["avatar"], "/132132")) {
                $data["avatar"] = str_replace("/132132", "/132", $member["avatar"]);
            }
            if ($member["sex"] == "1" || $member["sex"] == "2") {
                $data["sex"] = $member["sex"] == "1" ? "男" : "女";
            }
            pdo_update("tiny_wmall_members", $data, array("uid" => $uid));
        }
        $update = array();
        $update["success_num"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and uid = :uid and is_pay = 1 and status = 5", array(":uniacid" => $_W["uniacid"], ":uid" => $uid)));
        $update["success_price"] = floatval(pdo_fetchcolumn("select sum(final_fee) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and uid = :uid and is_pay = 1 and status = 5", array(":uniacid" => $_W["uniacid"], ":uid" => $uid)));
        $update["cancel_num"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and uid = :uid and status = 6", array(":uniacid" => $_W["uniacid"], ":uid" => $uid)));
        $update["cancel_price"] = floatval(pdo_fetchcolumn("select sum(final_fee) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and uid = :uid and status = 6", array(":uniacid" => $_W["uniacid"], ":uid" => $uid)));
        pdo_update("tiny_wmall_members", $update, array("uniacid" => $_W["uniacid"], "uid" => $uid));
        message(error(0, ""), "", "ajax");
    }
    $uids = pdo_getall("tiny_wmall_members", array("uniacid" => $_W["uniacid"]), array("uid"), "uid");
    $uids = array_keys($uids);
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $status = intval($_GPC["status"]);
    pdo_update("tiny_wmall_members", array("status" => $status), array("id" => $id, "uniacid" => $_W["uniacid"]));
    if ($status == 1) {
        mlog(6007, intval($_GPC["id"]));
    } else {
        if ($status == 0) {
            mlog(6000, intval($_GPC["id"]));
        }
    }
    imessage(error(0, "修改成功"), referer(), "ajax");
}
if ($op == "cancel") {
    $id = intval($_GPC["id"]);
    pdo_update("tiny_wmall_members", array("setmeal_endtime" => time()), array("id" => $id, "uniacid" => $_W["uniacid"]));
    mlog(6005, intval($_GPC["uid"]), "取消配送会员卡套餐");
    imessage(error(0, "套餐取消成功"), referer(), "ajax");
}
if ($op == "changes") {
    $uid = intval($_GPC["uid"]);
    $member = get_member($uid);
    if (empty($member)) {
        imessage(error(-1, "会员不存在或已经删除"), referer(), "ajax");
    }
    if ($_W["ispost"]) {
        $type = trim($_GPC["type"]);
        $change_type = intval($_GPC["change_type"]);
        $amount = floatval($_GPC["amount"]);
        $remark = trim($_GPC["remark"]);
        $credit = $member["credit1"];
        $credit_text = "积分";
        if ($type == "credit2") {
            $credit = $member["credit2"];
            $credit_text = "余额";
        }
        if ($type == "svip_credit1") {
            $credit = $member["svip_credit1"];
            $credit_text = "奖励金";
        }
        if ($change_type == 1) {
            $amount = "+" . $amount;
        } else {
            if ($change_type == 2) {
                $amount = "-" . $amount;
                if ($credit - $amount < 0) {
                    $amount = "-" . $credit;
                }
            } else {
                if ($change_type == 3) {
                    $amount = $amount - $credit;
                }
            }
        }
        $log = array($member["uid"], $remark);
        if ($type == "svip_credit1") {
            mload()->model("plugin");
            pload()->model("svip");
            $result = svip_member_svip_credit1_update($uid, $amount, "平台修改", $remark);
        } else {
            $result = member_credit_update($member["uid"], $type, $amount, $log);
        }
        if (is_error($result)) {
            mlog(6002, $member["uid"], "变更失败!" . $result["message"] . "。变动类型：" . $credit_text . "。变动方式:" . $change_type . "，金额：" . $amount . "。备注:" . $remark);
        } else {
            mlog(6002, $member["uid"], "变更成功。变动类型：" . $credit_text . "。变动方式:" . $change_type . "，金额：" . $amount . "。备注:" . $remark);
        }
        imessage(error(0, (string) $credit_text . "变动成功"), referer(), "ajax");
    }
    include itemplate("member/op");
    exit;
}
if ($op == "setmeal") {
    $id = intval($_GPC["id"]);
    $setmeals = pdo_fetchall("select id, uniacid, title from" . tablename("tiny_wmall_delivery_cards") . "where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]));
    $data = pdo_fetch("select id, uniacid, setmeal_starttime, setmeal_endtime, setmeal_day_free_limit, setmeal_deliveryfee_free_limit from" . tablename("tiny_wmall_members") . " where id = :id and uniacid = :uniacid", array(":id" => $id, ":uniacid" => $_W["uniacid"]));
    if ($data["setmeal_endtime"] <= TIMESTAMP) {
        $data["setmeal_starttime"] = TIMESTAMP;
        $data["setmeal_endtime"] = strtotime("+1 months");
        $data["setmeal_day_free_limit"] = 1;
    }
    if ($_W["ispost"]) {
        if (empty($id)) {
            imessage(error(-1, "请选择需要修改的会员"), referer(), "ajax");
        }
        $setmeal_day_free_limit = intval($_GPC["free"]);
        pdo_update("tiny_wmall_members", array("setmeal_id" => intval($_GPC["setmeal"]), "setmeal_starttime" => strtotime($_GPC["setmeal_starttime"]), "setmeal_endtime" => strtotime($_GPC["setmeal_endtime"]), "setmeal_day_free_limit" => $_GPC["free"], "setmeal_deliveryfee_free_limit" => intval($_GPC["deliveryfee"])), array("uniacid" => $_W["uniacid"], "id" => $id));
        mlog(6005, intval($_GPC["uid"]), "套餐id:" . $_GPC["setmeal"] . "，%套餐开售,结束" . $_GPC["setmeal_starttime"] . "-" . $_GPC["setmeal_endtime"] . "，每天可享受免费配送次数：" . $_GPC["free"] . "每单最多：" . $_GPC["deliveryfee"] . "次");
        imessage(error(0, "套餐修改成功"), referer(), "ajax");
    }
    include itemplate("member/listOp");
    exit;
}
if ($op == "group") {
    $uid = intval($_GPC["uid"]);
    $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $uid), "groupid");
    $groups = pdo_fetchall("select id, title from" . tablename("tiny_wmall_member_groups") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]));
    if ($_W["ispost"]) {
        if (empty($uid)) {
            imessage(error(-1, "请选择需要修改的会员"), referer(), "ajax");
        }
        $groupid = intval($_GPC["groupid"]);
        if (empty($groupid)) {
            imessage(error(-1, "请选择要修改的会员等级"), referer(), "ajax");
        }
        pdo_update("tiny_wmall_members", array("groupid" => $groupid), array("uniacid" => $_W["uniacid"], "uid" => $uid));
        mlog(6004, $uid, $groupid);
	imessage(error(0, "会员等级修改成功"), referer(), "ajax");
    }
    include itemplate("member/listOp");
    exit;
}
if ($op == "del") {
    $id = intval($_GPC["id"]);
    $uid = intval($_GPC["uid"]);
    if (empty($id) || empty($uid)) {
        imessage(error(0, "顾客不存在"), referer(), "ajax");
    }
    pdo_delete("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "id" => $id));
    $tables = array("tiny_wmall_activity_coupon_grant_log", "tiny_wmall_activity_coupon_record", "tiny_wmall_activity_redpacket_record", "tiny_wmall_address", "tiny_wmall_assign_board", "tiny_wmall_creditshop_order", "tiny_wmall_delivery_cards_order", "tiny_wmall_freelunch_partaker", "tiny_wmall_member_footmark", "tiny_wmall_member_invoice", "tiny_wmall_member_recharge", "tiny_wmall_notice_read_log", "tiny_wmall_order_cart", "tiny_wmall_perm_user", "tiny_wmall_report", "tiny_wmall_store_favorite", "tiny_wmall_store_members", "tiny_wmall_superredpacket_grant");
    foreach ($tables as $table) {
        if (pdo_tableexists($table) && pdo_fieldexists($table, "uid")) {
            pdo_delete($table, array("uniacid" => $_W["uniacid"], "uid" => $uid));
        }
    }
    mlog(6001, $uid);
    imessage(error(0, "删除成功"), referer(), "ajax");
}
if ($op == "info") {
    $uid = intval($_GPC["uid"]);
    $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $uid));
    if (empty($member)) {
        imessage(error(-1, "顾客不存在或已被删除"), referer(), "error");
    }
    if ($_W["ispost"]) {
        $member = array("nickname" => trim($_GPC["nickname"]), "realname" => trim($_GPC["realname"]), "mobile" => trim($_GPC["mobile"]), "salt" => $member["salt"]);
        $mobile = $member["mobile"];
        if (!is_validMobile($mobile)) {
            imessage(error(-1, "手机号格式错误"), "", "ajax");
        }
        $is_exist = pdo_fetchcolumn("select id from " . tablename("tiny_wmall_members") . " where uniacid = :uniacid and mobile = :mobile and uid != :uid", array(":uniacid" => $_W["uniacid"], ":mobile" => $mobile, ":uid" => $uid));
        if (!empty($is_exist)) {
            imessage(error(-1, "该手机号已绑定其他顾客, 请更换手机号"), "", "ajax");
        }
        if (!empty($_GPC["password"])) {
            $password = trim($_GPC["password"]);
            $length = strlen($password);
            if ($length < 8 || 20 < $length) {
                imessage(error(-1, "请输入8-20密码"), "", "ajax");
            }
            if (!preg_match(IREGULAR_PASSWORD, $password)) {
                imessage(error(-1, "密码必须由数字和字母组合"), "", "ajax");
            }
            $member["password"] = md5(md5($member["salt"] . trim($password)) . $member["salt"]);
        }
        pdo_update("tiny_wmall_members", $member, array("uid" => $uid));
        mlog(6003, $uid);
        imessage(error(0, "编辑成功"), referer(), "ajax");
    }
}
if ($op == "svip_status") {
    $uid = intval($_GPC["uid"]);
    $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $uid));
    if (empty($member)) {
        imessage(error(-1, "顾客不存在或已被删除"), referer(), "error");
    }
    if ($_W["ispost"]) {
        $status = intval($_GPC["status"]);
        if ($status == "1") {
            $svip_endtime = strtotime(trim($_GPC["svip_endtime"]));
            if ($svip_endtime < TIMESTAMP) {
                imessage(error(-1, "会员开始时间不能大于结束时间"), referer(), "ajax");
            }
            if ($member["svip_status"] == "1") {
                $data = array("svip_endtime" => $svip_endtime);
            } else {
                if ($member["svip_status"] == "2") {
                    $data = array("svip_status" => "1", "svip_endtime" => $svip_endtime);
                } else {
                    $data = array("svip_status" => "1", "svip_starttime" => TIMESTAMP, "svip_endtime" => $svip_endtime);
                }
            }
            pdo_update("tiny_wmall_members", $data, array("uniacid" => $_W["uniacid"], "uid" => $uid));
            mlog(6006, $uid, "设置开启超级会员，结束时间:" . $_GPC["svip_endtime"]);
            
	    imessage(error(0, "续费超级会员成功"), referer(), "ajax");
        } else {
            if ($status == "2") {
                if ($member["svip_status"] == "0" || $member["svip_status"] == "2") {
                    imessage(error(0, "撤销超级会员成功"), referer(), "ajax");
                } else {
                    $data = array("svip_status" => 2, "svip_endtime" => TIMESTAMP);
                    pdo_update("tiny_wmall_members", $data, array("uniacid" => $_W["uniacid"], "uid" => $uid));
                }
                mlog(6006, $uid, "设置撤销超级会员");
		imessage(error(0, "撤销超级会员成功"), referer(), "ajax");
            }
        }
    }
    if (!empty($member["svip_status"])) {
        $svip_endtime = $member["svip_endtime"];
    } else {
        $svip_endtime = TIMESTAMP;
    }
    include itemplate("member/listOp");
    exit;
}
include itemplate("member/list");

?>