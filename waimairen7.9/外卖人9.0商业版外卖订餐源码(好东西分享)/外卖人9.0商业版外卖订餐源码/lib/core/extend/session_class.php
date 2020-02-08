<?php
 
session_start();
 
class ISession
{
	//session前缀
	private static $pre='iweb_';

	//安全级别
	private static $level = 'normal';

	//获取配置的前缀
	private static function getPre()
	{
		if(isset(IWeb::$app->config['safePre']))
		{
			return IWeb::$app->config['safePre'];
		}
		else
		{
			return self::$pre;
		}
	}

	//获取当前的安全级别
	private static function getLevel()
	{
		if(isset(IWeb::$app->config['safeLevel']))
		{
			return IWeb::$app->config['safeLevel'];
		}
		else
		{
			return self::$level;
		}
	}

 
	public static function set($name,$value='')
	{
		self::$pre = self::getPre();
		if(self::checkSafe()==-1) $_SESSION[self::$pre.'safecode']=self::sessionId();
		$_SESSION[self::$pre.$name]=$value;
	}
     
	public static function get($name)
	{
		self::$pre  = self::getPre();
		$is_checked = self::checkSafe();

		if($is_checked == 1)
		{
			return isset($_SESSION[self::$pre.$name])?$_SESSION[self::$pre.$name]:null;
		}
		else if($is_checked == 0)
		{
			self::clear(self::$pre.'safecode');
		}
		return null;
	}
    
	public static function clear($name)
	{
		self::$pre = self::getPre();
		unset($_SESSION[self::$pre.$name]);
	}
   
	public static function clearAll()
	{
		return session_destroy();
	}

   
	private static function checkSafe()
	{
		self::$pre = self::getPre();
		if(isset($_SESSION[self::$pre.'safecode']))
		{
			if($_SESSION[self::$pre.'safecode']==self::sessionId())
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return -1;
		}
	}
   
	private static function sessionId()
	{
		$level = self::getLevel();
		if($level == 'none')
		{
			return '';
		}
		else if($level == 'normal')
		{
			return md5(IClient::getIP());
		}
		return md5(IClient::getIP().$_SERVER["HTTP_USER_AGENT"]);
	}
}
?>
