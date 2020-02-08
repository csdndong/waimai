<?php
 
class ICrypt
{
	 
	public static function md5($string)
	{
		return md5($string);
	}

	 
	public static function base64encode($str)
	{
		return base64_encode($str);
	}

	 
	public static function base64decode($str)
	{
		return base64_decode($str);
	}

	 
	public static function decode($string, $key='', $expiry=0)
	{
		return self::code($string,'decode', $key, $expiry);
	}

 
	public static function encode($string, $key='', $expiry=0)
	{
		return self::code($string,'encode', $key, $expiry);
	}
	 
	private static function code($string, $op="decode", $key='', $expiry=0)
	{
		$op=strtolower($op);
		$key_length=10;
		$key=md5($key?$key:"jooyea");
		//生成256长度的密码
		$key_1=md5(substr($key,0,4));
		$key_2=md5(substr($key,4,4));
		$key_3=md5(substr($key,8,4));
		$key_4=md5(substr($key,12,4));
		$key_5=md5(substr($key,16,4));
		$key_6=md5(substr($key,20,4));
		$key_7=md5(substr($key,24,4));
		$key_8=md5(substr($key,28,4));
		$key_e= $key_length ? ($op == 'decode' ? substr($string, 0, $key_length): substr(md5(microtime()), -$key_length)) : '';
		$cryptkey = md5($key_1|$key_e).md5($key_3|$key_e).md5($key_5|$key_e).md5($key_7|$key_e).md5($key_8|$key_e).md5($key_6|$key_e).md5($key_4|$key_e).md5($key_2|$key_e);
		$cryptkey_length = strlen($cryptkey);
		$string = $op == 'decode' ? self::base64decode(substr($string, $key_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$key_5), 0, 22).$string;
		$string_length = strlen($string);
		$result="";
		//通过循环的方式异或的方式加密，异或方式是加密中常用的一种处理方式
		for($i = 0; $i < $string_length; $i++)
		{
			$result .= chr(ord($string[$i]) ^ ($cryptkey[$i % 256]));
		}
		//解码部分
		if($op == 'decode')
		{
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 22) == substr(md5(substr($result, 32).$key_5), 0, 22))
			{
				return substr($result, 32);
			} else
			{
				return '';
			}
		}
		else
		{
			return $key_e.str_replace('=', '', self::base64encode($result));
		}
	}
}
?>