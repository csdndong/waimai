<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("deliveryer");
mload()->model("deliveryer.extra");
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "配送员提现记录";
    $condition = " WHERE uniacid = :uniacid";
    $params[":uniacid"] = $_W["uniacid"];
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    if (0 < $deliveryer_id) {
        $condition .= " AND deliveryer_id = :deliveryer_id";
        $params[":deliveryer_id"] = $deliveryer_id;
    }
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
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
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
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
} else {
    if ($op == "transfers") {
        $id = intval($_GPC["id"]);
        $result = deliveryer_getcash_update($id, "transfers");
        imessage($result, "", "ajax");
    } else {
        if ($op == "status") {
            $id = intval($_GPC["id"]);
            $status = intval($_GPC["status"]);
            $result = deliveryer_getcash_update($id, "status", array("status" => $status));
            imessage($result, "", "ajax");
        } else {
            if ($op == "cancel") {
                $id = intval($_GPC["id"]);
                $log = pdo_get("tiny_wmall_deliveryer_getcash_log", array("uniacid" => $_W["uniacid"], "id" => $id));
                if ($_W["ispost"]) {
                    $remark = trim($_GPC["remark"]);
                    $result = deliveryer_getcash_update($log, "cancel", array("remark" => $remark));
                    imessage($result, referer(), "ajax");
                }
                $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $log["deliveryer_id"]));
                include itemplate("deliveryer/plateformOp");
                exit;
            }
            if ($op == "query") {
                $id = intval($_GPC["id"]);
                $result = deliveryer_getcash_update($id, "query");
                imessage($result, "", "ajax");
            }
        }
    }
}
include itemplate("deliveryer/getcash");

?>