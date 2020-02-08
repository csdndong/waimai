<?php
defined("IN_IA") or exit("Access Denied");
load()->func("communication");
class DaDa
{
    protected $app = NULL;
    protected $app_secret = NULL;
    public $config = array();
    public function __construct()
    {
        global $_W;
        $this->config = get_plugin_config("dada");
        $this->app = array();
        $this->app_secret = $this->config["appsecret"];
        $api_urls = array("open" => "http://newopen.imdada.cn", "sandbox" => "http://newopen.qa.imdada.cn");
        $this->api_url = $api_urls["open"];
    }
    public function buildParams($body)
    {
        $params = array("app_key" => $this->config["appkey"], "source_id" => $this->config["sourceid"], "body" => "", "format" => "json", "timestamp" => TIMESTAMP, "v" => "1.0");
        $params["body"] = json_encode($body);
        $params["signature"] = $this->buildSign($params);
        return $params;
    }
    public function buildSign($params)
    {
        ksort($params);
        $str = "";
        foreach ($params as $key => $val) {
            $str .= $key . $val;
        }
        $str = $this->app_secret . $str . $this->app_secret;
        $sign = strtoupper(md5($str));
        return $sign;
    }
    public function httpPost($action, $params = "")
    {
        $buildparams = $this->buildParams($params);
        $response = ihttp_request($this->api_url . $action, json_encode($buildparams), array("Content-Type" => "application/json"));
        if (is_error($response)) {
            return error("-2", "请求接口出错:" . $response["message"]);
        }
        $result = @json_decode($response["content"], true);
        if ($result["status"] == "fail") {
            return error(-1, (string) $result["errorCode"] . ": " . $result["msg"] . ",");
        }
        return $result["result"];
    }
    public function queryCityCode()
    {
        $response = $this->httpPost("/api/cityCode/list", "");
        return $response;
    }
    public function buildBodyParams($id)
    {
        $order = order_fetch($id);
        $dada = store_get_data($order["sid"], "dada");
        $params = array("shop_no" => $dada["shopno"], "origin_id" => $order["ordersn"], "city_code" => $dada["citycode"], "cargo_price" => $order["final_fee"], "is_prepay" => 0, "expected_fetch_time" => TIMESTAMP + 60 * 10, "receiver_name" => $order["username"], "receiver_address" => $order["address"], "receiver_phone" => $order["mobile"], "receiver_lat" => $order["location_x"], "receiver_lng" => $order["location_y"], "info" => $order["note"], "callback" => imurl("dada/api", array(), true));
        return $params;
    }
    public function queryDeliverFee($id)
    {
        $params = $this->buildBodyParams($id);
        $response = $this->httpPost("/api/order/queryDeliverFee", $params);
        if (is_error($response)) {
            return error(-1, "达达订单编号获取失败,原因:" . $response["message"]);
        }
        set_order_data($id, "dada.deliveryno", $response["deliveryNo"]);
        return $response;
    }
    public function addAfterQuery($id)
    {
        $deliveryNo = get_order_data($id, "dada.deliveryno");
        $params = array("deliveryNo" => $deliveryNo);
        $response = $this->httpPost("/api/order/addAfterQuery", $params);
        return $response;
    }
    public function orderAccept($id)
    {
        $order = order_fetch($id);
        $params = array("order_id" => $order["ordersn"]);
        $response = $this->httpPost("/api/order/accept", $params);
        return $response;
    }
    public function orderDetailQuery($id)
    {
        $order = order_fetch($id);
        $params = array("order_id" => $order["ordersn"]);
        $response = $this->httpPost("/api/order/status/query", $params);
        return $response;
    }
    public function addOrder($id)
    {
        $params = $this->buildBodyParams($id);
        $response = $this->httpPost("/api/order/addOrder", $params);
        return $response;
    }
    public function cancelReason()
    {
        $response = $this->httpPost("/api/order/cancel/reasons", "");
        return $response;
    }
    public function cancelOrder($id)
    {
        $reason = order_cancel_reason($id);
        $order = order_fetch($id);
        $params = array("order_id" => $order["ordersn"], "cancel_reason_id" => 10000, "cancel_reason" => $reason);
        $response = $this->httpPost("/api/order/formalCancel", $params);
        return $response;
    }
}

?>