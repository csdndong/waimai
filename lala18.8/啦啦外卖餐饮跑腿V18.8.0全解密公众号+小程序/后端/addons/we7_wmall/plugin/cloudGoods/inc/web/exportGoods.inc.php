<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "从Excel导入商品";
    if ($_W["ispost"]) {
        $file = upload_file($_FILES["file"], "excel");
        if (is_error($file)) {
            imessage(error(-1, $file["message"]), "", "ajax");
        }
        $data = read_excel($file);
        if (is_error($data)) {
            imessage(error(-1, $data["message"]), "", "ajax");
        }
        unset($data[1]);
        if (empty($data)) {
            imessage(error(-1, "没有要导入的数据"), "", "ajax");
        }
        $da = array();
        $categorys = pdo_fetchall("select id, title, menu_id from " . tablename("tiny_wmall_cloudgoods_goods_category") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]), "id");
        $categorys_title = array();
        if (!empty($categorys)) {
            foreach ($categorys as $val) {
                $categorys_title[$val["id"]] = trim($val["title"]);
            }
        }
        foreach ($data as $da) {
            if (empty($da["0"]) || empty($da["1"])) {
                continue;
            }
            $insert = array("uniacid" => $_W["uniacid"], "title" => trim($da[0]), "unitname" => trim($da[2]), "price" => floatval($da[3]), "box_price" => floatval($da[4]), "label" => trim($da[5]), "total" => intval($da[6]), "thumb" => trim($da[8]), "displayorder" => intval($da[9]), "description" => trim($da[10]), "number" => intval($da[13]), "type" => intval($da[14]) ? intval($da[14]) : 3, "ts_price" => floatval($da[15]) ? floatval($da[15]) : floatval($da[3]));
            if (!empty($categorys_title)) {
                $da[1] = trim($da[1]);
                $category_index = array_search($da[1], $categorys_title);
                if (0 < $category_index) {
                    $insert["category_id"] = $categorys[$category_index]["id"];
                    $insert["menu_id"] = $categorys[$category_index]["menu_id"];
                }
            }
            if (!empty($da[11])) {
                $attrs = str_replace(",", ",", $da[11]);
                $attrs = explode(",", $attrs);
                $new_attrs = array();
                if (!empty($attrs)) {
                    foreach ($attrs as $attr) {
                        $attr = array_filter(explode("|", $attr));
                        $name = $attr[0];
                        array_shift($attr);
                        if (empty($name) || empty($attr)) {
                            continue;
                        }
                        $new_attrs[] = array("name" => $name, "label" => $attr);
                    }
                }
                $insert["attrs"] = iserializer($new_attrs);
            }
            pdo_insert("tiny_wmall_cloudgoods_goods", $insert);
            $goods_id = pdo_insertid();
            if (!empty($da[10])) {
                $options = str_replace("!", ",", $da[10]);
                $options = explode(",", $options);
                if (!empty($options)) {
                    foreach ($options as $option) {
                        $option = explode("|", $option);
                        if (count($option) == 4) {
                            $insert = array("uniacid" => $_W["uniacid"], "goods_id" => $goods_id, "name" => trim($option[0]), "price" => floatval($option[1]), "total" => intval($option[2]), "displayorder" => intval($option[3]));
                            pdo_insert("tiny_wmall_cloudgoods_goods_options", $insert);
                            $i++;
                        }
                    }
                    if (0 < $i) {
                        pdo_update("tiny_wmall_cloudgoods_goods", array("is_options" => 1), array("id" => $goods_id));
                    }
                }
            }
        }
        imessage(error(0, "导入商品成功"), iurl("cloudGoods/exportGoods/index"), "ajax");
    }
    include itemplate("exportGoods");
}

?>
