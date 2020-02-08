<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "代理列表";
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (title like '%" . $keyword . "%' or area like '%" . $keyword . "%')";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_agent") . $condition, $params);
    $agents = pdo_fetchall("select * from " . tablename("tiny_wmall_agent") . $condition . " order by id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
}
if ($op == "post") {
    $_W["page"]["title"] = "代理设置";
    $id = intval($_GPC["id"]);
    if (0 < $id) {
        $agent = pdo_get("tiny_wmall_agent", array("id" => $id));
        $agent["data"] = iunserializer($agent["data"]);
        $agent["geofence"] = iunserializer($agent["geofence"]);
        $item = array("isChange" => 1, "delivery_areas" => $agent["geofence"]["areas"], "location_y" => $agent["geofence"]["map"]["lng"], "location_x" => $agent["geofence"]["map"]["lat"]);
    } else {
        $item["isChange"] = 1;
    }
    if ($_W["ispost"]) {
        $mobile = trim($_GPC["mobile"]);
        if (!is_validMobile($mobile)) {
            imessage(error(-1, "手机号格式错误"), referer(), "ajax");
        }
        $is_exist = pdo_fetch("select id from " . tablename("tiny_wmall_agent") . " where uniacid = :uniacid and id != :id and mobile = :mobile", array(":id" => $id, ":mobile" => $mobile, ":uniacid" => $_W["uniacid"]));
        if (!empty($is_exist)) {
            imessage(error(-1, "该手机号已被其他代理注册"), referer(), "ajax");
        }
        $area = trim($_GPC["area"]);
        if (empty($area)) {
            imessage(error(-1, "代理区域不能为空"), referer(), "ajax");
        }
        mload()->classs("pinyin");
        $pinyin = new pinyin();
        $initial = $pinyin->getFirstPY($area);
        $initial = strtoupper(substr($initial, 0, 1));
        $data = array("uniacid" => intval($_W["uniacid"]), "title" => trim($_GPC["title"]), "realname" => trim($_GPC["realname"]), "mobile" => $mobile, "area" => $area, "initial" => $initial, "status" => intval($_GPC["status"]));
        $data["data"] = $agent["data"];
        $data["data"] = iserializer($data["data"]);
        $_GPC["areas"] = str_replace("&nbsp;", "#nbsp;", $_GPC["areas"]);
        $_GPC["areas"] = json_decode(str_replace("#nbsp;", "&nbsp;", html_entity_decode(urldecode($_GPC["areas"]))), true);
        foreach ($_GPC["areas"] as $key => &$val) {
            if (empty($val["path"])) {
                unset($_GPC["areas"][$key]);
            }
            $path = array();
            foreach ($val["path"] as $row) {
                $path[] = array($row["lng"], $row["lat"]);
            }
            $val["path"] = $path;
            unset($val["isAdd"]);
            unset($val["isActive"]);
        }
        $data["geofence"]["areas"] = $_GPC["areas"];
        $data["geofence"]["map"]["lat"] = trim($_GPC["map"]["lat"]);
        $data["geofence"]["map"]["lng"] = trim($_GPC["map"]["lng"]);
        $data["geofence"] = iserializer($data["geofence"]);
        if (0 < $id) {
            $password = trim($_GPC["password"]);
            if (!empty($password)) {
                $data["salt"] = random(6);
                $data["password"] = md5(md5($data["salt"] . $password) . $data["salt"]);
            }
            pdo_update("tiny_wmall_agent", $data, array("id" => $id, "uniacid" => $_W["uniacid"]));
        } else {
            $data["password"] = trim($_GPC["password"]) ? trim($_GPC["password"]) : imessage(error(-1, "密码不能为空"), "", "ajax");
            $data["salt"] = random(6);
            $data["password"] = md5(md5($data["salt"] . $data["password"]) . $data["salt"]);
            pdo_insert("tiny_wmall_agent", $data);
            $agent_id = pdo_insertid();
            mlog(5000, $agent_id);
        }
        imessage(error(0, "编辑代理成功"), iurl("agent/agent/list"), "ajax");
    }
}
if ($op == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        pdo_delete("tiny_wmall_agent", array("id" => $id, "uniacid" => $_W["uniacid"]));
        mlog(5001, $id);
    }
    imessage(error(0, "删除代理成功"), "", "ajax");
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $status = intval($_GPC["status"]);
    pdo_update("tiny_wmall_agent", array("status" => $status), array("id" => $id, "uniacid" => $_W["uniacid"]));
    imessage(error(0, ""), "", "ajax");
}
if ($op == "set") {
    $_W["page"]["title"] = "账户设置";
    $id = intval($_GPC["id"]);
    $agent = pdo_get("tiny_wmall_agent", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($agent)) {
        imessage(error(-1, "代理不存在或已删除"), "", "ajax");
    }
    $agent["fee"] = iunserializer($agent["fee"]);
    $gohome = $agent["fee"]["fee_gohome"];
    if ($_W["ispost"]) {
        $form_type = trim($_GPC["form_type"]);
        if ($form_type == "extra") {
            $data = array("amount_min" => floatval($_GPC["amount_min"]));
        } else {
            $fee_takeout = $agent["fee"]["fee_takeout"];
            $takeout_GPC = $_GPC["fee_takeout"];
            $fee_takeout["type"] = intval($takeout_GPC["type"]) ? intval($takeout_GPC["type"]) : 1;
            if ($fee_takeout["type"] == 2) {
                $fee_takeout["fee"] = floatval($takeout_GPC["fee"]);
            } else {
                if ($fee_takeout["type"] == 1) {
                    $fee_takeout["fee_rate"] = floatval($takeout_GPC["fee_rate"]);
                    $fee_takeout["fee_min"] = floatval($takeout_GPC["fee_min"]);
                    $items_yes = array_filter($takeout_GPC["items_yes"], trim);
                    if (empty($items_yes)) {
                imessage(error(-1, "至少选择一项抽佣项目"), "", "ajax");
                    }
                    $fee_takeout["items_yes"] = $items_yes;
                    $items_no = array_filter($takeout_GPC["items_no"], trim);
                    $fee_takeout["items_no"] = $items_no;
                } else {
                    if ($fee_takeout["type"] == 3) {
                        $fee_takeout["fee_rate"] = floatval($takeout_GPC["fee_rate_3"]);
                        $fee_takeout["fee_min"] = floatval($takeout_GPC["fee_min_3"]);
                    }
                }
            }
            $fee_selfDelivery = $agent["fee"]["fee_selfDelivery"];
            $selfDelivery_GPC = $_GPC["fee_selfDelivery"];
            $fee_selfDelivery["type"] = intval($selfDelivery_GPC["type"]) ? intval($selfDelivery_GPC["type"]) : 1;
            if ($fee_selfDelivery["type"] == 2) {
                $fee_selfDelivery["fee"] = floatval($selfDelivery_GPC["fee"]);
            } else {
                if ($fee_selfDelivery["type"] == 1) {
                    $fee_selfDelivery["fee_rate"] = floatval($selfDelivery_GPC["fee_rate"]);
                    $fee_selfDelivery["fee_min"] = floatval($selfDelivery_GPC["fee_min"]);
                    $items_yes = array_filter($selfDelivery_GPC["items_yes"], trim);
                    if (empty($items_yes)) {
                        imessage(error(-1, "至少选择一项抽佣项目"), "", "ajax");
                    }
                    $fee_selfDelivery["items_yes"] = $items_yes;
                    $items_no = array_filter($selfDelivery_GPC["items_no"], trim);
                    $fee_selfDelivery["items_no"] = $items_no;
                } else {
                    if ($fee_selfDelivery["type"] == 3) {
                        $fee_selfDelivery["fee_rate"] = floatval($selfDelivery_GPC["fee_rate_3"]);
                        $fee_selfDelivery["fee_min"] = floatval($selfDelivery_GPC["fee_min_3"]);
                    }
                }
            }
            $fee_instore = $agent["fee"]["fee_instore"];
            $instore_GPC = $_GPC["fee_instore"];
            $fee_instore["type"] = intval($instore_GPC["type"]) ? intval($instore_GPC["type"]) : 1;
            if ($fee_instore["type"] == 2) {
                $fee_instore["fee"] = floatval($instore_GPC["fee"]);
            } else {
                if ($fee_instore["type"] == 1) {
                    $fee_instore["fee_rate"] = floatval($instore_GPC["fee_rate"]);
                    $fee_instore["fee_min"] = floatval($instore_GPC["fee_min"]);
                    $items_yes = array_filter($instore_GPC["items_yes"], trim);
                    if (empty($items_yes)) {
                imessage(error(-1, "至少选择一项抽佣项目"), "", "ajax");
            }
            $fee_instore["items_yes"] = $items_yes;
            $items_no = array_filter($instore_GPC["items_no"], trim);
            $fee_instore["items_no"] = $items_no;
            } else {
                if ($fee_instore["type"] == 3) {
                    $fee_instore["fee_rate"] = floatval($instore_GPC["fee_rate_3"]);
                    $fee_instore["fee_min"] = floatval($instore_GPC["fee_min_3"]);
                    }
                }
            }
            $fee_paybill = $agent["fee"]["fee_paybill"];
            $paybill_GPC = $_GPC["fee_paybill"];
            $fee_paybill["type"] = intval($paybill_GPC["type"]) ? intval($paybill_GPC["type"]) : 1;
            if ($fee_paybill["type"] == 2) {
                $fee_paybill["fee"] = floatval($paybill_GPC["fee"]);
            } else {
                if ($fee_paybill["type"] == 1) {
                    $fee_paybill["fee_rate"] = floatval($paybill_GPC["fee_rate"]);
                    $fee_paybill["fee_min"] = floatval($paybill_GPC["fee_min"]);
                } else {
                    if ($fee_paybill["type"] == 3) {
                        $fee_paybill["fee_rate"] = floatval($paybill_GPC["fee_rate_3"]);
                        $fee_paybill["fee_min"] = floatval($paybill_GPC["fee_min_3"]);
                }
            }
        }
        $fee_errander = $agent["fee"]["fee_errander"];
        $errander_GPC = $_GPC["fee_errander"];
        $fee_errander["type"] = intval($errander_GPC["type"]) ? intval($errander_GPC["type"]) : 1;
        if ($fee_errander["type"] == 2) {
            $fee_errander["fee"] = floatval($errander_GPC["fee"]);
        } else {
            if ($fee_errander["type"] == 1) {
            $fee_errander["fee_rate"] = floatval($errander_GPC["fee_rate"]);
            $fee_errander["fee_min"] = floatval($errander_GPC["fee_min"]);
            $items_yes = array_filter($errander_GPC["items_yes"], trim);
            if (empty($items_yes)) {
                imessage(error(-1, "至少选择一项抽佣项目"), "", "ajax");
            }
            $fee_errander["items_yes"] = $items_yes;
            $items_no = array_filter($errander_GPC["items_no"], trim);
            $fee_errander["items_no"] = $items_no;
            } else {
                if ($fee_errander["type"] == 3) {
                    $fee_errander["fee_rate"] = floatval($errander_GPC["fee_rate_3"]);
                    $fee_errander["fee_min"] = floatval($errander_GPC["fee_min_3"]);
                }
            }
        }
        $fee_period = intval($_GPC["fee_period"]);
        if (check_plugin_perm("gohome")) {
        $kanjia_GPC = $_GPC["kanjia"];
        $kanjia["type"] = intval($kanjia_GPC["type"]);
        if ($kanjia["type"] == 2) {
            $kanjia["fee"] = floatval($kanjia_GPC["fee"]);
        } else {
                if ($kanjia["type"] == 1) {
            $kanjia["fee_rate"] = floatval($kanjia_GPC["fee_rate"]);
            $kanjia["fee_min"] = floatval($kanjia_GPC["fee_min"]);
            $items_yes = array_filter($kanjia_GPC["items_yes"], trim);
            if (empty($items_yes)) {
                imessage(error(-1, "至少选择一项砍价抽成项目"), "", "ajax");
            }
            $kanjia["items_yes"] = $items_yes;
            $items_no = array_filter($kanjia_GPC["items_no"], trim);
            $kanjia["items_no"] = $items_no;
                } else {
                    if ($kanjia["type"] == 3) {
                        $kanjia["fee_rate"] = floatval($kanjia_GPC["fee_rate_3"]);
                        $kanjia["fee_min"] = floatval($kanjia_GPC["fee_min_3"]);
                    }
                }
            }
        $pintuan_GPC = $_GPC["pintuan"];
        $pintuan["type"] = intval($pintuan_GPC["type"]);
        if ($pintuan["type"] == 2) {
            $pintuan["fee"] = floatval($pintuan_GPC["fee"]);
        } else {
                if ($pintuan["type"] == 1) {
            $pintuan["fee_rate"] = floatval($pintuan_GPC["fee_rate"]);
            $pintuan["fee_min"] = floatval($pintuan_GPC["fee_min"]);
            $items_yes = array_filter($pintuan_GPC["items_yes"], trim);
            if (empty($items_yes)) {
                imessage(error(-1, "至少选择一项拼团抽成项目"), "", "ajax");
            }
            $pintuan["items_yes"] = $items_yes;
            $items_no = array_filter($pintuan_GPC["items_no"], trim);
            $pintuan["items_no"] = $items_no;
                } else {
                    if ($pintuan["type"] == 3) {
                        $pintuan["fee_rate"] = floatval($pintuan_GPC["fee_rate_3"]);
                        $pintuan["fee_min"] = floatval($pintuan_GPC["fee_min_3"]);
                    }
                }
            }
        $seckill_GPC = $_GPC["seckill"];
        $seckill["type"] = intval($seckill_GPC["type"]);
        if ($seckill["type"] == 2) {
            $seckill["fee"] = floatval($seckill_GPC["fee"]);
        } else {
                if ($seckill["type"] == 1) {
            $seckill["fee_rate"] = floatval($seckill_GPC["fee_rate"]);
            $seckill["fee_min"] = floatval($seckill_GPC["fee_min"]);
            $items_yes = array_filter($seckill_GPC["items_yes"], trim);
            if (empty($items_yes)) {
                imessage(error(-1, "至少选择一项抢购抽成项目"), "", "ajax");
            }
            $seckill["items_yes"] = $items_yes;
            $items_no = array_filter($seckill_GPC["items_no"], trim);
            $seckill["items_no"] = $items_no;
                } else {
                    if ($seckill["type"] == 3) {
                        $seckill["fee_rate"] = floatval($seckill_GPC["fee_rate_3"]);
                        $seckill["fee_min"] = floatval($seckill_GPC["fee_min_3"]);
                    }
                }
            }
            $haodian_GPC = $_GPC["haodian"];
            $haodian["type"] = intval($haodian_GPC["type"]);
            if ($haodian["type"] == 2) {
                $haodian["fee"] = floatval($haodian_GPC["fee"]);
            } else {
                if ($haodian["type"] == 1) {
                $haodian["fee_rate"] = floatval($haodian_GPC["fee_rate"]);
                $haodian["fee_min"] = floatval($haodian_GPC["fee_min"]);
                $items_yes = array_filter($haodian_GPC["items_yes"], trim);
                if (empty($items_yes)) {
                    imessage(error(-1, "至少选择一项好店入驻抽成项目"), "", "ajax");
                }
                $haodian["items_yes"] = $items_yes;
                $haodian["items_no"] = array();
                } else {
                    if ($haodian["type"] == 3) {
                        $haodian["fee_rate"] = floatval($haodian_GPC["fee_rate_3"]);
                        $haodian["fee_min"] = floatval($haodian_GPC["fee_min_3"]);
                    }
                }
            }
            $tongcheng_GPC = $_GPC["tongcheng"];
            $tongcheng["type"] = intval($tongcheng_GPC["type"]);
            if ($tongcheng["type"] == 2) {
                $tongcheng["fee"] = floatval($tongcheng_GPC["fee"]);
            } else {
                if ($tongcheng["type"] == 1) {
                $tongcheng["fee_rate"] = floatval($tongcheng_GPC["fee_rate"]);
                $tongcheng["fee_min"] = floatval($tongcheng_GPC["fee_min"]);
                $items_yes = array_filter($tongcheng_GPC["items_yes"], trim);
                if (empty($items_yes)) {
                    imessage(error(-1, "至少选择一项同城发帖抽成项目"), "", "ajax");
                }
                $tongcheng["items_yes"] = $items_yes;
                $tongcheng["items_no"] = array();
                } else {
                    if ($tongcheng["type"] == 3) {
                        $tongcheng["fee_rate"] = floatval($tongcheng_GPC["fee_rate_3"]);
                        $tongcheng["fee_min"] = floatval($tongcheng_GPC["fee_min_3"]);
                    }
                }
            }
            $tiezi_GPC = $_GPC["tiezi"];
            $tiezi["type"] = intval($tiezi_GPC["type"]);
            if ($tiezi["type"] == 2) {
                $tiezi["fee"] = floatval($tiezi_GPC["fee"]);
            } else {
                if ($tiezi["type"] == 1) {
                $tiezi["fee_rate"] = floatval($tiezi_GPC["fee_rate"]);
                $tiezi["fee_min"] = floatval($tiezi_GPC["fee_min"]);
                $items_yes = array_filter($tiezi_GPC["items_yes"], trim);
                if (empty($items_yes)) {
                    imessage(error(-1, "至少选择一项帖子置顶抽成项目"), "", "ajax");
                }
                $tiezi["items_yes"] = $items_yes;
                $tiezi["items_no"] = array();
                } else {
                    if ($tiezi["type"] == 3) {
                        $tiezi["fee_rate"] = floatval($tiezi_GPC["fee_rate_3"]);
                        $tiezi["fee_min"] = floatval($tiezi_GPC["fee_min_3"]);
                    }
                }
            }
            $gohome = array("kanjia" => $kanjia, "pintuan" => $pintuan, "seckill" => $seckill, "haodian" => $haodian, "tiezi" => $tongcheng, "tiezi_stick" => $tiezi);
        }
            $fee = array("fee_takeout" => $fee_takeout, "fee_selfDelivery" => $fee_selfDelivery, "fee_instore" => $fee_instore, "fee_paybill" => $fee_paybill, "fee_errander" => $fee_errander, "fee_period" => $fee_period, "fee_gohome" => $gohome);
            $data = array("uniacid" => $_W["uniacid"], "fee" => iserializer($fee));
        }
        pdo_update("tiny_wmall_agent", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
        imessage(error(0, "设置代理账户成功"), "refresh", "ajax");
    }
}
if ($op == "changes") {
    $id = intval($_GPC["id"]);
    $agent = pdo_get("tiny_wmall_agent", array("uniacid" => $_W["uniacid"], "id" => $id));
    if ($_W["ispost"]) {
        $change_type = intval($_GPC["change_type"]);
        $amount = floatval($_GPC["amount"]);
        $remark = trim($_GPC["remark"]);
        $fee = $amount - $agent["amount"];
        if ($change_type == 1) {
            $fee = "+" . $amount;
            $amount = $agent["amount"] + $amount;
        } else {
            if ($change_type == 2) {
                $fee = "-" . $amount;
                $amount = $agent["amount"] - $amount;
                if ($amount < 0) {
                    $amount = 0;
                    $fee = "-" . $agent["amount"];
                }
            }
        }
        mload()->model("agent");
        agent_update_account($id, $fee, 3, "", $remark);
        imessage(error(0, "更改账余额成功"), referer(), "ajax");
    }
    include itemplate("accountOp");
    exit;
}
include itemplate("agent");

?>