<?php
defined("IN_IA") or exit("Access Denied");

function qrcode_wxapp_url($params)
{
    global $_W;
    $path = "we7_wmall/wxappqrcode/" . $params["type"] . "/" . $_W["uniacid"] . "/" . $params["name"] . ".png";
    $allpath = tomedia($path);
    if (ifile_exists($allpath)) {
        return $allpath;
    }
    $params = array("url" => $params["url"], "scene" => $params["scene"], "name" => $path);
    $res = qrcode_wxapp_build($params);
    if (is_error($res)) {
        return error(-1, "生成小程序码失败:" . $res["message"]);
    }
    return tomedia($path);
}
function qrcode_wxapp_build($params = array())
{
    $url = $params["url"];
    if (empty($url)) {
        return error(-1, "链接不能为空");
    }
    $scene = $params["scene"];
  //  mload()->model("cloud");
	$content = newGetQrcode($url, $scene);
    if (is_error($content)) {
        return $content;
    }
    $name = ifile_write($content, $params["name"]);
    return $name;
}
function qrcode_wechat_build($params = array())
{
    global $_W;
    $scene_str = $params["scene_str"];
    $qrcode = pdo_fetch("SELECT * FROM " . tablename("qrcode") . " WHERE uniacid = :uniacid AND scene_str = :scene_str", array(":uniacid" => $_W["uniacid"], ":scene_str" => $scene_str));
    if (!empty($qrcode)) {
        $rule_keyword = pdo_get("rule_keyword", array("uniacid" => $_W["uniacid"], "content" => $qrcode["keyword"]));
        if (!empty($rule_keyword)) {
            $rule = pdo_get("rule", array("uniacid" => $_W["uniacid"], "id" => $rule_keyword["rid"]));
            if (!empty($rule)) {
                return $qrcode;
            }
        }
    }
    if ($_W["account"]["level"] != 4) {
        return error(-1, "您的公众号不是认证服务号，没有创建二维码的权限");
    }
    if (empty($params["qrcode_type"])) {
        return error(-1, "二维码类型不能为空");
    }
    if (empty($params["module"])) {
        $params["module"] = "we7_wmall";
    }
    if (empty($params["scene_str"])) {
        return error(-1, "二维码场景值不能为空");
    }
    if (empty($params["keyword"])) {
        $params["keyword"] = $params["scene_str"];
    }
    $acc = WeAccount::create($_W["acid"]);
    if ($params["qrcode_type"] == "fixed") {
        $barcode = array("expire_seconds" => "", "action_name" => "QR_LIMIT_STR_SCENE", "action_info" => array("scene" => array("scene_str" => $params["scene_str"])));
        $result = $acc->barCodeCreateFixed($barcode);
    } else {
        $barcode = array("expire_seconds" => $params["expire_seconds"] ? $params["expire_seconds"] : 2592000, "action_name" => "QR_STR_SCENE", "action_info" => array("scene" => array("scene_str" => $params["scene_str"])));
        $result = $acc->barCodeCreateDisposable($barcode);
    }
    if (is_error($result)) {
        return error(-1, "生成微信二维码出错,错误详情:" . $result["message"]);
    }
    $qrcode = array("uniacid" => $_W["uniacid"], "acid" => $_W["acid"], "qrcid" => "", "scene_str" => $params["scene_str"], "keyword" => $params["keyword"], "name" => $params["name"], "model" => $params["qrcode_type"] == "fixed" ? 1 : 2, "ticket" => $result["ticket"], "url" => $result["url"], "expire" => $result["expire_seconds"], "createtime" => TIMESTAMP, "status" => "1", "type" => "we7_wmall");
    pdo_insert("qrcode", $qrcode);
    $rule = array("uniacid" => $_W["uniacid"], "name" => $params["name"], "module" => "we7_wmall", "status" => 1);
    pdo_insert("rule", $rule);
    $rid = pdo_insertid();
    $keyword = array("uniacid" => $_W["uniacid"], "module" => "we7_wmall", "content" => $params["keyword"], "status" => 1, "type" => 1, "displayorder" => 1, "rid" => $rid);
    pdo_insert("rule_keyword", $keyword);
    $kid = pdo_insertid();
    $data = array("uniacid" => $_W["uniacid"], "sid" => 0, "type" => $params["type"] ? $params["type"] : "spread", "rid" => $rid, "table_id" => 0, "extra" => iserializer(array("uid" => $params["uid"])));
    pdo_insert("tiny_wmall_reply", $data);
    $reply_id = pdo_insertid();
    $qrcode = array("ticket" => $result["ticket"], "url" => $result["url"]);
    return $qrcode;
}
function qrcode_url($ticket)
{
    return "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . urlencode($ticket);
}
function qrcode_normal_build($params)
{
    global $_W;
    if (empty($params["url"])) {
        return error(-1, "链接不能为空");
    }
    $path = MODULE_ROOT . "/resource/poster/qrcode/" . $_W["uniacid"] . "/";
    if (!is_dir($path)) {
        load()->func("file");
        mkdirs($path);
    }
    $file = md5(base64_encode($params["url"])) . ".jpg";
    $qrcode_file = $path . $file;
    if (!is_file($qrcode_file)) {
        require IA_ROOT . "/framework/library/qrcode/phpqrcode.php";
        QRcode::png($params["url"], $qrcode_file, QR_ECLEVEL_L, 4);
    }
    return (string) $_W["siteroot"] . "addons/we7_wmall/resource/poster/qrcode/" . $_W["uniacid"] . "/" . $file;
}


function newGetQrcode($inurl,$scene){
	global $_W;
	$access_token = getWxAccessToken();
	$url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$access_token['access_token'];
	$post_data=
        array(
            'page' => $inurl,
            'scene' => $scene
	);
	$post_data = json_encode($post_data);
	$data = send_post($url,$post_data);
	//$result = $this->data_uri($data,'image/png');
	return $data;
}
function send_post( $url, $post_data ) {
	$options = array(
		'http' => array(
			'method'  => 'POST',
			'header'  => 'Content-type:application/json',
			//header 需要设置为 JSON
			'content' => $post_data,
			'timeout' => 60
			//超时时间
		)
	);
	$context = stream_context_create( $options );
	$result = file_get_contents( $url, false, $context );
	return $result;
}
function getWxAccessToken(){        
        global $_W;
	//var $config_info=array();
        $config = pdo_get("tiny_wmall_config", array("uniacid" => $_W["uniacid"]), array("sysset", "id"));
	$config_info = iunserializer($config["sysset"]);
        $config_wxapp = $config_info['wxapp'];
	$appid=$config_wxapp['basic']['key'];
        $appsecret=$config_wxapp['basic']['secret'];
//      $appid=$_W["we7_wxapp"]["config"]["wxapp"]["key"];
  //    $appsecret=$_W["we7_wxapp"]["config"]["wxapp"]["secret"];
	
	
	$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
	$access_token = makeRequest($url);
	$access_token = json_decode($access_token['result'],true);
	return $access_token;
}

function makeRequest($url, $params = array(), $expire = 0, $extend = array(), $hostIp = '')
{
    if (empty($url)) {
        return array('code' => '100');
    }

    $_curl = curl_init();
    $_header = array(
        'Accept-Language: zh-CN',
        'Connection: Keep-Alive',
        'Cache-Control: no-cache'
    );
    // 方便直接访问要设置host的地址
    if (!empty($hostIp)) {
        $urlInfo = parse_url($url);
        if (empty($urlInfo['host'])) {
            $urlInfo['host'] = substr(DOMAIN, 7, -1);
            $url = "http://{$hostIp}{$url}";
        } else {
            $url = str_replace($urlInfo['host'], $hostIp, $url);
        }
        $_header[] = "Host: {$urlInfo['host']}";
    }

    // 只要第二个参数传了值之后，就是POST的
    if (!empty($params)) {
        curl_setopt($_curl, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($_curl, CURLOPT_POST, true);
    }

    if (substr($url, 0, 8) == 'https://') {
        curl_setopt($_curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($_curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    }
    curl_setopt($_curl, CURLOPT_URL, $url);
    curl_setopt($_curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($_curl, CURLOPT_USERAGENT, 'API PHP CURL');
    curl_setopt($_curl, CURLOPT_HTTPHEADER, $_header);

    if ($expire > 0) {
        curl_setopt($_curl, CURLOPT_TIMEOUT, $expire); // 处理超时时间
        curl_setopt($_curl, CURLOPT_CONNECTTIMEOUT, $expire); // 建立连接超时时间
    }

    // 额外的配置
    if (!empty($extend)) {
        curl_setopt_array($_curl, $extend);
    }

    $result['result'] = curl_exec($_curl);
    $result['code'] = curl_getinfo($_curl, CURLINFO_HTTP_CODE);
    $result['info'] = curl_getinfo($_curl);
    if ($result['result'] === false) {
        $result['result'] = curl_error($_curl);
        $result['code'] = -curl_errno($_curl);
    }

    curl_close($_curl);
    return $result;
}
?>