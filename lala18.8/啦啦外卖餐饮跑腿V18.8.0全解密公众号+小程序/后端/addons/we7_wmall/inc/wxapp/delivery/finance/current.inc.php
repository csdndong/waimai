<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " WHERE uniacid = :uniacid AND deliveryer_id = :deliveryer_id";
    $params = array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $_deliveryer["id"]);
    $trade_type = intval($_GPC["trade_type"]);
    if (0 < $trade_type) {
        $condition .= " and trade_type = :trade_type";
        $params[":trade_type"] = $trade_type;
    }
    $now_month = date("Y-m");
    $stat_month = trim($_GPC["stat_month"]) ? trim($_GPC["stat_month"]) : $now_month;
    $stat_month = str_replace("-", "", $stat_month);
    $condition .= " and stat_month = :stat_month";
    $params[":stat_month"] = $stat_month;
    $pindex = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]);
    $records = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_deliveryer_current_log") . $condition . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($records)) {
        foreach ($records as &$row) {
            if ($row["trade_type"] == 1) {
                $row["trade_type_cn"] = "配送费入账";
            } else {
                if ($row["trade_type"] == 2) {
                    $row["trade_type_cn"] = "申请提现";
                } else {
                    $row["trade_type_cn"] = "其他变动";
                }
            }
            $row["addtime_cn"] = date("Y-m-d H:i", $row["addtime"]);
        }
    }
    $result = array("records" => $records, "stat" => array("start" => "2016-01-01", "end" => date("Y-m-d"), "now" => $now_month));
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "detail") {
        $id = intval($_GPC["id"]);
        $current = pdo_get("tiny_wmall_deliveryer_current_log", array("uniacid" => $_W["uniacid"], "id" => $id));
        if (empty($current)) {
            imessage(error(-1, "交易记录不存在"), "", "ajax");
        }
        if ($current["trade_type"] == 2) {
            $getcash_log = pdo_get("tiny_wmall_deliveryer_getcash_log", array("uniacid" => $_W["uniacid"], "id" => $current["extra"]));
            $getcash_log["addtime_cn"] = date("Y-m-d H:i", $getcash_log["addtime"]);
        }
        $current["addtime_cn"] = date("Y-m-d H:i", $current["addtime"]);
        $trade_types = array(1 => array("text" => "配送费入账"), 2 => array("text" => "申请提现"), 3 => array("text" => "其他变动"));
        $current["trade_type_cn"] = $trade_types[$current["trade_type"]]["text"];
        $result = array("current" => $current, "getcash_log" => $getcash_log);
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($ta == "vue_list") {
            $condition = " WHERE uniacid = :uniacid AND deliveryer_id = :deliveryer_id";
            $params = array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $_deliveryer["id"]);
            $trade_type = intval($_GPC["trade_type"]);
            if (0 < $trade_type) {
                $condition .= " and trade_type = :trade_type";
                $params[":trade_type"] = $trade_type;
            }
            $pindex = max(1, intval($_GPC["page"]));
            $psize = intval($_GPC["psize"]);
            $records = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_deliveryer_current_log") . $condition . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
            if (!empty($records)) {
                foreach ($records as &$row) {
                    if ($row["trade_type"] == 1) {
                        $row["trade_type_cn"] = "配送费入账";
                    } else {
                        if ($row["trade_type"] == 2) {
                            $row["trade_type_cn"] = "申请提现";
                        } else {
                            $row["trade_type_cn"] = "其他变动";
                        }
                    }
                    $row["addtime_cn"] = date("Y-m-d H:i", $row["addtime"]);
                }
            }
            $result = array("records" => $records);
            imessage(error(0, $result), "", "ajax");
        }
    }
}

?>