<?php

function print_add_order($printDeviceInfo = array(), $order = array())
{
	global $_W;
	$printDeviceInfo['times'] = empty($printDeviceInfo['times']) ? 1 : $printDeviceInfo['times'];
	$order_table = 'tiny_wmall_order';

	if (in_array($order['order_type'], array('kanjia', 'pintuan', 'seckill'))) {
		$order_table = 'tiny_wmall_gohome_order';
	}

	if ($printDeviceInfo['type'] == 'feie') {
		$printContent = implode('<BR>', $printDeviceInfo['content']);
		$postdata = array('sn' => $printDeviceInfo['deviceno'], 'key' => $printDeviceInfo['key'], 'printContent' => $printContent, 'times' => $printDeviceInfo['times'], 'backurl' => $_W['siteroot'] . 'addons/we7_wmall/payment/print/notify.php');

		if ($printDeviceInfo['language'] == 'Uyghur') {
			$postdata['language'] = 'Uyghur';
		}

		$posturl = 'http://api163.feieyun.com/FeieServer/printOrderAction';
	}
	else if ($printDeviceInfo['type'] == 'feie_new') {
		$user = $printDeviceInfo['member_code'];
		$ukey = $printDeviceInfo['api_key'];
		$sign = sha1($user . $ukey . TIMESTAMP);
		$printContent = implode('<BR>', $printDeviceInfo['content']);
		$postdata = array('user' => $user, 'stime' => TIMESTAMP, 'sig' => $sign, 'apiname' => 'Open_printMsg', 'sn' => $printDeviceInfo['deviceno'], 'content' => $printContent, 'times' => $printDeviceInfo['times'], 'backurl' => $_W['siteroot'] . 'addons/we7_wmall/payment/print/notify.php', 'callbackurl' => $_W['siteroot'] . 'addons/we7_wmall/payment/print/notify.php');

		if ($printDeviceInfo['language'] == 'Uyghur') {
			$postdata['language'] = 'Uyghur';
		}

		$posturl = 'http://api.feieyun.cn/Api/Open/';
	}
	else if ($printDeviceInfo['type'] == 'feiyin') {
		$qrcode = str_replace(array('<QR>', '</QR>'), array('', ''), $printDeviceInfo['content']['qrcode']);
		$printDeviceInfo['content']['title'] = str_replace(array('<CB>', '</CB>'), array('<Font# Bold=0 Width=2 Height=2>', '</Font#>'), $printDeviceInfo['content']['title']);
		$printDeviceInfo['content']['pay'] = str_replace(array('<CB>', '</CB>'), array('<Font# Bold=0 Width=2 Height=2>', '</Font#>'), $printDeviceInfo['content']['pay']);
		$printDeviceInfo['content']['store'] = str_replace(array('<C>', '</C>'), array('<Font# Bold=0 Width=2 Height=2>', '</Font#>'), $printDeviceInfo['content']['store']);
		$printDeviceInfo['content']['print_header'] = str_replace(array('<C>', '</C>'), array('<Font# Bold=0 Width=2 Height=2>', '</Font#>'), $printDeviceInfo['content']['print_header']);
		$printDeviceInfo['content']['username'] = str_replace(array('<L>', '</L>'), array('<Font# Bold=0 Width=2 Height=2>', '</Font#>'), $printDeviceInfo['content']['username']);
		$printDeviceInfo['content']['mobile'] = str_replace(array('<L>', '</L>'), array('<Font# Bold=0 Width=2 Height=2>', '</Font#>'), $printDeviceInfo['content']['mobile']);
		$printDeviceInfo['content']['address'] = str_replace(array('<L>', '</L>'), array('<Font# Bold=0 Width=2 Height=2>', '</Font#>'), $printDeviceInfo['content']['address']);
		$printDeviceInfo['content']['note'] = str_replace(array('<L>', '</L>'), array('<Font# Bold=0 Width=2 Height=2>', '</Font#>'), $printDeviceInfo['content']['note']);

		if ($printDeviceInfo['content']['foinal_fee']) {
			$printDeviceInfo['content']['final_fee'] = '<Font# Bold=0 Width=2 Height=2>' . $printDeviceInfo['content']['final_fee'] . '</Font#>';
		}
		else {
			$printDeviceInfo['content']['total_fee'] = '<Font# Bold=0 Width=2 Height=2>' . $printDeviceInfo['content']['total_fee'] . '</Font#>';
		}

		$printDeviceInfo['content'] = implode('
', $printDeviceInfo['content']);
		$printDeviceInfo['content'] = str_replace(array('<CB>', '</CB>'), array('', ''), $printDeviceInfo['content']);
		$printDeviceInfo['content'] = str_replace(array('<C>', '</C>'), array('', ''), $printDeviceInfo['content']);
		$printDeviceInfo['content'] = str_replace(array('<L>', '</L>'), array('<Font# Bold=1 Width=1 Height=1>', '</Font#>'), $printDeviceInfo['content']);
		$postdata = array('memberCode' => $printDeviceInfo['member_code'], 'deviceNo' => $printDeviceInfo['deviceno'], 'reqTime' => number_format(1000 * time(), 0, '', ''), 'msgDetail' => $printDeviceInfo['content'], 'mode' => 2, 'msgNo' => $printDeviceInfo['orderindex']);
		$securityCode = $printDeviceInfo['member_code'] . $printDeviceInfo['content'] . $printDeviceInfo['deviceno'] . $printDeviceInfo['orderindex'] . $postdata['reqTime'] . $printDeviceInfo['key'];
		$postdata['securityCode'] = md5($securityCode);
		$posturl = 'http://my.feyin.net:80/api/sendMsg';
	}
	else if ($printDeviceInfo['type'] == 'AiPrint') {
		unset($printDeviceInfo['content']['qrcode']);
		$printDeviceInfo['content'] = implode('
', $printDeviceInfo['content']);
		$printDeviceInfo['content'] = str_replace(array('<CB>', '</CB>'), array('', ''), $printDeviceInfo['content']);
		$printDeviceInfo['content'] = str_replace(array('<C>', '</C>'), array('', ''), $printDeviceInfo['content']);
		$printDeviceInfo['content'] = str_replace(array('<L>', '</L>'), array('', ''), $printDeviceInfo['content']);
		$postdata = array('memberCode' => $printDeviceInfo['member_code'], 'deviceNo' => $printDeviceInfo['deviceno'], 'reqTime' => number_format(1000 * time(), 0, '', ''), 'msgDetail' => $printDeviceInfo['content'], 'mode' => 2, 'msgNo' => $printDeviceInfo['orderindex']);
		$securityCode = $printDeviceInfo['member_code'] . $printDeviceInfo['content'] . $printDeviceInfo['deviceno'] . $printDeviceInfo['orderindex'] . $postdata['reqTime'] . $printDeviceInfo['key'];
		$postdata['securityCode'] = md5($securityCode);
		$posturl = 'http://iprint.ieeoo.com/porderPrint';
	}
	else if ($printDeviceInfo['type'] == '365') {
		if (substr($printDeviceInfo['deviceno'], 0, 4) == 'kdt1') {
			$qrcode = str_replace(array('<QR>', '</QR>'), array('', ''), $printDeviceInfo['content']['qrcode']);
			$qrlength = chr(strlen($qrcode));
			$printDeviceInfo['content']['qrcode'] = '^Q' . $qrlength . $qrcode;
			array_unshift($printDeviceInfo['content'], '^N' . $printDeviceInfo['times'] . '^F1');
			$printDeviceInfo['content'] = str_replace(array('<CB>', '</CB>'), array('^H2', ''), $printDeviceInfo['content']);
			$printDeviceInfo['content'] = str_replace(array('<C>', '</C>'), array('^H2', ''), $printDeviceInfo['content']);
		}

		$printDeviceInfo['content'] = implode('
', $printDeviceInfo['content']);
		$printDeviceInfo['content'] = str_replace(array('<L>', '</L>'), array('', ''), $printDeviceInfo['content']);
		$postdata = array('deviceNo' => $printDeviceInfo['deviceno'], 'key' => $printDeviceInfo['key'], 'printContent' => $printDeviceInfo['content'], 'times' => $printDeviceInfo['times']);
		$posturl = 'http://open.printcenter.cn:8080/addOrder';
	}
	else if ($printDeviceInfo['type'] == 'yilianyun') {
		array_unshift($printDeviceInfo['content'], '**' . $printDeviceInfo['times']);
		$printDeviceInfo['content']['title'] = str_replace(array('<CB>', '</CB>'), array('<center>', '</center>'), $printDeviceInfo['content']['title']);
		$printDeviceInfo['content']['store'] = str_replace(array('<C>', '</C>'), array('<center>', '</center>'), $printDeviceInfo['content']['store']);
		$printDeviceInfo['content'] = implode('
', $printDeviceInfo['content']);
		$printDeviceInfo['content'] = str_replace(array('<QR>', '</QR>'), array('<q>', '</q>'), $printDeviceInfo['content']);
		$printDeviceInfo['content'] = str_replace(array('<L>', '</L>'), array('', ''), $printDeviceInfo['content']);
		$printDeviceInfo['content'] = str_replace(array('<CB>', '</CB>'), array('<center>', '</center>'), $printDeviceInfo['content']);
		$printDeviceInfo['content'] = str_replace(array('<C>', '</C>'), array('<center>', '</center>'), $printDeviceInfo['content']);
		$time = time();
		$sign = strtoupper(md5($printDeviceInfo['api_key'] . 'machine_code' . $printDeviceInfo['deviceno'] . 'partner' . $printDeviceInfo['member_code'] . 'time' . $time . $printDeviceInfo['key']));
		$postdata = array('partner' => $printDeviceInfo['member_code'], 'machine_code' => $printDeviceInfo['deviceno'], 'sign' => $sign, 'content' => $printDeviceInfo['content'], 'time' => $time);
		$postdata = http_build_query($postdata);
		$posturl = 'http://open.10ss.net:8888';
	}
	else if ($printDeviceInfo['type'] == 'qiyun') {
		$printDeviceInfo['content'] = str_replace(array('<C>', '</C>'), array('', ''), $printDeviceInfo['content']);
		$printDeviceInfo['content'] = str_replace(array('<CB>', '</CB>'), array('', ''), $printDeviceInfo['content']);
		$printDeviceInfo['content'] = str_replace(array('<L>', '</L>'), array('', ''), $printDeviceInfo['content']);
		$printDeviceInfo['content'] = str_replace(array('<N>', '</N>'), array('', ''), $printDeviceInfo['content']);
		$printDeviceInfo['content'] = implode('
', $printDeviceInfo['content']) . '




';
		$time = time();
		$sign = strtoupper(md5($printDeviceInfo['api_key'] . 'machine_code' . $printDeviceInfo['deviceno'] . 'partner' . $printDeviceInfo['member_code'] . 'time' . $time . $printDeviceInfo['key']));
		$postdata = array('partner' => $printDeviceInfo['member_code'], 'machine_code' => $printDeviceInfo['deviceno'], 'sign' => $sign, 'content' => $printDeviceInfo['content'], 'time' => $time);
		$postdata = http_build_query($postdata);
		$posturl = 'http://openapi.qiyunkuailian.com';
	}
	else if ($printDeviceInfo['type'] == 'xixun') {
		$printDeviceInfo['content'] = str_replace(array('<CB>', '</CB>'), array('', ''), $printDeviceInfo['content']);
		$printDeviceInfo['content'] = str_replace(array('<C>', '</C>'), array(' ', ' '), $printDeviceInfo['content']);
		$printDeviceInfo['content'] = str_replace(array('<L>', '</L>'), array('', ''), $printDeviceInfo['content']);
		$printDeviceInfo['content'] = str_replace(array('<QR>', '</QR>'), array('', ''), $printDeviceInfo['content']);
		$printDeviceInfo['content']['title'] = '<1D2101><1B6101>' . $printDeviceInfo['content']['title'];
		$printDeviceInfo['content']['store'] = '<1D2100><1B6101>' . $printDeviceInfo['content']['store'];

		if (!empty($printDeviceInfo['content']['print_header'])) {
			$printDeviceInfo['content']['print_header'] = '<1D2100><1B6101>' . $printDeviceInfo['content']['print_header'];
		}

		$printDeviceInfo['content']['pay'] = '<1D2101><1B6101>' . $printDeviceInfo['content']['pay'];

		if (!empty($printDeviceInfo['content']['note'])) {
			$printDeviceInfo['content']['note'] = '<1D2101><1B6100>' . $printDeviceInfo['content']['note'];
		}

		$printDeviceInfo['content']['username'] = '<1D2110><1B6100>' . $printDeviceInfo['content']['username'];
		$printDeviceInfo['content']['mobile'] = '<1D2110><1B6100>' . $printDeviceInfo['content']['mobile'];
		$printDeviceInfo['content']['address'] = '<1D2110><1B6100>' . $printDeviceInfo['content']['address'];

		if (!empty($printDeviceInfo['content']['print_footer'])) {
			$printDeviceInfo['content']['print_footer'] = '<1D2100><1B6101>' . $printDeviceInfo['content']['print_footer'];
		}

		if (!empty($printDeviceInfo['content']['qrcode'])) {
			$printDeviceInfo['content']['qrcode'] = '<1B2A>' . $printDeviceInfo['content']['qrcode'] . '<1B2A>';
			$printDeviceInfo['content']['end'] = '<1B2AD><1D2110><1B6101>' . $printDeviceInfo['content']['end'] . '<0D0A><0D0A><0D0A><0D0A><0D0A><0D0A><1D5642000A0A><1B2AD>';
		}
		else {
			$printDeviceInfo['content']['end'] = '<1D2110><1B6101>' . $printDeviceInfo['content']['end'] . '<0D0A><0D0A><0D0A><0D0A><0D0A><0D0A><1D5642000A0A>';
		}

		foreach ($printDeviceInfo['content'] as $printDeviceInfo['key'] => &$v) {
			if (strexists($printDeviceInfo['key'], 'goods_item_') || in_array($printDeviceInfo['key'], array('title', 'store', 'print_header', 'pay', 'username', 'mobile', 'address', 'print_footer', 'note', 'end', 'qrcode'))) {
				continue;
			}

			$v = '<1D2100><1B6100>' . $v;
		}

		$printDeviceInfo['content'] = implode('<0D0A>', $printDeviceInfo['content']);
		$printDeviceInfo['content'] = '<1B40><1B40><1B40>' . $printDeviceInfo['content'];
		$posturl = 'http://115.28.15.113:61111';

		if (empty($order['print_sn'])) {
			$print_sn = random(10, true);
			$order['print_sn'] = $print_sn;
			pdo_update($order_table, array('print_sn' => $print_sn), array('id' => $order['id']));
		}

		$postdata = array('dingdan' => $printDeviceInfo['content'], 'dingdanID' => $order['print_sn'], 'dayinjisn' => $printDeviceInfo['deviceno'], 'pages' => $printDeviceInfo['times'], 'replyURL' => $_W['siteroot'] . 'addons/we7_wmall/payment/print/notify.php');
	}
	else if ($printDeviceInfo['type'] == '365_s2') {
		$postdata = array('deviceNo' => $printDeviceInfo['deviceno'], 'key' => $printDeviceInfo['key'], 'printContent' => implode('<BR>', $printDeviceInfo['content']), 'times' => $printDeviceInfo['times']);
		$posturl = 'http://open.printcenter.cn:8080/addOrder';
	}
	else {
		if ($printDeviceInfo['type'] == 'jinyun') {
			unset($printDeviceInfo['qrcode']);
			$printDeviceInfo['content']['title'] = str_replace(array('<CB>', '</CB>'), array('<AM><S4>', '
</S4></AM>'), $printDeviceInfo['content']['title']);
			$printDeviceInfo['content'] = implode('
', $printDeviceInfo['content']);
			$printDeviceInfo['content'] = str_replace(array('<C>', '</C>'), array('<AM>', '
</AM>'), $printDeviceInfo['content']);
			$printDeviceInfo['content'] = str_replace(array('<CB>', '</CB>'), array('<AM><S2>', '
</S2></AM>'), $printDeviceInfo['content']);
			$printDeviceInfo['content'] = str_replace(array('<L>', '</L>'), array('<S3>', '</S3>'), $printDeviceInfo['content']);
			$printDeviceInfo['content'] = str_replace(array('<N>', '</N>'), array('', ''), $printDeviceInfo['content']);
			$print_params = array('seri_num' => $printDeviceInfo['print_no'], 'print_data' => $printDeviceInfo['content'], 'print_type' => '');
			$postdata = array('timestamp' => TIMESTAMP, 'action' => 'print', 'token' => $printDeviceInfo['member_code'], 'data' => base64_encode(json_encode($print_params)));
			ksort($postdata);
			$sign_str = '';

			foreach ($postdata as $key => $value) {
				$sign_str .= $key . $value;
			}

			$sign_str .= $printDeviceInfo['api_key'];
			$sign_str = md5($sign_str);
			$postdata['sign'] = $sign_str;
			$posturl = 'http://www.jinyunzn.com/api/print/index.php';
		}
	}

	if ($printDeviceInfo['type'] == 'feiyin' || $printDeviceInfo['type'] == 'AiPrint') {
		$response = ihttp_post($posturl, $postdata);

		if (is_error($response)) {
			return error(-1, '错误: ' . $response['message']);
		}

		$result['responseCode'] = intval($response['content']);
		$result['orderindex'] = $printDeviceInfo['orderindex'];

		if ($result['responseCode'] == 0) {
			return $result['orderindex'];
		}

		$errors = print_code_msg();
		return error(-1, $errors[$printDeviceInfo['type']]['printorder'][$result['responseCode']]);
	}

	if ($printDeviceInfo['type'] == 'xixun') {
		$array = array();

		foreach ($postdata as $key => $val) {
			$array[] = $key . '=' . $val;
		}

		$postdata = implode('&', $array);
		$url = 'http://115.28.15.113:61111';
		$response = ihttp_post($url, $postdata);

		if (is_error($response)) {
			return $response;
		}

		if ($response['content'] != 'OK') {
			return error(-1, $response['content']);
		}

		return $response['content'];
	}

	$response = ihttp_post($posturl, $postdata);

	if (is_error($response)) {
		return error(-1, '错误: ' . $response['message']);
	}

	if (in_array($printDeviceInfo['type'], array('feie', '365'))) {
		$result = @json_decode($response['content'], true);

		if ($printDeviceInfo['type'] == 'feie') {
			pdo_update($order_table, array('print_sn' => trim($result['orderindex'])), array('id' => $order['id']));
		}
	}
	else {
		if ($printDeviceInfo['type'] == 'feie_new') {
			$result = @json_decode($response['content'], true);

			if ($result['msg'] == 'ok') {
				pdo_update($order_table, array('print_sn' => trim($result['data'])), array('id' => $order['id']));
				return true;
			}

			return error(-1, $result['msg'] ? $result['msg'] : '未知');
		}

		if ($printDeviceInfo['type'] == 'qiyun') {
			$result = @json_decode($response['content'], true);

			if ($result['code'] == 200) {
				$result['responseCode'] = 0;
			}
			else {
				$result['responseCode'] = $result['code'];
				$result['responseMsg'] = $result['msg'];
			}
		}
		else if ($printDeviceInfo['type'] == 'yilianyun') {
			$result = @json_decode($response['content'], true);

			if ($result['state'] == 1) {
				$result['responseCode'] = 0;
				$result['orderindex'] = $result['id'];
			}
			else {
				$result['responseCode'] = $result['state'];
			}
		}
		else {
			if ($printDeviceInfo['type'] == '365_s2') {
				$result = @json_decode($response['content'], true);

				if ($result['responseCode']) {
					return error($result['responseCode'], $result['msg']);
				}

				$querydata = array('deviceNo' => $printDeviceInfo['deviceno'], 'key' => $printDeviceInfo['key'], 'orderindex' => $result['orderindex']);
				$queryurl = 'http://open.printcenter.cn:8080/queryOrder';
				$response_query = ihttp_post($queryurl, $querydata);

				if (is_error($response_query)) {
					return error(-1, '错误: ' . $response_query['message']);
				}

				$result_query = @json_decode($response_query['content'], true);

				if (!$result_query['responseCode']) {
					return true;
				}

				return error($result_query['responseCode'], $result_query['msg']);
			}

			if ($printDeviceInfo['type'] == 'jinyun') {
				$result = @json_decode($response['content'], true);

				if ($result['code'] == 0) {
					$result['responseCode'] = 0;
					$result['orderindex'] = $printDeviceInfo['orderindex'];
				}
				else {
					$result['responseCode'] = $result['message'];
				}
			}
			else {
				$result['responseCode'] = intval($response['content']);
				$result['orderindex'] = $printDeviceInfo['orderindex'];
			}
		}
	}

	if ($result['responseCode'] == 0 || $printDeviceInfo['type'] == '365' && $result['responseCode'] == 1) {
		return $result['orderindex'];
	}

	if (!empty($result['responseMsg'])) {
		return error(-1, $result['responseMsg']);
	}

	$errors = print_code_msg();
	return error(-1, $errors[$printDeviceInfo['type']]['printorder'][$result['responseCode']]);
}

function print_query_order_status($printer_type, $deviceno, $key, $member_code, $orderindex)
{
	if ($printer_type == 'feie') {
		$postdata = array('sn' => $deviceno, 'key' => $key, 'index' => $orderindex);
		$posturl = 'http://dzp.feieyun.com/FeieServer/queryOrderStateAction';
		$response = ihttp_post($posturl, $postdata);
	}
	else if ($printer_type == 'feiyin') {
		$postdata = array('memberCode' => $member_code, 'key' => $key, 'msgNo' => $orderindex, 'reqTime' => number_format(1000 * time(), 0, '', ''));
		$securityCode = $member_code . $postdata['reqTime'] . $key . $orderindex;
		$postdata['securityCode'] = md5($securityCode);
		$posturl = 'http://my.feyin.net/api/queryState?' . http_build_query($postdata);
		$response = ihttp_get($posturl);
	}
	else if ($printer_type == 'AiPrint') {
		$postdata = array('memberCode' => $member_code, 'msgNo' => $orderindex, 'reqTime' => number_format(1000 * time(), 0, '', ''));
		$securityCode = $member_code . $postdata['reqTime'] . $key . $orderindex;
		$postdata['securityCode'] = md5($securityCode);
		$posturl = 'http://iprint.ieeoo.com/porderqueryState?' . http_build_query($postdata);
		$response = ihttp_get($posturl);
	}
	else {
		if ($printer_type == '365') {
			$postdata = array('deviceNo' => $deviceno, 'key' => $key, 'orderindex' => $orderindex);
			$posturl = 'http://open.printcenter.cn:8080/queryOrder';
			$response = ihttp_post($posturl, $postdata);
		}
	}

	if (is_error($response)) {
		return error(-1, '错误: ' . $response['message']);
	}

	if (in_array($printer_type, array('feie', '365'))) {
		$result = @json_decode($response['content'], true);
	}
	else {
		$result['responseCode'] = intval($response['content']);
	}

	$status = 2;

	if (in_array($printer_type, array('feie', '365'))) {
		if ($result['responseCode'] == 0) {
			if ($printer_type == 'feie') {
				$status = $result['msg'] == '已打印' ? 1 : 2;
			}
			else {
				$status = 1;
			}
		}
	}
	else {
		if ($result['responseCode'] == 1) {
			$status = 1;
		}
	}

	return $status;
}

function print_query_printer_status($printer)
{
	if ($printer['type'] == 'feie') {
		$postdata = array('sn' => $printer['print_no'], 'key' => $printer['key']);
		$posturl = 'http://dzp.feieyun.com/FeieServer/queryPrinterStatusAction';
		$response = ihttp_post($posturl, $postdata);
	}
	else if ($printer['type'] == 'feie_new') {
		$sign = sha1($printer['member_code'] . $printer['api_key'] . TIMESTAMP);
		$postdata = array('user' => $printer['member_code'], 'stime' => TIMESTAMP, 'sig' => $sign, 'apiname' => 'Open_queryPrinterStatus', 'sn' => $printer['print_no']);
		$posturl = 'http://api.feieyun.cn/Api/Open/';
		$response = ihttp_post($posturl, $postdata);
	}
	else if ($printer['type'] == 'feiyin') {
		$postdata = array('memberCode' => $printer['member_code'], 'reqTime' => number_format(1000 * time(), 0, '', ''));
		$securityCode = $printer['member_code'] . $postdata['reqTime'] . $printer['key'];
		$postdata['securityCode'] = md5($securityCode);
		$posturl = 'http://my.feyin.net/api/listDevice?' . http_build_query($postdata);
		$response = ihttp_get($posturl);
	}
	else {
		if ($printer['type'] == '365' || $printer['type'] == '365_s2') {
			$postdata = array('deviceNo' => $printer['print_no'], 'key' => $printer['key']);
			$posturl = 'http://open.printcenter.cn:8080/queryPrinterStatus';
			$response = ihttp_post($posturl, $postdata);
		}
	}

	if (is_error($response)) {
		return error(-1, '错误: ' . $response['message']);
	}

	if (in_array($printer['type'], array('feie', '365', '365_s2'))) {
		$result = @json_decode($response['content'], true);
	}
	else {
		if (in_array($printer['type'], array('feie_new'))) {
			$result = @json_decode($response['content'], true);
			return $result['data'];
		}

		$result = intval($response['content']);
		if (is_numeric($result) && $result < 0) {
			$errors = print_code_msg();
			return $errors[$printer['type']]['qureystate'][$result];
		}

		$result = isimplexml_load_string($response['content']);
		$result = json_decode(json_encode($result), true);
		return $result['device']['deviceStatus'] . ',纸张状态:' . $result['device']['paperStatus'];
	}

	$errors = print_code_msg();
	if ($printer['type'] == 'feiyin' || $printer['type'] == '365' || $printer['type'] == '365_s2') {
		return $errors[$printer['type']]['qureystate'][$result['responseCode']];
	}

	return $result['msg'];
}

function print_2_Uyghur($content)
{
	$data = array('token' => '1b4667f54fawed9f9847665027ssd497c8848a91', 'text' => $content);
	$response = ihttp_post('http://feieyun.pengjisoft.com/get_wrapstring_uy.php', $data);

	if (is_error($response)) {
		return error(-1, '维文转码失败:' . $response['message']);
	}

	$result = @json_decode($response['content'], true);

	if ($result['result'] != 'success') {
		return error(-1, $result['code'] . ':' . $result['result']);
	}

	return $result['data'];
}

function print_code_msg()
{
	$data = array(
		'feie'      => array(
			'printorder' => array('服务器接收订单成功', '打印机编号错误', '服务器处理订单失败', '打印内容太长', '请求参数错误'),
			'qureyorder' => array('已打印/未打印', '请求参数错误', '服务器处理订单失败', '没有找到该索引的订单'),
			'qureystate' => array()
		),
		'feiyin'    => array(
			'printorder' => array(0 => '正常', -1 => 'IP地址不允许', -2 => '关键参数为空或请求方式不对', -3 => '客户编码不对', -4 => '安全校验码不正确', -5 => '请求时间失效', -6 => '订单内容格式不对', -7 => '重复的消息 （ msgNo 的值重复）', -8 => '消息模式不对', -9 => '服务器错误', -10 => '服务器内部错误', -111 => '打印终端不属于该账户'),
			'qureyorder' => array(0 => '打印请求/任务中队列中，等待打印', 1 => '打印任务已完成/请求数据已打印', 2 => '打印任务/请求失败', 9 => '打印任务/请求已发送', -1 => 'IP地址不允许', -2 => '关键参数为空或请求方式不对', -3 => '客户编码不正确', -4 => '安全校验码不正确', -5 => '请求时间失效。请求时间和请求到达飞印API的时间长超出安全范围。', -6 => '订单编号错误或者不存在'),
			'qureystate' => array(-1 => 'IP地址不允许', -2 => '关键参数为空或请求方式不对', -3 => '客户编码不正确', -4 => '安全校验码不正确', -5 => ' 同步应用服务器时间 了解更多飞印API的时间安全设置。')
		),
		365         => array(
			'printorder' => array(0 => '正常', 2 => '订单添加成功，但是打印机缺纸，无法打印', 3 => '订单添加成功，但是打印机不在线', 10 => '内部服务器错误', 11 => '参数不正确', 12 => '打印机未添加到服务器', 13 => '未添加为订单服务器', 14 => '订单服务器和打印机不在同一个组', 15 => '订单已经存在，不能再次打印'),
			'qureyorder' => array(0 => '打印成功', 1 => '正在打印中', 2 => '打印机缺纸', 3 => '打印机下线', 16 => '订单不存在'),
			'qureystate' => array(1 => '打印机正常在线', 2 => '打印机缺纸', 3 => '打印机下线', 4 => '错误的机器号或口令')
		),
		'365_s2'    => array(
			'printorder' => array(0 => '正常', 2 => '订单添加成功，但是打印机缺纸，无法打印', 3 => '订单添加成功，但是打印机不在线', 10 => '内部服务器错误', 11 => '参数不正确', 12 => '打印机未添加到服务器', 13 => '未添加为订单服务器', 14 => '订单服务器和打印机不在同一个组', 15 => '订单已经存在，不能再次打印'),
			'qureyorder' => array(0 => '打印成功', 1 => '正在打印中', 2 => '打印机缺纸', 3 => '打印机下线', 16 => '订单不存在'),
			'qureystate' => array(1 => '打印机正常在线', 2 => '打印机缺纸', 3 => '打印机下线', 4 => '错误的机器号或口令')
		),
		'yilianyun' => array(
			'printorder' => array(1 => '数据提交成功', 2 => '提交时间超时。验证你所提交的时间戳超过3分钟后拒绝接受', 3 => '参数有误', 4 => 'sign加密验证失败'),
			'qureyorder' => array('已打印/未打印', '请求参数错误', '服务器处理订单失败', '没有找到该索引的订单'),
			'qureystate' => array()
		)
	);
	return $data;
}

function print_printer_types()
{
	return array(
		'feie'     => array('text' => '飞鹅打印机', 'css' => 'label label-success'),
		'feie_new' => array('text' => '飞鹅打印机新接口', 'css' => 'label label-success'),
		'feiyin'     => array('text' => '飞印打印机', 'css' => 'label label-danger'),
		'365'         => array('text' => '365 S1打印机', 'css' => 'label label-warning'),
		'365_s2'     => array('text' => '365 S2打印机', 'css' => 'label label-success'),
		'AiPrint' => array('text' => 'AiPrint打印机', 'css' => 'label label-default'),
		'yilianyun' => array('text' => '易联云打印机', 'css' => 'label label-primary'),
		'qiyun'     => array('text' => '启云打印机', 'css' => 'label label-info'),
		'xixun'     => array('text' => '喜讯打印机', 'css' => 'label label-info'),
		'jinyun'     => array('text' => '进云打印机', 'css' => 'label label-info')
	);
}

defined('IN_IA') || exit('Access Denied');
load()->func('communication');

?>
