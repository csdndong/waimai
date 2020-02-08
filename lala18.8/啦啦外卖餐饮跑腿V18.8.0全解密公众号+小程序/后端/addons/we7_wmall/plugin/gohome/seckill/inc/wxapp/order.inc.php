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
    if ($_config_plugin["basic"]["status"]["seckill"] != 1) {
        imessage(error(-1, "抢购功能暂时关闭，敬请关注"), "", "ajax");
    }
    $goods_id = intval($_GPC["goods_id"]);
    $goods = seckill_goods($goods_id, "all");
    if (empty($goods)) {
        imessage(error(-1, "商品不存在或已删除"), "", "ajax");
    }
    if ($_W["ispost"]) {
        $username = trim($_GPC["username"]) ? trim($_GPC["username"]) : imessage(error(-1, "请输入核销人姓名"), "", "ajax");
        $mobile = trim($_GPC["mobile"]) ? trim($_GPC["mobile"]) : imessage(error(-1, "请输入预留手机号"), "", "ajax");
        $num = intval($_GPC["goods_num"]) ? intval($_GPC["goods_num"]) : 1;
        if (0 < $goods["total"] && $goods["total"] < $num) {
            imessage(error(-1, "商品库存不足"), "", "");
        }
        $data = array("uniacid" => $_W["uniacid"], "agentid" => $goods["store"]["agentid"], "sid" => $goods["sid"], "goods_id" => $goods["id"], "uid" => $_W["member"]["uid"], "openid" => $_W["member"]["openid"], "order_type" => "seckill", "ordersn" => date("YmdHis") . random(6, true), "price" => $goods["price"] * $num, "num" => $num, "final_fee" => $goods["price"] * $num, "is_pay" => 0, "addtime" => TIMESTAMP, "status" => 1, "code" => random(6, true), "buyremark" => trim($_GPC["buyremark"]), "username" => $username, "mobile" => $mobile, "stat_year" => date("Y", TIMESTAMP), "stat_month" => date("Ym", TIMESTAMP), "stat_day" => date("Ymd", TIMESTAMP));
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
        imessage(error(0, $id), "", "ajax");
    }
    $member = array("username" => $_W["member"]["realname"], "mobile" => $_W["member"]["mobile"]);
    $result = array("goods" => $goods, "member" => $member);
    imessage(error(0, $result), "", "ajax");
}

?>