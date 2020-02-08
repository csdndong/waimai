<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("deliveryer");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $deliveryer = deliveryer_filter();
    $result = array("records" => $deliveryer);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "change_status") {
        $deliveryerId = intval($_GPC["id"]);
        $type = trim($_GPC["type"]);
        $status = intval($_GPC["value"]);
        if ($type == "work_status") {
            $result = deliveryer_work_status_set($deliveryerId, $status, true, true);
        } else {
            if (in_array($type, array("accept_wechat_notice", "accept_voice_notice"))) {
                $result = deliveryer_set_extra($type, $status, $deliveryerId);
            }
        }
        if (is_error($result)) {
            imessage($result, "", "ajax");
        }
        imessage(error(0, ""), "", "ajax");
    } else {
        if ($ta == "post") {
            $id = intval($_GPC["id"]);
            if ($_W["ispost"]) {
                $deliveryer = $_GPC["deliveryer"];
                $wechat = $_GPC["wechat"];
                if (!$id && empty($wechat)) {
                    imessage(error(-1, "请选择一个粉丝"), "", "ajax");
                }
                if (!empty($wechat)) {
                    $openid = trim($wechat["openid"]);
                    if (!empty($openid)) {
                        $is_exist = pdo_fetchcolumn("select id from " . tablename("tiny_wmall_deliveryer") . " where uniacid = :uniacid and openid = :openid and id != :id", array(":uniacid" => $_W["uniacid"], ":openid" => $openid, ":id" => $id));
                        if (!empty($is_exist)) {
                            imessage(error(-1, "该微信信息已绑定其他配送员, 请更换微信信息"), "", "ajax");
                        }
                        $deliveryer["openid"] = $openid;
                    }
                    $openid_wxapp = trim($wechat["openid_wxapp"]);
                    if (!empty($openid_wxapp)) {
                        $is_exist = pdo_fetchcolumn("select id from " . tablename("tiny_wmall_deliveryer") . " where uniacid = :uniacid and openid_wxapp = :openid_wxapp and id != :id", array(":uniacid" => $_W["uniacid"], ":openid_wxapp" => $openid_wxapp, ":id" => $id));
                        if (!empty($is_exist)) {
                            imessage(error(-1, "该微信信息已绑定其他配送员, 请更换微信信息"), "", "ajax");
                        }
                        $deliveryer["openid_wxapp"] = $openid_wxapp;
                    }
                    $deliveryer["avatar"] = trim($wechat["avatar"]) ? trim($wechat["avatar"]) : "";
                }
                if (empty($deliveryer["title"])) {
                    imessage(error(-1, "请填写配送员真实姓名"), "", "ajax");
                }
                $mobile = trim($deliveryer["mobile"]);
                if (!is_validMobile($mobile)) {
                    imessage(error(-1, "手机号格式错误"), "", "ajax");
                }
                $is_exist = pdo_fetchcolumn("select id from " . tablename("tiny_wmall_deliveryer") . " where uniacid = :uniacid and mobile = :mobile and id != :id", array(":uniacid" => $_W["uniacid"], ":mobile" => $mobile, ":id" => $id));
                if (!empty($is_exist)) {
                    imessage(error(-1, "该手机号已绑定其他配送员, 请更换手机号"), "", "ajax");
                }
                if (empty($deliveryer["age"])) {
                    imessage(error(-1, "请填写配送员年龄"), "", "ajax");
                }
                if (!$id && empty($deliveryer["password"])) {
                    imessage(error(-1, "请填写登录密码"), "", "ajax");
                }
                $data = array("uniacid" => $_W["uniacid"], "mobile" => $mobile, "title" => trim($deliveryer["title"]), "openid" => trim($deliveryer["openid"]), "openid_wxapp" => trim($deliveryer["openid_wxapp"]), "nickname" => trim($deliveryer["nickname"]), "avatar" => trim($deliveryer["avatar"]), "sex" => trim($deliveryer["sex"]), "age" => intval($deliveryer["age"]));
                $password = trim($deliveryer["password"]);
                if (!empty($password)) {
                    $length = strlen($password);
                    if ($length < 8 || 20 < $length) {
                        imessage(error(-1, "请输入8-20密码"), "", "ajax");
                    }
                    if (!preg_match(IREGULAR_PASSWORD, $password)) {
                        imessage(error(-1, "密码必须由数字和字母组合"), "", "ajax");
                    }
                    if ($password != trim($deliveryer["repassword"])) {
                        imessage(error(-1, "两次密码输入不一致"), "", "ajax");
                    }
                    $data["salt"] = random(6);
                    $data["password"] = md5(md5($data["salt"] . $password) . $data["salt"]);
                }
                if (0 < $id) {
                    mlog(4001, $id);
                    pdo_update("tiny_wmall_deliveryer", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
                } else {
                    $data["token"] = random(32);
                    $data["addtime"] = TIMESTAMP;
                    pdo_insert("tiny_wmall_deliveryer", $data);
                    $id = pdo_insertid();
                    mlog(4000, $id, "平台添加配送员");
                }
                deliveryer_all(true);
                imessage(error(0, $id), "", "ajax");
            }
            $deliveryer = deliveryer_fetch($id);
            if (empty($deliveryer)) {
                imessage(error(-1, "配送员不存在"), "", "ajax");
            }
            if ($deliveryer["status"] != 1) {
                imessage(error(-1, "配送员已被删除"), "", "ajax");
            }
            $result = array("deliveryer" => $deliveryer);
            imessage(error(0, $result), "", "ajax");
        } else {
            if ($ta == "comision") {
                $id = intval($_GPC["id"]);
                $deliveryer = deliveryer_fetch($id);
                if (empty($deliveryer)) {
                    imessage(error(-1, "配送员不存在"), "", "ajax");
                }
                if ($deliveryer["status"] != 1) {
                    imessage(error(-1, "配送员已被删除"), "", "ajax");
                }
                if ($_W["ispost"]) {
                    $deliveryer = $_GPC["deliveryer"];
                    $fee_deliveryer_takeout = $deliveryer["fee_delivery"]["takeout"];
                    $deliveryer_takeout_fee_type = intval($fee_deliveryer_takeout["deliveryer_fee_type"]);
                    if (!in_array($deliveryer_takeout_fee_type, array(1, 2, 3))) {
                        imessage(error(-1, "请选择外卖单提成方式"), "", "ajax");
                    }
                    if ($deliveryer_takeout_fee_type == 1 || $deliveryer_takeout_fee_type == 2) {
                        $deliveryer_takeout_fee = floatval($fee_deliveryer_takeout["deliveryer_fee"]);
                    } else {
                        if ($deliveryer_takeout_fee_type == 3) {
                            $deliveryer_takeout_fee = array("start_fee" => floatval($fee_deliveryer_takeout["deliveryer_fee"]["start_fee"]), "start_km" => floatval($fee_deliveryer_takeout["deliveryer_fee"]["start_km"]), "pre_km" => floatval($fee_deliveryer_takeout["deliveryer_fee"]["pre_km"]), "max_fee" => floatval($fee_deliveryer_takeout["deliveryer_fee"]["max_fee"]));
                        }
                    }
                    $fee_deliveryer_errander = $deliveryer["fee_delivery"]["errander"];
                    $deliveryer_errander_fee_type = intval($fee_deliveryer_errander["deliveryer_fee_type"]);
                    if (!in_array($deliveryer_errander_fee_type, array(1, 2, 3))) {
                        imessage(error(-1, "请选择跑腿单提成方式"), "", "ajax");
                    }
                    if ($deliveryer_errander_fee_type == 1 || $deliveryer_errander_fee_type == 2) {
                        $deliveryer_errander_fee = floatval($fee_deliveryer_errander["deliveryer_fee"]);
                    } else {
                        if ($deliveryer_errander_fee_type == 3) {
                            $deliveryer_errander_fee = array("start_fee" => floatval($fee_deliveryer_errander["deliveryer_fee"]["start_fee"]), "start_km" => floatval($fee_deliveryer_errander["deliveryer_fee"]["start_km"]), "pre_km" => floatval($fee_deliveryer_errander["deliveryer_fee"]["pre_km"]), "max_fee" => floatval($fee_deliveryer_errander["deliveryer_fee"]["max_fee"]));
                        }
                    }
                    $fee_delivery = array("takeout" => array("deliveryer_fee_type" => $deliveryer_takeout_fee_type, "deliveryer_fee" => $deliveryer_takeout_fee), "errander" => array("deliveryer_fee_type" => $deliveryer_errander_fee_type, "deliveryer_fee" => $deliveryer_errander_fee));
                    $update = array("fee_delivery" => iserializer($fee_delivery));
                    if (empty($_W["agentid"])) {
                        $fee_getcash = array("get_cash_fee_limit" => intval($deliveryer["fee_getcash"]["get_cash_fee_limit"]), "get_cash_fee_min" => floatval($deliveryer["fee_getcash"]["get_cash_fee_min"]), "get_cash_fee_max" => floatval($deliveryer["fee_getcash"]["get_cash_fee_max"]), "get_cash_fee_rate" => floatval($deliveryer["fee_getcash"]["get_cash_fee_rate"]), "get_cash_period" => intval($deliveryer["fee_getcash"]["get_cash_period"]));
                        $update["fee_getcash"] = iserializer($fee_getcash);
                    }
                    mlog(4001, $id);
                    pdo_update("tiny_wmall_deliveryer", $update, array("uniacid" => $_W["uniacid"], "id" => $id));
                    imessage(error(0, "配送员提成及提现设置成功"), "", "ajax");
                }
                $result = array("deliveryer" => $deliveryer);
                imessage(error(0, $result), "", "ajax");
            } else {
                if ($ta == "power") {
                    $id = intval($_GPC["id"]);
                    $deliveryer = deliveryer_fetch($id);
                    if (empty($deliveryer)) {
                        imessage(error(-1, "配送员不存在"), "", "ajax");
                    }
                    if ($deliveryer["status"] != 1) {
                        imessage(error(-1, "配送员已被删除"), "", "ajax");
                    }
                    if ($_W["ispost"]) {
                        $deliveryer = $_GPC["deliveryer"];
                        $perm_cancel = array("status_takeout" => intval($deliveryer["perm_cancel"]["status_takeout"]), "status_errander" => intval($deliveryer["perm_cancel"]["status_errander"]));
                        $perm_transfer = array("status_takeout" => intval($deliveryer["perm_transfer"]["status_takeout"]), "status_errander" => intval($deliveryer["perm_transfer"]["status_errander"]), "max_takeout" => intval($deliveryer["perm_transfer"]["max_takeout"]), "max_errander" => intval($deliveryer["perm_transfer"]["max_errander"]));
                        $update = array("is_takeout" => intval($deliveryer["is_takeout"]), "is_errander" => intval($deliveryer["is_errander"]), "perm_cancel" => iserializer($perm_cancel), "perm_transfer" => iserializer($perm_transfer), "collect_max_takeout" => intval($deliveryer["collect_max_takeout"]), "collect_max_errander" => intval($deliveryer["collect_max_errander"]));
                        mlog(4001, $id);
                        pdo_update("tiny_wmall_deliveryer", $update, array("uniacid" => $_W["uniacid"], "id" => $id));
                        imessage(error(0, "配送员配送权限设置成功"), "", "ajax");
                    }
                    $result = array("deliveryer" => $deliveryer);
                    imessage(error(0, $result), "", "ajax");
                } else {
                    if ($ta == "location") {
                        mload()->model("deliveryer.extra");
                        $ids = array();
                        if (!empty($_GPC["ids"])) {
                            $ids = array_map("intval", $_GPC["ids"]);
                        }
                        $deliveryer = deliveryer_get_location(array("ids" => $ids));
                        $result = array("deliveryer" => $deliveryer);
                        imessage(error(0, $result), "", "ajax");
                    }
                }
            }
        }
    }
}

?>