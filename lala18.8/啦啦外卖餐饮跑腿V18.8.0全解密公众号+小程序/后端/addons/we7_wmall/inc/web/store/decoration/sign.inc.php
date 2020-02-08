<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $_W["page"]["title"] = "编辑门店招牌";
    $shopSign = store_get_data($sid, "shopSign");
    if ($_W["ispost"]) {
        $thumb = trim($_GPC["thumb"]);
        store_set_data($sid, "shopSign", $thumb);
        imessage(error(0, "设置门店招牌成功"), iurl("store/decoration/sign/index"), "ajax");
    }
}
include itemplate("store/decoration/sign");

?>