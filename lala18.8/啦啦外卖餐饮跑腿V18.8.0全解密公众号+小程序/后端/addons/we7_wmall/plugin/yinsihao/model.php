<?php

defined("IN_IA") or exit("Access Denied");
function yinsihao_get_order($orderOrId, $ordersn, $orderType = "waimai")
{
    global $_W;
    if (is_array($orderOrId)) {
        $order = $orderOrId;
    } else {
        if ($orderType == "waimai") {
            $order = order_fetch($orderOrId);
        } else {
            if ($orderType == "errander") {
                mload()->model("plugin");
                pload()->model("errander");
                $order = errander_order_fetch($orderOrId);
            }
        }
    }
    if ($orderType == "errander") {
        $order["ordersn"] = $order["order_sn"];
    }
    if (empty($order)) {
        return error(-1, "订单不存在");
    }
    if ($order["ordersn"] != $ordersn) {
        return error(-1, "订单信息有误");
    }
    if ($order["data"]["yinsihao_status"] != 1) {
        return error(-1, "该订单未开启号码保护功能");
    }
    return $order;
}
function yinsihao_bind($orderOrId, $type, $ordersn, $orderType = "waimai", $memberType = "accept")
{
    global $_W;
    global $_GPC;
    $order = yinsihao_get_order($orderOrId, $ordersn, $orderType);
    if (is_error($order)) {
        return $order;
    }
    $basic = get_plugin_config("yinsihao.basic");
    if (empty($basic) || $basic["status"] != 1) {
        return error(-1, "平台未开启号码保护功能");
    }
    $status = yinsihao_order_check($orderOrId, $ordersn, $orderType);
    if (empty($status)) {
        return error(-2, "订单已无法使用隐私号联系");
    }
    $types = array("store", "deliveryer", "member", "errander");
    if (!in_array($type, $types)) {
        return error(-1, "隐私号绑定类型错误");
    }
    $mobile = "";
    if ($type == "member") {
        $mobile = $order["mobile"];
    } else {
        if ($type == "store") {
            $mobile = pdo_fetchcolumn("select telephone from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid and id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $order["sid"]));
        } else {
            if ($type == "deliveryer") {
                $mobile = pdo_fetchcolumn("select mobile from " . tablename("tiny_wmall_deliveryer") . " where uniacid = :uniacid and id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $order["deliveryer_id"]));
            } else {
                if ($type == "errander") {
                    if ($memberType == "buy") {
                        $mobile = $order["buy_mobile"];
                    } else {
                        $mobile = $order["accept_mobile"];
                    }
                }
            }
        }
    }
    if (empty($mobile)) {
        return error(-1, "待绑定的手机号码不存在");
    }
    $checkMobile = yinsihao_checkMobileIsBind($mobile, $type);
    if (!empty($checkMobile)) {
        return $checkMobile;
    }
    $numbers = $basic[$type . "_number"];
    $secret_mobile = yinsihao_get_avaiable_secret_mobile($numbers);
    if (is_error($secret_mobile)) {
        return $secret_mobile;
    }
    $poolKey = "";
    if (!empty($basic["poolKey"])) {
        foreach ($basic["poolKey"] as $key => $value) {
            if (in_array($secret_mobile, $value)) {
                $poolKey = $key;
                break;
            }
        }
    }
    if (empty($poolKey)) {
        return error(-1, "没有有效的号码池Key");
    }
    mload()->model("sms");
    $expiration = TIMESTAMP + 6 * 30 * 24 * 3600;
    if ($type == "member") {
        $expiration = TIMESTAMP + $basic["member_expiration"] * 60;
    }
    $params = array("Expiration" => date("Y-m-d H:i:s", $expiration), "PhoneNoA" => $mobile, "PoolKey" => $poolKey, "PhoneNoX" => $secret_mobile, "AccessKeyId" => $basic["accessKeyId"], "AccessSecret" => $basic["accessSecret"]);
    $data = sms_bindAxnExtension($params);
    if (is_error($data)) {
        return $data;
    }
    $insert = array("uniacid" => $_W["uniacid"], "type" => $type, "real_mobile" => $mobile, "secret_mobile" => $data["SecretNo"], "extension" => $data["Extension"], "subsid" => $data["SubsId"], "addtime" => TIMESTAMP, "expiration" => $expiration);
    $id = pdo_insert("tiny_wmall_yinsihao_bind_list", $insert);
    if (empty($id)) {
        return error(-1, "隐私号绑定关系保存时发生错误");
    }
    return $insert;
}
function yinsihao_order_check($orderOrId, $ordersn, $orderType)
{
    global $_W;
    $order = yinsihao_get_order($orderOrId, $ordersn, $orderType);
    if ($order["data"]["yinsihao_status"] != 1) {
        return false;
    }
    if ($orderType == "waimai") {
        if ($order["status"] < 5) {
            return true;
        }
    } else {
        if ($orderType == "errander") {
            if ($order["status"] < 3) {
                return true;
            }
            $order["endtime"] = $order["delivery_success_time"];
        }
    }
    $usefultime = 0 * 24 * 3600;
    $overtime = $order["endtime"] + $usefultime;
    if ($overtime <= TIMESTAMP) {
        return false;
    }
    return true;
}
function yinsihao_checkMobileIsBind($mobile, $type)
{
    global $_W;
    $data = pdo_fetch("select * from " . tablename("tiny_wmall_yinsihao_bind_list") . " where uniacid = :uniacid and type = :type and real_mobile = :real_mobile and expiration > :expiration", array(":uniacid" => $_W["uniacid"], ":type" => $type, ":real_mobile" => $mobile, ":expiration" => TIMESTAMP));
    if (empty($data)) {
        return false;
    }
    return $data;
}
function yinsihao_get_avaiable_secret_mobile($numberArr)
{
    global $_W;
    if (empty($numberArr)) {
        return false;
    }
    $numberStr = "'" . implode("','", $numberArr) . "'";
    $data = pdo_fetchall("select  secret_mobile,count(*) as num from " . tablename("tiny_wmall_yinsihao_bind_list") . " where uniacid = :uniacid and expiration > :expiration and secret_mobile in (" . $numberStr . ") group by secret_mobile order by num asc ", array(":uniacid" => $_W["uniacid"], ":expiration" => TIMESTAMP), "secret_mobile");
    if (empty($data)) {
        return reset($numberArr);
    }
    if (count($numberArr) != count($data)) {
        $used = array_keys($data);
        foreach ($numberArr as $value) {
            if (!in_array($value, $used)) {
                return $value;
            }
        }
        return NULL;
    } else {
        $first = reset($data);
        if (200 <= $first["num"]) {
            return error(-1, "没有有效的隐私号段");
        }
        return $first["secret_mobile"];
    }
}

?>
