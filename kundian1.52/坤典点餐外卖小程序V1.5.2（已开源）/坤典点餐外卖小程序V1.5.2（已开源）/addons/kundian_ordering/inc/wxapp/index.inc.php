<?php
/**
 * Created by PhpStorm.
 * User: 坤典科技
 * Date: 2018/1/19 0019
 * Time: 10:08
 */
defined("IN_IA") or exit("Access denied");
require_once ROOT_KUNDIAN_ORDERING.'model/slide.php';
require_once ROOT_KUNDIAN_ORDERING.'model/user.php';
require_once ROOT_KUNDIAN_ORDERING.'model/common.php';
class IndexController{
    public $uniacid='';
    public $uid='';
    static $slide='';
    static $user='';
    static $common='';
    public function __construct(){
        global $_GPC;
        $this->uniacid=$_GPC['uniacid'];
        $this->uid=$_GPC['uid'];
        self::$slide=new Slide_KundianOrderingModel();
        self::$user=new User_KundianOrderingModel();
        self::$common=new Common_KundianOrderingModel();
    }
    public function index($get){
        $slide_where=array(
            'status'=>1,
            'uniacid'=>$this->uniacid,
        );
        $request['is_member']=2;
        if($this->uid){
            $memberData=self::$user->getUserByUid($this->uid,$this->uniacid);
            if(empty($memberData)){
                $request['is_member']=1;  //新用户
            }
        }

        $slideData=self::$slide->getSlideData($slide_where);
        for ($i=0;$i<count($slideData);$i++){
            $slideData[$i]['src']=tomedia($slideData[$i]['src']);
        }
        $aboutData=self::$common->getAboutData($this->uniacid);

        //获取设置信息
        $navData=self::$common->getNavList(['uniacid'=>$this->uniacid]);
        if(empty($navData)){
            $navData=array(
                [
                    'color'=>"#d9ead3",
                    'eng_title'=>"reservation",
                    'icon'=>"https://kundian.cqkundian.com/images/12/2018/06/GGtFrDfgRbi864gUQbzDqFeBdwWq6B.png",
                    'id'=>"1",
                    'path'=>"kundian_ordering/pages/make/index/index",
                    'rank'=>"99",
                    'title'=>"预约",
                    'uniacid'=>"12",
                ],
                [
                    'color'=>"#f9cb9c",
                    'eng_title'=>"order dishes",
                    'icon'=>"https://kundian.cqkundian.com/images/12/2018/06/kqR5jy5a8kQj2OZ2808j8Qa52soY8S.png",
                    'id'=>"2",
                    'path'=>"1",
                    'rank'=>"99",
                    'title'=>"点餐",
                    'uniacid'=>"12",
                ],
                [
                    'color'=>"#c9daf8",
                    'eng_title'=>"paying",
                    'icon'=>"https://kundian.cqkundian.com/images/12/2018/06/nXddhslBqxBD1lwXx3D4zZsW1EWAqw.png",
                    'id'=>"3",
                    'path'=>"kundian_ordering/pages/pay/index/index",
                    'rank'=>"99",
                    'title'=>"买单",
                    'uniacid'=>"12",
                ],
                [
                    'color'=>"#ead1dc",
                    'eng_title'=>"fast Foods",
                    'icon'=>"https://kundian.cqkundian.com/images/12/2018/06/G5s5YayEMtEEn95QPeflNQ715tlpAa.png",
                    'id'=>"1",
                    'path'=>"kundian_ordering/pages/out/take_out/index",
                    'rank'=>"99",
                    'title'=>"外卖",
                    'uniacid'=>"12",
                ]

            );
        }

        $otherCon=array(
            'ikey'=>array('is_close_home_type'),
            'uniacid'=>$this->uniacid,
        );
        $otherSet=pdo_getall('cqkundian_ordering_set',$otherCon);
        foreach ($otherSet as $key=>$v){
            $list[$v['ikey']]=$v['value'];
        }
        $request['deskConfig'] = $aboutData['business_mode'];
        $request['slideData']=$slideData;
        $request['aboutData']=$aboutData;
        $request['navData']=$navData;
        echo json_encode($request);
    }
    public function login($get){
        global $_W;
        //判断该用户是否是存在
        $member_where=array(
            'uid'=>$this->uid,
            'uniacid'=>$this->uniacid,
        );
        $memberData=pdo_get("cqkundian_ordering_user",$member_where);
        $updateData=array(
            'uid'=>$this->uid,
            'nickname'=>$get['nickname'] ? $get['nickname'] : $get['wxNickName'],
            'avatarurl'=>$get['avatarUrl'] ? $get['avatarUrl'] : $get['wxAvatar'],
            'create_time'=>time(),
            "rank"=>"99",
            'uniacid'=>$this->uniacid,
            'sex'=>$get['gender'],
        );

        if($_W['openid']){
            $updateData['openid']=$_W['openid'];
        }
        if(empty($memberData)){  //不存在
            $res=pdo_insert("cqkundian_ordering_user",$updateData);
            if($res){
                echo json_encode(array('code'=>'0','msg'=>"登陆成功"));die;
            }
            echo json_encode(array('code'=>'1','msg'=>"登陆失败"));die;
        }

        if($get['avatarUrl']!=undefined && $get['avatarUrl']!='') {
            $res = pdo_update("cqkundian_ordering_user", $updateData, array('uniacid' => $this->uniacid, 'uid' => $this->uid));
        }
        echo json_encode(array('code'=>'0','msg'=>"登陆成功1"));die;
    }
    public function checkHexiao($get){
        global $_W;
        $this->uniacid=$get['uniacid'];
        $this->uid=$get['uid'];
        $hexiaoData=pdo_get("cqkundian_ordering_cancel_person",array('uid'=>$this->uid,'uniacid'=>$this->uniacid,'status'=>1));
        $aboutData=pdo_get('cqkundian_ordering_about',array('uniacid'=>$this->uniacid));

        $condition=array(
            'ikey'=>array('is_open_token'),
            'uniacid'=>$this->uniacid,
        );
        $list=pdo_getall('cqkundian_ordering_set',$condition);
        $setData=array();
        foreach ($list as $key =>$v){
            $setData[$v['ikey']]=$v['value'];
        }

        $back_img=$_W['siteroot'].'addons/kundian_ordering/resource/img/water-1.png';
        $center_img=$_W['siteroot'].'addons/kundian_ordering/resource/img/center_img.jpg';

        if($hexiaoData){
            echo json_encode(array('is_hexiao'=>2,'aboutData'=>$aboutData,'setData'=>$setData,'back_img'=>$back_img,'center_img'=>$center_img));
        }else{
            echo json_encode(array('is_hexiao'=>1,'aboutData'=>$aboutData,'setData'=>$setData,'back_img'=>$back_img,'center_img'=>$center_img));
        }
    }
    public function getUserInfo($get){
        $userInfo=pdo_get('cqkundian_ordering_user',array('uid'=>$this->uid,'uniacid'=>$this->uniacid));
        echo json_encode(array('userInfo'=>$userInfo));
    }

    public function orderMake($get){
        $insertData = array(
            'uid' => $this->uid,
            'uniacid' => $this->uniacid,
            'name' => $get['name'],
            'phone' => $get['phone'],
            'use_date' => $get['date'],
            'use_time' => $get['time'],
            'person_count' => $get['person_count'],
            'create_time' => time(),
            'remark' => $get['remark'],
        );
        $res=pdo_insert('cqkundian_ordering_make_order',$insertData);
        $orderid=pdo_insertid();
        if($res){
            //给店家推送消息
            include 'function.php';
            $peiPerson=pdo_getall('cqkundian_ordering_cancel_person',array('uniacid'=>$this->uniacid,'type'=>2));
            for ($i=0;$i<count($peiPerson);$i++){
                send_make_order_message($peiPerson[$i]['wx_openid'],$orderid,$this->uniacid);
            }
            echo json_encode(array('code'=>1));die;
        }
        echo json_encode(array('code'=>2));die;
    }

    public function saveAddress($get){
        if($get['operation']=='add'){
            $res=self::$user->editAddress($get);
            echo $res ? json_encode(['code'=>'0','msg'=>'保存成功']) : json_encode(['code'=>-1,'msg'=>'保存失败']);die;
        }

        if($get['operation']=='changeDefault'){
            $res1=pdo_update('cqkundian_ordering_address',['is_default'=>0],['uid'=>$this->uid,'uniacid'=>$this->uniacid,'is_default'=>1]);
            $res=pdo_update('cqkundian_ordering_address',['is_default'=>$get['is_default']],['uid'=>$this->uid,'id'=>$get['id'],'uniacid'=>$this->uniacid]);
            if($res1 || $res){
                echo json_encode(['code'=>0,'msg'=>'设置成功']);die;
            }
            echo json_encode(['code'=>-1,'msg'=>'设置失败']);die;
        }

        if($get['operation']=='deleteAdd'){
            $res=pdo_delete('cqkundian_ordering_address',['id'=>$get['id'],'uid'=>$get['uid'],'uniacid'=>$this->uniacid]);
            echo $res ? json_encode(['code'=>0,'msg'=>'删除成功']): json_encode(['code'=>-1,'msg'=>'删除失败']);die;
        }

    }

    public function addressList($get){
        $addList=pdo_getall('cqkundian_ordering_address',['uniacid'=>$this->uniacid,'uid'=>$this->uid]);
        $addressSet=pdo_get('cqkundian_ordering_set',array('uniacid'=>$this->uniacid,'ikey'=>'address_switch'));
        echo json_encode(['addList'=>$addList,'address_switch'=>$addressSet['value']]);die;
    }

    public function getAddress($get){
        $address=pdo_get('cqkundian_ordering_address',['uid'=>$this->uid,'uniacid'=>$this->uniacid,'is_default'=>1]);
        echo json_encode(['address'=>$address]);die;
    }

}