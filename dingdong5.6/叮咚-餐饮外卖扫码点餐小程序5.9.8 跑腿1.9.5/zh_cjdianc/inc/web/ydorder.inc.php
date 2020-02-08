<?php
global $_GPC, $_W;
load()->func('tpl');
$GLOBALS['frames'] = $this->getMainMenu($storeid,$action);
$where=" where a.uniacid=:uniacid and a.type=3 and a.yy_state!=1";
$data[':uniacid']=$_W['uniacid'];
if(!empty($_GPC['keywords'])){
  $where.=" and (a.name LIKE  concat('%', :name,'%') || a.order_num LIKE  concat('%', :name,'%') || b.name LIKE  concat('%', :name,'%'))";
  $data[':name']=$_GPC['keywords'];
}
if($_GPC['time']){
  $start=strtotime($_GPC['time']['start']);
  $end=strtotime($_GPC['time']['end']);
  $where.=" and UNIX_TIMESTAMP(a.time) >={$start} and UNIX_TIMESTAMP(a.time)<={$end}";
}
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$type=isset($_GPC['type'])?$_GPC['type']:'all';
if($type=='wait'){
  $where.=" and a.yy_state=2";
}
if($type=='complete'){
  $where.=" and a.yy_state=3";
}
if($type=='cancel'){
  $where.=" and a.yy_state=4";
}
$sql="SELECT a.*,b.name as md_name,c.yd_poundage as md_poundage,d.poundage FROM ".tablename('cjdc_order'). " a"  . " left join " . tablename("cjdc_store") . " b on a.store_id=b.id " . " left join " . tablename("cjdc_storetype") . " c on b.md_type=c.id ". " left join " . tablename("cjdc_storeset") . " d on b.id=d.store_id ".$where." ORDER BY a.id DESC";
$total=pdo_fetchcolumn("SELECT count(*) FROM ".tablename('cjdc_order'). " a"  . " left join " . tablename("cjdc_store") . " b on a.store_id=b.id  " . " left join " . tablename("cjdc_storetype") . " c on b.md_type=c.id ". " left join " . tablename("cjdc_storeset") . " d on b.id=d.store_id ".$where." ORDER BY a.id DESC",$data);
$select_sql =$sql."  LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list=pdo_fetchall($select_sql,$data);
$pager = pagination($total, $pageindex, $pagesize);
if($_GPC['op']=='ok'){
  pdo_delete('cjdc_formid',array('time <='=>time()-60*60*24*7));
  $data2['yy_state']=3;
  // $data2['completion_time']=time();
  $rst=pdo_update('cjdc_order',$data2,array('id'=>$_GPC['id']));
  if($rst){
    file_get_contents("".$_W['siteroot']."app/index.php?i=".$_W['uniacid']."&c=entry&a=wxapp&do=addintegral&m=zh_cjdianc&type=4&order_id=".$_GPC['id']);
    //有效分销佣金
    $this->updcommission($_GPC['id']);
    ///////////////模板消息通过///////////////////
    function getaccess_token($_W){
      $res=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));
      $appid=$res['appid'];
      $secret=$res['appsecret'];
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret."";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,$url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
      $data = curl_exec($ch);
      curl_close($ch);
      $data = json_decode($data,true);
      return $data['access_token'];
    }
    //设置与发送模板信息
    function set_msg($_W){
      $access_token = getaccess_token($_W);
      $res=pdo_get('cjdc_message',array('uniacid'=>$_W['uniacid']));
      $res2=pdo_get('cjdc_order',array('id'=>$_GET['id']));
      $user=pdo_get('cjdc_user',array('id'=>$res2['user_id']));
      $store=pdo_get('cjdc_store',array('id'=>$res2['store_id']));
      $table=pdo_get('cjdc_table_type',array('id'=>$res2['table_id']));
      $form=pdo_get('cjdc_formid',array('user_id'=>$res2['user_id'],'time >='=>time()-60*60*24*7));
      $formwork ='{
           "touser": "'.$user["openid"].'",
           "template_id": "'.$res["yy_tid"].'",
           "page": "zh_cjdianc/pages/Liar/loginindex",
           "form_id":"'.$form['form_id'].'",
           "data": {
             "keyword1": {
               "value": "'.$store['name'].'",
               "color": "#173177"
             },
             "keyword2": {
               "value":"'.$res2['order_num'].'",
               "color": "#173177"
             },
             "keyword3": {
               "value": "'.$res2['tel'].'",
               "color": "#173177"
             },
             "keyword4": {
               "value":  "'.$res2['tableware'].'",
               "color": "#173177"
             },
             "keyword5": {
               "value": "'.$table['name'].'",
               "color": "#173177"
             },
             "keyword6": {
               "value": "'.$res2['time'].'",
               "color": "#173177"
             },
             "keyword7": {
               "value": "'.$res2['money'].'",
               "color": "#173177"
             },
              "keyword8": {
               "value": "'.$res2['delivery_time'].'",
               "color": "#173177"
             },
              "keyword9": {
               "value": "预约通过,请在规定时间前往就餐~",
               "color": "#173177"
             }
           }
         }';
      // $formwork=$data;
      $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$access_token."";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,$url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
      curl_setopt($ch, CURLOPT_POST,1);
      curl_setopt($ch, CURLOPT_POSTFIELDS,$formwork);
      $data = curl_exec($ch);
      curl_close($ch);
      // return $data;
      pdo_delete('cjdc_formid',array('id'=>$form['id']));
    }
    echo set_msg($_W);
    ///////////////模板消息///////////////////
    message('确认成功',$this->createWebUrl('ydorder',array()),'success');
  } else{
    message('确认失败','','error');
  }
}
if($_GPC['op']=='delete'){
  $res=pdo_delete('cjdc_order',array('id'=>$_GPC['id']));
  if($res){
    message('删除成功！', $this->createWebUrl('ydorder'), 'success');
  } else{
    message('删除失败！','','error');
  }
}
if($_GPC['op']=='tg'){
  pdo_delete('cjdc_formid',array('time <='=>time()-60*60*24*7));
  $this->invalidcommission($_GPC['id']);
  $order=pdo_get('cjdc_order',array('id'=>$_GPC['id']));
  if($order['money']!=0 and ($order['pay_type']==1 || $order['pay_type']==2)){
    //金额大于零且微信支付
    if($order['pay_type']==1){
      $result=$this->wxrefund($_GPC['id']);
    }
    if($order['pay_type']==2){
      pdo_update('cjdc_user', array('wallet +=' => $order['money']), array('id' => $order['user_id']));
      $tk['money'] = $order['money'];
      $tk['user_id'] = $order['user_id'];
      $tk['type'] = 1;
      $tk['note'] = '订单拒绝';
      $tk['time'] = date('Y-m-d H:i:s');
      $tkres = pdo_insert('cjdc_qbmx', $tk);
    }
    if ($result['result_code'] == 'SUCCESS' || $tkres) {
      //退款成功
      ///////////////模板消息拒绝///////////////////
      function getaccess_token($_W){
        $res=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));
        $appid=$res['appid'];
        $secret=$res['appsecret'];
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret."";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data,true);
        return $data['access_token'];
      }
      //设置与发送模板信息
      function set_msg($_W){
        $access_token = getaccess_token($_W);
        $res=pdo_get('cjdc_message',array('uniacid'=>$_W['uniacid']));
        $res2=pdo_get('cjdc_order',array('id'=>$_GET['id']));
        $user=pdo_get('cjdc_user',array('id'=>$res2['user_id']));
        $store=pdo_get('cjdc_store',array('id'=>$res2['store_id']));
        $form=pdo_get('cjdc_formid',array('user_id'=>$res2['user_id'],'time >='=>time()-60*60*24*7));
        $formwork ='{
           "touser": "'.$user["openid"].'",
           "template_id": "'.$res["jj_tid"].'",
           "page": "zh_cjdianc/pages/Liar/loginindex",
           "form_id":"'.$form['form_id'].'",
           "data": {
             "keyword1": {
               "value": "'.$res2['order_num'].'",
               "color": "#173177"
             },
             "keyword2": {
               "value":"'.date("Y-m-d H:i:s").'",
               "color": "#173177"
             },
             "keyword3": {
               "value": "非常抱歉,商家暂时无法接单哦",
               "color": "#173177"
             },
             "keyword4": {
               "value":  "'.$store['name'].'",
               "color": "#173177"
             },
             "keyword5": {
               "value": "'.$store['tel'].'",
               "color": "#173177"
             },
             "keyword6": {
               "value": "'.$res2['money'].'",
               "color": "#173177"
             },
             "keyword7": {
               "value": "退款将尽快送达您的账户，请耐心等待...",
               "color": "#173177"
             }
           }
         }';
        // $formwork=$data;
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$access_token."";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$formwork);
        $data = curl_exec($ch);
        curl_close($ch);
        // return $data;
        pdo_delete('cjdc_formid',array('id'=>$form['id']));
      }
      echo set_msg($_W);
      ///////////////模板消息///////////////////
      ///
      ///////////////模板消息退款///////////////////
      function getaccess_token2($_W){
        $res=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));
        $appid=$res['appid'];
        $secret=$res['appsecret'];
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret."";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data,true);
        return $data['access_token'];
      }
      //设置与发送模板信息
      function set_msg2($_W){
        $access_token = getaccess_token2($_W);
        $res=pdo_get('cjdc_message',array('uniacid'=>$_W['uniacid']));
        $res2=pdo_get('cjdc_order',array('id'=>$_GET['id']));
        if($res2['pay_type']==1){
          $note='微信钱包';
        } elseif($res2['pay_type']==2){
          $note='余额钱包';
        }
        $user=pdo_get('cjdc_user',array('id'=>$res2['user_id']));
        $store=pdo_get('cjdc_store',array('id'=>$res2['store_id']));
        $form=pdo_get('cjdc_formid',array('user_id'=>$res2['user_id'],'time >='=>time()-60*60*24*7));
        $formwork ='{
           "touser": "'.$user["openid"].'",
           "template_id": "'.$res["tk_tid"].'",
           "page": "zh_cjdianc/pages/Liar/loginindex",
           "form_id":"'.$form['form_id'].'",
           "data": {
             "keyword1": {
               "value": "'.$res2['order_num'].'",
               "color": "#173177"
             },
             "keyword2": {
               "value":"'.$store['name'].'",
               "color": "#173177"
             },
             "keyword3": {
               "value": "'.$res2['money'].'",
               "color": "#173177"
             },
             "keyword4": {
               "value":  "'.$note.'",
               "color": "#173177"
             },
             "keyword5": {
               "value": "'.date("Y-m-d H:i:s").'",
               "color": "#173177"
             }
           }
         }';
        // $formwork=$data;
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$access_token."";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$formwork);
        $data = curl_exec($ch);
        curl_close($ch);
        // return $data;
        pdo_delete('cjdc_formid',array('id'=>$form['id']));
      }
      echo set_msg2($_W);
      ///////////////模板消息///////////////////
      pdo_update('cjdc_order',array('yy_state'=>4),array('id'=>$_GPC['id']));
      message('拒绝成功',$this->createWebUrl('ydorder',array()),'success');
    } else{
      message($result['err_code_des'],'','error');
    }
  } else{
    $res=pdo_update('cjdc_order',array('yy_state'=>4),array('id'=>$_GPC['id']));
    if($res){
      ///////////////模板消息拒绝///////////////////
      function getaccess_token($_W){
        $res=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));
        $appid=$res['appid'];
        $secret=$res['appsecret'];
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret."";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data,true);
        return $data['access_token'];
      }
      //设置与发送模板信息
      function set_msg($_W){
        $access_token = getaccess_token($_W);
        $res=pdo_get('cjdc_message',array('uniacid'=>$_W['uniacid']));
        $res2=pdo_get('cjdc_order',array('id'=>$_GET['id']));
        $user=pdo_get('cjdc_user',array('id'=>$res2['user_id']));
        $store=pdo_get('cjdc_store',array('id'=>$res2['store_id']));
        $form=pdo_get('cjdc_formid',array('user_id'=>$res2['user_id'],'time >='=>time()-60*60*24*7));
        $formwork ='{
           "touser": "'.$user["openid"].'",
           "template_id": "'.$res["jj_tid"].'",
           "page": "zh_cjdianc/pages/Liar/loginindex",
           "form_id":"'.$form['form_id'].'",
           "data": {
             "keyword1": {
               "value": "'.$res2['order_num'].'",
               "color": "#173177"
             },
             "keyword2": {
               "value":"'.date("Y-m-d H:i:s").'",
               "color": "#173177"
             },
             "keyword3": {
               "value": "非常抱歉,商家暂时无法接单哦",
               "color": "#173177"
             },
             "keyword4": {
               "value":  "'.$store['name'].'",
               "color": "#173177"
             },
             "keyword5": {
               "value": "'.$store['tel'].'",
               "color": "#173177"
             },
             "keyword6": {
               "value": "'.$res2['money'].'",
               "color": "#173177"
             },
             "keyword7": {
               "value": "退款将尽快送达您的账户，请耐心等待...",
               "color": "#173177"
             }
           }
         }';
        // $formwork=$data;
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$access_token."";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$formwork);
        $data = curl_exec($ch);
        curl_close($ch);
        // return $data;
        pdo_delete('cjdc_formid',array('id'=>$form['id']));
      }
      echo set_msg($_W);
      ///////////////模板消息///////////////////
      ///
      ///////////////模板消息///////////////////
      message('拒绝成功',$this->createWebUrl('ydorder',array()),'success');
    } else{
      message('拒绝失败','','error');
    }
  }
}





if(checksubmit('export_submit', true)) {
  $time=date("Y-m-d");
  $time="'%$time%'";
   $start=$_GPC['time']['start'];
  $end=$_GPC['time']['end'];
  $count = pdo_fetchcolumn("SELECT COUNT(*) FROM". tablename("cjdc_order")." WHERE uniacid={$_W['uniacid']} and type=3 and yy_state!=1 and time >='{$start}' and time<='{$end}'");
  $pagesize = ceil($count/5000);
        //array_unshift( $names,  '活动名称'); 

  $header = array(
    'item'=>'序号',
    'md_name' => '门店名称',
    'order_num' => '订单号', 
    'time' => '下单时间',
    'money' => '金额',
    'yy_state' => '订单状态',
    'pay_type' => '支付方式',
    'table_name' => '桌子类型',
    'goods' => '商品'

    );

  $keys = array_keys($header);
  $html = "\xEF\xBB\xBF";
  foreach ($header as $li) {
    $html .= $li . "\t ,";
  }
  $html .= "\n";
  for ($j = 1; $j <= $pagesize; $j++) {
    $sql = "select a.*,b.name as md_name,c.name as table_name from " . tablename("cjdc_order")."  a"  . " inner join " . tablename("cjdc_store")." b on a.store_id=b.id inner join " . tablename("cjdc_table_type")." c on a.table_id=c.id WHERE  a.uniacid={$_W['uniacid']} and a.type=3 and yy_state!=1 and a.time >='{$start}' and a.time<='{$end}' limit " . ($j - 1) * 5000 . ",5000 ";
    $list = pdo_fetchall($sql);            
  }
  if (!empty($list)) {
    $size = ceil(count($list) / 500);
    for ($i = 0; $i < $size; $i++) {
      $buffer = array_slice($list, $i * 500, 500);
      $user = array();
      foreach ($buffer as $k =>$row) {
        $row['item']= $k+1;
        if($row['yy_state']==2){
          $row['yy_state']='待确认';
        }elseif($row['yy_state']==3){
          $row['yy_state']='已确认';
        }elseif($row['yy_state']==4){
          $row['yy_state']='已拒绝';
        }
        if($row['pay_type']==1){
          $row['pay_type']='微信支付';
        }elseif($row['pay_type']==2){
          $row['pay_type']='余额支付';
        }elseif($row['pay_type']==3){
          $row['pay_type']='积分支付';
        }elseif($row['pay_type']==5){
          $row['pay_type']='餐后付款';
        }
        $good=pdo_getall('cjdc_order_goods',array('order_id'=>$row['id']));
        for($i=0;$i<count($good);$i++){
          $date6='';
          if($good[$i]['spec']){
            $date6 .=$good[$i]['name'].'('.$good[$i]['spec'].')*'.$good[$i]['number']."  ";
          }else{
            $date6 .=$good[$i]['name'].'*'.$good[$i]['number']."  ";
          } 
        }
        $row['goods']=$date6;
        foreach ($keys as $key) {
          $data5[] = $row[$key];
        }
        $user[] = implode("\t ,", $data5) . "\t ,";
        unset($data5);
      }
      $html .= implode("\n", $user) . "\n";
    }
  }
  
  header("Content-type:text/csv");
  header("Content-Disposition:attachment; filename=预约订单数据.csv");
  echo $html;
  exit();
}

include $this->template('web/ydorder');