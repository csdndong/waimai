<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "account";
$config_store = $_W["we7_wmall"]["config"]["store"];
if ($config_store["settle"]["status"] != 1) {
    imessage(error(-1, "暂时不支持商户入驻"), "", "ajax");
}
$perm = check_max_store_perm();
if (empty($perm)) {
    imessage(error(-1, "门店入驻量已超过上限,请联系公众号管理员"), referer(), "ajax");
}
$clerk = pdo_get("tiny_wmall_clerk", array("uniacid" => $_W["uniacid"], "openid_wxapp" => $_W["openid"]));
if (empty($clerk) && !empty($_W["openid_wechat"])) {
    $clerk = pdo_get("tiny_wmall_clerk", array("uniacid" => $_W["uniacid"], "openid" => $_W["openid_wechat"]));
}
if ($op == "account") {
    if (!empty($clerk)) {
        imessage(error(-1000, ""), "", "ajax");
    }
    if ($_W["ispost"]) {
        $mobile = trim($_GPC["mobile"]);
        if (!is_validMobile($mobile)) {
            imessage(error(-1, "手机号格式错误"), "", "ajax");
        }
        if ($config_store["settle"]["mobile_verify_status"] == 1) {
            $code = trim($_GPC["code"]);
            $status = icheck_verifycode($mobile, $code);
            if (!$status) {
                imessage(error(-1, "验证码错误"), "", "ajax");
            }
        }
        $is_exist = pdo_fetchcolumn("select id from " . tablename("tiny_wmall_clerk") . " where uniacid = :uniacid and mobile = :mobile", array(":uniacid" => $_W["uniacid"], ":mobile" => $mobile));
        if (!empty($is_exist)) {
            imessage(error(-1, "该手机号已绑定其他店员, 请更换手机号"), "", "ajax");
        }
        $is_exist = pdo_fetchcolumn("select id from " . tablename("tiny_wmall_clerk") . " where uniacid = :uniacid and openid_wxapp = :openid_wxapp", array(":uniacid" => $_W["uniacid"], ":openid_wxapp" => $_W["openid"]));
        if (!empty($is_exist)) {
            imessage(error(-1, "该微信信息已绑定其他店员, 请更换微信信息"), "", "ajax");
        }
        if (!empty($_W["openid_wechat"])) {
            $is_exist = pdo_fetchcolumn("select id from " . tablename("tiny_wmall_clerk") . " where uniacid = :uniacid and openid = :openid_wechat", array(":uniacid" => $_W["uniacid"], ":openid_wechat" => $_W["openid_wechat"]));
            if (!empty($is_exist)) {
                imessage(error(-1, "该微信信息已绑定其他店员, 请更换微信信息"), "", "ajax");
            }
        }
        $password = trim($_GPC["password"]) ? trim($_GPC["password"]) : imessage(error(-1, "密码不能为空"), "", "ajax");
        $length = strlen($password);
        if ($length < 8 || 20 < $length) {
            imessage(error(-1, "请输入8-20密码"), "", "ajax");
        }
        if (!preg_match(IREGULAR_PASSWORD, $password)) {
            imessage(error(-1, "密码必须由数字和字母组合"), "", "ajax");
        }
        $data = array("uniacid" => $_W["uniacid"], "agentid" => intval($_GPC["agentid"]), "mobile" => $mobile, "title" => trim($_GPC["title"]), "openid" => $_W["openid_wechat"], "openid_wxapp" => $_W["openid"], "nickname" => $_W["member"]["nickname"], "avatar" => $_W["member"]["avatar"], "salt" => random(6), "token" => random(32), "addtime" => TIMESTAMP);
        $data["password"] = md5(md5($data["salt"] . $password) . $data["salt"]);
        pdo_insert("tiny_wmall_clerk", $data);
        $id = pdo_insertid();
        imessage(error(-1000, "继续完善商户信息"), "", "ajax");
    }
    $result = array("mobile_verify_status" => $config_store["settle"]["mobile_verify_status"], "captcha" => imurl("system/common/captcha", array(), true), "isagent" => $_W["is_agent"]);
    if ($_W["is_agent"]) {
        mload()->model("agent");
        $agents = get_agents(1);
        foreach ($agents as $val) {
            $result["agents"][] = array("text" => $val["area"], "id" => $val["id"]);
        }
    }
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($op == "store") {
        if (empty($clerk)) {
            imessage(error(-1000, "请申请商户入驻"), "", "ajax");
        }
        $store_clerk = pdo_get("tiny_wmall_store_clerk", array("uniacid" => $_W["uniacid"], "clerk_id" => $clerk["id"], "role" => "manager"));
        if (!empty($store_clerk)) {
            $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $store_clerk["sid"]));
        }
        if (!empty($store)) {
            $result = array("haodian_status" => $store["haodian_status"]);
            if ($store["haodian_status"] == 1) {
                $result["message"] = "商户入驻申请成功,请去往公众号或电脑端进行管理";
                imessage(error(0, $result), "", "ajax");
            } else {
                if ($store["haodian_status"] == 6) {
                    $order = pdo_get("tiny_wmall_haodian_order", array("uniacid" => $_W["uniacid"], "sid" => $store["id"]), array("id"));
                    $result["message"] = "商户入驻申请待支付,请先进行支付";
                    $result["order_id"] = $order["id"];
                    imessage(error(-1006, $result), "", "ajax");
                } else {
                    if ($store["haodian_status"] == 5) {
                        $result["message"] = "商户入驻申请正在审核中,请耐心等待或联系平台管理员";
                        imessage(error(-1005, $result), "", "ajax");
                    } else {
                        if ($store["haodian_status"] == 4) {
                            $result["message"] = "商户入驻审核未通过,详情请联系平台管理员";
                            imessage(error(-1004, $result), "", "ajax");
                        }
                    }
                }
            }
        }
        if ($_W["ispost"]) {
            $GPC_store = json_decode(htmlspecialchars_decode($_GPC["store"]), true);
            $title = trim($GPC_store["title"]) ? trim($GPC_store["title"]) : imessage(error(-1, "商户名称不能为空"), "", "ajax");
            $qualifications = $_W["ochannel"] == "wxapp" ? $GPC_store["qualification"] : $_GPC["qualification"];
            if ($config_store["settle"]["qualification_verify_status"] == 1 && empty($qualifications[0]["filename"])) {
                imessage(error(-1, "请上传营业执照照片"), "", "ajax");
            }
            $data = array("uniacid" => $_W["uniacid"], "agentid" => $clerk["agentid"] ? $clerk["agentid"] : $_W["agentid"], "title" => $title, "logo" => trim($GPC_store["logo"]), "address" => trim($GPC_store["address"]), "telephone" => trim($GPC_store["telephone"]), "content" => trim($GPC_store["content"]), "status" => $config_store["settle"]["audit_status"], "business_hours" => iserializer(array(array("s" => "8:00", "e" => "20:00"))), "payment" => iserializer(array("wechat")), "remind_time_limit" => 10, "remind_reply" => iserializer(array("快递员狂奔在路上,请耐心等待")), "addtype" => 2, "addtime" => TIMESTAMP, "delivery_mode" => $config_store["delivery"]["delivery_mode"], "delivery_fee_mode" => 1, "delivery_price" => $config_store["delivery"]["delivery_fee"], "push_token" => random(32), "self_audit_comment" => intval($config_store["settle"]["self_audit_comment"]), "haodian_starttime" => TIMESTAMP, "haodian_cid" => intval($GPC_store["haodian_cid"]), "haodian_child_id" => intval($GPC_store["haodian_child_id"]), "haodian_status" => 5, "is_haodian" => 1, "is_waimai" => 0);
            if ($config_store["delivery"]["delivery_fee_mode"] == 2) {
                $data["delivery_fee_mode"] = 2;
                $data["delivery_price"] = iserializer($data["delivery_price"]);
            } else {
                $data["delivery_fee_mode"] = 1;
                $data["delivery_price"] = floatval($data["delivery_price"]);
            }
            $delivery_times = get_config_text("takeout_delivery_time");
            $data["delivery_times"] = iserializer($delivery_times);
            $qualification = array();
            if ($qualifications[0]["filename"]) {
                $qualification["business"]["thumb"] = trim($qualifications[0]["filename"]);
            }
            if ($qualifications[1]["filename"]) {
                $qualification["service"]["thumb"] = trim($qualifications[1]["filename"]);
            }
            if ($qualifications[2]["filename"]) {
                $qualification["more1"]["thumb"] = trim($qualifications[2]["filename"]);
            }
            if (!empty($qualification)) {
                $data["qualification"] = iserializer($qualification);
            }
            $GPC_thumbs = $_W["ochannel"] == "wxapp" ? $GPC_store["thumbs"] : $_GPC["thumbs"];
            if (!empty($GPC_thumbs)) {
                foreach ($GPC_thumbs as $thumb) {
                    $thumbs[] = array("image" => $thumb["filename"], "url" => "");
                }
                $data["thumbs"] = iserializer($thumbs);
            }
            $meal = $_W["ochannel"] == "wxapp" ? $GPC_store["meal"] : $_GPC["meal"];
            if (0 < $meal["days"]) {
                $data["haodian_endtime"] = TIMESTAMP + intval($meal["days"]) * 86400;
            }
            if (0 < $meal["price"]) {
                $data["haodian_status"] = 6;
            }
            pdo_insert("tiny_wmall_store", $data);
            $store_id = pdo_insertid();
            $store_account = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $store_id, "fee_takeout" => iserializer($config_store["serve_fee"]["fee_takeout"]), "fee_selfDelivery" => iserializer($config_store["serve_fee"]["fee_selfDelivery"]), "fee_instore" => iserializer($config_store["serve_fee"]["fee_instore"]), "fee_paybill" => iserializer($config_store["serve_fee"]["fee_paybill"]), "fee_limit" => $config_store["serve_fee"]["get_cash_fee_limit"], "fee_rate" => $config_store["serve_fee"]["get_cash_fee_rate"], "fee_min" => $config_store["serve_fee"]["get_cash_fee_min"], "fee_max" => $config_store["serve_fee"]["get_cash_fee_max"]);
            pdo_insert("tiny_wmall_store_account", $store_account);
            $status = pdo_update("tiny_wmall_store_clerk", array("role" => "manager"), array("uniacid" => $_W["uniacid"], "clerk_id" => $clerk["id"], "sid" => $store_id));
            if (empty($status)) {
                pdo_insert("tiny_wmall_store_clerk", array("uniacid" => $_W["uniacid"], "sid" => $store_id, "clerk_id" => $clerk["id"], "role" => "manager", "addtime" => TIMESTAMP));
            }
            if (0 < $meal["price"]) {
                $meal_order = array("uniacid" => $_W["uniacid"], "agentid" => $data["agentid"], "sid" => $store_id, "uid" => $_W["member"]["uid"], "final_fee" => $meal["price"], "days" => $meal["days"], "ordersn" => date("YmdHis") . random(6, true), "addtime" => TIMESTAMP, "is_pay" => 0);
                $meal_order = haodian_settle_order_bill($meal_order);
		pdo_insert("tiny_wmall_haodian_order", $meal_order);
                $meal_order_id = pdo_insertid();
            }
            sys_notice_settle($store_id, "clerk", "");
            sys_notice_settle($store_id, "manager", "");
            if (0 < $meal_order_id) {
                imessage(error(-1006, $meal_order_id), "", "ajax");
            }
            imessage(error(-1005, ""), "", "ajax");
        }
        $agreement = pdo_get("tiny_wmall_text", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "name" => "agreement_settle"), array("title", "value"));
        $categorys = pdo_fetchall("select id, title, parentid from " . tablename("tiny_wmall_haodian_category") . " where uniacid = :uniacid and agentid = :agentid and status = 1", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]), "id");
        if (!empty($categorys)) {
            foreach ($categorys as &$val) {
                if (!empty($val["parentid"])) {
                    $categorys[$val["parentid"]]["child"][] = $val;
                    unset($categorys[$val["id"]]);
                }
            }
        }
        $config_haodian = $_config_plugin["haodian"];
        $result = array("agreement" => $agreement, "categorys" => array_values($categorys), "config" => array("qualification_verify_status" => intval($config_store["settle"]["qualification_verify_status"]), "meal" => $config_haodian["settle"]["status"] == 1 ? $config_haodian["settle"]["meal"] : array()));
        imessage(error(-1002, $result), "", "ajax");
        return 1;
    } else {
        if ($op == "captcha") {
            $result = array("captcha" => imurl("system/common/captcha", array("captcha" => TIMESTAMP), true));
            imessage(error(0, $result), "", "ajax");
        }
    }
}

?>