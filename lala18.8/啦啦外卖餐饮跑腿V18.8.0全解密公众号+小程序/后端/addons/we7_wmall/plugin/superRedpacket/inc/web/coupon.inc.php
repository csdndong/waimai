<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "post";
if ($op == "post") {
    $_W["page"]["title"] = "商家代金券设置";
    $coupon = get_plugin_config("superRedpacket.coupon.page");
    if ($_W["ispost"]) {
        if (!empty($_GPC["data"])) {
            set_plugin_config("superRedpacket.coupon.page", $_GPC["data"]);
        }
        imessage(error(0, "商家代金券设置成功"), iurl("superRedpacket/coupon/post"), "ajax");
    }
    include itemplate("coupon");
} else {
    if ($op == "showstores") {
        $_W["page"]["title"] = "首页显示商家";
        $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "status" => 1), array("id", "title", "logo"), "id");
        if ($_W["ispost"]) {
            if (empty($_GPC["store_ids"])) {
                imessage(error(-1, "请选择商户"), "", "ajax");
            } else {
                $num = count($_GPC["store_ids"]);
                if (4 < $num) {
                    imessage(error(-1, "商户数不能超过4个"), "", "ajax");
                }
            }
            $showstore = array();
            foreach ($_GPC["store_ids"] as $sid) {
                $showstore[$sid] = array("title" => $stores[$sid]["title"], "logo" => $stores[$sid]["logo"]);
            }
            $data = array("sids" => $_GPC["store_ids"], "stores" => $showstore);
            set_plugin_config("superRedpacket.coupon.store", $data);
            imessage(error(0, "设置成功"), referer(), "ajax");
        }
        $homeshowstores = get_plugin_config("superRedpacket.coupon.store");
        $showstores = $homeshowstores["sids"];
        include itemplate("couponShowstore");
    }
}

?>