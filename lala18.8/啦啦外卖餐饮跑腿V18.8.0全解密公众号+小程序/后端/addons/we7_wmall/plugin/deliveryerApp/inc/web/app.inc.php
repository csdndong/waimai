<?php


defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "app";
$_config = get_system_config("app");
$downurls = array("deliveryer" => array("ios" => MODULE_URL . "resource/apps/" . $_W["uniacid"] . "/ios/deliveryman_1.0.apk", "android" => MODULE_URL . "resource/apps/" . $_W["uniacid"] . "/android/deliveryman_1.0.apk", "apk" => MODULE_ROOT . "/resource/apps/" . $_W["uniacid"] . "/android/deliveryman_1.0.apk"));
if ($op == "app") {
    $_W["page"]["title"] = "配送员app设置";
    load()->func("file");
    $path = "resource/mp3/" . $_W["uniacid"] . "/";
    mkdirs(MODULE_ROOT . "/" . $path);
    $files = array();
    if ($_W["ispost"]) {
        if ($_GPC["form_type"] == "setting_app") {
            foreach ($_FILES as $key => $type) {
                if (!empty($type["name"])) {
                    foreach ($type["name"] as $key1 => $val) {
                        if (!empty($type["name"][$key1]) && $type["error"][$key1] == 0) {
                            $pathinfo = pathinfo($type["name"][$key1]);
                            $ext = strtolower($pathinfo["extension"]);
                            if ($ext != "mp3") {
                                imessage(error(-1, "仅支持mp3类型的语音文件"), referer(), "ajax");
                            }
                            $basename = (string) $key . $key1 . "." . $ext;
                            if (!file_move($type["tmp_name"][$key1], MODULE_ROOT . "/" . $path . $basename)) {
                                imessage(error(-1, "保存上传文件失败"), referer(), "ajax");
                            }
                            $files[$key][$key1] = $basename;
                        }
                        if (empty($files[$key][$key1])) {
                            $files[$key][$key1] = $_config["deliveryer"]["phonic"][$key][$key1];
                        }
                    }
                }
            }
            $data = array("serial_sn" => trim($_GPC["deliveryer"]["serial_sn"]), "push_key" => trim($_GPC["deliveryer"]["push_key"]), "push_secret" => trim($_GPC["deliveryer"]["push_secret"]), "push_tags" => $_config["deliveryer"]["push_tags"], "ios_build_type" => intval($_GPC["deliveryer"]["ios_build_type"]), "android_version" => intval($_GPC["deliveryer"]["android_version"]), "version" => array("ios" => trim($_GPC["deliveryer"]["version"]["ios"]), "android" => 1), "xunfei_Android_appid" => trim($_GPC["deliveryer"]["xunfei_Android_appid"]), "xunfei_ios_appid" => trim($_GPC["deliveryer"]["xunfei_ios_appid"]), "ios_download_link" => trim($_GPC["deliveryer"]["ios_download_link"]), "android_download_link" => MODULE_URL . "resource/apps/" . $_W["uniacid"] . "/android/deliveryman_1.0.apk", "phonic" => $files);
            if (empty($_config["deliveryer"]["push_tags"])) {
                $data["push_tags"] = array("working" => random(8), "rest" => random(8));
            }
            if (empty($_config["deliveryer"]["push_tags"]["waimai"])) {
                $data["push_tags"]["waimai"] = random(8);
            }
            if (empty($_config["deliveryer"]["push_tags"]["paotui"])) {
                $data["push_tags"]["paotui"] = random(8);
            }
            set_system_config("app.deliveryer", $data);
            imessage(error(0, "设置app参数成功"), "refresh", "ajax");
        } else {
            if ($_GPC["form_type"] == "upload_file") {
                set_time_limit(0);
                $file = upload_file($_FILES["file"], "app", "deliveryman_1.0.apk", "resource/apps/" . $_W["uniacid"] . "/android/");
                if (is_error($file)) {
                    imessage(error(-1, $file["message"]), "", "ajax");
                }
                imessage(error(0, "上传APP安装包成功"), "refresh", "ajax");
            }
        }
    }
    $app = get_system_config("app.deliveryer");
}
include itemplate("app");

?>