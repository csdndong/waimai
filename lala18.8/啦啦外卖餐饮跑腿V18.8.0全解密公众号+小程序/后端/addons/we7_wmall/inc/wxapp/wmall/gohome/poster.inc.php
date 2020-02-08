<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    mload()->model("qrcode");
    mload()->model("poster");
    $goods_id = intval($_GPC["goods_id"]);
    $type = trim($_GPC["type"]);
    $routers = array("pintuan" => array("url" => "package/pages/gohome/pintuan/detail", "table" => "tiny_wmall_pintuan_goods"), "kanjia" => array("url" => "package/pages/gohome/kanjia/index", "table" => "tiny_wmall_kanjia"), "seckill" => array("url" => "package/pages/gohome/seckill/index", "table" => "tiny_wmall_seckill_goods"));
    $router = $routers[$type];
    $url = ivurl($router["url"], array("id" => $goods_id), true);
    $params = array("url" => $url, "size" => 4);
    $qrcode_url = qrcode_normal_build($params);
    if (is_error($qrcode_url)) {
        $respon = array("errno" => -1, "message" => "生成二维码失败， 失败原因：" . $qrcode_url["message"]);
        imessage($respon, "", "ajax");
    }
    $poster["qrcode_url"] = $qrcode_url;
    $poster["bg"] = "../addons/we7_wmall/plugin/gohome/" . $type . "/static/img/" . $type . "posterbg.png";
    $poster["data"]["items"] = array(array("params" => array("imgurl" => ""), "style" => array("left" => "0px", "top" => "0px", "width" => "320px", "height" => "160px", "position" => "cover"), "id" => "image"), array("params" => array(), "style" => array("left" => "21.3px", "top" => "144.6px", "width" => "55px", "height" => "55px", "border" => ""), "id" => "avatar"), array("params" => array(), "style" => array("left" => "93px", "top" => "172px", "width" => "200px", "height" => "23px", "line" => "1", "size" => "9px", "color" => "#343434", "align" => "left"), "id" => "nickname"), array("params" => array("content" => "商品名称"), "style" => array("left" => "25px", "top" => "220px", "type" => "title", "width" => "266px", "height" => "75px", "line" => "3", "size" => "11px", "color" => "#343434", "align" => "left"), "id" => "text", "text_type" => "goods_name"), array("params" => array("content" => "市场价:￥"), "style" => array("left" => "30px", "top" => "330px", "type" => "text", "width" => "101px", "height" => "24px", "line" => "1", "size" => "9px", "color" => "#878787", "words" => "市场价:￥", "align" => "left"), "id" => "text", "text_type" => "oldprice"), array("params" => array(), "style" => array("left" => "197px", "top" => "290px", "width" => "85px", "height" => "85px", "size" => ""), "id" => "qrcode"), array("params" => array("content" => "￥"), "style" => array("left" => "75px", "top" => "306px", "type" => "text", "width" => "10px", "height" => "26px", "line" => "1", "size" => "10px", "color" => "#ff4744", "words" => "￥", "align" => "left"), "id" => "text", "text_type" => "price"), array("params" => array(), "style" => array("left" => "88px", "top" => "293px", "type" => "text", "width" => "150px", "height" => "40px", "line" => "1", "size" => "24px", "color" => "#ff4744", "words" => "", "align" => "left"), "id" => "text"), array("params" => array("content" => "已有人喜欢这款商品"), "style" => array("left" => "35px", "top" => "379px", "type" => "text", "width" => "150px", "height" => "18px", "line" => "1", "size" => "8px", "color" => "#343434", "words" => "已有人喜欢这款商品", "align" => "left"), "id" => "text", "text_type" => "takepart"));
    $goods = pdo_get($router["table"], array("uniacid" => $_W["uniacid"], "id" => $goods_id), array("id", "name", "thumb", "price", "oldprice", "sharenum", "looknum"));
    foreach ($poster["data"]["items"] as &$item) {
        if ($item["id"] == "image" && !empty($goods["thumb"])) {
            $item["params"]["imgurl"] = $goods["thumb"];
        } else {
            if ($item["text_type"] == "price") {
                $item["params"]["content"] = "￥" . $goods["price"];
            } else {
                if ($item["text_type"] == "oldprice") {
                    $item["params"]["content"] = "市场价￥" . $goods["oldprice"];
                } else {
                    if ($item["text_type"] == "goods_name") {
                        $item["params"]["content"] = (string) $goods["name"];
                    } else {
                        if ($item["text_type"] == "takepart") {
                            $item["params"]["content"] = "已有" . $goods["looknum"] . "人喜欢这款商品";
                        }
                    }
                }
            }
        }
    }
    $params = array("config" => $poster, "extra" => $_W["member"], "name" => "gohome_" . $type . "_" . $goods_id . "_" . $_W["member"]["uid"], "plugin" => $type);
    $url = poster_create($params);
    if (is_error($url)) {
        $respon = array("errno" => -1, "message" => "生成海报失败，失败原因：" . $url["message"]);
        imessage($respon, "", "ajax");
    }
    $reslut = array("respon" => $url);
    imessage(error(0, $reslut), "", "ajax");
}

?>