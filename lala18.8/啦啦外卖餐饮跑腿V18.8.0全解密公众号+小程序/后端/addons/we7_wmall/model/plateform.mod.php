<?php
defined("IN_IA") or exit("Access Denied");
function plateform_login($params)
{
    global $_W;
    $username = trim($params["username"]);
    if (empty($username)) {
        return error(-1, "用户名不能为空");
    }
    $password = trim($params["password"]);
    if (empty($password)) {
        return error(-1, "密码不能为空");
    }
    $type = "plateform";
    $types = explode(":", $username);
    if (count($types) == 2) {
        list($type, $username) = $types;
    }
    if ($type == "plateform") {
        $record = pdo_get("users", array("username" => $username));
        if (empty($record)) {
            return error(-1, "用户名不存在");
        }
        load()->model("user");
        $password = user_hash($password, $record["salt"]);
        if ($password != $record["password"]) {
            return error(-1, "密码错误");
        }
        $role = uni_permission($record["uid"], $_W["uniacid"]);
        if (empty($role)) {
            return error(-1, "您没有管理此公众号的权限");
        }
    } else {
        $record = pdo_get("tiny_wmall_agent", array("uniacid" => $_W["uniacid"], "mobile" => $username));
        if (empty($record)) {
            return error(-1, "用户名不存在");
        }
        $password = md5(md5($record["salt"] . $password) . $record["salt"]);
        if ($password != $record["password"]) {
            return error(-1, "密码错误");
        }
        if (!$record["status"]) {
            return error(-1, "您的账号正在审核或是已经被系统禁止，请联系网站管理员解决！");
        }
    }
    $update = array();
    if (empty($record["token"])) {
        $record["token"] = random(20);
        $update["token"] = $record["token"];
    }
    if (0 && !empty($params["registration_id"]) && $record["registration_id"] != $params["registration_id"]) {
        $update["registration_id"] = $params["registration_id"];
    }
    if (!empty($update)) {
        if ($type == "plateform") {
            pdo_update("users", $update, array("uid" => $record["uid"]));
        } else {
            pdo_update("tiny_wmall_agent", $update, array("id" => $record["id"]));
        }
    }
    $record["token"] = (string) $type . ":" . $record["token"];
    $record["usertype"] = $type;
    $record["jpush_relation"] = plateform_push_token($record);
    return $record;
}
function plateform_fetch($value, $field = "token")
{
    global $_W;
    $type = "plateform";
    if ($field == "token") {
        $tokens = explode(":", $value);
        if (count($tokens) == 2) {
            list($type, $value) = $tokens;
        }
    }
    if ($type == "plateform") {
        $record = pdo_get("users", array($field => $value));
    } else {
        $record = pdo_get("tiny_wmall_agent", array("uniacid" => $_W["uniacid"], $field => $value));
        if (!empty($record)) {
            foreach ($record as $key => $val) {
                if (in_array($key, array("sysset", "pluginset", "account", "geofence", "data", "fee"))) {
                    $record[$key] = iunserializer($record[$key]);
                }
            }
        }
    }
    if (empty($record)) {
        return error(41009, "用户不存在");
    }
    $record["jpush_relation"] = plateform_push_token($record);
    $_W["wxapp"]["jpush_relation"] = $record["jpush_relation"];
    $record["usertype"] = $type;
    if ($record["usertype"] == "plateform") {
        $role = uni_permission($record["uid"], $_W["uniacid"]);
        if (empty($role)) {
            return error(41011, "您的账号已禁用，请联系管理员！");
        }
        $record["role"] = $role;
        if ($role == "founder") {
            $_W["isfounder"] = 1;
        } else {
            $record["perms"] = "all";
            if ($role == "operator") {
                $user = get_user($record["uid"]);
                if (empty($user["status"])) {
                    return error(41011, "您的账号已禁用，请联系管理员！");
                }
                $record["perms"] = $user["perms"];
            }
        }
    } else {
        mload()->model("agent");
        $record["role"] = "agenter";
        $record["perms"] = get_agent_perms();
        $record["agent"] = $record;
    }
    return $record;
}
function plateform_push_token($user)
{
    global $_W;
    if (!is_array($user)) {
        return error(-1, "用户信息不完善");
    }
    $config = $_W["we7_wmall"]["config"]["app"]["plateform"]["app"];
    if (empty($config["push_tags"])) {
        $config["push_tags"] = array("all" => random(8));
        set_plugin_config("plateformApp.app", $config);
    }
    $relation = array("alias" => $user["token"], "tags" => array($config["push_tags"]["all"]));
    if ($user["usertype"] == "agenter" && !empty($user["token"])) {
        $relation["tags"][] = $user["token"];
    }
    $code = md5(iserializer($relation));
    $relation["code"] = $code;
    return $relation;
}
function icheckplateformer()
{
    global $_W;
    global $_GPC;
    $_W["plateformer"] = array();
    $token = trim($_GPC["token"]);
    if (!empty($token)) {
        $plateformer = plateform_fetch($token, "token");
        if (is_error($plateformer)) {
            imessage($plateformer, "", "ajax");
        }
        $_W["agentid"] = 0;
        $GPC["agentid"] = 0;
        $_W["is_agent"] = is_agent();
        $_W["role"] = $plateformer["role"];
        $_W["user"] = $plateformer;
        if ($plateformer["usertype"] == "plateform") {
            $_W["perms"] = $plateformer["perms"];
        } else {
            mload()->model("agent");
            $_W["perms"] = get_agent_perms();
            $_W["agent"] = $plateformer;
            $_W["agentid"] = $_W["agent"]["id"];
            $_GPC["agentid"] = $_W["agent"]["id"];
            $_W["we7_wmall"]["config"] = get_system_config();
        }
        $perm = (string) $_W["_action"] . "." . $_W["_op"];
        if (!empty($_W["_op_perm"])) {
            $perm = (string) $_W["_action"] . "." . $_W["_op_perm"];
        }
        if (!check_perm($perm, false)) {
            imessage(error(41011, "您没有权限进行该操作！"), "", "ajax");
        }
        $_W["isoperator"] = $_W["role"] == "operator";
        $_W["ismanager"] = $_W["role"] == "manager" || !empty($_W["isfounder"]);
        $_W["isagenter"] = $_W["role"] == "agenter" || !empty($_W["isfounder"]);
        if ($_W["role"] == "founder") {
            $plateformer["role_type"] = "平台管理员";
            $_W["role_cn"] = "平台管理员:" . $_W["user"]["username"];
        }
        if ($_W["role"] == "manager") {
            $plateformer["role_type"] = "公众号管理员";
            $_W["role_cn"] = "公众号管理员:" . $_W["user"]["username"];
        } else {
            if ($_W["role"] == "operator") {
                $plateformer["role_type"] = "公众号操作员";
                $_W["role_cn"] = "公众号操作员:" . $_W["user"]["username"];
            } else {
                if ($_W["role"] == "merchanter") {
                    $plateformer["role_type"] = "店铺管理员";
                    $_W["role_cn"] = "店铺管理员:" . $_W["user"]["username"];
                } else {
                    if ($_W["role"] == "agenter") {
                        $plateformer["username"] = $_W["agent"]["realname"];
                        $plateformer["role_type"] = "代理商";
                        $_W["role_cn"] = "代理商:" . $_W["agent"]["realname"];
                    }
                }
            }
        }
        $plateformer["role_cn"] = $_W["role_cn"];
        $_W["plateformer"] = $plateformer;
    }
    if (empty($_W["plateformer"]) && defined("IN_VUE")) {
        imessage(error(41009, "请先登录"), "", "ajax");
    }
}
function plateform_urls()
{
    global $_W;
    $data = array();
    $data["takeout"]["sys"] = array("title" => "外卖", "items" => array(array("title" => "外卖订单", "url" => "pages/order/takeout"), array("title" => "当面付", "url" => "pages/paycenter/paybill"), array("title" => "售后", "url" => "pages/service/comment?"), array("title" => "统计", "url" => "pages/statcenter/index")));
    $data["store"]["sys"] = array("title" => "商户", "items" => array(array("title" => "商户列表", "url" => "pages/merchant/store"), array("title" => "商户活动列表", "url" => "pages/merchant/activity/list"), array("title" => "提现申请记录", "url" => "pages/merchant/getcash"), array("title" => "账户明细记录", "url" => "pages/merchant/current"), array("title" => "商户入驻列表", "url" => "pages/merchant/settle"), array("title" => "商家回收站", "url" => "pages/merchant/storage"), array("title" => "投诉列表", "url" => "pages/merchant/report")));
    $data["deliveryer"]["sys"] = array("title" => "配送员", "items" => array(array("title" => "配送员管理", "url" => "pages/deliveryer/index"), array("title" => "配送员列表", "url" => "pages/deliveryer/deliveryer"), array("title" => "提现申请记录", "url" => "pages/deliveryer/getcash"), array("title" => "账户明细记录", "url" => "pages/deliveryer/current"), array("title" => "配送员位置", "url" => "pages/deliveryer/location")));
    if (check_plugin_perm("errander")) {
        $data["plugin"]["errander"] = array("title" => "跑腿", "items" => array(array("title" => "跑腿管理", "url" => "pages/plugin/paotui/index"), array("title" => "跑腿订单", "url" => "pages/plugin/paotui/list"), array("title" => "跑腿设置", "url" => "pages/plugin/paotui/config")));
    }
    if (check_plugin_perm("agent")) {
        $data["plugin"]["agent"] = array("title" => "区域代理", "items" => array(array("title" => "区域代理管理", "url" => "pages/plugin/agent/index"), array("title" => "代理列表", "url" => "pages/plugin/agent/agent"), array("title" => "提现记录", "url" => "pages/plugin/agent/getcash"), array("title" => "账户明细", "url" => "pages/plugin/agent/current")));
    }
    if (check_plugin_perm("creditshop")) {
        $data["plugin"]["creditshop"] = array("title" => "积分商城", "items" => array(array("title" => "兑换列表", "url" => "pages/plugin/creditshop/order")));
    }
    if (check_plugin_perm("deliveryCard")) {
        $data["plugin"]["deliveryCard"] = array("title" => "配送会员卡", "items" => array(array("title" => "购买记录", "url" => "pages/plugin/deliveryCard/order")));
    }
    if (check_plugin_perm("mealRedpacket")) {
        $data["plugin"]["mealRedpacket"] = array("title" => "套餐红包", "items" => array(array("title" => "购买记录", "url" => "pages/plugin/mealRedpacket/order")));
    }
    if (check_plugin_perm("wheel")) {
        $data["plugin"]["wheel"] = array("title" => "幸运大转盘", "items" => array(array("title" => "参与记录", "url" => "pages/plugin/wheel/record")));
    }
    if (check_plugin_perm("advertise")) {
        $data["plugin"]["advertise"] = array("title" => "商户广告通", "items" => array(array("title" => "购买记录", "url" => "pages/plugin/advertise/order")));
    }
    $data["other"]["sys"] = array("title" => "其他", "items" => array(array("title" => "顾客列表", "url" => "pages/member/list"), array("title" => "系统设置", "url" => "pages/config/index"), array("title" => "更多", "url" => "pages/more/index"), array("title" => "我的", "url" => "pages/member/mine")));
    return $data;
}
function get_plateform_menu()
{
    global $_W;
    $menu = get_plugin_config("plateformApp.menu");
    if (empty($menu)) {
        $menu = array("name" => "default", "params" => array("navstyle" => "0"), "css" => array("iconColor" => "#163636", "iconColorActive" => "#4FAE52", "textColor" => "#929292", "textColorActive" => "#4FAE52"), "data" => array("M0123456789101" => array("link" => "/pages/order/takeout", "icon" => "icon-order", "text" => "外卖"), "M0123456789102" => array("link" => "/pages/merchant/store", "icon" => "icon-shop", "text" => "店铺"), "M0123456789103" => array("link" => "/pages/more/index", "icon" => "icon-mark1", "text" => "更多"), "M0123456789104" => array("link" => "/pages/member/mine", "icon" => "icon-mine", "text" => "我的")));
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