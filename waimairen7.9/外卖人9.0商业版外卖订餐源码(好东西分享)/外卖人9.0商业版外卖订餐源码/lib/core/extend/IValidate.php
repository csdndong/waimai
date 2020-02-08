<?php
 
class IValidate
{		
     
    public static function email($str='')
    {
        return (bool)preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)+$/i',$str);
    }
    
    public static function qq($str='')
    {
        return (bool)preg_match('/^[1-9][0-9]{4,}$/i',$str);
    }
    
    public static function id($str='')
    {
        return (bool)preg_match('/^\d{15}(\d{2}[0-9x])?$/i',$str);
    }
     
    public static function ip($str='')
    {
        return (bool)preg_match('/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/i',$str);
    }
     
    public static function zip($str='')
    {
        return (bool)preg_match('/^\d{6}$/i',$str);
    }
     
    public static function len($str, $min, $max)
    {
        if(is_int($str)) return $str >= $min && $str <= $max;
        if(is_string($str))return IString::getStrLen($str) >= $min && IString::getStrLen($str) <= $max;
        return false;
    }
    
    public static function phone($str='')
    {
        return (bool)preg_match('/^((\d{3,4})|\d{3,4}-)?\d{7,8}(-\d+)*$/i',$str);
    }
    
    public static function mobi($str='')
    {
		    return (bool)preg_match("!^[0-9]{1,20}$!",$str);
    }
    //真实手机号  11位
    public static function suremobi($str='')
    {
         return (bool)preg_match("/^1[0-9]{1}[0-9]{1}[0-9]{8}$/",$str);
//    	 return (bool)preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{9}$|14[0-9]{9}$|17[0-9]{9}$/",$str);
    }
   
    public static function account($str, $minlen=4, $maxlen=16)
    {
        return (bool)preg_match('/^[a-zA-Z][a-zA-Z0-9_]{'.$minlen.','.$maxlen.'}$/i',$str);
    }
   
    public static function url($str='')
    {
        return (bool)preg_match('/^[a-zA-z]+:\/\/(\w+(-\w+)*)(\.(\w+(-\w+)*))+(\/?\S*)?$/i',$str);
    }
    
    public static function check($reg, $str='')
    {
        return (bool)preg_match('/^'.$reg.'$/i',$str);
    }
	 
    public static function required($str)
    {
         return (bool)preg_match('/\S+/i',$str);
    }
}
?>
