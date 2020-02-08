<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if (!$_config_plugin["status"]) {
    imessage(error(-1, "平台暂未开启跑腿功能"), "", "ajax");
}
if ($_W["is_agent"] && $_W["agentid"] == -1) {
    imessage(error(-2, "您所在的区域暂未开启跑腿功能,建议您手动搜索地址或切换到此前常用的地址再试试"), "", "ajax");
}
$_W["_share"] = get_errander_share();
if ($op == "index") {
    $ochannel = trim($_W["ochannel"]);
    $home_setting = "diy";
    if ($ochannel == "wap") {
        $home_setting = get_plugin_config("errander.page.home");
    }
    if ($home_setting != "diy") {
        $categorys = pdo_fetchall("select id, name, thumb from " . tablename("tiny_wmall_errander_page") . " where uniacid = :uniacid and agentid = :agentid and type = :type order by displayorder desc", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":type" => "scene"));
        if (!empty($categorys)) {
            foreach ($categorys as &$val) {
                $val["thumb"] = empty($val["thumb"]) ? "" : tomedia($val["thumb"]);
            }
        }
        $orders = pdo_fetchall("select a.anonymous_username,a.goods_name,a.order_cid, b.name, b.thumb from " . tablename("tiny_wmall_errander_order") . " as a left join " . tablename("tiny_wmall_errander_page") . " as b on a.order_cid = b.id where a.uniacid = :uniacid and a.agentid = :agentid order by a.id desc limit 5", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]));
        if (!empty($orders)) {
            foreach ($orders as &$val) {
                $val["thumb"] = empty($val["thumb"]) ? "" : tomedia($val["thumb"]);
            }
        }
        $delivery_num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_deliveryer") . " where uniacid = :uniacid and agentid = :agentid and status = 1 and is_errander = 1", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]));
        $deliveryers = deliveryer_fetchall(0, array("work_status" => 1, "order_type" => "is_errander"));
        $result = array("home_setting" => $home_setting, "config" => $_config_plugin, "categorys" => $categorys, "orders" => $orders, "delivery_num" => $delivery_num, "deliveryers" => array_values($deliveryers));
        imessage(error(0, $result), "", "ajax");
        return 1;
    } else {
        $homepage = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_errander_page") . " WHERE uniacid = :uniacid and agentid = :agentid and type = :type", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":type" => "home"));
        if (!empty($homepage)) {
            $homepage["data"] = json_decode(base64_decode($homepage["data"]), true);
            if (!empty($homepage["data"]["items"])) {
                foreach ($homepage["data"]["items"] as &$item) {
                    if (!empty($item["picture"])) {
                        foreach ($item["picture"] as &$pic) {
                            $pic["imgurl"] = tomedia($pic["imgurl"]);
                        }
                    }
                }
            }
            $result = array("home_setting" => $home_setting, "homepage" => $homepage);
            if ($ochannel == "wxapp") {
                $result = $homepage;
            }
            imessage(error(0, $result), "", "ajax");
        }
        imessage(error(-1, "平台未设置跑腿首页,请到后台设置跑腿首页并保存"), "", "ajax");
    }
}

?>