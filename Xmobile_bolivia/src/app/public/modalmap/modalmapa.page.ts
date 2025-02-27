import { Component, OnInit } from "@angular/core";

import {
  GoogleMaps,
  GoogleMap,
  GoogleMapsEvent,
  Marker,
  GoogleMapOptions,
  GoogleMapsAnimation,
  MyLocation
} from "@ionic-native/google-maps";
import { AndroidPermissions } from '@ionic-native/android-permissions/ngx';
import { LocationAccuracy } from '@ionic-native/location-accuracy/ngx';
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { Geolocation, GeolocationOptions, Geoposition, PositionError } from '@ionic-native/geolocation/ngx';
import { AlertController,Platform,NavController, LoadingController, ToastController,ModalController,NavParams } from "@ionic/angular";
import { Diagnostic } from '@ionic-native/diagnostic/ngx';

@Component({
  selector: 'app-modalmapa',
  templateUrl:'./modalmapa.page.html',
  styleUrls: ['./modalmapa.page.scss'],
})

  export class ModalMapaPage implements OnInit {
    map: GoogleMap;loading: any;
    public lat: any;
    public lng: any;
    public datos:any;
    public item: any;
   

    constructor(
      private navCrl: NavController,
      public modalController: ModalController,
      private diagnostic: Diagnostic,
      public loadingCtrl: LoadingController,
      public toastCtrl: ToastController,
      private platform: Platform,
      public geolocation: Geolocation,
      private androidPermissions: AndroidPermissions,
      private locationAccuracy: LocationAccuracy,
      private spinnerDialog: SpinnerDialog,
      public alertController: AlertController,
      public navParams: NavParams
    ) {
      this.item = navParams.data;
      console.log(" this.item ", this.item);
      this.datos = {
        "lat": this.item.lat,
        "lng": this.item.lng,
        "modo": this.item.modo
      };
    }

    

    async ngOnInit() {
      await this.platform.ready();
      await this.GPSPromise();
      await this.loadMap();
    }

    loadMap() {
      console.log("llama al mapa");   

      if(this.datos.modo == "used"){
        let mapOptions: GoogleMapOptions = {
          controls: {
            compass: true,
            myLocation: true,
            mapToolbar: true
          },
          camera: {
            target: {
              lat: this.datos.lat,
              lng: this.datos.lng
            },
            zoom: 18,
            tilt: 30
          }
        };
        console.log("llama al mapa",mapOptions);   
        this.map = GoogleMaps.create('map_canvas', mapOptions);   
      }else{
        let mapOptions: GoogleMapOptions = {
          controls: {
            compass: true,
            myLocation: true,
            mapToolbar: true
          },
          camera: {
            target: {
              lat: this.lat,
              lng: this.lng
            },
            zoom: 18,
            tilt: 30
          }
        };
        
        this.map = GoogleMaps.create('map_canvas', mapOptions);   
      }

     
      if(this.datos.modo == "used"){
        this.map.one(GoogleMapsEvent.MAP_READY)
        .then(() => {
          this.map.addMarker({
              title: 'Xmobile',
              icon: 'blue',
              animation: 'DROP',
              position: {
                lat: this.datos.lat,
                lng: this.datos.lng
              }
            })
        });
      }else{
        this.map.one(GoogleMapsEvent.MAP_READY)
          .then(() => {
            this.map.addMarker({
                title: 'Xmobile',
                icon: 'blue',
                animation: 'DROP',
                position: {
                  lat: this.lat,
                  lng: this.lng
                }
              })
          });
        }

        this.map.on(GoogleMapsEvent.MAP_LONG_CLICK).subscribe((params: any[]) => {
          let latLng = params[0];
          console.log("datos",params);
          this.map.clear();
          console.log("datos",latLng);
          this.lat = latLng.lat;
          this.lng = latLng.lng;
          this.map.addMarkerSync({
            position: latLng,
            title: latLng,
            animation: GoogleMapsAnimation.DROP
          });
        }); 
    }

    
  public closeModal(data: any) {
    this.map.clear;
    this.map.off;
    this.map.setVisible(false);
    this.map.setDiv(null);
    this.modalController.dismiss();
  }
  

  // de la pantalla
  async showToast(mensaje) {
    let toast = await this.toastCtrl.create({
      message: mensaje,
      duration: 2000,
      position: "bottom"
    });

    toast.present();
  }

  GPSPromise = () => {
    console.log("GPSPromise()");
    return new Promise((resolve, reject) => {
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

  seleccionubic(){
    this.map.clear;
    this.map.off;
    this.map.setVisible(false);
    this.map.setDiv(null);
    
    let rx = {
      "lat": this.lat,
      "lng": this.lng
    };
    
    this.modalController.dismiss(rx);
  }
}

