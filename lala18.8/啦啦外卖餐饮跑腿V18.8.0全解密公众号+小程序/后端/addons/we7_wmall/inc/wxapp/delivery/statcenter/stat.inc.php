<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $stat = deliveryer_stat_order();
    if (is_error($stat)) {
        imessage($stat, "", "ajax");
    }
    imessage(error(0, $stat), "", "ajax");
} else {
    if ($ta == "rank") {
        $params = array("type" => trim($_GPC["type"]), "deliveryer_id" => $_W["deliveryer"]["id"], "sort_type" => trim($_GPC["sort_type"]));
        $rank = deliveryer_takeout_rank($params);
        if (is_error($rank)) {
            imessage($rank, "", "ajax");
        }
        imessage(error(0, $rank), "", "ajax");
    } else {
        if ($ta == "rank_errander") {
            $params = array("type" => trim($_GPC["type"]), "deliveryer_id" => $_W["deliveryer"]["id"], "sort_type" => trim($_GPC["sort_type"]));
            $rank = deliveryer_errander_rank($params);
            if (is_error($rank)) {
                imessage($rank, "", "ajax");
            }
            imessage(error(0, $rank), "", "ajax");
        } else {
            if ($ta == "stat_vue") {
                $type = trim($_GPC["type"]);
                $stat = deliveryer_stat_order1($type);
                $result = array("stat" => $stat);
                imessage(error(0, $result), "", "ajax");
            } else {
                if ($ta == "detail") {
                    $type = trim($_GPC["type"]);
                    $result = array("records" => deliveryer_stat_detail($type));
                    imessage(error(0, $result), "", "ajax");
                }
            }
        }
    }
}

?>