<?php


defined("IN_IA") or exit("Access Denied");
function tpl_form_field_fans($name, $value, $scene = "notify", $required = false)
{
    global $_W;
    if (empty($default)) {
        $default = "./resource/images/nopic.jpg";
    }
    $s = "";
    if (!defined("TPL_INIT_TINY_FANS")) {
        $option = array("scene" => $scene);
        $option = json_encode($option);
        $s = "\r\n\t\t<script type=\"text/javascript\">\r\n\t\t\tfunction showFansDialog(elm) {\r\n\t\t\t\tvar btn = \$(elm);\r\n\t\t\t\tvar openid_wxapp = btn.parent().prev();\r\n\t\t\t\tvar openid = btn.parent().prev().prev();\r\n\t\t\t\tvar avatar = btn.parent().prev().prev().prev();\r\n\t\t\t\tvar nickname = btn.parent().prev().prev().prev().prev();\r\n\t\t\t\tvar img = btn.parent().parent().next().find(\"img\");\r\n\t\t\t\tirequire([\"web/tiny\"], function(tiny){\r\n\t\t\t\t\ttiny.selectfan(function(fans){\r\n\t\t\t\t\t\tconsole.log(fans);\r\n\t\t\t\t\t\tif(img.length > 0){\r\n\t\t\t\t\t\t\timg.get(0).src = fans.avatar;\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t\topenid_wxapp.val(fans.openid_wxapp);\r\n\t\t\t\t\t\topenid.val(fans.openid);\r\n\t\t\t\t\t\tavatar.val(fans.avatar);\r\n\t\t\t\t\t\tnickname.val(fans.nickname);\r\n\t\t\t\t\t}, " . $option . ");\r\n\t\t\t\t});\r\n\t\t\t}\r\n\t\t</script>";
        define("TPL_INIT_TINY_FANS", true);
    }
    $s .= "\r\n\t\t<div class=\"input-group\">\r\n\t\t\t<input type=\"text\" name=\"" . $name . "[nickname]\" value=\"" . $value["nickname"] . "\" class=\"form-control\" readonly " . ($required ? "required" : "") . ">\r\n\t\t\t<input type=\"hidden\" name=\"" . $name . "[avatar]\" value=\"" . $value["avatar"] . "\">\r\n\t\t\t<input type=\"hidden\" name=\"" . $name . "[openid]\" value=\"" . $value["openid"] . "\">\r\n\t\t\t<input type=\"hidden\" name=\"" . $name . "[openid_wxapp]\" value=\"" . $value["openid_wxapp"] . "\">\r\n\t\t\t<span class=\"input-group-btn\">\r\n\t\t\t\t<button class=\"btn btn-default\" type=\"button\" onclick=\"showFansDialog(this);\">选择粉丝</button>\r\n\t\t\t</span>\r\n\t\t</div>\r\n\t\t<div class=\"input-group\" style=\"margin-top:.5em;\">\r\n\t\t\t<img src=\"" . $value["avatar"] . "\" onerror=\"this.src='" . $default . "'; this.title='头像未找到.'\" class=\"img-responsive img-thumbnail\" width=\"150\" />\r\n\t\t</div>";
    return $s;
}
function itpl_form_field_daterange($name, $value = array(), $time = false)
{
    global $_GPC;
    $placeholder = isset($value["placeholder"]) ? $value["placeholder"] : "";
    $s = "";
    if (empty($time) && !defined("TPL_INIT_TINY_DATERANGE_DATE")) {
        $s = "\r\n<script type=\"text/javascript\">\r\n\trequire([\"daterangepicker\"], function() {\r\n\t\t\$(\".daterange.daterange-date\").each(function(){\r\n\t\t\tvar elm = this;\r\n\t\t\tvar container =\$(elm).parent().prev();\r\n\t\t\t\$(this).daterangepicker({\r\n\t\t\t\tformat: \"YYYY-MM-DD\"\r\n\t\t\t}, function(start, end){\r\n\t\t\t\t\$(elm).find(\".date-title\").html(start.toDateStr() + \" 至 \" + end.toDateStr());\r\n\t\t\t\tcontainer.find(\":input:first\").val(start.toDateTimeStr());\r\n\t\t\t\tcontainer.find(\":input:last\").val(end.toDateTimeStr());\r\n\t\t\t});\r\n\t\t});\r\n\t});\r\n\r\n\tfunction clearTime(obj){\r\n\t\t\$(obj).prev().html(\"<span class=date-title>\" + \$(obj).attr(\"placeholder\") + \"</span>\");\r\n\t\t\$(obj).parent().prev().find(\"input\").val(\"\");\r\n\t }\r\n</script>";
        define("TPL_INIT_TINY_DATERANGE_DATE", true);
    }
    if (!empty($time) && !defined("TPL_INIT_TINY_DATERANGE_TIME")) {
        $s = "\r\n<script type=\"text/javascript\">\r\n\trequire([\"daterangepicker\"], function(\$){\r\n\t\t\$(function(){\r\n\t\t\t\$(\".daterange.daterange-time\").each(function() {\r\n\t\t\t\tvar elm = this;\r\n\t\t\t\tvar container =\$(elm).parent().prev();\r\n\t\t\t\t\$(this).daterangepicker({\r\n\t\t\t\t\tformat: \"YYYY-MM-DD HH:mm\",\r\n\t\t\t\t\ttimePicker: true,\r\n\t\t\t\t\ttimePicker12Hour : false,\r\n\t\t\t\t\ttimePickerIncrement: 1,\r\n\t\t\t\t\tminuteStep: 1\r\n\t\t\t\t}, function(start, end){\r\n\t\t\t\t\t\$(elm).find(\".date-title\").html(start.toDateTimeStr() + \" 至 \" + end.toDateTimeStr());\r\n\t\t\t\t\tcontainer.find(\":input:first\").val(start.toDateTimeStr());\r\n\t\t\t\t\tcontainer.find(\":input:last\").val(end.toDateTimeStr());\r\n\t\t\t\t});\r\n\t\t\t});\r\n\t\t});\r\n\t});\r\n\r\n\tfunction clearTime(obj){\r\n\t\t\$(obj).prev().html(\"<span class=date-title>\" + \$(obj).attr(\"placeholder\") + \"</span>\");\r\n\t\t\$(obj).parent().prev().find(\"input\").val(\"\");\r\n\t }\r\n</script>";
        define("TPL_INIT_TINY_DATERANGE_TIME", true);
    }
    $str = $placeholder;
    $value["starttime"] = isset($value["starttime"]) ? $value["starttime"] : ($_GPC[$name]["start"] ? $_GPC[$name]["start"] : "");
    $value["endtime"] = isset($value["endtime"]) ? $value["endtime"] : ($_GPC[$name]["end"] ? $_GPC[$name]["end"] : "");
    if ($value["starttime"] && $value["endtime"]) {
        if (empty($time)) {
            $str = date("Y-m-d", strtotime($value["starttime"])) . "至 " . date("Y-m-d", strtotime($value["endtime"]));
        } else {
            $str = date("Y-m-d H:i", strtotime($value["starttime"])) . " 至 " . date("Y-m-d  H:i", strtotime($value["endtime"]));
        }
    }
    $s .= "\r\n\t\t<div style=\"float:left\">\r\n\t\t\t<input name=\"" . $name . "[start]" . "\" type=\"hidden\" value=\"" . $value["starttime"] . "\" />\r\n\t\t\t<input name=\"" . $name . "[end]" . "\" type=\"hidden\" value=\"" . $value["endtime"] . "\" />\r\n\t\t</div>\r\n\t\t<div class=\"btn-group\" style=\"padding-right:0;\">\r\n\t\t\t<button style=\"width:240px\" class=\"btn btn-default daterange " . (!empty($time) ? "daterange-time" : "daterange-date") . "\"  type=\"button\"><span class=\"date-title\">" . $str . "</span></button>\r\n\t\t\t<button class=\"btn btn-default\" type=\"button\" onclick=\"clearTime(this)\" placeholder=\"" . $placeholder . "\"><i class=\"fa fa-remove\"></i></button>\r\n\t\t</div>";
    return $s;
}
function tpl_form_field_tiny_link($name, $value = "", $options = array())
{
    global $_GPC;
    $s = "";
    if (!defined("TPL_INIT_TINY_LINK")) {
        $s = "\r\n\t\t<script type=\"text/javascript\">\r\n\t\t\tfunction showTinyLinkDialog(elm) {\r\n\t\t\t\tirequire([\"web/tiny\"], function(tiny){\r\n\t\t\t\t\tvar ipt = \$(elm).parent().prev();\r\n\t\t\t\t\ttiny.selectLink(function(href){\r\n\t\t\t\t\t\tipt.val(href);\r\n\t\t\t\t\t});\r\n\t\t\t\t});\r\n\t\t\t}\r\n\t\t</script>";
        define("TPL_INIT_TINY_LINK", true);
    }
    $s .= "\r\n\t<div class=\"input-group\">\r\n\t\t<input type=\"text\" value=\"" . $value . "\" name=\"" . $name . "\" class=\"form-control " . $options["css"]["input"] . "\" autocomplete=\"off\">\r\n\t\t<span class=\"input-group-btn\">\r\n\t\t\t<button class=\"btn btn-default " . $options["css"]["btn"] . "\" type=\"button\" onclick=\"showTinyLinkDialog(this);\">选择链接</button>\r\n\t\t</span>\r\n\t</div>\r\n\t";
    return $s;
}
function tpl_form_field_tiny_wxapp_link($name, $value = "", $options = array())
{
    global $_GPC;
    $s = "";
    if (!defined("TPL_INIT_TINY_WXAPP_LINK")) {
        $s = "\r\n\t\t<script type=\"text/javascript\">\r\n\t\t\tfunction showTinyWxappLinkDialog(elm) {\r\n\t\t\t\tirequire([\"web/tiny\"], function(tiny){\r\n\t\t\t\t\tvar ipt = \$(elm).parent().prev();\r\n\t\t\t\t\ttiny.selectWxappLink(function(href){\r\n\t\t\t\t\t\tipt.val(href);\r\n\t\t\t\t\t});\r\n\t\t\t\t});\r\n\t\t\t}\r\n\t\t</script>";
        define("TPL_INIT_TINY_WXAPP_LINK", true);
    }
    $s .= "\r\n\t<div class=\"input-group\">\r\n\t\t<input type=\"text\" value=\"" . $value . "\" name=\"" . $name . "\" class=\"form-control " . $options["css"]["input"] . "\" autocomplete=\"off\">\r\n\t\t<span class=\"input-group-btn\">\r\n\t\t\t<button class=\"btn btn-default " . $options["css"]["btn"] . "\" type=\"button\" onclick=\"showTinyWxappLinkDialog(this);\">选择链接</button>\r\n\t\t</span>\r\n\t</div>\r\n\t";
    return $s;
}
function tpl_form_field_tiny_coordinate($field, $value = array(), $required = false)
{
    global $_W;
    $s = "";
    if (!defined("TPL_INIT_TINY_COORDINATE")) {
        $s .= "<script type=\"text/javascript\">\r\n\t\t\t\tfunction showCoordinate(elm) {\r\n\t\t\t\t\tirequire([\"web/tiny\"], function(tiny){\r\n\t\t\t\t\t\tvar val = {};\r\n\t\t\t\t\t\tval.lng = parseFloat(\$(elm).parent().prev().prev().find(\":text\").val());\r\n\t\t\t\t\t\tval.lat = parseFloat(\$(elm).parent().prev().find(\":text\").val());\r\n\t\t\t\t\t\ttiny.map(val, function(r){\r\n\t\t\t\t\t\t\t\$(elm).parent().prev().prev().find(\":text\").val(r.lng);\r\n\t\t\t\t\t\t\t\$(elm).parent().prev().find(\":text\").val(r.lat);\r\n\t\t\t\t\t\t});\r\n\t\t\t\t\t});\r\n\t\t\t\t}\r\n\t\t\t</script>";
        define("TPL_INIT_TINY_COORDINATE", true);
    }
    $s .= "\r\n\t\t<div class=\"row row-fix\">\r\n\t\t\t<div class=\"col-xs-4 col-sm-4\">\r\n\t\t\t\t<input type=\"text\" name=\"" . $field . "[lng]\" value=\"" . $value["lng"] . "\" placeholder=\"地理经度\"  class=\"form-control\" " . ($required ? "required" : "") . "/>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"col-xs-4 col-sm-4\">\r\n\t\t\t\t<input type=\"text\" name=\"" . $field . "[lat]\" value=\"" . $value["lat"] . "\" placeholder=\"地理纬度\"  class=\"form-control\" " . ($required ? "required" : "") . "/>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"col-xs-4 col-sm-4\">\r\n\t\t\t\t<button onclick=\"showCoordinate(this);\" class=\"btn btn-default\" type=\"button\">选择坐标</button>\r\n\t\t\t</div>\r\n\t\t</div>";
    return $s;
}
function cloud_w_upgrade_version($family, $version, $release = 0)
{
    $verfile = MODULE_ROOT . "/version.php";
    $verdat = "<?php\r\n/**\r\n 三合一外卖系统\r\n * =========================================================\r\n * Copy right 2055-2088 。\r\n * ----------------------------------------------\r\n * 官方网址：？？？\r\n * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。\r\n * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。\r\n * =========================================================\r\n * @author : 外卖系统\r\n * @客服QQ : \r\n */\r\ndefined('IN_IA') or exit('Access Denied');\r\ndefine('MODULE_FAMILY', '" . $family . "');\r\ndefine('MODULE_VERSION', '" . $version . "');\r\ndefine('MODULE_RELEASE_DATE', '" . $release . "');";
    file_put_contents($verfile, trim($verdat));
}
function tpl_select2($name, $data, $value = 0, $filter = array("id", "title"), $default = "请选择")
{
    $element_id = "select2-" . $name;
    $json_data = array();
    foreach ($data as $da) {
        $json_data[] = array("id" => $da[$filter[0]], "text" => $da[$filter[1]]);
    }
    $json_data = json_encode($json_data);
    $html = "<select name=\"" . $name . "\" class=\"form-control\" id=\"" . $element_id . "\"></select>";
    $html .= "<script type=\"text/javascript\">\r\n\t\t\t\t\trequire([\"jquery\", \"select2\"], function(\$) {\r\n\t\t\t\t\t\t\$(\"#" . $element_id . "\").select2({\r\n\t\t\t\t\t\t\tplaceholder: \"" . $default . "\",\r\n\t\t\t\t\t\t\tdata: " . $json_data . ",\r\n\t\t\t\t\t\t\tval: " . $value . "\r\n\t\t\t\t\t\t});\r\n\t\t\t\t\t});\r\n\t\t\t  </script>";
    return $html;
}
function tpl_form_field_tiny_image($name, $value = "")
{
    global $_W;
    $default = "";
    $val = $default;
    if (!empty($value)) {
        $val = tomedia($value);
    }
    if (!empty($options["global"])) {
        $options["global"] = true;
    } else {
        $options["global"] = false;
    }
    if (empty($options["class_extra"])) {
        $options["class_extra"] = "";
    }
    if (isset($options["dest_dir"]) && !empty($options["dest_dir"]) && !preg_match("/^\\w+([\\/]\\w+)?\$/i", $options["dest_dir"])) {
        exit("图片上传目录错误,只能指定最多两级目录,如: \"we7_store\",\"we7_store/d1\"");
    }
    $options["direct"] = true;
    $options["multiple"] = false;
    if (isset($options["thumb"])) {
        $options["thumb"] = !empty($options["thumb"]);
    }
    $s = "";
    if (!defined("TPL_INIT_TINY_IMAGE")) {
        $s = "\r\n\t\t<script type=\"text/javascript\">\r\n\t\t\tfunction showImageDialog(elm, opts, options) {\r\n\t\t\t\trequire([\"util\"], function(util){\r\n\t\t\t\t\tvar btn = \$(elm);\r\n\t\t\t\t\tvar ipt = btn.parent().prev();\r\n\t\t\t\t\tvar val = ipt.val();\r\n\t\t\t\t\tvar img = ipt.parent().parent().find(\".input-group-addon img\");\r\n\t\t\t\t\toptions = " . str_replace("\"", "'", json_encode($options)) . ";\r\n\t\t\t\t\tutil.image(val, function(url){\r\n\t\t\t\t\t\tif(url.url){\r\n\t\t\t\t\t\t\tif(img.length > 0){\r\n\t\t\t\t\t\t\t\timg.get(0).src = url.url;\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t\tipt.val(url.attachment);\r\n\t\t\t\t\t\t\tipt.attr(\"filename\",url.filename);\r\n\t\t\t\t\t\t\tipt.attr(\"url\",url.url);\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t\tif(url.media_id){\r\n\t\t\t\t\t\t\tif(img.length > 0){\r\n\t\t\t\t\t\t\t\timg.get(0).src = \"\";\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t\tipt.val(url.media_id);\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t}, null, options);\r\n\t\t\t\t});\r\n\t\t\t}\r\n\t\t\tfunction deleteImage(elm){\r\n\t\t\t\trequire([\"jquery\"], function(\$){\r\n\t\t\t\t\t\$(elm).prev().attr(\"src\", \"./resource/images/nopic.jpg\");\r\n\t\t\t\t\t\$(elm).parent().prev().find(\"input\").val(\"\");\r\n\t\t\t\t});\r\n\t\t\t}\r\n\t\t</script>";
        define("TPL_INIT_TINY_IMAGE", true);
    }
    $s .= "\r\n\t\t<div class=\"input-group " . $options["class_extra"] . "\">\r\n\t\t\t<div class=\"input-group-addon\">\r\n\t\t\t\t<img src=\"" . $val . "\" onerror=\"this.src='" . $default . "'; this.title='图片未找到.'\" width=\"20\" height=\"20\" />\r\n\t\t\t</div>\r\n\t\t\t<input type=\"text\" name=\"" . $name . "\" value=\"" . $value . "\" class=\"form-control\" autocomplete=\"off\">\r\n\t\t\t<span class=\"input-group-btn\">\r\n\t\t\t\t<button class=\"btn btn-default\" type=\"button\" onclick=\"showImageDialog(this);\">选择图片</button>\r\n\t\t\t</span>\r\n\t\t</div>";
    return $s;
}
function tpl_form_field_store($name, $value = "", $option = array("mutil" => 0))
{
    global $_W;
    if (empty($default)) {
        $default = "./resource/images/nopic.jpg";
    }
    if (!is_array($value)) {
        $value = intval($value);
        $value = array($value);
    }
    $value_ids = implode(",", $value);
    $stores_temp = pdo_fetchall("select id, title, logo from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid and id in (" . $value_ids . ")", array(":uniacid" => $_W["uniacid"]));
    $stores = array();
    if (!empty($stores_temp)) {
        foreach ($stores_temp as $row) {
            $row["logo"] = tomedia($row["logo"]);
            $stores[] = $row;
        }
    }
    $definevar = "TPL_INIT_TINY_STORE";
    $function = "showStoreDialog";
    if (!empty($option["mutil"])) {
        $definevar = "TPL_INIT_TINY_MUTIL_STORE";
        $function = "showMutilStoreDialog";
    }
    $s = "";
    if (!defined($definevar)) {
        $option_json = json_encode($option);
        $s = "\r\n\t\t<script type=\"text/javascript\">\r\n\t\t\tfunction " . $function . "(elm) {\r\n\t\t\t\tvar btn = \$(elm);\r\n\t\t\t\tvar value_cn = btn.parent().prev();\r\n\t\t\t\tvar logo = btn.parent().parent().next().find(\"img\");\r\n\t\t\t\tirequire([\"web/tiny\"], function(tiny){\r\n\t\t\t\t\ttiny.selectstore(function(stores, option){\r\n\t\t\t\t\t\tif(option.mutil == 1) {\r\n\t\t\t\t\t\t\t\$.each(stores, function(idx, store){\r\n\t\t\t\t\t\t\t\t\$(elm).parent().parent().next().append('<div class=\"multi-item\"><img onerror=\"this.src=\\'./resource/images/nopic.jpg\\'; this.title=\\'图片未找到.\\'\" src=\"'+store.logo+'\" class=\"img-responsive img-thumbnail\"><input type=\"hidden\" name=\"'+name+'[]\" value=\"'+store.id+'\"><em class=\"close\" title=\"删除该门店\" onclick=\"deleteStore(this)\">×</em><span>'+store.title+'</span></div>');\r\n\t\t\t\t\t\t\t});\r\n\t\t\t\t\t\t} else {\r\n\t\t\t\t\t\t\tvalue_cn.val(stores.title);\r\n\t\t\t\t\t\t\tlogo[0].src = stores.logo;\r\n\t\t\t\t\t\t\tlogo.prev().val(stores.id);\r\n\t\t\t\t\t\t\tlogo.next().removeClass(\"hide\").html(stores.title);\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t}, " . $option_json . ");\r\n\t\t\t\t});\r\n\t\t\t}\r\n\r\n\t\t\tfunction deleteMutilStore(elm){\r\n\t\t\t\t\$(elm).parent().remove();\r\n\t\t\t}\r\n\t\t</script>";
        define($definevar, true);
    }
    $s .= "\r\n\t\t<div class=\"input-group\">\r\n\t\t\t<input type=\"text\" class=\"form-control store-cn\" readonly value=\"" . $stores[0]["title"] . "\">\r\n\t\t\t<span class=\"input-group-btn\">\r\n\t\t\t\t<button class=\"btn btn-default\" type=\"button\" onclick=\"" . $function . "(this);\">选择商家</button>\r\n\t\t\t</span>\r\n\t\t</div>";
    if (empty($option["mutil"])) {
        $s .= "\r\n\t\t<div class=\"input-group single-item\" style=\"margin-top:.5em;\">\r\n\t\t\t<input type=\"hidden\" name=\"" . $name . "\" value=\"" . $value[0] . "\">\r\n\t\t\t<img src=\"" . $stores[0]["logo"] . "\" onerror=\"this.src='" . $default . "'; this.title='图片未找到.'\" class=\"img-responsive img-thumbnail\" width=\"150\" />\r\n\t\t";
        if (empty($stores[0]["title"])) {
            $s .= "<span class=\"hide\"></span>";
        } else {
            $s .= "<span>" . $stores[0]["title"] . "</span>";
        }
        $s .= "</div>";
    } else {
        $s .= "<div class=\"input-group multi-img-details\">";
        foreach ($stores as $store) {
            $s .= "\r\n\t\t\t<div class=\"multi-item\">\r\n\t\t\t\t<img src=\"" . $store["logo"] . "\" title=\"" . $store["title"] . "\" onerror=\"this.src='./resource/images/nopic.jpg'; this.title='图片未找到.'\" class=\"img-responsive img-thumbnail\">\r\n\t\t\t\t<input type=\"hidden\" name=\"" . $name . "[]\" value=\"" . $store["id"] . "\">\r\n\t\t\t\t<em class=\"close\" title=\"删除该门店\" onclick=\"deleteMutilStore()\">×</em>\r\n\t\t\t\t<span>" . $store["title"] . "</span>\r\n\t\t\t</div>";
        }
        $s .= "</div>";
    }
    return $s;
}
function tpl_form_field_mutil_store($name, $value = "")
{
    return tpl_form_field_store($name, $value, $option = array("mutil" => 1));
}
function tpl_form_field_goods($name, $value = "", $option = array("mutil" => 0, "sid" => 0, "ignore" => array()))
{
    global $_W;
    if (!isset($option["mutil"])) {
        $option["mutil"] = 0;
    }
    if (empty($default)) {
        $default = "./resource/images/nopic.jpg";
    }
    if (!is_array($value)) {
        $value = intval($value);
        $value = array($value);
    }
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $value_ids = implode(",", $value);
    $condition .= " and id in (" . $value_ids . ")";
    $goods_temp = pdo_fetchall("select id, title, thumb from " . tablename("tiny_wmall_goods") . (string) $condition, $params);
    $goods = array();
    if (!empty($goods_temp)) {
        foreach ($goods_temp as $row) {
            $row["thumb"] = tomedia($row["thumb"]);
            $goods[] = $row;
        }
    }
    $definevar = "TPL_INIT_TINY_GOODS";
    $function = "showGoodsDialog";
    if (!empty($option["mutil"])) {
        $definevar = "TPL_INIT_TINY_MUTIL_GOODS";
        $function = "showMutilGoodsDialog";
    }
    $s = "";
    if (!defined($definevar)) {
        $option_json = json_encode($option);
        $s = "\r\n\t\t<script type=\"text/javascript\">\r\n\t\t\tfunction " . $function . "(elm) {\r\n\t\t\t\tvar btn = \$(elm);\r\n\t\t\t\tvar value_cn = btn.parent().prev();\r\n\t\t\t\tvar thumb = btn.parent().parent().next().find(\"img\");\r\n\t\t\t\ttiny.selectgoods(function(goods, option){\r\n\t\t\t\t\tif(option.mutil == 1) {\r\n\t\t\t\t\t\t\$.each(goods, function(idx, good){\r\n\t\t\t\t\t\t\t\$(elm).parent().parent().next().append('<div class=\"multi-item\"><img onerror=\"this.src=\\'./resource/images/nopic.jpg\\'; this.title=\\'图片未找到.\\'\" src=\"'+store.good+'\" class=\"img-responsive img-thumbnail\"><input type=\"hidden\" name=\"'+name+'[]\" value=\"'+good.id+'\"><em class=\"close\" title=\"删除该商品\" onclick=\"deleteStore(this)\">×</em><span>'+good.title+'</span></div>');\r\n\t\t\t\t\t\t});\r\n\t\t\t\t\t} else {\r\n\t\t\t\t\t\tvalue_cn.val(goods.title);\r\n\t\t\t\t\t\tthumb[0].src = goods.thumb;\r\n\t\t\t\t\t\tthumb.prev().val(goods.id);\r\n\t\t\t\t\t\tthumb.next().removeClass(\"hide\").html(goods.title);\r\n\t\t\t\t\t}\r\n\t\t\t\t}, " . $option_json . ");\r\n\t\t\t}\r\n\r\n\t\t\tfunction deleteMutilGoods(elm){\r\n\t\t\t\t\$(elm).parent().remove();\r\n\t\t\t}\r\n\t\t</script>";
        define($definevar, true);
    }
    $s .= "\r\n\t\t<div class=\"input-group\">\r\n\t\t\t<input type=\"text\" class=\"form-control store-cn\" readonly value=\"" . $goods[0]["title"] . "\">\r\n\t\t\t<span class=\"input-group-btn\">\r\n\t\t\t\t<button class=\"btn btn-default\" type=\"button\" onclick=\"" . $function . "(this);\">选择商品</button>\r\n\t\t\t</span>\r\n\t\t</div>";
    if (empty($option["mutil"])) {
        $s .= "\r\n\t\t<div class=\"input-group single-item\" style=\"margin-top:.5em;\">\r\n\t\t\t<input type=\"hidden\" name=\"" . $name . "\" value=\"" . $value[0] . "\">\r\n\t\t\t<img src=\"" . $goods[0]["thumb"] . "\" onerror=\"this.src='" . $default . "'; this.title='图片未找到.'\" class=\"img-responsive img-thumbnail\" width=\"150\" />\r\n\t\t";
        if (empty($goods[0]["title"])) {
            $s .= "<span class=\"hide\"></span>";
        } else {
            $s .= "<span>" . $goods[0]["title"] . "</span>";
        }
        $s .= "</div>";
    } else {
        $s .= "<div class=\"input-group multi-img-details\">";
        foreach ($goods as $good) {
            $s .= "\r\n\t\t\t<div class=\"multi-item\">\r\n\t\t\t\t<img src=\"" . $good["thumb"] . "\" title=\"" . $good["title"] . "\" onerror=\"this.src='./resource/images/nopic.jpg'; this.title='图片未找到.'\" class=\"img-responsive img-thumbnail\">\r\n\t\t\t\t<input type=\"hidden\" name=\"" . $name . "[]\" value=\"" . $good["id"] . "\">\r\n\t\t\t\t<em class=\"close\" title=\"删除该商品\" onclick=\"deleteMutilStore()\">×</em>\r\n\t\t\t\t<span>" . $good["title"] . "</span>\r\n\t\t\t</div>";
        }
        $s .= "</div>";
    }
    return $s;
}
function tpl_form_field_mutil_goods($name, $value = "", $option = array("sid" => 0, "ignore" => array()))
{
    if (!isset($option["mutil"])) {
        $option["mutil"] = 1;
    }
    return tpl_form_field_goods($name, $value, $option);
}
function tpl_form_filter_hidden($ctrls, $do = "web")
{
    global $_W;
    $html = "\r\n\t\t<input type=\"hidden\" name=\"c\" value=\"site\">\r\n\t\t<input type=\"hidden\" name=\"a\" value=\"entry\">\r\n\t\t<input type=\"hidden\" name=\"m\" value=\"we7_wmall\">\r\n\t\t<input type=\"hidden\" name=\"i\" value=\"" . $_W["uniacid"] . "\">\r\n\t\t<input type=\"hidden\" name=\"do\" value=\"" . $do . "\"/>\r\n\t";
    list($ctrl, $ac, $op, $ta) = explode("/", $ctrls);
    if (!empty($ctrl)) {
        $html .= "<input type=\"hidden\" name=\"ctrl\" value=\"" . $ctrl . "\"/>";
        if (!empty($ac)) {
            $html .= "<input type=\"hidden\" name=\"ac\" value=\"" . $ac . "\"/>";
        }
        if (!empty($ac)) {
            $html .= "<input type=\"hidden\" name=\"op\" value=\"" . $op . "\"/>";
            if (!empty($ta)) {
                $html .= "<input type=\"hidden\" name=\"ta\" value=\"" . $ta . "\"/>";
            }
        }
    }
    return $html;
}
function tpl_form_field_tiny_account($name, $value = 0, $required = false)
{
    $account = array();
    if (!empty($value)) {
        $account = pdo_get("account_wechats", array("uniacid" => $value));
    }
    $s = "";
    if (!defined("TPL_INIT_TINY_ACCOUNT")) {
        $s = "\r\n\t\t<script type=\"text/javascript\">\r\n\t\t\tfunction showTinyAccountDialog(elm) {\r\n\t\t\t\tirequire([\"web/tiny\"], function(tiny){\r\n\t\t\t\t\tvar \$uniacid = \$(elm).parent().prev();\r\n\t\t\t\t\tvar \$name = \$(elm).parent().prev().prev();\r\n\t\t\t\t\ttiny.selectaccount(function(account){\r\n\t\t\t\t\t\t\$uniacid.val(account.uniacid);\r\n\t\t\t\t\t\t\$name.val(account.name);\r\n\t\t\t\t\t});\r\n\t\t\t\t});\r\n\t\t\t}\r\n\t\t</script>";
        define("TPL_INIT_TINY_ACCOUNT", true);
    }
    $s .= "\r\n\t<div class=\"input-group\">\r\n\t\t<input type=\"text\" name=\"" . $name . "_cn\" value=\"" . $account["name"] . "\" class=\"form-control\" autocomplete=\"off\" readonly>\r\n\t\t<input type=\"hidden\" name=\"" . $name . "\" value=\"" . $value . "\">\r\n\t\t<span class=\"input-group-btn\">\r\n\t\t\t<button class=\"btn btn-default\" type=\"button\" onclick=\"showTinyAccountDialog(this);\">选择公众号</button>\r\n\t\t</span>\r\n\t</div>\r\n\t";
    return $s;
}
function tpl_form_field_tiny_category_2level($name, $parents, $children, $parentid, $childid)
{
    $html = "\r\n\t\t<script type=\"text/javascript\">\r\n\t\t\twindow._" . $name . " = " . json_encode($children) . ";\r\n\t\t</script>";
    if (!defined("TPL_INIT_TINY_CATEGORY")) {
        $html .= "\r\n\t\t\t\t\t<script type=\"text/javascript\">\r\n\t\t\t\t\t\tfunction irenderCategory(obj, name){\r\n\t\t\t\t\t\t\tvar index = obj.options[obj.selectedIndex].value;\r\n\t\t\t\t\t\t\trequire(['jquery', 'util'], function(\$, u){\r\n\t\t\t\t\t\t\t\t\$selectChild = \$('#'+name+'_child');\r\n\t\t\t\t\t\t\t\tvar html = '<option value=\"0\">请选择二级分类</option>';\r\n\r\n\t\t\t\t\t\t\t\tif (!window['_'+name] || !window['_'+name][index]) {\r\n\t\t\t\t\t\t\t\t\t\$selectChild.html(html);\r\n\t\t\t\t\t\t\t\t\treturn false;\r\n\t\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t\t\tfor(var i in window['_'+name][index]){\r\n\t\t\t\t\t\t\t\t\thtml += '<option value=\"'+window['_'+name][index][i]['id']+'\">'+window['_'+name][index][i]['name']+'</option>';\r\n\t\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t\t\t\$selectChild.html(html);\r\n\t\t\t\t\t\t\t});\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t</script>\r\n\t\t\t\t\t";
        define("TPL_INIT_TINY_CATEGORY", true);
    }
    $html .= "<div class=\"row row-fix tpl-category-container\">\r\n\t<div class=\"col-xs-12 col-sm-6 col-md-6 col-lg-6\">\r\n\t\t<select class=\"form-control tpl-category-parent\" id=\"" . $name . "_parent\" name=\"" . $name . "[parentid]\" onchange=\"irenderCategory(this,'" . $name . "')\">\r\n\t\t\t\t\t<option value=\"0\">请选择一级分类</option>";
    $ops = "";
    foreach ($parents as $row) {
        $html .= "\r\n\t\t\t\t\t<option value=\"" . $row["id"] . "\" " . ($row["id"] == $parentid ? "selected=\"selected\"" : "") . ">" . $row["name"] . "</option>";
    }
    $html .= "\r\n\t\t\t\t</select>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"col-xs-12 col-sm-6 col-md-6 col-lg-6\">\r\n\t\t\t\t<select class=\"form-control tpl-category-child\" id=\"" . $name . "_child\" name=\"" . $name . "[childid]\">\r\n\t\t\t\t\t<option value=\"0\">请选择二级分类</option>";
    if (!empty($parentid) && !empty($children[$parentid])) {
        foreach ($children[$parentid] as $row) {
            $html .= "\r\n\t\t\t\t\t<option value=\"" . $row["id"] . "\"" . ($row["id"] == $childid ? "selected=\"selected\"" : "") . ">" . $row["name"] . "</option>";
        }
    }
    $html .= "\r\n\t\t\t\t</select>\r\n\t\t\t</div>\r\n\t\t</div>\r\n\t";
    return $html;
}
function wxapp_urls($type = "wmall")
{
    global $_W;
    global $_GPC;
    $data = array();
    if ($type == "wmall") {
        $data["takeout"]["sys"] = array("title" => "外卖链接", "items" => array(array("title" => "平台首页", "url" => "pages/home/index"), array("title" => "搜索商家", "url" => "pages/home/search"), array("title" => "会员中心", "url" => "pages/member/mine"), array("title" => "我的订单", "url" => "pages/order/index"), array("title" => "我的代金券", "url" => "pages/member/coupon/index"), array("title" => "我的红包", "url" => "pages/member/redPacket/index"), array("title" => "我的收货地址", "url" => "pages/member/address"), array("title" => "我的收藏", "url" => "pages/member/favorite"), array("title" => "配送会员卡", "url" => "package/pages/deliveryCard/index"), array("title" => "领券中心", "url" => "pages/channel/coupon"), array("title" => "余额充值", "url" => "pages/member/recharge"), array("title" => "天天特价", "url" => "plugin/pages/bargain/index"), array("title" => "购物车", "url" => "pages/order/cart"), array("title" => "为您优选", "url" => "pages/channel/brand"), array("title" => "帮助中心", "url" => "pages/home/help"), array("title" => "客服中心", "url" => "pages/home/help")));
        if ($_W["we7_wmall"]["config"]["mall"]["store_use_child_category"] == 1) {
            $data["takeout"]["sys"]["items"][] = array("title" => "全部分类", "url" => "pages/home/allcategory");
        }
        $data["takeout"]["dis"] = array("title" => "优惠活动", "items" => array());
        $discounts = store_discounts();
        if (!empty($discounts)) {
            foreach ($discounts as $row) {
                $data["takeout"]["dis"]["items"][] = array("title" => $row["title"], "url" => ivurl("pages/home/category", array("dis" => $row["key"])));
            }
        }
        $data["other"] = array();
        if (check_plugin_perm("spread")) {
        $data["other"]["spread"] = array("title" => $_W["_plugins"]["spread"]["title"], "items" => array(array("title" => "推广中心", "url" => "pages/spread/index")));
    }
    if (check_plugin_perm("ordergrant")) {
        $data["other"]["ordergrant"] = array("title" =>  $_W["_plugins"]["ordergrant"]["title"], "items" => array(array("title" => "下单有礼", "url" => "package/pages/ordergrant/index")));
    }
        if (check_plugin_perm("shareRedpacket")) {
            $data["other"]["shareRedpacket"] = array("title" => $_W["_plugins"]["shareRedpacket"]["title"], "items" => array(array("title" => "分享有礼", "url" => "package/pages/shareRedpacket/index")));
        }
    if (check_plugin_perm("creditshop")) {
        $data["other"]["creditshop"] = array("title" => $_W["_plugins"]["creditshop"]["title"], "items" => array(array("title" => "积分商城", "url" => "pages/creditshop/index")));
    }
    if (check_plugin_perm("mealRedpacket")) {
        $data["other"]["mealRedpacket"] = array("title" =>$_W["_plugins"]["mealRedpacket"]["title"], "items" => array(array("title" => "套餐红包", "url" => "package/pages/mealRedpacket/meal"), array("title" => "套餐红包Plus", "url" => "package/pages/mealRedpacket/plus")));
    }
    if (check_plugin_perm("freeLunch")) {
        $data["other"]["freelunch"] = array("title" => $_W["_plugins"]["freeLunch"]["title"], "items" => array(array("title" => "霸王餐", "url" => "package/pages/freelunch/index")));
    }
    if (check_plugin_perm("errander")) {
        $data["errander"] = array(array("title" => "平台链接", "items" => array(array("title" => "跑腿首页", "url" => "pages/paotui/guide"), array("title" => "跑腿订单", "url" => "pages/paotui/order"))));
        $data["errander"]["scene"] = array("title" => "跑腿场景", "items" => array());
            $scenes = pdo_getall("tiny_wmall_errander_page", array("uniacid" => $_W["uniacid"], "type" => "scene"), array("id", "name"));
            if (!empty($scenes)) {
                foreach ($scenes as $scene) {
                    $data["errander"]["scene"]["items"][] = array("title" => $scene["name"], "url" => "pages/paotui/diy?id=" . $scene["id"]);
                }
            }
        }
        if (check_plugin_perm("diypage")) {
            $diypages = pdo_getall("tiny_wmall_diypage", array("uniacid" => $_W["uniacid"], "version" => 2), array("id", "name"));
            if (!empty($diypages)) {
                $data["diyPages"] = $diypages;
            }
        }
        if (check_plugin_perm("storebd")) {
        $data["other"]["storebd"] = array("title" => $_W["_plugins"]["storebd"]["title"], "items" => array(array("title" => "推广员入口", "url" => "package/pages/storebd/index")));
    }
    if (check_plugin_perm("gohome")) {
            $data["other"]["gohome"] = array("title" => $_W["_plugins"]["gohome"]["title"], "items" => array(array("title" => "生活圈首页", "url" => "gohome/pages/home/index"), array("title" => "订单列表", "url" => "gohome/pages/order/index"), array("title" => "我的收藏", "url" => "gohome/pages/member/favorite"), array("title" => "拼团首页", "url" => "gohome/pages/pintuan/index"), array("title" => "限时抢购首页", "url" => "gohome/pages/seckill/index"), array("title" => "砍价首页", "url" => "gohome/pages/kanjia/index"), array("title" => "我的砍价", "url" => "gohome/pages/kanjia/record"), array("title" => "好店首页", "url" => "gohome/pages/haodian/index")));
        $data["other"]["tongcheng"] = array("title" => "同城信息", "items" => array(array("title" => "同城首页", "url" => "gohome/pages/tongcheng/index"), array("title" => "同城搜索页", "url" => "gohome/pages/tongcheng/search"), array("title" => "信息发布首页", "url" => "gohome/pages/tongcheng/publish/index"), array("title" => "我的发布", "url" => "gohome/pages/tongcheng/publish/list")));
        }
        if (check_plugin_perm("svip")) {
            $data["other"]["svip"] = array("title" =>  $_W["_plugins"]["svip"]["title"], "items" => array(array("title" => "超级会员入口", "url" => "package/pages/svip/index"), array("title" => "超级会员个人中心", "url" => "package/pages/svip/mine")));
        }
        $data["operation"]["scanCode"] = array("title" => "扫码", "items" => array(array("title" => "扫码", "url" => "wx:scanCode")));
        $data["store"] = array(array("title" => "商户", "items" => array(array("title" => "门店详情", "url" => "pages/store/home?sid=" . $_GPC["__sid"]), array("title" => "点外卖", "url" => "pages/store/goods?sid=" . $_GPC["__sid"]), array("title" => "预定", "url" => "tangshi/pages/reserve/index?sid=" . $_GPC["__sid"]), array("title" => "当面付", "url" => "pages/store/paybill?sid=" . $_GPC["__sid"]), array("title" => "限时抢购", "url" => "pages/seckill/index?sid=" . $_GPC["__sid"]), array("title" => "排号", "url" => "tangshi/pages/assign/assign?sid=" . $_GPC["__sid"]))));
    } else {
        if ($type == "deliveryer") {
            $data["takeout"]["sys"] = array("title" => "订单", "items" => array(array("title" => "订单列表", "url" => "pages/order/takeout")));
            $data["store"]["sys"] = array("title" => "资产", "items" => array(array("title" => "我的账户", "url" => "pages/finance/index"), array("title" => "提现记录", "url" => "pages/finance/getcashList"), array("title" => "账户明细", "url" => "pages/finance/current"), array("title" => "申请提现", "url" => "pages/finance/getcash"), array("title" => "提现账户", "url" => "pages/finance/account")));
            $data["deliveryer"]["sys"] = array("title" => "统计", "items" => array(array("title" => "配送统计", "url" => "pages/statcenter/index"), array("title" => "外卖统计", "url" => "pages/statcenter/takeout")));
            if (check_plugin_perm("errander")) {
                $data["plugin"]["errander"] = array("title" => "跑腿", "items" => array(array("title" => "跑腿订单", "url" => "pages/paotui/index"), array("title" => "跑腿统计", "url" => "pages/statcenter/errander")));
            }
            $data["other"]["sys"] = array("title" => "其他", "items" => array(array("title" => "修改密码", "url" => "pages/member/setting"), array("title" => "我的", "url" => "pages/member/mine"), array("title" => "语音设置", "url" => "pages/member/phonic"), array("title" => "忘记密码", "url" => "pages/auth/forget"), array("title" => "我的评价", "url" => "pages/comment/list")));
        } else {
            if ($type == "manager") {
                $data["takeout"]["sys"] = array("title" => "订单", "items" => array(array("title" => "订单列表", "url" => "pages/order/index"), array("title" => "店内订单", "url" => "pages/order/tangshi/index")));
                $data["store"]["sys"] = array("title" => "商户", "items" => array(array("title" => "用户评价", "url" => "pages/service/comment"), array("title" => "店铺活动", "url" => "pages/activity/index"), array("title" => "全部商品", "url" => "pages/goods/index"), array("title" => "我的资产", "url" => "pages/finance/index"), array("title" => "店内桌台", "url" => "pages/tangshi/table"), array("title" => "排队", "url" => "pages/tangshi/assign"), array("title" => "店铺推广", "url" => "pages/advertise/index"), array("title" => "公告列表", "url" => "pages/news/notice"), array("title" => "账单", "url" => "pages/paybill/index")));
                $data["deliveryer"]["sys"] = array("title" => "统计", "items" => array(array("title" => "商户统计", "url" => "pages/statcenter/index"), array("title" => "营业统计", "url" => "pages/statcenter/order"), array("title" => "热门商品统计", "url" => "pages/statcenter/goods")));
                if (check_plugin_perm("gohome")) {
                    $data["plugin"]["gohome"] = array("title" => "生活圈", "items" => array(array("title" => "生活圈首页", "url" => "pages/gohome/index"), array("title" => "砍价列表", "url" => "pages/gohome/kanjia/goods/list"), array("title" => "拼团列表", "url" => "pages/gohome/pintuan/goods/list"), array("title" => "抢购列表", "url" => "pages/gohome/seckill/goods/list"), array("title" => "订单列表", "url" => "pages/gohome/order/index")));
                }
                $urls["other"]["sys"] = array("title" => "其他", "items" => array(array("title" => "基础设置", "url" => "pages/shop/index"), array("title" => "商户首页", "url" => "pages/shop/home"), array("title" => "账户设置", "url" => "pages/shop/account"), array("title" => "支付设置设置", "url" => "pages/shop/pill"), array("title" => "营业资质", "url" => "pages/shop/qualification"), array("title" => "商家中心", "url" => "pages/shop/setting"), array("title" => "更多设置", "url" => "pages/shop/settingMore"), array("title" => "语音提醒", "url" => "pages/shop/phonic")));
            } else {
                if ($type == "plateform") {
                    $data["takeout"]["sys"] = array("title" => "外卖", "items" => array(array("title" => "外卖订单", "url" => "pages/order/takeout"), array("title" => "当面付", "url" => "pages/paycenter/paybill"), array("title" => "售后", "url" => "pages/service/comment?"), array("title" => "统计", "url" => "pages/statcenter/index")));
                    $data["store"]["sys"] = array("title" => "商户", "items" => array(array("title" => "商户列表", "url" => "pages/merchant/store"), array("title" => "商户活动列表", "url" => "pages/merchant/activity/list"), array("title" => "提现申请记录", "url" => "pages/merchant/getcash"), array("title" => "账户明细记录", "url" => "pages/merchant/current"), array("title" => "商户入驻列表", "url" => "pages/merchant/settle"), array("title" => "商家回收站", "url" => "pages/merchant/storage"), array("title" => "投诉列表", "url" => "pages/merchant/report")));
                    $data["deliveryer"]["sys"] = array("title" => "配送员", "items" => array(array("title" => "配送员管理", "url" => "pages/deliveryer/index"), array("title" => "配送员列表", "url" => "pages/deliveryer/deliveryer"), array("title" => "提现申请记录", "url" => "pages/deliveryer/getcash"), array("title" => "账户明细记录", "url" => "pages/deliveryer/current"), array("title" => "配送员位置", "url" => "pages/deliveryer/location")));
                    if (check_plugin_perm("errander")) {
                        $data["plugin"]["errander"] = array("title" => "跑腿", "items" => array(array("title" => "跑腿管理", "url" => "pages/plugin/paotui/index"), array("title" => "跑腿订单", "url" => "pages/plugin/paotui/list"), array("title" => "跑腿设置", "url" => "pages/plugin/paotui/config")));
                    }
                    if (check_plugin_perm("agent")) {
                        $data["plugin"]["agent"] = array("title" => "区域代理", "items" => array(array("title" => "区域代理管理", "url" => "pages/plugin/agent/index"), array("title" => "代理列表", "url" => "pages/plugin/agent/agent"), array("title" => "提现记录", "url" => "pages/plugin/agent/getcash"), array("title" => "账户明细", "url" => "pages/plugin/agent/current")));
                    }
                    if (check_plugin_perm("creditshop")) {
                        $data["plugin"]["creditshop"] = array("title" => "积分商城", "items" => array(array("title" => "兑换列表", "url" => "pages/plugin/creditshop/order")));
                    }
                    if (check_plugin_perm("deliveryCard")) {
                        $data["plugin"]["deliveryCard"] = array("title" => "配送会员卡", "items" => array(array("title" => "购买记录", "url" => "pages/plugin/deliveryCard/order")));
                    }
                    if (check_plugin_perm("mealRedpacket")) {
                        $data["plugin"]["mealRedpacket"] = array("title" => "套餐红包", "items" => array(array("title" => "购买记录", "url" => "pages/plugin/mealRedpacket/order")));
                    }
                    if (check_plugin_perm("wheel")) {
                        $data["plugin"]["wheel"] = array("title" => "幸运大转盘", "items" => array(array("title" => "参与记录", "url" => "pages/plugin/wheel/record")));
                    }
                    if (check_plugin_perm("advertise")) {
                        $data["plugin"]["advertise"] = array("title" => "商户广告通", "items" => array(array("title" => "购买记录", "url" => "pages/plugin/advertise/order")));
                    }
                    $data["other"]["sys"] = array("title" => "其他", "items" => array(array("title" => "顾客列表", "url" => "pages/member/list"), array("title" => "系统设置", "url" => "pages/config/index"), array("title" => "更多", "url" => "pages/more/index"), array("title" => "我的", "url" => "pages/member/mine")));
                }
            }
        }
    }
    return $data;
}

?>