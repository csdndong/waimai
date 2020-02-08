<?php
/**
 * 点餐外卖模块定义
 *
 * @author cqkundian
 * @url https://bbs.5g-yun.com/
 */
defined('IN_IA') or exit('Access Denied');

class Kundian_orderingModule extends WeModule {
 
 	public function main(){
 		$url=url('site/entry/index',array('m'=>'kundian_ordering','op'=>'index'));
        message('',$url);
 	}
}

$kundian_ordering=new Kundian_orderingModule();
$kundian_ordering->main();