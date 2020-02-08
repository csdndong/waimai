<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth(true);
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "create";
if ($op == "create") {
    $activityid = intval($_GPC["activityid"]);
    $activity = kanjia_get_activity($activityid, "all");
    if (empty($activity)) {
        imessage(error(-1, "活动不存在或已删除"), "", "ajax");
    }
    $takeinfo = kanjia_member_takeinfo($activityid);
    if (empty($takeinfo)) {
        imessage(error(-1, "您暂未参与该商品的砍价活动，无法提交订单"), "", "ajax");
    }
    if ($activity["submitmoneylimit"] < $takeinfo["price"]) {
        imessage(error(-1, "当前商品的金额高于允许下单的金额，请继续邀请好友帮您砍价"), "", "ajax");
    }
    if ($_W["ispost"]) {
        $username = trim($_GPC["username"]) ? trim($_GPC["username"]) : imessage(error(-1, "请输入核销人姓名"), "", "ajax");
        $mobile = trim($_GPC["mobile"]) ? trim($_GPC["mobile"]) : imessage(error(-1, "请输入预留手机号"), "", "ajax");
        $data = array("uniacid" => $_W["uniacid"], "agentid" => $activity["store"]["agentid"], "sid" => $activity["sid"], "goods_id" => $activity["id"], "uid" => $_W["member"]["uid"], "openid" => $_W["member"]["openid"], "order_type" => "kanjia", "userid" => $takeinfo["id"], "ordersn" => date("YmdHis") . random(6, true), "price" => $takeinfo["price"], "num" => 1, "discount_fee" => 0, "final_fee" => $takeinfo["price"], "is_pay" => 0, "addtime" => TIMESTAMP, "status" => 1, "code" => random(6, true), "buyremark" => trim($_GPC["buyremark"]), "username" => $username, "mobile" => $mobile, "stat_year" => date("Y", TIMESTAMP), "stat_month" => date("Ym", TIMESTAMP), "stat_day" => date("Ymd", TIMESTAMP));
        $data["spreadbalance"] = 1;
        if (check_plugin_perm("spread")) {
            pload()->model("spread");
            $data = order_spread_commission_calculate("gohome", $data);
        }
        if (!empty($data["data"])) {
            $data["data"] = iserializer($data["data"]);
        }
        pdo_insert("tiny_wmall_gohome_order", $data);
        $id = pdo_insertid();
        gohome_goods_total_update($data, 0);
        gohome_order_update_bill($id);
        pdo_update("tiny_wmall_kanjia_userlist", array("status" => 2, "updatetime" => TIMESTAMP, "orderid" => $id), array("id" => $takeinfo["id"]));
        imessage(error(0, $id), "", "ajax");
    }
    $member = array("username" => $_W["member"]["realname"], "mobile" => $_W["member"]["mobile"]);
    $result = array("activity" => $activity, "takeinfo" => $takeinfo, "member" => $member);
    imessage(error(0, $result), "", "ajax");
}

?>