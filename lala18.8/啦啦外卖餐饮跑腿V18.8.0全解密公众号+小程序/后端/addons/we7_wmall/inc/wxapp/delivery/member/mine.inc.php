<?php


defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $stat = deliveryer_stat_order();
    $result = array("deliveryer" => $_W["deliveryer"], "stat" => $stat, "takeout_rank_status" => $config_delivery["extra"]["takeout_rank_status"], "errander_rank_status" => $config_delivery["extra"]["errander_rank_status"], "config" => array("development_delivery_location" => $_W["we7_wmall"]["global"]["development_delivery_location"]));
    $notice_total = deliveryer_notice_stat($_W["deliveryer"]["id"]);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "setting") {
        $which = trim($_GPC["which"]);
        if ($which == "work_status") {
            $work_status = !$_W["deliveryer"]["work_status"];
            deliveryer_work_status_set($_W["deliveryer"]["id"], !$_W["deliveryer"]["work_status"]);
            $_W["deliveryer"]["work_status"] = $work_status;
            $relation = deliveryer_push_token($_W["deliveryer"]);
            $_W["wxapp"]["jpush_relation"] = $relation;
            imessage(error(0, "工作状态设置成功"), "", "ajax");
        } else {
            deliveryer_set_extra($which, !$_W["deliveryer"]["extra"][$which], $_W["deliveryer"]["id"]);
            $message = $which == "accept_wechat_notice" ? "微信模板消息提醒设置成功" : "语音电话提醒设置成功";
            imessage(error(0, $message), "", "ajax");
        }
    } else {
        if ($ta == "password") {
            $password = trim($_GPC["password"]);
            $newpassword = trim($_GPC["newpassword"]);
            $repassword = trim($_GPC["repassword"]);
            if (empty($password)) {
                imessage(error(-1, "密码不能为空"), "", "ajax");
            }
            if (empty($newpassword)) {
                imessage(error(-1, "新密码不能为空"), "", "ajax");
            }
            $length = strlen($newpassword);
            if ($length < 8 || 20 < $length) {
                imessage(error(-1, "请输入8-20密码"), "", "ajax");
            }
            if (!preg_match(IREGULAR_PASSWORD, $newpassword)) {
                imessage(error(-1, "密码必须由数字和字母组合"), "", "ajax");
            }
            if (empty($repassword)) {
                imessage(error(-1, "请确认密码"), "", "ajax");
            }
            if ($newpassword != $repassword) {
                imessage(error(-1, "两次输入的密码不一致"), "", "ajax");
            }
            $password = md5(md5($_W["deliveryer"]["salt"] . $password) . $_W["deliveryer"]["salt"]);
            if ($password != $_W["deliveryer"]["password"]) {
                imessage(error(-1, "原密码错误"), "", "ajax");
            }
            $data = array("password" => md5(md5($_W["deliveryer"]["salt"] . $newpassword) . $_W["deliveryer"]["salt"]));
            pdo_update("tiny_wmall_deliveryer", $data, array("uniacid" => $_W["uniacid"], "id" => $_W["deliveryer"]["id"]));
            imessage(error(0, "修改成功"), "", "ajax");
        } else {
            if ($ta == "location") {
                $location = pdo_fetch("select * from " . tablename("tiny_wmall_deliveryer_location_log") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id order by id desc", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $_W["deliveryer"]["id"]));
                if (empty($location)) {
                    imessage(error(-1, "未获取到配送员位置信息"), "", "ajax");
                }
                $location["address"] = "未知地址";
                $address = geocode_regeo(array($location["location_y"], $location["location_x"]));
                if (!is_error($address)) {
                    $location["address"] = $address;
                }
                $result = array("location" => $location);
                imessage(error(0, $result), "", "ajax");
            }
        }
    }
}

?>