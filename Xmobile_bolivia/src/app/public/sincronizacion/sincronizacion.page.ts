import { Component, OnInit } from '@angular/core';
import { DataService } from "../../services/data.service";
import { ConfigService } from "../../models/config.service";
import { Databaseconf } from "../../models/databaseconf";
import { Productos } from "../../models/productos";
import { Clientes } from "../../models/clientes";
import { NavController } from "@ionic/angular";
import { Toast } from "@ionic-native/toast/ngx";
import { Clientessucursales } from "../../models/clientessucursales";
import { Nit } from "../../models/nit";
import { Documentos } from "../../models/documentos";
import { Detalle } from "../../models/detalle";
// import { Pagos } from "../../models/pagos";
import { Pagos } from "../../models/V2/pagos";
import { Condicionpago } from "../../models/condicionpago";
import { Tiposempresa } from "../../models/tiposempresa";
import { Contactos } from "../../models/contactos";
import { File, FileEntry } from '@ionic-native/file/ngx';
import { FileTransfer, FileTransferObject } from '@ionic-native/file-transfer/ngx';
import { Productosalmacenes } from "../../models/productosalmacenes";
import { Productosprecios } from "../../models/productosprecios";
import { Descuentos } from "../../models/descuentos";
import { Agendas } from "../../models/agendas";
import { Anular } from "../../models/anular";
import { HttpClient } from '@angular/common/http';
import { Dialogs } from "@ionic-native/dialogs/ngx";
import { Combos } from "../../models/combos";
import { Centrocostos } from "../../models/centrocostos";
import { Bancos } from "../../models/bancos";
import { Geolocalizacion } from "../../models/geolocalizacion";
import { Seriesproductos } from "../../models/seriesproductos";
import { Lotesproductos } from "../../models/lotesproductos";
import { Reimpresion } from "../../models/reimpresion";
import { Bonificaciones as Bonificacion_ca } from '../../models/V2/bonificaciones';
import { bonificacion_regalos } from '../../models/bonificacion_regalos';
import { bonificacion_compras } from '../../models/bonificacion_compras';
import { Dosificacionproductos } from "../../models/dosificacionproductos";
import { NativeStorage } from "@ionic-native/native-storage/ngx";
import * as moment from 'moment';
import { Network } from "@ionic-native/network/ngx";
import { Documentopago } from "../../models/documentopago";
import { companex_canal } from "../../models/companex_canal";
import { companex_subcanal } from "../../models/companex_subcanal";
import { companex_cadena } from "../../models/companex_cadena";
import { companex_tipotienda } from "../../models/companex_tipotienda";
import { companex_consolidador } from "../../models/companex_consolidador";
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { DirectoryEntry } from '@ionic-native/file';
import { AndroidPermissions } from '@ionic-native/android-permissions/ngx';
import { Territorios } from '../../models/territorios';


import { promocionaes } from '../../models/promociones';
import { bonificacionesDocCabezera } from '../../models/bonificacionDocCabezera.';
import { IPagos } from '../../types/IPagos';
import { PagosService } from '../../services/pagos.service';
import { ConfigLayautService } from 'src/app/services/config-layaut.service';
import { IConfigLayaut } from 'src/app/types/IConfiglayaut';

//Percepciones CUB
import { Tarjetas } from 'src/app/models/tarjetas';
import { Grupospercepciones } from 'src/app/models/grupospercepciones';
import { Clientespercepciones } from 'src/app/models/clientespercepciones';
import { Gestionsap } from 'src/app/models/gestionsap';
import { Almacenespercepciones } from 'src/app/models/almacenespercepciones';
import { Servicioventas } from 'src/app/models/servicioventas';
import { Indicadoresimpuestos } from 'src/app/models/indicadoresimpuestos';
import { Configuracionimpuestos } from 'src/app/models/configuracionimpuestos';
import { PerTipoOperaciones } from 'src/app/models/pertipooperaciones';
import { PerTipoPrecioVenta } from 'src/app/models/perpreciotipoventa';
import { PerFexAfectacionIgv } from 'src/app/models/perfexafectacionigv';
import { PerTransportista } from 'src/app/models/pertransportistas';
import { States } from 'src/app/models/States';
import { Provincias } from 'src/app/models/provincias';
import { Distritos } from 'src/app/models/distritos';

declare let window: any;


@Component({
    selector: 'app-sincronizacion',
    templateUrl: './sincronizacion.page.html',
    styleUrls: ['./sincronizacion.page.scss'],
})
export class SincronizacionPage implements OnInit {
    public clientes: boolean;
    public productos: boolean;
    public almacenes: boolean;
    public cambio: boolean;
    public listaprecios: boolean;
    public productosalmacenes: boolean;
    public clientessucursales: boolean;
    public productosprecios: boolean;
    public lotes: boolean;
    public loadlotes: boolean;
    public loadproductosprecios: boolean;
    public loaddatosexport: boolean;
    public isenabled: boolean;
    public contador: number;
    public totalexportpagos: number;
    public dataExportPagos: IPagos[];
    public todos: boolean;
    public estados: any;
    public datosexport: boolean;
    public importar: any;
    public path: any;
    public c: number;
    public id: number;
    public total: number;
    public totalImg: number;
    public totalImgCount: number;
    public arrxx: any;
    public arraux: any;
    public diasSemana: any;
    public totalexport: number;
    public totalexportClientes: number;
    public clientesexport: boolean;
    public clientesexportload: boolean;
    public clientesexportTotal: number;
    public xBack: boolean;
    public xDownloadData: Array<any>;
    public xDownload: Array<any>;
    public cxx: any;
    public xDownloadError: Array<any>;
    public loadClientes: boolean;
    public loadClientesError: boolean;
    public loadPedidos: boolean;
    public loadPedidosError: boolean;
    public loadPagos: boolean;
    public loadPagosError: boolean;
    public loadImg: boolean;
    public loadMultimedi: boolean;
    public loadMultimediaError: boolean;
    public xDownloadDoc: boolean;
    public xDownloadDocError: boolean;
    public modeSincy: boolean;
    public modelpago = new Pagos();

    constructor(private dataService: DataService, private configService: ConfigService, private network: Network, public pagosService: PagosService,
        private file: File, private dialogs: Dialogs, private nativeStorage: NativeStorage,
        private spinnerDialog: SpinnerDialog,
        private navCrl: NavController, private toast: Toast,
        private androidPermissions: AndroidPermissions,
        private httpclient: HttpClient, private transfer: FileTransfer,private _configLayautService:ConfigLayautService) {
        this.c = 0;
        this.todos = false;
        this.clientes = false;
        this.productos = false;
        this.almacenes = false;
        this.cambio = true;
        this.listaprecios = false;
        this.productosalmacenes = false;
        this.clientessucursales = false;
        this.productosprecios = false;
        this.lotes = false;
        this.modeSincy = false;
        this.loadproductosprecios = false;
        this.loadlotes = false;
        this.isenabled = true;
        this.estados = [];
        this.importar = [];
        this.contador = 0;
        this.total = 0;
        this.arrxx = [];
        this.arraux = [];
        this.datosexport = true;
        this.loaddatosexport = false;
        this.clientesexport = true;
        this.clientesexportload = false;
        this.clientesexportTotal = 0;
        this.xBack = false;
        this.xDownloadData = [];
        this.xDownload = [];
        this.cxx = [];
        this.xDownloadError = [];
        this.loadClientes = false;
        this.loadClientesError = false;
        this.loadPedidos = false;
        this.loadPedidosError = false;
        this.loadPagos = false;
        this.loadPagosError = false;
        this.loadMultimedi = false;
        this.loadMultimediaError = false;
        this.xDownloadDoc = false;
        this.xDownloadDocError = false;
        this.loadImg = false;
        this.path = '';
        this.totalImg = 0;
        this.totalImgCount = 0;
        this.totalexportpagos = 0;

        this.diasSemana = [
            { dia: 'Lunes', index: false },
            { dia: 'Martes', index: false },
            { dia: 'Miercoles', index: false },
            { dia: 'Jueves', index: false },
            { dia: 'Viernes', index: false },
            { dia: 'Sabado', index: false },
            { dia: 'Domingo', index: false }
        ];
    }


    chekPermission = async () => {

        return this.androidPermissions.requestPermissions(
            [
                this.androidPermissions.PERMISSION.READ_EXTERNAL_STORAGE,
                this.androidPermissions.PERMISSION.WRITE_EXTERNAL_STORAGE
            ]
        );

    }


    public async ngOnInit()
    {
        let id: any = await this.configService.getSession();
        this.id = id[0].idUsuario;
        let useChannel = id[0].canal && id[0].canal == '1' ? true : false;

        
        this.xDownloadData = [
            {
                servis: 'obtenerdocumentos',
                type: 'POST',
                label: 'Documentos locales.',
                objeto: new Documentos()
            },
            {
                servis: 'v2/obtenerpagos',
                type: 'POST',
                label: 'Pagos locales.',
                objeto: new Documentos()
            },
            {
                servis: 'v2/documentosmovilsap',
                type: 'POST',
                label: 'Documentos.',
                objeto: new Documentos()
            }, {
                servis: 'v2/documentosmovilsapdetalle',
                type: 'POST',
                label: 'Lineas de documentos.',
                objeto: new Detalle()
            }, {
                servis: 'v2/productos',// items  
                type: 'POST',
                label: 'Productos.',
                objeto: new Productos()
            }, {
                servis: 'v2/productosalmacenes',
                type: 'POST',
                label: 'Productos y almacenes.',
                objeto: new Productosalmacenes()
            },
            {
                servis: 'v2/productosprecios',
                type: 'POST',
                label: 'Precios de productos',
                objeto: new Productosprecios()
            }, {
                servis: 'seriesproductos',
                type: 'POST',
                label: 'Series productos',
                objeto: new Seriesproductos()
            },
            {
                servis: 'v2/productoslotes',
                type: 'POST',
                label: 'Lotes productos',
                objeto: new Lotesproductos()
            },
            {
                servis: 'v2/clientes',
                type: 'POST',
                label: 'Clientes',
                objeto: new Clientes()
            },
            {
                servis: 'v2/clientessucursales',
                type: 'POST',
                label: 'Sucursales de clientes',
                objeto: new Clientessucursales()
            },
            {
                servis: 'v2/clientescontactos',
                type: 'POST',
                label: 'Contactos',
                objeto: new Contactos()
            }, {
                servis: 'industrias',
                type: 'POST',
                label: 'Tipo de industrias',
                objeto: new Tiposempresa()
            },
            {
                servis: 'descuento',
                type: 'POST',
                label: 'Descuentos',
                objeto: new Descuentos()
            }, {
                servis: 'asunto',
                type: 'POST',
                label: 'Asuntos',
                objeto: new Agendas()
            }, {
                servis: 'tipoactividades',
                type: 'POST',
                label: 'Tipos de actividades',
                objeto: new Agendas()
            }, {
                servis: 'estadoactividades',
                type: 'POST',
                label: 'Estados de actividades',
                objeto: new Agendas()
            }, {
                servis: 'motivoanulacion',
                type: 'POST',
                label: 'Motivos anulación',
                objeto: new Anular()
            },
            {
                servis: 'condicionpago',
                type: 'POST',
                label: 'Condición de pago',
                objeto: new Condicionpago()
            },
            {
                servis: 'combos',
                type: 'POST',
                label: 'Combos',
                objeto: new Combos()
            }, {
                servis: 'centrocostos',
                type: 'POST',
                label: 'Centro de costos',
                objeto: new Centrocostos()
            }, {
                servis: 'v2/bancos',
                type: 'POST',
                label: 'Bancos',
                objeto: new Bancos()
            }, {
                servis: 'v2/bonificacioncabecera',
                type: 'POST',
                label: 'Bonificación Cabecera',
                objeto: new Bonificacion_ca()
            },
            {
                servis: 'v2/bonificacioncompra',
                type: 'POST',
                label: 'Bonificación compras',
                objeto: new bonificacion_compras()
            },
            {
                servis: '/v2/bonificacionregalo',
                type: 'POST',
                label: 'Bonificación regalos',
                objeto: new bonificacion_regalos()
            }, {
                servis: 'grupoproductodocificacion',
                type: 'POST',
                label: 'Dosificación Productos',
                objeto: new Dosificacionproductos()
            }
            , {
                servis: 'companexcanal',
                type: 'POST',
                label: 'Canales',
                objeto: new companex_canal()
            }
            , {
                servis: 'companexsubcanal',
                type: 'POST',
                label: 'Sub canales',
                objeto: new companex_subcanal()
            }

            , {
                servis: 'companextipotienda',
                type: 'POST',
                label: 'Tipos tienda',
                objeto: new companex_tipotienda()
            }

            , {
                servis: 'companexcadena',
                type: 'POST',
                label: 'Cadenas',
                objeto: new companex_cadena()
            }, {
                servis: 'companexconsolidador',
                type: 'POST',
                label: 'Consolidadores',
                objeto: new companex_consolidador()
            }, {
                servis: 'promociones',
                type: 'POST',
                label: 'Campañas',
                objeto: new promocionaes()
            }, {
                servis: 'territorios',
                type: 'POST',
                label: 'Territorios',
                objeto: new Territorios()
            },
            //Percepciones cub
            {
                servis: 'tarjetas',
                type: 'POST',
                label: 'Tarjetas',
                objeto: new Tarjetas()
            }, 
            {
                servis: 'v2/grupospercepciones',
                type: 'POST',
                label: 'Grupos Percepciones',
                objeto: new Grupospercepciones()
            }, 
            {
                servis: 'v2/clientespercepciones',
                type: 'POST',
                label: 'Clientes Percepciones',
                objeto: new Clientespercepciones()
            },
            {
                servis: 'v2/gestionsap',
                type: 'POST',
                label: 'Gestión Sap',
                objeto: new Gestionsap()
            },
            {
                servis: 'v2/almacenpercepciones',
                type: 'POST',
                label: 'Almacenes Percepciones',
                objeto: new Almacenespercepciones()
            },
            // {
            //     servis: 'v2/servicioventa',
            //     type: 'POST',
            //     label: 'Servicio Venta',
            //     objeto: new Servicioventas()
            // },
            // {
            //     servis: 'indicadoresimpuestos',
            //     type: 'POST',
            //     label: 'Indicadores de Impuestos',
            //     objeto: new Indicadoresimpuestos()
            // },
            {
                servis: 'v2/configuracionimpuestos',
                type: 'POST',
                label: 'Configuracion de Impuestos',
                objeto: new Configuracionimpuestos()
            },
            {
                servis: 'v2/tipooperacion',
                type: 'POST',
                label: 'Tipo de operaciones',
                objeto: new PerTipoOperaciones()
            }, 
            // {
            //     servis: 'v2/precioventaunit',
            //     type: 'POST',
            //     label: 'Precio venta uni ',
            //     objeto: new PerTipoPrecioVenta()
            // },
            // {
            //     servis: 'v2/tipoafectacionigv',
            //     type: 'POST',
            //     label: 'Tipo afectación igv',
            //     objeto: new PerFexAfectacionIgv()
            // },
            {
                servis: 'v2/transportista',
                type: 'POST',
                label: 'Transportistas',
                objeto: new PerTransportista()
            },
            // {
            //     servis: 'estadosregiones',
            //     type: 'POST',
            //     label: 'Región / estado',
            //     objeto: new States()
            // },
            // {
            //     servis: 'provincias',
            //     type: 'POST',
            //     label: 'Provincias',
            //     objeto: new Provincias()
            // },
            // {
            //     servis: 'distritos',
            //     type: 'POST',
            //     label: 'Distritos',
            //     objeto: new Distritos()
            // }
        ];
        if(id[0].usa_tabla_nit == '1'){
            this.xDownloadData.push({
                servis: 'v2/nit',
                type: 'POST',
                label: 'Nit',
                objeto: new Nit()
            });
        }

        this._configLayautService.getConfig().then(async(data) =>{
            console.log("data response config ", data);
            //si la data existe se guarda con una configuracion que viene
            
                await this._configLayautService.saveConfigLayaut(data); 
            
            
            let dataLayautconfig: IConfigLayaut = await  this._configLayautService.getConfig();
            console.log("dataguardada",dataLayautconfig);
        }).catch(e =>{
            console.log(e);
         });
        
        let permission = await this.chekPermission();
        console.log("permission file-->", permission);

        if (!permission.hasPermission) {
            return this.toast.show("Se necesita permisos de almacenamieto para esta acción", '4000', 'center').subscribe(t => t);
        }


        let data = new Databaseconf();
        /*let aux0 = await data.deletedblocal('pagos');
        console.log("tabla borrada");
        console.log(aux0);*/

        let aux = await data.createdblocal('pagos');
        console.log("datos retornados ");
        console.log(aux);

        let aux1 = await data.createdblocal('doc');
        console.log("datos retornados ");
        console.log(aux1);


        let aux2: any = await data.loadenddblocal('pagos');
        console.log("datos pagos ");
        console.log(aux2);

        let aux4: any = await data.loadenddblocal('doc');
        console.log("datos doc ");
        console.log(aux4);
        console.log("ngOnInit ");
        // let xData: any;
        // let dataext: any = {
        //     "codigo": "1002900012"
        // };

        // if (this.network.type != 'none') {
        //     xData = await this.dataService.servisReportPost("clientes/consultasaldoclientesap", dataext);
        //     let xJson = JSON.parse(xData.data);
        //     console.log("xJson.respuesta cliente balance ", xJson);
        //     //   await cliente.updatebalancemenossap(xJson.respuesta[0].Balance, this.productodata.CardCode);
        // }
        //this.dataService.exportPagosAsync(0);
        //this.dataService.exportDocumentosAsinc(0);
        let numLocalPago: any = 0;
        try {
            numLocalPago = await this.pagosService.getNumeracionpago();
        } catch (error) {
            console.log("error al traer numero local numLocalPago ", error);
            numLocalPago = 0;
        }


        console.log("numLocalPago ", numLocalPago);
        try {

            let resp: any = await this.dataService.exportNumeracionSync();
            if (resp) {


                console.log("resp  ", resp);
                console.log("resp numeracion ", resp.numgp);
                let numInsert: any = 0;
                if (numLocalPago >= resp.numgp) {

                    numInsert = numLocalPago;
                }
                if (resp.numgp >= numLocalPago) {
                    numInsert = resp.numgp;
                }
                console.log("numInsert ", numInsert);
                await this.configService.setNumeracionpago(numInsert);
            }

        } catch (error) {
            this.toast.show(`No pudimos conectarnos con el servidor, revisa tu conexion a internet.`, '3000', 'top').subscribe(toast => {
            });

            this.navCrl.pop();
        }

        //let xData = await this.dataService.__get("https://www.google.com/");
        // let urlSERVER = await this.configService.getIp();
        // console.log("urlSERVER ", urlSERVER);
        //   this.spinnerDialog.show(null, 'Verificando estado de la red...', true);
        /*   try {
               await this.dataService.__get("" + urlSERVER);
               this.spinnerDialog.hide();
   
           } catch (error) {
               this.spinnerDialog.hide();
               console.log("internet error ", error);
               console.log(" error cortado ", error.substr(0, 53));
               this.toast.show(`No pudimos conectarnos con el servidor, revisa tu conexion a internet.`, '3000', 'top').subscribe(toast => {
               });
   
               this.navCrl.pop();
         
   
           }
   */

       
        this.updateNumeration();
       
    }

    updateNumeration = async () => {
        let documentos = new Documentos();
        this.arrxx = await documentos.dataExport(this.id);
        this.totalexport = this.arrxx.length;
        let clientes = new Clientes();
        let clienteCount: any = await clientes.exportAll();
        this.totalexportClientes = clienteCount.length;

        //let pagosCount = await this.modelpago.selectAllCabezera('', '0');
        let pagosCount = await this.modelpago.selectAllpagos('', '0');
        console.log(" pagosCount  ", pagosCount);
        this.dataExportPagos = pagosCount;
        this.totalexportpagos = pagosCount.length;
    }
    
    public async atrasBack() {
        if (this.modeSincy == false) {
            this.navCrl.pop();
        } else {
            this.toast.show(`Aun no termino la sincronización espere por favor.`, '3000', 'top').subscribe(toast => {
            });
        }
    }

    public async exportacionesApp() {
        this.modeSincy = true;

        if (this.network.type == 'none') {
            this.modeSincy = false;

            this.toast.show(`No tienes conexión verifica la red.`, '3000', 'top').subscribe(toast => {
            });
            return false;
        }
        localStorage.setItem("newSession", "0")
        await this.configService.setActionMarker([]);
        this.dataService.exportVistas();
        try {


            let rcc = await this.configService.getSession();
            let obx: any = {
                usuarioNombreUsuario: rcc[0].nombreUsuario,
                usuarioClaveUsuario: rcc[0].contrapass,
                plataformaEmei: rcc[0].uuid
            };
            let x: any = await this.dataService.login(obx);
            let respUser: any = JSON.parse(x.data);
            respUser.respuesta[0].contrapass = rcc[0].contrapass;
            await this.configService.setSession(respUser.respuesta);
        } catch (e) {
            console.log(e);
        }

        try {
           // await this.exportLocation();
        } catch (e) {
            console.log(e);
        }
        try {
            await this.actionLbcc();
        } catch (e) {
            console.log(e);
        }
        try {
            await this.exportaclientes();
            await this.exportarReimpreciones();
        } catch (e) {
            console.log(e);
        }
        try {
            await this.exportDocumentos();
        } catch (e) {
            console.log(e);
        }
        try {
            await this.exportPagos();

        } catch (e) {
            console.log(e);
        }
        try {

        } catch (e) {
            console.log(e);
        }
        try {
            await this.subirMulti();
        } catch (e) {
            console.log(e);
        }
        try {
            await this.descargarData();
        } catch (e) {
            console.log(e);
        }

        this.modeSincy = false;
        await this.nativeStorage.setItem(moment().format('YYYY-MM-DD'), '1');
    }

    public async exportLocation() {
        console.log("exportLocation () ");
        return new Promise(async (resolve, reject) => {
            try {
                let geomodel = new Geolocalizacion();
                let respgeo = await geomodel.select();
                console.log("respgeo from select  ", respgeo);
                //alert("Localizaciones to Midleware "+JSON.stringify(respgeo));
                await this.dataService.ubicacionesExport(respgeo);
                await geomodel.clear();
                resolve(true);
            } catch (e) {
                reject(false);
            }
        })
    }

    public async actionLbcc() {
        return new Promise(async (resolve, reject) => {
            try {
                let datalbcc: any = await this.dataService.getLbcc();
                let data: any = JSON.parse(datalbcc.data);
                await this.configService.setLbcc(data.respuesta[0]);
                resolve(true);
            } catch (e) {
                reject(false);
            }
        })
    }

    public async exportDocumentos() {
        try {
            await this.dataService.exportDocumentosAsinc(0);
            await this.updateNumeration();
            let documentos = new Documentos();
            this.arrxx = await documentos.dataExport(this.id);
            this.totalexport = this.arrxx.length;
            return true;
        } catch (error) {
            return false;
        }


    }

    public async exportaclientes() {


        console.log("exportaclientes() ");
        this.loadClientes = true;
        this.loadClientesError = false;
        try {
            await this.dataService.exporcliensync();
            this.loadClientes = false;
            this.loadClientesError = false;
            await this.updateNumeration();
            let clientes = new Clientes();
            let clienteCount: any = await clientes.exportAll();
            this.totalexportClientes = clienteCount.length;
        } catch (e) {
            this.loadClientes = false;
            this.loadClientesError = false;
        }
    }

    public exportarReimpreciones() {
        return new Promise(async (resolve, reject) => {
            let reimprecion = new Reimpresion();
            let reimpre: any = await reimprecion.export();
            let rx = { "reimpresiones": reimpre };
            let r = await this.dataService.exportReimpreciones(rx);
            await reimprecion.updatex();
            resolve(r);
        });
    }

    public startUpload(imagen: any) {
        return new Promise((resolve, reject) => {
            let filePath: any = this.file.externalApplicationStorageDirectory + imagen;
            this.file.resolveLocalFilesystemUrl(filePath).then(entry => {
                (entry as FileEntry).file(file => {
                    let reader = new FileReader();
                    reader.onloadend = async () => {
                        let formData = new FormData();
                        let imgBlob = new Blob([reader.result], { type: file.type });
                        formData.append('file', imgBlob, file.name);
                        let path = await this.configService.getIp() + "imgs/uploadtwo.php";
                        this.httpclient.post(path, formData).subscribe((res) => {
                            resolve(true);
                        }, (error) => {
                            reject(false);
                        })
                    };
                    reader.readAsArrayBuffer(file);
                });
            }).catch(err => {
                reject(false);
            });
        });
    }

    public async subirMulti() {
        if (this.cxx.length > 0) {
            this.loadMultimedi = true;
            this.loadMultimediaError = false;
            for await (let inde of this.cxx) {
                try {
                    await this.startUpload(inde.img);
                    this.loadMultimedi = false;
                    this.loadMultimediaError = false;
                } catch (e) {
                    this.loadMultimedi = false;
                    this.loadMultimediaError = true;
                }
            }
        }
    }

    public async descargarData() {
        this.xBack = true;
        let i = 0;
        for await (let x of this.xDownloadData) {
            try {
                await this.actionDownload(x.servis, i, x.objeto, x.type);
            } catch (e) {
                this.toast.show(`Ocurrió un problema en la sincronización de ${x.label} verifica tu conexión o contáctate con el administrador del sistema `, '3000', 'top').subscribe(toast => {
                });
            }
            i++;
        }
        this.xBack = false;
        /*this.dialogs.confirm("Sincronización completada!. \n\n Desea continuar con la descarga de multimedia.", "Xmobile.", ["SI", "NO"]).then((data) => {
            if (data == 1) {
                this.donwloadImg();
            } else {
                //this.navCrl.pop();
            }
        }).catch((e) => {
            console.log(e);
        });*/

        this.dialogs.confirm("Sincronización completada!.", "Xmobile.", ["OK"]).then((data) => {
            if (data == 1) {
                this.navCrl.pop();
            } else {
                this.navCrl.pop();
            }
        }).catch((e) => {
            console.log(e);
        });
    }

    public async donwloadImg() {
        if (this.network.type == 'none') {
            this.toast.show(`No tienes conexión verifica la red.`, '3000', 'top').subscribe(toast => {
            });
            return false;
        }
        this.loadImg = true;
        this.path = await this.configService.getIp();
        let clientes = new Clientes();
        let imgs: any = await clientes.importAllImg();
        console.log("cantidad imagenes clientes ", imgs);
        let productos = new Productos();
        let imgp: any = await productos.selectProductos();
        console.log("cantidad productos imagenes ", imgs);
        let arrImg = [];
        for await (let img of imgs) if (img.img != 'null') arrImg.push({ img: img.img, tipo: '1' });
        for await (let img of imgp) arrImg.push({ img: img.ItemCode + '.jpeg', tipo: '2' });
        this.totalImg = arrImg.length;
        console.log("totalImg ", this.totalImg);
        this.totalImgCount = 0;
        for await (let img of arrImg) {
            try {
                await this.verificaImg(img);
            } catch (e) {
                try {
                    await this.descargaImg(img);
                } catch (e) {
                }
            }
            this.totalImgCount += 1;
        }
        this.loadImg = false;
        this.toast.show(`Finalizo la importación de multimedia`, '3000', 'top').subscribe(toast => {
        });
        this.navCrl.pop();
    }

    public verificaImg(name: any) {
        return new Promise((resolve, reject) => {
            let pathExternal = this.file.externalApplicationStorageDirectory;
            this.file.checkFile(pathExternal, name.img).then((data) => {
                resolve(true);
            }).catch((err) => {
                reject(false);
            });
        });
    }

    public descargaImg(img: any) {
        console.log("descargaImg ", img);
        return new Promise((resolve, reject) => {
            let nombre = img.img;
            let url = '';
            let pathExternal = this.file.externalApplicationStorageDirectory + nombre;
            console.log("descargando en :", pathExternal);
            (img.tipo == '1') ? url = this.path + 'imgs/cli/' + nombre : url = this.path + 'imgs/prod/' + nombre;

            let fileTransfer: FileTransferObject = this.transfer.create();
            fileTransfer.download(url, pathExternal).then((entry) => {
                resolve(true);
            }, (error) => {
                reject(false);
            });
        });
    }

    public async actionDownload(xDx: string, itx: number, objectoData: any, tipo: string) {
        console.log("........Sincronizando........");


        if (this.network.type == 'none') {
            this.toast.show(`No tienes conexión verifica la red.`, '3000', 'top').subscribe(toast => {
            });
            return false;
        }
        this.xDownload[itx] = true;
        this.xDownloadError[itx] = false;
        return new Promise(async (resolve, reject) => {
            let xData: any;
            if (tipo == 'POST') {
                xData = await this.dataService.servisDownloadPost(xDx + "/contador");

            } else {
                xData = await this.dataService.servisDownloadGet(xDx + "/contador");
            }
            let xJson = JSON.parse(xData.data);
            console.log("xJson.respuesta", xJson.respuesta);
            console.log("-Number(xJson.respuesta.contador)  ", Number(xJson.respuesta.contador));

            switch (xJson.estado) {
                case (200):
                    let dataAux = {
                        data: JSON.stringify({
                            "estado": 200,
                            "respuesta": [],
                            "mensaje": "OK"
                        })

                    };
                    if (Number(xJson.respuesta.contador) >= 0) {

                        for (let i = 0; i <= Number(xJson.respuesta.contador); i = i + 1000) {
                            if (tipo == 'POST') {
                                console.log("POST");
                                xData = await this.dataService.servisDownloadPostPaginate(xDx, i);
                            }
                            console.log("verificar ", JSON.parse(xData.data));
                            console.log("JSON.parse(xData.data).respuesta  ", JSON.parse(xData.data).respuesta);
                            if (JSON.parse(xData.data).estado == 200) {
                                console.log("insertAll call ");
                                console.log(xDx);
                                try {
                                    if (xDx == "obtenerdocumentos") {//Documentos locales
                                        console.log("insertAllLocales()");
                                        await objectoData.insertAllLocales(xData, this.id, i);
                                    } else if (xDx == "v2/obtenerpagos") {//Pagos locales
                                        console.log("insertAllLocalesPagos()");
                                        await objectoData.insertAllLocalesPagos(xData, this.id, i);
                                    } else {
                                        console.log("insertAll()");
                                        await objectoData.insertAll(xData, this.id, i);
                                    }

                                } catch (error) {
                                    console.log("SIN DATA PARA INSERTALL ", xDx);
                                    console.log("SIN DATA PARA INSERTALL ", error);
                                    // 
                                    await objectoData.insertAll(dataAux, this.id, i);
                                }

                            } else {
                                try {
                                    console.log("contador pagina sin data ");
                                    if (xDx == "obtenerdocumentos") {//Documentos locales
                                        await objectoData.insertAllLocales(dataAux, this.id, i);
                                    } else if (xDx == "obtenerpagos") {//Pagos locales
                                        await objectoData.insertAllLocalesPagos(dataAux, this.id, i);
                                    } else {
                                        await objectoData.insertAll(dataAux, this.id, i);
                                    }
                                } catch (error) {

                                }


                            }


                        }
                        this.xDownload[itx] = false;
                        resolve(true);

                    } else {

                        if (Number.isNaN(Number(xJson.respuesta.contador))) {
                            this.xDownloadError[itx] = true;
                        }
                        this.xDownload[itx] = false;
                        if (Number(xJson.respuesta.contador) == 0) {
                            try {
                                await objectoData.insertAll(xData, this.id, 0);
                            } catch (error) {
                                console.log("test error sincro ");

                                await objectoData.insertAll(dataAux, this.id, 0);
                            }


                        }
                        resolve(true);
                    }
                    break;
                default:
                    this.xDownload[itx] = false;
                    this.xDownloadError[itx] = true;
                    reject(false);
            }
            // } catch (e) {

            //     console.log("ERROR", e);
            //     this.xDownload[itx] = false;
            //     this.xDownloadError[itx] = true;
            //     reject(false);
            // }
        })
    }
    /*
        public async revisaTabla() {
            let bonif = new Bonificacion_ca();
            let resp: any = await bonif.showTable();
            let bonifde1 = new bonificacion_regalos();
            let respde1: any = await bonifde1.showTable();
        }
    */
    public async exportPagos() {
        try {
            this.loadPagos = true;
            this.loadPagosError = false;
            this.spinnerDialog.show();
            const recibosExportados = await this.pagosService.exportPagosPendientes(this.dataExportPagos);
            console.log("recibosExportados ", recibosExportados);
            this.spinnerDialog.hide();
            // await this.dataService.exportPagosAsync(0);
            // await this.dataService.exportPagosAsyncCancela();
            this.updateNumeration();
            this.loadPagos = false;
            this.loadPagosError = false;
            return (true);
        } catch (e) {
            this.loadPagos = false;
            this.loadPagosError = true;
            return (false);
        }
    }

    public async exportados(arr: any) {
        for await (let item of arr.respuesta) {
            try {
                let clientes = new Clientes();
                await clientes.updateImport(item.cardcode);
            } catch (e) {
                console.log(e);
            }
        }
    }
}
