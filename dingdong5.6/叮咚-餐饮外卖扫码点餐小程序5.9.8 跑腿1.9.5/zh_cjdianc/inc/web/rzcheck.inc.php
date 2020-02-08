<?php
global $_GPC, $_W;
$day=100;
// echo date('Y-m-d H:i:s',strtotime("+{$day}day"));die;
// echo strtotime('2020-06-08');die;
$GLOBALS['frames'] = $this->getMainMenu();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$type=empty($_GPC['type']) ? 'all' :$_GPC['type'];
$state=$_GPC['state'];
$pageindex = max(1, intval($_GPC['page']));
$pagesize=20;
$where=' WHERE  uniacid=:uniacid  and sq_id>0 and zf_state=2';
$data[':uniacid']=$_W['uniacid'];
if($_GPC['keywords']){
    $op=$_GPC['keywords'];
    $where.=" and name LIKE  concat('%', :name,'%') ";    
    $data[':name']=$op;
}
if($type !='all'){
     $where.= " and state=".$state;
}
$sql="SELECT * FROM ".tablename('cjdc_store'). $where." ORDER BY id DESC";
$total=pdo_fetchcolumn("SELECT count(*) FROM ".tablename('cjdc_store') .$where,$data);
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list=pdo_fetchall($select_sql,$data);
$pager = pagination($total, $pageindex, $pagesize);
if($operation=='adopt'){//审核通过 
    $id=$_GPC['id'];
    $store=pdo_get('cjdc_store',array('id'=>$id));
    if(!$store['md_type']){
    message('门店类型不能为空,为门店编辑分类后再来审核!','','error'); 
}
$dqtime=date('Y-m-d H:i:s',strtotime("+{$store['rz_time']}day"));
$res=pdo_update('cjdc_store',array('state'=>2,'rzdq_time'=>$dqtime),array('id'=>$id));  
    if($res){
    $set=pdo_get('cjdc_storeset',array('store_id'=>$id));
    if(!$set){   
      $data3['store_id']=$id;
      pdo_insert('cjdc_storeset',$data3);
    }
   
    pdo_delete('cjdc_formid',array('time <='=>time()-60*60*24*7));
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
         $res2=pdo_get('cjdc_store',array('id'=>$_GET['id']));
         $user=pdo_get('cjdc_user',array('id'=>$res2['sq_id']));
         if($res2['state']==2){
            $state="审核通过";
            $note="审核通过,请尽快完善信息";
         } if($res2['state']==3){
            $state="审核拒绝";
            $note="审核拒绝,请核对信息后再次提交";
         }  
         $form=pdo_get('cjdc_formid',array('user_id'=>$res2['sq_id'],'time >='=>time()-60*60*24*7));
         $formwork ='{
           "touser": "'.$user["openid"].'",
           "template_id": "'.$res["rzcg_tid"].'",
           "page": "zh_cjdianc/pages/Liar/loginindex",
           "form_id":"'.$form['form_id'].'",
           "data": {
             "keyword1": {
               "value": "'.$state.'",
               "color": "#173177"
             },
             "keyword2": {
               "value":"'.$res2['sq_time'].'",
               "color": "#173177"
             },
             "keyword3": {

               "value": "'.$res2['name'].'",
               "color": "#173177"
             },
             "keyword4": {
               "value":  "'. $note.'",
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
        message('审核成功',$this->createWebUrl('rzcheck',array()),'success');
    }else{
        message('审核失败','','error');
    }
}
if($operation=='reject'){
     $id=$_GPC['id'];
    $res=pdo_update('cjdc_store',array('state'=>3),array('id'=>$id));
     if($res){  
          pdo_delete('cjdc_formid',array('time <='=>time()-60*60*24*7));
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
         $res2=pdo_get('cjdc_store',array('id'=>$_GET['id']));
         $user=pdo_get('cjdc_user',array('id'=>$res2['sq_id']));
         if($res2['state']==2){
            $state="审核通过";
            $note="审核通过,请尽快完善信息";
         } if($res2['state']==3){
            $state="审核拒绝";
            $note="审核拒绝,请核对信息后再次提交";
         }  
         $form=pdo_get('cjdc_formid',array('user_id'=>$res2['sq_id'],'time >='=>time()-60*60*24*7));
         $formwork ='{
           "touser": "'.$user["openid"].'",
           "template_id": "'.$res["rzcg_tid"].'",
           "page": "zh_cjdianc/pages/Liar/loginindex",
           "form_id":"'.$form['form_id'].'",
           "data": {
             "keyword1": {
               "value": "'.$state.'",
               "color": "#173177"
             },
             "keyword2": {
               "value":"'.$res2['sq_time'].'",
               "color": "#173177"
             },
             "keyword3": {

               "value": "'.$res2['name'].'",
               "color": "#173177"
             },
             "keyword4": {
               "value":  "'. $note.'",
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
        message('拒绝成功',$this->createWebUrl('rzcheck',array()),'success');
    }else{
        message('拒绝失败','','error');
    }
}
if($operation=='delete'){
     $id=$_GPC['id'];
     $res=pdo_delete('cjdc_store',array('id'=>$id));
     if($res){
        message('删除成功',$this->createWebUrl('rzcheck',array()),'success');
    }else{
        message('删除失败','','error');
    }

}

include $this->template('web/rzcheck');