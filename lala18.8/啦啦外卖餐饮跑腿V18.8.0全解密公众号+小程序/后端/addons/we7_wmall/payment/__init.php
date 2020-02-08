<?php

defined("IN_IA") or exit("Access Denied");
function member_wxapp2openid($openid_wxapp = "")
{
    global $_W;
    if (empty($openid_wxapp)) {
        $openid_wxapp = $_W["openid_wxapp"];
    }
    $openid = pdo_fetchcolumn("select openid from " . tablename("tiny_wmall_members") . " where uniacid = :uniacid and openid_wxapp = :openid_wxapp", array(":uniacid" => $_W["uniacid"], ":openid_wxapp" => $openid_wxapp));
    return $openid;
}
//mai=wo0jie  mi  de yuanma #wo cao 5nilao lao
function get_system_config($key = "", $uniacid = -1)
{
    global $_W;
    if ($uniacid == -1) {
        $uniacid = intval($_W["uniacid"]);
    }
    $config = pdo_get("tiny_wmall_config", array("uniacid" => $uniacid), array("sysset", "pluginset", "id"));
    if (empty($config["id"])) {
        $init_config = array("uniacid" => $_W["uniacid"]);
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
        $sysset_agent = get_agent_system_config();
        if (!empty($sysset_agent)) {
            $sysset = multimerge($sysset, $sysset_agent);
        }
        $_W["is_agentconfig"] = $_W["agentid"];
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
function get_agent_system_config($key = "")
{
    global $_W;
    $config = pdo_get("tiny_wmall_agent", array("uniacid" => $_W["uniacid"], "id" => $_W["agentid"]), array("sysset", "id"));
    if (empty($config)) {
        return array();
    }
    $sysset = iunserializer($config["sysset"]);
    if (!is_array($sysset)) {
        $sysset = array();
    }
    if (empty($sysset["app"]) || empty($sysset["app"]["deliveryer"]["push_tags"])) {
        $sysset["app"]["deliveryer"]["push_tags"] = array("all" => random(8), "working" => random(8), "rest" => random(8));
        if (empty($sysset["app"]["deliveryer"]["push_tags"]["waimai"])) {
            $sysset["app"]["deliveryer"]["push_tags"]["waimai"] = random(8);
        }
        if (empty($sysset["app"]["deliveryer"]["push_tags"]["paotui"])) {
            $sysset["app"]["deliveryer"]["push_tags"]["paotui"] = random(8);
        }
        pdo_update("tiny_wmall_agent", array("sysset" => iserializer($sysset)), array("uniacid" => $_W["uniacid"], "id" => $_W["agentid"]));
    }
    if (empty($key)) {
        return $sysset;
    }
    $keys = explode(".", $key);
    $counts = count($key);
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
    if (in_array($key, array("pintuan", "kanjia", "seckill", "tongcheng"))) {
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
        if (!empty($keys[1])) {
            return $config_plugin[$keys[1]];
        }
        return $config_plugin;
    }
}
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
function get_global_config_1($key = "")
{
    $result = get_system_config($key, 0);
    return $result;
}
function islog($type, $title, $params, $message)
{
    global $_W;
    if (empty($type)) {
        return error(-1, "错误类型不能为空");
    }
    if (empty($message)) {
        return error(-1, "错误信息不能为空");
    }
    $data = array("uniacid" => $_W["uniacid"], "type" => $type, "title" => $title, "params" => iserializer($params), "message" => iserializer($message), "addtime" => TIMESTAMP);
    pdo_insert("tiny_wmall_system_log", $data);
    return true;
}

?>