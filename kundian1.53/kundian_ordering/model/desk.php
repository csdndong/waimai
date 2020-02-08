<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/23
 * Time: 10:02
 */
defined("IN_IA") or exit("Access Denied");
class Desk_KundianOrderingModel{
    /**
     * 获取餐桌列表信息
     * @param $cond
     * @param bool $mutilple
     * @return array|bool
     */
    public function getDeskList($cond,$mutilple=true){
        if($mutilple){
            $list=pdo_getall('cqkundian_ordering_desk',$cond,'','','rank asc');
        }else{
            $list=pdo_get('cqkundian_ordering_desk',$cond);
        }
        return $list;
    }
}