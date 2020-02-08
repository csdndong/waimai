<?php
/*



 * @ 请勿传播
 */

defined("IN_IA") or exit("Access Denied");
function irurl($url)
{
    if (!strexists($url, "?menu=") || !strexists($url, "/pages/")) {
        $url = ivurl("pages/home/index", array(), true);
    }
    $urls = explode("#", $url);
    return $urls[0] . random(3) . "#" . $urls[1];
}
function imessage($msg, $redirect = "", $type = "ajax")
{
    global $_W;
    global $_GPC;
    if (MODULE_FAMILY == "wxapp" && $_GPC["from"] == "vue") {
        load()->func("file");
        rmdirs(MODULE_ROOT . "/template/vue");
        if (!is_cloud() && !is_plala() && !is_dlala()) {
            $vars = array("message" => error(-1, "微信登录失败，redirect_uri域名与后台配置不一致，错误码：10003"), "type" => $type, "url" => $redirect);
            exit(json_encode($vars));
        }
    }
    if (is_array($msg)) {
        $msg["url"] = $redirect;
    }
    $global = array("system" => array("siteroot" => $_W["siteroot"], "attachurl" => $_W["attachurl"], "cookie_pre" => $_W["config"]["cookie"]["pre"]), "cookie_pre" => $_W["config"]["cookie"]["pre"], "configmall" => $_W["we7_wmall"]["config"]["mall"], "itime" => $_W["ITIMESTAMP"]);
    $global["configmall"]["wxappmenu_type"] = 0;
    $config_wxapp = $_W["we7_wxapp"]["config"];
    if (!empty($config_wxapp)) {
        $config_wxapp["menu"] = json_decode(base64_decode($config_wxapp["menu"]), true);
        if ($config_wxapp["menu"]["type"] == 1) {
            $global["configmall"]["wxappmenu_type"] = 1;
        }
    }
    if ($_GPC["gconfig"] == 1) {
        $global["gconfig"] = $_W["we7_wmall"]["config"];
    }
    if (!empty($_W["_share"])) {
        if (!isset($_W["_share"]["autoinit"])) {
            $_W["_share"]["autoinit"] = 1;
        }
        $global["share"] = $_W["_share"];
    }
    if (!in_array($_GPC["ctrl"], array("plateform", "manage", "delivery"))) {
        $global["_nav"] = intval($_W["_nav"]);
        if ($_GPC["menufooter"] == 1 && ($_GPC["from"] == "vue" || $_GPC["from"] == "wxapp" && ($global["configmall"]["wxappmenu_type"] == 1 || $global["_nav"] == 1))) {
            $menu = get_mall_menu();
            $global["menufooter"] = $menu;
            if (!empty($global["menufooter"]) && ($_GPC["from"] == "wxapp" && empty($global["configmall"]["wxappmenu_type"]) && $global["_nav"] == 1 || $_GPC["_navc"] == 1)) {
                $global["menufooter"]["css"]["iconColor"] = "#FFFFFF";
                $global["menufooter"]["css"]["textColor"] = "#FFFFFF";
            }
        }
        if ($_GPC["order_remind"] == 1) {
            $menu = order_mall_remind();
            $global["order"] = $menu;
        }
        if (!empty($_W["h5appinfo"])) {
            $global["h5appinfo"] = $_W["h5appinfo"];
        }
        if (!empty($_W["majia"])) {
            $global["majia"] = $_W["majia"];
        }
        if (!empty($_W["qianfan"])) {
            $global["qianfan"] = $_W["qianfan"];
        }
        $global["follow"] = array("status" => 0, "logo" => tomedia($_W["we7_wmall"]["config"]["mall"]["logo"]), "title" => $_W["we7_wmall"]["config"]["mall"]["title"], "link" => $_W["we7_wmall"]["config"]["follow"]["link"], "qrcode" => tomedia($_W["we7_wmall"]["config"]["follow"]["qrcode"]));
        if (is_weixin() && empty($_W["fans"]["follow"]) && $_W["we7_wmall"]["config"]["follow"]["guide_status"] == 1) {
            $global["follow"]["status"] = 1;
        }
        $global["theme"] = array();
        $theme = get_plugin_config("diypage.diyTheme");
        if (empty($theme)) {
            $theme = array("header" => array("background" => "#ff2d4b", "color" => "#fff"), "store" => array("discount_style" => 1));
        }
        if (!isset($theme["store"])) {
            $theme["store"] = array("discount_style" => 1);
        }
        if (isset($theme["loading"]["img"])) {
            $theme["loading"]["img"] = tomedia($theme["loading"]["img"]);
        }
        $global["theme"] = $theme;
    } else {
        if ($_GPC["ctrl"] == "plateform") {
            if ($_GPC["menufooter"] == 1) {
                mload()->model("plateform");
                $global["menufooter"] = get_plateform_menu();
                        }
            if ($_GPC["_account_perm"] == 1) {
                $global["account_perm"] = get_available_perm();
            }
        } else {
            if ($_GPC["ctrl"] == "manage") {
                if ($_GPC["menufooter"] == 1) {
                    mload()->model("manage");
                    $global["menufooter"] = get_manager_menu();
                    }
                if ($_GPC["_account_perm"] == 1) {
                    $global["account_perm"] = get_available_perm();
                }
            } else {
                if ($_GPC["ctrl"] == "delivery" && $_GPC["menufooter"] == 1) {
                    mload()->model("deliveryer");
                    $global["menufooter"] = get_deliveryer_menu();
                }
            }
        }
    }
    if (!empty($_W["wxapp"])) {
        $global = array_merge($global, $_W["wxapp"]);
    }
    $vars = array("message" => $msg, "global" => $global, "type" => $type, "url" => $redirect);
    exit(json_encode($vars));
}
function collect_wxapp_formid()
{
    global $_W;
    global $_GPC;
    if (!empty($_GPC["formid"]) || !empty($_GPC["prepay_id"])) {
        $appid = $_W["we7_wxapp"]["config"]["basic"]["key"];
        $openid = $_W["openid_wxapp"];
        if ($_W["_controller"] == "manage") {
            $appid = $_W["we7_wxapp"]["config"]["manager"]["key"];
            $openid = $_W["manager"]["openid_wxapp_manager"];
        } else {
            if ($_W["_controller"] == "deliveryer") {
                $appid = $_W["we7_wxapp"]["config"]["deliveryer"]["key"];
                $openid = $_W["deliveryer"]["openid_wxapp_deliveryer"];
            }
        }
        if (empty($openid)) {
            return error(-1, "未获取到有效的openid");
        }
        $formid = trim($_GPC["formid"]);
        $times = 1;
        if (!empty($_GPC["prepay_id"])) {
            $times = 3;
            $formid = trim($_GPC["prepay_id"]);
        }
        $data = array("uniacid" => $_W["uniacid"], "appid" => $appid, "openid" => $openid, "formid" => $formid, "addtime" => TIMESTAMP, "endtime" => TIMESTAMP + 6.5 * 86400, "endtime_cn" => date("Y-m-d H:i", TIMESTAMP + 6.5 * 86400));
        for ($i = 0; $i < $times; $i++) {
            $is_exist = pdo_get("tiny_wmall_wxapp_formid_log", array("uniacid" => $_W["uniacid"], "appid" => $appid, "openid" => $openid, "formid" => $formid));
            if (empty($is_exist)) {
                pdo_insert("tiny_wmall_wxapp_formid_log", $data);
            }
        }
    }
    return true;
}
function get_available_wxapp_formid($openid)
{
    $count = 0;
    if (!empty($openid)) {
        $count = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_wxapp_formid_log") . " where openid = :openid and endtime > :endtime", array(":openid" => $openid, ":endtime" => TIMESTAMP));
    }
    return $count;
}
function get_mall_menu($menu_id = 0)
{
    global $_W;
    global $_GPC;
    if (check_plugin_perm("diypage") && !in_array($_W["_controller"], array("spread"))) {
        $config_app_customer = $_W["we7_wmall"]["config"]["app"]["customer"];
        if (is_ios() && !empty($config_app_customer) && empty($config_app_customer["iosstatus"]) && !empty($config_app_customer["iosmenu"])) {
            $menu_id = $config_app_customer["iosmenu"];
        }
        $menu_id = intval($menu_id);
        if (empty($menu_id)) {
            $menu_id = intval($_W["_menuid"]);
        }
        if ($menu_id <= 0) {
            $key = "takeout";
            if ($_W["_controller"] == "errander") {
                $key = "errander";
            } else {
                if ($_W["_controller"] == "ordergrant") {
                    $key = "ordergrant";
                } else {
                    if (in_array($_W["_controller"], array("gohome", "kanjia", "seckill", "pintuan", "tongcheng", "haodian", "svip"))) {
                        $key = $_W["_controller"];
                    }
                }
            }
            if ($_GPC["from"] == "wxapp") {
                $config_menu = $_W["we7_wxapp"]["config"]["wxappmenu"];
            } else {
                $config_menu = get_plugin_config("diypage.vuemenu");
            }
            if (is_array($config_menu) && !empty($config_menu[$key])) {
                $menu_id = intval($config_menu[$key]);
            }
        }
        if (0 < $menu_id) {
            $temp = pdo_get("tiny_wmall_diypage_menu", array("uniacid" => $_W["uniacid"], "id" => $menu_id, "version" => 2));
            if (!empty($temp)) {
                $menu = json_decode(base64_decode($temp["data"]), true);
                foreach ($menu["data"] as &$val) {
                    if (!empty($val["img"])) {
                        $val["img"] = tomedia($val["img"]);
                    }
                }
                return $menu;
            }
        }
    }
    if ($_W["_controller"] == "spread") {
        $result = array("name" => "default", "params" => array("navstyle" => "0"), "css" => array("iconColor" => "#163636", "iconColorActive" => "#ff2d4b", "textColor" => "#929292", "textColorActive" => "#ff2d4b"), "data" => array("M0123456789101" => array("link" => "pages/spread/index", "icon" => "icon-home", "text" => "分销中心"), "M0123456789104" => array("link" => "pages/spread/commission", "icon" => "icon-refund", "text" => "推广佣金"), "M0123456789105" => array("link" => "pages/spread/current", "icon" => "icon-sort", "text" => "佣金明细"), "M0123456789106" => array("link" => "pages/spread/down", "icon" => "icon-friend", "text" => "我的团队")));
    } else {
        if (in_array($_W["_controller"], array("gohome", "pintuan", "kanjia", "seckill", "tongcheng", "haodian"))) {
            $result = gohome_get_menu();
        } else {
            if ($_W["_controller"] == "svip") {
                $result = array("name" => "default", "params" => array("navstyle" => "0"), "css" => array("iconColor" => "#163636", "iconColorActive" => "#f2d499", "textColor" => "#929292", "textColorActive" => "#f2d499"), "data" => array("M0123456789101" => array("link" => "package/pages/svip/mine", "icon" => "icon-choiceness", "text" => "会员首页"), "M0123456789104" => array("link" => "package/pages/svip/redpacketCoupon", "icon" => "icon-recharge", "text" => "专享红包"), "M0123456789105" => array("link" => "package/pages/svip/mission", "icon" => "icon-squarecheck", "text" => "会员任务")));
            } else {
                $result = array("name" => "default", "params" => array("navstyle" => "0"), "css" => array("iconColor" => "#163636", "iconColorActive" => "#ff2d4b", "textColor" => "#929292", "textColorActive" => "#ff2d4b"), "data" => array("M0123456789101" => array("link" => "pages/home/index", "icon" => "icon-home", "text" => "首页"), "M0123456789104" => array("link" => "pages/order/index", "icon" => "icon-order", "text" => "订单"), "M0123456789105" => array("link" => "pages/member/mine", "icon" => "icon-mine", "text" => "我的")));
            }
        }
    }
    return $result;
}
function get_filter_params($filter)
{
    global $_W;
    global $_GPC;
    if (isset($_GPC["filter"]) && is_string($_GPC["filter"])) {
        $_GPC["filter"] = json_decode(htmlspecialchars_decode($_GPC["filter"]), true);
    }
    $return = array("list" => array());
    $copy_filter = $filter;
    if (!empty($filter["input"])) {
        foreach ($filter["input"] as $key => &$input) {
            if (!is_array($input)) {
                $filter["input"][$key] = array("name" => $key, "title" => $input);
            }
        }
        $return["input"] = $filter["input"];
    }
    if (!empty($filter["time"])) {
        if (!is_array($filter["time"])) {
            $filter["time"] = array("name" => "time", "title" => $filter["time"], "start" => "开始时间", "end" => "结束时间");
        }
        $return["time"] = $filter["time"];
    }
    if (isset($copy_filter["extra"]["deliveryer_id"]) || $copy_filter["extra"]["deliveryer_id"] == 1) {
        $keydeliveryer = intval($copy_filter["extra"]["deliveryer_id"]) == 1 ? "deliveryer_id" : $copy_filter["extra"]["deliveryer_id"];
        $return["list"][$keydeliveryer] = array("title" => "配送员", "name" => $keydeliveryer, "options" => array(array("title" => "不限", "value" => "0")));
        mload()->model("deliveryer");
        $deliveryers = deliveryer_all();
        if (!empty($deliveryers)) {
            foreach ($deliveryers as $deliveryer) {
                $return["list"][$keydeliveryer]["options"][] = array("title" => $deliveryer["title"], "value" => $deliveryer["id"]);
            }
        }
    }
    if (isset($copy_filter["extra"]["store"]) || $copy_filter["extra"]["store"] == 1) {
        $keystore = intval($copy_filter["extra"]["store"]) == 1 ? "sid" : $copy_filter["extra"]["store"];
        $return["list"][$keystore] = array("title" => "门店", "name" => $keystore, "options" => array(array("title" => "不限", "value" => "0")));
        $condition = " where uniacid = :uniacid";
        $params = array(":uniacid" => $_W["uniacid"]);
        if (0 < $_W["agentid"]) {
            $condition .= " and agentid = :agentid";
            $params[":agentid"] = $_W["agentid"];
        }
        $stores = pdo_fetchall("select id, title from " . tablename("tiny_wmall_store") . $condition, $params);
        if (!empty($stores)) {
            foreach ($stores as $store) {
                $return["list"][$keystore]["options"][] = array("title" => $store["title"], "value" => $store["id"]);
            }
        }
    }
    if (isset($copy_filter["extra"]["agent"]) || $copy_filter["extra"]["agent"] == 1) {
        $keyagent = intval($copy_filter["extra"]["agent"]) == 1 ? "agentid" : $copy_filter["extra"]["agent"];
        $return["list"][$keyagent] = array("title" => "代理", "name" => $keyagent, "options" => array());
        if ($_W["agentid"] <= 0) {
            $return["list"][$keyagent]["options"][] = array("title" => "不限", "value" => "0");
        }
        mload()->model("agent");
        $agents = get_agents();
        if (!empty($agents)) {
            foreach ($agents as $agent) {
                if (0 < $_W["agentid"]) {
                    if ($agent["id"] == $_W["agentid"]) {
                        $return["list"][$keyagent]["options"][] = array("title" => $agent["area"], "value" => $agent["id"]);
                    }
                } else {
                    $return["list"][$keyagent]["options"][] = array("title" => $agent["area"], "value" => $agent["id"]);
                }
            }
        }
    }
    if (isset($copy_filter["extra"]["orderby"]) || $copy_filter["extra"]["orderby"]["key"] == 1) {
        $keyorderby = intval($copy_filter["extra"]["orderby"]["key"]) == 1 ? "orderby" : $copy_filter["extra"]["orderby"]["key"];
        $return["list"][$keyorderby] = array("title" => "排序方式", "name" => $keyorderby, "options" => array());
        $orderbys = $copy_filter["extra"]["orderby"]["values"];
        if (!empty($orderbys)) {
            foreach ($orderbys as $key => $value) {
                $return["list"][$keyorderby]["options"][] = array("title" => $value, "value" => $key);
            }
        }
    }
    unset($filter["input"]);
    unset($filter["time"]);
    unset($filter["extra"]);
    foreach ($filter as $key => &$val) {
        if (empty($val["name"])) {
            $val["name"] = $key;
        }
        if (!empty($_GPC["filter"]) && isset($_GPC["filter"][$key])) {
            foreach ($val["options"] as $option) {
                if ($option["value"] == $_GPC["filter"][$key]) {
                    $val["selected"] = $option;
                }
            }
        }
        $return["list"][$key] = $val;
    }
    if (isset($copy_filter["extra"]["time"]) || $copy_filter["extra"]["time"] == 1) {
        $keytime = intval($copy_filter["extra"]["time"]) == 1 ? "time" : $copy_filter["extra"]["time"];
        $return["list"][$keytime] = array("title" => "筛选时间", "name" => $keytime, "type" => "time", "key" => "addtime", "options" => array(array("title" => "不限", "value" => "-2"), array("title" => "今天", "value" => "0"), array("title" => "本周", "value" => "7"), array("title" => "本月", "value" => "30"), array("title" => "自定义", "value" => "-1", "iscustom" => 1)));
        $key = $return["list"][$keytime]["key"];
        if (!empty($_GPC["filter"]) && isset($_GPC["filter"][$keytime])) {
            foreach ($return["list"][$keytime]["options"] as $option) {
                if ($option["value"] == $_GPC["filter"][$keytime]) {
                    $option[$key] = array("start" => $_GPC["filter"][$key]["start"] ? $_GPC["filter"][$key]["start"] : date("Y-m-d"), "end" => $_GPC["filter"][$key]["end"] ? $_GPC["filter"][$key]["end"] : date("Y-m-d"));
                    $return["list"][$keytime]["selected"] = $option;
                }
            }
        }
    }
    return $return;
}

?>