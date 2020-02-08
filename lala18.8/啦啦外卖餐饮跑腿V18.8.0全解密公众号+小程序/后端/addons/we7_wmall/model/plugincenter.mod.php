<?php

defined('IN_IA') or exit('Access Denied');

function get_plugincenter_plugins($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    $condition = " where 1";
    $params = array();
    if (!isset($filter["uniacid"]) || !empty($filter["uniacid"])) {
        $condition .= " and uniacid = :uniacid";
        $params[":uniacid"] = $_W["uniacid"];
    }
    $status = isset($filter["status"]) ? intval($filter["status"]) : -1;
    if (-1 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    $keyword = trim($filter["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and title like :keyword";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $orderby = "displayorder";
    if (!empty($filter["orderby"])) {
        $orderby = $filter["orderby"];
    }
    $condition .= " order by " . $orderby . " desc";
    $page = max(1, intval($filter["page"]));
    $psize = intval($filter["psize"]) ? intval($filter["psize"]) : 100;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_plugincenter_plugin") . $condition, $params);
    $plugins = pdo_fetchall("select * from " . tablename("tiny_wmall_plugincenter_plugin") . $condition . " LIMIT " . ($page - 1) * $psize . "," . $psize, $params);
    foreach ($plugins as &$plugin) {
        $plugin["data"] = iunserializer($plugin["data"]);
        $plugin["thumb"] = tomedia($plugin["thumb"]);
    }
    $pager = pagination($total, $page, $psize);
    return array("plugins" => $plugins, "total" => $total, "pager" => $pager);
}
function get_plugincenter_package($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    $condition = " where 1";
    $params = array();
    if (!isset($filter["uniacid"]) || !empty($filter["uniacid"])) {
        $condition .= " and uniacid = :uniacid";
        $params[":uniacid"] = $_W["uniacid"];
    }
    $status = isset($filter["status"]) ? intval($filter["status"]) : -1;
    if (-1 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    $keyword = trim($filter["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and title like :keyword";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $condition .= " order by displayorder desc";
    $page = max(1, intval($filter["page"]));
    $psize = intval($filter["psize"]) ? intval($filter["psize"]) : 100;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_plugincenter_package") . $condition, $params);
    $packages = pdo_fetchall("select * from " . tablename("tiny_wmall_plugincenter_package") . $condition . " LIMIT " . ($page - 1) * $psize . "," . $psize, $params);
    $plugins = pdo_fetchall("select id, title, thumb, name from " . tablename("tiny_wmall_plugin") . " where 1", array(), "id");
    foreach ($packages as &$package) {
        $plugin_ids = explode(",", $package["pluginid"]);
        $package["packagename_cn"] = "";
        foreach ($plugin_ids as $id) {
            $package["plugins"][] = $plugins[$id];
            $package["packagename_cn"] .= (string) $plugins[$id]["title"] . ",";
        }
        $package["packagename_cn"] = trim($package["packagename_cn"], ",");
        $package["data"] = iunserializer($package["data"]);
    }
    $pager = pagination($total, $page, $psize);
    return array("packages" => $packages, "total" => $total, "pager" => $pager);
}
function getall_plugincenter_order($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    $condition = " where 1";
    $params = array();
    $uniacid = isset($filter["uniacid"]) ? intval($filter["uniacid"]) : 0;
    if (0 < $uniacid) {
        $condition .= " and a.uniacid = :uniacid";
        $params[":uniacid"] = $uniacid;
    }
    $pluginname = isset($filter["pluginname"]) ? $filter["pluginname"] : "";
    if (!empty($pluginname)) {
        $condition .= " and (a.pluginname = :pluginname or a.pluginname like :pluginnames)";
        $params[":pluginname"] = $pluginname;
        $params[":pluginnames"] = "%" . $pluginname . "%";
    }
    $condition .= " and is_pay = 1";
    if (!empty($filter["starttime"]) && !empty($filter["endtime"])) {
        $condition .= " AND a.addtime > :start AND a.addtime < :end";
        $params[":start"] = $filter["starttime"];
        $params[":end"] = $filter["endtime"];
    }
    $page = max(1, intval($filter["page"]));
    $psize = intval($filter["psize"]) ? intval($filter["psize"]) : 15;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_plugincenter_order") . " as a" . $condition, $params);
    $orders = pdo_fetchall("select a.*, b.name as uniacid_name, b.username from " . tablename("tiny_wmall_plugincenter_order") . " as a left join " . tablename("account_wechats") . " as b on a.uniacid = b.uniacid" . $condition . " order by id desc LIMIT " . ($page - 1) * $psize . "," . $psize, $params);
    $plugins = pdo_fetchall("select id, title, thumb, name from " . tablename("tiny_wmall_plugin") . " where 1", array(), "id");
    foreach ($orders as &$val) {
        $val["data"] = iunserializer($val["data"]);
        $plugin_ids = explode(",", $val["pluginid"]);
        $val["packagename_cn"] = "";
        foreach ($plugin_ids as $id) {
            $val["plugins"][] = $plugins[$id];
            $val["packagename_cn"] .= (string) $plugins[$id]["title"] . ",";
        }
        $val["packagename_cn"] = trim($val["packagename_cn"], ",");
    }
    $pager = pagination($total, $page, $psize);
    return array("orders" => $orders, "total" => $total, "pager" => $pager);
}
function get_plugincenter_package_detail($id, $type = "plugin")
{
    $routers = array("plugin" => "tiny_wmall_plugincenter_plugin", "package" => "tiny_wmall_plugincenter_package");
    $table = $routers[$type];
    $data = pdo_get($table, array("id" => $id));
    if (!empty($data)) {
        $data["data"] = iunserializer($data["data"]);
    }
    return $data;
}
function plugincenter_order_update($id, $type, $extra = array())
{
    $order = pdo_get("tiny_wmall_plugincenter_order", array("id" => $id));
    if (empty($order)) {
        return error(-1, "订单不存在或已删");
    }
    if ($order["is_pay"] == 1) {
        return error(-1, "订单已支付");
    }
    $update = array("is_pay" => 1, "pay_type" => $extra["type"], "final_fee" => $extra["card_fee"], "paytime" => TIMESTAMP);
    pdo_update("tiny_wmall_plugincenter_order", $update, array("id" => $id));
    $order["data"] = iunserializer($order["data"]);
    $buy_meal = $order["data"]["meal"];
    $meal_table = "tiny_wmall_plugincenter_plugin";
    if ($buy_meal["type"] == "package") {
        $meal_table = "tiny_wmall_plugincenter_package";
    }
    pdo_query("update " . tablename($meal_table) . " set sailed = sailed + 1 where id = :id", array(":id" => $buy_meal["id"]));
    $plugins = explode(",", $order["pluginname"]);
    $order["type"] = "pay";
    plugincenter_update_grant_log($plugins, $order);
    set_account_perm($order["uniacid"], $plugins);
    return true;
}
function plugincenter_update_grant_log($plugins, $extra = array())
{
    $uniacid = $extra["uniacid"];
    if (!is_array($plugins)) {
        $plugins = array($plugins);
    }
    foreach ($plugins as $plugin) {
        if ($plugin == "none") {
            continue;
        }
        $log = pdo_get("tiny_wmall_plugincenter_grant_log", array("uniacid" => $uniacid, "pluginname" => $plugin));
        if (empty($log)) {
            $insert = array("uniacid" => $uniacid, "pluginname" => $plugin, "pluginid" => $extra["pluginid"], "order_id" => $extra["order_id"], "month" => $extra["month"], "starttime" => TIMESTAMP, "endtime" => strtotime("+ " . $extra["month"] . " month"), "type" => $extra["type"], "updatetime" => TIMESTAMP);
            if (empty($extra["month"])) {
                $insert["endtime"] = 0;
            }
            pdo_insert("tiny_wmall_plugincenter_grant_log", $insert);
        } else {
            $update = array("type" => $extra["type"], "month" => $extra["month"], "updatetime" => TIMESTAMP);
            if (0 < $extra["month"]) {
                $endtime = max($log["endtime"], TIMESTAMP);
                $update["endtime"] = strtotime("+ " . $extra["month"] . " month", $endtime);
            } else {
                $update["endtime"] = 0;
            }
            pdo_update("tiny_wmall_plugincenter_grant_log", $update, array("uniacid" => $uniacid, "pluginname" => $plugin));
        }
    }
    return true;
}
function set_account_perm($uniacid, $plugins)
{
    $perm = pdo_get("tiny_wmall_perm_account", array("uniacid" => $uniacid));
    if (empty($perm)) {
        $insert = array("uniacid" => $uniacid, "plugins" => iserializer($plugins));
        pdo_insert("tiny_wmall_perm_account", $insert);
    } else {
        $has_plugins = iunserializer($perm["plugins"]);
        if (empty($has_plugins)) {
            $has_plugins = array();
        }
        $plugins = array_merge($has_plugins, $plugins);
        $plugins = array_unique($plugins);
        $update = array("plugins" => iserializer($plugins));
        pdo_update("tiny_wmall_perm_account", $update, array("id" => $perm["id"]));
    }
}
function account_perm_cron()
{
    $plugins_available = pdo_fetchall("select uniacid, pluginname from " . tablename("tiny_wmall_plugincenter_grant_log") . " where endtime > :endtime or endtime = 0", array(":endtime" => TIMESTAMP));
    if (empty($plugins_available)) {
        return true;
    }
    $available_account = array();
    foreach ($plugins_available as $val) {
        $available_account[$val["uniacid"]][] = $val["pluginname"];
    }
    foreach ($available_account as $key => $val) {
        pdo_update("tiny_wmall_perm_account", array("plugins" => iserializer($val)), array("uniacid" => $key));
    }
    return true;
}

?>
