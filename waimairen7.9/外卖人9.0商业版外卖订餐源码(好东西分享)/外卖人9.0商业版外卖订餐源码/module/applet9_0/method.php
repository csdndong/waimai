<?php
/*
*   method 方法  包含所有会员相关操作
    管理员/会员  添加/删除/编辑/用户登录
    用户日志其他相关连的通过  memberclass关联
*/
class method   extends  baseclass
{  
     public $CITY_ID;
     public $platpssetinfo;
     public $is_pass_applet ;
     public $default_adcode ;
	 
	public function __construct()
	{
		$this->mysql =  new mysql_class();  
		$datacache = Mysite::$app->config['datacache']; 
		if($datacache == 1){
			include_once(hopedir."/lib/core/extend/mysql_classcache.php");
			$this->mysqlcache = new mysql_classcache();
		}else{
			$this->mysqlcache = $this->mysql; 
		}
		$this->is_pass_applet = Mysite::$app->config['is_pass_applet'];
        $this->default_adcode = Mysite::$app->config['default_cityid'];
	}
	 
    public function curl_get_content($url)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_REFERER,'');// 设置Referer
        curl_setopt($curl, CURLOPT_POST, 0); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo;
    }
    //获取微信小程序配置信息
    function getconfig(){
       $data['appid'] =  Mysite::$app->config['appletAppID'];
       $data['secret'] = Mysite::$app->config['appletsecret'];
       $data['mapkey'] = Mysite::$app->config['appletmapkey']; 

        $this->success($data);
    }
    //根据adcode获取城市信息
     public function getOpenCity(){
		 $adcode = IFilter::act(IReq::get('adcode'));
		 if( $this->is_pass_applet == 0 || empty($adcode) ){
			 $adcode = $this->default_adcode;
		 }
		 //$adcode = '410100';
  		 if( !empty($adcode) ){
				$areacodeone =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."areacode where id=".$adcode."  ");
				#print_r($areacodeone);
				if( !empty($areacodeone) ){
					$adcodeid = $areacodeone['id'];
					$pid = $areacodeone['pid'];
   					$info = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where (adcode=".$adcodeid." or procode=".$adcodeid." )  ");
					#print_r($info);
					if( empty($info) ){
                        $info = $this->mysql->select_one("select `name`,`adcode` from ".Mysite::$app->config['tablepre']."area where adcode=".$pid."  ");
					}
					#print_r($info);
                    if( !empty($info) ){
                        $platpssetinfo = $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$info['adcode']."'   ");
                        if( !empty($platpssetinfo) ){
                            $platpssetinfo['cityname'] = $info['name'];
                            $this->platpssetinfo = $platpssetinfo;
                            $this->CITY_ID = $platpssetinfo['cityid'];
                        }
                    }else{
                        $this->message("获取城市信息失败");
                    }
				}else{
					$this->message("获取城市adcode失败");
				}
			}else{
				$this->message("城市adcode为空");
			}
 	 }
    //获取首页顶部分类数据
	  function checkopencity(){ 
			$this->getOpenCity();
			$moretypelist =array();
            if( !empty($this->platpssetinfo) ){
//                $moretypelist = $this->mysql->getarr("select `img`,`name`,`activity`,`param` from ".Mysite::$app->config['tablepre']."appadv where type=2 and cityid='".$this->CITY_ID."'  and ( activity = 'waimai' or activity = 'market') order by orderid  asc");
         #       $moretypelist = $this->mysql->getarr("select `img`,`name`,`activity`,`param` from ".Mysite::$app->config['tablepre']."appadv where type=2 and ( cityid='".$this->CITY_ID."' or  cityid = 0 )  and is_show = 1 order by orderid  asc");
            
			 $moretypelist = $this->mysql->getarr("select `img`,`name`,`activity`,`param` from ".Mysite::$app->config['tablepre']."appadv where type=2 and  cityid='".$this->CITY_ID."'  and ( activity = 'waimai' or activity = 'market' or activity = 'paotui')  and is_show = 1 order by orderid  asc");
            
			}
				 
 			$newmoretypelist = array();
			if( !empty($moretypelist) ){
				foreach($moretypelist as $key=>$value){
					$value['img'] = getImgQuanDir($value['img']);
					$newmoretypelist[] = $value;
				}
			}
			
          $data['moretypelist'] = array();
          $data['setinfo'] = $this->platpssetinfo;
          $data['typecount'] = count($newmoretypelist);
          if(count($newmoretypelist) > 0){
              $data['moretypelist'][]  = array_slice($newmoretypelist,0,10);
              if(count($newmoretypelist) > 10){
                  $data['moretypelist'][]  = array_slice($newmoretypelist,10,10);
              }
          }

          $advlist = $this->mysql->getarr("select `linkurl`,`img` from ".Mysite::$app->config['tablepre']."adv where advtype='weixinlb' and  module='".Mysite::$app->config['sitetemp']."' and cityid='".$this->CITY_ID."'  and is_show = 1 limit 0,5");
          $advArr = array();
          if( !empty($advlist) ){
              foreach($advlist as $va){
                  $va['img'] = getImgQuanDir($va['img']);
                  $advArr[] = $va;
              }
          }
          $data['advlist'] = $advArr;
		  
		  
		  
		  $lng = trim(IReq::get('lng'));
          $lat = trim(IReq::get('lat'));
          $lng = empty($lng)?0:$lng;
          $lat =empty($lat)?0:$lat;
		  
		//$lng = '113.543806';
        //$lat ='34.80233';
		/*首页分类背景   分类字体颜色   分类下一张图片*/		
		//分类背景设置
		$flinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."stationskin  where cityid = ".$this->CITY_ID." and type = 1 ");
		//分类和网站通知中间图片设置	
		$flxinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."stationskin  where cityid = ".$this->CITY_ID." and type = 2 ");	
		$data['flfontcolor'] = '';
		$data['flimgurl'] = '';
		if( !empty($flinfo) ){
			$data['flfontcolor'] = empty($flinfo['color'])?'#000000':$flinfo['color'];
			$data['flimgurl'] = $flinfo['is_show'] == 1? getImgQuanDir($flinfo['imgurl']):'';	
		}
		$flximginfo_img = '';
		if( !empty($flxinfo)  && $flxinfo['is_show']== 1 && !empty($flxinfo['imgurl'])  ){
			 $flximginfo_img = getImgQuanDir($flxinfo['imgurl']);
		} 
 		
		$data['flximginfo_img'] = $flximginfo_img;
		
		$juansetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 2 or name = '注册送优惠券' " );  	   
        $juaninfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan where type = 2 or name= '注册送优惠券' order by id asc " );	   
		if($juansetinfo['status'] ==1 && !empty($juaninfo)){
			$data['regimg'] = getImgQuanDir(Mysite::$app->config['regimg']);
		}else{
			$data['regimg'] = '';
		}
		  

          $data['notice'] = '';
          $notice = $this->mysql->select_one("select `title` from ".Mysite::$app->config['tablepre']."information where type=1 and ( cityid = '".$this->CITY_ID."' or cityid = 0  ) order by orderid asc ");
          if(!empty($notice)){
              $data['notice'] = $notice['title'];
          }

          /* $ztymode = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."ztymode where cityid='".$this->CITY_ID."'  ");
          $data['ztymode'] = $ztymode['type'];

          if($ztymode['type'] == 1){
              $limits = 5;
          }else if($ztymode['type'] == 2){
              $limits = 4;
          }else{
              $limits = 3;
          }
          $ztylist = $this->mysql->getarr("select `id`,`indeximg` from ".Mysite::$app->config['tablepre']."specialpage where is_show = 1  and is_bd = 2  and ( cityid='".$this->CITY_ID."' or  cityid = 0 ) and ztystyle={$ztymode['type']} order by orderid  asc limit {$limits} ");*/
          $ztyArr = array();
          /*if(!empty($ztylist)){
              foreach($ztylist as $vz){
                  $vz['indeximg'] = Mysite::$app->config['siteurl'].$vz['indeximg'];
                  $ztyArr[] = $vz;
              }
          } */
          $data['ztylist'] = $ztyArr;

          $advlist2 = $this->mysql->getarr("select `linkurl`,`img` from ".Mysite::$app->config['tablepre']."adv where advtype='weixinlb2' and  module='".Mysite::$app->config['sitetemp']."' and cityid='".$this->CITY_ID."'  and is_show = 1 limit 0,5");
          $advArr2 = array();
          if( !empty($advlist2) ){
              foreach($advlist2 as $va2){
                  $va2['img'] = getImgQuanDir($va2['img']);
                  $advArr2[] = $va2;
              }
          }
          $data['advlist2'] = $advArr2;

          
          $where =  Mysite::$app->config['plateshopid'] > 0 ? ' and id != '.Mysite::$app->config['plateshopid'] .' ': '';
          $where .= " and admin_id=".$this->CITY_ID."   ";
          $where .= ' and SQRT((`lat` -'.$lat.') * (`lat` -'.$lat.' ) + (`lng` -'.$lng.' ) * (`lng` -'.$lng.' )) < (`pradiusa`*0.01094-0.01094) ';
          $fyshoplist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shop where is_pass = 1 and isforyou = 1  and endtime > ".time()."  ".$where."  order by sort asc limit 6 ");
          $fyshopArr = array();
          if(!empty($fyshoplist)){
              foreach($fyshoplist as $tf){
                  $tf['shoplogo'] = empty($tf['shoplogo']) ? Mysite::$app->config['shoplogo'] : $tf['shoplogo'];
                  $tf['shoplogo'] = getImgQuanDir($tf['shoplogo']);
                  $fyshopArr[] = $tf;
              }
          }
          $data['fyshoplist'] = $fyshopArr;

          $wxkefu_open = 0;
          $wxkefu_logo = '';
          $wxkefu_ewm = '';
          $wxkefu_phone = '';
          if(!empty($this->platpssetinfo)){
              if($this->platpssetinfo['wxkefu_open'] == 1){
                  $wxkefu_open = 1;
              }
              if(!empty($this->platpssetinfo['wxkefu_ewm'])){
                  $wxkefu_ewm = getImgQuanDir($this->platpssetinfo['wxkefu_ewm']);
                  $wxkefu_logo = getImgQuanDir($this->platpssetinfo['wxkefu_logo']);
              }
              if(!empty($this->platpssetinfo['wxkefu_phone'])){
                  $wxkefu_phone = $this->platpssetinfo['wxkefu_phone'];
              }
          }
          $data['wxkefu_open'] = $wxkefu_open;
          $data['wxkefu_ewm'] = $wxkefu_ewm;
          $data['wxkefu_phone'] = $wxkefu_phone;
          $data['wxkefu_logo'] = $wxkefu_logo;
		$data['is_applet_examine'] = empty(Mysite::$app->config['is_pass_applet'])?0:Mysite::$app->config['is_pass_applet'];
		$siteurl = Mysite::$app->config['siteurl'];
		$siteurlarr = explode('//',$siteurl);
		$data['applet_url'] = 'https://'.$siteurlarr[1].'/index.php?ctrl=applet&action=indexcontent';
		$data['is_show_weather'] = empty(Mysite::$app->config['is_open_weather'])?0:Mysite::$app->config['is_open_weather'];
		$data['weatherinfo'] = $this->getweatherinfo($lat,$lng);
		$shangou = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."btninfo  where cityid = '".$this->CITY_ID."' and name = 'shangou' ");
		$paotui = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."btninfo  where cityid = '".$this->CITY_ID."' and name = 'paotui' ");
		$data['shangou'] = empty($shangou['is_show'])?0:$shangou['is_show'];
		$data['paotui'] = empty($paotui['is_show'])?0:$paotui['is_show'];
        $this->success($data);
	}
    //用户判断登录
    public function setwxlogin(){
        $code = IFilter::act(IReq::get('code'));
        $appid = IFilter::act(IReq::get('appid'));
        $secret = IFilter::act(IReq::get('secret'));
        $token_link = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
        $token =json_decode($this->curl_get_content($token_link), TRUE);
        if(isset($token['errcode'])){
            $this->message($token['errmsg']);
        }else{
            $wxoauth['openid'] = $token['openid'];
        }
		$pidinfo = $this->mysql->select_one("select fxpid from ".Mysite::$app->config['tablepre']."fxpid where openid ='".$wxoauth['openid']."' ");
        $wxuser['sex'] = IFilter::act(IReq::get('gender'));
        $wxoauth['username'] = IFilter::act(IReq::get('nickName'));
        $wxoauth['imgurl'] = IFilter::act(IReq::get('avatarUrl'));
        $oauthinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxuser where openid='".$wxoauth['openid']."'  ");
        if(empty($oauthinfo)){//写用户数据
            $arr['username'] = $wxoauth['openid'];
            $arr['phone'] = '';
            $arr['address'] = '';
            $arr['password'] = md5($wxoauth['openid']);
            $arr['email'] = '';
            $arr['creattime'] = time();
            $arr['score'] = empty(Mysite::$app->config['regesterscore']) ? 0 :Mysite::$app->config['regesterscore'];
            $arr['logintime'] = time();
            $arr['loginip'] ='';
            $arr['group'] = 10;
            $arr['logo'] = $wxoauth['imgurl'];
            $arr['sex'] = $wxuser['sex'];  //用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
            $newusername = $wxoauth['username'];
            $checkusername ='xxx';
            $k = 0;
            while(!empty($checkusername)){
                $newusername = $k==0? $newusername:$newusername.$k;
                $checkusername = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where username ='".$newusername."' ");
                $k = $k+1;
                if(empty($checkusername)){
                    break;
                }
            }
            $arr['username'] = $newusername;
			if($pidinfo['fxpid'] > 0 ){
				$arr['fxpid'] = $pidinfo['fxpid'];
			}
            $this->mysql->insert(Mysite::$app->config['tablepre']."member",$arr);
            $uid = $this->mysql->insertid();
            $userid = $uid;
            $mbdata['uid'] = $uid;
            $mbdata['openid'] = $wxoauth['openid'];
            $mbdata['is_bang'] = 0;
            $this->mysql->insert(Mysite::$app->config['tablepre'].'wxuser',$mbdata);
        }else{//更新用户数据
            $userid = $oauthinfo['uid'];
            $membercheck = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid ='".$oauthinfo['uid']."' ");
            if(!empty($membercheck)){
                if(empty($membercheck['username'])){
                    $newusername = $wxoauth['username'];
                    $checkusername ='xxx';
                    $k = 0;
                    while(!empty($checkusername)){
                        $newusername = $k==0? $newusername:$newusername.$k;
                        $checkusername = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where username ='".$newusername."' ");
                        $k = $k+1;
                        if(empty($checkusername)){
                            break;
                        }
                    }
                    $cnewdata['username'] = $newusername;
                }
                $cnewdata['logo'] = $wxoauth['imgurl'];
                $cnewdata['sex'] = $wxuser['sex'];
                $loginscore = Mysite::$app->config['loginscore'];
				if($loginscore!=0){
					$checktime = date('Y-m-d',time());
					$checktime = strtotime($checktime);
					$checklog = $this->mysql->select_one("select sum(result) as jieguo from ".Mysite::$app->config['tablepre']."memberlog where type = 1 and   userid = ".$oauthinfo['uid']." and addtype =1 and  addtime >= ".$checktime);
					//print_r($checklog);exit;
					$maxdayscore = Mysite::$app->config['maxdayscore'];
					if($maxdayscore > 0){
						$checkguo = $checklog['jieguo']+$loginscore;
						if($checkguo < Mysite::$app->config['maxdayscore']){
							$scoreadd = $loginscore;
						}elseif(Mysite::$app->config['maxdayscore'] > $checklog['jieguo']){
							//最大指 大于 已增指
							$scoreadd = Mysite::$app->config['maxdayscore'] - $checklog['jieguo'];
						}else{
							$scoreadd = 0;
						}
					}
				}
                $cnewdata['score'] = $membercheck['score'] + $scoreadd;
				if($pidinfo['fxpid'] > 0 ){
					$cnewdata['fxpid'] = $pidinfo['fxpid'];
				}
                $this->mysql->update(Mysite::$app->config['tablepre'].'member',$cnewdata,"uid='".$oauthinfo['uid']."'");
				if($scoreadd>0){
					$this->memberCls->addlog($userid,1,1,$scoreadd,'每天登录','用户登录赠送积分'.$scoreadd.'总积分'.$cnewdata['score'],$cnewdata['score']);
				}				
            }else{
                $arr['username'] = $wxoauth['openid'];
                $arr['phone'] = '';
                $arr['address'] = '';
                $arr['password'] = md5($wxoauth['openid']);
                $arr['email'] = '';
                $arr['creattime'] = time();
                $arr['score'] = empty(Mysite::$app->config['regesterscore']) ? 0 :Mysite::$app->config['regesterscore'];
                $arr['logintime'] = time();
                $arr['loginip'] ='';
                $arr['group'] = 10;
                $arr['logo'] = $wxoauth['imgurl'];
                $arr['sex'] = $wxoauth['sex'];  //用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
                $arr['uid'] = $oauthinfo['uid'];
                $newusername = $wxoauth['username'];
                $checkusername ='xxx';
                $k = 0;
                while(!empty($checkusername)){
                    $newusername = $k==0? $newusername:$newusername.$k;
                    $checkusername = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where username ='".$newusername."' ");
                    $k = $k+1;
                    if(empty($checkusername)){
                        break;
                    }
                }
                $arr['username'] = $newusername;
				if($pidinfo['fxpid'] > 0 ){
					$arr['fxpid'] = $pidinfo['fxpid'];
				}
                $this->mysql->insert(Mysite::$app->config['tablepre']."member",$arr);
                $uid = $this->mysql->insertid();
                $userid = $uid;
            }
        }
        $data['userinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid ='".$userid."' ");
        $this->success($data);
    }
    //获取用户信息
    public function getuserinfo(){
        $userid = intval(IReq::get('userid'));
        if(empty($userid)){
            $this->message("用户ID为空");
        }
        $data['userinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid ='".$userid."' ");
        if(empty($data['userinfo'])){
            $this->message("用户不存在");
        }
        $juanshu = $this->mysql->counts("select `id`  from ".Mysite::$app->config['tablepre']."juan where uid='".$userid."' and uid >0 and status < 2 and endtime > ".time());
        $data['juanshu'] = empty($juanshu) ? 0 : $juanshu;
		$data['is_open_distribution'] = isset(Mysite::$app->config['is_open_distribution'])?Mysite::$app->config['is_open_distribution']:0;
		if($data['userinfo']['admin_id']>0){
			$shangou = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."btninfo  where cityid = '".$data['userinfo']['admin_id']."' and name = 'shangou' ");
			$paotui = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."btninfo  where cityid = '".$data['userinfo']['admin_id']."' and name = 'paotui' ");
			$data['shangou'] = empty($shangou['is_show'])?0:$shangou['is_show'];
			$data['paotui'] = empty($paotui['is_show'])?0:$paotui['is_show'];
		}else{
			$data['shangou'] = 0;
			$data['paotui'] = 0;
		}
        $this->success($data);
    }
	  //获取用户充值信息以及余额变动信息
    public function memcard(){
        $userid = intval(IReq::get('userid'));
        if(empty($userid)){
            $this->message("用户ID为空");
        }
        $data['userinfo'] = $this->mysql->select_one("select cost from ".Mysite::$app->config['tablepre']."member where uid ='".$userid."' ");
        if(empty($data['userinfo'])){
            $this->message("用户不存在");
        }
		$data['rechargelist'] = array();
		$rechargelist = $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."rechargecost where cost > 0 order by cost asc limit 0,10000");
		if(!empty($rechargelist)){
			foreach($rechargelist as $key=>$value ){
				$totalsendcost = '';
				if( $value['is_sendcost'] == 1 ){
					$totalsendcost = $totalsendcost+$value['sendcost'];
				}
				if( $value['is_sendjuan'] == 1 ){
					$totalsendcost = $totalsendcost+$value['sendjuancost'];
				}
				$value['totalsendcost'] = $totalsendcost;
				$data['rechargelist'][] = $value;
			}
		}
		$data['costloglist'] = array();
		$costloglist = $this->mysql->getarr("select id,addtime,addtype,result,title from ".Mysite::$app->config['tablepre']."memberlog where userid = ".$userid." and  type = 2  order by addtime desc ");
		if(!empty($costloglist)){
			foreach($costloglist as $k=>$val){
				$val['addtime'] = date('Y-m-d H:i',$val['addtime']);
				if($val['addtype']==1){
					$val['result'] = '+'.$val['result'];
				}else{
					$val['result'] = '-'.$val['result'];
				}
				$data['costloglist'][] = $val;
			}	
		}
        $this->success($data);
    }
    //获取openid
    public function getwxapi(){
        $code = IFilter::act(IReq::get('code'));
        $appid = IFilter::act(IReq::get('appid'));
        $secret = IFilter::act(IReq::get('secret'));
        $token_link = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
        $token =json_decode($this->curl_get_content($token_link), TRUE);
        if(isset($token['errcode'])){
            $this->message($token['errmsg']);
        }else{
            $data['openid'] = $token['openid'];
            $this->success($data);
        }
    }
    //搜索 商家和商品页面
    function search(){
        $data['searchwords'] = array();
        if(!empty(Mysite::$app->config['searchwords'])){
            $data['searchwords'] = unserialize(Mysite::$app->config['searchwords']);
        }
        $this->success($data);
    }

    //店铺列表数据
    function indexshoplistdata(){		// 首页获取附近商家列表（外卖和超市）
		$this->getOpenCity();
		 #print_r($this->platpssetinfo);exit;
		$cxsignlist = $this->mysql->getarr("select `id`,`imgurl` from ".Mysite::$app->config['tablepre']."goodssign where type='cx' order by id desc limit 0, 100");
		$cxarray  =  array();
		foreach($cxsignlist as $key=>$value){
		   $cxarray[$value['id']] = $value['imgurl'];
		}
        $shopsearch = IFilter::act(IReq::get('searchname'));
        $shopsearch	= urldecode($shopsearch);
		$limitwhere = array();
         $pagesize = 30;
        if(!empty($shopsearch)){
             $pagesize = 100;
			 $limitwhere['search_input'] = $shopsearch;
        }
		$lng = trim(IReq::get('lng'));
		$lat = trim(IReq::get('lat'));
        $lng = empty($lng)?0:$lng;
        $lat =empty($lat)?0:$lat;
		$limitjuli = 0;
		if( $this->is_pass_applet == 0 ){ 
			$limitjuli = 1;
			$lat = Mysite::$app->config['maplat'];
			$lng = Mysite::$app->config['maplng'];
			$this->CITY_ID = Mysite::$app->config['default_cityid'];
		} 
		$source = 4; //小程序  暂时没啥用
		$order = intval(IReq::get('order'));
        $order = in_array($order,array(1,2,3))? $order:0; 
		$orderarray = array( 
			//默认距离由近到远排序					   
			'0' =>array('juli'=>'asc'),
			//按好评由高到低排序
			'1'=>array('ping'=>'desc'),
			//按起送价由低到高排序
			'2'=>array('limitcost'=>'asc'),
			//按销量由高到低排序           
			'3'=>array('sell'=>'desc'),			   
		);
		$sendtype = intval(IReq::get('sendtype')); //2平台配送   1店铺配送
		$cxtype = intval(IReq::get('cxtype'));	//1送赠品  2满减  3折扣  4免配送费 
		$limitarr['sendtype'] = $sendtype;
		$limitarr['cxtype'] = $cxtype;
		#$limitwhere['index_com'] =1;
		$datalistx = $this->Tdata($this->CITY_ID,$limitarr,$orderarray[$order],$lat,$lng,$source,$limitjuli);  
		 
		$pageinfo = new page();
		$pageinfo->setpage(intval(IReq::get('page')),$pagesize); 
		$starnum = $pageinfo->startnum();
		$newpagesize = $pageinfo->getsize();
		$templist = array();
		for($k = 0;$k<$newpagesize;$k++){
			$checknum = $starnum+$k;
			if(isset($datalistx[$checknum])){
				if( !empty( $datalistx[$checknum]['cxlist'] ) && $datalistx[$checknum]['cxcount'] > 0 ){
					$newcxlist = array();
					foreach( $datalistx[$checknum]['cxlist'] as $key=>$value){
						$value['imgurl'] = $value['imgurl'];
						$newcxlist[] = $value;
					}
					$datalistx[$checknum]['cxlist'] = $newcxlist;
				}
				$templist[] = $datalistx[$checknum];
			}else{
				break;
			}
		}  
		$data['shoplist']  = $templist;
        $data['psimg']  = getImgQuanDir(Mysite::$app->config['psimg']);
        $data['shoppsimg']  = getImgQuanDir(Mysite::$app->config['shoppsimg']);
		$platpssetinfo = $this->mysqlcache->longTime()->select_one("select is_allow_ziti from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$this->CITY_ID."'  ");
		$data['is_allow_ziti'] = empty($platpssetinfo['is_allow_ziti'])?0:$platpssetinfo['is_allow_ziti'];
		$data['ztimg']  = getImgQuanDir(Mysite::$app->config['ztimg']);
        $this->success($data);
	}
    //通用带条件的店铺列表
    function shoplistdata(){ 
        $this->getOpenCity();
        $shopshowtype = IFilter::act(IReq::get('shoptype'));
		$source = 2;
        $catid = intval(IReq::get('catid'));
        $order = intval(IReq::get('order'));
        $order = in_array($order,array(1,2,3))? $order:0; 
		$orderarray = array( 
			//默认距离由近到远排序					   
			'0' =>array('juli'=>'asc'),
			//按好评由高到低排序
			'1'=>array('ping'=>'desc'),
			//按起送价由低到高排序
			'2'=>array('limitcost'=>'asc'),
			//按销量由高到低排序           
			'3'=>array('sell'=>'desc'),			   
		);
    $sendtype = intval(IReq::get('sendtype')); //2平台配送   1店铺配送
		$cxtype = intval(IReq::get('cxtype'));	//1送赠品  2满减  3折扣  4免配送费 
		$lng = IReq::get('lng');
		$lat = IReq::get('lat');
		$lng = empty($lng)?0:$lng;
		$lat =empty($lat)?0:$lat;

		//返回的所有店铺数据
		$templist = array();
		//超市
		if($shopshowtype == 'market'){  
			$limitarr['shoptype'] = 2;
			$limitarr['shopcat'] = $catid;
			$limitarr['sendtype'] = $sendtype;
			$limitarr['cxtype'] = $cxtype;			
		    $datalistx = $this->Tdata($this->CITY_ID,$limitarr,$orderarray[$order],$lat,$lng,$source); 
			/*获取店铺*/
			$pageinfo = new page();
			$pageinfo->setpage(intval(IReq::get('page'))); 
			$starnum = $pageinfo->startnum();
			$pagesize = $pageinfo->getsize();			
			for($k = 0;$k<$pagesize;$k++){
				$checknum = $starnum+$k;
				if(isset($datalistx[$checknum])){
					$templist[] = $datalistx[$checknum];
				}else{
					break;
				}
			}  
	 	}else{
			$limitarr['shoptype'] = 1;
			$limitarr['shopcat'] = $catid;
			$limitarr['sendtype'] = $sendtype;
			$limitarr['cxtype'] = $cxtype; 
			if($shopshowtype == 'dingtai'){
				$limitarr['is_goshop'] = 1;
			}else{
				$limitarr['is_waimai'] = 1;
			} 
		    $datalistx = $this->Tdata($this->CITY_ID,$limitarr,$orderarray[$order],$lat,$lng,$source); 
			/*获取店铺*/
			$pageinfo = new page();
			$pageinfo->setpage(intval(IReq::get('page'))); 
			$starnum = $pageinfo->startnum();
			$pagesize = $pageinfo->getsize();
			
			for($k = 0;$k<$pagesize;$k++){
				$checknum = $starnum+$k;
				if(isset($datalistx[$checknum])){
					$templist[] = $datalistx[$checknum];
				}else{
					break;
				}
			}  
		}

        $data['shopshowtype'] = $shopshowtype;
        $data['shoplist']  = $templist;
        $data['psimg']  = getImgQuanDir(Mysite::$app->config['psimg']);
        $data['shoppsimg']  = getImgQuanDir(Mysite::$app->config['shoppsimg']);
		$data['ztimg']  = getImgQuanDir(Mysite::$app->config['ztimg']);
		$platpssetinfo = $this->mysqlcache->longTime()->select_one("select is_allow_ziti from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$this->CITY_ID."'  ");
		$data['is_allow_ziti'] = empty($platpssetinfo['is_allow_ziti'])?0:$platpssetinfo['is_allow_ziti'];
        $this->success($data);
    }
	function paotui(){
		$helpbuyinfo = $this->mysql->getarr("select * from " . Mysite::$app->config['tablepre'] . "helpbuy where isnotsee = 0 order by orderid asc");
        $helpmoveinfo = $this->mysql->getarr("select * from " . Mysite::$app->config['tablepre'] . "helpmove where isnotsee =  0 order by orderid asc ");
		$helpbuylist = array();
		$helpmovelist = array();
		if(!empty($helpbuyinfo)){
			foreach($helpbuyinfo as $k=>$val){
				$val['imgurl'] = getImgQuanDir($val['imgurl']); 
				$helpbuylist[] = $val;
			}
		}
		if(!empty($helpmoveinfo)){
			foreach($helpmoveinfo as $k=>$val){
				$val['imgurl'] = getImgQuanDir($val['imgurl']); 
				$helpmovelist[] = $val;
			}
		}
        $data['helpbuyinfo'] = $helpbuylist;
        $data['helpmoveinfo'] = $helpmovelist;
		$color = Mysite::$app->config['color'];
		#print_r($color);
		if($color == 'red'){
			$data['color'] = '#ff6e6e';
		}else if($color == 'green'){
			$data['color'] = '#00cd85';
		}else if($color == 'yellow'){
			$data['color'] = '#ff7600';
		}
		$data['weightlist'] = array(
			0=>'1kg',1=>'2kg',2=>'3kg',3=>'4kg',4=>'5kg',5=>'6kg',6=>'7kg',7=>'8kg',8=>'9kg',9=>'10kg',10=>'11kg',11=>'12kg',12=>'13kg',13=>'14kg',14=>'15kg',15=>'16kg',16=>'17kg',17=>'18kg',18=>'19kg',19=>'20kg'
		);
		$data['costlist'] = array(
			0=>'100元以下',1=>'100-200元',2=>'200-300元',3=>'300-400元',4=>'400-500元',5=>'500元以上'
		);
		$this->success($data);
	}
	function pthelpme(){  // 跑腿----帮我送/买
	    $id = intval(IReq::get('id'));		
		$userid = IFilter::act(IReq::get('userid'));
        $tarelist = $this->mysql->getarr("select `default`,`lng`,`lat`,`contactname`,`phone`,`bigadr`,`detailadr`,`tag`,`id`,`address` from ".Mysite::$app->config['tablepre']."address where userid='".$userid."' order by id asc limit 0,50");
        $defaultmsg = null;
       			
		if(!empty($tarelist)){
			foreach($tarelist as $value){
				if($value['default'] == 1){
					$defaultmsg = $value;						
				}
			}
		}
		
        if (!empty($id)) {
            $title = $this->mysql->select_one(" select * from " . Mysite::$app->config['tablepre'] . "helpbuy where id = " . $id . " ");
            $bqlist = $this->mysql->getarr(" select * from " . Mysite::$app->config['tablepre'] . "helpbuybq where parent_id = " . $id . " order by id asc");
        }
        $data['goods'] = IReq::get('goods');
        $data['title'] = $title;
        $data['bqlist'] = $bqlist;
        $data['movegoodsname'] = IReq::get('movegood');
        $data['movegoodscost'] = IReq::get('cost');
        $data['movegoodsweight'] = IReq::get('weight');
		$lng = IReq::get('lng');
		$lat = IReq::get('lat');
		$mapname = IReq::get('mapname');
		$city_id = IReq::get('adcode');
		$cityinfo = $this->mysql->select_one(" select name from " . Mysite::$app->config['tablepre'] . "area where adcode = " . $city_id . " "); 
		if( !empty($city_id) && !empty($cityinfo['name']) ){
			$where = " where  cityid = '".$city_id."' ";
		}		
		$data['lng'] = $lng;
		$data['lat'] = $lat;
		$data['mapname'] = $mapname;	
 		$pttype = intval(IReq::get('pttype')); 
		$data['pttype'] = $pttype;  // 1为帮我送  2为帮我买
		$data['ptsetinfo'] = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."paotuiset ".$where."  "); 
		 
		$postdate =  $data['ptsetinfo']['postdate'];
		$befortime = $data['ptsetinfo']['pt_orderday'];
		$is_ptorderbefore = $data['ptsetinfo']['is_ptorderbefore'];  
		if($is_ptorderbefore==0) $befortime=0;
		$nowhout = strtotime(date('Y-m-d',time()));//当天最小linux 时间
		$timelist = !empty($postdate)?unserialize($postdate):array();
		$data['pstimelist'] = array();
		$checknow = time();
 #print_r($befortime);
		$whilestatic = $befortime;
		$nowwhiltcheck = 0;
		while($whilestatic >= $nowwhiltcheck){
		    $startwhil = $nowwhiltcheck*86400;
			foreach($timelist as $key=>$value){
				$stime = $startwhil+$nowhout+$value['s'];
				$etime = $startwhil+$nowhout+$value['e'];
				if($etime  >= $checknow){
					$tempt = array();
					$tempt['value'] = $value['s']+$startwhil;
					$tempt['s'] = date('H:i',$nowhout+$value['s']);
					$tempt['e'] = date('H:i',$nowhout+$value['e']);
					$tempt['d'] = date('Y-m-d',$stime);
					$tempt['i'] =  $value['i'];
					$tempt['cost'] =  isset($value['cost'])?$value['cost']:0;					
					$tempt['name'] = $tempt['d'].' '.$tempt['s'].'-'.$tempt['e'].' '.$tempt['i'];
					$data['pstimelist'][] = $tempt;
					
				}
			}	 
			$nowwhiltcheck = $nowwhiltcheck+1;
		}
		$data['arealist'] = $tarelist;
        $data['defaultmsg'] = $defaultmsg;
		$this->success($data);
	}
	public function rechargepay(){ // 微信充值页面
		$uid = IFilter::act(IReq::get('userid'));
		$paylist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."paylist where type = 0 or type=2  order by id asc limit 0,50");
		if(is_array($paylist)){
			foreach($paylist as $key=>$value){
				$paytypelist[$value['loginname']] = $value['logindesc'];
			}
		}
		$data['paylist'] = $paylist;
		$this->success($data);
	}
	public function exchangcard(){		//充值卡充值
		$uid = IFilter::act(IReq::get('userid'));
		$card = trim(IFilter::act(IReq::get('cardnum')));
		$password = trim(IFilter::act(IReq::get('cardpwd')));
		if(empty($card)) $this->message('card_emptycard');
		if(empty($password)) $this->message('card_emptycardpwd');
		$meminfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid ='".$uid."' ");
		if(empty($meminfo)) $this->message('该用户不存在');
		$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."card where card ='".$card."'  and card_password = '".$password."' and uid =0 and status = 0");
		if(empty($checkinfo)) $this->message('充值卡不存在,请再核对下');
		$arr['uid'] = $meminfo['uid'];
		$arr['status'] =  1;
		$arr['username'] = $meminfo['username'];
        $arr['usetime'] = time();      
		$this->mysql->update(Mysite::$app->config['tablepre'].'card',$arr,"card ='".$card."'  and card_password = '".$password."' and uid =0 and status = 0");
		
		$this->mysql->update(Mysite::$app->config['tablepre'].'member','`cost`=`cost`+'.$checkinfo['cost'],"uid ='".$uid."' ");
		$allcost = $meminfo['cost']+$checkinfo['cost'];
		$this->memberCls->addlog($uid,2,1,$checkinfo['cost'],'充值卡充值','使用充值卡'.$checkinfo['card'].'充值'.$checkinfo['cost'].'元',$allcost);
		$this->memberCls->addmemcostlog($uid,$meminfo['username'],$meminfo['cost'],1,$checkinfo['cost'],$allcost,$meminfo['username']."使用充值卡充值",$meminfo['uid'],$meminfo['username']);

		$this->success('success');
	
	}
	function searchresult(){
		$this->getOpenCity();
		$searchname = IFilter::act(IReq::get('searchname'));
		$adcode = IFilter::act(IReq::get('adcode'));
		$lng = IFilter::act(IReq::get('lng'));
		$lat = IFilter::act(IReq::get('lat'));
        $lng = empty($lng)?0:$lng;
        $lat =empty($lat)?0:$lat;
		 /* 搜索店铺 结果  */
		$where = '';  
		$shopsearch	= urldecode($searchname); 
		if(!empty($shopsearch)) $where=" and shopname like '%".$shopsearch."%' "; 

		/*获取店铺*/
		$where = Mysite::$app->config['plateshopid'] > 0? $where.' and  id != '.Mysite::$app->config['plateshopid'] .' ':$where;
		$tempdd = array();		 
		$tempdd[] =   $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shop where is_pass = 1  and (   admin_id='".$adcode."'  or  admin_id = 0 )     and endtime > ".time()."  ".$where." ");		
		 
		$nowhour = date('H:i:s',time()); 
		$nowhour = strtotime($nowhour);
		$templist = array();
		$cxclass = new sellrule();  

		$templist111 = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where    parent_id = 0    order by orderid asc limit 0,1000"); 
		$attra = array();  
		foreach($templist111 as $key=>$vall){
			if(isset($attra[$vall['cattype']])){
				if($vall['type'] == 'input'){
					$attra[$vall['cattype']]['input'] =  $attra[$vall['cattype']]['input'] > 0?$attra[$vall['cattype']]['input']:$vall['id'];
				}elseif($vall['type'] == 'img'){
					$attra[$vall['cattype']]['img'] =  $attra[$vall['cattype']]['img'] > 0?$attra[$vall['cattype']]['img']:$vall['id'];
				}elseif($vall['type'] == 'checkbox'){
					$attra[$vall['cattype']]['checkbox'] =  $attra[$vall['cattype']]['checkbox'] > 0?$attra[$vall['cattype']]['checkbox']:$vall['id'];
				}
			}else{
				if($vall['type'] == 'input'){
					$attra[$vall['cattype']]['input'] =  $vall['id'];
				}elseif($vall['type'] == 'img'){
					$attra[$vall['cattype']]['img'] =  $vall['id'];
				}elseif($vall['type'] == 'checkbox'){
					$attra[$vall['cattype']]['checkbox'] = $vall['id'];
				}
			}
		} 		
		foreach($tempdd as $key=>$list){
			if(is_array($list)&& !empty($list)){
			    foreach($list as $keys=>$values){  
					// print_r($values['id']);
					if($values['id'] > 0){ 
						// print_r($values);
						if($values['shoptype'] == 1 ){
						    $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where  shopid = ".$values['id']."   ");
						}else{
						    $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where  shopid = ".$values['id']."   ");
						}
						if(empty($shopdet)){
							continue;
						}
						$values = array_merge($values,$shopdet);
						$values['shoplogo'] = empty($values['shoplogo'])? getImgQuanDir(Mysite::$app->config['shoplogo']):getImgQuanDir($values['shoplogo']);
						$checkinfo = $this->shopIsopen($values['is_open'],$values['starttime'],$values['is_orderbefore'],$nowhour); 
						$values['opentype'] = $checkinfo['opentype'];
						$values['newstartime']  =  $checkinfo['newstartime'];
 						
						$attrdet = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopattr where  `cattype` = '".$values['shoptype']."' and `shopid` = '".$values['id']."' ");
						$checkps = 	 $this->pscost($values,$lat,$lng); 
						$values['pscost'] = $checkps['pscost'];
						$mi = $this->GetDistance($lat,$lng, $values['lat'],$values['lng'], 1); 
						$tempmi = $mi;
						$julicc = $mi/1000;
						$mi = $mi > 1000? round($mi/1000,2).'km':$mi.'m';
						$values['juli'] = 		$mi;
						 
						//判断店铺是否超出配送区域
						if($julicc > $values['pradiusa']){
						    $values['outrange'] = 1;
						}else{
						    $values['outrange'] = 0;
						}							 
						$shopcounts = $this->mysql->select_one( "select count(id) as shuliang  from ".Mysite::$app->config['tablepre']."order	 where status = 3 and  shopid = ".$values['id']."" );
						if(empty( $shopcounts['shuliang']  )){
						    $values['ordercount'] = 0;
						}else{
						    $values['ordercount']  = $shopcounts['shuliang'];
						}                                                 
						$values['ordercount']  = $values['ordercount']+$values['virtualsellcounts'];
						$time = time();
						$newcxinfo = array();
					    $d = date("w") ==0?7:date("w");		
						$cxinfo = $this->mysql->getarr("select id,name,imgurl,controltype,parentid from ".Mysite::$app->config['tablepre']."rule where  FIND_IN_SET(".$values['id'].",shopid) and status = 1 and ( limittype =1 or ( limittype = 2 and  FIND_IN_SET(".$d.",limittime) ) or ( limittype = 3 and endtime > ".$time." and starttime < ".$time.")) order by id desc  ");
						if(!empty($cxinfo)){
							foreach($cxinfo as $k=>$val){
								$val['imgurl'] = getImgQuanDir($val['imgurl']);
								$newcxinfo[] = $val;
							}
						}
						$values['cxlist'] = $newcxinfo;
						/* 店铺星级计算 */
						$zongpoint = $values['point'];
						$zongpointcount = $values['pointcount'];
						if($zongpointcount != 0 ){
						     $shopstart = intval( round($zongpoint/$zongpointcount) );
						}else{
						     $shopstart= 0;
						}
						$values['point'] = 	$shopstart;						
						$values['attrdet'] = array();
						foreach($attrdet as $k=>$v){
							if(isset($attra[$values['shoptype']]['input']) && $v['firstattr'] == $attra[$values['shoptype']]['input']){
							    $values['attrdet']['input'] = $v['value'];
							}elseif(isset($attra[$values['shoptype']]['img']) && $v['firstattr'] == $attra[$values['shoptype']]['img']){
							    $values['attrdet']['img'][] = $v['value'];
							}elseif(isset($attra[$values['shoptype']]['checkbox']) && $v['firstattr'] == $attra[$values['shoptype']]['checkbox']){
							    $values['attrdet']['checkbox'][] = $v['value'];
							} 
						}
						$values['is_show_ztimg'] = ($this->platpssetinfo['is_allow_ziti']==1 && $values['is_ziti'])?1:0;
						$templist[] = $values;						
					}
				} 
			}
		}
		$shop1 = array();
		$shop2 = array();
		foreach($templist as $sk=>$sv){
			if($sv['outrange'] == 0){
				$shop1[] = $sv;
			}else{
				$shop2[] = $sv;
			}
		}
		$data['shopsearchlist'] = array_merge($shop1,$shop2); 
		#print_r($data['shopsearchlist']);exit;
		/* 搜索商品列表 */
		$weekji = date('w');
		$goodwhere = '';  
		$goodssearch = urldecode($searchname); 
		if(!empty($goodssearch)) $goodlistwhere=" and name like '%".$goodssearch."%' "; 
		$templist11 = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where  cattype = 0 and parent_id = 0 and is_main =1  order by orderid asc limit 0,1000"); 
		$attra['input'] = 0;
		$attra['img'] = 0;
		$attra['checkbox'] = 0; 
		foreach($templist11 as $key=>$value){
			if($value['type'] == 'input'){
			    $attra['input'] =  $attra['input'] > 0?$attra['input']:$value['id'];
			}elseif($value['type'] == 'img'){
			    $attra['img'] =  $attra['img'] > 0?$attra['img']:$value['id'];
			}elseif($value['type'] == 'checkbox'){
			    $attra['checkbox'] =  $attra['checkbox'] > 0?$attra['checkbox']:$value['id'];
			}
		} 
		/*获取店铺*/
		$pageinfo = new page();
		$pageinfo->setpage(intval(IReq::get('page')));
		$goodwhere = Mysite::$app->config['plateshopid'] > 0? $goodwhere.' and  id != '.Mysite::$app->config['plateshopid'] .' ':$goodwhere;
		$list =   $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shop where is_pass = 1     and endtime > ".time()."  ".$goodwhere." ");
		$nowhour = date('H:i:s',time()); 
		$nowhour = strtotime($nowhour);
		$goodssearchlist = array();
		$cxclass = new sellrule();

		if(is_array($list)){
			foreach($list as $keys=>$vatt){  

				if($vatt['id'] > 0){
					$detaa = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods where shopid='".$vatt['id']."'  and shoptype = ".$vatt['shoptype']."  and    FIND_IN_SET( ".$weekji." , `weeks` )  ".$goodlistwhere."   order by good_order asc ");
					if(!empty($detaa)){						
						foreach ( $detaa as $keyq=>$valq ){
							$mi = $this->GetDistance($lat,$lng, $vatt['lat'],$vatt['lng'], 1);  
							$julicc = $mi/1000; 
							//判断店铺是否超出配送区域
							if($julicc > $vatt['pradiusa']){
								$valq['outrange'] = 1;
							}else{
								$valq['outrange'] = 0;
							}
							$valq['goodattr'] = empty($valq['goodattr'])?$vatt['goodattrdefault']:$valq['goodattr'];
							$valq['descgoods'] = empty($valq['descgoods'])?'':$valq['descgoods'];
							$valq['newcost'] = $valq['cost'];
							if($valq['is_cx'] == 1){
								//测算促销 重新设置金额
								$cxdata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$valq['id']."  ");
								$newdata = getgoodscx($valq['cost'],$cxdata);
								$valq['zhekou'] = $newdata['zhekou'];
								$valq['is_cx'] = $newdata['is_cx'];
								$valq['newcost'] = number_format($newdata['cost'],2);
								$valq['cxnum'] = $cxdata['cxnum'];
							}
                            $valq['sellcount'] = $valq['sellcount'] + $valq['virtualsellcount'];
							if( $vatt['shoptype']== 1 ){
							    $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where  shopid = ".$valq['shopid']."   ");
							}else{
							    $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where  shopid = ".$valq['shopid']."   ");
							}
							$valq['img'] = empty($valq['img'])?getImgQuanDir(Mysite::$app->config['goodlogo']):getImgQuanDir($valq['img']);
							$checkinfo = $this->shopIsopen($vatt['is_open'],$vatt['starttime'],$shopdet['is_orderbefore'],$nowhour); 
							$valq['opentype'] = $checkinfo['opentype'];
							$valq['shoptype'] = $vatt['shoptype'];
							$valq['shopname'] = $vatt['shopname'];
							$temparray[] =$valq; 
							$vakk = $temparray;
						}
					}	
					$goodssearchlist = $vakk;
				}
			} 
		}
		$goods1 = array();
		$goods2 = array();		 
        foreach($goodssearchlist as $gk=>$gv){
			if($gv['outrange'] == 0){
				$goods1[] = $gv;
			}else{
				$goods2[] = $gv;
			}
		}
		$data['goodssearchlist']  = array_merge($goods1,$goods2);
		$data['psimg']  = getImgQuanDir(Mysite::$app->config['psimg']);
        $data['shoppsimg']  = getImgQuanDir(Mysite::$app->config['shoppsimg']);
		$data['ztimg']  = getImgQuanDir(Mysite::$app->config['ztimg']);
		$this->success($data);	
		
	}
    //店铺分类列表
    function shoptypelist(){
        $this->getOpenCity();
        $shopshowtype = IFilter::act(IReq::get('shoptype'));
        $typeid = IFilter::act(IReq::get('typeid'));
        if(!in_array($shopshowtype,array('waimai','market','dingtai'))){
            $shopshowtype = 'waimai';
        }
        $caipin = array();
        if($shopshowtype == 'market'){
            $cattype = 1;
        }else{
            $cattype = 0;
        }
        $templist = $this->mysql->select_one("select `id` from ".Mysite::$app->config['tablepre']."shoptype where  cattype = '".$cattype."' and parent_id = 0 and is_search = 1 and type ='checkbox'  order by orderid asc limit 0,1");
        if(!empty($templist)){
            $caipin  = $this->mysql->getarr("select `id`,`name` from ".Mysite::$app->config['tablepre']."shoptype where parent_id='".$templist['id']."'  ");
        }
        $data['caipin'] = $caipin;
        $typename = '';
        if(!empty($typeid)){
            $typeinfo = $this->mysql->select_one("select `name` from ".Mysite::$app->config['tablepre']."shoptype where id = '".$typeid."'");
            $typename = $typeinfo['name'];
        }
        if(empty($typename) && !empty($caipin)){
            $typename = $caipin[0]['name'];
        }
        $data['typename'] = $typename;
        $this->success($data);
    }
	//获取某店铺下折扣商品列表
	function getShopZhekouGoodsList($shopinfo){
		 /*折扣数据开始*/
		 $weekji = date('w');
		 $shopid = $shopinfo['id'];
		 $goodsSelect = "`id`,`is_cx`,`cost`,`img`,`goodattr`,`descgoods`,`sellcount`,`virtualsellcount`,`name`,`have_det`,`count`,`bagcost`";
		 $zkgoodinfo = $this->mysql->getarr("select ".$goodsSelect." from ".Mysite::$app->config['tablepre']."goods where shopid='".$shopid."' and    FIND_IN_SET( ".$weekji." , `weeks` )    and is_live = 1 and is_cx = 1  order by good_order asc ");
		 $cxgoodslist = array();
		 foreach ( $zkgoodinfo as $k1=>$v1 ){		 
		 
		 
				 
								//测算促销 重新设置金额
								$cxdata = $this->mysql->select_one("select `cxstarttime`,`ecxendttime`,`cxetime1`,`cxzhe`,`cxstime2`,`cxnum` from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$v1['id']."  ");
								$newdata = getgoodscx($v1['cost'],$cxdata);
								$v1['zhekou'] = $newdata['zhekou'];
								$v1['is_cx'] = $newdata['is_cx'];
								$v1['cxnum'] = $cxdata['cxnum'];
								$v1['newcost'] = number_format($newdata['cost'],2);
								$v1['oldcost'] = $v1['cost'];
								if($v1['have_det'] == 1){
									$price=array(); 
									$gooddet = $this->mysql->getarr("select cost from ".Mysite::$app->config['tablepre']."product where goodsid=".$v1['id']."  ");
									if( !empty($gooddet) ){
										foreach ( $gooddet as $k2=>$v2 ){
											$price[] = $v2['cost'];
										}
										$v1['cost'] = number_format(min($price),2);//获取多规格商品中价格最小的价格作为展示价格 
										if($v1['zhekou']>0){
											$v1['newcost'] = number_format(($v1['cost']*$v1['zhekou']*0.1),2);
										}
									}
										 
									
								}
								$v1['sellcount'] = $v1['sellcount']+$v1['virtualsellcount']; 
								$v1['shoplogo']  = getImgQuanDir($shopinfo['shoplogo']);
								$v1['img'] = empty($v1['img'])? getImgQuanDir(Mysite::$app->config['goodlogo']):getImgQuanDir($v1['img']);
								$v1['goodattr'] = empty($v1['goodattr'])? $shopinfo['goodattrdefault']:$v1['goodattr'];
								$v1['descgoods'] = empty($v1['descgoods'])? '':$v1['descgoods'];
								$v1['sellcount'] = $v1['sellcount']+$v1['virtualsellcount'];
								$v1['cartnum'] = 0;
								$v1['zhekou_name'] = '';
								if($v1['is_cx'] == 1){
									if( $v1['zhekou'] > 0 ){
										$temp = '';
										if( $v1['cxnum'] > 0 ){
											$temp = ",每单限购".$v1['cxnum'].$v1['goodattr'];
										}
										$v1['zhekou_name'] = $v1['zhekou']."折".$temp;
									}
									$cxgoodslist[]=$v1;
								}
 		   
			} 
		 return $cxgoodslist;
	}
	
    //店铺页面获取商品分类
    function shopgoodstype(){
        //$this->getOpenCity();
        $shoptype = IFilter::act(IReq::get('shoptype'));
        $id = intval(IReq::get('id'));
        $cateid = intval(IReq::get('cateid'));
        $lng = IFilter::act(IReq::get('lng'));
        $lat = IFilter::act(IReq::get('lat'));
        $userid = IFilter::act(IReq::get('userid'));
        $userid = empty($userid) ? 0 : $userid;

        $shopinfo = $this->mysql->select_one("select `id`,`is_open`,`starttime`,`lat`,`lng`,`goodlistmodule`,`shoplogo`,`notice_info`,`shopname`,`is_ziti` from ".Mysite::$app->config['tablepre']."shop where id='".$id."' ");
        $shopinfo['shoplogo'] = empty($shopinfo['shoplogo'])? getImgQuanDir(Mysite::$app->config['shoplogo']):getImgQuanDir($shopinfo['shoplogo']);
        $shopinfo['notice_info'] = empty($shopinfo['notice_info']) ? Mysite::$app->config['shopnotice'] : $shopinfo['notice_info'];
        if($shoptype == 1){
            $shopdet = $this->mysql->select_one("select `is_orderbefore`,`pradiusvalue`,`sendtype`,`limitcost`,`arrivetime` from ".Mysite::$app->config['tablepre']."shopmarket where shopid='".$id."' ");
            $cateWhere = '';
            if($cateid > 0)$cateWhere .= ' and id = '.$cateid;
            $goodstype=  $this->mysql->getarr("select `id`,`name` from ".Mysite::$app->config['tablepre']."marketcate where shopid = ".$shopinfo['id']."   and parent_id = 0 ".$cateWhere." order by orderid asc");
            $newgoodstype = array();
			
			
			if( !empty($goodstype) ){
				foreach($goodstype as $key=>$value){
					$soncatearray = $this->mysql->getarr("select `id`,`name` from ".Mysite::$app->config['tablepre']."marketcate where shopid = ".$id."   and parent_id = ".$value['id']."  order by orderid asc");
					$value['soncate']  = $soncatearray;
					$newgoodstype[] =$value;
				}
			}
            
            $data['goodstype'] = $newgoodstype;
            $shopdet['id'] = $id;
            $shopdet['shoptype']=1;
            $newshoparray = array_merge($shopinfo,$shopdet);
            $tempinfo = $this->pscost($newshoparray,$lng,$lat);
            $data['shopinfo'] = $newshoparray;
            $backdata['pstype'] = $tempinfo['pstype'];
            $backdata['pscost'] = $tempinfo['pscost'];
            $data['psinfo'] = $backdata;
        }else{
            $shopdet = $this->mysql->select_one("select `is_orderbefore`,`pradiusvalue`,`sendtype`,`limitcost`,`arrivetime` from ".Mysite::$app->config['tablepre']."shopfast where shopid='".$id."' ");
            $templist = $this->mysql->getarr("select `id`,`name` from ".Mysite::$app->config['tablepre']."goodstype  where shopid='".$id."' order by orderid asc  ");
            $newgoodstype = array();
			
			
			$ZhekouGoodsListReturn = $this->getShopZhekouGoodsList($shopinfo);
			if( !empty($ZhekouGoodsListReturn) ){
				$zhekoucate = array();
				$zhekoucate['id'] = '-1';
				$zhekoucate['name'] = '折扣';
				$zhekoucate['cate_icon'] = getImgQuanDir(Mysite::$app->config['zkimg']);
				$zhekoucate['soncate'] = array();
				$newgoodstype[] = $zhekoucate;
			}
			if( !empty($templist) ){
				foreach($templist as $value){
					$value['soncate']  = array();
					$value['cate_icon'] = '';
					$newgoodstype[] =$value;
				}
            }
            $data['goodstype'] = $newgoodstype;
            $shopdet['id'] = $id;
            $shopdet['shoptype']=1;
            $newshoparray = array_merge($shopinfo,$shopdet);
            $tempinfo = $this->pscost($newshoparray,$lng,$lat);
            $data['shopinfo'] = $newshoparray;
            $backdata['pstype'] = $tempinfo['pstype'];
            $backdata['pscost'] = $tempinfo['pscost'];
            $data['psinfo'] = $backdata;
        }
		if($userid==0){
			$data['collect'] = 2;
		}else{
			$collectInfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."collect where uid=".$userid." and collectid=".$id."  and collecttype = '0' ");
			if(!empty($collectInfo))
			{
				$data['collect'] = 1;
			}else{
				$data['collect'] = 2;
			}
		}
        $cxlist = $this->mysql->getarr("select `name`,`imgurl` from ".Mysite::$app->config['tablepre']."rule where  FIND_IN_SET(".$id.",shopid) and status = 1 and ( limittype < 3  or ( limittype = 3 and endtime > ".time()." and starttime < ".time().")) ");
        $cxArr = array();
        if(!empty($cxlist)){
            foreach($cxlist as $vc){
                $vc['imgurl']  = getImgQuanDir($vc['imgurl']);
                $cxArr[] = $vc;
            }
        }
        $data['cxlist'] = $cxArr;
		$data['is_show_ztimg'] = ($this->platpssetinfo['is_allow_ziti']==1 && $shopinfo['is_ziti']==1)?1:0;
        $this->success($data);
    }

    //店铺页面获取分类商品列表
    function catefoods(){
        $weekji = date('w');
        $shopid = intval( IFilter::act(IReq::get('shopid')) ) ;
        $curcateid = intval( IFilter::act(IReq::get('curcateid')) ) ;
        $shoptype = intval( IFilter::act(IReq::get('shoptype')) ) ;
        $shopinfo = $this->mysql->select_one("select `id`,`is_open`,`starttime`,`shoplogo`,`goodattrdefault` from ".Mysite::$app->config['tablepre']."shop where id=".$shopid." ");
        if($shoptype == 1 ){
            $cateinfo = $this->mysql->getarr("select `id`,`parent_id`,`name` from ".Mysite::$app->config['tablepre']."marketcate where id = ".$curcateid." and shopid = ".$shopid." ");
            $shopdet = $this->mysql->select_one("select `is_orderbefore`,`limitcost` from ".Mysite::$app->config['tablepre']."shopmarket where shopid = ".$shopinfo['id']." ");
        }else{
            $cateinfo = $this->mysql->getarr("select `id`,`name` from ".Mysite::$app->config['tablepre']."goodstype where id = ".$curcateid." ");
            $shopdet = $this->mysql->select_one("select `is_orderbefore`,`limitcost` from ".Mysite::$app->config['tablepre']."shopfast where shopid = ".$shopinfo['id']." ");
        }
        $nowhour = date('H:i:s',time());
        $nowhour = strtotime($nowhour);
        $checkinfo = $this->shopIsopen($shopinfo['is_open'],$shopinfo['starttime'],$shopdet['is_orderbefore'],$nowhour);
        $data['opentype'] = $checkinfo['opentype'];
        $newcateinfo = array();
        $goodsSelect = "`id`,`is_cx`,`cost`,`img`,`goodattr`,`descgoods`,`sellcount`,`virtualsellcount`,`name`,`have_det`,`count`,`bagcost`";
		if( $curcateid == '-1' ){//折扣商品列表
					$item['id'] = '-1';
					$item['name'] = '折扣';
					$ZhekouGoodsListReturn = $this->getShopZhekouGoodsList($shopinfo);
					$item['goodslist'] = $ZhekouGoodsListReturn;
					$newcateinfo[] = $item;
		}else{
			if(isset($cateinfo[0]['parent_id']) && $cateinfo[0]['parent_id'] == 0){
				$soncate = $this->mysql->getarr("select `id`,`parent_id`,`name` from ".Mysite::$app->config['tablepre']."marketcate where parent_id = ".$cateinfo[0]['id']." and shopid = ".$shopid." ");
				if(!empty($soncate)){
					foreach($soncate as $vv){
						$catefoodslist = array();
						$detaa = $this->mysql->getarr("select ".$goodsSelect." from ".Mysite::$app->config['tablepre']."goods where typeid='".$vv['id']."' and is_waisong = 1 and shopid = ".$shopid." and  FIND_IN_SET( ".$weekji." , `weeks` )  and is_live = 1  order by good_order asc  ");
						foreach ( $detaa as $valq ){
							$valq['cost'] = $this->formatcost($valq['cost'],2);
							$valq['newcost'] = $this->formatcost($valq['cost'],2);
							if($valq['is_cx'] == 1){
								//测算促销 重新设置金额 
								
								$cxdata = $this->mysql->select_one("select `cxstarttime`,`ecxendttime`,`cxetime1`,`cxzhe`,`cxstime2`,`cxnum` from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$valq['id']."  ");
								$newdata = getgoodscx($valq['cost'],$cxdata);
								$valq['zhekou'] = $newdata['zhekou'];
								$valq['is_cx'] = $newdata['is_cx'];
								$valq['newcost'] = $this->formatcost($newdata['cost'],2);
								$valq['cxnum'] = $cxdata['cxnum'];
								$valq['oldcost'] = $valq['cost'];
								
								if($valq['have_det'] == 1){
									$price=array(); 
									$gooddet = $this->mysql->getarr("select cost from ".Mysite::$app->config['tablepre']."product where goodsid=".$valq['id']."  ");
									if( !empty($gooddet) ){
										foreach ( $gooddet as $k2=>$v2 ){
											$price[] = $v2['cost'];
										}
										$valq['cost'] = $this->formatcost(min($price),2);//获取多规格商品中价格最小的价格作为展示价格 
										if($valq['zhekou']>0){
											$valq['newcost'] = $this->formatcost(($valq['cost']*$valq['zhekou']*0.1),2);
										}
									}  
									
								}
								
								
							}
							
							
							
							$valq['shoplogo']  = getImgQuanDir($shopinfo['shoplogo']);
							$valq['img'] = empty($valq['img'])? getImgQuanDir(Mysite::$app->config['goodlogo']):getImgQuanDir($valq['img']);
							$valq['goodattr'] = empty($valq['goodattr'])? $shopinfo['goodattrdefault']:$valq['goodattr'];
							$valq['descgoods'] = empty($valq['descgoods'])? '':$valq['descgoods'];
							$valq['sellcount'] = $valq['sellcount']+$valq['virtualsellcount'];
							$valq['cartnum'] = 0;
							
									$valq['zhekou_name'] = '';
 									$temp = '';
									if( $valq['zhekou'] > 0 ){
										if( $valq['cxnum'] > 0 ){
											$temp = ",每单限购".$valq['cxnum'].$valq['goodattr'];
										}
										$valq['zhekou_name'] = $valq['zhekou']."折".$temp;
									}
 								 
							
							$catefoodslist[] =$valq;
						}
						$vv['goodslist'] = $catefoodslist;
						$newcateinfo[] = $vv;
					}
				}
			}else{ 
				foreach($cateinfo as $item){
					$catefoodslist = array();
					$detaa = $this->mysql->getarr("select ".$goodsSelect." from ".Mysite::$app->config['tablepre']."goods where typeid='".$curcateid."' and is_waisong = 1 and shopid = ".$shopid." and    FIND_IN_SET( ".$weekji." , `weeks` )  and is_live = 1  order by good_order asc  ");
					foreach ( $detaa as $keyq=>$valq ){
						$valq['cost'] = $this->formatcost($valq['cost'],2);
						$valq['newcost'] = $this->formatcost($valq['cost'],2);
						if($valq['is_cx'] == 1){
							//测算促销 重新设置金额
							$cxdata = $this->mysql->select_one("select `cxstarttime`,`ecxendttime`,`cxetime1`,`cxzhe`,`cxstime2`,`cxnum` from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$valq['id']."  ");
							$newdata = getgoodscx($valq['cost'],$cxdata);
							$valq['zhekou'] = $newdata['zhekou'];
							$valq['is_cx'] = $newdata['is_cx'];
							$valq['newcost'] = $this->formatcost($newdata['cost'],2);
							$valq['cxnum'] = $cxdata['cxnum'];
							$valq['oldcost'] = $valq['cost'];
							if($valq['have_det'] == 1){
									$price=array(); 
									$gooddet = $this->mysql->getarr("select cost from ".Mysite::$app->config['tablepre']."product where goodsid=".$valq['id']."  ");
									if( !empty($gooddet) ){
										foreach ( $gooddet as $k2=>$v2 ){
											$price[] = $v2['cost'];
										}
										$valq['cost'] = $this->formatcost(min($price),2);//获取多规格商品中价格最小的价格作为展示价格 
										if($valq['zhekou']>0){
											$valq['newcost'] = $this->formatcost(($valq['cost']*$valq['zhekou']*0.1),2);
										}
									}  
									
								}
						}
						$valq['shoplogo']  = getImgQuanDir($shopinfo['shoplogo']);
						$valq['img'] = empty($valq['img'])? getImgQuanDir(Mysite::$app->config['goodlogo']):getImgQuanDir($valq['img']);
						$valq['goodattr'] = empty($valq['goodattr'])? $shopinfo['goodattrdefault']:$valq['goodattr'];
						$valq['descgoods'] = empty($valq['descgoods'])? '':$valq['descgoods'];
						$valq['sellcount'] = $valq['sellcount']+$valq['virtualsellcount'];
						$valq['cartnum'] = 0;
						
						
						$valq['zhekou_name'] = '';
									if( $valq['zhekou'] > 0 ){
										$temp = '';
										if( $valq['cxnum'] > 0 ){
											$temp = ",每单限购".$valq['cxnum'].$valq['goodattr'];
										}
										$valq['zhekou_name'] = $valq['zhekou']."折".$temp;
									}
						
						$catefoodslist[] =$valq;
					}
					$item['goodslist'] = $catefoodslist;
					$newcateinfo[] = $item;
				}
			}
        }

        $data['shopdet'] = $shopdet;
        $data['catefoodslist'] = $newcateinfo;
        $this->success($data);
    }

    //获取规格商品的规格信息
    function foodsgg(){
        $id = intval( IReq::get('id') );
        $attrid =  IReq::get('attrid');
        $foodshow = $this->mysql->select_one( "select `img`,`product_attr`,`is_cx`,`id`,`name`,`bagcost` from  ".Mysite::$app->config['tablepre']."goods where id= ".$id."  " );
        $foodshow['img'] = empty($foodshow['img'])?getImgQuanDir(Mysite::$app->config['goodlogo']):getImgQuanDir($foodshow['img']);
        $data['foodshow']  = $foodshow;
        $productinfo = !empty($foodshow)?unserialize($foodshow['product_attr']):array();
        $choiceattr = array();
        $newproductinfo = array();
        $choiceinfo = array(
              'id'=> 0,
              'goodsid'=> 0,
              'attrids'=> '',
              'cost'=> 0,
              'stock'=> 0,
              'attrname'=> ''
        );
        $data['chekcstr'] = '';
        if(!empty($productinfo)){
            foreach($productinfo as $values){
                foreach($values['det'] as $kk=>$vv){
                    if($kk == 0){
                        $choiceattr[] = $vv['id'];
                    }
                }
            }
            sort($choiceattr);
			if(!empty($attrid)){
				$tempid = $attrid;
			}else{
				$tempid = implode(',',$choiceattr);
			}
            
			#print_r($tempid);
            $productlist = $this->mysql->select_one("select `id`,`goodsid`,`cost`,`attrids`,`cost`,`stock`,`attrname` from ".Mysite::$app->config['tablepre']."product where goodsid=".$id."  and  `attrids` =  '".$tempid."'");
			#print_r($productlist);
            if(!empty($productlist)){
                if($foodshow['is_cx'] == 1){
                    //测算促销 重新设置金额
                    $cxdata = $this->mysql->select_one("select `cxstarttime`,`ecxendttime`,`cxetime1`,`cxzhe`,`cxstime2`,`cxnum` from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$foodshow['id']."  ");
                    $newdata = getgoodscx($productlist['cost'],$cxdata);
                    $productlist['zhekou'] = $newdata['zhekou'];
                    $productlist['is_cx'] = $newdata['is_cx'];
                    $productlist['oldcost'] = $this->formatcost($productlist['cost'],2);
                    $productlist['newcost'] = $this->formatcost($newdata['cost'],2);
                    $productlist['cxnum'] = $cxdata['cxnum'];
                }else{
					$productlist['is_cx'] = 0;
                    $productlist['oldcost'] = $this->formatcost($productlist['cost'],2);
                    $productlist['newcost'] = $this->formatcost($productlist['cost'],2);
				}
                $choiceinfo = $productlist;
            }
            $attrid = empty($attrid) ? $tempid : $attrid;
            $data['chekcstr'] = $attrid;

            $checkattr = explode(',',$attrid);
            foreach($productinfo as $value){
                if(!empty($value['det']) && !empty($attrid)){
                    $newdet = array();
                    foreach($value['det'] as $val){
                        if(in_array($val['id'],$checkattr)){
                            $val['check'] = 1;
                        }else{
                            $val['check'] = 0;
                        }
                        $newdet[] = $val;
                        $value['det'] = $newdet;
                    }
                }
                $newproductinfo[] =  $value;
            }
        }
        $data['choiceinfo'] = $choiceinfo;
        $data['productinfo'] = $newproductinfo;

        $this->success($data);
    }
    //点击规格获取商品属性
    function doselectproduct(){
        $goods_id = intval(IReq::get('goodsid'));
        $ggdetid =  trim(IReq::get('ggdetid'));//点击规格的id
        $attrids =  IReq::get('attrid');//上次选中的规格id集
        $mainid =  IReq::get('mainid');//点击规格的上级ID
        if(empty($ggdetid) || empty($mainid)) $this->message("请选择规格");
        $foodshow = $this->mysql->select_one( "select `img`,`product_attr`,`is_cx`,`id`,`name`,`bagcost` from  ".Mysite::$app->config['tablepre']."goods where id= ".$goods_id."  " );
        $foodshow['img'] = empty($foodshow['img'])? '':getImgQuanDir($foodshow['img']);
        $data['foodshow']  = $foodshow;
        $productinfo = !empty($foodshow)?unserialize($foodshow['product_attr']):array();
        $mainarr = array();
        if(!empty($productinfo)){
            foreach($productinfo as $vp){
                if($mainid == $vp['id']){
                    foreach($vp['det'] as $itt){
                        if($itt['id'] != $ggdetid){
                            $mainarr[] = $itt['id'];
                        }
                    }
                }
            }
        }else{
            $this->message("规格商品不存在");
        }
        $newcheck = array();
        if(!empty($attrids)){
            $attrArr = explode(',',$attrids);
            foreach($attrArr as $item){
                if(!in_array($item,$mainarr)){
                    $newcheck[] = $item;
                }
            }
        }
        $newcheck[] = $ggdetid;
        $newcheckstr = implode(',',$newcheck);
        $data['chekcstr'] = $newcheckstr;

        $ggdetids = explode(',',$newcheckstr);
        sort($ggdetids);
        $tempid = implode(',',$ggdetids);
        $productlist = $this->mysql->select_one("select `id`,`goodsid`,`cost`,`attrids`,`cost`,`stock`,`attrname` from ".Mysite::$app->config['tablepre']."product where goodsid=".$goods_id."  and  `attrids` =  '".$tempid."'");
        if(!empty($productlist)){
            if($foodshow['is_cx'] == 1){
                //测算促销 重新设置金额
                $cxdata = $this->mysql->select_one("select `cxstarttime`,`ecxendttime`,`cxetime1`,`cxzhe`,`cxstime2`,`cxnum` from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$foodshow['id']."  ");
                $newdata = getgoodscx($productlist['cost'],$cxdata);
                $productlist['zhekou'] = $newdata['zhekou'];
                $productlist['is_cx'] = $newdata['is_cx'];
                $productlist['oldcost'] = $productlist['cost'];
                $productlist['newcost'] = number_format($newdata['cost'],2);
                $productlist['cxnum'] = $cxdata['cxnum'];
            }else{
				$productlist['is_cx'] = 0;
                $productlist['oldcost'] = $productlist['cost'];
                $productlist['newcost'] = $productlist['cost'];
			}
            $data['productlist'] = $productlist;
        }else{
            $data['productlist'] = '';
        }

        $newproductinfo = array();
        if(!empty($productinfo)){
            foreach($productinfo as $value){
                if(!empty($value['det']) && !empty($newcheck)){
                    $newdet = array();
                    foreach($value['det'] as $val){
                        if(in_array($val['id'],$newcheck)){
                            $val['check'] = 1;
                        }else{
                            $val['check'] = 0;
                        }
                        $newdet[] = $val;
                        $value['det'] = $newdet;
                    }
                }
                $newproductinfo[] =  $value;
            }
        }
        $data['productinfo'] = $newproductinfo;
        $this->success($data);
    }

    //店铺评价页面
    function getshopcomment(){
        $shopid = IFilter::act(IReq::get('shopid'));
        $shopinfo = $this->mysql->select_one("select *  from  ".Mysite::$app->config['tablepre']."shop  where id = ".$shopid." ");

        if(empty($shopinfo)) $this->message('获取店铺数据失败');
        $data['collect'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."collect where  collecttype = 0 and uid = ".$this->member['uid']." and collectid  = '".$shopinfo['id']."' ");//收藏

        if( $shopinfo['pointcount'] != 0){
            $zongtistart = round( $shopinfo['point']/$shopinfo['pointcount'] ); // 总体评分  // 12 / 3 = 54
            $zonghefen = round( $shopinfo['point']/$shopinfo['pointcount'],1 ); // 综合评分
        }else{
            $zongtistart = 0;
            $zonghefen = 0;
        }
        if( $shopinfo['pointcount'] != 0){
            $psfuwustart = round( $shopinfo['psservicepoint']/$shopinfo['psservicepointcount'] ); // 配送服务
        }else{
            $psfuwustart = 0;
        }
        $data['shopinfo'] = $shopinfo;
        $data['zonghefen'] = $zonghefen;
        $data['zongtistart'] = $zongtistart > 5 ? 5 : $zongtistart;
        $data['psfuwustart'] = $psfuwustart > 5 ? 5 : $psfuwustart;
        $this->success($data);
    }
    //评价页面获取评价列表
    function getshopmorecomment(){
        $shopid = IFilter::act(IReq::get('shopid'));
        $goodid = IFilter::act(IReq::get('goodid'));

        $pageinfo = new page();
        $pageinfo->setpage(intval(IReq::get('page')),10);
        if( !empty($goodid) ){
            $temparray = $this->mysql->getarr("select * from  ".Mysite::$app->config['tablepre']."comment where  shopid=".$shopid." and is_show = 0 and goodsid = ".$goodid." order by addtime desc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");
        }else{
            $temparray = $this->mysql->getarr("select * from  ".Mysite::$app->config['tablepre']."comment where  shopid=".$shopid." and is_show = 0 order by addtime desc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");
        }
        $commentlist = array();
        foreach($temparray as $key=>$value){
            $memberinfo = $this->mysql->select_one("select * from  ".Mysite::$app->config['tablepre']."member where uid = ".$value['uid']." ");
            $goodinfo = $this->mysql->select_one("select * from  ".Mysite::$app->config['tablepre']."goods where id = ".$value['goodsid']." ");
            if(empty($goodinfo)){
                $goodinfo = $this->mysql->select_one("select * from  ".Mysite::$app->config['tablepre']."product where goodsid  = ".$value['goodsid']." ");
                $value['goodname'] =$goodinfo['goodsname'];
            }else{
                $value['goodname'] =$goodinfo['name'];
            }
            $value['username'] =$memberinfo['username'];

            if( !empty($value['virtualname']) ){
                $value['username'] = $value['virtualname'];
                $goodsinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goods   where id = '".$value['goodsid']."'   ");
                if( empty($goodsinfo) ){
                    $goodsinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."product   where goodsid = '".$value['goodsid']."'   ");
                    $value['goodsname'] = $goodsinfo['goodsname'].'【刷】';
                }else{
                    $value['goodsname'] = $goodsinfo['name'].'【刷】';
                }
            }

            $value['point'] = ceil($value['point']);
            $value['userlogo'] =$memberinfo['logo'];
            $value['goodpoint'] =$goodinfo['point'];
            $value['addtime'] = date('Y-m-d H:i',$value['addtime']);
            $value['huifutime'] = date('Y-m-d H:i',$value['replytime']);
            $commentlist[] = $value;
        }
        $data['commentlist'] = $commentlist;
        $this->success($data);
    }

    //商家页面
    function getdetailinfo(){
        $shopid = IFilter::act(IReq::get('shopid'));
        $lng = IFilter::act(IReq::get('lng'));
        $lat = IFilter::act(IReq::get('lat'));
        $shopinfo = $this->mysql->select_one("select *  from  ".Mysite::$app->config['tablepre']."shop  where id = ".$shopid." ");
        if(empty($shopinfo)) $this->message('获取店铺数据失败');
        if(empty($shopinfo['intr_info'])) $shopinfo['intr_info'] = '暂无说明';
		$shopinfo['shoplogo'] = empty($shopinfo['shoplogo'])?getImgQuanDir(Mysite::$app->config['shoplogo']):getImgQuanDir($shopinfo['shoplogo']);
        $data['collect'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."collect where  collecttype = 0 and uid = ".$this->member['uid']." and collectid  = '".$shopinfo['id']."' ");//收藏
        $shopreal = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopreal where  shopid = ".$shopid." limit 0,4");//商家实景分类
        $data['shopreal']=array();
        foreach($shopreal as $key=>$val){
            $shoprealimg = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoprealimg where  parent_id = ".$val['id']);//商家实景分类图片
            $imgcount = $this->mysql->select_one("select count(id) as count from ".Mysite::$app->config['tablepre']."shoprealimg where  parent_id = ".$val['id']);//商家实景分类图片总数
            $val['shoprealimg']=$shoprealimg;
            $val['imgcount']=$imgcount['count'];
            $data['shopreal'][]=$val;
        }
        $shopshowtype = $shopinfo['shoptype'];
        if( $shopshowtype == 1 ){
            $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where a.shopid = ".$shopid."   ");
        }else{
            $shopdet  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where a.shopid = ".$shopid."   ");
        }
        /* 店铺星级计算 */
        $zongpoint = $shopinfo['point'];
        $zongpointcount = $shopinfo['pointcount'];
        if($zongpointcount != 0 ){
            $shopstart = intval( round($zongpoint/$zongpointcount) );
        }else{
            $shopstart= 0;
        }
        $cxsignlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodssign where type='cx' order by id desc limit 0, 100");
        $cxarray  =  array();
        foreach($cxsignlist as $key=>$value){
            $cxarray[$value['id']] = $value['imgurl'];
        }
        $cxinfo = $this->mysql->getarr("select name,id,signid from ".Mysite::$app->config['tablepre']."rule where   shopid = ".$shopinfo['id']." and status = 1 and starttime  < ".time()." and endtime > ".time()." ");
        $cxlist = array();
        foreach($cxinfo as $k1=>$v1){
            if(isset($cxarray[$v1['signid']])){
                $v1['imgurl'] = $cxarray[$v1['signid']];
                $cxlist[] = $v1;
            }
        }
		$shopinfo['shoplogo'] = empty($shopinfo['shoplogo'])?Mysite::$app->config['tablepre'].Mysite::$app->config['shoplogo']:$shopinfo['shoplogo'];
        $data['cxlist'] = $cxlist;
        $newshoparray = array_merge($shopinfo,$shopdet);
        $tempinfo = $this->pscost($newshoparray,$lng,$lat);
        $backdata['pstype'] = $tempinfo['pstype'];
        $backdata['pscost'] = $tempinfo['pscost'];
        $data['psinfo'] = $backdata;
        $data['shopstart'] = $shopstart;
        $data['shopinfo'] = $shopinfo;
        $data['shopdet'] = $shopdet;
		$platpssetinfo = $this->mysql->select_one("select is_allow_ziti from ".Mysite::$app->config['tablepre']."platpsset where cityid= '".$shopinfo['admin_id']."' ");
		$data['is_show_ztimg'] = ($platpssetinfo['is_allow_ziti']==1 && $shopinfo['is_ziti']==1)?1:0;
		$data['ztimg']  = getImgQuanDir(Mysite::$app->config['ztimg']);
        $this->success($data);
    }
    //我的收获地址
    function address(){
        $userid = IFilter::act(IReq::get('userid'));
        $shopid = IFilter::act(IReq::get('shopid'));
        $area = IFilter::act(IReq::get('area'));
		$lat = IFilter::act(IReq::get('lat'));
		$lng = IFilter::act(IReq::get('lng'));
        $minit = IReq::get('minit');
		
		$goodid = IFilter::act(IReq::get('goodid'));
		$goodcx = IFilter::act(IReq::get('goodcx'));
		$goodcount = IFilter::act(IReq::get('goodcount'));
		$goodcost = IFilter::act(IReq::get('goodcost'));
		$ggoodid = IFilter::act(IReq::get('ggoodid'));
		$productid = IFilter::act(IReq::get('productid'));
		$ggoodcx = IFilter::act(IReq::get('ggoodcx'));
		$ggoodcount = IFilter::act(IReq::get('ggoodcount'));
		$ggoodcost = IFilter::act(IReq::get('ggoodcost'));
		
        $tarelist = $this->mysql->getarr("select `default`,`lng`,`lat`,`contactname`,`phone`,`bigadr`,`detailadr`,`tag`,`id`,`address` from ".Mysite::$app->config['tablepre']."address where userid='".$userid."' order by id asc limit 0,50");
		$data['arealist'] = $tarelist;
        $defaultmsg = null;
        $shopinfo = null;
        $data['open_acout'] = 0;
        $data['is_daopay'] = 0;
        $data['downcost'] = 0;
        $data['addpscost'] = 0;
        if(!empty($shopid)){
            $this->getOpenCity();
			if( !empty($this->platpssetinfo['paytype']) ){
					 $paytypearr = explode(',',$this->platpssetinfo['paytype']);
					 if( in_array('1',$paytypearr) ){
						  $data['open_acout'] = 1;
					 }
					  if( in_array('2',$paytypearr) ){
						  $data['is_daopay'] = 1;
					 }
			 } 
            $shopinfo = $this->mysql->select_one("select `id`,`shoptype`,`shopname`,`lat`,`lng`,`starttime`,`is_ziti`,`address`,`admin_id` from ".Mysite::$app->config['tablepre']."shop where  id ='".$shopid."'   ");
            if( !empty($shopinfo) ){
				  
                if( $shopinfo['shoptype'] == 1){
                    $shopdet = $this->mysql->select_one("select `is_orderbefore`,`postdate`,`befortime`,`sendtype`,`pradiusvalue` from ".Mysite::$app->config['tablepre']."shopmarket where  shopid ='".$shopinfo['id']."'   ");
                }else{
                    $shopdet = $this->mysql->select_one("select `is_orderbefore`,`postdate`,`befortime`,`sendtype`,`pradiusvalue` from ".Mysite::$app->config['tablepre']."shopfast where  shopid ='".$shopinfo['id']."'   ");
                }
            }
            if( !empty($shopdet)){
                $shopinfo = array_merge($shopinfo,$shopdet);
            }
            $platform = 2;//微信
            $paytype = IReq::get('paytype');
            if(!isset($paytype)){
                if($data['open_acout'] == 1){
                    $paytype = 1;
                }else{
                    if($data['is_daopay'] == 1){
                        $paytype = 0;
                    }
                }
            }
			$newpaytype = $paytype==0?2:1;
			//通过商品id及其促销信息计算相关金额
			$good=array();
			$goodlist = array();
			$ggood=array();
			$ggoodlist = array();
			$gooddown=0;		
			$goodbag=0;
			$goodnum=0;
			$goodsum=0;
			$productdown=0;
			$productbag=0;
			$productnum=0;
			$productsum=0;
			if(!empty($goodid)){			
				$goodids = explode(',',$goodid);
				$goodcxs = explode(',',$goodcx);
				$goodcounts = explode(',',$goodcount);
				$goodcosts = explode(',',$goodcost);
				#print_r($goodids);print_r($goodcxs);print_r($goodcounts);print_r($goodcosts);
				for($i=0;$i<count($goodids);$i++){
					$good[$i]=array(
						'id'=>empty($goodids[$i])?0:$goodids[$i],
						'gid'=>'',
						'is_cx'=>empty($goodcxs[$i])?0:$goodcxs[$i],
						'count'=>empty($goodcounts[$i])?0:$goodcounts[$i],
						'newcost'=>empty($goodcosts[$i])?0:$goodcosts[$i],
					);
					if(empty($goodids[$i])){
						unset($good[$i]);
					}
				}
				foreach($good as $k=>$val){					
					if(!empty($val['id'])){
						$goodinfo = $this->mysql->select_one("select cost,bagcost,name,img from ".Mysite::$app->config['tablepre']."goods  where id =".$val['id']." ");
						$val['name'] = $goodinfo['name'];
						$val['img'] = empty($goodinfo['img'])?getImgQuanDir(Mysite::$app->config['goodlogo']):getImgQuanDir($goodinfo['img']);
						$val['oldcost'] = $goodinfo['cost'];
						$val['attrname'] = '';
						$goodlist[] = $val;
						$gooddown += ($goodinfo['cost']-$val['newcost'])*$val['count'];
						$goodbag += $goodinfo['bagcost']*$val['count'];
						$goodnum +=$val['count'];
						$goodsum += $val['newcost']*$val['count'];
					}								
				}	
			}
			if(!empty($ggoodid)){
				$ggoodids = explode(',',$ggoodid);
				$productids = explode(',',$productid);
				$ggoodcxs = explode(',',$ggoodcx);
				$ggoodcounts = explode(',',$ggoodcount);
				$ggoodcosts = explode(',',$ggoodcost);
				for($i=0;$i<count($ggoodids);$i++){
					$ggood[$i]=array(
						'id'=>empty($ggoodids[$i])?0:$ggoodids[$i],
						'gid'=>empty($productids[$i])?0:$productids[$i],
						'is_cx'=>empty($ggoodcxs[$i])?0:$ggoodcxs[$i],
						'count'=>empty($ggoodcounts[$i])?0:$ggoodcounts[$i],
						'newcost'=>empty($ggoodcosts[$i])?0:$ggoodcosts[$i],
					);
					if(empty($ggoodids[$i])){
						unset($ggood[$i]);
					}
				}
				foreach($ggood as $k=>$val){
					if(!empty($val['gid']) && !empty($val['id'])){
						$ggoodinfo = $this->mysql->select_one("select bagcost,name,img from ".Mysite::$app->config['tablepre']."goods  where id =".$val['gid']." ");
						$productinfo = $this->mysql->select_one("select cost,attrname from ".Mysite::$app->config['tablepre']."product  where id =".$val['id']."");
						$val['name'] = $ggoodinfo['name'];
						$val['img'] = empty($ggoodinfo['img'])?getImgQuanDir(Mysite::$app->config['goodlogo']):getImgQuanDir($ggoodinfo['img']);
						$val['oldcost'] = $productinfo['cost'];
						$val['attrname'] = $productinfo['attrname'];
						$ggoodlist[] = $val;
						$productdown += ($productinfo['cost']-$val['newcost'])*$val['count'];
						$productbag += $ggoodinfo['bagcost']*$val['count'];
						$productnum +=$val['count'];
						$productsum += $val['newcost']*$val['count'];	
					}										
				}	
				#print_r($productdown);
			}
			$data['goodslist'] = array_merge($goodlist,$ggoodlist);
			$data['cartcost'] = number_format(($goodsum+$productsum),2);
			$data['sumcount'] = ($goodnum+$productnum);
			$data['bagcost'] = number_format(($goodbag+$productbag),2);
			$data['goodscxdowncost'] = number_format(($gooddown+$productdown),2);
			
			$data['cxdet'] = array();
			$cxclass = new sellrule();
			$cxclass->setdata($shopid,$data['cartcost'],$shopinfo['shoptype'],$userid,$platform,$paytype,$data['bagcost']);
			$cxinfo = $cxclass->getdata();
			$data['cx_shoudan'] = $cxinfo['cx_shoudan'];
			$data['cx_manjian'] = $cxinfo['cx_manjian'];
			$data['cx_zhekou'] = $cxinfo['cx_zhekou'];
			$data['surecost'] = $cxinfo['surecost'];
			$data['downcost'] = $cxinfo['downcost'];
			$data['cxdet'] = $cxinfo['cxdet'];
			#print_r($cxinfo);
			if(!empty($good)){
				foreach($good as $k=>$v){
					#print_r($v);
					if($v['is_cx'] == 1){
						$data['cx_shoudan'] = 0;
						$data['cx_manjian'] = 0;
						$data['cx_zhekou'] = 0;
						$data['downcost'] = 0;
						$data['surecost'] = $data['cartcost'];
						$data['cxdet'] = array();
						$cxinfo['nops'] = false;
						break;
					}				
				}
			}
			if(!empty($ggood)){
				foreach($ggood as $k=>$v){
					#print_r($v);
					if($v['is_cx'] == 1){
						$data['cx_shoudan'] = 0;
						$data['cx_manjian'] = 0;
						$data['cx_zhekou'] = 0;
						$data['downcost'] = 0;
						$data['surecost'] = $data['cartcost'];
						$data['cxdet'] = array();
						$cxinfo['nops'] = false;
						break;
					}				
				}
			}
			$data['nops'] = $cxinfo['nops'];
            $nowhout = strtotime(date('Y-m-d',time()));//当天最小linux 时间
            $timelist = !empty($shopinfo['postdate'])?unserialize($shopinfo['postdate']):array();
            $data['timelist'] = array();
            $datatimelist = array();
            $checknow = time();
            $whilestatic = $shopinfo['befortime'];
            $nowwhiltcheck = 0;
            while($whilestatic >= $nowwhiltcheck){
                $startwhil = $nowwhiltcheck*86400;
                foreach($timelist as $key=>$value){
                    $stime = $startwhil+$nowhout+$value['s'];
                    $etime = $startwhil+$nowhout+$value['e'];
                    if($etime  > $checknow){
                        $tempt = array();
                        $tempt['value'] = $value['s']+$startwhil;
                        $tempt['s'] = date('H:i',$nowhout+$value['s']);
                        $tempt['e'] = date('H:i',$nowhout+$value['e']);
                        $tempt['d'] = date('Y-m-d',$stime);
                        $tempt['i'] =  $value['i'];
                        $tempt['cost'] =  isset($value['cost'])?$value['cost']:'0';
                        $tempt['name'] = $tempt['s'].'-'.$tempt['e'];
                        $datatimelist[] = $tempt;
                    }
                }
                $nowwhiltcheck = $nowwhiltcheck+1;
            }
           if(!empty($datatimelist)){
               foreach($datatimelist as $k=>$v){
				   $dtime = date("H:i",time());
				   $timearr = explode('-',$v['name']);
					if($k == 0 && $dtime>$timearr[0] && $dtime<$timearr[1]){
						$v['name']='立即配送';					 
					}
                   $data['timelist'][]=$v;
               }
           }
			$data['isopenscoretocost'] = Mysite::$app->config['isopenscoretocost'];
            $score = intval(IReq::get('score'));
            $scoretocost = Mysite::$app->config['scoretocost'];
			$data['scoretocost'] = empty($scoretocost)?0:$scoretocost;
			$data['scoretocostmax'] = empty(Mysite::$app->config['scoretocostmax'])?0:Mysite::$app->config['scoretocostmax'];
            $data['scorelist'] = array(array('name'=>'不使用积分抵扣','cost'=>0));
            if($score > 0 && $scoretocost > 0 && $data['surecost'] > 0){
                $rslt = $score/$scoretocost;
                $cancost = $data['surecost'] > $rslt ? $rslt:$data['surecost'];
				$scoretocostmax = Mysite::$app->config['scoretocostmax'];
				if($scoretocostmax!=0){
					$cancost = $cancost >=$scoretocostmax ?$scoretocostmax:$cancost;
				}
                for($i = 1;$i <= $cancost;$i++){
                    $newScroe = array();
                    $jifenall = $scoretocost * $i;
                    $newScroe['name'] = '使用'.$jifenall.'积分抵扣'.$i.'元';
                    $newScroe['cost'] = $i;
                    $data['scorelist'][] = $newScroe;
                }
            }
			$juanlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan  where uid='".$userid."'  and endtime > ".time()." and status < 2  order by creattime asc ");
			#print_r($juanlist);
			$data['juanlist'] = array();
			if(!empty($juanlist)){
				foreach($juanlist as $k=>$val){
					if($data['cartcost'] >= $val['limitcost']){
						$val['creattime'] = date('Y-m-d',$val['creattime']);
						$val['endtime'] = date('Y-m-d',$val['endtime']);
						$val['paytype'] = explode(',',$val['paytype']);
						#print_r($val['paytype']);
						if(in_array($newpaytype,$val['paytype'])){
							$data['juanlist'][] = $val;
						}						
					}
					
				}
			}
			$data['juanshu'] = empty($data['juanlist'])?0:count($data['juanlist']);
			
            if(!empty($tarelist)){
                foreach($tarelist as $value){
                    if($value['default'] == 1){
						#print_r($shopinfo);print_r($value);
                        $psdata = $this->pscost2($shopinfo,$value['lng'],$value['lat']);
						#print_r($psdata);
                        $value['canps'] = $psdata['canps'];
                        $value['pscost'] =  $psdata['pscost'];
                        $defaultmsg = $value;						
                    }
                }
            }

            if(!empty($minit)){
                $tempdata = $this->getOpenPosttime($shopinfo['is_orderbefore'],$shopinfo['starttime'],$shopinfo['postdate'],$minit,$shopinfo['befortime']);
                $data['addpscost'] = $tempdata['cost'];
            }else{
                if(!empty($data['timelist'])){
                    $tempdata = $this->getOpenPosttime($shopinfo['is_orderbefore'],$shopinfo['starttime'],$shopinfo['postdate'],$data['timelist'][0]['value'],$shopinfo['befortime']);
                    $data['addpscost'] = $tempdata['cost'];
                }
            }
			$data['support_ziti'] = ($this->platpssetinfo['is_allow_ziti']==1&&$shopinfo['is_ziti']==1)?1:0;
			$data['psimg']  = getImgQuanDir(Mysite::$app->config['psimg']);
			$data['shoppsimg']  = getImgQuanDir(Mysite::$app->config['shoppsimg']);
			$data['ztimg']  = getImgQuanDir(Mysite::$app->config['ztimg']);
			$data['shopinfo'] = $shopinfo;
			$data['zttimelist'] = $this->creatzttime($shopid);
			$templatex = $this->pscost2($shopinfo,$lng,$lat);
			#print_r($lng); print_r($lat);
			$data['juli'] = $templatex['juli'];				
        }
        $data['citylist'] = array();
        if($area == 1){
            $citylist = $this->mysql->getarr("select `name`,`adcode` from ".Mysite::$app->config['tablepre']."area where  id > 0 and parent_id = 0  ");
            if(!empty($citylist)){
                foreach($citylist as $ct){
                    $searchLink = Mysite::$app->config['map_comment_link'].'restapi.amap.com/v3/geocode/geo?address='.$ct['name'].'&city='.$ct['adcode'].'&output=json&key='.Mysite::$app->config['map_webservice_key'];
                    $result =json_decode($this->curl_get_content($searchLink), TRUE);
                    $ct['location'] = '';
                    if($result['status'] == 1 && $result['info'] == 'OK'){
                        $ct['location'] = $result['geocodes'][0]['location'];
                    }
                    $data['citylist'][] = $ct;
                }
            }
        } 
        $default_cityid = Mysite::$app->config['default_cityid'];
        $data['default_cityid'] = empty($default_cityid) ? 0 : $default_cityid;
        $data['defaultmsg'] = $defaultmsg;     
        $this->success($data);
    }
	function creatzttime($shopid){
	    $zttime = array();		 
	    $shopinfo =  $this->mysql->select_one("select starttime,is_open,ziti_time from ".Mysite::$app->config['tablepre']."shop where id = ".$shopid."   ");
		 
	    if($shopinfo['is_open'] == 1 && !empty($shopinfo['starttime'])){
		    $shopoptimes = explode('|',$shopinfo['starttime']);//Array([0] => 01:00-12:00[1] => 13:00-14:00[2] => 14:02-23:00)
		    $timearr = array();
			$flagkey = 'x';
			foreach($shopoptimes as $k=>$v){//01:00-12:00
				$atime = explode('-',$v); //Array([0] => 01:00[1] => 12:00)
				$dftime = strtotime($atime[0]) + $shopinfo['ziti_time'] * 60;
				$detime = strtotime($atime[1]);
				if(time() >= strtotime($atime[0]) && time() <= strtotime($atime[1]) ){
					$flagkey = $k;
				}
				while($dftime <= ($detime - $shopinfo['ziti_time'] * 60)) {
				    $timearr[$k][] = date('H:i',$dftime);
					$dftime = $dftime + 20 *60;
				} 
			}				 
			if($flagkey === 'x'){ //不在任何一个时间段内
				foreach($timearr as $k1=>$v1){
					if(strtotime($v1[0]) > time() ){
						foreach($v1 as $k2=>$v2){
							$zttime[] = $v2;
						}
					}	
				}
			}else{//在某个时间段内   该时间段需要重新生成自提时间				
				$nowtimed = $shopoptimes[$flagkey];
				
				$wtime = explode('-',$nowtimed);
				$rtime = time() + $shopinfo['ziti_time'] * 60;
				$ytimearr = array();
				while($rtime <= (strtotime($wtime[1]) - $shopinfo['ziti_time'] * 60)) {
				    $ytimearr[] = date('H:i',$rtime);
					$rtime = $rtime + 20 *60;
				}
				$delkey = 0;
				
				while($delkey <= $flagkey ) {
				    unset($timearr[$delkey]);
					$delkey = $delkey + 1;
				}
				$ptime = array(); 
				foreach($timearr as $k3=>$v3){
					foreach($v3 as $k4=>$v4){
						$ptime[] = $v4;
					}
				}				 
				$zttime = array_merge($ytimearr,$ptime);
			}			 
	    }	
	    return $zttime; 
    }
    //获取单个地址信息
    function oneAddress(){
		$userid = intval(IReq::get('userid'));
        $addressid = intval(IReq::get('id'));
        if(empty($addressid)) $this->message('地址ID为空');
        $data['info'] = $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."address where id='".$addressid."' order by id asc limit 0,50");
		$data['arealist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."address where userid='".$userid."' order by id asc limit 0,50");
        $this->success($data);
    }
	 //更换默认地址
    function changeAddress(){
        $addressid = intval(IReq::get('addressid'));
		$userid = intval(IReq::get('userid'));
        if(empty($addressid)) $this->message('地址ID为空');
		$arr['default'] = 0;
        $this->mysql->update(Mysite::$app->config['tablepre'].'address',$arr,"userid='".$userid."'");
		$arr['default'] = 1;
        $this->mysql->update(Mysite::$app->config['tablepre'].'address',$arr,"id='".$addressid."' and userid='".$userid."' ");
        $data['info'] = $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."address where id='".$addressid."' order by id asc limit 0,50");
		$data['arealist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."address where userid='".$userid."' order by id asc limit 0,50");
        $this->success($data);
    }
	function fabupaotui(){
		$this->getOpenCity();
		$adcode = intval(IFilter::act(IReq::get('adcode'))); 
		$userid = intval(IFilter::act(IReq::get('uid'))); 
		if( empty($adcode) )  $this->message("获取所属城市失败");
		$ptinfoset = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."paotuiset  where cityid = '".$adcode."' ");
        $demandcontent = trim(IFilter::act(IReq::get('beizhu')));  // 需求内容
        $movegoodstype = trim(IFilter::act(IReq::get('movegoodstype')));
        $movegoodscost = trim(IFilter::act(IReq::get('movegoodscost')));
		 // 取货地址： 地址 补充地址  lng lat 
		$getaddress = trim(IReq::get('getaddr')); 		 
		$getlng = trim(IReq::get('getlng'));
		$getlat = trim(IReq::get('getlat'));
		 // 收货地址： 地址 补充地址  lng lat 
		$shouaddress = trim(IReq::get('shouaddr'));			
		$shoulng = trim(IReq::get('shoulng'));
		$shoulat = trim(IReq::get('shoulat'));
		$getphone = trim(IReq::get('getphone'));  // 取货电话
		$shouphone = trim(IReq::get('shouphone'));  // 收货电话
		$shouname = trim(IReq::get('shouname'));//收货人
		$getname = trim(IReq::get('getname'));//取货人
 		$minit = trim(IReq::get('minit'));			// 收/取 货时间
		$ptkg = trim(IReq::get('ptkg'));	// 货 公斤 数
		$ptkm = trim(IReq::get('ptkm'));	//  收取货 地址 两地 距离 km
 		$allkgcost = trim(IReq::get('allkgcost'));		// 重量价格
 		$allkmcost = trim(IReq::get('allkmcost'));		// 距离价格
 		$farecost = trim(IReq::get('farecost'));		// 加价（小费）
 		$allcost = trim(IReq::get('allcost'));		// 总价格
		$pttype = trim(IReq::get('pttype'));		// 	1为帮我送 2为帮我买
		$platpaytype =  $this->mysql->select_one("select paytype from ".Mysite::$app->config['tablepre']."platpsset  where cityid='".$adcode."' "); 
		$paytypestr = $platpaytype['paytype'];
		$paytypearr = explode(',',$paytypestr);
		if(!in_array(1,$paytypearr))$this->message("未开启在线支付，请联系管理员！");	
		$is_default = trim(IReq::get('is_default'));//是否是默认就近购买 1是 0不是
		$paytype = 1;		//  支付方式，默认为在线支付
		if(empty($demandcontent) && $pttype==2 ) $this->message('请简要填写需求内容'); 	
	    //除了帮我买默认就近购买的情况下  都需要验证取货(购买)地址
		if($pttype==2 && $is_default == 1){ 
		    $getaddress = '就近购买';
		}else{
			if( empty($getaddress) || empty($getlng) || empty($getlat)  )  $this->message("请选择取货地址");
		}
 		if( empty($shouaddress) || empty($shoulng) || empty($shoulat)  )  $this->message("请选择收货地址");
		if($pttype==1){
			if( empty($getphone) )  $this->message("请填写取货电话");
			if(!IValidate::suremobi($getphone))   $this->message('请输入正确的手机号'); 	
		}
		if( empty($shouphone) )  $this->message("请填写收货电话");
		if(!IValidate::suremobi($shouphone))   $this->message('请输入正确的手机号'); 	
		if(empty($demandcontent) && $pttype==1 ) $this->message('请简要填写需求内容'); 	
		if($minit == 0 ){
			$data['sendtime'] = time();
		    $data['postdate'] = '立即取货';
		}else{ 
			$tempdata = $this->getOpenPosttime($ptinfoset['is_ptorderbefore'],time(),$ptinfoset['postdate'],$minit,$ptinfoset['pt_orderday']); 
		    if($tempdata['is_opentime'] ==  2) $this->message('选择的配送时间段，店铺未设置');
		    if($tempdata['is_opentime'] == 3) $this->message('选择的配送时间段已超时');
		    $data['sendtime'] = $tempdata['is_posttime'];
		    $data['postdate'] = $tempdata['is_postdate'];
		}
 		$data['pttype'] = $pttype;  // 1为帮我送  2为帮我买	
		$data['admin_id'] = $adcode;
		$data['content'] = $demandcontent;
		$data['shopaddress']  = $getaddress;   
		$data['buyeraddress']  = $shouaddress;  
		if($pttype==1){
			$data['shopphone']  = $getphone;			//取件电话
		}
		$data['buyerphone']  = $shouphone;			//收件电话
		$data['addtime'] = time();		
		$data['ordertype'] = 3;//订单类型 	
		$data['shoptype'] = 100;//订单类型
		$data['paytype'] = $paytype; 
		$data['paystatus'] = 0; 
		$data['movegoodstype'] = $movegoodstype;
		$data['movegoodscost'] = $movegoodscost;
		$data['ptkg'] = $ptkg;
		$data['ptkm'] = $ptkm;
		$data['allkgcost'] = $allkgcost;
		$data['allkmcost'] = $allkmcost;
		$data['farecost'] = $farecost;
		$data['allcost'] = $allcost;
		/*检测订单金额是否小于后台最低金额*/
		if($data['pttype'] == 1){
            $checkcost = $ptinfoset['kmcost'] + $ptinfoset['kgcost'];
		}else{
			$checkcost = $ptinfoset['kmcost'];	
		}
        if($data['allcost'] < $checkcost )$this->message('总金额计算错误，请刷新页面重新下单'); 	
		$data['dno'] = time().rand(1000,9999);
		$data['pstype']  = 2;
		$data['buyeruid']  = $userid;
		$data['buyername'] = $shouname;
		$data['shopname'] = $pttype==2?'':$getname;
		$data['shoplat'] = $shoulat;//店铺lat坐标
		$data['shoplng'] = $shoulng;//店铺lng坐标 pstype
		$data['buyerlat'] = $getlat;//用户lat坐标
		$data['buyerlng'] = $getlng;//用户lng坐标
		/* 计算两点之间的距离  并且 判断是否与前台的  千米距离金额是否一致 */
		$juli = $this->GetDistance2($getlat,$getlng, $shoulat,$shoulng, 1,1); 
		$tempmi = $juli;
		$juli = round($juli/1000,1);		
		$tmpallkmcost =  0;
		if( $juli <= $ptinfoset['km']  ){
			$tmpallkmcost = $ptinfoset['kmcost'];
		}else{
			$addjuli = $juli-$ptinfoset['km'];
			$addnum = floor( ($addjuli/$ptinfoset['addkm']));
			$addcost = $addnum*$ptinfoset['addkmcost'];
			$tmpallkmcost = $ptinfoset['kmcost']+$addcost;
		}
		if( $ptkg <= $ptinfoset['kg']  ){
			$tmpallkgcost = $ptinfoset['kgcost'];
		}else{
			$addkg = $ptkg-$ptinfoset['kg'];
			$addkgnum = floor(($addkg/$ptinfoset['addkg']));
			$addkgcost = $addkgnum*$ptinfoset['addkgcost'];
			$tmpallkgcost = $ptinfoset['kgcost']+$addkgcost; 
		}
		if($pttype == 2){
			$tmpallkgcost =  0;
		}	
 		if($tmpallkgcost != $allkgcost )$this->message("获取重量总金额错误");
		$panduan = Mysite::$app->config['man_ispass'];
		$data['status'] = 0;
		if($panduan != 1 && $data['paytype'] == 0){
			$data['status'] = 1;
		} 	
		$data['ipaddress'] = "";
		$ip_l=new iplocation(); 
		$ipaddress=$ip_l->getaddress($ip_l->getIP());  
		if(isset($ipaddress["area1"])){
			if(function_exists(mb_convert_encoding)){
				$data['ipaddress']  = $ipaddress['ip'].mb_convert_encoding($ipaddress["area1"],'UTF-8','GB2312');//('GB2312','ansi',);
			}else if(function_exists(iconv)){
				$data['ipaddress']  = $ipaddress['ip'].iconv('GB2312',$ipaddress["area1"],'UTF-8');//('GB2312','ansi',);
			}else{
				$data['ipaddress']='0';
			}
		}
		$this->mysql->insert(Mysite::$app->config['tablepre'].'order',$data);
		$orderid = $this->mysql->insertid(); 
		$orderClass = new orderClass();
		$orderClass->writewuliustatus($orderid,1,$data['paytype']);
		$this->success($orderid);
	}
    //获取城市adcode
    function getadcode(){
        $lng = IReq::get('lng');
        $lat = IReq::get('lat');
        if(empty($lng) || empty($lat)) $this->message('坐标错误');
        $location = $lng.','.$lat;
        $searchLink = Mysite::$app->config['map_comment_link'].'restapi.amap.com/v3/geocode/regeo?key='.Mysite::$app->config['map_webservice_key'].'&location='.$location.'&output=json&radius=1000&extensions=all';
        $result =json_decode($this->curl_get_content($searchLink), TRUE);
        if(!empty($result['regeocode'])){
            $adcode = $result['regeocode']['addressComponent']['adcode'];
            $address = $result['regeocode']['addressComponent']['building']['name'];
            if( empty($address) ){
                $address = $result['regeocode']['pois'][0]['name'];
                if( empty($address) ){
                    $address = $result['regeocode']['addressComponent']['district'].$result['regeocode']['addressComponent']['township'];
                    if( empty($address) ){
                        $address = $result['regeocode']['formatted_address'];
                    }
                }
            }
        }else{
            $adcode = 410100;
            $address = '电子商务产业园(郑州高新区)';
        }

        if( !empty($adcode) ){
            $areacodeone =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."areacode where id=".$adcode."  ");
            if( !empty($areacodeone) ){
                $adcodeid = $areacodeone['id'];
                $pid = $areacodeone['pid'];
                $areainfoone =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where  adcode=".$adcodeid." or adcode=".$pid."  ");
                if( !empty($areainfoone) ){
                    $adcode = $areainfoone['adcode'];
                }
            }
        }
        $data['info']['adcode'] = $adcode;
        $data['info']['address'] = $address;
        $this->success($data);
    }
    //搜索地址
    function searchAddress(){
        $searchval = IFilter::act(IReq::get('searchval'));
        $cityname = IFilter::act(IReq::get('cityname'));
        $cityname = empty($cityname) ? Mysite::$app->config['CITY_NAME'] : $cityname;
        $searchLink = Mysite::$app->config['map_comment_link'].'restapi.amap.com/v3/place/text?&keywords='.$searchval.'&city='.$cityname.'&output=json&offset=20&page=1&key='.Mysite::$app->config['map_webservice_key'].'&extensions=all';
        $result =json_decode($this->curl_get_content($searchLink), TRUE);
        $data['list'] = $result['pois'];
        $this->success($data);
    }
    //保存地址
    function saveaddress(){
        $userid = intval(IReq::get('userid'));
        $username = trim(IReq::get('username'));
        $addressid = intval(IReq::get('addressid'));
        $arr['tag'] =  intval(IReq::get('tag'));
        if(empty($addressid))
        {
            $checknum = $this->mysql->counts("select * from ".Mysite::$app->config['tablepre']."address where userid='".$userid."' ");
            if(Mysite::$app->config['addresslimit'] < $checknum)$this->message('member_addresslimit');
            $arr['userid'] = $userid;
            $arr['username'] = $username;
            $arr['phone'] = IFilter::act(IReq::get('phone'));
            $arr['otherphone'] = '';
            $arr['contactname'] = IFilter::act(IReq::get('contactname'));
            $check_message = IFilter::act(IReq::get('check_message'));
            $arr['sex'] = 0;
            $arr['default'] = $checknum == 0?1:0;
            $arr['addtime'] = time();
            if(!(IValidate::len($arr['contactname'],2,6)))$this->message('contactlength');
            if(!(IValidate::suremobi($arr['phone'])))$this->message('errphone');
            $areacode = Mysite::$app->config['areacode'];
            if( $areacode  == 1 ){
                $phonecls = new phonecode($this->mysql,9,$arr['phone']);
                if($phonecls->checkcode($check_message)){
                }else{
                    $this->message($phonecls->getError());
                }
            }
            $arr['bigadr'] =  IFilter::act(IReq::get('bigadr'));
            $arr['lat'] =  IFilter::act(IReq::get('lat'));
            $arr['lng'] =  IFilter::act(IReq::get('lng'));
            $arr['detailadr'] =  IFilter::act(IReq::get('detailadr'));
            $arr['address'] = $arr['bigadr'].$arr['detailadr'];
            if( empty($arr['bigadr']) ||  $arr['bigadr'] == '点击选择地址' ) $this->message('请选择地址！');
            if( empty($arr['detailadr'])  ) $this->message('请填写详细地址！');
            if( empty($arr['lat'])  ) $this->message('获取地图坐标失败，请重新获取！');
            if( empty($arr['lng'])  ) $this->message('获取地图坐标失败，请重新获取！');
            if(!(IValidate::len($arr['address'],3,50)))$this->message('member_addresslength');
            $this->mysql->insert(Mysite::$app->config['tablepre'].'address',$arr);
            $addid = $this->mysql->insertid();
            $this->mysql->update(Mysite::$app->config['tablepre'].'address',array('default'=>0),'userid = '.$userid.' ');
            $this->mysql->update(Mysite::$app->config['tablepre'].'address',array('default'=>1),'userid = '.$userid.' and id='.$addid.'');
            $newid = $addid;
        }else{
            $arr['phone'] = IFilter::act(IReq::get('phone'));
            $arr['otherphone'] = '';
            $arr['contactname'] = IFilter::act(IReq::get('contactname'));
            $arr['sex'] = 0;
            $arr['addtime'] = time();
            if(!(IValidate::len($arr['contactname'],2,6)))$this->message('contactlength');
            if(!(IValidate::suremobi($arr['phone'])))$this->message('errphone');
            $check_message = IFilter::act(IReq::get('check_message'));
            if(Mysite::$app->config['areacode']==1){
                $phonecls = new phonecode($this->mysql,9,$arr['phone']);
                if($phonecls->checkcode($check_message)){
                }else{
                    $this->message($phonecls->getError());
                }
            }

            $arr['bigadr'] =  IFilter::act(IReq::get('bigadr'));
            $arr['lat'] =  IFilter::act(IReq::get('lat'));
            $arr['lng'] =  IFilter::act(IReq::get('lng'));
            $arr['detailadr'] =  IFilter::act(IReq::get('detailadr'));
            $arr['address'] = $arr['bigadr'].$arr['detailadr'];
            if( empty($arr['bigadr']) ||  $arr['bigadr'] == '点击选择地址' ) $this->message('请选择地址！');
            if( empty($arr['detailadr'])  ) $this->message('请填写详细地址！');
            if( empty($arr['lat'])  ) $this->message('获取地图坐标失败，请重新获取！');
            if( empty($arr['lng'])  ) $this->message('获取地图坐标失败，请重新获取！');
            if(!(IValidate::len($arr['address'],3,50)))$this->message('member_addresslength');
            $this->mysql->update(Mysite::$app->config['tablepre'].'address',$arr,'userid = '.$userid.' and id='.$addressid.'');
            $this->mysql->update(Mysite::$app->config['tablepre'].'address',array('default'=>0),'userid = '.$userid.' ');
            $this->mysql->update(Mysite::$app->config['tablepre'].'address',array('default'=>1),'userid = '.$userid.' and id='.$addressid.'');
            $newid = $addressid;
        }
        $data['info'] = $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."address where id='".$newid."' order by id asc limit 0,50");
		$data['arealist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."address where userid='".$userid."' order by id asc limit 0,50");
        $this->success($data);
    }

    //编辑地址
    function editaddress(){
        $what = trim(IFilter::act(IReq::get('what')));
        $addressid = intval(IReq::get('addressid'));
        $userid = intval(IReq::get('userid'));
		$addresstype = trim(IFilter::act(IReq::get('addresstype')));
        if(empty($addressid)) $this->message('member_noexitaddress');
        if($what == 'default')
        {
            $arr['default'] = 0;
            $this->mysql->update(Mysite::$app->config['tablepre'].'address',$arr,"userid='".$userid."'");
            $arr['default'] = 1;
            $this->mysql->update(Mysite::$app->config['tablepre'].'address',$arr,"id='".$addressid."' and userid='".$userid."' ");
            $this->success($data);
        }elseif($what == 'addr')
        {
            $arr['address'] = IFilter::act(IReq::get('controlname'));
            if(!(IValidate::len($arr['address'],3,50))) $this->message('member_addresslength');
            $this->mysql->update(Mysite::$app->config['tablepre'].'address',$arr,"id='".$addressid."' and userid='".$this->member['uid']."' ");
            $this->success('success');
        }elseif($what == 'phone')
        {
            $arr['phone'] = IFilter::act(IReq::get('controlname'));
            if(!IValidate::suremobi($arr['phone'])) $this->message('errphone');
            $this->mysql->update(Mysite::$app->config['tablepre'].'address',$arr,"id='".$addressid."' and userid='".$this->member['uid']."' ");
            $this->success('success');
        }
        elseif($what == 'bak_phone')
        {
            $arr['otherphone'] = IFilter::act(IReq::get('controlname'));
            if(!IValidate::suremobi($arr['otherphone']))$this->message('errphone');

            $this->mysql->update(Mysite::$app->config['tablepre'].'address',$arr,"id='".$addressid."' and userid='".$this->member['uid']."' ");
            $this->success('success');
        }
        elseif($what == 'recieve_name')
        {
            $arr['contactname'] =  IFilter::act(IReq::get('controlname'));
            if(!(IValidate::len($arr['contactname'],2,6))) $this->message('contactlength');
            $this->mysql->update(Mysite::$app->config['tablepre'].'address',$arr,"id='".$addressid."' and userid='".$this->member['uid']."' ");
            $this->success('success');
        }else{
            $this->message('nodefined_func');
        }
    }

    //删除地址
    function deladdress(){
        $addressid = intval(IReq::get('addressid'));
        $userid = intval(IReq::get('userid'));
        if(empty($addressid)) $this->message('member_noexitaddress');
        $this->mysql->delete(Mysite::$app->config['tablepre'].'address',"id = '$addressid' and  userid='".$userid."'");
        $this->success('success');
    }


    //订单列表
    function userorder(){
        $userid = intval(IReq::get('userid'));
        $pageinfo = new page();
        $pageinfo->setpage(intval(IReq::get('page')),10);
		$userinfo = $this->mysql->select_one("select score,admin_id from ".Mysite::$app->config['tablepre']."member where uid='".$userid."' ");
        $orderSelect = "`id`,`shopid`,`addtime`,`pstype`,`shopname`,`allcost`,`status`,`is_ping`,`is_acceptorder`,`shoptype`,`is_ziti`,`is_make`,`paytype`,`paystatus`,`posttime`,`psuid`,`psstatus`,`is_reback`,`yhjids`,`scoredown`,`buyeruid`";
        $datalist = $this->mysql->getarr("select ".$orderSelect." from ".Mysite::$app->config['tablepre']."order where buyeruid='".$userid."' and shoptype != 100 and is_userhide !=1  order by id desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
        $backdata = array();
        foreach($datalist as $key=>$value){
			//自动关闭订单
          if($value['paytype'] == 1 && $value['paystatus'] == 0 && $value['status'] < 3){
              $checktime = time() - $value['addtime'];
              if($checktime > 900){
                  //说明该订单可以关闭
				 //退返本单的优惠券及积分
				 $yhjids = $value['yhjids'];
				 if(!empty($yhjids)){
						$yhjarr = explode(',',$yhjids);
						foreach($yhjarr as $k=>$v){
							$yhjdata['status'] = 0;
							$this->mysql->update(Mysite::$app->config['tablepre'].'juan',$yhjdata,"id ='".$v."' ");
						}
					} 
			     if($value['scoredown'] > 0){
					$this->mysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`+'.$value['scoredown'],"uid ='".$value['buyeruid']."' ");
				    $jfdata['userid'] =  $value['buyeruid'];
					$jfdata['type'] = 1;
					$jfdata['addtype'] = 1;
					$jfdata['result'] = $value['scoredown'];
					$jfdata['addtime'] = time();
					$jfdata['title'] = '超时订单退还积分';
					$jfdata['content'] = '超时订单退还积分'.$value['scoredown'];  
					$jfdata['acount'] = $userinfo['score'] + $value['scoredown'];
		 		    $this->mysql->insert(Mysite::$app->config['tablepre'].'memberlog',$jfdata);
				 }		
                  $cdata['status'] = 4;
                  $this->mysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$value['id']."'");
                  $this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = {$value['id']} and status != 3");
                  /*更新订单 状态说明*/
                  $statusdata['orderid']     =  $value['id'];
                  $statusdata['addtime']     =  $value['addtime']+900;
                  $statusdata['statustitle'] =  "订单已取消";
                  $statusdata['ststusdesc']  =  "订单支付超时，系统已自动取消订单";
                  $this->mysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata);
				  //返回商品数量
					$goosinfo =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id = ".$value['id']."");
					if(!empty($goosinfo)){
						foreach($goosinfo as $k=>$val){
							if($val['goodsid'] > 0 && $val['goodscount'] > 0){
								if($val['product_id'] > 0){
									 $this->mysql->update(Mysite::$app->config['tablepre'].'product',"`stock` = `stock`-".$val['goodscount'],"id='".$val['product_id']."'");
								}
								$this->mysql->update(Mysite::$app->config['tablepre'].'goods',"`count` = `count`+".$val['goodscount'],"id='".$val['goodsid']."'");
								$this->mysql->update(Mysite::$app->config['tablepre'].'goods',"`sellcount` = `sellcount`-".$val['goodscount'],"id='".$val['goodsid']."'");
							}
						}
					}
              }
          }
            $listdet = $this->mysql->getarr("select `goodsname` from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$value['id']."'");
            $value['det'] = '';
            foreach($listdet as $k=>$v){
                if(!empty($value['det'])){
                    $value['det'] .= ','.$v['goodsname'];
                }else{
                    $value['det'] = $v['goodsname'];
                }
            }
            $shopinfo = $this->mysql->select_one("select `shoplogo`,`ziti_time` from ".Mysite::$app->config['tablepre']."shop where id='".$value['shopid']."'");
            $value['shoplogo'] = empty($shopinfo['shoplogo'])? getImgQuanDir(Mysite::$app->config['shoplogo']):getImgQuanDir($shopinfo['shoplogo']);
				/*判断订单显示状态*/
			if($value['pstype'] == 1){//商家配送情况
				if($value['status'] == 3){
					$orderstatus = '订单已完成';
				}elseif($value['status'] > 3 ){
					$orderstatus = '订单关闭';
				}else{
					if($value['paytype'] == 1 && $value['paystatus'] == 0){//在线支付未付
						$orderstatus = '待付款';
					}else{
						if($value['is_make'] == 0){
							$orderstatus = '待商家接单';
						}elseif($value['is_make'] == 1){
							if($value['is_ziti'] == 1){
								if($value['posttime']-time() <= $shopinfo['ziti_time']*60 ){
									$orderstatus = '等待到店自取';
								}else{
									$orderstatus = '商家已接单';
								}
							}else{
								if($value['status'] == 2){
									$orderstatus = '配送中';
								}else{
									$orderstatus = '商家已接单';
								}
							}	
						}else{
							$orderstatus = '商家未接单';
						}
					}	
				}
			}else{//平台配送情况
				if($value['status'] > 3){
					$orderstatus = '订单关闭';
				}else{
					if($value['status'] == 3){
						$orderstatus = '订单已完成';
					}else{
						if($value['paytype'] == 1 && $value['paystatus'] == 0){//在线支付未付
							 $orderstatus = '待付款';
						}else{
							if($value['is_make'] == 0){
								$orderstatus = '待商家接单';
							}elseif($value['is_make'] == 1){
								if($value['is_ziti'] == 1){
									if($value['posttime']-time() <= $shopinfo['ziti_time']*60 ){
										$orderstatus = '等待到店自取';
									}else{
										$orderstatus = '商家已接单';
									}
								}else{
									if($value['psuid'] > 0){
										if($value['psstatus'] == 1){
											$orderstatus = '配送员已接单';
										}
										if($value['psstatus'] == 2){
											$orderstatus = '配送员已到店';
										}
										if($value['psstatus'] == 3){
											$orderstatus = '配送中';
										}
									}else{
										$orderstatus = '商家已接单';
									}
								}								
							}else{
								$orderstatus = '商家未接单';
							}
						}							
					}
				}					 
			}
			if($value['is_reback'] == 2){
				$orderstatus = '订单关闭';
			}
			$value['orderwuliustatus'] = $orderstatus;
			$value['psstatus'] = empty($value['psstatus'])?0:$value['psstatus'];
            $value['addtime'] = date('Y-m-d H:i',$value['addtime']);
            if($value['is_ziti'] == 1){
				$value['provide'] = '支持到店自取服务';
			}else{
				if($value['pstype'] == 1){
					$value['provide'] = '由商家提供配送服务';					
				}else{
					$value['provide'] = '由平台提供配送服务';
				}				
			}
			$value['is_show_quhuo'] = 0;  	             
			 if($value['is_reback'] != 2 && $value['status'] < 3 && $value['is_make'] == 1 ){
				if($value['posttime']-time() <= $shopinfo['ziti_time']*60 && $value['is_ziti'] == 1){//自提单
					$value['is_show_quhuo'] = 1;
				}
				if($value['pstype'] == 1 && $value['status'] == 2){//商家配送 已发货
					$value['is_show_quhuo'] = 1;
				}
				 
			 } 
            $backdata[] =$value;
        }
        $data['orderlist'] = $backdata;
		$data['callphone'] = Mysite::$app->config['litel'];
		$data['allowreback'] = Mysite::$app->config['allowreback'];
		$shangou = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."btninfo  where cityid = '".$userinfo['admin_id']."' and name = 'shangou' ");
		$paotui = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."btninfo  where cityid = '".$userinfo['admin_id']."' and name = 'paotui' ");
		$data['shangou'] = empty($shangou['is_show'])?0:$shangou['is_show'];
		$data['paotui'] = empty($paotui['is_show'])?0:$paotui['is_show'];
        $this->success($data);
    }
    //订单详情
    function ordershow(){
        $orderid = intval(IReq::get('orderid'));
        $userid = intval(IReq::get('userid'));
		$userinfo = $this->mysql->select_one("select score from ".Mysite::$app->config['tablepre']."member where uid='".$userid."' ");
        $shareinfo = $this->mysql->select_one("select title,img,`describe`  from ".Mysite::$app->config['tablepre']."juanshowinfo where type=2 order by orderid asc  ");
        if( empty($shareinfo) ){
            $shareinfo['title'] = Mysite::$app->config['sitename'];
            $shareinfo['img'] = Mysite::$app->config['sitelogo'];
            $shareinfo['describe'] = Mysite::$app->config['sitename'];
        }
        $data['shareinfo'] = $shareinfo;
        $where = "  where type=2 and addtime < ".time()."  and is_open = 1 and juannum > 0 ";
        $checkinfosendjuan = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."juanrule ".$where." order by orderid asc ");
        $data['checkinfosendjuan'] = $checkinfosendjuan;
        if(!empty($orderid)){
            $order = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$userid."' and id = ".$orderid."");
            if(empty($order)){
                $this->message('订单不存在');
            }else{
                //自动关闭订单
            if($order['paytype'] == 1 && $order['paystatus'] == 0 && $order['status'] < 3){
                $checktime = time() - $order['addtime'];
                if($checktime > 900){
                    //说明该订单可以关闭
                    //退返本单的优惠券及积分
					 $yhjids = $order['yhjids'];
					 if(!empty($yhjids)){
							$yhjarr = explode(',',$yhjids);
							foreach($yhjarr as $k=>$v){
								$yhjdata['status'] = 0;
								$this->mysql->update(Mysite::$app->config['tablepre'].'juan',$yhjdata,"id ='".$v."' ");
							}
						}  
					 if($order['scoredown'] > 0){
						$this->mysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`+'.$order['scoredown'],"uid ='".$order['buyeruid']."' ");
						$jfdata['userid'] =  $order['buyeruid'];
						$jfdata['type'] = 1;
						$jfdata['addtype'] = 1;
						$jfdata['result'] = $order['scoredown'];
						$jfdata['addtime'] = time();
						$jfdata['title'] = '超时订单退还积分';
						$jfdata['content'] = '超时订单退还积分'.$order['scoredown'];  
						$jfdata['acount'] = $userinfo['score'] + $order['scoredown'];
						$this->mysql->insert(Mysite::$app->config['tablepre'].'memberlog',$jfdata);
					 }		
					if($order['yhjids']>0){
                        $jdata['status'] =1;
                        $this->mysql->update(Mysite::$app->config['tablepre'].'juan',$jdata,"id='".$order['yhjids']."'");
                    }
                    $cdata['status'] = 4;
                    $this->mysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$orderid."'");
                    $this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = '$orderid' and status != 3");
                    /*更新订单 状态说明*/
                    $statusdata['orderid']     =  $orderid;
                    $statusdata['addtime']     =  $order['addtime']+900;
                    $statusdata['statustitle'] =  "自动关闭订单";
                    $statusdata['ststusdesc']  =  "在线支付订单，未支付自动关闭";
                    $this->mysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata);
                    $order['status'] = 4;
					//返回商品数量
					$goosinfo =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id = ".$orderid."");
					if(!empty($goosinfo)){
						foreach($goosinfo as $k=>$val){
							if($val['goodsid'] > 0 && $val['goodscount'] > 0){
								if($val['product_id'] > 0){
									 $this->mysql->update(Mysite::$app->config['tablepre'].'product',"`stock` = `stock`-".$val['goodscount'],"id='".$val['product_id']."'");
								}
								$this->mysql->update(Mysite::$app->config['tablepre'].'goods',"`count` = `count`+".$val['goodscount'],"id='".$val['goodsid']."'");
								$this->mysql->update(Mysite::$app->config['tablepre'].'goods',"`sellcount` = `sellcount`-".$val['goodscount'],"id='".$val['goodsid']."'");
							}
						}
					}

                }
            }
                $scoretocost = Mysite::$app->config['scoretocost'];
                $order['scoredown'] =  number_format(($order['scoredown']/$scoretocost),2);//抵扣积分
                $order['ps'] = $order['shopps'];
				 $shopinfo =  $this->mysql->select_one("select ziti_time from ".Mysite::$app->config['tablepre']."shop where id='".$order['shopid']."'");
				 $order['is_show_quhuo'] = 0;  	             
				 if($order['is_reback'] != 2 && $order['status'] < 3 && $order['is_make'] == 1 ){
					if($order['posttime']-time() <= $shopinfo['ziti_time']*60 && $order['is_ziti'] == 1){//自提单
						$order['is_show_quhuo'] = 1;
					}
					if($order['pstype'] == 1 && $order['status'] == 2){//商家配送 已发货
						$order['is_show_quhuo'] = 1;
					}
					 
				 } 
                $orderdet = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$order['id']."'");
                $order['cp'] = count($orderdet);
                $order['surestatus'] = $order['status'];
                $order['basetype'] = $order['paytype'];
                $order['basepaystatus'] =$order['paystatus'];
                $order['addtime'] = date('Y-m-d H:i:s',$order['addtime']);
                $order['posttime'] = date('Y-m-d H:i:s',$order['posttime']);
                if($order['paystatus'] == 0){
                    $paystatusname = '（未付）';
                }elseif($order['paystatus'] == 1){
                    $paystatusname = '（已付）';
                }
                if($order['paytype'] == 0){
                    $order['paystatusname'] = '货到支付'.$paystatusname;
                }elseif($order['paytype'] == 1){
                    if($order['paytype_name'] == ''){
                        $order['paystatusname'] = '在线支付'.$paystatusname;
                    }else{
                        if($order['paytype_name'] == 'open_acout'){
                            $order['paystatusname'] = '余额支付'.$paystatusname;
                        }elseif($order['paytype_name'] == 'weixin'){
                            $order['paystatusname'] = '微信支付'.$paystatusname;
                        }elseif($order['paytype_name'] == 'alipay' || $order['paytype_name'] == 'alimobile'){
                            $order['paystatusname'] = '支付宝支付'.$paystatusname;
                        }
                    }
                }
                if($order['pstype'] == 1){
                    $pstypename = '商家';
                }else{
                    $pstypename = '平台';
                }
				if($order['is_ziti'] == 1){
					$order['pstypename'] = '支持到店自取服务';
				}else{
					$order['pstypename'] = '本订单由'.$pstypename.'提供配送服务';
				}
                $order['cxdet'] = unserialize($order['cxdet']);
				$data['allowreback'] = empty(Mysite::$app->config['allowreback'])?0:1;
                $data['order'] = $order;
                $data['orderdet'] = $orderdet;
                $data['psbpsyinfo'] = array();
                if(   $order['psuid'] > 0){
                    if(  $order['status'] == 2   ){
                        if(  $order['pstype'] == 2  ){
                            $psbinterface = new psbinterface();
                            $data['psbpsyinfo'] = $psbinterface->getpsbclerkinfo($order['psuid']);

                            if( !empty($data['psbpsyinfo']) && !empty($data['psbpsyinfo']['posilnglat']) ){
                                $posilnglatarr = explode(',',$data['psbpsyinfo']['posilnglat']);
                                $posilng = $posilnglatarr[0];
                                $posilat = $posilnglatarr[1];
                                if( !empty($posilng) && !empty($posilat)  ){
                                    $data['psbpsyinfo']['posilnglatarr'] = $posilnglatarr;
                                }else{
                                    $data['psbpsyinfo'] = array();
                                }

                            }
                        }else if(   $order['pstype'] == 0    ){
                            $data['psbpsyinfo'] = $this->mysql->select_one("select uid,lng,lat from ".Mysite::$app->config['tablepre']."locationpsy where uid='".$order['psuid']."' ");
                            if( !empty($data['psbpsyinfo'])  &&  !empty($data['psbpsyinfo']['lng'])  &&  !empty($data['psbpsyinfo']['lat'])      ){
                                $data['psbpsyinfo']['posilnglat'] = $data['psbpsyinfo']['lng'].','.$data['psbpsyinfo']['lat'];
                                $data['psbpsyinfo']['posilnglatarr'] = explode(',',$data['psbpsyinfo']['posilnglat']);
                            }else{
                                $data['psbpsyinfo'] = array();
                            }
                        }else{
                            $data['psbpsyinfo'] = array();
                        }
                    }else if(  $order['status'] == 3 &&  (  $order['pstype'] == 0 ||  $order['pstype'] == 2  ) ){
                        $psyoverlng = $order['psyoverlng'];
                        $psyoverlat = $order['psyoverlat'];
                        $data['psbpsyinfo']['clerkid'] = $order['psuid'];
                        $data['psbpsyinfo']['posilnglat'] = $psyoverlng.','.$psyoverlat;
                        $data['psbpsyinfo']['posilnglatarr'] = explode(',',$data['psbpsyinfo']['posilnglat']);
                    }
                }


                $orderwuliustatus = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderstatus where orderid = ".$order['id']." order by id desc limit 0,10 ");
                $data['orderwuliustatus'] = array();
                if(!empty($orderwuliustatus)){
                    foreach($orderwuliustatus as $vvl){
                        $vvl['addtime'] = date('m月d日 H:i',$vvl['addtime']);
                        $vvl['telnum'] = 0;
                        $vvl['showmap'] = 0;
                        if($vvl['statustitle'] == '配送员已抢单'){
                            $vvl['ststusdesc'] = '配送员电话：';
                            $vvl['telnum'] = $order['psemail'];
                        }elseif($vvl['statustitle'] == '商家已接单'){
                            $vvl['ststusdesc'] = '商家电话：';
                            $vvl['telnum'] = $order['shopphone'];
                        }elseif($vvl['statustitle'] == '配送员已取货'){
                            if($order['psuid'] > 0 && !empty($data['psbpsyinfo']) && !empty($data['psbpsyinfo']['posilnglat'])){
                                $vvl['showmap'] = 1;
                                $posilnglat = explode(',',$data['psbpsyinfo']['posilnglat']);

                                $vvl['markers'] = array(
                                    array(
                                        'id'=> 0,
                                        'iconPath'=> "/images/psylocation_icon.png",
                                        'latitude'=> $posilnglat[1],
                                        'longitude'=> $posilnglat[0],
                                        'width'=> 30,
                                        'height'=> 30
                                    )
                                );
                                $vvl['maplng'] = $posilnglat[0];
                                $vvl['maplat'] = $posilnglat[1];
                            }
                        }elseif($vvl['statustitle'] == '订单已提交'){
                            $vvl['ststusdesc'] = '订单已提交，等待商家确认';
                        }
                        $data['orderwuliustatus'][] = $vvl;
                    }
                }
				
                $paylist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."paylist where type = 0 or type=2  order by id asc limit 0,50");
                $data['paylist'] = $paylist;
                $this->success($data);
            }
        }else{
            $this->message('订单ID为空');
        }
    }
    //用户确认收货
    function acceptorder(){
        $userid = intval(IReq::get('userid'));
        $orderid = intval(IReq::get('orderid'));
        $userctlord = new userctlord($orderid,$userid,$this->mysql);
        if($userctlord->sureorder() == false){
            $this->message($userctlord->Error());
        }else{
            $this->success('success');
        }
    }
    // 用户删除订单
    function userdelorder(){
        $userid = intval(IReq::get('userid'));
        $orderid = intval(IReq::get('orderid'));
        $userctlord = new userctlord($orderid,$userid,$this->mysql);
        if($userctlord->delorder() == false){
            $this->message($userctlord->Error());
        }else{
            $this->success('success');
        }
    }
    //取消订单
    function userunorder(){
        $userid = intval(IReq::get('userid'));
        $orderid = intval(IReq::get('orderid'));
        $userctlord = new userctlord($orderid,$userid,$this->mysql);
        if($userctlord->unorder() == false){
            $this->message($userctlord->Error());
        }else{
            $this->success('success');
        }
    }

    //评价订单页面
    function commentorder(){
        $orderid = intval(IReq::get('orderid'));
        $userid = intval(IReq::get('userid'));
        if(empty($userid)) $this->message('用户ID为空');
        $data['orderdet'] = array();
        if(!empty($orderid)){
            $order = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$userid."' and id = ".$orderid."");
            if(empty($order)){
                $this->message('订单不存在');
            }else{
                $data['order'] = $order;
                $orderdet = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$order['id']."'");
                if(!empty($orderdet)){
                    foreach($orderdet as $val){
                        $val['evaluate'] = 5;
                        $data['orderdet'][] = $val;
                    }
                }
                $tempcoment = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."comment where orderid='".$order['id']."'");
                $data['comment'] = array();
                foreach($tempcoment as $key=>$value){
                    $data['comment'][$value['orderdetid']] = $value;
                }
            }
        }else{
            $this->message('订单ID为空');
        }
        $this->success($data);
    }
    //评价订单
    function yijianping(){
        $orderid = intval( IFilter::act(IReq::get('orderid')) );
        $userid = intval(IReq::get('userid'));
        if(empty($userid)) $this->message('用户ID为空');
        if(empty($orderid)) $this->message('订单ID为空');
        $orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$userid."' and id = ".$orderid."");
        if(empty($orderinfo)) $this->message('订单不存在');
        $memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$userid."' ");
        if(empty($memberinfo)) $this->message('用户不存在');
        if($orderinfo['is_ping'] == 1) $this->message('order_isping');
        $orderdet = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$orderinfo['id']."'");
        $data['orderid'] = $orderinfo['id'];
        $data['shopid'] = $orderinfo['shopid'];
        $data['uid'] = $userid;
        $data['addtime'] = time();
        $data['is_show'] = 0;
        $shoppointnum =  trim( IFilter::act(IReq::get('shoppointnum')) );
        $shopsudupointnum =  intval( IFilter::act(IReq::get('shopsudupointnum')) );
        if(empty($shoppointnum)) $this->message('请评论总体评价');
        if(empty($shopsudupointnum)) $this->message('请评论配送服务');

        foreach($orderdet as $key=>$value){
            $data['point'] = intval( IFilter::act(IReq::get('goodsid_'.$value['id'])) );
            $data['content'] =  trim( IFilter::act(IReq::get('content_'.$value['id'])) );
            $data['orderdetid'] = $value['id'];
            $data['goodsid'] =   $value['goodsid'];
            if(!empty($data['point']) || !empty($data['content']) ){
                $this->mysql->insert(Mysite::$app->config['tablepre'].'comment',$data);
                $udata['status'] = 1;
                $this->mysql->update(Mysite::$app->config['tablepre'].'orderdet',$udata,"id='".$value['id']."'");
                //商品评分
                $goodinfo  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goods where id='".$value['goodsid']."'  ");
                $goodpointcount = $goodinfo['pointcount'];
                $goodnewpoint['point'] = intval($goodinfo['point']+$data['point']);
                $goodnewpoint['pointcount'] = intval($goodpointcount+1);
                $this->mysql->update(Mysite::$app->config['tablepre'].'goods',$goodnewpoint,"id='".$value['goodsid']."'");
                /*写日志*/
                $issong = 1;
                if(intval(Mysite::$app->config['commentday']) > 0){//检测是否赠送积分
                    $uptime = Mysite::$app->config['commentday']*24*60*60;
                    $uptime = $orderinfo['addtime'] +$uptime;
                    if($uptime > time()){
                        $issong = 1;
                    }else{
                        $issong = 0;
                    }
                }
                $fscoreadd = 0;
                if(intval(Mysite::$app->config['commenttype']) > 0 && $issong == 1)
                { //赠送积分 大于0赠送积分到用户帐号  赠送基础积分
                    $scoreadd = Mysite::$app->config['commenttype'];
                    $checktime = date('Y-m-d',time());
                    $checktime = strtotime($checktime);
                    $checklog = $this->mysql->select_one("select sum(result) as jieguo from ".Mysite::$app->config['tablepre']."memberlog where type = 1 and   userid = ".$userid." and addtype =1 and  addtime > ".$checktime);
                    if(Mysite::$app->config['maxdayscore'] > 0){
                        $checkguo = $checklog['jieguo']+$scoreadd;
                        if($checkguo < Mysite::$app->config['maxdayscore']){
                            //最大值小于当前和
                        }elseif(Mysite::$app->config['maxdayscore'] > $checklog['jieguo']){
                            //最大指 大于 已增指
                            $scoreadd = Mysite::$app->config['maxdayscore'] - $checklog['jieguo'];
                        }else{
                            $scoreadd = 0;
                        }
                    }
                    if($scoreadd > 0){
                        $this->mysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`+'.$scoreadd,"uid='".$userid."'");
                        $fscoreadd =$scoreadd;
                        $memberallcost = $memberinfo['score']+$scoreadd;
                        $this->memberCls->addlog($userid,1,1,$scoreadd,'评价商品','评价商品'.$orderdet['goodsname'].'获得'.$scoreadd.'积分',$memberallcost);
                    }
                }
            }
        }
        $this->mysql->update(Mysite::$app->config['tablepre'].'order','`is_ping`=1',"id='".$orderinfo['id']."'");
        $ordCls = new orderclass();
        $ordCls->writewuliustatus($orderinfo['id'],11,$orderinfo['paytype']);  // 用户已评价订单，完成订单
        // 查询子订单是否所有的状态都为 1，  是的话更新订单标志
        $shuliang  = $this->mysql->counts("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$orderinfo['id']."' and status = 0");
        if($shuliang < 1)//订单已评价完毕
        {
            if(intval(Mysite::$app->config['commentscore']) > 0 && $issong ==  1){//扩张积分 大于0
                $scoreadd = intval(Mysite::$app->config['commentscore'])*$orderinfo['allcost'];
                $checktime = date('Y-m-d',time());
                $checktime = strtotime($checktime);
                $checklog = $this->mysql->select_one("select sum(result) as jieguo from ".Mysite::$app->config['tablepre']."memberlog where type = 1 and   userid = ".$userid." and addtype =1 and  addtime > ".$checktime);
                if(Mysite::$app->config['maxdayscore'] > 0){
                    $checkguo = $checklog['jieguo']+$scoreadd;
                    if($checkguo < Mysite::$app->config['maxdayscore']){
                        //最大值小于当前和
                    }elseif(Mysite::$app->config['maxdayscore'] > $checklog['jieguo']){
                        //最大指 大于 已增指
                        $scoreadd = Mysite::$app->config['maxdayscore'] - $checklog['jieguo'];
                    }else{
                        $scoreadd = 0;
                    }
                }
                if($scoreadd > 0){
                    $this->mysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`+'.$scoreadd,"uid='".$userid."'");
                    $memberallcost = $memberinfo['score']+$scoreadd+$fscoreadd;
                    $this->memberCls->addlog($userid,1,1,$scoreadd,'评价完订单','评价完订单'.$orderinfo['dno'].'奖励，'.$scoreadd.'积分',$memberallcost);
                }
            }
        }

        $shopinfo  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$orderinfo['shopid']."' ");
        $shuliangx = $shopinfo['point'];
        $pointcount = $shopinfo['pointcount'];
        $psservicepoint = $shopinfo['psservicepoint'];
        $psservicepointcount = $shopinfo['psservicepointcount'];
        $newpoint['point'] = intval($shoppointnum+$shuliangx);
        $newpoint['pointcount'] = intval($pointcount+1);
        $newpoint['psservicepoint'] = intval($psservicepoint+$shopsudupointnum);
        $newpoint['psservicepointcount'] = intval($psservicepointcount+1);
        $tjshop  = $this->mysql->select_one("select sum(goodscount) as sellcount from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$orderinfo['id']."'  ");
        if(!empty($tjshop) && $tjshop['sellcount'] > 0){
            $newpoint['sellcount'] = $shopinfo['sellcount']+$tjshop['sellcount'];
        }

        $this->mysql->update(Mysite::$app->config['tablepre'].'shop',$newpoint,"id='".$orderinfo['shopid']."'");
        $this->mysql->update(Mysite::$app->config['tablepre'].'orderps','`status`=3',"orderid='".$orderinfo['id']."'");
        $psbinterface = new psbinterface();
        $psbinterface->pingpsb($orderinfo['id'],$shopsudupointnum,'');
        $this->success('success');
    }

    //申请退款页面、查看退款详情页面
    function drawbacklog(){
        $userid = intval(IReq::get('userid'));
        $orderid = intval(IReq::get('orderid'));
        if(!empty($orderid)){
			$statusarr = array('0'=>'正常状态','1'=>'待平台处理','2'=>'退款成功','3'=>'退款失败','4'=>'待商家处理','5'=>'退款结束');
			$titlearr = array('0'=>'提交申请退款','1'=>'退款关闭','2'=>'商家同意退款','3'=>'商家拒绝退款','4'=>'退款成功');
            $order = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$userid."' and id = ".$orderid."");
            $data['order'] = $order;
            $data['drawbacklog'] = null;
			$data['showbtn'] = 1;
			if($order['status'] == 3 ){
				$data['showbtn'] = 0;
			}
			$data['backacount'] = '账户余额';
			if($order['paytype_name'] == 'weixin'){
				$data['backacount'] = '微信';
			}elseif($order['paytype_name'] == 'alipay' || $order['paytype_name'] == 'alimobile'){
				$data['backacount'] = '支付宝';
			} 
            if($order['is_reback'] > 0){
                $drawbacklog =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid='".$order['id']."' order by id DESC  ");
               $data['drawbacklog'] = array();
				foreach($drawbacklog as $k=>$v){
					$value['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
					$value['title'] = $titlearr[$v['status']];					
					
					if($v['status'] == 0){
						$value['content1'] = '退款理由：'.$v['reason'];
					    $value['content2'] = '退款说明：'.$v['content'];
					}
					if($v['status'] == 1){
						$value['content1'] = '你已取消了退款申请';
                        $value['content2'] = '';						
					}
					if($v['status'] == 2){
						$value['content1'] = '商家同意退款，等待平台处理';
						$value['content2'] = '';
					}
					if($v['status'] == 3){
						$value['content1'] = '拒绝理由：'.$v['reason'];
                        $value['content2'] = '';						
					}
					if($v['status'] == 4){
						$value['content1'] = '平台已退款，退款金额将在1~3个工作日内原路返回';
                        $value['content2'] = '';						
					}
					$data['drawbacklog'][] = $value; 
				}
				if($drawbacklog[0]['status'] ==1 || $drawbacklog[0]['status'] ==2 ){
					$data['showbtn'] = 0;
				}
				$data['status'] = $statusarr[$order['is_reback']];
				$data['nowstatus'] = $drawbacklog[0]['status'];
                $data['cost'] = $drawbacklog[0]['cost'];
            }
			$data['drawsmlist'] =  array();
            $drawsmlist = unserialize(Mysite::$app->config['drawsmlist']);
			foreach($drawsmlist as $k=>$val){
				if(!empty($val)){
					$data['drawsmlist'][] = $val;
				}
			}
            $this->success($data);
        }else{
            $this->message('订单ID为空');
        }
    }
    //提交退款申请
    function savedrawbacklog(){
        $drawbacklog = new drawbacklog($this->mysql);
        $data['allcost'] =  IFilter::act(IReq::get('allcost'));
        $data['orderid'] = intval(IFilter::act(IReq::get('orderid')));// 订单id
        $data['reason'] = trim(IFilter::act(IReq::get('reason'))); //退款原因
		$data['status'] = 0;  
        $data['content'] = trim(IFilter::act(IReq::get('content'))); //退款详细内容说明
        $data['typeid'] = intval(IFilter::act(IReq::get('typeid'))); //支付类型
        $data['uid'] = intval(IFilter::act(IReq::get('userid'))); //用户ID
		if(empty($data['reason'])) $this->message('请选择退款原因');
		if(empty($data['content']))$this->message('请填写退款详细内容说明');
        $orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$data['orderid']."'  ");
		if($orderinfo['shoptype'] == 100){
			$rdata['uid'] = $data['uid'];
			$rdata['username'] = $orderinfo['buyername'];
			$rdata['reason'] = $data['reason'];
			$rdata['orderid'] = $data['orderid'];
			$rdata['shopid'] = $orderinfo['shopid'];		
			$rdata['content'] = $data['content'];	
			$rdata['status'] = 0;//  退款状态 0待处理 1退款结束 2商家同意退款 3退款失败 4平台退款
			$rdata['addtime'] = time();
			$rdata['cost'] = $orderinfo['allcost'];
			$rdata['admin_id'] = $orderinfo['admin_id'];
			$rdata['type'] = 0;
			$this->mysql->insert(Mysite::$app->config['tablepre'].'drawbacklog',$rdata);   //写退款记录	 	
			$shenhedrawback = Mysite::$app->config['shenhedrawback'];//退款是否需要平台审核 1需要审核
			$orderClass = new orderClass();			
			if($shenhedrawback == 1){//需要审核 
	              $orderClass->writewuliustatus($data['orderid'],13,$orderinfo['paytype']);   
				  $this->mysql->update(Mysite::$app->config['tablepre'].'order',array('is_reback'=>1),"id='".$data['orderid']."'");
				  $psbinterface = new psbinterface();
				  if($psbinterface->psbdraworder($orderinfo['id'])){
				  }
				  $this->success('success');  
			}else{//不需要审核
				if($orderinfo['paytype_name'] == 'open_acout'){
					if(!empty($orderinfo['buyeruid'])){		 
						$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$orderinfo['buyeruid']."'   "); 
						$memclas = new memberclass($this->mysql);	
						if(!empty($memberinfo)){
							$this->mysql->update(Mysite::$app->config['tablepre'].'member','`cost`=`cost`+'.$rdata['cost'],"uid ='".$orderinfo['buyeruid']."' ");			 
						}	
						$shengyucost = $memberinfo['cost']+$rdata['cost'];							
						$memclas->addmemcostlog( $orderinfo['buyeruid'],$memberinfo['username'],$memberinfo['cost'],1,$rdata['cost'],$shengyucost,"管理员退款给用户",ICookie::get('adminuid'),ICookie::get('adminname') );				 
						$memclas->addlog($orderinfo['buyeruid'],2,1,$rdata['cost'],'退款处理','用户取消跑腿订单',$shengyucost);  
					} 
					$orderClass->writewuliustatus($data['orderid'],14,$orderinfo['paytype']);   
					$this->mysql->update(Mysite::$app->config['tablepre'].'order',array('is_reback'=>2,'status'=>4),"id='".$data['orderid']."'");
					$rdata['status'] = 4;
					$rdata['type'] = 1;
					$this->mysql->insert(Mysite::$app->config['tablepre'].'drawbacklog',$rdata);	
				}else{
					$orderClass->writewuliustatus($data['orderid'],13,$orderinfo['paytype']);   
					$this->mysql->update(Mysite::$app->config['tablepre'].'order',array('is_reback'=>1,'status'=>4),"id='".$data['orderid']."'");
					$rdata['status'] = 2;
					$rdata['type'] = 1;
					$this->mysql->insert(Mysite::$app->config['tablepre'].'drawbacklog',$data);		
				}	
				$psbinterface = new psbinterface();
				if($psbinterface->psbdraworder($orderinfo['id'])){
				}
				$this->success('success');  
			}			
		}else{
			$drawbacklog = new drawbacklog($this->mysql);		 
			$check = $drawbacklog->setsavedraw($data)->save();
			if($check == true){
				$this->success('success');  
			}else{
				$msg = $drawbacklog->GetErr();
				$this->message($msg);
		    } 			
		}
    }
	//取消退款
	function quxiaotk(){
		$orderid = IFilter::act(IReq::get('orderid'));
        $orderinfo = $this->mysql->select_one("select id,allcost,admin_id,shopid,buyeruid,buyername,status,is_reback,shoptype from ".Mysite::$app->config['tablepre']."order where id ='".$orderid."'  ");
		if(empty($orderinfo))$this->message("订单不存在");
		if($orderinfo['shoptype'] == 100){
			$data['uid'] = $orderinfo['buyeruid'];
			$data['username'] = $orderinfo['buyername'];			 
			$data['orderid'] = $orderinfo['id'];
			$data['shopid'] = $orderinfo['shopid'];		
			$data['status'] = 1;//  退款状态 0待处理 1退款结束 2商家同意退款 3退款失败 4平台退款
			$data['addtime'] = time();
			$data['cost'] = $orderinfo['allcost'];
			$data['admin_id'] = $orderinfo['admin_id'];
			$data['type'] = 0;
			$this->mysql->insert(Mysite::$app->config['tablepre'].'drawbacklog',$data);   //写退款记录	
			$orderClass = new orderclass();
			$orderClass->writewuliustatus($orderinfo['id'],16,$orderinfo['paytype']);  
            $this->mysql->update(Mysite::$app->config['tablepre'].'order',array('is_reback'=>5),"id='".$orderinfo['id']."'");
			$psbinterface = new psbinterface();
			if($psbinterface->psbqxdraworder($orderinfo['id'])){
			}
            $this->success('取消退款申请成功');			
		}else{
			$drawback = new drawbacklog($this->mysql);	
			$ddata=array('allcost'=>$orderinfo['allcost'],'orderid'=>$orderinfo['id'],'typeid'=>'1','status'=>'1','uid'=>$orderinfo['buyeruid']);		 	 
			$msg = $drawback->setsavedraw($ddata)->save();
			if($msg){	  
			    $psbinterface = new psbinterface();
				if($psbinterface->psbqxdraworder($orderinfo['id'])){
				}
				$this->success('取消退款申请成功');
			}else{
				$this->message($msg);
			}
		}		
	}
 function makeorder(){
        $this->getOpenCity();
        $userid = intval(IFilter::act(IReq::get('userid'))); //用户ID
        $addressinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."address  where userid = ".$userid." and `default`=1   ");
        if(empty($addressinfo)) $this->message('未设置默认地址');
		$is_ziti = IFilter::act(IReq::get('is_ziti')); 
		$is_hand = IFilter::act(IReq::get('is_hand'));
		$zititime = IFilter::act(IReq::get('zititime')); 
		$ziti_phone = IFilter::act(IReq::get('ziti_phone'));
		$username = IFilter::act(IReq::get('username'));
		$info['is_hand'] = $is_hand;
		$info['is_ziti'] = $is_ziti;
		$info['username'] = ($is_ziti == 1)?$username:$addressinfo['contactname']; 
		$info['mobile'] = ($is_ziti == 1)?$ziti_phone:$addressinfo['phone'];
        $info['addressdet'] = $addressinfo['address'];
        $info['buyerlng'] = $addressinfo['lng'];
        $info['buyerlat'] = $addressinfo['lat'];
        $info['shopid'] = intval(IReq::get('shopid'));//店铺ID
        $info['remark'] = IFilter::act(IReq::get('remark'));//备注
        $info['paytype'] = IFilter::act(IReq::get('paytype'));//支付方式
        $info['dikou'] = intval(IReq::get('dikou'));//抵扣金额
        $info['minit'] = IFilter::act(IReq::get('minit'));//配送时间（秒）
        $info['juanid'] = intval(IReq::get('juanid'));//优惠劵ID
        $info['userid'] = intval(IReq::get('userid'));
        $info['ordertype'] = 3;//订单类型
        $info['othercontent'] = '';//empty($peopleNum)?'':serialize(array('人数'=>$peopleNum));
        if(empty($info['shopid'])) $this->message('店铺ID错误');
        $shoptype = intval(IReq::get('shoptype'));
        if($shoptype == 1){
            $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$info['shopid']."'    ");
        }else{
            $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$info['shopid']."'    ");
        }
        if(empty($shopinfo))   $this->message('店铺获取失败');
        $temp = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."address  where userid = ".$info['userid']." and `default`=1   ");
        $checkps = 	 $this->pscost2($shopinfo,$temp['lng'],$temp['lat']);
        if($checkps['canps'] != 1 && $is_ziti != 1) $this->message('该店铺不在配送范围内');
        $info['cattype'] = 0;
        if(empty($info['username'])) 		  $this->message('联系人不能为空');
        if(!IValidate::suremobi($info['mobile']))   $this->message('请输入正确的手机号');
        if(empty($info['addressdet']) && $is_ziti != 1) $this->message('详细地址为空');

        if(Mysite::$app->config['allowedguestbuy'] != 1){
            if($info['userid']==0) $this->message('禁止游客下单');
        }
        $info['ipaddress'] = '';
        $ip_l=new iplocation();
        $ipaddress=$ip_l->getaddress($ip_l->getIP());
        if(isset($ipaddress["area1"])){
            $info['ipaddress']  = $ipaddress['ip'] ;
        }
        $info['areaids'] = '';
        if($shopinfo['is_open'] != 1) $this->message('店铺暂停营业');
        $tempdata = $this->getOpenPosttime($shopinfo['is_orderbefore'],$shopinfo['starttime'],$shopinfo['postdate'],$info['minit'],$shopinfo['befortime']);
        if($tempdata['is_opentime'] ==  2 && $is_ziti != 1) $this->message('选择的配送时间段，店铺未设置');
        if($tempdata['is_opentime'] == 3 && $is_ziti != 1) $this->message('选择的配送时间段已超时');
        $info['sendtime'] = $tempdata['is_posttime'];
        $info['postdate'] = ($is_ziti == 1)?$zititime:$tempdata['is_postdate'];
        $info['addpscost'] = ($is_ziti == 1)?0:$tempdata['cost'];
        $checksend = Mysite::$app->config['ordercheckphone'];
        if($checksend == 1){
            if(empty($info['userid'])){
                $checkphone = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."mobile where phone ='".$info['mobile']."' order by addtime desc limit 0,50");
                if(empty($checkphone)) $this->message('member_emailyan');
                if(empty($checkphone['is_send'])){
                    $mycode = IFilter::act(IReq::get('phonecode'));
                    if($mycode == $checkphone['code']){
                        $this->mysql->update(Mysite::$app->config['tablepre'].'mobile',array('is_send'=>1),"phone='".$info['mobile']."'");
                    }else{
                        $this->message('member_emailyan');
                    }
                }
            }
        }

        $info['shopinfo'] = $shopinfo;
        $info['allcost'] = IFilter::act(IReq::get('cartcost'));
        $info['bagcost'] = IFilter::act(IReq::get('bagcost'));
        $info['allcount'] = IFilter::act(IReq::get('allcount'));
        $info['shopps'] = ($is_ziti == 1)?0:$checkps['pscost']; 
        $dishs = stripslashes(trim(IReq::get('dishs'))); 
        $ggdishs = stripslashes(trim(IReq::get('ggdishs')));
 		 
        if(empty($dishs) && empty($ggdishs)) $this->message('对应店铺购物车商品为空');
        $goodslist = array();
        if(!empty($dishs)){
            $dishs = json_decode($dishs);
			#print_r($dishs);
            foreach($dishs as $vv){
                $vv = (array)$vv;
                if($vv['count'] > 0){
                    $newgoods = array();
                    $newgoods['shopid'] = $info['shopid'];
                    $newgoods['count'] = $vv['count'];
                    $newgoods['id'] = $vv['id'];
                    $newgoods['name'] = $vv['name'];
                    $newgoods['cost'] = $vv['price'];
                    $newgoods['have_det'] = 0;
					$newgoods['cxinfo']['is_cx'] = $vv['iscx'];
					$newgoods['cxinfo']['cxcost'] = $vv['oldcost']-$vv['price'];
                    $goodslist[] = $newgoods;
                }
            }
        }
        if(!empty($ggdishs)){
            $ggdishs = json_decode($ggdishs);
			#print_r($ggdishs);
            foreach($ggdishs as $vg){
                $vg = (array)$vg;
                if($vg['count'] > 0){
                    $ggnewgoods = array();
                    $ggnewgoods['shopid'] = $info['shopid'];
                    $ggnewgoods['count'] = $vg['count'];
                    $ggnewgoods['name'] = $vg['name'];
                    $ggnewgoods['have_det'] = 1;
                    $ggnewgoods['gg']['goodsid'] = $vg['gid'];
                    $ggnewgoods['gg']['attrname'] = $vg['attrname'];
                    $ggnewgoods['gg']['cost'] = $vg['price'];
                    $ggnewgoods['gg']['id'] = $vg['id'];
					$ggnewgoods['cxinfo']['is_cx'] = $vg['iscx'];
					$ggnewgoods['cxinfo']['cxcost'] = $vg['oldcost']-$vg['price'];
                    $goodslist[] = $ggnewgoods;
                }
            }
        } 
        $info['goodslist'] = $goodslist;
		#print_r($info['goodslist']);exit;
		$info['cx_manjian'] =  IReq::get('cx_manjian'); 
	    $info['cx_zhekou'] =  IReq::get('cx_zhekou'); 
	    $info['cx_shoudan'] =  IReq::get('cx_shoudan'); 
	    $info['cx_nopsf'] = ($is_ziti == 1)?0:IReq::get('cx_nopsf');
        $info['pstype'] = $checkps['pstype'];
        $info['cattype'] = 0;//表示不是预订
        $info['platform'] = 2;//微信
        $info['is_goshop'] = 0;
		#print_r($info);exit;
        if($shopinfo['limitcost'] > $info['allcost']) $this->message('商品总价低于最小起送价'.$shopinfo['limitcost']);
		//exit;
        $orderclass = new orderclass();
        $orderclass->makenormal($info);
        $orderid = $orderclass->getorder();
        $data['id'] = $orderid;
        $this->success($data);
    }

    //确认支付
    function gotopay(){
        $orderid = intval(IReq::get('orderid'));
        $paydotype = IFilter::act(IReq::get('paydotype'));
        $userid = intval(IReq::get('userid'));
        if(empty($orderid)) $this->message('订单ID为空');
        if(empty($userid)) $this->message('用户ID为空');
        if(empty($paydotype)) $this->message('支付类型为空');
        $orderinfo = $this->mysql->select_one("select * from `".Mysite::$app->config['tablepre']."order` where id=".$orderid."  ");//获取主单
        if(empty($orderinfo)) $this->message('订单不存在');
        $userinfo = $this->mysql->select_one("select * from `".Mysite::$app->config['tablepre']."member` where uid=".$userid."  ");//获取用户信息
        if(empty($userinfo)) $this->message('用户不存在');
        /* if(Mysite::$app->config['open_acout'] != 1){
            $this->message('网站未开启在线支付，不能支付');
        } */
        if($userid > 0){
            if($orderinfo['buyeruid'] !=  $userid){
                $this->message('订单不属于您');
            }
        }
        if($orderinfo['paytype'] == 0){
            $this->message('此订单是货到支付订单不可操作');
        }
        if($orderinfo['status']  > 2){
            $this->message('此订单已发货或者其他状态不可操作');
        }
        $paylist = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."paylist where  loginname = '".$paydotype."' and (type = 0 or type=2) order by id asc limit 0,50");
        if(empty($paylist)){
            $this->message('不存在的支付类型');
        }
        if($orderinfo['paystatus'] == 1){
            $this->message('此订单已支付');
        }
        $paydir = hopedir.'/plug/pay/'.$paydotype;
        if(!file_exists($paydir.'/pay.php'))
        {
            $this->message('支付方式文件不存在');
        }

        //更新用户数据
        $this->mysql->update(Mysite::$app->config['tablepre'].'member','`cost`=`cost`-'.$orderinfo['allcost'],"uid ='".$userid."' ");
        //更新订单数据
        $orderdata['paystatus'] = 1;
        if($orderinfo['status'] == 0){
            $orderdata['status'] = 1;
        }
        $orderdata['paytype_name'] = $paydotype;
        $this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdata,"id ='".$orderid."' ");
        $accost = $userinfo['cost']-$orderinfo['allcost'];
        $this->memberCls->addlog($userinfo['uid'],2,2,$orderinfo['allcost'],'余额支付订单','支付订单'.$orderinfo['dno'].'帐号金额减少'.$orderinfo['allcost'].'元', $accost);
        $this->memberCls->addmemcostlog($orderinfo['buyeruid'],$userinfo['username'],$userinfo['cost'],2,$orderinfo['allcost'],$accost,"下单余额消费",$userinfo['uid'],$userinfo['username']);
        $checkflag = false;
        $orderCLs = new orderclass();
        $orderCLs->writewuliustatus($orderinfo['id'],3,$orderinfo['paytype']);  //在线支付成功状态
		$shopinfo = $this->mysql->select_one("select is_autopreceipt from ".Mysite::$app->config['tablepre']."shop where id=".$orderinfo['shopid']."  "); 
        if($shopinfo['is_autopreceipt']  == 1){
			$datakk['is_make'] = 1;
			$datakk['maketime'] = time();
			$this->mysql->update(Mysite::$app->config['tablepre'].'order',$datakk,"id ='".$orderid."' ");
            $orderCLs->writewuliustatus($orderinfo['id'],4,$orderinfo['paytype']);  //商家自动确认接单
            $auto_send = Mysite::$app->config['auto_send'];
            if($auto_send == 1){
                $orderCLs->writewuliustatus($orderinfo['id'],6,$orderinfo['paytype']);//订单审核后自动 商家接单后自动发货
                $orderdatac['sendtime'] = time();
                $this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdatac,"id ='".$orderid."' ");
            }else{
                //自动生成配送单
                if($orderinfo['shoptype'] != 100){
                    if($orderinfo['pstype'] == 0 ){//网站配送自动生成配送费
                        $orderpsinfo  =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."orderps where  orderid ='".$orderid."'   ");
                        if(empty($orderpsinfo)){
                            $psdata['orderid'] = $orderinfo['id'];
                            $psdata['shopid'] = $orderinfo['shopid'];
                            $psdata['status'] = 0;
                            $psdata['dno'] = $orderinfo['dno'];
                            $psdata['addtime'] = time();
                            $psdata['pstime'] = $orderinfo['posttime'];
                            $admin_id = $orderinfo['admin_id'];
                            $psset = $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."platpsset  where cityid= '".$admin_id."'   ");
                            $checkpsyset = $psset['psycostset'];
                            $bilifei =$psset['psybili']*0.01*$orderinfo['allcost'];
                            $psdata['psycost'] = $checkpsyset == 1?$psset['psycost']:$bilifei;
                            $this->mysql->insert(Mysite::$app->config['tablepre'].'orderps',$psdata);  //写配送订单
                        }
                        $checkflag = true;
                    }elseif($orderinfo['pstype'] == 2 && $orderinfo['is_ziti'] == 0){
                        $psbinterface = new psbinterface();
                        if($psbinterface->psbnoticeorder($orderid)){
                            $checkflag = false;
                        }
                    }
                }else{
                    //生成跑腿订单的办法调用
                    $psbinterface = new psbinterface();
                    if($psbinterface->paotuitopsb($orderid)){
                        $checkflag = false;
                    }
                }
                //自动生成配送单结束-------------
            }
        }else{
            if($orderinfo['shoptype'] == 100){
                $psbinterface = new psbinterface();
                if($psbinterface->paotuitopsb($orderid)){
                    $checkflag = false;
                }
            }
        }
        $orderCLs->sendmess($orderid);
        if($checkflag ==true){
            $psylist =  $this->mysql->getarr("select a.*  from ".Mysite::$app->config['tablepre']."apploginps as a left join ".Mysite::$app->config['tablepre']."member as b on a.uid = b.uid where b.admin_id = ".$orderinfo['admin_id']."");
            $psCls = new apppsyclass();
            $psCls->SetUserlist($psylist)->sendNewmsg('订单提醒','有新订单可以处理');
        }

        $data['id'] = $orderid;
        $this->success($data);
    }

    //获取支付数据
    function paydata(){
        $dotype = IReq::get('dotype');
        $userid = intval(IReq::get('userid'));
        $orderid = intval(IReq::get('orderid'));
        $wxopenid = IReq::get('openid');
        if(empty($dotype))$this->message('支付类型错误');
        if(empty($wxopenid))$this->message('openid为空');

        if($dotype == 'order'){
            if(empty($orderid))$this->message('订单ID为空');
            $orderinfo = $this->mysql->select_one("select * from `".Mysite::$app->config['tablepre']."order` where id=".$orderid."  ");//获取订单信息
            if(empty($orderinfo))$this->message('订单不存在');
            $SetBody = '支付订单'.$orderinfo['dno'];
            $Attach = $orderinfo['dno'];
            $trade_no = $orderinfo['id'];
            $cost = $orderinfo['allcost'];
            $Goods_tag = '订餐';
        }elseif($dotype == 'account'){
            $cost = IReq::get('cost');
            if(empty($userid))$this->message('用户ID为空');
            if(empty($cost))$this->message('充值金额为空');
            $userinfo = $this->mysql->select_one("select * from `".Mysite::$app->config['tablepre']."member` where uid=".$userid."  ");//获取用户信息
            if(empty($userinfo)) $this->message('用户不存在');
            $SetBody = '余额充值acount_'.$userid;
            $Attach = 'acount_'.$userid;
            $trade_no = 'acount_'.time();
            $cost = round($cost,2);
            $Goods_tag = '在线充值';
        }

        $weixindir = hopedir.'/plug/pay/weixinapplet/';
        $weixindata = array(
            'appid'=>'',
            'nonce_str'=>'',
            'package'=>'',
            'paySign'=>'',
            'timeStamp'=>''
        );
        $weixincheck = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."paylist where  loginname ='weixin'   order by id asc limit 0,1");
        if(!empty($weixincheck)){
            require_once $weixindir."lib/WxPay.Api.php";
            //统一下单
            $input = new WxPayUnifiedOrder();
            $input->SetBody($SetBody);
            $input->SetAttach($Attach);
            $input->SetOut_trade_no($trade_no);
            $input->SetTotal_fee($cost*100);
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetTimeStamp(time());
            $input->SetGoods_tag($Goods_tag);
            $input->SetNotify_url(Mysite::$app->config['siteurl']."/plug/pay/weixinapplet/notify.php");
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($wxopenid);
//            print_r($input);
            $ordermm = WxPayApi::unifiedOrder($input);
//            print_r($ordermm);
            if($ordermm){
                $nowTime = (string)time();
                $weixindata['appid'] = $ordermm['appid'];
                $weixindata['nonce_str'] = $ordermm['nonce_str'];
                $weixindata['package'] = 'prepay_id='.$ordermm['prepay_id'];
                $key = WxPayConfig::KEY;
                $string = 'appId='.$ordermm['appid'].'&nonceStr='.$ordermm['nonce_str'].'&package='.$weixindata['package'].'&signType=MD5&timeStamp='.$nowTime.'&key='.$key;
                $string = md5($string);
                //签名步骤四：所有字符转为大写
                $result = strtoupper($string);
                $weixindata['paySign'] = $result;
                $weixindata['timeStamp'] = $nowTime;
            }
        }
        $this->success($weixindata);
    }

    //收藏操作
    function myFavorite(){
        $userid = intval(IReq::get('userid'));
        if(empty($userid)){
            $this->message("用户ID为空");
        }
        $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid ='".$userid."' ");
        if(empty($userinfo)){
            $this->message("用户不存在");
        }
        $collectid = intval(IReq::get('collectid'));
        $type = intval(IReq::get('type'));
        if(empty($collectid))$this->message('collect_err');
        $data['collecttype'] = empty($type)? 0 : 1;
        $data['collectid'] = $collectid;
        $data['uid'] = $userid;
        //收藏商品
        if($data['collecttype'] == 1)
        {
            $goodsinfo = $this->mysql->select_one("select uid from ".Mysite::$app->config['tablepre']."goods where id=".$collectid."  ");
            if(empty($goodsinfo)) $this->message('collect_err');
        }else{
            //收藏店铺
            $goodsinfo = $this->mysql->select_one("select uid from ".Mysite::$app->config['tablepre']."shop where id=".$collectid."  ");
            if(empty($goodsinfo)) $this->message('collect_err');
        }

        $checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."collect where uid=".$data['uid']." and collectid=".$data['collectid']."  and collecttype = '".$data['collecttype']."' ");
        if(!empty($checkinfo))
        {
            $this->mysql->delete(Mysite::$app->config['tablepre'].'collect',"uid='".$data['uid']."' and collectid = '".$data['collectid']."' and collecttype ='".$data['collecttype']."'");
            $backData['collect'] = 2;
        }else{
            $data['shopuid'] = $goodsinfo['uid'];
            $this->mysql->insert(Mysite::$app->config['tablepre'].'collect',$data);
            $backData['collect'] = 1;
        }
        $this->success($backData);
    }

    /* 网站通知列表 */
    function noticelist(){
        $this->getOpenCity();
        $pageinfo = new page();
        $pageinfo->setpage(intval(IReq::get('page')),10);

        $list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."information where type = 1  and ( cityid='".$this->CITY_ID."'  or  cityid = 0 ) order by orderid asc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
      
	    $backdata = array();
        foreach($list as $value){
            $value['addtime'] = date("Y-m-d",$value['addtime']);
            $value['img'] = empty($value['img']) ? getImgQuanDir(Mysite::$app->config['shoplogo']) : getImgQuanDir($value['img']);
            $value['content'] = strip_tags($value['content']);
            $backdata[] = $value;
        }
        $data['noticelist'] = $backdata;
        $this->success($data);
    }

    /* 网站通知详情 */
    function notice(){
        $id = intval( IReq::get('id') );
        if(empty($id))$this->message('通知ID为空');
        $notice = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."information  where type= 1 and  id = '".$id."' ");
        $notice['addtime'] = date("Y-m-d",$notice['addtime']);
        $data['notice'] = $notice;
        Mysite::$app->setdata($data);
    }
	/* 小程序审核首页显示内容 */
    function indexcontent(){
        $single = $this->mysql->select_one("select content from ".Mysite::$app->config['tablepre']."single  where code='applet' ");
        $data['content'] = $single['content'];
        Mysite::$app->setdata($data);
    }
    //获取优惠券列表
    function juanlist(){
        $userid = intval(IReq::get('userid'));

        //可用优惠券
        $juanlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan where status !=2 and  uid = ".$userid." ");
		//status 状态，0未使用，1已绑定，2已使用，3无效
		//spotordtype 支持频道 1外卖频道 2超市频道 3跑腿 空或1,2,3时全支持
		#print_r($juanlist);
        $backdata = array();
        if(!empty($juanlist)){
            foreach($juanlist as $k=>$value){
                $tiaojian = '';			
				if(!empty($value['spotordtype']) && $value['spotordtype'] !='1,2,3' ){
					$tiaojian = '仅限';
					if($value['spotordtype'] == '1') $tiaojian .='外卖频道';
					if($value['spotordtype'] == '2') $tiaojian .='超市频道';
					if($value['spotordtype'] == '3') $tiaojian .='跑腿频道';
					if($value['spotordtype'] == '1,2') $tiaojian .='外卖频道、超市频道';
					if($value['spotordtype'] == '1,3') $tiaojian .='外卖频道、跑腿频道';
					if($value['spotordtype'] == '2,3') $tiaojian .='超市频道、跑腿频道';					
					$tiaojian .='使用';
				}else{
					$tiaojian .='';
				}
				if($value['paytype'] =='1'){
					$tiaojian .=empty($tiaojian)?'仅限在线支付使用。':'；仅限在线支付使用。';
				}elseif($value['paytype'] =='2'){
					$tiaojian .=empty($tiaojian)?'仅限货到支付使用。':'；仅限货到支付使用。';
				}else{
					$tiaojian .='。';
				}
				if((empty($value['spotordtype']) || $value['spotordtype'] =='1,2,3') && ($value['paytype'] == '' || $value['paytype'] == '1,2' )){
					$tiaojian = '';
				}
				#print_r(date('Y-m-d',$value['endtime']));
				$checktime = $value['endtime']-time();
				if($checktime < 86400 && $checktime > 0 ){
					$value['timeout'] = 1;
				}else{
					$value['timeout'] = 0;		
				}
				$value['tiaojian'] = $tiaojian;
				$value['creattime'] = date('Y-m-d',$value['creattime']);
				if($value['endtime'] < time()){
					$value['status'] = $value['status']==2?2:4;
				}
				$value['endtime'] = date('Y-m-d',$value['endtime']);
				$backdata[] = $value;
			}
        }
		$canArr = array();
		$nouseArr = array();
		if(!empty($backdata)){
			foreach($backdata as $k=>$v){
				if($v['status'] > 1){
					$nouseArr[] = $v;
				}
				if($v['status'] < 2){
					$canArr[] = $v;
				}
			}
		}
        $data['list'] = $canArr;
        $data['nouselist'] = $nouseArr;
        $this->success($data);
    }

    //领取优惠券
    function exchangjuan(){
        $userid = intval(IReq::get('userid'));
        if(empty($userid)) $this->message("用户ID为空");
        $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid ='".$userid."' ");
        if(empty($userinfo)) $this->message("用户不存在");

        $card = trim(IFilter::act(IReq::get('card')));
        $password = trim(IFilter::act(IReq::get('password')));
        if(empty($card)) $this->message('card_emptyjuancard');
        if(empty($password)) $this->message('card_emptyjuanpwd');
        $checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."juan where card ='".$card."'  and card_password = '".$password."' and endtime > ".time()." and status = 0");
        if(empty($checkinfo)) $this->message('card_emptyjuan');
        if($checkinfo['uid'] > 0) $this->message('card_juanisuse');

        $arr['uid'] = $userinfo['uid'];
        $arr['status'] =  1;
        $arr['username'] = $userinfo['username'];
        $this->mysql->update(Mysite::$app->config['tablepre'].'juan',$arr,"card='".$card."'  and card_password = '".$password."' and endtime > ".time()." and status = 0 and uid = 0");

        $this->success('success');
    }
	//分销相关函数
	function distribution_center(){	  
		$userid = intval(IReq::get('userid'));
		if(empty($userid))  $this->message('用户ID为空');
		$userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid ='".$userid."' ");
        if(empty($userinfo)) $this->message("用户不存在");
	    $allordercost = $this->mysql->select_one("select sum(ordercost) as allordercost, sum(yjbcost) as allyjcost from  ".Mysite::$app->config['tablepre']."fxincomelog where uid=".$userid." ");
	    $data['allordercost'] = number_format($allordercost['allordercost'],2);
	    $data['allyjcost'] = number_format($allordercost['allyjcost'],2);
		if(empty($userinfo['invitecode'])){		
            $invitecode	= $this->recursion();			 
           	$this->mysql->update(Mysite::$app->config['tablepre'].'member',array('invitecode'=>$invitecode),"uid='".$userid."'");
            $data['invitecode'] = $invitecode;			
		}else{
			$data['invitecode'] = $userinfo['invitecode'];
		}
		if(empty($userinfo['fxcode'])){
			$wx_s = new wx_s();
			$ifmake = $wx_s->makefxcode($userid);
			if($ifmake == true){
			    if($wx_s->get_fxcodeurl($userid)){
					$userinfo['fxcode'] = $wx_s->get_fxcodeurl($userid);
				}
			}			
		}
	   $data['userinfo'] = $userinfo;
	   $this->success($data);
    }
	//递归函数--随机生成邀请码并检测其唯一性后返回邀请码
	function recursion(){
		$roundnumber = rand(100000,999999);	 
		$cinfo = $this->mysql->select_one("select uid from  ".Mysite::$app->config['tablepre']."member where invitecode=".$roundnumber." ");
		if(empty($cinfo)){
			return	$roundnumber;		
		}else{
			$this->recursion();
		}
	}
	function distribution_yjtx(){	  
		$userid = intval(IReq::get('userid'));
		if(empty($userid))  $this->message('用户ID为空');
		$userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid ='".$userid."' ");
        if(empty($userinfo)) $this->message("用户不存在");
		$data['userinfo'] = $userinfo;
	    $data['fxfeelv'] = isset(Mysite::$app->config['fxfeelv'])?Mysite::$app->config['fxfeelv']:0;
		$data['minfxtxcost'] = isset(Mysite::$app->config['minfxtxcost'])?Mysite::$app->config['minfxtxcost']:0;
	   $this->success($data);
    }
	function dofxtx(){	         	
		$uid = intval(IReq::get('userid'));
		$txtype = IFilter::act(IReq::get('txtype'));
		$txcost = IFilter::act(IReq::get('txcost'));
		$zfbaccount = IFilter::act(IReq::get('zfbaccount'));
		$zfbusername = IFilter::act(IReq::get('zfbusername'));
		$cardusername = IFilter::act(IReq::get('cardusername'));
		$cardnumber = IFilter::act(IReq::get('cardnumber'));
		$bankname = IFilter::act(IReq::get('bankname'));		
		$txdata = array(
		    'uid'=>$uid,
			'txtype'=>$txtype,
			'txcost'=>$txcost,
			'zfbaccount'=>$zfbaccount,
			'zfbusername'=>$zfbusername,
			'cardusername'=>$cardusername,
			'cardnumber'=>$cardnumber,
			'bankname'=>$bankname,
		);
		#print_r($txdata);exit;
		$distribution = new distribution();
		if($distribution->tixian($txdata)){
			$this->success("success");
		}else{
			$this->message($distribution->Error());
		}			
	}
	//我的佣金
	function distribution_myyj(){
		$uid = intval(IReq::get('userid'));
		$stime = strtotime(date('Y-m-d', time()));
		$meminfo = $this->mysql->select_one("select fxcost  from  ".Mysite::$app->config['tablepre']."member where uid=".$uid."  ");
		$todaycost = $this->mysql->select_one("select  sum(yjbcost) as yjbcost from  ".Mysite::$app->config['tablepre']."fxincomelog where uid=".$uid." and addtime > ".$stime." and addtime < ".time()." ");
		$data['todaycost'] = number_format($todaycost['yjbcost'],2);//今日收益佣金
		$allcost = $this->mysql->select_one("select  sum(yjbcost) as yjbcost from  ".Mysite::$app->config['tablepre']."fxincomelog where uid=".$uid."  ");
		$data['allcost'] = number_format($allcost['yjbcost'],2);//累计收益佣金
		$data['cantxcost'] = number_format($meminfo['fxcost'],2);//可提现金额
		$txcost = $this->mysql->select_one("select  sum(reallycost) as reallycost from  ".Mysite::$app->config['tablepre']."distributiontxlog where uid=".$uid." and status = 1 ");
		$data['txcost'] = number_format($txcost['reallycost'],2);//已提现佣金（扣除手续费后的实际提现到账金额）
		$this->success($data);
	}
	function yjloglist(){
		$uid = intval(IReq::get('userid'));
		$searchvalue = trim(IReq::get('searchvalue'));
		$page = intval(IReq::get('page'));
		$distribution = new distribution();
		$loglist = $distribution->getyjloglist($uid,$page,$searchvalue);
		#print_r($loglist);
		$data['loglist'] = $loglist;
		$this->success($data);					
	}
	function txloglist(){
		$uid = intval(IReq::get('userid'));
		$page = intval(IReq::get('page'));		 
		$distribution = new distribution();
		$loglist = $distribution->gettxloglist($uid,$page);
		$data['loglist'] = $loglist;
		$this->success($data);
	}
	//提现详情
	function fxtxdet(){
		$id = intval(IReq::get('id'));	
		$distribution = new distribution();
		$data['logdet'] = $distribution->gettxlogdet($id);
		$this->success($data);			
	}
	//我的下线列表  直接实例化distribution类  调用getmemberlist函数  按顺序传三个参数  1.当前登录用户uid  2.页码  3.下线等级	
	function myjuniorlist(){
		$uid = intval(IReq::get('userid'));
		$page = intval(IReq::get('page'));
		$grade = intval(IReq::get('grade'));		 
		$distribution = new distribution();
		$memberlist = $distribution->getmemberlist($uid,$page,$grade);
		$data['memberlist'] = $memberlist;
		$data['distribution_grade'] = Mysite::$app->config['distribution_grade'];
       $this->success($data);				
	}
	//佣金排名
	function yjranking(){		 
		$uid = intval(IReq::get('userid'));
		$distribution = new distribution();
		$data = $distribution->yjranking($uid);//array('list'=>排名列表,selfranking=>自身排名);
		if(!empty($data['list'])){
			foreach($data['list'] as $k=>$val){
				$val['logo'] = getImgQuanDir($val['logo']);
				$data['newlist'][]=$val;
			}
		}
		$data['meminfo']=$this->mysql->select_one("select fxcost,logo  from  ".Mysite::$app->config['tablepre']."member where uid=".$uid."  ");
		$data['meminfo']['logo'] = empty($data['meminfo']['logo'])?getImgQuanDir(Mysite::$app->config['userlogo']):getImgQuanDir($data['meminfo']['logo']);
		$this->success($data);	
	}
	function fxcontent(){		 
		$uid = intval(IReq::get('userid'));
		$singleinfo=$this->mysql->select_one("select *  from  ".Mysite::$app->config['tablepre']."single where code='fxsm' and title='分销说明'  ");
		$contentstr= empty($singleinfo['content'])?'':$singleinfo['content'];
		$data['content'] = $contentstr;
		 Mysite::$app->setdata($data);
	}
	//分销二维码
	function distribution_fxcode(){
	    $uid = intval(IReq::get('userid'));
	    $member = $this->mysql->select_one("select uid,logo,username,fxcode,invitecode from  ".Mysite::$app->config['tablepre']."member where uid=".$uid." ");
		if(empty($member['fxcode'])){
			$wx_s = new wx_s();
			$ifmake = $wx_s->makefxcode($userid);
			if($ifmake == true){
			    if($wx_s->get_fxcodeurl($userid)){
					$member['fxcode'] = $wx_s->get_fxcodeurl($userid);
				}
			}			
		}
		$data['meminfo'] = $member;		 
		$data['show'] = !empty($member['fxcode'])?1:0;//二维码生成后并存储在/upload/wxcode/目录下有一定延迟 
		$singleinfo=$this->mysql->select_one("select *  from  ".Mysite::$app->config['tablepre']."single where code='fxsm' and title='分销说明'  ");
		$contentstr= empty($singleinfo['content'])?'':$singleinfo['content'];
		$data['fxcontent'] = $contentstr;		
		$this->success($data);
    }
	//积分中心
	function gift(){
		$uid = IFilter::act(IReq::get('userid'));
		$member = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid = '".$uid."'");
		$giftlog = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."memberlog where type = 1 and userid = '".$uid."' order by addtime desc ");  //积分增减记录
		$singleinfo=$this->mysql->select_one("select *  from  ".Mysite::$app->config['tablepre']."single where code='jfgz' ");
		
		$data['jfgz'] = $singleinfo['content'];
		$data['giftlog'] = array();
		if(!empty($giftlog)){
			foreach($giftlog as $k=>$val){
				if($val['result']>0){
					$val['addtime'] = date('Y-m-d H:i',$val['addtime']);
					$data['giftlog'][] = $val;
				}
			}
		}
		$data['meminfo'] = $member;	
		$this->success($data);
	}
	//兑换产品列表
	function giftlist(){
		$adcode = IFilter::act(IReq::get('adcode'));
		$lunboinfo = $this->mysql->select_one("select img from ".Mysite::$app->config['tablepre']."adv where `advtype` = 'wxgift' and cityid = ".$adcode." " );
		$data['lunboimg'] = getImgQuanDir($lunboinfo['img']);
		$data['giftlist'] = array();
		$giftlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."gift where id > 0 order by id asc limit 0,100 " );
		if(!empty($giftlist)){
			foreach($giftlist as $k=>$val){
				$val['img'] = empty($val['img'])?getImgQuanDir(Mysite::$app->config['shoplogo']):getImgQuanDir($val['img']);
				$data['giftlist'][] = $val;
			}
		}
		$this->success($data);
	}
	//积分兑换详情
	function giftdetail(){
		$id = intval(IReq::get('giftid'));
		$data['giftinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."gift where id = ".$id);
		$data['giftinfo']['img'] = empty($data['giftinfo']['img'])?getImgQuanDir(Mysite::$app->config['shoplogo']):getImgQuanDir($data['giftinfo']['img']);
		$this->success($data);
	}
	 function exchang(){
		$userid = IFilter::act(IReq::get('userid'));
		$member = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid = '".$userid."'");
	   	if(empty($userid))$this->message("member_nologin");
	   	$giftid = intval(IReq::get('giftid'));
	   	if(empty($giftid)) $this->message("gift_empty");
	   	$lipininfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."gift where id ='".$giftid."'  order by id asc  ");
	    
		if(empty($lipininfo)) $this->message("gift_empty");
	   	if($lipininfo['stock'] < 1)$this->message("gift_emptystock");
	   	$moren_addr = intval(IReq::get('address_id'));

	   	if(empty($moren_addr)){
	   		$data['address'] = IFilter::act(IReq::get('address'));
	   		$data['contactman'] = IFilter::act(IReq::get('aboutname'));
	   		$data['telphone'] = IFilter::act(IReq::get('aboutphone'));
	   		$data['content'] = IFilter::act(IReq::get('content'));
	   		if(empty($data['contactman']))$this->message("emptycontact");
	   		if(empty($data['telphone']))$this->message("errphone");
	   		if(empty($data['address']))$this->message("emptyaddress");
	   	}else{
	   	   $addressinfo = 	$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."address where  id = '".$moren_addr."' order by id desc  ");
	   	    if(empty($moren_addr)) $this->message("member_noexitaddress");
	   	    if($addressinfo['userid'] != $userid) $this->message("member_noexitaddress");
	   	 	$data['address'] = $addressinfo['address'];
	   		$data['contactman'] =$addressinfo['contactname'];
	   		$data['telphone'] = $addressinfo['phone'];
			$data['content'] = IFilter::act(IReq::get('content'));
	   	}

		if(!IValidate::suremobi($data['telphone'])){
           $this->message('手机号格式错误');
       }
	   	if($member['score'] < $lipininfo['score']) $this->message('member_scoredown');
	   	$ndata['score'] = $member['score'] - $lipininfo['score'];
	   	//更新用户积分
	    $this->mysql->update(Mysite::$app->config['tablepre'].'member',$ndata,"uid='".$userid."'");
	   	$data['giftid'] = $lipininfo['id'];
	   	$data['uid'] = $userid;
	   	$data['addtime'] = time();
	   	$data['status'] = 0;
	   	$data['count'] = 1;
		$data['giftname'] = $lipininfo['title'];
	   	$data['score'] = $lipininfo['score'];
		#print_r($data);exit;
        $this->mysql->insert(Mysite::$app->config['tablepre'].'giftlog',$data);
        $this->memberCls->addlog($userid,1,2,$lipininfo['score'],'兑换礼品','兑换'.$lipininfo['title'].'减少'.$lipininfo['score'].'积分',$ndata['score']);
       //更新礼品表
        $lidata['stock'] =  $lipininfo['stock']-1;
        $lidata['sell_count'] =  $lipininfo['sell_count']+1;
      
        $this->mysql->update(Mysite::$app->config['tablepre'].'gift',$lidata,"id='".$giftid."'");
	    $this->success('success');
  }
  function giftlog(){
		$userid = IFilter::act(IReq::get('userid'));
		$giftloglist  = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."giftlog where uid ='".$userid."' order by addtime desc ");
		#print_r($giftloglist);
		$data['giftlog'] = array();
		$logstat = array('0'=>'待处理','1'=>'已处理，配送中','2'=>'兑换完成','3'=>'兑换成功','4'=>'已取消兑换');
		if(!empty($giftloglist)){
			foreach($giftloglist as $k=>$val){
				$val['addtime'] = date('m-d H:i',$val['addtime']);
				$val['status'] = $logstat[$val['status']];
				$data['giftlog'][] = $val;
			}
		}
		$this->success($data);

  }
  //收藏商家
  	function collectshopdata(){
		$userid = IFilter::act(IReq::get('userid'));
		$page = intval(IReq::get('page'));
		$where = '';  
		$lat =  trim(IFilter::act(IReq::get('lat')));
        $lng =  trim(IFilter::act(IReq::get('lng')));
		$lng = empty($lng)?0:$lng;
		$lat =empty($lat)?0:$lat;
		$orderarray = array(
		'0' =>" (2 * 6378.137* ASIN(SQRT(POW(SIN(3.1415926535898*(".$lat."-lat)/360),2)+COS(3.1415926535898*".$lat."/180)* COS(lat * 3.1415926535898/180)*POW(SIN(3.1415926535898*(".$lng."-lng)/360),2))))*1000  ASC ",                      
		); 
		/*获取店铺*/
		$pageinfo = new page();
		$pageinfo->setpage($page,10); 
		
		$list =   $this->mysql->getarr("select a.* from ".Mysite::$app->config['tablepre']."shop as a left join ".Mysite::$app->config['tablepre']."collect as b  on b.collectid  = a.id  where a.is_pass = 1 and a.is_open=1 and a.endtime > ".time()." and b.collecttype = 0 and b.uid = ".$userid." order by ".$orderarray[0]." limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");

		$nowhour = date('H:i:s',time()); 
		$nowhour = strtotime($nowhour);
		$templist = array();
		$cxclass = new sellrule();  
		if(is_array($list)){
			foreach($list as $keys=>$values){  
				$templist111 = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where cattype = ".$values['shoptype']." and  parent_id = 0    order by orderid asc limit 0,1000"); 
				$attra = array();
				$attra['input'] = 0;
				$attra['img'] = 0;
				$attra['checkbox'] = 0; 
				foreach($templist111 as $key=>$vall){
					if($vall['type'] == 'input'){
						$attra['input'] =  $attra['input'] > 0?$attra['input']:$vall['id'];
					}elseif($vall['type'] == 'img'){
						$attra['img'] =  $attra['img'] > 0?$attra['img']:$vall['id'];
					}elseif($vall['type'] == 'checkbox'){
						$attra['checkbox'] =  $attra['checkbox'] > 0?$attra['checkbox']:$vall['id'];
					}
				} 
				if($values['shoptype'] == 1 ){
					$shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where  shopid = ".$values['id']."   ");
				}else{
					$shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where  shopid = ".$values['id']."   ");
				}
				if(!empty($shopdet)){
				$values = array_merge($values,$shopdet);
				$values['shoplogo'] = empty($values['shoplogo'])? getImgQuanDir(Mysite::$app->config['shoplogo']):getImgQuanDir($values['shoplogo']);
				$checkinfo = $this->shopIsopen($values['is_open'],$values['starttime'],$values['is_orderbefore'],$nowhour); 
				$values['opentype'] = $checkinfo['opentype'];
				$values['newstartime']  =  $checkinfo['newstartime'];  

				$attrdet = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopattr where  cattype = ".$values['shoptype']." and shopid = ".$values['id']."");
				$cxclass->setdata($values['id'],1000,$values['shoptype']); 
			
				$mi = $this->GetDistance($lat,$lng, $values['lat'],$values['lng'], 1); 
				$tempmi = $mi;
				$mi = $mi > 1000? round($mi/1000,2).'km':$mi.'m';
				$values['juli'] = $mi;
				$checkps = 	 $this->pscost($values); 
				$values['pscost'] = $checkps['pscost'];

				$shopcounts = $this->mysql->select_one( "select sellcount as shuliang  from ".Mysite::$app->config['tablepre']."shop	 where    id = ".$values['id']."" );

				$values['ordercount']  = $values['ordercount']+$values['virtualsellcounts'];

				$cxinfo = array();
				$d = date("w") ==0?7:date("w");
				$time = time();
				
				$cxinfo = $this->mysqlcache->getarr("select id,name,imgurl,controltype,parentid from ".Mysite::$app->config['tablepre']."rule where  FIND_IN_SET(".$values['id'].",shopid)  and status = 1 and ( limittype =1 or ( limittype = 2 and  FIND_IN_SET(".$d.",limittime) ) or ( limittype = 3 and endtime > ".$time." and starttime < ".$time.")) order by id desc ");
				
				//筛选掉不符合配送条件的免配送费活动
				$newrule = array();
				foreach($cxinfo as $k=>$v){
					$v['imgurl'] = getImgQuanDir($v['imgurl']);
					 if($v['controltype'] == 4){
						 if($v['parentid'] == 0 && $values['sendtype'] == 1){
							 $newrule[] = $v;
						 }
						 if($v['parentid'] == 1 && $values['sendtype'] != 1){
							 $newrule[] = $v;
						 }
					 }else{
						 $newrule[] = $v;
					 }
				 }
				$values['cxlist'] =  $newrule;
				$values['cxcount'] =  count($newrule);
				$values['checkcx'] = 0;
				/* 店铺星级计算 */
				$zongpoint = $values['point'];
				$zongpointcount = $values['pointcount'];
				if($zongpointcount != 0 ){
					$shopstart = intval( round($zongpoint/$zongpointcount) );
				}else{
					$shopstart= 0;
				}
				$values['point'] = 	$shopstart;	
				$values['attrdet'] = array();
				foreach($attrdet as $k=>$v){
					if($v['firstattr'] == $attra['input']){
						$values['attrdet']['input'] = $v['value'];
					}elseif($v['firstattr'] == $attra['img']){
						$values['attrdet']['img'][] = $v['value'];
					}elseif($v['firstattr'] == $attra['checkbox']){
						$values['attrdet']['checkbox'][] = $v['value'];
					} 
				}
				$templist[] = $values;
				}
			}
		}
		$data['shoplist']  = $templist;
		$data['psimg']  = getImgQuanDir(Mysite::$app->config['psimg']);
        $data['shoppsimg']  = getImgQuanDir(Mysite::$app->config['shoppsimg']);
 #print_r($data);
	    $this->success($data);
	 }
	 function GetDistance3(){
		
		$lat1 = IFilter::act(IReq::get('lat1'));
		$lng1 = IFilter::act(IReq::get('lng1'));
		$lat2 = IFilter::act(IReq::get('lat2'));
		$lng2 = IFilter::act(IReq::get('lng2'));
		$kg = IReq::get('kg');
		$kgcost = IReq::get('kgcost');
		$addkg = IReq::get('addkg');
		$addkgcost = IReq::get('addkgcost');
		$km = IReq::get('km');
		$kmcost = IReq::get('kmcost');
		$addkm = IReq::get('addkm');
		$addkmcost = IReq::get('addkmcost');
		$allkg = IReq::get('allkg');
		$addcost = IReq::get('addcost');
		$lat1 = empty($lat1)?0:$lat1;
		$lng1 = empty($lng1)?0:$lng1;
		$lat2 = empty($lat2)?0:$lat2;
		$lng2 = empty($lng2)?0:$lng2;
		$data['juli'] = 0;
		$data['allcost'] = 0;
		$data['addcost'] = 0;
		$data['allkgcost'] = 0;
		$data['allkmcost'] = 0;
		$map_webservice_key =  Mysite::$app->config['map_webservice_key'];
		$origin = $lng1.",".$lat1;//始发地
		$destination = $lng2.",".$lat2;//目的地
		$content =   file_get_contents('https://restapi.amap.com/v4/direction/bicycling?key='.$map_webservice_key.'&origin='.$origin.'&destination='.$destination.''); 
		$backinfo  = json_decode($content,true);
		if( $backinfo['errcode'] == 0 && $backinfo['errmsg'] == 'OK' && count($backinfo['data']['paths']) > 0  ){
			$s = $backinfo['data']['paths'][0]['distance'];
		}else{  
			$earth = 6378.137;
			$pi = 3.1415926;
			$radLat1 = $lat1 * PI ()/ 180.0;   //PI()圆周率
			$radLat2 = $lat2 * PI() / 180.0;
			$a = $radLat1 - $radLat2;
			$b = ($lng1 * PI() / 180.0) - ($lng2 * PI() / 180.0);
			$s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
			$s = $s * EARTH_RADIUS;
			$s = round($s * 1000);
		}
		$s = number_format(($s/1000),2);
		#print_r(1111);
            if($allkg==0){
                $allkgcost = 0;
            }else{
                if($allkg<=$kg){
                   $allkgcost = $kgcost;
                }else{
                  $addweight = $allkg -$kg;
				  #print_r($addweight.'ccc');
                  $addweightkg = ceil($addweight/$addkg);
				   #print_r($addweightkg.'ccc');
                  $addweightkgcost = $addweightkg*$addkgcost;
				  #print_r($addweightkgcost.'ccc');
                  $allkgcost =number_format(($kgcost + $addweightkgcost),2);
				  #print_r($allkgcost);
                }
            }
            if($s<=$km){
               $allkmcost = $kmcost;
            }else{
              $addjuli = $s-$km; 
              $addnum = ceil($addjuli/$addkm);
              $addjulicost = $addnum*$addkmcost;
              $allkmcost = number_format(($kmcost + $addjulicost),2);
            }
            $allcost = $allkgcost+$allkmcost+$addcost;
            $allcost = number_format($allcost,2);
            $data['juli'] = $s;
            $data['allcost'] = $allcost;
            $data['addcost'] = $addcost;
            $data['allkgcost'] = $allkgcost;
            $data['allkmcost'] = $allkmcost;
          #print_r($data);
		$this->success($data);
	}
	function userptorder(){
	 $userid = IFilter::act(IReq::get('userid'));
	 $page = intval(IReq::get('page'));
	 $pageinfo = new page();
	 $pageinfo->setpage($page,5);  
	  //
	  $datalist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$userid."' and shoptype = 100 and is_userhide !=1 order by id desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
	   
	  $backdata = array();
	  foreach($datalist as $key=>$value){
        //自动关闭订单
          if($value['paytype'] == 1 && $value['paystatus'] == 0 && $value['status'] < 3){
              $checktime = time() - $value['addtime'];
              if($checktime > 900){
                  //说明该订单可以关闭
				 //退返本单的优惠券及积分
				 $yhjids = $value['yhjids'];
				 if(!empty($yhjids)){
						$yhjarr = explode(',',$yhjids);
						foreach($yhjarr as $k=>$v){
							$yhjdata['status'] = 0;
							$this->mysql->update(Mysite::$app->config['tablepre'].'juan',$yhjdata,"id ='".$v."' ");
						}
					} 			      		
                  $cdata['status'] = 4;
                  $this->mysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$value['id']."'");
                  $this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = {$value['id']} and status != 3");
                  /*更新订单 状态说明*/
                  $statusdata['orderid']     =  $value['id'];
                  $statusdata['addtime']     =  $value['addtime']+900;
                  $statusdata['statustitle'] =  "订单已取消";
                  $statusdata['ststusdesc']  =  "订单支付超时，系统已自动取消订单";
                  $this->mysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata);
				   
              }
          }
			$value['psstatus'] = empty($value['psstatus'])?0:$value['psstatus'];
				/*判断订单显示状态*/
				if($value['is_reback'] == 0 || $value['is_reback'] == 3 ){ //未申请退款或者拒绝退款 
					if($value['status'] > 3){
						$orderstatus = '已取消';
					}elseif($value['status'] == 3){
						$orderstatus = '已完成';
					}else{ 
						if($value['paytype'] == 1 && $value['paystatus'] == 0){//在线支付未付
							 $orderstatus = '待支付';
						}else{
							if($value['psstatus'] == 0){
								$orderstatus = '待接单';
							}elseif($value['psstatus'] == 1){
								$orderstatus = '待取货';								
							}elseif($value['psstatus'] == 2||$value['psstatus'] == 3){
								$orderstatus = '配送中';								
							}else{
								$orderstatus = '已完成';	
							}  
						}							 
					}					 
				}else{
					$orderstatus = $value['is_reback'] == 2? '已退款':'退款中';  
				}
				
				$value['orderwuliustatus'] = $orderstatus;
				$value['addtime'] = date('Y-m-d H:i',$value['addtime']);
				$backdata[] =$value;
		}
		$data['ptorderlist'] = $backdata;
		$data['callphone'] = Mysite::$app->config['litel'];		
		$this->success($data);
	}
	function paotuidetail(){//跑腿详情
        $userid = IFilter::act(IReq::get('userid'));
        $orderid = intval(IReq::get('orderid'));
        if (!empty($orderid)) {
            $order = $this->mysql->select_one("select * from " . Mysite::$app->config['tablepre'] . "order where buyeruid='".$userid."' and id = " . $orderid . " ");
            if (empty($order)) {
                $this->message('订单信息为空');
            } else {
				if($order['paytype'] == 1 && $order['paystatus'] == 0 && $order['status'] < 3){
					$checktime = time() - $order['addtime'];
					if($checktime > 900){
						//说明该订单可以关闭
						$cdata['status'] = 4;
						$this->mysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$orderid."'");
						$this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = '$orderid' and status != 3");
						/*更新订单 状态说明*/
						$statusdata['orderid']     =  $orderid;
						$statusdata['addtime']     =  $order['addtime']+900;
						$statusdata['statustitle'] =  "自动关闭订单";
						$statusdata['ststusdesc']  =  "在线支付订单，未支付自动关闭";
						$this->mysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata);
						$order['status'] = 4;
					}
				}
                $orderdet = $this->mysql->getarr("select * from " . Mysite::$app->config['tablepre'] . "orderdet where order_id='" . $order['id'] . "'");
                $order['cp'] = count($orderdet);
                $order['addtime'] = date('Y-m-d H:i:s',$order['addtime']);
				$order['psstatus'] = empty($order['psstatus'])?0:$order['psstatus'];
				$paystatusname= ($order['paystatus'] == 0)?'（未付）':'（已付）';
                if($order['paytype'] == 0){
                    $order['paystatusname'] = '货到支付'.$paystatusname;
                }elseif($order['paytype'] == 1){
                    if($order['paytype_name'] == ''){
                        $order['paystatusname'] = '在线支付'.$paystatusname;
                    }else{
                        if($order['paytype_name'] == 'open_acout'){
                            $order['paystatusname'] = '余额支付'.$paystatusname;
                        }elseif($order['paytype_name'] == 'weixin'){
                            $order['paystatusname'] = '微信支付'.$paystatusname;
                        }
                    }
                }
				$order['pstypename'] = '本订单由'.Mysite::$app->config['sitename'].'提供配送服务';
                $data['order'] = $order;              
                $data['orderdet'] = $orderdet;
                $psbpsyinfo = array();
                if ($order['psuid'] > 0 && $order['shoptype'] == 100) {
                    if ($order['status'] == 2) {
                        if ($order['pstype'] == 2) {
                            $psbinterface = new psbinterface();
                            $psbpsyinfo = $psbinterface->getpsbclerkinfo($order['psuid']);
                            if (!empty($psbpsyinfo) && !empty($psbpsyinfo['posilnglat'])) {
                                $posilnglatarr = explode(',', $psbpsyinfo['posilnglat']);
                                $posilng = $posilnglatarr[0];
                                $posilat = $posilnglatarr[1];
                                if (!empty($posilng) && !empty($posilat)) {
                                    $psbpsyinfo['posilnglatarr'] = $posilnglatarr;
                                } else {
                                    $psbpsyinfo = array();
                                }
                            }
                        } else if ($order['pstype'] == 0) {
                            $psbpsyinfo = $this->mysql->select_one("select uid,lng,lat from " . Mysite::$app->config['tablepre'] . "locationpsy where uid='" . $order['psuid'] . "' ");
                            if (!empty($psbpsyinfo) && !empty($psbpsyinfo['lng']) && !empty($psbpsyinfo['lat'])) {
                                $psbpsyinfo['posilnglat'] = $psbpsyinfo['lng'] . ',' . $psbpsyinfo['lat'];
                            } else {
                                $psbpsyinfo = array();
                            }
                        } else {
                            $psbpsyinfo = array();
                        }
                    } else if($order['status'] == 3 && ($order['pstype'] == 0 || $order['pstype'] == 2)) {
                        $psyoverlng = $order['psyoverlng'];
                        $psyoverlat = $order['psyoverlat'];
                        $psbpsyinfo['clerkid'] = $order['psuid'];
                        $psbpsyinfo['posilnglat'] = $psyoverlng . ',' . $psyoverlat;
                        $psbpsyinfo['posilnglatarr'] = explode(',', $psbpsyinfo['posilnglat']);
                    }
                }
				$orderwuliustatus = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderstatus where orderid = ".$order['id']." order by addtime desc limit 0,10 ");
                $data['orderwuliustatus'] = array();
                if(!empty($orderwuliustatus)){
                    foreach($orderwuliustatus as $vvl){
                        $vvl['addtime'] = date('m月d日 H:i',$vvl['addtime']);
                        $vvl['telnum'] = 0;
                        $vvl['showmap'] = 0;
                        if($vvl['statustitle'] == '配送员已抢单' || $vvl['statustitle'] == '配送员已接单'){
							if(!empty($order['psemail'])){
								if($order['pttype']==2){
									$vvl['ststusdesc'] = '正前往购买地，配送员电话：';									
								}else{
									$vvl['ststusdesc'] = '正前往取货地，配送员电话：';	
								}
								$vvl['telnum'] = $order['psemail'];
							}else{
								$vvl['telnum'] = '';
							}
                        }elseif($vvl['statustitle'] == '配送员已接指派订单'){
                            if(!empty($order['psemail'])){
								$vvl['ststusdesc'] = $order['psusername'].'接单成功，联系电话：';
								$vvl['telnum'] = $order['psemail'];
							}else{
								$vvl['telnum'] = '';
							}
                        }elseif($vvl['statustitle'] == '配送员已取货' || $vvl['statustitle'] == '配送员已购买'){
                            if($order['psuid'] > 0 && !empty($psbpsyinfo) && !empty($psbpsyinfo['posilnglat'])){
                                $vvl['showmap'] = 1;
                                $posilnglat = explode(',',$psbpsyinfo['posilnglat']);
                                $vvl['markers'] = array(
                                    array(
                                        'id'=> 0,
                                        'iconPath'=> "/images/psylocation_icon.png",
                                        'latitude'=> $posilnglat[1],
                                        'longitude'=> $posilnglat[0],
                                        'width'=> 30,
                                        'height'=> 30
                                    )
                                );
                                $vvl['maplng'] = $posilnglat[0];
                                $vvl['maplat'] = $posilnglat[1];
                            }
                        }
                        $data['orderwuliustatus'][] = $vvl;
                    }
                }
				$data['callphone'] = Mysite::$app->config['litel'];
				$this->success($data);
            }
        } 
    }
	/* 闪购 */
	function marketshop(){
		$adcode = IFilter::act(IReq::get('adcode')); 
		 //获取头部轮播图
		$imglist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."adv where `advtype` = 'chaoshilb' and cityid=".$adcode." " );
		$data['imglist'] = array();
		if(!empty($imglist)){
			foreach($imglist as $k=>$val){
				$val['img'] = getImgQuanDir($val['img']);
				$data['imglist'][] = $val; 
			}
		}
        $goodstype = array();
		$goodstype1 = array('id'=>0,'name'=>'全部');
		$goodstypex  = $this->mysql->select_one("select id  from ".Mysite::$app->config['tablepre']."shoptype  where parent_id = 0 and  is_search = 1  and cattype = 1 and type = 'checkbox'     ");
		$goodstype = $this->mysql->getarr("select id,name  from ".Mysite::$app->config['tablepre']."shoptype where parent_id = '".$goodstypex['id']."' ");
		array_unshift($goodstype,$goodstype1);
		$data['goodstype'] =  $goodstype;
		$this->success($data);
	}
	function marketlistdata(){ 
		//2微信端
		$source = 2;	
		$adcode = IFilter::act(IReq::get('adcode')); 		
		$shopcat = intval(IReq::get('shopcat'));
		$shopcat = $shopcat > 0?$shopcat:0;	  
		$lng = trim(IReq::get('lng'));
		$lat = trim(IReq::get('lat'));
		$lng = empty($lng)?Mysite::$app->config['maplng']:$lng;
        $lat =empty($lat)?Mysite::$app->config['maplat']:$lat;
		
		$limitarr['shoptype'] = 2;
		if($shopcat > 0){
			$limitarr['shopcat'] =$shopcat;
		} 	 	
		$datalistx = $this->Tdata($adcode,$limitarr,array('juli'=>'asc'),$lat,$lng,$source); 
			/*获取店铺*/
		$pageinfo = new page();
		$data['page'] = intval(IReq::get('page'));
		$pageinfo->setpage(intval(IReq::get('page'))); 
		$starnum = $pageinfo->startnum();
		$pagesize = $pageinfo->getsize();
		$templist = array();	
		for($k = 0;$k<$pagesize;$k++){
			$checknum = $starnum+$k;
			if(isset($datalistx[$checknum])){
				$templist[] = $datalistx[$checknum];
			}else{
				break;
			}
		}   
		 
		$templistx = array();
		$da = date("w");
		foreach($templist as $k=>$v){
			$goodslist = array();
			$goodslist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods where is_com = 1  and shopid =".$v['id']." and is_live = 1 and FIND_IN_SET(".$da.",weeks) order by good_order asc limit 3");
			$comlist = array();
			if(!empty($goodslist)){
				foreach($goodslist as $kk=>$vv){
					$cxinfo = $this->goodscx($vv);					 
					$value['id'] = $vv['id'];				 
					$value['name'] = $vv['name'];
					$value['img'] = empty($vv['img'])?getImgQuanDir(Mysite::$app->config['goodlogo']): getImgQuanDir($vv['img']);
					$value['is_cx'] = $cxinfo['is_cx'];
					$value['oldcost'] = floatval(round($cxinfo['oldcost'],2));					 
					$value['cost'] = floatval(round($cxinfo['cxcost'],2));					
					$value['zhekou'] = $cxinfo['zhekou'];
					$comlist[] = $value;
				}
			}
			$v['comgoodslist'] = $comlist;			 
			$templistx[] = $v;
		} 
        $data['shoplist'] = $templistx;			 	
		$this->success($data);
	 }
	  function goodscx($goodsinfo){
		#print_r($goodsinfo);
		$newdata =  new sellrule();
		$newarray = array('cxcost'=>$goodsinfo['cost'],'oldcost'=>$goodsinfo['cost'],'zhekou'=>0,'is_cx'=>0,'cxnum'=>0);	
		if($goodsinfo['is_cx'] == 1){
			$cxdata =	$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$goodsinfo['id']."  ");
			#print_r($cxdata);
			$newdata = getgoodscx($goodsinfo['cost'],$cxdata);
			#print_r($newdata);
			$newarray['oldcost'] = $goodsinfo['cost'];
			$newarray['cxcost'] = $newdata['cost'];
			$newarray['zhekou'] = $newdata['zhekou'];
			$newarray['is_cx'] = $newdata['is_cx'];
			//2016/12/27新增
			$newarray['cxnum'] = $cxdata['cxnum'];
			#print_r($newarray);
		}
		
		return  $newarray;
	}
	function index_cart(){
		$cartdata = IReq::get('cartdata');
		$cart = stripslashes($cartdata);
		$cart = json_decode($cart,true);
		$nowhour = date('H:i:s',time());
		$nowhour = strtotime($nowhour);
		$info = array();
		if(!empty($cart)){
			foreach($cart as $k=>$val){
				$shopinfo = $this->mysql->select_one("select id,shoplogo,shopname,shoptype,goodlistmodule,is_open,starttime from ".Mysite::$app->config['tablepre']."shop where id='".$val['id']."' ");
				$shopinfo['shoplogo'] = empty($shopinfo['shoplogo'])?Mysite::$app->config['shoplogo']:$shopinfo['shoplogo'];
				$shopinfo['shoplogo'] = getImgQuanDir($shopinfo['shoplogo']);				
				if(!empty($shopinfo)){
					if($shopinfo['shoptype'] == 1){
						$shopdet = $this->mysql->select_one("select limitcost,is_orderbefore,arrivetime from ".Mysite::$app->config['tablepre']."shopmarket where shopid='".$val['id']."' ");   
					} else{
						$shopdet = $this->mysql->select_one("select limitcost,is_orderbefore,arrivetime from ".Mysite::$app->config['tablepre']."shopfast where shopid='".$val['id']."' ");  
					}	
					$checkinfo = $this->shopIsopen($shopinfo['is_open'],$shopinfo['starttime'],$shopdet['is_orderbefore'],$nowhour);
					$shopinfo['opentype'] = $checkinfo['opentype'];  
					$shopinfo['arrivetime'] = $shopdet['arrivetime'];  
					$mstime = strtotime('-1 month',$nowhour);
					$monthsellcount  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order where shopid='".$val['id']."' and status = 3 and posttime < ".time()." and posttime > ".$mstime." ");
					$shopinfo['monthsellcount'] = $monthsellcount;
					$shopinfo['limitcost'] = $shopdet['limitcost'] > 0?$shopdet['limitcost']:0;
					$goodsinfo = array();
					$bagcost = 0;
					$shopcost = 0;
					$goodscxdowncost = 0;				
					//常规商品
					if(!empty($val['dishs'])){
						foreach($val['dishs'] as $key=>$value){
							if($value['count']>0){
								$good = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goods  where  id = ".$value['id']." and shopid = '".$val['id']."' ");
								if($good['count'] >= $value['count']){
									$cxinfo = $this->goodscx($good);
									$value['is_cx'] = $cxinfo['is_cx'];
									$value['cost'] = $value['price'];
									if($value['is_cx'] == 1){
										$value['cost'] = round($cxinfo['cxcost'],2);
										$goodscxdowncost += $value['count']*($value['oldcost'] - $value['cost']);
									}						
									$shopcost += $value['cost']*$value['count'];
									$bagcost += $value['bagcost']*$value['count']; 
								}
								$kk['img'] = empty($value['pic'])?getImgQuanDir(Mysite::$app->config['goodlogo']): getImgQuanDir($value['pic']);
								$kk['name'] = $value['name'];	
								$kk['attrname'] = '';								
								$kk['cost'] = floatval($this->formatcost($value['cost'],2));
								$kk['oldcost'] =floatval( $this->formatcost($value['oldcost'],2));
								$kk['count'] = $value['count'];
								$kk['is_cx'] = $value['is_cx'];
								$kk['id'] = $value['id'];
								$goodsinfo[] = $kk;
							}	
						} 
					}				
					//规格商品
				    if(!empty($val['ggdishs'])){
						foreach($val['ggdishs'] as $key=>$value){
							if($value['count']>0){
								$product =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."product where shopid ='".$val['id']."' and id = ".$value['id']." ");
								if($product['stock'] >= $value['count']){
									$dosee = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goods where shopid =".$val['id']." and id =".$value['gid']."  ");
	#print_r(1111);								
									if(!empty($dosee)){
										$cxinfo = $this->goodscx($dosee);
										$value['is_cx'] = $cxinfo['is_cx'];
										$value['cost'] = $value['price'];
										if($value['is_cx'] == 1){
											$value['cost'] = round($cxinfo['cxcost'],2);
											$goodscxdowncost += intval($value['count'])*($value['oldcost'] - $value['cost']);
										}								
										$shopcost += $value['cost']*intval($value['count']);
										$bagcost += $value['bagcost']*intval($value['count']); 
										$ee['img'] = empty($value['pic'])?getImgQuanDir(Mysite::$app->config['goodlogo']):getImgQuanDir($value['pic']);
										$ggname = $value['attrname'];
										$ee['name'] = $value['name'];
										$ee['attrname'] = empty($ggname)?'':$ggname;
										$ee['cost'] = floatval($this->formatcost($value['cost'],2));
										$ee['oldcost'] = floatval($this->formatcost($value['oldcost'],2));
										$ee['count'] = intval($value['count']);
										$ee['is_cx'] = $value['is_cx'];
										$ee['id'] = $value['id'];
										$goodsinfo[] = $ee;
									}								 
								}
							}														   
						}				   
					}			
					$shopinfo['bagcost'] = floatval($bagcost);
					$shopinfo['allcost'] = floatval($shopcost + $bagcost);
					$shopinfo['downcost'] = floatval($goodscxdowncost);
					$shopinfo['goodsinfo'] = $goodsinfo;
					$shopinfo['can_click'] = 1 ;
					$shopinfo['btntext'] = '去结算' ;					
					if($shopinfo['allcost'] < $shopinfo['limitcost']){
						$shopinfo['can_click'] = 0 ;
					    $shopinfo['btntext'] = '差¥'.floatval($shopinfo['limitcost']-$shopinfo['allcost']).'起送' ;
					}
					if($shopinfo['opentype'] != 2 && $shopinfo['opentype'] != 3 ){
						$shopinfo['can_click'] = 0 ;
					    $shopinfo['btntext'] = '已打烊' ;
					}                         
    				unset($shopinfo['limitcost']);
					unset($shopinfo['is_open']);
					unset($shopinfo['starttime']);
				}	
                 $info[] = $shopinfo;
			}
		}
		$data['cartinfo'] = $info;
		$this->success($data);	
	}
}