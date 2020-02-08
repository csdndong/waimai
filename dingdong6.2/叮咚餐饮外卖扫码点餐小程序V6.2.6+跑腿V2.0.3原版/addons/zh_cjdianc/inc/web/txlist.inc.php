<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$type=empty($_GPC['type']) ? 'wait' :$_GPC['type'];
$state=empty($_GPC['state']) ? '1' :$_GPC['state'];
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$where=' WHERE  a.uniacid=:uniacid';
$data[':uniacid']=$_W['uniacid'];
if(isset($_GPC['keywords'])){
    $op=$_GPC['keywords'];
    $where.=" and b.name LIKE  concat('%', :name,'%') ";    
    $data[':name']=$op;
    $type='all';
}
if($type!='all'){   
 $where.= " and a.state=$state"; 
}
  $sql="SELECT a.*,b.name as md_name,b.user_id,c.name as sk_name FROM ".tablename('cjdc_withdrawal') .  " a"  . " left join " . tablename("cjdc_store") . " b on a.store_id=b.id left join " . tablename("cjdc_user") . " c on c.id=b.user_id ". $where." ORDER BY a.time DESC";
  $total=pdo_fetchcolumn("SELECT count(*) FROM ".tablename('cjdc_withdrawal') .  " a"  . " left join " . tablename("cjdc_store") . " b on a.store_id=b.id".$where." ORDER BY a.time DESC",$data);

$list=pdo_fetchall($sql,$data);
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list=pdo_fetchall($select_sql,$data);
$pager = pagination($total, $pageindex, $pagesize);


if($operation=='adopt'){//审核通过
    $id=$_GPC['id'];
    $res=pdo_update('cjdc_withdrawal',array('state'=>2,'sh_time'=>date('Y-m-d H:i:s')),array('id'=>$id));  
    if($res){
        message('审核成功',$this->createWebUrl('txlist',array()),'success');
    }else{
        message('审核失败','','error');
    }
  
}


// if($operation=='adopt'){//审核通过

//     $id=$_GPC['id'];
//     $list=pdo_get('cjdc_withdrawal',array('id'=>$_GPC['id']));
//     $user=pdo_get('cjdc_user',array('id'=>$list['user_id']));
//     $res=pdo_update('cjdc_withdrawal',array('state'=>2,'sh_time'=>date('Y-m-d H:i:s')),array('id'=>$id));  
//     if($res){
//         message('审核成功',$this->createWebUrl('finance',array()),'success');
//     }else{
//         message('审核失败','','error');
//     }
  
// }


if($operation=='adopt2'){
    $id=$_GPC['id'];
    $list=pdo_get('cjdc_withdrawal',array('id'=>$_GPC['id']));
    $store=pdo_get('cjdc_store',array('id'=>$list['store_id']));
    $user=pdo_get('cjdc_user',array('id'=>$store['user_id']));
    if($list['state']==1){
pdo_update('cjdc_withdrawal',array('state'=>2,'sh_time'=>date('Y-m-d H:i:s')),array('id'=>$id));

////////////////打款//////////////////////
function arraytoxml($data){
        $str='<xml>';
        foreach($data as $k=>$v) {
            $str.='<'.$k.'>'.$v.'</'.$k.'>';
        }
        $str.='</xml>';
        return $str;
    }
    function xmltoarray($xml) { 
        //禁止引用外部xml实体 
        libxml_disable_entity_loader(true); 
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA); 
        $val = json_decode(json_encode($xmlstring),true); 
        return $val;
    } 
    function curl($param="",$url) {
        global $_GPC, $_W;
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();                                      //初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);                 //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);                    //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);            //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);                      //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);           // 增加 HTTP Header（头）里的字段 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);        // 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch,CURLOPT_SSLCERT,IA_ROOT . "/addons/zh_cjdianc/cert/".'apiclient_cert_' . $_W['uniacid'] . '.pem'); //这个是证书的位置绝对路径
        curl_setopt($ch,CURLOPT_SSLKEY,IA_ROOT . "/addons/zh_cjdianc/cert/".'apiclient_key_' . $_W['uniacid'] . '.pem'); //这个也是证书的位置绝对路径
        $data = curl_exec($ch);                                 //运行curl
        curl_close($ch);
        return $data;
    }  
    $system=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));
    $psystem=pdo_get('cjdc_pay',array('uniacid'=>$_W['uniacid']));
    $data=array(
        'mch_appid'=>$system['appid'],//商户账号appid
        'mchid'=>$psystem['mchid'],//商户号
        'nonce_str'=>mt_rand(11111111,99999999),//随机字符串
        'partner_trade_no'=>time().rand(11111,99999),//商户订单号
        'openid'=>$user['openid'],//用户openid
        'check_name'=>'NO_CHECK',//校验用户姓名选项,
        're_user_name'=>$list['name'],//收款用户姓名
        'amount'=>$list['sj_cost']*100,//金额
        'desc'=>'提现打款',//企业付款描述信息
        'spbill_create_ip'=>$psystem['ip'],//Ip地址
    );
  
    $key=$psystem['wxkey'];///这个就是个API密码。32位的。。随便MD5一下就可以了
   // $key=md5($key);
 	//var_dump($data);die;
    $data=array_filter($data);
    ksort($data);
    $str='';
    foreach($data as $k=>$v) {
        $str.=$k.'='.$v.'&';
    }
    $str.='key='.$key;
    $data['sign']=md5($str);

    $xml=arraytoxml($data);

    $url='https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
    $res=curl($xml,$url);

    $return=xmltoarray($res);
    if($return['result_code']=='SUCCESS'){
      message('审核成功',$this->createWebUrl('finance',array()),'success');
    }else{
        pdo_update('cjdc_withdrawal',array('state'=>1,'sh_time'=>0),array('id'=>$id));
        if($return['err_code_des']){
            $message=$return['err_code_des'];
        }else{
            $message='请检查证书是否上传正确!';
        }
      message($return['err_code_des'],'','error');
    }
    // print_r($return);
  }
////////////////打款//////////////////////

}



if($operation=='reject'){
     $id=$_GPC['id'];
    $res=pdo_update('cjdc_withdrawal',array('state'=>3,'sh_time'=>date('Y-m-d H:i:s')),array('id'=>$id));
     if($res){
        message('拒绝成功',$this->createWebUrl('txlist',array()),'success');
    }else{
        message('拒绝失败','','error');
    }
}
if($operation=='delete'){
     $id=$_GPC['id'];
     $res=pdo_delete('cjdc_withdrawal',array('id'=>$id));
     if($res){
        message('删除成功',$this->createWebUrl('txlist',array()),'success');
    }else{
        message('删除失败','','error');
    }

}

include $this->template('web/txlist');