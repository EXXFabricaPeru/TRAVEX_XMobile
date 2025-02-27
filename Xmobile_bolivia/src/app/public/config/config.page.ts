import { Component, OnInit } from '@angular/core';
import { ConfigService } from '../../models/config.service';
import { NativeService } from "../../services/native.service";
import { DataService } from "../../services/data.service";
import { SpinnerDialog } from "@ionic-native/spinner-dialog/ngx";
import { dataResetLocal } from '../../utilsx/dataResetLocal';
import { AlertController } from '@ionic/angular';
import { Documentos } from '../../models/documentos';
import { Documentopago } from '../../models/documentopago';

@Component({
    selector: 'app-config',
    templateUrl: './config.page.html',
    styleUrls: ['./config.page.scss'],
})
export class ConfigPage implements OnInit {
    public ip: string;
    public puerto: string;
    public dir: string;
    dataLocal = [];
    uuidLocal;
    versionMobile: String = "";
    public documentos = new Documentos();
    public documentopago = new Documentopago();
    constructor(public configService: ConfigService, public alertController: AlertController, public data: DataService, public native: NativeService, private spinnerDialog: SpinnerDialog) {
        
        // this.ip = 'http://192.168.1.20'; //beefoods
        // this.puerto = '8082';
        // this.dir = 'xmobile_bolivia/xmobile';

        this.ip = 'http://143.137.146.238'; //Travex
        this.puerto = '1433';
        this.dir = 'xmobile/xmobile';
    }

    public async ngOnInit() {
        let basesdatas: any = await this.configService.getBasesdatas();
        console.log("basesdatas ", basesdatas)
        this.dataLocal = basesdatas;
        try {
            /**
             * verificar si ya se registró un uuid para el centro
             */
            let uuidl: any = await this.configService.getUUID();
            this.uuidLocal = uuidl.uuid;
            console.log("this.uuidLocal  ", this.uuidLocal);
        } catch (e) {
            this.native.mensaje(`Debe registrar el equipo.`, '4000', 'top');
            console.error("No existe uuid");
            this.uuidLocal = "";
        }
    }

    public removeDuplicates(arrayIn) {
        let arrayOut = [];
        arrayIn.forEach(item => {
            try {
                if (JSON.stringify(arrayOut[arrayOut.length - 1].dir) !== JSON.stringify(item.code))
                    arrayOut.push(item);
            } catch (err) {
                arrayOut.push(item);
            }
        });
        return arrayOut;
    }

    public async save() {
        let configuraciones: any;
        if (this.ip.trim() == "" || this.puerto.trim() == "" || this.dir.trim() == "") {
            this.native.mensaje(`Datos requeridos.`, '3000', 'top');
            return false;
        }
        configuraciones = [];
        let dataconf: any;
        try {
            this.spinnerDialog.show(null, null, true);
            let urlxx = this.ip.trim() + ':' + this.puerto.trim() + '/' + this.dir.trim() + '/api/web/configuracionesgenerales';
            console.log("urlxx ", urlxx);
            let respx: any = await this.data.__getinit(urlxx);
            console.log("respx ", respx);

            let dataresp: any = JSON.parse(respx.data);
            console.log("dataresp ", dataresp);
            dataconf = {
                "ip": this.ip.trim(),
                "puerto": this.puerto.trim(),
                "dir": this.dir.trim(),
                "code": dataresp[0].code,
                // "code": Date.now().toString(),
                "name": dataresp[0].empresa,
                "provider": dataresp,
            };
            console.log("dataconf ", dataconf);
            configuraciones = await this.configService.getBasesdatas();
            console.log("configuraciones ", configuraciones);
            const found = configuraciones.find(element => element.name == dataconf.name);
            if (!found) {
                configuraciones.push(dataconf);
                let nuevoarr = this.removeDuplicates(configuraciones);
                console.log("ROMERO",nuevoarr);
                await this.configService.setBasesdatas(nuevoarr);
                this.dataLocal.push(dataconf);
                let basesdatas: any = await this.configService.getBasesdatas();
                console.log("ROMERO",basesdatas);
                this.native.mensaje(`Registrado correctamente.`, '3000', 'top');
            } else {
                this.native.mensaje(`Ya existe un centro con ese nombre.`, '3000', 'top');
            }
            this.spinnerDialog.hide();
        } catch (e) {
            if (e.code == 2) {
                configuraciones.push(dataconf);
                await this.configService.setBasesdatas(configuraciones);
                this.dataLocal.push(dataconf);
                await this.configService.setBasesdatas(configuraciones);
                this.native.mensaje(`Registrado correctamente.`, '3000', 'top');
            } else {
                this.native.mensaje(`Error de conexión no existe la dirección.`, '3000', 'top');
            }
            this.spinnerDialog.hide();
        }
    }

    deleteStorage = async (value) => {
        console.log('delete memory...')
        console.log(value);

        let arrxx: any = [];
        try {
            arrxx = await this.documentos.dataExportAll();
        } catch (error) {

        }

        let docPendientes = arrxx.length;
        console.log("docPendientes ", docPendientes);
        let pagosCount: any = []
        try {
            pagosCount = await this.documentopago.pagosexportCantidad();
        } catch (error) {

        }

        let totalexportpagos = pagosCount.length;
        console.log("totalexportpagos ", totalexportpagos);




        if (docPendientes > 0) {
            console.log("hay docs");
            await this.data.exportDocumentosAsinc(0);
            // return true;
        }

        if (totalexportpagos > 0) {
            console.log("hay pagos");
            this.data.exportPagosAsync(0);
            // return true;
        }


        console.log('DEVD handle event')

        const alert = await this.alertController.create({
            cssClass: "my-custom-class",
            header: "Eliminar centro",
            message: "¿Está seguro de eliminar el centro y UUID de la memoria local?",
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

                        this.acttionDelete()

                    },
                },
            ],
        });

        await alert.present();
    }



    acttionDelete = async () => {
        let classDeleteData = new dataResetLocal();
        this.spinnerDialog.show('', 'Eliminando...', true);
        try {
            // await classDeleteData.deleteLocalStorage()
            // await classDeleteData.deleteDatabase()
            const result = await classDeleteData.deleteNativeStorage();
            console.log("result storage ", result);
            this.dataLocal = [];
            this.uuidLocal = "";

        } catch (error) {
            console.log("DEVD error delete deleteLocalStorage ", error)
        }
        setTimeout(() => { this.spinnerDialog.hide() }, 2000)
    }
}
