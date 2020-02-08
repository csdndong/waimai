<?php
defined("IN_IA") or exit("Access Denied");
class WxPay
{
    protected $wxpay = NULL;
    protected $cert = NULL;
    public function __construct($pay_type = "")
    {
        global $_W;
        $wechat = $_W["we7_wmall"]["config"]["payment"]["wechat"];
        $wechat = $wechat[$wechat["type"]];
        $pay_type = empty($pay_type) ? "wap" : $pay_type;
        if ($pay_type == "h5app") {
            $wechat = $_W["we7_wmall"]["config"]["payment"]["app_wechat"];
        } else {
            if ($pay_type == "wxapp") {
                $payment = get_plugin_config("wxapp.payment");
                $wechat = $payment["wechat"];
                $wechat = $wechat[$wechat["type"]];
            } else {
                if ($pay_type == "H5") {
                    $wechat = $_W["we7_wmall"]["config"]["payment"]["h5_wechat"];
                }
            }
        }
        $this->pay_type = $pay_type;
        $this->wxpay = array("appid" => $wechat["appid"], "mch_id" => $wechat["mchid"], "sub_mch_id" => $wechat["sub_mch_id"], "key" => $wechat["apikey"]);
        $this->cert = array("apiclient_cert" => $wechat["apiclient_cert"], "apiclient_key" => $wechat["apiclient_key"], "rootca" => $wechat["rootca"], "payment_rsa" => $_W["we7_wmall"]["config"]["payment_rsa"]);
    }
    public function array2url($params, $force = false)
    {
        $str = "";
        foreach ($params as $key => $val) {
            if ($force && empty($val)) {
                continue;
            }
            $str .= (string) $key . "=" . $val . "&";
        }
        $str = trim($str, "&");
        return $str;
    }
    public function bulidSign($params)
    {
        unset($params["sign"]);
        ksort($params);
        $string = $this->array2url($params, true);
        $string = $string . "&key=" . $this->wxpay["key"];
        $string = md5($string);
        $result = strtoupper($string);
        return $result;
    }
    public function parseResult($result, $is_check_sign = false)
    {
        if (substr($result, 0, 5) != "<xml>") {
            return $result;
        }
        $result = json_decode(json_encode(isimplexml_load_string($result, "SimpleXMLElement", LIBXML_NOCDATA)), true);
        if (!is_array($result)) {
            return error(-1, "xml结构错误");
        }
        if (isset($result["return_code"]) && $result["return_code"] != "SUCCESS") {
            $msg = empty($result["return_msg"]) ? $result["err_code_des"] : $result["return_msg"];
            return error(-1, $msg);
        }
        if ($is_check_sign && $this->bulidsign($result) != $result["sign"]) {
            return error(-1, "验证签名出错");
        }
        return $result;
    }
    public function httpWxurl($url, $params, $extra = array())
    {
        load()->func("communication");
        $xml = array2xml($params);
        $response = ihttp_request($url, $xml, $extra);
        if (is_error($response)) {
            return $response;
        }
        $result = $this->parseResult($response["content"]);
        return $result;
    }
    public function shortUrl($url)
    {
        $params = array("appid" => $this->wxpay["appid"], "mch_id" => $this->wxpay["mch_id"], "long_url" => $url, "nonce_str" => random(32));
        $params["sign"] = $this->bulidSign($params);
        $result = $this->httpWxurl("https://api.mch.weixin.qq.com/tools/shorturl", $params);
        if (is_error($result)) {
            return $result;
        }
        return $result["short_url"];
    }
    public function checkCert()
    {
        if (empty($this->cert["apiclient_key"]) || empty($this->cert["apiclient_cert"])) {
            return error(-1, "支付证书不完整");
        }
        return true;
    }
    public function mktTransfers($params, $check_type = "FORCE_CHECK")
    {
        global $_W;
        $status = $this->checkCert();
        if (is_error($status)) {
            return $status;
        }
        $params_origin = $params;
        $elements = array("openid", "amount", "partner_trade_no", "re_user_name", "desc");
        $params = array_elements($elements, $params);
        if (empty($params["openid"])) {
            return error(-1, "粉丝信息错误,你可以撤销本次提现，让提现人重新设置提现账户，再次申请提现来解决此问题");
        }
        if (($check_type == "FORCE_CHECK" || $check_type == "OPTION_CHECK") && empty($params["re_user_name"])) {
            return error(-1, "收款人真实姓名不能为空");
        }
        if (empty($params["amount"])) {
            return error(-1, "打款金额不能为空");
        }
        if (empty($params["partner_trade_no"])) {
            return error(-1, "商户订单号不能为空");
        }
        if (empty($params["desc"])) {
            return error(-1, "付款描述信息不能为空");
        }
        $params["check_name"] = $check_type;
        $params["mch_appid"] = $this->wxpay["appid"];
        $params["mchid"] = $this->wxpay["mch_id"];
        $params["nonce_str"] = random(32);
        $params["spbill_create_ip"] = CLIENT_IP;
        $params["sign"] = $this->bulidSign($params);
        $extra = array(CURLOPT_SSLCERT => MODULE_ROOT . "/cert/" . $this->cert["apiclient_cert"] . "/apiclient_cert.pem", CURLOPT_SSLKEY => MODULE_ROOT . "/cert/" . $this->cert["apiclient_key"] . "/apiclient_key.pem", CURLOPT_CAINFO => MODULE_ROOT . "/cert/" . $this->cert["rootca"] . "/rootca.pem");
        $result = $this->httpWxurl("https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers", $params, $extra);
        if (is_error($result)) {
            return $result;
        }
        if ($result["result_code"] != "SUCCESS") {
            return error(-1, (string) $result["err_code"] . "：" . $result["err_code_des"]);
        }
        return true;
    }
    public function mktTransfersRepeat($params)
    {
        $result = $this->mktTransfers($params);
        return $result;
    }
    public function mktPayBank($params)
    {
        global $_W;
        $status = $this->checkCert();
        if (is_error($status)) {
            return $status;
        }
        $elements = array("partner_trade_no", "enc_bank_no", "enc_true_name", "bank_code", "amount", "desc");
        $params = array_elements($elements, $params);
        if (empty($params["partner_trade_no"])) {
            return error(-1, "商户企业付款单号不能为空");
        }
        if (empty($params["enc_bank_no"])) {
            return error(-1, "收款方银行卡号不能为空");
        }
        if (empty($params["enc_true_name"])) {
            return error(-1, "收款方用户名不能为空");
        }
        if (empty($params["bank_code"])) {
            return error(-1, "商户企业付款单号不能为空");
        }
        $bank_list = $this->getback();
        if (!array_key_exists($params["bank_code"], $bank_list)) {
            return error(-1, "暂不支持打款到该银行");
        }
        if (empty($params["amount"])) {
            return error(-1, "付款金额不能为空");
        }
        if (empty($params["desc"])) {
            return error(-1, "付款说明不能为空");
        }
        $params["mch_id"] = $this->wxpay["mch_id"];
        $params["nonce_str"] = random(32);
        $params["enc_bank_no"] = $this->toRSA($params["enc_bank_no"]);
        if (is_error($params["enc_bank_no"])) {
            return $params["enc_bank_no"];
        }
        $params["enc_true_name"] = $this->toRSA($params["enc_true_name"]);
        if (is_error($params["enc_true_name"])) {
            return $params["enc_true_name"];
        }
        $params["sign"] = $this->bulidSign($params);
        $extra = array(CURLOPT_SSLCERT => MODULE_ROOT . "/cert/" . $this->cert["apiclient_cert"] . "/apiclient_cert.pem", CURLOPT_SSLKEY => MODULE_ROOT . "/cert/" . $this->cert["apiclient_key"] . "/apiclient_key.pem", CURLOPT_CAINFO => MODULE_ROOT . "/cert/" . $this->cert["rootca"] . "/rootca.pem");
        $result = $this->httpWxurl("https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank", $params, $extra);
        if (is_error($result)) {
            return $result;
        }
        if ($result["result_code"] != "SUCCESS") {
            return error(-1, (string) $result["err_code"] . "：" . $result["err_code_des"]);
        }
        return true;
    }
    public function mktQueryBank($params)
    {
        global $_W;
        $status = $this->checkCert();
        if (is_error($status)) {
            return $status;
        }
        $elements = array("partner_trade_no");
        $params = array_elements($elements, $params);
        if (empty($params["partner_trade_no"])) {
            return error(-1, "商户企业付款单号不能为空");
        }
        $params["mch_id"] = $this->wxpay["mch_id"];
        $params["nonce_str"] = random(32);
        $params["sign"] = $this->bulidSign($params);
        $extra = array(CURLOPT_SSLCERT => MODULE_ROOT . "/cert/" . $this->cert["apiclient_cert"] . "/apiclient_cert.pem", CURLOPT_SSLKEY => MODULE_ROOT . "/cert/" . $this->cert["apiclient_key"] . "/apiclient_key.pem", CURLOPT_CAINFO => MODULE_ROOT . "/cert/" . $this->cert["rootca"] . "/rootca.pem");
        $result = $this->httpWxurl("https://api.mch.weixin.qq.com/mmpaysptrans/query_bank", $params, $extra);
        if (is_error($result)) {
            return $result;
        }
        if ($result["result_code"] != "SUCCESS") {
            return error(-1, (string) $result["err_code"] . "：" . $result["err_code_des"]);
        }
        $msg = array("SUCCESS" => array("errno" => 0, "text" => "该提现已成功到账", "toaccount_status" => 2), "PROCESSING" => array("errno" => 0, "text" => "该提现正在处理中，请耐心等待", "toaccount_status" => 1), "FAILED" => array("errno" => -1, "text" => "该提现到银行卡已失败", "toaccount_status" => 3), "BANK_FAIL" => array("errno" => -1, "text" => "该提现申请银行退款", "toaccount_status" => 3));
        return error(0, array(base64_decode("c3RhdHVz") => $result["status"], "errno" => $msg[$result["status"]]["errno"], "toaccount_status" => $msg[$result["status"]]["toaccount_status"], "msg" => $msg[$result["status"]]["text"]));
    }
    public function toRSA($data, $force = true)
    {
        global $_W;
        $public_key = "";
        if (empty($force)) {
            $public_key = file_get_contents(MODULE_ROOT . "/cert/" . $this->cert["payment_rsa"] . "/public_key.pem");
        }
        if (empty($public_key)) {
            $status = $this->checkCert();
            if (is_error($status)) {
                return $status;
            }
            $params = array("mch_id" => $this->wxpay["mch_id"], "nonce_str" => random(32), "sign_type" => "MD5");
            $params["sign"] = $this->bulidSign($params);
            $extra = array(CURLOPT_SSLCERT => MODULE_ROOT . "/cert/" . $this->cert["apiclient_cert"] . "/apiclient_cert.pem", CURLOPT_SSLKEY => MODULE_ROOT . "/cert/" . $this->cert["apiclient_key"] . "/apiclient_key.pem", CURLOPT_CAINFO => MODULE_ROOT . "/cert/" . $this->cert["rootca"] . "/rootca.pem");
            $result = $this->httpWxurl("https://fraud.mch.weixin.qq.com/risk/getpublickey", $params, $extra);
            if (is_error($result)) {
                return $result;
            }
            if ($result["result_code"] == "SUCCESS") {
                @unlink(MODULE_ROOT . "/cert/" . $this->cert["payment_rsa"] . "/public_key.pem");
                @rmdir(MODULE_ROOT . "/cert/" . $this->cert["payment_rsa"]);
                $name = random(10);
                $public_key = $result["pub_key"];
                $status = ifile_put_contents("cert/" . $name . "/public_key.pem", $public_key);
                $path = MODULE_ROOT . "/cert/" . $name . "/public_key.pem";
                if (!function_exists("exec")) {
                    return error(-1, "exec错误，请确认exec函数已启用");
                }
                $status = exec("openssl rsa -RSAPublicKey_in -in " . $path . " -pubout", $pems);
                $public_key = "";
                if (!empty($pems)) {
                    $split = "";
                    foreach ($pems as $v) {
                        $public_key .= $split . $v;
                        $split = "\n";
                    }
                }
                if (empty($public_key)) {
                    return error(-1, "微信公钥格式转换失败错误");
                }
                $status = ifile_put_contents("cert/" . $name . "/public_key.pem", $public_key);
                set_system_config(base64_decode("cGF5bWVudF9yc2E="), $name);
            } else {
                return error(-1, (string) $result["err_code"] . "：" . $result["err_code_des"]);
            }
        }
        $public_key = openssl_pkey_get_public($public_key);
        if (empty($public_key)) {
            return error(-1, "微信公钥错误");
        }
        openssl_public_encrypt($data, $encrypted, $public_key, OPENSSL_PKCS1_OAEP_PADDING);
        $encrypted = base64_encode($encrypted);
        return $encrypted;
    }
    public function payRefund_build($params)
    {
        global $_W;
        $status = $this->checkCert();
        if (is_error($status)) {
            return $status;
        }
        $elements = array("total_fee", "refund_fee", "out_trade_no", "out_refund_no");
        $params = array_elements($elements, $params);
        if (empty($params["total_fee"])) {
            return error(-1, "订单总金额不能为空");
        }
        if (empty($params["refund_fee"])) {
            return error(-1, "退款金额不能为空");
        }
        if (empty($params["out_trade_no"])) {
            return error(-1, "商户订单号不能为空");
        }
        if (empty($params["out_refund_no"])) {
            return error(-1, "商户退款单号不能为空");
        }
        $params["appid"] = $this->wxpay["appid"];
        $params["mch_id"] = $this->wxpay["mch_id"];
        $params["sub_mch_id"] = $this->wxpay["sub_mch_id"];
        $params["op_user_id"] = $this->wxpay["mch_id"];
        $params["nonce_str"] = random(32);
        $params["sign"] = $this->bulidSign($params);
        $extra = array(CURLOPT_SSLCERT => MODULE_ROOT . "/cert/" . $this->cert["apiclient_cert"] . "/apiclient_cert.pem", CURLOPT_SSLKEY => MODULE_ROOT . "/cert/" . $this->cert["apiclient_key"] . "/apiclient_key.pem", CURLOPT_CAINFO => MODULE_ROOT . "/cert/" . $this->cert["rootca"] . "/rootca.pem");
        $result = $this->httpWxurl("https://api.mch.weixin.qq.com/secapi/pay/refund", $params, $extra);
        if (is_error($result)) {
            if ($result["message"] == "certificate not match") {
                $order_channel = order_channel($this->pay_type);
                return error(-1, "发起退款申请失败:证书文件不匹配。该订单下单渠道为" . $order_channel . ",请检查" . $order_channel . "支付证书是否正确配置。");
            }
            return error(-1, "发起退款申请失败:" . $result["message"]);
        }
        if ($result["result_code"] != "SUCCESS") {
            return error(-10, "发起退款申请失败." . $result["err_code"] . "：" . $result["err_code_des"]);
        }
        return $result;
    }
    public function payRefund_query($params)
    {
        $elements = array("out_refund_no");
        $params = array_elements($elements, $params);
        if (empty($params["out_refund_no"])) {
            return error(-1, "商户退款单号不能为空");
        }
        $params["appid"] = $this->wxpay["appid"];
        $params["mch_id"] = $this->wxpay["mch_id"];
        $params["sub_mch_id"] = $this->wxpay["sub_mch_id"];
        $params["nonce_str"] = random(32);
        $params["sign"] = $this->bulidSign($params);
        $result = $this->httpWxurl("https://api.mch.weixin.qq.com/pay/refundquery", $params);
        if (is_error($result)) {
            return error(-1, "查询微信退款进度失败." . $result["message"]);
        }
        if ($result["result_code"] != "SUCCESS") {
            return error(-1, "查询微信退款进度失败." . $result["err_code"] . "：" . $result["err_code_des"]);
        }
        return $result;
    }
    public function payRefund_status()
    {
        $wechat_status = array("SUCCESS" => array("text" => "成功", "value" => 3), "FAIL" => array("text" => "失败", "value" => 4), "PROCESSING" => array("text" => "处理中", "value" => 2), "NOTSURE" => array("text" => "未确定，需要商户原退款单号重新发起", "value" => 5));
        return $wechat_status;
    }
    public function getback()
    {
        $bank_list = array("1002" => array("id" => "1002", "title" => "工商银行"), "1005" => array("id" => "1005", "title" => "农业银行"), "1026" => array("id" => "1026", "title" => "中国银行"), "1003" => array("id" => "1003", "title" => "建设银行"), "1001" => array("id" => "1001", "title" => "招商银行"), "1066" => array("id" => "1066", "title" => "邮储银行"), "1020" => array("id" => "1020", "title" => "交通银行"), "1004" => array("id" => "1004", "title" => "浦发银行"), "1006" => array("id" => "1006", "title" => "民生银行"), "1009" => array("id" => "1009", "title" => "兴业银行"), "1010" => array("id" => "1010", "title" => "平安银行"), "1021" => array("id" => "1021", "title" => "中信银行"), "1025" => array("id" => "1025", "title" => "华夏银行"), "1027" => array("id" => "1027", "title" => "广发银行"), "1022" => array("id" => "1022", "title" => "光大银行"), "1032" => array("id" => "1032", "title" => "北京银行"), "1056" => array("id" => "1056", "title" => "宁波银行"));
        return $bank_list;
    }
}

?>