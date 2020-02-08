<?php
  ini_set('display_errors',1);            //错误信息
   ini_set('display_startup_errors',1);    //php启动错误信息
    error_reporting(-1);
define('rootdir', './../');  
header("Content-Type:text/html;charset=utf-8"); 
date_default_timezone_set("Asia/Hong_Kong"); 
include(rootdir."/lib/Smarty/libs/Smarty.class.php");  
include('function.php');
$smarty = new Smarty();  //建立smarty实例对象$smarty       
$smarty->cache_lifetime = 0;  //设置缓存时间
$smarty->caching = false; 
$smarty->template_dir = "./templates"; //设置模板目录  
$smarty->compile_dir = "./templates_c"; //设置编译目录 
$smarty->cache_dir = "./smarty_cache"; //缓存文件夹  
$smarty->left_delimiter = "<{"; 
$smarty->right_delimiter = "}>";

 
 
 
$step = isset($_REQUEST['step'])?trim($_REQUEST['step']) ? trim($_REQUEST['step']) : 1:1;
$mode = 0777;
if(file_exists(rootdir.'install/lock.php'))
{
	echo '已经安装过此程序...';
	exit;
}

switch($step)
{
    case '1': //安装许可协议 
		$license = file_get_contents(rootdir."/install/license.txt"); 
	  $licenset =  nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($license,ENT_COMPAT,'UTF-8'))); 
		$smarty->assign("stepname", '安装许可协议'); 
		$smarty->assign("licenset", $licenset); 
		$smarty->display('step1.tpl.php');   
		break;
	  case '2': //安装许可协议 
	  $server =  $_SERVER; 
	  $info['is_php']= version_compare(phpversion(),'5.2.0','>');
	  
	  $info['mysql'] = extension_loaded('mysql')?1:0;
	  $info['iconv'] = extension_loaded('iconv')|| extension_loaded('mbstring')?1:0;
	  $info['PHP_GD']  = '';
		if(extension_loaded('gd')) {
			if(function_exists('imagepng')) $info['PHP_GD'] .= 'png';
			if(function_exists('imagejpeg')) $info['PHP_GD'] .= ' jpg';
			if(function_exists('imagegif')) $info['PHP_GD'] .= ' gif';
		}
		$info['PHP_JSON'] = '0';
		if(extension_loaded('json')) {
			if(function_exists('json_decode') && function_exists('json_encode')) $info['PHP_JSON'] = '1';
		}
		//新加fsockopen 函数判断,此函数影响安装后会员注册及登录操作。
		if(function_exists('fsockopen')) {
			$info['PHP_JSON'] = '1';
		} 
		$info['fsockopen'] = function_exists('fsockopen') ?1:0;
	  
		//是否满足phpcms安装需求
	 
		
		
		$tempdo = 1;
		$files = file(rootdir."install/chmod.txt");		
		foreach($files as $_k => $file) {
			$file = str_replace('*','',$file);
			$file = trim($file);
			if(is_dir(rootdir.$file)) {
				$is_dir = '1';
				$cname = '目录';
				//继续检查子目录权限，新加函数
				$write_able = writable_check(rootdir.$file);
			} else {
				$is_dir = '0';
				$cname = '文件';
			}
			//新的判断
			if($is_dir =='0' && is_writable(rootdir.$file)) {
				$is_writable = 1;
			} elseif($is_dir =='1' && dir_writeable(rootdir.$file)){
				$is_writable = $write_able;
				if($is_writable=='0'){
					$no_writablefile = 1;
				}
			}else{
				$is_writable = 0;
 				$no_writablefile = 1;
  			}
							
			$filesmod[$_k]['file'] = $file;
			$filesmod[$_k]['is_dir'] = $is_dir;
			$filesmod[$_k]['cname'] = $cname;			
			$filesmod[$_k]['is_writable'] = $is_writable;
			if($is_writable == 0)
			{
			    $tempdo = 0;
		  }
		}
		if(dir_writeable(rootdir)) {
			$is_writable = 1;
		} else {
			$is_writable = 0;
		}
		$filesmod[$_k+1]['file'] = '网站根目录';
		$filesmod[$_k+1]['is_dir'] = '1';
		$filesmod[$_k+1]['cname'] = '目录';			
		$filesmod[$_k+1]['is_writable'] = $is_writable;	
	   if($is_writable == 0)
			{
			    $tempdo = 0;
		  }
		
		$is_right = (phpversion() >= '5.2.0' && extension_loaded('mysql') && $info['PHP_JSON'] && $info['PHP_GD'] && $info['fsockopen'] && $tempdo) ? 1 : 0;		
		$smarty->assign("filesmod", $filesmod); 
	  $smarty->assign("info", $info);
	  $smarty->assign("is_right", $is_right);
	  $smarty->assign("stepname", '运行环境检测');
		$smarty->assign("server", $server); 
		$smarty->display('step2.tpl.php');   
		break;
		case '3': //安装许可协议 
		$smarty->assign("stepname", '网站配置');
		$smarty->display('step3.tpl.php');   
		break;
		 
	  case '4': //安装许可协议 
	  $infos = $_POST;
	  foreach($infos as $key=>$value){
	   
	  	$smarty->assign($key, $value);
	  }
	  $smarty->assign("stepname", '数据导入');
		$smarty->assign('dbcharset', 'utf8');
		$smarty->display('step4.tpl.php');   
		break; 
		case '5': //执行SQL
		$smarty->display('step5.tpl.php');
		break; 
		case 'installmodule': //执行SQL
		extract($_POST);
		$GLOBALS['dbcharset'] = 'utf8';
		$PHP_SELF = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['ORIG_PATH_INFO']);
		$rootpath = str_replace('\\','/',dirname($PHP_SELF));	
		$rootpath = substr($rootpath,0,-7);
		$rootpath = strlen($rootpath)>1 ? $rootpath : "/";	
    
		if($module == 'admin') { 
			$lnk = mysql_connect($dbhost, $dbuser, $dbpw) or die ('Not connected : ' . mysql_error());
			$version = @mysql_get_server_info(); 
			if($version > '4.1' && $dbcharset) {
				mysql_query("SET NAMES '$dbcharset'");
			} 
			if($version > '5.0') {
				mysql_query("SET sql_mode=''");
			}
												
			if(!@mysql_select_db($dbname)){
				@mysql_query("CREATE DATABASE $dbname");
				if(@mysql_error()) {
					echo 1;exit;
				} else {
					mysql_select_db($dbname);
				}
			}
			$dbfile =  'xiaozu.sql';	
			if(file_exists(rootdir."install/sql/".$dbfile)) {
				$sql = file_get_contents(rootdir."install/sql/".$dbfile);
				_sql_execute($sql,$tablepre); 
			} else {
				echo '2';//数据库文件不存在
			}							
		}elseif($module == 'mkconfig'){
			if($fp = @fopen(rootdir."config/hopeconfig.php", 'w')) {
       @fclose($fp);    
       }else{
       	echo '创建配置文件失败请检测网站根目录下的config文件是否拥有写入功能';
       	exit;
      }  
      include(rootdir.'lib/core/urlmanager_class.php'); 
      $info = include(rootdir.'install/config/newconfig.php');  
      $info['siteurl'] = IUrl::getHost();
			$info['dbhost'] = $dbhost;
			$info['dbuser'] = $dbuser;
			$info['dbpw'] = $dbpw;
			$info['tablepre'] = $tablepre;
			$info['dbname'] = $dbname;
			$info['sitekey'] = $sitekey; 
	  $info['imgserver'] =  IUrl::getHost();
      $configData = var_export($info,true);
		  $configStr = "<?php return {$configData}?>";
      $fp = fopen(rootdir.'config/hopeconfig.php', 'w');
	      fwrite($fp, $configStr);
        fclose($fp);
		   //写入配置文件
		  
		} 
		echo $module;
		break;
		
		
		
		
		case 'dbtest':
		extract($_GET);
		if(!@mysql_connect($dbhost, $dbuser, $dbpw)) {
			exit('2');
		}
		$server_info = mysql_get_server_info();
		if($server_info < '4.0') exit('6');
		if(!mysql_select_db($dbname)) {
			if(!@mysql_query("CREATE DATABASE `$dbname`")) exit('3');
			mysql_select_db($dbname);
		}
		$tables = array();
		$query = mysql_query("SHOW TABLES FROM `$dbname`");
		while($r = mysql_fetch_row($query)) {
			$tables[] = $r[0];
		}
		if($tables && in_array($tablepre.'module', $tables)) {
			exit('0');
		}
		else {
			exit('1');
		}
		break;
		
}
function writable_check($path){
	$dir = '';
	$is_writable = '1';
	if(!is_dir($path)){return '0';}
	$dir = opendir($path);
 	while (($file = readdir($dir)) !== false){
		if($file!='.' && $file!='..'){
			if(is_file($path.'/'.$file)){
				//是文件判断是否可写，不可写直接返回0，不向下继续
				if(!is_writable($path.'/'.$file)){
 					return '0';
				}
			}else{
				//目录，循环此函数,先判断此目录是否可写，不可写直接返回0 ，可写再判断子目录是否可写 
				$dir_wrt = dir_writeable($path.'/'.$file);
				if($dir_wrt=='0'){
					return '0';
				}
   				$is_writable = writable_check($path.'/'.$file);
 			}
		}
 	}
	return $is_writable;
}
function _sql_execute($sql,$r_tablepre = '',$s_tablepre = 'xiaozu_') {
    $sqls = _sql_split($sql,$r_tablepre,$s_tablepre);
   
	if(is_array($sqls))
    {
		foreach($sqls as $sql)
		{
			if(trim($sql) != '')
			{
				mysql_query($sql);
			}
		}
	}
	else
	{
		mysql_query($sqls);
	}
	return true;
}

function _sql_split($sql,$r_tablepre = '',$s_tablepre='xiaozu_') {
	global $dbcharset,$tablepre;
	$r_tablepre = $r_tablepre ? $r_tablepre : $tablepre;
	$dbcharset = 'utf8';
	if(mysql_get_server_info() > '4.1' && $dbcharset)
	{
		$sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=".$dbcharset,$sql);
	}
	
	if($r_tablepre != $s_tablepre) $sql = str_replace($s_tablepre, $r_tablepre, $sql);
	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	$queriesarray = explode(";\n", trim($sql));
	unset($sql);
	foreach($queriesarray as $query)
	{
		$ret[$num] = '';
		$queries = explode("\n", trim($query));
		$queries = array_filter($queries);
		foreach($queries as $query)
		{
			$str1 = substr($query, 0, 1);
			if($str1 != '#' && $str1 != '-') $ret[$num] .= $query;
		}
		$num++;
	}
	return $ret;
}
function dir_writeable($dir) {
	$writeable = 0;
	if(is_dir($dir)) {  
        if($fp = @fopen("$dir/chkdir.test", 'w')) {
            @fclose($fp);      
            @unlink("$dir/chkdir.test"); 
            $writeable = 1;
        } else {
            $writeable = 0; 
        } 
	}
	return $writeable;
}


 
?>