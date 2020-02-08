<?php
/**
 * 新达达开放平台接口调用工具类
 * 详情：签名，接口调用
 * 版本：1.0
 * 日期：2016-09-10
 * 说明：
 * 以下代码只是为了方便对接商户测试而提供的样例代码，对接商户可以根据自己的需求，按照技术文档编写，代码仅供参考。
 */

class DadaOpenapi{
    
    /**
     * 达达开发者app_key
     */
    private $app_key;
    /**
     * 达达开发者app_secret
     */
    private $app_secret;

    /**
     * api url地址
     */
    private $url;

    /**
     * api版本
     */
    private $v = "1.0";

    /**
     * 数据格式
     */
    private $format = "json";

    /**
     * 商户ID
     */
    private $source_id;

    /**
     * http request timeout;
     */
    private $httpTimeout = 5;

    /**
     * 请求响应返回的数据状态
     */
    private $status;

    /**
     * 请求响应返回的code
     */
    private $code;

    /**
     * 请求响应返回的信息
     */
    private $msg;

    /**
     * 请求响应返回的结果
     */
    private $result;

    /**
     * 判断求是否异常
     */
    private $isExcepet = false;

    /**
     * 异常信息
     */
    private $excepetMsg;

    /**
     * 构造函数
     * param array $config = array();
     */
    public function __construct($config){
        isset($config['app_key']) ? $this->app_key = $config['app_key'] : trigger_error('app_key不能为空', E_USER_ERROR);
        isset($config['app_secret']) ? $this->app_secret = $config['app_secret'] : trigger_error('app_secret不能为空', E_USER_ERROR);
        isset($config['url']) ? $this->url = $config['url'] : trigger_error('url不能为空', E_USER_ERROR);
        isset($config['source_id']) ? $this->source_id = $config['source_id'] : trigger_error('source_id不能为空', E_USER_ERROR);
        isset($config['v']) && $this->v = $config['v'];
        isset($config['format']) && $this->format = $config['format'];
        isset($config['timeout']) && $this->httpTimeout = intval($config['timeout']);
    }

    /**
     * 请求调用api
     * data:业务数据
     * @return bool
     */
    public function makeRequest($data){
        $reqParams = $this->bulidRequestParams(json_encode($data));
        $resp = $this->getHttpRequestWithPost($this->url, json_encode($reqParams));
        $this->parseResponseData($resp);
        return $this->isExcepet;
    }

    /**
     * 构造请求数据
     * data:业务参数，json字符串
     */
    public function bulidRequestParams($body){
        $requestParams = array();
        $requestParams['app_key'] = $this->app_key;
        $requestParams['body'] = $body;
        $requestParams['format'] = $this->format;
        $requestParams['v'] = $this->v;
        $requestParams['source_id'] = $this->source_id;
        $requestParams['timestamp'] = time();
        $requestParams['signature'] = $this->_sign($requestParams);
        return $requestParams;
    }

    /**
     * 签名生成signature
     */
    public function _sign($data){

        //1.升序排序
        ksort($data);

        //2.字符串拼接
        $args = "";
        foreach ($data as $key => $value) {
            $args.=$key.$value;
        }
        $args = $this->app_secret.$args.$this->app_secret;

        //3.MD5签名,转为大写
        $sign = strtoupper(md5($args));

        return $sign;
    }
    

    /**
     * 发送请求,POST
     * @param $url 指定URL完整路径地址
     * @param $data 请求的数据
     */
    public function getHttpRequestWithPost($url, $data){
        // json
        $headers = array(
            'Content-Type: application/json',
        );
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $resp = curl_exec($curl);
        //var_dump( curl_error($curl) );//如果在执行curl的过程中出现异常，可以打开此开关查看异常内容。
        $info = curl_getinfo($curl);
        curl_close($curl);
        if (isset($info['http_code']) && $info['http_code'] == 200) {
            return $resp;
        }
        return '';
    }

    /**
     * 解析响应数据
     * @param $arr返回的数据
     * 响应数据格式：{"status":"success","result":{},"code":0,"msg":"成功"}
     */
    public function parseResponseData($arr){
        if (empty($arr)) {
            $this->isExcepet = true;
            $this->excepetMsg = "接口请求失败";
        }else{
            $data = json_decode($arr, true);
           
            $this->status = $data['status'];
            $this->result = $data['result'];
            $this->code = $data['code'];
            $this->msg = $data['msg']; 
        }
        return true;
    }

    /**
     * 获取返回code
     */
    public function getCode(){
        return $this->code;
    }

    /**
     * 获取返回status
     */
    public function getStatus(){
        return $this->status;
    }

    /**
     * 获取返回msg
     */
    public function getMsg(){
        return $this->msg;
    }

    /**
     * 获取返回result
     */
    public function getResult(){
        return $this->result;
    }

}
