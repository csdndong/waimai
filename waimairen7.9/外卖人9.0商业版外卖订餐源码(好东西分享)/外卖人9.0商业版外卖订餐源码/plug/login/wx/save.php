<?php  
  $savedata['appid'] =  IReq::get('appid');
  $savedata['appsecret'] =  IReq::get('appsecret');
  $savedata['callback'] = Mysite::$app->config['siteurl'].'/plug/login/wx/login.php';
  $savedata['storageType'] = "file";
  $savedata['host'] = "localhost";
  $savedata['user'] = "root";
  $savedata['password'] = "root";
  $savedata['database'] = "test";
  $savedata['scope'] = "get_user_info";
  $savedata['errorReport'] = false;
  
  
  
  
  
  
  
  $setting = json_encode($savedata);
  
  // print_r('!!'.$logindir.'--');
  // print_r($idtype.'==');
  
  // print_r($taskinfo);exit;
    include_once($logindir.'/'.$idtype.'/set.php');
        	  $ssdata['loginname'] = $idtype;
        	  $ssdata['logindesc'] = $setinfo['name']; 
        	  $ssdata['logourl'] = Mysite::$app->config['siteurl'].'/plug/login/'.$idtype.'/images/'.$setinfo['logourl']; 
        	  $ssdata['temp'] = json_encode($savedata);
			  $taskinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."otherlogin where loginname='".$idtype."'  order by id desc ");
        	  if(empty($taskinfo))
        {
        	 unset($ssdata['addmeta']);
  	      	$this->mysql->insert(Mysite::$app->config['tablepre'].'otherlogin',$ssdata);  //写消息表	 
        }	else{
        	 unset($ssdata['addmeta']);
        	   unset($ssdata['loginname']);
        	 	 $this->mysql->update(Mysite::$app->config['tablepre'].'otherlogin',$ssdata,"loginname='".$idtype."'"); 
        }
        echo "<meta charset='utf-8' />";
         echo "<script>parent.uploadsucess('配置成功');</script>";   
         exit;
   
  
  /*   echo "<script>parent.uploaderror('".json_encode($upload->errmsg())."');</script>";   
		  }else{ 
		  echo "<script>parent.uploadsucess('".$filepath.$file[0][saveName]."');</script>"; */
?>