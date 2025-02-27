import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Migrate extends Databaseconf {
  public configService: ConfigService;

  private tables = `
                CREATE TABLE IF NOT EXISTS documentos (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  cod varchar(50) NOT NULL,
                  DocEntry varchar(255) not null,
                  DocNum integer NULL,
                  DocType varchar(10) NOT NULL,
                  canceled varchar(255) not NULL,
                  Printed varchar(255) not NULL,
                  DocStatus varchar(255) not NULL,
                  DocDate varchar(255) not null,
                  DocDueDate varchar(255) not null,
                  CardCode varchar(255) not null,
                  CardName varchar(255) NOT NULL,
                  NumAtCard varchar(255) NULL,
                  DiscPrcnt varchar(255) not null,
                  DiscSum varchar(255) not null,
                  DocCur varchar(255) NULL,
                  DocRate varchar(255) NULL,
                  DocTotal integer NULL,
                  PaidToDate varchar(255) NULL,
                  Ref1 varchar(255) NULL,
                  Ref2 varchar(255) NULL,
                  Comments varchar(255) NULL,
                  JrnlMemo varchar(255) NULL,
                  GroupNum varchar(255) NULL,
                  SlpCode varchar(255) NULL,
                  Series varchar(255) NULL,
                  TaxDate varchar(255) NOT NULL,
                  LicTradNum varchar(255) NULL,
                  Address varchar(255) NOT NULL,
                  UserSign varchar(255) NULL,
                  CreateDate varchar(255) NULL,
                  UserSign2 varchar(255) NULL,
                  UpdateDate varchar(255) NULL,
                  U_4MOTIVOCANCELADO varchar(255) NULL,
                  U_4NIT varchar(255) NULL,
                  U_4RAZON_SOCIAL varchar(255) NULL,
                  U_LATITUD varchar(255) NULL,
                  U_LONGITUD varchar(255) NULL,
                  U_4SUBTOTAL varchar(255) NULL,
                  U_4DOCUMENTOORIGEN varchar(255) NULL,
                  U_4MIGRADOCONCEPTO varchar(255) NULL,
                  U_4MIGRADO varchar(255) NULL,
                  PriceListNum varchar(255) NULL,
                  estadosend varchar(255) NULL,
                  fecharegistro varchar(20) NOT NULL,
                  fechaupdate varchar(20) NULL,
                  fechasend varchar(20) NOT NULL,
                  key varchar(255) NULL,
                  idUser varchar(255) NULL,
                  estado varchar(255),
                  gestion varchar(255) NULL,
                  mes varchar(255) NULL,
                  correlativo integer NULL,
                  rowNum varchar(255) NULL,
                  descuento integer NOT NULL,
                  tipocambio integer NULL,
                  currency VARCHAR(20) NULL,
                  clone integer NULL,
                  tipodescuento integer NULL,
                  federalTaxId VARCHAR(100) NOT NULL,
                  cardNameAux VARCHAR(150) NOT NULL,
                  PayTermsGrpCode VARCHAR(150) NOT NULL,
                  tipotransaccion VARCHAR(10) NULL,
                  tipoestado VARCHAR(10) NULL,
                  comentario text NULL,
                  cuenta text NULL,
                  origen VARCHAR(20) DEFAULT 'inner',
                  ReserveInvoice VARCHAR(20) DEFAULT 'N',
                  saldo integer DEFAULT 0,
                  Pendiente integer DEFAULT 0,
                  U_LB_NumeroFactura VARCHAR(100) DEFAULT 'null',
                  U_LB_NumeroAutorizac VARCHAR(100) DEFAULT 'null',
                  U_LB_FechaLimiteEmis VARCHAR(100) DEFAULT 'null',
                  U_LB_CodigoControl VARCHAR(100) DEFAULT 'null',
                  U_LB_EstadoFactura VARCHAR(100) DEFAULT 'null',
                  U_LB_RazonSocial VARCHAR(100) DEFAULT 'null',
                  U_LB_TipoFactura VARCHAR(100) DEFAULT 'null',
                  Reserve BOOLEAN DEFAULT false,
                  centrocosto VARCHAR(100) DEFAULT 'null',
                  unidadnegocio VARCHAR(100) DEFAULT 'null',
                  reimpresiones VARCHAR(10) DEFAULT '0',
                  codexternal varchar(255) NOT NULL,
                  grupoproductoscode varchar(20) NULL,
                  U_CodigoCampania varchar(255) NOT NULL,
                  U_Saldo  integer NOT NULL,
                  U_ValorSaldo  integer NOT NULL,
                  U_4MOTIVOCANCELADOCABEZERA varchar(255) NOT NULL,
                  U_DOCENTRY varchar(255) NOT NULL,
                  idSucursalMobile varchar(500) DEFAULT '0',
                  Fex_documento VARCHAR(2) DEFAULT '0',
                  Fex_tipodocumento integer NULL,
                  codeConsolidador varchar(20) NULL,
                  cndpagoname varchar(20) NULL,
                  cuota integer NULL,
                  TipoEnvioDoc varchar(20) NULL,
                  EnvioEvidencia integer NULL,
                  vendedor varchar(255) NULL
                );
                -*-
                
                CREATE TABLE IF NOT EXISTS detalle (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  DocEntry varchar(255) NULL,
                  DocNum varchar(255) NULL,
                  LineNum varchar(255) NULL,
                  BaseType varchar(255) NULL,
                  BaseEntry varchar(255) NULL,
                  BaseLine varchar(255) NULL,
                  LineStatus varchar(255) NULL,
                  ItemCode varchar(255) NULL,
                  Dscription varchar(255) NULL,
                  Quantity integer NULL,
                  OpenQty varchar(255) NULL,
                  Price varchar(255) NULL,
                  Currency varchar(255) NULL,
                  DiscPrcnt integer NULL,
                  LineTotal varchar(255) NULL,
                  WhsCode varchar(255) NULL,
                  CodeBars varchar(255) NULL,
                  PriceAfVAT varchar(255) NULL,
                  TaxCode varchar(255) NULL,
                  U_4DESCUENTO integer NULL,
                  XMPORCENTAJE integer NULL,
                  U_4LOTE varchar(255) NULL,
                  GrossBase varchar(255) NULL,
                  idDocumento varchar(50) NOT NULL,
                  fechaAdd date NOT NULL,
                  unidadid varchar(50) NULL,
                  tc varchar(255) NULL,
                  idCabecera varchar(255) NOT NULL,
                  idProductoPrecio varchar(255) NULL,
                  ProductoPrecio varchar(255) NULL,
                  LineTotalPay integer NULL,
                  DiscTotalPrcnt integer NULL,
                  DiscTotalMonetary integer NULL,
                  icett varchar(20) NULL,
                  icete varchar(20) NULL,
                  icetp varchar(20) NULL,
                  ICEt varchar(20) NULL,
                  ICEe varchar(20) NULL,
                  ICEp varchar(20) NULL,
                  bonificacion integer DEFAULT 0 NOT NULL,
                  combos text NULL,
                  PriceAfterVAT varchar(20) NULL,
                  Rate varchar(20) NULL,
                  TaxTotal varchar(20) NULL,
                  User varchar(20) NULL,
                  Status varchar(20) NULL,
                  DateUpdate varchar(20) NULL,
                  Entregado varchar(20) NULL,
                  Serie text NULL,
                  BaseId integer NULL,
                  IdBonfAut integer NULL,
                  GroupName varchar(255) NOT NULL,
                  codeBonificacionUse varchar(100) NOT NULL,
                  XMPORCENTAJECABEZERA integer NULL,
                  XMPROMOCIONCABEZERA integer NULL,
                  BaseQty integer NULL,
                  grupoproductodocificacion  integer NULL,
                  SumBoniLin varchar(20) NULL,
                  SumBoniCab varchar(20) NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS documentopago(
                  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                  cod VARCHAR(255) NOT NULL,
                  fecha DATE not null,
                  hora TIME NOT NULL,
                  closa text,
                  tipo VARCHAR(20) DEFAULT 'null' NOT NULL,
                  estado INTEGER NOT NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS descuentos(
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  ItemCode varchar(100) NULL,
                  CardCode varchar(100) NULL,
                  PRIORIDAD integer NULL,
                  LINEA integer NULL,
                  LISTA_PRECIO integer NULL,
                  DESCUENTO decimal(15,6) NULL,
                  DESDE DATE NULL,
                  HASTA DATE NULL,
                  TIPO INTEGER NULL,
                  CANTIDAD decimal(15,6) NULL,
                  PROPIEDADES varchar(200) NULL,
                  FABRICANTE varchar(200) NULL,
                  GRUPO_CLIENTE varchar(200) NULL,
                  GRUPO_PRODUCTO varchar(200) NULL,
                  CANTIDAD_LIBRE decimal(15,6) NULL,
                  MAXIMO_LIBRE decimal(15,6) NULL
                );
                /*CREATE TABLE IF NOT EXISTS descuentos(
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  tipodescuento varchar(200) NULL,
                  ItemCode varchar(100) NULL,
                  CardCode varchar(100) NULL,
                  PriceListNum varchar(50) NULL,
                  Price integer NULL,
                  Currency varchar(20) NULL,
                  DiscountPercent varchar(200) NULL,
                  paid varchar(200) NULL,
                  free varchar(200) NULL,
                  max varchar(200) NULL,
                  prioridad varchar(200) NULL,
                  linea varchar(200) NULL,
                  ValidTo varchar(200) NULL,
                  ValidFrom varchar(200) NULL,
                  DateUpdate varchar(200) NULL
                );/*
                -*-
                CREATE TABLE IF NOT EXISTS almacenes(
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  idUser integer not null,
                  Street varchar(255) not null,
                  WarehouseCode varchar(255) not null,
                  State varchar(255) not null,
                  Country varchar(255) not null,
                  City varchar(255) not null,
                  WarehouseName varchar(255) not null,
                  User integer not null,
                  Status integer not null,
                  DateUpdate varchar(255) NOT NULL,
                  idDocumento integer NULL,
                  idDetalle integer NULL,
                  FOREIGN KEY (idDetalle) REFERENCES detalle(id) ON UPDATE CASCADE ON DELETE CASCADE
                );
                -*-
                CREATE TABLE IF NOT EXISTS pagos (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  documentoId varchar(100) NULL,
                  clienteId varchar(50) NOT NULL,
                  formaPago VARCHAR(20) NOT NULL,
                  tipoCambioDolar integer NULL,
                  moneda VARCHAR(20) NULL,
                  monto integer NULL,
                  numCheque VARCHAR(200) NULL DEFAULT NULL,
                  numComprobante VARCHAR(200) NULL DEFAULT NULL,
                  numTarjeta VARCHAR(200) NULL,
                  numAhorro VARCHAR(200) NULL DEFAULT NULL,
                  numAutorizacion VARCHAR(200) NULL,
                  bancoCode VARCHAR(200) NULL,
                  ci VARCHAR(200) NULL,
                  fecha VARCHAR(20) NULL,
                  hora VARCHAR(20) NULL,
                  cambio VARCHAR(20) NULL,
                  monedaDolar integer NULL,
                  monedaLocal integer NULL,
                  estado integer NULL,
                  tipo integer NULL,
                  documentoPagoId varchar(50) NULL,
                  dx varchar(30) NOT NULL,
                  otpp integer DEFAULT 0,
                  centro varchar(50) NULL,
                  baucher varchar(80) NULL,
                  ncuota integer NULL,
                  checkdate varchar(20) NULL,
                  transferencedate varchar(20) NULL,
                  anulado integer NULL,
                  idUser integer NULL,
                   CreditCard integer NULL,
                   correlativo integer NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS cambio (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  idUser varchar(12) NOT NULL,
                  User varchar(10) NULL,
                  Status varchar(20) NULL,
                  DateUpdate varchar(20) NULL,
                  ExchangeRateFrom varchar(20) NULL,
                  ExchangeRateTo varchar(20) NULL,
                  ExchangeRateDate varchar(20) NULL,
                  ExchangeRate varchar(20) NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS combos (
                  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                  TreeCode VARCHAR(255) NULL,
                  ItemCode VARCHAR(255) NULL,
                  Quantity INTEGER NULL,
                  Warehouse VARCHAR(100) NULL,
                  Price INTEGER NULL,
                  Currency VARCHAR(100) NULL,
                  PriceList VARCHAR(100) NULL,
                  ChildNum VARCHAR(10) NULL,
                  ItemName VARCHAR(250) NULL,
                  TreeType VARCHAR(250) NULL,
                  Price2 VARCHAR(10) NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS clientes(
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  img varchar(3000) NULL,
                  idUser integer NOT NULL,
                  CardCode varchar(255) not null,
                  CardName varchar(255) not null,
                  CardType varchar(255),
                  Address varchar(255) not null,
                  CreditLimit varchar(255) not null,
                  MaxCommitment varchar(255) not null,
                  DiscountPercent varchar(255) not null,
                  PriceListNum varchar(255) not null,
                  SalesPersonCode varchar(255) not null,
                  Currency varchar(255),
                  County varchar(255),
                  Country varchar(255) not null,
                  CurrentAccountBalance varchar(255),
                  NoDiscounts varchar(255) not null,
                  PriceMode varchar(255),
                  FederalTaxId varchar(255) not null,
                  PhoneNumber varchar(255) NULL,
                  ContactPerson varchar(255) NOT NULL,
                  PayTermsGrpCode varchar(10) NOT NULL,
                  Latitude varchar(255) NOT NULL,
                  Longitude varchar(255) NOT NULL,
                  GroupCode varchar(255),
                  User varchar(255),
                  Status varchar(255),
                  DateUpdate varchar(255),
                  idDocumento integer NULL,
                  imagen varchar(255) NULL,
                  export integer NULL,
                  celular varchar(10) NULL,
                  pesonacontactocelular varchar(20) NULL,
                  correoelectronico varchar (50) NOT NULL,
                  rutaterritorisap varchar (20) NULL,
                  rutaterritorisaptext varchar (255) NULL,
                  diavisita varchar (250) NULL,
                  comentario text NULL,
                  creadopor varchar(20) NOT NULL,
                  xcodigocliente varchar(20) NOT NULL,
                  fechaset varchar(20) NOT NULL,
                  fechaupdate varchar(20) NOT NULL,
                  razonsocial varchar(255) NULL,
                  tipoEmpresa varchar(20) NULL,
                  cliente_std1 varchar(20) NULL,
                  cliente_std2 varchar(20) NULL,
                  cliente_std3 varchar(20) NULL,
                  cliente_std4 varchar(20) NULL,
                  cliente_std5 varchar(20) NULL,
                  cliente_std6 varchar(20) NULL,
                  cliente_std7 varchar(20) NULL,
                  cliente_std8 varchar(20) NULL,
                  cliente_std9 varchar(20) NULL,
                  cliente_std10 varchar(20) NULL,
                  anticipos varchar(20) NULL,
                  cndpago varchar(20) NULL,
                  cndpagoname varchar(100) NULL,
                  grupoSIN varchar(100) null,
                  iva varchar(100) null,
                  DescuentoG varchar(100) null,
                  DescuentoC varchar(100) null,
                  DescuentoCC varchar(100) null,
                  DescuentoA varchar(100) null,
                  GroupName varchar(100) null,
                  codeCanal  varchar(100) null,
                  codeSubCanal  varchar(100) null,
                  codeTipoTienda  varchar(100) null,
                  cadena  varchar(100) null,
                  codeCadenaConsolidador  varchar(100) null,
                  cadenaTxt  varchar(100) null,
                  Mobilecod  varchar(100) null,
                  cuccs  varchar(500) null,
                  Fex_tipodocumento integer NULL,
                  Fex_complemento varchar(250) null,
                  Fex_codigoexcepcion integer NULL,
                  activo varchar(10) NULL,
                  territorio varchar(100) null,
                  actualizado varchar(5) NULL,
                  U_EXX_TIPOPERS varchar(50) NULL,
                  U_EXX_TIPODOCU varchar(50) NULL,
                  U_EXX_APELLPAT varchar(50) NULL,
                  U_EXX_APELLMAT varchar(50) NULL,
                  U_EXX_PRIMERNO varchar(50) NULL,
                  U_EXX_SEGUNDNO varchar(50) NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS clientessucursales (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  idUser varchar(11) NOT NULL,
                  AddresName varchar(255) NOT NULL,
                  Street varchar(255) NOT NULL,
                  State varchar(255) NULL,
                  FederalTaxId varchar(255) NULL,
                  CreditLimit varchar(255) NULL,
                  CardCode varchar(255) NULL,
                  User varchar(25) NULL,
                  Status varchar(25) NULL,
                  DateUpdate varchar(255) NULL,
                  idDocumento integer NULL,
                  TaxCode varchar(255) NULL,
                  AdresType varchar(25) NULL, 
                  u_zona varchar(255) NULL,
                  u_lat varchar(255) NULL, 
                  u_lon varchar(255) NULL,
                  u_territorio varchar(255) NULL, 
                  u_vendedor varchar(25) NULL,
                  export integer,
                  Tax VARCHAR(10) NULL,
                  LineNum integer NULL,
                  City varchar(10) NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS listaprecios(
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  idUser integer NOT NULL,
                  GroupNum varchar(255) NOT NULL,
                  BasePriceList varchar(255) NOT NULL,
                  PriceListNo varchar(255) NOT NULL,
                  PriceListName varchar(255) NOT NULL,
                  DefaultPrimeCurrency varchar(255) NOT NULL,
                  User varchar(255) NOT NULL,
                  Status varchar(255) NOT NULL,
                  DateUpdate varchar(255),
                  IsGrossPrice varchar(255)
                );
                -*-
                CREATE TABLE IF NOT EXISTS lotes (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  idUser varchar(10) NULL,
                  ItemCode varchar(50) NULL,
                  ItemDescription varchar(60) NULL,
                  ItemStatus varchar(50) NULL,
                  Batch varchar(50) NULL,
                  AdmissionDate varchar(20) NULL,
                  ExpirationDate varchar(20) NULL,
                  Stock varchar(50) NULL,
                  User varchar(10) NULL,
                  Status varchar(2) NULL,
                  DateUpdate varchar(20) NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS productos(
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  idUser integer NOT NULL,
                  BarCode varchar(255) not null,
                  CustomsGroupCode varchar(255) not null,
                  DateUpdate varchar(255) not null,
                  DefaultSalesUoMEntry varchar(255) not null,
                  DefaultWarehouse varchar(255) not null,
                  ForceSelectionOfSerialNumber varchar(255) not null,
                  ForeignName varchar(255) not null,
                  InventoryItem varchar(255) not null,
                  ItemCode varchar(255) not null,
                  ItemName varchar(255) not null,
                  ItemsGroupCode varchar(255) not null,
                  ManageBatchNumbers varchar(255) not null,
                  ManageSerialNumbers varchar(255) not null,
                  ManageStockByWarehouse varchar(255) not null,
                  PurchaseItem varchar(255) not null,
                  PurchaseUnit varchar(255) not null,
                  QuantityOnStock varchar(255) not null,
                  QuantityOrderedByCustomers varchar(255) not null,
                  QuantityOrderedFromVendors varchar(255) not null,
                  SalesItem varchar(255) not null,
                  SalesUnit varchar(255) not null,
                  SalesUnitHeight varchar(255) not null,
                  SalesUnitLength varchar(255) not null,
                  SalesUnitVolume varchar(255) not null,
                  SalesUnitWidth varchar(255) not null,
                  SerialNum varchar(255) not null,
                  Series varchar(255) not null,
                  Status varchar(255) not null,
                  UoMGroupEntry varchar(255) not null,
                  User varchar(255) not null,
                  UserText varchar(255) not null,
                  key varchar(255) not null,
                  ICEt varchar(255) not null,
                  ICEp varchar(255) not null,
                  ICEe varchar(255) not null,
                  GroupName varchar(255) not null,
                  producto_std1 varchar(20) not null,
                  producto_std2 varchar(20) not null,
                  producto_std3 varchar(20) not null,
                  producto_std4 varchar(20) not null,
                  producto_std5 varchar(20) not null,
                  producto_std6 varchar(20) not null,
                  producto_std7 varchar(20) not null,
                  producto_std8 varchar(20) not null,
                  producto_std9 varchar(20) not null,
                  producto_std10 varchar(20) not null,
                  priceListNoms varchar(20) not null,
                  priceListNames varchar(100) not null,
                  combo varchar(100) not null,
                  almacenes text,
                  grupoSIN varchar(100) null,
                  iva varchar(100) null,
                  DescuentoG varchar(100) null,
                  DescuentoC varchar(100) null,
                  DescuentoCC varchar(100) null,
                  DescuentoA varchar(100) null
                );
                -*-
                CREATE TABLE IF NOT EXISTS productos(
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  idUser integer NOT NULL,
                  BarCode varchar(255) not null,
                  CustomsGroupCode varchar(255) not null,
                  DateUpdate varchar(255) not null,
                  DefaultSalesUoMEntry varchar(255) not null,
                  DefaultWarehouse varchar(255) not null,
                  ForceSelectionOfSerialNumber varchar(255) not null,
                  ForeignName varchar(255) not null,
                  InventoryItem varchar(255) not null,
                  ItemCode varchar(255) not null,
                  ItemName varchar(255) not null,
                  ItemsGroupCode varchar(255) not null,
                  ManageBatchNumbers varchar(255) not null,
                  ManageSerialNumbers varchar(255) not null,
                  ManageStockByWarehouse varchar(255) not null,
                  PurchaseItem varchar(255) not null,
                  PurchaseUnit varchar(255) not null,
                  QuantityOnStock varchar(255) not null,
                  QuantityOrderedByCustomers varchar(255) not null,
                  QuantityOrderedFromVendors varchar(255) not null,
                  SalesItem varchar(255) not null,
                  SalesUnit varchar(255) not null,
                  SalesUnitHeight varchar(255) not null,
                  SalesUnitLength varchar(255) not null,
                  SalesUnitVolume varchar(255) not null,
                  SalesUnitWidth varchar(255) not null,
                  SerialNum varchar(255) not null,
                  Series varchar(255) not null,
                  Status varchar(255) not null,
                  UoMGroupEntry varchar(255) not null,
                  User varchar(255) not null,
                  UserText varchar(255) not null,
                  key varchar(255) not null,
                  ICEt varchar(255) not null,
                  ICEp varchar(255) not null,
                  ICEe varchar(255) not null,
                  GroupName varchar(255) not null,
                  producto_std1 varchar(20) not null,
                  producto_std2 varchar(20) not null,
                  producto_std3 varchar(20) not null,
                  producto_std4 varchar(20) not null,
                  producto_std5 varchar(20) not null,
                  producto_std6 varchar(20) not null,
                  producto_std7 varchar(20) not null,
                  producto_std8 varchar(20) not null,
                  producto_std9 varchar(20) not null,
                  producto_std10 varchar(20) not null,
                  priceListNoms varchar(20) not null,
                  priceListNames varchar(100) not null,
                  combo varchar(100) not null,
                  almacenes text,
                  grupoSIN varchar(100) null,
                  iva varchar(100) null,
                  DescuentoG varchar(100) null,
                  DescuentoC varchar(100) null,
                  DescuentoCC varchar(100) null,
                  DescuentoA varchar(100) null
                );
                -*-
                CREATE TABLE IF NOT EXISTS productosalmacenes(
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  idUser number not null,
                  ItemCode varchar(255) not null,
                  WarehouseCode varchar(255) not null,
                  InStock varchar(255) not null,
                  Committed varchar(255) not null,
                  Locked varchar(255) not null,
                  Ordered varchar(255) not null,
                  User varchar(255) not null,
                  Status varchar(255) not null,
                  DateUpdate varchar(255) not null
                );
                -*-
                CREATE TABLE IF NOT EXISTS productosprecios (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  idUser varchar(25) NOT NULL,
                  ItemCode varchar(255) NOT NULL,
                  IdListaPrecios varchar(25) NULL,
                  IdUnidadMedida varchar(25) NULL,
                  Price varchar(100) NULL,
                  Currency varchar(25) NULL,
                  User varchar(25) NOT NULL,
                  Status varchar(2) NOT NULL,
                  DateUpdate varchar(20) NOT NULL,
                  Code varchar(10) NOT NULL,
                  Name varchar(10) NOT NULL,
                  PriceListName varchar(10) NULL,
                  PriceListNo varchar(10) NULL,
                  BaseQty varchar(10) NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS anular(
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  idx varchar(5) NULL,
                  Code varchar(105) NULL,
                  Name varchar(105) NULL,
                  U_TipoAnulacion varchar(105) NULL,
                  User varchar(105) NULL,
                  Status varchar(105) NULL,
                  DateUpdate varchar(50) NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS condicionpago (
                  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                  group_number INTEGER NULL,
                  payment_terms_group_name VARCHAR(15) NULL,
                  start_from VARCHAR(15) NULL,
                  number_of_additional_months VARCHAR(15) NULL,
                  number_of_additional_days VARCHAR(15) NULL,
                  credit_limit VARCHAR(15) NULL,
                  general_discount VARCHAR(15) NULL,
                  interest_on_arrears VARCHAR(15) NULL,
                  price_list_no VARCHAR(15) NULL,
                  load_limit VARCHAR(15) NULL,
                  open_receipt VARCHAR(6) NULL,
                  baseline_date VARCHAR(11) NULL,
                  number_of_installments VARCHAR(15) NULL,
                  number_of_tolerance_days VARCHAR(15) NULL,
                  u__usa_lc VARCHAR(15) NULL,
                  user VARCHAR(15) NULL,
                  date_updated VARCHAR(10) NULL,
                  status VARCHAR(15) NULL,
                  NumberLine VARCHAR(15) NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS tipoActividades (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  descripcion varchar(255) not null
                );
                -*-
                CREATE TABLE IF NOT EXISTS tiposempresa (
                  id varchar(150) NOT NULL,
                  nombre varchar(150) not null,
                  descripcion varchar(150) not null,
                  User varchar(150) null,
                  Status varchar(150) default 0,
                  DateUpdate varchar(150) not null
                );
                -*-
                CREATE TABLE IF NOT EXISTS contactos (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  nombre varchar(150) not null,
                  telefono varchar(50) not null,
                  comentario text null,
                  titulo text null,
                  correo varchar (50) null,
                  cardCode varchar(255) not null,
                  export integer,
                  fecha varchar(20) not null,
                  internalcode text null
                );
                -*-
                CREATE TABLE IF NOT EXISTS divisa (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  iddocdocumento varchar(255),
                  CardCode varchar(255),
                  monedaDe varchar(255),
                  monedaA varchar(255),
                  ratio decimal(10),
                  monto decimal(10),
                  cambio decimal(10),
                  usuario varchar(255),
                  created_at varchar(20),
                  updated_at varchar(20),
                  sap varchar(255) DEFAULT 0,
                  estado integer DEFAULT 0
                );
                -*-
                CREATE TABLE IF NOT EXISTS cuotasfactura (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  iddocpedido varchar(255) NULL,
                  DueDate varchar(20) NULL,
                  Percentage integer NULL,
                  Total integer NULL,
                  InstallmentId integer NULL,
                  usuario integer NULL,
                  fecharegistro varchar(20) NULL,
                  idcliente varchar(255) NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS centrocostos (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  PrcCode varchar(255) NULL,
                  PrcName varchar(255) NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS bancos (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  codigo varchar(50) NULL,
                  cuenta varchar(70) NULL,
                  nombre varchar(255) NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS bonificacion_ca (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  code varchar(255) NULL,
                  U_observacion varchar(255) NULL,
                  cabezera_tipo varchar(255) NULL,
                  cantidad_compra  integer NULL,
                  cantidad_regalo integer NULL,
                  fecha_fin  date NULL,
                  fecha_inicio  date NULL,
                  grupo_cliente  varchar(255) NULL,
                  maximo_regalo integer NULL,
                  nombre  varchar(255) NULL,
                  tipo  varchar(255) NULL,
                  unindad_compra  varchar(255) NULL,
                  unindad_regalo  varchar(255) NULL,
                  extra_descuento  varchar(255) NULL,
                  opcional  varchar(25) NULL,
                  codeMid varchar(255) NULL,
                  codigo_canal varchar(50) NULL,
                  id_regla_bonificacion varchar(50) NULL,
                  Description varchar(100) NULL,
                  TerritoryID varchar(50) NULL,
                  id_cliente_dosificacion varchar(50) NULL,
                  cliente_dosificacion varchar(50) NULL,
                  fijo varchar(10) NULL
                  
                );
                -*-
                CREATE TABLE IF NOT EXISTS bonificacion_regalos (
                  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                  code_regalo VARCHAR(255) NULL,
                  producto_nombre_regalo VARCHAR(255) NULL,
                  code_bonificacion_cabezera VARCHAR(255) NULL,
                  U_regla VARCHAR(255) NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS bonificacion_compras (
                  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                  code_compra VARCHAR(255) NULL,
                  producto_nombre_compra VARCHAR(255) NULL,
                  code_bonificacion_cabezera VARCHAR(255) NULL,
                  U_bonificacion VARCHAR(255) NULL,
                  producto_cantidad  integer NULL,
                  estado integer null
                );
                -*-
                CREATE TABLE IF NOT EXISTS bonificaciones_usadas (
                  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                  code_bonificacion_cabezera VARCHAR(255) NULL,
                  code_compra VARCHAR(255) NULL,
                  cantidad  integer NULL,
                  unidad VARCHAR(255) NULL,
                  cardCode VARCHAR(255) NULL,
                  estado VARCHAR(255) NULL,
                  id_vendedor VARCHAR(255) NULL,
                  idDocumento VARCHAR(255) NULL,
                  idDocumentoDetalle VARCHAR(255) NULL,
                  total VARCHAR(255) NULL

                );
                -*-
                CREATE TABLE IF NOT EXISTS bonificaciones_usadas_detalle (
                  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                  id_bonificaciones_usadas VARCHAR(255) NULL,
                  code_regalo VARCHAR(255) NULL,
                  cantidad  integer NULL,
                  unidad VARCHAR(255) NULL,
                  cardCode VARCHAR(255) NULL,
                  estado VARCHAR(255) NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS geolocalizacion(
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  idequipox VARCHAR(120) NULL DEFAULT NULL,
                  latitud VARCHAR(50) NULL DEFAULT NULL,
                  longitud VARCHAR(50) NULL DEFAULT NULL,
                  fecha DATE NULL DEFAULT NULL,
                  hora TIME NULL DEFAULT NULL,
                  idcliente integer NULL DEFAULT NULL,
                  documentocod VARCHAR(255) NULL DEFAULT NULL,
                  tipodoc VARCHAR(255) NULL DEFAULT NULL,
                  estado integer NULL DEFAULT NULL,
                  actividad VARCHAR(255) NULL DEFAULT NULL,
                  anexo VARCHAR(255) NULL DEFAULT NULL,
                  usuario integer NULL DEFAULT NULL,
                  status integer NULL DEFAULT NULL,
                  dateUpdate DATETIME NULL DEFAULT NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS seriesproductos(
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  DocEntry varchar(100) NOT NULL,
                  ItemCode varchar(100) NOT NULL,
                  SerialNumber varchar(100) NOT NULL,
                  SystemNumber varchar(100) NOT NULL,
                  AdmissionDate varchar(100) NOT NULL,
                  User varchar(100) NOT NULL,
                  Status varchar(100) NOT NULL,
                  Date varchar(100) NOT NULL,
                  WsCode varchar(100) NOT NULL,
                  producto varchar(100) NOT NULL
                );
                -*-
                CREATE TABLE IF NOT EXISTS lotesproductos(
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  ItemCode VARCHAR(50),
                  BatchNum VARCHAR(50),
                  WhsCode VARCHAR(50),
                  Quantity VARCHAR(50),
                  InDate VARCHAR(50),
                  ExpDate VARCHAR(50),
                  BaseNum VARCHAR(50)                
                  
                );
                -*-
                CREATE TABLE IF NOT EXISTS reimpresion (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  fechahora VARCHAR(255),
                  tipodocumento VARCHAR(10),
                  iddocumento VARCHAR(255),
                  usuario VARCHAR(20),
                  equipo VARCHAR(150),
                  estado VARCHAR(150)
                );
                -*-
                CREATE TABLE IF NOT EXISTS visitas(
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  CartCode varchar(100) NULL,
                  CartName varchar(100) NULL,
                  fecha date NOT NULL,
                  hora time NOT NULL,
                  horafin time NOT NULL,
                  lat decimal NOT NULL,
                  lng decimal NOT NULL,
                  foto varchar(200) NOT NULL,
                  estadoEnviado varchar(200),
                  motivoCode varchar(200) NOT NULL,
                  motivoRazon varchar(200) NOT NULL,
                  motivoName varchar(200) NOT NULL,
                  descripcionTxt varchar(200) NOT NULL,
                  img varchar(1000)
                );


                -*-
                CREATE TABLE IF NOT EXISTS dosificacionproductos (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  nombre varchar(150) not null,
                  code varchar(20) not null
                );
                -*-
                CREATE TABLE IF NOT EXISTS tipoActividades (
                    id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                    descripcion varchar(255) not null
                );
                -*-
                CREATE TABLE IF NOT EXISTS asunto(
                    id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                    descripcion varchar(255) not null
                );
                -*-
                CREATE TABLE IF NOT EXISTS estadoActividades(
                    id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                    descripcion varchar(255) not null
                );
                -*-
                CREATE TABLE IF NOT EXISTS actividades(
                    id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                    tipoActividad integer not null,
                    estado integer not null,
                    cardCode varchar(255) not null,
                    PhoneNumber varchar(255) NULL,
                    fecha varchar(255) NULL,
                    hora varchar(255) NULL,
                    asunto integer not null,
                    comentarios varchar(1000) NULL,
                    idUser integer not null,
                    DateUpdate varchar(255) NOT NULL,
                    FOREIGN KEY (tipoActividad) REFERENCES tipoActividades(id) ON UPDATE CASCADE ON DELETE CASCADE,
                    FOREIGN KEY (asunto) REFERENCES asunto(id) ON UPDATE CASCADE ON DELETE CASCADE,
                    FOREIGN KEY (estado) REFERENCES estadoActividades(id) ON UPDATE CASCADE ON DELETE CASCADE
                );
                -*-
                
                CREATE TABLE IF NOT EXISTS companex_canal  (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                docEntry integer  null,
                code varchar(255) NULL,
                name varchar(255) NULL,
                canceled varchar(255) NULL,
                objeto varchar(255) NULL
                );
                -*-
                
                CREATE TABLE IF NOT EXISTS companex_subcanal  (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                docEntry integer  null,
                canal varchar(255) NULL,
                code varchar(255) NULL,
                name varchar(255) NULL,
                objeto varchar(255) NULL,
                canceled varchar(255) NULL
                );
                -*-
                
                CREATE TABLE IF NOT EXISTS companex_cadena  (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                docEntry integer  null,
                tipotienda varchar(255) NULL,
                code varchar(255) NULL,
                name varchar(255) NULL,
                canceled varchar(255) NULL,
                objeto varchar(255) NULL,
                tipodato integer  null
                
                );
                -*-

                  CREATE TABLE IF NOT EXISTS companex_tipotienda  (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  docEntry  integer  null,
                  subcanal varchar(255) NULL,
                  code varchar(255) NULL,
                  name varchar(255) NULL,
                  canceled varchar(255) NULL,
                  objeto varchar(255) NULL
                  );
                  -*-

                  CREATE TABLE IF NOT EXISTS companex_consolidador(
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  docEntry  integer  null,
                  code varchar(255) NULL,
                  name varchar(255) NULL
                  );
                  -*-

                  CREATE TABLE IF NOT EXISTS promociones(
                    id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                    code varchar(255) NULL,
                    name varchar(255) NULL,
                    U_CardCode varchar(255) NULL,
                    U_CodigoCampania varchar(255) NULL,
                    U_ValorGanado varchar(255) NULL,
                    U_FechaInicio varchar(255) NULL,
                    U_FechaFinal varchar(255) NULL,
                    U_DocEntry varchar(255) NULL,
                    U_DocType varchar(255) NULL,
                    U_FechaMaximoCobro varchar(255) NULL,
                    U_ValorSaldo integer NULL,
                    U_Saldo integer NULL,
                    U_Meta integer NULL,
                    U_Acumulado integer NULL,
                    cumpleMeta integer NULL
                    );

                    -*-

                  CREATE TABLE IF NOT EXISTS promocionesUsadas(
                    id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                    code varchar(255) NULL,
                    name varchar(255) NULL,
                    U_CardCode varchar(255) NULL,
                    cod varchar(255) NULL,
                    fecha varchar(255) NULL,
                    U_ValorSaldo integer NULL,
                    U_Saldo integer NULL
                    );  
                    
                    -*-

                    CREATE TABLE IF NOT EXISTS territorios(
                      id integer NOT NULL PRIMARY KEY,
                      TerritoryID integer NOT NULL,
                      Description varchar(255) NULL,
                      LocationIndex integer  NULL,
                      Inactive varchar(10) NULL,
                      Parent varchar(10) NULL,
                      User integer NULL,
                      Status integer NULL,
                      DateUpdate varchar(20) NULL
                      );  
                      
                      -*-

                    CREATE TABLE IF NOT EXISTS bonificacionesDocCabezera(
                      id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                      Code varchar(255) NULL,
                      nombre varchar(50) NULL,
                      fecha_inicio varchar(50) NULL,
                      fecha_fin varchar(50) NULL,
                      maximo_regalo varchar(20) NULL,
                      U_observacion varchar(255) NULL,
                      tipo varchar(30) NULL,
                      cantidad_compra varchar(50) NULL,
                      unindad_compra varchar(50) NULL,
                      cantidad_regalo varchar(50) NULL,
                      unindad_regalo varchar(50) NULL,
                      cabezera_tipo varchar(50) NULL,
                      grupo_cliente varchar(200) NULL,
                      extra_descuento varchar(50) NULL,
                      opcional varchar(50) NULL,
                      territorio varchar(255) NULL,
                      idTerritorio varchar(50) NULL,
                      cantidad_maxima_compra varchar(50) NULL,
                      monto_total varchar(50) NULL,
                      id_cabezera_tipo varchar(50) NULL,
                      id_regla_bonificacion varchar(50) NULL,
                      TerritoryID varchar(50) NULL,
                      idUser varchar(50) NULL,
                      Description varchar(255) NULL,
                      id_bonificacion_cabezera  varchar(50) NULL,
                      porcentaje varchar(50) NULL,
                      codigo_canal varchar(50) NULL,
                      fijo varchar(50) NULL
                      );
  			 -*-
                    CREATE TABLE IF NOT EXISTS facturasPagos(
                      id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                      clienteId varchar(255) NULL,
                      cod varchar(100) NULL,
                      coddoc varchar(100) NULL,
                      docentry varchar(100) NULL,
                      docnum varchar(100) NULL,
                      pagarx decimal(15,6) NULL,
                         recibo varchar(255) NULL,
                      CardName varchar(100) NULL,
                      saldo decimal(15,6) NULL,
                      nroFactura varchar(100) NULL,
                      DocTotal decimal(15,6) NULL,
                      cuota integer NULL


                    );
         -*-
                    CREATE TABLE IF NOT EXISTS xmf_cabezera_pagos(
                      id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                      nro_recibo VARCHAR(20) NOT NULL,
                      correlativo integer NULL,
                      usuario integer NULL,
                      documentoId VARCHAR(20) NOT NULL,
                      fecha DATE NOT NULL,
                      hora DATE NOT NULL,
                      monto_total INTEGER NOT NULL,
                      tipo VARCHAR(20) NOT NULL,
                      otpp integer DEFAULT 0,
                      tipo_cambio integer DEFAULT 0,
                      moneda VARCHAR(20) NULL,
                      cliente_carcode VARCHAR(20) NOT NULL,
                      razon_social VARCHAR(100) NOT NULL,
                      nit VARCHAR(20) NOT NULL,
                      estado integer NULL,
                      equipo VARCHAR(20) NOT NULL,
                      latitud VARCHAR(20) NOT NULL,
                      longitud VARCHAR(20) NOT NULL,
                      cancelado integer NULL
                  
                    );
          -*-
                CREATE TABLE IF NOT EXISTS xmf_medios_pagos(
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  nro_recibo VARCHAR(20) NOT NULL,
                  documentoId varchar(100) NULL,
                  formaPago VARCHAR(20) NOT NULL,
                  monto integer NULL,
                  numCheque VARCHAR(200) NULL DEFAULT NULL,
                  numComprobante VARCHAR(200) NULL DEFAULT NULL,
                  numTarjeta VARCHAR(200) NULL,
                  bancoCode VARCHAR(200) NULL,
                  fecha VARCHAR(20) NULL,
                  cambio integer NULL,
                  monedaDolar integer NULL,
                  monedaLocal integer NULL,
                  centro varchar(50) NULL,
                  baucher varchar(80) NULL,
                  checkdate varchar(20) NULL,
                  transferencedate varchar(20) NULL,
                  CreditCard integer NULL,
                  idcabecera integer,
                  NumeroTarjeta varchar(50) NULL,
                  NumeroID varchar(50) NULL,
                  emitidoPor varchar(50) NULL,
                  tipoCheque varchar(50) NULL,
                  dateEmision varchar(50) NULL
                );

          -*-
                    CREATE TABLE IF NOT EXISTS xmf_facturas_pagos(
                      id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                      clienteId varchar(255) NULL,
                      nro_recibo VARCHAR(20) NOT NULL,
                      documentoId varchar(100) NULL,
                      docentry varchar(100) NULL,
                      monto decimal(15,6) NULL,
                      CardName varchar(100) NULL,
                      saldo decimal(15,6) NULL,
                      nroFactura varchar(100) NULL,
                      DocTotal decimal(15,6) NULL,
                      cuota integer NULL,
                      idcabecera integer,
                      vendedor varchar(100) NULL
                    );

          -*-
                    CREATE TABLE IF NOT EXISTS nit(
                      id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                      nit varchar(100) NULL,
                      razon_social VARCHAR(100) NOT NULL
                    );
              -*-
              CREATE TABLE IF NOT EXISTS unidadmedidaitems(
                id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                IdUnidadMedida integer NULL,
                Name  varchar(15) NULL,
                NameUnidad  varchar(30) NULL,
                ItemCode varchar(100) NULL
              );
              -*-
                CREATE TABLE IF NOT EXISTS gestionbancos (
                  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                  codigo varchar(50) NULL,
                  nombre varchar(255) NULL
                );
              -*-
              CREATE TABLE IF NOT EXISTS tarjetas(
                id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                CreditCard integer not null,
                CardName varchar(255) not null,
                AcctCode varchar(255) NULL,
                Phone varchar(255) NULL,
                CompanyId integer not null,
                Locked varchar(10) NULL,
                DataSource varchar(10) NULL,
                UserSign integer not null,
                LogInstanc integer not null,
                UpdateDate varchar(255) NULL,
                IntTaxCode varchar(255) NULL,
                UserSign2 integer not null,
                Country varchar(255) NOT NULL
            );
            -*-
            CREATE TABLE IF NOT EXISTS facturasMultiplesPagosAux(
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              cod varchar(100) NOT NULL,
              coddoc varchar(100) NOT NULL,
              monto integer NULL
            );
            -*-
            CREATE TABLE IF NOT EXISTS gruposPercepciones(
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              Code varchar(100) NOT NULL,
              Name varchar(100) NOT NULL,
              U_EXX_PORPER decimal NOT NULL,
              U_EXX_MONMIN decimal NOT NULL,
              U_EXX_TaxCode varchar(100) NOT NULL,
              U_EXX_GLP varchar(100) NOT NULL
            );
            -*-
            CREATE TABLE IF NOT EXISTS clientesPercepciones(
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              CardCode varchar(100) NOT NULL,
              CardType varchar(255) NOT NULL,
              U_EXX_TIPOPERS varchar(50)  NULL,
              QryGroup2 varchar(50) NULL,
              QryGroup3 varchar(50) NULL,
              QryGroup4 varchar(50) NULL,
              QryGroup6 varchar(50)  NULL,
              QryGroup7 varchar(50)  NULL,
              QryGroup8 varchar(50)  NULL,
              U_EXX_PERCOM varchar(50)  NULL,
              LicTradNum varchar(50) NULL,
              QryGroup1 varchar(255) NULL,
              iU_EXX_PERCDI varchar(50) NULL
            );
            -*-
            CREATE TABLE IF NOT EXISTS gestionsap(
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              CompnyName varchar(100) NOT NULL,
              CompnyAddr varchar(255) NULL,
              Country varchar(50)  NULL,
              TaxIdNum varchar(50) NULL,
              MainCurncy varchar(50) NULL,  
              SumDec int(11) NULL
            );
            -*-
            CREATE TABLE IF NOT EXISTS almacenesPercepciones(
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              WhsCode varchar(100) NOT NULL,
              WhsName varchar(255) NULL,
              U_EXX_PERDGH varchar(100)  NULL
            );
            -*-
            CREATE TABLE IF NOT EXISTS servicioVentas(
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              Code varchar(100) NOT NULL,
              Name varchar(200) NULL,
              U_EXX_GRUPER varchar(50)  NULL
            );
            -*-
            CREATE TABLE IF NOT EXISTS indicadoresimpuestos(
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              Code varchar(100) NOT NULL,
              Rate decimal(10,2) NULL,
              RowNumber INTEGER   NULL,
              STCCode varchar(10)  NULL,
              STACode varchar(10)  NULL,
              EffectiveRate decimal(10,2)  NULL,
              User INTEGER null,
              Status varchar (1)  NULL,
              DateUpdate varchar(30)  NULL             
            );
            -*-
            CREATE TABLE IF NOT EXISTS configuracionImpuestos(
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              STCCode varchar(100) NOT NULL,
              Line_ID INTEGER NULL,
              STACode varchar(10) NULL,
              STAType INTEGER NULL,                  
              EfctivRate decimal(10,2) NULL                  
            );
            -*-
            CREATE TABLE IF NOT EXISTS perTipoOperacion(
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              Code varchar(100)  NULL,
              Name varchar(255)  NULL
            );
            -*-
            CREATE TABLE IF NOT EXISTS perFeTpvu(
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              Code varchar(100)  NULL,
              Name varchar(255)  NULL
            );
            -*-
            CREATE TABLE IF NOT EXISTS perFeTaigv(
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              Code varchar(100)  NULL,
              Name varchar(255)  NULL
            );
            -*-
            CREATE TABLE IF NOT EXISTS perTransportistas(
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              CardCode varchar(255)  NULL,
              CardName varchar(255)  NULL,
              LicTradNum varchar(255)  NULL,
              Address varchar(255)  NULL,
              Name varchar(255)  NULL,
              Notes1 varchar(255)  NULL,
              U_EXX_PLAVEH varchar(255)  NULL,
              U_EXX_MARVEH varchar(255)  NULL,
              U_EXX_PLATOL varchar(255)  NULL
            );
            -*-
            CREATE TABLE IF NOT EXISTS perConfigEntrega(
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              U_EXX_CODTRANS varchar(255)  NULL,
              U_EXX_NOMTRANS varchar(255)  NULL,
              U_EXX_RUCTRANS varchar(255)  NULL,
              U_EXX_DIRTRANS varchar(255)  NULL,
              U_EXX_NOMCONDU varchar(255)  NULL,
              U_EXX_LICCONDU varchar(255)  NULL,
              U_EXX_PLACAVEH varchar(255)  NULL,
              U_EXX_MARCAVEH varchar(255)  NULL,
              U_EXX_PLACATOL varchar(255)  NULL,
              U_EXX_FE_MODTRA varchar(255)  NULL
            );
            -*-
            CREATE TABLE IF NOT EXISTS States(
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              Code varchar(255) NULL,
              Country varchar(255) NULL,
              Name varchar(255) NULL
              
              ); 
            -*-
            CREATE TABLE IF NOT EXISTS cliprovincias(
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              Code varchar(50)  NULL,
              Name varchar(150)  NULL,
              U_EXX_CODPAI varchar(150)  NULL,
              U_EXX_CODDEP varchar(150)  NULL
            );
            -*-
            CREATE TABLE IF NOT EXISTS clidistritos(
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              Code varchar(50)  NULL,
              Name varchar(150)  NULL,
              U_EXX_CODPRO varchar(150)  NULL,
              U_EXX_DESDIS varchar(150)  NULL
            );
            -*-
            CREATE TABLE IF NOT EXISTS pagoscuotas(
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              documentoId varchar(50)  NULL,
              pagoId varchar(50)  NULL,
              date varchar(20)  NULL,
              amount varchar(60)  NULL,
              porcentage varchar(150)  NULL,
              state varchar(1)  NULL
            );
            `;

  public async create() {
    let tbls: any = this.tables.split('-*-');
    for await (let tbl of tbls) {
      try {
        await this.executeSQL(tbl);
      } catch (e) {
        console.error("ERROR SQL", e);
        console.error(tbl);
      }
    }
  }

  public async alter(table,columna) {

    console.log("ACTUALIZA TABLA RAFAEL");

    let sql ="SELECT COUNT(*) AS cantidad FROM pragma_table_info('"+table+"') WHERE name='"+columna+"'";
    let resp: any = await this.queryAll(sql);

    if(resp[0].cantidad == 0){
      let sql = "ALTER TABLE "+table+" ADD COLUMN "+columna+" varchar(50)";
      console.log(sql);
      try {
        await this.executeSQL(sql);
      } catch (e) {
        console.error("ERROR SQL", e);
        console.error(sql);
      }
    }else{
      console.log("ya existe la columna "+columna+" en la tabla "+table);
    }

    let sql1 ="SELECT name FROM pragma_table_info('"+table+"')";
    let resp1: any = await this.queryAll(sql1);
    console.log(resp1);

  }
}
