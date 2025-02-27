
import { Component, ComponentFactoryResolver, OnInit } from '@angular/core';
import { MenuController, NavController, AlertController } from '@ionic/angular';
import { UniqueDeviceID } from '@ionic-native/unique-device-id/ngx';
import { DataService } from "../../services/data.service";
import { ConfigService } from "../../models/config.service";
import { Network } from "@ionic-native/network/ngx";
import { User } from "../../models/user";
import { AndroidFingerprintAuth } from '@ionic-native/android-fingerprint-auth/ngx';
import { NativeService } from "../../services/native.service";
import { WheelSelector } from "@ionic-native/wheel-selector/ngx";
import { NativeStorage } from '@ionic-native/native-storage/ngx';
import { Device } from '@ionic-native/device/ngx';

import { dataResetLocal } from "../../utilsx/dataResetLocal";
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { environment } from '../../../environments/environment';
import { Documentos } from '../../models/documentos';
import { Documentopago } from '../../models/documentopago';


@Component({
    selector: 'app-login',
    templateUrl: './login.page.html',
    styleUrls: ['./login.page.scss'],
})
export class LoginPage implements OnInit {
    public password: string;
    public username: string;
    public modo: boolean;
    public loding: boolean;
    public est: string;
    private userModel: User;
    public databasex: any;
    public loginOffline: boolean = false;
    public documentos = new Documentos();
    public documentopago = new Documentopago()


    constructor(private navCrl: NavController, private network: Network, public alertController: AlertController, private spinnerDialog: SpinnerDialog, private uniqueDeviceID: UniqueDeviceID, private _device: Device,
        private configService: ConfigService, private dataService: DataService, private menu: MenuController,

        private androidFingerprintAuth: AndroidFingerprintAuth, public native: NativeService, private selector: WheelSelector, private nativeStorage: NativeStorage,
        //  private androidPermissions:AndroidPermissions,
        //  private uid:Uid
    ) {

        this.username = '';
        this.password = '';
        this.est = '';
        this.databasex = 'SELECCIONAR.';
        this.modo = false;
        this.loding = false;
        this.userModel = new User();
        this.nativeStorage.getItem('userdata')
            .then((data: any) => {
                console.log(data);
            })
            .catch((err: any) => {
                console.error(err);
                this.nativeStorage.setItem('userdata', { username: '', password: '' })
                    .then(() =>
                        console.log('userdata almacenadoxx!'),
                        error => console.error('Error storing item', error)
                    );
            });


    }
    public async ionViewWillEnter() {
        try {
            let urlSERVER = await this.configService.getIp();
            console.log("urlSERVER ", urlSERVER);
            let basesdatas: any = await this.configService.getBasesdatas();
            console.log("ionWill ", basesdatas)
            //  if(basesdatas.length ==0){
            //     this.databasex = ""
            //     this.username = ""
            //     this.password = ""

            //  }
        } catch (error) {
            this.databasex = ""
            this.username = ""
            this.password = ""
        }

    }


    public async ngOnInit() {
        this.menu.enable(false, 'menuxmobile');
        this.menu.close('menuxmobile');
        let dataUsuario: any = await this.getUserdata();
        this.username = dataUsuario.username;
    }

    public async getUserdata() {
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem('userdata')
                .then((data: any) => {
                    resolve(data);
                })
                .catch((err: any) => {
                    console.error(err);
                    reject(err);
                });
        });
    }

    public async direccciones() {
        try {
            let basesdatas: any = await this.configService.getBasesdatas();
            this.selector.show({
                title: "SELECCIONAR CENTRO.",
                items: [basesdatas],
                positiveButtonText: "SELECCIONAR",
                negativeButtonText: "CANCELAR",
                displayKey: 'name'
            }).then(async (result: any) => {
                let datax: any = basesdatas[result[0].index];
                let path: string = datax.ip + ':' + datax.puerto + '/' + datax.dir + '/api/web/';
                await this.configService.setIp(path);
                await this.configService.setDireccion(datax.ip);
                await this.configService.setPuerto(datax.puerto);
                await this.configService.setDir(datax.dir);
                await this.configService.setDB(datax.code);
                await this.configService.setName(datax.name);
                this.databasex = datax.name;
                console.log(path);
                console.log(datax);

                this.native.mensaje(`Centro asignado.`, '3000', 'top');
            }, (err: any) => {
                console.log(err);
            });
        } catch (e) {
            this.navCrl.navigateForward(`config`);
            this.native.mensaje(`No tienes ningún centro asignado.`, '3000', 'top');
        }
    }

    public async resgiter() {
        console.log("databasex ", this.databasex);
        try {
            let urlSERVER = await this.configService.getIp();
            console.log("urlSERVER ", urlSERVER);
        } catch (error) {
            console.log("urlSERVER error ", error);
            this.native.mensaje(`Debe seleccionar un centro.`, '3000', 'top');
            return false;
        }

        if (this.databasex !== 'SELECCIONAR.') {
            this.navCrl.navigateForward(`register`);
        } else {
            console.error("error eleccionar centro.");
            this.native.mensaje(`Debe seleccionar un centro.`, '3000', 'top');
            return false;
        }
    }

    public async run() {
        try {
            let resp: any = await this.configService.getSession();
            if (resp != false) {
                this.navCrl.navigateRoot(`home`);
            } else {
                this.native.mensaje(`¡NO AUTENTICADO!`, '3000', 'top');
            }
        } catch (e) {
            console.log(e)
        }
    }

    public async actionTouchLogin() {
        let respUs: any;
        console.log("ingresando modo huella");

        try {
            //n  await this.configService.getSession();
            respUs = await this.configService.getSession();

        } catch (e) {
            this.native.mensaje(`¡NO AUTENTICADO! no encontramos  tu información de sesión en memoria local`, '3000', 'top');
            return;
        }

        this.androidFingerprintAuth.isAvailable().then((result) => {
            let hayInternet = true;
            this.network.onDisconnect().subscribe(() => {
                console.log('network was disconnected :-(');
                hayInternet = false;
            });

            if (result.isAvailable) {
                this.androidFingerprintAuth.encrypt({
                    clientId: 'exxis',
                    username: '6957636',
                    password: '6957636',
                    locale: "es",
                    disableBackup: true
                }).then(async result => {
                    console.log();
                    if (hayInternet) {
                        if (!this.loginOffline) {
                            this.loding = true;
                            let urlSERVER = await this.configService.getIp();
                            console.log("urlSERVER ", urlSERVER);
                            /*
                                                        try {
                                                            await this.dataService.__get("" + urlSERVER + "index.php");
                            
                            
                                                        } catch (error) {
                                                            this.loding = false;
                                                            console.log("internet error ", error);
                                                            this.native.mensaje(`No pudimos conectarnos con el servidor, revisa tu conexion a internet.`, '3000', 'top');
                                                            return false;
                                                        
                                                        }
                                                        */

                            console.log("respUs local  ", respUs);
                            let uuidLocal: any = await this.configService.getUUID();
                            console.log("uuidLocal  ", uuidLocal);
                            let obx: any = {
                                usuarioNombreUsuario: respUs[0].nombreUsuario,
                                usuarioClaveUsuario: respUs[0].contrapass,
                                plataformaEmei: uuidLocal.uuid,
                                version: environment.version
                            };
                            console.log("obx data login ", obx);
                            let x: any = await this.dataService.login(obx);
                            console.log("return x  ", x);
                            let respUser: any = JSON.parse(x.data);
                            if (!respUser) {
                                return this.native.mensaje(`¡Servidor no disponible`, '3000', 'top');
                            }

                            console.log("dataUser in login", JSON.parse(x.data));
                            await this.configService.setSession(respUser.respuesta);
                            //} catch (e) {
                            this.loding = false;
                            if (result.withFingerprint) {
                                console.log("run 1");
                                this.run();
                            } else if (result.withBackup) {
                                console.log("run 2");
                                this.run();
                            } else {
                                this.native.mensaje(`¡NO AUTENTICADO!`, '3000', 'top');
                            }
                        } else {
                            if (result.withFingerprint) {
                                console.log("run 1");
                                this.run();
                            } else if (result.withBackup) {
                                console.log("run 2");
                                this.run();
                            } else {
                                this.native.mensaje(`¡NO AUTENTICADO!`, '3000', 'top');
                            }
                        }
                    } else {
                        if (result.withFingerprint) {
                            console.log("run 1");
                            this.run();
                        } else if (result.withBackup) {
                            console.log("run 2");
                            this.run();
                        } else {
                            this.native.mensaje(`¡NO AUTENTICADO!`, '3000', 'top');
                        }
                    }
                }).catch(error => {
                    switch (error) {
                        case (this.androidFingerprintAuth.ERRORS.FINGERPRINT_CANCELLED):
                            this.native.mensaje(`AUTENTICACIÓN DE HUELLA DIGITAL CANCELADA`, '3000', 'top');
                            break;
                        case (this.androidFingerprintAuth.ERRORS.FINGERPRINT_ERROR):
                            this.native.mensaje(`FINGERPRINT ERROR`, '3000', 'top');
                            break;
                        case (this.androidFingerprintAuth.ERRORS.FINGERPRINT_NOT_AVAILABLE):
                            this.native.mensaje(`HUELLA NO DISPONIBLE`, '3000', 'top');
                            break;
                    }
                });
            } else {
                this.native.mensaje(`La autenticación de huellas digitales no está disponible`, '3000', 'top');
            }
        }).catch(error => {
            console.error(error);
        });
    }

    public uudi() {
        return new Promise((resolve, reject) => {
            this.uniqueDeviceID.get().then((uuid: any) => resolve(uuid)).catch((error: any) => reject(error));
        });
    }

    public settings() {
        this.navCrl.navigateForward(`config`);
    }

    public validar(e: any) {
        return /^[0-9a-zA-Z]+$/.test(e);
    }

    public async login() {
        let uuidLocal: any;
        console.log("loginOffline ", this.loginOffline);
        console.log("this.network.type  ", this.network.type);
        if (!this.loginOffline) {
            this.network.onDisconnect().subscribe(() => {
                this.native.mensaje(`Conectate a internet por favor.`, '3000', 'top');
                return false;
            });
        }

        let urlSERVER = await this.configService.getIp();
        console.log("urlSERVER ", urlSERVER);

        //  return false;
        // console.log("await this.configService.getName(); ", await this.configService.getName());
        if (this.databasex === 'SELECCIONAR.') {
            this.native.mensaje(`Debes seleccionar un centro.`, '3000', 'top');
            return false;
        }
        try {
            await this.configService.getName();
        } catch (e) {
            this.native.mensaje(`Debes seleccionar un centro.`, '3000', 'top');
            return false;
        }
        try {
            /**
             * verificar si ya se registró un uuid para el centro
             */
            uuidLocal = await this.configService.getUUID();
            console.log("uuidLocal  ", uuidLocal);

        } catch (e) {
            this.native.mensaje(`Debe registrar el equipo.`, '3000', 'top');
            console.error("No existe uuid");
            return false;
        }
        let uuid = uuidLocal.uuid;
        if (this.network.type != 'none' && !this.loginOffline) {
            if (this.username != '' && this.validar(this.username)) {
                this.loding = true;
                /*
                                try {
                                    await this.dataService.__get("" + urlSERVER + "index.php");
                
                
                                } catch (error) {
                                    this.loding = false;
                                    console.log("internet error ", error);
                                    this.native.mensaje(`No pudimos conectarnos con el servidor, revisa tu conexion a internet.`, '3000', 'top');
                                    return false;
                
                
                                }
                */
                try {
                    console.log("uuidLocal  ", uuidLocal);
                    let obx: any = {
                        usuarioNombreUsuario: this.username,
                        usuarioClaveUsuario: this.password,
                        plataformaEmei: uuid,
                        version: environment.version
                    };
                    console.log("obx data login ", obx);
                    let x: any = await this.dataService.login(obx);
                    console.log("return x  ", x);
                    let respUser: any = JSON.parse(x.data);
                    console.log("dataUser in login", JSON.parse(x.data));
                    console.log("dataUser return  login", respUser);

                    console.log("actionDownload() ",);
                    try {
                        // let territorios = await this.dataService.getTerritorios();
                        let territoriosUser = await this.dataService.getTerritoriosFilter();
                        // console.log("seteo de territorios ", territorios);
                        console.log("seteo de territoriosUser ", territoriosUser);
                        localStorage.setItem("territorios", JSON.stringify(territoriosUser.respuesta));
                    } catch (error) {
                        console.log("error", error);

                    }

                    if (!respUser) {
                        this.loding = false;
                        return this.native.mensaje(`¡Servidor no disponible`, '3000', 'top');
                    }

                    if (respUser.estado == 403) {
                        this.loding = false;
                        return this.native.mensaje(`Hay una nueva versión del aplicativo debe instalarla.`, '5000', 'top');
                    }

                    if (respUser.estado == 405) {
                        this.loding = false;
                        return this.native.mensaje(`Debe eliminar la base de datos local.`, '5000', 'top');
                    }

                    respUser.respuesta[0].contrapass = this.password;
                    /**param set*/
                    respUser.respuesta[0].nitCharactersQuantity = 15;
                    console.log("  respUser.respuesta[0].nitCharactersQuantity  ", respUser.respuesta[0].nitCharactersQuantity)
                    /**valid new session */
                    localStorage.setItem("newSession", "0")
                    localStorage.setItem("idSession", respUser.respuesta[0].idUsuario)
                    try {
                        if (!localStorage.getItem("beforeSession")) {
                            console.log("No hay session")
                            localStorage.setItem("beforeSession", respUser.respuesta[0].nombreUsuario)

                        } else {
                            console.log("la session es ", localStorage.getItem("beforeSession"));
                            if (localStorage.getItem("beforeSession") == respUser.respuesta[0].nombreUsuario) {
                                console.log(" mismo usuario")
                            } else {
                                console.log("otro usuario")
                                localStorage.setItem("beforeSession", respUser.respuesta[0].nombreUsuario)
                                localStorage.setItem("newSession", "1")
                            }
                        }
                    } catch (error) {
                        console.log("catch err session ", error)
                    }
                    if (typeof respUser.respuesta[0].token === 'undefined') {
                        this.native.mensaje(`El usuario no existe.`, '3000', 'top');
                        this.loding = false;
                        return false;
                    }
                    try {
                        //respUser.respuesta[0].config[0].moneda = 'BS';
                        console.log("respUser.respuesta) ", respUser.respuesta);
                        await this.configService.setSession(respUser.respuesta);
                        this.navCrl.navigateRoot(`home`);
                    } catch (e) {
                        console.log("error");
                        this.loding = false;
                        this.native.mensaje(`Credenciales incorrectas.`, '3000', 'top');
                    }
                    this.loding = false;
                } catch (error) {
                    this.loding = false;
                    this.native.mensaje(`Credenciales incorrectas.`, '3000', 'top');
                }
            } else {
                this.native.mensaje(`Usuario o contraseña no pueden ser nulo.`, '3000', 'top');
            }
        } else {
            let respUs: any;
            console.log("ingresando modo offline");

            try {

                respUs = await this.configService.getSession();
            } catch (e) {
                this.native.mensaje(`No encontramos tus credenciales en memoria local, conéctate a internet.`, '3000', 'top');
                return false;
            }

            this.loding = true;
            console.log("respUs local  ", respUs);
            console.log("respUs[0].nombreUsuario ", respUs[0].nombreUsuario);
            console.log("respUs[0].contrapass ", respUs[0].contrapass);
            console.log("this.username", this.username);
            console.log("this.password ", this.password);

            if (respUs[0].nombreUsuario.toLowerCase() === this.username.toLowerCase() && respUs[0].contrapass === this.password) {
                this.loding = false;
                //  this.native.mensaje(`Bienvenido ${respUs[0].nombrePersona}.`, '3000', 'top');
                this.navCrl.navigateRoot(`home`);
            } else {
                this.loding = false;
                this.native.mensaje(`El usuario no es válido.`, '3000', 'top');
            }
            //} catch (e) {
            this.loding = false;
            //     this.native.mensaje(`Conéctate a internet para continuar.`, '3000', 'top');
            // }
        }
    }

    settingsDeleteData = async () => {
        let uuidLocal: any;
        try {
            uuidLocal = await this.configService.getUUID();
            console.log("uuidLocal  ", uuidLocal);
            console.log('DEVD handle event')
        } catch (error) {
            console.log("sin UUID")
            return this.native.mensaje(`Registra tu equipo.`, '3000', 'top');
        }

        let docPendientes = 0;
        let totalexportpagos = 0;
        try {
            let arrxx: any = await this.documentos.dataExportAll();
            docPendientes = arrxx.length;
            console.log("docPendientes ", docPendientes);

            let pagosCount: any = await this.documentopago.pagosexportCantidad();
            totalexportpagos = pagosCount.length;
            console.log("totalexportpagos ", totalexportpagos);

        } catch (error) {
            console.log("no existe las tablas ")
        }

        // try {
        //     //n  await this.configService.getSession();
        //     let respUs = await this.configService.getSession();
        //     console.log("respUs ", respUs)

        // } catch (e) {
        //     this.native.mensaje(`¡NO AUTENTICADO! no encontramos  tu información de sesión en memoria local`, '3000', 'top');
        //     return;
        // }

        try {

            let resp: any = await this.dataService.exportNumeracionSync();
            console.log("resp nube ", resp)

        } catch (error) {
            console.log("sin conexion  nube ", error)
            return this.native.mensaje(`Sin conexión al servidor.`, '3000', 'top');
        }

        const alert = await this.alertController.create({
            cssClass: "my-custom-class",
            header: "Eliminar información local",
            message: "¿Está seguro de eliminar los datos de cache de la aplicacón?",
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
                        if (totalexportpagos > 0) {
                            this.dataService.exportDocumentosAsinc(0);
                        }
                        if (docPendientes > 0) {
                            this.dataService.exportPagosAsync(0);
                        }

                        // this.servisLogDeleteDB(uuidLocal);

                        this.acttionDelete()

                    },
                },
            ],
        });

        await alert.present();
    }

    servisLogDeleteDB = async (uuidLocal) => {
        console.log("servisLogDeleteDB () ")
        try {
            let x: any = await this.dataService.servisLogDeleteDB(uuidLocal);
            console.log("return x  ", x);
        } catch (error) {
            console.log("error servicio ", error)
        }
    }

    acttionDelete = async () => {
        let classDeleteData = new dataResetLocal();
        this.spinnerDialog.show('', 'Eliminando...', true);
        try {
            // let dataMigrates = [];
            // try {
            //     let x: any = await this.dataService.serviseMigratesMovil();
            //     console.log("return x migrates  ", JSON.parse(x.data).respuesta);
            //     dataMigrates = JSON.parse(x.data).respuesta;
            // } catch (error) {
            //     console.log("error servicio ", error)
            // }
            await classDeleteData.deleteDatabase() // ELIMINA BD
            // await classDeleteData.alterTables(dataMigrates) //ADICIONA CAMPOS 

        } catch (error) {
            console.log("DEVD error delete deleteLocalStorage ", error)
        }
        setTimeout(() => { this.spinnerDialog.hide() }, 2000)
    }
}
