<?php

/**

 * 快服务开放平台接口调用工具类

 * 详情：签名，接口调用

 * 版本：1.0

 * 日期：2016-09-10

 * 说明：

 * 以下代码只是为了方便对接商户测试而提供的样例代码，对接商户可以根据自己的需求，按照技术文档编写，代码仅供参考。

 */



class KfwOpenapi{

    

    /**

     * 快服务开发者app_key

     */

    private $app_key;

    /**

     * 快服务开发者app_secret

     */

    private $app_secret;



    /**

     * api url地址

     */

    private $url;



    /**

     * 用户授权 token

     */

    private $access_token;



    /**

     * 数据格式

     */

    private $format = "json";



    /**

     * 商户ID

     */

    private $openid ;



    /**

     * http request timeout;

     */

    private $httpTimeout = 5;



   



    /**

     * 签名生成signature

     */

    public function getSign($data,$app_secret){
        //1.升序排序
        ksort($data);
        //2.字符串拼接
        $args = "";
        foreach ($data as $key => $value) {
            $args.=$key."=".$value."&";
        }
        $args=rtrim($args, "&");
         $args=$args."&key=".$app_secret;
         //var_dump($args);die;
        //3.MD5签名,转为大写
        $sign = strtoupper(md5($args));
        return $sign;
    }

    /**
     * 发送请求,POST
     * @param $url 指定URL完整路径地址
     * @param $data 请求的数据
     */
    public function requestWithPost($url, $data){
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

