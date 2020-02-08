<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "basic";
if ($op == "basic") {
    $_W["page"]["title"] = "基础设置";
    if ($_W["ispost"]) {
        $zhunshibao = array("start_time" => trim($_GPC["start_time"]));
        set_agent_plugin_config("zhunshibao.basic", $zhunshibao);
        $dataProtocol = $_GPC["protocol"];
        set_config_text("准时宝服务协议", "zhunshibao:agreement", htmlspecialchars_decode($dataProtocol), $_W["agentid"]);
        imessage(error(0, "设置成功"), "refresh", "ajax");
    }
    $config = get_agent_plugin_config("zhunshibao.basic");
    $protocol = get_config_text("zhunshibao:agreement", $_W["agentid"]);
} else {
    if ($op == "setting") {
        $_W["page"]["title"] = "批量设置";
        $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "status" => 1), array("id", "title"));
        if ($_W["ispost"]) {
            $zhunshibao_GPC = $_GPC["zhunshibao"];
            $zhunshibao = array("status" => intval($zhunshibao_GPC["status"]), "price_type" => intval($zhunshibao_GPC["price_type"]), "fee_type" => intval($zhunshibao_GPC["fee_type"]));
            if ($zhunshibao["price_type"] == 1) {
                $zhunshibao["price"] = floatval($zhunshibao_GPC["price1"]);
            } else {
                if ($zhunshibao["price_type"] == 2) {
                    $zhunshibao["price"] = floatval($zhunshibao_GPC["price2"]);
                }
            }
            if ($zhunshibao["fee_type"] == "1") {
                $rule = $_GPC["rule"];
            } else {
                if ($zhunshibao["fee_type"] == "2") {
                    $rule = $_GPC["rule_type_2"];
                }
            }
            if (!empty($rule)) {
                foreach ($rule["time"] as $key => $val) {
                    if (empty($val)) {
                        continue;
                    }
                    $price = $rule["fee"][$key];
                    if (empty($price)) {
                        continue;
                    }
                    $zhunshibao["rule"][] = array("time" => intval($val), "fee" => floatval($price));
                }
            }
            mload()->model("activity");
            $extra_sync = intval($_GPC["extra_sync"]);
            if ($extra_sync == 1) {
                foreach ($stores as $val) {
                    store_set_data($val["id"], "zhunshibao", $zhunshibao);
                    $activity = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $val["id"], "title" => "准时宝", "type" => "zhunshibao", "status" => $zhunshibao["status"]);
                    activity_set($val["id"], $activity);
                }
            } else {
                if ($extra_sync == 2) {
                    $store_ids = $_GPC["store_ids"];
                    foreach ($store_ids as $storeid) {
                        store_set_data($storeid, "zhunshibao", $zhunshibao);
                        $activity = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $storeid, "title" => "准时宝", "type" => "zhunshibao", "status" => $zhunshibao["status"]);
                        activity_set($storeid, $activity);
                    }
                }
            }
            set_agent_plugin_config("zhunshibao.setting", $zhunshibao);
            imessage(error(0, "批量配置成功"), referer(), "ajax");
        }
        $setting = get_agent_plugin_config("zhunshibao.setting");
    }
}
include itemplate("config");

?>
