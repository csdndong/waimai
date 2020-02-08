<?php
/**
 * zh_cjpt模块定义
 *
 * @author 武汉志汇科技
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Zh_cjptModule extends WeModule {

    public function welcomeDisplay()
    {   
        global $_GPC, $_W;

            $url = $this->createWebUrl('index');
	    	//$url = $this->createWebUrl('gaikuangdata');
	        Header("Location: " . $url);
    	}
    

}