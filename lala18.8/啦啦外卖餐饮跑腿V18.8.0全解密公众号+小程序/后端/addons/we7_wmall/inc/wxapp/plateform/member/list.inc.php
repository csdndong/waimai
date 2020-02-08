<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("member");
$_W["page"]["title"] = "顾客列表";
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (uid = :uid or realname like :keyword or mobile like :keyword or nickname like :keyword)";
        $params[":uid"] = $keyword;
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $records = pdo_fetchall("select * from " . tablename("tiny_wmall_members") . $condition . " LIMIT " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($records)) {
        foreach ($records as &$val) {
            $member = get_member($val["uid"]);
            $val["credit1"] = floatval($member["credit1"]);
            $val["credit2"] = floatval($member["credit2"]);
            $val["success_first_time_cn"] = date("Y-m-d H:i", $val["success_first_time"]);
            $val["success_last_time_cn"] = date("Y-m-d H:i", $val["success_last_time"]);
            $val["setmeal_starttime_cn"] = date("Y-m-d", $val["setmeal_starttime"]);
            $val["setmeal_endtime_cn"] = date("Y-m-d", $val["setmeal_endtime"]);
            $val["card"] = tosetmeal($val["setmeal_id"], false);
            if ($val["setmeal_endtime"] <= time()) {
                $val["setmeal_status"] = "已到期";
            } else {
                $val["setmeal_status"] = "未到期";
            }
            $val["avatar"] = tomedia($val["avatar"]);
        }
    }
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "status") {
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
        imessage(error(0, ""), "", "ajax");
    } else {
        if ($ta == "del") {
            $id = intval($_GPC["id"]);
            $uid = intval($_GPC["uid"]);
            if (empty($id) || empty($uid)) {
                imessage(error(-1, "顾客不存在"), "", "ajax");
            }
            pdo_delete("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "id" => $id));
            $tables = array("tiny_wmall_activity_coupon_grant_log", "tiny_wmall_activity_coupon_record", "tiny_wmall_activity_redpacket_record", "tiny_wmall_address", "tiny_wmall_assign_board", "tiny_wmall_creditshop_order", "tiny_wmall_delivery_cards_order", "tiny_wmall_freelunch_partaker", "tiny_wmall_member_footmark", "tiny_wmall_member_invoice", "tiny_wmall_member_recharge", "tiny_wmall_notice_read_log", "tiny_wmall_order_cart", "tiny_wmall_perm_user", "tiny_wmall_report", "tiny_wmall_store_favorite", "tiny_wmall_store_members", "tiny_wmall_superredpacket_grant");
            foreach ($tables as $table) {
                if (pdo_tableexists($table) && pdo_fieldexists($table, "uid")) {
                    pdo_delete($table, array("uniacid" => $_W["uniacid"], "uid" => $uid));
                }
            }
            mlog(6001, $uid);
            imessage(error(0, ""), "", "ajax");
            return 1;
        } else {
            if ($ta == "change") {
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
                    $result = member_credit_update($member["uid"], $type, $amount, $log);
                    if (is_error($result)) {
                        mlog(6002, $member["uid"], "变更失败" . $result["message"] . "。变动类型：" . $credit_text . "。变动方式：" . $change_type . "，金额：" . $amount . "，备注:" . $remark);
                    } else {
                        mlog(6002, $member["uid"], "变更成功。变动类型：" . $credit_text . "。变动方式：" . $change_type . "，金额：" . $amount . "，备注:". $remark);
                    }
                    imessage(error(0, (string) $credit_text . "变动成功"), "", "ajax");
                }
                $result = array("member" => $member);
                imessage(error(0, $result), "", "ajax");
            }
        }
    }
}

?>