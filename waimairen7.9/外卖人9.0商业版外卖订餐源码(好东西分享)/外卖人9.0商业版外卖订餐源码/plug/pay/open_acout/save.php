<?php   
if(!defined('IN_WaiMai')) {
	exit('Access Denied');
}     
          $taskinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."paylist where loginname='".$idtype."'  ");  
        	  include_once($logindir.'/'.$idtype.'/set.php');
        	  $ssdata['loginname'] = $idtype;
        	  $ssdata['logindesc'] = $setinfo['name']; 
        	  $ssdata['logourl'] = ''; 
        	  $ssdata['temp'] = json_encode(array());
        	  $ssdata['type'] = 0;
        	  if(empty($taskinfo))
        {
        	 
  	      	$this->mysql->insert(Mysite::$app->config['tablepre'].'paylist',$ssdata);  //写消息表	 
        }	else{ 
        	   unset($ssdata['loginname']);
        	 	 $this->mysql->update(Mysite::$app->config['tablepre'].'paylist',$ssdata,"loginname='".$idtype."'"); 
        }
      $this->success('ok');
        	
   
 
?>