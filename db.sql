/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : tmp

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2018-04-04 12:52:52
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cas_action
-- ----------------------------
DROP TABLE IF EXISTS `cas_action`;
CREATE TABLE `cas_action` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `action_name` varchar(45) NOT NULL,
  `action_description` text NOT NULL,
  `action_api` varchar(45) NOT NULL,
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cas_action
-- ----------------------------
INSERT INTO `cas_action` VALUES ('1', '1', '贴吧签到', '每日自动签到关注的贴吧(每日只执行一次,新添加的贴吧第二天开始)', 'SignTieba');
INSERT INTO `cas_action` VALUES ('2', '2', 'bilibili直播签到', '每天自动签到', 'SignLive');
INSERT INTO `cas_action` VALUES ('3', '3', '网易云音乐签到', '每日自动签到网易云音乐', 'SignMusic');
INSERT INTO `cas_action` VALUES ('4', '4', 'V2EX每日任务', 'V2EX每日任务', 'SignV2EX');

-- ----------------------------
-- Table structure for cas_action_task
-- ----------------------------
DROP TABLE IF EXISTS `cas_action_task`;
CREATE TABLE `cas_action_task` (
  `tid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `aid` int(11) unsigned NOT NULL,
  `puid` int(11) unsigned NOT NULL,
  `task_param` text,
  `task_last_time` bigint(20) unsigned NOT NULL,
  `task_status` tinyint(4) NOT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cas_action_task
-- ----------------------------

-- ----------------------------
-- Table structure for cas_config
-- ----------------------------
DROP TABLE IF EXISTS `cas_config`;
CREATE TABLE `cas_config` (
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cas_config
-- ----------------------------
INSERT INTO `cas_config` VALUES ('monitor_status', '1');
INSERT INTO `cas_config` VALUES ('pwd_encode_salt', '#faxGht&cd');

-- ----------------------------
-- Table structure for cas_log
-- ----------------------------
DROP TABLE IF EXISTS `cas_log`;
CREATE TABLE `cas_log` (
  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `log_content` text NOT NULL,
  `log_type` tinyint(4) NOT NULL,
  `log_time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cas_log
-- ----------------------------

-- ----------------------------
-- Table structure for cas_platform
-- ----------------------------
DROP TABLE IF EXISTS `cas_platform`;
CREATE TABLE `cas_platform` (
  `pid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `platform_name` varchar(45) NOT NULL,
  `platform_description` text NOT NULL,
  `platform_api` varchar(45) NOT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cas_platform
-- ----------------------------
INSERT INTO `cas_platform` VALUES ('1', '百度', '百度账号', 'BaiduPlatform');
INSERT INTO `cas_platform` VALUES ('2', 'bilibili', 'bilibili账号', 'BilibiliPlatform');
INSERT INTO `cas_platform` VALUES ('3', '网易云', '网易云账号操作', 'WangyiPlatform');
INSERT INTO `cas_platform` VALUES ('4', 'V2EX', '一个汇集各类奇妙好玩的话题和流行动向的网站', 'V2EXPlatform');

-- ----------------------------
-- Table structure for cas_platform_account
-- ----------------------------
DROP TABLE IF EXISTS `cas_platform_account`;
CREATE TABLE `cas_platform_account` (
  `puid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `pu_u` varchar(16) DEFAULT NULL,
  `pu_p` varchar(16) DEFAULT NULL,
  `pu_cookie` text,
  `pu_time` bigint(20) unsigned NOT NULL,
  `pu_status` tinyint(4) NOT NULL,
  PRIMARY KEY (`puid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cas_platform_account
-- ----------------------------

-- ----------------------------
-- Table structure for cas_token
-- ----------------------------
DROP TABLE IF EXISTS `cas_token`;
CREATE TABLE `cas_token` (
  `token` varchar(128) NOT NULL,
  `value` varchar(64) NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  `type` int(4) NOT NULL COMMENT '0 user login 1 email',
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cas_token
-- ----------------------------

-- ----------------------------
-- Table structure for cas_users
-- ----------------------------
DROP TABLE IF EXISTS `cas_users`;
CREATE TABLE `cas_users` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` varchar(64) NOT NULL,
  `avatar` varchar(128) NOT NULL,
  `reg_time` bigint(20) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cas_users
-- ----------------------------
