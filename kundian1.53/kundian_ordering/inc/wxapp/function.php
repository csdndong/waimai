<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/2 0002
 * Time: 11:13
 */

/**
 * 取消订单模板消息推送
 * @param $touser       //要发送的用户
 * @param $template_id  //模板消息id
 * @param $orderData    //订单信息
 * @param $appid        //微信公众号appid
 * @param $secret       //微信公众号密钥
 * @return bool         //返回值
 */
function send_template_cancel_message($touser,$template_id,$orderData,$appid,$secret,$uniacid){
    $access_token = get_Wx_accessToken($appid,$secret,$uniacid);
    $status='';
    if($orderData['is_change']==2){
        $status='货到付款';
    }elseif ($orderData['is_pay']==0){
        $status='未支付';
    }elseif ($orderData['is_pay']==1){
        $status='已支付';
    }
    $cancel_data=array(
        'first'=>array('value'=>'订单取消通知'),
        'keyword1'=>array("value"=>$orderData['order_number']),
        'keyword2'=>array("value"=>$orderData['price'].'元 （支付状态：'.$status.'）'),
        'keyword3'=>array("value"=>date("Y-m-d H:i:s",time())),
        'remark'=>array("value"=>'配送地址：'.$orderData['address']),
    );

    $cancel_template = array(
        'touser' => $touser,
        'template_id' => $template_id,
        'data' => $cancel_data,
        "miniprogram"=>array(
            "appid"=>'',
            "pagepath"=>""
        )
    );
    $json_template = json_encode($cancel_template);
    $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;
    $dataRes = http_request($url, urldecode($json_template));
    if ($dataRes->errcode == 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * 获取access_token
 * @param $appid        //微信公众号appid
 * @param $secret       //微信公众号密钥
 * @param $uniacid      //小程序唯一标识
 * @return array|bool|Memcache|mixed|Redis|string
 */
function get_Wx_accessToken($appid,$secret,$uniacid){
    if(cache_load('ordering_access_token_time_'.$uniacid > time())){
        return cache_load('ordering_access_token_'.$uniacid);
    }else{
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
        $result = http_request($url);
        $res = json_decode($result,true);
        if($res){
            cache_write('ordering_access_token_'.$uniacid,$res['access_token']);
            cache_write('ordering_access_token_time_'.$uniacid,time()+7000);
            return $res['access_token'];
        }else{
            return 'api return error';
        }
    }
}
function http_request($url,$data=array()){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    // POST数据
    curl_setopt($ch, CURLOPT_POST, 1);
    // 把post的变量加上
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

/**
 * 给商家发送模板消息
 * @param $touser       //接收模板消息的用户openid
 * @param $orderData    //订单信息
 * @param $uniacid      //小程序唯一id
 * @return bool         //返回值
 */
function send_template_message($touser,$orderData,$uniacid){
    $msgConfig=pdo_get("cqkundian_ordering_msg_config",array('uniacid'=>$uniacid));
    $setting = uni_setting($uniacid, array('payment'));
    $wechat = $setting['payment']['wechat'];
    $sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wxapp') . ' WHERE `acid`=:acid';
    $row = pdo_fetch($sql, array(':acid' => $wechat['account']));

    $access_token = get_Wx_accessToken($msgConfig['wx_appid'],$msgConfig['wx_secret'],$uniacid);
    if($orderData['is_pay']==-1){
        $remark='货到付款';
    }else{
        $remark="订单已支付";
    }
    $data=array(
        'first'=>array('value'=>'您有新的订单',"color"=>"#FF4500"),
        'keyword1'=>array("value"=>$orderData['order_number']),
        'keyword2'=>array("value"=>$orderData['price']),
        'keyword3'=>array("value"=>$orderData['pei_time']),
        'keyword4'=>array("value"=>$orderData['phone']),
        'keyword5'=>array("value"=>$orderData['address']),
        'remark'=>array('value'=>$remark)
    );
    $template = array(
        'touser' => $touser,
        'template_id' => $msgConfig['wx_template_id'],
        'data' => $data,
        "miniprogram"=>array(
            "appid"=>$row['appid'],
            "pagepath"=>"kundian_ordering/pages/index/index/index"
        )
    );
    $json_template = json_encode($template);
    $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;
    $dataRes = http_request($url, urldecode($json_template));
    if ($dataRes->errcode == 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * 用户支付订单发送模板消息
 * @param $orderData
 * @param $prepay_id
 * @param $touser
 * @param $uniacid
 */
function send_msg_to_user($orderData,$prepay_id,$touser,$uniacid){
    $msgConfig=pdo_get("cqkundian_ordering_msg_config",array('uniacid'=>$uniacid));
    $account_api = WeAccount::create();
    $access_token=$account_api->getAccessToken();

    $shopData=pdo_get('cqkundian_ordering_about',array('uniacid'=>$uniacid));
    $value = array(
        "keyword1"=>array(
            "value"=>$shopData['merchant_name'].'消费',
            "color"=>"#4a4a4a"
        ),
        "keyword2"=>array(
            "value"=>date("Y-m-d H:i:s",$orderData['create_time']),
            "color"=>"#9b9b9b"
        ),
        "keyword3"=>array(
            "value"=>$orderData['price'],
            "color"=>"#9b9b9b"
        ),
    );
    $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
    $dd = array();
    $dd['touser']=$touser;
    $dd['template_id']=$msgConfig['wx_small_template_id'];
    $dd['page']='kundian_ordering/pages/order/index/index';
    $dd['form_id']=$prepay_id;
    $dd['data']=$value;
    $dd['color']='';
    $dd['emphasis_keyword']='';    //模板需要放大的关键词，不填则默认无放大
    /* curl_post()进行POST方式调用api： api.weixin.qq.com*/
    $result = https_curl_json($url,$dd,'json');
    if($result){
        echo json_encode(array('state'=>1,'msg'=>$result));
    }else{
        echo json_encode(array('state'=>2,'msg'=>$result));
    }
}

/**
 * 获取小程序的access_token
 * @param $appid    //小程序appid
 * @param $secret   //小程序密钥
 * @param $uniacid  //小程序唯一标识
 * @return array|bool|Memcache|mixed|Redis|string
 */
function get_accessToken($appid,$secret,$uniacid){
    if(cache_load('kundian_ordering_access_token_miniapp_time'.$uniacid)){
        return cache_load('kundian_ordering_access_token_miniapp'.$uniacid);
    } else{
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
        $result = http_request($url);
        $res = json_decode($result,true);
        if($res){
            cache_write('kundian_ordering_access_token_miniapp'.$uniacid,time()+7000);
            cache_write('kundian_ordering_access_token_miniapp_time'.$uniacid,$res['access_token']);
            return $res['access_token'];
        }else{
            return 'api return error';
        }
    }
}

/* 发送json格式的数据，到api接口 -xzz0704  */
function https_curl_json($url,$data,$type){
    if($type=='json'){//json $_POST=json_decode(file_get_contents('php://input'), TRUE);
        $headers = array("Content-type: application/json;charset=UTF-8","Accept: application/json","Cache-Control: no-cache", "Pragma: no-cache");
        $data=json_encode($data);
    }
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers );
    $output = curl_exec($curl);
    if (curl_errno($curl)) {
        echo 'Errno'.curl_error($curl);//捕抓异常
    }
    curl_close($curl);
    return $output;
}

/**
 * 给商家发送预约模板消息
 * @param $touser       //接收消息的openid
 * @param $order_id     //订单编号
 * @param $uniacid      //小程序唯一标识
 * @return bool
 */
function send_make_order_message($touser,$order_id,$uniacid){
    $msgConfig=pdo_get("cqkundian_ordering_msg_config",array('uniacid'=>$uniacid));
    $setting = uni_setting($uniacid, array('payment'));
    $wechat = $setting['payment']['wechat'];
    $sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wxapp') . ' WHERE `acid`=:acid';
    $row = pdo_fetch($sql, array(':acid' => $wechat['account']));

    $access_token = get_Wx_accessToken($msgConfig['wx_appid'],$msgConfig['wx_secret'],$uniacid);
    $orderData=pdo_get('cqkundian_ordering_make_order',array('id'=>$order_id,'uniacid'=>$uniacid));
    $data=array(
        'first'=>array('value'=>'有新的客户预约,请及时确认！'),
        'keyword1'=>array("value"=>$orderData['name']),
        'keyword2'=>array("value"=>$orderData['phone']),
        'keyword3'=>array("value"=>$orderData['use_date'] .$orderData['use_time']),
        'keyword4'=>array("value"=>'预约订餐'),
    );
    $template = array(
        'touser' => $touser,
        'template_id' => $msgConfig['wx_order_template_id'],
        'data' => $data,
        "miniprogram"=>array(
            "appid"=>$row['appid'],
            "pagepath"=>"kundian_ordering/pages/index/index/index"
        )
    );
    $json_template = json_encode($template);
    $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;
    $dataRes = http_request($url, urldecode($json_template));
    if ($dataRes->errcode == 0) {
        return true;
    } else {
        return false;
    }
}

//回调日志
function log_notify($content){
    if(is_array($content)){
        $content = json_encode($content,JSON_UNESCAPED_UNICODE);
    }
    $table = 'cqkundian_ordering_notify_log';
    $exts = pdo_tableexists('cqkundian_ordering_notify_log');
    if(!$exts){
        $sql = "CREATE TABLE $table ( `id` INT NOT NULL AUTO_INCREMENT";
        $sql .= ' , `content` TEXT NULL DEFAULT NULL , `status` TINYINT NOT NULL DEFAULT 1 ,';
        $sql .= '`create_time` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB; ';
        pdo_query($sql);
    }
    if(is_string($content)){
        pdo_insert($table,[
            'content'=>$content,
            'create_time'=>date('Y-m-d H:i:s'),
            'status'=> -1
        ]);
        return true;
    }
    return false;
}
