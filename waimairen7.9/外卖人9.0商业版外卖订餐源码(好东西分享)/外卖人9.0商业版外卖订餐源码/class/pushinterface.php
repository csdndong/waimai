<?php  
/*** 
*    多通道推送
*    使用到的接口
*    
**/
interface  pushinterface
{ 
    function Title($Title);//设置标题
    function Content($Content);//设置内容
	function UserList($userlist);//设置内容
	function Sound($sound);//声音类型
	function ExtData($extdata);//附加数据
	function SendMsg();//发送消息
	function SendNotify();//发送通知
	function ShopUid($uid);
}