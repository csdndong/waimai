<?php
defined('IN_IA') or exit('Access Denied');
load()->func('communication');

class Fengniao{
	protected $app = null;

	public function __construct($app = '') {
		global $_W;

		$this->app = array(
			'sid' => 1,
			'app_id' => '1f0962e2-e5dc-4191-89b4-67cccd76c141',
			'secret' => '1540faff-6147-497f-83a0-6b58328824b9',
			'notify_url' => 'http://1.xinzuowl.com/payment/alipay/notify.php',
		);
	}

	public function getAccessToken() {
		global $_W;
		$cachekey = "fengniao:accesstoken:{$_W['uniacid']}";
		$cache = cache_load($cachekey);
		if(!empty($cache) && !empty($cache['access_token']) && ($cache['expire_time'] - TIMESTAMP > 1800)) {
			return $cache['access_token'];
		}
		if(empty($this->app['app_id']) || empty($this->app['secret'])) {
			return error('-1', '未填写蜂鸟的 app_id 或 secret！');
		}
		$params = array(
			'app_id' => $this->app['app_id'],
			'salt' => mt_rand(1000, 9999),
		);
		$seed = "app_id={$params['app_id']}&salt={$params['salt']}&secret_key=" . $this->app['secret'];
		$sign = md5(urlencode($seed));
		$params['signature'] = $sign;
		$response = ihttp_get('https://exam-anubis.ele.me/anubis-webapi/get_access_token?' . http_build_query($params));
		if(is_error($response)) {
			return error('-2', "获取access_token失败:{$response['message']}");
		}
		$result = @json_decode($response['content'], true);
		if($result['code'] != 200) {
			return error(-1, "获取access_token失败: {$result['msg']}, 错误信息: {$result['data']}");
		}
		$result = $result['data'];
		$record = array(
			'access_token' => $result['access_token'],
			'expire_time' => TIMESTAMP + $result['expire_time'] - 200
		);
		cache_write($cachekey, $record);
		return $result['access_token'];
	}

	public function buildSign($params) {
		$access_token = $this->getAccessToken();
		if(is_error($access_token)) {
			return $access_token;
		}
		unset($params['signature']);
		$seed = "app_id={$params['app_id']}&access_token={$access_token}&data={$params['data']}&salt={$params['salt']}";
		return md5($seed);
	}

	public function checkSign($params) {
		$access_token = $this->getAccessToken();
		if(is_error($access_token)) {
			return $access_token;
		}
		$sign = $params['signature'];
		if($sign == $this->buildSign($params)) {
			return true;
		}
		return false;
	}

	public function orderPush($id, $type = 'takeout') {
		global $_W;
		$order = order_fetch($id);
		if(empty($order)) {
			return error(-1, '订单不存在或已删除');
		}
		$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $order['sid']));
		if(empty($store)) {
			return error(-1, '门店不存在');
		}
		$goods = order_fetch_goods($id);
		if(empty($goods)) {
			return error(-1, '商品信息有误');
		}
		$goods_items = array();
		foreach($goods as $item) {
			$goods_items[] = array(
				'item_id' => '',
				'item_name' => $item['goods_title'],
				'item_quantity' => $item['goods_num'],
				'item_price' => $item['goods_original_price'],
				'item_actual_price' => $item['goods_price'],
				'item_size' => '',
				'item_remark' => '',
				'is_need_package' => 0,
				'is_agent_purchase' => 0,
				'agent_purchase_price' => '',
			);
		}
		$data = array(
			'partner_remark' => '',
			'partner_order_code' => $order['ordersn'],
			'notify_url' => $this->app['notify_url'],
			'order_type' => 1,
			'transport_info' => array(
				'transport_name' => $store['title'],
				'transport_address' => $store['address'],
				'transport_longitude' => $store['location_y'],
				'transport_latitude' => $store['location_x'],
				'position_source' => 3,
				'transport_tel' => $store['telephone'],
				'transport_remark' => '',
			),
			'order_add_time' => $order['paytime'] * 1000,
			'order_total_amount' => $order['total_fee'],
			'order_actual_amount' => $order['final_fee'],
			'order_weight' => 1, //订单总重量（kg），营业类型选定为果蔬生鲜、商店超市、其他三类时必填，大于0kg并且小于等于6kg
			'order_remark' => $order['note'],
			'is_invoiced' => empty($order['invoice']) ? 0 : 1,
			'invoice' => $order['invoice'],
			'order_payment_status' => $order['is_pay'],
			'order_payment_method' => 1,
			'is_agent_payment' => 0, //是否需要ele代收 0:否
			'require_payment_pay' => 0, //需要代收时客户应付金额, 如果需要ele代收 此项必填
			'goods_count' => $order['num'],
			'require_receive_time' => '',
			'receiver_info' => array(
				'receiver_name' => $order['username'],
				'receiver_primary_phone' => $order['mobile'],
				'receiver_second_phone' => '',
				'receiver_address' => $order['address'],
				'receiver_longitude' => $order['location_y'],
				'receiver_latitude' => $order['location_x'],
				'position_source' => 3,
			),
			'items_json' => $goods_items,
			'serial_number' => $order['serial_sn'],
		);
		$post = array(
			'app_id' => $this->app['app_id'],
			'data' => urlencode(json_encode($data, JSON_UNESCAPED_UNICODE)),
			'salt' => mt_rand(1000, 9999),
		);
		$post['signature'] = $this->buildSign($post);
		if(is_error($post['signature'])) {
			return $post['signature'];
		}
		$response = ihttp_request('https://exam-anubis.ele.me/anubis-webapi/v2/order', json_encode($post), array('Content-Type' => 'application/json'));
		if(is_error($response)) {
			return error('-2', "推送订单失败:{$response['message']}");
		}
		$result = @json_decode($response['content'], true);
		p($result);
		if($result['code'] != 200) {
			return error(-1, "推送订单失败: {$result['msg']}, 错误信息: {$result['data']}");
		}
		return true;
	}

	public function orderCancel($id, $type = 'takeout') {
		global $_W;
		$order = order_fetch($id);
		if(empty($order)) {
			return error(-1, '订单不存在或已删除');
		}
		$data = array(
			'partner_order_code' => $order['ordersn'],
			'order_cancel_reason_code' => 2, //订单取消原因代码(2:商家取消)
			'partner_order_code' => 0, //订单取消编码（0:其他, 1:联系不上商户, 2:商品已经售完, 3:用户申请取消, 4:运力告知不配送 让取消订单, 5:订单长时间未分配, 6:接单后骑手未取件）
			'order_cancel_description' => '',
			'order_cancel_time' => time() * 1000,
			'salt' => mt_rand(1000, 9999)
		);
		$post = array(
			'app_id' => $this->app['app_id'],
			'data' => urlencode(json_encode($data)),
			'salt' => mt_rand(1000, 9999),
		);
		$post['signature'] = $this->buildSign($post);
		if(is_error($post['signature'])) {
			return $post['signature'];
		}
		$response = ihttp_request('https://exam-anubis.ele.me/anubis-webapi/v2/order/cancel', json_encode($post), array('Content-Type' => 'application/json'));
		if(is_error($response)) {
			return error('-2', "取消订单失败:{$response['message']}");
		}
		$result = @json_decode($response['content'], true);
		if($result['code'] != 200) {
			return error(-1, "取消订单失败: {$result['msg']}, 错误信息: {$result['data']}");
		}
		return true;
	}


	public function orderQuery($id, $type = 'takeout') {
		global $_W;
		$order = order_fetch($id);
		if(empty($order)) {
			return error(-1, '订单不存在或已删除');
		}
		$post = array(
			'app_id' => $this->app['app_id'],
			'data' => urlencode(json_encode(array(
				'partner_order_code' => $order['ordersn'],
			))),
			'salt' => mt_rand(1000, 9999),
		);
		$post['signature'] = $this->buildSign($post);
		if(is_error($post['signature'])) {
			return $post['signature'];
		}
		$response = ihttp_request('https://exam-anubis.ele.me/anubis-webapi/v2/order/cancel', json_encode($post), array('Content-Type' => 'application/json'));
		if(is_error($response)) {
			return error('-2', "查询订单失败:{$response['message']}");
		}
		$result = @json_decode($response['content'], true);
		if($result['code'] != 200) {
			return error(-1, "查询订单失败: {$result['msg']}, 错误信息: {$result['data']}");
		}
		return $result['data'];
	}

	/*
	 * 商户将订单推送给蜂鸟配送开放平台后，可根据需要调用订单投诉接口对订单进行投诉操作。
	 * 目前，商户只能针对订单状态进入终态（即状态为3，4，5）并且满足96小时之内并且获取到调度或配送员信息（即状态为1）的订单发起投诉操作。
	 * */
	public function orderComplaint($id, $type = 'takeout') {
		global $_W;
		$order = order_fetch($id);
		if(empty($order)) {
			return error(-1, '订单不存在或已删除');
		}
		$post = array(
			'app_id' => $this->app['app_id'],
			'data' => urlencode(json_encode(array(
				'partner_order_code' => $order['ordersn'],
				'order_complaint_code' => $order['ordersn'],
				'order_complaint_desc' => '',
				'order_complaint_time' => time() * 1000,
			))),
			'salt' => mt_rand(1000, 9999),
		);
		$post['signature'] = $this->buildSign($post);
		if(is_error($post['signature'])) {
			return $post['signature'];
		}
		$response = ihttp_request('https://exam-anubis.ele.me/anubis-webapi/v2/order/complaint', json_encode($post), array('Content-Type' => 'application/json'));
		if(is_error($response)) {
			return error('-2', "订单投诉失败:{$response['message']}");
		}
		$result = @json_decode($response['content'], true);
		if($result['code'] != 200) {
			return error(-1, "订单投诉失败: {$result['msg']}, 错误信息: {$result['data']}");
		}
		return $result['data'];
	}

	/*
	 * 商户将订单推送给蜂鸟配送开放平台后，可根据需要调用订单骑手位置查询接口获取骑手所在位置。
	 * 目前，可以在已分配骑手状态之后调用此查询接口获取位置信息，若订单未分配骑手，或已经完成配送／异常，将无法再获取骑手位置。
	 * */
	public function orderCarrier($id, $type = 'takeout') {
		global $_W;
		$order = order_fetch($id);
		if(empty($order)) {
			return error(-1, '订单不存在或已删除');
		}
		$post = array(
			'app_id' => $this->app['app_id'],
			'data' => urlencode(json_encode(array(
				'partner_order_code' => $order['ordersn'],
			))),
			'salt' => mt_rand(1000, 9999),
		);
		$post['signature'] = $this->buildSign($post);
		if(is_error($post['signature'])) {
			return $post['signature'];
		}
		$response = ihttp_request('https://exam-anubis.ele.me/anubis-webapi/v2/order/carrier', json_encode($post), array('Content-Type' => 'application/json'));
		if(is_error($response)) {
			return error('-2', "查看骑手位置失败:{$response['message']}");
		}
		$result = @json_decode($response['content'], true);
		if($result['code'] != 200) {
			return error(-1, "查看骑手位置失败: {$result['msg']}, 错误信息: {$result['data']}");
		}
		return $result['data'];
	}

	public function chainStore($sid) {
		global $_W;
		$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $sid));
		if(empty($store)) {
			return error(-1, '门店不存在或已删除');
		}
		if(empty($store['telephone'])) {
			return error(-1, '门店信息不完善');
		}
		if(empty($store['address'])) {
			return error(-1, '门店地址不完善');
		}
		if(empty($store['location_y']) || empty($store['location_x'])) {
			return error(-1, '门店经纬度不完善');
		}
		$post = array(
			'app_id' => $this->app['app_id'],
			'data' => urlencode(json_encode(array(
				'name' => $store['title'],
				'contactPhone' => $store['telephone'],
				'address' => $store['address'],
				'longitude' => $store['location_y'],
				'latitude' => $store['location_x'],
			))),
			'salt' => mt_rand(1000, 9999),
		);
		$post['signature'] = $this->buildSign($post);
		if(is_error($post['signature'])) {
			return $post['signature'];
		}
		$response = ihttp_request('https://exam-anubis.ele.me/anubis-webapi/v2/chain_store', json_encode($post), array('Content-Type' => 'application/json'));
		if(is_error($response)) {
			return error('-2', "添加门店失败:{$response['message']}");
		}
		$result = @json_decode($response['content'], true);
		if($result['code'] != 200) {
			return error(-1, "添加门店失败: {$result['msg']}, 错误信息: {$result['data']}");
		}
		return true;
	}
}


