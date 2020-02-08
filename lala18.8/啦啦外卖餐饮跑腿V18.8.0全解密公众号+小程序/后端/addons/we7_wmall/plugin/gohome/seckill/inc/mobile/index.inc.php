<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$_W["page"]["title"] = "限时抢购";
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
$task = seckill_fetch(4, true);
if ($op == "detail") {
    $id = intval($_GPC["id"]);
    mload()->model("goods");
    $goods = goods_fetch($id);
    if (is_error($goods)) {
        imessage(error(-1, "商品不存在或已删除"), "", "ajax");
    }
    imessage(error(0, $goods), "", "ajax");
}
include itemplate("index");

?>