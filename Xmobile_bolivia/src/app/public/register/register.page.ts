import { Component, OnInit } from '@angular/core';
import { DataService } from "../../services/data.service";
import { UniqueDeviceID } from '@ionic-native/unique-device-id/ngx';
import { Device } from '@ionic-native/device/ngx';
import { Toast } from '@ionic-native/toast/ngx';
import { AlertController, NavController } from "@ionic/angular";
import { ConfigService } from "../../models/config.service";
import { AndroidPermissions } from '@ionic-native/android-permissions/ngx';
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';

// import { AndroidPermissions } from '@ionic-native/android-permissions/ngx';
// import { Uid } from '@ionic-native/uid/ngx';

@Component({
    selector: 'app-register',
    templateUrl: './register.page.html',
    styleUrls: ['./register.page.scss'],
})
export class RegisterPage implements OnInit {
    public txtBusqueda: number;
    public userlogin: string;
    public view: boolean;
    public dataUser: Array<{ documentoIdentidadPersona: string, nombrePersona: string, apellidoPPersona: string, apellidoMPersona: string }>;
    public equiponame;
    public loding: boolean;

    constructor(private dataService: DataService, private configService: ConfigService,
        private uniqueDeviceID: UniqueDeviceID,
        private spinnerDialog: SpinnerDialog,
        private device: Device, private toast: Toast, private navCrl: NavController,
        private androidPermissions: AndroidPermissions, public alertController: AlertController

    ) {
        this.dataUser = [];
        this.view = false;
        this.equiponame = '';
        this.loding = false;
    }
    ngOnInit() {
        this.userlogin = '';
    }

    public uudi() {
        return new Promise((resolve, reject) => {
            this.uniqueDeviceID.get().then((uuid: any) => resolve(uuid)).catch((error: any) => reject(error));
        });
    }

    public async confirmRegister() {
        if (this.equiponame != '') {

            try {
                /**
                 * verificar si ya se registró un uuid para el centro
                 */
                let uuid = await this.configService.getUUID();
                console.log("uuid ", uuid);
                this.toast.show(`El equipo ya fué registrado.`, '3000', 'center').subscribe(toast => {
                });
                return false;
            } catch (e) {
                const alert = await this.alertController.create({
                    cssClass: "my-custom-class",
                    header: "Confirmar identificador",
                    message: "Debes recordar el identificador " + this.equiponame + " en caso de eliminar la aplicación y volver acceder en el mismo u otro dispositivo.",
                    buttons: [
                        {
                            text: "Cancelar",
                            role: "cancel",
                            cssClass: "secondary",
                            handler: (blah) => {
                                console.log(" Cancel: blah");
                                return false;
                            },
                        },
                        {
                            text: "Confirmar",

                            handler: (blah) => {
                                console.log("Confirm Okay");

                                this.registerEquip();
                            },
                        },
                    ],
                });

                await alert.present();
            }

        } else {
            this.toast.show(`Introduce el nombre del equipo.`, '3000', 'top').subscribe(toast => {
            });
        }
    }



    public async registerEquip() {
        console.log("registerEquip90  ");
        try {
            let urlSERVER = await this.configService.getIp();
            console.log("urlSERVER ", urlSERVER);
        } catch (error) {
            console.log("urlSERVER error ", error);
            this.toast.show(`Asegúrate de haber seleccionado el centro.`, '3000', 'center').subscribe(toast => {
            });
        }

        this.spinnerDialog.show(null, 'Loading...', true);

        let equi = {
            "equipo": this.equiponame + '-(' + this.device.model + ')',
            //"uuid": await  this.uudi(),
            "uuid": this.equiponame,
            "plataforma": this.device.platform
        };
        console.log("equi ", equi);
        try {
            let x: any = await this.dataService.registroEquipo(equi);
            console.log("register uuid  antiguo JSON.parse(x.data) ", JSON.parse(x.data));
            console.log("set uuid storage ", x.data);
            await this.configService.setUUID(equi);
            this.spinnerDialog.hide();
            this.navCrl.pop();
            this.toast.show(`Dispositivo registrado correctamente.`, '3000', 'center').subscribe(toast => {
            });
        } catch (e) {
            this.spinnerDialog.hide();
            let rx = JSON.parse(e.error);
            console.error("error apoi register ", rx);
            if (rx[0].message == "!ALERTA - El dispositivo ya fue registrado.") {
                await this.configService.setUUID(equi);// este data debe dev=olver el backend para volver a registrar al local
                console.log("await this.configService.getUUID() ", await this.configService.getUUID());
                this.navCrl.pop();
                this.toast.show(`Dispositivo asignado correctamente.`, '3000', 'center').subscribe(toast => {
                });
            } else {
                this.toast.show(`${rx[0].message}.`, '3000', 'center').subscribe(toast => {
                });
            }

        }

    }
}
