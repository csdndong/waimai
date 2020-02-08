<?php
defined("IN_IA") or exit("Access Denied");
function statistics_store()
{
    global $_W;
    $total_num = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_store") . "where uniacid = :uniacid and (status = 1 or status = 0)", array(":uniacid" => $_W["uniacid"])));
    $total_amount = floatval(pdo_fetchcolumn("select round(sum(amount), 2) from " . tablename("tiny_wmall_store_account") . "where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"])));
    $total_getcash = floatval(pdo_fetchcolumn("select round(sum(get_fee), 2) from " . tablename("tiny_wmall_store_getcash_log") . "where uniacid = :uniacid and status = 2", array(":uniacid" => $_W["uniacid"])));
    return array("total_num" => $total_num, "total_amount" => $total_amount, "total_getcash" => $total_getcash);
}
function statistics_deliveryer()
{
    global $_W;
    $total_num = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_deliveryer") . "where uniacid = :uniacid and status = 1 ", array(":uniacid" => $_W["uniacid"])));
    $total_credit2 = floatval(pdo_fetchcolumn("select round(sum(credit2), 2) from " . tablename("tiny_wmall_deliveryer") . "where uniacid = :uniacid and status = 1 ", array(":uniacid" => $_W["uniacid"])));
    $total_getcash = floatval(pdo_fetchcolumn("select round(sum(get_fee), 2) from " . tablename("tiny_wmall_deliveryer_getcash_log") . "where uniacid = :uniacid and status = 2", array(":uniacid" => $_W["uniacid"])));
    return array("total_num" => $total_num, "total_credit2" => $total_credit2, "total_getcash" => $total_getcash);
}
function statistics_member()
{
    global $_W;
    $total_num = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_members") . "where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"])));
    $total_credit1 = floatval(pdo_fetchcolumn("select round(sum(credit1), 2) from " . tablename("tiny_wmall_members") . "where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"])));
    $total_credit2 = floatval(pdo_fetchcolumn("select round(sum(credit2), 2) from " . tablename("tiny_wmall_members") . "where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"])));
    return array("total_num" => $total_num, "total_credit1" => $total_credit1, "total_credit2" => $total_credit2);
}

?>