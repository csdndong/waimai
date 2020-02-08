<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "basic";
$_config = get_plugin_config("agent");
if ($op == "basic") {
    $_W["page"]["title"] = "基本设置";
    if ($_W["ispost"]) {
        $basic = array("status" => intval($_GPC["status"]));
        set_plugin_config("agent.basic", $basic);
        imessage(error(0, "基本设置成功"), referer(), "ajax");
    }
    $config_basic = $_config["basic"];
    include itemplate("config");
}
if ($op == "serve_fee") {
    $_W["page"]["title"] = "服务费率";
    $serve_fee = $_config["serve_fee"];
    if ($_W["ispost"]) {
        $fee_takeout = $serve_fee["fee_takeout"];
        $takeout_GPC = $_GPC["fee_takeout"];
        $fee_takeout["type"] = intval($takeout_GPC["type"]) ? intval($takeout_GPC["type"]) : 1;
        if ($fee_takeout["type"] == 2) {
            $fee_takeout["fee"] = floatval($takeout_GPC["fee"]);
        } else {
            if ($fee_takeout["type"] == 1) {
            $fee_takeout["fee_rate"] = floatval($takeout_GPC["fee_rate"]);
            $fee_takeout["fee_min"] = floatval($takeout_GPC["fee_min"]);
            $items_yes = array_filter($takeout_GPC["items_yes"], trim);
            if (empty($items_yes)) {
                imessage(error(-1, "至少选择一项抽佣项目"), "", "ajax");
            }
            $fee_takeout["items_yes"] = $items_yes;
            $items_no = array_filter($takeout_GPC["items_no"], trim);
            $fee_takeout["items_no"] = $items_no;
            } else {
                if ($fee_takeout["type"] == 3) {
                    $fee_takeout["fee_rate"] = floatval($takeout_GPC["fee_rate_3"]);
                    $fee_takeout["fee_min"] = floatval($takeout_GPC["fee_min_3"]);
                }
            }
        }
        $fee_selfDelivery = $serve_fee["fee_selfDelivery"];
        $selfDelivery_GPC = $_GPC["fee_selfDelivery"];
        $fee_selfDelivery["type"] = intval($selfDelivery_GPC["type"]) ? intval($selfDelivery_GPC["type"]) : 1;
        if ($fee_selfDelivery["type"] == 2) {
            $fee_selfDelivery["fee"] = floatval($selfDelivery_GPC["fee"]);
        } else {
            if ($fee_selfDelivery["type"] == 1) {
            $fee_selfDelivery["fee_rate"] = floatval($selfDelivery_GPC["fee_rate"]);
            $fee_selfDelivery["fee_min"] = floatval($selfDelivery_GPC["fee_min"]);
            $items_yes = array_filter($selfDelivery_GPC["items_yes"], trim);
            if (empty($items_yes)) {
                imessage(error(-1, "至少选择一项抽佣项目"), "", "ajax");
            }
            $fee_selfDelivery["items_yes"] = $items_yes;
            $items_no = array_filter($selfDelivery_GPC["items_no"], trim);
            $fee_selfDelivery["items_no"] = $items_no;
            } else {
                if ($fee_selfDelivery["type"] == 3) {
                    $fee_selfDelivery["fee_rate"] = floatval($selfDelivery_GPC["fee_rate_3"]);
                    $fee_selfDelivery["fee_min"] = floatval($selfDelivery_GPC["fee_min_3"]);
                }
            }
        }
        $fee_instore = $serve_fee["fee_instore"];
        $instore_GPC = $_GPC["fee_instore"];
        $fee_instore["type"] = intval($instore_GPC["type"]) ? intval($instore_GPC["type"]) : 1;
        if ($fee_instore["type"] == 2) {
            $fee_instore["fee"] = floatval($instore_GPC["fee"]);
        } else {
            if ($fee_instore["type"] == 1) {
            $fee_instore["fee_rate"] = floatval($instore_GPC["fee_rate"]);
            $fee_instore["fee_min"] = floatval($instore_GPC["fee_min"]);
            $items_yes = array_filter($instore_GPC["items_yes"], trim);
            if (empty($items_yes)) {
                imessage(error(-1, "至少选择一项抽佣项目"), "", "ajax");
            }
            $fee_instore["items_yes"] = $items_yes;
            $items_no = array_filter($instore_GPC["items_no"], trim);
            $fee_instore["items_no"] = $items_no;
            } else {
                if ($fee_instore["type"] == 3) {
                    $fee_instore["fee_rate"] = floatval($instore_GPC["fee_rate_3"]);
                    $fee_instore["fee_min"] = floatval($instore_GPC["fee_min_3"]);
                }
            }
        }
        $fee_paybill = $serve_fee["fee_paybill"];
        $paybill_GPC = $_GPC["fee_paybill"];
        $fee_paybill["type"] = intval($paybill_GPC["type"]) ? intval($paybill_GPC["type"]) : 1;
        if ($fee_paybill["type"] == 2) {
            $fee_paybill["fee"] = floatval($paybill_GPC["fee"]);
        } else {
            if ($fee_paybill["type"] == 1) {
                $fee_paybill["fee_rate"] = floatval($paybill_GPC["fee_rate"]);
                $fee_paybill["fee_min"] = floatval($paybill_GPC["fee_min"]);
            } else {
                if ($fee_paybill["type"] == 3) {
                    $fee_paybill["fee_rate"] = floatval($paybill_GPC["fee_rate_3"]);
                    $fee_paybill["fee_min"] = floatval($paybill_GPC["fee_min_3"]);
                }
            }
        }
        $fee_errander = $serve_fee["fee_errander"];
        $errander_GPC = $_GPC["fee_errander"];
        $fee_errander["type"] = intval($errander_GPC["type"]) ? intval($errander_GPC["type"]) : 1;
        if ($fee_errander["type"] == 2) {
            $fee_errander["fee"] = floatval($errander_GPC["fee"]);
        } else {
            if ($fee_errander["type"] == 1) {
            $fee_errander["fee_rate"] = floatval($errander_GPC["fee_rate"]);
            $fee_errander["fee_min"] = floatval($errander_GPC["fee_min"]);
            $items_yes = array_filter($errander_GPC["items_yes"], trim);
            if (empty($items_yes)) {
                imessage(error(-1, "至少选择一项抽佣项目"), "", "ajax");
            }
            $fee_errander["items_yes"] = $items_yes;
            $items_no = array_filter($errander_GPC["items_no"], trim);
            $fee_errander["items_no"] = $items_no;
            } else {
                if ($fee_errander["type"] == 3) {
                    $fee_errander["fee_rate"] = floatval($errander_GPC["fee_rate_3"]);
                    $fee_errander["fee_min"] = floatval($errander_GPC["fee_min_3"]);
                }
            }
        }
        $fee_period = intval($_GPC["fee_period"]);
        $serve_fee = array("fee_takeout" => $fee_takeout, "fee_instore" => $fee_instore, "fee_selfDelivery" => $fee_selfDelivery, "fee_paybill" => $fee_paybill, "fee_period" => $fee_period, "fee_errander" => $fee_errander);
        if (check_plugin_exist("gohome")) {
            $kanjia_GPC = $_GPC["kanjia"];
            $kanjia["type"] = intval($kanjia_GPC["type"]);
            if ($kanjia["type"] == 2) {
                $kanjia["fee"] = floatval($kanjia_GPC["fee"]);
            } else {
                if ($kanjia["type"] == 1) {
                $kanjia["fee_rate"] = floatval($kanjia_GPC["fee_rate"]);
                $kanjia["fee_min"] = floatval($kanjia_GPC["fee_min"]);
                $items_yes = array_filter($kanjia_GPC["items_yes"], trim);
                if (empty($items_yes)) {
                    imessage(error(-1, "至少选择一项砍价抽成项目"), "", "ajax");
                }
                $kanjia["items_yes"] = $items_yes;
                $items_no = array_filter($kanjia_GPC["items_no"], trim);
                $kanjia["items_no"] = $items_no;
                } else {
                    if ($kanjia["type"] == 3) {
                        $kanjia["fee_rate"] = floatval($kanjia_GPC["fee_rate_3"]);
                        $kanjia["fee_min"] = floatval($kanjia_GPC["fee_min_3"]);
                    }
                }
            }
            $pintuan_GPC = $_GPC["pintuan"];
            $pintuan["type"] = intval($pintuan_GPC["type"]);
            if ($pintuan["type"] == 2) {
                $pintuan["fee"] = floatval($pintuan_GPC["fee"]);
            } else {
                if ($pintuan["type"] == 1) {
                $pintuan["fee_rate"] = floatval($pintuan_GPC["fee_rate"]);
                $pintuan["fee_min"] = floatval($pintuan_GPC["fee_min"]);
                $items_yes = array_filter($pintuan_GPC["items_yes"], trim);
                if (empty($items_yes)) {
                    imessage(error(-1, "至少选择一项拼团抽成项目"), "", "ajax");
                }
                $pintuan["items_yes"] = $items_yes;
                $items_no = array_filter($pintuan_GPC["items_no"], trim);
                $pintuan["items_no"] = $items_no;
                } else {
                    if ($pintuan["type"] == 3) {
                        $pintuan["fee_rate"] = floatval($pintuan_GPC["fee_rate_3"]);
                        $pintuan["fee_min"] = floatval($pintuan_GPC["fee_min_3"]);
                    }
                }
            }
            $seckill_GPC = $_GPC["seckill"];
            $seckill["type"] = intval($seckill_GPC["type"]);
            if ($seckill["type"] == 2) {
                $seckill["fee"] = floatval($seckill_GPC["fee"]);
            } else {
                if ($seckill["type"] == 1) {
                $seckill["fee_rate"] = floatval($seckill_GPC["fee_rate"]);
                $seckill["fee_min"] = floatval($seckill_GPC["fee_min"]);
                $items_yes = array_filter($seckill_GPC["items_yes"], trim);
                if (empty($items_yes)) {
                    imessage(error(-1, "至少选择一项抢购抽成项目"), "", "ajax");
                }
                $seckill["items_yes"] = $items_yes;
                $items_no = array_filter($seckill_GPC["items_no"], trim);
                $seckill["items_no"] = $items_no;
                } else {
                    if ($seckill["type"] == 3) {
                        $seckill["fee_rate"] = floatval($seckill_GPC["fee_rate_3"]);
                        $seckill["fee_min"] = floatval($seckill_GPC["fee_min_3"]);
                    }
                }
            }
            $haodian_GPC = $_GPC["haodian"];
            $haodian["type"] = intval($haodian_GPC["type"]);
            if ($haodian["type"] == 2) {
                $haodian["fee"] = floatval($haodian_GPC["fee"]);
            } else {
                if ($haodian["type"] == 1) {
                $haodian["fee_rate"] = floatval($haodian_GPC["fee_rate"]);
                $haodian["fee_min"] = floatval($haodian_GPC["fee_min"]);
                $items_yes = array_filter($haodian_GPC["items_yes"], trim);
                if (empty($items_yes)) {
                    imessage(error(-1, "至少选择一项好店入驻抽成项目"), "", "ajax");
                }
                $haodian["items_yes"] = $items_yes;
                $haodian["items_no"] = array();
                } else {
                    if ($haodian["type"] == 3) {
                        $haodian["fee_rate"] = floatval($haodian_GPC["fee_rate_3"]);
                        $haodian["fee_min"] = floatval($haodian_GPC["fee_min_3"]);
                    }
                }
            }
            $tongcheng_GPC = $_GPC["tongcheng"];
            $tongcheng["type"] = intval($tongcheng_GPC["type"]);
            if ($tongcheng["type"] == 2) {
                $tongcheng["fee"] = floatval($tongcheng_GPC["fee"]);
            } else {
                if ($tongcheng["type"] == 1) {
                $tongcheng["fee_rate"] = floatval($tongcheng_GPC["fee_rate"]);
                $tongcheng["fee_min"] = floatval($tongcheng_GPC["fee_min"]);
                $items_yes = array_filter($tongcheng_GPC["items_yes"], trim);
                if (empty($items_yes)) {
                    imessage(error(-1, "至少选择一项同城发帖抽成项目"), "", "ajax");
                }
                $tongcheng["items_yes"] = $items_yes;
                $tongcheng["items_no"] = array();
                } else {
                    if ($tongcheng["type"] == 3) {
                        $tongcheng["fee_rate"] = floatval($tongcheng_GPC["fee_rate_3"]);
                        $tongcheng["fee_min"] = floatval($tongcheng_GPC["fee_min_3"]);
                    }
                }
            }
            $tiezi_GPC = $_GPC["tiezi"];
            $tiezi["type"] = intval($tiezi_GPC["type"]);
            if ($tiezi["type"] == 2) {
                $tiezi["fee"] = floatval($tiezi_GPC["fee"]);
            } else {
                if ($tiezi["type"] == 1) {
                $tiezi["fee_rate"] = floatval($tiezi_GPC["fee_rate"]);
                $tiezi["fee_min"] = floatval($tiezi_GPC["fee_min"]);
                $items_yes = array_filter($tiezi_GPC["items_yes"], trim);
                if (empty($items_yes)) {
                    imessage(error(-1, "至少选择一项帖子置顶抽成项目"), "", "ajax");
                }
                $tiezi["items_yes"] = $items_yes;
                $tiezi["items_no"] = array();
                } else {
                    if ($tiezi["type"] == 3) {
                        $tiezi["fee_rate"] = floatval($tiezi_GPC["fee_rate_3"]);
                        $tiezi["fee_min"] = floatval($tiezi_GPC["fee_min_3"]);
                    }
                }
            }
            $gohome = array("kanjia" => $kanjia, "pintuan" => $pintuan, "seckill" => $seckill, "haodian" => $haodian, "tiezi" => $tongcheng, "tiezi_stick" => $tiezi);
            $serve_fee["fee_gohome"] = $gohome;
        }
        set_plugin_config("agent.serve_fee", $serve_fee);
        $sync = intval($_GPC["sync"]);
        if ($sync == 1) {
            $update = array("fee" => iserializer($serve_fee));
            pdo_update("tiny_wmall_agent", $update, array("uniacid" => $_W["uniacid"]));
        }
        imessage(error(0, "代理服务费率设置成功"), referer(), "ajax");
    }
    include itemplate("configServeFee");
}

?>