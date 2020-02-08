<?php
defined("IN_IA") or exit("Access Denied");
function alipay_build($params, $alipay = array())
{
    global $_W;
    $config_paycallback = $_W["we7_wmall"]["config"]["paycallback"];
    $notify_use_http = intval($config_paycallback["notify_use_http"]);
    load()->func("communication");
    $trade_type = $alipay["trade_type"];
    $set = array();
    $tid = $params["uniontid"];
    if ($trade_type == "APP") {
        $set["app_id"] = $alipay["appid"];
        $set["method"] = "alipay.trade.app.pay";
        $set["charset"] = "utf-8";
        $set["sign_type"] = $alipay["rsa_type"];
        $set["timestamp"] = date("Y-m-d H:i:s");
        $set["version"] = "1.0";
        $set["notify_url"] = (WE7_WMALL_ISHTTPS && $notify_use_http ? WE7_WMALL_URL_NOHTTPS : WE7_WMALL_URL) . "payment/alipay/notify.php";
        $biz_content = array("subject" => $params["title"], "out_trade_no" => $tid, "total_amount" => $params["fee"], "product_code" => "QUICK_MSECURITY_PAY", "goods_type" => 1);
        $set["biz_content"] = json_encode($biz_content);
        ksort($set);
        mload()->classs("alipay");
        $alipayClass = new AliPay("h5app");
        $set["sign"] = $alipayClass->bulidSign($set);
        $string = "";
        foreach ($set as $key => $value) {
            $value = rawurlencode($value);
            $string .= (string) $key . "=" . $value . "&";
        }
        $string = rtrim($string, "&");
        return array("orderInfo" => $string);
    } else {
        $set["service"] = "alipay.wap.create.direct.pay.by.user";
        $set["partner"] = $alipay["partner"];
        $set["_input_charset"] = "utf-8";
        $set["sign_type"] = "MD5";
        $set["notify_url"] = (WE7_WMALL_ISHTTPS && $notify_use_http ? WE7_WMALL_URL_NOHTTPS : WE7_WMALL_URL) . "payment/alipay/notify.php";
        $set["return_url"] = WE7_WMALL_URL . "payment/alipay/return.php";
        $set["out_trade_no"] = $tid;
        $set["subject"] = $params["title"];
        $set["total_fee"] = $params["fee"];
        $set["seller_id"] = $alipay["account"];
        $set["payment_type"] = 1;
        $set["body"] = $_W["uniacid"];
        $prepares = array();
        foreach ($set as $key => $value) {
            if ($key != "sign" && $key != "sign_type") {
                $prepares[] = (string) $key . "=" . $value;
            }
        }
        sort($prepares);
        $string = implode("&", $prepares);
        $string .= $alipay["secret"];
        $set["sign"] = md5($string);
        $response = ihttp_request("https://mapi.alipay.com/gateway.do?" . http_build_query($set, "", "&"), array(), array("CURLOPT_FOLLOWLOCATION" => 0));
        if (empty($response["headers"]["Location"])) {
        return error(-1, "生成url错误");
    }
    return array("url" => $response["headers"]["Location"]);
    }
}
function alipay_pc_build($params, $alipay = array())
{
    global $_W;
    $config_paycallback = $_W["we7_wmall"]["config"]["paycallback"];
    $notify_use_http = intval($config_paycallback["notify_use_http"]);
    load()->func("communication");
    $tid = $params["uniontid"];
    $set = array();
    $set["service"] = "create_direct_pay_by_user";
    $set["partner"] = $alipay["partner"];
    $set["_input_charset"] = "utf-8";
    $set["sign_type"] = "MD5";
    $set["notify_url"] = (WE7_WMALL_ISHTTPS && $notify_use_http ? WE7_WMALL_URL_NOHTTPS : WE7_WMALL_URL) . "payment/alipay/notify.php";
    $set["return_url"] = WE7_WMALL_URL . "payment/alipay/return.php";
    $set["out_trade_no"] = $tid;
    $set["subject"] = $params["title"];
    $set["total_fee"] = $params["fee"];
    $set["seller_email"] = $alipay["account"];
    $set["payment_type"] = 1;
    $set["body"] = (string) $_W["uniacid"] . ":wap:" . $alipay["plugin"];
    $prepares = array();
    foreach ($set as $key => $value) {
        if ($key != "sign" && $key != "sign_type") {
            $prepares[] = (string) $key . "=" . $value;
        }
    }
    sort($prepares);
    $string = implode("&", $prepares);
    $string .= $alipay["secret"];
    $set["sign"] = md5($string);
    $response = ihttp_request("https://mapi.alipay.com/gateway.do?" . http_build_query($set, "", "&"), array(), array("CURLOPT_FOLLOWLOCATION" => 0));
    if (empty($response["headers"]["Location"])) {
        return error(-1, "生成url错误");
    }
    return array("url" => $response["headers"]["Location"]);
}
function wechat_build($params, $wechat)
{
    global $_W;
    $config_paycallback = $_W["we7_wmall"]["config"]["paycallback"];
    $notify_use_http = intval($config_paycallback["notify_use_http"]);
    load()->func("communication");
    if (empty($wechat["version"]) && !empty($wechat["signkey"])) {
        $wechat["version"] = 1;
    }
    if (empty($wechat["channel"])) {
        $wechat["channel"] = "wap";
    }
    $wOpt = array();
    if ($wechat["version"] == 1) {
        $wOpt["appId"] = $wechat["appid"];
        $wOpt["timeStamp"] = strval(TIMESTAMP);
        $wOpt["nonceStr"] = random(8);
        $package = array();
        $package["bank_type"] = "WX";
        $package["body"] = $params["title"];
        $package["attach"] = (string) $_W["uniacid"] . ":" . $wechat["channel"];
        $package["partner"] = $wechat["partner"];
        $package["out_trade_no"] = $params["uniontid"];
        $package["total_fee"] = $params["fee"] * 100;
        $package["fee_type"] = "1";
        $package["notify_url"] = (WE7_WMALL_ISHTTPS && $notify_use_http ? WE7_WMALL_URL_NOHTTPS : WE7_WMALL_URL) . "payment/wechat/notify.php";
        $package["spbill_create_ip"] = CLIENT_IP;
        $package["time_start"] = date("YmdHis", TIMESTAMP);
        $package["time_expire"] = date("YmdHis", TIMESTAMP + 600);
        $package["input_charset"] = "UTF-8";
        if (!empty($wechat["sub_appid"])) {
            $package["sub_appid"] = $wechat["sub_appid"];
        }
        if (!empty($wechat["sub_mch_id"])) {
            $package["sub_mch_id"] = $wechat["sub_mch_id"];
        }
        ksort($package);
        $string1 = "";
        foreach ($package as $key => $v) {
            if (empty($v)) {
                continue;
            }
            $string1 .= (string) $key . "=" . $v . "&";
        }
        $string1 .= "key=" . $wechat["key"];
        $sign = strtoupper(md5($string1));
        $string2 = "";
        foreach ($package as $key => $v) {
            $v = urlencode($v);
            $string2 .= (string) $key . "=" . $v . "&";
        }
        $string2 .= "sign=" . $sign;
        $wOpt["package"] = $string2;
        $string = "";
        $keys = array("appId", "timeStamp", "nonceStr", "package", "appKey");
        sort($keys);
        foreach ($keys as $key) {
            $v = $wOpt[$key];
            if ($key == "appKey") {
                $v = $wechat["signkey"];
            }
            $key = strtolower($key);
            $string .= (string) $key . "=" . $v . "&";
        }
        $string = rtrim($string, "&");
        $wOpt["signType"] = "SHA1";
        $wOpt["paySign"] = sha1($string);
        return $wOpt;
    } else {
        $package = array();
        $package["appid"] = $wechat["appid"];
        $package["mch_id"] = $wechat["mchid"];
        $package["nonce_str"] = random(8);
        $package["body"] = cutstr($params["title"], 26);
        $package["attach"] = (string) $_W["uniacid"] . ":" . $wechat["channel"];
        $package["out_trade_no"] = $params["uniontid"];
        $package["total_fee"] = $params["fee"] * 100;
        $package["spbill_create_ip"] = CLIENT_IP;
        $package["time_start"] = date("YmdHis", TIMESTAMP);
        $package["time_expire"] = date("YmdHis", TIMESTAMP + 600);
        $package["notify_url"] = (WE7_WMALL_ISHTTPS && $notify_use_http ? WE7_WMALL_URL_NOHTTPS : WE7_WMALL_URL) . "payment/wechat/notify.php";
        $package["trade_type"] = $wechat["trade_type"] ? $wechat["trade_type"] : "JSAPI";
        if ($package["trade_type"] == "APP") {
            $body = $_W["we7_wmall"]["config"]["mall"]["title"] . "-" . $params["title"];
            $package["body"] = cutstr($body, 128);
        } else {
            $package["openid"] = empty($wechat["openid"]) ? $_W["fans"]["from_user"] : $wechat["openid"];
        }
        if (!empty($wechat["sub_appid"])) {
            $package["sub_appid"] = $wechat["sub_appid"];
        }
        if (!empty($wechat["sub_mch_id"])) {
            $package["sub_mch_id"] = $wechat["sub_mch_id"];
        }
        if ($package["trade_type"] == "MWEB") {
            $package["scene_info"] = json_encode(array("h5_info" => array("type" => "Wap", "wap_url" => $_W["siteroot"], "wap_name" => $_W["we7_wmall"]["config"]["mall"]["title"])));
        }
        ksort($package, SORT_STRING);
        $string1 = "";
        foreach ($package as $key => $v) {
            if (empty($v)) {
                continue;
            }
            $string1 .= (string) $key . "=" . $v . "&";
        }
        $string1 .= "key=" . $wechat["apikey"];
        $package["sign"] = strtoupper(md5($string1));
        $dat = array2xml($package);
        $response = ihttp_request("https://api.mch.weixin.qq.com/pay/unifiedorder", $dat);
        if (is_error($response)) {
            return $response;
        }
        $xml = @isimplexml_load_string($response["content"], "SimpleXMLElement", LIBXML_NOCDATA);
        if (strval($xml->return_code) == "FAIL") {
            return error(-1, strval($xml->return_msg));
        }
        if (strval($xml->result_code) == "FAIL") {
            return error(-1, strval($xml->err_code) . ": " . strval($xml->err_code_des));
        }
        $prepayid = $xml->prepay_id;
        $string = "";
        if ($package["trade_type"] == "APP") {
            $wOpt["appid"] = $wechat["appid"];
            $wOpt["partnerid"] = $wechat["mchid"];
            $wOpt["prepayid"] = (string) $prepayid;
            $wOpt["package"] = "Sign=WXPay";
            $wOpt["noncestr"] = random(8);
            $wOpt["timestamp"] = strval(TIMESTAMP);
        } else {
            $wOpt["appId"] = $wechat["appid"];
            $wOpt["timeStamp"] = strval(TIMESTAMP);
            $wOpt["nonceStr"] = random(8);
            $wOpt["package"] = "prepay_id=" . $prepayid;
            $wOpt["signType"] = "MD5";
        }
        ksort($wOpt, SORT_STRING);
        foreach ($wOpt as $key => $v) {
            $string .= (string) $key . "=" . $v . "&";
        }
        $string .= "key=" . $wechat["apikey"];
        $wOpt["paySign"] = strtoupper(md5($string));
        if ($wechat["channel"] == "wxapp") {
            $paylog = pdo_get("tiny_wmall_paylog", array("uniacid" => $_W["uniacid"], "order_sn" => $params["tid"]));
            if (!empty($paylog)) {
                $data = iunserializer($paylog["data"]);
                $data["prepay_id"] = (string) $prepayid;
                pdo_update("tiny_wmall_paylog", array("data" => iserializer($data)), array("id" => $paylog["id"]));
            }
        }
        if ($package["trade_type"] == "MWEB") {
            $mweb_url = $xml->mweb_url;
            $wOpt["mweb_url"] = (string) $mweb_url;
        }
        return $wOpt;
    }
}
function yimafu_build($params, $yimafu)
{
    global $_W;
    load()->func("communication");
    $package = array("selfOrdernum" => $params["uniontid"], "money" => $params["fee"], "openId" => $params["openid"], "customerId" => $yimafu["mchid"], "notifyUrl" => base64_encode(urlencode(WE7_WMALL_URL . "payment/yimafu/notify.php?uniacid=" . $_W["uniacid"])), "successUrl" => base64_encode(urlencode($params["url_detail"])), "uid" => "we7_wmall", "goodsName" => cutstr($params["title"], 26), "remark" => (string) $_W["uniacid"] . ":wap");
    ksort($package);
    $str = "";
    foreach ($package as $key => $val) {
        $str .= (string) $key . "=" . $val . "&";
    }
    $str = substr($str, 0, -1);
    $str = $yimafu["secret"] . $str;
    $sign = strtoupper(md5($str));
    $package["sign"] = $sign;
    $query = "";
    foreach ($package as $k => $v) {
        $query .= (string) $k . "/" . $v . "/";
    }
    $query = substr($query, 0, -1);
    $url = (string) $yimafu["host"] . "/index.php?s=/Home/linenew/m_pay/" . $query;
    return $url;
}
function pc_pay_prep($args)
{
    global $_W;
    global $_GPC;
    $_config = $_W["we7_wmall"]["config"];
    $id = intval($args["id"]);
    $type = trim($args["order_type"]);
    if (empty($id) || empty($type)) {
        imessage(error(-1, "参数错误"), "", "ajax");
    }
    $tables_router = array("plugincenter" => array("table" => "tiny_wmall_plugincenter_order", "order_sn" => "order_sn"));
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
	/*if(isset($_GPC['pay_type']) && $_GPC['pay_type'] == 'alipay') {
		$_W['member'] = get_member($order['uid']);
	}*/
    $order_sn = $order["ordersn"] ? $order["ordersn"] : $order["order_sn"];
    $record = pdo_get("tiny_wmall_paylog", array("uniacid" => $_W["uniacid"], "order_id" => $id, "order_type" => $type, "order_sn" => $order_sn));
    if (empty($record)) {
        $record = array("uniacid" => $_W["uniacid"], "order_sn" => $order_sn, "order_id" => $id, "order_type" => $type, "fee" => $order["final_fee"], "status" => 0, "addtime" => TIMESTAMP);
	        pdo_insert("tiny_wmall_paylog", $record);
        $record["id"] = pdo_insertid();
    } else {
        if ($record["status"] == 1) {
            imessage(error(-1, "该订单已支付,请勿重复支付"), "", "ajax");
        }
    }
    $logo = $_config["mall"]["logo"];
    $routers = array("plugincenter" => array("title" => "购买插件-" . $record["order_sn"], "url_detail" => iurl("plugin/plugincenter/buy", array(), true), "url_pay" => ivurl("pages/public/pay", array("id" => $order["id"], "order_type" => "takeout", "type" => 1), true)));
    $router = $routers[$type];
    $title = $router["title"];
    $data = array("title" => $title, "logo" => tomedia($logo), "fee" => $record["fee"]);
    pdo_update("tiny_wmall_paylog", array("data" => iserializer($data)), array("id" => $record["id"]));
    $params = array("module" => "we7_wmall", "ordersn" => $record["order_sn"], "tid" => $record["order_sn"], "user" => $_W["member"]["openid_wxapp"], "fee" => $record["fee"], "order_type" => $type, "title" => urldecode($title));
    $log = pdo_get("core_paylog", array("uniacid" => $_W["uniacid"], "module" => $params["module"], "tid" => $params["tid"]));
    if (empty($log)) {
        $log = array("uniacid" => $_W["uniacid"], "acid" => $_W["acid"], "openid" => $params["user"], "module" => $params["module"], "uniontid" => date("YmdHis") . random(14, 1), "tid" => $params["tid"], "fee" => $params["fee"], "card_fee" => $params["fee"], "status" => "0", "is_usecard" => "0", "type" => $order["pay_type"]);
        pdo_insert("core_paylog", $log);
    } else {
        if ($log["status"] == 1) {
            imessage(error(-1, "该订单已支付,请勿重复支付"), "", "ajax");
        }
    }
    $params["uniontid"] = $log["uniontid"];
    return $params;
}

?>
