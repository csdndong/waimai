<?php  
$alipay_config['partner']		= '2088611693765464'; 
$alipay_config['key']			= 'be7fowano2xohl7cti4gccq0rr5pcgz2'; 
$alipay_config['private_key_path']	= '/var/www/html/public_html/plug/pay/alimobile/key/rsa_private_key.pem';
$alipay_config['ali_public_key_path']= '/var/www/html/public_html/plug/pay/alimobile/key/alipay_public_key.pem';
$alipay_config['sign_type']    = '0001';
$alipay_config['input_charset']= 'utf-8'; 
$alipay_config['cacert']    = getcwd().'\\cacert.pem';
$alipay_config['transport']    = 'http';
$alipay_config['notify_url'] = 'http://m6.waimairen.com/plug/pay/alimobile/notify_url.php';
$alipay_config['return_url'] = 'http://m6.waimairen.com/plug/pay/alimobile/call_back_url.php';
$alipay_config['error_url'] = 'http://m6.waimairen.com/plug/pay/alimobile/error.php';
$alipay_config['seller_email'] = 'yamaida520@163.com';
?>