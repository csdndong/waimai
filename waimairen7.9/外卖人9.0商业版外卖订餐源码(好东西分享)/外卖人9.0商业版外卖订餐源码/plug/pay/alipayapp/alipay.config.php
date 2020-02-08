<?php  
 $alipay_config['partner']		= '2088611693765464';
 $alipay_config['private_key_path']			= 'key/rsa_private_key.pem';
 $alipay_config['ali_public_key_path']    = 'key/alipay_public_key.pem';
 $alipay_config['sign_type']= strtoupper('RSA');
 $alipay_config['input_charset'] = strtolower('utf-8');
 $alipay_config['cacert']    = getcwd().'\\cacert.pem';
 $alipay_config['transport']    = 'http';
?>