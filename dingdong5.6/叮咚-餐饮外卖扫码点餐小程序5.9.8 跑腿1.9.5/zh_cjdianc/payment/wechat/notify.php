<?php
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';
global $_W, $_GPC;
$input = file_get_contents('php://input');
$isxml = true;
if (!empty($input) && empty($_GET['out_trade_no'])) {
    $obj = isimplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
    $res = $data = json_decode(json_encode($obj), true);
    if (empty($data)) {
        $result = array(
            'return_code' => 'FAIL',
            'return_msg' => ''
        );
        echo array2xml($result);
        exit;
    }
    if ($data['result_code'] != 'SUCCESS' || $data['return_code'] != 'SUCCESS') {
        $result = array(
            'return_code' => 'FAIL',
            'return_msg' => empty($data['return_msg']) ? $data['err_code_des'] : $data['return_msg']
        );
        echo array2xml($result);
        exit;
    }
    $get = $data;
} else {
    $isxml = false;
    $get = $_GET;
}
load()->web('common');
load()->model('mc');
load()->func('communication');
$_W['uniacid'] = $_W['weid'] = intval($get['attach']);
$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
$_W['acid'] = $_W['uniaccount']['acid'];
$paySetting = uni_setting($_W['uniacid'], array('payment'));
if($res['return_code'] == 'SUCCESS' && $res['result_code'] == 'SUCCESS' ){
    $logno = trim($res['out_trade_no']);
    if (empty($logno)) {
        exit;
    }
$str=$_W['siteroot'];
$n = 0;
for($i = 1;$i <= 3;$i++) {
    $n = strpos($str, '/', $n);
    $i != 3 && $n++;
}
$url=substr($str,0,$n);
function getapi($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
    $data = curl_exec($ch);
    curl_close($ch);
}
    $order=pdo_get('cjdc_order',array('code'=>$logno));
    $czorder=pdo_get('cjdc_czorder',array('code'=>$logno));
    $hyorder=pdo_get('cjdc_hyorder',array('code'=>$logno));
    $qgorder=pdo_get('cjdc_qgorder',array('code'=>$logno));
    $grouporder=pdo_get('cjdc_grouporder',array('code'=>$logno));
    if($grouporder['state']==1){
    pdo_update('cjdc_grouporder',array('state'=>2,'pay_time'=>time()),array('id'=>$grouporder['id']));
    //改变商品
    pdo_update('cjdc_groupgoods',array('ysc_num +='=>$grouporder['goods_num'],'inventory -='=>$grouporder['goods_num']),array('id'=>$grouporder['goods_id']));
        if($grouporder['group_id']>0){
        $count=pdo_get('cjdc_grouporder', array('group_id'=>$grouporder['group_id'],'state '=>2), array('count(user_id) as count'));
        $group=pdo_get('cjdc_group',array('id'=>$grouporder['group_id']));
            if($group['kt_num']==$count['count']){
                $state=2;
            }else{
                $state=1;
            }
        //改变团状态
        pdo_update('cjdc_group',array('state'=>$state,'yg_num +='=>1),array('id'=>$grouporder['group_id']));
        }           
    }
    if($qgorder['state']==1){
        $time=time();
        $good=pdo_get('cjdc_qggoods',array('id'=>$qgorder['good_id']));
        $dq_time=$time+$good['consumption_time']*60*60*24;
        pdo_update('cjdc_qgorder',array('state'=>2,'dq_time'=>$dq_time,'pay_time'=>date('Y-m-d H:i:s',$time)),array('id'=>$qgorder['id']));
    }
    if($hyorder and $hyorder['state']==1){
        pdo_update('cjdc_hyorder',array('state'=>2,'day'=>date('d'),'time'=>date('Y-m-d H:i:s')),array('id'=>$hyorder['id']));
        pdo_update('cjdc_user',array('dq_time'=>date('Y-m-d',strtotime("+".$hyorder['month']." month")),'hy_day'=>date('d'),'user_name'=>$hyorder['user_name'],'user_tel'=>$hyorder['user_tel']),array('id'=>$hyorder['user_id'])); 
    }
    if($czorder and $czorder['state']==1){
        pdo_update('cjdc_czorder',array('state'=>2),array('id'=>$czorder['id']));
        $je=$res['total_fee']/100;
       $czhd = pdo_get('cjdc_czhd', array('uniacid' => $czorder['uniacid'],'full'=>$je));
       if($czhd['reduction']>0){
          pdo_update('cjdc_user', array('wallet +=' =>$je+$czhd['reduction']), array('id' => $czorder['user_id']));
          $cz['money'] = $je;
            $cz['user_id'] = $czorder['user_id'];
            $cz['type'] = 1;
            $cz['note'] = '在线充值';
            $cz['time'] = date('Y-m-d H:i:s');
           pdo_insert('cjdc_qbmx', $cz);
           $czzs['money'] = $czhd['reduction'];
            $czzs['user_id'] = $czorder['user_id'];
            $czzs['type'] = 1;
            $czzs['note'] = '充值赠送';
            $czzs['time'] = date('Y-m-d H:i:s');
           pdo_insert('cjdc_qbmx', $czzs);
       }else{
          pdo_update('cjdc_user', array('wallet +=' => $je), array('id' => $czorder['user_id']));
          $cz['money'] = $je;
            $cz['user_id'] = $czorder['user_id'];
            $cz['type'] = 1;
            $cz['note'] = '在线充值';
            $cz['time'] = date('Y-m-d H:i:s');
           pdo_insert('cjdc_qbmx', $cz);
       }
        // getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$czorder['uniacid']."&c=entry&a=wxapp&do=Recharge&m=zh_cjdianc&user_id=".$czorder['user_id']."&money=".$czorder['money']."&money2=".$czorder['money2']);//改变订单状态
        getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$czorder['uniacid']."&c=entry&a=wxapp&do=CzMessage&m=zh_cjdianc&order_id=".$czorder['id']);//改变订单状态
    }
    if($order['type']==1 and ($order['state']==1 || $order['state']==6)){//外卖
           getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=NewOrderMessage&m=zh_cjdianc&order_id=".$order['id']);//模板消息
        $store = pdo_get('cjdc_storeset', array('store_id' => $order['store_id']));
        $sys = pdo_get('cjdc_system', array('uniacid' => $order['uniacid']), 'ps_name');
        $ps_name = empty($sys['ps_name']) ? '超级跑腿' : $sys['ps_name'];
        if ($store['is_jd'] == 1) {
                $wmorder['state'] = 3;
                $wmorder['pay_time'] = date('Y-m-d H:i:s');
                $wmorder['jd_time'] = date('Y-m-d H:i:s');
                $res = pdo_update('cjdc_order', $wmorder, array('id' => $order['id']));
                  getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=" . $order['uniacid'] . "&c=entry&a=wxapp&do=jdnotice&m=zh_cjdianc&order_id=" . $order['id']);//达达
                
            } else {
                $wmorder['state'] = 2;
                $wmorder['pay_time'] = date('Y-m-d H:i:s');
                $res = pdo_update('cjdc_order', $wmorder, array('id' => $order['id']));
            }
            if ($store['is_jd'] == 1 && $order['order_type'] == 1) {//自动接单
                if ($store['ps_mode'] == '达达配送') {
                    getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=" . $order['uniacid'] . "&c=entry&a=wxapp&do=TestDada&m=zh_cjdianc&order_id=" . $order['id']);//达达
                }
                if ($store['ps_mode'] == '快服务配送') {
                    $res = getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=" . $order['uniacid'] . "&c=entry&a=wxapp&do=kfw&m=zh_cjdianc&order_id=" . $order['id']);//快服务
                    $kfw['ship_id'] = $res;
                    pdo_update('cjdc_order', $kfw, array('id' => $order['id']));
                }
                if ($store['ps_mode'] == $ps_name) {
                    getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=" . $order['uniacid'] . "&c=entry&a=wxapp&do=cjpt&m=zh_cjdianc&order_id=" . $order['id']);//跑腿
                }
            }
            pdo_update('cjdc_store', array('score +=' => 1), array('id' => $order['store_id']));
            $good = pdo_getall('cjdc_order_goods', array('order_id' => $order['id']));
            for ($i = 0; $i < count($good); $i++) {
                pdo_update('cjdc_goods', array('inventory -=' => $good[$i]['number']), array('id' => $good[$i]['dishes_id']));
                pdo_update('cjdc_goods', array('sales +=' => $good[$i]['number']), array('id' => $good[$i]['dishes_id']));
            }

     
        //getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=payorder&m=zh_cjdianc&order_id=".$order['id']);
        getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=Message&m=zh_cjdianc&order_id=".$order['id']);//模板消息
        getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=QtPrint&m=zh_cjdianc&order_id=".$order['id']);//打印机
        getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=HcPrint&m=zh_cjdianc&order_id=".$order['id']);//打印机
        getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=sms&m=zh_cjdianc&type=1&store_id=".$order['store_id']);//短信
            //分销佣金
    getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=JsCommission&m=zh_cjdianc&order_id=".$order['id']);
    }
    if($order['type']==2 and $order['dn_state']==1){//店内
        $dnorder['dn_state'] = 2;
        $dnorder['pay_time'] = date('Y-m-d H:i:s');
        $res = pdo_update('cjdc_order', $dnorder, array('id' => $order['id']));
        pdo_update('cjdc_store', array('score +=' => 1), array('id' => $order['store_id']));
        $good = pdo_getall('cjdc_order_goods', array('order_id' => $order['id']));
        for ($i = 0; $i < count($good); $i++) {
            pdo_update('cjdc_goods', array('inventory -=' => $good[$i]['number']), array('id' => $good[$i]['dishes_id']));
            pdo_update('cjdc_goods', array('sales +=' => $good[$i]['number']), array('id' => $good[$i]['dishes_id']));
        }
        //getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=payorder&m=zh_cjdianc&order_id=".$order['id']);
        getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=sms&m=zh_cjdianc&type=2&store_id=".$order['store_id']);//短信
        getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=QtPrint&m=zh_cjdianc&order_id=".$order['id']);//打印机
        getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=HcPrint&m=zh_cjdianc&order_id=".$order['id']);//打印机
        getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=addintegral&m=zh_cjdianc&type=2&order_id=".$order['id']);//短信
            //分销佣金
        getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=JsCommission&m=zh_cjdianc&order_id=".$order['id']);
    }
    if($order['type']==3 and $order['yy_state']==1){//预约
        pdo_update('cjdc_order',array('yy_state'=>2),array('code'=>$logno));
        getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=sms&m=zh_cjdianc&type=3&store_id=".$order['store_id']);//短信
            //分销佣金
    getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=JsCommission&m=zh_cjdianc&order_id=".$order['id']);
        getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=storeYyOrderMessage&m=zh_cjdianc&order_id=".$order['id']);//模板消息

    }
    if($order['type']==4 and $order['dm_state']==1){//当面付
        getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=NewDmOrderMessage&m=zh_cjdianc&order_id=".$order['id']);//模板消息
        $res=pdo_update('cjdc_order',array('dm_state'=>2),array('id'=>$order['id']));
        getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=sms&m=zh_cjdianc&type=2&store_id=".$order['store_id']);//短信
        getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=QtPrint&m=zh_cjdianc&order_id=".$order['id']);//打印机
        getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=addintegral&m=zh_cjdianc&type=5&order_id=".$order['id']);//短信
            //分销佣金
    getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$order['uniacid']."&c=entry&a=wxapp&do=JsCommission&m=zh_cjdianc&order_id=".$order['id']);

    }

    $store=pdo_get('cjdc_store',array('code'=>$logno));
        if($store['zf_state']==1){
              $res=pdo_update('cjdc_store',array('zf_state'=>2),array('id'=>$store['id']));
               getapi("https://".$_SERVER['HTTP_HOST'] . "/app/index.php?i=".$store['uniacid']."&c=entry&a=wxapp&do=SaveRzLog&m=zh_cjdianc&store_id=".$store['id']."&money=".$store['money']);

        }

           $result = array(
                'return_code' => 'SUCCESS',
                'return_msg' => 'OK'
            );
            echo array2xml($result);
            exit;
    }else{
        //订单已经处理过了
        $result = array(
            'return_code' => 'SUCCESS',
            'return_msg' => 'OK'
        );
        echo array2xml($result);
        exit;
    }