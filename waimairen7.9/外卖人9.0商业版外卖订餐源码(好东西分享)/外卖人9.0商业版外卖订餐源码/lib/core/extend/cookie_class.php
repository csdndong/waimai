<?php
 
class ICookie
{
	//cookie前缀
	private static $pre        = 'cook_';

	//默认cookie密钥
	private static $defaultKey = 'noone';

	//安全级别
	private static $level      = 'none';

	//获取配置的前缀
	private static function getPre()
	{ 
			return self::$pre; 
	}

	//获取当前的安全级别
	private static function getLevel()
	{ 
			return self::$level; 
	}

  
	public static function set($name,$value='',$time='3600',$path='/',$domain=null)
	{
		self::$pre = self::getPre();
		if($time <= 0) $time = -100;
		else $time = time() + $time;
		setCookie(self::$pre.'safecode',  ICrypt::encode( self::cookieId() , self::getKey()  )       ,$time,$path,$domain);
		if(is_array($value) || is_object($value)) $value=serialize($value);
		$value = ICrypt::encode($value , self::getKey() );
		setCookie(self::$pre.$name,$value,$time,$path,$domain);
	}

  
	public static function get($name)
	{
		self::$pre  = self::getPre();
		$is_checked = self::checkSafe();

		if($is_checked == 1)
		{
			if(isset($_COOKIE[self::$pre.$name]))
			{
				$cookie= ICrypt::decode($_COOKIE[self::$pre.$name],self::getKey());
				$tem = substr($cookie,0,10);
				if(preg_match('/^[Oa]:\d+:.*/',$tem)) return unserialize($cookie);
				else return $cookie;
			}
			return null;
		}
		else if($is_checked == 0)
		{
			self::clear(self::$pre.'safecode');
		}

		return null;
	}

 
	public static function clear($name)
	{
		self::set($name,'',0);
	}

    
	public static function clearAll()
	{
		self::$pre = self::getPre();
		$preLen = strlen(self::$pre);
		foreach($_COOKIE as $name => $val)
		{
			if(strpos($name,self::$pre) === 0)
			{
				self::clear(substr($name,$preLen));
			}
		}
	}

   
	private static function checkSafe()
	{
		self::$pre = self::getPre();
		if(isset($_COOKIE[self::$pre.'safecode']))
		{
			if( self::cookieId() == ICrypt::decode($_COOKIE[self::$pre.'safecode'] , self::getKey())    )
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

 
	private static function getKey()
	{
		$encryptKey =   self::$defaultKey;
		$encryptKey .= self::cookieId();
		return $encryptKey;
	}

   
	private static function cookieId()
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
