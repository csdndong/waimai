<?php





class Peisong

{

//计算配送费
public  static function getMoney($lat1, $lng1, $lat2, $lng2,$uniacid){

    $earthRadius = 6367000; //approximate radius of earth in meters   
    $lat1 = ($lat1 * pi() ) / 180;   
    $lng1 = ($lng1 * pi() ) / 180;   
    $lat2 = ($lat2 * pi() ) / 180;   
    $lng2 = ($lng2 * pi() ) / 180;   
    $calcLongitude = $lng2 - $lng1;   
    $calcLatitude = $lat2 - $lat1;   
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);   
    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));   
    $calculatedDistance =round($earthRadius * $stepTwo)/1000;
    $jl=pdo_getall('cjpt_fee',array('uniacid'=>$uniacid),array(),'','end ASC');
   // var_dump($jl);die;
    foreach ($jl as $key => $value) {
      if($value['end']>=$calculatedDistance){
        return $value['money'];   
        break;
      }
     
    }



}



    /**
     * 发送请求,POST
     * @param $url 指定URL完整路径地址
     * @param $data 请求的数据
     */
    public static function requestWithPost($url, $data){
        // json

        $headers = array(
            'Content-Type: application/json',
            );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }





}











































































