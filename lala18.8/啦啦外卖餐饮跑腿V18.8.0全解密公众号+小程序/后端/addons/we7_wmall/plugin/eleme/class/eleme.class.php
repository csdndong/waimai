<?php
defined("IN_IA") or exit("Access Denied");
load()->func("communication");
class Eleme
{
    protected $app = NULL;
    public $config = array();
    public function __construct($sid = 0)
    {
        global $_W;
        $store = store_fetch($sid, array("data", "eleme_status", "elemeShopId"));
        $this->config = get_plugin_config("eleme");
        $this->app = array("sid" => $sid, "store" => array("eleme_status" => $store["eleme_status"], "eleme" => $store["data"]["eleme"], "shopid" => $store["elemeShopId"]), "key" => $this->config["key"], "secret" => $this->config["secret"]);
        $this->shopid = $store["elemeShopId"];
        $api_urls = array("sandbox" => "https://open-api-sandbox.shop.ele.me/", "open" => "https://open-api.shop.ele.me/");
        $this->api_url = $api_urls["open"];
    }
    public function getOauthCodeUrl($callback, $state = "xyz")
    {
        global $_W;
        $callback = urlencode($callback);
        return (string) $this->api_url . "authorize?response_type=code&client_id=" . $this->app["key"] . "&redirect_uri=" . $callback . "&state=" . $state . "&scope=all";
    }
    public function getAccessTokenByCode($code, $callback)
    {
        global $_W;
        $body = array("grant_type" => "authorization_code", "code" => $code, "redirect_uri" => $callback, "client_id" => $this->app["key"]);
        $extra = array("Authorization" => "Basic " . base64_encode(urlencode($this->app["key"]) . ":" . urlencode($this->app["secret"])));
        $response = ihttp_request((string) $this->api_url . "token", http_build_query($body), $extra);
        if (is_error($response)) {
            message("获取饿了么授权失败: " . $response["message"]);
        }
        $result = @json_decode($response["content"], true);
        if (!empty($result["error"])) {
            return error(-1, "获取饿了么授权失败, 错误代码: " . $result["error"] . ", 错误信息: " . $result["error_description"]);
        }
        $record = array();
        $record["access_token"] = $result["access_token"];
        $record["refresh_token"] = $result["refresh_token"];
        $record["expire"] = TIMESTAMP + $result["expires_in"] - 200;
        $cachekey = "ele:accesstoken:" . $_W["uniacid"] . ":" . $this->app["sid"];
        icache_write($cachekey, $record);
        return $result;
    }
    public function refreshAccessTokenByCode($refresh_token)
    {
        global $_W;
        $body = array("grant_type" => "refresh_token", "refresh_token" => $refresh_token, "scope" => "all");
        $extra = array("Authorization" => "Basic " . base64_encode(urlencode($this->app["key"]) . ":" . urlencode($this->app["secret"])));
        $response = ihttp_request((string) $this->api_url . "token", http_build_query($body), $extra);
        if (is_error($response)) {
            message("获取饿了么授权失败: " . $response["message"]);
        }
        $result = @json_decode($response["content"], true);
        if (!empty($result["error"])) {
            return error(-1, "获取饿了么授权失败, 错误代码: " . $result["error"] . ", 错误信息: " . $result["error_description"]);
        }
        $record = array();
        $record["access_token"] = $result["access_token"];
        $record["refresh_token"] = $result["refresh_token"];
        $record["expire"] = TIMESTAMP + $result["expires_in"] - 200;
        $cachekey = "ele:accesstoken:" . $_W["uniacid"] . ":" . $this->app["sid"];
        icache_write($cachekey, $record);
        return $result;
    }
    public function getAccessToken()
    {
        global $_W;
        $cachekey = "ele:accesstoken:" . $_W["uniacid"] . ":" . $this->app["sid"];
        $cache = icache_load($cachekey);
        if (!empty($cache) && !empty($cache["access_token"]) && TIMESTAMP < $cache["expire"]) {
            if ($cache["expire"] - TIMESTAMP < 1800) {
                $cache = $this->refreshAccessTokenByCode($cache["refresh_token"]);
            }
            return $cache["access_token"];
        }
        if (empty($this->app["key"]) || empty($this->app["secret"])) {
            return error("-1", "未填写饿了么的 key 或 secret！");
        }
        return error("-1", "accesstoken失效");
    }
    public function orderStatusTransform($status, $type = 0)
    {
        $rounter = array("pending" => 0, "unprocessed" => 1, "valid" => 2, "invalid" => 6, "settled" => 5, "refunding" => 6);
        if (empty($type)) {
            return $rounter[$status];
        }
        $rounter = array_flip($rounter);
        return $rounter[$status];
    }
    public function getRolecn($role)
    {
        $roles = array("1" => "下单用户", "2" => "饿了么系统", "3" => "饿了么商户", "4" => "饿了么客服", "5" => "饿了么开放平台系统", "6" => "饿了么短信系统", "7" => "饿了么无线打印机系统", "8" => "饿了么风控系统");
        return $roles[$role];
    }
    public function buildRPCSign($protocol)
    {
        $access_token = $protocol["token"];
        if (empty($access_token)) {
            $access_token = $this->getAccessToken();
            if (is_error($access_token)) {
                return $access_token;
            }
        }
        $merged = array_merge($protocol["metas"], $protocol["params"]);
        ksort($merged);
        $string = "";
        foreach ($merged as $key => $value) {
            $string .= $key . "=" . json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        $splice = $protocol["action"] . $access_token . $string . $this->app["secret"];
        return strtoupper(md5($splice));
    }
    public function httpPost($action, $params)
    {
        $access_token = $this->getAccessToken();
        if (is_error($access_token)) {
            return $access_token;
        }
        $protocol = array("nop" => "1.0.0", "id" => create_uuid(), "action" => $action, "token" => $access_token, "metas" => array("app_key" => $this->app["key"], "timestamp" => TIMESTAMP), "params" => $params);
        $protocol["signature"] = $this->buildRPCSign($protocol);
        if (count($params) == 0) {
            $protocol["params"] = (object) array();
        }
        $response = ihttp_request((string) $this->api_url . "api/v1/", json_encode($protocol), array("Content-Type" => "application/json"));
        if (is_error($response)) {
            return error("-2", "请求接口出错:" . $response["message"]);
        }
        $result = @json_decode($response["content"], true);
        if (!empty($result["error"])) {
            if ($result["error"]["code"] == "UNAUTHORIZED") {
                return error(-1, (string) $result["error"]["code"] . ": " . $result["error"]["message"] . ",请重新去进行饿了么授权");
            }
            return error(-1, (string) $result["error"]["code"] . ": " . $result["error"]["message"] . ",");
        }
        return $result["result"];
    }
}

?>