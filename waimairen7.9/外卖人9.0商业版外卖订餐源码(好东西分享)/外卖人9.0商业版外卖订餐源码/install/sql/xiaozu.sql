/*
Navicat MySQL Data Transfer

Source Server         : test1
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : prowmrsqlx

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2018-06-23 20:32:55
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for apns_devices
-- ----------------------------
DROP TABLE IF EXISTS `apns_devices`;
CREATE TABLE `apns_devices` (
  `pid` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `clientid` varchar(64) NOT NULL,
  `appname` varchar(255) NOT NULL,
  `appversion` varchar(25) DEFAULT NULL,
  `deviceuid` char(40) NOT NULL,
  `devicetoken` char(64) DEFAULT NULL,
  `devicename` varchar(255) NOT NULL,
  `devicemodel` varchar(100) NOT NULL,
  `deviceversion` varchar(25) NOT NULL,
  `pushbadge` enum('disabled','enabled') DEFAULT 'disabled',
  `pushalert` enum('disabled','enabled') DEFAULT 'disabled',
  `pushsound` enum('disabled','enabled') DEFAULT 'disabled',
  `development` enum('production','sandbox') CHARACTER SET latin1 NOT NULL DEFAULT 'production',
  `status` enum('active','uninstalled') NOT NULL DEFAULT 'active',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pid`),
  UNIQUE KEY `appname_uid` (`appname`,`deviceuid`),
  UNIQUE KEY `appname_token` (`appname`,`devicetoken`),
  KEY `clientid` (`clientid`),
  KEY `devicetoken` (`devicetoken`),
  KEY `devicename` (`devicename`),
  KEY `devicemodel` (`devicemodel`),
  KEY `deviceversion` (`deviceversion`),
  KEY `pushbadge` (`pushbadge`),
  KEY `pushalert` (`pushalert`),
  KEY `pushsound` (`pushsound`),
  KEY `development` (`development`),
  KEY `status` (`status`),
  KEY `created` (`created`),
  KEY `modified` (`modified`)
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=utf8 COMMENT='Store unique devices';

-- ----------------------------
-- Records of apns_devices
-- ----------------------------

-- ----------------------------
-- Table structure for apns_device_history
-- ----------------------------
DROP TABLE IF EXISTS `apns_device_history`;
CREATE TABLE `apns_device_history` (
  `pid` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `clientid` varchar(64) NOT NULL,
  `appname` varchar(255) NOT NULL,
  `appversion` varchar(25) DEFAULT NULL,
  `deviceuid` char(40) NOT NULL,
  `devicetoken` char(64) DEFAULT NULL,
  `devicename` varchar(255) NOT NULL,
  `devicemodel` varchar(100) NOT NULL,
  `deviceversion` varchar(25) NOT NULL,
  `pushbadge` enum('disabled','enabled') DEFAULT 'disabled',
  `pushalert` enum('disabled','enabled') DEFAULT 'disabled',
  `pushsound` enum('disabled','enabled') DEFAULT 'disabled',
  `development` enum('production','sandbox') CHARACTER SET latin1 NOT NULL DEFAULT 'production',
  `status` enum('active','uninstalled') NOT NULL DEFAULT 'active',
  `archived` datetime NOT NULL,
  PRIMARY KEY (`pid`),
  KEY `clientid` (`clientid`),
  KEY `devicetoken` (`devicetoken`),
  KEY `devicename` (`devicename`),
  KEY `devicemodel` (`devicemodel`),
  KEY `deviceversion` (`deviceversion`),
  KEY `pushbadge` (`pushbadge`),
  KEY `pushalert` (`pushalert`),
  KEY `pushsound` (`pushsound`),
  KEY `development` (`development`),
  KEY `status` (`status`),
  KEY `appname` (`appname`),
  KEY `appversion` (`appversion`),
  KEY `deviceuid` (`deviceuid`),
  KEY `archived` (`archived`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='Store unique device history';

-- ----------------------------
-- Records of apns_device_history
-- ----------------------------

-- ----------------------------
-- Table structure for apns_messages
-- ----------------------------
DROP TABLE IF EXISTS `apns_messages`;
CREATE TABLE `apns_messages` (
  `pid` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `clientid` varchar(64) NOT NULL,
  `fk_device` int(9) unsigned NOT NULL,
  `message` varchar(255) NOT NULL,
  `delivery` datetime NOT NULL,
  `status` enum('queued','delivered','failed') CHARACTER SET latin1 NOT NULL DEFAULT 'queued',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pid`),
  KEY `clientid` (`clientid`),
  KEY `fk_device` (`fk_device`),
  KEY `status` (`status`),
  KEY `created` (`created`),
  KEY `modified` (`modified`),
  KEY `message` (`message`),
  KEY `delivery` (`delivery`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='Messages to push to APNS';

-- ----------------------------
-- Records of apns_messages
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_address
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_address`;
CREATE TABLE `xiaozu_address` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `userid` int(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL COMMENT '完整地址：选择地址与详细地址',
  `phone` varchar(15) NOT NULL,
  `otherphone` varchar(15) DEFAULT NULL,
  `contactname` varchar(50) DEFAULT NULL,
  `default` int(1) NOT NULL DEFAULT '1',
  `areaid1` int(20) NOT NULL DEFAULT '0' COMMENT '区域1ID',
  `areaid2` int(20) NOT NULL DEFAULT '0' COMMENT '区域2ID',
  `areaid3` int(20) NOT NULL DEFAULT '0' COMMENT '区域3ID',
  `sex` smallint(1) NOT NULL DEFAULT '0' COMMENT '1时是男性，值为2时是女性，值为0时是未知',
  `bigadr` varchar(255) NOT NULL COMMENT '选择的地址',
  `detailadr` varchar(255) NOT NULL COMMENT '详细地址',
  `lat` decimal(9,6) NOT NULL COMMENT '地址坐标',
  `lng` decimal(9,6) NOT NULL COMMENT '地址坐标',
  `addtime` int(11) NOT NULL COMMENT '时间',
  `tag` int(1) DEFAULT NULL COMMENT '标签/1家2公司3学校0无',
  `adcode` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4621 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_address
-- ----------------------------
INSERT INTO `xiaozu_address` VALUES ('4620', '22053', 'wmr', '河南省电子商务产业园1605', '15236978412', '', '测试', '1', '0', '0', '0', '0', '河南省电子商务产业园', '1605', '34.802330', '113.543806', '1529754123', '1', null);

-- ----------------------------
-- Table structure for xiaozu_admin
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_admin`;
CREATE TABLE `xiaozu_admin` (
  `uid` int(5) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `time` int(11) NOT NULL,
  `logintime` int(11) NOT NULL,
  `loginip` varchar(30) DEFAULT NULL,
  `limit` text,
  `groupid` int(20) NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=456 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_admin
-- ----------------------------
INSERT INTO `xiaozu_admin` VALUES ('1', 'admin', '5fe5b517ad0be76b12674ceaddf79d3a', '0', '1529753912', '127.0.0.1', 'siteset,seo_setsave,limitset,savelimitset,footlink,savefootlink,toplink,savetoplink,jflimitset,manegelist,manegeadd,editmanege,manegesave,delmanege,memberlist,addmember,editmember,membersave,delmember,oauthlist,deloauth,shoplist,shoplistw,shoptype,addshoptype,editshoptype,saveshoptype,delshoptype,arealist,addarea,eidtarea,savearea,cxsign,addcxsign,editcxsign,orderlist,ordertotal,markettj,marketorder,orderyjin,commentlist,delcommlist,asklist,delask,askshoplist,singlelist,addsingle,savesingle,delsingle,adv,addadv,advtype,giftlist,addgift,gifttype,addgifttype,giftlog,emailset,smsset,sendtasklist,sendtask,cardlist,addcard,juanlist,addjuan,excomm,pmes,loginlist,paylist,othertpl,ordertodayw,ordertoday,ordersend,basedata,rebkdata,market,addmarket,editmarket,delmarket,addmarkettype,delmarkettype,listmarkettype,friendlink,shoptopatt,wxset,wxback,wxmenu,printlog', '1');
INSERT INTO `xiaozu_admin` VALUES ('455', '郑州站', 'e10adc3949ba59abbe56e057f20f883e', '1529746086', '0', null, null, '4');

-- ----------------------------
-- Table structure for xiaozu_adv
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_adv`;
CREATE TABLE `xiaozu_adv` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL COMMENT '广告标题',
  `advtype` varchar(10) NOT NULL COMMENT '广告类型code',
  `img` varchar(255) DEFAULT NULL COMMENT '图片地址',
  `linkurl` varchar(255) DEFAULT NULL COMMENT '超链接',
  `module` varchar(50) DEFAULT 'newgreen',
  `cityid` int(12) NOT NULL COMMENT '城市ID',
  `sort` int(11) NOT NULL,
  `is_show` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=462 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_adv
-- ----------------------------
INSERT INTO `xiaozu_adv` VALUES ('451', '首页轮播图', 'weixinlb', '/images/410100/index/20180623174634768.png', '#', 'm7', '410100', '2', '1');
INSERT INTO `xiaozu_adv` VALUES ('452', '首页轮播图', 'weixinlb', '/images/410100/index/20180623174307811.png', '#', 'm7', '410100', '3', '0');
INSERT INTO `xiaozu_adv` VALUES ('453', '首页轮播图', 'weixinlb', '/images/410100/index/20180623174315523.png', '#', 'm7', '410100', '4', '0');
INSERT INTO `xiaozu_adv` VALUES ('454', '首页轮播图', 'weixinlb', '/images/410100/index/20180623174338252.png', '#', 'm7', '410100', '5', '0');
INSERT INTO `xiaozu_adv` VALUES ('455', '', 'weixinlb', '/upload/goods/20160109181719939.png', '#', 'm7', '410100', '999', '0');
INSERT INTO `xiaozu_adv` VALUES ('456', '首页轮播图', 'weixinlb', '/images/410100/index/20180623174238676.png', '#', 'm7', '410100', '1', '1');
INSERT INTO `xiaozu_adv` VALUES ('457', '首页轮播图', 'weixinlb2', '/images/410100/index/20180623185249860.png', '#', 'm7', '410100', '1', '1');
INSERT INTO `xiaozu_adv` VALUES ('458', '首页轮播图', 'weixinlb2', '/images/410100/index/20180623185302779.png', '#', 'm7', '410100', '2', '0');
INSERT INTO `xiaozu_adv` VALUES ('459', '首页轮播图', 'weixinlb2', '/images/410100/index/20180623185307705.png', '#', 'm7', '410100', '3', '0');
INSERT INTO `xiaozu_adv` VALUES ('460', '首页轮播图', 'weixinlb2', '/images/410100/index/20180623185318815.png', '#', 'm7', '410100', '4', '0');
INSERT INTO `xiaozu_adv` VALUES ('461', '首页轮播图', 'weixinlb2', '/images/410100/index/20180623185325292.png', '#', 'm7', '410100', '5', '0');

-- ----------------------------
-- Table structure for xiaozu_alljuan
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_alljuan`;
CREATE TABLE `xiaozu_alljuan` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `type` int(2) DEFAULT NULL COMMENT '优惠券类型1关注微信 2注册送 3充值送 4下单发红包 5邀请好友',
  `name` text COMMENT '优惠券名称',
  `cost` int(5) DEFAULT '0' COMMENT '固定面值',
  `costmax` int(5) DEFAULT '0' COMMENT '随机面值上限',
  `costmin` int(5) DEFAULT '0' COMMENT '随机面值下限',
  `limitcost` int(5) DEFAULT '0' COMMENT '限制满金额',
  `starttime` int(11) DEFAULT NULL COMMENT '有效时间开始值',
  `endtime` int(11) DEFAULT NULL COMMENT '有效时间结束值',
  `paytype` varchar(20) DEFAULT NULL COMMENT '支持类型 1 货到支付 2在线支付 1,2 都支持',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1048 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_alljuan
-- ----------------------------
INSERT INTO `xiaozu_alljuan` VALUES ('1046', '3', '充值送优惠券', '0', '10', '5', '50', '1512403200', '1515081599', '1,2');
INSERT INTO `xiaozu_alljuan` VALUES ('1047', '1', '关注送优惠券', '5', '0', '0', '20', '1514563200', '1515686399', '1');
INSERT INTO `xiaozu_alljuan` VALUES ('1043', '2', '注册送优惠券', '5', '0', '0', '20', '1508083200', '1516982399', '1,2');
INSERT INTO `xiaozu_alljuan` VALUES ('1044', '5', '邀请好友送红包', '0', '3', '1', '20', '1509984000', '1517327999', '1,2');
INSERT INTO `xiaozu_alljuan` VALUES ('1045', '4', '下单送优惠券', '0', '3', '1', '20', '1508860800', '1526140799', '1');

-- ----------------------------
-- Table structure for xiaozu_alljuanset
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_alljuanset`;
CREATE TABLE `xiaozu_alljuanset` (
  `id` int(2) NOT NULL,
  `name` text COMMENT '优惠券名称',
  `type` int(2) DEFAULT NULL COMMENT '优惠券类型1关注微信 2注册送 3充值送 4下单发红包 5邀请好友',
  `status` int(2) DEFAULT '0' COMMENT '状态 0关闭 1开启',
  `costtype` int(2) DEFAULT '1' COMMENT '面值类型 1固定面值 2随机面值',
  `paytype` varchar(20) DEFAULT NULL COMMENT '支持类型 1 货到支付 2在线支付 1,2 都支持',
  `timetype` int(2) DEFAULT '1' COMMENT '有效期类型  1领取后几天后失效 2固定时间段',
  `days` int(3) DEFAULT NULL COMMENT '有效天数',
  `starttime` int(11) DEFAULT NULL COMMENT '有效时间开始值',
  `endtime` int(11) DEFAULT NULL COMMENT '有效时间结束值',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_alljuanset
-- ----------------------------
INSERT INTO `xiaozu_alljuanset` VALUES ('1', '关注送优惠券', '1', '1', '1', '1', '2', '3', '1514563200', '1515686399');
INSERT INTO `xiaozu_alljuanset` VALUES ('2', '注册送优惠券', '2', '1', '1', '1,2', '1', '1', '1508083200', '1516982399');
INSERT INTO `xiaozu_alljuanset` VALUES ('3', '充值送优惠券', '3', '0', '2', '1,2', '2', '10', '1512403200', '1515081599');
INSERT INTO `xiaozu_alljuanset` VALUES ('4', '下单送优惠券', '4', '1', '2', '1', '2', '1', '1508860800', '1526140799');
INSERT INTO `xiaozu_alljuanset` VALUES ('5', '邀请好友送红包', '5', '1', '2', '1,2', '1', '3', '1509984000', '1517327999');

-- ----------------------------
-- Table structure for xiaozu_appadv
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_appadv`;
CREATE TABLE `xiaozu_appadv` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `type` int(1) DEFAULT NULL,
  `activity` varchar(255) DEFAULT NULL,
  `param` varchar(255) DEFAULT NULL,
  `orderid` int(5) NOT NULL DEFAULT '0',
  `cityid` int(12) NOT NULL COMMENT '所属城市ID',
  `is_show` int(2) NOT NULL DEFAULT '1',
  `modeopt` int(2) NOT NULL DEFAULT '1',
  `link` varchar(255) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1268 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_appadv
-- ----------------------------
INSERT INTO `xiaozu_appadv` VALUES ('1252', '精选美食', '/images/410100/index/20180623180345171.png', '2', 'waimai', '486', '1', '410100', '1', '1', '1');
INSERT INTO `xiaozu_appadv` VALUES ('1253', '快餐速食', '/images/410100/index/20180623180419808.png', '2', 'waimai', '486', '2', '410100', '1', '1', '1');
INSERT INTO `xiaozu_appadv` VALUES ('1254', '甜点饮品', '/images/410100/index/20180623180425993.png', '2', 'market', '483', '3', '410100', '1', '1', '1');
INSERT INTO `xiaozu_appadv` VALUES ('1255', '炸鸡汉堡', '/images/410100/index/20180623180535476.png', '2', 'market', '483', '4', '410100', '1', '1', '1');
INSERT INTO `xiaozu_appadv` VALUES ('1256', '火锅外送', '/images/410100/index/20180623180441212.png', '2', 'market', '483', '5', '410100', '1', '1', '1');
INSERT INTO `xiaozu_appadv` VALUES ('1257', '果蔬生鲜', '/images/410100/index/20180623180524274.png', '2', 'market', '483', '6', '410100', '1', '1', '1');
INSERT INTO `xiaozu_appadv` VALUES ('1258', '送药上门', '/images/410100/index/20180623180502887.png', '2', 'market', '483', '7', '410100', '1', '1', '1');
INSERT INTO `xiaozu_appadv` VALUES ('1259', '鲜花蛋糕', '/images/410100/index/20180623180513313.png', '2', 'market', '483', '8', '410100', '1', '1', '1');
INSERT INTO `xiaozu_appadv` VALUES ('1260', '跑腿', '/images/410100/index/20180623180714565.png', '2', 'paotui', 'paotui', '9', '410100', '0', '1', '1');
INSERT INTO `xiaozu_appadv` VALUES ('1261', '美食外卖', '/images/410100/index/20180623183144244.png', '2', 'market', '483', '10', '410100', '0', '1', '1');
INSERT INTO `xiaozu_appadv` VALUES ('1262', '美食外卖', '/images/410100/index/20180623183155527.png', '2', 'market', '483', '11', '410100', '0', '1', '1');
INSERT INTO `xiaozu_appadv` VALUES ('1263', '美食外卖', '/images/410100/index/20180623183206921.png', '2', 'market', '483', '12', '410100', '0', '1', '1');
INSERT INTO `xiaozu_appadv` VALUES ('1264', '美食外卖', '/images/410100/index/20180623183217379.png', '2', 'market', '483', '13', '410100', '0', '1', '1');
INSERT INTO `xiaozu_appadv` VALUES ('1265', '美食外卖', '/images/410100/index/20180623183232823.png', '2', 'market', '483', '14', '410100', '0', '1', '1');
INSERT INTO `xiaozu_appadv` VALUES ('1266', '美食外卖', '/images/410100/index/20180623183246704.png', '2', 'market', '483', '15', '410100', '0', '1', '1');
INSERT INTO `xiaozu_appadv` VALUES ('1267', '美食外卖', '/images/410100/index/20180623183304244.png', '2', 'market', '483', '16', '410100', '0', '1', '1');

-- ----------------------------
-- Table structure for xiaozu_appbuyerlogin
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_appbuyerlogin`;
CREATE TABLE `xiaozu_appbuyerlogin` (
  `uid` int(20) NOT NULL,
  `channelid` varchar(100) NOT NULL,
  `addtime` int(12) NOT NULL,
  `userid` varchar(100) NOT NULL,
  `xmuserid` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_appbuyerlogin
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_applogin
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_applogin`;
CREATE TABLE `xiaozu_applogin` (
  `uid` int(20) NOT NULL,
  `channelid` varchar(100) NOT NULL,
  `addtime` int(12) NOT NULL,
  `userid` varchar(100) NOT NULL,
  `xmuserid` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_applogin
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_apploginps
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_apploginps`;
CREATE TABLE `xiaozu_apploginps` (
  `uid` int(20) NOT NULL,
  `channelid` varchar(100) NOT NULL,
  `addtime` int(12) NOT NULL,
  `userid` varchar(100) NOT NULL,
  `xmuserid` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_apploginps
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_appmudel
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_appmudel`;
CREATE TABLE `xiaozu_appmudel` (
  `name` varchar(100) NOT NULL COMMENT '模块名称--固定写',
  `imgurl` varchar(255) NOT NULL COMMENT '模块图标 ',
  `is_display` int(1) NOT NULL COMMENT ' 0不在首页展示   1在首页展示',
  `cnname` varchar(100) NOT NULL COMMENT '中文名称（统一录入 无ID 仅name关键字）',
  `ctlname` varchar(100) NOT NULL COMMENT 'ctlname',
  `is_install` int(1) DEFAULT '0' COMMENT '0.APP完全不显示，1APP上显示',
  `orderid` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_appmudel
-- ----------------------------
INSERT INTO `xiaozu_appmudel` VALUES ('waimai', '/upload/goods/20151209172321460.png', '1', '外卖', '', '1', '1');
INSERT INTO `xiaozu_appmudel` VALUES ('market', '/upload/goods/20151209172327928.png', '1', '超市', '', '0', '2');
INSERT INTO `xiaozu_appmudel` VALUES ('diancai', '/upload/goods/20151209172334880.png', '1', '点菜', '', '1', '3');
INSERT INTO `xiaozu_appmudel` VALUES ('dingtai', '/upload/goods/20151209172339618.png', '1', '订台', '', '1', '4');
INSERT INTO `xiaozu_appmudel` VALUES ('paotui', '/upload/goods/20151209174536887.png', '1', '跑腿', '', '0', '5');
INSERT INTO `xiaozu_appmudel` VALUES ('gift', '/upload/goods/20160126135853109.png', '1', '礼品', '', '1', '2');
INSERT INTO `xiaozu_appmudel` VALUES ('collect', '/upload/goods/20160110135528286.png', '1', '收藏', '', '1', '1');
INSERT INTO `xiaozu_appmudel` VALUES ('newuser', '/upload/goods/20160110135543449.png', '1', '我是新手', '', '1', '3');

-- ----------------------------
-- Table structure for xiaozu_area
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_area`;
CREATE TABLE `xiaozu_area` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '区域名称',
  `pin` varchar(5) DEFAULT NULL COMMENT '首字母拼音',
  `parent_id` int(20) NOT NULL DEFAULT '0' COMMENT '上级区域ID',
  `orderid` int(20) DEFAULT NULL COMMENT '排序ID',
  `imgurl` varchar(255) DEFAULT NULL COMMENT '一级地址图片地址',
  `lng` decimal(9,6) NOT NULL DEFAULT '0.000000',
  `lat` decimal(9,6) NOT NULL DEFAULT '0.000000',
  `show` int(1) DEFAULT '0',
  `is_com` int(1) DEFAULT '0',
  `admin_id` int(20) NOT NULL DEFAULT '0',
  `adcode` int(8) NOT NULL COMMENT '地址code',
  `procode` int(8) NOT NULL COMMENT '省区域code(地址code上一级)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=231 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_area
-- ----------------------------
INSERT INTO `xiaozu_area` VALUES ('230', '郑州市', 'ZZS', '0', '0', null, '0.000000', '0.000000', '0', '0', '0', '410100', '410000');

-- ----------------------------
-- Table structure for xiaozu_areacode
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_areacode`;
CREATE TABLE `xiaozu_areacode` (
  `id` bigint(20) unsigned NOT NULL COMMENT '主键ID',
  `name` varchar(100) DEFAULT NULL COMMENT '省市区名称',
  `pid` bigint(20) DEFAULT NULL COMMENT '省市编码',
  `disorder` smallint(6) DEFAULT NULL COMMENT '排序代码',
  `procode` varchar(10) DEFAULT NULL COMMENT '省区域代码',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='省市区编码表';

-- ----------------------------
-- Records of xiaozu_areacode
-- ----------------------------
INSERT INTO `xiaozu_areacode` VALUES ('110000', '北京市', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110100', '北京市', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110101', '东城区', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110102', '西城区', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110103', '崇文区', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110104', '宣武区', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110105', '朝阳区', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110106', '丰台区', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110107', '石景山区', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110108', '海淀区', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110109', '门头沟区', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110111', '房山区', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110112', '通州区', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110113', '顺义区', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110114', '昌平区', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110115', '大兴区', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110116', '怀柔区', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110117', '平谷区', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110228', '密云县', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('110229', '延庆县', '110000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120000', '天津市', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120101', '和平区', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120102', '河东区', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120103', '河西区', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120104', '南开区', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120105', '河北区', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120106', '红桥区', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120107', '塘沽区', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120108', '汉沽区', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120109', '大港区', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120110', '东丽区', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120111', '西青区', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120112', '津南区', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120113', '北辰区', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120114', '武清区', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120115', '宝坻区', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120221', '宁河县', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120223', '静海县', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('120225', '蓟县', '120000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130000', '河北省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130100', '石家庄市', '130000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130101', '市辖区', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130102', '长安区', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130103', '桥东区', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130104', '桥西区', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130105', '新华区', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130107', '井陉矿区', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130108', '裕华区', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130121', '井陉县', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130123', '正定县', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130124', '栾城县', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130125', '行唐县', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130126', '灵寿县', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130127', '高邑县', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130128', '深泽县', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130129', '赞皇县', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130130', '无极县', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130131', '平山县', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130132', '元氏县', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130133', '赵县', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130181', '辛集市', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130182', '藁城市', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130183', '晋州市', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130184', '新乐市', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130185', '鹿泉市', '130100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130200', '唐山市', '130000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130201', '市辖区', '130200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130202', '路南区', '130200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130203', '路北区', '130200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130204', '古冶区', '130200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130205', '开平区', '130200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130207', '丰南区', '130200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130208', '丰润区', '130200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130223', '滦县', '130200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130224', '滦南县', '130200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130225', '乐亭县', '130200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130227', '迁西县', '130200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130229', '玉田县', '130200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130230', '唐海县', '130200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130281', '遵化市', '130200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130283', '迁安市', '130200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130300', '秦皇岛市', '130000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130301', '市辖区', '130300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130302', '海港区', '130300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130303', '山海关区', '130300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130304', '北戴河区', '130300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130321', '青龙满族自治县', '130300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130322', '昌黎县', '130300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130323', '抚宁县', '130300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130324', '卢龙县', '130300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130400', '邯郸市', '130000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130401', '市辖区', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130402', '邯山区', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130403', '丛台区', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130404', '复兴区', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130406', '峰峰矿区', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130421', '邯郸县', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130423', '临漳县', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130424', '成安县', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130425', '大名县', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130426', '涉县', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130427', '磁县', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130428', '肥乡县', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130429', '永年县', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130430', '邱县', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130431', '鸡泽县', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130432', '广平县', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130433', '馆陶县', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130434', '魏县', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130435', '曲周县', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130481', '武安市', '130400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130500', '邢台市', '130000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130501', '市辖区', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130502', '桥东区', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130503', '桥西区', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130521', '邢台县', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130522', '临城县', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130523', '内丘县', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130524', '柏乡县', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130525', '隆尧县', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130526', '任县', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130527', '南和县', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130528', '宁晋县', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130529', '巨鹿县', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130530', '新河县', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130531', '广宗县', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130532', '平乡县', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130533', '威县', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130534', '清河县', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130535', '临西县', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130581', '南宫市', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130582', '沙河市', '130500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130600', '保定市', '130000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130601', '市辖区', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130602', '新市区', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130603', '北市区', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130604', '南市区', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130621', '满城县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130622', '清苑县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130623', '涞水县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130624', '阜平县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130625', '徐水县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130626', '定兴县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130627', '唐县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130628', '高阳县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130629', '容城县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130630', '涞源县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130631', '望都县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130632', '安新县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130633', '易县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130634', '曲阳县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130635', '蠡县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130636', '顺平县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130637', '博野县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130638', '雄县', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130681', '涿州市', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130682', '定州市', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130683', '安国市', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130684', '高碑店市', '130600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130700', '张家口市', '130000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130701', '市辖区', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130702', '桥东区', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130703', '桥西区', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130705', '宣化区', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130706', '下花园区', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130721', '宣化县', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130722', '张北县', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130723', '康保县', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130724', '沽源县', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130725', '尚义县', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130726', '蔚县', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130727', '阳原县', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130728', '怀安县', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130729', '万全县', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130730', '怀来县', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130731', '涿鹿县', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130732', '赤城县', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130733', '崇礼县', '130700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130800', '承德市', '130000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130801', '市辖区', '130800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130802', '双桥区', '130800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130803', '双滦区', '130800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130804', '鹰手营子矿区', '130800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130821', '承德县', '130800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130822', '兴隆县', '130800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130823', '平泉县', '130800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130824', '滦平县', '130800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130825', '隆化县', '130800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130826', '丰宁满族自治县', '130800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130827', '宽城满族自治县', '130800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130828', '围场满族蒙古族自治县', '130800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130900', '沧州市', '130000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130901', '市辖区', '130900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130902', '新华区', '130900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130903', '运河区', '130900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130921', '沧县', '130900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130922', '青县', '130900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130923', '东光县', '130900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130924', '海兴县', '130900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130925', '盐山县', '130900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130926', '肃宁县', '130900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130927', '南皮县', '130900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130928', '吴桥县', '130900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130929', '献县', '130900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130930', '孟村回族自治县', '130900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130981', '泊头市', '130900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130982', '任丘市', '130900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130983', '黄骅市', '130900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('130984', '河间市', '130900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131000', '廊坊市', '130000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131001', '市辖区', '131000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131002', '安次区', '131000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131003', '广阳区', '131000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131022', '固安县', '131000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131023', '永清县', '131000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131024', '香河县', '131000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131025', '大城县', '131000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131026', '文安县', '131000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131028', '大厂回族自治县', '131000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131081', '霸州市', '131000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131082', '三河市', '131000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131100', '衡水市', '130000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131101', '市辖区', '131100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131102', '桃城区', '131100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131121', '枣强县', '131100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131122', '武邑县', '131100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131123', '武强县', '131100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131124', '饶阳县', '131100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131125', '安平县', '131100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131126', '故城县', '131100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131127', '景县', '131100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131128', '阜城县', '131100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131181', '冀州市', '131100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('131182', '深州市', '131100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140000', '山西省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140100', '太原市', '140000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140101', '市辖区', '140100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140105', '小店区', '140100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140106', '迎泽区', '140100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140107', '杏花岭区', '140100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140108', '尖草坪区', '140100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140109', '万柏林区', '140100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140110', '晋源区', '140100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140121', '清徐县', '140100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140122', '阳曲县', '140100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140123', '娄烦县', '140100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140181', '古交市', '140100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140200', '大同市', '140000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140201', '市辖区', '140200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140202', '城区', '140200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140203', '矿区', '140200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140211', '南郊区', '140200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140212', '新荣区', '140200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140221', '阳高县', '140200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140222', '天镇县', '140200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140223', '广灵县', '140200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140224', '灵丘县', '140200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140225', '浑源县', '140200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140226', '左云县', '140200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140227', '大同县', '140200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140300', '阳泉市', '140000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140301', '市辖区', '140300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140302', '城区', '140300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140303', '矿区', '140300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140311', '郊区', '140300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140321', '平定县', '140300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140322', '盂县', '140300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140400', '长治市', '140000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140401', '市辖区', '140400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140402', '城区', '140400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140411', '郊区', '140400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140421', '长治县', '140400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140423', '襄垣县', '140400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140424', '屯留县', '140400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140425', '平顺县', '140400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140426', '黎城县', '140400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140427', '壶关县', '140400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140428', '长子县', '140400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140429', '武乡县', '140400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140430', '沁县', '140400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140431', '沁源县', '140400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140481', '潞城市', '140400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140500', '晋城市', '140000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140501', '市辖区', '140500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140502', '城区', '140500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140521', '沁水县', '140500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140522', '阳城县', '140500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140524', '陵川县', '140500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140525', '泽州县', '140500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140581', '高平市', '140500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140600', '朔州市', '140000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140601', '市辖区', '140600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140602', '朔城区', '140600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140603', '平鲁区', '140600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140621', '山阴县', '140600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140622', '应县', '140600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140623', '右玉县', '140600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140624', '怀仁县', '140600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140700', '晋中市', '140000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140701', '市辖区', '140700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140702', '榆次区', '140700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140721', '榆社县', '140700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140722', '左权县', '140700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140723', '和顺县', '140700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140724', '昔阳县', '140700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140725', '寿阳县', '140700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140726', '太谷县', '140700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140727', '祁县', '140700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140728', '平遥县', '140700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140729', '灵石县', '140700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140781', '介休市', '140700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140800', '运城市', '140000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140801', '市辖区', '140800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140802', '盐湖区', '140800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140821', '临猗县', '140800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140822', '万荣县', '140800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140823', '闻喜县', '140800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140824', '稷山县', '140800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140825', '新绛县', '140800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140826', '绛县', '140800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140827', '垣曲县', '140800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140828', '夏县', '140800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140829', '平陆县', '140800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140830', '芮城县', '140800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140881', '永济市', '140800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140882', '河津市', '140800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140900', '忻州市', '140000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140901', '市辖区', '140900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140902', '忻府区', '140900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140921', '定襄县', '140900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140922', '五台县', '140900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140923', '代县', '140900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140924', '繁峙县', '140900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140925', '宁武县', '140900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140926', '静乐县', '140900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140927', '神池县', '140900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140928', '五寨县', '140900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140929', '岢岚县', '140900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140930', '河曲县', '140900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140931', '保德县', '140900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140932', '偏关县', '140900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('140981', '原平市', '140900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141000', '临汾市', '140000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141001', '市辖区', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141002', '尧都区', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141021', '曲沃县', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141022', '翼城县', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141023', '襄汾县', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141024', '洪洞县', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141025', '古县', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141026', '安泽县', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141027', '浮山县', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141028', '吉县', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141029', '乡宁县', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141030', '大宁县', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141031', '隰县', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141032', '永和县', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141033', '蒲县', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141034', '汾西县', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141081', '侯马市', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141082', '霍州市', '141000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141100', '吕梁市', '140000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141101', '市辖区', '141100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141102', '离石区', '141100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141121', '文水县', '141100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141122', '交城县', '141100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141123', '兴县', '141100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141124', '临县', '141100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141125', '柳林县', '141100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141126', '石楼县', '141100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141127', '岚县', '141100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141128', '方山县', '141100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141129', '中阳县', '141100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141130', '交口县', '141100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141181', '孝义市', '141100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('141182', '汾阳市', '141100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150000', '内蒙古自治区', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150100', '呼和浩特市', '150000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150101', '市辖区', '150100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150102', '新城区', '150100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150103', '回民区', '150100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150104', '玉泉区', '150100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150105', '赛罕区', '150100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150121', '土默特左旗', '150100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150122', '托克托县', '150100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150123', '和林格尔县', '150100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150124', '清水河县', '150100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150125', '武川县', '150100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150200', '包头市', '150000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150201', '市辖区', '150200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150202', '东河区', '150200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150203', '昆都仑区', '150200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150204', '青山区', '150200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150205', '石拐区', '150200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150206', '白云矿区', '150200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150207', '九原区', '150200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150221', '土默特右旗', '150200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150222', '固阳县', '150200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150223', '达尔罕茂明安联合旗', '150200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150300', '乌海市', '150000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150301', '市辖区', '150300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150302', '海勃湾区', '150300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150303', '海南区', '150300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150304', '乌达区', '150300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150400', '赤峰市', '150000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150401', '市辖区', '150400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150402', '红山区', '150400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150403', '元宝山区', '150400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150404', '松山区', '150400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150421', '阿鲁科尔沁旗', '150400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150422', '巴林左旗', '150400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150423', '巴林右旗', '150400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150424', '林西县', '150400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150425', '克什克腾旗', '150400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150426', '翁牛特旗', '150400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150428', '喀喇沁旗', '150400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150429', '宁城县', '150400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150430', '敖汉旗', '150400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150500', '通辽市', '150000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150501', '市辖区', '150500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150502', '科尔沁区', '150500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150521', '科尔沁左翼中旗', '150500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150522', '科尔沁左翼后旗', '150500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150523', '开鲁县', '150500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150524', '库伦旗', '150500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150525', '奈曼旗', '150500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150526', '扎鲁特旗', '150500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150581', '霍林郭勒市', '150500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150600', '鄂尔多斯市', '150000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150602', '东胜区', '150600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150621', '达拉特旗', '150600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150622', '准格尔旗', '150600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150623', '鄂托克前旗', '150600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150624', '鄂托克旗', '150600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150625', '杭锦旗', '150600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150626', '乌审旗', '150600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150627', '伊金霍洛旗', '150600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150700', '呼伦贝尔市', '150000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150701', '市辖区', '150700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150702', '海拉尔区', '150700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150721', '阿荣旗', '150700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150722', '莫力达瓦达斡尔族自治旗', '150700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150723', '鄂伦春自治旗', '150700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150724', '鄂温克族自治旗', '150700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150725', '陈巴尔虎旗', '150700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150726', '新巴尔虎左旗', '150700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150727', '新巴尔虎右旗', '150700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150781', '满洲里市', '150700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150782', '牙克石市', '150700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150783', '扎兰屯市', '150700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150784', '额尔古纳市', '150700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150785', '根河市', '150700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150800', '巴彦淖尔市', '150000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150801', '市辖区', '150800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150802', '临河区', '150800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150821', '五原县', '150800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150822', '磴口县', '150800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150823', '乌拉特前旗', '150800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150824', '乌拉特中旗', '150800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150825', '乌拉特后旗', '150800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150826', '杭锦后旗', '150800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150900', '乌兰察布市', '150000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150901', '市辖区', '150900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150902', '集宁区', '150900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150921', '卓资县', '150900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150922', '化德县', '150900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150923', '商都县', '150900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150924', '兴和县', '150900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150925', '凉城县', '150900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150926', '察哈尔右翼前旗', '150900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150927', '察哈尔右翼中旗', '150900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150928', '察哈尔右翼后旗', '150900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150929', '四子王旗', '150900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('150981', '丰镇市', '150900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152200', '兴安盟', '150000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152201', '乌兰浩特市', '152200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152202', '阿尔山市', '152200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152221', '科尔沁右翼前旗', '152200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152222', '科尔沁右翼中旗', '152200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152223', '扎赉特旗', '152200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152224', '突泉县', '152200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152500', '锡林郭勒盟', '150000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152501', '二连浩特市', '152500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152502', '锡林浩特市', '152500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152522', '阿巴嘎旗', '152500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152523', '苏尼特左旗', '152500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152524', '苏尼特右旗', '152500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152525', '东乌珠穆沁旗', '152500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152526', '西乌珠穆沁旗', '152500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152527', '太仆寺旗', '152500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152528', '镶黄旗', '152500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152529', '正镶白旗', '152500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152530', '正蓝旗', '152500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152531', '多伦县', '152500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152900', '阿拉善盟', '150000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152921', '阿拉善左旗', '152900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152922', '阿拉善右旗', '152900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('152923', '额济纳旗', '152900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210000', '辽宁省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210100', '沈阳市', '210000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210101', '市辖区', '210100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210102', '和平区', '210100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210103', '沈河区', '210100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210104', '大东区', '210100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210105', '皇姑区', '210100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210106', '铁西区', '210100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210111', '苏家屯区', '210100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210112', '东陵区', '210100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210113', '新城子区', '210100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210114', '于洪区', '210100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210122', '辽中县', '210100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210123', '康平县', '210100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210124', '法库县', '210100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210181', '新民市', '210100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210200', '大连市', '210000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210201', '市辖区', '210200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210202', '中山区', '210200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210203', '西岗区', '210200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210204', '沙河口区', '210200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210211', '甘井子区', '210200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210212', '旅顺口区', '210200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210213', '金州区', '210200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210224', '长海县', '210200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210281', '瓦房店市', '210200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210282', '普兰店市', '210200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210283', '庄河市', '210200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210300', '鞍山市', '210000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210301', '市辖区', '210300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210302', '铁东区', '210300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210303', '铁西区', '210300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210304', '立山区', '210300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210311', '千山区', '210300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210321', '台安县', '210300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210323', '岫岩满族自治县', '210300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210381', '海城市', '210300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210400', '抚顺市', '210000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210401', '市辖区', '210400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210402', '新抚区', '210400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210403', '东洲区', '210400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210404', '望花区', '210400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210411', '顺城区', '210400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210421', '抚顺县', '210400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210422', '新宾满族自治县', '210400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210423', '清原满族自治县', '210400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210500', '本溪市', '210000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210501', '市辖区', '210500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210502', '平山区', '210500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210503', '溪湖区', '210500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210504', '明山区', '210500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210505', '南芬区', '210500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210521', '本溪满族自治县', '210500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210522', '桓仁满族自治县', '210500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210600', '丹东市', '210000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210601', '市辖区', '210600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210602', '元宝区', '210600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210603', '振兴区', '210600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210604', '振安区', '210600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210624', '宽甸满族自治县', '210600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210681', '东港市', '210600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210682', '凤城市', '210600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210700', '锦州市', '210000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210701', '市辖区', '210700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210702', '古塔区', '210700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210703', '凌河区', '210700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210711', '太和区', '210700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210726', '黑山县', '210700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210727', '义县', '210700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210781', '凌海市', '210700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210782', '北宁市', '210700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210800', '营口市', '210000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210801', '市辖区', '210800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210802', '站前区', '210800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210803', '西市区', '210800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210804', '鲅鱼圈区', '210800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210811', '老边区', '210800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210881', '盖州市', '210800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210882', '大石桥市', '210800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210900', '阜新市', '210000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210901', '市辖区', '210900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210902', '海州区', '210900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210903', '新邱区', '210900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210904', '太平区', '210900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210905', '清河门区', '210900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210911', '细河区', '210900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210921', '阜新蒙古族自治县', '210900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('210922', '彰武县', '210900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211000', '辽阳市', '210000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211001', '市辖区', '211000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211002', '白塔区', '211000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211003', '文圣区', '211000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211004', '宏伟区', '211000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211005', '弓长岭区', '211000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211011', '太子河区', '211000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211021', '辽阳县', '211000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211081', '灯塔市', '211000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211100', '盘锦市', '210000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211101', '市辖区', '211100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211102', '双台子区', '211100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211103', '兴隆台区', '211100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211121', '大洼县', '211100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211122', '盘山县', '211100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211200', '铁岭市', '210000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211201', '市辖区', '211200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211202', '银州区', '211200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211204', '清河区', '211200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211221', '铁岭县', '211200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211223', '西丰县', '211200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211224', '昌图县', '211200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211281', '调兵山市', '211200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211282', '开原市', '211200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211300', '朝阳市', '210000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211301', '市辖区', '211300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211302', '双塔区', '211300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211303', '龙城区', '211300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211321', '朝阳县', '211300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211322', '建平县', '211300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211324', '喀喇沁左翼蒙古族自治县', '211300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211381', '北票市', '211300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211382', '凌源市', '211300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211400', '葫芦岛市', '210000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211401', '市辖区', '211400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211402', '连山区', '211400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211403', '龙港区', '211400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211404', '南票区', '211400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211421', '绥中县', '211400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211422', '建昌县', '211400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('211481', '兴城市', '211400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220000', '吉林省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220100', '长春市', '220000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220101', '市辖区', '220100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220102', '南关区', '220100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220103', '宽城区', '220100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220104', '朝阳区', '220100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220105', '二道区', '220100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220106', '绿园区', '220100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220112', '双阳区', '220100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220122', '农安县', '220100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220181', '九台市', '220100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220182', '榆树市', '220100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220183', '德惠市', '220100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220200', '吉林市', '220000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220201', '市辖区', '220200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220202', '昌邑区', '220200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220203', '龙潭区', '220200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220204', '船营区', '220200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220211', '丰满区', '220200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220221', '永吉县', '220200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220281', '蛟河市', '220200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220282', '桦甸市', '220200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220283', '舒兰市', '220200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220284', '磐石市', '220200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220300', '四平市', '220000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220301', '市辖区', '220300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220302', '铁西区', '220300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220303', '铁东区', '220300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220322', '梨树县', '220300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220323', '伊通满族自治县', '220300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220381', '公主岭市', '220300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220382', '双辽市', '220300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220400', '辽源市', '220000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220401', '市辖区', '220400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220402', '龙山区', '220400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220403', '西安区', '220400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220421', '东丰县', '220400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220422', '东辽县', '220400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220500', '通化市', '220000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220501', '市辖区', '220500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220502', '东昌区', '220500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220503', '二道江区', '220500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220521', '通化县', '220500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220523', '辉南县', '220500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220524', '柳河县', '220500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220581', '梅河口市', '220500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220582', '集安市', '220500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220600', '白山市', '220000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220601', '市辖区', '220600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220602', '八道江区', '220600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220621', '抚松县', '220600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220622', '靖宇县', '220600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220623', '长白朝鲜族自治县', '220600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220625', '江源县', '220600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220681', '临江市', '220600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220700', '松原市', '220000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220701', '市辖区', '220700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220702', '宁江区', '220700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220721', '前郭尔罗斯蒙古族自治县', '220700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220722', '长岭县', '220700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220723', '乾安县', '220700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220724', '扶余县', '220700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220800', '白城市', '220000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220801', '市辖区', '220800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220802', '洮北区', '220800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220821', '镇赉县', '220800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220822', '通榆县', '220800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220881', '洮南市', '220800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('220882', '大安市', '220800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('222400', '延边朝鲜族自治州', '220000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('222401', '延吉市', '222400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('222402', '图们市', '222400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('222403', '敦化市', '222400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('222404', '珲春市', '222400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('222405', '龙井市', '222400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('222406', '和龙市', '222400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('222424', '汪清县', '222400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('222426', '安图县', '222400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230000', '黑龙江', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230100', '哈尔滨市', '230000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230101', '市辖区', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230102', '道里区', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230103', '南岗区', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230104', '道外区', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230106', '香坊区', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230107', '动力区', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230108', '平房区', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230109', '松北区', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230111', '呼兰区', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230123', '依兰县', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230124', '方正县', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230125', '宾县', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230126', '巴彦县', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230127', '木兰县', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230128', '通河县', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230129', '延寿县', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230181', '阿城市', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230182', '双城市', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230183', '尚志市', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230184', '五常市', '230100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230200', '齐齐哈尔市', '230000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230201', '市辖区', '230200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230202', '龙沙区', '230200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230203', '建华区', '230200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230204', '铁锋区', '230200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230205', '昂昂溪区', '230200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230206', '富拉尔基区', '230200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230207', '碾子山区', '230200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230208', '梅里斯达斡尔族区', '230200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230221', '龙江县', '230200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230223', '依安县', '230200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230224', '泰来县', '230200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230225', '甘南县', '230200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230227', '富裕县', '230200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230229', '克山县', '230200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230230', '克东县', '230200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230231', '拜泉县', '230200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230281', '讷河市', '230200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230300', '鸡西市', '230000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230301', '市辖区', '230300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230302', '鸡冠区', '230300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230303', '恒山区', '230300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230304', '滴道区', '230300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230305', '梨树区', '230300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230306', '城子河区', '230300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230307', '麻山区', '230300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230321', '鸡东县', '230300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230381', '虎林市', '230300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230382', '密山市', '230300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230400', '鹤岗市', '230000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230401', '市辖区', '230400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230402', '向阳区', '230400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230403', '工农区', '230400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230404', '南山区', '230400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230405', '兴安区', '230400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230406', '东山区', '230400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230407', '兴山区', '230400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230421', '萝北县', '230400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230422', '绥滨县', '230400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230500', '双鸭山市', '230000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230501', '市辖区', '230500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230502', '尖山区', '230500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230503', '岭东区', '230500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230505', '四方台区', '230500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230506', '宝山区', '230500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230521', '集贤县', '230500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230522', '友谊县', '230500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230523', '宝清县', '230500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230524', '饶河县', '230500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230600', '大庆市', '230000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230601', '市辖区', '230600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230602', '萨尔图区', '230600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230603', '龙凤区', '230600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230604', '让胡路区', '230600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230605', '红岗区', '230600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230606', '大同区', '230600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230621', '肇州县', '230600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230622', '肇源县', '230600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230623', '林甸县', '230600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230624', '杜尔伯特蒙古族自治县', '230600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230700', '伊春市', '230000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230701', '市辖区', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230702', '伊春区', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230703', '南岔区', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230704', '友好区', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230705', '西林区', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230706', '翠峦区', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230707', '新青区', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230708', '美溪区', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230709', '金山屯区', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230710', '五营区', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230711', '乌马河区', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230712', '汤旺河区', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230713', '带岭区', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230714', '乌伊岭区', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230715', '红星区', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230716', '上甘岭区', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230722', '嘉荫县', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230781', '铁力市', '230700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230800', '佳木斯市', '230000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230801', '市辖区', '230800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230802', '永红区', '230800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230803', '向阳区', '230800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230804', '前进区', '230800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230805', '东风区', '230800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230811', '郊区', '230800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230822', '桦南县', '230800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230826', '桦川县', '230800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230828', '汤原县', '230800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230833', '抚远县', '230800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230881', '同江市', '230800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230882', '富锦市', '230800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230900', '七台河市', '230000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230901', '市辖区', '230900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230902', '新兴区', '230900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230903', '桃山区', '230900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230904', '茄子河区', '230900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('230921', '勃利县', '230900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231000', '牡丹江市', '230000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231001', '市辖区', '231000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231002', '东安区', '231000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231003', '阳明区', '231000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231004', '爱民区', '231000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231005', '西安区', '231000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231024', '东宁县', '231000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231025', '林口县', '231000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231081', '绥芬河市', '231000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231083', '海林市', '231000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231084', '宁安市', '231000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231085', '穆棱市', '231000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231100', '黑河市', '230000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231101', '市辖区', '231100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231102', '爱辉区', '231100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231121', '嫩江县', '231100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231123', '逊克县', '231100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231124', '孙吴县', '231100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231181', '北安市', '231100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231182', '五大连池市', '231100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231200', '绥化市', '230000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231201', '市辖区', '231200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231202', '北林区', '231200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231221', '望奎县', '231200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231222', '兰西县', '231200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231223', '青冈县', '231200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231224', '庆安县', '231200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231225', '明水县', '231200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231226', '绥棱县', '231200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231281', '安达市', '231200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231282', '肇东市', '231200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('231283', '海伦市', '231200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('232700', '大兴安岭地区', '230000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('232721', '呼玛县', '232700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('232722', '塔河县', '232700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('232723', '漠河县', '232700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310000', '上海市', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310101', '黄浦区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310103', '卢湾区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310104', '徐汇区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310105', '长宁区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310106', '静安区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310107', '普陀区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310108', '闸北区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310109', '虹口区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310110', '杨浦区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310112', '闵行区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310113', '宝山区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310114', '嘉定区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310115', '浦东新区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310116', '金山区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310117', '松江区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310118', '青浦区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310119', '南汇区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310120', '奉贤区', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('310230', '崇明县', '310000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320000', '江苏省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320100', '南京市', '320000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320101', '市辖区', '320100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320102', '玄武区', '320100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320103', '白下区', '320100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320104', '秦淮区', '320100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320105', '建邺区', '320100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320106', '鼓楼区', '320100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320107', '下关区', '320100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320111', '浦口区', '320100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320113', '栖霞区', '320100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320114', '雨花台区', '320100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320115', '江宁区', '320100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320116', '六合区', '320100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320124', '溧水县', '320100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320125', '高淳县', '320100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320200', '无锡市', '320000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320201', '市辖区', '320200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320202', '崇安区', '320200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320203', '南长区', '320200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320204', '北塘区', '320200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320205', '锡山区', '320200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320206', '惠山区', '320200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320211', '滨湖区', '320200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320281', '江阴市', '320200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320282', '宜兴市', '320200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320300', '徐州市', '320000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320301', '市辖区', '320300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320302', '鼓楼区', '320300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320303', '云龙区', '320300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320304', '九里区', '320300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320305', '贾汪区', '320300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320311', '泉山区', '320300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320321', '丰县', '320300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320322', '沛县', '320300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320323', '铜山县', '320300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320324', '睢宁县', '320300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320381', '新沂市', '320300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320382', '邳州市', '320300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320400', '常州市', '320000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320401', '市辖区', '320400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320402', '天宁区', '320400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320404', '钟楼区', '320400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320405', '戚墅堰区', '320400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320411', '新北区', '320400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320412', '武进区', '320400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320481', '溧阳市', '320400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320482', '金坛市', '320400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320500', '苏州市', '320000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320501', '市辖区', '320500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320502', '沧浪区', '320500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320503', '平江区', '320500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320504', '金阊区', '320500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320505', '虎丘区', '320500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320506', '吴中区', '320500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320507', '相城区', '320500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320581', '常熟市', '320500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320582', '张家港市', '320500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320583', '昆山市', '320500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320584', '吴江市', '320500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320585', '太仓市', '320500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320600', '南通市', '320000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320601', '市辖区', '320600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320602', '崇川区', '320600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320611', '港闸区', '320600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320621', '海安县', '320600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320623', '如东县', '320600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320681', '启东市', '320600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320682', '如皋市', '320600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320683', '通州市', '320600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320684', '海门市', '320600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320700', '连云港市', '320000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320701', '市辖区', '320700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320703', '连云区', '320700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320705', '新浦区', '320700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320706', '海州区', '320700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320721', '赣榆县', '320700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320722', '东海县', '320700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320723', '灌云县', '320700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320724', '灌南县', '320700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320800', '淮安市', '320000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320801', '市辖区', '320800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320802', '清河区', '320800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320803', '楚州区', '320800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320804', '淮阴区', '320800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320811', '清浦区', '320800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320826', '涟水县', '320800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320829', '洪泽县', '320800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320830', '盱眙县', '320800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320831', '金湖县', '320800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320900', '盐城市', '320000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320901', '市辖区', '320900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320902', '亭湖区', '320900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320903', '盐都区', '320900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320921', '响水县', '320900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320922', '滨海县', '320900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320923', '阜宁县', '320900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320924', '射阳县', '320900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320925', '建湖县', '320900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320981', '东台市', '320900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('320982', '大丰市', '320900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321000', '扬州市', '320000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321001', '市辖区', '321000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321002', '广陵区', '321000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321003', '邗江区', '321000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321011', '郊区', '321000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321023', '宝应县', '321000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321081', '仪征市', '321000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321084', '高邮市', '321000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321088', '江都市', '321000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321100', '镇江市', '320000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321101', '市辖区', '321100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321102', '京口区', '321100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321111', '润州区', '321100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321112', '丹徒区', '321100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321181', '丹阳市', '321100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321182', '扬中市', '321100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321183', '句容市', '321100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321200', '泰州市', '320000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321201', '市辖区', '321200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321202', '海陵区', '321200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321203', '高港区', '321200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321281', '兴化市', '321200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321282', '靖江市', '321200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321283', '泰兴市', '321200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321284', '姜堰市', '321200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321300', '宿迁市', '320000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321301', '市辖区', '321300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321302', '宿城区', '321300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321311', '宿豫区', '321300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321322', '沭阳县', '321300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321323', '泗阳县', '321300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('321324', '泗洪县', '321300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330000', '浙江省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330100', '杭州市', '330000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330101', '市辖区', '330100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330102', '上城区', '330100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330103', '下城区', '330100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330104', '江干区', '330100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330105', '拱墅区', '330100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330106', '西湖区', '330100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330108', '滨江区', '330100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330109', '萧山区', '330100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330110', '余杭区', '330100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330122', '桐庐县', '330100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330127', '淳安县', '330100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330182', '建德市', '330100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330183', '富阳市', '330100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330185', '临安市', '330100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330200', '宁波市', '330000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330201', '市辖区', '330200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330203', '海曙区', '330200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330204', '江东区', '330200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330205', '江北区', '330200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330206', '北仑区', '330200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330211', '镇海区', '330200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330212', '鄞州区', '330200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330225', '象山县', '330200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330226', '宁海县', '330200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330281', '余姚市', '330200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330282', '慈溪市', '330200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330283', '奉化市', '330200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330300', '温州市', '330000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330301', '市辖区', '330300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330302', '鹿城区', '330300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330303', '龙湾区', '330300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330304', '瓯海区', '330300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330322', '洞头县', '330300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330324', '永嘉县', '330300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330326', '平阳县', '330300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330327', '苍南县', '330300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330328', '文成县', '330300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330329', '泰顺县', '330300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330381', '瑞安市', '330300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330382', '乐清市', '330300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330400', '嘉兴市', '330000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330401', '市辖区', '330400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330402', '秀城区', '330400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330411', '秀洲区', '330400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330421', '嘉善县', '330400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330424', '海盐县', '330400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330481', '海宁市', '330400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330482', '平湖市', '330400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330483', '桐乡市', '330400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330500', '湖州市', '330000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330501', '市辖区', '330500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330502', '吴兴区', '330500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330503', '南浔区', '330500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330521', '德清县', '330500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330522', '长兴县', '330500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330523', '安吉县', '330500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330600', '绍兴市', '330000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330601', '市辖区', '330600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330602', '越城区', '330600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330621', '绍兴县', '330600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330624', '新昌县', '330600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330681', '诸暨市', '330600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330682', '上虞市', '330600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330683', '嵊州市', '330600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330700', '金华市', '330000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330701', '市辖区', '330700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330702', '婺城区', '330700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330703', '金东区', '330700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330723', '武义县', '330700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330726', '浦江县', '330700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330727', '磐安县', '330700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330781', '兰溪市', '330700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330782', '义乌市', '330700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330783', '东阳市', '330700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330784', '永康市', '330700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330800', '衢州市', '330000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330801', '市辖区', '330800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330802', '柯城区', '330800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330803', '衢江区', '330800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330822', '常山县', '330800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330824', '开化县', '330800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330825', '龙游县', '330800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330881', '江山市', '330800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330900', '舟山市', '330000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330901', '市辖区', '330900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330902', '定海区', '330900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330903', '普陀区', '330900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330921', '岱山县', '330900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('330922', '嵊泗县', '330900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331000', '台州市', '330000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331001', '市辖区', '331000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331002', '椒江区', '331000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331003', '黄岩区', '331000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331004', '路桥区', '331000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331021', '玉环县', '331000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331022', '三门县', '331000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331023', '天台县', '331000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331024', '仙居县', '331000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331081', '温岭市', '331000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331082', '临海市', '331000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331100', '丽水市', '330000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331101', '市辖区', '331100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331102', '莲都区', '331100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331121', '青田县', '331100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331122', '缙云县', '331100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331123', '遂昌县', '331100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331124', '松阳县', '331100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331125', '云和县', '331100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331126', '庆元县', '331100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331127', '景宁畲族自治县', '331100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('331181', '龙泉市', '331100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340000', '安徽省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340100', '合肥市', '340000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340101', '市辖区', '340100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340102', '瑶海区', '340100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340103', '庐阳区', '340100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340104', '蜀山区', '340100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340111', '包河区', '340100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340121', '长丰县', '340100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340122', '肥东县', '340100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340123', '肥西县', '340100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340200', '芜湖市', '340000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340201', '市辖区', '340200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340202', '镜湖区', '340200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340203', '马塘区', '340200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340204', '新芜区', '340200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340207', '鸠江区', '340200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340221', '芜湖县', '340200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340222', '繁昌县', '340200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340223', '南陵县', '340200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340300', '蚌埠市', '340000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340301', '市辖区', '340300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340302', '龙子湖区', '340300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340303', '蚌山区', '340300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340304', '禹会区', '340300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340311', '淮上区', '340300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340321', '怀远县', '340300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340322', '五河县', '340300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340323', '固镇县', '340300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340400', '淮南市', '340000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340401', '市辖区', '340400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340402', '大通区', '340400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340403', '田家庵区', '340400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340404', '谢家集区', '340400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340405', '八公山区', '340400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340406', '潘集区', '340400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340421', '凤台县', '340400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340500', '马鞍山市', '340000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340501', '市辖区', '340500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340502', '金家庄区', '340500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340503', '花山区', '340500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340504', '雨山区', '340500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340521', '当涂县', '340500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340600', '淮北市', '340000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340601', '市辖区', '340600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340602', '杜集区', '340600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340603', '相山区', '340600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340604', '烈山区', '340600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340621', '濉溪县', '340600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340700', '铜陵市', '340000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340701', '市辖区', '340700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340702', '铜官山区', '340700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340703', '狮子山区', '340700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340711', '郊区', '340700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340721', '铜陵县', '340700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340800', '安庆市', '340000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340801', '市辖区', '340800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340802', '迎江区', '340800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340803', '大观区', '340800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340811', '郊区', '340800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340822', '怀宁县', '340800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340823', '枞阳县', '340800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340824', '潜山县', '340800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340825', '太湖县', '340800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340826', '宿松县', '340800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340827', '望江县', '340800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340828', '岳西县', '340800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340881', '桐城市', '340800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341000', '黄山市', '340000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341001', '市辖区', '341000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341002', '屯溪区', '341000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341003', '黄山区', '341000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341004', '徽州区', '341000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341021', '歙县', '341000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341022', '休宁县', '341000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341023', '黟县', '341000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341024', '祁门县', '341000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341100', '滁州市', '340000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341101', '市辖区', '341100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341102', '琅琊区', '341100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341103', '南谯区', '341100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341122', '来安县', '341100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341124', '全椒县', '341100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341125', '定远县', '341100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341126', '凤阳县', '341100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341181', '天长市', '341100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341182', '明光市', '341100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341200', '阜阳市', '340000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341201', '市辖区', '341200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341202', '颍州区', '341200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341203', '颍东区', '341200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341204', '颍泉区', '341200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341221', '临泉县', '341200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341222', '太和县', '341200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341225', '阜南县', '341200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341226', '颍上县', '341200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341282', '界首市', '341200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341300', '宿州市', '340000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341301', '市辖区', '341300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341302', '墉桥区', '341300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341321', '砀山县', '341300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341322', '萧县', '341300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341323', '灵璧县', '341300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341324', '泗县', '341300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341400', '巢湖市', '340000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341401', '市辖区', '341400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341402', '居巢区', '341400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341421', '庐江县', '341400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341422', '无为县', '341400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341423', '含山县', '341400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341424', '和县', '341400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341500', '六安市', '340000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341501', '市辖区', '341500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341502', '金安区', '341500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341503', '裕安区', '341500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341521', '寿县', '341500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341522', '霍邱县', '341500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341523', '舒城县', '341500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341524', '金寨县', '341500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341525', '霍山县', '341500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341600', '亳州市', '340000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341601', '市辖区', '341600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341602', '谯城区', '341600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341621', '涡阳县', '341600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341622', '蒙城县', '341600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341623', '利辛县', '341600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341700', '池州市', '340000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341701', '市辖区', '341700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341702', '贵池区', '341700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341721', '东至县', '341700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341722', '石台县', '341700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341723', '青阳县', '341700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341800', '宣城市', '340000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341801', '市辖区', '341800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341802', '宣州区', '341800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341821', '郎溪县', '341800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341822', '广德县', '341800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341823', '泾县', '341800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341824', '绩溪县', '341800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341825', '旌德县', '341800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('341881', '宁国市', '341800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350000', '福建省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350100', '福州市', '350000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350101', '市辖区', '350100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350102', '鼓楼区', '350100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350103', '台江区', '350100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350104', '仓山区', '350100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350105', '马尾区', '350100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350111', '晋安区', '350100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350121', '闽侯县', '350100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350122', '连江县', '350100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350123', '罗源县', '350100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350124', '闽清县', '350100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350125', '永泰县', '350100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350128', '平潭县', '350100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350181', '福清市', '350100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350182', '长乐市', '350100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350200', '厦门市', '350000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350201', '市辖区', '350200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350203', '思明区', '350200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350205', '海沧区', '350200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350206', '湖里区', '350200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350211', '集美区', '350200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350212', '同安区', '350200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350213', '翔安区', '350200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350300', '莆田市', '350000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350301', '市辖区', '350300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350302', '城厢区', '350300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350303', '涵江区', '350300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350304', '荔城区', '350300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350305', '秀屿区', '350300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350322', '仙游县', '350300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350400', '三明市', '350000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350401', '市辖区', '350400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350402', '梅列区', '350400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350403', '三元区', '350400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350421', '明溪县', '350400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350423', '清流县', '350400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350424', '宁化县', '350400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350425', '大田县', '350400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350426', '尤溪县', '350400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350427', '沙县', '350400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350428', '将乐县', '350400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350429', '泰宁县', '350400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350430', '建宁县', '350400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350481', '永安市', '350400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350500', '泉州市', '350000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350501', '市辖区', '350500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350502', '鲤城区', '350500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350503', '丰泽区', '350500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350504', '洛江区', '350500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350505', '泉港区', '350500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350521', '惠安县', '350500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350524', '安溪县', '350500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350525', '永春县', '350500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350526', '德化县', '350500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350527', '金门县', '350500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350581', '石狮市', '350500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350582', '晋江市', '350500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350583', '南安市', '350500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350600', '漳州市', '350000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350601', '市辖区', '350600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350602', '芗城区', '350600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350603', '龙文区', '350600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350622', '云霄县', '350600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350623', '漳浦县', '350600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350624', '诏安县', '350600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350625', '长泰县', '350600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350626', '东山县', '350600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350627', '南靖县', '350600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350628', '平和县', '350600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350629', '华安县', '350600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350681', '龙海市', '350600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350700', '南平市', '350000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350701', '市辖区', '350700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350702', '延平区', '350700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350721', '顺昌县', '350700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350722', '浦城县', '350700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350723', '光泽县', '350700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350724', '松溪县', '350700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350725', '政和县', '350700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350781', '邵武市', '350700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350782', '武夷山市', '350700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350783', '建瓯市', '350700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350784', '建阳市', '350700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350800', '龙岩市', '350000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350801', '市辖区', '350800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350802', '新罗区', '350800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350821', '长汀县', '350800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350822', '永定县', '350800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350823', '上杭县', '350800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350824', '武平县', '350800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350825', '连城县', '350800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350881', '漳平市', '350800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350900', '宁德市', '350000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350901', '市辖区', '350900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350902', '蕉城区', '350900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350921', '霞浦县', '350900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350922', '古田县', '350900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350923', '屏南县', '350900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350924', '寿宁县', '350900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350925', '周宁县', '350900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350926', '柘荣县', '350900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350981', '福安市', '350900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('350982', '福鼎市', '350900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360000', '江西省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360100', '南昌市', '360000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360101', '市辖区', '360100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360102', '东湖区', '360100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360103', '西湖区', '360100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360104', '青云谱区', '360100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360105', '湾里区', '360100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360111', '青山湖区', '360100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360121', '南昌县', '360100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360122', '新建县', '360100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360123', '安义县', '360100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360124', '进贤县', '360100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360200', '景德镇市', '360000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360201', '市辖区', '360200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360202', '昌江区', '360200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360203', '珠山区', '360200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360222', '浮梁县', '360200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360281', '乐平市', '360200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360300', '萍乡市', '360000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360301', '市辖区', '360300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360302', '安源区', '360300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360313', '湘东区', '360300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360321', '莲花县', '360300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360322', '上栗县', '360300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360323', '芦溪县', '360300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360400', '九江市', '360000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360401', '市辖区', '360400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360402', '庐山区', '360400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360403', '浔阳区', '360400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360421', '九江县', '360400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360423', '武宁县', '360400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360424', '修水县', '360400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360425', '永修县', '360400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360426', '德安县', '360400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360427', '星子县', '360400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360428', '都昌县', '360400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360429', '湖口县', '360400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360430', '彭泽县', '360400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360481', '瑞昌市', '360400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360500', '新余市', '360000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360501', '市辖区', '360500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360502', '渝水区', '360500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360521', '分宜县', '360500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360600', '鹰潭市', '360000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360601', '市辖区', '360600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360602', '月湖区', '360600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360622', '余江县', '360600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360681', '贵溪市', '360600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360700', '赣州市', '360000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360701', '市辖区', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360702', '章贡区', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360721', '赣县', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360722', '信丰县', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360723', '大余县', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360724', '上犹县', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360725', '崇义县', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360726', '安远县', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360727', '龙南县', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360728', '定南县', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360729', '全南县', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360730', '宁都县', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360731', '于都县', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360732', '兴国县', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360733', '会昌县', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360734', '寻乌县', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360735', '石城县', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360781', '瑞金市', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360782', '南康市', '360700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360800', '吉安市', '360000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360801', '市辖区', '360800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360802', '吉州区', '360800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360803', '青原区', '360800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360821', '吉安县', '360800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360822', '吉水县', '360800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360823', '峡江县', '360800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360824', '新干县', '360800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360825', '永丰县', '360800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360826', '泰和县', '360800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360827', '遂川县', '360800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360828', '万安县', '360800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360829', '安福县', '360800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360830', '永新县', '360800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360881', '井冈山市', '360800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360900', '宜春市', '360000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360901', '市辖区', '360900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360902', '袁州区', '360900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360921', '奉新县', '360900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360922', '万载县', '360900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360923', '上高县', '360900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360924', '宜丰县', '360900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360925', '靖安县', '360900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360926', '铜鼓县', '360900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360981', '丰城市', '360900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360982', '樟树市', '360900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('360983', '高安市', '360900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361000', '抚州市', '360000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361001', '市辖区', '361000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361002', '临川区', '361000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361021', '南城县', '361000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361022', '黎川县', '361000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361023', '南丰县', '361000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361024', '崇仁县', '361000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361025', '乐安县', '361000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361026', '宜黄县', '361000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361027', '金溪县', '361000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361028', '资溪县', '361000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361029', '东乡县', '361000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361030', '广昌县', '361000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361100', '上饶市', '360000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361101', '市辖区', '361100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361102', '信州区', '361100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361121', '上饶县', '361100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361122', '广丰县', '361100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361123', '玉山县', '361100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361124', '铅山县', '361100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361125', '横峰县', '361100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361126', '弋阳县', '361100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361127', '余干县', '361100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361128', '鄱阳县', '361100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361129', '万年县', '361100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361130', '婺源县', '361100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('361181', '德兴市', '361100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370000', '山东省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370100', '济南市', '370000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370101', '市辖区', '370100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370102', '历下区', '370100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370103', '市中区', '370100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370104', '槐荫区', '370100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370105', '天桥区', '370100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370112', '历城区', '370100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370113', '长清区', '370100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370124', '平阴县', '370100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370125', '济阳县', '370100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370126', '商河县', '370100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370181', '章丘市', '370100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370200', '青岛市', '370000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370201', '市辖区', '370200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370202', '市南区', '370200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370203', '市北区', '370200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370205', '四方区', '370200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370211', '黄岛区', '370200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370212', '崂山区', '370200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370213', '李沧区', '370200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370214', '城阳区', '370200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370281', '胶州市', '370200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370282', '即墨市', '370200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370283', '平度市', '370200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370284', '胶南市', '370200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370285', '莱西市', '370200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370300', '淄博市', '370000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370301', '市辖区', '370300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370302', '淄川区', '370300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370303', '张店区', '370300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370304', '博山区', '370300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370305', '临淄区', '370300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370306', '周村区', '370300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370321', '桓台县', '370300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370322', '高青县', '370300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370323', '沂源县', '370300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370400', '枣庄市', '370000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370401', '市辖区', '370400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370402', '市中区', '370400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370403', '薛城区', '370400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370404', '峄城区', '370400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370405', '台儿庄区', '370400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370406', '山亭区', '370400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370481', '滕州市', '370400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370500', '东营市', '370000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370501', '市辖区', '370500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370502', '东营区', '370500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370503', '河口区', '370500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370521', '垦利县', '370500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370522', '利津县', '370500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370523', '广饶县', '370500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370600', '烟台市', '370000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370601', '市辖区', '370600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370602', '芝罘区', '370600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370611', '福山区', '370600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370612', '牟平区', '370600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370613', '莱山区', '370600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370634', '长岛县', '370600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370681', '龙口市', '370600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370682', '莱阳市', '370600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370683', '莱州市', '370600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370684', '蓬莱市', '370600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370685', '招远市', '370600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370686', '栖霞市', '370600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370687', '海阳市', '370600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370700', '潍坊市', '370000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370701', '市辖区', '370700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370702', '潍城区', '370700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370703', '寒亭区', '370700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370704', '坊子区', '370700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370705', '奎文区', '370700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370724', '临朐县', '370700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370725', '昌乐县', '370700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370781', '青州市', '370700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370782', '诸城市', '370700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370783', '寿光市', '370700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370784', '安丘市', '370700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370785', '高密市', '370700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370786', '昌邑市', '370700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370800', '济宁市', '370000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370801', '市辖区', '370800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370802', '市中区', '370800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370811', '任城区', '370800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370826', '微山县', '370800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370827', '鱼台县', '370800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370828', '金乡县', '370800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370829', '嘉祥县', '370800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370830', '汶上县', '370800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370831', '泗水县', '370800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370832', '梁山县', '370800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370881', '曲阜市', '370800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370882', '兖州市', '370800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370883', '邹城市', '370800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370900', '泰安市', '370000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370901', '市辖区', '370900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370902', '泰山区', '370900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370903', '岱岳区', '370900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370921', '宁阳县', '370900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370923', '东平县', '370900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370982', '新泰市', '370900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('370983', '肥城市', '370900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371000', '威海市', '370000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371001', '市辖区', '371000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371002', '环翠区', '371000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371081', '文登市', '371000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371082', '荣成市', '371000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371083', '乳山市', '371000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371100', '日照市', '370000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371101', '市辖区', '371100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371102', '东港区', '371100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371103', '岚山区', '371100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371121', '五莲县', '371100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371122', '莒县', '371100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371200', '莱芜市', '370000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371201', '市辖区', '371200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371202', '莱城区', '371200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371203', '钢城区', '371200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371300', '临沂市', '370000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371301', '市辖区', '371300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371302', '兰山区', '371300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371311', '罗庄区', '371300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371312', '河东区', '371300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371321', '沂南县', '371300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371322', '郯城县', '371300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371323', '沂水县', '371300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371324', '苍山县', '371300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371325', '费县', '371300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371326', '平邑县', '371300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371327', '莒南县', '371300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371328', '蒙阴县', '371300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371329', '临沭县', '371300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371400', '德州市', '370000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371401', '市辖区', '371400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371402', '德城区', '371400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371421', '陵县', '371400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371422', '宁津县', '371400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371423', '庆云县', '371400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371424', '临邑县', '371400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371425', '齐河县', '371400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371426', '平原县', '371400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371427', '夏津县', '371400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371428', '武城县', '371400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371481', '乐陵市', '371400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371482', '禹城市', '371400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371500', '聊城市', '370000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371501', '市辖区', '371500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371502', '东昌府区', '371500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371521', '阳谷县', '371500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371522', '莘县', '371500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371523', '茌平县', '371500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371524', '东阿县', '371500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371525', '冠县', '371500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371526', '高唐县', '371500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371581', '临清市', '371500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371600', '滨州市', '370000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371601', '市辖区', '371600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371602', '滨城区', '371600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371621', '惠民县', '371600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371622', '阳信县', '371600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371623', '无棣县', '371600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371624', '沾化县', '371600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371625', '博兴县', '371600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371626', '邹平县', '371600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371700', '荷泽市', '370000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371701', '市辖区', '371700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371702', '牡丹区', '371700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371721', '曹县', '371700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371722', '单县', '371700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371723', '成武县', '371700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371724', '巨野县', '371700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371725', '郓城县', '371700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371726', '鄄城县', '371700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371727', '定陶县', '371700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('371728', '东明县', '371700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410000', '河南省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410100', '郑州市', '410000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410101', '市辖区', '410100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410102', '中原区', '410100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410103', '二七区', '410100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410104', '管城回族区', '410100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410105', '金水区', '410100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410106', '上街区', '410100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410108', '邙山区', '410100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410122', '中牟县', '410100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410181', '巩义市', '410100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410182', '荥阳市', '410100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410183', '新密市', '410100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410184', '新郑市', '410100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410185', '登封市', '410100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410200', '开封市', '410000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410201', '市辖区', '410200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410202', '龙亭区', '410200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410203', '顺河回族区', '410200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410204', '鼓楼区', '410200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410205', '南关区', '410200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410211', '郊区', '410200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410221', '杞县', '410200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410222', '通许县', '410200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410223', '尉氏县', '410200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410224', '开封县', '410200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410225', '兰考县', '410200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410300', '洛阳市', '410000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410301', '市辖区', '410300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410302', '老城区', '410300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410303', '西工区', '410300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410304', '廛河回族区', '410300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410305', '涧西区', '410300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410306', '吉利区', '410300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410311', '洛龙区', '410300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410322', '孟津县', '410300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410323', '新安县', '410300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410324', '栾川县', '410300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410325', '嵩县', '410300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410326', '汝阳县', '410300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410327', '宜阳县', '410300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410328', '洛宁县', '410300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410329', '伊川县', '410300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410381', '偃师市', '410300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410400', '平顶山市', '410000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410401', '市辖区', '410400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410402', '新华区', '410400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410403', '卫东区', '410400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410404', '石龙区', '410400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410411', '湛河区', '410400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410421', '宝丰县', '410400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410422', '叶县', '410400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410423', '鲁山县', '410400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410425', '郏县', '410400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410481', '舞钢市', '410400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410482', '汝州市', '410400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410500', '安阳市', '410000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410501', '市辖区', '410500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410502', '文峰区', '410500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410503', '北关区', '410500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410505', '殷都区', '410500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410506', '龙安区', '410500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410522', '安阳县', '410500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410523', '汤阴县', '410500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410526', '滑县', '410500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410527', '内黄县', '410500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410581', '林州市', '410500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410600', '鹤壁市', '410000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410601', '市辖区', '410600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410602', '鹤山区', '410600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410603', '山城区', '410600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410611', '淇滨区', '410600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410621', '浚县', '410600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410622', '淇县', '410600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410700', '新乡市', '410000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410701', '市辖区', '410700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410702', '红旗区', '410700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410703', '卫滨区', '410700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410704', '凤泉区', '410700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410711', '牧野区', '410700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410721', '新乡县', '410700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410724', '获嘉县', '410700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410725', '原阳县', '410700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410726', '延津县', '410700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410727', '封丘县', '410700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410728', '长垣县', '410700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410781', '卫辉市', '410700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410782', '辉县市', '410700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410800', '焦作市', '410000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410801', '市辖区', '410800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410802', '解放区', '410800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410803', '中站区', '410800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410804', '马村区', '410800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410811', '山阳区', '410800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410821', '修武县', '410800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410822', '博爱县', '410800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410823', '武陟县', '410800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410825', '温县', '410800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410881', '济源市', '410800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410882', '沁阳市', '410800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410883', '孟州市', '410800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410900', '濮阳市', '410000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410901', '市辖区', '410900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410902', '华龙区', '410900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410922', '清丰县', '410900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410923', '南乐县', '410900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410926', '范县', '410900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410927', '台前县', '410900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('410928', '濮阳县', '410900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411000', '许昌市', '410000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411001', '市辖区', '411000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411002', '魏都区', '411000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411023', '许昌县', '411000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411024', '鄢陵县', '411000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411025', '襄城县', '411000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411081', '禹州市', '411000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411082', '长葛市', '411000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411100', '漯河市', '410000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411101', '市辖区', '411100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411102', '源汇区', '411100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411103', '郾城区', '411100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411104', '召陵区', '411100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411121', '舞阳县', '411100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411122', '临颍县', '411100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411200', '三门峡市', '410000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411201', '市辖区', '411200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411202', '湖滨区', '411200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411221', '渑池县', '411200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411222', '陕县', '411200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411224', '卢氏县', '411200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411281', '义马市', '411200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411282', '灵宝市', '411200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411300', '南阳市', '410000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411301', '市辖区', '411300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411302', '宛城区', '411300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411303', '卧龙区', '411300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411321', '南召县', '411300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411322', '方城县', '411300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411323', '西峡县', '411300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411324', '镇平县', '411300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411325', '内乡县', '411300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411326', '淅川县', '411300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411327', '社旗县', '411300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411328', '唐河县', '411300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411329', '新野县', '411300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411330', '桐柏县', '411300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411381', '邓州市', '411300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411400', '商丘市', '410000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411401', '市辖区', '411400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411402', '梁园区', '411400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411403', '睢阳区', '411400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411421', '民权县', '411400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411422', '睢县', '411400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411423', '宁陵县', '411400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411424', '柘城县', '411400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411425', '虞城县', '411400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411426', '夏邑县', '411400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411481', '永城市', '411400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411500', '信阳市', '410000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411501', '市辖区', '411500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411502', '师河区', '411500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411503', '平桥区', '411500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411521', '罗山县', '411500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411522', '光山县', '411500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411523', '新县', '411500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411524', '商城县', '411500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411525', '固始县', '411500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411526', '潢川县', '411500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411527', '淮滨县', '411500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411528', '息县', '411500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411600', '周口市', '410000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411601', '市辖区', '411600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411602', '川汇区', '411600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411621', '扶沟县', '411600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411622', '西华县', '411600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411623', '商水县', '411600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411624', '沈丘县', '411600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411625', '郸城县', '411600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411626', '淮阳县', '411600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411627', '太康县', '411600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411628', '鹿邑县', '411600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411681', '项城市', '411600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411700', '驻马店市', '410000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411701', '市辖区', '411700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411702', '驿城区', '411700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411721', '西平县', '411700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411722', '上蔡县', '411700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411723', '平舆县', '411700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411724', '正阳县', '411700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411725', '确山县', '411700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411726', '泌阳县', '411700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411727', '汝南县', '411700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411728', '遂平县', '411700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('411729', '新蔡县', '411700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420000', '湖北省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420100', '武汉市', '420000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420101', '市辖区', '420100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420102', '江岸区', '420100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420103', '江汉区', '420100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420104', '乔口区', '420100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420105', '汉阳区', '420100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420106', '武昌区', '420100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420107', '青山区', '420100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420111', '洪山区', '420100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420112', '东西湖区', '420100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420113', '汉南区', '420100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420114', '蔡甸区', '420100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420115', '江夏区', '420100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420116', '黄陂区', '420100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420117', '新洲区', '420100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420200', '黄石市', '420000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420201', '市辖区', '420200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420202', '黄石港区', '420200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420203', '西塞山区', '420200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420204', '下陆区', '420200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420205', '铁山区', '420200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420222', '阳新县', '420200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420281', '大冶市', '420200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420300', '十堰市', '420000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420301', '市辖区', '420300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420302', '茅箭区', '420300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420303', '张湾区', '420300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420321', '郧县', '420300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420322', '郧西县', '420300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420323', '竹山县', '420300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420324', '竹溪县', '420300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420325', '房县', '420300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420381', '丹江口市', '420300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420500', '宜昌市', '420000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420501', '市辖区', '420500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420502', '西陵区', '420500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420503', '伍家岗区', '420500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420504', '点军区', '420500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420505', '猇亭区', '420500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420506', '夷陵区', '420500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420525', '远安县', '420500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420526', '兴山县', '420500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420527', '秭归县', '420500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420528', '长阳土家族自治县', '420500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420529', '五峰土家族自治县', '420500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420581', '宜都市', '420500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420582', '当阳市', '420500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420583', '枝江市', '420500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420600', '襄樊市', '420000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420601', '市辖区', '420600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420602', '襄城区', '420600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420606', '樊城区', '420600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420607', '襄阳区', '420600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420624', '南漳县', '420600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420625', '谷城县', '420600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420626', '保康县', '420600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420682', '老河口市', '420600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420683', '枣阳市', '420600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420684', '宜城市', '420600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420700', '鄂州市', '420000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420701', '市辖区', '420700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420702', '梁子湖区', '420700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420703', '华容区', '420700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420704', '鄂城区', '420700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420800', '荆门市', '420000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420801', '市辖区', '420800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420802', '东宝区', '420800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420804', '掇刀区', '420800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420821', '京山县', '420800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420822', '沙洋县', '420800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420881', '钟祥市', '420800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420900', '孝感市', '420000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420901', '市辖区', '420900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420902', '孝南区', '420900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420921', '孝昌县', '420900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420922', '大悟县', '420900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420923', '云梦县', '420900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420981', '应城市', '420900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420982', '安陆市', '420900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('420984', '汉川市', '420900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421000', '荆州市', '420000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421001', '市辖区', '421000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421002', '沙市区', '421000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421003', '荆州区', '421000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421022', '公安县', '421000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421023', '监利县', '421000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421024', '江陵县', '421000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421081', '石首市', '421000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421083', '洪湖市', '421000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421087', '松滋市', '421000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421100', '黄冈市', '420000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421101', '市辖区', '421100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421102', '黄州区', '421100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421121', '团风县', '421100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421122', '红安县', '421100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421123', '罗田县', '421100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421124', '英山县', '421100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421125', '浠水县', '421100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421126', '蕲春县', '421100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421127', '黄梅县', '421100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421181', '麻城市', '421100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421182', '武穴市', '421100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421200', '咸宁市', '420000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421201', '市辖区', '421200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421202', '咸安区', '421200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421221', '嘉鱼县', '421200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421222', '通城县', '421200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421223', '崇阳县', '421200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421224', '通山县', '421200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421281', '赤壁市', '421200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421300', '随州市', '420000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421301', '市辖区', '421300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421302', '曾都区', '421300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('421381', '广水市', '421300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('422800', '恩施土家族苗族自治州', '420000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('422801', '恩施市', '422800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('422802', '利川市', '422800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('422822', '建始县', '422800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('422823', '巴东县', '422800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('422825', '宣恩县', '422800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('422826', '咸丰县', '422800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('422827', '来凤县', '422800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('422828', '鹤峰县', '422800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('429000', '省直辖行政单位', '420000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('429004', '仙桃市', '429000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('429005', '潜江市', '429000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('429006', '天门市', '429000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('429021', '神农架林区', '429000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430000', '湖南省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430100', '长沙市', '430000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430101', '市辖区', '430100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430102', '芙蓉区', '430100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430103', '天心区', '430100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430104', '岳麓区', '430100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430105', '开福区', '430100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430111', '雨花区', '430100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430121', '长沙县', '430100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430122', '望城县', '430100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430124', '宁乡县', '430100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430181', '浏阳市', '430100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430200', '株洲市', '430000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430201', '市辖区', '430200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430202', '荷塘区', '430200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430203', '芦淞区', '430200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430204', '石峰区', '430200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430211', '天元区', '430200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430221', '株洲县', '430200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430223', '攸县', '430200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430224', '茶陵县', '430200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430225', '炎陵县', '430200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430281', '醴陵市', '430200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430300', '湘潭市', '430000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430301', '市辖区', '430300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430302', '雨湖区', '430300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430304', '岳塘区', '430300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430321', '湘潭县', '430300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430381', '湘乡市', '430300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430382', '韶山市', '430300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430400', '衡阳市', '430000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430401', '市辖区', '430400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430405', '珠晖区', '430400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430406', '雁峰区', '430400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430407', '石鼓区', '430400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430408', '蒸湘区', '430400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430412', '南岳区', '430400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430421', '衡阳县', '430400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430422', '衡南县', '430400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430423', '衡山县', '430400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430424', '衡东县', '430400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430426', '祁东县', '430400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430481', '耒阳市', '430400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430482', '常宁市', '430400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430500', '邵阳市', '430000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430501', '市辖区', '430500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430502', '双清区', '430500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430503', '大祥区', '430500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430511', '北塔区', '430500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430521', '邵东县', '430500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430522', '新邵县', '430500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430523', '邵阳县', '430500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430524', '隆回县', '430500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430525', '洞口县', '430500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430527', '绥宁县', '430500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430528', '新宁县', '430500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430529', '城步苗族自治县', '430500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430581', '武冈市', '430500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430600', '岳阳市', '430000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430601', '市辖区', '430600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430602', '岳阳楼区', '430600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430603', '云溪区', '430600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430611', '君山区', '430600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430621', '岳阳县', '430600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430623', '华容县', '430600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430624', '湘阴县', '430600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430626', '平江县', '430600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430681', '汨罗市', '430600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430682', '临湘市', '430600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430700', '常德市', '430000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430701', '市辖区', '430700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430702', '武陵区', '430700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430703', '鼎城区', '430700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430721', '安乡县', '430700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430722', '汉寿县', '430700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430723', '澧县', '430700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430724', '临澧县', '430700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430725', '桃源县', '430700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430726', '石门县', '430700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430781', '津市市', '430700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430800', '张家界市', '430000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430801', '市辖区', '430800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430802', '永定区', '430800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430811', '武陵源区', '430800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430821', '慈利县', '430800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430822', '桑植县', '430800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430900', '益阳市', '430000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430901', '市辖区', '430900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430902', '资阳区', '430900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430903', '赫山区', '430900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430921', '南县', '430900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430922', '桃江县', '430900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430923', '安化县', '430900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('430981', '沅江市', '430900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431000', '郴州市', '430000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431001', '市辖区', '431000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431002', '北湖区', '431000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431003', '苏仙区', '431000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431021', '桂阳县', '431000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431022', '宜章县', '431000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431023', '永兴县', '431000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431024', '嘉禾县', '431000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431025', '临武县', '431000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431026', '汝城县', '431000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431027', '桂东县', '431000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431028', '安仁县', '431000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431081', '资兴市', '431000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431100', '永州市', '430000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431101', '市辖区', '431100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431102', '芝山区', '431100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431103', '冷水滩区', '431100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431121', '祁阳县', '431100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431122', '东安县', '431100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431123', '双牌县', '431100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431124', '道县', '431100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431125', '江永县', '431100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431126', '宁远县', '431100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431127', '蓝山县', '431100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431128', '新田县', '431100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431129', '江华瑶族自治县', '431100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431200', '怀化市', '430000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431201', '市辖区', '431200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431202', '鹤城区', '431200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431221', '中方县', '431200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431222', '沅陵县', '431200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431223', '辰溪县', '431200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431224', '溆浦县', '431200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431225', '会同县', '431200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431226', '麻阳苗族自治县', '431200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431227', '新晃侗族自治县', '431200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431228', '芷江侗族自治县', '431200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431229', '靖州苗族侗族自治县', '431200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431230', '通道侗族自治县', '431200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431281', '洪江市', '431200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431300', '娄底市', '430000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431301', '市辖区', '431300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431302', '娄星区', '431300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431321', '双峰县', '431300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431322', '新化县', '431300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431381', '冷水江市', '431300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('431382', '涟源市', '431300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('433100', '湘西土家族苗族自治州', '430000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('433101', '吉首市', '433100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('433122', '泸溪县', '433100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('433123', '凤凰县', '433100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('433124', '花垣县', '433100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('433125', '保靖县', '433100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('433126', '古丈县', '433100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('433127', '永顺县', '433100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('433130', '龙山县', '433100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440000', '广东省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440100', '广州市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440101', '市辖区', '440100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440102', '东山区', '440100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440103', '荔湾区', '440100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440104', '越秀区', '440100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440105', '海珠区', '440100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440106', '天河区', '440100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440107', '芳村区', '440100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440111', '白云区', '440100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440112', '黄埔区', '440100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440113', '番禺区', '440100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440114', '花都区', '440100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440183', '增城市', '440100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440184', '从化市', '440100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440200', '韶关市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440201', '市辖区', '440200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440203', '武江区', '440200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440204', '浈江区', '440200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440205', '曲江区', '440200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440222', '始兴县', '440200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440224', '仁化县', '440200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440229', '翁源县', '440200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440232', '乳源瑶族自治县', '440200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440233', '新丰县', '440200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440281', '乐昌市', '440200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440282', '南雄市', '440200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440300', '深圳市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440301', '市辖区', '440300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440303', '罗湖区', '440300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440304', '福田区', '440300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440305', '南山区', '440300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440306', '宝安区', '440300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440307', '龙岗区', '440300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440308', '盐田区', '440300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440400', '珠海市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440401', '市辖区', '440400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440402', '香洲区', '440400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440403', '斗门区', '440400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440404', '金湾区', '440400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440500', '汕头市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440501', '市辖区', '440500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440507', '龙湖区', '440500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440511', '金平区', '440500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440512', '濠江区', '440500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440513', '潮阳区', '440500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440514', '潮南区', '440500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440515', '澄海区', '440500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440523', '南澳县', '440500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440600', '佛山市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440601', '市辖区', '440600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440604', '禅城区', '440600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440605', '南海区', '440600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440606', '顺德区', '440600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440607', '三水区', '440600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440608', '高明区', '440600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440700', '江门市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440701', '市辖区', '440700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440703', '蓬江区', '440700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440704', '江海区', '440700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440705', '新会区', '440700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440781', '台山市', '440700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440783', '开平市', '440700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440784', '鹤山市', '440700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440785', '恩平市', '440700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440800', '湛江市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440801', '市辖区', '440800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440802', '赤坎区', '440800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440803', '霞山区', '440800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440804', '坡头区', '440800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440811', '麻章区', '440800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440823', '遂溪县', '440800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440825', '徐闻县', '440800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440881', '廉江市', '440800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440882', '雷州市', '440800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440883', '吴川市', '440800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440900', '茂名市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440901', '市辖区', '440900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440902', '茂南区', '440900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440903', '茂港区', '440900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440923', '电白县', '440900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440981', '高州市', '440900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440982', '化州市', '440900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('440983', '信宜市', '440900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441200', '肇庆市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441201', '市辖区', '441200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441202', '端州区', '441200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441203', '鼎湖区', '441200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441223', '广宁县', '441200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441224', '怀集县', '441200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441225', '封开县', '441200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441226', '德庆县', '441200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441283', '高要市', '441200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441284', '四会市', '441200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441300', '惠州市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441301', '市辖区', '441300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441302', '惠城区', '441300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441303', '惠阳区', '441300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441322', '博罗县', '441300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441323', '惠东县', '441300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441324', '龙门县', '441300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441400', '梅州市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441401', '市辖区', '441400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441402', '梅江区', '441400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441421', '梅县', '441400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441422', '大埔县', '441400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441423', '丰顺县', '441400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441424', '五华县', '441400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441426', '平远县', '441400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441427', '蕉岭县', '441400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441481', '兴宁市', '441400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441500', '汕尾市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441501', '市辖区', '441500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441502', '城区', '441500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441521', '海丰县', '441500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441523', '陆河县', '441500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441581', '陆丰市', '441500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441600', '河源市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441601', '市辖区', '441600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441602', '源城区', '441600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441621', '紫金县', '441600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441622', '龙川县', '441600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441623', '连平县', '441600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441624', '和平县', '441600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441625', '东源县', '441600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441700', '阳江市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441701', '市辖区', '441700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441702', '江城区', '441700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441721', '阳西县', '441700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441723', '阳东县', '441700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441781', '阳春市', '441700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441800', '清远市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441801', '市辖区', '441800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441802', '清城区', '441800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441821', '佛冈县', '441800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441823', '阳山县', '441800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441825', '连山壮族瑶族自治县', '441800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441826', '连南瑶族自治县', '441800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441827', '清新县', '441800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441881', '英德市', '441800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441882', '连州市', '441800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('441900', '东莞市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('442000', '中山市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445100', '潮州市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445101', '市辖区', '445100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445102', '湘桥区', '445100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445121', '潮安县', '445100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445122', '饶平县', '445100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445200', '揭阳市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445201', '市辖区', '445200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445202', '榕城区', '445200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445221', '揭东县', '445200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445222', '揭西县', '445200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445224', '惠来县', '445200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445281', '普宁市', '445200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445300', '云浮市', '440000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445301', '市辖区', '445300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445302', '云城区', '445300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445321', '新兴县', '445300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445322', '郁南县', '445300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445323', '云安县', '445300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('445381', '罗定市', '445300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450000', '广西壮族自治区', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450100', '南宁市', '450000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450101', '市辖区', '450100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450102', '兴宁区', '450100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450103', '青秀区', '450100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450105', '江南区', '450100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450107', '西乡塘区', '450100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450108', '良庆区', '450100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450109', '邕宁区', '450100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450122', '武鸣县', '450100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450123', '隆安县', '450100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450124', '马山县', '450100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450125', '上林县', '450100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450126', '宾阳县', '450100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450127', '横县', '450100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450200', '柳州市', '450000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450201', '市辖区', '450200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450202', '城中区', '450200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450203', '鱼峰区', '450200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450204', '柳南区', '450200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450205', '柳北区', '450200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450221', '柳江县', '450200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450222', '柳城县', '450200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450223', '鹿寨县', '450200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450224', '融安县', '450200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450225', '融水苗族自治县', '450200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450226', '三江侗族自治县', '450200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450300', '桂林市', '450000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450301', '市辖区', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450302', '秀峰区', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450303', '叠彩区', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450304', '象山区', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450305', '七星区', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450311', '雁山区', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450321', '阳朔县', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450322', '临桂县', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450323', '灵川县', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450324', '全州县', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450325', '兴安县', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450326', '永福县', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450327', '灌阳县', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450328', '龙胜各族自治县', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450329', '资源县', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450330', '平乐县', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450331', '荔蒲县', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450332', '恭城瑶族自治县', '450300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450400', '梧州市', '450000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450401', '市辖区', '450400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450403', '万秀区', '450400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450404', '蝶山区', '450400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450405', '长洲区', '450400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450421', '苍梧县', '450400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450422', '藤县', '450400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450423', '蒙山县', '450400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450481', '岑溪市', '450400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450500', '北海市', '450000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450501', '市辖区', '450500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450502', '海城区', '450500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450503', '银海区', '450500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450512', '铁山港区', '450500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450521', '合浦县', '450500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450600', '防城港市', '450000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450601', '市辖区', '450600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450602', '港口区', '450600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450603', '防城区', '450600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450621', '上思县', '450600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450681', '东兴市', '450600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450700', '钦州市', '450000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450701', '市辖区', '450700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450702', '钦南区', '450700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450703', '钦北区', '450700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450721', '灵山县', '450700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450722', '浦北县', '450700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450800', '贵港市', '450000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450801', '市辖区', '450800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450802', '港北区', '450800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450803', '港南区', '450800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450804', '覃塘区', '450800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450821', '平南县', '450800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450881', '桂平市', '450800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450900', '玉林市', '450000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450901', '市辖区', '450900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450902', '玉州区', '450900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450921', '容县', '450900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450922', '陆川县', '450900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450923', '博白县', '450900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450924', '兴业县', '450900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('450981', '北流市', '450900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451000', '百色市', '450000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451001', '市辖区', '451000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451002', '右江区', '451000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451021', '田阳县', '451000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451022', '田东县', '451000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451023', '平果县', '451000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451024', '德保县', '451000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451025', '靖西县', '451000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451026', '那坡县', '451000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451027', '凌云县', '451000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451028', '乐业县', '451000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451029', '田林县', '451000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451030', '西林县', '451000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451031', '隆林各族自治县', '451000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451100', '贺州市', '450000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451101', '市辖区', '451100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451102', '八步区', '451100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451121', '昭平县', '451100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451122', '钟山县', '451100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451123', '富川瑶族自治县', '451100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451200', '河池市', '450000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451201', '市辖区', '451200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451202', '金城江区', '451200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451221', '南丹县', '451200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451222', '天峨县', '451200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451223', '凤山县', '451200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451224', '东兰县', '451200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451225', '罗城仫佬族自治县', '451200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451226', '环江毛南族自治县', '451200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451227', '巴马瑶族自治县', '451200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451228', '都安瑶族自治县', '451200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451229', '大化瑶族自治县', '451200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451281', '宜州市', '451200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451300', '来宾市', '450000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451301', '市辖区', '451300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451302', '兴宾区', '451300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451321', '忻城县', '451300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451322', '象州县', '451300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451323', '武宣县', '451300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451324', '金秀瑶族自治县', '451300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451381', '合山市', '451300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451400', '崇左市', '450000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451401', '市辖区', '451400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451402', '江洲区', '451400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451421', '扶绥县', '451400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451422', '宁明县', '451400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451423', '龙州县', '451400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451424', '大新县', '451400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451425', '天等县', '451400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('451481', '凭祥市', '451400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('460000', '海南省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('460100', '海口市', '460000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('460101', '市辖区', '460100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('460105', '秀英区', '460100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('460106', '龙华区', '460100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('460107', '琼山区', '460100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('460108', '美兰区', '460100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('460200', '三亚市', '460000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('460201', '市辖区', '460200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469000', '省直辖县级行政单位', '460000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469001', '五指山市', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469002', '琼海市', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469003', '儋州市', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469005', '文昌市', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469006', '万宁市', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469007', '东方市', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469025', '定安县', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469026', '屯昌县', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469027', '澄迈县', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469028', '临高县', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469030', '白沙黎族自治县', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469031', '昌江黎族自治县', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469033', '乐东黎族自治县', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469034', '陵水黎族自治县', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469035', '保亭黎族苗族自治县', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469036', '琼中黎族苗族自治县', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469037', '西沙群岛', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469038', '南沙群岛', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('469039', '中沙群岛的岛礁及其海域', '469000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500000', '重庆市', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500101', '万州区', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500102', '涪陵区', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500103', '渝中区', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500104', '大渡口区', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500105', '江北区', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500106', '沙坪坝区', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500107', '九龙坡区', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500108', '南岸区', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500109', '北碚区', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500110', '万盛区', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500111', '双桥区', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500112', '渝北区', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500113', '巴南区', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500114', '黔江区', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500115', '长寿区', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500222', '綦江县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500223', '潼南县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500224', '铜梁县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500225', '大足县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500226', '荣昌县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500227', '璧山县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500228', '梁平县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500229', '城口县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500230', '丰都县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500231', '垫江县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500232', '武隆县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500233', '忠县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500234', '开县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500235', '云阳县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500236', '奉节县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500237', '巫山县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500238', '巫溪县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500240', '石柱土家族自治县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500241', '秀山土家族苗族自治县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500242', '酉阳土家族苗族自治县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500243', '彭水苗族土家族自治县', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500381', '江津市', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500382', '合川市', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500383', '永川市', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('500384', '南川市', '500000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510000', '四川省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510100', '成都市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510101', '市辖区', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510104', '锦江区', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510105', '青羊区', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510106', '金牛区', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510107', '武侯区', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510108', '成华区', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510112', '龙泉驿区', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510113', '青白江区', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510114', '新都区', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510115', '温江区', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510121', '金堂县', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510122', '双流县', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510124', '郫县', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510129', '大邑县', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510131', '蒲江县', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510132', '新津县', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510181', '都江堰市', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510182', '彭州市', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510183', '邛崃市', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510184', '崇州市', '510100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510300', '自贡市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510301', '市辖区', '510300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510302', '自流井区', '510300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510303', '贡井区', '510300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510304', '大安区', '510300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510311', '沿滩区', '510300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510321', '荣县', '510300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510322', '富顺县', '510300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510400', '攀枝花市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510401', '市辖区', '510400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510402', '东区', '510400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510403', '西区', '510400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510411', '仁和区', '510400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510421', '米易县', '510400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510422', '盐边县', '510400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510500', '泸州市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510501', '市辖区', '510500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510502', '江阳区', '510500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510503', '纳溪区', '510500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510504', '龙马潭区', '510500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510521', '泸县', '510500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510522', '合江县', '510500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510524', '叙永县', '510500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510525', '古蔺县', '510500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510600', '德阳市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510601', '市辖区', '510600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510603', '旌阳区', '510600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510623', '中江县', '510600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510626', '罗江县', '510600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510681', '广汉市', '510600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510682', '什邡市', '510600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510683', '绵竹市', '510600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510700', '绵阳市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510701', '市辖区', '510700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510703', '涪城区', '510700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510704', '游仙区', '510700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510722', '三台县', '510700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510723', '盐亭县', '510700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510724', '安县', '510700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510725', '梓潼县', '510700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510726', '北川羌族自治县', '510700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510727', '平武县', '510700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510781', '江油市', '510700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510800', '广元市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510801', '市辖区', '510800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510802', '市中区', '510800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510811', '元坝区', '510800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510812', '朝天区', '510800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510821', '旺苍县', '510800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510822', '青川县', '510800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510823', '剑阁县', '510800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510824', '苍溪县', '510800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510900', '遂宁市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510901', '市辖区', '510900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510903', '船山区', '510900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510904', '安居区', '510900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510921', '蓬溪县', '510900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510922', '射洪县', '510900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('510923', '大英县', '510900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511000', '内江市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511001', '市辖区', '511000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511002', '市中区', '511000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511011', '东兴区', '511000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511024', '威远县', '511000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511025', '资中县', '511000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511028', '隆昌县', '511000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511100', '乐山市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511101', '市辖区', '511100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511102', '市中区', '511100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511111', '沙湾区', '511100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511112', '五通桥区', '511100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511113', '金口河区', '511100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511123', '犍为县', '511100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511124', '井研县', '511100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511126', '夹江县', '511100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511129', '沐川县', '511100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511132', '峨边彝族自治县', '511100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511133', '马边彝族自治县', '511100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511181', '峨眉山市', '511100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511300', '南充市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511301', '市辖区', '511300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511302', '顺庆区', '511300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511303', '高坪区', '511300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511304', '嘉陵区', '511300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511321', '南部县', '511300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511322', '营山县', '511300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511323', '蓬安县', '511300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511324', '仪陇县', '511300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511325', '西充县', '511300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511381', '阆中市', '511300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511400', '眉山市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511401', '市辖区', '511400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511402', '东坡区', '511400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511421', '仁寿县', '511400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511422', '彭山县', '511400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511423', '洪雅县', '511400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511424', '丹棱县', '511400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511425', '青神县', '511400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511500', '宜宾市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511501', '市辖区', '511500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511502', '翠屏区', '511500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511521', '宜宾县', '511500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511522', '南溪县', '511500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511523', '江安县', '511500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511524', '长宁县', '511500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511525', '高县', '511500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511526', '珙县', '511500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511527', '筠连县', '511500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511528', '兴文县', '511500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511529', '屏山县', '511500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511600', '广安市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511601', '市辖区', '511600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511602', '广安区', '511600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511621', '岳池县', '511600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511622', '武胜县', '511600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511623', '邻水县', '511600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511681', '华莹市', '511600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511700', '达州市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511701', '市辖区', '511700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511702', '通川区', '511700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511721', '达县', '511700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511722', '宣汉县', '511700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511723', '开江县', '511700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511724', '大竹县', '511700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511725', '渠县', '511700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511781', '万源市', '511700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511800', '雅安市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511801', '市辖区', '511800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511802', '雨城区', '511800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511821', '名山县', '511800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511822', '荥经县', '511800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511823', '汉源县', '511800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511824', '石棉县', '511800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511825', '天全县', '511800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511826', '芦山县', '511800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511827', '宝兴县', '511800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511900', '巴中市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511901', '市辖区', '511900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511902', '巴州区', '511900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511921', '通江县', '511900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511922', '南江县', '511900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('511923', '平昌县', '511900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('512000', '资阳市', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('512001', '市辖区', '512000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('512002', '雁江区', '512000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('512021', '安岳县', '512000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('512022', '乐至县', '512000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('512081', '简阳市', '512000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513200', '阿坝藏族羌族自治州', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513221', '汶川县', '513200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513222', '理县', '513200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513223', '茂县', '513200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513224', '松潘县', '513200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513225', '九寨沟县', '513200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513226', '金川县', '513200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513227', '小金县', '513200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513228', '黑水县', '513200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513229', '马尔康县', '513200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513230', '壤塘县', '513200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513231', '阿坝县', '513200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513232', '若尔盖县', '513200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513233', '红原县', '513200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513300', '甘孜藏族自治州', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513321', '康定县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513322', '泸定县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513323', '丹巴县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513324', '九龙县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513325', '雅江县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513326', '道孚县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513327', '炉霍县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513328', '甘孜县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513329', '新龙县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513330', '德格县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513331', '白玉县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513332', '石渠县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513333', '色达县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513334', '理塘县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513335', '巴塘县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513336', '乡城县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513337', '稻城县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513338', '得荣县', '513300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513400', '凉山彝族自治州', '510000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513401', '西昌市', '513400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513422', '木里藏族自治县', '513400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513423', '盐源县', '513400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513424', '德昌县', '513400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513425', '会理县', '513400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513426', '会东县', '513400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513427', '宁南县', '513400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513428', '普格县', '513400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513429', '布拖县', '513400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513430', '金阳县', '513400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513431', '昭觉县', '513400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513432', '喜德县', '513400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513433', '冕宁县', '513400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513434', '越西县', '513400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513435', '甘洛县', '513400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513436', '美姑县', '513400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('513437', '雷波县', '513400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520000', '贵州省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520100', '贵阳市', '520000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520101', '市辖区', '520100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520102', '南明区', '520100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520103', '云岩区', '520100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520111', '花溪区', '520100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520112', '乌当区', '520100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520113', '白云区', '520100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520114', '小河区', '520100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520121', '开阳县', '520100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520122', '息烽县', '520100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520123', '修文县', '520100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520181', '清镇市', '520100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520200', '六盘水市', '520000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520201', '钟山区', '520200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520203', '六枝特区', '520200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520221', '水城县', '520200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520222', '盘县', '520200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520300', '遵义市', '520000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520301', '市辖区', '520300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520302', '红花岗区', '520300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520303', '汇川区', '520300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520321', '遵义县', '520300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520322', '桐梓县', '520300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520323', '绥阳县', '520300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520324', '正安县', '520300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520325', '道真仡佬族苗族自治县', '520300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520326', '务川仡佬族苗族自治县', '520300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520327', '凤冈县', '520300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520328', '湄潭县', '520300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520329', '余庆县', '520300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520330', '习水县', '520300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520381', '赤水市', '520300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520382', '仁怀市', '520300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520400', '安顺市', '520000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520401', '市辖区', '520400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520402', '西秀区', '520400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520421', '平坝县', '520400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520422', '普定县', '520400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520423', '镇宁布依族苗族自治县', '520400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520424', '关岭布依族苗族自治县', '520400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('520425', '紫云苗族布依族自治县', '520400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522200', '铜仁地区', '520000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522201', '铜仁市', '522200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522222', '江口县', '522200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522223', '玉屏侗族自治县', '522200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522224', '石阡县', '522200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522225', '思南县', '522200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522226', '印江土家族苗族自治县', '522200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522227', '德江县', '522200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522228', '沿河土家族自治县', '522200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522229', '松桃苗族自治县', '522200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522230', '万山特区', '522200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522300', '黔西南布依族苗族自治州', '520000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522301', '兴义市', '522300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522322', '兴仁县', '522300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522323', '普安县', '522300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522324', '晴隆县', '522300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522325', '贞丰县', '522300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522326', '望谟县', '522300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522327', '册亨县', '522300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522328', '安龙县', '522300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522400', '毕节地区', '520000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522401', '毕节市', '522400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522422', '大方县', '522400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522423', '黔西县', '522400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522424', '金沙县', '522400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522425', '织金县', '522400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522426', '纳雍县', '522400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522427', '威宁彝族回族苗族自治县', '522400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522428', '赫章县', '522400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522600', '黔东南苗族侗族自治州', '520000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522601', '凯里市', '522600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522622', '黄平县', '522600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522623', '施秉县', '522600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522624', '三穗县', '522600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522625', '镇远县', '522600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522626', '岑巩县', '522600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522627', '天柱县', '522600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522628', '锦屏县', '522600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522629', '剑河县', '522600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522630', '台江县', '522600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522631', '黎平县', '522600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522632', '榕江县', '522600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522633', '从江县', '522600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522634', '雷山县', '522600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522635', '麻江县', '522600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522636', '丹寨县', '522600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522700', '黔南布依族苗族自治州', '520000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522701', '都匀市', '522700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522702', '福泉市', '522700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522722', '荔波县', '522700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522723', '贵定县', '522700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522725', '瓮安县', '522700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522726', '独山县', '522700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522727', '平塘县', '522700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522728', '罗甸县', '522700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522729', '长顺县', '522700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522730', '龙里县', '522700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522731', '惠水县', '522700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('522732', '三都水族自治县', '522700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530000', '云南省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530100', '昆明市', '530000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530101', '市辖区', '530100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530102', '五华区', '530100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530103', '盘龙区', '530100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530111', '官渡区', '530100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530112', '西山区', '530100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530113', '东川区', '530100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530121', '呈贡县', '530100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530122', '晋宁县', '530100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530124', '富民县', '530100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530125', '宜良县', '530100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530126', '石林彝族自治县', '530100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530127', '嵩明县', '530100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530128', '禄劝彝族苗族自治县', '530100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530129', '寻甸回族彝族自治县', '530100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530181', '安宁市', '530100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530300', '曲靖市', '530000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530301', '市辖区', '530300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530302', '麒麟区', '530300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530321', '马龙县', '530300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530322', '陆良县', '530300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530323', '师宗县', '530300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530324', '罗平县', '530300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530325', '富源县', '530300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530326', '会泽县', '530300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530328', '沾益县', '530300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530381', '宣威市', '530300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530400', '玉溪市', '530000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530401', '市辖区', '530400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530402', '红塔区', '530400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530421', '江川县', '530400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530422', '澄江县', '530400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530423', '通海县', '530400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530424', '华宁县', '530400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530425', '易门县', '530400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530426', '峨山彝族自治县', '530400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530427', '新平彝族傣族自治县', '530400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530428', '元江哈尼族彝族傣族自治县', '530400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530500', '保山市', '530000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530501', '市辖区', '530500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530502', '隆阳区', '530500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530521', '施甸县', '530500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530522', '腾冲县', '530500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530523', '龙陵县', '530500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530524', '昌宁县', '530500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530600', '昭通市', '530000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530601', '市辖区', '530600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530602', '昭阳区', '530600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530621', '鲁甸县', '530600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530622', '巧家县', '530600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530623', '盐津县', '530600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530624', '大关县', '530600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530625', '永善县', '530600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530626', '绥江县', '530600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530627', '镇雄县', '530600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530628', '彝良县', '530600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530629', '威信县', '530600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530630', '水富县', '530600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530700', '丽江市', '530000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530701', '市辖区', '530700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530702', '古城区', '530700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530721', '玉龙纳西族自治县', '530700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530722', '永胜县', '530700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530723', '华坪县', '530700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530724', '宁蒗彝族自治县', '530700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530800', '思茅市', '530000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530801', '市辖区', '530800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530802', '翠云区', '530800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530821', '普洱哈尼族彝族自治县', '530800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530822', '墨江哈尼族自治县', '530800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530823', '景东彝族自治县', '530800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530824', '景谷傣族彝族自治县', '530800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530825', '镇沅彝族哈尼族拉祜族自治县', '530800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530826', '江城哈尼族彝族自治县', '530800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530827', '孟连傣族拉祜族佤族自治县', '530800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530828', '澜沧拉祜族自治县', '530800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530829', '西盟佤族自治县', '530800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530900', '临沧市', '530000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530901', '市辖区', '530900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530902', '临翔区', '530900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530921', '凤庆县', '530900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530922', '云县', '530900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530923', '永德县', '530900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530924', '镇康县', '530900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530925', '双江拉祜族佤族布朗族傣族自治县', '530900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530926', '耿马傣族佤族自治县', '530900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('530927', '沧源佤族自治县', '530900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532300', '楚雄彝族自治州', '530000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532301', '楚雄市', '532300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532322', '双柏县', '532300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532323', '牟定县', '532300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532324', '南华县', '532300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532325', '姚安县', '532300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532326', '大姚县', '532300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532327', '永仁县', '532300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532328', '元谋县', '532300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532329', '武定县', '532300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532331', '禄丰县', '532300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532500', '红河哈尼族彝族自治州', '530000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532501', '个旧市', '532500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532502', '开远市', '532500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532522', '蒙自县', '532500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532523', '屏边苗族自治县', '532500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532524', '建水县', '532500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532525', '石屏县', '532500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532526', '弥勒县', '532500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532527', '泸西县', '532500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532528', '元阳县', '532500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532529', '红河县', '532500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532530', '金平苗族瑶族傣族自治县', '532500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532531', '绿春县', '532500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532532', '河口瑶族自治县', '532500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532600', '文山壮族苗族自治州', '530000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532621', '文山县', '532600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532622', '砚山县', '532600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532623', '西畴县', '532600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532624', '麻栗坡县', '532600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532625', '马关县', '532600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532626', '丘北县', '532600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532627', '广南县', '532600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532628', '富宁县', '532600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532800', '西双版纳傣族自治州', '530000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532801', '景洪市', '532800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532822', '勐海县', '532800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532823', '勐腊县', '532800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532900', '大理白族自治州', '530000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532901', '大理市', '532900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532922', '漾濞彝族自治县', '532900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532923', '祥云县', '532900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532924', '宾川县', '532900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532925', '弥渡县', '532900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532926', '南涧彝族自治县', '532900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532927', '巍山彝族回族自治县', '532900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532928', '永平县', '532900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532929', '云龙县', '532900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532930', '洱源县', '532900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532931', '剑川县', '532900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('532932', '鹤庆县', '532900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('533100', '德宏傣族景颇族自治州', '530000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('533102', '瑞丽市', '533100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('533103', '潞西市', '533100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('533122', '梁河县', '533100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('533123', '盈江县', '533100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('533124', '陇川县', '533100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('533300', '怒江傈僳族自治州', '530000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('533321', '泸水县', '533300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('533323', '福贡县', '533300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('533324', '贡山独龙族怒族自治县', '533300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('533325', '兰坪白族普米族自治县', '533300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('533400', '迪庆藏族自治州', '530000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('533421', '香格里拉县', '533400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('533422', '德钦县', '533400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('533423', '维西傈僳族自治县', '533400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('540000', '西藏自治区', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('540100', '拉萨市', '540000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('540101', '市辖区', '540100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('540102', '城关区', '540100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('540121', '林周县', '540100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('540122', '当雄县', '540100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('540123', '尼木县', '540100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('540124', '曲水县', '540100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('540125', '堆龙德庆县', '540100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('540126', '达孜县', '540100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('540127', '墨竹工卡县', '540100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542100', '昌都地区', '540000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542121', '昌都县', '542100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542122', '江达县', '542100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542123', '贡觉县', '542100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542124', '类乌齐县', '542100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542125', '丁青县', '542100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542126', '察雅县', '542100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542127', '八宿县', '542100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542128', '左贡县', '542100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542129', '芒康县', '542100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542132', '洛隆县', '542100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542133', '边坝县', '542100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542200', '山南地区', '540000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542221', '乃东县', '542200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542222', '扎囊县', '542200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542223', '贡嘎县', '542200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542224', '桑日县', '542200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542225', '琼结县', '542200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542226', '曲松县', '542200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542227', '措美县', '542200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542228', '洛扎县', '542200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542229', '加查县', '542200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542231', '隆子县', '542200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542232', '错那县', '542200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542233', '浪卡子县', '542200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542300', '日喀则地区', '540000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542301', '日喀则市', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542322', '南木林县', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542323', '江孜县', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542324', '定日县', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542325', '萨迦县', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542326', '拉孜县', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542327', '昂仁县', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542328', '谢通门县', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542329', '白朗县', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542330', '仁布县', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542331', '康马县', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542332', '定结县', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542333', '仲巴县', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542334', '亚东县', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542335', '吉隆县', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542336', '聂拉木县', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542337', '萨嘎县', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542338', '岗巴县', '542300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542400', '那曲地区', '540000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542421', '那曲县', '542400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542422', '嘉黎县', '542400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542423', '比如县', '542400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542424', '聂荣县', '542400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542425', '安多县', '542400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542426', '申扎县', '542400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542427', '索县', '542400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542428', '班戈县', '542400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542429', '巴青县', '542400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542430', '尼玛县', '542400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542500', '阿里地区', '540000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542521', '普兰县', '542500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542522', '札达县', '542500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542523', '噶尔县', '542500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542524', '日土县', '542500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542525', '革吉县', '542500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542526', '改则县', '542500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542527', '措勤县', '542500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542600', '林芝地区', '540000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542621', '林芝县', '542600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542622', '工布江达县', '542600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542623', '米林县', '542600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542624', '墨脱县', '542600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542625', '波密县', '542600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542626', '察隅县', '542600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('542627', '朗县', '542600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610000', '陕西省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610100', '西安市', '610000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610101', '市辖区', '610100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610102', '新城区', '610100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610103', '碑林区', '610100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610104', '莲湖区', '610100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610111', '灞桥区', '610100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610112', '未央区', '610100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610113', '雁塔区', '610100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610114', '阎良区', '610100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610115', '临潼区', '610100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610116', '长安区', '610100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610122', '蓝田县', '610100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610124', '周至县', '610100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610125', '户县', '610100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610126', '高陵县', '610100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610200', '铜川市', '610000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610201', '市辖区', '610200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610202', '王益区', '610200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610203', '印台区', '610200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610204', '耀州区', '610200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610222', '宜君县', '610200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610300', '宝鸡市', '610000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610301', '市辖区', '610300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610302', '渭滨区', '610300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610303', '金台区', '610300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610304', '陈仓区', '610300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610322', '凤翔县', '610300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610323', '岐山县', '610300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610324', '扶风县', '610300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610326', '眉县', '610300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610327', '陇县', '610300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610328', '千阳县', '610300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610329', '麟游县', '610300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610330', '凤县', '610300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610331', '太白县', '610300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610400', '咸阳市', '610000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610401', '市辖区', '610400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610402', '秦都区', '610400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610403', '杨凌区', '610400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610404', '渭城区', '610400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610422', '三原县', '610400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610423', '泾阳县', '610400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610424', '乾县', '610400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610425', '礼泉县', '610400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610426', '永寿县', '610400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610427', '彬县', '610400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610428', '长武县', '610400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610429', '旬邑县', '610400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610430', '淳化县', '610400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610431', '武功县', '610400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610481', '兴平市', '610400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610500', '渭南市', '610000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610501', '市辖区', '610500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610502', '临渭区', '610500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610521', '华县', '610500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610522', '潼关县', '610500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610523', '大荔县', '610500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610524', '合阳县', '610500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610525', '澄城县', '610500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610526', '蒲城县', '610500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610527', '白水县', '610500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610528', '富平县', '610500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610581', '韩城市', '610500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610582', '华阴市', '610500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610600', '延安市', '610000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610601', '市辖区', '610600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610602', '宝塔区', '610600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610621', '延长县', '610600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610622', '延川县', '610600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610623', '子长县', '610600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610624', '安塞县', '610600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610625', '志丹县', '610600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610626', '吴旗县', '610600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610627', '甘泉县', '610600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610628', '富县', '610600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610629', '洛川县', '610600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610630', '宜川县', '610600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610631', '黄龙县', '610600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610632', '黄陵县', '610600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610700', '汉中市', '610000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610701', '市辖区', '610700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610702', '汉台区', '610700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610721', '南郑县', '610700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610722', '城固县', '610700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610723', '洋县', '610700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610724', '西乡县', '610700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610725', '勉县', '610700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610726', '宁强县', '610700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610727', '略阳县', '610700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610728', '镇巴县', '610700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610729', '留坝县', '610700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610730', '佛坪县', '610700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610800', '榆林市', '610000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610801', '市辖区', '610800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610802', '榆阳区', '610800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610821', '神木县', '610800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610822', '府谷县', '610800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610823', '横山县', '610800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610824', '靖边县', '610800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610825', '定边县', '610800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610826', '绥德县', '610800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610827', '米脂县', '610800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610828', '佳县', '610800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610829', '吴堡县', '610800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610830', '清涧县', '610800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610831', '子洲县', '610800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610900', '安康市', '610000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610901', '市辖区', '610900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610902', '汉滨区', '610900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610921', '汉阴县', '610900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610922', '石泉县', '610900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610923', '宁陕县', '610900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610924', '紫阳县', '610900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610925', '岚皋县', '610900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610926', '平利县', '610900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610927', '镇坪县', '610900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610928', '旬阳县', '610900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('610929', '白河县', '610900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('611000', '商洛市', '610000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('611001', '市辖区', '611000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('611002', '商州区', '611000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('611021', '洛南县', '611000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('611022', '丹凤县', '611000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('611023', '商南县', '611000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('611024', '山阳县', '611000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('611025', '镇安县', '611000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('611026', '柞水县', '611000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620000', '甘肃省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620100', '兰州市', '620000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620101', '市辖区', '620100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620102', '城关区', '620100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620103', '七里河区', '620100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620104', '西固区', '620100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620105', '安宁区', '620100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620111', '红古区', '620100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620121', '永登县', '620100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620122', '皋兰县', '620100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620123', '榆中县', '620100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620200', '嘉峪关市', '620000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620201', '市辖区', '620200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620300', '金昌市', '620000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620301', '市辖区', '620300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620302', '金川区', '620300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620321', '永昌县', '620300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620400', '白银市', '620000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620401', '市辖区', '620400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620402', '白银区', '620400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620403', '平川区', '620400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620421', '靖远县', '620400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620422', '会宁县', '620400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620423', '景泰县', '620400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620500', '天水市', '620000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620501', '市辖区', '620500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620502', '秦城区', '620500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620503', '北道区', '620500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620521', '清水县', '620500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620522', '秦安县', '620500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620523', '甘谷县', '620500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620524', '武山县', '620500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620525', '张家川回族自治县', '620500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620600', '武威市', '620000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620601', '市辖区', '620600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620602', '凉州区', '620600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620621', '民勤县', '620600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620622', '古浪县', '620600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620623', '天祝藏族自治县', '620600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620700', '张掖市', '620000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620701', '市辖区', '620700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620702', '甘州区', '620700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620721', '肃南裕固族自治县', '620700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620722', '民乐县', '620700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620723', '临泽县', '620700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620724', '高台县', '620700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620725', '山丹县', '620700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620800', '平凉市', '620000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620801', '市辖区', '620800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620802', '崆峒区', '620800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620821', '泾川县', '620800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620822', '灵台县', '620800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620823', '崇信县', '620800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620824', '华亭县', '620800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620825', '庄浪县', '620800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620826', '静宁县', '620800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620900', '酒泉市', '620000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620901', '市辖区', '620900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620902', '肃州区', '620900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620921', '金塔县', '620900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620922', '安西县', '620900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620923', '肃北蒙古族自治县', '620900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620924', '阿克塞哈萨克族自治县', '620900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620981', '玉门市', '620900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('620982', '敦煌市', '620900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621000', '庆阳市', '620000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621001', '市辖区', '621000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621002', '西峰区', '621000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621021', '庆城县', '621000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621022', '环县', '621000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621023', '华池县', '621000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621024', '合水县', '621000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621025', '正宁县', '621000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621026', '宁县', '621000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621027', '镇原县', '621000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621100', '定西市', '620000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621101', '市辖区', '621100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621102', '安定区', '621100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621121', '通渭县', '621100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621122', '陇西县', '621100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621123', '渭源县', '621100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621124', '临洮县', '621100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621125', '漳县', '621100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621126', '岷县', '621100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621200', '陇南市', '620000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621201', '市辖区', '621200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621202', '武都区', '621200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621221', '成县', '621200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621222', '文县', '621200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621223', '宕昌县', '621200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621224', '康县', '621200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621225', '西和县', '621200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621226', '礼县', '621200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621227', '徽县', '621200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('621228', '两当县', '621200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('622900', '临夏回族自治州', '620000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('622901', '临夏市', '622900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('622921', '临夏县', '622900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('622922', '康乐县', '622900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('622923', '永靖县', '622900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('622924', '广河县', '622900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('622925', '和政县', '622900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('622926', '东乡族自治县', '622900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('622927', '积石山保安族东乡族撒拉族自治县', '622900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('623000', '甘南藏族自治州', '620000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('623001', '合作市', '623000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('623021', '临潭县', '623000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('623022', '卓尼县', '623000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('623023', '舟曲县', '623000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('623024', '迭部县', '623000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('623025', '玛曲县', '623000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('623026', '碌曲县', '623000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('623027', '夏河县', '623000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('630000', '青海省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('630100', '西宁市', '630000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('630101', '市辖区', '630100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('630102', '城东区', '630100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('630103', '城中区', '630100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('630104', '城西区', '630100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('630105', '城北区', '630100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('630121', '大通回族土族自治县', '630100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('630122', '湟中县', '630100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('630123', '湟源县', '630100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632100', '海东地区', '630000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632121', '平安县', '632100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632122', '民和回族土族自治县', '632100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632123', '乐都县', '632100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632126', '互助土族自治县', '632100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632127', '化隆回族自治县', '632100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632128', '循化撒拉族自治县', '632100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632200', '海北藏族自治州', '630000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632221', '门源回族自治县', '632200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632222', '祁连县', '632200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632223', '海晏县', '632200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632224', '刚察县', '632200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632300', '黄南藏族自治州', '630000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632321', '同仁县', '632300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632322', '尖扎县', '632300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632323', '泽库县', '632300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632324', '河南蒙古族自治县', '632300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632500', '海南藏族自治州', '630000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632521', '共和县', '632500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632522', '同德县', '632500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632523', '贵德县', '632500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632524', '兴海县', '632500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632525', '贵南县', '632500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632600', '果洛藏族自治州', '630000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632621', '玛沁县', '632600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632622', '班玛县', '632600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632623', '甘德县', '632600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632624', '达日县', '632600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632625', '久治县', '632600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632626', '玛多县', '632600', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632700', '玉树藏族自治州', '630000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632721', '玉树县', '632700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632722', '杂多县', '632700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632723', '称多县', '632700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632724', '治多县', '632700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632725', '囊谦县', '632700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632726', '曲麻莱县', '632700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632800', '海西蒙古族藏族自治州', '630000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632801', '格尔木市', '632800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632802', '德令哈市', '632800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632821', '乌兰县', '632800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632822', '都兰县', '632800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('632823', '天峻县', '632800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640000', '宁夏回族自治区', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640100', '银川市', '640000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640101', '市辖区', '640100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640104', '兴庆区', '640100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640105', '西夏区', '640100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640106', '金凤区', '640100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640121', '永宁县', '640100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640122', '贺兰县', '640100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640181', '灵武市', '640100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640200', '石嘴山市', '640000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640201', '市辖区', '640200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640202', '大武口区', '640200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640205', '惠农区', '640200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640221', '平罗县', '640200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640300', '吴忠市', '640000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640301', '市辖区', '640300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640302', '利通区', '640300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640323', '盐池县', '640300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640324', '同心县', '640300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640381', '青铜峡市', '640300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640400', '固原市', '640000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640401', '市辖区', '640400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640402', '原州区', '640400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640422', '西吉县', '640400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640423', '隆德县', '640400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640424', '泾源县', '640400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640425', '彭阳县', '640400', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640500', '中卫市', '640000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640501', '市辖区', '640500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640502', '沙坡头区', '640500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640521', '中宁县', '640500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('640522', '海原县', '640500', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('650000', '新疆维吾尔自治区', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('650100', '乌鲁木齐市', '650000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('650101', '市辖区', '650100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('650102', '天山区', '650100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('650103', '沙依巴克区', '650100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('650104', '新市区', '650100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('650105', '水磨沟区', '650100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('650106', '头屯河区', '650100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('650107', '达坂城区', '650100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('650108', '东山区', '650100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('650121', '乌鲁木齐县', '650100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('650200', '克拉玛依市', '650000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('650201', '市辖区', '650200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('650202', '独山子区', '650200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('650203', '克拉玛依区', '650200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('650204', '白碱滩区', '650200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('650205', '乌尔禾区', '650200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652100', '吐鲁番地区', '650000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652101', '吐鲁番市', '652100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652122', '鄯善县', '652100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652123', '托克逊县', '652100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652200', '哈密地区', '650000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652201', '哈密市', '652200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652222', '巴里坤哈萨克自治县', '652200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652223', '伊吾县', '652200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652300', '昌吉回族自治州', '650000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652301', '昌吉市', '652300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652302', '阜康市', '652300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652303', '米泉市', '652300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652323', '呼图壁县', '652300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652324', '玛纳斯县', '652300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652325', '奇台县', '652300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652327', '吉木萨尔县', '652300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652328', '木垒哈萨克自治县', '652300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652700', '博尔塔拉蒙古自治州', '650000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652701', '博乐市', '652700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652722', '精河县', '652700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652723', '温泉县', '652700', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652800', '巴音郭楞蒙古自治州', '650000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652801', '库尔勒市', '652800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652822', '轮台县', '652800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652823', '尉犁县', '652800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652824', '若羌县', '652800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652825', '且末县', '652800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652826', '焉耆回族自治县', '652800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652827', '和静县', '652800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652828', '和硕县', '652800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652829', '博湖县', '652800', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652900', '阿克苏地区', '650000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652901', '阿克苏市', '652900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652922', '温宿县', '652900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652923', '库车县', '652900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652924', '沙雅县', '652900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652925', '新和县', '652900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652926', '拜城县', '652900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652927', '乌什县', '652900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652928', '阿瓦提县', '652900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('652929', '柯坪县', '652900', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653000', '克孜勒苏柯尔克孜自治州', '650000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653001', '阿图什市', '653000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653022', '阿克陶县', '653000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653023', '阿合奇县', '653000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653024', '乌恰县', '653000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653100', '喀什地区', '650000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653101', '喀什市', '653100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653121', '疏附县', '653100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653122', '疏勒县', '653100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653123', '英吉沙县', '653100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653124', '泽普县', '653100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653125', '莎车县', '653100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653126', '叶城县', '653100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653127', '麦盖提县', '653100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653128', '岳普湖县', '653100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653129', '伽师县', '653100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653130', '巴楚县', '653100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653131', '塔什库尔干塔吉克自治县', '653100', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653200', '和田地区', '650000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653201', '和田市', '653200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653221', '和田县', '653200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653222', '墨玉县', '653200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653223', '皮山县', '653200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653224', '洛浦县', '653200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653225', '策勒县', '653200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653226', '于田县', '653200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('653227', '民丰县', '653200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654000', '伊犁哈萨克自治州', '650000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654002', '伊宁市', '654000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654003', '奎屯市', '654000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654021', '伊宁县', '654000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654022', '察布查尔锡伯自治县', '654000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654023', '霍城县', '654000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654024', '巩留县', '654000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654025', '新源县', '654000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654026', '昭苏县', '654000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654027', '特克斯县', '654000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654028', '尼勒克县', '654000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654200', '塔城地区', '650000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654201', '塔城市', '654200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654202', '乌苏市', '654200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654221', '额敏县', '654200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654223', '沙湾县', '654200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654224', '托里县', '654200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654225', '裕民县', '654200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654226', '和布克赛尔蒙古自治县', '654200', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654300', '阿勒泰地区', '650000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654301', '阿勒泰市', '654300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654321', '布尔津县', '654300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654322', '富蕴县', '654300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654323', '福海县', '654300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654324', '哈巴河县', '654300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654325', '青河县', '654300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('654326', '吉木乃县', '654300', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('659000', '省直辖行政单位', '650000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('659001', '石河子市', '659000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('659002', '阿拉尔市', '659000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('659003', '图木舒克市', '659000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('659004', '五家渠市', '659000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('710000', '台湾省', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('810000', '香港特别行政区', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('820000', '澳门特别行政区', '100000', '0', '');
INSERT INTO `xiaozu_areacode` VALUES ('340705', '铜官区', '340700', '0', '');

-- ----------------------------
-- Table structure for xiaozu_areashop
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_areashop`;
CREATE TABLE `xiaozu_areashop` (
  `areaid` int(20) DEFAULT NULL,
  `shopid` int(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_areashop
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_ask
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_ask`;
CREATE TABLE `xiaozu_ask` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `uid` int(20) NOT NULL,
  `shopid` int(20) NOT NULL DEFAULT '0' COMMENT '当为网站留言时此值为0',
  `content` varchar(250) NOT NULL,
  `addtime` int(11) NOT NULL,
  `typeid` int(2) NOT NULL,
  `replycontent` varchar(250) DEFAULT NULL,
  `replytime` int(11) NOT NULL DEFAULT '0',
  `replyname` varchar(255) DEFAULT NULL COMMENT '回复者',
  `is_show` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=60 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_ask
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_card
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_card`;
CREATE TABLE `xiaozu_card` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `card` varchar(50) NOT NULL,
  `card_password` varchar(255) NOT NULL,
  `uid` int(20) NOT NULL DEFAULT '0',
  `username` varchar(100) DEFAULT NULL,
  `cost` int(4) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '0',
  `creattime` int(12) NOT NULL DEFAULT '0',
  `usetime` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14441 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_card
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_codemobile
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_codemobile`;
CREATE TABLE `xiaozu_codemobile` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `phone` varchar(50) NOT NULL,
  `addtime` int(12) NOT NULL,
  `code` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_codemobile
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_collect
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_collect`;
CREATE TABLE `xiaozu_collect` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `uid` int(20) NOT NULL,
  `collectid` int(20) NOT NULL COMMENT '对应商品/店铺ID',
  `collecttype` int(1) NOT NULL COMMENT '0店铺  1商品',
  `shopuid` int(20) NOT NULL COMMENT '店铺所有者ID',
  `orderid` int(11) NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2227 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_collect
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_comment
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_comment`;
CREATE TABLE `xiaozu_comment` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `orderid` int(20) NOT NULL,
  `orderdetid` int(20) NOT NULL,
  `shopid` int(20) NOT NULL,
  `goodsid` int(20) NOT NULL,
  `uid` int(20) NOT NULL DEFAULT '0',
  `content` varchar(250) DEFAULT NULL,
  `addtime` int(12) NOT NULL DEFAULT '0',
  `replycontent` varchar(250) DEFAULT NULL,
  `replytime` int(11) NOT NULL DEFAULT '0',
  `point` int(1) NOT NULL COMMENT '评分',
  `is_show` int(1) NOT NULL DEFAULT '0' COMMENT '0展示，1不展示',
  `virtualname` varchar(50) DEFAULT NULL COMMENT ' 新增 虚拟评论人名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2014 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_comment
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_cxruleset
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_cxruleset`;
CREATE TABLE `xiaozu_cxruleset` (
  `id` int(2) NOT NULL,
  `name` text COMMENT '活动类型',
  `imgurl` varchar(255) DEFAULT NULL COMMENT '活动图标地址',
  `supportorder` int(2) DEFAULT '1' COMMENT '支持订单类型  1支持全部订单 2只支持在线支付订单',
  `supportplat` varchar(50) DEFAULT '1,2,3,4' COMMENT '支持平台 1pc 2微信 3触屏 4app',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_cxruleset
-- ----------------------------
INSERT INTO `xiaozu_cxruleset` VALUES ('1', '满赠活动', 'http://image.ghwmr.com/images/410100/cx/20180525163736371.png', '2', '1,2,3,4');
INSERT INTO `xiaozu_cxruleset` VALUES ('2', '满减活动', '/images/410100/cx/20180623183540938.png', '2', '1,2,3,4');
INSERT INTO `xiaozu_cxruleset` VALUES ('3', '折扣活动', '/images/410100/cx/20180623183548611.png', '2', '1,2,3,4');
INSERT INTO `xiaozu_cxruleset` VALUES ('4', '免配送费', '/images/410100/cx/20180623183557249.png', '1', '1,2,3,4');
INSERT INTO `xiaozu_cxruleset` VALUES ('5', '首单立减', '/images/410100/cx/20180623183605525.png', '2', '1,2,3,4');

-- ----------------------------
-- Table structure for xiaozu_distributiontxlog
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_distributiontxlog`;
CREATE TABLE `xiaozu_distributiontxlog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT NULL COMMENT '提现会员uid',
  `cost` decimal(10,2) DEFAULT NULL COMMENT '提现金额',
  `fee` decimal(10,2) DEFAULT NULL COMMENT '手续费',
  `feelv` decimal(10,2) DEFAULT NULL COMMENT '手续费率',
  `reallycost` decimal(10,2) DEFAULT NULL COMMENT '扣除手续费后的实际到账金额',
  `yue` decimal(10,2) DEFAULT NULL COMMENT '本次操作后分销账户余额',
  `status` int(1) DEFAULT NULL COMMENT '提现进度 0处理中 1提现成功 2提现失败',
  `reason` text COMMENT '后台驳回提现申请的原因',
  `type` int(1) DEFAULT NULL COMMENT '提现方式 1账户余额 2支付宝 3银行卡',
  `zfbusername` text COMMENT '支付宝姓名',
  `zfbaccount` text COMMENT '支付宝账户',
  `cardusername` text COMMENT '持卡人姓名',
  `cardnumber` text COMMENT '银行卡号',
  `bankname` text COMMENT '银行名称',
  `addtime` int(11) DEFAULT NULL COMMENT '申请时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=104 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_distributiontxlog
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_drawbacklog
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_drawbacklog`;
CREATE TABLE `xiaozu_drawbacklog` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `uid` int(20) NOT NULL,
  `username` varchar(100) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `orderid` int(20) NOT NULL,
  `shopid` int(11) NOT NULL,
  `content` text NOT NULL,
  `phone` varchar(50) NOT NULL,
  `contactname` varchar(100) NOT NULL,
  `status` int(1) DEFAULT '0' COMMENT '退款状态 0未待处理 1为已退 2为拒绝退款',
  `addtime` int(12) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT '0.00',
  `bkcontent` varchar(255) DEFAULT NULL COMMENT '回复说明',
  `admin_id` int(20) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '0为待商家确认 1为商家同意退款 2为商家拒绝退款',
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3961 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_drawbacklog
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_excomment
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_excomment`;
CREATE TABLE `xiaozu_excomment` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `userid` int(20) NOT NULL COMMENT '用户ID',
  `username` varchar(255) NOT NULL,
  `userlog` varchar(255) NOT NULL,
  `addtime` int(12) NOT NULL COMMENT '评价时间',
  `score` int(1) NOT NULL DEFAULT '0' COMMENT '评分',
  `comtype` int(1) NOT NULL COMMENT '1网站 2订单   ',
  `scoreto` varchar(255) DEFAULT '0' COMMENT '评价对象',
  `shopid` int(20) NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `content` text COMMENT '内容',
  `comacout` int(5) NOT NULL DEFAULT '0' COMMENT '被回复次数',
  `orderid` int(20) DEFAULT '0' COMMENT '订单ID',
  `imgurl` varchar(255) DEFAULT NULL,
  `orderctime` int(12) DEFAULT '0' COMMENT '订单消费时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_excomment
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_extendcate
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_extendcate`;
CREATE TABLE `xiaozu_extendcate` (
  `goodsid` int(20) NOT NULL,
  `catid` int(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_extendcate
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_extendco
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_extendco`;
CREATE TABLE `xiaozu_extendco` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `userid` int(20) DEFAULT NULL,
  `username` varchar(200) NOT NULL,
  `userlog` varchar(255) NOT NULL,
  `comid` int(20) NOT NULL,
  `addtime` int(12) NOT NULL,
  `content` text,
  `parent_id` int(20) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_extendco
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_friendlink
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_friendlink`;
CREATE TABLE `xiaozu_friendlink` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `type` smallint(1) NOT NULL DEFAULT '1',
  `typevalue` varchar(255) DEFAULT NULL,
  `linkurl` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `orderid` int(10) DEFAULT '99',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_friendlink
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_fxincomelog
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_fxincomelog`;
CREATE TABLE `xiaozu_fxincomelog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT NULL COMMENT '该记录受益会员的uid',
  `buyeruid` int(10) DEFAULT NULL COMMENT '下单者uid',
  `buyername` text COMMENT '下单者用户名',
  `grade` int(1) DEFAULT NULL COMMENT '该收益记录的分销等级 1 2 3',
  `yjb` decimal(10,2) DEFAULT NULL COMMENT '该记录佣金比例',
  `yjbcost` decimal(10,2) DEFAULT NULL COMMENT '该记录受益佣金',
  `orderid` int(10) DEFAULT NULL COMMENT '下单id',
  `dno` varchar(20) DEFAULT NULL COMMENT '下单订单号',
  `addtime` int(11) DEFAULT NULL COMMENT '下单时间',
  `ordercost` decimal(10,2) DEFAULT NULL COMMENT '本单实付金额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_fxincomelog
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_fxpid
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_fxpid`;
CREATE TABLE `xiaozu_fxpid` (
  `openid` varchar(255) NOT NULL,
  `fxpid` int(10) DEFAULT NULL COMMENT 'pid',
  `addtime` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_fxpid
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_gift
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_gift`;
CREATE TABLE `xiaozu_gift` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `market_cost` decimal(10,2) NOT NULL,
  `score` int(7) NOT NULL DEFAULT '0',
  `title` varchar(100) DEFAULT NULL,
  `content` text,
  `typeid` int(10) NOT NULL DEFAULT '0',
  `sell_count` int(5) NOT NULL DEFAULT '0' COMMENT '销售数量',
  `stock` int(6) NOT NULL DEFAULT '0' COMMENT '库存',
  `img` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12345 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_gift
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_giftlog
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_giftlog`;
CREATE TABLE `xiaozu_giftlog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `giftid` int(20) NOT NULL,
  `uid` int(20) NOT NULL,
  `score` int(6) NOT NULL,
  `addtime` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `address` varchar(150) DEFAULT NULL,
  `telphone` varchar(15) DEFAULT NULL,
  `contactman` varchar(150) DEFAULT NULL,
  `count` int(2) NOT NULL DEFAULT '0',
  `content` varchar(255) NOT NULL COMMENT '备注',
  `giftname` text COMMENT '礼品名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12348 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_giftlog
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_gifttype
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_gifttype`;
CREATE TABLE `xiaozu_gifttype` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `orderid` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_gifttype
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_goods
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_goods`;
CREATE TABLE `xiaozu_goods` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `typeid` int(10) NOT NULL COMMENT '商品类型',
  `parentid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL COMMENT '商品名称',
  `count` int(5) NOT NULL COMMENT '商品数量',
  `cost` decimal(8,2) NOT NULL COMMENT '商品价格',
  `img` varchar(150) NOT NULL COMMENT '图片地址',
  `point` int(5) NOT NULL COMMENT '评分',
  `sellcount` int(5) NOT NULL COMMENT '销售数量',
  `shopid` int(1) NOT NULL COMMENT '店铺ID',
  `uid` int(20) NOT NULL,
  `signid` varchar(100) NOT NULL COMMENT '促销标签ID集',
  `pointcount` int(5) NOT NULL DEFAULT '0' COMMENT '评价次数',
  `instro` text COMMENT '简单说明',
  `descgoods` varchar(255) DEFAULT NULL COMMENT '商品简述',
  `daycount` int(5) NOT NULL DEFAULT '0' COMMENT '每日销售数量',
  `marketcost` decimal(8,2) NOT NULL COMMENT '超市价格',
  `is_com` varchar(1) NOT NULL DEFAULT '0' COMMENT '商城使用字段 1首页推荐',
  `show_com` int(1) NOT NULL DEFAULT '0' COMMENT '是否在店铺展示中显示',
  `is_hot` tinyint(1) NOT NULL DEFAULT '0' COMMENT '热卖',
  `is_new` tinyint(1) NOT NULL DEFAULT '0' COMMENT '新品',
  `bagcost` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '打包费',
  `shoptype` int(1) NOT NULL COMMENT '0外卖1超市',
  `good_order` int(11) DEFAULT '0',
  `is_waisong` int(11) NOT NULL DEFAULT '1',
  `is_dingtai` int(11) NOT NULL DEFAULT '1',
  `weeks` varchar(100) NOT NULL DEFAULT '1,2,3,4,5,6,0',
  `goodattr` varchar(25) NOT NULL,
  `have_det` int(1) NOT NULL DEFAULT '0' COMMENT '1表示是多个规格商品 否则0 ',
  `product_attr` text NOT NULL COMMENT '表示规格数组  使用的规格值',
  `is_cx` int(1) NOT NULL DEFAULT '0' COMMENT '是否开启商品促销，默认0未开启，1开启',
  `wx_url` varchar(255) DEFAULT NULL,
  `virtualsellcount` int(11) NOT NULL COMMENT '商品虚拟销量',
  `is_live` char(1) NOT NULL COMMENT '上、下架',
  PRIMARY KEY (`id`),
  KEY `shopid` (`shopid`)
) ENGINE=MyISAM AUTO_INCREMENT=9585 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_goods
-- ----------------------------
INSERT INTO `xiaozu_goods` VALUES ('9583', '1817', '7281', '蔓越莓酥饼', '999', '15.00', '/images/410100/shop/1213/goods/20180623185613222.png', '0', '1', '1213', '0', '', '0', '', '', '0', '0.00', '0', '0', '0', '0', '0.00', '0', '0', '1', '1', '1,2,3,4,5,6,0', '', '0', '', '0', null, '0', '1');
INSERT INTO `xiaozu_goods` VALUES ('9584', '1817', '7282', '罗马盾牌', '1000', '15.00', '/images/410100/shop/1213/goods/20180623185625559.png', '0', '0', '1213', '0', '', '0', '', '', '0', '0.00', '0', '0', '0', '0', '0.00', '0', '0', '1', '1', '1,2,3,4,5,6,0', '', '0', '', '0', null, '0', '1');

-- ----------------------------
-- Table structure for xiaozu_goodscx
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_goodscx`;
CREATE TABLE `xiaozu_goodscx` (
  `goodsid` int(20) NOT NULL,
  `cxzhe` int(3) NOT NULL DEFAULT '0',
  `cxstarttime` int(12) NOT NULL DEFAULT '0',
  `ecxendttime` int(12) NOT NULL DEFAULT '0',
  `cxstime1` int(8) NOT NULL DEFAULT '0',
  `cxetime1` int(8) NOT NULL DEFAULT '0',
  `cxstime2` int(8) NOT NULL DEFAULT '0',
  `cxetime2` int(8) NOT NULL DEFAULT '0',
  `cxnum` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_goodscx
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_goodsgg
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_goodsgg`;
CREATE TABLE `xiaozu_goodsgg` (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT '规格id',
  `shoptype` int(1) NOT NULL DEFAULT '0' COMMENT '店铺类型  0餐厅  1超市',
  `name` varchar(100) NOT NULL COMMENT '规格名称/规格值得',
  `orderid` int(5) NOT NULL DEFAULT '999' COMMENT '排序ID',
  `parent_id` int(20) NOT NULL DEFAULT '0' COMMENT '0表示规格名称    1表示规格值',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=271 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_goodsgg
-- ----------------------------
INSERT INTO `xiaozu_goodsgg` VALUES ('268', '0', '冷热', '1', '0');
INSERT INTO `xiaozu_goodsgg` VALUES ('269', '0', '常温', '0', '268');
INSERT INTO `xiaozu_goodsgg` VALUES ('270', '0', '冰镇', '0', '268');

-- ----------------------------
-- Table structure for xiaozu_goodsimg
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_goodsimg`;
CREATE TABLE `xiaozu_goodsimg` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `goodsid` int(20) NOT NULL,
  `imgname` varchar(250) NOT NULL,
  `imgurl` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=327 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_goodsimg
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_goodslibrary
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_goodslibrary`;
CREATE TABLE `xiaozu_goodslibrary` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `typeid` int(10) NOT NULL COMMENT '商品类型',
  `name` varchar(100) NOT NULL COMMENT '商品名称',
  `cost` decimal(8,2) NOT NULL COMMENT '商品价格',
  `img` varchar(150) NOT NULL COMMENT '图片地址',
  `instro` text COMMENT '简单说明',
  `good_order` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7663 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_goodslibrary
-- ----------------------------
INSERT INTO `xiaozu_goodslibrary` VALUES ('7281', '110', '蔓越莓酥饼', '15.00', '/upload/user/20170928095809628.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7282', '110', '罗马盾牌', '15.00', '/upload/user/20170928095818594.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7283', '110', '蔓越莓麦片', '15.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7284', '110', '乳酪饼干', '15.00', '/upload/user/20170928095854803.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7285', '110', '巧克力雪球', '15.00', '/upload/user/20170928095909360.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7288', '110', '咖啡坚果', '15.00', '/upload/user/20170928095941519.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7289', '110', '阿拉棒', '13.00', '/upload/user/20170928095952427.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7400', '117', '草莓奶茶', '8.00', '', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7415', '123', '维多利亚酒店', '168.00', '', '', '4');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7398', '117', '原味奶茶', '1.00', '', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6024', '43', '花生', '10.00', '/upload/user/20160111120210959.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6023', '43', '毛豆', '10.00', '/upload/user/20160111120215819.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6022', '43', '海带', '10.00', '/upload/user/20160111120224465.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6021', '43', '千张', '10.00', '/upload/user/20160111120231392.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6020', '43', '鸭肠', '10.00', '/upload/user/20160111120240413.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6018', '43', '鸭腿', '10.00', '/upload/user/20160111120247285.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6019', '43', '鸭翅', '10.00', '/upload/user/20160111120340607.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6017', '43', '鸭掌', '10.00', '/upload/user/20160111120259101.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6016', '43', '鸭头', '10.00', '/upload/user/20160111120305116.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6015', '43', '鸭舌', '10.00', '/upload/user/20160111120315814.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6014', '43', '鸭架', '10.00', '/upload/user/20160111120325992.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6013', '43', '鸭脖', '10.00', '/upload/user/20160111120331689.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7237', '110', '粉色记忆（12+8+6）', '298.00', '/upload/user/20170928094220774.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7236', '110', '绿野仙踪', '118.00', '/upload/user/20170928094234862.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7235', '110', '莓好时光', '118.00', '/upload/user/20170928094245951.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7234', '110', '花漾甜心', '118.00', '/upload/user/20170928094257470.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7233', '110', '动力小火车', '178.00', '/upload/user/20170928094310134.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7232', '110', 'kitty猫', '198.00', '/upload/user/20170928094321781.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7230', '110', '冰雪奇缘', '178.00', '/upload/user/20170928094331171.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7231', '110', '创意托马斯', '198.00', '/upload/user/20170928094344660.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7414', '123', '逸居酒店（南京路店）', '128.00', '', '', '3');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7508', '135', '300g飘柔垂顺亮泽精华发膜', '18.00', '', '简介内容36', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7507', '135', '300g飘柔人参精华精纯发膜', '18.00', '', '简介内容35', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7506', '135', '300g飘柔焗油丝质柔滑精华发膜', '18.00', '', '简介内容34', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7505', '135', '400g飘柔垂顺亮泽润发乳', '18.50', '', '简介内容33', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7504', '135', '400g飘柔人参滋养修护润发乳', '18.50', '', '简介内容32', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7493', '135', '190g飘柔家庭护理绿茶长效清爽去油洗发露', '7.00', '', '简介内容21', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7396', '116', '虾汁拌面', '10.00', '/upload/user/20170928133717586.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7395', '116', '飘香葱油咸饭', '10.00', '/upload/user/20170928133725919.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7394', '116', '拉面', '1.00', '/upload/user/20170928133731118.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7393', '116', '茴香小油条', '6.00', '/upload/user/20170928133743546.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7391', '116', '白米饭', '1.00', '/upload/user/20170928133748379.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7392', '116', '海鲜炒米线', '10.00', '/upload/user/20170928133754716.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7390', '116', '招牌小龙虾', '50.00', '/upload/user/20170928133812562.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7389', '116', '招牌秘制小龙虾', '50.00', '/upload/user/20170928133821133.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7388', '116', '清蒸小龙虾', '50.00', '/upload/user/20170928133827858.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7387', '116', '秘制小龙虾', '50.00', '/upload/user/20170928133835819.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7386', '116', '秘制虾尾', '50.00', '/upload/user/20170928133844522.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7385', '116', '龙虾两吃', '50.00', '/upload/user/20170928133852608.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7384', '116', '经典原味虾尾', '50.00', '/upload/user/20170928133903921.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7383', '116', '超值小龙虾套餐A', '50.00', '/upload/user/20170928133908763.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7382', '116', '爆蒜虾尾', '50.00', '/upload/user/20170928133913921.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7381', '49', '招牌皮皮虾', '45.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('5692', '0', '商品3', '30.00', '', '简介内容3', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('5691', '0', '商品2', '20.00', '', '简介内容2', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('5690', '0', '商品1', '10.00', '', '简介内容1', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('5689', '0', '商品3', '30.00', '', '简介内容3', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('5688', '0', '商品2', '20.00', '', '简介内容2', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('5687', '0', '商品1', '10.00', '', '简介内容1', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('5686', '0', '商品3', '30.00', '', '简介内容3', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('5685', '0', '商品2', '20.00', '', '简介内容2', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('5683', '0', '商品3', '30.00', '', '简介内容3', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('5684', '0', '商品1', '10.00', '', '简介内容1', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('5682', '0', '商品2', '20.00', '', '简介内容2', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('5681', '0', '商品1', '10.00', '', '简介内容1', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('5680', '0', '商品3', '30.00', '', '简介内容3', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('5678', '0', '商品1', '10.00', '', '简介内容1', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('5679', '0', '商品2', '20.00', '', '简介内容2', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6130', '48', '加多宝', '5.00', '/upload/pliang/20160109200441680.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6131', '48', '怡宝', '5.00', '/upload/pliang/20160109200438137.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7295', '48', '果粒橙（大）', '12.00', '/upload/user/20170928105854807.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6133', '48', '鲜榨豆浆', '5.00', '/upload/pliang/20160109200441572.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6134', '48', '农夫山泉', '5.00', '/upload/pliang/20160109200438641.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6135', '48', '恒大冰泉', '4.00', '/upload/pliang/20160109200439857.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6136', '48', '芬达', '5.00', '/upload/pliang/20160109200437214.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6137', '48', '红牛', '5.00', '/upload/pliang/20160109200442385.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6138', '48', '冰红茶', '5.00', '/upload/pliang/20160109200443891.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6139', '49', '腊八豆角脆骨', '25.00', '/upload/pliang/20160109201717396.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6140', '49', '萝卜干炒腊肉', '25.00', '/upload/pliang/20160109201841996.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6141', '49', '浓汤鸡汁脆笋', '30.00', '/upload/pliang/20160109201952833.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6142', '49', '农家小炒肉', '30.00', '/upload/pliang/20160109202112532.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6143', '49', '松仁玉米', '30.00', '/upload/pliang/20160109202102129.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6144', '49', '干锅啤酒鸭', '25.00', '/upload/pliang/20160109202334694.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6145', '49', '山城毛血旺', '30.00', '/upload/pliang/20160109202326999.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6146', '49', '毛氏红烧肉', '20.00', '/upload/pliang/20160109202501959.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6147', '50', '姜汁莲菜', '12.00', '/upload/pliang/20160109204923250.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6148', '50', '凉拌木耳', '12.00', '/upload/pliang/20160109204901641.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6149', '50', '酱泡萝卜皮', '12.00', '/upload/pliang/20160109201952833.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6150', '50', '木瓜沙拉', '12.00', '/upload/pliang/20160109204851764.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6151', '50', '开封桶子鸡', '12.00', '/upload/pliang/20160109204902179.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6152', '50', '大盘牛腱', '12.00', '/upload/pliang/20160109204901670.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6153', '50', '卤水拼盘', '12.00', '/upload/pliang/20160109204901433.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6154', '50', '大盘猪手', '12.00', '/upload/pliang/20160109204902239.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6155', '50', '水晶牛肉', '12.00', '/upload/pliang/20160109204901670.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7245', '110', '黑色风暴', '158.00', '/upload/user/20170928094356579.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7244', '110', '草莓马里奥', '118.00', '/upload/user/20170928094409276.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7243', '110', '缤纷贝蒂', '118.00', '/upload/user/20170928094419320.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7242', '110', '史努比', '118.00', '/upload/user/20170928094430862.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7241', '110', '逢赌必赢', '128.00', '/upload/user/20170928094440829.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7240', '110', '华尔兹舞曲', '118.00', '/upload/user/20170928094454897.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7239', '110', '蟠桃贺寿', '118.00', '/upload/user/20170928094509390.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7413', '51', '麻辣鸡', '15.00', '', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7238', '110', '寿比南山', '118.00', '/upload/user/20170928094522945.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6385', '0', 'set_time_limit(0);', '0.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6384', '0', '', '0.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6383', '0', '?>', '0.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6382', '0', 'phpinfo();', '0.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6381', '0', '?>', '0.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6380', '0', 'phpinfo();', '0.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6379', '0', '?>', '0.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6378', '0', 'phpinfo();', '0.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7216', '110', '经典伍仁', '7.00', '/upload/user/20170928094532786.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6213', '54', '伤风停胶囊', '16.00', '/upload/user/20160112113928521.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6214', '54', '小柴胡颗粒', '9.00', '/upload/user/20160112114025882.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6215', '54', '感冒止咳颗粒', '13.00', '/upload/user/20160112114134949.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6216', '54', '福森双黄连口服液', '18.00', '/upload/user/20160112114326285.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6217', '54', '午时茶颗粒', '9.00', '/upload/user/20160112114500995.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7167', '106', '苹果', '1.00', '/upload/user/20170927102118939.jpg', '', '2');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6218', '54', '柴胡滴丸', '12.00', '/upload/user/20160112114531342.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6219', '54', '黄连上清片', '9.00', '/upload/user/20160112114600802.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6220', '54', '复方氨酚烷胺片', '13.00', '/upload/user/20160112114633321.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6221', '54', '小羚羊小儿退热贴', '29.00', '/upload/user/20160112114721319.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6222', '54', '羚翘解毒丸', '12.00', '/upload/user/20160112114857504.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6223', '54', '双黄连口服液', '21.00', '/upload/user/20160112114928988.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6224', '54', '连翘败毒丸', '11.00', '/upload/user/20160112115010630.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6225', '54', '咽立爽口含滴丸', '18.00', '/upload/user/20160112115126874.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6226', '54', '二丁颗粒', '29.50', '/upload/user/20160112115215335.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6227', '54', '西瓜霜清咽含片', '6.00', '/upload/user/20160112115325811.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6228', '54', '栀子金花丸', '18.00', '/upload/user/20160112115354262.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6229', '54', '复方鲜竹沥液', '10.00', '/upload/user/20160112115659453.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6230', '54', '止咳宝片', '29.80', '/upload/user/20160112115730168.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6231', '54', '小儿止咳糖浆', '13.00', '/upload/user/20160112115753915.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6232', '54', '桔贝合剂', '35.00', '/upload/user/20160112115822990.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6233', '54', '小儿氨酚黄那敏颗粒', '11.00', '/upload/user/20160112115945679.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6234', '54', '999小儿感冒颗粒', '13.00', '/upload/user/20160112120011265.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6235', '54', '小儿清咽颗粒 54g', '16.00', '/upload/user/20160112120041593.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6236', '54', '小儿氨酚烷胺颗粒', '13.50', '/upload/user/20160112120236674.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6237', '54', '小儿氨酚黄那敏颗粒', '10.00', '/upload/user/20160112120308952.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6238', '54', '小儿感冒颗粒', '13.00', '/upload/user/20160112120339891.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6239', '54', '小儿宝泰康颗粒', '21.50', '/upload/user/20160112120414184.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6240', '54', '健胃消食片', '7.50', '/upload/user/20160112120549729.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6241', '54', '健胃消食片', '7.50', '/upload/user/20160112120616709.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6242', '54', '云南白药健胃消食片', '7.00', '/upload/user/20160112120657433.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6243', '54', '香砂养胃丸', '12.00', '/upload/user/20160112120735162.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6244', '54', '仲景 保和丸 200丸', '9.00', '/upload/user/20160112120817746.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6245', '54', '修正牌复方金银花颗', '20.00', '/upload/user/20160112121040384.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7166', '106', '火龙果', '3.00', '/upload/user/20170927102057400.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6246', '54', '蒲公英片', '10.50', '/upload/user/20160112121105671.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7214', '109', '原味牛奶', '5.00', '/upload/user/20170927163354234.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7213', '109', '红豆牛奶', '6.00', '/upload/user/20170927163416774.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7212', '109', '紫薯燕麦牛奶', '6.00', '/upload/user/20170927163433322.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7211', '109', '森林玫果', '5.00', '/upload/user/20170927163446465.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7210', '109', '冰鲜柠檬水', '5.00', '/upload/user/20170927163459813.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7209', '109', '水蜜桃汁', '5.00', '/upload/user/20170927163511372.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7208', '109', '峰蜜柚子茶', '7.00', '/upload/user/20170927163534809.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7207', '109', '冰爽红牛', '6.00', '/upload/user/20170927163547334.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7206', '109', '冰红茶', '6.00', '/upload/user/20170927163601569.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7205', '109', '紫薯燕麦奶茶', '6.00', '/upload/user/20170927163617252.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7204', '109', '紫薯燕麦奶茶', '6.00', '/upload/user/20170927163704377.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7203', '109', '南国红豆奶茶', '6.00', '/upload/user/20170927163722696.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7202', '109', '草莓奶茶', '6.00', '/upload/user/20170927163737511.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7201', '109', '蓝莓奶茶', '6.00', '/upload/user/20170927163805161.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7200', '109', '五谷养生奶茶', '8.00', '/upload/user/20170927163749295.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7490', '135', '190g飘柔家庭护理兰花长效洁顺水润洗发露', '7.00', '', '简介内容18', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7196', '108', '辣板', '10.00', '/upload/user/20170927104707803.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7195', '108', '五彩糯米糯', '20.00', '/upload/user/20170927104640114.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7194', '108', '绿茶糯米糯', '10.00', '/upload/user/20170927104543224.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7193', '108', '张君雅大礼包', '50.00', '/upload/user/20170927104513354.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7492', '135', '190g飘柔家庭护理芦荟长效止痒滋润洗发露', '7.00', '', '简介内容20', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7191', '108', '小面包', '10.00', '/upload/user/20170927103912710.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7190', '108', '肉松饼', '10.00', '/upload/user/20170927103831200.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7189', '108', '好丽友软面包', '10.00', '/upload/user/20170927103644735.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7188', '108', '小薯条', '10.00', '/upload/user/20170927103521503.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7187', '108', '盼盼软面包', '10.00', '/upload/user/20170927103447799.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7491', '135', '190g飘柔家庭护理兰花长效清爽去屑洗发露', '7.00', '', '简介内容19', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7185', '107', '无壳碧根果', '12.00', '/upload/user/20170927103250549.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7184', '107', '蚕豆', '12.00', '/upload/user/20170927103156259.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7183', '107', '坚果大礼包', '24.00', '/upload/user/20170927103054640.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7182', '107', '糖花生', '12.00', '/upload/user/20170927103004377.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7181', '107', '蜜板栗', '12.00', '/upload/user/20170927102938797.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7180', '107', '夏威夷果', '12.00', '/upload/user/20170927102913499.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7179', '107', '开心果', '12.00', '/upload/user/20170927102843401.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7178', '107', '碧根果', '12.00', '/upload/user/20170927102817700.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7177', '107', '巴旦木', '12.00', '/upload/user/20170927102742114.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7176', '106', '水果大礼包', '16.00', '/upload/user/20170927102706755.png', '', '3');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7175', '106', '提子', '3.00', '/upload/user/20170927102514731.jpg', '', '3');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7174', '106', '香梨', '2.00', '/upload/user/20170927102500416.jpg', '', '3');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7173', '106', '木瓜', '5.00', '/upload/user/20170927102430108.jpg', '', '3');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7172', '106', '石榴', '2.00', '/upload/user/20170927102319253.jpg', '', '3');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7171', '106', '香蕉', '1.00', '', '', '3');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7170', '106', '芒果', '3.00', '/upload/user/20170927102224311.jpg', '', '3');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7169', '106', '哈密瓜', '12.00', '/upload/user/20170927102204354.jpg', '', '3');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7168', '106', '青桔', '2.00', '/upload/user/20170927102151351.jpg', '', '3');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6308', '56', '19支红玫瑰黄莺搭配白色镂空纸包装咖啡色长款礼盒', '138.00', '/upload/user/20160115171834896.png', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6309', '56', '19支香槟玫瑰黄莺白色镂空纸酒红色缎带结咖啡色长款礼盒', '138.00', '/upload/user/20160115171908297.png', '', '2');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6310', '56', '33支红玫瑰2支小熊黄莺满天星黑色精品纸包装', '158.00', '/upload/user/20160115172044424.png', '', '3');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6311', '56', '11支粉玫瑰黄莺搭配深咖色皱纹纸麻片粉色缎带结白色长形礼盒', '118.00', '/upload/user/20160115172118985.png', '', '3');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6312', '56', '33支香槟玫瑰2支小熊英文报纸长形包装', '186.00', '/upload/pliang/20171225190516314.jpg', '<p><img src=\"http://m6.waimairen.com/upload/goods/20180112152506576.jpg\" alt=\"\" /></p>\r\n<p><img src=\"http://image.ghwmr.com/images/410100/goodspub/20180526091517621.png\" alt=\"\" /></p>\r\n<p><img src=\"http://m6.waimairen.com/upload/goods/20180112152517744.jpg\" alt=\"\" /></p>', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6313', '56', '33支红玫瑰一只小熊绿叶搭配love字样插制咖啡色长款礼盒', '195.00', '/upload/user/20160115172302168.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6314', '56', '11支白玫瑰栀子叶衬托深绿色皱纹纸圆形包装香槟色缎带结', '88.00', '/upload/user/20160115172523885.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6315', '56', '11支昆明蓝玫瑰黄莺满天星搭配宝蓝色瓦楞纸包装韩国纱束腰', '136.00', '/upload/user/20160115172554543.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6316', '56', '19支粉玫瑰黄莺白色蜡烛包装粉色缎带咖啡色礼盒', '136.00', '/upload/user/20160115172633940.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6317', '56', '19支红玫瑰花束', '148.00', '/upload/user/20160115172715669.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6318', '56', '9支粉玫瑰9颗费列罗巧克力两只小熊心形礼盒', '165.00', '/upload/user/20160115172757683.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6319', '56', '99支粉玫瑰黄莺满天星粉色卷纸粉色细纱包装', '368.00', '/upload/user/20160115172826237.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7497', '135', '200g飘柔净爽去屑洗发露', '11.00', '', '简介内容25', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7438', '56', '200g飘柔焗油丝质柔滑洗发露', '11.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7439', '56', '200g飘柔滋润去屑洗发露', '11.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6322', '56', '33支香槟2个小熊 英文纸包装', '158.00', '/upload/user/20160115173315697.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7501', '135', '200g飘柔滋润去屑洗发露', '11.00', '', '简介内容29', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7440', '56', '200g飘柔精油润养柔顺洗发露（原焗油丝质）', '0.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7489', '135', '190g飘柔家庭护理葵花籽长效黑亮滋润洗发露', '7.00', '', '简介内容17', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7488', '135', '400g飘柔垂顺亮泽洗发露', '20.00', '', '简介内容16', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7487', '135', '400g飘柔焗油去屑洗发露', '20.00', '', '简介内容15', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7486', '135', '400g飘柔人参滋养修护洗发露', '20.00', '', '简介内容14', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7485', '135', '400g飘柔滋润去屑洗发露', '20.00', '', '简介内容13', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7484', '135', '400g飘柔焗油丝质柔滑洗发露', '20.00', '', '简介内容12', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7483', '135', '400g飘柔家庭护理杏仁长效柔顺滋养洗发露', '14.00', '', '简介内容11', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7482', '135', '400g飘柔家庭护理绿茶长效清爽去油洗发露', '14.00', '', '简介内容10', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7481', '135', '400g飘柔家庭护理芦荟长效止痒滋润洗发露', '14.00', '', '简介内容9', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7480', '135', '400g飘柔家庭护理兰花长效洁顺水润洗发露', '14.00', '', '简介内容8', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7479', '135', '400g飘柔家庭护理兰花长效清爽去屑洗发露', '14.00', '', '简介内容7', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7478', '135', '750g飘柔家庭护理兰花长效清爽去屑洗发露', '22.50', '', '简介内容6', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7477', '135', '750g飘柔家庭护理兰花长效洁顺水润洗发露', '22.50', '', '简介内容5', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7474', '135', '750g飘柔家庭护理杏仁长效柔顺滋养洗发露', '22.50', '', '简介内容2', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7475', '135', '750g飘柔家庭护理绿茶长效清爽去油洗发露', '22.50', '', '简介内容3', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7476', '135', '750g飘柔家庭护理芦荟长效止痒滋润洗发露', '22.50', '', '简介内容4', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7499', '135', '200g飘柔焗油丝质柔滑洗发露', '11.00', '', '简介内容27', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7498', '135', '200g飘柔焗油去屑洗发露', '11.00', '', '简介内容26', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6342', '56', '11支香槟玫瑰黄莺两只小熊咖啡色镂空纸咖啡色长款礼盒', '98.00', '/upload/user/20160115180203249.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7502', '135', '400g飘柔焗油丝质柔滑润发乳', '18.50', '', '简介内容30', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6344', '56', '19支粉色康乃馨两支百合黄莺满天星淡紫色精品纸平角加纱', '132.00', '/upload/user/20160115180819424.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6345', '56', '19支红色康乃馨黄莺白色长款礼盒', '99.00', '/upload/user/20160115180957713.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6346', '56', '19支粉色康乃馨黄莺粉色长款礼盒', '109.00', '/upload/user/20160115181026598.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6347', '56', '19支粉色康乃馨3支百合黄莺满天星白色粉色卷纸圆形包装', '139.00', '/upload/user/20160115181100594.png', '<img src=\"http://m6.waimairen.com/upload/goods/20180112152446546.jpg\" alt=\"\" /><br />', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6348', '56', '19支红康 2支多头百合 黄莺满天星点缀 紫色卷纸包装', '128.00', '/upload/user/20160115181200931.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6349', '56', '33支粉色康乃馨黄莺满天星粉色衬淡紫色卷纸圆形包装', '148.00', '/upload/user/20160115181228344.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6350', '56', '99支红色康乃馨黄莺满天星外围淡紫色皱纹纸圆形包装', '226.00', '/upload/user/20160115181318986.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6351', '56', '99支粉色康乃馨黄莺满天星外围粉色卷纸粉纱圆形', '226.00', '/upload/user/20160115181350573.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6352', '56', '9支向日葵 咖啡色礼盒', '186.00', '/upload/user/20160115181419288.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6353', '56', '红粉香槟三色33支玫瑰英文报纸圆形包装', '188.00', '/upload/user/20160115181636135.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6354', '56', '33支粉玫瑰两只小熊黄莺满天星丰满粉色瓦楞纸多层圆形包装', '168.00', '/upload/user/20160115181726448.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6355', '56', '30支彩玫瑰新款彩玫圆形礼盒 粉佳人加粉色橘梗加时令绿色配草', '268.00', '/upload/user/20160115181842501.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6356', '56', '演讲台花', '119.00', '/upload/user/20160115181930240.png', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7215', '110', '五香牛肉', '8.00', '/upload/user/20170928094541140.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7437', '56', '200g飘柔精油润养柔顺润发乳', '0.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7163', '102', '五常大米', '38.00', '/upload/pliang/20170926200328432.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7434', '56', '200g飘柔垂顺亮泽润发乳', '10.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7217', '110', '抹茶红豆月饼', '7.00', '/upload/user/20170928094553787.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7218', '110', '金沙奶黄月饼', '8.00', '/upload/user/20170928094606903.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7219', '110', '莲蓉蛋黄月饼', '8.00', '/upload/user/20170928094616865.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7220', '110', '花艺E10+8双层', '228.00', '/upload/user/20170928094626457.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7221', '110', '花艺D', '178.00', '/upload/user/20170928094637447.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7222', '110', '花艺C', '178.00', '/upload/user/20170928094646716.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7223', '110', 'B款', '178.00', '/upload/user/20170928094659139.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7224', '110', 'A款', '178.00', '/upload/user/20170928094711335.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7226', '110', '榴莲千层', '198.00', '/upload/user/20170928094852490.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7227', '110', '芒果千层', '168.00', '/upload/user/20170928094902501.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7228', '110', '抹茶千层', '158.00', '/upload/user/20170928094927774.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7229', '110', '布朗熊灰', '178.00', '/upload/user/20170928094956944.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7280', '110', '瓦片酥', '8.00', '/upload/user/20170928095755681.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7279', '110', '黄金芝士曲奇', '12.00', '/upload/user/20170928095746834.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7278', '110', '紫玉蓝莓', '12.00', '/upload/user/20170928095735970.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7277', '110', '提拉米苏', '15.00', '/upload/user/20170928095725873.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7276', '110', '奥利奥班戟', '16.00', '/upload/user/20170928095715853.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7275', '110', '芒果班戟', '16.00', '/upload/user/20170928095656223.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7274', '110', '榴莲班戟', '19.00', '/upload/user/20170928095646963.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7273', '110', '抹茶慕斯', '12.00', '/upload/user/20170928095632197.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7272', '110', '草莓慕斯', '12.00', '/upload/user/20170928095623305.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7271', '110', '抹茶蛋卷', '20.00', '/upload/user/20170928095601694.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7270', '110', '蓝莓巧克力', '12.00', '/upload/user/20170928095551864.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7265', '110', '蛋糕寿司', '7.50', '/upload/user/20170928095442755.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7266', '110', '培根三明治', '6.00', '/upload/user/20170928095511900.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7267', '110', '大理石旋风', '12.00', '/upload/user/20170928095522367.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7268', '110', '豆乳盒子', '68.00', '/upload/user/20170928095531737.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7269', '110', '彩虹慕斯', '12.00', '/upload/user/20170928095542329.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7380', '49', '皮皮虾', '45.00', '/upload/user/20170928134505640.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7379', '49', '麻辣蟹爪', '45.00', '/upload/user/20170928134513671.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7378', '49', '金针菇酸汤肥牛', '45.00', '/upload/user/20170928134520403.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7377', '49', '基围虾', '45.00', '/upload/user/20170928134526413.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7376', '49', '沸腾鱼', '45.00', '/upload/user/20170928134532294.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7373', '50', '小黄瓜', '10.00', '/upload/user/20170928134351270.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7374', '50', '小西红柿', '10.00', '/upload/user/20170928134400224.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7375', '50', '油炸花生米', '10.00', '/upload/user/20170928134405464.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7262', '110', '肉松小贝', '12.00', '/upload/user/20170928095406243.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7261', '110', '蔓越莓小餐包', '7.50', '/upload/user/20170928095355162.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7260', '110', '乳酪蛋卷', '7.50', '/upload/user/20170928095344346.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7259', '110', '蟹小方', '15.00', '/upload/user/20170928095325102.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7258', '110', '脆皮奶滋', '6.00', '/upload/user/20170928095316443.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7257', '110', '肉松丸子', '6.50', '/upload/user/20170928095240705.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7256', '110', '巧克力软欧包', '8.50', '/upload/user/20170928095229107.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7255', '110', '草莓慕斯', '148.00', '/upload/user/20170928095006996.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7249', '110', '樱桃慕斯', '158.00', '/upload/user/20170928095015378.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7250', '110', '黑森林', '148.00', '/upload/user/20170928095027944.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7251', '110', '巧克力慕斯', '158.00', '/upload/user/20170928095037571.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7252', '110', '提拉米苏', '168.00', '/upload/user/20170928095047290.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7253', '110', '蓝莓慕斯', '158.00', '/upload/user/20170928095057653.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7254', '110', '芒果慕斯', '148.00', '/upload/user/20170928095108247.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7248', '110', '酸奶海洋慕斯', '158.00', '/upload/user/20170928095119613.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7247', '110', '芝士抹茶', '158.00', '/upload/user/20170928095207342.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7246', '110', '缤纷四季慕斯', '158.00', '/upload/user/20170928095217103.jpg', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7264', '110', '黄金热狗伴侣', '7.50', '/upload/user/20170928095430498.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7263', '110', '海苔肉松卷', '5.50', '/upload/user/20170928095418375.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7158', '102', '山西老陈醋', '8.00', '/upload/pliang/20170926200319628.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7159', '102', '新疆番茄酱', '5.00', '/upload/pliang/20170926200320954.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7160', '102', '老干妈', '8.00', '/upload/pliang/20170926200320902.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6780', '0', '系统1', '1.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6781', '0', '系统2', '1.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6782', '0', '系统3', '1.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6783', '0', '系统4', '1.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6784', '0', '系统5', '1.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6785', '0', '系统6', '1.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6786', '0', '系统7', '1.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6787', '0', '系统8', '1.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6788', '0', '系统9', '1.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('6789', '0', 'set_time_limit(0);', '0.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7287', '110', '芝士百里香', '15.00', '/upload/user/20170928095930274.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7291', '48', '雪碧', '4.00', '', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7292', '48', '芬达', '4.00', '/upload/user/20170928105816622.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7294', '48', '脉动', '4.00', '/upload/user/20170928105841702.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7300', '49', '麻辣香锅1人餐', '50.00', '/upload/user/20170928110705814.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7301', '49', '麻辣香锅2人餐', '99.00', '/upload/user/20170928110714752.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7302', '49', '麻辣香锅3人餐', '139.00', '/upload/user/20170928110723659.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7303', '49', '麻辣香锅4人餐', '169.00', '/upload/user/20170928110732802.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7304', '49', '香辣烤鱼(深海鱼)', '89.00', '/upload/user/20170928110745677.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7305', '49', '烤鱼（草鱼）', '108.00', '/upload/user/20170928110756534.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7306', '49', '烤鱼（清江鱼', '129.00', '/upload/user/20170928110805546.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7307', '49', '烤鱼（黑鱼）', '135.00', '/upload/user/20170928110820845.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7308', '49', '香辣牛蛙', '99.00', '/upload/user/20170928110835282.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7309', '49', '香辣鱿鱼', '89.00', '/upload/user/20170928110850289.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7310', '49', '香辣全家福', '169.00', '/upload/user/20170928110914187.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7311', '49', '香辣虾尾（大份', '148.00', '/upload/user/20170928110902166.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7312', '49', '香辣虾尾（小份）', '78.00', '/upload/user/20170928110924549.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7313', '111', '白灼花螺', '38.00', '/upload/user/20170928134549850.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7314', '111', '爆炒蛏子', '38.00', '/upload/user/20170928134553820.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7315', '111', '爆炒花蛤', '38.00', '/upload/user/20170928134559531.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7316', '111', '爆炒花蛤虾尾双拼', '38.00', '/upload/user/20170928134604399.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7317', '111', '蒜蓉粉丝鲍鱼', '38.00', '/upload/user/20170928134609926.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7318', '111', '蒜蓉粉丝蒸天鹅蛋', '38.00', '/upload/user/20170928134615445.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7319', '111', '蒜蓉花蛤蛏子双拼', '38.00', '/upload/user/20170928134620333.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7320', '111', '蒜蓉扇贝', '38.00', '/upload/user/20170928134624932.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7321', '111', '蒜蓉生蚝', '38.00', '/upload/user/20170928134629851.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7322', '111', '蒜蓉生蚝扇贝双拼', '38.00', '/upload/user/20170928134635960.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7323', '111', '香辣螺钉', '38.00', '/upload/user/20170928134639864.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7324', '111', '招牌花螺', '38.00', '/upload/user/20170928134644619.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7325', '48', '百事可乐', '5.00', '/upload/user/20170928134832114.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7326', '48', '百威啤酒', '10.00', '/upload/user/20170928134838390.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7327', '48', '冰镇酸梅汤', '5.00', '/upload/user/20170928134849777.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7328', '48', '菠萝啤', '5.00', '/upload/user/20170928134857611.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7329', '48', '果粒橙（大）', '8.00', '/upload/user/20170928134903652.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7330', '48', '哈啤', '5.00', '/upload/user/20170928134919601.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7331', '48', '加多宝', '5.00', '/upload/user/20170928134927458.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7332', '48', '可口可乐', '5.00', '/upload/user/20170928134933738.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7333', '48', '农夫山泉', '3.00', '/upload/user/20170928134949994.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7334', '48', '青岛啤酒（大）', '10.00', '/upload/user/20170928134938664.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7335', '48', '青岛啤酒15', '5.00', '/upload/user/20170928134943846.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7336', '48', '雪碧', '5.00', '/upload/user/20170928134958943.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7337', '112', '榴莲冰棍', '30.00', '/upload/user/20170928134440614.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7338', '112', '水果冰淇淋（单个）', '30.00', '/upload/user/20170928134446279.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7339', '112', '水果冰淇淋', '30.00', '/upload/user/20170928134451149.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7340', '110', '彩虹慕斯蛋糕', '58.00', '/upload/user/20170928135031154.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7341', '110', '法式芝士蛋糕', '58.00', '/upload/user/20170928135058532.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7342', '110', '慕斯榴莲蛋糕', '58.00', '/upload/user/20170928135106610.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7343', '110', '榴莲千层', '58.00', '/upload/user/20170928135037521.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7344', '110', '抹茶黄桃', '58.00', '/upload/user/20170928135043231.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7345', '110', '提拉米苏', '58.00', '/upload/user/20170928135112162.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7346', '113', '八爪鱼刺身', '55.00', '/upload/user/20170928134307768.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7347', '113', '北极贝刺身', '55.00', '/upload/user/20170928134311747.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7348', '113', '金枪鱼刺身', '55.00', '/upload/user/20170928134317149.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7349', '113', '三文鱼+北极贝双拼', '55.00', '/upload/user/20170928134328256.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7350', '113', '三文鱼刺身', '55.00', '/upload/user/20170928134334266.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7351', '114', '巴黎香蟹', '78.00', '/upload/user/20170928134145708.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7352', '114', '极品大闸蟹', '78.00', '/upload/user/20180403170414517.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7353', '114', '秘制大闸蟹', '78.00', '/upload/user/20170928134234106.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7354', '114', '清蒸大闸蟹', '78.00', '/upload/user/20170928134246505.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7355', '114', '虾兵蟹将', '78.00', '/upload/user/20170928134251726.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7356', '114', '招牌大闸蟹', '78.00', '/upload/user/20170928134256931.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7357', '115', '鲍汁糯米鸡', '15.00', '/upload/user/20170928133922416.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7358', '115', '豆沙包', '15.00', '/upload/user/20170928133927386.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7359', '115', '海绵宝宝小蒸包', '15.00', '/upload/user/20170928133932654.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7360', '115', '卡通蒸包拼盘', '15.00', '/upload/user/20170928133942382.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7361', '115', '台湾蚵仔煎', '15.00', '/upload/user/20170928133947346.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7362', '115', '虾饺', '15.00', '/upload/user/20170928133959663.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7363', '115', '香酥无骨鱼', '15.00', '/upload/user/20170928134003590.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7500', '135', '200g飘柔人参滋养修护洗发露', '11.00', '', '简介内容28', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7496', '135', '200g飘柔垂顺亮泽洗发露', '11.00', '', '简介内容24', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7495', '135', '190g飘柔植物精选系列清凉舒爽薄荷洗发露', '7.00', '', '简介内容23', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7494', '135', '190g飘柔家庭护理杏仁长效柔顺滋养洗发露', '7.00', '', '简介内容22', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7370', '50', '姜汁皮蛋', '10.00', '/upload/user/20170928134411415.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7371', '50', '泡椒小皮蛋', '10.00', '/upload/user/20170928134421532.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7372', '50', '泡椒鱼皮', '10.00', '/upload/user/20170928134427148.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7157', '102', '加加酱油', '5.00', '/upload/pliang/20170926200321569.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7156', '102', '谷锦稻米油', '59.00', '/upload/pliang/20170926200310473.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7152', '102', '纯玉米胚芽油', '25.00', '/upload/pliang/20170926200310473.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7151', '102', '谷锦稻米油', '58.00', '/upload/pliang/20170926200310294.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7150', '104', '番茄400g', '8.00', '/upload/pliang/20170926194303454.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7148', '104', '土豆400g', '15.00', '/upload/pliang/20170926194303131.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7149', '104', '蟹味菇150g', '10.00', '/upload/pliang/20170926194303322.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7147', '104', '白玉菇150g', '18.00', '/upload/pliang/20170926194302395.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7146', '104', '鳕鱼片250g', '28.00', '/upload/pliang/20170926195128671.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7145', '104', '东海小黄鱼12条装', '25.00', '/upload/pliang/20170926194312518.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7144', '104', '鳕鱼片250g', '28.00', '/upload/pliang/20170926195133785.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7143', '104', '鱿鱼花目鱼花300g', '28.00', '/upload/pliang/20170926194311702.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7142', '104', '丹江口野生餐条鲦鱼300g', '38.00', '/upload/pliang/20170926194312518.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7141', '104', '黑毛土猪肉小排 500g', '28.00', '/upload/pliang/20170926194319214.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7140', '104', '黑毛土猪肉方肉 500g', '38.00', '/upload/pliang/20170926194319739.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7137', '104', '黑毛土猪肉肉排 500g', '18.00', '/upload/pliang/20170926194317177.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7138', '104', '黑毛土猪肉大排 500g', '28.00', '/upload/pliang/20170926194319859.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7139', '104', '黑毛土猪肉猪骨 500g', '28.00', '/upload/pliang/20170926194319230.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7286', '110', '椰蓉圈', '13.00', '/upload/user/20170928095920675.jpg', '', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7503', '135', '400g飘柔滋润去屑润发精华素', '18.50', '', '简介内容31', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7407', '124', '鸿福好声音', '288.00', '', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7408', '123', '大世界酒店', '388.00', '', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7409', '123', '逅海主题酒店', '88.00', '', '', '2');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7410', '122', '曼谷皇宫', '138.00', '', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7411', '121', '富桥', '98.00', '', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7412', '120', '富桥', '88.00', '', '', '1');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7416', '124', '畅想ktv', '248.00', '', '', '2');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7509', '135', '200g飘柔垂顺亮泽润发乳', '9.50', '', '简介内容37', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7510', '135', '200g飘柔焗油丝质柔滑润发乳', '9.50', '', '简介内容38', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7511', '135', '200g飘柔人参滋养修护润发乳', '9.50', '', '简介内容39', '0');
INSERT INTO `xiaozu_goodslibrary` VALUES ('7512', '135', '200g飘柔滋润去屑润发精华素', '9.50', '', '简介内容40', '0');

-- ----------------------------
-- Table structure for xiaozu_goodslibrarycate
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_goodslibrarycate`;
CREATE TABLE `xiaozu_goodslibrarycate` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '分类名称',
  `orderid` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=139 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_goodslibrarycate
-- ----------------------------
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('43', '绝味鸭脖', '13');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('111', '贝螺类', '18');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('112', '冰淇淋', '19');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('113', '刺身', '20');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('54', '家庭常备药', '21');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('48', '饮料', '18');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('49', '菜品', '19');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('50', '凉菜系列', '20');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('51', '食品零食', '21');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('106', '水果', '13');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('56', '鲜花类', '1');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('107', '坚果', '14');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('110', '蛋糕 烘焙', '17');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('109', '下午茶', '16');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('108', '零食', '15');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('102', '便利店粮油系列', '17');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('104', '便利店生鲜蔬菜', '19');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('114', '大闸蟹', '21');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('115', '港式点心', '22');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('116', '小龙虾', '23');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('117', '奶茶店类', '21');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('120', '按摩', '24');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('121', '保健', '25');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('122', '桑拿', '26');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('123', '酒店', '27');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('124', 'ktv', '28');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('129', '肉店', '30');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('130', '新鲜果蔬', '31');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('131', '酒水', '32');
INSERT INTO `xiaozu_goodslibrarycate` VALUES ('135', '飘柔', '33');

-- ----------------------------
-- Table structure for xiaozu_goodssign
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_goodssign`;
CREATE TABLE `xiaozu_goodssign` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `imgurl` varchar(255) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL COMMENT 'goods商品,shop店铺,cx促销3类型',
  `instro` varchar(100) DEFAULT NULL COMMENT '说明',
  `typevalue` int(1) NOT NULL DEFAULT '0' COMMENT '0无,1新品，2热门，3推荐',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_goodssign
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_goodstype
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_goodstype`;
CREATE TABLE `xiaozu_goodstype` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `shopid` int(20) NOT NULL COMMENT '店铺ID',
  `name` varchar(50) NOT NULL COMMENT '分类名称',
  `orderid` int(3) NOT NULL DEFAULT '0',
  `cattype` int(1) NOT NULL DEFAULT '1' COMMENT '1外卖 2订台',
  PRIMARY KEY (`id`),
  KEY `shopid` (`shopid`)
) ENGINE=MyISAM AUTO_INCREMENT=1818 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_goodstype
-- ----------------------------
INSERT INTO `xiaozu_goodstype` VALUES ('1817', '1213', '精品小吃', '0', '0');

-- ----------------------------
-- Table structure for xiaozu_group
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_group`;
CREATE TABLE `xiaozu_group` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '用户组名称',
  `type` varchar(100) NOT NULL COMMENT '前台或者后台',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=77 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_group
-- ----------------------------
INSERT INTO `xiaozu_group` VALUES ('1', '超级管理员', 'admin');
INSERT INTO `xiaozu_group` VALUES ('3', '商家会员', 'font');
INSERT INTO `xiaozu_group` VALUES ('5', '普通会员', 'font');

-- ----------------------------
-- Table structure for xiaozu_handsendjuanlog
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_handsendjuanlog`;
CREATE TABLE `xiaozu_handsendjuanlog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `actname` text COMMENT '活动名称',
  `sendrange` int(1) DEFAULT NULL COMMENT '发放范围',
  `sendtime` int(11) DEFAULT NULL COMMENT '发放时间',
  `oneusercount` int(1) DEFAULT NULL COMMENT '每人发放几张',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=118 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_handsendjuanlog
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_helpbuy
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_helpbuy`;
CREATE TABLE `xiaozu_helpbuy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '帮我买物品类型',
  `description` varchar(50) DEFAULT NULL COMMENT '帮我买物品类型描述',
  `imgurl` varchar(200) NOT NULL COMMENT '图片路径',
  `isnotsee` int(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏 1隐藏 0不隐藏',
  `orderid` int(10) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_helpbuy
-- ----------------------------
INSERT INTO `xiaozu_helpbuy` VALUES ('1', '食品', '美味在身旁', 'http://image.ghwmr.com/images/410100/paotui/20180525121312898.png', '0', '1');
INSERT INTO `xiaozu_helpbuy` VALUES ('2', '买烟酒', '适量更怡情', '/upload/goods/20171221194654594.png', '0', '5');
INSERT INTO `xiaozu_helpbuy` VALUES ('3', '买水果', '多吃长不胖', '/upload/goods/20171221194731768.png', '0', '4');
INSERT INTO `xiaozu_helpbuy` VALUES ('4', '买咖啡', '午后好阳光', '/upload/goods/20171221194811324.png', '0', '3');
INSERT INTO `xiaozu_helpbuy` VALUES ('5', '买日用', '宅人便利店', '/upload/goods/20171221194824731.png', '0', '2');
INSERT INTO `xiaozu_helpbuy` VALUES ('6', '买药品', '药到病除', '/upload/goods/20171221194848728.png', '0', '1');

-- ----------------------------
-- Table structure for xiaozu_helpbuybq
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_helpbuybq`;
CREATE TABLE `xiaozu_helpbuybq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT '标签名字',
  `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '所属对应类型的id',
  `orderid` int(10) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=112 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_helpbuybq
-- ----------------------------
INSERT INTO `xiaozu_helpbuybq` VALUES ('4', '帝豪', '2', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('5', '苹果', '3', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('6', '香蕉', '3', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('7', '雀巢', '4', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('8', '牙刷', '5', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('9', '感冒灵', '6', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('10', '西瓜霜', '6', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('74', '瓜子', '1', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('24', '黄鹤楼', '2', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('25', '枝江', '2', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('26', '茅台', '2', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('27', '老村长', '2', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('28', '红旗渠', '2', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('29', '利群', '2', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('30', '江小白', '2', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('35', '葡萄', '3', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('36', '猕猴桃', '3', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('37', '甘蔗', '3', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('38', '橘子', '3', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('39', '西瓜', '3', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('40', '梨子', '3', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('41', '黄桃', '3', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('42', '斤', '3', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('86', '洗发水', '5', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('107', '冰咖啡', '4', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('106', '橙子', '3', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('47', '拿铁', '4', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('48', '牙膏', '5', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('49', '皮炎平', '6', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('50', '泻立停', '6', '1');
INSERT INTO `xiaozu_helpbuybq` VALUES ('73', '油条', '1', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('96', '送达大声道', '1', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('87', '沐浴露', '5', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('83', '盒', '1', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('82', '份', '1', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('81', '两', '1', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('80', '斤', '1', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('88', '香皂', '5', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('89', '盒', '5', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('90', '瓶', '5', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('91', '小柴胡', '6', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('92', '周黑鸭', '1', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('94', '牛奶/包', '1', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('109', '调料', '1', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('110', '洗面奶', '5', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('102', '猫屎', '4', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('104', '测试', '4', null);
INSERT INTO `xiaozu_helpbuybq` VALUES ('111', '甘草片', '6', null);

-- ----------------------------
-- Table structure for xiaozu_helpcenter
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_helpcenter`;
CREATE TABLE `xiaozu_helpcenter` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `addtime` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `orderid` int(5) NOT NULL,
  `seo_key` varchar(255) DEFAULT NULL,
  `seo_content` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_helpcenter
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_helpmove
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_helpmove`;
CREATE TABLE `xiaozu_helpmove` (
  `id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '帮我买物品类型',
  `imgurl` varchar(200) NOT NULL COMMENT '图片路径',
  `isnotsee` int(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏 1隐藏 0不隐藏',
  `orderid` int(10) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_helpmove
-- ----------------------------
INSERT INTO `xiaozu_helpmove` VALUES ('1', '鲜花蛋糕', 'http://image.ghwmr.com/images/410100/paotui/20180525153241136.png', '0', '1');
INSERT INTO `xiaozu_helpmove` VALUES ('2', '餐饮', '/upload/goods/20171221195154580.png', '0', '2');
INSERT INTO `xiaozu_helpmove` VALUES ('3', '果蔬生鲜', '/upload/goods/20171221195212319.png', '0', '3');
INSERT INTO `xiaozu_helpmove` VALUES ('4', '文件', '/upload/goods/20171221195234390.png', '0', '4');
INSERT INTO `xiaozu_helpmove` VALUES ('5', '电子产品', '/upload/goods/20171221195247990.png', '1', '5');
INSERT INTO `xiaozu_helpmove` VALUES ('6', '钥匙', '/upload/goods/20171221195301808.png', '1', '6');
INSERT INTO `xiaozu_helpmove` VALUES ('7', '服饰', '/upload/goods/20171221195311766.png', '1', '7');
INSERT INTO `xiaozu_helpmove` VALUES ('8', '其他', '/upload/goods/20171221195327306.png', '1', '1');

-- ----------------------------
-- Table structure for xiaozu_imglist
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_imglist`;
CREATE TABLE `xiaozu_imglist` (
  `imagename` varchar(255) DEFAULT NULL,
  `imageurl` varchar(255) DEFAULT NULL,
  `addtime` int(12) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_imglist
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_information
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_information`;
CREATE TABLE `xiaozu_information` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `img` varchar(255) NOT NULL,
  `addtime` int(11) NOT NULL,
  `orderid` mediumint(11) NOT NULL,
  `describe` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `phone` varchar(15) NOT NULL COMMENT '电话-生活助手页面用-type为2时候',
  `type` int(1) NOT NULL COMMENT 'type 1为网站通知 2为生活助手',
  `cityid` int(12) NOT NULL COMMENT '城市ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=200 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_information
-- ----------------------------
INSERT INTO `xiaozu_information` VALUES ('199', '欢迎订餐！欢迎订餐！欢迎订餐！', '/images/410100/news/20180623195920218.png', '1529755179', '1', '', '欢迎订餐！欢迎订餐！欢迎订餐！', '', '1', '410100');

-- ----------------------------
-- Table structure for xiaozu_jscompute
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_jscompute`;
CREATE TABLE `xiaozu_jscompute` (
  `id` int(2) NOT NULL,
  `type` tinytext COMMENT '公式类型',
  `pscost` int(2) DEFAULT '0' COMMENT '是否加配送费',
  `bagcost` int(2) DEFAULT '0' COMMENT '是否加打包费',
  `shopdowncost` int(2) DEFAULT '0' COMMENT '是否减促销中商家承担的部分',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_jscompute
-- ----------------------------
INSERT INTO `xiaozu_jscompute` VALUES ('1', '平台配送佣金', '0', '1', '1');
INSERT INTO `xiaozu_jscompute` VALUES ('2', '商家配送佣金', '1', '1', '1');
INSERT INTO `xiaozu_jscompute` VALUES ('3', '平台配送结算', '0', '1', '1');
INSERT INTO `xiaozu_jscompute` VALUES ('4', '商家配送结算', '0', '1', '1');

-- ----------------------------
-- Table structure for xiaozu_juan
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_juan`;
CREATE TABLE `xiaozu_juan` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `card` varchar(20) NOT NULL COMMENT '优惠劵卡号',
  `card_password` varchar(10) NOT NULL COMMENT '优惠劵密码',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态，0未使用，1已绑定，2已使用，3无效',
  `creattime` int(11) NOT NULL COMMENT '制造时间',
  `cost` int(5) NOT NULL COMMENT '优惠金额',
  `limitcost` int(5) NOT NULL COMMENT '购物车限制金额下限',
  `endtime` int(11) NOT NULL DEFAULT '0' COMMENT '失效时间',
  `uid` int(20) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `username` varchar(100) NOT NULL DEFAULT '0' COMMENT '用户名',
  `usetime` int(11) NOT NULL DEFAULT '0' COMMENT '使用时间',
  `name` varchar(50) NOT NULL DEFAULT '优惠劵',
  `shareuid` int(11) NOT NULL COMMENT '分享者UID',
  `paytype` varchar(20) DEFAULT NULL COMMENT '(1,2) 1支持货到付款2支持在线支付',
  `type` int(1) DEFAULT NULL COMMENT '1充值2下单3推广',
  `orderid` int(11) DEFAULT NULL,
  `bangphone` varchar(20) DEFAULT NULL COMMENT '绑定手机号',
  `spotordtype` text COMMENT '支持订单类型：1外卖2超市3跑腿',
  `actid` int(10) DEFAULT NULL COMMENT '手动发放优惠券活动id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=533506 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_juan
-- ----------------------------
INSERT INTO `xiaozu_juan` VALUES ('533504', '254', 'c52f1', '1', '1529750563', '5', '20', '1529769599', '22052', 'guanghe', '0', '注册送优惠券', '0', '1,2', '2', null, null, null, null);
INSERT INTO `xiaozu_juan` VALUES ('533505', '478', 'cfee3', '1', '1529751286', '5', '20', '1529769599', '22053', 'wmr', '0', '注册送优惠券', '0', '1,2', '2', null, null, null, null);

-- ----------------------------
-- Table structure for xiaozu_juanrule
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_juanrule`;
CREATE TABLE `xiaozu_juanrule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `type` int(1) NOT NULL COMMENT '营销类型 1充值  2下单成功分享优惠券  3会员中心推广优惠券',
  `juantotalcost` decimal(12,2) DEFAULT NULL COMMENT '充值赠送优惠券总金额',
  `juannum` int(11) NOT NULL COMMENT '送多少张优惠券(type3固定为1张)',
  `jiancostmin` decimal(8,2) NOT NULL COMMENT '满---最小',
  `jiancostmax` decimal(8,2) NOT NULL COMMENT '满---最大',
  `jiacostmin` decimal(8,2) NOT NULL COMMENT '减---最小',
  `jiacostmax` decimal(8,2) NOT NULL COMMENT '减---最大',
  `endtime` int(11) NOT NULL COMMENT '失效时间',
  `paytype` varchar(20) DEFAULT NULL COMMENT '(0,1) 0支持货到付款1支持在线支付',
  `is_open` int(1) NOT NULL DEFAULT '1' COMMENT '是否启用默认1开启',
  `addtime` int(11) NOT NULL COMMENT '添加时间',
  `orderid` int(11) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='分享优惠券规则类型';

-- ----------------------------
-- Records of xiaozu_juanrule
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_juanshowinfo
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_juanshowinfo`;
CREATE TABLE `xiaozu_juanshowinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(1) NOT NULL COMMENT '类型 2为下单分享 3为推广分享',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `img` varchar(255) NOT NULL COMMENT '分享的图标',
  `describe` varchar(255) NOT NULL COMMENT '简述',
  `bigimg` varchar(255) NOT NULL COMMENT '展示大图',
  `color` varchar(10) NOT NULL COMMENT '背景色调',
  `actcolor` varchar(10) NOT NULL COMMENT '活动规则背景色调',
  `avtrule` text NOT NULL COMMENT '活动规则',
  `orderid` int(11) NOT NULL,
  `addtime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='分享优惠券展示信息';

-- ----------------------------
-- Records of xiaozu_juanshowinfo
-- ----------------------------
INSERT INTO `xiaozu_juanshowinfo` VALUES ('1', '2', '外卖人发红包啦，快去抢抢看~', 'http://image.ghwmr.com/images/410100/cx/20180525165004894.png', '外卖人发红包啦，外卖神器~~抢抢抢！！！', 'http://image.ghwmr.com/images/410100/cx/20180525165038476.png', '#f55f0d', '#f88e56', '<span style=\"color:#ffffff;font-size:12px;\">1.红包新老用户同享</span><br>\n<p><span style=\"color:#ffffff;font-size:12px;\">2.红包可与其他优惠叠加使用，首单支付红包不可叠加</span></p>\n<p><span style=\"color:#ffffff;font-size:12px;\">3.红包仅限在外卖人最新版客户端下单且选择在线支付时使用</span></p>\n<p><span style=\"color:#ffffff;font-size:12px;\">4.使用红包时dasasadsas下单手机号码必须为抢红包时手机号码</span></p>\n<p><span style=\"color:#ffffff;font-size:12px;\">5.其他未尽事宜，请咨询客服</span></p>\n<p><span style=\"color:#ffffff;font-size:12px;\">6.红包解释权归官方网站所有</span></p>', '1', '1464693859');
INSERT INTO `xiaozu_juanshowinfo` VALUES ('2', '3', '会员推广分享优惠券领取优惠券红包啦！！！', '/upload/juan/20171204180025219.jpg', '会员推广分享优惠券领取优惠券红包啦，分享给好友，还有下单后分享者还可以返优惠券！！！', '/upload/app/20171011184412621.png', '#3a5a07', '#31b47f', '<span style=\"color: rgb(229, 51, 51); font-size: 12px;\">1.邀请新用户注册，TA立刻获得30元优惠券礼包。</span><br><span style=\"color: rgb(229, 51, 51); font-size: 12px;\">2.TA完成首单消费的24小时内，您也获得30元优惠券礼包。</span><br><span style=\"color: rgb(229, 51, 51); font-size: 12px;\">3.分享邀请链接给好友，让TA填写手机号码领取。微信端需要在会员中心绑定手机号码才能获得优惠券礼包。</span><br><span style=\"color: rgb(229, 51, 51); font-size: 12px;\">4.同一手机号、同一手机设备、同一支付账户均视为同一用户，新注册的用户仅限成功领取一次优惠礼包。</span><br><br><ul>\n</ul>\n<ul>\n				</ul>', '2', '1464754342');
INSERT INTO `xiaozu_juanshowinfo` VALUES ('3', '1', '关注微信领取优惠券', '/upload/juan/20160715165220312.png', '首次关注微信领取优惠券', 'http://image.ghwmr.com/images/410100/cx/20180525164548554.png', '#e4252a', '#e2fde6', '<p><br></p><p><br></p>', '3', '1468572770');

-- ----------------------------
-- Table structure for xiaozu_locationpsy
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_locationpsy`;
CREATE TABLE `xiaozu_locationpsy` (
  `uid` int(20) NOT NULL,
  `lat` varchar(20) NOT NULL,
  `lng` varchar(20) NOT NULL,
  `addtime` int(12) NOT NULL,
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_locationpsy
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_marketcate
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_marketcate`;
CREATE TABLE `xiaozu_marketcate` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '目录名称',
  `keywd` varchar(50) DEFAULT NULL COMMENT '关键字',
  `desc` varchar(255) DEFAULT NULL COMMENT '描述',
  `parent_id` int(20) NOT NULL DEFAULT '0' COMMENT '上级ID  0为1级目录',
  `shopid` int(20) DEFAULT NULL,
  `orderid` int(5) NOT NULL DEFAULT '999',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1492 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_marketcate
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_member
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_member`;
CREATE TABLE `xiaozu_member` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `phone` varchar(13) NOT NULL,
  `address` varchar(250) NOT NULL,
  `creattime` int(11) NOT NULL,
  `logintime` int(11) NOT NULL,
  `usertype` int(1) NOT NULL COMMENT '0.普通会员，1开店商家',
  `score` int(5) NOT NULL DEFAULT '0' COMMENT '积分',
  `cost` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '账号余额',
  `loginip` varchar(30) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '账号是否可用',
  `safepwd` varchar(100) DEFAULT NULL COMMENT '支付密码',
  `group` int(2) DEFAULT '5',
  `is_bang` int(1) NOT NULL DEFAULT '0',
  `parent_id` int(20) DEFAULT '0',
  `total` int(6) DEFAULT '0',
  `admin_id` int(20) NOT NULL DEFAULT '0',
  `sex` int(11) NOT NULL,
  `wxopenid` text NOT NULL,
  `temp_password` varchar(50) DEFAULT NULL,
  `shopid` int(11) NOT NULL DEFAULT '0',
  `shopcost` decimal(8,2) DEFAULT '0.00',
  `backacount` varchar(100) DEFAULT NULL,
  `md_flag` int(1) NOT NULL DEFAULT '0' COMMENT '分钟数',
  `befxtime` int(11) DEFAULT NULL COMMENT '成为分销会员时间',
  `fxcost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fxpid` int(12) NOT NULL DEFAULT '0',
  `fxcode` text,
  `invitecode` int(6) DEFAULT NULL COMMENT '邀请码',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=22054 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_member
-- ----------------------------
INSERT INTO `xiaozu_member` VALUES ('22052', 'e10adc3949ba59abbe56e057f20f883e', 'guanghe@qq.com', 'guanghe', '15236985214', '', '1529750563', '1529750563', '0', '10', '0.00', '127.0.0.1', '', '0', null, '3', '0', '0', '0', '410100', '0', '', '123456', '0', '0.00', null, '0', null, '0.00', '0', null, null);
INSERT INTO `xiaozu_member` VALUES ('22053', 'e10adc3949ba59abbe56e057f20f883e', '13241654@qq.com', 'wmr', '15038888888', '河南省电子商务产业园', '1529751286', '1529753628', '0', '180', '85.00', '127.0.0.1', '', '0', null, '5', '0', '0', '0', '0', '0', '', '123456', '0', '0.00', null, '0', null, '0.00', '0', '/images/user/wxcode/1529753025.png', '551565');

-- ----------------------------
-- Table structure for xiaozu_memberlog
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_memberlog`;
CREATE TABLE `xiaozu_memberlog` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `userid` int(20) NOT NULL,
  `type` int(1) NOT NULL DEFAULT '0' COMMENT '1积分/2资金',
  `addtype` int(1) NOT NULL DEFAULT '0' COMMENT '1增加2减少',
  `result` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '变动积分/金额数量',
  `addtime` int(12) NOT NULL DEFAULT '0',
  `content` varchar(255) DEFAULT NULL COMMENT '描述',
  `title` varchar(255) DEFAULT NULL,
  `acount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '账户总金额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31203 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_memberlog
-- ----------------------------
INSERT INTO `xiaozu_memberlog` VALUES ('31199', '22052', '1', '1', '10.00', '1529750563', '注册送积分10', '注册送积分', '10.00');
INSERT INTO `xiaozu_memberlog` VALUES ('31200', '22053', '1', '1', '100.00', '1529751286', '注册送积分100', '注册送积分', '100.00');
INSERT INTO `xiaozu_memberlog` VALUES ('31201', '22053', '1', '1', '80.00', '1529751299', '用户登陆赠送积分80总积分180', '每天登陆', '180.00');
INSERT INTO `xiaozu_memberlog` VALUES ('31202', '22053', '2', '2', '15.00', '1529754247', '支付订单15297541264635帐号金额减少15.00元', '余额支付订单', '85.00');

-- ----------------------------
-- Table structure for xiaozu_memcostlog
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_memcostlog`;
CREATE TABLE `xiaozu_memcostlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户uid',
  `username` varchar(30) NOT NULL COMMENT '用户名',
  `cost` decimal(10,2) NOT NULL COMMENT '剩余金额',
  `bdtype` int(1) NOT NULL COMMENT '变动类型 1为增加 2为减少',
  `bdcost` decimal(10,2) NOT NULL COMMENT '本次充值/扣除金额',
  `curcost` decimal(10,2) NOT NULL COMMENT '当前金额',
  `bdliyou` varchar(255) NOT NULL COMMENT '变动原因',
  `czuid` int(11) NOT NULL COMMENT '操作用户uid',
  `czusername` varchar(30) NOT NULL COMMENT '操作用户名称',
  `addtime` int(11) NOT NULL COMMENT '操作日期',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12521 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_memcostlog
-- ----------------------------
INSERT INTO `xiaozu_memcostlog` VALUES ('12520', '22053', 'wmr', '100.00', '2', '15.00', '85.00', '下单余额消费', '22053', 'wmr', '1529754247');

-- ----------------------------
-- Table structure for xiaozu_menu
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_menu`;
CREATE TABLE `xiaozu_menu` (
  `name` varchar(100) NOT NULL COMMENT '操作名称',
  `cnname` varchar(200) NOT NULL,
  `moduleid` int(20) NOT NULL,
  `group` int(20) NOT NULL,
  `id` int(5) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_menu
-- ----------------------------
INSERT INTO `xiaozu_menu` VALUES ('memcostloglist', '会员余额操作日志', '2', '19', '3');
INSERT INTO `xiaozu_menu` VALUES ('shop', '店铺销量分析', '18', '6', '0');
INSERT INTO `xiaozu_menu` VALUES ('orderyjin', '商家结算', '5', '6', '0');
INSERT INTO `xiaozu_menu` VALUES ('newslist', '新闻列表', '7', '6', '0');
INSERT INTO `xiaozu_menu` VALUES ('newslist', '新闻列表', '7', '7', '0');
INSERT INTO `xiaozu_menu` VALUES ('newstype', '新闻分类', '7', '7', '1');
INSERT INTO `xiaozu_menu` VALUES ('ordertoday', '当天订单处理', '5', '12', '0');
INSERT INTO `xiaozu_menu` VALUES ('glywxmsg', '微信端管理员发布置顶留言', '28', '4', '5');
INSERT INTO `xiaozu_menu` VALUES ('memberlistshop', '商家会员列表', '2', '19', '2');
INSERT INTO `xiaozu_menu` VALUES ('memberlist', '普通会员列表', '2', '19', '1');
INSERT INTO `xiaozu_menu` VALUES ('adminlist', '管理员列表', '2', '19', '0');
INSERT INTO `xiaozu_menu` VALUES ('modullist', '模块管理', '1', '19', '7');
INSERT INTO `xiaozu_menu` VALUES ('toplink', '网站导航', '1', '19', '5');
INSERT INTO `xiaozu_menu` VALUES ('footinfo', '网站底部', '1', '19', '6');
INSERT INTO `xiaozu_menu` VALUES ('sitebk', '网站水印', '1', '19', '4');
INSERT INTO `xiaozu_menu` VALUES ('tempset', '模板设置', '1', '19', '3');
INSERT INTO `xiaozu_menu` VALUES ('otherset', '网站限制', '1', '19', '2');
INSERT INTO `xiaozu_menu` VALUES ('index', '网站信息', '1', '19', '0');
INSERT INTO `xiaozu_menu` VALUES ('siteset', '网站设置', '1', '19', '1');
INSERT INTO `xiaozu_menu` VALUES ('psymap', '配送员订单', '32', '4', '1');
INSERT INTO `xiaozu_menu` VALUES ('memberlistps', '配送员管理', '32', '4', '0');
INSERT INTO `xiaozu_menu` VALUES ('wxuser', '微信用户', '28', '4', '3');
INSERT INTO `xiaozu_menu` VALUES ('yiqisaylist', '微信端一起说留言管理', '28', '4', '4');
INSERT INTO `xiaozu_menu` VALUES ('wxback', '微信自动回复', '28', '4', '2');
INSERT INTO `xiaozu_menu` VALUES ('wxmenu', '微信菜单', '28', '4', '1');
INSERT INTO `xiaozu_menu` VALUES ('wxset', '微信基本设置', '28', '4', '0');
INSERT INTO `xiaozu_menu` VALUES ('advlist', '广告列表', '14', '4', '0');
INSERT INTO `xiaozu_menu` VALUES ('advtype', '广告类型', '14', '4', '1');
INSERT INTO `xiaozu_menu` VALUES ('shoptx', '店铺提现记录', '5', '47', '11');
INSERT INTO `xiaozu_menu` VALUES ('shopjsadd', '店铺结算处理', '5', '47', '10');
INSERT INTO `xiaozu_menu` VALUES ('ordercomment', '订单评价列表', '5', '47', '9');
INSERT INTO `xiaozu_menu` VALUES ('logoset', 'logo设置', '1', '55', '2');
INSERT INTO `xiaozu_menu` VALUES ('tjshophui', '闪惠订单统计', '5', '18', '7');
INSERT INTO `xiaozu_menu` VALUES ('shophuiorder', '闪惠订单', '5', '18', '6');
INSERT INTO `xiaozu_menu` VALUES ('ordertotal', '订单统计', '5', '18', '5');
INSERT INTO `xiaozu_menu` VALUES ('setpaotui', '跑腿信息设置', '5', '18', '4');
INSERT INTO `xiaozu_menu` VALUES ('drawsmlist', '退款原因说明', '5', '18', '3');
INSERT INTO `xiaozu_menu` VALUES ('drawbacklog', '退款申请处理', '5', '18', '2');
INSERT INTO `xiaozu_menu` VALUES ('ordertoday', '当天订单处理', '5', '18', '1');
INSERT INTO `xiaozu_menu` VALUES ('orderlist', '所有订单', '5', '18', '0');
INSERT INTO `xiaozu_menu` VALUES ('user', '会员购买数据', '18', '18', '3');
INSERT INTO `xiaozu_menu` VALUES ('goods', '菜品销量分析', '18', '18', '2');
INSERT INTO `xiaozu_menu` VALUES ('shop', '店铺销量分析', '18', '18', '0');
INSERT INTO `xiaozu_menu` VALUES ('area', '地区销量分析', '18', '18', '1');
INSERT INTO `xiaozu_menu` VALUES ('adminshoplist', '后台商家列表', '3', '18', '0');
INSERT INTO `xiaozu_menu` VALUES ('memberlistshop', '商家会员列表', '2', '18', '1');
INSERT INTO `xiaozu_menu` VALUES ('memberlist', '普通会员列表', '2', '18', '0');
INSERT INTO `xiaozu_menu` VALUES ('areadtoji', '区域管理统计', '5', '18', '8');
INSERT INTO `xiaozu_menu` VALUES ('orderyjin', '商家结算', '5', '18', '9');
INSERT INTO `xiaozu_menu` VALUES ('beizhulist', '订单备注', '5', '18', '10');
INSERT INTO `xiaozu_menu` VALUES ('ordercomment', '订单评价列表', '5', '18', '11');
INSERT INTO `xiaozu_menu` VALUES ('adminfastfoods', '后台快速下单', '5', '18', '12');
INSERT INTO `xiaozu_menu` VALUES ('appset', 'app设置', '34', '18', '0');
INSERT INTO `xiaozu_menu` VALUES ('scoreset', '积分设置', '16', '4', '9');
INSERT INTO `xiaozu_menu` VALUES ('prensentjuan', '赠送卷设置', '16', '4', '10');
INSERT INTO `xiaozu_menu` VALUES ('addjuan', '添加优惠券', '16', '4', '8');
INSERT INTO `xiaozu_menu` VALUES ('siteset', '网站设置', '1', '55', '1');
INSERT INTO `xiaozu_menu` VALUES ('juanlist', '优惠券列表', '16', '4', '7');
INSERT INTO `xiaozu_menu` VALUES ('addcard', '添加充值卡', '16', '4', '6');
INSERT INTO `xiaozu_menu` VALUES ('cardlist', '充值卡列表', '16', '4', '5');
INSERT INTO `xiaozu_menu` VALUES ('addmember', '添加会员', '2', '19', '4');
INSERT INTO `xiaozu_menu` VALUES ('adminshoplist', '后台商家列表', '3', '19', '0');
INSERT INTO `xiaozu_menu` VALUES ('adminshopwati', '后台待审核商家', '3', '19', '1');
INSERT INTO `xiaozu_menu` VALUES ('addshop', '后台添加店铺', '3', '19', '2');
INSERT INTO `xiaozu_menu` VALUES ('shoptype', '后台模型', '3', '19', '3');
INSERT INTO `xiaozu_menu` VALUES ('goodssign', '促销商品标签', '3', '19', '4');
INSERT INTO `xiaozu_menu` VALUES ('goodsattr', '商品属性', '3', '19', '5');
INSERT INTO `xiaozu_menu` VALUES ('goodsgg', '商品规格', '3', '19', '6');
INSERT INTO `xiaozu_menu` VALUES ('searchattr', '搜索关键词', '3', '19', '7');
INSERT INTO `xiaozu_menu` VALUES ('orderlist', '所有订单', '5', '19', '0');
INSERT INTO `xiaozu_menu` VALUES ('ordertoday', '当天订单处理', '5', '19', '1');
INSERT INTO `xiaozu_menu` VALUES ('drawbacklog', '退款申请处理', '5', '19', '2');
INSERT INTO `xiaozu_menu` VALUES ('drawsmlist', '退款原因说明', '5', '19', '3');
INSERT INTO `xiaozu_menu` VALUES ('setpaotui', '跑腿信息设置', '5', '19', '4');
INSERT INTO `xiaozu_menu` VALUES ('ordertotal', '订单统计', '5', '19', '5');
INSERT INTO `xiaozu_menu` VALUES ('shophuiorder', '闪惠订单', '5', '19', '6');
INSERT INTO `xiaozu_menu` VALUES ('tjshophui', '闪惠订单统计', '5', '19', '7');
INSERT INTO `xiaozu_menu` VALUES ('areadtoji', '区域管理统计', '5', '19', '8');
INSERT INTO `xiaozu_menu` VALUES ('orderyjin', '商家结算', '5', '19', '9');
INSERT INTO `xiaozu_menu` VALUES ('beizhulist', '订单备注', '5', '19', '10');
INSERT INTO `xiaozu_menu` VALUES ('ordercomment', '订单评价列表', '5', '19', '11');
INSERT INTO `xiaozu_menu` VALUES ('adminfastfoods', '后台快速下单', '5', '19', '12');
INSERT INTO `xiaozu_menu` VALUES ('cardlist', '充值卡列表', '16', '19', '0');
INSERT INTO `xiaozu_menu` VALUES ('addcard', '添加充值卡', '16', '19', '1');
INSERT INTO `xiaozu_menu` VALUES ('juanlist', '优惠券列表', '16', '19', '2');
INSERT INTO `xiaozu_menu` VALUES ('addjuan', '添加优惠券', '16', '19', '3');
INSERT INTO `xiaozu_menu` VALUES ('scoreset', '积分设置', '16', '19', '4');
INSERT INTO `xiaozu_menu` VALUES ('prensentjuan', '赠送卷设置', '16', '19', '5');
INSERT INTO `xiaozu_menu` VALUES ('sendtasklist', '群发任务', '16', '19', '6');
INSERT INTO `xiaozu_menu` VALUES ('sendtask', '发布群发任务', '16', '19', '7');
INSERT INTO `xiaozu_menu` VALUES ('index', '网站信息', '1', '55', '0');
INSERT INTO `xiaozu_menu` VALUES ('receivejuanlog', '优惠券领取记录列表', '16', '4', '4');
INSERT INTO `xiaozu_menu` VALUES ('ordercomment', '订单评价列表', '5', '24', '11');
INSERT INTO `xiaozu_menu` VALUES ('beizhulist', '订单备注', '5', '24', '10');
INSERT INTO `xiaozu_menu` VALUES ('orderyjin', '商家结算', '5', '24', '9');
INSERT INTO `xiaozu_menu` VALUES ('areadtoji', '区域管理统计', '5', '24', '8');
INSERT INTO `xiaozu_menu` VALUES ('tjshophui', '闪惠订单统计', '5', '24', '7');
INSERT INTO `xiaozu_menu` VALUES ('shophuiorder', '闪惠订单', '5', '24', '6');
INSERT INTO `xiaozu_menu` VALUES ('ordertotal', '订单统计', '5', '24', '5');
INSERT INTO `xiaozu_menu` VALUES ('setpaotui', '跑腿信息设置', '5', '24', '4');
INSERT INTO `xiaozu_menu` VALUES ('drawsmlist', '退款原因说明', '5', '24', '3');
INSERT INTO `xiaozu_menu` VALUES ('drawbacklog', '退款申请处理', '5', '24', '2');
INSERT INTO `xiaozu_menu` VALUES ('ordertoday', '当天订单处理', '5', '24', '1');
INSERT INTO `xiaozu_menu` VALUES ('orderlist', '所有订单', '5', '24', '0');
INSERT INTO `xiaozu_menu` VALUES ('adminfastfoods', '后台快速下单', '5', '24', '12');
INSERT INTO `xiaozu_menu` VALUES ('adminarealist', '后台区域列表', '10', '24', '0');
INSERT INTO `xiaozu_menu` VALUES ('addarealist', '后台添加购区域', '10', '24', '1');
INSERT INTO `xiaozu_menu` VALUES ('memberlistps', '配送员管理', '32', '24', '0');
INSERT INTO `xiaozu_menu` VALUES ('psymap', '配送员订单', '32', '24', '1');
INSERT INTO `xiaozu_menu` VALUES ('psapp', 'app配送员', '34', '24', '0');
INSERT INTO `xiaozu_menu` VALUES ('shoptx', '店铺提现记录', '5', '61', '3');
INSERT INTO `xiaozu_menu` VALUES ('shopjsadd', '店铺结算处理', '5', '61', '2');
INSERT INTO `xiaozu_menu` VALUES ('drawsmlist', '退款原因说明', '5', '61', '1');
INSERT INTO `xiaozu_menu` VALUES ('drawbacklog', '退款申请处理', '5', '61', '0');
INSERT INTO `xiaozu_menu` VALUES ('drawbacklog', '退款申请处理', '5', '63', '2');
INSERT INTO `xiaozu_menu` VALUES ('ordertoday', '当天订单处理', '5', '63', '1');
INSERT INTO `xiaozu_menu` VALUES ('orderlist', '所有订单', '5', '63', '0');
INSERT INTO `xiaozu_menu` VALUES ('shoptxtlog', '店铺资金记录', '5', '65', '4');
INSERT INTO `xiaozu_menu` VALUES ('shoptx', '店铺提现记录', '5', '65', '3');
INSERT INTO `xiaozu_menu` VALUES ('shopjsadd', '店铺结算处理', '5', '65', '2');
INSERT INTO `xiaozu_menu` VALUES ('drawsmlist', '退款原因说明', '5', '65', '1');
INSERT INTO `xiaozu_menu` VALUES ('drawbacklog', '退款申请处理', '5', '65', '0');
INSERT INTO `xiaozu_menu` VALUES ('sharejsinfo', '分享优惠券展示信息列表', '16', '4', '3');
INSERT INTO `xiaozu_menu` VALUES ('rechargezend', '充值余额送列表', '16', '4', '2');
INSERT INTO `xiaozu_menu` VALUES ('juanopenset', '优惠券设置', '16', '20', '0');
INSERT INTO `xiaozu_menu` VALUES ('juanmarketing', '营销分享优惠券列表', '16', '20', '1');
INSERT INTO `xiaozu_menu` VALUES ('rechargezend', '充值余额送列表', '16', '20', '2');
INSERT INTO `xiaozu_menu` VALUES ('sharejsinfo', '分享优惠券展示信息列表', '16', '20', '3');
INSERT INTO `xiaozu_menu` VALUES ('virtualinfo', '店铺刷单', '16', '20', '4');
INSERT INTO `xiaozu_menu` VALUES ('receivejuanlog', '优惠券领取记录列表', '16', '20', '5');
INSERT INTO `xiaozu_menu` VALUES ('cardlist', '充值卡列表', '16', '20', '6');
INSERT INTO `xiaozu_menu` VALUES ('addcard', '添加充值卡', '16', '20', '7');
INSERT INTO `xiaozu_menu` VALUES ('juanlist', '优惠券列表', '16', '20', '8');
INSERT INTO `xiaozu_menu` VALUES ('addjuan', '添加优惠券', '16', '20', '9');
INSERT INTO `xiaozu_menu` VALUES ('scoreset', '积分设置', '16', '20', '10');
INSERT INTO `xiaozu_menu` VALUES ('prensentjuan', '赠送卷设置', '16', '20', '11');
INSERT INTO `xiaozu_menu` VALUES ('sendtasklist', '群发任务', '16', '20', '12');
INSERT INTO `xiaozu_menu` VALUES ('sendtask', '发布群发任务', '16', '20', '13');
INSERT INTO `xiaozu_menu` VALUES ('adminshoplist', '后台商家列表', '3', '33', '0');
INSERT INTO `xiaozu_menu` VALUES ('adminshopwati', '后台待审核商家', '3', '33', '1');
INSERT INTO `xiaozu_menu` VALUES ('addshop', '后台添加店铺', '3', '33', '2');
INSERT INTO `xiaozu_menu` VALUES ('shoptype', '后台模型', '3', '33', '3');
INSERT INTO `xiaozu_menu` VALUES ('goodssign', '促销商品标签', '3', '33', '4');
INSERT INTO `xiaozu_menu` VALUES ('goodsattr', '商品属性', '3', '33', '5');
INSERT INTO `xiaozu_menu` VALUES ('goodsgg', '商品规格', '3', '33', '6');
INSERT INTO `xiaozu_menu` VALUES ('searchattr', '搜索关键词', '3', '33', '7');
INSERT INTO `xiaozu_menu` VALUES ('orderlist', '所有订单', '5', '34', '0');
INSERT INTO `xiaozu_menu` VALUES ('siteset', '网站设置', '1', '35', '0');
INSERT INTO `xiaozu_menu` VALUES ('otherset', '网站限制', '1', '35', '1');
INSERT INTO `xiaozu_menu` VALUES ('addadmin', '添加管理员', '2', '35', '0');
INSERT INTO `xiaozu_menu` VALUES ('adminshoplist', '后台商家列表', '3', '35', '0');
INSERT INTO `xiaozu_menu` VALUES ('adminshopwati', '后台待审核商家', '3', '35', '1');
INSERT INTO `xiaozu_menu` VALUES ('addshop', '后台添加店铺', '3', '35', '2');
INSERT INTO `xiaozu_menu` VALUES ('shoptype', '后台模型', '3', '35', '3');
INSERT INTO `xiaozu_menu` VALUES ('goodssign', '促销商品标签', '3', '35', '4');
INSERT INTO `xiaozu_menu` VALUES ('goodsattr', '商品属性', '3', '35', '5');
INSERT INTO `xiaozu_menu` VALUES ('goodsgg', '商品规格', '3', '35', '6');
INSERT INTO `xiaozu_menu` VALUES ('searchattr', '搜索关键词', '3', '35', '7');
INSERT INTO `xiaozu_menu` VALUES ('area', '地区销量分析', '18', '35', '1');
INSERT INTO `xiaozu_menu` VALUES ('shop', '店铺销量分析', '18', '35', '0');
INSERT INTO `xiaozu_menu` VALUES ('goods', '菜品销量分析', '18', '35', '2');
INSERT INTO `xiaozu_menu` VALUES ('user', '会员购买数据', '18', '35', '3');
INSERT INTO `xiaozu_menu` VALUES ('orderlist', '所有订单', '5', '35', '0');
INSERT INTO `xiaozu_menu` VALUES ('juanopenset', '优惠券设置', '16', '4', '0');
INSERT INTO `xiaozu_menu` VALUES ('juanmarketing', '营销分享优惠券列表', '16', '4', '1');
INSERT INTO `xiaozu_menu` VALUES ('complaintlist', '投诉管理列表', '11', '4', '2');
INSERT INTO `xiaozu_menu` VALUES ('singlelist', '单页列表', '12', '4', '0');
INSERT INTO `xiaozu_menu` VALUES ('complreasonlist', '投诉原因列表', '11', '4', '1');
INSERT INTO `xiaozu_menu` VALUES ('adminpsset', '网站配送设置', '10', '4', '0');
INSERT INTO `xiaozu_menu` VALUES ('asklist', '后台留言列表', '11', '4', '0');
INSERT INTO `xiaozu_menu` VALUES ('errlog', '错误日志', '17', '4', '6');
INSERT INTO `xiaozu_menu` VALUES ('openset', '整站开关店铺', '17', '4', '7');
INSERT INTO `xiaozu_menu` VALUES ('cleartpl', '清理缓存', '17', '4', '5');
INSERT INTO `xiaozu_menu` VALUES ('smsset', '短信设置', '17', '4', '4');
INSERT INTO `xiaozu_menu` VALUES ('buyermsg', 'app用户群发', '34', '38', '2');
INSERT INTO `xiaozu_menu` VALUES ('appset', 'app设置', '34', '38', '0');
INSERT INTO `xiaozu_menu` VALUES ('buyerapp', 'app用户端', '34', '38', '1');
INSERT INTO `xiaozu_menu` VALUES ('lifeassistant', '生活服务管理', '7', '38', '1');
INSERT INTO `xiaozu_menu` VALUES ('information', '网站通知管理', '7', '38', '0');
INSERT INTO `xiaozu_menu` VALUES ('shoptxtlog', '店铺资金记录', '5', '38', '10');
INSERT INTO `xiaozu_menu` VALUES ('shoptx', '店铺提现记录', '5', '38', '9');
INSERT INTO `xiaozu_menu` VALUES ('shopjsadd', '店铺结算处理', '5', '38', '8');
INSERT INTO `xiaozu_menu` VALUES ('ordercomment', '订单评价列表', '5', '38', '7');
INSERT INTO `xiaozu_menu` VALUES ('beizhulist', '订单备注', '5', '38', '6');
INSERT INTO `xiaozu_menu` VALUES ('setpaotui', '跑腿信息设置', '5', '38', '4');
INSERT INTO `xiaozu_menu` VALUES ('shophuiorder', '闪惠订单', '5', '38', '5');
INSERT INTO `xiaozu_menu` VALUES ('drawsmlist', '退款原因说明', '5', '38', '3');
INSERT INTO `xiaozu_menu` VALUES ('drawbacklog', '退款申请处理', '5', '38', '2');
INSERT INTO `xiaozu_menu` VALUES ('ordertoday', '当天订单处理', '5', '38', '1');
INSERT INTO `xiaozu_menu` VALUES ('orderlist', '所有订单', '5', '38', '0');
INSERT INTO `xiaozu_menu` VALUES ('memberlistshop', '商家会员列表', '2', '38', '3');
INSERT INTO `xiaozu_menu` VALUES ('memberlist', '普通会员列表', '2', '38', '2');
INSERT INTO `xiaozu_menu` VALUES ('addadmin', '添加管理员', '2', '38', '1');
INSERT INTO `xiaozu_menu` VALUES ('adminlist', '管理员列表', '2', '38', '0');
INSERT INTO `xiaozu_menu` VALUES ('index', '网站信息', '1', '38', '0');
INSERT INTO `xiaozu_menu` VALUES ('shopapp', 'app商家端', '34', '38', '3');
INSERT INTO `xiaozu_menu` VALUES ('psapp', 'app配送员', '34', '38', '4');
INSERT INTO `xiaozu_menu` VALUES ('othertpl', '短信/邮件模板设置', '17', '4', '3');
INSERT INTO `xiaozu_menu` VALUES ('paylist', '支付接口列表', '17', '4', '0');
INSERT INTO `xiaozu_menu` VALUES ('loginlist', '第三方登陆', '17', '4', '1');
INSERT INTO `xiaozu_menu` VALUES ('specialpage', '专题页管理', '17', '4', '2');
INSERT INTO `xiaozu_menu` VALUES ('helpcenter', '帮助中心', '7', '4', '4');
INSERT INTO `xiaozu_menu` VALUES ('newstype', '新闻分类', '7', '4', '3');
INSERT INTO `xiaozu_menu` VALUES ('lifeassistant', '生活服务管理', '7', '4', '1');
INSERT INTO `xiaozu_menu` VALUES ('newslist', '新闻列表', '7', '4', '2');
INSERT INTO `xiaozu_menu` VALUES ('information', '网站通知管理', '7', '4', '0');
INSERT INTO `xiaozu_menu` VALUES ('shoptxtlog', '店铺资金记录', '5', '4', '8');
INSERT INTO `xiaozu_menu` VALUES ('shoptx', '店铺提现记录', '5', '4', '7');
INSERT INTO `xiaozu_menu` VALUES ('shopjsadd', '店铺结算处理', '5', '4', '6');
INSERT INTO `xiaozu_menu` VALUES ('ordercomment', '订单评价列表', '5', '4', '5');
INSERT INTO `xiaozu_menu` VALUES ('beizhulist', '订单备注', '5', '4', '4');
INSERT INTO `xiaozu_menu` VALUES ('setpaotui', '跑腿信息设置', '5', '4', '2');
INSERT INTO `xiaozu_menu` VALUES ('shophuiorder', '闪惠订单', '5', '4', '3');
INSERT INTO `xiaozu_menu` VALUES ('orderlist', '所有订单', '5', '4', '0');
INSERT INTO `xiaozu_menu` VALUES ('ordertoday', '当天订单处理', '5', '4', '1');
INSERT INTO `xiaozu_menu` VALUES ('psyyj', '配送员佣金统计', '18', '4', '9');
INSERT INTO `xiaozu_menu` VALUES ('shopjsover', '店铺结算统计', '18', '4', '8');
INSERT INTO `xiaozu_menu` VALUES ('orderyjin', '店铺订单统计', '18', '4', '7');
INSERT INTO `xiaozu_menu` VALUES ('ordertotal', '网站订单统计', '18', '4', '4');
INSERT INTO `xiaozu_menu` VALUES ('tjshophui', '闪惠订单统计', '18', '4', '5');
INSERT INTO `xiaozu_menu` VALUES ('areadtoji', '区域管理统计', '18', '4', '6');
INSERT INTO `xiaozu_menu` VALUES ('user', '会员购买数据', '18', '4', '3');
INSERT INTO `xiaozu_menu` VALUES ('goods', '菜品销量分析', '18', '4', '2');
INSERT INTO `xiaozu_menu` VALUES ('area', '地区销量分析', '18', '4', '1');
INSERT INTO `xiaozu_menu` VALUES ('appletset', '小程序管理', '1132', '1', '0');
INSERT INTO `xiaozu_menu` VALUES ('stationshoplist', '分站商家', '1126', '1', '5');
INSERT INTO `xiaozu_menu` VALUES ('managecity', '添加城市', '1126', '1', '4');
INSERT INTO `xiaozu_menu` VALUES ('citylist', '城市列表', '1126', '1', '3');
INSERT INTO `xiaozu_menu` VALUES ('stationorderlist', '分站订单', '1126', '1', '2');
INSERT INTO `xiaozu_menu` VALUES ('beizhulist', '订单备注', '5', '47', '8');
INSERT INTO `xiaozu_menu` VALUES ('shophuiorder', '闪惠订单', '5', '47', '7');
INSERT INTO `xiaozu_menu` VALUES ('helpmove', '帮我送设置', '5', '47', '6');
INSERT INTO `xiaozu_menu` VALUES ('helpbuy', '帮我买设置', '5', '47', '5');
INSERT INTO `xiaozu_menu` VALUES ('setpaotui', '跑腿信息设置', '5', '47', '4');
INSERT INTO `xiaozu_menu` VALUES ('managestation', '添加分站', '1126', '1', '1');
INSERT INTO `xiaozu_menu` VALUES ('drawsmlist', '退款原因说明', '5', '47', '3');
INSERT INTO `xiaozu_menu` VALUES ('drawbacklog', '退款申请处理', '5', '47', '2');
INSERT INTO `xiaozu_menu` VALUES ('ordertoday', '当天订单处理', '5', '47', '1');
INSERT INTO `xiaozu_menu` VALUES ('orderlist', '所有订单', '5', '47', '0');
INSERT INTO `xiaozu_menu` VALUES ('stationlist', '分站列表', '1126', '1', '0');
INSERT INTO `xiaozu_menu` VALUES ('shopapp', 'APP商家端', '34', '1', '1');
INSERT INTO `xiaozu_menu` VALUES ('appset', 'APP设置', '34', '1', '0');
INSERT INTO `xiaozu_menu` VALUES ('wxkefu', '微信客服设置', '28', '1', '5');
INSERT INTO `xiaozu_menu` VALUES ('wxuser', '微信用户列表', '28', '1', '4');
INSERT INTO `xiaozu_menu` VALUES ('wxback', '微信自动回复', '28', '1', '3');
INSERT INTO `xiaozu_menu` VALUES ('wxmenu', '微信菜单管理', '28', '1', '2');
INSERT INTO `xiaozu_menu` VALUES ('wxset', '微信基本设置', '28', '1', '1');
INSERT INTO `xiaozu_menu` VALUES ('indexstyle', '移动端首页设置', '28', '1', '0');
INSERT INTO `xiaozu_menu` VALUES ('advlist', '网站广告列表', '14', '1', '0');
INSERT INTO `xiaozu_menu` VALUES ('advtype', '网站广告类型', '14', '1', '1');
INSERT INTO `xiaozu_menu` VALUES ('fxtxlog', '分销佣金提现记录', '16', '1', '14');
INSERT INTO `xiaozu_menu` VALUES ('distributioncontent', '分销说明', '16', '1', '13');
INSERT INTO `xiaozu_menu` VALUES ('distributionset', '分销设置', '16', '1', '12');
INSERT INTO `xiaozu_menu` VALUES ('virtualinfo', '店铺刷单', '16', '1', '11');
INSERT INTO `xiaozu_menu` VALUES ('receivejuanlog', '优惠券领用记录', '16', '1', '10');
INSERT INTO `xiaozu_menu` VALUES ('cardlist', '充值卡列表', '16', '1', '9');
INSERT INTO `xiaozu_menu` VALUES ('rechargezend', '充值营销', '16', '1', '8');
INSERT INTO `xiaozu_menu` VALUES ('shop', '店铺销量分析', '18', '4', '0');
INSERT INTO `xiaozu_menu` VALUES ('searchattr', '搜索关键词', '3', '4', '7');
INSERT INTO `xiaozu_menu` VALUES ('goodsattr', '商品属性', '3', '4', '5');
INSERT INTO `xiaozu_menu` VALUES ('goodsgg', '商品规格', '3', '4', '6');
INSERT INTO `xiaozu_menu` VALUES ('goodssign', '促销商品标签', '3', '4', '4');
INSERT INTO `xiaozu_menu` VALUES ('shoptype', '后台模型', '3', '4', '3');
INSERT INTO `xiaozu_menu` VALUES ('addshop', '后台添加店铺', '3', '4', '2');
INSERT INTO `xiaozu_menu` VALUES ('adminshoplist', '后台商家列表', '3', '4', '0');
INSERT INTO `xiaozu_menu` VALUES ('adminshopwati', '后台待审核商家', '3', '4', '1');
INSERT INTO `xiaozu_menu` VALUES ('grouplist', '会员分组', '2', '4', '7');
INSERT INTO `xiaozu_menu` VALUES ('addgroup', '添加会员分组', '2', '4', '6');
INSERT INTO `xiaozu_menu` VALUES ('addmember', '添加会员', '2', '4', '5');
INSERT INTO `xiaozu_menu` VALUES ('memcostloglist', '会员余额操作日志', '2', '4', '4');
INSERT INTO `xiaozu_menu` VALUES ('memberlistshop', '商家会员列表', '2', '4', '3');
INSERT INTO `xiaozu_menu` VALUES ('memberlist', '普通会员列表', '2', '4', '2');
INSERT INTO `xiaozu_menu` VALUES ('addadmin', '添加管理员', '2', '4', '1');
INSERT INTO `xiaozu_menu` VALUES ('adminlist', '管理员列表', '2', '4', '0');
INSERT INTO `xiaozu_menu` VALUES ('limitset', '后台菜单管理', '1', '4', '5');
INSERT INTO `xiaozu_menu` VALUES ('footinfo', '网站底部', '1', '4', '4');
INSERT INTO `xiaozu_menu` VALUES ('toplink', '网站导航', '1', '4', '3');
INSERT INTO `xiaozu_menu` VALUES ('otherset', '网站限制', '1', '4', '2');
INSERT INTO `xiaozu_menu` VALUES ('siteset', '网站设置', '1', '4', '1');
INSERT INTO `xiaozu_menu` VALUES ('index', '网站信息', '1', '4', '0');
INSERT INTO `xiaozu_menu` VALUES ('appset', 'app设置', '34', '4', '0');
INSERT INTO `xiaozu_menu` VALUES ('buyerapp', 'app用户端', '34', '4', '1');
INSERT INTO `xiaozu_menu` VALUES ('buyermsg', 'app用户群发', '34', '4', '2');
INSERT INTO `xiaozu_menu` VALUES ('shopapp', 'app商家端', '34', '4', '3');
INSERT INTO `xiaozu_menu` VALUES ('psapp', 'app配送员', '34', '4', '4');
INSERT INTO `xiaozu_menu` VALUES ('index', '网站信息', '1', '50', '0');
INSERT INTO `xiaozu_menu` VALUES ('siteset', '网站设置', '1', '50', '1');
INSERT INTO `xiaozu_menu` VALUES ('otherset', '网站限制', '1', '50', '2');
INSERT INTO `xiaozu_menu` VALUES ('toplink', '网站导航', '1', '50', '3');
INSERT INTO `xiaozu_menu` VALUES ('giftlist', '积分兑换', '16', '1', '7');
INSERT INTO `xiaozu_menu` VALUES ('scoredx', '积分抵现', '16', '1', '6');
INSERT INTO `xiaozu_menu` VALUES ('scoreset', '积分发放', '16', '1', '5');
INSERT INTO `xiaozu_menu` VALUES ('specialpage', '专题页管理', '16', '1', '4');
INSERT INTO `xiaozu_menu` VALUES ('juanlist', '优惠券列表', '16', '1', '3');
INSERT INTO `xiaozu_menu` VALUES ('followsjset', '优惠券营销', '16', '1', '2');
INSERT INTO `xiaozu_menu` VALUES ('cxrulelist', '优惠活动列表', '16', '1', '1');
INSERT INTO `xiaozu_menu` VALUES ('singlelist', '网站单页列表', '12', '1', '0');
INSERT INTO `xiaozu_menu` VALUES ('cxruleset', '优惠活动设置', '16', '1', '0');
INSERT INTO `xiaozu_menu` VALUES ('asklist', '后台留言列表', '11', '1', '0');
INSERT INTO `xiaozu_menu` VALUES ('complreasonlist', '投诉原因列表', '11', '1', '1');
INSERT INTO `xiaozu_menu` VALUES ('complaintlist', '投诉管理列表', '11', '1', '2');
INSERT INTO `xiaozu_menu` VALUES ('platformpsrange', '平台地图配送范围', '10', '1', '1');
INSERT INTO `xiaozu_menu` VALUES ('adminpsset', '网站配送设置', '10', '1', '0');
INSERT INTO `xiaozu_menu` VALUES ('errlog', '错误日志', '17', '1', '12');
INSERT INTO `xiaozu_menu` VALUES ('cleartpl', '清理缓存', '17', '1', '11');
INSERT INTO `xiaozu_menu` VALUES ('rebkdata', '还原数据', '17', '1', '10');
INSERT INTO `xiaozu_menu` VALUES ('basedata', '备份数据', '17', '1', '9');
INSERT INTO `xiaozu_menu` VALUES ('emailset', '邮箱设置', '17', '1', '8');
INSERT INTO `xiaozu_menu` VALUES ('othertpl', '短信/邮件模板设置', '17', '1', '7');
INSERT INTO `xiaozu_menu` VALUES ('smsset', '短信接口', '17', '1', '6');
INSERT INTO `xiaozu_menu` VALUES ('loginlist', '登陆接口', '17', '1', '5');
INSERT INTO `xiaozu_menu` VALUES ('paylist', '支付接口', '17', '1', '4');
INSERT INTO `xiaozu_menu` VALUES ('imgset', '图片接口', '17', '1', '2');
INSERT INTO `xiaozu_menu` VALUES ('auto_callphone', '自动拨打电话接口', '17', '1', '3');
INSERT INTO `xiaozu_menu` VALUES ('apiset', '配送宝接口', '17', '1', '1');
INSERT INTO `xiaozu_menu` VALUES ('mapset', '地图接口', '17', '1', '0');
INSERT INTO `xiaozu_menu` VALUES ('glywxmsg', '一起说置顶留言', '7', '1', '5');
INSERT INTO `xiaozu_menu` VALUES ('yiqisaylist', '一起说留言管理', '7', '1', '4');
INSERT INTO `xiaozu_menu` VALUES ('newstype', '新闻分类列表', '7', '1', '3');
INSERT INTO `xiaozu_menu` VALUES ('lifeassistant', '生活服务管理', '7', '1', '1');
INSERT INTO `xiaozu_menu` VALUES ('shoptxtlog', '店铺资金记录', '5', '47', '12');
INSERT INTO `xiaozu_menu` VALUES ('asklist', '后台留言列表', '11', '47', '0');
INSERT INTO `xiaozu_menu` VALUES ('complreasonlist', '投诉原因列表', '11', '47', '1');
INSERT INTO `xiaozu_menu` VALUES ('complaintlist', '投诉管理列表', '11', '47', '2');
INSERT INTO `xiaozu_menu` VALUES ('memberlistps', '配送员管理', '32', '47', '0');
INSERT INTO `xiaozu_menu` VALUES ('psymap', '配送员订单', '32', '47', '1');
INSERT INTO `xiaozu_menu` VALUES ('otherset', '网站限制', '1', '55', '3');
INSERT INTO `xiaozu_menu` VALUES ('colorset', '色调管理', '1', '55', '4');
INSERT INTO `xiaozu_menu` VALUES ('toplink', '网站导航', '1', '55', '5');
INSERT INTO `xiaozu_menu` VALUES ('footinfo', '网站底部', '1', '55', '6');
INSERT INTO `xiaozu_menu` VALUES ('limitset', '后台菜单管理', '1', '55', '7');
INSERT INTO `xiaozu_menu` VALUES ('index', '网站信息', '1', '59', '0');
INSERT INTO `xiaozu_menu` VALUES ('newslist', '网站新闻列表', '7', '1', '2');
INSERT INTO `xiaozu_menu` VALUES ('shopjsover', '店铺结算统计', '18', '61', '2');
INSERT INTO `xiaozu_menu` VALUES ('orderyjin', '店铺订单统计', '18', '61', '1');
INSERT INTO `xiaozu_menu` VALUES ('areadtoji', '区域管理统计', '18', '61', '0');
INSERT INTO `xiaozu_menu` VALUES ('information', '网站通知管理', '7', '1', '0');
INSERT INTO `xiaozu_menu` VALUES ('helpmove', '帮我送设置', '5', '1', '12');
INSERT INTO `xiaozu_menu` VALUES ('setpaotui', '跑腿信息设置', '5', '1', '10');
INSERT INTO `xiaozu_menu` VALUES ('helpbuy', '帮我买设置', '5', '1', '11');
INSERT INTO `xiaozu_menu` VALUES ('drawsmlist', '退款原因说明', '5', '63', '3');
INSERT INTO `xiaozu_menu` VALUES ('setpaotui', '跑腿信息设置', '5', '63', '4');
INSERT INTO `xiaozu_menu` VALUES ('helpbuy', '帮我买设置', '5', '63', '5');
INSERT INTO `xiaozu_menu` VALUES ('helpmove', '帮我送设置', '5', '63', '6');
INSERT INTO `xiaozu_menu` VALUES ('shophuiorder', '闪惠订单', '5', '63', '7');
INSERT INTO `xiaozu_menu` VALUES ('beizhulist', '订单备注', '5', '63', '8');
INSERT INTO `xiaozu_menu` VALUES ('ordercomment', '订单评价列表', '5', '63', '9');
INSERT INTO `xiaozu_menu` VALUES ('shopjsadd', '店铺结算处理', '5', '63', '10');
INSERT INTO `xiaozu_menu` VALUES ('shoptx', '店铺提现记录', '5', '63', '11');
INSERT INTO `xiaozu_menu` VALUES ('shoptxtlog', '店铺资金记录', '5', '63', '12');
INSERT INTO `xiaozu_menu` VALUES ('shoptxtlog', '店铺资金记录', '5', '1', '9');
INSERT INTO `xiaozu_menu` VALUES ('shoptx', '店铺提现记录', '5', '1', '8');
INSERT INTO `xiaozu_menu` VALUES ('shopjsadd', '店铺结算处理', '5', '1', '7');
INSERT INTO `xiaozu_menu` VALUES ('ordercomment', '订单评价列表', '5', '1', '6');
INSERT INTO `xiaozu_menu` VALUES ('beizhulist', '订单备注设置', '5', '1', '5');
INSERT INTO `xiaozu_menu` VALUES ('shophuiorder', '闪惠订单列表', '5', '1', '4');
INSERT INTO `xiaozu_menu` VALUES ('drawsmlist', '退款原因说明', '5', '1', '3');
INSERT INTO `xiaozu_menu` VALUES ('drawbacklog', '退款申请处理', '5', '1', '2');
INSERT INTO `xiaozu_menu` VALUES ('ordertoday', '当天订单处理', '5', '1', '1');
INSERT INTO `xiaozu_menu` VALUES ('orderlist', '所有订单列表', '5', '1', '0');
INSERT INTO `xiaozu_menu` VALUES ('js_statisyic', '结算统计', '18', '1', '4');
INSERT INTO `xiaozu_menu` VALUES ('mem_statisyic', '会员统计', '18', '1', '3');
INSERT INTO `xiaozu_menu` VALUES ('shop_statisyic', '商家统计', '18', '1', '2');
INSERT INTO `xiaozu_menu` VALUES ('area_statisyic', '分站统计', '18', '1', '1');
INSERT INTO `xiaozu_menu` VALUES ('trade_statisyic', '交易统计', '18', '1', '0');
INSERT INTO `xiaozu_menu` VALUES ('openset', '一键开关店铺', '3', '1', '8');
INSERT INTO `xiaozu_menu` VALUES ('searchattr', '搜索关键词', '3', '1', '6');
INSERT INTO `xiaozu_menu` VALUES ('shopjsset', '结算设置', '3', '1', '7');
INSERT INTO `xiaozu_menu` VALUES ('newslist', '网站新闻列表', '7', '66', '0');
INSERT INTO `xiaozu_menu` VALUES ('newstype', '新闻分类列表', '7', '66', '1');
INSERT INTO `xiaozu_menu` VALUES ('goodsgg', '商品规格', '3', '1', '5');
INSERT INTO `xiaozu_menu` VALUES ('goodsattr', '商品单位', '3', '1', '4');
INSERT INTO `xiaozu_menu` VALUES ('shoptype', '店铺分类', '3', '1', '3');
INSERT INTO `xiaozu_menu` VALUES ('addshop', '添加店铺', '3', '1', '2');
INSERT INTO `xiaozu_menu` VALUES ('adminshopwati', '待审核商家', '3', '1', '1');
INSERT INTO `xiaozu_menu` VALUES ('adminshoplist', '商家列表', '3', '1', '0');
INSERT INTO `xiaozu_menu` VALUES ('addgroup', '添加会员分组', '2', '1', '7');
INSERT INTO `xiaozu_menu` VALUES ('memcostloglist', '会员余额操作日志', '2', '1', '8');
INSERT INTO `xiaozu_menu` VALUES ('grouplist', '会员分组', '2', '1', '6');
INSERT INTO `xiaozu_menu` VALUES ('memberlist', '普通会员列表', '2', '1', '3');
INSERT INTO `xiaozu_menu` VALUES ('memberlistshop', '商家会员列表', '2', '1', '4');
INSERT INTO `xiaozu_menu` VALUES ('addmember', '添加会员', '2', '1', '5');
INSERT INTO `xiaozu_menu` VALUES ('shop', '店铺销量分析', '18', '71', '0');
INSERT INTO `xiaozu_menu` VALUES ('area', '地区销量分析', '18', '71', '1');
INSERT INTO `xiaozu_menu` VALUES ('goods', '菜品销量分析', '18', '71', '2');
INSERT INTO `xiaozu_menu` VALUES ('user', '会员购买数据', '18', '71', '3');
INSERT INTO `xiaozu_menu` VALUES ('ordertotal', '网站订单统计', '18', '71', '4');
INSERT INTO `xiaozu_menu` VALUES ('paotuiorder', '跑腿订单统计', '18', '71', '5');
INSERT INTO `xiaozu_menu` VALUES ('addadmin', '添加管理员', '2', '1', '1');
INSERT INTO `xiaozu_menu` VALUES ('shop', '店铺销量分析', '18', '72', '0');
INSERT INTO `xiaozu_menu` VALUES ('area', '地区销量分析', '18', '72', '1');
INSERT INTO `xiaozu_menu` VALUES ('goods', '菜品销量分析', '18', '72', '2');
INSERT INTO `xiaozu_menu` VALUES ('user', '会员购买数据', '18', '72', '3');
INSERT INTO `xiaozu_menu` VALUES ('ordertotal', '网站订单统计', '18', '72', '4');
INSERT INTO `xiaozu_menu` VALUES ('paotuiorder', '跑腿订单统计', '18', '72', '5');
INSERT INTO `xiaozu_menu` VALUES ('memberstationlist', '分站管理员列表', '2', '1', '2');
INSERT INTO `xiaozu_menu` VALUES ('adminlist', '管理员列表', '2', '1', '0');
INSERT INTO `xiaozu_menu` VALUES ('limitset', '管理员权限', '1', '1', '7');
INSERT INTO `xiaozu_menu` VALUES ('footinfo', '网站底部导航', '1', '1', '6');
INSERT INTO `xiaozu_menu` VALUES ('toplink', '网站头部导航', '1', '1', '5');
INSERT INTO `xiaozu_menu` VALUES ('colorset', '色调管理', '1', '1', '4');
INSERT INTO `xiaozu_menu` VALUES ('otherset', '网站限制', '1', '1', '3');
INSERT INTO `xiaozu_menu` VALUES ('logoset', 'logo设置', '1', '1', '2');
INSERT INTO `xiaozu_menu` VALUES ('siteset', '网站设置', '1', '1', '1');
INSERT INTO `xiaozu_menu` VALUES ('index', '网站信息', '1', '1', '0');

-- ----------------------------
-- Table structure for xiaozu_messages
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_messages`;
CREATE TABLE `xiaozu_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `qq` int(15) NOT NULL,
  `shopname` varchar(255) NOT NULL,
  `shopaddress` varchar(255) NOT NULL,
  `addtime` varchar(11) NOT NULL,
  `is_pass` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_messages
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_mobile
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_mobile`;
CREATE TABLE `xiaozu_mobile` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `phone` varchar(50) NOT NULL,
  `addtime` int(12) NOT NULL,
  `code` varchar(50) NOT NULL,
  `is_send` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_mobile
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_mobileapp
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_mobileapp`;
CREATE TABLE `xiaozu_mobileapp` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `phone` varchar(20) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `addtime` int(11) NOT NULL,
  `type` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17845 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_mobileapp
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_module
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_module`;
CREATE TABLE `xiaozu_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `cnname` varchar(100) NOT NULL,
  `install` int(1) NOT NULL DEFAULT '0' COMMENT '0表未安装1，表已安装',
  `parent_id` int(5) NOT NULL DEFAULT '0' COMMENT '上级模块',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1137 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_module
-- ----------------------------
INSERT INTO `xiaozu_module` VALUES ('1', 'system', '系统管理', '1', '0');
INSERT INTO `xiaozu_module` VALUES ('2', 'member', '用户管理', '1', '0');
INSERT INTO `xiaozu_module` VALUES ('3', 'shop', '店铺管理', '1', '0');
INSERT INTO `xiaozu_module` VALUES ('18', 'analysis', '数据统计', '1', '0');
INSERT INTO `xiaozu_module` VALUES ('5', 'order', '订单管理', '1', '0');
INSERT INTO `xiaozu_module` VALUES ('7', 'news', '内容管理', '1', '0');
INSERT INTO `xiaozu_module` VALUES ('17', 'other', '接口管理', '1', '0');
INSERT INTO `xiaozu_module` VALUES ('10', 'area', '区域管理', '1', '3');
INSERT INTO `xiaozu_module` VALUES ('11', 'ask', '留言管理', '1', '7');
INSERT INTO `xiaozu_module` VALUES ('12', 'single', '单页', '1', '7');
INSERT INTO `xiaozu_module` VALUES ('16', 'card', '营销管理', '1', '0');
INSERT INTO `xiaozu_module` VALUES ('14', 'adv', '广告管理', '1', '7');
INSERT INTO `xiaozu_module` VALUES ('28', 'weixin', '移动端管理', '1', '0');
INSERT INTO `xiaozu_module` VALUES ('30', 'wxsite', '微信网站', '1', '28');
INSERT INTO `xiaozu_module` VALUES ('32', 'psuser', '配送员模块', '1', '3');
INSERT INTO `xiaozu_module` VALUES ('34', 'app', 'app', '1', '28');
INSERT INTO `xiaozu_module` VALUES ('1124', 'shopcenter', 'shopcenter', '1', '17');
INSERT INTO `xiaozu_module` VALUES ('1126', 'station', '分站管理', '1', '0');
INSERT INTO `xiaozu_module` VALUES ('1134', 'gift', '礼品', '1', '0');
INSERT INTO `xiaozu_module` VALUES ('1128', 'psapi', '配送宝', '1', '0');
INSERT INTO `xiaozu_module` VALUES ('1132', 'applet', 'applet', '1', '28');

-- ----------------------------
-- Table structure for xiaozu_news
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_news`;
CREATE TABLE `xiaozu_news` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `addtime` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `typeid` int(2) NOT NULL,
  `orderid` int(5) NOT NULL DEFAULT '1000' COMMENT '1000',
  `seo_key` varchar(255) DEFAULT NULL,
  `seo_content` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_news
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_newstype
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_newstype`;
CREATE TABLE `xiaozu_newstype` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '目录类型：1文章目录 2包含下级目录 ',
  `parent_id` int(20) NOT NULL DEFAULT '0' COMMENT '上级目录ID，0： 顶级目录',
  `displaytype` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1：一个文章显示一个页面，2表示此目录文章先到到一个文章里',
  `orderid` int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_newstype
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_oauth
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_oauth`;
CREATE TABLE `xiaozu_oauth` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `uid` int(20) NOT NULL,
  `token` varchar(255) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `addtime` int(12) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=96 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_oauth
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_onlinelog
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_onlinelog`;
CREATE TABLE `xiaozu_onlinelog` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `upid` int(20) NOT NULL DEFAULT '0',
  `dno` varchar(100) NOT NULL,
  `cost` decimal(6,2) NOT NULL DEFAULT '0.00',
  `status` int(1) NOT NULL DEFAULT '0',
  `addtime` int(12) NOT NULL,
  `source` int(11) NOT NULL,
  `paydotype` varchar(100) NOT NULL,
  `used` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=112879 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_onlinelog
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_order
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_order`;
CREATE TABLE `xiaozu_order` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `dno` varchar(100) NOT NULL COMMENT '订单编号',
  `shopuid` int(20) NOT NULL COMMENT '店铺UID',
  `shopid` int(20) NOT NULL COMMENT '店铺ID',
  `shopname` varchar(100) NOT NULL COMMENT '店铺名称',
  `shopphone` varchar(20) NOT NULL COMMENT '店铺电话',
  `shopaddress` varchar(150) NOT NULL COMMENT '店铺地址',
  `buyeruid` int(20) NOT NULL COMMENT '购买用户ID，0未注册用户',
  `buyername` varchar(100) NOT NULL COMMENT '购买热名称',
  `buyeraddress` varchar(150) NOT NULL COMMENT '联系地址',
  `buyerphone` varchar(20) NOT NULL COMMENT '联系电话',
  `status` int(1) NOT NULL COMMENT '状态',
  `is_acceptorder` int(1) NOT NULL DEFAULT '0' COMMENT '确认收货 0 未确认 1已确认',
  `paytype` varchar(20) NOT NULL DEFAULT '0' COMMENT '支付类型outpay货到支付，open_acout账户余额支付，other调用paylist',
  `paystatus` int(1) NOT NULL COMMENT '支付状态1已支付',
  `trade_no` varchar(50) NOT NULL COMMENT '在线支付交易号',
  `content` varchar(255) NOT NULL COMMENT '订单备注',
  `ordertype` int(1) NOT NULL DEFAULT '0' COMMENT '订餐方式1网站，2电话，3微信，4App',
  `daycode` int(10) NOT NULL DEFAULT '0' COMMENT '当天订单序号',
  `area1` varchar(255) DEFAULT NULL COMMENT '二级地址名称',
  `area2` varchar(255) DEFAULT NULL COMMENT '三级地址名称',
  `area3` varchar(255) DEFAULT NULL,
  `cxids` varchar(100) DEFAULT NULL COMMENT '促销规则ID集',
  `cxcost` decimal(10,2) DEFAULT '0.00',
  `yhjcost` int(5) NOT NULL DEFAULT '0' COMMENT '优惠劵优惠金额',
  `yhjids` varchar(255) DEFAULT NULL COMMENT '使用优惠劵ID集',
  `ipaddress` varchar(255) DEFAULT NULL,
  `scoredown` int(5) NOT NULL DEFAULT '0' COMMENT '积分抵扣数',
  `is_ping` int(11) NOT NULL DEFAULT '0' COMMENT '是否评价字段 1已评完 0未评',
  `isbefore` int(1) NOT NULL DEFAULT '0' COMMENT '0:普通订单，1订台订单',
  `marketcost` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '超市商品总价',
  `marketps` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '超市配送配送',
  `shopcost` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '店铺商品总价',
  `shopps` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '店铺配送费',
  `pstype` int(1) NOT NULL DEFAULT '0' COMMENT '配送方式 0：平台1：个人',
  `bagcost` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '打包费',
  `addtime` int(12) NOT NULL DEFAULT '0' COMMENT '制造时间',
  `posttime` int(12) NOT NULL DEFAULT '0' COMMENT '配送时间',
  `passtime` int(12) NOT NULL DEFAULT '0' COMMENT '审核时间',
  `sendtime` int(12) NOT NULL DEFAULT '0' COMMENT '发货时间',
  `suretime` int(12) NOT NULL DEFAULT '0' COMMENT '完成时间',
  `allcost` decimal(8,2) DEFAULT NULL,
  `buycode` varchar(50) DEFAULT NULL COMMENT '订台码',
  `othertext` text COMMENT '其他说明',
  `is_print` int(1) NOT NULL DEFAULT '0',
  `wxstatus` int(1) NOT NULL DEFAULT '0' COMMENT '1商家确认，2商家取消',
  `shoptype` int(1) DEFAULT '0',
  `is_make` int(1) NOT NULL DEFAULT '0',
  `is_reback` smallint(1) DEFAULT '0',
  `areaids` char(255) DEFAULT NULL,
  `psuid` int(20) DEFAULT NULL,
  `psusername` varchar(100) DEFAULT NULL,
  `psemail` varchar(100) DEFAULT NULL,
  `admin_id` int(20) NOT NULL DEFAULT '0',
  `is_goshop` int(1) NOT NULL DEFAULT '0' COMMENT '0:外送 1订台/到店消费/自取',
  `paytype_name` varchar(30) DEFAULT NULL COMMENT '支付类型code',
  `taxcost` decimal(5,2) NOT NULL,
  `postdate` text,
  `pttype` int(1) NOT NULL COMMENT '1为帮我送  2为帮我买',
  `ptkg` varchar(30) NOT NULL COMMENT '货 公斤 数',
  `ptkm` varchar(30) NOT NULL COMMENT '收取货 地址 两地 距离 km',
  `allkgcost` decimal(10,2) NOT NULL COMMENT '重量价格',
  `allkmcost` decimal(10,2) NOT NULL COMMENT '距离价格',
  `farecost` decimal(10,2) DEFAULT NULL COMMENT '小费价格',
  `dnos` int(11) NOT NULL DEFAULT '0',
  `shoplat` decimal(9,6) NOT NULL DEFAULT '0.000000' COMMENT '商家lat坐标',
  `shoplng` decimal(9,6) NOT NULL DEFAULT '0.000000' COMMENT '商家lng坐标',
  `buyerlat` decimal(9,6) NOT NULL DEFAULT '0.000000' COMMENT '用户lat坐标',
  `buyerlng` decimal(9,6) NOT NULL DEFAULT '0.000000' COMMENT '用户lng坐标',
  `psbflag` int(1) DEFAULT '1',
  `movegoodscost` varchar(50) NOT NULL COMMENT '帮我送物品价值',
  `movegoodstype` varchar(100) NOT NULL COMMENT '帮我送物品类型',
  `psyoverlng` decimal(9,6) DEFAULT NULL,
  `psyoverlat` decimal(9,6) DEFAULT NULL,
  `shopdowncost` decimal(10,2) DEFAULT '0.00',
  `psstatus` int(2) DEFAULT NULL,
  `picktime` int(12) DEFAULT NULL,
  `addpscost` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '附加配送费',
  `is_cui` int(2) DEFAULT '0' COMMENT '是否被催单 0否 1是',
  `maketime` int(12) DEFAULT NULL COMMENT '商家接单时间',
  `point` int(2) DEFAULT NULL COMMENT '订单评分，即评价订单时的总体评价',
  `is_hand` int(2) DEFAULT '0' COMMENT '是否是立即配送订单  1是 0否',
  `is_userhide` int(2) DEFAULT '0' COMMENT '0正常 1是用户删除，改字段变为1，用户在用户端看不到该订单',
  `scoredowncost` decimal(5,2) DEFAULT '0.00' COMMENT '积分抵现金额',
  `cx_shoudan` decimal(10,2) DEFAULT '0.00' COMMENT '促销中首单立减金额',
  `cx_manjian` decimal(10,2) DEFAULT '0.00' COMMENT '促销中满减金额',
  `cx_zhekou` decimal(10,2) DEFAULT '0.00' COMMENT '促销中折扣减金额',
  `cx_nopsf` decimal(10,2) DEFAULT '0.00' COMMENT '促销中免配送费金额',
  `is_ziti` int(1) DEFAULT '0' COMMENT '是否是自提订单 0否 1是',
  `cxdet` text COMMENT '本单用到的促销信息详情序列化数组',
  `paytime` int(11) NOT NULL COMMENT '支付时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=43242 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_order
-- ----------------------------
INSERT INTO `xiaozu_order` VALUES ('43241', '15297541264635', '22052', '1213', '光合旗舰店', '15038875888', '河南省电子商务产业园', '22053', '测试', '河南省电子商务产业园1605', '15236978412', '1', '0', '1', '1', '', '', '5', '1', null, null, null, '', '0.00', '0', '', '127.0.0.1', '0', '0', '0', '0.00', '0.00', '15.00', '0.00', '1', '0.00', '1529754247', '1529755326', '1529754126', '0', '0', '15.00', '33407e', '', '0', '0', '0', '0', '0', '', null, null, null, '410100', '0', 'open_acout', '0.00', '19:01-20:01', '0', '', '', '0.00', '0.00', null, '0', '34.802330', '113.543806', '34.802330', '113.543806', '1', '', '', null, null, '0.00', null, null, '0.00', '0', null, null, '0', '0', '0.00', '0.00', '0.00', '0.00', '0.00', '0', 'a:0:{}', '1529754247');

-- ----------------------------
-- Table structure for xiaozu_orderdet
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_orderdet`;
CREATE TABLE `xiaozu_orderdet` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `order_id` int(20) NOT NULL,
  `goodsid` int(20) NOT NULL,
  `goodsname` varchar(150) NOT NULL,
  `goodscost` decimal(9,2) NOT NULL,
  `goodscount` int(2) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `shopid` int(5) NOT NULL,
  `is_send` int(11) NOT NULL DEFAULT '0' COMMENT '是否是赠品 1赠品',
  `product_id` int(20) NOT NULL DEFAULT '0',
  `have_det` int(1) NOT NULL DEFAULT '0',
  `typeid` varchar(100) DEFAULT NULL,
  `goodsattr` varchar(25) NOT NULL DEFAULT '' COMMENT '商品单位 ',
  `img` text COMMENT '商品图片',
  `oldcost` decimal(10,2) DEFAULT NULL COMMENT '折扣商品的商品原价',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=41840 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_orderdet
-- ----------------------------
INSERT INTO `xiaozu_orderdet` VALUES ('41839', '43241', '9583', '蔓越莓酥饼', '15.00', '1', '0', '1213', '0', '0', '0', null, '份', '/images/410100/shop/1213/goods/20180623185613222.png', '15.00');

-- ----------------------------
-- Table structure for xiaozu_orderps
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_orderps`;
CREATE TABLE `xiaozu_orderps` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `orderid` int(20) NOT NULL,
  `shopid` int(20) NOT NULL,
  `psuid` int(20) NOT NULL DEFAULT '0',
  `psusername` varchar(50) DEFAULT NULL,
  `psemail` varchar(50) DEFAULT NULL,
  `status` int(1) NOT NULL,
  `dno` varchar(100) NOT NULL,
  `addtime` int(12) NOT NULL,
  `pstime` int(12) NOT NULL,
  `psycost` decimal(5,2) NOT NULL,
  `picktime` int(20) NOT NULL DEFAULT '0',
  `dotype` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1555 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_orderps
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_orderstatus
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_orderstatus`;
CREATE TABLE `xiaozu_orderstatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` int(11) NOT NULL COMMENT '订单ID',
  `statustitle` varchar(255) NOT NULL COMMENT '状态',
  `ststusdesc` varchar(255) NOT NULL COMMENT '状态描述',
  `addtime` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=74508 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_orderstatus
-- ----------------------------
INSERT INTO `xiaozu_orderstatus` VALUES ('74506', '43241', '订单已提交', '请在15分钟内完成支付，逾期订单将自动取消', '1529754126');
INSERT INTO `xiaozu_orderstatus` VALUES ('74507', '43241', '订单已支付', '订单支付成功，等待商家接单', '1529754247');

-- ----------------------------
-- Table structure for xiaozu_otherlogin
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_otherlogin`;
CREATE TABLE `xiaozu_otherlogin` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `loginname` varchar(10) NOT NULL,
  `logindesc` varchar(100) NOT NULL,
  `logourl` varchar(255) NOT NULL,
  `addmeta` varchar(255) DEFAULT NULL COMMENT '附加meta内容',
  `temp` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_otherlogin
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_paotuiset
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_paotuiset`;
CREATE TABLE `xiaozu_paotuiset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kg` varchar(20) NOT NULL,
  `kgcost` decimal(10,2) NOT NULL,
  `addkg` varchar(20) NOT NULL,
  `addkgcost` decimal(10,2) NOT NULL,
  `km` varchar(20) NOT NULL,
  `kmcost` decimal(10,2) NOT NULL,
  `addkm` varchar(20) NOT NULL,
  `addkmcost` decimal(10,2) NOT NULL,
  `postdate` text NOT NULL COMMENT '跑腿取件或者收货时间',
  `is_ptorderbefore` int(1) NOT NULL DEFAULT '1' COMMENT '是否支持预定默认为1 支持预定',
  `pt_orderday` int(11) NOT NULL DEFAULT '1' COMMENT '预定天数 默认为1支持预定1天',
  `cityid` int(12) NOT NULL COMMENT '所属城市ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_paotuiset
-- ----------------------------
INSERT INTO `xiaozu_paotuiset` VALUES ('53', '3', '5.00', '1', '2.00', '3', '5.00', '1', '2.00', 'a:1:{i:0;a:3:{s:1:\"s\";i:28800;s:1:\"e\";i:64800;s:1:\"i\";s:0:\"\";}}', '1', '2', '410100');

-- ----------------------------
-- Table structure for xiaozu_paotuitask
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_paotuitask`;
CREATE TABLE `xiaozu_paotuitask` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dno` varchar(100) NOT NULL,
  `uid` int(11) NOT NULL,
  `content` varchar(255) NOT NULL,
  `name` varchar(25) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `addtime` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 未处理  1已接受 2 已完成3取消订单',
  `ordertype` int(11) NOT NULL,
  `ipaddress` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_paotuitask
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_paylist
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_paylist`;
CREATE TABLE `xiaozu_paylist` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `loginname` varchar(10) NOT NULL,
  `logindesc` varchar(100) NOT NULL,
  `logourl` varchar(255) NOT NULL,
  `addmeta` varchar(255) DEFAULT NULL COMMENT '附加meta内容',
  `temp` text NOT NULL,
  `type` int(1) NOT NULL DEFAULT '0' COMMENT '0表示网站使用，1表示手机使用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_paylist
-- ----------------------------
INSERT INTO `xiaozu_paylist` VALUES ('86', 'open_acout', '余额支付', '', null, '[]', '0');

-- ----------------------------
-- Table structure for xiaozu_paytype
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_paytype`;
CREATE TABLE `xiaozu_paytype` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `arrayset` text NOT NULL,
  `is_plug` int(1) NOT NULL COMMENT '0不调用接口，1调用接口文件',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_paytype
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_platpsset
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_platpsset`;
CREATE TABLE `xiaozu_platpsset` (
  `cityid` int(12) NOT NULL COMMENT '城市ID',
  `locationradius` int(5) NOT NULL COMMENT '配送半径',
  `radiusvalue` text NOT NULL COMMENT '配送设置序列化值',
  `psycostset` int(1) NOT NULL COMMENT '配送员提成设置 1固定提成 2 比例提成',
  `psycost` decimal(10,2) NOT NULL COMMENT '配送员固定提成金额',
  `psybili` varchar(10) NOT NULL COMMENT '配送员固定提成比例',
  `waimai_psrange` text NOT NULL COMMENT '平台配送范围坐标集',
  `pttopsb` int(1) DEFAULT '2',
  `ptpsblink` varchar(100) DEFAULT NULL,
  `ptpsbaccid` varchar(10) DEFAULT NULL,
  `ptpsbkey` varchar(100) DEFAULT NULL,
  `ptpsbcode` varchar(100) DEFAULT NULL,
  `wxkefu_open` int(1) DEFAULT '0',
  `wxkefu_ewm` text,
  `wxkefu_phone` varchar(20) DEFAULT NULL,
  `wxkefu_logo` text,
  `paytype` text COMMENT '分站支付方式0支持货到付款 1支持在线支付 0,1都支持',
  `is_allow_ziti` int(1) DEFAULT '0' COMMENT '分站是否开启自提 0未开启 1开启'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='平台配送设置表';

-- ----------------------------
-- Records of xiaozu_platpsset
-- ----------------------------
INSERT INTO `xiaozu_platpsset` VALUES ('410100', '5', 'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}', '0', '0.00', '', '[[113.623527,34.810422],[113.711417,34.810422],[113.762916,34.759103],[113.734077,34.674442],[113.675025,34.631514],[113.604987,34.638859],[113.533576,34.697591],[113.528083,34.776589],[113.587134,34.806476]]', '2', null, null, null, null, '0', '', '', 'http://image.ghwmr.com/images/410100/other/20180623172553591.png', '1,2', '1');

-- ----------------------------
-- Table structure for xiaozu_pmes
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_pmes`;
CREATE TABLE `xiaozu_pmes` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `uid` int(20) DEFAULT '0',
  `username` varchar(100) DEFAULT NULL,
  `usercontent` text,
  `userimg` varchar(255) DEFAULT NULL,
  `creattime` int(12) NOT NULL DEFAULT '0',
  `backuid` int(20) NOT NULL DEFAULT '0',
  `backcontent` text,
  `backimg` varchar(255) DEFAULT NULL,
  `backtime` int(12) NOT NULL DEFAULT '0',
  `backusername` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_pmes
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_positionkey
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_positionkey`;
CREATE TABLE `xiaozu_positionkey` (
  `datatype` int(1) NOT NULL,
  `parent_id` int(20) NOT NULL,
  `datacode` varchar(100) NOT NULL,
  `datacontent` varchar(100) NOT NULL,
  `lat` varchar(20) DEFAULT NULL,
  `lng` varchar(20) DEFAULT NULL,
  FULLTEXT KEY `datacode` (`datacode`),
  FULLTEXT KEY `datacontent` (`datacontent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_positionkey
-- ----------------------------
INSERT INTO `xiaozu_positionkey` VALUES ('2', '1213', 'guangheqijiandian', '光合旗舰店', '34.80233', '113.543806');
INSERT INTO `xiaozu_positionkey` VALUES ('2', '1213', 'ghqjd', '光合旗舰店', '34.80233', '113.543806');

-- ----------------------------
-- Table structure for xiaozu_printlog
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_printlog`;
CREATE TABLE `xiaozu_printlog` (
  `orderid` int(20) NOT NULL,
  `addtime` time DEFAULT NULL,
  `status` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_printlog
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_product
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_product`;
CREATE TABLE `xiaozu_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '子商品ID',
  `goodsid` int(20) NOT NULL COMMENT '主商品ID',
  `goodsname` varchar(255) DEFAULT NULL COMMENT '商品名称',
  `attrname` varchar(255) DEFAULT NULL COMMENT '属性描述',
  `attrids` varchar(255) NOT NULL COMMENT '包含规格值ID集，分割',
  `stock` int(5) NOT NULL DEFAULT '0' COMMENT '库存',
  `cost` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `shopid` int(20) NOT NULL DEFAULT '0',
  `bagcost` decimal(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3140 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_product
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_rechargecost
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_rechargecost`;
CREATE TABLE `xiaozu_rechargecost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cost` decimal(12,2) NOT NULL COMMENT '充值金额',
  `is_sendcost` int(1) NOT NULL DEFAULT '1' COMMENT '是否赠送账户余额   默认1赠送0不',
  `sendcost` decimal(12,2) NOT NULL COMMENT '赠送金额',
  `is_sendjuan` int(1) NOT NULL COMMENT '是否赠送优惠券  默认0不赠送1赠送',
  `sendjuancost` decimal(12,2) DEFAULT NULL COMMENT '赠送优惠券总额',
  `orderid` int(11) NOT NULL COMMENT '排序',
  `juanid` int(5) DEFAULT NULL COMMENT '绑定优惠券id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_rechargecost
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_regsendjuan
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_regsendjuan`;
CREATE TABLE `xiaozu_regsendjuan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `limitcost` decimal(10,2) NOT NULL,
  `jiancost` decimal(10,2) NOT NULL,
  `starttime` int(11) NOT NULL,
  `endtime` int(11) NOT NULL,
  `is_open` int(1) NOT NULL DEFAULT '0' COMMENT '默认0开启 1不开启',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_regsendjuan
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_rule
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_rule`;
CREATE TABLE `xiaozu_rule` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '规则名称',
  `type` int(1) NOT NULL COMMENT '默认1，购物车总价',
  `limittype` int(1) NOT NULL COMMENT '是否指定具体时间1否2指定星期3指定小时',
  `limittime` varchar(255) NOT NULL COMMENT '具体时间：周几，或者具体时间',
  `limitcontent` text NOT NULL COMMENT '限制内容购物车总价',
  `controltype` int(1) NOT NULL COMMENT '规则类型：1赠，3折扣，2减费用',
  `controlcontent` text COMMENT '限制内容填写赠品ID，折扣率，费用等大于0',
  `starttime` int(11) NOT NULL COMMENT '开始时间',
  `endtime` int(11) NOT NULL COMMENT '结束时间',
  `status` tinyint(1) NOT NULL COMMENT '状态1有效 2无效',
  `shopid` text,
  `presenttitle` varchar(255) DEFAULT NULL COMMENT '赠品标题',
  `signid` int(20) NOT NULL,
  `cattype` int(1) NOT NULL DEFAULT '1' COMMENT '1外卖 2订台',
  `supporttype` varchar(150) NOT NULL COMMENT '支持类型：1首单有效，2在线支付有效',
  `supportplatform` varchar(150) NOT NULL COMMENT '支持平台：1pc端，2微信端，3触屏端，4客户端（安卓苹果）',
  `shopbili` int(3) DEFAULT '0' COMMENT '促销金额中平台承担的比例',
  `parentid` int(1) DEFAULT '0' COMMENT '商家设置的为0   平台设置的为1',
  `cityid` int(20) DEFAULT '0' COMMENT '促销规则的隶属分站',
  `imgurl` varchar(255) DEFAULT NULL COMMENT '活动图标地址',
  `creattime` int(11) DEFAULT NULL COMMENT '活动创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1252 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_rule
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_score_log
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_score_log`;
CREATE TABLE `xiaozu_score_log` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `uid` int(20) NOT NULL DEFAULT '0',
  `type` int(1) NOT NULL DEFAULT '0',
  `score` int(7) NOT NULL DEFAULT '0',
  `title` varchar(100) DEFAULT NULL,
  `content` text,
  `addtime` int(12) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_score_log
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_searchlog
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_searchlog`;
CREATE TABLE `xiaozu_searchlog` (
  `uid` int(11) NOT NULL COMMENT '用户UID',
  `searchval` varchar(255) NOT NULL COMMENT '搜索关键词',
  `searchtime` int(11) NOT NULL COMMENT '搜索时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_searchlog
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_searkey
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_searkey`;
CREATE TABLE `xiaozu_searkey` (
  `type` int(1) NOT NULL COMMENT '1外卖，2订台，3商城',
  `goid` int(20) NOT NULL,
  `keycontent` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  FULLTEXT KEY `keycontent` (`keycontent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_searkey
-- ----------------------------
INSERT INTO `xiaozu_searkey` VALUES ('1', '1213', 'guangheqijiandian', '光合旗舰店');
INSERT INTO `xiaozu_searkey` VALUES ('1', '1213', 'ghqjd', '光合旗舰店');

-- ----------------------------
-- Table structure for xiaozu_sharealog
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_sharealog`;
CREATE TABLE `xiaozu_sharealog` (
  `uid` int(20) NOT NULL,
  `childusername` varchar(100) DEFAULT NULL,
  `titile` varchar(255) DEFAULT NULL,
  `addtime` int(12) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `id` int(20) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_sharealog
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_shop
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_shop`;
CREATE TABLE `xiaozu_shop` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `uid` int(20) NOT NULL COMMENT '用户ID',
  `shortname` varchar(10) DEFAULT NULL COMMENT '店铺短地址',
  `shopname` varchar(150) NOT NULL COMMENT '店铺名称',
  `shoplogo` varchar(150) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL COMMENT '联系电话',
  `address` varchar(150) DEFAULT NULL COMMENT '联系地址',
  `point` int(10) NOT NULL DEFAULT '5' COMMENT '评分',
  `cx_info` text COMMENT '促销信息',
  `intr_info` text COMMENT '介绍信息',
  `notice_info` text COMMENT '公告信息',
  `starttime` varchar(100) DEFAULT NULL COMMENT '营业时间',
  `is_open` int(1) NOT NULL DEFAULT '0' COMMENT '是否营业',
  `is_onlinepay` int(1) NOT NULL DEFAULT '1' COMMENT '是否开启在线支付：默认为1开启，0不开启',
  `is_daopay` int(1) NOT NULL DEFAULT '1' COMMENT '是否开启到付：默认为1开启，0不开启',
  `is_pass` int(1) NOT NULL DEFAULT '0' COMMENT '是否通过审核',
  `is_recom` int(1) NOT NULL DEFAULT '0' COMMENT '是否是推荐店铺',
  `maphone` varchar(12) DEFAULT NULL,
  `addtime` int(12) DEFAULT NULL,
  `pointcount` int(5) NOT NULL DEFAULT '1',
  `psservicepoint` int(11) NOT NULL DEFAULT '5' COMMENT '配送服务评分',
  `psservicepointcount` int(11) NOT NULL DEFAULT '1' COMMENT '配送服务评论次数',
  `yjin` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '佣金比例当为0时调用网站设置',
  `lat` decimal(9,6) NOT NULL DEFAULT '0.000000' COMMENT '地图左坐标',
  `lng` decimal(9,6) NOT NULL DEFAULT '0.000000' COMMENT '地图右坐标',
  `shopbacklogo` varchar(255) DEFAULT NULL COMMENT '商家默认背景图片',
  `sort` int(20) NOT NULL DEFAULT '999' COMMENT '排序',
  `email` varchar(50) DEFAULT NULL,
  `areaid1` int(20) NOT NULL DEFAULT '0',
  `areaid2` int(20) NOT NULL DEFAULT '0',
  `areaid3` int(20) NOT NULL DEFAULT '0',
  `otherlink` varchar(255) DEFAULT NULL COMMENT '其他链接地址',
  `endtime` int(11) NOT NULL DEFAULT '0',
  `IMEI` varchar(255) DEFAULT NULL,
  `shoptype` int(2) NOT NULL DEFAULT '0',
  `noticetype` varchar(100) DEFAULT NULL,
  `machine_code` varchar(255) DEFAULT NULL COMMENT '打印2机器码',
  `mKey` varchar(255) DEFAULT NULL COMMENT '打印2打印密匙',
  `pradiusa` int(1) NOT NULL DEFAULT '3',
  `admin_id` int(20) NOT NULL DEFAULT '0',
  `sellcount` int(10) DEFAULT '0',
  `goodattrdefault` varchar(25) NOT NULL,
  `ruzhutype` int(11) NOT NULL DEFAULT '0',
  `qiyeimg` varchar(150) NOT NULL,
  `zmimg` varchar(150) NOT NULL,
  `fmimg` varchar(150) NOT NULL,
  `foodtongimg` varchar(150) DEFAULT NULL,
  `jkzimg` varchar(150) DEFAULT NULL,
  `sqimg` varchar(150) DEFAULT NULL,
  `qtshuoming` varchar(255) DEFAULT NULL,
  `wxhui_ewmurl` text NOT NULL COMMENT '闪惠制作二维码url',
  `goodlistmodule` int(1) NOT NULL DEFAULT '0' COMMENT '商品列表模板默认为0   ',
  `shoplicense` varchar(150) NOT NULL COMMENT '营业执照',
  `virtualsellcounts` int(11) DEFAULT NULL COMMENT '店铺虚拟总销量',
  `psblink` varchar(50) NOT NULL DEFAULT '' COMMENT '配送宝链接',
  `psbaccid` varchar(50) NOT NULL DEFAULT '' COMMENT '配送宝商家id',
  `psbkey` varchar(50) NOT NULL DEFAULT '' COMMENT '配送宝key',
  `psbcode` varchar(50) NOT NULL DEFAULT '' COMMENT '配送宝code',
  `psbversion` varchar(50) NOT NULL DEFAULT '' COMMENT '配送宝版本',
  `is_selfsitecx` int(1) DEFAULT '0' COMMENT '是否允许店铺自行设置促销规则  1允许  0不允许',
  `isforyou` int(2) NOT NULL DEFAULT '0' COMMENT '是否显示在优选商家里面 1是  0否',
  `is_autopreceipt` int(2) DEFAULT '0' COMMENT '是否开启自动接单  0未开启 1开启',
  `ordercount` int(11) DEFAULT '0' COMMENT '店铺订单总数 订单完成该值自动增加',
  `is_ziti` int(1) DEFAULT '0' COMMENT '店铺是否开启自提 0未开启 1开启',
  `ziti_time` int(2) DEFAULT '15' COMMENT '到店自取备餐时间',
  `zitiyjb` decimal(10,2) DEFAULT '10.00' COMMENT '自提订单佣金比例',
  `zitilimityj` decimal(10,2) DEFAULT '0.00' COMMENT '自提订单佣金不满x元按y元计算中的x',
  `zitianyj` decimal(10,2) DEFAULT '0.00' COMMENT '自提订单佣金不满x元按y元计算中的y',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1214 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_shop
-- ----------------------------
INSERT INTO `xiaozu_shop` VALUES ('1213', '22052', '', '光合旗舰店', null, '15038875888', '河南省电子商务产业园', '5', null, '店铺介绍', '店铺公告', '00:01-23:00', '1', '0', '0', '1', '0', '15236985214', '1529750563', '1', '5', '1', '2.00', '34.802330', '113.543806', null, '999', 'guanghe@qq.com', '0', '0', '0', '', '1561286563', '', '0', null, null, null, '3', '410100', '0', '份', '0', '', '', '', null, null, null, null, '', '0', '', null, '', '', '', '', '', '0', '1', '0', '0', '1', '15', '10.00', '0.00', '0.00');

-- ----------------------------
-- Table structure for xiaozu_shopattr
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_shopattr`;
CREATE TABLE `xiaozu_shopattr` (
  `shopid` int(20) NOT NULL,
  `cattype` int(1) NOT NULL DEFAULT '0' COMMENT '1外卖2订台',
  `firstattr` int(20) NOT NULL DEFAULT '0',
  `attrid` int(20) NOT NULL DEFAULT '0' COMMENT '该属性ID',
  `value` varchar(255) DEFAULT NULL COMMENT '值'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_shopattr
-- ----------------------------
INSERT INTO `xiaozu_shopattr` VALUES ('1213', '0', '2', '486', '当地特色');
INSERT INTO `xiaozu_shopattr` VALUES ('1213', '0', '2', '487', '夜市烧烤');
INSERT INTO `xiaozu_shopattr` VALUES ('1213', '0', '2', '488', '精品小吃');

-- ----------------------------
-- Table structure for xiaozu_shopcateadv
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_shopcateadv`;
CREATE TABLE `xiaozu_shopcateadv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shoptype` varchar(20) NOT NULL COMMENT '店铺类型',
  `cateid` int(12) NOT NULL COMMENT '分类ID',
  `title` varchar(255) NOT NULL COMMENT 'titile',
  `img` text NOT NULL COMMENT '图片',
  `desc` varchar(255) NOT NULL COMMENT '描述',
  `link` varchar(255) NOT NULL COMMENT '链接',
  `addtime` int(12) NOT NULL COMMENT '添加时间',
  `orderid` int(12) NOT NULL COMMENT '排序',
  `cityid` int(12) NOT NULL COMMENT '城市ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='店铺分类广告图';

-- ----------------------------
-- Records of xiaozu_shopcateadv
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_shopfast
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_shopfast`;
CREATE TABLE `xiaozu_shopfast` (
  `shopid` int(20) NOT NULL,
  `is_orderbefore` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0不支持提前预定，1支持',
  `delaytime` int(5) NOT NULL DEFAULT '0' COMMENT '营业时间和下单时间补差',
  `limitcost` int(3) NOT NULL DEFAULT '0' COMMENT '起送价格',
  `limitstro` varchar(255) DEFAULT NULL COMMENT '起送说明',
  `befortime` int(1) NOT NULL DEFAULT '0' COMMENT '起送提前天数',
  `personcost` int(5) NOT NULL DEFAULT '0' COMMENT '人均消费价格',
  `sendtype` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0网站配送，1自行配送',
  `is_hot` int(1) NOT NULL DEFAULT '0' COMMENT '热卖',
  `is_com` int(1) NOT NULL DEFAULT '0' COMMENT '推荐',
  `is_new` int(1) NOT NULL COMMENT '新店',
  `maketime` int(5) DEFAULT '0',
  `pradius` text,
  `pradiusvalue` text,
  `pscost` int(2) NOT NULL DEFAULT '0',
  `is_waimai` int(1) DEFAULT '1',
  `is_goshop` int(11) NOT NULL DEFAULT '0',
  `personcount` int(2) DEFAULT '0',
  `arrivetime` varchar(20) DEFAULT NULL,
  `discount` int(1) DEFAULT '0',
  `postdate` text,
  `is_hui` int(1) NOT NULL DEFAULT '0' COMMENT '管理员开启闪惠默认0为未开启 1开启',
  `is_shophui` int(1) NOT NULL DEFAULT '0' COMMENT '商家开启闪惠默认0为未开启 1开启',
  `is_shgift` int(1) NOT NULL DEFAULT '0' COMMENT '商家是否开启送积分',
  `sendgift` int(11) NOT NULL COMMENT '多少元赠送1积分',
  `is_timeduan` int(1) NOT NULL DEFAULT '0' COMMENT '是否使用时间段，默认0不使用，直接显示立即配送',
  `interval_minit` int(2) NOT NULL DEFAULT '30' COMMENT '分钟数',
  `is_sendqianjuan` int(1) NOT NULL DEFAULT '0' COMMENT '是否下单前领取代金券 默认0关闭',
  `is_sendhoujuan` int(1) NOT NULL DEFAULT '0' COMMENT '是否下单后赠送代金券 默认0关闭',
  UNIQUE KEY `shopid` (`shopid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_shopfast
-- ----------------------------
INSERT INTO `xiaozu_shopfast` VALUES ('1213', '0', '0', '10', '', '0', '0', '1', '0', '0', '0', '0', '3', 'a:3:{i:0;s:1:\"0\";i:1;s:1:\"0\";i:2;s:1:\"0\";}', '0', '0', '0', '0', '20', '0', 'a:23:{i:0;a:4:{s:1:\"s\";i:60;s:1:\"e\";i:3660;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:1;a:4:{s:1:\"s\";i:3660;s:1:\"e\";i:7260;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:2;a:4:{s:1:\"s\";i:7260;s:1:\"e\";i:10860;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:3;a:4:{s:1:\"s\";i:10860;s:1:\"e\";i:14460;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:4;a:4:{s:1:\"s\";i:14460;s:1:\"e\";i:18060;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:5;a:4:{s:1:\"s\";i:18060;s:1:\"e\";i:21660;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:6;a:4:{s:1:\"s\";i:21660;s:1:\"e\";i:25260;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:7;a:4:{s:1:\"s\";i:25260;s:1:\"e\";i:28860;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:8;a:4:{s:1:\"s\";i:28860;s:1:\"e\";i:32460;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:9;a:4:{s:1:\"s\";i:32460;s:1:\"e\";i:36060;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:10;a:4:{s:1:\"s\";i:36060;s:1:\"e\";i:39660;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:11;a:4:{s:1:\"s\";i:39660;s:1:\"e\";i:43260;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:12;a:4:{s:1:\"s\";i:43260;s:1:\"e\";i:46860;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:13;a:4:{s:1:\"s\";i:46860;s:1:\"e\";i:50460;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:14;a:4:{s:1:\"s\";i:50460;s:1:\"e\";i:54060;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:15;a:4:{s:1:\"s\";i:54060;s:1:\"e\";i:57660;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:16;a:4:{s:1:\"s\";i:57660;s:1:\"e\";i:61260;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:17;a:4:{s:1:\"s\";i:61260;s:1:\"e\";i:64860;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:18;a:4:{s:1:\"s\";i:64860;s:1:\"e\";i:68460;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:19;a:4:{s:1:\"s\";i:68460;s:1:\"e\";i:72060;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:20;a:4:{s:1:\"s\";i:72060;s:1:\"e\";i:75660;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:21;a:4:{s:1:\"s\";i:75660;s:1:\"e\";i:79260;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}i:22;a:4:{s:1:\"s\";i:79260;s:1:\"e\";i:82800;s:1:\"i\";s:0:\"\";s:4:\"cost\";s:0:\"\";}}', '0', '0', '0', '0', '0', '60', '0', '0');

-- ----------------------------
-- Table structure for xiaozu_shophui
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_shophui`;
CREATE TABLE `xiaozu_shophui` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '规则名称',
  `limittype` int(1) NOT NULL COMMENT '是否指定具体时间1否2指定星期3指定小时',
  `limitweek` varchar(255) NOT NULL COMMENT '具体时间：周几',
  `limittimes` text NOT NULL COMMENT '限制每天具体时间',
  `mjlimitcost` int(11) NOT NULL COMMENT '每满费用金额',
  `limitzhekoucost` int(11) NOT NULL COMMENT '折扣限制金额',
  `controltype` int(1) NOT NULL COMMENT '规则类型：1赠，3折扣，2减费用',
  `controlcontent` int(20) DEFAULT NULL COMMENT '限制内容填写赠品ID，折扣率，费用等大于0',
  `starttime` int(11) NOT NULL COMMENT '开始时间',
  `endtime` int(11) NOT NULL COMMENT '结束时间',
  `status` tinyint(1) NOT NULL COMMENT '状态1有效 2无效',
  `shopid` int(20) NOT NULL COMMENT '店铺id',
  `signid` int(20) NOT NULL,
  `cattype` int(1) NOT NULL DEFAULT '1' COMMENT '1外卖 2订台',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=95 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_shophui
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_shophuiorder
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_shophuiorder`;
CREATE TABLE `xiaozu_shophuiorder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户uid',
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `buyorderphone` varchar(100) NOT NULL,
  `dno` varchar(25) NOT NULL COMMENT '买单号',
  `shopid` int(11) NOT NULL COMMENT '店铺ID',
  `shopname` varchar(255) NOT NULL COMMENT '店铺名称',
  `xfcost` decimal(10,2) NOT NULL COMMENT '消费金额',
  `yhcost` decimal(10,2) NOT NULL COMMENT '优惠金额',
  `sjcost` decimal(10,2) NOT NULL COMMENT '实际支付金额',
  `givejifen` varchar(30) NOT NULL,
  `huiid` int(11) NOT NULL COMMENT '闪慧ID',
  `huiname` varchar(255) NOT NULL COMMENT '闪慧名称',
  `huitype` int(1) NOT NULL COMMENT '2是每满减 3是折扣',
  `huilimitcost` decimal(10,2) NOT NULL COMMENT '最低达到金额限制',
  `huicost` decimal(10,2) NOT NULL COMMENT '减金额',
  `paytype` int(11) NOT NULL COMMENT '1是微信支付',
  `paystatus` int(1) NOT NULL DEFAULT '0' COMMENT '0是未付1是已付',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0是未完成是已完成',
  `addtime` int(11) NOT NULL COMMENT '创建时间',
  `completetime` int(11) NOT NULL DEFAULT '0' COMMENT '支付买单完成时间',
  `admin_id` int(20) NOT NULL DEFAULT '0',
  `paytime` int(11) NOT NULL COMMENT '支付时间',
  `paytype_name` varchar(30) DEFAULT NULL COMMENT '支付类型',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1056 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_shophuiorder
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_shopjs
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_shopjs`;
CREATE TABLE `xiaozu_shopjs` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `onlinecost` decimal(7,1) NOT NULL,
  `onlinecount` int(5) NOT NULL,
  `unlinecount` int(11) NOT NULL,
  `unlinecost` decimal(7,2) NOT NULL,
  `yjb` varchar(30) DEFAULT NULL,
  `yjcost` decimal(5,2) NOT NULL COMMENT '佣金比例',
  `pstype` int(1) NOT NULL,
  `shopid` int(20) NOT NULL,
  `shopuid` int(20) NOT NULL,
  `acountcost` varchar(30) DEFAULT NULL,
  `addtime` int(12) NOT NULL,
  `jstime` int(12) NOT NULL,
  `jsid` int(20) NOT NULL DEFAULT '0',
  `orderid` int(12) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21484 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_shopjs
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_shopmarket
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_shopmarket`;
CREATE TABLE `xiaozu_shopmarket` (
  `shopid` int(20) NOT NULL,
  `is_orderbefore` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0不支持提前预定，1支持',
  `delaytime` int(5) NOT NULL DEFAULT '0' COMMENT '营业时间和下单时间补差',
  `limitcost` int(3) NOT NULL DEFAULT '0' COMMENT '起送价格',
  `limitstro` varchar(255) DEFAULT NULL COMMENT '起送说明',
  `befortime` int(1) NOT NULL DEFAULT '0' COMMENT '起送提前天数',
  `sendtype` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0网站配送，1自行配送',
  `is_hot` int(1) NOT NULL DEFAULT '0' COMMENT '热卖',
  `is_com` int(1) NOT NULL DEFAULT '0' COMMENT '推荐',
  `is_new` int(1) NOT NULL COMMENT '新店',
  `maketime` int(5) DEFAULT '0',
  `pradius` int(1) NOT NULL DEFAULT '3',
  `pradiusvalue` text,
  `pscost` int(2) DEFAULT '0',
  `arrivetime` varchar(20) DEFAULT NULL,
  `postdate` text,
  `is_hui` int(1) NOT NULL DEFAULT '0' COMMENT '管理员开启闪惠默认0为未开启 1开启',
  `is_shophui` int(1) NOT NULL DEFAULT '0' COMMENT '商家开启闪惠默认0为未开启 1开启',
  `is_shgift` int(1) NOT NULL DEFAULT '0' COMMENT '商家是否开启送积分',
  `sendgift` int(11) NOT NULL COMMENT '多少元赠送1积分',
  `is_timeduan` int(1) NOT NULL DEFAULT '0' COMMENT '是否使用时间段，默认0不使用，直接显示立即配送',
  `interval_minit` int(2) NOT NULL DEFAULT '30' COMMENT '分钟数',
  `is_sendqianjuan` int(1) NOT NULL DEFAULT '0' COMMENT '是否下单前领取代金券 默认0关闭',
  `is_sendhoujuan` int(1) NOT NULL DEFAULT '0' COMMENT '是否下单后赠送代金券 默认0关闭',
  UNIQUE KEY `shopid` (`shopid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_shopmarket
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_shopreal
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_shopreal`;
CREATE TABLE `xiaozu_shopreal` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `shopid` int(20) NOT NULL,
  `name` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=257 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_shopreal
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_shoprealimg
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_shoprealimg`;
CREATE TABLE `xiaozu_shoprealimg` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `parent_id` int(20) NOT NULL,
  `img` varchar(250) NOT NULL,
  `imgname` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=358 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_shoprealimg
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_shopreport
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_shopreport`;
CREATE TABLE `xiaozu_shopreport` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `typeidContent` text NOT NULL,
  `shopname` varchar(250) NOT NULL,
  `content` varchar(250) NOT NULL,
  `addtime` int(12) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=164 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_shopreport
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_shopsearch
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_shopsearch`;
CREATE TABLE `xiaozu_shopsearch` (
  `shopid` int(20) NOT NULL,
  `parent_id` int(20) NOT NULL DEFAULT '0',
  `second_id` int(20) NOT NULL DEFAULT '0',
  `cattype` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1外卖2订台'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_shopsearch
-- ----------------------------
INSERT INTO `xiaozu_shopsearch` VALUES ('1213', '2', '488', '0');
INSERT INTO `xiaozu_shopsearch` VALUES ('1213', '2', '487', '0');
INSERT INTO `xiaozu_shopsearch` VALUES ('1213', '2', '486', '0');

-- ----------------------------
-- Table structure for xiaozu_shoptx
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_shoptx`;
CREATE TABLE `xiaozu_shoptx` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `cost` decimal(7,2) NOT NULL DEFAULT '0.00',
  `type` int(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `addtime` int(12) NOT NULL,
  `shopid` int(20) NOT NULL,
  `shopuid` int(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `yue` double(8,2) NOT NULL,
  `jsid` int(20) NOT NULL DEFAULT '0',
  `changetype` int(1) DEFAULT '2' COMMENT '资金增加类型   1减少   2增加',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22158 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_shoptx
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_shoptype
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_shoptype`;
CREATE TABLE `xiaozu_shoptype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL COMMENT '分类名',
  `type` varchar(100) DEFAULT NULL COMMENT 'checkbox多选，radio单选，img图片，input输入框',
  `parent_id` int(20) NOT NULL DEFAULT '0' COMMENT '0表示导航明，1值',
  `cattype` int(1) NOT NULL DEFAULT '1' COMMENT '1外卖2订台3其他',
  `is_search` int(1) NOT NULL DEFAULT '0' COMMENT '0不是搜索关键字1搜索关键字',
  `is_main` int(1) NOT NULL DEFAULT '0' COMMENT '是否展示在店铺列表 0否1是',
  `is_admin` int(1) NOT NULL DEFAULT '0' COMMENT '设置类型0店铺1后台',
  `instro` varchar(255) DEFAULT NULL COMMENT '简单介绍',
  `orderid` int(10) NOT NULL DEFAULT '0' COMMENT '排序ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=489 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_shoptype
-- ----------------------------
INSERT INTO `xiaozu_shoptype` VALUES ('1', '超市', 'checkbox', '0', '1', '1', '1', '1', null, '0');
INSERT INTO `xiaozu_shoptype` VALUES ('2', '外卖', 'checkbox', '0', '0', '1', '1', '1', null, '0');
INSERT INTO `xiaozu_shoptype` VALUES ('483', '24h便利店', '0', '1', '1', '0', '0', '0', '', '0');
INSERT INTO `xiaozu_shoptype` VALUES ('484', '水果超市', '0', '1', '1', '0', '0', '0', '', '0');
INSERT INTO `xiaozu_shoptype` VALUES ('485', '医药超市', '0', '1', '1', '0', '0', '0', '', '0');
INSERT INTO `xiaozu_shoptype` VALUES ('486', '当地特色', '0', '2', '0', '0', '0', '0', '', '0');
INSERT INTO `xiaozu_shoptype` VALUES ('487', '夜市烧烤', '0', '2', '0', '0', '0', '0', '', '0');
INSERT INTO `xiaozu_shoptype` VALUES ('488', '精品小吃', '0', '2', '0', '0', '0', '0', '', '0');

-- ----------------------------
-- Table structure for xiaozu_shopwait
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_shopwait`;
CREATE TABLE `xiaozu_shopwait` (
  `shopid` int(20) NOT NULL,
  `personcost` decimal(5,0) NOT NULL DEFAULT '0' COMMENT '人均消费',
  `befortime` int(2) NOT NULL DEFAULT '14' COMMENT '提前预定天数',
  `maxperson` int(3) NOT NULL DEFAULT '10' COMMENT '最大消费人数',
  `position` text COMMENT '使用,分割',
  `is_hot` int(1) NOT NULL DEFAULT '0' COMMENT '热卖',
  `is_com` int(1) NOT NULL DEFAULT '0' COMMENT '推荐',
  `is_new` int(1) NOT NULL COMMENT '新店',
  `timesplit` int(11) NOT NULL DEFAULT '30' COMMENT '时间间隔',
  `limitcost` int(5) DEFAULT '0' COMMENT '起订价',
  UNIQUE KEY `shopid` (`shopid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_shopwait
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_shopzt
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_shopzt`;
CREATE TABLE `xiaozu_shopzt` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `shopid` int(20) DEFAULT NULL COMMENT '所属店铺id',
  `title` text COMMENT '专题名称',
  `ztimg` text COMMENT '专题图片',
  `goodids` text COMMENT '包含商品id集',
  `is_show` int(1) DEFAULT '0' COMMENT '是否显示  1显示 2隐藏 ',
  `sort` int(5) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_shopzt
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_single
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_single`;
CREATE TABLE `xiaozu_single` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `title` varchar(100) NOT NULL,
  `code` varchar(10) NOT NULL,
  `addtime` int(11) NOT NULL,
  `seo_key` varchar(255) DEFAULT NULL,
  `seo_content` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_single
-- ----------------------------
INSERT INTO `xiaozu_single` VALUES ('19', '<h3 style=\"padding:0px;line-height:40px;margin:10px 0px;font-family:宋体, 微软雅黑;color:#333333;font-size:24px;text-rendering:optimizeLegibility;\">前言：</h3>\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">本服务协议双方分别为郑州光合科技有限公司旗下网站“外卖人”（以下简称“外卖人”）与“外卖人”网站用户，本服务协议具有合同效力。用户必须为具备完全民事行为能力的自然人，或者是具有合法经营资格的实体组织。</span><br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">用户确认本服务协议后，本服务协议即在用户和“外卖人”之间产生法律效力。请用户务必在注册之前认真阅读全部服务协议内容，如有任何疑问，可向“外卖人”咨询。&nbsp;</span><br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">本服务协议内容包括“外卖人”已经发布的或将来可能发布的各类规则。所有规则为协议不可分割的一部分，与协议正文具有同等法律效力。用户在使用“外卖人”提供的各项服务的同时，承诺接受并遵守各项相关规则的规定。无论用户事实上是否在注册之前认真阅读了本服务协议，默认同意此协议并按照“外卖人”注册程序成功注册为用户，用户的行为仍然表示其同意并签署了本服务协议。</span><br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<h3 style=\"padding:0px;line-height:40px;margin:10px 0px;font-family:宋体, 微软雅黑;color:#333333;font-size:24px;text-rendering:optimizeLegibility;\">双方权利与义务：</h3>\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">用户登录“外卖人”网站，并按要求填写相关信息并确认同意履行相关用户协议的过程。用户因进行交易，获取订餐、订外卖的有偿服务或接触“外卖人”网站服务器而发生的所有应纳税赋，以及一切硬件、软件、服务及其他方面的费用均由用户负责支付。“外卖人”网站仅作为订餐、订外卖的交易地点。</span><br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">用户有义务在注册时提供自己的真实资料，并保证诸如电子邮件地址、联系电话、联系地址等内容的有效性及安全性，保证“外卖人”及其他用户可以通过上述联系方式与自己进行联系。同时，用户也有义务在相关资料实际变更时及时更新有关注册资料。用户保证不以他人资料在“外卖人”网站进行注册或认证。若用户使用虚假电话、姓名、地址或冒用他人信息使用“外卖人”订餐服务，“外卖人”将做出相应处罚或屏蔽地址的处理；对“外卖人”造成经济损失的，“外卖人”将保留追究法律责任的权利；用户不应在“外卖人”网站上订餐、订外卖交易平台上恶意评价其他用户，或采取不正当手段提高自身的信用度或降低其他用户的信用度。</span><br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">对于用户在“外卖人”网站上订餐、订外卖交易平台上的不当行为或其它任何“外卖人”认为应当终止服务的情况，“外卖人”有权随时做出删除相关信息、终止服务提供等处理，而无须征得用户的同意。</span><br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">用户在“外卖人”网站上订餐、订外卖交易过程中如与其他用户因交易产生纠纷，请求“外卖人”从中予以调处，经“外卖人”审核后，“外卖人”有权通过电话或电子邮件联系向纠纷双方了解情况，并将所了解的情况通过电话或电子邮件互相通知对方；</span><br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">“外卖人”有权对用户的注册资料及交易行为进行查阅，发现注册资料或订餐、订外卖交易行为中存在任何问题或怀疑，均有权向用户发出询问及要求改正的通知或者直接做出删除等处理；</span><br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">“外卖人”有义务在现有技术上维护整个网站上订餐、订外卖交易平台的正常运行，并努力提升和改进技术，使用户网站上订餐、订外卖交易活动得以顺利进行。</span><br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">对用户在注册使用“外卖人”网站上订餐、订外卖交易平台中所遇到的与交易或注册有关的问题及反映的情况，“外卖人”应及时做出回复。</span><br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">用户因在“外卖人”网站上订餐、订外卖交易与其他用户产生诉讼的，用户通过司法部门或行政部门依照法定程序要求“外卖人”提供相关资料，外卖人应积极配合并提供有关资料；</span><br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<h3 style=\"padding:0px;line-height:40px;margin:10px 0px;font-family:宋体, 微软雅黑;color:#333333;font-size:24px;text-rendering:optimizeLegibility;\">安全：</h3>\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">外卖人账户均有密码保护功能，请妥善保管用户的账户及密码信息；</span><br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">在使用“外卖人”服务进行网站上订餐、订外卖交易时，用户不可避免的要向订餐、订外卖交易对方或潜在的订餐、订外卖交易对方提供自己的个人信息，如联络方式或送餐地址。请用户妥善保护自己的个人信息，仅在必要的情形下向他人提供；</span><br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">如果用户发现自己的个人信息泄密，尤其是“外卖人”账户或及密码发生泄露，请用户立即联络“外卖人”客服，以便“外卖人”采取相应措施。</span><br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<br style=\"padding:0px;line-height:20px;margin:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\" />\r\n<h3 style=\"padding:0px;line-height:40px;margin:10px 0px;font-family:宋体, 微软雅黑;color:#333333;font-size:24px;text-rendering:optimizeLegibility;\">“外卖人”保留对本协议做出不定时修改的权利</h3>\r\n<p style=\"margin-top:10px;margin-bottom:10px;padding:0px;line-height:40px;font-family:宋体, 微软雅黑;color:#333333;font-size:24px;text-rendering:optimizeLegibility;\">&nbsp;</p>\r\n<p style=\"margin-top:10px;margin-bottom:10px;padding:0px;line-height:40px;font-family:宋体, 微软雅黑;color:#333333;font-size:24px;text-rendering:optimizeLegibility;\">&nbsp;</p>', '商家入驻协议', 'shopxieyi', '1509897600', '商家入驻协议', '商家入驻协议');
INSERT INTO `xiaozu_single` VALUES ('18', '<div style=\"text-align:center;padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\">\r\n<h2 style=\"padding-bottom:0px;line-height:40px;margin:10px 0px;padding-left:0px;padding-right:0px;font-family:inherit;font-size:30px;padding-top:0px;text-rendering:optimizelegibility;\">尊敬的用户，欢迎阅读”外卖人”服务协议</h2>\r\n</div>\r\n<h3 style=\"padding-bottom:0px;line-height:40px;margin:10px 0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:24px;padding-top:0px;text-rendering:optimizelegibility;\">前言：</h3>\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">本服务协议双方分别为郑州光合科技有限公司旗下网站“外卖人”（以下简称“外卖人”）与“外卖人”网站用户，本服务协议具有合同效力。用户必须为具备完全民事行为能力的自然人，或者是具有合法经营资格的实体组织。</span><br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">用户确认本服务协议后，本服务协议即在用户和“外卖人”之间产生法律效力。请用户务必在注册之前认真阅读全部服务协议内容，如有任何疑问，可向“外卖人”咨询。&nbsp;</span><br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">本服务协议内容包括“外卖人”已经发布的或将来可能发布的各类规则。所有规则为协议不可分割的一部分，与协议正文具有同等法律效力。用户在使用“外卖人”提供的各项服务的同时，承诺接受并遵守各项相关规则的规定。无论用户事实上是否在注册之前认真阅读了本服务协议，默认同意此协议并按照“外卖人”注册程序成功注册为用户，用户的行为仍然表示其同意并签署了本服务协议。</span><br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<h3 style=\"padding-bottom:0px;line-height:40px;margin:10px 0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:24px;padding-top:0px;text-rendering:optimizelegibility;\">双方权利与义务：</h3>\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">用户登录“外卖人”网站，并按要求填写相关信息并确认同意履行相关用户协议的过程。用户因进行交易，获取订餐、订外卖的有偿服务或接触“外卖人”网站服务器而发生的所有应纳税赋，以及一切硬件、软件、服务及其他方面的费用均由用户负责支付。“外卖人”网站仅作为订餐、订外卖的交易地点。</span><br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">用户有义务在注册时提供自己的真实资料，并保证诸如电子邮件地址、联系电话、联系地址等内容的有效性及安全性，保证“外卖人”及其他用户可以通过上述联系方式与自己进行联系。同时，用户也有义务在相关资料实际变更时及时更新有关注册资料。用户保证不以他人资料在“外卖人”网站进行注册或认证。若用户使用虚假电话、姓名、地址或冒用他人信息使用“外卖人”订餐服务，“外卖人”将做出相应处罚或屏蔽地址的处理；对“外卖人”造成经济损失的，“外卖人”将保留追究法律责任的权利；用户不应在“外卖人”网站上订餐、订外卖交易平台上恶意评价其他用户，或采取不正当手段提高自身的信用度或降低其他用户的信用度。</span><br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">对于用户在“外卖人”网站上订餐、订外卖交易平台上的不当行为或其它任何“外卖人”认为应当终止服务的情况，“外卖人”有权随时做出删除相关信息、终止服务提供等处理，而无须征得用户的同意。</span><br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">用户在“外卖人”网站上订餐、订外卖交易过程中如与其他用户因交易产生纠纷，请求“外卖人”从中予以调处，经“外卖人”审核后，“外卖人”有权通过电话或电子邮件联系向纠纷双方了解情况，并将所了解的情况通过电话或电子邮件互相通知对方；</span><br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">“外卖人”有权对用户的注册资料及交易行为进行查阅，发现注册资料或订餐、订外卖交易行为中存在任何问题或怀疑，均有权向用户发出询问及要求改正的通知或者直接做出删除等处理；</span><br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">“外卖人”有义务在现有技术上维护整个网站上订餐、订外卖交易平台的正常运行，并努力提升和改进技术，使用户网站上订餐、订外卖交易活动得以顺利进行。</span><br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">对用户在注册使用“外卖人”网站上订餐、订外卖交易平台中所遇到的与交易或注册有关的问题及反映的情况，“外卖人”应及时做出回复。</span><br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">用户因在“外卖人”网站上订餐、订外卖交易与其他用户产生诉讼的，用户通过司法部门或行政部门依照法定程序要求“外卖人”提供相关资料，外卖人应积极配合并提供有关资料；</span><br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<h3 style=\"padding-bottom:0px;line-height:40px;margin:10px 0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:24px;padding-top:0px;text-rendering:optimizelegibility;\">安全：</h3>\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">外卖人账户均有密码保护功能，请妥善保管用户的账户及密码信息；</span><br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">在使用“外卖人”服务进行网站上订餐、订外卖交易时，用户不可避免的要向订餐、订外卖交易对方或潜在的订餐、订外卖交易对方提供自己的个人信息，如联络方式或送餐地址。请用户妥善保护自己的个人信息，仅在必要的情形下向他人提供；</span><br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<span style=\"line-height:20px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;\">如果用户发现自己的个人信息泄密，尤其是“外卖人”账户或及密码发生泄露，请用户立即联络“外卖人”客服，以便“外卖人”采取相应措施。</span><br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<br style=\"padding-bottom:0px;line-height:20px;margin:0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:14px;padding-top:0px;\" />\r\n<h3 style=\"padding-bottom:0px;line-height:40px;margin:10px 0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:24px;padding-top:0px;text-rendering:optimizelegibility;\">“外卖人”保留对本协议做出不定时修改的权利</h3>\r\n<p style=\"padding-bottom:0px;line-height:40px;margin:10px 0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:24px;padding-top:0px;text-rendering:optimizelegibility;\">&nbsp;</p>\r\n<p style=\"padding-bottom:0px;line-height:40px;margin:10px 0px;padding-left:0px;padding-right:0px;font-family:宋体, 微软雅黑;color:#333333;font-size:24px;padding-top:0px;text-rendering:optimizelegibility;\">&nbsp;</p>', '服务协议', 'xieyi', '1508688000', '服务协议', '服务协议');
INSERT INTO `xiaozu_single` VALUES ('20', '<p>1、商城积分只可在淘宝商城购物时使用；\r\n2、积分的数值精确到个位（小数点后全部舍弃，不进行四舍五入），赠送积分的商品，商家承担积分费用，费用保留小数点后二位（费用以元为单位），100个积分等于现金1元；\r\n例如某商品积分派送比率为1%，不同售价情况下淘宝扣除的费用和送出积分情况如下：\r\n商品售价 商家付出的推广费用 买家获得积分\r\n100 1元 100\r\n99 0.99元 99\r\n101 1.01元 101\r\n3、积分有效期为至少1年，当年产生的积分次年公历年底前有效，以公历的年、北京时间为准，过期作废；（如若交易在使用的积分有效期之外发生退款，该部分积分不予退还）\r\n4、商品的最低积分返点比例为0.5%（其中，虚拟类商品最低积分返点比例为0.1% ）,商家可以用最低比例的倍数作为自设的积分比例；\r\n5、买家在使用积分付款时，每笔订单可使用积分付款的部分不得超过该笔订单金额（不包括邮费）的50%；\r\n6、买家在完成该笔交易后（支付宝软件系统显示该交易状态为\"交易成功\"）后，才能得到此次交易的相应积分；\r\n7、积分不得进行任何转让，不可以进行兑换</p>\r\n<p><img src=\"http://image.ghwmr.com/images/410100/news/20180525162914902.png\" alt=\"\" /></p>', '积分规则', 'jfgz', '1511884800', '积分规则', '积分规则');
INSERT INTO `xiaozu_single` VALUES ('24', '<div><ul><li><span style=\"font-size:16px;\">1.分享二维码名片给好友、朋友圈；</span></li>\r\n<span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><li><span style=\"font-size:16px;\">2.微信好友可通过识别/扫描二维码，登录成功后成为你的下线推广员；</span></li>\r\n<span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><li><span style=\"font-size:16px;\">3.通过APP注册的用户可在注册页面填写上面的邀请码，注册成功后成为你的下线推广员；</span></li>\r\n<span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span></ul>\r\n<span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><p></p>\r\n<span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><ul><li><span style=\"font-size:16px;\">4.下线推广员在平台下单，自己获得佣金；</span></li>\r\n<span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><li><span style=\"font-size:16px;\">5.订单完成后返佣金，佣金可提现！</span></li>\r\n<span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><li><img src=\"http://image.ghwmr.com/images/410100/news/20180525170508285.png\" alt=\"\" /></li>\r\n<span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><span style=\"font-size:16px;\"> </span><li><span style=\"font-size:16px;\">6.测试使用的呢</span></li>\r\n</ul>\r\n<p></p>\r\n</div>\r\n<ul>\r\n</ul>\r\n<p></p>\r\n<p></p>\r\n<p></p>\r\n<p></p>', '分销说明', 'fxsm', '1529745430', null, null);

-- ----------------------------
-- Table structure for xiaozu_siteset
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_siteset`;
CREATE TABLE `xiaozu_siteset` (
  `id` int(1) NOT NULL,
  `instro` text NOT NULL,
  `gonggao` varchar(250) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_siteset
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_specialpage
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_specialpage`;
CREATE TABLE `xiaozu_specialpage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名称',
  `indeximg` varchar(255) NOT NULL COMMENT '首页显示图片	',
  `imgwidth` varchar(50) NOT NULL DEFAULT '50%' COMMENT '首页显示图片宽度',
  `imgheight` varchar(50) NOT NULL DEFAULT '62' COMMENT '首页显示图片高度',
  `specialimg` varchar(255) NOT NULL COMMENT '首页显示图片	',
  `color` varchar(20) NOT NULL COMMENT '专题页背景主色调',
  `showtype` int(1) NOT NULL DEFAULT '0' COMMENT '针对的是商品还是店铺  默认0为店铺 1为商品',
  `is_custom` int(1) NOT NULL DEFAULT '1' COMMENT '是否是自定义	默认为1固定的  0为自定义的',
  `cx_type` int(1) NOT NULL COMMENT '如果是商品1为折扣  如果是店铺 1为推荐店铺  2为免减商家 3为打折商家 4免配送费',
  `listids` text NOT NULL COMMENT '如果是自定义的话 所对应的店铺id集或者商品id集',
  `ruleintro` text COMMENT '规则说明',
  `is_show` int(1) NOT NULL DEFAULT '1' COMMENT '是否展示 默认1展示 0不展示',
  `orderid` int(11) NOT NULL COMMENT '排序',
  `addtime` int(11) NOT NULL COMMENT '添加时间',
  `cityid` int(12) NOT NULL COMMENT '城市ID',
  `zdylink` text,
  `is_bd` int(2) NOT NULL DEFAULT '1',
  `zttype` int(2) NOT NULL DEFAULT '1',
  `ztystyle` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=175 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_specialpage
-- ----------------------------
INSERT INTO `xiaozu_specialpage` VALUES ('170', '商家入驻', '', '50%', '62', 'emptyimg', '#000000', '0', '1', '10', '', 'emptyContent', '1', '0', '0', '410100', null, '2', '5', '3');
INSERT INTO `xiaozu_specialpage` VALUES ('171', '商品活动', '', '50%', '62', '/images/410100/cx/20180623184546184.png', '#123456', '1', '0', '0', '9583,9584', '规则说明', '0', '0', '0', '410100', null, '2', '2', '3');
INSERT INTO `xiaozu_specialpage` VALUES ('172', '店铺活动', '', '50%', '62', '/images/410100/cx/20180623184621230.png', '#123456', '0', '0', '0', '1213', '店铺活动', '0', '0', '0', '410100', null, '2', '1', '2');
INSERT INTO `xiaozu_specialpage` VALUES ('173', '跑腿', '', '50%', '62', 'emptyimg', '#000000', '0', '1', '9', '', 'emptyContent', '0', '0', '0', '410100', null, '2', '5', '3');
INSERT INTO `xiaozu_specialpage` VALUES ('174', '满减活动', '', '50%', '62', '/images/410100/cx/20180623184809274.png', '#123456', '1', '1', '1', '', '规则说明', '0', '0', '0', '410100', null, '2', '4', '1');

-- ----------------------------
-- Table structure for xiaozu_stationadmininfo
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_stationadmininfo`;
CREATE TABLE `xiaozu_stationadmininfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(12) NOT NULL COMMENT '分站用户UID',
  `stationname` varchar(255) NOT NULL COMMENT '分站名称',
  `stationusername` varchar(50) NOT NULL COMMENT '分站负责人',
  `stationphone` varchar(30) NOT NULL COMMENT '分站电话',
  `cityid` int(12) NOT NULL COMMENT '所属城市ID',
  `stationlnglat` varchar(100) NOT NULL COMMENT '分站坐标',
  `stationaddress` varchar(255) NOT NULL COMMENT '分站地址',
  `orderid` int(12) NOT NULL COMMENT '排序ID',
  `stationis_open` int(1) NOT NULL COMMENT '是否开启 默认0开启1关闭',
  `is_selfsitecx` int(1) DEFAULT '0' COMMENT '是否允许分站自行设置促销规则  1允许  0不允许',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=181 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_stationadmininfo
-- ----------------------------
INSERT INTO `xiaozu_stationadmininfo` VALUES ('180', '455', '郑州站', '郑州站', '15236985214', '410100', '', '郑州市高新区', '0', '0', '1');

-- ----------------------------
-- Table structure for xiaozu_stationskin
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_stationskin`;
CREATE TABLE `xiaozu_stationskin` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cityid` int(10) NOT NULL COMMENT '分站cityid',
  `imgurl` text COMMENT '图片url',
  `is_show` int(1) DEFAULT '0' COMMENT '是否显示 0否 1是',
  `color` text COMMENT '色值',
  `is_gourl` int(1) DEFAULT '0' COMMENT '是否跳转连接0否 1是',
  `title` text COMMENT '跳转连接标题',
  `gourl` text COMMENT '跳转连接url',
  `type` int(3) DEFAULT NULL COMMENT '类型 1分类背景图设置   2分类和网站通知中间一张图 ',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=136 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_stationskin
-- ----------------------------
INSERT INTO `xiaozu_stationskin` VALUES ('134', '410100', '/images/410100/index/20180623183320907.png', '0', '#210303', '0', null, null, '1');
INSERT INTO `xiaozu_stationskin` VALUES ('135', '410100', '/images/410100/index/20180623183334121.png', '0', null, '0', '', '', '2');

-- ----------------------------
-- Table structure for xiaozu_task
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_task`;
CREATE TABLE `xiaozu_task` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `taskname` varchar(100) NOT NULL,
  `tasktype` int(1) NOT NULL,
  `taskusertype` int(1) NOT NULL,
  `tasklimit` text NOT NULL,
  `start_id` int(20) NOT NULL,
  `end_id` int(20) NOT NULL,
  `content` text NOT NULL,
  `status` int(1) NOT NULL,
  `othercontent` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_task
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_top
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_top`;
CREATE TABLE `xiaozu_top` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `typeid` int(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `instro` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_top
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_topatt
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_topatt`;
CREATE TABLE `xiaozu_topatt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `orderid` int(5) DEFAULT '99',
  `ids` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_topatt
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_upgradelog
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_upgradelog`;
CREATE TABLE `xiaozu_upgradelog` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `versionid` varchar(50) DEFAULT NULL COMMENT '版本id',
  `name` varchar(255) DEFAULT NULL COMMENT '版本名称',
  `instro` varchar(255) DEFAULT NULL COMMENT '版本说明',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '1单城市版 2多城市版本',
  `vertype` int(1) NOT NULL DEFAULT '1' COMMENT '默认是1 beta版   2使用版',
  `creattime` int(12) NOT NULL COMMENT '创建时间',
  `addtime` int(12) NOT NULL COMMENT '添加时间',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '1未安装 2安装中 3已安装',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of xiaozu_upgradelog
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_userjuannotice
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_userjuannotice`;
CREATE TABLE `xiaozu_userjuannotice` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `actname` text COMMENT '活动名称',
  `uid` int(20) DEFAULT NULL COMMENT '用户uid',
  `actid` int(10) DEFAULT NULL COMMENT '手动发放优惠券活动id',
  `is_read` int(1) DEFAULT '0' COMMENT '是否阅读0否 1是',
  `addtime` int(11) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=346961 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_userjuannotice
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_usrlimit
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_usrlimit`;
CREATE TABLE `xiaozu_usrlimit` (
  `name` varchar(100) NOT NULL COMMENT '操作名称',
  `cnname` varchar(200) NOT NULL,
  `moduleid` int(20) NOT NULL,
  `group` int(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_usrlimit
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_wxappoauth
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_wxappoauth`;
CREATE TABLE `xiaozu_wxappoauth` (
  `openid` varchar(200) NOT NULL,
  `username` varchar(100) NOT NULL,
  `imgurl` varchar(255) NOT NULL,
  `uid` int(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_wxappoauth
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_wxback
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_wxback`;
CREATE TABLE `xiaozu_wxback` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `msgtype` int(1) NOT NULL DEFAULT '1',
  `values` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_wxback
-- ----------------------------
INSERT INTO `xiaozu_wxback` VALUES ('26', 'subscribe', '2', '自动回复');

-- ----------------------------
-- Table structure for xiaozu_wxcomment
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_wxcomment`;
CREATE TABLE `xiaozu_wxcomment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '微信用户uid',
  `usercontent` varchar(2000) NOT NULL COMMENT '发表主题',
  `userimg` varchar(2000) NOT NULL COMMENT '图片集',
  `addtime` varchar(20) NOT NULL COMMENT '添加时间',
  `cityname` varchar(25) NOT NULL,
  `areaname` varchar(25) NOT NULL,
  `streetname` varchar(25) NOT NULL,
  `is_top` int(11) NOT NULL DEFAULT '0',
  `is_show` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=897 DEFAULT CHARSET=utf8 COMMENT='微信一起说主表';

-- ----------------------------
-- Records of xiaozu_wxcomment
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_wxjuan
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_wxjuan`;
CREATE TABLE `xiaozu_wxjuan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cartname` varchar(50) NOT NULL COMMENT '优惠卷名称',
  `cartdesrc` varchar(255) NOT NULL COMMENT '优惠卷描述',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0未使用，1已绑定，2已使用，3无效',
  `cost` int(5) NOT NULL COMMENT '优惠金额',
  `limitcost` int(5) NOT NULL COMMENT '最低消费限制金额',
  `creattime` int(11) NOT NULL DEFAULT '0' COMMENT '制造时间',
  `endtime` int(11) NOT NULL DEFAULT '0' COMMENT '失效时间',
  `lqrule` varchar(50) NOT NULL COMMENT 'lqrule',
  `limitdayshu` int(11) NOT NULL COMMENT '限制每天领取次数',
  `limitzongshu` int(11) NOT NULL COMMENT '限制总共领取次数',
  `lqlink` varchar(255) NOT NULL COMMENT '领取连接',
  `sharetitle` varchar(255) NOT NULL COMMENT '分享标题',
  `sharezhaiy` varchar(255) NOT NULL COMMENT '分享摘要',
  `shareimg` varchar(255) NOT NULL COMMENT '分享图片',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_wxjuan
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_wxmenu
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_wxmenu`;
CREATE TABLE `xiaozu_wxmenu` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL COMMENT 'view表示连接，click事件',
  `name` varchar(255) NOT NULL COMMENT 'an钮事件名称',
  `values` text COMMENT '当type为view时跳转连接，当click为则为内容',
  `parent_id` int(20) NOT NULL DEFAULT '0' COMMENT '0一级菜单',
  `sort` int(3) NOT NULL,
  `msgtype` int(1) NOT NULL DEFAULT '0' COMMENT '0:连接1文本2图文',
  `code` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=60 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_wxmenu
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_wxpjzan
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_wxpjzan`;
CREATE TABLE `xiaozu_wxpjzan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `commentid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1279 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_wxpjzan
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_wxreplycomment
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_wxreplycomment`;
CREATE TABLE `xiaozu_wxreplycomment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '微信用户uid',
  `parentid` int(11) NOT NULL COMMENT '评论who id',
  `content` varchar(500) NOT NULL COMMENT '发表主题',
  `kejian` int(11) NOT NULL DEFAULT '0' COMMENT '0所有人可见1仅对方可见',
  `addtime` varchar(20) NOT NULL COMMENT '添加时间',
  `cityname` varchar(25) NOT NULL,
  `areaname` varchar(25) NOT NULL,
  `streetname` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=251 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_wxreplycomment
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_wxuser
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_wxuser`;
CREATE TABLE `xiaozu_wxuser` (
  `openid` varchar(255) NOT NULL,
  `uid` int(20) NOT NULL,
  `is_bang` int(1) NOT NULL DEFAULT '0',
  `wxlat` varchar(255) DEFAULT NULL,
  `wxlng` varchar(255) DEFAULT NULL,
  `access_token` varchar(100) DEFAULT NULL,
  `expires_in` int(12) DEFAULT NULL,
  `refresh_token` varchar(255) DEFAULT NULL,
  `wxusername` varchar(255) NOT NULL,
  `wxuserlogo` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_wxuser
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_wxuserjuan
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_wxuserjuan`;
CREATE TABLE `xiaozu_wxuserjuan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `juanid` int(11) NOT NULL,
  `fafangtime` int(11) NOT NULL,
  `uid` int(11) NOT NULL COMMENT '顾客uid',
  `username` varchar(50) NOT NULL COMMENT '顾客姓名',
  `juanname` varchar(50) NOT NULL COMMENT '优惠卷名称',
  `juancost` int(5) NOT NULL COMMENT '面值',
  `juanlimitcost` int(5) NOT NULL COMMENT '限制金额',
  `endtime` int(11) NOT NULL COMMENT '过期时间',
  `lqstatus` int(11) NOT NULL COMMENT '领取状态',
  `status` int(5) NOT NULL COMMENT '状态',
  `juanshu` int(5) NOT NULL COMMENT '优惠卷数量',
  `usetime` int(11) NOT NULL COMMENT '优惠卷使用时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_wxuserjuan
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_wxuserjubao
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_wxuserjubao`;
CREATE TABLE `xiaozu_wxuserjubao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `commentid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_wxuserjubao
-- ----------------------------

-- ----------------------------
-- Table structure for xiaozu_ztyimginfo
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_ztyimginfo`;
CREATE TABLE `xiaozu_ztyimginfo` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL DEFAULT '1',
  `indeximg` varchar(255) NOT NULL,
  `ztyid` int(11) NOT NULL,
  `cityid` int(11) NOT NULL,
  `is_show` int(2) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=831 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_ztyimginfo
-- ----------------------------
INSERT INTO `xiaozu_ztyimginfo` VALUES ('819', '1', '/images/410100/index/20180623183811603.png', '170', '410100', '1', '0');
INSERT INTO `xiaozu_ztyimginfo` VALUES ('820', '1', '/images/410100/index/20180623184829678.png', '171', '410100', '1', '1');
INSERT INTO `xiaozu_ztyimginfo` VALUES ('821', '1', '/images/410100/index/20180623184851794.png', '172', '410100', '1', '2');
INSERT INTO `xiaozu_ztyimginfo` VALUES ('822', '1', '/images/410100/index/20180623184902907.png', '173', '410100', '1', '3');
INSERT INTO `xiaozu_ztyimginfo` VALUES ('823', '1', '/images/410100/index/20180623184911267.png', '174', '410100', '1', '4');
INSERT INTO `xiaozu_ztyimginfo` VALUES ('824', '2', '/images/410100/index/20180623184938578.png', '170', '410100', '1', '0');
INSERT INTO `xiaozu_ztyimginfo` VALUES ('825', '2', '/images/410100/index/20180623184944291.png', '171', '410100', '1', '1');
INSERT INTO `xiaozu_ztyimginfo` VALUES ('826', '2', '/images/410100/index/20180623184954389.png', '172', '410100', '1', '2');
INSERT INTO `xiaozu_ztyimginfo` VALUES ('827', '2', '/images/410100/index/20180623185009818.png', '173', '410100', '1', '3');
INSERT INTO `xiaozu_ztyimginfo` VALUES ('828', '3', '/images/410100/index/20180623185034444.png', '170', '410100', '1', '0');
INSERT INTO `xiaozu_ztyimginfo` VALUES ('829', '3', '/images/410100/index/20180623185051146.png', '171', '410100', '1', '1');
INSERT INTO `xiaozu_ztyimginfo` VALUES ('830', '3', '/images/410100/index/20180623185059535.png', '173', '410100', '1', '2');

-- ----------------------------
-- Table structure for xiaozu_ztymode
-- ----------------------------
DROP TABLE IF EXISTS `xiaozu_ztymode`;
CREATE TABLE `xiaozu_ztymode` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL DEFAULT '1' COMMENT '1样式一，2样式二，3样式三',
  `cityid` int(11) DEFAULT NULL COMMENT '城市id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xiaozu_ztymode
-- ----------------------------
INSERT INTO `xiaozu_ztymode` VALUES ('55', '1', '410100');

-- ----------------------------
-- Table structure for xx_positionkey
-- ----------------------------
DROP TABLE IF EXISTS `xx_positionkey`;
CREATE TABLE `xx_positionkey` (
  `datatype` int(1) NOT NULL,
  `parent_id` int(20) NOT NULL,
  `datacode` varchar(100) CHARACTER SET utf8 NOT NULL,
  `datacontent` varchar(100) CHARACTER SET utf8 NOT NULL,
  `lat` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `lng` varchar(20) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of xx_positionkey
-- ----------------------------
DROP TRIGGER IF EXISTS `insertpsorder`;
DELIMITER ;;
CREATE TRIGGER `insertpsorder` BEFORE INSERT ON `xiaozu_order` FOR EACH ROW begin 
  declare n int;
    select max(id) into n from `xiaozu_order`;
    if n is null  then
        set n=1;
    else
        set n=n+1;
    end if;
if new.pstype = 0 && new.is_goshop =0 && new.shoptype != 100 &&  new.is_make = 1 then
       
    insert into `xiaozu_orderps`(`orderid`,`shopid`,`psuid`,`psusername`,`psemail`,`status`,`dno`,`addtime`,`pstime`,`psycost`,`picktime`,`dotype`)
                 values(n,new.shopid,0,'','',0,new.dno,new.addtime,new.posttime,0,0,1);  
    
end if; 

end
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `orderJs`;
DELIMITER ;;
CREATE TRIGGER `orderJs` AFTER UPDATE ON `xiaozu_order` FOR EACH ROW begin
 
  
 
 if old.paystatus = 0 && new.paystatus = 1 then
	if old.pstype = 0 && old.is_goshop =0 && old.shoptype != 100 &&  old.is_make = 1 then 
  		  insert into `xiaozu_orderps`(`orderid`,`shopid`,`psuid`,`psusername`,`psemail`,`status`,`dno`,`addtime`,`pstime`,`psycost`,`picktime`,`dotype`)
               		  values(old.id,new.shopid,0,'','',0,new.dno,unix_timestamp(),new.posttime,0,0,1);  
	end if; 
end if;
#当商家确认制作后执行
if old.is_make = 0 && new.is_make = 1 then
	if old.pstype = 0 && old.is_goshop =0 && old.shoptype != 100  then 
	 
  		  insert into `xiaozu_orderps`(`orderid`,`shopid`,`psuid`,`psusername`,`psemail`,`status`,`dno`,`addtime`,`pstime`,`psycost`,`picktime`,`dotype`)
               		  values(old.id,new.shopid,0,'','',0,new.dno,unix_timestamp(),new.posttime,0,0,1);  
	end if;
 
end if;
 
 
set @dotime = unix_timestamp();
 
IF old.status <> 3 && new.status = 3 && old.shoptype <> 100 then
select id into @cf_id from xiaozu_shopjs where orderid=old.id;
    IF @cf_id is null then 
            select id,shoptype,yjin,uid,zitiyjb,zitilimityj,zitianyj into @cf_shopid,@cf_shoptype,@cf_yjin,@cf_uid,@cf_zitiyjb,@cf_zitilimityj,@cf_zitianyj from xiaozu_shop where id=old.shopid;
            IF @cf_shoptype = 1 then
                    select sendtype into @cf_sendtype from xiaozu_shopmarket where shopid=@cf_shopid;
                    
            ELSE         
                    select sendtype into @cf_sendtype from xiaozu_shopfast where shopid=@cf_shopid;
            END IF;
            
            IF old.paytype =1 then 
            	set @onlinecost = old.allcost;
                set @onlinecount = 1;
                set @unlinecost = 0;
                set @unlinecount = 0;
				
            ELSE
            	set @onlinecost = 0;
                set @onlinecount = 0;
                set @unlinecost = old.allcost;
                set @unlinecount = 1;
            END IF;
            
            IF @cf_sendtype = 1 then
			    set @sjyjnum = old.shopcost;
			    select pscost,bagcost,shopdowncost into @cf_pscost,@cf_bagcost,@cf_shopdowncost from xiaozu_jscompute where id=2;
				if @cf_pscost = 1 then  
				    set @sjyjnum = @sjyjnum + old.shopps;  
				end if;  	
				if @cf_bagcost = 1 then  
				    set @sjyjnum = @sjyjnum + old.bagcost;  
				end if;  
				if @cf_shopdowncost = 1 then  
				    set @sjyjnum = @sjyjnum - (old.cxcost-old.shopdowncost);  
				end if;  
				
				if old.is_ziti = 1 then  
				    set @yjcostx = @sjyjnum*(@cf_zitiyjb*0.01);
					if @yjcostx > @cf_zitilimityj then
					    set @yjcost = @yjcostx;
					else
						set @yjcost = @cf_zitianyj;
					end if;
				else
					set @yjcost = @sjyjnum*(@cf_yjin*0.01);
				end if;

				set @sjjsnum = old.shopcost;
			    select pscost,bagcost,shopdowncost into @cf_pscost,@cf_bagcost,@cf_shopdowncost from xiaozu_jscompute where id=4;
				if @cf_pscost = 1 then  
				    set @sjjsnum = @sjjsnum + old.shopps;  				 
				end if;  
				if @cf_bagcost = 1 then  
				    set @sjjsnum = @sjjsnum + old.bagcost;  
				end if;  
				if @cf_shopdowncost = 1 then  
				    set @sjjsnum = @sjjsnum - (old.cxcost-old.shopdowncost);   
				end if;  
				
				if old.paytype = 0 then
				    set @acountcost =  - @yjcost;
				else
				    set @acountcost = @sjjsnum - @yjcost;
				end if;
            ELSE
				set @ptyjnum = old.shopcost;
			    select pscost,bagcost,shopdowncost into @cf_pscost,@cf_bagcost,@cf_shopdowncost from xiaozu_jscompute where id=1;
				if @cf_pscost = 1 then  
				    set @ptyjnum = @ptyjnum + old.shopps;  				 
				end if; 
				if @cf_bagcost = 1 then  
				    set @ptyjnum = @ptyjnum + old.bagcost;  
				end if; 
				if @cf_shopdowncost = 1 then  
				    set @ptyjnum = @ptyjnum - (old.cxcost-old.shopdowncost);  
				end if; 
				
				if old.is_ziti = 1 then  
				    set @yjcostx = @ptyjnum*(@cf_zitiyjb*0.01);
					if @yjcostx > @cf_zitilimityj then
					    set @yjcost = @yjcostx;
					else
						set @yjcost = @cf_zitianyj;
					end if;
				else
					set @yjcost = @ptyjnum*(@cf_yjin*0.01);
				end if;
			
            	set @ptjsnum = old.shopcost;
			    select pscost,bagcost,shopdowncost into @cf_pscost,@cf_bagcost,@cf_shopdowncost from xiaozu_jscompute where id=3;
				if @cf_pscost = 1 then  
				    set @ptjsnum = @ptjsnum + old.shopps;  				 
				end if; 
				if @cf_bagcost = 1 then  
				    set @ptjsnum = @ptjsnum + old.bagcost;  
				end if; 
				if @cf_shopdowncost = 1 then  
				    set @ptjsnum = @ptjsnum - (old.cxcost-old.shopdowncost);  
				end if;  
 				set @acountcost = @ptjsnum - @yjcost;
            END IF;
    	    select max(id) into @n from `xiaozu_shopjs`;
			
			if @n is null  then
				set @n=1;
			else
				set @n=@n+1;
			end if;
            if old.is_ziti = 1 then			
			    set @cf_yjinx = @cf_zitiyjb;
			else
				set @cf_yjinx = @cf_yjin;
			end if;
			
			insert into xiaozu_shopjs(onlinecount,onlinecost,unlinecount,unlinecost,yjb,acountcost,yjcost,pstype,shopid,shopuid,addtime,jstime,orderid) values (@onlinecount,@onlinecost,@unlinecount,@unlinecost,@cf_yjinx,@acountcost,@yjcost,@cf_shoptype,@cf_shopid,@cf_uid,@dotime,@dotime,old.id);  
     
			select shopcost into @cf_shopcost from xiaozu_member where uid=@cf_uid;
			set @add_cost = @cf_shopcost+@acountcost;
			
			if @acountcost > 0 then
			    insert into xiaozu_shoptx(cost,type,status,changetype,addtime,shopid,shopuid,name,yue,jsid) values (@acountcost,3,2,2,@dotime,@cf_shopid,@cf_uid,'订单结算转入余额',@add_cost,@n);
			else
			    insert into xiaozu_shoptx(cost,type,status,changetype,addtime,shopid,shopuid,name,yue,jsid) values (@acountcost,3,2,1,@dotime,@cf_shopid,@cf_uid,'订单结算扣除佣金',@add_cost,@n);
			end if;
			
			UPDATE  `xiaozu_member` SET `shopcost` = `shopcost`+@acountcost  WHERE `uid`=@cf_uid;  
			UPDATE  `xiaozu_shop` SET `ordercount` = `ordercount`+1  WHERE `id`=@cf_shopid;  
        	 
     END IF;
     ELSEIF old.is_reback = 1 && new.is_reback = 2 then
		IF  old.status = 3   then  
			select id,onlinecost,onlinecount,unlinecount,unlinecost,yjb,yjcost,pstype,shopid,shopuid,acountcost,jsid,orderid into @cf_id,@cf_onlinecost,@cf_onlinecount,@cf_unlinecount,@cf_unlinecost,@cf_yjb,@cf_yjcost,@cf_pstype,@cf_shopid,@cf_shopuid,@cf_acountcost,@cf_jsid,@cf_orderid from xiaozu_shopjs where orderid=old.id;
			IF @cf_id is null then 
				set @X = 1;
			ELSE			 
				select shopcost into @cf_shopcost from xiaozu_member where uid=@cf_shopuid;
				set @jian_cost = @cf_shopcost-@cf_acountcost;
				insert into xiaozu_shoptx(cost,type,status,changetype,addtime,shopid,shopuid,name,yue,jsid) values (@cf_acountcost,3,2,1,@dotime,@cf_shopid,@cf_shopuid,'订单退款扣款',@jian_cost,@cf_id);
				
				UPDATE  `xiaozu_member` SET `shopcost` = `shopcost`-@cf_acountcost  WHERE `uid`=@cf_shopuid;
			
			END IF;
		END IF;
ELSE	 
	 set @X = 1;
END IF;
end
;;
DELIMITER ;
