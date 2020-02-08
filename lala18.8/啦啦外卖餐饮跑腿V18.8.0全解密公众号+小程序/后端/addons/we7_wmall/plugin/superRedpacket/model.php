<?php
defined("IN_IA") or exit("Access Denied");
function superRedpacket_grant_show()
{
    global $_W;
    $gift_redpacket = pdo_get("tiny_wmall_superredpacket", array("uniacid" => $_W["uniacid"], "type" => "gift", "status" => 1));
    if (!empty($gift_redpacket)) {
        $can_grant = 1;
        $today = date("Ymd");
        $uid = $openid = "";
        if ($gift_redpacket["starttime"] <= TIMESTAMP && TIMESTAMP < $gift_redpacket["endtime"]) {
            $gift_redpacket["data"] = json_decode(base64_decode($gift_redpacket["data"]), true);
            $gift_redpacket["data"]["page"]["image"] = tomedia($gift_redpacket["data"]["page"]["image"]);
            $grant_object = iunserializer($gift_redpacket["grant_object"]);
            $gift_params = $gift_redpacket["data"]["params"];
            if ($grant_object[$today]["success"] < $gift_params["everyday_nums"]) {
                $start_hour = strtotime($gift_params["start_hour"]);
                $end_hour = strtotime($gift_params["end_hour"]);
                if ($start_hour <= TIMESTAMP && TIMESTAMP < $end_hour) {
                    if (!empty($_W["member"]["uid"])) {
                        $uid = $_W["member"]["uid"];
                        $is_exist = pdo_fetch("select id, granttime from " . tablename("tiny_wmall_activity_redpacket_record") . " where uniacid = :uniacid and type = :type and uid = :uid and activity_id = :activity_id order by id desc", array(":uniacid" => $_W["uniacid"], ":type" => "gift", ":uid" => $uid, ":activity_id" => $gift_redpacket["id"]));
                    } else {
                        if (!empty($_W["openid"])) {
                            $openid = $_W["openid"];
                            $is_exist = pdo_fetch("select id, granttime from " . tablename("tiny_wmall_activity_redpacket_record") . " where uniacid = :uniacid and type = :type and openid = :openid and activity_id = :activity_id order by id desc", array(":uniacid" => $_W["uniacid"], ":type" => "gift", ":openid" => $openid, ":activity_id" => $gift_redpacket["id"]));
                        }
                    }
                    if (!empty($is_exist)) {
                        if ($gift_params["grant_num_limit"] == "one_all") {
                            $can_grant = 0;
                        } else {
                            $grant_day = date("Ymd", $is_exist["granttime"]);
                            if ($today == $grant_day) {
                                $can_grant = 0;
                            }
                        }
                    }
                    if ($can_grant == 1) {
                        foreach ($gift_redpacket["data"]["redpackets"] as $redpacket) {
                            if (empty($redpacket["times"])) {
                                $redpacket["times"] = array();
                            }
                            $params = array("title" => $redpacket["name"], "activity_id" => $gift_redpacket["id"], "uid" => $uid, "openid" => $openid, "channel" => "superRedpacket", "type" => "gift", "discount" => $redpacket["discount"], "condition" => $redpacket["condition"], "grant_days_effect" => $redpacket["grant_days_effect"], "days_limit" => $redpacket["use_days_limit"], "is_show" => 1, "scene" => $redpacket["scene"], "order_type_limit" => $redpacket["order_type_limit"]);
                            $times_limit = array();
                            if (!empty($redpacket["times"])) {
                                foreach ($redpacket["times"] as $time) {
                                    if ($time["start_hour"] && $time["end_hour"]) {
                                        $times_limit[] = $time;
                                    }
                                }
                            }
                            if (!empty($times_limit)) {
                                $params["times_limit"] = iserializer($times_limit);
                            }
                            $category_limit = array();
                            if (!empty($redpacket["categorys"])) {
                                foreach ($redpacket["categorys"] as $category) {
                                    $category_limit[] = $category["id"];
                                }
                            }
                            $params["category_limit"] = implode("|", $category_limit);
                            mload()->model("redPacket");
                            redPacket_grant($params, false);
                        }
                        $grant_object[$today]["grant_success"]++;
                        $grant_object["total"]++;
                        if (!empty($uid)) {
                            $redpackets = pdo_fetchall("select * from " . tablename("tiny_wmall_activity_redpacket_record") . " where uniacid = :uniacid and type = :type and uid = :uid and activity_id = :activity_id and grantday = :grantday", array(":uniacid" => $_W["uniacid"], ":type" => "gift", ":uid" => $uid, ":activity_id" => $gift_redpacket["id"], ":grantday" => $today));
                        } else {
                            if (!empty($openid)) {
                                $redpackets = pdo_fetchall("select * from " . tablename("tiny_wmall_activity_redpacket_record") . " where uniacid = :uniacid and type = :type and openid = :openid and activity_id = :activity_id and grantday = :grantday", array(":uniacid" => $_W["uniacid"], ":type" => "gift", ":openid" => $openid, ":activity_id" => $gift_redpacket["id"], ":grantday" => $today));
                            }
                        }
                        pdo_update("tiny_wmall_superredpacket", array("grant_object" => iserializer($grant_object)), array("id" => $gift_redpacket["id"], "uniacid" => $_W["uniacid"]));
                        if (!empty($redpackets)) {
                            foreach ($redpackets as &$row) {
                                $row["discount"] = floatval($row["discount"]);
                                $row["condition"] = floatval($row["condition"]);
                                $row["use_days_limit_text"] = date("Y-m-d", $row["starttime"]) . "~" . date("Y-m-d", $row["endtime"]) . "有效";
                            }
                            $data = array("page" => $gift_redpacket["data"]["page"], "redpackets" => $redpackets);
                            return error(0, $data);
                        }
                    }
                }
            }
        }
    }
    $activity_id = pdo_fetchcolumn("select activity_id from " . tablename("tiny_wmall_activity_redpacket_record") . " where uniacid = :uniacid and uid = :uid and channel = :channel and type = :type and status = 1 and is_show = 0", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":channel" => "superRedpacket", ":type" => "grant"));
    if (!empty($activity_id)) {
        $superRedpacket = pdo_get("tiny_wmall_superredpacket", array("uniacid" => $_W["uniacid"], "id" => $activity_id));
        if (empty($superRedpacket)) {
            return error(-1, "发放红包活动不存在2");
        }
        $superRedpacket["data"] = json_decode(base64_decode($superRedpacket["data"]), true);
        $superRedpacket["data"]["page"]["image"] = tomedia($superRedpacket["data"]["page"]["image"]);
        $redpackets = pdo_fetchall("select * from " . tablename("tiny_wmall_activity_redpacket_record") . " where uniacid = :uniacid and uid = :uid and channel = :channel and activity_id = :activity_id and status = 1 and is_show = 0", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":channel" => "superRedpacket", ":activity_id" => $activity_id));
        if (!empty($redpackets)) {
            foreach ($redpackets as &$row) {
                $row["discount"] = floatval($row["discount"]);
                $row["condition"] = floatval($row["condition"]);
                $row["use_days_limit_text"] = date("Y-m-d", $row["starttime"]) . "~" . date("Y-m-d", $row["endtime"]) . "有效";
            }
            $data = array("page" => $superRedpacket["data"]["page"], "redpackets" => $redpackets);
            pdo_update("tiny_wmall_activity_redpacket_record", array("is_show" => 1), array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "channel" => "superRedpacket", "activity_id" => $activity_id));
            return error(0, $data);
        }
    }
    $showcoupons = array();
    $homeshowcoupon = get_plugin_config("superRedpacket.coupon");
    $couponshowsids = $homeshowcoupon["store"]["sids"];
    $couponshowstore = $homeshowcoupon["store"]["stores"];
    if (!empty($couponshowsids)) {
        mload()->model("coupon");
        foreach ($couponshowsids as $sid) {
            $coupon = coupon_collect($sid);
            if (!is_error($coupon) && !empty($coupon["max"])) {
                $coupon["max"]["store_title"] = $couponshowstore[$sid]["title"];
                $coupon["max"]["store_logo"] = tomedia($couponshowstore[$sid]["logo"]);
                $coupon["max"]["use_days_limit"] = ($coupon["max"]["endtime"] - $coupon["max"]["granttime"]) / 86400;
                $showcoupons[] = $coupon["max"];
            }
        }
        if (!empty($showcoupons)) {
            $homeshowcoupon["page"]["params"]["image"] = tomedia($homeshowcoupon["page"]["params"]["image"]);
            $data = array("type" => "coupon", "page" => $homeshowcoupon["page"], "redpackets" => $showcoupons);
            return error(0, $data);
        }
    }
    return error(-1, "");
}
function superRedpacket_share_insert($order_id)
{
    global $_W;
    $order = order_fetch($order_id);
    if (empty($order)) {
        return error(-1, "订单不存在");
    }
    $activity = pdo_get("tiny_wmall_superredpacket", array("uniacid" => $_W["uniacid"], "type" => "share", "status" => 1));
    if (empty($activity)) {
        return error(-1, "没有开启的分享超级红包活动");
    }
    if ($order["total_fee"] < $activity["condition"]) {
        return error(-1, "没有达到分享超级红包的条件");
    }
    $activity["data"] = json_decode(base64_decode($activity["data"]), true);
    $packet_num = rand($activity["data"]["activity"]["packet_min_num"], $activity["data"]["activity"]["packet_max_num"]);
    $insert = array("uniacid" => $_W["uniacid"], "uid" => $order["uid"], "order_id" => $order_id, "activity_id" => $activity["id"], "packet_dosage" => $packet_num, "packet_total" => $packet_num, "addtime" => TIMESTAMP);
    pdo_insert("tiny_wmall_superredpacket_grant", $insert);
    return true;
}
function superRedpacket_share_grant($order_id)
{
    global $_W;
    $grant = pdo_get("tiny_wmall_superredpacket_grant", array("uniacid" => $_W["uniacid"], "order_id" => $order_id));
    if (empty($grant)) {
        return error(-1, "分享红包不存在");
    }
    if ($grant["packet_dosage"] <= 0) {
        return error(-1, "红包已发放完毕");
    }
    $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $grant["uid"]));
    if (empty($member)) {
        return error(-1, "分享人不存在");
    }
    $activity = pdo_get("tiny_wmall_superredpacket", array("uniacid" => $_W["uniacid"], "id" => $grant["activity_id"], "type" => "share", "status" => 1));
    if (empty($activity)) {
        return error(-1, "分享超级红包活动不存在或已结束");
    }
    $activity["data"] = json_decode(base64_decode($activity["data"]), true);
    $redpackets = pdo_fetchall("select * from " . tablename("tiny_wmall_superredpacket_share") . " where uniacid = :uniacid and activity_id = :activity_id and nums > 0", array(":uniacid" => $_W["uniacid"], ":activity_id" => $grant["activity_id"]), "id");
    if (empty($redpackets)) {
        return error(-1, "没有可发放的红包");
    }
    $redpacket_grant_num = rand($activity["data"]["activity"]["redpacket_min_num"], $activity["data"]["activity"]["redpacket_max_num"]);
    mload()->model("redPacket");
    $ids = array_keys($redpackets);
    if ($redpacket_grant_num < count($redpackets)) {
        $ids = array_rand($redpackets, $redpacket_grant_num);
    }
    if (!empty($ids)) {
        foreach ($ids as $id) {
            $params = array("uniacid" => $_W["uniacid"], "activity_id" => $activity["id"], "super_share_id" => $grant["id"], "title" => $redpackets[$id]["title"], "channel" => "superRedpacket", "type" => "share", "uid" => $_W["member"]["uid"], "discount" => $redpackets[$id]["discount"], "condition" => $redpackets[$id]["condition"], "days_limit" => $redpackets[$id]["use_days_limit"], "grant_days_effect" => $redpackets[$id]["grant_days_effect"], "category_limit" => $redpackets[$id]["category_limit"], "times_limit" => $redpackets[$id]["times_limit"]);
            $status = redPacket_grant($params, false);
            $nums = $redpackets[$id]["nums"] - 1;
            pdo_update("tiny_wmall_superredpacket_share", array("nums" => $nums), array("uniacid" => $_W["uniacid"], "id" => $id));
        }
        $packet_dosage = $grant["packet_dosage"] - 1;
        pdo_update("tiny_wmall_superredpacket_grant", array("packet_dosage" => $packet_dosage), array("uniacid" => $_W["uniacid"], "id" => $grant["id"]));
    }
    return true;
}
function superRedpacket_cron()
{
    global $_W;
    pdo_query("update " . tablename("tiny_wmall_superredpacket") . " set status = 0 where uniacid = :uniacid and status = 1 and type = :type and (endtime < :time or starttime > :time)", array(":uniacid" => $_W["uniacid"], ":type" => "share", ":time" => TIMESTAMP));
    pdo_query("update " . tablename("tiny_wmall_superredpacket") . " set status = 1 where uniacid = :uniacid and status = 0 and type = :type and (endtime > :time and starttime < :time)", array(":uniacid" => $_W["uniacid"], ":type" => "share", ":time" => TIMESTAMP));
    return true;
}

?>