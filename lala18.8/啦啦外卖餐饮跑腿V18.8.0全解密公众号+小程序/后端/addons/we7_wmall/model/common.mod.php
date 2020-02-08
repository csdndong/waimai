<?php

defined("IN_IA") or exit("Access Denied");
if (!function_exists("get_available_formid")) {
    function get_available_formid($openid, $autodel = true)
    {
        $form = pdo_fetch("select * from " . tablename("tiny_wmall_wxapp_formid_log") . " where openid = :openid and endtime > :endtime order by id asc", array(":openid" => $openid, ":endtime" => TIMESTAMP));
        if (!empty($form)) {
            if ($autodel) {
                pdo_delete("tiny_wmall_wxapp_formid_log", array("id" => $form["id"]));
            }
            return $form["formid"];
        }
        return false;
    }
}
if (!function_exists("get_system_config")) {
    function get_system_config($key = "", $uniacid = -1)
    {
        global $_W;
        if ($uniacid == -1) {
            $uniacid = intval($_W["uniacid"]);
        }
        $config = pdo_get("tiny_wmall_config", array("uniacid" => $uniacid), array("sysset", "pluginset", "id"));
        if (empty($config["id"])) {
            $init_config = array("uniacid" => $uniacid);
            pdo_insert("tiny_wmall_config", $init_config);
            return array();
        }
        if (defined("IN_WXAPP") && $key == "payment") {
            $pluginset = iunserializer($config["pluginset"]);
            $config_wxapp = $pluginset["wxapp"];
            return $config_wxapp["payment"];
        }
        $sysset = iunserializer($config["sysset"]);
        if (!is_array($sysset)) {
            $sysset = array();
        }
        $pluginset = iunserializer($config["pluginset"]);
        if (!is_array($pluginset)) {
            $pluginset = array();
        }
        $sysset["wxapp"] = $pluginset["wxapp"];
        unset($sysset["wxapp"]["menu"]);
        unset($sysset["wxapp"]["extPages"]);
        $_W["is_agentconfig"] = 0;
        if (0 < $_W["agentid"]) {
            $sysset["manager_plateform"] = $sysset["manager"];
            $sysset_agent = get_agent_system_config();
            if (!empty($sysset_agent)) {
                $sysset = multimerge($sysset, $sysset_agent);
            }
            $_W["is_agentconfig"] = $_W["agentid"];
        }
        if (empty($sysset["takeout"]) || empty($sysset["takeout"]["range"]["map"]["location_x"])) {
            $sysset["takeout"]["range"]["map"] = array("location_x" => "39.908743", "location_y" => "116.397573");
        }
        if (empty($sysset["sms"]["verify"])) {
            $sysset["sms"]["verify"] = array("clerk_register" => 1, "consumer_register" => 1);
        }
        if (empty($sysset["store"]["activity"]["perm"])) {
            $all_activity = store_all_activity();
            $all_activity = array_keys($all_activity);
            foreach ($all_activity as $type) {
                $sysset["store"]["activity"]["perm"][$type] = array("status" => 1, "cancel_status" => 1);
            }
        }
        if (!empty($sysset["mall"]["logo"])) {
            $sysset["mall"]["logo"] = tomedia($sysset["mall"]["logo"]);
        }
        if (empty($sysset["getcash"]["channel"])) {
            $sysset["getcash"]["channel"]["wechat"] = "wechat";
        }
        if (MODULE_FAMILY == "wxapp") {
            $sysset["getcash"]["channel"]["wechat"] = "wxapp";
        }
        if (empty($key)) {
            return $sysset;
        }
        $keys = explode(".", $key);
        $counts = count($keys);
        if ($counts == 1) {
            return $sysset[$key];
        }
        if ($counts == 2) {
            return $sysset[$keys[0]][$keys[1]];
        }
        if ($counts == 3) {
            return $sysset[$keys[0]][$keys[1]][$keys[2]];
        }
    }
}
if (!function_exists("get_plugin_config")) {
    function get_plugin_config($key = "")
    {
        global $_W;
        $_W["uniacid"] = intval($_W["uniacid"]);
        $config = pdo_get("tiny_wmall_config", array("uniacid" => $_W["uniacid"]), array("pluginset"));
        if (empty($config)) {
            return array();
        }
        $pluginset = iunserializer($config["pluginset"]);
        if (!is_array($pluginset)) {
            flog("page", $_W["siteurl"], "bt");
            flog("agentid", $_W["agentid"], "bt");
            flog("pluginset", $config["pluginset"], "bt");
            flog("key", $key, "bt");
            return array();
        }
        if (0 < $_W["agentid"]) {
            $plugin_agent = get_agent_plugin_config();
            if (!empty($plugin_agent)) {
                $pluginset = multimerge($pluginset, $plugin_agent);
            }
        }
        if (empty($key)) {
            return $pluginset;
        }
        if (in_array($key, array("pintuan", "kanjia", "seckill", "tongcheng", "haodian"))) {
            $key = "gohome";
        }
        $keys = explode(".", $key);
        $plugin = $keys[0];
        if (!empty($plugin)) {
            $config_plugin = $pluginset[$plugin];
            if (!is_array($config_plugin)) {
                return array();
            }
            $count = count($keys);
            if ($count == 2) {
                return $config_plugin[$keys[1]];
            }
            if ($count == 3) {
                return $config_plugin[$keys[1]][$keys[2]];
            }
            return $config_plugin;
        }
    }
}
if (!function_exists("get_agent_system_config")) {
    function get_agent_system_config($key = "", $agentid = 0)
    {
        global $_W;
        if (empty($agentid)) {
            $agentid = $_W["agentid"];
        }
        $config = pdo_get("tiny_wmall_agent", array("uniacid" => $_W["uniacid"], "id" => $agentid), array("sysset", "id"));
        if (empty($config)) {
            return array();
        }
        $sysset = iunserializer($config["sysset"]);
        if (!is_array($sysset)) {
            $sysset = array();
        }
        if (empty($sysset["app"]) || empty($sysset["app"]["deliveryer"]["push_tags"])) {
            $sysset["app"]["deliveryer"]["push_tags"] = array("all" => random(8), "working" => random(8), "rest" => random(8), "waimai" => random(8), "paotui" => random(8));
            pdo_update("tiny_wmall_agent", array("sysset" => iserializer($sysset)), array("uniacid" => $_W["uniacid"], "id" => $agentid));
        }
        if (empty($sysset["app"]["deliveryer"]["push_tags"]["waimai"])) {
            $sysset["app"]["deliveryer"]["push_tags"]["waimai"] = random(8);
            $sysset["app"]["deliveryer"]["push_tags"]["paotui"] = random(8);
            pdo_update("tiny_wmall_agent", array("sysset" => iserializer($sysset)), array("uniacid" => $_W["uniacid"], "id" => $agentid));
        }
        if (empty($key)) {
            return $sysset;
        }
        $keys = explode(".", $key);
        $counts = count($keys);
        if ($counts == 1) {
            return $sysset[$key];
        }
        if ($counts == 2) {
            return $sysset[$keys[0]][$keys[1]];
        }
        if ($counts == 3) {
            return $sysset[$keys[0]][$keys[1]][$keys[1]];
        }
    }
}
if (!function_exists("get_agent_plugin_config")) {
    function get_agent_plugin_config($key = "")
    {
        global $_W;
        $config = pdo_get("tiny_wmall_agent", array("uniacid" => $_W["uniacid"], "id" => $_W["agentid"]), array("pluginset"));
        if (empty($config)) {
            return array();
        }
        $pluginset = iunserializer($config["pluginset"]);
        if (!is_array($pluginset)) {
            return array();
        }
        if (empty($key)) {
            return $pluginset;
        }
        $keys = explode(".", $key);
        $plugin = $keys[0];
        if (!empty($plugin)) {
            $config_plugin = $pluginset[$plugin];
            if (!is_array($config_plugin)) {
                return array();
            }
            $count = count($keys);
            if ($count == 2) {
                return $config_plugin[$keys[1]];
            }
            if ($count == 3) {
                return $config_plugin[$keys[1]][$keys[2]];
            }
            return $config_plugin;
        }
    }
}
function check_cache_status($key, $timelimit = 300)
{
    global $_W;
    $cache = cache_read($key);
    if (empty($cache) || 0 < $cache["starttime"] && $cache["starttime"] + $timelimit < TIMESTAMP) {
        return false;
    }
    return true;
}
function set_cache($key, $value)
{
    global $_W;
    if (empty($value["starttime"])) {
        $value["starttime"] = TIMESTAMP;
    }
    cache_write($key, $value);
    return true;
}
function get_cache($key)
{
    global $_W;
    $cache = cache_read($key);
    if (empty($cache) || 0 < $cache["starttime"] && $cache["starttime"] + $timelimit < TIMESTAMP) {
        return false;
    }
    return $cache;
}
function set_system_config($key, $value)
{
    global $_W;
    $sysset = get_system_config();
    $keys = explode(".", $key);
    $counts = count($keys);
    if ($counts == 1) {
        $sysset[$keys[0]] = $value;
    } else {
        if ($counts == 2) {
            if (!is_array($sysset[$keys[0]])) {
                $sysset[$keys[0]] = array();
            }
            $sysset[$keys[0]][$keys[1]] = $value;
        } else {
            if ($counts == 3) {
                if (!is_array($sysset[$keys[0]])) {
                    $sysset[$keys[0]] = array();
                } else {
                    if (!is_array($sysset[$keys[0]][$keys[1]])) {
                        $sysset[$keys[0]][$keys[1]] = array();
                    }
                }
                $sysset[$keys[0]][$keys[1]][$keys[2]] = $value;
            }
        }
    }
    pdo_update("tiny_wmall_config", array("sysset" => iserializer($sysset)), array("uniacid" => $_W["uniacid"]));
    return true;
}
function set_global_config($key, $value)
{
    global $_W;
    $_W["uniacid"] = 0;
    $status = set_system_config($key, $value);
    return $status;
}
function get_global_config($key = "")
{
    $result = get_system_config($key, 0);
    return $result;
}
function get_available_payment($order_type = "", $sid = 0, $all = false, $orderType = 1)
{
    global $_W;
    if (!defined("IN_WXAPP")) {
        $payment = $_W["we7_wmall"]["config"]["payment"];
    } else {
        $payment = $_W["we7_wxapp"]["config"]["payment"];
    }
    if (empty($order_type)) {
        $payment = $payment["weixin"];
    } else {
        if (is_wxapp()) {
            $payment = $payment["wxapp"];
        } else {
            if (is_weixin() || is_vue()) {
                if (is_h5app()) {
                    $payment = $payment["app"];
                } else {
                    $payment = $payment["weixin"];
                    if (is_vue()) {
                        if (!is_weixin()) {
                            $index = array_search("peerpay", $payment);
                            if ($index !== false) {
                                unset($payment[$index]);
                            }
                        }
                        $index = array_search("yimafu", $payment);
                        if ($index !== false) {
                            unset($payment[$index]);
                        }
                    }
                }
            } else {
                if (is_h5app()) {
                    $payment = $payment["app"];
                } else {
                    if (is_qianfan() || is_majia()) {
                        $payment = $payment["weixin"];
                    } else {
                        $payment = $payment["wap"];
                    }
                }
            }
        }
        if (empty($payment)) {
            return array();
        }
        if ($order_type == "takeout") {
            if ($orderType == 3) {
                mload()->model("store");
                $tangshi_payment = store_get_data($sid, "tangshi.payment");
                if (empty($tangshi_payment)) {
                    return array();
                }
                $payment[] = "finishMeal";
                $store_payment = $tangshi_payment;
            } else {
                $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $sid), array("payment"));
                if (empty($store["payment"])) {
                    return array();
                }
                $store["payment"] = iunserializer($store["payment"]);
                $store_payment = $store["payment"];
            }
            foreach ($payment as $key => $row) {
                if (!in_array($row, $store_payment)) {
                    if ($orderType == 1 && $row == "peerpay") {
                        continue;
                    }
                    unset($payment[$key]);
                }
                if ($orderType == 4 && $row == "delivery") {
                    unset($payment[$key]);
                }
            }
        } else {
            if ($order_type == "paybill") {
                $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $sid), array("payment"));
                if (empty($store["payment"])) {
                    return array();
                }
                $store["payment"] = iunserializer($store["payment"]);
                foreach ($payment as $key => $row) {
                    if (!in_array($row, $store["payment"]) || $row == "delivery") {
                        unset($payment[$key]);
                    }
                }
            } else {
                if ($order_type == "recharge" || $order_type == "recharge_vip") {
                    $index = array_search("delivery", $payment);
                    if ($index !== false) {
                        unset($payment[$index]);
                    }
                    $index = array_search("credit", $payment);
                    if ($index !== false) {
                        unset($payment[$index]);
                    }
                    $index = array_search("peerpay", $payment);
                    if ($index !== false) {
                        unset($payment[$index]);
                    }
                } else {
                    if ($order_type == "freelunch") {
                        $index = array_search("delivery", $payment);
                        if ($index !== false) {
                            unset($payment[$index]);
                        }
                        $index = array_search("peerpay", $payment);
                        if ($index !== false) {
                            unset($payment[$index]);
                        }
                    } else {
                        if ($order_type == "mealRedpacket" || $order_type == "mealRedpacket_plus") {
                            $index = array_search("delivery", $payment);
                            if ($index !== false) {
                                unset($payment[$index]);
                            }
                            $index = array_search("peerpay", $payment);
                            if ($index !== false) {
                                unset($payment[$index]);
                            }
                        } else {
                            if ($order_type == "vip") {
                                $index = array_search("delivery", $payment);
                                if ($index !== false) {
                                    unset($payment[$index]);
                                }
                                $index = array_search("credit", $payment);
                                if ($index !== false) {
                                    unset($payment[$index]);
                                }
                                $index = array_search("peerpay", $payment);
                                if ($index !== false) {
                                    unset($payment[$index]);
                                }
                            } else {
                                $index = array_search("delivery", $payment);
                                if ($index !== false) {
                                    unset($payment[$index]);
                                }
                                $index = array_search("peerpay", $payment);
                                if ($index !== false) {
                                    unset($payment[$index]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    if ($all) {
        $routers = array("alipay" => array("title" => "支付宝", "value" => "alipay"), "wechat" => array("title" => "微信支付", "value" => "wechat"), "credit" => array("title" => "余额支付", "value" => "credit"), "delivery" => array("title" => "货到付款", "value" => "delivery"), "yimafu" => array("title" => "一码付", "value" => "yimafu"), "delivery" => array("title" => "货到付款", "value" => "delivery"), "finishMeal" => array("title" => "餐后付款", "value" => "finishMeal"), "peerpay" => array("title" => "找朋友代付", "value" => "peerpay"));
        $payments = array();
        foreach ($payment as $item) {
            $payments[] = $routers[$item];
        }
        return $payments;
    } else {
        return $payment;
    }
}
function set_plugin_config($key, $value)
{
    global $_W;
    $keys = explode(".", $key);
    $counts = count($keys);
    $pluginset = get_plugin_config();
    $config_plugin = $pluginset[$keys[0]];
    if ($counts == 1) {
        $config_plugin = $value;
    } else {
        if ($counts == 2) {
            $config_plugin[$keys[1]] = $value;
        } else {
            if ($counts == 3) {
                $config_plugin[$keys[1]][$keys[2]] = $value;
            }
        }
    }
    $pluginset[$keys[0]] = $config_plugin;
    pdo_update("tiny_wmall_config", array("pluginset" => iserializer($pluginset)), array("uniacid" => $_W["uniacid"]));
    return true;
}
function set_config_text($title, $name, $value = "", $agentid = 0)
{
    global $_W;
    $config = pdo_get("tiny_wmall_text", array("uniacid" => $_W["uniacid"], "name" => $name, "agentid" => $agentid));
    if (empty($config)) {
        $data = array("uniacid" => $_W["uniacid"], "agentid" => $agentid, "name" => $name, "title" => $title, "value" => is_array($value) ? iserializer($value) : $value);
        pdo_insert("tiny_wmall_text", $data);
    } else {
        $data = array("uniacid" => $_W["uniacid"], "title" => $title, "value" => is_array($value) ? iserializer($value) : $value);
        pdo_update("tiny_wmall_text", $data, array("uniacid" => $_W["uniacid"], "name" => $name, "agentid" => $agentid));
    }
    return true;
}
function get_config_text($name, $agentid = 0)
{
    global $_W;
    $config = pdo_get("tiny_wmall_text", array("uniacid" => $_W["uniacid"], "agentid" => $agentid, "name" => $name));
    if ($name = "takeout_delivery_time") {
        $config["value"] = iunserializer($config["value"]);
    }
    return $config["value"];
}
function get_account_perm($key = "", $uniacid = 0)
{
    global $_W;
    if (empty($uniacid)) {
        $uniacid = $_W["uniacid"];
    }
    $perm = pdo_get("tiny_wmall_perm_account", array("uniacid" => $uniacid));
    if (empty($perm)) {
        return false;
    }
    if (!empty($perm)) {
        $perm["plugins"] = iunserializer($perm["plugins"]);
        if (!is_array($perm["plugins"])) {
            $perm["plugins"] = array();
        }
        if (empty($perm["plugins"])) {
            $perm["plugins"] = array("none");
        }
        if (!empty($key)) {
            return $perm[$key];
        }
    }
    return $perm;
}
function get_available_plugin()
{
    global $_W;
    mload()->model("plugin");
    $plugins = plugin_fetchall();
    $perms = get_account_perm("plugins");
    $array = array();
    $plugin_config = get_plugin_config();
    foreach ($plugins as $row) {
        if (!empty($perms) && !in_array($row["name"], $perms)) {
            continue;
        }
        if ($row["name"] == "svip" && !empty($plugin_config["svip"]) && $plugin_config["svip"]["basic"]["status"] != 1) {
            continue;
        }
        $array[] = $row["name"];
    }
    return $array;
}
function get_available_perm()
{
    global $_W;
    $data = array("plugin" => get_available_plugin());
    return $data;
}
function check_max_store_perm()
{
    global $_W;
    $max_store = intval(get_account_perm("max_store"));
    if (!$max_store) {
        return true;
    }
    $now_store = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]));
    $now_store = intval($now_store);
    if ($max_store <= $now_store) {
        return false;
    }
    return true;
}
function get_mall_share()
{
    global $_W;
    $config_share = $_W["we7_wmall"]["config"]["share"];
    $_share = array("title" => $config_share["title"], "desc" => $config_share["desc"], "link" => !empty($config_share["link"]) ? $config_share["link"] : imurl("wmall/home/index", array(), true), "imgUrl" => tomedia($config_share["imgUrl"]));
    if (defined("IN_VUE")) {
        $_share["link"] = ivurl("pages/home/index", array(), true);
    }
    return $_share;
}
function is_agent()
{
    $status = 0;
    if (check_plugin_perm("agent") && get_plugin_config("agent.basic.status") == 1) {
        $status = 1;
    }
    return $status;
}
function set_agent_system_config($key, $value, $agentid = 0)
{
    global $_W;
    if (empty($agentid)) {
        $agentid = $_W["agentid"];
    }
    $sysset = get_agent_system_config("", $agentid);
    $keys = explode(".", $key);
    $counts = count($keys);
    if ($counts == 1) {
        $sysset[$keys[0]] = $value;
    } else {
        if ($counts == 2) {
            $sysset[$keys[0]][$keys[1]] = $value;
        } else {
            if ($counts == 3) {
                $sysset[$keys[0]][$keys[1]][$keys[2]] = $value;
            }
        }
    }
    pdo_update("tiny_wmall_agent", array("sysset" => iserializer($sysset)), array("uniacid" => $_W["uniacid"], "id" => $agentid));
    return true;
}
function set_agent_config_text($title, $key, $value = "")
{
    global $_W;
    set_config_text($title, $key, $value, $_W["agentid"]);
    return true;
}
function get_agent_config_text($name)
{
    global $_W;
    return get_config_text($name, $_W["agentid"]);
}
function set_agent_plugin_config($key, $value)
{
    global $_W;
    $keys = explode(".", $key);
    $counts = count($keys);
    $pluginset = get_agent_plugin_config();
    $config_plugin = $pluginset[$keys[0]];
    if ($counts == 1) {
        $config_plugin = $value;
    } else {
        if ($counts == 2) {
            $config_plugin[$keys[1]] = $value;
        } else {
            if ($counts == 3) {
                $config_plugin[$keys[1]][$keys[2]] = $value;
            }
        }
    }
    $pluginset[$keys[0]] = $config_plugin;
    pdo_update("tiny_wmall_agent", array("pluginset" => iserializer($pluginset)), array("uniacid" => $_W["uniacid"], "id" => $_W["agentid"]));
    return true;
}
function get_agent_perm($key = "", $agentid = 0)
{
    global $_W;
    if (empty($agentid)) {
        $agentid = $_W["agentid"];
    }
    $agent = pdo_get("tiny_wmall_agent", array("uniacid" => $_W["uniacid"], "id" => $agentid));
    if (empty($agent)) {
        return false;
    }
    $agent["plugins"] = array("errander", "bargain", "gohome", "diypage", "zhunshibao");
    if (!empty($key)) {
        return $agent[$key];
    }
    return $agent;
}
function check_mall_status($agentid = 0)
{
    global $_W;
    if ($_W["is_agent"]) {
        if (!empty($_W["agent"])) {
            $agent = $_W["agent"];
            $config = $_W["we7_wmall"]["config"]["close"];
        } else {
            $config = get_agent_system_config("close", $agentid);
            $agent = pdo_get("tiny_wmall_agent", array("uniacid" => $_W["uniacid"], "id" => $agentid), array("status"));
        }
        $tips = !empty($config["tips"]) ? $config["tips"] : "亲！平台正在整治商家中。。。";
        if ($config["status"] == 2) {
            return error(-1, (string) $tips);
        }
        if (empty($agent["status"])) {
            return error(-1, (string) $tips);
        }
    } else {
        $config = $_W["we7_wmall"]["config"]["close"];
        if ($config["status"] == 2) {
            $tips = !empty($config["tips"]) ? $config["tips"] : "平台正在整治商家中。。。";
            return error(-1, (string) $tips);
        }
    }
    return true;
}
function get_user($uid = 0)
{
    global $_W;
    if (empty($uid)) {
        if ($_W["role"] == "agent_operator") {
            $uid = $_W["we7_wmall"]["agent_user"]["id"];
        } else {
        $uid = $_W["uid"];
        }
    }
    $user = pdo_fetch("select a.*,b.perms as perms_role from " . tablename("tiny_wmall_perm_user") . " as a left join " . tablename("tiny_wmall_perm_role") . " as b on a.roleid = b.id where a.uniacid = :uniacid and a.agentid = :agentid and a.uid = :uid", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":uid" => $uid));
    if (empty($user)) {
        return false;
    }
    $user["perms_role"] = explode(",", $user["perms_role"]);
    $user["perms"] = explode(",", $user["perms"]);
    $user["perms"] = array_merge($user["perms"], $user["perms_role"]);
    return $user;
}
function check_perm($perm, $redirct = false)
{
    global $_W;
    $redircts = array("common", "store");
    if (in_array($_W["_controller"], $redircts)) {
        return true;
    }
    if ($_W["isfounder"] == 1 || $_W["perms"] == "all") {
        return true;
    }
    if (empty($_W["perms"])) {
        return false;
    }
    if ($perm == "plugin.index") {
        return true;
    }
    if (in_array($perm, $_W["perms"])) {
        return true;
    }
    if (defined("IN_PLATEFORM")) {
        $all_perms = array();
        if ($_W["plateformer"]["usertype"] == "agenter") {
            $all_perms = get_agent_perms();
        } else {
            $all_perms = get_all_perms(true);
        }
        if (!in_array($perm, $all_perms)) {
            return true;
        }
    }
    if ($_W["role"] == "agent_operator") {
        $extraperm = array("agent.loginout", "agent.setting", "oauth.login");
        if (in_array($perm, $extraperm)) {
            return true;
        }
    }
    if ($redirct) {
        $perms_init = array("dashboard.index", "merchant.store", "order.takeout", "statcenter.takeout", "paycenter.paybill", "merchant.store", "service.comment", "deliveryer.account", "clerk.account", "member.index", "config.mall", "errander.index", "bargain.index", "deliveryCard.index", "qianfanApp.index", "majiaApp.index", "shareRedpacket.index", "freeLunch.index", "diypage.index", "ordergrant.index", "superRedpacket.index", "creditshop.index", "agent.index", "wheel.index", "gohome.index", "svip.index", "spread.index", "advertise.index", "cloudGoods.index", "mealRedpacket.index", "storebd.index", "zhunshibao.index");
        if (in_array($perm, $perms_init)) {
            $perm_arr = explode(".", $perm);
            foreach ($_W["perms"] as $row) {
                if (strexists($row, (string) $perm_arr[0] . ".")) {
                    $perm = explode(".", $row);
                    header("location:" . iurl((string) $perm["0"] . "/" . $perm["1"]));
                    exit;
                }
            }
            return false;
        }
    }
    return false;
}
function get_all_perms($justkey = false)
{
    $all_perms = array("dashboard" => array("title" => "概括", "perms" => array("dashboard.index" => "运营概况", "dashboard.ad" => "全屏引导页", "dashboard.slide" => "幻灯片", "dashboard.nav" => "导航图标", "dashboard.notice" => "公告", "dashboard.cube" => "图片魔方")), "order" => array("title" => "订单", "perms" => array("order.takeout" => "外卖", "order.takeoutNew" => "未完成", "order.distribute" => "订单分布", "order.neworder" => "代客下单", "order.dispatch" => "调度中心-待指派", "order.records" => "调度中心-接单统计/接单记录", "order.tangshi" => "店内")), "paycenter" => array("title" => "当面付", "perms" => array("paycenter.paybill" => "买单订单")), "statcenter" => array("title" => "数据", "perms" => array("statcenter.takeout" => "外卖统计", "statcenter.paytype" => "支付方式统计", "statcenter.takeoutOrder" => "店铺订单统计", "statcenter.takeoutOrderChannel" => "订单来源统计", "statcenter.delivery" => "配送统计/配送详情", "statcenter.hot" => "热门商品", "statcenter.finance" => "财务统计", "statcenter.takeoutAgent" => "代理订单统计")), "merchant" => array("title" => "商户", "perms" => array("merchant.store" => "商户列表", "merchant.account" => "商户账户", "merchant.activity" => "商户活动/活动展示", "merchant.getcash" => "申请提现", "merchant.current" => "账户明细", "merchant.settle" => "入驻", "merchant.storage" => "商家回收站", "merchant.newsCategory" => "资讯分类", "merchant.news" => "资讯列表", "merchant.ad" => "广告", "merchant.notice" => "公告列表", "merchant.report" => "投诉列表")), "service" => array("title" => "售后", "perms" => array("service.comment" => "用户评价")), "deliveryer" => array("title" => "配送员", "perms" => array("deliveryer.plateform" => "配送员", "deliveryer.getcash" => "提现申请", "deliveryer.current" => "账户明细", "deliveryer.comment" => "配送评价", "deliveryer.storage" => "配送员回收站", "deliveryer.cover" => "注册&登录")), "clerk" => array("title" => "店员", "perms" => array("clerk.account" => "店员列表", "clerk.cover" => "注册&登录")), "member" => array("title" => "顾客", "perms" => array("member.index" => "顾客概况", "member.list" => "顾客列表", "member.groups" => "顾客等级", "member.address" => "顾客地址", "member.coupon" => "顾客代金券", "member.redpacket" => "顾客红包", "member.recharge" => "充值明细", "member.credit" => "积分明细/余额明细")), "config" => array("title" => "设置", "perms" => array("config.mall" => "基础设置/分享及关注/平台状态/oAuth设置", "config.trade" => "支付方式/支付回调/充值", "config.notice" => "模板消息/短信消息", "config.sms" => "短信接入/短信模板", "config.takeout" => "服务范围/订单相关", "config.store" => "配送模式/服务费率/商户入驻/其他批量操作", "config.deliveryer" => "配送员申请/提成及提现", "config.activity" => "新用户/红包到期通知/代金券", "config.sensitive" => "敏感词过滤", "config.member" => "顾客设置", "config.label" => "商户标签/配送标签", "config.report" => "商户举报类型", "config.help" => "常见问题", "config.cover" => "平台入口/商家管理入口/商家入驻入口/配送员入口")));
    if (check_plugin_perm("errander")) {
        $all_perms["errander"] = array("title" => "跑腿", "perms" => array("errander.order" => "订单", "errander.statcenter" => "跑腿统计", "errander.statDelivery" => "配送统计/配送详情", "errander.diypage" => "首页设置/跑腿首页跑腿场景", "errander.config" => "跑腿设置", "errander.cover" => "入口设置"));
    }
    if (check_plugin_perm("agent")) {
        $all_perms["agent"] = array("title" => "区域代理", "perms" => array("agent.agent" => "代理列表", "agent.initialize" => "数据初始化", "agent.getcash" => "提现申请", "agent.current" => "账户明细", "agent.config" => "设置", "agent.cover" => "代理入口"));
    }
    if (check_plugin_perm("deliveryCard")) {
        $all_perms["deliveryCard"] = array("title" => "配送会员卡", "perms" => array("deliveryCard.order" => "订单", "deliveryCard.setmeal" => "会员卡套餐", "deliveryCard.config" => "会员卡设置", "deliveryCard.cover" => "入口设置"));
    }
    if (check_plugin_perm("qianfanApp")) {
        $all_perms["qianfanApp"] = array("title" => "千帆APP整合", "perms" => array("qianfanApp.config" => "设置"));
    }
    if (check_plugin_perm("majiaApp")) {
        $all_perms["majiaApp"] = array("title" => "马甲APP整合", "perms" => array("majiaApp.config" => "设置"));
    }
    if (check_plugin_perm("creditshop")) {
        $all_perms["creditshop"] = array("title" => "积分兑换", "perms" => array("creditshop.order" => "兑换记录", "creditshop.goods" => "商品管理", "creditshop.config" => "系统设置", "creditshop.cover" => "入口设置"));
    }
    if (check_plugin_perm("shareRedpacket")) {
        $all_perms["shareRedpacket"] = array("title" => "分享有礼", "perms" => array("shareRedpacket.activity" => "红包活动", "shareRedpacket.cover" => "入口设置"));
    }
    if (check_plugin_perm("freeLunch")) {
        $all_perms["freeLunch"] = array("title" => "霸王餐", "perms" => array("freeLunch.activity" => "霸王餐", "freeLunch.cover" => "入口设置"));
    }
    if (check_plugin_perm("bargain")) {
        $all_perms["bargain"] = array("title" => "天天特价", "perms" => array("bargain.index" => "活动设置", "bargain.cover" => "入口设置", "bargain.goods" => "活动商品"));
    }
    if (check_plugin_perm("ordergrant")) {
        $all_perms["ordergrant"] = array("title" => "下单有礼", "perms" => array("ordergrant.config" => "活动设置", "ordergrant.record" => "奖励记录", "ordergrant.share" => "分享订单", "ordergrant.cover" => "入口设置"));
    }
    if (check_plugin_perm("superRedpacket")) {
        $all_perms["superRedpacket"] = array("title" => "超级红包", "perms" => array("superRedpacket.grant" => "发放红包", "superRedpacket.share" => "分享红包", "superRedpacket.gift" => "天降红包", "superRedpacket.coupon" => "代金券设置/显示商家"));
    }
    if (check_plugin_perm("diypage")) {
        $all_perms["diypage"] = array("title" => "平台装修", "perms" => array("diypage.menu" => "自定义菜单", "diypage.mall" => "商城设置", "diypage.danmu" => "订单弹幕", "diypage.diy" => "页面管理", "diypage.vuemenu" => "菜单列表", "diypage.vuemall" => "菜单设置", "diypage.diyPage" => "自定义DIY", "diypage.vuediyShop" => "页面设置", "diypage.template" => "模板管理"));
    }
    if (check_plugin_perm("creditshop")) {
        $all_perms["creditshop"] = array("title" => "积分商城", "perms" => array("creditshop.order" => "兑换记录", "creditshop.goods" => "商品管理", "creditshop.adv" => "幻灯片", "creditshop.category" => "商品分类", "creditshop.cover" => "入口设置"));
    }
    if (check_plugin_perm("wheel")) {
        $all_perms["wheel"] = array("title" => "幸运大转盘", "perms" => array("wheel.activity" => "活动列表", "wheel.record" => "参与记录", "wheel.scene" => "使用场景","wheel.cover"=> "入口链接"));
    }
    if (check_plugin_perm("storebd")) {
        $all_perms["storebd"] = array("title" => "店铺推广", "perms" => array("storebd.account" => "店铺推广员", "storebd.current" => "账户明细", "storebd.getcash" => "提现申请", "storebd.bind" => "店铺绑定", "storebd.config" => "基本设置"));
    }
    if (check_plugin_perm("cloudGoods")) {
        $all_perms["cloudGoods"] = array("title" => "云商品库", "perms" => array("cloudGoods.goods" => "商品库", "cloudGoods.goodsCategory" => "商品分类", "cloudGoods.menuCategory" => "菜单", "cloudGoods.storeGoods" => "商户商品", "cloudGoods.exportGoods" => "从Excel导入"));
    }
    if (check_plugin_perm("spread")) {
        $all_perms["spread"] = array("title" => "啦啦推广", "perms" => array("spread.members" => "推广员", "spread.user" => "推广关系", "spread.groups" => "推广员等级", "spread.getcash" => "提现记录", "spread.current" => "账户明细", "spread.config" => "基本设置", "spread.postera" => "推广海报", "spread.rank" => "排行榜设置", "spread.cover" => "入口设置"));
    }
    if (check_plugin_perm("mealRedpacket")) {
        $all_perms["mealRedpacket"] = array("title" => "套餐红包", "perms" => array("mealRedpacket.meal" => "套餐管理", "mealRedpacket.mealorder" => "购买记录", "mealRedpacket.plus" => "套餐红包Plus套餐管理", "mealRedpacket.plusorder" => "套餐红包Plus购买记录"));
    }
    if (check_plugin_perm("advertise")) {
        $all_perms["advertise"] = array("title" => "店铺广告", "perms" => array("advertise.trade" => "购买记录", "advertise.config" => "基本设置/支付设置", "advertise.advertise" => "为您优选推广/商家置顶推广", "advertise.slide" => "首页顶部/会员中心/收银台/订单详情"));
    }
    if (check_plugin_perm("gohome")) {
        $all_perms["gohome"] = array("title" => "啦啦生活圈", "perms" => array("gohome.slide" => "生活圈幻灯片", "gohome.nav" => "生活圈导航图标", "gohome.notice" => "生活圈公告", "gohome.order" => "订单列表", "gohome.statcenter" => "数据统计","kanjia.category" => "砍价活动分类", "kanjia.activity" => "砍价活动列表", "pintuan.category" => "拼团活动分类", "pintuan.activity" => "拼团活动列表", "pintuan.basic" => "拼团活动设置", "seckill.goods_category" => "抢购活动分类", "seckill.goods" => "抢购活动列表", "gohome.comment" => "订单评论", "gohome.complain" => "投诉列表", "gohome.memberBlack" => "黑名单", "gohome.config" => "活动设置/费率设置", "gohome.poster" => "活动海报", "gohome.cover" => "活动入口", "tongcheng.category" => "同城分类列表", "tongcheng.basic" => "同城设置", "tongcheng.slide" => "同城幻灯片", "tongcheng.information" => "同城帖子", "tongcheng.comment" => "同城评论列表", "haodian.settle" => "好店入驻设置", "haodian.slide" => "好店幻灯片", "haodian.category" => "好店商户分类", "haodian.store" => "好店商户列表"));
    }
    if (check_plugin_perm("svip")) {
        $all_perms["svip"] = array("title" => "超级会员", "perms" => array("svip.redpacket" => "超级会员专享红包", "svip.goods" => "超级会员专享商品", "svip.task" => "超级会员任务", "svip.credit" => "超级会员奖励金记录", "svip.code" => "超级会员兑换码", "svip.setmeal" => "套餐列表", "svip.order" => "购买记录", "svip.config" => "会员设置", "svip.cover" => "入口设置"));
    }
    if (check_plugin_perm("zhunshibao")) {
        $all_perms["zhunshibao"] = array("title" => "准时宝", "perms" => array("zhunshibao.config" => "准时宝设置"));
    }
    if ($justkey) {
        $perms = array();
        foreach ($all_perms as $key => $item) {
            $perms[] = $key;
            if (!empty($item["perms"])) {
                foreach ($item["perms"] as $key1 => $item1) {
                    $perms[] = $key1;
                }
            }
        }
        return $perms;
    } else {
        return $all_perms;
    }
}
function is_in_plateform_radius($lnglat)
{
    global $_W;
    global $_GPC;
    $address_type = $_W["we7_wmall"]["config"]["mall"]["address_type"];
    if ($address_type == 1) {
        return true;
    }
    $status = $_W["we7_wmall"]["config"]["takeout"]["range"]["status"];
    if (empty($status)) {
        return true;
    }
    if (empty($lnglat) || !is_array($lnglat)) {
        return false;
    }
    if (empty($lnglat[0]) || empty($lnglat[1])) {
        return false;
    }
    $areas = get_config_text("delivery_areas");
    if (empty($areas) || empty($areas["normal"]) && empty($areas["special"])) {
        return true;
    }
    $flag = false;
    if (!empty($areas["special"])) {
        $special = $areas["special"];
        foreach ($special as $svalue) {
            if (empty($svalue["startHour"]) || empty($svalue["endHour"]) || empty($svalue["areas"])) {
                continue;
            }
            $now = date("H:i");
            $now = strtotime($now);
            $startHour = strtotime($svalue["startHour"]);
            $endHour = strtotime($svalue["endHour"]);
            $crossNight = 0;
            if ($endHour < $startHour) {
                $crossNight = 1;
            }
            if (!$crossNight && $startHour <= $now && $now < $endHour || $crossNight && ($startHour <= $now || $now <= $endHour)) {
                foreach ($svalue["areas"] as $areaItem) {
                    $flag = isPointInPolygon($areaItem["path"], array($lnglat[0], $lnglat[1]));
                    if (!empty($flag)) {
                        break;
                    }
                }
                return $flag;
            }
        }
    }
    if (empty($flag)) {
        $normal = $areas["normal"];
        if (!empty($normal)) {
            foreach ($normal as $nvalue) {
                if (!empty($nvalue)) {
                    foreach ($nvalue["areas"] as $areaItem) {
                        $flag = isPointInPolygon($areaItem["path"], array($lnglat[0], $lnglat[1]));
                        if (!empty($flag)) {
                            break;
                        }
                    }
                }
            }
        } else {
            $flag = true;
        }
    }
    return $flag;
}
function mlog($type, $log_id = 0, $message = "")
{
    global $_W;
    if (empty($type)) {
        return error(-1, "日志类型不能为空");
    }
    $type_info = mlog_types($_W["role"], $type);
    if (empty($type_info["type"])) {
        return error(-1, "日志类型有误");
    }
    $content = sprintf($type_info["content"], $log_id, $message);
    $data = array("uniacid" => $_W["uniacid"], "username" => $_W["role_cn"], "uid" => $_W["uid"], "role" => $_W["role"], "type" => $type, "content" => $content, "ip" => CLIENT_IP, "address" => "", "source" => "", "addtime" => TIMESTAMP);
    pdo_insert("tiny_wmall_operate_log", $data);
    return true;
}
function mlog_types($role = "", $value = 0)
{
    if ($role == "operator") {
        $role = "manager";
    } else {
        if ($role == "merchanter") {
            $role = "clerker";
        } else {
            if ($role == "agent_operator") {
                $role = "agenter";
            }
        }
    }
    $common = array("1000" => array("key" => 1000, "type" => "订单完成", "content" => "订单完成(订单id:%s)"), "1002" => array("key" => 1002, "type" => "订单取消", "content" => "订单取消(订单id:%s)"), "3000" => array("key" => 3000, "type" => "添加店员", "content" => "添加店员(店员id:%s)，详情：%s"), "3002" => array("key" => 3002, "type" => "删除店员", "content" => "删除店员(店员id:%s)，详情：%s"), "4000" => array("key" => 4000, "type" => "添加配送员", "content" => "添加配送员(配送员id:%s)，详情：%s"), "4002" => array("key" => 4002, "type" => "删除配送员", "content" => "删除配送员(配送员id:%s)，详情：%s"));
    $type_all = array("manager" => array("1001" => array("key" => 1001, "type" => "订单部分退款", "content" => "平台发起订单部分退款(退款id:%s)"), "1004" => array("key" => 1004, "type" => "订单已退款", "content" => "平台将订单设为已退款(订单id:%s)"), "1005" => array("key" => 1005, "type" => "订单发起退款", "content" => "平台发起订单退款(订单id:%s)"), "2000" => array("key" => 2000, "type" => "添加商户", "content" => "平台后台添加商户(商户id:%s)"), "2001" => array("key" => 2001, "type" => "删除商户", "content" => "平台后台删除商户(商户id:%s)"), "2002" => array("key" => 2002, "type" => "商户加入回收站", "content" => "平台后台将商户加入回收站(商户id:%s)"), "2003" => array("key" => 2003, "type" => "商户入驻审核通过", "content" => "商户入驻审核通过(商户id:%s), 备注：%s"), "2004" => array("key" => 2004, "type" => "商户入驻审核不通过", "content" => "商户入驻审核不通过(商户id:%s), 备注：%s"), "2005" => array("key" => 2005, "type" => "平台变动商户账户", "content" => "后台变动商户账户(记录id:%s), 备注：%s"), "2006" => array("key" => 2006, "type" => "撤销商户提现", "content" => "撤销商户提现(记录id:%s), 备注：%s"), "2007" => array("key" => 2007, "type" => "商户提现打款", "content" => "商户提现打款(记录id:%s), 结果:%s"), "2008" => array("key" => 2008, "type" => "商户提现设为已处理", "content" => "商户提现设为已处理(记录id:%s)"), "2010" => array("key" => 2010, "type" => "平台创建商户活动", "content" => "平台创建商户活动，详情：%s"), "2011" => array("key" => 2011, "type" => "更改商户提现账户", "content" => "平台更改商户提现账户(商户id:%s)"), "2012" => array("key" => 2012, "type" => "更改商户营业状态", "content" => "平台更改商户营业状态(商户id:%s)"), "3001" => array("key" => 3001, "type" => "编辑店员", "content" => "平台编辑店员(店员id:%s)"), "4001" => array("key" => 4001, "type" => "编辑配送员", "content" => "平台编辑配送员(配送员id:%s)"), "4003" => array("key" => 4003, "type" => "后台变动配送员账户", "content" => "后台变动配送员账户(记录id:%s)"), "4004" => array("key" => 4004, "type" => "撤销配送员提现", "content" => "撤销配送员提现(记录id:%s), 备注：%s"), "4005" => array("key" => 4005, "type" => "配送员提现打款", "content" => "配送员提现打款(记录id:%s), 结果:%s"), "4006" => array("key" => 4006, "type" => "配送员提现设为已处理", "content" => "配送员提现设为已处理(记录id:%s)"), "4007" => array("key" => 4007, "type" => "配送员加入回收站", "content" => "平台将配送员加入回收站(配送员id:%s)"), "4008" => array("key" => 4008, "type" => "将配送员从回收站中恢复", "content" => "平台将配送员从回收站中恢复(配送员id:%s)"), "5000" => array("key" => 5000, "type" => "添加代理", "content" => "平台后台添加代理(代理id:%s)"), "5001" => array("key" => 5001, "type" => "删除代理", "content" => "平台后台删除代理(代理id:%s)"), "5002" => array("key" => 5002, "type" => "平台变动代理账户", "content" => "平台变动代理账户(记录id:%s), 备注：%s"), "5003" => array("key" => 5003, "type" => "撤销代理提现", "content" => "撤销代理提现(记录id:%s, 备注：%s)"), "5004" => array("key" => 5004, "type" => "代理提现打款", "content" => "代理提现打款(记录id:%s), 结果:%s"), "5005" => array("key" => 5005, "type" => "代理提现设为已处理", "content" => "代理提现设为已处理(记录id:%s)"), "5007" => array("key" => 5007, "type" => "更改代理提现账户", "content" => "平台更改代理提现账户(代理id:%s)"), "6000" => array("key" => 6000, "type" => "顾客加入黑名单", "content" => "顾客加入黑名单(顾客id:%s)"), "6001" => array("key" => 6001, "type" => "删除顾客", "content" => "平台后台删除顾客(uid:%s)"), "6002" => array("key" => 6002, "type" => "平台变动顾客账户", "content" => "平台变动顾客账户(uid:%s), 详情:%s"), "6003" => array("key" => 6003, "type" => "平台编辑顾客", "content" => "平台编辑顾客(uid:%s)"), "6004" => array("key" => 6004, "type" => "平台设置顾客等级", "content" => "平台设置顾客等级(uid:%s), 设置等级id:%s"), "6005" => array("key" => 6005, "type" => "平台设置顾客配送会员卡", "content" => "平台设置顾客配送会员卡(uid:%s), 详情:%s"), "6006" => array("key" => 6006, "type" => "平台设置顾客超级会员", "content" => "平台设置顾客超级会员(uid:%s), 详情:%s"), "6007" => array("key" => 6007, "type" => "顾客移出黑名单", "content" => "顾客移出黑名单(顾客id:%s)")), "agenter" => array("1001" => array("key" => 1001, "type" => "订单部分退款", "content" => "代理发起订单部分退款(退款id:%s)"), "1004" => array("key" => 1004, "type" => "订单已退款", "content" => "代理将订单设为已退款(订单id:%s)"), "1005" => array("key" => 1005, "type" => "订单发起退款", "content" => "代理发起订单退款(订单id:%s)"), "2000" => array("key" => 2000, "type" => "添加商户", "content" => "代理后台添加商户(商户id:%s)"), "2001" => array("key" => 2001, "type" => "删除商户", "content" => "代理后台删除商户(商户id:%s)"), "2002" => array("key" => 2002, "type" => "商户加入回收站", "content" => "代理后台将商户加入回收站(商户id:%s)"), "2003" => array("key" => 2003, "type" => "商户入驻审核通过", "content" => "代理商户入驻审核通过(商户id:%s), 备注：%s"
), "2004" => array("key" => 2004, "type" => "商户入驻审核不通过", "content" => "代理商户入驻审核不通过(商户id:%s), 备注：%s"), "2011" => array("key" => 2011, "type" => "更改商户提现账户", "content" => "代理更改商户提现账户(商户id:%s)"), "2012" => array("key" => 2012, "type" => "更改商户营业状态", "content" => "代理更改商户营业状态(商户id:%s)"), "3001" => array("key" => 3001, "type" => "编辑店员", "content" => "代理编辑店员(店员id:%s)"), "4001" => array("key" => 4001, "type" => "编辑配送员", "content" => "代理编辑配送员(配送员id:%s)"), "4007" => array("key" => 4007, "type" => "配送员加入回收站", "content" => "代理将配送员加入回收站(配送员id:%s)"), "4008" => array("key" => 4008, "type" => "将配送员从回收站中恢复", "content" => "代理将配送员从回收站中恢复(配送员id:%s)"), "5006" => array("key" => 5006, "type" => "代理发起提现申请", "content" => "代理发起提现申请(记录id:%s)"), "5007" => array("key" => 5007, "type" => "更改代理提现账户", "content" => "代理更改提现账户(代理id:%s)")), "clerker" => array("1001" => array("key" => 1001, "type" => "订单部分退款", "content" => "商户发起订单部分退款(退款id:%s)"), "2009" => array("key" => 2009, "type" => "商户发起提现申请", "content" => "商户发起提现申请(记录id:%s)"), "2011" => array("key" => 2011, "type" => "更改商户提现账户", "content" => "商户更改提现账户(商户id:%s)"), "2012" => array("key" => 2012, "type" => "更改商户营业状态", "content" => "商户更改营业状态(商户id:%s)")));
    if (!empty($value)) {
        $type = $common[$value];
        if (empty($type)) {
            if ($role == "founder") {
                $type = $type_all["manager"][$value];
                if (empty($type)) {
                    $type = $type_all["agenter"][$value];
                }
                if (empty($type)) {
                    $type = $type_all["clerker"][$value];
                }
            } else {
                $type = $type_all[$role][$value];
            }
        }
    }
    if (empty($role) || $role == "founder") {
        $types = array_merge($common, $type_all["manager"], $type_all["agenter"], $type_all["clerker"]);
    } else {
        $types = array_merge($common, $type_all[$role]);
    }
    if (empty($value)) {
        return $types;
    }
    return $type;
}
function mlog_fetch_all($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $role = trim($filter["role"]);
    if (!empty($role)) {
        $condition .= " and role = :role";
        $params[":role"] = $role;
    }
    $type = intval($filter["type"]);
    if (!empty($type)) {
        $condition .= " and type = :type";
        $params[":type"] = $type;
    }
    $keyword = trim($filter["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and username like :keyword";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    if (!empty($filter["starttime"]) && !empty($filter["endtime"])) {
        $condition .= " AND addtime > :start AND addtime < :end";
        $params[":start"] = $filter["starttime"];
        $params[":end"] = $filter["endtime"];
    }
    $page = max(1, intval($filter["page"]));
    $psize = intval($filter["psize"]) ? intval($filter["psize"]) : 20;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_operate_log") . $condition, $params);
    $logs = pdo_fetchall("select * from " . tablename("tiny_wmall_operate_log") . $condition . " order by id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($logs)) {
        foreach ($logs as &$val) {
            $log_type = mlog_types($val["role"], $val["type"]);
            $val["type_cn"] = $log_type["type"];
            $val["addtime_cn"] = date("Y-m-d H:i", $val["addtime"]);
        }
    }
    $pager = pagination($total, $page, $psize);
    return array("logs" => $logs, "pager" => $pager);
}

?>
