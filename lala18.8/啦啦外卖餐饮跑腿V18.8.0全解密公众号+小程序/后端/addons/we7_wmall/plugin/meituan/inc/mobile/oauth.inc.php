<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "storemap";
$sid = intval($_GPC["state"]);
if (empty($sid)) {
    imessage("店铺id不能为空", "", "info");
}
pload()->classs("meituan");
$app = new Meituan($sid);
if ($op == "storemap") {
    $url = $app->getStoremapUrl();
} else {
    if ($op == "releasebinding") {
        $config_meituan = store_get_data($sid, "meituan");
        if (empty($config_meituan["basic"]["status"])) {
            imessage("您还没有进行美团对接,不能进行解绑操作", "", "error");
        }
        $url = $app->getReleasebindingUrl();
    }
}
if (is_error($url)) {
    imessage("获取url失败:" . $url["message"], "", "error");
}
header("Location: " . $url);
exit;

?>