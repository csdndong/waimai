<?php
class method   extends baseclass
{
	 
	//生成签名
	function getsignature($data){ 
	 
		ksort($data); 
		$stringA = '';
		$newarray = array();
		foreach($data as $key=>$value){
 			$newarray[] = $key.'='.$value;
		}
		$stringA = implode('&',$newarray);
		 
		$apimiyao = 'GHkj2004GHkj2004GHkj2004GHkj2004';
		$stringSignTemp = $stringA.'&key='.$apimiyao;
		 print_R($stringSignTemp);
		$sign = strtoupper(md5($stringSignTemp));
 		 
		return $sign;
		
	}
	//测试退款操作
	function wxrefundpay(){
	 
	 
		$datas = array();
		$datas['appid'] = 'wx90d68db4fe91edae';
		$datas['mch_id'] = '1302694901';
		$datas['nonce_str'] = md5(time().$datas['appid'].time().$datas['mch_id']); 
   		$datas['op_user_id'] = '1302694901';
		$datas['out_refund_no'] = '812749725686';
		$datas['total_fee'] = '100';
		$datas['refund_fee'] = '100'; 
		$datas['transaction_id'] = '4003282001201705090146580408';
		$datas['sign'] = $this->getsignature($datas);
		
	    $newdataa = $this->ToXml($datas);
		#print_R($datas);
		
		$returnxml = $this->curl_post_ssl('https://api.mch.weixin.qq.com/secapi/pay/refund',   $newdataa );
		$returndata = $this->FromXml($returnxml);
		print_r($returndata);
	}

	/**
	 * 输出xml字符
	 * @throws WxPayException
	**/
	public function ToXml($datas)
	{
		if(!is_array($datas) 
			|| count($datas) <= 0)
		{
    		throw new WxPayException("数组数据异常！");
    	}
    	
    	$xml = "<xml>";
    	foreach ($datas as $key=>$val)
    	{
    		if (is_numeric($val)){
    			$xml.="<".$key.">".$val."</".$key.">";
    		}else{
    			$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
    		}
        }
        $xml.="</xml>";
        return $xml; 
	}
	
 /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
	public function FromXml($returnxml)
	{	
		if(!$returnxml){
			throw new WxPayException("xml数据异常！");
		}
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($returnxml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
		return $data;
	}
function curl_post_ssl($url, $vars, $second=30,$aHeader=array())
{
	$ch = curl_init();
	//超时时间
	curl_setopt($ch,CURLOPT_TIMEOUT,$second);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
	//这里设置代理，如果有的话
	//curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
	//curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
	
	//以下两种方式需选择一种
	
	//第一种方法，cert 与 key 分别属于两个.pem文件
	//默认格式为PEM，可以注释
	curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
	curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/plug/pay/weixin/cert/apiclient_cert.pem');
	//默认格式为PEM，可以注释
	curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
	curl_setopt($ch,CURLOPT_SSLKEY,getcwd().'/plug/pay/weixin/cert/apiclient_key.pem');
	
	//第二种方式，两个文件合成一个.pem文件
//	curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/all.pem');
 
	if( count($aHeader) >= 1 ){
		curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
	}
 
	curl_setopt($ch,CURLOPT_POST, 1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
	$data = curl_exec($ch);
	 
	if($data){
		curl_close($ch);
		return $data;
	}
	else { 
		$error = curl_errno($ch);
		echo "call faild, errorCode:$error\n"; 
		curl_close($ch);
		return false;
	}
}



	
	
	    public function adminupload()  // 会员中心申请开店
	 {
	 	 $func = IFilter::act(IReq::get('func'));
		 $obj = IReq::get('obj');
		 $uploaddir =IFilter::act(IReq::get('uploaddir'));
		 
 	   if(is_array($_FILES)&& isset($_FILES['imgFile']))
	   {
	   	 $uploaddir = empty($uploaddir)?'other':$uploaddir;
 			$uploadpath = 'images/'.$uploaddir.'/'; 
 			$upload = new upload($uploadpath);//upload 自动生成压缩图片 
			$filedir = $upload->getSigImgDir(); 
			$filedir = getImgQuanDir($filedir);
       if($upload->errno!=15&&$upload->errno!=0){
		     echo "<script>parent.".$func."(true,'".$obj."','".json_encode($upload->errmsg())."');</script>";
		   }else{
		      echo "<script>parent.".$func."(false,'".$obj."','".$filedir."');</script>";

		   }
		   exit;
	   }
	   $data['obj'] = $obj;
	   $data['uploaddir'] = $uploaddir;
	   $data['func'] = $func;
	   Mysite::$app->setdata($data);
	 }
	
	 public function saveupload()
	 {
	 	$uploaddir =IFilter::act(IReq::get('uploaddir'));
		$uploaddir = empty($uploaddir)?'other':$uploaddir;
		  $json = new Services_JSON();
		  if( $uploaddir == 'goodspub' ){
			  $uploadpath = 'images/'.$uploaddir.'/'; 
		  }else{
			    $default_cityid = Mysite::$app->config['default_cityid'];
				if( !empty($default_cityid) ){
					$uploadpath = 'images/'.$default_cityid.'/'.$uploaddir.'/'; 
				}else{
					$uploadpath = 'images/'.$uploaddir.'/'; 
				}
		  }
 		
		 $filepath = Mysite::$app->config['siteurl'].'/upload/goods/';
      $upload = new upload($uploadpath);//upload
     $filedir = $upload->getSigImgDir(); 
	 $filedir = Mysite::$app->config['imgserver'].$filedir;
     if($upload->errno!=15&&$upload->errno!=0) {
			$msg = $json->encode(array('error' => 1, 'message' => $upload->errmsg()));

		  }else{
			$msg = $json->encode(array('error' => 0, 'url' => $filedir, 'trueurl' => $upload->returninfo['name']));
		 }
		 echo $msg;
		 exit;
	 }
	 
	 
	 
	 function updateuserimg(){
 		  $_FILES['imgFile'] = $_FILES['head'];   
		$json = new Services_JSON();
		$uploadpath = 'images/user/';
 		$upload = new upload($uploadpath);
		$filedir = $upload->getSigImgDir();  
		if($upload->errno!=15&&$upload->errno!=0) {
			$this->message($upload->errmsg());

		}else{
			$data['logo'] = $filedir;
		    $this->mysql->update(Mysite::$app->config['tablepre'].'member',$data,"uid='".$this->member['uid']."'  "); 
			$filedir = getImgQuanDir($filedir);
 			$this->success($filedir);
		} 
	}
	 
	 
	 public function userupload()
	 {
	 	 $link = IUrl::creatUrl('member/login');
	 	  // if($this->member['uid'] == 0&&$this->admin['uid'] == 0)  $this->message('未登录',$link);
	 	  $_FILES['imgFile'] = $_FILES['head'];
	 	  $type = IFilter::act(IReq::get('type'));
	 	  if(empty($type)) $this->message('未定义的操作');
			$json = new Services_JSON();
			$uploadpath = 'images/other/';
 		  $upload = new upload($uploadpath);//upload
		  $filedir = $upload->getSigImgDir();
		 
		  if($upload->errno!=15&&$upload->errno!=0) {
				  $this->message($upload->errmsg());
			  }else{
				  if($type == 'userlogo'){
					 $arr['logo'] = $filedir;
					 $this->mysql->update(Mysite::$app->config['tablepre'].'member',$arr,'uid = '.$this->member['uid'].' ');
				  }elseif($type == 'goods'){
					 $shopid = ICookie::get('adminshopid');
					$gid = intval(IFilter::act(IReq::get('gid')));
					 $data['img'] = $filedir;
					$this->mysql->update(Mysite::$app->config['tablepre'].'goods',$data,"id='".$gid."' and shopid='".$shopid."'");
				  }elseif($type == 'shoplogo'){
					$shopid = ICookie::get('adminshopid');
					if(!empty($shopid)){
						$data['shoplogo'] = $filedir;
						$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$data,"id='".$shopid."'");
					}
				  }
				  
				  $filedir = getImgQuanDir($filedir);
				  $this->success($filedir);
				  
				  
			  }
	 }
	 function goodsupload(){
	 	 $link = IUrl::creatUrl('member/login');
	 	  if($this->member['uid'] == 0&&$this->admin['uid'] == 0)  $this->message('未登录',$link);
	 	 $type = IReq::get('type');
		 $goodsid =  intval(IReq::get('goodsid'));
		 $shopid = ICookie::get('adminshopid');
		 if($shopid < 0){
		   echo '无权限操作';
		   exit;
		 }
	   if(is_array($_FILES)&& isset($_FILES['imgFile']))
	   {

	  	$json = new Services_JSON();
      $uploadpath = 'upload/shop/';
		  $filepath ='/upload/shop/';
      $upload = new upload($uploadpath,array('gif','jpg','jpge','doc','png'));//upload
      $file = $upload->getfile();
      if($upload->errno!=15&&$upload->errno!=0) {
		   echo "<script>parent.uploaderror('".json_encode($upload->errmsg())."');</script>";
		  }else{
		     	 if($goodsid > 0&& $shopid > 0){
		     	 	  $data['img'] = $filepath.$file[0]['saveName'];
		          $this->mysql->update(Mysite::$app->config['tablepre'].'goods',$data,"id='".$goodsid."' and shopid='".$shopid."'");
		     	 }
		       echo "<script>parent.uploadsucess('".$filepath.$file[0]['saveName']."');</script>";
		  }
		  exit;
	   }
	   $imgurl ='';
	   if($goodsid > 0 && $type == 'goods'){
	  	  $temp = $this->mysql->select_one("select img from ".Mysite::$app->config['tablepre']."goods where id='".$goodsid."' and shopid='".$shopid."'");
	  	  $imgurl = $temp['img'];
	   }
	    Mysite::$app->setdata(array('type'=>$type,'goodsid'=>$goodsid,'imgurl'=>$imgurl));
	 }
	 
	 
	  /* 配送宝上传图片接口 */
	 function psbimgUpload(){ 
 	 
		$uploadname ='imgFile';//传入参数  用户名 
		$json = new Services_JSON();
		$uploadpath = 'upload/psbimg/';//size 获取文件大小 
		if(isset($_FILES[$uploadname])){  
		    if($_FILES[$uploadname]['error'] == 0 ){//可以上传
				$upload1 = new upload($uploadpath,array('gif','jpg','jpge','doc','png'));
				$file1 = $upload1->getfile(); 
				 
				if($upload1->errno !=15 && $upload1->errno !=0){   
					$this->message($upload1->errmsg());
				}else{ 
					$data['psbimgUploadUrl'] = Mysite::$app->config['siteurl'].'/'.$uploadpath.$file1[0]['saveName'];
					$this->success($data);
			    }
		    }else{
				$this->message('上传失败');
			}
		}else{
			$this->message('上传失败');
		}
  	}  
	  
	 
	 
	 
}



?>