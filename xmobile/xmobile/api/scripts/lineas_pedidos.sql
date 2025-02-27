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

 Date: 03/07/2019 18:00:50
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for lineas_pedidos
-- ----------------------------
DROP TABLE IF EXISTS `lineas_pedidos`;
CREATE TABLE `lineas_pedidos`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `DocEntry` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `DocNum` int(11) NULL DEFAULT NULL,
  `LineNum` int(11) NULL DEFAULT NULL,
  `BaseType` int(11) NULL DEFAULT NULL,
  `BaseEntry` int(11) NULL DEFAULT NULL,
  `BaseLine` int(11) NULL DEFAULT NULL,
  `LineStatus` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `ItemCode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Dscription` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Quantity` int(11) NULL DEFAULT NULL,
  `OpenQty` int(11) NULL DEFAULT NULL,
  `Price` int(11) NULL DEFAULT NULL,
  `Currency` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `DiscPrcnt` int(11) NULL DEFAULT NULL,
  `LineTotal` int(11) NULL DEFAULT NULL,
  `WhsCode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `CodeBars` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `PriceAfVAT` int(11) NULL DEFAULT NULL,
  `TaxCode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `U_4DESCUENTO` int(11) NULL DEFAULT NULL,
  `U_4LOTE` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `GrossBase` int(11) NULL DEFAULT NULL,
  `idDocumento` int(11) NULL DEFAULT NULL,
  `fechaAdd` date NOT NULL,
  `unidadid` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `tc` decimal(65, 0) NULL DEFAULT NULL,
  `idDocPedido` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 64 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
