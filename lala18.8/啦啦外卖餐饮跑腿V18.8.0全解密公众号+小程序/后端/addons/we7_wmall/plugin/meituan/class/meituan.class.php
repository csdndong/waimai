<?php
defined("IN_IA") or exit("Access Denied");
load()->func("communication");
class Meituan
{
    protected $app = NULL;
    public $config = array();
    public function __construct($sid = 0)
    {
        global $_W;
        $store = store_fetch($sid, array("data", "meituan_status", "id", "title", "meituanShopId"));
        if (empty($store["meituanShopId"])) {
            $store["meituanShopId"] = random(20, true);
            pdo_update("tiny_wmall_store", array("meituanShopId" => $store["meituanShopId"]), array("id" => $sid));
        }
        $this->config = get_plugin_config("meituan");
        $this->app = array("sid" => $sid, "store" => array("meituan_status" => $store["meituan_status"], "meituan" => $store["data"]["meituan"], "shopid" => $store["meituanShopId"]), "developerId" => $this->config["developerId"], "signKey" => $this->config["signKey"]);
        $this->store = $store;
        $this->shopid = $store["meituanShopId"];
        $api_urls = array("sandbox" => "http://api.open.cater.meituan.com/waimai/", "open" => "http://api.open.cater.meituan.com/waimai/");
        $this->api_url = $api_urls["open"];
    }
    public function getStoremapUrl()
    {
        return "https://open-erp.meituan.com/storemap?developerId=" . $this->app["developerId"] . "&businessId=2&ePoiId=" . $this->store["meituanShopId"] . "&signKey=" . $this->app["signKey"] . "&ePoiName=" . $this->store["title"];
    }
    public function getReleasebindingUrl()
    {
        $appAuthToken = $this->getAppAuthToken();
        if (is_error($appAuthToken)) {
            return $appAuthToken;
        }
        return "https://open-erp.meituan.com/releasebinding?businessId=2&signKey=" . $this->app["signKey"] . "&appAuthToken=" . $appAuthToken;
    }
    public function saveAppAuthToken($params)
    {
        global $_W;
        $meituanShopId = trim($params["ePoiId"]);
        $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "meituanShopId" => $meituanShopId), array("id"));
        if (!empty($store)) {
            $cachekey = "meituan:appAuthToken:" . $_W["uniacid"] . ":" . $store["id"];
            $record = array("appAuthToken" => $params["appAuthToken"]);
            store_set_data($store["id"], "meituan.basic.status", 1);
            icache_write($cachekey, $record);
        }
        echo "{\"data\":\"success\"}";
        exit;
    }
    public function getAppAuthToken()
    {
        global $_W;
        $cachekey = "meituan:appAuthToken:" . $_W["uniacid"] . ":" . $this->app["sid"];
        $cache = icache_load($cachekey);
        if (!empty($cache)) {
            return $cache["appAuthToken"];
        }
        return error("-1", "appAuthToken不存在");
    }
    public function releasebinding($params)
    {
        global $_W;
        $meituanShopId = trim($params["ePoiId"]);
        $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "meituanShopId" => $meituanShopId), array("id"));
        if (!empty($store)) {
            $cachekey = "meituan:appAuthToken:" . $_W["uniacid"] . ":" . $store["id"];
            icache_delete($cachekey);
            store_set_data($store["id"], "meituan.basic.status", 0);
        }
        echo "{\"data\":\"success\"}";
        exit;
    }
    public function yinsihaojiangji($params)
    {
        global $_W;
        echo "{\"data\":\"ok\"}";
        exit;
    }
    public function orderStatusTransform($status, $type = 0)
    {
        $rounter = array("1" => 1, "2" => 1, "4" => 2, "6" => 4, "8" => 5, "9" => 6);
        if (empty($type)) {
            return $rounter[$status];
        }
        $rounter = array_flip($rounter);
        return $rounter[$status];
    }
    public function buildSign($params)
    {
        unset($params["sign"]);
        ksort($params);
        $string = $this->app["signKey"];
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $string .= (string) $key . $value;
            }
        }
        return strtolower(sha1($string));
    }
    public function httpGet($action, $params = array())
    {
        $getAppAuthToken = $this->getAppAuthToken();
        if (is_error($getAppAuthToken)) {
            return $getAppAuthToken;
        }
        $data = array("appAuthToken" => $getAppAuthToken, "charset" => "utf-8", "timestamp" => TIMESTAMP, "version" => 1);
        $data = array_merge($data, $params);
        $data["sign"] = $this->buildSign($data);
        $url = (string) $this->api_url . $action . "?" . http_build_query($data);
        $response = ihttp_get($url);
        if (is_error($response)) {
            return error("-2", "请求接口出错:" . $response["message"]);
        }
        $result = @json_decode($response["content"], true);
        if (!empty($result["error"])) {
            return error(-1, "访问美团接口出错," . $result["error"]["code"] . ": " . $result["error"]["message"]);
        }
        return $result["data"];
    }
    public function httpPost($action, $params = array())
    {
        $getAppAuthToken = $this->getAppAuthToken();
        if (is_error($getAppAuthToken)) {
            return $getAppAuthToken;
        }
        $data = array("appAuthToken" => $getAppAuthToken, "charset" => "utf-8", "timestamp" => TIMESTAMP, "version" => 1);
        $data = array_merge($data, $params);
        $data["sign"] = $this->buildSign($data);
        $url = (string) $this->api_url . $action;
        $response = ihttp_post($url, $data);
        if (is_error($response)) {
            return error("-2", "请求接口出错:" . $response["message"]);
        }
        $result = @json_decode($response["content"], true);
        if (!empty($result["error"])) {
            return error(-1, "访问美团接口出错," . $result["error"]["code"] . ": " . $result["error"]["message"]);
        }
        return $result["data"];
    }
}

?>