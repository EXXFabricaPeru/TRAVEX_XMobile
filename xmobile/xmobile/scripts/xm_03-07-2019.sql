-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 03-07-2019 a las 17:47:14
-- Versión del servidor: 10.1.37-MariaDB
-- Versión de PHP: 7.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `xm`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE PROCEDURE `pa_insert_solicitud_registro` (IN `username` VARCHAR(250), IN `password_hash` VARCHAR(250), IN `plataformaUsuario` VARCHAR(250), IN `plataformaPlataforma` VARCHAR(250), IN `plataformaEmei` VARCHAR(250), IN `idPersona` INT, IN `estadoUsuario` INT, IN `reset` TINYINT)  BEGIN

INSERT INTO user( `username`, `password_hash`, `plataformaUsuario`, `plataformaPlataforma`, `plataformaEmei`, `idPersona`, `estadoUsuario`, `reset`) VALUES (username, password_hash, plataformaUsuario, plataformaPlataforma, plataformaEmei, idPersona, estadoUsuario, reset);

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configapp`
--

CREATE TABLE `configapp` (
  `idUsuario` int(11) NOT NULL,
  `precio` int(11) NOT NULL,
  `proyecto` int(11) NOT NULL,
  `centro_costo` int(11) NOT NULL,
  `almacen` int(11) NOT NULL,
  `almacen_defecto` varchar(50) NOT NULL,
  `impresora` int(11) NOT NULL,
  `impresora_defecto` varchar(50) NOT NULL,
  `informacion_tributaria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configsap`
--

CREATE TABLE `configsap` (
  `idUsario` int(11) NOT NULL,
  `serie_oferta` int(11) NOT NULL,
  `serie_pedido` int(11) NOT NULL,
  `serie_factura_reserva` int(11) NOT NULL,
  `serie_factura_deudor` int(11) NOT NULL,
  `serie_cobranza` int(11) NOT NULL,
  `flujo_caja` int(11) NOT NULL,
  `empleado_ventas` int(11) NOT NULL,
  `cuenta_efectivo` int(11) NOT NULL,
  `cuenta_cheque` int(11) NOT NULL,
  `cuenta_transferencia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_usuarios`
--

CREATE TABLE `config_usuarios` (
  `id` int(11) UNSIGNED NOT NULL,
  `idEstado` int(11) UNSIGNED DEFAULT NULL,
  `idTipoPrecio` int(11) UNSIGNED DEFAULT NULL,
  `idTipoImpresora` int(11) UNSIGNED DEFAULT NULL,
  `ruta` varchar(240) DEFAULT NULL,
  `ctaEfectivo` varchar(240) DEFAULT NULL,
  `ctaCheque` varchar(240) DEFAULT NULL,
  `ctaTransferencia` varchar(240) DEFAULT NULL,
  `ctaFcEfectivo` varchar(240) DEFAULT NULL,
  `ctaFcCheque` varchar(240) DEFAULT NULL,
  `ctaFcTransferencia` varchar(240) DEFAULT NULL,
  `sreOfertaVenta` varchar(240) DEFAULT NULL,
  `sreOrdenVenta` varchar(240) DEFAULT NULL,
  `sreFactura` varchar(240) DEFAULT NULL,
  `sreFacturaReserva` varchar(240) DEFAULT NULL,
  `sreCobro` varchar(240) DEFAULT NULL,
  `flujoCaja` float(11,6) DEFAULT NULL,
  `modInfTributaria` varchar(240) DEFAULT NULL,
  `codEmpleadoVenta` varchar(240) DEFAULT NULL,
  `codVendedor` varchar(240) DEFAULT NULL,
  `nombre` varchar(240) DEFAULT NULL,
  `almacenes` varchar(255) NOT NULL,
  `idUser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `config_usuarios`
--

INSERT INTO `config_usuarios` (`id`, `idEstado`, `idTipoPrecio`, `idTipoImpresora`, `ruta`, `ctaEfectivo`, `ctaCheque`, `ctaTransferencia`, `ctaFcEfectivo`, `ctaFcCheque`, `ctaFcTransferencia`, `sreOfertaVenta`, `sreOrdenVenta`, `sreFactura`, `sreFacturaReserva`, `sreCobro`, `flujoCaja`, `modInfTributaria`, `codEmpleadoVenta`, `codVendedor`, `nombre`, `almacenes`, `idUser`) VALUES
(1, 1, 2, 1, '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 0.000000, '0', '0', '21', 'INKAFERRO', '', 12),
(2, 1, 1, 2, '0', '0', '0', '0', '00', '0', '0', '0', '0', '00', '0', '0', 0.000000, '0', '0', '9', 'ZOILA MENDOZA', '', 14),
(3, 1, 2, 2, '0', '0', '00', '0', '0', '00', '0', '0', '0', '00', '00', '0', 0.000000, '0', '0', '8', 'VICTOR ZUÑIGA', '', 15),
(4, 1, 2, 2, '0', '00', '0', '0', '0', '000', '0', '0', '0', '00', '0', '00', 0.000000, '0', '0', '7', 'ANGELICA TORERO', '', 16),
(5, 1, 2, 2, '0', '00', '00', '0', '0', '0', '00000', '00', '0', '0', '0', '00', 0.000000, '0', '0', '6', 'ANDREINA GONZALES', '', 17),
(6, 1, 1, 1, '0', '0', '00', '0', '00', '0', '0', '00', '00', '0', '0', '0', 0.000000, '00', '0', '8', 'VICTOR ZUÑIGA', '', 16),
(7, 1, 2, 2, '0', '0', '00', '0', '0', '00', '0', '00', '0', '0', '0', '0', 0.000000, '00', '0', '6', 'ANDREINA GONZALES', '', 22),
(8, 1, 1, 1, '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 0.000000, '0', '0', '21', 'INKAFERRO', '', 34),
(9, 1, 1, 1, '0', '0', '0', '00', '0', '0', '0', '0', '00', '0', '00', '0', 0.000000, '0', '0', '21', 'INKAFERRO', '', 14),
(10, 1, 1, 1, '0', '0', '0', '00', '0', '0', '0', '0', '00', '0', '00', '0', 0.000000, '0', '0', '21', 'INKAFERRO', '', 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etiquetas`
--

CREATE TABLE `etiquetas` (
  `id` int(11) NOT NULL,
  `clave` varchar(255) NOT NULL COMMENT 'Clave',
  `valor` text NOT NULL COMMENT 'Valor',
  `estado` tinyint(4) NOT NULL COMMENT 'Estado',
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha',
  `idlocalidad` int(11) NOT NULL COMMENT 'Localidad'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formulario`
--

CREATE TABLE `formulario` (
  `idFormulario` int(11) NOT NULL,
  `nombreGeneralFormulario` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `funcionFormulario` text COLLATE utf8_spanish_ci,
  `nombreFormularioWeb` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `raizFormularioWeb` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nombreFormularioMovil` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `raizFormulariomovil` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `estadoFormulario` int(2) DEFAULT NULL,
  `fechaUMFormulario` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `formulario`
--

INSERT INTO `formulario` (`idFormulario`, `nombreGeneralFormulario`, `funcionFormulario`, `nombreFormularioWeb`, `raizFormularioWeb`, `nombreFormularioMovil`, `raizFormulariomovil`, `estadoFormulario`, `fechaUMFormulario`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `itm1`
--

CREATE TABLE `itm1` (
  `ItemCode` varchar(20) NOT NULL,
  `PriceList` int(11) NOT NULL,
  `Price` decimal(19,6) DEFAULT NULL,
  `Currency` varchar(3) DEFAULT NULL,
  `Factor` decimal(19,6) DEFAULT NULL,
  `UserSign` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `UserSign2` int(11) DEFAULT NULL,
  `UpdateDate` datetime DEFAULT NULL,
  `U_4MIGRADO` varchar(50) DEFAULT NULL,
  `U_4MIGRADOCONCEPTO` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `localidad`
--

CREATE TABLE `localidad` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre',
  `descripcion` text NOT NULL COMMENT 'Descripcion',
  `estado` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Estado',
  `cod_loc` varchar(255) DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `localidad`
--

INSERT INTO `localidad` (`id`, `nombre`, `descripcion`, `estado`, `cod_loc`, `fecha`) VALUES
(1, 'EXXIS', 'ES-BO', 1, 'ES-BO', '2019-06-12 19:53:49'),
(2, 'APPLE', 'ES-AR', 1, 'ES-AR', '2019-06-12 19:53:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1556901105),
('m130524_201442_init', 1556901107),
('m190124_110200_add_verification_token_column_to_user_table', 1556901107);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ocrd`
--

CREATE TABLE `ocrd` (
  `CardCode` varchar(15) NOT NULL,
  `CardName` varchar(100) DEFAULT NULL,
  `CardType` char(1) DEFAULT NULL,
  `GroupCode` int(11) DEFAULT NULL,
  `Address` varchar(100) DEFAULT NULL,
  `Phone1` varchar(20) DEFAULT NULL,
  `Phone2` varchar(20) DEFAULT NULL,
  `CntctPrsn` varchar(90) DEFAULT NULL,
  `Notes` varchar(100) DEFAULT NULL,
  `Balance` decimal(19,6) DEFAULT NULL,
  `GroupNum` int(11) DEFAULT NULL,
  `CreditLine` decimal(19,6) DEFAULT NULL,
  `LicTradNum` varchar(32) DEFAULT NULL,
  `ListNum` int(11) DEFAULT NULL,
  `Free_Text` text,
  `Currency` varchar(3) DEFAULT NULL,
  `CardFName` varchar(100) DEFAULT NULL,
  `SlpCode` int(11) DEFAULT NULL,
  `validFor` char(1) DEFAULT NULL,
  `validFrom` datetime DEFAULT NULL,
  `validTo` datetime DEFAULT NULL,
  `frozenFor` char(1) DEFAULT NULL,
  `frozenFrom` datetime DEFAULT NULL,
  `frozenTo` datetime DEFAULT NULL,
  `UserSign` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `UserSign2` int(11) DEFAULT NULL,
  `UpdateDate` datetime DEFAULT NULL,
  `U_4LATITUD` varchar(100) DEFAULT NULL,
  `U_4LONGITUD` varchar(100) DEFAULT NULL,
  `U_4MIGRADO` varchar(50) DEFAULT NULL,
  `U_4MIGRADOCONCEPTO` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oitm`
--

CREATE TABLE `oitm` (
  `ItemCode` varchar(20) NOT NULL,
  `ItemName` varchar(100) DEFAULT NULL,
  `FrgnName` varchar(100) DEFAULT NULL,
  `ItmsGrpCod` int(11) DEFAULT NULL,
  `CodeBars` varchar(254) DEFAULT NULL,
  `SellItem` char(1) DEFAULT NULL,
  `OnHand` decimal(19,6) DEFAULT NULL,
  `IsCommited` decimal(19,6) DEFAULT NULL,
  `OnOrder` decimal(19,6) DEFAULT NULL,
  `SalUnitMsr` varchar(100) DEFAULT NULL,
  `PicturName` varchar(200) DEFAULT NULL,
  `UserText` text,
  `ManBtchNum` char(1) DEFAULT NULL,
  `validFor` char(1) DEFAULT NULL,
  `validFrom` datetime DEFAULT NULL,
  `validTo` datetime DEFAULT NULL,
  `frozenFor` char(1) DEFAULT NULL,
  `frozenFrom` datetime DEFAULT NULL,
  `frozenTo` datetime DEFAULT NULL,
  `UserSign` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `UserSign2` int(11) DEFAULT NULL,
  `UpdateDate` datetime DEFAULT NULL,
  `U_4MIGRADO` varchar(50) DEFAULT NULL,
  `U_4MIGRADOCONCEPTO` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordr`
--

CREATE TABLE `ordr` (
  `DocEntry` int(11) NOT NULL,
  `DocNum` int(11) NOT NULL,
  `DocType` char(1) DEFAULT NULL,
  `CANCELED` char(1) DEFAULT NULL,
  `Printed` char(1) DEFAULT NULL,
  `DocStatus` char(1) DEFAULT NULL,
  `DocDate` datetime DEFAULT NULL,
  `DocDueDate` datetime DEFAULT NULL,
  `CardCode` varchar(15) DEFAULT NULL,
  `CardName` varchar(100) DEFAULT NULL,
  `NumAtCard` varchar(100) DEFAULT NULL,
  `DiscPrcnt` decimal(19,6) DEFAULT NULL,
  `DiscSum` decimal(19,6) DEFAULT NULL,
  `DocCur` varchar(3) DEFAULT NULL,
  `DocRate` decimal(19,6) DEFAULT NULL,
  `DocTotal` decimal(19,6) DEFAULT NULL,
  `PaidToDate` decimal(19,6) DEFAULT NULL,
  `Ref1` varchar(11) DEFAULT NULL,
  `Ref2` varchar(11) DEFAULT NULL,
  `Comments` varchar(254) DEFAULT NULL,
  `JrnlMemo` varchar(50) DEFAULT NULL,
  `GroupNum` int(11) DEFAULT NULL,
  `SlpCode` int(11) DEFAULT NULL,
  `Series` int(11) DEFAULT NULL,
  `TaxDate` datetime DEFAULT NULL,
  `LicTradNum` varchar(32) DEFAULT NULL,
  `Address` varchar(250) DEFAULT NULL,
  `UserSign` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `UserSign2` int(11) DEFAULT NULL,
  `UpdateDate` datetime DEFAULT NULL,
  `U_4MOTIVOCANCELADO` int(11) DEFAULT NULL,
  `U_4NIT` varchar(30) DEFAULT NULL,
  `U_4RAZON_SOCIAL` varchar(100) DEFAULT NULL,
  `U_LATITUD` varchar(30) DEFAULT NULL,
  `U_LONGITUD` varchar(30) DEFAULT NULL,
  `U_4SUBTOTAL` decimal(18,6) DEFAULT NULL,
  `U_4DOCUMENTOORIGEN` char(10) CHARACTER SET utf8 DEFAULT NULL,
  `U_4MIGRADOCONCEPTO` varchar(250) DEFAULT NULL,
  `U_4MIGRADO` char(1) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oslp`
--

CREATE TABLE `oslp` (
  `SlpCode` int(11) NOT NULL,
  `SlpName` varchar(155) NOT NULL,
  `Locked` char(1) DEFAULT NULL,
  `Active` char(1) DEFAULT NULL,
  `Telephone` varchar(20) DEFAULT NULL,
  `Mobil` varchar(50) DEFAULT NULL,
  `Fax` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `UserSign` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `UserSign2` int(11) DEFAULT NULL,
  `UpdateDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `owhs`
--

CREATE TABLE `owhs` (
  `WhsCode` varchar(8) NOT NULL,
  `WhsName` varchar(100) DEFAULT NULL,
  `Locked` char(1) DEFAULT NULL,
  `Street` varchar(100) DEFAULT NULL,
  `Block` varchar(100) DEFAULT NULL,
  `ZipCode` varchar(20) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `County` varchar(100) DEFAULT NULL,
  `Country` varchar(3) DEFAULT NULL,
  `State` varchar(3) DEFAULT NULL,
  `Location` int(11) DEFAULT NULL,
  `UserSign` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `UserSign2` int(11) DEFAULT NULL,
  `UpdateDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `idPersona` int(11) NOT NULL,
  `nombrePersona` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `apellidoPPersona` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `apellidoMPersona` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `estadoPersona` int(2) DEFAULT NULL,
  `fechaUMPersona` datetime DEFAULT NULL,
  `documentoIdentidadPersona` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`idPersona`, `nombrePersona`, `apellidoPPersona`, `apellidoMPersona`, `estadoPersona`, `fechaUMPersona`, `documentoIdentidadPersona`) VALUES
(1, 'Administrador', 'Exxis', NULL, 1, '2019-05-20 10:49:14', '45612345'),
(7, 'Miguel', 'Paterno', 'Materno', 1, '2019-05-20 10:49:14', '45678978'),
(8, 'Miguel', 'Paterno', 'Materno', 1, '2019-05-20 10:49:14', '78912345'),
(9, 'Prueba1', 'Prueba12', 'Prueba13', 1, '2019-05-20 10:49:14', NULL),
(10, 'pedro', 'picapiedra', 'xx', 1, '2019-05-20 10:49:14', NULL),
(11, 'pedro', 'picapiedra', 'xxx', 1, '2019-05-20 10:49:14', NULL),
(12, 'Pablo', 'Marmol', 'xxx', 1, '2019-05-20 10:49:14', NULL),
(13, 'prueba 02', 'prueba 02', 'prueba 02', 1, '2019-05-20 10:49:14', NULL),
(14, 'Pablo', 'Marmol', 'xxx', 1, '2019-05-20 10:49:14', NULL),
(15, 'Pablo', 'Marmol', 'xxx', 1, '2019-05-20 10:49:14', NULL),
(16, 'prueba003', 'prueba003', 'prueba003', 1, '2019-05-20 10:49:14', NULL),
(17, 'Mauricio', 'arriaza', 'sn', 1, '2019-05-20 10:49:14', NULL),
(18, 'Juan Carlos', 'Alvarado', 'sn', 1, '2019-05-20 10:49:14', NULL),
(19, 'eddy', 'quenallata', 'ss', 1, NULL, NULL),
(20, 'Ruben', 'Lecona', 'ss', 1, NULL, NULL),
(21, 'eddy', 'quenallata', 'ss', 1, NULL, NULL),
(22, 'miguel', 'angel', 'choque', 1, NULL, NULL),
(23, 'Rodrigo', 'cruz', 'ss', NULL, NULL, NULL),
(24, 'miguel', 'angel', 'centellas', NULL, NULL, NULL),
(25, 'Ismael', 'Bautista', 'ss', NULL, NULL, NULL),
(26, 'Moises Pablo', 'Nava', 'Sangüeza', NULL, NULL, NULL),
(27, 'usuario', 'usuario', 'master', NULL, NULL, NULL),
(28, 'usuario', 'usuario', 'master', NULL, NULL, NULL),
(29, 'usuario', 'usuario', 'master', NULL, NULL, NULL),
(30, 'usuario', 'usuario', 'master', NULL, NULL, NULL),
(31, 'usuario', 'usuario', 'master', NULL, NULL, NULL),
(32, 'usuario', 'usuario', 'master', NULL, NULL, NULL),
(33, 'usuario', 'usuario', 'master', NULL, NULL, NULL),
(34, 'usuario', 'usuario', 'master', NULL, NULL, NULL),
(35, 'usuario', 'usuario', 'master', NULL, NULL, NULL),
(36, 'unicoUsuario', 'usuario', 'usuario', NULL, NULL, NULL),
(37, 'unicoUsuario', 'usuario', 'usuario', NULL, NULL, NULL),
(38, 'usuario50', 'usuario', 'usuario', NULL, NULL, NULL),
(39, 'usuario50', 'usuario', 'usuario', NULL, NULL, NULL),
(40, 'usuario50', 'usuario', 'usuario', NULL, NULL, NULL),
(41, 'unicoUsuario5000', 'usuario', 'usuario', NULL, NULL, NULL),
(42, 'usuario', 'usuario', 'usuario', NULL, NULL, NULL),
(43, 'usuario50', 'usuario', 'usuario', NULL, NULL, NULL),
(44, 'prueba0030', 'us', 'uus', NULL, NULL, NULL),
(45, 'user', 'user', 'user', NULL, NULL, NULL),
(46, 'usuario', 'usuario', 'usuario', NULL, NULL, NULL),
(47, 'usuario', 'usuario', 'usuario', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rdr1`
--

CREATE TABLE `rdr1` (
  `DocEntry` int(11) NOT NULL,
  `DocNum` int(11) NOT NULL,
  `LineNum` int(11) NOT NULL,
  `BaseType` int(11) DEFAULT NULL,
  `BaseEntry` int(11) DEFAULT NULL,
  `BaseLine` int(11) DEFAULT NULL,
  `LineStatus` char(1) DEFAULT NULL,
  `ItemCode` varchar(50) DEFAULT NULL,
  `Dscription` varchar(100) DEFAULT NULL,
  `Quantity` decimal(19,6) DEFAULT NULL,
  `OpenQty` decimal(19,6) DEFAULT NULL,
  `Price` decimal(19,6) DEFAULT NULL,
  `Currency` varchar(3) DEFAULT NULL,
  `DiscPrcnt` decimal(19,6) DEFAULT NULL,
  `LineTotal` decimal(19,6) DEFAULT NULL,
  `WhsCode` varchar(50) DEFAULT NULL,
  `CodeBars` varchar(254) DEFAULT NULL,
  `PriceAfVAT` decimal(19,6) DEFAULT NULL,
  `TaxCode` varchar(8) DEFAULT NULL,
  `U_4DESCUENTO` decimal(19,6) DEFAULT NULL,
  `U_4LOTE` varchar(30) DEFAULT NULL,
  `GrossBase` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_estados`
--

CREATE TABLE `tipos_estados` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(240) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tipos_estados`
--

INSERT INTO `tipos_estados` (`id`, `nombre`) VALUES
(1, 'cuentasss'),
(2, 'plazos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_impresoras`
--

CREATE TABLE `tipos_impresoras` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(240) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tipos_impresoras`
--

INSERT INTO `tipos_impresoras` (`id`, `nombre`) VALUES
(1, 'print'),
(2, 'epson');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_precios`
--

CREATE TABLE `tipos_precios` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tipos_precios`
--

INSERT INTO `tipos_precios` (`id`, `nombre`) VALUES
(1, 2147483647),
(2, NULL),
(3, NULL),
(4, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `verification_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_token` text COLLATE utf8_unicode_ci NOT NULL,
  `idPersona` int(11) NOT NULL,
  `estadoUsuario` int(2) NOT NULL,
  `fechaUMUsuario` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `plataformaUsuario` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `plataformaPlataforma` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `plataformaEmei` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reset` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `status`, `created_at`, `updated_at`, `verification_token`, `access_token`, `idPersona`, `estadoUsuario`, `fechaUMUsuario`, `plataformaUsuario`, `plataformaPlataforma`, `plataformaEmei`, `reset`) VALUES
(8, 'admin', 'lKkiXFMtKI6kpmBxxvp7m4h916knCSmm', '$2y$12$lhBNgyoeEMd8bExXOjZMtOeg/OzSAep5LfV6Hfo4lQRJL51yBE9Iu', NULL, 10, 1559063448, 1559063448, 'WgEwhMXKpN7w3TFlHQth4ZPTDVi0_apY_1559063448', '447a54582d3359663041554c4a715a4a3434334a7a4e5f6771366d6936614479313536313634313134375f31353631363431313437', 1, 1, '2019-05-28 00:00:00', 'w', '0', '0', 1),
(9, 'mobil', 'Ytef5sn33yloOjtgOp4CM-na7IqmWcdv', '$2y$13$f1mFdNWgg4w6n.N64FCNN.R86GzMaLpXyLUJjpp.ABtED60crIoP2', NULL, 9, 1559063537, 1559063537, 'dqwXB8u5nDKhH8hce7-JZxRIAYvAhxio_1559063537', '48586a616e6d6d666e57355630744746686953743145554b514c334342714b70313536303737393735315f31353630373739373531', 1, 1, '2019-05-28 00:00:00', 'm', 'Android', '879364570727558', 0),
(10, 'mau', 'Pq8lfjgPVrpRXBGZI9Uk2DWtBggrlGJy', '$2y$12$I.B9nqnjWQ./mR9/eQs7e.ROL6ZEd8NS7npr1Xu2IbRd.S5vq.zfa', NULL, 9, 1559074185, 1559074185, '9xhci6-xVahfu1jXfgobSA7uvkIsQ8w-_1559074185', '384c417573526231743433674b4f3936625363314e5a36653859783757703168313535393037343138355f31353539303734313835', 1, 1, '2019-05-28 00:00:00', 'w', '0', '879364570727558', 0),
(11, 'miguel', '-7_XQiWXKW5fCoQOmsvIKGC-kZ6URMUf', '$2y$13$yv8slu9cSchzKWzlyVpY2uMYe70HAdCl7GbRwQVxEaqxX9dioa77O', NULL, 10, 12321321, 2147483647, NULL, '', 2, 1, '2019-05-29 11:02:18', 'm', 'Android', '879364570727558', 1),
(12, 'exxis', 'ozlq_w3gNQ8xMDN2TnhUlgp8nnYFtHZa', '$2y$13$C5DUQZ6I8osKSvoTNJRNP.sGV0aOl8REZ.A8scqwdEg5iShoa6pEG', NULL, 9, 1559168638, 1559168638, '6QoYnV8wlvnrrhpggxpLc_UDOmUlkCjo_1559168638', '4c70674e766f5656304132306c6c485951474966545374326b7a6632744e7459313536313634393532385f31353631363439353238', 1, 1, '2019-05-30 00:00:00', 'm', 'Android', '867204041565474', 1),
(13, 'ppicapiedra', '91wfWFyENrXBqH_CSXpY9IiTf4fywECc', '$2y$13$NHhmtIrTZ071AMUrvBiQg.b.1oi1FQAMtGow/wNbJZc/nqcdh3Jh.', NULL, 10, 0, 0, NULL, '', 12, 1, '2019-05-31 11:35:14', 'w', '0', '879364570727558', 1),
(14, 'pmarmol', '000000aaaa', '$2y$13$tCWNcaDcmSOXM.25P/umgOU4Fu3pkbQOQqld3gJ5YlqSYkfl4nzNu', NULL, 10, 1559317504, 1559317504, NULL, '0', 15, 1, '2019-05-31 11:45:03', 'w', '0', '879364570727558', 0),
(15, 'prueba2', '000000aaaa', '$2y$13$8.xm/rR90DYMfAq4gGNcFuru5gi0mNcDRkr3UQFiGzlTM1ihzaSI.', NULL, 10, 1559318161, 1559318161, NULL, '0', 15, 1, '2019-05-31 11:56:00', 'w', '0', '879364570727558', 0),
(16, 'prueba3', '000000aaaa', '$2y$13$F6LnKIXtvMv175hnFTUxXOxlyt6HO06comPZ5rwv37YHHJP6Hq0se', NULL, 10, 1559318389, 1559318389, NULL, '0', 15, 1, '2019-05-31 11:59:48', 'w', '0', '879364570727558', 0),
(17, 'prueba4', '000000aaaa', '$2y$13$lJrD4GEdmNt0yhFFB1FoaOg.1HYHnhnscjq8GBBwUGZOvriFgay6C', NULL, 10, 1559318449, 1559318449, NULL, '0', 15, 1, '2019-05-31 12:00:49', 'w', '0', '879364570727558', 0),
(18, 'prueba5', '000000aaaa', '$2y$13$.Ur70PL3q14i3IxstskO/.WccUOVWMmAKjhV0/aumJoPX.GInLUAG', NULL, 10, 1559318615, 1559318615, NULL, '0', 15, 1, '2019-05-31 12:03:35', 'w', '0', '879364570727558', 0),
(19, 'mauricio', 'FKhctNVWBpuAbIqCJRWhEp78mIs2Q26g', '$2y$13$.fzAyNUzPCcgr2aBrGn09uL2cZpZPYcyPgC5OlA4OA17U/F7uuJ6S', NULL, 10, 1559338468, 1559338468, NULL, '32384d457a626f46333576504a622d49596e354d592d706a66453142424d5576313536303532363631335f31353630353236363133', 17, 1, '2019-05-31 17:34:27', 'm', 'Android', '879364570727558', 0),
(20, 'jcarlos', '000000aaaa', '$2y$13$.CdFJi10.DqHTq4kZhAIIONRDaYiF5dv7we9KdC2/U0fqWmTSy0Ce', NULL, 10, 1559339220, 1559339220, NULL, '54436d41724a5f74654f30444f5f5369566c74784f6139324b775232315a554f313536303434363834325f31353630343436383432', 13, 1, '2019-05-31 17:47:00', 'm', 'Android', '879364570727558', 0),
(21, 'jcarlos1', '000000aaaa', '$2y$13$sSm.JQVPlf76kxxh38i.k.SuuKWqOVI2IWW9mFr6tmyGoqb5EVSqK', NULL, 10, 1559339355, 1559339355, NULL, '0', 13, 1, '2019-05-31 17:49:14', 'm', 'Android', '879364570727558', 0),
(22, 'usuario2', '000000aaaa', '$2y$13$SM1Dqsj4HDv1BlJc8jAwHeJV3tkRqLQu.cDqJeH49.fYQxp/Ic6hW', NULL, 10, 1559680240, 1559680240, NULL, '5f42566c77684b642d5a38637856507069614345546a525f492d4d6147685648313535393638313335395f31353539363831333539', 20, 1, '2019-06-04 16:30:40', 'm', 'Android', '879364570727558', 0),
(23, 'usuario1', '000000aaaa', '$2y$13$s2RLA7mtZHYQotp7OZLdlO/LTRQmQF4D0fj80gYVlbzT3nudxFA4C', NULL, 10, 1559680659, 1559680659, NULL, '76784e435156746f4e30772d4e6b76304a6b393148684f5631716b6762343834313535393638313335355f31353539363831333535', 21, 1, '2019-06-04 16:37:39', 'm', 'Android', '879364570727558', 0),
(24, 'usuario3', '000000aaaa', '$2y$13$1UIiMbaTcW0RQ2HBPwp0jO4QCIBbq/sXktAx2f7dhzUg6eh1HE1Tm', NULL, 10, 1559680762, 1559680762, NULL, '634b6e774c75464f656c7854317068586d7654397a75484a6b572d31775a5235313535393638313433345f31353539363831343334', 22, 1, '2019-06-04 16:39:21', 'm', 'Android', '879364570727558', 0),
(25, 'usuario4', '000000aaaa', '$2y$13$G0ZAOa4RPRO052JB3xSFru.YmFZH3QCC0d/coPj/CYd03881TZQba', NULL, 10, 1559680882, 1559680882, NULL, '69636d7735336b7573514b4f58566f6f455166422d67306c3750496d43756b44313535393638313331395f31353539363831333139', 23, 1, '2019-06-04 16:41:23', 'm', 'Android', '879364570727558', 0),
(26, 'usuario5', '000000aaaa', '$2y$13$LZLZAQ5jX03zs7/ElqQTHeu/J285BkFNlqpj8ygIgwHRzcHfi683a', NULL, 10, 1559681194, 1559681194, NULL, '0', 24, 1, '2019-06-04 16:46:34', 'm', 'Android', '879364570727558', 0),
(27, 'usuario6', '000000aaaa', '$2y$13$LSEgNktxDbFY.vDg8nsvvu6W/Ha6Tx4zsZDl1WfgAcNOwYYm5pmM2', NULL, 10, 1559681284, 1559681284, NULL, '75634e3370364a7a7664624346443565752d5551377a474c554664466c435766313535393638313535385f31353539363831353538', 25, 1, '2019-06-04 16:48:04', 'm', 'Android', '879364570727558', 0),
(28, 'mnava', 'PickFJ5SvaRU2qra2OavjrrqVwmvHMUP', '$2y$13$XbCicKy1VKXDiLjA4.ugA.q3rRHoJloXORZewjKmXe/tQRqRJiMkC', NULL, 10, 1559749247, 1559749247, NULL, '6479516672553649385548685844314b6c5055532d48666a755867617a6a746e313536303837333934345f31353630383733393434', 26, 1, '2019-06-05 11:40:46', 'w', '0', '0', 0),
(29, 'usuario7', '000000aaaa', '$2y$13$r/TNuaTfPlTfKenrbIGAY.Q4WulRCfo5quQp0zy28oOw9UVH2twBa', NULL, 10, 1559749341, 1559749341, NULL, '0', 19, 1, '2019-06-05 11:42:21', 'w', '0', '879364570727558', 0),
(30, 'usuario8', '000000aaaa', '$2y$13$OAmGYeZptCLiCZ6Spzwew.YGeEeeSK0mDU48vzg977QMifp6MZT3K', NULL, 10, 1559749477, 1559749477, NULL, '0', 19, 1, '2019-06-05 11:44:37', 'w', '0', '879364570727558', 0),
(31, 'usuario9', '000000aaaa', '$2y$13$/fKMl9U43B4aiglFQtu/BujQnchoTuC0Kzycavv3pNnDUwUnGdvKW', NULL, 10, 1559749706, 1559749706, NULL, '0', 19, 1, '2019-06-05 11:48:25', 'w', '0', '879364570727558', 0),
(32, 'admin2', '000000aaaa', '$2y$13$W7d6Ea.0OBQTpoqtCG5yteM23nwryNLCzIfi.7SZjePeyZE0PETZe', NULL, 10, 1559774349, 1559774349, NULL, '0', 1, 1, '2019-06-05 18:39:08', 'w', '0', '879364570727558', 0),
(33, 'usuarioMaster', 'orr3uGjz2OjLCrLyMludiz4YsIvPBWNv', '$2y$13$C2FyC8bKag6snJSXG5UkQuw1Pyvcqq9Y2E.cyH45zWBAOXB13GHhi', NULL, 10, 1560872061, 1560872061, NULL, '0', 35, 1, '2019-06-18 11:34:21', 'w', '0', '0', 0),
(34, 'unicoUsuario1', 'AHdHtVc8r94QNH53caxw_BIBTgHeRCWi', '$2y$13$Ic.85tY60lX6z7GvDA1UaeQfC1Vpyn6Kn96wg4qavqtoTbyF5Qx1G', NULL, 10, 1560957026, 1560957026, NULL, '0', 0, 1, '2019-06-19 11:10:25', 'w', '0', '0', 0),
(35, 'usuarioUnico2', '000000aaaa', '$2y$13$ZDAmWSbH5oiS6bAyF5WWWeeEFPV3kJIojjJL6RzuiyWsel4ZM0QuC', NULL, 10, 1560957580, 1560957580, NULL, '0', 0, 1, '2019-06-19 11:19:39', 'w', '0', '0', 0),
(36, 'emulador', '45gMLYGJYeYvAt05nWgc6Qk4nYYJoOHU', '$2y$13$wX2m4gFss05rVtF6XB3l5OPgQ6tsAU7eoH.psQWBqPoy74kE2NeCO', NULL, 10, 1560975109, 1560975109, NULL, '5575716239706132645346796549734e4c6456476a675f574756756a37397954313536313537393239335f31353631353739323933', 0, 1, '2019-06-19 16:11:49', 'm', 'Android', '358240051111110', 1),
(37, 'mnava25', 'NWyXtcox8P6OH0ThOk_m_Ke6FWvIjXhE', '$2y$13$lwfpu20z8t/qL92OTECXv.P4wJE3I7ZLwNge5ugI2DA7ZG4I6U0A.', NULL, 10, 1560980226, 1560980226, NULL, '69434649725f4f5445576f4a646d5764726547726d52456a557a486262586866313536303938323838355f31353630393832383835', 26, 1, '2019-06-19 17:37:05', 'm', 'Android', '865591035356882', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idUsuario` int(11) NOT NULL,
  `nombreUsuario` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `claveUsuario` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `idPersona` int(11) DEFAULT NULL,
  `estadoUsuario` int(2) DEFAULT NULL,
  `fechaUMUsuario` datetime DEFAULT NULL,
  `plataformaUsuario` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `plataformaPlataforma` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `plataformaEmei` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idUsuario`, `nombreUsuario`, `claveUsuario`, `idPersona`, `estadoUsuario`, `fechaUMUsuario`, `plataformaUsuario`, `plataformaPlataforma`, `plataformaEmei`) VALUES
(1, 'admin', 'web', 1, 1, '2019-05-20 10:46:46', 'w', '0', '0'),
(2, 'admin', 'movil', 1, 1, '2019-05-20 10:47:37', 'm', 'Android', '357870066679189'),
(3, 'micky', 'admin', 1, 1, '2019-05-30 00:00:00', 'm', 'Android', '358240051111110');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `configapp`
--
ALTER TABLE `configapp`
  ADD PRIMARY KEY (`idUsuario`);

--
-- Indices de la tabla `configsap`
--
ALTER TABLE `configsap`
  ADD PRIMARY KEY (`idUsario`);

--
-- Indices de la tabla `config_usuarios`
--
ALTER TABLE `config_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idEstado` (`idEstado`),
  ADD KEY `idTipoPrecio` (`idTipoPrecio`),
  ADD KEY `idTipoImpresora` (`idTipoImpresora`);

--
-- Indices de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_etiquetas_localidad_idx` (`idlocalidad`);

--
-- Indices de la tabla `formulario`
--
ALTER TABLE `formulario`
  ADD PRIMARY KEY (`idFormulario`);

--
-- Indices de la tabla `itm1`
--
ALTER TABLE `itm1`
  ADD PRIMARY KEY (`ItemCode`,`PriceList`);

--
-- Indices de la tabla `localidad`
--
ALTER TABLE `localidad`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Indices de la tabla `ocrd`
--
ALTER TABLE `ocrd`
  ADD PRIMARY KEY (`CardCode`);

--
-- Indices de la tabla `oitm`
--
ALTER TABLE `oitm`
  ADD PRIMARY KEY (`ItemCode`);

--
-- Indices de la tabla `ordr`
--
ALTER TABLE `ordr`
  ADD PRIMARY KEY (`DocEntry`);

--
-- Indices de la tabla `owhs`
--
ALTER TABLE `owhs`
  ADD PRIMARY KEY (`WhsCode`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`idPersona`),
  ADD UNIQUE KEY `docuementoIdentidad` (`documentoIdentidadPersona`);

--
-- Indices de la tabla `rdr1`
--
ALTER TABLE `rdr1`
  ADD PRIMARY KEY (`DocNum`,`LineNum`);

--
-- Indices de la tabla `tipos_estados`
--
ALTER TABLE `tipos_estados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipos_impresoras`
--
ALTER TABLE `tipos_impresoras`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipos_precios`
--
ALTER TABLE `tipos_precios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `config_usuarios`
--
ALTER TABLE `config_usuarios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT de la tabla `formulario`
--
ALTER TABLE `formulario`
  MODIFY `idFormulario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `localidad`
--
ALTER TABLE `localidad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ordr`
--
ALTER TABLE `ordr`
  MODIFY `DocEntry` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `idPersona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de la tabla `tipos_estados`
--
ALTER TABLE `tipos_estados`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipos_impresoras`
--
ALTER TABLE `tipos_impresoras`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipos_precios`
--
ALTER TABLE `tipos_precios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `config_usuarios`
--
ALTER TABLE `config_usuarios`
  ADD CONSTRAINT `config_usuarios_ibfk_1` FOREIGN KEY (`idEstado`) REFERENCES `tipos_estados` (`id`),
  ADD CONSTRAINT `config_usuarios_ibfk_2` FOREIGN KEY (`idTipoPrecio`) REFERENCES `tipos_precios` (`id`),
  ADD CONSTRAINT `config_usuarios_ibfk_3` FOREIGN KEY (`idTipoImpresora`) REFERENCES `tipos_impresoras` (`id`);

--
-- Filtros para la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  ADD CONSTRAINT `fk_etiquetas_localidad` FOREIGN KEY (`idlocalidad`) REFERENCES `localidad` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `event_8` ON SCHEDULE AT '2019-06-28 09:12:27' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE user SET access_token = '' WHERE id = 8$$

CREATE DEFINER=`root`@`localhost` EVENT `event_12` ON SCHEDULE AT '2019-06-28 11:32:08' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE user SET access_token = '' WHERE id = 12$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
