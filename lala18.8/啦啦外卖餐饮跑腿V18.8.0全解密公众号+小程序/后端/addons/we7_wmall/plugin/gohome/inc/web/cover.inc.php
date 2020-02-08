<?php
/*
 * @169170
 * @tb@开源学习用
 * @ 仅供学习，商业使用后果自负
 * @ 谢谢
 */

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("cover");
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "kanjia";
$routers = array("gohome" => array("title" => "生活圈首页入口", "url" => ivurl("/gohome/pages/home/index", array(), true), "do" => "gohome"), "kanjia" => array("title" => "砍价入口", "url" => ivurl("/gohome/pages/kanjia/index", array(), true), "do" => "kanjia"), "pintuan" => array("title" => "拼团入口", "url" => ivurl("/gohome/pages/pintuan/index", array(), true), "do" => "pintuan"), "seckill" => array("title" => "抢购入口", "url" => ivurl("/gohome/pages/seckill/index", array(), true), "do" => "seckill"), "tongcheng" => array("title" => "同城首页入口", "url" => ivurl("/gohome/pages/tongcheng/index", array(), true), "do" => "tongcheng"), "haodian" => array("title" => "好店首页入口", "url" => ivurl("/gohome/pages/haodian/index", array(), true), "do" => "haodian"), "haodian_settle" => array("title" => "好店入驻入口", "url" => ivurl("/gohome/pages/haodian/settle", array(), true), "do" => "haodian_settle"));
$router = $routers[$op];
$_W["page"]["title"] = $router["title"];
if ($_W["ispost"]) {
    $keyword = trim($_GPC["keyword"]) ? trim($_GPC["keyword"]) : imessage(error(-1, "关键词不能为空"), "", "ajax");
    $cover = array("keyword" => trim($_GPC["keyword"]), "title" => trim($_GPC["title"]), "thumb" => trim($_GPC["thumb"]), "description" => trim($_GPC["description"]), "do" => $router["do"], "url" => $router["url"], "status" => intval($_GPC["status"]));
    cover_build($cover);
    imessage(error(0, "设置封面成功"), referer(), "ajax");
}
$cover = cover_fetch(array("do" => $router["do"]));
$cover = array_merge($cover, $router);
include itemplate("cover");

?>