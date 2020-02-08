<?php  
 $alipay_config['business']		= '';
 $alipay_config['currency_code']			= '';
 $alipay_config['input_charset']= strtolower('utf-8');
 $alipay_config['transport'] = 'http';
 $alipay_config['cacert']    = getcwd().'\\cacert.pem';
 $alipay_config['clientId']		= '';
 $alipay_config['secret']		= '';
 $alipay_config['is_sanbox']		= '';
 $notify_url= 'http://m6.waimairen.com/plug/pay/paypal/notify_url.php';
 $return_url= 'http://m6.waimairen.com/plug/pay/paypal/return_url.php';
 $cancel_return= 'http://m6.waimairen.com/plug/pay/paypal/cancel_return.php';
?>