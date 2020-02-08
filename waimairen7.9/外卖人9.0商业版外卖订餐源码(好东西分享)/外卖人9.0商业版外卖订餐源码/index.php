<?php
@define('IN_WaiMai', TRUE);
/*  用于防止跨站调用
if(!defined('IN_WaiMai')) {
	exit('Access Denied');
}*/  
define('hopedir', dirname(__FILE__).DIRECTORY_SEPARATOR);  
define('plugdir', dirname(__FILE__).'/plug');  
define('EARTH_RADIUS', 6378.137);//地球半径，假设地球是规则的球体
define('PI', 3.1415926);
  error_reporting(0);   
    // ini_set('display_errors',1);            //错误信息
   // ini_set('display_startup_errors',1);    //php启动错误信息
    // error_reporting(-1);
 date_default_timezone_set("Asia/Hong_Kong"); //时间区域
header("Content-Type:text/html;charset=utf-8"); //输出格式 
if(!file_exists(hopedir.'config/hopeconfig.php'))
{

	echo '未安装程序<a href="/install/index.php">进入安装页面</a>';
	exit;
}
include(hopedir.'/lib/function.php');
$config = hopedir."/config/hopeconfig.php";
$Mysite = hopedir."/lib/core/Mysite.php";
include(hopedir.'/lib/authorize.php'); 
logwrite("xx");
require($Mysite);
Mysite::createWebApp($config)->run(); 

