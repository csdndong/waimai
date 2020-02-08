<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
if ($_config_wxapp["diy"]["use_diy_member"] != 1) {
    $user = $_W["member"];
    $favorite = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_store_favorite") . " where uniacid = :uniacid and uid = :uid", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"])));
    $coupon_nums = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_activity_coupon_record") . " where uniacid = :uniacid and uid = :uid and status = 1", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"])));
    $redpacket_nums = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_activity_redpacket_record") . " where uniacid = :uniacid and uid = :uid and status = 1", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"])));
    $deliveryCard_status = check_plugin_perm("deliveryCard") && get_plugin_config("deliveryCard.card_apply_status");
    $deliveryCard_setmeal_ok = 0;
    if ($deliveryCard_status && 0 < $user["setmeal_id"] && TIMESTAMP < $user["setmeal_endtime"]) {
        $deliveryCard_setmeal_ok = 1;
    }
    $redpacket_status = check_plugin_perm("shareRedpacket") || check_plugin_perm("freeLunch") || check_plugin_perm("superRedpacket");
    if (check_plugin_perm("spread")) {
        $config_spread = get_plugin_config("spread.basic");
        if ($config_spread["show_in_mine"]) {
            $spread = array("status" => 1, "title" => $config_spread["menu_name"]);
        }
    }
    if (check_plugin_perm("ordergrant") && get_plugin_config("ordergrant.status")) {
        $ordergrant = 1;
    } else {
        $ordergrant = 0;
    }
    $slides = sys_fetch_slide("member", true);
    if (empty($slides)) {
        $slides = false;
    }
    $cover = array("clerk" => imurl("manage/home/index", array(), true), "deliveryer" => imurl("delivery/home/index", array(), true));
    $mealRedpacket = array();
    if (check_plugin_exist("mealRedpacket")) {
        mload()->model("plugin");
        pload()->model("mealRedpacket");
        $mealRedpacket = mealRedpacket_member();
    }
    $has_gohome = 0;
    if (check_plugin_exist("gohome")) {
        $has_gohome = 1;
    }
    $has_wheel = 0;
    if (check_plugin_exist("wheel")) {
        $has_wheel = 1;
    }
    $has_svip = 0;
    if (check_plugin_exist("svip")) {
        $has_svip = 1;
    }
    $result = array("config" => $_W["we7_wmall"]["config"], "redpacket_nums" => $redpacket_nums, "coupon_nums" => $coupon_nums, "credit2" => floatval($user["credit2"]), "credit1" => floatval($user["credit1"]), "user" => $user, "deliveryCard_status" => $deliveryCard_status, "deliveryCard_setmeal_ok" => $deliveryCard_setmeal_ok, "spread" => $spread, "ordergrant" => $ordergrant, "slides" => $slides, "cover" => $cover, "mealRedpacket" => $mealRedpacket, "has_gohome" => $has_gohome, "has_wheel" => $has_wheel, "has_svip" => $has_svip);
} else {
    $id = $_config_wxapp["diy"]["shopPage"]["member"];
    if (empty($id)) {
        imessage(error(-1, "未设置会员中心DIY页面"), "", "ajax");
    }
    mload()->model("diy");
    $page = get_wxapp_diy($id, true);
    if (empty($page)) {
        imessage(error(-1, "页面不能为空"), "", "ajax");
    }
    $result = array("is_use_diy" => 1, "diy" => $page, "user" => $_W["member"]);
}
imessage(error(0, $result), "", "ajax");

?>