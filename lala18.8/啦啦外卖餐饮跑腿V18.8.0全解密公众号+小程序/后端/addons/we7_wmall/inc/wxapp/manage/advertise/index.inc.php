<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("activity");
global $_W;
global $_GPC;
$advertise = get_plugin_config("advertise");
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($advertise["basic"]["status"] != 1) {
    imessage(error(-1, "广告暂未开售"), "", "ajax");
}
$amount = $store["account"]["amount"];
if ($ta == "index") {
    $result = array("advertise" => $advertise);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "stick") {
        mload()->model("plugin");
        pload()->model("advertise");
        $type = trim($_GPC["type"]);
        $advertise = get_advertise_info($type);
        if ($advertise["status"] != 1) {
            imessage(error(-1, "该广告暂未开售"), "", "ajax");
        }
        $all_type = advertise_get_types();
        $page_title = $all_type[$type]["text"];
        if (empty($page_title)) {
            imessage(error(-1, "广告类型错误"), "", "ajax");
        }
        if ($_W["ispost"]) {
            $displayorder = intval($_GPC["displayorder"]);
            if ($type == "stick" && !$displayorder) {
                imessage(error(-1, "请选择置顶位置"), "", "ajax");
            }
            if (empty($advertise["leave"])) {
                imessage(error(-1, "商家置顶广告位已售空，请选择其他广告位"), "", "ajax");
            }
            $day = intval($_GPC["day"]);
            if (!$day) {
                imessage(error(-1, "请选择购买天数"), "", "ajax");
            }
            $pay_type = $_GPC["pay_type"];
            if (!$pay_type) {
                imessage(error(-1, "请选择支付方式"), "", "ajax");
            }
            if ($type == "stick") {
                $finalfee = $advertise["prices"][$displayorder]["fees"][$day]["fee"];
                $title = "置顶No." . $displayorder . "," . $day . "天";
            } else {
                $finalfee = $advertise["prices"][$day]["fee"];
                $title = (string) $page_title . "幻灯片展示" . $day . "天";
            }
            $finalfee = $type == "stick" ? $advertise["prices"][$displayorder]["fees"][$day]["fee"] : $advertise["prices"][$day]["fee"];
            if ($pay_type == "credit" && $amount < $finalfee) {
                imessage(error(-1, "余额不足，请选择其他支付方式"), "", "ajax");
            }
            $update = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $sid, "type" => $type, "displayorder" => $displayorder, "title" => $title, "status" => 0, "addtime" => TIMESTAMP, "starttime" => TIMESTAMP, "endtime" => TIMESTAMP, "is_pay" => 0, "order_sn" => date("YmdHis", time()) . random(6, true), "final_fee" => $finalfee, "pay_type" => $pay_type, "days" => $day, "data" => "");
            if ($type == "stick") {
                $update["data"] = iserializer(array("displayorder" => $store["displayorder"]));
            }
            pdo_insert("tiny_wmall_advertise_trade", $update);
            $id = pdo_insertid();
            imessage(error(0, array("id" => $id, "sid" => $sid)), "", "ajax");
        }
        if ($type == "stick") {
            foreach ($advertise["prices"] as $key => &$val) {
                if (in_array($key, $advertise["sailed"])) {
                    $val["sailed"] = 1;
                } else {
                    $val["sailed"] = 0;
                }
            }
        }
        $result = array("page_title" => $page_title, "type" => $type, "advertise" => $advertise, "amount" => $amount);
        imessage(error(0, $result), "", "ajax");
        return 1;
    } else {
        if ($ta == "recommend") {
            mload()->model("plugin");
            pload()->model("advertise");
            $recommendHome = get_advertise_info("recommendHome");
            if ($recommendHome["status"] != 1) {
                imessage(error(-1, "该广告暂未开售"), "", "ajax");
            }
            $recommendOther = get_advertise_info("recommendOther");
            if ($_W["ispost"]) {
                $type = trim($_GPC["type"]);
                if ($type == "recommendHome" && !$recommendHome["leave"]) {
                    imessage(error(-1, "为您优选首页广告位已售空，请选择其他广告位"), "", "ajax");
                }
                if ($type == "recommendOther" && !$recommendOther["leave"]) {
                    imessage(error(-1, "为您优选更多页广告位已售空，请选择其他广告位"), "", "ajax");
                }
                $day = intval($_GPC["day"]);
                if (!$day) {
                    imessage(error(-1, "请选择购买天数"), "", "ajax");
                }
                $pay_type = $_GPC["pay_type"];
                if (!$pay_type) {
                    imessage(error(-1, "请选择支付方式"), "", "ajax");
                }
                if ($type == "recommendHome") {
                    $finalfee = $recommendHome["prices"][$day]["fee"];
                } else {
                    if ($type == "recommendOther") {
                        $finalfee = $recommendOther["prices"][$day]["fee"];
                    }
                }
                if ($pay_type == "credit" && $amount < $finalfee) {
                    imessage(error(-1, "余额不足，请选择其他支付方式"), "", "ajax");
                }
                $update = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $sid, "type" => $type, "title" => (string) $page_title . "幻灯片展示" . $day . "天", "status" => 0, "addtime" => TIMESTAMP, "starttime" => TIMESTAMP, "endtime" => TIMESTAMP, "is_pay" => 0, "order_sn" => date("YmdHis", time()) . random(6, true), "final_fee" => $finalfee, "pay_type" => $pay_type, "days" => $day, "data" => "");
                pdo_insert("tiny_wmall_advertise_trade", $update);
                $id = pdo_insertid();
                imessage(error(0, array("id" => $id, "sid" => $sid)), "", "ajax");
            }
            $result = array("advertise" => array("recommendHome" => $recommendHome, "recommendOther" => $recommendOther), "amount" => $amount);
            imessage(error(0, $result), "", "ajax");
        }
    }
}

?>