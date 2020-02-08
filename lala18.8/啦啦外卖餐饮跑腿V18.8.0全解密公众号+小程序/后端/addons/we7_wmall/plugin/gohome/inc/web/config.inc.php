<?php
/*

 * @开源学习用
 * @Popping
 * 源码仅供研究学习，请勿用于商业用途
 */

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "activity";
if ($op == "activity") {
    $_W["page"]["title"] = "活动设置";
    if ($_W["ispost"]) {
        if ($_GPC["form_type"] == "basic_setting") {
        $data = array("black_tip" => trim($_GPC["black_tip"]), "status" => array_map("intval", $_GPC["status"]), "danmu_status" => array_map("intval", $_GPC["danmu_status"]));
        set_plugin_config("gohome.basic", $data);
        } else {
            $config["credit1"] = array("status" => intval($_GPC["credit1"]["status"]), "grant_type" => intval($_GPC["credit1"]["grant_type"]));
            $config["credit1"]["grant_num"] = $config["credit1"]["grant_type"] == 1 ? intval($_GPC["credit1"]["grant_num_1"]) : intval($_GPC["credit1"]["grant_num_2"]);
            set_plugin_config("gohome.credit", $config);
        }
        imessage(error(0, "保存成功"), referer(), "ajax");
    }
    $config = get_plugin_config("gohome.basic");
    $credit = get_plugin_config("gohome.credit");
} else {
    if ($op == "serve_fee") {
        $_W["page"]["title"] = "费率设置";
        if ($_W["ispost"]) {
            $kanjia_GPC = $_GPC["kanjia"];
            $kanjia["type"] = intval($kanjia_GPC["type"]);
            if ($kanjia["type"] == 2) {
                $kanjia["fee"] = floatval($kanjia_GPC["fee"]);
            } else {
                $kanjia["fee_rate"] = floatval($kanjia_GPC["fee_rate"]);
                $kanjia["fee_min"] = floatval($kanjia_GPC["fee_min"]);
                $items_yes = array_filter($kanjia_GPC["items_yes"], trim);
                if (empty($items_yes)) {
                    imessage(error(-1, "至少选择一项抽成项目"), "", "ajax");
                }
                $kanjia["items_yes"] = $items_yes;
                $items_no = array_filter($kanjia_GPC["items_no"], trim);
                $kanjia["items_no"] = $items_no;
            }
            $pintuan_GPC = $_GPC["pintuan"];
            $pintuan["type"] = intval($pintuan_GPC["type"]);
            if ($pintuan["type"] == 2) {
                $pintuan["fee"] = floatval($pintuan_GPC["fee"]);
            } else {
                $pintuan["fee_rate"] = floatval($pintuan_GPC["fee_rate"]);
                $pintuan["fee_min"] = floatval($pintuan_GPC["fee_min"]);
                $items_yes = array_filter($pintuan_GPC["items_yes"], trim);
                if (empty($items_yes)) {
                    imessage(error(-1, "至少选择一项抽成项目"), "", "ajax");
                }
                $pintuan["items_yes"] = $items_yes;
                $items_no = array_filter($pintuan_GPC["items_no"], trim);
                $pintuan["items_no"] = $items_no;
            }
            $seckill_GPC = $_GPC["seckill"];
            $seckill["type"] = intval($seckill_GPC["type"]);
            if ($seckill["type"] == 2) {
                $seckill["fee"] = floatval($seckill_GPC["fee"]);
            } else {
                $seckill["fee_rate"] = floatval($seckill_GPC["fee_rate"]);
                $seckill["fee_min"] = floatval($seckill_GPC["fee_min"]);
                $items_yes = array_filter($seckill_GPC["items_yes"], trim);
                if (empty($items_yes)) {
                    imessage(error(-1, "至少选择一项抽成项目"), "", "ajax");
                }
                $seckill["items_yes"] = $items_yes;
                $items_no = array_filter($seckill_GPC["items_no"], trim);
                $seckill["items_no"] = $items_no;
            }
            $data = array("kanjia" => $kanjia, "pintuan" => $pintuan, "seckill" => $seckill);
            set_plugin_config("gohome.serve_fee", $data);
            $update = array("fee_gohome" => iserializer($data));
            $sync = intval($_GPC["sync"]);
            if ($sync == 1) {
                pdo_update("tiny_wmall_store_account", $update, array("uniacid" => $_W["uniacid"]));
            } else {
                if ($sync == 2) {
                    $store_ids = $_GPC["store_ids"];
                    foreach ($store_ids as $storeid) {
                        pdo_update("tiny_wmall_store_account", $update, array("uniacid" => $_W["uniacid"], "sid" => intval($storeid)));
                    }
                }
            }
            imessage(error(0, "费率设置成功"), referer(), "ajax");
        }
        $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title"));
        $gohome = get_plugin_config("gohome.serve_fee");
    } else {
        if ($op == "share") {
            $_W["page"]["title"] = "分享设置";
            if ($_W["ispost"]) {
                $data = array("title" => trim($_GPC["title"]), "thumb" => trim($_GPC["thumb"]), "detail" => trim($_GPC["detail"]), "link" => trim($_GPC["link"]));
                set_plugin_config("gohome.share", $data);
                imessage(error(0, "分享设置设置成功"), referer(), "ajax");
            }
            $share = get_plugin_config("gohome.share");
        }
    }
}
include itemplate("config");

?>