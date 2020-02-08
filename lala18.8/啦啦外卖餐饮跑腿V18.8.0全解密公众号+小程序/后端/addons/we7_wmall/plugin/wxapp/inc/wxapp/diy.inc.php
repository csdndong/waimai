<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
$config_mall = $_W["we7_wmall"]["config"]["mall"];
if ($op == "index") {
    $id = intval($_GPC["id"]);
    if (empty($id)) {
        $id = $_config_plugin["diy"]["shopPage"]["home"];
    }
    if (empty($id)) {
        imessage(error(-1, "页面id不能为空"), "", "ajax");
    }
    mload()->model("diy");
    $page = get_wxapp_diy($id, true);
    if (empty($page)) {
        imessage(error(-1, "页面不能为空"), "", "ajax");
    }
    $result = array("config" => $config_mall, "config_wxapp" => $_config_wxapp, "diy" => $page, "cart_sum" => $page["is_show_cart"] == 1 ? get_member_cartnum() : 0);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($op == "store") {
        mload()->model("page");
        $result = store_filter();
        imessage(error(0, $result), "", "ajax");
    }
}

?>