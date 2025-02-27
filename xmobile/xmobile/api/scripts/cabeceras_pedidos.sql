/*
 Navicat Premium Data Transfer

 Source Server         : mariadb_local
 Source Server Type    : MariaDB
 Source Server Version : 100315
 Source Host           : localhost:3306
 Source Schema         : xm

 Target Server Type    : MariaDB
 Target Server Version : 100315
 File Encoding         : 65001

 Date: 03/07/2019 18:00:58
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cabeceras_pedidos
-- ----------------------------
DROP TABLE IF EXISTS `cabeceras_pedidos`;
CREATE TABLE `cabeceras_pedidos`  (
  `DocEntry` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `DocNum` int(11) NOT NULL,
  `DocType` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `canceled` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Printed` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `DocStatus` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `DocDate` date NOT NULL,
  `DocDueDate` date NOT NULL,
  `CardCode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `CardName` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `NumAtCard` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `DiscPrcnt` int(11) NULL DEFAULT NULL,
  `DiscSum` int(11) NULL DEFAULT NULL,
  `DocCur` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `DocRate` int(11) NULL DEFAULT NULL,
  `DocTotal` int(11) NULL DEFAULT NULL,
  `PaidToDate` int(11) NULL DEFAULT NULL,
  `Ref1` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Ref2` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Comments` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `JrnlMemo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `GroupNum` int(11) NULL DEFAULT NULL,
  `SlpCode` int(11) NULL DEFAULT NULL,
  `Series` int(11) NULL DEFAULT NULL,
  `TaxDate` date NOT NULL,
  `LicTradNum` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `UserSign` int(11) NULL DEFAULT NULL,
  `CreateDate` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `UserSign2` int(11) NULL DEFAULT NULL,
  `UpdateDate` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `U_4MOTIVOCANCELADO` int(11) NULL DEFAULT NULL,
  `U_4NIT` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `U_4RAZON_SOCIAL` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `U_LATITUD` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `U_LONGITUD` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `U_4SUBTOTAL` int(11) NULL DEFAULT NULL,
  `U_4DOCUMENTOORIGEN` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `U_4MIGRADOCONCEPTO` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `U_4MIGRADO` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `PriceListNum` int(11) NULL DEFAULT NULL,
  `estadosend` int(11) NULL DEFAULT NULL,
  `fecharegistro` date NOT NULL,
  `fechaupdate` date NULL DEFAULT NULL,
  `fechasend` date NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idDocPedido` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `idUser` int(255) NULL DEFAULT NULL,
  `estado` tinyint(1) NULL DEFAULT 1,
  `gestion` int(255) NULL DEFAULT NULL,
  `mes` int(255) NULL DEFAULT NULL,
  `correlativo` int(255) NULL DEFAULT NULL,
  `rowNum` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
