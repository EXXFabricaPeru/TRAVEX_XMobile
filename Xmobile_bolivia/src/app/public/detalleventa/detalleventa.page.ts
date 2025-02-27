import { Component, OnInit, ɵCodegenComponentFactoryResolver, Renderer2, ChangeDetectorRef } from '@angular/core';
import { AlertController, ModalController, NavController, NavParams, Platform } from "@ionic/angular";
import { WheelSelector } from '@ionic-native/wheel-selector/ngx';
import { ConfigService } from "../../models/config.service";
import { Productosprecios } from "../../models/productosprecios";
import { Clientes } from "../../models/clientes";
import { NativeService } from "../../services/native.service"
import { Paraguay } from "../../utilsx/paraguay";
import { Network } from "@ionic-native/network/ngx";
import { Bolivia } from "../../utilsx/bolivia";
import { Descuentos } from "../../models/descuentos";
import { Companex } from "../../utilsx/companex";
import { Chile } from "../../utilsx/chile";
import { Productosalmacenes } from "../../models/productosalmacenes";
import { ModalseriesPage } from "../modalseries/modalseries.page";
import { Seriesproductos } from "../../models/seriesproductos";
import { Lotesproductos } from "../../models/lotesproductos";
import { Combos } from "../../models/combos";
import { Productos } from "../../models/productos";
import { Calculo } from "../../utilsx/calculo";
import { Lotes } from "../../models/lotes";
import { Detalle } from "../../models/detalle";
import { Dialogs } from "@ionic-native/dialogs/ngx";
import 'lodash';
import { log } from "util";
import { bonificacion_regalos } from '../../models/bonificacion_regalos';
import { bonificacion_compras } from '../../models/bonificacion_compras';
import { Bonificaciones as Bonificacion_ca } from "../../models/V2/bonificaciones";
import { Documentos } from '../../models/documentos';
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { UtilsService } from '../../services/ utils.service';
import { DataService } from '../../services/data.service';
import { GlobalConstants } from "../../../global";

//Percepciones
import { peruSinPerc } from "../../utilsx/peruSinPerc";
import { Calculopercepciones } from 'src/app/utilsx/calculopercepciones';
import { Grupospercepciones } from './../../models/grupospercepciones';
import { Indicadoresimpuestos } from 'src/app/models/indicadoresimpuestos';
import { Configuracionimpuestos } from 'src/app/models/configuracionimpuestos';
import { Clientessucursales } from 'src/app/models/clientessucursales';
import { PerFexAfectacionIgv } from 'src/app/models/perfexafectacionigv';
import { PerTipoPrecioVenta } from 'src/app/models/perpreciotipoventa';
import { Peru } from "../../utilsx/peru";
import { Toast } from '@ionic-native/toast/ngx';

declare var _: any;

@Component({
    selector: 'app-detalleventa',
    templateUrl: './detalleventa.page.html',
    styleUrls: ['./detalleventa.page.scss'],
})
export class DetalleventaPage implements OnInit {
    public idfrom = 12;
    public bonificacion_regalos = new bonificacion_regalos();
    public bonificacion_compras = new bonificacion_compras();
    public Bonificacion_ca = new Bonificacion_ca();
    public data: any;
    public modalcerrado: number;
    public tipollenado: any;
    public cantidad: any;
    public cantidadUI: number;
    public precio: number;
    public price: number;
    public precioNetoInicial: number;
    public total: number;
    public totalNeto: number;
    public localizacion: any;
    public currencyDefault: any;
    public productosprecios: any;
    public descuentoporsentual: number;
    public descuentomonetario: number;
    public descuentoNeto: number;
    public descuentoadicional: number;
    public conprometidoheader: any;
    public indicador_impuesto: string;
    public disponibleheader: any;
    public seriesSlide: any;
    public slideopt: any;
    public listaLotes: any;
    public listaLotesAux: any;
    public listaLotesarr: any;
    public listaLotesarr2: any;
    public dataLotes: Lotesproductos;
    public nombreunidad: string;
    public nombreProduct: string;
    public bonificacion: boolean;
    public estado: boolean;
    public estadodes: boolean;
    public descuentocombo: number;
    public indexUnidad: number;
    public auxTLoc: number;
    public tice: string;
    public producto_std2: any;
    public producto_std3: any;
    public producto_std4: any;
    public ice: any;
    public icee: any;
    public icep: any;
    public cantidaduni: any;
    public unidaddemedida: any;
    public userdata: any;
    public descuentolinea: any = 1;
    public unids: any;
    public bonificacionx: boolean;
    public cantidadproductos: number;
    public segmentModel: any;
    public sindescuento: boolean;
    public condescuento: boolean;
    public estadostocks: any;
    public boniestado: boolean;
    public descuentosEspeciales: boolean;
    public usaIce: boolean;
    disponibleStock = 0;
    eventoClick = null;

    //modelos para uso de percepciones
    public grupoPersencionesModel = new Grupospercepciones();
    public indicadoresImpuestosModel = new Indicadoresimpuestos();
    public configuracionImpuestosModel = new Configuracionimpuestos();
    public clientesSucursalesModel = new Clientessucursales();
    public CalculoPercepcionesUtils = new Calculopercepciones();
    public tipoPreciosVentaModel = new PerTipoPrecioVenta();
    public fexAfectacionIgvModel = new PerFexAfectacionIgv();

    //data para percepciones
    public manejaPercepcion: boolean = false;
    public manejaPrecioBruto: boolean = false;
    public dataPercepcion: any;
    public dataSelect = [{ code: "Y", name: "SI" }, { code: "N", name: "NO" }];
    public grpPerManual: boolean;
    public perDispCombustible: boolean;
    public cPersepciones = new Calculopercepciones()
    public impuestoVal: string = "";
    public taxOnly: boolean;
    public VatSum: Number;
    public TotalFrgn: Number;
    public VatSumFrgn: Number;
    public dataIndicadoresImp: any = [];
    public dataTipoPrecioVentas: any = [];
    public selecTipoPrecioVenta: any = [];
    public dataFexAfectacionIgv: any = [];
    public selecFexAfectacionIgv: any = [];
    public contenedorcampos: string;
    public maxdescuentolinea: number;
    public btnSaveDisabled:Boolean=false;
    public auxPriceOriginal: number;
    public VatPrcnt: number;
    public dataGrupoPer: any = [];
    descuentoporsentualNoBonificacion: number;
    descuentomonetarioNoBonificacion: number;
    dataConfig: any = [];
    oneChangeCant: boolean = false;
    public colapse: boolean = false;
    public preciocomp: number;
    public precioOriginal: number;
    public estadounidad: boolean;
    public validaprecio: any = 0;
    public iscombo: boolean;
    public itemcombo: any;
    public itemcombolotes: any;
    public auxprecio: number;
    public unidad: any;
    public auxprecioBruto: number;

    constructor(private selector: WheelSelector, private native: NativeService, private dataService: DataService, private spinnerDialog: SpinnerDialog, public navParams: NavParams, public modalController: ModalController, private platform: Platform,
        private configService: ConfigService, public alertController: AlertController, private dialogs: Dialogs, private navCrl: NavController,private network: Network,
        private utilisService: UtilsService,private renderer: Renderer2
    ) {
        this.data = navParams.data;
        this.cantidadproductos = 100000000000;
        this.dataLotes = new Lotesproductos();
        this.tipollenado = false;
        this.currencyDefault = this.data.dataexport.moneda;
        this.precio = 1;
        this.price = 1;
        this.precioNetoInicial = 0;
        this.cantidad = 1;
        this.cantidadUI = 1;
        this.descuentomonetario = 0;
        this.descuentoporsentual = 0;
        this.descuentoadicional = 0;
        this.bonificacion = false;
        this.estado = this.data.estado;
        this.estadodes = this.data.estado; 
        this.seriesSlide = [];
        this.listaLotes = [];
        this.listaLotesAux = [];
        this.listaLotesarr = [];
        this.listaLotesarr2 = [];
        this.productosprecios = [];
        this.producto_std2 = "";
        this.producto_std2 = 0;
        this.producto_std2 = 0;
        this.ice = "";
        this.icee = 0;
        this.icep = 0;
        this.auxTLoc = 0;
        this.tice = '';
        this.cantidaduni = 1;
        this.unids = 1;
        this.indicador_impuesto = 'ICE';
        this.modalcerrado = 0;
        this.bonificacionx = true;
        this.segmentModel = true;
        this.sindescuento = true;
        this.condescuento = false;
        this.slideopt = {
            slidesPerView: 2.5,
            freeMode: true
        };
        if (this.data.dataexport.cliente.tipoestado == "cerrado" || this.data.dataexport.cliente.tipoestado == "anulado") {
            this.estadostocks = 0;
        } else {
            this.estadostocks = 1;
        }
        this.boniestado = (this.data.bonificacion == '1');
        this.usaIce = true;
        
        //Percepciones
        this.preciocomp = 1;
        this.precioOriginal = 1;
        this.cantidadproductos = 100000000;
        this.iscombo = false;
        this.itemcombo = [];
        this.itemcombolotes = [];
        this.auxprecio = 0;
        this.unidad= [];
        this.validaprecio = 0;
    }

    public confInit() {
        if (this.userdata[0].perManejaPercepcion == 1) {
            this.manejaPercepcion = true;
        } else {
            this.manejaPercepcion = false;
        }
        if (this.userdata[0].perManejaPrecioBruto == 1) {
            this.manejaPrecioBruto = true;
        } else {
            this.manejaPrecioBruto = false;
        }
    }

    public async ngOnInit() {

        console.log("DATOS INICIALES",this.data);
        this.tice = this.data.dataexport.listaPrecio.IsGrossPrice;


        if (localStorage.getItem('mofificarBonificacion') != "SI") {
            localStorage.setItem('mofificarBonificacion', 'NO');
        }

        console.log("CONSULTA DATOS DE SESION 175");
        this.userdata = await this.configService.getSession();

        if(this.userdata[0].indicador_impuesto){
            this.indicador_impuesto = this.userdata[0].indicador_impuesto;
        }else{
            this.indicador_impuesto = 'ICE';
        }

        this.descuentolinea = 1;

        this.confInit();

        try {
            
            if (this.data.dataexport.cliente.DocType == 'DOE' || (this.data.dataexport.tipoDocx == 0 && this.data.dataexport.cliente.DocType == 'DFA')) {

                let productos = new Productos();
                console.log("HACE CONSULTA A PRODUCTOS 190");
                let rxx: any = await productos.select(this.data.ItemCode);
                this.data.ManageBatchNumbers = rxx.ManageBatchNumbers;
                this.data.ManageSerialNumbers = rxx.ManageSerialNumbers;

            }

            //await this.controlDescuento();
            console.log("LLAMA CONTROL DE STOCK 198");
            let rx: any = await this.controlStock();

            if (typeof rx == 'undefined' || typeof rx == undefined) {

                this.conprometidoheader = 0;
                this.disponibleheader = 0;

            } else {
                console.log("LLAMA A CALCULO 207");
                this.conprometidoheader = Calculo.round(rx.Committed);
                let conut = Calculo.round(rx.Ordered);
                let fx = Calculo.round(rx.InStock);

                let sumx = (parseFloat(this.data.Quantity) * parseFloat(this.data.BaseId));

                if (this.data.edit == true && (this.data.dataexport.tipoDoc == 'DFA' && this.data.dataexport.tipoDocx == 0 || this.data.dataexport.cliente.DocType == 'DOE')) {  
                    this.disponibleheader = (fx + sumx);
                    this.disponibleStock = (fx - this.conprometidoheader)+conut;
                
                } else {
                    this.disponibleheader = fx
                    this.disponibleStock = (fx -this.conprometidoheader)+conut;
                }

            }

            console.log("CONSULTA DATOS DE SESION 225");
            let servi = await this.configService.getSession();
     
            this.dataConfig = servi;
            this.dataConfig[0].priceValidateMax = this.dataConfig[0].priceValidateMax ? this.dataConfig[0].priceValidateMax : "400"; // variable en porcentaje    
            this.dataConfig[0].priceValidateMin = this.dataConfig[0].priceValidateMin ? this.dataConfig[0].priceValidateMin : "100"; //variable en procentaje
            this.dataConfig[0].editPrice = this.dataConfig[0].editPrice ? this.dataConfig[0].editPrice : "0";

            if (this.dataConfig[0].descuentosEspeciales) {
                if (this.dataConfig[0].descuentosEspeciales == 1) {
                    this.descuentosEspeciales = true;
                }
            }

            switch (parseInt(servi[0].localizacion)) {
                case (1):
                    this.localizacion = new Bolivia();
                    this.auxTLoc = 1;
                    break;
                case (2):
                    this.localizacion = new Companex();
                    this.auxTLoc = 2;
                    break;
                case (3):
                    this.localizacion = new Paraguay();
                    this.auxTLoc = 3;
                    break;
                case (4):
                    this.localizacion = new Chile();
                    this.auxTLoc = 4;
                    break;
                case (5):
                    this.localizacion = new Peru();
                    this.auxTLoc = 5;
                    break;
                case (6):
                    this.localizacion = new peruSinPerc();
                    this.auxTLoc = 6;
                    break;
            }

            this.inicializarPercepciones();

            let productosprecios = new Productosprecios();
            console.log("CONSULTA PRODUCTOS PRECIOS 229");
            let rxx: any = await productosprecios.selectPrecios(this.data.ItemCode);
            
            let moneda_cliente = this.data.dataexport.cliente.Currency;

            if(moneda_cliente == '##'){
                moneda_cliente = this.data.dataexport.moneda;
            }

            let tiene_precio = 0;
            for (let i = 0; i < rxx.length; i++) {
                console.log("Lista de precio ------->", rxx[i].PriceListNo , this.data.dataexport.listaPrecio.PriceListNo);

                if (rxx[i].IdListaPrecios == this.data.dataexport.listaPrecio.PriceListNo) {
                    tiene_precio = 1;
                    if (rxx[i].Currency != moneda_cliente) {
                        console.log("paso");
                        this.native.mensaje('No se encontro precio para el tipo de moneda: ' + moneda_cliente + '|' + rxx[i].Currency, '3000', 'center');
                        this.cerrar(3);
                    }
                }
            }

            if(tiene_precio == 0){
                this.native.mensaje('No se encontró precio para el producto en la lista de precio seleccionada ', '3000', 'center');
                this.cerrar(1);
            }


            if (servi[0]) {
                if (servi[0].descuentosEspeciales == 1) {
                    this.descuentosEspeciales = true;
                }
            }

            console.log(rxx);

            switch (parseInt(servi[0].localizacion)) {
                case (1):
                    this.localizacion = new Bolivia();
                    this.auxTLoc = 1;
                    break;
                case (2):
                    this.localizacion = new Companex();
                    this.auxTLoc = 2;
                    break;
                case (3):
                    this.localizacion = new Paraguay();
                    this.auxTLoc = 3;
                    break;
                case (4):
                    this.localizacion = new Chile();
                    this.auxTLoc = 4;
                    break;
            }

            console.log("LLAMA FUNCION VARINIT 276");

            this.varinit();
        } catch (e) {
            this.native.mensaje('No existe el producto en la galería de productos.');
        }
    }

    public async varinit() {

        console.log("INICIA varinit()");
        
        let productosprecios = new Productosprecios();
        let itemcode = this.data.ItemCode;

       
        console.log("this.data ", this.data);
        console.log("this.data.bonificacionx ", this.data.bonificacionx);

        if (this.data.bonificacionx == 1) {
            console.log("this.data.dataexport.databoni.unindad_regalo ", this.data.dataexport.databoni[0].unindad_regalo);
            this.productosprecios = this.productosprecios.filter((prod) => prod.Code === this.data.dataexport.databoni[0].unindad_regalo);
            this.bonificacion = true;
            this.bonificacionx = false;
            this.descuentoporsentual = 100;
            this.estadodes = true; 
            this.tipollenado = true;
            this.cantidad = this.cantidadUI;

            this.data.unidadid = this.data.dataexport.databoni[0].unindad_regalo;
            if (this.data.dataexport.databoni[0].cantidad_regalo) {
                this.cantidadproductos = parseInt(localStorage.getItem('cantidadproductoslimitBONI'));
            } else {
                if (this.data.Quantity) {
                    this.cantidadproductos = parseFloat(this.data.Quantity);
                } else {
                    this.cantidadproductos = 0;
                }
            }
        }

        if (this.data.bonificacionx > 0 && this.data.edit == true) {
            this.estado = true;
            this.estadodes = true; 
        }

 
        if(this.data.unidadid == undefined){

            let productosprecios = new Productosprecios();
            console.log("CONSULTA PRODUCTOS PRECIOS 326");
            let rxx: any = await productosprecios.selectPrecios(this.data.ItemCode);
            // console.log(rxx);            
            if(rxx.length>0){
                this.data.unidadid = rxx[0].IdUnidadMedida;
            }else{
                if(this.modalcerrado == 0){
                    this.native.mensaje('No se Encontro Unidad de Medida para esta lista de precio');
                    this.cerrar(1);
                }
            }
        }  

        console.log("CONSULTA UNIDADES DE MEDIDAS 338");
        this.unidaddemedida = await productosprecios.selectunidadmedida(itemcode, this.data.dataexport.listaPrecio.PriceListNo,this.data.unidadid);
        console.log("unidad medida--->",this.unidaddemedida);

        if(this.unidaddemedida.length == 0){

            console.log("CONSULTA PRECIOS 344");
            let rxx: any = await productosprecios.selectPrecios(this.data.ItemCode);
            console.table("Productos precio",rxx);
            this.data.unidadid = rxx[0].IdUnidadMedida;
            console.log("CONSULTA UNIDADES DE MEDIDAS 348");
            this.unidaddemedida  = await productosprecios.selectunidadmedida(itemcode, this.data.dataexport.listaPrecio.PriceListNo,this.data.unidadid);
            
        }

        this.productosprecios = [{
            "BaseQty": "0",
            "Code": this.unidaddemedida[0].Code,
            "Currency": "SOL",
            "DateUpdate": "2021-05-18 00:00:00",
            "IdListaPrecios": "0",
            "IdUnidadMedida": this.unidaddemedida[0].IdUnidadMedida,
            "ItemCode": this.data.ItemCode,
            "Name": this.unidaddemedida[0].Name,
            "Price": this.data.Price,
            "PriceListName": "IMPORTADO",
            "PriceListNo": "1",
            "Status": "1",
            "User": "1",
            "id": 1,
            "idUser": "1",
        }];

        console.log("VALIDA SI ES UN PRODUCTO A EDITAR 372");
        if (this.data.edit == true) {
            console.log("ES UN PRODUCTO A EDITAR 374");
            let index = _.findIndex(this.productosprecios, { 'Code': this.data.unidadid });

            if (this.data.dataexport.origen == 'outer' || this.data.dataexport.cliente.clone.length > 3) {
                console.log("DEVD es importado o clonado");
                this.estado = true;
                this.estadodes = true; 
                this.cantidaduni = parseFloat(this.data.Quantity);
        
                this.productosprecios = [{
                    "BaseQty": "0",
                    "Code": this.unidaddemedida[0].Code,
                    "Currency": "BS",
                    "DateUpdate": "2021-05-18 00:00:00",
                    "IdListaPrecios": "0",
                    "IdUnidadMedida": this.unidaddemedida[0].IdUnidadMedida,
                    "ItemCode": this.data.ItemCode,
                    "Name": this.unidaddemedida[0].Name,
                    "Price": this.data.Price,
                    "PriceListName": "IMPORTADO",
                    "PriceListNo": "1",
                    "Status": "1",
                    "User": "1",
                    "id": 1,
                    "idUser": "1",
                }];
                index = 0;
            } else {
                let PriceListNo = this.data.dataexport.listaPrecio.PriceListNo;
                console.log("CONSULTA PRODUCTOS PRECIOS 403");
                this.productosprecios = await productosprecios.selectPreciosproducto(itemcode, PriceListNo);
                index = _.findIndex(this.productosprecios, { 'Code': this.data.unidadid });

            }

            if (this.data.XMPORCENTAJECABEZERA > 0) {

                this.estado = true;
                this.estadodes = true; 
            }
            let documentosdata = new Documentos();

            console.log("CONSULTA PAGOS LOCALES 416",this.productosprecios);
            let doc: any = await documentosdata.controlPagoslocal(GlobalConstants.CabeceraDoc, GlobalConstants.DetalleDoc);

            if (Number(doc.descuento) > 0) {
                this.estado = true;
                this.estadodes = true; 
            }

            this.cantidadUI = parseFloat(this.data.Quantity);
            this.nombreProduct = this.data.Dscription;
            this.descuentomonetario = 0;
            this.descuentoporsentual = this.data.XMPORCENTAJE; 
            if (this.data.bonificacion == 2) {

                this.descuentoporsentual = this.data.DiscTotalPrcnt; 
            }
            this.precio = this.data.Price;
            this.cantidad = parseFloat(this.data.Quantity);

            this.producto_std2 = this.data.ICEt;
            this.producto_std4 = this.data.icete;
            this.producto_std3 = this.data.icetp;
          
            if (this.descuentoporsentual > 0) this.tipollenado = true;
     
            this.segmentModel = !this.tipollenado;

            console.log("CONSULTA LOTES 443");
            this.listaLotesarr = await this.dataLotes.select2(this.data.ItemCode, this.data.dataexport.almacen.WarehouseCode);
            this.listaLotesarr2 = await this.dataLotes.select(this.data.ItemCode, this.data.dataexport.almacen.WarehouseCode);
            let arrlotex = [];


            if(this.data.lote != undefined){

                for await (let lotex of this.data.lotes) {
                    arrlotex.push({
                        lote: lotex.loteName,
                        cant: lotex.num,
                        label: lotex.num,
                    });
                }
            }

            this.seriesSlide = [];
            let seriesproducto = new Seriesproductos();
            console.log("CONSULTA SERIES 462");
            let arrseries: any = await seriesproducto.selectserie(this.data.id);
            for (let seriex of arrseries) this.seriesSlide.push(seriex.SerialNumber);


            if (this.auxTLoc == 3) {
                let datoscliente = new Clientes();
                console.log("CONSULTA CLIENTE 469");
                let cliente: any = await datoscliente.find(this.data.dataexport.cliente.CardCode);
                this.data.dataexport.cliente.cliente_std1 = cliente[0].cliente_std1;
                this.data.dataexport.cliente.cliente_std2 = cliente[0].cliente_std2;
                this.data.dataexport.cliente.cliente_std3 = cliente[0].cliente_std3;
            }
            if (this.auxTLoc == 5) {
                this.grpPerManual = this.data.U_EXX_GRUPERMAN == 'Y' ? true : false;
                this.perDispCombustible = this.data.U_EXX_PERDGHDCM == 'Y' ? true : false;
                this.taxOnly = this.data.TaxOnly == 'Y' ? true : false;
                this.VatSum = this.data.VatSum;
                this.TotalFrgn = this.data.TotalFrgn;
                this.VatSumFrgn = this.data.VatSumFrgn;

                console.log("recalculoPrecios index ", index);
                // this.recalculoPrecios(index);
            }
            if (this.auxTLoc == 6) {
                console.log("this.auxTLoc is ", this.auxTLoc);
                let datoscliente = new Clientes();
                let cliente: any = await datoscliente.find(this.data.dataexport.cliente.CardCode);
                console.log("client ", cliente);
                this.data.dataexport.cliente.cliente_std6 = cliente[0].cliente_std6;
                this.data.dataexport.cliente.cliente_std7 = cliente[0].cliente_std7;
                this.data.dataexport.cliente.cliente_std8 = cliente[0].cliente_std8;
            }

            if (this.descuentosEspeciales) {
                if (this.descuentoporsentual <= 0) {
                    console.log("CONSULTA DESCUENTAS SAP 479");
                    this.descuentoporsentual = await this.utilisService.DescuentosSap(this.data.dataexport.cliente.CardCode, this.data.ItemCode, this.data.PriceListNo, this.cantidadUI, 0, 0, "", 0);;
                    if (this.descuentoporsentual > 0) {
                        this.native.mensaje('Descuento porcentual asignado : ' + this.descuentoporsentual);
                        this.descuentomonetario = 0;
                        this.data.U_4DESCUENTO = 0;
                    }
                }
            }
            console.log("CONSULTA FUNCION listarlotesedit 488");
            this.listarlotesedit(arrlotex);
            console.log("CONSULTA FUNCION recalculoPrecios 490");
            this.recalculoPrecios(index);
            console.log("CONSULTA FUNCION calTotal 492");
            this.calTotal();
        } else {
            console.log("NO ES UN PRODUCTO A EDITAR 495");
            let PriceListNo = this.data.dataexport.listaPrecio.PriceListNo;
            console.log("Lista Precio",PriceListNo);            
            console.log("CONSULTA PRODUCTOS PRECIOS 498");
            // debugger;
            this.productosprecios = await productosprecios.selectPreciosproducto(itemcode, PriceListNo);
            console.log("CONSULTA PRODUCTOS PRECIOS 498",this.productosprecios);
            if(this.productosprecios.length == 0){
                this.native.mensaje('No existe el producto para la lista de precio Seleccionada.');
                this.cerrar(3);
            }else{
      
                this.tipollenado = false;
                if (this.descuentosEspeciales) {
                    console.log("CONSULTA DESCUENTOS SAP 508");
                    this.descuentoporsentual = await this.utilisService.DescuentosSap(this.data.dataexport.cliente.CardCode, this.data.ItemCode, this.data.dataexport.listaPrecio.PriceListNo, this.cantidadUI, 0, 0, "", 0);;
                    if (this.descuentoporsentual > 0) {
                        this.native.mensaje('Descuento porcentual asignado : ' + this.descuentoporsentual);
                        this.segmentModel = false;
                        this.cambiartipollenado(true);
                        this.estadodes = true;
                    }
                }

                this.data.BaseId = this.data.dataexport.listaPrecio.PriceListNo;
                this.nombreProduct = this.data.ItemCode + ' ' + this.data.ItemName;

                console.log("CONSULTA LOTES 518");
                this.listaLotesarr = await this.dataLotes.select2(this.data.ItemCode, this.data.dataexport.almacen.WarehouseCode);

                this.producto_std3 = this.data.producto_std3;
                if (this.data.bonificacionx == 1) {
                    let index: any;
                    let ban = 0;

                    for await (let unidades of this.productosprecios){

                        if(this.data.SalesUnit == unidades.Code){
                            ban = 1;
                            index = _.findIndex(this.productosprecios, { 'Code': unidades.Code });
                        }
                    }

                    if(ban == 0){
                        index = _.findIndex(this.productosprecios, { 'Code': "UNI" });
                    }
                    console.log("LLAMA A LA FUNCION recalculoPrecios 537");
                    this.recalculoPrecios(index);
                } else {
                    
                    let index: any;
                    let ban = 0;
                    for await (let unidades of this.productosprecios){
                        
                        if(this.data.SalesUnit == unidades.Code){
                           
                            ban = 1;
                            index = _.findIndex(this.productosprecios, { 'Code': unidades.Code });
                        }
                    }
                    if(ban == 0){
                        //this.native.mensaje('No existe precio para la unidad de medida de Venta.');
                        index = 0;
                    }
                    console.log("LLAMA A LA FUNCION recalculoPrecios 555");
                    this.recalculoPrecios(index);
                }
            }
        }
    }

    private async recalculoPrecios(index: number) {

        console.log("INICIA recalculoPrecios 464");

        let items = [];

        let arregloConRepetidos = this.productosprecios;
        items.push(this.productosprecios[0]);
        for (let itemAux of this.productosprecios) {
            let versiHay = items.filter((n) => {
                return n.Code == itemAux.Code;
            });
            if (versiHay.length == 0) {
                items.push(itemAux);
            }
        }
      
        this.productosprecios = items;

        if (index < 0) {
            index = 0;
        }

        this.indexUnidad = index;
        
        console.log("VALIDA PRODUCTOSPRECIOS 587");
        
        if (this.productosprecios.length > 0) {
            if(this.productosprecios[index]){
                try {
                    if (this.data.combo != 1) {
                        console.log("CONSULTA DESCUENTOS ADICIONALES 593");
                        this.descuentoadicional = await this.localizacion.descuentosadicionales(this.data);
                        this.nombreunidad = this.productosprecios[index].Name;
                        this.precioNetoInicial = this.productosprecios[index].Price;
                        this.cantidaduni = this.productosprecios[index].BaseQty;
                        console.log("VALIDA REDONDEO CALCULO 598",this.precioNetoInicial);
                        this.precio = Calculo.round((this.precioNetoInicial - this.descuentoadicional),4);
                        console.log("VALIDA REDONDEO CALCULO 598");
                        this.precio = Calculo.round((this.precioNetoInicial - this.descuentoadicional), 4);
                        console.log("LLAMA A FUNCION calTotal 600");
                        this.calTotal();
                    } else {
                        let combx: any = new Combos();
                        console.log("CONSULTA LOS COMBOS 604");
                        let combos: any = await combx.findTreecode(this.data.ItemCode);
                        let sumacombo = 0;
                        let sumadescuentos = 0;
                        for (let combo of combos) {
                            sumacombo += combo.Price;
                            sumadescuentos += parseFloat(combo.Price2);
                        }
                        this.descuentoadicional = 0;
                        this.descuentocombo = sumadescuentos;
                        this.descuentomonetario = sumadescuentos;
                        this.nombreunidad = 'COMBO';
                        this.precioNetoInicial = sumacombo;
                        this.precio = (this.precioNetoInicial - this.descuentoadicional);
                        
                        this.productosprecios = [{
                            Price: this.precio,
                            Name: this.nombreunidad
                        }];
                        console.log("LLAMA A FUNCION calTotal 623 ");
                        this.calTotal();
                    }
                } catch (error) {
                    this.native.mensaje('Lista de precios no encontrado', '3000', 'center');
                    this.cerrar(2);
                }
            }
        } else {
            //   alert("x");
            this.native.mensaje('Lista de precios no encontrado', '3000', 'center');
            this.cerrar(2);
        }
    }

    public async controlStock() {
        let xData: any;
        let usuariodata: any = await this.configService.getSession();
        let dataext: any = {
            "usuario":usuariodata[0].idUsuario,
            "sucursal": this.data.dataexport.sucursal.id,
            "equipo":usuariodata[0].equipo,
            "pagina": 0,
            "texto": this.data.ItemCode,
            "almacen": this.data.dataexport.almacen.WarehouseCode
        };
        let productosalmacenes = new Productosalmacenes();

        if (this.network.type != 'none') {

            this.spinnerDialog.show('', 'Cargando...', true);
            try {
                xData = await this.dataService.servisReportPost("v2/productosalmacenes/buscador",dataext,3);
                let xJson = JSON.parse(xData.data);
                console.table(xJson.respuesta[0]);
                if(xJson.respuesta[0]){
                    await productosalmacenes.addUpdateprodcualmacenessap(xJson.respuesta[0]);
                }
                this.spinnerDialog.hide();

            } catch (error) {
                console.log(error);
                this.spinnerDialog.hide();
                this.native.mensaje('Sin conexión a SAP se cargaran datos locales', '3000', 'center');
            }

        }
        
        let prodAlma = await productosalmacenes.find(this.data.dataexport.almacen.WarehouseCode, this.data.ItemCode);

        if ((typeof prodAlma == 'undefined')) {
            localStorage.setItem("stockBoni", "0");
            this.modalcerrado = 1;
            this.native.mensaje('Stock no encontrado', '3000', 'center');
            console.log("RAFAEL CIERRA 2");
            this.cerrar(3);
        }else{
            return prodAlma;
        }
        

}

    public listarlotesedit(lotes: any) {
       
        this.listaLotesAux = lotes;
        this.listaLotes = [];
        for (let lotex of lotes) {
            this.listaLotes.push({
                loteName: lotex.lote,
                num: Number(lotex.label),
            });
        }
    }

    public listarlotes(lotes: any) {
        this.listaLotes = [];
        for (let lotex of lotes) {
            this.listaLotes.push({
                loteName: lotex.lote,
                num: Number(lotex.cant)
            });
        }
    }

    public async changecantidadlote(lotex: any, index: any) {
        let r = this.listaLotesarr.filter(lote => lote.BatchNum == lotex.loteName);
        let alert: any = await this.alertController.create({
            header: `LOTES`,
            mode: 'ios',
            inputs: [{
                name: 'cantidad',
                type: 'number',
                value: lotex.num
            }],
            buttons: [{ text: 'CANCELAR' }, {
                text: 'CAMBIAR',
                handler: (data: any) => {
                    if (Number(data.cantidad) > 0 && (Number(data.cantidad) <= Number(r[0].Quantity))) {
                        lotex.num = Number(data.cantidad);
                    } else {
                        this.native.mensaje('Lote insuficiente.', '3000', 'top');
                        return false;
                    }
                }
            }]
        });
        await alert.present();
    }

    public xrun(BatchNum: string) {
        let rxc = _.filter(this.listaLotes, { "loteName": BatchNum });
        if (rxc.length > 0) {
            return rxc[0].cant;
        } else {
            return 0;
        }
    }

    public async actionLotes() {
        let lotesarrx = [];
        if (this.listaLotes.length > 0) {
            lotesarrx = [];
            for (let lox of this.listaLotesarr) {
                let rsxpx: any = this.xrun(lox.BatchNum);
                lox.Quantity = (parseFloat(lox.Quantity) + rsxpx);
                lotesarrx.push(lox);
            }
        } else {
            lotesarrx = this.listaLotesarr;
        }
        let arrayLotes = [];
        for (let lote of lotesarrx) {
            arrayLotes.push({
                name: lote.BatchNum,
                type: 'checkbox',
                label: lote.BatchNum + ": Cant. " + lote.Quantity,
                value: lote.BatchNum
            })
        }
        const alert: any = await this.alertController.create({
            header: 'SELECCIONAR LOTE',
            inputs: arrayLotes,
            mode: 'ios',
            buttons: [
                {
                    text: 'CANCELAR',
                    role: 'cancel',
                }, {
                    text: 'CONTINUAR',
                    handler: (data: any) => {
                        let arrx: any = [];
                        for (let lotex of data)
                            arrx.push({
                                lote: lotex,
                                cant: 1
                            });
                        this.listarlotes(arrx);
                        this.estado = false;
                        this.estadodes = false; 

                    }
                }
            ]
        });
        await alert.present();
    }

    public async actionSeries() {
        console.log("abrir modal");
        
        let dataseries: any = {
            ItemCode: this.data.ItemCode,
            WhsCode: this.data.dataexport.almacen.WarehouseCode,
            cantidad: this.cantidad
        };
        let mcproducto: any = { component: ModalseriesPage, componentProps: dataseries };
        let modalproducto: any = await this.modalController.create(mcproducto);
        modalproducto.onDidDismiss().then(async (data: any) => {
            if (Array.isArray(data.data))
                this.seriesSlide = data.data;
            this.estado = false;
            this.estadodes = false;
            console.log("this.estado  ", this.estado);
        });
        return await modalproducto.present();
    }

    public selectUnidades() {
        console.log("this.data.bonificacionx  ", this.data.bonificacionx);

        if (this.data.bonificacionx == 1) return false;
        if (this.productosprecios.length > 1) {
            try {
                this.selector.show({
                    title: "SELECCIONAR UNIDAD.",
                    items: [this.productosprecios],
                    positiveButtonText: "SELECCIONAR",
                    negativeButtonText: "CANCELAR",
                    displayKey: 'Name'
                }).then((result: any) => {
                    let ux: any = this.productosprecios[result[0].index];
                    this.recalculoPrecios(result[0].index);
                }, (err: any) => {
                    console.log(err);
                });
            } catch (e) {
                console.log(e);
            }
        } else if (this.productosprecios.length == 1) {
            this.native.mensaje('Solo tiene un tipo de unidad', '3000', 'top');
        } else {
            this.native.mensaje('No existe precio asociado al producto.', '3000', 'top');
        }
    }

    /*SETTEADOR DE DATA*/
    public async cantidadCal(event: any) {
        (isNaN(event.detail.target.valueAsNumber)) ? this.cantidad = 1 : this.cantidad = event.detail.target.valueAsNumber;
        this.cantidadUI = this.cantidad;
        if (this.data.combo == 1) {
            this.descuentomonetario = (this.cantidad * this.descuentomonetario);
        }

        //IBJ
        //this.descuentoporsentual  = 20;
        console.log("activo para descuetos especiales", this.descuentosEspeciales);
        if (this.descuentosEspeciales) {
            if (this.data.edit == true) {

                this.descuentoporsentual = await this.utilisService.DescuentosSap(this.data.dataexport.cliente.CardCode, this.data.ItemCode, this.data.PriceListNo, this.cantidadUI, 0, 0, "", 0);;

            } else {
                this.descuentoporsentual = await this.utilisService.DescuentosSap(this.data.dataexport.cliente.CardCode, this.data.ItemCode, this.data.dataexport.listaPrecio.PriceListNo, this.cantidadUI, 0, 0, "", 0);;

            }
            console.log("this.descuentoporsentual  ", this.descuentoporsentual);
            if (this.descuentoporsentual > 0) {
                this.native.mensaje('Descuento porcentual asignado : ' + this.descuentoporsentual);
                this.segmentModel = false;
                this.cambiartipollenado(true)
            } else {
                //this.native.mensaje('Descuento porcentual asignado : '+ this.descuentoporsentual);
                this.segmentModel = true;
                this.cambiartipollenado(false)
            }
        }
        this.calTotal();
    }

    public precioCal(event: any) {
        this.precio = event.detail.target.valueAsNumber;
        this.calTotal();
    }

    public bonificacionEstado() {
        if (this.bonificacion == true) {
            this.descuentomonetario = 0;
            this.descuentoporsentual = 0;
        }
        this.calTotal();
    }

    public descporcentualCal(event: any) {
        if (this.bonificacion == true) {
            this.descuentomonetario = 0;
            this.descuentoporsentual = 0;
            this.calTotal();
            return false;
        }
        this.descuentomonetario = 0;
        (isNaN(event.detail.target.valueAsNumber)) ? this.descuentoporsentual = 0 : this.descuentoporsentual = event.detail.target.valueAsNumber;
        console.log(" this.descuentoporsentual ", this.descuentoporsentual);

        this.calTotal();
    }

    public descmonetarioCal(event: any) {
        if (this.bonificacion == true) {
            this.descuentomonetario = 0;
            this.descuentoporsentual = 0;
            this.calTotal();
            return false;
        }
        this.descuentoporsentual = 0;
        (isNaN(event.detail.target.valueAsNumber)) ? this.descuentomonetario = 0 : this.descuentomonetario = event.detail.target.valueAsNumber;
        this.calTotal();
    }

    public async calTotal() {

        console.log("INICIA calTotal 915");
        
        console.log("CONSULTA DATOS DE SESION");
        let usuariodata: any = await this.configService.getSession();

        this.localizacion.usadecimales = usuariodata[0].usa_redondeo;
        this.localizacion.precio = this.precio;
        this.localizacion.cantidad = this.cantidad;
        this.localizacion.porcentual = this.descuentoporsentual;
        this.localizacion.monetario = this.descuentomonetario;

        console.log("VALIDA auxTLoc 926",this.auxTLoc);

        if (this.auxTLoc == 2) {
            this.localizacion.tice = this.producto_std2;
            this.localizacion.icee = this.producto_std4;
            this.localizacion.icep = this.producto_std3;
            this.localizacion.cantidaduni = this.cantidaduni;
        }
        if (this.auxTLoc == 3) {
            this.localizacion.tice = this.data.dataexport.listaPrecio.IsGrossPrice;
            this.localizacion.icee = this.producto_std3;
            this.localizacion.turismo = this.data.dataexport.cliente.cliente_std1;
            this.localizacion.vigencia = this.data.dataexport.cliente.cliente_std2;
            this.localizacion.extranjero = this.data.dataexport.cliente.cliente_std3;
        }
        if (this.auxTLoc == 4) {
            this.localizacion.tice = this.data.dataexport.listaPrecio.IsGrossPrice;
            this.localizacion.icee = this.producto_std3;
        }
        if (this.auxTLoc == 5) {
            console.log("auxprecio neto", this.localizacion.precio, this.auxprecio);
            this.localizacion.precio = this.auxprecio;
            this.localizacion.tice = this.data.dataexport.listaPrecio.IsGrossPrice;
            this.localizacion.icee = this.producto_std3;
            this.localizacion.precioespecial =this.validaprecio;
            let indicadorImpuesto: any = await this.indicadoresImpuestosModel.find(this.impuestoVal);
            console.log("indicadores de impuesto",indicadorImpuesto);
            if (indicadorImpuesto && indicadorImpuesto.length > 0) {
                this.localizacion.icee = indicadorImpuesto[0].Rate;
            }
            console.log("maneja percepcion",this.manejaPercepcion);
            this.localizacion.manejaPercepcion = this.manejaPercepcion;

        }
        if (this.auxTLoc == 6) {
            console.log("precio asignado de auxiliar->", this.localizacion.precio);
            this.localizacion.tice = this.data.dataexport.listaPrecio.IsGrossPrice;
            this.localizacion.icee = this.producto_std3;
            this.localizacion.turismo = this.data.dataexport.cliente.cliente_std6;//1
            this.localizacion.vigencia = this.data.dataexport.cliente.cliente_std7;//2
            this.localizacion.extranjero = this.data.dataexport.cliente.cliente_std8;//3
            this.localizacion.turismoproducto = this.producto_std2;
        }

        console.log("LLAMA A CALCULO DE LA LOCALIZACION 946");
        let respuesta = await this.localizacion.calculo();
        console.log("DATOS QUE SE RETORNAN",respuesta);
        let auxiliar: number = 0.00;

        this.totalNeto = respuesta.totalNeto;
        this.descuentoNeto = respuesta.descuento;
        this.total = respuesta.total;

        console.log("VALIDA auxTLoc 953",this.auxTLoc);

        if (this.userdata[0].redondeo == 1 && this.currencyDefault != 'USD') {
            this.totalNeto = Math.round(Number(respuesta.totalNeto));
            this.descuentoNeto = Math.round(Number(respuesta.descuento));
            this.total = respuesta.total;
            auxiliar = Math.round(Number(respuesta.icee));
        }
        else {
            if (this.auxTLoc == 3 || this.auxTLoc == 5) {
                auxiliar = respuesta.icee;
            }
            this.totalNeto = respuesta.totalNeto;
            this.descuentoNeto = respuesta.descuento;
            this.total = respuesta.total;
        }

        if (this.auxTLoc == 2) {
            this.ice = this.localizacion.tice;
            this.icee = respuesta.icee;

            this.icep = respuesta.icep;
        }
        if (this.auxTLoc == 3) {
            this.ice = this.localizacion.tice;
            this.icee = respuesta.icee;
            this.icep = this.data.producto_std2;
        }
        if (this.auxTLoc == 4) {
            this.icep = respuesta.icep;
        }
        if (this.auxTLoc == 5) {
            if (this.manejaPercepcion) {
                this.ice = this.localizacion.tice;
                //this.icee = respuesta.icee;                           
                if (this.data.edit == true) {
                    this.impuestoVal = this.data.NomIva ? this.data.NomIva : this.data.TaxCode;
                }

                this.icee = auxiliar;
                this.VatPrcnt = this.icee;
                this.icep = this.data.producto_std2;
                console.log("elprecio es",respuesta.precio);
                this.precio = Number(respuesta.precio);
                this.VatSum = this.icee;
                this.auxprecioBruto=respuesta.precioBruto

            } else {
                this.ice = this.localizacion.tice;
                this.icee = auxiliar;//respuesta.icee;
                this.VatPrcnt = this.icee;
                console.log("impuesto final", this.icee)
                this.icep = this.data.producto_std2;
                console.log("====", respuesta)
                console.log("====", this.productosprecios)
                //this.productosprecios[this.indexUnidad].Price = respuesta.precio;
                this.precio = respuesta.precio;
                this.auxprecioBruto=respuesta.precioBruto
                this.VatSum = 0
            }

            this.TotalFrgn = 0;
            this.VatSumFrgn = 0;
        }

        if (this.auxTLoc == 6) {
            this.ice = this.localizacion.tice;
            this.icee = respuesta.icee;
            this.icep = this.data.producto_std2;
            this.productosprecios[this.indexUnidad].Price = respuesta.precio;
            this.precio = respuesta.precio;            
        }
    }

    public async control() {
        console.log("  control() ");
        let rxr: boolean = true;
        let ox: any = await this.configService.getSession();
        await this.controlDescuento();
        let rx: any = await this.controlStock();
        let tx: any = await this.configService.getTipo();
        let tfx: any = this.data.dataexport.tipoDocx;
        if ((tx == 'DFA') && (tfx == 0)) {
            try {
                if (this.cantidad > rx.InStock) {
                    return false;
                }
            } catch (e) {
                this.native.mensaje('La cantidad a superado al disponible.', '3000', 'top');
                return false;
            }
        }
        if (tx == 'DOE') {
            try {
                if (this.cantidad > rx.InStock) {
                    return false;
                }
            } catch (e) {
                this.native.mensaje('La cantidad a superado al disponible.', '3000', 'top');
                return false;
            }
        }
        if (tx == 'DOP') {
            if (ox[0].config[0].accessstock == '1') {
                if (this.cantidad > rx.InStock) {
                    this.native.mensaje('La cantidad a superado al disponible.', '3000', 'top');
                    return false;
                }
            }
        }
        return rxr;
    }
    
    public async exeguardardocumento() {
        
        console.log("CONSOLA: INICIA exeguardardocumento 1015");

        this.estado = true;
        this.estadodes = true;

        console.log("CONSOLA: VALIDA SI ES DOF O DOP 1020");
        if (this.data.dataexport.tipoDoc == 'DOF' || this.data.dataexport.tipoDoc == 'DOP') {
            let cantidadBase = (this.cantidad * this.cantidaduni);
            console.log("CONSOLA: CONSULTA DATOS DE LA SESION");
            const datasession: any = await this.configService.getSession();
            let validador = 0;
            let mensaje = '';

            if(datasession[0].validaciondisponible == '1'){
                validador = this.disponibleheader;
                mensaje = "La cantidad supero al Stock.";
            }else{
                validador = this.disponibleStock;
                mensaje = "La cantidad supero al disponible.";
            }
            
            if ((validador - cantidadBase) < 0) {
                this.dialogs.confirm(mensaje, "Xmobile.", ["CANCELAR", "CONTINUAR"]).then((data) => {
                    switch (data) {
                        case (2):
                            console.log("CONSOLA: LLAMA FUNCION exeguardar 1040");
                            this.exeguardar();
                            break;
                        case (1):
                            this.estado = false;
                            this.estadodes = false;
                            break;
                        case (0):
                            this.estado = false;
                            this.estadodes = false;
                            break;
                        default:
                            console.log("CONSOLA: LLAMA FUNCION exeguardar 1052");
                            this.exeguardar();
                    }
                }).catch(() => {

                    this.navCrl.pop();
                })
            } else {
                console.log("CONSOLA: LLAMA FUNCION exeguardar 1060");
                this.exeguardar();
            }
        } else {
            console.log("CONSOLA: LLAMA FUNCION exeguardar 1064");
            this.exeguardar();
        }
    }

    public async exeguardar() {
        console.log("CONSOLA: INICIA exeguardar 1070");

        if (this.descuentoporsentual < 0) {
            this.native.mensaje('El descuento no puede ser negativo.', '3000', 'top');
            this.estado = false;
            this.estadodes = false;
            return false;
        }


        if (this.userdata[0].config[0].descuentosDocumento == '0' && this.descuentoporsentual != 0) {

            this.native.mensaje('No está permitido para asignar descuentos a nivel linea.', '3000', 'top');
            this.estado = false;
            this.estadodes = false;
            return false;
        }

        if (this.descuentoporsentual > this.userdata[0].config[0].totalDescuento && this.data.bonificacionx == 0) {
            this.native.mensaje('El descuento no debe superar el  ' + this.userdata[0].config[0].totalDescuento + '%. ', '4000', 'top');
            this.estado = false;
            this.estadodes = false;
            return false;
        }

        if (this.cantidad == 0) {
            this.native.mensaje('Cantidad debe ser mayor a 0', '3000', 'top');
            this.estado = false;
            this.estadodes = false;
            return false;
        }
        if (this.validanum(this.cantidad) == false) {
            this.native.mensaje('Insertar numeros enteros en cantidad', '3000', 'top');
            this.estado = false;
            this.estadodes = false;
            return false;
        }

        if (this.data.bonificacionx == 1 || this.data.IdBonfAut != 0) {
            if (this.cantidad > this.cantidadproductos) {
                this.native.mensaje('Cantidad es mayor al limite.', '3000', 'top');
                this.estado = false;
                this.estadodes = false;
                return false;
            }
        }
        if (this.data.combo == 0) {

            console.log("CONSOLA: LLAMA A FUNCION control 1118");
            let xrx: any = await this.control();

            if (xrx == false) {
                this.native.mensaje('La cantidad a superado al disponible.', '3000', 'top');
                this.estado = false;
                this.estadodes = false;

                return false;
            }
        }

        if ((this.data.dataexport.tipoDoc == 'DFA' && this.data.dataexport.tipoDocx == 0 || this.data.dataexport.cliente.DocType == 'DOE')) {
            let cantidadBase = (this.cantidad * this.cantidaduni);
            
            if (this.estado == false) {
                if ((this.disponibleheader - cantidadBase) < 0) {
                    this.native.mensaje('La cantidad supero al disponible.', '4000', 'top');
                    this.estado = false;
                    this.estadodes = false;
                    return false;
                }
            } else {
                if ((this.disponibleheader - cantidadBase) < 0) {
                    this.native.mensaje('La cantidad supero al disponible.', '4000', 'top');
                    this.estado = false;
                    this.estadodes = false;
                    return false;
                }
            }

        }

        if ((this.data.dataexport.tipoDoc == 'DFA' && this.data.dataexport.tipoDocx == 0 && this.data.ManageSerialNumbers == '1') ||
            (this.data.dataexport.cliente.DocType == 'DOE' && this.data.ManageSerialNumbers == '1')) {
            if (this.seriesSlide.length > 0 && (this.cantidadUI == this.seriesSlide.length)) {
            } else {
                this.native.mensaje('El número de series no coincide con la cantidad seleccionada.', '3000', 'top');
                this.estado = false;
                this.estadodes = false;
                return false;
            }
        }
        if ((this.data.dataexport.tipoDoc == 'DFA' && this.data.dataexport.tipoDocx == 0 && this.data.ManageBatchNumbers == '1') ||
            (this.data.dataexport.cliente.DocType == 'DOE' && this.data.ManageBatchNumbers == '1')) {
            let xlot: number = 0;
            for (let lox of this.listaLotes) xlot += lox.num;
            if (this.auxTLoc == 2) {
                if (xlot == 0) {
                    console.log("CONSOLA: LLAMA FUNCION agregar 1167");
                    this.agregar();
                    return;
                }
                if (xlot > 0) {
                    if (xlot === this.cantidad) {
                        console.log("CONSOLA: LLAMA FUNCION agregar 1173");
                        this.agregar();
                        return;
                    } else {
                        this.estado = false;
                        this.estadodes = false;
                        this.native.mensaje('La cantidad  de lotes no es igual verifíquelo e inténtelo nuevamente. ', '4000', 'top');
                        return false;
                    }
                }
            } else {
                if (this.auxTLoc != 2) {
                    if (xlot === this.cantidad) {
                        console.log("CONSOLA: LLAMA FUNCION agregar 1186");
                        this.agregar();
                        return;
                    } else {
                        this.estado = false;
                        this.estadodes = false;
                        this.native.mensaje('La cantidad  de lotes no es igual verifíquelo e inténtelo nuevamente. ', '4000', 'top');
                        return false;
                    }
                } else {
                    this.estado = false;
                    this.estadodes = false;
                    this.native.mensaje('Debes seleccionar lotes. ', '4000', 'top');
                    return false;
                }
            }
            this.estado = false;
            this.estadodes = false;
        }
        console.log("CONSOLA: LLAMA FUNCION agregar 1205");
        this.agregar();
    }

    public async agregar() {
        
        console.log("CONSOLA: INICIA agregar 1211");

        let detalles = new Detalle();
        let documento = GlobalConstants.CabeceraDoc;

        let hayBonificacion = [];

        let itemsAux = GlobalConstants.DetalleDoc;

        console.log("CONSOLA: VALIDA SI idPedido ES DIFERENTE A 0 1220");
        if (this.data.dataexport.idPedido != 0) {
            for await (let item of itemsAux) {

                if (item.bonificacion > 0) {
                    console.log("-----> a eliminar each itemsAux ", item);
                    hayBonificacion.push(item);

                }
            }

            if (documento[0].clone != 0 && hayBonificacion.length > 0 && this.data.edit == true) { //
                const alert = await this.alertController.create({
                    cssClass: "my-custom-class",
                    header: "Guardar ",
                    message: "Se encontraron Bonificaciones / Descuentos en el documento, deberá volver a seleccionarlos al guardar el documento. <strong> </strong>",
                    buttons: [
                        {
                            text: "Cancelar",
                            role: "cancel",
                            cssClass: "secondary",
                            handler: (blah) => {
                                return false;
                            },
                        },
                        {
                            text: "Aceptar",
                            handler: async (data: any) => {
                                if (hayBonificacion.length > 0) {
                                    for await (let item of hayBonificacion) {

                                        if (item.bonificacion == 1) {
                                            console.log("CONSOLA: LLAMA FUNCION updateBonificacionLineaReset 1252");
                                            detalles.updateBonificacionLineaReset(item.id);
                                            localStorage.setItem('mofificarBonificacion', 'SI');
                                        }
                                        if (item.bonificacion == 2 || item.bonificacion == 3) {
                                            console.log("CONSOLA: LLAMA FUNCION updateDescuentoLineaReset 1257");
                                            detalles.updateDescuentoLineaReset(this.data.dataexport.idPedido, item.id, item.LineTotal);
                                            localStorage.setItem('mofificarBonificacion', 'SI');
                                        }

                                    }
                                    console.log("CONSOLA: LLAMA FUNCION agregarProducto 1263");
                                    this.agregarProducto();
                                }

                            },
                        },
                    ],
                });

                await alert.present();
                return false;

            } else {
                console.log("CONSOLA: LLAMA FUNCION agregarProducto 1276");
                this.agregarProducto();
            }
        } else {
            console.log("CONSOLA: LLAMA FUNCION agregarProducto 1280");
            this.agregarProducto();
        }
    }

    public async agregarProducto() {

        console.log("CONSOLA: INICIA agregarProducto 1287");

        this.estado = false;
        this.estadodes = false;
        let data: object;

        console.log("CONSOLA: CARGA DATOS A RETORNAR 1293",this.auxTLoc);
        switch (this.auxTLoc) {
            case (2):
                data = {
                    almacen: this.data.dataexport.almacen.WarehouseCode,
                    itemName: this.data.Dscription,
                    unidad: this.productosprecios[this.indexUnidad],
                    cantidad: this.cantidad,
                    presio: this.totalNeto,
                    descuento: this.descuentoNeto,
                    descuentoporsentaje: this.descuentoporsentual,
                    descuentototal: this.descuentoNeto,
                    bonificacion: this.bonificacion,
                    icett: this.total,
                    icete: this.producto_std4,
                    icetp: this.producto_std3,
                    ICEt: this.ice,
                    ICEp: this.icep,
                    ICEe: this.icee,
                    lotesarr: this.listaLotes,
                    lotesarrAux: this.listaLotesAux,
                    seriesarr: this.seriesSlide,
                    GroupName: this.data.GroupName
                };
                break;
            case (3):
                data = {
                    almacen: this.data.dataexport.almacen.WarehouseCode,
                    itemName: this.data.Dscription,
                    unidad: this.productosprecios[this.indexUnidad],
                    cantidad: this.cantidad,
                    presio: this.totalNeto,
                    descuento: this.descuentoNeto,
                    descuentoporsentaje: this.descuentoporsentual,
                    descuentototal: this.descuentoNeto,
                    bonificacion: this.bonificacion,
                    icett: this.total,
                    icete: this.producto_std4,
                    icetp: this.producto_std3,
                    ICEt: this.ice,
                    ICEp: 0,
                    ICEe: this.icee,
                    lotesarr: this.listaLotes,
                    lotesarrAux: this.listaLotesAux,
                    seriesarr: this.seriesSlide,

                };
                break;
            case (4):
                data = {
                    almacen: this.data.dataexport.almacen.WarehouseCode,
                    itemName: this.data.Dscription,
                    unidad: this.productosprecios[this.indexUnidad],
                    cantidad: this.cantidad,
                    presio: this.totalNeto,
                    descuento: this.descuentoNeto,
                    descuentoporsentaje: this.descuentoporsentual,
                    descuentototal: this.descuentoNeto,
                    bonificacion: this.bonificacion,
                    icett: this.total,
                    icete: this.producto_std4,
                    icetp: this.producto_std3,
                    ICEt: this.ice,
                    ICEp: this.icep,
                    ICEe: this.icee,
                    lotesarr: this.listaLotes,
                    lotesarrAux: this.listaLotesAux,
                    seriesarr: this.seriesSlide,
                    GroupName: this.data.GroupName
                };
                break;
            case (5):
                data = {
                    almacen: this.data.dataexport.almacen.WarehouseCode,
                    itemName: this.data.Dscription,
                    unidad: this.productosprecios[this.indexUnidad],
                    cantidad: this.cantidad,
                    presio: this.precio,
                    descuento: this.descuentoNeto,
                    descuentoporsentaje: this.descuentoporsentual,
                    descuentototal: this.descuentoNeto,
                    bonificacion: this.bonificacion,
                    icett: this.total,
                    icete: this.producto_std4,
                    icetp: this.producto_std3,
                    ICEt: this.ice,
                    ICEp: 0,
                    ICEe: this.icee,
                    lotesarr: this.listaLotes,
                    lotesarrAux: this.listaLotesAux,
                    seriesarr: this.seriesSlide,
                    combo: this.itemcombo,
                    escombo: this.iscombo,
                    //campos para percepciones
                    TaxCode: this.impuestoVal,
                    VatPrcnt: this.VatPrcnt,
                    TaxOnly: this.taxOnly ? 'Y' : 'N',
                    U_EXX_GRUPOPER: this.dataPercepcion,
                    U_EXX_GRUPERMAN: this.grpPerManual ? 'Y' : 'N',
                    U_EXX_PERDGHDCM: this.perDispCombustible ? 'Y' : 'N',
                    VatSum: this.VatSum,
                    TotalFrgn: this.TotalFrgn,
                    VatSumFrgn: this.VatSumFrgn,
                    U_EXX_FE_TPVU: this.selecTipoPrecioVenta ? this.selecTipoPrecioVenta : "",
                    U_EXX_FE_TAIGV: this.selecFexAfectacionIgv ? this.selecFexAfectacionIgv : "",
                    PriceListGross: this.data.dataexport.listaPrecio.IsGrossPrice,
                    NomIva: "",
                    LineTotalPay: this.total,
                    PriceAfterVAT: this.auxprecio,
                    unidadid: this.productosprecios[this.indexUnidad].IdUnidadMedida,
                    U_XM_fraccionamiento: this.productosprecios[this.indexUnidad].U_XM_fraccionamiento,    
                };
                break;
            case (6):
                data = {
                    almacen: this.data.dataexport.almacen.WarehouseCode,
                    itemName: this.data.Dscription,
                    unidad: this.productosprecios[this.indexUnidad],
                    cantidad: this.cantidad,
                    presio: this.totalNeto,
                    descuento: this.descuentoNeto,
                    descuentoporsentaje: this.descuentoporsentual,
                    descuentototal: this.descuentoNeto,
                    bonificacion: this.bonificacion,
                    icett: this.total,
                    icete: this.producto_std4,
                    icetp: this.producto_std3,
                    ICEt: this.ice,
                    ICEp: 0,
                    ICEe: this.icee,
                    lotesarr: this.listaLotes,
                    lotesarrAux: this.listaLotesAux,
                    seriesarr: this.seriesSlide,
                    combo: this.itemcombo,
                    escombo: this.iscombo
                };
                break;
            default:
                data = {
                    almacen: this.data.dataexport.almacen.WarehouseCode,
                    itemName: this.data.Dscription,
                    unidad: this.productosprecios[this.indexUnidad],
                    cantidad: this.cantidad,
                    presio: this.totalNeto,
                    descuento: this.descuentoNeto,
                    descuentoporsentaje: this.descuentoporsentual,
                    descuentototal: this.descuentoNeto,
                    bonificacion: this.bonificacion,
                    icett: this.totalNeto,
                    icete: 0,
                    icetp: 0,
                    ICEt: 0,
                    ICEp: 0,
                    ICEe: 0,
                    lotesarr: this.listaLotes,
                    lotesarrAux: this.listaLotesAux,
                    seriesarr: this.seriesSlide

                };
        }

        if (this.data.bonificacionx == 1) {

            let cantAux = parseInt(localStorage.getItem('cantidadproductoslimitBONI'));
            cantAux = cantAux - this.cantidadUI;
            localStorage.setItem('cantidadproductoslimitBONI', "" + cantAux);
            localStorage.setItem("cancelado", "NO");
        }
        this.modalController.dismiss(data);
        
    }


    public cambiartipollenado(segm: any) {
        console.log("LLAMA A CAMBIAR PESTAÑA",segm);
        if (!this.data.XMPORCENTAJECABEZERA || this.data.XMPORCENTAJECABEZERA == 0) {
            this.tipollenado = segm;
        } else {
            this.native.mensaje('Acción no permitida, existe un descuento porcentual de cabezera, no podemos re calcular el porcentaje a nivel item. ', '4000', 'top');

        }

    }

    public cerrar(data: any) {
        if (this.data.bonificacionx == 0) {
            this.modalController.dismiss(data);
        } else {
            this.modalController.dismiss(4);
        }

    }

    public validanum(newValue: any) {
        const regExp = new RegExp(/^([0-9])*$/) // --- sin comillas
        const resultado = regExp.test(newValue)
        return resultado;
    }
    /**
       * BONIFICACIONEES
       */
    public async validBonificacion(producto: any) {
        if (this.data.bonificacionx == 0) {
            let bonificacionesUsadas = [];
            console.log("DEVD validBonificacion() this.data ", this.data);
            let grupoProducto = this.data.GroupName;
            console.log("grupoProducto ", grupoProducto);
            let grupoCliente = this.data.dataexport.cliente.GroupName;

            console.log("grupoCliente ", grupoCliente);

            console.log("DEVD producto a evaluar () ", producto);

            let existeEnCompras: any = await this.bonificacion_compras.getIdCabezera(this.data.ItemCode);
            let existeEnComprasGrupo: any = [];
            console.log("existeEnCompras ", existeEnCompras);

            if (existeEnCompras.length == 0) {
                existeEnComprasGrupo = await this.bonificacion_compras.getGruposIdCabezera(grupoProducto);

                if (existeEnComprasGrupo.length > 0) {
                    existeEnCompras = existeEnComprasGrupo;
                    console.log("existeEnCompras grupos ", existeEnComprasGrupo);
                }

            }
            console.log("existeEnCompras ", existeEnCompras);
            for (let i = 0; i < existeEnCompras.length; i++) {
                console.log("new each ", existeEnCompras[i]);
                let bonificacionVigenteCabezera: any = await this.Bonificacion_ca.getBonificacionExist(existeEnCompras[i].code_bonificacion_cabezera);
                // let bonificacionVigenteCabezera: any = await this.Bonificacion_ca.getBonificacionExist(existeEnCompras.code_bonificacion_cabezera);

                // let bonificacionVigenteCabezera: any = await this.Bonificacion_ca.getBonificacionesDisponibles(0, 0);
                console.log("DEVD bonificacionVigenteCabezera ", i, bonificacionVigenteCabezera);
                if (bonificacionVigenteCabezera.length > 0) {

                    let productosBonificacionCompra: any = await this.bonificacion_compras.findForCabezera(bonificacionVigenteCabezera[0].code);

                    if (productosBonificacionCompra.length > 0 && bonificacionVigenteCabezera.length > 0) {

                        let productoIsBonificable: any;
                        if (existeEnComprasGrupo.length > 0) {
                            productoIsBonificable = await this.bonificacion_compras.validProductoInComprasGrupo(grupoProducto, bonificacionVigenteCabezera[0].code);
                        } else {
                            productoIsBonificable = await this.bonificacion_compras.validProductoInCompras(this.data.ItemCode, bonificacionVigenteCabezera[0].code);

                        }

                        console.log("DEVD logica bonificacion dispobles");
                        //VER SI EL PRODUCTO ESTA EN PRODUCTOS COMPRAS 
                        if (productoIsBonificable.length) {
                            console.log("DEVD esta en bopnificaciones compras productoIsBonificable ", productoIsBonificable);
                            // VER SI CUMPLE REQUISITOS PARA SER PARTE DE UNA BONIFICACION
                            let validProductoUnindadGrupo: any = await this.bonificacion_compras.validProductoUnindadGrupo(bonificacionVigenteCabezera[0].unindad_compra, bonificacionVigenteCabezera[0].code);
                            if (validProductoUnindadGrupo.length > 0) {
                                console.log("DEVD cumple requisitos ");
                                // if (bonificacionVigenteCabezera[0].tipo == 'PRODUCTOS ESPECIFICOS') {
                                //   console.log("Es de tipo espcifico");
                                if (producto.unidad.BaseQty == 0) {
                                    producto.unidad.BaseQty = 1;
                                }
                                bonificacionesUsadas.push({
                                    code_bonificacion_cabezera: bonificacionVigenteCabezera[0].code,
                                    code_compra: this.data.ItemCode,
                                    //cantidad: this.cantidadUI,
                                    cantidad: this.cantidadUI * producto.unidad.BaseQty,
                                    unidad: bonificacionVigenteCabezera[0].unindad_compra, // this.nombreunidad,
                                    cardCode: this.data.dataexport.cliente.CardCode,
                                    estado: "PENDIENTE",
                                    id_vendedor: 1,
                                    idDocumento: 0,
                                    idDocumentoDetalle: 0,
                                    total: this.total
                                });
                                //this.bonificacion_compras.markeUseCardCodeCompra(this.data.ItemCode);
                                /*  }
                                  if (bonificacionVigenteCabezera[0].tipo == 'GRUPO DE PRODUCTOS') {
  
                                      console.log("No de tipo espcifico");
  
  
                                  }
                                  */


                            } else {
                                console.log("DEVD NO cumple requisitos ");
                            }

                        } else {
                            console.log("DEVD no es bonificable ", this.data.ItemCode);
                        }
                        //console.log("select all usados ", await this.Bonificacion_ca.getCompraUsados());

                        //    console.info("DEVD bonificacionVigenteCabezera() ", bonificacionVigenteCabezera);
                        //    console.info("DEVD productosBonificacionCompra() ", productosBonificacionCompra);

                    }
                    console.log("bonificacionesUsadas ", bonificacionesUsadas.length)
                    producto.bonificacionesUsadas = bonificacionesUsadas;
                    console.log("nuevo data interno producto ", producto);
                } else {
                    console.log("DEVD Bonifdicacion no vigentge ");
                    bonificacionVigenteCabezera = [];
                    producto.bonificacionesUsadas = bonificacionesUsadas;
                }

            }

        }
        else {

        }

        /*       //bonificacionVigenteCabezera
              
             
               console.info("DEVD getBonificacionesDisponibles() ", bonificacionVigenteCabezera);
              
               console.info("DEVD findForCabezera() ", await this.bonificacion_compras.findForCabezera(bonificacionVigente[0].code));
               
       */
    }

    /* CAMPOS DINAMICOS DE USUARIO*/
    public async carga_camposusuario(datos,id) {
        console.log("llaga aqui0");
        let usuariodata: any = await this.configService.getSession();
        let contenedorcampos = '';

        if (usuariodata[0].campodinamicos.length > 0) {

            contenedorcampos = await this.dataService.createcampususer(usuariodata[0].campodinamicos, this.idfrom, datos);
        }

        const div: HTMLDivElement = this.renderer.createElement('div');
        div.className = "col-md-12";
        div.innerHTML = contenedorcampos;
        this.renderer.appendChild(document.getElementById(id), div);

        for (let i = 0; i < usuariodata[0].campodinamicos.length; i++) {
            if (usuariodata[0].campodinamicos[i].Objeto == this.idfrom) {
                if (usuariodata[0].campodinamicos[i].tipocampo == 1) {
                    if (usuariodata[0].campodinamicos[i].flagrelacion == 1) {
                        let campo = "campousu" + usuariodata[0].campodinamicos[i].Nombre;
                        this.eventoClick = this.renderer.listen(
                            document.getElementById(campo),
                            "ionChange",
                            evt => {
                                this.cargalista_campousuario(evt, usuariodata[0].campodinamicos[i].Id);
                            }
                        );
                    }
                }
            }
        }
    }

    public async datacamposusuario() {
        let data = [];
        let valor: any;
        let sesion = await this.configService.getSession();
        let camposusuario = sesion[0].campodinamicos;
        for (let i = 0; i < camposusuario.length; i++) {
            if (camposusuario[i].Objeto == this.idfrom) {
                let campo = "campousu" + camposusuario[i].Nombre;
                var variable = document.getElementsByClassName(campo);
                if (camposusuario[i].tipocampo == 1) {
                    for (let i = 0; i < variable[0]["childNodes"].length; i++) {
                        if (variable[0]["childNodes"][i]["className"] == "aux-input") {
                            valor = variable[0]["childNodes"][i]["defaultValue"];
                        }
                    }
                } else {
                    if (camposusuario[i].tipocampo == 0) {
                        valor = variable[0]["childNodes"][0]["childNodes"][0]["defaultValue"];
                    } else {

                        valor = variable[0]["childNodes"][1]["value"];
                    }
                }
                data.push({
                    Objeto: camposusuario[i].Objeto,
                    cmidd: camposusuario[i].cmidd,
                    tabla: camposusuario[i].tabla,
                    campo: campo,
                    valor: valor
                });
            }
        }
        return data;
    }

    public async cargalista_campousuario(val, id) {
        let sesion = await this.configService.getSession();
        let camposusuario = sesion[0].campodinamicos;
        let codigosel = '';
        for (let i = 0; i < camposusuario.length; i++) {
            if (camposusuario[i].tipocampo == 1) {
                if (camposusuario[i].Id == id) {
                    for (let l = 0; l < camposusuario[i].lista.length; l++) {
                        if (camposusuario[i].lista[l].codigo == val.detail.value) {
                            codigosel = camposusuario[i].lista[l].Id;
                        }
                    }
                }
            }
        }


        for (let i = 0; i < camposusuario.length; i++) {
            if (camposusuario[i].Objeto == this.idfrom) {
                if (camposusuario[i].tipocampo == 1) {
                    if (camposusuario[i].relacionado == id) {
                        let campo = ".campousu" + camposusuario[i].Nombre;
                        const objeto = document.querySelector(campo);
                        let contenedorcampos = '';
                        for (let l = 0; l < camposusuario[i].lista.length; l++) {
                            if (camposusuario[i].lista[l].cabecera == id && camposusuario[i].lista[l].detalle == codigosel) {
                                let codigo = camposusuario[i].lista[l].codigo;
                                let nombre = camposusuario[i].lista[l].nombre.replace(/['"]+/g, '');
                                contenedorcampos += "<ion-select-option  value=" + codigo + ">" + nombre + "</ion-select-option>"
                            }
                        }
                        objeto.innerHTML = contenedorcampos;
                    }
                }
            }
        }
    }


    public async controlDescuento() {
        let xData: any;
        let dataext: any = {
            "codigo": this.data.ItemCode,
            "cardcode": this.data.dataexport.cliente.CardCode
        };
        let decuentos = new Descuentos();

        if (this.network.type != 'none') {
            this.spinnerDialog.show('', 'Cargando...', true);
            try {
                xData = await this.dataService.servisDescuentosapPost("descuentosespecialessap/descuentossap",dataext,3);
                let xJson = JSON.parse(xData.data);
                //console.log("xJson.respuesta", xJson.respuesta);
                await decuentos.ActualizadecuentosSap(xData,this.data.ItemCode,this.data.dataexport.cliente.CardCode);
                this.spinnerDialog.hide();
            } catch (error) {
                this.spinnerDialog.hide();
                this.native.mensaje('No se encontraron descuentos en Sap', '3000', 'center');
            }

        }
    }

    public async inicializarPercepciones() {
        if (this.auxTLoc == 5) {

            this.grpPerManual = false;
            this.perDispCombustible = false;
            // inicializando grupo de percepciones
            this.dataGrupoPer = await this.grupoPersencionesModel.selectGrupospercepciones();

            this.dataIndicadoresImp = await this.indicadoresImpuestosModel.selectIndicadoresImpuestosNoRepetidos();
            console.log("mjt indicadores de impouestos", this.dataIndicadoresImp);


            if (this.data.edit == true) {
                this.impuestoVal = this.data.TaxCode; //await this.cPersepciones.bf_impuestos(this.data.dataexport.sucursal.AddresName,this.data.dataexport.cliente.CardCode,'I','0000',this.dataPercepcion,'',this.impuestoVal);
            }
            else {
                this.impuestoVal = await this.CalculoPercepcionesUtils.getImpuestoDefault(this.data.dataexport.cliente.CardCode, this.data.dataexport.sucursal.AddresName);
                console.log("impuesto por defecto", this.impuestoVal);
            }
            this.setGrupoPerEImpuesto();

            this.dataTipoPrecioVentas = await this.tipoPreciosVentaModel.selectPerPrecioVenta();
            this.dataFexAfectacionIgv = await this.fexAfectacionIgvModel.selectPerfexAceptacionIgv();
            console.log(this.dataTipoPrecioVentas);
            if (this.data.edit == true) {
                this.selecTipoPrecioVenta = this.data.U_EXX_FE_TPVU;
                this.selecFexAfectacionIgv = this.data.U_EXX_FE_TAIGV;
            }
            else {
                this.selecTipoPrecioVenta = "01";
                this.selecFexAfectacionIgv = "10";
                this.dataPercepcion = "0000";
            }
        }
    }

    public async setGrupoPerEImpuesto() {        
        await this.setGrupoPercepciones();
        this.spinnerDialog.show('', 'Cargando...', true);
        await this.setImpuesto();
        await setTimeout(() => {
            this.calTotal()
           }, 500);
        this.spinnerDialog.hide();
    }

    async setGrupoPercepciones(): Promise<any> {
        if (this.manejaPercepcion == true) {
            //'SOL 11800.00'
            let sTota = this.currencyDefault + " " + (this.cantidadUI * this.precio);
            console.log("man sTota->", sTota)
            console.log("man cantidadUI->", this.cantidadUI);
            console.log("man precio->", this.precio);
            console.log("man warehouse->", this.data.dataexport.almacen.WarehouseCode);
            console.log("man cliente carcode->", this.data.dataexport.cliente.CardCode);
            let impuesto: any = await this.configuracionImpuestosModel.findBySTACode(this.impuestoVal);
            let porImp: number = 0;

            if (impuesto.length > 0) {
                impuesto.forEach(element => {
                    porImp = porImp + Number(element.EfctivRate);
                });
                this.VatPrcnt = ((this.cantidadUI * this.precio) * porImp) / 100
                console.log("aux porcentaje del precio ->", this.VatPrcnt);
                console.log("datos inciales GrpPer", this.data.ItemCode, (this.grpPerManual ? 'Y' : 'N'), sTota, this.data.dataexport.almacen.WarehouseCode, (this.perDispCombustible ? 'Y' : 'N'), this.data.dataexport.cliente.CardCode, (this.taxOnly ? 'Y' : 'N'), this.cantidadUI, this.dataPercepcion, porImp, (this.currencyDefault + " " + this.VatPrcnt), 'I')
                let cPercepcion = await this.cPersepciones.bf_GrupoPercepcion(this.data.ItemCode, (this.grpPerManual ? 'Y' : 'N'), sTota, this.data.dataexport.almacen.WarehouseCode, (this.perDispCombustible ? 'Y' : 'N'), this.data.dataexport.cliente.CardCode, (this.taxOnly ? 'Y' : 'N'), this.cantidadUI, this.dataPercepcion, porImp, (this.currencyDefault + " " + this.VatPrcnt), 'I')

                console.log("pecepcion seleccionada->", cPercepcion);
                this.dataPercepcion = cPercepcion.igrupoPercepcion;

                Promise.resolve(true);
            } else {
                // colocar mensaje de no se encontraron impuestos
            }
        }
    }

    async setImpuesto(): Promise<any> {
        console.log("**IBJ**");
        console.log(this.data.TaxCode);

        if (this.data.edit == true) {
            this.impuestoVal = this.data.NomIva ? this.data.NomIva : this.data.TaxCode; //await this.cPersepciones.bf_impuestos(this.data.dataexport.sucursal.AddresName,this.data.dataexport.cliente.CardCode,'I','0000',this.dataPercepcion,'',this.impuestoVal);
        }
        else {
            console.log("datos entrada", this.data.dataexport.sucursal.AddresName, this.data.dataexport.cliente.CardCode, 'I', '0000', this.dataPercepcion, '', this.impuestoVal)
            this.impuestoVal = await this.cPersepciones.bf_impuestos(this.data.dataexport.sucursal.AddresName, this.data.dataexport.cliente.CardCode, 'I', '0000', this.dataPercepcion, '', this.impuestoVal);
        }


        console.log("imp set impuesto", this.impuestoVal);
        Promise.resolve(true);

    }

//#region Funciones peru
    public async priceOriginal() {
        console.log("this.calTotal()  this.precio ", this.precio, "aux local", this.auxTLoc);
        this.localizacion.precio = this.precio;
        this.localizacion.cantidad = this.cantidad;
        this.localizacion.porcentual = this.descuentoporsentual;
        this.localizacion.monetario = this.descuentomonetario;
        if (this.data.edit == false) {
            this.preciocomp = this.auxprecio;
        }

        if (this.auxTLoc == 3) {
            console.log("this.auxprecio1 ->", this.auxprecio);
            console.log("this.data.dataexport.listaPrecio.IsGrossPrice1 ->", this.data.dataexport.listaPrecio.IsGrossPrice);
            console.log(" this.producto_std3 1 ", this.producto_std3);
            console.log("this.data.dataexport.cliente.cliente_std6  1 ->", this.data.dataexport.cliente.cliente_std6);
            console.log(" this.data.dataexport.cliente.cliente_std7 1 ->", this.data.dataexport.cliente.cliente_std7);
            console.log(" this.data.dataexport.cliente.cliente_std8 1 ->", this.data.dataexport.cliente.cliente_std8);

            this.localizacion.precio = this.auxprecio;
            console.log("precio asignado de auxiliar->", this.localizacion.precio);
            this.localizacion.tice = this.data.dataexport.listaPrecio.IsGrossPrice;
            this.localizacion.icee = this.producto_std3;
            this.localizacion.turismo = this.data.dataexport.cliente.cliente_std6;//1
            this.localizacion.vigencia = this.data.dataexport.cliente.cliente_std7;//2
            this.localizacion.extranjero = this.data.dataexport.cliente.cliente_std8;//3
            this.localizacion.turismoproducto = this.producto_std2;
        }

        if (this.auxTLoc == 5) {
            console.log("auxprecio origen", this.localizacion.precio, this.auxprecio);
            this.localizacion.precio = this.auxprecio;
            this.localizacion.tice = this.data.dataexport.listaPrecio.IsGrossPrice;
            this.localizacion.icee = this.producto_std3;
            let indicadorImpuesto: any = await this.indicadoresImpuestosModel.find(this.impuestoVal);
            if (indicadorImpuesto && indicadorImpuesto.length > 0) {
                this.localizacion.icee = indicadorImpuesto[0].Rate;
            }
            this.localizacion.manejaPercepcion = this.manejaPercepcion;
        }
        if (this.auxTLoc == 6) {
            console.log("this.auxprecio1 ->", this.auxprecio);
            console.log("this.data.dataexport.listaPrecio.IsGrossPrice1 ->", this.data.dataexport.listaPrecio.IsGrossPrice);
            console.log(" this.producto_std3 1 ", this.producto_std3);
            console.log("this.data.dataexport.cliente.cliente_std6  1 ->", this.data.dataexport.cliente.cliente_std6);
            console.log(" this.data.dataexport.cliente.cliente_std7 1 ->", this.data.dataexport.cliente.cliente_std7);
            console.log(" this.data.dataexport.cliente.cliente_std8 1 ->", this.data.dataexport.cliente.cliente_std8);

            this.localizacion.precio = this.auxprecio;
            console.log("precio asignado de auxiliar->", this.localizacion.precio);
            this.localizacion.tice = this.data.dataexport.listaPrecio.IsGrossPrice;
            this.localizacion.icee = this.producto_std3;
            this.localizacion.turismo = this.data.dataexport.cliente.cliente_std6;//1
            this.localizacion.vigencia = this.data.dataexport.cliente.cliente_std7;//2
            this.localizacion.extranjero = this.data.dataexport.cliente.cliente_std8;//3
            this.localizacion.turismoproducto = this.producto_std2;
        }

        console.log("antes de calcular --->", this.localizacion.precio, this.auxprecio);

        let respuesta = await this.localizacion.calculo();
        let precio = respuesta.precio;
        console.log("precio Calculo Localizacion del precio original->" + precio);
        console.log(respuesta);

        return precio;
    }

    public async openGrupoPer() {
        try {
            let data: any = await this.grupoPersencionesModel.selectGrupospercepciones();
            let respuesta = await this.selectComponent(data, "SELECCIONAR GRUPO PERCEPCIONES", 'Name');
            this.dataPercepcion = respuesta;
            console.log("variable listo para usar", respuesta);
        } catch (error) {
            console.log("error");

        }

    }

    public async selectComponent(data: any, msgHeader: string = "", key): Promise<any> {
        try {
            console.log("data grupo percepciones", data);

            const result: any = await this.selector.show({
                title: msgHeader,
                items: [data],
                positiveButtonText: "SELECCIONAR",
                negativeButtonText: "CANCELAR",
                displayKey: key
            });

            if (result) {
                console.log("selected", result[0]);
                return Promise.resolve(data[result[0].index]);
            } else {
                return Promise.reject([])
            }
        } catch (e) {
            this.native.mensaje(`Tienes que seleccionar una opción.`, '3000', 'top');
            console.log(e)
        }
    }

    public async onOpenGrpPerManual() {
        try {
            let respuesta = await this.selectComponent(this.dataSelect, "SELECCIONAR OPCIÓN", 'name');
            console.log("variable listo para usar", respuesta);
            this.grpPerManual = respuesta;
        } catch (error) {
            console.log("error");
        }
    }

    public async onOpenPerDispCombustible() {
        try {
            let respuesta = await this.selectComponent(this.dataSelect, "SELECCIONAR OPCIÓN 2", 'name');
            console.log("variable listo para usar", respuesta);
            this.perDispCombustible = respuesta
        } catch (error) {
            console.log("error");
        }
    }

    onRestablecer() {
        this.grpPerManual = false;
        this.perDispCombustible = false;
        this.taxOnly = false;
        this.setGrupoPerEImpuesto();
    }
//#endregion
    
}
