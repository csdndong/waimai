<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "统一收银台";
if (empty($_GPC["pay_type"]) || $_GPC["pay_type"] != "alipay") {
    icheckauth();
}
$_config = $_W["we7_wmall"]["config"];
$id = intval($_GPC["id"]);
$type = trim($_GPC["order_type"]);
if (empty($id) || empty($type)) {
    imessage(error(-1, "参数错误"), "", "ajax");
}
$tables_router = array("takeout" => array("table" => "tiny_wmall_order", "cancel_status" => 6, "order_sn" => "ordersn"), "deliveryCard" => array("table" => "tiny_wmall_delivery_cards_order", "order_sn" => "ordersn"), "errander" => array("table" => "tiny_wmall_errander_order", "cancel_status" => 4, "order_sn" => "order_sn"), "recharge" => array("table" => "tiny_wmall_member_recharge", "order_sn" => "order_sn"), "freelunch" => array("table" => "tiny_wmall_freelunch_partaker", "order_sn" => "order_sn"), "peerpay" => array("table" => "tiny_wmall_order_peerpay_payinfo", "order_sn" => "order_sn"), "paybill" => array("table" => "tiny_wmall_paybill_order", "order_sn" => "order_sn"), "creditshop" => array("table" => "tiny_wmall_creditshop_order_new", "order_sn" => "order_sn"), "seckill" => array("table" => "tiny_wmall_seckill_order", "order_sn" => "order_sn"), "vip" => array("table" => "tiny_wmall_vip_order", "order_sn" => "order_sn"), "recharge_vip" => array("table" => "tiny_wmall_member_recharge", "order_sn" => "order_sn"), "mealRedpacket_plus" => array("table" => "tiny_wmall_superredpacket_meal_order", "order_sn" => "order_sn"), "mealRedpacket" => array("table" => "tiny_wmall_superredpacket_meal_order", "order_sn" => "order_sn"), "gohome" => array("table" => "tiny_wmall_gohome_order", "order_sn" => "ordersn"), "tongcheng" => array("table" => "tiny_wmall_tongcheng_order", "order_sn" => "ordersn"), "haodian" => array("table" => "tiny_wmall_haodian_order", "order_sn" => "ordersn"), "svip" => array("table" => "tiny_wmall_svip_meal_order", "order_sn" => "ordersn"));
$router = $tables_router[$type];
$order = pdo_get($router["table"], array("uniacid" => $_W["uniacid"], "id" => $id));
if (empty($order)) {
    imessage(error(-1, "订单不存在或已删除"), "", "ajax");
}
if (!empty($order["is_pay"])) {
    imessage(error(-1, "该订单已付款"), "", "ajax");
}
if (isset($router["cancel_status"]) && $order["status"] == $router["cancel_status"]) {
    imessage(error(-1, "订单已取消，不能发起支付"), "", "ajax");
}
if (isset($_GPC["pay_type"]) && $_GPC["pay_type"] == "alipay") {
    $_W["member"] = get_member($order["uid"]);
}
if ($type == "creditshop") {
    $order["final_fee"] = $order["use_credit2"];
} else {
    if ($type == "takeout") {
        $order["data"] = iunserializer($order["data"]);
        if (!empty($order["data"]["meal_redpacket"]) && 0 < $order["data"]["meal_redpacket"]["fee"]) {
            $order["final_fee"] = $order["final_fee"] + $order["data"]["meal_redpacket"]["fee"];
        }
        if (!empty($order["data"]["svip"]) && 0 < $order["data"]["svip"]["fee"]) {
            $order["final_fee"] = $order["final_fee"] + $order["data"]["svip"]["fee"];
        }
    }
}
$order_sn = $order["ordersn"] ? $order["ordersn"] : $order["order_sn"];
$record = pdo_get("tiny_wmall_paylog", array("uniacid" => $_W["uniacid"], "order_id" => $id, "order_type" => $type, "order_sn" => $order_sn));
if (empty($record)) {
    $record = array("uniacid" => $_W["uniacid"], "agentid" => $order["agentid"], "uid" => $_W["member"]["uid"], "order_sn" => $order_sn, "order_id" => $id, "order_type" => $type, "fee" => $order["final_fee"], "status" => 0, "addtime" => TIMESTAMP);
    pdo_insert("tiny_wmall_paylog", $record);
    $record["id"] = pdo_insertid();
} else {
    if ($record["status"] == 1) {
        imessage(error(-1, "该订单已支付,请勿重复支付"), "", "ajax");
    }
    if ($order["final_fee"] != $record["fee"]) {
        pdo_update("tiny_wmall_paylog", array("fee" => $order["final_fee"]), array("id" => $record["id"]));
        $record["fee"] = $order["final_fee"];
    }
}
$logo = $_config["mall"]["logo"];
if ($type == "takeout") {
    store_business_hours_init($order["sid"]);
    $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $order["sid"]), array("title", "logo", "is_rest"));
    if ($store["is_rest"] == 1) {
        imessage(error(-1, "门店已打烊,换个店铺下单哇！"), "", "ajax");
    }
    $logo = $store["logo"];
}
$routers = array("takeout" => array("title" => (string) $store["title"] . "-" . $record["order_sn"], "url_detail" => ivurl("pages/order/detail", array("id" => $order["id"]), true), "url_pay" => ivurl("pages/public/pay", array("id" => $order["id"], "order_type" => "takeout", "type" => 1), true)), "errander" => array("title" => "跑腿-" . $record["order_sn"], "url_detail" => ivurl("pages/paotui/detail", array("id" => $order["id"]), true), "url_pay" => ivurl("pages/public/pay", array("id" => $order["id"], "order_type" => "errander", "type" => 1), true)), "deliveryCard" => array("title" => "配送会员卡-" . $record["order_sn"], "url_pay" => imurl("deliveryCard/apply/index", array(), true), "url_detail" => ivurl("pages/member/mine", array(), true)), "recharge" => array("title" => "账户充值-" . $record["order_sn"], "url_pay" => ivurl("pages/public/pay", array("id" => $order["id"], "order_type" => "recharge", "type" => 1), true), "url_detail" => ivurl("pages/member/mine", array(), true)), "recharge_vip" => array("title" => "会员充值-" . $record["order_sn"], "url_pay" => ivurl("pages/public/pay", array("id" => $order["id"], "order_type" => "recharge_vip", "type" => 1), true), "url_detail" => ivurl("pages/member/mine", array(), true)), "freelunch" => array("title" => "霸王餐-" . $record["order_sn"], "url_pay" => ivurl("pages/public/pay", array("id" => $order["id"], "order_type" => "freelunch", "type" => 1), true), "url_detail" => ivurl("pages/freeLunch/partakeSuccess", array(), true)), "peerpay" => array("title" => "帮人代付-" . $record["order_sn"], "url_pay" => ivurl("pages/public/pay", array("id" => $order["id"], "order_type" => "peerpay", "type" => 1), true), "url_detail" => ivurl("pages/public/peerpay/paylist", array("id" => $order["pid"]), true)), "paybill" => array("title" => "买单-" . $record["order_sn"], "url_pay" => ivurl("pages/public/pay", array("id" => $order["id"], "order_type" => "paybill", "type" => 1), true), "url_detail" => ivurl("pages/member/mine", array(), true)), "creditshop" => array("title" => "积分兑换-" . $record["order_sn"], "url_pay" => ivurl("pages/public/pay", array("id" => $order["id"], "order_type" => "creditshop", "type" => 1), true), "url_detail" => ivurl("pages/creditshop/detail", array(), true)), "seckill" => array("title" => "限时抢购-" . $record["order_sn"], "url_pay" => ivurl("pages/public/pay", array("id" => $order["id"], "order_type" => "seckill", "type" => 1), true), "url_detail" => ivurl("pages/seckill/orderList", array(), true)), "vip" => array("title" => "开通会员-" . $record["order_sn"]), "mealRedpacket_plus" => array("title" => "套餐红包Plus-" . $record["order_sn"], "url_pay" => ivurl("pages/public/pay", array("id" => $order["id"], "order_type" => "mealRedpacket_plus", "type" => 1), true), "url_detail" => ivurl("pages/mealRedpacket/plusOrder", array(), true)), "mealRedpacket" => array("title" => "套餐红包-" . $record["order_sn"], "url_pay" => ivurl("pages/public/pay", array("id" => $order["id"], "order_type" => "mealRedpacket", "type" => 1), true), "url_detail" => ivurl("pages/mealRedpacket/mealOrder", array(), true)), "gohome" => array("title" => "砍拼抢优惠活动-" . $record["order_sn"], "url_pay" => ivurl("pages/public/pay", array("id" => $order["id"], "order_type" => "gohome", "type" => 1), true), "url_detail" => ivurl("gohome/pages/kanjia/order/detail", array("id" => $order["id"]), true)), "tongcheng" => array("title" => "信息发布-" . $record["order_sn"], "url_pay" => ivurl("pages/public/pay", array("id" => $order["id"], "order_type" => "tongcheng", "type" => 1), true), "url_detail" => ivurl("gohome/pages/tongcheng/detail", array("id" => $order["tid"]), true)), "haodian" => array("title" => "好店入驻-" . $record["order_sn"], "url_pay" => ivurl("pages/public/pay", array("id" => $order["id"], "order_type" => "haodian", "type" => 1), true), "url_detail" => ivurl("gohome/pages/haodian/application", array(), true)), "svip" => array("title" => "超级会员-" . $record["order_sn"], "url_pay" => ivurl("pages/public/pay", array("id" => $order["id"], "order_type" => "svip", "type" => 1), true), "url_detail" => ivurl("/pages/svip/mine", array(), true)));
$router = $routers[$type];
$title = $router["title"];
$data = array("title" => $title, "logo" => tomedia($logo), "fee" => $record["fee"]);
pdo_update("tiny_wmall_paylog", array("data" => iserializer($data)), array("id" => $record["id"]));
$params = array("module" => "we7_wmall", "ordersn" => $record["order_sn"], "tid" => $record["order_sn"], "user" => $_W["member"]["openid_wxapp"], "fee" => $record["fee"], "title" => $title, "order_type" => $type, "sid" => $order["sid"], "title" => urldecode($title));
$log = pdo_get("core_paylog", array("uniacid" => $_W["uniacid"], "module" => $params["module"], "tid" => $params["tid"]));
if (empty($log)) {
    $log = array("uniacid" => $_W["uniacid"], "acid" => $_W["acid"], "openid" => $params["user"], "module" => $params["module"], "uniontid" => date("YmdHis") . random(14, 1), "tid" => $params["tid"], "fee" => $params["fee"], "card_fee" => $params["fee"], "status" => "0", "is_usecard" => "0");
    pdo_insert("core_paylog", $log);
} else {
    if ($log["status"] == 1) {
        imessage(error(-1, "该订单已支付,请勿重复支付"), "", "ajax");
    }
    if ($log["card_fee"] != $params["fee"]) {
        pdo_update("core_paylog", array("fee" => $params["fee"], "card_fee" => $params["fee"]), array("plid" => $log["plid"]));
        $log["fee"] = $params["fee"];
        $log["card_fee"] = $log["fee"];
    }
}
$params["uniontid"] = $log["uniontid"];
$payment = get_available_payment($type, $order["sid"], true, $order["order_type"]);
if ($type == "takeout" && (!empty($order["data"]["meal_redpacket"]) && 0 < $order["data"]["meal_redpacket"]["fee"] || !empty($order["data"]["svip"]) && 0 < $order["data"]["svip"]["fee"])) {
    foreach ($payment as $index => $val) {
        if ($val["value"] == "delivery") {
            unset($payment[$index]);
        }
    }
}
if (empty($payment)) {
    imessage(error(-1, "没有有效的支付方式, 请联系网站管理员"), "", "ajax");
}
if ($_GPC["type"]) {
    if ($type == "takeout") {
        if ($order["order_type"] == 3 || $order["order_type"] == 4) {
            $data["pay_endtime"] = 0;
        } else {
            $config_takeout = $_W["we7_wmall"]["config"]["takeout"]["order"];
            if (is_array($config_takeout) && 0 < $config_takeout["pay_time_limit"]) {
                $data["pay_endtime"] = $order["addtime"] + $config_takeout["pay_time_limit"] * 60;
                $data["pay_endtime_cn"] = date("Y/m/d H:i:s", $data["pay_endtime"]);
                if ($data["pay_endtime"] < TIMESTAMP) {
                    $data["pay_endtime"] = 0;
                }
            }
        }
    } else {
        if ($type == "errander") {
            $config_errander = get_plugin_config("errander");
            if (is_array($config_errander) && 0 < $config_errander["pay_time_limit"]) {
                $data["pay_endtime"] = $order["addtime"] + $config_errander["pay_time_limit"] * 60;
                $data["pay_endtime_cn"] = date("Y/m/d H:i:s", $data["pay_endtime"]);
                if ($data["pay_endtime"] < TIMESTAMP) {
                    $data["pay_endtime"] = 0;
                }
            }
        }
    }
    $agentid = intval($order["agentid"]);
    $slides = sys_fetch_slide("paycenter", true, $agentid);
    if (empty($slides)) {
        $slides = false;
    }
    $result = array("order" => $data, "payment" => $payment, "slides" => $slides, "member" => $_W["member"]);
    if (is_h5app()) {
        $config_payment = get_system_config("payment");
        $config_alipay = $config_payment["app_alipay"];
        $payinfo = array("mallName" => $_config["mall"]["title"], "money" => $log["card_fee"], "ordersn" => $log["uniontid"], "desc" => $title, "attach" => (string) $_W["uniacid"] . ":h5app", "rsa_type" => $config_alipay["rsa_type"] ? $config_alipay["rsa_type"] : "RSA");
        $_W["h5appinfo"] = array("payinfo" => $payinfo);
    } else {
        if (is_qianfan()) {
            $type_pay = get_plugin_config("qianfanApp.type_pay_id");
            $payinfo = array("type" => $type_pay, "item" => json_encode(array(array("title" => $title, "cover" => "", "num" => 1, "gold_cost" => 0, "cash_cost" => $_W["member"]["uid"] == "112813212" ? 0.01 : $log["card_fee"]))), "address" => json_encode(array("name" => $order["username"], "mobile" => $order["mobile"], "address" => $order["address"])), "send_type" => 0, "allow_pay_type" => 14, "uniontid" => $log["uniontid"], "tid" => $params["tid"], "url_pay" => $routers[$type]["url_pay"], "url_detail" => $routers[$type]["url_detail"]);
            $_W["qianfan"] = array("payinfo" => $payinfo);
        } else {
            if (is_majia()) {
                mload()->model("plugin");
                pload()->model("majiaApp");
                $payinfo = array("uniontid" => $log["uniontid"], "orderNum" => $log["uniontid"], "trade_no" => $log["uniontid"], "title" => $title, "amount" => $log["card_fee"], "money" => $log["card_fee"], "user_id" => $_W["member"]["uid_majia"], "des" => "", "remark" => "", "type" => (string) $_W["we7_wmall"]["config"]["mall"]["title"] . "订单支付", "url_pay" => $routers[$type]["url_pay"], "url_detail" => $routers[$type]["url_detail"]);
                $res = majiapay_build($payinfo);
                if (is_error($res)) {
                    imessage($res, "", "ajax");
                }
                $payinfo["unionOrderNum"] = $res;
                $payinfo["payWay"] = array("wallet" => 1, "weixin" => 1, "alipay" => 1);
                $_W["majia"] = array("payinfo" => $payinfo);
            }
        }
    }
    imessage(error(0, $result), "", "ajax");
}
$pay_type = !empty($_GPC["pay_type"]) ? trim($_GPC["pay_type"]) : $order["pay_type"];
if ($pay_type && !$_GPC["type"] && in_array($pay_type, array_keys($payment))) {
    if ($order["final_fee"] == 0) {
        $pay_type = "credit";
    }
    pdo_update("core_paylog", array("type" => $pay_type), array("uniacid" => $_W["uniacid"], "module" => $params["module"], "plid" => $log["plid"]));
    if ($type == "takeout" && $pay_type == "peerpay") {
        imessage(error(0, array("pid" => $record["id"])), "", "ajax");
    }
    if ($type == "creditshop" && 0 < $order["use_credit1"] && $order["use_credit1_status"] == 0) {
        $result = member_credit_update($_W["member"]["uid"], "credit1", 0 - $order["use_credit1"], array($_W["member"]["uid"], "消费积分:" . $order["use_credit1"]));
        if (is_error($result)) {
            imessage("兑换此商品需要" . $order["use_credit1"] . "积分,系统扣除积分失败:" . $result["message"], "", "error");
        }
        pdo_update("tiny_wmall_creditshop_order_new", array("use_credit1_status" => 1), array("id" => $order["id"]));
    }
    $wheel_url = "";
    if ($type == "takeout" && check_plugin_perm("wheel")) {
        mload()->model("plugin");
        pload()->model("wheel");
        $wheel_url = get_wheel_url(array("order_id" => $record["order_id"], "ochannel" => $_W["ochannel"]));
    }
    if ($pay_type == "wechat") {
        mload()->model("payment");
        if ($_W["ochannel"] == "wxapp") {
            $config_payment = $_W["we7_wxapp"]["config"]["payment"];
            $wechat = $config_payment["wechat"]["default"];
            $wechat["channel"] = "wxapp";
        } else {
            if (is_weixin()) {
                $config_payment = $_W["we7_wmall"]["config"]["payment"];
                $config_wechat = $config_payment["wechat"];
                if (in_array($config_wechat["type"], array("borrow", "borrow_partner"))) {
                    $_SESSION["pay_params"] = array("id" => $id, "order_type" => $type, "pay_type" => $pay_type);
                    $url = iaurl("system/paycenter/pay", array("id" => $id, "order_type" => $type, "pay_type" => $pay_type), true);
                    $oauth = member_oauth_info($url, $config_wechat[$config_wechat["type"]], $_W["openid"]);
                    if (is_error($oauth)) {
                        imessage(error(-1, "获取粉丝身份出错,请重新发起支付。具体原因:" . $oauth["message"]), "", "ajax");
                    }
                    $_W["oauth_openid"] = $oauth["openid"];
                }
                unset($_SESSION["pay_params"]);
                $wechat = $config_payment["wechat"][$config_payment["wechat"]["type"]];
                $wechat["channel"] = "wap";
                $wechat["openid"] = !empty($_W["oauth_openid"]) ? $_W["oauth_openid"] : $_W["openid"];
            } else {
                if (is_glala()) {
                    $wechat = $_W["we7_wmall"]["config"]["payment"]["app_wechat"];
                    $wechat["channel"] = "app";
                    $wechat["version"] = 2;
                    $wechat["trade_type"] = "APP";
                } else {
                $wechat = $_W["we7_wmall"]["config"]["payment"]["h5_wechat"];
                $wechat["trade_type"] = "MWEB";
                $wechat["channel"] = "H5";
                $wOpt = wechat_build($params, $wechat);
                if (is_error($wOpt)) {
                    imessage($wOpt, "", "ajax");
                } else {
                    $wOpt["url"] = (string) $wOpt["mweb_url"] . "&redirect_url=" . urlencode($router["url_detail"]);
                    $wOpt["mweb_url"] = $wOpt["url"];
                    imessage(error(41100, $wOpt), "", "ajax");
                    }
                }
            }
        }
        $wOpt = wechat_build($params, $wechat);
        if (is_error($wOpt)) {
            imessage(error(-1, "抱歉，发起支付失败，具体原因为：“" . $wOpt["errno"] . ":" . $wOpt["message"]), "", "ajax");
        }
        $wOpt["wheel_url"] = $wheel_url;
        imessage(error(0, $wOpt), "", "ajax");
        return 1;
    }
    if ($pay_type == "alipay") {
        mload()->model("payment");
        if (is_glala()) {
            $alipay = $_W["we7_wmall"]["config"]["payment"]["app_alipay"];
            $alipay["trade_type"] = "APP";
        } else {
            $alipay = $_W["we7_wmall"]["config"]["payment"]["alipay"];
        }
        $ret = alipay_build($params, $alipay);
        if (is_error($ret)) {
            imessage(error(-1, "支付宝支付参数有错"), "", "ajax");
        }
        $ret["wheel_url"] = $wheel_url;
        imessage(error(0, $ret), "", "ajax");
        return 1;
    }
    if ($pay_type == "credit") {
        if ($_W["member"]["credit2"] < $params["fee"]) {
            imessage(error(-1000, "余额不足以支付, 需要 " . $params["fee"] . ", 当前 " . $_W["member"]["credit2"] . " 元"), "", "ajax");
        }
        $fee = floatval($params["fee"]);
        $result = member_credit_update($_W["member"]["uid"], "credit2", 0 - $fee, array($_W["member"]["uid"], "消费余额:" . $fee . "元"));
        if (is_error($result)) {
            imessage($result["message"], "", "error");
        }
        if (!empty($_W["openid"])) {
            mc_notice_credit2($_W["openid"], $_W["member"]["uid"], $fee, 0, "线上消费");
        }
        pdo_update("core_paylog", array("status" => "1", "type" => "credit"), array("plid" => $log["plid"]));
        $site = WeUtility::createModuleSite($log["module"]);
        if (!is_error($site)) {
            $site->weid = $_W["weid"];
            $site->uniacid = $_W["uniacid"];
            $site->inMobile = true;
            $method = "payResult";
            if (method_exists($site, $method)) {
                $ret = array();
                $ret["result"] = "success";
                $ret["type"] = "credit";
                $ret["channel"] = $_W["ochannel"];
                $ret["from"] = "notify";
                $ret["tid"] = $log["tid"];
                $ret["uniontid"] = $log["uniontid"];
                $ret["user"] = $log["openid"];
                $ret["fee"] = $log["fee"];
                $ret["weid"] = $log["weid"];
                $ret["uniacid"] = $log["uniacid"];
                $ret["acid"] = $log["acid"];
                $ret["is_usecard"] = $log["is_usecard"];
                $ret["card_type"] = $log["card_type"];
                $ret["card_fee"] = $log["card_fee"];
                $ret["card_id"] = $log["card_id"];
                $result = array("message" => array("errno" => 0, "message" => "支付成功", "pay_type" => $ret["type"], "wheel_url" => $wheel_url));
                echo json_encode($result);
                $site->{$method}($ret);
                return 1;
            }
        }
    } else {
        if ($pay_type == "delivery" || $pay_type == "finishMeal") {
            $site = WeUtility::createModuleSite($log["module"]);
            if (!is_error($site)) {
                $site->weid = $_W["weid"];
                $site->uniacid = $_W["uniacid"];
                $site->inMobile = true;
                $method = "payResult";
                if (method_exists($site, $method)) {
                    $ret = array();
                    $ret["result"] = "success";
                    $ret["type"] = $pay_type;
                    $ret["channel"] = $_W["ochannel"];
                    $ret["from"] = "notify";
                    $ret["tid"] = $log["tid"];
                    $ret["user"] = $log["openid"];
                    $ret["fee"] = $log["fee"];
                    $ret["weid"] = $log["weid"];
                    $ret["uniacid"] = $log["uniacid"];
                    $ret["is_usecard"] = $log["is_usecard"];
                    $ret["card_type"] = $log["card_type"];
                    $ret["card_fee"] = $log["card_fee"];
                    $ret["card_id"] = $log["card_id"];
                    $result = array("message" => array("errno" => 0, "message" => "支付成功", "wheel_url" => $wheel_url));
                    echo json_encode($result);
                    exit($site->{$method}($ret));
                }
            }
        }
    }
}

?>