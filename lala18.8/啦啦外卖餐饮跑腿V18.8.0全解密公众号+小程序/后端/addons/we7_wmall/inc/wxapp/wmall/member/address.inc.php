<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
$_W["agentid"] = 0 < intval($_GPC["agentid"]) ? intval($_GPC["agentid"]) : $_W["agentid"];
if ($ta == "list") {
    $sid = intval($_GPC["sid"]);
    $erranderId = intval($_GPC["erranderId"]);
    if (0 < $sid) {
        $addresses = member_fetchall_address_bystore($sid);
        $available = array();
        $disavailable = array();
        if (!empty($addresses)) {
            foreach ($addresses as $val) {
                if ($val["available"] == 1) {
                    $available[] = $val;
                } else {
                    $disavailable[] = $val;
                }
            }
        }
        $addresses = array("available" => $available, "dis_available" => $disavailable);
    } else {
        if (0 < $erranderId) {
            $_config_errander = get_plugin_config("errander");
            $filter = array("serve_radius" => $_config_errander["serve_radius"], "location_x" => $_config_errander["map"]["location_x"], "location_y" => $_config_errander["map"]["location_y"], "nokey" => 1);
            $addresses = member_fetchall_address($filter);
        } else {
            $addresses = member_fetchall_address();
            $addresses = array_values($addresses);
        }
    }
    $config = array("use_weixin_address" => $_W["we7_wmall"]["config"]["member"]["use_weixin_address"]);
    $respon = array("errno" => 0, "message" => $addresses, "config" => $config);
    imessage($respon, "", "ajax");
    return 1;
} else {
    if ($ta == "post") {
        $id = intval($_GPC["id"]);
        if (0 < $id) {
            $address = member_fetch_address($id);
            if (empty($address)) {
                imessage(error(-1, "地址不存在或已经删除"), "", "ajax");
            }
        } else {
            $address = array("mobile" => $_W["member"]["mobile"], "realname" => $_W["member"]["realname"], "area_id" => 0, "area_parentid" => 0, "sex" => $_W["member"]["sex"] == "男" ? "先生" : "女士","tag" => "0");
        }
        $store = array("auto_get_address" => 1);
        $sid = intval($_GPC["sid"]);
        if (0 < $sid) {
            $store = store_fetch($sid);
        }
        if ($_W["is_agent"]) {
            $store["auto_get_address"] = 1;
        }
        $address["store"] = $store;
        $address["address_type"] = 0;
        if ((0 < $id && 0 < $address["area_id"] && 0 < $address["area_parentid"] || empty($id)) && check_plugin_perm("area") && $_W["we7_wmall"]["config"]["mall"]["address_type"] == 1) {
            $address["address_type"] = 1;
            $store["auto_get_address"] = 1;
            $address["store"]["auto_get_address"] = $store["auto_get_address"];
            mload()->model("plugin");
            pload()->model("area");
            $area_group = area_store_areas_fetchAll($store);
            $address["area_parent_index"] = 0;
            $address["area_index"] = 0;
            if (!empty($area_group) && !empty($address["id"]) && !empty($address["area_id"]) && !empty($address["area_parentid"])) {
                foreach ($area_group as $key1 => $parent) {
                    if ($parent["id"] == $address["area_parentid"]) {
                        $address["area_parent_index"] = $key1;
                        if (!empty($parent["child"])) {
                            foreach ($parent["child"] as $key2 => $child) {
                                if ($child["id"] == $address["area_id"]) {
                                    $address["area_index"] = $key2;
                                }
                            }
                        }
                    }
                }
            }
            $address["areas"] = $area_group;
        }
        if ($_W["ispost"]) {
            if (empty($_GPC["realname"]) || empty($_GPC["mobile"])) {
                imessage(error(-1, "信息有误"), "", "ajax");
            }
            $data = array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "realname" => trim($_GPC["realname"]), "sex" => trim($_GPC["sex"]), "mobile" => trim($_GPC["mobile"]), "address" => trim($_GPC["address"]), "number" => trim($_GPC["number"]), "location_x" => floatval($_GPC["location_x"]), "location_y" => floatval($_GPC["location_y"]), "type" => 1, "area_id" => intval($_GPC["area_id"]), "area_parentid" => intval($_GPC["area_parentid"]), "tag" => intval($_GPC["tag"]));
            $force = intval($_GPC["force"]);
            $channel = 0 < $sid ? "takeout" : trim($_GPC["channel"]);
            $order_id = intval($_GPC["order_id"]);
            if (!$force) {
                if ($channel == "takeout") {
                    if (empty($store["auto_get_address"])) {
                        $city = $_W["we7_wmall"]["config"]["takeout"]["range"]["city"];
                        $location = geocode_geo($data["address"], $city);
                        $data["location_x"] = empty($location["location"][1]) ? "" : $location["location"][1];
                        $data["location_y"] = empty($location["location"][0]) ? "" : $location["location"][0];
                    } else {
                        $address = member_takeout_address_check($sid, $data);
                        if (is_error($address)) {
                        imessage(error(-1000, "亲,您的地址已超出商家的配送范围了"), "", "ajax");
                        }
                        $config = $_W["we7_wmall"]["config"]["takeout"]["order"]["order_update"];
                        if (0 < $order_id && !empty($config) && $config["status"] == 1) {
                            $order = order_fetch($order_id);
                            $distance = batch_calculate_distance(array($address["location_y"], $address["location_x"]), array($order["location_y"], $order["location_x"]), 1);
                            if (is_error($distance)) {
                                imessage($distance, "", "ajax");
                            }
                            if ($config["newaddress_distance"] * 1000 < $distance[0]["distance"]) {
                            imessage(error(-1000, "新地址与原地址的距离超过" . $config["newaddress_distance"] . "km"), "", "ajax");
                            }
                        }
                    }
                } else {
                    if ($channel == "errander") {
                        mload()->model("plugin");
                        pload()->model("errander");
                        $address = member_errander_address_check($data);
                        if (is_error($address)) {
                            imessage(error(-1000, "亲,您的地址已超出跑腿的服务范围了"), "", "ajax");
                        }
                    }
                }
            }
            if (!empty($id)) {
                pdo_update("tiny_wmall_address", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
            } else {
                pdo_insert("tiny_wmall_address", $data);
                $id = pdo_insertid();
            }
            imessage(error(0, $id), "", "ajax");
        }
        imessage(error(0, $address), "", "ajax");
        return 1;
    } else {
        if ($ta == "location") {
            $config = $_W["we7_wmall"]["config"]["takeout"];
            $map_type = "gaode";
            $map = array("center" => array("location_x" => 39.90923, "location_y" => 116.397428), "serve_radius" => 0);
            if (!empty($config["range"]["map"])) {
                $map["center"] = array("location_x" => floatval($config["range"]["map"]["location_x"]), "location_y" => floatval($config["range"]["map"]["location_y"]));
                if (!empty($config["map"]["keywords"])) {
                    $map["keywords"] = $config["map"]["keywords"];
                }
            }
            if (!empty($config["range"]["serve_radius"])) {
                $map["serve_radius"] = $config["range"]["serve_radius"];
            }
            $erranderId = intval($_GPC["erranderId"]);
            if (0 < $erranderId) {
                $_config_errander = get_plugin_config("errander");
                if (!empty($_config_errander["map"]["location_x"]) && !empty($_config_errander["map"]["location_y"])) {
                    $map["center"] = array("location_x" => $_config_errander["map"]["location_x"], "location_y" => $_config_errander["map"]["location_y"]);
                }
            }
            $result = array("map" => $map, "map_type" => $map_type);
            imessage(error(0, $result), "", "ajax");
        } else {
            if ($ta == "del") {
                $id = intval($_GPC["id"]);
                pdo_delete("tiny_wmall_address", array("uniacid" => $_W["uniacid"], "id" => $id));
                imessage(error(0, "删除成功"), "", "ajax");
            } else {
                if ($ta == "default") {
                    $id = intval($_GPC["id"]);
                    pdo_update("tiny_wmall_address", array("is_default" => 0), array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "type" => 1));
                    pdo_update("tiny_wmall_address", array("is_default" => 1), array("uniacid" => $_W["uniacid"], "id" => $id));
                    exit;
                }
                if ($ta == "wxaddress_add") {
                    if ($_W["ispost"]) {
                        if (empty($_GPC["realname"]) || empty($_GPC["mobile"])) {
                        imessage(error(-1, "信息有误"), "", "ajax");
                        }
                        $address_detail = trim($_GPC["detailInfo"]);
                        if (empty($address_detail)) {
                        imessage(error(-1, "地址不能为空"), "", "ajax");
                        }
                        $address = trim($_GPC["address"]);
                        $location = geocode_geo($address, $_GPC["cityName"]);
                        $data = array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "realname" => trim($_GPC["realname"]), "mobile" => trim($_GPC["mobile"]), "address" => trim($_GPC["address"]), "location_x" => $location["location"][1], "location_y" => $location["location"][0], "type" => 1);
                        $sid = intval($_GPC["sid"]);
                        $force = intval($_GPC["force"]);
                        $channel = 0 < $sid ? "takeout" : trim($_GPC["channel"]);
                        if (!$force) {
                            if ($channel == "takeout") {
                                $address = member_takeout_address_check($sid, $data);
                                if (is_error($address)) {
                                imessage(error(-1000, "亲,您的地址已超出商家的配送范围了"), "", "ajax");
                                }
                            } else {
                                if ($channel == "errander") {
                                    mload()->model("plugin");
                                    pload()->model("errander");
                                    $address = member_errander_address_check($data);
                                    if (is_error($address)) {
                                    imessage(error(-1000, "亲,您的地址已超出跑腿的服务范围了"), "", "ajax");
                                    }
                                }
                            }
                        }
                        pdo_insert("tiny_wmall_address", $data);
                        $id = pdo_insertid();
                        imessage(error(0, $id), "", "ajax");
                        return 1;
                    }
                } else {
                    if ($ta == "handAdd") {
                        $address = trim($_GPC["address"]);
                        if (empty($address)) {
                            imessage(error(-1, "请输入收货地址"), "", "ajax");
                        }
                        $city = $_W["we7_wmall"]["config"]["takeout"]["range"]["city"];
                        $map_type = trim($_GPC["map_type"]);
                        $map_type = empty($map_type) ? "gaode" : $map_type;
                        if ($map_type == "gaode") {
                            $location = geocode_geo($address, $city);
                        } else {
                            if ($map_type == "google") {
                                mload()->model("google");
                                $location = google_geocode_geo($address);
                            }
                        }
                        if (is_error($location)) {
                            imessage($location, "", "ajax");
                        }
                        $location_x = $location["location"][1];
                        $location_y = $location["location"][0];
                        if (empty($location_x) || empty($location_y)) {
                            imessage(error(-1, "未查询到该地址对应的坐标，请重新输入"), "", "ajax");
                        }
                        $result = array("address" => array("address" => $address, "lat" => $location_x, "lng" => $location_y, "location_x" => $location_x, "location_y" => $location_y));
                        imessage(error(0, $result), "", "ajax");
                    }
                }
            }
        }
    }
}

?>