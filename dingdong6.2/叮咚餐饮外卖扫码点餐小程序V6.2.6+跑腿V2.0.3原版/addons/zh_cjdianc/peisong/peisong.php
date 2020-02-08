<?php



include_once "DadaOpenapi.php";
include_once "KfwOpenapi.php";

class Peisong

{

	//获取城市对应code	
	public static function getCityCode($config,$city){
	 $obj = new DadaOpenapi($config);
       $data=array();
       $reqStatus = $obj->makeRequest($data);
       if (!$reqStatus) {
        if ($obj->getCode() == 0) {
          $arr=$obj->getResult();
          foreach($arr as $v){
            if($city==$v['cityName']){
              $cityCode=$v['cityCode'];
            }
          }
          return $cityCode;
	}
	}
}
	//处理接口

public  static function requestMethod ($config,$data){
	$obj = new DadaOpenapi($config);
	$reqStatus = $obj->makeRequest($data);
	if (!$reqStatus) {
		if ($obj->getCode() == 0) {
			return $obj->getResult();
		}else{
			return  '错误码'.$obj->getCode();
		}         
	}

}


//腾讯转百度坐标转换
public static function coordinate_switchf($a,$b){
  $x = (double)$b ;
  $y = (double)$a;
  $x_pi = 3.14159265358979324;
  $z = sqrt($x * $x+$y * $y) + 0.00002 * sin($y * $x_pi);
  $theta = atan2($y,$x) + 0.000003 * cos($x*$x_pi);
  $gb = number_format($z * cos($theta) + 0.0065,6);
  $ga = number_format($z * sin($theta) + 0.006,6);
 
  return ['Latitude'=>$ga,'Longitude'=>$gb];
 
}











}











































































