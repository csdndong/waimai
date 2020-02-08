<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/30 0030
 * Time: 11:40
 */
defined("IN_IA") or exit("Access denied");
!defined('ROOT_KUNDIAN_ORDERING') && define('ROOT_KUNDIAN_ORDERING', IA_ROOT . '/addons/kundian_ordering/');
global $_W, $_GPC;
$uniacid = $_W['uniacid'];
$op = $_GPC['op'] ? $_GPC['op'] : 'desk_list';
//餐台列表
if ($op == 'desk_list') {
    $all = pdo_getall('cqkundian_ordering_desk', array('uniacid' => $uniacid));
    $total = count($all);
    $pageIndex = $_GPC['page'] ? intval($_GPC['page']) : 1;
    $pageSize = 15;
    $pager = pagination($total, $pageIndex, $pageSize);
    $list = pdo_getall('cqkundian_ordering_desk', array('uniacid' => $uniacid), '', '', 'rank asc', array($pageIndex, $pageSize));
    for ($i = 0; $i < count($list); $i++) {
        if ($list[$i]['status'] == 1) {
            $list[$i]['orderData'] = pdo_get('cqkundian_ordering_desk_order', array('uniacid' => $uniacid, 'desk_id' => $list[$i]['id'], 'status' => 0));
            $list[$i]['orderData']['create_time'] = date("H:i", $list[$i]['orderData']['create_time']);
        } elseif ($list[$i]['status'] == 2) {
            $list[$i]['orderData'] = pdo_get('cqkundian_ordering_desk_order', array('uniacid' => $uniacid, 'desk_id' => $list[$i]['id'], 'status' => 1));
            $list[$i]['orderData']['create_time'] = date("H:i", $list[$i]['orderData']['create_time']);
        }
    }
    include $this->template('web/desk/index');
}
//餐桌列表
if ($op == 'desk_table') {
    $all = pdo_getall('cqkundian_ordering_desk', array('uniacid' => $uniacid));
    $total = count($all);
    $pageIndex = $_GPC['page'] ? intval($_GPC['page']) : 1;
    $pageSize = 10;
    $pager = pagination($total, $pageIndex, $pageSize);
    $list = pdo_getall('cqkundian_ordering_desk', array('uniacid' => $uniacid), '', '', 'rank asc', array($pageIndex, $pageSize));
    include $this->template('web/desk/desk_table');
}

//餐桌列表添加/编辑
if ($op == 'desk_table_edit') {
    if (!empty($_GPC['id'])) {
        $list = pdo_get('cqkundian_ordering_desk', array('uniacid' => $uniacid, 'id' => $_GPC['id']));
    }
    include $this->template('web/desk/desk_table_edit');
}

//餐桌添加/编辑保存
if ($op == 'desk_table_save') {
    $data = array(
        'name' => trim($_GPC['name']),
        'person' => intval($_GPC['person']),
        'rank' => intval($_GPC['rank']),
        'uniacid' => $uniacid,
    );
    if (empty($_GPC['id'])) {
        $data['create_time'] = time();
        $res = pdo_insert('cqkundian_ordering_desk', $data);
    } else {
        $res = pdo_update('cqkundian_ordering_desk', $data, array('uniacid' => $uniacid, 'id' => $_GPC['id']));
    }
    if ($res) {
        message('操作成功', url('site/entry/desk', array('m' => 'kundian_ordering', 'op' => 'desk_table')));
        die;
    } else {
        message('操作失败');
        die;
    }
}

//餐台->下单
if ($op == 'desk_detail') {
    $id = $_GPC['id'];
    if ($id) {    //存在餐桌id
        //查询改餐桌是否为空闲
        $deskData = pdo_get('cqkundian_ordering_desk', array('uniacid' => $uniacid, 'id' => $id));
        //不是空闲则需要查询订单信息
        if ($deskData['status'] != 0) {
            $orderData = pdo_get('cqkundian_ordering_desk_order', array('uniacid' => $uniacid, 'desk_id' => $id, 'status' => 0));
            $orderData['create_time'] = date("Y-m-d H:i:s", $orderData['create_time']);    //格式化时间
            number_format($orderData['total_price'], 2);     //格式化价格
            $orderDetail = pdo_getall('cqkundian_ordering_desk_order_detail', array('unaicid' => $uniacid, 'order_id' => $orderData['id']));
        }
        //查询商品分类以及商品信息
        $goodsType = pdo_getall('cqkundian_ordering_goods_type', array('uniacid' => $uniacid));
        $goodsData = pdo_getall('cqkundian_ordering_goods', array('uniacid' => $uniacid), '', '', 'rank asc');

        include $this->template("web/desk/desk_detail");
    }
}

//根据分类获取商品信息
if ($op == 'getGoodsData') {
    $type_id = $_GPC['type_id'];
    $condition = array(
        'uniacid' => $uniacid,
        'is_put_away' => 1,
    );
    if (!empty($type_id)) {
        $condition = array(
            'type_id' => $type_id,
        );
    }
    if (!empty($_GPC['goods_number'])) {
        $condition['goods_number LIKE'] = '%' . $_GPC['goods_number'] . '%';
    }
    if (!empty($_GPC['goods_name'])) {
        $condition['goods_name LIKE'] = '%' . $_GPC['goods_name'] . '%';
    }
    $goodsData = pdo_getall('cqkundian_ordering_goods', $condition, '', '', 'rank asc');
    echo json_encode($goodsData);
}

//下订单
if ($op == 'addDeskOrder') {
    $goods_id = explode('_', $_GPC['goods_id']);
    $count = explode('_', $_GPC['count']);
    $desk_id = $_GPC['desk_id'];
    $person = $_GPC['person'];
    $person_price = $_GPC['person_price'];
    $total_price = $_GPC['total_price'];
    $deskData = pdo_get('cqkundian_ordering_desk', array('uniacid' => $uniacid, 'id' => $desk_id));
    if ($deskData['status'] == 0) {
        //组装订单数据
        $orderData = array(
            'order_number' => rand(100, 999) . time() . rand(100, 999),
            'desk_id' => $desk_id,
            'person_count' => $person,
            'person_price' => $person_price,
            'create_time' => time(),
            'uniacid' => $uniacid,
            'status' => 0,
            'total_price' => $total_price,
        );
        $order_res = pdo_insert('cqkundian_ordering_desk_order', $orderData);
        $order_id = pdo_insertid();
        //查询商品信息
        $goods_where = array(
            'id in' => $goods_id,
            'uniacid' => $uniacid,
        );
        $goodsData = pdo_getall('cqkundian_ordering_goods', $goods_where);
        //组装商品详细信息
        for ($i = 0; $i < count($goodsData); $i++) {
            $orderDetail = array(
                'order_id' => $order_id,
                'goods_name' => $goodsData[$i]['goods_name'],
                'cover' => $goodsData[$i]['cover'],
                'goods_id' => $goodsData[$i]['id'],
                'price' => $goodsData[$i]['price'],
                'count' => $count[$i],
                'create_time' => time(),
                'uniacid' => $uniacid,
                'status' => 0,
            );
            pdo_insert('cqkundian_ordering_desk_order_detail', $orderDetail);
        }
        if ($order_res) {
            //改变餐桌状态=>开餐
            pdo_update('cqkundian_ordering_desk', array('status' => 1), array('uniacid' => $uniacid, 'id' => $desk_id));
            echo json_encode(array('code' => 1));
            die;
        } else {
            echo json_encode(array('code' => 2));
            die;
        }
    } else {
        echo json_encode(array('code' => 3));
        die;
    }
}

//餐台订单详情
if ($op == 'desk_order_detail') {
    $id = $_GPC['desk_id'];
    if ($id) {    //存在餐桌id
        //查询改餐桌是否为空闲
        $deskData = pdo_get('cqkundian_ordering_desk', array('uniacid' => $uniacid, 'id' => $id));
        //不是空闲则需要查询订单信息
        if ($deskData['status'] != 0) {
            $orderData = pdo_get('cqkundian_ordering_desk_order', array('uniacid' => $uniacid, 'desk_id' => $id, 'status' => 0));
            $orderData['create_time'] = date("Y-m-d H:i:s", $orderData['create_time']);    //格式化时间
            number_format($orderData['total_price'], 2);     //格式化价格
            $orderDetail = pdo_getall('cqkundian_ordering_desk_order_detail', array('uniacid' => $uniacid, 'order_id' => $orderData['id']));
            $total_price = getTotalPrice($orderData['id'], $uniacid);
        }
        //查询商品分类以及商品信息
        $goodsType = pdo_getall('cqkundian_ordering_goods_type', array('uniacid' => $uniacid));
        $goodsData = pdo_getall('cqkundian_ordering_goods', array('uniacid' => $uniacid), '', '', 'rank asc');

        include $this->template("web/desk/desk_order_detail");
    }
}

//操作订单信息
if ($op == 'operationOrder') {
    $goods_id = $_GPC['goods_id'];
    $order_id = $_GPC['order_id'];
    $type = $_GPC['type'];
    $condition = array(
        'goods_id' => $goods_id,
        'order_id' => $order_id,
        'status' => 0,
    );
    $orderDetail = pdo_get('cqkundian_ordering_desk_order_detail', $condition);
    if ($type > 0) {    //增加数据
        $goodsData = pdo_get('cqkundian_ordering_goods', array('uniacid' => $uniacid, 'id' => $goods_id));
        $order_detail = array(
            'order_id' => $order_id,
            'goods_name' => $goodsData['goods_name'],
            'cover' => $goodsData['cover'],
            'goods_id' => $goods_id,
            'price' => $goodsData['price'],
            'count' => 1,
            'create_time' => time(),
            'uniacid' => $uniacid,
            'status' => 0,
        );
        if (empty($orderDetail)) {        //订单详情中不存在订单
            $res = pdo_insert('cqkundian_ordering_desk_order_detail', $order_detail);
        } else {
            $res = pdo_update('cqkundian_ordering_desk_order_detail', array('count +=' => 1), $condition);
        }
    } else {      //减少数据
        if ($orderDetail['count'] > 1) {
            $res = pdo_update('cqkundian_ordering_desk_order_detail', array('count -=' => 1), $condition);
        } else {
            $res = pdo_delete('cqkundian_ordering_desk_order_detail', $condition);
        }
    }
    $orderDataDetail = pdo_getall('cqkundian_ordering_desk_order_detail', array('uniacid' => $uniacid, 'order_id' => $order_id), '', '', 'create_time asc');
    foreach ($orderDataDetail as $key => $v) {
        $v['price'] = number_format($v['price'], 2);
    }
    $request['orderDetail'] = $orderDataDetail;
    $request['total_price'] = getTotalPrice($order_id, $uniacid);
    echo $res ? json_encode($request) : json_encode(array('code' => 2));
    die;
}

//给后厨打印出菜小票
if ($op == 'printFood') {
    $printData = $_GPC['printInfo'];
    $desk = pdo_get('cqkundian_ordering_desk', ['id' => $printData[0]['desk_id']]);
    foreach ($printData as $k => $v) {
        if ($v['count'] > 0) {
            $goods = pdo_get('cqkundian_ordering_goods', ["id" => $v['goods_id'], 'uniacid' => $uniacid]);
            $content[$k]['goods_name'] = $goods['goods_name'];
            $content[$k]['price'] = $goods['price'];
            $content[$k]['count'] = $v['count'];
            $content[$k]['desk_id'] = $v['desk_id'];
        }

    }
    if ($printData[0]['type'] == 'view') {
        echo json_encode(['data' => $content]);
        die;
    }
    $ps = $printData[0]['getPs'];
    $res = printDeskDish($uniacid, $content, $desk['name'], $ps);
    if ($res && json_decode($res)->ret == 0) {
        echo 200;
    } else {
        echo $res;
    }
    die;
}

//编辑人数和餐位费
if ($op == 'operationOrderPerson') {
    $order_id = $_GPC['order_id'];
    $person_price = $_GPC['person_price'];
    $person = $_GPC['person'];
    $res = pdo_update('cqkundian_ordering_desk_order', array('person_count' => $person, 'person_price' => $person_price));
    $total_price = getTotalPrice($order_id, $uniacid);
    if ($res) {
        $request['code'] = 1;
        $request['total_price'] = $total_price;
        echo json_encode($request);
        die;
    } else {
        $request['code'] = 2;
        echo json_encode($request);
        die;
    }
}

//将餐台设置为空闲
if ($op == 'set_desk_leisure') {
    $desk_id = $_GPC['desk_id'];
    $res = pdo_update('cqkundian_ordering_desk', array('status' => 0), array('uniacid' => $uniacid, 'id' => $desk_id));
    if ($res) {
        message('操作成功', url('site/entry/desk', array('m' => 'kundian_ordering', 'op' => 'desk_list')));
        die;
    } else {
        message('操作失败');
        die;
    }
}

//结账
if ($op == 'settleAccounts') {
    $order_id = $_GPC['order_id'];
    $is_print_order = $_GPC['is_print_order'];
    $pay_method = $_GPC['pay_method'];
    $orderData = pdo_get('cqkundian_ordering_desk_order', array('id' => $order_id, 'uniacid' => $uniacid));
    $print_count = $_GPC['print_count'];
    //1修改订单为已结算 2修改餐桌为已结账
    $order_update = array(
        'status' => 1,
        'pay_method' => $pay_method,
        'pay_time' => time(),
        'pra_price' => getTotalPrice($order_id, $uniacid),
    );
    $order_res = pdo_update('cqkundian_ordering_desk_order', $order_update, array('id' => $order_id, 'uniacid' => $uniacid));
    $desk_res = pdo_update('cqkundian_ordering_desk', array('status' => 2), array('id' => $orderData['desk_id'], 'uniacid' => $uniacid));
    if ($order_res && $desk_res) {
        //订单修改成功 =》 1修改商品库存以及销量 2.打印订单
        updateGoodsCount($order_id, $uniacid);
        if ($is_print_order) {
            printOrder($order_id, $uniacid, $print_count);
        }
        echo json_encode(array('status' => 1));
        die;
    } else {
        echo json_encode(array('status' => 2));
        die;
    }
}

//查看历史订单列表
if ($op == 'history_order') {
    $desk_id = $_GPC['desk_id'];
    $condition = array(
        'uniacid' => $uniacid,
        'desk_id' => $desk_id,
    );
    if (!empty($_GPC['order_number'])) {
        $order_number = trim($_GPC['order_number']);
        $condition['order_number LIKE'] = '%' . $order_number . '%';
    }
    $time = $_GPC['time'];
    if ($time) {
        $condition['create_time >'] = strtotime($time['start']);
        $condition['create_time <'] = strtotime($time['end']);
    }

    $deskData = pdo_get('cqkundian_ordering_desk', array('uniacid' => $uniacid, 'id' => $desk_id));
    $all = pdo_getall('cqkundian_ordering_desk_order', $condition);
    $total = count($all);
    $pageIndex = $_GPC['page'] ? intval($_GPC['page']) : 1;
    $pageSize = 15;
    $pager = pagination($total, $pageIndex, $pageSize);
    $list = pdo_getall('cqkundian_ordering_desk_order', $condition, '', '', 'create_time desc', array($pageIndex, $pageSize));
    $totalMoney = 0;
    foreach ($list as $k => $v) {
        $totalMoney += $v['pra_price'];
        $v['total_price'] = number_format($v['total_price'], 2);
    }
    $totalMoney = number_format($totalMoney, 2);
    include $this->template('web/desk/history_order');
}

//查看历史订单详细信息
if ($op == 'history_order_detail') {
    $order_id = $_GPC['id'];
    $orderData = pdo_get('cqkundian_ordering_desk_order', array('uniacid' => $uniacid, 'id' => $order_id));
    $orderData['create_time'] = date("Y-m-d H:i:s", $orderData['create_time']);
    $orderData['pay_time'] = date("Y-m-d H:i:s", $orderData['pay_time']);
    $orderDetail = pdo_getall('cqkundian_ordering_desk_order_detail', array('uniacid' => $uniacid, 'order_id' => $order_id));
    include $this->template("web/desk/history_order_detail");
}

if ($op == 'order') {
    $desk_id = $_GPC['desk_id'];
    $condition = array(
        'uniacid' => $uniacid,
    );
    if (!empty($_GPC['order_number'])) {
        $order_number = trim($_GPC['order_number']);
        $condition['order_number LIKE'] = '%' . $order_number . '%';
    }
    $time = $_GPC['time'];
    if ($time) {
        $condition['create_time >'] = strtotime($time['start']);
        $condition['create_time <'] = strtotime($time['end']);
    }

    $deskData = pdo_get('cqkundian_ordering_desk', array('uniacid' => $uniacid, 'id' => $desk_id));
    $pageIndex = $_GPC['page'] ? intval($_GPC['page']) : 1;
    $returnData = getOrderList($condition, $pageIndex);
    include $this->template('web/desk/order');
}

//导出订单
if ($op == 'outOrder') {
    $data[][0] = array('订单编号', '用餐人数', '餐位费', '订单金额', '下单时间', '是否结算', '结算时间', '支付方式');
    $desk_id = $_GPC['desk_id'];
    $condition = array();
    if (!empty($_GPC['order_number'])) {
        $order_number = trim($_GPC['order_number']);
        $condition['order_number LIKE'] = '%' . $order_number . '%';
    }
    if ($_GPC['begin_time'] && $_GPC['end_time']) {
        $begin_time = $_GPC['begin_time'];
        $end_time = $_GPC['end_time'];
        $condition['create_time >'] = strtotime($begin_time);
        $condition['create_time <'] = strtotime($end_time);
    }
    if ($desk_id) {
        $condition['desk_id'] = $desk_id;
    }
    $condition['uniacid'] = $uniacid;
    $listCount = pdo_getall("cqkundian_ordering_desk_order", $condition, '', '', 'create_time desc');

    //循环遍历整理卡券信息
    $orderData = array();
    for ($i = 0; $i < count($listCount); $i++) {
        $orderData[$i]['order_number'] = ' ' . $listCount[$i]['order_number'];
        $orderData[$i]['person_count'] = ' ' . $listCount[$i]['person_count'];
        $orderData[$i]['person_price'] = ' ' . $listCount[$i]['person_price'];
        $orderData[$i]['total_price'] = $listCount[$i]['total_price'];
        $orderData[$i]['create_time'] = ' ' . date("Y-m-d H:i:s", $listCount[$i]['create_time']);
        if ($listCount[$i]['status'] == 1) {
            $orderData[$i]['is_pay'] = "已结算";
        } else {
            $orderData[$i]['is_pay'] = "未支付";
        }
        $orderData[$i]['pay_time'] = ' ' . date("Y-m-d H:i:s", $listCount[$i]['pay_time']);
        $orderData[$i]['pay_method'] = $listCount[$i]['pay_method'];
    }
    $data[] = $orderData;
    require_once "Org/PHPExcel.class.php";
    require_once "Org/PHPExcel/Writer/Excel5.php";
    require_once "Org/PHPExcel/IOFactory.php";
    require_once "Org/function.php";
    $filename = "订单";
    getExcel($filename, $data);
}

if ($op == 'desk_print_set') {
    $printData = pdo_getall('cqkundian_ordering_print', array('uniacid' => $uniacid));
    $con = array(
        'uniacid' => $uniacid,
        'ikey' => array('print_sn', 'print_count'),
    );
    $data = pdo_getall('cqkundian_ordering_set', $con);
    $list = array();
    foreach ($data as $value) {
        $list[$value['ikey']] = $value['value'];
    }
    include $this->template("web/desk/print_set");
}

if ($op == 'print_set_save') {
    $data = $_POST;
    $res = 0;
    foreach ($data as $key => $v) {
        $insertData = array(
            'ikey' => $key,
            'value' => $v,
            'uniacid' => $uniacid,
        );
        $con = array(
            'uniacid' => $uniacid,
            'ikey' => $key,
        );
        $have = pdo_get('cqkundian_ordering_set', $con);
        if (empty($have)) {
            $res += pdo_insert('cqkundian_ordering_set', $insertData);
        } else {
            $res += pdo_update('cqkundian_ordering_set', $insertData, $con);
        }
    }
    if ($res) {
        message('操作成功', url('site/entry/desk', array('m' => 'kundian_ordering', 'op' => 'desk_print_set')));
        die;
    } else {
        message('操作失败');
        die;
    }
}
if ($op == 'desk_pay_set') {
    $printData = pdo_getall('cqkundian_ordering_print', array('uniacid' => $uniacid));
    $con = array(
        'uniacid' => $uniacid,
        'ikey' => array('is_open_desk_pay'),
    );
    $data = pdo_getall('cqkundian_ordering_set', $con);
    $list = array();
    foreach ($data as $value) {
        $list[$value['ikey']] = $value['value'];
    }
    include $this->template("web/desk/pay_set");
}

if ($op == 'pay_set_save') {
    $data = $_POST;
    $res = 0;
    if (empty($data['is_open_desk_pay'])) {
        $data['is_open_desk_pay'] = 0;
    }
    foreach ($data as $key => $v) {
        $insertData = array(
            'ikey' => $key,
            'value' => $v,
            'uniacid' => $uniacid,
        );
        $con = array(
            'uniacid' => $uniacid,
            'ikey' => $key,
        );
        $have = pdo_get('cqkundian_ordering_set', $con);
        if (empty($have)) {
            $res += pdo_insert('cqkundian_ordering_set', $insertData);
        } else {
            $res += pdo_update('cqkundian_ordering_set', $insertData, $con);
        }
    }
    if ($res) {
        message('操作成功', url('site/entry/desk', array('m' => 'kundian_ordering', 'op' => 'desk_pay_set')));
        die;
    } else {
        message('操作失败');
        die;
    }
}

//删除餐桌
if ($op == 'delete_desk') {
    $res = pdo_delete('cqkundian_ordering_desk', array('uniacid' => $uniacid, 'id' => $_GPC['id']));
    if ($res) {
        echo json_encode(array('status' => 1));
        die;
    } else {
        echo json_encode(array('status' => 2));
        die;
    }
}

//上菜和退菜
if($op == 'serveGoods'){
    $where['order_id'] = $_GPC['order_id'];
    $where['goods_id'] = $_GPC['goods_id'];
    $where['uniacid'] = $uniacid;
    $where['status'] = 0;
    //var_dump($where);die;
    $status = $_GPC['type']=='returned'? 2 : 1;
    $res = pdo_update('cqkundian_ordering_desk_order_detail', array('status' => $status), $where);
    //pdo_debug();
    if($res){
        echo 200;die;
    }
    echo $res;die;
}

/**
 * 计算订单总价
 * @param $order_id
 * @param $uniacid
 * @return string
 */
function getTotalPrice($order_id, $uniacid)
{
    $orderData = pdo_get('cqkundian_ordering_desk_order', array('uniacid' => $uniacid, 'id' => $order_id));
    $orderDetail = pdo_getall('cqkundian_ordering_desk_order_detail', array('uniacid' => $uniacid, 'order_id' => $order_id));
    $total_price = 0;
    $set_price = $orderData['person_price'] * $orderData['person_count'];
    if ($set_price) {
        $total_price += $set_price;
    }
    foreach ($orderDetail as $key => $v) {
        if ($v['status'] != 2) {
            $total_price += $v['price'] * $v['count'];
        }
    }
    pdo_update('cqkundian_ordering_desk_order', array('total_price' => $total_price), array('uniacid' => $uniacid, 'id' => $order_id));
    return number_format($total_price, 2);
}

/**
 * 更新商品库存以及销量
 * @param $order_id
 * @param $uniacid
 */
function updateGoodsCount($order_id, $uniacid)
{
    $orderDetail = pdo_getall('cqkundian_ordering_desk_order_detail', array('order_id' => $order_id, 'uniacid' => $uniacid));
    foreach ($orderDetail as $key => $value) {
        $goods_update = array(
            'count -=' => $value['count'],
            'sale_count +=' => $value['count'],
        );
        pdo_update('cqkundian_ordering_goods', $goods_update, array('id' => $value['goods_id'], 'uniacid' => $uniacid));
    }
}

function getOrderList($condition, $pageIndex)
{
    $all = pdo_getall('cqkundian_ordering_desk_order', $condition);
    $total = count($all);
    $pageSize = 15;
    $pager = pagination($total, $pageIndex, $pageSize);
    $list = pdo_getall('cqkundian_ordering_desk_order', $condition, '', '', 'create_time desc', array($pageIndex, $pageSize));
    $totalMoney = 0;
    foreach ($list as $k => $v) {
        $totalMoney += $v['pra_price'];
        $v['total_price'] = number_format($v['total_price'], 2);
    }
    $totalMoney = number_format($totalMoney, 2);
    return array('pager' => $pager, 'list' => $list, 'totalMoney' => $totalMoney);
}

/**
 * @param $order_id  订单号
 */
function printOrder($order_id, $uniacid, $times)
{
    $aboutData = pdo_get('cqkundian_ordering_about', array('uniacid' => $uniacid));
    $orderData = pdo_get("cqkundian_ordering_desk_order", array('uniacid' => $uniacid, 'id' => $order_id));
    $orderDetail = pdo_getall("cqkundian_ordering_desk_order_detail", array('uniacid' => $uniacid, 'order_id' => $order_id));
    $msgConfig = pdo_get("cqkundian_ordering_msg_config", array('uniacid' => $uniacid));
    include 'print/HttpClient.class.php';
    define('USER', $msgConfig['user']);    //*必填*：飞鹅云后台注册账号
    define('UKEY', $msgConfig['ukey']);    //*必填*: 飞鹅云注册账号后生成的UKEY
    define('SN', $msgConfig['sn']);        //*必填*：打印机编号，必须要在管理后台里添加打印机或调用API接口添加之后，才能调用API
    //以下参数不需要修改
    define('IP', 'api.feieyun.cn');            //接口IP或域名
    define('PORT', 80);                        //接口IP端口
    define('PATH', '/Api/Open/');        //接口路径
    define('STIME', time());                //公共参数，请求时间
    define('SIG', sha1(USER . UKEY . STIME));   //公共参数，请求公钥

    $orderInfo = '<CB>' . $aboutData['merchant_name'] . '</CB><BR>';
    $orderInfo .= '名称　　　　　 单价 数量 金额<BR>';
    $orderInfo .= '--------------------------------<BR>';
    for ($i = 0; $i < count($orderDetail); $i++) {
        $goods_name = mb_substr($orderDetail[$i]['goods_name'], 0, 7, "utf-8");
        if (strlen($goods_name) / 3 < 7) {
            for ($j = 0; $j < 7 - strlen($goods_name) / 3; $j++) {
                $goods_name .= "　";
            }
        }
        $orderDetail[$i]['total_price'] = $orderDetail[$i]['price'] * $orderDetail[$i]['count'];
        $orderInfo .= $goods_name . '　' . $orderDetail[$i]['price'] . '   ' . $orderDetail[$i]['count'] . '   ' . $orderDetail[$i]['total_price'] . '<BR>';
    }
    $orderInfo .= '备注：' . $orderData['remark'] . '<BR>';
    $orderInfo .= '--------------------------------<BR>';
    $orderInfo .= '合计：' . $orderData['total_price'] . '元<BR>';
    $orderInfo .= '结账时间：' . date("Y-m-d H:i:s", $orderData['pay_time']) . '<BR>';

//echo $orderInfo;
    //打开注释可测试
    return wp_print(SN, $orderInfo, $times);

}

//给后厨的票
function printDeskDish($uniacid, $content, $desk_name, $ps = '', $num = 1)
{
    $msgConfig = pdo_get("cqkundian_ordering_msg_config", array('uniacid' => $uniacid));
    include 'print/HttpClient.class.php';
    define('USER', $msgConfig['user']);    //*必填*：飞鹅云后台注册账号
    define('UKEY', $msgConfig['ukey']);    //*必填*: 飞鹅云注册账号后生成的UKEY
    define('SN', $msgConfig['sn']);        //*必填*：打印机编号，必须要在管理后台里添加打印机或调用API接口添加之后，才能调用API
    //以下参数不需要修改
    define('IP', 'api.feieyun.cn');            //接口IP或域名
    define('PORT', 80);                        //接口IP端口
    define('PATH', '/Api/Open/');        //接口路径
    define('STIME', time());                //公共参数，请求时间
    define('SIG', sha1(USER . UKEY . STIME));   //公共参数，请求公钥

    $orderInfo = '<CB>' . $content[0]['desk_id'] . '号桌（' . $desk_name . '）点餐</CB><BR>';
    $orderInfo .= '名称　　　　　 单价 数量 金额<BR>';
    $orderInfo .= '--------------------------------<BR>';
    $total_price = 0;
    for ($i = 0; $i < count($content); $i++) {
        $goods_name = mb_substr($content[$i]['goods_name'], 0, 7, "utf-8");
        if (strlen($goods_name) / 3 < 7) {
            for ($j = 0; $j < 7 - strlen($goods_name) / 3; $j++) {
                $goods_name .= "　 ";
            }
        }
        $content[$i]['total_price'] = $content[$i]['price'] * $content[$i]['count'];
        $orderInfo .= $goods_name . '　' . $content[$i]['price'] . '   ' . $content[$i]['count'] . '   ' . $content[$i]['total_price'] . '<BR>';
        $total_price += $content[$i]['total_price'];
    }
    $orderInfo .= '<BR><BOLD>备注：' . $ps . '</BOLD><BR>';
    $orderInfo .= '--------------------------------<BR>';
    $orderInfo .= '合计：' . $total_price . '元<BR>';
    $orderInfo .= '出票时间：' . date("Y-m-d H:i:s") . '<BR>';
    //return $orderInfo;
    return wp_print(SN, $orderInfo, $num);
}


/*
 *  方法1
	拼凑订单内容时可参考如下格式
	根据打印纸张的宽度，自行调整内容的格式，可参考下面的样例格式
*/
function wp_print($printer_sn, $orderInfo, $times)
{

    $content = array(
        'user' => USER,
        'stime' => STIME,
        'sig' => SIG,
        'apiname' => 'Open_printMsg',

        'sn' => $printer_sn,
        'content' => $orderInfo,
        'times' => $times//打印次数
    );

    $client = new HttpClient(IP, PORT);
    if (!$client->post(PATH, $content)) {
        echo 'error';
    } else {
        //服务器返回的JSON字符串，建议要当做日志记录起来
        return $client->getContent();
    }

}













