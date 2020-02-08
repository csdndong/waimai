<?php
 
class IFilter
{
	 
	public static function limitLen($str,$length)
	{
		if($length !== false)
		{
			$count = IString::getStrLen($str);
			if($count > $length)
			{
				return '';
			}
			else
			{
				return $str;
			}
		}
		return $str;
	}

	 
	public static function act($str,$type = 'string',$limitLen = false)
	{
		if(is_array($str))
		{
			foreach($str as $key => $val)
			{
				$resultStr[$key] = self::act($val, $type, $limitLen);
			}
			return $resultStr;
		}
		else
		{
			switch($type)
			{
				case "int":
					return intval($str);
					break;

				case "float":
					return floatval($str);
					break;

				case "text":
					return self::text($str,$limitLen);
					break;

				case "bool":
					return (bool)$str;
					break;

				default:
					return self::string($str,$limitLen);
					break;
			}
		}
	}

	 
	public static function string($str,$limitLen = false)
	{
		$str = self::limitLen($str,$limitLen);
		$str = trim($str);
		$except = array('　');
		$str = str_replace($except,'',htmlspecialchars($str,ENT_QUOTES));
		return self::addSlash($str);
	}

 
	public static function text($str,$limitLen = false)
	{
		$str = self::limitLen($str,$limitLen);
		$str = trim($str);

		require_once(dirname(__FILE__)."/htmlpurifier-4.3.0/HTMLPurifier.standalone.php");
		$cache_dir=hopedir."templates_c/htmlpurifier/";
		if(!file_exists($cache_dir))
		{
			IFile::mkdir($cache_dir);
		}
		$config = HTMLPurifier_Config::createDefault();

		//配置 允许flash
		$config->set('HTML.SafeEmbed',true);
		$config->set('HTML.SafeObject',true);
		$config->set('Output.FlashCompat',true);

		//配置 缓存目录
		$config->set('Cache.SerializerPath',$cache_dir); //设置cache目录

		//允许<a>的target属性
		$def = $config->getHTMLDefinition(true);
		$def->addAttribute('a', 'target', 'Enum#_blank,_self,_target,_top');

		$purifier = new HTMLPurifier($config);//过略掉所有<script>，<i?frame>标签的on事件,css的js-expression、import等js行为，a的js-href
		return $purifier->purify($str);
	}

	/**
	 * @brief 增加转义斜线
	 * @param string $str 要转义的字符串
	 * @return string 转义后的字符串
	 */
	public static function addSlash($str)
	{
		if(is_array($str))
		{
			$resultStr = array();
			foreach($str as $key => $val)
			{
				$resultStr[$key] = self::addSlash($val);
			}
			return $resultStr;
		}
		else
		{
			return addslashes($str);
		}
	}

	/**
	 * @brief 增加转义斜线
	 * @param string $str 要转义的字符串
	 * @return string 转义后的字符串
	 */
	public static function stripSlash($str)
	{
		if(is_array($str))
		{
			$resultStr = array();
			foreach($str as $key => $val)
			{
				$resultStr[$key] = self::stripSlash($val);
			}
			return $resultStr;
		}
		else
		{
			return stripslashes($str);
		}
	}

	 
	public static function checkHex($file)
	{
		$resource = fopen($file, 'rb');
		$fileSize = filesize($file);
		fseek($resource, 0);
		// 读取文件的头部和尾部
		if ($fileSize > 512)
		{
			$hexCode = bin2hex(fread($resource, 512));
			fseek($resource, $fileSize - 512);
			$hexCode .= bin2hex(fread($resource, 512));
		}
		// 读取文件的全部内容
		else
		{
			$hexCode = bin2hex(fread($resource, $fileSize));
		}
		fclose($resource);
		/* 匹配16进制中的 <% ( | ) %> */
		/* 匹配16进制中的 <? ( | ) ?> */
		/* 匹配16进制中的 <script | /script>  */
		if (preg_match("/(3c25.*?28)|(29.*?253e)|(3c3f.*?28)|(29.*?3f3e)|(3C534352495054)|(2F5343524950543E)|(3C736372697074)|(2F7363726970743E)/is", $hexCode))
		{
			return false;
		}
		else
		{
			return true;	
		}
	}
}