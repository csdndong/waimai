<?php
global $_GPC, $_W;

$GLOBALS['frames'] = $this->getMainMenu();
$storetype=pdo_getall('cjdc_storetype',array('uniacid'=>$_W['uniacid']),array(),'','num asc');
$sql="SELECT * FROM ".tablename('cjdc_store')."  where id=:id ";
$item=pdo_fetch($sql,array(':id'=>$_GPC['id']));
// print_r($item);die;
if(checksubmit('submit')){   
	$data['name']=$_GPC['name'];    
	$data['address'] = $_GPC['address']; 
	$data['link_name']=$_GPC['link_name'];
	$data['link_tel']=$_GPC['link_tel'];
	$data['state']=$_GPC['state'];
	$data['rzdq_time']=date('Y-m-d H:i:s',strtotime("+{$item['rz_time']}day"));;
	$data['md_type']=$_GPC['md_type'];
	if(empty($_GPC['md_type'])){
		 message('分类不能为空','','error');
	}
	if($_GPC['state']==2){
		// if(strlen($item['logo'])<25){
		// 	$data['logo']=$_W['attachurl'].$item['logo'];
		// }
		// if(strlen($item['zm_img'])<25){
		// 	$data['zm_img']=$_W['attachurl'].$item['zm_img'];
		// }
		// if(strlen($item['fm_img'])<25){
		// 	$data['fm_img']=$_W['attachurl'].$item['fm_img'];
		// }
		// if(strlen($item['yyzz'])<25){
		// 	$data['yyzz']=$_W['attachurl'].$item['yyzz'];
		// }
		$set=pdo_get('cjdc_storeset',array('store_id'=>$item['id']));
    if(!$set){   
      $data3['store_id']=$item['id'];
      pdo_insert('cjdc_storeset',$data3);
  		}
	}
	$data['details']=html_entity_decode($_GPC['details']);
	$rst=pdo_update('cjdc_store',$data,array('id'=>$item['id']));
	if($rst){
        $dqtime=date('Y-m-d H:i:s',strtotime("+{$store['rz_time']}day"));

            $set = pdo_get('cjdc_storeset', array('store_id' => $id));
            if (!$set) {
                $data3['store_id'] = $id;
                pdo_insert('cjdc_storeset', $data3);
            }

            pdo_delete('cjdc_formid', array('time <=' => time() - 60 * 60 * 24 * 7));
            ///////////////模板消息拒绝///////////////////
            function getaccess_token($_W)
            {
                $res = pdo_get('cjdc_system', array('uniacid' => $_W['uniacid']));
                $appid = $res['appid'];
                $secret = $res['appsecret'];
                $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $secret . "";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $data = curl_exec($ch);
                curl_close($ch);
                $data = json_decode($data, true);
                return $data['access_token'];
            }

            //设置与发送模板信息
            function set_msg($_W)
            {
                $access_token = getaccess_token($_W);
                $res = pdo_get('cjdc_message', array('uniacid' => $_W['uniacid']));
                $res2 = pdo_get('cjdc_store', array('id' => $_GET['id']));
                $user = pdo_get('cjdc_user', array('id' => $res2['sq_id']));
                if ($res2['state'] == 2) {
                    $state = "审核通过";
                    $note = "审核通过,请尽快完善信息";
                }
                if ($res2['state'] == 3) {
                    $state = "审核拒绝";
                    $note = "审核拒绝,请核对信息后再次提交";
                }
                $form = pdo_get('cjdc_formid', array('user_id' => $res2['sq_id'], 'time >=' => time() - 60 * 60 * 24 * 7));
                $formwork = '{
           "touser": "' . $user["openid"] . '",
           "template_id": "' . $res["rzcg_tid"] . '",
           "page": "zh_cjdianc/pages/Liar/loginindex",
           "form_id":"' . $form['form_id'] . '",
           "data": {
             "keyword1": {
               "value": "' . $state . '",
               "color": "#173177"
             },
             "keyword2": {
               "value":"' . $res2['sq_time'] . '",
               "color": "#173177"
             },
             "keyword3": {

               "value": "' . $res2['name'] . '",
               "color": "#173177"
             },
             "keyword4": {
               "value":  "' . $note . '",
               "color": "#173177"
             }
        
           }
         }';
                // $formwork=$data;
                $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=" . $access_token . "";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $formwork);
                $data = curl_exec($ch);
                curl_close($ch);
                // return $data;
                pdo_delete('cjdc_formid', array('id' => $form['id']));
            }

            echo set_msg($_W);


	     message('编辑成功！', $this->createWebUrl('rzcheck'), 'success');
	}else{
	     message('编辑失败！','','error');
	}

}

include $this->template('web/rzcheckinfo');