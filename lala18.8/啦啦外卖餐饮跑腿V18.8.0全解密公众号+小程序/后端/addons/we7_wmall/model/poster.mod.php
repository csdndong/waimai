<?php
function poster_trimPx($data)
{
    $data["left"] = intval(str_replace("px", "", $data["left"])) * 2;
    $data["top"] = intval(str_replace("px", "", $data["top"])) * 2;
    $data["width"] = intval(str_replace("px", "", $data["width"])) * 2;
    $data["height"] = intval(str_replace("px", "", $data["height"])) * 2;
    $data["fontsize"] = intval(str_replace("px", "", $data["fontsize"])) * 2;
    $data["src"] = tomedia($data["src"]);
    return $data;
}
function poster_mergeImage($target, $imgurl, $data)
{
    $img = poster_createImage($imgurl);
    $w = imagesx($img);
    $h = imagesy($img);
    if ($data["border"] == "radius" || $data["border"] == "circle") {
        $img = imageRadius($img, $data["border"] == "circle");
    }
    if ($data["position"] == "cover") {
        $oldheight = $data["height"];
        $data["height"] = $data["width"] * $h / $w;
        if ($oldheight < $data["height"]) {
            $data["top"] = $data["top"] - ($data["height"] - $oldheight) / 2;
        }
    }
    imagecopyresized($target, $img, $data["left"], $data["top"], 0, 0, $data["width"], $data["height"], $w, $h);
    imagedestroy($img);
    return $target;
}
function poster_createImage($imgurl)
{
    load()->func("communication");
    $resp = ihttp_request($imgurl);
    if ($resp["code"] == 200 && !empty($resp["content"])) {
        return imagecreatefromstring($resp["content"]);
    }
    for ($i = 0; $i < 3; $i++) {
        $resp = ihttp_request($imgurl);
        if ($resp["code"] == 200 && !empty($resp["content"])) {
            return imagecreatefromstring($resp["content"]);
        }
    }
    return "";
}
function poster_mergeText($target, $text, $data)
{
    $font = MODULE_ROOT . "/static/fonts/pingfang.ttf";
    if (!is_file($font)) {
        $font = MODULE_ROOT . "/static/fonts/msyh.ttf";
    }
    $colors = poster_hex2rgb($data["color"]);
    $color = imagecolorallocate($target, $colors["red"], $colors["green"], $colors["blue"]);
    $text = poster_autowrap($data["fontsize"], 0, $font, $text, $data["width"], $data["line"]);
    imagettftext($target, $data["fontsize"], 0, $data["left"], $data["top"] + $data["fontsize"], $color, $font, $text);
    return $target;
}
function poster_hex2rgb($colour)
{
    if ($colour[0] == "#") {
        $colour = substr($colour, 1);
    }
    if (strlen($colour) == 6) {
        list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
    } else {
        if (strlen($colour) == 3) {
            list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
        } else {
            return false;
        }
    }
    $r = hexdec($r);
    $g = hexdec($g);
    $b = hexdec($b);
    return array("red" => $r, "green" => $g, "blue" => $b);
}
function poster_create($poster)
{
    global $_W;
    $file = "/resource/poster/" . $poster["plugin"] . "/" . $_W["uniacid"] . "/iposter_" . $poster["name"] . ".jpg";
    $qrcode = MODULE_ROOT . (string) $file;
    if (file_exists($qrcode)) {
        return (string) $_W["siteroot"] . "addons/we7_wmall" . $file . "?t=" . time();
    }
    load()->func("file");
    mkdirs(dirname($qrcode));
    set_time_limit(0);
    @ini_set(@"memory_limit", @"256M");
    $bg = tomedia($poster["config"]["bg"]);
    if (empty($bg)) {
        return error(-1, "背景图片不存在");
    }
    $size = getimagesize($bg);
    if (empty($size)) {
        return error(-1, "获取背景图片信息失败");
    }
    $target = imagecreatetruecolor($size[0], $size[1]);
    $bg = poster_createimage($bg);
    imagecopy($target, $bg, 0, 0, 0, 0, $size[0], $size[1]);
    imagedestroy($bg);
    $extra = $poster["extra"];
    $parts = $poster["config"]["data"]["items"];
    foreach ($parts as $part) {
        $style = poster_trimpx($part["style"]);
        if ($part["id"] == "qrcode") {
            $qrcode_url = $poster["config"]["qrcode_url"];
            poster_mergeimage($target, $qrcode_url, $style);
        } else {
            if ($part["id"] == "image") {
                poster_mergeimage($target, tomedia($part["params"]["imgurl"]), $style);
            } else {
                if ($part["id"] == "avatar") {
                    poster_mergeimage($target, $extra["avatar"], $style);
                } else {
                    if ($part["id"] == "nickname") {
                        poster_mergetext($target, $extra["nickname"], $style);
                    } else {
                        if ($part["id"] == "text") {
                            poster_mergetext($target, $part["params"]["content"], $style);
                        }
                    }
                }
            }
        }
    }
    $quality = intval($poster["config"]["data"]["page"]["quality"]);
    if (empty($quality)) {
        $quality = 75;
    }
    imagejpeg($target, $qrcode, $quality);
    imagedestroy($target);
    return (string) $_W["siteroot"] . "addons/we7_wmall" . $file . "?t=" . time();
}
function poster_getQR($fans, $poster, $sid, $modulename)
{
    global $_W;
    $pid = $poster["id"];
    $share = pdo_fetch("select * from " . tablename($modulename . "_share") . " where id='" . $sid . "'");
    if (!empty($share["url"])) {
        $out = false;
        if ($poster["rtype"]) {
            $qrcode = pdo_fetch("select * from " . tablename("qrcode") . " where uniacid='" . $_W["uniacid"] . "' and qrcid='" . $share["sceneid"] . "' " . " and ticket='" . $share["ticketid"] . "' and url='" . $share["url"] . "'");
            if ($qrcode["createtime"] + $qrcode["expire"] < time()) {
                pdo_delete("qrcode", array("id" => $qrcode["id"]));
                $out = true;
            }
        }
        if (!$out) {
            return $share["url"];
        }
    }
    $model = 2 - intval($poster["rtype"]);
    $sceneid = pdo_fetchcolumn("select qrcid from " . tablename("qrcode") . " where uniacid='" . $_W["uniacid"] . "' and model='" . $model . "' order by qrcid desc limit 1");
    if (empty($sceneid)) {
        $sceneid = 20000;
    } else {
        $sceneid++;
    }
    $barcode["action_info"]["scene"]["scene_id"] = $sceneid;
    load()->model("account");
    $acid = pdo_fetchcolumn("select acid from " . tablename("account") . " where uniacid=" . $_W["uniacid"]);
    $uniacccount = WeAccount::create($acid);
    $time = 0;
    if ($poster["rtype"]) {
        $barcode["action_name"] = "QR_SCENE";
        $barcode["expire_seconds"] = 30 * 24 * 3600;
        $res = $uniacccount->barCodeCreateDisposable($barcode);
        $time = $barcode["expire_seconds"];
    } else {
        $barcode["action_name"] = "QR_LIMIT_SCENE";
        $res = $uniacccount->barCodeCreateFixed($barcode);
    }
    pdo_insert("qrcode", array("uniacid" => $_W["uniacid"], "acid" => $acid, "qrcid" => $sceneid, "name" => $poster["title"], "keyword" => $poster["kword"], "model" => $model, "ticket" => $res["ticket"], "expire" => $time, "createtime" => time(), "status" => 1, "url" => $res["url"]));
    pdo_update($modulename . "_share", array("sceneid" => $sceneid, "ticketid" => $res["ticket"], "url" => $res["url"]), array("id" => $sid));
    return $res["url"];
}
function imageRadius($target = false, $circle = false)
{
    $w = imagesx($target);
    $h = imagesy($target);
    $w = min($w, $h);
    $h = $w;
    $img = imagecreatetruecolor($w, $h);
    imagesavealpha($img, true);
    $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
    imagefill($img, 0, 0, $bg);
    $radius = $circle ? $w / 2 : 20;
    $r = $radius;
    for ($x = 0; $x < $w; $x++) {
        for ($y = 0; $y < $h; $y++) {
            $rgbColor = imagecolorat($target, $x, $y);
            if ($radius <= $x && $x <= $w - $radius || $radius <= $y && $y <= $h - $radius) {
                imagesetpixel($img, $x, $y, $rgbColor);
            } else {
                $y_x = $r;
                $y_y = $r;
                if (($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y) <= $r * $r) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
                $y_x = $w - $r;
                $y_y = $r;
                if (($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y) <= $r * $r) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
                $y_x = $r;
                $y_y = $h - $r;
                if (($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y) <= $r * $r) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
                $y_x = $w - $r;
                $y_y = $h - $r;
                if (($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y) <= $r * $r) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
            }
        }
    }
    return $img;
}
function poster_autowrap($fontsize, $angle, $fontface, $string, $width, $needhang = 1)
{
    $content = "";
    $hang = 1;
    for ($i = 0; $i < mb_strlen($string, "UTF8"); $i++) {
        $letter[] = mb_substr($string, $i, 1, "UTF8");
    }
    foreach ($letter as $l) {
        $teststr = $content . " " . $l;
        $testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
        if ($width < $testbox[2] && $content !== "") {
            if ($hang < $needhang) {
                $content .= "\n";
                $hang++;
            } else {
                break;
            }
        }
        $content .= $l;
    }
    return $content;
}

?>