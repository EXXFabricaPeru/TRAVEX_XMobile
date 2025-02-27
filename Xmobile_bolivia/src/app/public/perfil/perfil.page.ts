import { Component, OnInit } from '@angular/core';
import { ConfigService } from "../../models/config.service";
import { AlertController, LoadingController, NavController } from "@ionic/angular";
import { DataService } from "../../services/data.service";
import { Toast } from "@ionic-native/toast/ngx";
import { File } from '@ionic-native/file/ngx';
import { Pagos } from '../../models/pagos';
import { Documentos } from '../../models/documentos';
import { FileTransfer, FileTransferObject } from '@ionic-native/file-transfer/ngx';
import { SQLite } from '@ionic-native/sqlite/ngx';
import { NativeStorage } from '@ionic-native/native-storage/ngx';
import { SQLitePorter } from '@ionic-native/sqlite-porter/ngx';
import { AndroidPermissions } from '@ionic-native/android-permissions/ngx';

@Component({
    selector: 'app-perfil',
    templateUrl: './perfil.page.html',
    styleUrls: ['./perfil.page.scss'],
})
export class PerfilPage implements OnInit {
    public data: any;
    private promise: Promise<string>;
    private stringToWrite: string;
    private blob: Blob;
    exportDB: boolean = false;
    public pathLink: any = "";
    constructor(private navCrl: NavController, public alertController: AlertController, private toast: Toast,
        private configService: ConfigService, private dataService: DataService,
        private androidPermissions: AndroidPermissions,
        private file: File,
        private transfer: FileTransfer,
        private sqlite: SQLite,
        private nativeStorage: NativeStorage,
        private sqlitePhoter: SQLitePorter,
        private loadingCtrl: LoadingController,
    )
    {
        this.data = [];
    }

    public async ngOnInit() {


        try {
            let resp: any = await this.configService.getSession();
            this.data = resp[0];
            console.log(this.data);
        } catch (e) {
        }
    }

    public pageSincronizar() {
        this.navCrl.navigateForward(`sincronizacion`);
    }

    public async resetpass() {
        let alert: any = await this.alertController.create({
            header: 'INTRODUZCA NUEVO PASSWORD.',
            subHeader: 'Debe tener al menos 8 caracteres de letras Mayúsculas, Minúsculas y números.',
            mode: 'ios',
            inputs: [{
                name: 'pass',
                type: 'password',
                placeholder: '(EJE): Exxis123'
            }],
            buttons: [{
                text: 'CAMBIAR',
                handler: async (data: any) => {
                    if (!this.validar_clave(data.pass)) {
                        this.toast.show(`El password debe tener al menos 8 caracteres de letras Mayúsculas, Minúsculas y números.`, '6000', 'top').subscribe(toast => {
                        });
                        return false;
                    }
                    try {
                        await this.dataService.resepassword(this.data.idUsuario, data);
                        this.toast.show(`El password modificado correctamente. `, '4000', 'top').subscribe(toast => {
                        });
                    } catch (e) {
                        console.log(e);
                    }
                }
            }]
        });
        await alert.present();
    }

    private validar_clave(contrasenna) {
        console.log("contrasenna ", contrasenna);

        // let pattern = new RegExp(/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*()_+])[A-Za-z\d][A-Za-z\d!@#$%^&*()_+]{7,19}$/);
        var pattern = new RegExp(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/);
        return pattern.test(contrasenna);
    }
    /* downloadData = async () => {




        // this.file.checkDir('/', 'mydir').then(_ => console.log('Directory exists')).catch(err =>
        ///     console.log('Directory doesnt exist'));
        try {


            const path = this.file.externalRootDirectory + 'Download';
            console.log("path ", path);
            


            console.log("---> continuar ");
            this.file.createFile(path, 'DataBaseLocal', true);


            this.promise = this.file.readAsText(path, 'DataBaseLocal');
            await this.promise.then(value => {
                console.log(value);
            });
            let modelPago = new Pagos();
            let modelDocumentos = new Documentos();
            let dataDocs = await modelDocumentos.selectAll();
            let dataDocsDetalle = await modelDocumentos.selectAllDetalle();
            let dataMaster = await modelPago.selectAllDocs();
            let dataDetalle = await modelPago.selectAll();

            let stringToInsert = 'CabezeraPago ' + JSON.stringify(dataMaster);
            stringToInsert = stringToInsert + 'detallePago ' + JSON.stringify(dataDetalle);
            stringToInsert = stringToInsert + 'detaCabezeraDocumentos ' + JSON.stringify(dataDocs);
            stringToInsert = stringToInsert + 'detalleDocumentos ' + JSON.stringify(dataDocsDetalle);
            /// this.stringToWrite = 'I learned this from Medium';
            this.blob = new Blob([stringToInsert], { type: 'text/plain' });
            this.file.writeFile(path, 'DataBaseLocal', this.blob, { replace: true, append: false });

            this.toast.show(`Creando Archivo DataBaseLocal en ${path}`, '9000', 'center').subscribe(toast => {
            });
            
        } catch (error) {

        }
    } */
    chekPermission = async () => {

        return this.androidPermissions.requestPermissions(
            [
                this.androidPermissions.PERMISSION.READ_EXTERNAL_STORAGE,
                this.androidPermissions.PERMISSION.WRITE_EXTERNAL_STORAGE
            ]
        );

    }
    downloadData = async () => {

        let permission = await this.chekPermission();
        console.log("permission file-->", permission);

        if (!permission.hasPermission) {
            return this.toast.show("Se necesita permisos de almacenamieto para esta acción", '4000', 'center').subscribe(t => t);
        }

        let loading = await this.loadingCtrl.create();
        let basesdatas: any = await this.nativeStorage.getItem('DB');
        let datasession: any = await this.nativeStorage.getItem(basesdatas + 'SESSION');
        loading.present();
        // let db = window.openDatabase('1605122856xm', '1.0', '1605122856xm', 1 * 1024);
        let db = await this.sqlite.create({
            name: basesdatas+"_"+datasession[0].idUsuario+ 'xm.db',
            location: 'default'
        });
        let dbtxt = "";
        try {
            dbtxt = await this.sqlitePhoter.exportDbToSql(db)
            loading.dismiss();
            // console.log("sql armado", dbtxt);
        } catch (error) {
            console.log("error al exportar con sqlpoy¿ther", error);
            this.toast.show("Error al exportar la base de datos", "4000", "center").subscribe(e => e);
        }
        try {
            const path = this.file.externalRootDirectory + 'Download';
            console.log("path ", path);
            /* this.file.readAsText(path, 'foo.txt').then((file) => {
                 console.log('process file');
             }).catch((e) => {
                 console.log('process error ', e);
             });
     */

            console.log("---> continuar ");
            this.file.createFile(path, 'DataBaseLocal', true);


            this.promise = this.file.readAsText(path, 'DataBaseLocal');
            await this.promise.then(value => {
                console.log(value);
            });
            let modelPago = new Pagos();
            let modelDocumentos = new Documentos();
            let dataDocs = await modelDocumentos.selectAll();
            let dataDocsDetalle = await modelDocumentos.selectAllDetalle();
            let dataMaster = await modelPago.selectAllDocs();
            let dataDetalle = await modelPago.selectAll();

            let stringToInsert = 'CabezeraPago ' + JSON.stringify(dataMaster);
            stringToInsert = stringToInsert + 'detallePago ' + JSON.stringify(dataDetalle);
            stringToInsert = stringToInsert + 'detaCabezeraDocumentos ' + JSON.stringify(dataDocs);
            stringToInsert = stringToInsert + 'detalleDocumentos ' + JSON.stringify(dataDocsDetalle);
            /// this.stringToWrite = 'I learned this from Medium';
            this.blob = new Blob([dbtxt], { type: 'text/plain' });
            await this.file.writeFile(path, 'DataBaseLocal.sql', this.blob, { replace: true, append: false });
            console.log("urlimg-->", path + '/DataBaseLocal.sql');
            setTimeout(() => {

                this.uploadFile(path + '/DataBaseLocal.sql');
            }, 3000);
            this.toast.show(`Creando Archivo DataBaseLocal en ${path}`, '3000', 'center').subscribe(toast => {
            });

        } catch (error) {
            console.log("error al crear el slq", error);
        }

    }
    async uploadFile(imageURI) {

        const hoy = new Date();
        const fecha = hoy.getDate() + '' + (hoy.getMonth() + 1) + '' + hoy.getFullYear();
        const hora = hoy.getHours() + '' + hoy.getMinutes() + '' + hoy.getSeconds();
        this.pathLink = await this.configService.getIp();

        let loader = await this.loadingCtrl.create({ message: "Cargando archivo" });
        loader.present();
        const fileTransfer: FileTransferObject = this.transfer.create();
        let user: any = await this.configService.getSession();
        let options: any = {
            fileKey: 'file',
            fileName: (user[0].nombreUsuario ? user[0].nombreUsuario + fecha + '_' + hora : 'user_desconecido'),
            chunkedMode: false,
            mimeType: "text/plain",
            headers: {}
        }
        console.log("phat for link", this.pathLink);
        fileTransfer.upload(imageURI, this.pathLink, options)
            .then((data) => {
                console.log(data+" Uploaded Successfully ", imageURI);
                console.log("this.pathLink + 'uploadlogmob' ", this.pathLink + 'uploadlogmob')
                // this.imageFileName = "http://192.168.0.7:8080/static/images/ionicfile.jpg"
                loader.dismiss();
                this.toast.show("Copia de seguridad exitosa", "3000", "center").subscribe();
            }, (err) => {
                console.log(err);
                loader.dismiss();
                //this.toast(err);
                this.toast.show("Error al crear una copia de seguridad ", "3000", "center").subscribe();

                // this.toast.show(""+JSON.stringify(err),"3000","center").subscribe();

            });
    }
}
