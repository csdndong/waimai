<?php
defined('IN_IA') or exit('Access Denied');

mload()->model('order.extra');
function set_order_data($id, $key, $value) {
	global $_W;
	$data = get_order_data($id);
	$keys = explode('.', $key);
	$counts = count($keys);
	if($counts == 1) {
		$data[$keys[0]] = $value;
	} elseif($counts == 2) {
		$data[$keys[0]][$keys[1]] = $value;
	} elseif($counts == 3) {
		$data[$keys[0]][$keys[1]][$keys[2]] = $value;
	}
	pdo_update('tiny_wmall_order', array('data' => iserializer($data)), array('uniacid' => $_W['uniacid'], 'id' => $id));
	return true;
}

function get_order_data($idOrorder, $key = '') {
	global $_W;
	$order = $idOrorder;
	if(!is_array($order) || empty($order['data'])) {
		$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $order), array('data', 'id'));
	}
	if(empty($order['data'])) {
		return array();
	}
	$data = iunserializer($order['data']);
	if(!is_array($data)) {
		$data = array();
	}
	if(empty($key)) {
		return $data;
	}
	$keys = explode('.', $key);
	$counts = count($keys);
	if($counts == 1) {
		return $data[$key];
	} elseif($counts == 2) {
		return $data[$keys[0]][$keys[1]];
	} elseif($counts == 3) {
		return $data[$keys[0]][$keys[1]][$keys[2]];
	}
}

function order_cancel_types($role = 'clerker') {
	$types = array(
		'clerker' => array(
			'fakeOrder' => '用户信息不符',
			'foodSoldOut' => '商品已经售完',
			'restaurantClosed' => '商家已经打烊',
			'distanceTooFar' => '超出配送范围',
			'restaurantTooBusy' => '商家现在太忙',
			'forceRejectOrder' => '用户申请取消',
			'deliveryFault' => '配送出现问题',
			'notSatisfiedDeliveryRequirement' => '不满足起送要求',
		),
		'manager' => array(
			'fakeOrder' => '用户信息不符',
			'foodSoldOut' => '商品已经售完',
			'restaurantClosed' => '商家已经打烊',
			'distanceTooFar' => '超出配送范围',
			'restaurantTooBusy' => '商家现在太忙',
			'forceRejectOrder' => '用户申请取消',
			'deliveryFault' => '配送出现问题',
			'notSatisfiedDeliveryRequirement' => '不满足起送要求',
		),
		'consumer' => array(),
	);
	return $types[$role];
}

function order_cancel_reason($id) {
	$log = pdo_fetch('select * from ' . tablename('tiny_wmall_order_status_log') . ' where oid = :id and status = 6 order by id desc', array(':id' => $id));
	if(empty($log)) {
		return '未知';
	}
	$reason = "未知";
	if(!empty($log['note'])) {
		$reason = "{$log['note']}";
	}
	if(!empty($log['role_cn'])) {
		$reason = "{$reason}。操作人:{$log['role_cn']}";
	}
	return $reason;
}
//order_insert_status_log
function order_insert_status_log($id, $type, $note = '', $role = '', $role_cn = '') {
	global $_W;
	if(empty($type)) {
		return false;
	}
	if(in_array($type, array('place_order', 'pay'))) {
		$order = order_fetch($id);
	}
	$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
	$notes = array(
		'place_order' => array(
			'status' => 1,
			'title' => '订单提交成功',
			'note' => "单号:{$order['ordersn']},请耐心等待商家确认",
			'ext' => array(
				array(
					'key' => 'pay_time_limit',
					'title' => '订单待支付',
					'note' => "请在订单提交后{$config_takeout['pay_time_limit']}分钟内完成支付",
				)
			)
		),
		'handle' => array(
			'status' => 2,
			'title' => '商户已确认订单',
			'note' => '正在为您准备商品',
		),
		'delivery_wait' => array(
			'status' => 3,
			'title' => '商品已准备就绪',
			'note' => '商品已准备就绪,正在分配配送员'
		),
		'delivery_ing' => array(
			'status' => 3,
			'title' => '商品已准备就绪',
			'note' => '商品已准备就绪,商家正在为您配送中'
		),
		'delivery_assign' => array(
			'status' => 4,
			'title' => '已分配配送员正为您取货中',
			'note' => ''
		),
		'delivery_instore' => array(
			'status' => 12,
			'title' => '配送员已到店',
			'note' => '配送员已到店, 正等待商家为您出餐'
		),
		'delivery_takegoods' => array(
			'status' => 12,
			'title' => '配送员已取货',
			'note' => '商家已出餐, 骑士将骑上战马为您急速送达'
		),
		'delivery_transfer' => array(
			'status' => 13,
			'title' => '配送员申请转单',
			'note' => ''
		),
		'end' => array(
			'status' => 5,
			'title' => '订单已完成',
			'note' => '任何意见和吐槽,都欢迎联系我们'
		),
		'cancel' => array(
			'status' => 6,
			'title' => '订单已取消',
			'note' => ''
		),
		'pay' => array(
			'status' => 7,
			'title' => '订单已支付',
			'note' => '支付成功.付款时间:' . date('Y-m-d H:i:s'),
			'ext' => array(
				array(
					'key' => 'handle_time_limit',
					'title' => '等待商户接单',
					'note' => "{$config_takeout['handle_time_limit']}分钟内商户未接单,将自动取消订单",
				)
			)
		),
		'remind' => array(
			'status' => 8,
			'title' => '商家已收到催单',
			'note' => '商家会尽快回复您的催单请求'
		),
		'remind_reply' => array(
			'status' => 9,
			'title' => '商家回复了您的催单',
			'note' => ''
		),
		'delivery_success' => array(
			'status' => 10,
			'title' => '订单配送完成',
			'note' => ''
		),
		'delivery_fail' => array(
			'status' => 10,
			'title' => '订单配送失败',
			'note' => ''
		),
		'pay_notice' => array(
			'status' => 1,
			'title' => '您的订单未支付，请尽快支付',
		),
		'direct_transfer' => array(
			'status' => 1,
			'title' => '配送员发起定向转单申请',
		),
		'direct_transfer_agree' => array(
			'status' => 1,
			'title' => '配送员同意接受转单',
		),
		'direct_transfer_refuse' => array(
			'status' => 1,
			'title' => '配送员拒绝接受转单',
		),
		'third_party_cancel_order' => array(
			'status' => 1,
			'title' => '第三方配送平台取消订单'
		)
	);
	if($type == 'pay' && $order['order_type'] == 3 && $order['pay_type'] == 'finishMeal') {
		$notes['pay'] =  array(
			'status' => 7,
			'title' => '订单支付',
			'note' => '您选择餐后付款，请在用餐完成后到门店收银台完成支付。',
		);
	}
	$title = $notes[$type]['title'];
	$note = $note ? $note : $notes[$type]['note'];
	$role = !empty($role) ? $role : $_W['role'];
	$role_cn = !empty($role_cn) ? $role_cn : $_W['role_cn'];
	$hash = random(20);
	if($type == 'delivery_assign') {
		$hash = md5("{$id}{$type}" . TIMESTAMP);
	}
	$data = array(
		'uniacid' => $_W['uniacid'],
		'oid' => $id,
		'status' => $notes[$type]['status'],
		'type' => $type,
		'role' => $role,
		'role_cn' => $role_cn,
		'title' => $title,
		'note' => $note,
		'addtime' => TIMESTAMP,
		'hash' => $hash,
	);
	pdo_insert('tiny_wmall_order_status_log', $data);
	$insert_id = pdo_insertid();
	if(empty($insert_id)) {
		return false;
	}
	if(!empty($notes[$type]['ext'])) {
		foreach($notes[$type]['ext'] as $val) {
			if($type == 'place_order' && in_array($order['order_type'], array(3, 4))) {
				continue;
			}
			if($val['key'] == 'pay_time_limit' && !$config_takeout['pay_time_limit']) {
				unset($val['note']);
			}
			elseif($val['key'] == 'handle_time_limit' && empty($config_takeout['handle_time_limit'])) {
				unset($val['note']);
			}
			$data = array(
				'uniacid' => $_W['uniacid'],
				'oid' => $id,
				'title' => $val['title'],
				'note' => $val['note'],
				'addtime' => TIMESTAMP,
				'hash' => random(20),
			);
			pdo_insert('tiny_wmall_order_status_log', $data);
		}
	}
	return true;
}

//order_fetch_status_log
function order_fetch_status_log($id) {
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_order_status_log') . ' WHERE uniacid = :uniacid and oid = :oid order by id asc', array(':uniacid' => $_W['uniacid'], ':oid' => $id), 'id');
	foreach($data as &$val) {
		$val['addtime_cn'] = date('Y-m-d H:i', $val['addtime']);
	}
	return $data;
}

function order_fetch_refund_log($id) {
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_order_refund_log') . ' WHERE uniacid = :uniacid and oid = :oid and order_type = :order_type order by id asc', array(':uniacid' => $_W['uniacid'], ':oid' => $id, ':order_type' => 'order'), 'id');
	if(!empty($data)) {
		foreach($data as &$val) {
			$val['addtime_cn'] = date('H:i', $val['addtime']);
		}
	}
	return $data;
}

//print_order
function order_print($id, $type = 'order', $extra = array()) {
	global $_W;
	$order= order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在');
	}
	$sid = intval($order['sid']);
	$store = store_fetch($order['sid'], array('title'));
	//获取该门店的所有打印机
	$prints = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_printer') . ' WHERE uniacid = :aid AND sid = :sid AND status = 1', array(':aid' => $_W['uniacid'], ':sid' => $sid));
	if(empty($prints)) {
		return error(-1, '没有有效的打印机');
	}
	mload()->model('print');
	if($type == 'collect') {
		$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $order['deliveryer_id']), array('title', 'mobile'));
		$content = array(
			"<CB>#{$order['serial_sn']} {$store['title']}</CB>",
			"<C>#{$order['serial_sn']} 号单已被配送员接单</C>",
			"<C>配送员:{$deliveryer['title']} {$deliveryer['mobile']}</C>",
		);
		foreach($prints as $li) {
			if(!empty($li['print_no'])) {
				//默认都只打印一次
				$li['deviceno'] = $li['print_no'];
				$li['content'] = $content;
				$li['orderindex'] = $order['ordersn'] . random(10, true);
				$status = print_add_order($li, $order);
			}
		}
	} else {
		if($order['data']['yinsihao_status'] == 1) {
			mload()->model('plugin');
			pload()->model('yinsihao');
			$yinsihao = yinsihao_bind($order, 'member', $order['ordersn']);
			if(is_error($yinsihao)) {
				slog('yinsihao', '隐私号绑定错误', array('order_id' => $id), "生成顾客隐私号错误: {$yinsihao['message']}");
				$order['mobile'] = '隐私号生成失败';
			} else {
				$order['mobile'] = "{$yinsihao['secret_mobile']}转{$yinsihao['extension']}";
			}
		}
		$num = 0;
		$grant = pdo_get('tiny_wmall_order_discount', array('oid' => $id, 'type' => 'grant'));
		foreach($prints as $li) {
			if(!empty($li['print_no'])) {
				$content = array(
					'title' => "<CB>#{$order['serial_sn']} {$_W['we7_wmall']['config']['mall']['title']}</CB>",
				);
				if($order['is_reserve'] == 1) {
					$content['is_reserve'] = '<CB>预订单</CB>';
				}
				if($order['zhunshibao_status'] > 0) {
					$content['zhunshibao_status'] = '<CB>超时赔付</CB>';
				}
				if($li['language'] == 'Uyghur') {
					$store_title = print_2_Uyghur($store['title']);
					if(!is_error($store_title)) {
						$store['title'] = $store_title;
					}
				}
				$content['store'] = "<C>*{$store['title']}*</C>";
				if(!empty($li['print_header'])) {
					$content['print_header'] = "<C>{$li['print_header']}</C>";
					if($li['type'] == '365') {
						$content['print_header'] = "{$li['print_header']}";
					}
				}
				if($order['is_pay'] == 1) {
					$content['pay'] = "<CB>--{$order['pay_type_cn']}--</CB>";
				}
				if($li['type'] == '365') {
					$content['store'] = "*{$store['title']}*";
					$content['pay'] = "--{$order['pay_type_cn']}--";
				}
				$li['data'] = iunserializer($li['data']);
				$format = $li['data']['format'];
				if(!empty($order['note'])) {
					$content['note'] = "<L>备注:{$order['note']}</L>";
					if(!empty($format) && !empty($format['note']) && $format['note']['font_size'] == 'normal') {
						$content['note'] = str_replace(array("<L>", "</L>"), array('', ''), $content['note']);
					}
					if($li['type'] == '365') {
						$content['note'] = "<C>备注:{$order['note']}</C>";
					}
					if($li['language'] == 'Uyghur') {
						$note = print_2_Uyghur($order['note']);
						if(!is_error($note)) {
							$content['note'] = $note;
						}
					}
				}
				$content[] = '--------------------------------';
				if($order['order_type'] == 1) {
					$content[] = "<L>送达时间:{$order['delivery_day']} {$order['delivery_time']}</L>";
				} elseif($order['order_type'] == 2) {
					$content[] = "自提时间:{$order['delivery_day']} {$order['delivery_time']}";
				} elseif($order['order_type'] >= 3) {
					$content[] = "<CB>{$order['table']['title']}</CB>";
				}
				$content[] = "下单时间:" . date('Y-m-d H:i', $order['addtime']);
				$content[] = "订单编号:{$order['ordersn']}";
				$content[] = '--------------------------------';
				$goods_font_size = 'large';
				if(!empty($format) && !empty($format['goods_title']) && $format['goods_title']['font_size'] == 'normal') {
					$goods_font_size = 'normal';
				}
				if($li['is_print_all'] == 1) {
					$order['goods'] = order_fetch_goods($order['id'], $li['print_label'], $type, $extra);
					if(!empty($order['goods']) || $order['order_type'] == 4) {
						$content['goods_header'] = '名称　　　　　　　数量　　　金额';
						$content[] = '********************************';
						foreach($order['goods'] as $di) {
							if(!empty($di['goods_number'])) {
								$di['goods_title'] = "{$di['goods_title']}-{$di['goods_number']}";
							}
							$title = $di['goods_title'];
							$title = iconv('utf-8', 'GBK//IGNORE', $title);
							$length = strlen($title);
							if($li['language'] == 'Uyghur') {
								$title_temp = print_2_Uyghur($di['goods_title']);
								if(!is_error($title_temp)) {
									$di['goods_title'] = $title_temp;
								}
								$length = 17;
							}
							if($li['type'] == 'xixun') {
								if($length > 16) {
									$content['goods_item_title_' . $di['id']] = "<1D2101><1B6100>{$di['goods_title']}";
									$str = '';
									$str .= '　　　　　　　　　<1D2101><1B6100>X' . str_pad($di['goods_num'], '3', ' ', STR_PAD_RIGHT);
									$str .= str_pad($di['goods_price'], '10', ' ', STR_PAD_LEFT);
									$content['goods_item_price_' . $di['id']] = $str;
								} else {
									$title = str_pad($title, '16', ' ', STR_PAD_RIGHT );
									$title = iconv('GBK', 'utf-8', $title);
									$str = '<1D2101><1B6100>' . $title;
									$str .= '　<1D2101><1B6100>X' . str_pad($di['goods_num'], '3', ' ', STR_PAD_RIGHT);
									$str .= str_pad($di['goods_price'], '10', ' ', STR_PAD_LEFT);
									$content['goods_item_' . $di['id']] = $str;
								}
							} elseif($li['type'] == '365') {
								if($length > 16) {
									$content[] = "<C>{$di['goods_title']}</C>";
									$str = '<C>';
									$str .= '　　　　　　　　　X' . str_pad($di['goods_num'], '3', ' ', STR_PAD_RIGHT);
									$str .= str_pad($di['goods_price'], '10', ' ', STR_PAD_LEFT). '</C>';
									$content[] = $str;
								} else {
									$title = str_pad($title, '16', ' ', STR_PAD_RIGHT);
									$title = iconv('GBK', 'utf-8', $title);
									$str = '<C>' . $title . '</C>';
									$str .= '　X' . str_pad($di['goods_num'], '3', ' ', STR_PAD_RIGHT);
									$str .= str_pad($di['goods_price'], '10', ' ', STR_PAD_LEFT);
									$content[] = $str;
								}
							} else {
								if($length > 16) {
									if($goods_font_size == 'normal') {
										$content[] = $di['goods_title'];
									} else {
										$content[] = "<L>{$di['goods_title']}</L>";
									}
									$str = '';
									$str .= '　　　　　　　　　<L>X' . str_pad($di['goods_num'], '3', ' ', STR_PAD_RIGHT) . '</L>';
									$str .= '<L>' . str_pad($di['goods_price'], '10', ' ', STR_PAD_LEFT). '</L>';
									if($goods_font_size == 'normal') {
										$str = str_replace(array("<L>", "</L>"), array('', ''), $str);
									}
									$content[] = $str;
								} else {
									$title = str_pad($title, '16', ' ', STR_PAD_RIGHT );
									$title = iconv('GBK', 'utf-8', $title);
									$str = '<L>' . $title . '</L>';
									$str .= '　<L>X' . str_pad($di['goods_num'], '3', ' ', STR_PAD_RIGHT) . '</L>';
									$str .= '<L>' . str_pad($di['goods_price'], '10', ' ', STR_PAD_LEFT). '</L>';
									if($goods_font_size == 'normal') {
										$str = str_replace(array("<L>", "</L>"), array('', ''), $str);
									}
									$content[] = $str;
								}
							}
						}
						$content[] = '********************************';
						$content[] = "订单类型:{$order['order_type_cn']}";
						if($order['box_price'] > 0) {
							$content[] = "餐盒　费:{$order['box_price']}元";
						}
						if($order['pack_fee'] > 0) {
							$content[] = "包装　费:{$order['pack_fee']}元";
						}
						if($order['delivery_fee'] > 0) {
							$content[] = "配送　费:{$order['delivery_fee']}元";
						}
						if($order['extra_fee'] > 0) {
							foreach($order['data']['extra_fee'] as $item) {
								$content[] = "{$item['name']}:{$item['fee']}元";
							}
						}

						if($order['order_type'] >= 3) {
							$content[] = '';
							$content[] = "合　　计:<L>{$order['total_fee']}元</L>";
							$content[] = '';
							if($order['discount_fee'] > 0) {
								$content[] = "线上优惠:<L>-{$order['discount_fee']}元</L>";
								$content[] = '';
								$content[] = "实际支付:<L>{$order['final_fee']}元</L>";
								$content[] = '';
							}
						} else {
							$content['total_fee'] = "合　　计:{$order['total_fee']}元";
							if($order['discount_fee'] > 0) {
								$content[] = "线上优惠:-{$order['discount_fee']}元";
								$content['final_fee'] = "实际支付:{$order['final_fee']}元";
								if($li['type'] == '365') {
									$content['final_fee'] = "<C>实际支付:{$order['final_fee']}元</C>";
								}
							}
						}
						if(!empty($grant)) {
							$content[] = "赠　　品:{$grant['note']}";
						}
						if($li['language'] == 'Uyghur') {
							if(!empty($order['username'])) {
								$username = print_2_Uyghur($order['username']);
								if(!is_error($username)) {
									$order['username'] = $username;
								}
							}
							if(!empty($order['address'])) {
								$address = print_2_Uyghur($order['address']);
								if(!is_error($address)) {
									$order['address'] = $address;
								}
							}
						}
						if($order['order_type'] == 1) {
							if($li['type'] == '365') {
								$content['username'] = "<C>{$order['username']}</C>";
								$content['mobile'] = "<C>{$order['mobile']}</C>";
								$content['address'] = "<C>{$order['address']}</C>";
							} else {
								$content['username'] = "<L>{$order['username']}</L>";
								$content['mobile'] = "<L>{$order['mobile']}</L>";
								$content['address'] = "<L>{$order['address']}</L>";
							}
						} elseif($order['order_type'] == 2) {
							$content['username'] = "下单　人:{$order['username']}";
							$content['mobile'] = "联系电话:{$order['mobile']}";
						} elseif($order['order_type'] == 3) {
							$content[] = "来客人数:{$order['person_num']}";
						} elseif($order['order_type'] == 4) {
							$content[] = "预定时间:{$order['reserve_time']}";
							$content[] = "桌台类型:{$order['table_category']['title']}";
							if($order['table_id'] > 0) {
								$content[] = "预定桌号:{$order['table']['title']}";
							}
						}
						if(!empty($order['invoice'])) {
							$content[] = "发票信息:{$order['invoice']}";
						}
						if(!empty($li['print_footer'])) {
							$content['print_footer'] = "<C>{$li['print_footer']}</C>";
							if($li['type'] == '365') {
								$content['print_footer'] = "{$li['print_footer']}";
							}
						}
						if($li['qrcode_type'] == 'delivery_assign') {
							$li['qrcode_link'] = imurl('delivery/order/takeout/detail', array('id' => $order['id'], 'r' => 'collect'), true);
						}
						if(!empty($li['qrcode_link'])) {
							if(strlen($li['qrcode_link']) > 110) {
								$link_status = longurl2short($li['qrcode_link']);
								if(is_error($link_status)) {
									$link_status = longurl2short($li['qrcode_link']);
									if(is_error($link_status)) {
										slog('longurl2short', '长网址转短网址错误', array('link' => $li['qrcode_link']), "生成小票配送员接单二维码错误: {$link_status['message']}");
									} else {
										$li['qrcode_link'] = $link_status;
									}
								} else {
									$li['qrcode_link'] = $link_status;
								}
							}
							$content['qrcode'] = "<QR>{$li['qrcode_link']}</QR>";
						}
						if($li['type'] == 'feie') {
							$content[] = implode('', array("\x1b","\x64","\x01","\x1b","\x70","\x30","\x1e","\x78"));
						} elseif($li['type'] == 'qiyun' && $li['print_nums'] > 0) {
							$content[] = "<N>{$li['print_nums']}</N>";
						}
						$content['end'] = "<CB>*****#{$order['serial_sn']}完*****</CB>";

						$li['deviceno'] = $li['print_no'];
						$li['content'] = $content;
						$li['times'] = $li['print_nums'];
						$li['orderindex'] = $order['ordersn'] . random(10, true);
						if(($li['type'] == 'feiyin' || $li['type'] == 'AiPrint') && $li['print_nums'] > 0) {
							for($i = 0; $i < $li['print_nums']; $i++) {
								$li['orderindex'] = $order['ordersn'] . random(10, true);
								$status = print_add_order($li, $order);
								if(!is_error($status)) {
									$num++;
								}
							}
						} else {
							$status = print_add_order($li, $order);
							if(!is_error($status)) {
								$num += $li['print_nums'];
							}
						}
					}
				} else {
					$order['goods'] = order_fetch_goods($order['id'], $li['print_label'], $type, $extra);
					if(!empty($order['goods']) || $order['order_type'] == 4) {
						$content = array();
						if(!empty($order['note'])) {
							$content['note'] = "<L>备注:{$order['note']}</L>";
							if(!empty($format) && !empty($format['note']) && $format['note']['font_size'] == 'normal') {
								$content['note'] = str_replace(array("<L>", "</L>"), array('', ''), $content['note']);
							}
							if($li['type'] == '365') {
								$content['note'] = "<C>备注:{$order['note']}</C>";
							}
							if($li['language'] == 'Uyghur') {
								$note = print_2_Uyghur($order['note']);
								if(!is_error($note)) {
									$content['note'] = $note;
								}
							}
						}
						$content[] = "订单　号:{$order['ordersn']}";
						$content[] = "下单时间:" . date('Y-m-d H:i', $order['addtime']);
						if($order['order_type'] == 1) {
							$content[] = "订单类型:外卖单";
							$content[] = "配送时间:{$order['delivery_day']} {$order['delivery_time']}";
						} elseif($order['order_type'] == 2) {
							$content[] = "订单类型:自提单";
							$content[] = "自提时间:{$order['delivery_day']} {$order['delivery_time']}";
						} elseif($order['order_type'] == 3) {
							$content[] = "订单类型:堂食单";
							$content[] = "桌　　号:{$order['table']['title']}";
						} elseif($order['order_type'] == 4) {
							$content[] = "订单类型:预定单";
							$content[] = "预定时间:{$order['reserve_time']}";
						}
						$content[] = '名称　　　　　　　数量　　　金额';
						$content[] = '********************************';
						foreach($order['goods'] as $di) {
							if(!empty($di['goods_number'])) {
								$di['goods_title'] = "{$di['goods_title']}-{$di['goods_number']}";
							}
							$title = $di['goods_title'];
							$title = iconv('utf-8', 'GBK//IGNORE', $title);
							$length = strlen($title);
							if($li['language'] == 'Uyghur') {
								$title_temp = print_2_Uyghur($di['goods_title']);
								if(!is_error($title_temp)) {
									$di['goods_title'] = $title_temp;
								}
								$length = 17;
							}
							if($length > 16) {
								$di['goods_title'] = "<L>{$di['goods_title']}</L>";
								$str = '';
								$str .= '　　　　　　　　　<L>X' . str_pad($di['goods_num'], '3', ' ', STR_PAD_RIGHT) . '</L>';
								$str .= '<L>' . str_pad($di['goods_price'], '10', ' ', STR_PAD_LEFT) . '</L>';
								if($goods_font_size == 'normal') {
									$di['goods_title'] = str_replace(array("<L>", "</L>"), array('', ''), $di['goods_title']);
									$str = str_replace(array("<L>", "</L>"), array('', ''), $str);
								}
								$goods_info = array(
									$di['goods_title'],
									$str,
									'********************************'
								);
							} else {
								$title = str_pad($title, '16', ' ', STR_PAD_RIGHT );
								$title = iconv('GBK', 'utf-8', $title);
								$str = "<L>{$title}</L>";
								$str .= '　<L>X' . str_pad($di['goods_num'], '3', ' ', STR_PAD_RIGHT) . '</L>';
								$str .= '<L>' . str_pad($di['goods_price'], '10', ' ', STR_PAD_LEFT) . '</L>';
								if($goods_font_size == 'normal') {
									$str = str_replace(array("<L>", "</L>"), array('', ''), $str);
								}
								$goods_info = array(
									$str,
									'********************************'
								);
							}
							$content_merge = array_merge($content, $goods_info);
							if($li['type'] == 'qiyun' && $li['print_nums'] > 0) {
								$content_merge[] = "<N>{$li['print_nums']}</N>";
							}
							$li['content'] = $content_merge;
							$li['deviceno'] = $li['print_no'];
							$li['times'] = $li['print_nums'];
							if(($li['type'] == 'feiyin' || $li['type'] == 'AiPrint') && $li['print_nums'] > 0) {
								for($i = 0; $i < $li['print_nums']; $i++) {
									$li['orderindex'] = $order['ordersn'] . random(10, true);
									$status = print_add_order($li, $order);
									if(!is_error($status)) {
										$num++;
									}
								}
							} else {
								$status = print_add_order($li, $order);
								if(!is_error($status)) {
									$num += $li['print_nums'];
								}
							}
						}
					}
				}
			}
		}
		if($num > 0) {
			pdo_query('UPDATE ' . tablename('tiny_wmall_order') . " SET print_nums = print_nums + {$num}, print_status = 1 WHERE uniacid = {$_W['uniacid']} AND id = {$order['id']}");
			return true;
		}
		if($order['print_status'] != 0) {
			pdo_update('tiny_wmall_order', array('print_status' => 0), array('id' => $order['id']));
		}
		slog('orderprint', '请求打印接口失败', '', "订单号:{$order['id']};错误信息:{$status['message']}");
		return error(-1, "请求打印接口失败:{$status['message']}");
	}
	return true;
}

function order_collect_type($orderOrType = '', $all = false) {
	$data = array(
		0 => array(
			'css' => 'label label-success',
			'text' => '自己抢单',
			'color' => ''
		),
		1 => array(
			'css' => 'label label-info',
			'text' => '系统派单',
			'color' => ''
		),
		2 => array(
			'css' => 'label label-info',
			'text' => '调度派单',
			'color' => ''
		),
		3 => array(
			'css' => 'label label-info',
			'text' => '转单',
			'color' => ''
		),
	);
	if(is_array($orderOrType)) {
		$type = $orderOrType['delivery_collect_type'];
	}
	if(isset($type)) {
		$data = $data[$type];
		if($type == 3) {
			$deliveryers = deliveryer_all();
			$data[3]['text'] = $deliveryers[$orderOrType['deliveryer_id']]['title'];
		}
		if(empty($all)) {
			$data = $data['text'];
		}
	}
	return $data;
}

function order_channel($channel = '', $all = false) {
	$data = array(
		'h5app' => array(
			'css' => 'label label-warning',
			'text' => '顾客APP下单',
			'color' => ''
		),
		'wxapp' => array(
			'css' => 'label label-info',
			'text' => '小程序下单',
			'color' => ''
		),
		'wap' => array(
			'css' => 'label label-success',
			'text' => '公众号下单',
			'color' => ''
		),
		'qianfan' => array(
			'css' => 'label label-success',
			'text' => '千帆APP',
			'color' => ''
		),
		'majia' => array(
			'css' => 'label label-success',
			'text' => '马甲APP',
			'color' => ''
		),
	);
	if(!empty($channel)) {
		$data = $data[$channel];
		if(empty($all)) {
			$data = $data['text'];
		}
	}
	return $data;
}

function order_status() {
	$data = array(
		'0' => array(
			'css' => '',
			'text' => '所有',
			'color' => ''
		),
		'1' => array(
			'css' => 'label label-default',
			'text' => '待确认',
			'color' => '',
		),
		'2' => array(
			'css' => 'label label-info',
			'text' => '处理中',
			'color' => 'color-primary'
		),
		'3' => array(
			'css' => 'label label-warning',
			'text' => '待配送',
			'color' => 'color-warning'
		),
		'4' => array(
			'css' => 'label label-warning',
			'text' => '配送中',
			'color' => 'color-warning'
		),
		'5' => array(
			'css' => 'label label-success',
			'text' => '已完成',
			'color' => 'color-success'
		),
		'6' => array(
			'css' => 'label label-danger',
			'text' => '已取消',
			'color' => 'color-danger'
		)
	);
	return $data;
}

function order_trade_status() {
	$data = array(
		'1' => array(
			'css' => 'label label-success',
			'text' => '交易成功',
		),
		'2' => array(
			'css' => 'label label-warning',
			'text' => '交易进行中',
		),
		'3' => array(
			'css' => 'label label-danger',
			'text' => '交易失败',
		),
		'4' => array(
			'css' => 'label label-default',
			'text' => '交易关闭',
		),
	);
	return $data;
}

function order_trade_type() {
	$data = array(
		'1' => array(
			'css' => 'label label-success',
			'text' => '外卖店内订单入账',
		),
		'2' => array(
			'css' => 'label label-danger',
			'text' => '申请提现',
		),
		'3' => array(
			'css' => 'label label-default',
			'text' => '其他变动',
		),
		'4' => array(
			'css' => 'label label-success',
			'text' => '买单订单入账',
		),
		'8' => array(
			'css' => 'label label-info',
			'text' => '生活圈订单入账',
		),
		'9' => array(
			'css' => 'label label-warning',
			'text' => '好店入驻订单入账',
		),
		'10' => array(
			'css' => 'label label-info',
			'text' => '同城发帖或置顶入账',
		),
	);
	return $data;
}

function order_delivery_status() {
	$data = array(
		'0' => array(
			'css' => '',
			'text' => '',
			'color' => ''
		),
		'3' => array(
			'css' => 'label label-warning',
			'text' => '待配送',
			'color' => 'color-warning'
		),
		'4' => array(
			'css' => 'label label-warning',
			'text' => '配送中',
			'color' => 'color-warning'
		),
		'5' => array(
			'css' => 'label label-success',
			'text' => '配送成功',
			'color' => 'color-success'
		),
		'6' => array(
			'css' => 'label label-danger',
			'text' => '配送失败',
			'color' => 'color-danger'
		),
		'7' => array(
			'css' => 'label label-danger',
			'text' => '待取货',
			'color' => 'color-danger'
		)
	);
	return $data;
}

function order_types() {
	$data = array(
		'1' => array(
			'css' => 'label label-success',
			'text' => '外卖',
			'color' => 'color-success'
		),
		'2' => array(
			'css' => 'label label-danger',
			'text' => '自提',
			'color' => 'color-danger'
		),
		'3' => array(
			'css' => 'label label-warning',
			'text' => '店内',
			'color' => 'color-info'
		),
		'4' => array(
			'css' => 'label label-info',
			'text' => '预定',
			'color' => 'color-info'
		),
	);
	return $data;
}

function order_reserve_type() {
	$data = array(
		'table' => array(
			'css' => 'label label-success',
			'text' => '只订座',
			'color' => 'color-success'
		),
		'order' => array(
			'css' => 'label label-danger',
			'text' => '预定商品',
			'color' => 'color-danger'
		),
	);
	return $data;
}

//order_status_notice
function order_status_notice($id, $status, $note = '') {
	global $_W;
	$status_arr = array(
		'handle', //处理中
		'delivery_assign', //抢单完成
		'delivery_instore', //确认到店
		'delivery_takegoods', //确认取货
		'delivery_ing', //商家直接设置订单为配送中
		'end', //已完成
		'cancel',//已取消
		'pay',//已支付
		'remind',
		'reply_remind',
		'delivery_notice',
		'pay_notice', //待支付
		'communicate', //沟通
	);
	if(!in_array($status, $status_arr)) {
		return false;
	}
	$type = $status;
	$order = order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在');
	}
	$store = store_fetch($order['sid'], array('title'));
	$deliveryer = array();
	if(!empty($order['deliveryer_id'])) {
		$deliveryer = pdo_get('tiny_wmall_deliveryer', array('id' => $order['deliveryer_id']));
	}
	$config_wxapp_basic = $_W['we7_wmall']['config']['wxapp']['basic'];

	if(!empty($order['openid'])) {
		$order_channel = $order['order_channel'];
		if($order_channel == 'wxapp') {
			if($config_wxapp_basic['wxapp_consumer_notice_channel'] == 'wechat' || (in_array($order['pay_type'], array('credit', 'delivery')) && empty($order['data']['formId'])) || (empty($order['data']['formId']) && $order['data']['prepay_times'] <= 0)) {
				mload()->model('member');
				$openid = member_wxapp2openid($order['openid']);
				if(!empty($openid)) {
					$order_channel = 'wap';
					$order['openid'] = $openid;
				}
			}
		}
		$acc = TyAccount::create($order['acid'], $order_channel);
		if($order_channel == 'wap') {
			if($type == 'pay') {
				$title = '您的订单已付款';
				$remark = array(
					"门店名称: {$store['title']}",
					"订单类型: {$order['order_type_cn']}",
					"支付方式: {$order['pay_type_cn']}",
					"支付时间: " . date('Y-m-d H: i', time()),
				);
			}
			elseif($type == 'pay_notice') {
				$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
				$limited = '尽快';
				if($config_takeout['pay_time_limit'] > 0) {
					$limited = '在'.($config_takeout['pay_time_limit'] - $config_takeout['pay_time_notice']).'分钟内';
				}
				$pay_notice = "您提交的{$_W['we7_wmall']['config']['mall']['title']}订单即将取消，请{$limited}完成支付。您可以点击本消息，选取未完成的订单进行支付。";
				$title = $pay_notice;
				$remark = array(
					"门店名称: {$store['title']}",
					"订单类型: {$order['order_type_cn']}",
					"下单时间: " . date('Y-m-d H: i', $order['addtime']),
					"订单金额: {$order['final_fee']}",
				);
			}
			elseif($type == 'handle') {
				$title = '商家已接单,正在准备商品中...';
				$remark = array(
					"门店名称: {$store['title']}",
					"订单类型: {$order['order_type_cn']}",
					"处理时间: " . date('Y-m-d H: i', time()),
				);
			}
			elseif($type == 'delivery_ing') {
				$title = '您的订单正在为您配送中';
				$remark = array(
					"门店名称: {$store['title']}",
					"订单类型: {$order['order_type_cn']}",
				);
				$end_remark = "骑士将骑上战马为您急速送达, 请保持电话畅通";
			}
			elseif($type == 'delivery_assign') {
				$title = '配送员已经接单，为您取货中';
				$remark = array(
					"门店名称: {$store['title']}",
					"订单类型: {$order['order_type_cn']}",
				);
				$end_remark = "配送员已接单, 正赶至商家为您取货。";
			}
			elseif($type == 'delivery_instore') {
				$title = '配送员已到店，正等待商家出餐';
				$remark = array(
					"门店名称: {$store['title']}",
					"订单类型: {$order['order_type_cn']}",
				);
				$end_remark = "配送员已到店，正等待商家为您出餐。";
			}
			elseif($type == 'delivery_takegoods') {
				$title = '配送员已取货，正在配送中';
				$remark = array(
					"门店名称: {$store['title']}",
					"订单类型: {$order['order_type_cn']}",
				);
				$end_remark = "配送员已取货，正在为您配送中。";
			}
			elseif($type == 'delivery_notice') {
				$title = "配送员【{$deliveryer['title']}】已达到你下单时填写的送货地址, 配送员手机号:【{$deliveryer['mobile']}】, 请注意接听配送员来电";
				$remark = array(
					"门店名称: {$store['title']}",
					"配送员: {$deliveryer['title']}",
					"手机号: {$deliveryer['mobile']}",
				);
				unset($note);
			}
			elseif($type == 'end') {
				$title = '订单处理完成';
				$remark = array(
					"门店名称: {$store['title']}",
					"订单类型: {$order['order_type_cn']}",
					"完成时间: " . date('Y-m-d H: i', time()),
				);
				$end_remark = "您的订单已处理完成, 如对商品有不满意或投诉请联系客服:{$_W['we7_wmall']['config']['mobile']},欢迎您下次光临.戳这里记得给我们的服务评价.";
				$grant = get_plugin_config('ordergrant.share');
				if(check_plugin_perm('ordergrant') && $grant['status'] && $grant['share_grant'] > 0) {
					$end_remark .= "评价并分享订单即可获取{$grant['share_grant']}{$grant['grantpe_cn']}奖励";
				}
			}
			elseif($type == 'cancel') {
				$title = '订单已取消';
				$remark = array(
					"门店名称: {$store['title']}",
					"订单类型: {$order['order_type_cn']}",
					"取消时间: " . date('Y-m-d H: i', time()),
				);
			}
			elseif($type == 'reply_remind') {
				$title = '订单催单有新的回复';
				$remark = array(
					"门店名称: {$store['title']}",
					"订单类型: {$order['order_type_cn']}",
					"回复时间: " . date('Y-m-d H:i', time()),
				);
			}
			elseif($type == 'reserve_order_pay') {
				$title = '你的预定单已支付';
				$remark = array(
					"门店名称: {$store['title']}",
					"订单类型: {$order['order_type_cn']}",
					"支付方式: {$order['pay_type_cn']}",
					"预定时间: {$order['reserve_time']}",
					"预定桌台: {$order['table_category']['title']}",
					"预定类型: {$order['reserve_type_cn']}",
				);
				if($order['table_id'] > 0) {
					$remark[] = "预定桌号: {$order['table']['title']}";
				}
			}
			elseif($type == 'communicate') {
				$title = '商家消息提示';
				$remark = array(
					"门店名称: {$store['title']}",
					"订单类型: {$order['order_type_cn']}",
					"消息时间: " . date('Y-m-d H:i', time()),
				);
			}
			if(!empty($note)) {
				if(!is_array($note)) {
					$remark[] = $note;
				} else {
					$remark[] = implode("\n", $note);
				}
			}
			if(!empty($end_remark)) {
				$remark[] = $end_remark;
			}
			if(is_array($remark)) {
				$remark = implode("\n", $remark);
			}
			$url = ivurl('pages/order/detail', array('id' => $order['id']), true);
			$miniprogram = '';
			if($config_wxapp_basic['tpl_consumer_url'] == 'wxapp' && $type != 'communicate') {
				$miniprogram = array(
					'appid' => $config_wxapp_basic['key'],
					'pagepath'=> "pages/order/index",
				);
			}
			$send = tpl_format($title, $order['ordersn'], $order['status_cn'], $remark);
			$status = $acc->sendTplNotice($order['openid'], $_W['we7_wmall']['config']['notice']['wechat']['public_tpl'], $send, $url, $miniprogram);
		} else {
			$send = array(
				'keyword1' => array(
					'value' => "#{$order['serial_sn']}",
					'color' => '#ff510'
				),
				'keyword2' => array(
					'value' => "{$order['order_type_cn']}",
					'color' => '#ff510'
				),
				'keyword3' => array(
					'value' => $order['status_cn'],
					'color' => '#ff510'
				),
				'keyword4' => array(
					'value' => $order['username'],
					'color' => '#ff510'
				),
				'keyword5' => array(
					'value' => $order['mobile'],
					'color' => '#ff510'
				),
				'keyword6' => array(
					'value' => date('Y-m-d H:i'),
					'color' => '#ff510'
				),
				'keyword7' => array(
					'value' => $order['final_fee'],
					'color' => '#ff510'
				),
				'keyword8' => array(
					'value' => $order['ordersn'],
					'color' => '#ff510'
				),
			);
			$public_tpl = $_W['we7_wmall']['config']['wxapp']['wxtemplate']['public_tpl'];
			$form_id = $order['data']['formId'];
			$form_type = 'formId';
			if(empty($form_id)) {
				if($order['data']['prepay_times'] > 0) {
					$form_type = 'prepayId';
					$form_id = $order['data']['prepay_id'];
				}
			}
			if(!empty($form_id)) {
				if($form_type == 'formId') {
					set_order_data($order['id'], 'formId', '');
				} else {
					$prepay_times = $order['data']['prepay_times'] - 1;
					set_order_data($order['id'], 'prepay_times', $prepay_times);
				}
				$status = $acc->sendTplNotice($order['openid'], $public_tpl, $send, "pages/home/index", $form_id);
			}
		}

		if(is_error($status)) {
			slog('wxtplNotice', "订单状态改变微信通知顾客-order_id:{$order['id']}", $send, $status['message']);
		}
	}
	$router = array(
		'pay' => array(
			'title' => '您的订单已付款',
			'content' => "支付方式:{$order['pay_type_cn']},支付时间:" . date('Y-m-d H:i', $order['paytime']),
		),
		'pay_notice' => array(
			'title' => '您的订单未支付，请尽快支付',
			'content' => $pay_notice,
		),
		'handle' => array(
			'title' => '您的订单商家已接单',
			'content' => '商家已接单,正在为您准备商品中',
		),
		'delivery_assign' => array(
			'title' => '您的订单已分配配送员',
			'content' => "配送员:{$deliveryer['title']},手机号:{$deliveryer['mobile']},配送员已接单, 正赶至商家取货, 骑士将骑上战马为您急速送达, 请保持电话畅通",
		),
		'delivery_instore' => array(
			'title' => '配送员已到店',
			'content' => "配送员已到店,正在为您取货,配送员:{$deliveryer['title']},手机号:{$deliveryer['mobile']}",
		),
		'delivery_takegoods' => array(
			'title' => '配送员已取货',
			'content' => "配送员已到店取货,正在为您配送中,配送员:{$deliveryer['title']},手机号:{$deliveryer['mobile']}",
		),
		'end' => array(
			'title' => '您的订单已完成',
			'content' => "您的订单已处理完成, 如对商品有不满意或投诉请联系客服:{$_W['we7_wmall']['config']['mobile']},欢迎您下次光临.戳这里记得给我们的服务评价.",
		),
		'cancel' => array(
			'title' => '您的订单已取消',
			'content' => "取消原因:" . (empty($extra['note']) ? '未知' : $extra['note'])
		),
		'delivery_notice' => array(
			'title' => '配送员到达您的收货地址',
			'content' => "配送员已到达你下单时填写的送货地址,配送员手机号:【{$deliveryer['mobile']}】, 请注意接听配送员来电"
		),
		'reply_remind' => array(
			'title' => '催单回复',
			'content' => $note
		),
		'communicate' => array(
			'title' => '商家消息提醒',
			'content' => $note
		),
	);
	if($order['order_channel'] == 'h5app') {
		mload()->model('h5app');
		if(empty($router[$type])) {
			return true;
		}
		mload()->model('member');
		$token = member_uid2token($order['uid']);
		$data = $router[$type];
		if(!empty($token)) {
			mload()->model('h5app');
			$url = ivurl('pages/order/detail', array('id' => $order['id']), true);
			$status = h5app_push($token, $data['title'], $data['content'], $url);
		}
	} elseif($order['order_channel'] == 'app' && $order['pay_type'] == 'majia') {
		$data = $router[$type];
		if(empty($data)) {
			return true;
		}
		mload()->model('member');
		$member = member_fetch($order['uid']);
		//助手消息
		$params = array(
			'user_id' => $member['uid_majia'],
			'type' => 'remind',
			'content' => array(
				'tag' => '订单状态变更提醒',
				'title' => $data['title'],
				'subtitle' => $data['title'],
				'link' => ivurl('pages/order/detail', array('id' => $order['id']), true),
				'extra_info' => array(
					array(
						'key' => '消息内容',
						'val' => $data['content']
					)
				)
			),
			'is_push' => 1
		);
		if(!function_exists('majia_send_assistant_msg')) {
			mload()->model('plugin');
			pload()->model('majiaApp');
		}
		$status = majia_send_assistant_msg($params);
	}
	return $status;
}

//order_clerk_notice
function order_clerk_notice($id, $type, $note = '', $extra = array()) {
	global $_W;
	$order = order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$_W['agentid'] = $order['agentid'];
	$store = store_fetch($order['sid'], array('title', 'id', 'push_token'));
	mload()->model('clerk');
	$clerks = clerk_fetchall($order['sid']);
	if(empty($clerks)) {
		return false;
	}
	$account = $order['acid'];
	$channel_notice = 'wechat';
	$config_wxapp_manager = $_W['we7_wmall']['config']['wxapp']['manager'];
	if(MODULE_FAMILY == 'wxapp' && $config_wxapp_manager['wxapp_manager_notice_channel'] == 'wxapp') {
		$channel_notice = 'wxapp';
		$account = $config_wxapp_manager;
	}
	$acc = TyAccount::create($account, $channel_notice);
	if($type == 'place_order') {
		if(empty($extra) || $extra['is_reserve'] != 1) {//预订单第一次通知不计数
			$order['notify_clerk_total'] = $order['notify_clerk_total'] + 1;
			pdo_update('tiny_wmall_order', array('last_notify_clerk_time' => TIMESTAMP, 'notify_clerk_total' => $order['notify_clerk_total']), array('id' => $order['id']));
		}

		$title = "您的店铺有新的外卖订单,订单号为 #{$order['serial_sn']} ,订单金额:{$order['final_fee']}元,请请尽快处理";
		$goods_temp = order_fetch_goods($id);
		$goods = array();
		foreach($goods_temp as $row) {
			$goods[] = "{$row['goods_title']} x {$row['goods_num']};";
		}
		unset($goods_temp);
		$goods = implode('', $goods);
		$remark = array(
			"门店名称： {$store['title']}",
			"订单备注：{$order['note']}",
			"商品信息： {$goods}",
			"下单时间： " . date('Y-m-d H:i', $order['addtime']),
			"总金　额： {$order['final_fee']}",
			"支付状态： {$order['pay_type_cn']}",
			"订单类型： {$order['order_type_cn']}"
		);
	} elseif($type == 'remind') {
		$title = "订单号为 #{$order['serial_sn']} 的订单有催单, 请请尽快回复";
		$remark = array(
			"门店名称: {$store['title']}",
			"订单类型: {$order['order_type_cn']}",
			"下单时间: " . date('Y-m-d H:i', $order['addtime']),
		);
	} elseif($type == 'collect') {
		$title = "您订单号为: {$order['ordersn']}的外卖单已被配送员接单";
		$remark = array(
			"门店名称: {$store['title']}",
			"下单时间: " . date('Y-m-d H:i', $order['addtime']),
		);
	} elseif($type == 'store_order_place') {
		$title = '您的店铺有新的店内订单,订单号: ' . $order['ordersn'];
		$remark = array(
			"门店名称: {$store['title']}",
			"下单时间: " . date('Y-m-d H:i', $order['addtime']),
			"桌　　号: " . $order['table']['title'] . '桌',
			"客人数量: " . $order['person_num'] . '人',
		);
	} elseif($type == 'store_order_pay') {
		$title = "订单号为{$order['ordersn']}的订单已付款";
		$remark = array(
			"门店名称: {$store['title']}",
			"下单时间: " . date('Y-m-d H:i', $order['addtime']),
			"桌　　号: " . $order['table']['title'] . '桌',
			"客人数量: " . $order['person_num'] . '人',
		);
	} elseif($type == 'reserve_order_pay') {
		$title = "您有新的预定订单,订单号{$order['ordersn']}, 已付款, 支付方式:{$order['pay_type_cn']}";
		$remark = array(
			"门店名称: {$store['title']}",
			"下单时间: " . date('Y-m-d H:i', $order['addtime']),
			"预定时间: " . $order['reserve_time'],
			"预定类型: " . $order['reserve_type_cn'],
			"预定桌台: " . $order['table_category']['title'],
			"预定　人: " . $order['username'],
			"手机　号: " . $order['mobile'],
		);
		if($order['table_id'] > 0) {
			$remark[] = "预定桌号: {$order['table']['title']}";
		}
	} elseif($type == 'store_order_jiacai') {
		$title = "{$order['table']['title']}客人加菜了, 订单号为{$order['ordersn']}, 请尽快处理";
		$goods_temp = pdo_fetchall('select id, goods_title, goods_num from ' . tablename('tiny_wmall_order_stat') . " where uniacid = :uniacid and oid = :oid and goods_type = :goods_type and id in ({$extra['jiacai_ids']})", array('uniacid' => $_W['uniacid'], 'oid' => $id, 'goods_type' => 'jiacai'));
		$goods = array();
		foreach($goods_temp as $row) {
			$goods[] = "{$row['goods_title']} x {$row['goods_num']};";
		}
		unset($goods_temp);
		$goods = implode('', $goods);
		$remark = array(
			"门店名称： {$store['title']}",
			"桌　　号：{$order['table']['title']}",
			"商品信息： {$goods}",
			"加菜时间： " . date('Y-m-d H:i'),
			"总金　额： {$order['final_fee']}",
			"支付状态： {$order['pay_type_cn']}",
		);
	}

	if($channel_notice == 'wechat') {
		if(!empty($note)) {
			if(!is_array($note)) {
				$remark[] = $note;
			} else {
				$remark[] = implode("\n", $note);
			}
		}
		$url = imurl('manage/order/takeout/detail', array('id' => $order['id'], 'sid' => $order['sid']), true);
		$remark = implode("\n", $remark);
		$miniprogram = '';
		if($config_wxapp_manager['tpl_manager_url'] == 'wxapp') {
			$miniprogram = array(
				'appid' => $config_wxapp_manager['key'],
				'pagepath'=> "pages/order/index?sid={$order['sid']}",
			);
		}
		$send = tpl_format($title, $order['ordersn'], $order['status_cn'], $remark);
		$public_tpl = $_W['we7_wmall']['config']['notice']['wechat']['public_tpl'];
	} else {
		$data = array(
			$title,
			$store['title'],
			"#{$order['serial_cn']}",
			$order['order_type_cn'],
			date('Y-m-d H:i', $order['addtime']),
			$order['note'],
			"￥{$order['final_fee']}",
		);
		$send = format_wxapp_tpl($data);
		$url = "pages/order/index";
		$public_tpl = $config_wxapp_manager['wxtemplate']['status_tpl'];
	}
	mload()->model('sms');
	foreach($clerks as $clerk) {
		$config_notify_rule_clerk = $_W['we7_wmall']['config']['takeout']['order']['notify_rule_clerk'];
		$notify_phonecall_time = intval($config_notify_rule_clerk['notify_phonecall_time']);
		if(!empty($clerk['mobile']) && in_array($type, array('place_order', 'store_order_place')) && $order['notify_clerk_total'] >= $notify_phonecall_time && $clerk['extra']['accept_voice_notice'] == 1) {
			$result = sms_singlecall($clerk['mobile'], array('name' => $clerk['title'], 'store' => $store['title'], 'price' => $order['final_fee']), 'clerk');
			if(is_error($result)) {
				slog('alidayuCall', "订单状态变动阿里大鱼语音通知商户-{$store['title']}:{$clerk['title']}", array('name' => $clerk['title'], 'store' => $store['title'], 'price' => $order['final_fee']), $result['message']);
			}
		}
		if($clerk['extra']['accept_wechat_notice'] == 1) {
			if($channel_notice == 'wechat') {
				$status = $acc->sendTplNotice($clerk['openid'], $public_tpl, $send, $url, $miniprogram);
			} else {
				if(in_array($type, array('place_order', 'store_order_place'))) {
					$status = $acc->sendTplNotice($clerk['openid_wxapp_manager'], $public_tpl, $send, $url);
				}
			}
			if(is_error($status)) {
				slog('wxtplNotice', "订单状态变动微信通知商户-{$store['title']}:{$clerk['title']},渠道:{$channel_notice}", $send, $status['message']);
			}
		} else {
			slog('wxtplNotice', "订单状态变动微信通知商户-{$store['title']}:{$clerk['title']}", $send, '商户设置不接收微信模板消息');
		}
	}
	if(in_array($type, array('place_order', 'remind', 'store_order_place'))) {
		$audience = array(
			'tag' => array(
				$store['push_token']
			)
		);
		$url = isurl('/pages/order/index', array('sid' => $order['sid']), true);
		$data = Jpush_clerk_send('您的店铺有新的订单', $title, array('voice_text' => $title, 'url' => $url, 'notify_type' => $type, 'id' => $order['id']), $audience);
	}
	return true;
}

function order_deliveryer_notice($id, $type, $deliveryer_id = 0, $note = '') {
	global $_W;
	$order = order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$_W['agentid'] = $order['agentid'];
	if($_W['is_agentconfig'] != $_W['agentid']) {
		$_W['we7_wmall']['config'] = get_system_config();
	}
	if($order['order_type'] != 1) {
		return error(-1, '不是外卖订单');
	}
	mload()->model('deliveryer');
	$store = store_fetch($order['sid'], array('title', 'id', 'delivery_mode'));
	if($deliveryer_id > 0) {
		$deliveryers[] = $deliveryer = deliveryer_fetch($deliveryer_id);
	} else {
		if($store['delivery_mode'] == 2) {
			$deliveryers = deliveryer_fetchall(0, array('over_max_collect_show' => 0));
		} else {
			//只有是店内配送员模式的时候, 才可以指定配送员
			$deliveryers = deliveryer_fetchall($order['sid'], array('agentid' => -1));
		}
	}
	if(empty($deliveryers)) {
		//通知平台管理员没有接单中的配送员
		if($store['delivery_mode'] == 2) {
			order_manager_notice($order['id'], 'no_working_deliveryer');
		}
		return false;
	}
	$account = $order['acid'];
	$channel_notice = 'wechat';
	$config_wxapp_deliveryer = $_W['we7_wmall']['config']['wxapp']['deliveryer'];
	if(MODULE_FAMILY == 'wxapp' && $config_wxapp_deliveryer['wxapp_deliveryer_notice_channel'] == 'wxapp') {
		$channel_notice = 'wxapp';
		$account = $config_wxapp_deliveryer;
	}
	$show_mobile = 1;
	if($order['data']['yinsihao_status'] == 1) {
		$yinsihao_config = get_plugin_config('yinsihao.basic');
		if(!empty($yinsihao_config) && empty($yinsihao_config['deliveryer_tpl_status'])) {
			$show_mobile = 0;
		}
	}
	//注意：new_delivery：通知的是单个配送员。delivery_wait：通知的是平台所有配送员
	$acc = TyAccount::create($account, $channel_notice);
	if($type == 'new_delivery') {
		$title = "店铺{$store['title']}有新的外卖配送订单, 配送地址为{$order['address']}, 快去处理吧";
		if($show_mobile == 1) {
			$remark = array(
				"门店名称: {$store['title']}",
				"下单时间: " . date('Y-m-d H:i', $order['addtime']),
				"总金　额: {$order['final_fee']}",
				"支付状态: {$order['pay_type_cn']}",
				"下单　人: {$order['username']}",
				"联系手机: {$order['mobile']}",
				"送货地址: {$order['address']}",
				"订单类型: " . ($store['delivery_mode'] == 1 ? '店内配送单' : '平台配送单'),
			);
		} else {
			$remark = array(
				"门店名称: {$store['title']}",
				"下单时间: " . date('Y-m-d H:i', $order['addtime']),
				"总金　额: {$order['final_fee']}",
				"支付状态: {$order['pay_type_cn']}",
				"送货地址: {$order['address']}",
				"订单类型: " . ($store['delivery_mode'] == 1 ? '店内配送单' : '平台配送单'),
			);
		}
		if(!empty($note)) {
			$remark[] = $note;
		}
		$remark = implode("\n", $remark);
		$url = imurl('delivery/order/takeout/detail', array('id' => $order['id']), true);
	} else if($type == 'delivery_wait') {
		$config_takeout = $_W['we7_wmall']['config']['takeout'];
		if($config_takeout['order']['dispatch_mode'] != 1 && !$config_takeout['order']['can_collect_order']) {
			slog('wxtplNotice', "订单状态变动微信通知配送员", '', '当前外卖订单调度模式为管理员派单或系统自动分配订单，并且在该模式下设置了不允许配送员抢单,所以系统不再通过微信模板消息和APP语音通知配送员接单');
			return true;
		}
		$order['notify_deliveryer_total'] = $order['notify_deliveryer_total'] + 1;
		pdo_update('tiny_wmall_order', array('last_notify_deliveryer_time' => TIMESTAMP, 'notify_deliveryer_total' => $order['notify_deliveryer_total']), array('id' => $order['id']));
		$title = "店铺{$store['title']}有新的配送订单, 配送地址为{$order['address']}, 快去抢单吧";
		if($show_mobile == 1) {
			$remark = array(
				"门店名称: {$store['title']}",
				"下单时间: " . date('Y-m-d H:i', $order['addtime']),
				"下单　人: {$order['username']}",
				"联系手机: {$order['mobile']}",
				"送货地址: {$order['address']}",
				"订单类型: " . ($store['delivery_mode'] == 1 ? '店内配送单' : '平台配送单'),
			);
		} else {
			$remark = array(
				"门店名称: {$store['title']}",
				"下单时间: " . date('Y-m-d H:i', $order['addtime']),
				"送货地址: {$order['address']}",
				"订单类型: " . ($store['delivery_mode'] == 1 ? '店内配送单' : '平台配送单'),
			);
		}
		if(!empty($note)) {
			$remark[] = $note;
		}
		$remark = implode("\n", $remark);
		$url = imurl('delivery/order/takeout/list', array(), true);
		$durl = idurl('pages/order/takeout', array(), true);
		if($store['delivery_mode'] == 2 && $order['delivery_type'] == 2) {//平台配送才会语音提示
			Jpush_deliveryer_send('您有新的外卖待抢订单', $title, array('url' => $durl, 'id' => $order['id'], 'voice_text' => $title, 'notify_type' => 'ordernew', 'redirect_type' => 'takeout', 'redirect_extra' => 3));
		}
	} else if($type == 'cancel') {
		$title = "收货地址为{$order['address']}, 收货人为{$order['username']}的订单已取消,请及时调整配送顺序";
		if($show_mobile == 1) {
			$remark = array(
				"门店名称: {$store['title']}",
				"下单时间: " . date('Y-m-d H:i', $order['addtime']),
				"下单　人: {$order['username']}",
				"联系手机: {$order['mobile']}",
				"送货地址: {$order['address']}",
				"订单类型: " . ($store['delivery_mode'] == 1 ? '店内配送单' : '平台配送单')
			);
		} else {
			$remark = array(
				"门店名称: {$store['title']}",
				"下单时间: " . date('Y-m-d H:i', $order['addtime']),
				"送货地址: {$order['address']}",
				"订单类型: " . ($store['delivery_mode'] == 1 ? '店内配送单' : '平台配送单')
			);
		}
		if(!empty($note)) {
			$remark[] = $note;
		}
		$remark = implode("\n", $remark);
		$url = imurl('delivery/order/takeout/detail', array('id' => $order['id']), true);
	} elseif($type == 'direct_transfer') {
		$from_deliveryer = $note['from_deliveryer'];
		$title = "{$from_deliveryer['title']}向您外卖单发起转单申请，收货地址为{$order['address']},请及时做出回复";
		if($show_mobile == 1) {
			$remark = array(
				"门店名称: {$store['title']}",
				"下单时间: " . date('Y-m-d H:i', $order['addtime']),
				"转单时间: " . date('Y-m-d H:i', TIMESTAMP),
				"下单　人: {$order['username']}",
				"联系手机: {$order['mobile']}",
				"送货地址: {$order['address']}",
				"订单类型: " . ($store['delivery_mode'] == 1 ? '店内配送单' : '平台配送单')
			);
		} else {
			$remark = array(
				"门店名称: {$store['title']}",
				"下单时间: " . date('Y-m-d H:i', $order['addtime']),
				"转单时间: " . date('Y-m-d H:i', TIMESTAMP),
				"送货地址: {$order['address']}",
				"订单类型: " . ($store['delivery_mode'] == 1 ? '店内配送单' : '平台配送单')
			);
		}
		$remark = implode("\n", $remark);
		if($order['delivery_status'] == 8) {
			$order['delivery_status'] = 7;
		}
		$url = imurl('delivery/order/takeout/list', array('status' => $order['delivery_status']), true);
	} elseif($type == 'direct_transfer_refuse') {
		$from_deliveryer = $note['from_deliveryer'];
		$to_deliveryer = $note['to_deliveryer'];
		$title = "{$to_deliveryer['title']}拒绝了收获地址为{$order['address']}的外卖单定向转单申请,此订单将由您继续配送";
		if($show_mobile == 1) {
			$remark = array(
				"门店名称: {$store['title']}",
				"下单时间: " . date('Y-m-d H:i', $order['addtime']),
				"下单　人: {$order['username']}",
				"联系手机: {$order['mobile']}",
				"送货地址: {$order['address']}",
				"订单类型: " . ($store['delivery_mode'] == 1 ? '店内配送单' : '平台配送单')
			);
		} else {
			$remark = array(
				"门店名称: {$store['title']}",
				"下单时间: " . date('Y-m-d H:i', $order['addtime']),
				"送货地址: {$order['address']}",
				"订单类型: " . ($store['delivery_mode'] == 1 ? '店内配送单' : '平台配送单')
			);
		}
		$remark = implode("\n", $remark);
		if($order['delivery_status'] == 8) {
			$order['delivery_status'] = 7;
		}
		$url = imurl('delivery/order/takeout/list', array('status' => $order['delivery_status']), true);
	} elseif($type == 'remind') {
		$title = "收货地址为{$order['address']}, 收货人为{$order['username']}向您催单, 请尽快送达";
		if($show_mobile == 1) {
			$remark = array(
				"门店名称: {$store['title']}",
				"下单时间: " . date('Y-m-d H:i', $order['addtime']),
				"下单　人: {$order['username']}",
				"联系手机: {$order['mobile']}",
				"送货地址: {$order['address']}",
			);
		} else {
			$remark = array(
				"门店名称: {$store['title']}",
				"下单时间: " . date('Y-m-d H:i', $order['addtime']),
				"送货地址: {$order['address']}",
			);
		}
		$remark = implode("\n", $remark);
		$url = imurl('delivery/order/takeout/detail', array('id' => $order['id']), true);
	}
	if($channel_notice == 'wechat') {
		$miniprogram = '';
		if($config_wxapp_deliveryer['tpl_deliveryer_url'] == 'wxapp') {
			$miniprogram = array(
				'appid' => $config_wxapp_deliveryer['key'],
				'pagepath'=> "pages/order/list",
			);
		}
		$send = tpl_format($title, $order['ordersn'], $order['status_cn'], $remark);
	} else {
		$data = array(
			$title,
			$store['title'],
			"#{$order['serial_cn']}",
			$order['order_type_cn'],
			date('Y-m-d H:i', $order['addtime']),
			$order['note'],
			"￥{$order['final_fee']}",
		);
		$send = format_wxapp_tpl($data);
		$url = "pages/order/list";
	}

	mload()->model('sms');
	foreach($deliveryers as $deliveryer) {
		$config_notify_rule_deliveryer = $_W['we7_wmall']['config']['takeout']['order']['notify_rule_deliveryer'];
		$notify_phonecall_time = intval($config_notify_rule_deliveryer['notify_phonecall_time']);
		//只有新订单会发起语音电话通知
		if(!empty($deliveryer['mobile']) && !in_array($type, array('cancel', 'direct_transfer', 'direct_transfer_refuse')) && $order['notify_deliveryer_total'] >= $notify_phonecall_time && $deliveryer['extra']['accept_voice_notice'] == 1) {
			$data = sms_singlecall($deliveryer['mobile'], array('name' => $deliveryer['title'], 'store' => $store['title']), 'deliveryer');
			if(is_error($data)) {
				slog('alidayuCall', "订单状态变动阿里大鱼语音通知配送员-{$deliveryer['title']}",array('name' => $deliveryer['title'], 'store' => $store['title']), $data['message']);
			}
		}
		if($deliveryer['extra']['accept_wechat_notice'] == 1) {
			if($channel_notice == 'wechat') {
				$status = $acc->sendTplNotice($deliveryer['openid'], $_W['we7_wmall']['config']['notice']['wechat']['public_tpl'], $send, $url, $miniprogram);
			} else {
				if(in_array($type, array('new_delivery', 'delivery_wait', 'cancel'))) {
					$status = $acc->sendTplNotice($deliveryer['openid_wxapp_deliveryer'], $config_wxapp_deliveryer['wxtemplate']['status_tpl'], $send, $url);
				}
			}
			if(is_error($status)) {
				slog('wxtplNotice', "订单状态变动微信通知配送员:{$deliveryer['title']},渠道:{$channel_notice}", $send, $status['message']);
			}
		} else {
			slog('wxtplNotice', "订单状态变动微信通知配送员:{$deliveryer['title']}", $send, '配送员设置不接收微信模板消息');
		}
		if($store['delivery_mode'] == 2 && $order['delivery_type'] == 2) {//平台配送才会语音提示
			if(!empty($deliveryer['token'])) {
				$audience = array(
					'alias' => array($deliveryer['token'])
				);
				$durl = idurl('pages/order/takeout', array(), true);
				if($type == 'new_delivery') {
					Jpush_deliveryer_send('您有新的外卖配送订单', $title, array('url' => $durl, 'id' => $order['id'], 'voice_text' => $title, 'notify_type' => 'orderassign', 'redirect_type' => 'takeout', 'redirect_extra' => 7), $audience);
				} elseif($type == 'cancel') {
					Jpush_deliveryer_send('订单取消通知', $title, array('url' => $durl, 'id' => $order['id'], 'voice_text' => $title, 'notify_type' => 'ordercancel','redirect_type' => 'takeout', 'redirect_extra' => 3), $audience);
				} elseif($type == 'direct_transfer') {
					Jpush_deliveryer_send("{$from_deliveryer['title']}向您发起转单申请", $title, array('url' => $durl, 'id' => $order['id'], 'voice_text' => $title, 'notify_type' => 'orderDirectTransfer','redirect_type' => 'takeout', 'redirect_extra' => $order['delivery_status']), $audience);
				} elseif($type == 'direct_transfer_refuse') {
					Jpush_deliveryer_send("{$to_deliveryer['title']}拒绝了收获地址为{$order['address']}的定向转单申请", $title, array('url' => $durl, 'id' => $order['id'], 'voice_text' => $title, 'notify_type' => 'orderDirectTransferRefuse','redirect_type' => 'takeout', 'redirect_extra' => $order['delivery_status']), $audience);
				} elseif($type == 'remind') {
					Jpush_deliveryer_send("有用户向您催单", $title, array('url' => $durl, 'id' => $order['id'], 'voice_text' => $title, 'notify_type' => 'orderRemind','redirect_type' => 'takeout', 'redirect_extra' => $order['delivery_status']), $audience);
				}
			}
		}
	}
	return true;
}

function order_refund_fetch($refund_id) {
	global $_W;
	$refund = pdo_get('tiny_wmall_order_refund', array('uniacid' => $_W['uniacid'], 'id' => $refund_id));
	if(empty($refund)) {
		return error(-1, '退款记录不存在');
	}
	$refund_status = order_refund_status();
	$refund_channel = order_refund_channel();
	$refund['status_cn'] = $refund_status[$refund['status']]['text'];
	$refund['channel_cn'] = $refund_channel[$refund['channel']]['text'];
	return $refund;
}

function order_refund_notice($refund_id, $type, $extra = array()) {
	global $_W;
	$refund = order_refund_fetch($refund_id);
	if(is_error($refund)) {
		return $refund;
	}
	$order_id = $refund['order_id'];
	$order = order_fetch($order_id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$note = $extra['note'];
	$store = store_fetch($order['sid'], array('title', 'id'));
	$acc = WeAccount::create($order['acid']);
	mload()->model('clerk');
	$clerks = clerk_fetchall($order['sid']);
	$reason_title = '订单取消';
	if($refund['type'] == 'goods') {
		$reason_title = '订单部分退款';
	}
	if($type == 'apply') {
		if($order['agentid'] > 0) {
			$_W['agentid'] = 0;
			$_W['we7_wmall']['config'] = get_system_config();
		}
		$maneger = $_W['we7_wmall']['config']['manager'];
		if(!empty($maneger['openid'])) {
			//通知平台管理员
			$tips = "您的平台有新的【退款申请】, 单号【{$refund['out_refund_no']}】,请尽快处理";
			$remark = array(
				"申请门店: " . $store['title'],
				"退款单号: " . $refund['out_refund_no'],
				"支付方式: " . $order['pay_type_cn'],
				"用户姓名: " . $order['username'],
				"联系方式: " . $order['mobile'],
				$note
			);
			$params = array(
				'first' => $tips,
				'reason' => "{$reason_title}, 发起退款流程",
				'refund' => $refund['fee'],
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall']['config']['notice']['wechat']['refund_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', '申请退款微信通知平台管理员', $send, $status['message']);
			}
		}

		if(!empty($clerks)) {
			//通知门店店员
			$tips = "您的【退款申请】已提交,单号【{$refund['out_refund_no']}】,平台会尽快处理";
			$remark = array(
				"申请门店: " . $store['title'],
				"退款单号: " . $refund['out_refund_no'],
				"支付方式: " . $order['pay_type_cn'],
				"用户姓名: " . $order['username'],
				"联系方式: " . $order['mobile'],
				"已付款项会在1-3工作日内返回到用户的账号, 如有疑问, 请联系平台管理员",
			);
			$params = array(
				'first' => $tips,
				'reason' => "{$reason_title}, 发起退款流程",
				'refund' => $refund['fee'],
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			foreach($clerks as $clerk) {
				if($clerk['extra']['accept_wechat_notice'] == 1) {
					$status = $acc->sendTplNotice($clerk['openid'], $_W['we7_wmall']['config']['notice']['wechat']['refund_tpl'], $send);
					if(is_error($status)) {
						slog('wxtplNotice', "申请退款微信通知门店店员-{$store['title']}:{$clerk['title']}", $send, $status['message']);
					}
				} else {
					slog('wxtplNotice', "申请退款微信通知门店店员-{$store['title']}:{$clerk['title']}", $send, '店员设置了不接受微信模板消息');
				}
			}
		}
		if($_W['role'] == 'clerker' && $refund['type'] == 'goods') {
			if(!empty($order['openid'])) {
				//通知顾客
				$tips = "您订单号为【{$order['ordersn']}】的订单，商户发起部分退款";
				$remark = array(
					"下单门店: " . $store['title'],
					"支付方式: " . $order['pay_type_cn'],
					"退款渠道: " . $refund['channel_cn'],
					"退款账户: " . $refund['account'],
					"退款金额: " . $refund['fee'],
					"如有疑问, 请联系商户咨询",
				);
				$params = array(
					'first' => $tips,
					'reason' => "商户发起部分退款,{$refund['reason']}",
					'refund' => $refund['fee'],
					'remark' => implode("\n", $remark)
				);
				$url = '';
				if(MODULE_FAMILY != 'wxapp') {
					$url = ivurl('pages/order/refund', array('id' => $order['id']), true);
				}
				$send = sys_wechat_tpl_format($params);
				$status = $acc->sendTplNotice($order['openid'], $_W['we7_wmall']['config']['notice']['wechat']['refund_tpl'], $send, $url);
				if(is_error($status)) {
					slog('wxtplNotice', "申请退款成功微信通知顾客-order_id:{$order['id']}", $send, $status['message']);
				}
			}
		}
	} elseif($type == 'success') {
		if(!empty($clerks)) {
			//通知门店店员
			$tips = "您店铺单号为【{$refund['out_refund_no']}】的退款已退款成功";
			$remark = array(
				"申请门店: " . $store['title'],
				"支付方式: " . $order['pay_type_cn'],
				"用户姓名: " . $order['username'],
				"联系方式: " . $order['mobile'],
				"退款渠道: " . $refund['channel_cn'],
				"退款账户: " . $refund['account'],
				"如有疑问, 请联系平台管理员",
			);
			$params = array(
				'first' => $tips,
				'reason' => "{$reason_title}, 发起退款流程",
				'refund' => $refund['fee'],
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);

			foreach($clerks as $clerk) {
				if($clerk['extra']['accept_wechat_notice'] == 1) {
					$status = $acc->sendTplNotice($clerk['openid'], $_W['we7_wmall']['config']['notice']['wechat']['refund_tpl'], $send);
					if(is_error($status)) {
						slog('wxtplNotice', "申请退款成功微信通知门店店员-{$store['title']}:{$clerk['title']}", $send, $status['message']);
					}
				} else {
					slog('wxtplNotice', "申请退款成功微信通知门店店员-{$store['title']}:{$clerk['title']}", $send, '店员设置了不接受微信模板消息');
				}
			}
		}

		if(!empty($order['openid'])) {
			//通知顾客
			$tips = "您订单号为【{$order['ordersn']}】的退款已退款成功";
			$remark = array(
				"下单门店: " . $store['title'],
				"支付方式: " . $order['pay_type_cn'],
				"退款渠道: " . $refund['channel_cn'],
				"退款账户: " . $refund['account'],
				"如有疑问, 请联系平台管理员",
			);
			$params = array(
				'first' => $tips,
				'reason' => "{$reason_title}, 发起退款流程",
				'refund' => $refund['fee'],
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($order['openid'], $_W['we7_wmall']['config']['notice']['wechat']['refund_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', "申请退款成功微信通知顾客-order_id:{$order['id']}", $send, $status['message']);
			}
		}
	} elseif($type == 'fail') {
		if($order['agentid'] > 0) {
			$_W['agentid'] = 0;
			$_W['we7_wmall']['config'] = get_system_config();
		}
		$maneger = $_W['we7_wmall']['config']['manager'];
		if(!empty($maneger['openid'])) {
			//通知平台管理员
			$tips = "您的平台的退款订单【退款失败】,退款单号【{$refund['out_refund_no']}】,请尽快处理";
			$remark = array(
				"申请门店: " . $store['title'],
				"退款单号: " . $refund['out_refund_no'],
				"支付方式: " . $order['pay_type_cn'],
				"用户姓名: " . $order['username'],
				"联系方式: " . $order['mobile'],
				$note
			);
			$params = array(
				'first' => $tips,
				'reason' => "{$reason_title}, 发起退款失败",
				'refund' => $refund['fee'],
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall']['config']['notice']['wechat']['refund_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', '发起退款失败微信通知平台管理员', $send, $status['message']);
			}
		}
	}
	return true;
}

//pay_types
function order_pay_types() {
	$pay_types = array(
		'' => array(
			'css' => 'label label-info',
			'text' => '未支付',
		),
		'alipay' => array(
			'css' => 'label label-info',
			'text' => '支付宝',
		),
		'wechat' => array(
			'css' => 'label label-success',
			'text' => '微信支付',
		),
		'yimafu' => array(
			'css' => 'label label-success',
			'text' => '一码付',
		),
		'credit' => array(
			'css' => 'label label-warning',
			'text' => '余额支付',
		),
		'delivery' => array(
			'css' => 'label label-primary',
			'text' => '货到付款',
		),
		'cash' => array(
			'css' => 'label label-primary',
			'text' => '现金支付',
		),
		'qianfan' => array(
			'css' => 'label label-primary',
			'text' => '千帆APP支付',
		),
		'majia' => array(
			'css' => 'label label-primary',
			'text' => '马甲APP支付',
		),
		'peerpay' => array(
			'css' => 'label label-primary',
			'text' => '找人代付',
		),
		'eleme' => array(
			'css' => 'label label-primary',
			'text' => '饿了么支付',
		),
		'maituan' => array(
			'css' => 'label label-primary',
			'text' => '美团支付',
		),
		'finishMeal' => array(
			'css' => 'label label-primary',
			'text' => '餐后付款',
		),
	);
	return $pay_types;
}

function order_check_member_cart($sid) {
	global $_W;
	$cart = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
	if(empty($cart)) {
		return error(-1, '购物车为空');
	}
	$cart['data'] = iunserializer($cart['data']);
	if(empty($cart['data'])) {
		return error(-1, '购物车为空');
	}
	$errno = 0;
	$errmessage = '';
	$goods_ids = implode(',', array_keys($cart['data']));
	$goods_info = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') ." WHERE uniacid = :uniacid AND sid = :sid AND id IN ($goods_ids)", array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');
	if(!empty($goods_info)) {
		foreach($goods_info as &$value) {
			if(defined('ORDER_TYPE') && ORDER_TYPE == 'tangshi') {
				$value['price'] = $value['ts_price'];
			}
		}
	}
	foreach($cart['data'] as $goods_id => $cart_item) {
		if(!empty($errno)) {
			break;
		}
		$goods = $goods_info[$goods_id];
		if(!$goods_info[$goods_id]['is_options']) {
			$option_item = $cart_item[0];
			if($option_item['discount_num'] > 0 && $option_item['bargain_type'] == 'bargain') {
				$bargain = pdo_get('tiny_wmall_activity_bargain', array('uniacid' => $_W['uniacid'], 'id' => $option_item['bargain_id'], 'sid' => $sid, 'status' => '1'));
				if(empty($bargain)) {
					$errno = -3; //特价活动已结束
					$errmessage = "特价活动{$bargain['title']}已结束！";
					break;
				}
				$bargain_goods = pdo_get('tiny_wmall_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'bargain_id' => $option_item['bargain_id'], 'goods_id' => $goods_id));
				if($bargain_goods['discount_available_total'] != -1 && $option_item['discount_num'] > $bargain_goods['discount_available_total']) {
					$errno = -4; //特价商品库存不足
					$errmessage = "参与特价活动{$bargain['title']}的{$goods['title']}库存不足！";
					break;
				}
			} else {
				if($goods['total'] != -1 && $option_item['num'] > $goods['total']) {
					$errno = -2; //商品库存不足
					$errmessage = "{$option_item['title']}库存不足！";
					break;
				}
			}
		} else {
			foreach($cart_item as $option_id => $option_item) {
				$option = pdo_get('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $option_id));
				if(empty($option)) {
					continue;
				}
				if($option['total'] != -1 && $cart_item['num'] > $option['total']) {
					$errno = -2; //商品库存不足
					$errmessage = "{$option_item['title']}库存不足！";
					break;
				}
			}
		}
	}
	if(!empty($errno)) {
		return error($errno, $errmessage);
	}
	return $cart;
}

function order_insert_cart($sid) {
	global $_W, $_GPC;
	if(!empty($_GPC['goods'])) {
		$num = 0;
		$price = 0;
		$box_price = 0;
		$ids_str = implode(',', array_keys($_GPC['goods']));
		$goods_info = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') ." WHERE uniacid = :aid AND sid = :sid AND id IN ($ids_str)", array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');
		foreach($_GPC['goods'] as $k => $v) {
			$k = intval($k);
			$goods_box_price = $goods_info[$k]['box_price'];
			if(!$goods_info[$k]['is_options']) {
				$v = intval($v['options'][0]);
				if($v > 0) {
					$goods[$k][0] = array(
						'title' => $goods_info[$k]['title'],
						'num' => $v,
						'price' => $goods_info[$k]['price'],
					);
					$num += $v;
					$price += $goods_info[$k]['price'] * $v;
					$box_price += $goods_box_price * $v;
				}
			} else {
				foreach($v['options'] as $key => $val) {
					$key = intval($key);
					$val = intval($val);
					if($key > 0 && $val > 0) {
						$option = pdo_get('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $key));
						if(empty($option)) {
							continue;
						}
						$goods[$k][$key] = array(
							'title' => $goods_info[$k]['title'] . "({$option['name']})",
							'num' => $val,
							'price' => $option['price'],
						);
						$num += $val;
						$price += $option['price'] * $val;
						$box_price += $goods_box_price * $val;
					}
				}
			}
		}

		$isexist = pdo_fetchcolumn('SELECT id FROM ' . tablename('tiny_wmall_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'uid' => $_W['member']['uid'],
			'groupid' => $_W['member']['groupid'],
			'num' => $num,
			'price' => $price,
			'box_price' => $box_price,
			'data' => iserializer($goods),
			'addtime' => TIMESTAMP,
		);
		if(empty($isexist)) {
			pdo_insert('tiny_wmall_order_cart', $data);
		} else {
			pdo_update('tiny_wmall_order_cart', $data, array('uniacid' => $_W['uniacid'], 'id' => $isexist, 'uid' => $_W['member']['uid']));
		}
		$data['data'] = $goods;
		return $data;
	} else {
		return error(-1, '商品信息错误');
	}
	return true;
}

//order_fetch_member_cart
function order_fetch_member_cart($sid, $goods_is_sail = true) {
	global $_W;
	$cart = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
	if(empty($cart)) {
		return false;
	}
	if((TIMESTAMP - $cart['addtime']) > 7*86400) {
		pdo_delete('tiny_wmall_order_cart', array('id' => $cart['id']));
		return false;
	}
	$cart['data'] = iunserializer($cart['data']);
	$cart['original_data'] = (array)iunserializer($cart['original_data']);
	if($goods_is_sail) {
		$goodsids = array_keys($cart['data']);
		$goodsids_str = implode(',', $goodsids);
		if(!empty($goodsids_str)) {
			$goods = pdo_fetchall('select id, status, is_showtime, start_time1, end_time1, start_time2, end_time2, week from ' . tablename('tiny_wmall_goods') . " where uniacid = :uniacid and sid = :sid and id in ($goodsids_str)", array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
			mload()->model('goods');
			foreach($goods as &$good) {
				if(ORDER_TYPE == 'tangshi') {
					$good['price'] = $good['ts_price'];
				}
				$is_sail_now = goods_is_available($good);
				if(!$is_sail_now || (ORDER_TYPE == 'takeout' && $good['type'] == 2) || (ORDER_TYPE == 'tangshi' && $good['type'] == 1)) {
					unset($cart['data'][$good['id']]);
					unset($cart['original_data'][$good['id']]);
				}
			}
		}
	}
	return $cart;
}

//order_del_member_cart
function order_del_member_cart($sid, $pindan_id = 0) {
	global $_W;
	$pindan_id = intval($pindan_id);
	if($pindan_id > 0) {
		pdo_delete('tiny_wmall_order_cart', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'pindan_id' => $pindan_id));
	} else {
		pdo_delete('tiny_wmall_order_cart', array('sid' => $sid, 'uid' => $_W['member']['uid']));
	}
	return true;
}

//order_del_member_cart
function get_member_cartnum($uid = 0) {
	global $_W;
	if(empty($uid)) {
		$uid = $_W['member']['uid'];
	}
	if(empty($uid)) {
		return 0;
	}
	$cart_sum = pdo_fetchcolumn('select sum(num) from' . tablename('tiny_wmall_order_cart') . ' where uniacid = :uniacid and uid = :uid', array(':uniacid' => $_W['uniacid'], ':uid' => $uid));
	return intval($cart_sum);
}

//order_order_update_goods_info
function order_update_goods_info($order_id, $sid, $cart = array()) {
	global $_W;
	if(empty($cart)) {
		$cart = order_fetch_member_cart($sid, false);
	}
	if(empty($cart['data'])) {
		return false;
	}
	if($cart['pindan_id'] > 0 && $cart['pindan_status'] == 2 && !empty($cart['pindan_data'])) {
		$cart['data'] = $cart['pindan_data'];
	}
	$categorys = pdo_getall('tiny_wmall_goods_category', array('uniacid' => $_W['uniacid']), 'id');
	$ids_str = implode(',', array_keys($cart['data']));
	$goods_info = pdo_fetchall('SELECT id,sid,cid,title,number,price,total,total_warning,total_update_type,print_label,svip_status,svip_price FROM ' . tablename('tiny_wmall_goods') ." WHERE uniacid = :aid AND sid = :sid AND id IN ($ids_str)", array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');
	$jiacai_ids = array();
	foreach($cart['data'] as $goods_id => $options) {
		$goods = $goods_info[$goods_id];
		if(empty($goods)) {
			continue;
		}
		foreach($options as $option_id => $item) {
			if($option_id > 0) {
				$option = pdo_get('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $option_id));
			}
			if($item['is_svip_price'] == 1) {
				if($option_id > 0 && $option['svip_price'] < $option['price']) {
					$option['price'] = $option['svip_price'];
				} elseif($goods['svip_status'] == 1) {
					$goods['price'] = $goods['svip_price'];
				}
			}
			if($goods['total_update_type'] == 1) {
				if(!$option_id) {
					if($goods['total'] != -1 && $goods['total'] > 0) {
						pdo_query('UPDATE ' . tablename('tiny_wmall_goods') . " set total = total - {$item['num']} WHERE uniacid = :aid AND id = :id", array(':aid' => $_W['uniacid'], ':id' => $goods_id));
						$total_now = $goods['total'] - $item['num'];
						if($goods['total_warning'] > 0 && $total_now <= $goods['total_warning']) {
							//库存报警
							goods_total_warning_notice($goods, 0, array('total_now' => $total_now));
						}
					}
					if($item['bargain_id'] > 0 && $item['discount_num'] > 0) {
						$bargain_goods = pdo_get('tiny_wmall_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'bargain_id' => $item['bargain_id'], 'goods_id' => $goods_id));
						if($bargain_goods['discount_available_total'] != -1 && $bargain_goods['discount_available_total'] > 0) {
							pdo_query('UPDATE ' . tablename('tiny_wmall_activity_bargain_goods') . " set discount_available_total = discount_available_total - {$item['discount_num']} WHERE uniacid = :uniacid AND bargain_id = :bargain_id and goods_id = :goods_id", array(':uniacid' => $_W['uniacid'], ':bargain_id' => $item['bargain_id'], ':goods_id' => $goods_id));
						}
					}
				} else {
					if(!empty($option) && $option['total'] != -1 && $option['total'] > 0) {
						pdo_query('UPDATE ' . tablename('tiny_wmall_goods_options') . " set total = total - {$item['num']} WHERE uniacid = :aid AND id = :id", array(':aid' => $_W['uniacid'], ':id' => $option_id));
						$total_now = $option['total'] - $item['num'];
						if($option['total_warning'] > 0 && $total_now <= $option['total_warning']) {
							//库存报警
							goods_total_warning_notice($goods, $option, array('total_now' => $total_now));
						}
					}
				}
			}
			$stat = array();
			if($item['num'] > 0) {
				$stat['oid'] = $order_id;
				$stat['uniacid'] = $_W['uniacid'];
				$stat['agentid'] = $_W['agentid'];
				$stat['sid'] = $sid;
				$stat['uid'] = $_W['member']['uid'];
				$stat['goods_id'] = $goods_id;
				$stat['goods_cid'] = $goods['cid'];
				$stat['option_id'] = $option_id;
				$stat['goods_category_title'] = $categorys[$goods['cid']]['title'];
				$stat['goods_title'] = $item['title'];
				$stat['goods_number'] = $goods['number'];
				$stat['goods_num'] = $item['num'];
				$stat['goods_discount_num'] = $item['discount_num'];
				$stat['goods_unit_price'] = $option_id > 0 ? $option['price'] : $goods['price'];
				$stat['goods_price'] = $item['total_discount_price'];
				$stat['goods_original_price'] = $item['total_price'];
				$stat['bargain_id'] = $item['bargain_id'];
				$stat['total_update_status'] = $goods['total_update_type'] == 2 ? 0 : 1;
				$stat['print_label'] = $goods['print_label'];
				$stat['addtime'] = TIMESTAMP;
				$stat['stat_year'] = date('Y');
				$stat['stat_month'] = date('Ym');
				$stat['stat_day'] = date('Ymd');
				$stat['stat_week'] = date('YW');
				$stat['goods_type'] = empty($cart['goods_type']) ? 'normal' : $cart['goods_type'];
				if($item['total_huangou_price'] > 0) {
					$stat['goods_type'] = 'huangou';
				}
				pdo_insert('tiny_wmall_order_stat', $stat);
				if($cart['goods_type'] == 'jiacai') {
					$jiacai_id = pdo_insertid();
					$jiacai_ids[] = $jiacai_id;
				}
			}
		}
	}
	if(!empty($jiacai_ids)) {
		return $jiacai_ids;
	}
	return true;
}

function order_amount_stat($sid) {
	global $_W;
	$stat = array();
	$today_starttime = strtotime(date('Y-m-d'));
	$month_starttime = strtotime(date('Y-m'));
	$stat['today_total'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $today_starttime)));
	$stat['today_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $today_starttime)));
	$stat['month_total'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $month_starttime)));
	$stat['month_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $month_starttime)));
	return $stat;
}

function order_count_activity($sid, $cart, $recordid = 0, $redPacket_id = 0, $delivery_price = 0, $delivery_free_price = 0, $order_type = '') {
	global $_W, $_GPC;
	$activityed = array('list' => '', 'total' => 0, 'activity' => 0, 'token' => 0, 'store_discount_fee' => 0, 'agent_discount_fee' => 0, 'plateform_discount_fee' => 0);
	$store = store_fetch($sid, array('delivery_mode', 'delivery_fee_mode', 'delivery_extra', 'delivery_free_price', 'cid'));
	//优惠计算条件金额计算餐盒费
	$order_type = intval($order_type);
	if($order_type <= 2) {
		$cart['price'] = $cart['price'] + $cart['box_price'];
	}
	if(($order_type == 1 || empty($order_type)) && (($_GPC['ac'] == 'order' && $_GPC['op'] == 'create') || defined('IN_WXAPP'))) {
		if(isset($delivery_free_price)) {
			$store['delivery_free_price'] = $delivery_free_price;
		}
		if(!empty($delivery_price) && $store['delivery_fee_mode'] <= 3 && $store['delivery_free_price'] > 0 && $cart['price'] >= $store['delivery_free_price']) {
			if($store['delivery_mode'] == 1) {
				//店内配送模式
				$store_discount_fee = $delivery_price;
				$agent_discount_fee = 0;
				$plateform_discount_fee = 0;
			} else {
				//平台配送模式
				$delivery_free_bear = trim($store['delivery_extra']['delivery_free_bear']);
				if($_W['is_agent']) {
					$agent_discount_fee = $delivery_price;
					$plateform_discount_fee = 0;
					$store_discount_fee = 0;
					if($delivery_free_bear == 'store') {
						$agent_discount_fee = 0;
						$store_discount_fee = $delivery_price;
					}
				} else {
					$agent_discount_fee = 0;
					$plateform_discount_fee = $delivery_price;
					$store_discount_fee = 0;
					if($delivery_free_bear == 'store') {
						$plateform_discount_fee = 0;
						$store_discount_fee = $delivery_price;
					}
				}
			}
			$activityed['list']['delivery'] = array(
				'text' => "-￥{$delivery_price}",
				'value' => $delivery_price,
				'type' => 'delivery',
				'name' => "满{$store['delivery_free_price']}元免配送费",
				'icon' => 'mian_b.png',
				'plateform_discount_fee' => $plateform_discount_fee,
				'store_discount_fee' => $store_discount_fee,
				'agent_discount_fee' => $agent_discount_fee
			);
			$activityed['total'] += $delivery_price;
			$activityed['activity'] += $delivery_price;
			$activityed['store_discount_fee'] += $store_discount_fee;
			$activityed['plateform_discount_fee'] += $plateform_discount_fee;
			$activityed['agent_discount_fee'] += $agent_discount_fee;
		}
		//免配送费
		if(empty($activityed['list']['delivery']) && $store['delivery_mode'] == 2 && !empty($delivery_price)) {
			if($_W['member']['setmeal_id'] > 0 && $_W['member']['setmeal_endtime'] >= TIMESTAMP) {
				$nums = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and vip_free_delivery_fee = 1 and status != 6 and addtime >= :addtime', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':addtime' => strtotime(date('Y-m-d'))));
				if(($nums < $_W['member']['setmeal_day_free_limit'] && $_W['member']['setmeal_day_free_limit'] > 0) || empty($_W['member']['setmeal_day_free_limit'])) {
					$free_delivery_price = $delivery_price;
					if($delivery_price > $_W['member']['setmeal_deliveryfee_free_limit'] && $_W['member']['setmeal_deliveryfee_free_limit'] > 0) {
						$free_delivery_price = $_W['member']['setmeal_deliveryfee_free_limit'];
					}
					$activityed['list']['vip_delivery'] = array('text' => "-￥{$free_delivery_price}", 'value' => $free_delivery_price, 'type' => 'delivery', 'name' => '会员免配送费', 'icon' => 'mian_b.png', 'plateform_discount_fee' => $free_delivery_price, 'agent_discount_fee' => 0, 'store_discount_fee' => 0);
					$activityed['total'] += $free_delivery_price;
					$activityed['activity'] += $free_delivery_price;
					$activityed['store_discount_fee'] += 0;
					$activityed['plateform_discount_fee'] += $free_delivery_price;
					$activityed['agent_discount_fee'] += 0;
				}
			}
		}
	}
	/*	if($cart['bargain_use_limit'] == 2) {
			return $activityed;
		}*/
	//兼容折扣商品与平台红包同时使用  $cart['bargain_use_limit'] != 2
	if($cart['bargain_use_limit'] != 2) {
		//新用户支付优惠和满减优惠不能同时享受
		mload()->model('activity');
		$activity = activity_getall($sid, 1);
		//自提满减
		if(($order_type == 2 || empty($order_type)) && !empty($activity) && !empty($activity['selfPickup'])) {
			$activity_selfPickup = $activity['selfPickup'];
			if($activity_selfPickup['status'] == 1) {
				$discount_temp = array_compare($cart['price'], $activity_selfPickup['data']);
				if(!empty($discount_temp)) {
					$discount = array(
						'back' => $discount_temp['back'],
						'plateform_discount_fee' => $discount_temp['plateform_charge'],
						'store_discount_fee' => $discount_temp['store_charge'],
						'agent_discount_fee' => $discount_temp['agent_charge'],
					);
					$activityed['list']['selfPickup'] = array('text' => "-￥{$discount['back']}", 'value' => $discount['back'], 'type' => 'discount', 'name' => '自提满减优惠', 'icon' => 'discount_b.png', 'store_discount_fee' => $discount['store_discount_fee'], 'agent_discount_fee' => $discount['agent_discount_fee'], 'plateform_discount_fee' => $discount['plateform_discount_fee']);
					$activityed['total'] += $discount['back'];
					$activityed['activity'] += $discount['back'];
					$activityed['store_discount_fee'] += $discount['store_discount_fee'];
					$activityed['plateform_discount_fee'] += $discount['plateform_discount_fee'];
					$activityed['agent_discount_fee'] += $discount['agent_discount_fee'];
				}
			}
		}
		if(!empty($activity) && ($order_type == 2 || empty($order_type)) && empty($activityed['list']['selfPickup'])) {
			$selfDelivery = $activity['selfDelivery'];
			if(!empty($selfDelivery['status'])) {
				$discount_temp = array_compare($cart['price'], $selfDelivery['data']);
				if(!empty($discount_temp)) {
					$discount_fee = round((10 - $discount_temp['back']) / 10 * $cart['price'], 2);
					$discount = array(
						'back' => $discount_temp['back'],
						'value' => $discount_fee,
						'plateform_discount_fee' => round($discount_fee * $discount_temp['plateform_charge'] / $discount_temp['back'], 2),
						'agent_discount_fee' => round($discount_fee * $discount_temp['agent_charge'] / $discount_temp['back'], 2),
						'store_discount_fee' => round($discount_fee * $discount_temp['store_charge'] / $discount_temp['back'], 2),
					);
					$activityed['list']['selfDelivery'] = array('text' => "-￥{$discount['value']}", 'value' => $discount['value'], 'type' => 'selfDelivery', 'name' => '自提优惠', 'icon' => 'selfDelivery_b.png', 'store_discount_fee' => $discount['store_discount_fee'], 'agent_discount_fee' => $discount['agent_discount_fee'], 'plateform_discount_fee' => $discount['plateform_discount_fee']);
					$activityed['total'] += $discount['value'];
					$activityed['activity'] += $discount['value'];
					$activityed['store_discount_fee'] += $discount['store_discount_fee'];
					$activityed['agent_discount_fee'] += $discount['agent_discount_fee'];
					$activityed['plateform_discount_fee'] += $discount['plateform_discount_fee'];
					if($order_type == 2) {
						return $activityed;
					}
				}
			}
		}
	}

	//平台首单红包不与其他优惠同时享受。（可与免配送费同享）
	if($redPacket_id > 0 || is_array($redPacket_id)) {
		mload()->model('redPacket');
		$record = redpacket_available_check($redPacket_id, $cart['price'], explode('|', $store['cid']), array('scene' => 'waimai', 'sid' => $sid, 'order_type' => $order_type));
		if(!is_error($record) && ($record['type'] != 'mallNewMember' || ($record['type'] == 'mallNewMember' && $_W['member']['is_mall_newmember']))) {
			$activityed['list']['redPacket'] = array('text' => "-￥{$record['discount']}", 'value' => $record['discount'], 'type' => 'redPacket', 'name' => '平台红包优惠', 'icon' => 'redPacket_b.png', 'redPacket_id' => $record['id'], 'plateform_discount_fee' => $record['data']['discount_bear']['plateform_charge'], 'agent_discount_fee' => $record['data']['discount_bear']['agent_charge'], 'store_discount_fee' => $record['data']['discount_bear']['store_charge']);
			$activityed['redPacket'] = $record;
			$activityed['total'] += $record['discount'];
			$activityed['activity'] += $record['discount'];
			$activityed['store_discount_fee'] += $record['data']['discount_bear']['store_charge'];
			$activityed['agent_discount_fee'] += $record['data']['discount_bear']['agent_charge'];
			$activityed['plateform_discount_fee'] += $record['data']['discount_bear']['plateform_charge'];
			if($record['type'] == 'mallNewMember') {
				return $activityed;
			}
		}
	}
	if($cart['bargain_use_limit'] == 2) {
		return $activityed;
	}

	if($recordid > 0) {
		$record = pdo_get('tiny_wmall_activity_coupon_record', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid'], 'status' => 1, 'id' => $recordid));
		if(!empty($record) && $record['starttime'] <= TIMESTAMP && $record['endtime'] >= TIMESTAMP && $cart['price'] >= $record['condition']) {
			$activityed['list']['token'] = array('text' => "-￥{$record['discount']}", 'value' => $record['discount'], 'type' => 'couponCollect', 'name' => '代金券优惠', 'icon' => 'couponCollect_b.png', 'recordid' => $recordid, 'plateform_discount_fee' => 0, 'agent_discount_fee' => 0, 'store_discount_fee' => $record['discount']);
			$activityed['total'] += $record['discount'];
			$activityed['activity'] += $record['discount'];
			$activityed['store_discount_fee'] += $record['discount'];
			$activityed['agent_discount_fee'] += 0;
			$activityed['plateform_discount_fee'] += 0;
		}
	}

	if(!empty($activity)) {
		$mallNewMember = $activity['mallNewMember'];
		if(!empty($mallNewMember['status'])) {
			if(!empty($_W['member']['is_mall_newmember'])) {
				$discount = array(
					'back' => $mallNewMember['data']['back'],
					'plateform_discount_fee' => $mallNewMember['data']['plateform_charge'],
					'store_discount_fee' => floatval($mallNewMember['data']['store_charge']),
					'agent_discount_fee' => $mallNewMember['data']['agent_charge'],
				);
				$activityed['list']['mallNewMember'] = array('text' => "-￥{$discount['back']}", 'value' => $discount['back'], 'type' => 'mallNewMember', 'name' => '首单优惠', 'icon' => 'mallNewMember_b.png', 'store_discount_fee' => $discount['store_discount_fee'], 'plateform_discount_fee' => $discount['plateform_discount_fee'], 'agent_discount_fee' => $discount['agent_discount_fee']);
				$activityed['total'] += $discount['back'];
				$activityed['activity'] += $discount['back'];
				$activityed['store_discount_fee'] += $discount['store_discount_fee'];
				$activityed['agent_discount_fee'] += $discount['agent_discount_fee'];
				$activityed['plateform_discount_fee'] += $discount['plateform_discount_fee'];
			}
		}

		if(!empty($activity['newMember'])) {
			$newMember = $activity['newMember'];
			if($newMember['status'] == 1 && !empty($_W['member']['is_store_newmember'])) {
				$discount = array(
					'back' => $newMember['data']['back'],
					'plateform_discount_fee' => $newMember['data']['plateform_charge'],
					'store_discount_fee' => $newMember['data']['store_charge'],
					'agent_discount_fee' => $newMember['data']['agent_charge'],
				);
				$activityed['list']['newMember'] = array('text' => "-￥{$discount['back']}", 'value' => $discount['back'], 'type' => 'newMember', 'name' => '新用户优惠', 'icon' => 'newMember_b.png', 'store_discount_fee' => $discount['store_discount_fee'], 'agent_discount_fee' => $discount['agent_discount_fee'], 'plateform_discount_fee' => $discount['plateform_discount_fee']);
				$activityed['total'] += $discount['back'];
				$activityed['activity'] += $discount['back'];
				$activityed['store_discount_fee'] += $discount['store_discount_fee'];
				$activityed['plateform_discount_fee'] += $discount['plateform_discount_fee'];
				$activityed['agent_discount_fee'] += $discount['agent_discount_fee'];
			}
		}

		if(empty($activityed['list']['mallNewMember']) && !empty($activity['discount'])) {
			$activity_discount = $activity['discount'];
			if($activity_discount['status'] == 1) {
				$discount_temp = array_compare($cart['price'], $activity_discount['data']);
				if(!empty($discount_temp)) {
					$discount = array(
						'back' => $discount_temp['back'],
						'plateform_discount_fee' => $discount_temp['plateform_charge'],
						'store_discount_fee' => $discount_temp['store_charge'],
						'agent_discount_fee' => $discount_temp['agent_charge'],
					);
					$activityed['list']['discount'] = array('text' => "-￥{$discount['back']}", 'value' => $discount['back'], 'type' => 'discount', 'name' => '满减优惠', 'icon' => 'discount_b.png', 'store_discount_fee' => $discount['store_discount_fee'], 'agent_discount_fee' => $discount['agent_discount_fee'], 'plateform_discount_fee' => $discount['plateform_discount_fee']);
					$activityed['total'] += $discount['back'];
					$activityed['activity'] += $discount['back'];
					$activityed['store_discount_fee'] += $discount['store_discount_fee'];
					$activityed['plateform_discount_fee'] += $discount['plateform_discount_fee'];
					$activityed['agent_discount_fee'] += $discount['agent_discount_fee'];
				}
			}
		}

		$cashGrant = $activity['cashGrant'];
		if(!empty($cashGrant['status'])) {
			$discount = array_compare($cart['price'], $cashGrant['data']);
			if(!empty($discount)) {
				$activityed['list']['cashGrant'] = array('text' => "返{$discount['back']}元", 'value' => $discount['back'], 'type' => 'cashGrant', 'name' => '返现优惠', 'icon' => 'cashGrant_b.png', 'store_discount_fee' => $discount['store_charge'], 'agent_discount_fee' => $discount['agent_charge'], 'plateform_discount_fee' => $discount['plateform_charge']);
				$activityed['total'] += 0;
				$activityed['activity'] += 0;
				$activityed['store_discount_fee'] += $discount['store_charge'];
				$activityed['plateform_discount_fee'] += $discount['plateform_charge'];
				$activityed['agent_discount_fee'] += $discount['agent_charge'];
			}
		}

		$grant = $activity['grant'];
		if(!empty($grant['status'])) {
			$discount = array_compare($cart['price'], $grant['data']);
			if(!empty($discount)) {
				$activityed['list']['grant'] = array('text' => "{$discount['back']}", 'value' => 0, 'type' => 'grant', 'name' => '满赠优惠', 'icon' => 'grant_b.png');
				$activityed['total'] += 0;
				$activityed['activity'] += 0;
			}
		}

		$coupon_grant = $activity['couponGrant'];
		if(!empty($coupon_grant['status'])) {
			mload()->model('coupon');
			$coupon = coupon_grant_available($sid, $cart['price']);
			if(!empty($coupon)) {
				$activityed['list']['couponGrant'] = array('text' => "返{$coupon['discount']}元代金券", 'value' => 0, 'type' => 'couponGrant', 'name' => '满返优惠', 'icon' => 'couponGrant_b.png');
				$activityed['total'] += 0;
				$activityed['activity'] += 0;
			}
		}

		//满减配送费
		$deliveryFeeDiscount = $activity['deliveryFeeDiscount'];
		if(empty($activityed['list']['delivery']) && empty($activityed['list']['vip_delivery']) && ($order_type == 1 || empty($order_type)) && (($_GPC['ac'] == 'order' && $_GPC['op'] == 'create') || defined('IN_WXAPP'))) {
			if(!empty($deliveryFeeDiscount) && !empty($deliveryFeeDiscount['status'])) {
				$deliveryFeeDiscount_temp = array_compare($cart['price'], $deliveryFeeDiscount['data']);
				if(!empty($deliveryFeeDiscount_temp)) {
					if($store['delivery_mode'] == 1) {
						//店内配送模式
						$store_delivery_discount_fee = $deliveryFeeDiscount_temp['back'];
						$agent_delivery_discount_fee = 0;
						$plateform_delivery_discount_fee = 0;
					} else {
						$store_delivery_discount_fee = $deliveryFeeDiscount_temp['store_charge'];
						$agent_delivery_discount_fee = $deliveryFeeDiscount_temp['agent_charge'];
						$plateform_delivery_discount_fee = $deliveryFeeDiscount_temp['plateform_charge'];
					}
					$activityed['list']['deliveryFeeDiscount'] = array(
						'text' => "-￥{$deliveryFeeDiscount_temp['back']}",
						'value' => $deliveryFeeDiscount_temp['back'],
						'type' => 'delivery',
						'name' => "满{$deliveryFeeDiscount_temp['condition']}元减{$deliveryFeeDiscount_temp['back']}元配送费",
						'icon' => 'discount_b.png',
						'plateform_discount_fee' => $plateform_delivery_discount_fee,
						'store_discount_fee' => $store_delivery_discount_fee,
						'agent_discount_fee' => $agent_delivery_discount_fee
					);
					$activityed['total'] += $deliveryFeeDiscount_temp['back'];
					$activityed['activity'] += $deliveryFeeDiscount_temp['back'];
					$activityed['store_discount_fee'] += $store_delivery_discount_fee;
					$activityed['plateform_discount_fee'] += $plateform_delivery_discount_fee;
					$activityed['agent_discount_fee'] += $agent_delivery_discount_fee;
				}
			}
		}
	}
	return $activityed;
}

function order_insert_refund_log($id, $refund_id, $type, $note = '') {
	global $_W;
	if(empty($type)) {
		return false;
	}
	$notes = array(
		'apply' => array(
			'status' => 1,
			'title' => '提交退款申请',
			'note' => "",
		),
		'handle' => array(
			'status' => 2,
			'title' => "{$_W['we7_wmall']['config']['mall']['title']}接受退款申请",
			'note' => ''
		),
		'success' => array(
			'status' => 3,
			'title' => "退款成功",
			'note' => ''
		),
		'fail' => array(
			'status' => 4,
			'title' => "退款失败",
			'note' => ''
		),

		'rejected' => array(
			'status' => 5,
			'title' => '店铺拒绝退单',
			'note' => "",
		),
		'arbitrating' => array(
			'status' => 6,
			'title' => '客服仲裁中',
			'note' => "",
		),
	);
	$title = $notes[$type]['title'];
	if(in_array($type, array('apply', 'handle'))) {
		$refund = pdo_get('tiny_wmall_order_refund', array('uniacid' => $_W['uniacid'], 'id' => $refund_id), array('type'));
		if($refund['type'] == 'goods') {
			$titles = array(
				'apply' => "{$_W['role_cn']}发起部分退款",
				'handle' => "{$_W['we7_wmall']['config']['mall']['title']}接受部分退款申请",
			);
			$title = $titles[$type];
		}
	}
	$note = $note ? $note : $notes[$type]['note'];
	$data = array(
		'uniacid' => $_W['uniacid'],
		'oid' => $id,
		'refund_id' => $refund_id,
		'order_type' => 'order',
		'status' => $notes[$type]['status'],
		'type' => $type,
		'title' => $title,
		'note' => $note,
		'addtime' => TIMESTAMP,
	);
	pdo_insert('tiny_wmall_order_refund_log', $data);
	return true;
}

function order_begin_payrefund($refund_id) {
	global $_W;
	$refund = pdo_get('tiny_wmall_order_refund', array('uniacid' => $_W['uniacid'], 'id' => $refund_id));
	if(empty($refund)) {
		return error(-1, '退款申请不存在或已删除');
	}
	if($refund['status'] == 2) {
		return error(-1, '退款进行中, 不能发起退款');
	}
	if($refund['status'] == 3) {
		return error(-1, '退款已成功, 不能发起退款');
	}
	if($refund['pay_type'] == 'credit') {
		if($refund['uid'] > 0) {
			$log = array(
				$refund['uid'],
				"外送模块订单退款, 订单号:{$refund['order_sn']}, 退款金额:{$refund['fee']}元",
				'we7_wmall'
			);
			mload()->model('member');
			member_credit_update($refund['uid'], 'credit2', $refund['fee'], $log);
			$refund_update = array(
				'status' => 3,
				'account' => '支付用户的平台余额',
				'channel' => 'ORIGINAL',
				'handle_time' => TIMESTAMP,
				'success_time' => TIMESTAMP,
			);
			pdo_update('tiny_wmall_order_refund', $refund_update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
			set_order_refund_status($refund['order_id'], 3);
			order_insert_refund_log($refund['order_id'], $refund['id'], 'handle');
			order_insert_refund_log($refund['order_id'], $refund['id'], 'success');
		}
		return error(0, '退款成功,支付金额已退款至顾客的平台余额');
	} elseif($refund['pay_type'] == 'wechat') {
		mload()->classs('wxpay');
		$pay = new WxPay($refund['order_channel']);
		$params = array(
			'total_fee' => $refund['total_fee'] * 100,
			'refund_fee' => $refund['fee'] * 100,
			'out_trade_no' => $refund['out_trade_no'],
			'out_refund_no' => $refund['out_refund_no'],
		);
		$response = $pay->payRefund_build($params);
		if(is_error($response)) {
			return error(-1, $response['message']);
		}
		pdo_update('tiny_wmall_order_refund', array('status' => 2, 'handle_time' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
		set_order_refund_status($refund['order_id'], 2);
		order_insert_refund_log($refund['order_id'], $refund['id'], 'handle');
		$query = order_query_payrefund($refund['id']);
		return $query;
	} elseif($refund['pay_type'] == 'alipay') {
		mload()->classs('alipay');
		$pay = new AliPay($refund['order_channel']);
		$params = array(
			'refund_fee' => $refund['fee'],
			'out_trade_no' => $refund['out_trade_no'],
		);
		if($refund['fee'] < $refund['total_fee']) {
			$params['out_request_no'] = $refund['out_refund_no']; //如需部分退款，则此参数必传
		}
		$response = $pay->payRefund_build($params);
		if(is_error($response)) {
			return error(-1, $response['message']);
		}
		$refund_update = array(
			'status' => 3,
			'account' => $response['buyer_logon_id'],
			'channel' => 'ORIGINAL',
			'handle_time' => TIMESTAMP,
			'success_time' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_order_refund', $refund_update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
		set_order_refund_status($refund['order_id'], 3);
		order_insert_refund_log($refund['order_id'], $refund['id'], 'handle');
		order_insert_refund_log($refund['order_id'], $refund['id'], 'success');
		return error(0, "退款成功,支付金额已退款至顾客的支付宝账户:{$response['buyer_logon_id']}");
	} elseif($refund['pay_type'] == 'yimafu') {
		$transactionid = pdo_get('tiny_wmall_order',array('uniacid' => $_W['uniacid'], 'id' => $refund['order_id']),array('transaction_id'));
		$orderno = $transactionid['transaction_id'];
		mload()->classs('yimafu');
		$pay = new YiMaFu();
		$response = $pay->payRefund_build($orderno);
		if(is_error($response)) {
			return error(-1,  '退款失败');
		}
		$refund_update = array(
			'status' => 3,
			'account' => '',
			'channel' => 'ORIGINAL',
			'handle_time' => TIMESTAMP,
			'success_time' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_order_refund', $refund_update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
		set_order_refund_status($refund['order_id'], 3);
		order_insert_refund_log($refund['order_id'], $refund['id'], 'handle');
		order_insert_refund_log($refund['order_id'], $refund['id'], 'success');
		return error(0, "退款成功,支付金额已退款至顾客一码付账户");
	} elseif($refund['pay_type'] == 'qianfan') {
		$member = pdo_get('tiny_wmall_members', array('uid' => $refund['uid']));
		if(empty($member['uid_qianfan'])) {
			return error(-1, "获取用户uid失败");
		}
		mload()->model('plugin');
		pload()->model('qianfanApp');
		$status = qianfan_user_credit_add($member['uid_qianfan'], $refund['fee']);
		if(is_error($status)) {
			return error(-1, "退款失败:{$status['message']}");
		}
		$refund_update = array(
			'status' => 3,
			'account' => '支付用户的APP账户余额',
			'channel' => 'ORIGINAL',
			'handle_time' => TIMESTAMP,
			'success_time' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_order_refund', $refund_update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
		set_order_refund_status($refund['order_id'], 3);
		order_insert_refund_log($refund['order_id'], $refund['id'], 'handle');
		order_insert_refund_log($refund['order_id'], $refund['id'], 'success');
		return error(0, '退款成功,支付金额已退款至顾客的APP账户余额');
	} elseif($refund['pay_type'] == 'majia') {
		$member = pdo_get('tiny_wmall_members', array('uid' => $refund['uid']));
		if(empty($member['uid_majia'])) {
			return error(-1, "获取马甲用户uid失败");
		}
		mload()->model('plugin');
		pload()->model('majiaApp');
		$status = majia_user_credit_add($member['uid_majia'], $refund['fee'], "外卖订单退款,订单ID:{$refund['order_id']}", array('out_trade_code' => $refund['order_sn']));
		if(is_error($status)) {
			return error(-1, "退款失败:{$status['message']}");
		}
		$refund_update = array(
			'status' => 3,
			'account' => '支付用户的马甲APP账户余额',
			'channel' => 'ORIGINAL',
			'handle_time' => TIMESTAMP,
			'success_time' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_order_refund', $refund_update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
		set_order_refund_status($refund['order_id'], 3);
		order_insert_refund_log($refund['order_id'], $refund['id'], 'handle');
		order_insert_refund_log($refund['order_id'], $refund['id'], 'success');
		return error(0, '退款成功,支付金额已退款至顾客的马甲APP账户余额');
	} elseif($refund['pay_type'] == 'peerpay') {
		return error(-1, '退款失败,找人代付不支持退款功能。如需退款,请自行处理');
	}
}

function order_query_payrefund($refund_id) {
	global $_W;
	$refund = pdo_get('tiny_wmall_order_refund', array('uniacid' => $_W['uniacid'], 'id' => $refund_id));
	if(empty($refund)) {
		return error(-1, '退款申请不存在或已删除');
	}
	if($refund['status'] != 2) {
		return error(-1, '退款已处理');
	}
	if($refund['pay_type'] == 'wechat') {
		//只有微信需要查询,余额和支付宝不需要
		mload()->classs('wxpay');
		$pay = new WxPay($refund['order_channel']);
		$response = $pay->payRefund_query(array('out_refund_no' => $refund['out_refund_no']));
		if(is_error($response)) {
			return $response;
		}
		$wechat_status = $pay->payRefund_status();
		$update = array(
			'status' => $wechat_status[$response['refund_status_0']]['value'],
		);
		if($response['refund_status_0'] == 'SUCCESS') {
			$update['channel'] = $response['refund_channel_0'];
			$update['account'] = $response['refund_recv_accout_0'];
			$update['success_time'] = TIMESTAMP;
			pdo_update('tiny_wmall_order_refund', $update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
			set_order_refund_status($refund['order_id'], 3);
			order_insert_refund_log($refund['order_id'], $refund['id'], 'success');
			return error(0, "退款成功,支付金额已退款至顾客的微信账号:{$response['refund_recv_accout_0']}");
		} else {
			set_order_refund_status($refund['order_id'], $update['status']);
			pdo_update('tiny_wmall_order_refund', $update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
			return error(0, '退款进行中, 请耐心等待。微信官方说明：退款有一定延时，用零钱支付的退款20分钟内到账，银行卡支付的退款3个工作日后重新查询退款状态。');
		}
	}
	return true;
}

function order_refund_status_update($order_id, $refund_id, $type, $extra = array()) {
	global $_W;
	if(empty($refund_id)) {
		$refund = pdo_fetch('select id from ' . tablename('tiny_wmall_order_refund') . ' where uniacid = :uniacid and order_id = :order_id and type = :type', array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id, ':type' => 'order'));
		$refund_id = $refund['id'];
	}
	if(empty($refund_id)) {
		return error(-1, '退款记录不存在');
	}
	if($type == 'query') {
		$result = order_query_payrefund($refund_id);
	} elseif($type == 'handle') {
		$result = order_begin_payrefund($refund_id);
		if(is_error($result)) {
			order_refund_notice($refund_id, 'fail', array('note' => "失败原因: {$result['message']}"));
		} else {
			mlog(1005, $order_id);
			order_refund_notice($refund_id, 'success');
		}
	} elseif($type == 'status') {
		$refund = pdo_get('tiny_wmall_order_refund', array('uniacid' => $_W['uniacid'], 'id' => $refund_id));
		if(empty($refund)) {
			$result = error(-1, '退款申请不存在或已删除');
		} else {
			pdo_update('tiny_wmall_order_refund', array('status' => 3), array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
			order_insert_refund_log($order_id, $refund_id, 'success');
			set_order_refund_status($order_id, 3);
			mlog(1004, $order_id);
			$result = error(0, '设置为已退款成功');
		}
	}
	return $result;
}

function set_order_refund_status($order_id, $status) {
	global $_W;
	$refund_status = $status;
	if($refund_status == 3) {
		$refunds = pdo_fetchall('select id, status from ' . tablename('tiny_wmall_order_refund') . ' where uniacid = :uniacid and order_id = :order_id', array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id));
		if(!empty($refunds)) {
			$length = count($refunds);
			if($length > 1) {
				foreach($refunds as $refund) {
					if($refund['status'] == 1 || $refund['status'] == 2) {
						$refund_status = $refund['status'];
						break;
					}
				}
			}
		}
	}
	pdo_update('tiny_wmall_order', array('refund_status' => $refund_status), array('uniacid' => $_W['uniacid'], 'id' => $order_id));
	return true;
}

function order_fetchall_refund($order_id, $filter = array()) {
	global $_W;
	$condition = ' where uniacid = :uniacid and order_id = :order_id order by id desc';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':order_id' => $order_id
	);
	//$refunds = pdo_fetchall('select a.*, (SELECT group_concat(b.id, b.type, b.title,b.note, b.addtime) from ' . tablename('tiny_wmall_order_refund_log') . ' as b where a.id = b.refund_id) as logs from ' . tablename('tiny_wmall_order_refund') . ' as a ' . $condition, $params);
	//$refunds = pdo_fetchall('select a.*, b.* from ' . tablename('tiny_wmall_order_refund') . ' as a left join ' . tablename('tiny_wmall_order_refund_log') . ' as b on a.id = b.refund_id ' . $condition, $params);
	$refunds = pdo_fetchall('select * from ' . tablename('tiny_wmall_order_refund') . $condition, $params);
	if(!empty($refunds)) {
		$refund_status = order_refund_status();
		$refund_channel = order_refund_channel();
		foreach($refunds as &$refund) {
			$refund['data'] = iunserializer($refund['data']);
			if(!empty($refund['data']['refund_info']['thumbs'])) {
				foreach($refund['data']['refund_info']['thumbs'] as &$thumb) {
					$thumb = tomedia($thumb);
				}
			}
			if(!empty($refund['data']['refund_goods'])) {
				foreach($refund['data']['refund_goods'] as &$gthumb) {
					$gthumb['thumb'] = tomedia($gthumb['thumb']);
				}
				$refund['data']['refund_goods'] = array_values($refund['data']['refund_goods']);
			}
			$refund['status_cn'] = $refund_status[$refund['status']]['text'];
			$refund['channel_cn'] = $refund_channel[$refund['channel']]['text'];
			if($filter['refund_logs'] == 1) {
				$logs = pdo_fetchall('select * from ' . tablename('tiny_wmall_order_refund_log') . ' where uniacid = :uniacid and refund_id = :refund_id order by id asc', array(':uniacid' => $_W['uniacid'], ':refund_id' => $refund['id']));
				$length = 0;
				foreach($logs as &$log) {
					$length++;
					$log['addtime_cn'] = date('Y-m-d H:i', $log['addtime']);
				}
				$refund['logs'] = $logs;
				$refund['logs_length'] = $length;
			}
		}
	}
	return $refunds;
}

//pay_types
function order_refund_status() {
	$refund_status = array(
		'1' => array(
			'css' => 'label label-info',
			'text' => '退款申请中',
		),
		'2' => array(
			'css' => 'label label-warning',
			'text' => '退款处理中',
		),
		'3' => array(
			'css' => 'label label-success',
			'text' => '退款成功',
		),
		'4' => array(
			'css' => 'label label-danger',
			'text' => '退款失败',
		),
		'5' => array(
			'css' => 'label label-default',
			'text' => '退款状态未确定',
		),
		'6' => array(
			'css' => 'label label-danger',
			'text' => '退款被驳回',
		),
	);
	return $refund_status;
}

function to_paytype($type, $key = 'all') {
	$data = array(
		'' => '未支付',
		'alipay' => array(
			'css' => 'label label-info',
			'text' => '支付宝',
		),
		'wechat' => array(
			'css' => 'label label-success',
			'text' => '微信支付',
		),
		'credit' => array(
			'css' => 'label label-warning',
			'text' => '余额支付',
		),
		'delivery' => array(
			'css' => 'label label-primary',
			'text' => '货到付款',
		),
		'cash' => array(
			'css' => 'label label-primary',
			'text' => '现金支付',
		),
		'qianfan' => array(
			'css' => 'label label-primary',
			'text' => 'APP支付',
		),
		'majia' => array(
			'css' => 'label label-primary',
			'text' => 'APP支付',
		),
		'peerpay' => array(
			'css' => 'label label-primary',
			'text' => '找人代付',
		),
		'eleme' => array(
			'css' => 'label label-primary',
			'text' => '饿了么支付',
		),
		'maituan' => array(
			'css' => 'label label-primary',
			'text' => '美团支付',
		),
	);
	if($key == 'all') {
		return $data[$type];
	} elseif($key == 'text') {
		return $data[$type]['text'];
	} elseif($key == 'css') {
		return $data[$type]['css'];
	}
}

function order_refund_channel() {
	$refund_channel = array(
		'ORIGINAL' => array(
			'css' => 'label label-warning',
			'text' => '原路返回',
		),
		'BALANCE' => array(
			'css' => 'label label-danger',
			'text' => '退回余额',
		),
	);
	return $refund_channel;
}

function order_comment_status() {
	$status = array(
		'0' => array(
			'css' => 'color-primary',
			'text' => '待审核',
		),
		'1' => array(
			'css' => 'color-success',
			'text' => '审核通过',
		),
		'2' => array(
			'css' => 'color-danger',
			'text' => '审核未通过',
		),
	);
	return $status;
}

function order_status_update($id, $type, $extra = array()) {
	global $_W;
	$order = order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
	$_W['agentid'] = $order['agentid'];
	if($_W['agentid'] > 0) {
		$_W['we7_wmall']['config'] = get_system_config();
		$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
	}
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $order['sid']), array('delivery_mode', 'auto_handel_order', 'auto_print_order', 'auto_notice_deliveryer', 'data'));

	if(is_open_order($order)) {
		$store['data'] = iunserializer($store['data']);
		mload()->model('plugin');
		if($order['order_plateform'] == 'eleme') {
			$_W['_plugin'] = array(
				'name' => 'eleme'
			);
			$config_open = $store['data']['eleme'];
			$openOrderId = $order['elemeOrderId'];
		} elseif($order['order_plateform'] == 'meituan') {
			$_W['_plugin'] = array(
				'name' => 'meituan'
			);
			$config_open = $store['data']['meituan'];
			$openOrderId = $order['meituanOrderId'];
		}
		pload()->classs('order');
		$openOrder = new order($order['sid']);
		$store['auto_handel_order'] = $config_open['order']['auto_handel_order'];
		$store['auto_notice_deliveryer'] = $config_open['order']['auto_notice_deliveryer'];
		$store['auto_print'] = $config_open['order']['auto_print'];
		$store['delivery_mode'] = $config_open['delivery']['delivery_mode'];
	}

	if($type == 'handle') {
		if($order['status'] != 1) {
			return error(-1, '订单状态不是待处理状态,不能接单');
		}
		if(!$order['is_pay'] && $order['order_type'] <= 2) {
			return error(-1, '该订单属于外卖单,并且未支付,不能接单');
		}
		if($extra['role'] == 'printer' && $store['auto_handel_order'] != 2) {
			slog('printnotify', '打印机回调', array(), "自动接单方式不是打印机打印机后自动接单，所以系统没有自动接单,print_sn:{$order['print_sn']}");
			return error(-1, '自动接单方式不是打印机打印机后自动接单，所以系统没有自动接单');
		}
		if(is_open_order($order)) {
			if(!in_array($extra['role'], array('eleme', 'meituan'))) {
				//饿了么开放平台接单后,消息推送时不执行此方法(饿了么推送已接单消息的时候， role = eleme, role != eleme代表非饿了么消息推送接单，而是商户主动接单)
				$result = $openOrder->confirmOrderLite($openOrderId);
				if(is_error($result)) {
					return $result;
				}
			}
		}
		$update = array(
			'status' => 2,
			'handletime' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		pdo_update('tiny_wmall_order_stat', array('status' => 2), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
		if($store['auto_print_order'] == 1) {
			order_print($order['id']);
		}
		order_insert_status_log($order['id'], 'handle');
		order_status_notice($order['id'], 'handle');
		if($store['auto_notice_deliveryer'] == 1 && (empty($order['is_reserve']) || $config_takeout['reserve_order_auto_notice_deliveryer'])) {
			order_status_update($order['id'], 'notify_deliveryer_collect');
		}
		return error(0, '接单成功');
	} elseif($type == 'notify_deliveryer_collect') {
		mload()->model('deliveryer');
		if($order['order_type'] > 1) {
			return error(-1, '订单类型不是外卖单,不需要通知配送员抢单');
		}
		if($order['status'] > 3 && $extra['channel'] != 're_notify_deliveryer_collect') {
			return error(-1, '订单状态有误');
		}
		$update = array(
			'status' => 3,
			'delivery_status' => 3,
			'delivery_type' => $store['delivery_mode'],
			'clerk_notify_collect_time' => TIMESTAMP,
		);
		if($extra['channel'] == 're_notify_deliveryer_collect') {
			$update['deliveryer_id'] = 0;
			if(in_array($extra['role'], array('dada', 'uupaotui', 'shansong', 'dianwoda'))) {
				$third_partys = array(
					'dada' => '达达',
					'uupaotui' => 'UU跑腿',
					'shansong' => '闪送',
					'dianwoda' => '点我达'
				);
				order_insert_status_log($order['id'], 'third_party_cancel_order', "{$third_partys[$extra['role']]}配送取消了订单, 平台正在重新分配配送员");
			}
		} elseif($extra['channel'] == 'delivery_transfer') {
			unset($update['clerk_notify_collect_time']);
		}
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		pdo_update('tiny_wmall_order_stat', array('status' => 3), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
		order_insert_status_log($order['id'], 'delivery_wait');
		if($extra['channel'] == 're_notify_deliveryer_collect') {
			deliveryer_order_num_update($order['deliveryer_id']);
		}
		//如果由第三方平台配送,delivery_type为0,所以不需要通知系统的配送员进行配送
		if(!$update['delivery_type']) {
			return error(0, '通知配送员抢单成功,请耐心等待配送员接单');
		}
		if($order['delivery_type'] == 2) {
			if($config_takeout['dispatch_mode'] <= 1 || empty($store['delivery_mode']) || !empty($extra['force'])) {
				//抢单模式
				if(empty($extra['notify_channel']) || ($extra['notify_channel'] == 'first' && empty($config_takeout['notify_rule_deliveryer']['notify_delay']))) {
					order_deliveryer_notice($order['id'], 'delivery_wait');
				}
			} elseif($config_takeout['dispatch_mode'] == 2) {
				//管理员派单(只需要通知平台管理员调度即可)
				order_manager_notice($order['id'], 'new_delivery');
			} elseif($config_takeout['dispatch_mode'] == 3) {
				//系统自动分配
				$result = order_dispatch_analyse($id);
				if(is_error($result)) {
					slog('takeoutdispatcherror', "系统分配订单失败,订单id:{$order['id']}", array(), "失败原因：{$result['message']}");
					order_manager_notice($order['id'], 'dispatch_error', "失败原因：{$result['message']}");
					return $result;
				}
				$deliveryer = array_shift($result['deliveryers']);
				$status = order_assign_deliveryer($id, $deliveryer['id']);
				if(is_error($status)) {
					slog('takeoutdispatcherror', "系统分配订单失败,订单id:{$order['id']}", array(), "失败原因：{$status['message']}");
					return $status;
				}
			} elseif($config_takeout['dispatch_mode'] == 4) {
				//系统自动分配
				$result = order_dispatch_analyse1($id);
				if(is_error($result)) {
					slog('takeoutdispatcherror', "系统分配订单失败,订单id:{$order['id']}", array(), "失败原因：{$result['message']}");
					order_manager_notice($order['id'], 'dispatch_error', "失败原因：{$result['message']}");
					return $result;
				}
			}
		} else {
			//店内配送模式
			if(empty($extra['notify_channel']) || ($extra['notify_channel'] == 'first' && empty($config_takeout['notify_rule_deliveryer']['notify_delay']))) {
				order_deliveryer_notice($order['id'], 'delivery_wait');
			}
		}
		return error(0, '通知配送员抢单成功,请耐心等待配送员接单');
	} elseif($type == 'cancel') {
		mload()->model('deliveryer');
		//订单状态, 1:已提交,待确认 2:商家已接单 3: 待配送 4:配送中 5:已完成 6:已取消
		if($order['status'] == 5) {
			return error(-1, '系统已完成， 不能取消订单');
		}
		if($order['status'] == 6) {
			return error(-1, '系统已取消， 不能取消订单');
		}
		if(in_array($_W['role'], array('merchanter', 'clerker')) && $order['status'] > 1 && $config_takeout['cancel_after_handle'] == 0) {
			return error(-1, '接单后不可取消订单');
		}
		if($_W['role'] == 'consumer') {
			if($order['status'] == 2) {
				if($config_takeout['customer_cancel_status'] == 1) {
					if(empty($config_takeout['customer_cancel_timelimit']) || TIMESTAMP - $order['handletime'] >= $config_takeout['customer_cancel_timelimit'] * 60) {
						return error(-1, '商户已接单,如需取消订单请联系商户处理');
					}
				} else {
					return error(-1, '商户已接单,如需取消订单请联系商户处理');
				}
			} elseif($order['status'] == 1) {
				if($config_takeout['customer_cancel_status'] != 1 && $order['is_pay'] == 1) {
					return error(-1, '该订单已支付,如需取消订单请联系商户处理');
				}
			} else {
				return error(-1, '商户已接单,如需取消订单请联系商户处理');
			}
		}
		if(is_open_order($order)) {
			if(!in_array($extra['role'], array('eleme', 'meituan'))) {
				//饿了么开放平台取消订单后,消息推送时不执行此方法,
				//本地取消美团/饿了么订单，调用接口通知美团/饿了么取消订单
				if($order['order_plateform'] == 'meituan') {
					$extra['remark'] = $extra['note'];
				}
				$result = $openOrder->cancelOrderLite($openOrderId, $extra['reason'], $extra['remark']);
				if(is_error($result)) {
					return $result;
				}
			}
		}
		//取消订单时，如果订单已经推送到达达，需要请求接口取消达达的订单
		if(get_order_data($order, 'data.status') == 1 && $extra['role'] !== 'dada'){
			mload()->model('plugin');
			$_W['_plugin'] = array(
				'name' => 'dada'
			);
			pload()->classs('dada');
			$dada = new DaDa();
			$result = $dada->cancelOrder($id);
			if(is_error($result)) {
				return $result;
			}
		}

		//当订单已经推送到UU跑腿,并且是店内配送员接单时，需要取消UU跑腿的订单
		if(get_order_data($order, 'uupaotui.status') == 1 && $extra['role'] !== 'uupaotui'){
			mload()->model('plugin');
			$_W['_plugin'] = array(
				'name' => 'uupaotui'
			);
			pload()->classs('uu');
			$uupaotui = new uuPaoTui($order['sid']);
			$result = $uupaotui->cancelOrder($order['ordersn'], $extra['note']);
			if(is_error($result)) {
				return $result;
			}
		}
		if(get_order_data($order, 'shansong.status') == 1 && $extra['role'] !== 'shansong'){
			mload()->model('plugin');
			$_W['_plugin'] = array(
				'name' => 'shansong'
			);
			pload()->classs('shansong');
			$shanSong = new shanSong($order['id'], $order['sid']);
			$result = $shanSong->cancelOrder();
			if(is_error($result)) {
				return $result;
			}
		}
		//当订单已经推送到点我达, 需要取消点我达的订单
		if(get_order_data($order, 'dianwoda.status') == 1 && $extra['role'] !== 'dianwoda'){
			mload()->model('plugin');
			$_W['_plugin'] = array(
				'name' => 'dianwoda'
			);
			pload()->classs('dianwoda');
			$dianwoda = new dianwoda($order['id'], $order['sid']);
			$result = $dianwoda->orderCancel();
			if(is_error($result)) {
				return $result;
			}
		}
		$is_refund = 0;
		pdo_update('tiny_wmall_order_stat', array('status' => 6), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));

		//第三方平台的订单取消后,由第三方进行退款
		if(is_open_order($order) || !$order['is_pay'] || $order['final_fee'] <= 0 || ($order['is_pay'] == 1 && ($order['pay_type'] == 'delivery' || $order['pay_type'] == 'cash'))) {
			pdo_update('tiny_wmall_order', array('status' => 6, 'delivery_status' => 6, 'spreadbalance' => 1, 'is_remind' => 0), array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
			order_insert_status_log($order['id'], 'cancel', $extra['note']);
			if($extra['reason'] != 'over_paylimit') {
				//超时未支付的订单，取消时候不通知顾客
				order_status_notice($order['id'], 'cancel', $extra['note']);
				if($order['deliveryer_id'] > 0 && empty($extra['deliveryer_id'])) {
					order_deliveryer_notice($order['id'], 'cancel', $order['deliveryer_id']);
				}
			}
		} else {
			if($order['refund_status'] > 0) {
				$order_refund = pdo_get('tiny_wmall_order_refund', array('uniacid' => $_W['uniacid'], 'order_id' => $order['id'], 'type' => 'order'), array('id'));
				if(!empty($order_refund)) {
					return error(-1, '退款申请处理中, 请勿重复发起');
				}
			}
			$update = array(
				'status' => 6,
				'delivery_status' => 6,
				'refund_status' => 1, //发起退款申请
				'refund_fee' => $order['final_fee'],
				'spreadbalance' => 1,
				'is_remind' => 0,
			);
			pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
			order_insert_status_log($order['id'], 'cancel', $extra['note']);

			$refund_fee = $order['final_fee'] - $order['refund_fee'];
			if($refund_fee > 0) {
				$refund = array(
					'uniacid' => $order['uniacid'],
					'acid' => $order['acid'],
					'sid' => $order['sid'],
					'uid' => $order['uid'],
					'order_id' => $order['id'],
					'order_sn' => $order['ordersn'],
					'order_channel' => $order['order_channel'],
					'pay_type' => $order['pay_type'],
					'fee' => $order['final_fee'] - $order['refund_fee'],
					'total_fee' => $order['data']['final_fee_pay'],
					'status' => 1,
					'out_trade_no' => $order['out_trade_no'],
					'out_refund_no' => date('YmdHis') . random(10, true),
					'apply_time' => TIMESTAMP,
					'reason' => $extra['note'] ? $extra['note'] : '订单取消，发起退款'
				);
				pdo_insert('tiny_wmall_order_refund', $refund);
				$refund_id = pdo_insertid();
				$is_refund = 1;
				set_order_refund_status($order['id'], 1);
				order_insert_refund_log($order['id'], $refund_id, 'apply');
				if($config_takeout['auto_refund_cancel_order'] == 1 || $extra['force_refund'] == 1) {
					$refund_result = order_begin_payrefund($refund_id);
					if(is_error($refund_result)) {
						order_refund_notice($refund_id, 'fail', array('note' => "失败原因: {$refund_result['message']}"));
					} else {
						order_refund_notice($refund_id, 'success');
					}
				}
			}
			$note = array(
				"取消原因: {$extra['note']}",
				"退款金额: {$refund_fee}元",
				"已付款项会在1-3工作日内返回您的账号",
			);
			order_status_notice($order['id'], 'cancel', $note);
			if(!isset($refund_result)) {
				order_refund_notice($refund_id, 'apply', array('note' => "取消原因: {$extra['note']}"));
			}
			if($_W['role'] != 'deliveryer' && $order['deliveryer_id'] > 0) {
				//如果是配送员自己取消订单,不通知配送员订单已取消。
				order_deliveryer_notice($order['id'], 'cancel', $order['deliveryer_id']);
			}
		}
		//对顾客的订单数据进行更新处理
		$member_mall = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $order['uid']));
		if(!empty($member_mall)) {
			$member_update = array(
				'cancel_num' => $member_mall['cancel_num'] + 1,
				'cancel_price' => round($member_mall['cancel_price'] + $order['final_fee'], 2),
				'cancel_last_time' => TIMESTAMP,
			);
			if(empty($member_mall['cancel_first_time'])) {
				$member_update['cancel_first_time'] = TIMESTAMP;
			}
			pdo_update('tiny_wmall_members', $member_update, array('id' => $member_mall['id']));
			$member_store = pdo_get('tiny_wmall_store_members', array('uniacid' => $_W['uniacid'], 'sid' => $order['sid'], 'uid' => $order['uid']));
			if(empty($member_store)) {
				$insert = array(
					'uniacid' => $_W['uniacid'],
					'sid' => $order['sid'],
					'uid' => $order['uid'],
					'openid' => $order['openid'],
					'cancel_first_time' => TIMESTAMP,
					'cancel_last_time' => TIMESTAMP,
					'cancel_num' => 1,
					'cancel_price' => $order['final_fee'],
				);
				pdo_insert('tiny_wmall_store_members', $insert);
			} else {
				$member_update = array(
					'cancel_num' => $member_store['cancel_num'] + 1,
					'cancel_price' => round($member_store['cancel_price'] + $order['final_fee'], 2),
					'cancel_last_time' => TIMESTAMP,
				);
				pdo_update('tiny_wmall_store_members', $member_update, array('id' => $member_store['id']));
			}
		}
		deliveryer_order_num_update($order['deliveryer_id']);
		//返还红包代金券
		$config_activity = $_W['we7_wmall']['config']['activity'];
		$return_redpacket_status = intval($config_activity['return_redpacket_status']);
		if($return_redpacket_status == 1 && $order['discount_fee'] > 0) {
			pdo_update('tiny_wmall_activity_redpacket_record', array('status' => 1, 'usetime' => 0, 'order_id' => 0), array('uniacid' => $_W['uniacid'], 'uid' => $order['uid'], 'order_id' => $order['id']));
			pdo_update('tiny_wmall_activity_coupon_record', array('status' => 1, 'usetime' => 0, 'order_id' => 0), array('uniacid' => $_W['uniacid'], 'uid' => $order['uid'], 'order_id' => $order['id']));
		}
		mlog(1002, $order['id']);
		return error(0, array('is_refund' => $is_refund, 'refund_message' => $refund_result['message'], 'refund_code' => $refund_result['errno']));
	} elseif($type == 'end') {
		mload()->model('deliveryer');
		if($order['status'] == 5) {
			return error(-1, '系统已完成， 请勿重复操作');
		}
		if($order['status'] == 6) {
			return error(-1, '系统已取消， 不能在进行其他操作');
		}
		if($order['order_type'] > 2 && $order['is_pay'] == 0) {
			return error(-1, '订单未支付，无法完成订单');
		}
		if($config_takeout['delivery_success_verify'] == 1 && $_W['role'] == 'deliveryer') {
			$_deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $order['deliveryer_id']), array('location_x', 'location_y'));
			if(!empty($_deliveryer['location_y']) && !empty($_deliveryer['location_x']) && !empty($order['location_y']) && !empty($order['location_x'])) {
				$distance = distanceBetween($_deliveryer['location_y'], $_deliveryer['location_x'], $order['location_y'], $order['location_x']);
				if($distance > $config_takeout['delivery_success_verify_distance']) {
					return error(-1, "系统检测到距离收货地址还有{$distance}米，不能完成订单。{$config_takeout['delivery_success_verify_distance']}米内，可以完成订单");
				}
			}
		}
		if(is_open_order($order)) {
			if(!in_array($extra['role'], array('eleme', 'meituan'))) {
				//饿了么开放平台完成订单后,消息推送时不执行此方法
				$result = $openOrder->receivedOrderLite($openOrderId);
				if(is_error($result)) {
					//暂时先屏蔽掉此处，因为后台设置了订单自动完成时间， 饿了么和美团完成订单，有accesstoken限制， 如果accesstoken失效，此处直接返回，会造成订单不能完成
					//return $result;
				}
			}
		}

		$is_timeout = 0;
		if(($config_takeout['timeout_limit'] > 0) && (TIMESTAMP - $order['paytime'] > $config_takeout['timeout_limit'] * 60)) {
			$is_timeout = 1;
		}
		$update = array(
			'is_timeout' => $is_timeout,
			'status' => 5,
			'delivery_status' => 5, //已送达
			'endtime' => TIMESTAMP,
			'delivery_success_time' => TIMESTAMP,
			'delivery_success_location_x' => $extra['delivery_success_location_x'],
			'delivery_success_location_y' => $extra['delivery_success_location_y'],
			'is_remind' => 0
		);
		if(!empty($extra['deliveryer_id'])) {
			$update['deliveryer_id'] = $extra['deliveryer_id'];
		}
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		pdo_update('tiny_wmall_order_stat', array('status' => 5), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
		deliveryer_order_num_update($order['deliveryer_id']);
		//订单统计， 商家服务费处理等操作, 客户积分赠送
		if($order['delivery_type'] == 2) {
			if($order['plateform_deliveryer_fee'] > 0) {
				deliveryer_update_credit2($order['deliveryer_id'], $order['plateform_deliveryer_fee'], 1, $order['id']);
			}
			//修改:货到付款不直接扣除配送员账户，在配送统计里计算货到付款金额
			if($config_takeout['deliveryPay_update_deliveryerAccount'] == 1 && $order['pay_type'] == 'delivery') {
				$note = "{$order['id']}属于货到支付单,您线下收取客户{$order['final_fee']}元,平台从您的账户扣除该费用";
				deliveryer_update_credit2($order['deliveryer_id'], -$order['final_fee'], 3, $order['id'], $note);
			}
		}
		if($order['is_pay'] == 1) {
			if(in_array($order['pay_type'], array('wechat', 'alipay', 'credit', 'peerpay', 'qianfan', 'majia', 'eleme', 'meituan')) || ($order['delivery_type'] == 2 && $order['pay_type'] == 'delivery')) {
				store_update_account($order['sid'], $order['store_final_fee'], 1, $order['id']);
			} else {
				$remark = "编号为{$order['id']}的订单属于线下支付,平台需要扣除{$order['plateform_serve_fee']}元服务费";
				store_update_account($order['sid'], -$order['plateform_serve_fee'], 3, $order['id'], $remark);
			}
			if($order['agentid'] > 0) {
				mload()->model('agent');
				$remark = '';
				agent_update_account($order['agentid'], $order['agent_final_fee'], 1, $order['id'], $remark, 'takeout');
			}
		}
		/*		mload()->model('queue');
				$queue_params = array(
					'order_id' => $order['id']
					'type' => 'takeout',
					'data' => ''
				);
				queue_add($queue_params);*/
		//门店销量
		$update_sailed = 1;
		if($config_takeout['store_sailed_type'] == 'goods') {
			$update_sailed = $order['num'];
		}
		pdo_query('UPDATE ' . tablename('tiny_wmall_store') . " set sailed = sailed + {$update_sailed} WHERE uniacid = :uniacid AND id = :id", array(':uniacid' => $_W['uniacid'], ':id' => $order['sid']));

		//订单完成对客户赠送积分
		$credit1_config = $config_takeout['grant_credit']['credit1'];
		if($credit1_config['status'] == 1 && $credit1_config['grant_num'] > 0) {
			if($order['uid'] > 0) {
				$credit1 = $credit1_config['grant_num'];
				if($credit1_config['grant_type'] == 2) {
					$credit1 = round($order['final_fee'] * $credit1_config['grant_num'], 2);
				}
				if($credit1 > 0) {
					mload()->model('member');
					$result = member_credit_update($order['uid'], 'credit1', $credit1, array(0, "外送模块订单完成, 赠送{$credit1}积分"));
					if(is_error($result)) {
						slog('credit1Update', "下单送积分-order_id:{$order['id']}", array('order_id' => $order['id'], 'uid' => $order['uid'], 'credit_type' => 'credit1') ,$result['message']);
					}
				}
			}
		}
		//赠送余额
		$cash_grant = order_fetch_discount($order['id'], 'cashGrant');
		if(!empty($cash_grant) && $cash_grant['fee'] > 0) {
			mload()->model('member');
			$result = member_credit_update($order['uid'], 'credit2', $cash_grant['fee'], array(0, "外送模块订单完成, 赠送{$cash_grant['fee']}元"), true);
			if(is_error($result)) {
				slog('credit2Update', "下单返余额-order_id:{$order['id']}", array('order_id' => $order['id'], 'uid' => $order['uid'], 'credit_type' => 'credit2') ,$result['message']);
			}
		}
		//赠送优惠券
		$result = order_coupon_grant($order['id']);
		if(is_error($result)) {
			slog('couponGrant', "满赠优惠券-order_id:{$order['id']}", array('order_id' => $order['id'], 'uid' => $order['uid']) ,$result['message']);
		}
		mload()->model('plugin');
		//赠送红包
		if($order['mall_first_order'] == 1 && check_plugin_perm('shareRedpacket')) {
			pload()->model('shareRedpacket');
			$result = shareRedpacket_sharer_grant($order['uid']);
			if(is_error($result)) {
				slog('shareRedpacket', "分享赠送红包-order_id:{$order['id']}", array('order_id' => $order['id'], 'uid' => $order['uid']) ,$result['message']);
			}
		}
		if(check_plugin_perm('ordergrant')) {
			pload()->model('ordergrant');
			ordergrant_grant($id);
		}
		if(check_plugin_perm('spread')) {
			pload()->model('spread');
			member_spread_confirm($id);
			spread_order_balance($id);
		}
		//对顾客的订单数据进行更新处理
		$member_mall = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $order['uid']));
		if(!empty($member_mall)) {
			$member_update = array(
				'success_num' => $member_mall['success_num'] + 1,
				'success_price' => round($member_mall['success_price'] + $order['final_fee'], 2),
				'success_last_time' => TIMESTAMP,
			);
			if(!$member_mall['success_first_time']) {
				$member_update['success_first_time'] = TIMESTAMP;
			}
			pdo_update('tiny_wmall_members', $member_update, array('id' => $member_mall['id']));
			$member_store = pdo_get('tiny_wmall_store_members', array('uniacid' => $_W['uniacid'], 'sid' => $order['sid'], 'uid' => $order['uid']));
			if(empty($member_store)) {
				$insert = array(
					'uniacid' => $_W['uniacid'],
					'sid' => $order['sid'],
					'uid' => $order['uid'],
					'openid' => $order['openid'],
					'success_first_time' => TIMESTAMP,
					'success_last_time' => TIMESTAMP,
					'success_num' => 1,
					'success_price' => $order['final_fee'],
				);
				pdo_insert('tiny_wmall_store_members', $insert);
			} else {
				$member_update = array(
					'success_num' => $member_store['success_num'] + 1,
					'success_price' => round($member_store['success_price'] + $order['final_fee'], 2),
					'success_last_time' => TIMESTAMP,
				);
				pdo_update('tiny_wmall_store_members', $member_update, array('id' => $member_store['id']));
			}
		}
		//店铺推广员
		if(check_plugin_perm('storebd')) {
			$storebd_config = get_plugin_config('storebd.basic');
			if($storebd_config['status'] == 1) {
				pload()->model('storebd');
				$storebd_order = storebd_user_order_commision($order);
				storebd_user_credit_update($storebd_order);
			}
		}
		if($order['order_type'] == 3 && !empty($order['table_id'])) {//清空桌台
			pdo_update('tiny_wmall_tables', array('order_id' => 0, 'status' => 1), array('uniacid' => $_W['uniacid'], 'id' => $order['table_id']));
		}
		if(check_plugin_perm('svip')) {
			pload()->model('svip');
			svip_task_finish_check($order['uid'], 'oneOrderFee', $order);
		}
		if(check_plugin_perm('zhunshibao')) {//更新order['data']字段需要处理，zhunshibao_check()函数更新data字段没有重新获取
			pload()->model('zhunshibao');
			$order['endtime'] = $update['endtime'];
			zhunshibao_check($order);
		}
		order_insert_status_log($order['id'], 'end');
		order_status_notice($order['id'], 'end');
		mlog(1000, $order['id']);
		return error(0, '完成订单成功');
	} elseif($type == 'delivery_ing') {
		if($order['status'] == 5) {
			return error(-1, '系统已完成， 请勿重复操作');
		}
		if($order['status'] == 6) {
			return error(-1, '系统已取消， 不能在进行其他操作');
		}
		if($store['delivery_mode'] == 2) {
			return error(-1, '门店配送模式为平台配送， 不能直接设置为配送中');
		}
		if($order['deliveryer_id'] > 0) {
			return error(-1, '该订单已有配送员接单, 不能直接设置为配送中');
		}
		$update = array(
			'status' => 4,
			'delivery_status' => 4,
		);
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		pdo_update('tiny_wmall_order_stat', array('status' => 4), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
		order_insert_status_log($order['id'], 'delivery_ing');
		order_status_notice($order['id'], 'delivery_ing');

		//订单配送信息同步到美团
		if($order['order_plateform'] == 'meituan') {
			$_W['_plugin'] = array(
				'name' => 'meituan'
			);
			mload()->model('plugin');
			pload()->classs('order');
			$openOrderId = $order['meituanOrderId'];
			$openOrder = new Order($order['sid']);
			$openOrder->updateOrderDeliverying($openOrderId);
		}
		return error(0, '变更订单状态成功');
	} elseif($type == 'remind') {
		if(in_array($order['status'], array(5, 6)) || ($order['status'] == 1 && $order['is_pay'] == 0)) {
			return error(-1, '订单状态有误，不能催单');
		}
		$is_exist = 0;
		if($extra['role'] == 'eleme') {
			$log = pdo_get('tiny_wmall_order_remind_log', array('remindid' => $extra['remindId'], 'oid' => $order['id']));
			if(!empty($log)) {
				$is_exist = 1;
			}
		}
		if(empty($is_exist)) {
			$log = array(
				'uniacid' => $_W['uniacid'],
				'oid' => $order['id'],
				'remindid' => $extra['remindId'] ? $extra['remindId'] : date('YmdHis') . random(5, true),
				'status' => 0,
				'channel' => $extra['role'],
				'addtime' => TIMESTAMP,
			);
			pdo_insert('tiny_wmall_order_remind_log', $log);
		}
		pdo_update('tiny_wmall_order', array('is_remind' => '1'), array('uniacid' => $_W['uniacid'], 'id' => $id));
		order_insert_status_log($id, 'remind');
		if($order['status'] == 4) {
			order_deliveryer_notice($id, 'remind', $order['deliveryer_id']);
		} else {
			order_clerk_notice($id, 'remind');
		}
		return error(0, '催单成功');
	} elseif($type == 'reply') {
		$reply = trim($extra['reply']);
		if(empty($reply)) {
			return error(-1, '回复内容不能为空');
		}
		if($order['order_plateform'] == 'eleme') {
			$remind_log = pdo_fetch('select id, remindid from ' . tablename('tiny_wmall_order_remind_log') . ' where oid = :oid and channel = :channel order by id desc', array(':oid' => $order['id'], ':channel' => 'eleme'));
			if(empty($remind_log)) {
				return error(-1, '未找到饿了么的催单记录');
			}
			$result = $openOrder->replyReminder($remind_log['remindid'], 'custom', $reply);
			if(is_error($result)) {
				return $result;
			}
		} else {
			$remind_log = pdo_fetch('select id,remindid from ' . tablename('tiny_wmall_order_remind_log') . ' where oid = :oid and channel = :channel order by id desc', array(':oid' => $order['id'], ':channel' => 'system'));
		}
		pdo_update('tiny_wmall_order_remind_log', array('reply' => $reply), array('id' => $remind_log['id']));
		pdo_update('tiny_wmall_order', array('is_remind' => 2), array('uniacid' => $_W['uniacid'], 'id' => $id));
		order_insert_status_log($order['id'], 'remind_reply', $reply);
		order_status_notice($order['id'], 'reply_remind', "回复内容:{$reply}");
		return error(0, '回复顾客催单成功');
	} elseif($type == 'notify_clerk_handle') {
		order_clerk_notice($order['id'], 'place_order', '平台管理员催促您尽快处理该订单');
		return error(0, '通知商户接单成功');
	} elseif($type == 'pay') {
		if($order['is_pay'] == 1) {
			return error(-1, '订单已支付，请勿重复支付');
		}
		$update = array(
			'is_pay' => 1,
			'pay_type' => 'cash',
			'paytime' => TIMESTAMP,
			'store_final_fee' => -$order['plateform_serve_fee'],
		);
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		order_insert_status_log($order['id'], 'pay');
		order_status_notice($order['id'], 'pay');
		return error(0, '设置订单支付成功');
	} elseif($type == 'pay_notice') {
		if($order['is_pay'] == 1) {
			return error(-1, '订单已支付');
		}
		$log = pdo_get('tiny_wmall_order_status_log', array('type' => 'pay_notice', 'oid' => $order['id']));
		if(!empty($log)) {
			return true;
		}
		order_insert_status_log($order['id'], 'pay_notice');
		order_status_notice($order['id'], 'pay_notice');
		return error(0, '未支付提醒成功');
	}
}

function order_assign_deliveryer($order_id, $deliveryer_id, $update_deliveryer = false, $note = '') {
	global $_W;
	$order = order_fetch($order_id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	if($order['status'] == 5) {
		return error(-1, '系统已完成， 请勿重复操作');
	}
	if($order['status'] == 6) {
		return error(-1, '系统已取消， 不能在进行其他操作');
	}
	if($order['deliveryer_id'] > 0 && !$update_deliveryer) {
		return error(-1000, "#{$order['serial_sn']}订单已经被配送员接单");
	}
	mload()->model('deliveryer');
	$deliveryer = deliveryer_fetch($deliveryer_id);
	if(empty($deliveryer)) {
		return error(-1, '配送员不存在,请指定其他配送员配送');
	}
	if($deliveryer['status'] != 1) {
		return error(-1, '配送员已被删除,请指定其他配送员配送');
	}
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $order['sid']), array('delivery_mode'));
	if($store['delivery_mode'] == 1) {
		$permission = pdo_getall('tiny_wmall_store_deliveryer', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $deliveryer_id), array('sid'), 'sid');
		if(empty($permission)) {
			return error(-1, "门店配送模式为店内配送,没有添加店内配送员");
		}
		if(!in_array($order['sid'], array_keys($permission))) {
			return error(-1, "门店配送模式为店内配送，该配送员没有该门店的配送权限");
		}
	} else {
		if(empty($deliveryer['is_takeout'])) {
			return error(-1, "该配送员没有平台外卖订单的配送权限");
		}
	}
	$update = array(
		'status' => 4,
		'delivery_status' => 7, //订单已被抢单
		'deliveryer_id' => $deliveryer_id,
		'delivery_type' => $store['delivery_mode'],
		'delivery_assign_time' => TIMESTAMP,
		'delivery_collect_type' => $update_deliveryer ? 2 : 1,
		'transfer_delivery_status' => $update_deliveryer ? 0 : $order['transfer_delivery_status'],
		'plateform_deliveryer_fee' => order_calculate_deliveryer_fee($order, $deliveryer),
	);
	pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
	pdo_update('tiny_wmall_order_stat', array('status' => 4), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
	order_update_bill($order['id'], array('deliveryer_id' => $deliveryer_id));
	if($order['deliveryer_id'] > 0) {
		deliveryer_order_num_update($order['deliveryer_id']);
	}
	deliveryer_order_num_update($deliveryer_id);
	$note = "配送员：{$deliveryer['title']}, 手机号：{$deliveryer['mobile']}";
	order_insert_status_log($order['id'], 'delivery_assign', $note);
	$remark = array("配送员：{$deliveryer['title']}", "手机号：{$deliveryer['mobile']}");
	order_status_notice($order['id'], 'delivery_assign', $remark);
	order_deliveryer_notice($order['id'], 'new_delivery', $deliveryer['id']);
	//订单配送信息同步到美团
	if($order['order_plateform'] == 'meituan') {
		$_W['_plugin'] = array(
			'name' => 'meituan'
		);
		mload()->model('plugin');
		pload()->classs('order');
		$openOrderId = $order['meituanOrderId'];
		$openOrder = new Order($order['sid']);
		$openOrder->updateOrderDeliverying($openOrderId, $deliveryer);
	}
	return error(0, '订单分派配送员成功');
}

function order_system_status_update($id, $type, $extra = array()) {
	global $_W;
	set_time_limit(0);
	$order = order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $order['sid']), array('delivery_mode', 'auto_handel_order', 'auto_print_order', 'auto_notice_deliveryer'));
	$_W['agentid'] = $order['agentid'];
	$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
	if($type == 'pay') {
		if($order['is_pay'] == 1) {
			return error(-1, '订单已支付，请勿重复支付');
		}
		$update = array(
			'is_pay' => 1,
			'order_channel' => $extra['channel'],
			'pay_type' => $extra['type'],
			'final_fee' => $extra['card_fee'],
			'paytime' => TIMESTAMP,
			'transaction_id' => $extra['transaction_id'],
			'out_trade_no' => $extra['uniontid'],
		);
		if($extra['type'] == 'finishMeal') {
			$update['is_pay'] = 0;
			$update['pay_type'] = 'finishMeal';
			$update['paytime'] = 0;
		}
		$update['data'] = $order['data'];
		$update['data']['final_fee_pay'] = $extra['card_fee'];
		if(!empty($extra['prepay_id'])) {
			$update['data']['prepay_id'] = $extra['prepay_id'];
			$update['data']['prepay_times'] = 3;
		}
		$update['data'] = iserializer($update['data']);
		$print_order = 0;
		if($store['auto_print_order'] == 0 || ($store['auto_print_order'] == 1 && $store['auto_handel_order'] == 1)) {
			$print_order = 1;
		}
		if($order['order_type'] <= 2) {
			$reserve_status = order_is_reserve($order);
			$update['is_reserve'] = $reserve_status['is_reserve'];
			if($update['is_reserve'] == 1) {
				$notice_clerk_before_delivery = empty($reserve_status['notice_clerk_before_delivery']) ? 40 : intval($reserve_status['notice_clerk_before_delivery']);
				$update['reserve_notify_clerk_starttime'] = $order['deliverytime'] - $notice_clerk_before_delivery * 60;
			}
			if(!empty($order['data']['meal_redpacket']) && $order['data']['meal_redpacket']['fee'] > 0) {
				$update['final_fee'] = $update['final_fee'] - $order['data']['meal_redpacket']['fee'];
			}
			if(!empty($order['data']['svip']) && $order['data']['svip']['fee'] > 0) {
				$update['final_fee'] = $update['final_fee'] - $order['data']['svip']['fee'];
			}
			if($store['auto_handel_order'] == 1) {
				$update['status'] = 2;
				$update['handletime'] = TIMESTAMP;
				if($order['order_type'] == 2) {
					$update['status'] = 4;
				}
				if($store['auto_notice_deliveryer'] == 1 && $order['order_type'] == 1 && empty($update['is_reserve'])) {
					$update['delivery_type'] = $store['delivery_mode'];
					$update['status'] = 3; //待配送（待抢单）
					$update['delivery_status'] = 3;
					$update['deliveryer_id'] = 0;
					$update['clerk_notify_collect_time'] = TIMESTAMP;
				}
				pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
				order_insert_status_log($order['id'], 'pay');
				order_insert_status_log($order['id'], 'handle');
				if($store['auto_notice_deliveryer'] == 1 && empty($update['is_reserve'])) {
					order_insert_status_log($order['id'], 'delivery_wait');
				}
				if($print_order == 1) {
					order_print($order['id']);
				}
				order_status_notice($order['id'], 'handle');
				order_clerk_notice($order['id'], 'place_order', '', array('is_reserve' => $update['is_reserve']));
				if($store['auto_notice_deliveryer'] == 1 && (empty($update['is_reserve']) || $config_takeout['reserve_order_auto_notice_deliveryer'] == 1)) {
					order_status_update($order['id'], 'notify_deliveryer_collect', array('notify_channel' => 'first'));
				}
			} else {
				pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
				order_insert_status_log($order['id'], 'pay');
				if($print_order == 1) {
					order_print($order['id']);
				}
				order_status_notice($order['id'], 'pay');
				//下单立即通知
				if(empty($config_takeout['notify_rule_clerk']['notify_delay'])) {
					order_clerk_notice($order['id'], 'place_order', '', array('is_reserve' => $update['is_reserve']));
				}
			}
			if(check_plugin_perm('superRedpacket')) {
				mload()->model('plugin');
				pload()->model('superRedpacket');
				$result = superRedpacket_share_insert($order['id']);
				if(is_error($result)) {
					slog('superRedpacket_share', "超级红包-分享红包-order_id:{$order['id']}", array('order_id' => $order['id'], 'uid' => $order['uid']), $result['message']);
				}
			}
			if(!empty($order['data']['meal_redpacket'])) {
				mload()->model('plugin');
				pload()->model('mealRedpacket');
				$meal_redpacket = $order['data']['meal_redpacket'];
				$extra_meal = array(
					'type' => $extra['type'],
					'order_id' => $order['id'],
					'status' => 2,
					'pre_mealredpacket_used_key' => $meal_redpacket['pre_mealredpacket_used_key'],
				);
				$result = mealRedpacket_order_update($meal_redpacket['meal_order_id'], 'pay', $extra_meal);
				if(is_error($result)) {
					slog('mealRedpacket', "外卖订单购买套餐红包-order_id:{$meal_redpacket['meal_order_id']}", array('order_id' => $meal_redpacket['meal_order_id'], 'uid' => $order['uid']), $result['message']);
				}
			}
			if(!empty($order['data']['svip'])) {
				$data_svip = $order['data']['svip'];
				mload()->model('plugin');
				pload()->model('svip');
				$svip_extra = array(
					'channel' => $extra['channel'],
					'type' => $extra['type'],
					'card_fee' => $data_svip['fee'],
				);
				$result = svip_meal_order_update($data_svip['svip_order_id'], 'pay', $svip_extra);
				if(is_error($result)) {
					slog('svip', "外卖订单购买超级会员-order_id:{$data_svip['svip_order_id']}", array('uid' => $order['uid']), $result['message']);
				}
				if($data_svip['pre_svip_redpacket_id'] > 0) {
					$used_svip_redpacket = pdo_get('tiny_wmall_svip_redpacket', array('uniacid' => $_W['uniacid'], 'id' => $data_svip['pre_svip_redpacket_id']));
					$redpacket_insert = array(
						'uniacid' => $_W['uniacid'],
						'title' => $used_svip_redpacket['title'],
						'sid' => $used_svip_redpacket['sid'],
						'activity_id' => $used_svip_redpacket['id'],
						'uid' => $order['uid'],
						'openid' => $order['openid'],
						'order_id' => $order['id'],
						'channel' => 'svip',
						'type' => 'grant',
						'discount' => $used_svip_redpacket['discount'],
						'condition' => $used_svip_redpacket['condition'],
						'starttime' => TIMESTAMP,
						'endtime' => TIMESTAMP,
						'usetime' => TIMESTAMP,
						'status' => 2,
						'granttime' => TIMESTAMP,
						'grantday' => date('Ymd'),
					);
					pdo_insert('tiny_wmall_activity_redpacket_record', $redpacket_insert);
				}
			}
			order_plateform_notice($order, 'ordernew');
		} elseif($order['order_type'] == 3) {
			//店内订单
			mload()->model('table');
			$update['status'] = 2;
			$update['handletime'] = TIMESTAMP;
			pdo_update('tiny_wmall_order', $update, array('id' => $order['id'], 'uniacid' => $_W['uniacid']));

			table_order_update($order['table_id'], $order['id'], 4);
			order_insert_status_log($order['id'], 'pay');
			if($print_order == 1) {
				order_print($order['id']);
			}
			order_status_notice($order['id'], 'pay');
			order_clerk_notice($order['id'], 'store_order_pay');
		} elseif ($order['order_type'] == 4) {
			$update['status'] = 2;
			pdo_update('tiny_wmall_order', $update, array('id' => $order['id'], 'uniacid' => $_W['uniacid']));
			order_insert_status_log($order['id'], 'pay');
			if($print_order == 1) {
				order_print($order['id']);
			}
			order_status_notice($order['id'], 'pay');
			order_clerk_notice($order['id'], 'reserve_order_pay');
		}
		//减库存
		$stat = pdo_getall('tiny_wmall_order_stat', array('uniacid' => $_W['uniacid'], 'oid' => $order['id']), array('id', 'sid', 'goods_id', 'option_id', 'goods_num', 'goods_discount_num', 'bargain_id', 'total_update_status'));
		if(!empty($stat)) {
			foreach($stat as $row) {
				pdo_query('UPDATE ' . tablename('tiny_wmall_goods') . " set sailed = sailed + {$row['goods_num']} WHERE uniacid = :uniacid AND id = :id", array(':uniacid' => $_W['uniacid'], ':id' => $row['goods_id']));
				if(!$row['total_update_status']) {
					if(!$row['option_id']) {
						$goods = pdo_get('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'id' => $row['goods_id']));
						if($goods['total'] != -1 && $goods['total'] > 0) {
							pdo_query('UPDATE ' . tablename('tiny_wmall_goods') . " set total = total - {$row['goods_num']} WHERE uniacid = :aid AND id = :id", array(':aid' => $_W['uniacid'], ':id' => $row['goods_id']));
							$total_now = $goods['total'] - $row['goods_num'];
							if($goods['total_warning'] > 0 && $total_now <= $goods['total_warning']) {
								//库存报警
								goods_total_warning_notice($goods, 0, array('total_now' => $total_now));
							}
						}
						if($row['bargain_id'] > 0 && $row['goods_discount_num'] > 0) {
							$bargain_goods = pdo_get('tiny_wmall_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'bargain_id' => $row['bargain_id'], 'goods_id' => $row['goods_id']));
							if($bargain_goods['discount_available_total'] != -1 && $bargain_goods['discount_available_total'] > 0) {
								pdo_query('UPDATE ' . tablename('tiny_wmall_activity_bargain_goods') . " set discount_available_total = discount_available_total - {$row['goods_discount_num']} WHERE uniacid = :uniacid AND bargain_id = :bargain_id and goods_id = :goods_id", array(':uniacid' => $_W['uniacid'], ':bargain_id' => $row['bargain_id'], ':goods_id' => $row['goods_id']));
							}
						}
					} else {
						$option = pdo_get('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $row['option_id']));
						if(!empty($option) && $option['total'] != -1 && $option['total'] > 0) {
							pdo_query('UPDATE ' . tablename('tiny_wmall_goods_options') . " set total = total - {$row['goods_num']} WHERE uniacid = :uniacid AND id = :id", array(':uniacid' => $_W['uniacid'], ':id' => $row['option_id']));
							$total_now = $option['total'] - $row['goods_num'];
							if($option['total_warning'] > 0 && $total_now <= $option['total_warning']) {
								//库存报警
								goods_total_warning_notice($option['goods_id'], $option, array('total_now' => $total_now));
							}
						}
					}
					pdo_update('tiny_wmall_order_stat', array('total_update_status' => 1), array('id' => $stat['id']));
				}
			}
		}
		return error(0, '订单支付成功');
	}
}

function order_deliveryer_update_status($id, $type, $extra = array()) {
	global $_W;
	$order = order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];

	if($type == 'delivery_assign') {
		if($order['status'] == 5) {
			return error(-1, '系统已完成， 不能抢单或分配订单');
		}
		if($order['status'] == 6) {
			return error(-1, '系统已取消， 不能抢单或分配订单');
		}
		if($order['deliveryer_id'] > 0) {
			return error(-1, '来迟了, 该订单已被别人接单');
		}
		if(in_array($extra['role'], array('eleme', 'meituan','dada', 'uupaotui', 'shansong', 'dianwoda'))) {
			$deliveryer = $extra['deliveryer'];
		} else {
			if(empty($extra['deliveryer_id'])) {
				return error(-1, '配送员id不存在');
			}
			mload()->model('deliveryer');
			$deliveryer = deliveryer_fetch($extra['deliveryer_id']);
			if(empty($deliveryer)) {
				return error(-1, '配送员不存在');
			}
			if($deliveryer['status'] != 1) {
				return error(-1, '配送员已被删除');
			}
			if($order['delivery_type'] == 2) {
				if($deliveryer['collect_max_takeout'] > 0 && $deliveryer['order_takeout_num'] >= $deliveryer['collect_max_takeout']) {
					return error(-1, "每人最多可抢{$deliveryer['collect_max_takeout']}个外卖单");
				}
			}
		}
		$note = "配送员：{$deliveryer['title']}, 手机号：{$deliveryer['mobile']}";
		$log_status = order_insert_status_log($order['id'], 'delivery_assign', $note);
		if(empty($log_status)) {
			return error(-1, '来迟了, 该订单已被别人接单-hash');
		}

		$update = array(
			'status' => 4,
			'delivery_status' => 7, //订单已被抢单
			'deliveryer_id' => $extra['deliveryer_id'],
			'delivery_assign_time' => TIMESTAMP,
			'delivery_handle_type' => !empty($extra['delivery_handle_type']) ? $extra['delivery_handle_type'] : 'wechat',
			'plateform_deliveryer_fee' => order_calculate_deliveryer_fee($order, $deliveryer),
		);
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		pdo_update('tiny_wmall_order_stat', array('status' => 4), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
		order_update_bill($order['id'], array('deliveryer_id' => $extra['deliveryer_id']));
		mload()->model('deliveryer');
		if($order['deliveryer_id'] > 0) {
			deliveryer_order_num_update($order['deliveryer_id']);
		}
		deliveryer_order_num_update($deliveryer['id']);
		$remark = array("配送员：{$deliveryer['title']}", "手机号：{$deliveryer['mobile']}");
		order_status_notice($order['id'], 'delivery_assign', $remark);
		if($config_takeout['deliveryer_collect_notify_clerk'] == 1) {
			order_clerk_notice($order['id'], 'collect', $remark);
			order_print($order['id'], 'collect');
		}
		//当订单已经推送到达达,并且是店内配送员接单时，需要取消达达的订单
		if(get_order_data($order, 'data.status') == 1 && $extra['role'] !== 'dada'){
			mload()->model('plugin');
			$_W['_plugin'] = array(
				'name' => 'dada'
			);
			pload()->classs('dada');
			$dada = new DaDa();
			$result = $dada->cancelOrder($id);
			if(is_error($result)) {
				return $result;
			}
		}

		//当订单已经推送到UU跑腿,又被配送员接单时，需要取消UU跑腿的订单
		if(get_order_data($order, 'uupaotui.status') == 1 && $extra['role'] !== 'uupaotui'){
			mload()->model('plugin');
			$_W['_plugin'] = array(
				'name' => 'uupaotui'
			);
			pload()->classs('uu');
			$uupaotui = new uuPaoTui($order['sid']);
			$result = $uupaotui->cancelOrder($order['ordersn'], '订单已被抢');
			if(is_error($result)) {
				return $result;
			}
		}
		if(get_order_data($order, 'shansong.status') == 1 && $extra['role'] !== 'shansong'){
			mload()->model('plugin');
			$_W['_plugin'] = array(
				'name' => 'shansong'
			);
			pload()->classs('shansong');
			$shanSong = new shanSong($order['id'], $order['sid']);
			$result = $shanSong->cancelOrder();
			if(is_error($result)) {
				return $result;
			}
		}
		//订单推送到点我达，又配送员接单时，取消点我达订单
		if(get_order_data($order, 'dianwoda.status') == 1 && $extra['role'] !== 'dianwoda'){
			mload()->model('plugin');
			$_W['_plugin'] = array(
				'name' => 'dianwoda'
			);
			pload()->classs('dianwoda');
			$dianwoda = new dianwoda($order['id'], $order['sid']);
			$result = $dianwoda->orderCancel();
			if(is_error($result)) {
				return $result;
			}
		}

		//订单配送信息同步到美团
		if($order['order_plateform'] == 'meituan') {
			$_W['_plugin'] = array(
				'name' => 'meituan'
			);
			mload()->model('plugin');
			pload()->classs('order');
			$openOrderId = $order['meituanOrderId'];
			$openOrder = new Order($order['sid']);
			$openOrder->updateOrderDeliverying($openOrderId, $deliveryer);
		}
		return error(0, '抢单成功');
	} elseif($type == 'delivery_instore') {
		if($order['status'] == 5) {
			return error(-1, '系统已完成， 不能抢单或分配订单');
		}
		if($order['status'] == 6) {
			return error(-1, '系统已取消， 不能抢单或分配订单');
		}
		if(!in_array($extra['role'], array('eleme', 'meituan','dada', 'uupaotui', 'shansong', 'dianwoda'))) {
			if(empty($extra['deliveryer_id'])) {
				return error(-1, '配送员不存在');
			}
			$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $extra['deliveryer_id']));
			if(empty($deliveryer)) {
				return error(-1, '配送员不存在');
			}
			if($deliveryer['status'] != 1) {
				return error(-1, '配送员已被删除');
			}
			if($order['deliveryer_id'] != $deliveryer['id']) {
				return error(-1, '该订单不是您配送，不能确认取货');
			}
		}
		$update = array(
			'delivery_status' => 8, //已到店，待取货
			'delivery_instore_time' => TIMESTAMP,
			'delivery_handle_type' => !empty($extra['delivery_handle_type']) ? $extra['delivery_handle_type'] : 'wechat'
		);
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		order_insert_status_log($order['id'], 'delivery_instore');
		order_status_notice($order['id'], 'delivery_instore');
		return error(0, '确认到店成功');
	} elseif($type == 'delivery_takegoods') {
		if($order['status'] == 5) {
			return error(-1, '系统已完成， 不能抢单或分配订单');
		}
		if($order['status'] == 6) {
			return error(-1, '系统已取消， 不能抢单或分配订单');
		}
		if(!in_array($extra['role'], array('eleme', 'meituan','dada', 'uupaotui', 'shansong', 'dianwoda'))) {
			if(empty($extra['deliveryer_id'])) {
				return error(-1, '配送员不存在');
			}
			$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $extra['deliveryer_id']));
			if(empty($deliveryer)) {
				return error(-1, '配送员不存在');
			}
			if($deliveryer['status'] != 1) {
				return error(-1, '配送员已被删除');
			}
			if($order['deliveryer_id'] != $deliveryer['id']) {
				return error(-1, '该订单不是您配送，不能确认取货');
			}
		}
		$update = array(
			'delivery_status' => 4, //已取货
			'delivery_takegoods_time' => TIMESTAMP,
			'delivery_handle_type' => !empty($extra['delivery_handle_type']) ? $extra['delivery_handle_type'] : 'wechat'
		);
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		order_insert_status_log($order['id'], 'delivery_takegoods');
		order_status_notice($order['id'], 'delivery_takegoods');
		return error(0, '确认取货成功');
	} elseif($type == 'delivery_success') {
		$result = order_status_update($order['id'], 'end', $extra);
		if(is_error($result)) {
			return $result;
		}
		return error(0, '确认送达成功');
	} elseif($type == 'delivery_transfer') {
		if($order['status'] == 5) {
			return error(-1, '系统已完成， 不能申请转单');
		}
		if($order['status'] == 6) {
			return error(-1, '系统已取消， 不能申请转单');
		}
		if($order['delivery_type'] != 2) {
			return error(-1, '该单属于店内配送单，不能申请转单');
		}
		if(empty($extra['reason'])) {
			return error(-1, '转单理由不能为空');
		}
		if(empty($extra['deliveryer_id'])) {
			return error(-1, '配送员不存在');
		}
		$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $extra['deliveryer_id']), array('id', 'perm_transfer', 'status'));
		if(empty($deliveryer)) {
			return error(-1, '配送员不存在');
		}
		if($deliveryer['status'] != 1) {
			return error(-1, '配送员已被删除');
		}
		if($order['deliveryer_id'] != $deliveryer['id']) {
			return error(-1, '该订单不是您配送，不能申请转单');
		}
		$deliveryer['perm_transfer'] = iunserializer($deliveryer['perm_transfer']);
		if(!$deliveryer['perm_transfer']['status_takeout']) {
			return error(-1, '您没有转单权限，请联系平台管理员');
		}
		$transfer_num = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_deliveryer_transfer_log') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and order_type = :order_type and stat_day = :stat_day', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $extra['deliveryer_id'], ':order_type' => 'takeout', ':stat_day' => date('Ymd')));
		if($deliveryer['perm_transfer']['max_takeout'] > 0 && $transfer_num >= $deliveryer['perm_transfer']['max_takeout']) {
			return error(-1, "每天最多可以转单{$deliveryer['perm_transfer']['max_takeout']}次,您已超过限定次数");
		}
		$transfer_log = array(
			'uniacid' => $_W['uniacid'],
			'deliveryer_id' => $extra['deliveryer_id'],
			'order_type' => 'takeout',
			'order_id' => $order['id'],
			'reason' => $extra['reason'],
			'addtime' => TIMESTAMP,
			'stat_year' => date('Y'),
			'stat_month' => date('Ym'),
			'stat_day' => date('Ymd'),
		);
		pdo_insert('tiny_wmall_deliveryer_transfer_log', $transfer_log);
		$update = array(
			'status' => 3,
			'delivery_status' => 3,
			'delivery_handle_type' => 'wechat',
			'deliveryer_id' => 0,
		);
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		order_insert_status_log($order['id'], 'delivery_transfer', "转单理由:{$extra['reason']},等待其他配送员接单");
		order_status_update($order['id'], 'notify_deliveryer_collect', array('channel' => 'delivery_transfer'));
		deliveryer_order_num_update($extra['deliveryer_id']);
		return error(0, '转单成功');
	} elseif($type == 'delivery_cancel') {
		//需要权限判断
		if(empty($extra['note'])) {
			return error(-1, '订单取消原因不能为空');
		}
		if(empty($extra['deliveryer_id'])) {
			return error(-1, '配送员不存在');
		}
		$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $extra['deliveryer_id']), array('id', 'perm_cancel', 'status'));
		if(empty($deliveryer)) {
			return error(-1, '配送员不存在');
		}
		if($deliveryer['status'] != 1) {
			return error(-1, '配送员已被删除');
		}
		$deliveryer['perm_cancel'] = iunserializer($deliveryer['perm_cancel']);
		if(!$deliveryer['perm_cancel']['status_takeout']) {
			return error(-1, '您没有取消订单的权限');
		}
		if($order['deliveryer_id'] != $deliveryer['id']) {
			return error(-1, '该订单不是您配送，不能取消');
		}
		$result = order_status_update($order['id'], 'cancel', $extra);
		if(is_error($result)) {
			return $result;
		}
		return error(0, '取消订单成功');
	} elseif($type == 'direct_transfer') {
		//需要权限判断
		/*		if(empty($extra['note'])) {
					return error(-1, '转单原因不能为空');
				}*/
		if(empty($extra['from_deliveryer_id'])) {
			return error(-1, '配送员不存在');
		}
		if(empty($extra['to_deliveryer_id'])) {
			return error(-1, '转单目标配送员不存在');
		}
		$to_deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $extra['to_deliveryer_id']), array('id', 'title', 'status'));
		if(empty($to_deliveryer)) {
			return error(-1, '转单目标配送员不存在');
		}
		if($to_deliveryer['status'] != 1) {
			return error(-1, '转单目标配送员已被删除');
		}
		$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $extra['from_deliveryer_id']), array('id', 'title', 'perm_transfer', 'status'));
		if(empty($deliveryer)) {
			return error(-1, '配送员不存在');
		}
		if($deliveryer['status'] != 1) {
			return error(-1, '配送员已被删除');
		}
		$deliveryer['perm_transfer'] = iunserializer($deliveryer['perm_transfer']);
		if(!$deliveryer['perm_transfer']['status_takeout']) {
			return error(-1, '您没有转单权限');
		}
		if($order['deliveryer_id'] != $deliveryer['id']) {
			return error(-1, '该订单不是您配送，不能转单');
		}
		$transfer_num = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_deliveryer_transfer_log') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and order_type = :order_type and stat_day = :stat_day', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $extra['deliveryer_id'], ':order_type' => 'takeout', ':stat_day' => date('Ymd')));
		if($deliveryer['perm_transfer']['max_takeout'] > 0 && $transfer_num >= $deliveryer['perm_transfer']['max_takeout']) {
			return error(-1, "每天最多可以转单{$deliveryer['perm_transfer']['max_takeout']}次,您已超过限定次数");
		}
		$order['data']['transfer_delivery_reason'] = $extra['note'];
		$order['data']['original_delivery_collect_type'] = $order['delivery_collect_type'];
		$update = array(
			'delivery_collect_type' => 3,
			'transfer_deliveryer_id' => $extra['to_deliveryer_id'],
			'transfer_delivery_status' => 1,
			'data' => iserializer($order['data']),
		);
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		order_insert_status_log($order['id'], 'direct_transfer', "目标配送员:{$to_deliveryer['title']},转单理由:{$extra['reason']},等待其他配送员回复");
		//通知目标配送员
		$extra['from_deliveryer'] = $deliveryer;
		order_deliveryer_notice($order['id'], 'direct_transfer', $extra['to_deliveryer_id'], $extra);
		return error(0, '发起定向转单申请成功，请等待目标配送员回复');
	} elseif($type == 'direct_transfer_reply') {
		if(empty($extra['deliveryer_id'])) {
			return error(-1, '目标配送员不存在');
		}
		$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $extra['deliveryer_id']), array('id', 'title', 'mobile', 'status'));
		if(empty($deliveryer)) {
			return error(-1, '配送员不存在');
		}
		if($deliveryer['status'] != 1) {
			return error(-1, '配送员已被删除');
		}
		if($order['transfer_deliveryer_id'] != $deliveryer['id']) {
			return error(-1, '您没有转单回复的权限');
		}
		$from_deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $order['deliveryer_id']), array('id', 'title', 'status'));
		if(empty($from_deliveryer)) {
			return error(-1, '转单配送员不存在');
		}
		if($from_deliveryer['status'] != 1) {
			return error(-1, '转单配送员已被删除');
		}
		//定向转单目前不受最多可接多少单限制
		if($extra['result'] == 'agree') {
			$update = array(
				'delivery_collect_type' => 3,
				'deliveryer_id' => $extra['deliveryer_id'],
				'transfer_deliveryer_id' => $order['deliveryer_id'],
				'transfer_delivery_status' => 0,
			);
			pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
			order_insert_status_log($order['id'], 'direct_transfer_agree', "{$deliveryer['title']}接受来自{$from_deliveryer['title']}的转单,此订单由{$deliveryer['title']}为您配送，手机号：<a href='tel:{$deliveryer['mobile']}'>{$deliveryer['mobile']}</a>");
			deliveryer_order_num_update($deliveryer['id']);
			deliveryer_order_num_update($order['deliveryer_id']);
			return error(0, '接受转单成功');
		} else {
			$update = array(
				'delivery_collect_type' => $order['data']['original_delivery_collect_type'],
				'transfer_deliveryer_id' => 0,
				'transfer_delivery_status' => 0,
			);
			pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
			order_insert_status_log($order['id'], 'direct_transfer_agree', "{$deliveryer['title']}拒绝来自{$from_deliveryer['title']}的转单");
			//通知目标配送员
			$extra = array(
				'from_deliveryer' => $from_deliveryer,
				'to_deliveryer' => $deliveryer
			);
			order_deliveryer_notice($order['id'], 'direct_transfer_refuse', $order['deliveryer_id'], $extra);
			return error(0, '已拒绝转单');
		}
	}
}

function order_manager_notice($order_id, $type, $note = '') {
	global $_W;
	$maneger = $_W['we7_wmall']['config']['manager'];
	if(empty($maneger)) {
		return error(-1, '管理员信息不完善');
	}
	$order = order_fetch($order_id);
	if(empty($order)) {
		return error(-1, '订单不存在或已经删除');
	}
	$store = store_fetch($order['sid'], array('id', 'title'));
	$acc = WeAccount::create($order['acid']);
	if($type == 'new_delivery') {
		$title = '平台有新的外卖订单，请尽快登录后台调度处理';
		$remark = array(
			"门店名称: {$store['title']}",
			"订单类型: {$order['order_type_cn']}",
			"支付方式: {$order['pay_type_cn']}",
			"支付时间: " . date('Y-m-d H:i', $order['paytime']),
		);
	} elseif($type == 'dispatch_error') {
		$title = '平台有新的外卖订单，系统自动调度失败，请登录后台人工调度';
		$remark = array(
			"门店名称: {$store['title']}",
			"订单类型: {$order['order_type_cn']}",
			"支付方式: {$order['pay_type_cn']}",
			"支付时间: " . date('Y-m-d H:i', $order['paytime']),
		);
	} elseif($type == 'no_working_deliveryer') {
		$title = '平台有新的待配送外卖订单,但没有接单中的配送员,请尽快协调';
		$remark = array(
			"订单类型: 外卖订单",
		);
	}
	if(!empty($note)) {
		if(!is_array($note)) {
			$remark[] = $note;
		} else {
			$remark[] = implode("\n", $note);;
		}
	}
	if(!empty($end_remark)) {
		$remark[] = $end_remark;
	}
	$remark = implode("\n", $remark);
	$send = tpl_format($title, $order['ordersn'], $order['status_cn'], $remark);
	$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall']['config']['notice']['wechat']['public_tpl'], $send);
	if(is_error($status)) {
		slog('wxtplNotice', '平台新订单微信通知平台管理员', $send, $status['message']);
	}
	return $status;
}

function order_calculate_deliveryer_fee($order, $deliveryerOrid = 0) {
	global $_W;
	if($order['order_type'] != 1) {
		return 0;
	}
	if($order['delivery_type'] == 1) {
		return 0;
	}
	$deliveryer = $deliveryerOrid;
	if(!is_array($deliveryer) || !is_array($deliveryer['fee_delivery'])) {
		mload()->model('deliveryer');
		$deliveryer = deliveryer_fetch($deliveryerOrid);
	}
	if(empty($deliveryer)) {
		return 0;
	}
	$config_takeout = get_deliveryer_feerate($deliveryer, 'takeout');
	$plateform_deliveryer_fee = floatval($config_takeout['deliveryer_fee']);
	if($config_takeout['deliveryer_fee_type'] == 2) {
		if(is_open_order($order)) {
			$order['delivery_fee'] = $order['plateform_delivery_fee'];
		}
		$plateform_deliveryer_fee = round($order['delivery_fee'] * $config_takeout['deliveryer_fee'] / 100, 2);
	} elseif($config_takeout['deliveryer_fee_type'] == 3) {
		$config_deliveryer_fee = $config_takeout['deliveryer_fee'];
		$plateform_deliveryer_fee = floatval($config_deliveryer_fee['start_fee']);
		$over_km = $order['distance'] - $config_deliveryer_fee['start_km'];
		if($over_km > 0) {
			$over_fee = round($over_km * $config_deliveryer_fee['pre_km'], 2);
		}
		$plateform_deliveryer_fee += $over_fee;
		if($config_deliveryer_fee['max_fee'] > 0) {
			$plateform_deliveryer_fee = min($plateform_deliveryer_fee, $config_deliveryer_fee['max_fee']);
		}
	} elseif($config_takeout['deliveryer_fee_type'] == 4) {
		$plateform_deliveryer_fee = round($order['final_fee'] * $config_takeout['deliveryer_fee'] / 100, 2);
	}
	$config_store = store_fetch($order['sid'], array('delivery_extra'));
	$plateform_bear_deliveryprice = $config_store['delivery_extra']['plateform_bear_deliveryprice'];
	$plateform_deliveryer_fee += $plateform_bear_deliveryprice;
	return $plateform_deliveryer_fee;
}

function order_update_bill($order_id, $extra_data = array()) {
	global $_W;
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $order_id));
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	if(in_array($order['order_plateform'], array('eleme', 'meituan'))) {
		return true;
	}
	$order['price_origin'] = $order['price'];
	$account = store_account($order['sid']);

	$plateform_delivery_fee = 0;
	if($order['order_type'] == 1) {
		$fee_type = 'fee_takeout';
		if($order['delivery_type'] == 2) {
			//平台配送模式
			$plateform_delivery_fee = $order['delivery_fee'];
		}
	} elseif($order['order_type'] == 2) {
		$fee_type = 'fee_selfDelivery';
	} else {
		$fee_type = 'fee_instore';
	}

	$fee_config = $account[$fee_type];
	if($fee_config['type'] == 2) {
		$plateform_serve_rate = 0;
		$platform_serve_fee = floatval($fee_config['fee']);
		$plateform_serve = array(
			'fee_type' => 2,
			'fee_rate' => 0,
			'fee' => $platform_serve_fee,
			'note' => "每单固定{$platform_serve_fee}元"
		);
	} else {
		//只有订单按百分比提成才执行特殊商品佣金计算
		$special_goods_plateform_fees = 0;
		$special_goods_price_total = 0;
		$special_goods_note = '';
		if(!empty($account['fee_goods'])) {
			$order_data = iunserializer($order['data']);
			$cart_goods = $order_data['cart'];
			$cart_goods_ids = array_keys($cart_goods);
			$special_config_goodsids = array_keys($account['fee_goods']);
			$special_goods_ids = array_intersect($cart_goods_ids, $special_config_goodsids);
			if(!empty($special_goods_ids)) {
				foreach($special_goods_ids as $goodsid) {
					$special_goods_price = 0;
					$special_goods_num = 0;
					foreach($cart_goods[$goodsid]['options'] as $option) {
						//兼容 小程序total_price，公众号price_total
						$price_total = empty($option['price_total']) ? $option['total_price'] : $option['price_total'];
						$special_goods_price += $price_total;
						$special_goods_num += $option['num'];
					}
					$type = $account['fee_goods'][$goodsid]['type'];
					if($type == 1) {
						$special_goods_plateform_fee_item = round($special_goods_price * $account['fee_goods'][$goodsid]['fee_rate']/100, 2);
					} else {
						$special_goods_plateform_fee_item = round($account['fee_goods'][$goodsid]['fee'] * $special_goods_num, 2);
					}
					$special_goods_plateform_fees += $special_goods_plateform_fee_item;
					$special_goods_note .= " + {$cart_goods[$goodsid]['title']}佣金 ￥{$special_goods_plateform_fee_item}";
					$special_goods_price_total += $special_goods_price;
				}
			}
		}
		$basic = 0;
		$note = array(
			'yes' => array(),
			'no' => array(),
		);
		$fee_items = store_serve_fee_items();
		if(!empty($fee_config['items_yes'])) {
			$fee_config['items_yes'] = array_unique($fee_config['items_yes']);
			foreach($fee_config['items_yes'] as $item) {
				if($item == 'delivery_fee' && $order['delivery_type'] == 2) {
					continue;
				}
				if($item == 'price') {
					$order[$item] -= $special_goods_price_total;
				}
				$basic += $order[$item];
				$note['yes'][] = "{$fee_items['yes'][$item]} ￥{$order[$item]}";
			}
		}
		if(!empty($fee_config['items_no'])) {
			$fee_config['items_no'] = array_unique($fee_config['items_no']);
			foreach($fee_config['items_no'] as $item) {
				$basic -= $order[$item];
				$note['no'][] = "{$fee_items['no'][$item]} ￥{$order[$item]}";
			}
		}
		if($basic < 0) {
			$basic = 0;
		}
		$plateform_serve_rate = $fee_config['fee_rate'];
		$platform_serve_fee = round($basic * ($plateform_serve_rate / 100), 2);
		$text = '(' . implode(' + ', $note['yes']);
		if(!empty($note['no'])) {
			$text .= ' - ' . implode(' - ', $note['no']);
		}
		$text .= ") x {$plateform_serve_rate}%";
		if($fee_config['fee_min'] > 0 && $platform_serve_fee < $fee_config['fee_min']) {
			$platform_serve_fee = $fee_config['fee_min'];
			$text .= ' 佣金小于平台设置最少抽佣金额，以最少抽佣金额计';
		}
		$plateform_serve = array(
			'fee_type' => 1,
			'fee_rate' => $plateform_serve_rate,
			'fee' => $platform_serve_fee,
			'note' => $text
		);
		$plateform_serve['fee'] += $special_goods_plateform_fees;
		$platform_serve_fee += $special_goods_plateform_fees;
		$plateform_serve['note'] .= $special_goods_note;
	}
	$store_order_total_fee = $order['price_origin'] + $order['box_price'] + $order['pack_fee'] + $order['serve_fee'];
	if($order['order_type'] == 1) {
		if($order['delivery_type'] == 1) {
			//店内配送模式
			$store_order_total_fee += $order['delivery_fee'];
		} else {
			//平台配送模式下,如果商家设置承担额外配送费,则要从商家的利润中扣除额外配送费
			$store = pdo_get('tiny_wmall_store', array('id' => $order['sid']), array('delivery_extra'));
			$extra = unserialize($store['delivery_extra']);
			$store_bear_deliveryprice = floatval($extra['store_bear_deliveryprice']);
			if(!empty($store_bear_deliveryprice)) {
				$platform_serve_fee += $store_bear_deliveryprice;
				$plateform_serve['fee'] += $store_bear_deliveryprice;
				$plateform_serve['note'] .= " + 商家额外承担配送费 ￥{$store_bear_deliveryprice}";
			}
		}
	}
	$cashGrant = $extra_data['activity']['list']['cashGrant'];
	if(!empty($cashGrant)) {
		$order['discount_fee'] += $cashGrant['value'];
	} else {//配送员接单后计算没有activity信息返现优惠计算，也会给商家返现
		$cashGrant = pdo_get('tiny_wmall_order_discount', array('uniacid' => $_W['uniacid'], 'oid' => $order_id, 'type' => 'cashGrant'), array('fee'));
		if(!empty($cashGrant)) {
			$order['discount_fee'] += $cashGrant['fee'];
		}
	}

	$store_final_fee = $store_order_total_fee - $order['discount_fee'] - $platform_serve_fee + $order['plateform_discount_fee'] + $order['agent_discount_fee'];
	//$store_final_fee = $store_final_fee < 0 ? 0 : $store_final_fee;
	if($order['refund_fee'] > 0 && $order['refund_status'] > 0) {//配送员接单后重新计算商家入账
		$store_final_fee = $store_final_fee - $order['refund_fee'];
	}
	if($order['agentid'] > 0) {
		$account_agent = get_agent($order['agentid'], 'fee');
		$agent_fee_config = $account_agent['fee'][$fee_type];
		if($agent_fee_config['type'] == 2) {
			$agent_serve_fee = floatval($agent_fee_config['fee']);
			$agent_serve = array(
				'fee_type' => 2,
				'fee_rate' => 0,
				'fee' => $agent_serve_fee,
				'note' => "每单固定{$agent_serve_fee}元"
			);
		} elseif($agent_fee_config['type'] == 3) {
			$agent_serve_rate = floatval($agent_fee_config['fee_rate']);
			$agent_serve_fee = round($platform_serve_fee * $agent_serve_rate / 100, 2);
			$text = "本单代理佣金￥{$platform_serve_fee} x {$agent_serve_rate}%";
			if($agent_fee_config['fee_min'] > 0 && $agent_serve_fee < $agent_fee_config['fee_min']) {
				$agent_serve_fee = $agent_fee_config['fee_min'];
				$text .= '， 佣金小于代理设置最少抽佣金额，以最少抽佣金额计';
			}
			$agent_serve = array(
				'fee_type' => 3,
				'fee_rate' => $agent_serve_rate,
				'fee' => $agent_serve_fee,
				'note' => $text
			);
		} else {
			$basic = 0;
			$note = array(
				'yes' => array(),
				'no' => array(),
			);
			$fee_items = agent_serve_fee_items();
			if(!empty($agent_fee_config['items_yes'])) {
				foreach($agent_fee_config['items_yes'] as $item) {
					if($item == 'delivery_fee' && $order['delivery_type'] == 2) {
						continue;
					}
					$basic += $order[$item];
					$note['yes'][] = "{$fee_items['yes'][$item]} ￥{$order[$item]}";
				}
			}
			if(!empty($agent_fee_config['items_no'])) {
				foreach($agent_fee_config['items_no'] as $item) {
					$basic -= $order[$item];
					$note['no'][] = "{$fee_items['no'][$item]} ￥{$order[$item]}";
				}
			}
			if($basic < 0) {
				$basic = 0;
			}
			$agent_serve_rate = floatval($agent_fee_config['fee_rate']);
			$agent_serve_fee = round($basic * ($agent_serve_rate / 100), 2);
			$text = '(' . implode(' + ', $note['yes']);
			if(!empty($note['no'])) {
				$text .= ' - ' . implode(' - ', $note['no']);
			}
			$text .= ") x {$agent_serve_rate}%";
			if($agent_fee_config['fee_min'] > 0 && $agent_serve_fee < $agent_fee_config['fee_min']) {
				$agent_serve_fee = $agent_fee_config['fee_min'];
				$text .= ' 佣金小于代理设置最少抽佣金额，以最少抽佣金额计';
			}
			$agent_serve = array(
				'fee_type' => 1,
				'fee_rate' => $agent_serve_rate,
				'fee' => $agent_serve_fee,
				'note' => $text,
			);
		}
	}
	$deliveryer_id = $extra_data['deliveryer_id'];
	$plateform_deliveryer_fee = order_calculate_deliveryer_fee($order, $deliveryer_id);
	$agent_final_fee = $platform_serve_fee - $agent_serve_fee - $order['agent_discount_fee'];
	$zhunshibao_cn = '';
	if($order['zhunshibao_status'] > 0) {
		$agent_final_fee = $agent_final_fee + floatval($order['zhunshibao_price']);
		$zhunshibao_cn = " + 准时宝收入 ￥{$order['zhunshibao_price']}";
	}
	if($order['delivery_type'] == 2) {
		//平台配送模式下,平台需要给配送员支付配送费，所以从代理的利润里扣除
		$agent_final_fee = $agent_final_fee + $plateform_delivery_fee - $plateform_deliveryer_fee;
	}

	//附加费代理入账
	$extra_fee = floatval($order['extra_fee']);
	$agent_final_fee += $extra_fee;
	$extra_fee_cn = " + 附加费收入 ￥{$extra_fee}";

	$agent_serve['final'] = "(代理商抽取佣金 ￥{$platform_serve_fee} - 平台服务佣金 ￥{$agent_serve_fee} - 代理商补贴 ￥{$order['agent_discount_fee']} + 代理商配送费 ￥{$plateform_delivery_fee} - 代理商支付给配送员配送费 ￥{$plateform_deliveryer_fee}{$extra_fee_cn}{$zhunshibao_cn})";
	$data = array(
		'agent_final_fee' => $agent_final_fee,
		'agent_serve' => iserializer($agent_serve),
		'agent_serve_fee' => $agent_serve_fee,
	);
	$data['plateform_delivery_fee'] = $plateform_delivery_fee;
	$data['plateform_deliveryer_fee'] = $plateform_deliveryer_fee;
	$data['plateform_serve_rate'] = $plateform_serve_rate;
	$data['plateform_serve_fee'] = $platform_serve_fee;
	$data['plateform_serve'] = iserializer($plateform_serve);
	$data['store_final_fee'] = $store_final_fee;
	$data['stat_year'] = date('Y', $order['addtime']);
	$data['stat_month'] = date('Ym', $order['addtime']);
	$data['stat_day'] = date('Ymd', $order['addtime']);
	$data['stat_week'] = date('w', $order['addtime']);
	$data['meals_cn'] = get_meals_type($order['addtime']);
	pdo_update('tiny_wmall_order', $data, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
	return true;
}

function order_mall_remind() {
	global $_W;
	if(empty($_W['member']['uid'])) {
		return array();
	}
	$order = array();
	if($_W['we7_wmall']['config']['close']['status'] != 2 && $_W['member']['uid'] > 0) {
		$order = pdo_fetch('select id,status,addtime,paytime from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and order_type = 1 and is_pay = 1 and status < 5 order by id desc', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
		if(!empty($order)) {
			$log = pdo_fetch('select * from ' . tablename('tiny_wmall_order_status_log') . ' where oid = :id order by id desc', array(':id' => $order['id']));
			$order['log'] = $log;
			$order['logo'] = tomedia($_W['we7_wmall']['config']['mall']['logo']);
		}
	}
	return $order;
}

function order_coupon_grant($id) {
	global $_W;
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id), array('id', 'sid', 'final_fee', 'uid'));
	if(empty($order)) {
		return error(-1, '订单不存在');
	}
	mload()->model('coupon');
	$coupon = coupon_grant_available($order['sid'], $order['final_fee']);
	if(empty($coupon) || !is_array($coupon)) {
		return error(-1, '门店没有设置满赠券活动');
	}
	$params = $coupon['coupons'];
	$params['coupon_id'] = $coupon['id'];
	$params['sid'] = $order['sid'];
	$params['channel'] = 'couponGrant';
	$params['type'] = 'couponGrant';
	$params['uid'] = $order['uid'];
	//$params['order_id'] = $order['id'];
	$result = coupon_grant($params);
	return $result;
}

function order_time_analyse($id) {
	global $_W;
	$order = order_fetch($id);
	$time_interval = array(
		'store_consum_time' => transform_time($order['handletime'] - $order['paytime']),
		'deliveryer_consum_time' => transform_time($order['endtime'] - $order['delivery_assign_time']),
		'order_consum_time' => transform_time($order['endtime'] - $order['paytime']),
	);
	$timeout_limit = $_W['we7_wmall']['config']['takeout']['order']['timeout_limit'];
	if($timeout_limit > 0 && $order['status'] < 6) {
		$time_interval['timeout_text'] = '';
		$endtime = TIMESTAMP;
		if($order['status'] == 5) {
			$endtime = $order['endtime'];
		}
		$time_difference = $endtime - $order['paytime'] - $timeout_limit * 60;
		if($time_difference > 0) {
			$time_interval['is_timeout'] = 1;
			$time_difference = transform_time($time_difference);
			$time_interval['timeout_text'] = "已超时{$time_difference}";
			$time_interval['timeout_css'] = 'color-danger';
		} else {
			$time_interval['is_timeout'] = 0;
			$time_difference = -$time_difference;
			$time_difference = transform_time($time_difference);
			$time_interval['timeout_text'] = "距超时{$time_difference}";
			$time_interval['timeout_css'] = 'color-default';
		}
	}
	return $time_interval;
}

function goods_total_warning_notice($goodsOrid, $optionOrid, $extra = array()) {
	global $_W;
	$goods = $goodsOrid;
	if(!is_array($goodsOrid)) {
		$goodsOrid = intval($goodsOrid);
		$goods = pdo_get('tiny_wmall_goods', array(':uniacid' => $_W['uniacid'], 'id' => $goodsOrid));
	}
	if(empty($goods)) {
		return error(-1, '商品不存在');
	}
	if(!empty($optionOrid)) {
		$option = $optionOrid;
		if(!is_array($optionOrid)) {
			$optionOrid = intval($optionOrid);
			$option = pdo_get('tiny_wmall_goods_option', array(':uniacid' => $_W['uniacid'], 'id' => $optionOrid));
		}
		if(empty($option)) {
			return error(-1, '商品规格不存在');
		}
	}
	$tips = "商品{$goods['title']}";
	if(!empty($option)) {
		$tips .= "(规格:{$option['name']}),当前库存{$extra['total_now']}{$goods['uniname']},达到预警值,快去备货吧！";
	}
	$params = array(
		'first' => $tips,
		'keyword1' => "商品{$goods['title']}库存不足",
		'keyword2' => "{$_W['we7_wmall']['config']['mall']['title']}",
		'keyword3' => date('Y-m-d H:i:s'),
		'keyword4' => "商品{$goods['title']}库存不足",
	);
	$send = sys_wechat_tpl_format($params);
	mload()->model('clerk');
	$clerks = clerk_fetchall($goods['sid']);
	$acc = WeAccount::create($_W['acid']);
	foreach($clerks as $clerk) {
		if($clerk['extra']['accept_wechat_notice']) {
			$status = $acc->sendTplNotice($clerk['openid'], $_W['we7_wmall']['config']['notice']['wechat']['warning_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', "库存预警通知店员：{$clerk['title']}", $send, $status['message']);
			}
		} else {
			slog('wxtplNotice', "库存预警通知店员:{$clerk['title']}", $send, '店员设置了不接受微信模板消息');
		}
	}
	return true;
}

function order_delivery_info($store, $predict_index = -1, $condition = array()) {
	global $_W;
	$address = $condition['address'];
	$delivery_price = 0;
	$price = array(
		'send_price' => $store['send_price'],
		'delivery_free_price' => $store['delivery_free_price'],
	);
	if($store['address_type'] == 1) {
		$delivery_price = $delivery_price_basic = $store['delivery_areas1'][$address['area_id']]['price'];
	} else {
		if($store['delivery_fee_mode'] == 1) {
			$delivery_price = $delivery_price_basic = $store['delivery_price'];
		} elseif($store['delivery_fee_mode'] == 2) {
			$delivery_price = $delivery_price_basic = $store['delivery_price_extra']['start_fee'];
			if(!empty($address) && !is_error($address)) {
				$distance = $address['distance'];
				if($distance > 0) {
					if($distance > $store['delivery_price_extra']['over_km'] && $distance > $store['delivery_price_extra']['start_km'] && $store['delivery_price_extra']['over_pre_km_fee'] > 0) {
						$delivery_price += ($distance - $store['delivery_price_extra']['start_km']) * $store['delivery_price_extra']['over_pre_km_fee'];
						if($_W['member']['uid'] == 175917884) {
							flog('c', $delivery_price, 'ppp');
						}
					} elseif($distance > $store['delivery_price_extra']['start_km'] && $store['delivery_price_extra']['pre_km_fee'] > 0) {
						$delivery_price += ($distance - $store['delivery_price_extra']['start_km']) * $store['delivery_price_extra']['pre_km_fee'];
						if($_W['member']['uid'] == 175917884) {
							flog('d', $delivery_price, 'ppp');
						}
						}
					$delivery_price = $delivery_price_basic = round($delivery_price, 2);
					if($store['delivery_price_extra']['max_fee'] > 0 && $delivery_price > $store['delivery_price_extra']['max_fee']) {
						$delivery_price = $delivery_price_basic = $store['delivery_price_extra']['max_fee'];
						if($_W['member']['uid'] == 175917884) {
							flog('e', $delivery_price, 'ppp');
						}
					}
				}
			}
			if($_W['member']['uid'] == 175917884) {
				flog('a', $delivery_price, 'ppp');
				flog('b', $distance, 'ppp');
			}
		} elseif($store['delivery_fee_mode'] == 3) {
			if(!empty($address) && !is_error($address)) {
				$area_index = 0;
				foreach($store['delivery_areas'] as $key => $row) {
					$is_ok = isPointInPolygon($row['path'], array($address['location_y'], $address['location_x']));
					if($is_ok) {
						$area_index = $key;
						break;
					}
				}
				if(!empty($area_index)) {
					$area = $store['delivery_areas'][$area_index];
					$delivery_price = $delivery_price_basic = round($area['delivery_price'], 2);
					$price['send_price'] = $area['send_price'];
					$price['delivery_free_price'] = $area['delivery_free_price'];
				}
			}
		}
	}

	$delivery_time = store_delivery_times($store['id']);
	foreach($delivery_time['times'] as &$time) {
		$time['time_cn'] = "{$time['start']}~{$time['end']}";
		$time['total_delivery_price'] = round($delivery_price_basic + $time['fee'], 2);
		$time['total_delivery_price_cn'] = "{$time['total_delivery_price']}元配送费";
	}
	$delivery_times = array();
	//非可售时间下单，当前时间设置为门店营业时间；
	$predict_timestamp = TIMESTAMP + 60 * $store['delivery_time'];
	if($store['is_in_business_hours'] && !store_is_in_business_hours($store['business_hours'])) {
		$is_rest_order = 1;
		foreach($store['business_hours'] as $hours) {
			$starthour = strtotime($hours['s']);
			if($starthour > TIMESTAMP) {
				$predict_timestamp = $starthour + 60 * $store['delivery_time'];
				break;
			}
		}
	}
	$data = array_order($predict_timestamp, $delivery_time['timestamp']);
	$sys_predict_index = array_search($data, $delivery_time['timestamp']);
	foreach($delivery_time['days'] as &$days) {
		$delivery_times['days'][] = $days;
		$delivery_times['times'][$days] = array(
			'days' => $days,
			'times' => $delivery_time['times']
		);
		if($days == date('m-d') || $days == $delivery_time['nextday']) {
			if(empty($sys_predict_index)) {
				unset($delivery_times['times'][$days], $delivery_times['days'][0], $delivery_time['days'][0]);
				continue;
			}
			foreach($delivery_times['times'][$days]['times'] as $key1 => $time1) {
				if($key1 < $sys_predict_index) {
					unset($delivery_times['times'][$days]['times'][$key1]);
				}
			}
		}
	}
	$predict_day = $condition['predict_day_cn'];
	if(!in_array($predict_day, $delivery_times['days'])) {
		$predict_index = -1;
	}
	if($sys_predict_index !== false) {
		if(!$delivery_time['reserve']) {
			if(empty($is_rest_order)) {
				$delivery_times['times'][$delivery_time['days'][0]]['times'][$sys_predict_index]['time_cn'] = '立即送出';
			}
		}
		if($sys_predict_index > $predict_index && $predict_day == date('m-d')) {
			//判断$predict_day，比如：今天10号10点，选择11号9点下单$predict_index = -1;$sys_predict_index !== false；下单时间会变为10号立即送出
			//顾客选择的配送时间已经过去了。比如：顾客选择12-12:30，当前时间为14点
			$predict_index = -1;
		}
	} else {
		if($predict_day == date('m-d')) {
			//当天没有可配送时间，需要调到下一天配送
			$predict_index = -1;
		}
	}
	if($predict_index == -1) {
		if(!$delivery_time['reserve']) {
			//可提前几天下单
			if($sys_predict_index !== false) {
				$predict_day = $delivery_time['days'][0];
				$predict_index = $sys_predict_index;
				if(empty($is_rest_order)) {
					$predict_time = "立即送出";
				} else {
					$predict_time = $delivery_times['times'][$predict_day]['times'][$sys_predict_index]['time_cn'];
				}
			} else {
				//如果当天没有配送时间段，默认为下一天，如果仅限当天，商家应该设置营业时间与配送时间段匹配
				if(!empty($delivery_times['days'][1])) {
					$predict_day = $delivery_time['days'][1];
					$predict_index = key($delivery_time['times']);
					$predict_time_item = array_shift($delivery_time['times']);
					$predict_time = $predict_time_item['time_cn'];
				} else {
					$predict_day = date('m-d');
				}
			}
		} else {
			//需提前几天下单
			$predict_index = key($delivery_time['times']);
			$predict_day = $delivery_time['days'][0];
			$predict_time_item = array_shift($delivery_time['times']);
			$predict_time = $predict_time_item['time_cn'];
		}
	} else {
		$predict_day = $condition['predict_day_cn'];
		$predict_time = $condition['predict_time_cn'];
	}
	$delivery_times['predict_index'] = $predict_index;
	$delivery_times['predict_day'] = $predict_day;
	$delivery_times['predict_day_cn'] = $predict_day ? $predict_day : date('m-d');
	$delivery_times['predict_time_cn'] = $predict_time ? $predict_time : '立即送出';
	$total_delivery_price = round($delivery_price_basic + $delivery_time['times'][$predict_index]['fee'], 2);
	if($store['delivery_type'] == 2 || $condition['order_type'] == 2) {
		$result = array(
			'total_delivery_price' => 0,
			'send_price' => 0,
			'delivery_free_price' => 0,
			'delivery_times' => $delivery_times
		);
		return $result;
	}
	$result = array(
		'total_delivery_price' => $total_delivery_price,
		'delivery_times' => $delivery_times,
		'send_price' => $price['send_price'],
		'delivery_free_price' => $price['delivery_free_price'],
	);
	return $result;
}

function order_calculate($sidOrStore, $cart, $condition = array()) {
	global $_W;
	//先计算配送费
	$sid = $sidOrStore;
	if(is_array($sidOrStore)) {
		$sid = $sidOrStore['id'];
		$store = $sidOrStore;
	}
	if(!isset($condition['predict_index'])) {
		$condition['predict_index'] = -1;
	}
	$delivery_info = order_delivery_info($sidOrStore, $condition['predict_index'], $condition);

	$coupon_id = intval($condition['coupon_id']);
	if($coupon_id > 0) {
		mload()->model('coupon');
		$coupon = coupon_available_check($sid, $coupon_id, $cart['price']);
	}

	$meal_redpackets = $condition['meal_redpackets'];
	if($meal_redpackets) {
		$meal_redpackets['redpacket_used_key'] = 0;
	}
	$meal_redpacket_key = stristr($condition['redpacket_id'], 'pre_mealredpacket_');
	if($meal_redpacket_key) {
		$meal_redpacket_key = substr($meal_redpacket_key, 18);
		$redpacket = redpacket_available_check($meal_redpackets['data'][$meal_redpacket_key], $cart['price'], explode('|', $store['cid']));
		if(!is_error($redpacket)) {
			$meal_redpackets['pre_mealredpacket_used_key'] = $meal_redpacket_key;
		}
	} elseif(stristr($condition['redpacket_id'], 'svip_')) {
		mload()->model('redPacket');
		$redpacket = redpacket_available_check($condition['svip_redpacket'], $cart['price'], explode('|', $store['cid']), array('scene' => 'waimai', 'sid' => $sid));
		$pre_svip_redpacket_id = substr($condition['redpacket_id'], 5);
	} else {
		$redpacket_id = intval($condition['redpacket_id']);
		if($redpacket_id > 0) {
			mload()->model('redPacket');
			$redpacket = redpacket_available_check($redpacket_id, $cart['price'], explode('|', $store['cid']), array('scene' => 'waimai', 'sid' => $sid, 'order_type' => $condition['order_type']));
		}
	}
	if($meal_redpackets) {
		$meal_redpackets['data'] = array_values($meal_redpackets['data']);
	}

	$activityed = order_count_activity($sid, $cart, $coupon_id, $redpacket, $delivery_info['total_delivery_price'], $delivery_info['delivery_free_price'], $condition['order_type'], array('meal_redpacket' => $meal_redpackets));
	if(empty($activityed['list']['token']) && !is_error($coupon)) {
		unset($coupon);
	}
	if(empty($activityed['list']['redPacket']) && !is_error($redpacket)) {
		unset($redpacket);
	}
	//平台附加费
	$extra_fee_note = array();
	$extra_fee = 0;
	if(!empty($store['data']['extra_fee'])) {
		foreach($store['data']['extra_fee'] as $item) {
			$item_fee = floatval($item['fee']);
			if($item['status'] == 1 && $item_fee > 0) {
				$extra_fee += $item_fee;
				$extra_fee_note[] = $item;
			}
		}
	}
	$buy_svip_fee = 0;
	if($cart['is_buysvip'] == 1) {
		if(check_plugin_perm('svip')) {
			$svip_meal = pdo_fetch('select * from ' . tablename('tiny_wmall_svip_meal') . ' where uniacid = :uniacid and status = 1 order by price asc', array(':uniacid' => $_W['uniacid']));
			if(!empty($svip_meal)) {
				$buy_svip_fee = $svip_meal['price'];
			}
		}
	}
	if(empty($condition['person_num']) && $cart['takepart_num'] > 0) {
		$condition['person_num'] = $cart['takepart_num'];
	}
	$order_type = $condition['order_type'];
	if(in_array($store['delivery_type'], array(1, 2))) {
		$order_type = $store['delivery_type'];
	}
	$order = array(
		'order_type' => $order_type,
		'pack_fee' => $store['pack_price'],
		'price' => $cart['price'],
		'box_price' => $cart['box_price'],
		'delivery_fee' => $delivery_info['total_delivery_price'],
		'extra_fee' => $extra_fee,
		'extra_fee_detail' => $extra_fee_note,
		'total_fee' => round($cart['price'] + $cart['box_price'] + $store['pack_price'] + $delivery_info['total_delivery_price'] + $extra_fee, 2),
		'discount_fee' => $activityed['total'],
		'activityed' => $activityed,
		'redpacket' => $redpacket,
		'coupon' => $coupon,
		'deliveryTimes' => $delivery_info['delivery_times'],
		'note' => trim($condition['note']),
		'person_num' => trim($condition['person_num']),
		'invoiceId' => intval($condition['invoiceId']),
		'meal_redpackets' => $meal_redpackets,
	);
	$order['final_fee'] = $order['total_fee'] - $activityed['total'];
	$order['buy_zhunshibao'] = 0;
	if(!empty($store['data']['zhunshibao']) && $store['data']['zhunshibao']['status'] == 1) {
		if($store['data']['zhunshibao']['price_type'] == 1) {
			$order['zhunshibao_price'] = $store['data']['zhunshibao']['price'];
		} elseif($store['data']['zhunshibao']['price_type'] == 2) {
			$order_final_fee = $order['final_fee'] + floatval($cart['total_huangou_price']);
			$order['zhunshibao_price'] = round($order_final_fee * $store['data']['zhunshibao']['price'] / 100, 2);
		}
		if($condition['buy_zhunshibao'] == 1) {
			$order['buy_zhunshibao'] = 1;
			$order['final_fee'] += $order['zhunshibao_price'];
		}
	}
	if($condition['buy_mealredpacket'] == 1) {
		$order['final_fee'] += $meal_redpackets['price'];
	}
	if($buy_svip_fee > 0) {
		$order['final_fee'] += $buy_svip_fee;
		$order['svip_meal'] = $svip_meal;
		$order['pre_svip_redpacket_id'] = $pre_svip_redpacket_id;
	}
	$order['final_fee_show'] = $order['final_fee'];
	$order['total_fee_show'] = $order['total_fee'];
	if($cart['total_huangou_price'] > 0) {
		$order['final_fee'] = $order['final_fee'] + $cart['total_huangou_price'];
		$order['total_fee'] = $order['total_fee'] + $cart['total_huangou_price'];
	}
	if($order['final_fee'] < 0) {
		$order['final_fee_show'] = $order['final_fee'] = 0;
	}
	return $order;
}

function get_deliveryer_feerate($deliveryer, $type = '') {
	$delivery_fee = iunserializer($deliveryer['fee_delivery']);
	if(!empty($delivery_fee[$type])) {
		return $delivery_fee[$type];
	}
	return array();
}

function order_push_dada($id) {
	global $_W;
	$_W['_plugin'] = array(
		'name' => 'dada'
	);
	mload()->model('plugin');
	pload()->classs('dada');
	$dada = new DaDa();
	$response = $dada->addOrder($id);
	if(is_error($response)) {
		return error(-1, "订单推送到达达失败:{$response['message']}");
	}
	$data = array(
		'status' => 1,
		'fee' => $response['fee'],
		'distance' => $response['distance']
	);
	set_order_data($id, 'data', $data);
	return error(0, '成功推送订单到达达');
}

function order_timeout_remind($idOrOrder){
	global $_W;
	if(is_array($idOrOrder)) {
		$order = $idOrOrder;
	} else {
		$order = order_fetch($idOrOrder);
	}
	if(!in_array($order['status'], array(3, 4))) {
		return false;
	}
	$config_order = $_W['we7_wmall']['config']['takeout']['order'];
	$config_delivery_remind = $config_order["delivery_status_{$order['delivery_status']}"];
	if(empty($config_delivery_remind)) {
		return false;
	}
	$remind = array(
		'text' => '',
		'endtime' => '',
		'text' => '',
	);
	if($order['delivery_status'] == 3) {
		$notifytime = $order['clerk_notify_collect_time'];
		$timeover = $order['clerk_notify_collect_time'] + $config_delivery_remind['timeout_limit'] * 60;
		if($timeover < TIMESTAMP) {
			$remind['text'] = '配送员接单已超时';
		} else {
			$remind['text'] = "距接单超时还剩";
			$remind['endtime'] = $timeover;
		}
	} elseif($order['delivery_status'] == 7) {
		$notifytime = $order['delivery_assign_time'];
		$timeover = $order['delivery_assign_time'] + $config_delivery_remind['timeout_limit'] * 60;
		if($timeover < TIMESTAMP) {
			$remind['text'] = '配送员到店已超时';
		} else {
			$remind['text'] = "距到店超时还剩";
			$remind['endtime'] = $timeover;
		}
	} elseif($order['delivery_status'] == 4) {
		$notifytime = $order['delivery_takegoods_time'];
		$timeover = $order['delivery_takegoods_time'] + $config_delivery_remind['timeout_limit'] * 60;
		if($timeover < TIMESTAMP) {
			$remind['text'] = '配送员送达已超时';
		} else {
			$remind['text'] = "距离订单送达还剩";
			$remind['endtime'] = $timeover;
		}
	}
	if($config_delivery_remind['timeout_remind']) {
		$array = array_sort($config_delivery_remind['timeout_remind'], 'minute');
		foreach($array as $val) {
			$second = ($val['minute'] * 60) + $notifytime;
			if($second <= TIMESTAMP){
				$remind['color'] = $val['color'];
			}
		}
	}
	if(empty($remind['color'])) {
		return false;
	}
	return $remind;
}

function get_meals_type($time) {
	$time = date('Hi', $time);
	if($time >= 0600 && $time <= 1030) {
		return 'breakfast';
	}
	if($time > 1030 && $time <= 1430) {
		return 'lunch';
	}
	if($time > 1430 && $time <= 1700) {
		return 'tea';
	}
	if($time > 1700 && $time <= 2030) {
		return 'dinner';
	}
	if($time > 2030 && $time <= 2359) {
		return 'supper';
	}
}

function order_plateform_notice($idOrorder, $type, $extra = array()) {
	global $_W;
	$order = $idOrorder;
	if(!is_array($idOrorder)) {
		$order = order_fetch($idOrorder);
	}
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$_W['agentid'] = $order['agentid'];
	$store = store_fetch($order['sid'], array('title', 'id', 'push_token'));
	if($type == 'ordernew') {
		$pextra = array(
			'title' => '平台有新的外卖订单',
			'voice_text' => "店铺{$store['title']}有新的外卖订单,收货地址为{$order['address']},订单金额{$order['final_fee']}元",
			'notify_type' => 'ordernew',
			'url' => ipurl('pages/order/takeout', array('status' => $order['status']), true),
			'order_id' => $order['id'],
			'perm' => 'order.takeout',
		);
	}
	mload()->model('jpush');
	$data = Jpush_platefrom_send($pextra);
	return $data;
}

function order_is_reserve($order) {
	if(empty($order['deliverytime'])) {
		return array(
			'is_reserve' => 0,
		);
	}
	$config_store_reserve = store_get_data($order['sid'], 'reserve');
	$reserve_time_limit = empty($config_store_reserve['reserve_time_limit']) ? 120 : intval($config_store_reserve['reserve_time_limit']);
	$is_reserve = 0;
	if($order['deliverytime'] - $order['paytime'] > $reserve_time_limit * 60){
		$is_reserve = 1;
	}
	return array(
		'is_reserve' => $is_reserve,
		'store_reserve' => $config_store_reserve
	);
}

function order_goods_cancel_calculate($orderOrId, $stat_id = 0, $sign = '', $cart = array(), $extra = array()) {
	global $_W;
	$order = $orderOrId;
	if(!is_array($order)) {
		$order = order_fetch($order);
	}
	if($order['status'] == 1) {
		return error(-1, '订单未接单，不能部分退款');
	}
	if($order['status'] == 6) {
		return error(-1, '订单已取消，不能部分退款');
	}
	$id = $order['id'];
	$original_final_fee = $order['final_fee'];
	$refund_part = $cart['refund_part'];
	if($sign == '+') {
		if(empty($refund_part[$stat_id])) {
			$cart['refund_part'][$stat_id] = array(
				'total_num' => 1,
				'discount_num' => 0,
				'fee' => 0,
			);
		} else {
			$cart['refund_part'][$stat_id]['total_num']++;
		}
	} elseif($sign == '-') {
		if(empty($refund_part[$stat_id])) {
			return error(-1, '请先添加要退款的商品');
		} else {
			$cart['refund_part'][$stat_id]['total_num']--;
		}
	}
	$refund_part = $cart['refund_part'];
	$calculate_refund_fee = 0;
	$calculate_refund_total = 0;
	$can_refund_num_total = 0;
	$goods = order_fetch_goods($id);
	if($extra['is_submit'] == 1) {
		foreach($goods as $val) {
			$can_refund_num_total += $val['can_refund_num'];
		}
		if($can_refund_num_total == $cart['refund_total_num']) {
			return error(-1, '全部商品的退款,请使用取消订单功能');
		}
	}
	foreach($goods as $val) {
		if(!empty($refund_part[$val['id']])) {
			$refund_item = $refund_part[$val['id']];
			if($refund_item['total_num'] > $val['can_refund_num']) {
				return error(-1, '退款商品份数不能超过已购买数量');
			}
			if($refund_item['total_num'] < 0) {
				return error(-1, '退款商品份数不能为负数');
			}
			$normal_num = $val['can_refund_num'] - $val['can_refund_discount_num'];
			$refund_discount_num = 0;
			if($refund_item['total_num'] <= $normal_num) {
				$refund_item_fee = ($val['goods_unit_price'] + $val['box_price']) / ($order['box_price'] + $order['price']) * ($original_final_fee - $order['delivery_fee'] - $order['extra_fee']) * $refund_item['total_num'];
			} elseif($refund_item['total_num'] > $normal_num && $refund_item['total_num'] <= $val['can_refund_num']) {
				$refund_item_fee = ($val['goods_unit_price'] + $val['box_price']) / ($order['box_price'] + $order['price']) * ($original_final_fee - $order['delivery_fee'] - $order['extra_fee']) * $normal_num;
				$discount_price = $val['goods_unit_price'] - ($val['goods_original_price'] - $val['goods_price']) / $val['goods_discount_num'];
				$refund_discount_num = $refund_item['total_num'] - $normal_num;
				$refund_item_fee += ($discount_price + $val['box_price']) / ($order['box_price'] + $order['price']) * ($original_final_fee - $order['delivery_fee'] - $order['extra_fee']) * $refund_discount_num;
			}
			$cart['refund_part'][$val['id']] = array(
				'total_num' => $refund_item['total_num'],
				'discount_num' => $refund_discount_num,
				'fee' => round($refund_item_fee, 2)
			);
			if($cart['refund_part'][$val['id']]['total_num'] == 0) {
				unset($cart['refund_part'][$val['id']]);
			}
			$calculate_refund_fee += $refund_item_fee;
			$calculate_refund_total += $refund_item['total_num'];
		}
		if(is_array($cart['refund_part'][$val['id']])) {
			if($extra['is_submit'] == 1) {
				if(empty($val['data'])) {
					$val['data'] = array(
						'refund_total_num' => 0,
						'refund_total_discount_num' => 0,
						'refund_total_fee' => 0,
					);
				}
				$val['data']['refund_total_num'] += $cart['refund_part'][$val['id']]['total_num'];
				$val['data']['refund_total_discount_num'] += $cart['refund_part'][$val['id']]['discount_num'];
				$val['data']['refund_total_fee'] += $cart['refund_part'][$val['id']]['fee'];
				pdo_update('tiny_wmall_order_stat', array('data' => iserializer($val['data'])), array('uniacid' => $_W['uniacid'], 'id' => $val['id']));

				$order_goods = current($order['data']['cart'][$val['goods_id']]['options']);
				$cart['refund_part'][$val['id']]['discount_num'] = $refund_discount_num;
				$cart['refund_part'][$val['id']]['goods_unit_discount_price'] = $discount_price;
				$cart['refund_part'][$val['id']]['fee'] = round($refund_item_fee, 2);
				$cart['refund_part'][$val['id']]['goods_id'] = $val['goods_id'];
				$cart['refund_part'][$val['id']]['goods_title'] = $val['goods_title'];
				$cart['refund_part'][$val['id']]['goods_unit_price'] = $val['goods_unit_price'];
				$cart['refund_part'][$val['id']]['option_id'] = $val['option_id'];
				$cart['refund_part'][$val['id']]['thumb'] = $order_goods['thumb'];
			}
		}
	}
	$cart['refund_total_fee'] = round($calculate_refund_fee, 2);
	$cart['refund_total_num'] = $calculate_refund_total;
	return $cart;
}

function order_part_refund_handle($order, $refund_id, $extra = array()) {
	global $_W;
	$data = $order['data'];
	if(empty($data['part_refund'])) {
		$data['part_refund'] = array(
			'refund_total_fee' => 0
		);
	}
	$data['part_refund']['refund_total_fee'] += $extra['refund_fee'];
	$update_order = array(
		'store_final_fee' => $order['store_final_fee'] - $extra['refund_fee'],
		'data' => iserializer($data),
		'refund_fee' => $order['refund_fee'] + $extra['refund_fee'],
	);
	pdo_update('tiny_wmall_order', $update_order, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
	if($order['status'] == 5) {
		mload()->model('store');
		store_update_account($order['sid'], -$extra['refund_fee'], 3, $order['id'], '订单完成后发起部分退款，变更商家账户');
	}
	if($order['is_pay'] && $order['pay_type'] != 'delivery') {
		$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
		if($config_takeout['auto_refund_cancel_order'] == 1) {
			order_insert_refund_log($order['id'], $refund_id, 'apply', '部分商品退款');
			order_refund_notice($refund_id, 'apply');
			order_refund_status_update($order['id'], $refund_id, 'handle');
		} else {
			set_order_refund_status($order['id'], 1);
			order_refund_notice($refund_id, 'apply');
			order_insert_refund_log($order['id'], $refund_id, 'apply', '部分商品退款');
		}
	}
	return true;
}

function order_goods_category_limit_check($sid, $cart) {
	global $_W;
	$categorys_limit = pdo_fetchall('select id,title,min_fee,is_showtime,start_time,end_time,week from ' . tablename('tiny_wmall_goods_category') . ' where uniacid = :uniacid and sid = :sid and status = 1 and min_fee > 0', array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');
	if(empty($categorys_limit)) {
		return true;
	}
	foreach($cart['data'] as $cart_item) {
		foreach($cart_item as $goods) {
			if(!empty($categorys_limit[$goods['cid']])) {
				if(!isset($categorys_limit[$goods['cid']]['fee'])) {
					$categorys_limit[$goods['cid']]['fee'] = 0;
				}
				$categorys_limit[$goods['cid']]['fee'] += $goods['total_discount_price'];
			}
		}
	}
	foreach($categorys_limit as $limit) {
		if($limit['is_showtime'] == 1) {
			$now_week = date('N', TIMESTAMP);
			$start_time = intval(strtotime($limit['start_time']));
			$end_time = intval(strtotime($limit['end_time']));
			if(!empty($limit['week'])) {
				$week = explode(',', $limit['week']);
			}
			if(!empty($week) && !in_array($now_week, $week)) {
				continue;
			}
			if(!empty($start_time) && (TIMESTAMP <= $start_time || TIMESTAMP >= $end_time)) {
				continue;
			}
		}
		if(empty($limit['fee'])) {
			return error(-1, "需要选择【{$limit['title']}】下的商品才可下单哦~");
		}
		if($limit['fee'] > 0 && $limit['fee'] < $limit['min_fee']) {
			return error(-1, "分类【{$limit['title']}】最低消费{$limit['min_fee']}元才能下单");
		}
	}
	return true;
}

function order_push_uupaotui($id, $push = false) {
	global $_W;
	if(!check_plugin_exist('uupaotui')) {
		return error(-1, '请先购买UU跑腿配送插件');
	}
	$_W['_plugin'] = array(
		'name' => 'uupaotui'
	);
	mload()->model('plugin');
	pload()->classs('uu');
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id), array('sid', 'status', 'delivery_type'));
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	if(!in_array($order['status'], array(2, 3))) {
		return error(-1, '订单状态有误');
	}
	$balance_type = get_plugin_config('uupaotui.type');
	if($balance_type == 'store') {
		$sid = $order['sid'];
	} else {
		$sid = $order['delivery_type'] == 1 ? $order['sid'] : 0;
	}
	$uupaotui = new uuPaoTui($sid);
	$result = $uupaotui->getOrderPrice($id);
	if($push) {
		$final_fee = $result['need_paymoney'];
		$total_fee = $result['total_money'];
		$result = $uupaotui->addOrder($id, $result);
		if(!is_error($result)) {
			$data = array(
				'status' =>1,
				'final_fee' => $final_fee,
				'total_fee' => $total_fee,
				'ordercode' => $result['ordercode']
			);
			set_order_data($id, 'uupaotui', $data);
		}
	}
	return $result;
}

function order_push_shansong($id, $push = false) {
	global $_W;
	$_W['_plugin'] = array(
		'name' => 'shansong'
	);
	mload()->model('plugin');
	pload()->classs('shansong');
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id), array('sid', 'status', 'delivery_type'));
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	if(!in_array($order['status'], array(2, 3))) {
		return error(-1, '订单状态有误');
	}
	$shansong = new shanSong($id, $order['sid']);
	if($push) {
		$result = $shansong->addOrder();
	} else {
		$result = $shansong->queryDeliveryFee();
	}
	return $result;
}

function order_push_dianwoda($id, $push = false) {
	global $_W;
	$_W['_plugin'] = array(
		'name' => 'dianwoda'
	);
	mload()->model('plugin');
	pload()->classs('dianwoda');
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id), array('sid', 'status', 'delivery_type'));
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	if(!in_array($order['status'], array(2, 3))) {
		return error(-1, '订单状态有误');
	}
	$dianwoda = new dianwoda($id, $order['sid']);
	if($push) {
		$result = $dianwoda->orderCreate();
		if(!is_error($result)) {
			$data = array(
				'status' =>1,
				'ordercode' => $result['dwd_order_id'],	//点我达订单编号
				'skycon' => $result['skycon'],			//触发补贴的天气标签（多标签用英文逗号分隔）仅即时单有此逻辑
				'price' => $result['price'],			//补贴金额（单位：分）仅即时单有此逻辑
				'distance' => $result['distance']		//高德步行路径距离（单位：米）
			);
			set_order_data($id, 'dianwoda', $data);
		}
	} else {
		$result = $dianwoda->orderCostEstimate();
	}
	return $result;
}

?>
