<?php
/**
 * Created by PhpStorm.
 * User: zyl
 * Date: 2018/10/21
 * Time: 9:44
 */
defined("IN_IA") or exit("Access Denied");
class User_KundianOrderingModel{
    protected $tableName='cqkundian_ordering_user';

    /**
     * 获取用户信息
     * @param $cond
     * @param string $pageIndex
     * @param string $pageSize
     * @param array $filed
     * @param string $orderBy
     * @return array
     */
    public function getUserList($cond,$pageIndex='',$pageSize='',$filed=array(),$orderBy='create_time asc'){
        if(!empty($pageIndex) && !empty($pageSize)){
            $list=pdo_getall($this->tableName,$cond,$filed,'',$orderBy,array($pageIndex,$pageSize));
        }else{
            $list=pdo_getall($this->tableName,$cond,$filed,'',$orderBy);
        }
        return $list;
    }


    /**
     * 根据UID获取用户信息
     * @param $uid
     * @param $uniacid
     * @param array $field
     * @return bool
     */
    public function getUserByUid($uid,$uniacid,$field=array()){
        $list=pdo_get($this->tableName,array('uid'=>$uid,'uniacid'=>$uniacid),$field);
        return $list;
    }
    public function editAddress($data){
        $insertData=[
            'uid'=>$data['uid'],
            'region'=>$data['region'],
            'uniacid'=>$data['uniacid'],
            'address'=>$data['address'],
            'name'=>$data['name'],
            'phone'=>$data['phone']
        ];
        if(empty($data['id'])){
            return pdo_insert('cqkundian_ordering_address',$insertData);
        }
        return pdo_update('cqkundian_ordering_address',$insertData,['id'=>$data['id']]);
    }

}