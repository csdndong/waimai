<?php
defined("IN_IA") or exit("Access Denied");
load()->func("communication");
function percentEncode($str)
{
    $result = urlencode($str);
    $result = preg_replace("/\\+/", "%20", $result);
    $result = preg_replace("/\\*/", "%2A", $result);
    $result = preg_replace("/%7E/", "~", $result);
    return $result;
}
function sms_send($type, $mobile, $content, $sid = 0)
{
    global $_W;
    $config_sms = $_W["we7_wmall"]["config"]["sms"];
    if (!is_array($config_sms["set"])) {
        return error(-1, "平台没有设置短信参数");
    }
    if (empty($config_sms["set"]["status"])) {
        return error(-1, "平台已关闭短信功能");
    }
    if ($config_sms["set"]["version"] == 2) {
        date_default_timezone_set("GMT");
        $post = array("PhoneNumbers" => $mobile, "SignName" => $config_sms["set"]["sign"], "TemplateCode" => trim($type), "TemplateParam" => json_encode($content), "OutId" => "", "RegionId" => "cn-hangzhou", "AccessKeyId" => $config_sms["set"]["key"], "Format" => "json", "SignatureMethod" => "HMAC-SHA1", "SignatureVersion" => "1.0", "SignatureNonce" => uniqid(), "Timestamp" => date("Y-m-d\\TH:i:s\\Z"), "Action" => "SendSms", "Version" => "2017-05-25");
        ksort($post);
        $str = "";
        foreach ($post as $key => $value) {
            $str .= "&" . percentencode($key) . "=" . percentencode($value);
        }
        $stringToSign = "GET" . "&%2F&" . percentencode(substr($str, 1));
        $signature = base64_encode(hash_hmac("sha1", $stringToSign, (string) $config_sms["set"]["secret"] . "&", true));
        $post["Signature"] = $signature;
        $url = "http://dysmsapi.aliyuncs.com/?" . http_build_query($post);
        $result = ihttp_get($url);
        if (is_error($result)) {
            return $result;
        }
        $result = @json_decode($result["content"], true);
        if ($result["Code"] != "OK") {
            return error(-1, $result["Message"]);
        }
    } else {
        $post = array("method" => "alibaba.aliqin.fc.sms.num.send", "app_key" => $config_sms["set"]["key"], "timestamp" => date("Y-m-d H:i:s"), "format" => "json", "v" => "2.0", "sign_method" => "md5", "sms_type" => "normal", "sms_free_sign_name" => $config_sms["set"]["sign"], "rec_num" => $mobile, "sms_template_code" => trim($type), "sms_param" => json_encode($content));
        ksort($post);
        $str = "";
        foreach ($post as $key => $val) {
            $str .= $key . $val;
        }
        $secret = $config_sms["set"]["secret"];
        $post["sign"] = strtoupper(md5($secret . $str . $secret));
        $query = "";
        foreach ($post as $key => $val) {
            $query .= (string) $key . "=" . urlencode($val) . "&";
        }
        $query = substr($query, 0, -1);
        $url = "http://gw.api.taobao.com/router/rest?" . $query;
        $result = ihttp_get($url);
        if (is_error($result)) {
            return $result;
        }
        $result = @json_decode($result["content"], true);
        if (!empty($result["error_response"])) {
            if (isset($result["error_response"]["sub_code"])) {
                $msg = sms_error_code($result["error_response"]["sub_code"]);
                if (empty($msg)) {
                    $msg["msg"] = $result["error_response"]["sub_msg"];
                }
            } else {
                $msg["msg"] = $result["error_response"]["msg"];
            }
            return error(-1, $msg["msg"]);
        }
    }
    return true;
}
function sms_singlecall($called_num, $content, $type = "clerk")
{
    global $_W;
    $config_sms = $_W["we7_wmall"]["config"]["sms"];
    if (!is_array($config_sms["set"])) {
        return error(-1, "平台没有设置短信参数");
    }
    if (empty($config_sms["set"]["status"])) {
        return error(-1, "平台已关闭短信功能");
    }
    $config_notice_sms = $_W["we7_wmall"]["config"]["notice"]["sms"];
    if (!is_array($config_notice_sms)) {
        return error(-1, "平台没有设置语音电话通知参数");
    }
    if (!is_array($config_notice_sms[$type]) || !$config_notice_sms[$type]["status"]) {
        return error(-1, "没有开启语音电话通知功能");
    }
    $config_sensitive = $_W["we7_wmall"]["config"]["sensitive"];
    if (!empty($config_sensitive) && !empty($config_sensitive["sensitive_words"])) {
        foreach ($content as &$row) {
            $row = str_replace($config_sensitive["sensitive_words"], $config_sensitive["replace_words"], $row);
        }
    }
    if ($type == "errander_deliveryer" && $config_notice_sms["errander_deliveryer"]["version"] == 1) {
        $content["deliveryerfee"] = $content["deliveryer_fee"];
        unset($content["deliveryer_fee"]);
    }
    if ($config_sms["set"]["version"] == 2) {
        date_default_timezone_set("GMT");
        $post = array("CalledNumber" => $called_num, "CalledShowNumber" => $config_notice_sms[$type]["called_show_num"], "TtsCode" => $config_notice_sms[$type]["tts_code"], "TtsParam" => json_encode($content), "OutId" => "", "RegionId" => "cn-hangzhou", "AccessKeyId" => $config_sms["set"]["key"], "Format" => "json", "SignatureMethod" => "HMAC-SHA1", "SignatureVersion" => "1.0", "SignatureNonce" => uniqid(), "Timestamp" => date("Y-m-d\\TH:i:s\\Z"), "Action" => "SingleCallByTts", "Version" => "2017-05-25");
        ksort($post);
        $str = "";
        foreach ($post as $key => $value) {
            $str .= "&" . percentencode($key) . "=" . percentencode($value);
        }
        $stringToSign = "GET" . "&%2F&" . percentencode(substr($str, 1));
        $signature = base64_encode(hash_hmac("sha1", $stringToSign, (string) $config_sms["set"]["secret"] . "&", true));
        $post["Signature"] = $signature;
        $url = "http://dyvmsapi.aliyuncs.com/?" . http_build_query($post);
        $result = ihttp_get($url);
        if (is_error($result)) {
            return $result;
        }
        $result = @json_decode($result["content"], true);
        date_default_timezone_set("Asia/Shanghai");
        if ($result["Code"] != "OK") {
            return error(-1, $result["Message"]);
        }
    } else {
        $post = array("method" => "alibaba.aliqin.fc.tts.num.singlecall", "app_key" => $config_sms["set"]["key"], "timestamp" => date("Y-m-d H:i:s"), "format" => "json", "v" => "2.0", "sign_method" => "md5", "called_num" => $called_num, "called_show_num" => $config_notice_sms[$type]["called_show_num"], "tts_code" => $config_notice_sms[$type]["tts_code"], "tts_param" => json_encode($content));
        ksort($post);
        $str = "";
        foreach ($post as $key => $val) {
            $str .= $key . $val;
        }
        $secret = $config_sms["set"]["secret"];
        $post["sign"] = strtoupper(md5($secret . $str . $secret));
        $query = "";
        foreach ($post as $key => $val) {
            $query .= (string) $key . "=" . urlencode($val) . "&";
        }
        $query = substr($query, 0, -1);
        $url = "http://gw.api.taobao.com/router/rest?" . $query;
        $result = ihttp_get($url);
        if (is_error($result)) {
            return $result;
        }
        $result = @json_decode($result["content"], true);
        if (!empty($result["error_response"])) {
            $msg = $result["error_response"]["sub_msg"] ? $result["error_response"]["sub_msg"] : $result["error_response"]["msg"];
            return error(-1, $msg);
        }
    }
    return true;
}
function sms_types()
{
    return array("verify_code_tpl" => "手机验证验证码");
}
function sms_error_code($code)
{
    $msgs = array("isv.OUT_OF_SERVICE" => array("msg" => "业务停机", "handle" => "登录www.alidayu.com充值"), "isv.PRODUCT_UNSUBSCRIBE" => array("msg" => "产品服务未开通", "handle" => "登录www.alidayu.com开通相应的产品服务"), "isv.ACCOUNT_NOT_EXISTS" => array("msg" => "账户信息不存在", "handle" => "登录www.alidayu.com完成入驻"), "isv.ACCOUNT_ABNORMAL" => array("msg" => "账户信息异常", "handle" => "联系技术支持"), "isv.SMS_TEMPLATE_ILLEGAL" => array("msg" => "模板不合法", "handle" => "登录www.alidayu.com查询审核通过短信模板使用"), "isv.SMS_SIGNATURE_ILLEGAL" => array("msg" => "签名不合法", "handle" => "登录www.alidayu.com查询审核通过的签名使用"), "isv.MOBILE_NUMBER_ILLEGAL" => array("msg" => "手机号码格式错误", "handle" => "使用合法的手机号码"), "isv.MOBILE_COUNT_OVER_LIMIT" => array("msg" => "手机号码数量超过限制", "handle" => "批量发送，手机号码以英文逗号分隔，不超过200个号码"), "isv.TEMPLATE_MISSING_PARAMETERS" => array("msg" => "短信模板变量缺少参数", "handle" => "确认短信模板中变量个数，变量名，检查传参是否遗漏"), "isv.INVALID_PARAMETERS" => array("msg" => "参数异常", "handle" => "检查参数是否合法"), "isv.BUSINESS_LIMIT_CONTROL" => array("msg" => "触发业务流控限制", "handle" => "短信验证码，使用同一个签名，对同一个手机号码发送短信验证码，允许每分钟1条，累计每小时7条。 短信通知，使用同一签名、同一模板，对同一手机号发送短信通知，允许每天50条（自然日）"), "isv.INVALID_JSON_PARAM" => array("msg" => "触发业务流控限制", "handle" => "JSON参数不合法\tJSON参数接受字符串值"));
    return $msgs[$code];
}
function sms_bindAxnExtension($params)
{
    global $_W;
    $elements = array("Expiration", "PhoneNoA", "PoolKey", "PhoneNoX", "AccessKeyId", "AccessSecret");
    $params = array_elements($elements, $params);
    if (empty($params["AccessKeyId"])) {
        return error(-1, "AccessKey不能为空");
    }
    if (empty($params["AccessSecret"])) {
        return error(-1, "AccessSecret不能为空");
    }
    if (empty($params["Expiration"])) {
        return error(-1, "绑定关系的过期时间不能为空");
    }
    if (empty($params["PhoneNoA"])) {
        return error(-1, "要加密的电话号码不能为空");
    }
    if (empty($params["Expiration"])) {
        return error(-1, "号码池Key不能为空");
    }
    $accessSecret = $params["AccessSecret"];
    unset($params["AccessSecret"]);
    date_default_timezone_set("GMT");
    $public_params = array("SignatureMethod" => "HMAC-SHA1", "SignatureVersion" => "1.0", "SignatureNonce" => uniqid(), "Timestamp" => date("Y-m-d\\TH:i:s\\Z"), "Action" => "BindAxnExtension", "Version" => "2017-05-25", "Format" => "json");
    $post = array_merge($public_params, $params);
    ksort($post);
    $str = "";
    foreach ($post as $key => $value) {
        $str .= "&" . percentencode($key) . "=" . percentencode($value);
    }
    $stringToSign = "GET" . "&%2F&" . percentencode(substr($str, 1));
    $signature = base64_encode(hash_hmac("sha1", $stringToSign, (string) $accessSecret . "&", true));
    $post["Signature"] = $signature;
    $url = "https://dyplsapi.aliyuncs.com/?" . http_build_query($post);
    $result = ihttp_get($url);
    if (is_error($result)) {
        return $result;
    }
    $result = @json_decode($result["content"], true);
    if ($result["Code"] != "OK") {
        $error = sms_bindAxn_error_code($result["Code"]);
        return error(-1, $error["msg"]);
    }
    return $result["SecretBindDTO"];
}
function sms_bindAxn_error_code($code)
{
    $msg = array("isp.RAM_PERMISSION_DENY" => array("msg" => "RAM权限DENY"), "isv.OUT_OF_SERVICE" => array("msg" => "业务停机"), "isv.PRODUCT_UN_SUBSCRIPT" => array("msg" => "未开通云通信产品的阿里云客户"), "isv.ACCOUNT_NOT_EXISTS" => array("msg" => "账户不存在"), "isv.ACCOUNT_ABNORMAL" => array("msg" => "账户异常"), "isp.SYSTEM_ERROR" => array("msg" => "isp.SYSTEM_ERROR"), "isp.UNKNOWN_ERR_CODE" => array("msg" => "运营商未知错误"), "isv.PARTNER_NOT_EXIST" => array("msg" => "未知合作伙伴"), "isv.NO_NOT_EXIST" => array("msg" => "号码不存在"), "isv.ILLEGAL_ARGUMENT" => array("msg" => "参数非法"), "isp.DAO_EXCEPTION" => array("msg" => "数据库异常"), "isv.NO_AVAILABLE_NUMBER" => array("msg" => "无可用号码"), "isp.VENDOR_UNAVAILABLE" => array("msg" => "运营商降费"), "isv.FLOW_LIMIT" => array("msg" => "业务流控"), "isv.PARTNER_IS_CLOSED" => array("msg" => "partner被关停"), "isv.FORBIDDEN_ACTION" => array("msg" => "无权操作"), "isv.NO_USED_BY_OTHERS" => array("msg" => "号码被其他业务方占用"), "isv.VENDOR_BIND_FAILED" => array("msg" => "运营商绑定失败"), "isv.EXPIRE_DATE_ILLEGAL" => array("msg" => "过期时间非法"), "isv.MOBILE_NUMBER_ILLEGAL" => array("msg" => "号码格式非法"), "isv.BIND_CONFLICT" => array("msg" => "绑定冲突"));
    return $msg[$code];
}

?>