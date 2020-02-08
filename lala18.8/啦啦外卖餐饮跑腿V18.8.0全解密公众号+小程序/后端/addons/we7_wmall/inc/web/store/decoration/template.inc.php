<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $_W["page"]["title"] = "页面配置";
    $template = store_get_data($sid, "wxapp.template");
    $template_page = store_get_data($sid, "wxapp.template_page");
    if ($_W["ispost"]) {
        $type = trim($_GPC["type"]);
        if ($type == "template") {
            $value = intval($_GPC["value"]);
            if (!check_plugin_perm("diypage") && in_array($value, array(4, 5))) {
                $value = 2;
            }
            store_set_data($sid, "wxapp.template", $value);
            imessage(error(0, "商品列表单/双列设置成功"), referer(), "ajax");
        } else {
            $value = array("wxapp" => intval($_GPC["template_page"]["wxapp"]), "vue" => intval($_GPC["template_page"]["vue"]));
            store_set_data($sid, "wxapp.template_page", $value);
            imessage(error(0, "商品列表页风格设置成功"), referer(), "ajax");
        }
    }
}
include itemplate("store/decoration/template");

?>