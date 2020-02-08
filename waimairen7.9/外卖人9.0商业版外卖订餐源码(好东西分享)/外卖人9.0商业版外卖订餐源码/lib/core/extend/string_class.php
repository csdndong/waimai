<?php
 
class IString
{
	 
	public static function substr($str, $length = 0, $append = true, $isUTF8=true)
	{
		$byte   = 0;
		$amount = 0;
		$str    = trim($str);
		$length = intval($length);

		//获取字符串总字节数
		$strlength = strlen($str);

		//无截取个数 或 总字节数小于截取个数
		if($length==0 || $strlength <= $length)
		{
			return $str;
		}

		//utf8编码
		if($isUTF8 == true)
		{
			while($byte < $strlength)
			{
				if(ord($str{$byte}) >= 224)
				{
					$byte += 3;
					$amount++;
				}
				else if(ord($str{$byte}) >= 192)
				{
					$byte += 2;
					$amount++;
				}
				else
				{
					$byte += 1;
					$amount++;
				}

				if($amount >= $length)
				{
					$resultStr = substr($str, 0, $byte);
					break;
				}
			}
		}

		//非utf8编码
		else
		{
			while($byte < $strlength)
			{
				if(ord($str{$byte}) > 160)
				{
					$byte += 2;
					$amount++;
				}
				else
				{
					$byte++;
					$amount++;
				}

				if($amount >= $length)
				{
					$resultStr = substr($str, 0, $byte);
					break;
				}
			}
		}

		//实际字符个数小于要截取的字符个数
		if($amount < $length)
		{
			return $str;
		}

		//追加省略号
		if($append)
		{
			$resultStr .= '...';
		}
		return $resultStr;
	}

	 
	public static function setCode($str,$outCode='UTF-8')
	{
		if(self::isUTF8($str)==false)
		{
			return iconv('GBK',$outCode,$str);
		}
		return $str;
	}

	 
	public static function isUTF8($str)
	{
		$result=preg_match('%^(?:[\x09\x0A\x0D\x20-\x7E] # ASCII
		| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
		| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
		| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
		| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
		| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
		| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
		| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
		)*$%xs', $str);
		return $result ? true : false;
	}

	 
	public static function getStrLen($str)
	{
		$byte   = 0;
		$amount = 0;
		$str    = trim($str);

		//获取字符串总字节数
		$strlength = strlen($str);

		//检测是否为utf8编码
		$isUTF8=self::isUTF8($str);

		//utf8编码
		if($isUTF8 == true)
		{
			while($byte < $strlength)
			{
				if(ord($str{$byte}) >= 224)
				{
					$byte += 3;
					$amount++;
				}
				else if(ord($str{$byte}) >= 192)
				{
					$byte += 2;
					$amount++;
				}
				else
				{
					$byte += 1;
					$amount++;
				}
			}
		}

		//非utf8编码
		else
		{
			while($byte < $strlength)
			{
				if(ord($str{$byte}) > 160)
				{
					$byte += 2;
					$amount++;
				}
				else
				{
					$byte++;
					$amount++;
				}
			}
		}
		return $amount;
	}
}
?>