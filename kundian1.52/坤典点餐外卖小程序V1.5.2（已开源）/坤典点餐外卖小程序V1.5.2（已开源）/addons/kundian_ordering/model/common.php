<?php
/**
 * Created by PhpStorm.
 * User: zyl
 * Date: 2018/10/21
 * Time: 9:52
 */
defined("IN_IA") or exit("Access Denied");
class Common_KundianOrderingModel{

    /**
     * 获取关于我们的信息
     * @param $uniacid
     * @return bool
     */
    public function getAboutData($uniacid){
        $list=pdo_get('cqkundian_ordering_about',array('uniacid'=>$uniacid));
        return $list;
    }

    /**
     * 获取配置信息
     * @param $field
     * @param $uniacid
     * @return array
     */
    public function getSetData($field,$uniacid){
        $cond=array(
            'ikey'=>$field,
            'uniacid'=>$uniacid,
        );
        $list=pdo_getall('cqkundian_ordering_set',$cond);
        $setData=array();
        foreach ($list as $key=>$v){
            $setData[$v['ikey']]=$v['value'];
        }
        return $setData;
    }

    /**
     * 更新导航信息
     * @param $data
     * @param array $cond
     * @return bool
     */
    public function updateNavData($data,$cond=array()){
        if(!empty($cond)){
            $res=pdo_update('cqkundian_ordering_nav',$data,$cond);
        }else{
            $res=pdo_insert('cqkundian_ordering_nav',$data);
        }
        return $res;
    }

    /**
     * 获取导航列表信息
     * @param $cond
     * @param $mutilple 是否查询多条数据
     * @param string $order_by
     * @return array
     */
    public function getNavList($cond,$mutilple=true,$order_by='rank asc'){
        if($mutilple){
            $list=pdo_getall('cqkundian_ordering_nav',$cond,'','',$order_by);
        }else{
            $list=pdo_get('cqkundian_ordering_nav',$cond);
        }
        return $list;
    }

    /**
     * 删除导航信息
     * @param $id
     * @param $uniacid
     * @return bool
     */
    public function deleteNav($id,$uniacid){
        $res=pdo_delete('cqkundian_ordering_nav',array('uniacid'=>$uniacid,'id'=>$id));
        return $res;
    }


    /**
     * 阿里大于短信
     * @param $msgConfig
     * @param $order_number
     * @return bool
     */
    public function sendAliYunMsg($msgConfig,$order_number){
        include ROOT_KUNDIAN_ORDERING."inc/wxapp/alidayu/top/TopClient.php";
        include ROOT_KUNDIAN_ORDERING."inc/wxapp/alidayu/top/request/AlibabaAliqinFcSmsNumSendRequest.php";
        include ROOT_KUNDIAN_ORDERING."inc/wxapp/alidayu/top/ResultSet.php";
        include ROOT_KUNDIAN_ORDERING."inc/wxapp/alidayu/top/RequestCheckUtil.php";
        include ROOT_KUNDIAN_ORDERING."inc/wxapp/alidayu/top/TopLogger.php";
        $c = new TopClient();
        $c->appkey = $msgConfig['appkey'];
        $c->secretKey = $msgConfig['secret'];
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName($msgConfig['sign_name']);
        $req->setSmsParam("{orderno:'$order_number'}");
        $req->setRecNum($msgConfig['phone']);
        $req->setSmsTemplateCode($msgConfig['template_code']);
        $resp = $c->execute($req);
        $result=$resp->result;
        if($result->err_code==0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 打印订单信息
     * @param $orderData
     * @param $uniacid
     * @return string|void
     */
    public function neatenPrintInfo($orderData,$uniacid){
        $orderDetail=pdo_getall("cqkundian_ordering_order_detail",array('uniacid'=>$uniacid,'order_id'=>$orderData['id']));
        $totalPrice = 0;
        $printInfo='';
        for ($i = 0; $i < count($orderDetail); $i++) {
            $productData = pdo_get("cqkundian_ordering_product", array('uniacid' => $uniacid, 'id' => $orderDetail[$i]['pid']));
            $orderDetail[$i]['product_name'] = $productData['product_name'];
            $orderDetail[$i]['price'] = $productData['price'];
            $totalPrice += $orderDetail[$i]['price'] * $orderDetail[$i]['num'];

            $product_name = mb_substr($orderDetail[$i]['product_name'], 0, 7, "utf-8");
            if (strlen($product_name) / 3 < 7) {
                for ($j = 0; $j < 7 - strlen($product_name) / 3; $j++) {
                    $product_name .= "　";
                }
            }
            $printInfo .= '<RIGHT>'.$product_name . '　' . $orderDetail[$i]['price'] . '   ' . $orderDetail[$i]['num'] . '   ' . $orderDetail[$i]['total_price'] . '</RIGHT><BR>';
        }
        if(isset($orderData['is_fast_food'])&&$orderData['is_fast_food']){
            $res=$this->printFastfoodOrder($orderData,$totalPrice,$printInfo,$uniacid);
            return $res;
//            return $this->printFastfoodOrder($orderData,$totalPrice,$printInfo,$uniacid);
        }else{
            $res=$this->printOrder($orderData,$totalPrice,$printInfo,$uniacid);
            return $res;
        }

    }

    /**
     * 打印订单信息
     * @param $orderData
     * @param $totalPrice
     * @param $printInfo
     * @param $uniacid
     * @return string
     */
    public function printOrder($orderData,$totalPrice,$printInfo,$uniacid){
        $list=$this->getSetData(['print_sn','print_count'],$uniacid);
        $msgConfig=pdo_get("cqkundian_ordering_msg_config",array('uniacid'=>$uniacid));
        require_once ROOT_KUNDIAN_ORDERING.'inc/wxapp/print/HttpClient.class.php';
        define('USER', $msgConfig['user']);    //*必填*：飞鹅云后台注册账号
        define('UKEY', $msgConfig['ukey']);    //*必填*: 飞鹅云注册账号后生成的UKEY
        define('SN', $msgConfig['sn']);        //*必填*：打印机编号，必须要在管理后台里添加打印机或调用API接口添加之后，才能调用API
        //以下参数不需要修改
        define('IP', 'api.feieyun.cn');            //接口IP或域名
        define('PORT', 80);                        //接口IP端口
        define('PATH', '/Api/Open/');        //接口路径
        define('STIME', time());                //公共参数，请求时间
        define('SIG', sha1(USER . UKEY . STIME));   //公共参数，请求公钥

        $orderInfo = '<CB>外卖订餐</CB><BR>';
        $orderInfo .= '名称　　　　　 单价 数量 金额<BR>';
        $orderInfo .= '--------------------------------<BR>';
        $orderInfo.=$printInfo;
        $orderInfo .= '备注：' . $orderData['remark'] . '<BR>';
        $orderInfo .= '--------------------------------<BR>';
        $orderInfo .= '合计：' . $totalPrice . '元<BR>';
        if($orderData['is_pay']==-1){
            $orderInfo .= '支付方式：货到付款<BR>';
        }elseif($orderData['is_pay']){
            $orderInfo .= '支付方式：微信支付<BR>';
        }

        $orderInfo .= '收货人：' . $orderData['name'] . '<BR>';
        $orderInfo .= '送货地点：' . $orderData['address'] . '<BR>';
        $orderInfo .= '联系电话：' . $orderData['phone'] . '<BR>';
        $orderInfo .= '订餐时间：' . date("Y-m-d H:i:s", $orderData['create_time']) . '<BR>';
        return $this->wp_print(SN, $orderInfo, $list['print_count']);
    }


    public function printFastfoodOrder($orderData,$totalPrice,$printInfo,$uniacid){
        $msgConfig=pdo_get("cqkundian_ordering_msg_config",array('uniacid'=>$uniacid));
        $list=$this->getSetData(['print_sn','print_count'],$uniacid);
        $hasPackage = '';
        $str='';
        if($orderData['package_price']){
            $totalPrice+=$orderData['package_price'];
            $hasPackage = '<CB>(打 包)</CB><BR>';
            $str .= '<BR><RIGHT>打包费：' . $orderData['package_price'] . '元</RIGHT>';
        }
        require_once ROOT_KUNDIAN_ORDERING. 'inc/web/print/HttpClient.class.php';
        define('USER', $msgConfig['user']);    //*必填*：飞鹅云后台注册账号
        define('UKEY', $msgConfig['ukey']);    //*必填*: 飞鹅云注册账号后生成的UKEY
        define('SN', $msgConfig['sn']);        //*必填*：打印机编号，必须要在管理后台里添加打印机或调用API接口添加之后，才能调用API
        //以下参数不需要修改
        define('IP', 'api.feieyun.cn');            //接口IP或域名
        define('PORT', 80);                        //接口IP端口
        define('PATH', '/Api/Open/');        //接口路径
        define('STIME', time());                //公共参数，请求时间
        define('SIG', sha1(USER . UKEY . STIME));   //公共参数，请求公钥

        $MON = $this->makeOrderNumber($uniacid);
        $title = '<CB>#取餐号：' . $MON . '</CB><BR>'.$hasPackage;
        //$str .= '<BR><RIGHT><BOLD>[' . $deskData['name'] . ']</BOLD></RIGHT>';
        $str .= '<BR><RIGHT>合计：' . $totalPrice . '元</RIGHT><BR>';
        $str .= '<RIGHT>'.$orderData['pay_method'].'</RIGHT>';
        $str .= '<BR><RIGHT>时间：' . date('Y-m-d H:i:s') . '</RIGHT><BR>';
        $end = '<C>流水号：' . $this->makeSnOrder() . '</C>';

        $orderInfo = $title . '<BR>';
        $orderInfo .= '名称　　　　　 单价 数量 金额<BR>';
        $orderInfo .= '--------------------------------<BR>';
        $orderInfo .= $printInfo;
        $orderInfo .= $str;
        $orderInfo .= '--------------------------------<BR>' . $end;
        $res = $this->wp_print(SN, $orderInfo, $list['print_count']);
        pdo_update('cqkundian_ordering_order',['fast_food_number'=>$MON],['id'=>$orderData['id']]);
        return $res;
    }

    /**
     * 打印
     * @param $printer_sn
     * @param $orderInfo
     * @param $times
     * @return string
     */
    public function wp_print($printer_sn,$orderInfo,$times){
        $content = array(
            'user'=>USER,
            'stime'=>STIME,
            'sig'=>SIG,
            'apiname'=>'Open_printMsg',

            'sn'=>$printer_sn,
            'content'=>$orderInfo,
            'times'=>$times//打印次数
        );
        $client = new HttpClient(IP,PORT);
        if(!$client->post(PATH,$content)){
            return 'error';
        }else{
            //服务器返回的JSON字符串，建议要当做日志记录起来
            return $client->getContent();
        }
    }

    public function makeSnOrder(){
        return 'NO.' . sprintf('%05d', mt_rand(0, 99999)) . date('Ymd') . sprintf('%03d', mt_rand(0, 999));
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
}