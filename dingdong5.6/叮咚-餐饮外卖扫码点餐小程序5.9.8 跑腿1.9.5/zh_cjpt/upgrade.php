<?php
$sql="CREATE TABLE IF NOT EXISTS `ims_cjpt_ad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `logo` varchar(100) NOT NULL COMMENT '图片',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_cjpt_bind` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pt_uniacid` int(11) NOT NULL COMMENT '跑腿小程序ID',
  `cy_uniacid` int(11) NOT NULL COMMENT '餐饮小程序ID',
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='绑定表';
CREATE TABLE IF NOT EXISTS `ims_cjpt_dispatch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(30) NOT NULL COMMENT '第三方订单ID',
  `goods_info` varchar(255) NOT NULL COMMENT '商品信息',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `sender_name` varchar(50) NOT NULL COMMENT '发货人',
  `sender_address` varchar(150) NOT NULL COMMENT '发货地址',
  `sender_tel` varchar(20) NOT NULL COMMENT '发货人联系方式',
  `sender_lat` varchar(15) NOT NULL COMMENT '发货人维度',
  `sender_lng` varchar(15) NOT NULL COMMENT '发货人经度',
  `receiver_name` varchar(50) NOT NULL COMMENT '收货人',
  `receiver_address` varchar(150) NOT NULL COMMENT '收货人地址',
  `receiver_tel` varchar(20) NOT NULL COMMENT '收货人电话',
  `receiver_lat` varchar(15) NOT NULL COMMENT '收货人维度',
  `receiver_lng` varchar(15) NOT NULL COMMENT '收货人经度',
  `ps_num` varchar(15) NOT NULL COMMENT '配送单号',
  `ps_money` decimal(10,2) NOT NULL COMMENT '配送费',
  `qs_id` int(11) NOT NULL COMMENT '骑手id',
  `state` int(4) NOT NULL COMMENT '1待接单，2接单，3取货，4完成,5取消',
  `time` int(11) NOT NULL COMMENT '推送时间',
  `jd_time` int(11) NOT NULL COMMENT '接单时间',
  `dd_time` int(11) NOT NULL COMMENT '到店时间',
  `wc_time` int(11) NOT NULL COMMENT '完成时间',
  `uniacid` int(11) NOT NULL,
  `note` varchar(255) NOT NULL,
  `store_logo` varchar(255) NOT NULL,
  `yh_money` decimal(10,2) NOT NULL,
  `origin_id` int(11) NOT NULL,
  `item` int(4) NOT NULL DEFAULT '1',
  `pay_type` int(4) NOT NULL DEFAULT '1',
  `delivery_time` varchar(20) NOT NULL DEFAULT '尽快送达',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_cjpt_fee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start` int(11) NOT NULL COMMENT '配送起始值',
  `end` int(11) NOT NULL COMMENT '配送结束值',
  `money` decimal(10,2) NOT NULL COMMENT '价格',
  `num` int(11) NOT NULL COMMENT '排序',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_cjpt_help` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(200) NOT NULL COMMENT '标题',
  `answer` text NOT NULL COMMENT '回答',
  `sort` int(4) NOT NULL COMMENT '排序',
  `uniacid` varchar(50) NOT NULL,
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_cjpt_mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT '发送帐号用户名',
  `password` varchar(50) NOT NULL COMMENT 'smtp客户端授权密码',
  `type` varchar(10) NOT NULL COMMENT 'qq/163',
  `sender` varchar(50) NOT NULL COMMENT '发件人名称',
  `signature` text NOT NULL COMMENT '邮件签名',
  `uniacid` varchar(50) NOT NULL,
  `is_email` int(4) DEFAULT '2' COMMENT '1开启，2关闭',
  `body` text NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邮件配置表';
CREATE TABLE IF NOT EXISTS `ims_cjpt_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(200) NOT NULL COMMENT '标题',
  `answer` text NOT NULL COMMENT '回答',
  `sort` int(4) NOT NULL COMMENT '排序',
  `uniacid` varchar(50) NOT NULL,
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_cjpt_rider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(50) NOT NULL,
  `name` varchar(30) NOT NULL COMMENT '姓名',
  `tel` varchar(20) NOT NULL COMMENT '电话',
  `logo` varchar(255) NOT NULL COMMENT 'logo',
  `pwd` varchar(50) NOT NULL COMMENT '登入密码',
  `zm_img` varchar(255) NOT NULL COMMENT '正面照',
  `fm_img` varchar(255) NOT NULL COMMENT '反面照',
  `state` int(4) NOT NULL COMMENT '1待审核，2已审核,3拒绝',
  `status` int(4) NOT NULL DEFAULT '1' COMMENT '1在岗，2休息',
  `time` int(11) NOT NULL COMMENT '时间',
  `sh_time` int(11) NOT NULL COMMENT '审核时间',
  `uniacid` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='骑手表';
CREATE TABLE IF NOT EXISTS `ims_cjpt_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appid` varchar(50) NOT NULL COMMENT 'appid',
  `appsecret` varchar(50) NOT NULL COMMENT 'appsecret',
  `mchid` varchar(20) NOT NULL,
  `wxkey` varchar(50) NOT NULL,
  `apiclient_cert` text NOT NULL,
  `apiclient_key` text NOT NULL,
  `client_ip` varchar(20) NOT NULL,
  `url_name` varchar(20) NOT NULL COMMENT '前台名称',
  `details` text NOT NULL COMMENT '关于我们',
  `logo` varchar(100) NOT NULL COMMENT 'logo',
  `bj_logo` varchar(255) NOT NULL COMMENT '注册背景图',
  `color` varchar(20) NOT NULL COMMENT '平台颜色',
  `rz_details` text NOT NULL COMMENT '入驻协议',
  `map_key` varchar(100) NOT NULL COMMENT '腾讯地图key',
  `tel` varchar(20) NOT NULL COMMENT '平台电话',
  `is_dxyz` int(4) NOT NULL DEFAULT '2' COMMENT '1开启,2关闭',
  `appkey` varchar(32) NOT NULL COMMENT '短信key',
  `tpl_id` varchar(10) NOT NULL COMMENT '模板ID',
  `tpl_id2` varchar(10) NOT NULL COMMENT '忘记密码模板ID',
  `tpl_id3` varchar(10) NOT NULL COMMENT 'j接单短信模板ID',
  `tpl_id4` varchar(10) NOT NULL COMMENT '派单短息模板id',
  `tx_sxf` varchar(10) NOT NULL COMMENT '提现手续费',
  `tx_zdmoney` decimal(10,2) NOT NULL COMMENT '提现最低金额',
  `tx_notice` text NOT NULL COMMENT '提现须知',
  `uniacid` int(11) NOT NULL,
  `distance` varchar(10) NOT NULL DEFAULT '5',
  `db_logo` varchar(255) NOT NULL,
  `db_content` text NOT NULL,
  `yc_money` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_cjpt_withdrawal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL COMMENT '真实姓名',
  `username` varchar(100) NOT NULL COMMENT '账号',
  `type` int(11) NOT NULL COMMENT '1支付宝 2.微信 3.银行',
  `time` int(11) NOT NULL COMMENT '申请时间',
  `sh_time` int(11) NOT NULL COMMENT '审核时间',
  `state` int(11) NOT NULL COMMENT '1.待审核 2.通过  3.拒绝',
  `tx_cost` decimal(10,2) NOT NULL COMMENT '提现金额',
  `sj_cost` decimal(10,2) NOT NULL COMMENT '实际金额',
  `qs_id` int(11) NOT NULL COMMENT '骑手id',
  `uniacid` int(11) NOT NULL,
  `tx_num` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
pdo_run($sql);
if(!pdo_fieldexists('cjpt_ad',  'id')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_ad')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('cjpt_ad',  'logo')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_ad')." ADD `logo` varchar(100) NOT NULL COMMENT '图片';");
}
if(!pdo_fieldexists('cjpt_ad',  'src')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_ad')." ADD `src` varchar(100) NOT NULL COMMENT '内部链接';");
}
if(!pdo_fieldexists('cjpt_ad',  'src2')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_ad')." ADD `src2` varchar(200) NOT NULL COMMENT '外部链接';");
}
if(!pdo_fieldexists('cjpt_ad',  'created_time')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_ad')." ADD `created_time` varchar(20) NOT NULL COMMENT '创建时间';");
}
if(!pdo_fieldexists('cjpt_ad',  'orderby')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_ad')." ADD `orderby` int(11) NOT NULL COMMENT '排序';");
}
if(!pdo_fieldexists('cjpt_ad',  'status')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_ad')." ADD `status` int(11) NOT NULL COMMENT '1.启用2.禁用';");
}
if(!pdo_fieldexists('cjpt_ad',  'type')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_ad')." ADD `type` int(11) NOT NULL COMMENT '类型';");
}
if(!pdo_fieldexists('cjpt_ad',  'appid')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_ad')." ADD `appid` varchar(20) NOT NULL COMMENT '小程序appid';");
}
if(!pdo_fieldexists('cjpt_ad',  'title')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_ad')." ADD `title` varchar(20) NOT NULL COMMENT '小程序appid';");
}
if(!pdo_fieldexists('cjpt_ad',  'xcx_name')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_ad')." ADD `xcx_name` varchar(20) NOT NULL COMMENT '小程序名称';");
}
if(!pdo_fieldexists('cjpt_ad',  'item')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_ad')." ADD `item` int(11) NOT NULL COMMENT '1.内部2.外部3.跳转小程序';");
}
if(!pdo_fieldexists('cjpt_ad',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_ad')." ADD `uniacid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_bind',  'id')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_bind')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('cjpt_bind',  'pt_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_bind')." ADD `pt_uniacid` int(11) NOT NULL COMMENT '跑腿小程序ID';");
}
if(!pdo_fieldexists('cjpt_bind',  'cy_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_bind')." ADD `cy_uniacid` int(11) NOT NULL COMMENT '餐饮小程序ID';");
}
if(!pdo_fieldexists('cjpt_bind',  'time')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_bind')." ADD `time` int(11) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_dispatch',  'id')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('cjpt_dispatch',  'order_id')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `order_id` varchar(30) NOT NULL COMMENT '第三方订单ID';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'goods_info')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `goods_info` varchar(255) NOT NULL COMMENT '商品信息';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'goods_price')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `goods_price` decimal(10,2) NOT NULL COMMENT '商品价格';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'sender_name')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `sender_name` varchar(50) NOT NULL COMMENT '发货人';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'sender_address')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `sender_address` varchar(150) NOT NULL COMMENT '发货地址';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'sender_tel')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `sender_tel` varchar(20) NOT NULL COMMENT '发货人联系方式';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'sender_lat')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `sender_lat` varchar(15) NOT NULL COMMENT '发货人维度';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'sender_lng')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `sender_lng` varchar(15) NOT NULL COMMENT '发货人经度';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'receiver_name')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `receiver_name` varchar(50) NOT NULL COMMENT '收货人';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'receiver_address')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `receiver_address` varchar(150) NOT NULL COMMENT '收货人地址';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'receiver_tel')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `receiver_tel` varchar(20) NOT NULL COMMENT '收货人电话';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'receiver_lat')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `receiver_lat` varchar(15) NOT NULL COMMENT '收货人维度';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'receiver_lng')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `receiver_lng` varchar(15) NOT NULL COMMENT '收货人经度';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'ps_num')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `ps_num` varchar(15) NOT NULL COMMENT '配送单号';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'ps_money')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `ps_money` decimal(10,2) NOT NULL COMMENT '配送费';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'qs_id')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `qs_id` int(11) NOT NULL COMMENT '骑手id';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'state')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `state` int(4) NOT NULL COMMENT '1待接单，2接单，3取货，4完成,5取消';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'time')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `time` int(11) NOT NULL COMMENT '推送时间';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'jd_time')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `jd_time` int(11) NOT NULL COMMENT '接单时间';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'dd_time')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `dd_time` int(11) NOT NULL COMMENT '到店时间';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'wc_time')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `wc_time` int(11) NOT NULL COMMENT '完成时间';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `uniacid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_dispatch',  'note')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `note` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_dispatch',  'store_logo')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `store_logo` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_dispatch',  'yh_money')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `yh_money` decimal(10,2) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_dispatch',  'origin_id')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `origin_id` int(11) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_dispatch',  'item')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `item` int(4) NOT NULL DEFAULT '1';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'pay_type')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `pay_type` int(4) NOT NULL DEFAULT '1';");
}
if(!pdo_fieldexists('cjpt_dispatch',  'delivery_time')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_dispatch')." ADD `delivery_time` varchar(20) NOT NULL DEFAULT '尽快送达';");
}
if(!pdo_fieldexists('cjpt_fee',  'id')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_fee')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('cjpt_fee',  'start')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_fee')." ADD `start` int(11) NOT NULL COMMENT '配送起始值';");
}
if(!pdo_fieldexists('cjpt_fee',  'end')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_fee')." ADD `end` int(11) NOT NULL COMMENT '配送结束值';");
}
if(!pdo_fieldexists('cjpt_fee',  'money')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_fee')." ADD `money` decimal(10,2) NOT NULL COMMENT '价格';");
}
if(!pdo_fieldexists('cjpt_fee',  'num')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_fee')." ADD `num` int(11) NOT NULL COMMENT '排序';");
}
if(!pdo_fieldexists('cjpt_fee',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_fee')." ADD `uniacid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_help',  'id')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_help')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('cjpt_help',  'question')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_help')." ADD `question` varchar(200) NOT NULL COMMENT '标题';");
}
if(!pdo_fieldexists('cjpt_help',  'answer')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_help')." ADD `answer` text NOT NULL COMMENT '回答';");
}
if(!pdo_fieldexists('cjpt_help',  'sort')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_help')." ADD `sort` int(4) NOT NULL COMMENT '排序';");
}
if(!pdo_fieldexists('cjpt_help',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_help')." ADD `uniacid` varchar(50) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_help',  'created_time')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_help')." ADD `created_time` datetime NOT NULL;");
}
if(!pdo_fieldexists('cjpt_mail',  'id')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_mail')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('cjpt_mail',  'username')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_mail')." ADD `username` varchar(50) NOT NULL COMMENT '发送帐号用户名';");
}
if(!pdo_fieldexists('cjpt_mail',  'password')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_mail')." ADD `password` varchar(50) NOT NULL COMMENT 'smtp客户端授权密码';");
}
if(!pdo_fieldexists('cjpt_mail',  'type')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_mail')." ADD `type` varchar(10) NOT NULL COMMENT 'qq/163';");
}
if(!pdo_fieldexists('cjpt_mail',  'sender')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_mail')." ADD `sender` varchar(50) NOT NULL COMMENT '发件人名称';");
}
if(!pdo_fieldexists('cjpt_mail',  'signature')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_mail')." ADD `signature` text NOT NULL COMMENT '邮件签名';");
}
if(!pdo_fieldexists('cjpt_mail',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_mail')." ADD `uniacid` varchar(50) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_mail',  'is_email')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_mail')." ADD `is_email` int(4) DEFAULT '2' COMMENT '1开启，2关闭';");
}
if(!pdo_fieldexists('cjpt_mail',  'body')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_mail')." ADD `body` text NOT NULL COMMENT '内容';");
}
if(!pdo_fieldexists('cjpt_notice',  'id')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_notice')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('cjpt_notice',  'question')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_notice')." ADD `question` varchar(200) NOT NULL COMMENT '标题';");
}
if(!pdo_fieldexists('cjpt_notice',  'answer')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_notice')." ADD `answer` text NOT NULL COMMENT '回答';");
}
if(!pdo_fieldexists('cjpt_notice',  'sort')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_notice')." ADD `sort` int(4) NOT NULL COMMENT '排序';");
}
if(!pdo_fieldexists('cjpt_notice',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_notice')." ADD `uniacid` varchar(50) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_notice',  'created_time')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_notice')." ADD `created_time` datetime NOT NULL;");
}
if(!pdo_fieldexists('cjpt_rider',  'id')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_rider')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('cjpt_rider',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_rider')." ADD `openid` varchar(50) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_rider',  'name')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_rider')." ADD `name` varchar(30) NOT NULL COMMENT '姓名';");
}
if(!pdo_fieldexists('cjpt_rider',  'tel')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_rider')." ADD `tel` varchar(20) NOT NULL COMMENT '电话';");
}
if(!pdo_fieldexists('cjpt_rider',  'logo')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_rider')." ADD `logo` varchar(255) NOT NULL COMMENT 'logo';");
}
if(!pdo_fieldexists('cjpt_rider',  'pwd')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_rider')." ADD `pwd` varchar(50) NOT NULL COMMENT '登入密码';");
}
if(!pdo_fieldexists('cjpt_rider',  'zm_img')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_rider')." ADD `zm_img` varchar(255) NOT NULL COMMENT '正面照';");
}
if(!pdo_fieldexists('cjpt_rider',  'fm_img')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_rider')." ADD `fm_img` varchar(255) NOT NULL COMMENT '反面照';");
}
if(!pdo_fieldexists('cjpt_rider',  'state')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_rider')." ADD `state` int(4) NOT NULL COMMENT '1待审核，2已审核,3拒绝';");
}
if(!pdo_fieldexists('cjpt_rider',  'status')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_rider')." ADD `status` int(4) NOT NULL DEFAULT '1' COMMENT '1在岗，2休息';");
}
if(!pdo_fieldexists('cjpt_rider',  'time')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_rider')." ADD `time` int(11) NOT NULL COMMENT '时间';");
}
if(!pdo_fieldexists('cjpt_rider',  'sh_time')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_rider')." ADD `sh_time` int(11) NOT NULL COMMENT '审核时间';");
}
if(!pdo_fieldexists('cjpt_rider',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_rider')." ADD `uniacid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_rider',  'email')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_rider')." ADD `email` varchar(50) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_system',  'id')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('cjpt_system',  'appid')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `appid` varchar(50) NOT NULL COMMENT 'appid';");
}
if(!pdo_fieldexists('cjpt_system',  'appsecret')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `appsecret` varchar(50) NOT NULL COMMENT 'appsecret';");
}
if(!pdo_fieldexists('cjpt_system',  'mchid')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `mchid` varchar(20) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_system',  'wxkey')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `wxkey` varchar(50) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_system',  'apiclient_cert')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `apiclient_cert` text NOT NULL;");
}
if(!pdo_fieldexists('cjpt_system',  'apiclient_key')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `apiclient_key` text NOT NULL;");
}
if(!pdo_fieldexists('cjpt_system',  'client_ip')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `client_ip` varchar(20) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_system',  'url_name')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `url_name` varchar(20) NOT NULL COMMENT '前台名称';");
}
if(!pdo_fieldexists('cjpt_system',  'details')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `details` text NOT NULL COMMENT '关于我们';");
}
if(!pdo_fieldexists('cjpt_system',  'logo')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `logo` varchar(100) NOT NULL COMMENT 'logo';");
}
if(!pdo_fieldexists('cjpt_system',  'bj_logo')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `bj_logo` varchar(255) NOT NULL COMMENT '注册背景图';");
}
if(!pdo_fieldexists('cjpt_system',  'color')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `color` varchar(20) NOT NULL COMMENT '平台颜色';");
}
if(!pdo_fieldexists('cjpt_system',  'rz_details')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `rz_details` text NOT NULL COMMENT '入驻协议';");
}
if(!pdo_fieldexists('cjpt_system',  'map_key')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `map_key` varchar(100) NOT NULL COMMENT '腾讯地图key';");
}
if(!pdo_fieldexists('cjpt_system',  'tel')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `tel` varchar(20) NOT NULL COMMENT '平台电话';");
}
if(!pdo_fieldexists('cjpt_system',  'is_dxyz')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `is_dxyz` int(4) NOT NULL DEFAULT '2' COMMENT '1开启,2关闭';");
}
if(!pdo_fieldexists('cjpt_system',  'appkey')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `appkey` varchar(32) NOT NULL COMMENT '短信key';");
}
if(!pdo_fieldexists('cjpt_system',  'tpl_id')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `tpl_id` varchar(10) NOT NULL COMMENT '模板ID';");
}
if(!pdo_fieldexists('cjpt_system',  'tpl_id2')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `tpl_id2` varchar(10) NOT NULL COMMENT '忘记密码模板ID';");
}
if(!pdo_fieldexists('cjpt_system',  'tpl_id3')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `tpl_id3` varchar(10) NOT NULL COMMENT 'j接单短信模板ID';");
}
if(!pdo_fieldexists('cjpt_system',  'tpl_id4')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `tpl_id4` varchar(10) NOT NULL COMMENT '派单短息模板id';");
}
if(!pdo_fieldexists('cjpt_system',  'tx_sxf')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `tx_sxf` varchar(10) NOT NULL COMMENT '提现手续费';");
}
if(!pdo_fieldexists('cjpt_system',  'tx_zdmoney')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `tx_zdmoney` decimal(10,2) NOT NULL COMMENT '提现最低金额';");
}
if(!pdo_fieldexists('cjpt_system',  'tx_notice')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `tx_notice` text NOT NULL COMMENT '提现须知';");
}
if(!pdo_fieldexists('cjpt_system',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `uniacid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_system',  'distance')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `distance` varchar(10) NOT NULL DEFAULT '5';");
}
if(!pdo_fieldexists('cjpt_system',  'db_logo')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `db_logo` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_system',  'db_content')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `db_content` text NOT NULL;");
}
if(!pdo_fieldexists('cjpt_system',  'yc_money')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_system')." ADD `yc_money` decimal(10,2) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_withdrawal',  'id')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_withdrawal')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('cjpt_withdrawal',  'name')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_withdrawal')." ADD `name` varchar(10) NOT NULL COMMENT '真实姓名';");
}
if(!pdo_fieldexists('cjpt_withdrawal',  'username')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_withdrawal')." ADD `username` varchar(100) NOT NULL COMMENT '账号';");
}
if(!pdo_fieldexists('cjpt_withdrawal',  'type')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_withdrawal')." ADD `type` int(11) NOT NULL COMMENT '1支付宝 2.微信 3.银行';");
}
if(!pdo_fieldexists('cjpt_withdrawal',  'time')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_withdrawal')." ADD `time` int(11) NOT NULL COMMENT '申请时间';");
}
if(!pdo_fieldexists('cjpt_withdrawal',  'sh_time')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_withdrawal')." ADD `sh_time` int(11) NOT NULL COMMENT '审核时间';");
}
if(!pdo_fieldexists('cjpt_withdrawal',  'state')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_withdrawal')." ADD `state` int(11) NOT NULL COMMENT '1.待审核 2.通过  3.拒绝';");
}
if(!pdo_fieldexists('cjpt_withdrawal',  'tx_cost')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_withdrawal')." ADD `tx_cost` decimal(10,2) NOT NULL COMMENT '提现金额';");
}
if(!pdo_fieldexists('cjpt_withdrawal',  'sj_cost')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_withdrawal')." ADD `sj_cost` decimal(10,2) NOT NULL COMMENT '实际金额';");
}
if(!pdo_fieldexists('cjpt_withdrawal',  'qs_id')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_withdrawal')." ADD `qs_id` int(11) NOT NULL COMMENT '骑手id';");
}
if(!pdo_fieldexists('cjpt_withdrawal',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_withdrawal')." ADD `uniacid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('cjpt_withdrawal',  'tx_num')) {
	pdo_query("ALTER TABLE ".tablename('cjpt_withdrawal')." ADD `tx_num` varchar(30) NOT NULL;");
}

?>