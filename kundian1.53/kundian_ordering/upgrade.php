<?php
//升级数据表
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_about` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `logo_img` text NOT NULL COMMENT 'logo',
  `merchant_name` char(200) NOT NULL COMMENT '名称',
  `merchant_desc` text NOT NULL COMMENT '简介',
  `wxchat` char(100) NOT NULL COMMENT '微信',
  `phone` char(20) NOT NULL COMMENT '联系电话',
  `in_time` char(50) NOT NULL COMMENT '营业时间',
  `address` char(200) NOT NULL COMMENT '地址',
  `begin_price` float NOT NULL COMMENT '起送价',
  `send_price` float NOT NULL COMMENT '配送费',
  `tags` char(200) NOT NULL COMMENT '提示',
  `longitude` char(200) NOT NULL COMMENT '经度',
  `latitude` char(200) NOT NULL COMMENT '伟度',
  `send_time` int(11) NOT NULL COMMENT '配送时间',
  `is_jian_send_price` tinyint(1) NOT NULL COMMENT '是否开启满减配送费',
  `man_price` float NOT NULL COMMENT '满多少减',
  `center_banner` text NOT NULL COMMENT '个人中心banner',
  `ordering_title` char(100) NOT NULL COMMENT '预定页title',
  `business_mode` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否开启快餐',
  `package_price` float NOT NULL DEFAULT '0' COMMENT '打包费',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_about','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','logo_img')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `logo_img` text NOT NULL COMMENT 'logo'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','merchant_name')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `merchant_name` char(200) NOT NULL COMMENT '名称'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','merchant_desc')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `merchant_desc` text NOT NULL COMMENT '简介'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','wxchat')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `wxchat` char(100) NOT NULL COMMENT '微信'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','phone')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `phone` char(20) NOT NULL COMMENT '联系电话'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','in_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `in_time` char(50) NOT NULL COMMENT '营业时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','address')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `address` char(200) NOT NULL COMMENT '地址'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','begin_price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `begin_price` float NOT NULL COMMENT '起送价'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','send_price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `send_price` float NOT NULL COMMENT '配送费'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','tags')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `tags` char(200) NOT NULL COMMENT '提示'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','longitude')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `longitude` char(200) NOT NULL COMMENT '经度'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','latitude')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `latitude` char(200) NOT NULL COMMENT '伟度'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','send_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `send_time` int(11) NOT NULL COMMENT '配送时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','is_jian_send_price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `is_jian_send_price` tinyint(1) NOT NULL COMMENT '是否开启满减配送费'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','man_price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `man_price` float NOT NULL COMMENT '满多少减'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','center_banner')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `center_banner` text NOT NULL COMMENT '个人中心banner'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','ordering_title')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `ordering_title` char(100) NOT NULL COMMENT '预定页title'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','business_mode')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `business_mode` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否开启快餐'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','package_price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   `package_price` float NOT NULL DEFAULT '0' COMMENT '打包费'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_about','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_about')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `region` char(100) NOT NULL,
  `address` char(200) NOT NULL,
  `name` char(50) NOT NULL,
  `phone` char(20) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `is_default` tinyint(1) NOT NULL COMMENT '1默认 0其他',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_address','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_address')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_cqkundian_ordering_address','uid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_address')." ADD   `uid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_address','region')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_address')." ADD   `region` char(100) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_address','address')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_address')." ADD   `address` char(200) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_address','name')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_address')." ADD   `name` char(50) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_address','phone')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_address')." ADD   `phone` char(20) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_address','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_address')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_address','is_default')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_address')." ADD   `is_default` tinyint(1) NOT NULL COMMENT '1默认 0其他'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_address','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_address')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_batch` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `title` char(100) NOT NULL COMMENT '标题',
  `count` int(11) NOT NULL COMMENT '张数',
  `prefix` char(20) NOT NULL COMMENT '批次编号前缀',
  `status` tinyint(1) NOT NULL COMMENT '1/启用 2/未启用',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `lid` int(11) NOT NULL COMMENT '等级id',
  `rank` int(11) NOT NULL COMMENT '排序',
  `is_create` tinyint(1) NOT NULL COMMENT '1/未生成 2/已生成',
  `expire_time` int(11) NOT NULL COMMENT '过期时间',
  `price` float NOT NULL COMMENT '单价',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_batch','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_batch')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_batch','title')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_batch')." ADD   `title` char(100) NOT NULL COMMENT '标题'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_batch','count')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_batch')." ADD   `count` int(11) NOT NULL COMMENT '张数'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_batch','prefix')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_batch')." ADD   `prefix` char(20) NOT NULL COMMENT '批次编号前缀'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_batch','status')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_batch')." ADD   `status` tinyint(1) NOT NULL COMMENT '1/启用 2/未启用'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_batch','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_batch')." ADD   `create_time` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_batch','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_batch')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_batch','lid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_batch')." ADD   `lid` int(11) NOT NULL COMMENT '等级id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_batch','rank')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_batch')." ADD   `rank` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_batch','is_create')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_batch')." ADD   `is_create` tinyint(1) NOT NULL COMMENT '1/未生成 2/已生成'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_batch','expire_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_batch')." ADD   `expire_time` int(11) NOT NULL COMMENT '过期时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_batch','price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_batch')." ADD   `price` float NOT NULL COMMENT '单价'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_batch','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_batch')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_cancel_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `phone` char(20) NOT NULL COMMENT 'phone',
  `pwd` char(50) NOT NULL COMMENT '密码',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `status` tinyint(1) NOT NULL COMMENT '0/未启用 1/已启用',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `rank` int(11) NOT NULL COMMENT '排序',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `wx_openid` char(200) NOT NULL COMMENT '接收信息',
  `type` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_person','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_person')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_person','phone')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_person')." ADD   `phone` char(20) NOT NULL COMMENT 'phone'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_person','pwd')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_person')." ADD   `pwd` char(50) NOT NULL COMMENT '密码'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_person','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_person')." ADD   `create_time` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_person','status')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_person')." ADD   `status` tinyint(1) NOT NULL COMMENT '0/未启用 1/已启用'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_person','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_person')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_person','rank')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_person')." ADD   `rank` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_person','uid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_person')." ADD   `uid` int(11) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_person','wx_openid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_person')." ADD   `wx_openid` char(200) NOT NULL COMMENT '接收信息'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_person','type')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_person')." ADD   `type` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_person','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_person')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_cancel_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `tid` int(11) NOT NULL COMMENT '卡号id',
  `card_num` char(50) NOT NULL COMMENT '卡号id',
  `cid` int(11) NOT NULL COMMENT '核销员id',
  `phone` char(100) NOT NULL COMMENT '核销员电话',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_record','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_record')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_record','tid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_record')." ADD   `tid` int(11) NOT NULL COMMENT '卡号id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_record','card_num')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_record')." ADD   `card_num` char(50) NOT NULL COMMENT '卡号id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_record','cid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_record')." ADD   `cid` int(11) NOT NULL COMMENT '核销员id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_record','phone')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_record')." ADD   `phone` char(100) NOT NULL COMMENT '核销员电话'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_record','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_record')." ADD   `create_time` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_record','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_record')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cancel_record','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cancel_record')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `pid` int(11) NOT NULL COMMENT '商品id',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `create_time` int(11) NOT NULL COMMENT '添加时间',
  `count` int(11) NOT NULL COMMENT '数量',
  `price` float NOT NULL COMMENT '单价',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `goods_name` char(200) NOT NULL COMMENT '商品名称',
  `type_id` int(11) NOT NULL COMMENT '商品分类',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_cart','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cart')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cart','pid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cart')." ADD   `pid` int(11) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cart','uid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cart')." ADD   `uid` int(11) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cart','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cart')." ADD   `create_time` int(11) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cart','count')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cart')." ADD   `count` int(11) NOT NULL COMMENT '数量'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cart','price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cart')." ADD   `price` float NOT NULL COMMENT '单价'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cart','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cart')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cart','goods_name')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cart')." ADD   `goods_name` char(200) NOT NULL COMMENT '商品名称'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cart','type_id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cart')." ADD   `type_id` int(11) NOT NULL COMMENT '商品分类'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_cart','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_cart')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `customer_name` char(100) NOT NULL COMMENT '客户名称',
  `phone` char(20) NOT NULL COMMENT '联系电话',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `buy_count` int(11) NOT NULL COMMENT '购买数量',
  `address` char(200) NOT NULL COMMENT '地址',
  `price` float NOT NULL COMMENT '金额',
  `remark` char(200) NOT NULL COMMENT '备注',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `rank` int(11) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_customer','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_customer')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_customer','customer_name')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_customer')." ADD   `customer_name` char(100) NOT NULL COMMENT '客户名称'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_customer','phone')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_customer')." ADD   `phone` char(20) NOT NULL COMMENT '联系电话'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_customer','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_customer')." ADD   `create_time` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_customer','buy_count')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_customer')." ADD   `buy_count` int(11) NOT NULL COMMENT '购买数量'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_customer','address')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_customer')." ADD   `address` char(200) NOT NULL COMMENT '地址'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_customer','price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_customer')." ADD   `price` float NOT NULL COMMENT '金额'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_customer','remark')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_customer')." ADD   `remark` char(200) NOT NULL COMMENT '备注'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_customer','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_customer')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_customer','rank')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_customer')." ADD   `rank` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_customer','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_customer')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_desk` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` char(100) NOT NULL COMMENT '餐桌名称',
  `person` int(11) NOT NULL COMMENT '容纳人数',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `code` text NOT NULL COMMENT '餐桌二维码',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `rank` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '0/未开餐 1/已开餐 2/已结账',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_desk','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk','name')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk')." ADD   `name` char(100) NOT NULL COMMENT '餐桌名称'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk','person')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk')." ADD   `person` int(11) NOT NULL COMMENT '容纳人数'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk')." ADD   `create_time` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk','code')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk')." ADD   `code` text NOT NULL COMMENT '餐桌二维码'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk','rank')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk')." ADD   `rank` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk','status')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk')." ADD   `status` tinyint(1) NOT NULL COMMENT '0/未开餐 1/已开餐 2/已结账'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_desk_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` char(50) NOT NULL COMMENT '订单编号',
  `desk_id` int(11) NOT NULL COMMENT '餐桌id',
  `person_count` int(11) NOT NULL COMMENT '用餐人数',
  `person_price` float NOT NULL COMMENT '餐位费',
  `total_price` float NOT NULL COMMENT '订单总价',
  `discount` float NOT NULL COMMENT '折扣',
  `body` char(100) NOT NULL COMMENT '订单说明',
  `create_time` int(11) NOT NULL COMMENT '用餐时间',
  `status` tinyint(1) NOT NULL COMMENT '0/未结算 1/已结算',
  `pay_time` int(11) NOT NULL COMMENT '结算时间',
  `pay_method` char(50) NOT NULL COMMENT '支付方式',
  `pra_price` float NOT NULL COMMENT '实际支付金额',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order','order_number')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order')." ADD   `order_number` char(50) NOT NULL COMMENT '订单编号'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order','desk_id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order')." ADD   `desk_id` int(11) NOT NULL COMMENT '餐桌id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order','person_count')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order')." ADD   `person_count` int(11) NOT NULL COMMENT '用餐人数'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order','person_price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order')." ADD   `person_price` float NOT NULL COMMENT '餐位费'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order','total_price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order')." ADD   `total_price` float NOT NULL COMMENT '订单总价'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order','discount')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order')." ADD   `discount` float NOT NULL COMMENT '折扣'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order','body')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order')." ADD   `body` char(100) NOT NULL COMMENT '订单说明'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order')." ADD   `create_time` int(11) NOT NULL COMMENT '用餐时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order','status')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order')." ADD   `status` tinyint(1) NOT NULL COMMENT '0/未结算 1/已结算'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order','pay_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order')." ADD   `pay_time` int(11) NOT NULL COMMENT '结算时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order','pay_method')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order')." ADD   `pay_method` char(50) NOT NULL COMMENT '支付方式'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order','pra_price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order')." ADD   `pra_price` float NOT NULL COMMENT '实际支付金额'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_desk_order_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `uid` int(11) NOT NULL COMMENT '用户uid',
  `openid` char(100) NOT NULL COMMENT '用户openid',
  `goods_name` char(100) NOT NULL COMMENT '商品名称',
  `price` float NOT NULL COMMENT '单价',
  `count` int(11) NOT NULL COMMENT '数量',
  `create_time` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0/准备中 1/已上菜 2/已退菜',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `uniacid` int(11) NOT NULL,
  `cover` text NOT NULL,
  `order_type` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order_detail','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order_detail')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order_detail','order_id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order_detail')." ADD   `order_id` int(11) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order_detail','uid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order_detail')." ADD   `uid` int(11) NOT NULL COMMENT '用户uid'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order_detail','openid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order_detail')." ADD   `openid` char(100) NOT NULL COMMENT '用户openid'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order_detail','goods_name')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order_detail')." ADD   `goods_name` char(100) NOT NULL COMMENT '商品名称'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order_detail','price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order_detail')." ADD   `price` float NOT NULL COMMENT '单价'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order_detail','count')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order_detail')." ADD   `count` int(11) NOT NULL COMMENT '数量'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order_detail','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order_detail')." ADD   `create_time` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order_detail','status')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order_detail')." ADD   `status` tinyint(1) NOT NULL COMMENT '0/准备中 1/已上菜 2/已退菜'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order_detail','goods_id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order_detail')." ADD   `goods_id` int(11) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order_detail','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order_detail')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order_detail','cover')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order_detail')." ADD   `cover` text NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order_detail','order_type')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order_detail')." ADD   `order_type` tinyint(2) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_desk_order_detail','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_desk_order_detail')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_exchange` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `pid` int(11) NOT NULL COMMENT '商品id',
  `tid` int(11) NOT NULL COMMENT '卡券id',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `address` char(200) NOT NULL COMMENT '收货地址',
  `name` char(100) NOT NULL COMMENT '收货人',
  `phone` char(20) NOT NULL COMMENT '联系电话',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `status` tinyint(1) NOT NULL COMMENT '0/兑换中，1兑换成功',
  `order_id` int(11) NOT NULL COMMENT '订单id',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_exchange','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_exchange')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_exchange','pid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_exchange')." ADD   `pid` int(11) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_exchange','tid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_exchange')." ADD   `tid` int(11) NOT NULL COMMENT '卡券id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_exchange','uid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_exchange')." ADD   `uid` int(11) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_exchange','address')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_exchange')." ADD   `address` char(200) NOT NULL COMMENT '收货地址'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_exchange','name')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_exchange')." ADD   `name` char(100) NOT NULL COMMENT '收货人'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_exchange','phone')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_exchange')." ADD   `phone` char(20) NOT NULL COMMENT '联系电话'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_exchange','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_exchange')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_exchange','status')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_exchange')." ADD   `status` tinyint(1) NOT NULL COMMENT '0/兑换中，1兑换成功'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_exchange','order_id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_exchange')." ADD   `order_id` int(11) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_exchange','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_exchange')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_giftlevel` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `price` float NOT NULL COMMENT '面值',
  `status` tinyint(1) NOT NULL COMMENT '1/启用 2/未启用',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `remark` char(200) NOT NULL COMMENT '备注',
  `rank` int(11) NOT NULL COMMENT '排序',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_giftlevel','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_giftlevel')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_giftlevel','price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_giftlevel')." ADD   `price` float NOT NULL COMMENT '面值'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_giftlevel','status')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_giftlevel')." ADD   `status` tinyint(1) NOT NULL COMMENT '1/启用 2/未启用'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_giftlevel','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_giftlevel')." ADD   `create_time` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_giftlevel','remark')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_giftlevel')." ADD   `remark` char(200) NOT NULL COMMENT '备注'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_giftlevel','rank')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_giftlevel')." ADD   `rank` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_giftlevel','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_giftlevel')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_giftlevel','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_giftlevel')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `type_id` int(11) NOT NULL COMMENT '分类id',
  `goods_name` char(100) NOT NULL COMMENT '商品名称',
  `goods_number` char(50) NOT NULL COMMENT '商品编号',
  `cover` text NOT NULL COMMENT '封面',
  `slide_src` text NOT NULL COMMENT '轮播图',
  `is_put_away` tinyint(1) NOT NULL COMMENT '0/不上架 1上架',
  `rank` int(11) NOT NULL COMMENT '排序',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `price` float NOT NULL COMMENT '价格',
  `old_price` float NOT NULL COMMENT '原价',
  `count` int(11) NOT NULL COMMENT '库存',
  `sale_count` int(11) NOT NULL COMMENT '销量',
  `detail_desc` text NOT NULL COMMENT '描述',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_goods','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods','type_id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods')." ADD   `type_id` int(11) NOT NULL COMMENT '分类id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods','goods_name')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods')." ADD   `goods_name` char(100) NOT NULL COMMENT '商品名称'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods','goods_number')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods')." ADD   `goods_number` char(50) NOT NULL COMMENT '商品编号'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods','cover')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods')." ADD   `cover` text NOT NULL COMMENT '封面'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods','slide_src')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods')." ADD   `slide_src` text NOT NULL COMMENT '轮播图'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods','is_put_away')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods')." ADD   `is_put_away` tinyint(1) NOT NULL COMMENT '0/不上架 1上架'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods','rank')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods')." ADD   `rank` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods')." ADD   `create_time` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods','price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods')." ADD   `price` float NOT NULL COMMENT '价格'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods','old_price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods')." ADD   `old_price` float NOT NULL COMMENT '原价'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods','count')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods')." ADD   `count` int(11) NOT NULL COMMENT '库存'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods','sale_count')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods')." ADD   `sale_count` int(11) NOT NULL COMMENT '销量'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods','detail_desc')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods')." ADD   `detail_desc` text NOT NULL COMMENT '描述'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_goods_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `type_name` char(50) NOT NULL COMMENT '分类名称',
  `uniacid` int(11) NOT NULL,
  `rank` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '0/不显示 1/显示',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_goods_type','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods_type')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods_type','type_name')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods_type')." ADD   `type_name` char(50) NOT NULL COMMENT '分类名称'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods_type','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods_type')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods_type','rank')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods_type')." ADD   `rank` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods_type','status')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods_type')." ADD   `status` tinyint(1) NOT NULL COMMENT '0/不显示 1/显示'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_goods_type','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_goods_type')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_make_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `name` char(100) NOT NULL COMMENT '姓名',
  `phone` char(20) NOT NULL COMMENT '联系电话',
  `use_date` char(100) NOT NULL COMMENT '用餐日期',
  `use_time` char(100) NOT NULL COMMENT '用餐时间',
  `person_count` int(11) NOT NULL COMMENT '用餐人数',
  `remark` char(200) NOT NULL COMMENT '备注',
  `total_price` float NOT NULL COMMENT '总价',
  `is_pay` tinyint(1) NOT NULL COMMENT '0/未支付 1/已支付',
  `is_use` tinyint(1) NOT NULL COMMENT '0/未用餐 1/已用餐 2/申请取消 3/已取消',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_make_order','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order','uid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order')." ADD   `uid` int(11) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order')." ADD   `create_time` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order','name')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order')." ADD   `name` char(100) NOT NULL COMMENT '姓名'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order','phone')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order')." ADD   `phone` char(20) NOT NULL COMMENT '联系电话'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order','use_date')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order')." ADD   `use_date` char(100) NOT NULL COMMENT '用餐日期'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order','use_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order')." ADD   `use_time` char(100) NOT NULL COMMENT '用餐时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order','person_count')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order')." ADD   `person_count` int(11) NOT NULL COMMENT '用餐人数'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order','remark')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order')." ADD   `remark` char(200) NOT NULL COMMENT '备注'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order','total_price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order')." ADD   `total_price` float NOT NULL COMMENT '总价'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order','is_pay')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order')." ADD   `is_pay` tinyint(1) NOT NULL COMMENT '0/未支付 1/已支付'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order','is_use')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order')." ADD   `is_use` tinyint(1) NOT NULL COMMENT '0/未用餐 1/已用餐 2/申请取消 3/已取消'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_make_order_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `mid` int(11) NOT NULL COMMENT '预约id',
  `pid` int(11) NOT NULL COMMENT '商品id',
  `product_name` char(200) NOT NULL COMMENT '商品名称',
  `cover` text NOT NULL COMMENT '商品图片',
  `price` float NOT NULL COMMENT '单价',
  `count` int(11) NOT NULL COMMENT '数量',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_make_order_detail','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order_detail')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order_detail','mid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order_detail')." ADD   `mid` int(11) NOT NULL COMMENT '预约id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order_detail','pid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order_detail')." ADD   `pid` int(11) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order_detail','product_name')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order_detail')." ADD   `product_name` char(200) NOT NULL COMMENT '商品名称'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order_detail','cover')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order_detail')." ADD   `cover` text NOT NULL COMMENT '商品图片'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order_detail','price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order_detail')." ADD   `price` float NOT NULL COMMENT '单价'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order_detail','count')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order_detail')." ADD   `count` int(11) NOT NULL COMMENT '数量'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order_detail','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order_detail')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_make_order_detail','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_make_order_detail')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_msg_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `appkey` char(200) NOT NULL COMMENT 'appkey',
  `secret` char(200) NOT NULL COMMENT '密钥',
  `sign_name` char(200) NOT NULL COMMENT '短信签名',
  `phone` char(20) NOT NULL COMMENT '接收短信手机号',
  `template_code` char(50) NOT NULL COMMENT '短信模板',
  `user` char(200) NOT NULL COMMENT '打印机用户',
  `ukey` char(200) NOT NULL COMMENT '打印机key',
  `sn` char(200) NOT NULL COMMENT '打印机编号',
  `print_is_open` tinyint(1) NOT NULL COMMENT '是否开启打印机',
  `wx_appid` char(200) NOT NULL COMMENT '微信公众号appid',
  `wx_secret` char(200) NOT NULL COMMENT '微信公众号密钥',
  `wx_template_id` char(200) NOT NULL COMMENT '微信公众号模板消息id',
  `wx_small_template_id` char(200) NOT NULL COMMENT '微信小程序模板id',
  `msg_type` char(200) NOT NULL COMMENT '发送消息类型',
  `get_openid` char(200) NOT NULL COMMENT '收取信息的openid',
  `wx_cert` text NOT NULL COMMENT '微信证书',
  `wx_key` text NOT NULL COMMENT '微信证书key',
  `wx_order_template_id` char(200) NOT NULL COMMENT '模板消息id',
  `wx_cancel_order_template` char(200) NOT NULL COMMENT '取消订单模板消息id',
  `coupon_id` char(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','appkey')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `appkey` char(200) NOT NULL COMMENT 'appkey'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','secret')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `secret` char(200) NOT NULL COMMENT '密钥'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','sign_name')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `sign_name` char(200) NOT NULL COMMENT '短信签名'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','phone')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `phone` char(20) NOT NULL COMMENT '接收短信手机号'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','template_code')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `template_code` char(50) NOT NULL COMMENT '短信模板'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','user')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `user` char(200) NOT NULL COMMENT '打印机用户'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','ukey')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `ukey` char(200) NOT NULL COMMENT '打印机key'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','sn')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `sn` char(200) NOT NULL COMMENT '打印机编号'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','print_is_open')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `print_is_open` tinyint(1) NOT NULL COMMENT '是否开启打印机'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','wx_appid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `wx_appid` char(200) NOT NULL COMMENT '微信公众号appid'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','wx_secret')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `wx_secret` char(200) NOT NULL COMMENT '微信公众号密钥'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','wx_template_id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `wx_template_id` char(200) NOT NULL COMMENT '微信公众号模板消息id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','wx_small_template_id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `wx_small_template_id` char(200) NOT NULL COMMENT '微信小程序模板id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','msg_type')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `msg_type` char(200) NOT NULL COMMENT '发送消息类型'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','get_openid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `get_openid` char(200) NOT NULL COMMENT '收取信息的openid'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','wx_cert')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `wx_cert` text NOT NULL COMMENT '微信证书'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','wx_key')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `wx_key` text NOT NULL COMMENT '微信证书key'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','wx_order_template_id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `wx_order_template_id` char(200) NOT NULL COMMENT '模板消息id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','wx_cancel_order_template')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `wx_cancel_order_template` char(200) NOT NULL COMMENT '取消订单模板消息id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','coupon_id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   `coupon_id` char(200) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('ims_cqkundian_ordering_msg_config','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_msg_config')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_nav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` char(50) NOT NULL COMMENT '标题',
  `eng_title` char(50) NOT NULL COMMENT '英文标题',
  `icon` text NOT NULL COMMENT '图标',
  `color` char(30) NOT NULL COMMENT '颜色',
  `path` char(100) NOT NULL COMMENT '跳转路径',
  `rank` int(11) NOT NULL COMMENT '排序',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_nav','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_nav')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_cqkundian_ordering_nav','title')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_nav')." ADD   `title` char(50) NOT NULL COMMENT '标题'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_nav','eng_title')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_nav')." ADD   `eng_title` char(50) NOT NULL COMMENT '英文标题'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_nav','icon')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_nav')." ADD   `icon` text NOT NULL COMMENT '图标'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_nav','color')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_nav')." ADD   `color` char(30) NOT NULL COMMENT '颜色'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_nav','path')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_nav')." ADD   `path` char(100) NOT NULL COMMENT '跳转路径'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_nav','rank')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_nav')." ADD   `rank` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_nav','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_nav')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_nav','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_nav')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `order_number` char(50) NOT NULL COMMENT '订单编号',
  `price` float NOT NULL COMMENT '订单总价',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `pay_time` int(11) NOT NULL COMMENT '支付时间',
  `is_pay` tinyint(1) NOT NULL COMMENT '0/未支付 1已支付',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `name` char(20) NOT NULL COMMENT '收货人',
  `phone` char(20) NOT NULL COMMENT '联系电话',
  `address` char(200) NOT NULL COMMENT '收货地址',
  `is_send` tinyint(1) NOT NULL COMMENT '0/未发货 1/已发货',
  `sent_time` int(11) NOT NULL COMMENT '发货时间',
  `gift_sub_price` float NOT NULL COMMENT '卡券抵消金额',
  `pra_price` float NOT NULL COMMENT '实际支付金额',
  `pay_method` char(100) NOT NULL COMMENT '支付方式',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `is_change` tinyint(11) NOT NULL COMMENT '1购买 2/兑换',
  `tid` int(11) NOT NULL,
  `send_number` char(100) NOT NULL COMMENT '物流编号',
  `remark` char(100) NOT NULL COMMENT '物流编号',
  `pei_time` char(100) NOT NULL COMMENT '物流编号',
  `fast_food_number` varchar(6) NOT NULL DEFAULT '' COMMENT '快餐，模式取餐号',
  `is_fast_food` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否快餐订单',
  `package_price` float NOT NULL DEFAULT '0' COMMENT '打包费',
  `uniontid` char(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_order','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','order_number')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `order_number` char(50) NOT NULL COMMENT '订单编号'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `price` float NOT NULL COMMENT '订单总价'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `create_time` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','pay_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `pay_time` int(11) NOT NULL COMMENT '支付时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','is_pay')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `is_pay` tinyint(1) NOT NULL COMMENT '0/未支付 1已支付'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','uid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `uid` int(11) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','name')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `name` char(20) NOT NULL COMMENT '收货人'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','phone')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `phone` char(20) NOT NULL COMMENT '联系电话'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','address')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `address` char(200) NOT NULL COMMENT '收货地址'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','is_send')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `is_send` tinyint(1) NOT NULL COMMENT '0/未发货 1/已发货'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','sent_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `sent_time` int(11) NOT NULL COMMENT '发货时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','gift_sub_price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `gift_sub_price` float NOT NULL COMMENT '卡券抵消金额'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','pra_price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `pra_price` float NOT NULL COMMENT '实际支付金额'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','pay_method')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `pay_method` char(100) NOT NULL COMMENT '支付方式'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','is_change')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `is_change` tinyint(11) NOT NULL COMMENT '1购买 2/兑换'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','tid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `tid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','send_number')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `send_number` char(100) NOT NULL COMMENT '物流编号'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','remark')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `remark` char(100) NOT NULL COMMENT '物流编号'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','pei_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `pei_time` char(100) NOT NULL COMMENT '物流编号'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','fast_food_number')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `fast_food_number` varchar(6) NOT NULL DEFAULT '' COMMENT '快餐，模式取餐号'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','is_fast_food')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `is_fast_food` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否快餐订单'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','package_price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `package_price` float NOT NULL DEFAULT '0' COMMENT '打包费'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','uniontid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   `uniontid` char(200) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_order_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `pid` int(11) NOT NULL COMMENT '商品编号',
  `order_id` int(11) NOT NULL COMMENT '订单编号',
  `num` int(11) NOT NULL COMMENT '购买数量',
  `total_price` float NOT NULL COMMENT '价格',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_order_detail','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order_detail')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order_detail','pid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order_detail')." ADD   `pid` int(11) NOT NULL COMMENT '商品编号'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order_detail','order_id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order_detail')." ADD   `order_id` int(11) NOT NULL COMMENT '订单编号'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order_detail','num')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order_detail')." ADD   `num` int(11) NOT NULL COMMENT '购买数量'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order_detail','total_price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order_detail')." ADD   `total_price` float NOT NULL COMMENT '价格'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order_detail','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order_detail')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_order_detail','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_order_detail')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_print` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` char(200) NOT NULL,
  `key` char(200) NOT NULL,
  `name` char(200) NOT NULL,
  `carnum` char(100) NOT NULL,
  `create_time` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_print','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_print')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_cqkundian_ordering_print','sn')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_print')." ADD   `sn` char(200) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_print','key')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_print')." ADD   `key` char(200) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_print','name')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_print')." ADD   `name` char(200) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_print','carnum')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_print')." ADD   `carnum` char(100) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_print','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_print')." ADD   `create_time` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_print','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_print')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_cqkundian_ordering_print','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_print')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `product_name` char(200) NOT NULL COMMENT '产品名称',
  `old_price` float NOT NULL COMMENT '原价',
  `price` float NOT NULL COMMENT '现价',
  `sale_count` int(11) NOT NULL COMMENT '销量',
  `count` int(11) NOT NULL COMMENT '库存',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `is_putaway` tinyint(1) NOT NULL COMMENT '1/上架 0/未上架',
  `cover` text NOT NULL COMMENT '封面',
  `tid` int(11) NOT NULL COMMENT '分类id',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `rank` int(11) NOT NULL COMMENT '排序',
  `is_change` tinyint(1) NOT NULL COMMENT '1/参加 0/不参加',
  `slide_src` text NOT NULL COMMENT '轮播图片',
  `detail_desc` text NOT NULL COMMENT '详细描述',
  `is_recommend` tinyint(1) NOT NULL COMMENT '0/不推荐，1推荐',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_product','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product','product_name')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product')." ADD   `product_name` char(200) NOT NULL COMMENT '产品名称'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product','old_price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product')." ADD   `old_price` float NOT NULL COMMENT '原价'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product','price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product')." ADD   `price` float NOT NULL COMMENT '现价'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product','sale_count')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product')." ADD   `sale_count` int(11) NOT NULL COMMENT '销量'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product','count')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product')." ADD   `count` int(11) NOT NULL COMMENT '库存'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product')." ADD   `create_time` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product','is_putaway')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product')." ADD   `is_putaway` tinyint(1) NOT NULL COMMENT '1/上架 0/未上架'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product','cover')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product')." ADD   `cover` text NOT NULL COMMENT '封面'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product','tid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product')." ADD   `tid` int(11) NOT NULL COMMENT '分类id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product','rank')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product')." ADD   `rank` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product','is_change')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product')." ADD   `is_change` tinyint(1) NOT NULL COMMENT '1/参加 0/不参加'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product','slide_src')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product')." ADD   `slide_src` text NOT NULL COMMENT '轮播图片'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product','detail_desc')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product')." ADD   `detail_desc` text NOT NULL COMMENT '详细描述'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product','is_recommend')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product')." ADD   `is_recommend` tinyint(1) NOT NULL COMMENT '0/不推荐，1推荐'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_product_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `type_name` char(50) NOT NULL COMMENT '分类名称',
  `icon` text NOT NULL COMMENT '分类图标',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `is_use` tinyint(1) NOT NULL COMMENT '1/未启用2/已启用',
  `is_recommend` tinyint(1) NOT NULL COMMENT '1/不推荐 2/推荐',
  `rank` int(11) NOT NULL COMMENT '排序',
  `uniacid` int(11) NOT NULL COMMENT '小程序',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_product_type','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product_type')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product_type','type_name')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product_type')." ADD   `type_name` char(50) NOT NULL COMMENT '分类名称'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product_type','icon')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product_type')." ADD   `icon` text NOT NULL COMMENT '分类图标'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product_type','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product_type')." ADD   `create_time` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product_type','is_use')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product_type')." ADD   `is_use` tinyint(1) NOT NULL COMMENT '1/未启用2/已启用'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product_type','is_recommend')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product_type')." ADD   `is_recommend` tinyint(1) NOT NULL COMMENT '1/不推荐 2/推荐'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product_type','rank')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product_type')." ADD   `rank` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product_type','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product_type')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_product_type','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_product_type')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_sale` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `cid` int(11) NOT NULL COMMENT '公司id',
  `count` int(11) NOT NULL COMMENT '购买数量',
  `price` float NOT NULL COMMENT '总价',
  `remark` char(200) NOT NULL COMMENT '备注',
  `tid` text NOT NULL COMMENT '卡券id',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `create_time` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_sale','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_sale')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_sale','cid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_sale')." ADD   `cid` int(11) NOT NULL COMMENT '公司id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_sale','count')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_sale')." ADD   `count` int(11) NOT NULL COMMENT '购买数量'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_sale','price')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_sale')." ADD   `price` float NOT NULL COMMENT '总价'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_sale','remark')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_sale')." ADD   `remark` char(200) NOT NULL COMMENT '备注'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_sale','tid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_sale')." ADD   `tid` text NOT NULL COMMENT '卡券id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_sale','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_sale')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_sale','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_sale')." ADD   `create_time` int(11) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_sale','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_sale')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_slide` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `title` char(100) NOT NULL COMMENT '标题',
  `src` text NOT NULL COMMENT '路径',
  `path` text NOT NULL COMMENT '跳转路径',
  `status` tinyint(1) NOT NULL COMMENT '1/显示 2/隐藏',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `rank` int(11) NOT NULL COMMENT '排序',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_slide','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_slide')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_slide','title')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_slide')." ADD   `title` char(100) NOT NULL COMMENT '标题'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_slide','src')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_slide')." ADD   `src` text NOT NULL COMMENT '路径'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_slide','path')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_slide')." ADD   `path` text NOT NULL COMMENT '跳转路径'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_slide','status')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_slide')." ADD   `status` tinyint(1) NOT NULL COMMENT '1/显示 2/隐藏'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_slide','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_slide')." ADD   `create_time` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_slide','rank')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_slide')." ADD   `rank` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_slide','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_slide')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_slide','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_slide')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `card_num` char(200) NOT NULL COMMENT '卡号',
  `password` char(200) NOT NULL COMMENT '密码',
  `is_use` tinyint(1) NOT NULL COMMENT '0/未使用 1/已使用',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `bid` int(11) NOT NULL COMMENT '批次id',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `is_sale` tinyint(1) NOT NULL COMMENT '0/未售出 1/已售出',
  `cid` int(11) NOT NULL COMMENT '客户id',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_token','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_token')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_token','card_num')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_token')." ADD   `card_num` char(200) NOT NULL COMMENT '卡号'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_token','password')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_token')." ADD   `password` char(200) NOT NULL COMMENT '密码'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_token','is_use')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_token')." ADD   `is_use` tinyint(1) NOT NULL COMMENT '0/未使用 1/已使用'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_token','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_token')." ADD   `create_time` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_token','bid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_token')." ADD   `bid` int(11) NOT NULL COMMENT '批次id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_token','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_token')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_token','is_sale')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_token')." ADD   `is_sale` tinyint(1) NOT NULL COMMENT '0/未售出 1/已售出'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_token','cid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_token')." ADD   `cid` int(11) NOT NULL COMMENT '客户id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_token','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_token')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_cqkundian_ordering_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uid` int(11) NOT NULL COMMENT '微擎用户id',
  `nickname` char(50) NOT NULL COMMENT '微信昵称',
  `avatarurl` text NOT NULL COMMENT '头像',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `phone` char(20) NOT NULL COMMENT '联系电话',
  `sex` tinyint(1) NOT NULL COMMENT '1/男 2/女',
  `address` char(200) NOT NULL COMMENT '地址',
  `rank` int(11) NOT NULL COMMENT '排序',
  `uniacid` int(11) NOT NULL COMMENT '小程序id',
  `order_count` int(11) NOT NULL COMMENT '订单数量',
  `wx_openid` char(50) NOT NULL COMMENT '微信公众号openid',
  `openid` char(100) NOT NULL COMMENT '微信公众号openid',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_cqkundian_ordering_user','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_user')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_user','uid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_user')." ADD   `uid` int(11) NOT NULL COMMENT '微擎用户id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_user','nickname')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_user')." ADD   `nickname` char(50) NOT NULL COMMENT '微信昵称'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_user','avatarurl')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_user')." ADD   `avatarurl` text NOT NULL COMMENT '头像'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_user','create_time')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_user')." ADD   `create_time` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_user','phone')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_user')." ADD   `phone` char(20) NOT NULL COMMENT '联系电话'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_user','sex')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_user')." ADD   `sex` tinyint(1) NOT NULL COMMENT '1/男 2/女'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_user','address')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_user')." ADD   `address` char(200) NOT NULL COMMENT '地址'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_user','rank')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_user')." ADD   `rank` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_user','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_user')." ADD   `uniacid` int(11) NOT NULL COMMENT '小程序id'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_user','order_count')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_user')." ADD   `order_count` int(11) NOT NULL COMMENT '订单数量'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_user','wx_openid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_user')." ADD   `wx_openid` char(50) NOT NULL COMMENT '微信公众号openid'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_user','openid')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_user')." ADD   `openid` char(100) NOT NULL COMMENT '微信公众号openid'");}
if(!pdo_fieldexists('ims_cqkundian_ordering_user','id')) {pdo_query("ALTER TABLE ".tablename('ims_cqkundian_ordering_user')." ADD   PRIMARY KEY (`id`)");}
