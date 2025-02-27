import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { ModalclientePage } from "../modalcliente/modalcliente.page";
import { ModalproductoPage } from "../modalproducto/modalproducto.page"
import { IonFab, IonRouterOutlet, LoadingController, ModalController, NavController, Platform } from "@ionic/angular";
import { Toast } from "@ionic-native/toast/ngx";
import { Documentos } from "../../models/documentos";
import { Detalle } from "../../models/detalle";
import { ConfigService } from "../../models/config.service";
import { Clientessucursales } from "../../models/clientessucursales";
import { WheelSelector } from '@ionic-native/wheel-selector/ngx';
import { Dialogs } from '@ionic-native/dialogs/ngx';
import { PopoverController } from '@ionic/angular';
import { PopinfoComponent } from "../../components/popinfo/popinfo.component";
import { Clientes } from '../../models/clientes';
import { PopoverPage } from "../popover/popover.page";
import { FromevidenciaPage } from "../fromevidencia/fromevidencia.page";
import { DetalleventaPage } from "../detalleventa/detalleventa.page";
import { AnularPage } from "../anular/anular.page";
import { Lotesproductos } from "../../models/lotesproductos";
import { AlertController } from '@ionic/angular';
import { Calculo } from "../../utilsx/calculo";
import { ReportService } from "../../services/report.service";
import { Codigocontrol } from "../../models/codigocontrol";
import { Tiempo } from "../../models/tiempo";
import { Seriesproductos } from "../../models/seriesproductos"
import { SpinnerDialog } from "@ionic-native/spinner-dialog/ngx";
import { DataService } from "../../services/data.service";
import { Network } from "@ionic-native/network/ngx";
import { FrompagosPage } from "../frompagos/frompagos.page";
import { Productos } from "../../models/productos";
import { Lotes } from "../../models/lotes";
import { Reimpresion } from "../../models/reimpresion";
import { Documentopago } from "../../models/documentopago";
import { Dosificacionproductos } from "../../models/dosificacionproductos";
import { bonificacion_regalos } from '../../models/bonificacion_regalos';
import { bonificacion_compras } from '../../models/bonificacion_compras';
import { Bonificaciones as Bonificacion_ca } from "../../models/V2/bonificaciones";
import * as moment from 'moment';
import 'lodash';
import { Pagos } from "../../models/pagos";
import { Productosalmacenes } from "../../models/productosalmacenes";
import { Location } from '@angular/common';

import { promocionaes } from '../../models/promociones';
import { GlobalConstants } from "../../../global";
import { PagosService } from '../../services/pagos.service';
import { IDataPagoPdf, IPagos } from '../../types/IPagos';
import { ICliente } from '../../types/IClientes';
import { BonificacionesService } from '../../services/bonificaciones.service';
import { Geolocation } from '@ionic-native/geolocation/ngx';
import { InAppBrowser,InAppBrowserOptions } from '@ionic-native/in-app-browser/ngx';
import { ReportStyleUnoService } from 'src/app/services/report-style-uno.service';
import { Bolivia } from "../../utilsx/bolivia";
import { Companex } from "../../utilsx/companex";
import { Chile } from "../../utilsx/chile";
import { Paraguay } from "../../utilsx/paraguay";
import { ConfiguracionEntregaPage } from '../configuracion-entrega/configuracion-entrega.page';

declare var _: any;

interface DataInterface {
    WarehouseName: String;
    codigo: String;
    WarehouseCode: String;
}

@Component({
    selector: 'app-pedido',
    templateUrl: './pedido.page.html',
    styleUrls: ['./pedido.page.scss'],
})
export class PedidoPage implements OnInit {
    public bonificacion_regalos = new bonificacion_regalos();
    public bonificacion_compras = new bonificacion_compras();
    public Bonificacion_ca = new Bonificacion_ca();
    public descuentoDelTotal: number;
    public cli: any;
    public descuentoDelTotalPorcentual: any;
    public tipo: any;
    public titulo: string;
    public estadoTotal: boolean;
    public headerPedido: any;
    public detallePedido: any;
    public indicador_impuesto: string;
    public idPedido: any;
    public items: any;
    public sucursales: any;
    public idUser: number;
    public idDocument: number;
    public selectSucursal: any;
    public fechaCurrent: number;
    public timenetrega: any;
    public pedidoData: any;
    public almacenarr: DataInterface = <DataInterface>{};
    public listAlmacen: any[];
    public dataexport: any;
    public total: any;
    public estadoViewPedido: number;
    public Currency: string;
    public fechaCurrenttext: string;
    public codDocumento: any;
    public minPiker: string;
    public isenabled: boolean;
    public isEdit: boolean;
    public activoInactivo: string;
    public textFecha: string;
    public tipoDoc: any;
    public dataArr: any;
    public litPrecios: any;
    public litPreciosSelect: any;
    public CardName: string;
    public CardCode: string;
    public Address: string;
    public Moneda: string;
    public cantidadItems: boolean;
    public cantidadItemsTexto: boolean;
    public estadoFechaentrega: boolean;
    public estadoBtnAdd: boolean;
    public activoOptionsData: string;
    public descuentoporsentaje: number;
    public controlStatus: number;
    public xmtd: string;
    public iniNum: any;
    public seriesSlide: any;
    public documentosdata: Documentos;
    public clone: any;
    public clonado: any;
    public userdata: any;
    public estado: boolean;
    public tipoFactura: number;
    public usaFacturaReserva: string;
    public localizacion: number;
    public grupoproductostext: string;
    public grupoproductoscode: any;
    public bonificaciontext: string;
    public bonificacioncode: any;
    public tarjetaBonificacion = new bonificacion_regalos();
    public tarjetaBonificacionCabecera = new Bonificacion_ca();
    public respboni: any;
    public databoniexpo: any;
    public auxdescuentocabecera: any;
    public totalnetox: any = 0;
    public totaldescuentox: any = 0;
    public auxBonificacion = 0;
    public datos_validar: any;
    public accion = 0;
    public auxListaprecio: number;
    public btnSave: boolean = false;
    public almacenes: any;
    public vercontrato:any;
    public vernotaventa: any;
    public localizacion_calculo: any;
    newBonificacionesUsados: any;
    newBoniComprasData: any;
    newBoniRegalosData: any;
    newBoniValidosData: any;
    productosAregalar: any;
    cardCode: any;
    hiden: boolean = true;
    descuentoGlobal: any = 0;
    clonadoEstado: boolean = false;
    cambioAlmacen: boolean = false;
    SWaddProducto: boolean = false;

    usaIce: boolean;
    usaGrupoProductos: boolean;
    swDescuentoCabezera: boolean = false;
    // swDescuentoMonetario: boolean = false;

    territorioCliente = ''
    private reportService: any;
    constructor(private selector: WheelSelector, private network: Network, private dataService: DataService, private navCrl: NavController, private _location: Location,
        public popoverController: PopoverController, private spinnerDialog: SpinnerDialog, private platform: Platform, private routerOutlet: IonRouterOutlet,
        private activatedRoute: ActivatedRoute, private dialogs: Dialogs, private toast: Toast, private _reportService: ReportService,
        public modalController: ModalController, private configService: ConfigService, public alertController: AlertController, private alertCtrl: AlertController, public pagosService: PagosService, private bonificacionesService: BonificacionesService,
        public loadinCtrl:LoadingController,public geolocation: Geolocation,private iab: InAppBrowser, private _reportStyleUno:ReportStyleUnoService
    )
    {        
        this.documentosdata = new Documentos();
        this.headerPedido = [];
        this.iniNum = [];
        this.detallePedido = [];
        this.items = [];
        this.userdata = [];
        this.selectSucursal = [];
        this.idDocument = 0;
        this.controlStatus = 0;
        this.sucursales = [];
        this.dataexport = [];
        this.litPrecios = [];
        this.pedidoData = [];
        this.dataArr = [];
        this.clone = [];
        this.clonado = '0';
        this.litPreciosSelect = [];
        this.respboni = [];
        this.indicador_impuesto = 'ICE';
        this.tipoFactura = 100;
        this.usaFacturaReserva= '0';
        this.total = 0;
        this.descuentoDelTotal = 0;
        this.descuentoDelTotalPorcentual = 0;
        this.estadoViewPedido = 1;
        this.fechaCurrent = 0;
        this.timenetrega = '';
        this.Currency = '';
        this.textFecha = '';
        this.fechaCurrenttext = '';
        this.codDocumento = '';
        this.activoInactivo = '';
        this.estadoTotal = false;
        this.isenabled = false;
        this.cantidadItems = false;
        this.cantidadItemsTexto = false;
        this.CardName = '';
        this.CardCode = '';
        this.Address = '';
        this.Moneda = '';
        this.isEdit = false;
        this.estadoFechaentrega = true;
        this.estadoBtnAdd = true;
        this.activoOptionsData = '';
        this.cli = 0;
        this.descuentoporsentaje = 0;
        this.estado = false;
        this.seriesSlide = [];
        this.databoniexpo = [];
        this.localizacion = 1;
        this.grupoproductostext = '';
        this.grupoproductoscode = '';
        this.bonificaciontext = 'SELECCIONAR BONIFICACIÓN';
        this.bonificacioncode = '';
        this.auxdescuentocabecera = 0;
        this.auxListaprecio = 0;
        this.usaIce = true;
        this.usaGrupoProductos = true;
        this.datos_validar = [];
        this.vernotaventa = 0;
    }

    async valkidBonificacionesCompras() {
        console.log("valkidBonificacionesCompras() ");
        this.newBonificacionesUsados = await this.Bonificacion_ca.getCompraUsadosAgrupadoDoc(this.territorioCliente);
        console.log(" this.newBonificacionesUsados  ", this.newBonificacionesUsados);
        if (this.newBonificacionesUsados.length == 0) {
            this.bonificaciontext = "";
        } else {
            this.bonificaciontext = 'SELECCIONAR BONIFICACIÓN';
        }
    }    

    public async ngOnInit() {
        this.valkidBonificacionesCompras();
        let servi = await this.configService.getSession();
        switch (parseInt(servi[0].localizacion)) {
            case (1):
                this.localizacion_calculo = new Bolivia();
                break;
            case (2):
                this.localizacion_calculo = new Companex();
                break;
            case (3):
                this.localizacion_calculo = new Paraguay();
                break;
            case (4):
                this.localizacion_calculo = new Chile();
                break;
        }

        const datasession: any = await this.configService.getSession();

        if(datasession[0].indicador_impuesto){
            this.indicador_impuesto = datasession[0].indicador_impuesto;
        }else{
            this.indicador_impuesto = 'ICE';
        }

        this.almacenes = datasession;
        this.userdata = await this.configService.getSession();
        console.log("  this.userdata ", this.userdata);

        let styleLayaut = this.userdata[0].layautConfig ? this.userdata[0].layautConfig : null;
        console.log("  this.userdata 2 ", styleLayaut);
        
        switch (Number(styleLayaut))
        {
            case 0:
                this.reportService = this._reportService;
                break;
            case 1:
                console.log("  this.userdata 3 ", styleLayaut);
                this.reportService = this._reportStyleUno;
                break;
            case 2:            
                break;
            default:
                this.reportService = this._reportService;
                break;
        }

        this.usaFacturaReserva = this.userdata[0].usaFacturaReserva;
        console.log("this.usaFacturaReserva",this.usaFacturaReserva);
        
        this.idUser = datasession[0].idUsuario;
        this.vercontrato = datasession[0].ctrl_contrato;
        if(datasession[0].ver_nota_venta){
            this.vernotaventa = datasession[0].ver_nota_venta;
        }else{
            this.vernotaventa = 0;
        }

        console.log("CONTRATO",this.vercontrato)
        this.litPrecios = datasession[0].listaprecios;
        this.tipoDoc = await this.configService.getTipo();
        this.minPiker = this.documentosdata.getFechaPicker();
        this.tipo = this.activatedRoute.snapshot.paramMap.get('id');
        this.xmtd = this.activatedRoute.snapshot.paramMap.get('tp');
        this.cli = this.activatedRoute.snapshot.paramMap.get('cli');

        console.log("DEVD  this.tipo  ", this.tipo);
        console.log("DEVD this.xmtd  ", this.xmtd);
        console.log("DEVD  this.cli  ", this.cli);
        console.log("DEVD this.tipoDoc ", this.tipoDoc);
        console.log("ES CLON ", GlobalConstants.Clon);
        console.log("ES CLON ", GlobalConstants.tipeDoc);
        console.log("DATOS", JSON.stringify(GlobalConstants.CabeceraDoc));
        console.log("Detalle", JSON.stringify(GlobalConstants.DetalleDoc));

        if (GlobalConstants.tipeDoc != 'N' && GlobalConstants.Clon == 0) {
            console.log('PASO');
            let detalles = new Detalle();
            GlobalConstants.CabeceraDoc[0] = await this.documentosdata.findexe(this.tipo);
            let det: any = await detalles.showTable(this.tipo);
            let det2: any = await detalles.showTable2(this.tipo);
            
            for (let x of det) {
                GlobalConstants.DetalleDoc.push(x);
            }
        }

        console.log("DATOS", JSON.stringify(GlobalConstants.CabeceraDoc));
        console.log("Detalle", JSON.stringify(GlobalConstants.DetalleDoc));

        if (this.cli == 0) {
            console.log("DEVD logica de cliente normal");
        } else {
            console.log("DEVD logica de cliente marker");
            // await this.simularDismissModalClient();
        }

        this.Address = 'null';
        this.localizacion = parseInt(datasession[0].localizacion);
        
        if (datasession[0].ctrl_ice == 0) {
            this.usaIce = false;
        }
        if (datasession[0].ctrl_grp_prods == 0) {
            this.usaGrupoProductos = false;
        }

        this.iniciando(true);

        this.platform.backButton.subscribeWithPriority(10, async (processNextHandler) => {
            let ruta = '/pedido/' + this.codDocumento + '/' + this.tipoDoc + '/0';
            let rc = await this._location.isCurrentPathEqualTo(ruta);
            if (rc == true) {
                this.backCancel();
            } else {
                this.navCrl.pop();
            }
        });
    }

    public async web(){
        let conarr: any = await this.configService.getSession();
        let usuario = conarr;
        console.log("DATOS DEL PEDIDO",this.pedidoData);
        if (this.tipoDoc == 'DFA') {
            if (usuario[0].uso_fex == '1') {
                console.log("fex_offline", usuario[0].docificacion[0].fex_offline)
                if (usuario[0].docificacion[0].fex_offline == '0') {
                    const options: InAppBrowserOptions = {
                        location: 'no',
                        clearcache: 'yes',
                        zoom: 'yes',
                        toolbar: 'yes',
                        closebuttoncaption: 'close'
                    };                    

                    let cliente: any = new Clientes();
                    let dataCliente: any = await cliente.find(this.pedidoData.CardCode);

                    let dataext2: any = {
                        "iddoc": this.codDocumento,
                        "nit": this.pedidoData.U_4NIT,
                        "accion": 1
                    };

                    let xData: any;

                    try {
                        xData = await this.dataService.servisReportConsultaCufPost(dataext2, 3);
                        let xJson = JSON.parse(xData.data);
                        console.log("xJson.respuesta servisReportConsultaCufPost..", xJson.respuesta[0]);
                        dataCliente[0].U_EXX_FE_Cuf = xJson.respuesta[0].U_EXX_FE_Cuf;
                        dataCliente[0].U_EXX_FENUM = xJson.respuesta[0].U_LB_NumeroFactura;
                        console.log("todo bien hasta aqui datacliente", dataCliente);

                        if(dataCliente[0].U_EXX_FENUM == "" || dataCliente[0].U_EXX_FE_Cuf == "0"){
                            this.toast.show(`Factura aun no se envia a impuestos.`, '3000', 'bottom').subscribe(toast => {
                            });
                            return false;
                        }else{
                            let ruta2 = usuario[0].fex_url_siat + 'consulta/QR?nit=' + usuario[0].empresa[0].nit + '&cuf=' + dataCliente[0].U_EXX_FE_Cuf + '&numero=' + dataCliente[0].U_EXX_FENUM + '&t=1';
                            console.log(ruta2);
                                                    
                            const browser : any = this.iab.create(ruta2, '_system', options);      
                            browser.on('loadstop').subscribe(event => {
                                const navUrl = event.url;
                                if (navUrl.includes('success')) {
                                browser.close();
                                }
                            });
                        }
                    } catch (error) {
                        console.log("error al parsear el servicio de reporte", error);
                        this.spinnerDialog.hide();
                    }                    
                }
            }
        }
    }

    public cambiatipofactura(){
        if(this.tipoFactura == 0){
            this.tipoFactura = 1;
        }else{
            this.tipoFactura = 0;
        }
    }

    public async iniciando(tick: boolean) {
        console.log("DEVD iniciando() ");
        console.log(" DEVD this.dataexport ", this.dataexport);
        this.isenabled = true;
        this.tipoDocumento();
        console.log(" DEVD this.tipo ", this.tipo);

        if (this.tipo == 'null') {
            // try {
            this.accion = 0;
            let datax: any = await this.codGenx(this.tipoDoc);
            console.log("DEVD datax ", datax);
            this.codDocumento = await this.documentosdata.generaCod(this.tipoDoc, this.idUser, datax);
            console.log("DEVD this.codDocumento ", this.codDocumento);

            this.configService.setCodigo(this.codDocumento);
            this.timenetrega = Tiempo.fecha();
            this.dataexport.idPedido = '0';
            this.dataexport.fechaentrega = Tiempo.fecha();
            this.dataexport.tipoDoc = this.tipoDoc;
            this.isenabled = false;
            this.cantidadItems = true;
            this.cantidadItemsTexto = true;
            this.pedidoData.tipoestado = 'new';

            this.estadosDoumentos();
            this.getAlmacen(1);
            this.listarGrupoProductos(false);

            //await this.tarjetaBonificacion.cleanTable();
            if (tick != false)
                setTimeout(() => {
                    this.selectCliente();
                }, 200);
            //} catch (e) {
            //  console.log(e);
            //}
        } else {
            this.accion = 1;
            this.isenabled = true;
            (tick == false) ? this.controlStatus = 3 : this.controlStatus = 0;
            this.tipo = GlobalConstants.CabeceraDoc[0].cod;
            this.idPedido = this.tipo;
            this.dataexport.idPedido = this.tipo;

            this.pedidoData = GlobalConstants.CabeceraDoc[0];
            console.log(" this.pedidoData  ", this.pedidoData);
            this.timenetrega = this.pedidoData.fecharegistro;

            this.relacionclone();
            this.codDocumento = this.pedidoData.cod;
            this.pedidoData.Currency = this.pedidoData.currency;
            let sucursal: any = await this.documentosdata.selectDocumentSucursal(this.pedidoData.CardCode, this.pedidoData.idSucursalMobile);

            console.log("sucursal with lineNum ", sucursal);
            if (sucursal.length > 0) {
                this.selectSucursal = sucursal;
                console.log(" this.selectSucursal ", this.selectSucursal);

            }else {
                this.selectSucursal = [];
            }

            this.dataexport.sucursal = sucursal;
            this.dataexport.tipoDocx = this.pedidoData.Reserve;
            this.dataexport.tipoDoc = this.pedidoData.DocType;

            await this.dibujavista(this.pedidoData, false);
            await this.listarDetalle(tick);
            await this.estadosDoumentos();
            await this.getAlmacen(2);

            console.log("DEVD items existente ", this.items);
            if (this.items.length > 0) {
                this.SWaddProducto = true;
            }

            console.log("this.SWaddProducto cambiado ", this.SWaddProducto);
            if (this.pedidoData.origen == 'outer') {
                this.pedidoData.grupoproductoscode = this.items[0].grupoproductodocificacion;
                this.listarGrupoProductos(this.pedidoData.grupoproductoscode);
                console.log(" this.pedidoData  ", this.pedidoData);
            }

            let dosificacionproductos = new Dosificacionproductos();
            let grpprod: any = await dosificacionproductos.find();
            console.log("DEVD dosificacionproductos ", grpprod);
            console.log("this.pedidoData.grupoproductoscode ", this.pedidoData.grupoproductoscode);

            let greaterTen2 = grpprod.filter(data => data.code == this.pedidoData.grupoproductoscode);
            console.log("DEVD greaterTen2 ", greaterTen2);
            if (greaterTen2.length > 0) {
                this.grupoproductostext = greaterTen2[0].nombre;
                this.grupoproductoscode = greaterTen2[0].code;
                this.dataexport.grupoproductoscode = greaterTen2[0].code;
            }
            this.isenabled = false;
        }

        GlobalConstants.Clon = 0;
    }

    public async listarPrecios(x: boolean) {
        console.log("listarPrecios() ", x);
        console.log("this.dataexport ", this.dataexport);
        console.log("this.pedidoData ", this.pedidoData);
        console.log("this.SWaddProducto ", this.SWaddProducto);
        console.log("this.litPrecios ", this.litPrecios);

        if (this.items.length > 0 || this.SWaddProducto) {
            this.toast.show(`No es posible selecionar otra lista de precios.`, '3000', 'bottom').subscribe(toast => {
            });
            return false;
        }

        if (this.userdata[0].config[0].ctrl_listaPrecios == '0' && this.accion == 0) {
            x = true;
        } else {
            x = false;
        }

        console.log("userdata", this.userdata[0]);        
        console.log("this.ctrl_listaPrecios", this.userdata[0].config[0].ctrl_listaPrecios);

        if (x == true) {
            if (this.userdata[0].config[0].controlarModificarListaPrecios == '0') {
                this.toast.show(`No está permitido para modificar lista de precios.`, '2500', 'center').subscribe(toast => {
                });
                return false;
            }

            if (this.litPrecios.length > 0) {
                let arr = [];
                for (let x of this.litPrecios)
                    arr.push({ description: x.PriceListName });
                this.selector.show({
                    title: "SELECCIONA LA LISTA DE PRECIOS.",
                    items: [arr],
                    positiveButtonText: "SELECCIONAR",
                    negativeButtonText: "CANCELAR"
                }).then((result: any) => {
                    this.litPreciosSelect = this.litPrecios[result[0].index];
                    this.dataexport.listaPrecio = this.litPrecios[result[0].index];
                }, (err: any) => {
                    console.log(err);
                });
            }
        } else {
            console.log("else x ");
            console.log("this.litPrecios ", this.litPrecios);
            console.log("this.pedidoData ", this.pedidoData);
            try {
                let arr = [];
                for (let x of this.litPrecios) {
                    if (x.PriceListNo == this.dataexport.cliente.PriceListNum) {
                        arr.push(x);
                        break;
                    }
                }

                console.log("precios del cliente ", arr);
                if (arr.length > 0) {
                    this.litPreciosSelect = arr[0];
                    this.dataexport.listaPrecio = arr[0];
                } else {
                    console.log("this.pedidoData.origen  ", this.pedidoData.origen);
                    console.log("this.pedidoData.clone.length  ", this.pedidoData.clone.length);

                    if (this.pedidoData.origen == 'inner' && this.pedidoData.clone.length < 3) {
                        const alert = await this.alertController.create({
                            cssClass: "my-custom-class",
                            header: "Lista de precio no encontrada ",
                            message: "La lista de precios del cliente no coincide con los del vendedor actual<strong></strong>...",
                            buttons: [
                                {
                                    text: "Cancelar",
                                    role: "cancel",
                                    cssClass: "secondary",
                                    handler: (blah) => {
                                        console.log("Confirm Cancel: blah");
                                        return false;
                                    },
                                },
                                {
                                    text: "CONFIRMAR",
                                    handler: () => {
                                        console.log("Confirm Okay");
                                        //this.listarPrecios(true);
                                        return false;
                                    },
                                },
                            ],
                        });

                        await alert.present();
                    } else {
                        console.log("es outer lista de precios ");
                        console.log("los items son ", this.items);
                        if (this.pedidoData.origen == 'inner' && this.pedidoData.tipoestado == 'new' && (this.pedidoData.DocType == "DOP" || this.pedidoData.DocType == "DOF")) {
                            this.toast.show(`La lista de precios del cliente no coincide con los del vendedor actual, necesita seleccionar una lista de precios para adicionar items.`, '14000', 'center').subscribe(toast => {
                            });
                            this.SWaddProducto = false;
                            return false;
                            //this.listarPrecios(true);
                        }else{
                            for (let x of this.litPrecios) {
                                if (x.PriceListNo == this.pedidoData.PriceListNum) {
                                    arr.push(x);
                                    break;
                                }
                            }
                            if (arr.length > 0) {
                                this.litPreciosSelect = arr[0];
                                this.dataexport.listaPrecio = arr[0];
                            }else{
                                for (let x of this.litPrecios) {
                                    arr.push(x);
                                }
                                this.litPreciosSelect = arr[0];
                                this.dataexport.listaPrecio = arr[0];
                            }
                        }
                    }
                }
                console.log("this.hiden ", this.hiden);
            } catch (e) {
                console.log(e);
            }
        }
    }

    private async codGenx(tipoDoc: any) {
        this.iniNum = await this.configService.getNumeracion();
        console.log("DEVD  this.iniNum  ", this.iniNum);

        let numerox: number;
        if(this.iniNum == null){
            this.toast.show(`No existe numeracion para el tipo de documento`, '4000', 'top').subscribe(toast => { });
        }else{
            switch (tipoDoc) {
                case ('DFA'):
                    numerox = this.iniNum.numdfa += 1;
                    break;
                case ('DOP'):
                    numerox = this.iniNum.numdop += 1;
                    break;
                case ('DOE'):
                    numerox = this.iniNum.numdoe += 1;
                    break;
                case ('DOF'):
                    numerox = this.iniNum.numdof += 1;
                    break;
            }
        }        

        // console.log("se consulta si ya existe un documemnto con esa numeracion ");
        // let docm = new Documentos;
        // let aux: any = await docm.numeraldoc(tipoDoc, this.idUser, numerox);
        // console.log("numero ", aux);

        // if (numerox < aux) {
        //     numerox = aux;
        //     let obx: any = {
        //         tipoDoc: tipoDoc,
        //         idUser: this.idUser,
        //         numerox: (numerox - 1)
        //     };
        //     console.log("numero es menor se actualizara la numeracion");

        //     if (this.network.type != 'none') {
        //         let resp: any = await this.dataService.NumeracionAction(obx);
        //     } else {
        //         this.toast.show(`Conexion limitada o inexistente`, '4000', 'top').subscribe(toast => { });
        //     }


        // }
        return numerox;
    }

    public async eliminadoc() {
        let detalles = new Detalle();
        console.log("this.items ", this.items);
        for (let item of this.items) {
            await detalles.eliminar(item.id, this.dataexport.tipoDoc, this.dataexport.tipoDocx);
        }
        this.documentosdata.deletedoc(this.pedidoData.id);
        await detalles.eliminargrupo(this.pedidoData.cod);
        return true;
    }

    public async backCancel() {
        if (GlobalConstants.DetalleDoc.length > 0 && GlobalConstants.CabeceraDoc[0].tipoestado == 'new') {
            this.dialogs.confirm('Al salir se borrarán todos los datos del documento ¿Desea Salir?', "Xmobile.", ["SI", "NO"]).then(async (data) => {
                switch (data) {
                    case (1):
                        this.navCrl.pop();
                        break;
                    case (2):
                        return false;
                }
            }).catch(async (e) => {
                return false;
            });
        } else {
            this.navCrl.pop();
        }       
    }

    public async saveDocument(datazz: any) {
        console.log("CONSOLA: INICIA FUNCION saveDocument 788");

        if (this.idPedido != 0) {
            console.log("Inicia");
            this.spinnerDialog.show('', 'Cargando...', true);
            this.controlStatus = 0;
            let clientes = new Clientes();
            let dataDoc: any;
            let session: any;
            let greaterTen2x: any;
            let docificacionx: any;

            console.log("CONSOLA: DATOS DEL DOCUMENTO 800",JSON.stringify(GlobalConstants.CabeceraDoc));

            let xData: any;
            let validador = 0;
            
            console.log("CONSOLA: VALIDA SI EL TIPO DE DOCUMENTO ES DFA O DOE 805");

            if(GlobalConstants.CabeceraDoc[0].DocType == 'DFA' || GlobalConstants.CabeceraDoc[0].DocType == 'DOE'){
                this.spinnerDialog.hide();
                this.spinnerDialog.show('', 'Validando Stock...', true);
                try {
                    if (this.network.type != 'none') {
                        if(this.tipoFactura == 0){
                            console.log("CONSOLA: CONSULTA EN ENDPOINT QUE VALIDA EL STOCK 813");
                            xData = await this.dataService.stocklistaitems(this.datos_validar);
                            if(xData.respuesta != '0'){
                                validador = 1;
                            }
                        }
                    }else{
                        this.toast.show(`Sin conexión, no se pudo validar stock`, '5000', 'center').subscribe(toast => {
                        });
                    }
                } catch (error) {
                    console.log(error);
                    this.spinnerDialog.hide();
                    let mensaje = xData.respuesta.replace(/pxp/g,"\n");
                    this.dialogs.confirm(mensaje, "Xmobile.", ["OK"]).then(async (data) => {
                    }).catch(async (e) => {
                        return false;
                    });

                }
            }else{
                validador = 0;
            }
            
            if(validador == 1){
                this.spinnerDialog.hide();
                let mensaje = xData.respuesta.replace(/pxp/g,"\n");
                this.dialogs.confirm(mensaje, "Xmobile.", ["OK"]).then(async (data) => {
                }).catch(async (e) => {
                    return false;
                });
            }else{
                try {
                    this.spinnerDialog.hide();
                    this.spinnerDialog.show(null, 'Enviando a SAP...', true);

                    dataDoc = GlobalConstants.CabeceraDoc[0];

                    session = await this.configService.getSession();
                    let clientearr = await clientes.find(dataDoc.CardCode);

                    greaterTen2x = session[0].docificacion;

                    for (let doci of greaterTen2x) {
                        if (doci.U_FechaLimiteEmision >= moment().format('YYYY-MM-DD')) {
                            docificacionx = doci;
                        }
                    }

                    if (typeof docificacionx == 'undefined') {
                        docificacionx = {
                            U_NumeroAutorizacion: '0',
                            U_NumeroSiguiente: '0',
                            U_LlaveDosificacion: ''
                        };
                    }

                    let numeroAuthorization = docificacionx.U_NumeroAutorizacion;
                    let fex_sucursal = docificacionx.fex_sucursal;
                    let fex_modalidad = docificacionx.fex_modalidad;
                    let fex_tipo_emision = docificacionx.fex_tipo_emision;
                    let fex_codigoDocumentoFiscal = docificacionx.fex_codigoDocumentoFiscal;
                    let fex_tipoDocumentoSector = docificacionx.fex_tipoDocumentoSector;
                    let fex_puntoventa = docificacionx.fex_puntoventa;

                    if (numeroAuthorization == null && dataDoc.DocType == 'DFA') {
                        this.toast.show(`No se encontro Numero de Autorizacion Sincronice el Middelware`, '3000', 'center').subscribe(toast => {
                        });
                        this.spinnerDialog.hide();
                        return false;
                    }
                  
                    let numeofactura = parseInt(docificacionx.U_NumeroSiguiente);                    
                    let ci = datazz.nit;
                    let fechaTansaccion = dataDoc.fechasend;
                    let cantidadTransaccion = dataDoc.DocumentTotalPay;
                    let docificacion = docificacionx.U_LlaveDosificacion;
                    let resp: string = '';
                    let fecharegistrocuf = dataDoc.fecharegistro;
                    let DocumentTotalPay = 0;
                    let doctotal = 0;

                    console.log("CONSOLA: LLAMA FUNCION sumaTotalLocal 898");
                    let totalx: any = await this.localizacion_calculo.sumaTotalLocal(GlobalConstants.CabeceraDoc, GlobalConstants.DetalleDoc);
                    GlobalConstants.CabeceraDoc[0].DocTotal = totalx.doctotal;
                    GlobalConstants.CabeceraDoc[0].DocumentTotalPay =totalx.total;
                    
                    console.log("CONSOLA: VALIDA SI ES DFA 903");
                    if (dataDoc.DocType == 'DFA') {
                        let fechaxxx = fechaTansaccion.replace('/', '').replace('/', '').replace('-', '').replace('-', '');
                        fecharegistrocuf = fecharegistrocuf.replace('/', '').replace('/', '').replace('-', '').replace('-', '').replace(':', '').replace(':', '').replace(' ', '').replace(' ', '');

                        let cod = new Codigocontrol();
                        console.log("CONSOLA: VALIDA SI uso_fex ES IGUAL A 1 909");
                        if (session[0].uso_fex == '1') {
                            console.log("CONSOLA: VALIDA SI fex_offline ES IGUAL A 1 911");
                            if (docificacionx.fex_offline == '1') {

                                console.log("CONSOLA: LLAMA FUNCION cod.calcularCUF 914");
                                resp = cod.calcularCUF(ci.toString(), fecharegistrocuf, fex_sucursal, fex_modalidad, fex_tipo_emision, fex_codigoDocumentoFiscal, fex_tipoDocumentoSector, numeofactura.toString(), fex_puntoventa, numeroAuthorization).toString();
                            } else {
                                resp = '0';
                            }
                        } else {
                            console.log("CONSOLA: LLAMA FUNCION cod.generateExe 920");
                            resp = cod.generateExe(numeroAuthorization.toString(), numeofactura, ci, fechaxxx, cantidadTransaccion.toString(), docificacion.toString());
                        }

                        docificacionx.U_NumeroSiguiente = parseInt(docificacionx.U_NumeroSiguiente) + 1;

                        await clientes.updatebalancemas(DocumentTotalPay, dataDoc.CardCode);
                    }

                    let tik: number;
                    (datazz.reserva == true) ? tik = 1 : tik = 0;

                    console.log("CONSOLA: REALIZA ULTIMOS CAMBIOS EN EL OBJETO 933",JSON.stringify(GlobalConstants.CabeceraDoc));
                  
                    GlobalConstants.CabeceraDoc[0] = dataDoc;
                    GlobalConstants.CabeceraDoc[0].tipoestado = 'cerrado';
                    GlobalConstants.CabeceraDoc[0].U_LB_CodigoControl = resp;
                    GlobalConstants.CabeceraDoc[0].U_LB_NumeroFactura = numeofactura;
                    GlobalConstants.CabeceraDoc[0].U_LB_NumeroAutorizac = numeroAuthorization;
                    GlobalConstants.CabeceraDoc[0].Reserve = tik;
                    GlobalConstants.CabeceraDoc[0].U_4RAZON_SOCIAL = datazz.razonsocial;
                    GlobalConstants.CabeceraDoc[0].U_4NIT = datazz.nit;
                    GlobalConstants.CabeceraDoc[0].fechasend = datazz.plazospago;
                    GlobalConstants.CabeceraDoc[0].DocDueDate = datazz.plazospago;
                    GlobalConstants.CabeceraDoc[0].federalTaxId = datazz.nit;
                    GlobalConstants.CabeceraDoc[0].cardNameAux = datazz.razonsocial;
                    GlobalConstants.CabeceraDoc[0].U_LB_RazonSocial = datazz.razonsocial;
                    GlobalConstants.CabeceraDoc[0].comentario = datazz.comentario;
                    GlobalConstants.CabeceraDoc[0].cuenta = datazz.cuenta;
                    GlobalConstants.CabeceraDoc[0].estadosend = 1;
                    GlobalConstants.CabeceraDoc[0].PayTermsGrpCode = datazz.condicion;
                    GlobalConstants.CabeceraDoc[0].codeConsolidador = datazz.carcodeConso;
                    GlobalConstants.CabeceraDoc[0].Fex_documento = datazz.documentofex;
                    GlobalConstants.CabeceraDoc[0].Fex_tipodocumento = datazz.tipodocumento;
                    GlobalConstants.CabeceraDoc[0].DocDate = datazz.plazospago;
                    GlobalConstants.CabeceraDoc[0].equipoId = this.userdata[0].equipoId;
                    GlobalConstants.CabeceraDoc[0].camposusuario = datazz.camposusuario;

                    if (datazz.transportista) {
                        GlobalConstants.CabeceraDoc[0].transportista = datazz.transportista;
                    }
                    
                    console.log("CONSOLA: ENVIA OBJETO A SAP 960",JSON.stringify(GlobalConstants.CabeceraDoc));
                    let respuesta = await this.dataService.exportDocumentoObjeto();
                    console.log("CONSOLA: RESPUESTA DEL MIDD 962",respuesta);

                    if (respuesta.respuesta.estadoDoc == 0 && respuesta.respuesta.numeracionDoc == 0) {

                        GlobalConstants.CabeceraDoc[0].estado = '1';
                        GlobalConstants.CabeceraDoc[0].estadosend = '1';

                        await this.insertobjetoDoc(respuesta);
                        this.spinnerDialog.hide();
                        this.toast.show('Sin conexion envie el documento desde la sincronización', '3000', 'center').subscribe(toast => { });
                        if (GlobalConstants.CabeceraDoc[0].DocType == 'DFA' && GlobalConstants.CabeceraDoc[0].PayTermsGrpCode == -1) {
                            await this.pagosService.payCreate2(GlobalConstants.CabeceraDoc[0].pagos, respuesta);
                            try {

                                if (GlobalConstants.CabeceraDoc[0].U_MontoCampania > 0) {
                                    let documentos = new Documentos();
                                    let modelPromociones = new promocionaes();
                                    //await documentos.descuentoICE(GlobalConstants.CabeceraDoc[0].U_MontoCampania, 0, GlobalConstants.CabeceraDoc[0].cod, false);
                                    await modelPromociones.insertUse(GlobalConstants.Campaña, GlobalConstants.CabeceraDoc[0].cod, GlobalConstants.CabeceraDoc[0].U_MontoCampania);

                                    //console.log("promociones usadas ", await modelPromociones.showAllUses());
                                }

                                console.log("datafactura realizada", GlobalConstants.CabeceraDoc[0]);
                                let pago: IPagos = { ...GlobalConstants.CabeceraDoc[0].pagos }
                                let clientes = new Clientes();
                                let dataClient: any = await clientes.find(GlobalConstants.CabeceraDoc[0].CardCode);

                                console.log("data cliente", dataClient);
                                let dCliente: ICliente[] = [...dataClient];
                                let dataPagoPdf: IDataPagoPdf = {
                                    iddocumento: pago.nro_recibo,
                                    fechahora: Tiempo.fecha() + " " + Tiempo.hora(),
                                    tipodocumento: "DPG",
                                    dataPago: pago,
                                    equipo: this.userdata[0].equipoId,
                                    usuario: this.userdata[0].idUsuario,
                                    "dataCliente": dCliente[0]
                                }
                                this.spinnerDialog.hide();
                                let rex: any = await this.reportService.generareciboV2(dataPagoPdf, this.userdata);
                                if (rex) this.reportService.generateEXE(pago.nro_recibo);
                            } catch (error) {
                                this.spinnerDialog.hide();
                                this.toast.show("Error al generar el reporte de pago.", '3000', 'center').subscribe(toast => { });
                            }
                        }
                        this.navCrl.pop();
                    } else {
                        GlobalConstants.CabeceraDoc[0].estado = respuesta.respuesta.estadoDoc;
                        GlobalConstants.CabeceraDoc[0].estadosend = respuesta.respuesta.estadoDoc;
                        if (respuesta.respuesta.estadoDoc == 3) {
                            await this.insertobjetoDoc(respuesta);
                            this.spinnerDialog.hide();
                            this.toast.show(respuesta.respuesta.mensajeDoc, '3000', 'center').subscribe(toast => { });

                            if (GlobalConstants.CabeceraDoc[0].DocType == 'DFA' && GlobalConstants.CabeceraDoc[0].PayTermsGrpCode == -1) {

                                if (GlobalConstants.CabeceraDoc[0].U_MontoCampania > 0) {
                                    let documentos = new Documentos();
                                    let modelPromociones = new promocionaes();

                                    //await documentos.descuentoICE(GlobalConstants.CabeceraDoc[0].U_MontoCampania, 0, GlobalConstants.CabeceraDoc[0].cod, false);
                                    await modelPromociones.insertUse(GlobalConstants.Campaña, GlobalConstants.CabeceraDoc[0].cod, GlobalConstants.CabeceraDoc[0].U_MontoCampania);

                                    //console.log("promociones usadas ", await modelPromociones.showAllUses());
                                }

                                await this.pagosService.payCreate2(GlobalConstants.CabeceraDoc[0].pagos, respuesta);
                                this.toast.show(respuesta.respuesta.mensajePago, '3000', 'center').subscribe(toast => { });
                                if (respuesta.respuesta.estadoPago == 3) {

                                    try {
                                        console.log("datafactura realizada", GlobalConstants.CabeceraDoc[0]);
                                        let pago: IPagos = { ...GlobalConstants.CabeceraDoc[0].pagos }
                                        let clientes = new Clientes();
                                        let dataClient: any = await clientes.find(GlobalConstants.CabeceraDoc[0].CardCode);

                                        console.log("data cliente", dataClient);
                                        let dCliente: ICliente[] = [...dataClient];
                                        let dataPagoPdf: IDataPagoPdf = {
                                            iddocumento: pago.nro_recibo,
                                            fechahora: Tiempo.fecha() + " " + Tiempo.hora(),
                                            tipodocumento: "DPG",
                                            dataPago: pago,
                                            equipo: this.userdata[0].equipoId,
                                            usuario: this.userdata[0].idUsuario,
                                            "dataCliente": dCliente[0]
                                        }
                                        this.spinnerDialog.hide();
                                        let rex: any = await this.reportService.generareciboV2(dataPagoPdf, this.userdata);
                                        if (rex) this.reportService.generateEXE(pago.nro_recibo);
                                    } catch (error) {
                                        this.spinnerDialog.hide();
                                        this.toast.show("Error al generar el reporte de pago.", '3000', 'center').subscribe(toast => { });
                                    }
                                }
                            }
                            this.navCrl.pop();
                        } else {                            
                            GlobalConstants.DetalleDoc = [];
                            let tipo_pago = GlobalConstants.CabeceraDoc[0].PayTermsGrpCode
                            GlobalConstants.CabeceraDoc = JSON.parse(GlobalConstants.auxiliarcloncabeceras);
                            GlobalConstants.CabeceraDoc[0].tipoestado = 'new';
                            let auxdetalle: any;
                            console.log("Detalle CLONADO RECUPERADO", JSON.stringify(GlobalConstants.auxiliarclondetalle));
                            auxdetalle = JSON.parse(GlobalConstants.auxiliarclondetalle);
                            console.log("log mau bef",auxdetalle);
                            if(GlobalConstants.CabeceraDoc[0].clone == 0 ){
                                for (let i = 0; i < auxdetalle.length; i++) {
                                    if (auxdetalle[i].codeBonificacionUse == 0) {
                                        GlobalConstants.DetalleDoc.push(auxdetalle[i]);
                                    }
                                }
                            }else{
                                if(GlobalConstants.CabeceraDoc[0].DocType == 'DFA'){
                                    for (let i = 0; i < auxdetalle.length; i++) {
                                        GlobalConstants.DetalleDoc.push(auxdetalle[i]);
                                    }
                                }else{
                                    for (let i = 0; i < auxdetalle.length; i++) {
                                        if (auxdetalle[i].codeBonificacionUse == 0) {
                                            GlobalConstants.DetalleDoc.push(auxdetalle[i]);
                                        }
                                    }
                                }
                            }
                            GlobalConstants.auxiliarclondetalle = '';
                            GlobalConstants.auxiliarcloncabeceras = '';

                            console.log("log mau bef 2",GlobalConstants.DetalleDoc);

                            if (respuesta.respuesta.estadoDoc == 0 && respuesta.respuesta.numeracionDoc > 0) {

                                this.pedidoData.tipoestado = 'new';
                                console.log("la numeracion anterior es", this.iniNum);
                                console.log("tipo de documento", GlobalConstants.CabeceraDoc[0].DocType);

                                switch (GlobalConstants.CabeceraDoc[0].DocType) {
                                    case ('DOF'): //oferta (DOF)
                                        this.iniNum.numdof = respuesta.respuesta.numeracionDoc;
                                        break;
                                    case ('DOP'): //pedido (DOP)
                                        this.iniNum.numdop = respuesta.respuesta.numeracionDoc;
                                        break;
                                    case ('DOFX'): //oferta (DOF) duplicacioan
                                        this.iniNum.numdof = respuesta.respuesta.numeracionDoc;
                                        break;
                                    case ('DOPX'): //pedido (DOP) duplicacioan
                                        this.iniNum.numdop = respuesta.respuesta.numeracionDoc;
                                        break;
                                    case ('DFA'): //factura (DFA) deudor
                                        this.iniNum.numdfa = respuesta.respuesta.numeracionDoc;
                                        break;
                                    case ('DFAX'): //factura (DFA) reserva
                                        this.iniNum.numdfa = respuesta.respuesta.numeracionDoc
                                        break;
                                    case ('DOE'): //entrega (DOE)
                                        this.iniNum.numdoe = respuesta.respuesta.numeracionDoc;
                                        break;
                                }
                                
                                console.log("nueva numeracion", this.iniNum);

                                await this.configService.setNumeracion(this.iniNum);
                                let numero = respuesta.respuesta.numeracionDoc
                                let documentid = await this.documentosdata.generaCod(this.tipoDoc, this.idUser, numero);
                                console.log("nueva documentid", documentid);
                                
                                for await (let data of GlobalConstants.DetalleDoc) {
                                    data.idDocumento = documentid;
                                }
                            
                                await this.limpiarBonificacion(1);

                                GlobalConstants.CabeceraDoc[0].cod = documentid;
                                GlobalConstants.CabeceraDoc[0].tipoestado = 'new';
                                this.spinnerDialog.hide();
                                console.log("log mau bef 3",GlobalConstants.DetalleDoc);

                                this.dialogs.confirm('Se actualizo la numeracion, envie el documento nuevamente', "Transacción fallida.", ["Ok"]).then(async (data) => {
                                    this.iniciando(false);
                                }).catch(async (e) => { });

                            } else {
                                this.pedidoData.tipoestado = 'new';
                                if (GlobalConstants.CabeceraDoc[0].DocType == 'DFA' && tipo_pago == -1) {
                                    console.log("es factura y contado");
                                    if (respuesta.respuesta.estadoPago == 0 && respuesta.respuesta.numeracionPago > 0) {

                                        console.log("estado 0 y numeracion mayor a 0");
                                        GlobalConstants.numeropago = respuesta.respuesta.numeracionPago;
                                        console.log("nueva numeracion del pago", GlobalConstants.numeropago);
                                        this.dialogs.confirm('Se actualizo la numeracion del pago, envie el documento nuevamente', "Transacción fallida.", ["Ok"]).then(async (data) => {
                                            this.iniciando(false);
                                        }).catch(async (e) => { });
                                    } else {
                                        this.spinnerDialog.hide();
                                        if (respuesta.respuesta.estadoDoc == 2) {
                                            this.dialogs.confirm(respuesta.respuesta.mensajeDoc, "Transacción fallida.", ["Ok"]).then(async (data) => {
                                                this.iniciando(false);
                                            }).catch(async (e) => { });
                                        } else {
                                            this.dialogs.confirm(respuesta.respuesta.mensajePago, "Transacción fallida.", ["Ok"]).then(async (data) => {
                                                this.iniciando(false);
                                            }).catch(async (e) => { });
                                        }

                                    }
                                } else {

                                    this.spinnerDialog.hide();
                                    this.dialogs.confirm(respuesta.respuesta.mensajeDoc, "Transacción fallida.", ["Ok"]).then(async (data) => {
                                        this.iniciando(false);
                                    }).catch(async (e) => { });
                                }
                                
                                this.spinnerDialog.hide();
                            }
                        }
                    }

                    GlobalConstants.auxiliarcloncabeceras = '';
                    GlobalConstants.auxiliarclondetalle = '';

                    this.spinnerDialog.hide();

                } catch (e) {
                    console.log("ROMERO",e);
                    this.toast.show("Ha ocurrido un error intente nuevamente", '3000', 'center').subscribe(toast => { });

                    GlobalConstants.CabeceraDoc = JSON.parse(GlobalConstants.auxiliarcloncabeceras);
                    GlobalConstants.DetalleDoc = [];
                    GlobalConstants.CabeceraDoc = JSON.parse(GlobalConstants.auxiliarcloncabeceras);
                    GlobalConstants.CabeceraDoc[0].tipoestado = 'new';
                    this.pedidoData.tipoestado = 'new';
                    
                    let auxdetalle: any;
                    auxdetalle = JSON.parse(GlobalConstants.auxiliarclondetalle);
                    if(GlobalConstants.CabeceraDoc[0].clone == 0 ){
                        for (let i = 0; i < auxdetalle.length; i++) {
                            if (auxdetalle[i].codeBonificacionUse == 0) {
                                GlobalConstants.DetalleDoc.push(auxdetalle[i]);
                            }
                        }
                    }else{
                        if(GlobalConstants.CabeceraDoc[0].DocType == 'DFA'){
                            for (let i = 0; i < auxdetalle.length; i++) {
                                GlobalConstants.DetalleDoc.push(auxdetalle[i]);
                            }
                        }else{
                            for (let i = 0; i < auxdetalle.length; i++) {
                                if (auxdetalle[i].codeBonificacionUse == 0) {
                                    GlobalConstants.DetalleDoc.push(auxdetalle[i]);
                                }
                            }
                        }
                    }

                    GlobalConstants.auxiliarcloncabeceras = '';
                    GlobalConstants.auxiliarclondetalle = '';

                    this.iniciando(false);
                    this.spinnerDialog.hide();
                    console.log("ERRRRROXX DOC :::: ", e);
                }
            }
            console.log("CIERRA");
            GlobalConstants.auxiliarcloncabeceras = '';
            GlobalConstants.auxiliarclondetalle = '';
            this.spinnerDialog.hide();
        }
    }

    public async insertobjetoDoc(respuesta: any) {
        let session = await this.configService.getSession();
        this.network.onDisconnect().subscribe(() => {
            GlobalConstants.CabeceraDoc[0].TipoEnvioDoc = 'Offline'
        });

        await this.documentosdata.insertDoc(GlobalConstants.CabeceraDoc[0]);
        let detalle = new Detalle();
        await detalle.insertDoc(GlobalConstants.DetalleDoc);

        await this.codGenx(this.tipoDoc);
        //debugger;
        console.log("volviendo a setear los valores",this.iniNum);
        await this.configService.setNumeracion(this.iniNum);
        await this.configService.setSession(session);

        this.toast.show(respuesta.respuesta.mensajeDoc, '3000', 'center').subscribe(toast => { });
    }

    public async exportarDocument(idPedido) {
        if (this.network.type != 'none') {
            try {
                this.navCrl.pop();
                await this.dataService.exportDocumentosAsinc(idPedido);
            } catch (e) {
            }
            this.navCrl.pop();
        } else {
            try {
                this.navCrl.pop();
                console.log("llego aqui")
                await this.dataService.exportDocumentoslocal(idPedido);
            } catch (e) {
            }
            this.navCrl.pop();
        }
    }

    public async datosAdicionales() {
        console.log("CONSOLA: INICIA datosAdicionales 1329");

        this.btnSave = true;
        setTimeout(() => {
            this.btnSave = false;
        }, 6000);

        let clientes = new Clientes();
        let dataDoc: any = GlobalConstants.CabeceraDoc[0]

        console.log("CONSOLA: CONSULTA DATOS DE LA SESION 1339");
        let session: any = await this.configService.getSession();

        console.log("CONSOLA: CONSULTA DATOS DEL CLIENTE 1342");
        let clientearr: any = await clientes.find(dataDoc.CardCode);


        if (clientearr.length == 0) {
            this.toast.show(`No se encontró el cardCode del cliente.`, '4000', 'bottom').subscribe(toast => {
            });
        }

        this.cardCode = clientearr;
        
        console.log("CONSOLA: VALIDA SI ES DFA 1353");
        if (this.pedidoData.DocType == 'DFA') {

            let greaterTen2x: any = session[0].docificacion.filter(dx => {
                return moment().format('YYYY-MM-DD') <= dx.U_FechaLimiteEmision //dx.U_GrupoCliente == clientearr[0].cliente_std1 && dx.U_GrupoProducto == this.grupoproductoscode && 
            });

            console.log("CONSOLA: VALIDA LOS GRUPOS DE DOCIFICACION 1360");
            let docificacionx = greaterTen2x[0];
            if (typeof docificacionx == 'undefined') {
                this.toast.show(`Los grupos de dosificaciones no se encuentran disponibles para el cliente.`, '4000', 'bottom').subscribe(toast => {
                });
                return false;
            }

            let itemsAux: any = GlobalConstants.DetalleDoc;
          
            let productos = new Productos();

            console.log("CONSOLA: RECORRE LOS ITEMS 1372");
            for await (let item of itemsAux) {

                console.log("CONSOLA: CONSULTA LOS PRODUCTIOS productos.select 1375");
                let rxx: any = await productos.select(item.ItemCode);
                let ManageBatchNumbers = rxx.ManageBatchNumbers;
       
                console.log("CONSOLA: VALIDACION DE CONTROL DE LOTES 1379");
                if(session[0].ctrl_lote=='0' && ManageBatchNumbers == '1'){

                    if(item.lotes != undefined){
                        let cant = item.Quantity;
                        let can_lot = 0;

                        if(item.lotes.length > 0){
                            for await (let lot of item.lotes){
                                can_lot = can_lot+lot.num;
                            }
                            console.log("can_lot",can_lot);
                            console.log("can_lot",can_lot);

                            if(cant > can_lot){
                                this.dialogs.confirm(`La cantidad de `+item.Dscription+` es mayor a la suma de los lotes seleccionados.`, "Xmobile.", ["OK"]).then(async (data) => {
                                }).catch(async (e) => { });
                                return false;
                            }

                            if(cant < can_lot){
                                this.dialogs.confirm(`La cantidad de `+item.Dscription+` es menor a la suma de los lotes seleccionados.`, "Xmobile.", ["OK"]).then(async (data) => {
                                }).catch(async (e) => { });
                                return false;
                            }
                        }
                    }else{
                        this.dialogs.confirm(`No hay lotes seleccionados para el items  `+item.Dscription+`.`, "Xmobile.", ["OK"]).then(async (data) => {
                        }).catch(async (e) => { });
                        return false;
                    }
                }
            }
        }

        console.log("CONSOLA: VALIDA QUE EL TOTAL DE DESCUENTO NO SEA MAYOR AL NETO 1414");
        if (this.totalnetox <= this.descuentoDelTotal) {
            this.toast.show(`No Es posible guardar el documento revise los descuentos.`, '4000', 'bottom').subscribe(toast => {
            });
            return false;
        } else {
            console.log("CONSOLA: VALIDA SI ES CLON 1420");
            if (this.pedidoData.clone && this.pedidoData.clone.includes("DOE")) {
                this.gusrdadocx();
            } else {
                console.log("CONSOLA: VALIDA SI ES TIPO DOE O RESERVA IGUAL A 0 Y TIPO DFA 1424 ");
                if ((this.pedidoData.DocType == 'DOE') || (this.pedidoData.Reserve == 0 && this.pedidoData.DocType == 'DFA')) {
                    /**** LOTES ***/
                    let seriados = [];
                    let loteados = [];
                    for await (let itm of this.items) {
                        let productos = new Productos();
                        console.log("CONSOLA: CONSULTA EL ITEMS EN  productos.select 1431");
                        let rxx: any = await productos.select(itm.ItemCode);
                        if (rxx.ManageBatchNumbers == '1') {
                            let lotes = new Lotes();
                            console.log("CONSOLA: CONSULTA EL LOTES  EN lotes.selectSumLoteProducto 1435");
                            let lotex = await lotes.selectSumLoteProducto(itm.id);
                            try {
                                if (lotex[0].Stock < itm.Quantity || typeof lotex[0].Stock == 'undefined') {
                                    loteados.push({
                                        itemCode: itm.ItemCode,
                                        cantidad: itm.Quantity
                                    });
                                }
                            } catch (e) {
                                loteados.push({
                                    itemCode: itm.ItemCode,
                                    cantidad: itm.Quantity
                                });
                            }
                        }

                        if (rxx.ManageSerialNumbers == '1') {
                            let seriesproductos = new Seriesproductos();
                            console.log("CONSOLA: CONSULTA EL SERIE  EN seriesproductos.selectseriecount 1435");
                            let prox: any = await seriesproductos.selectseriecount(itm.id);
                            if (prox[0].totalx < itm.Quantity) {
                                seriados.push({
                                    itemCode: itm.ItemCode,
                                    cantidad: itm.Quantity
                                });
                            }
                        }
                    }
                    let aux_session: any = await this.configService.getSession();
                    if (aux_session[0].localizacion != 2) {
                        if (loteados.length > 0) {
                            this.toast.show(`El producto [${loteados[0].itemCode}] No tiene LOTES .`, '4000', 'bottom').subscribe(toast => {
                            });
                            return false;
                        }
                    }
                    if (seriados.length > 0) {
                        this.toast.show(`El producto [${seriados[0].itemCode}] No tiene SERIES .`, '4000', 'bottom').subscribe(toast => {
                        });
                        return false;
                    }

                    console.log("CONSOLA: LLAMA A LA FUNCION gusrdadocx 1478");
                    this.gusrdadocx();
                } else {
                    this.gusrdadocx();
                }
            }
        }
    }

    public async gusrdadocx() {
        console.log("CONSOLA: INICIA FUNCION guardadocx 1488");

        this.datos_validar = [];
        this.spinnerDialog.show();
        let itemsAux: any = GlobalConstants.DetalleDoc;

        for await (let item of itemsAux) {
            let cantidad = 0;
            if(item.BaseQty != 'NULL' && item.BaseQty > 0){
                cantidad = (item.BaseQty*item.Quantity);
            }else{
                cantidad =item.Quantity;
            }

            this.datos_validar.push({
                ItemCode: item.ItemCode,
                cantidad: cantidad,
                almacen: item.WhsCode,
                desc: item.Dscription
            });

        }

        GlobalConstants.auxiliarclondetalle = '';
        GlobalConstants.auxiliarcloncabeceras = '';
        GlobalConstants.CabeceraDoc[0].idSucursalMobile = this.selectSucursal.LineNum;        
        GlobalConstants.auxiliarclondetalle = JSON.stringify(GlobalConstants.DetalleDoc);
        GlobalConstants.auxiliarcloncabeceras = JSON.stringify(GlobalConstants.CabeceraDoc);

        console.log("CONSOLA: CLONA LOS DATOS DE CabeceraDoc 1519",JSON.stringify(GlobalConstants.auxiliarcloncabeceras));
        console.log("CONSOLA: CLONA LOS DATOS DE DetalleDoc 1520",JSON.stringify(GlobalConstants.auxiliarclondetalle));

        setTimeout(async () => {
            this.spinnerDialog.hide();
            this.territorioCliente = this.cardCode[0].rutaterritorisap;
            let detalles = new Detalle();
            let hayBonificacion: any = [];
            let itemsAux: any = GlobalConstants.DetalleDoc;
            let auxtotal = 0;

            for await (let item of itemsAux) {

                console.log("CONSOLA: LLAMA FUNCION localizacion_calculo.xneto 1533");
                let sumLineTotal: any = await this.localizacion_calculo.xneto(item);
                auxtotal = sumLineTotal.xneto;

                if (item.bonificacion > 0) {
                    hayBonificacion.push(item);
                }
            }

            GlobalConstants.CabeceraDoc[0].DocumentTotalPay = auxtotal;
            let documento = GlobalConstants.CabeceraDoc;

            console.log("CONSOLA: VALIDA SI ES CLON O SI mofificarBonificacion == SI 1546");

            if (documento[0].clone == 0 || localStorage.getItem('mofificarBonificacion') === "SI") {
                if (hayBonificacion.length > 0) {             
                    for await (let item of hayBonificacion) {
                        if (item.bonificacion == 1) {

                            console.log("CONSOLA: LLAMA A LA FUNCION detalles.updateBonificacionLineaReset 1554");

                            detalles.updateBonificacionLineaReset(item.id);
                        }
                        if (item.bonificacion > 2) {

                            console.log("CONSOLA: LLAMA A LA FUNCION detalles.updateDescuentoLineaReset 1560");

                            detalles.updateDescuentoLineaReset(this.idPedido, item.id, item.LineTotal);
                        }
                    }
                }

                console.log("CONSOLA: LLAMA A LA FUNCION validBonificacion 1568");
                await this.bonificacionesService.validBonificacion(this.items, this.cardCode);    
                this.newBonificacionesUsados = await this.Bonificacion_ca.getCompraUsadosAgrupadoDoc(this.territorioCliente);
                console.log("newBonificacionesUsados ", this.newBonificacionesUsados);
                let auxBoni = [];

                for await (let item of this.newBonificacionesUsados) {

                    if (item.totalCantidad >= item.cantidad_compra) {
                        auxBoni.push(item);
                        // return false;
                    }
                }

                this.newBonificacionesUsados = auxBoni;
                if (auxBoni.length > 0) {
                    let sms = "";
                    for await (let item of auxBoni) {
                        sms = sms + "<strong> - " + item.cabezera_tipo + " : </strong> " + item.nombre + " <br/> ";
                    }
                    const alert = await this.alertController.create({
                        cssClass: "my-custom-class",
                        header: "Guardar documento",
                        message: "Se encontraron Bonificaciones / Descuentos disponibles:<br/>   " + sms + "<strong> </strong>",
                        buttons: [
                            {
                                text: "Cancelar",
                                role: "cancel",
                                cssClass: "secondary",
                                handler: (blah) => {
                                    console.log("Confirm Cancel: blah");
                                },
                            },
                            {
                                text: "Aceptar",
                                //  handler: () => {
                                handler: async (data: any) => {
                                    console.log("CONSOLA: LLAMA A LA FUNCION quemarBonificaciones 1604");
                                    await this.quemarBonificaciones();
                                },
                            },
                        ],
                    });

                    await alert.present();
                    return false;
                }
            }
            else {
                console.log(" es clon ");
                console.log("hayBonificacion ", hayBonificacion)
                localStorage.setItem("esClon", "SI");
            }

            console.log("CONSOLA: LLAMA A LA FUNCION guardarPedidoBonificado 1604");
            this.guardarPedidoBonificado();
        }, 2000);
    }

    /**
     * VALIDAR SI UN CARRITO TIENE MISMOS PRODUCTOS QUE OTROS CARRITO
     */
    public async validBonificacionItems(carrito: any, bonoCompras: any, SWcantidad = 0) {
        console.log("validBonificacionItems()");
        console.log("carrito()", carrito);
        console.log("bonoCompras()", bonoCompras);
        let returnValid: any = true;

        for (let value of bonoCompras) {
            let filterItemValid = carrito.filter(x => x.ItemCode == value.code_compra);
            if (filterItemValid.length > 0) {
                console.log(" CUMPLE ", value.code_compra);

            } else {
                console.log("No CUMPLE ", value.code_compra);
                returnValid = false;
            }
        }
        return returnValid;
    }

    public async relacionclone() {
        console.log("relacionclone() ");
        this.clone = await this.documentosdata.findexe(this.pedidoData.clone);
        if (typeof this.clone !== 'undefined') {
            // await this.tarjetaBonificacion.cleanTable();
            this.clonado = this.clone.cod;
            this.clonadoEstado = true;
        }
    }

    public async estadosDoumentos() {
        switch (this.pedidoData.tipoestado) {
            case ('new'):
                this.estadoFechaentrega = false;
                this.estadoBtnAdd = false;
                this.activoOptionsData = '';
                this.estado = false;
                break;
            case ('activo'):
                this.estadoFechaentrega = false;
                this.estadoBtnAdd = false;
                this.activoOptionsData = 'disabled';
                break;
            case ('cerrado'):
                this.estadoFechaentrega = true;
                this.estadoBtnAdd = true;
                this.activoOptionsData = 'disabled';
                break;
            case ('anulado'):
                this.estadoFechaentrega = true;
                this.estadoBtnAdd = true;
                this.activoOptionsData = 'disabled';
                break;
        }
    }

    public tipoDocumento() {
        let tituloTipo = "";
        console.log("DEVD tipoDocumento ()  ", this.tipoDoc)
        switch (this.tipoDoc) {
            case ('DOF'):
                tituloTipo = 'OFERTA';
                this.textFecha = 'Fecha de validez';
                break;
            case ('DOP'):
                tituloTipo = 'PEDIDO';
                this.textFecha = 'Fecha del pedido';
                break;
            case ('DFA'):
                tituloTipo = 'FACTURA';
                this.textFecha = 'Fecha de la factura';
                break;
            case ('DOE'):
                tituloTipo = 'ENTREGAS';
                this.textFecha = 'Fecha de entrega';
                break;
        }

        this.titulo = tituloTipo;
        console.log("this.titulo  ", this.titulo);
    }

    public async dibujavista(data, tistap = true) {
        console.log("dibujavista ", data);

        if (data.tipoestado == 'new' || typeof data.tipoestado == 'undefined' || data.tipoestado == 'null'){
            this.estado = false;
        }else{
            this.estado = true;
        } 

        if (data.PriceListNum == "") data.PriceListNum = "1";
        if (data != null) {
            console.log("ROMERO ENTRA");
            this.Moneda = data.Currency;
            this.CardName = data.CardName;
            this.CardCode = data.CardCode;
            this.dataexport.cliente = data;
            this.dataexport.moneda = data.Currency;
            this.Currency = data.Currency;
            if(this.tipoFactura == 100){
                this.tipoFactura = 0;
            }            

            console.log(" this.Currency  ", this.Currency);
            if (typeof data.Address === 'undefined') {
                this.Address = 'null';
            } else {
                this.Address = data.Address;
            }
        }

        await this.listarSucursal();

        //if(!this.estado){
            this.listarPrecios(tistap);
        //}
        
    }

    public tipodocumentfactura() {
        //  this.dialogs.confirm("Selecciona el tipo de factura !", "Xmobile.", ["Reserva", "Deudor"]).then((data) => {
        //   if (data == 2 || data == 0) {
        this.tipoFactura = 0;
        this.toast.show(`Factura de DEUDOR.`, '3000', 'bottom').subscribe(toast => {
        });
        //   } else {
        //        this.tipoFactura = 1;
        //       this.toast.show(`Factura de RESERVA.`, '3000', 'bottom').subscribe(toast => {
        //      });
        //    }
        //  }).catch(() => {
        // })
    }

    public async selectCliente() {
        console.log("selectCliente()");
        console.log("tipoDoc()", this.tipoDoc);
        console.log("cli())", this.cli);
        let modelPromo = new promocionaes();
    
        if (this.cli == 0) {
            let mcliente: any = { component: ModalclientePage, componentProps: { tipo: this.tipoDoc } };
            let modalcliente: any = await this.modalController.create(mcliente);
            
            modalcliente.onDidDismiss().then(async (data: any) => {
                if (data.data != false && typeof data.data != "undefined") {
                    if (this.tipoDoc == 'DFA' || this.tipoDoc == 'DOE') this.tipodocumentfactura();
                    this.controlStatus = 1;
                    this.cantidadItems = true;
                    this.cantidadItemsTexto = true;
                    this.CardCode = data.CardCode;
                    console.log("cliente seleccionado ", data.data);

                    let detalledoc = new Detalle();
                    detalledoc.Deletedetalleinicial(this.codDocumento);

                    let dataPromociones: any = await modelPromo.findCurrentAll(data.data.CardCode);
                    let sms = ``;
                    console.log("dataPromociones ", dataPromociones)

                    for (let item of dataPromociones) {
                        console.log(item);
                        let smsAux = '';
                        if (item.cumpleMeta == 0) {
                            item.U_Saldo = 0;
                            smsAux = 'Valor a ganar';
                        } else {
                            smsAux = 'Valor ganado';
                        }
                        sms = sms + ` - <strong>${item.name}</strong> <br>
                        <strong> Meta : </strong>${Number(item.U_Meta).toFixed(2)} Bs.<br>
                        <strong>Acumulado : </strong> ${Number(item.U_Acumulado).toFixed(2)} Bs. <br>
                        <strong> ${smsAux}:</strong>  ${Number(item.U_ValorGanado).toFixed(2)} Bs. <br>
                        <strong>  Saldo :</strong>  ${Number(item.U_Saldo).toFixed(2)} Bs. <br>
                       `;
                    }

                    if (data.data.promo) {
                        if (data.data.promo == 1) {
                            const alert = await this.alertController.create({
                                cssClass: "my-custom-class",
                                header: "Campañas",
                                message: "" + sms,
                                buttons: [
                                    {
                                        text: "Entendido",
                                        handler: () => {
                                            console.log("Confirm Okay");
                                            //this.spinnerDialog.hide();
                                        },
                                    },
                                ],
                            });
                            await alert.present();
                        }
                    }

                    this.dibujavista(data.data, false);
                    this.cambiarMoneda(false);

                } else {
                    this.navCrl.pop();
                    this.toast.show(`Selecciona un cliente para continuar.`, '4000', 'bottom').subscribe(toast => {
                    });
                }
            });
            return await modalcliente.present();
        } else {
            //  simularDismissModalClient = async () => {
            console.log("cliente selecccionado ", this.cli);
            if (this.cli != 0 && this.cli != "undefined") {
                if (this.tipoDoc == 'DFA' || this.tipoDoc == 'DOE') this.tipodocumentfactura();
                this.controlStatus = 1;
                this.cantidadItems = true;
                this.cantidadItemsTexto = true;
                this.CardCode = this.cli;
                let model = new Clientes();
                let cliente = await model.find(this.cli);

                if (!cliente[0]) {
                    this.navCrl.pop();
                    this.toast.show(`Registro no encontrado en la cartera de clientes.`, '4000', 'bottom').subscribe(toast => {
                    });
                }

                console.log("cliente seleccionado ", cliente[0]);
                this.dibujavista(cliente[0], false);
                this.cambiarMoneda(false);
            } else {
                this.navCrl.pop();
                this.toast.show(`Selecciona un cliente para continuar.`, '4000', 'bottom').subscribe(toast => {
                });
            }
            //  }
            return true;
        }
    }

    public async listarGrupoProductos(x: any) {
        console.log("listarGrupoProductos()", x);
        console.log("this.SWaddProducto  ", this.SWaddProducto);
        let dosificacionproductos = new Dosificacionproductos();
        let grpprod: any = await dosificacionproductos.find();
        console.log("DEVD dosificacionproductos ", grpprod);
        console.log(" x  ", x)

        if (grpprod.length > 0) {
        this.grupoproductostext = grpprod[0].nombre;
        this.grupoproductoscode = grpprod[0].code;
        this.dataexport.grupoproductoscode = grpprod[0].code;
        ///    return false;
        }

        if (this.SWaddProducto) {
            this.toast.show(`No es posible selecionar otro grupo.`, '4000', 'bottom').subscribe(toast => {
            });
            return false;
        }

        if (this.pedidoData.origen == 'outer') {
            console.log("DEVD nueva logica de grupo dosificacion ",);
            let greaterTen2 = grpprod.filter(data => data.code == x);
            console.log("DEVD greaterTen2 ", greaterTen2);
            this.grupoproductostext = greaterTen2[0].nombre;
            this.grupoproductoscode = greaterTen2[0].code;
            this.dataexport.grupoproductoscode = greaterTen2[0].code;
        }
        if (typeof x === 'string') {
            try {
                let greaterTen2 = grpprod.filter(data => data.code == x);
                this.grupoproductostext = greaterTen2[0].nombre;
                this.grupoproductoscode = greaterTen2[0].code;
                this.dataexport.grupoproductoscode = greaterTen2[0].code;
            } catch (e) {
            }
            return false;
        }

        if (x === true) {
            if (grpprod.length > 0) {
                let selectx: any = {
                    title: "GRUPO DE PRODUCTOS.",
                    items: [grpprod],
                    positiveButtonText: "SELECCIONAR",
                    negativeButtonText: "CANCELAR",
                    wrapWheelText: true,
                    displayKey: 'nombre'
                };
                try {
                    let respx: any = await this.selector.show(selectx);
                    let rrx = grpprod[respx[0].index];
                    this.grupoproductostext = rrx.nombre;
                    this.grupoproductoscode = rrx.code;
                    this.dataexport.grupoproductoscode = rrx.code;
                } catch (e) {
                }
            }
        }
    }

    public listarSucursal(xx = true) {
        console.log("listarSucursal() ");
        console.log("this.dataexport ", this.dataexport);
        console.log("       this.selectSucursal.length ", this.selectSucursal.length);
        console.log("       this.selectSucursal ", this.selectSucursal);

        return new Promise(async (resolve, reject) => {
            let listSucursal = [];
            this.sucursales = [];
            let clientesucursales = new Clientessucursales();
            this.sucursales = await clientesucursales.findAll(this.CardCode, this.idUser);
            console.log("  this.sucursales  ", this.sucursales);
            for (let x of this.sucursales) {

                if (x.AdresType == "B") {
                    x.typeLabel = "COBRO";
                }
                if (x.AdresType == "S") {
                    x.typeLabel = "ENVÍO";
                }
                listSucursal.push({ description: x.typeLabel + " - " + x.AddresName });
            }

            if (this.sucursales.length > 0) {
                let sucursal = this.sucursales[0];
                if (this.selectSucursal.length > 0) {
                    sucursal = this.selectSucursal[0];
                }

                this.selectSucursal = sucursal;
                this.dataexport.sucursal = sucursal;

                if (xx == false) {
                    this.selector.show({
                        title: "SELECCIONA LA SUCURSAL DEL CLIENTE.",
                        items: [listSucursal],
                        positiveButtonText: "Seleccionar",
                        negativeButtonText: "Cancelar"
                    }).then(async (result: any) => {
                        let sucursal = this.sucursales[result[0].index];
                        /// this.selectSucursal.AddresName = '';
                        this.selectSucursal = sucursal;
                        this.dataexport.sucursal = sucursal;

                        console.log("this.selectSucursal    ", this.selectSucursal);
                        console.log("     this.dataexport.sucursal     ", this.dataexport.sucursal);

                        GlobalConstants.CabeceraDoc
                    }, (err: any) => {
                        this.toast.show(`Selecciona una sucursal.`, '4000', 'top').subscribe(toast => {
                        });
                    });
                } else {
                    console.log("listarSucursal() this.selectSucursal ", this.selectSucursal);
                    ///  if (this.sucursales.length == 1) {x

                    //    let sucursal = this.sucursales[0];
                    //    this.selectSucursal = sucursal;
                    //    this.dataexport.sucursal = sucursal;
                    // }
                }

            } else {
                this.dataexport.sucursal = [];
                this.sucursales.typeLabel = "Sin Dirección";
                console.log("no tiene sucursal ", this.sucursales.typeLabel);
                this.selectSucursal.typeLabel = this.sucursales.typeLabel;
                console.log("  this.selectSucursal  ", this.selectSucursal);
                this.toast.show(`No se encontró direcciones disponibles del cliente.`, '8000', 'center').subscribe(toast => {});
                //this.estado = true
            }
            resolve(true);
        });
    }

    public async getAlmacen(ini: number) {
        console.log("getAlmacen() ", ini);        
        let storage: any = await this.configService.getSession();
        console.log("storage session ", storage)
        console.log("almacenesdb ", storage[0].almacenes);
        let almacenesdb: any = storage[0].almacenes;

        switch (ini) {
            case (1):
                this.listAlmacen = [];
                for (let x of almacenesdb)
                    this.listAlmacen.push({ WarehouseName: x.WarehouseName });
                this.dataexport.almacen = almacenesdb[0];
                this.almacenarr = almacenesdb[0];
                this.cambioAlmacen = true;
                break;
            case (2):
                let detx: any;
                console.log("datos del detalle", GlobalConstants.DetalleDoc);
                detx = GlobalConstants.DetalleDoc;
                // let detalle = new Detalle();
                // detx = await detalle.docCount(this.pedidoData.cod);
                console.log("detalle almacen ", detx);
                this.dataexport.almacen = this.almacenarr;
                if(detx.length > 0){
                    if (!detx[0].WhsCode) {
                        this.toast.show(`Almacen no encontrado intente nuevamente.`, '4000', 'center').subscribe(toast => {
                        });
                        return false;
                    }
                }

                for (let x of almacenesdb) {
                    //this.listAlmacen.push({ WarehouseName: x.WarehouseName });//ojo
                    if (x.WarehouseCode == detx[0].WhsCode) {
                        this.dataexport.almacen = x;
                        this.almacenarr = x;
                        this.cambioAlmacen = true;
                        break;
                    }
                }

                break;
            case (3):
                this.listAlmacen = [];
                for (let x of almacenesdb)
                    this.listAlmacen.push({ WarehouseName: x.WarehouseName });
                if (almacenesdb.length > 0) {
                    let selectx: any = {
                        title: "QUE ALMACÉN USARAS?.",
                        items: [this.listAlmacen],
                        positiveButtonText: "Seleccionar",
                        negativeButtonText: "Cancelar",
                        wrapWheelText: true,
                        displayKey: 'WarehouseName'
                    };
                    this.selector.show(selectx).then((result: any) => {
                        let data = almacenesdb[result[0].index];
                        this.dataexport.almacen = data;
                        this.almacenarr = data;
                        this.cambioAlmacen = true;
                    }, (err: any) => {
                        console.log(err);
                    });
                }
                break;
        }
        console.log(" this.almacenarr   ", this.almacenarr);
    }

    public async generaPDF() {
        console.log("generaPDF()");

        try {
            this.spinnerDialog.show();
            let rx = {
                "fechahora": Tiempo.fecha() + " " + Tiempo.hora(),
                "tipodocumento": this.tipoDoc,
                "iddocumento": this.codDocumento,
                "usuario": this.userdata[0].idUsuario,
                "equipo": this.userdata[0].equipoId
            };

            let rddx: any = await this.documentosdata.findexe(this.pedidoData.cod);
            console.log("datos del documento", rddx);

            /* let reimpresion = new Reimpresion();
            let numActual = await reimpresion.buscarreimpresion(this.codDocumento);
            console.log("numActual en base  ", numActual[0]["contador"]);
            if (numActual[0]["contador"] == 2 && this.tipoDoc == 'DFA') {
                this.toast.show(`Le queda una copia disponible para llegar al máximo permitido.`, '6000', 'center').subscribe(toast => {
                });
            }
            if (numActual[0]["contador"] > 1111 && this.tipoDoc == 'DFA') {
                this.spinnerDialog.hide();
                return this.toast.show(`El documento superó el máximo de impresiones.`, '6000', 'center').subscribe(toast => {
                });
            } */

            /* await reimpresion.insert(rx);
            let num_imp = await reimpresion.buscarreimpresion(this.codDocumento); */
            
            let cliente: any = new Clientes();
            let dataCliente: any = await cliente.find(this.pedidoData.CardCode);
            if (dataCliente.length == 0) {
                let data = {
                    "codigo": this.pedidoData.CardCode
                }
                let xData: any = await this.dataService.Consultasaldoclientesap(data);
                let xJson = JSON.parse(xData.data);
                console.log("xJson.respuesta", xJson.respuesta[0]);
                dataCliente = xJson.respuesta[0];
                let xclientx = {
                    data: JSON.stringify({
                        "estado": 200,
                        "respuesta": xJson.respuesta,
                        "mensaje": "OK"

                    })
                };
                // JSON.parse(xData.data).respuesta)
                let clientes: any = new Clientes();
                await clientes.insertAll(xclientx, 0, 1);
            }
            if (typeof dataCliente != "undefined" || typeof dataCliente != undefined) {
                let rddx: any = await this.documentosdata.findexe(this.pedidoData.cod);
                console.log("datos del documento", rddx);
                let conarr: any = await this.configService.getSession();
                let usuario = conarr;
                if (rddx.PayTermsGrpCode == -1) {
                    console.log("es -1 ");
                    conarr = conarr[0].condicionespago.filter((item) => {
                        return item.GroupNumber == rddx.PayTermsGrpCode;
                    });
                    console.log("condiciones filtrado ", conarr);
                    rddx.displayCondicion = conarr[0].PaymentTermsGroupName;
                } else {
                    console.log("No es -1 ");
                    rddx.displayCondicion = dataCliente[0].cndpagoname;
                }
                let rex: any;
                //console.log("numero actual de impresion ", num_imp[0]["contador"]);

                console.log("Datos del Cliente", dataCliente);

                dataCliente[0].U_EXX_FE_Cuf = '0';
                if (this.tipoDoc == 'DFA') {

                    if (usuario[0].uso_fex == '1') {
                        console.log("fex_offline", usuario[0].docificacion[0].fex_offline)
                        if (usuario[0].docificacion[0].fex_offline == '0') {
                            console.log("conslta");
                            console.log(rddx);
                            let xData: any;
                            let dataext: any = {
                                "iddoc": this.codDocumento,
                                "nit": rddx.U_4NIT,
                                "accion": 0
                            };

                            /*  try {
                                 xData = await this.dataService.servisReportConsultaCufPost(dataext, 3);
                                 let xJson = JSON.parse(xData.data);
                                 console.log("xJson.respuesta.. servisReportConsultaCufPost usa fex", xJson.respuesta[0]);
                                 if (xJson.respuesta[0].CANTIDAD < 1) {
                                     this.toast.show(`El documento no puede ser impreso no se encontro documento en SAP.`, '4000', 'center').subscribe(toast => {
                                     });
                                     this.spinnerDialog.hide();
                                     return false;
                                 }
                             } catch (error) {
                                 console.log("El documento no puede ser impreso no se encontro documento en SAP.");
                                 this.toast.show(`El documento no puede ser impreso no se encontro documento en SAP.`, '4000', 'center').subscribe(toast => {
                                 });
                                 this.spinnerDialog.hide();
                                 return false;
                             } */

                            let dataext2: any = {
                                "iddoc": this.codDocumento,
                                "nit": dataCliente[0].FederalTaxId,
                                "accion": 1
                            };

                            let dataext3: any = {
                                "iddoc": this.codDocumento,
                                "docentry": rddx.DocEntry
                            };

                            try {
                                xData = await this.dataService.servisReportConsultaCufPost(dataext2, 3);
                                let xJson = JSON.parse(xData.data);
                                console.log("xJson.respuesta servisReportConsultaCufPost..", xJson.respuesta[0]);
                                dataCliente[0].U_EXX_FE_Cuf = xJson.respuesta[0].U_EXX_FE_Cuf;
                                dataCliente[0].U_EXX_FENUM = xJson.respuesta[0].U_LB_NumeroFactura;
                                console.log("todo bien hasta aqui datacliente", dataCliente);

                                xData = await this.dataService.servisReportConsultaestadofactura(dataext3, 3);
                                let respuesta = JSON.parse(xData.data);
                                console.log(respuesta.respuesta[0].ESTADO);

                                this.toast.show(respuesta.respuesta[0].ESTADO, '6000', 'center').subscribe(toast => { })
                                this.spinnerDialog.hide();
                            } catch (error) {
                                console.log("error al parsear el servicio de reporte", error);
                                this.spinnerDialog.hide();
                            }
                        } else {
                            dataCliente[0].U_EXX_FE_Cuf = rddx.U_LB_CodigoControl;
                        }
                        console.log("ROMERO1");
                        rex = await this.reportService.docFacturaV2(dataCliente[0], rddx, this.items, this.userdata, 0);
                    } else {
                        console.log("no usa factura electronica")
                        console.log("ROMERO2");
                        rex = await this.reportService.docFactura(dataCliente[0], rddx, this.items, this.userdata, 0);
                    }
                } else {
                    console.log("ROMERO3");
                    rex = await this.reportService.factura(dataCliente[0], rddx, this.items, this.userdata, 0, "0");
                    dataCliente[0].U_EXX_FE_Cuf = rddx.U_LB_CodigoControl;
                }
                console.log("generando PDF...", rex, 'document', this.codDocumento);
                // rex = await this.reportService.docFacturaV2(dataCliente[0], rddx, this.items, this.userdata, 0);
                // rex = await this.reportService.docFactura(dataCliente[0], rddx, this.items, this.userdata, num_imp);
                //rex = await this.reportService.factura(dataCliente[0], rddx, this.items, this.userdata, num_imp, '0');
                this.reportService.generateEXE(this.codDocumento + '.pdf');
                //if (rex) this.reportService.generateEXE(this.codDocumento + '.pdf');
            } else {
                this.toast.show(`El documento no puede ser impreso.`, '4000', 'center').subscribe(toast => {
                });
            }
            this.spinnerDialog.hide();
        } catch (e) {
            this.spinnerDialog.hide();
            console.log("error en algun lugar", e);
        }
    }

    public async generaEntrega() {
        console.log("generaEntrega()");

        try {
            this.spinnerDialog.show();
            let cliente: any = new Clientes();
            let dataCliente: any = await cliente.find(this.pedidoData.CardCode);
            let rddx: any = await this.documentosdata.findexe(this.pedidoData.cod);

            let reimpresion = new Reimpresion();
            let num_imp = await reimpresion.buscarreimpresion(this.codDocumento);

            let rex: any;
            rex = await this.reportService.factura(dataCliente[0], rddx, this.items, this.userdata, num_imp, '1');
            if (rex) this.reportService.generateEXE(this.codDocumento + '.pdf');
            this.spinnerDialog.hide();
        } catch (e) { }
    }

    public async generaContrato() {
        console.log("generaContrato()");

        try {
            this.spinnerDialog.show();
            let reimpresion = new Reimpresion();
            let num_imp = await reimpresion.buscarreimpresion(this.codDocumento);
            console.log(num_imp)
            let cliente: any = new Clientes();
            let dataCliente: any = await cliente.find(this.pedidoData.CardCode);
            let rddx: any = await this.documentosdata.findexe(this.pedidoData.cod);
            let dataext2: any = {
                "iddoc": this.codDocumento,
                "nit": dataCliente[0].FederalTaxId,
                "accion": 1
            };

            let xData: any;

            try {
                xData = await this.dataService.servisReportConsultaCufPost(dataext2, 3);
                let xJson = JSON.parse(xData.data);
                console.log("xJson.respuesta servisReportConsultaCufPost..", xJson.respuesta[0]);
                dataCliente[0].U_EXX_FE_Cuf = xJson.respuesta[0].U_EXX_FE_Cuf;
                dataCliente[0].U_EXX_FENUM = xJson.respuesta[0].U_LB_NumeroFactura;
                console.log("todo bien hasta aqui datacliente", dataCliente);

                if(dataCliente[0].U_EXX_FENUM == "" || dataCliente[0].U_EXX_FE_Cuf == "0"){
                    this.spinnerDialog.hide();
                    this.toast.show(`Factura aun no se envia a impuestos.`, '3000', 'bottom').subscribe(toast => {
                    });
                    return false;
                }else{
                    this.spinnerDialog.hide();
                    let rex: any;
                    rex = await this.reportService.docContrato(dataCliente[0], rddx, this.items, this.userdata);
                    if (rex) this.reportService.generateEXE(this.codDocumento + '.pdf');
                    
                }
            } catch (error) {
                console.log("error al parsear el servicio de reporte", error);
                this.spinnerDialog.hide();
            }           
        } catch (e) { }
    }

    public async generateCollilla() {
        console.log("generateCollilla()");

        try {
            this.spinnerDialog.show();
            let rx = {
                "fechahora": Tiempo.fecha() + " " + Tiempo.hora(),
                "tipodocumento": this.tipoDoc,
                "iddocumento": this.codDocumento,
                "usuario": this.userdata[0].idUsuario,
                "equipo": this.userdata[0].equipoId
            };
            console.log("this.pedidoData ", this.pedidoData);
            // let reimpresion = new Reimpresion();
            // let numActual = await reimpresion.buscarreimpresion(this.codDocumento);
            // console.log("numActual en base  ", numActual[0]["contador"]);
            // if (numActual[0]["contador"] == 2 && this.tipoDoc == 'DFA') {
            //     this.toast.show(`Le queda una copia disponible para llegar al máximo permitido.`, '4000', 'center').subscribe(toast => {
            //     });
            // }
            // if (numActual[0]["contador"] > 3 && this.tipoDoc == 'DFA') {
            //     this.spinnerDialog.hide();
            //     return this.toast.show(`El documento superó el máximo de impresiones.`, '4000', 'center').subscribe(toast => {
            //     });
            // }

            // await reimpresion.insert(rx);
            // let num_imp = await reimpresion.buscarreimpresion(this.codDocumento);
            let cliente: any = new Clientes();
            let dataCliente: any = await cliente.find(this.pedidoData.CardCode);
            if (typeof dataCliente != "undefined" || typeof dataCliente != undefined) {
                let rddx: any = await this.documentosdata.findexe(this.pedidoData.cod);
                let conarr: any = await this.configService.getSession();
                if (rddx.PayTermsGrpCode == -1) {
                    console.log("es -1 ");
                    conarr = conarr[0].condicionespago.filter((item) => {
                        return item.GroupNumber == rddx.PayTermsGrpCode;
                    });
                    console.log("condiciones filtrado ", conarr);
                    rddx.displayCondicion = conarr[0].PaymentTermsGroupName;
                } else {
                    console.log("No es -1 ");
                    rddx.displayCondicion = dataCliente[0].cndpagoname;
                }
                let rex: any;
                // console.log("numero actual de impresion ", num_imp[0]["contador"]);
                if (this.tipoDoc == 'DFA') {
                    rex = await this.reportService.docFacturaCollilla(dataCliente[0], rddx, this.items, this.userdata, "0001");
                }
                if (rex) this.reportService.generateEXE(this.codDocumento);
            } else {
                this.toast.show(`El documento no puede ser impreso.`, '4000', 'center').subscribe(toast => {
                });
            }
            this.spinnerDialog.hide();
        } catch (e) {
        }
    }

    public async cambiarMoneda(tipo = false) {
        console.log("cambiarMoneda ()");
        if (this.userdata[0].config[0].controlarCambioMoneda == 0 && tipo == true) {
            this.toast.show(`No está permitido para modificar moneda .`, '2500', 'center').subscribe(toast => {
            });
            return false;
        }
        if (this.items.length == 0) {
            let monedas = [];
            let moneda: any = await this.configService.getSession();
            console.log("sesion moneda ", moneda);
            if (this.Moneda == '##' && tipo == true) {
                try {
                    for (let x of moneda[0].monedas)
                        monedas.push({ description: x.Code });
                    this.selector.show({
                        title: "SELECCIONAR MONEDA.",
                        items: [monedas],
                        positiveButtonText: "OK",
                        negativeButtonText: "Cancelar"
                    }).then((result: any) => {
                        let dd = moneda[0].monedas[result[0].index];
                        this.Currency = dd.Code;
                        console.log("moneda seleccionada", dd.Code);                        
                        this.dataexport.moneda = dd.Code;
                        this.dataexport.Type = dd.Type;
                    }, (err: any) => {
                        console.log(err);
                    });
                } catch (e) {
                }
            } else {
                let money = '';
                let moneytipo = '';
                if (this.dataexport.cliente.Currency == '##') {
                    for (let x of moneda[0].monedas) {
                        if (x.Type == 'L') {
                            money = x.Code;
                            moneytipo = x.Type;
                        }
                    }
                } else {
                    for (let x of moneda[0].monedas) {
                        console.log("x.Code ", x.Code, " ==  this.dataexport.cliente.Currency ", this.dataexport.cliente.Currency);
                        if (x.Code == this.dataexport.cliente.Currency) {
                            money = x.Code;
                            moneytipo = x.Type;
                        }
                    }
                    if (money === '') {
                        this.toast.show(`No se encontró una moneda para el cliente, buscaremos uno por defecto.`, '5000', 'center').subscribe(toast => {
                        });
                        this.dataexport.cliente.Currency
                        console.log("carga moneda", this.dataexport.cliente.Currency);
                        money = moneda[0].monedas[0].Code;
                        moneytipo = moneda[0].monedas[0].Type;
                        console.log("carga moneda", money);
                        console.log("carga moneda", moneytipo);
                    }
                }

                this.Currency = money;
                this.dataexport.Type = moneytipo;
                this.dataexport.moneda = money;
                this.dataexport.Type = moneytipo;
            }
        }
    }

    private cotrollineas(arr: any) {
        return arr.reduce((sum, value) => (sum + value.Quantity), 0);
    }

    ionViewDidEnter() {

        console.log("ionViewDidEnter()");
        if (this.pedidoData.tipoestado == 'new') {
            this.validPromocionesUsadas();

        }
    }

    async validPromocionesUsadas() {
        let modelPromociones = new promocionaes();
        let promosUsadas: any = await modelPromociones.showAllUsesBycod(this.codDocumento);
        console.log("promociones usadas por dfa ", promosUsadas);

        promosUsadas.forEach(async element => {
            console.log("element ", element);
            await modelPromociones.deleteUse(this.codDocumento);
        });
    }

    public async popOver(event) {

        let dataProps: any;
       
        dataProps = {
            tipo: this.pedidoData.DocType,
            estado: this.pedidoData.canceled
        };
        
        await this.configService.getTipo();

        try {
            let poop: any = { component: PopinfoComponent, componentProps: dataProps,event: event,translucent: true, mode: 'ios' };

            let popover: any = await this.popoverController.create(poop);
            await popover.present();
            let { data } = await popover.onWillDismiss();
            switch (data.index) {
                case (1):
                    this.pagos(data.index);
                    break;
                case (2):
                    this.pagos(data.index);
                    break;
                case (3):
                    this.anular();
                    break;
                case (4):
                    this.estadodoc();
                    break;
                case ('DOF'): //oferta (DOF)
                    this.clonar('DOF');
                    break;
                case ('DOP'): //pedido (DOP)
                    this.clonar('DOP');
                    break;
                case ('DOFX'): //oferta (DOF) duplicacioan
                    this.clonar('DOF', 11);
                    break;
                case ('DOPX'): //pedido (DOP) duplicacioan
                    this.clonar('DOP', 11);
                    break;
                case ('DFA'): //factura (DFA) deudor
                    this.clonar('DFA', 0);
                    break;
                case ('DFAX'): //factura (DFA) reserva
                    this.clonar('DFA', 1);
                    break;
                case ('DOE'): //entrega (DOE)
                    this.clonar('DOE');
                    break;
                case ('FED'): 
                    this.fromevidencia();
                break;
            }
        } catch (e) {
        }
    }

    public async clonar(tipo: any, facturareserva = 0) {
        //await this.tarjetaBonificacion.cleanTable()
        this.spinnerDialog.show(null, null, true);
        console.log("EL TIPO QUE ENVIA ES", tipo)
        let rxd: any = await this.documentosdata.documentospen();
        if (rxd.length > 0) {
            let rxx = rxd[0];
            let docx = '';
            switch (rxx.DocType) {
                case ('DOF'):
                    docx = 'OFERTA';
                    break;
                case ('DOP'):
                    docx = 'PEDIDO';
                    break;
                case ('DOE'):
                    docx = 'ENTREGA';
                    break;
                case ('DFA'):
                    docx = 'FACTURA';
                    break;
            }
            this.spinnerDialog.hide();
            this.toast.show(`Imposible de crear tiene un documento de ${docx} en memoria.`, '2500', 'center').subscribe(toast => {
            });
            return false;
        }
        if (this.pedidoData.tipoestado == 'anulado' && facturareserva != 11) {
            this.spinnerDialog.hide();
            this.toast.show(`Esta acción no está permitida el documento esta anulado.`, '2500', 'top').subscribe(toast => {
            });
            return false;
        }
        if (this.tipoDoc == 'DOP' && this.userdata[0].config[0].permisoCopiarPedido == 0) {
            this.spinnerDialog.hide();
            this.toast.show(`No está permitido para copiar pedido .`, '2500', 'center').subscribe(toast => {
            });
            return false;
        }
        if (this.tipoDoc == 'DFA' && this.userdata[0].config[0].permisoCopiarOferta == 0) {
            this.spinnerDialog.hide();
            this.toast.show(`No está permitido para crear facturas.`, '2500', 'center').subscribe(toast => {
            });
            return false;
        }
        // alert("this.userdata[0].config[0].permisoFactura " + this.userdata[0].config[0].permisoFactura);
        // alert("this.tipoDoc " + tipo);
        if (tipo == 'DFA' && this.userdata[0].config[0].permisoFactura == '0') {
            this.spinnerDialog.hide();
            this.toast.show(`No está permitido para facturar .`, '2500', 'center').subscribe(toast => {
            });
            return false;
        }

        if (tipo == 'DOE' && this.userdata[0].config[0].permisoEntrega == '0') {
            this.spinnerDialog.hide();
            this.toast.show(`No está permitido para realizar entregas .`, '2500', 'center').subscribe(toast => {
            });
            return false;
        }

        if (tipo == 'DFA') {
            try {
                console.log("iempo.fecha() ", moment().format('YYYY-MM-DD'), " this.userdata[0].docificacion[0].U_FechaLimiteEmision ", this.userdata[0].docificacion[0].U_FechaLimiteEmision)
                // if (this.userdata[0].docificacion[0].U_FechaLimiteEmision > moment().format('YYYY-MM-DD')) {
                //     this.toast.show(`La fecha de dosificación caducada sincronice nuevamente.`, '4000', 'center').subscribe(toast => {
                //     });
                //     return false;
                // }
                let conx = 0;
                for (let doci of this.userdata[0].docificacion) {
                    if (doci.U_FechaLimiteEmision >= moment().format('YYYY-MM-DD')) {
                        conx++;
                    }
                }
                if (conx == 0) {
                    this.spinnerDialog.hide();
                    this.toast.show(`La fecha de dosificación caducada sincronice nuevamente.`, '4000', 'center').subscribe(toast => {
                    });
                    return false;
                }
            } catch (e) {
                this.spinnerDialog.hide();
                this.toast.show(`La fecha de dosificación caducada sincronice nuevamente.`, '4000', 'center').subscribe(toast => {
                });
                return false;
            }
        }

        console.log("this.pedidoData.origen  ", this.pedidoData.origen);
        console.log("tipo de documento", tipo);
        console.log("tipo de documento", this.cambioAlmacen);

        if ((tipo == 'DFA' && facturareserva == 0) || tipo == 'DOE') {
            if (true) { //this.pedidoData.origen == "inner"
                let detalles = new Detalle();
                let items: any = await detalles.itemsGroup(this.idPedido);
                for await (let item of items) {
                    try {
                        let xData: any;
                        let dataext: any = {
                            "codigo": item.ItemCode,
                            "almacen": item.WhsCode
                        };
                        let productosalmacenes = new Productosalmacenes();
                        xData = await this.dataService.servisReportValidaStockPost(dataext, 3);
                        let xJson = JSON.parse(xData.data);
                        console.log("xJson.respuesta", xJson.respuesta[0]);
                        await productosalmacenes.addUpdateprodcualmacenessap(xJson.respuesta[0]);
                    } catch (error) {
                        console.log("Error no se pudo valir con sap", error);
                        console.log(error);
                    }
                }

                let prodalma: any = await detalles.verificarStock(this.idPedido);

                if (prodalma == false) {

                    //   if (JSON.stringify(this.almacenarr) == '{}') {
                    //execute
                    if (!this.cambioAlmacen) {
                        console.log('Almacen vacio');
                        let storage: any = await this.configService.getSession();
                        console.log("almacenesdb ", storage[0].almacenes);
                        let almacenesdb: any = storage[0].almacenes;
                        this.listAlmacen = [];
                        for (let x of almacenesdb)
                            this.listAlmacen.push({ WarehouseName: x.WarehouseName });
                        let selectx: any = {
                            title: "QUE ALMACÉN USARAS?.",
                            message: "No encontramos el almacen del documento original, seleccione un almacen.",
                            items: [this.listAlmacen],
                            positiveButtonText: "Seleccionar",
                            negativeButtonText: "Cancelar",
                            wrapWheelText: true,
                            displayKey: 'WarehouseName'
                        };
                        this.selector.show(selectx).then(async (result: any) => {
                            this.spinnerDialog.show(null, null, true);
                            let data = almacenesdb[result[0].index];
                            this.dataexport.almacen = data;
                            this.almacenarr = data;
                            console.log("Almacen seleccionado ", this.almacenarr);
                            await detalles.cambiarAlmacenStock(this.idPedido, this.almacenarr.WarehouseCode);
                            await this.listarDetalle();
                            this.cambioAlmacen = true;
                            setTimeout(() => {
                                this.spinnerDialog.hide();
                            }, 2000);

                            try {
                                this.spinnerDialog.hide();
                                this.toast.show(`Almacen cambiado con éxito.`, '2500', 'top').subscribe(toast => {
                                });
                            } catch (error) {
                                this.spinnerDialog.hide();
                                this.toast.show(`Error al cambiar el almacen.`, '2500', 'top').subscribe(toast => {
                                });
                            }
                        }, (err: any) => {
                            console.log(err);
                        });
                    } else {
                        this.spinnerDialog.hide();
                        this.toast.show(`Sin stock en almacen.`, '2500', 'top').subscribe(toast => { });
                    }
                    return false;
                }
            }
            /* if (this.pedidoData.origen == "inner") {
 
             }*/
        }

        this.dataArr = [];
        let copiados: any = await this.documentosdata.copiados(this.pedidoData.cod);
        let x: boolean;
        (this.pedidoData.DocType == tipo) ? x = true : x = false;

        if ((copiados.length > 0) && (x == false)) {
            let cods = [];
            let cantlineas = 0;
            for (let copiado of copiados) {
                let detalle = new Detalle();
                let itemx = await detalle.docCount(copiado.cod);
                cantlineas += this.cotrollineas(itemx);
                cods.push('"' + copiado.cod + '"');
            }
            if (cantlineas >= this.cotrollineas(this.items)) {
                this.spinnerDialog.hide();
                this.toast.show(`El documento ya fue copiado.`, '4000', 'top').subscribe(toast => {
                });
                return false;
            } else {
                let detalle = new Detalle();
                let detallesx = await detalle.detallesDocumentosunion(String(cods));
            }
        }

        let aux_ctrl_ent_to_fact = this.pedidoData.DocType;
        if ((aux_ctrl_ent_to_fact == "DOE") && (this.pedidoData.origen == "inner")) {
            let ctrfct = this.pedidoData.clone;
            let aux_ctrfct = ctrfct.indexOf("DFA");
            if ((aux_ctrfct > -1) && (tipo = "DFA")) {
                this.spinnerDialog.hide();
                this.toast.show(`El documento ya fue facturado.`, '4000', 'top').subscribe(toast => {
                });
                return false;
            }
        }

        if (aux_ctrl_ent_to_fact == "DFA") {
            if (this.pedidoData.Reserve == 0) {
                this.spinnerDialog.hide();
                this.toast.show(`No puede generar una entrega de una factura de deudor.`, '4000', 'top').subscribe(toast => {
                });
                return false;
            }
        }

        try {
            this.isenabled = true;
            let datax: any = await this.codGenx(tipo);
            let cod = await this.documentosdata.generaCod(tipo, this.idUser, datax);
            GlobalConstants.Clon = 1;
            GlobalConstants.CabeceraDoc = [];
            
            this.geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 10000 }).then((resp) => {
                if (resp) {
                    console.log('resp GEOPOSITION SIN PROBLEMAS', resp);
                    let obj: any = { lat: resp.coords.latitude, lng: resp.coords.longitude };
                    console.log("registerLocation() ", "" + obj);
                    localStorage.setItem("lat", obj.lat);
                    localStorage.setItem("lng", obj.lng);
                    GlobalConstants.Longitud = obj.lng;
                    GlobalConstants.latitud = obj.lat; 
                }
            }).catch(error => {
                console.log("error 1 ", error);
            });

            let idnuevo: any = await this.documentosdata.clonar(this.idPedido, this.idUser, tipo, x, cod, facturareserva);
            if (idnuevo == '0') {
                this.spinnerDialog.hide();
                this.toast.show(`No se encontraron sucursales asociadas al cliente.`, '2500', 'top').subscribe(toast => {
                });
                this.isenabled = false;
            } else {
                let detalle = new Detalle();
                GlobalConstants.DetalleDoc = [];
                this.geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 10000 }).then((resp) => {
                    if (resp) {
                        console.log('resp GEOPOSITION SIN PROBLEMAS', resp);
                        let obj: any = { lat: resp.coords.latitude, lng: resp.coords.longitude };
                        console.log("registerLocation() ", "" + obj);
                        localStorage.setItem("lat", obj.lat);
                        localStorage.setItem("lng", obj.lng);
                        GlobalConstants.Longitud = obj.lng; 
                        GlobalConstants.latitud = obj.lat;
                    }
                }).catch(error => {
                    console.log("error 1 ", error);
                });
                await detalle.clonar(this.idPedido, idnuevo, x, tipo, facturareserva);
                this.isenabled = false;
                await this.configService.setDocSC(tipo);
                if (localStorage.getItem('verImportadosMarker') == "SI") {
                    console.log("mandar a factura mokaer logic ");
                }
                this.navCrl.pop();
            }
            this.spinnerDialog.hide();
            this.navCrl.pop();
        } catch (e) {
            this.spinnerDialog.hide();
            console.log(e);
        }
    }

    public async upadteDetalle(data: any) {

        /* let lotes: any = new Lotes();
         await lotes.deleteLote(data.dataproducto.id);
         for await (let lote of data.lotesarr) {
             console.log("  each lote insert  ", lote);
             let lotes: any = new Lotes();
             await lotes.insertLote(data.dataproducto.id, lote.loteName, lote.num, data.dataproducto.BaseQty, data.WhsCode, data.ItemCode);
             //   await lotes.insertLote(resp, lote.loteName, lote.num, dataPedido.BaseId, pedido.WhsCode, dataPedido.ItemCode);
         }
 
         if (data.seriesarr.length > 0) {
             let raxs = [];
             for await (let serie of data.seriesarr) {
                 let xv = serie.replace(/'/gi, '');
                 raxs.push('"' + xv + '"');
             }
             let series: Seriesproductos = new Seriesproductos();
             await series.updateAfter(data.dataproducto.id);
             for await (let serie of data.seriesarr) {
                 let series = new Seriesproductos();
                 await series.updatetemp(serie, data.dataproducto.id);
             }
         }*/

        let detalle = new Detalle();
        await detalle.updateItemsLocal(data, data.dataproducto.id, this.dataexport.tipoDoc, this.dataexport.tipoDocx);
        //  await this.updateItemFromBonusCard(data.dataproducto.ItemCode);
        this.listarDetalle();

    }

    public async detalleProductoVenta(item: any) {
        console.log("-- item ", item)
        console.log("this.dataexport. ", this.dataexport);
        let x = this.almacenes[0].almacenes;
        let almacenx: any = _.filter(x, { "WarehouseCode": item.WhsCode });
        if (almacenx.length > 0) {
            this.dataexport.almacen = almacenx[0];
            this.almacenarr = almacenx[0];
        } else {
            if (this.dataexport.cliente.origen == 'outer' || this.dataexport.cliente.clone.length > 3) {
                console.log("es clon o outer ");
                /**
                 * asignar almacen por defecto para documentos importados
                 */
                this.dataexport.almacen = this.almacenes[0].almacenes[0];
                this.almacenarr = this.almacenes[0].almacenes[0];
                // this.clonadoEstado=true;

            } else {
                this.toast.show(`No existe almacén para detallar el producto.`, '2500', 'top').subscribe(toast => {
                });

                return false;
            }
        }
        this.dataexport.origen = this.pedidoData.origen;
        this.auxBonificacion = item.Quantity;
        item.dataexport = this.dataexport;
        item.edit = true;
        item.estado = this.estado;
        item.bonificacionx = item.bonificacion;

        let mcproducto: any = {
            component: DetalleventaPage,
            cssClass: 'transparente',
            componentProps: item,
        };
        let modalventa: any = await this.modalController.create(mcproducto);
        modalventa.onDidDismiss().then(async (data: any) => {
            if (typeof data.data != "undefined") {
                if (data.data != 1) {
                    let c = data.data;
                    let dataInsert = {
                        dataproducto: item,
                        cantidad: c.cantidad,
                        descuento: c.descuento,
                        porcentajedata: c.descuentoporsentaje,
                        descuentototal: c.descuentototal,
                        presio: parseFloat(c.unidad.Price),
                        bonificacion: c.bonificacion,
                        icett: (c.icett - c.descuentototal),
                        TotalPay: c.icett,
                        icete: c.icete,
                        icetp: c.icetp,
                        ICEp: c.ICEp,
                        ICEe: c.ICEe,
                        ICEt: c.ICEt,
                        unidadid: c.unidad.Code,
                        lotesarr: c.lotesarr,
                        lotesarrAux: c.lotesarrAux,
                        seriesarr: c.seriesarr,
                        BaseId: c.unidad.BaseQty,
                        BaseQty: c.unidad.BaseQty,
                        WhsCode: c.almacen,
                        ItemCode: c.unidad.ItemCode,
                        usarBonificacion: c.bonificaciones,
                    };
                    /*if (this.auxBonificacion != c.cantidad && c.bonificacion == 0) {
                        await this.deleteBonusFromDetail();
                    }
                    */
                    this.SWaddProducto = true;
                    console.log("DEVD on dissmiss detail ", dataInsert);
                    console.log("DEVD on dissmiss  this.SWaddProducto  ", this.SWaddProducto);

                    await this.upadteDetalle(dataInsert);

                    if (this.descuentoDelTotalPorcentual > 0) {
                        console.log("descuento porcentual colocaldo:", this.descuentoDelTotalPorcentual);
                        this.descuntoAdicional(this.descuentoDelTotalPorcentual, true);
                    }
                }
            }
        });
        return await modalventa.present();
    }

    /******Start pagos******/
    public async formPago(tipo: number, monto: number) {
        let numerox: number = 0;
        let inix: any = await this.pagosService.getNumeracionpago();
        numerox = (inix + 1);
        let codPago = Calculo.generaCodeRecibo(this.idUser.toString(), numerox.toString(), '1');
        let cliente: any = new Clientes();
        let dataCliente: any = await cliente.find(this.pedidoData.CardCode);
        let datospago = {
            modo: 'CLIENTE',
            cod: codPago,
            cliente: this.pedidoData.CardCode,
            dataCliente: dataCliente[0],
            tipo: tipo,
            monto: monto,
            documento: [{
                cod: this.idPedido,
                coddoc: this.codDocumento,
                pagarx: monto
            }],
            correlativo: numerox
        };
        let obj: any = { component: FrompagosPage, componentProps: datospago };
        let modal: any = await this.modalController.create(obj);
        modal.onDidDismiss().then(async (data: any) => {
            if (data.data != 0) {
                await this.sumadorrecibo();
                //Modificaciones impresion de recibos y actualizacion de reimpresion
                let documentopago = new Documentopago();
                console.log("retornadosss",data);

                data.data.documentoId = this.idPedido;

                let rex: any = await this.reportService.generarecibo(data.data, this.dataexport.cliente, true, data.data.mediosPago, this.userdata);
                if (rex) this.reportService.generateEXE(data.data.nro_recibo);

                /*let aux_pago_cab = await documentopago.findPagos(data.data.documentoPagoId);
                console.log("dadtos retornados", aux_pago_cab);


                let rx = {
                    "fechahora": Tiempo.fecha() + " " + Tiempo.hora(),
                    "tipodocumento": "DPG",
                    "iddocumento": aux_pago_cab[0].codigo,
                    "usuario": this.userdata[0].idUsuario,
                    "equipo": this.userdata[0].equipoId
                };
                let reimpresion = new Reimpresion();
                await reimpresion.insert(rx);
                let num_imp = await reimpresion.buscarreimpresion(aux_pago_cab[0].codigo);
                aux_pago_cab[0].reimpresion = num_imp;
                //Modificaciones impresion de recibos y actualizacion de reimpresion
                let aux_pago_det = [];
                data.data["DocumentTotalPay"] = this.dataexport.cliente.DocumentTotalPay;
                data.data["saldox"] = this.dataexport.cliente.saldox - data.data.monto;
                aux_pago_det.push(data.data);
                let rex: any = await this.reportService.generarecibo(aux_pago_cab[0], this.dataexport.cliente, true, aux_pago_det, this.userdata);
                if (rex) this.reportService.generateEXE(data.data.documentoPagoId);*/
                this.toast.show(`El pago se realizó correctamente.`, '4000', 'top').subscribe(toast => {
                });
            }
        });
        return await modal.present();
    }

    public async sumadorrecibo(): Promise<any> {
        try {
            let inix: any = await this.pagosService.getNumeracionpago();
            let xc: number = (inix + 1);
            await this.configService.setNumeracionpago(xc);
            return true;
        } catch (e) {
        }
    }

    public async pagos(tipo: any) {
        if (this.pedidoData.tipoestado == 'anulado') {
            this.toast.show(`Esta acción no está permitida el documento esta anulado.`, '2500', 'top').subscribe(toast => {
            });
            return false;
        }
        if (this.tipoDoc == 'DFA') {
            if (this.userdata[0].config[0].permisoPagosFacturasLocales == 0 && this.pedidoData.origen == 'origen') {
                this.toast.show(`No está permitido para esta acción .`, '2500', 'center').subscribe(toast => {
                });
                return false;
            }
            if (this.userdata[0].config[0].permisoPagoFacturasImportadas == 0 && this.pedidoData.origen == 'outer') {
                this.toast.show(`No está permitido para realizar pagos de factura .`, '2500', 'center').subscribe(toast => {
                });
                return false;
            }
            let resp: any = await this.documentosdata.existDocument(this.codDocumento);
            if (resp.xd > 0) {
                this.toast.show(`Este documento se puede pagar desde documentos importados. `, '5000', 'top').subscribe(toast => {
                });
                return false;
            }
        }
        let dataDoc: any = await this.documentosdata.findexe(this.idPedido);
        if (dataDoc.pago == dataDoc.DocumentTotalPay || dataDoc.saldox < 0) {
            this.toast.show(`El documento ya fue cancelado.`, '4000', 'top').subscribe(toast => {
            });
            return false;
        }
        let total = 0;
        let saldoxx = 0;
        switch (tipo) {
            case (1):
                total = Calculo.round(dataDoc.saldox / parseFloat(this.userdata[0].tipocambioparalelo.tipoCambio));
                saldoxx = Calculo.round(dataDoc.saldox / parseFloat(this.userdata[0].tipocambioparalelo.tipoCambio));
                break;
            case (2):
                total = dataDoc.saldox;
                saldoxx = dataDoc.saldox;
                break;
        }

        let alert: any = await this.alertController.create({
            header: 'MONTO A CANCELAR.',
            mode: 'ios',
            inputs: [/*{
                name: 'saldox',
                type: 'text',
                value: 'SALDO: ' + saldoxx,
                disabled: true
            }, */{
                    name: 'data',
                    type: 'number',
                    min: 0,
                    max: 100000,
                    value: total,
                    placeholder: '0',
                    disabled: true
                }],
            buttons: [{
                text: 'CONTINUAR',
                handler: (data: any) => {
                    if (data.data > 0) {
                        this.formPago(tipo, data.data);
                    } else {
                        this.toast.show(`El monto introducido no es válido.`, '4000', 'top').subscribe(toast => {
                        });
                        return false;
                    }
                }
            }]
        });
        await alert.present();
    }

    /******End pagos******/
    public async anulardoc() {
        if (this.tipoDoc == 'DOP' && this.userdata[0].config[0].permisoAnularPedido == 0) {
            this.toast.show(`No está permitido para anular pedidos .`, '2500', 'center').subscribe(toast => {
            });
            return false;
        }
        if (this.tipoDoc == 'DOF' && this.userdata[0].config[0].permisoAnularOferta == 0) {
            this.toast.show(`No está permitido para anular ofertas .`, '2500', 'center').subscribe(toast => {
            });
            return false;
        }
        let copiados: any = await this.documentosdata.copiados(this.pedidoData.cod);
        if (copiados.length > 0) {
            this.toast.show(`El documento tiene copias asociadas no se puede anular.  `, '4000', 'top').subscribe(toast => {
            });
            return false;
        }

        if (this.tipoDoc == 'DOP' && this.userdata[0].config[0].permisoAnularPedidoImportado == 0) {
            this.toast.show(`No es posible anular documentos importados.  `, '4000', 'top').subscribe(toast => {
            });
            return false;
        }

        let fecha = moment().format('YYYY-MM-DD');
        console.log("fecha ", fecha)
        console.log("this.pedidoData ", this.pedidoData)

        if (this.pedidoData.CreateDate == fecha) {
            let obj: any = { component: AnularPage };
            let modal: any = await this.modalController.create(obj);
            modal.onDidDismiss().then(async (data: any) => {
                if (data.data != false) {
                    if (this.network.type != 'none')
                    {
                        let loading = await this.loadinCtrl.create({message:"Anulando..."});
                        let respuesta: any;

                        try {
                            loading.present();
                            console.log("ID QUE MANDA1",this.userdata[0].idUsuario);
                            respuesta = await this.dataService.exportanulacionlocal(data.data, this.pedidoData.cod, this.tipoDoc,this.userdata[0].idUsuario);
                            this.toast.show(respuesta.mensaje, '3000', 'center').subscribe(toast => { });
                            
                            loading.dismiss();
                        } catch (error){
                            console.log("eroor al eportar la anulacion");
                            loading.dismiss();
                        }
                        console.log("respuesta", respuesta);
                        if (respuesta && respuesta.estado == 3) {
                            await this.documentosdata.anulacionDocumento(data.data, this.pedidoData.cod);
                            let detalle = new Detalle();
                            switch (this.pedidoData.DocType) {
                                case ("DOP"):
                                    await detalle.detalledocremovestockCommited(this.pedidoData.cod);
                                    break;
                                case ("DFA"):
                                    if (this.pedidoData.Reserve == 0) {
                                        detalle.detalledocAddstock(this.pedidoData.cod);
                                    }
                                    break;
                            }
                        } else {
                            this.toast.show(respuesta.mensaje, '3000', 'center').subscribe(toast => { });
                        }
                    } else {
                        this.toast.show("Su conexión es nula o limitada. La anulacion no se puede realizar Offline", '3000', 'center').subscribe(toast => { });
                    }
                    this.navCrl.pop();
                }
            });
            return await modal.present();
        } else {
            const alert = await this.alertController.create({
                cssClass: "custom-alert",
                header: "Operación requiere una autorización",
                message: "<p>Se verificará la autorización de anulación del documento.</p>",
                buttons: [
                    {
                        text: "Cancelar",
                        role: "cancel",
                        cssClass: "secondary",
                        handler: async (blah) => {
                            console.log(" Cancel: blah");
                            return false;
                        },
                    },
                    {
                        text: "Confirmar",

                        handler: async (blah) => {
                            console.log("Confirm Okay ", blah);
                            try {
                                let response: any = await this.dataService.serviceAutorization({ idUsuario: this.userdata[0].idUsuario, tipoDoc: this.tipoDoc, codDoc: this.pedidoData.cod });
                                console.log("response ", response)
                                let responseJson: any = JSON.parse(response.data);
                                console.log("responseJson ", responseJson)
                                if (responseJson.mensaje) {
                                    console.log("cancelando DOCUMENTO")
                                    let obj: any = { component: AnularPage };
                                    let modal: any = await this.modalController.create(obj);
                                    modal.onDidDismiss().then(async (data: any) => {
                                        if (data.data != false) {

                                            if (this.network.type != 'none') {
                                                console.log("ID QUE MANDA",this.userdata[0].idUsuario);
                                                let respuesta = await this.dataService.exportanulacionlocal(data.data, this.pedidoData.cod, this.tipoDoc,this.userdata[0].idUsuario);
                                                console.log("respuesta", respuesta);
                                                if (respuesta.estado == 3) {
                                                    await this.documentosdata.anulacionDocumento(data.data, this.pedidoData.cod);
                                                    let detalle = new Detalle();
                                                    switch (this.pedidoData.DocType) {
                                                        case ("DOP"):
                                                            await detalle.detalledocremovestockCommited(this.pedidoData.cod);
                                                            break;
                                                        case ("DFA"):
                                                            if (this.pedidoData.Reserve == 0) {
                                                                detalle.detalledocAddstock(this.pedidoData.cod);
                                                            }
                                                            break;
                                                    }
                                                } else {
                                                    this.toast.show(respuesta.mensaje, '3000', 'center').subscribe(toast => { });
                                                }
                                            } else {
                                                this.toast.show("Su conexión es nula o limitada. La anulacion no se puede realizar Offline", '3000', 'center').subscribe(toast => { });
                                            }
                                            this.navCrl.pop();
                                        }
                                    });
                                    return await modal.present();
                                } else {
                                    this.toast.show(`Autorización rechazada.`, '4000', 'center').subscribe(toast => {
                                    });
                                }
                            } catch (error) {
                                console.log("error ", error)
                                this.toast.show(`Error inesperado.`, '4000', 'center').subscribe(toast => {
                                });
                            }
                        },
                    },
                ],
            });

            await alert.present();
        }
    }

    public async anular() {
        let pagos = new Pagos();
        let px: any = await pagos.findDoc(this.idPedido);
        console.log("px ", px);
        console.log("px ", this.pedidoData.estadosend);
        if (px.length > 0) {
            this.toast.show(`Este documento tiene pagos acción no permitida.`, '4000', 'top').subscribe(toast => {
            });
            return false;
        }
        switch (this.pedidoData.estadosend) {
            case ('3'):
                this.anulardoc();
                break;
            case ('1'):
                this.anulardoc();
                break;
            case ('7'):
                this.toast.show(`El documento ya fue anulado!`, '4000', 'top').subscribe(toast => {
                });
                break;
            default:
                this.toast.show(`Es imposible anular porque el documento no fue enviado!`, '4000', 'top').subscribe(toast => {
                });
        }
    }

    public async estadodoc() {

        let data = {
            "codigo": this.codDocumento
        }
        let xData: any = await this.dataService.Consultasaldodocumento(data);
        let xJson = JSON.parse(xData.data);
        console.log("respuesta Consultasaldoclientesap", xJson.respuesta[0]);

        if (xJson.respuesta[0].ESTADO >= 3) {
            this.toast.show(`El documento ` + this.codDocumento + ` fue Enviado a SAP`, '4000', 'top').subscribe(toast => { });
        } else {
            this.toast.show(`Documento ` + this.codDocumento + ` no enviado a SAP, contacte con el responsable`, '4000', 'top').subscribe(toast => { });
        }
    }

    public detalleProducto(item: any) {
        this.navCrl.navigateForward(`producto/` + item.ItemCode);
    }

    public eliminarProducto(item: any) {
        this.dialogs.confirm("¿Esta seguro eliminar el producto?", "Xmobile.", ["SI", "NO"]).then((data) => {
            if (data == 1)
                this.eliminde(item.id);
        }).catch(() => {
        })
    }

    public async eliminde(id: any) {
        this.spinnerDialog.show(null, null, true);

        let detalle = new Detalle();

        await detalle.eliminar(id, this.dataexport.tipoDoc, this.dataexport.tipoDocx);

        this.spinnerDialog.hide();
        this.listarDetalle();
    }

    public async listarDetalle(aux = false) {

        console.log("CONSOLA: ---INICIA FUNCION listarDetalle 3333");
        let documentos = new Documentos();

        let detalle = new Detalle();
        this.items = [];
        this.cantidadItems = true;
        let variableaux = 1;
        if(GlobalConstants.Clon == 1){
            variableaux = 0;
        }

        if(GlobalConstants.CabeceraDoc.length > 0){

            console.log("CONSOLA: VALIDA SI HAY DESCUENTO EN CABACERA 3367");
            console.log(GlobalConstants.CabeceraDoc);
            
            if(variableaux == 1 && GlobalConstants.CabeceraDoc[0].tipodescuento >= 0){ 
                for await (let itm of GlobalConstants.DetalleDoc) {
                    console.log("CONSOLA: VALIDA SI LA SUMA DE LOS DESCUENTOS ES MAYOR A 0 3370",itm);
                    if(itm.XMPORCENTAJE+itm.XMPORCENTAJECABEZERA > 0){
                        this.localizacion_calculo.usadecimales = this.userdata[0].usa_redondeo;
                        console.log("CONSOLA: LLAMA FUNCION DEL CALCULO CalculoDescuentocabecera 3374");
                        let calculo = await this.localizacion_calculo.CalculoDescuentocabecera(itm);
                        console.log("CONSOLA: DATOS RETORNADOS DE CalculoDescuentocabecera",calculo)
                        itm.ICEe = calculo.ICEe;
                        itm.ICEp = calculo.ICEp;
                        itm.U_4DESCUENTO = calculo.U_4DESCUENTO;
                        itm.LineTotal = calculo.LineTotal;
                        itm.LineTotalPay = calculo.LineTotalPay;
                    }
                }
            }

            console.log("CONSOLA: LLAMA FUNCION findLocal 3385");
            this.items = await detalle.findLocal(GlobalConstants.DetalleDoc);
            console.log("CONSOLA: LLAMA FUNCION controlPagoslocal 3387->", this.items);
            let doc: any = await this.documentosdata.controlPagoslocal(GlobalConstants.CabeceraDoc, GlobalConstants.DetalleDoc);

            console.log("CONSOLA: LLAMA FUNCION sumaTotalLocal 3387");
            //let totalx: any = await detalle.sumaTotalLocal(GlobalConstants.CabeceraDoc, GlobalConstants.DetalleDoc);
            let totalx: any = await this.localizacion_calculo.sumaTotalLocal(GlobalConstants.CabeceraDoc, GlobalConstants.DetalleDoc);
            console.log("CONSOLA: CARGA DATOS DEL TOTAL",totalx.total);
            this.total = Number(totalx.total).toFixed(2);

            this.descuentoDelTotalPorcentual = 0;
            if (doc.tipodescuento > 0) {
                this.descuentoDelTotalPorcentual = doc.tipodescuento;
            }
            this.descuentoDelTotal = doc.descuento;
            if (Number(doc.descuento) > 0) {
                this.swDescuentoCabezera = true;
            }

            this.isenabled = false;
            let sumLineTotalPay = 0;

            if (this.items.length > 0) {
                this.cantidadItems = true;
                this.cantidadItemsTexto = false;
                this.totalnetox = 0;
                this.totaldescuentox = 0;

                

                for await (let item of this.items) {

                    item.faltalote = 0; 

                    console.log("CONSOLA: LLAMA FUNCION localizacion_calculo.xneto 3419");
                    let sumLineTotal: any = await this.localizacion_calculo.xneto(item);
                    item.xneto = sumLineTotal.xneto;
                    this.totalnetox += sumLineTotal.totalnetox;
                    sumLineTotalPay += sumLineTotal.sumLineTotalPay;
                    this.totaldescuentox +=sumLineTotal.totaldescuentox;
                    
                    /*item.xneto = ((item.Quantity * item.Price) - item.U_4DESCUENTO) + Number(item.ICEe) + Number(item.ICEp);
                    this.totalnetox += (item.Quantity * item.Price);

                    sumLineTotalPay  = sumLineTotalPay+item.xneto; 

                    this.totaldescuentox += parseFloat(item.U_4DESCUENTO);*/

                    if(item.unidadid == "undefined"){
                        item.unidadid = 'Manual';
                    }


                    if(this.userdata[0].ctrl_lote == 0){
                        
                        if( GlobalConstants.CabeceraDoc[0].DocType == 'DFA' && (item.lotes == undefined || item.lotes.length == 0) ){
                            let lotes = new Lotesproductos();
                            let lot: any =  await lotes.select2(item.ItemCode, item.WhsCode);
                            if(lot.length > 0){
                                item.faltalote= 1;
                            }
                        }
                    }


                }
                GlobalConstants.CabeceraDoc[0].DocumentTotalPay = sumLineTotalPay;
            } else {
                this.cantidadItemsTexto = true;
                this.cantidadItems = false;
            }
        }
    }

    public async cambiaestados(dt, est) {
        let documentos = new Documentos();
        await documentos.actualizarconfirmacion(dt, this.idPedido, est);
        this.cambiaEstado();
    }

    public async cambiaEstado(est = 0) {
        if (est != 0) {
            let resp: any = this.documentosdata.find(this.idPedido);
            switch (est) {
                case (2):
                    let opciones: any = { component: PopoverPage, componentProps: this.dataexport };
                    let modalconfirmacion: any = await this.modalController.create(opciones);
                    modalconfirmacion.onDidDismiss().then((data: any) => {
                        if (data.data != 1) {
                            let dt = data.data;
                            dt.plazospago = this.documentosdata.converterTime(dt.plazospago);
                            this.cambiaestados(dt, est);
                            this.isEdit = true;
                        }
                    });
                    return await modalconfirmacion.present();
                    break;
                case (1):
                    this.toast.show(`No puedes realizar esta acción.`, '4000', 'bottom').subscribe(toast => {
                    });
                    break;
            }
        } else {
            this.toast.show(`No puedes realizar esta acción.`, '4000', 'bottom').subscribe(toast => {
            });
        }
        if (this.idDocument > 0) {
            let documentos = new Documentos();
            let resp: any = await documentos.find(this.idDocument);
            this.estadoViewPedido = resp.estadosend;
        }
    }

    public detalleCliente() {
        this.navCrl.navigateForward(`cliente/` + this.CardCode);
    }

    public async descuntoAdicional(descuento: number, x: any) {

        let detalle = new Detalle();
       /* if ((this.swDescuentoCabezera) && (descuento > 0) ) {
            this.toast.show(`Documento ya tiene un descuento de cabezera.`, '2500', 'center').subscribe(toast => {
            });
            return false;
        }*/

        let total = this.total;
        console.log("total ", total);
        console.log("descuento ", descuento);
        console.log(" x ", x);

        if (x == true) {
            if (descuento >= 0 && descuento < 80) {
                this.descuentoDelTotalPorcentual = descuento;
                this.descuentoDelTotal = (parseFloat(descuento.toString()) / 100) * total;
            } else {
                this.toast.show(`Descuento porcentual no valido.`, '4000', 'top').subscribe(toast => {
                });
                return false;
            }
        } else {
            if (descuento >= 0 && descuento <= total) {
                this.descuentoDelTotal = descuento;
                this.descuentoDelTotalPorcentual = 0;
            } else {
                this.toast.show(`Descuento moneda local no valido.`, '4000', 'top').subscribe(toast => {
                });
                return false;
            }
        }
        
        let documentos: Documentos = new Documentos();
        console.log("this.localizacion:", this.localizacion);

        console.log("this.descuentoDelTotal ", this.descuentoDelTotal);
        console.log("this.descuentoDelTotalPorcentual ", this.descuentoDelTotalPorcentual);
        console.log("this.idPedido ", this.idPedido);

        this.bonificacionesService.descuentoCabezeraPorcentual(this.descuentoDelTotal, this.descuentoDelTotalPorcentual, this.idPedido);

        this.listarDetalle();
    }

    public async masdescuento() {
        if (this.userdata[0].config[0].descuentosDocumento == '0' ) {
            this.toast.show(`No está permitido para asignar descuentos a nivel documento.`, '2500', 'center').subscribe(toast => {
            });
            return false;
        }


        let detalle = new Detalle();
        // let totalx: any = await detalle.sumaTotal(this.idPedido);
        let total = this.total
        let alert: any = await this.alertController.create({
            header: 'DESCUENTO EN MONEDA LOCAL.',
            inputs: [{
                name: 'data',
                type: 'number',
                min: 1,
                max: total,
                value: '',
                placeholder: '0'
            }],
            buttons: [{
                text: 'CANCELAR',
                role: 'cancel',
            }, {
                text: 'CONTINUAR',
                handler: (data: any) => {
                    let xx = parseFloat(data.data);
                    if (xx >= 0 && xx <= total) {
                        console.log("es cero xx", xx)
                        this.descuntoAdicional(xx, false);
                        // this.swDescuentoMonetario = true;
                    } else {
                        this.toast.show(`El número introducido es mayor el total del documento.`, '4000', 'top').subscribe(toast => {
                        });
                    }
                }
            }]
        });
        await alert.present();
    }

    public async masdescuentoporsentual() {
        if (this.userdata[0].config[0].descuentosDocumento == '0') {
            this.toast.show(`No está permitido para esta acción .`, '2500', 'center').subscribe(toast => {
            });
            return false;
        }
        // if ( this.swDescuentoPorcentual) {
        //     this.toast.show(`Documento ya tiene un descuento de cabezera porcentual.`, '2500', 'center').subscribe(toast => {
        //     });
        //     return false;
        // }
        if (this.clonado != '0') {
            // this.toast.show(`No está permitido para esta acción .`, '2500', 'center').subscribe(toast => {
            //  });
            return false;
        }

        let aux_session: any = await this.configService.getSession();

        /*if (aux_session[0].localizacion == 2) {
            if (this.descuentoDelTotalPorcentual > 0) {
                return this.toast.show(`Ya se hizo un descuento global.`, '4000', 'top').subscribe(toast => {
                });
            }
        }*/

        //if (this.descuentoDelTotalPorcentual == 0) {
            // let totalx: any = await this.documentosdata.controlPagos(this.idPedido);
            // let total = totalx.totalNeto;
        let total = this.total;
        let alert: any = await this.alertController.create({
            header: 'DESCUENTO GLOBAL(%).',
            message: "Adiciona un descuento a todos los items de tu documento, No se puede adicionar o editar items después de hacer esta acción. ",

            inputs: [{
                name: 'data',
                type: 'number',
                value: this.descuentoDelTotalPorcentual,
                min: 1,
                max: 100,
                placeholder: '5'
            }],
            buttons: [{
                text: 'CANCELAR',
                role: 'cancel',
            }, {
                text: 'CONTINUAR',
                handler: (data: any) => {
                    let xx = parseFloat(data.data);
                    //alert(xx);
                    console.log("xx ", xx);
                    if (xx >= 0 && xx < 100) {

                        console.log(xx + "  " + this.userdata[0].config[0].totalDescuentoDocumento);
                        if (xx > this.userdata[0].config[0].totalDescuentoDocumento) {
                            this.toast.show('El descuento no debe superar el  ' + this.userdata[0].config[0].totalDescuentoDocumento + '%. ', '2500', 'top').subscribe(toast => {
                            });
                            return false;
                        } else {
                            this.descuentoDelTotalPorcentual = xx;
                            let porcentaje = Calculo.porcentaje(total, xx);
                            this.descuntoAdicional(xx, true);

                        }

                    } else {
                        this.toast.show(`El número no se encuentra en el rango permitido.`, '4000', 'top').subscribe(toast => { });
                    }
                }
            }]
        });
        await alert.present();
        //} else {
        //    this.toast.show(`Ya se hizo un descuento global.`, '4000', 'top').subscribe(toast => {
        //    });
        //}
    }

    public async changeDate(event: any) {
        this.dataexport.fechaentrega = Tiempo.formatFecha(event.detail.value);
    }

    public async agregarProductos() {        
        console.log("CONSOLA:INICIA LA FUNCION agregarProductos 3695");
        console.log("CONSOLA:LLAMA A LA FUNCION limpiarBonificacion 3695");
        this.limpiarBonificacion(1);

        if (this.litPreciosSelect.PriceListName) {
            if (!this.SWaddProducto) {
                this.dialogs.confirm(`Si adiciona producto(s) no podrá cambiar los siguientes datos : Lista de precios ${this.litPreciosSelect.PriceListName}  y grupo de productos ${this.grupoproductostext}.`, "Xmobile.", ["Continuar", "Cancelar"]).then(async (data) => {
                    switch (data) {
                        case (1):
                            this.openModalDatalleVenta();
                            //this.SWaddProducto = true;
                            break;
                    }
                });
            }
        } else {
            this.toast.show(`Selecciona una lista de precios.`, '4000', 'top').subscribe(toast => {
            });
        }
        if (this.SWaddProducto) {
            console.log("datos del detalle",JSON.stringify(GlobalConstants.DetalleDoc));
            this.openModalDatalleVenta();
        }
    }

    async openModalDatalleVenta() {
        console.log("openModalDatalleVenta ");        
        this.dataexport.tipoDocx = this.tipoFactura;
        this.dataexport.grupoBonificacion = 0;
        console.log("this.dataexport ", this.dataexport);
        let mcproducto: any = { component: ModalproductoPage, componentProps: this.dataexport };
        console.log("producto", mcproducto);        
        let modalproducto: any = await this.modalController.create(mcproducto);
        
        modalproducto.onDidDismiss().then(async (data: any) => {
            console.log("modalproducto.onDidDismiss() ");
            if (data.data != 0 && typeof data.data != "undefined") {
                this.SWaddProducto = true;

                console.log("DEVD on dissmiss  this.SWaddProducto  ", this.SWaddProducto);
                this.valkidBonificacionesCompras();
                this.controlStatus = 3;
                this.tipo = data.data;
                this.idPedido = data.data;
                this.dataexport.idPedido = this.idPedido;
                this.iniciando(false);
            }

        });
        (await modalproducto).present();
        console.log("se abrio el modal");
    }

    public cambioProductoCombo(item: any) {
        console.log(item);
    }

    public async quemarBonificaciones() {
        //debugger;
        let masterBoni: any;
        console.log("****** normal1 ", this.items);
        console.group("LOGICA DE QUEMAR BONOS");
        console.log("****** todos BonificacionesUsados ", this.newBonificacionesUsados);

        if (this.newBonificacionesUsados.length > 0) {
            for (let item of this.newBonificacionesUsados) {
                console.log("--> each bonificables ", item);
                let bonificable = item;
                //   console.log("select bonificacion ", bonificable);
                masterBoni = await this.Bonificacion_ca.getFind(bonificable.code_bonificacion_cabezera, this.territorioCliente);
                
                console.log("masterBoni ", masterBoni);
                console.log("****** normal2 ", this.items);

                /**
                 * PARAMETROS DE CONFIGURACION 
                 */
                if ((masterBoni[0].cabezera_tipo == "BONO" || masterBoni[0].cabezera_tipo == "BONIFICACION")
                    && bonificable.totalCantidad >= masterBoni[0].cantidad_compra && masterBoni[0].cabezera_tipo != "DESCUENTO"

                ) {
                    // console.log("productos a regalar todos ", await this.bonificacion_regalos.showTable());
                    console.log("bonificable.code_bonificacion_cabezera ", bonificable.code_bonificacion_cabezera);
                    this.productosAregalar = await this.bonificacion_regalos.findOne(bonificable.code_bonificacion_cabezera, this.territorioCliente);

                    console.log("****** normal3 ", this.items);
                    console.log("Genera productosAregalar ", this.productosAregalar);
                    //  console.log("all regalos ", await this.bonificacion_regalos.showTable());
                    this.dataexport.grupoBonificacion = 1;

                    if (this.productosAregalar.length > 0) {
                        console.log("****** normal4 ", this.items);
                        console.log("DEVD hay productos a regalar ");
                        console.log("DEVD masterBoni ", masterBoni);

                        this.productosAregalar.cantidadConsumo = bonificable.totalCantidad;
                        this.productosAregalar.opcional = masterBoni[0].opcional;

                        await this.eliminarItemBonificado(bonificable);
                        console.log("****** normal5 ", this.items);

                        await this.actionBonificaciones(this.productosAregalar)
                            .then(response => console.log("DEVD RETORNO DEL MODAL ", response))
                            .catch(err => console.log("DEVD RETORNO DEL MODAL ", err))
                        console.log("****** normal6 ", this.items);
                        console.log("----> regalar ", this.productosAregalar);
                    } /*else {
                        this.toast.show(`No existen productos registrados para regalar, sincronice nuevamente.`, '4000', 'top').subscribe(toast => {
                        });
                    }*/
                } /*else {
                    if (masterBoni[0].cabezera_tipo != "DESCUENTO") {
                        //this.toast.show(`Debes tener al menos  ${masterBoni[0].cantidad_compra} items bonificables.`, '4000', 'top').subscribe(toast => {
                        //});
                        // this.eliminarItemBonificado(bonificable);
                    }
                }*/

                if (masterBoni[0].cabezera_tipo == "DESCUENTO" || masterBoni[0].cabezera_tipo == "DESCUENTO LINEA"
                    && bonificable.totalCantidad >= masterBoni[0].cantidad_compra
                    // && masterBoni[0].maximo_regalo == 0
                    // && masterBoni[0].grupo_cliente == 0
                    // && masterBoni[0].tipo == "PRODUCTOS ESPECIFICOS"
                ) {
                    console.log("****** normal7 ", this.items);
                    console.log("masterBoni  es descuento ", masterBoni);
                    this.productosAregalar = await this.bonificacion_regalos.findOne(bonificable.code_bonificacion_cabezera, this.territorioCliente);
                    console.log("Genera productosAregalar ", this.productosAregalar);
                    console.log("****** normal8 ", this.items);

                    this.eliminarItemBonificado(bonificable);
                    this.dataexport.grupoBonificacion = 1;
                    console.log("****** normal9 ", this.items);
                    if (masterBoni[0]) { }
                    let addButton: any;
                    if (masterBoni[0].opcional == 'OPCIONAL') {
                        addButton = {
                            text: 'OMITIR',
                            handler: async (blah) => {
                                console.log('Confirm Cancel: blah');
                                await this.quemarBonificaciones();
                            }
                        };
                    } else {
                        addButton = '';
                    }
                    console.log("****** normal9.0 ", this.items);

                    let alert2: any = await this.alertController.create({
                        header: '% DESCUENTO: ' + masterBoni[0].cantidad_regalo + '%',
                        subHeader: 'Descuento:' + masterBoni[0].nombre,
                        backdropDismiss: false,
                        inputs: [{
                            name: 'data',
                            type: 'number',
                            value: ""+masterBoni[0].cantidad_regalo,
                            min: 1,
                            max: 100,
                            placeholder: '0',
                            disabled: masterBoni[0].fijo &&Number(masterBoni[0].fijo)==1?true:false
                        }],
                        buttons: [addButton, {
                            text: 'CONTINUAR',
                            handler: async (data: any) => {
                                console.log("****** normal9.1 ", this.items);
                                let value = parseFloat(data.data);
                                console.log("****** normal9.2 ", this.items);
                                if (value > 0 && value < 100) {
                                    console.log("****** normal10 ", this.items);
                                    console.log("validar ");
                                    console.log("value ", value, " > ", masterBoni[0].cantidad_regalo);
                                    console.log("cantidad_regalo ", parseFloat(masterBoni[0].cantidad_regalo));
                                    console.log("extra_descuento  ", parseFloat(masterBoni[0].extra_descuento));
                                    console.log("(parseFloat(masterBoni[0].cantidad_regalo) + parseFloat(masterBoni[0].extra_descuento)) ", (parseFloat(masterBoni[0].cantidad_regalo) + parseFloat(masterBoni[0].extra_descuento)));
                                    if (value <= masterBoni[0].cantidad_regalo) {
                                        console.log("****** normal ");
                                        console.log("****** normal ", this.items);
                                        this.descuentoBonificacion(value, masterBoni[0].code, masterBoni, 0);
                                        console.log("****** normal ", this.items);
                                        // this.toast.show(`El porcentaje ${value} no debe superar los ${masterBoni[0].cantidad_regalo}%`, '6000', 'top').subscribe(toast => {
                                        // });
                                    } else if (value <= (parseFloat(masterBoni[0].cantidad_regalo) + parseFloat(masterBoni[0].extra_descuento))) {
                                        value = Number(Number(value).toFixed(2));
                                        const alert = await this.alertController.create({
                                            cssClass: 'my-custom-class',
                                            header: 'Está seguro?',
                                            backdropDismiss: false,
                                            message: ' Adicionar el descuento de <strong>' + value + '%</strong> ya que supera el límite normal.',
                                            buttons: [
                                                {
                                                    text: 'Cancel',
                                                    role: 'cancel',
                                                    cssClass: 'secondary',
                                                    handler: async (blah) => {
                                                        console.log('Confirm Cancel: blah');
                                                        if (this.newBonificacionesUsados.length > 0) {
                                                            console.log("---> recursivo desde descuentos ");
                                                            //this.descuentoBonificacion(value, masterBoni[0].code, masterBoni, );
                                                            // await this.quemarBonificaciones();
                                                        } else {
                                                            console.log("---> llamar a popover ");
                                                            // this.guardarPedidoBonificado();
                                                        }
                                                    }
                                                }, {
                                                    text: 'OK',
                                                    handler: () => {
                                                        console.log('Confirm Okay');
                                                        this.descuentoBonificacion(value, masterBoni[0].code, masterBoni, 1);
                                                    }
                                                }
                                            ]
                                        });
                                        await alert.present();
                                        return false;
                                    } else {
                                        this.toast.show(`El porcentaje no debe superar los ${masterBoni[0].cantidad_regalo}% + ${masterBoni[0].extra_descuento}% `, '6000', 'top').subscribe(toast => {
                                        });
                                        //  await alert2.present();
                                        return false;
                                    }
                                    // this.newBonificacionesUsados = [];
                                } else {
                                    this.toast.show(`El número no se encuentra en el rango permitido.`, '4000', 'top').subscribe(toast => {
                                    });
                                    // await alert2.present();
                                    return false;
                                }
                            }
                        }]
                    });
                    await alert2.present();
                    return false;
                    //this.actionBonificaciones(this.productosAregalar);
                }
                /* else {
                     if (masterBoni[0].cabezera_tipo != "BONO") {
                         //  this.eliminarItemBonificado(bonificable);
                         // this.toast.show(`Debes tener al menos  ${masterBoni[0].cantidad_compra} items para el descuento.`, '4000', 'top').subscribe(toast => {
                         // });
                     }
 
                 }*/
                //this.databoniexpo = resultadox;
                //this.bonificaciontext = resultadox.nombre;
                //this.bonificacioncode = resultadox.U_ID_bonificacion;
                //this.dataexport.grupoBonificacion = this.bonificacioncode;
                // this.dataexport.grupoBonificacion = 1;
                //this.actionBonificaciones(bonificable);
                //this.eliminarItemBonificado(bonificable);
            }

            console.groupEnd();
        } else {
            console.log("---> fion quemar bopnificaciones ", this.newBonificacionesUsados);
            console.log("---> llamar a popover ");
            this.guardarPedidoBonificado();
        }
    }

    eliminarItemBonificado(bonificable) {
        var i = this.newBonificacionesUsados.indexOf(bonificable);
        if (i !== -1) {
            this.newBonificacionesUsados.splice(i, 1);
        }
        console.log("**** eliminado  this.newBonificacionesUsados ", this.newBonificacionesUsados);
    }

    async descuentoBonificacion(value, code_cabezera, masterBoni, esExtra) {
        console.log("DEVD masterBoni", masterBoni);
        console.log("DEVD esExtra", esExtra);

        let detalle = new Detalle();
        //let totalx: any = await detalle.sumaTotal(this.idPedido);
        // let total = totalx.total;
        //    console.log("total ", total);
        let documentos = new Documentos();

        let codessadosBoniMaster: any = await this.Bonificacion_ca.getCompraUsados();
        console.log("codessadosBoniMaster ", codessadosBoniMaster);
        codessadosBoniMaster = codessadosBoniMaster.filter((value) => value.code_bonificacion_cabezera == code_cabezera);
        console.log("codessadosBoniMaster filtrado para actualizar desc ", codessadosBoniMaster);

        console.log("this.items  ", this.items);
        
        let datauser: any = await this.configService.getSession();

        for  (let i = 0; i < codessadosBoniMaster.length; i++) {
            console.log("codessadosBoniMaster i code_compra ", codessadosBoniMaster[i].code_compra);
            console.log("this.items  ", this.items);

            let item;
            console.log("codessadosBoniMaster[i].cantidad  ", codessadosBoniMaster[i].cantidad);
            console.log("codessadosBoniMaster[i].cantidad  ", codessadosBoniMaster[i].cantidad);
            item = this.items.filter((value) => {
                return codessadosBoniMaster[i].code_compra == value.ItemCode && codessadosBoniMaster[i].cantidad == (value.Quantity * value.BaseQty);
            });

            console.log("FILTRADO item  ", item);

            await detalle.updateDescuentoLinea(this.idPedido, value, item[0].id, masterBoni[0].codeMid, esExtra, ((item[0].Price * item[0].Quantity) - item[0].U_4DESCUENTO),datauser[0].usa_redondeo);
            await this.Bonificacion_ca.DeleteBoniUsadasOne(codessadosBoniMaster[i].code_compra, codessadosBoniMaster[i].code_bonificacion_cabezera);
        }
        console.log("this.newBonificacionesUsados ", this.newBonificacionesUsados);
        if (this.newBonificacionesUsados.length > 0) {
            console.log("---> recursivo desde descuentos ");
            await this.quemarBonificaciones();
        } else {
            console.log("---> llamar a popover ");
            this.guardarPedidoBonificado();
        }

        this.toast.show(`Descuento adicionado.`, '4000', 'center').subscribe(toast => { });
        //this.listarDetalle();
    }

    public async actionBonificaciones(bonificable) {
        // this.dataexport.tipoDocx = this.tipoFactura;        
        this.dataexport.databoni = bonificable;
        this.dataexport.territorioCliente = this.territorioCliente;
        // this.dataexport.grupoBonificacion = this.bonificacioncode;
        console.log("bonificable ", bonificable);
        return new Promise(async (resolve, reject) => {
            let mcproducto: any = { component: ModalproductoPage, componentProps: this.dataexport };
            let modalproducto: any = await this.modalController.create(mcproducto);
            modalproducto.onDidDismiss().then(async (data: any) => {
                let verificaAccion = false;
                console.log("dissmiss modal bonificaciones ", this.newBonificacionesUsados);
                if (this.newBonificacionesUsados.length > 0) {
                    verificaAccion = true;
                    console.log("llamar otra ves a quemar  ");
                    await this.quemarBonificaciones();
                    // let detalle = new Detalle();
                    //  this.items = [];
                    // this.cantidadItems = true;
                    // this.items = await detalle.findAll(this.idPedido);
                    //  console.log("this.items  **** ", this.items);
                } else {
                    verificaAccion = true;
                    // let ver = false;
                    if (localStorage.getItem("cancelado") == "NO" || localStorage.getItem("stockBoni") == "2" || localStorage.getItem("stockBoni") == "3") {
                        console.log("llamar a popover ");
                        this.guardarPedidoBonificado();
                        // } else {
                        //     console.log("ALGO SALIO MAL AL CERRAR MODAL");
                        // }
                    } else {
                        if (localStorage.getItem("omitir") == "SI") {
                            console.log("omitido");
                            await this.quemarBonificaciones();
                        } else {
                            this.limpiarBonificacion(0);
                            console.log("modal cancelado ");
                        }
                    }
                }
                if (verificaAccion) {
                    resolve(true);
                } else {
                    reject(false);
                }
            });
            await modalproducto.present();
        })
    }

    async guardarPedidoBonificado() {
        this.geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 10000 }).then((resp) => {
            if (resp) {
                console.log('resp GEOPOSITION SIN PROBLEMAS', resp);
                let obj: any = { lat: resp.coords.latitude, lng: resp.coords.longitude };
                console.log("registerLocation() ", "" + obj);
                localStorage.setItem("lat", obj.lat);
                localStorage.setItem("lng", obj.lng);
                GlobalConstants.Longitud = obj.lng;
                GlobalConstants.latitud = obj.lat;
            }
        }).catch(error => {
            console.log("error 1 ", error);
        });

        GlobalConstants.CabeceraDoc[0].U_LONGITUD = GlobalConstants.Longitud;
        GlobalConstants.CabeceraDoc[0].U_LATITUD = GlobalConstants.latitud;

        /*GlobalConstants.auxiliarclondetalle = JSON.stringify(GlobalConstants.DetalleDoc);
        GlobalConstants.auxiliarcloncabeceras = JSON.stringify(GlobalConstants.CabeceraDoc);
        console.log("DATOS CLONADOS",  JSON.stringify(GlobalConstants.auxiliarclondetalle));
        console.log("DATOS CLONADOS",  JSON.stringify(GlobalConstants.auxiliarcloncabeceras));*/

        try {
            let ox: any = await this.configService.getSession();            
            // if (ox[0].config[0].modInfTributaria == '1') {
            if (this.tipoDoc == 'DOE') {
                await this.guardarEntrega()
            }
            else{
                console.log("this.pedidoData  send props ", this.pedidoData)
                let mcproducto: any = { component: PopoverPage, componentProps: this.pedidoData, id: "modalpedido" };
                let modalpoper: any = await this.modalController.create(mcproducto);
                
                modalpoper.onDidDismiss().then(async (data: any) => {
                    console.log("data.data dissmis popover  ", data.data);
                    this.spinnerDialog.show(null, null, true);
                    if (localStorage.getItem("cancelado") == "NO") {
                        if (typeof data.data === 'object') {
                            console.log("DISSMIS OBJETO ");
                        } else {
                            //if (localStorage.getItem("esClon") == "NO") {
                            console.log("Detalle", JSON.stringify(GlobalConstants.DetalleDoc));
                            this.limpiarBonificacion(1);
                            console.log("Detalle", JSON.stringify(GlobalConstants.DetalleDoc));
                            //GlobalConstants.auxiliarclondetalle = '';
                            //GlobalConstants.auxiliarcloncabeceras = '';
                            //}
                            console.log("modal cancelado ");
                        }this.spinnerDialog.hide();
                    }
    
                    if (data.data == 100) {
                        this.spinnerDialog.hide();
                        this.navCrl.pop();
                        return false;
                    }
    
                    if (this.tipoFactura == 100) {
                        if (data.data != '1') {
                            this.spinnerDialog.hide();
                            await this.saveDocument(data.data);
                        }
                    } else {
                        if (data.data != '1') {
                            data.data.reserva = this.tipoFactura;
                            this.spinnerDialog.hide();
                            await this.saveDocument(data.data);
                        }
                    }
    
                    if (this.tipoDoc == 'DFA') {
                        if (this.items.length > 0) for await (let serie of this.items) {
                            let seriesx = new Seriesproductos();
                            await seriesx.updateSave(serie.id);
                            this.spinnerDialog.hide();
                        }
                    }                
                });
    
                return await modalpoper.present();
            }
            
        } catch (e) {
            console.log(e);
        }
    }

    public async guardarEntrega(){
        try {
            let ox: any = await this.configService.getSession();            
            // if (ox[0].config[0].modInfTributaria == '1') {
            console.log("this.pedidoData  send props Transportista ------>", this.pedidoData)
            let mcTransportista: any = { component: ConfiguracionEntregaPage, componentProps: this.pedidoData, id: "modalpedido" };
            let modalTransportista: any = await this.modalController.create(mcTransportista);
            
            modalTransportista.onDidDismiss().then(async (data1: any) => {
                console.log("data.data dissmis transportista  ", data1.data);
                let mcproducto: any = { component: PopoverPage, componentProps: data1.data, id: "modalpedido" };
                let modalpoper: any = await this.modalController.create(mcproducto);
                
                const _transportista: any = data1.data.transportista;

                modalpoper.onDidDismiss().then(async (data: any) => {
                    console.log("data.data dissmis popover  ", data.data);
                    this.spinnerDialog.show(null, null, true);
                    if (localStorage.getItem("cancelado") == "NO") {
                        if (typeof data.data === 'object') {
                            console.log("DISSMIS OBJETO ");
                        } else {
                            //if (localStorage.getItem("esClon") == "NO") {
                                console.log("Detalle", JSON.stringify(GlobalConstants.DetalleDoc));
                                this.limpiarBonificacion(1);
                                console.log("Detalle", JSON.stringify(GlobalConstants.DetalleDoc));
                                //GlobalConstants.auxiliarclondetalle = '';
                                //GlobalConstants.auxiliarcloncabeceras = '';
                                //}
                                console.log("modal cancelado ");
                            }this.spinnerDialog.hide();
                        }
                        
                    // debugger;   
                    data.data.transportista = _transportista;             
                    if (data.data == 100) {
                        this.spinnerDialog.hide();
                        this.navCrl.pop();
                        return false;
                    }
    
                    if (this.tipoFactura == 100) {
                        if (data.data != '1') {
                            this.spinnerDialog.hide();
                            await this.saveDocument(data.data);
                        }
                    } else {
                        if (data.data != '1') {
                            data.data.reserva = this.tipoFactura;
                            this.spinnerDialog.hide();
                            await this.saveDocument(data.data);
                        }
                    }
    
                    if (this.tipoDoc == 'DFA') {
                        if (this.items.length > 0) for await (let serie of this.items) {
                            let seriesx = new Seriesproductos();
                            await seriesx.updateSave(serie.id);
                            this.spinnerDialog.hide();
                        }
                    }                
                });

                return await modalpoper.present();
            });

            await modalTransportista.present();

        } catch (e) {
            console.log(e);
        }
    }

    public async limpiarBonificacion(accion = 0) {
        console.log("CONSOLA:INICIA LA FUNCION limpiarBonificacion 4201");        
        if((GlobalConstants.auxiliarcloncabeceras) && (GlobalConstants.auxiliarcloncabeceras.length>0)){
            console.log("CONSOLA: VALIDA SI LAS VARIABLES LOCALES TIENEN DATOS 4205");

            GlobalConstants.DetalleDoc = [];
            let tipo_pago = GlobalConstants.CabeceraDoc[0].PayTermsGrpCode
            GlobalConstants.CabeceraDoc = JSON.parse(GlobalConstants.auxiliarcloncabeceras);
            GlobalConstants.CabeceraDoc[0].tipoestado = 'new';
            let auxdetalle: any;
            auxdetalle = JSON.parse(GlobalConstants.auxiliarclondetalle);

            console.log("CONSOLA: VALIDA SI EL DOCUMENTO ES UN CLON 4214");
            if(GlobalConstants.CabeceraDoc[0].clone == 0 ){

                for (let i = 0; i < auxdetalle.length; i++) {
                    if (auxdetalle[i].codeBonificacionUse == 0 || auxdetalle[i].codeBonificacionUse == 2) {
                        GlobalConstants.DetalleDoc.push(auxdetalle[i]);
                    }
                }
            }else{
                console.log("CONSOLA: VALIDA SI EL DOCUMENTO ES DFA 4223");
                if(GlobalConstants.CabeceraDoc[0].DocType == 'DFA'){
                    for (let i = 0; i < auxdetalle.length; i++) {
                        GlobalConstants.DetalleDoc.push(auxdetalle[i]);
                    }
                }else{
                    for (let i = 0; i < auxdetalle.length; i++) {
                        if (auxdetalle[i].codeBonificacionUse == 0 || auxdetalle[i].codeBonificacionUse == 2) {
                            GlobalConstants.DetalleDoc.push(auxdetalle[i]);
                        }
                    }
                }
            }
            
            console.log("CONSOLA: RECORRE LOS ITEMS Y INICIALIZA LAS VARIABLES DE BONIFICACION EN 0");
            for (let x = 0; x < GlobalConstants.DetalleDoc.length; x++) {
                GlobalConstants.DetalleDoc[x]['U_4DESCUENTOBef_line'] = 0;
                GlobalConstants.DetalleDoc[x]['U_4DESCUENTOBef_cab'] = 0;
                GlobalConstants.DetalleDoc[x]['U_4DESCUENTOBoni'] = 0;
                GlobalConstants.DetalleDoc[x]['LineTotalPayBoni'] = 0;
                GlobalConstants.DetalleDoc[x]['U_4DESCUENTO'] = GlobalConstants.DetalleDoc[x]['DiscTotalMonetary']; 
            }

            if(accion == 0 && GlobalConstants.CabeceraDoc[0].tipodescuento >= 0){ 
                for await (let itm of GlobalConstants.DetalleDoc) {
                    console.log(itm);

                    let porcentaje = itm.XMPORCENTAJE;
                    let precio = itm.Price*itm.Quantity;
                    let descuento = precio*(porcentaje/100);
                    let valor = precio-descuento;
                    let iva = valor*0.13;
                    let neto = valor-iva;
                    let valorice = neto*(itm.icetp/100);
                    let icees = itm.Quantity*itm.icete*itm.BaseQty;

                    itm.ICEe = icees;
                    itm.ICEp = valorice;
                    itm.U_4DESCUENTO = descuento;
                    itm.LineTotal = (icees+valorice+precio);
                    itm.LineTotalPay = (icees+valorice+valor);
                    itm.XMPORCENTAJECABEZERA = 0;
                }

                GlobalConstants.CabeceraDoc[0].tipodescuento = 0;
            }

            this.items.forEach(linea=>{
                linea.U_4DESCUENTO=linea.U_4DESCUENTOBef_line;
                linea.U_4DESCUENTOBef_line=0;
                linea.U_4DESCUENTOBef_cab=0;
                linea.U_4DESCUENTOBoni=0;
            });
        }

        GlobalConstants.auxiliarclondetalle = "";
        GlobalConstants.auxiliarcloncabeceras = "";

        let detalles = new Detalle();
        let documentosdata = new Documentos();
        let documento = await documentosdata.findOne(this.idPedido);

        let hayBonificacion = [];
        let itemsAux = await detalles.findAll(this.idPedido);

        let descDocumento = 0;
        for await (let item of itemsAux) {

            if (item.bonificacion > 0) {
                hayBonificacion.push(item);
            }
            if (item.XMPORCENTAJECABEZERA > 0) {
                descDocumento = item.XMPORCENTAJECABEZERA;

            }
        }

        let boniReset = 0;
        if (hayBonificacion.length > 0) {
            for await (let item of hayBonificacion) {
                if (item.bonificacion == 1) {
                    boniReset = 1;
                    detalles.updateBonificacionLineaReset(item.id);
                }
                if (item.bonificacion > 1) {
                    boniReset = 2;
                    detalles.updateDescuentoLineaReset(this.idPedido, item.id, item.LineTotal);
                }
                setTimeout(() => {
                    this.listarDetalle();
                }, 1000);

            }


        }

        this.listarDetalle();

    }

    public async fromevidencia() {        
        try {
            let document = new Documentos;
            let envio = await document.consultaEnvioEvidencia(GlobalConstants.CabeceraDoc[0].cod)

            console.log("ENVIO ROMERO",envio[0].EnvioEvidencia);

            if(envio[0].EnvioEvidencia == 1){
                this.toast.show("Ya se han enviado evidencias para este documento", "3000", "top").subscribe(toast => {
                });
            }else{
                let fromevidenciamodal: any = { component: FromevidenciaPage,componentProps: GlobalConstants.CabeceraDoc[0]};
                let modalfromevidencia: any = await this.modalController.create(fromevidenciamodal);
                modalfromevidencia.onDidDismiss().then(async (data: any) => {
                    console.log("data.data dissmis popover  ", data.data);
                });
                return await modalfromevidencia.present();
            }
        } catch (e) {
            console.log("error");
            console.log(e);
        }
    }

    /*async muestrafirma(){
        try {
            console.log("sss");
            let firmamodal: any = { component: FirmaPage,componentProps: "0"};
            console.log("sss1");
            let modalfirma: any = await this.modalController.create(firmamodal);
            console.log("sss2");

            modalfirma.onDidDismiss().then(async (data: any) => {
                console.log("data.data dissmis popover  ", data.data);
            });
            return await modalfirma.present();
        } catch (e) {
            console.log("error");
            console.log(e);
        }
    }*/
}
