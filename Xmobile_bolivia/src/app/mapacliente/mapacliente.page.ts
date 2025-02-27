import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from "@angular/router";
import { ConfigService } from "../models/config.service";
import { Clientes } from "../models/clientes";
import { SpinnerDialog } from "@ionic-native/spinner-dialog/ngx";
import { Dialogs } from "@ionic-native/dialogs/ngx";
import { Toast } from "@ionic-native/toast/ngx";
import {
    GoogleMaps,
    GoogleMap,
    ILatLng,
    GoogleMapsEvent,
    GoogleMapOptions,
    CameraPosition,
    MarkerOptions,
    Marker,
    Environment
} from '@ionic-native/google-maps';
import { Clientessucursales } from '../models/clientessucursales';
import { AlertController } from '@ionic/angular';
import { DataService } from '../services/data.service';

declare var google;

@Component({
    selector: 'app-mapacliente',
    templateUrl: './mapacliente.page.html',
    styleUrls: ['./mapacliente.page.scss'],
})
export class MapaclientePage implements OnInit {

    public cccliente: string;
    public idUser: any;
    public data: any;
    public direccion: any;
    public map: GoogleMap;
    public latitude: any;
    public longitude: any;
    public lat: any;
    public log: any;
    public msg: any;
    public errorgps: boolean;
    public marker: Marker;
    public mode: string;


    constructor(private activatedRoute: ActivatedRoute, private spinnerDialog: SpinnerDialog, private dataService: DataService,
        public alertController: AlertController,
        private dialogs: Dialogs, private configService: ConfigService, private toast: Toast) {
        this.latitude = 0;
        this.longitude = 0;
        this.errorgps = false;
        this.lat = 0;
        this.log = 0;
    }

    public async ngOnInit() {
        console.log("DEVD ngOnInit()");

        this.cccliente = this.activatedRoute.snapshot.paramMap.get('id');
        this.mode = this.activatedRoute.snapshot.paramMap.get('mode');
        console.log(" this.cccliente   nav params ", this.cccliente, this.mode);
        console.log("DEVD this.mode ", this.mode);
        if (this.mode == "cliente") {
            this.toast.show('Presiona el icono de la parte superior derecha para mover la ubicación.',
                '5000', 'bottom').subscribe(toast => {
                });
            let id: any = await this.configService.getSession();
            this.idUser = id.idUsuario;
            let clientes = new Clientes();
            this.data = await clientes.selectCarCode(this.cccliente);
            this.msg = [
                "Cliente:" + this.data["CardCode"] + "\n",
                "Nombre:" + this.data["CardName"] + "\n",
                "Dirección:" + this.data["Address"] + "\n",
                "NIT:" + this.data["FederalTaxId"] + "\n",
                "RZN :" + this.data["razonsocial"] + "\n",
                "Fono:" + this.data["PhoneNumber"],
            ].join("\n");
            console.log("objeto del cliente ", this.msg);
            if (this.data.Latitude == 0 || this.data.Latitude == 'null' || this.data.Latitude == null) {
                console.log("hay direccion ");
                this.errorgps = true;
                setTimeout(() => {
                    this.errorgps = false;
                }, 7000);
                await this.Coordenadas();
            } else {
                console.log("no hay direccion ");

                this.lat = this.data.Latitude;
                this.log = this.data.Longitude;
                this.latitude = this.data.Latitude;
                this.longitude = this.data.Longitude;
            }
            this.loadMap();

        } else {
            //await this.Coordenadas();
            console.log("id cliente uindefined load new map");
            let clientessucursales = new Clientessucursales();
            let dataSucursal = await clientessucursales.findOne(this.cccliente);
            console.log("data encontrado de la sucursal ", dataSucursal);
            /**
             * -33.45694, -70.64827
             */
            if (dataSucursal[0].u_lat !== "undefined" && dataSucursal[0].u_lat !== "0") {
                console.log("si hay lat ");
                this.lat = Number(dataSucursal[0].u_lat);
                this.log = Number(dataSucursal[0].u_lon);
                this.latitude = Number(dataSucursal[0].u_lat);
                this.longitude = Number(dataSucursal[0].u_lon);
                this.direccion = dataSucursal[0].Street;
            } else {
                console.log("no hay lat ");
                this.toast.show('no se encontró una direccion registrada.', '3000', 'bottom').subscribe(toast => {
                });
                this.latitude = Number(this.lat);
                this.longitude = Number(this.log);
            }
            this.loadMap();
        }
    }

    public Coordenadas() {
        console.log("Coordenadas()");
        return new Promise(async (resolve, reject) => {
            let ubx: any = await this.configService.getUbicacion();
            this.lat = ubx.lat;
            this.log = ubx.lng;
            console.log("DEVD ubx ", ubx);
            resolve(true);
        });
    }

    public loadMap() {
        console.log("lo9ad map");
        let mapOptions: GoogleMapOptions = {
            camera: {
                target: {
                    lat: this.lat,
                    lng: this.log
                },
                zoom: 15,
                tilt: 30
            }
        };
        this.map = GoogleMaps.create('map_canvas', mapOptions);
        this.addMarker();
    }

    public disEnb() {
        this.marker.setDraggable(true);
        this.toast.show('Activado para ser movido el marcador.', '3000', 'bottom').subscribe(toast => {
        });
    }

    public addMarker() {
        this.marker = this.map.addMarkerSync({
            title: this.msg,
            icon: 'blue',
            animation: 'DROP',
            position: {
                lat: this.lat,
                lng: this.log
            }
        });
        this.marker.on(GoogleMapsEvent.MARKER_DRAG_END).subscribe((data: any) => {
            console.log("event drag ", data);
            this.latitude = data[0].lat;
            this.longitude = data[0].lng;
            if (this.mode == "cliente") {
                console.log("entro para aqui para actulaizar la ubicacion del cliente mas 1-->" + this.mode)
                this.updateUbicacion();
            } else {
                console.log("entro para aqui actualizar pago mode 2-->" + this.mode)

                this.updateUbicacionSucursal();
            }
        });
        this.marker.setDraggable(true);
    }

    public geocodeLatLng(latlng) {
        console.log("init latlng ");
        return new Promise((resolve, reject) => {
            try {

                let geocoder = new google.maps.Geocoder;
                geocoder.geocode({ 'location': latlng }, (results, status) => {
                    console.log("results ", results);
                    (status === google.maps.GeocoderStatus.OK) ? resolve(results[1].formatted_address) : resolve('Sin resultado');
                });
            } catch (e) {
                reject(e);
                console.log("error geocode ", e);
            }
        });

        /*
         return new Promise((resolve, reject) => {
             let geocoder = new google.maps.Geocoder();
             geocoder.geocode({'location': latlng}, (results, status) => {
                 console.log("results ", results, status);
                 (status === google.maps.GeocoderStatus.OK) ? resolve(results[1].formatted_address) : resolve('Sin resultado');
             });
         });
         */

    }

    public async updateUbicacion() {
        let latlng = {
            lat: this.latitude,
            lng: this.longitude
        };
        this.spinnerDialog.show(null, 'Loading...', true);
        //this.direccion = await this.geocodeLatLng(latlng);
        this.spinnerDialog.hide();
        this.dialogs.prompt('', 'Actualizar dirección', ['SI', 'NO'], this.direccion)
            .then(async (data) => {
                if (data.buttonIndex == 1) {
                    try {
                        let clientx = new Clientes();
                        await clientx.updateLocate(this.latitude, this.longitude, data.input1, this.cccliente);



                        this.toast.show('La ubicación se actualizo correctamente.', '3000', 'bottom').subscribe(toast => {
                        });
                        this.data = await clientx.selectCarCode(this.cccliente);
                        this.msg = [
                            "Cliente:" + this.data["CardCode"] + "\n",
                            "Nombre:" + this.data["CardName"] + "\n",
                            "Direccion:" + this.data["Address"] + "\n",
                            "Nit:" + this.data["FederalTaxId"] + "\n",
                            "RZN :" + this.data["razonsocial"] + "\n",
                            "Fono:" + this.data["PhoneNumber"],
                        ].join("\n");
                    } catch (e) {
                        alert("error en la actuializacion del cliente" + JSON.stringify(e))
                        this.toast.show('Error al actualizar la ubicación del cliente.', '3000', 'bottom').subscribe(toast => {
                        });
                    }
                }
            }).catch((e: any) => {
                this.toast.show('¡Error: Verifica tu GPS', '3000', 'bottom').subscribe(toast => {
                });
            });
    }


    public async updateUbicacionSucursal() {
        console.log("updateUbicacionSucursal () ");

        let latlng = {
            lat: this.latitude,
            lng: this.longitude
        };
        // this.spinnerDialog.show(null, 'Loading...', true);
        //  this.direccion = await this.geocodeLatLng(latlng);
        //  this.spinnerDialog.hide();

        const alert = await this.alertController.create({
            cssClass: 'my-custom-class',
            header: 'Actualizar dirección',
            backdropDismiss: false,
            inputs: [
                {
                    name: 'direccion',
                    type: 'text',
                    label: 'Dirección',
                    // max: '60',
                    value: this.direccion,
                    placeholder: '(Requerido)',
                    // cssClass: 'specialClass',
                    attributes: {
                        maxlength: 40,
                        // inputmode: 'decimal'
                    }
                }
            ],
            buttons: [
                {
                    text: 'Cancel',
                    role: 'cancel',
                    cssClass: 'secondary',
                    handler: (data) => {
                        console.log('Confirm Cancel');
                    }
                }, {
                    text: 'Ok',
                    handler: (data) => {
                        console.log('Confirm Ok', data);
                        if (data.direccion.trim() == "") {
                            this.toast.show('Completa el campo.', '3000', 'bottom').subscribe(toast => {
                            });
                            return false;
                        } else {
                            this.updateLocate(data);
                        }


                    }
                }
            ]
        });
        await alert.present();

        // this.dialogs.prompt('', 'Actualizar dirección', ['SI', 'NO'], this.direccion)
        //     .then(async (data) => {
        //         console.log("data.input1.trim()  ", data.input1.trim())

        //         console.log("data.input1 ", data.input1)

        //         }
        //     }).catch((e: any) => {
        //         alert("error en la ubicacion" + JSON.stringify(e));
        //         this.toast.show('¡Error: Verifica tu GPS', '3000', 'bottom').subscribe(toast => {
        //         });
        //     });
    }
    updateLocate = async (data) => {

        try {


            let clientessucursales = new Clientessucursales();
            let dataSucursal = await clientessucursales.findOne(this.cccliente);
            await clientessucursales.updateLocate(this.latitude, this.longitude, data.direccion, this.cccliente);
            let clientx = new Clientes();
            await clientx.updateLocate(this.latitude, this.longitude, data.input1, dataSucursal[0].CardCode);



            this.toast.show('La ubicación se actualizó correctamente.', '3000', 'bottom').subscribe(toast => {
            });
            try {
                await this.dataService.exporcliensync();
            } catch (error) {
                this.toast.show('No se pudo exportar la información, sincronice con una conexión a internet .', '7000', 'center').subscribe(toast => {
                });
            }



        } catch (e) {
            //  alert("error en la ubicacion mostra 1 " + JSON.stringify(e));
            this.toast.show('Error al actualizar la ubicación del cliente.', '3000', 'bottom').subscribe(toast => {
            });
        }

    }
    /**
     * asignar ubicacion default 
     */
    async ubicacionDefault() {


        const alert = await this.alertController.create({
            cssClass: "my-custom-class",
            header: "Mover mapa",
            message: "Se moverá el mapa a una direccion por defecto (La paz) <strong></strong>...",
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
                        console.log("Confirm Okay");
                        this.lat = -16.4897;
                        this.log = -68.1193;
                        this.latitude = this.lat;
                        this.longitude = this.log
                        this.marker.remove();
                        this.map.remove();
                        //  let ubi: any = await this.configService.getUbicacion();
                        //  console.log("ubi ", ubi);
                        /*
                        this.marker = this.map.addMarkerSync({
                            title: this.msg,
                            icon: 'blue',
                            animation: 'DROP',
                            position: {
                                lat: this.lat,
                                lng: this.log
                            }
                        });
*/



                        this.loadMap();
                        this.toast.show('Presiona fijamente el markador azul para mover y actualizar la dirección', '6000', 'center').subscribe(toast => {
                        });
                        //  this.updateUbicacionSucursal();


                    },
                },
            ],
        });

        await alert.present();


    }
    public cierramapa(){
        this.map.clear;
        this.map.off;
        this.map.setVisible(false);
        this.map.setDiv(null);
    }
    /*ionViewWillLeave(){
        this.cerrarpaginamapa();
    }
    ionViewDidEnter(){
        if(this.iniciador == 1){
            this.ngOnInit();
        }
    }*/

}
