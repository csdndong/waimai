<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("plugin");
mload()->model("cloud");
mload()->model("plugincenter");
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "buy";
if ($op == "buy") {
    $_W["page"]["title"] = "应用购买";
    $filter = array("status" => 1, "uniacid" => 0);
    $packages = get_plugincenter_package($filter);
    $packages = $packages["packages"];
    $sort = trim($_GPC["sort"]);
    if (!empty($sort)) {
        $filter["orderby"] = $sort;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $filter["keyword"] = $keyword;
    }
    $plugins = get_plugincenter_plugins($filter);
    $plugins = $plugins["plugins"];
    $slides = pdo_fetchall("select * from " . tablename("tiny_wmall_plugincenter_slide") . " where status = 1 order by id desc");
} else {
    if ($op == "detail") {
        $_W["page"]["title"] = "应用详情";
        $type = trim($_GPC["type"]);
        $id = intval($_GPC["id"]);
        $detail = get_plugincenter_package_detail($id, $type);
        $config = get_global_config("plugincenter");
        $perms = get_account_perm("plugins");
        $log = false;
        if (empty($perms)) {
            $log = true;
        } else {
            if ($type == "plugin") {
                if (in_array($detail["pluginname"], $perms)) {
                    $log = true;
                }
            } else {
                $names = $detail["pluginname"];
                $names = explode(",", $names);
                $is_exist = array_intersect($names, $perms);
                if (!empty($is_exist)) {
                    $log = true;
                }
            }
        }
    } else {
        if ($op == "submit") {
            $_W["page"]["title"] = "购买中";
            if ($_W["ispost"]) {
                $id = intval($_GPC["id"]);
                $type = trim($_GPC["type"]);
                $detail = get_plugincenter_package_detail($id, $type);
                if (empty($detail)) {
                    imessage(error(-1, "购买插件套餐不存在"), "", "ajax");
                }
                $index = trim($_GPC["index"]);
                $meal = $detail["data"]["meal"][$index];
                $insert = array("uniacid" => $_W["uniacid"], "pluginname" => $detail["pluginname"], "pluginid" => $detail["pluginid"], "month" => $meal["month"], "final_fee" => $meal["price"], "order_sn" => date("YmdHis") . random(6, true), "addtime" => TIMESTAMP, "pay_type" => "alipay", "data" => iserializer(array("meal" => array("id" => $id, "type" => $type, "title" => $detail["title"], "thumb" => $detail["thumb"]))));
                pdo_insert("tiny_wmall_plugincenter_order", $insert);
                $order_id = pdo_insertid();
                mload()->model("payment");
                $params = pc_pay_prep(array("id" => $order_id, "order_type" => "plugincenter"));
                $alipay_config = get_global_config("plugincenter.pay_type.alipay");
                $alipay_config["plugin"] = "plugincenter";
                $ret = alipay_pc_build($params, $alipay_config);
                if (is_error($ret)) {
                    imessage(error(-1, "支付宝支付参数有错"), "", "ajax");
                }
                imessage(error(0, $ret), "", "ajax");
            }
        }
    }
}
include itemplate('plugin/plugincenter');

?>