import { Component, OnInit } from '@angular/core';
import { MenuController, Platform } from '@ionic/angular';
import { SplashScreen } from '@ionic-native/splash-screen/ngx';
import { StatusBar } from '@ionic-native/status-bar/ngx';
import { DataService } from "./services/data.service";
import { ConfigService } from "./models/config.service";
import { Vibration } from "@ionic-native/vibration/ngx";
import { LocationAccuracy } from "@ionic-native/location-accuracy/ngx";
import { Network } from "@ionic-native/network/ngx";
import { Toast } from "@ionic-native/toast/ngx";
import {
    BackgroundGeolocation,
    BackgroundGeolocationConfig,
    BackgroundGeolocationEvents,
    BackgroundGeolocationResponse
} from '@ionic-native/background-geolocation/ngx';
import { Tiempo } from "./models/tiempo";
import { Geolocalizacion } from "./models/geolocalizacion";
import { AndroidPermissions } from '@ionic-native/android-permissions/ngx';
import { Diagnostic } from '@ionic-native/diagnostic/ngx';
import { Geolocation } from '@ionic-native/geolocation/ngx';
import { Router } from '@angular/router';

@Component({
    selector: 'app-root',
    templateUrl: 'app.component.html',
    styleUrls: ['app.component.scss']
})
export class AppComponent implements OnInit {
    public selectedIndex = 0;
    public appPages: any;
    public labels = [];
    public xp: boolean;

    constructor(private platform: Platform, private splashScreen: SplashScreen, private dataService: DataService,
        private locationAccuracy: LocationAccuracy, private statusBar: StatusBar, private backgroundGeolocation: BackgroundGeolocation,
        private menuCtrl: MenuController, private vibration: Vibration, private configService: ConfigService,
        public geolocation: Geolocation,
        private diagnostic: Diagnostic, private androidPermissions: AndroidPermissions,
        private network: Network, public toast: Toast, private router: Router
    
    )
    {
        this.appPages = [];
        this.initializeApp();
    }

    initializeApp() {
        this.platform.ready().then(async () => {
            localStorage.setItem("lat", "");
            localStorage.setItem("lng", "");
            this.platform.backButton.subscribeWithPriority(9999, async () => {
                this.toast.show("Use la navegación de la aplicación", "4000", "bottom").subscribe(e => e);

                document.addEventListener('backbutton', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    console.log('cancel DEVD');
                }, false);
            });
            /* this.platform.backButton.subscribeWithPriority(0, () => {
                 this.toast.show("Esta acción esta desabilitada, utilice la navegación de la aplicación", "4000", "bottom").subscribe(e => e);
                 document.addEventListener('backbutton', function (event) {
                     event.preventDefault();
                     event.stopPropagation();
                 }, false);
 
             });*/
            this.splashScreen.hide();
            await this.statusBar.overlaysWebView(false);
            await this.statusBar.backgroundColorByHexString('#06092b');
            this.appPages = await this.dataService.menu();

            console.log(" DEVD  this.appPages  ", this.appPages);
            //this.activeGPS();
            this.getLocation();
            this.conexion();

           // this.watchpos();


        });
    }
    public async closeSesion(){
        this.router.navigateByUrl("");
    }

    public async activeGPS() {
        console.log("activeGPS() ");
        try {
            let canRequest: boolean = await this.locationAccuracy.canRequest();
            console.log("canRequest ", canRequest);
            if (canRequest) {
                this.locationAccuracy.request(this.locationAccuracy.REQUEST_PRIORITY_HIGH_ACCURACY).then(() => {
                    console.log("this.locationAccuracy success ");
                  //  this.watchpos();
                }, (error) => {
                    this.activeGPS();
                });
            } else {
                console.log("this.locationAccuracy err ");
                //this.watchpos();
            }
        } catch (e) {
            console.log(e);
        }
    }

    public async watchpos() {
        console.log("watchpos()");
        const config: BackgroundGeolocationConfig = {
            desiredAccuracy: 10,
            stationaryRadius: 1,
            distanceFilter: 1,
            debug: false,
            stopOnTerminate: false,
            locationProvider: 1,
            startForeground: true,
            interval: 14000,
            fastestInterval: 1000,
            activitiesInterval: 1000,
            notificationsEnabled: false
        };
        let resp: any = await this.backgroundGeolocation.configure(config);
        console.log("resp background location ", resp);
        if (resp == 'OK') {
            this.backgroundGeolocation.on(BackgroundGeolocationEvents.location).subscribe((location: BackgroundGeolocationResponse) => {
                console.log("get position bakground ", location);
                let obj: any = { lat: location.latitude, lng: location.longitude };
                this.guardaUltimaUbicacion(obj);
            });
            this.backgroundGeolocation.watchLocationMode();
            this.backgroundGeolocation.start();
        } else {

        }
    }

    public async guardaUltimaUbicacion(ubi: any) {
        console.log("guardaUltimaUbicacion()");
        await this.configService.setUbicacion(ubi);
        this.registerLocation(ubi)
    }

    public async registerLocation(ubi = null) {
        console.log("registerLocation() ", "" + ubi);
        localStorage.setItem("lat", ubi.lat);
        localStorage.setItem("lng", ubi.lng);
        if (this.xp == false) {
            let resp: any = await this.configService.getSession();
            let ubx: any = {
                "idequipox": resp[0].uuid,
                "latitud": ubi.lat,
                "longitud": ubi.lng,
                "fecha": Tiempo.fecha(),
                "hora": Tiempo.hora(),
                "idcliente": 0,
                "documentocod": "",
                "tipodoc": "",
                "estado": 1,
                "actividad": 1,
                "anexo": "",
                "usuario": resp[0].idUsuario,
                "status": 1,
                "dateUpdate": Tiempo.fecha() + ' ' + Tiempo.hora()
            };
            let geomodel = new Geolocalizacion();
            await geomodel.insert(ubx);
            this.xp = true;
        } else {
            this.xp = false;
        }
    }

    public conexion() {
        this.network.onDisconnect().subscribe(() => {
            this.toast.show(`Verifica tu conexión de red.`, '3000', 'top').subscribe(toast => {
            });
            this.vibration.vibrate(1000);
        });
        this.network.onConnect().subscribe(async () => {
            // try {
            //     await this.dataService.exporcliensync();
            //     await this.dataService.exportDocumentosAsinc();
            // } catch (e) {
            //     this.toast.show(`Existen problemas con la conexión a los servicios conéctate con el administrador. `, '4000', 'top').subscribe(toast => {
            //     });
            // }
        });
    }

    eventRoute() {

        console.log("eventRoute() ELIMINANDO verImportadosMarker ")
        localStorage.removeItem('verImportadosMarker');
        localStorage.removeItem('facturasPenMarker');
        localStorage.removeItem('cardCodeMarker'); // 1004100009
        //alert("sadad")


    }
    ngOnInit() {
        const path = window.location.pathname.split('folder/')[1];
        if (path !== undefined) {
            this.selectedIndex = this.appPages.findIndex(page => page.title.toLowerCase() === path.toLowerCase());
        }
    }

    public async exit() {
        navigator['app'].exitApp();
    }


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
                            console.log("No tiene permiso");
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
        /// this.diagnostic.isLocationEnabled().then((data) => {

        this.geolocation.getCurrentPosition({ maximumAge: 3000, timeout: 10000, enableHighAccuracy: false }).then((resp) => {
            if (resp) {
                console.log('resp GEOPOSITION SIN PROBLEMAS', resp);
                let obj: any = { lat: resp.coords.latitude, lng: resp.coords.longitude };
                this.guardaUltimaUbicacion(obj);

            }
        }).catch(error => {
            console.log("error 1 ", error);


            // alert("error"+JSON.stringify(error));
        });

        // if (true) {
        this.geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 10000 }).then((resp) => {
            if (resp) {
                console.log("grantRequest lat long " + JSON.stringify(resp.coords.longitude));

                console.log(" al sacar latitud sacó ", resp.coords.longitude);

                let obj: any = { lat: resp.coords.latitude, lng: resp.coords.longitude };
                this.guardaUltimaUbicacion(obj);

                //   this.diagnostic.switchToLocationSettings();



                console.log("llamar a bacground");
                this.activeGPS();

            }
        }).catch(error => {
            console.log("error 2", error);


            // alert("error"+JSON.stringify(error));
        });

        // }
        // } else {
        //     this.diagnostic.switchToLocationSettings();
        //     this.geolocation.getCurrentPosition({ maximumAge: 3000, timeout: 30000, enableHighAccuracy: false }).then((resp) => {
        //         if (resp) {
        //             // alert('current');
        //             // alert('ress'+resp.coords.latitude);
        //             this.getLocation();
        //         }
        //     }).catch(error => {
        //         // alert(JSON.stringify(error));
        //         console.log(error);
        //     });
        // }
        // }, error => {
        //     console.log('errir', error);

        // }).catch(error => {
        //     console.log('error', error);
        // });

    }


}
