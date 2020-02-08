<?php
 
class IClient
{
	 
	public static function getIp()
	{
	    $realip = NULL;
	    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	    {
	    	$ipArray = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
	    	foreach($ipArray as $rs)
	    	{
	    		$rs = trim($rs);
	    		if($rs != 'unknown')
	    		{
	    			$realip = $rs;
	    			break;
	    		}
	    	}
	    }
	    else if(isset($_SERVER['HTTP_CLIENT_IP']))
	    {
	    	$realip = $_SERVER['HTTP_CLIENT_IP'];
	    }
	    else
	    {
	    	$realip = $_SERVER['REMOTE_ADDR'];
	    }

	    preg_match("/[\d\.]{7,15}/", $realip, $match);
	    $realip = !empty($match[0]) ? $match[0] : '0.0.0.0';
	    return $realip;
	}

	 
	public static function getPreUrl()
	{
		return $_SERVER['HTTP_REFERER'];
	}
 
	public static function getTime()
	{
		if(IServer::isGeVersion('5.1.0'))
			return $_SERVER['REQUEST_TIME'];
		else
			return time();
	}
}
?>