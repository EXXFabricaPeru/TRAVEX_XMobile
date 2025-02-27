import { Component, OnInit, ViewChild,Renderer2 } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Clientes } from "../../models/clientes";
import { Listaprecios } from "../../models/listaprecios";
import { Productosprecios } from "../../models/productosprecios";
import { Clientessucursales } from "../../models/clientessucursales";
import { ConfigService } from "../../models/config.service";
import { NavController, ActionSheetController, ModalController, AlertController, Platform } from "@ionic/angular";
import { SpinnerDialog } from "@ionic-native/spinner-dialog/ngx";
import { Toast } from "@ionic-native/toast/ngx";
import { DataService } from "../../services/data.service";
import { Dialogs } from "@ionic-native/dialogs/ngx";
import { Documentos } from "../../models/documentos";
import { WheelSelector } from "@ionic-native/wheel-selector/ngx";
import { Contactos } from "../../models/contactos";
import { WebView } from "@ionic-native/ionic-webview/ngx";
import { File } from '@ionic-native/file/ngx';
import { Tiposempresa } from "../../models/tiposempresa";
import { Calculo } from "../../utilsx/calculo";
import { FrompagosPage } from "../frompagos/frompagos.page";
import { ReportService } from "../../services/report.service";
import { VisitasPage } from "../visitas/visitas.page";
import { Documentopago } from "../../models/documentopago";
import { Pagos } from "../../models/pagos";
import { Geolocation } from '@ionic-native/geolocation/ngx';
import { NativeGeocoder, NativeGeocoderResult, NativeGeocoderOptions } from '@ionic-native/native-geocoder/ngx';
import { Diagnostic } from '@ionic-native/diagnostic/ngx';
import { AndroidPermissions } from '@ionic-native/android-permissions/ngx';
import { companex_canal } from '../../models/companex_canal';
import 'lodash';
import { companex_consolidador } from '../../models/companex_consolidador';
import { DocumentService } from '../../services/documents.service';
import {PagosService} from '../../services/pagos.service'

declare var _: any;
declare var google;

@Component({
    selector: 'app-cliente',
    templateUrl: './cliente.page.html',
    styleUrls: ['./cliente.page.scss'],
})
export class ClientePage implements OnInit {
    public camposclientes: any;
    public data: any;
    public dataContactos: any;
    public id: any;
    public idUser: any;
    public preciosdata: any;
    public sucursalesdata: any;
    public latitude: any;
    public longitude: any;
    public direccion: any;
    public nombreTipoEmpresa: any;
    public srcimg: any;
    public facturastotal: any;
    public ofertastotal: any;
    public pedidostotal: any;
    public xt: number;
    public rama: any;
    public userdata: any;
    public locationbtn: boolean;
    public textDocificaion: string;
    public idfrom = 3;
    public grupoClienteDosificacion: string;
    public usaIce: boolean;
    dataSucursales: any;
    VistaCliNormal: boolean = true;
    camposCuccs: any = [];
    constructor(private platform: Platform, private activatedRoute: ActivatedRoute, private configService: ConfigService, private dialogs: Dialogs, public alertController: AlertController, private diagnostic: Diagnostic,
        private navCrl: NavController, private spinnerDialog: SpinnerDialog, private toast: Toast, public modalController: ModalController, private documentService: DocumentService,
        private dataService: DataService, private selector: WheelSelector, private reportService: ReportService, private geolocation: Geolocation,
        private file: File, private nativeGeocoder: NativeGeocoder, private webview: WebView, public actionSheetController: ActionSheetController,private renderer: Renderer2,private pagosservice:PagosService) {
        this.data = [];
        this.preciosdata = [];
        this.sucursalesdata = [];
        this.userdata = [];
        this.direccion = '';
        this.nombreTipoEmpresa = '';
        this.facturastotal = 0;
        this.ofertastotal = 0;
        this.pedidostotal = 0;
        this.xt = 0;
        this.rama = 0;
        this.locationbtn = true;
        this.textDocificaion = '';
        this.grupoClienteDosificacion = '';
        this.usaIce = true;
    }

    public async ngOnInit() {
        try{
            this.userdata = await this.configService.getSession();
            if (this.userdata[0].ctrl_ice == 0) {
                this.usaIce = false;
            }
            this.rama = this.activatedRoute.snapshot.paramMap.get('rama');
            if (localStorage.getItem('VistaCliNormal') == "0") {
                this.VistaCliNormal = false;
            }
        }
        catch(ex){
            this.toast.show(ex, '4000', 'top').subscribe(toast => {  });
        }        
    }

    public async carga_camposusuario(datos){
    
        let usuariodata: any = await this.configService.getSession();
        let contenedorcampos = '';

        if(usuariodata[0].campodinamicos.length > 0){
            contenedorcampos = await this.dataService.createcampususerlabel(usuariodata[0].campodinamicos,this.idfrom,datos);
        }

        document.getElementById("contenedorcamposlabel").innerHTML="";

        const div: HTMLDivElement = this.renderer.createElement('div');
        div.className = "col-md-12";
        div.innerHTML = contenedorcampos;
        this.renderer.appendChild(document.getElementById("contenedorcamposlabel"), div);
       

        /*for (let i = 0; i < usuariodata[0].campodinamicos.length; i++) {
            if(usuariodata[0].campodinamicos[i].Objeto == this.idfrom){
                if(usuariodata[0].campodinamicos[i].tipocampo == 1){
                    if(usuariodata[0].campodinamicos[i].flagrelacion == 1){
                        let campo = "campousu"+usuariodata[0].campodinamicos[i].Nombre;
                        this.eventoClick = this.renderer.listen(
                            document.getElementById(campo),
                            "ionChange",
                            evt => {
                                this.cargalista_campousuario(evt,usuariodata[0].campodinamicos[i].Id);
                            }
                        );
                    }
                }
            }
        }*/
    }

    public ionViewWillEnter() {
        this.init();
        this.contador();
    }

    /********START PAGO*********/
    public async formPago(tipo: number, monto = 1) {
        let numerox: number = 0;
        let inix: any = await this.pagosservice.getNumeracionpago();
        numerox = (inix + 1);
        let codPago = Calculo.generaCodeRecibo(this.idUser.toString(), numerox.toString(), '1');
        let datospago = {
            dataCliente: this.data,
            modo: 'CLIENTE',
            cod: codPago,
            cliente: this.data.CardCode,
            tipo: tipo,
            monto: monto,
            documento: [],
            correlativo: numerox
        };
        console.log("Enviando data", datospago);
        let obj: any = { component: FrompagosPage, componentProps: datospago };
        let modal: any = await this.modalController.create(obj);
        modal.onDidDismiss().then(async (data: any) => {
            if (typeof data.data != "undefined")
                if (data.data != 0) {
                    await this.sumadorrecibo();
                    let documentopago = new Documentopago();
                    let pagos = new Pagos();
                    let aux_pago_cab = await documentopago.findPagos(data.data.documentoPagoId);
                    let aux_pago_detalle: any = [];
                    if (data.data.dx == 'cuenta') aux_pago_detalle = await pagos.findAllPagosCuenta(data.data.documentoPagoId);
                    else aux_pago_detalle = await pagos.findAllPagos(data.data.documentoPagoId);
                    try {
                        let resp: any = await this.reportService.generarecibo(aux_pago_cab[0], this.data, true, aux_pago_detalle, this.userdata);
                        if (resp) this.reportService.generateEXE(data.data.documentoPagoId);
                        this.toast.show(`El pago se realizó correctamente.`, '4000', 'top').subscribe(toast => {
                        });
                    } catch (error) {
                        this.toast.show(`El pago se realizó correctamente.`, '4000', 'top').subscribe(toast => {
                        });
                    }


                }
        });
        return await modal.present();
    }

    public async sumadorrecibo(): Promise<any> {
        try {
            let inix: any = await this.pagosservice.getNumeracionpago();
            let xc: number = (inix + 1);
            await this.configService.setNumeracionpago(xc);
            return true;
        } catch (e) {
        }
    }

    public async pagos() {
        if (this.data.activo == 'N') {
            return this.toast.show(`Cliente inactivo.`, '2500', 'center').subscribe(toast => {
            });
        }
        if (this.userdata[0].config[0].permisoPagosAnticipados == 0) {
            this.toast.show(`No está permitido para realizar pagos de anticipo .`, '2500', 'center').subscribe(toast => {
            });
            return false;
        }
        let alert: any = await this.alertController.create({
            header: 'INTRODUZCA EL MONTO A CANCELAR.',
            mode: 'ios',
            inputs: [{
                name: 'data',
                type: 'number',
                min: 0,
                max: 10000,
                value: "",
                placeholder: '0'
            }],
            buttons: [{
                text: 'SOLES (SOL)',
                handler: (data: any) => {
                    if (data.data > 0) {
                        this.formPago(2, data.data);
                    } else {
                        this.toast.show(`El monto introducido no es válido.`, '4000', 'top').subscribe(toast => {
                        });
                        return false;
                    }
                }
            }/*, {
                text: 'DOLARES (USD)',
                handler: (data: any) => {
                    if (data.data > 0) {
                        this.formPago(1, data.data);
                    } else {
                        this.toast.show(`El monto introducido no es válido.`, '4000', 'top').subscribe(toast => {
                        });
                        return false;
                    }
                }
            }*/]
        });
        await alert.present();
    }

    public async pagosDeudas() {

        try {

            await this.documentService.downloadDeudasCliente(this.data.CardCode)
        } catch (error) {
            console.log("OCURRIO UN ERROR ");

        }
        console.log(this.data.activo);
        if (this.data.activo == 'N') {
            return this.toast.show(`Cliente inactivo.`, '2500', 'center').subscribe(toast => {
            });
        }
        this.navCrl.navigateForward(`detallepago/${this.data.CardCode}`);
    }
    /********END PAGO*********/

    public crearDocumento() {
        let arr = [
            { tipo: 'DOF', description: 'Documento de oferta' },
            { tipo: 'DOP', description: 'Documento de pedido' },
            { tipo: 'DFA', description: 'Documento de factura' }
        ];
        this.selector.show({
            title: "Crear documento.",
            items: [arr],
            positiveButtonText: "Seleccionar",
            negativeButtonText: "Cancelar"
        }).then(async (result: any) => {
            let tipx: any = arr[result[0].index];
            let tp = tipx.tipo;
            await this.configService.setTipo(tp);
            this.navCrl.navigateForward(`pedido/${this.xt}/${tp}/${this.id}`);
        }, (err: any) => {
            console.log(err);
        });
    }

    public async contador() {
        let documento = new Documentos();
        let x: any = await documento.contador('DFA', this.id);
        let y: any = await documento.contador('DOF', this.id);
        let z: any = await documento.contador('DOP', this.id);
        this.facturastotal = x.total;
        this.ofertastotal = y.total;
        this.pedidostotal = z.total;
    }

    public editar() {
        if (this.userdata[0].config[0].permisoEditarClientes == 0) {
            this.toast.show(`No está permitido editar clientes .`, '2500', 'center').subscribe(toast => {
            });
            return false;
        }
        this.navCrl.navigateForward(`formcliente/` + this.id);
    }

    public async exportaclientes(clienteArr: any) {
        return new Promise(async (resolve, reject) => {
            try {
                let rx = await this.dataService.exportClientes(clienteArr);
                resolve(rx);
            } catch (e) {
                reject(e);
            }
        })
    }

    public gpsnavegador() {
        let options: NativeGeocoderOptions = {
            useLocale: true,
            maxResults: 5
        };
        return new Promise(async (resolve, reject) => {
            try {
                let rxx: any = await this.configService.getUbicacion();
                let rexx: any = await this.nativeGeocoder.reverseGeocode(rxx.lat, rxx.lng, options);
                this.direccion = '';
                resolve({ "lat": rxx.lat, "lng": rxx.lng });
            } catch (e) {
                this.toast.show('El dispositivo  no tiene soporte para el GPS.', '3000', 'top').subscribe(toast => {
                });
                reject({ "lat": 0, "lng": 0 });
            }
        })
    }

    public async updateUbicacion() {
        this.locationbtn = false;
        let ubx: any = await this.gpsnavegador();
        this.latitude = ubx.lat;
        this.longitude = ubx.lng;
        this.locationbtn = true;
        if (ubx.lat == 0 || ubx.lng == 0) return false;
        console.log(ubx);
        /*this.dialogs.prompt('', 'Describe tu ubicación.', ['Modificar', 'Cancelar'], this.direccion).then(async (data) => {
            if (data.buttonIndex == 1) {
                let clientx = new Clientes();
                this.toast.show('La ubicación se actualizo correctamente', '3000', 'top').subscribe(toast => {
                });
                await clientx.updateLocate(this.latitude, this.longitude, data.input1, this.id);
                this.init();
                try {
                    let arr = [];
                    let dx: any = await clientx.selectCarCode(this.id);
                    arr.push(dx);
                    await this.exportaclientes(arr);
                } catch (e) {
                    console.log(e);
                }
            }
        }).catch((e: any) => {
            this.toast.show('¡Error: Verifica tu GPS', '3000', 'top').subscribe(toast => {
            });
        });*/
    }

    public async exportacliente(data: any) {
        return new Promise(async (resolve, reject) => {
            try {
                let clientes = new Clientes();
                let cx = await clientes.exportOne(data.CardCode, data.CardName);
                let rx = await this.dataService.exportClientes(cx);
                let ux = await clientes.updateExport(data.CardCode, data.CardName);
                resolve(rx);
            } catch (e) {
                reject(e);
            }
        })
    }

    public async vistasCliente() {
        let ubi: any;
        ubi = {
            lat: 0,
            lng: 0
        };
        try {
            ubi = await this.configService.getUbicacion();
        } catch (e) {
        }
        let datospago = {
            CardCode: this.data.CardCode,
            CardName: this.data.CardName,
            lat: ubi.lat,
            lng: ubi.lng,
            foto: 'null.jpg',

        };
        let obj: any = { component: VisitasPage, componentProps: datospago };
        let modal: any = await this.modalController.create(obj);
        modal.onDidDismiss().then(async (data: any) => {
            console.log(data);
        });
        return await modal.present();
    }

    public printImg() {
        console.log("printImg()");
        console.log("this.data.img ", this.data.img);
        if (this.data.img == "" || this.data.img == undefined || this.data.img == null) {
            this.srcimg = '../../../assets/broken-image.svg';
        } else {
            fetch(this.data.img, { method: 'HEAD' })
                .then(res => {
                    if (res.ok) {
                        this.srcimg = this.data.img;
                        console.log('Image exists.');
                    } else {
                        console.log('Image does not exist.');
                        this.srcimg = '../../../assets/broken-image.svg';


                    }
                }).catch(err => console.log('Error:', err));
        }

        /*
                if (typeof (this.data.img) !== 'undefined') {
                    let name = this.data.img;
                    let pathExternal = this.file.externalApplicationStorageDirectory;
                    this.file.checkFile(pathExternal, name).then((data) => {
                        this.srcimg = this.webview.convertFileSrc(pathExternal + name);
                    }).catch((err) => {
                        this.srcimg = '../../../assets/broken-image.svg';
                    })
                } else {
                    this.srcimg = '../../../assets/broken-image.svg';
                }
                */
    }

    public async init() {
        console.log("init()");
        this.id = this.activatedRoute.snapshot.paramMap.get('id');
        let id: any = await this.configService.getSession();
        this.idUser = id[0].idUsuario;
        this.camposclientes = id[0].config[0].camposclientes;
        let clientes = new Clientes();
        let contactos = new Contactos();
        this.data = await clientes.selectCarCode(this.id);
        await this.carga_camposusuario(this.data);

        this.data.labelCanal = await this.getOneCanal(this.data.codeCanal);
        this.data.labelSubCanal = await this.getOneSubCanal(this.data.codeSubCanal);
        this.data.labelTipoTienda = await this.getOneTipoTienda(this.data.codeTipoTienda);
        this.data.labelCadena = await this.getOneCadena(this.data.cadena);
        this.data.labelConsolidador = await this.getOneConsolidador(this.data.codeCadenaConsolidador);
        this.data.cadenaTxt = this.data.cadenaTxt;
        try {
            this.camposCuccs = JSON.parse(this.data.cuccs);
        } catch (error) {
            this.camposCuccs = [];
        }

        console.log("this.data cliente ", this.data);
        this.dataContactos = await contactos.selectCarCode(this.id);
        console.log(" this.dataContactos ", this.dataContactos);
        this.sucuralesArr();
        //this.dataSucursales = await contactos.selectCarCode(this.id);
        try {
            let doci = _.filter(this.userdata[0].docificacion, { 'U_GrupoCliente': this.data.cliente_std1 });
            this.textDocificaion = doci[0].nombreGrupoCliente;
        } catch (e) {
            this.textDocificaion = 'No asignado';
        }
        try {
            let tipoempresa = new Tiposempresa();
            let tpempresa: any = await tipoempresa.selectTipoEmpresaId(this.data.tipoEmpresa);
            this.nombreTipoEmpresa = tpempresa.nombre;
        } catch (e) {
            this.nombreTipoEmpresa = 'No asignado';
        }

        //this.srcimg = '../../../assets/broken-image.svg';
        this.printImg();

        this.precios();
    }

    public async precios() {
        let listaprecios = new Productosprecios();
         this.preciosdata = await listaprecios.selectPreciosClient2(this.data.PriceListNum);
         console.log(" this.preciosdata ", this.preciosdata);

    }

    public async sucuralesArr() {
        let model = new Clientessucursales();
        this.sucursalesdata = await model.findAll(this.data.CardCode, this.idUser);
        console.log("sucursalesdata ", this.sucursalesdata);
        this.listarSucursales();
    }

    public facturasPendientes() {
        this.navCrl.navigateForward(`pendientes/` + this.data.CardCode);
    }

    public verMapa() {
        /*if (this.data.Latitude == 0) {
            this.toast.show('El cliente no tiene una ubicación presiona el botón de lado. ', '3000', 'top').subscribe(toast => {
            });
            return false;
        }*/
        this.navCrl.navigateForward('mapacliente/' + this.data.CardCode);
    }

    public llamar(num: string) {
        this.callNumber(num);
    }

    public async callNumber(num): Promise<any> {
        try {
            //await this.call.callNumber(String(num), true)
            return window.open("tel:" + String(num), '_blank');
        } catch (e) {
            console.log(e);
        }


    }

    public verMapaNew(item: any) {
        console.log("item ", item);
        console.log(" this.data.CardCode ", this.data.CardCode);
        this.navCrl.navigateForward('mapacliente/' + item.id + '/' + "sucursal");
    }

    errorLoadImg() {
        console.error("ocurrio un error al cargar imagen ");
    }

    listarSucursales() {
        console.log("this.sucursalesdata ", this.sucursalesdata);
        if(JSON.parse(localStorage.getItem("territorios"))){
            console.log(" JSON.parse(localStorage.getItem territorios ", JSON.parse(localStorage.getItem("territorios")));
            this.sucursalesdata.forEach(element => {
                // element.labelTerritorio="asd";
                // if(element.u_territorio){
                element.labelTerritorio = "Sin territorio";
                if (JSON.parse(localStorage.getItem("territorios")).filter(value => value.TerritoryID == element.u_territorio).length > 0) {

                    element.labelTerritorio = JSON.parse(localStorage.getItem("territorios")).filter(value => value.TerritoryID == element.u_territorio);

                    element.labelTerritorio = element.labelTerritorio[0].Description;
                    console.log("element.labelTerritorio ", element.labelTerritorio);
                }
                /// }
            });
        }
        console.log("this.sucursalesdata ", this.sucursalesdata);        
    }

    async getOneCanal(codeCanal) {

        let model = new companex_canal();
        try {
            let dataCombo: any = await model.getOneCanal(codeCanal);
            console.log("dataCombo a devolver ", dataCombo);
            return dataCombo[0].name;
        } catch (error) {
            return '';
        }
    }

    async getOneSubCanal(codeSubCanal) {
        console.log("codeSubCanal ", codeSubCanal);
        let model = new companex_canal();
        try {
            let dataCombo: any = await model.getOneSubCanal(codeSubCanal);
            console.log("subcanal  a devolver ", dataCombo);
            return dataCombo[0].name;
        } catch (error) {
            return '';
        }
    }

    async getOneTipoTienda(codeTipoTienda) {
        try {
            let model = new companex_canal();
            let dataCombo: any = await model.getOneTipoTienda(codeTipoTienda);
            return dataCombo[0].name;
        } catch (error) {
            return '';
        }
    }

    async getOneCadena(codeCadena) {
        try {
            let model = new companex_canal();
            let dataCombo: any = await model.getOneCadena(codeCadena);
            return dataCombo[0].name;
        } catch (error) {
            return '';
        }
    }

    async getOneConsolidador(code) {
        try {
            let model = new companex_consolidador();
            let dataCombo: any = await model.showOne(code);
            return dataCombo[0].name;
        } catch (error) {
            return '';
        }
    }

}
