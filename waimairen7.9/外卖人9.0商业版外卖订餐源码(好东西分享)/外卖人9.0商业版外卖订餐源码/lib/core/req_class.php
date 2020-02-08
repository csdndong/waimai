<?php 
class IReq
{ 
	public static function get($key, $type=false)
	{
		//默认方式
		if($type==false)
		{
			if(isset($_GET[$key])) return $_GET[$key];
			else if(isset($_POST[$key])) return $_POST[$key];
			else return null;
		}

		//get方式
		else if($type=='get' && isset($_GET[$key]))
			return $_GET[$key];

		//post方式
		else if($type=='post' && isset($_POST[$key]))
			return $_POST[$key];

		//无匹配
		else
			return null;

	} 
	public static function set($key, $value, $type='get')
	{
		//get方式
		if($type=='get')
			$_GET[$key] = $value;

		//post方式
		else if($type=='post')
			$_POST[$key] = $value;
	}
}
?>