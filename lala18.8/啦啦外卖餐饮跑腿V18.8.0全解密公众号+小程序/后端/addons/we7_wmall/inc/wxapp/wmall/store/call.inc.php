<?php
/*

 
 
 * 源码仅供研究学习，请勿用于商业用途
 */

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$sid = intval($_GPC["sid"]);
$store = store_fetch($sid);
if (empty($store)) {
    imessage(error(-1, "门店不存在或已经删除"), "", "ajax");
}
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $table_id = intval($_GPC["table_id"]);
    $update = array("uniacid" => $_W["uniacid"], "sid" => $sid, "status" => 0, "table_id" => $table_id, "addtime" => TIMESTAMP);
    pdo_insert("tiny_wmall_table_call_record", $update);
    $id = pdo_insertid();
    mload()->model("table");
    $result = call_notice_clerk($id);
    if (is_error($result)) {
        imessage(error(-1, "呼叫服务员失败"), "", "ajax");
    }
    imessage(error(0, "呼叫服务员成功，请稍等"), "", "ajax");
}

?>