<?php
defined("IN_IA") or exit("Access Denied");
class YiMaFu
{
    public $yimafu = NULL;
    public function __construct()
    {
        global $_W;
        $this->yimafu = $_W["we7_wmall"]["config"]["payment"]["yimafu"];
    }
    public function array2query($params)
    {
        ksort($params);
        $str = "";
        foreach ($params as $key => $val) {
            $str .= (string) $key . "/" . $val . "/";
        }
        $str = substr($str, 0, -1);
        return $str;
    }
    public function payRefund_build($orderno)
    {
        load()->func("communication");
        if (empty($orderno)) {
            return error(-1, "商户订单号不能为空");
        }
        $yimafu = $this->yimafu;
        $sign = $this->buildSign($orderno);
        $params = array("sign" => $sign, "uid" => "we7_wmall", "orderno" => $orderno);
        $query = $this->array2query($params);
        $url = (string) $yimafu["host"] . "/index.php?s=/Home/linenew/m_backpay/" . $query;
        $result = ihttp_get($url);
        if (is_error($result)) {
            return $result;
        }
        $result = json_decode($result["content"], true);
        if (!is_array($result)) {
            return error(-1, "返回数据错误");
        }
        if ($result["result"] != "0000") {
            return error(-1, $result["desc"]);
        }
        return $result;
    }
    public function buildSign($params)
    {
        unset($sign);
        $yimafu = $this->yimafu;
        $package = array("uid" => "we7_wmall", "orderno" => $params);
        ksort($package);
        $str = "";
        foreach ($package as $key => $val) {
            $str .= (string) $key . "=" . $val . "&";
        }
        $str = substr($str, 0, -1);
        $str = $yimafu["secret"] . $str;
        $sign = strtoupper(md5($str));
        return $sign;
    }
}

?>