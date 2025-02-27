import {
    GoogleMaps, GoogleMap, ILatLng, GoogleMapsEvent, GoogleMapOptions,
    CameraPosition, MarkerOptions, Marker, Environment, HtmlInfoWindow, Polyline, CircleOptions, Circle, LatLng,
    MyLocationOptions, MarkerIcon
} from '@ionic-native/google-maps';
import { Component, OnInit, ChangeDetectorRef, VERSION, Renderer2, ɵɵsetComponentScope } from '@angular/core';
import { Camera, CameraOptions, PictureSourceType } from '@ionic-native/camera/ngx';
import { Clientes } from '../../models/clientes';
import { ConfigService } from '../../models/config.service';
import { Toast } from '@ionic-native/toast/ngx';
import { File, FileEntry } from '@ionic-native/file/ngx';
import { NavController, ToastController, Platform, AlertController, ModalController, ActionSheetController, LoadingController } from '@ionic/angular';
import { Listaprecios } from '../../models/listaprecios';
import { DataService } from '../../services/data.service';
import { WebView } from '@ionic-native/ionic-webview/ngx';
import { Network } from '@ionic-native/network/ngx';
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { WheelSelector } from '@ionic-native/wheel-selector/ngx';
import { HTTP } from '@ionic-native/http/ngx';
import { ActivatedRoute } from '@angular/router';
import { FilePath } from '@ionic-native/file-path/ngx';
import { HttpClient } from '@angular/common/http';
import { Tiposempresa } from "../../models/tiposempresa";
import { Contactos } from "../../models/contactos";
import { Dialogs } from "@ionic-native/dialogs/ngx";
import { ModalClienteSucursalPage } from '../modal-cliente-sucursal/modal-cliente-sucursal.page';
import { ModalMapaPage } from '../modalmap/modalmapa.page';
import { Clientessucursales } from '../../models/clientessucursales';
import { ConstantPool } from '@angular/compiler';
import { AndroidPermissions } from '@ionic-native/android-permissions/ngx';
import { Diagnostic } from '@ionic-native/diagnostic/ngx';
import { Geolocation, GeolocationOptions, Geoposition, PositionError } from '@ionic-native/geolocation/ngx';
import { LocationAccuracy } from '@ionic-native/location-accuracy/ngx';
import { companex_canal } from '../../models/companex_canal';
import { Territorios } from '../../models/territorios';

import {
    FormGroup,
    FormBuilder,
    FormControl,
    Validators
} from '@angular/forms';
import { DeviceNativeValidationService } from '../../services/device-native-validation.service';



declare var _: any;
declare var google;

@Component({
    selector: 'app-formcliente',
    templateUrl: './formcliente.page.html',
    styleUrls: ['./formcliente.page.scss']
})
export class FormclientePage implements OnInit {

    public images = [{
        name: 'addPhoto.svg',
        path: '',
        filePath: '../../assets/addPhoto.svg'
    }];
    public idfrom = 3;
    public image: string = null;
    public map: GoogleMap;
    public selectedImage = '../../assets/addPhoto.svg';
    public imagen: string;
    public fex_tipo: string;
    public codCliente: string;
    public code: string;
    public codigoexcepcion: string;
    public nombreCliente: string;
    public tipoDocu: string;
    public tipoexcep: string;
    public nitci: string;
    public razonsocial: string;
    public direccion: string;
    public rutaterritorisaptext: string;
    public rutaterritorisap: any;
    public diavisitatext: string;
    public telefono: number;
    public listaPrecio: string;
    public listaPreciotext: string;
    public comentario: string;
    public complemento: string;
    public creadopor: string;
    public personaContacto: any;
    public rutaCliente: string;
    public diasVisita: string;
    public datalistprecio: any;
    public email: any;
    public longitude: any;
    public tipoDocumentoName: string;
    public latitude: any;
    public diavisita: any;
    public xcodigocliente: any;
    public telefonoEmp: number;
    public telefonoCel: number;
    public textAction: string;
    public diasSemana: any;
    public iniNum: any;
    public win: any = window;
    public loadImgCamera: boolean;
    public moneda: string;
    public groupCode: number;
    public idEmpresa: number;
    public tipoEmpresa: string;
    public camposclientes: any;
    public cliente_std1: any;
    public cliente_std2: any;
    public cliente_std3: any;
    public cliente_std4: any;
    public cliente_std5: any;
    public cliente_std6: any;
    public cliente_std7: any;
    public cliente_std8: any;
    public cliente_std9: any;
    public cliente_std10: any;
    public estadoBtnRegister: boolean;
    public numerador: number;
    public arrinputs: any;
    public numeroIdentificacionTributariaLabel: string;
    validarExistCadena = false;
    public dataUser: any;
    public dataSucursales: any = [];
    public dataDireccion: string;
    territoriosJson: any;
    public u_territorio2: string;
    public contenedorcampos: any;
    public code_territorio2: string;
    isModal: boolean = false;
    currentImage: any = "";
    canal: any = "";
    codeCanal: any = "";
    subCanal: any = "";
    codeSubCanal: any = "";
    tipoTienda: any = "";
    codeTipoTienda: any = "";
    cadena: any = "";
    codeCadena: any = "";
    codeCadenaConsolidador: any = "";
    listCuccs = [];
    eventoClick = null;
    name = "Angular " + VERSION.major;
    public dataClient;
    public tipoPersona: any;
    public tipoPersonaCode: string;
    public apellidoPaterno: string;
    public apellidoMaterno: string;
    public primerNombre: string;
    public segundNombre: string;

    constructor(private activatedRoute: ActivatedRoute, 
        private spinnerDialog: SpinnerDialog, 
        private http: HTTP,
        private camera: Camera, 
        private file: File, 
        private network: Network, 
        private toast: Toast,
        private locationAccuracy: LocationAccuracy,
        private webview: WebView, 
        private configService: ConfigService, 
        private selector: WheelSelector,
        private navCrl: NavController,
        private dataService: DataService,
        private platform: Platform,
        private androidPermissions: AndroidPermissions,
        private diagnostic: Diagnostic,
        public geolocation: Geolocation,
        private filePath: FilePath,
        public alertController: AlertController,
        private formBuilder: FormBuilder,
        private toastController: ToastController, 
        private ref: ChangeDetectorRef, 
        private httpclient: HttpClient,
        private alertCtrl: AlertController, 
        private dialogs: Dialogs, 
        public modalController: ModalController,
        private diagnostict: DeviceNativeValidationService,
        private renderer: Renderer2,
        private loadingCtrl:LoadingController
    ) {
        this.tipoexcep = '0';
        this.tipoDocu = '0';
        this.tipoDocumentoName = '';
        this.codigoexcepcion = '0';
        this.arrinputs = [];
        this.cliente_std1 = '0';
        this.cliente_std2 = 'Todos';
        this.cliente_std3 = '';
        this.cliente_std4 = '';
        this.cliente_std5 = '';
        this.cliente_std6 = '';
        this.cliente_std7 = '';
        this.cliente_std8 = '';
        this.cliente_std9 = '';
        this.cliente_std10 = '';
        this.estadoBtnRegister = false;
        this.diasSemana = [
            { dia: 'Lunes', index: false },
            { dia: 'Martes', index: false },
            { dia: 'Miercoles', index: false },
            { dia: 'Jueves', index: false },
            { dia: 'Viernes', index: false },
            { dia: 'Sabado', index: false },
            { dia: 'Domingo', index: false }
        ];

        this.tipoPersona = [
            { Desc: 'Natural', Val: 'TPN' },
            { Desc: 'Juridica', Val: 'TPJ' },
            { Desc: 'Sujeto No Domiciliado', Val: 'SND' },
            { Desc: 'Adquiriente Ticket', Val: 'ADT' }
        ];
        console.log("tipoPersona", this.tipoPersona);

        this.personaContacto = [];
        this.numeroIdentificacionTributariaLabel = 'Número de Documento';
        
        // this.checkGPSPermission();
    }

    //Check if application having GPS access permission  
    async checkGPSPermission() {
        console.log("checkGPSPermission()");
        this.androidPermissions.checkPermission(this.androidPermissions.PERMISSION.ACCESS_COARSE_LOCATION).then(
            async result => {
                console.log("result.hasPermission ", result.hasPermission);
                if (result.hasPermission) {

                    //If having permission show 'Turn On GPS' dialogue
                    this.askToTurnOnGPS();
                } else {
                    const alert = await this.alertController.create({
                        cssClass: "my-custom-class",
                        header: "GPS",
                        message: "Dar permisos de geolocalización <strong></strong>",
                        buttons: [
                            {
                                text: "Cancelar",
                                role: "cancel",
                                cssClass: "secondary",
                                handler: (blah) => {
                                    console.log("Confirm Cancel: blah");
                                    // this.estadoBtnRegister = false;
                                },
                            },
                            {
                                text: "Confirmar",
                                handler: () => {
                                    console.log("Confirm Okay");
                                    this.diagnostic.switchToLocationSettings();
                                    //this.grantRequest();
                                    this.navCrl.pop();
                                    // this.checkGPSPermission();
                                    //this.spinnerDialog.hide();
                                },
                            },
                        ],
                    });

                    await alert.present();
                    //If not having permission ask for permission
                }
            },
            err => {
                alert(err);
            }
        );
    }
    /*
        requestGPSPermission() {
            this.locationAccuracy.canRequest().then((canRequest: boolean) => {
                if (canRequest) {
                    console.log("4");
                } else {
                    //Show 'GPS Permission Request' dialogue
                    this.androidPermissions.requestPermission(this.androidPermissions.PERMISSION.ACCESS_COARSE_LOCATION)
                        .then(
                            () => {
                                // call method to turn on GPS
                                console.log("call method to turn on GPS");
                                this.askToTurnOnGPS();
                            },
                            error => {
                                //Show alert if user click on 'No Thanks'
                                console.log('requestPermission Error requesting location permissions ' + JSON.stringify(error))
    
                            }
                        );
                }
            });
        }
    */
    
    askToTurnOnGPS() {
        this.locationAccuracy.request(this.locationAccuracy.REQUEST_PRIORITY_HIGH_ACCURACY).then(
            () => {
                // When GPS Turned ON call method to get Accurate location coordinates
                this.getLocationCoordinates();
            },
            error => {
                this.diagnostic.switchToLocationSettings();
            }
        );
    }

    // Methos to get device accurate coordinates using device GPS
    async getLocationCoordinates() {
        /*this.spinnerDialog.show(null, null, true);

        setInterval(() => {
            this.spinnerDialog.hide();
        }, 3000);*/

        console.log("etLocationCoordinates() ");
        this.geolocation.getCurrentPosition().then((resp) => {
            this.getLocation();
        }).catch(async (error) => {
            // alert('Error getting location' + error);
            const alert = await this.alertController.create({
                cssClass: "my-custom-class",
                header: "GPS",
                message: "Dar permisos de geolocalización a la aplicación. <strong></strong>",
                buttons: [
                    {
                        text: "Cancelar",
                        role: "cancel",
                        cssClass: "secondary",
                        handler: (blah) => {
                            console.log("Confirm Cancel: blah");
                            // this.estadoBtnRegister = false;
                        },
                    },
                    {
                        text: "Confirmar",
                        handler: () => {
                            console.log("Confirm Okay");

                            this.diagnostic.switchToLocationSettings();
                            //this.grantRequest();
                            this.navCrl.pop();
                            // this.checkGPSPermission();
                            //this.spinnerDialog.hide();

                        },
                    },
                ],
            });

            await alert.present();
        });
    }

    private async codGenx() {
        console.log("codGenx()");
        let inix: any = await this.configService.getNumeracion();
        console.log("numero generado inicial ", inix);
        console.log("this.numerador sumar 1 ", this.numerador);
        this.numerador = (inix.numcli += 1);
        return this.numerador;
    }

    public async ngOnInit() {
        console.log("DEVD ngOnInit()");
        // debugger;
                
        this.loadImgCamera = false;
        let usuariodata: any = await this.configService.getSession();
        this.listCuccs = usuariodata[0].camposUsuario;
        this.camposclientes = usuariodata[0].config[0].camposclientes;
        let idUser = usuariodata[0].idUsuario;
        this.code = this.activatedRoute.snapshot.paramMap.get("id");

        if (this.code == "null") {
            console.log("NUEVO REGISTRO ");
            await this.carga_camposusuario('');
            let datauser: any = await this.configService.getSession();
            console.log("DATA USER ", datauser);
            this.textAction = "Registrar";
            this.imagen = "../../../assets/addPhoto.svg";
            let codx: any = await this.codGenx();
            console.log("generando codigo codx ", codx);
            console.log("atauser[0].idUsuario ", datauser[0].idUsuario);
            // let codix: any = await this.generacode(String(datauser[0].idUsuario), String(codx));
            let codix: any = await this.generacode2();
            console.log("codigo generado ", codix);
            await this.getOptionsIdent(false);
            await this.getOptionsexcepcion(false);

            let uso_fex: any = datauser[0].uso_fex;

            this.fex_tipo = uso_fex;
            this.codCliente = codix;
            this.xcodigocliente = this.codCliente;
            this.nombreCliente = "";
            this.nitci = "";
            this.razonsocial = "";
            this.direccion = "";
            this.email = "";
            this.creadopor = "";
            this.diavisita = "";
            this.comentario = "";
            this.complemento = "";
            this.personaContacto = [];
            this.rutaCliente = "";
            this.diasVisita = "";
            this.telefono = null;
            this.telefonoEmp = null;
            this.telefonoCel = null;
            this.diavisitatext = "";
            this.datalistprecio = [];
            this.listaPrecio = datauser[0].config[0].listaPrecios;
            this.listaPreciotext = "";
            this.rutaterritorisap = datauser[0].config[0].territorio;
            this.rutaterritorisaptext = datauser[0].config[0].rutaterritorisaptext;
            this.moneda = datauser[0].config[0].moneda;
            this.groupCode = datauser[0].config[0].grupoCliente;
            this.idEmpresa = -1;
            this.tipoEmpresa = "";
            //let territorios = await this.dataService.getTerritoriosFilter();
           //let territorios = await this.territorios.findAll();
            //this.territoriosJson = territorios.respuesta;
            let Territorio = new Territorios();
            this.territoriosJson = await Territorio.findAll();
            console.log("this.territoriosJson",this.territoriosJson);

            try {
                if (this.network.type != 'none') {

                    let o: any = await this.Coordenadas();
                    let latlng = {
                        lat: o.lat,
                        lng: o.lng
                    };
                    this.geocodeLatLng(latlng);
                    console.log("this.direccion ", this.direccion);
                }
            } catch (e) {
                this.latitude = 0;
                this.longitude = 0;
                this.direccion = "...";
            }
        } else {

            this.spinnerDialog.show(null, null, true);
            this.textAction = "Guardar";
            let cliente = new Clientes();
            let data: any = await cliente.selectCarCode(this.code);
            this.dataClient = data;
            console.log("A EDITAR ", JSON.stringify(data));

            await this.carga_camposusuario(data);

            this.canal = await this.getOneCanal(data.codeCanal);
            this.subCanal = await this.getOneSubCanal(data.codeSubCanal);
            this.tipoTienda = await this.getOneTipoTienda(data.codeTipoTienda);
            this.cadena = await this.getOneCadena(data.cadena);
            let dataCliente = await cliente.selectOnline(this.code);

            console.log("A EDITAR ", JSON.stringify(data));
            let contactos = new Contactos();
            this.email = data.correoelectronico == 'null' ? '' : data.correoelectronico;
            try {
                if (this.network.type != 'none') {

                    let o: any = await this.Coordenadas();
                    let latlng = {
                        lat: o.lat,
                        lng: o.lng
                    };
                    this.geocodeLatLng(latlng);
                    console.log("this.direccion ", this.direccion);
                }
            } catch (e) {
                this.latitude = 0;
                this.longitude = 0;
                // this.direccion = "...";
            }
            this.printImg();

            this.canal = await this.getOneCanal(data.codeCanal);
            this.subCanal = await this.getOneSubCanal(data.codeSubCanal);
            this.tipoTienda = await this.getOneTipoTienda(data.codeTipoTienda);
            this.cadena = await this.getOneCadena(data.cadena);
            console.log("cadena a editar ", this.cadena);

            await this.getOptionsIdent(false);
            await this.getOptionsexcepcion(false);

            let codigoexcepcion: any = usuariodata[0].fex_codigoexcepcion;
            if (codigoexcepcion.length > 0) {
                for (let x of codigoexcepcion) {
                    if (x.codigo == data.Fex_codigoexcepcion) {
                        this.codigoexcepcion = x.descripcion;
                        this.tipoexcep = x.codigo;
                    }
                }
            }

            this.tipoexcep = data.Fex_codigoexcepcion

            let uso_fex: any = usuariodata[0].uso_fex;
            this.fex_tipo = uso_fex;

            this.currentImage = data.imagen;
            this.codCliente = data.CardCode;
            this.nombreCliente = data.CardName;
            this.nitci = data.FederalTaxId;
            this.razonsocial = data.razonsocial;
            data.xcodigocliente == "" ? (this.xcodigocliente = data.CardCode) : (this.xcodigocliente = data.xcodigocliente);
            this.rutaterritorisap = data.rutaterritorisap;
            this.diavisita = data.diavisita;
            this.complemento = data.Fex_complemento == 'null' ? '' : data.Fex_complemento;

            this.tipoEmpresa = data.idEmpresa;
            this.comentario = data.comentario == 'null' ? '' : data.comentario;
            let Territorio = new Territorios();
            this.territoriosJson = await Territorio.findAll();
            console.log("this.territoriosJson",this.territoriosJson);

            //let territorios = await this.dataService.getTerritoriosFilter();
            //this.territoriosJson = territorios.respuesta;

            for (let i = 0; i < this.territoriosJson.length; i++) {
                if (this.territoriosJson[i].TerritoryID == data.territorio) {
                    this.u_territorio2 = this.territoriosJson[i].Description;
                    this.code_territorio2 = data.territorio;
                }
            }
            this.code_territorio2 = data.territorio,
                this.dataDireccion = data.Address == 'null' ? '' : data.Address;

            this.personaContacto = await contactos.selectCarCode(this.code);

            console.log("datos del contacto", this.personaContacto);

            for (let itmpc of this.personaContacto) {
                itmpc.nombrePersonaContacto = itmpc.nombre;
                itmpc.fonoPersonaContacto = itmpc.telefono;
                itmpc.comentarioPersonaContacto = itmpc.comentario;
                itmpc.tituloPersonaContacto = itmpc.titulo;
                itmpc.correoPersonaContacto = itmpc.correo;
            }

            console.log("datos de la sucursal");
            let sesion: any = await this.configService.getSession();

            let clientessucursales = new Clientessucursales();
            this.dataSucursales = await clientessucursales.findAll(this.code, 0);
            for (let x = 0; x < this.dataSucursales.length; x++) {
                if (sesion[0].campodinamicos.length > 0) {
                    for (let i = 0; i < sesion[0].campodinamicos.length; i++) {
                        if (sesion[0].campodinamicos[i].Objeto == 4) {
                            let campo = 'campousu' + sesion[0].campodinamicos[i].Nombre;
                            let aux: any;

                            this.dataSucursales[x].camposusuario = {
                                Objeto: "4",
                                campo: campo,
                                cmidd: sesion[0].campodinamicos[i].cmidd,
                                tabla: sesion[0].campodinamicos[i].tabla,
                                valor: this.dataSucursales[x][campo]
                            };

                        }
                    }
                }
            }
            console.log("RESULTADOS", this.dataSucursales);

            this.listarSucursales();

            this.diasVisita = data.diavisita;

            this.telefono = data.pesonacontactocelular;
            //   this.telefonoEmp = data.PhoneNumber;
            this.telefonoEmp = this.telefonoEmp == null ? '' : data.PhoneNumber            
            this.telefonoCel = data.celular == 'null' ? '' : data.celular;
            this.telefonoEmp = data.PhoneNumber == 'null' ? '' : data.PhoneNumber;
            this.rutaterritorisaptext = data.rutaterritorisaptext;
            this.datalistprecio = [];
            this.moneda = data.Currency;
            this.groupCode = data.GroupCode;
            this.cliente_std1 = data.cliente_std1 ? data.cliente_std1 : "0";
            this.cliente_std2 = data.cliente_std2;
            this.cliente_std3 = data.cliente_std3;
            this.cliente_std4 = data.cliente_std4;
            this.cliente_std5 = data.cliente_std5;
            this.cliente_std6 = data.cliente_std6;
            this.cliente_std7 = data.cliente_std7;
            this.cliente_std8 = data.cliente_std8;
            this.cliente_std9 = data.cliente_std9;
            this.cliente_std10 = data.cliente_std10;
            //this.optionsFilterCamposClient = JSON.parse(data.cuccs);
            let listap = new Listaprecios();
            let listaprecioarr: any = await listap.findSelect(idUser);
            for (let itm of listaprecioarr) {
                if (itm.PriceListNo == data.PriceListNum) {
                    this.listaPrecio = itm.PriceListNo;
                    this.listaPreciotext = itm.PriceListName;
                }
            }

            let terr: any = await cliente.selectTerritorios();
            console.log(" terr itorios ", terr);
            let listTerritorio = [];
            for (let t of terr)
                listTerritorio.push({
                    description: t.rutaterritorisaptext,
                    id: t.rutaterritorisap
                });
            if (listTerritorio.length > 0) {
                for (let itmrr of listTerritorio) {
                    if (itmrr.id == data.rutaterritorisap) {
                        this.rutaterritorisap = itmrr.id;
                        this.rutaterritorisaptext = itmrr.description;
                        break;
                    }
                }
            }
            this.comentario = data.comentario == 'null' ? '' : data.comentario; data.comentario;
            this.complemento = data.Fex_complemento == 'null' ? '' : data.Fex_complemento;

            this.rutaCliente = "3";
            this.diavisitatext = "";
            let diasarraux = [];

            //campos nuevos
            // debugger;
            this.tipoPersonaCode = data.U_EXX_TIPOPERS;
            this.apellidoPaterno = data.U_EXX_APELLPAT == "undefined" ? "" : (data.U_EXX_APELLPAT == "null" ? "" : data.U_EXX_APELLPAT);;
            this.apellidoMaterno = data.U_EXX_APELLMAT == "undefined" ? "" : (data.U_EXX_APELLMAT == "null" ? "" : data.U_EXX_APELLMAT);;
            this.primerNombre = data.U_EXX_PRIMERNO == "undefined" ? "" : (data.U_EXX_PRIMERNO == "null" ? "" : data.U_EXX_PRIMERNO);;
            this.segundNombre = data.U_EXX_SEGUNDNO == "undefined" ? "" : (data.U_EXX_SEGUNDNO == "null" ? "" : data.U_EXX_SEGUNDNO);
            this.tipoDocu = data.U_EXX_TIPODOCU == 6 ? "5" : (data.U_EXX_TIPODOCU == 7 ? "3" : (data.U_EXX_TIPODOCU == 0 ? "4" : (data.U_EXX_TIPODOCU == 4 ? "2" : "1")));

            let tipoDocumento: any = usuariodata[0].fex_tipoDocumento;
            if (tipoDocumento.length > 0) {
                for (let x of tipoDocumento) {
                    if (x.id == this.tipoDocu) {
                        this.tipoDocumentoName = x.descripcion;
                        // this.tipoDocu = x.id;
                    }
                }
            }

            for (let ix of this.diasSemana) {

                if (this.existDia(ix.dia)) {
                    diasarraux.push({ dia: ix.dia, index: true });
                } else {
                    diasarraux.push({ dia: ix.dia, index: false });
                }
            }
            this.diasSemana = diasarraux;

            try {
                let tipoempresa = new Tiposempresa();
                let tpempresa: any = await tipoempresa.selectTipoEmpresaId(data.tipoEmpresa);
                console.log("dato de emprea", tpempresa);
                if(tpempresa){
                    this.idEmpresa = tpempresa.id;
                    this.tipoEmpresa = tpempresa.nombre;
                }
            } catch (e) {
                console.log(e);
            }
            this.spinnerDialog.hide();
        }
    }

    public async printImg() {
        console.log("printImg()");
        let cliente = new Clientes();
        let data: any = await cliente.selectCarCode(this.code);
        console.log("this.data.img ", data.img);
        if (data.img == "" || data.img == undefined || data.img == null) {
            this.imagen = '../../../assets/broken-image.svg';
        } else {
            fetch(data.img, { method: 'HEAD' })
                .then(res => {
                    if (res.ok) {
                        this.imagen = data.img;
                        console.log('Image exists.');
                    } else {
                        console.log('Image does not exist.');
                        this.imagen = '../../../assets/broken-image.svg';
                    }
                }).catch(err => console.log('Error:', err));
        }

        /*
                if (typeof (data.img) !== 'undefined') {
                    let name = data.img;
                    let pathExternal = this.file.externalApplicationStorageDirectory;
                    this.file.checkFile(pathExternal, name).then((data) => {
                        this.imagen = this.webview.convertFileSrc(pathExternal + name);
                    }).catch((err) => {
                        this.imagen = '../../../assets/broken-image.svg';
                    })
                } else {
                    this.imagen = '../../../assets/broken-image.svg';
                }
                */
    }

    public async contactPersonDelete(pers: any) {
        this.dialogs.confirm("¿Esta seguro eliminar la persona de contacto?", "Xmobile.", ["SI", "NO"]).then((data) => {
            if (data == 1) {
                let i = this.personaContacto.indexOf(pers);
                if (i !== -1) {
                    this.personaContacto.splice(i, 1);
                }
            }
        }).catch(() => {
        })
    }

    async alertContactPerson() {
        let localCardCode = this.codCliente;
        const alert = await this.alertController.create({
            header: 'PERSONAS DE CONTACTO',
            subHeader: 'Ingresa una persona de contacto',
            mode: 'ios',
            backdropDismiss: false,
            inputs: [
                {
                    name: 'contactName',
                    type: 'text',
                    label: 'Mobile Phone',
                    placeholder: 'Nombre (Requerido)',
                    attributes: {
                        maxlength: 30,
                    }
                }, {
                    name: 'contactPhone',
                    type: 'text',
                    id: 'telefonocontacto',
                    placeholder: 'Nro Celular (Requerido)',
                    attributes: {
                        maxlength: 50,
                    }
                }, {
                    name: 'contactTitle',
                    type: 'text',
                    placeholder: 'Título EJ:(Gerente) (Requerido) ',
                    attributes: {
                        maxlength: 10,
                    }
                }, {
                    name: 'contactMail',
                    type: 'email',
                    placeholder: 'Email  EJ:(ejemplo@gmail.com)',
                    attributes: {
                        maxlength: 30,
                    }
                }, {
                    name: 'contactCommentary',
                    type: 'text',
                    placeholder: 'Comentario',
                    attributes: {
                        maxlength: 50,
                    }
                }

            ],
            buttons: [
                {
                    text: 'Cancelar',
                    role: 'cancel',
                    cssClass: 'secondary',
                    handler: () => {
                        console.log('Confirm Cancel');
                    }
                }, {
                    text: 'Agregar',
                    handler: (data) => {
                        console.log("data ", data);
                        let soloCadena = /^[A-Za-z\s]+$/;

                        if (data.contactName == "") {
                            this.toast.show("Nombre es requerido.", "4000", "top").subscribe(toast => {
                            });
                            return false;
                        }

                        if (data.contactName !== "" && !soloCadena.test(data.contactName)) {
                            this.toast.show("Nombre inválido, ingresa solo letras.", "4000", "top").subscribe(toast => {
                            });
                            return false;
                        }

                        let celularRegex = /^[0-9]{7}/i;

                        if (!celularRegex.test(data.contactPhone)) {
                            this.toast.show("Número de celular inválido, ingresa solo números.", "4000", "top").subscribe(toast => {
                            });
                            return false;
                        }
                        // if (data.contactPhone.toString().charAt(0) != '6' && data.contactPhone.toString().charAt(0) != '7') {
                        //     this.toast.show("Número de celular inválido, debe empezar con 6 - 7", "4000", "top").subscribe(toast => {
                        //     });
                        //     return false;
                        // }

                        if (data.contactTitle) {

                            let emailRegex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;


                            console.log("emailRegex.test(d.correoelectronico) ", emailRegex.test(data.contactMail));
                            if (data.contactMail !== "" && !emailRegex.test(data.contactMail)) {
                                this.toast.show("Correo electrónico no es válido.", "5000", "top").subscribe(toast => {
                                });
                                return false;
                            } else {

                                let aux = 0;
                                for (let i = 0; i < this.personaContacto.length; i++) {
                                    if (this.personaContacto[0].nombrePersonaContacto.replace(/ /g, "") == data.contactName.replace(/ /g, "")) {
                                        this.toast.show("Ya existe un contacto con el mismo nombre.", "5000", "top").subscribe(toast => {
                                        });
                                        aux = 1;
                                        return false;

                                    }
                                }
                                if (aux == 0) {
                                    this.personaContacto.push({
                                        nombrePersonaContacto: data.contactName,
                                        fonoPersonaContacto: data.contactPhone,
                                        comentarioPersonaContacto: data.contactCommentary,
                                        tituloPersonaContacto: data.contactTitle,
                                        correoPersonaContacto: data.contactMail,
                                        cardCode: localCardCode,
                                        internalcode: '',
                                    });
                                }
                            }
                        } else {
                            this.toast.show("Nombre y título obligatorios, verifique los datos antes de guardar.", "3000", "top").subscribe(toast => {
                            });
                            return false;
                        }
                    }
                }
            ]
        });

        await alert.present();
    }

    public existDia(diax) {

        let ar = this.diasVisita;

        let rr = ar.split(",");

        for (let u of rr) {

            if (u == diax) {

                return true;
            }
        }
        return false;
    }

    public generacode(user: string, total: string) {
        let u = "10000";
        let ux = u.slice(0, -user.length);
        let i = "00000";
        let ix = i.slice(0, -total.length);
        return ux + "" + user + "" + ix + "" + total;
    }

    public generacode2() {
        let xCodigo: string = "";
        if(this.tipoDocu == "1"){
            xCodigo = "C000" + this.nitci;
        }else if(this.tipoDocu == "5"){
            xCodigo = "C" + this.nitci;
        }else if(this.tipoDocu == "2"){
            xCodigo = "CE" + this.nitci;
        }

        console.log("Codigo cliente",this.nitci);
        this.codCliente = xCodigo;
        return xCodigo;
    }

    public selectDia() {
        this.diavisita = "";
        let dp = [];
        for (let dia of this.diasSemana) if (dia.index == true) dp.push(dia.dia);
        this.diavisita = JSON.stringify(dp);
        let arr = this.diavisita.slice(1, -1);
        this.diavisita = arr.replace(/"/g, "");
    }

    public async selectEmpresa() {
        let tipoempresa = new Tiposempresa();
        let tEmpresa: any = await tipoempresa.selectTipoEmpresa();
        let listTipoEmpresa = [];
        for (let t of tEmpresa)
            listTipoEmpresa.push({ description: t.nombre });
        if (listTipoEmpresa.length > 0) {
            this.selector.show({
                title: "Selecionar tipo de Empresa.",
                items: [listTipoEmpresa],
                positiveButtonText: "Seleccionar",
                negativeButtonText: "Cancelar"
            }).then((result: any) => {
                let resp: any = tEmpresa[result[0].index];
                this.tipoEmpresa = resp.nombre;
                this.idEmpresa = resp.id;
            }, (err: any) => {
                console.log(err);
            });
        }
    }

    public async selectTerritorio() {
        let cliente = new Clientes();
        let terr: any = await cliente.selectTerritorios();
        let listTerritorio = [];
        for (let t of terr)
            listTerritorio.push({
                description: t.rutaterritorisaptext,
                id: t.rutaterritorisap
            });
        if (listTerritorio.length > 0) {
            this.selector.show({
                title: "Selecionar ruta.",
                items: [listTerritorio],
                positiveButtonText: "Seleccionar",
                negativeButtonText: "Cancelar"
            }).then((result: any) => {
                let resp: any = listTerritorio[result[0].index];
                this.rutaterritorisap = resp.id;
                this.rutaterritorisaptext = resp.description;
            },
                (err: any) => {
                    console.log(err);
                }
            );
        }
    }

    public async selectListCuccs() {
        let listData = []

        for (let t of this.listCuccs)
            listData.push({
                description: t.rutaterritorisaptext,
                id: t.rutaterritorisap
            });
        if (listData.length > 0) {
            this.selector.show({
                title: "Selecionar ruta.",
                items: [listData],
                positiveButtonText: "Seleccionar",
                negativeButtonText: "Cancelar"
            }).then((result: any) => {
                let resp: any = listData[result[0].index];
                this.rutaterritorisap = resp.id;
                this.rutaterritorisaptext = resp.description;
            },
                (err: any) => {
                    console.log(err);
                }
            );
        }
    }

    public geocodeLatLng(latlng) {
        console.log("call geocodeLatLng");

        try {
            let geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: latlng }, (results, status) => {
                status === google.maps.GeocoderStatus.OK ? results[1] ? (this.direccion = results[1].formatted_address) : (this.direccion = "Sin resultado") : (this.direccion = "Sin resultado");



            });
        } catch (e) {

        }
    }

    public async selectlistaprecios() {
        try {
            this.datalistprecio = [];
            let datauser: any = await this.configService.getSession();
            let idUser = datauser[0].idUsuario;
            let listap = new Listaprecios();
            this.datalistprecio = await listap.findSelect(idUser);
            let listSucursal = [];
            for (let x of this.datalistprecio)
                listSucursal.push({ description: x.PriceListName });
            if (listSucursal.length > 0) {
                this.selector
                    .show({
                        title: "Selecciona lista de precios.",
                        items: [listSucursal],
                        positiveButtonText: "Seleccionar",
                        negativeButtonText: "Cancelar"
                    })
                    .then(
                        (result: any) => {
                            let resp: any = this.datalistprecio[result[0].index];
                            this.listaPrecio = resp.PriceListNo;
                            this.listaPreciotext = resp.PriceListName;
                        },
                        (err: any) => {
                            console.log(err);
                        }
                    );
            } else {
                this.toast.show("No tiene ninguna lista de precios.", "3000", "top").subscribe(toast => {
                });
            }
        } catch (e) {
            console.log(e);
        }
    }

    public Coordenadas() {
        console.log("Coordenadas()");
        return new Promise((resolve, reject) => {
            this.geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 10000 }).then(resp => {
                this.latitude = resp.coords.latitude;
                this.longitude = resp.coords.longitude;
                let latlng = {
                    lat: this.latitude,
                    lng: this.longitude
                };
                console.log("latlng ", latlng);
                resolve(latlng);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async removeItemFromArr(arr, item) {
        let i = arr.indexOf(item);
        if (i !== -1) {
            arr.splice(i, 1);
        }
    }

    public async registreData(data: any, datauser: any) {
        let cliente = new Clientes();
        let contactos = new Contactos();
        let clientessucursales = new Clientessucursales();

        console.log("data ", data);
        let err: any = await cliente.validate(data);

        let vt: any = await cliente.selectCarCodeValidate(data.CardCode);

        if (vt.total > 0) {
            // let codx: any = await this.codGenx();
            // console.log("generando codigo codx ", codx);
            // console.log("atauser[0].idUsuario ", datauser[0].idUsuario);
            // let codix: any = await this.generacode(String(datauser[0].idUsuario), String(codx));
            let codix: any = await this.generacode2();
            console.log("codigo generado ", codix);
            this.codCliente = codix;
        }

        // VALIDAR CONTRA SAP
        let xData: any;
        console.log("data.FederalTaxId ", data.FederalTaxId);

        let dataext: any = {
            "rut": data.FederalTaxId,
            "CardCode": data.CardCode,
            "idUser": datauser[0].idUsuario
        };

        if (this.network.type != 'none') {
            let loadingValidate = await this.loadingCtrl.create({message:"Verificando en sap..."});
            loadingValidate.present();
            try{
                //this.spinnerDialog.show("Validando Cliente");
                xData = await this.dataService.servisReportPost("v2/clientes/validate", dataext, 3);
               
                console.log(xData.data);
                let respuesta = JSON.parse(xData.data);
                console.log("respuesta", respuesta);
                if (respuesta.rut > 0) {
                    loadingValidate.dismiss();
                    this.estadoBtnRegister = false;
                  //  this.spinnerDialog.hide();
                    return this.toast.show("RUC ya existe en SAP", "3000", "top").subscribe(toast => { });
                };
                if (respuesta.serie > 0) {
                    this.estadoBtnRegister = false;
                    if (respuesta.nuevaserie > 0) {
                        loadingValidate.dismiss();
                        this.iniNum = await this.configService.getNumeracion();
                        this.iniNum.numcli = respuesta.nuevaserie;
                        await this.configService.setNumeracion(this.iniNum);
                        let codx: any = await this.codGenx();
                        // let codix: any = await this.generacode(String(datauser[0].idUsuario), String(codx));
                        let codix: any = await this.generacode2();
                        console.log("codigo generado ", codix);
                        this.codCliente = codix;
                      //  this.spinnerDialog.hide();
                        return this.toast.show("El numero de serie se actualizo registre nuvamente", "3000", "top").subscribe(toast => { });
                    } else {
                       // this.spinnerDialog.hide();
                        return this.toast.show("Error en el numero de serie sincronice nuevamente", "3000", "top").subscribe(toast => { });
                    }
                };
                loadingValidate.dismiss();
            } catch (error) {
                loadingValidate.dismiss();
               // this.spinnerDialog.hide();
                this.toast.show("Sin conexión a SAP no se pudo validar si el cliente ya existe.", "3000", "top").subscribe(toast => {
                });
            }
        }

        if (err == "OK"){
            let loadingCeate = await this.loadingCtrl.create({message:"Enviando a sap..."});
            try {
                //this.spinnerDialog.show("Procesando Clientes");              
                if (this.network.type != 'none'){
                   // this.spinnerDialog.show();
                   loadingCeate.present()
                    let respuesta = await this.dataService.exporclienlocal(data, 1);
                    console.log("Respuesta", respuesta);
                    if (respuesta.estado == 3) {
                        data.export = 10;
                        data.CardCode = respuesta.data.CardCode;

                        for (let i = 0; i < data.ContactPerson.length; i++) {
                            for (let x = 0; x < respuesta.data.ContactEmployees.length; x++) {
                                if (respuesta.data.ContactEmployees[x].Name == data.ContactPerson[i].nombrePersonaContacto) {
                                    data.ContactPerson[i].internalcode = respuesta.data.ContactEmployees[x].InternalCode
                                }
                            }
                        }

                        for (let i = 0; i < data.SucursalesCliente.length; i++) {
                            console.log("datosssssss", respuesta.data.BPAddresses);
                            for (let x = 0; x < respuesta.data.BPAddresses.length; x++) {

                                let AddressName = respuesta.data.BPAddresses[x].AddressName.replace(/ /g, "");
                                let AddresName = data.SucursalesCliente[i].AddresName.replace(/ /g, "");
                                if (AddressName == AddresName) {

                                    console.log("dato a actualizar", data.SucursalesCliente[i].CardCode);
                                    console.log("dato nuevo", respuesta.data.BPAddresses[x].BPCode);

                                    data.SucursalesCliente[i].LineNum = respuesta.data.BPAddresses[x].RowNum;
                                    data.SucursalesCliente[i].CardCode = respuesta.data.BPAddresses[x].BPCode;
                                }
                            }
                        }

                        data.export = 10;

                        console.log("datos antes de registrar", data);
                        let estado = ''
                        if(datauser[0].creacionClientesActivos == "0"){
                            estado = 'N';
                        }

                        await cliente.insertRegister(data, datauser[0].idUsuario,estado);
                        await contactos.insertRegister(data.ContactPerson, data.CardCode);
                        await clientessucursales.insertRegister(data.SucursalesCliente, datauser[0].idUsuario);
                        let inix: any = await this.configService.getNumeracion();
                        inix.numcli = this.numerador;
                        await this.configService.setNumeracion(inix);
                        //this.spinnerDialog.hide();
                        loadingCeate.dismiss();
                        this.toast.show("Cliente guardado correctamente.", "3000", "top").subscribe(toast => { });
                        this.navCrl.pop();
                    } else {
                        this.estadoBtnRegister = false;
                       // this.spinnerDialog.hide();
                       loadingCeate.dismiss();
                        this.toast.show(respuesta.mensaje, "3000", "top").subscribe(toast => { });
                    }
                } else{
                    loadingCeate.dismiss();
                    this.spinnerDialog.show("Guardando");
                    let estado = ''
                    if(datauser[0].creacionClientesActivos == "0"){
                        estado = 'N';
                    }
                    await cliente.insertRegister(data, datauser[0].idUsuario,estado);
                    await contactos.insertRegister(data.ContactPerson, data.CardCode);
                    await clientessucursales.insertRegister(data.SucursalesCliente, datauser[0].idUsuario);
                    let inix: any = await this.configService.getNumeracion();
                    inix.numcli = this.numerador;
                    await this.configService.setNumeracion(inix);
                    this.spinnerDialog.hide();
                    this.toast.show("Cliente guardado correctamente.", "3000", "top").subscribe(toast => { });
                    this.navCrl.pop();
                }
            } catch (e) {
                this.spinnerDialog.hide();
                loadingCeate.dismiss();
                console.log("ocurrio un error en algun lugar en el momento de registrar usuariso->", e);
                this.toast.show("Ocurrió un error al insertar el cliente.", "3000", "top").subscribe(toast => {
                });
            }
        } else {
            this.toast.show(err, "3000", "top").subscribe(toast => {
            });
        }
        this.estadoBtnRegister = false;
    }

    public async editData(data: any, datauser: any) {

        console.log("DATOS A EDITAR", data);
        let cliente = new Clientes();
        let err = await cliente.validate(data, true);
        
        if (err == "OK") {
            let loading = await this.loadingCtrl.create({message:"Actualizando datos..."});
            let contactos = new Contactos();
            let clientessucursales = new Clientessucursales();
            //this.spinnerDialog.show("Procesando Clientes");
            loading.present();
            if (this.network.type != 'none')
            {
               
                let respuesta = await this.dataService.exporclienlocal(data, 2);
                console.log("Respuesta", respuesta);
                if (respuesta.estado == 3) {
                    for (let i = 0; i < data.ContactPerson.length; i++) {
                        for (let x = 0; x < respuesta.data.ContactEmployees.length; x++) {
                            if (respuesta.data.ContactEmployees[x].Name == data.ContactPerson[i].nombrePersonaContacto) {
                                data.ContactPerson[i].internalcode = respuesta.data.ContactEmployees[x].InternalCode
                            }
                        }
                    }
                    for (let i = 0; i < data.SucursalesCliente.length; i++) {
                        for (let x = 0; x < respuesta.data.BPAddresses.length; x++) {
                            let AddressName = respuesta.data.BPAddresses[x].AddressName.replace(/ /g, "");
                            let AddresName = data.SucursalesCliente[i].AddresName.replace(/ /g, "");
                            if (AddressName == AddresName) {
                                data.SucursalesCliente[i].LineNum = respuesta.data.BPAddresses[x].RowNum;
                            }
                        }
                    }
                    console.log("datos antes de registrar", data);

                    await cliente.EditRegister(data, datauser[0].idUsuario);
                    await contactos.delete(data.CardCode);
                    await contactos.insertRegister(data.ContactPerson, data.CardCode);
                    await clientessucursales.delete(data.CardCode);
                    await clientessucursales.insertRegister(data.SucursalesCliente, 1);// error ojo 1 iud uder
                    //this.spinnerDialog.hide();
                    loading.dismiss()
                    this.toast.show("El cliente se actualizó correctamente.", "4000", "center").subscribe(toast => {
                    });
                    this.navCrl.pop();
                } else {
                    //this.spinnerDialog.hide();
                    loading.dismiss()
                    this.toast.show(respuesta.mensaje, "3000", "top").subscribe(toast => {
                    });
                    this.navCrl.pop();
                }
                
            } else {
                await cliente.EditRegister(data, datauser[0].idUsuario);
                await contactos.delete(data.CardCode);
                await contactos.insertRegister(data.ContactPerson, data.CardCode);
                await clientessucursales.delete(data.CardCode);
                await clientessucursales.insertRegister(data.SucursalesCliente, 1);// error ojo 1 iud uder
                //this.spinnerDialog.hide();
                loading.dismiss();
                this.toast.show("El cliente se actualizó correctamente.", "4000", "center").subscribe(toast => {
                });
                this.navCrl.pop();
            }
        } else {
            this.toast.show(err, "3000", "top").subscribe(toast => {
            });
        }
        this.estadoBtnRegister = false;
    }

    public async register() {
        console.log("register()");

        let cliente = new Clientes();

        this.estadoBtnRegister = true;

        let camposusuario = await this.datacamposusuario();

        let aux_Currency = '';
        let aux_County = '';
        let aux_Country = '';

        let datauser: any = await this.configService.getSession();
        if (this.code == "null") {
            aux_Currency = datauser[0].config[0].moneda;
            aux_County =  "0";
            aux_Country=  "0";
        }else{
            let datos: any = await cliente.selectCarCode(this.code);
            aux_Currency = datos.Currency;
            aux_County =  datos.County;
            aux_Country=  datos.Country;
        }

        let data = {
            idUser: datauser[0].idUsuario,
            CardCode: this.codCliente,
            CardName: this.nombreCliente,
            CardType: '0',
            Address: this.dataDireccion,
            CreditLimit: "0",
            MaxCommitment: "0",
            DiscountPercent: "0",
            PriceListNum: datauser[0].config[0].listaPrecios,
            SalesPersonCode: datauser[0].config[0].codEmpleadoVenta,
            Currency: aux_Currency,
            County: aux_County,
            Country: aux_Country,
            CurrentAccountBalance: "0",
            NoDiscounts: "0",
            PriceMode: "0",
            FederalTaxId: (this.nitci.trim() == '') ? '0' : this.nitci,
            PhoneNumber: this.telefonoEmp,
            ContactPerson: this.personaContacto,
            SucursalesCliente: this.dataSucursales,
            PayTermsGrpCode: "0",
            Latitude: this.latitude,
            Longitude: this.longitude,
            GroupCode: datauser[0].config[0].grupoCliente,
            User: datauser[0].idUsuario,
            territorio: this.code_territorio2,
            Status: "1000",
            DateUpdate: cliente.getFechaPicker(),
            idDocumento: "0",
            imagen: this.currentImage,
            export: 0,
            celular: this.telefonoCel,
            pesonacontactocelular: this.telefono,
            correoelectronico: this.email,
            rutaterritorisap: datauser[0].config[0].territorio,
            rutaterritorisaptext: "0",
            diavisita: this.diavisita,
            diavisitatext: this.diavisitatext,
            comentario: this.comentario,
            creadopor: datauser[0].config[0].idUser,
            xcodigocliente: this.xcodigocliente,
            fechaset: cliente.getFechaPicker(),
            fechaupdate: cliente.getFechaPicker(),
            razonsocial: (this.razonsocial.trim() == '') ? 'SIN NOMBRE' : this.razonsocial,
            idEmpresa: this.idEmpresa,
            codeCanal: this.codeCanal,
            codeSubCanal: this.codeSubCanal,
            codeTipoTienda: this.codeTipoTienda,
            cadena: this.codeCadena,
            codeCadenaConsolidador: this.codeCadenaConsolidador,
            img: this.currentImage,
            cliente_std1: this.cliente_std1 ? this.cliente_std1 : "0",
            cliente_std2: this.cliente_std2,
            cliente_std3: this.cliente_std3,
            cliente_std4: this.cliente_std4,
            cliente_std5: this.cliente_std5,
            cliente_std6: this.cliente_std6,
            cliente_std7: this.cliente_std7,
            cliente_std8: this.cliente_std8,
            cliente_std9: this.cliente_std9,
            cliente_std10: this.cliente_std10,
            Fex_tipodocumento: this.tipoDocu,
            Fex_complemento: this.complemento,
            Fex_codigoexcepcion: this.tipoexcep,
            camposusuario: camposusuario,
            U_EXX_TIPODOCU: this.tipoDocu == "5" ? 6 : (this.tipoDocu == "3" ? 7 : (this.tipoDocu == "4" ? 0 : (this.tipoDocu == "2" ? 4 : 1))),
            U_EXX_TIPOPERS: this.tipoPersonaCode,
            U_EXX_APELLPAT: this.apellidoPaterno,
            U_EXX_APELLMAT: this.apellidoMaterno,
            U_EXX_PRIMERNO: this.primerNombre,
            U_EXX_SEGUNDNO: this.segundNombre
        };

        console.log("send service register local ", data);

        let err: any = await cliente.validate(data);
        
        console.log("err validation ", err);
        if (err == "OK") {
            const alert = await this.alertController.create({
                cssClass: "my-custom-class",
                header: "Guardar datos",
                message: "Está seguro de realizar la acción?<strong></strong>",
                buttons: [
                    {
                        text: "Cancelar",
                        role: "cancel",
                        cssClass: "secondary",
                        handler: (blah) => {
                            console.log("Confirm Cancel: blah");
                            this.estadoBtnRegister = false;
                        },
                    },
                    {
                        text: "Confirmar",
                        handler: async () => {
                            console.log("Confirm Okay");
                            if (this.code == "null") {
                                console.log("NUEVO REGISTRO");
                                data.cliente_std1 = datauser[0].config[0].grupoClienteDosificacion;
                                await this.registreData(data, datauser);
                            } else {
                                console.log("EDITAR");
                               // this.spinnerDialog.show(null, null, true);
                                await this.editData(data, datauser);
                            }
                        },
                    },
                ],
            });

            await alert.present();
        } else {
            this.estadoBtnRegister = false;
            this.toast.show(err, "3000", "top").subscribe(toast => {
            });


        }
    }

    public nameCort(name: any) {
        let img = name;
        let imgx = img.split('/');
        return imgx[imgx.length - 1];
    }

    public async exportaContactos(contactosArr) {
        return new Promise(async (resolve, reject) => {
            try {
                let rx = await this.dataService.exportContactos(contactosArr);
                resolve(rx);
            } catch (e) {
                reject(e);
            }
        });
    }

    public async exportaclientes(clienteArr: any) {

        return new Promise(async (resolve, reject) => {
            try {
                let rx = await this.dataService.exportClientes(clienteArr);
                resolve(rx);
            } catch (e) {
                reject(e);
            }
        });
    }

    /* Inicio Funciones */
    public async selectImage() {

        let alert: any = await this.alertController.create({
            header: 'OBTENER FOTO.',
            mode: 'ios',

            buttons: [{
                text: 'CAMARA',
                handler: () => {
                    this.takePicture(this.camera.PictureSourceType.CAMERA);

                }
            }, {
                text: 'GALERIA',
                handler: () => {
                    this.takePicture(this.camera.PictureSourceType.PHOTOLIBRARY);

                }
            }
                , {
                text: 'CANCELAR',
                handler: () => {


                }
            }
            ]
        });
        await alert.present();

    }

    takePicture(sourceType) {
        const options: CameraOptions = {
            quality: 100,
            sourceType: sourceType,
            destinationType: this.camera.DestinationType.DATA_URL,
            encodingType: this.camera.EncodingType.JPEG,
            mediaType: this.camera.MediaType.PICTURE
        };

        this.camera.getPicture(options).then((imageData) => {
            this.currentImage = 'data:image/jpeg;base64,' + imageData;
            this.imagen = 'data:image/jpeg;base64,' + imageData;
            console.log("this.currentImage ", this.currentImage);
        }, (err) => {
            this.currentImage = "";
            // Handle error
            console.log("Camera issue:" + err);
        });
    }

    public async takePicture2(sourceType: PictureSourceType) {
        console.log("sourceType ", sourceType);
        let fileNameArchivo: any = await this.createFileName();
        console.log("fileNameArchivo ", fileNameArchivo);
        // try {
        let options: CameraOptions = {
            quality: 50,
            destinationType: this.camera.DestinationType.FILE_URI,
            sourceType: sourceType,
            saveToPhotoAlbum: true,
            correctOrientation: true,
            mediaType: this.camera.MediaType.PICTURE,
            targetWidth: 250,
            targetHeight: 250
        };
        let imagePath: any = await this.camera.getPicture(options);
        let currentName = imagePath.substr(imagePath.lastIndexOf("/") + 1);
        let correctPath = imagePath.substr(0, imagePath.lastIndexOf("/") + 1);
        this.copyFileToLocalDir(correctPath, currentName, fileNameArchivo);
        /*} catch (e) {
            this.toast.show("La imagen no pudo ser cargada por la tipo por la versión del dispositivo : "+ e, "3000", "top").subscribe(toast => {
            });
        }
        */
    }

    public createFileName() {
        return new Promise((resolve, reject) => {
            resolve(Date.now() + '-' + this.codCliente + ".jpg");
        });
    }

    public copyFileToLocalDir(namePath, currentName, newFileName) {
        this.file.copyFile(namePath, currentName, this.file.externalApplicationStorageDirectory, newFileName).then(() => {
            this.updateStoredImages(newFileName);
        }, error => {
            this.toast.show("La imagen no pudo ser cargada por la tipo por la versión del dispositivo.", "3000", "top").subscribe(toast => {
            });
        });
    }

    public async updateStoredImages(name) {
        let pathExternal = this.file.externalApplicationStorageDirectory;
        this.imagen = this.webview.convertFileSrc(pathExternal + name);
        let filePath = this.file.externalApplicationStorageDirectory + name;
        let resPath = this.pathForImage(filePath);
        let newEntry = {
            name: name,
            path: resPath,
            filePath: filePath
        };
        this.images = [newEntry];
        this.selectedImage = await this.win.Ionic.WebView.convertFileSrc(filePath);
        this.imagen = this.selectedImage;
    }

    public async presentToast(text) {
        const toast: any = await this.toastController.create({
            message: text,
            position: 'bottom',
            duration: 3000
        });
        toast.present();
    }

    public pathForImage(img) {
        if (img === null) {
            return '';
        } else {
            let converted = this.webview.convertFileSrc(img);
            return converted;
        }
    }

    public startUpload() {
        this.loadImgCamera = true;
        let imgEntry = this.images[0];
        this.file.resolveLocalFilesystemUrl(imgEntry.filePath).then(entry => {
            (entry as FileEntry).file(file => {
                const reader = new FileReader();
                reader.onloadend = async () => {
                    const formData = new FormData();
                    const imgBlob = new Blob([reader.result], { type: file.type });
                    formData.append('file', imgBlob, file.name);
                    let path = await this.configService.getIp() + "imgs/uploadtwo.php";
                    this.httpclient.post(path, formData).subscribe((res) => {
                        console.log("IMG OK");
                        this.loadImgCamera = false;
                    }, (error) => {
                        console.log("IMG ERROR", error);
                        this.loadImgCamera = false;
                    })
                };
                reader.readAsArrayBuffer(file);
            });
        }).catch(err => {
            this.presentToast('Error al leer el archivo');
            this.loadImgCamera = false;
        });
    }

    public onKeyUp(event: any) {
        const NUMBER_REGEXP = /^\s*(\-|\+)?(\d+|(\d*(\.\d*)))([eE][+-]?\d+)?\s*$/;
        let newValue = event.target.value;
        let regExp = new RegExp(NUMBER_REGEXP);

        if (!regExp.test(newValue)) {
            event.target.value = newValue.slice(0, -1);
        }

        if(event.id == "txtRUC"){
            this.generacode2();
        }
        
    }

    public onKeyUpDigits(event: any) {
        // digits and special characters
        // const NUMBER_REGEXP = /^\s*(\-|\+)?(\d+|(\d*(\.\d*)))([eE][+-]?\d+)?\s*$/;
        const NUMBER_REGEXP = /\D*\d/g;
        let newValue = event.target.value;
        let regExp = new RegExp(NUMBER_REGEXP);

        if (!regExp.test(newValue)) {
            event.target.value = newValue.slice(0, -1);
        }
    }

    public async addNewSucursal() {
        this.spinnerDialog.show(null, null, true);

        setInterval(() => {
            this.spinnerDialog.hide();
        }, 2000);
        console.log("this.dataSucursales ", this.dataSucursales);
        let nombresSucursales = [];
        this.dataSucursales.forEach(element => {
            nombresSucursales.push(
                element.AddresName
            );
        });
        this.isModal = true;

        let dataProps = {
            modo: 'NEW',
            direccion: this.direccion,
            dataSucursales: this.dataSucursales,
            nombresSucursales: nombresSucursales
        };
        console.log("realizar  props ", dataProps);
        let obj: any = { component: ModalClienteSucursalPage, componentProps: dataProps };
        let modal: any = await this.modalController.create(obj);

        modal.onDidDismiss().then(async (data: any) => {
            this.isModal = false;
            console.log("data return ", data);
            let datauser: any = await this.configService.getSession();
            console.log("datauser ", datauser);

            if (data.data.length > 0) {
                console.log("this.latitude= ", this.latitude);
                if (this.latitude == undefined || this.latitude == "undefined") {
                    console.log("no hay latitud ");
                    try {
                        let ubx: any = await this.configService.getUbicacion();
                        console.log("ubx ", ubx);
                        this.latitude = ubx.lat;
                        this.longitude = ubx.lng;
                        this.latitude = "-68.1500000";
                        this.longitude = "-16.5000000";
                    } catch (error) {
                        console.log("No encontramos la  ubicacion ");
                        // let ubx: any = await this.configService.getUbicacion();
                        this.latitude = "-68.1500000";
                        this.longitude = "-16.5000000";
                    }

                }
                this.dataSucursales.push({
                    idUser: datauser[0].idUsuario,
                    AddresName: data.data[0].AddresName,
                    Street: data.data[0].Street,
                    LineNum: data.data[0].LineNum,
                    State: 0,
                    FederalTaxId: 0,
                    CreditLimit: 0,
                    CardCode: this.codCliente, //"901000001",
                    User: datauser[0].idUsuario,
                    Status: 1, //"1",
                    DateUpdate: "",
                    idDocumento: 0,
                    TaxCode: "",//"IVA_10", NO 
                    AdresType: data.data[0].AdresType,  //"S", // select (ENTREGA S  FACTURACION B)
                    u_zona: "",// NO */
                    u_lat: this.latitude,
                    u_lon: this.longitude,
                    u_territorio: data.data[0].u_territorio, //null, // SERVICE
                    u_vendedor: datauser[0].config[0].codEmpleadoVenta,// null (USER LOGEO)
                    camposusuario: data.data[0].camposusuario
                });
                this.listarSucursales();
                console.log("objeto final  ", this.dataSucursales);
            }

        });
        return await modal.present();
    }

    public async addNewDireccion() {
        this.spinnerDialog.show(null, null, true);

        setInterval(() => {
            this.spinnerDialog.hide();
        }, 2000);
        console.log("this.dataSucursales ", this.dataSucursales);
        let nombresSucursales = [];
        this.dataSucursales.forEach(element => {
            nombresSucursales.push(
                element.AddresName
            );
        });
        this.isModal = true;
        let dataProps = {
            modo: 'NEW',
            direccion: this.direccion,
            dataSucursales: this.dataSucursales,
            nombresSucursales: nombresSucursales
        };
        console.log("realizar  props ", dataProps);
        let obj: any = { component: ModalClienteSucursalPage, componentProps: dataProps };
        let modal: any = await this.modalController.create(obj);

        modal.onDidDismiss().then(async (data: any) => {
            this.isModal = false;
            console.log("data return ", data);
            let datauser: any = await this.configService.getSession();
            console.log("datauser ", datauser);

            if (data.data.length > 0) {
                console.log("this.latitude= ", this.latitude);
                if (this.latitude == undefined || this.latitude == "undefined") {
                    console.log("no hay latitud ");
                    try {
                        let ubx: any = await this.configService.getUbicacion();
                        console.log("ubx ", ubx);
                        this.latitude = ubx.lat;
                        this.longitude = ubx.lng;
                        this.latitude = "-68.1500000";
                        this.longitude = "-16.5000000";
                    } catch (error) {
                        console.log("No encontramos la  ubicacion ");
                        // let ubx: any = await this.configService.getUbicacion();
                        this.latitude = "-68.1500000";
                        this.longitude = "-16.5000000";
                    }

                }
                this.dataSucursales.push({
                    idUser: datauser[0].idUsuario,
                    AddresName: data.data[0].AddresName,
                    Street: data.data[0].Street,
                    LineNum: data.data[0].LineNum,
                    State: 0,
                    FederalTaxId: 0,
                    CreditLimit: 0,
                    CardCode: this.codCliente, //"901000001",
                    User: datauser[0].idUsuario,
                    Status: 1, //"1",
                    DateUpdate: "",
                    idDocumento: 0,
                    TaxCode: "",//"IVA_10", NO 
                    AdresType: data.data[0].AdresType,  //"S", // select (ENTREGA S  FACTURACION B)
                    u_zona: "",// NO */
                    u_lat: this.latitude,
                    u_lon: this.longitude,
                    u_territorio: data.data[0].u_territorio, //null, // SERVICE
                    u_vendedor: datauser[0].config[0].codEmpleadoVenta// null (USER LOGEO)
                });
                this.listarSucursales();
                console.log("objeto final  ", this.dataSucursales);
            }

        });
        return await modal.present();
    }

    deleteSucursal(item) {
        console.log("delete ", item);
        this.dialogs.confirm("¿Esta seguro eliminar el registro?", "Xmobile.", ["SI", "NO"]).then((data) => {
            if (data == 1) {
                let i = this.dataSucursales.indexOf(item);
                if (i !== -1) {
                    this.dataSucursales.splice(i, 1);
                }
            }
        }).catch(() => {
        })
    }
    /**
     * geolocation
     */

    async getLocation() {
        console.log("DEVD getLocation()");
        this.platform.ready().then(() => {
            if (this.platform.is('android')) {
                this.androidPermissions.checkPermission(this.androidPermissions.PERMISSION.ACCESS_FINE_LOCATION).then(
                    result => console.log('Has permission?', result.hasPermission),
                    err => this.androidPermissions.requestPermission(this.androidPermissions.PERMISSION.ACCESS_FINE_LOCATION)
                );
                this.grantRequest();
            } else if (this.platform.is('ios')) {
                this.grantRequest();
            } else {
                this.geolocation.getCurrentPosition({ maximumAge: 3000, timeout: 10000, enableHighAccuracy: false }).then((resp) => {
                    if (resp) {
                        console.log('+++++++++++++++++++++++++++++++++++++++++++++++++++++ resp current ', resp);

                        // this.lat = resp.coords.latitude;
                        //  this.lng = resp.coords.longitude;
                        this.latitude = resp.coords.latitude;
                        this.longitude = resp.coords.longitude;
                        // this.loadmap(resp.coords.latitude, resp.coords.longitude, this.mapEle);
                        this.getAddress(resp.coords.latitude, resp.coords.longitude);
                    }
                });
            }
        });
    }
    /**
     * validar permisos
     */
    async grantRequest() {
        console.log("DEVD grantRequest()");
        this.spinnerDialog
        this.diagnostic.isLocationEnabled().then((data) => {
            if (data) {
                this.geolocation.getCurrentPosition({ maximumAge: 3000, timeout: 10000, enableHighAccuracy: false }).then((resp) => {
                    if (resp) {
                        console.log('resp GEOPOSITION SIN PROBLEMAS', resp);
                        this.latitude = resp.coords.latitude;
                        this.longitude = resp.coords.longitude;
                        // this.loadmap(resp.coords.latitude, resp.coords.longitude, this.mapEle);
                        this.getAddress(resp.coords.latitude, resp.coords.longitude);
                    }
                });
            } else {
                this.diagnostic.switchToLocationSettings();
                this.geolocation.getCurrentPosition({ maximumAge: 3000, timeout: 10000, enableHighAccuracy: false }).then(async (resp) => {
                    if (resp) {
                        console.log('DIAGNOSTIC ,', resp);


                        const alert = await this.alertController.create({
                            cssClass: "my-custom-class",
                            header: "GPS",
                            message: "Dar permisos de geolocalización a la aplicación. <strong></strong>",
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
                                    text: "Confirmar",
                                    handler: () => {
                                        console.log("Confirm Okay");

                                        this.diagnostic.switchToLocationSettings();
                                        //this.grantRequest();
                                        this.navCrl.pop();
                                        // this.checkGPSPermission();
                                        //this.spinnerDialog.hide();

                                    },
                                },
                            ],
                        });
                    }
                });
            }
        }, error => {
            // console.log('errir', error);
        }).catch(error => {
            // console.log('error', error);
        });

    }

    getAddress(lat, lng) {
        this.spinnerDialog.show(null, null, true);

        // setInterval(() => {
        //     this.spinnerDialog.hide();
        // }, 3000);
        try {


            let addres;
            const geocoder = new google.maps.Geocoder();
            const location = new google.maps.LatLng(lat, lng);
            geocoder.geocode({ 'location': location }, (results, status) => {
                this.spinnerDialog.hide();
                //console.log(results);
                addres = results[0].formatted_address;
                console.log("**********direccion en texto : ", results[0].formatted_address);
                this.direccion = results[0].formatted_address;
                //this.lat = lat;
                //this.lng = lng;
            });
        } catch (error) {
            console.error("ocurrió un error al obtener la direccion en testo");
            this.spinnerDialog.hide();
        }
        // alert("addres " + addres);
    }

    listarSucursales() {
        console.log("this.dataSucursales ", this.dataSucursales);
        console.log(" JSON.parse(localStorage.getItem territorios ", JSON.parse(localStorage.getItem("territorios")));

        

        for (let i = 0; i < this.dataSucursales.length; i++) {
            this.dataSucursales[i].labelTerritorio= "Sin territorio";
            for (let x = 0; x < this.territoriosJson.length; x++) {
                if(this.territoriosJson[x].TerritoryID == this.dataSucursales[i].u_territorio){
                    this.dataSucursales[i].labelTerritorio = this.territoriosJson[x].Description;
                }
            }
        }
       /* this.dataSucursales.forEach(element => {
            element.labelTerritorio = "Sin territorio";
            if (this.territoriosJson).filter(value => value.TerritoryID == element.u_territorio).length > 0) {
                element.labelTerritorio = JSON.parse(localStorage.getItem("territorios")).filter(value => value.TerritoryID == element.u_territorio)[0].Description;


            }
        });*/
        console.log("this.dataSucursales ", this.dataSucursales);
    }

    async getOneCanal(codeCanal) {

        let model = new companex_canal();
        try {
            let dataCombo: any = await model.getOneCanal(codeCanal);
            console.log("dataCombo a devolver ", dataCombo);
            this.codeCanal = dataCombo[0].code;


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
            this.codeSubCanal = dataCombo[0].code;

            return dataCombo[0].name;
        } catch (error) {
            return '';
        }

    }

    async getOneTipoTienda(codeTipoTienda) {
        try {
            let model = new companex_canal();
            let dataCombo: any = await model.getOneTipoTienda(codeTipoTienda);
            this.codeTipoTienda = dataCombo[0].code;

            return dataCombo[0].name;
        } catch (error) {
            return '';
        }

    }

    async getOneCadena(codeCadena) {
        try {
            let model = new companex_canal();
            let dataCombo: any = await model.getOneCadena(codeCadena);
            console.log("dataCombo ", dataCombo);
            this.validarExistCadena = true;
            this.codeCadena = dataCombo[0].code;
            return dataCombo[0].name;
        } catch (error) {
            return '';
        }
    }

    async backCancel() {
        console.log("  this.navCrl.pop(); ");
        const alert = await this.alertController.create({
            cssClass: "my-custom-class",
            header: "Salir sin guardar",
            //  message: "Se moverá el mapa a una direccion por defecto (La paz) <strong></strong>...",
            buttons: [
                {
                    text: "Cancelar",
                    role: "cancel",
                    cssClass: "secondary",
                    handler: (blah) => {
                        console.log("Confirm Cancel: blah");
                        // this.estadoBtnRegister = false;
                    },
                },
                {
                    text: "Confirmar",
                    handler: () => {
                        this.navCrl.pop();



                    },
                },
            ],
        });

        await alert.present();

    }

    public async getOptionsIdent(x = true) {
        try {
            let arrxx = [];
            let conarr: any = await this.configService.getSession();
            let tipoDocumento: any = conarr[0].fex_tipoDocumento;

            if (tipoDocumento.length > 0) {
                for (let x of tipoDocumento) {
                    arrxx.push({
                        description: x.descripcion,
                        PaymentTermsGroupName: x.descripcion,
                        GroupNumber: x.id,
                    });
                }
            }
            let tipoDocumentoauxtotal = _.sortBy(arrxx, [(o) => o.GroupNumber]);
            let tipoDocumentoaux = _.uniqBy(tipoDocumentoauxtotal, (o) => o.GroupNumber);

            console.log("tipoDocumentoaux2 ", tipoDocumentoaux);
            let arrx = [];
            if (tipoDocumentoaux.length > 1 && x == true) {
                for (let x of tipoDocumentoaux)
                    arrx.push({ description: x.PaymentTermsGroupName });
                this.selector.show({
                    title: "SELECCIONAR TIPO DE DOCUMENTO",
                    items: [arrx],
                    positiveButtonText: "SELECCIONAR",
                    negativeButtonText: "CANCELAR"
                }).then((result: any) => {
                    this.arrinputs = [];
                    let ux: any = tipoDocumentoaux[result[0].index];
                    this.tipoDocumentoName = ux.PaymentTermsGroupName;
                    this.tipoDocu = ux.GroupNumber;
                }, (err: any) => {
                    console.log(err);
                });
            } else {
                this.arrinputs = [];
                let u: any = tipoDocumentoaux[0];
                this.tipoDocumentoName = u.PaymentTermsGroupName;
                this.tipoDocu = u.GroupNumber;
            }
        } catch (e) {
            console.log(e);
        }
    }

    public async getOptionsexcepcion(x = true) {
        try {
            let arrxx = [];
            let conarr: any = await this.configService.getSession();
            let codigoexcepcion: any = conarr[0].fex_codigoexcepcion;

            if (codigoexcepcion.length > 0) {
                for (let x of codigoexcepcion) {
                    arrxx.push({
                        description: x.descripcion,
                        PaymentTermsGroupName: x.descripcion,
                        GroupNumber: x.codigo,
                    });
                }
            }
            let codigoexcepcionauxtotal = _.sortBy(arrxx, [(o) => o.GroupNumber]);
            let codigoexcepcionaux = _.uniqBy(codigoexcepcionauxtotal, (o) => o.GroupNumber);

            console.log("XXXXXXXXX ", codigoexcepcionaux);
            let arrx = [];
            if (codigoexcepcionaux.length > 1 && x == true) {
                for (let x of codigoexcepcionaux)
                    arrx.push({ description: x.PaymentTermsGroupName });
                this.selector.show({
                    title: "SELECCIONAR UNA EXCEPCION",
                    items: [arrx],
                    positiveButtonText: "SELECCIONAR",
                    negativeButtonText: "CANCELAR"
                }).then((result: any) => {
                    this.arrinputs = [];
                    let ux: any = codigoexcepcionaux[result[0].index];
                    this.codigoexcepcion = ux.PaymentTermsGroupName;
                    this.tipoexcep = ux.GroupNumber;
                }, (err: any) => {
                    console.log(err);
                });
                console.log(this.tipoexcep);
            } else {
                this.arrinputs = [];
                let u: any = codigoexcepcionaux[0];
                this.codigoexcepcion = u.PaymentTermsGroupName;
                this.tipoexcep = u.GroupNumber;
            }
            console.log(this.tipoexcep);
        } catch (e) {
            console.log(e);
        }
    }

    public async addDireccion() {

        if (this.network.type != 'none'){
            this.spinnerDialog.show(null, null, true);
            setInterval(() => {
                this.spinnerDialog.hide();
            }, 2000);
            this.isModal = true;
            let dataProps: any;
            console.log("data direccion",this.latitude," - ",this.longitude);
            if (this.dataDireccion != '') {
                dataProps = {
                    modo: 'used',
                    lat: this.dataClient && this.dataClient.Latitude != null && this.dataClient.Latitude != '' ? Number(this.dataClient.Latitude) : this.latitude,
                    lng: this.dataClient && this.dataClient.Longitude!=null && this.dataClient.Longitude!=''?Number(this.dataClient.Longitude): this.longitude
                };
            } else {
                dataProps = {
                    modo: 'NEW',
                    lat: '',
                    lng: ''
                };
            }
            console.log("Datos enviados ", dataProps);
            let obj: any = { component: ModalMapaPage, componentProps: dataProps };
            let modal: any = await this.modalController.create(obj);
            modal.onDidDismiss().then(async (data: any) => {
                this.isModal = false;
                console.log("data return ", data.data);
                if (data.data != undefined) {

                    this.latitude = data.data.lat;
                    this.longitude = data.data.lng;

                    let addres;
                    const geocoder = new google.maps.Geocoder();
                    const location = new google.maps.LatLng(data.data.lat, data.data.lng);
                    geocoder.geocode({ 'location': location }, (results, status) => {
                        addres = results[0].formatted_address;
                        console.log("**********direccion en texto : ", results[0].formatted_address);
                        this.dataDireccion = results[0].formatted_address;
                    });
                }
            });
            return await modal.present();
        }else{
            this.toast.show(`No se puede obtener ubicacion en modo Offline.`, '4000', 'center').subscribe(toast => {
            })
        }
    }

    listarTerritorios() {
        if (this.territoriosJson.length > 0) {
            let arr = [];
            for (let x of this.territoriosJson)
                arr.push({ description: x.Description });
            this.selector.show({
                title: "TERRITORIO.",
                items: [arr],
                positiveButtonText: "SELECCIONAR",
                negativeButtonText: "CANCELAR"
            }).then((result: any) => {
                // this.litPreciosSelect = this.territoriosJson[result[0].index];


                this.u_territorio2 = result[0].description;
                this.code_territorio2 = this.territoriosJson.filter(value => value.Description == this.u_territorio2)[0].TerritoryID;

            }, (err: any) => {
                console.log(err);
            });
        }
    }

    /* CAMPOS DINAMICOS DE USUARIO*/

    public async carga_camposusuario(datos) {
        console.log("llaga aqui0");
        let usuariodata: any = await this.configService.getSession();
        let contenedorcampos = '';

        if (usuariodata[0].campodinamicos.length > 0) {

            contenedorcampos = await this.dataService.createcampususer(usuariodata[0].campodinamicos, this.idfrom, datos);
        }

        const div: HTMLDivElement = this.renderer.createElement('div');
        div.className = "col-md-12";
        div.innerHTML = contenedorcampos;
        this.renderer.appendChild(document.getElementById("contenedorcampos"), div);


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

}
