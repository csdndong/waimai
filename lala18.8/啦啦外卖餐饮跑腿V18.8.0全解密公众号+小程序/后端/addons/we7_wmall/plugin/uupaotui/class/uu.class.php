<?php
defined("IN_IA") or exit("Access Denied");
load()->func("communication");
class uuPaoTui
{
    protected $app = NULL;
    protected $app_secret = NULL;
    public $config = array();
    public function __construct($sid = "")
    {
        $this->config = array();
        if (!empty($sid)) {
            $this->config = store_get_data($sid, "uupaotui");
            $this->city = get_plugin_config("uupaotui.city");
        } else {
            $this->config = get_plugin_config("uupaotui");
            $this->city = $this->config["city"];
        }
        $this->openid = $this->config["openid"];
        $this->appid = $this->config["appid"];
        $this->appkey = $this->config["appkey"];
        $this->api_url = "http://openapi.uupaotui.com/v2_0/";
    }
    public function buildParams($params)
    {
        $common_params = array("nonce_str" => random(10), "timestamp" => TIMESTAMP, "openid" => $this->openid, "appid" => $this->appid);
        $params = array_merge($params, $common_params);
        $params["sign"] = $this->buildSign($params);
        return $params;
    }
    public function buildSign($params)
    {
        ksort($params);
        $arr = array();
        foreach ($params as $key => $value) {
            if (!empty($value) || $value === 0) {
                $arr[] = $key . "=" . $value;
            }
        }
        $arr[] = "key=" . $this->appkey;
        $str = strtoupper(implode("&", $arr));
        return strtoupper(md5($str));
    }
    public function httpPost($action, $params = array())
    {
        $buildparams = $this->buildParams($params);
        $response = ihttp_request($this->api_url . $action, $buildparams);
        if (is_error($response)) {
            return error("-2", "请求接口出错:" . $response["message"]);
        }
        $result = @json_decode($response["content"], true);
        if ($result["return_code"] != "ok") {
            return error(-1, "错误详情：" . $result["return_msg"]);
        }
        return $result;
    }
    public function getOrderPrice($id)
    {
        global $_W;
        if ($this->config["status"] != 1) {
            return error(-1, "UU跑腿未开启");
        }
        $order = order_fetch($id);
        if (!in_array($order["status"], array(2, 3))) {
            return error(-1, "订单不是待配送状态");
        }
        $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $order["sid"]), array("telephone", "address", "location_x", "location_y"));
        $params = array("origin_id" => $order["ordersn"], "from_address" => $store["address"], "to_address" => $order["address"], "city_name" => $this->city, "subscribe_type" => 0, "send_type" => 0, "to_lat" => $order["location_x"], "to_lng" => $order["location_y"], "from_lat" => $store["location_x"], "from_lng" => $store["location_y"]);
        $response = $this->httpPost("getorderprice.ashx", $params);
        return $response;
    }
    public function addOrder($id, $data)
    {
        global $_W;
        $order = order_fetch($id);
        $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $order["sid"]), array("telephone", "address", "location_x", "location_y"));
        $params = array("price_token" => $data["price_token"], "order_price" => $data["total_money"], "balance_paymoney" => $data["need_paymoney"], "receiver" => $order["username"], "receiver_phone" => $order["mobile"], "callback_url" => WE7_WMALL_URL . "/plugin/uupaotui/notify.php", "push_type" => 0, "push_str" => "", "special_type" => 0, "callme_withtake" => 0, "pubusermobile" => $store["telephone"]);
        if (!empty($order["note"])) {
            $params["note"] = $order["note"];
        }
        $response = $this->httpPost("addorder.ashx", $params);
        return $response;
    }
    public function cancelOrder($id, $reason)
    {
        if (empty($reason)) {
            return error(-1, "请输入取消原因");
        }
        $params = array("origin_id" => $id, "reason" => $reason);
        $response = $this->httpPost("cancelorder.ashx", $params);
        return $response;
    }
    public function getOrderDetail($id)
    {
        $params = array("origin_id" => $id);
        $response = $this->httpPost("getorderdetail.ashx", $params);
        return $response;
    }
    public function queryCityList()
    {
        $response = $this->httpPost("getcitylist.ashx", "");
        return $response;
    }
}

?>