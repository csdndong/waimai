<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $manager = $_W["manager"];
    if ($_W["manager"]["extra"]["accept_voice_notice"]) {
        $order = pdo_get("tiny_wmall_order", array("uniacid" => $_W["uniacid"], "sid" => $sid, "status" => 1, "is_pay" => 1));
        $audioSrc = (string) $_W["siteroot"] . "addons/we7_wmall/resource/mp3/click.mp3";
        $app_manager = $_W["we7_wmall"]["config"]["app"]["manager"];
        if (!empty($app_manager["phonic"]["new"])) {
            $audioSrc = WE7_WMALL_URL . "resource/mp3/" . $_W["uniacid"] . "/" . $app_manager["phonic"]["new"];
        }
        $result = array("audioSrc" => $audioSrc);
        if (!empty($order)) {
            imessage(error(0, $result), "", "ajax");
            return 1;
        }
        imessage(error(-1, ""), "", "ajax");
        return 1;
    }
    imessage(error(-1, ""), "", "ajax");
}

?>