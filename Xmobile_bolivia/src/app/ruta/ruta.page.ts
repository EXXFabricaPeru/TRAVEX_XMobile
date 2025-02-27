import {
    GoogleMaps, GoogleMap, ILatLng, GoogleMapsEvent, GoogleMapOptions,
    CameraPosition, MarkerOptions, Marker, Environment, HtmlInfoWindow, Polyline, CircleOptions, Circle, LatLng,
    MyLocationOptions, MarkerIcon
} from '@ionic-native/google-maps';
import { Component, OnInit } from '@angular/core';
import { LocationAccuracy } from '@ionic-native/location-accuracy/ngx';
import { Clientes } from "../models/clientes";
import { AlertController, ModalController, NavController, Platform } from "@ionic/angular";
import { ModalclientePage } from "../public/modalcliente/modalcliente.page";
import { Toast } from "@ionic-native/toast/ngx";
import { ConfigService } from "../models/config.service";
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { AndroidPermissions } from '@ionic-native/android-permissions/ngx';
import { Diagnostic } from '@ionic-native/diagnostic/ngx';
import { Geolocation, GeolocationOptions, Geoposition, PositionError } from '@ionic-native/geolocation/ngx';
import { Documentos } from '../models/documentos';
import * as moment from 'moment';
import { VisitasPage } from "../../app/public/visitas/visitas.page";
import { ClientePage } from '../public/cliente/cliente.page';
import { DetallepagoPage } from '../public/detallepago/detallepago.page';
import { Network } from '@ionic-native/network/ngx';
import { DataService } from '../services/data.service';
import { GlobalConstants } from "../../global";

declare var google;

@Component({
    selector: 'app-ruta',
    templateUrl: './ruta.page.html',
    styleUrls: ['./ruta.page.scss'],
})
export class RutaPage implements OnInit {
    public map: GoogleMap;
    public data: any;
    public searchData: string;
    private clienteData: Clientes;
    public items: any;
    private lmt: number;
    public oringen: string;
    public ruta: any;
    public poliline: Polyline;
    public lat: any;
    public lng: any;
    public mRoadPolyline: any;
    public marckauser: Marker;
    public isenabled: boolean;
    public timeOut: any;
    public clientes: any;
    public iniciador:number;
    documentos = new Documentos()
    fechaData: any;
    fechamax: any;
    modelDoc = new Documentos()
    tipoVendedor;
    array_marcadores = new Array();
    poligono: any;
    dataDia: any;
    docsCardCode: any;
    pagosCardCode: any;
    NoVisitasCardCode: any;
    fechaMoment: String;
    onlyCustomers: boolean = false;
    public dataSesion: any;
    public weekday = 0;
    public diasSemana:any;
    constructor(private locationAccuracy: LocationAccuracy, private toast: Toast, private network: Network, private configService: ConfigService, private spinnerDialog: SpinnerDialog,
        private navCrl: NavController, public modalController: ModalController, public alertController: AlertController,
        private androidPermissions: AndroidPermissions,
        private diagnostic: Diagnostic,
        public geolocation: Geolocation,
        private platform: Platform,
        private dataService:DataService
    ) {
        this.items = [];
        this.lmt = 30;
        this.oringen = "";
        this.clienteData = new Clientes();
        this.ruta = [];
        this.mRoadPolyline = null;
        this.isenabled = false;
    }



    public async ngOnInit() {
        // this.existIn("1003200001");
        let todate= new Date()
        this.weekday = todate.getDay();
        console.log("dia de la semana->",this.weekday);
        this.diasSemana = [
            {
                name: 'radio1',
                type: 'radio',
                label: 'Lunes',
                value: 'Lunes',
               // checked: true
            }, {
                name: 'radio2',
                type: 'radio',
                label: 'Martes',
                value: 'Martes'
            }, {
                name: 'radio3',
                type: 'radio',
                label: 'Miercoles',
                value: 'Miercoles'
            }, {
                name: 'radio4',
                type: 'radio',
                label: 'Jueves',
                value: 'Jueves'
            }, {
                name: 'radio5',
                type: 'radio',
                label: 'Viernes',
                value: 'Viernes'
            }, {
                name: 'radio6',
                type: 'radio',
                label: 'Sabado',
                value: 'Sabado'
            }, {
                name: 'radio6',
                type: 'radio',
                label: 'Domingo',
                value: 'Domingo'
            }];
        this.diasSemana.forEach((data, index) =>{
            if ((index + 1) == this.weekday){ 
                data.checked = true
                this.dataDia = data.value;
            } else{ 
                data.checked = false
            } 
        });
        try
        {
          
            this.dataSesion = await this.configService.getSession();
            let poli: any = await this.dataService.actionPoligono({ "idvendedor": this.dataSesion[0].idUsuario });
            let dataRutas: any = await this.configService.setPoligono(JSON.parse(poli.data));//
        } catch (error) {
            console.log("error al descargar la data", error);
        }
     
        this.iniciador = 0;
        let validClient = await this.clienteData.selectUltimo();

        console.log("DEVD validClient ", validClient);
        this.fechaMoment = moment().format('YYYY-MM-DD');
        console.log(" DEVD this.fechaMoment ", this.fechaMoment);
        this.poligono = await this.configService.getPoligono();
        console.log(" DEVD this.poligono  ", this.poligono);
        this.docsCardCode = await this.modelDoc.selectAllDoc(this.fechaMoment)
        this.pagosCardCode = await this.modelDoc.selectAllPagos(this.fechaMoment)
        this.NoVisitasCardCode = await this.modelDoc.selectInVisitas(this.fechaMoment)
        console.log(" docsCardCode ", this.docsCardCode);
        console.log("pagosCardCode ", this.pagosCardCode);
        console.log("NoVisitasCardCode ", this.NoVisitasCardCode);

        if (!this.poligono.respuesta) {
            this.tipoVendedor = 100;
            this.onlyCustomers = true;
            this.toast.show(`No encontramos rutas registradas, Se cargarán las ubicaciones de los clientes de acuerdo al territorio asignado.`, '9000', 'center').subscribe(toast => { });
        }

        try {
            this.tipoVendedor = this.poligono.respuesta[0].tipoVendedor;
        } catch (error) {
            this.tipoVendedor = 100;
        }


        if (!validClient) {
            this.tipoVendedor = 100;
            this.toast.show(`No encontramos clientes en tu cartera.`, '9000', 'top').subscribe(toast => {
            });
        }
        console.log("  this.tipoVendedor ", this.tipoVendedor);
        this.documentos = new Documentos()
        this.fechaData = this.documentos.getFechaPicker();
        this.fechamax = this.documentos.getFechaPickerMasAnio();
        console.log("fechaData ", this.fechaData);
        console.log("fechamax ", this.fechamax);
        /*
                try {
                    let canRequest: boolean = await this.locationAccuracy.canRequest();
                    if (canRequest) {
                        this.locationAccuracy.request(this.locationAccuracy.REQUEST_PRIORITY_HIGH_ACCURACY).then(() => {
        
                        }, (error) => {
                            console.log("DEVD ERR1 ", error);
                        });
                    }
                } catch (e) {
                    console.log("DEVD ERR2 ", e);
                }
                */
        this.platform.ready().then(async () => {
            this.spinnerDialog.show(null, null, true);
            try {
                await this.GPSPromise();
                console.log("DEVD Continua con el mapa ...");
                this.toast.show(`Ubicación obtenida con éxito.`, '5000', 'top').subscribe(toast => {
                });
                this.spinnerDialog.hide();
            } catch (error) {
                if (localStorage.getItem("lat") != "") {
                    this.toast.show(`Última ubicación obtenida con éxito.`, '5000', 'top').subscribe(toast => {
                    });
                    this.lat = Number(localStorage.getItem("lat"));
                    this.lng = Number(localStorage.getItem("lng"));
                } else {
                    this.toast.show(`No encontramos tu geoposición, revisa tu red y configuración de GPS.`, '9000', 'center').subscribe(toast => {
                    });
                    console.log("DEVD error controlado GPS ", error);
                }


                this.spinnerDialog.hide();
            }
            console.log("DEVD al final  this.lat  ", this.lat);
            await this.loadMap();

            if (!this.lat) {
                this.tipoVendedor = 100;
            } else {
                await this.posicion();
            }

            //await this.poligonoFunction();


        });
    }

    GPSPromise = () => {
        console.log("GPSPromise()");
        return new Promise((resolve, reject) => {
            /*  if (false) {
                  this.lat = "123";
                  setTimeout(() => {
                      console.log("Verificando GPS...");
                      resolve(true);
                  }, 3000);
  
              } else {
                  this.lat = "0";
                  reject(false);
              }
              */
            //
            this.androidPermissions.checkPermission(this.androidPermissions.PERMISSION.ACCESS_COARSE_LOCATION).then(
                async result => {
                    console.log("result.hasPermission ", result.hasPermission);
                    if (result.hasPermission) {

                        //If having permission show 'Turn On GPS' dialogue

                        this.locationAccuracy.request(this.locationAccuracy.REQUEST_PRIORITY_HIGH_ACCURACY).then(
                            () => {
                                // When GPS Turned ON call method to get Accurate location coordinates
                                //this.getLocationCoordinates();

                                console.log("es ANDROID Y TIENE PERMISOS");
                                this.geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 20000 }).then((resp) => {
                                    //   this.geolocation.getCurrentPosition({ maximumAge: 3000, timeout: 10000, enableHighAccuracy: false }).then((resp) => {
                                    if (resp) {
                                        this.spinnerDialog.hide();
                                        console.log('resp GEOPOSITION SIN PROBLEMAS', resp);
                                        this.lat = resp.coords.latitude;
                                        this.lng = resp.coords.longitude;
                                        // this.loadmap(resp.coords.latitude, resp.coords.longitude, this.mapEle);

                                        console.log("this.lat  ", this.lat);
                                        console.log("this.lng  ", this.lng);
                                        // this.marckauser = this.map.addMarkerSync({
                                        //    position:  
                                        //     });

                                        let ubi = { lat: parseFloat(this.lat), lng: parseFloat(this.lng) };
                                        /* this.map.addMarkerSync({
                         
                                             position: ubi,
                                         });
                                         */

                                        resolve(true);
                                        // this.map.setCameraZoom(17);
                                        // this.map.setCameraTarget(ubi);
                                    } else {
                                        this.spinnerDialog.hide();

                                    }
                                }).catch(error => {
                                    console.log("error controlado get current position  ", error);
                                    reject(false);
                                });



                            },
                            error => {
                                // this.diagnostic.switchToLocationSettings();
                                reject(false);

                            }
                        );
                    } else {
                        const alert = await this.alertController.create({
                            cssClass: "my-custom-class",
                            header: "GPS no encontrado",
                            message: "Dar permisos de geolocalización <strong></strong>",
                            buttons: [
                                {
                                    text: "Cancelar",
                                    role: "cancel",
                                    cssClass: "secondary",
                                    handler: (blah) => {
                                        console.log("Confirm Cancel: blah");
                                        this.navCrl.navigateRoot(`home`);
                                        // this.estadoBtnRegister = false;
                                    },
                                },
                                {
                                    text: "Confirmar",
                                    handler: () => {
                                        console.log("Confirm Okay");

                                        this.diagnostic.switchToLocationSettings();
                                        //this.grantRequest();
                                        this.navCrl.navigateRoot(`home`);
                                        //navigator['app'].exitApp();
                                        // this.checkGPSPermission();
                                        //this.spinnerDialog.hide();

                                    },
                                },
                            ],
                        });

                        await alert.present();

                        reject(false);
                        //If not having permission ask for permission
                    }
                },
                err => {
                    console.log("Error permisions ");
                    reject(false);
                    // alert(err);
                }
            );


        })
    }

    async reloadMap()
    {
        try {
            let poli: any = await this.dataService.actionPoligono({ "idvendedor": this.dataSesion[0].idUsuario });
            let dataRutas: any = await this.configService.setPoligono(JSON.parse(poli.data));//

   
            this.poligono = dataRutas
        } catch (error) {
            console.log("error al descargar la data", error);
        }
        this.docsCardCode = await this.modelDoc.selectAllDoc(this.fechaMoment)
        this.pagosCardCode = await this.modelDoc.selectAllPagos(this.fechaMoment)
        this.NoVisitasCardCode = await this.modelDoc.selectInVisitas(this.fechaMoment)

        console.log("datafecha ", this.fechaData);

        this.spinnerDialog.show(null, null, true);
        this.map.clear();
        await this.posicion();

        setTimeout(async () => {

            await this.poligonoFunction();


            this.spinnerDialog.hide();


        }, 2000);
    }
    clickMap() {
        console.log("clickMap()");
        this.map.clear();
        this.spinnerDialog.show(null, null, true);
        setTimeout(() => {
            this.spinnerDialog.hide();


        }, 2000);

    }

    existIn = async (cardCode) => {
        let cardCodesDocsPagos = [];
        let cardCodesNovisitas = [];
        //  let pagosCardCode: any = await this.modelDoc.selectAllPagos(cardCode, this.fechaData)
        //  let NoVisitasCardCode: any = await this.modelDoc.selectInVisitas(cardCode, this.fechaData)
        // console.log("docs ", docsCardCode.length);
        //console.log("pagosCardCode ", pagosCardCode);
        //  console.log("EXIST IN docsCardCode", this.docsCardCode);
        this.docsCardCode.forEach(element => {
            if (element.CreateDate == this.fechaData) {
                //  console.log(" cliente encontrado en documentos  ", element)
                cardCodesDocsPagos.push(
                    element.CardCode
                );
            }
        });

        this.pagosCardCode.forEach(element => {
            if (element.fecha == this.fechaData) {
                // console.log(" cliente encontrado en pagos  ", element)
                cardCodesDocsPagos.push(
                    element.clienteId
                );
            }
        });

        this.NoVisitasCardCode.forEach(element => {
            if (element.fecha == this.fechaData) {
                // console.log(" cliente encontrado en no visitas  ", element)
                cardCodesNovisitas.push(
                    element.CartCode
                );
            }
        });

        if (cardCodesDocsPagos.includes(cardCode)) {
            return true;
        } else if (cardCodesNovisitas.includes(cardCode)) {
            return false;
        }

        /*  if (docsCardCode.length > 0 && pagosCardCode.length > 0) {
              return true;
          } else if (NoVisitasCardCode.length > 0) {
              return false;
          }
          */
        return null;
    }

    public async customersFunction() {
        this.clientes = await this.clienteData.findClienteUbicaciones()
        // let  clientesAux =  this.clientes.map((item, index)=>{
        //     return item.CardCode
        // })
        console.log("clientes ", this.clientes);
        this.clientes.forEach(async (cliente, index) => {
            index = index + 1;
            let ubi = { lat: parseFloat(cliente.Latitude), lng: parseFloat(cliente.Longitude) };


            let colorValid: boolean | null = null;
            try {
                if (this.fechaMoment == this.fechaData) {
                    colorValid = await this.existIn(cliente.CardCode);
                }
                let statusVisit = colorValid; //await this.existIn(poli.cardcode); //null; //a
                if (statusVisit == true) {
                    cliente.condition = true;
                }
                if (statusVisit == false) {
                    cliente.condition = false;
                }
                if (statusVisit == null) {
                    cliente.condition = null;
                }
            } catch (error) {
                console.log("---------CRASHEO?");
            }


            let imgMarker;
            let markerOption: MarkerOptions;

            if (cliente.condition == null) {
                if (Number(index) <= 99) {
                    imgMarker = `./assets/markers${index}.png`;
                } else {
                    imgMarker = `./assets/markers100.png`;
                }
            } else {
                if (cliente.condition == true) {
                    imgMarker = `./assets/markerCheck.png`;
                }
                if (cliente.condition == false) {
                    imgMarker = `./assets/markerUncheck.png`;
                }
                //  console.log("cliente.condition ", cliente.condition);
            }


            markerOption = {
                position: ubi,
                identificador: index,
                // title: "[" + cliente.cardcode + "]",
                // snippet: "" + cliente.cardname,
                icon: {
                    url: imgMarker,
                    size: {
                        width: 40,
                        height: 50
                    }
                }
            };

            let markerOne = this.map.addMarker(markerOption).then((marker: Marker) => {
                this.array_marcadores.push(marker);

                marker.on(GoogleMapsEvent.MARKER_CLICK).subscribe(() => {
                    //   alert("event click " + cliente.cardname);
                    // if (cliente.condition == null) {
                    this.optionsMarker({ posicion: index, cardcode: cliente.CardCode, latitud: cliente.Latitude, lonitud: cliente.Longitude, cardname: cliente.CardName });
                    //   }

                    //  this.navCrl.navigateForward(`cliente/` + obj.CardCode);
                });
                //  marker.showInfoWindow();

            });

        })

    }
    public async poligonoFunction() {

        if (!this.lat) {
            return false;
        }


        if (this.onlyCustomers) {
            await this.customersFunction();
        } else {

            //console.log("poligono()  ");
            let poligonoDIA: any = [];
            //try {
            let model = new Documentos();


            console.group("POLIGONOS RUTAS ");
            console.log("poligono ", this.poligono);
            console.log("TIPO VENDEDOR ", this.poligono.respuesta[0].tipoVendedor);

            /*   let markersUsados: any = await this.configService.getActionMarker();
               markersUsados = markersUsados.filter((item) => {
                   return item.dateUse == this.fechaData;
               });
               console.log("markersUsados ", markersUsados);
       */
            if (this.poligono.respuesta.length > 0) {

                // console.log("hay poligonos ");
                //  console.log("this.fechaData ", this.fechaData);


                if (this.tipoVendedor == 0) {
                    poligonoDIA = this.poligono.respuesta.filter((n) => {
                        return n.dia == this.dataDia;
                    });
                }
                if (this.tipoVendedor == 1) {
                    poligonoDIA = this.poligono.respuesta.filter((n) => {
                        return n.fechaRegistro == this.fechaData;
                    });
                }
                console.log("poligonoDIA ", poligonoDIA);

                // poligonoDIA[0].poligono.sort((a, b) => b.posicion - a.posicion);
                //  console.log("poligonoDIA ordenado ", poligonoDIA[0].poligono);

                // let arrxx = [];
                let posicionInicialLat = this.lat;
                let posicionInicialLng = this.lng;
                let posicionFinalLat = 0;
                let posicionFinalLng = 0;
                if (poligonoDIA.length > 0) {

                    console.group("**DEVD VALIDACION ESTADO *");

                    for await (let poli of poligonoDIA[0].poligono) {

                        let colorValid = null;
                        try {
                            if (this.fechaMoment == this.fechaData) {
                                colorValid = await this.existIn(poli.cardcode);
                            }
                            let statusVisit = colorValid; //await this.existIn(poli.cardcode); //null; //a
                            if (statusVisit == true) {
                                poli.condition = true;
                            }
                            if (statusVisit == false) {
                                poli.condition = false;
                            }
                            if (statusVisit == null) {
                                poli.condition = null;
                            }
                        } catch (error) {
                            console.log("---------CRASHEO?");
                        }


                    }

                    console.groupEnd();
                    if (this.network.type == 'none') {
                        return this.poligonoOfflineFunction(poligonoDIA);
                    }
                    this.spinnerDialog.show(null, null, true);
                    // }
                    //     console.log("************** fin usados");
                    //  }
                    let i = 0;
                    for await (let poli of poligonoDIA[0].poligono) {
                        i = i + 1;
                        console.log("posicion i ", poli.posicion, poli);
                        posicionFinalLat = poli.latitud;
                        posicionFinalLng = poli.longitud;
                        // [0].poligono.sort((a, b) => Number(b.posicion) - Number(a.posicion));
                        let ubi = { lat: parseFloat(poli.latitud), lng: parseFloat(poli.longitud) };

                        let imgMarker;
                        let markerOption: MarkerOptions;
                        /*
                                            if (poli.condition == true) {
                                                imgMarker = `./assets/markerCheck.png`;
                                                console.log(" imgMarker ", imgMarker);
                                            } if (poli.condition == false) {
                                                imgMarker = `./assets/markerUncheck.png`;
                                                console.log(" imgMarker ", imgMarker);
                                            }
                                            */
                        if (poli.condition == null) {
                            if (Number(poli.posicion) <= 99) {
                                imgMarker = `./assets/markers${poli.posicion}.png`;
                            } else {
                                imgMarker = `./assets/markers100.png`;
                            }
                        } else {
                            if (poli.condition == true) {
                                imgMarker = `./assets/markerCheck.png`;
                            }
                            if (poli.condition == false) {
                                imgMarker = `./assets/markerUncheck.png`;
                            }
                            //  console.log("poli.condition ", poli.condition);
                        }


                        markerOption = {
                            position: ubi,
                            identificador: poli.posicion,
                            // title: "[" + poli.cardcode + "]",
                            // snippet: "" + poli.cardname,
                            icon: {
                                url: imgMarker,
                                size: {
                                    width: 40,
                                    height: 50
                                }
                            }
                        };

                        let markerOne = this.map.addMarker(markerOption).then((marker: Marker) => {
                            this.array_marcadores.push(marker);

                            marker.on(GoogleMapsEvent.MARKER_CLICK).subscribe(() => {
                                //   alert("event click " + poli.cardname);
                                // if (poli.condition == null) {
                                this.optionsMarker(poli);
                                //   }

                                //  this.navCrl.navigateForward(`cliente/` + obj.CardCode);
                            });
                            //  marker.showInfoWindow();

                        });

                        posicionInicialLat = posicionFinalLat;
                        posicionInicialLng = posicionFinalLng;

                        console.log("i ", i);
                        if (i == 1000) {
                            this.toast.show(`Cargando markers...`, '10000', 'top').subscribe(toast => {
                            });
                        }
                        console.log("(poligonoDIA..poligonolength - 1) ", (poligonoDIA[0].poligono.length - 1));
                        if ((poligonoDIA[0].poligono.length - 1) == i) {

                            console.log("CERRAR SPINER ");
                        }
                    }
                    setTimeout(() => {
                        this.spinnerDialog.hide();
                    }, 2000);

                    this.map.setCameraZoom(10);

                } else {
                    if (this.tipoVendedor == 1) {
                        this.toast.show(`No se encontraron rutas registradas para la fecha seleccionada.`, '10000', 'top').subscribe(toast => {
                        });
                    }
                    if (this.tipoVendedor == 0) {
                        this.toast.show(`No se encontraron rutas registradas para el día seleccionado.`, '10000', 'top').subscribe(toast => {
                        });
                    }

                }
                console.groupEnd();

            }
        }

    }

    poligonoOfflineFunction = async (poligonoDIA) => {
        console.log("offline ", poligonoDIA)
        let dataClients: any = "";
        if (poligonoDIA.length > 0) {
            for await (let poli of poligonoDIA[0].poligono) {
                if (poli.condition) {
                    poli.condition = "VISITADO";
                }
                if (!poli.condition) {
                    poli.condition = "NO VISITADO";
                }
                if (poli.condition === null) {
                    poli.condition = "PENDIENTE VISITA";
                }
                dataClients = ` <li> ${poli.cardcode} <br>${poli.cardname} (${poli.condition}).</li>` + dataClients;

            }

        }
        this.alertController.create({
            header: `Clientes`,
            // subHeader: `resumen`,
            mode: 'ios',
            message: `   
     
            <ul>
         ${dataClients}
          
            </ul>`,
            cssClass: 'alertClients',
            buttons: ['Okay']

        }).then(res => {
            res.present();
        });
    }
    async optionsMarker(poli) {
        // preventista
        /*   
               solo pedidos 
               vista de deudas 
               no visita
               //repartidor
               factura
               pedidos importados
       
       
       
               desde el documento poder registrar no visita
       */
        let tc;
        let respUs: any = await this.configService.getSession();
        console.log("respUs ", respUs);

        let cambioparalelo: any = respUs[0].tipocambioparalelo;
        console.log("cambioparalelo ", cambioparalelo)
        if (cambioparalelo.fecha == moment().format('YYYY-MM-DD')) {
            tc = cambioparalelo.tipoCambio;
        } else {
            tc = 0;
        }

        console.log("INICIO -------> CREACION DE DOCUMENTOS");
        GlobalConstants.tipeDoc = 'N';
        GlobalConstants.numitems = 0;
        console.log("reiniciado1");
        GlobalConstants.CabeceraDoc = [];
        GlobalConstants.DetalleDoc = [];
        GlobalConstants.auxiliarcloncabeceras = '';
        GlobalConstants.auxiliarclondetalle = '';
        GlobalConstants.Facturaruta = 0;

        console.log("poli ", poli);
        let optionsUser: any = [];
        // if (this.tipoVendedor == 0) {
        optionsUser.push({
            text: 'CREAR PEDIDO ',
            handler: () => {
                console.log('CREAR PEDIDO');
                if (tc == 0) {
                    return this.toast.show(`Tipo de cambio no encontrado.`, '10000', 'top').subscribe(toast => {
                    });
                } else {
                    this.cerrarpaginamapa();
                    this.createPedido(poli);

                }

            }
        });
        optionsUser.push({
            text: 'CREAR FACTURA',
            handler: () => {
                console.log('FACTURAR');
                if (tc == 0) {
                    return this.toast.show(`Tipo de cambio no encontrado.`, '10000', 'top').subscribe(toast => {
                    });
                } else {
                    this.cerrarpaginamapa();
                    this.createFactura(poli);
                }


            }
        });
        optionsUser.push({
            text: 'FAC. PENDIENTES PAGO',
            handler: () => {
                if (tc == 0) {
                    return this.toast.show(`Tipo de cambio no encontrado.`, '10000', 'top').subscribe(toast => {
                    });
                } else {
                    console.log('FACTURAS PENDIENTES');
                    this.cerrarpaginamapa();
                    this.pendientesDocumentsFac(poli);
                }


            }
        });

        // optionsUser.push({
        //     text: 'VISITA',
        //     handler: () => {
        //         console.log('REALIZAR VISITA');
        //         console.log(" this.array_marcadores ", this.array_marcadores);
        //         console.log("poli.posicion ", poli.posicion);
        //         this.createVisit(poli);
        //     }
        // });


        // }
        // else {
        optionsUser.push({
            text: 'PEDIDOS PENDIENTES',
            handler: () => {
                console.log('PEDIDOS PENDIENTES');
                if (tc == 0) {
                    return this.toast.show(`Tipo de cambio no encontrado.`, '10000', 'top').subscribe(toast => {
                    });
                } else {
                    GlobalConstants.Facturaruta = 1;
                    this.cerrarpaginamapa();
                    this.pendientesDocuments(poli);
                }


            }
        });

        optionsUser.push({
            text: 'SIN ENTREGA',
            handler: () => {
                console.log('REALIZAR VISITA');
                console.log(" this.array_marcadores ", this.array_marcadores);
                console.log("poli.posicion ", poli.posicion);
                if (tc == 0) {
                    return this.toast.show(`Tipo de cambio no encontrado.`, '10000', 'top').subscribe(toast => {
                    });
                } else {
                    this.cerrarpaginamapa();
                    this.createVisit(poli);
                }


            }
        });


        /*   {
               text: 'FACTURA',
               handler: () => {
                   console.log('FACTURAR');
                   this.createFactura(poli);
               }
           },
           */

        /* {
             text: 'FAC. PENDIENTES PAGO',
             handler: () => {
                 console.log('FACTURAS PENDIENTES');
                 this.pendientesDocumentsFac(poli);
             }
         },*/

        // }
        this.alertController.create({
            header: `${poli.cardcode}`,
            subHeader: `${poli.cardname}`,
            mode: 'ios',
            message: `Posición ${poli.posicion}`,
            buttons:
                optionsUser

        }).then(res => {
            res.present();
        });
    }

    createPedido = async (poli) => {
        console.log(poli);
        /// this.minPiker = documentosdata.getFechaPicker();
        await this.configService.setTipo("DOP");
        this.navCrl.navigateForward(`pedido/null/DOP/${poli.cardcode}`);

    }
    createFactura = async (poli) => {
        console.log(poli);

        await this.configService.setTipo("DFA");
        this.navCrl.navigateForward(`pedido/null/DFA/${poli.cardcode}`);
    }
    pendientesDocuments = (poli) => {
        localStorage.setItem('verImportadosMarker', 'SI');
        localStorage.setItem('cardCodeMarker', poli.cardcode);
        this.navCrl.navigateForward(`/pedidos/DOP`);

        console.log(poli);
    }
    pendientesDocumentsFac = async (poli) => {
        console.log(poli);
        //  this.navCrl.navigateForward(`detallepago/${poli.cardcode}`);
        let datos = {

            cardcode: poli.cardcode
        };
        localStorage.setItem('facturasPenMarker', 'SI');
        localStorage.setItem('cardCodeMarker', poli.cardcode);

        let modalObj: any = { component: DetallepagoPage, componentProps: datos };
        let modal: any = await this.modalController.create(modalObj);
        modal.onDidDismiss().then(async (data: any) => {
            console.log("return dismiss ", data.data);
            let usesMarkers: any = await this.configService.getActionMarker();
            let dataMarkersUse =
            {
                "dateUse": this.fechaData,
                "positionUse": poli.posicion,
                "condition": "pagofac"
            };
            console.log("dataMarkersUse ", dataMarkersUse);
            usesMarkers.push(dataMarkersUse);
            await this.configService.setActionMarker(usesMarkers);
            // this.clickMap();
            //this.accionFecha();
        });
        return await modal.present();

    }
    createVisit = async (poli) => {
        console.log(poli);
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
            CardCode: poli.cardcode,
            CardName: poli.cardname,
            lat: this.lat,
            lng: this.lng,
            foto: 'null.jpg',

        };
        let obj: any = { component: VisitasPage, componentProps: datospago };
        let modal: any = await this.modalController.create(obj);
        modal.onDidDismiss().then(async (data: any) => {
            console.log("return dismiss ", data.data);
            if (data.data) {
                console.log("eliminando marker ...");
                console.log("await this.configService.getActionMarker() ", await this.configService.getActionMarker());

                let usesMarkers: any = await this.configService.getActionMarker();
                let dataMarkersUse =
                {
                    "dateUse": this.fechaData,
                    "positionUse": poli.posicion,
                    "condition": "noVisit"
                };
                console.log("dataMarkersUse ", dataMarkersUse);
                usesMarkers.push(dataMarkersUse);
                await this.configService.setActionMarker(usesMarkers);
                console.log("await this.configService.getActionMarker() ", await this.configService.getActionMarker());


                this.ngOnInit();
            }else{
                this.ngOnInit();
            }

        });
        
        return await modal.present();
    }


    generarLetra() {
        var letras = ["a", "b", "c", "d", "e", "f", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
        var numero = (Math.random() * 15).toFixed(0);
        return letras[numero];
    }

    colorHEX() {
        var coolor = "";
        for (var i = 0; i < 6; i++) {
            coolor = coolor + this.generarLetra();
        }
        return "#" + coolor;
    }
    async dibujarRuteo(originLat, originLon, destinationLat, destinationLon) {
        console.log("generarLetra ", this.colorHEX());
        let color = this.colorHEX();
        //  let ubi = { lat: this.lat, lng: this.lng };
        //this.map.setCameraZoom(18);
        //  this.map.setCameraTarget(ubi);
        let request: any = {
            origin: { lat: parseFloat(originLat), lng: parseFloat(originLon) },
            destination: { lat: parseFloat(destinationLat), lng: parseFloat(destinationLon) },
            travelMode: google.maps.TravelMode.WALKING
        };
        // console.log("ruteo request ", request);
        let directionService = new google.maps.DirectionsService();
        directionService.route(request, async (response, status) => {
            console.log("status ", status);
            if (status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
                console.log('DEVD se sobre paso el limite ', status);

            }
            let arrxx = [];
            let rutageneral = response.routes[0].overview_path;
            //   console.log("ruta general ", rutageneral);
            if (rutageneral.length > 0) {
                for (let i = 0; i < rutageneral.length; i++)
                    arrxx.push(new LatLng(rutageneral[i].lat(), rutageneral[i].lng()));

                //console.log("arrxx ", arrxx);
                /* let myRoute = response.routes[0].legs[0];
                 var image = 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png';
                 for (let x = 0; x < myRoute.steps.length; x++) {
                     let ubi = { lat: myRoute.steps[x].start_point.lat(), lng: myRoute.steps[x].start_point.lng() };
                     this.map.addMarkerSync({
                         position: ubi,
                         icon: image
                     });
                 }
                 */
                try {

                    await this.map.addPolyline({
                        points: arrxx,
                        visible: true,
                        color: color,
                        geodesic: true,
                        width: 4
                    });
                    this.map.setCameraZoom(15);

                } catch (e) {
                    console.log("DEVD ocurrio un error al dibujar la ruta ", e, request);
                }
            }
        });
    }

    public async accionFecha() {
        // console.log("accionFecha() ", this.fechaData);
        if (!this.lat) {
            if (localStorage.getItem("lat") != "") {
                this.toast.show(`Última ubicación obtenida con éxito.`, '5000', 'top').subscribe(toast => {
                });
                this.lat = Number(localStorage.getItem("lat"));
                this.lng = Number(localStorage.getItem("lng"));
            } else {
                this.toast.show(`No encontramos tu geoposición, revisa tu red y configuración de GPS.`, '9000', 'center').subscribe(toast => {
                });

            }
        }
        this.fechaData = moment(this.fechaData).format('YYYY-MM-DD');
        console.log("fechaData ", this.fechaData);

        await this.posicion();
        await this.poligonoFunction();


        // this.poligonoFunction();
        //    this.ventas = [];
        ///     this.searchData = resp;
        //    this.buscar(null);
    }
    public async CoordenadasStorage() {
        this.checkGPSPermission();


        /*
                try {
                    return new Promise(async (resolve, reject) => {
                        let ubx: any = await this.configService.getUbicacion();
                        this.lat = ubx.lat;
                        this.lng = ubx.lng;
                        console.log("DEVD ubx ", ubx);
                        resolve(true);
                    });
                } catch (error) {
                    console.error("error cvalidar ");
                    this.toast.show(`No encontramos tu ubicación, revisa si la aplicación tiene permisos.`, '5000', 'top').subscribe(toast => {
                    });
                    this.navCrl.pop();
                }*/

    }
    //Check if application having GPS access permission  
    async checkGPSPermission() {
        /*this.platform.ready().then(async () => {



            //this.activeGPS();
            this.getLocation();



        });
        */

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
    askToTurnOnGPS() {

        this.locationAccuracy.request(this.locationAccuracy.REQUEST_PRIORITY_HIGH_ACCURACY).then(
            () => {
                // When GPS Turned ON call method to get Accurate location coordinates
                //this.getLocationCoordinates();
                this.getLocation();
            },
            error => {
                this.diagnostic.switchToLocationSettings();

            }
        );
    }

    public async presentAlertRadioDias() {
        this.spinnerDialog.show(null, null, true);
        this.map.clear();

        setTimeout(() => {
            this.spinnerDialog.hide();
        }, 2000);
         
        
       // console.log("semanas seleccionadas",this.diasSemana);
        const alert: any = await this.alertController.create({
            header: 'Seleccionar día',
            inputs: [...this.diasSemana],
            buttons: [
                {
                    text: 'Cancelar',
                    role: 'cancel',
                    handler: () => {
                        console.log('Confirm Cancel');
                    }
                }, {
                    text: 'Ok',
                    handler: async (data) => {
                        //  this.verdia(data);
                        console.log(data);
                        this.dataDia = data;
                        this.spinnerDialog.show(null, null, true);
                        this.diasSemana.forEach((value, index) =>{
                            console.log("data user",data, value);
                            if (data == value.value){ 
                                value.checked = true
                               // this.dataDia = data;
                            } else{ 
                                value.checked = false
                            } 
                        });
                        setTimeout(async () => {
                            this.spinnerDialog.hide();
                            await this.posicion();
                            await this.poligonoFunction();
                        }, 1000);


                    }
                }
            ]
        });
        await alert.present();
    }

    public async verdia(dia: any) {
        let poligono: any = await this.configService.getPoligono();
        console.log("verdia ", dia);
        console.log("poligono ", poligono);
        if (poligono.respuesta.poligono.length > 0 && poligono.respuesta.ruta.length > 0) {
            let arrxxpoli = [];
            let respmarker: any = poligono.respuesta.ruta.filter((pol) => pol.dia == dia);
            for (let poli of respmarker) {
                let ubi = { lat: parseFloat(poli.latitud), lng: parseFloat(poli.longitud) };
                arrxxpoli.push(new LatLng(parseFloat(poli.latitud), parseFloat(poli.longitud)));
                this.map.addMarkerSync({
                    title: poli.cardname + ' [' + poli.cardcode + ']',
                    position: ubi,
                });

            }
            let colorx = ['#f44336', '#d81b60', '#8e24aa', '#673ab7', '#2196f3', '#4caf50', '#cddc39'];
            try {
                this.isenabled = false;
                await this.map.addPolyline({
                    points: arrxxpoli,
                    visible: true,
                    color: colorx[(parseInt(dia) - 1)],
                    geodesic: true,
                    width: 2
                });
            } catch (e) {
                this.isenabled = false;
            }
        }
    }


    resetmap() {
        console.log("clear()");
        setTimeout(() => {
            this.map.clear();
        }, 2000);

    }
    public ngOnDestroy() {
        clearTimeout(this.timeOut);
    }

    public puntoClientes() {
        for (let obj of this.clientes) {
            let ubi: any = {
                lat: parseFloat(obj.Latitude),
                lng: parseFloat(obj.Longitude),
            };
            let marker: Marker = this.map.addMarkerSync({
                icon: 'assets/marker.png',
                position: ubi,
                disableAutoPan: true
            });
            marker.on(GoogleMapsEvent.MARKER_CLICK).subscribe(() => {
                this.navCrl.navigateForward(`cliente/` + obj.CardCode);
            });
        }
    }
    /*
        public async watchpos() {
            console.log(" watchpos() ");
            try {
                let ubi: any = await this.configService.getUbicacion();
                console.log("ubi ", ubi)
                this.marckauser.remove();
                this.marckauser = this.map.addMarkerSync({
                    position: ubi
                });
                this.timeOut = setTimeout(() => {
                    this.watchpos();
                }, 500);
            } catch (e) {
                this.timeOut = setTimeout(() => {
                    this.watchpos();
                }, 500);
            }
        }
    */
    public loadMap() {

        console.log("loadMap() ");
        return new Promise(async (resolve) => {
            let opciones = {
                zoom: false,
                myLocationButton: true
            };
            this.map = GoogleMaps.create("map_canvas", { controls: opciones });
            await this.map.one(GoogleMapsEvent.MAP_READY);
            resolve(true);
        }).then(() => {
            this.poligonoFunction();
        })
    }

    public async selectCliente() {
        let mcliente: any = { component: ModalclientePage };
        let modalcliente: any = await this.modalController.create(mcliente);
        modalcliente.onDidDismiss().then((data: any) => {
            if (data.data != false) {
                let infocli: any = data.data;
                this.marker(infocli.Latitude, infocli.Longitude);
            } else {
                this.toast.show(`Seleciona un cliente para continuar.`, '4000', 'top').subscribe(toast => {
                });
            }
        });
        return await modalcliente.present();
    }

    public async marker(lat, lng) {
        if ((typeof lat !== 'undefined' || lat == null) && lat != 0) {
            this.isenabled = true;
            let ubi = { lat: lat, lng: lng };
            this.map.setCameraZoom(18);
            this.map.setCameraTarget(ubi);
            let request: any = {
                origin: { lat: parseFloat(lat), lng: parseFloat(lng) },
                destination: { lat: parseFloat(this.lat), lng: parseFloat(this.lng) },
                travelMode: google.maps.TravelMode.WALKING
            };
            let directionService = new google.maps.DirectionsService();
            directionService.route(request, async (response, status) => {
                let arrxx = [];
                let rutageneral = response.routes[0].overview_path;
                if (rutageneral.length > 0) {
                    for (let i = 0; i < rutageneral.length; i++)
                        arrxx.push(new LatLng(rutageneral[i].lat(), rutageneral[i].lng()));
                    let myRoute = response.routes[0].legs[0];
                    var image = 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png';
                    for (let x = 0; x < myRoute.steps.length; x++) {
                        let ubi = { lat: myRoute.steps[x].start_point.lat(), lng: myRoute.steps[x].start_point.lng() };
                        this.map.addMarkerSync({
                            position: ubi,
                            icon: image
                        });
                    }
                    try {
                        this.isenabled = false;
                        await this.map.addPolyline({
                            points: arrxx,
                            visible: true,
                            color: '#ff2129',
                            geodesic: true,
                            width: 4
                        });
                        this.map.setCameraZoom(15);
                    } catch (e) {
                        this.isenabled = false;
                    }
                }
            });
        } else {
            this.toast.show(`El cliente seleccionado no tiene ubicación.`, '4000', 'top').subscribe(toast => {
            });
        }
    }
    /*
        public posicion() {
            console.log(" posicion()  ");
            this.map.getMyLocation().then((location: any) => {
    
                this.lat = location.latLng.lat;
                this.lng = location.latLng.lng;
                console.log("this.lat  ", this.lat);
                console.log("this.lng  ", this.lng);
                this.marckauser = this.map.addMarkerSync({
                    position: location.latLng
                });
                this.map.setCameraZoom(17);
                this.map.setCameraTarget(location.latLng);
                this.watchpos();
            });
        }
    */
    public async localizar() {
        this.marckauser.remove();
    }

    public async agregarclientes() {
        let data: any = await this.clienteData.selectClientesPos();
        for (let itm = 0; itm < data.length; itm++) {
            this.items.push(data[itm]);
            this.cargarRuta(data[itm]);
            this.ruta.push({ lat: data[itm]["Latitude"], lng: data[itm]["Longitude"] });
        }
        this.generarLinea();
    }

    public cargarRuta($objeto) {
        let htmlInfoWindow = new HtmlInfoWindow();
        var btn1 = document.createElement("Button");
        var txtbtn1 = document.createTextNode("Detalle Cliente");
        btn1.appendChild(txtbtn1);
        btn1.className = 'myButton';
        btn1.addEventListener("click", (event) => {
            this.navCrl.navigateForward(`cliente/` + $objeto["CardCode"]);
        });
        var btn2 = document.createElement("Button");
        var txtbtn2 = document.createTextNode("Pagos Cliente");
        btn2.appendChild(txtbtn2);
        btn2.className = 'myButton';
        btn2.addEventListener("click", (event) => {
            this.navCrl.navigateForward(`pagos`);
        });
        var btn3 = document.createElement("Button");
        var txtbtn3 = document.createTextNode("Deuda Cliente");
        btn3.appendChild(txtbtn3);
        btn3.className = 'myButton';
        btn3.addEventListener("click", (event) => {
            this.navCrl.navigateForward(`pendientes/` + $objeto["CardCode"]);
        });
        var btn4 = document.createElement("Button");
        var txtbtn4 = document.createTextNode("Pedido Cliente");
        btn4.appendChild(txtbtn4);
        btn4.className = 'myButton';
        btn4.addEventListener("click", (event) => {
            this.navCrl.navigateForward(`pedido/0/DOP/` + $objeto["CardCode"]);
        });
        let frame: HTMLElement = document.createElement('div');
        frame.innerHTML = '<div class="flip-container" id="flip-container">';
        frame.innerHTML += '<div class="flipper">';
        frame.innerHTML += '<div class="front">';
        frame.innerHTML += '<h4>' + $objeto["CardCode"] + '</h4>';
        frame.innerHTML += '<h5>' + $objeto["CardName"] + '</h5>';
        frame.innerHTML += '</div>';
        frame.innerHTML += '<div class="back">';
        frame.innerHTML += ' <strong>Direccion: </strong>' + $objeto["Address"] + '<br/>';
        frame.innerHTML += ' <strong>NIT: </strong>' + $objeto["FederalTaxId"] + '<br/>';
        frame.innerHTML += ' <strong>Razon Social: </strong>' + $objeto["razonsocial"] + '<br/>';
        frame.innerHTML += ' <strong>Fono: </strong>' + $objeto["PhoneNumber"] + '<br/>';
        frame.innerHTML += '</div>';
        frame.innerHTML += '</div>';
        frame.innerHTML += '</div>';
        frame.appendChild(btn1);
        frame.appendChild(btn2);
        frame.appendChild(btn3);
        //frame.appendChild(btn4);
        frame.addEventListener("click", (evt) => {
            let container = document.getElementById('flip-container');
            if (container.className.indexOf(' hover') > -1) {
                container.className = container.className.replace(" hover", "");
            } else {
                container.className += " hover";
            }
        });
        htmlInfoWindow.setContent(frame, {
            width: "300px"
        });
        let marker: Marker = this.map.addMarkerSync({
            icon: 'blue',
            animation: 'DROP',
            position: {
                lat: $objeto["Latitude"],
                lng: $objeto["Longitude"]
            },
            disableAutoPan: true
        });
    }

    public generarLinea() {
        this.poliline = this.map.addPolylineSync({
            points: this.ruta,
            color: '#AA00FF',
            width: 5,
        });
    }

    public posicionar($xcliente) {
        let nps: ILatLng = {
            lat: $xcliente["Latitude"],
            lng: $xcliente["Longitude"]
        };
        this.map.setCameraTarget(nps);
    }

    public async searchInput(event) {
        this.searchData = event.detail.value;
        this.poliline.remove();
        this.items = [];
        this.ruta = [];
        this.map.clear().then(() => {
        });
        if (this.searchData == "") {
            this.agregarclientes();
        } else {
            let data: any = await this.clienteData.selectBuscarClientesPos(this.searchData);
            for (let itm = 0; itm < data.length; itm++) {
                this.items.push(data[itm]);
                this.cargarRuta(data[itm]);
                this.ruta.push({ lat: data[itm]["Latitude"], lng: data[itm]["Longitude"] });
            }
            this.generarLinea();
        }
    }
    //Check if application having GPS access permission  
    async posicion() {
        console.log("DEVD posicion()");
        //{ timeout: 200000, enableHighAccuracy: false }
        //  this.geolocation.getCurrentPosition().then((position: Geoposition) => {
        //     console.log("DEVD posicion", position);
        //    this.lat = position.coords.latitude;
        //    this.lng = position.coords.longitude;
        /* if (!this.lat) {
 
             return await this.checkGPSPermission();
         }
         */
        console.log("this.lat  ", this.lat);
        console.log("this.lng  ", this.lng);


        // this.marckauser = this.map.addMarkerSync({
        //    position:  
        //     });
        let ubi = { lat: parseFloat(this.lat), lng: parseFloat(this.lng) };

        this.addMarker(this.map, this.lat, this.lng, "Estas aqui", "", 1);
        // this.map.addMarker(markerOption);
        /// marker.showInfoWindow();
        this.map.setCameraZoom(12);
        this.map.setCameraTarget(ubi);

    }


    addMarker(map, latitude, longitude, title, comentario, cardCode) {
        // create LatLng object
        let ionic: LatLng = new LatLng(latitude, longitude);

        // create new marker
        let markerOptions: MarkerOptions = {
            position: ionic,
            title: title,
            //snippet: "" + comentario,
            icon: {
                'url': "./assets/marker.png", 'size': {
                    wcardCodeth: 50,
                    height: 60
                }
            },
        };

        this.marker = map.addMarker(markerOptions)
            .then((marker: Marker) => {
                marker.showInfoWindow();
                marker.setAnimation('DROP');
                marker.addEventListener(GoogleMapsEvent.INFO_CLICK).subscribe(
                    (data) => {
                        console.log("event position");
                        //   this.showDetails(id);
                    }
                );
            });
    }

    // Methos to get device accurate coordinates using device GPS
    async getLocationCoordinates() {
        this.getLocation();
        /*
        this.spinnerDialog.show(null, null, true);

        setInterval(() => {
            this.spinnerDialog.hide();
        }, 3000);
        console.log("etLocationCoordinates() ");
        this.geolocation.getCurrentPosition().then((resp) => {
            this.getLocation();
        }).catch(async (error) => {
            //alert('Error getting location' + error);
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
        */
    }

    /**
 * geolocation
 */
    /**
       * geolocation
       */

    /*getLocation() {
        console.log("geolocation ");

        this.platform.ready().then(() => {
            if (this.platform.is('android')) {
                //alert ("es android");
                this.androidPermissions.checkPermission(this.androidPermissions.PERMISSION.ACCESS_FINE_LOCATION).then(
                    result => console.log('Has permission?', result.hasPermission),
                    err => this.androidPermissions.requestPermission(this.androidPermissions.PERMISSION.ACCESS_FINE_LOCATION)
                );
                console.log("es ANDROID Y TIENE PERMISOS");
                this.grantRequest();
            } else {
                console.log("no es ANDROID");
                this.geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 10000 }).then((resp) => {
                    if (resp) {
                        console.log('geolocalizacion else', resp);

                    }
                }).catch(error => {
                    // alert("erro geo");
                    console.log("error geolocation ", error);
                    // this.grantRequest();
                });
            }
        });
    }*/

    getLocation() {
        console.log("geolocation ");

        this.platform.ready().then(() => {
            if (this.platform.is('android')) {
                //alert ("es android");
                this.androidPermissions.checkPermission(this.androidPermissions.PERMISSION.ACCESS_FINE_LOCATION).then(
                    result => {
                        if (result.hasPermission) {
                            console.log("es ANDROID Y TIENE PERMISOS");

                            this.grantRequest();
                        } else {
                            console.log("NO tiene permiso");
                            this.androidPermissions.requestPermission(this.androidPermissions.PERMISSION.ACCESS_FINE_LOCATION);
                        }
                    },
                    err => this.androidPermissions.requestPermission(this.androidPermissions.PERMISSION.ACCESS_FINE_LOCATION)
                );

            } else {
                console.log("no es ANDROID");
                this.geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 10000 }).then((resp) => {
                    if (resp) {
                        console.log('geolocalizacion else', resp);

                    }
                }).catch(error => {
                    // alert("erro geo");
                    console.log("error geolocation ", error);
                    // this.grantRequest();
                });
            }
        });
    }


    grantRequest() {
        console.log(" grantRequest()");
        this.spinnerDialog.show(null, null, true);
        /*    this.diagnostic.isLocationEnabled().then((data) => {
                if (data) {
                    */
        this.geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 20000 }).then((resp) => {
            //   this.geolocation.getCurrentPosition({ maximumAge: 3000, timeout: 10000, enableHighAccuracy: false }).then((resp) => {
            if (resp) {
                this.spinnerDialog.hide();
                console.log('resp GEOPOSITION SIN PROBLEMAS', resp);
                this.lat = resp.coords.latitude;
                this.lng = resp.coords.longitude;
                // this.loadmap(resp.coords.latitude, resp.coords.longitude, this.mapEle);

                console.log("this.lat  ", this.lat);
                console.log("this.lng  ", this.lng);
                // this.marckauser = this.map.addMarkerSync({
                //    position:  
                //     });
                let ubi = { lat: parseFloat(this.lat), lng: parseFloat(this.lng) };
                this.map.addMarkerSync({

                    position: ubi,
                });

                this.map.setCameraZoom(17);
                this.map.setCameraTarget(ubi);
            } else {
                this.spinnerDialog.hide();

            }
        }).catch(error => {
            console.log(error);
            alert("error" + JSON.stringify(error));
        });
        /*    } else {
            this.spinnerDialog.hide();

            this.diagnostic.switchToLocationSettings();
            this.geolocation.getCurrentPosition({ maximumAge: 3000, timeout: 30000, enableHighAccuracy: false }).then((resp) => {
                if (resp) {
                    // alert('current');
                    // alert('ress'+resp.coords.latitude);
                    this.getLocation();
                }
            }).catch(error => {
                // alert(JSON.stringify(error));
                console.log(error);
            });
        }
}, error => {
        console.log('errir', error);
        this.spinnerDialog.hide();


    }).catch(error => {
        console.log('error', error);
        this.spinnerDialog.hide();

    });
    */

    }



    /**
     * 
     * geocoder
     */
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
                // this.direccion = results[0].formatted_address;
                //this.lat = lat;
                //this.lng = lng;
            });
        } catch (error) {
            console.error("ocurrió un error al obtener la direccion en testo");
            this.spinnerDialog.hide();
        }
        // alert("addres " + addres);
    }

    public async showDatesRoutes() {
        console.log("this.poligono.respuesta. ", this.poligono.respuesta);

        // let sms = "";
        // for await (let item of auxBoni) {
        //     sms = sms + "<strong> - " + item.cabezera_tipo + " : </strong> " + item.nombre + " <br/> ";
        // }
        const alert = await this.alertController.create({
            cssClass: "my-custom-class",
            header: "Rutas encontradas",
            message: "",
            buttons: [

                {
                    text: "Aceptar",
                    handler: () => {
                        console.log("Confirm Okay");

                    },
                },
            ],
        });
        await alert.present();
    }
    public cerrarpaginamapa(){
        this.map.clear;
        this.map.off;
        this.map.setVisible(false);
        this.map.setDiv(null);
        this.iniciador = 1;
    }
    ionViewWillLeave(){
        this.cerrarpaginamapa();
    }
    ionViewDidEnter(){
        if(this.iniciador == 1){
            this.ngOnInit();
        }
    }
}