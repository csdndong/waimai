<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$item=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));
$time=time()-60*60*24*7;
$sql=" select distinct user_id from".tablename('cjdc_formid')." where  uniacid={$_W['uniacid']} and time>={$time} ";
$user=pdo_fetchall($sql);

 if(checksubmit('submit')){
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
function set_msg($_W, $_GPC,$user){
 $access_token = getaccess_token($_W);
 $res=pdo_get('cjdc_message',array('uniacid'=>$_W['uniacid']));
 $userinfo=pdo_get('cjdc_user',array('id'=>$user));
 $time=time()-60*60*24*7;
 if($_POST['dz']){
 	$_POST['dz']=$_POST['dz'];
 }else{
 	$_POST['dz']="zh_cjdianc/pages/Liar/loginindex";
 }
  if($_POST['sj']){
 	$_POST['sj']=$_POST['sj'];
 }else{
 	$_POST['sj']=date("Y-m-d H:i:s");
 }
 $form=pdo_fetch("select * from ".tablename('cjdc_formid')." where user_id={$user} and time>={$time} order by id asc");
$formwork ='{
 "touser": "'.$userinfo["openid"].'",
 "template_id": "'.$res["qf_tid"].'",
 "page":"'.$_POST['dz'].'",
 "form_id":"'.$form['form_id'].'",
 "data": {
  "keyword1": {
     "value": "'.$_POST['bz'].'",
     "color": "#173177"
   },
   "keyword2": {
     "value": "'.$_POST['ly'].'",
     "color": "#173177"
   },
   "keyword3": {
     "value":"'.$_POST['nr'].'",
     "color": "#173177"
   },
    "keyword4": {
     "value":"'.$_POST['sj'].'",
     "color": "#173177"
   }
 },
   "emphasis_keyword": "keyword1.DATA"  
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
//return $data;
pdo_delete('cjdc_formid',array('id'=>$form['id']));
}
for($i=0;$i<count($user);$i++){
    echo set_msg($_W,$_GPC,$user[$i]['user_id']);
}

$data['note']=$_GPC['bz'];
$data['source']=$_GPC['ly'];
$data['content']=$_GPC['nr'];
if($_GPC['sj']){
  $data['time']=$_GPC['sj'];
}else{
  $data['time']=date("Y-m-d H:i:s");
}
if($_GPC['dz']){
 $data['src']=$_GPC['dz'];
}else{
  $data['src']="首页";
}

$data['fs_time']=date("Y-m-d H:i:s");
$data['uniacid']=$_W['uniacid'];
pdo_insert('cjdc_message2',$data);
message('发送成功',$this->createWebUrl('message',array()),'success');
}

include $this->template('web/message');