<?php 
@define('IN_WaiMai', TRUE);  
$xhopedir = '../../../'; 
define('hopedir', $xhopedir);  
define('plugdir', $xhopedir.'/plug');  
define('PlugName', 'wxOauth'); 
date_default_timezone_set("Asia/Hong_Kong"); //时间区域
header("Content-Type:text/html;charset=utf-8"); //输出格式  
$Mconfig = include(hopedir."config/hopeconfig.php");  
include('wxlogin.php'); 
// include(hopedir.'/wx/mysql_class.php'); //加载数据库类
//include(hopedir.'/lib/function.php');
include(hopedir.'/lib/function.php');
$config = hopedir."/config/hopeconfig.php";  
$Mysite = hopedir."/lib/core/Mysite.php";
require($Mysite);
Mysite::createWebApp($config);
$wxlogin = new wxlogin();
$wxlogin->login();
?>