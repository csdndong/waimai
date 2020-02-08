<?php

class Upt {


public static function request_post($url = '', $post_data = array()) {
    if (empty($url) || empty($post_data)) {
        return false;
    }
    
    $arr = [];
    foreach ($post_data as $key => $value) {
      $arr[] = $key.'='.$value;
    }

    $curlPost = implode('&', $arr);
/*
    var_dump($arr);
    echo '<br />';
    var_dump('签名后:'.$curlPost);*/

    $postUrl = $url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$postUrl);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;

	}


public static function guid(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);                
        return $uuid;
    }
}


public static function sign($data, $appKey) {
  $arr = [];
  foreach ($data as $key => $value) {
    $arr[] = $key.'='.$value;
  }

  $arr[] = 'key='.$appKey;
  $str = strtoupper(implode('&', $arr));
  //$str = http_build_query($arr, '&');
 /* var_dump('签名前:'.$str);
  echo "<br />";*/
  return strtoupper(md5($str));
	}







}