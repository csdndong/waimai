<?php
/**
 * Created by PhpStorm.
 * User: zyl
 * Date: 2018/10/20
 * Time: 19:57
 */
defined("IN_IA") or exit("Access Denied");
class Slide_KundianOrderingModel{
    protected $tableName='cqkundian_ordering_slide';

    /**
     * 获取轮播图信息
     * @param $cond
     * @param string $pageIndex
     * @param string $pageSize
     * @param array $filed
     * @param string $orderBy
     * @return array
     */
    public function getSlideData($cond,$pageIndex='',$pageSize='',$filed=array(),$orderBy='rank asc'){
        if(!empty($pageIndex) && !empty($pageSize)){
            $list=pdo_getall($this->tableName,$cond,$filed,'',$orderBy,array($pageIndex,$pageSize));
        }else{
            $list=pdo_getall($this->tableName,$cond,$filed,'',$orderBy);
        }
        return $list;
    }
}