<?php  
 $alipay_config['partner']		= '2088611693765464';
 $alipay_config['key']			= 'be7fowano2xohl7cti4gccq0rr5pcgz2';
 $alipay_config['sign_type']    = strtoupper('MD5');
 $alipay_config['input_charset']= strtolower('utf-8');
 $alipay_config['transport'] = 'http';
 $alipay_config['cacert']    = getcwd().'\\cacert.pem';
 $notify_url= 'http://m6.waimairen.com/plug/pay/alipay/notify_url.php';
 $return_url= 'http://m6.waimairen.com/plug/pay/alipay/return_url.php';
 $seller_email= 'yamaida520@163.com';
?>