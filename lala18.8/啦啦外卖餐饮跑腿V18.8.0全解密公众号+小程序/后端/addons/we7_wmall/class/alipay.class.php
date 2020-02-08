<?php
defined("IN_IA") or exit("Access Denied");
class AliPay
{
    public $alipay = NULL;
    public function __construct($pay_type = "wap")
    {
        global $_W;
        $alipay = $_W["we7_wmall"]["config"]["payment"]["alipay"];
        if ($pay_type == "h5app") {
            $alipay = $_W["we7_wmall"]["config"]["payment"]["app_alipay"];
        }
        $this->alipay = array("app_id" => $alipay["appid"], "rsa_type" => empty($alipay["rsa_type"]) ? "RSA" : $alipay["rsa_type"]);
        $this->cert = array("private_key" => $alipay["private_key"], "public_key" => $alipay["public_key"]);
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
        $priKey = file_get_contents(MODULE_ROOT . "/cert/" . $this->cert["private_key"] . "/private_key.pem");
        $res = openssl_get_privatekey($priKey);
        if ($params["sign_type"] == "RSA") {
            openssl_sign($string, $sign, $res);
        } else {
            openssl_sign($string, $sign, $res, OPENSSL_ALGO_SHA256);
        }
        openssl_free_key($res);
        $sign = base64_encode($sign);
        return $sign;
    }
    public function checkCert()
    {
        global $_W;
        if (empty($this->cert["private_key"]) || empty($this->cert["public_key"])) {
            return error(-1, "支付宝支付证书不完整");
        }
        return true;
    }
    public function payRefund_build($params)
    {
        global $_W;
        $status = $this->checkCert();
        if (is_error($status)) {
            return $status;
        }
        $elements = array("refund_fee", "out_trade_no", "out_refund_no", "refund_reason", "out_request_no");
        $params = array_elements($elements, $params);
        if (empty($params["refund_fee"])) {
            return error(-1, "退款金额不能为空");
        }
        if (empty($params["out_trade_no"])) {
            return error(-1, "商户订单号不能为空");
        }
        $set = array();
        $set["app_id"] = $this->alipay["app_id"];
        $set["method"] = "alipay.trade.refund";
        $set["charset"] = "utf8";
        $set["sign_type"] = $this->alipay["rsa_type"];
        $set["timestamp"] = date("Y-m-d H:i:s");
        $set["version"] = "1.0";
        $other = array("out_trade_no" => $params["out_trade_no"], "refund_amount" => $params["refund_fee"], "refund_reason" => $params["refund_reason"] ? $params["refund_reason"] : "正常退款");
        if (!empty($params["out_request_no"])) {
            $other["out_request_no"] = $params["out_request_no"];
        }
        $set["biz_content"] = json_encode($other);
        $set["sign"] = $this->bulidSign($set);
        load()->func("communication");
        $result = ihttp_post("https://openapi.alipay.com/gateway.do", $set);
        if (is_error($result)) {
            return $result;
        }
        $result["content"] = iconv("GBK", "UTF-8//IGNORE", $result["content"]);
        $result = json_decode($result["content"], true);
        if (!is_array($result)) {
            return error(-1, "返回数据错误");
        }
        if ($result["alipay_trade_refund_response"]["code"] != 10000) {
            return error(-1, $result["alipay_trade_refund_response"]["sub_msg"]);
        }
        return $result["alipay_trade_refund_response"];
    }
    public function transfer($params, $payee_type = "ALIPAY_LOGONID")
    {
        global $_W;
        $status = $this->checkCert();
        if (is_error($status)) {
            return $status;
        }
        $elements = array("out_biz_no", "payee_type", "payee_account", "amount", "payee_real_name", "remark");
        $params = array_elements($elements, $params);
        if (empty($params["out_biz_no"])) {
            return error(-1, "商户转账订单号不能为空");
        }
        if (!in_array($payee_type, array("ALIPAY_USERID", "ALIPAY_LOGONID"))) {
            return error(-1, "收款方账户类型");
        }
        $params["payee_type"] = $payee_type;
        if (empty($params["payee_account"])) {
            return error(-1, "收款方账户不能为空");
        }
        if (empty($params["amount"])) {
            return error(-1, "转账金额不能为空");
        }
        if (empty($params["payee_real_name"])) {
            return error(-1, "收款方真实姓名不能为空");
        }
        $set["app_id"] = $this->alipay["app_id"];
        $set["method"] = "alipay.fund.trans.toaccount.transfer";
        $set["charset"] = "utf-8";
        $set["sign_type"] = $this->alipay["rsa_type"];
        $set["timestamp"] = date("Y-m-d H:i:s");
        $set["version"] = "1.0";
        $set["biz_content"] = json_encode($params);
        $set["sign"] = $this->bulidSign($set);
        load()->func("communication");
        $result = ihttp_post("https://openapi.alipay.com/gateway.do", $set);
        if (is_error($result)) {
            return $result;
        }
        $result["content"] = iconv("GBK", "UTF-8//IGNORE", $result["content"]);
        $result = json_decode($result["content"], true);
        if (!is_array($result)) {
            return error(-1, "返回数据错误");
        }
        if ($result["alipay_fund_trans_toaccount_transfer_response"]["code"] != 10000 || empty($result["alipay_fund_trans_toaccount_transfer_response"]["pay_date"])) {
            return error(-1, $result["alipay_fund_trans_toaccount_transfer_response"]["sub_msg"]);
        }
        return true;
    }
    public function transOrderQuery($params)
    {
        global $_W;
        $status = $this->checkCert();
        if (is_error($status)) {
            return $status;
        }
        $elements = array("out_biz_no", "order_id");
        $params = array_elements($elements, $params);
        if (empty($params["out_biz_no"]) && empty($params["order_id"])) {
            return error(-1, "请输入商户转账订单号或者支付宝转账单据号码");
        }
        if (!empty($params["out_biz_no"])) {
            unset($params["order_id"]);
        }
        $set["app_id"] = $this->alipay["app_id"];
        $set["method"] = "alipay.fund.trans.order.query";
        $set["charset"] = "utf-8";
        $set["sign_type"] = $this->alipay["rsa_type"];
        $set["timestamp"] = date("Y-m-d H:i:s");
        $set["version"] = "1.0";
        $set["biz_content"] = json_encode($params);
        $set["sign"] = $this->bulidSign($set);
        load()->func("communication");
        $result = ihttp_post("https://openapi.alipay.com/gateway.do", $set);
        if (is_error($result)) {
            return $result;
        }
        $result["content"] = iconv("GBK", "UTF-8//IGNORE", $result["content"]);
        $result = json_decode($result["content"], true);
        if (!is_array($result)) {
            return error(-1, "返回数据错误");
        }
        if ($result["alipay_fund_trans_order_query_response"]["code"] != 10000) {
            return error(-1, $result["alipay_fund_trans_order_query_response"]["sub_msg"]);
        }
        return true;
    }
}

?>