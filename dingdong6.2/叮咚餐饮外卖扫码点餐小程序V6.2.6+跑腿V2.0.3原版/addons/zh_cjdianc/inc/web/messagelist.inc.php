<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$sql="select * from".tablename('cjdc_message2')." where uniacid={$_W['uniacid']} ORDER BY id DESC";
$total=pdo_fetchcolumn("select count(*) from".tablename('cjdc_message2')." where uniacid={$_W['uniacid']} ");
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list=pdo_fetchall($select_sql);
$pager = pagination($total, $pageindex, $pagesize);
if($_GPC['op']=='delete'){
	$res=pdo_delete('cjdc_message2',array('id'=>$_GPC['id']));
	if($res){
		 message('删除成功！', $this->createWebUrl('messagelist'), 'success');
		}else{
			  message('删除失败！','','error');
		}
}

include $this->template('web/messagelist');