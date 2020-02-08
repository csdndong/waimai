<?php 

/**
 * @class phptoexcel
 * @brief 数据内容导出成excel文件.xls文件类型
 调用说明 
 $check = $sendmsg->login();
 if($check == 'ok')
{
//获取账号余额代码

     $check->getBalance();
//发送短信代码
 sendmsg->sendsms(array(),'内容');
}

$sendmsg->endsend();//每次处理完成后需要退出
 */
class pushshop
{
	//推送一条信息给店铺
	public static function push($content,$shopuid){
		$pushdir = plugdir.'/xinge'; 

		if(file_exists($pushdir.'/XingeApp.php')){  
			require_once($pushdir.'/XingeApp.php');
			//IOSENV_PROD  
			XingeApp::PushAccountIos(2200181584, "215bb54a1a234ef010ef93c539d5ebdb", $content, $shopuid, XingeApp::IOSENV_DEV);
			logwrite('xxx'); 
			
			
		} 
	}
}
?>