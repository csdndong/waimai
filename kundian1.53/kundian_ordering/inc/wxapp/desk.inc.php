<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/4 0004
 * Time: 11:50
 * 点餐controller
 */
defined("IN_IA") or exit("Access Denied");
require_once  ROOT_KUNDIAN_ORDERING.'model/desk.php';
require_once  ROOT_KUNDIAN_ORDERING.'model/goods.php';
require_once  ROOT_KUNDIAN_ORDERING.'model/common.php';
require_once  ROOT_KUNDIAN_ORDERING.'model/order.php';
class DeskController{
    public $uniacid='';
    public $uid='';
    static $desk='';
    static $goods='';
    static $common='';
    static $order='';

    public function __construct(){
        global $_GPC;
        $this->uniacid=$_GPC['uniacid'];
        $this->uid=$_GPC['uid'];
        self::$goods=new Goods_KundianOrderingModel();
        self::$common=new Common_KundianOrderingModel();
        self::$desk=new Desk_KundianOrderingModel();
        self::$order=new Order_KundianOrderingModel();
    }

    public function getGoodsData($get){
        $desk_id = $get['desk_id'];
        if (!isset($get['uniacid'])) {
            echo json_encode(array('code' => 2));
            die;
        }
        $deskData=self::$desk->getDeskList(['uniacid'=>$this->uniacid,'id'=>$desk_id],false);
        //判断该餐桌是否存在正在用餐的订单
//        if ($business_mode['value'] != 1 && $deskData['status'] != 1) {
//            echo json_encode(array('code' => 2));
//            die;
//        }
        if($deskData['status'] != 1){
            echo json_encode(['code'=>2,'msg'=>'餐桌未用餐！']);die;
        }

        //分页
        $pageIndex = $get['page_index']+0;
        $pageSize = $get['page_size']+0;
        $type_id = $get['goods_type']+0;

        //商品条件
        $goods_where = [
            'uniacid'=>$this->uniacid,
            'is_put_away'=>1 //是否上架
        ];
        //商品分类条件
        $type_where = [
            'uniacid'=>$this->uniacid,
            'status'=>1
        ];

        $typeData=self::$goods->getGoodsType($type_where);
        for ($i = 0; $i < count($typeData); $i++) {
            $goods_where['type_id'] = $type_id? $type_id : $typeData[$i]['id'];
            $goodsData=self::$goods->getGoodsList($goods_where,$pageIndex,$pageSize);
            for ($j = 0; $j < count($goodsData); $j++) {
                $goodsData[$j]['price'] = number_format($goodsData[$j]['price'], 2);
                $goodsData[$j]['selectNum'] = 0;
            }
            $typeData[$i]['items'] = $goodsData;
        }
        //查询商家信息
        $aboutData=self::$common->getAboutData($this->uniacid);
        //查询商家实景图
        $setData = pdo_get('cqkundian_ordering_set', array('uniacid' => $this->uniacid, 'ikey' => 'shop_img'));
        $setData['value'] = unserialize($setData['value']);

        $request['aboutData'] = $aboutData;
        $request['typeData'] = $typeData;
        $request['setData'] = $setData;
        echo json_encode($request);die;
    }

    /** 获取餐桌信息以及该餐桌的正在用餐的订单 */
    public function getDeskData($get){
        $deskData=self::$desk->getDeskList(['uniacid'=>$this->uniacid,'id'=>$get['desk_id']],false);
        $orderData=self::$order->getDeskOrder(['uniacid' => $this->uniacid, 'desk_id' => $get['desk_id'], 'status' => 0],false);
        $setData=self::$common->getSetData(['is_open_desk_pay'],$this->uniacid);
        $request=[
            'deskData'=>$deskData,
            'orderData'=>$orderData,
            'setData'=>$setData,
        ];


        echo json_encode($request);die;
    }

    public function confirmOrder($get){
        //接收要下单的数据
        $selectData = json_decode($_POST['select'], true);
        $desk_id = $get['desk_id'];  //餐桌id
        $orderData = pdo_get('cqkundian_ordering_desk_order', array('uniacid' => $this->uniacid, 'desk_id' => $desk_id, 'status' => 0));
        //判断快餐模式
        //$business_mode = pdo_get('cqkundian_ordering_set', ['ikey' => 'business_mode', 'uniacid' => $this->$this->uniacid]);
//        $fastFood = (isset($business_mode['value']) && $business_mode['value'] == 1) ? true : false;
//        $orderType = $fastFood ? 1 : 0;
        $orderType=1;
        $res = 0;
        $orderInfo = '';
        $order_detail_id = '';
        $total_price = 0;
        foreach ($selectData as $key => $v) {
            $goodsData = pdo_get('cqkundian_ordering_goods', array('uniacid' => $this->uniacid, 'id' => $v['id']));
            if ($goodsData['count'] > 0) {
                if ($goodsData['count'] >= $v['selectNum']) {
                    $selectNum = $v['selectNum'];
                } else {
                    $selectNum = $goodsData['count'];
                }
                $orderDetail = array(
                    'order_id' => $orderData['id'],
                    'uid' => $this->uid,
                    'uniacid' => $this->uniacid,
                    'count' => $selectNum,
                    'goods_name' => $v['goods_name'],
                    'cover' => $v['cover'],
                    'status' => 0,
                    'goods_id' => $v['id'],
                    'price' => $v['price'],
                    'create_time' => time(),
                    'order_type' => $orderType,
                );

                $total_price += $v['price'];
                $goods_name = mb_substr($orderDetail['goods_name'], 0, 7, "utf-8");
                if (strlen($goods_name) / 3 < 7) {
                    $len = 7 - (strlen($goods_name) / 3);
                    for ($j = 0; $j < $len; $j++) {
                        $goods_name .= "--";
                    }
                }
                $orderInfo .= $goods_name . ' ' . $orderDetail['price'] . '  ' . $orderDetail['count'] . '  ' . number_format($orderDetail['count'] * $orderDetail['price'], 2) . '<BR>';
                $res += pdo_insert('cqkundian_ordering_desk_order_detail', $orderDetail);
                $order_detail_id .= pdo_insertid() . ',';
                pdo_update('cqkundian_ordering_goods', array('count -=' => $selectNum, 'sale_count +=' => $selectNum), array('uniacid' => $this->uniacid, 'id' => $v['id']));
            }
        }
        if ($res) {
            $this->printOrder($desk_id, $this->uniacid, $orderInfo);
            //var_dump($fastFood);die;
            //打印订单
//            if (!$fastFood) {
//                printOrder($desk_id, $this->uniacid, $orderInfo);
                //printOrder($desk_id, $this->$this->uniacid, $orderInfo, $total_price, $fastFood);
//            }

//            $createOrder['order_number'] = mt_rand(100, 999) . time() . sprintf('%03d', mt_rand(0, 999));
//            $createOrder['price'] = $createOrder['pra_price'] = round($total_price, 2);//总价
//            $createOrder['create_time'] = time();
//            $createOrder['pay_method'] = 'wxpay';
//            $createOrder['uniacid'] = $this->uniacid;
//            $createOrder['uid'] = $this->uid;
//            $createOrder['is_fast_food'] = 1;
//            $addOrder = pdo_insert('cqkundian_ordering_order', $createOrder);
//
//            if ($addOrder) {
//                $order_id = pdo_insertid();
//                $update_where['order_id IN'] = '(\'' . $order_detail_id . '0\')';
//                $res_2 = pdo_update('cqkundian_ordering_desk_order_detail', ['order_id' => $order_id], $update_where);
//                if ($res_2) {
//                    echo json_encode(['code' => 1, 'order_id' => $order_id]);
//                    die;
//                }
//                echo json_encode(array('code' => 2));
//                die;
//            }

            //更新订单总价
            $this->updateTotalPrice((int)$orderData['id'], $this->uniacid);
            echo json_encode(['code' => 1,'msg'=>'订单生成成功']);die;
        }
        echo json_encode(array('code' => 2,'msg'=>'订单生成失败!'));die;
    }

    public function deskOrderDetail($get){
        if (isset($get['desk_id'])) {
            $desk_id = (int)$get['desk_id'];
            $deskData = pdo_get('cqkundian_ordering_desk', array('uniacid' => $this->uniacid, 'id' => $desk_id));
            if(!$deskData){
                echo json_encode(['code'=>3,'msg'=>'桌台不存在']);die;
            }

            $orderData = pdo_get('cqkundian_ordering_desk_order', array('uniacid' => $this->uniacid, 'desk_id' => $desk_id, 'status' => 0));
            $order_id = $orderData['id'];
        } else {
            $order_id = $get['order_id'];
//            if ($business_mode['value'] == 1) {
//                $table_order = 'cqkundian_ordering_order';
//            } else {
//                $table_order = 'cqkundian_ordering_desk_order';
//            }
            $table_order = 'cqkundian_ordering_desk_order';
            $orderData = pdo_get($table_order, array('uniacid' => $this->uniacid, 'id' => $order_id));
            $deskData = pdo_get('cqkundian_ordering_desk', array('uniacid' => $this->uniacid, 'id' => $orderData['desk_id']));
        }

        $orderData['create_time'] = date("H:i", $orderData['create_time']);
        $orderDetailData = pdo_getall('cqkundian_ordering_desk_order_detail', array('uniacid' => $this->uniacid, 'order_id' => $order_id));
        $items = array();
        foreach ($orderDetailData as $item) {
            $item['price'] = number_format($item['price'], 2);
            $item['all'] += $item['count'];
            $item['selected'] = false;
            $uid = $item['uid'];
            unset($item['uid']);

            if (!isset($items[$uid])) {
                $items[$uid] = array('uid' => $uid, 'items' => array());
            }
            $items[$uid]['items'][] = $item;
            $items[$uid]['goods_count'] += $item['count'];
            $userData = pdo_get('cqkundian_ordering_user', array('uid' => $uid, 'uniacid' => $this->uniacid));
            $items[$uid]['userData'] = $userData;
            $items[$uid]['show'] = false;
            if ($item['status'] == 0) {
                $items[$uid]['is_up_goods'] += $item['count'];
            }
        }

        $setData=self::$common->getSetData(['is_open_desk_pay'],$this->uniacid);

        $request=[
            'orderDetail'=>$items,
            'orderData'=>$orderData,
            'deskData'=>$deskData,
            'setData'=>$setData,
        ];
        echo json_encode($request);die;
    }

    public function notify($get){
        $order_id = $get['order_id'];
        $orderData = pdo_get('cqkundian_ordering_desk_order', array('id' => $order_id, 'uniacid' => $this->uniacid));
        //1修改订单为已结算 2修改餐桌为已结账
        $order_update = array(
            'status' => 1,
            'pay_method' => '微信支付',
            'pay_time' => time(),
            'pra_price' => $orderData['total_price'],
        );
        $order_res = pdo_update('cqkundian_ordering_desk_order', $order_update, array('id' => $order_id, 'uniacid' =>  $this->uniacid));
        $desk_res = pdo_update('cqkundian_ordering_desk', array('status' => 2), array('id' => $orderData['desk_id'], 'uniacid' =>  $this->uniacid));
        if ($order_res && $desk_res) {
            //订单修改成功 =》 1修改商品库存以及销量 2.打印订单
            $this->updateGoodsCount($order_id,  $this->uniacid);
            echo json_encode(array('status' => 1));die;
        }
        echo json_encode(array('status' => 2));die;
    }

    public function getDeskOrderList($get){
        $deskOrderWhere = [
            'uniacid' => $this->uniacid,
            'uid' => $this->uid,
        ];
        $orderDetail = pdo_getall('cqkundian_ordering_desk_order_detail', $deskOrderWhere, '', '', 'create_time desc');
        $items = array();
        foreach ($orderDetail as $item) {
            $item['price'] = number_format($item['price'], 2);
            $item['all'] += $item['count'];
            $order_id = $item['order_id'];
            unset($item['order_id']);
            if ($order_id != 0) {
                if (!isset($items[$order_id])) {
                    $items[$order_id] = array('order_id' => $order_id, 'items' => array());
                }
                $items[$order_id]['items'][] = $item;
                $items[$order_id]['goods_count'] += $item['count'];
//                if ($item['order_type'] == 1) {
//                    $orderData = pdo_get('cqkundian_ordering_order', array('id' => $order_id, 'uniacid' => $this->uniacid, 'uid' => $this->uid));
//                } else {
//                    $orderData = pdo_get('cqkundian_ordering_desk_order', array('id' => $order_id, 'uniacid' => $this->uniacid));
//                }
                $orderData = pdo_get('cqkundian_ordering_desk_order', array('id' => $order_id, 'uniacid' => $this->uniacid));
                $deskData = pdo_get('cqkundian_ordering_desk', array('uniacid' => $this->uniacid, 'id' => $orderData['desk_id']));
                $orderData['deskData'] =$deskData;
                $orderData['create_time'] = date("Y-m-d H:i:s", $orderData['create_time']);

                $items[$order_id]['orderData'] = $orderData;

            }
        }
        $userData = pdo_get('cqkundian_ordering_user', array('uid' => $this->uid, 'uniacid' => $this->uniacid));
        $aboutData = pdo_get('cqkundian_ordering_about', array('uniacid' => $this->uniacid));
        $request=[
            'orderData'=>array_reverse(array_reverse($items)), //倒叙排列订单信息
            'userData'=>$userData,
            'aboutData'=>$aboutData,
        ];
        echo json_encode($request);die;
    }

    /** 获取商家端点餐订单信息 */
    public function getDeskShopOrder($get){
        $page=empty($get['page']) ? 1 : $get['page'] +1 ;
        $orderData = pdo_getall('cqkundian_ordering_desk_order', array('uniacid' => $this->uniacid), '', '', 'create_time desc', array($page, 15));
        for ($i = 0; $i < count($orderData); $i++) {
            $deskData = pdo_get('cqkundian_ordering_desk', array('id' => $orderData[$i]['desk_id'], 'uniacid' => $this->uniacid));
            $orderData[$i]['desk_name'] = $deskData['name'];
            $orderData[$i]['create_time'] = date("Y-m-d H:i:s", $orderData[$i]['create_time']);
        }
        $aboutData = pdo_get('cqkundian_ordering_about', array('uniacid' => $this->uniacid));
        $request=[
            'orderData'=>$orderData,
            'aboutData'=>$aboutData,
        ];
        echo json_encode($request);die;
    }

    /** 商家端获取餐桌信息 */
    public function getShopDeskData(){
        $deskData = pdo_getall('cqkundian_ordering_desk', array('uniacid' => $this->uniacid), '', '', 'rank asc');
        echo json_encode(['deskData'=>$deskData]);die;
    }

    /** 上菜 / 退菜 操作 */
    public function deskOpeartionGoods($get){
        //接收要上菜的数据
        $selectData = json_decode($_POST['select'], true);
        $res = 0;
        foreach ($selectData as $key => $v) {
            if ($get['type'] > 0) {
                $res += pdo_update('cqkundian_ordering_desk_order_detail', array('status' => 1), array('id' => $v['id'], 'uniacid' => $this->uniacid));
            } else {
                $res += pdo_update('cqkundian_ordering_desk_order_detail', array('status' => 2), array('id' => $v['id'], 'uniacid' => $this->uniacid));
            }
        }
        if ($res > 0) {
            $this->updateTotalPrice($get['order_id'], $this->uniacid);
            echo json_encode(array('code' => 1,'msg'=>'操作成功'));die;
        }
        echo json_encode(array('code' => 1,'msg'=>'操作失败'));die;
    }

    /** 快餐模式打印小票 */
    public function fastFoodPrint($get){
        if (!isset($_GPC['order_id']) || !$get['order_id']) {
            echo json_encode(['code' => 1123]);die;
        }
        $order_id = $get['order_id'];
        $orderData = pdo_getall('cqkundian_ordering_desk_order_detail', ['order_id' => $order_id]);
        if (!$orderData) {
            echo json_encode(['code' => 226]);die;
        }
        $orderInfo = '';
        $total_price = 0;
        foreach ($orderData as $k => $v) {
            $goods_name = mb_substr($v['goods_name'], 0, 7, "utf-8");
            if (strlen($goods_name) / 3 < 7) {
                $len = 7 - (strlen($goods_name) / 3);
                for ($j = 0; $j < $len; $j++) {
                    $goods_name .= "--";
                }
            }
            $orderInfo .= $goods_name . ' ' . $v['price'] . '  ' . $v['count'] . '  ' .
                number_format($v['count'] * $v['price'], 2) . '<BR>';
            $total_price += $v['price'];
        }
        pdo_update('cqkundian_ordering_order', ['is_pay' => 1], ['id' => $order_id]);
        $orderInfo . '<BR><RIGHT>已支付</RIGHT>';
        $res = printOrder(0, $this->uniacid, $orderInfo, $total_price, $order_id);
        if ($res) {
            $order_info = pdo_get('cqkundian_ordering_order', ['id' => $order_id]);
            $order_info['create_time'] = date('Y-m-d H:i:s', $order_info['create_time']);
            $orderData[0]['orderInfo'] = $order_info;

            echo json_encode(['code' => 1, 'order_data' => $orderData]);die;
        }
        echo json_encode(['code' => 2]);die;
    }

    public function fastFoodDetail($get){
        if (!isset($get['order_id']) || !$get['order_id']) {
            echo json_encode(['code' => 1124]);die;
        }

        $query_arr = [
            'order_id'=>$get['order_id'],
            'uid'=>$this->uid
        ];
        $orderData = pdo_getall('cqkundian_ordering_desk_order_detail', $query_arr);
        if($orderData){
            $tmp = pdo_get('cqkundian_ordering_order',['id'=>$get['order_id']]);
            $tmp['create_time'] = date('Y-m-d H:i:s',$tmp['create_time']);
            $orderData[0]['orderInfo'] = $tmp;
            echo json_encode(['code'=>1,'data'=>$orderData]);die;
        }
        echo json_encode(['code'=>2]);die;
    }

    /** 更新订单总价 */
    public function updateTotalPrice($order_id,$uniacid){
        $orderData = pdo_get('cqkundian_ordering_desk_order', array('uniacid' => $uniacid, 'id' => $order_id));
        $orderDetail = pdo_getall('cqkundian_ordering_desk_order_detail', array('uniacid' => $uniacid, 'order_id' => $order_id, 'status' => array(0, 1)));
        $total_price = $orderData['person_price'] * $orderData['person_count'];
        foreach ($orderDetail as $key => $v) {
            if ($v['status'] != 2) {
                $total_price += $v['price'] * $v['count'];
            }
        }
        pdo_update('cqkundian_ordering_desk_order', array('total_price' => $total_price), array('uniacid' => $uniacid, 'id' => $order_id));
    }

    public function updateGoodsCount($order_id, $uniacid){
        $orderDetail = pdo_getall('cqkundian_ordering_desk_order_detail', array('order_id' => $order_id, 'uniacid' => $uniacid));
        foreach ($orderDetail as $key => $value) {
            $goods_update = array(
                'count -=' => $value['count'],
                'sale_count +=' => $value['count'],
            );
            pdo_update('cqkundian_ordering_goods', $goods_update, array('id' => $value['goods_id'], 'uniacid' => $uniacid));
        }
    }
    public function printOrder($desk_id, $uniacid, $str, $total_price = 0, $fastFood = false){
        $deskData = pdo_get('cqkundian_ordering_desk', array('uniacid' => $uniacid, 'id' => $desk_id));
        $msgConfig = pdo_get("cqkundian_ordering_msg_config", array('uniacid' => $uniacid));

        $con = array(
            'uniacid' => $uniacid,
            'ikey' => array('print_sn', 'print_count'),
        );
        $data = pdo_getall('cqkundian_ordering_set', $con);
        $list = array();
        foreach ($data as $value) {
            $list[$value['ikey']] = $value['value'];
        }

        include 'print/HttpClient.class.php';
        define('USER', $msgConfig['user']);    //*必填*：飞鹅云后台注册账号
        define('UKEY', $msgConfig['ukey']);    //*必填*: 飞鹅云注册账号后生成的UKEY
        define('SN', $list['print_sn']);        //*必填*：打印机编号，必须要在管理后台里添加打印机或调用API接口添加之后，才能调用API
        //以下参数不需要修改
        define('IP', 'api.feieyun.cn');            //接口IP或域名
        define('PORT', 80);                        //接口IP端口
        define('PATH', '/Api/Open/');        //接口路径
        define('STIME', time());                //公共参数，请求时间
        define('SIG', sha1(USER . UKEY . STIME));   //公共参数，请求公钥
        $deskData['name'] = isset($deskData['name']) ? $deskData['name'] : '未知';
        $title = '<CB>' . $deskData['name'] . '下单</CB><BR>';
        $end = '';
        $MON = $this->makeOrderNumber($uniacid);
        if ($fastFood) {
            $title = '<CB>#取餐号：' . $MON . '</CB><BR>';
            $str .= '<BR><RIGHT>合计：' . $total_price . '元</RIGHT><BR>';
            $str .= '<RIGHT>已付款</RIGHT>';
            $str .= '<BR><RIGHT>时间：' . date('Y-m-d H:i:s') . '</RIGHT><BR>';
            $end = '<C>流水号：' . $this->makeSnOrder() . '</C>';
            //神奇吧，这里的fastFood是订单号ID
            if (is_numeric($fastFood)) {
                pdo_update('cqkundian_ordering_order', ['fast_food_number' => $MON], ['id' => $fastFood]);
            }
        }
        $orderInfo = $title . '<BR>';
        $orderInfo .= '名称　　　　　 单价 数量 金额<BR>';
        $orderInfo .= '--------------------------------<BR>';
        $orderInfo .= $str;
        $orderInfo .= '--------------------------------<BR>' . $end;
        //打印
        $res = $this->wp_print(SN, $orderInfo, $list['print_count']);
        if(is_string($res)){
            file_put_contents('printRequest.log',$res);
        }
        return $res;
    }

    public function makeOrderNumber($uniacid){
        $key = 'makeOrderNumber:' . $uniacid;
        $hasKey = cache_load($key);
        //初始化
        if (!$hasKey || !isset($hasKey[1]) || $hasKey[1] < date('Ymd')) {
            $num = 0;
        } else {
            $num = (int)$hasKey[0];
        }
        $num++;

        if (strlen($num) < 3) {
            $num = sprintf('%03d', $num);
        }
        cache_write($key, [$num, date('Ymd')]);
        return $num;
    }

    public function wp_print($printer_sn, $orderInfo, $times){
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

    public function makeSnOrder(){
        return 'NO.' . sprintf('%05d', mt_rand(0, 99999)) . date('Ymd') . sprintf('%03d', mt_rand(0, 999));
    }
}
