<?php
//升级数据表
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `storeid` varchar(1000) NOT NULL,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `from_user` varchar(100) NOT NULL DEFAULT '',
  `accountname` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(200) NOT NULL DEFAULT '',
  `salt` varchar(10) NOT NULL DEFAULT '',
  `pwd` varchar(50) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `email` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `pay_account` varchar(200) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '状态',
  `role` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1:店长,2:店员',
  `lastvisit` int(10) unsigned NOT NULL DEFAULT '0',
  `lastip` varchar(15) NOT NULL,
  `areaid` int(10) NOT NULL DEFAULT '0' COMMENT '区域id',
  `is_admin_order` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_notice_order` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_notice_queue` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_notice_service` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_notice_boss` tinyint(1) NOT NULL DEFAULT '0',
  `remark` varchar(1000) NOT NULL DEFAULT '' COMMENT '备注',
  `lat` decimal(18,10) NOT NULL DEFAULT '0.0000000000' COMMENT '经度',
  `lng` decimal(18,10) NOT NULL DEFAULT '0.0000000000' COMMENT '纬度',
  `authority` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_account','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_account','weid')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号'");}
if(!pdo_fieldexists('cjdc_account','storeid')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `storeid` varchar(1000) NOT NULL");}
if(!pdo_fieldexists('cjdc_account','uid')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `uid` int(10) unsigned NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('cjdc_account','from_user')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `from_user` varchar(100) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('cjdc_account','accountname')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `accountname` varchar(50) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('cjdc_account','password')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `password` varchar(200) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('cjdc_account','salt')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `salt` varchar(10) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('cjdc_account','pwd')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `pwd` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_account','mobile')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `mobile` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_account','email')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `email` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_account','username')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `username` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_account','pay_account')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `pay_account` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_account','displayorder')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_account','dateline')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `dateline` int(10) unsigned NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('cjdc_account','status')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `status` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '状态'");}
if(!pdo_fieldexists('cjdc_account','role')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `role` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1:店长,2:店员'");}
if(!pdo_fieldexists('cjdc_account','lastvisit')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `lastvisit` int(10) unsigned NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('cjdc_account','lastip')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `lastip` varchar(15) NOT NULL");}
if(!pdo_fieldexists('cjdc_account','areaid')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `areaid` int(10) NOT NULL DEFAULT '0' COMMENT '区域id'");}
if(!pdo_fieldexists('cjdc_account','is_admin_order')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `is_admin_order` tinyint(1) unsigned NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_account','is_notice_order')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `is_notice_order` tinyint(1) unsigned NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_account','is_notice_queue')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `is_notice_queue` tinyint(1) unsigned NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_account','is_notice_service')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `is_notice_service` tinyint(1) unsigned NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_account','is_notice_boss')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `is_notice_boss` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('cjdc_account','remark')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `remark` varchar(1000) NOT NULL DEFAULT '' COMMENT '备注'");}
if(!pdo_fieldexists('cjdc_account','lat')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `lat` decimal(18,10) NOT NULL DEFAULT '0.0000000000' COMMENT '经度'");}
if(!pdo_fieldexists('cjdc_account','lng')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `lng` decimal(18,10) NOT NULL DEFAULT '0.0000000000' COMMENT '纬度'");}
if(!pdo_fieldexists('cjdc_account','authority')) {pdo_query("ALTER TABLE ".tablename('cjdc_account')." ADD   `authority` text NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_ad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `logo` varchar(200) NOT NULL,
  `src` varchar(100) NOT NULL COMMENT '内部链接',
  `src2` varchar(200) NOT NULL COMMENT '外部链接',
  `created_time` varchar(20) NOT NULL COMMENT '创建时间',
  `orderby` int(11) NOT NULL COMMENT '排序',
  `status` int(11) NOT NULL COMMENT '1.启用2.禁用',
  `type` int(11) NOT NULL COMMENT '类型',
  `appid` varchar(20) NOT NULL COMMENT '小程序appid',
  `title` varchar(20) NOT NULL COMMENT '小程序appid',
  `xcx_name` varchar(20) NOT NULL COMMENT '小程序名称',
  `item` int(11) NOT NULL COMMENT '1.内部2.外部3.跳转小程序',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_ad','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_ad')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_ad','logo')) {pdo_query("ALTER TABLE ".tablename('cjdc_ad')." ADD   `logo` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_ad','src')) {pdo_query("ALTER TABLE ".tablename('cjdc_ad')." ADD   `src` varchar(100) NOT NULL COMMENT '内部链接'");}
if(!pdo_fieldexists('cjdc_ad','src2')) {pdo_query("ALTER TABLE ".tablename('cjdc_ad')." ADD   `src2` varchar(200) NOT NULL COMMENT '外部链接'");}
if(!pdo_fieldexists('cjdc_ad','created_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_ad')." ADD   `created_time` varchar(20) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('cjdc_ad','orderby')) {pdo_query("ALTER TABLE ".tablename('cjdc_ad')." ADD   `orderby` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_ad','status')) {pdo_query("ALTER TABLE ".tablename('cjdc_ad')." ADD   `status` int(11) NOT NULL COMMENT '1.启用2.禁用'");}
if(!pdo_fieldexists('cjdc_ad','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_ad')." ADD   `type` int(11) NOT NULL COMMENT '类型'");}
if(!pdo_fieldexists('cjdc_ad','appid')) {pdo_query("ALTER TABLE ".tablename('cjdc_ad')." ADD   `appid` varchar(20) NOT NULL COMMENT '小程序appid'");}
if(!pdo_fieldexists('cjdc_ad','title')) {pdo_query("ALTER TABLE ".tablename('cjdc_ad')." ADD   `title` varchar(20) NOT NULL COMMENT '小程序appid'");}
if(!pdo_fieldexists('cjdc_ad','xcx_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_ad')." ADD   `xcx_name` varchar(20) NOT NULL COMMENT '小程序名称'");}
if(!pdo_fieldexists('cjdc_ad','item')) {pdo_query("ALTER TABLE ".tablename('cjdc_ad')." ADD   `item` int(11) NOT NULL COMMENT '1.内部2.外部3.跳转小程序'");}
if(!pdo_fieldexists('cjdc_ad','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_ad')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area_name` varchar(20) NOT NULL COMMENT '区域名称',
  `num` int(11) NOT NULL COMMENT '排序',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='门店区域';

");

if(!pdo_fieldexists('cjdc_area','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_area')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_area','area_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_area')." ADD   `area_name` varchar(20) NOT NULL COMMENT '区域名称'");}
if(!pdo_fieldexists('cjdc_area','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_area')." ADD   `num` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_area','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_area')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_assess` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `img` text NOT NULL,
  `stars` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `time` varchar(20) NOT NULL,
  `state` int(11) NOT NULL COMMENT '1.未回复2.已回复',
  `order_id` int(11) NOT NULL,
  `hf` text NOT NULL,
  `hf_time` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_assess','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_assess')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_assess','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_assess')." ADD   `user_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_assess','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_assess')." ADD   `store_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_assess','content')) {pdo_query("ALTER TABLE ".tablename('cjdc_assess')." ADD   `content` text NOT NULL");}
if(!pdo_fieldexists('cjdc_assess','img')) {pdo_query("ALTER TABLE ".tablename('cjdc_assess')." ADD   `img` text NOT NULL");}
if(!pdo_fieldexists('cjdc_assess','stars')) {pdo_query("ALTER TABLE ".tablename('cjdc_assess')." ADD   `stars` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_assess','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_assess')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_assess','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_assess')." ADD   `time` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_assess','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_assess')." ADD   `state` int(11) NOT NULL COMMENT '1.未回复2.已回复'");}
if(!pdo_fieldexists('cjdc_assess','order_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_assess')." ADD   `order_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_assess','hf')) {pdo_query("ALTER TABLE ".tablename('cjdc_assess')." ADD   `hf` text NOT NULL");}
if(!pdo_fieldexists('cjdc_assess','hf_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_assess')." ADD   `hf_time` varchar(20) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_call` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL COMMENT '门店ID',
  `is_open` int(4) NOT NULL DEFAULT '2' COMMENT '1开启,2关闭',
  `appid` varchar(20) NOT NULL,
  `apikey` varchar(50) NOT NULL,
  `src` varchar(50) NOT NULL COMMENT '音频文件路径',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='讯飞表';

");

if(!pdo_fieldexists('cjdc_call','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_call')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_call','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_call')." ADD   `store_id` int(11) NOT NULL COMMENT '门店ID'");}
if(!pdo_fieldexists('cjdc_call','is_open')) {pdo_query("ALTER TABLE ".tablename('cjdc_call')." ADD   `is_open` int(4) NOT NULL DEFAULT '2' COMMENT '1开启,2关闭'");}
if(!pdo_fieldexists('cjdc_call','appid')) {pdo_query("ALTER TABLE ".tablename('cjdc_call')." ADD   `appid` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_call','apikey')) {pdo_query("ALTER TABLE ".tablename('cjdc_call')." ADD   `apikey` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_call','src')) {pdo_query("ALTER TABLE ".tablename('cjdc_call')." ADD   `src` varchar(50) NOT NULL COMMENT '音频文件路径'");}
if(!pdo_fieldexists('cjdc_call','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_call')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_calllog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `store_id` int(11) NOT NULL COMMENT '门店ID',
  `table_id` int(11) NOT NULL COMMENT '餐桌ID',
  `time` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `state` int(4) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='呼叫记录';

");

if(!pdo_fieldexists('cjdc_calllog','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_calllog')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_calllog','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_calllog')." ADD   `user_id` int(11) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('cjdc_calllog','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_calllog')." ADD   `store_id` int(11) NOT NULL COMMENT '门店ID'");}
if(!pdo_fieldexists('cjdc_calllog','table_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_calllog')." ADD   `table_id` int(11) NOT NULL COMMENT '餐桌ID'");}
if(!pdo_fieldexists('cjdc_calllog','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_calllog')." ADD   `time` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_calllog','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_calllog')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_calllog','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_calllog')." ADD   `state` int(4) NOT NULL DEFAULT '2'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_collection` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `state` int(4) NOT NULL COMMENT '1收藏,2取消',
  `time` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='门店收藏表';

");

if(!pdo_fieldexists('cjdc_collection','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_collection')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_collection','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_collection')." ADD   `user_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_collection','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_collection')." ADD   `store_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_collection','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_collection')." ADD   `state` int(4) NOT NULL COMMENT '1收藏,2取消'");}
if(!pdo_fieldexists('cjdc_collection','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_collection')." ADD   `time` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_collection','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_collection')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_commission_withdrawal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `state` int(11) NOT NULL COMMENT '1.审核中2.通过3.拒绝',
  `time` int(11) NOT NULL COMMENT '申请时间',
  `sh_time` int(11) NOT NULL COMMENT '审核时间',
  `uniacid` int(11) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `account` varchar(100) NOT NULL,
  `tx_cost` decimal(10,2) NOT NULL COMMENT '提现金额',
  `sj_cost` decimal(10,2) NOT NULL COMMENT '实际到账金额',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='佣金提现';

");

if(!pdo_fieldexists('cjdc_commission_withdrawal','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_commission_withdrawal')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_commission_withdrawal','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_commission_withdrawal')." ADD   `user_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_commission_withdrawal','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_commission_withdrawal')." ADD   `state` int(11) NOT NULL COMMENT '1.审核中2.通过3.拒绝'");}
if(!pdo_fieldexists('cjdc_commission_withdrawal','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_commission_withdrawal')." ADD   `time` int(11) NOT NULL COMMENT '申请时间'");}
if(!pdo_fieldexists('cjdc_commission_withdrawal','sh_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_commission_withdrawal')." ADD   `sh_time` int(11) NOT NULL COMMENT '审核时间'");}
if(!pdo_fieldexists('cjdc_commission_withdrawal','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_commission_withdrawal')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_commission_withdrawal','user_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_commission_withdrawal')." ADD   `user_name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_commission_withdrawal','account')) {pdo_query("ALTER TABLE ".tablename('cjdc_commission_withdrawal')." ADD   `account` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_commission_withdrawal','tx_cost')) {pdo_query("ALTER TABLE ".tablename('cjdc_commission_withdrawal')." ADD   `tx_cost` decimal(10,2) NOT NULL COMMENT '提现金额'");}
if(!pdo_fieldexists('cjdc_commission_withdrawal','sj_cost')) {pdo_query("ALTER TABLE ".tablename('cjdc_commission_withdrawal')." ADD   `sj_cost` decimal(10,2) NOT NULL COMMENT '实际到账金额'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_continuous` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `day` int(11) NOT NULL COMMENT '天数',
  `integral` int(11) NOT NULL COMMENT '积分',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='连签奖励';

");

if(!pdo_fieldexists('cjdc_continuous','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_continuous')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_continuous','day')) {pdo_query("ALTER TABLE ".tablename('cjdc_continuous')." ADD   `day` int(11) NOT NULL COMMENT '天数'");}
if(!pdo_fieldexists('cjdc_continuous','integral')) {pdo_query("ALTER TABLE ".tablename('cjdc_continuous')." ADD   `integral` int(11) NOT NULL COMMENT '积分'");}
if(!pdo_fieldexists('cjdc_continuous','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_continuous')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '优惠券名称',
  `start_time` varchar(20) NOT NULL COMMENT '开始时间',
  `end_time` varchar(20) NOT NULL COMMENT '结束时间',
  `full` decimal(10,1) NOT NULL COMMENT '慢',
  `reduce` decimal(10,1) NOT NULL COMMENT '减',
  `store_id` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1.外卖2.店内3.通用',
  `uniacid` int(11) NOT NULL,
  `number` int(11) NOT NULL COMMENT '数量',
  `stock` int(11) NOT NULL COMMENT '库存',
  `instruction` text NOT NULL COMMENT '使用说明',
  `type_id` varchar(20) NOT NULL COMMENT '分类id',
  `is_hy` int(1) NOT NULL DEFAULT '2',
  `day` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_coupons','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_coupons')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_coupons','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_coupons')." ADD   `name` varchar(20) NOT NULL COMMENT '优惠券名称'");}
if(!pdo_fieldexists('cjdc_coupons','start_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_coupons')." ADD   `start_time` varchar(20) NOT NULL COMMENT '开始时间'");}
if(!pdo_fieldexists('cjdc_coupons','end_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_coupons')." ADD   `end_time` varchar(20) NOT NULL COMMENT '结束时间'");}
if(!pdo_fieldexists('cjdc_coupons','full')) {pdo_query("ALTER TABLE ".tablename('cjdc_coupons')." ADD   `full` decimal(10,1) NOT NULL COMMENT '慢'");}
if(!pdo_fieldexists('cjdc_coupons','reduce')) {pdo_query("ALTER TABLE ".tablename('cjdc_coupons')." ADD   `reduce` decimal(10,1) NOT NULL COMMENT '减'");}
if(!pdo_fieldexists('cjdc_coupons','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_coupons')." ADD   `store_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_coupons','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_coupons')." ADD   `type` int(11) NOT NULL COMMENT '1.外卖2.店内3.通用'");}
if(!pdo_fieldexists('cjdc_coupons','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_coupons')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_coupons','number')) {pdo_query("ALTER TABLE ".tablename('cjdc_coupons')." ADD   `number` int(11) NOT NULL COMMENT '数量'");}
if(!pdo_fieldexists('cjdc_coupons','stock')) {pdo_query("ALTER TABLE ".tablename('cjdc_coupons')." ADD   `stock` int(11) NOT NULL COMMENT '库存'");}
if(!pdo_fieldexists('cjdc_coupons','instruction')) {pdo_query("ALTER TABLE ".tablename('cjdc_coupons')." ADD   `instruction` text NOT NULL COMMENT '使用说明'");}
if(!pdo_fieldexists('cjdc_coupons','type_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_coupons')." ADD   `type_id` varchar(20) NOT NULL COMMENT '分类id'");}
if(!pdo_fieldexists('cjdc_coupons','is_hy')) {pdo_query("ALTER TABLE ".tablename('cjdc_coupons')." ADD   `is_hy` int(1) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_coupons','day')) {pdo_query("ALTER TABLE ".tablename('cjdc_coupons')." ADD   `day` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_couponset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_tjhb` int(11) NOT NULL DEFAULT '2' COMMENT '1.开启2.关闭',
  `yhq_set` int(11) NOT NULL DEFAULT '2' COMMENT '1.是2否商家优惠券和平台红包是否可同时使用',
  `time` varchar(10) NOT NULL COMMENT '开始时间',
  `time2` varchar(10) NOT NULL COMMENT '结束时间',
  `number` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_couponset','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_couponset')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_couponset','is_tjhb')) {pdo_query("ALTER TABLE ".tablename('cjdc_couponset')." ADD   `is_tjhb` int(11) NOT NULL DEFAULT '2' COMMENT '1.开启2.关闭'");}
if(!pdo_fieldexists('cjdc_couponset','yhq_set')) {pdo_query("ALTER TABLE ".tablename('cjdc_couponset')." ADD   `yhq_set` int(11) NOT NULL DEFAULT '2' COMMENT '1.是2否商家优惠券和平台红包是否可同时使用'");}
if(!pdo_fieldexists('cjdc_couponset','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_couponset')." ADD   `time` varchar(10) NOT NULL COMMENT '开始时间'");}
if(!pdo_fieldexists('cjdc_couponset','time2')) {pdo_query("ALTER TABLE ".tablename('cjdc_couponset')." ADD   `time2` varchar(10) NOT NULL COMMENT '结束时间'");}
if(!pdo_fieldexists('cjdc_couponset','number')) {pdo_query("ALTER TABLE ".tablename('cjdc_couponset')." ADD   `number` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_couponset','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_couponset')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_cptj` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `img` varchar(500) NOT NULL,
  `details` text NOT NULL,
  `content` varchar(100) NOT NULL,
  `src` varchar(100) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_cptj','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_cptj')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_cptj','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_cptj')." ADD   `name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_cptj','img')) {pdo_query("ALTER TABLE ".tablename('cjdc_cptj')." ADD   `img` varchar(500) NOT NULL");}
if(!pdo_fieldexists('cjdc_cptj','details')) {pdo_query("ALTER TABLE ".tablename('cjdc_cptj')." ADD   `details` text NOT NULL");}
if(!pdo_fieldexists('cjdc_cptj','content')) {pdo_query("ALTER TABLE ".tablename('cjdc_cptj')." ADD   `content` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_cptj','src')) {pdo_query("ALTER TABLE ".tablename('cjdc_cptj')." ADD   `src` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_cptj','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_cptj')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_cptj','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_cptj')." ADD   `num` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_czhd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full` int(11) NOT NULL,
  `reduction` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_czhd','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_czhd')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_czhd','full')) {pdo_query("ALTER TABLE ".tablename('cjdc_czhd')." ADD   `full` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_czhd','reduction')) {pdo_query("ALTER TABLE ".tablename('cjdc_czhd')." ADD   `reduction` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_czhd','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_czhd')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_czorder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `money` decimal(10,2) NOT NULL,
  `money2` decimal(10,2) NOT NULL,
  `user_id` int(11) NOT NULL,
  `state` int(11) NOT NULL COMMENT '1.待支付2.已支付',
  `code` varchar(100) NOT NULL,
  `form_id` varchar(100) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `time` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_czorder','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_czorder')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_czorder','money')) {pdo_query("ALTER TABLE ".tablename('cjdc_czorder')." ADD   `money` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_czorder','money2')) {pdo_query("ALTER TABLE ".tablename('cjdc_czorder')." ADD   `money2` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_czorder','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_czorder')." ADD   `user_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_czorder','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_czorder')." ADD   `state` int(11) NOT NULL COMMENT '1.待支付2.已支付'");}
if(!pdo_fieldexists('cjdc_czorder','code')) {pdo_query("ALTER TABLE ".tablename('cjdc_czorder')." ADD   `code` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_czorder','form_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_czorder')." ADD   `form_id` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_czorder','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_czorder')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_czorder','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_czorder')." ADD   `time` varchar(20) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_distribution` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start` int(11) NOT NULL COMMENT '配送起始值',
  `end` int(11) NOT NULL COMMENT '配送结束值',
  `money` decimal(10,2) NOT NULL COMMENT '价格',
  `num` int(11) NOT NULL COMMENT '排序',
  `store_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `store_id_2` (`store_id`),
  KEY `store_id_3` (`store_id`),
  KEY `store_id_4` (`store_id`),
  KEY `store_id_5` (`store_id`),
  KEY `store_id_6` (`store_id`),
  KEY `store_id_7` (`store_id`),
  KEY `store_id_8` (`store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_distribution','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_distribution')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_distribution','start')) {pdo_query("ALTER TABLE ".tablename('cjdc_distribution')." ADD   `start` int(11) NOT NULL COMMENT '配送起始值'");}
if(!pdo_fieldexists('cjdc_distribution','end')) {pdo_query("ALTER TABLE ".tablename('cjdc_distribution')." ADD   `end` int(11) NOT NULL COMMENT '配送结束值'");}
if(!pdo_fieldexists('cjdc_distribution','money')) {pdo_query("ALTER TABLE ".tablename('cjdc_distribution')." ADD   `money` decimal(10,2) NOT NULL COMMENT '价格'");}
if(!pdo_fieldexists('cjdc_distribution','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_distribution')." ADD   `num` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_distribution','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_distribution')." ADD   `store_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_distribution','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_distribution')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('cjdc_distribution','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_distribution')." ADD   KEY `store_id` (`store_id`)");}
if(!pdo_fieldexists('cjdc_distribution','store_id_2')) {pdo_query("ALTER TABLE ".tablename('cjdc_distribution')." ADD   KEY `store_id_2` (`store_id`)");}
if(!pdo_fieldexists('cjdc_distribution','store_id_3')) {pdo_query("ALTER TABLE ".tablename('cjdc_distribution')." ADD   KEY `store_id_3` (`store_id`)");}
if(!pdo_fieldexists('cjdc_distribution','store_id_4')) {pdo_query("ALTER TABLE ".tablename('cjdc_distribution')." ADD   KEY `store_id_4` (`store_id`)");}
if(!pdo_fieldexists('cjdc_distribution','store_id_5')) {pdo_query("ALTER TABLE ".tablename('cjdc_distribution')." ADD   KEY `store_id_5` (`store_id`)");}
if(!pdo_fieldexists('cjdc_distribution','store_id_6')) {pdo_query("ALTER TABLE ".tablename('cjdc_distribution')." ADD   KEY `store_id_6` (`store_id`)");}
if(!pdo_fieldexists('cjdc_distribution','store_id_7')) {pdo_query("ALTER TABLE ".tablename('cjdc_distribution')." ADD   KEY `store_id_7` (`store_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_drorder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  `time` varchar(20) NOT NULL,
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_drorder','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_drorder')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_drorder','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_drorder')." ADD   `user_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_drorder','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_drorder')." ADD   `store_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_drorder','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_drorder')." ADD   `state` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_drorder','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_drorder')." ADD   `time` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_drorder','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_drorder')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_dyj` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `tag_id` varchar(30) NOT NULL COMMENT '打印标签id',
  `name` varchar(20) NOT NULL COMMENT '打印机名称',
  `dyj_title` varchar(50) NOT NULL COMMENT '打印机标题',
  `dyj_id` varchar(50) NOT NULL COMMENT '打印机编号',
  `dyj_key` varchar(50) NOT NULL COMMENT '打印机key',
  `api` varchar(100) NOT NULL COMMENT 'API密钥',
  `mid` varchar(100) NOT NULL COMMENT '打印机终端号',
  `state` int(11) NOT NULL COMMENT '1开启2关闭',
  `location` int(11) NOT NULL COMMENT '1..前台 2后厨',
  `yy_id` varchar(20) NOT NULL COMMENT '用户id',
  `token` varchar(50) NOT NULL COMMENT '打印机终端密钥',
  `num` int(11) NOT NULL DEFAULT '1' COMMENT '打印几次',
  `fezh` varchar(40) NOT NULL COMMENT '飞蛾账号',
  `fe_ukey` varchar(50) NOT NULL COMMENT 'ukey',
  `fe_dycode` varchar(20) NOT NULL COMMENT '打印机编号',
  `type` int(11) NOT NULL COMMENT '1.365  2.易联云，3飞蛾',
  `uniacid` varchar(50) NOT NULL,
  `xx_sn` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_dyj','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_dyj','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `store_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_dyj','tag_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `tag_id` varchar(30) NOT NULL COMMENT '打印标签id'");}
if(!pdo_fieldexists('cjdc_dyj','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `name` varchar(20) NOT NULL COMMENT '打印机名称'");}
if(!pdo_fieldexists('cjdc_dyj','dyj_title')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `dyj_title` varchar(50) NOT NULL COMMENT '打印机标题'");}
if(!pdo_fieldexists('cjdc_dyj','dyj_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `dyj_id` varchar(50) NOT NULL COMMENT '打印机编号'");}
if(!pdo_fieldexists('cjdc_dyj','dyj_key')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `dyj_key` varchar(50) NOT NULL COMMENT '打印机key'");}
if(!pdo_fieldexists('cjdc_dyj','api')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `api` varchar(100) NOT NULL COMMENT 'API密钥'");}
if(!pdo_fieldexists('cjdc_dyj','mid')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `mid` varchar(100) NOT NULL COMMENT '打印机终端号'");}
if(!pdo_fieldexists('cjdc_dyj','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `state` int(11) NOT NULL COMMENT '1开启2关闭'");}
if(!pdo_fieldexists('cjdc_dyj','location')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `location` int(11) NOT NULL COMMENT '1..前台 2后厨'");}
if(!pdo_fieldexists('cjdc_dyj','yy_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `yy_id` varchar(20) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('cjdc_dyj','token')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `token` varchar(50) NOT NULL COMMENT '打印机终端密钥'");}
if(!pdo_fieldexists('cjdc_dyj','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `num` int(11) NOT NULL DEFAULT '1' COMMENT '打印几次'");}
if(!pdo_fieldexists('cjdc_dyj','fezh')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `fezh` varchar(40) NOT NULL COMMENT '飞蛾账号'");}
if(!pdo_fieldexists('cjdc_dyj','fe_ukey')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `fe_ukey` varchar(50) NOT NULL COMMENT 'ukey'");}
if(!pdo_fieldexists('cjdc_dyj','fe_dycode')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `fe_dycode` varchar(20) NOT NULL COMMENT '打印机编号'");}
if(!pdo_fieldexists('cjdc_dyj','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `type` int(11) NOT NULL COMMENT '1.365  2.易联云，3飞蛾'");}
if(!pdo_fieldexists('cjdc_dyj','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `uniacid` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_dyj','xx_sn')) {pdo_query("ALTER TABLE ".tablename('cjdc_dyj')." ADD   `xx_sn` varchar(10) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_dytag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL COMMENT '门店id',
  `tag_name` varchar(30) NOT NULL COMMENT '标签名称',
  `sort` int(11) NOT NULL COMMENT '排序',
  `time` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='打印标签表';

");

if(!pdo_fieldexists('cjdc_dytag','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_dytag')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_dytag','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_dytag')." ADD   `store_id` int(11) NOT NULL COMMENT '门店id'");}
if(!pdo_fieldexists('cjdc_dytag','tag_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_dytag')." ADD   `tag_name` varchar(30) NOT NULL COMMENT '标签名称'");}
if(!pdo_fieldexists('cjdc_dytag','sort')) {pdo_query("ALTER TABLE ".tablename('cjdc_dytag')." ADD   `sort` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_dytag','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_dytag')." ADD   `time` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_dytag','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_dytag')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_earnings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT '订单ID',
  `user_id` int(11) NOT NULL,
  `son_id` int(11) NOT NULL COMMENT '下线',
  `money` decimal(10,2) NOT NULL,
  `time` int(11) NOT NULL,
  `note` varchar(50) NOT NULL COMMENT '备注',
  `state` int(4) NOT NULL COMMENT '佣金状态,1冻结,2有效,3无效',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='佣金收益表';

");

if(!pdo_fieldexists('cjdc_earnings','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_earnings')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_earnings','order_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_earnings')." ADD   `order_id` int(11) NOT NULL COMMENT '订单ID'");}
if(!pdo_fieldexists('cjdc_earnings','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_earnings')." ADD   `user_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_earnings','son_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_earnings')." ADD   `son_id` int(11) NOT NULL COMMENT '下线'");}
if(!pdo_fieldexists('cjdc_earnings','money')) {pdo_query("ALTER TABLE ".tablename('cjdc_earnings')." ADD   `money` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_earnings','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_earnings')." ADD   `time` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_earnings','note')) {pdo_query("ALTER TABLE ".tablename('cjdc_earnings')." ADD   `note` varchar(50) NOT NULL COMMENT '备注'");}
if(!pdo_fieldexists('cjdc_earnings','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_earnings')." ADD   `state` int(4) NOT NULL COMMENT '佣金状态,1冻结,2有效,3无效'");}
if(!pdo_fieldexists('cjdc_earnings','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_earnings')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_formid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `form_id` varchar(200) NOT NULL,
  `time` int(11) NOT NULL,
  `state` int(11) NOT NULL DEFAULT '1' COMMENT '1.未使用2.已使用',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_formid','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_formid')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_formid','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_formid')." ADD   `user_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_formid','form_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_formid')." ADD   `form_id` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_formid','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_formid')." ADD   `time` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_formid','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_formid')." ADD   `state` int(11) NOT NULL DEFAULT '1' COMMENT '1.未使用2.已使用'");}
if(!pdo_fieldexists('cjdc_formid','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_formid')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_fxset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fx_details` text NOT NULL COMMENT '分销商申请协议',
  `tx_details` text NOT NULL COMMENT '佣金提现协议',
  `is_fx` int(11) NOT NULL COMMENT '1.一级分销,2二级分销',
  `is_ej` int(11) NOT NULL COMMENT '是否开启二级分销1.是2.否',
  `is_type` int(11) NOT NULL COMMENT '1.开启2.关闭(分类佣金)',
  `tx_rate` int(11) NOT NULL COMMENT '提现手续费',
  `dn_yj` varchar(10) NOT NULL COMMENT '店内一级佣金',
  `dn_ej` varchar(10) NOT NULL COMMENT '店内二级佣金',
  `wm_yj` varchar(10) NOT NULL COMMENT '外卖一级',
  `wm_ej` varchar(10) NOT NULL COMMENT '外卖二级',
  `tx_money` int(11) NOT NULL COMMENT '提现门槛',
  `img` varchar(100) NOT NULL,
  `img2` varchar(100) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `is_open` int(11) NOT NULL DEFAULT '1' COMMENT '1.开启2关闭',
  `instructions` text NOT NULL COMMENT '分销商说明',
  `fx_name` varchar(30) NOT NULL DEFAULT '分销中心',
  `type` int(4) NOT NULL DEFAULT '1',
  `is_check` int(4) NOT NULL DEFAULT '1',
  `xx_name` varchar(50) NOT NULL DEFAULT '我的伙伴',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_fxset','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_fxset','fx_details')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `fx_details` text NOT NULL COMMENT '分销商申请协议'");}
if(!pdo_fieldexists('cjdc_fxset','tx_details')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `tx_details` text NOT NULL COMMENT '佣金提现协议'");}
if(!pdo_fieldexists('cjdc_fxset','is_fx')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `is_fx` int(11) NOT NULL COMMENT '1.一级分销,2二级分销'");}
if(!pdo_fieldexists('cjdc_fxset','is_ej')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `is_ej` int(11) NOT NULL COMMENT '是否开启二级分销1.是2.否'");}
if(!pdo_fieldexists('cjdc_fxset','is_type')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `is_type` int(11) NOT NULL COMMENT '1.开启2.关闭(分类佣金)'");}
if(!pdo_fieldexists('cjdc_fxset','tx_rate')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `tx_rate` int(11) NOT NULL COMMENT '提现手续费'");}
if(!pdo_fieldexists('cjdc_fxset','dn_yj')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `dn_yj` varchar(10) NOT NULL COMMENT '店内一级佣金'");}
if(!pdo_fieldexists('cjdc_fxset','dn_ej')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `dn_ej` varchar(10) NOT NULL COMMENT '店内二级佣金'");}
if(!pdo_fieldexists('cjdc_fxset','wm_yj')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `wm_yj` varchar(10) NOT NULL COMMENT '外卖一级'");}
if(!pdo_fieldexists('cjdc_fxset','wm_ej')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `wm_ej` varchar(10) NOT NULL COMMENT '外卖二级'");}
if(!pdo_fieldexists('cjdc_fxset','tx_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `tx_money` int(11) NOT NULL COMMENT '提现门槛'");}
if(!pdo_fieldexists('cjdc_fxset','img')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `img` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_fxset','img2')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `img2` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_fxset','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_fxset','is_open')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `is_open` int(11) NOT NULL DEFAULT '1' COMMENT '1.开启2关闭'");}
if(!pdo_fieldexists('cjdc_fxset','instructions')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `instructions` text NOT NULL COMMENT '分销商说明'");}
if(!pdo_fieldexists('cjdc_fxset','fx_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `fx_name` varchar(30) NOT NULL DEFAULT '分销中心'");}
if(!pdo_fieldexists('cjdc_fxset','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `type` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_fxset','is_check')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `is_check` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_fxset','xx_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxset')." ADD   `xx_name` varchar(50) NOT NULL DEFAULT '我的伙伴'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_fxuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '一级分销',
  `fx_user` int(11) NOT NULL COMMENT '二级分销',
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_fxuser','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxuser')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_fxuser','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxuser')." ADD   `user_id` int(11) NOT NULL COMMENT '一级分销'");}
if(!pdo_fieldexists('cjdc_fxuser','fx_user')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxuser')." ADD   `fx_user` int(11) NOT NULL COMMENT '二级分销'");}
if(!pdo_fieldexists('cjdc_fxuser','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_fxuser')." ADD   `time` datetime NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '商品名称',
  `type_id` int(11) NOT NULL COMMENT '商品分类',
  `label_id` int(11) NOT NULL COMMENT '标签',
  `logo` varchar(200) NOT NULL,
  `money` decimal(10,2) NOT NULL COMMENT '售价',
  `money2` decimal(10,2) NOT NULL COMMENT '原价',
  `vip_money` decimal(10,2) NOT NULL COMMENT '会员价',
  `dn_money` decimal(10,2) NOT NULL,
  `is_show` int(11) NOT NULL DEFAULT '1' COMMENT '是否上架',
  `inventory` int(11) NOT NULL COMMENT '库存',
  `content` varchar(50) NOT NULL COMMENT '简介',
  `details` text NOT NULL COMMENT '详情',
  `sales` int(11) NOT NULL COMMENT '销量',
  `num` int(11) NOT NULL COMMENT '排序',
  `is_gg` int(11) NOT NULL DEFAULT '1',
  `is_hot` int(11) NOT NULL DEFAULT '2' COMMENT '是否热销',
  `is_zp` int(11) NOT NULL DEFAULT '2' COMMENT '是否招牌',
  `store_id` int(11) NOT NULL COMMENT '商家id',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `type` int(11) NOT NULL COMMENT '1.外卖2.店内3.店内+外卖',
  `quantity` int(11) NOT NULL,
  `box_money` decimal(10,2) NOT NULL COMMENT '餐盒费',
  `is_new` int(11) NOT NULL DEFAULT '2',
  `is_tj` int(11) NOT NULL DEFAULT '2',
  `restrict_num` int(11) NOT NULL,
  `start_num` int(11) NOT NULL,
  `dn_hymoney` decimal(10,2) NOT NULL,
  `is_max` int(1) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `type_id` (`type_id`),
  KEY `store_id_2` (`store_id`),
  KEY `type_id_2` (`type_id`),
  KEY `store_id_3` (`store_id`),
  KEY `type_id_3` (`type_id`),
  KEY `store_id_4` (`store_id`),
  KEY `type_id_4` (`type_id`),
  KEY `store_id_5` (`store_id`),
  KEY `type_id_5` (`type_id`),
  KEY `store_id_6` (`store_id`),
  KEY `type_id_6` (`type_id`),
  KEY `store_id_7` (`store_id`),
  KEY `type_id_7` (`type_id`),
  KEY `store_id_8` (`store_id`),
  KEY `type_id_8` (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_goods','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_goods','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `name` varchar(20) NOT NULL COMMENT '商品名称'");}
if(!pdo_fieldexists('cjdc_goods','type_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `type_id` int(11) NOT NULL COMMENT '商品分类'");}
if(!pdo_fieldexists('cjdc_goods','label_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `label_id` int(11) NOT NULL COMMENT '标签'");}
if(!pdo_fieldexists('cjdc_goods','logo')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `logo` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_goods','money')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `money` decimal(10,2) NOT NULL COMMENT '售价'");}
if(!pdo_fieldexists('cjdc_goods','money2')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `money2` decimal(10,2) NOT NULL COMMENT '原价'");}
if(!pdo_fieldexists('cjdc_goods','vip_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `vip_money` decimal(10,2) NOT NULL COMMENT '会员价'");}
if(!pdo_fieldexists('cjdc_goods','dn_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `dn_money` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_goods','is_show')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `is_show` int(11) NOT NULL DEFAULT '1' COMMENT '是否上架'");}
if(!pdo_fieldexists('cjdc_goods','inventory')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `inventory` int(11) NOT NULL COMMENT '库存'");}
if(!pdo_fieldexists('cjdc_goods','content')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `content` varchar(50) NOT NULL COMMENT '简介'");}
if(!pdo_fieldexists('cjdc_goods','details')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `details` text NOT NULL COMMENT '详情'");}
if(!pdo_fieldexists('cjdc_goods','sales')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `sales` int(11) NOT NULL COMMENT '销量'");}
if(!pdo_fieldexists('cjdc_goods','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `num` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_goods','is_gg')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `is_gg` int(11) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_goods','is_hot')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `is_hot` int(11) NOT NULL DEFAULT '2' COMMENT '是否热销'");}
if(!pdo_fieldexists('cjdc_goods','is_zp')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `is_zp` int(11) NOT NULL DEFAULT '2' COMMENT '是否招牌'");}
if(!pdo_fieldexists('cjdc_goods','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `store_id` int(11) NOT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('cjdc_goods','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('cjdc_goods','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `type` int(11) NOT NULL COMMENT '1.外卖2.店内3.店内+外卖'");}
if(!pdo_fieldexists('cjdc_goods','quantity')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `quantity` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_goods','box_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `box_money` decimal(10,2) NOT NULL COMMENT '餐盒费'");}
if(!pdo_fieldexists('cjdc_goods','is_new')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `is_new` int(11) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_goods','is_tj')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `is_tj` int(11) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_goods','restrict_num')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `restrict_num` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_goods','start_num')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `start_num` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_goods','dn_hymoney')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `dn_hymoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_goods','is_max')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   `is_max` int(1) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_goods','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('cjdc_goods','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   KEY `store_id` (`store_id`)");}
if(!pdo_fieldexists('cjdc_goods','type_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   KEY `type_id` (`type_id`)");}
if(!pdo_fieldexists('cjdc_goods','store_id_2')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   KEY `store_id_2` (`store_id`)");}
if(!pdo_fieldexists('cjdc_goods','type_id_2')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   KEY `type_id_2` (`type_id`)");}
if(!pdo_fieldexists('cjdc_goods','store_id_3')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   KEY `store_id_3` (`store_id`)");}
if(!pdo_fieldexists('cjdc_goods','type_id_3')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   KEY `type_id_3` (`type_id`)");}
if(!pdo_fieldexists('cjdc_goods','store_id_4')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   KEY `store_id_4` (`store_id`)");}
if(!pdo_fieldexists('cjdc_goods','type_id_4')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   KEY `type_id_4` (`type_id`)");}
if(!pdo_fieldexists('cjdc_goods','store_id_5')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   KEY `store_id_5` (`store_id`)");}
if(!pdo_fieldexists('cjdc_goods','type_id_5')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   KEY `type_id_5` (`type_id`)");}
if(!pdo_fieldexists('cjdc_goods','store_id_6')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   KEY `store_id_6` (`store_id`)");}
if(!pdo_fieldexists('cjdc_goods','type_id_6')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   KEY `type_id_6` (`type_id`)");}
if(!pdo_fieldexists('cjdc_goods','store_id_7')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   KEY `store_id_7` (`store_id`)");}
if(!pdo_fieldexists('cjdc_goods','type_id_7')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   KEY `type_id_7` (`type_id`)");}
if(!pdo_fieldexists('cjdc_goods','store_id_8')) {pdo_query("ALTER TABLE ".tablename('cjdc_goods')." ADD   KEY `store_id_8` (`store_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL COMMENT '门店id',
  `goods_id` int(11) NOT NULL COMMENT '商品ID',
  `goods_logo` varchar(255) NOT NULL,
  `goods_name` varchar(255) NOT NULL COMMENT '商品名称',
  `kt_num` int(11) NOT NULL COMMENT '开团人数',
  `yg_num` int(11) NOT NULL COMMENT '已购数量',
  `kt_time` int(11) NOT NULL COMMENT '开团时间',
  `dq_time` int(11) NOT NULL COMMENT '到期时间',
  `state` int(4) NOT NULL COMMENT '1.拼团中2成功,3失败',
  `user_id` int(11) NOT NULL COMMENT '团长user_id',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_group','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_group')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_group','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_group')." ADD   `store_id` int(11) NOT NULL COMMENT '门店id'");}
if(!pdo_fieldexists('cjdc_group','goods_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_group')." ADD   `goods_id` int(11) NOT NULL COMMENT '商品ID'");}
if(!pdo_fieldexists('cjdc_group','goods_logo')) {pdo_query("ALTER TABLE ".tablename('cjdc_group')." ADD   `goods_logo` varchar(255) NOT NULL");}
if(!pdo_fieldexists('cjdc_group','goods_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_group')." ADD   `goods_name` varchar(255) NOT NULL COMMENT '商品名称'");}
if(!pdo_fieldexists('cjdc_group','kt_num')) {pdo_query("ALTER TABLE ".tablename('cjdc_group')." ADD   `kt_num` int(11) NOT NULL COMMENT '开团人数'");}
if(!pdo_fieldexists('cjdc_group','yg_num')) {pdo_query("ALTER TABLE ".tablename('cjdc_group')." ADD   `yg_num` int(11) NOT NULL COMMENT '已购数量'");}
if(!pdo_fieldexists('cjdc_group','kt_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_group')." ADD   `kt_time` int(11) NOT NULL COMMENT '开团时间'");}
if(!pdo_fieldexists('cjdc_group','dq_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_group')." ADD   `dq_time` int(11) NOT NULL COMMENT '到期时间'");}
if(!pdo_fieldexists('cjdc_group','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_group')." ADD   `state` int(4) NOT NULL COMMENT '1.拼团中2成功,3失败'");}
if(!pdo_fieldexists('cjdc_group','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_group')." ADD   `user_id` int(11) NOT NULL COMMENT '团长user_id'");}
if(!pdo_fieldexists('cjdc_group','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_group')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_groupgoods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL COMMENT '门店id',
  `type_id` int(11) NOT NULL COMMENT '分类ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `logo` varchar(255) NOT NULL COMMENT 'logo',
  `img` text NOT NULL COMMENT '多图',
  `inventory` int(11) NOT NULL COMMENT '库存',
  `pt_price` decimal(10,2) NOT NULL COMMENT '拼团价格',
  `y_price` decimal(10,2) NOT NULL COMMENT '原价',
  `dd_price` decimal(10,2) NOT NULL COMMENT '单独购买价格',
  `ycd_num` int(11) NOT NULL COMMENT '已成团数量',
  `ysc_num` int(11) NOT NULL COMMENT '已售出数量',
  `people` int(11) NOT NULL COMMENT '成团人数',
  `start_time` int(11) NOT NULL COMMENT '开始时间',
  `end_time` int(11) NOT NULL COMMENT '结束时间',
  `xf_time` int(11) NOT NULL COMMENT '消费截止时间',
  `is_shelves` int(4) NOT NULL DEFAULT '1' COMMENT '1上架,2下架',
  `details` text NOT NULL COMMENT '商品详情',
  `num` int(11) NOT NULL COMMENT '排序',
  `display` int(4) NOT NULL DEFAULT '1' COMMENT '1显示,2隐藏',
  `uniacid` int(11) NOT NULL,
  `introduction` text NOT NULL,
  `time` int(11) NOT NULL DEFAULT '1532425107',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_groupgoods','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_groupgoods','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `store_id` int(11) NOT NULL COMMENT '门店id'");}
if(!pdo_fieldexists('cjdc_groupgoods','type_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `type_id` int(11) NOT NULL COMMENT '分类ID'");}
if(!pdo_fieldexists('cjdc_groupgoods','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `name` varchar(255) NOT NULL COMMENT '名称'");}
if(!pdo_fieldexists('cjdc_groupgoods','logo')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `logo` varchar(255) NOT NULL COMMENT 'logo'");}
if(!pdo_fieldexists('cjdc_groupgoods','img')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `img` text NOT NULL COMMENT '多图'");}
if(!pdo_fieldexists('cjdc_groupgoods','inventory')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `inventory` int(11) NOT NULL COMMENT '库存'");}
if(!pdo_fieldexists('cjdc_groupgoods','pt_price')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `pt_price` decimal(10,2) NOT NULL COMMENT '拼团价格'");}
if(!pdo_fieldexists('cjdc_groupgoods','y_price')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `y_price` decimal(10,2) NOT NULL COMMENT '原价'");}
if(!pdo_fieldexists('cjdc_groupgoods','dd_price')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `dd_price` decimal(10,2) NOT NULL COMMENT '单独购买价格'");}
if(!pdo_fieldexists('cjdc_groupgoods','ycd_num')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `ycd_num` int(11) NOT NULL COMMENT '已成团数量'");}
if(!pdo_fieldexists('cjdc_groupgoods','ysc_num')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `ysc_num` int(11) NOT NULL COMMENT '已售出数量'");}
if(!pdo_fieldexists('cjdc_groupgoods','people')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `people` int(11) NOT NULL COMMENT '成团人数'");}
if(!pdo_fieldexists('cjdc_groupgoods','start_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `start_time` int(11) NOT NULL COMMENT '开始时间'");}
if(!pdo_fieldexists('cjdc_groupgoods','end_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `end_time` int(11) NOT NULL COMMENT '结束时间'");}
if(!pdo_fieldexists('cjdc_groupgoods','xf_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `xf_time` int(11) NOT NULL COMMENT '消费截止时间'");}
if(!pdo_fieldexists('cjdc_groupgoods','is_shelves')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `is_shelves` int(4) NOT NULL DEFAULT '1' COMMENT '1上架,2下架'");}
if(!pdo_fieldexists('cjdc_groupgoods','details')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `details` text NOT NULL COMMENT '商品详情'");}
if(!pdo_fieldexists('cjdc_groupgoods','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `num` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_groupgoods','display')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `display` int(4) NOT NULL DEFAULT '1' COMMENT '1显示,2隐藏'");}
if(!pdo_fieldexists('cjdc_groupgoods','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_groupgoods','introduction')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `introduction` text NOT NULL");}
if(!pdo_fieldexists('cjdc_groupgoods','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_groupgoods')." ADD   `time` int(11) NOT NULL DEFAULT '1532425107'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_grouphx` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL COMMENT '门店id',
  `hx_id` int(11) NOT NULL COMMENT '核销人id',
  `time` int(11) NOT NULL COMMENT '时间',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='核销表';

");

if(!pdo_fieldexists('cjdc_grouphx','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouphx')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_grouphx','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouphx')." ADD   `store_id` int(11) NOT NULL COMMENT '门店id'");}
if(!pdo_fieldexists('cjdc_grouphx','hx_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouphx')." ADD   `hx_id` int(11) NOT NULL COMMENT '核销人id'");}
if(!pdo_fieldexists('cjdc_grouphx','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouphx')." ADD   `time` int(11) NOT NULL COMMENT '时间'");}
if(!pdo_fieldexists('cjdc_grouphx','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouphx')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_grouporder` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL COMMENT '团id',
  `store_id` int(11) NOT NULL COMMENT '门店id',
  `goods_id` int(11) NOT NULL COMMENT '商品ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `order_num` varchar(30) NOT NULL COMMENT '订单号',
  `logo` varchar(255) NOT NULL COMMENT '商品图片',
  `goods_name` varchar(255) NOT NULL COMMENT '商品名称',
  `goods_type` varchar(50) NOT NULL COMMENT '商品类型',
  `price` decimal(10,2) NOT NULL COMMENT '单价',
  `goods_num` int(11) NOT NULL COMMENT '商品数量',
  `money` decimal(10,2) NOT NULL COMMENT '订单金额',
  `pay_type` int(4) NOT NULL COMMENT '付款方式1微信，2余额',
  `receive_name` varchar(30) NOT NULL COMMENT '收货人',
  `receive_tel` varchar(20) NOT NULL COMMENT '收货人电话',
  `receive_address` varchar(100) NOT NULL COMMENT '收货人地址',
  `note` varchar(100) NOT NULL COMMENT '备注',
  `state` int(4) NOT NULL COMMENT '1未付款,2已付款,3已完成,4已关闭,5已失效',
  `xf_time` int(11) NOT NULL COMMENT '消费截止时间',
  `time` int(11) NOT NULL COMMENT '下单时间',
  `pay_time` int(11) NOT NULL COMMENT '付款时间',
  `cz_time` int(11) NOT NULL COMMENT '完成/关闭/失效时间',
  `code` varchar(30) NOT NULL COMMENT '支付商户号',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_grouporder','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_grouporder','group_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `group_id` int(11) NOT NULL COMMENT '团id'");}
if(!pdo_fieldexists('cjdc_grouporder','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `store_id` int(11) NOT NULL COMMENT '门店id'");}
if(!pdo_fieldexists('cjdc_grouporder','goods_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `goods_id` int(11) NOT NULL COMMENT '商品ID'");}
if(!pdo_fieldexists('cjdc_grouporder','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `user_id` int(11) NOT NULL COMMENT '用户ID'");}
if(!pdo_fieldexists('cjdc_grouporder','order_num')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `order_num` varchar(30) NOT NULL COMMENT '订单号'");}
if(!pdo_fieldexists('cjdc_grouporder','logo')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `logo` varchar(255) NOT NULL COMMENT '商品图片'");}
if(!pdo_fieldexists('cjdc_grouporder','goods_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `goods_name` varchar(255) NOT NULL COMMENT '商品名称'");}
if(!pdo_fieldexists('cjdc_grouporder','goods_type')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `goods_type` varchar(50) NOT NULL COMMENT '商品类型'");}
if(!pdo_fieldexists('cjdc_grouporder','price')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `price` decimal(10,2) NOT NULL COMMENT '单价'");}
if(!pdo_fieldexists('cjdc_grouporder','goods_num')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `goods_num` int(11) NOT NULL COMMENT '商品数量'");}
if(!pdo_fieldexists('cjdc_grouporder','money')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `money` decimal(10,2) NOT NULL COMMENT '订单金额'");}
if(!pdo_fieldexists('cjdc_grouporder','pay_type')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `pay_type` int(4) NOT NULL COMMENT '付款方式1微信，2余额'");}
if(!pdo_fieldexists('cjdc_grouporder','receive_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `receive_name` varchar(30) NOT NULL COMMENT '收货人'");}
if(!pdo_fieldexists('cjdc_grouporder','receive_tel')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `receive_tel` varchar(20) NOT NULL COMMENT '收货人电话'");}
if(!pdo_fieldexists('cjdc_grouporder','receive_address')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `receive_address` varchar(100) NOT NULL COMMENT '收货人地址'");}
if(!pdo_fieldexists('cjdc_grouporder','note')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `note` varchar(100) NOT NULL COMMENT '备注'");}
if(!pdo_fieldexists('cjdc_grouporder','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `state` int(4) NOT NULL COMMENT '1未付款,2已付款,3已完成,4已关闭,5已失效'");}
if(!pdo_fieldexists('cjdc_grouporder','xf_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `xf_time` int(11) NOT NULL COMMENT '消费截止时间'");}
if(!pdo_fieldexists('cjdc_grouporder','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `time` int(11) NOT NULL COMMENT '下单时间'");}
if(!pdo_fieldexists('cjdc_grouporder','pay_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `pay_time` int(11) NOT NULL COMMENT '付款时间'");}
if(!pdo_fieldexists('cjdc_grouporder','cz_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `cz_time` int(11) NOT NULL COMMENT '完成/关闭/失效时间'");}
if(!pdo_fieldexists('cjdc_grouporder','code')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `code` varchar(30) NOT NULL COMMENT '支付商户号'");}
if(!pdo_fieldexists('cjdc_grouporder','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouporder')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_grouptype` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `img` varchar(100) NOT NULL,
  `num` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='拼团分类';

");

if(!pdo_fieldexists('cjdc_grouptype','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouptype')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_grouptype','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouptype')." ADD   `name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_grouptype','img')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouptype')." ADD   `img` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_grouptype','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouptype')." ADD   `num` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_grouptype','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_grouptype')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_help` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(200) NOT NULL COMMENT '标题',
  `answer` text NOT NULL COMMENT '回答',
  `sort` int(4) NOT NULL COMMENT '排序',
  `uniacid` varchar(50) NOT NULL,
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_help','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_help')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_help','question')) {pdo_query("ALTER TABLE ".tablename('cjdc_help')." ADD   `question` varchar(200) NOT NULL COMMENT '标题'");}
if(!pdo_fieldexists('cjdc_help','answer')) {pdo_query("ALTER TABLE ".tablename('cjdc_help')." ADD   `answer` text NOT NULL COMMENT '回答'");}
if(!pdo_fieldexists('cjdc_help','sort')) {pdo_query("ALTER TABLE ".tablename('cjdc_help')." ADD   `sort` int(4) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_help','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_help')." ADD   `uniacid` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_help','created_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_help')." ADD   `created_time` datetime NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_hyorder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `money` decimal(10,2) NOT NULL,
  `day` int(11) NOT NULL COMMENT '开通号数',
  `month` int(11) NOT NULL COMMENT '开通期限',
  `time` varchar(20) NOT NULL COMMENT '开通时间',
  `pay_type` int(11) NOT NULL COMMENT '1.微信2.余额',
  `state` int(11) NOT NULL COMMENT '1.待支付2.已支付',
  `code` varchar(100) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `user_tel` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_hyorder','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_hyorder')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_hyorder','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_hyorder')." ADD   `user_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_hyorder','money')) {pdo_query("ALTER TABLE ".tablename('cjdc_hyorder')." ADD   `money` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_hyorder','day')) {pdo_query("ALTER TABLE ".tablename('cjdc_hyorder')." ADD   `day` int(11) NOT NULL COMMENT '开通号数'");}
if(!pdo_fieldexists('cjdc_hyorder','month')) {pdo_query("ALTER TABLE ".tablename('cjdc_hyorder')." ADD   `month` int(11) NOT NULL COMMENT '开通期限'");}
if(!pdo_fieldexists('cjdc_hyorder','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_hyorder')." ADD   `time` varchar(20) NOT NULL COMMENT '开通时间'");}
if(!pdo_fieldexists('cjdc_hyorder','pay_type')) {pdo_query("ALTER TABLE ".tablename('cjdc_hyorder')." ADD   `pay_type` int(11) NOT NULL COMMENT '1.微信2.余额'");}
if(!pdo_fieldexists('cjdc_hyorder','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_hyorder')." ADD   `state` int(11) NOT NULL COMMENT '1.待支付2.已支付'");}
if(!pdo_fieldexists('cjdc_hyorder','code')) {pdo_query("ALTER TABLE ".tablename('cjdc_hyorder')." ADD   `code` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_hyorder','user_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_hyorder')." ADD   `user_name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_hyorder','user_tel')) {pdo_query("ALTER TABLE ".tablename('cjdc_hyorder')." ADD   `user_tel` varchar(20) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_hyqx` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `days` int(11) NOT NULL,
  `money` decimal(10,2) NOT NULL,
  `num` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_hyqx','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_hyqx')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_hyqx','days')) {pdo_query("ALTER TABLE ".tablename('cjdc_hyqx')." ADD   `days` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_hyqx','money')) {pdo_query("ALTER TABLE ".tablename('cjdc_hyqx')." ADD   `money` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_hyqx','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_hyqx')." ADD   `num` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_hyqx','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_hyqx')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_integral` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `score` int(11) NOT NULL COMMENT '分数',
  `type` int(4) NOT NULL COMMENT '1加,2减',
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `cerated_time` datetime NOT NULL COMMENT '创建时间',
  `uniacid` varchar(50) NOT NULL,
  `note` varchar(20) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='入驻记录';

");

if(!pdo_fieldexists('cjdc_integral','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_integral')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_integral','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_integral')." ADD   `user_id` int(11) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('cjdc_integral','score')) {pdo_query("ALTER TABLE ".tablename('cjdc_integral')." ADD   `score` int(11) NOT NULL COMMENT '分数'");}
if(!pdo_fieldexists('cjdc_integral','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_integral')." ADD   `type` int(4) NOT NULL COMMENT '1加,2减'");}
if(!pdo_fieldexists('cjdc_integral','order_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_integral')." ADD   `order_id` int(11) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('cjdc_integral','cerated_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_integral')." ADD   `cerated_time` datetime NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('cjdc_integral','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_integral')." ADD   `uniacid` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_integral','note')) {pdo_query("ALTER TABLE ".tablename('cjdc_integral')." ADD   `note` varchar(20) NOT NULL COMMENT '备注'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_jfgoods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名称',
  `img` varchar(100) NOT NULL,
  `money` int(11) NOT NULL COMMENT '价格',
  `type_id` int(11) NOT NULL COMMENT '分类id',
  `goods_details` text NOT NULL,
  `process_details` text NOT NULL,
  `attention_details` text NOT NULL,
  `number` int(11) NOT NULL COMMENT '数量',
  `time` varchar(50) NOT NULL COMMENT '期限',
  `is_open` int(11) NOT NULL COMMENT '1.开启2关闭',
  `type` int(11) NOT NULL COMMENT '1.余额2.实物',
  `num` int(11) NOT NULL COMMENT '排序',
  `uniacid` int(11) NOT NULL,
  `hb_moeny` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='入驻记录';

");

if(!pdo_fieldexists('cjdc_jfgoods','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfgoods')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_jfgoods','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfgoods')." ADD   `name` varchar(50) NOT NULL COMMENT '名称'");}
if(!pdo_fieldexists('cjdc_jfgoods','img')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfgoods')." ADD   `img` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_jfgoods','money')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfgoods')." ADD   `money` int(11) NOT NULL COMMENT '价格'");}
if(!pdo_fieldexists('cjdc_jfgoods','type_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfgoods')." ADD   `type_id` int(11) NOT NULL COMMENT '分类id'");}
if(!pdo_fieldexists('cjdc_jfgoods','goods_details')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfgoods')." ADD   `goods_details` text NOT NULL");}
if(!pdo_fieldexists('cjdc_jfgoods','process_details')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfgoods')." ADD   `process_details` text NOT NULL");}
if(!pdo_fieldexists('cjdc_jfgoods','attention_details')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfgoods')." ADD   `attention_details` text NOT NULL");}
if(!pdo_fieldexists('cjdc_jfgoods','number')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfgoods')." ADD   `number` int(11) NOT NULL COMMENT '数量'");}
if(!pdo_fieldexists('cjdc_jfgoods','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfgoods')." ADD   `time` varchar(50) NOT NULL COMMENT '期限'");}
if(!pdo_fieldexists('cjdc_jfgoods','is_open')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfgoods')." ADD   `is_open` int(11) NOT NULL COMMENT '1.开启2关闭'");}
if(!pdo_fieldexists('cjdc_jfgoods','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfgoods')." ADD   `type` int(11) NOT NULL COMMENT '1.余额2.实物'");}
if(!pdo_fieldexists('cjdc_jfgoods','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfgoods')." ADD   `num` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_jfgoods','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfgoods')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_jfgoods','hb_moeny')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfgoods')." ADD   `hb_moeny` decimal(10,2) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_jfrecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `good_id` int(11) NOT NULL COMMENT '商品id',
  `time` varchar(20) NOT NULL COMMENT '兑换时间',
  `user_name` varchar(20) NOT NULL COMMENT '用户地址',
  `user_tel` varchar(20) NOT NULL COMMENT '用户电话',
  `address` varchar(200) NOT NULL COMMENT '地址',
  `note` varchar(20) NOT NULL,
  `integral` int(11) NOT NULL COMMENT '积分',
  `good_name` varchar(50) NOT NULL COMMENT '商品名称',
  `good_img` varchar(100) NOT NULL,
  `state` int(11) NOT NULL DEFAULT '2' COMMENT '1.未处理 2.已处理',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='入驻记录';

");

if(!pdo_fieldexists('cjdc_jfrecord','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfrecord')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_jfrecord','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfrecord')." ADD   `user_id` int(11) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('cjdc_jfrecord','good_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfrecord')." ADD   `good_id` int(11) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('cjdc_jfrecord','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfrecord')." ADD   `time` varchar(20) NOT NULL COMMENT '兑换时间'");}
if(!pdo_fieldexists('cjdc_jfrecord','user_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfrecord')." ADD   `user_name` varchar(20) NOT NULL COMMENT '用户地址'");}
if(!pdo_fieldexists('cjdc_jfrecord','user_tel')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfrecord')." ADD   `user_tel` varchar(20) NOT NULL COMMENT '用户电话'");}
if(!pdo_fieldexists('cjdc_jfrecord','address')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfrecord')." ADD   `address` varchar(200) NOT NULL COMMENT '地址'");}
if(!pdo_fieldexists('cjdc_jfrecord','note')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfrecord')." ADD   `note` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_jfrecord','integral')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfrecord')." ADD   `integral` int(11) NOT NULL COMMENT '积分'");}
if(!pdo_fieldexists('cjdc_jfrecord','good_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfrecord')." ADD   `good_name` varchar(50) NOT NULL COMMENT '商品名称'");}
if(!pdo_fieldexists('cjdc_jfrecord','good_img')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfrecord')." ADD   `good_img` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_jfrecord','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_jfrecord')." ADD   `state` int(11) NOT NULL DEFAULT '2' COMMENT '1.未处理 2.已处理'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_jftype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `img` varchar(100) NOT NULL,
  `num` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='入驻记录';

");

if(!pdo_fieldexists('cjdc_jftype','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_jftype')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_jftype','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_jftype')." ADD   `name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_jftype','img')) {pdo_query("ALTER TABLE ".tablename('cjdc_jftype')." ADD   `img` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_jftype','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_jftype')." ADD   `num` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_jftype','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_jftype')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_kfwset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL COMMENT '门店ID',
  `user_id` int(11) NOT NULL COMMENT '商户id',
  `access_token` varchar(50) NOT NULL COMMENT '用户授权token',
  `openid` varchar(20) NOT NULL COMMENT '新商户ID',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='快服务设置表';

");

if(!pdo_fieldexists('cjdc_kfwset','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_kfwset')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_kfwset','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_kfwset')." ADD   `store_id` int(11) NOT NULL COMMENT '门店ID'");}
if(!pdo_fieldexists('cjdc_kfwset','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_kfwset')." ADD   `user_id` int(11) NOT NULL COMMENT '商户id'");}
if(!pdo_fieldexists('cjdc_kfwset','access_token')) {pdo_query("ALTER TABLE ".tablename('cjdc_kfwset')." ADD   `access_token` varchar(50) NOT NULL COMMENT '用户授权token'");}
if(!pdo_fieldexists('cjdc_kfwset','openid')) {pdo_query("ALTER TABLE ".tablename('cjdc_kfwset')." ADD   `openid` varchar(20) NOT NULL COMMENT '新商户ID'");}
if(!pdo_fieldexists('cjdc_kfwset','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_kfwset')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_llz` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `type` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `src` varchar(100) NOT NULL,
  `cityname` varchar(20) NOT NULL,
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='拼团分类';

");

if(!pdo_fieldexists('cjdc_llz','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_llz')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_llz','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_llz')." ADD   `name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_llz','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_llz')." ADD   `type` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_llz','status')) {pdo_query("ALTER TABLE ".tablename('cjdc_llz')." ADD   `status` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_llz','src')) {pdo_query("ALTER TABLE ".tablename('cjdc_llz')." ADD   `src` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_llz','cityname')) {pdo_query("ALTER TABLE ".tablename('cjdc_llz')." ADD   `cityname` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_llz','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_llz')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL COMMENT '邮箱用户名',
  `password` varchar(50) NOT NULL COMMENT '邮箱密码',
  `type` varchar(10) NOT NULL COMMENT '发件人名称',
  `sender` varchar(20) NOT NULL COMMENT '发件人名称',
  `signature` varchar(100) NOT NULL COMMENT '邮箱签名',
  `is_email` int(11) NOT NULL COMMENT '1.开启2.关闭',
  `xd_tid` varchar(60) NOT NULL COMMENT '下单通知',
  `jd_tid` varchar(60) NOT NULL COMMENT '接单通知',
  `yy_tid` varchar(60) NOT NULL COMMENT '预约通知',
  `dm_tid` varchar(60) NOT NULL COMMENT '当面通知',
  `sj_tid` varchar(60) NOT NULL COMMENT '公众号商家新订单通知',
  `sj_tid2` varchar(60) NOT NULL COMMENT '公众号商家预约通知',
  `wx_appid` varchar(60) NOT NULL COMMENT '通知公众号appid',
  `wx_secret` varchar(60) NOT NULL COMMENT '通知公众号AppSecret',
  `is_dxyz` int(4) NOT NULL DEFAULT '2' COMMENT '是否开启短信1是,2否',
  `appkey` varchar(50) NOT NULL COMMENT '应用key',
  `tpl_id` varchar(20) NOT NULL COMMENT '短信模板id',
  `uniacid` int(11) NOT NULL COMMENT '小程序id ',
  `jj_tid` varchar(200) NOT NULL,
  `tk_tid` varchar(200) NOT NULL,
  `rzsh_tid` varchar(60) NOT NULL,
  `rzcg_tid` varchar(60) NOT NULL,
  `rzjj_tid` varchar(60) NOT NULL,
  `cz_tid` varchar(100) NOT NULL,
  `xdd_tid` varchar(200) NOT NULL,
  `xdd_tid2` varchar(200) NOT NULL,
  `qf_tid` varchar(200) NOT NULL,
  `qh_tid` varchar(100) NOT NULL,
  `item` int(4) NOT NULL DEFAULT '1',
  `appid` varchar(20) NOT NULL,
  `tx_appkey` varchar(50) NOT NULL,
  `template_id` varchar(10) NOT NULL,
  `sign` varchar(200) NOT NULL,
  `code` varchar(10) NOT NULL DEFAULT '86',
  `sjyy_tid` varchar(60) NOT NULL,
  `shtk_tid` varchar(50) NOT NULL,
  `aliyun_appkey` varchar(20) NOT NULL,
  `aliyun_appsecret` varchar(50) NOT NULL,
  `aliyun_sign` varchar(20) NOT NULL,
  `aliyun_id` varchar(20) NOT NULL,
  `group_tid` varchar(50) NOT NULL,
  `rush_tid` varchar(50) NOT NULL,
  `wechat_appId` varchar(50) NOT NULL,
  `wechat_appsecret` varchar(100) NOT NULL,
  `wechat_wm_tid` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='通知表';

");

if(!pdo_fieldexists('cjdc_message','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_message','username')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `username` varchar(20) NOT NULL COMMENT '邮箱用户名'");}
if(!pdo_fieldexists('cjdc_message','password')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `password` varchar(50) NOT NULL COMMENT '邮箱密码'");}
if(!pdo_fieldexists('cjdc_message','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `type` varchar(10) NOT NULL COMMENT '发件人名称'");}
if(!pdo_fieldexists('cjdc_message','sender')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `sender` varchar(20) NOT NULL COMMENT '发件人名称'");}
if(!pdo_fieldexists('cjdc_message','signature')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `signature` varchar(100) NOT NULL COMMENT '邮箱签名'");}
if(!pdo_fieldexists('cjdc_message','is_email')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `is_email` int(11) NOT NULL COMMENT '1.开启2.关闭'");}
if(!pdo_fieldexists('cjdc_message','xd_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `xd_tid` varchar(60) NOT NULL COMMENT '下单通知'");}
if(!pdo_fieldexists('cjdc_message','jd_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `jd_tid` varchar(60) NOT NULL COMMENT '接单通知'");}
if(!pdo_fieldexists('cjdc_message','yy_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `yy_tid` varchar(60) NOT NULL COMMENT '预约通知'");}
if(!pdo_fieldexists('cjdc_message','dm_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `dm_tid` varchar(60) NOT NULL COMMENT '当面通知'");}
if(!pdo_fieldexists('cjdc_message','sj_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `sj_tid` varchar(60) NOT NULL COMMENT '公众号商家新订单通知'");}
if(!pdo_fieldexists('cjdc_message','sj_tid2')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `sj_tid2` varchar(60) NOT NULL COMMENT '公众号商家预约通知'");}
if(!pdo_fieldexists('cjdc_message','wx_appid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `wx_appid` varchar(60) NOT NULL COMMENT '通知公众号appid'");}
if(!pdo_fieldexists('cjdc_message','wx_secret')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `wx_secret` varchar(60) NOT NULL COMMENT '通知公众号AppSecret'");}
if(!pdo_fieldexists('cjdc_message','is_dxyz')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `is_dxyz` int(4) NOT NULL DEFAULT '2' COMMENT '是否开启短信1是,2否'");}
if(!pdo_fieldexists('cjdc_message','appkey')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `appkey` varchar(50) NOT NULL COMMENT '应用key'");}
if(!pdo_fieldexists('cjdc_message','tpl_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `tpl_id` varchar(20) NOT NULL COMMENT '短信模板id'");}
if(!pdo_fieldexists('cjdc_message','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id '");}
if(!pdo_fieldexists('cjdc_message','jj_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `jj_tid` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','tk_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `tk_tid` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','rzsh_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `rzsh_tid` varchar(60) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','rzcg_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `rzcg_tid` varchar(60) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','rzjj_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `rzjj_tid` varchar(60) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','cz_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `cz_tid` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','xdd_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `xdd_tid` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','xdd_tid2')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `xdd_tid2` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','qf_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `qf_tid` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','qh_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `qh_tid` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','item')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `item` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_message','appid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `appid` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','tx_appkey')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `tx_appkey` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','template_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `template_id` varchar(10) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','sign')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `sign` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','code')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `code` varchar(10) NOT NULL DEFAULT '86'");}
if(!pdo_fieldexists('cjdc_message','sjyy_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `sjyy_tid` varchar(60) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','shtk_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `shtk_tid` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','aliyun_appkey')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `aliyun_appkey` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','aliyun_appsecret')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `aliyun_appsecret` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','aliyun_sign')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `aliyun_sign` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','aliyun_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `aliyun_id` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','group_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `group_tid` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','rush_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `rush_tid` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','wechat_appId')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `wechat_appId` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','wechat_appsecret')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `wechat_appsecret` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_message','wechat_wm_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message')." ADD   `wechat_wm_tid` varchar(100) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_message2` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `note` varchar(100) NOT NULL,
  `source` varchar(100) NOT NULL,
  `content` varchar(100) NOT NULL,
  `time` varchar(100) NOT NULL,
  `fs_time` varchar(20) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `src` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_message2','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_message2')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_message2','note')) {pdo_query("ALTER TABLE ".tablename('cjdc_message2')." ADD   `note` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_message2','source')) {pdo_query("ALTER TABLE ".tablename('cjdc_message2')." ADD   `source` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_message2','content')) {pdo_query("ALTER TABLE ".tablename('cjdc_message2')." ADD   `content` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_message2','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_message2')." ADD   `time` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_message2','fs_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_message2')." ADD   `fs_time` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_message2','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_message2')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_message2','src')) {pdo_query("ALTER TABLE ".tablename('cjdc_message2')." ADD   `src` varchar(100) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_nav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL COMMENT '标题',
  `title_color` varchar(20) NOT NULL COMMENT '标题选中颜色',
  `title_color2` varchar(20) NOT NULL COMMENT '标题未选中颜色',
  `logo` varchar(200) NOT NULL COMMENT '选中图片',
  `logo2` varchar(200) NOT NULL COMMENT '未选中图片',
  `url` varchar(200) NOT NULL COMMENT '跳转链接',
  `num` int(11) NOT NULL COMMENT '排序',
  `state` int(11) NOT NULL DEFAULT '1' COMMENT '1开启2关闭',
  `uniacid` int(11) NOT NULL,
  `xcx_name` varchar(50) NOT NULL,
  `appid` varchar(30) NOT NULL,
  `item` int(4) NOT NULL DEFAULT '1',
  `src2` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_nav','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_nav')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_nav','title')) {pdo_query("ALTER TABLE ".tablename('cjdc_nav')." ADD   `title` varchar(20) NOT NULL COMMENT '标题'");}
if(!pdo_fieldexists('cjdc_nav','title_color')) {pdo_query("ALTER TABLE ".tablename('cjdc_nav')." ADD   `title_color` varchar(20) NOT NULL COMMENT '标题选中颜色'");}
if(!pdo_fieldexists('cjdc_nav','title_color2')) {pdo_query("ALTER TABLE ".tablename('cjdc_nav')." ADD   `title_color2` varchar(20) NOT NULL COMMENT '标题未选中颜色'");}
if(!pdo_fieldexists('cjdc_nav','logo')) {pdo_query("ALTER TABLE ".tablename('cjdc_nav')." ADD   `logo` varchar(200) NOT NULL COMMENT '选中图片'");}
if(!pdo_fieldexists('cjdc_nav','logo2')) {pdo_query("ALTER TABLE ".tablename('cjdc_nav')." ADD   `logo2` varchar(200) NOT NULL COMMENT '未选中图片'");}
if(!pdo_fieldexists('cjdc_nav','url')) {pdo_query("ALTER TABLE ".tablename('cjdc_nav')." ADD   `url` varchar(200) NOT NULL COMMENT '跳转链接'");}
if(!pdo_fieldexists('cjdc_nav','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_nav')." ADD   `num` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_nav','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_nav')." ADD   `state` int(11) NOT NULL DEFAULT '1' COMMENT '1开启2关闭'");}
if(!pdo_fieldexists('cjdc_nav','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_nav')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_nav','xcx_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_nav')." ADD   `xcx_name` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_nav','appid')) {pdo_query("ALTER TABLE ".tablename('cjdc_nav')." ADD   `appid` varchar(30) NOT NULL");}
if(!pdo_fieldexists('cjdc_nav','item')) {pdo_query("ALTER TABLE ".tablename('cjdc_nav')." ADD   `item` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_nav','src2')) {pdo_query("ALTER TABLE ".tablename('cjdc_nav')." ADD   `src2` varchar(255) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_number` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `store_id` int(11) NOT NULL COMMENT '商家ID',
  `code` varchar(10) NOT NULL COMMENT '编号',
  `num` varchar(30) NOT NULL COMMENT '餐桌名称',
  `people` int(4) NOT NULL COMMENT '就餐人数',
  `state` int(4) NOT NULL COMMENT '1排队,2用餐,3作废',
  `time` datetime NOT NULL COMMENT '时间',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='排队取号';

");

if(!pdo_fieldexists('cjdc_number','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_number')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_number','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_number')." ADD   `user_id` int(11) NOT NULL COMMENT '用户ID'");}
if(!pdo_fieldexists('cjdc_number','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_number')." ADD   `store_id` int(11) NOT NULL COMMENT '商家ID'");}
if(!pdo_fieldexists('cjdc_number','code')) {pdo_query("ALTER TABLE ".tablename('cjdc_number')." ADD   `code` varchar(10) NOT NULL COMMENT '编号'");}
if(!pdo_fieldexists('cjdc_number','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_number')." ADD   `num` varchar(30) NOT NULL COMMENT '餐桌名称'");}
if(!pdo_fieldexists('cjdc_number','people')) {pdo_query("ALTER TABLE ".tablename('cjdc_number')." ADD   `people` int(4) NOT NULL COMMENT '就餐人数'");}
if(!pdo_fieldexists('cjdc_number','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_number')." ADD   `state` int(4) NOT NULL COMMENT '1排队,2用餐,3作废'");}
if(!pdo_fieldexists('cjdc_number','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_number')." ADD   `time` datetime NOT NULL COMMENT '时间'");}
if(!pdo_fieldexists('cjdc_number','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_number')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_numbertype` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `typename` varchar(30) NOT NULL COMMENT '分类名称',
  `sort` int(4) NOT NULL COMMENT '排序',
  `time` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='排队分类';

");

if(!pdo_fieldexists('cjdc_numbertype','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_numbertype')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_numbertype','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_numbertype')." ADD   `store_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_numbertype','typename')) {pdo_query("ALTER TABLE ".tablename('cjdc_numbertype')." ADD   `typename` varchar(30) NOT NULL COMMENT '分类名称'");}
if(!pdo_fieldexists('cjdc_numbertype','sort')) {pdo_query("ALTER TABLE ".tablename('cjdc_numbertype')." ADD   `sort` int(4) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_numbertype','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_numbertype')." ADD   `time` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_numbertype','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_numbertype')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `order_num` varchar(20) NOT NULL COMMENT '订单号',
  `state` int(11) NOT NULL COMMENT '1.待付款2.待结单3.等待送达4.完成5.已评价6.取消7.拒绝8.退款中9.已退款10.退款拒绝',
  `time` varchar(20) NOT NULL COMMENT '下单时间',
  `pay_time` varchar(20) NOT NULL COMMENT '支付时间',
  `jd_time` varchar(20) NOT NULL COMMENT '接单时间',
  `cancel_time` varchar(20) NOT NULL COMMENT '取消时间',
  `complete_time` varchar(20) NOT NULL COMMENT '完成时间',
  `money` decimal(10,2) NOT NULL COMMENT '付款金额',
  `box_money` decimal(10,2) NOT NULL COMMENT '餐盒费',
  `ps_money` decimal(10,2) NOT NULL COMMENT '配送费',
  `mj_money` decimal(10,2) NOT NULL COMMENT '满减优惠',
  `xyh_money` decimal(10,2) NOT NULL COMMENT '新用户立减',
  `tel` varchar(20) NOT NULL COMMENT '电话',
  `name` varchar(20) NOT NULL COMMENT '姓名',
  `address` varchar(200) NOT NULL COMMENT '地址',
  `type` int(11) NOT NULL COMMENT '1.外卖2.店内3.预定4.当面付',
  `store_id` int(11) NOT NULL COMMENT '商家id',
  `note` varchar(50) NOT NULL COMMENT '备注',
  `jj_note` varchar(50) NOT NULL COMMENT '拒绝理由',
  `area` varchar(20) NOT NULL COMMENT '区域',
  `lat` varchar(20) NOT NULL COMMENT '经度',
  `lng` varchar(20) NOT NULL COMMENT '纬度',
  `del` int(11) NOT NULL DEFAULT '2' COMMENT '1.删除  2.未删除',
  `pay_type` int(11) NOT NULL COMMENT '1.微信支付2.余额支付3.积分支付4.货到付款',
  `form_id` varchar(50) NOT NULL COMMENT '模板消息form_id',
  `form_id2` varchar(50) NOT NULL COMMENT '发货formid',
  `code` varchar(100) NOT NULL COMMENT '支付code',
  `order_type` int(11) NOT NULL COMMENT '1.配送2.到店自取',
  `delivery_time` varchar(20) NOT NULL COMMENT '送达时间',
  `sex` int(11) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `tableware` int(11) NOT NULL COMMENT '餐具',
  `dd_info` text NOT NULL COMMENT '达达信息',
  `uniacid` int(11) NOT NULL,
  `yhq_money` decimal(10,2) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `yhq_money2` decimal(10,2) NOT NULL,
  `coupon_id2` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `dn_state` int(11) NOT NULL,
  `dm_state` int(4) NOT NULL,
  `yy_state` int(11) NOT NULL,
  `deposit` decimal(10,2) NOT NULL,
  `ship_id` varchar(30) NOT NULL,
  `zk_money` decimal(10,2) NOT NULL,
  `is_dd` int(11) NOT NULL DEFAULT '2',
  `pt_info` text NOT NULL,
  `kfw_info` text NOT NULL,
  `hb_type` int(4) NOT NULL DEFAULT '1',
  `original_money` decimal(10,2) NOT NULL,
  `oid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `store_id` (`store_id`),
  KEY `state` (`state`),
  KEY `order_type` (`order_type`),
  KEY `uniacid` (`uniacid`),
  KEY `user_id_2` (`user_id`),
  KEY `store_id_2` (`store_id`),
  KEY `state_2` (`state`),
  KEY `order_type_2` (`order_type`),
  KEY `uniacid_2` (`uniacid`),
  KEY `user_id_3` (`user_id`),
  KEY `store_id_3` (`store_id`),
  KEY `state_3` (`state`),
  KEY `order_type_3` (`order_type`),
  KEY `uniacid_3` (`uniacid`),
  KEY `user_id_4` (`user_id`),
  KEY `store_id_4` (`store_id`),
  KEY `state_4` (`state`),
  KEY `order_type_4` (`order_type`),
  KEY `uniacid_4` (`uniacid`),
  KEY `user_id_5` (`user_id`),
  KEY `store_id_5` (`store_id`),
  KEY `state_5` (`state`),
  KEY `order_type_5` (`order_type`),
  KEY `uniacid_5` (`uniacid`),
  KEY `user_id_6` (`user_id`),
  KEY `store_id_6` (`store_id`),
  KEY `state_6` (`state`),
  KEY `order_type_6` (`order_type`),
  KEY `uniacid_6` (`uniacid`),
  KEY `user_id_7` (`user_id`),
  KEY `store_id_7` (`store_id`),
  KEY `state_7` (`state`),
  KEY `order_type_7` (`order_type`),
  KEY `uniacid_7` (`uniacid`),
  KEY `user_id_8` (`user_id`),
  KEY `store_id_8` (`store_id`),
  KEY `state_8` (`state`),
  KEY `order_type_8` (`order_type`),
  KEY `uniacid_8` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_order','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_order','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `user_id` int(11) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('cjdc_order','order_num')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `order_num` varchar(20) NOT NULL COMMENT '订单号'");}
if(!pdo_fieldexists('cjdc_order','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `state` int(11) NOT NULL COMMENT '1.待付款2.待结单3.等待送达4.完成5.已评价6.取消7.拒绝8.退款中9.已退款10.退款拒绝'");}
if(!pdo_fieldexists('cjdc_order','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `time` varchar(20) NOT NULL COMMENT '下单时间'");}
if(!pdo_fieldexists('cjdc_order','pay_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `pay_time` varchar(20) NOT NULL COMMENT '支付时间'");}
if(!pdo_fieldexists('cjdc_order','jd_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `jd_time` varchar(20) NOT NULL COMMENT '接单时间'");}
if(!pdo_fieldexists('cjdc_order','cancel_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `cancel_time` varchar(20) NOT NULL COMMENT '取消时间'");}
if(!pdo_fieldexists('cjdc_order','complete_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `complete_time` varchar(20) NOT NULL COMMENT '完成时间'");}
if(!pdo_fieldexists('cjdc_order','money')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `money` decimal(10,2) NOT NULL COMMENT '付款金额'");}
if(!pdo_fieldexists('cjdc_order','box_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `box_money` decimal(10,2) NOT NULL COMMENT '餐盒费'");}
if(!pdo_fieldexists('cjdc_order','ps_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `ps_money` decimal(10,2) NOT NULL COMMENT '配送费'");}
if(!pdo_fieldexists('cjdc_order','mj_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `mj_money` decimal(10,2) NOT NULL COMMENT '满减优惠'");}
if(!pdo_fieldexists('cjdc_order','xyh_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `xyh_money` decimal(10,2) NOT NULL COMMENT '新用户立减'");}
if(!pdo_fieldexists('cjdc_order','tel')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `tel` varchar(20) NOT NULL COMMENT '电话'");}
if(!pdo_fieldexists('cjdc_order','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `name` varchar(20) NOT NULL COMMENT '姓名'");}
if(!pdo_fieldexists('cjdc_order','address')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `address` varchar(200) NOT NULL COMMENT '地址'");}
if(!pdo_fieldexists('cjdc_order','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `type` int(11) NOT NULL COMMENT '1.外卖2.店内3.预定4.当面付'");}
if(!pdo_fieldexists('cjdc_order','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `store_id` int(11) NOT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('cjdc_order','note')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `note` varchar(50) NOT NULL COMMENT '备注'");}
if(!pdo_fieldexists('cjdc_order','jj_note')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `jj_note` varchar(50) NOT NULL COMMENT '拒绝理由'");}
if(!pdo_fieldexists('cjdc_order','area')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `area` varchar(20) NOT NULL COMMENT '区域'");}
if(!pdo_fieldexists('cjdc_order','lat')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `lat` varchar(20) NOT NULL COMMENT '经度'");}
if(!pdo_fieldexists('cjdc_order','lng')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `lng` varchar(20) NOT NULL COMMENT '纬度'");}
if(!pdo_fieldexists('cjdc_order','del')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `del` int(11) NOT NULL DEFAULT '2' COMMENT '1.删除  2.未删除'");}
if(!pdo_fieldexists('cjdc_order','pay_type')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `pay_type` int(11) NOT NULL COMMENT '1.微信支付2.余额支付3.积分支付4.货到付款'");}
if(!pdo_fieldexists('cjdc_order','form_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `form_id` varchar(50) NOT NULL COMMENT '模板消息form_id'");}
if(!pdo_fieldexists('cjdc_order','form_id2')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `form_id2` varchar(50) NOT NULL COMMENT '发货formid'");}
if(!pdo_fieldexists('cjdc_order','code')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `code` varchar(100) NOT NULL COMMENT '支付code'");}
if(!pdo_fieldexists('cjdc_order','order_type')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `order_type` int(11) NOT NULL COMMENT '1.配送2.到店自取'");}
if(!pdo_fieldexists('cjdc_order','delivery_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `delivery_time` varchar(20) NOT NULL COMMENT '送达时间'");}
if(!pdo_fieldexists('cjdc_order','sex')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `sex` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_order','discount')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `discount` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_order','tableware')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `tableware` int(11) NOT NULL COMMENT '餐具'");}
if(!pdo_fieldexists('cjdc_order','dd_info')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `dd_info` text NOT NULL COMMENT '达达信息'");}
if(!pdo_fieldexists('cjdc_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_order','yhq_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `yhq_money` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_order','coupon_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `coupon_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_order','yhq_money2')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `yhq_money2` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_order','coupon_id2')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `coupon_id2` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_order','table_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `table_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_order','dn_state')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `dn_state` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_order','dm_state')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `dm_state` int(4) NOT NULL");}
if(!pdo_fieldexists('cjdc_order','yy_state')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `yy_state` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_order','deposit')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `deposit` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_order','ship_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `ship_id` varchar(30) NOT NULL");}
if(!pdo_fieldexists('cjdc_order','zk_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `zk_money` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_order','is_dd')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `is_dd` int(11) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_order','pt_info')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `pt_info` text NOT NULL");}
if(!pdo_fieldexists('cjdc_order','kfw_info')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `kfw_info` text NOT NULL");}
if(!pdo_fieldexists('cjdc_order','hb_type')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `hb_type` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_order','original_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `original_money` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_order','oid')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   `oid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_order','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('cjdc_order','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `user_id` (`user_id`)");}
if(!pdo_fieldexists('cjdc_order','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `store_id` (`store_id`)");}
if(!pdo_fieldexists('cjdc_order','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `state` (`state`)");}
if(!pdo_fieldexists('cjdc_order','order_type')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `order_type` (`order_type`)");}
if(!pdo_fieldexists('cjdc_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('cjdc_order','user_id_2')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `user_id_2` (`user_id`)");}
if(!pdo_fieldexists('cjdc_order','store_id_2')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `store_id_2` (`store_id`)");}
if(!pdo_fieldexists('cjdc_order','state_2')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `state_2` (`state`)");}
if(!pdo_fieldexists('cjdc_order','order_type_2')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `order_type_2` (`order_type`)");}
if(!pdo_fieldexists('cjdc_order','uniacid_2')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `uniacid_2` (`uniacid`)");}
if(!pdo_fieldexists('cjdc_order','user_id_3')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `user_id_3` (`user_id`)");}
if(!pdo_fieldexists('cjdc_order','store_id_3')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `store_id_3` (`store_id`)");}
if(!pdo_fieldexists('cjdc_order','state_3')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `state_3` (`state`)");}
if(!pdo_fieldexists('cjdc_order','order_type_3')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `order_type_3` (`order_type`)");}
if(!pdo_fieldexists('cjdc_order','uniacid_3')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `uniacid_3` (`uniacid`)");}
if(!pdo_fieldexists('cjdc_order','user_id_4')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `user_id_4` (`user_id`)");}
if(!pdo_fieldexists('cjdc_order','store_id_4')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `store_id_4` (`store_id`)");}
if(!pdo_fieldexists('cjdc_order','state_4')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `state_4` (`state`)");}
if(!pdo_fieldexists('cjdc_order','order_type_4')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `order_type_4` (`order_type`)");}
if(!pdo_fieldexists('cjdc_order','uniacid_4')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `uniacid_4` (`uniacid`)");}
if(!pdo_fieldexists('cjdc_order','user_id_5')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `user_id_5` (`user_id`)");}
if(!pdo_fieldexists('cjdc_order','store_id_5')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `store_id_5` (`store_id`)");}
if(!pdo_fieldexists('cjdc_order','state_5')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `state_5` (`state`)");}
if(!pdo_fieldexists('cjdc_order','order_type_5')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `order_type_5` (`order_type`)");}
if(!pdo_fieldexists('cjdc_order','uniacid_5')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `uniacid_5` (`uniacid`)");}
if(!pdo_fieldexists('cjdc_order','user_id_6')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `user_id_6` (`user_id`)");}
if(!pdo_fieldexists('cjdc_order','store_id_6')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `store_id_6` (`store_id`)");}
if(!pdo_fieldexists('cjdc_order','state_6')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `state_6` (`state`)");}
if(!pdo_fieldexists('cjdc_order','order_type_6')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `order_type_6` (`order_type`)");}
if(!pdo_fieldexists('cjdc_order','uniacid_6')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `uniacid_6` (`uniacid`)");}
if(!pdo_fieldexists('cjdc_order','user_id_7')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `user_id_7` (`user_id`)");}
if(!pdo_fieldexists('cjdc_order','store_id_7')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `store_id_7` (`store_id`)");}
if(!pdo_fieldexists('cjdc_order','state_7')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `state_7` (`state`)");}
if(!pdo_fieldexists('cjdc_order','order_type_7')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `order_type_7` (`order_type`)");}
if(!pdo_fieldexists('cjdc_order','uniacid_7')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `uniacid_7` (`uniacid`)");}
if(!pdo_fieldexists('cjdc_order','user_id_8')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `user_id_8` (`user_id`)");}
if(!pdo_fieldexists('cjdc_order','store_id_8')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `store_id_8` (`store_id`)");}
if(!pdo_fieldexists('cjdc_order','state_8')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `state_8` (`state`)");}
if(!pdo_fieldexists('cjdc_order','order_type_8')) {pdo_query("ALTER TABLE ".tablename('cjdc_order')." ADD   KEY `order_type_8` (`order_type`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_order_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `img` varchar(200) NOT NULL,
  `number` int(11) NOT NULL COMMENT '数量',
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `name` varchar(20) NOT NULL COMMENT '商品名称',
  `money` decimal(10,2) NOT NULL COMMENT '商品金额',
  `dishes_id` int(11) NOT NULL COMMENT '菜品id',
  `spec` varchar(50) NOT NULL COMMENT '规格',
  `uniacid` int(11) NOT NULL,
  `is_jc` int(11) NOT NULL DEFAULT '2',
  `is_qg` int(4) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `order_id_2` (`order_id`),
  KEY `order_id_3` (`order_id`),
  KEY `order_id_4` (`order_id`),
  KEY `order_id_5` (`order_id`),
  KEY `order_id_6` (`order_id`),
  KEY `order_id_7` (`order_id`),
  KEY `order_id_8` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_order_goods','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_order_goods','img')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   `img` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_order_goods','number')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   `number` int(11) NOT NULL COMMENT '数量'");}
if(!pdo_fieldexists('cjdc_order_goods','order_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   `order_id` int(11) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('cjdc_order_goods','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   `name` varchar(20) NOT NULL COMMENT '商品名称'");}
if(!pdo_fieldexists('cjdc_order_goods','money')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   `money` decimal(10,2) NOT NULL COMMENT '商品金额'");}
if(!pdo_fieldexists('cjdc_order_goods','dishes_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   `dishes_id` int(11) NOT NULL COMMENT '菜品id'");}
if(!pdo_fieldexists('cjdc_order_goods','spec')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   `spec` varchar(50) NOT NULL COMMENT '规格'");}
if(!pdo_fieldexists('cjdc_order_goods','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_order_goods','is_jc')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   `is_jc` int(11) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_order_goods','is_qg')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   `is_qg` int(4) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_order_goods','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('cjdc_order_goods','order_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   KEY `order_id` (`order_id`)");}
if(!pdo_fieldexists('cjdc_order_goods','order_id_2')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   KEY `order_id_2` (`order_id`)");}
if(!pdo_fieldexists('cjdc_order_goods','order_id_3')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   KEY `order_id_3` (`order_id`)");}
if(!pdo_fieldexists('cjdc_order_goods','order_id_4')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   KEY `order_id_4` (`order_id`)");}
if(!pdo_fieldexists('cjdc_order_goods','order_id_5')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   KEY `order_id_5` (`order_id`)");}
if(!pdo_fieldexists('cjdc_order_goods','order_id_6')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   KEY `order_id_6` (`order_id`)");}
if(!pdo_fieldexists('cjdc_order_goods','order_id_7')) {pdo_query("ALTER TABLE ".tablename('cjdc_order_goods')." ADD   KEY `order_id_7` (`order_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchid` varchar(20) NOT NULL COMMENT '商户号',
  `wxkey` varchar(100) NOT NULL COMMENT '支付秘钥',
  `apiclient_cert` text NOT NULL COMMENT '支付证书',
  `apiclient_key` text NOT NULL COMMENT '支付证书',
  `ip` varchar(20) NOT NULL COMMENT 'ip地址',
  `jf_proportion` int(11) NOT NULL DEFAULT '10' COMMENT '积分支付比例',
  `integral` int(11) NOT NULL COMMENT '评价的积分',
  `integral2` int(11) NOT NULL COMMENT '消费得积分',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_pay','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_pay')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_pay','mchid')) {pdo_query("ALTER TABLE ".tablename('cjdc_pay')." ADD   `mchid` varchar(20) NOT NULL COMMENT '商户号'");}
if(!pdo_fieldexists('cjdc_pay','wxkey')) {pdo_query("ALTER TABLE ".tablename('cjdc_pay')." ADD   `wxkey` varchar(100) NOT NULL COMMENT '支付秘钥'");}
if(!pdo_fieldexists('cjdc_pay','apiclient_cert')) {pdo_query("ALTER TABLE ".tablename('cjdc_pay')." ADD   `apiclient_cert` text NOT NULL COMMENT '支付证书'");}
if(!pdo_fieldexists('cjdc_pay','apiclient_key')) {pdo_query("ALTER TABLE ".tablename('cjdc_pay')." ADD   `apiclient_key` text NOT NULL COMMENT '支付证书'");}
if(!pdo_fieldexists('cjdc_pay','ip')) {pdo_query("ALTER TABLE ".tablename('cjdc_pay')." ADD   `ip` varchar(20) NOT NULL COMMENT 'ip地址'");}
if(!pdo_fieldexists('cjdc_pay','jf_proportion')) {pdo_query("ALTER TABLE ".tablename('cjdc_pay')." ADD   `jf_proportion` int(11) NOT NULL DEFAULT '10' COMMENT '积分支付比例'");}
if(!pdo_fieldexists('cjdc_pay','integral')) {pdo_query("ALTER TABLE ".tablename('cjdc_pay')." ADD   `integral` int(11) NOT NULL COMMENT '评价的积分'");}
if(!pdo_fieldexists('cjdc_pay','integral2')) {pdo_query("ALTER TABLE ".tablename('cjdc_pay')." ADD   `integral2` int(11) NOT NULL COMMENT '消费得积分'");}
if(!pdo_fieldexists('cjdc_pay','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_pay')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_psset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_id` int(11) NOT NULL COMMENT '商家',
  `shop_no` varchar(20) NOT NULL COMMENT '门店编号',
  `store_id` int(11) NOT NULL COMMENT '门店id',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_psset','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_psset')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_psset','source_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_psset')." ADD   `source_id` int(11) NOT NULL COMMENT '商家'");}
if(!pdo_fieldexists('cjdc_psset','shop_no')) {pdo_query("ALTER TABLE ".tablename('cjdc_psset')." ADD   `shop_no` varchar(20) NOT NULL COMMENT '门店编号'");}
if(!pdo_fieldexists('cjdc_psset','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_psset')." ADD   `store_id` int(11) NOT NULL COMMENT '门店id'");}
if(!pdo_fieldexists('cjdc_psset','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_psset')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_qbmx` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `money` decimal(10,2) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1.加2减',
  `note` varchar(20) NOT NULL COMMENT '备注',
  `time` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_qbmx','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_qbmx')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_qbmx','money')) {pdo_query("ALTER TABLE ".tablename('cjdc_qbmx')." ADD   `money` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_qbmx','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_qbmx')." ADD   `type` int(11) NOT NULL COMMENT '1.加2减'");}
if(!pdo_fieldexists('cjdc_qbmx','note')) {pdo_query("ALTER TABLE ".tablename('cjdc_qbmx')." ADD   `note` varchar(20) NOT NULL COMMENT '备注'");}
if(!pdo_fieldexists('cjdc_qbmx','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_qbmx')." ADD   `time` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_qbmx','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_qbmx')." ADD   `user_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_qbmx','order_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_qbmx')." ADD   `order_id` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_qggoods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `logo` varchar(200) NOT NULL,
  `price` decimal(10,1) NOT NULL COMMENT '原价',
  `money` decimal(10,1) NOT NULL COMMENT '现价',
  `number` int(11) NOT NULL COMMENT '数量',
  `surplus` int(11) NOT NULL COMMENT '剩余',
  `start_time` varchar(20) NOT NULL COMMENT '开始时间',
  `end_time` varchar(20) NOT NULL COMMENT '结束时间',
  `consumption_time` int(11) NOT NULL COMMENT '消费截止时间',
  `details` text NOT NULL,
  `store_id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `state` int(11) NOT NULL DEFAULT '1' COMMENT '1.上架2.下架',
  `type_id` int(11) NOT NULL,
  `img` text NOT NULL,
  `num` int(11) NOT NULL,
  `hot` int(11) NOT NULL,
  `state2` int(4) NOT NULL DEFAULT '1',
  `content` varchar(100) NOT NULL,
  `time` varchar(20) NOT NULL,
  `qg_num` int(4) NOT NULL DEFAULT '1',
  `type` int(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_qggoods','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_qggoods','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_qggoods','logo')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `logo` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_qggoods','price')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `price` decimal(10,1) NOT NULL COMMENT '原价'");}
if(!pdo_fieldexists('cjdc_qggoods','money')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `money` decimal(10,1) NOT NULL COMMENT '现价'");}
if(!pdo_fieldexists('cjdc_qggoods','number')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `number` int(11) NOT NULL COMMENT '数量'");}
if(!pdo_fieldexists('cjdc_qggoods','surplus')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `surplus` int(11) NOT NULL COMMENT '剩余'");}
if(!pdo_fieldexists('cjdc_qggoods','start_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `start_time` varchar(20) NOT NULL COMMENT '开始时间'");}
if(!pdo_fieldexists('cjdc_qggoods','end_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `end_time` varchar(20) NOT NULL COMMENT '结束时间'");}
if(!pdo_fieldexists('cjdc_qggoods','consumption_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `consumption_time` int(11) NOT NULL COMMENT '消费截止时间'");}
if(!pdo_fieldexists('cjdc_qggoods','details')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `details` text NOT NULL");}
if(!pdo_fieldexists('cjdc_qggoods','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `store_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_qggoods','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_qggoods','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `state` int(11) NOT NULL DEFAULT '1' COMMENT '1.上架2.下架'");}
if(!pdo_fieldexists('cjdc_qggoods','type_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `type_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_qggoods','img')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `img` text NOT NULL");}
if(!pdo_fieldexists('cjdc_qggoods','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `num` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_qggoods','hot')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `hot` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_qggoods','state2')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `state2` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_qggoods','content')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `content` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_qggoods','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `time` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_qggoods','qg_num')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `qg_num` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_qggoods','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_qggoods')." ADD   `type` int(4) NOT NULL DEFAULT '1'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_qgorder` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_num` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `user_tel` varchar(20) NOT NULL,
  `store_id` int(11) NOT NULL,
  `money` decimal(10,2) NOT NULL,
  `good_id` int(11) NOT NULL,
  `pay_type` int(11) NOT NULL COMMENT '1.微信支付2.余额支付',
  `state` int(11) NOT NULL COMMENT '1.待支付2已支付3.已核销',
  `dq_time` varchar(20) NOT NULL COMMENT '到期时间',
  `uniacid` int(11) NOT NULL,
  `code` varchar(100) NOT NULL,
  `good_name` varchar(20) NOT NULL,
  `good_logo` varchar(200) NOT NULL,
  `pay_time` varchar(20) NOT NULL,
  `hx_time` varchar(20) NOT NULL,
  `del` int(11) NOT NULL DEFAULT '2' COMMENT '1删除2.未删除',
  `note` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_qgorder','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_qgorder','order_num')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `order_num` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_qgorder','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `user_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_qgorder','user_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `user_name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_qgorder','user_tel')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `user_tel` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_qgorder','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `store_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_qgorder','money')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `money` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_qgorder','good_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `good_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_qgorder','pay_type')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `pay_type` int(11) NOT NULL COMMENT '1.微信支付2.余额支付'");}
if(!pdo_fieldexists('cjdc_qgorder','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `state` int(11) NOT NULL COMMENT '1.待支付2已支付3.已核销'");}
if(!pdo_fieldexists('cjdc_qgorder','dq_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `dq_time` varchar(20) NOT NULL COMMENT '到期时间'");}
if(!pdo_fieldexists('cjdc_qgorder','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_qgorder','code')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `code` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_qgorder','good_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `good_name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_qgorder','good_logo')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `good_logo` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_qgorder','pay_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `pay_time` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_qgorder','hx_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `hx_time` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_qgorder','del')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `del` int(11) NOT NULL DEFAULT '2' COMMENT '1删除2.未删除'");}
if(!pdo_fieldexists('cjdc_qgorder','note')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgorder')." ADD   `note` varchar(50) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_qgtype` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `num` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `state` int(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_qgtype','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgtype')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_qgtype','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgtype')." ADD   `name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_qgtype','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgtype')." ADD   `num` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_qgtype','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgtype')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_qgtype','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_qgtype')." ADD   `state` int(4) NOT NULL DEFAULT '1'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_reduction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '活动名称',
  `full` int(11) NOT NULL COMMENT '满',
  `reduction` int(11) NOT NULL COMMENT '减',
  `type` int(11) NOT NULL COMMENT '1.外卖 2.店内 3.外卖+店内',
  `store_id` int(11) NOT NULL COMMENT '商家id',
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `store_id_2` (`store_id`),
  KEY `store_id_3` (`store_id`),
  KEY `store_id_4` (`store_id`),
  KEY `store_id_5` (`store_id`),
  KEY `store_id_6` (`store_id`),
  KEY `store_id_7` (`store_id`),
  KEY `store_id_8` (`store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='满减活动';

");

if(!pdo_fieldexists('cjdc_reduction','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_reduction')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_reduction','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_reduction')." ADD   `name` varchar(50) NOT NULL COMMENT '活动名称'");}
if(!pdo_fieldexists('cjdc_reduction','full')) {pdo_query("ALTER TABLE ".tablename('cjdc_reduction')." ADD   `full` int(11) NOT NULL COMMENT '满'");}
if(!pdo_fieldexists('cjdc_reduction','reduction')) {pdo_query("ALTER TABLE ".tablename('cjdc_reduction')." ADD   `reduction` int(11) NOT NULL COMMENT '减'");}
if(!pdo_fieldexists('cjdc_reduction','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_reduction')." ADD   `type` int(11) NOT NULL COMMENT '1.外卖 2.店内 3.外卖+店内'");}
if(!pdo_fieldexists('cjdc_reduction','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_reduction')." ADD   `store_id` int(11) NOT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('cjdc_reduction','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_reduction')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('cjdc_reduction','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_reduction')." ADD   KEY `store_id` (`store_id`)");}
if(!pdo_fieldexists('cjdc_reduction','store_id_2')) {pdo_query("ALTER TABLE ".tablename('cjdc_reduction')." ADD   KEY `store_id_2` (`store_id`)");}
if(!pdo_fieldexists('cjdc_reduction','store_id_3')) {pdo_query("ALTER TABLE ".tablename('cjdc_reduction')." ADD   KEY `store_id_3` (`store_id`)");}
if(!pdo_fieldexists('cjdc_reduction','store_id_4')) {pdo_query("ALTER TABLE ".tablename('cjdc_reduction')." ADD   KEY `store_id_4` (`store_id`)");}
if(!pdo_fieldexists('cjdc_reduction','store_id_5')) {pdo_query("ALTER TABLE ".tablename('cjdc_reduction')." ADD   KEY `store_id_5` (`store_id`)");}
if(!pdo_fieldexists('cjdc_reduction','store_id_6')) {pdo_query("ALTER TABLE ".tablename('cjdc_reduction')." ADD   KEY `store_id_6` (`store_id`)");}
if(!pdo_fieldexists('cjdc_reduction','store_id_7')) {pdo_query("ALTER TABLE ".tablename('cjdc_reduction')." ADD   KEY `store_id_7` (`store_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_reservation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '门店',
  `time` varchar(200) NOT NULL DEFAULT '' COMMENT '预定时间',
  `label` varchar(50) NOT NULL DEFAULT '' COMMENT '标签',
  `num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_reservation','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_reservation')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_reservation','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_reservation')." ADD   `store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '门店'");}
if(!pdo_fieldexists('cjdc_reservation','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_reservation')." ADD   `time` varchar(200) NOT NULL DEFAULT '' COMMENT '预定时间'");}
if(!pdo_fieldexists('cjdc_reservation','label')) {pdo_query("ALTER TABLE ".tablename('cjdc_reservation')." ADD   `label` varchar(50) NOT NULL DEFAULT '' COMMENT '标签'");}
if(!pdo_fieldexists('cjdc_reservation','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_reservation')." ADD   `num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_reservation','dateline')) {pdo_query("ALTER TABLE ".tablename('cjdc_reservation')." ADD   `dateline` int(10) unsigned NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('cjdc_reservation','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_reservation')." ADD   `uniacid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_retail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `user_tel` varchar(20) NOT NULL,
  `state` int(11) NOT NULL COMMENT '1.审核中2.通过3.拒绝',
  `uniacid` int(11) NOT NULL,
  `sh_time` int(11) NOT NULL COMMENT '审核时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分销申请';

");

if(!pdo_fieldexists('cjdc_retail','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_retail')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_retail','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_retail')." ADD   `user_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_retail','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_retail')." ADD   `time` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_retail','user_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_retail')." ADD   `user_name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_retail','user_tel')) {pdo_query("ALTER TABLE ".tablename('cjdc_retail')." ADD   `user_tel` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_retail','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_retail')." ADD   `state` int(11) NOT NULL COMMENT '1.审核中2.通过3.拒绝'");}
if(!pdo_fieldexists('cjdc_retail','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_retail')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_retail','sh_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_retail')." ADD   `sh_time` int(11) NOT NULL COMMENT '审核时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_rzlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL COMMENT '门店ID',
  `money` decimal(10,2) NOT NULL COMMENT '钱',
  `time` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `note` varchar(30) NOT NULL COMMENT '入驻',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='入驻记录';

");

if(!pdo_fieldexists('cjdc_rzlog','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_rzlog')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_rzlog','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_rzlog')." ADD   `store_id` int(11) NOT NULL COMMENT '门店ID'");}
if(!pdo_fieldexists('cjdc_rzlog','money')) {pdo_query("ALTER TABLE ".tablename('cjdc_rzlog')." ADD   `money` decimal(10,2) NOT NULL COMMENT '钱'");}
if(!pdo_fieldexists('cjdc_rzlog','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_rzlog')." ADD   `time` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_rzlog','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_rzlog')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_rzlog','note')) {pdo_query("ALTER TABLE ".tablename('cjdc_rzlog')." ADD   `note` varchar(30) NOT NULL COMMENT '入驻'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_rzqx` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `days` int(11) NOT NULL,
  `money` decimal(10,2) NOT NULL,
  `num` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_rzqx','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_rzqx')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_rzqx','days')) {pdo_query("ALTER TABLE ".tablename('cjdc_rzqx')." ADD   `days` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_rzqx','money')) {pdo_query("ALTER TABLE ".tablename('cjdc_rzqx')." ADD   `money` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_rzqx','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_rzqx')." ADD   `num` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_rzqx','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_rzqx')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_rzset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cjwt` text NOT NULL COMMENT '常见问题',
  `rzxy` text NOT NULL COMMENT '入驻协议',
  `is_ruzhu` int(11) NOT NULL DEFAULT '2' COMMENT '是否开启入驻',
  `is_img` int(11) NOT NULL DEFAULT '2' COMMENT '入驻食品和身份证照片',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_rzset','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_rzset')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_rzset','cjwt')) {pdo_query("ALTER TABLE ".tablename('cjdc_rzset')." ADD   `cjwt` text NOT NULL COMMENT '常见问题'");}
if(!pdo_fieldexists('cjdc_rzset','rzxy')) {pdo_query("ALTER TABLE ".tablename('cjdc_rzset')." ADD   `rzxy` text NOT NULL COMMENT '入驻协议'");}
if(!pdo_fieldexists('cjdc_rzset','is_ruzhu')) {pdo_query("ALTER TABLE ".tablename('cjdc_rzset')." ADD   `is_ruzhu` int(11) NOT NULL DEFAULT '2' COMMENT '是否开启入驻'");}
if(!pdo_fieldexists('cjdc_rzset','is_img')) {pdo_query("ALTER TABLE ".tablename('cjdc_rzset')." ADD   `is_img` int(11) NOT NULL DEFAULT '2' COMMENT '入驻食品和身份证照片'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_service` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL COMMENT '门店id',
  `pid` int(11) NOT NULL COMMENT '父ID',
  `num` int(11) NOT NULL COMMENT '排序',
  `time` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '时间',
  `dateline` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='送达时间表';

");

if(!pdo_fieldexists('cjdc_service','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_service')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_service','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_service')." ADD   `store_id` int(11) NOT NULL COMMENT '门店id'");}
if(!pdo_fieldexists('cjdc_service','pid')) {pdo_query("ALTER TABLE ".tablename('cjdc_service')." ADD   `pid` int(11) NOT NULL COMMENT '父ID'");}
if(!pdo_fieldexists('cjdc_service','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_service')." ADD   `num` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_service','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_service')." ADD   `time` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '时间'");}
if(!pdo_fieldexists('cjdc_service','dateline')) {pdo_query("ALTER TABLE ".tablename('cjdc_service')." ADD   `dateline` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_service','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_service')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_service_pay` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mchid` varchar(20) NOT NULL,
  `wxkey` varchar(100) NOT NULL,
  `apiclient_cert` text NOT NULL,
  `apiclient_key` text NOT NULL,
  `is_open` int(11) NOT NULL DEFAULT '2',
  `uniacid` int(11) NOT NULL,
  `appid` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_service_pay','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_service_pay')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_service_pay','mchid')) {pdo_query("ALTER TABLE ".tablename('cjdc_service_pay')." ADD   `mchid` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_service_pay','wxkey')) {pdo_query("ALTER TABLE ".tablename('cjdc_service_pay')." ADD   `wxkey` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_service_pay','apiclient_cert')) {pdo_query("ALTER TABLE ".tablename('cjdc_service_pay')." ADD   `apiclient_cert` text NOT NULL");}
if(!pdo_fieldexists('cjdc_service_pay','apiclient_key')) {pdo_query("ALTER TABLE ".tablename('cjdc_service_pay')." ADD   `apiclient_key` text NOT NULL");}
if(!pdo_fieldexists('cjdc_service_pay','is_open')) {pdo_query("ALTER TABLE ".tablename('cjdc_service_pay')." ADD   `is_open` int(11) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_service_pay','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_service_pay')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_service_pay','appid')) {pdo_query("ALTER TABLE ".tablename('cjdc_service_pay')." ADD   `appid` varchar(50) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_shopcar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `good_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `spec` varchar(100) NOT NULL,
  `combination_id` int(11) NOT NULL,
  `money` decimal(10,2) NOT NULL,
  `box_money` decimal(10,2) NOT NULL,
  `son_id` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `dr_id` int(11) NOT NULL,
  `is_qg` int(4) NOT NULL DEFAULT '2',
  `qg_name` varchar(20) NOT NULL,
  `qg_logo` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_shopcar','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_shopcar')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_shopcar','good_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_shopcar')." ADD   `good_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_shopcar','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_shopcar')." ADD   `user_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_shopcar','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_shopcar')." ADD   `store_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_shopcar','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_shopcar')." ADD   `num` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_shopcar','spec')) {pdo_query("ALTER TABLE ".tablename('cjdc_shopcar')." ADD   `spec` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_shopcar','combination_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_shopcar')." ADD   `combination_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_shopcar','money')) {pdo_query("ALTER TABLE ".tablename('cjdc_shopcar')." ADD   `money` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_shopcar','box_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_shopcar')." ADD   `box_money` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_shopcar','son_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_shopcar')." ADD   `son_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_shopcar','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_shopcar')." ADD   `type` int(11) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_shopcar','dr_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_shopcar')." ADD   `dr_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_shopcar','is_qg')) {pdo_query("ALTER TABLE ".tablename('cjdc_shopcar')." ADD   `is_qg` int(4) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_shopcar','qg_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_shopcar')." ADD   `qg_name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_shopcar','qg_logo')) {pdo_query("ALTER TABLE ".tablename('cjdc_shopcar')." ADD   `qg_logo` varchar(500) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_signlist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `time` varchar(20) NOT NULL COMMENT '签到时间',
  `integral` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `time2` int(11) NOT NULL,
  `time3` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签到列表';

");

if(!pdo_fieldexists('cjdc_signlist','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_signlist')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_signlist','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_signlist')." ADD   `user_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_signlist','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_signlist')." ADD   `time` varchar(20) NOT NULL COMMENT '签到时间'");}
if(!pdo_fieldexists('cjdc_signlist','integral')) {pdo_query("ALTER TABLE ".tablename('cjdc_signlist')." ADD   `integral` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_signlist','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_signlist')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_signlist','time2')) {pdo_query("ALTER TABLE ".tablename('cjdc_signlist')." ADD   `time2` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_signlist','time3')) {pdo_query("ALTER TABLE ".tablename('cjdc_signlist')." ADD   `time3` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_signset` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `one` int(11) NOT NULL COMMENT '首次奖励积分',
  `integral` int(11) NOT NULL COMMENT '每天签到积分',
  `is_open` int(11) NOT NULL COMMENT '1.开启2.关闭  签到',
  `is_bq` int(11) NOT NULL COMMENT '1.开启2.关闭  补签',
  `bq_integral` int(11) NOT NULL COMMENT '补签扣除积分',
  `details` text NOT NULL COMMENT '签到说明',
  `uniacid` int(11) NOT NULL,
  `qd_img` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_signset','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_signset')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_signset','one')) {pdo_query("ALTER TABLE ".tablename('cjdc_signset')." ADD   `one` int(11) NOT NULL COMMENT '首次奖励积分'");}
if(!pdo_fieldexists('cjdc_signset','integral')) {pdo_query("ALTER TABLE ".tablename('cjdc_signset')." ADD   `integral` int(11) NOT NULL COMMENT '每天签到积分'");}
if(!pdo_fieldexists('cjdc_signset','is_open')) {pdo_query("ALTER TABLE ".tablename('cjdc_signset')." ADD   `is_open` int(11) NOT NULL COMMENT '1.开启2.关闭  签到'");}
if(!pdo_fieldexists('cjdc_signset','is_bq')) {pdo_query("ALTER TABLE ".tablename('cjdc_signset')." ADD   `is_bq` int(11) NOT NULL COMMENT '1.开启2.关闭  补签'");}
if(!pdo_fieldexists('cjdc_signset','bq_integral')) {pdo_query("ALTER TABLE ".tablename('cjdc_signset')." ADD   `bq_integral` int(11) NOT NULL COMMENT '补签扣除积分'");}
if(!pdo_fieldexists('cjdc_signset','details')) {pdo_query("ALTER TABLE ".tablename('cjdc_signset')." ADD   `details` text NOT NULL COMMENT '签到说明'");}
if(!pdo_fieldexists('cjdc_signset','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_signset')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_signset','qd_img')) {pdo_query("ALTER TABLE ".tablename('cjdc_signset')." ADD   `qd_img` varchar(200) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appkey` varchar(100) NOT NULL,
  `wm_tid` varchar(20) NOT NULL,
  `dn_tid` varchar(20) NOT NULL,
  `tel` varchar(20) NOT NULL,
  `store_id` int(11) NOT NULL,
  `is_wm` int(11) NOT NULL DEFAULT '2',
  `is_dn` int(11) NOT NULL DEFAULT '2',
  `yy_tid` varchar(20) NOT NULL,
  `is_yy` int(11) NOT NULL DEFAULT '2',
  `item` int(4) NOT NULL DEFAULT '1',
  `appid` varchar(20) NOT NULL,
  `tx_appkey` varchar(50) NOT NULL,
  `template_id` varchar(50) NOT NULL,
  `sign` varchar(200) NOT NULL,
  `code` varchar(10) NOT NULL DEFAULT '86',
  `aliyun_appkey` varchar(20) NOT NULL,
  `aliyun_appsecret` varchar(50) NOT NULL,
  `aliyun_sign` varchar(20) NOT NULL,
  `is_tk` int(1) NOT NULL DEFAULT '2',
  `tk_tid` varchar(30) NOT NULL,
  `openId` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_sms','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_sms','appkey')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `appkey` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_sms','wm_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `wm_tid` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_sms','dn_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `dn_tid` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_sms','tel')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `tel` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_sms','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `store_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_sms','is_wm')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `is_wm` int(11) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_sms','is_dn')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `is_dn` int(11) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_sms','yy_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `yy_tid` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_sms','is_yy')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `is_yy` int(11) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_sms','item')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `item` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_sms','appid')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `appid` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_sms','tx_appkey')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `tx_appkey` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_sms','template_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `template_id` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_sms','sign')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `sign` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_sms','code')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `code` varchar(10) NOT NULL DEFAULT '86'");}
if(!pdo_fieldexists('cjdc_sms','aliyun_appkey')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `aliyun_appkey` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_sms','aliyun_appsecret')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `aliyun_appsecret` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_sms','aliyun_sign')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `aliyun_sign` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_sms','is_tk')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `is_tk` int(1) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_sms','tk_tid')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `tk_tid` varchar(30) NOT NULL");}
if(!pdo_fieldexists('cjdc_sms','openId')) {pdo_query("ALTER TABLE ".tablename('cjdc_sms')." ADD   `openId` varchar(100) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_spec` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '规格名称',
  `good_id` int(11) NOT NULL COMMENT '商品id',
  `num` int(11) NOT NULL COMMENT '排序',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_spec','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_spec')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_spec','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_spec')." ADD   `name` varchar(20) NOT NULL COMMENT '规格名称'");}
if(!pdo_fieldexists('cjdc_spec','good_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_spec')." ADD   `good_id` int(11) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('cjdc_spec','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_spec')." ADD   `num` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_spec','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_spec')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_spec_combination` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wm_money` decimal(10,2) NOT NULL COMMENT '外卖价格',
  `dn_money` decimal(10,2) NOT NULL COMMENT '店内价格',
  `combination` varchar(100) NOT NULL COMMENT '组合',
  `number` int(11) NOT NULL COMMENT '库存',
  `good_id` int(11) NOT NULL COMMENT '商品id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_spec_combination','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_spec_combination')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_spec_combination','wm_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_spec_combination')." ADD   `wm_money` decimal(10,2) NOT NULL COMMENT '外卖价格'");}
if(!pdo_fieldexists('cjdc_spec_combination','dn_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_spec_combination')." ADD   `dn_money` decimal(10,2) NOT NULL COMMENT '店内价格'");}
if(!pdo_fieldexists('cjdc_spec_combination','combination')) {pdo_query("ALTER TABLE ".tablename('cjdc_spec_combination')." ADD   `combination` varchar(100) NOT NULL COMMENT '组合'");}
if(!pdo_fieldexists('cjdc_spec_combination','number')) {pdo_query("ALTER TABLE ".tablename('cjdc_spec_combination')." ADD   `number` int(11) NOT NULL COMMENT '库存'");}
if(!pdo_fieldexists('cjdc_spec_combination','good_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_spec_combination')." ADD   `good_id` int(11) NOT NULL COMMENT '商品id'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_spec_val` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '规格属性名称',
  `spec_id` int(11) NOT NULL COMMENT '规格id',
  `num` int(11) NOT NULL COMMENT '排序',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `good_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_spec_val','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_spec_val')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_spec_val','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_spec_val')." ADD   `name` varchar(20) NOT NULL COMMENT '规格属性名称'");}
if(!pdo_fieldexists('cjdc_spec_val','spec_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_spec_val')." ADD   `spec_id` int(11) NOT NULL COMMENT '规格id'");}
if(!pdo_fieldexists('cjdc_spec_val','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_spec_val')." ADD   `num` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_spec_val','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_spec_val')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('cjdc_spec_val','good_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_spec_val')." ADD   `good_id` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_special` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `day` varchar(20) NOT NULL COMMENT '日期',
  `integral` int(11) NOT NULL COMMENT '积分',
  `title` varchar(20) NOT NULL COMMENT '标题说明',
  `color` varchar(20) NOT NULL COMMENT '颜色',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='特殊日期签到';

");

if(!pdo_fieldexists('cjdc_special','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_special')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_special','day')) {pdo_query("ALTER TABLE ".tablename('cjdc_special')." ADD   `day` varchar(20) NOT NULL COMMENT '日期'");}
if(!pdo_fieldexists('cjdc_special','integral')) {pdo_query("ALTER TABLE ".tablename('cjdc_special')." ADD   `integral` int(11) NOT NULL COMMENT '积分'");}
if(!pdo_fieldexists('cjdc_special','title')) {pdo_query("ALTER TABLE ".tablename('cjdc_special')." ADD   `title` varchar(20) NOT NULL COMMENT '标题说明'");}
if(!pdo_fieldexists('cjdc_special','color')) {pdo_query("ALTER TABLE ".tablename('cjdc_special')." ADD   `color` varchar(20) NOT NULL COMMENT '颜色'");}
if(!pdo_fieldexists('cjdc_special','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_special')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '商家名称',
  `address` varchar(200) NOT NULL COMMENT '商家地址',
  `time` varchar(20) NOT NULL COMMENT '营业时间',
  `time2` varchar(20) NOT NULL COMMENT '营业时间',
  `time3` varchar(20) NOT NULL COMMENT '营业时间',
  `time4` varchar(20) NOT NULL COMMENT '营业时间',
  `tel` varchar(20) NOT NULL COMMENT '电话',
  `announcement` varchar(200) NOT NULL COMMENT '公告',
  `is_rest` int(11) NOT NULL DEFAULT '2' COMMENT '是否休息(1 是  2否)',
  `img` text NOT NULL COMMENT '商家图片',
  `start_at` varchar(20) NOT NULL COMMENT '起送价',
  `freight` varchar(20) NOT NULL COMMENT '配送费',
  `logo` varchar(200) NOT NULL COMMENT 'logo',
  `details` text NOT NULL COMMENT '商家简介',
  `color` varchar(20) NOT NULL COMMENT '颜色',
  `coordinates` varchar(50) NOT NULL COMMENT '经纬度',
  `yyzz` text NOT NULL COMMENT '营业资质',
  `md_area` int(11) NOT NULL COMMENT '门店区域id',
  `md_type` int(11) NOT NULL COMMENT '门店类型id',
  `sales` decimal(10,1) NOT NULL,
  `score` int(11) NOT NULL COMMENT '销量',
  `capita` int(11) NOT NULL COMMENT '人均',
  `tx_user` int(11) NOT NULL COMMENT '提现人',
  `is_open` int(11) NOT NULL DEFAULT '1' COMMENT '是否开启门店',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `number` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `environment` text NOT NULL,
  `is_brand` int(11) NOT NULL DEFAULT '2',
  `state` int(4) NOT NULL DEFAULT '2',
  `rz_time` varchar(10) NOT NULL,
  `rzdq_time` varchar(20) NOT NULL DEFAULT '2020-06-08',
  `sq_id` int(11) NOT NULL DEFAULT '0',
  `zm_img` varchar(255) NOT NULL,
  `fm_img` varchar(255) NOT NULL,
  `zf_state` int(4) NOT NULL,
  `code` varchar(50) NOT NULL,
  `link_name` varchar(20) NOT NULL,
  `link_tel` varchar(20) NOT NULL,
  `sq_time` datetime NOT NULL,
  `money` decimal(10,2) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `is_mp3` int(1) NOT NULL DEFAULT '2',
  `is_video` int(1) NOT NULL DEFAULT '2',
  `store_mp3` varchar(1000) NOT NULL,
  ` store_video` varchar(1000) NOT NULL,
  `store_video` varchar(1000) NOT NULL,
  `ps_poundage` varchar(10) NOT NULL,
  `qrcode` varchar(300) NOT NULL,
  `is_select` int(4) NOT NULL DEFAULT '2',
  `store_mchid` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uniacid_2` (`uniacid`),
  KEY `uniacid_3` (`uniacid`),
  KEY `uniacid_4` (`uniacid`),
  KEY `uniacid_5` (`uniacid`),
  KEY `uniacid_6` (`uniacid`),
  KEY `uniacid_7` (`uniacid`),
  KEY `uniacid_8` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_store','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_store','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `name` varchar(50) NOT NULL COMMENT '商家名称'");}
if(!pdo_fieldexists('cjdc_store','address')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `address` varchar(200) NOT NULL COMMENT '商家地址'");}
if(!pdo_fieldexists('cjdc_store','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `time` varchar(20) NOT NULL COMMENT '营业时间'");}
if(!pdo_fieldexists('cjdc_store','time2')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `time2` varchar(20) NOT NULL COMMENT '营业时间'");}
if(!pdo_fieldexists('cjdc_store','time3')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `time3` varchar(20) NOT NULL COMMENT '营业时间'");}
if(!pdo_fieldexists('cjdc_store','time4')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `time4` varchar(20) NOT NULL COMMENT '营业时间'");}
if(!pdo_fieldexists('cjdc_store','tel')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `tel` varchar(20) NOT NULL COMMENT '电话'");}
if(!pdo_fieldexists('cjdc_store','announcement')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `announcement` varchar(200) NOT NULL COMMENT '公告'");}
if(!pdo_fieldexists('cjdc_store','is_rest')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `is_rest` int(11) NOT NULL DEFAULT '2' COMMENT '是否休息(1 是  2否)'");}
if(!pdo_fieldexists('cjdc_store','img')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `img` text NOT NULL COMMENT '商家图片'");}
if(!pdo_fieldexists('cjdc_store','start_at')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `start_at` varchar(20) NOT NULL COMMENT '起送价'");}
if(!pdo_fieldexists('cjdc_store','freight')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `freight` varchar(20) NOT NULL COMMENT '配送费'");}
if(!pdo_fieldexists('cjdc_store','logo')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `logo` varchar(200) NOT NULL COMMENT 'logo'");}
if(!pdo_fieldexists('cjdc_store','details')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `details` text NOT NULL COMMENT '商家简介'");}
if(!pdo_fieldexists('cjdc_store','color')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `color` varchar(20) NOT NULL COMMENT '颜色'");}
if(!pdo_fieldexists('cjdc_store','coordinates')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `coordinates` varchar(50) NOT NULL COMMENT '经纬度'");}
if(!pdo_fieldexists('cjdc_store','yyzz')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `yyzz` text NOT NULL COMMENT '营业资质'");}
if(!pdo_fieldexists('cjdc_store','md_area')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `md_area` int(11) NOT NULL COMMENT '门店区域id'");}
if(!pdo_fieldexists('cjdc_store','md_type')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `md_type` int(11) NOT NULL COMMENT '门店类型id'");}
if(!pdo_fieldexists('cjdc_store','sales')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `sales` decimal(10,1) NOT NULL");}
if(!pdo_fieldexists('cjdc_store','score')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `score` int(11) NOT NULL COMMENT '销量'");}
if(!pdo_fieldexists('cjdc_store','capita')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `capita` int(11) NOT NULL COMMENT '人均'");}
if(!pdo_fieldexists('cjdc_store','tx_user')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `tx_user` int(11) NOT NULL COMMENT '提现人'");}
if(!pdo_fieldexists('cjdc_store','is_open')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `is_open` int(11) NOT NULL DEFAULT '1' COMMENT '是否开启门店'");}
if(!pdo_fieldexists('cjdc_store','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('cjdc_store','number')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `number` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_store','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `user_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_store','environment')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `environment` text NOT NULL");}
if(!pdo_fieldexists('cjdc_store','is_brand')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `is_brand` int(11) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_store','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `state` int(4) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_store','rz_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `rz_time` varchar(10) NOT NULL");}
if(!pdo_fieldexists('cjdc_store','rzdq_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `rzdq_time` varchar(20) NOT NULL DEFAULT '2020-06-08'");}
if(!pdo_fieldexists('cjdc_store','sq_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `sq_id` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('cjdc_store','zm_img')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `zm_img` varchar(255) NOT NULL");}
if(!pdo_fieldexists('cjdc_store','fm_img')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `fm_img` varchar(255) NOT NULL");}
if(!pdo_fieldexists('cjdc_store','zf_state')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `zf_state` int(4) NOT NULL");}
if(!pdo_fieldexists('cjdc_store','code')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `code` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_store','link_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `link_name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_store','link_tel')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `link_tel` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_store','sq_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `sq_time` datetime NOT NULL");}
if(!pdo_fieldexists('cjdc_store','money')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `money` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_store','admin_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `admin_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_store','is_mp3')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `is_mp3` int(1) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_store','is_video')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `is_video` int(1) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_store','store_mp3')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `store_mp3` varchar(1000) NOT NULL");}
if(!pdo_fieldexists('cjdc_store',' store_video')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   ` store_video` varchar(1000) NOT NULL");}
if(!pdo_fieldexists('cjdc_store','store_video')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `store_video` varchar(1000) NOT NULL");}
if(!pdo_fieldexists('cjdc_store','ps_poundage')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `ps_poundage` varchar(10) NOT NULL");}
if(!pdo_fieldexists('cjdc_store','qrcode')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `qrcode` varchar(300) NOT NULL");}
if(!pdo_fieldexists('cjdc_store','is_select')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `is_select` int(4) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_store','store_mchid')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   `store_mchid` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_store','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('cjdc_store','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('cjdc_store','uniacid_2')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   KEY `uniacid_2` (`uniacid`)");}
if(!pdo_fieldexists('cjdc_store','uniacid_3')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   KEY `uniacid_3` (`uniacid`)");}
if(!pdo_fieldexists('cjdc_store','uniacid_4')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   KEY `uniacid_4` (`uniacid`)");}
if(!pdo_fieldexists('cjdc_store','uniacid_5')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   KEY `uniacid_5` (`uniacid`)");}
if(!pdo_fieldexists('cjdc_store','uniacid_6')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   KEY `uniacid_6` (`uniacid`)");}
if(!pdo_fieldexists('cjdc_store','uniacid_7')) {pdo_query("ALTER TABLE ".tablename('cjdc_store')." ADD   KEY `uniacid_7` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_storead` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `logo` varchar(200) NOT NULL,
  `src` varchar(100) NOT NULL COMMENT '内部链接',
  `src2` varchar(200) NOT NULL COMMENT '外部链接',
  `created_time` varchar(20) NOT NULL COMMENT '创建时间',
  `orderby` int(11) NOT NULL COMMENT '排序',
  `status` int(11) NOT NULL COMMENT '1.启用2.禁用',
  `type` int(11) NOT NULL COMMENT '类型',
  `appid` varchar(20) NOT NULL COMMENT '小程序appid',
  `title` varchar(20) NOT NULL COMMENT '小程序appid',
  `xcx_name` varchar(20) NOT NULL COMMENT '小程序名称',
  `item` int(11) NOT NULL COMMENT '1.内部2.外部3.跳转小程序',
  `store_id` int(11) NOT NULL COMMENT '商家id',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_storead','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_storead')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_storead','logo')) {pdo_query("ALTER TABLE ".tablename('cjdc_storead')." ADD   `logo` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_storead','src')) {pdo_query("ALTER TABLE ".tablename('cjdc_storead')." ADD   `src` varchar(100) NOT NULL COMMENT '内部链接'");}
if(!pdo_fieldexists('cjdc_storead','src2')) {pdo_query("ALTER TABLE ".tablename('cjdc_storead')." ADD   `src2` varchar(200) NOT NULL COMMENT '外部链接'");}
if(!pdo_fieldexists('cjdc_storead','created_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_storead')." ADD   `created_time` varchar(20) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('cjdc_storead','orderby')) {pdo_query("ALTER TABLE ".tablename('cjdc_storead')." ADD   `orderby` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_storead','status')) {pdo_query("ALTER TABLE ".tablename('cjdc_storead')." ADD   `status` int(11) NOT NULL COMMENT '1.启用2.禁用'");}
if(!pdo_fieldexists('cjdc_storead','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_storead')." ADD   `type` int(11) NOT NULL COMMENT '类型'");}
if(!pdo_fieldexists('cjdc_storead','appid')) {pdo_query("ALTER TABLE ".tablename('cjdc_storead')." ADD   `appid` varchar(20) NOT NULL COMMENT '小程序appid'");}
if(!pdo_fieldexists('cjdc_storead','title')) {pdo_query("ALTER TABLE ".tablename('cjdc_storead')." ADD   `title` varchar(20) NOT NULL COMMENT '小程序appid'");}
if(!pdo_fieldexists('cjdc_storead','xcx_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_storead')." ADD   `xcx_name` varchar(20) NOT NULL COMMENT '小程序名称'");}
if(!pdo_fieldexists('cjdc_storead','item')) {pdo_query("ALTER TABLE ".tablename('cjdc_storead')." ADD   `item` int(11) NOT NULL COMMENT '1.内部2.外部3.跳转小程序'");}
if(!pdo_fieldexists('cjdc_storead','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_storead')." ADD   `store_id` int(11) NOT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('cjdc_storead','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_storead')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_storeset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `xyh_money` decimal(10,2) NOT NULL COMMENT '新用户立减金额',
  `xyh_open` int(11) NOT NULL DEFAULT '2' COMMENT '是否开启新用户立减1是2否',
  `integral` int(11) NOT NULL COMMENT '评价的积分',
  `integral2` int(11) NOT NULL COMMENT '消费得积分',
  `is_jd` int(11) NOT NULL DEFAULT '2' COMMENT '1自动接单  2.手动接单',
  `store_mp3` text NOT NULL COMMENT '商家音乐',
  `store_video` text NOT NULL COMMENT '商家视频',
  `is_mp3` int(11) NOT NULL DEFAULT '2' COMMENT '是否开启音乐',
  `is_video` int(11) NOT NULL DEFAULT '2' COMMENT '是否开启视频',
  `is_jfpay` int(11) DEFAULT '2' COMMENT '积分支付',
  `is_yuepay` int(11) NOT NULL DEFAULT '2' COMMENT '余额支付',
  `is_yuejf` int(11) NOT NULL DEFAULT '2' COMMENT '余额支付得积分',
  `is_wxpay` int(11) NOT NULL DEFAULT '1' COMMENT '微信支付',
  `poundage` int(11) NOT NULL COMMENT '手续费',
  `is_pj` int(11) NOT NULL DEFAULT '1' COMMENT '1.开启 2.关闭(评价)',
  `is_chzf` int(11) NOT NULL DEFAULT '2' COMMENT '餐后支付',
  `box_name` varchar(20) NOT NULL COMMENT '餐盒费文本',
  `yhq_name` varchar(20) NOT NULL COMMENT '收银文本',
  `sy_name` varchar(20) NOT NULL COMMENT '收银文本',
  `dn_name` varchar(20) NOT NULL COMMENT '店内文本',
  `wm_name` varchar(20) NOT NULL COMMENT '外卖文本',
  `yy_name` varchar(20) NOT NULL COMMENT '预约文本',
  `yhq_img` varchar(200) NOT NULL,
  `sy_img` varchar(200) NOT NULL,
  `dn_img` varchar(200) NOT NULL,
  `wm_img` varchar(200) NOT NULL,
  `yy_img` varchar(200) NOT NULL,
  `is_yhq` int(11) NOT NULL DEFAULT '1' COMMENT '是否开启优惠券',
  `is_sy` int(11) NOT NULL DEFAULT '1' COMMENT '是否开启收银',
  `is_dn` int(11) NOT NULL DEFAULT '1' COMMENT '是否开启店内',
  `is_wm` int(11) NOT NULL DEFAULT '1' COMMENT '是否开启外卖',
  `is_yy` int(11) NOT NULL DEFAULT '1' COMMENT '是否开启预约',
  `store_id` int(11) NOT NULL,
  `ps_time` varchar(20) NOT NULL COMMENT '配送时间',
  `ps_mode` varchar(20) NOT NULL COMMENT '1.商家配送2.达达配送',
  `ps_jl` int(11) NOT NULL COMMENT '配送距离',
  `is_zt` int(11) NOT NULL DEFAULT '2' COMMENT '1.开启2.关闭(是否自提)',
  `is_hdfk` int(11) NOT NULL DEFAULT '2' COMMENT '1.开启2.关闭(货到付款)',
  `print_type` int(4) NOT NULL DEFAULT '1' COMMENT '1整单,2分单打印',
  `ztxy` text NOT NULL COMMENT '自提协议',
  `top_style` int(11) NOT NULL DEFAULT '1',
  `info_style` int(11) NOT NULL DEFAULT '1',
  `yysm` varchar(20) NOT NULL,
  `wmsm` varchar(20) NOT NULL,
  `dnsm` varchar(20) NOT NULL,
  `sysm` varchar(20) NOT NULL,
  `is_wxzf` int(11) NOT NULL DEFAULT '1',
  `print_mode` int(4) NOT NULL DEFAULT '1',
  `is_yydc` int(11) NOT NULL DEFAULT '1',
  ` is_ps` int(1) NOT NULL DEFAULT '1',
  `is_ps` int(1) NOT NULL DEFAULT '1',
  `is_dd` int(11) NOT NULL DEFAULT '2',
  `is_cj` int(11) NOT NULL DEFAULT '1',
  `cj_name` varchar(20) NOT NULL DEFAULT '餐具用量',
  `wmps_name` varchar(20) NOT NULL DEFAULT '外卖配送',
  `is_czztpd` int(11) NOT NULL DEFAULT '1',
  `is_dcyhq` int(4) NOT NULL DEFAULT '1',
  `is_pt` int(4) NOT NULL DEFAULT '2',
  `pt_name` varchar(20) NOT NULL,
  `ptsm` varchar(100) NOT NULL,
  `pt_img` varchar(200) NOT NULL,
  `qg_name` varchar(20) NOT NULL,
  `qgsm` varchar(20) NOT NULL,
  `qg_img` varchar(200) NOT NULL,
  `is_qg` int(4) NOT NULL DEFAULT '2',
  `is_ydtime` int(4) NOT NULL DEFAULT '1',
  `is_yyzw` int(4) NOT NULL DEFAULT '1',
  `pd_name` varchar(30) NOT NULL,
  `pdsm` varchar(100) NOT NULL,
  `pd_img` varchar(255) NOT NULL,
  `is_pd` int(4) NOT NULL DEFAULT '1',
  `tz_src` int(4) NOT NULL DEFAULT '1',
  `full_delivery` int(11) NOT NULL,
  `is_poundage` int(1) NOT NULL DEFAULT '2',
  `dn_poundage` varchar(20) NOT NULL,
  `dm_poundage` varchar(20) NOT NULL,
  `yd_poundage` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `store_id_2` (`store_id`),
  KEY `store_id_3` (`store_id`),
  KEY `store_id_4` (`store_id`),
  KEY `store_id_5` (`store_id`),
  KEY `store_id_6` (`store_id`),
  KEY `store_id_7` (`store_id`),
  KEY `store_id_8` (`store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_storeset','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_storeset','xyh_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `xyh_money` decimal(10,2) NOT NULL COMMENT '新用户立减金额'");}
if(!pdo_fieldexists('cjdc_storeset','xyh_open')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `xyh_open` int(11) NOT NULL DEFAULT '2' COMMENT '是否开启新用户立减1是2否'");}
if(!pdo_fieldexists('cjdc_storeset','integral')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `integral` int(11) NOT NULL COMMENT '评价的积分'");}
if(!pdo_fieldexists('cjdc_storeset','integral2')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `integral2` int(11) NOT NULL COMMENT '消费得积分'");}
if(!pdo_fieldexists('cjdc_storeset','is_jd')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_jd` int(11) NOT NULL DEFAULT '2' COMMENT '1自动接单  2.手动接单'");}
if(!pdo_fieldexists('cjdc_storeset','store_mp3')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `store_mp3` text NOT NULL COMMENT '商家音乐'");}
if(!pdo_fieldexists('cjdc_storeset','store_video')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `store_video` text NOT NULL COMMENT '商家视频'");}
if(!pdo_fieldexists('cjdc_storeset','is_mp3')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_mp3` int(11) NOT NULL DEFAULT '2' COMMENT '是否开启音乐'");}
if(!pdo_fieldexists('cjdc_storeset','is_video')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_video` int(11) NOT NULL DEFAULT '2' COMMENT '是否开启视频'");}
if(!pdo_fieldexists('cjdc_storeset','is_jfpay')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_jfpay` int(11) DEFAULT '2' COMMENT '积分支付'");}
if(!pdo_fieldexists('cjdc_storeset','is_yuepay')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_yuepay` int(11) NOT NULL DEFAULT '2' COMMENT '余额支付'");}
if(!pdo_fieldexists('cjdc_storeset','is_yuejf')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_yuejf` int(11) NOT NULL DEFAULT '2' COMMENT '余额支付得积分'");}
if(!pdo_fieldexists('cjdc_storeset','is_wxpay')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_wxpay` int(11) NOT NULL DEFAULT '1' COMMENT '微信支付'");}
if(!pdo_fieldexists('cjdc_storeset','poundage')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `poundage` int(11) NOT NULL COMMENT '手续费'");}
if(!pdo_fieldexists('cjdc_storeset','is_pj')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_pj` int(11) NOT NULL DEFAULT '1' COMMENT '1.开启 2.关闭(评价)'");}
if(!pdo_fieldexists('cjdc_storeset','is_chzf')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_chzf` int(11) NOT NULL DEFAULT '2' COMMENT '餐后支付'");}
if(!pdo_fieldexists('cjdc_storeset','box_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `box_name` varchar(20) NOT NULL COMMENT '餐盒费文本'");}
if(!pdo_fieldexists('cjdc_storeset','yhq_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `yhq_name` varchar(20) NOT NULL COMMENT '收银文本'");}
if(!pdo_fieldexists('cjdc_storeset','sy_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `sy_name` varchar(20) NOT NULL COMMENT '收银文本'");}
if(!pdo_fieldexists('cjdc_storeset','dn_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `dn_name` varchar(20) NOT NULL COMMENT '店内文本'");}
if(!pdo_fieldexists('cjdc_storeset','wm_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `wm_name` varchar(20) NOT NULL COMMENT '外卖文本'");}
if(!pdo_fieldexists('cjdc_storeset','yy_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `yy_name` varchar(20) NOT NULL COMMENT '预约文本'");}
if(!pdo_fieldexists('cjdc_storeset','yhq_img')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `yhq_img` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','sy_img')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `sy_img` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','dn_img')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `dn_img` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','wm_img')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `wm_img` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','yy_img')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `yy_img` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','is_yhq')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_yhq` int(11) NOT NULL DEFAULT '1' COMMENT '是否开启优惠券'");}
if(!pdo_fieldexists('cjdc_storeset','is_sy')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_sy` int(11) NOT NULL DEFAULT '1' COMMENT '是否开启收银'");}
if(!pdo_fieldexists('cjdc_storeset','is_dn')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_dn` int(11) NOT NULL DEFAULT '1' COMMENT '是否开启店内'");}
if(!pdo_fieldexists('cjdc_storeset','is_wm')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_wm` int(11) NOT NULL DEFAULT '1' COMMENT '是否开启外卖'");}
if(!pdo_fieldexists('cjdc_storeset','is_yy')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_yy` int(11) NOT NULL DEFAULT '1' COMMENT '是否开启预约'");}
if(!pdo_fieldexists('cjdc_storeset','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `store_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','ps_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `ps_time` varchar(20) NOT NULL COMMENT '配送时间'");}
if(!pdo_fieldexists('cjdc_storeset','ps_mode')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `ps_mode` varchar(20) NOT NULL COMMENT '1.商家配送2.达达配送'");}
if(!pdo_fieldexists('cjdc_storeset','ps_jl')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `ps_jl` int(11) NOT NULL COMMENT '配送距离'");}
if(!pdo_fieldexists('cjdc_storeset','is_zt')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_zt` int(11) NOT NULL DEFAULT '2' COMMENT '1.开启2.关闭(是否自提)'");}
if(!pdo_fieldexists('cjdc_storeset','is_hdfk')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_hdfk` int(11) NOT NULL DEFAULT '2' COMMENT '1.开启2.关闭(货到付款)'");}
if(!pdo_fieldexists('cjdc_storeset','print_type')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `print_type` int(4) NOT NULL DEFAULT '1' COMMENT '1整单,2分单打印'");}
if(!pdo_fieldexists('cjdc_storeset','ztxy')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `ztxy` text NOT NULL COMMENT '自提协议'");}
if(!pdo_fieldexists('cjdc_storeset','top_style')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `top_style` int(11) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_storeset','info_style')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `info_style` int(11) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_storeset','yysm')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `yysm` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','wmsm')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `wmsm` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','dnsm')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `dnsm` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','sysm')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `sysm` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','is_wxzf')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_wxzf` int(11) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_storeset','print_mode')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `print_mode` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_storeset','is_yydc')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_yydc` int(11) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_storeset',' is_ps')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   ` is_ps` int(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_storeset','is_ps')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_ps` int(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_storeset','is_dd')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_dd` int(11) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_storeset','is_cj')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_cj` int(11) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_storeset','cj_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `cj_name` varchar(20) NOT NULL DEFAULT '餐具用量'");}
if(!pdo_fieldexists('cjdc_storeset','wmps_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `wmps_name` varchar(20) NOT NULL DEFAULT '外卖配送'");}
if(!pdo_fieldexists('cjdc_storeset','is_czztpd')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_czztpd` int(11) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_storeset','is_dcyhq')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_dcyhq` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_storeset','is_pt')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_pt` int(4) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_storeset','pt_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `pt_name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','ptsm')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `ptsm` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','pt_img')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `pt_img` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','qg_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `qg_name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','qgsm')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `qgsm` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','qg_img')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `qg_img` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','is_qg')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_qg` int(4) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_storeset','is_ydtime')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_ydtime` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_storeset','is_yyzw')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_yyzw` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_storeset','pd_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `pd_name` varchar(30) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','pdsm')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `pdsm` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','pd_img')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `pd_img` varchar(255) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','is_pd')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_pd` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_storeset','tz_src')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `tz_src` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_storeset','full_delivery')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `full_delivery` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','is_poundage')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `is_poundage` int(1) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_storeset','dn_poundage')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `dn_poundage` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','dm_poundage')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `dm_poundage` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','yd_poundage')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   `yd_poundage` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_storeset','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('cjdc_storeset','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   KEY `store_id` (`store_id`)");}
if(!pdo_fieldexists('cjdc_storeset','store_id_2')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   KEY `store_id_2` (`store_id`)");}
if(!pdo_fieldexists('cjdc_storeset','store_id_3')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   KEY `store_id_3` (`store_id`)");}
if(!pdo_fieldexists('cjdc_storeset','store_id_4')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   KEY `store_id_4` (`store_id`)");}
if(!pdo_fieldexists('cjdc_storeset','store_id_5')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   KEY `store_id_5` (`store_id`)");}
if(!pdo_fieldexists('cjdc_storeset','store_id_6')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   KEY `store_id_6` (`store_id`)");}
if(!pdo_fieldexists('cjdc_storeset','store_id_7')) {pdo_query("ALTER TABLE ".tablename('cjdc_storeset')." ADD   KEY `store_id_7` (`store_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_storetype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL COMMENT '类型名称',
  `num` int(11) NOT NULL,
  `img` varchar(200) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `poundage` varchar(20) NOT NULL,
  `dn_poundage` varchar(20) NOT NULL,
  `dm_poundage` varchar(20) NOT NULL,
  `yd_poundage` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_storetype','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_storetype')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_storetype','type_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_storetype')." ADD   `type_name` varchar(50) NOT NULL COMMENT '类型名称'");}
if(!pdo_fieldexists('cjdc_storetype','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_storetype')." ADD   `num` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_storetype','img')) {pdo_query("ALTER TABLE ".tablename('cjdc_storetype')." ADD   `img` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_storetype','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_storetype')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_storetype','poundage')) {pdo_query("ALTER TABLE ".tablename('cjdc_storetype')." ADD   `poundage` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_storetype','dn_poundage')) {pdo_query("ALTER TABLE ".tablename('cjdc_storetype')." ADD   `dn_poundage` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_storetype','dm_poundage')) {pdo_query("ALTER TABLE ".tablename('cjdc_storetype')." ADD   `dm_poundage` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_storetype','yd_poundage')) {pdo_query("ALTER TABLE ".tablename('cjdc_storetype')." ADD   `yd_poundage` varchar(20) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appid` varchar(50) NOT NULL COMMENT 'appid',
  `appsecret` varchar(50) NOT NULL COMMENT 'appsecret',
  `url_name` varchar(20) NOT NULL COMMENT '前台名称',
  `details` text NOT NULL COMMENT '关于我们',
  `url_logo` varchar(200) NOT NULL,
  `color` varchar(20) NOT NULL COMMENT '平台颜色',
  `link_name` varchar(20) NOT NULL COMMENT '后台名称',
  `link_logo` varchar(200) NOT NULL,
  `model` int(11) NOT NULL DEFAULT '3' COMMENT '1.多店2.单店',
  `default_store` int(11) NOT NULL COMMENT '默认门店id',
  `support` varchar(20) NOT NULL COMMENT '技术支持',
  `bq_logo` varchar(200) NOT NULL,
  `bq_name` varchar(50) NOT NULL COMMENT '版权名称',
  `map_key` varchar(100) NOT NULL COMMENT '腾讯地图key',
  `tz_appid` varchar(30) NOT NULL COMMENT '跳转小程序appid',
  `tz_name` varchar(20) NOT NULL COMMENT '跳转小程序名称',
  `tel` varchar(20) NOT NULL COMMENT '平台电话',
  `dada_key` varchar(50) NOT NULL COMMENT '达达key',
  `dada_secret` varchar(50) NOT NULL COMMENT '达达secret',
  `is_psxx` int(11) NOT NULL DEFAULT '1' COMMENT '配送信息是否显示',
  `jfgn` int(11) NOT NULL DEFAULT '2' COMMENT '积分功能1.开启2.关闭',
  `msgn` int(11) NOT NULL DEFAULT '2' COMMENT '平台模式功能',
  `fxgn` int(11) NOT NULL DEFAULT '2' COMMENT '分销功能',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `typeset` int(11) NOT NULL DEFAULT '1' COMMENT '是否显示分类',
  `wm_name` varchar(20) NOT NULL,
  `dc_name` varchar(20) NOT NULL,
  `yd_name` varchar(20) NOT NULL,
  `gs_img` text NOT NULL,
  `gs_details` text,
  `gs_tel` varchar(200) NOT NULL,
  `gs_time` varchar(50) NOT NULL,
  `gs_add` varchar(200) NOT NULL,
  `gs_zb` varchar(50) NOT NULL,
  `is_brand` int(11) NOT NULL DEFAULT '1',
  `kfw_appid` varchar(50) NOT NULL,
  `kfw_appsecret` varchar(50) NOT NULL,
  `fl_more` int(11) NOT NULL DEFAULT '1',
  `tx_zdmoney` decimal(10,2) NOT NULL,
  `tx_notice` text NOT NULL,
  `is_tj` int(11) NOT NULL DEFAULT '2',
  `is_mdrz` int(4) NOT NULL DEFAULT '1',
  `md_sh` int(4) NOT NULL DEFAULT '1',
  `md_sf` int(4) NOT NULL DEFAULT '1',
  `rz_details` text NOT NULL,
  `rz_title` varchar(100) NOT NULL,
  `rz_ms` text NOT NULL,
  `countdown` int(11) NOT NULL DEFAULT '3',
  `distance` int(11) NOT NULL DEFAULT '10000',
  `integral` int(11) NOT NULL,
  `integral2` int(11) NOT NULL,
  `is_jf` int(11) NOT NULL DEFAULT '2',
  ` fx_title` varchar(200) NOT NULL,
  `fx_title` varchar(200) NOT NULL,
  `is_hy` int(1) NOT NULL DEFAULT '2',
  `hygn` int(1) NOT NULL DEFAULT '2',
  `hy_discount` int(11) NOT NULL,
  `hy_details` text NOT NULL,
  `kt_details` text NOT NULL,
  `cz_notice` text NOT NULL,
  `is_cz` int(1) NOT NULL DEFAULT '2',
  `hy_note` text NOT NULL,
  `is_yuepay` int(11) NOT NULL DEFAULT '2',
  `ps_name` varchar(30) NOT NULL,
  `is_tzms` int(11) NOT NULL DEFAULT '2',
  `is_wx` int(4) NOT NULL DEFAULT '1',
  `is_yhk` int(4) NOT NULL DEFAULT '1',
  `is_sj` int(4) NOT NULL DEFAULT '1',
  `is_dada` int(4) NOT NULL DEFAULT '1',
  `is_kfw` int(4) NOT NULL DEFAULT '1',
  `is_pt` int(4) NOT NULL DEFAULT '1',
  `sh_time` int(4) NOT NULL,
  `ptgn` int(4) NOT NULL DEFAULT '1',
  `qggn` int(4) NOT NULL DEFAULT '1',
  `is_zb` int(4) NOT NULL DEFAULT '1',
  `is_qg` int(4) NOT NULL DEFAULT '2',
  `isyykg` int(4) NOT NULL DEFAULT '2',
  `zb_img` varchar(300) NOT NULL,
  `is_pay` int(1) NOT NULL DEFAULT '2',
  `pay_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_vip_delivery` int(1) NOT NULL DEFAULT '2',
  `is_tel` int(1) NOT NULL DEFAULT '2',
  `is_yy` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_system','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_system','appid')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `appid` varchar(50) NOT NULL COMMENT 'appid'");}
if(!pdo_fieldexists('cjdc_system','appsecret')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `appsecret` varchar(50) NOT NULL COMMENT 'appsecret'");}
if(!pdo_fieldexists('cjdc_system','url_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `url_name` varchar(20) NOT NULL COMMENT '前台名称'");}
if(!pdo_fieldexists('cjdc_system','details')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `details` text NOT NULL COMMENT '关于我们'");}
if(!pdo_fieldexists('cjdc_system','url_logo')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `url_logo` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','color')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `color` varchar(20) NOT NULL COMMENT '平台颜色'");}
if(!pdo_fieldexists('cjdc_system','link_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `link_name` varchar(20) NOT NULL COMMENT '后台名称'");}
if(!pdo_fieldexists('cjdc_system','link_logo')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `link_logo` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','model')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `model` int(11) NOT NULL DEFAULT '3' COMMENT '1.多店2.单店'");}
if(!pdo_fieldexists('cjdc_system','default_store')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `default_store` int(11) NOT NULL COMMENT '默认门店id'");}
if(!pdo_fieldexists('cjdc_system','support')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `support` varchar(20) NOT NULL COMMENT '技术支持'");}
if(!pdo_fieldexists('cjdc_system','bq_logo')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `bq_logo` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','bq_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `bq_name` varchar(50) NOT NULL COMMENT '版权名称'");}
if(!pdo_fieldexists('cjdc_system','map_key')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `map_key` varchar(100) NOT NULL COMMENT '腾讯地图key'");}
if(!pdo_fieldexists('cjdc_system','tz_appid')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `tz_appid` varchar(30) NOT NULL COMMENT '跳转小程序appid'");}
if(!pdo_fieldexists('cjdc_system','tz_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `tz_name` varchar(20) NOT NULL COMMENT '跳转小程序名称'");}
if(!pdo_fieldexists('cjdc_system','tel')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `tel` varchar(20) NOT NULL COMMENT '平台电话'");}
if(!pdo_fieldexists('cjdc_system','dada_key')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `dada_key` varchar(50) NOT NULL COMMENT '达达key'");}
if(!pdo_fieldexists('cjdc_system','dada_secret')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `dada_secret` varchar(50) NOT NULL COMMENT '达达secret'");}
if(!pdo_fieldexists('cjdc_system','is_psxx')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_psxx` int(11) NOT NULL DEFAULT '1' COMMENT '配送信息是否显示'");}
if(!pdo_fieldexists('cjdc_system','jfgn')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `jfgn` int(11) NOT NULL DEFAULT '2' COMMENT '积分功能1.开启2.关闭'");}
if(!pdo_fieldexists('cjdc_system','msgn')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `msgn` int(11) NOT NULL DEFAULT '2' COMMENT '平台模式功能'");}
if(!pdo_fieldexists('cjdc_system','fxgn')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `fxgn` int(11) NOT NULL DEFAULT '2' COMMENT '分销功能'");}
if(!pdo_fieldexists('cjdc_system','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('cjdc_system','typeset')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `typeset` int(11) NOT NULL DEFAULT '1' COMMENT '是否显示分类'");}
if(!pdo_fieldexists('cjdc_system','wm_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `wm_name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','dc_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `dc_name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','yd_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `yd_name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','gs_img')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `gs_img` text NOT NULL");}
if(!pdo_fieldexists('cjdc_system','gs_details')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `gs_details` text");}
if(!pdo_fieldexists('cjdc_system','gs_tel')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `gs_tel` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','gs_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `gs_time` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','gs_add')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `gs_add` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','gs_zb')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `gs_zb` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','is_brand')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_brand` int(11) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_system','kfw_appid')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `kfw_appid` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','kfw_appsecret')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `kfw_appsecret` varchar(50) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','fl_more')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `fl_more` int(11) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_system','tx_zdmoney')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `tx_zdmoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','tx_notice')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `tx_notice` text NOT NULL");}
if(!pdo_fieldexists('cjdc_system','is_tj')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_tj` int(11) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_system','is_mdrz')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_mdrz` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_system','md_sh')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `md_sh` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_system','md_sf')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `md_sf` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_system','rz_details')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `rz_details` text NOT NULL");}
if(!pdo_fieldexists('cjdc_system','rz_title')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `rz_title` varchar(100) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','rz_ms')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `rz_ms` text NOT NULL");}
if(!pdo_fieldexists('cjdc_system','countdown')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `countdown` int(11) NOT NULL DEFAULT '3'");}
if(!pdo_fieldexists('cjdc_system','distance')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `distance` int(11) NOT NULL DEFAULT '10000'");}
if(!pdo_fieldexists('cjdc_system','integral')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `integral` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','integral2')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `integral2` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','is_jf')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_jf` int(11) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_system',' fx_title')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   ` fx_title` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','fx_title')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `fx_title` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','is_hy')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_hy` int(1) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_system','hygn')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `hygn` int(1) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_system','hy_discount')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `hy_discount` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','hy_details')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `hy_details` text NOT NULL");}
if(!pdo_fieldexists('cjdc_system','kt_details')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `kt_details` text NOT NULL");}
if(!pdo_fieldexists('cjdc_system','cz_notice')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `cz_notice` text NOT NULL");}
if(!pdo_fieldexists('cjdc_system','is_cz')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_cz` int(1) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_system','hy_note')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `hy_note` text NOT NULL");}
if(!pdo_fieldexists('cjdc_system','is_yuepay')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_yuepay` int(11) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_system','ps_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `ps_name` varchar(30) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','is_tzms')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_tzms` int(11) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_system','is_wx')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_wx` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_system','is_yhk')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_yhk` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_system','is_sj')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_sj` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_system','is_dada')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_dada` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_system','is_kfw')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_kfw` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_system','is_pt')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_pt` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_system','sh_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `sh_time` int(4) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','ptgn')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `ptgn` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_system','qggn')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `qggn` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_system','is_zb')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_zb` int(4) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('cjdc_system','is_qg')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_qg` int(4) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_system','isyykg')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `isyykg` int(4) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_system','zb_img')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `zb_img` varchar(300) NOT NULL");}
if(!pdo_fieldexists('cjdc_system','is_pay')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_pay` int(1) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_system','pay_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `pay_money` decimal(10,2) NOT NULL DEFAULT '0.00'");}
if(!pdo_fieldexists('cjdc_system','is_vip_delivery')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_vip_delivery` int(1) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_system','is_tel')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_tel` int(1) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('cjdc_system','is_yy')) {pdo_query("ALTER TABLE ".tablename('cjdc_system')." ADD   `is_yy` int(1) NOT NULL DEFAULT '1'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '桌台号',
  `num` int(4) NOT NULL COMMENT '就餐人数',
  `type_id` varchar(50) NOT NULL COMMENT '桌台类型ID',
  `tag` varchar(50) NOT NULL COMMENT '标签',
  `orderby` int(11) NOT NULL COMMENT '排序',
  `status` int(4) NOT NULL COMMENT '状态，0 空闲，1开台，2已下单，3已支付',
  `uniacid` varchar(50) NOT NULL COMMENT '公众号ID',
  `store_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_table','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_table')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_table','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_table')." ADD   `name` varchar(50) NOT NULL COMMENT '桌台号'");}
if(!pdo_fieldexists('cjdc_table','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_table')." ADD   `num` int(4) NOT NULL COMMENT '就餐人数'");}
if(!pdo_fieldexists('cjdc_table','type_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_table')." ADD   `type_id` varchar(50) NOT NULL COMMENT '桌台类型ID'");}
if(!pdo_fieldexists('cjdc_table','tag')) {pdo_query("ALTER TABLE ".tablename('cjdc_table')." ADD   `tag` varchar(50) NOT NULL COMMENT '标签'");}
if(!pdo_fieldexists('cjdc_table','orderby')) {pdo_query("ALTER TABLE ".tablename('cjdc_table')." ADD   `orderby` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_table','status')) {pdo_query("ALTER TABLE ".tablename('cjdc_table')." ADD   `status` int(4) NOT NULL COMMENT '状态，0 空闲，1开台，2已下单，3已支付'");}
if(!pdo_fieldexists('cjdc_table','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_table')." ADD   `uniacid` varchar(50) NOT NULL COMMENT '公众号ID'");}
if(!pdo_fieldexists('cjdc_table','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_table')." ADD   `store_id` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_table_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名字',
  `fw_cost` decimal(10,2) NOT NULL COMMENT '服务费',
  `zd_cost` decimal(10,2) NOT NULL COMMENT '最低消费',
  `yd_cost` decimal(10,2) NOT NULL COMMENT '预付款',
  `num` int(11) NOT NULL COMMENT '数量',
  `orderby` int(11) NOT NULL,
  `store_id` int(11) NOT NULL COMMENT '商家id',
  `uniacid` varchar(50) NOT NULL COMMENT '公众号ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_table_type','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_table_type')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_table_type','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_table_type')." ADD   `name` varchar(50) NOT NULL COMMENT '名字'");}
if(!pdo_fieldexists('cjdc_table_type','fw_cost')) {pdo_query("ALTER TABLE ".tablename('cjdc_table_type')." ADD   `fw_cost` decimal(10,2) NOT NULL COMMENT '服务费'");}
if(!pdo_fieldexists('cjdc_table_type','zd_cost')) {pdo_query("ALTER TABLE ".tablename('cjdc_table_type')." ADD   `zd_cost` decimal(10,2) NOT NULL COMMENT '最低消费'");}
if(!pdo_fieldexists('cjdc_table_type','yd_cost')) {pdo_query("ALTER TABLE ".tablename('cjdc_table_type')." ADD   `yd_cost` decimal(10,2) NOT NULL COMMENT '预付款'");}
if(!pdo_fieldexists('cjdc_table_type','num')) {pdo_query("ALTER TABLE ".tablename('cjdc_table_type')." ADD   `num` int(11) NOT NULL COMMENT '数量'");}
if(!pdo_fieldexists('cjdc_table_type','orderby')) {pdo_query("ALTER TABLE ".tablename('cjdc_table_type')." ADD   `orderby` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_table_type','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_table_type')." ADD   `store_id` int(11) NOT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('cjdc_table_type','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_table_type')." ADD   `uniacid` varchar(50) NOT NULL COMMENT '公众号ID'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_txset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tx_money` decimal(10,2) NOT NULL COMMENT '最低提现金额',
  `tx_rate` int(11) NOT NULL COMMENT '手续费',
  `tx_details` text NOT NULL COMMENT '提现详情',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_txset','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_txset')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_txset','tx_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_txset')." ADD   `tx_money` decimal(10,2) NOT NULL COMMENT '最低提现金额'");}
if(!pdo_fieldexists('cjdc_txset','tx_rate')) {pdo_query("ALTER TABLE ".tablename('cjdc_txset')." ADD   `tx_rate` int(11) NOT NULL COMMENT '手续费'");}
if(!pdo_fieldexists('cjdc_txset','tx_details')) {pdo_query("ALTER TABLE ".tablename('cjdc_txset')." ADD   `tx_details` text NOT NULL COMMENT '提现详情'");}
if(!pdo_fieldexists('cjdc_txset','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_txset')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(20) NOT NULL COMMENT '分类名称',
  `order_by` int(11) NOT NULL COMMENT '排序',
  `store_id` int(11) NOT NULL COMMENT '商家id',
  `is_open` int(11) NOT NULL COMMENT '是否开启',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_type','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_type')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_type','type_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_type')." ADD   `type_name` varchar(20) NOT NULL COMMENT '分类名称'");}
if(!pdo_fieldexists('cjdc_type','order_by')) {pdo_query("ALTER TABLE ".tablename('cjdc_type')." ADD   `order_by` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_type','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_type')." ADD   `store_id` int(11) NOT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('cjdc_type','is_open')) {pdo_query("ALTER TABLE ".tablename('cjdc_type')." ADD   `is_open` int(11) NOT NULL COMMENT '是否开启'");}
if(!pdo_fieldexists('cjdc_type','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_type')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_typead` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `logo` varchar(200) NOT NULL,
  `src` varchar(100) NOT NULL COMMENT '内部链接',
  `src2` varchar(200) NOT NULL COMMENT '外部链接',
  `created_time` varchar(20) NOT NULL COMMENT '创建时间',
  `orderby` int(11) NOT NULL COMMENT '排序',
  `status` int(11) NOT NULL COMMENT '1.启用2.禁用',
  `type` int(11) NOT NULL COMMENT '类型',
  `appid` varchar(20) NOT NULL COMMENT '小程序appid',
  `title` varchar(20) NOT NULL COMMENT '小程序appid',
  `xcx_name` varchar(20) NOT NULL COMMENT '小程序名称',
  `item` int(11) NOT NULL COMMENT '1.内部2.外部3.跳转小程序',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_typead','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_typead')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_typead','logo')) {pdo_query("ALTER TABLE ".tablename('cjdc_typead')." ADD   `logo` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_typead','src')) {pdo_query("ALTER TABLE ".tablename('cjdc_typead')." ADD   `src` varchar(100) NOT NULL COMMENT '内部链接'");}
if(!pdo_fieldexists('cjdc_typead','src2')) {pdo_query("ALTER TABLE ".tablename('cjdc_typead')." ADD   `src2` varchar(200) NOT NULL COMMENT '外部链接'");}
if(!pdo_fieldexists('cjdc_typead','created_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_typead')." ADD   `created_time` varchar(20) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('cjdc_typead','orderby')) {pdo_query("ALTER TABLE ".tablename('cjdc_typead')." ADD   `orderby` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('cjdc_typead','status')) {pdo_query("ALTER TABLE ".tablename('cjdc_typead')." ADD   `status` int(11) NOT NULL COMMENT '1.启用2.禁用'");}
if(!pdo_fieldexists('cjdc_typead','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_typead')." ADD   `type` int(11) NOT NULL COMMENT '类型'");}
if(!pdo_fieldexists('cjdc_typead','appid')) {pdo_query("ALTER TABLE ".tablename('cjdc_typead')." ADD   `appid` varchar(20) NOT NULL COMMENT '小程序appid'");}
if(!pdo_fieldexists('cjdc_typead','title')) {pdo_query("ALTER TABLE ".tablename('cjdc_typead')." ADD   `title` varchar(20) NOT NULL COMMENT '小程序appid'");}
if(!pdo_fieldexists('cjdc_typead','xcx_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_typead')." ADD   `xcx_name` varchar(20) NOT NULL COMMENT '小程序名称'");}
if(!pdo_fieldexists('cjdc_typead','item')) {pdo_query("ALTER TABLE ".tablename('cjdc_typead')." ADD   `item` int(11) NOT NULL COMMENT '1.内部2.外部3.跳转小程序'");}
if(!pdo_fieldexists('cjdc_typead','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_typead')." ADD   `uniacid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8mb4 NOT NULL,
  `join_time` varchar(20) NOT NULL,
  `img` varchar(200) NOT NULL,
  `openid` text NOT NULL,
  `total_score` int(11) NOT NULL,
  `wallet` decimal(10,2) NOT NULL,
  `commission` decimal(10,2) NOT NULL,
  `day` int(11) NOT NULL,
  `order_money` decimal(10,2) NOT NULL,
  `order_number` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `dq_time` varchar(20) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `user_tel` varchar(20) NOT NULL,
  `hy_day` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=332 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_user','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_user')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_user','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_user')." ADD   `name` varchar(20) CHARACTER SET utf8mb4 NOT NULL");}
if(!pdo_fieldexists('cjdc_user','join_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_user')." ADD   `join_time` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_user','img')) {pdo_query("ALTER TABLE ".tablename('cjdc_user')." ADD   `img` varchar(200) NOT NULL");}
if(!pdo_fieldexists('cjdc_user','openid')) {pdo_query("ALTER TABLE ".tablename('cjdc_user')." ADD   `openid` text NOT NULL");}
if(!pdo_fieldexists('cjdc_user','total_score')) {pdo_query("ALTER TABLE ".tablename('cjdc_user')." ADD   `total_score` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_user','wallet')) {pdo_query("ALTER TABLE ".tablename('cjdc_user')." ADD   `wallet` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_user','commission')) {pdo_query("ALTER TABLE ".tablename('cjdc_user')." ADD   `commission` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_user','day')) {pdo_query("ALTER TABLE ".tablename('cjdc_user')." ADD   `day` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_user','order_money')) {pdo_query("ALTER TABLE ".tablename('cjdc_user')." ADD   `order_money` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('cjdc_user','order_number')) {pdo_query("ALTER TABLE ".tablename('cjdc_user')." ADD   `order_number` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_user','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_user')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_user','dq_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_user')." ADD   `dq_time` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_user','user_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_user')." ADD   `user_name` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_user','user_tel')) {pdo_query("ALTER TABLE ".tablename('cjdc_user')." ADD   `user_tel` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_user','hy_day')) {pdo_query("ALTER TABLE ".tablename('cjdc_user')." ADD   `hy_day` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_useradd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(200) NOT NULL COMMENT '地址',
  `area` varchar(50) NOT NULL COMMENT '地区',
  `user_name` varchar(20) NOT NULL COMMENT '用户id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `tel` varchar(20) NOT NULL COMMENT '电话',
  `is_default` int(11) NOT NULL COMMENT '是否默认',
  `sex` int(11) NOT NULL COMMENT '1.男2.女',
  `lat` varchar(20) NOT NULL COMMENT '经度',
  `lng` varchar(20) NOT NULL COMMENT '纬度',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_useradd','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_useradd')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_useradd','address')) {pdo_query("ALTER TABLE ".tablename('cjdc_useradd')." ADD   `address` varchar(200) NOT NULL COMMENT '地址'");}
if(!pdo_fieldexists('cjdc_useradd','area')) {pdo_query("ALTER TABLE ".tablename('cjdc_useradd')." ADD   `area` varchar(50) NOT NULL COMMENT '地区'");}
if(!pdo_fieldexists('cjdc_useradd','user_name')) {pdo_query("ALTER TABLE ".tablename('cjdc_useradd')." ADD   `user_name` varchar(20) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('cjdc_useradd','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_useradd')." ADD   `user_id` int(11) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('cjdc_useradd','tel')) {pdo_query("ALTER TABLE ".tablename('cjdc_useradd')." ADD   `tel` varchar(20) NOT NULL COMMENT '电话'");}
if(!pdo_fieldexists('cjdc_useradd','is_default')) {pdo_query("ALTER TABLE ".tablename('cjdc_useradd')." ADD   `is_default` int(11) NOT NULL COMMENT '是否默认'");}
if(!pdo_fieldexists('cjdc_useradd','sex')) {pdo_query("ALTER TABLE ".tablename('cjdc_useradd')." ADD   `sex` int(11) NOT NULL COMMENT '1.男2.女'");}
if(!pdo_fieldexists('cjdc_useradd','lat')) {pdo_query("ALTER TABLE ".tablename('cjdc_useradd')." ADD   `lat` varchar(20) NOT NULL COMMENT '经度'");}
if(!pdo_fieldexists('cjdc_useradd','lng')) {pdo_query("ALTER TABLE ".tablename('cjdc_useradd')." ADD   `lng` varchar(20) NOT NULL COMMENT '纬度'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_usercoupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `state` int(11) NOT NULL DEFAULT '2' COMMENT '1.使用2.未使用',
  `uniacid` int(11) NOT NULL,
  `time` varchar(20) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1.手动2.自动',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_usercoupons','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_usercoupons')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_usercoupons','user_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_usercoupons')." ADD   `user_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_usercoupons','coupon_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_usercoupons')." ADD   `coupon_id` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_usercoupons','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_usercoupons')." ADD   `state` int(11) NOT NULL DEFAULT '2' COMMENT '1.使用2.未使用'");}
if(!pdo_fieldexists('cjdc_usercoupons','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_usercoupons')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_usercoupons','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_usercoupons')." ADD   `time` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_usercoupons','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_usercoupons')." ADD   `type` int(11) NOT NULL COMMENT '1.手动2.自动'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cjdc_withdrawal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL COMMENT '真实姓名',
  `username` varchar(100) NOT NULL COMMENT '账号',
  `type` int(11) NOT NULL COMMENT '1支付宝 2.微信 3.银行',
  `time` varchar(20) NOT NULL COMMENT '申请时间',
  `sh_time` varchar(20) NOT NULL COMMENT '审核时间',
  `state` int(11) NOT NULL COMMENT '1.待审核 2.通过  3.拒绝',
  `tx_cost` decimal(10,2) NOT NULL COMMENT '提现金额',
  `sj_cost` decimal(10,2) NOT NULL COMMENT '实际金额',
  `store_id` int(11) NOT NULL COMMENT '商家id',
  `uniacid` int(11) NOT NULL,
  `yhk_num` varchar(20) NOT NULL,
  `tel` varchar(20) NOT NULL,
  `yh_info` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('cjdc_withdrawal','id')) {pdo_query("ALTER TABLE ".tablename('cjdc_withdrawal')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('cjdc_withdrawal','name')) {pdo_query("ALTER TABLE ".tablename('cjdc_withdrawal')." ADD   `name` varchar(10) NOT NULL COMMENT '真实姓名'");}
if(!pdo_fieldexists('cjdc_withdrawal','username')) {pdo_query("ALTER TABLE ".tablename('cjdc_withdrawal')." ADD   `username` varchar(100) NOT NULL COMMENT '账号'");}
if(!pdo_fieldexists('cjdc_withdrawal','type')) {pdo_query("ALTER TABLE ".tablename('cjdc_withdrawal')." ADD   `type` int(11) NOT NULL COMMENT '1支付宝 2.微信 3.银行'");}
if(!pdo_fieldexists('cjdc_withdrawal','time')) {pdo_query("ALTER TABLE ".tablename('cjdc_withdrawal')." ADD   `time` varchar(20) NOT NULL COMMENT '申请时间'");}
if(!pdo_fieldexists('cjdc_withdrawal','sh_time')) {pdo_query("ALTER TABLE ".tablename('cjdc_withdrawal')." ADD   `sh_time` varchar(20) NOT NULL COMMENT '审核时间'");}
if(!pdo_fieldexists('cjdc_withdrawal','state')) {pdo_query("ALTER TABLE ".tablename('cjdc_withdrawal')." ADD   `state` int(11) NOT NULL COMMENT '1.待审核 2.通过  3.拒绝'");}
if(!pdo_fieldexists('cjdc_withdrawal','tx_cost')) {pdo_query("ALTER TABLE ".tablename('cjdc_withdrawal')." ADD   `tx_cost` decimal(10,2) NOT NULL COMMENT '提现金额'");}
if(!pdo_fieldexists('cjdc_withdrawal','sj_cost')) {pdo_query("ALTER TABLE ".tablename('cjdc_withdrawal')." ADD   `sj_cost` decimal(10,2) NOT NULL COMMENT '实际金额'");}
if(!pdo_fieldexists('cjdc_withdrawal','store_id')) {pdo_query("ALTER TABLE ".tablename('cjdc_withdrawal')." ADD   `store_id` int(11) NOT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('cjdc_withdrawal','uniacid')) {pdo_query("ALTER TABLE ".tablename('cjdc_withdrawal')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('cjdc_withdrawal','yhk_num')) {pdo_query("ALTER TABLE ".tablename('cjdc_withdrawal')." ADD   `yhk_num` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_withdrawal','tel')) {pdo_query("ALTER TABLE ".tablename('cjdc_withdrawal')." ADD   `tel` varchar(20) NOT NULL");}
if(!pdo_fieldexists('cjdc_withdrawal','yh_info')) {pdo_query("ALTER TABLE ".tablename('cjdc_withdrawal')." ADD   `yh_info` varchar(100) NOT NULL");}
