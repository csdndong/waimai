<?php
 
class JSON
{
	//第三方JSON类库实例
	private static $_jsonInstance = null;
 
	public static function encode($param)
	{
		if(version_compare(phpversion(),'5.4.0') >= 0)
		{
			return json_encode($param,JSON_UNESCAPED_UNICODE);
		}

		$result = '';
		if(function_exists('json_encode'))
		{
			$result = json_encode($param);
		}
		else
		{
			$jsonObject = self::getJsonInstance();
			$result = $jsonObject->encodeUnsafe($param);
		}
		//对于中文的转换
		return preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $result);
	}

	 
	public static function decode($string)
	{
		if(function_exists('json_decode'))
		{
			return json_decode($string,true);
		}

		$jsonObject = self::getJsonInstance();
		return $jsonObject->decode($string);
	}

	 
	private static function getJsonInstance()
	{
		if(self::$_jsonInstance == null)
		{
			include(dirname(__FILE__).'/Services_JSON-1.0.3/JSON.php');
			self::$_jsonInstance = new Services_JSON();
		}
		return self::$_jsonInstance;
	}
}