<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
icheckauth();
if ($_config_plugin["basic"]["status"] != 1) {
    imessage(error(-1, "超级会员功能未开启"), "", "ajax");
}
if ($op == "index") {
    $filter = $_GPC;
    $filter["status"] = 1;
    $data = svip_goods_getall($filter);
    $result = array("goods" => $data["goods"]);
    imessage(error(0, $result), "", "ajax");
}

?>