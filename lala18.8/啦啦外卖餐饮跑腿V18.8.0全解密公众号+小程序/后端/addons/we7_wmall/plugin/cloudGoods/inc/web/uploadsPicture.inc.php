<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "批量上传图片";
    if ($_W["ispost"]) {
        $file = upload_file($_FILES["file"], "zip");
        if (is_error($file)) {
            imessage(error(-1, $file["message"]), "", "ajax");
        }
        $zip = new ZipArchive();
        $res = $zip->open(MODULE_ROOT . "/" . $file);
        if ($res === true) {
            $pathname = "images/" . $_W["uniacid"] . "/" . date("Y/m/") . "/" . random(5, true);
            $original_pathname = $pathname;
            $filename = ATTACHMENT_ROOT . "/" . $original_pathname;
            mkdirs($filename);
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $image_name = $zip->getNameIndex($i);
                $images[] = array("path" => (string) $original_pathname . "/" . $image_name, "remote_status" => 1, "name" => $image_name);
            }
            $zip->extractTo($filename);
            $zip->close();
            $data = array("all" => $images, "process" => $images);
            $key = "cloudGoodsUpload:" . $_W["uniacid"] . ":0:" . $original_pathname;
            cache_write($key, $data);
            if (!empty($_W["setting"]["remote"]["type"])) {
                imessage(error(0, "上传成功"), iurl("cloudGoods/uploadsPicture/remote", array("key" => $key)), "ajax");
            }
            imessage(error(0, "上传成功"), iurl("cloudGoods/uploadsPicture/show", array("key" => $key)), "ajax");
        }
    }
    include itemplate("uploadsPicture");
} else {
    if ($op == "show") {
        $_W["page"]["title"] = "上传图片";
        $key = trim($_GPC["key"]);
        $data = cache_read($key);
        $images = $data["all"];
        include itemplate("showPicture");
    } else {
        if ($op == "remote") {
            $_W["page"]["title"] = "上传远程图片";
            $key = trim($_GPC["key"]);
            $data = cache_read($key);
            $all = $data["all"];
            $process = $data["process"];
            if ($_W["ispost"]) {
                $i = intval($_GPC["__input"]["i"]);
                $images = array_slice($process, $i, 10, true);
                load()->func("file");
                foreach ($images as $key => $item) {
                    $item["path"] = "/" . $item["path"];
                    $status = file_remote_upload($item["path"]);
                    if (!is_error($status)) {
                        $data["all"][$key]["remote_status"] = 0;
                        $data["process"][$key]["remote_status"] = 0;
                    }
                    $more = 1;
                    if (count($images) < 10) {
                        $more = 0;
                    }
                }
                cache_write($key, $data);
                imessage(error(0, $more), "", "ajax");
            }
            include itemplate("uploadRemotePicture");
        }
    }
}

?>