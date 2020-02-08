<?php
/*

 * @开源学习用
 * @Popping
 * 源码仅供研究学习，请勿用于商业用途
 */

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
icheckauth(true);
if ($op == "index") {
    if ($_config_plugin["basic"]["status"]["pintuan"] != 1) {
        imessage(error(-1, "拼团功能暂时关闭，敬请关注"), "", "ajax");
    }
    $id = intval($_GPC["id"]);
    $is_team = intval($_GPC["is_team"]);
    if ($is_team) {
        $record = pintuan_get_member_takepart($id);
        if (!empty($record)) {
            imessage(error(-1001, $record), "", "ajax");
        }
    }
    $team_id = intval($_GPC["team_id"]);
    $pintuan_goods = pintuan_get_activity($id);
    if (is_error($pintuan_goods)) {
        imessage($pintuan_goods, "", "ajax");
    }
    $available = pintuan_is_available($pintuan_goods, $team_id);
    if (is_error($available)) {
        imessage($available, "", "ajax");
    }
    $params = json_decode(htmlspecialchars_decode($_GPC["extra"]), true);
    $params["is_team"] = $is_team;
    $order = pintuan_order_calculate($pintuan_goods, $params);
    $_W["_share"] = array("title" => $pintuan_goods["share"]["share_title"], "desc" => $pintuan_goods["share"]["share_detail"], "imgUrl" => tomedia($pintuan_goods["share"]["share_thumb"]), "link" => ivurl("/gohome/pages/pintuan/detail", array("id" => $pintuan_goods["id"], "share_uid" => $_W["member"]["uid"]), true));
    $result = array("goods" => $pintuan_goods, "order" => $order);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($op == "create") {
        $id = intval($_GPC["id"]);
        $team_id = intval($_GPC["team_id"]);
        $pintuan_goods = pintuan_get_activity($id);
        if (is_error($pintuan_goods)) {
            imessage($pintuan_goods, "", "ajax");
        }
        $available = pintuan_is_available($pintuan_goods, $team_id);
        if (is_error($available)) {
            imessage($available, "", "ajax");
        }
        $params = json_decode(htmlspecialchars_decode($_GPC["extra"]), true);
        $order = pintuan_order_calculate($pintuan_goods, $params);
        $address = $order["address"];
        if ($_W["ispost"]) {
            $order = array("uniacid" => $_W["uniacid"], "agentid" => $order["store"]["agentid"], "sid" => $order["store"]["id"], "uid" => $_W["member"]["uid"], "ordersn" => date("YmdHis") . random(6, true), "code" => random(6, true), "is_team" => $team_id ? 1 : 0, "team_id" => $team_id, "order_type" => "pintuan", "goods_id" => $id, "openid" => $_W["openid"], "mobile" => !empty($params["mobile"]) ? trim($params["mobile"]) : $_W["member"]["mobile"], "username" => !empty($params["username"]) ? trim($params["username"]) : $_W["member"]["realname"], "address" => $pintuan_goods["usetype"] == 2 ? $order["address"]["address"] . $order["address"]["number"] : "", "pay_type" => "", "num" => $order["goods_num"], "goodsprice" => $order["goods_price"], "price" => $order["final_fee"], "discount_fee" => $order["discount_real"], "final_fee" => $order["final_fee"], "status" => 1, "addtime" => TIMESTAMP, "buyremark" => $order["remark"], "stat_year" => date("Y", TIMESTAMP), "stat_month" => date("Ym", TIMESTAMP), "stat_day" => date("Ymd", TIMESTAMP));
            if ($order["is_team"] == 1) {
                $order["team_num"] = $pintuan_goods["peoplenum"];
            }
            if (0 < $pintuan_goods["grouptime"]) {
                $order["overtime"] = $order["addtime"] + $pintuan_goods["grouptime"] * 3600;
            }
            if (0 < $team_id) {
                $order["overtime"] = $available["overtime"];
            }
            $order["spreadbalance"] = 1;
            if (check_plugin_perm("spread")) {
                pload()->model("spread");
                $order = order_spread_commission_calculate("gohome", $order);
            }
            if (!empty($order["data"])) {
                $order["data"] = iserializer($order["data"]);
            }
            pdo_insert("tiny_wmall_gohome_order", $order);
            $order_id = pdo_insertid();
            gohome_goods_total_update($order, 0);
            gohome_order_update_bill($order_id);
            if ($params["is_team"] == 1 && empty($team_id)) {
                $update = array("is_team" => 1, "team_id" => $order_id, "team_num" => $pintuan_goods["peoplenum"]);
                pdo_update("tiny_wmall_gohome_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order_id));
            }
            imessage(error(0, $order_id), "", "ajax");
        }
    }
}

?>