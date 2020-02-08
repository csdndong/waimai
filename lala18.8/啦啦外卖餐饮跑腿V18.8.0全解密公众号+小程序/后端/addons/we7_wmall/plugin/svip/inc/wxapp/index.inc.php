<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "meal";
icheckauth();
if ($_config_plugin["basic"]["status"] != 1) {
    imessage(error(-1, "超级会员功能未开启"), "", "ajax");
}
if ($op == "index") {
    $member = $_W["member"];
    if ($member["svip_status"] == 1) {
        imessage(error(-2, ""), "", "ajax");
    }
    $filter = array("status" => 1, "psize" => 10);
    $redpackets = svip_redpacket_fetchall($filter);
    $tasks = svip_task_getall(array("status" => 1, "psize" => 3));
    $result = array("redpackets" => $redpackets["redpackets"], "tasks" => $tasks["tasks"], "agreement" => get_config_text("agreement_svip"), "config" => array("exchange_max" => intval($_config_plugin["basic"]["exchange_max"])));
    imessage(error(0, $result), "", "ajax");
} else {
    if ($op == "meal") {
        $member = $_W["member"];
        $member["svip_endtime_cn"] = date("Y-m-d", $member["svip_endtime"]);
        $result = array("meals" => svip_meal_getall(array("status" => 1)), "member" => $member, "agreement" => get_config_text("agreement_svip"));
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($op == "buy") {
            $id = intval($_GPC["id"]);
            $meal = svip_meal_get($id);
            if (empty($meal)) {
                imessage(error(-1, "套餐不存在"), "", "ajax");
            }
            $order = array("uniacid" => $_W["uniacid"], "acid" => $_W["acid"], "uid" => $_W["member"]["uid"], "openid" => $_W["openid"], "ordersn" => date("YmdHis") . random(6, true), "meal_id" => $meal["id"], "final_fee" => $meal["price"], "is_pay" => 0, "order_channel" => $_W["ochannel"] == "wxapp" ? "wxapp" : "wechat", "addtime" => TIMESTAMP, "data" => iserializer(array("days" => $meal["days"])));
            pdo_insert("tiny_wmall_svip_meal_order", $order);
            $id = pdo_insertid();
            imessage(error(0, array("id" => $id)), "", "ajax");
        }
    }
}

?>