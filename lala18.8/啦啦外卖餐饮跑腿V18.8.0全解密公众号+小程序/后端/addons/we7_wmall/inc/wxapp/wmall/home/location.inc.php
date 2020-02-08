<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth(false);
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    if (0 < $_W["member"]["uid"]) {
        $addresses = pdo_getall("tiny_wmall_address", array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]));
    }
    imessage(error(0, $addresses), "", "ajax");
} else {
    if ($ta == "suggestion") {
        $key = trim($_GPC["key"]);
        $query = array("key" => "2ECBZ-DXGLS-26MOI-6XMGC-QLTA6-SYFYL", "keyword" => $key, "region" => "全国", "region_fix" => 1, "output" => "json");
        $city = trim($_GPC["city"]);
        if (!empty($city)) {
            $query["region"] = $city;
        } else {
            $plugin = trim($_GPC["plugin"]) ? trim($_GPC["plugin"]) : "takeout";
            $config = $_W["we7_wmall"]["config"];
            if ($plugin == "takeout") {
                $city = $config["takeout"]["range"]["city"];
            } else {
                if ($plugin == "errander") {
                    $city = get_plugin_config("errander.city");
                }
            }
            $query["region"] = $city;
        }
        $query = http_build_query($query);
        $result = ihttp_get("http://apis.map.qq.com/ws/place/v1/suggestion?" . $query);
        if (is_error($result)) {
            imessage(error(-1, "访问出错"), "", "ajax");
        }
        $result = @json_decode($result["content"], true);
        if ($result["status"] != 0) {
            imessage(error(-1, $result["message"]), "", "ajax");
        }
        if (!empty($result["data"])) {
            foreach ($result["data"] as $key => &$val) {
                $val["name"] = $val["title"];
                $val["address"] = $val["address"];
                $val["location_y"] = $val["location"]["lng"];
                $val["lng"] = $val["location_y"];
                $val["location_x"] = $val["location"]["lat"];
                $val["lat"] = $val["location_x"];
            }
        }
        imessage(error(0, $result["data"]), "", "ajax");
    }
}

?>