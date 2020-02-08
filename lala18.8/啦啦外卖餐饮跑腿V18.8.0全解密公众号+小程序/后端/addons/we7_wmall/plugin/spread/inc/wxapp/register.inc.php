<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
icheckauth();
$check = $_config_plugin["relate"]["become_check"];
if ($op == "index") {
    $spread = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]));
    if ($spread["is_spread"] == 1) {
        if ($spread["spread_status"] == 1) {
            imessage(error(-1000, ""), "index", "ajax");
        } else {
            if ($spread["spread_status"] == 2) {
                imessage(error(0, "您已经被加入到推广员黑名单"), "", "ajax");
            } else {
                if ($spread["spread_status"] == 0) {
                    imessage(error(-1001, "您已提交推广员申请,请等待管理员审核"), "", "ajax");
                }
            }
        }
    }
    $configRelate = $_config_plugin["relate"];
    $configTemplate = $_config_plugin["template"];
    $configTemplate["avatar"] = tomedia($configTemplate["avatar"]);
    $result = array("configRelate" => $configRelate, "agreement" => get_config_text("spread:agreement"), "configTemplate" => $configTemplate, "title" => $_config_mall["title"], "spread" => $spread);
    $legal = 0;
    if ($configRelate["become"] == 0) {
        $legal = 1;
    } else {
        if ($configRelate["become"] == 1) {
            $legal = 0;
        } else {
            if ($configRelate["become"] == 2) {
                $condition = "where uniacid = :uniacid and status = 5 and uid = :uid";
                $params = array(":uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]);
                $frquency = pdo_fetchcolumn("select count(*) from" . tablename("tiny_wmall_order") . $condition, $params);
                $result["frquency"] = $frquency;
                if ($_W["ispost"] && $configRelate["become_ordercount"] <= $frquency) {
                    $legal = 1;
                }
            } else {
                if ($configRelate["become"] == 3) {
                    $condition = "where uniacid = :uniacid and status = 5 and uid = :uid";
                    $params = array(":uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]);
                    $price = pdo_fetchcolumn("select sum(final_fee) from " . tablename("tiny_wmall_order") . $condition, $params);
                    $price = round($price, 0);
                    $result["price"] = $price;
                    if ($_W["ispost"] && $configRelate["become_moneycount"] <= $price) {
                        $legal = 1;
                    }
                }
            }
        }
    }
    if ($legal == 1) {
        $update = array("is_spread" => 1);
        if ($check == 1) {
            $update["spread_status"] = 0;
        } else {
            $update["spread_status"] = 1;
            $update["spreadtime"] = TIMESTAMP;
        }
        pdo_update("tiny_wmall_members", $update, array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]));
        $errno = 0;
        if ($update["spread_status"] == 1) {
            $errno = -1000;
        }
        imessage(error($errno, $result), "", "ajax");
    }
    imessage(error(0, $result), "", "ajax");
}
if ($op == "application") {
    $name = trim($_GPC["name"]);
    if (empty($name)) {
        imessage(error(-1, "姓名不能为空"), "", "ajax");
    }
    $mobile = trim($_GPC["mobile"]);
    if (empty($mobile)) {
        imessage(error(-1, "手机号不能为空"), "", "ajax");
    }
    $update = array("realname" => $name, "mobile" => $mobile, "is_spread" => 1);
    if ($check == 1) {
        $update["spread_status"] = 0;
    } else {
        $update["spread_status"] = 1;
        $update["spreadtime"] = TIMESTAMP;
    }
    pdo_update("tiny_wmall_members", $update, array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]));
    if ($check == 1) {
        sys_notice_spread_settle($_W["member"]["uid"], "apply");
    } else {
        sys_notice_spread_settle($_W["member"]["uid"], "success");
    }
    $errno = 0;
    if ($update["spread_status"] == 1) {
        $errno = -1000;
    }
    imessage(error($errno, ""), "", "ajax");
}

?>