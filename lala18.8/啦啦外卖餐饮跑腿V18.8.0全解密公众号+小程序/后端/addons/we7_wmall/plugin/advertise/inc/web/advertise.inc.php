<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
$config_advertise = get_plugin_config("advertise.type");
if ($op == "recommend") {
    $_W["page"]["title"] = "为您优选推广设置";
    if ($_W["ispost"]) {
        $home = array("num" => intval($_GPC["home"]["num"]));
        foreach ($_GPC["home"]["days"] as $key => $day) {
            $day = intval($day);
            $fee = floatval($_GPC["home"]["fees"][$key]);
            if (!empty($day)) {
                $home["prices"][$day] = array("day" => $day, "fee" => $fee);
            }
        }
        $other = array("num" => intval($_GPC["other"]["num"]));
        foreach ($_GPC["other"]["days"] as $key => $day) {
            $day = intval($day);
            $fee = floatval($_GPC["other"]["fees"][$key]);
            if (!empty($day)) {
                $other["prices"][$day] = array("day" => $day, "fee" => $fee);
            }
        }
        $data = array("status" => intval($_GPC["status"]) ? intval($_GPC["status"]) : 0, "recommendHome" => $home, "recommendOther" => $other);
        set_plugin_config("advertise.type.recommend", $data);
        imessage(error(0, "设置为您优选推广成功"), "refresh", "ajax");
    }
    $config_recommend = $config_advertise["recommend"];
    include itemplate("recommend");
}
if ($op == "stick") {
    $_W["page"]["title"] = "商家置顶推广设置";
    if ($_W["ispost"]) {
        $displayorder_fees = array();
        $displayorders = $_GPC["displayorder"];
        $fees = $_GPC["prices"];
        foreach ($displayorders as $key => $val) {
            $val = intval($val);
            if (!empty($val)) {
                $displayorder_fees[$val] = array("displayorder" => $val);
                $fee = trim($fees[$key]);
                $fee = preg_replace("/，/", ",", $fee);
                $fee = explode(",", $fee);
                foreach ($fee as $k => $v) {
                    $v = explode("-", $v);
                    $displayorder_fees[$val]["fees"][$v[0]] = array("day" => $v[0], "fee" => $v[1]);
                }
            }
        }
        $data = array("status" => intval($_GPC["status"]) ? intval($_GPC["status"]) : 0, "num" => intval($_GPC["num"]), "displayorder_fees" => $displayorder_fees);
        set_plugin_config("advertise.type.stick", $data);
        imessage(error(0, "设置商家置顶推广成功"), "refresh", "ajax");
    }
    $config_stick = $config_advertise["stick"];
    $fees = $config_stick["displayorder_fees"];
    $displayorder = array();
    foreach ($fees as $key => $val) {
        foreach ($val["fees"] as $k => $v) {
            $displayorder[$key][] = implode("-", $v);
        }
        $displayorder["displayorder_fees"][] = array("fees" => implode(",", $displayorder[$key]), "displayorder" => $val["displayorder"]);
    }
    $displayorder_fees = $displayorder["displayorder_fees"];
    include itemplate("stick");
}

?>