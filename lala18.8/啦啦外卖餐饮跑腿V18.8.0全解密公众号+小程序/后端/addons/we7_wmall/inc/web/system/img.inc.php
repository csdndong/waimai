<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
set_time_limit(0);
load()->func("file");
$goods_img = pdo_fetchall("select * from" . tablename("tiny_wmall_goods") . "where imgstatus = 0 order by id asc limit 3", array());
if (!empty($goods_img)) {
    foreach ($goods_img as $value) {
        $value["img"] = "http://waimai.dsjax.com/upload/" . $value["thumb"];
        $img = ihttp_get($value["img"]);
        if (is_error($img)) {
            continue;
        }
        $content = $img["content"];
        $name = ifile_write($content, "", true);
        if (is_error($name)) {
            continue;
        }
        pdo_update("tiny_wmall_goods", array("thumb" => $name, "imgstatus" => 1), array("id" => $value["id"]));
    }
}
echo "ddd";
exit;
function ifile_write($content, $name = "", $remote = false)
{
    global $_W;
    if (empty($name)) {
        $name = "/images/" . $_W["uniacid"] . "/" . date("Y/m/") . random(30) . ".jpg";
    }
    $filename = ATTACHMENT_ROOT . "/" . $name;
    mkdirs(dirname($filename));
    file_put_contents($filename, $content);
    @chmod($filename, $_W["config"]["setting"]["filemode"]);
    if (!is_file($filename)) {
        return error(-1, "保存图片失败");
    }
    if ($remote || !empty($_W["setting"]["remote"]["type"])) {
        $status = file_remote_upload($name);
        if (is_error($status)) {
            return error(-1, "上传到远程失败");
        }
    }
    return $name;
}

?>