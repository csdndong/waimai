<?php 
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    mload()->model("qrcode");
    mload()->model("poster");
    $goods_id = intval($_GPC["goods_id"]);
    $type = trim($_GPC["type"]);
    $routers = array("pintuan" => array("url" => "gohome/pages/pintuan/detail", "table" => "tiny_wmall_pintuan_goods"), "kanjia" => array("url" => "gohome/pages/kanjia/detail", "table" => "tiny_wmall_kanjia"), "seckill" => array("url" => "gohome/pages/seckill/detail", "table" => "tiny_wmall_seckill_goods"));
    $router = $routers[$type];
    if ($_W["ochannel"] == "wxapp") {
        $array = array("url" => "gohome/pages/" . $type . "/detail", "scene" => "id:" . $goods_id, "path" => "/we7_wmall/wxappqrcode/gohome/" . $type . "_" . $goods_id . ".png");
        $qrcode_url = qrcode_wxapp_build($array);
        if (is_error($qrcode_url)) {
            $respon = array("errno" => 1, "message" => "生成小程序二维码失败，失败原因：" . $qrcode_url["message"]);
            imessage($respon, "", "ajax");
        }
    } else {
        $url = ivurl($router["url"], array("id" => $goods_id), true);
        $params = array("url" => $url, "fontsize" => 4);
        $qrcode_url = qrcode_normal_build($params);
        if (is_error($qrcode_url)) {
            $respon = array("errno" => -1, "message" => "生成二维码失败， 失败原因：" . $qrcode_url["message"]);
            imessage($respon, "", "ajax");
        }
    }
    $poster["qrcode_url"] = tomedia($qrcode_url);
    $poster["bg"] = "../addons/we7_wmall/plugin/gohome/static/img/posterbg.jpg";
    $poster["data"]["items"] = array("thumb" => array("params" => array("imgurl" => ""), "style" => array("left" => "0px", "top" => "0px", "width" => "320px", "height" => "160px", "position" => "cover"), "id" => "image"), "bg" => array("params" => array("imgurl" => "../addons/we7_wmall/plugin/gohome/static/img/" . $type . "posterbg.png"), "style" => array("left" => "0px", "top" => "0px", "width" => "320px", "height" => "419px"), "id" => "image"), "avatar" => array("params" => array(), "style" => array("left" => "21.3px", "top" => "144.6px", "width" => "55px", "height" => "55px", "border" => ""), "id" => "avatar"), "nickname" => array("params" => array(), "style" => array("left" => "93px", "top" => "172px", "width" => "200px", "height" => "23px", "line" => "1", "fontsize" => "9px", "color" => "#343434", "align" => "left"), "id" => "nickname"), "goods_name" => array("params" => array("content" => "商品名称"), "style" => array("left" => "25px", "top" => "220px", "type" => "title", "width" => "266px", "height" => "75px", "line" => "3", "fontsize" => "11px", "color" => "#343434", "align" => "left"), "id" => "text"), "oldprice" => array("params" => array("content" => "市场价:￥"), "style" => array("left" => "30px", "top" => "330px", "type" => "text", "width" => "101px", "height" => "24px", "line" => "1", "fontsize" => "9px", "color" => "#878787", "words" => "市场价:￥", "align" => "left"), "id" => "text"), "qrcode" => array("params" => array(), "style" => array("left" => "197px", "top" => "290px", "width" => "85px", "height" => "85px", "fontsize" => ""), "id" => "qrcode"), "price_symbol" => array("params" => array("content" => "￥"), "style" => array("left" => "75px", "top" => "306px", "type" => "text", "width" => "10px", "height" => "26px", "line" => "1", "fontsize" => "10px", "color" => "#ff4744", "words" => "￥", "align" => "left"), "id" => "text"), "price" => array("params" => array("content" => "￥"), "style" => array("left" => "88px", "top" => "293px", "type" => "text", "width" => "150px", "height" => "40px", "line" => "1", "fontsize" => "24px", "color" => "#ff4744", "words" => "", "align" => "left"), "id" => "text"), "takepart" => array("params" => array("content" => "已有人喜欢这款商品"), "style" => array("left" => "35px", "top" => "379px", "type" => "text", "width" => "150px", "height" => "18px", "line" => "1", "fontsize" => "8px", "color" => "#343434", "words" => "已有人喜欢这款商品", "align" => "left"), "id" => "text"));
    $goods = pdo_get($router["table"], array("uniacid" => $_W["uniacid"], "id" => $goods_id), array("id", "name", "thumb", "price", "oldprice", "sharenum", "looknum"));
    foreach ($poster["data"]["items"] as $key => &$item) {
        if ($key == "thumb" && !empty($goods["thumb"])) {
            $item["params"]["imgurl"] = $goods["thumb"];
        } else {
            if ($key == "price") {
                $item["params"]["content"] = (string) $goods["price"];
            } else {
                if ($key == "oldprice") {
                    $item["params"]["content"] = "市场价￥" . $goods["oldprice"];
                } else {
                    if ($key == "goods_name") {
                        $item["params"]["content"] = (string) $goods["name"];
                    } else {
                        if ($key == "takepart") {
                            $item["params"]["content"] = "已有" . $goods["looknum"] . "人喜欢这款商品";
                        }
                    }
                }
            }
        }
    }
    $params = array("config" => $poster, "extra" => $_W["member"], "name" => "gohome_" . $_W["ochannel"] . "_" . $type . "_" . $goods_id . "_" . $_W["member"]["uid"], "plugin" => "gohome");
    $url = poster_create($params);
    if (is_error($url)) {
        $respon = array("errno" => -1, "message" => "生成海报失败，失败原因：" . $url["message"]);
        imessage($respon, "", "ajax");
    }
    $reslut = array("respon" => $url);
    imessage(error(0, $reslut), "", "ajax");
}

?>