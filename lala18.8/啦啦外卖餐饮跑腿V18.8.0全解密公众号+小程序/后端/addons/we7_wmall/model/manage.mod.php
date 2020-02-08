<?php

defined("IN_IA") or exit("Access Denied");

function icheckmanage()
{
    global $_W;
    global $_GPC;
    $_W["manager"] = array();
    if (defined("IN_WXAPP") || defined("IN_VUE")) {
            $token = trim($_GPC["token"]);
            if (!empty($token)) {
                $clerk = pdo_get("tiny_wmall_clerk", array("uniacid" => $_W["uniacid"], "token" => $token));
            if (!empty($clerk) && empty($clerk["openid_wxapp_manager"])) {
                    $oauth = pdo_get("tiny_wmall_oauth_fans", array("openid" => $token), array("oauth_openid"));
                    if (!empty($oauth["oauth_openid"])) {
                        pdo_update("tiny_wmall_clerk", array("openid_wxapp_manager" => $oauth["oauth_openid"]), array("uniacid" => $_W["uniacid"], "id" => $clerk["id"]));
                        $clerk["openid_wxapp_manager"] = $oauth["oauth_openid"];
                    }
                }
            }
    } else {
        if (is_weixin() && !defined("IN_WXAPP") && !empty($_W["openid"])) {
            $clerk = pdo_get("tiny_wmall_clerk", array("uniacid" => $_W["uniacid"], "openid" => $_W["openid"]));
        }
    }
    if (!empty($clerk)) {
        if (empty($clerk["openid_wxapp"]) && !empty($clerk["openid"])) {
            $openid_wxapp = member_openid2wxapp($clerk["openid"]);
            if (!empty($openid_wxapp)) {
                $clerk["openid_wxapp"] = $openid_wxapp;
                pdo_update("tiny_wmall_clerk", array("openid_wxapp" => $openid_wxapp), array("id" => $clerk["id"]));
            }
        }
        if (empty($clerk["openid"]) && !empty($clerk["openid_wxapp"])) {
            $openid = member_wxapp2openid($clerk["openid_wxapp"]);
            if (!empty($openid)) {
                $clerk["openid"] = $openid;
                pdo_update("tiny_wmall_clerk", array("openid" => $openid), array("id" => $clerk["id"]));
            }
        }
        $_W["manager"] = $clerk;
        $_W["openid"] = $clerk["openid"];
        $_W["openid_wxapp"] = $clerk["openid_wxapp"];
    }
    if (empty($_W["manager"])) {
        $key = "we7_wmall_manager_session_" . $_W["uniacid"];
        if (isset($_GPC[$key])) {
            $session = json_decode(base64_decode($_GPC[$key]), true);
            if (is_array($session)) {
                $clerk = pdo_get("tiny_wmall_clerk", array("uniacid" => $_W["uniacid"], "id" => $session["id"]));
                if (is_array($clerk) && $session["hash"] == $clerk["password"]) {
                    $_W["manager"] = $clerk;
                } else {
                    isetcookie($key, false, -100);
                }
            } else {
                isetcookie($key, false, -100);
            }
        }
    }
    if (empty($_W["openid"])) {
        $_W["openid"] = $_W["manager"]["openid"];
    }
    if (!empty($_W["manager"])) {
        return true;
    }
    if (defined("IN_WXAPP") || defined("IN_VUE")) {
        imessage(error(41009, "请先登录"), "", "ajax");
    } else {
        if ($_W["ispost"]) {
            imessage(error(-1, "请先登录"), imurl("manage/auth/login", array("force" => 1)), "ajax");
        }
        header("location: " . imurl("manage/auth/login", array("force" => 1)), true);
        exit;
    }
}
function store_finance_stat($sid, $filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($sid)) {
        return false;
    }
    $condition = " WHERE uniacid = :uniacid AND sid = :sid";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
    $days = isset($_GPC["days"]) ? intval($_GPC["days"]) : 0;
    if ($days == -1) {
        $starttime = str_replace("-", "", trim($filter["start"]));
        $endtime = str_replace("-", "", trim($filter["end"]));
        $condition .= " and stat_day >= :start_day and stat_day <= :end_day";
        $params[":start_day"] = $starttime;
        $params[":end_day"] = $endtime;
    } else {
        $todaytime = strtotime(date("Y-m-d"));
        $starttime = date("Ymd", strtotime("-" . $days . " days", $todaytime));
        $endtime = date("Ymd", $todaytime + 86399);
        $condition .= " and stat_day >= :stat_day";
        $params[":stat_day"] = $starttime;
    }
    $stat = array();
    $stat["time"] = array("start" => $starttime, "end" => $endtime);
    $stat["total_fee"] = floatval(pdo_fetchcolumn("select round(sum(final_fee), 2) from " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1", $params));
    $stat["store_final_fee"] = floatval(pdo_fetchcolumn("select round(sum(store_final_fee), 2) from " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1", $params));
    $stat["total_success_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1", $params);
    $stat["total_cancel_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . $condition . " and status = 6", $params);
    return $stat;
}
function get_manager_menu()
{
    global $_W;
    $menu = get_plugin_config("managerApp.menu");
    if (empty($menu)) {
        $menu = array("name" => "default", "params" => array("navstyle" => "0"), "css" => array("iconColor" => "#666", "iconColorActive" => "#33aafc", "textColor" => "#666", "textColorActive" => "#33aafc"), "data" => array("M0123456789101" => array("link" => "/pages/order/index", "icon" => "icon-order", "text" => "外卖"), "M0123456789102" => array("link" => "/pages/order/tangshi/index", "icon" => "icon-shop", "text" => "店内"), "M0123456789103" => array("link" => "/pages/shop/home", "icon" => "icon-apps", "text" => "运营"), "M0123456789104" => array("link" => "/pages/shop/setting", "icon" => "icon-settings", "text" => "设置")));
    } else {
        $menu = json_decode(base64_decode($menu), true);
        foreach ($menu["data"] as &$val) {
            if (!empty($val["img"])) {
                $val["img"] = tomedia($val["img"]);
            }
        }
    }
    return $menu;
}

?>