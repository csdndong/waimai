<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "delivery";
if ($ta == "delivery") {
    $config_deliveryer = $_W["we7_wmall"]["config"]["app"]["deliveryer"];
    $result = array("link_ios" => $config_deliveryer["ios_download_link"], "link_android" => $config_deliveryer["android_download_link"]);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "manager") {
        $config_manager = $_W["we7_wmall"]["config"]["app"]["manager"];
        $result = array("link_ios" => $config_manager["ios_download_link"], "link_android" => $config_manager["android_download_link"]);
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($ta == "customer") {
            $config_customer = $_W["we7_wmall"]["config"]["app"]["customer"];
            $result = array("link_ios" => $config_customer["ios_download_link"], "link_android" => $config_customer["android_download_link"]);
            imessage(error(0, $result), "", "ajax");
        } else {
            if ($ta == "plateform") {
                $config_plateform = get_plugin_config("plateformApp.app");
                $result = array("link_ios" => $config_plateform["ios_download_link"], "link_android" => $config_plateform["android_download_link"]);
                imessage(error(0, $result), "", "ajax");
            }
        }
    }
}

?>