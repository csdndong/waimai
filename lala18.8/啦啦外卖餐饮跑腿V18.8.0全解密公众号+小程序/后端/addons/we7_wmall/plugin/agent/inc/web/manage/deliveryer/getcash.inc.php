<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("deliveryer");
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "配送员提现记录";
    $condition = " WHERE uniacid = :uniacid AND agentid = :agentid";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    if (0 < $deliveryer_id) {
        $condition .= " AND deliveryer_id = :deliveryer_id";
        $params[":deliveryer_id"] = $deliveryer_id;
    }
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
    }
    $channel = trim($_GPC["channel"]);
    if (!empty($channel)) {
        if ($channel == "weixin") {
            $condition .= " AND (channel = 'weixin' OR channel = 'wxapp') ";
        } else {
            $condition .= " AND channel = :channel";
            $params[":channel"] = $channel;
        }
    }
    $days = isset($_GPC["days"]) ? intval($_GPC["days"]) : -2;
    $todaytime = strtotime(date("Y-m-d"));
    $starttime = $todaytime;
    $endtime = $starttime + 86399;
    if (-2 < $days) {
        if ($days == -1) {
            $starttime = strtotime($_GPC["addtime"]["start"]);
            $endtime = strtotime($_GPC["addtime"]["end"]);
            $condition .= " AND addtime > :start AND addtime < :end";
            $params[":start"] = $starttime;
            $params[":end"] = $endtime;
        } else {
            $starttime = strtotime("-" . $days . " days", $todaytime);
            $condition .= " and addtime >= :start";
            $params[":start"] = $starttime;
        }
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_deliveryer_getcash_log") . $condition, $params);
    $records = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_deliveryer_getcash_log") . $condition . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($records)) {
        $toaccount_status_arr = getcash_toaccount_status();
        foreach ($records as &$row) {
            $row["account"] = iunserializer($row["account"]);
            if ($row["channel"] == "weixin" || $row["channel"] == "wxapp") {
                $row["channel_cn"] = "打款到微信零钱";
            } else {
                if ($row["channel"] == "alipay") {
                    $row["channel_cn"] = "打款到支付宝余额";
                } else {
                    if ($row["channel"] == "bank") {
                        $row["channel_cn"] = "打款到银行卡";
                    }
                }
            }
        }
    }
    $pager = pagination($total, $pindex, $psize);
    $deliveryers = deliveryer_all(true);
    $channels = getcash_channels();
}
include itemplate("deliveryer/getcash");

?>