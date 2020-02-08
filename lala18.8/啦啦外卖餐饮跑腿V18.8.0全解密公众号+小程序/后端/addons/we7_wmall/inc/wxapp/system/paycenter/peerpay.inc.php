<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "paytype";
if (!is_weixin()) {
    imessage(error(-1, "请在微信中访问该链接"), "", "ajax");
}
icheckauth();
$config_peerpay = $_W["we7_wmall"]["config"]["payment"]["peerpay"];
if ($ta == "paytype" || $ta == "message") {
    $plid = intval($_GPC["id"]);
    $paylog = pdo_get("tiny_wmall_paylog", array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "id" => $plid));
    if (empty($paylog)) {
        imessage(error(-1, "订单交易记录不存在"), "", "ajax");
    }
    $member = get_member($paylog["uid"]);
}
if ($ta == "message") {
    $peerpay = pdo_get("tiny_wmall_order_peerpay", array("uniacid" => $_W["uniacid"], "plid" => $paylog["id"]));
    if ($_W["ispost"]) {
        $message = trim($_GPC["message"]);
        if (!empty($message)) {
            pdo_update("tiny_wmall_order_peerpay", array("peerpay_message" => $message), array("uniacid" => $_W["uniacid"], "id" => $peerpay["id"]));
            imessage(error(0, "编辑留言成功"), "", "ajax");
        }
    }
    $type = intval($_GPC["type"]);
    if (empty($peerpay)) {
        $peerpay = array("uniacid" => $_W["uniacid"], "uid" => $paylog["uid"], "plid" => $paylog["id"], "orderid" => $paylog["order_id"], "peerpay_type" => $type, "peerpay_price" => $paylog["fee"], "peerpay_realprice" => $paylog["fee"], "createtime" => TIMESTAMP, "data" => $paylog["data"], "peerpay_message" => $config_peerpay["help_words"][0]);
        if ($peerpay["peerpay_type"] == 1) {
            $peerpay["peerpay_selfpay"] = $config_peerpay["peerpay_max_limit"];
        }
        pdo_insert("tiny_wmall_order_peerpay", $peerpay);
        $peerpay["id"] = pdo_insertid();
    }
    pdo_update("tiny_wmall_order", array("pay_type" => "peerpay"), array("uniacid" => $_W["uniacid"], "id" => $peerpay["orderid"]));
    $peerpay["data"] = iunserializer($peerpay["data"]);
    $peerpay["data"]["logo"] = tomedia($peerpay["data"]["logo"]);
    $result = array("member_avatar" => tomedia($member["avatar"]), "peerpay" => $peerpay, "help_words" => !empty($config_peerpay["help_words"]) ? shuffle($config_peerpay["help_words"]) : "", "page_title" => "编辑代付留言");
    imessage(error(0, $result), "", "ajax");
}
if ($ta == "paylist") {
    $id = intval($_GPC["id"]);
    $peerpay = pdo_get("tiny_wmall_order_peerpay", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($peerpay)) {
        $payinfo_id = intval($_GPC["payinfo_id"]);
        $payinfo = pdo_get("tiny_wmall_order_peerpay_payinfo", array("uniacid" => $_W["uniacid"], "id" => $payinfo_id));
        $peerpay = pdo_get("tiny_wmall_order_peerpay", array("uniacid" => $_W["uniacid"], "id" => $payinfo["pid"]));
    }
    if (empty($peerpay)) {
        imessage(error(-1, "代付记录不存在"), "", "ajax");
    }
    $peerpay["data"] = iunserializer($peerpay["data"]);
    $paylog = pdo_get("tiny_wmall_paylog", array("uniacid" => $_W["uniacid"], "id" => $peerpay["plid"]));
    if (empty($paylog)) {
        imessage(error(-1, "订单交易记录不存在"), "", "ajax");
    }
    $order = pdo_get("tiny_wmall_order", array("uniacid" => $_W["uniacid"], "id" => $peerpay["orderid"]), array("id", "status", "addtime"));
    if (empty($order)) {
        imessage(error(-1, "订单不存在"), "", "ajax");
    }
    if ($order["status"] == 6) {
        imessage(error(-1, "订单已取消,不能进行代付了"), "", "ajax");
    }
    $config_takeout = $_W["we7_wmall"]["config"]["takeout"]["order"];
    if (is_array($config_takeout) && 0 < $config_takeout["pay_time_limit"]) {
        $order["pay_endtime"] = $order["addtime"] + $config_takeout["pay_time_limit"] * 60;
        $order["pay_endtime_cn"] = date("Y/m/d H:i:s", $order["pay_endtime"]);
        if ($order["pay_endtime"] < TIMESTAMP) {
            $order["pay_endtime"] = 0;
        }
    }
    $member = get_member($paylog["uid"]);
    $peerpay["peerpay_realprice"] = floatval($peerpay["peerpay_realprice"]);
    $differ = $peerpay["peerpay_price"] - $peerpay["peerpay_realprice"];
    $percent = round(($peerpay["peerpay_price"] - $peerpay["peerpay_realprice"]) / $peerpay["peerpay_price"], 2) * 100;
    $payinfos = pdo_getall("tiny_wmall_order_peerpay_payinfo", array("pid" => $peerpay["id"], "is_pay" => 1));
    $member["avatar"] = tomedia($member["avatar"]);
    $peerpay["data"]["logo"] = tomedia($peerpay["data"]["logo"]);
    $result = array("member" => $member, "peerpay" => $peerpay, "payinfos" => $payinfos, "differ" => $differ, "percent" => $percent, "page_title" => empty($member["nickname"]) ? "我的代付" : (string) $member["nickname"] . "的代付", "is_same_person" => $_W["member"]["uid"] == $member["uid"] ? 1 : 0);
    $title = "亲爱哒，帮我付一下呗~";
    if (!empty($config_peerpay["help_words"])) {
        shuffle($config_peerpay["help_words"]);
        $title = array_pop($config_peerpay["help_words"]);
    }
    $_W["_share"] = array("title" => $title, "desc" => $peerpay["peerpay_message"], "imgUrl" => tomedia($peerpay["data"]["logo"]), "link" => ivurl("pages/public/peerpay/paylist", array("id" => $peerpay["id"]), true));
    imessage(error(0, $result), "", "ajax");
}
if ($ta == "payment") {
    $id = intval($_GPC["id"]);
    $peerpay = pdo_get("tiny_wmall_order_peerpay", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($peerpay)) {
        imessage(error(-1, "代付记录不存在"), "", "ajax");
    }
    if ($peerpay["status"] == 1) {
        imessage(error(-1, "该订单已代付成功"), "", "ajax");
    }
    $peerpay["peerpay_selfpay"] = floatval($peerpay["peerpay_selfpay"]);
    if (empty($peerpay["peerpay_selfpay"])) {
        $peerpay["peerpay_selfpay"] = $peerpay["peerpay_realprice"];
    }
    $paylog = pdo_get("tiny_wmall_paylog", array("uniacid" => $_W["uniacid"], "id" => $peerpay["plid"]));
    if (empty($paylog)) {
        imessage(error(-1, "订单交易记录不存在"), "", "ajax");
    }
    if ($_W["ispost"]) {
        $fee = floatval($_GPC["val"]);
        if (empty($fee)) {
            imessage(error(-1, "代付金额必须大于0"), "", "ajax");
        }
        $insert = array("uniacid" => $_W["uniacid"], "pid" => $peerpay["id"], "uid" => $_W["member"]["uid"], "headimg" => $_W["member"]["avatar"], "openid" => $_W["member"]["openid"], "uname" => $_W["member"]["nickname"], "usay" => trim($_GPC["note"]), "final_fee" => $fee, "createtime" => TIMESTAMP, "order_sn" => date("YmdHis") . random(6, true));
        if ($peerpay["peerpay_selfpay"] < $insert["final_fee"]) {
            $insert["final_fee"] = $peerpay["peerpay_selfpay"];
        }
        $is_exist = pdo_get("tiny_wmall_order_peerpay_payinfo", array("pid" => $peerpay["id"], "openid" => $_GPC["openid"]), array("id"));
        if (empty($is_exist)) {
            pdo_insert("tiny_wmall_order_peerpay_payinfo", $insert);
            $id = pdo_insertid();
        } else {
            pdo_update("tiny_wmall_order_peerpay_payinfo", $insert, array("id" => $is_exist["id"]));
            $id = $is_exist["id"];
        }
        imessage(error(0, array("id" => $id)), "", "ajax");
    }
    $member = get_member($paylog["uid"]);
    shuffle($config_peerpay["notes"]);
    $note = array_pop($config_peerpay["notes"]);
    $member["avatar"] = tomedia($member["avatar"]);
    $result = array("member" => $member, "peerpay" => $peerpay, "note" => $note, "page_title" => empty($member["nickname"]) ? "我的代付" : (string) $member["nickname"] . "的代付");
    imessage(error(0, $result), "", "ajax");
}

?>