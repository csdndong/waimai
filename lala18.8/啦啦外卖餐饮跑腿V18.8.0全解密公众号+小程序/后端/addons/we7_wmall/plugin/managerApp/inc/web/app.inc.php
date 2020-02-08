<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "app";
$_config = get_system_config("app");
$downurls = array("manager" => array("ios" => MODULE_URL . "resource/apps/" . $_W["uniacid"] . "/ios/manager.apk", "android" => MODULE_URL . "resource/apps/" . $_W["uniacid"] . "/android/manager.apk", "apk" => MODULE_ROOT . "/resource/apps/" . $_W["uniacid"] . "/android/manager.apk"));
if ($op == "app") {
    $_W["page"]["title"] = "商家app设置";
    load()->func("file");
    $path = "resource/mp3/" . $_W["uniacid"] . "/";
    mkdirs(MODULE_ROOT . "/" . $path);
    $files = array();
    if ($_W["ispost"]) {
        if ($_GPC["form_type"] == "setting_app") {
        foreach ($_FILES as $key => $val) {
            if (!empty($val["name"]) && $val["error"] == 0) {
                $pathinfo = pathinfo($val["name"]);
                $ext = strtolower($pathinfo["extension"]);
                if ($ext != "mp3") {
                    imessage(error(-1, "仅支持mp3类型的语音文件"), referer(), "ajax");
                }
                $basename = (string) $key . "." . $ext;
                if (!file_move($val["tmp_name"], MODULE_ROOT . "/" . $path . $basename)) {
                    imessage(error(-1, "保存上传文件失败"), referer(), "ajax");
                }
                $files[$key] = $basename;
            }
            if (empty($files[$key])) {
                $files[$key] = $_config["manager"]["phonic"][$key];
            }
        }
        $data = array("serial_sn" => trim($_GPC["manager"]["serial_sn"]), "push_key" => trim($_GPC["manager"]["push_key"]), "push_secret" => trim($_GPC["manager"]["push_secret"]), "ios_build_type" => intval($_GPC["manager"]["ios_build_type"]), "version" => array("ios" => trim($_GPC["manager"]["version"]["ios"]), "android" => 1), "ios_download_link" => trim($_GPC["manager"]["ios_download_link"]), "android_download_link" => MODULE_URL . "resource/apps/" . $_W["uniacid"] . "/android/manager.apk", "phonic" => $files);
        set_system_config("app.manager", $data);
        imessage(error(0, "设置app参数成功"), "refresh", "ajax");
        } else {
            if ($_GPC["form_type"] == "upload_file") {
                set_time_limit(0);
                $file = upload_file($_FILES["file"], "app", "manager.apk", "resource/apps/" . $_W["uniacid"] . "/android/");
                if (is_error($file)) {
                    imessage(error(-1, $file["message"]), "", "ajax");
                }
                imessage(error(0, "上传APP安装包成功"), "refresh", "ajax");
            }
        }
    }
    $app = get_system_config("app.manager");
}
include itemplate("app");

?>