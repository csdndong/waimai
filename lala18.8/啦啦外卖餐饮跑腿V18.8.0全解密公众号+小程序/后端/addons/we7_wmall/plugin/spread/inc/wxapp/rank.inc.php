<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $rank = get_plugin_config("spread.rank");
    if (empty($rank["status"])) {
        imessage(error(-1, "排行榜未开启"), "", "ajax");
    }
    $user = $_W["member"];
    $user["avatar"] = tomedia($user["avatar"]);
    $count_final_fee = pdo_fetchcolumn("select sum(final_fee) from" . tablename("tiny_wmall_spread_getcash_log") . "where uniacid = :uniacid and status = 1 and spreadid = :spreadid", array(":uniacid" => $_W["uniacid"], ":spreadid" => $_W["member"]["uid"]));
    if (empty($count_final_fee)) {
        $count_final_fee = 0;
    }
    if ($rank["type"] == 2) {
        $rank["infomation"] = array_sort($rank["infomation"], "commission", SORT_DESC);
        foreach ($rank["infomation"] as &$v) {
            $v["avatar"] = tomedia($v["avatar"]);
        }
        $result = array("member" => $user, "list" => $rank["infomation"], "rank" => $rank, "count_final_fee" => $count_final_fee);
        imessage(error(0, $result), "", "ajax");
        return 1;
    } else {
        $num = intval($rank["num"]);
        if (!$num) {
            $num = 300;
        }
        $pindex = max(1, intval($_GPC["min"]));
        $psize = 8;
        $count = 1;
        if ($rank["type"] == 1) {
            $temp = pdo_fetchall("select sum(final_fee) as final_fee from " . tablename("tiny_wmall_spread_getcash_log") . " where uniacid = :uniacid and status = 1 group by spreadid order by final_fee desc limit " . $num, array(":uniacid" => $_W["uniacid"]));
            foreach ($temp as $val) {
                if ($count_final_fee < $val["final_fee"]) {
                    $count++;
                }
            }
            $getcash = pdo_fetchall("select sum(final_fee) as final_fee,spreadid from" . tablename("tiny_wmall_spread_getcash_log") . "where uniacid = :uniacid and status =1 group by spreadid order by final_fee desc LIMIT " . ($pindex - 1) * $psize . "," . $psize, array(":uniacid" => $_W["uniacid"]));
        } else {
            if ($rank["type"] == 0) {
                $final_fee = pdo_fetchcolumn("select sum(fee) from" . tablename("tiny_wmall_spread_current_log") . "where uniacid = :uniacid and trade_type = 1 and spreadid = :spreadid", array(":uniacid" => $_W["uniacid"], ":spreadid" => $_W["member"]["uid"]));
                $temp = pdo_fetchall("select sum(fee) as final_fee from" . tablename("tiny_wmall_spread_current_log") . "where uniacid = :uniacid and trade_type = 1 group by spreadid order by final_fee desc limit " . $num, array(":uniacid" => $_W["uniacid"]));
                foreach ($temp as $val) {
                    if ($final_fee < $val["final_fee"]) {
                        $count++;
                    }
                }
                $getcash = pdo_fetchall("select sum(fee) as final_fee,spreadid from" . tablename("tiny_wmall_spread_current_log") . "where uniacid = :uniacid and trade_type =1 group by spreadid order by final_fee desc LIMIT " . ($pindex - 1) * $psize . "," . $psize, array(":uniacid" => $_W["uniacid"]));
            }
        }
        if (30 < $pindex) {
            $getcash = array();
        }
        $min = 0;
        $basic = ($pindex - 1) * $psize;
        if (!empty($getcash)) {
            foreach ($getcash as &$v) {
                $v["i"] = ++$basic;
                $member = pdo_fetch("select avatar,nickname from" . tablename("tiny_wmall_members") . "where uniacid = :uniacid and uid = :uid", array(":uniacid" => $_W["uniacid"], ":uid" => $v["spreadid"]));
                $v["nickname"] = $member["nickname"];
                $v["avatar"] = $member["avatar"];
            }
            $min = $pindex + 1;
        }
        if (empty($final_fee)) {
            $final_fee = 0;
        }
        $getcash = array_values($getcash);
        $result = array("member" => $user, "list" => $getcash, "final_fee" => $final_fee, "count" => $count, "count_final_fee" => $count_final_fee, "rank" => $rank);
        $respon = array("errno" => 0, "message" => $result, "min" => $min);
        imessage($respon, "", "ajax");
    }
}

?>
