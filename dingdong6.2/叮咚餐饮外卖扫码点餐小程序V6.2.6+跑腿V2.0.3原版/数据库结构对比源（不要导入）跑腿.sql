/*
Navicat MySQL Data Transfer

Source Server         : root
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-08-09 13:09:54
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ims_cjpt_ad
-- ----------------------------
DROP TABLE IF EXISTS `ims_cjpt_ad`;
CREATE TABLE `ims_cjpt_ad` (
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

-- ----------------------------
-- Table structure for ims_cjpt_bind
-- ----------------------------
DROP TABLE IF EXISTS `ims_cjpt_bind`;
CREATE TABLE `ims_cjpt_bind` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pt_uniacid` int(11) NOT NULL COMMENT '跑腿小程序ID',
  `cy_uniacid` int(11) NOT NULL COMMENT '餐饮小程序ID',
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='绑定表';

-- ----------------------------
-- Table structure for ims_cjpt_dispatch
-- ----------------------------
DROP TABLE IF EXISTS `ims_cjpt_dispatch`;
CREATE TABLE `ims_cjpt_dispatch` (
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
  `oid` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ims_cjpt_fee
-- ----------------------------
DROP TABLE IF EXISTS `ims_cjpt_fee`;
CREATE TABLE `ims_cjpt_fee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start` int(11) NOT NULL COMMENT '配送起始值',
  `end` int(11) NOT NULL COMMENT '配送结束值',
  `money` decimal(10,2) NOT NULL COMMENT '价格',
  `num` int(11) NOT NULL COMMENT '排序',
  `uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ims_cjpt_help
-- ----------------------------
DROP TABLE IF EXISTS `ims_cjpt_help`;
CREATE TABLE `ims_cjpt_help` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(200) NOT NULL COMMENT '标题',
  `answer` text NOT NULL COMMENT '回答',
  `sort` int(4) NOT NULL COMMENT '排序',
  `uniacid` varchar(50) NOT NULL,
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ims_cjpt_mail
-- ----------------------------
DROP TABLE IF EXISTS `ims_cjpt_mail`;
CREATE TABLE `ims_cjpt_mail` (
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

-- ----------------------------
-- Table structure for ims_cjpt_message
-- ----------------------------
DROP TABLE IF EXISTS `ims_cjpt_message`;
CREATE TABLE `ims_cjpt_message` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL COMMENT '小程序Id',
  `appId` varchar(30) NOT NULL COMMENT '公众号appID',
  `appSecret` varchar(50) NOT NULL COMMENT '公众号AppSecret',
  `jdId` varchar(50) NOT NULL COMMENT '接单模板Id',
  `pdId` varchar(50) NOT NULL COMMENT '派单模板ID',
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='模板消息表';

-- ----------------------------
-- Table structure for ims_cjpt_notice
-- ----------------------------
DROP TABLE IF EXISTS `ims_cjpt_notice`;
CREATE TABLE `ims_cjpt_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(200) NOT NULL COMMENT '标题',
  `answer` text NOT NULL COMMENT '回答',
  `sort` int(4) NOT NULL COMMENT '排序',
  `uniacid` varchar(50) NOT NULL,
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ims_cjpt_rider
-- ----------------------------
DROP TABLE IF EXISTS `ims_cjpt_rider`;
CREATE TABLE `ims_cjpt_rider` (
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
  `weChatId` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='骑手表';

-- ----------------------------
-- Table structure for ims_cjpt_system
-- ----------------------------
DROP TABLE IF EXISTS `ims_cjpt_system`;
CREATE TABLE `ims_cjpt_system` (
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
  `aliyun_appkey` varchar(50) NOT NULL,
  `aliyun_appsecret` varchar(50) NOT NULL,
  `aliyun_sign` varchar(50) NOT NULL,
  `aliyun_id` varchar(50) NOT NULL,
  `item` int(1) NOT NULL DEFAULT '1' COMMENT '1聚合,2阿里云',
  `aliyun_id2` varchar(50) NOT NULL,
  `aliyun_id3` varchar(50) NOT NULL,
  `aliyun_id4` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ims_cjpt_withdrawal
-- ----------------------------
DROP TABLE IF EXISTS `ims_cjpt_withdrawal`;
CREATE TABLE `ims_cjpt_withdrawal` (
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
